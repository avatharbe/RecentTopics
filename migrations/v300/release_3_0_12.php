<?php
/**
 *
 * @package Recent Topics Extension
 * @copyright (c) 2026 Andreas Vandenberghe (avathar)
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Release 3.0.12 — adds the "Show announcements first" setting (issue #201).
 */

namespace avathar\recenttopics\migrations\v300;

class release_3_0_12 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['rt_announcements_first']);
	}

	public static function depends_on()
	{
		return ['\avathar\recenttopics\migrations\v300\release_3_0_0'];
	}

	public function update_data()
	{
		return [
			['config.add', ['rt_announcements_first', 0]],
		];
	}

	public function revert_data()
	{
		return [
			['config.remove', ['rt_announcements_first']],
		];
	}
}
