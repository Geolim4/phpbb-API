<?php
/**
*
* @package language [English] phpBB API (phpBB Permission Set)
^>@version $Id: permissions_phpbb_api.php v0.0.1 13h37 03/08/2014 Geolim4 Exp $
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

/**
*	MODDERS PLEASE NOTE
*	
*	You are able to put your permission sets into a separate file too by
*	prefixing the new file with permissions_ and putting it into the acp
*	language folder.
*
*	An example of how the file could look like:
*
*	<code>
*
*	if (empty($lang) || !is_array($lang))
*	{
*		$lang = array();
*	}
*
*	// Adding new category
*	$lang['permission_cat']['bugs'] = 'Bugs';
*
*	// Adding new permission set
*	$lang['permission_type']['bug_'] = 'Bug Permissions';
*
*	// Adding the permissions
*	$lang = array_merge($lang, array(
*		'acl_bug_view'		=> array('lang' => 'Can view bug reports', 'cat' => 'bugs'),
*		'acl_bug_post'		=> array('lang' => 'Can post bugs', 'cat' => 'post'), // Using a phpBB category here
*	));
*
*	</code>
*/

$lang['permission_cat']['api'] = 'API';
$lang = array_merge($lang, array(
	//ACP acl
	'acl_a_phpbb_api_config'		=> array('lang' => 'Can manage API configuration', 'cat' => 'api'),
	'acl_a_phpbb_api_hooks'			=> array('lang' => 'Can manage API hooks, including installation, uninstall, and hooks deletion.<br /><em>The hooks may allow execution of arbitrary code, so you must grant this permission only to trusted users!</em>', 'cat' => 'api'),	
	'acl_a_phpbb_api_keys'			=> array('lang' => 'Can manage the keys of API, including Administrators keys', 'cat' => 'api'),	
	'acl_a_phpbb_api_logs'			=> array('lang' => 'Can manage API logs', 'cat' => 'api'),
	'acl_a_phpbb_api_stats'			=> array('lang' => 'Can manage API statistics', 'cat' => 'api'),
	//UCP acl
	'acl_u_phpbb_api_history'		=> array('lang' => 'Can view the key use history', 'cat' => 'api'),
	'acl_u_phpbb_api_ignore_day'	=> array('lang' => 'Can ignore the daily request limit', 'cat' => 'api'),
	'acl_u_phpbb_api_ignore_max'	=> array('lang' => 'Can ignore the maximum request limit', 'cat' => 'api'),	
	'acl_u_phpbb_api_ignore_month'	=> array('lang' => 'Can ignore the monthly request limit', 'cat' => 'api'),
	'acl_u_phpbb_api_ignore_week'	=> array('lang' => 'Can ignore the weekly request limit', 'cat' => 'api'),
	'acl_u_phpbb_api_ips'			=> array('lang' => 'Can set allowed/disallowed IPs to use the key', 'cat' => 'api'),	// à mettre en concordance avec la fr
	'acl_u_phpbb_api_regenerate'	=> array('lang' => 'Can regenerate a new key when the current key is expired or has reached its maximum quota of request', 'cat' => 'api'),
	'acl_u_phpbb_api_stats'			=> array('lang' => 'Can view the key statistics', 'cat' => 'api'),
	'acl_u_phpbb_api_use'			=> array('lang' => 'Can use the API', 'cat' => 'api'),	
	
));
?>