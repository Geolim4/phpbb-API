<?php
/**
*
* @package phpBB3 API base common fork
^>@version $Id: common.php v0.0.1 13h37 03/08/2014 Geolim4 Exp $
* @copyright (c) 2012 - 2014 Geolim4.com http://geolim4.com
* @bug/function request: http://geolim4.com/tracker
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
* Minimum Requirement: PHP 5.4.1
*/

/**
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

require($phpbb_root_path . 'includes/startup.' . $phpEx);

if (file_exists($phpbb_root_path . 'config.' . $phpEx))
{
	require($phpbb_root_path . 'config.' . $phpEx);
}
//Overwrite DB settings to ours
if (file_exists($phpbb_root_path . 'includes/api/config.' . $phpEx))
{
	require($phpbb_root_path . 'includes/api/config.' . $phpEx);
	if (!empty($api_dbuser) && isset($api_dbpasswd))
	{
		$dbuser = $api_dbuser;
		$dbpasswd = $api_dbpasswd;
	}
}

if (!defined('PHPBB_INSTALLED'))
{
	// Redirect the user to the installer
	// We have to generate a full HTTP/1.1 header here since we can't guarantee to have any of the information
	// available as used by the redirect function
	$server_name = (!empty($_SERVER['HTTP_HOST'])) ? strtolower($_SERVER['HTTP_HOST']) : ((!empty($_SERVER['SERVER_NAME'])) ? $_SERVER['SERVER_NAME'] : getenv('SERVER_NAME'));
	$server_port = (!empty($_SERVER['SERVER_PORT'])) ? (int) $_SERVER['SERVER_PORT'] : (int) getenv('SERVER_PORT');
	$secure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 1 : 0;

	$script_name = (!empty($_SERVER['PHP_SELF'])) ? $_SERVER['PHP_SELF'] : getenv('PHP_SELF');
	if (!$script_name)
	{
		$script_name = (!empty($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : getenv('REQUEST_URI');
	}

	// Replace any number of consecutive backslashes and/or slashes with a single slash
	// (could happen on some proxy setups and/or Windows servers)
	$script_path = trim(dirname($script_name)) . '/install/index.' . $phpEx;
	$script_path = preg_replace('#[\\\\/]{2,}#', '/', $script_path);

	$url = (($secure) ? 'https://' : 'http://') . $server_name;

	if ($server_port && (($secure && $server_port <> 443) || (!$secure && $server_port <> 80)))
	{
		// HTTP HOST can carry a port number...
		if (strpos($server_name, ':') === false)
		{
			$url .= ':' . $server_port;
		}
	}

	$url .= $script_path;
	header('Location: ' . $url);
	exit;
}

if (defined('DEBUG_EXTRA'))
{
	$base_memory_usage = 0;
	if (function_exists('memory_get_usage'))
	{
		$base_memory_usage = memory_get_usage();
	}
}

// Load Extensions
// dl() is deprecated and disabled by default as of PHP 5.3.
//... so we removing it as the API require 5.4.1

// Include files
require($phpbb_root_path . 'includes/acm/acm_' . $acm_type . '.' . $phpEx);
require($phpbb_root_path . 'includes/cache.' . $phpEx);
require($phpbb_root_path . 'includes/template.' . $phpEx);
require($phpbb_root_path . 'includes/session.' . $phpEx);
require($phpbb_root_path . 'includes/auth.' . $phpEx);

require($phpbb_root_path . 'includes/functions.' . $phpEx);
require($phpbb_root_path . 'includes/functions_content.' . $phpEx);

require($phpbb_root_path . 'includes/constants.' . $phpEx);
require($phpbb_root_path . 'includes/db/' . $dbms . '.' . $phpEx);
require($phpbb_root_path . 'includes/utf/utf_tools.' . $phpEx);
require($phpbb_root_path . 'includes/db/db_tools.' . $phpEx);
require($phpbb_root_path . 'includes/functions_messenger.' . $phpEx);

// Set PHP error handler to ours
// In phpBB API the error handler will be modified later in core_catchable_error.php
$level = E_ALL & ~E_DEPRECATED & ~E_STRICT;
set_error_handler('api_common_error_handler', $level);

// Instantiate some basic classes
$user		= new user();
$auth		= new auth();
$template	= new template();
$cache		= new cache();
$db			= new $sql_db();

//Hack to handle errors happened before API instantiation
$error_handled = array();

// Connect to DB
$db->sql_connect($dbhost, $dbuser, $dbpasswd, $dbname, $dbport, false, defined('PHPBB_DB_NEW_LINK') ? PHPBB_DB_NEW_LINK : false);

// We do not need this any longer, unset for safety purposes
unset($dbpasswd, $api_dbpasswd);

// Grab global variables, re-cache if necessary
$config = $cache->obtain_config();
//Let's rock to ignore some config entries
$config['check_dnsbl'] = false;
$config['email_check_mx'] = false;
$config['ip_login_limit_max'] = false;
$config['chg_passforce'] = false;

// Add own hook handler if specified in the API
if (defined('LOAD_PHPBB_HOOKS') && LOAD_PHPBB_HOOKS)
{
	require($phpbb_root_path . 'includes/hooks/index.' . $phpEx);
	$phpbb_hook = new phpbb_hook(array('exit_handler', 'phpbb_user_session_handler', 'append_sid', array('template', 'display')));

	foreach ($cache->obtain_hooks() as $hook)
	{
		include($phpbb_root_path . 'includes/hooks/' . $hook . '.' . $phpEx);
	}
}

/****
* php_is_up_to_date()
* Check required PHP version... before includes.
* @noparam
****/
function php_is_up_to_date()
{
	global $phpbb_root_path, $phpEx, $config;
	$api_target_php_version = API_TARGET_PHP_VERSION;
	if (!defined('PHP_VERSION_ID'))
	{
		$version = explode('.', PHP_VERSION);
		define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
	}
	if (PHP_VERSION_ID < $api_target_php_version)
	{
		//$user is not initialized yet...
		include($phpbb_root_path . 'language/' . $config['default_lang'] .  '/mods/phpbb_api.' . $phpEx);

		$msg = sprintf($lang['API_ERROR_PHP_VERSION'], substr($api_target_php_version, 0, 1) . '.' . (int) substr($api_target_php_version, 1, 2) . '.' . (int) substr($api_target_php_version, 3, 5), PHP_VERSION);
		$result = array(
			'msg' => $msg,
			'err_level' => E_USER_ERROR,
		);

		header('Content-Type: application/json; charset=UTF-8');
		echo(json_encode($result, JSON_FORCE_OBJECT));

		garbage_collection();
		exit_handler();
	}
	return true;
}

/****
* api_common_error_handler()
* Set a neutral error handler awaiting
* @noparam
****/
function api_common_error_handler($errno, $errstr, $errfile, $errline)
{
	global $error_handled;

	$error_handled[] = array(
		'errno'		=> $errno,
		'errstr'	=> $errstr,
		'errfile'	=> $errfile,
		'errline'	=> $errline,
	);
}