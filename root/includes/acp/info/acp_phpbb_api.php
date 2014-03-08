<?php
/**
*
* @package info ACP info phpBB API
^>@version $Id: acp_phpbb_api.php v0.0.1 13h37 03/08/2014 Geolim4 Exp $
* @copyright (c) 2012 - 2014 Geolim4.com http://geolim4.com
* @bug/function request: http://geolim4.com/tracker
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/

/**
* @package module_install
*/
if (!defined('IN_PHPBB'))
{
	exit;
}
/**
* @package module_install
*/
class acp_phpbb_api_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_phpbb_api',
			'title'		=> 'ACP_PHPBB_API',
			'version'	=> '1.1.0',
			'modes'		=> array(
				'config'	=> array('title' => 'ACP_PHPBB_API_CONFIG', 'auth' => 'acl_a_phpbb_api_config', 'cat' => array('ACP_PHPBB_API')),
				'keys'		=> array('title' => 'ACP_PHPBB_API_KEYS', 'auth' => 'acl_a_phpbb_api_keys && cfg_api_mod_enable', 'cat' => array('ACP_PHPBB_API')),
				'logs'		=> array('title' => 'ACP_PHPBB_API_LOGS', 'auth' => 'acl_a_phpbb_api_logs && cfg_api_mod_enable', 'cat' => array('ACP_PHPBB_API')),
				'err_logs'	=> array('title' => 'ACP_PHPBB_API_ERR_LOGS', 'auth' => 'acl_a_phpbb_api_logs && cfg_api_mod_enable', 'cat' => array('ACP_PHPBB_API')),
				'hooks'		=> array('title' => 'ACP_PHPBB_API_HOOKS', 'auth' => 'acl_a_phpbb_api_hooks && cfg_api_mod_enable', 'cat' => array('ACP_PHPBB_API')),
				'stats'		=> array('title' => 'ACP_PHPBB_API_STATS', 'auth' => 'acl_a_phpbb_api_stats && cfg_api_mod_enable', 'cat' => array('ACP_PHPBB_API')),
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