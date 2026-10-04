<?php
/**
 *
 * @package Recent Topics Extension
 * @copyright (c) 2015 PayBas
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Based on the original NV Recent Topics by Joas Schilling (nickvergessen)
 * Arabic translation by Bassel Taha Alhitary (www.alhitary.net)
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
	'RECENT_TOPICS'    => 'أحدث المواضيع',
	'RT_NO_TOPICS'		=> 'لا توجد مواضيع جديدة لعرضها.',
	'LIKES'				=> 'إعجابات',
	'VIEWING_RECENT_TOPICS'	=> 'يتصفح أحدث المواضيع',
	'EXTENSION_REQUIRES_330'	=> 'هذا الامتداد يتطلب phpBB 3.3.0 أو أعلى.',

	// is_enableable() error messages
	'RECENTTOPICS_PHP_VERSION_FAIL'		=> 'يتطلب هذا الإمتداد PHP %1$s أو أعلى. أنت تستخدم PHP %2$s.',
	'RECENTTOPICS_PHPBB_VERSION_FAIL'	=> 'يتطلب هذا الإمتداد phpBB %1$s أو أعلى. أنت تستخدم phpBB %2$s.',
	)
);
