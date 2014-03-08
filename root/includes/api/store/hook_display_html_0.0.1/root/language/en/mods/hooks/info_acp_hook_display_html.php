<?php
/**
*
* @package language [English] phpBB API
* @version $Id: info_acp_hook_display_html.php v0.0.1 13h37 03/08/2014 Geolim4 Exp $
* @copyright (c) 2012 Geolim4.com  http://Geolim4.com
* @bug/function request: http://geolim4.com/tracker.php
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
// Some characters you may want to copy&paste:
// ’ » “ ” …
// Use: <strong style="color:green">Texte</strong>',
//For add Color
//
$lang = array_merge($lang, array(
	'ACP_API_MANAGE_HOOK_DISPLAY_HTML' => 'HTML format output',
	'ACP_API_OUTPUT_HTML_TITLE' => 'API gateway interface',
	'ACP_API_OUTPUT_HTML_ROOT' => 'root',
));

$lang['UCP_PHPBB_API_KNOWLEDGE_BASE_HOOKS'][] = array(
		//'method' => '',//Not translatable
		//'a_'	=> true,//require the user to have at least an administrator key to view that paragraph!
		0 => 'Get data to HTML format',
		1 => 'This extension will allow you to retrieve data in HTML (Unordered list).
			[code=php] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "topic",
		"m" =>  false,
		"t" => "topic_id",
		"s" => "operator:LIKE",
		"d" => "Welcome",
		"o" => "html",
));[/code]'
);