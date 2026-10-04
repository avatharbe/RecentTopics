<?php
/**
 *
 * @package Recent Topics Extension
 * @copyright (c) 2015 PayBas
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Based on the original NV Recent Topics by Joas Schilling (nickvergessen)
 * Slovak translation, originally by Dark77
 */

if (!defined('IN_PHPBB'))
{
	exit;
}
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge(
	$lang, array(
	'RECENT_TOPICS'     => 'Najnovšie témy',
	'RT_NO_TOPICS'		=> 'Nie sú žiadne nové témy na zobrazenie.',
	'LIKES'				=> 'Lajky',
	'VIEWING_RECENT_TOPICS'	=> 'Prezerá Najnovšie témy',
	'EXTENSION_REQUIRES_330'	=> 'Toto rozšírenie vyžaduje phpBB 3.3.0 alebo vyššie.',

	// is_enableable() error messages
	'RECENTTOPICS_PHP_VERSION_FAIL'		=> 'Toto rozšírenie vyžaduje PHP %1$s alebo novší. Používate PHP %2$s.',
	'RECENTTOPICS_PHPBB_VERSION_FAIL'	=> 'Toto rozšírenie vyžaduje phpBB %1$s alebo novší. Používate phpBB %2$s.',
	)
);
