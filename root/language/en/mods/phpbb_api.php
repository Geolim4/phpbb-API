<?php
/**
*
* @package language [English] phpBB API
^>@version $Id: phpbb_api.php v0.0.1 05h12 01/17/2013 Geolim4 Exp $
* @copyright (c) 2012 - 2014 Geolim4.com http://geolim4.com
* @bug/function request: http://geolim4.com/tracker
* @translator papicx 17/01/2014 11h40  version e papicx@phpbb-fr.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
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
// For add Color
//
$lang = array_merge($lang, array(
	'API_CACHED_PURGED'			=> 'The cache has been purged.',
	'API_CRYPTO_PRIVATE'		=> 'Private. See your User Control Panel to get it.',
	'API_DEACTIVATED'			=> 'This key has been deactivated, contact an administrator for more informations.',
	'API_GENERATE_TIME'			=> 'Page generated in %s seconds.',
	'API_LIFETIME'				=> 'Lifetime',
	'API_ITEM_KEYWORD'			=> 'item',//Should uppercase without special chars unless underscore: _
	'API_LOGIN_WAIT'			=> 'Logging in, please wait…',
	'API_NO_RECORD'				=> 'Any record found',
	'API_INCORRECT_DATA'		=> 'Incorrect data send',
	'API_SUCCESS'				=> 'Operation completed successfully',
	'API_SUCCESS_CRON'			=> 'Cron task successfully running, an email has been sent to all founders.',
	'API_SUCCESS_QUERY'			=> 'Query successfully executed: %s',
	'API_SUSPENDED'				=> 'This key has been suspended, contact an administrator for more informations.',
	'API_STATUS_DISABLE'		=> 'Disable',//No special chars(é,à,è)
	'API_STATUS_ENABLE'			=> 'Enable',//No special chars(é,à,è)

	//Here we allow to translate some method we pass through the URL
	//You cannot use é è à ï etc.
	//To translators: You can use only uppercase chars without special chars unless underscore: _
	'API_TRANSLATED_METHOD'		=> array(
		'login'				=> 'login',
		'post'				=> 'post',
		'topic'				=> 'topic',
		'forum'				=> 'forum',
		'group'				=> 'group',
		'perm_ban'			=> 'perm_ban',
		'unban'				=> 'unban',
		'get_bans'			=> 'get_bans',
		'key_options'		=> 'key_options',
		'key_stats'			=> 'key_statistics',
		'sql_query'			=> 'sql_query',
		'get_constants'		=> 'get_constants',
		'refresh_stats'		=> 'refresh_stats',
		'get_config'		=> 'get_config',
		'set_config'		=> 'set_config',
		'board_status'		=> 'board_status',
		'php_configuration'	=> 'php_configuration',
		'search_ip'			=> 'search_ip',
	),
	//Short methods description
	'API_FULL_TRANSLATED_METHOD'		=> array(
		'login'				=> 'Login to account',
		'post'				=> 'Get post data',
		'topic'				=> 'Get topic data',
		'forum'				=> 'Get forum data',
		'group'				=> 'Get group data',
		'perm_ban'			=> 'Permanent banning',
		'unban'				=> 'Unbanning',
		'get_bans'			=> 'Get bans',
		'key_options'		=> 'Get key option',
		'key_stats'			=> 'Get key statistics',
		'sql_query'			=> 'Run SQL query',
		'get_constants'		=> 'Get local constants',
		'refresh_stats'		=> 'Refresh statistics',
		'get_config'		=> 'Get config',
		'set_config'		=> 'Set config',
		'board_status'		=> 'Change board status',
		'php_configuration'	=> 'Get PHP configuration',
		'search_ip'			=> 'Search an IP',
	),

	//Full submethods translation
	'API_FULL_TRANSLATED_SUBMETHOD'		=> array(
		//User table translation
		'user_id'			=> 'User identifier',
		'username'			=> 'Username',
		'username_clean'	=> 'Username clean',
		'user_email'		=> 'User email',
		'user_type'			=> 'User type',
		'user_regdate'		=> 'User registration date (UNIX time)',
		'user_ip'			=> 'User registration IP',
		'user_passchg'		=> 'Last password change (UNIX time)',
		//Topic table translation
		'topic_id'			=> 'Topic identifier',
		'topic_title'		=> 'Topic title',
		'icon_id'			=> 'Icon identifier',//Also used in following table: post_id
		'topic_type'		=> 'Topic type',
		'topic_views'		=> 'Topic views',
		'topic_poster'		=> 'Topic poster',
		//Group table translation
		'group_id'			=> 'Group identifier',
		'group_name'		=> 'Group name',
		'group_desc'		=> 'Group description',
		'group_colour'		=> 'Group colour',
		//Post table translation
		'post_id'			=> 'Post identifier',
		'poster_ip'			=> 'Poster IP',
		'post_time'			=> 'Post time',
		//Forum table translation
		'forum_id'			=> 'Forum identifier',
		//Misc
		'json'				=> 'JSON string',
		'serialize'			=> 'Serialized string (PHP)',
		'ban_id'			=> 'Ban identifier',
		'userid'			=> 'User identifier',
		'email'				=> 'Email',
	),

	'API_UNAUTHORIZED'			=> 'Unallowed API key!',
	'API_UNAUTHORIZED_USER'		=> 'The account attached to this key has been permanently deleted.',
	'API_UNAUTHORIZED_AUTH'		=> 'You do not have the required permissions.',
	'API_UNAUTHORIZED_FN'		=> 'Functionality disabled on this key!',
	'API_UNAUTHORIZED_PVG'		=> 'Denied privilege: %s. Please note that this attempt has been recorded.',
	'API_UNAUTHORIZED_SQL_API'	=> 'Cannot operate on key table and log table due to security restriction of the key.',

	//API errors...
	'API_BAD_EMAIL'					=> 'This key is secured with the email address of his owner. Please refer to the API manual to authenticate with the email address.',
	'API_BAD_IP'					=> 'This key cannot be used with this IP address (your current IP: %s)',
	'API_BAN_REASON'				=> 'You have been permanently banned from this forum by API.',

	'API_ERROR_ATTEMPTS'			=> 'You exceeded the maximum allowed number of login attempts with this IP resulting a temporary ban from the API.',
	'API_ERROR_CRYPTO_DISABLED'		=> 'The cryptographic functionality has been disabled by an Administrator.',
	'API_ERROR_DEACTIVATED_METHOD'	=> 'That method has been deactivated on this key, contact an administrator for more informations.',
	'API_ERROR_EXCEEDED'			=> 'You have exceeded your maximum quota of queries on this key (%s)',
	'API_ERROR_EXPIRED'				=> 'This key is expired since %s',
	'API_ERROR_HOOK_OUTDATED'		=> 'This hook is outdated, last version of the API: %1$s, hook version: %2$s',
	'API_ERROR_HOOK_NOCONST'		=> 'Missing update constant: %s',
	'API_ERROR_INTERNAL'			=> 'Message: %1$s File: %2$s Line: %3$s ',//That key is used to fill the physical log
	'API_ERROR_METHOD'				=> 'Critical error: Method "%s" not found ! The name of the method can vary depending the language set in the account attached to your key.',
	'API_ERROR_METHOD_DISPLAY'		=> 'Output format « %s » not found !',
	'API_ERROR_METHOD_REQUEST'		=> 'Cannot proceed to the request as in %s mode',
	'API_ERROR_DISABLED'			=> 'The API has been disabled by an administrator.',
	'API_ERROR_NO_SSL'				=> 'The request must be re-addressed using the SSL protocol',
	'API_ERROR_NO_METHOD'			=> 'No method selected!',
	'API_ERROR_NO_SUBMETHOD'		=> 'No sub-method selected!',
	'API_ERROR_PER_DAY'				=> 'You have exceeded your maximum quota of queries per day (%s)',
	'API_ERROR_PER_MONTH'			=> 'You have exceeded your maximum quota of queries per month (%s)',
	'API_ERROR_PER_WEEK'			=> 'You have exceeded your maximum quota of queries per week (%s)',
	'API_ERROR_PHP_VERSION'			=> 'The API require PHP %1$s or later, your PHP version is %2$s',
	'API_ERROR_TYPE'				=> 'Unknown SQL column "%s" in table "%s"',

	'API_FATAL_ERROR'				=> 'Fatal error',
	'API_FATAL_ERROR_DATE'			=> 'Date',
	'API_FATAL_ERROR_FILE'			=> 'File',
	'API_FATAL_ERROR_INTERNAL'		=> '[ %1$s ] File: %2$s, Line: %3$s : Message: %4$s',
	'API_FATAL_ERROR_LINE'			=> 'Line',
	'API_FATAL_ERROR_MSG'			=> 'Message',
	'API_FATAL_ERROR_PHP_VERSION'	=> 'PHP version',
	'API_FATAL_ERROR_REQUEST'		=> 'Request method',
	'API_FATAL_ERROR_SERVER'		=> 'Server',
	'API_FATAL_ERROR_TEXT_EXP'		=>'That page is a Fatal Error handled by phpBB API v%s.
					<br />Usually this page should never appear, you have to search and fix that error quickly.
					<br />If you can’t do it, contact the API developer <a href="http://geolim4.com/tracker.php" title="phpBB API tracker">here</a>.',
	'API_FATAL_ERROR_TITLE_EXP'		=> 'Explanations',
	'API_FATAL_ERROR_TYPE'			=> 'Type',
	'API_STATS_DAY_FMT'				=> 'F l j Y',//April 7th Thursday 2013
));

?>