<?php
/**
*
* @package phpBB3 API Class core
^>@version $Id: core.php v0.0.1 00h11 12/20/2013 Geolim4 Exp $
* @copyright (c) 2012 - 2014 Geolim4.com http://geolim4.com
* @bug/function request: http://geolim4.com/tracker
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/

namespace phpbb_api;
use SimpleXMLElement;

/**
* @ignore
*/
if (!defined('IN_PHPBB') || !defined('IN_PHPBB_API'))
{
	exit;
}

foreach (explode(',', API_TRAITS) AS $trait)
{
	if (!trait_exists($trait))
	{
		require(API_CORE_PATH . trim($trait) . DOT . $phpEx);
	}
}

class api
{
	//Load methods from traits
	use core_loader, core_methods, core_static, core_crypto;

	//Hooks vars
	public $hooks = array();
	public $hook_lang_files = array();

	//Methods vars
	public $api_key = '';
	public $api_secret_key = '';
	public $api_type = '';
	public $api_action = '';
	public $api_action_translated = '';
	public $api_subaction_translated = '';
	public $api_acls = array();
	public $api_filters = array();
	public $api_key_stats = array();
	public $api_privileges = array();
	public $api_methods_type = array();

	//Non-direct method vars
	public $timestampable = array();
	public $ignore_cron = false;
	public $ignore_attempt = false;
	public $skip_counter = false;
	public $skip_crypto = false;
	public $bypass_logout = false;
	public $custom_output = false;
	public $join_user_data = false;
	public $convert_timestamp = false;
	public $encrypted_output = false;
	public $cron_id = 0;
	public $now = 0;
	public $browser = '';
	public $output_str = '';
	public $forwarded_for = '';
	public $template_content = '';

	//Shared error handler var
	//Add a fully descriptive backtrace in case of critical error. If turned to false, that setting overwrite the config setting!
	//Please note that setting is the Master setting in case of PHP fatal error !! (See also core_catchable_error.php)
	public $backtrace = true;

	//Core vars
	protected $error;
	protected $errno = 0;
	protected $output;
	protected $errors = array();
	protected $template_path = '';
	protected $destructor_handler = array();

	//phpBB root vars
	public $template;
	public $auth;
	public $user;
	public $config;
	public $phpbb_root_path;
	public $phpEx;
	public $cache;
	public $table_prefix;

	//phpBB extended vars
	public $messenger;
	public $db_tools;

	/***
	** Magic Methods
	***/

	/****
	* __construct()
	* Core constructor
	* @param string $output Output type (json, xml...)
	* @param string $key Key ID
	****/
	public function __construct($output, $key)
	{
		if (!defined('API_BREAK_LOADER'))
		{
			$this->loader($output, $key);
		}
	}

	/****
	* __destruct()
	* Core destructor
	* @noparam
	****/
	public function __destruct()
	{
		//http://www.php.net/manual/en/language.oop5.decon.php
		//PHP manual wrote: Destructors called during [...] The working directory in the script shutdown phase
		//can be different with some SAPIs (e.g. Apache)
		@chdir(dirname($_SERVER['SCRIPT_FILENAME']));

		foreach($this->destructor_handler AS $handler)
		{
			if(is_callable($handler))
			{
				$handler();
			}
			else if(is_array($handler) && !empty($handler['class']) && !empty($handler['method']))
			{
				if(is_object($handler['class']) && method_exists($handler['class'], $handler['method']))
				{
					$handler['class']->{$handler['method']}();
				}
				else if(class_exists($handler['class']) && method_exists($handler['class'], $handler['method']))
				{
					call_user_func(array($handler['class'], $handler['method']));
				}
			}
		}
	}

	/****
	* __call()
	* Magic handler for non-found methods.
	* @param string $name the method name we tried to pass
	* @param array $arguments arguments passed through
	****/
	public function __call($name, $arguments)
	{
		//Here we generate a php Warning like a non-existing method)
		//Like a php Warning, we need to use hardcoded language.
		//That warning will be handled into the child-exception result.
		error_handling\generate_warning('Method "' . $name . '" not found in class "' . __CLASS__ . '" (' . sizeof($arguments) . ' args passed through)', __FILE__, __LINE__);
		return array(false, $name, $arguments);
	}

	/***
	** API base methods
	***/

	/****
	* add_destructor_handler()
	* Add a new destructor handler
	* @noparam
	****/
	public function add_destructor_handler($handler)
	{
		if(is_array($handler) && !empty($handler['class']) && !empty($handler['method']))
		{
			if(is_object($handler['class']) && method_exists($handler['class'], $handler['method']))
			{
				$this->destructor_handler[] = array('class' => $handler['class'], 'method' => $handler['method']);
			}
		}
		else if(is_callable($handler))
		{
			$this->destructor_handler[] = $handler;
		}
	}

	/****
	* set_headers()
	* Set "no-cache" headers
	* @noparam
	****/
	protected function set_headers()
	{
		functions\set_no_cache_headers();
		if (!empty($this->config['api_mod_origin_header']))
		{
			header('X-Frame-Options: SAMEORIGIN');
		}
	}

	/****
	* set_output()
	* Set output format
	* @param string $output Output type (json, xml...)
	****/
	protected function set_output($output)
	{
		$this->output = $output;
	}

	/****
	* load_login_attempts()
	* Check login attempts from this IP
	* @param string $key key to log in case of ban
	****/
	protected function load_login_attempts($key)
	{

		if (empty($this->config['api_mod_max_attempts']))
		{
			return;
		}

		//Ignore white-listed IPs
		if($this->check_ip(array($this->user->ip), explode("\n", $this->config['api_mod_whitelist']), true))
		{
			$this->ignore_attempt = true;
			return;
		}

		//Calculate yesterday's first/last seconds
		//No rolling time here, 24h is 24h!! No more, no less!!
		$begin_day_time = $this->now - $this->config['api_mod_max_attempts_time'];
		$end_day_time = $this->now;

		//No where clause: In CRON task we trust, as she must clean former ban each 24 hours.
		$sql = 'SELECT COUNT(attempt_ip) AS total
			FROM ' . API_LOGIN_ATTEMPTS . '
			WHERE attempt_ip = \'' . $this->db->sql_escape($this->ip) . '\'
				AND attempt_time BETWEEN ' . $begin_day_time . ' AND ' . $end_day_time;
		$result = $this->db->sql_query($sql);
		$total_attempts = $this->db->sql_fetchfield('total');
		$this->db->sql_freeresult($result);

		if ($total_attempts >= $this->config['api_mod_max_attempts'])
		{
			functions\api_add_log('API_LOG_BANNED_IP', $key, $total_attempts);
			$this->trigger_error('API_ERROR_ATTEMPTS', E_USER_WARNING);
		}
	}

	/****
	* authenticate_key()
	* Authenticate a provided key
	* @param string $key key to authenticate
	****/
	protected function authenticate_key($key)
	{
		//Here we join the users_table table to check if the user still exists !!
		$sql = 'SELECT k.*, u.user_id AS user_exists
			FROM ' . API_KEYS_TABLE . ' k
			LEFT JOIN  ' . USERS_TABLE . ' u
				ON(u.user_id = k.user_id)
			WHERE key_id = \'' . $this->db->sql_escape($key) . '\'';
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$row)
		{
			if(!$this->ignore_attempt)
			{
				functions\add_login_attempt($this->ip, $this->browser, $this->forwarded_for);
				functions\api_add_log('API_LOG_BAD_AUTH_KEY', $key);
			}
			$this->trigger_error('API_UNAUTHORIZED', E_USER_WARNING);
		}
		else if($row && empty($row['user_exists']))
		{
			//The key is still valid, it's not an hacking attempt
			//functions\add_login_attempt($this->ip, $this->browser, $this->forwarded_for);
			//But we still log that attempt :>
			functions\api_add_log('API_LOG_BAD_AUTH_USER', $key);
			$this->trigger_error('API_UNAUTHORIZED_USER', E_USER_WARNING);
		}
		else
		{
			$this->key_id = $key;
		}
		$row['queries_history'] = array();

		//kill registered user... Here we are authenticated via the API key
		if ($this->user->data['is_registered'])
		{
			$this->user->session_kill(false);
		}

		//Remove previously declared headers
		if (!headers_sent())
		{
			header_remove();
			$this->set_headers();
		}

		//Declare our own header
		if (!empty($this->config['api_mod_header']))
		{
			header('X-Powered-By: phpBB API/' . API_VERSION);
		}

		//Here we re-auth the current user because we are authed by the API KEY
		$this->user->update_session_page = false;
		$this->user->session_create($row['user_id'],/* (($row['key_type'] == API_TYPE_ADMIN) ? true : */ false /*)*/, false, false);
		$this->auth->acl($this->user->data);
		$this->user->setup();

		//Load hooks language
		foreach ($this->hook_lang_files AS $hook_lang_files)
		{
			$this->user->add_lang($hook_lang_files);
		}

		//Reset user timezone to local timezone
		$timetable = functions\automatic_dst_get_timetable();
		date_default_timezone_set($timetable[$this->user->data['user_timezone']]);

		//Reset api language...
		$this->user->add_lang(array('mods/phpbb_api', 'mods/info_ucp_phpbb_api'));

		//Reset error handler
		error_handling\api_reset_error_handler();

		//Check for API activation
		if (empty($this->config['api_mod_enable']))
		{
			$this->trigger_error('API_ERROR_DISABLED', E_USER_WARNING);
		}

		//Check for SSL status
		if (!empty($this->config['api_mod_force_ssl']) && !is_ssl_request())
		{
			$this->trigger_error('API_ERROR_NO_SSL', E_USER_WARNING);
		}

		//Fully check key options, we may miss some required informations...
		$this->check_key_options($row);
	}

	/****
	* add_filters()
	* Add datas filters
	* @param array $filter additional filter (e.g: from hooks)
	****/
	protected function add_filters($add_hook_filter)
	{
		$this->api_filters = array_merge($this->api_filters, $add_hook_filter);
	}

	/****
	* add_acls()
	* Add ACLs
	* @param array $filter additional ACLs (e.g: from hooks)
	****/
	protected function add_acls($add_hook_acls)
	{
		$this->api_acls = array_merge($this->api_acls, $add_hook_acls);
	}

	/****
	* check_key_options()
	* Check options of an authenticated key
	* @param array $row key options to check
	****/
	protected function check_key_options($row)
	{
		if (!$this->auth->acl_get('u_phpbb_api_use'))
		{
			$this->trigger_error('NOT_AUTHORISED', E_USER_WARNING);
		}

		if ($row['key_status'] == API_STATUS_SUSPENDED)//Most probably admin decision
		{
			functions\api_add_log('API_LOG_BAD_AUTH_SUSPENDED', $this->key_id);
			$this->trigger_error($this->user->lang['API_SUSPENDED'], E_USER_WARNING);
		}

		if ($row['key_status'] == API_STATUS_DEACTIVATED)//Most probably user regeneration
		{
			functions\api_add_log('API_LOG_BAD_AUTH_DEACTIVATED', $this->key_id);
			$this->trigger_error($this->user->lang['API_DEACTIVATED'], E_USER_WARNING);
		}

		$counter = functions\sort_queries_history($row['queries_history'], $row['key_id']);
		if (!$this->auth->acl_get('u_phpbb_api_ignore_day') && $row['max_queries_per_day'] && ($counter['queries_per_day'] > ($row['max_queries_per_day'] - 1)))
		{
			$this->trigger_error($this->user->lang('API_ERROR_PER_DAY', $row['max_queries_per_day']), E_USER_WARNING);
		}
		else if ($counter['queries_per_day'] && $row['max_queries_per_day'] && !$this->auth->acl_get('u_phpbb_api_ignore_day'))
		{
			$this->api_key_stats['daily_use'] = round(($counter['queries_per_day'] * 100) / $row['max_queries_per_day'], 2) . '%';
			$this->api_key_stats['daily_quota'] = $counter['queries_per_day'] . '/' . $row['max_queries_per_day'];
		}
		else
		{
			$this->api_key_stats['daily_quota'] = $counter['queries_per_day'] . '/' . $this->user->lang['UCP_PHPBB_API_INFINITE_SYMBOL'];
		}

		if (!$this->auth->acl_get('u_phpbb_api_ignore_week') && $row['max_queries_per_week'] && ($counter['queries_per_week'] > ($row['max_queries_per_week'] - 1)))
		{
			$this->trigger_error($this->user->lang('API_ERROR_PER_WEEK', $row['max_queries_per_week']), E_USER_WARNING);
		}
		else if ($counter['queries_per_week'] && $row['max_queries_per_week'] && !$this->auth->acl_get('u_phpbb_api_ignore_week'))
		{
			$this->api_key_stats['weekly_use'] = round(($counter['queries_per_week'] * 100) / $row['max_queries_per_week'], 2) . '%';
			$this->api_key_stats['weekly_quota'] = $counter['queries_per_week'] . '/' . $row['max_queries_per_week'];
		}
		else
		{
			$this->api_key_stats['weekly_quota'] = $counter['max_queries_per_week'] . '/' . $this->user->lang['UCP_PHPBB_API_INFINITE_SYMBOL'];
		}

		if (!$this->auth->acl_get('u_phpbb_api_ignore_month') && $row['max_queries_per_month'] && ($counter['queries_per_month'] > ($row['max_queries_per_month'] - 1)))
		{
			$this->trigger_error($this->user->lang('API_ERROR_PER_MONTH', $row['max_queries_per_month']), E_USER_WARNING);
		}
		else if ($counter['queries_per_month'] && $row['max_queries_per_month'] && !$this->auth->acl_get('u_phpbb_api_ignore_month'))
		{
			$this->api_key_stats['monthly_use'] = round(($counter['queries_per_month'] * 100) / $row['max_queries_per_month'], 2) . '%';
			$this->api_key_stats['monthly_quota'] = $counter['queries_per_month'] . '/' . $row['max_queries_per_month'];
		}
		else
		{
			$this->api_key_stats['monthly_quota'] = $counter['queries_per_month'] . '/' . $this->user->lang['UCP_PHPBB_API_INFINITE_SYMBOL'];
		}

		if (!$this->auth->acl_get('u_phpbb_api_ignore_max') && $row['max_queries'] && ($row['queries'] > ($row['max_queries'] - 1)))
		{
			functions\api_add_log('API_LOG_BAD_AUTH_OUT_OF_QUOTA', $row['key_id']);
			$this->trigger_error($this->user->lang('API_ERROR_EXCEEDED', $row['max_queries']), E_USER_WARNING);
		}
		else if ($row['queries'] && $row['max_queries'] && !$this->auth->acl_get('u_phpbb_api_ignore_max'))
		{
			$this->api_key_stats['total_use'] = round(($row['queries'] * 100) / $row['max_queries'], 2) . '%';
			$this->api_key_stats['total_quota'] = $row['queries'] . '/' . $row['max_queries'];
		}
		else
		{
			$this->api_key_stats['total_quota'] = $row['queries'] . '/' . $this->user->lang['UCP_PHPBB_API_INFINITE_SYMBOL'];
		}

		if ($row['expire_time'] && ($this->time > $row['expire_time']))
		{
			functions\api_add_log('API_LOG_BAD_AUTH_OUDATED', $row['key_id']);
			$this->trigger_error($this->user->lang('API_ERROR_EXPIRED', $this->user->format_date($row['expire_time'])), E_USER_WARNING);
		}
		else if ($row['expire_time'])
		{
			$this->api_key_stats['days_left'] = ceil(($row['expire_time'] - $this->time) / API_DAY_SECONDS);
		}
		else
		{
			$this->api_key_stats['days_left'] = $this->user->lang['API_LIFETIME'];
		}

		if ($row['force_post'] && !is_post_request())
		{
			$this->trigger_error($this->user->lang('API_ERROR_METHOD_REQUEST', get_request_method()), E_USER_WARNING);
		}

		if ($row['email_auth'] || $row['key_type'] == API_TYPE_ADMIN)
		{
			global $key_email, $switch_pvg;

			if (!empty($switch_pvg))
			{
				$row['key_type'] = API_TYPE_USER;
			}

			$key_email = trim($key_email);
			if ($key_email != $this->user->data['user_email'])
			{
				if ($key_email)
				{
					functions\api_add_log('API_LOG_BAD_AUTH_EMAIL', $row['key_id'], $key_email);
				}
				else
				{
					functions\api_add_log('API_LOG_BAD_AUTH_NO_EMAIL', $row['key_id'], $key_email);
				}

				$this->trigger_error('API_BAD_EMAIL', E_USER_WARNING);
			}
		}

		if ($row['key_ips'])
		{
			$allowed_ips = explode("\n", $row['key_ips']);
			if (!$this->check_ip(array($this->user->ip), $allowed_ips, ($row['key_ips_type']) ? true : false))
			{
				functions\api_add_log('API_LOG_BAD_AUTH_IP', $row['key_id']);
				$this->trigger_error($this->user->lang('API_BAD_IP', $this->user->ip), E_USER_WARNING);
			}
		}

		//We are on good standing, we can continue.
		$this->api_key = $row['key_id'];
		$this->api_secret_key = $row['key_secret_key'];
		$this->api_type = $row['key_type'];
		$this->key_options = $row;

		//Merge hooks method types
		foreach ($this->hooks AS $hook)
		{
			$hook = __NAMESPACE__ . '\hooks\methods\hook_api_set_submethod_' . $hook;
			if (function_exists($hook))
			{
				$hook($this);
			}
			$hook = __NAMESPACE__ . '\hooks\displays\hook_api_set_submethod_' . $hook;
			if (function_exists($hook))
			{
				$hook($this);
			}
		}
	}

	/****
	* check_ip()
	* Check if the IP is allowed
	* @param array $iplist IP(s) to check
	* @param array $allowed_ips IP(s) to compare
	* @param bool $is_allowed type of auth.
	****/
	protected function check_ip($iplist = array(), $allowed_ips = array(), $is_allowed = true)
	{
		if (!sizeof($iplist))
		{
			return false;
		}
		foreach ($allowed_ips AS $allowed_ips_)
		{
			$site_ip = trim($allowed_ips_);
			$hostname = '';
			if ($site_ip)
			{
				foreach ($iplist as $ip)
				{
					if (preg_match('#^' . str_replace('\*', '.*?', preg_quote($site_ip, '#')) . '$#i', $ip))
					{

						return $is_allowed;
					}
				}
			}

/* 			$site_hostname = @gethostbyaddr(trim($allowed_ips_));
			if ($site_hostname)
			{
				if (preg_match('#^' . str_replace('\*', '.*?', preg_quote($site_hostname, '#')) . '$#i', $hostname))
				{
					return $is_allowed;
				}
			} */
		}
		return !$is_allowed;
	}


	/****
	* check_acls()
	* Check user's ACLs
	* @param string Action ACL to check
	****/
	protected function check_acls($action)
	{
		if (!empty($this->api_acls[$action]))
		{
			if (isset($this->api_acls[$action][API_AUTH_XOR]))
			{
				$true_count = 0;
				foreach ($this->api_acls[$action][API_AUTH_XOR] AS $filters_)
				{
					if ($this->auth->acl_get($filters_))
					{
						$true_count++;
						if ($true_count >= 2)
						{
							$this->trigger_error('API_UNAUTHORIZED_AUTH', E_USER_WARNING);
						}
					}
				}
			}
			else if (isset($this->api_acls[$action][API_AUTH_OR]))
			{
				if (!$this->auth->acl_gets($this->api_acls[$action][API_AUTH_OR]))
				{
					$this->trigger_error('API_UNAUTHORIZED_AUTH', E_USER_WARNING);
				}
			}
			elseif (!empty($this->api_acls[$action][API_AUTH_AND]))
			{
				foreach ($this->api_acls[$action][API_AUTH_AND] AS $filters_)
				{
					if (!$this->auth->acl_get($filters_))
					{
						$this->trigger_error('API_UNAUTHORIZED_AUTH', E_USER_WARNING);
					}
				}
			}
		}
	}

	/****
	* invoke()
	* Invoke a local method @link [ROOT]/api.php
	****/
	public function invoke($action, $data, $type)
	{
		//Something wrong happened before, but let the cron task working.
		if (!empty($this->error_triggered))
		{
			return;
		}
		if (empty($action))
		{
			$this->trigger_error('API_ERROR_NO_METHOD', E_USER_WARNING);
		}
		if ($type == $this->config['api_mod_wildcard_char'])
		{
			$type = '';
		}
		if ($data == $this->config['api_mod_wildcard_char'])
		{
			$data = '';
		}
		if (($translated_method = trim(strtolower(functions\utf8_cleaning(array_search($action, $this->user->lang['API_TRANSLATED_METHOD']))))) /*!== false*/)//Even an empty result isn't an expected result...
		{
			$action = $translated_method;
		}
		$this->check_acls($action);
		if (($translated_submethod = trim(strtolower(array_search($type, $this->user->lang['API_FULL_TRANSLATED_SUBMETHOD'])))) /*!== false*/)//Even an empty result isn't an expected result...
		{
			$type = $translated_submethod;
			$this->api_subaction_translated = $translated_submethod;
		}

		if (isset($this->user->lang['API_FULL_TRANSLATED_METHOD'][$action]))
		{
			$this->api_action_translated = $this->user->lang['API_FULL_TRANSLATED_METHOD'][$action];
		}
		else
		{
			$this->api_action_translated = $action;
		}

		if (in_array($action, explode(',', $this->key_options['deactivated_methods'])))
		{
			if (isset($this->user->lang['API_FULL_TRANSLATED_METHOD'][$action]))
			{
				$action = $this->user->lang['API_FULL_TRANSLATED_METHOD'][$action];
			}
			functions\api_add_log('API_LOG_DEACTIVATED_METHOD', $this->api_key, $action);
			$this->trigger_error('API_ERROR_DEACTIVATED_METHOD', E_USER_WARNING);
		}
		$this->api_action = $action;
		$api_action = 'api_' . $action;

		if ($data && $this->api_type == API_TYPE_ADMIN && request_var('v', 1))
		{
			$defined_constants = get_defined_constants(true);
			$data = @preg_replace_callback('#\$([A-Za-z0-9_]*)#',
				function($match) use ($defined_constants)
				{
					$constant_name = strtoupper($match[1]);
					if (defined($constant_name) && isset($defined_constants['user'][$constant_name]))
					{
						return constant($constant_name);
					}
					return $match[1];
				},
				$data
			);
		}

		if (method_exists(__CLASS__, $api_action))
		{
			//That's the local scope.. 2 param only
			$this->{$api_action}($data, $type);
		}
		else if (function_exists('\phpbb_api\hooks\methods\hook_' . $api_action))
		{
			//Here we need to pass $this through too.
			call_user_func('\phpbb_api\hooks\methods\hook_' . $api_action, $this, $data, $type);
		}
		else
		{
			//try to purge the cache if that file is new and not cached yet...
			$this->load_hooks(true, true);
			if (($translated_method = trim(strtolower(functions\utf8_cleaning(array_search($action, $this->user->lang['API_TRANSLATED_METHOD']))))) /*!== false*/)//Even an empty result isn't an expected result...
			{
				$action = $translated_method;
				$this->api_action = $action;
				$api_action = 'api_' . $action;
			}
			$hook_nmspce = '\phpbb_api\hooks\methods\\';
			$hook_prefix = "hook_";
			$const_sufix = "_HOOK_API_TARGET_VERSION";
			if (function_exists($hook_nmspce . $hook_prefix . $api_action))
			{
				if (defined($hook_nmspce . strtoupper($action) . $const_sufix))
				{
					if (phpbb_version_compare(constant($hook_nmspce . strtoupper($action) . $const_sufix), API_VERSION, '>='))
					{
						call_user_func($hook_nmspce . $hook_prefix . $api_action, $this, $data, $type);
					}
					else
					{
						$this->trigger_error($this->user->lang('API_ERROR_HOOK_OUTDATED', API_VERSION, constant($hook_nmspce . strtoupper($action) . $const_sufix)), E_USER_ERROR);
					}
				}
				else
				{
					$this->trigger_error($this->user->lang('API_ERROR_HOOK_NOCONST', $hook_nmspce . strtoupper($action) . $const_sufix), E_USER_ERROR);
				}
			}
			else
			{
				$this->trigger_error($this->user->lang('API_ERROR_METHOD', $api_action), E_USER_ERROR);
			}
		}
	}

	/****
	* check_admin_privilege() (Public as we call it from hook too)
	* Check user privileges.
	* @param string $method privilege method to check
	****/
	public function check_admin_privilege($method)
	{
		if (strpos($method, 'api_') === 0)
		{
			$method = str_replace('api_', '', $method);
		}
		if (empty($this->api_privileges[$method][$this->api_type]))
		{
			functions\api_add_log('API_LOG_DENIED_PRIVILEGE', $this->api_key, (isset($this->user->lang['API_TRANSLATED_METHOD'][$method])) ? $this->user->lang['API_TRANSLATED_METHOD'][$method] : $method);
			$this->trigger_error($this->user->lang('API_UNAUTHORIZED_PVG', (isset($this->user->lang['API_TRANSLATED_METHOD'][$method])) ? $this->user->lang['API_TRANSLATED_METHOD'][$method] : $method), E_USER_ERROR);
		}
	}

	/****
	* validate_sql_column() (Public as we call it from hook too)
	* Validate a table && column for a SQL query
	* @param string $table table to validate
	* @param string $column column of $table to validate
	****/
	public function validate_sql_column($table, $column)
	{
		$sql_mapping = $this->api_cache->obtain_database_mapping();
		if (!isset($sql_mapping[$table][$column]))
		{
			$this->trigger_error($this->user->lang('API_ERROR_TYPE', $column, $table), E_USER_ERROR);
		}
	}

	/****
	* sanitize() (Public as we call it from hook too)
	* Sanitize external data
	* @param string $method method we're working
	* @param ref string $data data to sanitize
	* @param ref string $type type of data to sanitize
	* @param bool $prefilter use prefilter
	****/
	public function sanitize($method, &$data, &$type, $prefilter = true, $always_escape = true)
	{
		$method = substr($method, 4);
		switch($method)
		{
			case 'post':
				$data = (is_numeric($data) ? ((strpos($data, DOT) !== false) ? (float) $data : (int) $data) : '\'' . $this->db->sql_escape($data) .  '\'');
				$this->validate_sql_column(POSTS_TABLE, $type);
			break;

			case 'topic':
				$data = (is_numeric($data) ? ((strpos($data, DOT) !== false) ? (float) $data : (int) $data) : '\'' . $this->db->sql_escape($data) .  '\'');
				$this->validate_sql_column(TOPICS_TABLE, $type);
			break;

			case 'forum':
				$data = (is_numeric($data) ? ((strpos($data, DOT) !== false) ? (float) $data : (int) $data) : '\'' . $this->db->sql_escape($data) .  '\'');
				$this->validate_sql_column(FORUMS_TABLE, $type);
			break;

			case 'group':
				$data = (is_numeric($data) ? ((strpos($data, DOT) !== false) ? (float) $data : (int) $data) : '\'' . $this->db->sql_escape($data) .  '\'');
				$this->validate_sql_column(GROUPS_TABLE, $type);
			break;

			default:
				if (function_exists(__NAMESPACE__ . '\hooks\methods\hook_api_sanitize_' . $method))
				{
					//$@"#~% call-time pass-by-reference removing... PHP developers, i hate you so much !!!!!
					$call_user_func = __NAMESPACE__ . '\hooks\methods\hook_api_sanitize_' . $method;
					$call_user_func($this, $data, $type);
				}
				else if ($always_escape)
				{
					$data = $this->db->sql_escape($data);
					$type = $this->db->sql_escape($type);
				}
			break;
		}
		if ($prefilter)
		{
			if (!$this->prefilter($type, $method))
			{
				$this->trigger_error($this->user->lang('API_UNAUTHORIZED'), E_USER_WARNING);
			}
		}
	}

	/****
	* prefilter() (Public as we call it from hook too)
	* Prefilter datas to prevent forbidden search criteria (e.g: http://localhost/api/key_id/user/user_password/$H$jhfejfejfekfkfekf/xml)
	* @param array $type to filter
	* @param string $method filter method
	****/
	public function prefilter($type, $method)
	{
		if (isset($this->api_filters[$method]))
		{
			$filter_type = array_flip($this->api_filters[$method][$this->api_type]);
		}
		if (!is_array($type))
		{
			$type = array($type);
		}
		foreach ($type AS $type_)
		{
			//Check filter exist && ignore wildcard (*)
			if ((!isset($filter_type[$type_]) && !isset($filter_type['*']) && isset($this->api_filters[$method]) && $this->api_filters[$method][API_FILTER_TYPE] == API_FILTER_INSET) || (isset($filter_type[$type_]) && $this->api_filters[$method][API_FILTER_TYPE] == API_FILTER_UNSET))
			{
				//Forbidden call: Trying to search using a forbidden type
				return false;
			}
		}
		return true;
	}

	/****
	* filter() (Public as we call it from hook too)
	* Filter datas depending key privilege
	* @param array $rows to filter
	* @param string $method filter method
	****/
	public function filter(&$rows, $method)
	{
		if (strpos($method, 'api_') === 0)
		{
			$method = str_replace('api_', '', $method);
		}

		//Check filter exist && ignore wildcard (*)
		if (isset($this->api_filters[$method]) && current($this->api_filters[$method][$this->api_type]) != '*')
		{
			$array_filter = 'array_' . (($this->api_filters[$method][API_FILTER_TYPE] == API_FILTER_INSET) ? 'intersect' : 'diff' ) . '_key';
			$rows = $array_filter($rows, array_flip($this->api_filters[$method][$this->api_type]));
		}
		$this->timestransform($rows, $method);

		return $rows;
	}

	/****
	* timestransform() (Called from filter() method)
	* Format timestamp to human readable date
	* @param array $rows to filter
	* @param string $method filter method
	****/
	public function timestransform(&$rows, $method)
	{
		if (strpos($method, 'api_') === 0)
		{
			$method = str_replace('api_', '', $method);
		}
		if (!isset($this->timestampable[$method]) || !$this->convert_timestamp)
		{
			return;
		}

		foreach ($rows AS $key => &$rows_)
		{
			if (is_string($rows_) && in_array($key, $this->timestampable[$method]) && preg_match('#([0-9]{10})#', $rows_))
			{
				$rows_ = $this->user->format_date($rows_);
			}
		}
	}

	/****
	* get_template_content() (Public as we call it from hook/error handler too)
	* Get an API template content
	* @param string $template_name template name to get
	* @param string $force_path use custom path
	****/
	public function get_template_content($template_name, $force_path = false, $no_trigger = false)
	{
		if (!$force_path)
		{
			$template_path = $this->template_path;
		}
		else
		{
			$template_path = $force_path;
		}
		$filename = $template_path . $template_name;
		if (file_exists($template_path . $template_name))
		{
			$handle = fopen($filename, "rb");
			$template_content = fread($handle, filesize($filename));
			fclose($handle);
			return $template_content;
		}
		else
		{
			if ($no_trigger)
			{
				error_handling\generate_warning('Template path could not be found: '  . $template_path . $template_name, __FILE__, __LINE__);
			}
			else
			{
				$this->trigger_error('Template path could not be found: '  . $template_path . $template_name, E_USER_ERROR);
			}
		}
	}

	/****
	* trigger_error() (Public as we call it from hook/error handler too)
	* Trigger.... An API error :s
	* @param string $msg message do display
	* @param int $errno errno
	****/
	public function trigger_error($msg, $errno = E_USER_NOTICE, $line = '', $file = '')
	{
		$this->error_triggered = true;
		$this->errno = $errno;
		switch($errno)
		{
			case E_USER_ERROR:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			case E_RECOVERABLE_ERROR:
			case E_NOTICE:
			case E_WARNING:
			case E_ERROR:
			case E_STRICT:
			case E_DEPRECATED:
				$result = array(
					'msg' => isset($this->user->lang[$msg]) ? $this->user->lang[$msg] : $msg,
					'errno' => $errno,
				);
				if($errno == E_WARNING || $errno == E_NOTICE)
				{
					$result['msg'] = error_handling\e_user_level($errno, true) . ' ' . $result['msg'];
					$result += array(
						'line' => $line,
						'file' => $file
					);
				}
				if (($errno == E_RECOVERABLE_ERROR || $errno == E_CORE_ERROR || $errno == E_USER_ERROR || $errno ==  E_ERROR || $errno ==  E_COMPILE_ERROR) && $this->config['api_mod_backtrace'] && $this->backtrace)
				{
					$debug_backtrace = debug_backtrace();
					$debug = array();
					$i = 0;//Used to inject args var correctly...
					// Here we don't skip the first one.
					$debug_backtrace = array_reverse($debug_backtrace);

					foreach ($debug_backtrace as $trace)
					{
						if ($trace['function'] == 'api_error_handler' || (empty($trace['file']) && empty($trace['line'])))
						{
							continue;
						}
						$debug[$i] = array(
							'file' => isset($trace['file']) ? htmlspecialchars(phpbb_filter_root_path($trace['file'])) : "-",
							'line' => isset($trace['line']) ? $trace['line'] : "-",
							'function' => isset($trace['function']) ? $trace['function'] : "-",
							'class' => isset($trace['class']) ? $trace['class'] : "-",
							'type' => isset($trace['type']) ? $trace['type'] : "-",
						);
						if (!empty($trace['args'][0]) && in_array($trace['function'], array('include', 'require', 'include_once', 'require_once')))
						{
							$debug[$i] = array_merge($debug[$i], array(
								'args' => $trace['args'],
							));
						}
						$i++;
					}
					if ($this->api_type == API_TYPE_ADMIN)
					{
						$result += array(
							'backtrace' => $debug
						);
					}
					$trace = debug_backtrace();
					$caller = array_shift($trace);

					$result['msg'] = $this->user->lang('API_ERROR_INTERNAL', error_handling\e_user_level($errno), $caller['file'], $caller['line'], strip_tags($msg));

					functions\api_err_log($this->user->lang('API_ERROR_INTERNAL', strip_tags($msg), htmlspecialchars(phpbb_filter_root_path($caller['file'])), $caller['line']));
					functions\api_add_log('API_LOG_FATAL_ERROR', $this->api_key, array(htmlspecialchars(phpbb_filter_root_path($caller['file'])), $caller['line'], strip_tags($msg)));
				}

				if ($errno == E_RECOVERABLE_ERROR || $errno == E_CORE_ERROR || $errno == E_USER_ERROR || $errno ==  E_ERROR || $errno ==  E_COMPILE_ERROR)
				{
					send_status_line(503, 'Service Unavailable');
					$result += array(
						'status' => '503 Service Unavailable',
					);
					$this->display($result, true);
				}
				else
				{
					$this->errors[] = $result;
				}
			break;

			case E_USER_WARNING:
				$this->display(array(
					'msg' => isset($this->user->lang[$msg]) ? $this->user->lang[$msg] : $msg,
					'errno' => $errno,
				), true);
			break;

			//E_USER_NOTICE...
			default:
				$this->display(array(
					'msg' => isset($this->user->lang[$msg]) ? $this->user->lang[$msg] : $msg,
				), true);
			break;
		}
	}

	/****
	* internal_error() (Public as we call it from hook/error handler too)
	* API internal error handler
	* @param string $errstr Error message returned from PHP core
	* @param string $file File where PHP handling that error
	* @param int $line Line where PHP handling that error
	* @param int $errno errno
	****/
	public function internal_error($errstr, $file = '', $line = 0, $errno = E_USER_ERROR)
	{
		if (isset($this->user->lang[$errstr]))
		{
			//Handle trigger_error() from phpBB too. You must declare the language key to use them, else API_ERROR_INTERNAL will be triggered instead.
			$this->trigger_error($this->user->lang[$errstr], $errno, $line, $file);
		}
		else
		{
			$this->trigger_error($errstr, $errno, $line, $file);
		}
	}

	/****
	* display() (Public as we call it from hook too)
	* Display result of request to the user depending chosen format
	* @param array $array Array to display (even with recursive array)
	* @param bool $is_error Define display context: Safe or Error
	* @param bool $return Return result instead displaying it to user.
	****/
	public function display($array = array(), $is_error = false, $return = false)
	{
		global $api_bench_start;
		$callback = request_var('c', '');
		$fallback = request_var('f', '');
		$params = request_var('p', false);
		if (empty($array) && empty($this->output_str))
		{
			$this->trigger_error('API_NO_RECORD', E_USER_NOTICE);
		}
		//Do not count a non user-error as a new queries
		if (!$is_error || $this->errno == E_USER_NOTICE)
		{
			if (!$this->skip_counter)
			{
				$sql_ary = array(
					'key_id'	=> $this->api_key,
					'time'		=> $this->now,
					'method'	=> $this->api_action,
					'ip'		=> $this->user->ip
				);

				$sql = 'INSERT INTO ' . API_HISTORY_TABLE . ' ' . $this->db->sql_build_array('INSERT', $sql_ary);
				$this->db->sql_query($sql);

				$sql = 'UPDATE ' . API_KEYS_TABLE . '
					SET last_querie = ' . (int) time() .', queries = queries + 1
					WHERE key_id = \'' . $this->db->sql_escape($this->api_key) . '\'';
				$this->db->sql_query($sql);
			}
			if (!$is_error)
			{
				$array = array(
					'results' => $array,
				);
			}
		}
		else
		{
			if ($fallback)
			{
				$array += array(
					'fallback' => $fallback,
				);
			}
		}
		functions\force_array($this->api_exceptions);
		$this->api_exceptions = array_slice($this->api_exceptions, 0, 99, true);

		if (!empty($this->api_exceptions))
		{
			if ($this->config['api_mod_backtrace'] && $this->api_type == API_TYPE_ADMIN)
			{
				$array += array(
					'exceptions' => $this->api_exceptions,
				);
			}
			foreach ($this->api_exceptions AS $api_exceptions_)
			{
				functions\api_err_log($this->user->lang('API_ERROR_INTERNAL', $api_exceptions_['msg'], $api_exceptions_['file'], $api_exceptions_['line']));
				functions\api_add_log('API_LOG_NON_FATAL_ERROR', $this->api_key, array($api_exceptions_['file'], $api_exceptions_['line'], $api_exceptions_['msg']));
			}
		}

		if (!isset($array['status']))
		{
			$array['status'] = '200 OK';
		}

		if (!isset($array['userdata']) && $this->join_user_data)
		{
			$array['userdata'] = array(
				'user_id'		=> $this->user->data['user_id'],
				'user_email'	=> $this->user->data['user_email'],
				'username'		=> $this->user->data['username'],
				'user_colour'	=> $this->user->data['user_colour'],
			);
		}

		if (!isset($array['fallback']) && $callback)
		{
			$array += array(
				'callback' => $callback,
			);
		}

		if (!empty($params))
		{
			$array += array('params' => array(
				'GET' => $_GET,
				'POST' => $_POST,
			));
		}

		if (!isset($array['timing']))
		{
			$array['timing'] = round(functions\api_bench_end($api_bench_start), 4, PHP_ROUND_HALF_UP) . 's';
		}
		//Jump hack
		output_switch:
		switch($this->output)
		{
			case API_CUSTOM_OUTPUT:
				if (!$this->custom_output)
				{
					//Hey you're not allowed to use custom output, get out!!
					$this->output = 'json';
					goto output_switch;
				}
				else
				{
					//We are allowed to use the "custom" switch statement, so we directly jump to display statement (echo)
					$output = $this->output_str;
				}
			break;

			case 'json':
				header('Content-Type: application/json; charset=UTF-8');
				//functions\array_keys_stringify($array, $this->user->lang['API_ITEM_KEYWORD']);
				$output = json_encode($array, JSON_FORCE_OBJECT);
			break;

			case 'xml':
				header('Content-Type: text/xml; charset=UTF-8');
				$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><root/>');
				//functions\array_keys_stringify($array, $this->user->lang['API_ITEM_KEYWORD']);
				functions\array_walk_xml($array, $xml);
				$output = $xml->asXML();
			break;

			case 'serialize':
				header('Content-Type: text/plain; charset=UTF-8');
				functions\array_keys_stringify($array, $this->user->lang['API_ITEM_KEYWORD']);
				$output = serialize($array);
			break;

			default:
				$display_prefix = "phpbb_api\hooks\displays\\";

				if (!function_exists($display_prefix . 'hook_display_' . $this->output))
				{
					//try to purge the cache if that file is new and not cached yet...
					$this->load_hooks(true);
					if ((@call_user_func($display_prefix . 'hook_display_' . $this->output, $array, $this)) === false)
					{
						header('Content-Type: application/json; charset=UTF-8');
						functions\array_keys_stringify($array, $this->user->lang['API_ITEM_KEYWORD']);
						$output = json_encode($array, JSON_FORCE_OBJECT);
					}
					else
					{
						$tmp = $this->output;
						$this->output = 'json';
						$this->trigger_error($this->user->lang('API_ERROR_METHOD_DISPLAY', utf8_htmlspecialchars($tmp)), E_USER_ERROR);
					}
				}
				else
				{
					functions\add_hooks_lang();
					$output = call_user_func($display_prefix . 'hook_display_' . $this->output, $array, $this);
				}
			break;
		}
		if (!$return)
		{
			//header('Content-Length: ' . strlen($output));
			//Here we bypass CDN like cloudflare...
			//header('Content-Length2: ' . strlen($output));
			$output = $this->encrypt($output, $this->api_secret_key, true);
			echo $output;
		}
		else
		{
			return $output;
		}
		//This is not really needed since we ignore the sid while script begin...
		if (!empty($this->config['api_mod_force_logout']) && empty($this->bypass_logout))
		{
			$this->user->session_kill(false);
		}
		//This is the cron's job to call them
		//garbage_collection();
		//exit_handler();

		$this->cron($this->config['api_mod_cron_task']);
	}

	/****
	* cron() Forked from cron.php
	* Send daily cron stats, simple-cleaning the API
	* @param bool $cron Cron enabling status
	****/
	protected function cron($cron)
	{
		if ($cron && !defined('IN_API_CRON') && !$this->ignore_cron)
		{
			//Turn on output buffering
			@ob_start();

			//Multi-instance protection
			define('IN_API_CRON', true);

			if (!isset($this->config['api_cron_lock']))
			{
				set_config('api_cron_lock', $this->now, true);
			}

			// make sure the cron task doesn't run multiple times in parallel (since another user)
			if ($this->config['api_cron_lock'])
			{
				// Check if we've already sent the daily digest
				$cron_status = explode(' ', $this->config['api_cron_lock']);
				$time = $cron_status[0];
				$locked = @$cron_status[1];
				//Something went wrong, the cron task is locked on a old day, unlock it.
				if (date('j') != date('j', $time) || date('n') != date('n', $time) || date('Y') != date('Y', $time) && $locked)
				{
					$sql = 'UPDATE ' . CONFIG_TABLE . "
						SET config_value = " . (int) $time . "
						WHERE config_name = 'api_cron_lock'";
					$this->db->sql_query($sql);
					$this->cache->purge();
				}
				//The cron task has been sent today or is locked, stop here.
				else if ((date('j') == date('j', $time) && date('n') == date('n', $time) && date('Y') == date('Y', $time)) || $locked)
				{
					goto bans_cleaner;
				}
			}
			//Lock the cron status
			//Due to a bug with "is_dynamic" column, we do not use set_config() #26125
			$sql = 'UPDATE ' . CONFIG_TABLE . "
				SET config_value = '" . $this->db->sql_escape($this->now . ' ' . true) . "'
				WHERE config_name = 'api_cron_lock'";
			$this->db->sql_query($sql);

			//Not needed as the api_cron_lock config is dynamic
			//$this->cache->purge();

			$founders = array();

			//We may use these later...
			//$first_day = (date('j') == 1) ? true : false;
			//$last_month_time_beginning = mktime(0, 0, 0, ((date("n") == 1) ? date("n") - 1 : 12), 1, date("Y") - ((date("n") == 1) ? 1 : 0));
			//$last_month_time_ending = mktime(23, 59, 59, ((date("n") == 1) ? date("n") - 1 : 12), 32, date("Y") - ((date("n") == 1) ? 1 : 0));

			//Get Stats !!
			$assign_vars = array();

			//Yesterday's date
			$assign_vars['DATE'] = $this->user->format_date($this->now - API_DAY_SECONDS, $this->user->lang['API_STATS_DAY_FMT']);

			//Calculate yesterday's first/last seconds
			$begin_day_time = (int) strtotime("midnight", $this->now - API_DAY_SECONDS);
			$end_day_time = $begin_day_time + API_DAY_SECONDS;

			//Total queries
			$sql = 'SELECT COUNT(history_id) AS total
				FROM ' . API_HISTORY_TABLE . '
				WHERE time BETWEEN ' . $begin_day_time . ' AND ' . $end_day_time;
			$result = $this->db->sql_query($sql);
			$assign_vars['TOTAL_QUERIES'] = $this->db->sql_fetchfield('total');
			$this->db->sql_freeresult($result);

			//Total users
			$sql = 'SELECT COUNT(DISTINCT u.user_id) AS active_users
				FROM ' . API_HISTORY_TABLE . ' h
				LEFT JOIN ' . API_KEYS_TABLE . ' k
					ON(k.key_id = h.key_id)
				LEFT JOIN  ' . USERS_TABLE . ' u
					ON(u.user_id = k.user_id)
				WHERE h.time BETWEEN ' . $begin_day_time . ' AND ' . $end_day_time;
			$result = $this->db->sql_query($sql);
			$assign_vars['TOTAL_USERS'] = $this->db->sql_fetchfield('active_users');
			$this->db->sql_freeresult($result);

			//Last query
			$sql = 'SELECT MAX(time) AS time
				FROM ' . API_HISTORY_TABLE . '
				WHERE time BETWEEN ' . $begin_day_time . ' AND ' . $end_day_time;
			$result = $this->db->sql_query_limit($sql, 1);
			$last_querie = $this->db->sql_fetchfield('time');
			if (empty($last_querie))
			{
				$last_querie = $this->user->lang['API_NO_RECORD'];
			}
			else
			{
				$last_querie = $this->user->format_date($last_querie);
			}
			$assign_vars['LAST_QUERIE_DATE'] = $last_querie;
			$this->db->sql_freeresult($result);

			//Expired keys
			$sql = 'SELECT COUNT(key_id) AS expired_keys
				FROM ' . API_KEYS_TABLE . '
				WHERE expire_time BETWEEN ' . $begin_day_time . ' AND ' . $end_day_time;
			$result = $this->db->sql_query($sql);
			$assign_vars['EXPIRED_KEY_COUNT'] = $this->db->sql_fetchfield('expired_keys');
			$this->db->sql_freeresult($result);

			//Created keys
			$sql = 'SELECT COUNT(key_id) AS created_keys
				FROM ' . API_KEYS_TABLE . '
				WHERE creation_time BETWEEN ' . $begin_day_time . ' AND ' . $end_day_time;
			$result = $this->db->sql_query($sql);
			$assign_vars['CREATED_KEY_COUNT'] = $this->db->sql_fetchfield('created_keys');
			$this->db->sql_freeresult($result);

			//Total IP ban recorded
			$sql = 'SELECT COUNT(attempt_ip) AS total
				FROM ' . API_LOGIN_ATTEMPTS . '
				WHERE attempt_time <= BETWEEN ' . $begin_day_time . ' AND ' . $end_day_time;
			$result = $this->db->sql_query($sql);
			$assign_vars['TOTAL_BANNED_IPS'] = $this->db->sql_fetchfield('total');
			$this->db->sql_freeresult($result);

			//Get all founder, then email them
			$sql = 'SELECT *
				FROM ' . USERS_TABLE .  '
				WHERE user_type = ' . USER_FOUNDER;
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$founders[$row['user_id']] = $row;
			}
			$this->db->sql_freeresult($result);

			foreach ($founders AS $founders_)
			{
				$email_template = 'api/daily_statistics';
				$this->messenger->template($email_template, $founders_['user_lang']);
				$this->messenger->to($founders_['user_email'], $founders_['username']);
				$this->messenger->anti_abuse_headers($this->config, $user);

				$this->messenger->assign_vars(array_merge($assign_vars, array(
					'BOARD_CONTACT'			=> htmlspecialchars_decode($this->config['board_email']),
					'USERNAME'				=> htmlspecialchars_decode($founders_['username']),
					'SITENAME'				=> htmlspecialchars_decode($this->config['sitename']),
					'EMAIL_SIG'				=> htmlspecialchars_decode($this->config['board_email_sig']),
				)));
				$this->messenger->send(NOTIFY_EMAIL);
			}
			//Rebuild the API cache.
			$this->api_cache->rebuild();

			//Unlock the cron status
			//Due to a bug with "is_dynamic", we do not use set_config() #26125
			$sql = 'UPDATE ' . CONFIG_TABLE . "
				SET config_value = " . (int) $this->now . "
				WHERE config_name = 'api_cron_lock'";
			$this->db->sql_query($sql);

			//Not needed as the api_cron_lock config is dynamic
			//$this->cache->purge();
		}

		bans_cleaner:

		//Unban former API bans.
		if (true)
		{
			$sql = 'DELETE FROM ' . API_LOGIN_ATTEMPTS . '
				WHERE attempt_time <= ' . (int) ($this->now - $this->config['api_mod_max_attempts_time']);
			$result = $this->db->sql_query($sql);
			set_config('api_next_ip_unban', $this->now + API_HOUR_SECONDS);
		}

		// Destroy the output buffer, we do not expect to output something anymore.
		@ob_end_clean();
		garbage_collection();
		exit_handler();
	}
}