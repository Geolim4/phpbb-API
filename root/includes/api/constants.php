<?php
/**
*
* @package API constants
^>@version $Id: constants.php v0.0.1 00h11 12/20/2013 Geolim4 Exp $
* @copyright (c) 2012 - 2014 Geolim4.com http://geolim4.com
* @bug/function request: http://geolim4.com/tracker
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/
namespace phpbb_api;

/**
* @ignore
*/
if (!defined('IN_PHPBB') || !defined('IN_PHPBB_API'))
{
	exit;
}

if (defined('API_CONST_LOADED'))
{
	return;
}
else
{
	define('API_CONST_LOADED', true);
}

//Define some "shortcuts"
define('API_TRAITS', 'core_loader,core_methods,core_static,core_crypto');
define('API_ROOT_PATH', $phpbb_root_path . 'includes/api/');
define('API_CORE_PATH', $phpbb_root_path . 'includes/api/core_extended/');
define('API_CACHE_PATH', $phpbb_root_path . 'includes/api/cache/');
define('API_HOOKS_PATH', $phpbb_root_path . 'includes/api/hooks/');
define('API_TEMPLATES_PATH', $phpbb_root_path . 'includes/api/templates/');
define('API_ERR_LOG_FILE', API_ROOT_PATH . 'error.log');

//Define core constants
define('API_PCHART_DOWNLOAD', 'http://geolim4.com');
define('API_HTML_EXT', 'html');
define('API_EXPIRE_HOUR', 'hours');
define('API_EXPIRE_DAY', 'days');
define('API_EXPIRE_MONTH', 'month');
define('API_EXPIRE_YEAR', 'year');
define('API_EXPIRE_LIFETIME', 'lifetime');
define('API_HOUR_SECONDS', 3600);
define('API_DAY_SECONDS', 86400);
define('API_EXPIRE_SELECTOR', '1,2,3,4,5,6,7,8,9,10,15,20,25');
define('API_STATUS_DOWN', false);
define('API_STATUS_UP', true);
define('API_TYPE_USER', 1);
define('API_TYPE_ADMIN', 2);
define('API_CALENDAR_TIME', 1);
define('API_ROLLING_TIME', 2);
define('API_LOG_RESULTS_COUNT', '5,10,15,25,50,75,100,150,200,250,300,400,500');
define('API_AUTH', 'auth');
define('API_AUTH_XOR', -1);
define('API_AUTH_OR', 0);
define('API_AUTH_AND', 1);
define('API_FILTER_TYPE', 'type');
define('API_CUSTOM_OUTPUT', 'custom');
define('API_FILTER_UNSET', 0);
define('API_FILTER_INSET', 1);
define('API_VERSION', '0.0.1');
define('API_STATUS_ACTIVE', 1);
define('API_STATUS_SUSPENDED', 2);
define('API_STATUS_DEACTIVATED', 3);
define('API_PCHART_HEADER', 'vbGltNC5jb20gLSBwQ2hhcnQgMi54');
define('API_IP_ALLOWED', 1);
define('API_IP_DISALLOWED', 0);
define('API_KEY_LENGHT', 40);
define('API_GEN_SOURCE_ACP', 1);
define('API_GEN_SOURCE_UCP', 2);
define('LOG_API', 1);
define('LOG_API_ERROR_OPERATIONS', 'API_LOG_FATAL_ERROR,API_LOG_NON_FATAL_ERROR');

//Define some tricks
define('DOT', '.');
define('SLASH', '/');

//We do not use $user->format_date() for a PHP header file...
if (defined('ADMIN_START'))
{
	define('API_DEFAULT_CONFIG_FILE', "<?php
/**
*
* phpBB API " . API_VERSION . " auto-generated database configuration file
* Do not change anything in this file!
^>@version \$Id: config.php | Auto-Generated on " . date('F d, Y \@ h\hiA') . " by " . $user->data['username'] . "
* @copyright (c) 2012 - 2014 Geolim4.com http://geolim4.com
* @bug/function request: http://geolim4.com/tracker
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/
//\$dbms = '';
//\$dbhost = '';
//\$dbport = '';
//\$dbname = '';
\$api_dbuser = '%USER%';
\$api_dbpasswd = '%PASSWORD%';
//\$table_prefix = '';
//\$acm_type = '';
//\$load_extensions = '';

//@define('PHPBB_INSTALLED', true);
//@define('DEBUG', true);
//@define('DEBUG_EXTRA', true);");
}

global $table_prefix;
if (!isset($table_prefix))
{
	$table_prefix = 'phpbb_';
}

// Table names
define('API_HISTORY_TABLE',		$table_prefix . 'api_history');
define('API_KEYS_TABLE',		$table_prefix . 'api_keys');
define('API_LOG_TABLE',			$table_prefix . 'api_log');
define('API_LOGIN_ATTEMPTS',	$table_prefix . 'api_login_attempts');