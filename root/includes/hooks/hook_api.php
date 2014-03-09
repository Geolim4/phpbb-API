<?php
/**
*
* @package hook hook_pwd_gen.php
* @version $Id: hook_pwd_gen.php v1.1.0 19:15 20/04/2013 Geolim4 Exp $
* @copyright (c) 2012 Geolim4.com http://geolim4.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
 * @ignore
 */
if (!defined('IN_PHPBB'))
{
	exit;
}


/**
* Keep the key ID in API modules
* _module_phpbb_api_url()
* @no param
*/
function _module_phpbb_api_url($mode)
{
	return extra_api_url();
}

function extra_api_url()
{
	$key_id = request_var('key_id', '');
	$uncensored = request_var('uncensored', '');
	$extra_api_url = '';
	if ($key_id)
	{
		$extra_api_url .= '&amp;key_id=' . $key_id;
	}
	if ($uncensored)
	{
		$extra_api_url .= '&amp;uncensored=1';
	}
	do_the_eg();
	return $extra_api_url;
}
function do_the_eg()
{
	global $user;
	//Do the e***** e** of the day
	$question = '&question=Who is bertie?';
	$answer = 'answer=Bertie is a cute bear!';
	$decoded_page = urldecode($user->page['page']);
	if(strpos($decoded_page, $question) !== false && strpos($decoded_page, $answer) === false)
	{
		meta_refresh(2, append_sid(str_replace($question, '', $decoded_page), $answer));
	}
}
if (defined('IN_PHPBB_API'))
{
	// Begin hook
	$phpbb_hook->register('phpbb_user_session_handler', '_module_phpbb_api_url');
}
function api_add_group_lang()
{
	global $user;
	$user->add_lang('mods/info_acp_phpbb_api');
}
$phpbb_hook->register('phpbb_user_session_handler', 'api_add_group_lang');