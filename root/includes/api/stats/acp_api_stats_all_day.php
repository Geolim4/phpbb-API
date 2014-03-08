<?php
/**
*
* @package API statistic module
^>@version $Id: acp_api_stats_all_day.php v0.0.1 13h37 03/08/2014 Geolim4 Exp $
* @copyright (c) 2012 - 2014 Geolim4.com http://geolim4.com
* @bug/function request: http://geolim4.com/tracker
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB') || !defined('IN_PHPBB_API') || !defined('ADMIN_START'))
{
	exit;
}
/* Library settings */
$pchart_root_path = $phpbb_root_path . 'includes/api/pchart/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
require($pchart_root_path . 'class/pData.class.' . $phpEx);
require($pchart_root_path . 'class/pDraw.class.' . $phpEx);
require($pchart_root_path . 'class/pImage.class.' . $phpEx);

//Will be implemented later in ACP...
$config['api_stats_height'] = 460;
$config['api_stats_width'] = 900;

$key_id = empty($key_id) ? request_var('key_id', '') : $key_id;
$range_year = request_var('range_year', (int) date('Y'));//There is one year only as in month mode
$range_month = request_var('range_month', date('M'));//There is one month only as in day mode
$range_day = array_slice(explode(',', request_var('range_day', date('d'))), 0, 31, true);

$seed = preg_replace("/[^a-z0-9]+/", "", request_var('seed', ''));
$years_label = $requests_per_day = $queries_history = $ip_history = array();
$day_label = phpbb_api\functions\generate_montly_days($range_year, phpbb_api\functions\inttostrtime($range_month, 'M'), max($range_day));
$requests_per_day = $ip_per_day = array_fill_keys($day_label, 0);
$hightest_score = $average = $final_score = 0;
$where_sql = '';
$begin_time = (int) mktime(0, 0, 0, phpbb_api\functions\inttostrtime($range_month, 'M'), min($range_day), $range_year);
date('I', $begin_time) ? $begin_time = ($begin_time + API_HOUR_SECONDS): false;
$end_time = (int) mktime(0, 0, 0, phpbb_api\functions\inttostrtime($range_month, 'M'), max($range_day) + 1, $range_year);
if (!empty($config['api_mod_cache_stats']))
{
	$cached_stats = $cache->get("_{$range_year}{$range_month}_" . str_replace('.' . $phpEx, '', substr(strrchr(__FILE__, DIRECTORY_SEPARATOR), 1)));
	if ($cached_stats !== false)
	{
		list($requests_per_day, $ip_per_day, $day_label, $hightest_score, $final_score) = unserialize($cached_stats);
		goto skip_sql_loop;
	}
}
if (phpbb_api\functions\validate_key($key_id, true))
{
	$where_sql = " AND key_id = '{$key_id}'";
	$user->lang['ACP_PHPBB_API_STATS_ALL_QR'] = $key_id;
}
$sql = 'SELECT time, ip
	FROM ' . API_HISTORY_TABLE .  "
	WHERE time BETWEEN " . $begin_time . ' AND ' . $end_time . $where_sql;
$result = $db->sql_query($sql, API_HOUR_SECONDS);

while ($row = $db->sql_fetchrow($result))
{
	$queries_history[] = $row;
}
$db->sql_freeresult($result);

asort($queries_history);
foreach ($queries_history AS $queries_history_)
{
	$queries_history_['time'] = (int) $queries_history_['time'];
	$year = date('Y', $queries_history_['time']);
	$month = date('M', $queries_history_['time']);
	$daystr = date('D', $queries_history_['time']);
	$dayint = date('d', $queries_history_['time']);
	if ($year == $range_year && $range_month == $month && in_array($dayint, $range_day))
	{
		$daystr =  $daystr . '  ' . $dayint;

		$day_label[$daystr] = $daystr;
		if (isset($requests_per_day[$daystr]))
		{
			$requests_per_day[$daystr]++;
		}
		else
		{
			$requests_per_day[$daystr] = 1;
		}
		if (!isset($ip_history[$daystr]))
		{
			$ip_history[$daystr] = array();
		}
		if (!isset($ip_history[$daystr][$queries_history_['ip']]))
		{
			$ip_history[$daystr][$queries_history_['ip']] = 1;
			$ip_per_day[$daystr]++;
		}
		if ($hightest_score < $requests_per_day[$daystr])
		{
			$hightest_score = $requests_per_day[$daystr];
		}
		$average++;
	}
}

phpbb_api\functions\dsort($day_label, 5);
phpbb_api\functions\dsort($requests_per_day, 5);
$final_score = $average / sizeof($requests_per_day);
if (array_sum($requests_per_day) == 0 /*&& empty($hour_label)*/)
{
	$no_result = true;
	$requests_per_day[] = VOID;
}
if (!empty($config['api_mod_cache_stats']))
{
	$cache->put("_{$range_year}{$range_month}_" . str_replace('.' . $phpEx, '', substr(strrchr(__FILE__, DIRECTORY_SEPARATOR), 1)), serialize(array($requests_per_day, $ip_per_day, $day_label, $hightest_score, $final_score)), API_HOUR_SECONDS);
	skip_sql_loop:
}
/* Create and populate the pData object */
$bertie_colorize_the_world = new pData();  
$bertie_colorize_the_world->addPoints(array_slice($requests_per_day, 0, 60, true), $user->lang['ACP_PHPBB_API_STATS_ALL_QR']);
$bertie_colorize_the_world->addPoints(array_slice($ip_per_day, 0, 60, true), $user->lang['ACP_PHPBB_API_STATS_MONTH_IP']);
$bertie_colorize_the_world->setSerieTicks($user->lang['ACP_PHPBB_API_STATS_ALL_QR'], 4);
$bertie_colorize_the_world->setAxisName(0, $user->lang['ACP_PHPBB_API_STATS_MONTH_REQ']);
$bertie_colorize_the_world->addPoints(phpbb_api\functions\phpbb_datify(array_slice($day_label, 0, 60, true)),"Labels");
$bertie_colorize_the_world->setSerieDescription("Labels","Months");
$bertie_colorize_the_world->setAbscissa("Labels");
$bertie_colorize_the_world->setAxisDisplay(0,AXIS_FORMAT_METRIC,1); 

/* Create the pChart object */
$bertie_picture = new pImage($config['api_stats_width'], $config['api_stats_height'], $bertie_colorize_the_world,true);
 /* Retrieve the image map */
 
if (isset($_GET["ImageMap"]) || isset($_POST["ImageMap"]))
{
	$bertie_picture->dumpImageMap("ImageMapAreaChart",IMAGE_MAP_STORAGE_FILE,"AreaChart_" . $seed, $pchart_root_path . "tmp"); 
}
/* Set the image map name */
$bertie_picture->initialiseImageMap("ImageMapAreaChart",IMAGE_MAP_STORAGE_FILE,"AreaChart_" . $seed, $pchart_root_path . "tmp"); 

/* Turn off Antialiasing */

$bertie_picture->Antialias = false;

/* Draw the background */ 
$Settings = array("R" => 170, "G" => 183, "B" => 87, "Dash" => 1, "DashR" => 190, "DashG" => 203, "DashB" => 107);
$bertie_picture->drawFilledRectangle(0,0, $config['api_stats_width'], $config['api_stats_height'], $Settings); 

/* Overlay with a gradient */
$Settings = array("StartR" => 219, "StartG" => 231, "StartB" => 139, "EndR" => 1, "EndG" => 138, "EndB" => 68, "Alpha" => 50);
if (!empty($no_result))
{
	$Settings = array("StartR" => 225, "StartG" => 25, "StartB" => 25, "EndR" => 180, "EndG" => 10, "EndB" => 10, "Alpha" => 25);
}
$bertie_picture->drawGradientArea(0,0, $config['api_stats_width'],460,DIRECTION_VERTICAL, $Settings); 
$bertie_picture->drawGradientArea(0,0, $config['api_stats_width'],20,DIRECTION_VERTICAL,array("StartR" => 0,"StartG" => 0,"StartB" => 0,"EndR" => 50,"EndG" => 50,"EndB" => 50,"Alpha" => 80));

/* Add a border to the picture */
$bertie_picture->drawRectangle(0,0, $config['api_stats_width'] - 1, $config['api_stats_height']-1,array("R" => 0,"G" => 0,"B" => 0));

/* Write the chart title */ 
$bertie_picture->setFontProperties(array("FontName" => $pchart_root_path . "fonts/Forgotte.ttf","FontSize" => 11));
$bertie_picture->drawText(40,50, $user->lang['ACP_PHPBB_API_STATS_HISTORY'] . ":",array("FontSize" => 20,"Align" => TEXT_ALIGN_BOTTOMLEFT));
$bertie_picture->drawText(160,49,$user->lang['ACP_PHPBB_API_STATS_MONTH'] . ": " . $user->lang['datetime'][phpbb_api\functions\inttostrtime($range_month, 'F')] . ' ' . $range_year . '   ' . $user->lang('ACP_PHPBB_API_STATS_TOTAL', floor(array_sum($requests_per_day))),array("FontSize" => 16,"Align" => TEXT_ALIGN_BOTTOMLEFT));

if (!empty($no_result))
{
	/* Write the chart title */ 
	$bertie_picture->setFontProperties(array("FontName" => $pchart_root_path . "fonts/Forgotte.ttf","FontSize" => 11));
	$bertie_picture->drawText(350,250,"No result found :(",array("R" => 245,"G" => 60,"B" => 30,"Alpha" => 255, "FontSize" => 38,"Align" => TEXT_ALIGN_BOTTOMLEFT));
}
/* Write the picture title */ 
$bertie_picture->setFontProperties(array("FontName" =>  $pchart_root_path . "fonts/verdana.ttf","FontSize" => 8));
$bertie_picture->drawText((($config['api_stats_width'] / 3) * 2),16, base64_decode($config['api_mod_pchart_header'] . API_PCHART_HEADER),array("R" => 255,"G" => 255,"B" => 255)); 

/* Set the default font */
$bertie_picture->setFontProperties(array("FontName" => $pchart_root_path . "fonts/calibri.ttf","FontSize" => 8));

/* Define the chart area */
$bertie_picture->setGraphArea(45,70, $config['api_stats_width'] - 15,400);

/* Draw the scale */
$scaleSettings = array('LabelRotation'=>45, "Mode" => SCALE_MODE_ADDALL_START0,"XMargin" => 10,"YMargin" => 0,"Floating" => true,"GridR" => 255,"GridG" => 255,"GridB" => 255,"DrawSubTicks" => true,"CycleBackground" => true);
$bertie_picture->drawScale($scaleSettings);

/* Write the chart legend */
$bertie_picture->drawLegend(40,60,array("Style" => LEGEND_NOBORDER,"Mode" => LEGEND_HORIZONTAL));

/* Turn on Antialiasing */
$bertie_picture->Antialias = true;

/* Draw the area chart and detect anormal peaks */
$threshold = array();
if (phpbb_api\functions\percent($hightest_score, $final_score) > 150)
{
	$threshold[] = array("Min" => $final_score,"Max" => $hightest_score,"R" => 255, "G" => 135, "B" => 10, "Alpha" => 50);
}
if (phpbb_api\functions\percent($hightest_score, $final_score) > 200)
{
	$threshold[] = array("Min" => $final_score * 2,"Max" => $hightest_score,"R" => 220,"G" => 30,"B" => 20,"Alpha" => 50);
}
$bertie_picture->drawFilledSplineChart(array("Threshold" => $threshold));

/* Draw a line and a plot chart on top */
$bertie_picture->setShadow(true ,array("X" => 1,"Y" => 1,"R" => 0,"G" => 0,"B" => 0,"Alpha" => 4));
$bertie_picture->drawSplineChart(); 
$bertie_picture->drawPlotChart(array("RecordImageMap" => true, "PlotBorder" => true,"PlotSize" => 4,"BorderSize" => 1,"Surrounding" => 20,"BorderAlpha" => 110));

/* Render the picture (choose the best way) */
$bertie_picture->autoOutput("pictures/example.transparent.background.png");

garbage_collection();
exit_handler();