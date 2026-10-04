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
	//forum acp
	'RECENT_TOPICS_LIST'            => 'Zobraziť v „Najnovšie témy"',
	'RECENT_TOPICS_LIST_EXPLAIN'    => 'Ak je povolené, témy z tohto fóra budú zobrazené v bloku najnovších tém (rozšírenie).',

	//acp title
	'RECENT_TOPICS'                 => 'Najnovšie témy',
	'RT_CONFIG'                     => 'Nastavenie',
	'RECENT_TOPICS_EXPLAIN'         => 'Na tejto stránke môžete meniť nastavenia rozšírenia „Recent Topics".<br /><br />Konkrétne fóra je možné zahrnúť alebo vylúčiť úpravou nastavení jednotlivých fór.<br />Uistite sa tiež, že máte správne nastavené používateľské oprávnenia. Na základe oprávnení si môžu používatelia niektoré z nižšie uvedených nastavení meniť podľa svojich potrieb v používateľskom paneli.',

	//global settings
	'RT_GLOBAL_SETTINGS'            => 'Globálne nastavenia',
	'RT_DISPLAY_INDEX'              => 'Zobraziť na úvodnej stránke',
	'RT_DISPLAY_VIEWFORUM'          => 'Zobraziť na stránke fóra',
	'RT_VIEWFORUM_LOCATION'         => 'Umiestnenie na stránke fóra',
	'RT_VIEWFORUM_LOCATION_EXP'     => 'Vyberte, kde sa majú zobrazovať najnovšie témy na stránke fóra. Toto nastavenie je nezávislé od úvodnej stránky.',
	'RT_NUMBER'                     => 'Počet najnovších tém na zobrazenie',
	'RT_NUMBER_EXP'                 => 'Maximálny počet tém na zobrazenie na stránku.',
	'RT_PAGE_NUMBER'                => 'Zobraziť všetky stránky najnovších tém',
	'RT_PAGE_NUMBER_EXP'            => 'Táto funkcia prepíše nastavený maximálny počet stránok a zobrazí všetky stránky bez ohľadu na to, koľko stránok je touto voľbou nastavené.',
	'RT_PAGE_NUMBERMAX'             => 'Maximálny počet stránok',
	'RT_PAGE_NUMBERMAX_EXP'         => 'Nastavte maximum stránok pre zobrazenie v stránkovaní najnovších tém (ak nie je prepísané iným nastavením).',
	'RT_MIN_TOPIC_LEVEL'            => 'Minimálna úroveň témy',
	'RT_MIN_TOPIC_LEVEL_EXP'        => 'Určuje minimálnu úroveň typu témy pre zobrazenie. Zobrazené budú iba témy zvolenej úrovne a vyššie.',
	'RT_ANNOUNCEMENTS_FIRST'        => 'Zobraziť oznámenia ako prvé',
	'RT_ANNOUNCEMENTS_FIRST_EXP'    => 'Presunie oznámenia a globálne oznámenia na každej stránke zoznamu na začiatok danej stránky. Staršie oznámenia, ktoré na stránke nie sú, sa nepridávajú.',
	'RT_ANTI_TOPICS'                => 'Vylúčené témy',
	'RT_ANTI_TOPICS_EXP'            => 'ID tém na vylúčenie, oddelené čiarkou „," (príklad: 7,9)<br />Hodnota 0 túto funkciu vypne.',
	'RT_ANTI_TOPICS_INVALID'        => 'ID vylúčených tém musia byť celé čísla oddelené čiarkami, napríklad 7,9. Nič nebolo uložené.',
	'RT_PARENTS'                    => 'Zobraziť nadradené fóra',
	'RT_PARENTS_EXP'                => 'Zobraziť nadradené fóra v riadku podrobností pod názvom najnovšej témy.',
	'RT_TOPIC_LINK_TO'              => 'Odkaz názvu témy vedie na',
	'RT_TOPIC_LINK_TO_EXP'          => 'Vyberte, na ktorý príspevok bude názov témy v zozname najnovších tém odkazovať.',
	'RT_TOPIC_LINK_FIRST'           => 'Prvý príspevok',
	'RT_TOPIC_LINK_LAST'            => 'Posledný príspevok',
	'RT_TOPIC_LINK_UNREAD'          => 'Prvý neprečítaný príspevok',
	'RT_SIDE_SHOW_DATE'             => 'Zobraziť dátum v bočnom zobrazení',
	'RT_SIDE_SHOW_DATE_EXP'         => 'Zobrazí dátum príspevku v bočnom rozložení. Vypnite pre kompaktnejší bočný panel.',
	'RT_SHOW_LIKES'                 => 'Zobraziť počet lajkov',
	'RT_SHOW_LIKES_EXP'             => 'Zobrazí počet lajkov PostLove pri nedávnych témach.',

	//User Overridable settings. these apply for anon users and can be overridden by UCP
	'RT_OVERRIDABLE'                => 'Predvolené nastavenia (možno prepísať v používateľskom paneli)',
	'RT_LOCATION'                   => 'Umiestnenie zobrazenia',
	'RT_LOCATION_EXP'               => 'Nastavenie umiestnenia bloku najnovších tém.',
	'RT_TOP'                        => 'Zobraziť hore',
	'RT_BOTTOM'                     => 'Zobraziť dole',
	'RT_SIDE'                       => 'Zobraziť na strane',
	'RT_SORT_START_TIME'            => 'Zoradiť témy podľa času vytvorenia',
	'RT_SORT_START_TIME_EXP'        => 'Ak je povolené, najnovšie témy budú zoradené podľa času vytvorenia namiesto času odoslania posledného príspevku.',
	'RT_UNREAD_ONLY'                => 'Zobraziť iba neprečítané témy',
	'RT_UNREAD_ONLY_EXP'            => 'Ak je povolené, zobrazené budú iba neprečítané témy (bez ohľadu na to, či sú „najnovšie" alebo nie). Táto funkcia používa rovnaké nastavenia (okrem vylúčených fór/tém) ako bežný režim. Poznámka: Funguje iba pre prihlásených používateľov; návštevníci uvidia bežný zoznam.',
	'RT_RESET_DEFAULT'              => 'Obnoviť používateľské nastavenia',
	'RT_RESET_DEFAULT_EXP'          => 'Obnoviť používateľské nastavenia na predvolené hodnoty.',

	//Version checker
	'RT_VERSION_CHECK'				=> 'Kontrola verzie',
	'RT_LATEST_VERSION'				=> 'Najnovšia verzia',
	'RT_EXT_VERSION'				=> 'Verzia rozšírenia',
	'RT_CHECK_UPDATE'				=> 'Informácie o dostupných aktualizáciách nájdete na <a href="https://www.avathar.be/forum/app.php/dlext/details?df_id=35">avathar.be</a>.',

	//Standalone pages
	'RT_PAGES'                      => 'Samostatné stránky',
	'RT_PAGE_ENABLE'                => 'Povoliť samostatné stránky',
	'RT_PAGE_ENABLE_EXP'            => 'Povoliť prístup k samostatným stránkam najnovších tém. Toto je nezávislé od zobrazenia na úvodnej stránke.',
	'RT_PAGE'                       => 'Celá stránka',
	'RT_PAGE_EXP'                   => 'Samostatná stránka najnovších tém s kompletnou hlavičkou a pätičkou fóra.',
	'RT_SIMPLE_PAGE'                => 'Zjednodušená stránka',
	'RT_SIMPLE_PAGE_EXP'            => 'Zjednodušená stránka najnovších tém bez hlavičky a pätičky fóra, vhodná na vloženie do iframe.',
	'RT_VIEW_PAGE'                  => 'Zobraziť stránku v novej karte',

	//Advertisement block
	'RT_ADS_SETTINGS'           => 'Reklamný blok',
	'RT_ADS_ENABLE'             => 'Povoliť reklamný blok',
	'RT_ADS_ENABLE_EXP'         => 'Zobrazí vlastný HTML blok vedľa nedávnych tém na úvodnej stránke. Viditeľné iba pri nastavení umiestnenia na "Strana".',
	'RT_ADS_CODE'               => 'HTML reklamy',
	'RT_ADS_CODE_EXP'           => 'Zadajte vlastný HTML pre zobrazenie v reklamnom bloku (napr. reklamný kód, tlačidlo pre dary alebo iný obsah).',

	//Donation
	'PATREON_ALT'                => 'Staňte sa patrónom',
	'RT_DONATE'					=> 'Prispejte na vývoj RecentTopics',
	'RT_DONATE_SHORT'			=> 'Podporte vývoj rozšírenia RecentTopics',
	'RT_DONATE_EXPLAIN'			=> 'Rozšírenie RecentTopics je úplne zadarmo. Je to hobby projekt, ktorému venujeme veľa svojho času a financií. Robíme to radi, ale čas je drahý. Ak vám rozšírenie RecentTopics príde užitočné, budeme veľmi radi, keď nás podporíte.',
	)
);
