<?php
/**
*
* @package phpBB3 API Core extend: Static methods
^>@version $Id: core_static.php v0.0.1 13h37 03/08/2014 Geolim4 Exp $
* @copyright (c) 2012 - 2014 Geolim4.com http://geolim4.com
* @bug/function request: http://geolim4.com/tracker
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/
namespace phpbb_api;

/**
* @ignore
*/
if (!defined('IN_PHPBB') || !defined('IN_PHPBB_API'))
{
	exit;
}

trait core_static
{
	static public function STC_get_privileges($privileges = array())
	{
		return self::load_privileges($privileges, true);
	}

	static public function STC_get_timestampable($timestampables = array())
	{
		return self::load_timestampable($timestampables, true);
	}

	static public function STC_get_crypto_config()
	{
		global $user;

		return array(
			'cypher'		=> self::$api_crypto_cipher, 
			'mode'			=> self::$api_crypto_mode,
			'iv'			=> self::$api_crypto_iv,
			'filename'		=> self::$api_crypto_filename,
			'secret_key'	=> $user->lang['API_CRYPTO_PRIVATE'],
		);
	}
}