<?php
/**
*
* @package UMIL phpBB API Install file
* @version $Id: phpbb_api_install.php v0.0.1 13h37 03/08/2014 Geolim4 Exp $
* @copyright (c) 2012 Geolim4.com  http://Geolim4.com
* @bug/function request: http://geolim4.com/tracker.php
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/

/**
 * @ignore
 */
define('UMIL_AUTO', true);
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

include($phpbb_root_path . 'common.' . $phpEx);
$user->session_begin();
$auth->acl($user->data);
$user->setup();

if (!file_exists($phpbb_root_path . 'umil/umil_auto.' . $phpEx))
{
	trigger_error('Please download the latest UMIL (Unified MOD Install Library) from: <a href="http://www.phpbb.com/mods/umil/">phpBB.com/mods/umil</a>', E_USER_ERROR);
}

//Hardcoded table constants (cannot call them from namespace in UMIL)
define('API_HISTORY_TABLE',		$table_prefix . 'api_history');
define('API_KEYS_TABLE',		$table_prefix . 'api_keys');
define('API_LOG_TABLE',			$table_prefix . 'api_log');
define('API_LOGIN_ATTEMPTS',	$table_prefix . 'api_login_attempts');

// The name of the mod to be displayed during installation.
$mod_name = 'ACP_PHPBB_API';

/*
* The name of the config variable which will hold the currently installed version
* UMIL will handle checking, setting, and updating the version itself.
*/
$version_config_name = 'api_mod_version';

// The language file which will be included when installing
//We use add_lang() instead of $language_file, cuz we need the immediate language availability (ReflectionClass, php version etc...)!!
//$language_file = 'mods/phpbb_api';
$user->add_lang(array('mods/phpbb_api', 'mods/info_ucp_phpbb_api', 'mods/info_acp_phpbb_api'));

/*
* Optionally we may specify our own logo image to show in the upper corner instead of the default logo.
* $phpbb_root_path will get prepended to the path specified
* Image height should be 50px to prevent cut-off or stretching.
*/
$logo_img = 'images/api_config.png';

/*
* The array of versions and actions within each.
* You do not need to order it a specific way (it will be sorted automatically), however, you must enter every version, even if no actions are done for it.
*
* You must use correct version numbering.  Unless you know exactly what you can use, only use X.X.X (replacing X with an integer).
* The version numbering must otherwise be compatible with the version_compare function - http://php.net/manual/en/function.version-compare.php
*/

//Check for php version
define('API_TARGET_PHP_VERSION', '5.4.10');//5.4.10 at least!
$php_v_required = phpbb_version_compare(PHP_VERSION, API_TARGET_PHP_VERSION, '>=');
$pchart = file_exists($phpbb_root_path . "includes/api/pchart/class/pChart.phpbb." . $phpEx) ? true : false;
$reflection = extension_loaded('reflection') ? true : false;
$zip = extension_loaded('zip') ? true : false;
$curl = extension_loaded('curl') ? true : false;
$mcrypt = extension_loaded('mcrypt') ? true : false;

if (!$php_v_required || !$reflection || !$zip || !$curl /*|| !$mcrypt*/)//Mcrypt extension shouldn't be a requirement
{
	$suitable = false;
}
else
{
	$suitable = true;
}
// Options to display to the user
$options = array(
	'legend2'		=> 'INFORMATION',
	'pchart'		=> array('lang' => 'ACP_PHPBB_API_CONFIG_UMIL_PCHART', 'type' => 'custom', 'function' => 'display_status', 'params' => array($user->lang('ACP_PHPBB_API_CONFIG_UMIL_PCHART_' . ($pchart ? 'OK' : 'NO')), ($pchart ? 'success' : 'error')), 'explain' => false),
	'php_zip'		=> array('lang' => 'ACP_PHPBB_API_CONFIG_UMIL_ZIP', 'type' => 'custom', 'function' => 'display_status', 'params' => array($user->lang('ACP_PHPBB_API_CONFIG_UMIL_ZIP_' . ($zip ? 'OK' : 'NO')), ($zip ? 'success' : 'error')), 'explain' => false),
	'php_curl'		=> array('lang' => 'ACP_PHPBB_API_CONFIG_UMIL_CURL', 'type' => 'custom', 'function' => 'display_status', 'params' => array($user->lang('ACP_PHPBB_API_CONFIG_UMIL_CURL_' . ($curl ? 'OK' : 'NO')), ($curl ? 'success' : 'error')), 'explain' => false),
	'reflection'	=> array('lang' => 'ACP_PHPBB_API_CONFIG_UMIL_REFLECTION', 'type' => 'custom', 'function' => 'display_status', 'params' => array($user->lang('ACP_PHPBB_API_CONFIG_UMIL_REFLECTION_' . ($reflection ? 'OK' : 'NO')), ($reflection ? 'success' : 'error')), 'explain' => false),
	'mcrypt'		=> array('lang' => 'ACP_PHPBB_API_CONFIG_UMIL_MCRYPT', 'type' => 'custom', 'function' => 'display_status', 'params' => array($user->lang('ACP_PHPBB_API_CONFIG_UMIL_MCRYPT_' . ($mcrypt ? 'OK' : 'NO')), ($mcrypt ? 'success' : 'error')), 'explain' => false),
	'php_version'	=> array('lang' => 'ACP_PHPBB_API_CONFIG_UMIL_PHP', 'type' => 'custom', 'function' => 'display_status', 'params' => array($user->lang('ACP_PHPBB_API_CONFIG_UMIL_PHP_' . ($php_v_required ? 'OK' : 'NO'), API_TARGET_PHP_VERSION), ($php_v_required ? 'success' : 'error')), 'explain' => false),
	'displayer'		=> array('lang' => '', 'type' => 'custom', 'function' => 'submit_status', 'explain' => false),
);
/**
* Display a message with a specified css class
*
* @param string		$lang_string	The language string to display
* @param string		$class			The css class to apply
* @return string					Formated html code
**/
function display_status($lang_string, $class)
{
	global $user;

	$lang_string = isset($user->lang[$lang_string]) ? $user->lang[$lang_string] : $lang_string;
	return '<span class="' . $class . '">' . $lang_string . '</span>';
}

/**
* Display (or not) the submit/reset button
*
* @noparam
**/
function submit_status()
{
	global $suitable;

	//Yeah it's horrible to use javascript, but UMIL does not offert the possibility to "hide" the submit button :'(
	return '
	<script type="text/javascript">
	// <![CDATA[
		window.onload = function(){' . ($suitable ? '' : 'dE("submit", 0, "hide");dE("reset", 0, "hide");') . '
			var thisScript = document.thisScript || document.scripts[document.scripts.length - 1];
			thisScript = thisScript.parentNode.parentNode;
			thisScript.parentNode.removeChild(thisScript);
		};
	// ]]>
	</script>';
}

$versions = array(
	'0.0.1' => array(
		'custom' => 'create_api_group',
		'permission_add' => array(
			//ACP acl
			array('a_phpbb_api_config', true),
			array('a_phpbb_api_hooks', true),
			array('a_phpbb_api_keys', true),
			array('a_phpbb_api_logs', true),
			array('a_phpbb_api_stats', true),
			//UCP acl
			array('u_phpbb_api_history', true),
			array('u_phpbb_api_ignore_day', true),
			array('u_phpbb_api_ignore_max', true),
			array('u_phpbb_api_ignore_month', true),
			array('u_phpbb_api_ignore_week', true),
			array('u_phpbb_api_ips', true),
			array('u_phpbb_api_regenerate', true),
			array('u_phpbb_api_stats', true),
			array('u_phpbb_api_use', true),
		),	
		'permission_set' => array(
			//UCP ROLES
			array('ROLE_USER_STANDARD', array('u_phpbb_api_history', 'u_phpbb_api_ignore_day', 'u_phpbb_api_ignore_max', 'u_phpbb_api_ignore_month', 'u_phpbb_api_ignore_week', 'u_phpbb_api_ips', 'u_phpbb_api_regenerate', 'u_phpbb_api_stats', 'u_phpbb_api_use')),
			array('ROLE_USER_FULL', array('u_phpbb_api_history', /*'u_phpbb_api_ignore_day', 'u_phpbb_api_ignore_max', 'u_phpbb_api_ignore_month', 'u_phpbb_api_ignore_week',*/ 'u_phpbb_api_ips', 'u_phpbb_api_regenerate', 'u_phpbb_api_stats', 'u_phpbb_api_use')),
			array('ROLE_ADMIN_FULL', array('a_phpbb_api_config', 'a_phpbb_api_hooks', 'a_phpbb_api_keys', 'a_phpbb_api_logs', 'a_phpbb_api_stats', )),
			array('ROLE_ADMIN_STANDARD', array('a_phpbb_api_config', /*'a_phpbb_api_hooks', 'a_phpbb_api_keys', 'a_phpbb_api_logs',*/ 'a_phpbb_api_stats', )),
		),
		'config_add' => array(
			array('api_mod_enable', 1),
			array('api_mod_ucp_keys', 1),
			array('api_mod_force_logout', 0),
			array('api_mod_max_queries', 100000),
			array('api_mod_mqpd', 100),
			array('api_mod_mqpw', 1000),
			array('api_mod_mqpm', 10000),
			array('api_mod_time_type', /*API_CALENDAR_TIME*/ 1),//No namespace here, magic number required.
			array('api_mod_list_ip', 1),
			array('api_mod_acp_pagination', 10),
			array('api_mod_backtrace', 1),
			array('api_mod_fatal_html', 1),
			array('api_mod_install_age', time()),
			array('api_next_install_check', time()),
			array('api_next_ip_unban', time()),
			array('api_cron_lock', time()),
			array('api_mod_query_limit', 30),
			array('api_mod_header', 1),
			array('api_mod_origin_header', 1),
			array('api_mod_ucp_pagination', 25),
			array('api_mod_stat_limit', 75),
			array('api_mod_ucp_crypt', 1),
			array('api_mod_db_credentials', 1),
			array('api_mod_purge_temp', 0),
			array('api_mod_purge_api', 0),
			array('api_mod_pchart_header', 'cGhwQkIgQVBJIHYwLjAuMSAoQykgR2V'),
			array('api_mod_deactivated_methods', 'get_config'),
			array('api_mod_force_ssl', 0, true),//This config var is dynamic
			array('api_mod_cron_task', 1),
			array('api_mod_wildcard_char', '-'),
			array('api_mod_faq_multi_column', 0),
			array('api_mod_cache_stats', 1),
			array('api_mod_max_attempts', 25),
			array('api_mod_max_attempts_time', 86400),
			array('api_mod_crypto_enabled', 0),
			array('api_mod_whitelist', ''),
		),
		'table_add' => array(
			array(API_HISTORY_TABLE, array(
				'COLUMNS' => array(
					'history_id'		=> array('USINT', NULL, 'auto_increment'),
					'key_id'			=> array('VCHAR:40', ''),
					'time'				=> array('TIMESTAMP'),
					'method'			=> array('VCHAR:40', ''),
					'ip'				=> array('VCHAR:45', ''),
				),
				'PRIMARY_KEY'	=> 'history_id',
			)),
			array(API_KEYS_TABLE, array(
				'COLUMNS' => array(
					'key_id'				=> array('VCHAR:40', ''),//Key ID are hexadecimal([a-z0-9]) format
					'key_secret_key'		=> array('VCHAR:40', ''),
					'user_id'				=> array('UINT:10', ANONYMOUS),
					'key_ips'				=> array('TEXT', ''),
					'key_ips_type'			=> array('UINT:1', 1),
					'key_type'				=> array('UINT:1', 1),
					'key_status'			=> array('UINT:1', 1),
					'gen_source'			=> array('UINT:1', 1),
					'last_querie'			=> array('TIMESTAMP', 0),
					'max_queries_per_day'	=> array('UINT:10', 0),
					'max_queries_per_week'	=> array('UINT:10', 0),
					'max_queries_per_month'	=> array('UINT:10', 0),
					'max_queries'			=> array('UINT:10', 0),
					'queries'				=> array('UINT:10', 0),
					'query_sql'				=> array('UINT:1', 0),
					'query_sql_api'			=> array('TIMESTAMP', 0),
					'creation_time'			=> array('TIMESTAMP', 0),
					'expire_time'			=> array('UINT:10', 0),
					'email_auth'			=> array('UINT:1', 0),
					'force_post'			=> array('UINT:1', 0),
					'deactivated_methods'	=> array('VCHAR:255', ''),
				),
				'PRIMARY_KEY'	=> 'key_id',
			)),
			array(API_LOG_TABLE, array(
				'COLUMNS' => array(
					'log_id'		=> array('USINT', NULL, 'auto_increment'),
					'log_type'		=> array('UINT:1', 5),
					'user_id'		=> array('UINT:10', ANONYMOUS),
					'key_id'		=> array('VCHAR:40', ''),//Key ID are hexadecimal([a-z0-9]) format
					'log_ip'		=> array('VCHAR:45', ''),
					'log_time'		=> array('TIMESTAMP'),
					'log_operation'	=> array('VCHAR:100', ''),
					'log_data'	=> array('TEXT', ''),
				),
				'PRIMARY_KEY'	=> 'log_id',
			)),
			array(API_LOGIN_ATTEMPTS, array(
				'COLUMNS' => array(
					'attempt_id'			=> array('USINT', NULL, 'auto_increment'),
					'attempt_ip'			=> array('VCHAR:45', ''),
					'attempt_browser'		=> array('VCHAR:150', ''),
					'attempt_forwarded_for'	=> array('VCHAR:255', ''),
					'attempt_time'			=> array('TIMESTAMP'),
				),
			)),
		),
	//ACP Module
		'module_add' => array(
			// Add the ACP API Category, placed under the System Tab
			array('acp', 'ACP_CAT_SYSTEM', 'ACP_PHPBB_API'),

			//Add ACP API sub-modules
			array('acp', 'ACP_PHPBB_API', array(
				'module_basename' => 'phpbb_api',
				'module_langname' => 'ACP_PHPBB_API_CONFIG',
				'module_mode'	=> 'config',
				'module_auth' => 'acl_a_phpbb_api_config',
			)),
			array('acp', 'ACP_PHPBB_API', array(
				'module_basename' => 'phpbb_api',
				'module_langname' => 'ACP_PHPBB_API_KEYS',
				'module_mode'	=> 'keys',
				'module_auth' => 'acl_a_phpbb_api_keys',
			)),
			array('acp', 'ACP_PHPBB_API', array(
				'module_basename' => 'phpbb_api',
				'module_langname' => 'ACP_PHPBB_API_LOGS',
				'module_mode'	=> 'logs',
				'module_auth' => 'acl_a_phpbb_api_logs',
			)),
			array('acp', 'ACP_PHPBB_API', array(
				'module_basename' => 'phpbb_api',
				'module_langname' => 'ACP_PHPBB_API_ERR_LOGS',
				'module_mode'	=> 'err_logs',
				'module_auth' => 'acl_a_phpbb_api_logs',
			)),
			array('acp', 'ACP_PHPBB_API', array(
				'module_basename' => 'phpbb_api',
				'module_langname' => 'ACP_PHPBB_API_HOOKS',
				'module_mode'	=> 'hooks',
				'module_auth' => 'acl_a_phpbb_api_hooks',
			)),
			array('acp', 'ACP_PHPBB_API', array(
				'module_basename' => 'phpbb_api',
				'module_langname' => 'ACP_PHPBB_API_STATS',
				'module_mode'	=> 'stats',
				'module_auth' => 'acl_a_phpbb_api_stats',
			)),

			// Add the UCP API Category, pas a new tab
			array('ucp', 0, 'UCP_PHPBB_API'),

			//Add UCP API sub-modules
			array('ucp', 'UCP_PHPBB_API', array(
				'module_basename' => 'phpbb_api',
				'module_langname' => 'UCP_PHPBB_API_KEYS',
				'module_mode'	=> 'keys',
				'module_auth' => 'cfg_api_mod_enable && cfg_api_mod_ucp_keys && acl_u_phpbb_api_use',
			)),
			array('ucp', 'UCP_PHPBB_API', array(
				'module_basename' => 'phpbb_api',
				'module_langname' => 'UCP_PHPBB_API_STATS',
				'module_mode'	=> 'stats',
				'module_auth' => 'cfg_api_mod_enable && cfg_api_mod_ucp_keys && acl_u_phpbb_api_use && acl_u_phpbb_api_stats',
			)),
			array('ucp', 'UCP_PHPBB_API', array(
				'module_basename' => 'phpbb_api',
				'module_langname' => 'UCP_PHPBB_API_HISTORY',
				'module_mode'	=> 'history',
				'module_auth' => 'cfg_api_mod_enable && cfg_api_mod_ucp_keys && acl_u_phpbb_api_use && acl_u_phpbb_api_history',
			)),
			array('ucp', 'UCP_PHPBB_API', array(
				'module_basename' => 'phpbb_api',
				'module_langname' => 'UCP_PHPBB_API_KB',
				'module_mode'	=> 'kb',
				'module_auth' => 'cfg_api_mod_enable && cfg_api_mod_ucp_keys && acl_u_phpbb_api_use',
			)),
		),
		'cache_purge' => array(''),
	),
);

// Include the UMIL Auto file, it handles the rest
include($phpbb_root_path . 'umil/umil_auto.' . $phpEx);

/**
* create_api_group()
* Create the API MANAGER group
* @param string $action The action (install|update|uninstall) will be sent through this.
* @param string $version The version this is being run for will be sent through this.
*/
function create_api_group($action, $version)
{
	global $db, $user, $phpbb_root_path, $phpEx;
	include($phpbb_root_path . 'includes/functions_user.' . $phpEx);

	switch($action)
	{
		case 'install':
			$group_attributes = array(
				'group_founder_manage'	=> true,
				'group_skip_auth'		=> false,
				'group_display'			=> false,
				'group_desc'			=> '',
				'group_desc'			=> '',
				'group_avatar'			=> '',
				'group_avatar_type'		=> 0,
				'group_avatar_width'	=> 0,
				'group_avatar_height'	=> 0,
				'group_rank'			=> '',
				'group_colour'			=> 'FF6600',
				'group_sig_chars'		=> 0,
				'group_receive_pm'		=> true,
				'group_message_limit'	=> 0,
				'group_max_recipients'	=> 0,
				'group_legend'			=> true,
			);
			group_create($group_id, GROUP_SPECIAL, 'API_MANAGER', '', $group_attributes, false, false, false);
			
			if ($group_id)
			{
				group_user_add($group_id, array($user->data['user_id']),  false, false, false, true, false, false);
			}
		break;

		case 'uninstall':
			group_delete(false, 'API_MANAGER');
		break;
	}
}

/****
* php_is_up_to_date()
* Check required PHP version... before includes.
* @noparam
****/
function php_is_up_to_date()
{
	global $phpbb_root_path, $phpEx, $user;
	$api_target_php_version = API_TARGET_PHP_VERSION;
	if (!defined('PHP_VERSION_ID'))
	{
		$version = explode('.', PHP_VERSION);
		define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
	}
	if (PHP_VERSION_ID < $api_target_php_version)
	{
		return $user->lang('API_ERROR_PHP_VERSION', substr($api_target_php_version, 0, 1) . '.' . (int) substr($api_target_php_version, 1, 2) . '.' . (int) substr($api_target_php_version, 3, 5), PHP_VERSION);
	}
	else
	{
		return true;
	}
}