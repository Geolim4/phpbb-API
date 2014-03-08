<?php
/**
*
* @package UCP info phpBB API
^>@version $Id: ucp_phpbb_api.php v0.0.1 13h37 03/08/2014 Geolim4 Exp $
* @copyright (c) 2012 - 2014 Geolim4.com http://geolim4.com
* @bug/function request: http://geolim4.com/tracker
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/

/**
* @package module_install
*/
class ucp_phpbb_api_info
{
	function module()
	{
		return array(
			'filename'	=> 'ucp_phpbb_api',
			'title'		=> 'UCP_PHPBB_API',
			'version'	=> '0.0.1',
			'modes'		=> array(
				'key'		=> array('title' => 'UCP_PHPBB_API_KEY', 'auth' => 'cfg_api_mod_enable && cfg_api_mod_ucp_keys && acl_u_phpbb_api_use', 'cat' => array('UCP_PHPBB_API')),
				'stats'		=> array('title' => 'UCP_PHPBB_API_STATS', 'auth' => 'cfg_api_mod_enable && cfg_api_mod_ucp_keys && acl_u_phpbb_api_use && acl_u_phpbb_api_stats', 'cat' => array('UCP_PHPBB_API')),
				'history'	=> array('title' => 'UCP_PHPBB_API_HISTORY', 'auth' => 'cfg_api_mod_enable && cfg_api_mod_ucp_keys && acl_u_phpbb_api_use && acl_u_phpbb_api_history', 'cat' => array('UCP_PHPBB_API')),
				'kb'		=> array('title' => 'UCP_PHPBB_API_KB', 'auth' => 'cfg_api_mod_enable && cfg_api_mod_ucp_keys && acl_u_phpbb_api_use', 'cat' => array('UCP_PHPBB_API')),
			),
		);
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}

?>