<?php
/**
*
* @package phpBB API hook
^>@version $Id: hook_user.php v0.0.1 00h11 12/20/2013 Geolim4 Exp $
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
if (!defined('IN_PHPBB') || !defined('IN_PHPBB_API'))
{
	exit;
}
if (!defined(__NAMESPACE__ . '\USER_HOOK_API_TARGET_VERSION'))
{
	//Define hook version here (MANDATORY)
	define(__NAMESPACE__ . '\USER_HOOK_API_TARGET_VERSION', '0.0.1');

	//Add your custom lang file with this hook, it will handled into the API core and in the ACP (hook management).
	//You can pass it as a string (array is unsupported) as you can see below
	$add_hook_lang = 'mods/hooks/info_acp_hook_user';

	//Set a template name here if your hook use it!
	//You can get it using later in your main function using $mom->template_content
	//$add_hook_tpl = 'template_name.' . API_HTML_EXT;

	//Set privileges
	$add_privileges['user'] = array(API_TYPE_ADMIN => true, API_TYPE_USER => true);

	//Used to identifie that method in key management in ACP
	//Comment it out if you want to disable the ability to disallow that method in ACP
	$base_hook_name = 'user';

	//Hook identifier (ACP management)
	if (defined('ADMIN_START'))
	{
		$acp_api_hook_install = function()
		{
			set_config('phpbb_api_hook', 'Bertie just plastered !!');
		};

		$acp_api_hook_uninstall = function()
		{
			\phpbb_api\unset_config('phpbb_api_hook');
		};

		$acp_api_hook_manager = array(
			//in case you want to use your own version checker, uncomment this, and follow instruction here: 
			// https://geolim4.com/centre-de-documentations/phpbb-api-version-checker-instructions-t1763.html
			//'vchecker'=> array('yourwebsite.com', '/subpath', 'hookversion.txt'),
			'download'	=> 'http://geolim4.com/api-hooks.html?h=purge_cache',
			'website'	=> 'http://geolim4.com',
			'author'	=> 'Geolim4',
			'version'	=> USER_HOOK_API_TARGET_VERSION,
			'name'		=> 'ACP_API_MANAGE_HOOK_USER',//Hey hook, what is your name?
			'date'		=> array('10/15/2013 23:12', '%m/%d/%Y %H:%M'),//Will be passed into strptime()
		);
		return;
	}

	/****
	* hook_api_user()
	* Grab user datas
	* @param string $data data to grab
	* @param string $type Type of data to grab
	****/
	function hook_api_user(api_master $mom, $data, $type)
	{
		/**
		* @ignore unprivileged user
		*/
		$mom->check_admin_privilege(substr(apiFN\rmvnmspce(__FUNCTION__), 5));
		$mom->sanitize(substr(apiFN\rmvnmspce(__FUNCTION__), 5), $data, $type);
		$mom->skip_counter = false;
		$mom->skip_crypto = false;
		$mom->ignore_cron = false;

		//$template_content = $mom->template_content;
		global $sql_sorting;
		$sql_sorting = apiFN\sql_sorting($sql_sorting, $data);
		$rows = array();
		if ($type && $data !== '')
		{
			$sql = "SELECT *
				FROM " . USERS_TABLE . "
				WHERE $type {$sql_sorting['operator']} $data";
			$result = $mom->db->sql_query_limit($sql, $sql_sorting['limit'], $sql_sorting['offset']);
			while ($row = $mom->db->sql_fetchrow($result))
			{
				$rows[] = $row;
			}
			$mom->db->sql_freeresult($result);
			foreach ($rows AS $key => $rows_)
			{
				$mom->filter($rows_, substr(apiFN\rmvnmspce(__FUNCTION__), 5));
				$rows[$key] = $rows_;
			}
			$mom->display($rows);
		}
		else
		{
			$mom->trigger_error('API_ERROR_NO_SUBMETHOD', E_USER_WARNING);
		}
	}
/***
**
* Hook options
**
***/
	//Declare your own sanitize function
	//@link from phpbb_api::sanitize
	//function must begin like this: hook_api_sanitize_
	//And finish like the method name: hook_api_sanitize_user
	function hook_api_sanitize_user(api_master $mom, &$data, &$type)
	{
		$mom->validate_sql_column(USERS_TABLE, $type);
		$data = (is_numeric($data) ? ((strpos($data, '.') !== false) ? (float) $data : (int) $data) : '\'' . $mom->db->sql_escape($data) .  '\'');
	}

	//Will be called in api::authenticate_key() once the user is authed 
	function hook_api_set_submethod_user(api_master $mom)
	{
		$mom->api_methods_type['user'] = apiFN\array_key_fill_value($mom->api_filters['user'][$mom->api_type]);
	}

/***
---------------------------------------------------------------------------------
***/
	//Add you custom filter depending key privilege
	//Depending type of filter: 
	//		API_FILTER_UNSET will remove specified rows
	//		API_FILTER_INSET will only keep specified rows (you can use wildcard to ignore it: array('*'))
	//Use $sql_mapping[SQL_TABLE_NAME] instead of wildcard if needed
	$add_hook_filter['user'] = array(
		API_FILTER_TYPE		=> API_FILTER_INSET,
		API_TYPE_ADMIN => isset($sql_mapping) ? $sql_mapping[USERS_TABLE] : array('*'),
		API_TYPE_USER => array(
			'user_id', 'username', 
			'group_id', 'user_regdate', 
			'user_colour', 'user_posts',
			'user_website', 'user_interests'
		),
	);

	//Check ACL
	//This doesn't support forum ACL as you can't provide forum's ID.
	//$add_hook_filter['user']['auth'][API_AUTH_AND] = array('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel'); //Will require u_viewprofile AND a_user AND a_useradd AND a_userdel
	//$add_hook_acl['user'][API_AUTH_XOR] = array('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel'); //Will require u_viewprofile exclusively OR a_user exclusively OR a_useradd OR a_userdel exclusively 
	$add_hook_acl['user'][API_AUTH_OR] = array('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel'); //Will require u_viewprofile OR a_user OR a_useradd OR a_userdel

	//Add time-convertible columns.
	$add_hook_timestamp['user'] = array('user_regdate', 'user_passchg', 'user_lastmark', 'user_lastvisit', 'user_last_privmsg', 'user_last_search', 'user_lastpost_time', 'user_inactive_time');

/***
---------------------------------------------------------------------------------
***/
}