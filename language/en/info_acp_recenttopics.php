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
	//forum acp
	'RECENT_TOPICS_LIST'            => 'Display on "Recent Topics"',
	'RECENT_TOPICS_LIST_EXPLAIN'    => 'Enable to display topics in this forum in the “recent topics” extension.',

	//acp title
	'RECENT_TOPICS'                 => 'Recent Topics',
	'RT_CONFIG'                     => 'Configuration',
	'RECENT_TOPICS_EXPLAIN'         => 'On this page you can change the settings specific for the Recent Topics extension.<br /><br />Specific forums can be included or excluded by editing the respective forums in your ACP.<br />Also be sure to check your user permissions, which allow users to change some of the settings found below for themselves.',

	//global settings
	'RT_GLOBAL_SETTINGS'            => 'Global Settings',
	'RT_DISPLAY_INDEX'              => 'Display on Index page',
	'RT_DISPLAY_VIEWFORUM'          => 'Display on Viewforum page',
	'RT_VIEWFORUM_LOCATION'         => 'Viewforum display location',
	'RT_VIEWFORUM_LOCATION_EXP'     => 'Select where to display recent topics on the viewforum page. This setting is independent from the index page location.',
	'RT_NUMBER'                     => 'Number of recent topics to show',
	'RT_NUMBER_EXP'                 => 'Maximum number of topics to display per page.',
	'RT_PAGE_NUMBER'                => 'Show all recent topic pages',
	'RT_PAGE_NUMBER_EXP'            => 'This function overrides the configured maximum number of pages and shows all pages no matter how many pages are set by the option.',
	'RT_PAGE_NUMBERMAX'             => 'Maximum number of pages',
	'RT_PAGE_NUMBERMAX_EXP'         => 'Set the page maximum to display in the recent topics pagination unless overridden.',
	'RT_MIN_TOPIC_LEVEL'            => 'Minimum topic type level',
	'RT_MIN_TOPIC_LEVEL_EXP'        => 'Determines the minimum level of the topic-type to display. It will only display topics of the set level and higher.',
	'RT_ANNOUNCEMENTS_FIRST'        => 'Show announcements first',
	'RT_ANNOUNCEMENTS_FIRST_EXP'    => 'Moves the announcements and global announcements on each page of the list to the top of that page. Older announcements that are not on the page are not added.',
	'RT_ANTI_TOPICS'                => 'Excluded topic IDs',
	'RT_ANTI_TOPICS_EXP'            => 'The IDs of topics to exclude, separated by “,” (Example: 7,9)<br />The value 0 disables this behaviour.',
	'RT_ANTI_TOPICS_INVALID'        => 'Excluded topic IDs must be whole numbers separated by commas, for example 7,9. Nothing was saved.',
	'RT_PARENTS'                    => 'Display parent forums',
	'RT_PARENTS_EXP'                => 'Display parent forums inside the topic row of recent topics.',
	'RT_TOPIC_LINK_TO'              => 'Topic title links to',
	'RT_TOPIC_LINK_TO_EXP'          => 'Choose which post the topic title links to in the Recent Topics list.',
	'RT_TOPIC_LINK_FIRST'           => 'First post',
	'RT_TOPIC_LINK_LAST'            => 'Last post',
	'RT_TOPIC_LINK_UNREAD'          => 'First unread post',
	'RT_SIDE_SHOW_DATE'             => 'Show date in side view',
	'RT_SIDE_SHOW_DATE_EXP'         => 'Display the post date in the side layout. Disable for a more compact sidebar.',
	'RT_SHOW_LIKES'                 => 'Show like counts',
	'RT_SHOW_LIKES_EXP'             => 'Display postlove like counts alongside recent topics.',

	//User Overridable settings. these apply for anon users and can be overridden by UCP
	'RT_OVERRIDABLE'                => 'UCP Overridable Settings',
	'RT_LOCATION'                   => 'Display location',
	'RT_LOCATION_EXP'               => 'Select location to display recent topics.',
	'RT_TOP'                        => 'Show on top',
	'RT_BOTTOM'                     => 'Show on bottom',
	'RT_SIDE'                       => 'Show on side',
	'RT_SORT_START_TIME'            => 'Sort by topic start time',
	'RT_SORT_START_TIME_EXP'        => 'Enable to sort recent topics by the starting time of the topic, instead of the last post time.',
	'RT_UNREAD_ONLY'                => 'Only display unread topics',
	'RT_UNREAD_ONLY_EXP'            => 'Enable to only display unread topics (whether they are “recent” or not). This function uses the same settings (excluding forums/topics etc.) as normal mode. Note: this only works for logged-in users; guests will get the normal list.',
	'RT_RESET_DEFAULT'              => 'Reset user settings',
	'RT_RESET_DEFAULT_EXP'          => 'Reset user settings to default.',

	//Version checker
	'RT_VERSION_CHECK'				=> 'Version Check',
	'RT_LATEST_VERSION'				=> 'Latest version',
	'RT_EXT_VERSION'				=> 'Extension version',
	'RT_CHECK_UPDATE'				=> 'Check <a href="https://www.avathar.be/forum/app.php/dlext/details?df_id=35">avathar.be</a> to see if there are updates available.',

	//Standalone pages
	'RT_PAGES'                      => 'Standalone Pages',
	'RT_PAGE_ENABLE'                => 'Enable standalone pages',
	'RT_PAGE_ENABLE_EXP'            => 'Allow access to the standalone Recent Topics pages. This is independent from the index page display.',
	'RT_PAGE'                       => 'Full page',
	'RT_PAGE_EXP'                   => 'Standalone Recent Topics page with full board header and footer.',
	'RT_SIMPLE_PAGE'                => 'Simplified page',
	'RT_SIMPLE_PAGE_EXP'            => 'Simplified Recent Topics page without board header/footer, suitable for embedding in an iframe.',
	'RT_VIEW_PAGE'                  => 'View page in new tab',

	//Advertisement block
	'RT_ADS_SETTINGS'           => 'Advertisement Block',
	'RT_ADS_ENABLE'             => 'Enable advertisement block',
	'RT_ADS_ENABLE_EXP'         => 'Display a custom HTML block alongside Recent Topics on the index page. Only visible when display location is set to "Side".',
	'RT_ADS_CODE'               => 'Advertisement HTML',
	'RT_ADS_CODE_EXP'           => 'Enter custom HTML to display in the advertisement block (e.g. ad code, donation button, or any other content).',

	//Donation
	'PATREON_ALT'                => 'Become a patron',
	'RT_DONATE'					=> 'Donate to RecentTopics',
	'RT_DONATE_SHORT'			=> 'Make a donation to RecentTopics',
	'RT_DONATE_EXPLAIN'			=> 'RecentTopics is 100% free. It is a hobby project that I am spending my time and money on, just for the fun of it. If you enjoy using RecentTopics, please consider making a donation. I would really appreciate it. No strings attached.',
	)
);
