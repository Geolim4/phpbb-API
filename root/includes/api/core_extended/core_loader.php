<?php
/**
*
* @package phpBB3 API Core extend: Loader
^>@version $Id: core_loader.php v0.0.1 13h37 03/08/2014 Geolim4 Exp $
* @copyright (c) 2012 - 2014 Geolim4.com http://geolim4.com
* @bug/function request: http://geolim4.com/tracker
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/
namespace phpbb_api;
//Import special phpBB classes
use messenger, phpbb_db_tools;

/**
* @ignore
*/
if (!defined('IN_PHPBB') || !defined('IN_PHPBB_API'))
{
	exit;
}

trait core_loader
{
	/****
	* loader()
	* API core loader
	* @param string $output Output type (json, xml...)
	* @param string $key Key ID
	****/
	protected function loader($output, $key)
	{
		$this->set_output($output);
		$this->load_vars();
		$this->set_headers();
		$this->load_settings();
		$this->load_filters();
		$this->load_timestampable();
		$this->load_acls();
		$this->load_privileges();
		$this->load_hooks();
		$this->load_login_attempts($key);
		$this->authenticate_key($key);
		$this->load_methods_type();

		//If there is an error during initialization, stop it here.
		if ($this->error)
		{
			$this->trigger_error($this->error, E_USER_WARNING);
		}
	}

	/****
	* load_vars()
	* Initialize basic vars
	* @noparam
	****/
	protected function load_vars()
	{
		global $template, $db, $user, $auth, $config, $api_exception;
		global $phpbb_root_path, $phpEx, $cache, $api_cache, $table_prefix;
		//Do the globals vars fork
		$this->template			= &$template;
		$this->db				= &$db;
		$this->user				= &$user;
		$this->auth				= &$auth;
		$this->config			= &$config;
		$this->phpbb_root_path	= &$phpbb_root_path;
		$this->phpEx			= &$phpEx;
		$this->cache			= &$cache;//phpBB's cache
		$this->messenger		= new messenger(false);
		$this->api_cache		= &$api_cache;//API's cache
		$this->table_prefix		= &$table_prefix;
		$this->db_tools			= new phpbb_db_tools($db);
		$this->time				= time();
		$this->api_exceptions	= &$api_exception->errors;
		$this->browser			= (!empty($_SERVER['HTTP_USER_AGENT'])) ? htmlspecialchars((string) $_SERVER['HTTP_USER_AGENT']) : '';
		$this->ip				= ($this->config['ip_login_limit_use_forwarded'] && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $this->ip = $_SERVER['HTTP_X_FORWARDED_FOR'] : ((!empty($_SERVER['REMOTE_ADDR'])) ? (string) $_SERVER['REMOTE_ADDR'] : '127.0.0.1');
		$this->forwarded_for	= (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) ? htmlspecialchars((string) $_SERVER['HTTP_X_FORWARDED_FOR']) : '';
	}

	/****
	* load_settings()
	* Initialize basic settings
	* @noparam
	****/
	protected function load_settings()
	{
		$this->user->add_lang('mods/phpbb_api');
		functions\phpbb_logs_status(API_STATUS_DOWN);
		$this->template_path = API_ROOT_PATH . 'templates/';

		if ($this->backtrace)
		{
			$this->backtrace = $this->config['api_mod_backtrace'];
		}

		$this->join_user_data = request_var('u', false);
		$this->convert_timestamp = request_var('h', false);
		$this->encrypted_output = request_var('n', false);
		$this->sql_mapping = $this->api_cache->obtain_database_mapping();
		$this->now = time();
	}

	/****
	* load_filters()
	* Initialize datas filters
	* @param array $filter additional filter
	****/
	protected function load_filters($add_hook_filter = array())
	{
		if (empty($add_hook_filter))
		{
			$this->api_filters = array_merge($this->api_filters, array(
				'post'		=> array (
					API_FILTER_TYPE		=> API_FILTER_INSET,
					API_TYPE_ADMIN => $this->sql_mapping[POSTS_TABLE],
					API_TYPE_USER => array('post_id', 'poster_id', 'icon_id', 'topic_id', 'forum_id', 'post_subject', 'post_text', 'post_attachment', 'post_time', 'bbcode_bitfield', 'bbcode_uid'),
				),
				'topic'		=> array (
					API_FILTER_TYPE		=> API_FILTER_UNSET,
					API_TYPE_ADMIN => array(),//In unset mode we let it empty :)
					API_TYPE_USER => array('topic_moved_id', 'topic_bumped', 'topic_bumper', 'poll_start', 'poll_length', 'poll_max_options', 'poll_last_vote', 'poll_vote_change'),
				),
				'forum'		=> array (
					API_FILTER_TYPE		=> API_FILTER_INSET,
					API_TYPE_ADMIN => $this->sql_mapping[FORUMS_TABLE],
					API_TYPE_USER => array('forum_id', 'forum_name', 'forum_desc', 'forum_desc_bitfield', 'forum_desc_options', 'forum_desc_uid'),
				),
				'group'		=> array (
					API_FILTER_TYPE		=> API_FILTER_INSET,
					API_TYPE_ADMIN => array('*'),//group_real_name isn't a real column but the real translation, so we use wildcard 
					API_TYPE_USER => array('group_id', 'group_name', 'group_real_name', 'group_desc', 'group_desc_bitfield', 'group_desc_options', 'group_desc_uid', 'group_colour'),
				),
				'key_options'		=> array (
					API_FILTER_TYPE		=> API_FILTER_UNSET,
					API_TYPE_ADMIN => array('key_secret_key'),//Hide the secret key, even for admin keys.
					API_TYPE_USER => array('key_secret_key', 'query_sql', 'query_sql_api', 'deactivated_methods'),
				),
				'get_config'		=> array (
					API_FILTER_TYPE		=> API_FILTER_INSET,
					API_TYPE_ADMIN => array('*'),
					API_TYPE_USER => array('allow_avatar_remote_upload', 'avatar_filesize', 'avatar_max_height', 'avatar_max_width', 'avatar_min_height', 'avatar_min_width', 'board_timezone', 'board_email_sig', 'board_startdate', 'max_filesize', 'max_filesize_pm', 'site_desc', 'sitename'),
				),
			));
		}
		else
		{
			$this->add_filters($add_hook_filter);
		}
	}

	/****
	* load_acls()
	* Initialize ACLs
	* @param array $add_hook_acl additional ACLs
	****/
	protected function load_acls($add_hook_acl = array())
	{
		if (empty($add_hook_acl))
		{
			$this->api_acls = array_merge($this->api_acls, array(
				'group'		=> array (
					API_AUTH_OR		=> array('u_viewprofile', 'a_group'),
				),
			));
		}
		else
		{
			$this->add_acls($add_hook_acl);
		}
	}

	/****
	* load_hooks()
	* Initialize hooks
	* @param bool $purge_cache Force hook to be extracted from /includes/api/hooks folder (Bypass cache)
	****/
	protected function load_hooks($purge_cache = false, $reload_lang_file = true)
	{
		if ($purge_cache)
		{
			$this->api_cache->destroy('_api_hooks');
		}
		$sql_mapping = $this->sql_mapping;


		foreach ($this->api_cache->obtain_api_hooks() AS $hook)
		{
			if (file_exists(API_ROOT_PATH . 'hooks/' . $hook . DOT . $this->phpEx))
			{
				include(API_ROOT_PATH . 'hooks/' . $hook . DOT . $this->phpEx);

				if (!empty($base_hook_name))
				{
					$this->hooks = array_unique(array_merge($this->hooks, array($base_hook_name)));
					$base_hook_name = null;
				}

				if (!empty($add_hook_lang))
				{
					$this->hook_lang_files = array_unique(array_merge($this->hook_lang_files, array($add_hook_lang)));
					//$this->user->add_lang($add_hook_lang);
					$add_hook_lang = null;
				}

				if (!empty($add_hook_filter))
				{
					$this->add_filters($add_hook_filter);
					$add_hook_filter = null;
				}

				if (!empty($add_hook_acl))
				{
					$this->add_acls($add_hook_acl);
					$add_hook_acl = null;
				}

				if (!empty($add_privileges))
				{
					$this->load_privileges($add_privileges);
					$add_privileges = null;
				}

				if (!empty($add_hook_tpl))
				{
					$this->template_content = $this->get_template_content($add_hook_tpl);
					$add_hook_tpl = null;
				}

				if (!empty($add_hook_timestamp))
				{
					$this->load_timestampable($add_hook_timestamp);
					$add_hook_timestamp = null;
				}
			}
			else
			{
				//Here we generate a php Warning like a failed include()
				//Like a php Warning, we need to use hardcoded language.
				//That warning will be handled into the child-exception result.
				error_handling\generate_warning('File ' . API_ROOT_PATH . 'hooks/' . $hook . DOT . $this->phpEx . ' does not exist!', __FILE__, __LINE__);
			}
		}
		if ($purge_cache)
		{
			//Cache purged, recheck ACL's
			$this->check_acls($this->api_action);
		}
		if ($reload_lang_file)
		{
			functions\add_hooks_lang();
		}
	}

	/****
	* load_methods_type()
	* Load methods type.
	* @param array $add_type Type to merge (e.g: from hooks)
	****/
	protected function load_methods_type($add_type = array())
	{
		$sql_mapping = $this->api_cache->obtain_database_mapping();
		$this->api_methods_type = array_merge(array(
				'post'				=> array(),
				'post'				=> $sql_mapping[POSTS_TABLE],
				'topic'				=> $sql_mapping[TOPICS_TABLE],
				'forum'				=> $sql_mapping[FORUMS_TABLE],
				'group'				=> $sql_mapping[GROUPS_TABLE],
				'refresh_stats'		=> functions\array_key_fill_value(array('all', 'num_posts', 'num_topics', 'num_users', 'num_files', 'upload_dir_size', 'update_last_username')),
				'perm_ban'			=> functions\array_key_fill_value(array('user', 'ip', 'email')),
				'unban'				=> functions\array_key_fill_value(array('user', 'ip', 'email')),
				'get_bans'			=> functions\array_key_fill_value(array('userid', 'ip', 'email', 'all')),
				'get_config'		=> functions\array_key_fill_value(array('all', 'cached', 'dynamic', 'custom')),
				'set_config'		=> functions\array_key_fill_value(array('json', 'serialize')),
				'key_options'		=> array(),
				'key_stats'			=> array(),
				'sql_query'			=> array(),
				'get_constants'		=> array(),
				'board_status'		=> array($this->user->lang['API_STATUS_DISABLE'], $this->user->lang['API_STATUS_ENABLE']),
				'php_configuration'	=> array(get_loaded_extensions()),
				'search_ip'			=> functions\array_key_fill_value(array('all', 'users', 'posts', 'logs', 'banlist', 'login_attempts')),
			), $add_type, $this->api_methods_type
		);

		foreach ($this->api_methods_type AS $method_name => &$method_types)
		{
			$this->filter($method_types, $method_name);
		}
	}

	/****
	* load_privileges()
	* Initialize API privileges
	* @param array $add_privileges Privileges to merge (e.g: from hooks)
	****/
	protected function load_privileges($add_privileges = array(), $return = false)
	{
		$api_privileges = array_merge(array(
				'login'				=> array(API_TYPE_ADMIN => true, API_TYPE_USER => true),
				'post'				=> array(API_TYPE_ADMIN => true, API_TYPE_USER => true),
				'topic'				=> array(API_TYPE_ADMIN => true, API_TYPE_USER => true),
				'forum'				=> array(API_TYPE_ADMIN => true, API_TYPE_USER => true),
				'group'				=> array(API_TYPE_ADMIN => true, API_TYPE_USER => true),
				'refresh_stats'		=> array(API_TYPE_ADMIN => true, API_TYPE_USER => false),
				'perm_ban'			=> array(API_TYPE_ADMIN => true, API_TYPE_USER => false),
				'unban'				=> array(API_TYPE_ADMIN => true, API_TYPE_USER => false),
				'get_bans'			=> array(API_TYPE_ADMIN => true, API_TYPE_USER => false),
				'get_config'		=> array(API_TYPE_ADMIN => true, API_TYPE_USER => true),
				'set_config'		=> array(API_TYPE_ADMIN => true, API_TYPE_USER => false),
				'key_options'		=> array(API_TYPE_ADMIN => true, API_TYPE_USER => true),
				'key_stats'			=> array(API_TYPE_ADMIN => true, API_TYPE_USER => true),
				'sql_query'			=> array(API_TYPE_ADMIN => true, API_TYPE_USER => false),
				'get_constants'		=> array(API_TYPE_ADMIN => true, API_TYPE_USER => false),
				'board_status'		=> array(API_TYPE_ADMIN => true, API_TYPE_USER => false),
				'php_configuration'	=> array(API_TYPE_ADMIN => true, API_TYPE_USER => false),
				'search_ip'			=> array(API_TYPE_ADMIN => true, API_TYPE_USER => false),
			), $add_privileges
		);

		if (!$return)
		{
			$this->api_privileges = array_merge($api_privileges, $this->api_privileges);
		}
		else
		{
			return $api_privileges;
		}
	}
	
	/****
	* load_timestampable()
	* Initialize timestampable array
	* @param array $add_timestampable Timestamp keys to merge (e.g: from hooks)
	****/
	protected function load_timestampable($add_timestampable = array(), $return = false)
	{
		$api_timestampable = array_merge(array(
				/*Config Table*/	
				'get_config'	=> array('cache_last_gc', 'database_last_gc', 'last_queue_run', 'rand_seed_last_update', 'record_online_date', 'search_last_gc', 'session_last_gc', 'warnings_last_gc', 'board_startdate', 'api_next_install_check', 'api_mod_install_age', 'pchart_next_install_check'),
				/*Post Table*/
				'post'			=> array('post_time', 'post_edit_time'),
				/*Topic Table*/		
				'topic'			=> array('topic_time', 'topic_last_post_time', 'topic_last_view_time'),
				/*Bans Table*/
				'get_bans'		=> array('ban_start', 'ban_end'),
			
			), $add_timestampable
		);
		if (!$return)
		{
			$this->timestampable = array_merge($api_timestampable, $this->timestampable);
		}
		else
		{
			return $api_timestampable;
		}
	}
}
