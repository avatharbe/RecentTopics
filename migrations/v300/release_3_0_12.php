<?php
/**
 *
 * @package Recent Topics Extension
 * @copyright (c) 2026 Andreas Vandenberghe (avathar)
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Release 3.0.12 — adds the "Show announcements first" setting (issue #201) and clears the
 * permission cache once for boards affected by issue #194.
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
			['custom', [[$this, 'clear_permission_cache']]],
		];
	}

	/**
	 * Clear every user's cached permission set.
	 *
	 * Boards that enabled 3.0.11 or earlier may still hold cached sets without u_rt_view, because
	 * rt_perms added the permissions without clearing the cache (issue #194). That fix only reaches
	 * fresh installs, so the upgrade clears the cache once here. Same pattern as core's
	 * remove_orphaned_roles migration; no $auth is injected into a migration.
	 *
	 * @return void
	 */
	public function clear_permission_cache()
	{
		$auth = new \phpbb\auth\auth();
		$auth->acl_clear_prefetch();
	}

	public function revert_data()
	{
		return [
			['config.remove', ['rt_announcements_first']],
		];
	}
}
