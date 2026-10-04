<?php
/**
 *
 * @package Recent Topics Extension
 * @copyright (c) 2026 Andreas Vandenberghe
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace avathar\recenttopics\tests\event;

class ucp_listener_test extends \phpbb_test_case
{
	/** @var \avathar\recenttopics\event\ucp_listener */
	protected $listener;

	/** @var \phpbb\auth\auth|\PHPUnit\Framework\MockObject\MockObject */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\request\request|\PHPUnit\Framework\MockObject\MockObject */
	protected $request;

	/** @var \phpbb\template\template|\PHPUnit\Framework\MockObject\MockObject */
	protected $template;

	/** @var \phpbb\user|\PHPUnit\Framework\MockObject\MockObject */
	protected $user;

	/** @var \phpbb\language\language|\PHPUnit\Framework\MockObject\MockObject */
	protected $language;

	/** @var \phpbb\db\driver\driver_interface|\PHPUnit\Framework\MockObject\MockObject */
	protected $db;

	public function setUp(): void
	{
		parent::setUp();

		$this->auth = $this->createMock('\phpbb\auth\auth');
		$this->config = new \phpbb\config\config(array(
			'rt_index'              => 1,
			'rt_sort_start_time'    => 0,
			'rt_unread_only'        => 0,
			'rt_location'           => 'RT_TOP',
			'rt_viewforum_location' => 'RT_TOP',
			'rt_number'             => 5,
		));
		$this->request = $this->createMock('\phpbb\request\request');
		$this->template = $this->createMock('\phpbb\template\template');
		$this->user = $this->getMockBuilder('\phpbb\user')
			->disableOriginalConstructor()
			->getMock();
		$this->language = $this->createMock('\phpbb\language\language');
		$this->db = $this->createMock('\phpbb\db\driver\driver_interface');
	}

	protected function set_listener()
	{
		$this->listener = new \avathar\recenttopics\event\ucp_listener(
			$this->auth,
			$this->config,
			$this->request,
			$this->template,
			$this->user,
			$this->language,
			$this->db
		);
	}

	public function test_getSubscribedEvents()
	{
		$this->assertEquals(array(
			'core.ucp_prefs_view_data',
			'core.ucp_prefs_view_update_data',
			'core.ucp_register_register_after',
		), array_keys(\avathar\recenttopics\event\ucp_listener::getSubscribedEvents()));
	}

	/**
	 * The registration defaults must hang off an event that fires after user_add() and
	 * carries the new user_id. core.ucp_register_data_after fires during form validation,
	 * has no user_id, and made the UPDATE hit user_id = 0 (#196).
	 */
	public function test_register_defaults_run_after_the_account_exists()
	{
		$events = \avathar\recenttopics\event\ucp_listener::getSubscribedEvents();

		$this->assertSame('ucp_register_set_data', $events['core.ucp_register_register_after'] ?? null);
		$this->assertArrayNotHasKey('core.ucp_register_data_after', $events);
	}

	/**
	 * A user holding every RT permission has all six preferences persisted.
	 */
	public function test_ucp_prefs_set_data()
	{
		$this->auth->method('acl_get')->willReturn(true);

		$this->set_listener();

		$event = new \phpbb\event\data(array(
			'data'    => array(
				'rt_enable'             => 1,
				'rt_location'           => 'RT_BOTTOM',
				'rt_viewforum_location' => 'RT_TOP',
				'rt_number'             => 10,
				'rt_sort_start_time'    => 1,
				'rt_unread_only'        => 0,
			),
			'sql_ary' => array(),
		));

		$this->listener->ucp_prefs_set_data($event);

		$this->assertEquals(1, $event['sql_ary']['user_rt_enable']);
		$this->assertEquals('RT_BOTTOM', $event['sql_ary']['user_rt_location']);
		$this->assertEquals('RT_TOP', $event['sql_ary']['user_rt_viewforum_location']);
		$this->assertEquals(10, $event['sql_ary']['user_rt_number']);
		$this->assertEquals(1, $event['sql_ary']['user_rt_sort_start_time']);
		$this->assertEquals(0, $event['sql_ary']['user_rt_unread_only']);
	}

	/**
	 * ucp_prefs_get_data() decides which fields to render from the
	 * per-preference ACLs, so the write path must apply the same gate.
	 * Otherwise a user can POST a field they were never shown and have it
	 * persisted.
	 */
	public function test_ucp_prefs_set_data_respects_acls()
	{
		// Only two of the five preferences are permitted.
		$this->auth->method('acl_get')
			->willReturnCallback(function ($perm) {
				return ($perm === 'u_rt_enable' || $perm === 'u_rt_number');
			});

		$this->set_listener();

		$event = new \phpbb\event\data(array(
			'data'    => array(
				'rt_enable'             => 1,
				'rt_location'           => 'RT_BOTTOM',
				'rt_viewforum_location' => 'RT_TOP',
				'rt_number'             => 10,
				'rt_sort_start_time'    => 1,
				'rt_unread_only'        => 0,
			),
			'sql_ary' => array(),
		));

		$this->listener->ucp_prefs_set_data($event);

		$this->assertEquals(1, $event['sql_ary']['user_rt_enable']);
		$this->assertEquals(10, $event['sql_ary']['user_rt_number']);

		$this->assertArrayNotHasKey('user_rt_location', $event['sql_ary'],
			'user_rt_location must not be written without u_rt_location');
		$this->assertArrayNotHasKey('user_rt_viewforum_location', $event['sql_ary'],
			'user_rt_viewforum_location must not be written without u_rt_location');
		$this->assertArrayNotHasKey('user_rt_sort_start_time', $event['sql_ary'],
			'user_rt_sort_start_time must not be written without u_rt_sort_start_time');
		$this->assertArrayNotHasKey('user_rt_unread_only', $event['sql_ary'],
			'user_rt_unread_only must not be written without u_rt_unread_only');
	}

	/**
	 * A user with none of the RT permissions must not have any RT column
	 * written, and must not disturb sql_ary entries put there by core or by
	 * other extensions.
	 */
	public function test_ucp_prefs_set_data_no_permissions()
	{
		$this->auth->method('acl_get')->willReturn(false);

		$this->set_listener();

		$event = new \phpbb\event\data(array(
			'data'    => array(
				'rt_enable'             => 1,
				'rt_location'           => 'RT_BOTTOM',
				'rt_viewforum_location' => 'RT_TOP',
				'rt_number'             => 10,
				'rt_sort_start_time'    => 1,
				'rt_unread_only'        => 0,
			),
			'sql_ary' => array('user_style' => 2),
		));

		$this->listener->ucp_prefs_set_data($event);

		$this->assertSame(array('user_style' => 2), $event['sql_ary'],
			'No RT column may be written without the matching permission');
	}

	public function test_ucp_prefs_get_data_no_submit()
	{
		$this->user->data = array(
			'user_rt_enable'             => 1,
			'user_rt_location'           => 'RT_TOP',
			'user_rt_viewforum_location' => 'RT_TOP',
			'user_rt_number'             => 5,
			'user_rt_sort_start_time'    => 0,
			'user_rt_unread_only'        => 0,
		);

		$this->request->method('variable')
			->willReturnCallback(function ($var, $default) {
				return $default;
			});

		$this->auth->method('acl_get')
			->willReturnCallback(function ($perm) {
				return ($perm === 'u_rt_view' || $perm === 'u_rt_enable');
			});

		$this->language->expects($this->once())
			->method('add_lang')
			->with('recenttopics_ucp', 'avathar/recenttopics');

		$this->template->expects($this->once())
			->method('assign_vars');

		$this->set_listener();

		$event = new \phpbb\event\data(array(
			'data'   => array(),
			'submit' => false,
		));

		$this->listener->ucp_prefs_get_data($event);

		$this->assertEquals(1, $event['data']['rt_enable']);
		$this->assertEquals('RT_TOP', $event['data']['rt_location']);
		$this->assertEquals(5, $event['data']['rt_number']);
	}

	/**
	 * Submitted location / number preferences, and what must reach $event['data'] (#198).
	 * Stored user values: location RT_BOTTOM, viewforum location RT_TOP, number 5.
	 */
	public function submitted_preferences_data()
	{
		return array(
			'valid values pass through'           => array('RT_SIDE', 'RT_BOTTOM', 20, 'RT_SIDE', 'RT_BOTTOM', 20),
			'unknown location keeps stored value' => array('RT_EVIL', 'RT_TOP', 5, 'RT_BOTTOM', 'RT_TOP', 5),
			'side is not a viewforum location'    => array('RT_TOP', 'RT_SIDE', 5, 'RT_TOP', 'RT_TOP', 5),
			'number above 999 is clamped'         => array('RT_TOP', 'RT_TOP', 100000, 'RT_TOP', 'RT_TOP', 999),
			'zero is raised to 1'                 => array('RT_TOP', 'RT_TOP', 0, 'RT_TOP', 'RT_TOP', 1),
			'negative number is raised to 1'      => array('RT_TOP', 'RT_TOP', -5, 'RT_TOP', 'RT_TOP', 1),
		);
	}

	/**
	 * @dataProvider submitted_preferences_data
	 */
	public function test_submitted_preferences_are_validated($location, $vf_location, $number, $expected_location, $expected_vf_location, $expected_number)
	{
		$this->user->data = array(
			'user_rt_enable'             => 1,
			'user_rt_location'           => 'RT_BOTTOM',
			'user_rt_viewforum_location' => 'RT_TOP',
			'user_rt_number'             => 5,
			'user_rt_sort_start_time'    => 0,
			'user_rt_unread_only'        => 0,
		);

		$submitted = array('rt_location' => $location, 'rt_viewforum_location' => $vf_location, 'rt_number' => $number);
		$this->request->method('variable')
			->willReturnCallback(function ($var, $default) use ($submitted) {
				return $submitted[$var] ?? $default;
			});

		$this->set_listener();

		$event = new \phpbb\event\data(array('data' => array(), 'submit' => true));
		$this->listener->ucp_prefs_get_data($event);

		$this->assertSame($expected_location, $event['data']['rt_location']);
		$this->assertSame($expected_vf_location, $event['data']['rt_viewforum_location']);
		$this->assertSame($expected_number, $event['data']['rt_number']);
	}

	/**
	 * A stored location that is itself invalid falls back to the board default (#198).
	 */
	public function test_invalid_stored_location_falls_back_to_board_default()
	{
		$this->config['rt_location'] = 'RT_SIDE';
		$this->user->data = array(
			'user_rt_enable'             => 1,
			'user_rt_location'           => 'GARBAGE',
			'user_rt_viewforum_location' => 'RT_TOP',
			'user_rt_number'             => 5,
			'user_rt_sort_start_time'    => 0,
			'user_rt_unread_only'        => 0,
		);

		$this->request->method('variable')
			->willReturnCallback(function ($var, $default) {
				return $var === 'rt_location' ? 'RT_EVIL' : $default;
			});

		$this->set_listener();

		$event = new \phpbb\event\data(array('data' => array(), 'submit' => true));
		$this->listener->ucp_prefs_get_data($event);

		$this->assertSame('RT_SIDE', $event['data']['rt_location']);
	}

	public function test_ucp_prefs_get_data_on_submit()
	{
		$this->user->data = array(
			'user_rt_enable'             => 1,
			'user_rt_location'           => 'RT_TOP',
			'user_rt_viewforum_location' => 'RT_TOP',
			'user_rt_number'             => 5,
			'user_rt_sort_start_time'    => 0,
			'user_rt_unread_only'        => 0,
		);

		$this->request->method('variable')
			->willReturnCallback(function ($var, $default) {
				return $default;
			});

		// On submit, template should not be touched
		$this->template->expects($this->never())
			->method('assign_vars');

		$this->set_listener();

		$event = new \phpbb\event\data(array(
			'data'   => array(),
			'submit' => true,
		));

		$this->listener->ucp_prefs_get_data($event);

		// Data should still be merged
		$this->assertArrayHasKey('rt_enable', $event['data']);
	}

	public function test_ucp_register_set_data()
	{
		// Verify the SQL query is built and executed
		$this->db->expects($this->once())
			->method('sql_build_array')
			->with('UPDATE', $this->callback(function ($sql_ary) {
				return $sql_ary['user_rt_enable'] === 1
					&& $sql_ary['user_rt_location'] === 'RT_TOP'
					&& $sql_ary['user_rt_viewforum_location'] === 'RT_TOP'
					&& $sql_ary['user_rt_number'] === 5
					&& $sql_ary['user_rt_sort_start_time'] === 0
					&& $sql_ary['user_rt_unread_only'] === 0;
			}))
			->willReturn("user_rt_enable = 1");

		$this->db->expects($this->once())
			->method('sql_query')
			->with($this->stringContains('WHERE user_id = 3'));

		$this->set_listener();

		$event = new \phpbb\event\data(array(
			'user_id' => 3,
		));

		$this->listener->ucp_register_set_data($event);
	}
}
