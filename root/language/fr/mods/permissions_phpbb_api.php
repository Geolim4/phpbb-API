<?php
/**
*
* @package language [English] phpBB API (phpBB Permission Set)
^>@version $Id: permissions_phpbb_api.php v0.0.1 18h35 03/08/2014 Zoddo Exp $
* @copyright (c) 2012 Geolim4.com  http://Geolim4.com
* @bug/function request: http://geolim4.com/tracker.php
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/

/**
* DO NOT CHANGE
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

/**
*	MODDERS PLEASE NOTE
*	
*	You are able to put your permission sets into a separate file too by
*	prefixing the new file with permissions_ and putting it into the acp
*	language folder.
*
*	An example of how the file could look like:
*
*	<code>
*
*	if (empty($lang) || !is_array($lang))
*	{
*		$lang = array();
*	}
*
*	// Adding new category
*	$lang['permission_cat']['bugs'] = 'Bugs';
*
*	// Adding new permission set
*	$lang['permission_type']['bug_'] = 'Bug Permissions';
*
*	// Adding the permissions
*	$lang = array_merge($lang, array(
*		'acl_bug_view'		=> array('lang' => 'Can view bug reports', 'cat' => 'bugs'),
*		'acl_bug_post'		=> array('lang' => 'Can post bugs', 'cat' => 'post'), // Using a phpBB category here
*	));
*
*	</code>
*/

$lang['permission_cat']['api'] = 'API';
$lang = array_merge($lang, array(
	//ACP acl
	'acl_a_phpbb_api_config'		=> array('lang' => 'Peut gérer la configuration de l’API', 'cat' => 'api'),
	'acl_a_phpbb_api_hooks'			=> array('lang' => 'Peut gérer les hooks de l’API, y compris l’installation, la désinstallation et la suppression des hooks.<br /><em>Les hooks permettent l’execution de code arbitraire, donc vous devriez accorder cette permission uniquement aux utilisateurs de confiance !</em>', 'cat' => 'api'),	
	'acl_a_phpbb_api_keys'			=> array('lang' => 'Peut gérer les clés de l’API, y compris les clés d’administrateur', 'cat' => 'api'),	
	'acl_a_phpbb_api_logs'			=> array('lang' => 'Peut gérer les journaux de l’API', 'cat' => 'api'),
	'acl_a_phpbb_api_stats'			=> array('lang' => 'Peut gérer les statistiques de l’API', 'cat' => 'api'),
	//UCP acl
	'acl_u_phpbb_api_history'		=> array('lang' => 'Peut voir l’historique d’utilisation d’une clé', 'cat' => 'api'),
	'acl_u_phpbb_api_ignore_day'	=> array('lang' => 'Peut ignorer les limites de requêtes journalières', 'cat' => 'api'),
	'acl_u_phpbb_api_ignore_max'	=> array('lang' => 'Peut ignorer les limites de requêtes maximum', 'cat' => 'api'),	
	'acl_u_phpbb_api_ignore_month'	=> array('lang' => 'Peut ignorer les limites de requêtes mensuelles', 'cat' => 'api'),
	'acl_u_phpbb_api_ignore_week'	=> array('lang' => 'Peut ignorer les limites de requêtes hebdomadaire', 'cat' => 'api'),
	'acl_u_phpbb_api_ips'			=> array('lang' => 'Peut modifier la liste des adresses IPs autorisées/interdites à utiliser la clé', 'cat' => 'api'),	// à mettre en concordance avec la fr
	'acl_u_phpbb_api_regenerate'	=> array('lang' => 'Peut générer une nouvelle clé quand la clé actuelle est expirée ou a atteind le quota maximum de requête.', 'cat' => 'api'),
	'acl_u_phpbb_api_stats'			=> array('lang' => 'Peut voir les statistiques de la clé', 'cat' => 'api'),
	'acl_u_phpbb_api_use'			=> array('lang' => 'Peut utiliser l’API', 'cat' => 'api'),	
	
));
?>