<?php
/**
*
* @package ACP phpBB API
^>@version $Id: acp_phpbb_api.php v0.0.1 00h11 12/20/2013 Geolim4 Exp $
* @copyright (c) 2012 - 2014 Geolim4.com http://geolim4.com
* @bug/function request: http://geolim4.com/tracker
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* @package acp
*/

define('IN_PHPBB_API', true);

class acp_phpbb_api
{
	var $u_action;

	function main($id, $mode)
	{
		global $config, $phpbb_root_path, $phpEx, $db, $user, $template, $phpbb_admin_path, $auth, $table_prefix, $dbname, $cache;

		if (!defined('phpbb_api\API_CONST_LOADED'))
		{
			include($phpbb_root_path . 'includes/api/constants.' . $phpEx);
			include($phpbb_root_path . 'includes/api/functions.' . $phpEx);
		}
		//Check install before all !!
		$this->api_check_install();
		$profile_url = append_sid("{$phpbb_admin_path}index.$phpEx", 'i=users&amp;mode=overview');
		$user->add_lang(array('mods/phpbb_api', 'install'));

		//Change the local timezone depending user timezone
		$timetable = phpbb_api\functions\automatic_dst_get_timetable();
		date_default_timezone_set($timetable[$user->data['user_timezone']]);

		//tpl settings..
		$this->page_title = $user->lang['ACP_PHPBB_API'] . ' : ' . $user->lang['ACP_PHPBB_API_' . strtoupper($mode)];
		//Grab basic vars
		$marked					= request_var('mark', array(''));
		$key_id					= request_var('key_id', array(''));
		$action					= request_var('action', '');
		$secret_key_reset		= request_var('secret_key_reset', false);

		//grab submit buttons
		$update					= (isset($_POST['update']))	? true : false;
		$submit					= (isset($_POST['submit']))	? true : false;
		$cancel					= (isset($_POST['cancel']))	? true : false;
		$create					= (isset($_POST['create']))	? true : false;
		$search					= (isset($_POST['search']))	? true : false;
		$ajax					= (isset($_POST['ajax']))	? true : false;
		//secure your mom
		$form_key = 'acp_phpbb_api';
		add_form_key($form_key);

		$sql_ary = array();
		$latest_version = $announcement_url = $trigger_info = $api_exclusion_options = $ignored = '';//Empty string...
		$loop = $total_posts = $word_count = $start = 0;

		$this->tpl_name = 'api/acp_phpbb_api_' . $mode;
		switch ($mode)
		{
			case 'config':
					// Get current and latest version
					$errstr = '';
					$errno = 0;
					$info = get_remote_file('gl4.fr', '', 'phpbb_api.txt', $errstr, $errno);
					if ($ajax)
					{
						goto db_test;
					}
					if ($update)
					{
						//before all check form integrity pls !!
						if (!check_form_key($form_key))
						{
							trigger_error($user->lang['FORM_INVALID'], E_USER_WARNING);
						}
						$no_db_change = (isset($_POST['api_mod_db_no_change'])) ? true : false;
						$settings = array (
							'api_mod_enable'				=> request_var('api_mod_enable', 1),
							'api_mod_ucp_keys'				=> request_var('api_mod_ucp_keys', 1),
							'api_mod_force_logout'			=> request_var('api_mod_force_logout', 1),
							'api_mod_max_queries'			=> request_var('api_mod_max_queries', 10000),
							'api_mod_mqpd'					=> request_var('api_mod_mqpd', 10),
							'api_mod_mqpw'					=> request_var('api_mod_mqpw', 100),
							'api_mod_mqpm'					=> request_var('api_mod_mqpm', 1000),
							'api_mod_time_type'				=> request_var('api_mod_time_type', API_CALENDAR_TIME),
							'api_mod_list_ip'				=> request_var('api_mod_list_ip', 1),
							'api_mod_acp_pagination'		=> request_var('api_mod_acp_pagination', 10),
							'api_mod_backtrace'				=> request_var('api_mod_backtrace', 1),
							'api_mod_fatal_html'			=> request_var('api_mod_fatal_html', 1),
							'api_mod_query_limit'			=> request_var('api_mod_query_limit', 30),
							'api_mod_header'				=> request_var('api_mod_header', 1),
							'api_mod_origin_header'			=> request_var('api_mod_origin_header', 1),
							'api_mod_ucp_pagination'		=> request_var('api_mod_ucp_pagination', 25),
							'api_mod_ucp_expire_value'		=> request_var('api_mod_ucp_expire_value', 6),
							'api_mod_ucp_expire_type'		=> request_var('api_mod_ucp_expire_type', API_EXPIRE_MONTH),
							'api_mod_stat_limit'			=> request_var('api_mod_stat_limit', 250),
							'api_mod_ucp_crypt'				=> request_var('api_mod_ucp_crypt', 1),
							'api_mod_db_credentials'		=> request_var('api_mod_db_credentials', 1),
							'api_mod_purge_temp'			=> request_var('api_mod_purge_temp', 0),
							'api_mod_purge_api'				=> request_var('api_mod_purge_api', 0),
							'api_mod_purge_bans'			=> request_var('api_mod_purge_bans', 0),
							'api_mod_deactivated_methods'	=> implode(',', request_var('api_mod_deactivated_methods', array(''))),
							'api_mod_force_ssl'				=> request_var('api_mod_force_ssl', 1),
							'api_mod_cron_task'				=> request_var('api_mod_cron_task', 1),
							'api_mod_wildcard_char'			=> request_var('api_mod_wildcard_char', '-'),
							'api_mod_faq_multi_column'		=> request_var('api_mod_faq_multi_column', 0),
							'api_mod_cache_stats'			=> request_var('api_mod_cache_stats', 1),
							'api_mod_max_attempts'			=> request_var('api_mod_max_attempts', 25),
							'api_mod_max_attempts_time'		=> request_var('api_mod_max_attempts_time', API_DAY_SECONDS),
							'api_mod_unban_ipbans'			=> request_var('api_mod_unban_ipbans', array('')),
							'api_mod_crypto_enabled'		=> request_var('api_mod_crypto_enabled', 0),
							'api_mod_whitelist'				=> request_var('api_mod_whitelist', ''),
						);
						//Check if provided credentials are correct
						if (!$no_db_change)
						{
							$api_mod_db_username = $api_mod_db_password = '';
							if ( $settings['api_mod_db_credentials'])
							{
								//Here we go from ajax test
								db_test:

								global $sql_db;
								$api_mod_db_username = str_replace('\'', '\\\'', trim(request_var('api_mod_db_username', '')));//Escape simple quote and trim username
								$api_mod_db_password = str_replace('\'', '\\\'', request_var('api_mod_db_password', ''));//We cannot trim a password...
								$db_test = new $sql_db();
								require($phpbb_root_path . 'config.' . $phpEx);

								// Connect to DB
								$db_test->sql_return_on_error(true);
								$db_test->sql_connect($dbhost, $api_mod_db_username, $api_mod_db_password, $dbname, $dbport, false, defined('PHPBB_DB_NEW_LINK') ? PHPBB_DB_NEW_LINK : false);

								if ($error = $db_test->sql_error())
								{
									if ($ajax && !empty($error['message']) && !empty($error['code']))
									{
										echo(json_encode(array(
											'msg' => '<span class="error">' . "{$error['message']}&nbsp;[{$error['code']}]" . '</span>'
										), JSON_FORCE_OBJECT));
										garbage_collection();
										exit_handler();
									}
									else
									{
										if (!empty($error['message']) && !empty($error['code']))
										{
											trigger_error("{$error['message']}&nbsp;[{$error['code']}]" . adm_back_link(append_sid($this->u_action)), E_USER_WARNING);
										}
									}
								}
							}
							if (is_writable(API_ROOT_PATH . 'config.' . $phpEx) && empty($ajax))
							{
								$handle = fopen(API_ROOT_PATH . 'config.' . $phpEx, 'wb');
								fwrite($handle, str_replace(array('%USER%', '%PASSWORD%'), array($api_mod_db_username, $api_mod_db_password), API_DEFAULT_CONFIG_FILE));
								fclose($handle);
							}
							if ($ajax)
							{
								echo(json_encode(array(
									'msg' => '<span class="success">' . $user->lang['SUCCESSFUL_CONNECT'] . '</span>'
								), JSON_FORCE_OBJECT));
								garbage_collection();
								exit_handler();
							}

						}
						if ($settings['api_mod_crypto_enabled'] && !extension_loaded('mcrypt'))
						{
							trigger_error('ACP_PHPBB_API_CRYPTO_ERROR', E_USER_WARNING);
						}
						if ($settings['api_mod_purge_temp'])
						{
							phpbb_api\functions\rrmdir(API_ROOT_PATH . 'pchart/tmp/', true);
							$cache->destroy('api_history');//Cannot use constant because of table prefix
						}
						if ($settings['api_mod_purge_api'])
						{
							include(API_ROOT_PATH . 'cache.' . $phpEx);
							$api_cache = new phpbb_api\api_cache();//API's cache
							$api_cache->purge();
						}
						if ($settings['api_mod_purge_bans'])
						{
							$sql = 'DELETE FROM ' . API_LOGIN_ATTEMPTS . '
								WHERE attempt_time < ' . (int) (time() - $config['api_mod_max_attempts_time']);
							$result = $db->sql_query($sql);
						}
						if ($settings['api_mod_unban_ipbans'])
						{
							$sql = 'DELETE FROM ' . API_LOGIN_ATTEMPTS . '
								WHERE ' . $db->sql_in_set('attempt_ip', $settings['api_mod_unban_ipbans']);
							$db->sql_query($sql);
						}
						unset($settings['api_mod_purge_temp'], $settings['api_mod_purge_api'], $settings['api_mod_purge_bans'], $settings['api_mod_unban_ipbans']);
						foreach ($settings as $config_name => $config_value)
						{
							if (!isset($config[$config_name]) || $config_value != $config[$config_name])
							{
								set_config($config_name, $config_value, false);
							}
						}
						add_log('admin', 'API_LOG_CONFIG_UPDATED');
						trigger_error($user->lang['ACP_PHPBB_API_UPDATED_CFG'] . $trigger_info . adm_back_link($this->u_action, ($start ? 'start=' . $start : '')));
					}
					if ($info === false)
					{
						$template->assign_vars(array(
							'S_ERROR'   => true,
							'ERROR_MSG' => sprintf($user->lang['API_UNABLE_CONNECT'], $errstr),
						));
					}
					else
					{
						$info 				= explode("\n", $info);
						$latest_version 	= trim($info[0]);
						$announcement_url 	= trim($info[1]);
						$up_to_date			= phpbb_version_compare($config['api_mod_version'], $latest_version, '<') ? false : true;

						if (!$up_to_date)
						{
							$template->assign_vars(array(
								'S_ERROR'   			=> true,
								'S_UP_TO_DATE'			=> false,
								'ERROR_MSG' 			=> sprintf($user->lang['API_NEW_VERSION'], $config['api_mod_version'], $latest_version),
								'UPDATE_INSTRUCTIONS'	=> sprintf($user->lang['API_ERRORS_UPDATE_INSTRUCTIONS'], $announcement_url, $latest_version),
							));
						}
						else
						{
							$template->assign_vars(array(
								'S_ERROR'   			=> false,
								'S_UP_TO_DATE'			=> true,
								'UP_TO_DATE_MSG'		=> sprintf($user->lang['API_ERRORS_VERSION_UP_TO_DATE'], $config['api_mod_version']),
								'UPDATE_INSTRUCTIONS'	=> sprintf($user->lang['API_ERRORS_INSTRUCTIONS'], $config['api_mod_version'], $announcement_url, trim($info[2]), trim($info[3])),
							));
						}
					}
					if ($cancel)
					{
						redirect(append_sid($this->u_action));
					}

					$sql = 'SELECT COUNT(attempt_ip) AS total
						FROM ' . API_LOGIN_ATTEMPTS . '
						WHERE attempt_time < ' . (int) (time() - $config['api_mod_max_attempts_time']);
					$result = $db->sql_query($sql);
					$active_ban_recorded = $db->sql_fetchfield('total');
					$db->sql_freeresult($result);

					$selector_ip = '';
					$sql = 'SELECT attempt_ip
						FROM ' . API_LOGIN_ATTEMPTS . '
						GROUP BY attempt_ip
						HAVING COUNT(attempt_ip) >= ' . (int) $config['api_mod_max_attempts'];
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result))
					{
						$selector_ip .= "<option value=\"{$row['attempt_ip']}\">{$row['attempt_ip']}</option>";
					}
					$db->sql_freeresult($result);

					$stats_files = phpbb_api\functions\directory_files_count(API_ROOT_PATH . 'pchart/tmp/');
					$cached_files = phpbb_api\functions\directory_files_count(API_ROOT_PATH . 'cache/');
					$api_mod_ucp_expire_value = $api_mod_ucp_expire_type = '';

					foreach (explode(',', API_EXPIRE_SELECTOR) AS $api_expire_selector_)
					{
						$api_mod_ucp_expire_value .= '<option value="' . $api_expire_selector_ . '"' . (($api_expire_selector_ ==  $config['api_mod_ucp_expire_value']) ? 'selected="selected"' : '' ) . '>' . $api_expire_selector_ . '</option>';
					}
					foreach (array(API_EXPIRE_HOUR, API_EXPIRE_DAY, API_EXPIRE_MONTH, API_EXPIRE_YEAR, API_EXPIRE_LIFETIME) AS $api_expire_selector_)
					{
						$api_mod_ucp_expire_type .= '<option value="' . $api_expire_selector_ . '"' . (($api_expire_selector_ ==  $config['api_mod_ucp_expire_type']) ? 'selected="selected"' : '' ) . '>' . (isset($user->lang[strtoupper($api_expire_selector_)]) ? $user->lang[strtoupper($api_expire_selector_)] : $api_expire_selector_) . '</option>';
					}
					$template->assign_vars(array(
						//pagination
						'S_VERSION'				=> isset($config['api_mod_version']) ? $config['api_mod_version'] : '',
						'S_ON_PAGE'				=> ($total_posts > $config['api_mod_acp_pagination']) ? true : false,
						'TOTAL_MESSAGES'		=> sprintf($user->lang['ACP_PHPBB_API_PAGINATION_KEY' .(($total_posts > 1) ? 'S' : '')], $total_posts),
						'PAGE_NUMBER' 			=> on_page($total_posts, $config['api_mod_acp_pagination'], $start),
						'PAGINATION' 			=> generate_pagination($this->u_action, $total_posts, $config['api_mod_acp_pagination'], $start),

						//Basics vars
						'S_CONFIG'				=> true,
						'U_ACTION'				=> $this->u_action,
						'TITLE'					=> $this->page_title,
						'TITLE_EXPLAIN'			=> $user->lang['API_ERRORS_CONFIG_EXPLAIN'],
						'TITLE_IMG'				=> $phpbb_root_path . 'images/api_' . $mode . '.png',

						//Database info
						'S_DB_NAME'				=> $dbname,
						'S_TABLE_PREFIX'		=> $table_prefix,

						//Mods vars
						'ERRORS_VERSION'				=> sprintf($user->lang['API_ERRORS_VERSION_COPY'], $announcement_url, $config['api_mod_version']),
						'S_NO_VERSION'					=> $latest_version ? false : true,
						'S_IS_FOUNDER'					=> ($user->data['user_type'] == USER_FOUNDER) ? true : false,
						'LATEST_VERSION'				=> $latest_version ? $latest_version : $user->lang['ERRORS_NO_VERSION'],
						'CURRENT_VERSION'				=> $config['api_mod_version'],
						'API_MOD_CACHED_FILES'			=> $user->lang('ACP_PHPBB_API_PURGE_FILES', $cached_files['total_files'], get_formatted_filesize($cached_files['total_size'])),
						'API_MOD_STATS_FILES'			=> $user->lang('ACP_PHPBB_API_PURGE_FILES', $stats_files['total_files'], get_formatted_filesize($stats_files['total_size'])),
						'API_MOD_BAN_RECORDED'			=> $active_ban_recorded > 1 ? $user->lang('ACP_PHPBB_API_BANS_RECORDED',  $active_ban_recorded) : $user->lang('ACP_PHPBB_API_BAN_RECORDED', $active_ban_recorded),
						'API_MOD_UNBAN_IPBANS'			=> $selector_ip ? $selector_ip : '<option value="0" class="disabled" disabled="disabled">' . $user->lang['ACP_PHPBB_API_NO_BAN_FOUND'] . '</option>',
						'API_MOD_ENABLE'				=> isset($config['api_mod_enable'])				? (int)	$config['api_mod_enable']			: 1,
						'API_MOD_UCP_KEYS'				=> isset($config['api_mod_ucp_keys'])			? (int)	$config['api_mod_ucp_keys']			: 1,
						'API_MOD_FORCE_LOGOUT'			=> isset($config['api_mod_force_logout'])		? (int)	$config['api_mod_force_logout']		: 0,
						'API_MOD_MAX_QUERIES'			=> isset($config['api_mod_max_queries'])		? (int)	$config['api_mod_max_queries']		: 10000,
						'API_MOD_MQPD'					=> isset($config['api_mod_mqpd'])				? (int)	$config['api_mod_mqpd']				: 10,
						'API_MOD_MQPW'					=> isset($config['api_mod_mqpw'])				? (int)	$config['api_mod_mqpw']				: 100,
						'API_MOD_MQPM'					=> isset($config['api_mod_mqpm'])				? (int)	$config['api_mod_mqpm']				: 1000,
						'API_MOD_TIME_TYPE'				=> isset($config['api_mod_time_type'])			? (int)	$config['api_mod_time_type']		: API_CALENDAR_TIME,
						'API_MOD_LIST_IP'				=> isset($config['api_mod_list_ip'])			? (int)	$config['api_mod_list_ip']			: 0,
						'API_MOD_ACP_PAGINATION'		=> isset($config['api_mod_acp_pagination'])		? (int)	$config['api_mod_acp_pagination']	: 10,
						'API_MOD_BACKTRACE'				=> isset($config['api_mod_backtrace'])			? (int)	$config['api_mod_backtrace']		: 1,
						'API_MOD_FATAL_HTML'			=> isset($config['api_mod_fatal_html'])			? (int)	$config['api_mod_fatal_html']		: 1,
						'API_MOD_QUERY_LIMIT'			=> isset($config['api_mod_query_limit'])		? (int)	$config['api_mod_query_limit']		: 30,
						'API_MOD_HEADER'				=> isset($config['api_mod_header'])				? (int)	$config['api_mod_header']			: 1,
						'API_MOD_ORIGIN_HEADER'			=> isset($config['api_mod_origin_header'])		? (int)	$config['api_mod_origin_header']	: 1,
						'API_MOD_UCP_PAGINATION'		=> isset($config['api_mod_ucp_pagination'])		? (int)	$config['api_mod_ucp_pagination']	: 25,
						'API_MOD_STAT_LIMIT'			=> isset($config['api_mod_stat_limit'])			? (int)	$config['api_mod_stat_limit']		: 250,
						'API_MOD_UCP_CRYPT'				=> isset($config['api_mod_ucp_crypt'])			? (int)	$config['api_mod_ucp_crypt']		: 1,
						'API_MOD_DB_CREDENTIALS'		=> isset($config['api_mod_db_credentials'])		? (int)	$config['api_mod_db_credentials']	: 1,
						'API_MOD_DEACTIVATED_METHODS'	=> isset($config['api_mod_deactivated_methods'])? (string) phpbb_api\functions\get_api_methods(true, true, explode(',', $config['api_mod_deactivated_methods'])) : '',
						'API_MOD_FORCE_SSL'				=> isset($config['api_mod_force_ssl'])			? (int)	$config['api_mod_force_ssl']		: 1,
						'API_MOD_CRON_TASK'				=> isset($config['api_mod_cron_task'])			? (int)	$config['api_mod_cron_task']		: 1,
						'API_MOD_UCP_EXPIRE_VALUE'		=> isset($config['api_mod_ucp_expire_value'])	? (int)	$config['api_mod_ucp_expire_value']	: 6,
						'API_MOD_UCP_EXPIRE_TYPE'		=> isset($config['api_mod_ucp_expire_type'])	? (string) $config['api_mod_ucp_expire_type'] : $user->lang['LIFETIME'],
						'API_MOD_WILDCARD_CHAR'			=> isset($config['api_mod_wildcard_char'])		? (string) $config['api_mod_wildcard_char'] : '-',
						'API_MOD_FAQ_MULTI_COLUMN'		=> isset($config['api_mod_faq_multi_column'])	? (int)	$config['api_mod_faq_multi_column']	: 0,
						'API_MOD_CACHE_STATS'			=> isset($config['api_mod_cache_stats'])		? (int)	$config['api_mod_cache_stats']		: 0,
						'API_MOD_MAX_ATTEMPTS'			=> isset($config['api_mod_max_attempts'])		? (int)	$config['api_mod_max_attempts']		: 25,
						'API_MOD_MAX_ATTEMPTS_TIME'		=> isset($config['api_mod_max_attempts_time'])	? (int)	$config['api_mod_max_attempts_time']: API_DAY_SECONDS,
						'API_MOD_CRYPTO_ENABLED'		=> isset($config['api_mod_crypto_enabled'])		? (int)	$config['api_mod_crypto_enabled']	: 0,
						'API_MOD_WHITELIST'				=> isset($config['api_mod_whitelist'])			? (string) $config['api_mod_whitelist'] 	: '',
						//Selectors
						'S_API_MOD_UCP_EXPIRE_VALUE'		=> $api_mod_ucp_expire_value,
						'S_API_MOD_UCP_EXPIRE_TYPE'			=> $api_mod_ucp_expire_type,

					));
			break;

			case 'keys' :
					$start = request_var('start', $start);
					$search_type = request_var('search_type', '');
					$search_field = utf8_normalize_nfc(request_var('search_field', '', true));
					$search_sorting = request_var('search_sorting', '');
					//$search
					switch ( $action)
					{
						case'suspend':
						case'active':
						case'deactivate':
							if ($key_id && !is_array($key_id))
							{
								$marked = array($key_id);
							}
							else if ($key_id && is_array($key_id))
							{
								$marked = $key_id;
							}
							if (empty($marked))
							{
								redirect($this->u_action);
							}

							$sql = 'UPDATE ' . API_KEYS_TABLE . "
								SET key_status = " . (( $action == 'active' ) ? API_STATUS_ACTIVE : (($action == 'suspend') ? API_STATUS_SUSPENDED : API_STATUS_DEACTIVATED)) . "
								WHERE " . $db->sql_in_set('key_id', $marked);
							$db->sql_query($sql);
							foreach ($marked AS $marked_)
							{
								//This is not the API log but the ADMIN log ;)
								add_log('admin', 'API_LOG_KEY_' . strtoupper($action), $marked_);
							}
							//We cannot load the default case of this switch for now...
							redirect($this->u_action);
						break;

						case'delete':
							if ($key_id && !is_array($key_id))
							{
								$marked = array($key_id);
							}
							else if ($key_id && is_array($key_id))
							{
								$marked = $key_id;
							}
							if (empty($marked))
							{
								redirect($this->u_action);
							}
							if (confirm_box(true))
							{
								$message = array();

								//remove the key
								$sql = 'DELETE FROM ' . API_KEYS_TABLE . " WHERE " . $db->sql_in_set('key_id', $marked);
								$db->sql_query($sql);

								//remove the history
								$sql = 'DELETE FROM ' . API_HISTORY_TABLE . " WHERE " . $db->sql_in_set('key_id', $marked);
								$db->sql_query($sql);

								//remove the logs
								$sql = 'DELETE FROM ' . API_LOG_TABLE . " WHERE " . $db->sql_in_set('key_id', $marked);
								$db->sql_query($sql);

								foreach ($marked AS $marked_)
								{
									add_log('admin', 'API_LOG_KEY_DELETED', $marked_);
									$message[] = $user->lang('ACP_PHPBB_API_DELETED', $marked_);
								}
								trigger_error(implode('<br />', $message) . adm_back_link(append_sid($this->u_action)), E_USER_NOTICE);
							}
							else
							{
								confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array_merge(array(
											'start'		=> $start,
											'mode'		=> $mode,
											'action'	=> $action
										), phpbb_api\functions\array_keys_stringify($marked, 'mark', true)
									))
								);
								//We cannot load the default case of this switch for now...
								redirect($this->u_action);
							}
						break;

						case 'create':
							add_form_key('api_create');
							$template->assign_var('S_KEYS_CREATE', true);
							if (!$update)
							{
								$troll_key = phpbb_api\functions\generate_api_key();
								$key_status = '';
								foreach ($user->lang['ACP_PHPBB_API_KEY_STATUS'] AS $value_ => $key_status_)
								{
									$key_status .= '<option value="' . $value_ . '">' . $key_status_ . '</option>';
								}
								$template->assign_block_vars('api_keys', array(
									'MAX_QUERIES_PER_DAY'	=> $config['api_mod_mqpd'],
									'MAX_QUERIES_PER_WEEK'	=> $config['api_mod_mqpw'],
									'MAX_QUERIES_PER_MONTH'	=> $config['api_mod_mqpm'],
									'MAX_QUERIES'			=> $config['api_mod_max_queries'],
									'KEY_ID'				=> $troll_key,
									'USERNAME'				=> $user->data['username'],
									'KEY_IPS_TYPE'			=> API_IP_ALLOWED,
									'KEY_IPS'				=> $user->ip,
									'KEY_TYPE'				=> API_TYPE_USER,
									'KEY_TIME'				=> date('m/d/Y h:i', time()),
									'KEY_STATUS'			=> $key_status,
									'U_FIND_USERNAME'		=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=searchuser&amp;form=api_keys&amp;field=key_id_user_' . $troll_key . '&amp;select_single=true'),
									'DEACTIVATED_METHODS' 	=> phpbb_api\functions\get_api_methods(true, true),
								));
							}
							else
							{
								if (!check_form_key('api_create'))
								{
									trigger_error('FORM_INVALID', E_USER_WARNING);
								}

								$message = array();
								//Request_var does not support array > 1 level recursion ...
								$key_id_ = trim(key($_POST['key_id']));
								$key_values = $_POST['key_id'][$key_id_];
								if ($key_id_ && sizeof($key_values))
								{
									//Init $sql_ary
									$sql_ary = array(
										'KEY_ID' => $key_id_
									);
									if (!isset($key_values['lifetime']) && !empty($key_values['expire_time']))
									{
										$strptime = phpbb_api\functions\strptime($key_values['expire_time'], '%m/%d/%Y %H:%M');
										//St***d admins can try to generate a notice as submitting bad format... @ save the world of them...
										$timestamp = @mktime($strptime['tm_hour'], $strptime['tm_min'], 0, $strptime['tm_mon']+1, $strptime['tm_mday'], $strptime['tm_year']+1900);
										if (strlen($timestamp) == 10)
										{
											if ($timestamp < time())
											{
												$message[] = $user->lang('ACP_PHPBB_API_KEY_OUTDATED', $key_id_);
											}
											$sql_ary['expire_time'] = $timestamp;
										}
										else
										{
											$timestamp = time();
											$message[] = $user->lang('ACP_PHPBB_API_KEY_INVALID_TIME', $key_id_);
										}
									}
									else
									{
										$sql_ary['expire_time'] = 0;
									}
									$sql_ary['creation_time']			= (int) time();
									$sql_ary['max_queries_per_day']		= (int) (isset($key_values['max_queries_per_day']) ? $key_values['max_queries_per_day'] : 0);
									$sql_ary['max_queries_per_week']	= (int) (isset($key_values['max_queries_per_week']) ? $key_values['max_queries_per_week'] : 0);
									$sql_ary['max_queries_per_month']	= (int) (isset($key_values['max_queries_per_month']) ? $key_values['max_queries_per_month'] : 0);
									$sql_ary['max_queries']				= (int) (isset($key_values['max_queries']) ? $key_values['max_queries'] : 0);
									$sql_ary['query_sql']				= (int) (isset($key_values['query_sql']) ? $key_values['query_sql'] : 0);
									$sql_ary['query_sql_api']			= (int) (isset($key_values['query_sql_api']) ? $key_values['query_sql_api'] : 0);
									$sql_ary['email_auth']				= (int) (isset($key_values['email_auth']) ? $key_values['email_auth'] : 0);
									$sql_ary['key_status']				= (int) (isset($key_values['key_status']) ? $key_values['key_status'] : API_STATUS_ACTIVE);
									$sql_ary['force_post']				= (int) (isset($key_values['force_post']) ? $key_values['force_post'] : 0);
									$sql_ary['key_type']				= (int) (isset($key_values['key_type']) ? $key_values['key_type'] : API_TYPE_USER);
									$sql_ary['key_ips_type']			= (int) (isset($key_values['key_ips_type']) ? $key_values['key_ips_type'] : API_IP_ALLOWED);
									$sql_ary['key_ips']					= (isset($key_values['key_ips']) ? $key_values['key_ips'] : '');
									$sql_ary['gen_source']				= API_GEN_SOURCE_ACP;
									$sql_ary['deactivated_methods']		= (isset($key_values['deactivated_methods']) ? (is_array($key_values['deactivated_methods']) ? implode(',', $key_values['deactivated_methods']) : '') : '');
									$sql_ary['key_secret_key']			= phpbb_api\functions\generate_api_secret_key();
									//For now, $key_values['user_id'] isn't really an user_id but an username (not clean)
									if (isset($key_values['user_id']))
									{
										$sql = 'SELECT user_id
											FROM ' . USERS_TABLE . '
											WHERE username = \'' . $db->sql_escape($key_values['user_id']) . '\'';
										$result = $db->sql_query_limit($sql, 1);
										$sql_ary['user_id'] = (int) $db->sql_fetchfield('user_id');
										$db->sql_freeresult($result);
										if (!$sql_ary['user_id'])
										{
											//The username seem to be invalid, use YOUR user_id...
											$sql_ary['user_id'] = $user->data['user_id'];
											$message[] = $user->lang('ACP_PHPBB_API_KEY_INVALID_USERNAME', $key_id_);
										}
									}
									else
									{
										//The username seem to be empty, use YOUR user_id...
										$sql_ary['user_id'] = $user->data['user_id'];
									}
									if ($sql_ary['key_type'] == API_TYPE_ADMIN)
									{
										$sql_ary['email_auth'] = 1;
									}
									else
									{
										$sql_ary['query_sql'] = $sql_ary['query_sql_api'] = 0;
									}
									$sql = 'INSERT INTO ' . API_KEYS_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
									$db->sql_query($sql);
									add_log('admin', 'API_LOG_KEY_CREATED', $key_id_);
								}
								$message[] = $user->lang('ACP_PHPBB_API_CREATED', $key_id_);
								trigger_error(implode('<br />', $message) . adm_back_link(append_sid($this->u_action)), E_USER_NOTICE);
							}
						break;

						case 'edit':
								add_form_key('api_edit');
								$template->assign_var('S_KEYS_EDIT', true);
								if ($key_id && !is_array($key_id))
								{
									$marked = array($key_id);
								}
								else if ($key_id && is_array($key_id))
								{
									$marked = $key_id;
								}
								if (empty($marked))
								{
									redirect($this->u_action);
								}
								if($secret_key_reset)
								{
									$key_id = (is_array($key_id)) ? current($key_id) : $key_id;
									if (confirm_box(true))
									{
										$sql = 'UPDATE ' . API_KEYS_TABLE . '
											SET ' . $db->sql_build_array('UPDATE', array('key_secret_key' => phpbb_api\functions\generate_api_secret_key())) . '
											WHERE key_id = \'' . $db->sql_escape($key_id) . '\'';
										$db->sql_query($sql);

										$message = $user->lang('ACP_PHPBB_API_KEY_SECRET_UPDATED', $key_id);
										add_log('admin', 'API_LOG_KEY_REINITIALIZED', $key_id);

										trigger_error($message . adm_back_link(append_sid($this->u_action)), E_USER_NOTICE);
									}
									else
									{
										confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
											'secret_key_reset'	=> true,
											'key_id[]'			=> $key_id,
											'mode'				=> $mode,
											'action'			=> $action
											))
										);
									}
								}
								if ($update)
								{
									if (!check_form_key('api_edit'))
									{
										trigger_error('FORM_INVALID', E_USER_WARNING);
									}

									$message = array();
									//Thanks request_var()...But you don't support array > 1 level recursion ...
									$key_id = $_POST['key_id'];
									foreach ($key_id AS $key_id_ => $key_values)
									{
										//Init $sql_ary
										$sql_ary = array();
										if (!isset($key_values['lifetime']) && !empty($key_values['expire_time']))
										{
											$strptime = phpbb_api\functions\strptime($key_values['expire_time'], '%m/%d/%Y %H:%M');
											//Stupid admins can try to generate a notice as submitting bad format... @ save the world of them...
											$timestamp = @mktime($strptime['tm_hour'], $strptime['tm_min'], 0, $strptime['tm_mon']+1, $strptime['tm_mday'], $strptime['tm_year']+1900);
											if (strlen($timestamp) == 10)
											{
												if ($timestamp < time())
												{
													$message[] = $user->lang('ACP_PHPBB_API_KEY_OUTDATED', $key_id_);
												}
												$sql_ary['expire_time'] = $timestamp;
											}
											else
											{
												$timestamp = time();
												$message[] = $user->lang('ACP_PHPBB_API_KEY_INVALID_TIME', $key_id_);
											}
										}
										else
										{
											$sql_ary['expire_time'] = 0;
										}
										if (isset( $key_values['reset'] ))
										{
											$sql_ary['queries'] = 0;
											$sql = 'DELETE FROM ' . API_HISTORY_TABLE . "
												WHERE key_id = '" . $db->sql_escape($key_id_) . "'";
											$db->sql_query($sql);
										}
										$sql_ary['max_queries_per_day']		= (int) (isset($key_values['max_queries_per_day']) ? $key_values['max_queries_per_day'] : 0);
										$sql_ary['max_queries_per_week']	= (int) (isset($key_values['max_queries_per_week']) ? $key_values['max_queries_per_week'] : 0);
										$sql_ary['max_queries_per_month']	= (int) (isset($key_values['max_queries_per_month']) ? $key_values['max_queries_per_month'] : 0);
										$sql_ary['max_queries']				= (int) (isset($key_values['max_queries']) ? $key_values['max_queries'] : 0);
										$sql_ary['query_sql']				= (int) (isset($key_values['query_sql']) ? $key_values['query_sql'] : 0);
										$sql_ary['query_sql_api']			= (int) (isset($key_values['query_sql_api']) ? $key_values['query_sql_api'] : 0);
										$sql_ary['email_auth']				= (int) (isset($key_values['email_auth']) ? $key_values['email_auth'] : 0);
										$sql_ary['key_status']				= (int) (isset($key_values['key_status']) ? $key_values['key_status'] : API_STATUS_ACTIVE);
										$sql_ary['force_post']				= (int) (isset($key_values['force_post']) ? $key_values['force_post'] : 0);
										$sql_ary['key_type']				= (int) (isset($key_values['key_type']) ? $key_values['key_type'] : API_TYPE_USER);
										$sql_ary['key_ips_type']			= (int) (isset($key_values['key_ips_type']) ? $key_values['key_ips_type'] : API_IP_ALLOWED);
										$sql_ary['key_ips']					= (isset($key_values['key_ips']) ? $key_values['key_ips'] : '');
										$sql_ary['deactivated_methods']		= (isset($key_values['deactivated_methods']) ? (is_array($key_values['deactivated_methods']) ? implode(',', $key_values['deactivated_methods']) : '') : '');
										//$sql_ary['gen_source']				= API_GEN_SOURCE_ACP; //If we edit the key, we do not modify that value.
										//For now, $key_values['user_id'] isn't really an user_id but an username (not clean)
										if (isset($key_values['user_id']))
										{
											$sql = 'SELECT user_id
												FROM ' . USERS_TABLE . '
												WHERE username = \'' . $db->sql_escape($key_values['user_id']) . '\'';
											$result = $db->sql_query_limit($sql, 1);
											$sql_ary['user_id'] = (int) $db->sql_fetchfield('user_id');
											$db->sql_freeresult($result);
											if (!$sql_ary['user_id'])
											{
												//The username seem to be invalid, do not update it...
												unset($sql_ary['user_id']);
												$message[] = $user->lang('ACP_PHPBB_API_KEY_INVALID_USERNAME', $key_id_);
											}
										}
										if ($sql_ary['key_type'] == API_TYPE_ADMIN)
										{
											$sql_ary['email_auth'] = 1;
										}
										else
										{
											$sql_ary['query_sql'] = $sql_ary['query_sql_api'] = 0;
										}
										if(!empty($sql_ary['user_id']))
										{
											$sql = 'SELECT user_id
												FROM ' . API_KEYS_TABLE . '
												WHERE key_id =\'' . $db->sql_escape($key_id_) . '\'';
											$result = $db->sql_query_limit($sql, 1);
											$krow = $db->sql_fetchrow($result);
											$db->sql_freeresult($result);

											//The username has changed, reinitialize the secret key
											if($krow['user_id'] != $sql_ary['user_id'])
											{
												$sql_ary['key_secret_key'] = phpbb_api\functions\generate_api_secret_key();
											}
										}
										$sql = 'UPDATE ' . API_KEYS_TABLE . '
											SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
											WHERE key_id = \'' . $db->sql_escape($key_id_) . '\'';
										$db->sql_query($sql);

										$message[] = $user->lang('ACP_PHPBB_API_KEY_UPDATED', $key_id_);
										add_log('admin', 'API_LOG_KEY_UPDATED', $key_id_);
									}
									trigger_error(implode('<br />', $message) . adm_back_link(append_sid($this->u_action)), E_USER_NOTICE);
								}
								else
								{
									$sql = 'SELECT a.*, u.username, u.user_colour
										FROM ' . API_KEYS_TABLE . ' a
										LEFT JOIN ' . USERS_TABLE . ' u
											ON (u.user_id = a.user_id)
										WHERE ' . $db->sql_in_set('key_id', $marked, false, true);
									$result = $db->sql_query_limit($sql, $config['api_mod_acp_pagination']);
									while ($row = $db->sql_fetchrow($result))
									{
										$key_status = '';
										foreach ($user->lang['ACP_PHPBB_API_KEY_STATUS'] AS $value_ => $key_status_)
										{
											$key_status .= '<option value="' . $value_ . '"' .(($row['key_status'] == $value_) ? ' selected="selected"' : '') . '>' . $key_status_ . '</option>';
										}
										$row['key_status'] = $key_status;
										$row['key_ips'] = $row['key_ips'];
										$row['creation_time'] = $user->format_date($row['creation_time']);
										$row['key_time'] = $row['expire_time'] ? date('m/d/Y h:i', $row['expire_time']) : 0;
										$row['u_action_secret_key_reset'] = append_sid($this->u_action, 'action=edit&amp;secret_key_reset=true&amp;key_id[]=' . $row['key_id']);
										$row['u_view_history'] = append_sid($this->u_action, 'action=history&amp;key_id[]=' . $row['key_id']);
										$row['u_view_detailed_history'] = append_sid("{$phpbb_admin_path}index.$phpEx", 'i=phpbb_api&amp;&amp;mode=stats&amp;action=all&amp;key_id=' . $row['key_id']);
										$row['u_find_username'] = append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=searchuser&amp;form=api_keys&amp;field=key_id_user_' . $row['key_id'] . '&amp;select_single=true');
										$row['username_full'] = get_username_string('full', $row['user_id'], $row['username'], $row['user_colour'], false, $profile_url);
										$row['expire_time'] = $row['expire_time'] ? $user->format_date($row['expire_time']) : 0;
										$row['deactivated_methods'] = phpbb_api\functions\get_api_methods(true, true, explode(',', $row['deactivated_methods']));
										$template->assign_block_vars('api_keys', array_change_key_case($row, CASE_UPPER));
									}
									$db->sql_freeresult($result);
								}
						break;

						case'history';
							if ($key_id && !is_array($key_id))
							{
								$marked = array($key_id);
							}
							else if ($key_id && is_array($key_id))
							{
								$marked = $key_id;
							}
							$history = array();
							$sql = 'SELECT time
								FROM ' . API_HISTORY_TABLE .  '
								WHERE ' . $db->sql_in_set('key_id', $marked, false, true);
							$result = $db->sql_query_limit($sql, 50000);
							while ($row = $db->sql_fetchrow($result))
							{
								$history[] = $row['time'];
							}
							asort($history);
							$db->sql_freeresult($result);
							if (!empty($history))
							{
								$template->assign_var('S_KEYS_HISTORY', sizeof($history));
								foreach ($history AS $history_)
								{
									$history_ = (int) trim($history_);
									if (strlen($history_) == 10)
									{
										$template->assign_block_vars('history', array(
											'ROW'	=> $user->format_date($history_),
										));
									}
								}
							}
							else
							{
								trigger_error($user->lang['API_NO_RECORD'], E_USER_WARNING);
							}
						break;

						default:
							$from_sql = 'FROM ' . API_KEYS_TABLE . ' a';
							$join_sql = 'LEFT JOIN ' . USERS_TABLE . ' u ON (u.user_id = a.user_id)';
							$where_sql = $uri_params = '';
							if (($search || $search_sorting) && $search_type && $search_field)
							{
								if ($search_type == 'key_id')
								{
									$from_sql = 'FROM ' . API_KEYS_TABLE . ' a';
									$join_sql = 'LEFT JOIN ' . USERS_TABLE . ' u ON (u.user_id = a.user_id)';
									$where_sql =  "WHERE key_id = '" .  $db->sql_escape($search_field) . "'";
									$uri_params = 'search_type=' . $search_type . '&amp;search_field=' . $search_field . '&amp;search_sorting=true';
									$template->assign_vars(array(
										'S_SEARCH_TYPE'	=> $search_type,
										'S_SEARCH_FIELD'	=> $search_field,
									));
								}
								else if ($search_type == 'username')
								{
									$from_sql = 'FROM ' . USERS_TABLE . ' u';
									$join_sql = 'LEFT JOIN ' . API_KEYS_TABLE . " a ON (a.user_id = u.user_id)";
									$where_sql = "WHERE u.username = '" .  $db->sql_escape($search_field) . "'";
									$uri_params = 'search_type=' . $search_type . '&amp;search_field=' . $search_field . '&amp;search_sorting=true';
									$template->assign_vars(array(
										'S_SEARCH_TYPE'	=> $search_type,
										'S_SEARCH_FIELD'	=> $search_field,
									));
								}
							}
							$sql = "SELECT a.*, u.username, u.user_colour
								$from_sql
								$join_sql $where_sql";
							$result = $db->sql_query_limit($sql, $config['api_mod_acp_pagination'], $start);
							while ($row = $db->sql_fetchrow($result))
							{
								$row['is_outdated'] = (($row['expire_time'] < time() && $row['expire_time'] > 0) ? true : false);
								$row['is_out_of_quota'] = ($row['max_queries'] && $row['max_queries'] <= $row['queries']) ? true : false;
								$row['expire_time'] = $row['expire_time'] ? $user->format_date($row['expire_time']) : $user->lang['ACP_PHPBB_API_LIFETIME'];
								$row['username_full'] = get_username_string('full', $row['user_id'], $row['username'], $row['user_colour'], false, $profile_url);
								$row['acp_username'] = append_sid("{$phpbb_admin_path}index.$phpEx", 'i=users&amp;mode=overview&amp;username=' . $row['username']);
								$row['U_EDIT'] = append_sid($this->u_action, 'action=edit&amp;key_id[]=' . $row['key_id']);
								$row['U_DELETE'] = append_sid($this->u_action, 'action=delete&amp;key_id[]=' . $row['key_id']);
								$template->assign_block_vars('api_keys', array_change_key_case($row, CASE_UPPER));
							}
							$db->sql_freeresult($result);
							$sql = "SELECT COUNT(a.key_id) AS key_id
								$from_sql
								$join_sql $where_sql";
							$result = $db->sql_query($sql);
							$total_keys = $db->sql_fetchfield('key_id');
							$db->sql_freeresult($result);
							$template->assign_vars(array(
								//pagination
								'S_ON_PAGE'				=> ($total_keys > $config['api_mod_acp_pagination']) ? true : false,
								'TOTAL_MESSAGES'		=> sprintf($user->lang['ACP_PHPBB_API_PAGINATION_KEY' .(($total_keys > 1) ? 'S' : '')], $total_keys),
								'PAGE_NUMBER' 			=> on_page($total_keys, $config['api_mod_acp_pagination'], $start),
								'PAGINATION' 			=> generate_pagination(append_sid($this->u_action, $uri_params), $total_keys, $config['api_mod_acp_pagination'], $start),
							));
						break;
					}
					$template->assign_vars(array(
						'S_VERSION'				=> isset($config['api_mod_version']) ? $config['api_mod_version'] : '',

						//Basics vars
						'S_CONFIG'				=> false,
						'S_KEYS_MANAGE'			=> true,
						'U_ACTION'				=> append_sid( $this->u_action, ($action ? 'action=' . $action : '') . ($start ? 'start=' . $start : '')),
						'U_ACTION_CREATE'		=> append_sid($this->u_action, 'action=create'),
						'TITLE'					=> $this->page_title,
						'TITLE_EXPLAIN'			=> $user->lang['API_ERRORS_CONFIG_EXPLAIN'],
						'TITLE_IMG'				=> $phpbb_root_path . 'images/api_' . $mode . '.png',

						//Mods vars
						'ERRORS_VERSION'		=> sprintf($user->lang['API_ERRORS_VERSION_COPY'], $announcement_url, $config['api_mod_version']),
						'S_NO_VERSION'			=> $latest_version ? false : true,
						'LATEST_VERSION'		=> $latest_version ? $latest_version : $user->lang['ERRORS_NO_VERSION'],
						'CURRENT_VERSION'		=> $config['api_mod_version'],
						'API_MOD_ENABLE'		=> isset($config['api_mod_enable'])		? (((bool)	$config['api_mod_enable']	== 1) ? true : false) : '',
						'API_MOD_MAX_OLD'		=> isset($config['api_mod_max_old'])		? (int)		$config['api_mod_max_old'] : '',

					));
			break;

			case 'logs' :
			case 'err_logs' :
				$user->add_lang(array('mcp', 'search'));
				if ($mode == 'err_logs')
				{
					$err_logs = true;
					$err_logs_path =
					$this->tpl_name = 'api/acp_phpbb_api_logs';
					$template->assign_vars(array(
						'S_HARD_LOG' => true,
						'S_HARD_LOG_DOWNLOAD' => append_sid($this->u_action, array('action' => 'download_err_logs')),
						'S_HARD_LOG_PURGE' => append_sid($this->u_action, array('action' => 'purge_err_logs')),
						'S_HARD_LOG_SIZE' => $user->lang('ACP_PHPBB_API_ERR_LOGS_HARD', get_formatted_filesize(filesize(API_ERR_LOG_FILE))),
					));
				}
				// Set up general vars
				$action		= request_var('action', '');
				$start		= request_var('start', 0);
				$results	= request_var('results', $config['api_mod_acp_pagination']);
				$deletemark = (!empty($_POST['delmarked'])) ? true : false;
				$submitadv		= (!empty($_POST['submitadv'])) ? true : false;
				$deleteall	= (!empty($_POST['delall'])) ? true : false;
				$marked		= request_var('mark', array(0));
				$marked_cnt = sizeof($marked);
				// Sort keys
				$sort_days	= request_var('st', 0);
				$sort_key	= request_var('sk', 't');
				$sort_dir	= request_var('sd', 'd');

				$this->log_type = LOG_API;
				(int) $config['api_mod_acp_pagination'] = &$results;
				if ($mode == 'err_logs' && $action == 'download_err_logs')
				{
					header('Content-Description: File Transfer');
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename=' . basename(API_ERR_LOG_FILE));
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate');
					header('Pragma: public');
					header('Content-Length: ' . filesize(API_ERR_LOG_FILE));
					@ob_clean();
					readfile(API_ERR_LOG_FILE);
					garbage_collection();
					exit_handler();
				}
				else if ($mode == 'err_logs' && $action == 'purge_err_logs')
				{
					if (confirm_box(true))
					{
						if (is_writable(API_ERR_LOG_FILE))
						{
							$handle = fopen(API_ERR_LOG_FILE, "w+b");
							//Lock file during truncating
							if (flock($handle, LOCK_EX))
							{
								ftruncate($handle, 0);
								fflush($handle);
								flock($handle, LOCK_UN);
							}
							else
							{
								fclose($handle);
								trigger_error('ACP_PHPBB_API_ERR_LOGS_PURGE_ERROR', E_USER_ERROR);
							}
							add_log('admin', 'API_LOG_ERROR_CLEARED');
							redirect(append_sid($this->u_action));
						}
						else
						{
							trigger_error('ACP_PHPBB_API_ERR_LOGS_PURGE_ERROR', E_USER_ERROR);
						}
					}
					else
					{
						confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
							'mode'		=> $mode,
							'action'	=> $action))
						);
					}
				}
				// Delete entries if requested and able
				if (($deletemark || $deleteall) && $auth->acl_get('a_clearlogs'))
				{
					if (confirm_box(true))
					{
						$where_sql = '';
						if ($deletemark && $marked_cnt)
						{
							$sql_in = array();
							foreach ($marked AS $mark)
							{
								$sql_in[] = $mark;
							}
							$where_sql .= ' AND ' . $db->sql_in_set('log_id', $sql_in);
							unset($sql_in);
						}
						$where_sql .= ' AND ' . $db->sql_in_set('log_operation', explode(',', LOG_API_ERROR_OPERATIONS), $mode == 'logs' ? true : false);

						if ($where_sql || $deleteall)
						{
							$sql = 'DELETE FROM ' . API_LOG_TABLE . "
								WHERE log_type = {$this->log_type}
								$where_sql";
							$db->sql_query($sql);
							if ($marked_cnt)
							{
								add_log('admin', 'API_LOG' . (($marked_cnt > 1) ? 'S' : '') . '_CLEAR', $marked_cnt);
							}
							else
							{
								add_log('admin', 'API_LOG_ERROR_CLEARED');
							}
						}
					}
					else
					{
						confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
							'start'		=> $start,
							'delmarked'	=> $deletemark,
							'delall'	=> $deleteall,
							'mark'		=> $marked,
							'st'		=> $sort_days,
							'sk'		=> $sort_key,
							'sd'		=> $sort_dir,
							'i'			=> $id,
							'mode'		=> $mode,
							'action'	=> $action))
						);
					}
				}

				// Sorting
				$limit_days = array(0 => $user->lang['ALL_ENTRIES'], 1 => $user->lang['1_DAY'], 7 => $user->lang['7_DAYS'], 14 => $user->lang['2_WEEKS'], 30 => $user->lang['1_MONTH'], 90 => $user->lang['3_MONTHS'], 180 => $user->lang['6_MONTHS'], 365 => $user->lang['1_YEAR']);
				$sort_by_text = array('u' => $user->lang['SORT_USERNAME'], 't' => $user->lang['SORT_DATE'], 'i' => $user->lang['SORT_IP'], 'o' => $user->lang['SORT_ACTION']);
				$sort_by_sql = array('u' => 'u.username_clean', 't' => 'l.log_time', 'i' => 'l.log_ip', 'o' => 'l.log_operation');

				$s_limit_days = $s_sort_key = $s_sort_dir = $u_sort_param = $results_param = $where_sql = '';
				gen_sort_selects($limit_days, $sort_by_text, $sort_days, $sort_key, $sort_dir, $s_limit_days, $s_sort_key, $s_sort_dir, $u_sort_param);

				// Define where and sort sql for use in displaying logs
				$sql_where = ($sort_days) ? (time() - ($sort_days * API_DAY_SECONDS)) : 0;
				$sql_sort = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');

				$keywords = utf8_normalize_nfc(request_var('keywords', '', true));
				$log_username = utf8_normalize_nfc(request_var('log_username', '', true));
				$log_key_id = utf8_normalize_nfc(request_var('log_key_id', '', true));
				$log_date_from = utf8_normalize_nfc(request_var('log_date_from', '', true));
				$log_date_to = utf8_normalize_nfc(request_var('log_date_to', '', true));
				$username_param = $key_id_param = $date_from_param = $date_to_param = '';
				$keywords_param = !empty($keywords) ? '&amp;keywords=' . urlencode(htmlspecialchars_decode($keywords)) : '';
				$results_param = !empty($results) ? '&amp;results=' . $config['api_mod_acp_pagination'] : '';
				if ($log_username)
				{
					$username_param =  '&amp;log_username=' . urlencode(htmlspecialchars_decode($log_username));
					$template->assign_var('S_LOG_USERNAME', htmlspecialchars($log_username));
					$where_sql .= ' AND u.username ="' . $db->sql_escape($log_username) . '"';
				}
				if ($log_key_id)
				{
					$key_id_param =  '&amp;log_key_id=' . urlencode(htmlspecialchars_decode($log_key_id));
					$template->assign_var('S_LOG_KEY_ID', htmlspecialchars($log_key_id));
					$where_sql .= ' AND l.key_id ="' . $db->sql_escape($log_key_id) . '"';
				}
				if ($log_date_from && $log_date_to)
				{
					$date_from_param =  '&amp;log_date_from=' . urlencode(htmlspecialchars_decode($log_date_from));
					$date_to_param =  '&amp;log_date_to=' . urlencode(htmlspecialchars_decode($log_date_to));
					$from_strptime = phpbb_api\functions\strptime($log_date_from, '%m/%d/%Y %H:%M');
					//Stupid admins can try to generate a notice as submitting bad format... @ save the world of them...
					$from_timestamp = @mktime($from_strptime['tm_hour'], $from_strptime['tm_min'], 0, $from_strptime['tm_mon']+1, $from_strptime['tm_mday'], $from_strptime['tm_year']+1900);
					$to_strptime = phpbb_api\functions\strptime($log_date_to, '%m/%d/%Y %H:%M');
					//Stupid admins can try to generate a notice as submitting bad format... @ save the world of them...
					$to_timestamp = @mktime($to_strptime['tm_hour'], $to_strptime['tm_min'], 0, $to_strptime['tm_mon']+1, $to_strptime['tm_mday'], $to_strptime['tm_year']+1900);
					$where_sql .= ' AND l.log_time BETWEEN ' . (int) $from_timestamp . ' AND ' . (int) $to_timestamp;
					$template->assign_vars(array(
						'S_LOG_DATE_FROM'	=> $log_date_from,
						'S_LOG_DATE_TO'		=> $log_date_to,
					));
				}

				$l_title = $this->page_title;
				$l_title_explain = $user->lang['ACP_PHPBB_API_LOGS_EXPLAIN'];

				// Grab log data
				$log_data = array();
				$log_count = 0;
				$s_results = '';
				$start = phpbb_api\functions\api_view_log('api', $log_data, $log_count, $config['api_mod_acp_pagination'], $start, $sql_where, $sql_sort, $keywords, $where_sql, (empty($err_logs)) ? true : false);
				if ($log_username)
				{
					$sql = 'SELECT user_id
						FROM ' . USERS_TABLE .  '
						WHERE username = "' . $db->sql_escape($log_username) .  '"';
					$result = $db->sql_query($sql);
					$user_id = (int) $db->sql_fetchfield('user_id');
					$db->sql_freeresult($result);

					$s_user_keys = '<option' . (($log_key_id) ? '' : ' selected="selected"') . ' value="-1">'. $user->lang['ACP_PHPBB_API_KEY_SELECT'] . "</option>\n";
					$sql = 'SELECT k.key_id, l.key_id AS log_key_id, COUNT(l.key_id) AS total_logs
						FROM ' . API_KEYS_TABLE .  ' k
						LEFT JOIN ' . API_LOG_TABLE .  ' l
							ON (l.key_id = k.key_id AND l.user_id = ' . (int) $user_id . ')
						WHERE k.user_id = ' . (int) $user_id . '
						GROUP by k.key_id';
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result))
					{
						$s_user_keys .= '<option' . (($log_key_id == $row['key_id']) ? ' selected="selected"' : '') . ' value="' . $row['key_id']  . '">' . $row['key_id'] . ' (' . $row['total_logs'] . ")</option>\n";
					}
					$db->sql_freeresult($result);
					$template->assign_var('S_USER_KEYS'	, $s_user_keys);
				}
				foreach (array_unique(phpbb_api\functions\ssort(array_merge(array($results), explode(',', API_LOG_RESULTS_COUNT)))) AS $results_)
				{
					$s_results .= '<option value="' . $results_ . '"' . (($results == $results_) ? 'selected="selected"' : '' ) . '>' . $results_ . '</option>';
				}
				$template->assign_vars(array(
					'L_TITLE'		=> $l_title,
					'L_EXPLAIN'		=> $l_title_explain,
					'U_ACTION'		=> append_sid($this->u_action, "$u_sort_param$keywords_param$username_param$key_id_param$date_from_param$date_to_param&amp;start=$start"),
					'U_FIND_USERNAME'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=searchuser&amp;form=list&amp;field=log_username&amp;select_single=true'),
					'S_ON_PAGE'		=> on_page($log_count, $config['api_mod_acp_pagination'], $start),
					'TOTAL'			=> ($log_count == 1) ? $user->lang['TOTAL_LOG'] : sprintf($user->lang['TOTAL_LOGS'], $log_count),
					'PAGINATION'	=> generate_pagination(append_sid($this->u_action, "$u_sort_param$keywords_param$username_param$key_id_param$date_from_param$date_to_param$results_param"), $log_count, $config['api_mod_acp_pagination'], $start, true),

					'S_LOG_RESULTS'	=> ($results && $submitadv) ? true : false,
					'S_RESULTS'		=> $s_results,
					'S_LIMIT_DAYS'	=> $s_limit_days,
					'S_SORT_KEY'	=> $s_sort_key,
					'S_SORT_DIR'	=> $s_sort_dir,
					'S_CLEARLOGS'	=> $auth->acl_get('a_clearlogs'),
					'S_KEYWORDS'	=> $keywords,
					)
				);
				$template->assign_vars(array(
					//pagination
					'S_VERSION'				=> isset($config['api_mod_version']) ? $config['api_mod_version'] : '',
					//Basics vars
					'U_ACTION_CREATE'		=> append_sid($this->u_action, 'action=create'),
					'TITLE'					=> $this->page_title,
					'TITLE_EXPLAIN'			=> $user->lang['API_ERRORS_CONFIG_EXPLAIN'],
					'TITLE_IMG'				=> $phpbb_root_path . 'images/api_' . $mode . '.png',

					//Mods vars
					'ERRORS_VERSION'		=> sprintf($user->lang['API_ERRORS_VERSION_COPY'], $announcement_url, $config['api_mod_version']),

				));
				foreach ($log_data as $row)
				{
/* 					$data = array();

					$checks = array('viewtopic', 'viewlogs', 'viewforum');
					foreach ($checks as $check)
					{
						if (isset($row[$check]) && $row[$check])
						{
							$data[] = '<a href="' . $row[$check] . '">' . $user->lang['LOGVIEW_' . strtoupper($check)] . '</a>';
						}
					} */

					$template->assign_block_vars('log', array(
						'USERNAME'			=> $row['username_full'],
						'KEY_ID'			=> $row['key_id'],
						'REPORTEE_USERNAME'	=> ($row['reportee_username'] && $row['user_id'] != $row['reportee_id']) ? $row['reportee_username_full'] : '',

						'IP'				=> $row['ip'],
						'DATE'				=> $user->format_date($row['time']),
						'ACTION'			=> $row['action'],
/* 						'DATA'				=> (sizeof($data)) ? implode(' | ', $data) : '', */
						'ID'				=> $row['id'],
						)
					);
				}
			break;

			case'hooks';
				$action = request_var('action', '');
				$action_file = phpbb_api\functions\cleanup_filename(request_var('file', ''));
				$submithook = request_var('submithook', '');

				if (!class_exists('api_cache'))
				{
					include(API_ROOT_PATH . 'cache.' . $phpEx);
				}
				$api_cache		= new phpbb_api\api_cache();//API's cache

				if ($submithook && !empty($_FILES))
				{
					$action = 'upload';
				}
				switch( $action)
				{
					case'delete':
						if (file_exists(API_ROOT_PATH . 'hooks/' . $action_file .'.' . $phpEx))
						{
							trigger_error($user->lang['ACP_PHPBB_API_HOOK_DELETE_ERR'] . adm_back_link(append_sid($this->u_action)), E_USER_WARNING);
						}
						foreach ($api_cache->obtain_api_hooks(true) AS $hooks)
						{
							$hook_file = $hook = '';
							$dh = @opendir($hooks . '/root/includes/api/hooks');
							if ($dh)
							{
								while (($file = readdir($dh)) !== false)
								{
									$hook = substr($file, 0, -(strlen($phpEx) + 1));
									if (substr($file, -(strlen($phpEx) + 1)) === '.' . $phpEx && $hook == $action_file)
									{
										$hook_file = $hooks . '/root/includes/api/hooks/' . substr($file, 0, -(strlen($phpEx) + 1)) .'.' . $phpEx;
										if (file_exists($hook_file))
										{
											//Check if file exist, is writable and there is not an exploit include attempt.
											if (is_writable($hook_file) && strpos($file, '..') === false)
											{
												if (confirm_box(true))
												{
													$this->directory_delete($hooks);
													if ( file_exists($hooks . '.zip'))
													{
														unlink($hooks . '.zip');
													}
													trigger_error($user->lang('ACP_PHPBB_API_HOOK_DELETED', $file) . adm_back_link($this->u_action), E_USER_NOTICE);
												}
												else
												{
													confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
														'action'	=> $action,
														'file'		=> $action_file,
													)));
												}
											}
										}
									}
								}
								closedir($dh);
							}
						}
					break;

					case 'install':
						$lang_dir = array();
						$result = $db->sql_query('SELECT * FROM ' . LANG_TABLE);
						while ($row = $db->sql_fetchrow($result))
						{
							$lang_dir[] = $row['lang_dir'];
						}
						$db->sql_freeresult($result);
						foreach ($api_cache->obtain_api_hooks(true) AS $hooks)
						{
							$hook_file = $hook = '';
							$dh = @opendir($hooks . '/root/includes/api/hooks');
							if ($dh)
							{
								while (($file = readdir($dh)) !== false)
								{
									$hook = substr($file, 0, -(strlen($phpEx) + 1));
									if (substr($file, -(strlen($phpEx) + 1)) === '.' . $phpEx && $hook == $action_file)
									{
										$hook_file = $hooks . '/root/includes/api/hooks/' . substr($file, 0, -(strlen($phpEx) + 1)) .'.' . $phpEx;
										if (file_exists($hook_file))
										{
											include($hook_file);
											//Check if file exist, is writable and there is not an exploit include attempt.
											if (is_writable($hook_file) && strpos($file, '..') === false)
											{
												if (confirm_box(true))
												{
													if (!empty($acp_api_hook_install) && is_object($acp_api_hook_install))
													{
														$acp_api_hook_install();
														$acp_api_hook_install = null;
													}
													if (!empty($add_hook_tpl))
													{
														copy($hooks . '/root/includes/api/store/' . $add_hook_tpl . '.' . API_HTML_EXT, API_ROOT_PATH . 'store/' . $add_hook_tpl . '.' . API_HTML_EXT);
														$add_hook_tpl = null;
													}
													copy($hook_file, API_ROOT_PATH . 'hooks/' . substr($file, 0, -(strlen($phpEx) + 1)) .'.' . $phpEx);
													$api_cache->destroy('_api_hooks');
													//Now install lang files.
													foreach ($lang_dir AS $lang_dir_)
													{
														if (file_exists($hooks . '/root/language/' . $lang_dir_ . '/mods/hooks/info_acp_' . substr($file, 0, -(strlen($phpEx) + 1)) . '.' . $phpEx) && is_dir($phpbb_root_path . 'language/' . $lang_dir_ . '/mods/hooks'))
														{
															copy($hooks . '/root/language/' . $lang_dir_ . '/mods/hooks/info_acp_' . substr($file, 0, -(strlen($phpEx) + 1)) . '.' . $phpEx, $phpbb_root_path . 'language/' . $lang_dir_ . '/mods/hooks/info_acp_' . substr($file, 0, -(strlen($phpEx) + 1)) . '.' . $phpEx);
														}
													}
													trigger_error($user->lang['ACP_PHPBB_API_OPERATION_SUCCESS'] . adm_back_link(append_sid($this->u_action)), E_USER_NOTICE);
												}
												else
												{
													confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
														'action'	=> $action,
														'file'		=> $action_file,
													)));
												}
											}
										}
									}
								}
								closedir($dh);
							}
						}
					break;

					case 'uninstall':
						$lang_dir = array();
						$result = $db->sql_query('SELECT * FROM ' . LANG_TABLE);
						while ($row = $db->sql_fetchrow($result))
						{
							$lang_dir[] = $row['lang_dir'];
						}
						$db->sql_freeresult($result);
						$hook_file = $hook = '';
						if (strpos($action_file, '..') === false)
						{
							if (file_exists(API_ROOT_PATH . 'hooks/' . $action_file . '.' . $phpEx))
							{
								include(API_ROOT_PATH . 'hooks/' . $action_file . '.' . $phpEx);
								if (!empty($acp_api_hook_manager))
								{
									$hook_file = API_ROOT_PATH . 'hooks/' . $action_file . '.' . $phpEx;
									if (is_writable($hook_file))
									{
										if (confirm_box(true))
										{
											if (!empty($acp_api_hook_uninstall) && is_object($acp_api_hook_uninstall))
											{
												$acp_api_hook_uninstall();
												$acp_api_hook_uninstall = null;
											}
											if (!empty($add_hook_tpl))
											{
												unlink(API_ROOT_PATH . 'store/' . $add_hook_tpl . '.' . API_HTML_EXT);
												$add_hook_tpl = null;
											}
											unlink($hook_file);
											$api_cache->destroy('_api_hooks');
											//Now uninstall lang files.
											foreach ($lang_dir AS $lang_dir_)
											{
												if (file_exists($phpbb_root_path . 'language/' . $lang_dir_ . '/mods/hooks/info_acp_' . $action_file . '.' . $phpEx))
												{
													if (is_writable($phpbb_root_path . 'language/' . $lang_dir_ . '/mods/hooks/info_acp_' . $action_file . '.' . $phpEx))
													{
														unlink($phpbb_root_path . 'language/' . $lang_dir_ . '/mods/hooks/info_acp_' . $action_file . '.' . $phpEx);
													}
												}
											}
											trigger_error($user->lang['ACP_PHPBB_API_OPERATION_SUCCESS'] . adm_back_link(append_sid($this->u_action)), E_USER_NOTICE);
										}
										else
										{
											confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
												'action'	=> $action,
												'file'		=> $action_file,
											)));
										}
									}
								}
							}
						}
					break;

					case'download':
						$zip_path = API_ROOT_PATH . 'store/'. $action_file . '.zip';
						if (file_exists($zip_path))
						{
							download_zipped_hook:

							header('Content-Description: File Transfer');
							header('Content-Type: application/octet-stream');
							header('Content-Disposition: attachment; filename=' . basename($zip_path));
							header('Content-Transfer-Encoding: binary');
							header('Expires: 0');
							header('Cache-Control: must-revalidate');
							header('Pragma: public');
							header('Content-Length: ' . filesize($zip_path));
							@ob_clean();
							readfile($zip_path);
							garbage_collection();
							exit_handler();
						}
						else
						{
							if (is_dir(API_ROOT_PATH . 'store/'. $action_file))
							{
								if (phpbb_api\functions\directory_to_zip(API_ROOT_PATH . 'store/' . $action_file, $zip_path) !== false)
								{
									goto download_zipped_hook;
								}
								else
								{
									trigger_error('ACP_PHPBB_API_INSTALL_NO_ZIP', E_USER_ERROR);
								}
							}
						}
					break;

					case'upload':
						$user->add_lang('posting');
						include($phpbb_root_path . 'includes/functions_upload.' . $phpEx);
						$allowed_patterns = array(
							API_ROOT_PATH,
							$phpbb_root_path . 'language/'
						);
						$upload = new fileupload();
						$hook_dir = '';
						$hooks_dir = API_ROOT_PATH . 'store';
						// Only allow ZIP files
						$upload->set_allowed_extensions(array('zip'));
						// Let's make sure the mods directory exists and if it doesn't then create it
						if (!is_dir($hooks_dir))
						{
							mkdir($hooks_dir, octdec('0755'), true);
						}
						$file = $upload->form_upload('hookupload');
						if (empty($file->filename))
						{
							$max_upload = (int)(ini_get('upload_max_filesize'));
							$max_post = (int)(ini_get('post_max_size'));
							$memory_limit = (int)(ini_get('memory_limit'));
							$upload_mb = min($max_upload, $max_post, $memory_limit);
							trigger_error($user->lang('ACP_PHPBB_API_HOOK_NO_UPLOAD', $upload_mb) . adm_back_link(append_sid($this->u_action)), E_USER_WARNING);
						}
						else
						{
							if (!$file->init_error && !sizeof($file->error))
							{
								$file->clean_filename('real');
								$file->move_file(str_replace($phpbb_root_path, '', $hooks_dir), true, true);

								if (!sizeof($file->error))
								{
									include($phpbb_root_path . 'includes/functions_compress.' . $phpEx);
									$hook_dir = $hooks_dir . '/' .  preg_replace("/([^0-9a-zA-Z_.])([0-9]+)?([^0-9a-zA-Z_.])?/","", str_replace('.zip', '', $file->uploadname));
									$compress = new compress_zip('r', $file->destination_file);
									$compress->extract($hook_dir . '_tmp/');
									$compress->close();
									$folder_contents = scandir($hook_dir . '_tmp/', 1);
									if (is_dir($hook_dir))
									{
										trigger_error($user->lang['ACP_PHPBB_API_HOOKS_INSTALLED_ERR'] . adm_back_link(append_sid($this->u_action)), E_USER_WARNING);
									}
									// We need to check if there's a main directory inside the temp HOOK directory
									if (sizeof($folder_contents) == 3)
									{
										// We need to move that directory then
										$this->directory_move($hook_dir . '_tmp/', $hook_dir, $allowed_patterns);
										$file->remove();
									}
									else if (!is_dir($hook_dir))
									{
										// Change the name of the directory by moving to directory without _tmp in it
										$this->directory_move($hook_dir . '_tmp/', $hook_dir, $allowed_patterns);
										$file->remove();
									}
									$this->directory_delete($hook_dir . '_tmp/');
								}
							}
							$file->remove();
							if ($file->init_error || sizeof($file->error))
							{
								trigger_error((sizeof($file->error) ? implode('<br />', $file->error) : $user->lang['ACP_PHPBB_API_HOOKS_UPLOAD_FAIL']) . adm_back_link($this->u_action), E_USER_WARNING);
							}
						}
					break;
				}
				if (!empty($purge_cache))
				{
					$api_cache->destroy('_api_hooks');
				}
				$checksums = get_remote_file('gl4.fr', '', 'api_checksum.txt', $errstr, $errno);
				if ($checksums !== false && empty($errstr))
				{
					$checksums = json_decode($checksums, true);
				}
				else
				{
					$template->assign_vars(array(
						'S_HOOK_ERROR'		=> true,
						'HOOK_ERROR_MSG'	=> sprintf($user->lang['API_UNABLE_CONNECT_HOOK'], $errstr),
					));
					//Server unavailable
				}
				$installed = array();
				foreach ($api_cache->obtain_api_hooks() as $hook)
				{
					if (file_exists(API_ROOT_PATH . 'hooks/' . $hook . '.' . $phpEx))
					{
						include(API_ROOT_PATH . 'hooks/' . $hook . '.' . $phpEx);
						if (!empty($acp_api_hook_manager))
						{
							if (!empty($add_hook_lang) && !isset($user->lang[$acp_api_hook_manager['name']]) && file_exists($phpbb_root_path . 'language/' . $user->lang_name . '/' . $add_hook_lang . '.' . $phpEx))
							{
								$user->add_lang($add_hook_lang);
								$add_hook_lang = null;
							}
							$official = false;
							$is_up_to_date = true;
							if (isset($checksums[$hook]))
							{
								if (in_array(sha1_file(API_ROOT_PATH . 'hooks/' . $hook . '.' . $phpEx), explode(',', $checksums[$hook]['sha1'])))
								{
									$official = true;
								}
								if (phpbb_version_compare($acp_api_hook_manager['version'], $checksums[$hook]['version'], '>='))
								{
									$is_up_to_date = true;
								}
								else
								{
									$is_up_to_date = false;
								}
							}
							else if (!empty($acp_api_hook_manager['vchecker']) && sizeof($acp_api_hook_manager['vchecker']) == 3)
							{
								$official = false;
								$vchecker = get_remote_file($acp_api_hook_manager['vchecker'][0], $acp_api_hook_manager['vchecker'][1], $acp_api_hook_manager['vchecker'][2], $errstr, $errno);
								if (phpbb_version_compare($acp_api_hook_manager['version'], $vchecker, '>='))
								{
									$is_up_to_date = true;
								}
								else
								{
									$is_up_to_date = false;
								}
								unset($acp_api_hook_manager['vchecker']);
							}

							$strptime = phpbb_api\functions\strptime($acp_api_hook_manager['date'][0], $acp_api_hook_manager['date'][1]);
							//Stupid admins can try to generate a notice as submitting bad format... @ save the world of them...
							$timestamp = @mktime($strptime['tm_hour'], $strptime['tm_min'], 0, $strptime['tm_mon']+1, $strptime['tm_mday'], $strptime['tm_year']+1900);
							$template->assign_block_vars('api_hooks', array(
								'AUTHOR'		=> $acp_api_hook_manager['author'],
								'WEBSITE'		=> isset($acp_api_hook_manager['website']) ? strip_tags($acp_api_hook_manager['website']) : false,
								'VERSION'		=> $acp_api_hook_manager['version'],
								'NAME'			=> isset($user->lang[$acp_api_hook_manager['name']]) ? $user->lang[$acp_api_hook_manager['name']] : $acp_api_hook_manager['name'],
								'DATE'			=> $user->format_date($timestamp),
								'FILE'			=> $hook . '.' . $phpEx,
								'OFFICIAL'		=> $official,
								'IS_UP_TO_DATE'	=> $is_up_to_date,
								'U_UNINSTALL'		=> append_sid($this->u_action, 'action=uninstall&amp;file=' . $hook),
								'U_DOWNLOAD'		=> append_sid($this->u_action, 'action=download&amp;file=' . $hook . '_' . $acp_api_hook_manager['version']),
								'U_LAST_UPDATE'	=> 'http://geolim4.com/hooks.html?h=' . $hook,//No need append_sid() here
								'SHA1'			=> sha1_file(API_ROOT_PATH . 'hooks/' . $hook . '.' . $phpEx),
								'INSTALLED'		=> true,
							));
							$installed[] = $hook;
							$acp_api_hook_manager = null;
						}
					}
				}
				foreach ($api_cache->obtain_api_hooks(true) AS $hooks)
				{
					$hook_file = $hook = '';
					$dh = @opendir($hooks . '/root/includes/api/hooks');
					if ($dh)
					{
						while (($file = readdir($dh)) !== false)
						{
							if (strpos($file, 'hook_') === 0 && substr($file, -(strlen($phpEx) + 1)) === '.' . $phpEx)
							{
								$hook = substr($file, 0, -(strlen($phpEx) + 1));
								$hook_file = $hooks . '/root/includes/api/hooks/' . substr($file, 0, -(strlen($phpEx) + 1)) .'.' . $phpEx;
							}
						}
						closedir($dh);
					}
					if (!empty($hook) && file_exists($hook_file) && !in_array($hook, $installed))
					{
						include($hook_file);
						if (!empty($acp_api_hook_manager))
						{
							if (!empty($add_hook_lang))
							{
								if (include($hooks . '/root/language/' . $user->lang_name . '/' . $add_hook_lang . '.' . $phpEx))
								{
									$user->lang[$acp_api_hook_manager['name']] = $lang[$acp_api_hook_manager['name']];
									$add_hook_lang = null;
								}
							}
							$official = false;
							$is_up_to_date = true;
							if (isset($checksums[$hook]['sha1']) && isset($checksums[$hook]['version']))
							{
								if (in_array(sha1_file($hook_file), explode(',', $checksums[$hook]['sha1'])))
								{
									$official = true;
								}
								if (phpbb_version_compare($acp_api_hook_manager['version'], $checksums[$hook]['version'], '>='))
								{
									$is_up_to_date = true;
								}
								else
								{
									$is_up_to_date = false;
								}
							}

							$strptime = phpbb_api\functions\strptime($acp_api_hook_manager['date'][0], $acp_api_hook_manager['date'][1]);
							//Stupid admins can try to generate a notice as submitting bad format... @ save the world of them...
							$timestamp = @mktime($strptime['tm_hour'], $strptime['tm_min'], 0, $strptime['tm_mon']+1, $strptime['tm_mday'], $strptime['tm_year']+1900);
							$template->assign_block_vars('api_hooks', array(
								'AUTHOR'		=> $acp_api_hook_manager['author'],
								'WEBSITE'		=> isset($acp_api_hook_manager['website']) ? strip_tags($acp_api_hook_manager['website']) : false,
								'VERSION'		=> $acp_api_hook_manager['version'],
								'NAME'			=> isset($user->lang[$acp_api_hook_manager['name']]) ? $user->lang[$acp_api_hook_manager['name']] : $acp_api_hook_manager['name'],
								'DATE'			=> $user->format_date($timestamp),
								'FILE'			=> $hook . '.' . $phpEx,
								'OFFICIAL'		=> $official,
								'IS_UP_TO_DATE'	=> $is_up_to_date,
								'U_DOWNLOAD'	=> append_sid($this->u_action, 'action=download&amp;file=' . $hook . '_' . $acp_api_hook_manager['version']),
								'U_DELETE'		=> append_sid($this->u_action, 'action=delete&amp;file=' . $hook),
								'U_INSTALL'		=> append_sid($this->u_action, 'action=install&amp;file=' . $hook),
								'U_LAST_UPDATE'	=> 'http://geolim4.com/hooks.html?h=' . $hook,//No need append_sid() here
								'SHA1'			=> sha1_file($hook_file),
								'INSTALLED'		=> false,
							));
							$acp_api_hook_manager = null;
						}
					}
				}
				$can_upload = (@ini_get('file_uploads') == '0' || strtolower(@ini_get('file_uploads')) == 'off' || !@extension_loaded('zlib')) ? false : true;
				$template->assign_vars(array(
					'S_FORM_ENCTYPE'	=> ($can_upload) ? ' enctype="multipart/form-data"' : '',
					//pagination
					'S_VERSION'				=> isset($config['api_mod_version']) ? $config['api_mod_version'] : '',
					//Basics vars
					'S_HOOK_MANAGE'			=> true,
					'ICON_HOOK_DELETE'		=> '<img class="title" src="' . $phpbb_admin_path . 'images/api/delete_hook.png" alt="' . $user->lang['DELETE'] . '" title="' . $user->lang['DELETE'] . '" />',
					'ICON_HOOK_DOWNLOAD'	=> '<img class="title" src="' . $phpbb_admin_path . 'images/api/download_hook.png" alt="' . $user->lang['DOWNLOAD'] . '" title="' . $user->lang['DOWNLOAD'] . '" />',
					'ICON_HOOK_INSTALL'		=> '<img class="title" src="' . $phpbb_admin_path . 'images/api/install_hook.png" alt="' . $user->lang['ACP_PHPBB_API_HOOK_INSTALL'] . '" title="' . $user->lang['ACP_PHPBB_API_HOOK_INSTALL'] . '" />',
					'ICON_HOOK_UNINSTALL'	=> '<img class="title" src="' . $phpbb_admin_path . 'images/api/uninstall_hook.png" alt="' . $user->lang['ACP_PHPBB_API_HOOK_UNINSTALL'] . '" title="' . $user->lang['ACP_PHPBB_API_HOOK_UNINSTALL'] . '" />',
					'TITLE'					=> $this->page_title,
					'TITLE_EXPLAIN'			=> $user->lang['ACP_PHPBB_API_HOOKS_EXPLAIN'],
					'TITLE_IMG'				=> $phpbb_root_path . 'images/api_' . $mode . '.png',

					//Mods vars
					'ERRORS_VERSION'		=> sprintf($user->lang['API_ERRORS_VERSION_COPY'], $announcement_url, $config['api_mod_version']),

				));
			break;

			case'stats';
				$action = request_var('action', '');
				$interval = request_var('interval', '');
				$range = request_var('key_id', date('Y'));
				$image = request_var('image', '');
				$key_id = request_var('key_id', '');
				$day_stats_img = $stats_suffixe = '';
				$imgmap_param = 'ImageMap=get';
				$start = request_var('start', 0);
				$force_check =  request_var('force_check', 0);
				if (!file_exists($phpbb_root_path . "includes/api/pchart/class/pChart.phpbb." . $phpEx) || $force_check)
				{
					$this->check_pchart_integrity(true);
					if ($force_check)
					{
						trigger_error($user->lang['ACP_PHPBB_API_PCHART_CHECKED'] . adm_back_link(append_sid($this->u_action)), E_USER_NOTICE);
					}
				}
				else
				{
					$this->check_pchart_integrity();
				}
				if ($image)
				{
					switch($action)
					{
						case'all':
							switch($interval)
							{
								case'hour':
								case'day':
								case'month':
								case'year':
									if (request_var('ImageMap', '') == 'get')
									{
										header('Content-Type: text/plain; charset=UTF-8');
									}
									//Run pChart engine, it handle the rest...
									require($phpbb_root_path . "includes/api/stats/acp_api_stats_{$action}_{$interval}." . $phpEx);
								break;
							}
						break;
					}
				}
				if ($action == 'ajax')
				{
					phpbb_api\functions\add_hooks_lang();
					$range_year = request_var('range_year', (int) date('Y'));
					$range_month = request_var('range_month', date('M'));
					$range_day = request_var('range_day', (int) date('d'));
					$range_hour = request_var('range_hour', (int) date('d'));
					$where_sql = '';
					if (phpbb_api\functions\validate_key($key_id, true))
					{
						$where_sql = " AND ah.key_id = '" . $key_id . "'";
					}
					$queries_history = array();
					$begin_time = (int) mktime(0, 0, 0, phpbb_api\functions\inttostrtime($range_month, 'M'), $range_day, $range_year) + ($range_hour * API_HOUR_SECONDS);
					date('I', $begin_time) ? $begin_time = ($begin_time + API_HOUR_SECONDS): false;
					$end_time = $begin_time + API_HOUR_SECONDS;
					$sql = 'SELECT ah.*, ak.user_id, us.user_id, us.username, us.user_colour
						FROM ' . API_HISTORY_TABLE .  " ah
						LEFT JOIN " . API_KEYS_TABLE .  " ak
							ON (ak.key_id = ah.key_id)
						LEFT JOIN " . USERS_TABLE .  " us
							ON (us.user_id = ak.user_id)
						WHERE ah.time BETWEEN " . $begin_time . ' AND ' . $end_time . $where_sql . '
						ORDER BY ah.time ASC';
					$result = $db->sql_query_limit($sql, $config['api_mod_stat_limit'] + 1, $start, 60);
					while ($row = $db->sql_fetchrow($result))
					{
						if (isset($queries_history[$config['api_mod_stat_limit']-1]))
						{
							$queries_history['view_more'] = true;
							break;
						}
						if (isset($user->lang['API_FULL_TRANSLATED_METHOD'][str_replace('api_', '', $row['method'])]))
						{
							$row['method'] = $user->lang['API_FULL_TRANSLATED_METHOD'][str_replace('api_', '', $row['method'])];
						}
						$row['time'] = $user->format_date($row['time']);
						$row['username'] = get_username_string('full', $row['user_id'], $row['username'], $row['user_colour'], false, $profile_url);
						unset($row['user_id'], $row['user_colour']);
						$queries_history[] = $row;
					}
					$db->sql_freeresult($result);
					phpbb_api\functions\set_no_cache_headers();
					header('Content-Type: application/json; charset=UTF-8');
					echo(json_encode($queries_history, JSON_FORCE_OBJECT));
					garbage_collection();
					exit_handler();
				}
				if (phpbb_api\functions\validate_key($key_id, true))
				{
					$sql = 'SELECT MIN(time) AS time
						FROM ' . API_HISTORY_TABLE . "
						WHERE key_id = '{$key_id}'";//Look @ validate_key() function...
					$result = $db->sql_query($sql);
					$stats_begin = (int) $db->sql_fetchfield('time');
					$db->sql_freeresult($result);
					$stats_suffixe = '&amp;key_id=' . $key_id;
				}
				else
				{
					$stats_begin = $config['api_mod_install_age'];
				}
				if (!$stats_begin)
				{
					$stats_begin = time();
				}
				$year_from_to = $install_year = date('Y', $stats_begin);
				if (date('Y', $stats_begin) == date('Y', time()))
				{
					$year_from_to--;
					$install_year--;
				}
				$year_array = array();
				$s_year_count = 1;
				while (!in_array(date('Y'), explode(',', $year_from_to)))
				{
					if ($install_year != $install_year++)
					{
						$year_array[$install_year] = array();
						$year_from_to .= ',' . $install_year;
					}
					$template->assign_block_vars('timeline', array(
						'S_YEAR_COUNT'	=> $s_year_count++,
						'YEAR' 			=> $install_year,
					));
					$month_from_to = $install_month = date('m', mktime(0, 0, 0, 12, 32, $install_year));
					$s_month_count = 1;
					while (!in_array(date('m', mktime(0, 0, 0, $install_month, 32, $install_year)), $year_array[$install_year]))
					{
						if ($install_month >= $install_month++)
						{
							//Do not reach the current year/month
							if ($install_month <= 12 && mktime(0, 0, 0, $install_month - 1, date('d'), $install_year) <= time())
							{
								$month_from_to .= ',' . (strlen($install_month) == 1 ? '0' : '') . $install_month;
								$start_day = $range_day = '';
								while ($start_day <= date('t', mktime(0, 0, 0, $install_month - 1, 1, $install_year)))
								{
									$start_day++;
									(string) $range_day = ($range_day) ? $range_day . ',' . phpbb_api\functions\intdatify($start_day) : phpbb_api\functions\intdatify($start_day);
								}
							}
							$seed = unique_id();
							$template->assign_block_vars('timeline.month', array(
								'S_MONTH_COUNT'		=> $s_month_count++,
								'MONTH' 			=> $user->lang['datetime'][phpbb_api\functions\inttostrtime(phpbb_api\functions\inttostrtime(phpbb_api\functions\intdatify((int) $install_month - 1), 'm'), 'F')],
								'MONTH_SHORT' 		=> phpbb_api\functions\inttostrtime(phpbb_api\functions\inttostrtime(phpbb_api\functions\intdatify((int) $install_month - 1), 'm'), 'F'),
								'U_LINK' 			=> str_replace('&amp;', '&', append_sid($this->u_action, 'action=all&amp;image=get&amp;interval=day&amp;range_year=' . $install_year . '&amp;range_month=' . phpbb_api\functions\inttostrtime(phpbb_api\functions\intdatify((int) $install_month - 1), 'm') . '&amp;range_day=' . $range_day . '&amp;seed=' . $seed . $stats_suffixe)),//UA is not applicable here....
								'U_LINK_IMAGEMAP'	=> str_replace('&amp;', '&', append_sid($this->u_action, 'action=all&amp;image=get&amp;interval=day&amp;range_year=' . $install_year . '&amp;range_month=' . phpbb_api\functions\inttostrtime(phpbb_api\functions\intdatify((int) $install_month - 1), 'm') . '&amp;range_day=' . $range_day . '&amp;seed=' . $seed . $stats_suffixe . '&amp;ImageMap=get')),//Here too...
								'SEED'				=> $seed,
							));
							$s_day_count = 1;
							foreach (explode(',', $range_day) AS $range_day_)
							{
								$seed = unique_id();
								$template->assign_block_vars('timeline.month.day', array(
									'S_DAY_COUNT'		=> $s_day_count++,
									'DAY' 				=> $range_day_,
									'U_LINK' 			=> str_replace('&amp;', '&', append_sid($this->u_action, 'action=all&amp;image=get&amp;interval=hour&amp;range_year=' . $install_year . '&amp;range_month=' . phpbb_api\functions\inttostrtime(phpbb_api\functions\intdatify((int) $install_month - 1), 'm') . '&amp;range_day=' . $range_day_ . '&amp;seed=' . $seed . $stats_suffixe)),//UA is not applicable here....
									'U_LINK_IMAGEMAP'	=> str_replace('&amp;', '&', append_sid($this->u_action, 'action=all&amp;image=get&amp;interval=hour&amp;range_year=' . $install_year . '&amp;range_month=' . phpbb_api\functions\inttostrtime(phpbb_api\functions\intdatify((int) $install_month - 1), 'm') . '&amp;range_day=' . $range_day_ . '&amp;seed=' . $seed . $stats_suffixe . '&amp;ImageMap=get')),//Here too...
									'SEED'				=> $seed,
								));
								$day_stats_img = str_replace('&amp;', '&', append_sid($this->u_action, 'action=all&amp;image=get&amp;interval=hour&amp;range_year=' . $install_year . '&amp;range_month=' . phpbb_api\functions\inttostrtime(phpbb_api\functions\intdatify((int) $install_month - 1), 'm') . '&amp;range_day=' .  date('d') . '&amp;seed=' . $seed . $stats_suffixe));//UA is not applicable here....
							}
							if (mktime(0, 0, 0, $install_month, date('d'), $install_year) > time())
							{
								break;
							}
						}
						$year_array[$install_year][] = $install_month;
					}
					$seed = unique_id();
					$tpl_row = array(
						'YEAR' 				=> $install_year,
						'U_LINK' 			=> str_replace('&amp;', '&', append_sid($this->u_action, 'action=all&amp;image=get&amp;interval=month&amp;&amp;range_year=' . $install_year . '&amp;range_month=' . phpbb_api\functions\inttostrtime($month_from_to, 'm') . '&amp;seed=' . $seed . $stats_suffixe)),//UA is not applicable here....
						'U_LINK_IMAGEMAP'	=> str_replace('&amp;', '&', append_sid($this->u_action, 'action=all&amp;image=get&amp;interval=month&amp;&amp;range_year=' . $install_year . '&amp;range_month=' . phpbb_api\functions\inttostrtime($month_from_to, 'm') . '&amp;seed=' . $seed . $stats_suffixe . '&amp;ImageMap=get')),//Here too...
						'SEED'				=> $seed,
					);
					$template->alter_block_array('timeline', $tpl_row, true, 'change');
				}
				$template->assign_vars(array(
					//pagination
					'S_VERSION'				=> isset($config['api_mod_version']) ? $config['api_mod_version'] : '',
					//Basics vars
					'S_STATS_MANAGE'		=> true,
					'S_API_LAZYLOAD'		=> true,
					'S_SINGLE_STAT'			=> $key_id ? true : false,
					'S_KEY_ID'				=> phpbb_api\functions\validate_key($key_id) ? $key_id : '',
					'TITLE'					=> $this->page_title,
					'S_RANDOM_SEED'			=> unique_id(),
					'TITLE_EXPLAIN'			=> $user->lang['ACP_PHPBB_API_HOOKS_EXPLAIN'],
					'TITLE_IMG'				=> $phpbb_root_path . 'images/api_' . $mode . '.png',
					'U_DAY_STATS_IMG'		=> $day_stats_img,
					'U_PCHART_CHECK'		=> append_sid($this->u_action, 'force_check=1'),
					'S_IMGMAP_PARAM'		=> $imgmap_param,
					//Mods vars
					'ERRORS_VERSION'		=> sprintf($user->lang['API_ERRORS_VERSION_COPY'], $announcement_url, $config['api_mod_version']),

				));
			break;
			default:
				trigger_error('NO_MODE', E_USER_ERROR);
			break;
		}
		//Globals vars (ALL MODES)
		$template->assign_vars(array(
			'DATETIME_DAY_NAMES'			=> implode(', ', array('"'. utf8_substr($user->lang['datetime']['Sunday'], 0, 12) . '"', '"' . utf8_substr($user->lang['datetime']['Monday'], 0, 12) . '"', '"'. utf8_substr($user->lang['datetime']['Tuesday'], 0, 12) . '"', '"' . utf8_substr($user->lang['datetime']['Wednesday'], 0, 12) . '"', '"' .  utf8_substr($user->lang['datetime']['Thursday'], 0, 12) . '"', '"' . utf8_substr($user->lang['datetime']['Friday'], 0, 12) . '"', '"' . utf8_substr($user->lang['datetime']['Saturday'], 0, 12) . '"')),
			'DATETIME_DAY_NAMES_MIN'		=> implode(', ', array('"'. utf8_substr($user->lang['datetime']['Sun'], 0, 2) . '"', '"' . utf8_substr($user->lang['datetime']['Mon'], 0, 2) . '"', '"'. utf8_substr($user->lang['datetime']['Tue'], 0, 2) . '"', '"' . utf8_substr($user->lang['datetime']['Wed'], 0, 2) . '"', '"' .  utf8_substr($user->lang['datetime']['Thu'], 0, 2) . '"', '"' . utf8_substr($user->lang['datetime']['Fri'], 0, 2) . '"', '"' . utf8_substr($user->lang['datetime']['Sat'], 0, 2) . '"')),
			'DATETIME_MONTH_NAMES_SHORT' 	=> implode(', ', array('"'. utf8_substr($user->lang['datetime']['January'], 0, 3) . '"', '"' . utf8_substr($user->lang['datetime']['February'], 0, 3) . '"', '"'. utf8_substr($user->lang['datetime']['March'], 0, 3) . '"', '"' . utf8_substr($user->lang['datetime']['April'], 0, 3) . '"', '"' .  utf8_substr($user->lang['datetime']['May'], 0, 3) . '"', '"' . utf8_substr($user->lang['datetime']['June'], 0, 3) . '"', '"' . utf8_substr($user->lang['datetime']['July'], 0, 3) . '"', '"' . utf8_substr($user->lang['datetime']['August'], 0, 3) . '"', '"' . utf8_substr($user->lang['datetime']['September'], 0, 3) . '"', '"' . utf8_substr($user->lang['datetime']['October'], 0, 3) . '"', '"' . utf8_substr($user->lang['datetime']['November'], 0, 3) . '"', '"' . utf8_substr($user->lang['datetime']['December'], 0, 3) . '"')),
			'DATETIME_MONTH_NAMES'			=> implode(', ', array('"'. utf8_substr($user->lang['datetime']['January'], 0, 12) . '"', '"' . utf8_substr($user->lang['datetime']['February'], 0, 12) . '"', '"'. utf8_substr($user->lang['datetime']['March'], 0, 12) . '"', '"' . utf8_substr($user->lang['datetime']['April'], 0, 12) . '"', '"' .  utf8_substr($user->lang['datetime']['May'], 0, 12) . '"', '"' . utf8_substr($user->lang['datetime']['June'], 0, 12) . '"', '"' . utf8_substr($user->lang['datetime']['July'], 0, 12) . '"', '"' . utf8_substr($user->lang['datetime']['August'], 0, 12) . '"', '"' . utf8_substr($user->lang['datetime']['September'], 0, 12) . '"', '"' . utf8_substr($user->lang['datetime']['October'], 0, 12) . '"', '"' . utf8_substr($user->lang['datetime']['November'], 0, 12) . '"', '"' . utf8_substr($user->lang['datetime']['December'], 0, 12) . '"')),//UMADBRO?
			//Lang
			'L_ACP_PHPBB_API_DEACTIVATED_METHODS_EXP' => $user->lang('ACP_PHPBB_API_DEACTIVATED_METHODS_EXP', '<a href="' .  append_sid($phpbb_root_path . 'ucp.' . $phpEx, array('i' => 'phpbb_api', 'mode' => 'kb')) . '">', '</a>'),
		));
	}
	function directory_delete($dir)
	{
		global $phpbb_root_path;
		$hooks_dir = $phpbb_root_path . 'api/store';
		if (!file_exists($dir))
		{
			return true;
		}

		if (!is_dir($dir) && is_file($dir))
		{
			phpbb_chmod($dir, CHMOD_ALL);
			return unlink($dir);
		}

        foreach (scandir($dir) as $item)
		{
            if ($item == '.' || $item == '..')
			{
				continue;
			}
            if (!$this->directory_delete($dir . "/" . $item))
			{
				phpbb_chmod($dir . "/" . $item, CHMOD_ALL);
                if (!$this->directory_delete($dir . "/" . $item))
				{
					return false;
				}
            }
        }

		// Make sure we don't delete the store directory
		if ($dir != $hooks_dir)
		{
			return rmdir($dir);
		}
	}

	function directory_move($src, $dest, $allowed_patterns = array())
	{
		global $config;
		$src_contents = scandir($src);
		phpbb_api\functions\force_array($allowed_patterns);
		if (!is_dir($dest) && is_dir($src))
		{
			mkdir($dest, 0755);
		}
		foreach ($src_contents as $src_entry)
		{
			if ($allowed_patterns)
			{
				foreach ($allowed_patterns AS $allowed_patterns_)
				{
					if (strpos($dest . '/' . $src_entry, $allowed_patterns_) !== 0)//We match for first char in string so we do not use FALSE here.
					{
						continue;
					}
				}
			}
			if ($src_entry != '.' && $src_entry != '..')
			{
				if (is_dir($src . '/' . $src_entry) && !is_dir($dest . '/' . $src_entry))
				{
					$this->directory_move($src . '/' . $src_entry, $dest . '/' . $src_entry);
				}
				else if (is_file($src . '/' . $src_entry) && !is_file($dest . '/' . $src_entry))
				{
					copy($src . '/' . $src_entry, $dest . '/' . $src_entry);
					chmod($dest . '/' . $src_entry, 0755);
				}
			}
		}
	}
//Fast Install checking
/**
* Check all the steps of the install of the mod
* @noparam
* @return trigger_error if install is corrupted/uncompleted
*/
	function api_check_install()
	{
		global $config, $user, $phpbb_root_path, $phpbb_admin_path, $phpEx, $db;
		if (@$config['api_next_install_check'] > time())
		{
			return;
		}
		if (!class_exists('phpbb_db_tools') || !class_exists('dbal'))
		{
			include($phpbb_root_path . 'includes/db/db_tools.' . $phpEx);
		}
		$api_db	= new phpbb_db_tools($db);
		$error = '';
		if (!defined('API_KEYS_TABLE'))
		{
			//Check constant here for avoid SQL error when collumn/table checking...
			$error .= $user->lang['ACP_PHPBB_API_ERR_NOCONST'] . '<br />';
			trigger_error($error . adm_back_link($this->u_action), E_USER_WARNING);
		}
/* 		$sql = 'SELECT style_name
			FROM ' . STYLES_TABLE . "
			WHERE style_id= '" . $config['default_style'] . " ' ";
		$result = $db->sql_query($sql);
		$row_style = $db->sql_fetchrow($result);
		$db->sql_freeresult($result); */
		$filelist = array ();
		$filelist[] = 'adm/style/api/acp_phpbb_api_config.html';
		$filelist[] = 'adm/style/api/acp_phpbb_api_keys.html';
		$filelist[] = 'adm/style/api/acp_phpbb_api_logs.html';
		$filelist[] = 'adm/style/api/acp_phpbb_api_hooks.html';
		$filelist[] = 'adm/style/api/acp_phpbb_api_stats.html';
		$filelist[] = 'adm/style/api/acp_phpbb_api_jquery_package.html';
		$filelist[] = 'includes/api/templates/api_default.html';
		$filelist[] = 'includes/api/templates/fatal_error_handler.html';
		$filelist[] = 'includes/api/cache.' . $phpEx;
		$filelist[] = 'includes/api/common.' . $phpEx;
		$filelist[] = 'includes/api/config.' . $phpEx;
		$filelist[] = 'includes/api/constants.' . $phpEx;
		$filelist[] = 'includes/api/constructor.' . $phpEx;
		$filelist[] = 'includes/api/core.' . $phpEx;
		$filelist[] = 'includes/api/core_extended/core_loader.' . $phpEx;
		$filelist[] = 'includes/api/core_extended/core_methods.' . $phpEx;
		$filelist[] = 'includes/api/core_extended/core_static.' . $phpEx;
		$filelist[] = 'includes/api/core_extended/core_crypto.' . $phpEx;
		$filelist[] = 'includes/api/core_extended/core_error_catcher.' . $phpEx;
		$filelist[] = 'includes/api/error.log';
		$filelist[] = 'includes/api/functions.' . $phpEx;
		$filelist[] = 'includes/api/stats/acp_api_stats_all_day.' . $phpEx;
		$filelist[] = 'includes/api/stats/acp_api_stats_all_hour.' . $phpEx;
		$filelist[] = 'includes/api/stats/acp_api_stats_all_month.' . $phpEx;
		$filelist[] = 'includes/api/store/';
		//$filelist[] = 'includes/acp/acp_phpbb_api.' . $phpEx; //current file :P
		$filelist[] = 'includes/acp/info/acp_phpbb_api.' . $phpEx;
		$filelist[] = 'includes/hooks/hook_api.' . $phpEx;
		$filelist[] = 'includes/ucp/ucp_phpbb_api.' . $phpEx;
		$filelist[] = 'includes/ucp/info/ucp_phpbb_api.' . $phpEx;
		$filelist[] = 'language/' . $config['default_lang'] . '/mods/phpbb_api.' . $phpEx;
		$filelist[] = 'language/' . $config['default_lang'] . '/mods/info_acp_phpbb_api.' . $phpEx;
		$filelist[] = 'language/' . $config['default_lang'] . '/mods/info_ucp_phpbb_api.' . $phpEx;
		$filelist[] = 'language/' . $config['default_lang'] . '/mods/permissions_phpbb_api.' . $phpEx;

		if (!$api_db->sql_table_exists(API_KEYS_TABLE))
		{
			$error .= sprintf($user->lang['ACP_PHPBB_API_INSTALL_NO_TABLE'] . '<br />', API_KEYS_TABLE);
			$error .= $user->lang['ACP_PHPBB_API_ERR_INSTALL'];
			//Disable Mod: install not complete !!!
			if ($config['api_mod_enable'])
			{
				add_log('critical', 'ACP_PHPBB_API_LOG_OFF', $error);
			}
			set_config('api_mod_enable', 0, false);
			trigger_error($error . adm_back_link($this->u_action), E_USER_WARNING);
		}
		$collumnlist = array ();

		foreach ($filelist as $key => $file_)
		{
			if (!file_exists($phpbb_root_path . $file_) && substr($file_, -1) != '/')
			{
				$error .= sprintf($user->lang['ACP_PHPBB_API_INSTALL_NO_FILE'] . '<br />', $file_);
			}
			else if (!is_dir($phpbb_root_path . $file_) && substr($file_, -1) == '/')
			{
				$error .= sprintf($user->lang['ACP_PHPBB_API_INSTALL_NO_DIRECTORY'] . '<br />', $file_);
			}
		}
		foreach ($collumnlist as $key => $collumn_)
		{
			if (!$api_db->sql_column_exists(API_KEYS_TABLE, $collumn_))
			{
				$error .= sprintf($user->lang['ACP_PHPBB_API_INSTALL_NO_COLLUMN'] . '<br />', $collumn_, API_KEYS_TABLE);
			}
		}
		if ($error)
		{
			$error .= $user->lang['ACP_PHPBB_API_ERR_INSTALL'];
			//Disable Mod: install not complete !!!
			if ($config['api_mod_enable'])
			{
				add_log('critical', 'ACP_PHPBB_API_LOG_OFF', $error);
			}
			set_config('api_mod_enable', 0, false);
			trigger_error($error . adm_back_link($this->u_action), E_USER_WARNING);
		}
		set_config('api_next_install_check', time() + 7200, false);
	}
	function check_pchart_integrity($force_check = false)
	{
		global $config, $user, $phpbb_root_path, $phpbb_admin_path, $phpEx, $db;
		if (@$config['pchart_next_install_check'] > time() && !$force_check)
		{
			return;
		}
		else
		{
			clearstatcache(true);
		}
		$pchart_root_path = API_ROOT_PATH . 'pchart/';
		$error = '';
		$files_list = array(
			'cache/'		=> 	array(
				//Nothing here
			),
			'class/'		=> 	array(
					//0 => 'pChart.phpbb.' . $phpEx,
					1 => 'pBarcode128.class.' . $phpEx,
					2 => 'pBarcode39.class.' . $phpEx,
					3 => 'pBubble.class.' . $phpEx,
					4 => 'pCache.class.' . $phpEx,
					5 => 'pData.class.' . $phpEx,
					6 => 'pDraw.class.' . $phpEx,
					7 => 'pImage.class.' . $phpEx,
					8 => 'pIndicator.class.' . $phpEx,
					9 => 'pPie.class.' . $phpEx,
					10 => 'pRadar.class.' . $phpEx,
					11 => 'pScatter.class.' . $phpEx,
					12 => 'pSplit.class.' . $phpEx,
					13 => 'pSpring.class.' . $phpEx,
					14 => 'pStock.class.' . $phpEx,
					15 => 'pSurface.class.' . $phpEx
			),
			'data/'		=> 	array(
					1 => '128B.db',
					2 => '39.db',
			),
			'fonts/'		=> 	array(
					1 => 'advent_light.ttf',
					2 => 'Bedizen.ttf',
					3 => 'calibri.ttf',
					4 => 'Forgotte.ttf',
					5 => 'GeosansLight.ttf',
					6 => 'MankSans.ttf',
					7 => 'pf_arma_five.ttf',
					8 => 'Silkscreen.ttf',
					9 => 'verdana.ttf'
			),
			'palettes/'		=> 	array(
					1 => 'autumn.color',
					2 => 'blind.color',
					3 => 'evening.color',
					4 => 'kitchen.color',
					5 => 'light.color',
					6 => 'navy.color',
					7 => 'shade.color',
					8 => 'spring.color',
					9 => 'summer.color'
			),
			'tmp/'		=> 	array(
				//Nothing here
			),
			'GPLv3.txt' => array(),
		);
		foreach ($files_list AS $directory_ => $file_)
		{
			if (!file_exists($pchart_root_path . $directory_))
			{
				$error .= sprintf($user->lang['ACP_PHPBB_API_INSTALL_NO_DIRECTORY'] . '<br />', str_replace($phpbb_root_path, '[ROOT]/', $pchart_root_path) . $directory_);
			}
			if (!empty($file_))
			{
				foreach ($file_ AS $file__)
				{
					if (!file_exists($pchart_root_path . $directory_ . $file__))
					{
						$error .= sprintf($user->lang['ACP_PHPBB_API_INSTALL_NO_FILE'] . '<br />', str_replace($phpbb_root_path, '[ROOT]/', $pchart_root_path) . $directory_ . $file__);
					}
				}
			}
		}
		if ($error)
		{
			$error .= '<br />' . $user->lang('ACP_PHPBB_API_NO_PCHART', str_replace($phpbb_root_path, '[ROOT]/', $pchart_root_path));
			trigger_error($error . adm_back_link($this->u_action), E_USER_WARNING);
		}
		else
		{
			set_config('pchart_next_install_check', time() + 7200, false);
		}
	}
}
?>