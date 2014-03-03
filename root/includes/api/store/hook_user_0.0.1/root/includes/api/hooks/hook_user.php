<?php
/**
*
* @package phpBB API hook
^>@version $Id: hook_user.php v0.0.1 13h13 10/19/2013 Geolim4 Exp $
* @copyright (c) 2012 - 2014 Geolim4.com http://geolim4.com
* @bug/function request: http://geolim4.com/tracker
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/
namespace phpbb_api\hooks\methods
{
	/**
	 * @ignore
	 */
	if (!defined('IN_PHPBB') || !defined('IN_PHPBB_API') )
	{
		exit;
	}
	if (!defined(__NAMESPACE__ . '\USER_HOOK_API_TARGET_VERSION') )
	{
		//Define hook version here (MANDATORY)
		define(__NAMESPACE__ . '\USER_HOOK_API_TARGET_VERSION', '0.0.1');
		//Add your custom lang file with this hook, it will handled into the API core and in the ACP (hook management).
		//You can pass it as a string (array is unsupported) as you can see below
		$add_hook_lang = 'mods/hooks/info_acp_hook_user';

		//Hook identifier (ACP management)
		if (defined('ADMIN_START'))
		{
			//Used to identifie that method in key management in ACP
			//Comment it out if you want to disable the ability to disallow that method in ACP
			$base_hook_name = 'user';

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
		function hook_api_user($mom, $data, $type)
		{
			/**
			* @ignore unprivileged user
			*/
			$mom->require_admin_privilege(false, substr(\phpbb_api\rmvnmspce(__FUNCTION__), 5));
			$mom->sanitize(substr(\phpbb_api\rmvnmspce(__FUNCTION__), 5), $data, $type);
			$mom->can_skip_counter = false;
			$mom->ignore_cron = false;

			global $sql_sorting;
			$sql_sorting = \phpbb_api\sql_sorting($sql_sorting);
			$rows = array();
			if ($data && $type)
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
					$mom->filter($rows_, substr(\phpbb_api\rmvnmspce(__FUNCTION__), 5));
					$rows[$key] = $rows_;
				}
				$mom->display($rows);
			}
		}
	/***
	**
	* Hook options
	**
	***/
		//Declare your own sanitize function
		//http://localhost/api/?a=purge&t=$type&d=$data
		//@link from phpbb_api::sanitize
		//function must begin like this: hook_api_sanitize_
		//And finish like the method name: hook_api_sanitize_"purge"
		function hook_api_sanitize_user($mom, &$data, &$type)
		{
			$mom->validate_sql_column(USERS_TABLE, $type);
			$data = (is_string($data) ? '\'' . $mom->db->sql_escape($data) .  '\'' : (int) $data);
		}


	/***
	---------------------------------------------------------------------------------
	***/
		//Add you custom filter depending key privilege
		//Depending type of filter: 
		//							API_FILTER_UNSET will remove specified rows
		//							API_FILTER_INSET will only keep specified rows (you can use wildcard to ignore it: '*')

		$add_hook_filter['user'] = array(
			API_FILTER_TYPE		=> API_FILTER_INSET,
			API_TYPE_ADMIN => array('*'),
			API_TYPE_USER => array(
				'user_id', 'username', 
				'group_id', 'user_regdate', 
				'user_colour'
			),
		);
		//Check ACL
		//This doesn't support forum ACL as you can't provide forum's ID.
		//$add_hook_filter['auth'][API_AUTH_AND] = array('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel'); //Will require u_viewprofile AND a_user AND a_useradd AND a_userdel
		$add_hook_filter['auth'][API_AUTH_OR] = array('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel'); //Will require u_viewprofile OR a_user OR a_useradd OR a_userdel

	/***
	---------------------------------------------------------------------------------
	***/
	}
}

