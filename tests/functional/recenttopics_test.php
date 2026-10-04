<?php
/**
 *
 * @package Recent Topics Extension
 * @copyright (c) 2026 Andreas Vandenberghe
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace avathar\recenttopics\tests\functional;

/**
 * @group functional
 */
class recenttopics_test extends \phpbb_functional_test_case
{
	protected static function setup_extensions()
	{
		return array('avathar/recenttopics');
	}

	// -----------------------------------------------------------------------
	// Standalone page tests  (app.php/rt and app.php/rt/simple)
	// -----------------------------------------------------------------------

	/**
	 * The dedicated RT page renders the RT wrapper and carries the correct
	 * page title when rt_page_enable is on.
	 */
	public function test_rt_page()
	{
		$this->login();

		// Enable the RT page
		$this->set_config('rt_page_enable', 1);

		$crawler = self::request('GET', 'app.php/rt?sid=' . $this->sid);

		// The anchor is static in recent_topics_page.html — always present
		$this->assertGreaterThanOrEqual(1, $crawler->filter('a#recent-topics')->count(),
			'Anchor <a id="recent-topics"> must be present on the RT page');

		// The controller passes lang('RECENT_TOPICS') as the page title
		$this->assertStringContainsString('Recent Topics', $crawler->filter('title')->text(),
			'Page <title> must contain "Recent Topics"');
	}

	/**
	 * The simple RT page (for iframe embedding) renders the same wrapper
	 * without a full page chrome.
	 */
	public function test_rt_simple_page()
	{
		$this->login();
		$this->set_config('rt_page_enable', 1);

		$crawler = self::request('GET', 'app.php/rt/simple?sid=' . $this->sid);

		// The anchor is static in recent_topics_simple.html — always present
		$this->assertGreaterThanOrEqual(1, $crawler->filter('a#recent-topics')->count(),
			'Anchor <a id="recent-topics"> must be present on the simple RT page');
	}

	/**
	 * When rt_page_enable is off the controller still renders the template
	 * skeleton, but never calls display_recent_topics(), so the topic box
	 * must not appear.
	 */
	public function test_rt_page_disabled()
	{
		$this->login();

		// Disable the RT page — should still render but without topics
		$this->set_config('rt_page_enable', 0);

		$crawler = self::request('GET', 'app.php/rt?sid=' . $this->sid);

		// The static anchor is still in the template
		$this->assertGreaterThanOrEqual(1, $crawler->filter('a#recent-topics')->count(),
			'Anchor <a id="recent-topics"> must still be present when page is disabled');

		// The topic box is inside the topics loop — nothing was loaded, so it
		// must not be present
		$this->assertSame(0, $crawler->filter('#recent-topics-box')->count(),
			'#recent-topics-box must not render when rt_page_enable is 0');
	}

	// -----------------------------------------------------------------------
	// Board index integration
	// -----------------------------------------------------------------------

	/**
	 * After creating a topic the RT block on the board index must show that
	 * topic's title.
	 */
	public function test_index_has_recent_topics()
	{
		$this->login();
		$this->set_config('rt_index', 1);

		// Create a topic so there is content to display
		$this->create_topic(2, 'RT Functional Test Topic', 'This is a test topic for recent topics.');

		$crawler = self::request('GET', 'index.php?sid=' . $this->sid);

		// The RT block anchor is present in recent_topics_body_topbottom.html
		$this->assertGreaterThanOrEqual(1, $crawler->filter('a#recent-topics')->count(),
			'Anchor <a id="recent-topics"> must be present on the board index when RT is enabled');

		// The topic box must be rendered with at least one row
		$this->assertGreaterThanOrEqual(1, $crawler->filter('#recent-topics-box')->count(),
			'#recent-topics-box must be present on the board index after a topic was created');

		// The created topic title must appear inside the RT block
		$blockText = $crawler->filter('#recent-topics-box')->text();
		$this->assertStringContainsString('RT Functional Test Topic', $blockText,
			'The created topic title must appear inside the RT block on the board index');
	}

	// -----------------------------------------------------------------------
	// UCP preferences
	// -----------------------------------------------------------------------

	/**
	 * The UCP display preferences page must include the RT-specific form
	 * fields injected by the extension.
	 */
	public function test_ucp_preferences()
	{
		$this->login();

		$crawler = self::request('GET', 'ucp.php?i=ucp_prefs&mode=view&sid=' . $this->sid);

		// The template event ucp_prefs_view_select_menu_append.html injects
		// RT radio buttons when S_RT_SHOW is true (requires u_rt_enable or
		// one of the other RT permissions — the admin account has them all)
		$this->assertGreaterThanOrEqual(1, $crawler->filter('input[name="rt_enable"]')->count(),
			'UCP display preferences must contain the RT enable radio buttons');

		// The number input is also injected when the user has u_rt_number
		$this->assertGreaterThanOrEqual(1, $crawler->filter('input[name="rt_number"]')->count(),
			'UCP display preferences must contain the RT topics-per-page input');
	}

	// -----------------------------------------------------------------------
	// ACP
	// -----------------------------------------------------------------------

	/**
	 * The "reset user preferences" action UPDATEs every row in the users
	 * table with no WHERE clause, so a forged cross-site POST would wipe the
	 * Recent Topics preferences of the whole board. It must be rejected when
	 * the request carries no valid form token.
	 */
	public function test_acp_reset_rejects_missing_form_token()
	{
		$this->login();
		$this->admin_login();

		$db = $this->get_db();

		// Give the admin a preference that differs from the board default, so
		// that a reset is detectable.
		$db->sql_query('UPDATE phpbb_users SET user_rt_number = 42 WHERE username_clean = \'admin\'');

		// Forged request: the reset button only, no creation_time/form_token.
		self::request('POST', $this->acp_module_url(), array(
			'rt_reset_default' => 'Reset',
		));

		$this->assertSame(42, $this->get_admin_rt_number(),
			'A POST without a valid form token must not reset user preferences');
	}

	/**
	 * Guard for the fix above: submitting the real ACP form, which carries a
	 * valid token, must still perform the reset.
	 */
	public function test_acp_reset_works_with_valid_form_token()
	{
		$this->login();
		$this->admin_login();

		$db = $this->get_db();
		$db->sql_query('UPDATE phpbb_users SET user_rt_number = 42 WHERE username_clean = \'admin\'');

		$crawler = self::request('GET', $this->acp_module_url());
		$form = $crawler->selectButton('rt_reset_default')->form();
		self::submit($form);

		$this->assertNotSame(42, $this->get_admin_rt_number(),
			'Submitting the genuine ACP form must still reset user preferences');
	}

	/**
	 * The ACP donate button uses the extension's bundled image, not Patreon's CDN,
	 * and that image is actually served (#200).
	 */
	public function test_acp_patreon_button_is_local()
	{
		$this->login();
		$this->admin_login();

		$crawler = self::request('GET', $this->acp_module_url());
		$src = $crawler->filter('a[href*="patreon.com"] img')->attr('src');

		$this->assertStringNotContainsString('patreon.com', $src);
		$this->assertStringEndsWith('ext/avathar/recenttopics/adm/style/images/become_a_patron_button.png', $src);

		self::request('GET', 'ext/avathar/recenttopics/adm/style/images/become_a_patron_button.png', array(), false);
		$this->assertSame(200, self::$client->getResponse()->getStatus());
	}

	// -----------------------------------------------------------------------
	// Helper
	// -----------------------------------------------------------------------

	/**
	 * URL of the Recent Topics ACP module for the current session.
	 */
	private function acp_module_url()
	{
		return 'adm/index.php?i=-avathar-recenttopics-acp-recenttopics_module&mode=recenttopics_config&sid=' . $this->sid;
	}

	/**
	 * Read the admin account's stored topics-per-page preference.
	 */
	private function get_admin_rt_number()
	{
		$db = $this->get_db();

		$result = $db->sql_query('SELECT user_rt_number FROM phpbb_users WHERE username_clean = \'admin\'');
		$value = $db->sql_fetchfield('user_rt_number');
		$db->sql_freeresult($result);

		return (int) $value;
	}

	/**
	 * Set a phpBB config value via direct SQL and flush the cache, so the
	 * running phpBB instance picks it up immediately.
	 */
	private function set_config($name, $value)
	{
		$db = $this->get_db();

		$sql = "UPDATE phpbb_config SET config_value = '" . $db->sql_escape($value) . "' WHERE config_name = '" . $db->sql_escape($name) . "'";
		$db->sql_query($sql);

		// If the row didn't exist, insert it
		if (!$db->sql_affectedrows())
		{
			$sql = "INSERT INTO phpbb_config (config_name, config_value, is_dynamic) VALUES ('" . $db->sql_escape($name) . "', '" . $db->sql_escape($value) . "', 0)";
			$db->sql_query($sql);
		}

		$this->purge_cache();
	}
}
