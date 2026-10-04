<?php
/**
 *
 * @package Recent Topics Extension
 * @copyright (c) 2015 PayBas
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Based on the original NV Recent Topics by Joas Schilling (nickvergessen)
 * Dutch translation by PayBas, Sajaki
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
	'RECENT_TOPICS'   => 'Recente Onderwerpen',
	'RT_NO_TOPICS'	  => 'Er zijn geen recente onderwerpen weer te geven.',
	'LIKES'				=> 'Likes',
	'VIEWING_RECENT_TOPICS'	=> 'Bekijkt Recente Onderwerpen',
	'EXTENSION_REQUIRES_330'	=> 'Deze extensie vereist phpBB 3.3.0 of hoger.',

	// is_enableable() error messages
	'RECENTTOPICS_PHP_VERSION_FAIL'		=> 'Deze extensie vereist PHP %1$s of hoger. Je gebruikt PHP %2$s.',
	'RECENTTOPICS_PHPBB_VERSION_FAIL'	=> 'Deze extensie vereist phpBB %1$s of hoger. Je gebruikt phpBB %2$s.',
	)
);
