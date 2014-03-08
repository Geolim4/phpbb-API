<?php
/**
*
* @package language [Standard french] phpBB API
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
	'UCP_PHPBB_API_ADMIN_KEY'		=> 'Ceci est une clé d’administrateur, pour accéder à la gestion complète de la clé vous devez passer par le panneau d’administration.
										<br />[ %1$sAdministrer la clé%2$s ]',
	'UCP_PHPBB_API_ADMIN_KEY_ONLY'	=> 'Disponible uniquement sur les clés d’administrateur actives.',
	'UCP_PHPBB_API_ADMIN_KEY_INFO'	=> 'Impossible de désactiver l’authentification par email pour les clés d’administrateur.',
	'UCP_PHPBB_API_CONFIRM_EXPLAIN'	=> 'Cela va désactiver la clé <em>%s</em> et en recréer une nouvelle par la suite.',
	'UCP_PHPBB_API_DAILY_USE'		=> 'Utilisation quotidienne',
	'UCP_PHPBB_API_EMAIL'			=> 'Authentifier la clé avec l’e-mail',
	'UCP_PHPBB_API_KB'				=> 'Base de connaissance',
	'UCP_PHPBB_API_FORCE_POST'		=> 'Méthode HTTP «&nbsp;POST&nbsp;» seulement',
	'UCP_PHPBB_API_FORCE_POST_EXP'	=> 'Forcer la clé a être utilisée en <a href="http://fr.wikipedia.org/wiki/Hypertext_Transfer_Protocol#M.C3.A9thodes">POST</a> uniquement.',
	'UCP_PHPBB_API_GENERATE'		=> '<a href="%s">En générer une nouvelle ?</a>',
	'UCP_PHPBB_API_GEN_AUTH'		=> 'Vous n’avez pas la permission de générer une nouvelle clé, contactez un Administrateur pour plus d’informations.',
	'UCP_PHPBB_API_HISTORY'			=> 'Historique',
	'UCP_PHPBB_API_PAGINATION_EVT'	=> '%s évènement',
	'UCP_PHPBB_API_PAGINATION_EVTS'	=> '%s évènements',
	'UCP_PHPBB_API_INFINITE_SYMBOL'	=> '∞',
	'UCP_PHPBB_API_KEY_IPS'			=> 'Filtre IP',
	'ACP_PHPBB_API_KEY_IPS_EXP'		=> 'Pour indiquer plusieurs adresses IPs ou noms d’hôtes différents, entrez chacun d’eux sur une nouvelle ligne. Pour indiquer une plage d’adresses IP, séparez le début et la fin par un tiret, et utilisez * comme caractère joker.',
	'UCP_PHPBB_API_KEY_IPS_TYPE_A'	=> 'IPs autorisées',
	'UCP_PHPBB_API_KEY_IPS_TYPE_D'	=> 'IPs interdites',
	'UCP_PHPBB_API_KEYS'			=> 'Gestion des clés',
	'UCP_PHPBB_API_KEY_ID'			=> 'Clé',
	'UCP_PHPBB_API_SECRET_KEY'		=> 'Clé secrète',
	'UCP_PHPBB_API_SECRET_KEY_EXP'	=> 'La clé secrète est utile uniquement si vous utilisez la fonctionnalité de chiffrage de l’API. Comme la clé API, cette clé est strictement personnelle et vous ne devez pas la partager à des personnes qui ne sont pas de confiance.
										<br />Si vous oubliez cette clé ou si vous pensez qu’elle a été compromise, demandez à un administrateur de la réinitialiser.',
	'UCP_PHPBB_API_KEY_ID_EXP'		=> 'Cette clé est strictement confidentielle, elle est votre identifiant pour pouvoir opérer sur l’API et vous identifie en tant que tel, pour plus de sécurité, vous pouvez forcer l’authentification de cette clé par votre e-mail.',
	'UCP_PHPBB_API_LAST_QUERIES'	=> 'Dernière requête : %s',
	'UCP_PHPBB_API_LOADING'			=> 'Chargement …',
	'UCP_PHPBB_API_MONTHLY_USE'		=> 'Utilisation mensuelle',
	'UCP_PHPBB_API_NO_KEY'			=> 'Aucune clé trouvée.',
	'UCP_PHPBB_API_NO_REQUEST'		=> 'Aucune',
	'UCP_PHPBB_API_PERCENT'			=> '%',
	'UCP_PHPBB_API_QUERIE'			=> '%1$s/%2$s requête',
	'UCP_PHPBB_API_QUERIES'			=> '%1$s/%2$s requêtes',
	'UCP_PHPBB_API_REGENERATE'		=> '<a href="%s">Regénérer une nouvelle clé</a>',
	'UCP_PHPBB_API_STATS'			=> 'Statistiques',
	'UCP_PHPBB_API_STATUS'			=> 'Statut de la clé',
	'UCP_PHPBB_API_STATUS_TYPE'		=> array(
		1		=> '<strong style="color:green">Active</strong>',
		2		=> '<strong style="color:red">Suspendue</strong>',
		3		=> '<strong style="color:grey">Désactivée</strong>',
	),
	'UCP_PHPBB_API_TOTAL_USE'		=> 'Utilisation totale',
	'UCP_PHPBB_API_UNCENSORED'		=> 'Non-censuré',
	'UCP_PHPBB_API_UPDATED_CFG'		=> 'Paramètres sauvegardés',
	'UCP_PHPBB_API_VALIDITY'		=> 'Validité',
	'UCP_PHPBB_API_VALIDITY_LFTM'	=> 'Cette clé est valable à vie',
	'UCP_PHPBB_API_VALIDITY_EXPIRED'=> 'Cette clé est périmée',
	'UCP_PHPBB_API_OUT_OF_QUOTA'	=> 'Cette clé a dépassé son quota total de requête',
	'UCP_PHPBB_API_VALIDITY_UNTIL'	=> 'Cette clé est valable jusqu’au %s',
	'UCP_PHPBB_API_WEEKLY_USE'		=> 'Utilisation hebdomadaire',
	'UCP_PHPBB_API_WITH'			=> 'avec <em title="Nom original: %1$s">%2$s</em>',
));

$lang['UCP_PHPBB_API_KNOWLEDGE_BASE_HOOKS'] = array();//Init the KB hooks array
// Important note to translators & users:
// BBCODE is supported only on key ID: 1
// Use [adminkey][/adminkey] bbcode to add admin-key-only text.
$lang['UCP_PHPBB_API_KNOWLEDGE_BASE'] = array(
	array(
		0 => '--',
		1 => 'Documentation'
	),
	array(
		0 => 'Interaction avec l’API',
		1 => 'Sauf spécifications contraires sur la clé les requêtes peuvent être effectués en GET ou en POST (qui est toutefois recommandé).
			[br]Les requêtes devront être envoyées via le protocole HTTP ou HTTPS si l’Administrateur l’a activé.'
	),
	array(
		0 => 'Communication avec l’API',
		1 => 'Vous trouverez ci-dessus différents moyens de communiquer avec l’API selon le langage que vous utilisez:
			[list]
				[*]Perl (<a href="http://search.cpan.org/~gaas/libwww-perl-6.05/lib/LWP.pm#NETWORK_SUPPORT">LWP</a>)
				[*]Java (<a href="http://docs.oracle.com/javase/7/docs/api/java/net/HttpURLConnection.html">HttpURLConnection</a> | <a href="http://docs.oracle.com/javase/7/docs/api/javax/net/ssl/HttpsURLConnection.html">HttpsURLConnection</a>)
				[*]PHP (<a href="http://php.net/manual/fr/function.fsockopen.php">fsockopen</a> | <a href="http://php.net/manual/fr/book.curl.php">cURL</a>)
				[*]Python (<a href="http://www.python.org/doc/current/lib/module-urllib.html">urllib</a>)
				[*]WinDev (<a href="http://doc.pcsoft.fr/fr-FR/?httprequete_fonction">HTTPRequête</a>)
				[*]Qt (<a href="http://qt-project.org/doc/qt-4.8/qwebview.html">QWebView</a>)
				[*]Windows (<a href="http://support.microsoft.com/default.aspx?scid=kb;fr-fr;168151">WinInet</a> | <a href="http://msdn.microsoft.com/fr-fr/library/system.net.httpwebrequest%28v=vs.110%29.aspx">HttpWebRequest Class</a>)
			[/list]'
	),
	array(
		0 => 'Point d’entrée',
		1 => 'Le point d’entrée de l’API se situe ici: [b]{KB_GATEWAY_INTERFACE}[/b]'
	),

	array(
		0 => 'Les paramètres',
		1 => 'L’API utilise de nombreux paramètres que vous pouvez retrouver ci-dessous:
		[list]
			[*][b]k[/b]: La clé API (obligatoire). Exemple: [i]k={KB_API_KEY}[/i]
			[*][b]e[/b]: L’e-mail authentifiant la clé (si requis). Exemple: [i]e={KB_USER_EMAIL}[/i]
			[*][b]a[/b]: L’action réclamée (obligatoire) Exemple: [i]a=topic[/i] Notez que l’action est disponible également dans votre langage si elle a été traduite, exemple: [i]sujet[/i]
			[*][b]m[/b]: Activation du multibyte: Active la gestion des caractères spéciaux pour les paramètre [b]d[/b] et [b]t[/b] (facultatif). Exemple: [i]m=true[/i]
			[*][b]t[/b]: Type de données qualifiant le paramètre [b]d[/b] (requis selon l’action). Exemple: [i]t=topic_id[/i]
			[*][b]s[/b]: Opérateurs de tri et de comparaison séparés par une virgule. Exemple: [i]s=operator:&lt;&gt;,start:5,limit:10[/i]
			[*][b]d[/b]: Valeur recherchée en fonction du paramètre [b]t[/b]. Exemple: [i]d=24[/i]
			[*][b]o[/b]: Format de sortie(optionnel en POST, JSON par défaut). Exemple: [i]o=json[/i]
			[*][b]c[/b]: Callback JSONP (optionnel, méthode POST seulement). Exemple: [i]c=mafonction()[/i]
			[*][b]f[/b]: Fallback JSONP (optionnel, méthode POST seulement). Exemple: [i]f=mafonction()[/i]
			[*][b]u[/b]: Jointure de données sur l’utilisateur courant (optionnel, désactivé par défaut): Joint à la requête des données sur le propriétaire de la clé. Exemple: [i]u=true[/i]
			[*][b]h[/b]: Active la conversion des temps UNIX en temps textuel (optionnel, désactivé par défaut): Retourne un temps textuel plutôt qu’un entier. Exemple: [i]h=true[/i]
			[*][b]p[/b]: Paramètres GET/POST envoyés (optionnel, désactivé par défaut): Retourne la liste des paramètres GET et POST envoyés au serveur. Exemple: [i]p=true[/i]
			[adminkey][*][b]v[/b]: Active la prise en charge des constantes systèmes (optionnelles, activées par défaut). Exemple: [i]v=true[/i]
			[*][b]i[/b]: Utilise la clé sans privilège d’administrateur, elle seras donc utilisée en tant que clé d’utilisateur (optionnel, désactivé par défaut). Exemple: [i]i=true[/i][/adminkey]
			[*][b]n[/b]: Active les communications cryptées (optionnel, désactivé par défaut). Exemple: [i]n=true[/i]. Veuillez lire attentivement la section [i]Support du chiffrement[/i].[/list]'
	),
	array(
		0 => 'Traductions des méthodes',
		1 => 'La plupart des méthodes de l’API sont traduites et peuvent donc de ce fait être utilisées dans la langue par défaut définie sur votre panneau de de contrôle d’utilisateur.
			[br]Notez que les méthodes sont toujours disponibles dans leur dénomination de base. Ci-dessous vous pouvez récupérer la liste des méthodes actuellement traduites.
			[code]{KB_SERVER_PROTOCOL}{KB_SERVER_NAME}{KB_SCRIPT_PATH}/api/{KB_API_KEY}/get_methods/-/-/json[/code]
			Notez que cette méthode n’est pas traduisible et doit être appelée comme telle.'
	),
	array(
		0 => 'Traductions des sous-méthodes',
		1 => 'La plupart des sous-méthodes de l’API sont également traduites et peuvent donc de ce fait être utilisées dans la langue par défaut définie sur votre panneau de de contrôle d’utilisateur.
			[br]Notez que les sous-méthodes sont toujours disponibles dans leur dénomination de base. Ci-dessous vous pouvez récupérer la liste des sous-méthodes actuellement traduites pour une méthode.
			[code]{KB_SERVER_PROTOCOL}{KB_SERVER_NAME}{KB_SCRIPT_PATH}/api/{KB_API_KEY}/get_submethods/topic/-/json[/code]
			[b][color=#BC2A4D]/!\[/color][/b] La sous-méthode traduite ne peux pas être utilisé en mode [b]GET[/b], vous devrez donc l’appeler selon sa dénomination d’origine. Exemple: <em>topic_id</em>
			Notez que cette méthode n’est pas traduisible et doit être appelée comme telle.'
	),
	array(
		0 => 'Accès simplifié avec le mode GET',
		1 => 'Pour les requêtes simples le mode GET peut être utilisée, tentons de récupérer les informations du sujet avec l’ID N°24 au format JSON :
			[code]{KB_SERVER_PROTOCOL}{KB_SERVER_NAME}{KB_SCRIPT_PATH}/api/{KB_API_KEY}/topic/topic_id/24/json[/code]
			[br]Si la requête s’est bien passée, le serveur retournera une réponse similaire:
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
		[br]Toutefois si la requête s’est mal déroulée une réponse similaire sera retournée:
			[code]{
	msg: "Clé API non autorisée !",
	timing: 0.0056,
	status: "200 OK"
}[/code]
			En cas d’erreur fatale, l’API joindra le degré d’erreur selon les normes de [url=http://php.net/manual/fr/errorfunc.constants.php]PHP[/url]:
	[code]{
	msg: "Erreur critique : Méthode « api_xxxxx » non trouvée ! Le nom de la méthode peut varier selon le language défini dans le compte rattaché à votre clé.",
	errno: 256,
	timing: 0.0056,
	status: "503 Service Unavailable"
}[/code]Sur l’exemple ci-dessus le degré de l’erreur est de type [i]E_USER_ERROR[/i].
			[br]Si la clé requiert une authentification par e-mail ([b]e[/b]) elle devra être suffixée à la clé entre parenthèse:
			[code]{KB_SERVER_PROTOCOL}{KB_SERVER_NAME}{KB_SCRIPT_PATH}/api/{KB_API_KEY}({KB_USER_EMAIL})/topic/topic_id/24/json[/code]
			[br]Le mode GET prends aussi en charge quelques opérateurs de tri (S.S.O) et de comparaison sur la sous-méthode de la méthode courante (voir chapitre sur les accès en « POST »).
				Les opérateurs doivent être suffixés à la sous-méthode de la méthode courante enveloppé avec des crochets, ici nous recherchons seulement les 10 sujets où l’ID de sujet est différent de 24 en ignorant les 5 premiers sujets.
			[code]{KB_SERVER_PROTOCOL}{KB_SERVER_NAME}{KB_SCRIPT_PATH}/api/{KB_API_KEY}({KB_USER_EMAIL})/topic/topic_id(operator:<>,start:5,limit:10)/24/json[/code]
			[b][color=#BC2A4D]/!\[/color][/b] Il est toutefois hautement recommandé d’utiliser le mode POST pour les requêtes compliquées.
			Seul les opérateurs suivants sont supportés: [i]NOT LIKE,LIKE,REGEXP,&nbsp;&lt;&gt;,&nbsp;&gt;,&nbsp;&lt;,&nbsp;=,&nbsp;&lt;=,&nbsp;&gt;=[/i]
			[br]Si la sous-méthode ([b]t[/b]) et la valeur ([b]d[/b]) sont facultatifs, vous pouvez les remplacer par le caractère générique suivant : [b]{KB_WILDCARD_CHAR}[/b]
			[code]{KB_SERVER_PROTOCOL}{KB_SERVER_NAME}{KB_SCRIPT_PATH}/api/{KB_API_KEY}({KB_USER_EMAIL})/key_stats/{KB_WILDCARD_CHAR}/{KB_WILDCARD_CHAR}/json[/code]'
	),
	array(
		0 => 'Accès avancés avec le mode POST',
		1 => 'Le mode POST est [b]hautement[/b] recommandée de manière générale surtout pour les requêtes utilisant des opérateurs et/ou des caractères spéciaux comme des accents.
			Les exemples ci-dessous seront représentés sous forme d’un tableau cURL (PHP).
			[br]Récupérons ici le sujet dont le titre contient le mot « élémentaire »
			[code] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "topic",
		"m" => true,
		"t" => "topic_id",
		"s" => "operator:LIKE",
		"d" => "élémentaire",
		"o" => "json",
));[/code]Nous avons donc utilisé l’O.S.S "LIKE". Le masque "%" est ajouté automatiquement par l’API.
[br]Vous pouvez également utiliser une REGEXP pour affiner les critères de recherche :[code] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
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
[br]Exemple de correspondance : [color=#00BF40]345[/color], [color=#00BF40]1259413[/color], [color=#00BF40]550[/color]
Exemple de non-correspondance : [color=#FF0000]725[/color], [color=#FF0000]05[/color], [color=#FF0000]1358[/color]
Si vous souhaitez que la correspondance se fasse sur la chaîne entière et non une partie de celle-ci vous devez le préciser comme ceci : [b]^[0-5]{3}$[/b]'
	),
	array(
		'cfg' => 'api_mod_crypto_enabled',
		0 => 'upport du chiffrement',
		1 => 'L’API fournit un support basic du chiffrement. Pour démarrer une communication cryptée, vous devez activer le paramètre [b]n[/b].
			L’algorithme de chiffrement actuel est {KB_CRYPTO_CIPHER} (mode : [i]{KB_CRYPTO_MODE}[/i]), mais vous pouvez l’obtenir depuis l’API en utilisant la méthode [b]get_crypto_config[/b].
			[code]{KB_SERVER_PROTOCOL}{KB_SERVER_NAME}{KB_SCRIPT_PATH}/api/{KB_API_KEY}({KB_USER_EMAIL})/get_crypto_config/-/-/json[/code]
			[size=100][u]Demander une réponse ciffrée à l’API :[/u][/size]
			La syntaxe est presque la même que d’habitude, à l’exception du paramètre [b]n[/b] qui est activé.
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
[b][color=#BC2A4D]/!\[/color][/b] L’API retournera un fichier nommé [b]{KB_CRYPTO_FILENAME}[/b] à la place d’une réponse HTTP standard. Ce fichier est chiffrée et vous ne pouvez le déchiffrer qu’avec votre clé secrète.
[br][size=100][u]Déchiffrer une réponse chiffrée de l’API (méthode PHP):[/u][/size]
[code=php]$handle = fopen("{KB_CRYPTO_FILENAME}", "rb");
$encrypted_content = fread($handle, filesize("api.response"));
fclose($handle);
$decrypted_content = mcrypt_decrypt({KB_CRYPTO_CIPHER}, "your_secret_key", $encrypted_content, {KB_CRYPTO_MODE}, "{KB_CRYPTO_IV}");[/code]
Comme vous pouvez le voir ci-dessus, le code utilisé pour déchiffrer le fichier est assez simple. Si l’algorithme de chiffrement a changé depuis votre dernière utilisation, contactez un administrateur pour plus d’informations.'
	),

	array(
		'a_'	=> true,//require the user to have at least an administrator key to view that part
		0 => 'Recherche par constantes systèmes',
		1 => 'Vous pouvez utiliser des constantes de phpBB (et uniquement celles-ci) afin d’effectuer des recherches plus poussées. Vous devez la préfixer avec le signe [b]$[/b]
			[code] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
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
		'method' => 'topic,post,forum,group',//Automatically translated
		0 => 'Récupérer des données de sujets/messages/forums/groupes',
		1 => 'L’API vous permet de récupérer quelques données basiques des sujets/messages/forums/groupes.
			[br]Sur l’exemple ci-dessous, nous tentons de récupérer les données du forum dont l’ID est [b]1[/b]
			[code] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
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
		'method' => 'get_config',//Automatically translated
		0 => 'Récupérer des données de configuration',
		1 => 'L’API vous permet de récupérer quelques données basiques de configuration.
			[br]Récupérons ici toutes les données de configurations basique, comme par exemple la taille maximum des avatars, la description du site ou encore la date d’ouverture du forum :
			[code] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "{METHOD}",
		"m" =>  false,
		"t" => "all",
		"s" => "",
		"d" => "",
		"o" => "json",
));[/code][adminkey]
Vous pouvez également récupérer différents types de configuration avec trois modes différents : « cached », « dynamic », « custom ».
			[br]Récupérons ici toutes les variables de configuration en cache :
			[code] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "{METHOD}",
		"m" =>  false,
		"t" => "cached",
		"s" => "",
		"d" => "",
		"o" => "json",
));[/code]
			[br]Récupérons ici toutes les variables de configuration dynamiques :
			[code] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "{METHOD}",
		"m" =>  false,
		"t" => "dynamic",
		"s" => "",
		"d" => "",
		"o" => "json",
));[/code]
			Récupérons ici des variables de configuration personnalisées, en utilisant le paramètre [b]d[/b] en séparant chaque nom de configuration par une virgule.
			[code] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
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
		0 => 'Récupérer les constantes systèmes disponibles',
		1 => 'Vous pouvez récupérer la liste des constantes système disponibles en utilisant la méthode [b]{METHOD}[/b]
			[code] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
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
		0 => 'Modifier les variables de configuration',
		1 => 'L’API permet également de [u]modifier[/u] les variables de configuration. [color=red]Attention toutefois aux mauvaises manipulations ![/color]
			[br]Modifions ici quelques variables de configuration, nous devons donc faire appel au paramètre [b]d[/b].
			Vous ne pouvez utiliser que deux formats pour envoyer une nouvelle valeur de configuration : JSON et serialize (PHP).
			[br]Les données de configuration à modifier sont envoyées par paire « nom de configuration/valeur »
			[code] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
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
		'method' => 'refresh_stats',//Automatically translated
		'a_'	=> true,//require the user to have at least an administrator key to view that part
		0 => 'Actualiser les statistiques du forum',
		1 => 'Vous pouvez actualiser les statistiques de votre forum directement via l’API, attention toutefois à la fréquence de ces actualisations qui sont gourmandes en ressources.
			[br]Le paramètre [b]t[/b] vous permettra de choisir le type d’actualisation :
			[list]
				[*][b]all[/b]: Force la ré-actualisation de [b]TOUTES[/b] les statistiques (Non recommander en heure de pointe).
				[*][b]num_posts[/b]: Force la ré-actualisation des statistiques de messages.
				[*][b]num_topics[/b]: Force la ré-actualisation des statistiques de sujets.
				[*][b]num_users[/b]: Force la ré-actualisation des statistiques du nombre d’utilisateurs.
				[*][b]num_files[/b]: Force la ré-actualisation des statistiques du nombre de fichiers-joints.
				[*][b]upload_dir_size[/b]: Force la ré-actualisation de la taille du répertoire des fichiers-joints.
				[*][b]update_last_username[/b]: Force la ré-actualisation du dernier utilisateur inscrit.
			[/list]
			[code] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
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
		0 => 'Effectuer une requête SQL',
		1 => 'Vous pouvez effectuer des requêtes SQL directement via l’API (mode POST uniquement), cependant selon la configuration de votre clé vous pouvez ne pas être en mesure de pouvoir modifier les tables sécurisées qui comprennent les tables des logs et de l’API.
			[br]Le paramètre [b]s[/b] est disponible pour les clauses <em>start</em> et <em>limit</em>.
			[br]Il est préférable d’activer le paramètre multibyte ([b]m[/b]) pour des raisons de compatibilité.
			[br]Soyez très prudent lors d’exécution de requêtes sensibles telles que [b]DELETE/DROP/TRUNCATE[/b].
			[br][b][color=#BC2A4D]Pour des raisons de sécurité, toutes les requêtes SQL sont archivées dans les journaux après exécution.[/color][/b]
			[code] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "{METHOD}",
		"m" => true,
		"t" => "all",
		"s" => "start:5,limit:10",
		"d" => \'SELECT * FROM $USERS_TABLE WHERE user_id = 1\',//Vous pouvez utiliser en toute sécurité les constantes de phpBB ici, mais n’oubliez pas d’utiliser des guillemets simples pour éviter une mauvaise interprétation de PHP
		"o" => "json",
));[/code]'
	),
	array(
		'method' => 'perm_ban',//Automatically translated
		'a_'	=> true,//require the user to have at least an administrator key to view that part
		0 => 'Bannissement permanant',
		1 => 'Vous pouvez bannir définitivement une entité telle qu’une IP, un nom d’utilisateur ou bien encore une adresse e-mail.
			Utilisez la sous-méthode ([b]t[/b]) pour définir le type d’entité à bannir tels que <em>user/ip/email</em>.
			La valeur ([b]d[/b]) représentera l’entité à bannir.
			[code] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
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
		0 => 'Débannissement',
		1 => 'Vous pouvez dé-bannir une entité telle qu’une IP, un nom d’utilisateur ou bien encore une adresse e-mail.
			Utilisez la sous-méthode ([b]t[/b]) pour définir le type d’entité à dé-bannir tels que <em>user/ip/email</em>.
			La valeur ([b]d[/b]) représentera l’entité à dé-bannir.
			[code] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
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
		0 => 'Activer/désactiver le forum',
		1 => 'Vous pouvez activer ou désactiver le forum pour des manipulations sensibles par exemple.
			Utilisez la sous-méthode ([b]t[/b]) pour définir le statut du forum <em>activer/désactiver</em> ou <em>true/false</em>.
			La valeur ([b]d[/b]) représentera le message que vous souhaitez afficher en conséquence. Il ne doit cependant pas dépasser 255 caractère au risque d’être tronqué
			[code] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "{METHOD}",
		"m" => false,
		"t" => "désactiver",//Valeurs possibles : false/true/activer/désactiver
		"s" => "",
		"d" => "Le forum a été désactivé via phpBB API.",//Le message qui sera afficher publiquement
		"o" => "json",
));[/code]'
	),
	array(
		'method' => 'key_stats',//Automatically translated
		0 => 'Récupérer les statistiques d’utilisation de la clé',
		1 => 'A tout moment vous pouvez consulter le taux d’utilisation de votre clé soi depuis votre panneau de contrôle d’utilisateur soi depuis l’API directement :
			[code] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "{METHOD}",
		"m" =>  false,
		"t" => "",
		"s" => "",
		"d" => "",
		"o" => "json",
));[/code]
Veuilliez notez que la consultation de vos statistiques n’est pas comptabilisé en tant que requête.'
	),
	array(
		'method' => 'key_options',//Automatically translated
		0 => 'Récupérer les options disponibles de la clé',
		1 => 'A tout moment vous pouvez consulter les options disponibles de votre clé soi depuis votre panneau de contrôle d’utilisateur soi depuis l’API directement :
			[code] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "{METHOD}",
		"m" =>  false,
		"t" => "",
		"s" => "",
		"d" => "",
		"o" => "json",
));[/code]
Veuilliez notez que la consultation de vos statistiques n’est pas comptabilisé en tant que requête.'
	),
	array(
		'method' => 'login',//Automatically translated
		0 => 'Se connecter à votre compte via l’API',
		1 => 'Vous pouvez vous connecter à votre compte via l’API en utilisant la méthode [i]{METHOD}[/i] sans aucun argument supplémentaire. (Hormis l’email si besoin)
			[code] curl_setopt($handle, CURLOPT_POSTFIELDS, array(
		"k" => "{KB_API_KEY}",
		"e" => "{KB_USER_EMAIL}",
		"a" => "{METHOD}",
		"m" =>  false,
		"t" => "",
		"s" => "",
		"d" => "",
		"o" => "json",
));[/code]
[b][color=#BC2A4D]/!\[/color][/b] Cette fonction ignorera le paramètre [b]o[/b] en retournant des données au format HTML !'
	),
	// This block will switch the knowledge to the second template column
	array(
		0 => '--',
		1 => '--'
	),
	array(
		0 => '--',
		1 => 'FAQ (Questions posées fréquemment)'
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
		1 => 'Lexique'
	),
	array(
		0 => 'O.S.S',
		1 => 'Abréviation de <em>Opérateur SQL Sécurisé</em> : Opérateurs SQL parsés afin de sécuriser les requêtes SQL.'
	),
	array(
		0 => 'REGEXP',
		1 => 'Acronyme de <em>expressions régulières</em> ou <em> expressions rationnelles</em> : masque de caractère permettant de faire correspondre une chaine de caractère. <a href="https://fr.wikipedia.org/wiki/Expression_rationnelle">Plus de détails</a>.'
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
		1 => 'Fonctionnalités additionnelles [i](extensions)[/i]'
	),
);