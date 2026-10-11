<?php
/**
 *
 * @package Recent Topics Extension
 * @copyright (c) 2015 PayBas
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Based on the original NV Recent Topics by Joas Schilling (nickvergessen)
 */

namespace avathar\recenttopics\controller;

use phpbb\config\config;
use phpbb\controller\helper;
use phpbb\db\driver\driver_interface;
use phpbb\language\language;
use phpbb\user;
use avathar\recenttopics\core\recenttopics;

/**
 * Controller for the standalone Recent Topics pages.
 *
 * Serves the two routes that show the list on a page of its own rather than as a block on the index
 * or a forum: the full page at index.php/rt, and a chrome-less variant at index.php/rt/simple meant to be
 * embedded in an iframe. Both delegate the actual list to the recenttopics service and render nothing
 * but the surrounding page when the rt_page_enable setting is off.
 */
class page_controller implements page_interface
{
	/** @var config */
	protected $config;

	/** @var driver_interface */
	protected $db;

	/** @var helper */
	protected $helper;

	/** @var language */
	protected $language;

	/** @var recenttopics */
	protected $rt_functions;

	/** @var user */
	protected $user;

	/**
	 * page_controller constructor.
	 *
	 * @param config              $config
	 * @param driver_interface    $db
	 * @param helper              $helper
	 * @param language            $language
	 * @param recenttopics        $functions
	 * @param user                $user
	 */
	public function __construct(
		config $config,
		driver_interface $db,
		helper $helper,
		language $language,
		recenttopics $functions,
		user $user
	)
	{
		$this->config       = $config;
		$this->db           = $db;
		$this->helper       = $helper;
		$this->language     = $language;
		$this->rt_functions = $functions;
		$this->user         = $user;
	}

	/**
	 * Display the page index.php/rt/ (full header/footer)
	 *
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function display()
	{
		return $this->render_page('recent_topics_page.html');
	}

	/**
	 * Display the page index.php/rt/simple (no header/footer, for iframe embedding)
	 * Forces PBWoW3 style if installed, otherwise uses the board default.
	 *
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function display_simple()
	{
		$this->force_style('pbwow3');
		return $this->render_page('recent_topics_simple.html');
	}

	/**
	 * Force a specific style by style_path, falling back to the board default.
	 *
	 * Overrides the style on the user object for this request only; nothing is written back to the
	 * user's profile. An inactive or missing style leaves the board default in place.
	 *
	 * @param  string $style_path The style directory name (e.g. 'pbwow3')
	 * @return void
	 */
	private function force_style($style_path)
	{
		$sql = 'SELECT *
			FROM ' . STYLES_TABLE . "
			WHERE style_path = '" . $this->db->sql_escape($style_path) . "'
				AND style_active = 1";
		$result = $this->db->sql_query($sql);
		$style_row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($style_row)
		{
			$this->user->style = $style_row;
		}
	}

	/**
	 * Build the recent topics list and render it into the given template.
	 *
	 * Shared by both routes; the only difference between them is the template. If rt_page_enable is
	 * off the page is still returned, just without a list, so the route never 404s once registered.
	 *
	 * @param  string $template Template file to render
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	private function render_page($template)
	{
		$this->language->add_lang(['info_acp_recenttopics', 'recenttopics'], 'avathar/recenttopics');

		if (isset($this->config['rt_page_enable']) && $this->config['rt_page_enable'])
		{
			$this->rt_functions->display_recent_topics();
		}

		return $this->helper->render($template, $this->language->lang('RECENT_TOPICS'));
	}
}
