<?php
/**
 *
 * @package Recent Topics Extension
 * @copyright (c) 2026 Andreas Vandenberghe
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Static checks on the extension's templates.
 *
 * See: https://github.com/avatharbe/RecentTopics/issues/215
 */

namespace avathar\recenttopics\tests\template;

class template_vars_test extends \phpbb_test_case
{
	/**
	 * One row per template file the extension ships.
	 */
	public function template_data()
	{
		$root = dirname(__DIR__, 2) . '/styles';
		$data = [];
		$files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS));
		foreach ($files as $file)
		{
			if ($file->getExtension() === 'html')
			{
				$data[substr($file->getPathname(), strlen($root) + 1)] = [$file->getPathname()];
			}
		}
		ksort($data);

		return $data;
	}

	/**
	 * VIEW_LATEST_POST is never assigned, so it rendered an empty screen-reader label on the
	 * last-post link. The link's text comes from lang('GOTO_LAST_POST'), matching its title (#215).
	 *
	 * @dataProvider template_data
	 */
	public function test_no_unassigned_view_latest_post($file)
	{
		$this->assertStringNotContainsString('VIEW_LATEST_POST', file_get_contents($file));
	}
}
