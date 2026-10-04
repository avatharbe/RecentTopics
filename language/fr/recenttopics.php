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
	'RECENT_TOPICS'     => 'Sujets récents',
	'RT_NO_TOPICS'		=> 'Il n’y a aucun nouveau sujet à afficher.',
	'LIKES'				=> 'J’aime',
	'VIEWING_RECENT_TOPICS'	=> 'Consulte les Sujets récents',
	'EXTENSION_REQUIRES_330'	=> 'Cette extension nécessite phpBB 3.3.0 ou supérieur.',

	// is_enableable() error messages
	'RECENTTOPICS_PHP_VERSION_FAIL'		=> 'Cette extension nécessite PHP %1$s ou supérieur. Vous utilisez PHP %2$s.',
	'RECENTTOPICS_PHPBB_VERSION_FAIL'	=> 'Cette extension nécessite phpBB %1$s ou supérieur. Vous utilisez phpBB %2$s.',
	)
);
