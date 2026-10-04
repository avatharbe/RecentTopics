<?php
/**
 *
 * @package Recent Topics Extension
 * @copyright (c) 2015 PayBas
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Based on the original NV Recent Topics by Joas Schilling (nickvergessen)
 * Czech translation by R3gi
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
	'RECENT_TOPICS_LIST'            => 'Zahrnout obsah do nedávných témat',
	'RECENT_TOPICS_LIST_EXPLAIN'    => 'Je-li povoleno, témata z tohoto fóra mohou být zobrazena v bloku nedávných témat (rozšíření).',
	//acp title
	'RECENT_TOPICS'                 => 'Nedávná témata',
	'RT_CONFIG'                     => 'Nastavení',
	'RECENT_TOPICS_EXPLAIN'         => 'Na této stránce můžete měnit nastavení rozšíření „Recent Topics“.<br /><br />Konkrétní fóra lze zahrnout či vyloučit změnou nastavení jednotlivých fór.<br />Ujistěte se také, že mají uživatelé správně nastavena uživatelská oprávnění dle vašich potřeb. Na základě oprávnění si mohou uživatelé některá níže uvedená nastavení měnit dle svých potřeb ve svém uživatelském panelu.',
	//global settings
	'RT_GLOBAL_SETTINGS'            => 'Globální nastavení',
	'RT_DISPLAY_INDEX'              => 'Zobrazovat na úvodní stránce?',
	'RT_DISPLAY_VIEWFORUM'          => 'Zobrazovat na stránce fóra',
	'RT_VIEWFORUM_LOCATION'         => 'Umístění na stránce fóra',
	'RT_VIEWFORUM_LOCATION_EXP'     => 'Vyberte, kde se mají zobrazovat nedávná témata na stránce fóra. Toto nastavení je nezávislé na úvodní stránce.',
	'RT_NUMBER'                     => 'Nedávná témata',
	'RT_NUMBER_EXP'                 => 'Počet nedávných témat k zobrazení.',
	'RT_PAGE_NUMBER'                => 'Počet stránek nedávných témat',
	'RT_PAGE_NUMBER_EXP'            => 'Tato funkce přepíše nastavený maximální počet stránek a zobrazí všechny stránky bez ohledu na to, kolik stránek je touto volbou nastaveno.',
	'RT_PAGE_NUMBERMAX'             => 'Maximální počet stránek',
	'RT_PAGE_NUMBERMAX_EXP'         => 'Nastavte maximum stránek pro zobrazení ve stránkování nedávných témat (není-li přepsáno jiným nastavením).',
	'RT_MIN_TOPIC_LEVEL'            => 'Minimální úroveň tématu',
	'RT_MIN_TOPIC_LEVEL_EXP'        => 'Určuje minimální úroveň typu tématu pro zobrazení. Zobrazena budou pouze témata zvolené úrovně a vyšší.',
	'RT_ANNOUNCEMENTS_FIRST'        => 'Zobrazit oznámení jako první',
	'RT_ANNOUNCEMENTS_FIRST_EXP'    => 'Přesune oznámení a globální oznámení na každé stránce seznamu na začátek dané stránky. Starší oznámení, která na stránce nejsou, se nepřidávají.',
	'RT_ANTI_TOPICS'                => 'Vyloučená témata',
	'RT_ANTI_TOPICS_EXP'            => 'Identifikátory témat k vyloučení, oddělené čárkou „,“ (příklad: 7,9)<br />',
	'RT_ANTI_TOPICS_INVALID'        => 'ID vyloučených témat musí být celá čísla oddělená čárkami, například 7,9. Nic nebylo uloženo.',
	'RT_PARENTS'                    => 'Zobrazit nadřazená fóra',
	'RT_PARENTS_EXP'                => 'Zobrazit nadřazená fóra v řádku podrobností pod názvem nedávného tématu.',
	'RT_TOPIC_LINK_TO'              => 'Odkaz názvu tématu vede na',
	'RT_TOPIC_LINK_TO_EXP'          => 'Zvolte, na který příspěvek bude název tématu v seznamu nedávných témat odkazovat.',
	'RT_TOPIC_LINK_FIRST'           => 'První příspěvek',
	'RT_TOPIC_LINK_LAST'            => 'Poslední příspěvek',
	'RT_TOPIC_LINK_UNREAD'          => 'První nepřečtený příspěvek',
	'RT_SIDE_SHOW_DATE'             => 'Zobrazit datum v postranním zobrazení',
	'RT_SIDE_SHOW_DATE_EXP'         => 'Zobrazí datum příspěvku v postranním rozložení. Vypněte pro kompaktnější postranní panel.',
	'RT_SHOW_LIKES'                 => 'Zobrazit počet lajků',
	'RT_SHOW_LIKES_EXP'             => 'Zobrazí počet lajků PostLove u nedávných témat.',

	//User Overridable settings. these apply for anon users and can be overridden by UCP
	'RT_OVERRIDABLE'                => 'Výchozí nastavení (lze přepsat v uživatelském panelu)',
	'RT_LOCATION'                   => 'Místo zobrazení',
	'RT_LOCATION_EXP'               => 'Nastavení umístění bloku nedávných témat. (prosilver)',
	'RT_TOP'                        => 'Zobrazit nahoře',
	'RT_BOTTOM'                     => 'Zobrazit dole',
	'RT_SIDE'                       => 'Zobrazit na straně',
	'RT_SORT_START_TIME'            => 'Řadit témata dle času založení',
	'RT_SORT_START_TIME_EXP'        => 'Je-li povoleno, nedávná témata budou řazena podle času založení namísto času odeslání posledního příspěvku.',
	'RT_UNREAD_ONLY'                => 'Zobrazovat pouze nepřečtená témata',
	'RT_UNREAD_ONLY_EXP'            => 'Je-li povoleno, budou zobrazena pouze nepřečtená témata (nehledě na to, zda jsou „nedávná“ či ne). Tato funkce používá stejné nastavení (vyjma fór/témat apod.) jako běžný režim. Poznámka: Nastavení funguje jen pro přihlášené uživatele, návštěvníci uvidí stále jen běžný seznam.',
	'RT_RESET_DEFAULT'              => 'Resetovat uživatelské nastavení',
	'RT_RESET_DEFAULT_EXP'          => 'Obnovit uživatelské nastavení na výchozí hodnoty.',
	//Version checker
	'RT_VERSION_CHECK'				=> 'Kontrola verze',
	'RT_LATEST_VERSION'				=> 'Poslední verze',
	'RT_EXT_VERSION'				=> 'Verze rozšíření',
	'RT_CHECK_UPDATE'				=> 'Informace o dostupných aktualizacích naleznete zde: <a href="https://www.avathar.be/forum/app.php/dlext/details?df_id=35">avathar.be</a>.',
	//Standalone pages
	'RT_PAGES'                      => 'Samostatné stránky',
	'RT_PAGE_ENABLE'                => 'Povolit samostatné stránky',
	'RT_PAGE_ENABLE_EXP'            => 'Povolit přístup k samostatným stránkám nedávných témat. Toto je nezávislé na zobrazení na úvodní stránce.',
	'RT_PAGE'                       => 'Celá stránka',
	'RT_PAGE_EXP'                   => 'Samostatná stránka nedávných témat s kompletní hlavičkou a patičkou fóra.',
	'RT_SIMPLE_PAGE'                => 'Zjednodušená stránka',
	'RT_SIMPLE_PAGE_EXP'            => 'Zjednodušená stránka nedávných témat bez hlavičky a patičky fóra, vhodná pro vložení do iframe.',
	'RT_VIEW_PAGE'                  => 'Zobrazit stránku v nové kartě',

	//Advertisement block
	'RT_ADS_SETTINGS'           => 'Reklamní blok',
	'RT_ADS_ENABLE'             => 'Povolit reklamní blok',
	'RT_ADS_ENABLE_EXP'         => 'Zobrazí vlastní HTML blok vedle nedávných témat na úvodní stránce. Viditelné pouze při nastavení umístění na "Strana".',
	'RT_ADS_CODE'               => 'HTML reklamy',
	'RT_ADS_CODE_EXP'           => 'Zadejte vlastní HTML pro zobrazení v reklamním bloku (např. reklamní kód, tlačítko pro dary nebo jiný obsah).',

	//Donation
	'PATREON_ALT'                => 'Staňte se patronem',
	'RT_DONATE'					=> 'Přispějte na vývoj RecentTopics',
	'RT_DONATE_SHORT'			=> 'Podpořte vývoj rozšíření RecentTopics',
	'RT_DONATE_EXPLAIN'			=> 'Rozšíření RecentTopics je zcela zdarma. Jedná se o hobby projekt, kterému věnujeme spoustu svého času a financí. Děláme to rádi, ale čas je drahý. Pokud vám rozšíření RecentTopics přijde užitečné, budeme velmi rádi, když nás podpoříte.',
	)
);
