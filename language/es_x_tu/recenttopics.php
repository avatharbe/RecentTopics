<?php
/**
 *
 * @package Recent Topics Extension
 * @copyright (c) 2015 PayBas
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Based on the original NV Recent Topics by Joas Schilling (nickvergessen)
 * Spanish (Tu) translation by Raul [ThE KuKa] (www.phpbb-es.com)
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
	'RECENT_TOPICS'    => 'Temas Recientes',
	'RT_NO_TOPICS'		=> 'No hay nuevos temas que mostrar.',
	'LIKES'				=> 'Me gusta',
	'VIEWING_RECENT_TOPICS'	=> 'Viendo Temas Recientes',
	'EXTENSION_REQUIRES_330'	=> 'Esta extensión requiere phpBB 3.3.0 o superior.',

	// is_enableable() error messages
	'RECENTTOPICS_PHP_VERSION_FAIL'		=> 'Esta extensión requiere PHP %1$s o superior. Estás ejecutando PHP %2$s.',
	'RECENTTOPICS_PHPBB_VERSION_FAIL'	=> 'Esta extensión requiere phpBB %1$s o superior. Estás ejecutando phpBB %2$s.',
	)
);
