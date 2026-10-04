<?php
/**
 *
 * @package Recent Topics Extension
 * @copyright (c) 2026 Andreas Vandenberghe
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Tests for the ACP "Show announcements first" setting (rt_announcements_first).
 *
 * See: https://github.com/avatharbe/RecentTopics/issues/201
 */

// fill_template() calls these as unqualified global functions. In CI the test bootstrap
// loads phpBB's real ones, so the globals they read are set in make_rt() as well.
namespace
{
	if (!function_exists('censor_text'))
	{
		function censor_text($text) { return $text; }
	}
	if (!function_exists('topic_status'))
	{
		function topic_status($row, $replies, $unread_topic, &$folder_img, &$folder_alt, &$topic_type)
		{
			$folder_img = 'folder';
			$folder_alt = 'TOPIC_READ';
			$topic_type = '';
		}
	}
	if (!function_exists('append_sid'))
	{
		function append_sid($url, $params = false, $is_amp = true, $session_id = false)
		{
			return $url . ($params ? '?' . $params : '');
		}
	}
	if (!function_exists('get_username_string'))
	{
		function get_username_string($mode, $user_id, $username, $user_colour = '', $custom_profile_url = false)
		{
			return $username;
		}
	}
	if (!function_exists('get_forum_parents'))
	{
		function get_forum_parents($row) { return []; }
	}
}

namespace avathar\recenttopics\tests\core
{

class announcements_first_test extends \phpbb_test_case
{
	/** @var array Topic ids in the order they reached assign_block_vars() */
	private $rendered = [];

	/**
	 * A page of four topics as get_topics_sql() returns them, newest first:
	 * 1 normal, 2 announcement, 3 sticky, 4 global announcement.
	 */
	private function page_rows(): array
	{
		$rows = [];
		foreach ([1 => POST_NORMAL, 2 => POST_ANNOUNCE, 3 => POST_STICKY, 4 => POST_GLOBAL] as $topic_id => $type)
		{
			$rows[] = [
				'topic_id' => $topic_id, 'forum_id' => 1, 'topic_type' => $type, 'topic_status' => ITEM_UNLOCKED,
				'topic_moved_id' => 0, 'topic_visibility' => ITEM_APPROVED, 'topic_posts_unapproved' => 0,
				'topic_reported' => 0, 'topic_attachment' => 0, 'topic_last_post_time' => 1700000000 - $topic_id,
				'topic_last_view_time' => 1700000000, 'topic_time' => 1699990000 - $topic_id, 'topic_last_post_id' => 99,
				'topic_last_post_subject' => 'Reply', 'topic_title' => "Topic $topic_id", 'topic_views' => 10,
				'icon_id' => 0, 'poll_start' => 0, 'topic_posted' => 0, 'topic_poster' => 2,
				'topic_first_poster_name' => 'testuser', 'topic_first_poster_colour' => '',
				'topic_last_poster_id' => 2, 'topic_last_poster_name' => 'testuser',
				'topic_last_poster_colour' => '', 'forum_name' => 'General',
			];
		}
		return $rows;
	}

	/**
	 * @param int $announcements_first Value of the ACP setting
	 * @return \avathar\recenttopics\core\recenttopics
	 */
	private function make_rt(int $announcements_first)
	{
		$rows = $this->page_rows();
		$db = $this->createMock(\phpbb\db\driver\driver_interface::class);
		$db->method('sql_in_set')->willReturn('1=1');
		$db->method('sql_build_query')->willReturn('SELECT 1');
		$db->method('sql_query')->willReturn('result');
		$db->method('sql_query_limit')->willReturn('result');
		$db->method('sql_fetchrow')->willReturnCallback(function () use (&$rows) {
			return array_shift($rows) ?? false;
		});

		$this->rendered = [];
		$template = $this->createMock(\phpbb\template\template::class);
		$template->method('assign_block_vars')->willReturnCallback(function ($loop, $vars) {
			if ($loop === 'recent_topics')
			{
				$this->rendered[] = $vars['TOPIC_ID'];
			}
		});

		$content_visibility = $this->createMock(\phpbb\content_visibility::class);
		$content_visibility->method('get_count')->willReturn(1);

		$user = $this->createMock(\phpbb\user::class);
		$user->data = ['user_id' => 2, 'is_registered' => true];
		$user->page = ['query_string' => '', 'page_name' => 'index.php'];
		$user->session_id = 'testsession';
		$user->method('format_date')->willReturn('01 Jan 2026');
		$user->method('img')->willReturn('');

		$dispatcher = new \phpbb\event\dispatcher();

		$rt = new \avathar\recenttopics\core\recenttopics(
			$this->createMock(\phpbb\auth\auth::class),
			$this->createMock(\phpbb\cache\service::class),
			new \phpbb\config\config(['posts_per_page' => 10, 'rt_topic_link_to' => 0, 'rt_announcements_first' => $announcements_first]),
			$this->createMock(\phpbb\language\language::class),
			$content_visibility,
			$db,
			$dispatcher,
			$this->createMock(\phpbb\pagination::class),
			$this->createMock(\phpbb\request\request_interface::class),
			$template,
			$user,
			'/',
			'php',
			$this->createMock(\phpbb\config\db_text::class)
		);

		foreach ([
			'topic_list' => [1, 2, 3, 4], 'sort_topics' => 'topic_last_post_time', 'display_parent_forums' => false,
			'topics_per_page' => 10, 'unread_only' => false, 'icons' => [], 'rtstart' => 0, 'total_topics_limit' => 100,
		] as $name => $value)
		{
			$property = new \ReflectionProperty($rt, $name);
			$property->setAccessible(true);
			$property->setValue($rt, $value);
		}

		// phpBB's real censor_text() (loaded by the CI bootstrap) reads global $config, $user and $auth.
		$GLOBALS['phpbb_dispatcher'] = $dispatcher;
		$GLOBALS['config'] = ['allow_smilies' => 0, 'allow_nocensors' => 1];
		$GLOBALS['user'] = $user;
		$auth_global = $this->createMock(\phpbb\auth\auth::class);
		$auth_global->method('acl_get')->willReturn(true);
		$GLOBALS['auth'] = $auth_global;

		return $rt;
	}

	private function render($rt): array
	{
		$method = new \ReflectionMethod($rt, 'fill_template');
		$method->setAccessible(true);
		$method->invoke($rt, 'recent_topics', [], 4);
		return $this->rendered;
	}

	/**
	 * Setting on: the page's announcement and global announcement come first, still newest
	 * first among themselves; the rest keep their order and stickies are not moved up (#201).
	 */
	public function test_announcements_lead_the_page_when_setting_is_on()
	{
		$this->assertSame([2, 4, 1, 3], $this->render($this->make_rt(1)));
	}

	/**
	 * Setting off (the default): the page keeps its time order.
	 */
	public function test_page_keeps_time_order_when_setting_is_off()
	{
		$this->assertSame([1, 2, 3, 4], $this->render($this->make_rt(0)));
	}
}
}
