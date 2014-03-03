<?php
/**
*
* @package phpBB API hook
^>@version $Id: hook_display_raw.php v0.0.1 00h11 12/20/2013 Geolim4 Exp $
* @copyright (c) 2012 - 2014 Geolim4.com http://geolim4.com
* @bug/function request: http://geolim4.com/tracker
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/
namespace phpbb_api\hooks\displays;
use \phpbb_api\api AS api_master;

/**
 * @ignore
 */
if (!defined('IN_PHPBB') || !defined('IN_PHPBB_API'))
{
	exit;
}
if (!defined(__NAMESPACE__ . '\DISPLAY_RAW_API_TARGET_VERSION'))
{
	//Define hook version here (MANDATORY)
	define(__NAMESPACE__ . '\DISPLAY_RAW_API_TARGET_VERSION', '0.0.1');
	//Add your custom lang file with this hook, it will handled into the API core.
	//You can pass it as a string (array is unsupported) as you can see below
	$add_hook_lang = 'mods/hooks/info_acp_hook_display_raw';
	//Set a template name here if your hook use it!
	//You can get it using later in your main function using $mom->template_content
	//$add_hook_tpl = 'template_name.' . API_HTML_EXT;

	//Hook identifier (ACP management)
	if (defined('ADMIN_START'))
	{
		$acp_api_hook_install = function()
		{
			//set_config('phpbb_api_hook', 'Bertie just plastered !!');
		};

		$acp_api_hook_uninstall = function()
		{
/* 			global $db, $cache;

			$sql = 'DELETE 
				FROM ' . CONFIG_TABLE . "
				WHERE config_name = 'phpbb_api_hook'";
			$db->sql_query($sql);
			$cache->purge(); */
		};
		$acp_api_hook_manager = array(
			//in case you want to use your own version checker, uncomment this, and follow instruction here: 
			// https://geolim4.com/centre-de-documentations/phpbb-api-version-checker-instructions-t1763.html
			//'vchecker'=> array('yourwebsite.com', '/subpath', 'display_raw.txt'),
			'download'	=> 'http://geolim4.com/api-hooks.html?h=hook_display_raw',
			'website'	=> 'http://geolim4.com',
			'author'	=> 'Geolim4',
			'version'	=> DISPLAY_RAW_API_TARGET_VERSION,
			'name'		=> 'ACP_API_MANAGE_HOOK_DISPLAY_RAW',//Hey hook, what is your name?
			'date'		=> array('2/15/2013 23:12', '%m/%d/%Y %H:%M'),//Will be passed into strptime()
		);
		return;
	}

	function hook_display_raw($array, api_master $api)
	{
		//$template_content = $api->template_content;
		header('Content-Type: text/plain; charset=UTF-8');
		return print_r($array, true);
	}
}