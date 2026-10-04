<?php
/**
 *
 * @package Recent Topics Extension
 * @copyright (c) 2015 PayBas
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Based on the original NV Recent Topics by Joas Schilling (nickvergessen)
 * French translation by ForumsFaciles (www.forumsfaciles.fr) & Galixte (www.galixte.com) & Sajaki (www.avathar.be)
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
//
// Some characters you may want to copy&paste:
// ’ « » “ ” …
//

$lang = array_merge($lang, array(
	//forum acp
	'RECENT_TOPICS_LIST'            => 'Afficher les sujets récents',
	'RECENT_TOPICS_LIST_EXPLAIN'    => 'Si activé, les sujets de ce forum seront affichés dans la liste des sujets récents.',

	//acp title
	'RECENT_TOPICS'                 => 'Sujets récents',
	'RT_CONFIG'                     => 'Configuration',
	'RECENT_TOPICS_EXPLAIN'         => 'Sur cette page il est possible de modifier les paramètres spécifiques de l’extension « Sujets récents ».<br /><br />Les forums peuvent être inclus ou exclus de la liste des sujets récents en modifiant leurs paramètres respectifs depuis le « Panneau d’administration », onglet « FORUMS ».<br />Il est recommandé de vérifier les autorisations des utilisateurs leur permettant de modifier par eux-mêmes certains paramètres présents ci-dessous.',

	//global settings
	'RT_GLOBAL_SETTINGS'            => 'Paramètres généraux',
	'RT_DISPLAY_INDEX'              => 'Permet d’afficher la liste des sujets récents sur la page de l’index du forum.',
	'RT_DISPLAY_VIEWFORUM'          => 'Afficher sur la page du forum',
	'RT_VIEWFORUM_LOCATION'         => 'Emplacement sur la page du forum',
	'RT_VIEWFORUM_LOCATION_EXP'     => 'Sélectionnez où afficher les sujets récents sur la page du forum. Ce paramètre est indépendant de la page d’index.',
	'RT_NUMBER'                     => 'Nombre de sujets récents affichés',
	'RT_NUMBER_EXP'                 => 'Permet de saisir le nombre maximum de sujets récents à afficher par page.',
	'RT_PAGE_NUMBER'                => 'Afficher toutes les pages des sujets récents',
	'RT_PAGE_NUMBER_EXP'            => 'Permet de passer outre le « Nombre maximal de pages » à afficher dans la pagination. Si activé, tous les sujets du forum seront paginés et autant de pages que nécessaire seront affichées (non recommandé).<br />Si désactivé (décochée), merci de saisir le nombre de page à afficher dans l’option « Nombre maximal de pages ».',
	'RT_PAGE_NUMBERMAX'             => 'Nombre maximal de pages',
	'RT_PAGE_NUMBERMAX_EXP'         => 'Permet de saisir le nombre maximal de pages à afficher dans la pagination des sujets récents lorsque l’option « Afficher toutes les pages des sujets récents » est désactivée (décochée).',
	'RT_MIN_TOPIC_LEVEL'            => 'Niveau minimum du type de sujets affichés',
	'RT_MIN_TOPIC_LEVEL_EXP'        => 'Permet de sélectionner le niveau minimum du type de sujets à afficher dans la liste des sujets récents. Les sujets correspondants au niveau paramétré et aux niveaux supérieurs seront affichés.',
	'RT_ANNOUNCEMENTS_FIRST'        => 'Afficher les annonces en premier',
	'RT_ANNOUNCEMENTS_FIRST_EXP'    => 'Place les annonces et annonces globales de chaque page de la liste en haut de cette page. Les annonces plus anciennes qui ne figurent pas sur la page ne sont pas ajoutées.',
	'RT_ANTI_TOPICS'                => 'Exclure des sujets de l’affichage',
	'RT_ANTI_TOPICS_EXP'            => 'Permet de saisir les ID, séparés par une virgule (exemple : 7,9), des sujets à exclure de l’affichage des sujets récents.<br />Pour afficher tous les sujets saisir la valeur 0.',
	'RT_PARENTS'                    => 'Afficher les forums parents',
	'RT_PARENTS_EXP'                => 'Permet d’afficher les forums parents dans l’arborescence des forums de l’affichage des sujets récents.',
	'RT_TOPIC_LINK_TO'              => 'Le titre du sujet renvoie vers',
	'RT_TOPIC_LINK_TO_EXP'          => 'Permet de choisir vers quel message le titre du sujet renvoie dans la liste des sujets récents.',
	'RT_TOPIC_LINK_FIRST'           => 'Premier message',
	'RT_TOPIC_LINK_LAST'            => 'Dernier message',
	'RT_TOPIC_LINK_UNREAD'          => 'Premier message non lu',
	'RT_SIDE_SHOW_DATE'             => 'Afficher la date en vue latérale',
	'RT_SIDE_SHOW_DATE_EXP'         => 'Affiche la date du message en vue latérale. Désactiver pour une barre latérale plus compacte.',
	'RT_SHOW_LIKES'                 => 'Afficher le nombre de j\'aime',
	'RT_SHOW_LIKES_EXP'             => 'Affiche le nombre de j\'aime PostLove à côté des sujets récents.',

	//User Overridable settings. these apply for anon users and can be overridden by UCP
	'RT_OVERRIDABLE'                => 'Paramètres personnalisables depuis le « Panneau de l’utilisateur »',
	'RT_LOCATION'                   => 'Emplacement des sujets récents',
	'RT_LOCATION_EXP'               => 'Permet de sélectionner l’emplacement où afficher la liste des sujets récents (option applicable uniquement aux styles basés sur « prosilver »).',
	'RT_TOP'                        => 'Au-dessus de la liste des forums',
	'RT_BOTTOM'                     => 'En dessous de la liste des forums',
	'RT_SIDE'                       => 'Sur le côté droit de la liste des forums',
	'RT_SORT_START_TIME'            => 'Trier selon les nouveaux sujets créés',
	'RT_SORT_START_TIME_EXP'        => 'Permet d’afficher les sujets récents triés selon la date de création du sujet en lieu et place de la date du dernier message.',
	'RT_UNREAD_ONLY'                => 'Afficher uniquement les sujets non lus',
	'RT_UNREAD_ONLY_EXP'            => 'Permet d’afficher uniquement les sujets non lus qu’ils soient récents ou non. Cette fonctionnalité utilise les mêmes paramètres qu’en temps normal (excluant les forums, sujets, etc.). Note : Cette option est uniquement dédiée aux utilisateurs connectés; les invités verront la liste « normale ».',
	'RT_RESET_DEFAULT'              => 'Réinitialiser les paramètres utilisateur',
	'RT_RESET_DEFAULT_EXP'          => 'Permet de réinitialiser les « Paramètres personnalisables » par défaut (ceux présents sur cette page) à tous les utilisateurs.',


	//Version checker
	'RT_VERSION_CHECK'				=> 'Vérification de la version',
	'RT_LATEST_VERSION'				=> 'Dernière version',
	'RT_EXT_VERSION'				=> 'Version de l’extension',
	'RT_CHECK_UPDATE'				=> 'Vérifier manuellement depuis le site Web : <a href="https://www.avathar.be/forum/app.php/dlext/details?df_id=35">avathar.be</a> si une nouvelle version est disponible.',

	//Standalone pages
	'RT_PAGES'                      => 'Pages autonomes',
	'RT_PAGE_ENABLE'                => 'Activer les pages autonomes',
	'RT_PAGE_ENABLE_EXP'            => 'Autoriser l’accès aux pages autonomes des sujets récents. Ceci est indépendant de l’affichage sur la page d’index.',
	'RT_PAGE'                       => 'Page complète',
	'RT_PAGE_EXP'                   => 'Page autonome des sujets récents avec l’en-tête et le pied de page complets du forum.',
	'RT_SIMPLE_PAGE'                => 'Page simplifiée',
	'RT_SIMPLE_PAGE_EXP'            => 'Page simplifiée des sujets récents sans en-tête ni pied de page du forum, adaptée à l’intégration dans une iframe.',
	'RT_VIEW_PAGE'                  => 'Voir la page dans un nouvel onglet',

	//Bloc publicitaire
	'RT_ADS_SETTINGS'           => 'Bloc publicitaire',
	'RT_ADS_ENABLE'             => 'Activer le bloc publicitaire',
	'RT_ADS_ENABLE_EXP'         => 'Affiche un bloc HTML personnalisé à côté des sujets récents sur la page d\'index. Visible uniquement lorsque la position est réglée sur "Côté".',
	'RT_ADS_CODE'               => 'HTML publicitaire',
	'RT_ADS_CODE_EXP'           => 'Entrez du HTML personnalisé à afficher dans le bloc publicitaire (par exemple, code publicitaire, bouton de don ou autre contenu).',

	//Donation
	'PATREON_ALT'                => 'Devenir un patron',
	'RT_DONATE'					=> 'Soutenir le développement',
	'RT_DONATE_SHORT'			=> 'Faire un don PayPal',
	'RT_DONATE_EXPLAIN'			=> 'Permet de soutenir le développement de l’extension « Recent Topics » qui est distribuée librement. L’auteur consacre temps et argent à son développement, sur son temps libre, et parce qu’il y trouve un certain plaisir. Si cette extension est appréciée il est recommandé, de faire un don pour soutenir ce projet, et comme le dit l’auteur : « J’apprécierai grandement votre geste, d’avance mes remerciements ! ».',
	)
);
