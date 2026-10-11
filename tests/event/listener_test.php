<?php
/**
 *
 * @package Recent Topics Extension
 * @copyright (c) 2026 Andreas Vandenberghe
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace avathar\recenttopics\tests\event;

class listener_test extends \phpbb_test_case
{
	/** @var \avathar\recenttopics\event\listener */
	protected $listener;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\request\request|\PHPUnit\Framework\MockObject\MockObject */
	protected $request;

	/** @var \phpbb\controller\helper|\PHPUnit\Framework\MockObject\MockObject */
	protected $helper;

	/** @var \phpbb\language\language|\PHPUnit\Framework\MockObject\MockObject */
	protected $language;

	/** @var \avathar\recenttopics\core\recenttopics|\PHPUnit\Framework\MockObject\MockObject */
	protected $rt_functions;

	public function setUp(): void
	{
		parent::setUp();

		$this->config = new \phpbb\config\config(array(
			'rt_index' => 1,
		));

		$this->request = $this->createMock('\phpbb\request\request');
		$this->helper = $this->createMock('\phpbb\controller\helper');
		$this->language = $this->createMock('\phpbb\language\language');
		$this->rt_functions = $this->getMockBuilder('\avathar\recenttopics\core\recenttopics')
			->disableOriginalConstructor()
			->getMock();
	}

	protected function set_listener()
	{
		$this->listener = new \avathar\recenttopics\event\listener(
			$this->rt_functions,
			$this->config,
			$this->request,
			$this->helper,
			$this->language
		);
	}

	public function test_getSubscribedEvents()
	{
		$this->assertEquals(array(
			'core.index_modify_page_title',
			'core.viewforum_generate_page_after',
			'core.viewonline_overwrite_location',
			'core.acp_manage_forums_request_data',
			'core.acp_manage_forums_initialise_data',
			'core.acp_manage_forums_display_form',
			'core.permissions',
			'avathar.recenttopics.modify_topictitle',
		), array_keys(\avathar\recenttopics\event\listener::getSubscribedEvents()));
	}

	public function test_display_rt_enabled()
	{
		$this->config['rt_index'] = 1;
		$this->rt_functions->expects($this->once())
			->method('display_recent_topics');

		$this->set_listener();
		$this->listener->display_rt();
	}

	public function test_display_rt_disabled()
	{
		$this->config['rt_index'] = 0;
		$this->rt_functions->expects($this->never())
			->method('display_recent_topics');

		$this->set_listener();
		$this->listener->display_rt();
	}

	public static function viewonline_data()
	{
		return array(
			'rt_page' => array(
				array(1, 'index'),
				array('session_page' => 'index.php/rt'),
				'VIEWING_RECENT_TOPICS',
				'avathar_recenttopics_page',
			),
			'rt_page_query' => array(
				array(1, 'index'),
				array('session_page' => 'index.php/rt?start=10'),
				'VIEWING_RECENT_TOPICS',
				'avathar_recenttopics_page',
			),
			'rt_simple' => array(
				array(1, 'index'),
				array('session_page' => 'index.php/rt/simple'),
				'VIEWING_RECENT_TOPICS',
				'avathar_recenttopics_simple',
			),
			'other_page' => array(
				array(1, 'index'),
				array('session_page' => 'index.php/other'),
				null,
				null,
			),
			'other_route_starting_with_rt' => array(
				array(1, 'index'),
				array('session_page' => 'index.php/rtfoo'),
				null,
				null,
			),
			'not_app' => array(
				array(1, 'viewtopic'),
				array('session_page' => 'viewtopic.php?t=1'),
				null,
				null,
			),
		);
	}

	/**
	 * @dataProvider viewonline_data
	 */
	public function test_viewonline_overwrite_location($on_page, $row, $expected_lang, $expected_route)
	{
		$this->set_listener();

		$location = '';
		$location_url = '';

		if ($expected_lang !== null)
		{
			$this->language->expects($this->once())
				->method('lang')
				->with($expected_lang)
				->willReturn('Recent Topics');

			$this->helper->expects($this->once())
				->method('route')
				->with($expected_route)
				->willReturn('/rt');
		}

		$event = new \phpbb\event\data(array(
			'on_page'      => $on_page,
			'row'          => $row,
			'location'     => $location,
			'location_url' => $location_url,
		));

		$this->listener->viewonline_overwrite_location($event);

		if ($expected_lang !== null)
		{
			$this->assertEquals('Recent Topics', $event['location']);
			$this->assertEquals('/rt', $event['location_url']);
		}
		else
		{
			$this->assertEquals('', $event['location']);
			$this->assertEquals('', $event['location_url']);
		}
	}

	public function test_acp_manage_forums_request_data()
	{
		$this->request->expects($this->once())
			->method('variable')
			->with('forum_recent_topics', 1)
			->willReturn(0);

		$this->set_listener();

		$event = new \phpbb\event\data(array(
			'forum_data' => array('forum_id' => 1),
		));

		$this->listener->acp_manage_forums_request_data($event);

		$this->assertEquals(0, $event['forum_data']['forum_recent_topics']);
	}

	public function test_acp_manage_forums_initialise_data_add()
	{
		$this->set_listener();

		$event = new \phpbb\event\data(array(
			'action'     => 'add',
			'forum_data' => array(),
		));

		$this->listener->acp_manage_forums_initialise_data($event);

		$this->assertEquals('1', $event['forum_data']['forum_recent_topics']);
	}

	public function test_acp_manage_forums_initialise_data_edit()
	{
		$this->set_listener();

		$event = new \phpbb\event\data(array(
			'action'     => 'edit',
			'forum_data' => array(),
		));

		$this->listener->acp_manage_forums_initialise_data($event);

		$this->assertArrayNotHasKey('forum_recent_topics', $event['forum_data']);
	}

	public function test_acp_manage_forums_display_form()
	{
		$this->set_listener();

		$event = new \phpbb\event\data(array(
			'forum_data'    => array('forum_recent_topics' => 1),
			'template_data' => array(),
		));

		$this->listener->acp_manage_forums_display_form($event);

		$this->assertEquals(1, $event['template_data']['RECENT_TOPICS']);
	}

	public function test_add_permission()
	{
		$this->set_listener();

		$event = new \phpbb\event\data(array(
			'permissions' => array(),
		));

		$this->listener->add_permission($event);

		$permissions = $event['permissions'];
		$this->assertArrayHasKey('u_rt_view', $permissions);
		$this->assertArrayHasKey('u_rt_enable', $permissions);
		$this->assertArrayHasKey('u_rt_location', $permissions);
		$this->assertArrayHasKey('u_rt_sort_start_time', $permissions);
		$this->assertArrayHasKey('u_rt_unread_only', $permissions);
		$this->assertArrayHasKey('u_rt_number', $permissions);

		$this->assertEquals('misc', $permissions['u_rt_view']['cat']);
	}

	public static function topictitle_remove_re_data()
	{
		return array(
			'with_re_prefix' => array(
				array('topic_last_post_subject' => 'Re: Test topic'),
				'Test topic',
			),
			'without_re_prefix' => array(
				array('topic_last_post_subject' => 'Test topic'),
				'Test topic',
			),
			'no_subject_key' => array(
				array('some_other_key' => 'value'),
				null,
			),
		);
	}

	/**
	 * @dataProvider topictitle_remove_re_data
	 */
	public function test_topictitle_remove_re($row, $expected)
	{
		$this->set_listener();

		$event = new \phpbb\event\data(array(
			'row' => $row,
		));

		$this->listener->topictitle_remove_re($event);

		if ($expected !== null)
		{
			$this->assertEquals($expected, $event['row']['topic_last_post_subject']);
		}
		else
		{
			$this->assertArrayNotHasKey('topic_last_post_subject', $event['row']);
		}
	}
}
