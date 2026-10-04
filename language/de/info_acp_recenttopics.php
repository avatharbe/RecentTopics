<?php
/**
 *
 * @package Recent Topics Extension
 * @copyright (c) 2015 PayBas
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Based on the original NV Recent Topics by Joas Schilling (nickvergessen)
 * German translation by Andreas Vandenberghe
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
// ‚ ‘ ’ « » „ “ ” …
//

$lang = array_merge(
	$lang, array(
	//forum acp
	'RECENT_TOPICS_LIST'            => 'In „Aktuelle Themen“ anzeigen',
	'RECENT_TOPICS_LIST_EXPLAIN'    => 'Aktiviere dieses Kontrollkästchen, um Themen in diesem Forum in der Erweiterung „Aktuelle Themen“ anzuzeigen.',

	//acp title
	'RECENT_TOPICS'                 => 'Aktuelle Themen',
	'RT_CONFIG'                     => 'Einstellungen',
	'RECENT_TOPICS_EXPLAIN'         => 'Auf dieser Seite kannst du die Einstellungen der Erweiterung „Aktuelle Themen“ anpassen.<br /><br />Spezifische Foren können eingeschlossen oder ausgeschlossen werden.<br />Überprüfe auch die Benutzerberechtigungen, welche Benutzern erlauben, einige der Parameter für sich selbst zu verändern. Diese haben dann Vorrang vor den Einstellungen des Admin-Panels.',

	//allgemeine Einstellungen
	'RT_GLOBAL_SETTINGS'            => 'Globale Einstellungen',
	'RT_DISPLAY_INDEX'              => 'Anzeigen auf der Index-Seite',
	'RT_DISPLAY_VIEWFORUM'          => 'Anzeigen auf der Forenansicht-Seite',
	'RT_VIEWFORUM_LOCATION'         => 'Anzeigeort Forenansicht',
	'RT_VIEWFORUM_LOCATION_EXP'     => 'Wähle, wo die aktuellen Themen auf der Forenansicht-Seite angezeigt werden sollen. Diese Einstellung ist unabhängig von der Indexseite.',
	'RT_NUMBER'                     => 'Anzahl Aktuelle Themen',
	'RT_NUMBER_EXP'                 => 'Maximale Anzahl anzuzeigender Themen pro Seite',
	'RT_PAGE_NUMBER'                => 'Alle Seiten anzeigen',
	'RT_PAGE_NUMBER_EXP'            => 'Diese Funktion überschreibt die eingestellte maximale Seitenanzahl und zeigt alle Seiten an, egal wie viele Seiten durch die Option eingestellt werden.',
	'RT_PAGE_NUMBERMAX'             => 'Maximale Seitenanzahl',
	'RT_PAGE_NUMBERMAX_EXP'         => 'Lege die maximale Anzahl der Seiten fest.',
	'RT_MIN_TOPIC_LEVEL'            => 'Minimaler Thementyp',
	'RT_MIN_TOPIC_LEVEL_EXP'        => 'Definiert das Minimum des anzuzeigenden Thementyps. Wenn du einen Thementyp angibst, werden nur Themen dieses oder eines höheren Typs angezeigt.',
	'RT_ANNOUNCEMENTS_FIRST'        => 'Bekanntmachungen zuerst anzeigen',
	'RT_ANNOUNCEMENTS_FIRST_EXP'    => 'Verschiebt die Bekanntmachungen und globalen Bekanntmachungen auf jeder Seite der Liste an den Anfang dieser Seite. Ältere Bekanntmachungen, die nicht auf der Seite stehen, werden nicht hinzugefügt.',
	'RT_ANTI_TOPICS'                => 'Ausgeschlossene Themen',
	'RT_ANTI_TOPICS_EXP'            => 'Gib die Themen-IDs ein, kommagetrennt (z. B. 7,9), andernfalls 0, um alle Themen anzuzeigen (wie in der URL viewtopic.php?t=12345).',
	'RT_PARENTS'                    => 'Übergeordnete Foren anzeigen',
	'RT_PARENTS_EXP'                => 'Übergeordnete Foren in der Liste der aktuellen Themen anzeigen.',
	'RT_TOPIC_LINK_TO'              => 'Thementitel verlinkt auf',
	'RT_TOPIC_LINK_TO_EXP'          => 'Wähle, auf welchen Beitrag der Thementitel in der Liste der aktuellen Themen verlinkt.',
	'RT_TOPIC_LINK_FIRST'           => 'Erster Beitrag',
	'RT_TOPIC_LINK_LAST'            => 'Letzter Beitrag',
	'RT_TOPIC_LINK_UNREAD'          => 'Erster ungelesener Beitrag',
	'RT_SIDE_SHOW_DATE'             => 'Datum in Seitenansicht anzeigen',
	'RT_SIDE_SHOW_DATE_EXP'         => 'Zeigt das Beitragsdatum in der Seitenansicht an. Deaktivieren für eine kompaktere Seitenleiste.',
	'RT_SHOW_LIKES'                 => 'Like-Anzahl anzeigen',
	'RT_SHOW_LIKES_EXP'             => 'Zeigt die PostLove Like-Anzahl neben den neuesten Themen an.',

	//Benutzereinstellungen
	'RT_OVERRIDABLE'                => 'Einstellungen, die im Benutzerkontrollzentrum geändert werden können',
	'RT_LOCATION'                   => 'Anzeigeort',
	'RT_LOCATION_EXP'               => 'Wähle den Anzeigeort der aktuellen Themen.',
	'RT_TOP'                        => 'Ansicht oben',
	'RT_BOTTOM'                     => 'Ansicht unten',
	'RT_SIDE'                       => 'Ansicht an der Seite',
	'RT_SORT_START_TIME'            => 'Nach Themen-Startzeit sortieren',
	'RT_SORT_START_TIME_EXP'        => 'Wenn diese Option aktiviert ist, werden die Themen nach dem Themenstartzeitpunkt anstelle des letzten Beitrags sortiert.',
	'RT_UNREAD_ONLY'                => 'Nur ungelesene Themen anzeigen',
	'RT_UNREAD_ONLY_EXP'            => 'Diese Option zeigt nur ungelesene Themen an (egal ob diese aktuell sind oder nicht). Diese Funktion nutzt die gleichen Einstellungen (Ausgeschlossene Foren / Themen, etc.) wie die normale Version. Hinweis: diese Funktion steht nur eingeloggten Benutzern zur Verfügung; Gäste sehen die normale „Aktuelle Themen“ Liste.',
	'RT_RESET_DEFAULT'              => 'Benutzereinstellungen zurücksetzen',
	'RT_RESET_DEFAULT_EXP'          => 'Setzt die Benutzereinstellungen zurück auf die Standardeinstellungen.',

	//Versie controle
	'RT_VERSION_CHECK'				=> 'Versionskontrolle',
	'RT_LATEST_VERSION'				=> 'Letzte Version',
	'RT_EXT_VERSION'				=> 'Extensionsversion',
	'RT_CHECK_UPDATE'				=> 'Besuche <a href="https://www.avathar.be/forum/app.php/dlext/details?df_id=35">avathar.be</a> für neuere Versionen.',

	//Eigenständige Seiten
	'RT_PAGES'                      => 'Eigenständige Seiten',
	'RT_PAGE_ENABLE'                => 'Eigenständige Seiten aktivieren',
	'RT_PAGE_ENABLE_EXP'            => 'Zugriff auf die eigenständigen Letzte-Themen-Seiten erlauben. Dies ist unabhängig von der Anzeige auf der Foren-Übersicht.',
	'RT_PAGE'                       => 'Vollständige Seite',
	'RT_PAGE_EXP'                   => 'Eigenständige Seite der aktuellen Themen mit vollständigem Board-Header und -Footer.',
	'RT_SIMPLE_PAGE'                => 'Vereinfachte Seite',
	'RT_SIMPLE_PAGE_EXP'            => 'Vereinfachte Seite der aktuellen Themen ohne Board-Header und -Footer, geeignet zur Einbettung in einen iFrame.',
	'RT_VIEW_PAGE'                  => 'Seite in neuem Tab öffnen',

	//Werbeblock
	'RT_ADS_SETTINGS'           => 'Werbeblock',
	'RT_ADS_ENABLE'             => 'Werbeblock aktivieren',
	'RT_ADS_ENABLE_EXP'         => 'Zeigt einen benutzerdefinierten HTML-Block neben den neuesten Themen auf der Indexseite an. Nur sichtbar, wenn die Anzeigeposition auf "Seite" eingestellt ist.',
	'RT_ADS_CODE'               => 'Werbe-HTML',
	'RT_ADS_CODE_EXP'           => 'Geben Sie benutzerdefiniertes HTML für den Werbeblock ein (z.B. Werbecode, Spenden-Button oder andere Inhalte).',

	//Donatiies
	'PATREON_ALT'                => 'Werde ein Patron',
	'RT_DONATE'					=> 'Spende an RecentTopics',
	'RT_DONATE_SHORT'			=> 'Spende an RecentTopics',
	'RT_DONATE_EXPLAIN'			=> 'RecentTopics ist zu 100% kostenlos. Wenn du dies für eine nützliche Erweiterung hältst, und du die Autoren unterstützen möchtest, könntest du eine unverbindliche Spende in Erwägung ziehen.',
	)
);
