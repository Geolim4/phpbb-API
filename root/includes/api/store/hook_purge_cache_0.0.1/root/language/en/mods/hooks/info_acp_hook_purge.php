<?php
/**
*
* @package language [English] phpBB API
* @version $Id: phpbb_api.php v1.0.0 16:55 02/11/2012 Geolim4 Exp $
* @copyright (c) 2012 Geolim4.com  http://Geolim4.com
* @bug/function request: http://geolim4.com/tracker.php
* @translator papicx 28/11/2013 14h50  version a papicx@phpbb-fr.com
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
// ’ « » “ ” …
// Use: <strong style="color:green">Texte</strong>',
//For add Color
//
$lang = array_merge($lang, array(
	'ACP_API_MANAGE_HOOK_PURGE'		=> 'Cache purging',
));

$lang['API_FULL_TRANSLATED_METHOD']	+= array(
		'purge'			=> 'Cache purge',
);
$lang['API_TRANSLATED_METHOD']	+= array(
		'purge'			=> 'purge',
);
$lang['UCP_PHPBB_API_KNOWLEDGE_BASE_HOOKS'][] = array(
		//'cfg' => 'api_config_name',////Require that the specified config must be true to show that part
		'method' => 'purge',//Automatically translated
		'a_'	=> true,//require the user to have at least an administrator key to view that part!
		0 => 'Purge the cache',
		1 => 'You can quickly purge the cache using the [i]{METHOD}[/i] method.
			[code=php] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "{METHOD}",
		"m" => false,
		"t" => "all",
		"s" => "",
		"d" => "",
		"o" => "json",
));[/code]'
);