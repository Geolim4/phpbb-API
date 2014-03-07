<?php
/**
*
* @package phpBB3 API Class core
^>@version $Id: core_methods.php v0.0.1 00h11 12/20/2013 Geolim4 Exp $
* @copyright (c) 2012 - 2014 Geolim4.com http://geolim4.com
* @bug/function request: http://geolim4.com/tracker
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/
namespace phpbb_api;

/**
* @ignore
*/
if (!defined('IN_PHPBB') || !defined('IN_PHPBB_API'))
{
	exit;
}

trait core_methods
{
	/****
	* api_get_methods()
	* Get available methods
	* @param string $data --Not used--
	* @param string $type --Not used--
	****/
	protected function api_get_methods($data, $type)//Protected as this method must be ignored by Reflection extension
	{
		/**
		* @ignore unprivileged user
		*/
		//$this->check_admin_privilege(substr(__FUNCTION__, 4));
		//$this->sanitize(__FUNCTION__, $data, $type);
		$this->skip_counter = true;
		$this->skip_crypto = false;

		$rows = array('lang' => array(), 'name' => array());

		foreach ($this->api_privileges AS $privilege_name => $privilege_value)
		{
			if (!empty($this->api_privileges[$privilege_name][$this->api_type]) && !in_array($privilege_name, explode(',', $this->key_options['deactivated_methods'])))
			{
				$rows['lang'][$privilege_name] = isset($this->user->lang['API_FULL_TRANSLATED_METHOD'][$privilege_name]) ? $this->user->lang['API_FULL_TRANSLATED_METHOD'][$privilege_name] : $privilege_name;
				$rows['name'][$privilege_name] = isset($this->user->lang['API_TRANSLATED_METHOD'][$privilege_name]) ? $this->user->lang['API_TRANSLATED_METHOD'][$privilege_name] : $privilege_name;
			}
		}
		$this->display($rows);
	}

	/****
	* api_get_submethods()
	* Get available methods
	* @param string $data --Not used--
	* @param string $type --Not used--
	****/
	protected function api_get_submethods($data, $type)//Protected as this method must be ignored by Reflection extension
	{
		/**
		* @ignore unprivileged user
		*/
		//$this->check_admin_privilege(substr(__FUNCTION__, 4));
		//$this->sanitize(__FUNCTION__, $data, $type);
		$this->skip_counter = true;
		$this->skip_crypto = false;

		$rows = $submethods = array();

		if (isset($this->api_privileges[$type]))
		{
			if (empty($this->api_privileges[$type][$this->api_type]))
			{
				$this->trigger_error('NOT_AUTHORISED', E_USER_WARNING);
			}
		}
		else
		{
			$this->trigger_error('API_ERROR_NO_SUBMETHOD', E_USER_WARNING);
		}

		if (isset($this->api_methods_type[$type]))
		{
			foreach ($this->api_methods_type[$type] AS $methods_type_k => $methods_type_v)
			{
				if (isset($this->user->lang['API_FULL_TRANSLATED_SUBMETHOD'][$methods_type_k]))
				{
					$submethods[$methods_type_k] = $this->user->lang['API_FULL_TRANSLATED_SUBMETHOD'][$methods_type_k];
				}
				else
				{
					$submethods[$methods_type_k] = $methods_type_v;
				}
			}
		}
		$rows[$type] = $submethods;
		$this->display($rows);
	}

	/****
	* api_get_crypto_config()
	* Get available methods
	* @param string $data --Not used--
	* @param string $type --Not used--
	****/
	protected function api_get_crypto_config($data, $type)//Protected as this method must be ignored by Reflection extension
	{
		/**
		* @ignore unprivileged user
		*/
		//$this->check_admin_privilege(substr(__FUNCTION__, 4));
		$this->skip_counter = true;
		$this->skip_crypto = false;

		$this->display($this->get_crypto_config());
	}

	/****
	* api_login()
	* Login into the API using the key.
	* @param string $data --Not used--
	* @param string $type --Not used--
	****/
	private function api_login($data, $type)
	{
		/**
		* @ignore unprivileged user
		*/
		$this->check_admin_privilege(substr(__FUNCTION__, 4));
		$this->sanitize(__FUNCTION__, $data, $type);
		$this->ignore_cron = false;
		$this->skip_counter = false;
		$this->skip_crypto = false;
		$this->bypass_logout = true;
		$this->custom_output = true;
		$this->output = API_CUSTOM_OUTPUT;
		$this->template_content = $this->get_template_content('api_default.html');
		$this->user->set_cookie('api', unique_id(), time() + $this->config['session_length']);

		header('Content-Type: text/html; charset=UTF-8');
		
		$vars = array(
			'HEAD_META_CONTENT' => '<meta http-equiv="refresh" content="3; url=' . append_sid(generate_board_url()) . '" />',
			'CSS_CONTENT' => 'p{font-weight: bold;text-align: center;}',
			'MAIN_HTML_CONTENT' => '<p>' . $this->user->lang['API_LOGIN_WAIT'] . '</p>',
			'METHOD_NAME' => $this->api_action_translated,
		);

		$this->output_str = preg_replace_callback('#\{([A-Z0-9_]*)\}#', 
			function($match) use ($vars)
			{
				if (isset($vars[$match[1]]))
				{
					return $vars[$match[1]];
				}
				else
				{
					if (substr($match[1], 0, 2) == 'L_')
					{
						if (isset($this->user->lang[substr($match[1], 2)]))
						{
							return $this->user->lang[substr($match[1], 2)];
						}
						return substr($match[1], 2);
					}
					return '';
				}
			},
			$this->template_content
		);

		functions\api_add_log('API_LOG_API_LOGIN_ACCOUNT', $this->api_key);
		$this->display();
	}

	/****
	* api_search_ip()
	* Search an IP in the forum.
	* @param string $data The IP we're looking for
	* @param string $type The table we're looking into
	****/
	private function api_search_ip($data, $type)
	{
		/**
		* @ignore unprivileged user
		*/
		$this->check_admin_privilege(substr(__FUNCTION__, 4));
		$this->sanitize(__FUNCTION__, $data, $type);
		$this->skip_counter = false;
		$this->skip_crypto = false;

		$rows = array();

		if ($type == 'all' || $type == 'users')
		{
			$record = array();
			$sql = 'SELECT username
				FROM ' . USERS_TABLE . '
				WHERE user_ip =\''  . $data . '\'';
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$record[] = $row['username'];
			}
			$this->db->sql_freeresult($result);

			if (!empty($record))
			{
				$rows['users'] = array_unique($record);
			}
		}

		if ($type == 'all' || $type == 'sessions')
		{
			$record = array();
			$sql = 'SELECT u.username
				FROM ' . SESSIONS_TABLE . ' s
				LEFT JOIN ' . USERS_TABLE . ' u
					ON(u.user_id = s.session_user_id)
				WHERE s.session_ip =\''  . $data . '\'';
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$record[] = $row['username'];
			}
			$this->db->sql_freeresult($result);

			if (!empty($record))
			{
				$rows['sessions'] = array_unique($record);
			}
		}

		if ($type == 'all' || $type == 'posts')
		{
			$record = array();
			$sql = 'SELECT u.username
				FROM ' . POSTS_TABLE . ' p
				LEFT JOIN ' . USERS_TABLE . ' u
					ON(u.user_id = p.poster_id)
				WHERE p.poster_ip =\''  . $data . '\'';
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$record[] = $row['username'];
			}
			$this->db->sql_freeresult($result);

			if (!empty($record))
			{
				$rows['posts'] = array_unique($record);
			}
		}

		if ($type == 'all' || $type == 'logs')//Including API's logs
		{
			$record = array();
			//Check phpBB log table
			$sql = 'SELECT u.username
				FROM ' . LOG_TABLE . ' l
				LEFT JOIN ' . USERS_TABLE . ' u
					ON(u.user_id = l.user_id)
				WHERE l.log_ip =\''  . $data . '\'';
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$record[] = $row['username'];
			}
			$this->db->sql_freeresult($result);
			
			//Check API log table
			$sql = 'SELECT u.username
				FROM ' . API_LOG_TABLE . ' l
				LEFT JOIN ' . USERS_TABLE . ' u
					ON(u.user_id = l.user_id)
				WHERE l.log_ip =\''  . $data . '\'';
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$record[] = $row['username'];
			}
			$this->db->sql_freeresult($result);

			if (!empty($record))
			{
				$rows['logs'] = array_unique($record);
			}
		}

		if ($type == 'all' || $type == 'banlist')
		{
			$record = array();
			//Check banlist table
			$sql = 'SELECT ban_ip
				FROM ' . BANLIST_TABLE . '
				WHERE ban_ip =\''  . $data . '\'';
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$record[] = $row['ban_ip'];
			}
			$this->db->sql_freeresult($result);

			if (!empty($record))
			{
				$rows['banlist'] = array_unique($record);
			}
		}

		if ($type == 'all' || $type == 'login_attempts')
		{
			$record = array();
			//Check login attempts table
			$sql = 'SELECT COUNT(attempt_ip) AS attempt_ip
				FROM ' . LOGIN_ATTEMPT_TABLE . '
				WHERE attempt_ip =\''  . $data . '\'';
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				if (!empty($row['attempt_ip']))
				{
					$record[] = $data . '(' . $row['attempt_ip'] . ')';
				}
			}
			$this->db->sql_freeresult($result);

			if (!empty($record))
			{
				$rows['login_attempts'] = array_unique($record);
			}
		}

		$this->filter($rows, substr(__FUNCTION__, 4));
		$this->display($rows);
	}

	/****
	* api_post()
	* Grab post datas
	* @param string $data data to grab
	* @param string $type Type of data to grab
	****/
	private function api_post($data, $type)
	{
		/**
		* @ignore unprivileged user
		*/
		$this->check_admin_privilege(substr(__FUNCTION__, 4));
		$this->sanitize(__FUNCTION__, $data, $type);
		$this->skip_counter = false;
		$this->skip_crypto = false;

		global $sql_sorting;
		$sql_sorting = functions\sql_sorting($sql_sorting, $data);

		$rows = array();
		if ($type && $data !== '')
		{
			$sql = "SELECT *
				FROM " . POSTS_TABLE . "
				WHERE $type {$sql_sorting['operator']} $data";
			$result = $this->db->sql_query_limit($sql, $sql_sorting['limit'], $sql_sorting['offset']);
			while ($row = $this->db->sql_fetchrow($result))
			{
				if ($this->auth->acl_get('f_read', $row['forum_id']) && ($row['post_approved'] || $this->auth->acl_get('m_approve', $row['forum_id'])))
				{
					$rows[] = $row;
				}
			}
			$this->db->sql_freeresult($result);

			foreach ($rows AS &$rows_)
			{
				$this->filter($rows_, substr(__FUNCTION__, 4));
			}
			$this->display($rows);
		}
		else
		{
			$this->trigger_error('API_ERROR_NO_SUBMETHOD', E_USER_WARNING);
		}
	}

	/****
	* api_topic()
	* Grab topic datas
	* @param string $data data to grab
	* @param string $type Type of data to grab
	****/
	private function api_topic($data, $type)
	{
		/**
		* @ignore unprivileged user
		*/
		$this->check_admin_privilege(substr(__FUNCTION__, 4));
		$this->sanitize(__FUNCTION__, $data, $type);
		$this->skip_counter = false;
		$this->skip_crypto = false;

		global $sql_sorting;
		$sql_sorting = functions\sql_sorting($sql_sorting, $data);

		$rows = array();
		if ($type && $data !== '')
		{
			$sql = "SELECT *
				FROM " . TOPICS_TABLE . "
				WHERE $type {$sql_sorting['operator']} $data";
			$result = $this->db->sql_query_limit($sql, $sql_sorting['limit'], $sql_sorting['offset']);
			while ($row = $this->db->sql_fetchrow($result))
			{
				if ($this->auth->acl_get('f_read', $row['forum_id']) && ($row['topic_approved'] || $this->auth->acl_get('m_approve', $row['forum_id'])))
				{
					$rows[] = $row;
				}
			}
			$this->db->sql_freeresult($result);

			foreach ($rows AS &$rows_)
			{
				$this->filter($rows_, substr(__FUNCTION__, 4));
			}
			$this->display($rows);
		}
		else
		{
			$this->trigger_error('API_ERROR_NO_SUBMETHOD', E_USER_WARNING);
		}
	}

	/****
	* api_forum()
	* Grab forum datas
	* @param string $data data to grab
	* @param string $type Type of data to grab
	****/
	private function api_forum($data, $type)
	{
		$this->check_admin_privilege(substr(__FUNCTION__, 4));
		$this->sanitize(__FUNCTION__, $data, $type);
		$this->skip_counter = false;
		$this->skip_crypto = false;

		global $sql_sorting;
		$sql_sorting = functions\sql_sorting($sql_sorting, $data);

		$rows = array();
		if ($type && $data !== '')
		{
			$sql = "SELECT *
				FROM " . FORUMS_TABLE . "
				WHERE $type {$sql_sorting['operator']} $data";
			$result = $this->db->sql_query_limit($sql, $sql_sorting['limit'], $sql_sorting['offset']);
			while ($row = $this->db->sql_fetchrow($result))
			{
				if ($this->auth->acl_get('f_read', $row['forum_id']))
				{
					$rows[] = $row;
				}
			}
			$this->db->sql_freeresult($result);

			foreach ($rows AS &$rows_)
			{
				$this->filter($rows_, substr(__FUNCTION__, 4));
			}
			$this->display($rows);
		}
		else
		{
			$this->trigger_error('API_ERROR_NO_SUBMETHOD', E_USER_WARNING);
		}
	}

	/****
	* api_group()
	* Grab group datas
	* @param string $data data to grab
	* @param string $type Type of data to grab
	****/
	private function api_group($data, $type)
	{
		$this->check_admin_privilege(substr(__FUNCTION__, 4));
		$this->sanitize(__FUNCTION__, $data, $type);
		$this->skip_counter = false;
		$this->skip_crypto = false;

		global $sql_sorting;
		$sql_sorting = functions\sql_sorting($sql_sorting, $data);

		$rows = array();
		if ($type && $data !== '')
		{
			$sql = "SELECT *
				FROM " . GROUPS_TABLE . "
				WHERE $type {$sql_sorting['operator']} $data";
			$result = $this->db->sql_query_limit($sql, $sql_sorting['limit'], $sql_sorting['offset']);
			while ($row = $this->db->sql_fetchrow($result))
			{
				if (isset($this->user->lang['G_' . $row['group_name']]))
				{
					reset($row);
					$row['group_real_name'] = $this->user->lang['G_' . $row['group_name']];
				}
				$rows[] = $row;
			}
			$this->db->sql_freeresult($result);
			
			//If the user cannot manage groups and is not member of hidden groups then remove them, the user should not be able to view these groups.
			if (!$this->auth->acl_get('a_group') &&  functions\in_array_key_per_value($rows, 'group_type', GROUP_HIDDEN))
			{
				$group_ids = array();
				$sql = "SELECT group_id
					FROM " . USER_GROUP_TABLE . "
					WHERE user_pending = 0 
						AND user_id = " . (int) $this->user->data['user_id'];
				$result = $this->db->sql_query($sql);
				while ($row = $this->db->sql_fetchrow($result))
				{
					$group_ids[] = $row['group_id'];
				}
				$this->db->sql_freeresult($result);

				foreach ($rows AS $key => $rows_)
				{
					if (!in_array($rows_['group_id'], $group_ids) && $rows_['group_type'] == GROUP_HIDDEN)
					{
						unset($rows[$key]);
					}
				}
			}
			foreach ($rows AS &$rows_)
			{
				$this->filter($rows_, substr(__FUNCTION__, 4));
			}
			$this->display($rows);
		}
		else
		{
			$this->trigger_error('API_ERROR_NO_SUBMETHOD', E_USER_WARNING);
		}
	}

	/****
	* api_refresh_stats()
	* Renew board stats
	* @param string $data data to renew
	* @param string $type Type of data to renew
	****/
	private function api_refresh_stats($data, $type = 'all')
	{
		/**
		* @ignore unprivileged user
		*/
		$this->check_admin_privilege(substr(__FUNCTION__, 4));
		$this->skip_counter = false;
		$this->skip_crypto = false;

		if ($type == 'all' || $type == 'num_posts')
		{
			$sql = 'SELECT COUNT(post_id) AS stat
				FROM ' . POSTS_TABLE . '
				WHERE post_approved = 1';
			$result = $this->db->sql_query($sql);
			set_config('num_posts', (int) $this->db->sql_fetchfield('stat'), true);
			$this->db->sql_freeresult($result);
		}

		if ($type == 'all' || $type == 'num_topics')
		{
			$sql = 'SELECT COUNT(topic_id) AS stat
				FROM ' . TOPICS_TABLE . '
				WHERE topic_approved = 1';
			$result = $this->db->sql_query($sql);
			set_config('num_topics', (int) $this->db->sql_fetchfield('stat'), true);
			$this->db->sql_freeresult($result);
		}

		if ($type == 'all' || $type == 'num_users')
		{
			$sql = 'SELECT COUNT(user_id) AS stat
				FROM ' . USERS_TABLE . '
				WHERE user_type IN (' . USER_NORMAL . ',' . USER_FOUNDER . ')';
			$result = $this->db->sql_query($sql);
			set_config('num_users', (int) $this->db->sql_fetchfield('stat'), true);
			$this->db->sql_freeresult($result);
		}

		if ($type == 'all' || $type == 'num_files')
		{
			$sql = 'SELECT COUNT(attach_id) as stat
				FROM ' . ATTACHMENTS_TABLE . '
				WHERE is_orphan = 0';
			$result = $this->db->sql_query($sql);
			set_config('num_files', (int) $this->db->sql_fetchfield('stat'), true);
			$this->db->sql_freeresult($result);
		}

		if ($type == 'all' || $type == 'upload_dir_size')
		{
			$sql = 'SELECT SUM(filesize) as stat
				FROM ' . ATTACHMENTS_TABLE . '
				WHERE is_orphan = 0';
			$result = $this->db->sql_query($sql);
			set_config('upload_dir_size', (float) $this->db->sql_fetchfield('stat'), true);
			$this->db->sql_freeresult($result);
		}

		if ($type == 'all' || $type == 'update_last_username')
		{
			if (!function_exists('update_last_username'))
			{
				include($this->phpbb_root_path . "includes/functions_user." . $this->phpEx);
			}
			update_last_username();
		}

		functions\api_add_log('API_LOG_RESYNC_STAT' . (($type == 'all') ? 'S' : ''), $this->api_key);
		$this->trigger_error('API_SUCCESS', E_USER_NOTICE);
	}

	/****
	* api_perm_ban()
	* Perm ban an user/ip/email
	* @param string $data What we'll ban
	* @param string $type Type of we'll ban
	****/
	private function api_perm_ban($data, $type = 'user')
	{
		/**
		* @ignore unprivileged user
		*/
		$this->check_admin_privilege(substr(__FUNCTION__, 4));
		$this->user->add_lang(array('acp/ban', 'acp/users'));
		$this->skip_counter = false;
		$this->skip_crypto = false;

		switch($type)
		{
			case'user':
			case'ip' :
			case'email':
				if (!function_exists('user_ban'))
				{
					include($this->phpbb_root_path . 'includes/functions_user.' . $this->phpEx);
				}
				if (user_ban($type, $data, 0, 0, false, $this->user->lang['API_BAN_REASON'],  $this->user->lang['API_BAN_REASON']))
				{
					functions\api_add_log('API_LOG_BAN_' . strtoupper($type), $this->api_key, $data);
					$this->trigger_error('API_SUCCESS', E_USER_NOTICE);
				}
			break;
		}

	}

	/****
	* api_unban()
	* Unban an user/ip/email
	* @param string $data What we'll unban
	* @param string $type Type of we'll unban
	****/
	private function api_unban($data, $type = 'user')
	{
		/**
		* @ignore unprivileged user
		*/
		$this->check_admin_privilege(substr(__FUNCTION__, 4));
		$this->user->add_lang(array('acp/ban', 'acp/users'));
		$this->skip_counter = false;
		$this->skip_crypto = false;

		switch($type)
		{
			case'user':
			case'ip' :
			case'email':
				switch($type)
				{
					case'user':
						$sql = 'SELECT user_id
							FROM ' . USERS_TABLE . "
							WHERE username_clean = '" . $this->db->sql_escape(utf8_clean_string($data)) . "'";
						$result = $this->db->sql_query($sql);
						$username = $data;
						$data = (int) $this->db->sql_fetchfield('user_id');
						$this->db->sql_freeresult($result);

						$where_sql = "ban_userid = %s";
					break;

					case'ip' :
						$where_sql = "ban_ip = '%s'";
					break;

					case'email':
						$where_sql = "ban_email = '%s'";
					break;
				}

				if (!function_exists('user_unban'))
				{
					include($this->phpbb_root_path . 'includes/functions_user.' . $this->phpEx);
				}

				$sql = 'SELECT ban_id
					FROM ' . BANLIST_TABLE . '
					WHERE ' . sprintf($where_sql, $this->db->sql_escape($data) );
				$result = $this->db->sql_query($sql);
				$ban = $this->db->sql_fetchfield('ban_id');
				$this->db->sql_freeresult($result);

				if ($ban)
				{
					user_unban($type, $ban);
					if ($type == 'user')
					{
						$data = $username;
					}
					functions\api_add_log('API_LOG_UNBAN_' . strtoupper($type), $this->api_key, $data);
					$this->trigger_error('API_SUCCESS', E_USER_NOTICE);
				}
				else
				{
					$this->trigger_error('API_NO_RECORD', E_USER_NOTICE);
				}
			break;
		}

	/****
	* api_get_config()
	* Get forum config
	* @param string $data bans to grab
	* @param string $type Type of ban to grab
	****/
	}
	private function api_get_bans($data, $type = 'all')
	{
		/**
		* @ignore unprivileged user
		*/
		$this->check_admin_privilege(substr(__FUNCTION__, 4));
		$this->skip_counter = false;
		$this->skip_crypto = false;

		$rows = array();
		if ($data)
		{
			$where_data = " = '" . $this->db->sql_escape($data) . "'";
		}
		else
		{
			$where_data =  " != ''";
		}
		$type = str_replace('user_id', 'userid', $type);//Small tips again phpbb_users.user_id column ;)

		switch($type)
		{
			case 'userid':
			case 'ip':
			case 'email':
				$where_sql = " WHERE ban_{$type} $where_data";
			break;

			case 'all':
			default:
				$where_sql = '';
			break;
		}
		$sql = 'SELECT *
			FROM ' . BANLIST_TABLE . "
			$where_sql ";
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			if ($row['ban_userid'])
			{
				$rows['user_id'][] = $row;
			}
			else if ($row['ban_ip'])
			{
				$rows['ban_ip'][] = $row;
			}
			else if ($row['ban_email'])
			{
				$rows['ban_email'][] = $row;
			}
		}
		$this->db->sql_freeresult($result);

		foreach ($rows AS &$rows_)
		{
			foreach ($rows_ AS &$rows__)
			{
				$this->filter($rows__, substr(__FUNCTION__, 4));
			}
		}
		$this->display($rows);
	}

	/****
	* api_get_config()
	* Get forum config
	* @param string $data config to grab
	* @param string $type Type of config to grab
	****/
	private function api_get_config($data, $type = 'all')
	{
		/**
		* @ignore unprivileged user
		*/
		$this->check_admin_privilege(substr(__FUNCTION__, 4));
		$this->skip_counter = false;
		$this->skip_crypto = false;

		//We allow users to get some configuration points
		$rows = array();
		$data = explode(',', $data);
		switch ($type)
		{
			case 'dynamic';
			case 'cached';
				$sql = 'SELECT config_name, config_value
					FROM ' . CONFIG_TABLE . '
					WHERE is_dynamic = ' . (($type == 'dynamic') ? 1 : 0);
				$result = $this->db->sql_query($sql);
				while ($row = $this->db->sql_fetchrow($result))
				{
					$rows[$row['config_name']] = $row['config_value'];
				}
				$this->db->sql_freeresult($result);
			break;

			case 'custom':
				if (!sizeof($data))
				{
					$this->trigger_error('API_NO_RECORD', E_USER_NOTICE);
				}
				if (!$this->prefilter($data, substr(__FUNCTION__, 4)))
				{
					$this->trigger_error($this->user->lang('API_UNAUTHORIZED'), E_USER_WARNING);
				}
				$sql = 'SELECT config_name, config_value
					FROM ' . CONFIG_TABLE . '
					WHERE ' . $this->db->sql_in_set('config_name', $data);
				$result = $this->db->sql_query($sql);
				while ($row = $this->db->sql_fetchrow($result))
				{
					$rows[$row['config_name']] = $row['config_value'];
				}
				$this->db->sql_freeresult($result);
			break;

			case 'all':
			default:
				$rows = $this->config;
			break;
		}
		functions\api_add_log('API_LOG_GET_CONFIG', $this->api_key, implode(', ', array_flip($rows)));

		$this->filter($rows, substr(__FUNCTION__, 4));
		$this->display($rows);
	}

	/****
	* api_set_config()
	* Set forum config
	* @param string $data config => value to set
	* @param string $type Format of $data: Json/Serialize
	****/
	private function api_set_config($data, $type)
	{
		/**
		* @ignore unprivileged user
		*/
		$this->check_admin_privilege(substr(__FUNCTION__, 4));
		$this->skip_counter = false;
		$this->skip_crypto = false;

		switch($type)
		{
			case 'json':
				$data = json_decode(functions\unescape_gpc($data), true);
			break;

			case 'serialize';
				$data = unserialize($data);
			break;
		}
		$rows = array();
		if (is_array($data))
		{
			foreach ($data AS $config_name => $config_value)
			{
				set_config($config_name, $config_value);
				$rows[$config_name] = $config_value;
			}
		}
		else
		{
			$this->trigger_error('API_INCORRECT_DATA', E_USER_WARNING);
		}
		$this->display($rows);
	}

	/****
	* api_key_options()
	* Get current key options
	* @param string $data --Not used--
	* @param string $type --Not used--
	****/
	private function api_key_options($data, $type)
	{
		/**
		* @ignore unprivileged user
		*/
		$this->check_admin_privilege(substr(__FUNCTION__, 4));
		//We should not count that call
		$this->skip_counter = true;
		$this->skip_crypto = false;

		//Simply....
		$options = $this->key_options;
		$options['key_ips'] = $deactivated_methods = array();

		foreach (explode("\n", $this->key_options['key_ips']) AS $key => $key_ips_)
		{
			$options['key_ips'][$key] = $key_ips_;
		}

		foreach (explode(',', $options['deactivated_methods']) AS $deactivated_methods_)
		{
			$deactivated_methods[] = $deactivated_methods_;
		}

		$options['deactivated_methods'] = $deactivated_methods;
		$options['last_five_queries'] = array_slice($options['queries_history'], 0, 5, true);
		unset($options['queries_history']);

		$this->filter($options, substr(__FUNCTION__, 4));
		//functions\api_add_log('API_LOG_API_KEY_OPTION', $this->api_key); //Not really needed, un comment it if you want to log it...
		$this->display($options);
	}

	/****
	* api_key_stats()
	* Get key options
	* @param string $data --Not used--
	* @param string $type --Not used--
	****/
	private function api_key_stats($data, $type)
	{
		/**
		* @ignore unprivileged user
		*/
		$this->check_admin_privilege(substr(__FUNCTION__, 4));

		//We should not count that call
		$this->skip_counter = true;
		$this->skip_crypto = false;

		//Simply....
		$options = $this->api_key_stats;

		//functions\api_add_log('API_LOG_API_KEY_OPTION', $this->api_key); //Not really needed, uncomment it if you want to enable it...
		$this->display($options);
	}

	/****
	* api_sql_query()
	* Do a SQL query, can be used only with an Administrator Key...
	* @param string $data SQL query
	* @param string $type Additional statements: start/limit
	****/
	private function api_sql_query($data, $type)
	{
		/**
		* @ignore unprivileged user
		*/
		$this->check_admin_privilege(substr(__FUNCTION__, 4));
		$this->skip_counter = false;
		$this->skip_crypto = false;

		global $sql_sorting;
		$sql_sorting = functions\sql_sorting($sql_sorting, $data);
		$sql = &$data;
		$rows = array();

		if (!$this->key_options['query_sql'])
		{
			$this->trigger_error('API_UNAUTHORIZED_FN', E_USER_WARNING);
		}
		//Unfortunately here match also statement content....
		if (!$this->key_options['query_sql_api'] && (stripos($data, API_KEYS_TABLE) !== false || stripos($data, LOG_TABLE) !== false || stripos($data, API_LOG_TABLE) !== false  || stripos($data, API_HISTORY_TABLE) !== false ))
		{
			functions\api_add_log('API_LOG_SQL_QUERY_UNAUTHORIZED', $this->api_key, $sql);
			$this->trigger_error($this->user->lang('API_UNAUTHORIZED_SQL_API'), E_USER_WARNING);
		}
		if (!functions\is_post_request())
		{
			//$this->trigger_error($this->user->lang('API_ERROR_METHOD_REQUEST', functions\get_request_method()), E_USER_WARNING);
		}
		if (!($sql = trim($sql)))
		{
			$this->trigger_error('API_ERROR_EMPTY_SQL', E_USER_NOTICE);
		}
		$result = $this->db->sql_query_limit($sql, $sql_sorting['limit'], $sql_sorting['offset']);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$rows[] = $row;
		}
		$this->db->sql_freeresult($result);

		functions\api_add_log('API_LOG_SQL_QUERY', $this->api_key, $sql);
		//An empty result does not mean necessarily than the sql query was failed, if that was the case, DBAL engine would return an E_USER_ERROR alert.
		if (empty($rows))
		{
			$this->trigger_error($this->user->lang('API_SUCCESS_QUERY', $sql), E_USER_NOTICE);
		}
		$this->display($rows);
	}

	/****
	* api_get_constants()
	* Get user-defined constant
	* @param string $data --Not used--
	* @param mixed $type --Not used--
	****/
	private function api_get_constants($data, $type)
	{
		/**
		* @ignore unprivileged user
		*/
		$this->check_admin_privilege(substr(__FUNCTION__, 4));
		$this->skip_counter = true;
		$this->skip_crypto = false;

		$defined_constants = get_defined_constants(true);
		$defined_constants['user'] = functions\array_key_match($defined_constants['user'], '^[A-Z]([A-Z0-9_])+$');
		$this->display($defined_constants['user']);
	}

	/****
	* board_status()
	* Change the board status: Disabled/Enabled
	* @param string $data Short message to display
	* @param mixed $type Board status
	****/
	private function api_board_status($data, $type)
	{
		/**
		* @ignore unprivileged user
		*/
		$this->check_admin_privilege(substr(__FUNCTION__, 4));
		$this->skip_counter = false;
		$this->skip_crypto = false;

		//This is ambiguous, right?
		if (preg_grep("#$type#i", array(strtoupper($this->user->lang['API_STATUS_DISABLE']), 'disable', 'off', false, 0)))
		{
			$type = true;
		}
		else if (preg_grep("#$type#i", array(strtoupper($this->user->lang['API_STATUS_ENABLE']), 'enable', 'on', true, 1)))
		{
			$type = false;
		}
		if (utf8_strlen($data) > 255)
		{
			$data = utf8_substr($data, 0, 252) . '...';
		}

		set_config('board_disable', $type);
		set_config('board_disable_msg', $data);

		$this->trigger_error('API_SUCCESS', E_USER_NOTICE);
	}
	
	/****
	* api_php_configuration()
	* Get the php configuration
	* @param string $data Short message to display
	* @param mixed $type Board status
	****/
	private function api_php_configuration($data, $type)
	{
		/**
		* @ignore unprivileged user
		*/
		$this->check_admin_privilege(substr(__FUNCTION__, 4));
		$this->skip_counter = false;
		$this->skip_crypto = false;

		if ($type)
		{
			$rows = ini_get_all($type);
		}
		else
		{
			$rows = ini_get_all();
		}
		$this->display($rows);
	}
}