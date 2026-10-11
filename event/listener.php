<?php
/**
 *
 * @package Recent Topics Extension
 * @copyright (c) 2015 PayBas
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Based on the original NV Recent Topics by Joas Schilling (nickvergessen)
 *
 */

namespace avathar\recenttopics\event;

use avathar\recenttopics\core\recenttopics;
use phpbb\config\config;
use phpbb\controller\helper;
use phpbb\language\language;
use phpbb\request\request;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener for the board-facing side of Recent Topics.
 *
 * Renders the topic list on the index and viewforum pages, registers the extension's u_rt_*
 * permissions, adds the per-forum "show in Recent Topics" switch to the ACP, and labels the
 * standalone Recent Topics pages on Who Is Online.
 */
class listener implements EventSubscriberInterface
{
	/* @var recenttopics */
	protected $rt_functions;

	/** @var config */
	protected $config;

	/** @var request */
	protected $request;

	/** @var helper */
	protected $helper;

	/** @var language */
	protected $language;

	/**
	 * listener constructor.
	 *
	 * @param recenttopics $functions
	 * @param config       $config
	 * @param request      $request
	 * @param helper       $helper
	 * @param language     $language
	 */
	public function __construct(recenttopics $functions, config $config, request $request, helper $helper, language $language)
	{
		$this->rt_functions = $functions;
		$this->config = $config;
		$this->request = $request;
		$this->helper = $helper;
		$this->language = $language;
	}

	/**
	 * Map the phpBB core events this listener hooks onto the methods that handle them.
	 *
	 * The last entry is not a core event but one this extension dispatches itself, so other
	 * extensions can alter a topic title before it is rendered.
	 *
	 * @return array Event name => method name
	 * @static
	 */
	public static function getSubscribedEvents()
	{
		return array(
			'core.index_modify_page_title'           => 'display_rt',
			'core.viewforum_generate_page_after'     => 'display_rt_viewforum',
			'core.viewonline_overwrite_location'     => 'viewonline_overwrite_location',
			'core.acp_manage_forums_request_data'    => 'acp_manage_forums_request_data',
			'core.acp_manage_forums_initialise_data' => 'acp_manage_forums_initialise_data',
			'core.acp_manage_forums_display_form'    => 'acp_manage_forums_display_form',
			'core.permissions'                       => 'add_permission',

			// Events added by this extension
			'avathar.recenttopics.modify_topictitle'  => 'topictitle_remove_re',
		);
	}

	/**
	 * Render the recent topics list on the board index, if enabled board-wide.
	 *
	 * @return void
	 */
	public function display_rt()
	{
		if (isset($this->config['rt_index']) && $this->config['rt_index'])
		{
			$this->rt_functions->display_recent_topics();
		}
	}

	/**
	 * Render the recent topics list on a forum page, if enabled board-wide.
	 *
	 * Passes 'viewforum' as the context so the list is placed and scoped for that page rather
	 * than reusing the index layout.
	 *
	 * @return void
	 */
	public function display_rt_viewforum()
	{
		if (isset($this->config['rt_viewforum']) && $this->config['rt_viewforum'])
		{
			$this->rt_functions->display_recent_topics('recent_topics', 'viewforum');
		}
	}

	/**
	 * Show users viewing Recent Topics on the Who Is Online page.
	 *
	 * Without this the standalone rt pages are listed as the index page; the session page is
	 * matched to name the location and link it back to the right controller.
	 *
	 * phpBB 4.0 serves routes through index.php, so the session page is index.php/rt or
	 * index.php/rt/simple, optionally followed by a query string.
	 *
	 * @param  \phpbb\event\data $event Event object; reads ['row'], writes ['location'] and ['location_url']
	 * @return void
	 */
	public function viewonline_overwrite_location($event)
	{
		if (!preg_match('#^index\.php/rt(/simple)?(?:[/?]|$)#', $event['row']['session_page'], $matches))
		{
			return;
		}

		$event['location'] = $this->language->lang('VIEWING_RECENT_TOPICS');
		$event['location_url'] = $this->helper->route(!empty($matches[1]) ? 'avathar_recenttopics_simple' : 'avathar_recenttopics_page');
	}

	/**
	 * Read the per-forum "show in Recent Topics" setting from the ACP forum add/edit form.
	 *
	 * Defaults to 1 so a forum stays included when the checkbox is absent from the submitted form.
	 *
	 * @param  \phpbb\event\data $event Event object; reads and writes ['forum_data']
	 * @return void
	 */
	public function acp_manage_forums_request_data($event)
	{
		$array = $event['forum_data'];
		$array['forum_recent_topics'] = $this->request->variable('forum_recent_topics', 1);
		$event['forum_data'] = $array;
	}

	/**
	 * Include newly created forums in Recent Topics by default.
	 *
	 * @param  \phpbb\event\data $event Event object; reads ['action'], reads and writes ['forum_data']
	 * @return void
	 */
	public function acp_manage_forums_initialise_data($event)
	{
		if ($event['action'] == 'add')
		{
			$array = $event['forum_data'];
			$array['forum_recent_topics'] = '1';
			$event['forum_data'] = $array;
		}
	}

	/**
	 * Expose the forum's current Recent Topics setting to the ACP forum form template.
	 *
	 * @param  \phpbb\event\data $event Event object; reads ['forum_data'], reads and writes ['template_data']
	 * @return void
	 */
	public function acp_manage_forums_display_form($event)
	{
		$array = $event['template_data'];
		$array['RECENT_TOPICS'] = $event['forum_data']['forum_recent_topics'];
		$event['template_data'] = $array;
	}

	/**
	 * Register the extension's u_rt_* permissions so they appear under Misc in the ACP.
	 *
	 * ucp_listener uses these to decide which preference fields a user may see and save.
	 *
	 * @param  \phpbb\event\data $event Event object; reads and writes ['permissions']
	 * @return void
	 * @access public
	 */
	public function add_permission($event)
	{
		$permissions = $event['permissions'];
		$permissions['u_rt_view'] = array('lang' => 'ACL_U_RT_VIEW', 'cat' => 'misc');
		$permissions['u_rt_enable'] = array('lang' => 'ACL_U_RT_ENABLE', 'cat' => 'misc');
		$permissions['u_rt_location'] = array('lang' => 'ACL_U_RT_LOCATION', 'cat' => 'misc');
		$permissions['u_rt_sort_start_time'] = array('lang' => 'ACL_U_RT_SORT_START_TIME', 'cat' => 'misc');
		$permissions['u_rt_unread_only'] = array('lang' => 'ACL_U_RT_UNREAD_ONLY', 'cat' => 'misc');
		$permissions['u_rt_number'] = array('lang' => 'ACL_U_RT_NUMBER', 'cat' => 'misc');
		$event['permissions'] = $permissions;
	}

	/**
	 * Remove the leading "Re: " from a topic's last-post subject.
	 *
	 * Handles this extension's own avathar.recenttopics.modify_topictitle event, so the list shows
	 * the topic title rather than the reply prefix.
	 *
	 * @param  \phpbb\event\data $event Event object; reads and writes ['row']
	 * @return void
	 * @access public
	 */
	public function topictitle_remove_re($event)
	{
		if (isset($event['row']['topic_last_post_subject']))
		{
			$array = (array) $event['row'];
			$lastpost = $array['topic_last_post_subject'];
			$array['topic_last_post_subject'] = preg_replace('/^Re: /', '', $lastpost);
			$event['row'] = $array;
		}
	}

}
