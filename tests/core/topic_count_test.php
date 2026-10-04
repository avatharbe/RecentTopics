<?php
/**
 *
 * @package Recent Topics Extension
 * @copyright (c) 2026 Andreas Vandenberghe
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Tests for the "Show all recent topic pages" count in
 * recenttopics::display_recent_topics().
 *
 * See: https://github.com/avatharbe/RecentTopics/issues/195
 */

// display_recent_topics() includes functions_display.php unless topic_status()
// already exists; the stub keeps the test independent of a board install.
namespace
{
	if (!function_exists('topic_status'))
	{
		function topic_status($row, $replies, $unread_topic, &$folder_img, &$folder_alt, &$topic_type)
		{
			$folder_img = 'folder';
			$folder_alt = 'TOPIC_READ';
			$topic_type = '';
		}
	}
}

namespace avathar\recenttopics\tests\core
{

class topic_count_test extends \phpbb_test_case
{
	/**
	 * With "Show all recent topic pages" on, every topic query, including the
	 * count that sets the page limit, must be scoped to the user's forum list
	 * (#195). Before the fix the count ran before get_forum_list() and got null,
	 * so it only counted forums where the user has m_approve.
	 */
	public function test_page_count_query_uses_forum_list()
	{
		$visibility_forum_ids = [];
		$content_visibility = $this->createMock(\phpbb\content_visibility::class);
		$content_visibility->method('get_forums_visibility_sql')
			->willReturnCallback(function ($mode, $forum_ids) use (&$visibility_forum_ids) {
				$visibility_forum_ids[] = $forum_ids;
				return '1=1';
			});

		$auth = $this->createMock(\phpbb\auth\auth::class);
		$auth->method('acl_get')->willReturnCallback(function ($opt) {
			return $opt === 'u_rt_view';
		});
		$auth->method('acl_getf')->willReturnCallback(function ($opt) {
			return $opt === 'f_read' ? [1 => ['f_read' => 1], 2 => ['f_read' => 1]] : [];
		});

		$user = $this->createMock(\phpbb\user::class);
		$user->data = ['user_id' => 2, 'user_rt_enable' => 1, 'is_registered' => true];
		$user->method('get_passworded_forums')->willReturn([]);

		$queried = [];
		$db = $this->createMock(\phpbb\db\driver\driver_interface::class);
		$db->method('sql_in_set')->willReturnCallback(function ($field, $ids) use (&$queried) {
			if ($field === 'forum_id')
			{
				$queried = array_values($ids);
			}
			return '1=1';
		});
		$db->method('sql_build_query')->willReturn('SELECT 1');
		$db->method('sql_query')->willReturn('result');
		$db->method('sql_query_limit')->willReturn('result');
		$db->method('sql_fetchfield')->willReturn(0);
		$db->method('sql_fetchrow')->willReturnCallback(function () use (&$queried) {
			$forum_id = array_shift($queried);
			return $forum_id === null ? false : ['forum_id' => $forum_id];
		});

		$rt = new \avathar\recenttopics\core\recenttopics(
			$auth,
			$this->createMock(\phpbb\cache\service::class),
			new \phpbb\config\config([
				'rt_parents'         => 0,
				'rt_location'        => 'RT_TOP',
				'rt_unread_only'     => 0,
				'rt_number'          => 5,
				'rt_anti_topics'     => '0',
				'rt_min_topic_level' => 0,
				'rt_page_number'     => 1,
				'rt_page_numbermax'  => 0,
				'rt_sort_start_time' => 0,
			]),
			$this->createMock(\phpbb\language\language::class),
			$content_visibility,
			$db,
			new \phpbb\event\dispatcher(),
			$this->createMock(\phpbb\pagination::class),
			$this->createMock(\phpbb\request\request_interface::class),
			$this->createMock(\phpbb\template\template::class),
			$user,
			'/',
			'php',
			$this->createMock(\phpbb\config\db_text::class)
		);

		$rt->display_recent_topics();

		$this->assertNotEmpty($visibility_forum_ids, 'no topic query was built');
		foreach ($visibility_forum_ids as $forum_ids)
		{
			$forum_ids = array_values((array) $forum_ids);
			sort($forum_ids);
			$this->assertSame([1, 2], $forum_ids);
		}
	}
}
}
