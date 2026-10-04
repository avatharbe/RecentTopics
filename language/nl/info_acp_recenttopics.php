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

$lang = array_merge(
	$lang, array(
		//forum acp
		'RECENT_TOPICS_LIST'            => 'Weergeven in Recente Onderwerpen',
		'RECENT_TOPICS_LIST_EXPLAIN'    => 'Vink dit aan om onderwerpen van dit forum weer te geven in de ”Recente Onderwerpen” extensie.',

		//acp title
		'RECENT_TOPICS'                 => 'Recente Onderwerpen',
		'RT_CONFIG'                     => 'Instellingen',
		'RECENT_TOPICS_EXPLAIN'         => 'Hier kan je de instellingen aanpassen van de Recente Onderwerpen Extensie.<br /><br />Forumactivering kan ingesteld worden in het Forum beheerderspaneel voor dat forum.<br />Ga ook uw gebruikerspaneel na dat voorrang heeft op beheerderspaneelinstellingen.',

		//algemene instellingen
		'RT_GLOBAL_SETTINGS'            => 'Algemene instellingen',
		'RT_DISPLAY_INDEX'              => 'Toon op de indexpagina',
		'RT_DISPLAY_VIEWFORUM'          => 'Toon op de forumpagina',
		'RT_VIEWFORUM_LOCATION'         => 'Weergavelocatie forumpagina',
		'RT_VIEWFORUM_LOCATION_EXP'     => 'Selecteer waar recente onderwerpen op de forumpagina worden weergegeven. Deze instelling is onafhankelijk van de indexpagina.',
		'RT_NUMBER'                     => 'Aantal recente onderwerpen',
		'RT_NUMBER_EXP'                 => 'Maximum aantal onderwerpen per pagina.',
		'RT_PAGE_NUMBER'                => 'Toon alle pagina’s',
		'RT_PAGE_NUMBER_EXP'            => 'Deze functie overschrijft het ingestelde maximale aantal pagina’s en toont alle pagina’s, ongeacht het aantal pagina’s dat door de optie is ingesteld.',
		'RT_PAGE_NUMBERMAX'             => 'Maximum aantal pagina’s',
		'RT_PAGE_NUMBERMAX_EXP'         => 'Stel het maximum aantal pagina’s in.',
		'RT_MIN_TOPIC_LEVEL'            => 'Onderwerptypes',
		'RT_MIN_TOPIC_LEVEL_EXP'        => 'Stel het minimum weer te geven onderwerptype in.',
		'RT_ANNOUNCEMENTS_FIRST'        => 'Mededelingen eerst tonen',
		'RT_ANNOUNCEMENTS_FIRST_EXP'    => 'Plaatst de mededelingen en algemene mededelingen op elke pagina van de lijst bovenaan die pagina. Oudere mededelingen die niet op de pagina staan, worden niet toegevoegd.',
		'RT_ANTI_TOPICS'                => 'Uitgesloten onderwerpen',
		'RT_ANTI_TOPICS_EXP'            => 'Vul de onderwerp id’s in (bijvoorbeeld 7,9), anders 0. (deze nummers vind je in de url viewtopic.php?t=12345)',
		'RT_ANTI_TOPICS_INVALID'        => 'Uitgesloten onderwerp-ID’s moeten gehele getallen zijn, gescheiden door komma’s, bijvoorbeeld 7,9. Er is niets opgeslagen.',
		'RT_PARENTS'                    => 'Weergeven van hoofdforums',
		'RT_PARENTS_EXP'                => 'Toon de hoofdforums in de onderwerpregel van de recente onderwerpen.',
		'RT_TOPIC_LINK_TO'              => 'Onderwerptitel linkt naar',
		'RT_TOPIC_LINK_TO_EXP'          => 'Kies naar welk bericht de onderwerptitel linkt in de lijst van recente onderwerpen.',
		'RT_TOPIC_LINK_FIRST'           => 'Eerste bericht',
		'RT_TOPIC_LINK_LAST'            => 'Laatste bericht',
		'RT_TOPIC_LINK_UNREAD'          => 'Eerste ongelezen bericht',
		'RT_SIDE_SHOW_DATE'             => 'Datum tonen in zijweergave',
		'RT_SIDE_SHOW_DATE_EXP'         => 'Toont de berichtdatum in de zijweergave. Uitschakelen voor een compactere zijbalk.',
		'RT_SHOW_LIKES'                 => 'Aantal likes tonen',
		'RT_SHOW_LIKES_EXP'             => 'Toont het aantal PostLove likes naast de recente onderwerpen.',

		//user instellingen
		'RT_OVERRIDABLE'                => 'Instellingen waarvoor gebruikerspaneel voorrang heeft',
		'RT_LOCATION'                   => 'Blok instellingen',
		'RT_LOCATION_EXP'               => 'Kies plaats van ’Recente onderwerpen’ blok.',
		'RT_TOP'                        => 'Toon boven',
		'RT_BOTTOM'                     => 'Toon beneden',
		'RT_SIDE'                       => 'Toon rechts',
		'RT_SORT_START_TIME'            => 'Sorteer op onderwerptijdstip',
		'RT_SORT_START_TIME_EXP'        => 'Sorteer op onderwerptijdstip, niet op tijdstip laatste reactie',
		'RT_UNREAD_ONLY'                => 'Enkel ongelezen onderwerpen weergeven',
		'RT_UNREAD_ONLY_EXP'            => 'Activeer deze optie om enkel ongelezen recente onderwerpen weer te geven.',
		'RT_RESET_DEFAULT'              => 'Stel gebruikersinstellingen opnieuw in',
		'RT_RESET_DEFAULT_EXP'          => 'Stel instellingen van alle gebruikers opnieuw in tot de standaard',

		//Versie controle
		'RT_VERSION_CHECK'				=> 'Versiecontrole',
		'RT_LATEST_VERSION'				=> 'Laatste versie',
		'RT_EXT_VERSION'				=> 'Extensieversie',
		'RT_CHECK_UPDATE'				=> 'Bezoek <a href="https://www.avathar.be/forum/app.php/dlext/details?df_id=35">avathar.be</a> voor nieuwere versies.',

		//Zelfstandige pagina's
		'RT_PAGES'                      => 'Zelfstandige pagina\'s',
		'RT_PAGE_ENABLE'                => 'Zelfstandige pagina\'s inschakelen',
		'RT_PAGE_ENABLE_EXP'            => 'Toegang tot de zelfstandige Recente Onderwerpen pagina\'s toestaan. Dit is onafhankelijk van de weergave op de indexpagina.',
		'RT_PAGE'                       => 'Volledige pagina',
		'RT_PAGE_EXP'                   => 'Zelfstandige pagina met recente onderwerpen inclusief volledige header en footer van het forum.',
		'RT_SIMPLE_PAGE'                => 'Vereenvoudigde pagina',
		'RT_SIMPLE_PAGE_EXP'            => 'Vereenvoudigde pagina met recente onderwerpen zonder header en footer, geschikt voor inbedding in een iframe.',
		'RT_VIEW_PAGE'                  => 'Bekijk pagina in nieuw tabblad',

		//Advertentieblok
		'RT_ADS_SETTINGS'           => 'Advertentieblok',
		'RT_ADS_ENABLE'             => 'Advertentieblok inschakelen',
		'RT_ADS_ENABLE_EXP'         => 'Toon een aangepast HTML-blok naast de recente onderwerpen op de indexpagina. Alleen zichtbaar wanneer de weergavepositie op "Zijkant" staat.',
		'RT_ADS_CODE'               => 'Advertentie-HTML',
		'RT_ADS_CODE_EXP'           => 'Voer aangepaste HTML in voor het advertentieblok (bijv. advertentiecode, donatieknop of andere inhoud).',

		//Donatiies
		'PATREON_ALT'                => 'Word een patron',
		'RT_DONATE'					=> 'Donatie aan RecentTopics',
		'RT_DONATE_SHORT'			=> 'Doe een donatie aan RecentTopics',
		'RT_DONATE_EXPLAIN'			=> 'RecentTopics is 100% gratis. Als je dit een nuttige extensie vindt en je de auteurs wil ondersteunen, kan je overwegen om een vrijblijvende donatie te doen.',
	)
);
