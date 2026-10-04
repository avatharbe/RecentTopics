<?php
/**
 *
 * @package Recent Topics Extension
 * @copyright (c) 2015 PayBas
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Based on the original NV Recent Topics by Joas Schilling (nickvergessen)
 * Portuguese translation by phpbbpt
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
	'RECENT_TOPICS'     => 'Tópicos Recentes',
	'RT_NO_TOPICS'		=> 'Não há novos tópicos a serem exibidos.',
	'LIKES'				=> 'Gostos',
	'VIEWING_RECENT_TOPICS'	=> 'Visualizando Tópicos Recentes',
	'EXTENSION_REQUIRES_330'	=> 'Esta extensão requer o phpBB 3.3.0 ou superior.',

	// is_enableable() error messages
	'RECENTTOPICS_PHP_VERSION_FAIL'		=> 'Esta extensão requer PHP %1$s ou superior. Está a executar PHP %2$s.',
	'RECENTTOPICS_PHPBB_VERSION_FAIL'	=> 'Esta extensão requer phpBB %1$s ou superior. Está a executar phpBB %2$s.',
	)
);
