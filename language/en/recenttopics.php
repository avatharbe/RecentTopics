<?php
/**
 *
 * @package Recent Topics Extension
 * @copyright (c) 2015 PayBas
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Based on the original NV Recent Topics by Joas Schilling (nickvergessen)
 * English translation by PayBas
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
	'RECENT_TOPICS'     => 'Recent Topics',
	'RT_NO_TOPICS'		=> 'There are no new topics to display.',
	'LIKES'				=> 'Likes',
	'VIEWING_RECENT_TOPICS'	=> 'Viewing Recent Topics',
	'EXTENSION_REQUIRES_330'	=> 'This extension requires phpBB 3.3.0 or higher.',

	// is_enableable() error messages
	'RECENTTOPICS_PHP_VERSION_FAIL'		=> 'This extension requires PHP %1$s or higher. You are running PHP %2$s.',
	'RECENTTOPICS_PHPBB_VERSION_FAIL'	=> 'This extension requires phpBB %1$s or higher. You are running phpBB %2$s.',
	)
);
