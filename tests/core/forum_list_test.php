<?php
/**
 *
 * @package Recent Topics Extension
 * @copyright (c) 2026 Andreas Vandenberghe
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Tests for recenttopics::get_forum_list(), which decides which forums the
 * Recent Topics list may draw topics from.
 *
 * See: https://github.com/avatharbe/RecentTopics/issues/193
 */

namespace avathar\recenttopics\tests\core;

class forum_list_test extends \phpbb_test_case
{
	/**
	 * Build a recenttopics instance for get_forum_list().
	 *
	 * The db stub answers the forum_recent_topics query with every forum id it
	 * was asked about, so the result only reflects the permission and password
	 * filtering done before that query.
	 *
	 * @param array $readable_forum_ids   Forums the user holds f_read on
	 * @param array $passworded_forum_ids Passworded forums the user has not unlocked
	 * @return \avathar\recenttopics\core\recenttopics
	 */
	private function make_rt(array $readable_forum_ids, array $passworded_forum_ids)
	{
		$f_read = [];
		foreach ($readable_forum_ids as $forum_id)
		{
			$f_read[$forum_id] = ['f_read' => 1];
		}

		$auth = $this->createMock(\phpbb\auth\auth::class);
		$auth->method('acl_getf')->willReturnCallback(function ($opt) use ($f_read) {
			return $opt === 'f_read' ? $f_read : [];
		});

		$user = $this->createMock(\phpbb\user::class);
		$user->method('get_passworded_forums')
			->willReturn(array_combine($passworded_forum_ids, $passworded_forum_ids) ?: []);

		$queried = [];
		$db = $this->createMock(\phpbb\db\driver\driver_interface::class);
		$db->method('sql_in_set')->willReturnCallback(function ($field, $ids) use (&$queried) {
			$queried = array_values($ids);
			return $field . ' IN (' . implode(',', $queried) . ')';
		});
		$db->method('sql_query')->willReturn('result');
		$db->method('sql_fetchrow')->willReturnCallback(function () use (&$queried) {
			$forum_id = array_shift($queried);
			return $forum_id === null ? false : ['forum_id' => $forum_id];
		});

		return new \avathar\recenttopics\core\recenttopics(
			$auth,
			$this->createMock(\phpbb\cache\service::class),
			new \phpbb\config\config([]),
			$this->createMock(\phpbb\language\language::class),
			$this->createMock(\phpbb\content_visibility::class),
			$db,
			$this->createMock(\phpbb\event\dispatcher_interface::class),
			$this->createMock(\phpbb\pagination::class),
			$this->createMock(\phpbb\request\request_interface::class),
			$this->createMock(\phpbb\template\template::class),
			$user,
			'/',
			'php',
			$this->createMock(\phpbb\config\db_text::class)
		);
	}

	/**
	 * Run get_forum_list() and return the resulting forum ids, sorted.
	 *
	 * @param \avathar\recenttopics\core\recenttopics $rt
	 * @return array
	 */
	private function forum_ids($rt): array
	{
		$method = new \ReflectionMethod($rt, 'get_forum_list');
		$method->setAccessible(true);
		$method->invoke($rt);

		$property = new \ReflectionProperty($rt, 'forum_ids');
		$property->setAccessible(true);
		$forum_ids = array_values($property->getValue($rt));
		sort($forum_ids);

		return $forum_ids;
	}

	/**
	 * Passworded forums the user has not unlocked must not feed the list (#193).
	 * phpBB grants f_read on them regardless of the password, so the ACL check
	 * alone lets their titles and authors through.
	 */
	public function test_locked_passworded_forum_is_excluded()
	{
		$rt = $this->make_rt([1, 2, 3], [2]);

		$this->assertSame([1, 3], $this->forum_ids($rt));
	}

	/**
	 * When excluding the locked forum leaves a single forum, that forum is
	 * still the only one listed.
	 */
	public function test_locked_passworded_forum_is_excluded_when_one_forum_remains()
	{
		$rt = $this->make_rt([1, 2], [2]);

		$this->assertSame([1], $this->forum_ids($rt));
	}

	/**
	 * A forum the user has unlocked, or one without a password, is unaffected.
	 */
	public function test_forums_without_locked_password_are_kept()
	{
		$rt = $this->make_rt([1, 2, 3], []);

		$this->assertSame([1, 2, 3], $this->forum_ids($rt));
	}
}
