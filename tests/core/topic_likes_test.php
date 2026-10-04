<?php
/**
 *
 * @package Recent Topics Extension
 * @copyright (c) 2026 Andreas Vandenberghe
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Tests for the ACP "Show like counts" setting (rt_show_likes).
 *
 * See: https://github.com/avatharbe/RecentTopics/issues/197
 */

// fill_template() calls these as unqualified global functions.
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

class topic_likes_test extends \phpbb_test_case
{
	/** @var object Post Love stand-in that records calls; the real service is optional */
	private $likes_service;

	/** @var array Template vars passed to assign_block_vars() for the topic row */
	private $assigned = [];

	/**
	 * Build a recenttopics instance ready for fill_template(), with one topic (42)
	 * that the Post Love stand-in reports 7 likes for.
	 *
	 * @param int $rt_show_likes Value of the ACP "Show like counts" setting
	 * @return \avathar\recenttopics\core\recenttopics
	 */
	private function make_rt(int $rt_show_likes)
	{
		$this->likes_service = new class {
			public $calls = 0;
			public function get_topic_like_counts(array $topic_ids)
			{
				$this->calls++;
				return [42 => 7];
			}
		};

		$row = [
			'topic_id' => 42, 'forum_id' => 1, 'topic_type' => POST_NORMAL, 'topic_status' => ITEM_UNLOCKED,
			'topic_moved_id' => 0, 'topic_visibility' => ITEM_APPROVED, 'topic_posts_unapproved' => 0,
			'topic_reported' => 0, 'topic_attachment' => 0, 'topic_last_post_time' => 1700000000,
			'topic_last_view_time' => 1700000000, 'topic_time' => 1699990000, 'topic_last_post_id' => 99,
			'topic_last_post_subject' => 'Test reply', 'topic_title' => 'Test topic', 'topic_views' => 10,
			'icon_id' => 0, 'poll_start' => 0, 'topic_posted' => 0, 'topic_poster' => 2,
			'topic_first_poster_name' => 'testuser', 'topic_first_poster_colour' => '',
			'topic_last_poster_id' => 2, 'topic_last_poster_name' => 'testuser',
			'topic_last_poster_colour' => '', 'forum_name' => 'General',
		];
		$db = $this->createMock(\phpbb\db\driver\driver_interface::class);
		$db->method('sql_in_set')->willReturn('1=1');
		$db->method('sql_build_query')->willReturn('SELECT 1');
		$db->method('sql_query_limit')->willReturn('result');
		$db->method('sql_fetchrow')->willReturnOnConsecutiveCalls($row, false);

		$template = $this->createMock(\phpbb\template\template::class);
		$template->method('assign_block_vars')->willReturnCallback(function ($loop, $vars) {
			$this->assigned = $vars;
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
			new \phpbb\config\config(['posts_per_page' => 10, 'rt_topic_link_to' => 0, 'rt_show_likes' => $rt_show_likes]),
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
			$this->createMock(\phpbb\config\db_text::class),
			null,
			$this->likes_service
		);

		foreach ([
			'topic_list' => [42], 'sort_topics' => 'topic_last_post_time', 'display_parent_forums' => false,
			'topics_per_page' => 5, 'unread_only' => false, 'icons' => [], 'rtstart' => 0, 'total_topics_limit' => 100,
		] as $name => $value)
		{
			$property = new \ReflectionProperty($rt, $name);
			$property->setAccessible(true);
			$property->setValue($rt, $value);
		}

		// phpBB's real censor_text() (loaded by the test bootstrap) reads global $config, $user and
		// $auth. allow_nocensors = 1 with acl_get() = true leaves the text untouched.
		$GLOBALS['phpbb_dispatcher'] = $dispatcher;
		$GLOBALS['config'] = ['allow_smilies' => 0, 'allow_nocensors' => 1];
		$GLOBALS['user'] = $user;
		$auth_global = $this->createMock(\phpbb\auth\auth::class);
		$auth_global->method('acl_get')->willReturn(true);
		$GLOBALS['auth'] = $auth_global;

		return $rt;
	}

	private function call_private($rt, string $method, array $args = [])
	{
		$ref = new \ReflectionMethod($rt, $method);
		$ref->setAccessible(true);
		return $ref->invokeArgs($rt, $args);
	}

	/**
	 * "Show like counts" off: no like counts, and Post Love is not queried (#197).
	 */
	public function test_like_counts_hidden_when_setting_is_off()
	{
		$rt = $this->make_rt(0);

		$this->call_private($rt, 'fill_template', ['recent_topics', [], 1]);

		$this->assertSame(0, $this->assigned['TOPIC_LIKES']);
		$this->assertSame(0, $this->likes_service->calls);
		$this->assertFalse($this->call_private($rt, 'show_likes'));
	}

	/**
	 * "Show like counts" on and Post Love available: the topic's like count is shown.
	 */
	public function test_like_counts_shown_when_setting_is_on()
	{
		$rt = $this->make_rt(1);

		$this->call_private($rt, 'fill_template', ['recent_topics', [], 1]);

		$this->assertSame(7, $this->assigned['TOPIC_LIKES']);
		$this->assertSame(1, $this->likes_service->calls);
		$this->assertTrue($this->call_private($rt, 'show_likes'));
	}
}
}
