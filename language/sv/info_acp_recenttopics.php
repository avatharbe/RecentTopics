<?php
/**
 *
 * @package Recent Topics Extension
 * Swedish translation
 *
 * @copyright (c) 2015 PayBas
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Based on the original NV Recent Topics by Joas Schilling (nickvergessen)
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
	'RECENT_TOPICS_LIST'            => 'Visa i "Senaste trådar"',
	'RECENT_TOPICS_LIST_EXPLAIN'    => 'Aktivera för att visa trådar från detta forum i tillägget "Senaste trådar".',

	//acp title
	'RECENT_TOPICS'                 => 'Senaste trådar',
	'RT_CONFIG'                     => 'Konfiguration',
	'RECENT_TOPICS_EXPLAIN'         => 'På den här sidan kan du ändra inställningarna för tillägget Senaste trådar.<br /><br />Specifika forum kan inkluderas eller exkluderas genom att redigera respektive forum i administratörspanelen.<br />Kontrollera även användarrättigheterna, som låter användare ändra några av inställningarna nedan själva.',

	//global settings
	'RT_GLOBAL_SETTINGS'            => 'Globala inställningar',
	'RT_DISPLAY_INDEX'              => 'Visa på indexsidan',
	'RT_DISPLAY_VIEWFORUM'          => 'Visa på forumsidan',
	'RT_VIEWFORUM_LOCATION'         => 'Visningsplats på forumsidan',
	'RT_VIEWFORUM_LOCATION_EXP'     => 'Välj var senaste trådar ska visas på forumsidan. Denna inställning är oberoende av indexsidan.',
	'RT_NUMBER'                     => 'Antal senaste trådar att visa',
	'RT_NUMBER_EXP'                 => 'Maximalt antal trådar att visa per sida.',
	'RT_PAGE_NUMBER'                => 'Visa alla sidor med senaste trådar',
	'RT_PAGE_NUMBER_EXP'            => 'Denna funktion åsidosätter det inställda maximala antalet sidor och visar alla sidor oavsett hur många sidor som har angetts.',
	'RT_PAGE_NUMBERMAX'             => 'Maximalt antal sidor',
	'RT_PAGE_NUMBERMAX_EXP'         => 'Ange det maximala antalet sidor att visa i pagineringen av senaste trådar om det inte åsidosätts.',
	'RT_MIN_TOPIC_LEVEL'            => 'Lägsta trådtypsnivå',
	'RT_MIN_TOPIC_LEVEL_EXP'        => 'Bestämmer den lägsta nivån av trådtyp som ska visas. Endast trådar av den angivna nivån och högre visas.',
	'RT_ANNOUNCEMENTS_FIRST'        => 'Visa meddelanden först',
	'RT_ANNOUNCEMENTS_FIRST_EXP'    => 'Flyttar meddelanden och globala meddelanden på varje sida i listan överst på den sidan. Äldre meddelanden som inte finns på sidan läggs inte till.',
	'RT_ANTI_TOPICS'                => 'Exkluderade tråd-ID:n',
	'RT_ANTI_TOPICS_EXP'            => 'ID:n för trådar att exkludera, separerade med "," (Exempel: 7,9)<br />Värdet 0 inaktiverar denna funktion.',
	'RT_PARENTS'                    => 'Visa överordnade forum',
	'RT_PARENTS_EXP'                => 'Visa överordnade forum i trådraden för senaste trådar.',
	'RT_TOPIC_LINK_TO'              => 'Trådtiteln länkar till',
	'RT_TOPIC_LINK_TO_EXP'          => 'Välj vilket inlägg trådtiteln ska länka till i listan över senaste trådar.',
	'RT_TOPIC_LINK_FIRST'           => 'Första inlägget',
	'RT_TOPIC_LINK_LAST'            => 'Senaste inlägget',
	'RT_TOPIC_LINK_UNREAD'          => 'Första olästa inlägget',
	'RT_SIDE_SHOW_DATE'             => 'Visa datum i sidovy',
	'RT_SIDE_SHOW_DATE_EXP'         => 'Visar inläggsdatumet i sidolayouten. Inaktivera för en mer kompakt sidopanel.',
	'RT_SHOW_LIKES'                 => 'Visa antal gillningar',
	'RT_SHOW_LIKES_EXP'             => 'Visar PostLove-gillningar bredvid senaste ämnen.',

	//User Overridable settings
	'RT_OVERRIDABLE'                => 'Inställningar som kan ändras i kontrollpanelen',
	'RT_LOCATION'                   => 'Visningsplats',
	'RT_LOCATION_EXP'               => 'Välj plats för att visa senaste trådar.',
	'RT_TOP'                        => 'Visa överst',
	'RT_BOTTOM'                     => 'Visa nederst',
	'RT_SIDE'                       => 'Visa vid sidan',
	'RT_SORT_START_TIME'            => 'Sortera efter trådens starttid',
	'RT_SORT_START_TIME_EXP'        => 'Aktivera för att sortera senaste trådar efter trådens starttid istället för senaste inläggets tid.',
	'RT_UNREAD_ONLY'                => 'Visa bara olästa trådar',
	'RT_UNREAD_ONLY_EXP'            => 'Aktivera för att bara visa olästa trådar (oavsett om de är "senaste" eller inte). Denna funktion använder samma inställningar (exkluderade forum/trådar osv.) som normalläget. Obs: detta fungerar bara för inloggade användare; gäster ser den vanliga listan.',
	'RT_RESET_DEFAULT'              => 'Återställ användarinställningar',
	'RT_RESET_DEFAULT_EXP'          => 'Återställ användarinställningar till standard.',

	//Version checker
	'RT_VERSION_CHECK'				=> 'Versionskontroll',
	'RT_LATEST_VERSION'				=> 'Senaste version',
	'RT_EXT_VERSION'				=> 'Tilläggsversion',
	'RT_CHECK_UPDATE'				=> 'Besök <a href="https://www.avathar.be/forum/app.php/dlext/details?df_id=35">avathar.be</a> för att se om det finns uppdateringar.',

	//Standalone pages
	'RT_PAGES'                      => 'Fristående sidor',
	'RT_PAGE_ENABLE'                => 'Aktivera fristående sidor',
	'RT_PAGE_ENABLE_EXP'            => 'Tillåt åtkomst till de fristående sidorna för senaste ämnen. Detta är oberoende av visningen på indexsidan.',
	'RT_PAGE'                       => 'Fullständig sida',
	'RT_PAGE_EXP'                   => 'Fristående sida med senaste trådar med fullständigt sidhuvud och sidfot.',
	'RT_SIMPLE_PAGE'                => 'Förenklad sida',
	'RT_SIMPLE_PAGE_EXP'            => 'Förenklad sida med senaste trådar utan sidhuvud och sidfot, lämplig för inbäddning i en iframe.',
	'RT_VIEW_PAGE'                  => 'Visa sidan i ny flik',

	//Advertisement block
	'RT_ADS_SETTINGS'           => 'Annonsblock',
	'RT_ADS_ENABLE'             => 'Aktivera annonsblock',
	'RT_ADS_ENABLE_EXP'         => 'Visar ett anpassat HTML-block bredvid senaste ämnen på indexsidan. Synligt endast när visningsplatsen är inställd på "Sida".',
	'RT_ADS_CODE'               => 'Annons-HTML',
	'RT_ADS_CODE_EXP'           => 'Ange anpassad HTML att visa i annonsblocket (t.ex. annonskod, donationsknapp eller annat innehåll).',

	//Donation
	'PATREON_ALT'                => 'Bli en patron',
	'RT_DONATE'					=> 'Donera till RecentTopics',
	'RT_DONATE_SHORT'			=> 'Gör en donation till RecentTopics',
	'RT_DONATE_EXPLAIN'			=> 'RecentTopics är 100% gratis. Det är ett hobbyprojekt som jag lägger min tid och mina pengar på, bara för nöjes skull. Om du gillar att använda RecentTopics, överväg gärna att donera. Jag skulle verkligen uppskatta det. Inga villkor.',
	)
);
