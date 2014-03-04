<?php
/**
*
* @package language [Standard french] phpBB API
^>@version $Id: info_acp_phpbb_api.php v0.0.1 00h11 12/20/2013 Geolim4 Exp $
* @copyright (c) 2012 - 2014 Geolim4.com http://geolim4.com
* @bug/function request: http://geolim4.com/tracker
* @translator papicx 28/11/2013 14h25  version e papicx@phpbb-fr.com
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
$api_lang_suffix = '';
global $config;
$lang = array_merge($lang, array(
	'G_API_MANAGER'						=> 'Gestionnaire API',
//ACP hook management
	//UMIL
	'ACP_PHPBB_API_CONFIG_UMIL_PHP'				=> 'Version de PHP',
	'ACP_PHPBB_API_CONFIG_UMIL_PHP_OK'			=> 'Vous possédez PHP <strong>%s</strong> ou supérieur, vous pouvez continuer l’installation.',
	'ACP_PHPBB_API_CONFIG_UMIL_PHP_NO'			=> 'Vous ne possédez pas PHP <strong>%s</strong> ou supérieur, impossible de continuer l’installation',
	'ACP_PHPBB_API_CONFIG_UMIL_REFLECTION'		=> 'Extension Reflection',
	'ACP_PHPBB_API_CONFIG_UMIL_REFLECTION_OK'	=> 'L’extension <em><a href="http://php.net/manual/fr/book.reflection.php">Reflection</a></em> est présente.',
	'ACP_PHPBB_API_CONFIG_UMIL_REFLECTION_NO'	=> 'L’extension <em><a href="http://php.net/manual/fr/book.reflection.php">Reflection</a></em> est manquante, contactez votre hébergeur.',
	'ACP_PHPBB_API_CONFIG_UMIL_PCHART'			=> 'Librairie pChart',
	'ACP_PHPBB_API_CONFIG_UMIL_PCHART_OK'		=> 'La librairie pChart est présente.',
	'ACP_PHPBB_API_CONFIG_UMIL_PCHART_NO'		=> 'pChart est absent, les statistique seront donc désactivés, <em><a href="http://geolim4.com">plus d’information</a></em>.',
	'ACP_PHPBB_API_CONFIG_UMIL_ZIP'				=> 'Extension ZIP',
	'ACP_PHPBB_API_CONFIG_UMIL_ZIP_OK'			=> 'L’extension <em><a href="http://php.net/manual/fr/book.zip.php">ZIP</a></em> est présente.',
	'ACP_PHPBB_API_CONFIG_UMIL_ZIP_NO'			=> 'L’extension <em><a href="http://php.net/manual/fr/book.zip.php">ZIP</a></em> est manquante, contactez votre hébergeur.',
	'ACP_PHPBB_API_CONFIG_UMIL_CURL'			=> 'Extension cURL',
	'ACP_PHPBB_API_CONFIG_UMIL_CURL_OK'			=> 'L’extension <em><a href="http://php.net/manual/fr/book.curl.php">cURL</a></em> est présente.',
	'ACP_PHPBB_API_CONFIG_UMIL_CURL_NO'			=> 'L’extension <em><a href="http://php.net/manual/fr/book.curl.php">cURL</a></em> est manquante, contactez votre hébergeur.',
	'ACP_PHPBB_API_CONFIG_UMIL_CURL'			=> 'Extension Mcrypt',
	'ACP_PHPBB_API_CONFIG_UMIL_CURL_OK'			=> 'L’extension <em><a href="http://php.net/manual/fr/book.mcrypt.php">Mcrypt</a></em> est présente.',
	'ACP_PHPBB_API_CONFIG_UMIL_CURL_NO'			=> 'L’extension <em><a href="http://php.net/manual/fr/book.mcrypt.php">Mcrypt</a></em> est manquante, contactez votre hébergeur.',

	'ACP_PHPBB_API_HOOK_AUTHOR'			=> 'Auteur',
	'ACP_PHPBB_API_HOOK_DATE'			=> 'Date',
	'ACP_PHPBB_API_HOOK_DELETED'		=> 'Le hook %s a été supprimé du serveur.',
	'ACP_PHPBB_API_HOOK_DELETE_ERR'		=> 'Impossible de supprimer un hook non-désinstallé !',
	'ACP_PHPBB_API_HOOK_FILE'			=> 'Fichier',
	'ACP_PHPBB_API_HOOK_INSTALL'		=> 'Installer',
	'ACP_PHPBB_API_HOOK_INSTALLED'		=> 'Hooks installés',
	'ACP_PHPBB_API_HOOK_NAME'			=> 'Nom',
	'ACP_PHPBB_API_HOOK_NO_UPLOAD'		=> 'Aucun fichier n’a été envoyé, peut être que celui-ci dépasse la limite autorisé par votre hébergeur : %s MB',
	'ACP_PHPBB_API_HOOK_OFFICIAL'		=> 'Officiel',
	'ACP_PHPBB_API_HOOK_UNINSTALL'		=> 'Désinstaller',
	'ACP_PHPBB_API_HOOK_UNINSTALLED'	=> 'Hooks non installés',
	'ACP_PHPBB_API_HOOK_UNOFFICIAL'		=> 'Non-officiel',
	'ACP_PHPBB_API_HOOK_VERSION'		=> 'Version',

//API's Stats
	'ACP_PHPBB_API_STATS_ALL_QR'	=> 'Nombre de requêtes',
	'ACP_PHPBB_API_STATS_DAY'		=> 'Statistiques journalières',
	'ACP_PHPBB_API_STATS_DAY_EXP'	=> 'Cliquez sur un article quotidien ci-dessus pour plus de détails.',
	'ACP_PHPBB_API_STATS_DAY_FMT'	=> '{daystr} {dayint} {monthstr} {yearint}',//Jeudi 7 Avril 2013// Ce n'est pas traduisible, vous pouvez seulement ré-ordonner selon votre langage
	'ACP_PHPBB_API_STATS_DAY_REQ'	=> 'Requêtes/heure',
	'ACP_PHPBB_API_STATS_DAY_IP'	=> 'IPs/heure',
	'ACP_PHPBB_API_STATS_MONTH_IP'	=> 'IPs/jour',
	'ACP_PHPBB_API_STATS_YEAR_IP'	=> 'IPs/mois',
	'ACP_PHPBB_API_STATS_HISTORY'	=> 'Historique',
	'ACP_PHPBB_API_STATS_HOUR'		=> 'G \h',//14h http://php.net/manual/fr/function.date.php
	'ACP_PHPBB_API_STATS_HOURS'		=> 'Statistiques horaires',
	'ACP_PHPBB_API_STATS_HOURS_EXP'	=> 'Cliquez sur un point horaire ci-dessus pour plus de détails.',
	'ACP_PHPBB_API_STATS_MONTH'		=> 'Statistiques mensuelles',
	'ACP_PHPBB_API_STATS_MONTH_REQ'	=> 'Requêtes/jour',
	'ACP_PHPBB_API_STATS_TOTAL'		=> '(%s requêtes totales)',
	'ACP_PHPBB_API_STATS_YEAR'		=> 'Statistiques annuelles',
	'ACP_PHPBB_API_STATS_YEAR_REQ'	=> 'Requêtes/mois',

//API's Logs
	'API_LOGS_CLEAR'				=> '<strong>Journal de l’API purgé</strong><br />» %s messages supprimés' . $api_lang_suffix,

	'API_LOG_API_KEY_DEACTIVATED'	=> '<strong>Désactivation de sa clé</strong>' . $api_lang_suffix,
	'API_LOG_API_KEY_OPTION'		=> 'Consultation des options de sa clé' . $api_lang_suffix,
	'API_LOG_API_LOGIN_ACCOUNT'		=> 'Connexion au compte via l’API' . $api_lang_suffix,
	'API_LOG_BAD_AUTH_EMAIL'		=> '<strong>Authentification de la clé refusée</strong><br />» E-mail utilisé :</strong> %s' . $api_lang_suffix,
	'API_LOG_BAD_AUTH_NO_EMAIL'		=> '<strong>Authentification de la clé refusée:</strong><br />» Aucun e-mail renseigné' . $api_lang_suffix,
	'API_LOG_BAD_AUTH_USER'			=> '<strong>Authentification de la clé refusée:</strong><br />» L’utilisateur attaché à la clé a été supprimé' . $api_lang_suffix,
	'API_LOG_BAD_AUTH_IP'			=> '<strong>Authentification refusée :</strong><br />» IP non autorisée' . $api_lang_suffix,
	'API_LOG_BAD_AUTH_KEY'			=> '<strong>Authentification échouée :</strong><br />» Clé invalide' . $api_lang_suffix,
	'API_LOG_BAD_AUTH_OUDATED'		=> '<strong>Authentification refusée :</strong><br />» Clé périmée' . $api_lang_suffix,
	'API_LOG_BAD_AUTH_OUT_OF_QUOTA'	=> '<strong>Authentification refusée :</strong><br />» Quota atteint' . $api_lang_suffix,
	'API_LOG_BAD_AUTH_DEACTIVATED'	=> '<strong>Authentification refusée :</strong><br />» Clé désactivée' . $api_lang_suffix,
	'API_LOG_BAD_AUTH_SUSPENDED'	=> '<strong>Authentification refusée :</strong><br />» Clé suspendue' . $api_lang_suffix,
	'API_LOG_BAN_EMAIL'				=> '<strong>Bannissement d’une adresse e-mail</strong><br />» %s' . $api_lang_suffix,
	'API_LOG_BAN_IP'				=> '<strong>Bannissement d’une adresse IP</strong><br />» %s' . $api_lang_suffix,
	'API_LOG_BANNED_IP'				=> '<strong>Authentification refusée :</strong><br />» IP bannie (%s tentatives)' . $api_lang_suffix,
	'API_LOG_BAN_USER'				=> '<strong>Bannissement d’un utilisateur</strong><br />» %s' . $api_lang_suffix,
	'API_LOG_CLEAR'					=> '<strong>Journal de l’API purgé</strong><br />» %s messages supprimés' . $api_lang_suffix,
	'API_LOG_CLEARED'				=> '<strong>Journal de l’API purgé</strong>' . $api_lang_suffix,
	'API_LOG_ERROR_CLEARED'			=> '<strong>Journal d’erreur de l’API purgé</strong>' . $api_lang_suffix,
	'API_LOG_CONFIG_UPDATED'		=> 'Mise à jour des paramètres de phpBB API',
	'API_LOG_DEACTIVATED_METHOD'	=> '<strong>Tentative d’utilisation d’une méthode désactivée</strong><br />» %s' . $api_lang_suffix,
	'API_LOG_DENIED_PRIVILEGE'		=> '<strong>Privilège refusé</strong><br />» %s' . $api_lang_suffix,
	'API_LOG_FATAL_ERROR'			=> '<strong>Une erreur fatale de PHP est survenue :</strong><br /><strong>Fichier : </strong><code>%1$s</code><br /><strong>Ligne : </strong><code>%2$s</code><br /><strong>Message : </strong><code>%3$s</code>' . $api_lang_suffix,
	'API_LOG_NON_FATAL_ERROR'		=> '<strong>Une erreur a été retournée par le débogueur :</strong><br /><strong>Fichier : </strong><code>%1$s</code><br /><strong>Ligne : </strong><code>%2$s</code><br /><strong>Message : </strong><code>%3$s</code>' . $api_lang_suffix,
	'API_LOG_GET_CONFIG'			=> '<strong>Récupération des données de configuration :</strong> %s' . $api_lang_suffix,
	'API_LOG_KEY_ACTIVE'			=> '<strong>Ré-activation de la clé API <em>%s</em></strong>',
	'API_LOG_KEY_CREATED'			=> '<strong>Création de la clé API <em>%s</em></strong>',
	'API_LOG_KEY_DELETED'			=> '<strong>Suppression de la clé API <em>%s</em></strong>',
	'API_LOG_KEY_DEACTIVATE'		=> '<strong>Désactivation de la clé API <em>%s</em></strong>',
	'API_LOG_KEY_SUSPEND'			=> '<strong>Suspension de la clé API <em>%s</em></strong>',
	'API_LOG_KEY_UPDATED'			=> '<strong>Mise à jour des options de la clé API <em>%s</em></strong>',
	'API_LOG_KEY_REINITIALIZED'		=> '<strong>Clé secrète de la clé API <em>%s</em> réinitialisée</strong>',
	'API_LOG_PURGE_CACHE'			=> '<strong>Cache purgé</strong>' . $api_lang_suffix,
	'API_LOG_RESYNC_STAT'			=> '<strong>Resynchronisation partielle des statistiques</strong>' . $api_lang_suffix,
	'API_LOG_RESYNC_STATS'			=> '<strong>Messages, sujets et statistiques utilisateurs resynchronisés</strong>' . $api_lang_suffix,
	'API_LOG_SQL_QUERY'				=> '<strong>Exécution d’une requête SQL :</strong>&nbsp;<textarea rows="2" cols="1" readonly="readonly" class="logsql">%s</textarea>' . $api_lang_suffix,
	'API_LOG_SQL_QUERY_UNAUTHORIZED'=> '<strong>Requête SQL non autorisée :</strong>&nbsp;<textarea rows="2" cols="1" readonly="readonly" class="logsql">%s</textarea>' . $api_lang_suffix,

	'API_LOG_UNBAN_IP'				=> '<strong>Débannissement d’une adresse IP</strong><br />» %s' . $api_lang_suffix,
	'API_LOG_UNBAN_EMAIL'			=> '<strong>Débannissement d’une adresse e-mail</strong><br />» %s' . $api_lang_suffix,
	'API_LOG_UNBAN_USER'			=> '<strong>Débannissement d’un utilisateur</strong><br />» %s' . $api_lang_suffix,

	'ACP_PHPBB_API'						=> 'phpBB API',
	'ACP_PHPBB_API_ACTIVE'				=> 'Ré-activer',

	'ACP_PHPBB_API_BACKTRACE'			=> 'Activer le backtrace de l’API',
	'ACP_PHPBB_API_BACKTRACE_EXP'		=> 'Cette option vous permettra de tracer les erreurs potentielles de l’API.
											<br />Notez que seules les clés des Administrateurs possèdent le privilège de voir les exceptions dans la réponse de l’API. Dans tous les cas, les exceptions seront toujours enregistrées dans les journaux.',
	'ACP_PHPBB_API_BAN_RECORDED'		=> '%d bannissement expiré enregistré',
	'ACP_PHPBB_API_BANS_RECORDED'		=> '%d bannissements expirés enregistrés',

	'ACP_PHPBB_API_CHANGE'				=> 'Changer',
	'ACP_PHPBB_API_CLOCK'				=> 'Horloge',
	'ACP_PHPBB_API_CLOSE'				=> 'Fermer',
	'ACP_PHPBB_API_CONFIG'				=> 'Configuration',
	'ACP_PHPBB_API_CREATE'				=> 'Créer',
	'ACP_PHPBB_API_CREATED'				=> 'La clé <strong>%s</strong> a été créée avec succès !',
	'ACP_PHPBB_API_CREATE_EXP'			=> 'Créer une nouvelle clé.',
	'ACP_PHPBB_API_CREATION_DATE'		=> 'Date de création',
	'ACP_PHPBB_API_CACHE_STATS'			=> 'Statistique en cache',
	'ACP_PHPBB_API_CACHE_STATS_EXP'		=> 'Cette option vous permettra de mettre les statistiques en cache afin d’alléger la charge du serveur, toutefois celle-ci ne seront actualisé qu’à intervalle de quelques heures.',
	'ACP_PHPBB_API_CRON_TASK'			=> 'Tâches CRON',
	'ACP_PHPBB_API_CRON_TASK_EXP'		=> 'Les tâches CRON enverrons un rapport de statistiques périodique à tout les fondateurs du forum et effectueront un nettoyage partiel de l’API.',

	'ACP_PHPBB_API_DB_CREDENTIALS'		=> 'Données de connexion',
	'ACP_PHPBB_API_DB_NO_CHANGE'		=> 'Ne rien modifier',
	'ACP_PHPBB_API_DB_PASSWORD'			=> 'Mot de passe',
	'ACP_PHPBB_API_DB_SETTINGS'			=> 'Utiliser des identifiants de base de données distincts pour l’API',
	'ACP_PHPBB_API_DB_USERNAME'			=> 'Nom d’utilisateur',
	'ACP_PHPBB_API_DB_WARNING'			=> 'Par sécurité, vous devez ressaisir les identifiants de base de données même si vous les avez déjà entrés auparavant.',

	'ACP_PHPBB_API_DEACTIVATE'			=> 'Désactiver',
	'ACP_PHPBB_API_DEACTIVATED_METHODS'	=> 'Méthodes désactivées',
	'ACP_PHPBB_API_DEACTIVATED_METHODS_EXP'	=> 'Sélectionnez une ou plusieurs méthodes d’un coup en utilisant la combinaison de touches appropriée avec votre clavier et votre souris.
											<br />Notez que certaines méthodes sont déjà réservées aux clés d’administrateur. Consultez le %1$smanuel%2$s pour plus d’informations.',
	'ACP_PHPBB_API_DEFAULT_SETTINGS'	=> 'Paramètres par défaut',
	'ACP_PHPBB_API_DELETE'				=> 'Supprimer',
	'ACP_PHPBB_API_DELETED'				=> 'La clé <strong>%s</strong> a été supprimée avec succès !',

	'ACP_PHPBB_API_EDIT'				=> 'Éditer',
	'ACP_PHPBB_API_ERRORS_HANDLER'		=> 'Gestion des erreurs de l’API',
	'ACP_PHPBB_API_EVENT_ID'			=> 'Identifiant d’évènement',
	'ACP_PHPBB_API_EXPIRATION_DATE'		=> 'Date d’expiration',

	'ACP_PHPBB_API_FAQ_MULTI_COLUMN'	=> 'FAQ multi-colonnes',
	'ACP_PHPBB_API_FATAL_HTML'			=> 'Afficher les erreurs fatales au format HTML',
	'ACP_PHPBB_API_FATAL_HTML_EXP'		=> 'Si activé, l’API tentera de formater les erreurs fatales de PHP au format HTML plutôt qu’au format de sortie choisi par l’utilisateur',
	'ACP_PHPBB_API_FORCE_LOGOUT'		=> 'Forcer la déconnexion',
	'ACP_PHPBB_API_FORCE_LOGOUT_EXP'	=> 'Pour plus de sécurité, chaque requête à l’API sera terminée par une déconnexion de la session. Cela impose néanmoins deux requêtes SQL supplémentaires par requête à l’API.',
	'ACP_PHPBB_API_FORCE_SSL'			=> 'Forcer le protocole SSL',
	'ACP_PHPBB_API_FORCE_SSL_EXP'		=> 'Si activé, l’API rejettera toutes les requêtes qui n’utilisent pas le protocole SSL (HTTPS).
											<br /><strong>Attention :</strong> Votre serveur doit avoir un certificat SSL valide faute de quoi les clients recevront une alerte de sécurité !',

	'ACP_PHPBB_API_HEADER'				=> 'En-tête phpBB API',
	'ACP_PHPBB_API_HEADER_EXP'			=> 'Si activé, le serveur renvera un en-tête “<em>X-Powered-By: phpBB API/' . $config['api_mod_version'] . '</em>”',
	'ACP_PHPBB_API_CRYPTO_ENABLED'		=> 'Activer le support du chiffrement',
	'ACP_PHPBB_API_CRYPTO_ENABLED_EXP'	=> 'Cela activera le support du chiffrement, voir la base de connaissance de l’API pour en savoir plus.',
	'ACP_PHPBB_API_HOOKS'				=> 'Gestion des Hooks',
	'ACP_PHPBB_API_HOOKS_EXPLAIN'		=> 'Sur cette page vous pourrez gérer les hooks de l’API, et consulter les dernières mise à jour de ceux-ci.',
	'ACP_PHPBB_API_HOOKS_INSTALLED_ERR'	=> 'Ce hook a déjà été installé, merci de le désinstaller avant de procéder à sa réinstallation.',
	'ACP_PHPBB_API_HOOKS_UPLOAD_FAIL'	=> 'Une erreur s’est produite lors de l’initialisation du processus d’upload.',

	'ACP_PHPBB_API_IGNORE_COUNTER'		=> 'Notez qu’il est possible d’ignorer ces limites en utilisant les permissions d’utilisateurs.',

	'ACP_PHPBB_API_KEYS'					=> 'Gestion des clés',
	'ACP_PHPBB_API_KEY_ASSIGNED'			=> 'Clé assignée à',
	'ACP_PHPBB_API_KEY_ASSIGNED_WARNING'	=> '<strong class="error">Attention</strong> : Changer le propriétaire de la clé <strong>n’empêche pas</strong> l’ancien propriétaire d’utiliser la clé ! Si vous changez le propriétaire, la clé secrète sera réinitialisée.',
	'ACP_PHPBB_API_KEY_EMAIL'				=> 'Clé authentifiée',
	'ACP_PHPBB_API_KEY_EMAIL_EXP'			=> 'Cette clé ne pourra être utilisée seulement si l’adresse e-mail associée au proriétaire de la clé est fournie.
												<br />Notez que ce paramètre est forcé pour les clés de type «&nbsp;Administrateur&nbsp;».',
	'ACP_PHPBB_API_KEY_FORCE_POST'			=> 'Forcer la méthode POST',
	'ACP_PHPBB_API_KEY_FORCE_POST_EXP'		=> 'Cela empêchera la clé d’être utilisée autrement qu’avec la méthode POST.',
	'ACP_PHPBB_API_KEY_HISTORY'				=> 'Historique brut',
	'ACP_PHPBB_API_KEY_HISTORY_DET'			=> 'Historique détaillé',
	'ACP_PHPBB_API_KEY_INDEX'				=> 'Index des clés',
	'ACP_PHPBB_API_KEY_INVALID_TIME'		=> 'La date d’expiration saisie est invalide pour la clé <strong>%s</strong>, retour à la valeur par défaut',
	'ACP_PHPBB_API_KEY_INVALID_USERNAME'	=> 'Le nom d’utilisateur saisi est invalide pour la clé <strong>%s</strong>, retour à la valeur par défaut',
	'ACP_PHPBB_API_KEY_IPS'					=> 'Filtre IP',
	'ACP_PHPBB_API_KEY_IPS_EXP'				=> 'Pour indiquer plusieurs adresses IPs différentes, entrez chacune d’elles sur une nouvelle ligne. Vous pouvez utiliser «&nbsp;*&nbsp;» comme joker.',
	'ACP_PHPBB_API_KEY_IPS_TYPE_A'			=> 'IPs autorisées',
	'ACP_PHPBB_API_KEY_IPS_TYPE_D'			=> 'IPs interdites',
	'ACP_PHPBB_API_KEY_NO_KEY'				=> 'Aucune clé trouvée.',
	'ACP_PHPBB_API_KEY_OUTDATED'			=> 'La clé <strong>%s</strong> est maintenant périmée',

	'ACP_PHPBB_API_KEY_QUERY_SQL'			=> 'Requêtes SQL',
	'ACP_PHPBB_API_KEY_QUERY_SQL_API'		=> 'Requêtes SQL sur la table de stockage des clés/logs de l’API',
	'ACP_PHPBB_API_KEY_QUERY_SQL_API_EXP'	=> 'Vous ne devriez accorder cette fonctionnalité seulement si vous avez une entière confiance au propriétaire de cette clé.
										<br />En effet, cette fonctionnalité étend les possibilités des requêtes SQL en autorisant celle-ci sur les tables de l’API et des logs.
										<br />Il est donc techniquement possible d’usurper ces clés API, après avoir effectué une requête de type «&nbsp;SELECT&nbsp;» sur les tables de l’API et/ou une requête de type «&nbsp;DELETE&nbsp;» sur la table des logs.',
	'ACP_PHPBB_API_KEY_QUERY_SQL_EXP'		=> 'Soyez vigilant, cette fonctionnalité autorise la clé à effectuer n’importe quelle requête SQL, sauf sur les tables de l’API et des logs.',

	'ACP_PHPBB_API_KEY_SELECT'			=> 'Sélectionner la clé',
	//Constants are unavailable here :/
	'ACP_PHPBB_API_KEY_STATUS'			=> array(
						1	=> 'Clé active',
						2	=> 'Clé suspendue',
						3	=> 'Clé désactivée',
	),
	'ACP_PHPBB_API_KEY_STATUS_EXP'		=> 'Statut de la clé',

	'ACP_PHPBB_API_KEY_TIME_EXP'		=> '<strong>Important</strong> : Pour améliorer la précision des heures et des minutes, vérifiez l’exactitude de votre fuseau horaire et de l’heure d’été en vigueur !',
	'ACP_PHPBB_API_KEY_TITLE'			=> 'Clé',
	'ACP_PHPBB_API_KEY_SECRET_KEY'		=> 'Clé secrète',
	'ACP_PHPBB_API_KEY_SECRET_KEY_EXP'	=> 'La clé secrète est liée au chiffrement et seulement l’utilisateur la connait. Cependant, vous pouvez la changer si nécéssaire.',
	'ACP_PHPBB_API_KEY_TOOLS'			=> 'Outils',
	'ACP_PHPBB_API_KEY_TYPE_A'			=> 'Clé d’administrateur',
	'ACP_PHPBB_API_KEY_TYPE_U'			=> 'Clé d’utilisateur',
	'ACP_PHPBB_API_KEY_UPDATED'			=> 'Clé <strong>%s</strong> mise à jour',
	'ACP_PHPBB_API_KEY_SECRET_UPDATED'	=> 'Clé secrète de la clé <strong>%s</strong> réinitialisée !',

	'ACP_PHPBB_API_LEGEND'				=> '╚═►',
	'ACP_PHPBB_API_LEGEND_DEACTIVATED'	=> 'Clé désactivée',
	'ACP_PHPBB_API_LEGEND_OUTDATED'		=> 'Clé périmée',
	'ACP_PHPBB_API_LEGEND_OUT_OF_QUOTA' => 'Quota atteint',
	'ACP_PHPBB_API_LEGEND_SUSPENDED'	=> 'Clé suspendue',

	'ACP_PHPBB_API_LIFETIME'			=> 'A vie',
	'ACP_PHPBB_API_LIST_IP'				=> 'Autoriser les utilisateurs à interdire/autoriser une ou plusieurs IP',
	'ACP_PHPBB_API_LOADING'				=> 'Chargement …',
	'ACP_PHPBB_API_LOAD_STATS'			=> 'Récupération des statistiques …',
	'ACP_PHPBB_API_LOG_ALTERED'			=> '<strong>Modification des paramètres de phpBB API</strong>',
	'ACP_PHPBB_API_LOG_OFF'				=> '<strong>phpBB API désactivé car l’installation est incomplète.</strong><br />»Pour plus d’informations consultez phpBB API afin de voir les erreurs retournées.',
	'ACP_PHPBB_API_LOG_UPDATE'			=> 'Mise à jour du MOD phpBB API depuis la version <strong style="color: red;">%s</strong> vers la version <strong style="color: green;">%s</strong>',

	'ACP_PHPBB_API_LOGS'				=> 'Journal d’activité',
	'ACP_PHPBB_API_LOGS_EXPLAIN'		=> 'Liste des actions effectuées via l’API. Vous pouvez trier par nom, date, IP ou par action. Si vous avez les permissions nécessaires vous pouvez aussi effacer individuellement les opérations ou le journal complet.',

	'ACP_PHPBB_API_ERR_LOGS'			=> 'Journal d’erreurs',
	'ACP_PHPBB_API_ERR_LOGS_EXPLAIN'	=> 'Liste des erreurs survenues via l’API. Vous pouvez trier par nom, date, IP ou par action. Si vous avez les permissions nécessaires vous pouvez aussi effacer individuellement les opérations ou le journal complet.',
	'ACP_PHPBB_API_ERR_LOGS_HARD'		=> 'Taille du journal d’erreurs physique : %s',
	'ACP_PHPBB_API_ERR_LOGS_HARD_EXP'	=> 'Le journal d’erreurs physique contient toutes les erreurs fatales y compris celle qui n’ont pu être enregistrés en base de données',
	'ACP_PHPBB_API_ERR_LOGS_PURGE'		=> 'Vider',
	'ACP_PHPBB_API_ERR_LOGS_PURGE_ERROR'=> 'Impossible de vider le journal d’erreurs physique, fichier non accessible en écriture !',

	'ACP_PHPBB_API_MAX_ATTEMPS'			=> 'Nombre maximum de tentatives d’essaie de clé par adresse IP',
	'ACP_PHPBB_API_MAX_ATTEMPS_EXP'		=> 'Le seuil de tentatives d’essaie de clé autorisée pour une adresse IP avant d’être exclu temporairement de l’API. Entrez 0 pour désactiver cette fonctionnalité.',
	'ACP_PHPBB_API_MAX_ATTEMPS_TIME' 	=> 'Temps d’expiration des tentatives d’essaie de clé par adresse IP',
	'ACP_PHPBB_API_MAX_ATTEMPS_TIME_EXP'=> 'Les tentatives d’essaie de clé expirent après cette période.',
	'ACP_PHPBB_API_MAX_QUERIES'			=> 'Nombre maximum total de requêtes',
	'ACP_PHPBB_API_MAX_QUERIES_SHORT'	=> 'Requêtes max.',
	'ACP_PHPBB_API_MOD'					=> 'Activer phpBB API',
	'ACP_PHPBB_API_MQPD'				=> 'Nombre maximum de requêtes par jour',
	'ACP_PHPBB_API_MQPM'				=> 'Nombre maximum de requêtes par mois',
	'ACP_PHPBB_API_MQPW'				=> 'Nombre maximum de requêtes par semaine',
	'ACP_PHPBB_API_MQ_EXPLAIN'			=> 'Mettre <strong>0</strong> pour désactiver cette restriction.',

	'ACP_PHPBB_API_NEXT'				=> 'Suivant',
	'ACP_PHPBB_API_NOW'					=> 'Maintenant',
	'ACP_PHPBB_API_NO_BAN_FOUND'		=> '---Pas de bannissement trouvé---',

	'ACP_PHPBB_API_OPERATION_SUCCESS'	=> 'Opération terminée avec succès.',
	'ACP_PHPBB_API_ORIGIN_HEADER'		=> 'Politique «&nbsp;same-origin&nbsp;»',
	'ACP_PHPBB_API_ORIGIN_HEADER_EXP'	=> 'Si activé, l’API retournera un en-tête «&nbsp;same-origin&nbsp;», qui protégera l’API contre la plupart des exploits CSRF.',

	'ACP_PHPBB_API_PAGINATION'			=> 'Nombre d’éléments par page',
	'ACP_PHPBB_API_PAGINATION_KEY'		=> '%s clé',
	'ACP_PHPBB_API_PAGINATION_KEYS'		=> '%s clés',
	'ACP_PHPBB_API_PCHART_CHECK'		=> 'Vérification de l’installation de pChart',
	'ACP_PHPBB_API_PCHART_CHECKED'		=> 'La vérification s’est terminé sans problèmes.',
	'ACP_PHPBB_API_PREV'				=> 'Précédent',
	'ACP_PHPBB_API_PURGE_FILES'			=> '%1$s fichier(s), %2$s',
	'ACP_PHPBB_API_PURGE_API'			=> 'Purger les fichiers temporaires de l’API',
		'ACP_PHPBB_API_PURGE_BANS'		=> 'Purger les bannissements expirées',
	'ACP_PHPBB_API_PURGE_TEMP'			=> 'Purger les fichiers temporaires des statistiques',

	'ACP_PHPBB_API_QUERIES'				=> 'Requêtes',
	'ACP_PHPBB_API_QUERIES_EXP'			=> 'Nombre actuel de requêtes faites avec cette clé.',
	'ACP_PHPBB_API_QUERY_LIMIT'			=> 'Limite du nombre de résultat par défaut',
	'ACP_PHPBB_API_QUERY_LIMIT_EXP'		=> 'Cette fonctionnalité permettra de définir le nombre de résultats maximum renvoyé par la base de données dans certaines méthodes de l’API.',

	'ACP_PHPBB_API_RESET'				=> 'Ceci va ré-initialiser le compteur de requêtes de cette clé.\nÊtes vous sûr de vouloir continuer ?',
	'ACP_PHPBB_API_RESULT'				=> 'Résultats par pages',

	'ACP_PHPBB_API_SELECTOR'			=> 'Action',
	'ACP_PHPBB_API_SETTINGS_ACP'		=> 'Paramètres ACP de l’API',
	'ACP_PHPBB_API_SETTINGS_UCP'		=> 'Paramètres UCP de l’API',
	'ACP_PHPBB_API_SHA1'				=> 'SHA1',
	'ACP_PHPBB_API_STAT_LIMIT'			=> 'Limite du nombre d’évènements affichés dans les statistiques',
	'ACP_PHPBB_API_STATS'				=> 'Statistiques',
	'ACP_PHPBB_API_SUSPEND'				=> 'Suspendre',
	'ACP_PHPBB_API_TIME_TYPE'			=> 'Base de temps',
	'ACP_PHPBB_API_TIME_CALENDAR'		=> 'Temps calendaires',
	'ACP_PHPBB_API_TIME_ROLLING'		=> 'Temps glissants (recommandé)',
	'ACP_PHPBB_API_TYPE'				=> 'Type de clé',
	'ACP_PHPBB_API_TYPE_EXP'			=> 'Notez que les clés d’administrateur ne peuvent pas être gérées depuis le panneau de contrôle de l’utilisateur.',

	'ACP_PHPBB_API_UCP_KEYS'			=> 'Activer les clés d’utilisateurs',
	'ACP_PHPBB_API_UCP_KEYS_EXP'		=> 'Ceci permettra à vos utilisateurs de gérer leurs clés de façon autonome.',
	'ACP_PHPBB_API_UCP_URL_CRYPT'		=> 'Chiffrer la clé dans l’URL',
	'ACP_PHPBB_API_UCP_URL_CRYPT_EXP'	=> 'Cette fonctionnalité permettra de cacher les clés de vos utilisateurs des regards indiscrets ainsi que de leur historique de navigation.
											<br />Elle ajoute également une sécurité supplémentaire face aux script tiers en rendant la clé difficilement récupérable.',
	'ACP_PHPBB_API_UNBAN'			=> 'Débannir les adresses IPs',
	'ACP_PHPBB_API_UNBAN_EXP'		=> 'Vous pouvez débannir (ou ne plus exclure) plusieurs adresses IPs d’un coup en utilisant la combinaison de touches appropriée avec votre clavier et votre souris.',
	'ACP_PHPBB_API_WHITELIST'		=> 'Liste blanche d’adresses IPs',
	'ACP_PHPBB_API_WHITELIST_EXP'	=> 'Liste des adresses IPs qui doivent être exclus des tentatives d’essai de clés. Pour indiquer plusieurs adresses IPs différentes, entrez chacune d’elles sur une nouvelle ligne. Vous pouvez utiliser «&nbsp;*&nbsp;» comme joker.',
	'ACP_PHPBB_API_UNBANNING'		=> 'Débannissement',
	'ACP_PHPBB_API_UPDATED_CFG'		=> 'Paramètres sauvegardés',
	'ACP_PHPBB_API_UPLOAD'			=> 'Transférer',
	'ACP_PHPBB_API_UPLOAD_HOOK'		=> 'Transférer un hook',
	'ACP_PHPBB_API_UPLOAD_HOOK_EXP'	=> 'Vous pouvez transférer ici une archive compressée d’un HOOK au format BertiX (voir le hook <em>user</em>) qui seras transférée puis décompressée dans le noyau de l’API.',

	'ACP_PHPBB_API_VALIDITY_DATE'	=> 'Temps de validité',
	'ACP_PHPBB_API_VIEW_MORE'		=> 'Voir plus',
	'ACP_PHPBB_API_WILDCARD_CHAR'	=> 'Caractère générique',
	'ACP_PHPBB_API_WILDCARD_CHAR_EXP'=> 'Ce caractère seul envoyé sur l’API sera considéré comme générique et sera donc traité comme une chaine vide.',

//Mod error
	'ACP_PHPBB_API_CRYPTO_ERROR'		=> 'L’extension mcrypt est manquante. Vous ne pouvez pas activer ce paramètre.',
	'ACP_PHPBB_API_ERR_INSTALL'			=> 'Le Mod a donc été désactivé par sécurité tant que l’installation ne sera pas complétée.',
	'ACP_PHPBB_API_ERR_NOCONST'			=> 'Constante <em>API_KEYS_TABLE</em> absente … Vérifiez le fichier «&nbsp;/includes/api/constants.php&nbsp;»',
	'ACP_PHPBB_API_INSTALL_NO_COLLUMN'	=> 'La colonne SQL «&nbsp;<strong>%1$s</strong>&nbsp;» de la table «&nbsp;<strong>%2$s</strong&nbsp;» est absente.',
	'ACP_PHPBB_API_INSTALL_NO_FILE'		=> 'Le fichier «&nbsp;<strong>%s</strong>&nbsp;» est absent.',
	'ACP_PHPBB_API_INSTALL_NO_DIRECTORY'=> 'Le dossier «&nbsp;<strong>%s</strong>&nbsp;» est absent.',
	'ACP_PHPBB_API_INSTALL_NO_TABLE'	=> 'La table SQL «&nbsp;<strong>%1$s</strong>&nbsp;» est absente.',
	'ACP_PHPBB_API_NO_JAVASCRIPT'		=> 'L’administration de ce mod requiert Javascript pour de meilleures performances, merci de l’activer !',
	'ACP_PHPBB_API_NO_PCHART'			=> 'La librairie pChart est manquante ou corrompue, la consultation des statistiques est par conséquent désactivée.
											<br /><br /><strong>Information additionnelle :</strong>
											<br />La librairie pChart étant sous licence GNU/GPL<sup>V3</sup>, vous devez la télécharger vous-même <a href="http://geolim4.com/pchart/pchart.zip">ici</a> et l’extraire dans le dossier %s.
											<br /><br /><em>Pourquoi sur Geolim4.com et non sur le site de l’auteur de pChart?</em>
											<br />Du fait d’un conflit d’encodage de caractère entre phpBB et pChart, la librairie a du être légèrement modifiée afin de fonctionner correctement.',

//Version Check
	'ACP_ERRORS'						=> 'Erreurs',

	'API_CURRENT_VERSION'				=> 'Version actuelle',
	'API_ERRORS_CONFIG_ALT'				=> 'Configuration de phpBB API',
	'API_ERRORS_CONFIG_EXPLAIN'			=> 'Sur cette page, vous pouvez vérifier si votre version de ce Mod est bien à jour ou, dans le cas contraire, les actions à effectuer pour le mettre à jour.<br />Vous pouvez également régler des points simple de configuration qui s’y rapportent.',
	'API_ERRORS_INSTRUCTIONS'			=> '<br /><h1>Utilisation de phpBB API v%1$s</h1><br />
												<p>L’équipe de Geolim4.com vous remercie de votre confiance et espère que vous apprécierez les fonctionalités de ce Mod.<br />
												N’hésitez pas à donner votre avis … Rendez-vous <strong><a href="%2$s" title="phpBB API">sur cette page</a></strong></p>
												<p>Pour toute demande de support, rendez vous dans le <strong><a href="%3$s" title="Forum de support">forum de support</a></strong>.</p>
												<p>Visitez également le Traqueur <strong><a href="%4$s" title="Traqueur du Mod phpBB API">sur cette page</a></strong>. Tenez vous informé des éventuels bugs, ajouts ou demandes de fonctionnalités, la sécurité …</p>',
	'API_ERRORS_NO_VERSION'				=> '<span style="color: red">La dernière version n’a pas pu être trouvée …</span>',
	'API_ERRORS_UPDATE_INSTRUCTIONS'	=> '
		<h1>Annonce de sortie</h1>
		<p>Veuillez lire <a href="%1$s" title="%1$s"><strong>l’annonce de sortie de la dernière version</strong></a> pour accéder au processus de mise à jour, il peut contenir des informations utiles. Il incluera également le lien de téléchargement ainsi que le journal des modifications.</p>
		<br />
		<h1>Comment mettre à jour votre installation de phpBB API</h1>
		<p>► Téléchargez la dernière version.</p>
		<p>► Décompressez l’archive et ouvrez le fichier install.xml, il contient toutes les informations de mise à jour.</p>
		<p>► Annonce officielle de la dernière version : (%2$s)</p>',

	'API_ERRORS_VERSION_CHECK'			=> 'Vérificateur de version de phpBB API',
	'API_ERRORS_VERSION_CHECK_EXPLAIN'	=> 'Vérifie si la version de phpBB API que vous utilisez est à jour.',
	'API_ERRORS_VERSION_COPY'			=> '<a href="%1$s" title="Mod phpBB API">Mod phpBB API v%2$s</a> &copy; 2011 - ' . date('Y') . ' <a href="http://geolim4.com" title="geolim4.com"><em>Geolim4.com</em></a>',
	'API_ERRORS_VERSION_NOT_UP_TO_DATE'	=> 'Votre version de phpBB API n’est pas à jour.<br />Ci-dessous vous trouverez un lien vers l’annonce de sortie de la dernière version ainsi que des instructions sur la façon d’effectuer la mise à jour.',
	'API_ERRORS_VERSION_UP_TO_DATE'		=> 'Votre installation est à jour.',

	'API_LATEST_VERSION'				=> 'Dernière version',
	'API_NEW_VERSION'					=> 'Votre version de phpBB API n’est pas à jour. Votre version est la %1$s, la dernière version est la %2$s. Lisez la suite pour plus d’informations.',

	'API_UNABLE_CONNECT'				=> 'Impossible de récupérer la version du mod depuis le serveur, message d’erreur : %s',
	'API_UNABLE_CONNECT_HOOK'			=> 'Impossible de récupérer les versions des hooks depuis le serveur, message d’erreur : %s',
));

//phpBB complement
$lang = array_merge($lang, array(
	'LIFETIME'		=> 'A vie'
));
?>