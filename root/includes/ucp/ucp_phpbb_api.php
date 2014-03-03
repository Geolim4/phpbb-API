<?php
/**
*
* @package UCP phpBB API
^>@version $Id: ucp_phpbb_api.php v0.0.1 00h11 12/20/2013 Geolim4 Exp $
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
* ucp_phpbb_api
* @package ucp
*/
define('IN_PHPBB_API', true);

class ucp_phpbb_api
{
	var $u_action;
	var $p_master;

	function ucp_phpbb_api(&$p_master)
	{
		$this->p_master = &$p_master;
	}
	function main($id, $mode)
	{
		global $config, $db, $user, $auth, $template, $phpbb_root_path, $phpEx, $module;
		$user->add_lang(array('acp/common', 'mods/phpbb_api', 'mods/info_acp_phpbb_api'));
		if (!defined('API_CONST_LOADED'))
		{
			include($phpbb_root_path . 'includes/api/constants.' . $phpEx);
			include($phpbb_root_path . 'includes/api/functions.' . $phpEx);
		}
		phpbb_api\functions\add_hooks_lang();

		$submit = isset($_POST['submit']) ? true : false;
		$s_hidden_fields = $key_selector = '';
		$uncensored = request_var('uncensored', 0);
		$generate = request_var('generate', '');
		$regenerate = request_var('regenerate', '');//API_STATUS_DEACTIVATED
		$email_auth = request_var('email_auth', 0);
		$force_post = request_var('force_post', 0);
		$start =  request_var('start', 0);
		$ajax =  request_var('ajax', 0);
		$input_id =  request_var('input_id', '');
		$key_id = request_var('key_id', '');
		if ($key_id && $config['api_mod_ucp_crypt'])
		{
			$key_id = '$H$' . $key_id;
		}
		$key_ips = request_var('key_ips', '');
		$key_ips_type = request_var('key_ips_type', API_IP_ALLOWED);
		$key_allowed = false;
		//secure your mom
		$form_key = 'acp_phpbb_api';
		add_form_key($form_key);
		switch($mode)
		{
			/***********
			*Keys MODE
			***********/
			case'keys':
					//Init $sql_ary
					$sql_ary = array();
					if ($auth->acl_get('a_phpbb_api_keys'))
					{
						$where_sql = 'WHERE user_id = ' . (int) $user->data['user_id'];
					}
					else
					{
						$where_sql = 'WHERE user_id = ' . (int) $user->data['user_id'] . ' AND key_type = ' . API_TYPE_USER;
					}
					$sql = 'SELECT *
						FROM '  . API_KEYS_TABLE . "
						$where_sql";
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result))
					{
						$key_exists = true;
						if (empty($key_id))
						{
							$key_id = $config['api_mod_ucp_crypt'] ? phpbb_hash($row['key_id']) : $row['key_id'];
							foreach ($row AS $key => $row_)
							{
								if ($key == 'key_id' && $config['api_mod_ucp_crypt'])
								{
									$template->assign_var('S_REAL_KEY_ID', $row_);
									$row_ = str_replace('$H$', '', phpbb_hash($row_));
								}
								//A simple way to prefix them :)
								$template->assign_var('S_' . strtoupper($key), $row_);
							}
						}
						if ((phpbb_check_hash($row['key_id'], $key_id) && $config['api_mod_ucp_crypt']) xor $row['key_id'] == $key_id)
						{
							//Force the SID to auth this ajax request
							if ($ajax && phpbb_api\functions\is_post_request() && (isset($_GET['sid']) && $user->session_id === $_GET['sid']) && check_link_hash(request_var('hash', ''), "api_key"))
							{
								switch($input_id)
								{
									case 'key_id':
										echo($row['key_id']);
									break;
									
									case 'secret_key':
										echo($row['key_secret_key']);
									break;
									
									default:
										echo($input_id);
									break;
								}
								garbage_collection();
								exit_handler();
							}
							$key_allowed = true;
							$is_expired = ($row['expire_time'] && $row['expire_time'] < time()) ? true : false;
							$is_out_of_quota = (!$auth->acl_get('u_phpbb_api_ignore_max') && $row['max_queries'] <= $row['queries']) ? true : false;
							$is_deactivated = ($row['key_status'] == API_STATUS_SUSPENDED || $row['key_status'] == API_STATUS_DEACTIVATED) ? true : false;
							if (!$is_deactivated && $regenerate && ($is_expired || $is_out_of_quota) && $auth->acl_get('u_phpbb_api_regenerate'))
							{
								if (confirm_box(true))
								{
										$sql_ary = array(
											'key_status' => API_STATUS_DEACTIVATED,
										);
										$sql = 'UPDATE ' . API_KEYS_TABLE . '
											SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
											WHERE key_id = \'' . $db->sql_escape($row['key_id']) . '\'';
										$db->sql_query($sql);
										phpbb_api\functions\api_add_log('API_LOG_API_KEY_DEACTIVATED', $row['key_id']);
										//Generate a new fresh meat
										goto keygeneration;
								}
								else
								{
									if ($config['api_mod_ucp_crypt'])
									{
										$key_id = str_replace('$H$', '', $key_id);
									}
									confirm_box(false, $user->lang['CONFIRM_OPERATION'] . '<br />' . $user->lang('UCP_PHPBB_API_CONFIRM_EXPLAIN', $row['key_id']), build_hidden_fields(array(
											'key_id'		=> $key_id,
											'regenerate'	=> true,
											)
										)
									);
								}
							}
							if ($submit && !$is_deactivated)
							{
								//before all check form integrity pls !!
								if (!check_form_key($form_key))
								{
									trigger_error($user->lang['FORM_INVALID'], E_USER_WARNING);
								}
								$sql_ary['email_auth']		= $email_auth;
								//Force email authentication for admin keys
								if ($row['key_type'] == API_TYPE_ADMIN)
								{
									$sql_ary['email_auth'] = true;
								}
								$sql_ary['force_post']		= $force_post;
								if ($auth->acl_get('u_phpbb_api_ips') && $config['api_mod_list_ip'])
								{
									$sql_ary['key_ips_type']	= $key_ips_type;
									$sql_ary['key_ips']			= $key_ips;
								}

								$sql = 'UPDATE ' . API_KEYS_TABLE . '
									SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
									WHERE key_id = \'' . $db->sql_escape($row['key_id']) . '\'';
								$db->sql_query($sql);
								meta_refresh(3, append_sid($this->u_action, 'key_id=' . $row['key_id']));
								$message = $user->lang['UCP_PHPBB_API_UPDATED_CFG'];
								$message .= $user->lang('RETURN_PAGE', '<br /><br /><a href="' . append_sid($this->u_action, '') . '">', '</a>');
								trigger_error($message, E_USER_NOTICE);
							}
							foreach ($row AS $key => $row_)
							{
								if ($key == 'key_id' && $config['api_mod_ucp_crypt'])
								{
									$template->assign_var('S_REAL_KEY_ID', $row_);
									$row_ = str_replace('$H$', '', phpbb_hash($row_));
								}
								//A simple way to prefix them :)
								$template->assign_var('S_' . strtoupper($key), $row_);
							}
							$template->assign_vars(array(
								'U_AJAX_GET_KEY'			=> append_sid($this->u_action, 'ajax=1' . '&amp;hash=' . generate_link_hash('api_key') , true, $user->session_id),//Force the SID
								'S_API_MOD_UCP_CRYPT'		=> $config['api_mod_ucp_crypt'] ? true : false,
								'S_UCP_PHPBB_API_STATUS'	=> ($row['key_status'] == API_STATUS_ACTIVE) ? $user->lang['UCP_PHPBB_API_STATUS_TYPE'][API_STATUS_ACTIVE] : ((($row['key_status'] == API_STATUS_SUSPENDED)) ? $user->lang['UCP_PHPBB_API_STATUS_TYPE'][API_STATUS_SUSPENDED] : $user->lang['UCP_PHPBB_API_STATUS_TYPE'][API_STATUS_DEACTIVATED]),
								'S_UCP_PHPBB_API_VALIDITY'	=> $row['expire_time'] ? ($is_expired ? $user->lang['UCP_PHPBB_API_VALIDITY_EXPIRED'] : $user->lang('UCP_PHPBB_API_VALIDITY_UNTIL', $user->format_date($row['expire_time']))) : ($is_out_of_quota ? $user->lang['UCP_PHPBB_API_OUT_OF_QUOTA'] : $user->lang['UCP_PHPBB_API_VALIDITY_LFTM']),
								'S_IS_ADMIN_KEY'			=> ($row['key_type'] == API_TYPE_ADMIN) ? $user->lang('UCP_PHPBB_API_ADMIN_KEY', '<a href="' . append_sid("{$phpbb_root_path}adm/index.$phpEx", array('i' => 'phpbb_api', 'mode' => 'keys', 'action' => 'edit', 'key_id[]' => $row['key_id']), true, $user->session_id) . '">' , '</a>') : false,
								'S_CAN_MANAGE_KEYS'			=> $auth->acl_get('a_phpbb_api_keys') ? true : false,
								'S_IS_EXPIRED'				=> $is_expired,
								'S_IS_OUT_OF_QUOTA'			=> $is_out_of_quota,
								'S_IS_DEACTIVATED'			=> $is_deactivated,
								'UCP_PHPBB_API_REGENERATE'	=> $user->lang('UCP_PHPBB_API_REGENERATE', append_sid($this->u_action, 'regenerate=true')),
							));
						}
						$template->assign_vars(array(
							'S_UCP_ACTION' => $this->u_action,
							'S_CAN_SET_KEY_IP' => ($auth->acl_get('u_phpbb_api_ips') && $config['api_mod_list_ip']) ? true : false,
							'S_CAN_REGENERATE' => $auth->acl_get('u_phpbb_api_regenerate'),
						));
					}
					$db->sql_freeresult($result);
					if (!$row && !isset($key_exists))
					{
						//Here we go from regeneration
						keygeneration:

						$module->set_display('phpbb_api', 'stats', false);
						$module->set_display('phpbb_api', 'history', false);
						$module->set_display('phpbb_api', 'kb', false);
						if (($generate || $regenerate) && $auth->acl_get('u_phpbb_api_regenerate'))
						{
							$sql_ary['creation_time']			= time();
							$sql_ary['expire_time']				= phpbb_api\functions\calculate_key_validity();
							$sql_ary['user_id']					= $user->data['user_id'];
							$sql_ary['key_id']					= phpbb_api\functions\generate_api_key();
							$sql_ary['queries']					= 0;
							$sql_ary['max_queries_per_day']		= $config['api_mod_mqpd'];
							$sql_ary['max_queries_per_week']	= $config['api_mod_mqpw'];
							$sql_ary['max_queries_per_month']	= $config['api_mod_mqpm'];
							$sql_ary['max_queries']				= $config['api_mod_max_queries'];
							$sql_ary['query_sql']				= 0;
							$sql_ary['query_sql_api']			= 0;
							$sql_ary['email_auth']				= 0;
							$sql_ary['key_status']				= API_STATUS_ACTIVE;
							$sql_ary['force_post']				= 0;
							$sql_ary['key_type']				= API_TYPE_USER;
							$sql_ary['key_ips_type']			= API_IP_ALLOWED;
							$sql_ary['key_ips']					= $user->ip;
							$sql_ary['gen_source']				= API_GEN_SOURCE_UCP;
							$sql_ary['deactivated_methods']		= $config['api_mod_deactivated_methods'];
							$sql_ary['key_secret_key']			= phpbb_api\functions\generate_api_secret_key();

							$sql = 'INSERT INTO ' . API_KEYS_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
							$db->sql_query($sql);
							phpbb_api\functions\api_add_log('API_LOG_KEY_CREATED', $sql_ary['key_id'], $sql_ary['key_id']);
							//Hate IE...
							phpbb_api\functions\set_no_cache_headers();
							redirect($this->u_action . '&amp;key_id=' . ($config['api_mod_ucp_crypt'] ? str_replace('$H$', '', phpbb_hash($sql_ary['key_id'])) : $sql_ary['key_id']));
						}
						$template->assign_vars(array(
							'S_NO_API_KEY'					=> true,
							'S_CAN_GENERATE'				=> $auth->acl_get('u_phpbb_api_regenerate'),
							'UCP_PHPBB_API_GENERATE'		=> $user->lang('UCP_PHPBB_API_GENERATE', append_sid($this->u_action, 'generate=true'))
						));
					}
			break;

			/***********
			*Stats MODE
			***********/
			case'stats':
					include(API_ROOT_PATH . 'core.' . $phpEx);
					if ($auth->acl_get('a_phpbb_api_keys'))
					{
						$where_sql = 'WHERE user_id = ' . (int) $user->data['user_id'];
					}
					else
					{
						$where_sql = 'WHERE user_id = ' . (int) $user->data['user_id'] . ' AND key_type = ' . API_TYPE_USER;
					}
					$sql = 'SELECT *
						FROM '  . API_KEYS_TABLE . "
						$where_sql";
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result))
					{
						$key_exists = true;
						if (empty($key_id))
						{
							$key_id =  $config['api_mod_ucp_crypt'] ? phpbb_hash($row['key_id']) : $row['key_id'];
						}
						if ((phpbb_check_hash($row['key_id'], $key_id) && $config['api_mod_ucp_crypt']) xor $row['key_id'] == $key_id)
						{
							$key_allowed = true;
							$queries_history = array();
							$sql = 'SELECT *
								FROM '  . API_HISTORY_TABLE . "
								WHERE key_id = '" .  $db->sql_escape($row['key_id']) . "'
									AND time >= " . (int) (time() - phpbb_api\functions\calculate_month_seconds());
							$result2 = $db->sql_query($sql, 60);
							while ($hrow = $db->sql_fetchrow($result2))
							{
								$queries_history[] = $hrow['time'];
							}
							$db->sql_freeresult($result2);
							$counter = phpbb_api\functions\sort_queries_history($queries_history);
							if ($auth->acl_get('u_phpbb_api_ignore_day'))
							{
								$row['max_queries_per_day'] = 0;
							}
							if ($auth->acl_get('u_phpbb_api_ignore_week'))
							{
								$row['max_queries_per_week'] = 0;
							}
							if ($auth->acl_get('u_phpbb_api_ignore_month'))
							{
								$row['max_queries_per_month'] = 0;
							}
							if ($auth->acl_get('u_phpbb_api_ignore_max'))
							{
								$row['max_queries'] = 0;
							}
							$template->assign_vars(array(
								'S_DAY_STATS' => true,
								'S_QUERIES_PER_DAY' => $user->lang('UCP_PHPBB_API_QUERIE' . (($counter['queries_per_day'] > 1) ? 'S' : ''), $counter['queries_per_day'], (($row['max_queries_per_day']) ? $row['max_queries_per_day'] : $user->lang['UCP_PHPBB_API_INFINITE_SYMBOL'])),
								'S_PERCENT_PER_DAY' => $row['max_queries_per_day'] && $counter['queries_per_day'] ? round(($counter['queries_per_day'] * 100) / $row['max_queries_per_day'], 2) : 0,
								'S_CEIL_PER_DAY' => $row['max_queries_per_day'] && $counter['queries_per_day'] ? ceil(($counter['queries_per_day'] * 100) / $row['max_queries_per_day']) : 0,

								'S_WEEK_STATS' => true,
								'S_QUERIES_PER_WEEK' => $user->lang('UCP_PHPBB_API_QUERIE' . (($counter['queries_per_week'] > 1) ? 'S' : ''), $counter['queries_per_week'], (($row['max_queries_per_week']) ? $row['max_queries_per_week'] : $user->lang['UCP_PHPBB_API_INFINITE_SYMBOL'])),
								'S_PERCENT_PER_WEEK' => $row['max_queries_per_week'] && $counter['queries_per_week'] ? round(($counter['queries_per_week'] * 100) / $row['max_queries_per_week'], 2) : 0,
								'S_CEIL_PER_WEEK' => $row['max_queries_per_week'] && $counter['queries_per_week'] ? ceil(($counter['queries_per_week'] * 100) / $row['max_queries_per_week']) : 0,

								'S_MONTH_STATS' => true,
								'S_QUERIES_PER_MONTH' => $user->lang('UCP_PHPBB_API_QUERIE' . (($counter['queries_per_month'] > 1) ? 'S' : ''), $counter['queries_per_month'], (($row['max_queries_per_month']) ? $row['max_queries_per_month'] : $user->lang['UCP_PHPBB_API_INFINITE_SYMBOL'])),
								'S_PERCENT_PER_MONTH' => $row['max_queries_per_month'] && $counter['queries_per_month'] ? round(($counter['queries_per_month'] * 100) / $row['max_queries_per_month'], 2) : 0,
								'S_CEIL_PER_MONTH' => $row['max_queries_per_month'] && $counter['queries_per_month'] ? ceil(($counter['queries_per_month'] * 100) / $row['max_queries_per_month']) : 0,

								'S_MAX_STATS' => true,
								'S_QUERIES_MAX' => $user->lang('UCP_PHPBB_API_QUERIE' . (($row['queries'] > 1) ? 'S' : ''), $row['queries'], (($row['max_queries']) ? $row['max_queries'] : $user->lang['UCP_PHPBB_API_INFINITE_SYMBOL'])),
								'S_PERCENT_MAX' => $row['max_queries'] && $row['queries'] ? round(($row['queries'] * 100) / $row['max_queries'], 2) : 0,
								'S_CEIL_MAX' => $row['max_queries'] && $row['queries'] ? ceil(($row['queries'] * 100) / $row['max_queries']) : 0,

								'S_LAST_QUERIES' => $user->lang('UCP_PHPBB_API_LAST_QUERIES', sizeof($queries_history) ? $user->format_date(max($queries_history)) : $user->lang['UCP_PHPBB_API_NO_REQUEST']),
							));
						}
					}
					$db->sql_freeresult($result);
					if (!isset($key_exists))
					{
						phpbb_api\functions\set_no_cache_headers();
						redirect(append_sid($phpbb_root_path . 'ucp.' . $phpEx, 'i=phpbb_api&amp;mode=keys'));
					}
					$template->assign_vars(array(
						'S_UCP_ACTION' => $this->u_action,
					));
			break;
			/***********
			*History MODE
			***********/
			case'history':
				include(API_ROOT_PATH . 'core.' . $phpEx);
				if ($auth->acl_get('a_phpbb_api_keys'))
				{
					$where_sql = 'WHERE user_id = ' . (int) $user->data['user_id'];
				}
				else
				{
					$where_sql = 'WHERE user_id = ' . (int) $user->data['user_id'] . ' AND key_type = ' . API_TYPE_USER;
				}
				$sql = 'SELECT *
					FROM '  . API_KEYS_TABLE . "
					$where_sql";
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
				{
					if (empty($key_id))
					{
						$key_id =  $config['api_mod_ucp_crypt'] ? phpbb_hash($row['key_id']) : $row['key_id'];
					}
					if ((phpbb_check_hash($row['key_id'], $key_id) && $config['api_mod_ucp_crypt']) xor $row['key_id'] == $key_id)
					{
						$key_exists = true;
						$key_allowed = true;
						$queries_history = array();
						$sql = 'SELECT *
							FROM '  . API_HISTORY_TABLE . "
							WHERE key_id = '" .  $db->sql_escape($row['key_id']) . "'
							ORDER BY history_id DESC";
						$result2 = $db->sql_query_limit($sql, $config['api_mod_acp_pagination'], $start, 60);
						while ($row2 = $db->sql_fetchrow($result2))
						{
							$row2['time'] = $user->format_date($row2['time']);
							if (isset($user->lang['API_FULL_TRANSLATED_METHOD'][str_replace('api_', '', $row2['method'])]))
							{
								$row2['method'] = $user->lang['API_FULL_TRANSLATED_METHOD'][str_replace('api_', '', $row2['method'])];
							}
							$row2['key_id'] = $uncensored ? $row2['key_id'] : phpbb_api\functions\censor_key($row2['key_id']);
							$template->assign_block_vars('history', array_change_key_case($row2, CASE_UPPER));
						}
						$db->sql_freeresult($result2);

						$sql = "SELECT COUNT(key_id) AS history
							FROM "  . API_HISTORY_TABLE . "
							WHERE key_id = '" .  $db->sql_escape($row['key_id']) . "'";
						$result = $db->sql_query($sql, 60);
						$history = $db->sql_fetchfield('history');
						$db->sql_freeresult($result);

						//We got the key, no more looping please
						break;
					}
				}
				$db->sql_freeresult($result);
				$template->assign_vars(array(
					//pagination
					'S_ON_PAGE'				=> ($history > $config['api_mod_acp_pagination']) ? true : false,
					'TOTAL_MESSAGES'		=> sprintf($user->lang['UCP_PHPBB_API_PAGINATION_EVT' .(($history > 1) ? 'S' : '')], $history),
					'PAGE_NUMBER' 			=> on_page($history, $config['api_mod_acp_pagination'], $start),
					'PAGINATION' 			=> generate_pagination($this->u_action, $history, $config['api_mod_acp_pagination'], $start),
					'S_UCP_ACTION' => $this->u_action,
				));
			break;
			/***********
			*KB MODE (A light version of faq.php)
			***********/
			case'kb':
				$key_exists = $key_allowed = true;
				$hard_bbcodes = array('[br]' => '<br />');
				//Check if current user has an admin key
				$sql = 'SELECT *
					FROM '  . API_KEYS_TABLE . '
					WHERE user_id = ' . (int) $user->data['user_id'] . '
						AND key_type = ' . API_TYPE_ADMIN . '
						AND key_status = ' . API_STATUS_ACTIVE;
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);
				// Pull the array data from the lang pack
				$switch_column = $found_switch = false;
				$help_blocks = array();
				$api_methods = '';
				foreach (phpbb_api\functions\get_api_methods(true, false, false, 'API_TRANSLATED_METHOD') AS $key_ => $api_methods_)
				{
					$api_methods .= '<li>' . $key_ . ' => ' . $api_methods_ . '</li>';
				}
				//Assign template-like vars into knowledge language
				$real_api_key = phpbb_api\functions\extract_hashed_key($key_id, $user->data['user_id']);
				$api_key = $uncensored ? $real_api_key : phpbb_api\functions\censor_key($real_api_key);
				$crypto_config = \phpbb_api\api::STC_get_crypto_config();
				$assign_lang_vars = array(
					'KB_GATEWAY_INTERFACE' => generate_board_url() . '/api',
					'KB_TRANSLATED_METHOD' => $api_methods,
					'KB_USER_EMAIL' => $user->data['user_email'],
					'KB_SERVER_PROTOCOL' => $config['server_protocol'],
					'KB_SERVER_NAME' => $config['server_name'],
					'KB_SCRIPT_PATH' => ($config['script_path'] != '/') ? $config['script_path'] : '',
					'KB_WILDCARD_CHAR' => $config['api_mod_wildcard_char'],
					'KB_API_KEY' => $api_key,
					'KB_CRYPTO_CIPHER' => $crypto_config['cypher'],
					'KB_CRYPTO_MODE' => $crypto_config['mode'],
					'KB_CRYPTO_IV' => $crypto_config['iv'],
					'KB_CRYPTO_FILENAME' => $crypto_config['filename'],
				);
				foreach (array_unique($user->lang['UCP_PHPBB_API_KNOWLEDGE_BASE'], SORT_REGULAR) AS $help_ary)
				{
					if (!$row && !empty($help_ary['a_']))
					{
						//User has no permission to view administrator manual
						continue;
					}
					if (!empty($help_ary['cfg']) && empty($config[$help_ary['cfg']]))
					{
						//Require that the specified config must be true
						continue;
					}
					if (!empty($help_ary['method']))
					{
						foreach (explode(',', $help_ary['method']) AS $method_)
						{
							if (isset($user->lang['API_TRANSLATED_METHOD'][$method_]))
							{
								$method .= $user->lang('UCP_PHPBB_API_WITH', $method_, $user->lang['API_TRANSLATED_METHOD'][$method_]) . ', ';
							}
							else
							{
								$method .= $user->lang('UCP_PHPBB_API_WITH', $method_, $method_) . ', ';
							}
						}
						$help_ary[1] = str_replace('{METHOD}', $user->lang['API_TRANSLATED_METHOD'][$method_], $help_ary[1]);
						unset($method_);
					}
					foreach ($assign_lang_vars AS $key_ => $assign_lang_vars_)
					{
						$help_ary[1] = str_replace('{' . $key_ . '}', $assign_lang_vars_, $help_ary[1]);
					}
					foreach($hard_bbcodes AS $hard_bbcode => $html_replacement)
					{
						$help_ary[1] = str_replace($hard_bbcode, $html_replacement, $help_ary[1]);
					}
					if (!$row && !empty($help_ary[1]) && strpos($help_ary[1], '[adminkey]') !== false)
					{
						//User has no permission to view reserved "part of code"
						$help_ary[1] = preg_replace('#(' . preg_quote('[adminkey]') . '(.*?)' . preg_quote('[/adminkey]') . ')#uise', '', $help_ary[1]);
					}
					else
					{
						$help_ary[1] = str_ireplace(array('[adminkey]', '[/adminkey]'), array('', ''), $help_ary[1]);
					}
					$help_ary[1] = str_replace("\n\n", "", $help_ary[1]);
					$help_ary[1] = str_replace("\t\t\t", "", $help_ary[1]);
					$uid = $bitfield = $flags = '';
					generate_text_for_storage($help_ary[1], $uid, $bitfield, $flags, true, false, true);
					$help_ary[1] = generate_text_for_display($help_ary[1], $uid, $bitfield, $flags);
					if ($help_ary[0] == '--')
					{
						if ($help_ary[1] == '--')
						{
							$switch_column = true;
							$found_switch = true;
							continue;
						}

						$template->assign_block_vars('kb_block', array(
							'BLOCK_TITLE'		=> $help_ary[1],
							'SWITCH_COLUMN'		=> $switch_column,
						));

						if ($switch_column)
						{
							$switch_column = false;
						}
						continue;
					}
					//Inject hooks knowledge
					if ($help_ary[0] == '--HOOKS--')
					{
						if (empty($user->lang['UCP_PHPBB_API_KNOWLEDGE_BASE_HOOKS']))
						{
							continue;
						}
						$template->assign_block_vars('kb_block', array(
							'BLOCK_TITLE'		=> $help_ary[1],
							'SWITCH_COLUMN'		=> $switch_column,
						));

						foreach (array_unique($user->lang['UCP_PHPBB_API_KNOWLEDGE_BASE_HOOKS'], SORT_REGULAR) AS $help_ary)
						{
							if (!$row && !empty($help_ary['a_']))
							{
								//User has no permission to view administrator manual
								continue;
							}
							if (!empty($help_ary['method']))
							{
								foreach (explode(',', $help_ary['method']) AS $method_)
								{
									if (isset($user->lang['API_TRANSLATED_METHOD'][$method_]))
									{
										$method .= $user->lang('UCP_PHPBB_API_WITH', $method_, $user->lang['API_TRANSLATED_METHOD'][$method_]) . ', ';
									}
									else
									{
										$method .= $user->lang('UCP_PHPBB_API_WITH', $method_, $method_) . ', ';
									}
								}
								$help_ary[1] = str_replace('{METHOD}', $user->lang['API_TRANSLATED_METHOD'][$method_], $help_ary[1]);
								unset($method_);
							}
							foreach ($assign_lang_vars AS $key_ => $assign_lang_vars_)
							{
								$help_ary[1] = str_replace('{' . $key_ . '}', $assign_lang_vars_, $help_ary[1]);
							}
							foreach($hard_bbcodes AS $hard_bbcode => $html_replacement)
							{
								$help_ary[1] = str_replace($hard_bbcode, $html_replacement, $help_ary[1]);
							}
							if (!$row && !empty($help_ary[1]) && strpos($help_ary[1], '[adminkey]') !== false)
							{
								//User has no permission to view reserved "part of code"
								$help_ary[1] = preg_replace('#(' . preg_quote('[adminkey]') . '(.*?)' . preg_quote('[/adminkey]') . ')#uise', '', $help_ary[1]);
							}
							else
							{
								$help_ary[1] = str_ireplace(array('[adminkey]', '[/adminkey]'), array('', ''), $help_ary[1]);
							}
							$help_ary[1] = str_replace("\n\n", "", $help_ary[1]);
							$help_ary[1] = str_replace("\t\t\t", "", $help_ary[1]);
							$uid = $bitfield = $flags = '';
							generate_text_for_storage($help_ary[1], $uid, $bitfield, $flags, true, false, true);
							$help_ary[1] = generate_text_for_display($help_ary[1], $uid, $bitfield, $flags);
							$template->assign_block_vars('kb_block.kb_row', array(
								'KB_QUESTION'	=> $help_ary[0],
								'KB_ANSWER'		=> $help_ary[1],
								'KB_ADMIN'		=> !empty($help_ary['a_']) ? true : false,
								'KB_METHOD'		=> !empty($method) ? $method : false,
							));
							//reset it
							$method = null;
						}
						continue;
					}
					$template->assign_block_vars('kb_block.kb_row', array(
						'KB_QUESTION'	=> $help_ary[0],
						'KB_ANSWER'		=> $help_ary[1],
						'KB_ADMIN'		=> !empty($help_ary['a_']) ? true : false,
						'KB_METHOD'		=> !empty($method) ? $method : false,
					));
					//reset it
					$method = null;
				}
				$template->assign_vars(array(
					'SWITCH_COLUMN_MANUALLY'	=> (!$found_switch) ? true : false,
				));
			break;
		}
		//Extra security control point
		if (!$key_allowed && !empty($key_exists))
		{
			phpbb_api\functions\set_no_cache_headers();
			redirect(append_sid($phpbb_root_path . 'ucp.' . $phpEx, 'i=phpbb_api&amp;mode=keys'));
		}
		$this->key_selector(false, $uncensored);
		$l_mode = strtoupper($mode);
		$template->assign_vars(array(
			'L_TITLE'				=> $user->lang['UCP_PHPBB_API_' . $l_mode],
			'S_RETURN_PAGE'			=> $user->lang('RETURN_PAGE', '<a class="left" href="' . append_sid($phpbb_root_path . 'ucp.' . $phpEx, 'i=phpbb_api&amp;mode=keys') . '">', '</a>'),
			'S_MULTI_COLUMN'		=> isset($config['api_mod_faq_multi_column']) ? $config['api_mod_faq_multi_column'] : false,
			'S_KEY_EXISTS'			=> !empty($key_exists) ? true : false,
			'S_HIDDEN_FIELDS'		=> $s_hidden_fields,
			'S_UCP_ACTION'			=> $this->u_action,
			'S_UCP_SELECTOR_ACTION'	=> append_sid($phpbb_root_path . 'ucp.' . $phpEx, 'i=phpbb_api&amp;mode=' . $mode),
			'S_UNCENSORED'			=> $uncensored,
		));

		$this->tpl_name = 'api/ucp_api_' . $mode;
		$this->page_title = 'UCP_PHPBB_API_' . $l_mode;
	}

	function key_selector($key_id = false, $uncensored = false)
	{
		global $db, $config, $template, $user, $phpbb_root_path, $phpEx, $auth;
		$key_id = request_var('key_id', '');
		if ($key_id && $config['api_mod_ucp_crypt'])
		{
			$key_id = '$H$' . $key_id;
		}
		$key_selector = '';
		if (!defined('API_CONST_LOADED'))
		{
			include(API_ROOT_PATH . 'constants.' . $phpEx);
			include(API_ROOT_PATH . 'functions.' . $phpEx);
		}
		$status_color = array(
			API_STATUS_ACTIVE			=> 'green',
			API_STATUS_SUSPENDED		=> 'red',
			API_STATUS_DEACTIVATED		=> 'grey',
		);
		if ($auth->acl_get('a_phpbb_api_keys'))
		{
			$where_sql = 'WHERE user_id = ' . (int) $user->data['user_id'];
		}
		else
		{
			$where_sql = 'WHERE user_id = ' . (int) $user->data['user_id'] . ' AND key_type = ' . API_TYPE_USER;
		}
		$sql = 'SELECT *
			FROM '  . API_KEYS_TABLE . "
			$where_sql";
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$key_selector .= '<option style="color: ' . $status_color[$row['key_status']] . '" value="' . ($config['api_mod_ucp_crypt'] ? str_replace('$H$', '', phpbb_hash($row['key_id'])) : $row['key_id']) . '"' . (((phpbb_check_hash($row['key_id'], $key_id) && $config['api_mod_ucp_crypt']) xor $row['key_id'] == $key_id) ? 'selected="selected"' : '') . '>' . ($uncensored ? $row['key_id'] : phpbb_api\functions\censor_key($row['key_id'])). '</option>';
		}
		$db->sql_freeresult($result);
		$template->assign_var('S_KEY_SELECTOR', $key_selector);
	}
}
?>