<?php
/**
*
* @package API constructor
* @That file has been made in purpose to avoid a parse syntax error in api.php (gateway interface) with PHP < 5.4.x
^>@version $Id: constructor.php v0.0.1 13h37 03/08/2014 Geolim4 Exp $
* @copyright (c) 2012 - 2014 Geolim4.com http://geolim4.com
* @bug/function request: http://geolim4.com/tracker
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/

namespace phpbb_api;
use \phpbb_api\error_handling\exception;

/**
* @ignore
*/
if (!defined('IN_PHPBB') || !defined('IN_PHPBB_API'))
{
	exit;
}
if (!class_exists(__NAMESPACE__ . '\api_cache') || !class_exists(__NAMESPACE__ . '\error_handling\exception') || !class_exists(__NAMESPACE__ . '\api'))
{
	exit;
}
$api_cache		= new api_cache();				//Instantiate API's cache
$api_exception	= new exception($output);		//Instantiate API's error handler
$api			= new api($output, $key);		//Instantiate API
$api_exception->zombify($api, $error_handled);	//Save the world
$api->invoke($action, $data, $type);			//Do the Job