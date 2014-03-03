<?php
/**
*
* @package phpBB API hook
^>@version $Id: hook_display_html.php v0.0.1 00h11 12/20/2013 Geolim4 Exp $
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
if (!defined(__NAMESPACE__ . '\DISPLAY_HTML_API_TARGET_VERSION'))
{
	//Define hook version here (MANDATORY)
	define(__NAMESPACE__ . '\DISPLAY_HTML_API_TARGET_VERSION', '0.0.1');
	//Add your custom lang file with this hook, it will handled into the API core.
	//You can pass it as a string (array is unsupported) as you can see below
	$add_hook_lang = 'mods/hooks/info_acp_hook_display_html';
	//Set a template name here if your hook use it!
	//You can get it using later in your main function using $mom->template_content
	$add_hook_tpl = 'hook_api_display.' . API_HTML_EXT;

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
			//'vchecker'=> array('yourwebsite.com', '/subpath', 'display_html.txt'),
			'download'	=> 'http://geolim4.com/api-hooks.html?h=hook_display_html',
			'website'	=> 'http://geolim4.com',
			'author'	=> 'Geolim4',
			'version'	=> DISPLAY_HTML_API_TARGET_VERSION,
			'name'		=> 'ACP_API_MANAGE_HOOK_DISPLAY_HTML',//Hey hook, what is your name?
			'date'		=> array('2/15/2013 22:12', '%m/%d/%Y %H:%M'),//Will be passed into strptime()
		);
		return;
	}

	function hook_display_html($array, api_master $api)
	{
		header('Content-Type: text/html; charset=UTF-8');
		return(array_to_html($array, $api));
	}

	function array_to_html($array, api_master $api)
	{
		$str = $api->template_content;
		\phpbb_api\functions\array_keys_stringify($array, $api->user->lang['API_ITEM_KEYWORD']);
		array_walk_html($array, $main_content, array('<ul>', '</ul>'), $api->user->lang['ACP_API_OUTPUT_HTML_ROOT']);
		$vars = array(
			'USER_LANG' => $api->user->data['user_lang'],
			'MAIN_CONTENT' => $main_content,
			'METHOD_NAME' => $api->api_action_translated,
		);
		$str = @preg_replace_callback('#\{([A-Z0-9_]*)\}#', 
			function($match) use ($vars, $api)
			{
				if (isset($vars[$match[1]]) )
				{
					return $vars[$match[1]];
				}
				else
				{
					if (substr($match[1], 0, 2) == 'L_')
					{
						if (isset($api->user->lang[substr($match[1], 2)]) )
						{
							return $api->user->lang[substr($match[1], 2)];
						}
						return substr($match[1], 2);
					}
					return $match[1];
				}
			},
			$str
		);
		return $str;
	}

	function array_walk_html($array, &$html = '', $begin, $key = '')
	{
		if (!empty($begin[0]))
		{
			$html .= $begin[0];
		}
		if (is_array($array) )
		{
			$html .= '<li name="' . $key . '"><strong>' .  $key . '</strong><ul>';
		}
		foreach ($array AS $key_ => $value)
		{
			if (is_array($value) )
			{
				array_walk_html($value, $html, array(), $key_);
			}
			else
			{
				$html .= '<li id="uid' . substr(unique_id(), 0, 5) . '" ondblclick="selectCode(this)" name="' . $key_ . '"><strong>' .  $key_ . ':&nbsp;&nbsp;</strong><code>' . $value . '</code></li>';
			}
		}
		if (is_array($array) )
		{
			$html .= '</ul></li>';
		}
		if (!empty($begin[1]))
		{
			$html .= $begin[1];
		}
	}
}