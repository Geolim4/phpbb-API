<?php
/**
*
* @package language [English] phpBB API
^>@version $Id: info_ucp_phpbb_api.php v0.0.1 00h11 12/20/2013 Geolim4 Exp $
* @copyright (c) 2012 - 2014 Geolim4.com http://geolim4.com
* @bug/function request: http://geolim4.com/tracker
* @translator papicx 28/11/2013 09h03  version a papicx@phpbb-fr.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
// Some characters you may want to copy&paste:
// ’ « » “ ” …
// Use: <strong style="color:green">Texte</strong>',
// For add Color
//
global $config;
$lang = array_merge($lang, array(
	'UCP_PHPBB_API'					=> 'API',
	'UCP_PHPBB_API_ADMIN_KEY'		=> 'That is an administrator key, to access to the full key management you need to pass through the administration panel.
										<br />[ %1$sAdministrate the key%2$s ]',
	'UCP_PHPBB_API_ADMIN_KEY_ONLY'	=> 'Only available on active admin keys.',
	'UCP_PHPBB_API_ADMIN_KEY_INFO'	=> 'Cannot disable email authentication on admin keys',
	'UCP_PHPBB_API_CONFIRM_EXPLAIN'	=> 'That will deactivate the key <em>%s</em> and create a new one subsequently.',
	'UCP_PHPBB_API_DAILY_USE'		=> 'Daily use',
	'UCP_PHPBB_API_EMAIL'			=> 'Authenticate the key with email',
	'UCP_PHPBB_API_KB'				=> 'Knowledge base',
	'UCP_PHPBB_API_FORCE_POST'		=> 'HTTP “POST” method only',
	'UCP_PHPBB_API_FORCE_POST_EXP'	=> 'Force the key to be used as in <a href="http://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Request_methods">POST</a> mode only.',
	'UCP_PHPBB_API_GENERATE'		=> '<a href="%s">Generate a new one?</a>',
	'UCP_PHPBB_API_GEN_AUTH'		=> 'You do not have permission to generate a new key, contact an Administrator for more informations.',
	'UCP_PHPBB_API_HISTORY'			=> 'History',
	'UCP_PHPBB_API_PAGINATION_EVT'	=> '%s event',
	'UCP_PHPBB_API_PAGINATION_EVTS'	=> '%s events',
	'UCP_PHPBB_API_INFINITE_SYMBOL'	=> '∞',
	'UCP_PHPBB_API_KEY_IPS'			=> 'IP filter',
	'UCP_PHPBB_API_KEY_IPS_EXP'		=> 'To specify multiple IPs or different host names, enter each on a new line. To specify an IP address range, separate the beginning and end with a hyphen, and use * as a wildcard.',
	'UCP_PHPBB_API_KEY_IPS_TYPE_A'	=> 'Allowed IPs',
	'UCP_PHPBB_API_KEY_IPS_TYPE_D'	=> 'Disallowed IPs',
	'UCP_PHPBB_API_KEYS'			=> 'Keys management',
	'UCP_PHPBB_API_KEY_ID'			=> 'Key',
	'UCP_PHPBB_API_SECRET_KEY'		=> 'Secret key',
	'UCP_PHPBB_API_SECRET_KEY_EXP'	=> 'The secret key is useful only if you use the cryptographic functionality of the API. Like the API key, that key is strictly personal and you must not share it to untrusted people. 
										<br />In case you compromise this key, ask an administrator to reinitialize it.',
	'UCP_PHPBB_API_KEY_ID_EXP'		=> 'This key is strictly personal, it is your username to be able to operate on the API and identifies you as such, for more security, you can force authentication of this key by email.',
	'UCP_PHPBB_API_LAST_QUERIES'	=> 'Last queries was: %s',
	'UCP_PHPBB_API_LOADING'			=> 'Loading…',
	'UCP_PHPBB_API_MONTHLY_USE'		=> 'Monthly use',
	'UCP_PHPBB_API_NO_KEY'			=> 'Any key found.',
	'UCP_PHPBB_API_NO_REQUEST'		=> 'Any',
	'UCP_PHPBB_API_PERCENT'			=> '%',
	'UCP_PHPBB_API_QUERIE'			=> '%1$s/%2$s queries',
	'UCP_PHPBB_API_QUERIES'			=> '%1$s/%2$s queries',
	'UCP_PHPBB_API_REGENERATE'		=> '<a href="%s">Regenerate a new one</a>',
	'UCP_PHPBB_API_STATS'			=> 'Statistics',
	'UCP_PHPBB_API_STATUS'			=> 'Key status',
	'UCP_PHPBB_API_STATUS_TYPE'		=> array(
		1		=> '<strong style="color:green">Active</strong>',
		2		=> '<strong style="color:red">Suspended</strong>',
		3		=> '<strong style="color:grey">Deactivated</strong>',
	),
	'UCP_PHPBB_API_TOTAL_USE'		=> 'Total use',
	'UCP_PHPBB_API_UNCENSORED'		=> 'Uncensored',
	'UCP_PHPBB_API_UPDATED_CFG'		=> 'Settings saved',
	'UCP_PHPBB_API_VALIDITY'		=> 'Validity',
	'UCP_PHPBB_API_VALIDITY_LFTM'	=> 'This key is lifetime valid',
	'UCP_PHPBB_API_VALIDITY_EXPIRED'=> 'This key is expired',
	'UCP_PHPBB_API_OUT_OF_QUOTA'	=> 'This key is out of maximum quota',
	'UCP_PHPBB_API_VALIDITY_UNTIL'	=> 'This key will is valid until %s',
	'UCP_PHPBB_API_WEEKLY_USE'		=> 'Weekly use',
	'UCP_PHPBB_API_WITH'			=> 'with <em title="Original name: %1$s">%2$s</em>',
));

$lang['UCP_PHPBB_API_KNOWLEDGE_BASE_HOOKS'] = array();//Init the KB hooks array
// Important note to translators & users: 
// BBCODE is supported only on key ID: 1
// Use [adminkey][/adminkey] bbcode to add admin-key-only text.
$lang['UCP_PHPBB_API_KNOWLEDGE_BASE'] = array(
	array(
		0 => '--',
		1 => 'Knowledge base'
	),
	array(
		0 => 'Interaction with the API',
		1 => 'Unless otherwise specified on the key, all requests must be done in GET or POST (recommended).
			[br]All request must be sent using the HTTP protocol, or HTTPS if the administrator has forced SSL.'
	),
	array(
		0 => 'Communicate with the API',
		1 => 'You will find below, different ways to communicate with the API depending the programing language you use:
			[list]
				[*]Perl (<a href="http://search.cpan.org/~gaas/libwww-perl-6.05/lib/LWP.pm#NETWORK_SUPPORT">LWP</a>)
				[*]Java (<a href="http://docs.oracle.com/javase/7/docs/api/java/net/HttpURLConnection.html">HttpURLConnection</a> | <a href="http://docs.oracle.com/javase/7/docs/api/javax/net/ssl/HttpsURLConnection.html">HttpsURLConnection</a>)
				[*]PHP (<a href="http://php.net/manual/en/function.fsockopen.php">fsockopen</a> | <a href="http://php.net/manual/en/book.curl.php">cURL</a>)
				[*]Python (<a href="http://www.python.org/doc/current/lib/module-urllib.html">urllib</a>)
				[*]WinDev (<a href="http://doc.windev.com/en-US/?3043007&amp;name=httprequest_function">HTTPRequest</a>)
				[*]Qt (<a href="http://qt-project.org/doc/qt-4.8/qwebview.html">QWebView</a>)
				[*]Windows (<a href="http://msdn.microsoft.com/en-us/library/windows/desktop/aa383630%28v=vs.85%29.aspx">WinInet</a> | <a href="http://msdn.microsoft.com/fr-fr/library/system.net.httpwebrequest%28v=vs.110%29.aspx">HttpWebRequest Class</a>)
			[/list]'
	),
	array(
		0 => 'Entry point',
		1 => 'The entry point is here: [b]{KB_GATEWAY_INTERFACE}[/b]'
	),

	array(
		0 => 'Parameters (Query string)',
		1 => 'The API use a lot of parameters that you can find below:
		[list]
			[*][b]k[/b]: The API key (mandatory). Example: [i]k={KB_API_KEY}[/i]
			[*][b]e[/b]: Email that will authenticate the key (if needed). Example: [i]e={KB_USER_EMAIL}[/i]
			[*][b]a[/b]: The action (mandatory) Example: [i]a=topic[/i] Note that the action is also is also available in your language if it was translated. Example (french): [i]sujet[/i]
			[*][b]m[/b]: Multibyte activation: Enable UTF8 normalizer for [b]d[/b] and [b]t[/b] parameters (optional). Example: [i]m=true[/i]
			[*][b]t[/b]: Type of data that identify the [b]d[/b] parameter (required depending [b]a[/b] parameter). Example: [i]t=topic_id[/i]
			[*][b]s[/b]: SSO separated with a comma. Example: [i]s=operator:<>,start:5,limit:10[/i]
			[*][b]d[/b]: Value sought depending [b]t[/b] parameter. Example: [i]d=24[/i]
			[*][b]o[/b]: Output format (POST optional, JSON per default). Example: [i]o=json[/i]
			[*][b]c[/b]: Callback JSONP (optional, POST only). Example: [i]c=mafonction()[/i]
			[*][b]f[/b]: Fallback JSONP (optional, POST only). Example: [i]f=mafonction()[/i]
			[*][b]u[/b]: Join current userdata (optional, disabled by default): Join to the result some informations about key owner. Example: [i]u=true[/i]
			[*][b]h[/b]: Active la conversion des temps UNIX en temps textuel (optional, désactivé par défaut): Retourne un temps textuel plutôt qu’un entier. Example: [i]h=true[/i]
			[*][b]p[/b]: Paramètres GET/POST envoyés (optional, désactivé par défaut): Retourne la liste des paramètres GET et POST envoyés au serveur. Example: [i]p=true[/i]
			[adminkey][*][b]v[/b]: Active la prise en charge des constantes systèmes (optional, activé par défaut). Example: [i]v=true[/i]
			[*][b]i[/b]: Use the key without administrator privilege, therefore it will be used as an user key (optional, disabled by default). Example: [i]i=true[/i][/adminkey]
			[*][b]n[/b]: Enable the encrypted communication (optional, disabled by default). Example: [i]n=true[/i]. Please read carefully the [i]Cryptography support[/i] section.[/list]'
	),
	array(
		0 => 'Methods translation',
		1 => 'Most API methods are translated and therefore can be used in the default language set on your user control panel.
			[br]Note that methods are always available in their origin name. Below you can get the list of methods currently translated.
			[code]{KB_SERVER_PROTOCOL}{KB_SERVER_NAME}{KB_SCRIPT_PATH}/api/{KB_API_KEY}({KB_USER_EMAIL})/get_methods/-/-/json[/code]
			Note that this method is not translatable and must be called as is.'
	),
	array(
		0 => 'Sub-methods translation',
		1 => 'Most API sub-methods are also translated and therefore can be used in the default language set on your user control panel.
			[br]Note that sub-methods are always available in their origin name. Below you can get the list of sub-methods currently translated for a certain method.
			[code]{KB_SERVER_PROTOCOL}{KB_SERVER_NAME}{KB_SCRIPT_PATH}/api/{KB_API_KEY}({KB_USER_EMAIL})/get_submethods/topic/-/json[/code]
			[b][color=#BC2A4D]/!\[/color][/b] The translated sub-method cannot be called in [b]GET[/b] mode, you must call it in his origin name. Example: <em>topic_id</em>
			Note that this method is not translatable and must be called as is.'
	),
	array(
		0 => 'Simplified access with GET mode',
		1 => 'For simple requests the GET mode can be used, trying to retrieve information from the topic with the ID N°24 in JSON format:
			[code]{KB_SERVER_PROTOCOL}{KB_SERVER_NAME}{KB_SCRIPT_PATH}/api/{KB_API_KEY}({KB_USER_EMAIL})/topic/topic_id/24/json[/code]
			[br]If the request was successful, the server will return a similar response:
			[code]{
	results: {
		item0: {
		topic_id: "24",
		forum_id: "4",
		icon_id: "0",
		topic_attachment: "0",
		topic_approved: "1",
		topic_reported: "0",
		topic_title: "Welcome on phpBB3",
		topic_poster: "2",
		topic_time: "1352406260",
		topic_time_limit: "0",
		topic_views: "0",
		topic_replies: "9",
		topic_replies_real: "9",
		topic_status: "0",
		topic_type: "0",
		topic_first_post_id: "139",
		topic_first_poster_name: "Geolim4",
		topic_first_poster_colour: "AA0000",
		topic_last_post_id: "148",
		topic_last_poster_id: "2",
		topic_last_poster_name: "Geolim4",
		topic_last_poster_colour: "AA0000",
		topic_last_post_subject: "Re: Welcome on phpBB3",
		topic_last_post_time: "1373142670",
		topic_last_view_time: "1373142670",
		poll_title: ""
		}
	},
	timing: 0.215,
	status: "200 OK"
}[/code]
		[br]However, if the request went wrong, a similar response will be returned:
			[code]{
	msg: "Unauthorized API key !",
	timing: 0.0056s,
	status: "200 OK"
}[/code]
			In case of fatal error, the API will join the error value according [url=http://php.net/manual/en/errorfunc.constants.php]PHP manual[/url]:
	[code]{
	msg: "Critical error: Method "api_xxxxx" not found ! The name of the method can vary depending the language set in the account attached to your key.",
	errno: 256,
	timing: 0.0056s,
	status: "503 Service Unavailable"
}[/code]In the example above the level of the error is [i]E_USER_ERROR[/i].
			[br]If the key requires authentication by e-mail ([b]e[/b]) it will be suffixed with the key wrapped with brackets:
			[code]{KB_SERVER_PROTOCOL}{KB_SERVER_NAME}{KB_SCRIPT_PATH}/api/{KB_API_KEY}({KB_USER_EMAIL})/topic/topic_id/24/json[/code]
			[br]The GET mode also take care of some secured sort operators (S.S.O) and comparison on the sub-method of the current method (see «Advanced access with POST mode» chapter).
				S.S.O must be suffixed to the sub-method the current method wrapped with brackets, here we only look for the ten topics where the topic_id is different of 24 while ignoring the first five topics.
			[code]{KB_SERVER_PROTOCOL}{KB_SERVER_NAME}{KB_SCRIPT_PATH}/api/{KB_API_KEY}({KB_USER_EMAIL})/topic/topic_id(operator:<>,start:5,limit:10)/24/json[/code]
			[b][color=#BC2A4D]/!\[/color][/b] However, it is highly recommended to use the POST mode for hard requests.
			Only the following S.S.O are supported: [i]NOT LIKE,LIKE,REGEXP,&nbsp;&lt;&gt;,&nbsp;&gt;,&nbsp;&lt;,&nbsp;=,&nbsp;&lt;=,&nbsp;&gt;=[/i]
			[br]If the sub-methods ([b]t[/b]) and the value ([b]d[/b]) are optionals you can replace them using the generic char: [b]{KB_WILDCARD_CHAR}[/b]
			[code]{KB_SERVER_PROTOCOL}{KB_SERVER_NAME}{KB_SCRIPT_PATH}/api/{KB_API_KEY}({KB_USER_EMAIL})/key_stats/{KB_WILDCARD_CHAR}/{KB_WILDCARD_CHAR}/json[/code]'
	),
	array(
		0 => 'Advanced access with POST mode',
		1 => 'The POST mode is [b][u]highly[/u][/b] recommended especially for general requests using S.S.O and/or special characters such as accents.
			The examples below will be represented as a cURL array (PHP).
			[br]Here we retrieve the topic subject which contain the « elementary » word
			[code=php] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "topic",
		"m" => true,
		"t" => "topic_id",
		"s" => "operator:LIKE",
		"d" => "elementary",
		"o" => "json",
));[/code]We used the "LIKE" S.S.O. The "%" mask is automatically added by the API.
[br]You can also use a REGEXP to improve the search accuracy:[code=php] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "topic",
		"m" => true,
		"t" => "topic_id",
		"s" => "operator:REGEXP",
		"d" => "[0-5]{3}",
		"o" => "json",
));[/code]
Here we searched a topic which his subject identifier [b]contain[/b] a three lenght digit integer between 0 and 5.
[br]Matching example: [color=#00BF40]345[/color], [color=#00BF40][u]125[/u]9[u]413[/u][/color], [color=#00BF40]550[/color]
Non-matching example: [color=#FF0000]725[/color], [color=#FF0000]05[/color], [color=#FF0000]1385[/color]
If you want the matching is done on the entire string and not a part only you need to specify like this: [b]^[0-5]{3}$[/b]'
	),
	array(
		'cfg' => 'api_mod_crypto_enabled',
		0 => 'Cryptography support',
		1 => 'The API provide a basic cryptography support, to engage an encrypted communication you have to turn on the [b]n[/b] parameter.
			The current crypto cipher is {KB_CRYPTO_CIPHER} (mode: [i]{KB_CRYPTO_MODE}[/i]), but you can get it from the API using the [b]get_crypto_config[/b] method.
			[code]{KB_SERVER_PROTOCOL}{KB_SERVER_NAME}{KB_SCRIPT_PATH}/api/{KB_API_KEY}({KB_USER_EMAIL})/get_crypto_config/-/-/json[/code]
			[size=100][u]Request an encrypted response to the API:[/u][/size]
			The syntax is pretty the same as usual, except the [b]n[/b] parameter which is turned on.
			[code=php] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "topic",
		"m" =>  false,
		"n" => true,
		"t" => "topic_id",
		"s" => "s=operator:<>,start:5,limit:10",
		"d" => "2",
		"o" => "json",
));[/code]
[b][color=#BC2A4D]/!\[/color][/b] The API will return a file named [b]{KB_CRYPTO_FILENAME}[/b] instead of a standard HTTP response. As request this file is encrypted and you can only decrypt it using your secret key.
[br][size=100][u]Decrypt an encrypted response from the API (PHP method):[/u][/size]
[code=php]$handle = fopen("{KB_CRYPTO_FILENAME}", "rb");
$encrypted_content = fread($handle, filesize("api.response"));
fclose($handle);
$decrypted_content = mcrypt_decrypt({KB_CRYPTO_CIPHER}, "your_secret_key", $encrypted_content, {KB_CRYPTO_MODE}, "{KB_CRYPTO_IV}");[/code]
As you can see above the code used to decrypt the file is pretty simple. If the crypto cypher has changed since your last use contact an administrator for more informations.'
	),

	array(
		'a_'	=> true,//require the user to have at least an administrator key to view that part
		0 => 'Search by system constants',
		1 => 'You can use phpBB constants (and only them) to improve significantly the search accuracy. You must prefix the sign [b]$[/b]
			[code=php] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "topic",
		"m" => false,
		"t" => "topic_status",
		"s" => "",
		"d" => "$item_locked",
		"o" => "json",
));[/code]
You can modify the [b]v[/b] parameter to disable phpBB constants (enabled by default).'
	),
	array(
		0 => '--',
		1 => 'Functionalities'
	),
	array(
		'method' => 'topic,post,forum,group',//Automatically translated
		0 => 'Get topics/posts/forums/groups data',
		1 => 'The API allow you to get some basic topics/posts/forums/groups data.
			[br]On the example below, we will try to get data from the forum with the following forum ID: [b]1[/b]
			[code=php] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "forum",
		"m" => false,
		"t" => "forum_id",
		"s" => "",
		"d" => "1",
		"o" => "json",
));[/code]
Of course you can alter the code if you want to retrieve data from topics, posts, or groups.'
	),
	array(
		'method' => 'get_config',//Automatically translated
		0 => 'Get configuration data',
		1 => 'The API allow you to get some basic configuration data.
			[br]Here we will get all basic configuration data, like maximum avatar size, website description, or board’s opening date:
			[code=php] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "{METHOD}",
		"m" =>  false,
		"t" => "all",
		"s" => "",
		"d" => "",
		"o" => "json",
));[/code][adminkey]
You can also get different types of configuration with three different modes: « cached », « dynamic », « custom ».
			[br]Here we get all cached configuration data:
			[code=php] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "{METHOD}",
		"m" =>  false,
		"t" => "cached",
		"s" => "",
		"d" => "",
		"o" => "json",
));[/code]
			[br]Here we get all dynamic configuration data:
			[code=php] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "{METHOD}",
		"m" =>  false,
		"t" => "dynamic",
		"s" => "",
		"d" => "",
		"o" => "json",
));[/code]
			Here we get custom configuration data, using the [b]d[/b] parameter separating each configuration name with a comma.
			[code=php] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "{METHOD}",
		"m" =>  false,
		"t" => "custom",
		"s" => "",
		"d" => "board_email_sig,max_filesize",
		"o" => "json",
));[/code][/adminkey]'
	),
	array(
		'method' => 'get_constants',//Automatically translated
		'a_'	=> true,//require the user to have at least an administrator key to view that part
		0 => 'Get available system constants',
		1 => 'You can retrieve the list of available phpBB constants using the [b]{METHOD}[/b] method
			[code=php] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "{METHOD}",
		"m" => false,
		"t" => "",
		"s" => "",
		"d" => "",
		"o" => "json",
));[/code]'
	),
	array(
		'method' => 'set_config',//Automatically translated
		'a_'	=> true,//require the user to have at least an administrator key to view that part
		0 => 'Alter configuration variables',
		1 => 'The API also allows you to [u]alter[/u] the configuration variables. [color=red]Be careful with mishandling![/color]
			[br]Altering a some configuration variables, so we need to use the [b]d[/b] parameter.
			You can only use two formats to send a new configuration value: JSON and serialize(PHP). 
			[br]The configuration data to modify must be sent as a pair « config name/value »
			[code=php] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "{METHOD}",
		"m" =>  false,
		"t" => "json",
		"s" => "",
		"d" => \'{"board_email_sig": "Thanks, the great team.", "max_filesize" : 262144}\',
		"o" => "json",
));[/code]'
	),
	array(
		'method' => 'refresh_stats',//Automatically translated
		'a_'	=> true,//require the user to have at least an administrator key to view that part
		0 => 'Refresh board’s statistics',
		1 => 'You can refresh the statistics of your board directly via the API, but beware the frequency of these refresh that are resource intensive.
			[br]The [b]t[/b] parameter will allow you to choose the type of refresh:
			[list]
				[*][b]all[/b]: Force the refresh of [b]ALL[/b] statistics (Not recommended in high hourly traffic).
				[*][b]num_posts[/b]: Force the refresh of the posts number statistics.
				[*][b]num_topics[/b]: Force the refresh of the topics number statistics.
				[*][b]num_users[/b]: Force the refresh of the users number statistics.
				[*][b]num_files[/b]: Force the refresh of the attachments number statistics.
				[*][b]upload_dir_size[/b]: Force the refresh of the attachments directory size statistics.
				[*][b]update_last_username[/b]: Force the refresh of the latest user’s registration
			[/list]
			[code=php] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "{METHOD}",
		"m" => false,
		"t" => "all",
		"s" => "",
		"d" => "",
		"o" => "json",
));[/code]'
	),
	array(
		'method' => 'sql_query',//Automatically translated
		'a_'	=> true,//require the user to have at least an administrator key to view that part
		0 => 'Perform an SQL query',
		1 => 'You can perform SQL queries directly via the API (POST mode only), however depending on the configuration of your key you may not be able to modify sensible tables like API/logs table.
			[br]The [b]s[/b] parameter is available for clauses such <em>start</em> and <em>limit</em>.
			[br]It is preferable to enable the multibyte ([b]m[/b]) parameter for compatibility reasons.
			[br]Be very careful when running sensitive queries such as [b]DELETE/DROP/TRUNCATE[/b].
			[br][b][color=#BC2A4D]For security reasons, all SQL statements are stored in logs after execution.[/color][/b]
			[code=php] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "{METHOD}",
		"m" => true,
		"t" => "all",
		"s" => "start:5,limit:10",
		"d" => \'SELECT * FROM $USERS_TABLE WHERE user_id = 1\',//You can safely use phpBB constants here, but do not forget to use simple quotes to avoid bad PHP interpretation
		"o" => "json",
));[/code]'
	),
	array(
		'method' => 'perm_ban',//Automatically translated
		'a_'	=> true,//require the user to have at least an administrator key to view that part
		0 => 'Permanent banning',
		1 => 'You can permanently ban an entity such an IP, an username or even an email address.
			Use the sub-method ([b]t[/b]) to define the entity that you will ban. Possible values: <em>user/ip/email</em>.
			The value ([b]d[/b]) is the entity to ban.
			[code=php] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "{METHOD}",
		"m" => true,
		"t" => "ip",
		"s" => "",
		"d" => "1.3.3.7",
		"o" => "json",
));[/code]'
	),
	array(
		'method' => 'unban',//Automatically translated
		'a_'	=> true,//require the user to have at least an administrator key to view that part
		0 => 'Unbanning',
		1 => 'Conversely, can unban an entity such an IP, an username or even an email address.
			Use the sub-method ([b]t[/b]) to define the entity that you will unban. Possible values: <em>user/ip/email</em>.
			The value ([b]d[/b]) is the entity to unban.
			[code=php] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "{METHOD}",
		"m" => true,
		"t" => "ip",
		"s" => "",
		"d" => "1.3.3.7",
		"o" => "json",
));[/code]'
	),
	array(
		'a_'	=> true,//require the user to have at least an administrator key to view that part
		'method' => 'board_status',//Automatically translated
		0 => 'Enable/disable board',
		1 => 'You can disable the board for example during a maintenance operation.
			Use the sub-method ([b]t[/b]) to define the board status <em>enable/disable</em> or <em>true/false</em>.
			The value ([b]d[/b]) will define your custom message that will displayed publicly. However, it must not exceed 255 characters, else it may be truncated..
			[code=php] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "{METHOD}",
		"m" => false,
		"t" => "disable",//Possible values: false/true/enable/disable
		"s" => "",
		"d" => "The board has been disable using API",//Your custom message that will displayed publicly
		"o" => "json",
));[/code]'
	),
	array(
		'method' => 'key_stats',//Automatically translated
		0 => 'Retrieve key use statistics',
		1 => 'At any time you can look at the current use average of your key from your user control panel or from the API directly:
			[code=php] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "{METHOD}",
		"m" =>  false,
		"t" => "",
		"s" => "",
		"d" => "",
		"o" => "json",
));[/code]
Please note that viewing your statistics from the API will [b]not[/b] be deducted from your day/week/month/total counter. '
	),
	array(
		'method' => 'key_options',//Automatically translated
		0 => 'Retrieve available key options',
		1 => 'At any time you can look at the current available options of your key from your user control panel or from the API directly:
			[code=php] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "{METHOD}",
		"m" =>  false,
		"t" => "",
		"s" => "",
		"d" => "",
		"o" => "json",
));[/code]
Please note that viewing your statistics from the API will [b]not[/b] be deducted from your day/week/month/total counter. '
	),
	array(
		'method' => 'login',//Automatically translated
		0 => 'Connect to your account using API',
		1 => 'You can connect to your account via the l’API using the [i]{METHOD}[/i] method without sub-method ([b]t[/b]) or value ([b]d[/b]) required. (Unless email ([b]e[/b]) if required)
			[code=php] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "{METHOD}",
		"m" =>  false,
		"t" => "",
		"s" => "",
		"d" => "",
		"o" => "json",
));[/code]
[b][color=#BC2A4D]/!\[/color][/b] This function will violate the output format ([b]o[/b]) as returning HTML data!'
	),
	// This block will switch the knowledge to the second template column
	array(
		0 => '--',
		1 => '--'
	),
	array(
		0 => '--',
		1 => 'FAQ (Frequently Asked Questions)'
	),
	array(
		0 => 'Where can i see my request quota ?',
		1 => 'You can see it in your user control panel, tab «Statistics».'
	),
	array(
		0 => 'I reached my quota of total requests, how to continue to use the API?',
		1 => 'Vous pouvez demander à un administrateur d’augmenter votre quota de requête totales ou, si vous en avez la permission, générer une nouvelle clé depuis votre panneau de contrôle de l’utilisateur.'
	),
	array(
		0 => 'My key has expired has expired, what can i do ?',
		1 => 'Vous pouvez demander à un administrateur de rallonger la durée de vie de votre clé ou, si vous en avez la permission, en générer une nouvelle clé depuis votre panneau de contrôle de l’utilisateur.'
	),
	array(
		0 => 'My key has been disabled or is suspended, what can i do ?',
		1 => 'Votre clé à peut-être été suspendue par un administrateur à la suite d’un abus. Notez qu’une clé est automatiquement désactivée lorsque vous en générez une nouvelle.'
	),
	array(
		0 => 'What is the API response encoding ?',
		1 => 'Sauf spécifications contraire, toutes les réponses de L’API sont encodés en UTF-8 (sans BOM).'
	),
	array(
		0 => 'Existe-t-il des traces de mes requêtes sur l’API ?',
		1 => 'Oui, vous pouvez les consulter depuis votre panneau de l’utilisateur sur l’onglet [i]Historique[/i], ainsi que l’administrateur qui peut voir de manière détaillé chaque requête que vous avez effectué.'
	),
	array(
		0 => 'Combien existe-t-il de chance que l’on puisse trouver ma clé au hasard ?',
		1 => 'Elle sont de [i]1,7868991024601705453143247728944e+62[/i] (38√<sup>40</sup>) [u]sans[/u] authentification par email !'
	),
	array(
		0 => 'Que faire en cas d’activité anormale sur une de mes clés ?',
		1 => 'Vous devez contacter un administrateur au plus vite afin de faire suspendre celle-ci si il s’avère qu’une personne mal intentionné a fait usage de votre clé.'
	),
	array(
		0 => 'L’API à retourné une erreur fatale ou une exception, que doit-je faire ?',
		1 => 'Chaque évènement inattendu est automatiquement géré et consigné par l’API, vous pouvez donc notifier un administrateur qui agiras selon les données archivés.'
	),
	array(
		0 => '--',
		1 => 'Lexical'
	),
	array(
		0 => 'S.S.O',
		1 => 'Abbreviation of <em>Secured SQL Operator</em>: Secured and parsed SQL operator to perform customs sorting/comparison operations.'
	),
	array(
		0 => 'REGEXP',
		1 => 'Acronyme de <em>expressions régulières</em> ou <em> expressions rationnelles</em>: masque de caractère permettant de faire correspondre une chaine de caractère. <a href="https://fr.wikipedia.org/wiki/Expression_rationnelle">Plus de détails</a>.'
	),
	array(
		0 => 'cURL',
		1 => 'Librairie disponible en PHP permettant la communication avec de nombreux protocoles tel que HTTP, HTTPS, FTP, Telnet etc.'
	),
	array(
		0 => 'Callback',
		1 => 'Fonction de retour executée en cas de requête réussie.'
	),
	array(
		0 => 'Fallback',
		1 => 'Fonction de retour executée en cas de requête échouée.'
	),
	array(
		0 => '--HOOKS--',
		1 => 'Additional functionalities [i](extensions)[/i]'
	),
);