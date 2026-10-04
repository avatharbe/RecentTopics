<?php
/**
 *
 * @package Recent Topics Extension
 * Swedish translation
 *
 * @copyright (c) 2015 PayBas
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Based on the original NV Recent Topics by Joas Schilling (nickvergessen)
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
	'RECENT_TOPICS'     => 'Senaste trådar',
	'RT_NO_TOPICS'		=> 'Det finns inga nya trådar att visa.',
	'LIKES'				=> 'Gillningar',
	'VIEWING_RECENT_TOPICS'	=> 'Visar Senaste trådar',
	'EXTENSION_REQUIRES_330'	=> 'Detta tillägg kräver phpBB 3.3.0 eller högre.',

	// is_enableable() error messages
	'RECENTTOPICS_PHP_VERSION_FAIL'		=> 'Det här tillägget kräver PHP %1$s eller högre. Du kör PHP %2$s.',
	'RECENTTOPICS_PHPBB_VERSION_FAIL'	=> 'Det här tillägget kräver phpBB %1$s eller högre. Du kör phpBB %2$s.',
	)
);
