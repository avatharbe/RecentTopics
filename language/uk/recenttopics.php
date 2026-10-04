<?php
/**
 *
 * @package Recent Topics Extension
 * @copyright (c) 2015 PayBas
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Based on the original NV Recent Topics by Joas Schilling (nickvergessen)
 * Ukrainian translation
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
	'RECENT_TOPICS'     => 'Останні Теми',
	'RT_NO_TOPICS'		=> 'Немає нових тем.',
	'LIKES'				=> 'Вподобання',
	'VIEWING_RECENT_TOPICS'	=> 'Переглядає Останні Теми',
	'EXTENSION_REQUIRES_330'	=> 'Це розширення потребує phpBB 3.3.0 або вище.',

	// is_enableable() error messages
	'RECENTTOPICS_PHP_VERSION_FAIL'		=> 'Це розширення вимагає PHP %1$s або вище. У вас встановлено PHP %2$s.',
	'RECENTTOPICS_PHPBB_VERSION_FAIL'	=> 'Це розширення вимагає phpBB %1$s або вище. У вас встановлено phpBB %2$s.',
	)
);
