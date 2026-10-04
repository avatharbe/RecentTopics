<?php
/**
 *
 * @package Recent Topics Extension
 * @copyright (c) 2026 Andreas Vandenberghe
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Checks the Who Is Online label in every language pack.
 *
 * See: https://github.com/avatharbe/RecentTopics/issues/199
 */

namespace avathar\recenttopics\tests\language;

class viewonline_lang_test extends \phpbb_test_case
{
	/**
	 * One row per language pack shipped with the extension.
	 */
	public function language_data()
	{
		$data = [];
		foreach (glob(__DIR__ . '/../../language/*/recenttopics.php') as $file)
		{
			$data[basename(dirname($file))] = [$file];
		}

		return $data;
	}

	/**
	 * Who Is Online already wraps the location in <a href="{U_FORUM_LOCATION}">, and the listener
	 * passes no argument, so VIEWING_RECENT_TOPICS must be plain text: a nested link with an
	 * unfilled %s broke the label (#199).
	 *
	 * @dataProvider language_data
	 */
	public function test_viewing_recent_topics_is_plain_text($file)
	{
		$lang = [];
		include $file;

		$this->assertArrayHasKey('VIEWING_RECENT_TOPICS', $lang);
		$this->assertStringNotContainsString('<', $lang['VIEWING_RECENT_TOPICS']);
		$this->assertStringNotContainsString('%', $lang['VIEWING_RECENT_TOPICS']);
	}
}
