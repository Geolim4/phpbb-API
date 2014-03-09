<?php
/**
*
* @package language [English] phpBB API
^>@version $Id: info_acp_phpbb_api.php v0.0.1 13h37 03/08/2014 Geolim4 Exp $
* @copyright (c) 2012 - 2014 Geolim4.com http://geolim4.com
* @bug/function request: http://geolim4.com/tracker
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
$api_lang_suffix = '';
global $config;
$lang = array_merge($lang, array(
	'G_API_MANAGER'						=> 'API Manager',
//ACP hook management
	//UMIL
	'ACP_PHPBB_API_CONFIG_UMIL_PHP'				=> 'PHP version',
	'ACP_PHPBB_API_CONFIG_UMIL_PHP_OK'			=> 'You have PHP <strong>%s</strong> or higher, you can proceed to the mod install',
	'ACP_PHPBB_API_CONFIG_UMIL_PHP_NO'			=> 'You don’t have PHP <strong>%s</strong> or higher, you cannot proceed to the mod install',
	'ACP_PHPBB_API_CONFIG_UMIL_REFLECTION'		=> 'Reflection Extension',
	'ACP_PHPBB_API_CONFIG_UMIL_REFLECTION_OK'	=> 'The <em><a href="http://php.net/manual/en/book.reflection.php">Reflection</a></em> extension is present.',
	'ACP_PHPBB_API_CONFIG_UMIL_REFLECTION_NO'	=> 'The <em><a href="http://php.net/manual/en/book.reflection.php">Reflection</a></em> extension is missing, contact your hosting provider.',
	'ACP_PHPBB_API_CONFIG_UMIL_PCHART'			=> 'pChart library',
	'ACP_PHPBB_API_CONFIG_UMIL_PCHART_OK'		=> 'pChart library is present.',
	'ACP_PHPBB_API_CONFIG_UMIL_PCHART_NO'		=> 'pChart is missing, statistics will be disabled, <em><a href="http://geolim4.com">more infos</a></em>.',
	'ACP_PHPBB_API_CONFIG_UMIL_ZIP'				=> 'ZIP Extension',
	'ACP_PHPBB_API_CONFIG_UMIL_ZIP_OK'			=> 'The <em><a href="http://php.net/manual/en/book.zip.php">ZIP</a></em> extension is present.',
	'ACP_PHPBB_API_CONFIG_UMIL_ZIP_NO'			=> 'The <em><a href="http://php.net/manual/en/book.zip.php">ZIP</a></em> extension is missing, contact your hosting provider.',
	'ACP_PHPBB_API_CONFIG_UMIL_CURL'			=> 'cURL Extension',
	'ACP_PHPBB_API_CONFIG_UMIL_CURL_OK'			=> 'The <em><a href="http://php.net/manual/en/book.curl.php">cURL</a></em> extension is present.',
	'ACP_PHPBB_API_CONFIG_UMIL_CURL_NO'			=> 'The <em><a href="http://php.net/manual/en/book.curl.php">cURL</a></em> extension is missing, contact your hosting provider.',
	'ACP_PHPBB_API_CONFIG_UMIL_MCRYPT'			=> 'Mcrypt Extension',
	'ACP_PHPBB_API_CONFIG_UMIL_MCRYPT_OK'		=> 'The <em><a href="http://php.net/manual/en/book.mcrypt.php">Mcrypt</a></em> extension is present.',
	'ACP_PHPBB_API_CONFIG_UMIL_MCRYPT_NO'		=> 'The <em><a href="http://php.net/manual/en/book.mcrypt.php">Mcrypt</a></em> extension is missing, you wont be able to use the cryptographie feature, contact your hosting provider if you want to use it.',

	'ACP_PHPBB_API_HOOK_AUTHOR'			=> 'Author',
	'ACP_PHPBB_API_HOOK_DATE'			=> 'Date',
	'ACP_PHPBB_API_HOOK_DELETED'		=> 'The hook %s has been deleted from the server',
	'ACP_PHPBB_API_HOOK_DELETE_ERR'		=> 'Cannot delete an non-uninstalled hook!',
	'ACP_PHPBB_API_HOOK_FILE'			=> 'File',
	'ACP_PHPBB_API_HOOK_INSTALL'		=> 'Install',
	'ACP_PHPBB_API_HOOK_INSTALLED'		=> 'Installed hooks',
	'ACP_PHPBB_API_HOOK_NAME'			=> 'Name',
	'ACP_PHPBB_API_HOOK_NO_UPLOAD'		=> 'No file has been sent, maybe it exceeds the limit allowed by your host: %s MB',
	'ACP_PHPBB_API_HOOK_OFFICIAL'		=> 'Official',
	'ACP_PHPBB_API_HOOK_UNINSTALL'		=> 'Uninstall',
	'ACP_PHPBB_API_HOOK_UNINSTALLED'	=> 'Uninstalled hooks',
	'ACP_PHPBB_API_HOOK_UNOFFICIAL'		=> 'Unofficial',
	'ACP_PHPBB_API_HOOK_VERSION'		=> 'Version',

//API's Stats
	'ACP_PHPBB_API_STATS_ALL_QR'	=> 'Count of queries',
	'ACP_PHPBB_API_STATS_DAY'		=> 'Daily statistics',
	'ACP_PHPBB_API_STATS_DAY_EXP'	=> 'Click on a daily item above for more details.',
	'ACP_PHPBB_API_STATS_DAY_FMT'	=> '{monthstr} {daystr} {dayint}th  {yearint}',//April Thursday 7th 2013// This is not translatable, you can only re-order this depending your language
	'ACP_PHPBB_API_STATS_DAY_REQ'	=> 'Requests/hour',
	'ACP_PHPBB_API_STATS_DAY_IP'	=> 'IPs/hour',
	'ACP_PHPBB_API_STATS_MONTH_IP'	=> 'IPs/day',
	'ACP_PHPBB_API_STATS_YEAR_IP'	=> 'IPs/month',
	'ACP_PHPBB_API_STATS_HISTORY'	=> 'History',
	'ACP_PHPBB_API_STATS_HOUR'		=> 'h\h A',//02h PM http://php.net/manual/en/function.date.php
	'ACP_PHPBB_API_STATS_HOURS'		=> 'Hourly statistics',
	'ACP_PHPBB_API_STATS_HOURS_EXP'	=> 'Click on a hourly item above for more details.',
	'ACP_PHPBB_API_STATS_MONTH'		=> 'Monthly statistics',
	'ACP_PHPBB_API_STATS_MONTH_REQ'	=> 'Requests/day',
	'ACP_PHPBB_API_STATS_TOTAL'		=> '(%s totals requests)',
	'ACP_PHPBB_API_STATS_YEAR'		=> 'Yearly statistics',
	'ACP_PHPBB_API_STATS_YEAR_REQ'	=> 'Requests/month',

//API's Logs
	'API_LOGS_CLEAR'				=> '<strong>Purged API logs</strong><br />» %s deleted messages' . $api_lang_suffix,

	'API_LOG_API_KEY_DEACTIVATED'	=> '<strong>Deactivating own key</strong>' . $api_lang_suffix,
	'API_LOG_API_KEY_OPTION'		=> 'Consulting own key options' . $api_lang_suffix,
	'API_LOG_API_LOGIN_ACCOUNT'		=> 'Logging in account via the API' . $api_lang_suffix,
	'API_LOG_BAD_AUTH_EMAIL'		=> '<strong>Key authentication denied</strong><br />» Email used: %s' . $api_lang_suffix,
	'API_LOG_BAD_AUTH_NO_EMAIL'		=> '<strong>Key authentication denied:</strong><br />» No email entered' . $api_lang_suffix,
	'API_LOG_BAD_AUTH_USER'			=> '<strong>Key authentication denied:</strong><br />» The user attached to the key has been permanently deleted' . $api_lang_suffix,
	'API_LOG_BAD_AUTH_IP'			=> '<strong>Authentication denied:</strong><br />» IP not allowed' . $api_lang_suffix,
	'API_LOG_BAD_AUTH_KEY'			=> '<strong>Authentication failed:</strong><br />» Invalid key' . $api_lang_suffix,
	'API_LOG_BAD_AUTH_OUDATED'		=> '<strong>Authentication denied:</strong><br />» Outdated key' . $api_lang_suffix,
	'API_LOG_BAD_AUTH_OUT_OF_QUOTA'	=> '<strong>Authentication denied:</strong><br />» Reached quota' . $api_lang_suffix,
	'API_LOG_BAD_AUTH_DEACTIVATED'	=> '<strong>Authentication denied:</strong><br />» Deactivated key' . $api_lang_suffix,
	'API_LOG_BAD_AUTH_SUSPENDED'	=> '<strong>Authentication denied:</strong><br />» Suspended key' . $api_lang_suffix,
	'API_LOG_BAN_EMAIL'				=> '<strong>Banned email</strong><br />» %s' . $api_lang_suffix,
	'API_LOG_BAN_IP'				=> '<strong>Banned IP</strong><br />» %s' . $api_lang_suffix,
	'API_LOG_BANNED_IP'				=> '<strong>Authentication denied:</strong><br />» Banned IP (%s attempts)' . $api_lang_suffix,
	'API_LOG_BAN_USER'				=> '<strong>Banned user</strong><br />» %s' . $api_lang_suffix,
	'API_LOG_CLEAR'					=> '<strong>Purged API log</strong><br />» %s deleted message' . $api_lang_suffix,
	'API_LOG_CLEARED'				=> '<strong>Purged API log</strong>' . $api_lang_suffix,
	'API_LOG_ERROR_CLEARED'			=> '<strong>Purged API error log</strong>' . $api_lang_suffix,
	'API_LOG_CONFIG_UPDATED'		=> 'Updated settings of phpBB API',
	'API_LOG_DEACTIVATED_METHOD'	=> '<strong>Attempting to use a deactivated method</strong><br />» %s' . $api_lang_suffix,
	'API_LOG_DENIED_PRIVILEGE'		=> '<strong>Denied privilege</strong><br />» %s' . $api_lang_suffix,
	'API_LOG_FATAL_ERROR'			=> '<strong>A PHP fatal error occured:</strong><br /><strong>File: </strong><code>%1$s</code><br /><strong>Line: </strong><code>%2$s</code><br /><strong>Message: </strong><code>%3$s</code>' . $api_lang_suffix,
	'API_LOG_NON_FATAL_ERROR'		=> '<strong>An error has been returned from the debugger:</strong><br /><strong>File: </strong><code>%1$s</code><br /><strong>Line: </strong><code>%2$s</code><br /><strong>Message: </strong><code>%3$s</code>' . $api_lang_suffix,
	'API_LOG_GET_CONFIG'			=> '<strong>Retrieving configuration data:</strong> %s' . $api_lang_suffix,
	'API_LOG_KEY_ACTIVE'			=> '<strong>Re-activated API key <em>%s</em></strong>',
	'API_LOG_KEY_CREATED'			=> '<strong>Created the API key <em>%s</em></strong>',
	'API_LOG_KEY_DELETED'			=> '<strong>Deleted the API key <em>%s</em></strong>',
	'API_LOG_KEY_DEACTIVATE'		=> '<strong>Deactivated API key <em>%s</em></strong>',
	'API_LOG_KEY_SUSPEND'			=> '<strong>Suspended API key <em>%s</em></strong>',
	'API_LOG_KEY_UPDATED'			=> '<strong>Updated option of the API key <em>%s</em></strong>',
	'API_LOG_KEY_REINITIALIZED'		=> '<strong>Secret key of the API key <em>%s</em> reinitialized</strong>',
	'API_LOG_PURGE_CACHE'			=> '<strong>Purged cache</strong>' . $api_lang_suffix,
	'API_LOG_RESYNC_STAT'			=> '<strong>Partial statistic resync</strong>' . $api_lang_suffix,
	'API_LOG_RESYNC_STATS'			=> '<strong>Post, topic and user statistics resynchronised</strong>' . $api_lang_suffix,
	'API_LOG_SQL_QUERY'				=> '<strong>Running a SQL query:</strong>&nbsp;<textarea rows="2" cols="1" readonly="readonly" class="logsql">%s</textarea>' . $api_lang_suffix,
	'API_LOG_SQL_QUERY_UNAUTHORIZED'=> '<strong>Disallowed SQL query:</strong>&nbsp;<textarea rows="2" cols="1" readonly="readonly" class="logsql">%s</textarea>' . $api_lang_suffix,

	'API_LOG_UNBAN_IP'				=> '<strong>Unbanned IP</strong><br />» %s' . $api_lang_suffix,
	'API_LOG_UNBAN_EMAIL'			=> '<strong>Unbanned email</strong><br />» %s' . $api_lang_suffix,
	'API_LOG_UNBAN_USER'			=> '<strong>Unbanned user</strong><br />» %s' . $api_lang_suffix,

	'ACP_PHPBB_API'						=> 'phpBB API',
	'ACP_PHPBB_API_ACTIVE'				=> 'Re-active',

	'ACP_PHPBB_API_BACKTRACE'			=> 'Enable API backtrace',
	'ACP_PHPBB_API_BACKTRACE_EXP'		=> 'That feature allows you to trace potential errors of the API.
											<br />Note that only administrator keys have the privilege to see exceptions in API response. In every instance, the exceptions will be still logged in logs',
	'ACP_PHPBB_API_BAN_RECORDED'		=> '%d expired ban recorded',
	'ACP_PHPBB_API_BANS_RECORDED'		=> '%d expired bans recorded',

	'ACP_PHPBB_API_CHANGE'				=> 'Change',
	'ACP_PHPBB_API_CLOCK'				=> 'Clock',
	'ACP_PHPBB_API_CLOSE'				=> 'Close',
	'ACP_PHPBB_API_CONFIG'				=> 'Settings',
	'ACP_PHPBB_API_CREATE'				=> 'Create',
	'ACP_PHPBB_API_CREATED'				=> 'The key <strong>%s</strong> has been successfully created!',
	'ACP_PHPBB_API_CREATE_EXP'			=> 'Create a new key',
	'ACP_PHPBB_API_CREATION_DATE'		=> 'Creation date',
	'ACP_PHPBB_API_CACHE_STATS'			=> 'Cache statistics',
	'ACP_PHPBB_API_CACHE_STATS_EXP'		=> 'This option will allow you to cache the statistics to reduce server load, however these will only be updated every few hours.',
	'ACP_PHPBB_API_CRON_TASK'			=> 'CRON tasks',
	'ACP_PHPBB_API_CRON_TASK_EXP'		=> 'The CRON tasks send a periodic statistic report to all founders of the board and will partially clean the API.',

	'ACP_PHPBB_API_DB_CREDENTIALS'		=> 'Credentials data',
	'ACP_PHPBB_API_DB_NO_CHANGE'		=> 'No change',
	'ACP_PHPBB_API_DB_PASSWORD'			=> 'Password',
	'ACP_PHPBB_API_DB_SETTINGS'			=> 'Use distinct database credentials for the API',
	'ACP_PHPBB_API_DB_USERNAME'			=> 'Username',
	'ACP_PHPBB_API_DB_WARNING'			=> 'For safety reasons, you must re-enter the database credentials even if you already typed them before.',

	'ACP_PHPBB_API_DEACTIVATE'			=> 'Deactivate',
	'ACP_PHPBB_API_DEACTIVATED_METHODS'	=> 'Deactivated methods',
	'ACP_PHPBB_API_DEACTIVATED_METHODS_EXP'	=> 'You can deactivate multiple methods in one go using the appropriate combination of mouse and keyboard for your computer and browser.
											<br />Please note that some methods are already administrator`s reserved. Check out the %1$smanual%2$s for more information.',
	'ACP_PHPBB_API_DEFAULT_SETTINGS'	=> 'Default settings',
	'ACP_PHPBB_API_DELETE'				=> 'Delete',
	'ACP_PHPBB_API_DELETED'				=> 'The key <strong>%s</strong> has been successfully deleted!',

	'ACP_PHPBB_API_EDIT'				=> 'Edit',
	'ACP_PHPBB_API_ERRORS_HANDLER'		=> 'API errors handler',
	'ACP_PHPBB_API_EVENT_ID'			=> 'Event identifier',
	'ACP_PHPBB_API_EXPIRATION_DATE'		=> 'Expiration date',

	'ACP_PHPBB_API_FAQ_MULTI_COLUMN'	=> 'Multi-column FAQ',
	'ACP_PHPBB_API_FATAL_HTML'			=> 'Show fatal errors as HTML format',
	'ACP_PHPBB_API_FATAL_HTML_EXP'		=> 'If enabled, the API will attempt to format the PHP fatal errors as in HTML format instead of the user chosen output',
	'ACP_PHPBB_API_FORCE_LOGOUT'		=> 'Force logout',
	'ACP_PHPBB_API_FORCE_LOGOUT_EXP'	=> 'For extra safety, each request to the API will be terminated by a session disconnection. However this requires two additional SQL queries per request.',
	'ACP_PHPBB_API_FORCE_SSL'			=> 'Force SSL protocol',
	'ACP_PHPBB_API_FORCE_SSL_EXP'		=> 'If enabled, the API will reject each query which doesn’t use the SSL protocol (HTTPS).
											<br /><strong>Warning:</strong> Your server must have a valid SSL certificate, otherwise clients will receive a security alert!',
											
	'ACP_PHPBB_API_HEADER'				=> 'phpBB API Header',
	'ACP_PHPBB_API_HEADER_EXP'			=> 'If enabled, the server will return an header “<em>X-Powered-By: phpBB API/%s</em>”',
	'ACP_PHPBB_API_CRYPTO_ENABLED'		=> 'Enable cryptographic support',
	'ACP_PHPBB_API_CRYPTO_ENABLED_EXP'	=> 'That will enable the cryptographic support, see the API knowledge base to read more.',
	'ACP_PHPBB_API_HOOKS'				=> 'Hooks management',
	'ACP_PHPBB_API_HOOKS_EXPLAIN'		=> 'On this page you can manage API’s hooks, and also check last updates of them.',
	'ACP_PHPBB_API_HOOKS_INSTALLED_ERR'	=> 'That hook is already installed, please uninstall it before to proceed to the resettlement.',
	'ACP_PHPBB_API_HOOKS_UPLOAD_FAIL'	=> 'There was an error initializing the HOOK upload process.',
	
	'ACP_PHPBB_API_IGNORE_COUNTER'		=> 'Note that it is possible to ignore these limits using users’ permissions.',

	'ACP_PHPBB_API_KEYS'					=> 'Keys management',
	'ACP_PHPBB_API_KEY_ASSIGNED'			=> 'Key assigned to',
	'ACP_PHPBB_API_KEY_ASSIGNED_WARNING'	=> '<strong class="error">Warning</strong>: Changing the key owner <strong>will not</strong> prevent the former owner to use the key! Also, if you change the owner, the secret key will be reinitialized.',
	'ACP_PHPBB_API_KEY_EMAIL'				=> 'Authenticated Key',
	'ACP_PHPBB_API_KEY_EMAIL_EXP'			=> 'This key will be able to be used only if the email associated with the key-owner is provided .
												<br />Please note that this setting is forced for "Administrator" key type.',
	'ACP_PHPBB_API_KEY_FORCE_POST'			=> 'Force POST method',
	'ACP_PHPBB_API_KEY_FORCE_POST_EXP'		=> 'This will block the key to be used as non-POST method',
	'ACP_PHPBB_API_KEY_HISTORY'				=> 'Brute history',
	'ACP_PHPBB_API_KEY_HISTORY_DET'			=> 'Detailed history',
	'ACP_PHPBB_API_KEY_INDEX'				=> 'Keys index',
	'ACP_PHPBB_API_KEY_INVALID_TIME'		=> 'Invalid expiration date entered for the key <strong>%s</strong>, back to default value',
	'ACP_PHPBB_API_KEY_INVALID_USERNAME'	=> 'Invalid username entered for the key <strong>%s</strong>, back to default value',
	'ACP_PHPBB_API_KEY_IPS'					=> 'IP filter',
	'ACP_PHPBB_API_KEY_IPS_EXP'				=> 'To specify several different IPs enter each on a new line. To specify a wildcard use “*”.',
	'ACP_PHPBB_API_KEY_IPS_TYPE_A'			=> 'Allowed IPs',
	'ACP_PHPBB_API_KEY_IPS_TYPE_D'			=> 'Disallowed IPs',
	'ACP_PHPBB_API_KEY_NO_KEY'				=> 'No key found.',
	'ACP_PHPBB_API_KEY_OUTDATED'			=> 'The key <strong>%s</strong> is now outdated',

	'ACP_PHPBB_API_KEY_QUERY_SQL'			=> 'SQL queries',
	'ACP_PHPBB_API_KEY_QUERY_SQL_API'		=> 'SQL queries on storage table of API keys/logs',
	'ACP_PHPBB_API_KEY_QUERY_SQL_API_EXP'	=> 'You should give this feature only if you have full confidence to the owner of this key.
										<br />Indeed, this feature extends the capabilities of SQL queries by allowing it on the API/logs tables.
										<br />It is technically possible to spoof these API key, after performing a query "SELECT" type on the API tables keys and/or a request for "DELETE" type on the logs table.',
	'ACP_PHPBB_API_KEY_QUERY_SQL_EXP'		=> 'Be careful, this feature allows the key to perform any SQL statement, except on the API/logs tables.',

	'ACP_PHPBB_API_KEY_SELECT'			=> 'Select the key',
	//Constants are unavailable here :/
	'ACP_PHPBB_API_KEY_STATUS'			=> array(
			1	=> 'Active Key',
			2	=> 'Suspended Key',
			3	=> 'Deactivated key',
	),
	'ACP_PHPBB_API_KEY_STATUS_EXP'		=> 'Key status',

	'ACP_PHPBB_API_KEY_TIME_EXP'		=> '<strong>Important</strong>: To increase the accuracy of the hours and minutes, check the exactness of your time zone and daylight saving time!',
	'ACP_PHPBB_API_KEY_TITLE'			=> 'Key',
	'ACP_PHPBB_API_KEY_SECRET_KEY'		=> 'Secret key',
	'ACP_PHPBB_API_KEY_SECRET_KEY_EXP'	=> 'The secret key is cryptographic-related, and only the user knows. However you can change it if needed.',
	'ACP_PHPBB_API_KEY_TOOLS'			=> 'Tools',
	'ACP_PHPBB_API_KEY_TYPE_A'			=> 'Admin key',
	'ACP_PHPBB_API_KEY_TYPE_U'			=> 'User key',
	'ACP_PHPBB_API_KEY_UPDATED'			=> 'Key <strong>%s</strong> updated',
	'ACP_PHPBB_API_KEY_SECRET_UPDATED'	=> 'Secret key of the key <strong>%s</strong> reinitialized!',

	'ACP_PHPBB_API_LEGEND'				=> '╚═►',
	'ACP_PHPBB_API_LEGEND_DEACTIVATED'	=> 'Deactivated key',
	'ACP_PHPBB_API_LEGEND_OUTDATED'		=> 'Outdated key',
	'ACP_PHPBB_API_LEGEND_OUT_OF_QUOTA' => 'Key out of quota',
	'ACP_PHPBB_API_LEGEND_SUSPENDED'	=> 'Suspended key',
	
	'ACP_PHPBB_API_LIFETIME'			=> 'Lifetime',
	'ACP_PHPBB_API_LIST_IP'				=> 'Allow users to disallow/allow one or more IP',
	'ACP_PHPBB_API_LOADING'				=> 'Loading…',
	'ACP_PHPBB_API_LOAD_STATS'			=> 'Retrieving statistics…',
	'ACP_PHPBB_API_LOG_ALTERED'			=> '<strong>Altered phpBB API’s settings</strong>',
	'ACP_PHPBB_API_LOG_OFF'				=> '<strong>phpBB API has been disabled because the installation is not completed.</strong><br />»For further information, jump on phpBB API to see returned errors.',
	'ACP_PHPBB_API_LOG_UPDATE'			=> 'Updated phpBB API’s MOD from <strong style="color: red;">%s</strong> to	 <strong style="color: green;">%s</strong>',

	'ACP_PHPBB_API_LOGS'				=> 'Activity log',
	'ACP_PHPBB_API_LOGS_EXPLAIN'		=> 'List of actions performed via the API. You can sort by name, date, IP or action. If you have appropriate permissions you can also clear individual operations or the log',

	'ACP_PHPBB_API_ERR_LOGS'			=> 'Error log',
	'ACP_PHPBB_API_ERR_LOGS_EXPLAIN'	=> 'List of errors occured via the API. You can sort by name, date, IP or action. If you have appropriate permissions you can also clear individual operations or the log',
	'ACP_PHPBB_API_ERR_LOGS_HARD'		=> 'Size of physical error log: %s',
	'ACP_PHPBB_API_ERR_LOGS_HARD_EXP'	=> 'The physical error log contains all the fatal errors including those that has failed to be recorded in the database',
	'ACP_PHPBB_API_ERR_LOGS_PURGE'		=> 'Purge',
	'ACP_PHPBB_API_ERR_LOGS_PURGE_ERROR'=> 'Cannot purge the physical error log, file non-writable!',

	'ACP_PHPBB_API_MAX_ATTEMPS'			=> 'Maximum number of key attempts per IP address',
	'ACP_PHPBB_API_MAX_ATTEMPS_EXP'		=> 'The threshold of key attempts allowed from a single IP address before a temporary kick of the API is triggered. Enter 0 to prevent the temporary kick from being triggered by IP addresses.',
	'ACP_PHPBB_API_MAX_ATTEMPS_TIME' 	=> 'IP address key attempt expiration time',
	'ACP_PHPBB_API_MAX_ATTEMPS_TIME_EXP'=> 'Key attempts expire after this period.',
	'ACP_PHPBB_API_MAX_QUERIES'			=> 'Maximum total number of requests',
	'ACP_PHPBB_API_MAX_QUERIES_SHORT'	=> 'Max. requests',
	'ACP_PHPBB_API_MOD'					=> 'Enable phpBB API',
	'ACP_PHPBB_API_MQPD'				=> 'Maximum number of requests per day',
	'ACP_PHPBB_API_MQPM'				=> 'Maximum number of requests per month',
	'ACP_PHPBB_API_MQPW'				=> 'Maximum number of requests per week',
	'ACP_PHPBB_API_MQ_EXPLAIN'			=> 'Set 0 to disable this restriction.',

	'ACP_PHPBB_API_NEXT'				=> 'Next',
	'ACP_PHPBB_API_NOW'					=> 'Now',
	'ACP_PHPBB_API_NO_BAN_FOUND'		=> '---No ban found---',

	'ACP_PHPBB_API_OPERATION_SUCCESS'	=> 'Operation terminated successfully.',
	'ACP_PHPBB_API_ORIGIN_HEADER'		=> 'Same-origin policy',
	'ACP_PHPBB_API_ORIGIN_HEADER_EXP'	=> 'If enabled the API will return a same-origin header, which mean that the API will be protected against most CSRF exploits.',

	'ACP_PHPBB_API_PAGINATION'			=> 'Number of element per page',
	'ACP_PHPBB_API_PAGINATION_KEY'		=> '%s key',
	'ACP_PHPBB_API_PAGINATION_KEYS'		=> '%s keys',
	'ACP_PHPBB_API_PCHART_CHECK'		=> 'Check pChart installation',
	'ACP_PHPBB_API_PCHART_CHECKED'		=> 'Checking completed without problems.',
	'ACP_PHPBB_API_PREV'				=> 'Previous',
	'ACP_PHPBB_API_PURGE_FILES'			=> '%1$s file(s), %2$s',
	'ACP_PHPBB_API_PURGE_API'			=> 'Purge API temporary files',
	'ACP_PHPBB_API_PURGE_BANS'			=> 'Purge expired bans',
	'ACP_PHPBB_API_PURGE_TEMP'			=> 'Purge statistics temporary files',
	
	'ACP_PHPBB_API_QUERIES'				=> 'Requests',
	'ACP_PHPBB_API_QUERIES_EXP'			=> 'Current total count of requests with this key.',
	'ACP_PHPBB_API_QUERY_LIMIT'			=> 'Database fetch result limit',
	'ACP_PHPBB_API_QUERY_LIMIT_EXP'		=> 'That feature will set the maximum number of returned rows by the database in some API methods.',

	'ACP_PHPBB_API_RESET'				=> 'This will reset the request counter of this key.\nAre you sure to continue?',
	'ACP_PHPBB_API_RESULT'				=> 'Results per pages',

	'ACP_PHPBB_API_SELECTOR'			=> 'Action',
	'ACP_PHPBB_API_SETTINGS_ACP'		=> 'ACP API Settings',
	'ACP_PHPBB_API_SETTINGS_UCP'		=> 'UCP API Settings',
	'ACP_PHPBB_API_SHA1'				=> 'SHA1',
	'ACP_PHPBB_API_STAT_LIMIT'			=> 'Number of events displayed in the statistics',
	'ACP_PHPBB_API_STATS'				=> 'Statistics',
	'ACP_PHPBB_API_SUSPEND'				=> 'Suspend',
	'ACP_PHPBB_API_TIME_TYPE'			=> 'Time based',
	'ACP_PHPBB_API_TIME_CALENDAR'		=> 'On a per calendar basis',
	'ACP_PHPBB_API_TIME_ROLLING'		=> 'Within delay (recommended)',
	'ACP_PHPBB_API_TYPE'				=> 'Key type',
	'ACP_PHPBB_API_TYPE_EXP'			=> 'Note that the administrator keys can not be managed from the user control panel.',

	'ACP_PHPBB_API_UCP_KEYS'			=> 'Enable users keys',
	'ACP_PHPBB_API_UCP_KEYS_EXP'		=> 'This will allow your users to manage their keys in an autonomous way.',
	'ACP_PHPBB_API_UCP_URL_CRYPT'		=> 'Crypt the key into the URL',
	'ACP_PHPBB_API_UCP_URL_CRYPT_EXP'	=> 'This functionality will hide the keys of your users from indiscreet eyes and their browsing history.
										<br />It also adds extra security against third party script by making the key hard to recover',
	'ACP_PHPBB_API_UNBAN'			=> 'Un-ban IPs',
	'ACP_PHPBB_API_UNBAN_EXP'		=> 'You can unban (or un-exclude) multiple IP addresses in one go using the appropriate combination of mouse and keyboard for your computer and browser.',
	'ACP_PHPBB_API_WHITELIST'		=> 'IP white-list',
	'ACP_PHPBB_API_WHITELIST_EXP'	=> 'List of IP that will be excluded of key attempts. To specify several different IPs enter each on a new line. To specify a wildcard use “*”.',
	'ACP_PHPBB_API_UNBANNING'		=> 'Unbanning',
	'ACP_PHPBB_API_UPDATED_CFG'		=> 'Settings saved',
	'ACP_PHPBB_API_UPLOAD'			=> 'Upload',
	'ACP_PHPBB_API_UPLOAD_HOOK'		=> 'Upload hook',
	'ACP_PHPBB_API_UPLOAD_HOOK_EXP'	=> 'Here you can upload a zipped HOOK package at BertiX (see <em>user</em> hook) format which will be uploaded and unzipped into the API core.',

	'ACP_PHPBB_API_VALIDITY_DATE'	=> 'Validity time',
	'ACP_PHPBB_API_VIEW_MORE'		=> 'View more',
	'ACP_PHPBB_API_WILDCARD_CHAR'	=> 'Wildcard char',
	'ACP_PHPBB_API_WILDCARD_CHAR_EXP'=> 'This char sent alone on the API will be treated as a wildcard and so will be treated as an empty string.',

//Mod error
	'ACP_PHPBB_API_CRYPTO_ERROR'		=> 'The mcrypt extension is missing. Cannot activate that setting.',
	'ACP_PHPBB_API_ERR_INSTALL'			=> 'The MOD is now disabled for security reasons until its installation is complete.',
	'ACP_PHPBB_API_ERR_NOCONST'			=> '<em>API_KEYS_TABLE</em> Constant is missing… Check file in “/includes/api/constants.php” file',
	'ACP_PHPBB_API_INSTALL_NO_COLLUMN'	=> 'The SQL column <strong>“ %1$s ”</strong> from the <strong>“ %2$s ”</strong> table is missing.',
	'ACP_PHPBB_API_INSTALL_NO_FILE'		=> 'The file<strong>“ %s ”</strong> is missing.',
	'ACP_PHPBB_API_INSTALL_NO_DIRECTORY'=> 'The directory<strong>“ %s ”</strong> is missing.',
	'ACP_PHPBB_API_INSTALL_NO_TABLE'	=> 'The SQL table <strong>“ %1$s ”</strong> is missing.',
	'ACP_PHPBB_API_NO_JAVASCRIPT'		=> 'Administration of this MOD require Javascript for better performances, please enable it!',
    'ACP_PHPBB_API_NO_PCHART'			=> 'The pChart library is missing or corrupted, therefore the statistics viewing is disabled.
											<br /><br /><strong>additional Information:</strong>
											<br />The pChart library is licensed GNU/GPL<sup>V3</sup>, you must download yourself <a href="http://geolim4.com/pchart/pchart.zip">here</a> and extract inside %s directory.
											<br /><br /><em>Why on Geolim4.com and not on the pChart’s website author?</em>
											<br />Due to an encoding conflict of character between phpBB and pChart, the library had to be slightly modified to run correctly.',

//Version Check
	'ACP_ERRORS'						=> 'Errors',

	'API_CURRENT_VERSION'				=> 'Current version',
	'API_ERRORS_CONFIG_ALT'				=> 'Configuration of the MOD phpBB API',
	'API_ERRORS_CONFIG_EXPLAIN'			=> 'On this page you can check if your version of this mod is up to date or otherwise, actions to take for the update.<br />You can also set points simple configuration related.',
	'API_ERRORS_INSTRUCTIONS'			=> '<br /><h1>To use phpBB API v%1$s</h1><br />
													<p>Geolim4.com hopes you will like the features of this Mod.<br />
													Feel free to give your feedback… Go <strong><a href="%2$s" title="Alert For Login">on this page</a></strong></p>
													<p>For any support request, go to the <strong><a href="%3$s" title="Support Forum">Support Forum</a></strong></p>
													<p>Also visit the Tracker <strong><a href="%4$s" title="Tracker of Mod phpBB API">on this page</a></strong>. Keep you informed of any bugs, feature requests or additions, security…</p>',
	'API_ERRORS_NO_VERSION'				=> '<span style="color: red">The version of the server could not be found…</span>',
	'API_ERRORS_UPDATE_INSTRUCTIONS'		=> '
		<h1>Release announcement</h1>
		<p>Please read <a href="%1$s" title="%1$s"><strong>the release announcement of the latest version</strong></a> before beginning the process of updating, it may contain useful information. It also contains download links and a complete change log.</p>
		<br />
		<h1>How to update your installation of phpBB API</h1>
		<p>► Download the latest version.</p>
		<p>► Unzip the archive and open the install.xml file, it contains all the update information.</p>
		<p>► Official announcement of the latest version: (%2$s)</p>',

	'API_ERRORS_VERSION_CHECK'			=> 'Version checker of phpBB API',
	'API_ERRORS_VERSION_CHECK_EXPLAIN'	=> 'Checks if the version of phpBB API that you are currently using is the latest.',
	'API_ERRORS_VERSION_COPY'			=> '<a href="%1$s" title="Mod phpBB API">Mod phpBB API v%2$s</a> &copy; 2011 - ' . date('Y') . ' <a href="http://geolim4.com" title="geolim4.com"><em>Geolim4.com</em></a>',
	'API_ERRORS_VERSION_NOT_UP_TO_DATE'	=> 'Your version of phpBB API is outdated.<br />Below you will find a link to the release announcement of the latest version and instructions on how to perform the update.',
	'API_ERRORS_VERSION_UP_TO_DATE'		=> 'Your installation is up to date.',

	'API_LATEST_VERSION'				=> 'Latest version',
	'API_NEW_VERSION'					=> 'Your version of phpBB API is not up to date. Your version is the %1$s, the latest version is the %2$s. Read the next line for more information',

	'API_UNABLE_CONNECT'				=> 'Can not get MOD version from server, error message: %s',
	'API_UNABLE_CONNECT_HOOK'			=> 'Can not get hooks versions from server, error message: %s',
));

//phpBB complement
$lang = array_merge($lang, array(
	'LIFETIME'		=> 'Lifetime'
));
?>