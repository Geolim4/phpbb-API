<?php
/**
*
* @package API gateway interface
^>@version $Id: api.php v0.0.1 00h11 12/20/2013 Geolim4 Exp $
* @copyright (c) 2012 - 2014 Geolim4.com http://geolim4.com
* @bug/function request: http://geolim4.com/tracker
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/

//Set hard work-time limit
$microtime = microtime();
@set_time_limit(10);//10 seconds is large-acceptable response time delay for an API

//Define routines constants/vars
define('IN_PHPBB', true);
define('IN_PHPBB_API', true);
define('IN_PHPBB_API_CORE', true);
define('LOAD_PHPBB_HOOKS', false);
define('API_TARGET_PHP_VERSION', 50410);//5.4.10 at least!
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

//Call the own API common.php
include($phpbb_root_path . 'includes/api/common.' . $phpEx);

//Does we've the required php version (at this moment the code is still compatible php 4.3.3 -_-)
if (php_is_up_to_date())
{
	//Include required API composants
	require($phpbb_root_path . 'includes/api/constants.' . $phpEx);
	require($phpbb_root_path . 'includes/api/functions.' . $phpEx);
	require($phpbb_root_path . 'includes/api/cache.' . $phpEx);
	require($phpbb_root_path . 'includes/api/core.' . $phpEx);
	require($phpbb_root_path . 'includes/api/core_extended/core_error_catcher.' . $phpEx);

	//Here the bench begin
	$api_bench_start = call_user_func('phpbb_api\functions\api_bench_start');

	// Start session management (Do not update users last page entry)
	$user->session_begin(false);

	// Commented out: will be authenticated by the API key later !!
	//$auth->acl($user->data); 
	//$user->setup();
	$user->update_session_page = false;

	//Grab user vars
	$output			= request_var('o', 'json');		//Intput: json/xml/serialize/ini ...
	$action			= request_var('a', '-');		//Intput: topic/post/user ...
	$multibyte		= request_var('m', false);		//Intput: multibyte
	$type			= request_var('t', '-', (bool) $multibyte);		//Intput: topic_id/post_id/user_id ...
	$key			= request_var('k', '-');		//Intput: the api key
	$key_email		= request_var('e', '-');		//Intput: the api email
	$sql_sorting	= request_var('s', '');			//Intput: start:10/limit:10 ...
	$data			= request_var('d', (isset($_POST['d']) ? (preg_match('/^[0-9]+$/', $_POST['d']) ? 0 : '') : (isset($_GET['d'])) ? (preg_match('/^[0-9]+$/', $_GET['d']) ? 0 : '') : ''), (bool) $multibyte);//Deal with it now!
	$switch_pvg		= request_var('i', false);

	//If multibyte enabled then call utf8_normalize_nfc.
	if ($multibyte)
	{
		$action = utf8_normalize_nfc($action);
		$data = utf8_normalize_nfc($data);
		$type = utf8_normalize_nfc($type);
	}

	//Now, we handle fatal errors using ob_start hack.
	if (function_exists('phpbb_api\error_handling\fatal_api_error_handler'))
	{
		ob_start('phpbb_api\error_handling\fatal_api_error_handler', 0, PHP_OUTPUT_HANDLER_STDFLAGS ^ PHP_OUTPUT_HANDLER_REMOVABLE);
	}

	//Include the constructor, it handles the rest...
	require($phpbb_root_path . 'includes/api/constructor.' . $phpEx);
}
?>