<?php
/**
 *
 * @package Recent Topics Extension
 * @copyright (c) 2026 Andreas Vandenberghe
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Tests for the ACP "Excluded topic IDs" input (rt_anti_topics).
 *
 * See: https://github.com/avatharbe/RecentTopics/issues/217
 */

namespace avathar\recenttopics\tests\acp;

class anti_topics_test extends \phpbb_test_case
{
	/**
	 * Raw ACP input => value to store, or null when the input must be rejected.
	 */
	public function anti_topics_data()
	{
		return [
			'single id'                    => ['7', '7'],
			'list'                         => ['7,9', '7,9'],
			'spaces are trimmed'           => [' 7 , 9 ', '7,9'],
			'duplicates are dropped'       => ['7,9,7', '7,9'],
			'trailing comma is ignored'    => ['7,9,', '7,9'],
			'empty field excludes nothing' => ['', '0'],
			'blank field excludes nothing' => ['  ', '0'],
			'zero keeps meaning nothing'   => ['0', '0'],
			'text is rejected'             => ['7,abc', null],
			'decimal is rejected'          => ['7.5', null],
			'negative is rejected'         => ['-3', null],
		];
	}

	/**
	 * Clearing the field must be possible and invalid entries must be reported, not silently
	 * dropped while the ACP says the settings were saved (#217).
	 *
	 * @dataProvider anti_topics_data
	 */
	public function test_normalise_anti_topics($input, $expected)
	{
		$this->assertSame($expected, \avathar\recenttopics\acp\recenttopics_module::normalise_anti_topics($input));
	}
}
