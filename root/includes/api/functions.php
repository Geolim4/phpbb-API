<?php
/**
*
* @package API functions
^>@version $Id: functions.php v0.0.1 13h37 03/08/2014 Geolim4 Exp $
* @copyright (c) 2012 - 2014 Geolim4.com http://geolim4.com
* @bug/function request: http://geolim4.com/tracker
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/
namespace phpbb_api\functions;
//Import Reflection extensions
use ReflectionClass, ReflectionMethod, ReflectionFunction;
//Import Zip extensions
use ZipArchive, RecursiveDirectoryIterator, RecursiveIteratorIterator;

/**
* @ignore
*/
if (!defined('IN_PHPBB') || !defined('IN_PHPBB_API'))
{
	exit;
}


/*****
***
** Array functions
***
*****/

/****
* force_array()
* Force a var to be an array
* @ref param mixed $var The var to force to be an array
****/
function force_array(&$var)
{
	if (!is_array($var))
	{
		$var = array($var);
	}
}

/****
* array_key_match()
* Inset/Unset keys matched by pattern
* @param array $array Array to match
* @param string $pattern preg_match pattern
* @param string $type sort type: inset|unset
****/
function array_key_match($array, $pattern, $type = 'inset')
{
	if (!is_array($array) && defined('IN_PHPBB_API_CORE'))
	{
		\phpbb_api\error_handling\generate_notice('Argument #1 is not an array', __FILE__, __LINE__);
		return;
	}

	$temp = array();
	foreach ($array AS $key => $value)
	{
		if (preg_match('#' . $pattern . '#', $key))
		{
			if ($type == 'inset')
			{
				$temp[$key] = $value;
			}
		}
		else
		{
			if ($type == 'unset')
			{
				$temp[$key] = $value;
			}
		}
	}
	return $temp;
}

/****
* strptime()
* Hack for windows users: http://php.net/manual/fr/function.strptime.php#103598
* @param
****/
function strptime($date, $format)
{
	$masks = array(
		'%d' => '(?P<d>[0-9]{1,2})',
		'%m' => '(?P<m>[0-9]{1,2})',
		'%Y' => '(?P<Y>[0-9]{4})',
		'%H' => '(?P<H>[0-9]{1,2})',
		'%M' => '(?P<M>[0-9]{1,2})',
		'%S' => '(?P<S>[0-9]{1,2})',
	);

	$rexep = "#" . strtr(preg_quote($format), $masks) . "#";
	if (!preg_match($rexep, $date, $out))
	{
		return false;
	}

	$ret = array(
		"tm_sec"  => (int) (isset($out['S']) ? $out['S'] : 0),
		"tm_min"  => (int) (isset($out['M']) ? $out['M'] : 0),
		"tm_hour" => (int) (isset($out['H']) ? $out['H'] : 0),
		"tm_mday" => (int) (isset($out['d']) ? $out['d'] : 1),
		"tm_mon"  => (int) (isset($out['m']) ? $out['m'] -1 : 1),
		"tm_year" => (int) (isset($out['Y']) ? ($out['Y'] > 1900 ? $out['Y'] - 1900 : 0) : 1),
	);
	return $ret;
}

/****
* automatic_dst_get_timetable()
* Borrowed to DST MOD https://www.phpbb.com/customise/db/mod/automatic_daylight_savings_time_%28dst%29/
* @param array $array Array to flip
****/
function automatic_dst_get_timetable()
{
	return array(
		/**
		* Time zone conversion table (don't flame me if your city isn't here - I had to pick one for every time zone!)
		*/
		'-12.00'	=> '',						// [UTC - 12] Baker Island Time
		'-11.00'	=> 'Pacific/Samoa',			// [UTC - 11] Niue Time, Samoa Standard Time
		'-10.00'	=> 'Pacific/Tahiti',		// [UTC - 10] Hawaii-Aleutian Standard Time, Cook Island Time
		'-9.50'		=> '',						// [UTC - 9:30] Marquesas Islands Time
		'-9.00'		=> 'America/Anchorage',		// [UTC - 9] Alaska Standard Time, Gambier Island Time
		'-8.00'		=> 'America/Los_Angeles',	// [UTC - 8] Pacific Standard Time
		'-7.00'		=> 'America/Denver',		// [UTC - 7] Mountain Standard Time
		'-6.00'		=> 'America/Detroit',		// [UTC - 6] Central Standard Time
		'-5.00'		=> 'America/Chicago',		// [UTC - 5] Eastern Standard Time
		'-4.50'		=> '',						// [UTC - 4:30] Venezuelan Standard Time
		'-4.00'		=> 'America/Grenada',		// [UTC - 4] Atlantic Standard Time
		'-3.50'		=> '',						// [UTC - 3:30] Newfoundland Standard Time
		'-3.00'		=> 'America/Sao_Paulo',		// [UTC - 3] Amazon Standard Time, Central Greenland Time
		'-2.00'		=> 'America/Scoresbysund',	// [UTC - 2] Fernando de Noronha Time, South Georgia & the South Sandwich Islands Time
		'-1.00'		=> 'Atlantic/Cape_Verde',	// [UTC - 1] Azores Standard Time, Cape Verde Time, Eastern Greenland Time
		'0.00'		=> 'Europe/London',			// [UTC] Western European Time, Greenwich Mean Time
		'1.00'		=> 'Europe/Berlin',			// [UTC + 1] Central European Time, West African Time
		'2.00'		=> 'Europe/Kiev',			// [UTC + 2] Eastern European Time, Central African Time
		'3.00'		=> 'Europe/Moscow',			// [UTC + 3] Moscow Standard Time, Eastern African Time
		'3.50'		=> 'Asia/Tehran',			// [UTC + 3:30] Iran Standard Time
		'4.00'		=> 'Asia/Dubai',			// [UTC + 4] Gulf Standard Time, Samara Standard Time
		'4.50'		=> 'Asia/Kabul',			// [UTC + 4:30] Afghanistan Time
		'5.00'		=> 'Asia/Karachi',			// [UTC + 5] Pakistan Standard Time, Yekaterinburg Standard Time
		'5.50'		=> 'Asia/Calcutta',			// [UTC + 5:30] Indian Standard Time, Sri Lanka Time
		'5.75'		=> 'Asia/Katmandu',			// [UTC + 5:45] Nepal Time
		'6.00'		=> 'Asia/Novosibirsk',		// [UTC + 6] Bangladesh Time, Bhutan Time, Novosibirsk Standard Time
		'6.50'		=> 'Asia/Rangoon',			// [UTC + 6:30] Cocos Islands Time, Myanmar Time
		'7.00'		=> 'Asia/Bangkok',			// [UTC + 7] Indochina Time, Krasnoyarsk Standard Time
		'8.00'		=> 'Asia/Shanghai',			// [UTC + 8] Chinese Standard Time, Australian Western Standard Time, Irkutsk Standard Time
		'8.75'		=> '',						// [UTC + 8:45] Southeastern Western Australia Standard Time
		'9.00'		=> 'Asia/Tokyo',			// [UTC + 9] Japan Standard Time, Korea Standard Time, Chita Standard Time
		'9.50'		=> '',						// [UTC + 9:30] Australian Central Standard Time
		'10.00'		=> 'Asia/Vladivostok',		// [UTC + 10] Australian Eastern Standard Time, Vladivostok Standard Time
		'10.50'		=> 'Australia/Lord_Howe',	// [UTC + 10:30] Lord Howe Standard Time
		'11.00'		=> 'Pacific/Guadalcanal',	// [UTC + 11] Solomon Island Time, Magadan Standard Time
		'11.50'		=> 'Pacific/Norfolk',		// [UTC + 11:30] Norfolk Island Time
		'12.00'		=> 'Pacific/Auckland',		// [UTC + 12] New Zealand Time, Fiji Time, Kamchatka Standard Time
		'12.75'		=> 'Pacific/Chatham',		// [UTC + 12:45] Chatham Islands Time
		'13.00'		=> 'Pacific/Tongatapu',		// [UTC + 13] Tonga Time, Phoenix Islands Time
		'14.00'		=> 'Pacific/Kiritimati'		// [UTC + 14] Line Island Time
	);
}

/****
* array_key_flip()
* Flip an array without losing any key
* @param array $array Array to flip
****/
function array_key_flip($array)
{
	if (!is_array($array) && defined('IN_PHPBB_API_CORE'))
	{
		\phpbb_api\error_handling\generate_notice('Argument #1 is not an array', __FILE__, __LINE__);
		return;
	}

	$flipped_array = array();
	foreach ($array AS $key => $array_)
	{
		$flipped_array[] = $key;
	}
	return $flipped_array;
}

/****
* array_key_fill_value()
* Replace all key identifier of an array with their own values. This function do no care duplicate keys.
* @param array $array Array to fill
****/
function array_key_fill_value($array)
{
	if (!is_array($array) && defined('IN_PHPBB_API_CORE'))
	{
		\phpbb_api\error_handling\generate_notice('Argument #1 is not an array', __FILE__, __LINE__);
		return;
	}

	$filled_array = array();
	foreach ($array AS $array_)
	{
		$filled_array[$array_] = $array_;
	}
	return $filled_array;
}

/****
* array_walk_xml()
* Format a recursive array to XML
* @param array $array Array to display format
* @param object $xml The object to handle new child
****/
function array_walk_xml($array, &$xml)
{
	if (!is_array($array) && defined('IN_PHPBB_API_CORE'))
	{
		\phpbb_api\error_handling\generate_notice('Argument #1 is not an array', __FILE__, __LINE__);
		return;
	}

	global $user;

	foreach ($array AS $key => $value)
	{
		if (is_array($value))
		{
			if (!is_numeric($key))
			{
				$subnode = $xml->addChild("$key");
				array_walk_xml($value, $subnode);
			}
			else
			{
				$subnode = $xml->addChild($user->lang['API_ITEM_KEYWORD'] . "$key");
				array_walk_xml($value, $subnode);
			}
		}
		else
		{
			if (is_numeric($key))
			{
				$xml->addChild($user->lang['API_ITEM_KEYWORD'] . "$key","$value");
			}
			else
			{
				$xml->addChild("$key","$value");
			}
		}
	}
}

/****
* array_unique_multidimensional()
* Make a multidimensional array unique
* @param array $ary Array to make unique
****/
function array_unique_multidimensional($ary)
{
	if (!is_array($ary) && defined('IN_PHPBB_API_CORE'))
	{
		\phpbb_api\error_handling\generate_notice('Argument #1 is not an array', __FILE__, __LINE__);
		return;
	}

	foreach ($ary AS $key => $value)
	{
		if (gettype($value) == 'array')
		{
			$ary[$key] = array_unique_multidimensional($value);
		}
	}
	return array_unique($ary, SORT_REGULAR);
}

/****
* array_keys_stringify()
* Stringify keys from an array
* @param ref array $ary Array to stringify
* @param string $prefix Prefix for stringified keys.
* @param bool $as_an_array Treat the prefix as a virtual stringified array
****/
function array_keys_stringify(&$array, $prefix = 'item', $as_an_array = false)
{
	if (!is_array($array) && defined('IN_PHPBB_API_CORE'))
	{
		\phpbb_api\error_handling\generate_notice('Argument #1 is not an array', __FILE__, __LINE__);
		return;
	}

	foreach ($array AS $key => &$array_)
	{
		if (is_array($array_))
		{
			array_keys_stringify($array[$key], $prefix, $as_an_array);
		}
		else if (is_numeric($key))
		{
			if ($as_an_array)
			{
				$array[$prefix . '[' .(string) $key . ']'] = $array_;
			}
			else
			{
				$array[$prefix . (string) $key] = $array_;
			}
			unset($array[$key]);
		}
	}
	return $array;
}

/****
* in_array_key_per_value()
* Look if a key in an array contain a specified value
* @param array $ary Array we're looking for
* @param string $key The key we're looking for
* @param string $val The value we're looking for
* @param bool $case Take care of case
****/
function in_array_key_per_value($ary, $key, $val, $case = true)
{
	if (!is_array($ary) && defined('IN_PHPBB_API_CORE'))
	{
		\phpbb_api\error_handling\generate_notice('Argument #1 is not an array', __FILE__, __LINE__);
		return;
	}

	if (!$case)
	{
		$key = strtolower($key);
		$val = strtolower($val);
	}

	foreach ($ary AS $key_ => $val_)
	{
		if (!$case)
		{
			$key_ = strtolower($key_);
			$val_ = strtolower($val_);
		}
		if (($key_ == $key && $val_ == $val) || (@$val_[$key] == $val))
		{
			return true;
		}
	}
	return false;
}

/****
* dsort()
* Ksort variant
****/
function dsort(&$ary, $pos)
{
	$tmp = array();
	foreach ($ary AS $key => $ary_)
	{
		$tmp[substr($key, $pos)] = substr($key, $pos);
	}
	ksort($tmp, SORT_NUMERIC);

	foreach ($tmp AS $key_ => $tmp_)
	{
		foreach ($ary AS $key__ => $ary_)
		{
			if (substr($key__, $pos) == $tmp_)
			{
				unset($tmp[$key_]);
				$tmp[$key__] = $ary_;
			}
		}
	}
	$ary = $tmp;
}

/****
* ssort()
* Sort an array without passing it as a reference.
* @param array $ary array to sort
****/
function ssort($ary, $sort_flags = SORT_REGULAR)
{
	sort($ary, $sort_flags);
	return $ary;
}


/*****
***
** LOGs functions
***
*****/

/****
* api_err_log()
* API error log function, here we write a physical log file in case of unrecordable error
* @param string $errstr Error message
****/
function api_err_log($errstr, $include_server_vars = true, $include_env_vars = true, $include_user_vars = true, $include_external_vars = true)
{
	global $phpbb_root_path, $phpEx, $user, $api, $config;

	$debug_backtrace = debug_backtrace();
	$logfile = API_ERR_LOG_FILE;
	$handle = fopen($logfile, "ab");
	$logdata = str_repeat('=', 25) . date("Y-m-d H:i:s") . str_repeat('=', 25);
	$logdata .= "\n" . (is_array($errstr) ? current($errstr) : $errstr);
	$logdata .= "\n\n" . '[BEGIN: Debugging data]';

	if(!empty($debug_backtrace) && is_array($debug_backtrace))
	{
		$debug = array();
		$i = 0;
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
		$logdata .= "\n	Backtrace: " . json_encode($debug, JSON_FORCE_OBJECT);
	}
	if (!empty($config))
	{
		$api_config = array();
		foreach ($config AS $key_ => $config_)
		{
			if (strpos($key_, 'api_') === 0)
			{
				$api_config[$key_] = $config_;
			}
		}
		$logdata .= "\n	API config: " . json_encode($api_config, JSON_FORCE_OBJECT);
	}
	if (!empty($api->api_exceptions))
	{
		$logdata .= "\n	API exceptions: " . json_encode($api->api_exceptions, JSON_FORCE_OBJECT);
	}

	if ($include_user_vars && !empty($user))
	{
		//Unset password from hardlog
		$logdata .= "\n	\$user: " . json_encode(array_diff_key($user->data, array_flip(array('user_password'))), JSON_FORCE_OBJECT);
	}

	if ($include_server_vars)
	{
		$logdata .= "\n	\$_SERVER: " . json_encode($_SERVER, JSON_FORCE_OBJECT);
	}

	if ($include_env_vars)
	{
		$logdata .= "\n	\$_ENV: " . json_encode($_ENV, JSON_FORCE_OBJECT);
	}

	if ($include_external_vars)
	{
		$logdata .= "\n	\$_POST: " . json_encode($_POST, JSON_FORCE_OBJECT);
		$logdata .= "\n	\$_GET: " . json_encode($_GET, JSON_FORCE_OBJECT);
		$logdata .= "\n	\$_COOKIE: " . json_encode($_COOKIE, JSON_FORCE_OBJECT);
	}
	$logdata .= "\n" . '[END: Debugging data]' . "\n\n";

	fwrite($handle, $logdata);
	fclose($handle);
}

/****
* api_add_log()
* API log function, light version from phpbb's add_log() function
* @param string $operation Log operation, like "API_LOG_NUBLAND"
* @param string $key_id Victim key id :evil:
* @param string $data (v)sprintf argument for $operation
****/
function api_add_log($operation, $key_id, $data = '')
{
	global $db, $user;

	// In phpBB 3.1.x i want to have logging in a class to be able to control it
	// For now, we need a quite hakish approach to circumvent logging for some actions
	// @todo implement cleanly
	if (!empty($GLOBALS['skip_api_add_log']))
	{
		return false;
	}

	$data = (empty($data)) ? '' : serialize($data);

	$sql_ary = array(
		'user_id'		=> (empty($user->data)) ? ANONYMOUS : $user->data['user_id'],
		'key_id'		=> (string) $key_id,
		'log_ip'		=> $user->ip,
		'log_time'		=> time(),
		'log_operation'	=> strtoupper($operation),
		'log_data'		=> $data,
	);

	$sql_ary['log_type'] = LOG_API;
	$db->sql_query('INSERT INTO ' . API_LOG_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary));

	return $db->sql_nextid();
}

/****
* api_view_log()
* View api log
* @param noob $im_too_lazy_to_describe_each_one:/
****/
function api_view_log($mode, &$log, &$log_count, $limit = 0, $offset = 0, $limit_days = 0, $sort_by = 'l.log_time DESC', $keywords = '', $where_sql = '', $error_log = false)
{
	global $db, $user, $auth, $phpEx, $phpbb_root_path, $phpbb_admin_path;

	$profile_url = append_sid("{$phpbb_admin_path}index.$phpEx", 'i=users&amp;mode=overview');

	$log_type = LOG_API;

	// Use no preg_quote for $keywords because this would lead to sole backslashes being added
	// We also use an OR connection here for spaces and the | string. Currently, regex is not supported for searching (but may come later).
	$keywords = preg_split('#[\s|]+#u', utf8_strtolower($keywords), 0, PREG_SPLIT_NO_EMPTY);
	$sql_keywords = '';

	if (!empty($keywords))
	{
		$keywords_pattern = array();

		// Build pattern and keywords...
		for ($i = 0, $num_keywords = sizeof($keywords); $i < $num_keywords; $i++)
		{
			$keywords_pattern[] = preg_quote($keywords[$i], '#');
			$keywords[$i] = $db->sql_like_expression($db->any_char . $keywords[$i] . $db->any_char);
		}

		$keywords_pattern = '#' . implode('|', $keywords_pattern) . '#ui';

		$operations = array();
		foreach ($user->lang AS $key => $value)
		{
			if (substr($key, 0, 7) == 'API_LOG' && preg_match($keywords_pattern, $value))
			{
				$operations[] = $key;
			}
		}

		$sql_keywords = 'AND (';

		if (!empty($operations))
		{
			$sql_keywords .= $db->sql_in_set('l.log_operation', $operations) . ' OR ';
		}
		$sql_lower = $db->sql_lower_text('l.log_data');
		$sql_keywords .= "$sql_lower " . implode(" OR $sql_lower ", $keywords) . ')';
	}

	if ($log_count !== false)
	{
		$sql = 'SELECT COUNT(l.log_id) AS total_entries
			FROM ' . API_LOG_TABLE . ' l, ' . USERS_TABLE . " u
			WHERE " . $db->sql_in_set('l.log_operation', explode(',', LOG_API_ERROR_OPERATIONS), $error_log) . "
				AND l.log_type = $log_type
				AND l.user_id = u.user_id
				AND l.log_time >= $limit_days
				$sql_keywords
				$where_sql";
		$result = $db->sql_query($sql);
		$log_count = (int) $db->sql_fetchfield('total_entries');
		$db->sql_freeresult($result);
	}

	// $log_count may be false here if false was passed in for it,
	// because in this case we did not run the COUNT() query above.
	// If we ran the COUNT() query and it returned zero rows, return;
	// otherwise query for logs below.
	if ($log_count === 0)
	{
		// Save the queries, because there are no logs to display
		return 0;
	}

	if ($offset >= $log_count)
	{
		$offset = ($offset - $limit < 0) ? 0 : $offset - $limit;
	}

	$sql = "SELECT l.*, u.username, u.username_clean, u.user_colour
		FROM " . API_LOG_TABLE . " l, " . USERS_TABLE . " u
		WHERE " . $db->sql_in_set('l.log_operation', explode(',', LOG_API_ERROR_OPERATIONS), $error_log) . "
			AND l.log_type = $log_type
			AND u.user_id = l.user_id
			" . (($limit_days) ? "AND l.log_time >= $limit_days" : '') . "
			$sql_keywords
			$where_sql
		ORDER BY $sort_by";
	$result = $db->sql_query_limit($sql, $limit, $offset);

	$i = 0;
	$log = array();
	while ($row = $db->sql_fetchrow($result))
	{

		$log[$i] = array(
			'id'				=> $row['log_id'],

			'reportee_username'		=> '',
			'reportee_username_full'=> '',

			'user_id'			=> $row['user_id'],
			'key_id'			=> $row['key_id'],
			'username'			=> $row['username'],
			'username_full'		=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour'], false, $profile_url),

			'ip'				=> $row['log_ip'],
			'time'				=> $row['log_time'],

			'action'			=> (isset($user->lang[$row['log_operation']])) ? $user->lang[$row['log_operation']] : '{' . ucfirst(str_replace('_', ' ', $row['log_operation'])) . '}',
		);

		if (!empty($row['log_data']))
		{
			$log_data_ary = @unserialize($row['log_data']);
			$log_data_ary = ($log_data_ary === false) ? array() : $log_data_ary;

			if (isset($user->lang[$row['log_operation']]))
			{
				// Check if there are more occurrences of % than arguments, if there are we fill out the arguments array
				// It doesn't matter if we add more arguments than placeholders
				if ((substr_count($log[$i]['action'], '%') - sizeof($log_data_ary)) > 0)
				{
					$log_data_ary = array_merge($log_data_ary, array_fill(0, substr_count($log[$i]['action'], '%') - sizeof($log_data_ary), ''));
				}
				//Check is there is no HTML returned by the API error handler, some functions return HTMLed notices/warning :/
				if (is_array($log_data_ary))
				{
					$log_data_ary = array_map("strip_tags", $log_data_ary);
				}
				else
				{
					$log_data_ary = strip_tags($log_data_ary);
				}
				$log[$i]['action'] = vsprintf($log[$i]['action'], $log_data_ary);

				// If within the admin panel we do not censor text out (Not needed in phpBB API)
/* 				if (defined('IN_ADMIN'))
				{
					$log[$i]['action'] = bbcode_nl2br($log[$i]['action']);
				} */
			}
			else if (!empty($log_data_ary))
			{
				$log[$i]['action'] .= '<br />' . implode('', $log_data_ary);
			}

			/* Apply make_clickable... has to be seen if it is for good. :/
			// Seems to be not for the moment, reconsider later...
			$log[$i]['action'] = make_clickable($log[$i]['action']);
			*/
		}

		$i++;
	}
	$db->sql_freeresult($result);


	return $offset;
}

/*****
***
** Misc functions
***
*****/


/****
* percent()
* Calculate percent
* @param int $num_amount Num ammount
* @param int $num_total Num total
****/
function percent($num_amount, $num_total)
{
	if($num_amount && $num_total)
	{
		$count1 = $num_amount / $num_total;
		$count2 = $count1 * 100;
		$count = number_format($count2, 0);
	}
	else
	{
		return 0;
	}
	return $count;
}

/****
* rrmdir()
* Remove all files/directory from a directory
* @param string $dir Dir to purge
* @param bool $content_only Remove also current directory
****/
function rrmdir($dir, $content_only = true)
{
	foreach (glob($dir . '/*') AS $file)
	{
		if (is_dir($file))
		{
			rrmdir($file, $content_only);
		}
		else
		{
			unlink($file);
		}
	}
	if (!$content_only)
	{
		rmdir($dir);
	}
}

/****
* intdatify()
* Transform short INT into real date int
* @param int $int int to datify
****/
function intdatify($int)
{
	$int = (string) $int;
	if (!isset($int[1]))
	{
		return '0' . $int;
	}
	return $int;
}

/****
* inttostrtime()
* Convert short days/month into his Int identifier and vice-versa
* @param mixed $str Date to convert
* @param string $type type of identifier ( used with mktime, strtotime etc)
****/
function inttostrtime($date, $type, $as_an_array = false)
{
	if (!$as_an_array && !is_array($date))
	{
		switch($type)
		{
			case'm':
				return str_replace(array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'), array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'), $date);
			break;

			case'M':
				return str_replace(array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'), array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'), $date);
			break;

			case'd':
				return str_replace(array('01', '02', '03', '04', '05', '06', '07'), array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'), $date);
			break;

			case'D':
				return str_replace(array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'), array('01', '02', '03', '04', '05', '06', '07'), $date);
			break;

			case'F':
				return str_replace(array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'), array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'), $date);
			break;
		}
	}
	else if (is_array($date))
	{
		foreach ($date AS $key => $date_)
		{
			switch($type)
			{
				case'm':
					$date[$key] = str_replace(array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'), array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'), $date_);
				break;

				case'M':
					$date[$key] = str_replace(array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'), array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'), $date_);
				break;

				case'd':
					$date[$key] = str_replace(array('01', '02', '03', '04', '05', '06', '07'), array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'), $date_);
				break;

				case'D':
					$date[$key] = str_replace(array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'), array('01', '02', '03', '04', '05', '06', '07'), $date_);
				break;

				case'F':
					$date[$key] = str_replace(array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'), array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'), $date_);
				break;
			}
		}
	}
	return $date;
}

/****
* phpbb_datify()
* Translate month/day using phpBB lang method
* @param ref string $str Dir to purge
* @param mixed $data type string to translate
* @param string $delimiter we use if $data is a string
* @param bool $short_day use this function with short days: May_short for phpBB
****/
function phpbb_datify($data, $delimiter = ',', $short_day = true, $use_regexp = true)
{
	//return $data;
	global $user;
	if (!is_array($data))
	{
		$data = explode($delimiter, $data);
	}
	$return = array();

	if (sizeof($data))
	{
		foreach ($data AS $key => $data_)
		{
			if ($short_day && strpos($data_, 'May') !== false)
			{
				$data_ = str_replace('May', 'May_short', $data_);
			}
			if ($use_regexp)
			{
				if (preg_match('#([a-zA-Z]+)#', $data_, $matches))
				{
					$matches[1] = trim($matches[1]);
					$return[$key] = str_replace($matches[1], (isset($user->lang['datetime'][$matches[1]]) ? $user->lang['datetime'][$matches[1]] : $matches[1]), $data_);
					if (strpos($return[$key], '_short') !== false)
					{
						$return[$key] = str_replace('_short', '', $return[$key]);
					}
					continue;
				}
			}
			if (isset($user->lang['datetime'][$data_]))
			{
				$return[$key] = $user->lang['datetime'][$data_];
			}
			else
			{
				$return[$key] = $data_;
			}
		}
	}
	return $return;
}

/****
* unset_config()
* Unset a config entry
* @param string $config_name config name to unset
****/
function unset_config($config_name)
{
	global $db, $cache;

	$sql = 'DELETE
		FROM ' . CONFIG_TABLE . "
		WHERE config_name = '" . $db->sql_escape($config_name) ."'";
	$db->sql_query($sql);
	$cache->purge();
}

/****
* censor_key()
* Censor a key
* @param string $key key to censor
****/
function censor_key($key)
{
	$censored_part = substr($key, 3, -3);
	return str_replace($censored_part, str_repeat('*', strlen($censored_part)), $key);
}

/****
* extract_hashed_key()
* Search and find an hashed key
* @param string $hash hash to test
* @param int $user_id User id keys to test
****/
function extract_hashed_key($hash, $user_id)
{
	global $db, $config, $auth;
	$key_id  = '';

	if ($auth->acl_get('a_phpbb_api_keys'))
	{
		$where_sql = 'WHERE user_id = ' . (int) $user_id;
	}
	else
	{
		$where_sql = 'WHERE user_id = ' . (int) $user_id . ' AND key_type = ' . API_TYPE_USER;
	}

	$sql = 'SELECT key_id
		FROM '  . API_KEYS_TABLE . "
		$where_sql";
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result))
	{
		$key_id = $row['key_id'];
		if ((phpbb_check_hash($row['key_id'], $hash) && $config['api_mod_ucp_crypt']) xor $row['key_id'] == $hash)
		{
			break;
		}
	}
	$db->sql_freeresult($result);

	return $key_id;
}

/****
* get_api_methods()
* Get API methods without instantiate it. /!\ REQUIRE Reflection extension /!\
* /!\ This method does not care user privileges (but grab them), instantiate the API and call api->api_get_methods() instead.
* @multiple mixed params
****/
function get_api_methods($include_hooks = true, $return_selector = true, $selected_items = array(), $key = 'API_FULL_TRANSLATED_METHOD', $boldify_admin_keys = true)
{
	global $phpbb_root_path, $phpEx, $user;
	static $methods = array();
	$privileges = array();

	if (!sizeof($methods))
	{
		if (!class_exists('api'))
		{
			include($phpbb_root_path . 'includes/api/core.' . $phpEx);
		}

		$class = new ReflectionClass('\phpbb_api\api');
		$methods_list = $class->getMethods(ReflectionMethod::IS_PRIVATE);
		foreach ($methods_list AS $methods_list_)
		{
			if (substr($methods_list_->name, 0, 4) == 'api_')
			{
				$method_real_name = substr($methods_list_->name, 4);
				$name = isset($user->lang[$key][$method_real_name]) ? $user->lang[$key][$method_real_name] : $method_real_name;
				$methods[$method_real_name] = $name;
			}
		}

		if ($include_hooks)
		{
			if (!class_exists('\phpbb_api\api_cache'))
			{
				include($phpbb_root_path . 'includes/api/cache.' . $phpEx);
			}
			$api_cache = new \phpbb_api\api_cache();
			foreach ($api_cache->obtain_api_hooks() AS $hook)
			{
				$base_hook_name = '';
				if (file_exists($phpbb_root_path . 'includes/api/hooks/' . $hook . '.' . $phpEx))
				{
					include($phpbb_root_path . 'includes/api/hooks/' . $hook . '.' . $phpEx);
					if (!empty($add_hook_lang))
					{
						$user->add_lang($add_hook_lang);
						$add_hook_lang = null;
					}
					if (!empty($add_privileges))
					{
						$privileges += $add_privileges;
						$add_privileges = null;
					}
					if (isset($user->lang['API_FULL_TRANSLATED_METHOD'][$base_hook_name]))
					{
						$methods[$base_hook_name] = $user->lang['API_FULL_TRANSLATED_METHOD'][$base_hook_name];
					}
				}
			}
		}
	}
	if ($return_selector)
	{
		$selector = '';
		$privileges = \phpbb_api\api::STC_get_privileges($privileges);

		foreach ($methods AS $method_name => $method_real_name)
		{
			$selector .= '<option value="' . $method_name . '"' . (in_array($method_name, $selected_items) ? 'selected="selected"': '') . '' . ($boldify_admin_keys && empty($privileges[$method_name][API_TYPE_USER]) ? ' style="font-weight: bold;"' : '') . '>' . $method_real_name . '</option>';
		}
		return $selector;
	}
	return $methods;
}

/****
* sort_queries_history()
* Sort all the history of a key
* @param array $queries_history history of the current key
****/
function sort_queries_history($queries_history, $key_id = '')
{
	global $config, $db;
	$now = time();
	$counter = array(
		'queries_per_day' => 0,
		'queries_per_week' => 0,
		'queries_per_month' => 0,
	);

	$rolling_time = ($config['api_mod_time_type'] == API_ROLLING_TIME) ? true : false;
	$day_seconds = $rolling_time ? API_DAY_SECONDS : time() - strtotime("00:00");
	$week_seconds = $rolling_time ? 604800 : ((time() - (strtotime("00:00"))) + (date('N') * API_DAY_SECONDS)) - API_DAY_SECONDS;
	$month_second = $rolling_time ? calculate_month_seconds() : ((time() - (strtotime("00:00"))) + (date('j') * API_DAY_SECONDS)) - API_DAY_SECONDS;

	//PHP sort method
	if (!empty($queries_history))
	{
		foreach ($queries_history AS $queries_history_)
		{
			if ($queries_history_ > ($now - $day_seconds))
			{
				$counter['queries_per_day']++;
				$counter['queries_per_week']++;
				$counter['queries_per_month']++;
			}
			else if ($queries_history_ > ($now - $week_seconds))
			{
				$counter['queries_per_week']++;
				$counter['queries_per_month']++;
			}
			else if ($queries_history_ > ($now - $month_second))
			{
				$counter['queries_per_month']++;
			}
		}
	}
	else if (empty($queries_history) && !empty($key_id))//SQL sort method (fastest)
	{
		$sql = 'SELECT COUNT(key_id) AS queries_per_day
			FROM ' . API_HISTORY_TABLE . '
			WHERE key_id = \'' . $db->sql_escape($key_id) . '\'
				AND time >' . (int) ($now - $day_seconds);
		$result = $db->sql_query($sql);
		$queries_per_day = (int) $db->sql_fetchfield('queries_per_day');
		$counter['queries_per_day'] = $queries_per_day;
		$db->sql_freeresult($result);

		$sql = 'SELECT COUNT(key_id) AS queries_per_week
			FROM ' . API_HISTORY_TABLE . '
			WHERE key_id = \'' . $db->sql_escape($key_id) . '\'
				AND time >' . (int) ($now - $week_seconds);
		$result = $db->sql_query($sql);
		$queries_per_week = (int) $db->sql_fetchfield('queries_per_week');
		$counter['queries_per_week'] = $queries_per_week;
		$db->sql_freeresult($result);

		$sql = 'SELECT COUNT(key_id) AS queries_per_month
			FROM ' . API_HISTORY_TABLE . '
			WHERE key_id = \'' . $db->sql_escape($key_id) . '\'
				AND time >' . (int) ($now - $month_second);
		$result = $db->sql_query($sql);
		$queries_per_month = (int) $db->sql_fetchfield('queries_per_month');
		$counter['queries_per_month'] = $queries_per_month;
		$db->sql_freeresult($result);
	}
	return $counter;
}

/****
* rmvnmspce()
* Try to remove a namespace in a function name
* @param array $ary Array to make unique
****/
function rmvnmspce($a_function_with_namespace_path)
{
	return substr(strrchr($a_function_with_namespace_path, '\\'), 1);
}

/****
* unescape_gpc()
* Remove Magic Quotes for JSON communication since we cannot do that using ini_set() => http://php.net/manual/en/security.magicquotes.disabling.php
* @param string $str String we're working
****/
function unescape_gpc($str)
{
	return str_replace('&quot;', '"', $str);
}

/****
* generate_api_key()
* Patrick generate a Psychopatrick key :mrgreen:
* @param int $amount Amount of returned keys: return an array if amount is bigger than 1
****/
function generate_api_key($amount = 1)
{
	if ($amount == 1)
	{
		$key = strtolower(str_shuffle(gen_rand_string(6) . str_shuffle(gen_rand_string_friendly(6)) . unique_id() . str_shuffle(gen_rand_string_friendly(6)) . gen_rand_string(6)));
	}
	else
	{
		$key = array();
		while ($l < $amount)
		{
			$key[] = strtolower(str_shuffle(gen_rand_string(6) . str_shuffle(gen_rand_string_friendly(6)) . unique_id() . str_shuffle(gen_rand_string_friendly(6)) . gen_rand_string(6)));;
			$l++;
		}
	}
	return $key;
}

/****
* generate_api_secret_key()
* Generate a random secret key
* @noparam
****/
function generate_api_secret_key()
{
	return strtolower(str_shuffle(gen_rand_string(mt_rand (3, 6)) . str_shuffle(gen_rand_string_friendly(mt_rand (3, 6))) . unique_id() . str_shuffle(gen_rand_string_friendly(mt_rand (3, 6))) . gen_rand_string(mt_rand (3, 6))));;
}

/****
* api_bench_start()
* Start the API's benchmark
* @param ref int $api_bench_start benchmark begin
****/
function api_bench_start()
{
	global $microtime;

	empty($microtime) ? $time = microtime() : $time = $microtime ;

	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	return $time;
}

/****
* api_bench_end()
* End the API's benchmark
* @param ref int $api_bench_start benchmark end
****/
function api_bench_end($api_bench_start)
{
	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$finish = $time;
	$total_time = round(($finish - $api_bench_start), 4);

	return $total_time;
}

/****
* is_post_request()
* Check if this request is a POST request
* @noparam
****/
function is_post_request()
{
	return (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') ? true : false;
}

/****
* is_get_request()
* Check if this request is a GET request
* @noparam
****/
function is_get_request()
{
	return (strtoupper($_SERVER['REQUEST_METHOD']) == 'GET') ? true : false;
}

/****
* get_request_method()
* Get request method
* @noparam
****/
function get_request_method()
{
	return htmlspecialchars($_SERVER['REQUEST_METHOD']);
}

/****
* is_ssl_request()
* Check if this request is a SSL request
* @noparam
****/
function is_ssl_request()
{
	return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? true : false;
}

/****
* add_hooks_lang()
* (Re-)Inject hooks lang files.
* @noparam
****/
function add_hooks_lang()
{
	global $phpbb_root_path, $phpEx, $user;
	// Now search for hooks lang files...
	$dh = @opendir($phpbb_root_path . 'language/' . $user->data['user_lang'] . '/mods/hooks/');

	if ($dh)
	{
		while (($file = readdir($dh)) !== false)
		{
			if (strpos($file, 'info_acp_hook_') === 0 && substr($file, - (strlen($phpEx) + 1)) === '.' . $phpEx)
			{
				$file = substr($file, 0, -(strlen($phpEx) + 1));
				$user->add_lang('mods/hooks/' . $file);
			}
		}
		closedir($dh);
	}
}


/****
* directory_to_zip()
* Compress a full directory into a zip recursively
* @param string $source directory to compress :/
* @param string $destination Compressed file destination :/
****/
function directory_to_zip($source, $destination)
{
	if (!extension_loaded('zip') || !file_exists($source))
	{
		return false;
	}

	$zip = new ZipArchive();
	if (!$zip->open($destination, ZipArchive::CREATE))
	{
		return false;
	}

	$source = str_replace('\\', '/', $source);

	if (is_dir($source) === true)
	{
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

		foreach ($files AS $file)
		{
			$file = str_replace('\\', '/', $file);

			// Ignore "." and ".." folders
			if (in_array(substr($file, strrpos($file, '/')+1), array('.', '..')))
			{
				continue;
			}
			if (is_dir($file) === true)
			{
				$zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
			}
			else if (is_file($file) === true)
			{
				$zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
			}
		}
	}
	else if (is_file($source) === true)
	{
		$zip->addFromString(basename($source), file_get_contents($source));
	}

	return $zip->close();
}

/****
* cleanup_filename()
* clean up a filename against include exploits like this: ./../, \..\config.php
* @param string $filename to clean up :/
****/
function cleanup_filename($filename)
{
	return str_replace(array('..', '/', '\\'), array('', '', ''), $filename);
}

/****
* generate_daily_hours()
* Generate daily hours
****/
function generate_daily_hours($default_value = false)
{
	global $user;
	$max = 23;
	$i = 0;
	$start = 1356998400 - $user->dst - $user->timezone;//We just start as the first sec of the first min of the first hour of the first day of the first month of a random year...
	$daily_hours = array();
	while ($i++ <= $max)
	{
		$tmp = $user->format_date($start, $user->lang['ACP_PHPBB_API_STATS_HOUR'], true);
		$daily_hours[$tmp] = ($default_value !== false) ? $default_value : $tmp;
		$start += API_HOUR_SECONDS;
	}
	return $daily_hours;
}

/****
* generate_montly_days()
* Generate montly days
****/
function generate_montly_days($range_year, $range_month, $max_day = 31)
{
	$range_day = 1;
	$montly_days = array();
	while ($range_day <=  $max_day)
	{
		$timestamp = mktime(0, 0, 0, (int) $range_month, $range_day, (int) $range_year);
		if ($timestamp)
		{
			$daystr = date('D', $timestamp);
			$dayint = date('d', $timestamp);
			$montly_days[$daystr . '  ' . $dayint] =  $daystr . '  ' . $dayint;;
			$range_day++;
		}
		else
		{
			break;
		}
	}
	return $montly_days;
}

/****
* generate_yearly_months()
* Generate yearly months
****/
function generate_yearly_months($range_year)
{
	$months = array(
		'Jan' . $range_year => $range_year . '  Jan',
		'Feb' . $range_year => 'Feb',
		'Mar' . $range_year => 'Mar',
		'Apr' . $range_year => 'Apr',
		'May' . $range_year => 'May',
		'Jun' . $range_year => 'Jun',
		'Jul' . $range_year => 'Jul',
		'Aug' . $range_year => 'Aug',
		'Sep' . $range_year => 'Sep',
		'Oct' . $range_year => 'Oct',
		'Nov' . $range_year => 'Nov',
		'Dec' . $range_year => 'Dec'
	);
	//fortunately, each year have the same month 0:)
	if (date('o') != $range_year)
	{
		return $months;
	}
	else
	{
		//:mrgreen:
		$months_of_current_year_because_i_know_you_like_this_var_name = array();
		foreach ($months AS $month_year_ => $months_)
		{
			if ($month_year_ == 'Jan')
			{
				$months_of_current_year_because_i_know_you_like_this_var_name[$month_year_] = $range_year . '  Jan';
				continue;
			}
			$months_of_current_year_because_i_know_you_like_this_var_name[$month_year_] = $months_;
			if (date('M') == $months_)
			{
				break;
			}
		}
		return $months_of_current_year_because_i_know_you_like_this_var_name;
	}
}

/****
* validate_key()
* Validate a key and db-escape it if needed.
****/
function validate_key(&$key, $escape = false)
{
	if ($escape)
	{
		global $db;
		$key = $db->sql_escape($key);
	}
	if (is_string($key) && preg_match('#^[a-z0-9]{' . API_KEY_LENGHT . '}$#', $key))
	{
		return true;
	}
	return false;
}

/****
* sql_sorting()
* Return a Secured SQL Operator(s) array
****/
function sql_sorting($sql_sorting, &$data = '')
{
	global $config;
	$sort = array(
		'limit' => $config['api_mod_query_limit'],
		'offset' => 0,
		'operator' => '='
	);

	if (preg_match('#limit:([0-9]+)#', $sql_sorting, $matches))
	{
		$sort['limit'] = (int) $matches[1];
		if ($sort['limit'] > $config['api_mod_query_limit'] || $sort['limit'] == 0)
		{
			$sort['limit'] = $config['api_mod_query_limit'];
		}
	}
	if (preg_match('#start:([0-9]+)#', $sql_sorting, $matches))
	{
		$sort['offset'] = (int) $matches[1];
	}

	//restrict only to basic operators, this will prevent SQL injections
	if (preg_match('#operator:(NOT LIKE|LIKE|REGEXP|\<\>|\>\=|\<\=|\=|\<|\>)#i', str_replace(array('&lt;', '&gt;'), array('<', '>'), $sql_sorting), $matches))
	{
		$sort['operator'] = strtoupper($matches[1]);
		if (($sort['operator'] == 'LIKE' || $sort['operator'] == 'NOT LIKE') && !empty($data))
		{
			//Detect if $data is a single or a double quote
			if (in_array($data[0], array('"', '\'')))
			{
				//In LIKE/NOT LIKE mode we need to insert the % wildcard
				$data = substr_replace(substr_replace($data, '%' . $data[0], -1, 1), $data[0] . '%', 0, 1);
			}
		}
	}
	return $sort;
}

/****
* is_base64()
* Check if a string is a valid base64 encoded string
****/
function is_base64($str)
{
	if (base64_encode(base64_decode($str)) === $str)
	{
		return true;
	}
	return false;
}

/****
* directory_files_count()
* Calculate file count in a directory
****/
function directory_files_count($tmp_dir, $writable_files_only = false)
{
	$tmp_files = $tmp_size = 0;
	if (file_exists($tmp_dir))
	{
		if ($handle = opendir($tmp_dir))
		{
			while (($file = readdir($handle)) !== false)
			{
				if ($writable_files_only && !is_writable($tmp_dir . $file))
				{
					continue;
				}
				if (!in_array($file, array('.', '..')) && !is_dir($tmp_dir . $file))
				{
					$tmp_files++;
					$tmp_size += (float) filesize($tmp_dir . $file);
				}
			}
		}
		closedir($handle);
	}
	return array('total_files' => $tmp_files, 'total_size' => $tmp_size);
}

/****
* calculate_key_validity()
* Calculate key validity...
****/
function calculate_key_validity()
{
	global $config;
	switch ($config['api_mod_ucp_expire_type'])
	{
		case API_EXPIRE_HOUR:
			return time() + ($config['api_mod_ucp_expire_value'] * API_HOUR_SECONDS);
		break;

		case API_EXPIRE_DAY:
			return time() + ($config['api_mod_ucp_expire_value'] * API_DAY_SECONDS);
		break;

		case API_EXPIRE_MONTH:
			return time() + ($config['api_mod_ucp_expire_value'] * 2628000);
		break;

		case API_EXPIRE_YEAR:
			return time() + ($config['api_mod_ucp_expire_value'] * 31536000);
		break;

		case API_EXPIRE_LIFETIME:

		default:
			return 0;//Lifetime
		break;
	}
}

/****
* add_login_attempt()
* Add a new login
* @param string $ip IP to record
****/
function add_login_attempt($ip, $browser, $forward = '')
{
	global $db;

	$sql_ary = array(
		'attempt_ip'				=> $ip,
		'attempt_browser'			=> trim(substr($browser, 0, 149)),
		'attempt_forwarded_for'		=> $forward,
		'attempt_time'				=> time(),
	);
	$db->sql_query('INSERT INTO ' . API_LOGIN_ATTEMPTS . ' ' . $db->sql_build_array('INSERT', $sql_ary));
}

/****
* phpbb_logs_status()
* Change phpBB logs status
* @param bool $status Logs status.
****/
function phpbb_logs_status($status = API_STATUS_DOWN)
{
	$GLOBALS['skip_add_log'] = !$status;
}

/****
* calculate_month_seconds()
* Calculate seconds of the current month
****/
function calculate_month_seconds()
{
	return ((int) date('t') * 60 * 60 * 24);
}

/****
* set_no_cache_headers()
* Set no-cache headers.
****/
function set_no_cache_headers()
{
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
}

/****
* utf8_cleaning()
* Clean UTF-8 chars
****/
function utf8_cleaning($string)
{
	if (!preg_match('/[\x80-\xff]/', $string))
	{
		return $string;
	}

	$chars = array(
		// decompositions for latin-1 supplement
		chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
		chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
		chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
		chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
		chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
		chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
		chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
		chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
		chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
		chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
		chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
		chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
		chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
		chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
		chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
		chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
		chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
		chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
		chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
		chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
		chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
		chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
		chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
		chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
		chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
		chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
		chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
		chr(195).chr(191) => 'y',

		// decompositions for latin extended-a
		chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
		chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
		chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
		chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
		chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
		chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
		chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
		chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
		chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
		chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
		chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
		chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
		chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
		chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
		chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
		chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
		chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
		chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
		chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
		chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
		chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
		chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
		chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
		chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
		chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
		chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
		chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
		chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
		chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
		chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
		chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
		chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
		chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
		chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
		chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
		chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
		chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
		chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
		chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
		chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
		chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
		chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
		chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
		chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
		chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
		chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
		chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
		chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
		chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
		chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
		chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
		chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
		chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
		chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
		chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
		chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
		chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
		chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
		chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
		chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
		chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
		chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
		chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
		chr(197).chr(190) => 'z', chr(197).chr(191) => 's',

		// convert middle-european windows charset (cp1250)
		chr(225) => 'á', chr(228) => 'ä', chr(232) => 'č',
		chr(233) => 'é', chr(236) => 'ě', chr(237) => 'í',
		chr(242) => 'ň', chr(244) => 'ô', chr(243) => 'ó',
		chr(250) => 'ú', chr(249) => 'ů', chr(157) => 'ť',
		chr(193) => 'Á', chr(196) => 'Ä', chr(200) => 'Č',
		chr(204) => 'Ě', chr(205) => 'Í', chr(197) => 'Ĺ',
		chr(212) => 'Ô', chr(211) => 'Ó', chr(138) => 'Š',
		chr(239) => 'ď', chr(229) => 'ĺ', chr(229) => 'ľ',
		chr(154) => 'š', chr(248) => 'ř', chr(253) => 'ý',
		chr(158) => 'ž', chr(207) => 'Ď', chr(201) => 'É',
		chr(188) => 'Ľ', chr(210) => 'Ň', chr(218) => 'Ú',
		chr(217) => 'Ů', chr(141) => 'Ť', chr(221) => 'Ý',
		chr(142) => 'Ž',chr(216) => 'Ř'
	);

	$string = strtr($string, $chars);
	return $string;
}