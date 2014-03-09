<?php
/**
*
* @package phpBB API hook
^>@version $Id: hook_purge_cache.php v0.0.1 13h37 03/08/2014 Geolim4 Exp $
* @copyright (c) 2012 - 2014 Geolim4.com http://geolim4.com
* @bug/function request: http://geolim4.com/tracker
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/
namespace phpbb_api\hooks\methods;
use \phpbb_api\functions AS apiFN, \phpbb_api\api AS api_master;

/**
 * @ignore
 */
if (!defined('IN_PHPBB') || !defined('IN_PHPBB_API') )
{
	exit;
}
if (!defined(__NAMESPACE__ . '\PURGE_HOOK_API_TARGET_VERSION') )
{
	//Define hook version here (MANDATORY)
	define(__NAMESPACE__ . '\PURGE_HOOK_API_TARGET_VERSION', '0.0.1');

	//Add your custom lang file with this hook, it will handled into the API core.
	//You can pass it as a string (array is unsupported) as you can see below
	$add_hook_lang = 'mods/hooks/info_acp_hook_purge';

	//Set a template name here if your hook use it!
	//You can get it using later in your main function using $mom->template_content
	//$add_hook_tpl = 'template_name.' . API_HTML_EXT;

	//Set privileges
	$add_privileges['purge'] = array(API_TYPE_ADMIN => true, API_TYPE_USER => false);
	
	//Used to identifie that method in key management in ACP
	//Comment it out if you want to disable the ability to disallow that method in ACP
	$base_hook_name = 'purge';
	
	//Hook identifier (ACP management)
	if (defined('ADMIN_START'))
	{
		$acp_api_hook_install = function()
		{
			// set_config('phpbb_api_hook', 'Bertie just plastered !!');
		};

		$acp_api_hook_uninstall = function()
		{
			// \phpbb_api\unset_config('phpbb_api_hook');
		};
		$acp_api_hook_manager = array(
			//in case you want to use your own version checker, uncomment this, and follow instruction here: 
			// https://geolim4.com/centre-de-documentations/phpbb-api-version-checker-instructions-t1763.html
			//'vchecker'=> array('yourwebsite.com', '/subpath', 'hookversion.txt'),
			'download'	=> 'http://geolim4.com/api-hooks.html?h=hook_purge_cache',
			'website'	=> 'http://geolim4.com',
			'author'	=> 'Geolim4',
			'version'	=> PURGE_HOOK_API_TARGET_VERSION,
			'name'		=> 'ACP_API_MANAGE_HOOK_PURGE',//Hey hook, what is your name?
			'date'		=> array('2/15/2013 23:12', '%m/%d/%Y %H:%M'),//Will be passed into strptime()
		);
		return;
	}
	//Here you can declare your own sanitize function
	//http://localhost/api/?a=purge&t=$type&d=$data
	//@link from phpbb_api::call
	function hook_api_purge(api_master $mom, $data, $type)
	{
		$mom->check_admin_privilege(substr(apiFN\rmvnmspce(__FUNCTION__), 5));
		//$mom->sanitize(substr(__FUNCTION__, 5), $data, $type);
		$mom->skip_counter = false;
		$mom->skip_crypto = false;
		$mom->ignore_cron = false;
		//$template_content = $mom->template_content;

		if (!function_exists('cache_moderators') )
		{
			include($mom->phpbb_root_path . 'includes/functions_admin.' . $mom->phpEx);
		}
		$mom->cache->purge();
		// Clear permissions
		$mom->auth->acl_clear_prefetch();
		cache_moderators();
		apiFN\api_add_log('API_LOG_PURGE_CACHE', $mom->api_key);
		$mom->trigger_error('API_CACHED_PURGED');
	}
/***
**
* Hook options
**
***/
	//Declare your own sanitize function
	//@link from phpbb_api::sanitize
	//function must begin like this: hook_api_sanitize_
	//And finish like the method name: hook_api_sanitize_purge
	function hook_api_sanitize_purge(api_master $mom, &$data, &$type)
	{
		//We do not need to sanitize data here
		//$mom->validate_sql_column(USERS_TABLE, $type);
		//$data = (is_string($data) ? '\'' . $this->db->sql_escape($data) .  '\'' : (int) $data);
	}
/***
---------------------------------------------------------------------------------
***/
	//Add you custom filter depending key privilege
	//Depending type of filter: 
	//		API_FILTER_UNSET will remove specified rows
	//		API_FILTER_INSET will only keep specified rows (you can use wildcard to ignore it: '*')
/* 	$add_hook_filter['purge'] = array(
			API_FILTER		=> API_FILTER_UNSET,
			API_TYPE_ADMIN	=> array(),//Nothing will be removed
			API_TYPE_USER	=> array('user_password', 'forum_password'),//There rows has been removed for key type: API_TYPE_USER
	); */

/* 	$add_hook_filter['purge'] = array(
			API_FILTER		=> API_FILTER_INSET,
			API_TYPE_ADMIN	=> array(),//Warning: Nothing will be displayed: Nothing mentioned unless if you use wildcard: API_TYPE_ADMIN	=> array('*'),
			API_TYPE_USER	=> array('user_password', 'forum_password'),//Only these rows will be displayed !
	); */
/***
---------------------------------------------------------------------------------
***/
}