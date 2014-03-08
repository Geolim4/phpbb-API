<?php
/**
*
* @package phpBB3 API Core extend: Error catcher
^>@version $Id: core_catchable_error.php v0.0.1 13h37 03/08/2014 Geolim4 Exp $
* @copyright (c) 2012 - 2014 Geolim4.com http://geolim4.com
* @bug/function request: http://geolim4.com/tracker
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/
namespace phpbb_api\error_handling;
use \phpbb_api\api AS api_master, \phpbb_api\functions AS apiFN;
/**
* @ignore
*/
if (!defined('IN_PHPBB') || !defined('IN_PHPBB_API'))
{
	exit;
}

define('API_DISPLAY_FATAL_AS_HTML', true);

//Don't allow extending of api exception
final class exception extends api_master
{
	public function __construct($output)
	{
		$this->set_output($output);
		$this->load_vars();
		$this->set_headers();
		$this->load_settings();
	}

	//If the API crash, retrieve all properties before calling the error handler :/
	public function zombify(&$api, $error_handled = array())
	{
		if (is_object($api) && $api instanceof api_master)
		{
			foreach ($api AS $key_ => $api_)
			{
				$this->{$key_} = $api_;
			}
		}

		foreach ($error_handled AS $error_handled_)
		{
			api_error_handler($error_handled_['errno'], strip_tags($error_handled_['errstr']), $error_handled_['errfile'], $error_handled_['errline']);
		}
	}
}

function api_reset_error_handler()
{
	if (!defined('API_ERROR_HANDLED'))
	{
		if (!defined('E_DEPRECATED'))
		{
			define('E_DEPRECATED', 8192);
		}
		//$level = E_ALL & ~E_NOTICE & ~E_DEPRECATED;
		$level = E_ALL;
		//3.0.x UTF8 -_-
		$level &= ~E_STRICT;
		error_reporting($level);
		set_error_handler('phpbb_api\error_handling\api_error_handler', $level);
	}
}

function api_error_handler($errno, $errstr, $errfile, $errline)
{
	global $api_exception;

	if (!defined('API_ERROR_HANDLED'))
	{
		define('API_ERROR_HANDLED', true);
	}

	$error_reporting = error_reporting();

	// Do not display notices if we suppress them via @
	if ($error_reporting === 0)
	{
		return;
	}

	//Do not save errors while cron task is running
	if (defined('IN_API_CRON'))
	{
		return;
	}

	//Ignore "Cannot modify header information" notices
	if (strpos($errstr, 'Cannot modify header information') !== false)
	{
		return;
	}

	if (is_object($api_exception) && $api_exception instanceof exception)
	{
		if (in_array($errno, array(E_USER_NOTICE, E_USER_WARNING)))
		{
			$api_exception->trigger_error($errstr, $errno);
		}
		else
		{
			$api_exception->internal_error($errstr, htmlspecialchars(phpbb_filter_root_path($errfile)), $errline, $errno);
		}
	}

	/* Don't run PHP internal error handler */
	return true;
}

function generate_notice($errstr, $errfile, $errline)
{
	global $api_exception;

	if (is_object($api_exception) && $api_exception instanceof exception)
	{
		$api_exception->internal_error($errstr, htmlspecialchars(phpbb_filter_root_path($errfile)), $errline, E_NOTICE);
	}
}

function generate_warning($errstr, $errfile, $errline)
{
	global $api_exception;

	if (is_object($api_exception) && $api_exception instanceof exception)
	{
		$api_exception->internal_error($errstr, htmlspecialchars(phpbb_filter_root_path($errfile)), $errline, E_WARNING);
	}
}

function e_user_level($type, $use_bracket = false)
{
	$return = "";
	if ($type & E_ERROR) // 1 //
	{
		$return .= '& E_ERROR ';
	}
	if ($type & E_WARNING) // 2 //
	{
		$return .= '& E_WARNING ';
	}
	if ($type & E_PARSE) // 4 //
	{
		$return .= '& E_PARSE ';
	}
	if ($type & E_NOTICE) // 8 //
	{
		$return .= '& E_NOTICE ';
	}
	if ($type & E_CORE_ERROR) // 16 //
	{
		$return .= '& E_CORE_ERROR ';
	}
	if ($type & E_CORE_WARNING) // 32 //
	{
		$return .= '& E_CORE_WARNING ';
	}
	if ($type & E_COMPILE_ERROR) // 64 //
	{
		$return .= '& E_COMPILE_ERROR ';
	}
	if ($type & E_COMPILE_WARNING) // 128 //
	{
		$return .= '& E_COMPILE_WARNING ';
	}
	if ($type & E_USER_ERROR) // 256 //
	{
		$return .= '& E_USER_ERROR ';
	}
	if ($type & E_USER_WARNING) // 512 //
	{
		$return .= '& E_USER_WARNING ';
	}
	if ($type & E_USER_NOTICE) // 1024 //
	{
		$return .= '& E_USER_NOTICE ';
	}
	if ($type & E_STRICT) // 2048 //
	{
		$return .= '& E_STRICT ';
	}
	if ($type & E_RECOVERABLE_ERROR) // 4096 //
	{
		$return .= '& E_RECOVERABLE_ERROR ';
	}
	if ($type & E_DEPRECATED) // 8192 //
	{
		$return .= '& E_DEPRECATED ';
	}
	if ($type & E_USER_DEPRECATED) // 16384 //
	{
		$return .= '& E_USER_DEPRECATED ';
	}
	if (empty($return) && $type)
	{
		$return .= '& E_UNKNOW_ERRNO ';
	}
	$return = substr($return, 2, -1);

	if($use_bracket)
	{
		$return = "[ {$return} ]";
	}
	return $return; 
}

//try to catch up that fatal error !!
//using @ operator into this function to avoid potentials errors in..... Error handler :/
function fatal_api_error_handler($buffer)
{
	define('API_FATAL_ERROR_HANDLED', true);
	//Set here the hardcoded language in case PHP cannot get it from language file
	$api_fatal_error_text_exp = 'That page is a Fatal Error handled by phpBB API v' . API_VERSION . '.
					Usually this page should never appear, you have to search and fix that error quickly.
					If you can’t do it, contact the phpBB API developer <a href="http://geolim4.com/tracker.php" title="phpBB API tracker">here</a>.';

	@chdir(dirname($_SERVER['SCRIPT_FILENAME']));//Pre-caution related to ob_start()
	$error = error_get_last();

	//Ignore XCache errors !! (Xcache errors are still logged in error_log)
	if (strpos($error['message'], 'XCache: Cannot init') !== false)
	{
		return $buffer;
	}

	if (in_array($error['type'], array(E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_RECOVERABLE_ERROR)))
	{
		//Even in case of fatal error, $phpbb_root_path, $user, $config, $api_bench_start are still available grab them now!!
		global $phpbb_root_path, $user, $config, $api_bench_start;

		//Here we do not listen at all
		error_reporting(0);
		send_status_line(503, 'Service Unavailable');
		if (API_DISPLAY_FATAL_AS_HTML && !empty($config['api_mod_fatal_html']))
		{
			//Overwrite Content-Type header !
			@header('Content-Type: text/html; charset=UTF-8');

			$filename = $phpbb_root_path . 'includes/api/templates/fatal_error_handler.' . API_HTML_EXT;
			if(file_exists($filename))
			{
				$handle = @fopen($filename, "rb");
				$buffer_handled = @fread($handle, @filesize($filename));
				@fclose($filename);
			}
			else
			{
				return unrecoverable_fatal_error("Template {$filename} not found while trying to properly handle a fatal error.", true);
			}

			$vars = array(
				'ERROR_TITLE'			=> isset($user->lang['API_FATAL_ERROR']) ? $user->lang['API_FATAL_ERROR'] : 'Fatal error',
				'ERROR_TYPE'			=> e_user_level($error['type']),
				'ERROR_MSG'				=> strip_tags($error['message']),
				'ERROR_FILE'			=> htmlspecialchars(phpbb_filter_root_path($error['file'])),
				'ERROR_LINE'			=> $error['line'],
				'ERROR_DATE'			=> isset($user) ? $user->format_date(time()) : date('F j, Y, g:i:s a'),
				'ERROR_SERVER'			=> isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : '-',
				'ERROR_REQUEST_METHOD'	=> isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '-',
				'ERROR_PHP_VERSION'		=> phpversion(),
				'ERROR_TITLE_EXP'		=> isset($user->lang['API_FATAL_ERROR_TITLE_EXP']) ? $user->lang['API_FATAL_ERROR_TITLE_EXP'] : 'Explanations',
				'ERROR_TEXT_EXP'		=> isset($user->lang['API_FATAL_ERROR_TEXT_EXP']) ? $user->lang('API_FATAL_ERROR_TEXT_EXP', API_VERSION) : $api_fatal_error_text_exp,
				'API_GENERATE_TIME'		=> isset($user->lang['API_GENERATE_TIME']) ? $user->lang('API_GENERATE_TIME', round(apiFN\api_bench_end($api_bench_start), 4, PHP_ROUND_HALF_UP)) : 'Page generated in ' . round(apiFN\api_bench_end($api_bench_start), 4, PHP_ROUND_HALF_UP) . ' seconds.',
			);

			$buffer_handled = @preg_replace_callback('#\{([A-Z0-9_]*)\}#', 
				function($match) use ($vars, $user)
				{
					if (isset($vars[$match[1]]))
					{
						return $vars[$match[1]];
					}
					else
					{
						if (substr($match[1], 0, 2) == 'L_')
						{
							if (isset($user->lang[substr($match[1], 2)]))
							{
								return $user->lang[substr($match[1], 2)];
							}
							return substr($match[1], 2);
						}
						return $match[1];
					}
				},
				$buffer_handled
			);

			$error['message'] = e_user_level($error['type'], true) . ' ' . $error['message'];
			try 
			{
				apiFN\api_err_log($user->lang('API_ERROR_INTERNAL', strip_tags($error['message']), htmlspecialchars(phpbb_filter_root_path($error['file'])), $error['line']));
				apiFN\api_add_log('API_LOG_FATAL_ERROR', request_var('k', ''), array(htmlspecialchars(phpbb_filter_root_path($error['file'])), $error['line'], strip_tags($error['message'])));
			} 
			catch (Exception $e) 
			{
				error_handling\unrecoverable_fatal_error($e);
			}
			@header('Content-Length2: ' . strlen($buffer_handled));
			return $buffer_handled;
		}
		else
		{
			global $api;
			if (!empty($api))
			{
				$result = array(
					'msg' => e_user_level($error['type'], true) . ' ' . $error['message'],
					'errno' => $error['type'],
				);
				if ($api->backtrace)
				{
					$debug_backtrace = debug_backtrace();
					$debug = array();
					$i = 0;//Used to inject args var correctly...
					// We skip the first one, because it only shows this file/function
					unset($debug_backtrace[0]);
					$debug_backtrace = @array_reverse($debug_backtrace);

					foreach ($debug_backtrace as $trace)
					{
						if (isset($trace['function']) && $trace['function'] == 'api_error_handler')
						{
							continue;
						}
						$debug[$i] = array(
							'file' => isset($trace['file']) ? htmlspecialchars(phpbb_filter_root_path($trace['file'])) : "-",
							'line' => isset($trace['line']) ? $trace['line'] : "-",
							'function' => isset($trace['function']) ? $trace['function'] : "-",
							'class' => isset($trace['class']) ? $trace['class'] : "-",
							'type' => isset($trace['type']) ? $trace['type'] : "-",
						);
						if (!empty($trace['args'][0]) && in_array($trace['function'], array('include', 'require', 'include_once', 'require_once')))
						{
							$debug[$i] = array(
								'args' => $trace['args'],
							);
						}
						$i++;
					}
					if (!empty($debug))
					{
						$result['backtrace'] = $debug;
					}

					$result['status'] = '503 Service Unavailable';
				}
				try 
				{
					apiFN\api_err_log($api->user->lang('API_ERROR_INTERNAL', strip_tags($result['msg']), htmlspecialchars(phpbb_filter_root_path($error['file'])), $error['line']));
					apiFN\api_add_log('API_LOG_FATAL_ERROR', $api->api_key, array(htmlspecialchars(phpbb_filter_root_path($error['file'])), $error['line'], strip_tags($result['msg'])));
				} 
				catch (Exception $e) 
				{
					error_handling\unrecoverable_fatal_error($e);
				}
				$result = $api->display($result, true, true);
				//Here we bypass CDN like Cloudflare... So you can get it even if your CDN remove "Content-Length" header.
				header('Content-Length2: ' . strlen($result));
				return $result;
			}
			else
			{
				return unrecoverable_fatal_error("The API object is died, cannot properly handle a fatal error.", true);
			}
		}
	}
	return $buffer;
}
//Something went very wrong
//At this moment we cannot recover nothing anymore, we need to show a last message to the user :'(
function unrecoverable_fatal_error($message, $return = false)
{
	$message = "Unrecoverable fatal error: «{$message}» Please report that error to an administrator, including all parameters you passed into the API.";
	if($return)
	{
		return $message;
	}

	if(!headers_sent())
	{
		send_status_line(503, 'Service Unavailable');
	}
	echo $message;

	garbage_collection();
	exit_handler();
}