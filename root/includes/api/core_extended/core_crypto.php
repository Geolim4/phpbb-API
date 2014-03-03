<?php
/**
*
* @package phpBB3 API Class core
^>@version $Id: core_crypto.php v0.0.1 00h11 12/20/2013 Geolim4 Exp $
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

trait core_crypto
{
	//We declare these static because we re-use them into knowledge base.
	protected static $api_crypto_cipher = 'MCRYPT_BLOWFISH';
	protected static $api_crypto_mode = 'MCRYPT_MODE_CFB';
	protected static $api_crypto_iv = '';//Not used for now.
	protected static $api_crypto_filename = 'api.response';
	
	public function encrypt($string, $key, $set_headers = true)
	{
		if(!$this->skip_crypto)
		{
			if(!empty($this->config['api_mod_crypto_enabled']) && !empty($this->encrypted_output))
			{
				if(extension_loaded('mcrypt'))
				{
					if($set_headers)
					{
						header('Content-Type: multipart/encrypted');
						header('Content-Transfer-Encoding: binary');
						if ( strpos($this->user->browser, 'MSIE') !== false || strpos($this->user->browser, 'Safari') !== false || strpos($this->user->browser, 'Konqueror') !== false )
						{
							$header_filename = "filename=" . rawurlencode(self::$api_crypto_filename);
						}
						else
						{
							$header_filename = "filename*=UTF-8''" . rawurlencode(self::$api_crypto_filename);
						}
						header('Content-Disposition: attachment; ' . $header_filename);
					}
					return $this->_encrypt($string, $key);
				}
				else
				{
					error_handling\generate_warning('The mcrypt extension is missing.', __FILE__, __LINE__);
				}
			}
			else if (empty($this->config['api_mod_crypto_enabled']) && !empty($this->encrypted_output))
			{
				//Prevent following fatal error: Maximum function nesting level of '100' reached, aborting!
				$this->encrypted_output = false;
				$this->trigger_error('API_ERROR_CRYPTO_DISABLED', E_USER_WARNING);
			}
		}
		return $string;
	}

	public function get_crypto_config()
	{
		return array(
			'cypher' => self::$api_crypto_cipher, 
			'mode' => self::$api_crypto_mode,
			'iv' =>  self::$api_crypto_iv,
			'secret_key' => $this->user->lang['API_CRYPTO_PRIVATE'],
		);
	}

	public function decrypt($string, $key)
	{
		if(extension_loaded('mcrypt') && !empty($this->config['api_mod_crypto_enabled']) && !empty($this->encrypted_output))
		{
			if(extension_loaded('mcrypt'))
			{
				return $this->_decrypt($string, $key);
			}
			else
			{
				error_handling\generate_warning('The mcrypt extension is missing.', __FILE__, __LINE__);
			}
		}
		return $string;
	}

	protected function _encrypt($string, $key)
	{
		return mcrypt_encrypt(constant(self::$api_crypto_cipher), $key, trim($string), constant(self::$api_crypto_mode), self::$api_crypto_iv);
	}

	protected function _decrypt($string, $key)
	{
		return base64_decode(mcrypt_decrypt(constant(self::$api_crypto_cipher), $key, $string, constant(self::$api_crypto_mode), self::$api_crypto_iv));
	}
}