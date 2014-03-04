<?php
/**
*
* @package language [Standard french] phpBB API
^>@version $Id: phpbb_api.php v0.0.1 05h12 01/17/2013 Geolim4 Exp $
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
$lang = array_merge($lang, array(
	'API_CACHED_PURGED'			=> 'Le cache a été purgé',
	'API_CRYPTO_PRIVATE'		=> 'Privée. Allez dans votre panneau d’utilisateur pour l’obtenir.',
	'API_DEACTIVATED'			=> 'Cette clé a été désactivée, veuillez contacter un administrateur pour plus d’informations.',
	'API_GENERATE_TIME'			=> 'Page générée en %s secondes.',
	'API_LIFETIME'				=> 'A vie',
	'API_ITEM_KEYWORD'			=> 'objet',//Should uppercase without special chars unless underscore: _
	'API_LOGIN_WAIT'			=> 'Connexion, veuillez patienter …',
	'API_NO_RECORD'				=> 'Aucun enregistrement trouvé',
	'API_INCORRECT_DATA'		=> 'Données incorrectes envoyées',
	'API_SUCCESS'				=> 'Opération terminée avec succès',
	'API_SUCCESS_CRON'			=> 'Tâche cron exécutée avec succès, un email a été envoyé à tous les fondateurs.',
	'API_SUCCESS_QUERY'			=> 'Requête executée avec succès : %s',
	'API_SUSPENDED'				=> 'Cette clé a été suspendue, veuillez contacter un administrateur pour plus d’informations.',
	'API_STATUS_DISABLE'		=> 'Desactiver',//No special chars(é,à,è)
	'API_STATUS_ENABLE'			=> 'Activer',//No special chars(é,à,è)

	//Here we allow to translate some method we pass through the URL
	//You cannot use é è à ï etc.
	//To translators: You can use only uppercase chars without special chars unless underscore: _
	'API_TRANSLATED_METHOD'		=> array(
		'login'				=> 'connexion',
		'post'				=> 'message',
		'topic'				=> 'sujet',
		'forum'				=> 'forum',
		'group'				=> 'groupe',
		'perm_ban'			=> 'ban_perm',
		'unban'				=> 'debannir',
		'get_bans'			=> 'recuperer_bans',
		'key_options'		=> 'options_cle',
		'key_stats'			=> 'statistiques_cle',
		'sql_query'			=> 'requete_sql',
		'get_constants'		=> 'recuperer_constantes',
		'refresh_stats'		=> 'rafraichir_statistiques',
		'get_config'		=> 'recuperer_config',
		'set_config'		=> 'changement_config',
		'board_status'		=> 'forum_statut',
		'php_configuration'	=> 'configuration_php',
		'search_ip'			=> 'recherche_ip',
	),
	//Short methods description
	'API_FULL_TRANSLATED_METHOD'		=> array(
		'login'				=> 'Connexion au compte',
		'post'				=> 'Récupération des données d’un message',
		'topic'				=> 'Récupération des données d’un sujet',
		'forum'				=> 'Récupération des données d’un forum',
		'group'				=> 'Récupération des données d’un groupe',
		'perm_ban'			=> 'Bannissement permanent',
		'unban'				=> 'Débannissement',
		'get_bans'			=> 'Récupération des bannissements',
		'key_options'		=> 'Récupération des options de la clé',
		'key_stats'			=> 'Récupération des statistiques de la clé',
		'sql_query'			=> 'Exécution d’une requête SQL',
		'get_constants'		=> 'Récupération des constantes locales',
		'refresh_stats'		=> 'Rafraîchissement des statistiques',
		'get_config'		=> 'Récupération de la configuration',
		'set_config'		=> 'Changement de la configuration',
		'board_status'		=> 'Changement du statut du forum',
		'php_configuration'	=> 'Récupération de la configuration de PHP',
		'search_ip'			=> 'Rechercher une IP',
	),

	//Full submethods translation
	'API_FULL_TRANSLATED_SUBMETHOD'		=> array(
		//User table translation
		'user_id'			=> 'Identifiant utilisateur',
		'username'			=> 'Nom d’utilisateur',
		'username_clean'	=> 'Nom d’utilisateur épuré',
		'user_email'		=> 'E-mail de l’utilisateur',
		'user_type'			=> 'Type d’utilisateur',
		'user_regdate'		=> 'Date d’enregistrement (Temps UNIX)',
		'user_ip'			=> 'IP d’enregistrement',
		'user_passchg'		=> 'Dernier changement du mot de passe (Temps UNIX)',
		//Topic table translation
		'topic_id'			=> 'Identifiant du sujet',
		'topic_title'		=> 'Titre du sujet',
		'icon_id'			=> 'Identifiant de l’icône',//Also used in following table: post_id
		'topic_type'		=> 'Type du sujet',
		'topic_views'		=> 'Vues du sujet',
		'topic_poster'		=> 'Créateur du sujet',
		//Group table translation
		'group_id'			=> 'Identifiant du groupe',
		'group_name'		=> 'Nom du groupe',
		'group_desc'		=> 'Description du groupe',
		'group_colour'		=> 'Couleur du groupe',
		//Post table translation
		'post_id'			=> 'Identifiant du message',
		'poster_ip'			=> 'IP du posteur',
		'post_time'			=> 'Date de publication',
		//Forum table translation
		'forum_id'			=> 'Identifiant du forum',
		//Misc
		'json'				=> 'Chaîne JSON',
		'serialize'			=> 'Chaîne linéarisé (PHP)',
		'ban_id'			=> 'Identifiant du ban',
		'userid'			=> 'Identifiant utilisateur',
		'email'				=> 'E-mail',
	),

	'API_UNAUTHORIZED'			=> 'Clé non autorisée !',
	'API_UNAUTHORIZED_USER'		=> 'Le compte liée à cette clé a été supprimée.',
	'API_UNAUTHORIZED_AUTH'		=> 'Vous n’avez pas les permissions requises',
	'API_UNAUTHORIZED_FN'		=> 'Fonctionnalité désactivée sur cette clé !',
	'API_UNAUTHORIZED_PVG'		=> 'Privilège refusé : %s',
	'API_UNAUTHORIZED_SQL_API'	=> 'Impossible d’opérer sur les tables des logs et des clés suite à une restriction de sécurité de la clé.',

	//API errors...
	'API_BAD_EMAIL'					=> 'Cette clé est sécurisée avec l’adresse e-mail de son propriétaire. Veuillez vous référer au manuel de l’API pour vous authentifier avec l’adresse e-mail.',
	'API_BAD_IP'					=> 'Cette clé ne peut pas être utilisée avec cette adresse IP (votre IP actuelle : %s)',
	'API_BAN_REASON'				=> 'Vous avez été banni définitivement de ce forum via l’API.',

	'API_ERROR_ATTEMPTS'			=> 'Vous avez dépasser le nombre maximum de connection autorisée avec cette IP ce qui résulte d’un bannissement temporaire de l’API.',
	'API_ERROR_CRYPTO_DISABLED'		=> 'Le chiffrement a été désactivé par l’administrateur.',
	'API_ERROR_DEACTIVATED_METHOD'	=> 'Cette méthode a été désactivée sur cette clé, veuillez contacter un Administrateur pour plus d’informations.',
	'API_ERROR_EXCEEDED'			=> 'Vous avez dépassé votre quota maximum de requêtes sur cette clé (%s)',
	'API_ERROR_EXPIRED'				=> 'Cette clé est périmée depuis le %s',
	'API_ERROR_HOOK_OUTDATED'		=> 'Ce hook n’est plus a jour. La dernière version de l’API est : %1$s, la version de ce hook est : %2$s',
	'API_ERROR_HOOK_NOCONST'		=> 'Constante de mise à jour manquante : %s',
	'API_ERROR_INTERNAL'			=> 'Message : %1$s Fichier : %2$s Ligne : %3$s ', // That key is used to fill the physical log
	'API_ERROR_METHOD'				=> 'Erreur critique : Méthode « %s » non trouvée ! Le nom de la méthode peut varier selon le language défini dans le compte rattaché à votre clé.',
	'API_ERROR_METHOD_DISPLAY'		=> 'Format de sortie « %s » non trouvé !',
	'API_ERROR_METHOD_REQUEST'		=> 'Impossible de procéder à la requête en mode %s',
	'API_ERROR_DISABLED'			=> 'L’API a été désactivé par un administrateur.',
	'API_ERROR_NO_SSL'				=> 'La requête doit être ré-adressé en utilisant le protocole SSL',
	'API_ERROR_NO_METHOD'			=> 'Aucune méthode sélectionnée !',
	'API_ERROR_NO_SUBMETHOD'		=> 'Aucune sous-méthode sélectionnée !',
	'API_ERROR_PER_DAY'				=> 'Vous avez dépassé votre quota maximum de requêtes par jour (%s)',
	'API_ERROR_PER_MONTH'			=> 'Vous avez dépassé votre quota maximum de requêtes par mois (%s)',
	'API_ERROR_PER_WEEK'			=> 'Vous avez dépassé votre quota maximum de requêtes par semaine (%s)',
	'API_ERROR_PHP_VERSION'			=> 'L’API requiert PHP %1$s ou ultérieur, votre version de PHP est la %2$s',
	'API_ERROR_TYPE'				=> 'Colonne SQL « %s » inconnue dans la table « %s »',

	'API_FATAL_ERROR'				=> 'Erreur fatale',
	'API_FATAL_ERROR_DATE'			=> 'Date',
	'API_FATAL_ERROR_FILE'			=> 'Fichier',
	'API_FATAL_ERROR_INTERNAL'		=> '[ %1$s ] Fichier: %2$s, Ligne : %3$s : Message : %4$s',
	'API_FATAL_ERROR_LINE'			=> 'Ligne',
	'API_FATAL_ERROR_MSG'			=> 'Message',
	'API_FATAL_ERROR_PHP_VERSION'	=> 'Version de PHP',
	'API_FATAL_ERROR_REQUEST'		=> 'Mode de requête',
	'API_FATAL_ERROR_SERVER'		=> 'Serveur',
	'API_FATAL_ERROR_TEXT_EXP'		=> 'Cette page est une erreur fatale récupérée par phpBB API v%s.
					<br />Habituellement cette page ne devrait pas apparaître, vous devriez trouver et corriger cette erreur rapidement.
					<br />Si vous n’y arrivez pas, contactez le développeur de l’API <a href="http://geolim4.com/tracker.php" title="Traqueur phpBB API">ici</a>.',
	'API_FATAL_ERROR_TITLE_EXP'		=> 'Explications',
	'API_FATAL_ERROR_TYPE'			=> 'Type',
	'API_STATS_DAY_FMT'				=> 'l j  F Y',//Jeudi 7 Avril 2013
));

?>