<?php
/**
*
* @package language [English] phpBB API
^>@version $Id: info_ucp_phpbb_api.php v0.0.1 00h11 12/20/2013 Geolim4 Exp $
* @copyright (c) 2012 - 2014 Geolim4.com http://geolim4.com
* @bug/function request: http://geolim4.com/tracker
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
			<br />All request must be sent using the HTTP protocol, or HTTPS if the administrator has forced SSL.'
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
			[*][b]i[/b]: Utilise la clé sans privilège d’administrateur, elle seras donc utilisée en tant que clé d’utilisateur (optional, désactivé par défaut). Example: [i]i=true[/i][/adminkey]
			[*][b]n[/b]: Enable the encrypted communication (optional, disabled by default). Example: [i]n=true[/i]. Please read carefully the [i]Cryptography support[/i] section.[/list]'
	),
	array(
		0 => 'Methods translation',
		1 => 'La plupart des méthodes de l’API sont traduites et peuvent donc de ce fait être utilisées dans la langue par défaut définie sur votre panneau de de contrôle d’utilisateur.
			<br />Notez que les méthodes sont toujours disponibles dans leur dénomination de base. Ci-dessous vous pouvez récupérer la liste des méthodes actuellement traduites.
			[code]{KB_SERVER_PROTOCOL}{KB_SERVER_NAME}{KB_SCRIPT_PATH}/api/{KB_API_KEY}({KB_USER_EMAIL})/get_methods/-/-/json[/code]
			Notez que cette méthode n’est pas traduisible et doit être appelée comme telle.'
	),
	array(
		0 => 'Traductions des sous-méthodes',
		1 => 'La plupart des sous-méthodes de l’API sont également traduites et peuvent donc de ce fait être utilisées dans la langue par défaut définie sur votre panneau de de contrôle d’utilisateur.
			<br />Notez que les sous-méthodes sont toujours disponibles dans leur dénomination de base. Ci-dessous vous pouvez récupérer la liste des sous-méthodes actuellement traduites pour une méthode.
			[code]{KB_SERVER_PROTOCOL}{KB_SERVER_NAME}{KB_SCRIPT_PATH}/api/{KB_API_KEY}({KB_USER_EMAIL})/get_submethods/topic/-/json[/code]
			[b][color=#BC2A4D]/!\[/color][/b] La sous-méthode traduite ne peux pas être utilisé en mode [b]GET[/b], vous devrez donc l’appeler selon sa dénomination d’origine. Exemple: <em>topic_id</em>
			Notez que cette méthode n’est pas traduisible et doit être appelée comme telle.'
	),
	array(
		0 => 'Accès simplifié avec la méthode GET',
		1 => 'Pour les requêtes simples la méthode GET peut être utilisée, tentons de récupérer les informations du sujet avec l’ID N°24 au format JSON:
			[code]{KB_SERVER_PROTOCOL}{KB_SERVER_NAME}{KB_SCRIPT_PATH}/api/{KB_API_KEY}({KB_USER_EMAIL})/topic/topic_id/24/json[/code]
			<br />Si la requête s’est bien passée, le serveur retournera une réponse similaire:
			[code]{
	results: {
		item0: {
		topic_id: "24",
		forum_id: "4",
		icon_id: "0",
		topic_attachment: "0",
		topic_approved: "1",
		topic_reported: "0",
		topic_title: "Bienvenue sur phpBB3",
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
		topic_last_post_subject: "Re: Bienvenue sur phpBB3",
		topic_last_post_time: "1373142670",
		topic_last_view_time: "1373142670",
		poll_title: ""
		}
	},
	timing: 0.215,
	status: "200 OK"
}[/code]
		<br />Toutefois si la requête s’est mal déroulée une réponse similaire sera retournée:
			[code]{
	msg: "Clé API non autorisée !",
	timing: 0.0056s,
	status: "200 OK"
}[/code]
			En cas d’erreur critique ou fatale, l’API joindra le degré d’erreur selon les normes de [url=http://php.net/manual/fr/errorfunc.constants.php]PHP[/url]:
	[code]{
	msg: "Clé API suspendue !",
	errno: 512,
	timing: 0.0056s,
	status: "200 OK"
}[/code]Sur l’exemple ci-dessus le degré de l’erreur est de type [i]E_USER_WARNING[/i].
			<br />Si la clé requiert une authentification par e-mail elle devra être suffixée à la clé entre parenthèse:
			[code]{KB_SERVER_PROTOCOL}{KB_SERVER_NAME}{KB_SCRIPT_PATH}/api/{KB_API_KEY}({KB_USER_EMAIL})/topic/topic_id/24/json[/code]
			<br />La méthode GET prends aussi en charge quelques opérateurs de tri et de comparaison sur le mode de la méthode courante (voir chapitre sur les accès en « POST »).
				Les opérateurs doivent être suffixés au mode de la méthode courante, ici nous recherchons seulement les 10 sujets où l’ID de sujet est différent de 24 en ignorant les 5 premiers sujets.
			[code]{KB_SERVER_PROTOCOL}{KB_SERVER_NAME}{KB_SCRIPT_PATH}/api/{KB_API_KEY}({KB_USER_EMAIL})/topic/topic_id(operator:<>,start:5,limit:10)/24/json[/code]
			[b][color=#BC2A4D]/!\[/color][/b] Il est toutefois hautement recommandé d’utiliser la méthode POST pour les requêtes compliquées.
			Seul les opérateurs suivants sont supportés: <em>NOT LIKE,LIKE,REGEXP,&nbsp;&lt;&gt;,&nbsp;&gt;,&nbsp;&lt;,&nbsp;=,&nbsp;&lt;=,&nbsp;&gt;=</em><br />
			<br />Si le type de données et la valeur recherchée sont facultatifs, vous pouvez les remplacer par le caractère générique suivant: [b]{KB_WILDCARD_CHAR}[/b]
			[code]{KB_SERVER_PROTOCOL}{KB_SERVER_NAME}{KB_SCRIPT_PATH}/api/{KB_API_KEY}({KB_USER_EMAIL})/key_stats/{KB_WILDCARD_CHAR}/{KB_WILDCARD_CHAR}/json[/code]'
	),
	array(
		0 => 'Accès avancés avec la méthode POST',
		1 => 'La méthode POST est [b]chaudement recommandée[/b] de manière générale surtout pour les requêtes utilisant des opérateurs et/ou des caractères spéciaux comme des accents.
			Les exemples ci-dessous seront représentés sous forme d’un tableau cURL (PHP).
			<br />Récupérons ici le sujet dont le titre contient le mot « élémentaire »
			[code=php] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "topic",
		"m" => true,
		"t" => "topic_id",
		"s" => "operator:LIKE",
		"d" => "élémentaire",
		"o" => "json",
));[/code]Nous avons donc utilisé l’O.S.S "LIKE". L’ajout du masque "%" est ajouté automatiquement par l’API.
<br />Vous pouvez également utiliser une REGEXP pour affiner les critères de recherche:[code=php] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "topic",
		"m" => true,
		"t" => "topic_id",
		"s" => "operator:REGEXP",
		"d" => "[0-5]{3}",
		"o" => "json",
));[/code]
Ici nous avons recherché un sujet dont son identifiant de sujet [b]contient[/b] un entier de 3 chiffres de long compris entre 0 et 5.
<br />Exemple de correspondance: [color=#00BF40]345[/color], [color=#00BF40]1259413[/color], [color=#00BF40]550[/color]
Exemple de non-correspondance: [color=#FF0000]725[/color], [color=#FF0000]05[/color], [color=#FF0000]1358[/color]
Si vous souhaitez que la correspondance se fasse sur la chaîne entière et non une partie de celle-ci vous devez le préciser comme ceci: [b]^[0-5]{3}$[/b]'
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
		'a_'	=> true,//require the user to have at least an administrator key to view that paragraph!
		0 => 'Recherche par constantes systèmes',
		1 => 'Vous pouvez utiliser des constantes de phpBB (et uniquement celles-ci) afin d’effectuer des recherches plus poussées. Vous devez la préfixer avec le signe [b]$[/b]
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
Vous pouvez modifier le paramètre [b]v[/b] afin de désactiver ces dernières (activées par défaut).'
	),
	array(
		0 => '--',
		1 => 'Fonctionnalités'
	),
	array(
		'method' => 'topic,post,forum,group',//Not translatable
		0 => 'Récupérer des données de sujets/messages/forums/groupes',
		1 => 'L’API d’en récupérer quelques données basiques.
			<br />Sur l’exemple ci-dessous, nous tentons de récupérer les données du forum dont l’identifiant est "1"
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
Bien sûr à vous d’adapter le code si vous souhaitez récupérer des données de sujets, de messages ou encore de groupes.'
	),
	array(
		'method' => 'get_config',//Not translatable
		0 => 'Récupérer des données de configuration',
		1 => 'L’API de récupérer quelques données basiques de configuration.
			<br />Récupérons ici toutes les données de configurations basique, comme par exemple la taille maximum des avatars, la description du site ou encore la date d’ouverture du forum:
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
Vous pouvez également récupérer différents types de configuration avec trois modes différents: « cached », « dynamic », « custom ».
			<br />Récupérons ici toutes les variables de configuration en cache:
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
			<br />Récupérons ici toutes les variables de configuration dynamiques:
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
			Récupérons ici des variables de configuration personnalisées, nous faisons donc appel au paramètre [b]d[/b] en séparant chaque nom de données de configuration par une virgule.
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
		'method' => 'get_constants',//Not translatable
		'a_'	=> true,//require the user to have at least an administrator key to view that paragraph!
		0 => 'Récupérer les constantes systèmes disponibles',
		1 => 'Vous pouvez récupérer la liste des constantes système disponibles en utilisant la méthode [b]{METHOD}[/b]
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
		'method' => 'set_config',//Not translatable
		'a_'	=> true,//require the user to have at least an administrator key to view that paragraph!
		0 => 'Modifier des données de configuration',
		1 => 'L’API permet également de modifier les données de configuration. Attention toutefois aux mauvaises manipulations !
			<br />Modifions ici quelques variables de configuration, nous devons donc faire appel au paramètre [b]d[/b].
			Vous ne pouvez utiliser que deux formats pour envoyer une donnée de configuration: JSON et serialize(PHP). 
			<br />Les données de configuration à modifier sont envoyées par paire « nom de configuration/valeur »
			[code=php] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "{METHOD}",
		"m" =>  false,
		"t" => "json",
		"s" => "",
		"d" => \'{"board_email_sig": "Merci, l’équipe du forum", "max_filesize" : 262144}\',
		"o" => "json",
));[/code]'
	),
	array(
		'method' => 'refresh_stats',//Not translatable
		'a_'	=> true,//require the user to have at least an administrator key to view that paragraph!
		0 => 'Actualiser les statistiques du forum',
		1 => 'Vous pouvez actualiser les statistiques de votre forum directement via l’API, attention toutefois à la fréquence de ces actualisations qui sont gourmandes en ressources.
			<br />Le paramètre [b]t[/b] vous permettra de choisir le type d’actualisation:
			[list]
				[*][b]all[/b]: Force la ré-actualisation de toutes les statistiques.
				[*][b]num_posts[/b]: Force la ré-actualisation des statistiques de messages.
				[*][b]num_topics[/b]: Force la ré-actualisation des statistiques de sujets.
				[*][b]num_users[/b]: Force la ré-actualisation des statistiques du nombre d’utilisateurs.
				[*][b]num_files[/b]: Force la ré-actualisation des statistiques du nombre de fichiers-joints.
				[*][b]upload_dir_size[/b]: Force la ré-actualisation des statistiques de la taille du répertoire des fichiers-joints.
				[*][b]update_last_username[/b]: Force la ré-actualisation des statistiques du dernier utilisateur inscrit.
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
		'method' => 'sql_query',//Not translatable
		'a_'	=> true,//require the user to have at least an administrator key to view that paragraph!
		0 => 'Effectuer une requête SQL',
		1 => 'Vous pouvez effectuer des requêtes SQL directement via l’API (méthode POST uniquement), cependant selon la configuration de votre clé vous pouvez ne pas être en mesure de pouvoir modifier les tables sécurisées qui comprennent les tables des logs et de l’API.
			<br />Le paramètre [b]s[/b] est disponible pour les clauses <em>start</em> et <em>limit</em>, il vous permettra de choisir le type d’actualisation:
			<br />Il est préférable d’activer le paramètre [b]m[/b] pour des raisons de compatibilité.
			<br />Soyez très prudent lors d’exécution de requêtes sensibles telles que [b]DELETE/DROP/TRUNCATE[/b].
			<br />[b][color=#BC2A4D]Pour des raisons de sécurité, toutes les requêtes SQL sont archivées dans les logs après exécution.[/color][/b]
			[code=php] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "{METHOD}",
		"m" => true,
		"t" => "all",
		"s" => "start:5,limit:10",
		"d" => "SELECT * FROM phpbb_user_group WHERE group_leader = 1",
		"o" => "json",
));[/code]'
	),
	array(
		'method' => 'perm_ban',//Not translatable
		'a_'	=> true,//require the user to have at least an administrator key to view that paragraph!
		0 => 'Bannissement définitif',
		1 => 'Vous pouvez bannir définitivement une entité telle qu’une IP, un nom d’utilisateur ou bien encore une adresse e-mail.
			Utilisez le paramètre [b]t[/b] pour définir le type d’entité à bannir tels que <em>user/ip/email</em>.
			Le paramètre [b]d[/b] représentera l’entité à bannir.
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
		'method' => 'unban',//Not translatable
		'a_'	=> true,//require the user to have at least an administrator key to view that paragraph!
		0 => 'Débannissement',
		1 => 'Vous pouvez dé-bannir une entité telle qu’une IP, un nom d’utilisateur ou bien encore une adresse e-mail.
			Utilisez le paramètre [b]t[/b] pour définir le type d’entité à dé-bannir tels que <em>user/ip/email</em>.
			Le paramètre [b]d[/b] représentera l’entité à dé-bannir.
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
		'a_'	=> true,//require the user to have at least an administrator key to view that paragraph!
		'method' => 'board_status',//Not translatable
		0 => 'Activer/désactiver le forum',
		1 => 'Vous pouvez activer ou désactiver le forum pour des manipulations sensibles par exemple.
			Utilisez le paramètre [b]t[/b] pour définir le statut du forum <em>activer/désactiver</em> ou <em>1/0</em>.
			Le paramètre [b]d[/b] représentera le message que vous souhaitez afficher en conséquence. Il ne doit cependant pas dépasser 255 caractère au risque d’être tronqué
			[code=php] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "{METHOD}",
		"m" => false,
		"t" => "désactiver",
		"s" => "",
		"d" => "Le forum a été désactivé via phpBB API.",
		"o" => "json",
));[/code]'
	),
	array(
		'method' => 'key_stats',//Not translatable
		0 => 'Récupérer les statistiques d’utilisation de ma clé',
		1 => 'A tout moment vous pouvez consulter le taux d’utilisation de votre clé soi depuis votre panneau de contrôle d’utilisateur soi depuis l’API directement:
			La consultation de vos statistiques n’est pas comptabilisé en tant que requête. Il en est de même pour la consultation des options de votre clé.
			[code=php] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "{METHOD}",
		"m" =>  false,
		"t" => "",
		"s" => "",
		"d" => "",
		"o" => "json",
));[/code]'
	),
	array(
		'method' => 'key_options',//Not translatable
		0 => 'Récupérer les options disponibles de ma clé',
		1 => 'A tout moment vous pouvez consulter les options disponibles de votre clé soi depuis votre panneau de contrôle d’utilisateur soi depuis l’API directement:
			A noter que cette requête n’est pas comptabilisé sur votre compteur de requête.
			[code=php] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "{METHOD}",
		"m" =>  false,
		"t" => "",
		"s" => "",
		"d" => "",
		"o" => "json",
));[/code]'
	),
	array(
		'method' => 'login',//Not translatable
		0 => 'Me connecter à mon compte via l’API',
		1 => 'Vous pouvez vous connecter à votre compte via l’API en utilisant la méthode [i]{METHOD}[/i] sans aucun argument supplémentaire. (Hormis l’email si besoin)
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
[b][color=#BC2A4D]/!\[/color][/b] Cette fonction ignorera le paramètre [b]o[/b] en retournant des données au format HTML!'
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
		0 => 'Ou puis-je consulter mon quota de requêtes ?',
		1 => 'Vous pouvez consulter celui-ci dans votre panneau d’utilisateur dans l’onglet API » Statistiques.'
	),
	array(
		0 => 'J’ai atteint mon quota de requêtes totales, comment continuer à utiliser l’API ?',
		1 => 'Vous pouvez demander à un administrateur d’augmenter votre quota de requête totales ou, si vous en avez la permission, générer une nouvelle clé depuis votre panneau de contrôle de l’utilisateur.'
	),
	array(
		0 => 'Ma clé a expirée, que dois-je faire ?',
		1 => 'Vous pouvez demander à un administrateur de rallonger la durée de vie de votre clé ou, si vous en avez la permission, en générer une nouvelle clé depuis votre panneau de contrôle de l’utilisateur.'
	),
	array(
		0 => 'Ma clé a été désactivée ou suspendue, que dois-je faire ?',
		1 => 'Votre clé à peut-être été suspendue par un administrateur à la suite d’un abus. Notez qu’une clé est automatiquement désactivée lorsque vous en générez une nouvelle.'
	),
	array(
		0 => 'Quel est l’encodage de réponse de l’API ?',
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
		0 => '--HOOKS--',//Not translatable
		1 => 'Additional functionalities [i](extensions)[/i]'
	),
);