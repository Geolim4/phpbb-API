<?php
/**
*
* @package phpBB API hook
^>@version $Id: hook_display_ini.php v0.0.1 13h13 10/19/2013 Geolim4 Exp $
* @copyright (c) 2012 - 2014 Geolim4.com http://geolim4.com
* @bug/function request: http://geolim4.com/tracker
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/
namespace phpbb_api\hooks\displays
{
	/**
	 * @ignore
	 */
	if (!defined('IN_PHPBB') || !defined('IN_PHPBB_API') )
	{
		exit;
	}
	if (!defined(__NAMESPACE__ . '\DISPLAY_INI_API_TARGET_VERSION') )
	{
		//Define hook version here (MANDATORY)
		define(__NAMESPACE__ . '\DISPLAY_INI_API_TARGET_VERSION', '0.0.1');
		//Add your custom lang file with this hook, it will handled into the API core.
		//You can pass it as a string (array is unsupported) as you can see below
		$add_hook_lang = 'mods/hooks/info_acp_hook_display_ini';

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
				//'vchecker'=> array('yourwebsite.com', '/subpath', 'display_ini.txt'),
				'download'	=> 'http://geolim4.com/api-hooks.html?h=hook_display_ini',
				'author'	=> 'Geolim4',
				'version'	=> DISPLAY_INI_API_TARGET_VERSION,
				'name'		=> 'ACP_API_MANAGE_HOOK_DISPLAY_INI',//Hey hook, what is your name?
				'date'		=> array('2/15/2013 23:12', '%m/%d/%Y %H:%M'),//Will be passed into strptime()
			);
			return;
		}
		function hook_display_ini($array)
		{
			header('Content-Type: text/plain; charset=UTF-8');
			return(array_to_ini($array));
		}
		function array_to_ini($array, $i = 0)
		{
			$str="";
			foreach ($array as $key => $value)
			{
				if (is_array($value))
				{
					$str.= str_repeat(" ", $i * 2)."[$key]" . PHP_EOL;
					$str.= array_to_ini($value, $i + 1);
				}else
				{
					$str.=str_repeat(" ",$i * 2)."$key = $value" . PHP_EOL;
				}
			}
			return $str;
		}
	}
}


