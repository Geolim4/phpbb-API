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
namespace phpbb_api\hooks\displays
{
	/**
	 * @ignore
	 */
	if (!defined('IN_PHPBB') || !defined('IN_PHPBB_API') )
	{
		exit;
	}
	if (!defined(__NAMESPACE__ . '\DISPLAY_HTML_API_TARGET_VERSION') )
	{
		//Define hook version here (MANDATORY)
		define(__NAMESPACE__ . '\DISPLAY_HTML_API_TARGET_VERSION', '0.0.1');
		//Add your custom lang file with this hook, it will handled into the API core.
		//You can pass it as a string (array is unsupported) as you can see below
		$add_hook_lang = 'mods/hooks/info_acp_hook_display_html';

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
				'author'	=> 'Geolim4',
				'version'	=> DISPLAY_HTML_API_TARGET_VERSION,
				'name'		=> 'ACP_API_MANAGE_HOOK_DISPLAY_HTML',//Hey hook, what is your name?
				'date'		=> array('2/15/2013 23:12', '%m/%d/%Y %H:%M'),//Will be passed into strptime()
			);
			return;
		}
		function hook_display_html($array)
		{
			header('Content-Type: text/html; charset=UTF-8');
			return(array_to_html($array));
		}
		function array_to_html($array, $i = 0)
		{
			global $user;
			$str = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="' . $user->data['user_lang'] . '" xml:lang="' . $user->data['user_lang'] . '">';
			$str .= '<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="' . $user->data['user_lang'] . '" />

<title>' . $user->lang['ACP_API_OUTPUT_HTML_TITLE'] . '</title>
<script type="text/javascript">
// <![CDATA[
function selectCode(a)
{
	// Get ID of code block
	var e = a.getElementsByTagName("CODE")[0];

	// Not IE and IE9+
	if (window.getSelection)
	{
		var s = window.getSelection();
		// Safari
		if (s.setBaseAndExtent)
		{
			s.setBaseAndExtent(e, 0, e, e.innerText.length - 1);
		}
		// Firefox and Opera
		else
		{
			// workaround for bug # 42885
			if (window.opera && e.innerHTML.substring(e.innerHTML.length - 4) == "<BR>")
			{
				e.innerHTML = e.innerHTML + "&nbsp;";
			}

			var r = document.createRange();
			r.selectNodeContents(e);
			s.removeAllRanges();
			s.addRange(r);
		}
	}
	// Some older browsers
	else if (document.getSelection)
	{
		var s = document.getSelection();
		var r = document.createRange();
		r.selectNodeContents(e);
		s.removeAllRanges();
		s.addRange(r);
	}
	// IE
	else if (document.selection)
	{
		var r = document.body.createTextRange();
		r.moveToElementText(e);
		r.select();
	}
}
// >
</script>
<style>
ul { 
  margin-left:0em; 
  padding-left:0.2em; 
  margin-bottom:1em; 
}
ul > li{ 
  background:url("data:image/gif;base64,R0lGODlhBAAHAIABAACBy////yH5BAEAAAEALAAAAAAEAAcAAAIIRA4WaeyrVCgAOw==") 0em 0.5em no-repeat; /* change background em accordingly */
  padding-left: 0.8em; 
  list-style: none; 
}
li:nth-of-type(1) { 
	background:url("data:image/gif;base64,R0lGODlhBAAHAIABACQ1Pv///yH5BAEAAAEALAAAAAAEAAcAAAIIRA4WaeyrVCgAOw==") no-repeat; 
  list-style: none; 
}
</style>
<body>';
			\phpbb_api\array_keys_stringify($array);
			$str .= array_walk_html($array, $str, array('<ul>', '</ul>'), $user->lang['ACP_API_OUTPUT_HTML_ROOT']);
			$str .='</body><html>';
			return $str;
		}
		function array_walk_html($array, &$html, $begin, $key = '')
		{
			if(!empty($begin[0]))
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
			if(!empty($begin[1]))
			{
				$html .= $begin[1];
			}
		}
	}
}


