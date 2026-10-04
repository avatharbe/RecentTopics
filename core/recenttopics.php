<?php
/**
 *
 * @package Recent Topics Extension
 * @copyright (c) 2015 PayBas
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Based on the original NV Recent Topics by Joas Schilling (nickvergessen)
 */

namespace avathar\recenttopics\core;

use phpbb\auth\auth;
use phpbb\cache\service as cache_service;
use phpbb\config\config;
use phpbb\config\db_text;
use phpbb\content_visibility;
use phpbb\db\driver\driver_interface;
use phpbb\event\dispatcher_interface;
use phpbb\language\language;
use phpbb\pagination;
use phpbb\request\request_interface;
use phpbb\template\template;
use phpbb\user;

/**
 * Builds and renders the recent topics list.
 *
 * The one worker behind every entry point — the index and viewforum listeners and the standalone
 * rt controllers all call display_recent_topics(). Board config supplies the defaults, and the
 * user_rt_* preferences override them wherever the matching u_rt_* permission is held.
 *
 * The pipeline is: resolve the effective settings, narrow the forums to those the user may read and
 * that are not excluded from Recent Topics, select the topic ids, then pull each topic's data and
 * assign it to the template. Several steps dispatch events so other extensions can alter the SQL,
 * the topic list, or the per-topic template array.
 *
 * @package avathar\recenttopics\core
 */
class recenttopics
{
	/**
	* @var auth
	*/
	protected $auth;

	/**
	* @var config
	*/
	protected $config;

	/**
	* @var db_text
	*/
	protected $config_text;

	/**
	 * @var language
	 */
	protected $language;

	/**
	* @var cache_service
	*/
	protected $cache;

	/**
	* @var content_visibility
	*/
	protected $content_visibility;

	/**
	* @var driver_interface
	*/
	protected $db;

	/**
	* @var dispatcher_interface
	*/
	protected $dispatcher;

	/**
	* @var pagination
	*/
	protected $pagination;

	/**
	* @var request_interface
	*/
	protected $request;

	/**
	* @var template
	*/
	protected $template;

	/**
	* @var user
	*/
	protected $user;

	/**
	* @var string phpBB root path
	*/
	protected $root_path;

	/**
	* @var string PHP extension
	*/
	protected $phpEx;

	/**
	* array of allowable forum id's
	*
	* @var array
	*/
	private $forum_ids;

	/**
	* array of topics to show
	*
	* @var array
	*/
	private $topic_list;

	/**
	* show unread topics only ?
	*
	* @var boolean
	*/
	private $unread_only;

	/**
	* show a forum icon ?
	*
	* @var boolean
	*/
	private $obtain_icons;

	/**
	* forum objects we need
	*
	* @var array
	*/
	private $forums;

	/**
	 *
	 * @var Collapsable
	 */
	private $collapsable_categories;

	/**
	 * @var \avathar\postlove\service\topic_likes|null
	 */
	private $topic_likes_service;

	/**
	 * @var int
	 */
	private $rtstart;

	/**
	 * @var int
	 */
	private $topics_per_page;

	/**
	 * @var int
	 */
	private $total_topics_limit;

	/**
	 * @var int
	 */
	private $sort_topics;

	/**
	 * @var int
	 */
	private $display_parent_forums;

	/**
	 * Block location
	 * @var string
	 */
	private $location;

	/**
	 *
	 * @var int
	 */
	private $icons;

	/**
	 * @var string
	 */
	private $excluded_topics;

	/**
	 * recenttopics constructor.
	 *
	 * @param auth                                                $auth
	 * @param cache_service                                       $cache
	 * @param config                                              $config
	 * @param language                                            $language
	 * @param content_visibility                                  $content_visibility
	 * @param driver_interface                                    $db
	 * @param dispatcher_interface                                $dispatcher
	 * @param pagination                                          $pagination
	 * @param request_interface                                   $request
	 * @param template                                            $template
	 * @param user                                                $user
	 * @param string                                              $root_path
	 * @param string                                              $phpEx
	 * @param db_text                                             $config_text
	 * @param \phpbb\collapsiblecategories\operator\operator|NULL $collapsable_categories
	 * @param \avathar\postlove\service\topic_likes|NULL         $topic_likes_service
	 */
	public function __construct(auth $auth,
		cache_service $cache,
		config $config,
		language $language,
		content_visibility $content_visibility,
		driver_interface $db,
		dispatcher_interface $dispatcher,
		pagination $pagination,
		request_interface $request,
		template $template,
		user $user,
		$root_path,
		$phpEx,
		db_text $config_text,
		?\phpbb\collapsiblecategories\operator\operator $collapsable_categories = null,
		$topic_likes_service = null
	)
	{
		$this->auth = $auth;
		$this->cache = $cache;
		$this->config = $config;
		$this->language = $language;
		$this->content_visibility = $content_visibility;
		$this->db = $db;
		$this->dispatcher = $dispatcher;
		$this->pagination = $pagination;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->root_path = $root_path;
		$this->phpEx = $phpEx;
		$this->config_text = $config_text;
		$this->collapsable_categories = $collapsable_categories;
		$this->topic_likes_service = $topic_likes_service;
	}

	/**
	 * Assemble the recent topics list and hand it to the template.
	 *
	 * Returns early without rendering anything if the user lacks u_rt_view, has switched the block
	 * off, or if no forum or topic survives filtering. Board config values are read first and then
	 * overridden by the user's own preference wherever the matching u_rt_* permission is held.
	 *
	 * @param  string $tpl_loopname Template block to write the topics into; also prefixes the pagination request var
	 * @param  string $context      'index' or 'viewforum'; selects which location setting and template vars are used
	 * @return void
	 */
	public function display_recent_topics($tpl_loopname = 'recent_topics', $context = 'index')
	{
		if (!function_exists('topic_status'))
		{
			include($this->root_path . 'includes/functions_display.' . $this->phpEx);
		}

		// can view rt ?
		if ($this->auth->acl_get('u_rt_view') == '0')
		{
			return;
		}

		// if user can enable recent topics and it is not enabled then return
		if ($this->auth->acl_get('u_rt_enable') && !$this->user->data['user_rt_enable'])
		{
			return;
		}

		// support for phpbb collapsable categories extension
		if ($this->collapsable_categories !== null)
		{
			$fid = 'fid_rt'; // can be any unique string to identify your extension's collapsible element
			$this->template->assign_vars(array(
				'S_EXT_COLCAT_HIDDEN'       => $this->collapsable_categories->is_collapsed($fid),
				'U_EXT_COLCAT_COLLAPSE_URL' => $this->collapsable_categories->get_collapsible_link($fid),
			));
		}

		//display parent forums
		$this->display_parent_forums = $this->config['rt_parents'];

		//rt block location
		if ($context === 'viewforum')
		{
			$this->location = $this->config['rt_viewforum_location'];
			if ($this->auth->acl_get('u_rt_location') && isset($this->user->data['user_rt_viewforum_location']))
			{
				$this->location = $this->user->data['user_rt_viewforum_location'];
			}
		}
		else
		{
			$this->location = $this->config['rt_location'];
			if ($this->auth->acl_get('u_rt_location') && isset($this->user->data['user_rt_location']))
			{
				$this->location = $this->user->data['user_rt_location'];
			}
		}

		$this->unread_only = $this->config['rt_unread_only'];
		if ($this->auth->acl_get('u_rt_unread_only') && isset($this->user->data['user_rt_unread_only']))
		{
			$this->unread_only = $this->user->data['user_rt_unread_only'];
		}

		$this->rtstart = $this->request->variable($tpl_loopname . '_start', 0);

		// set # topics shown per page
		$this->topics_per_page = (int) $this->config['rt_number'];
		if ($this->auth->acl_get('u_rt_number') && isset($this->user->data['user_rt_number']))
		{
			$this->topics_per_page = (int) $this->user->data['user_rt_number'];
		}

		$this->excluded_topics = explode(',', $this->config['rt_anti_topics']);
		$min_topic_level = $this->config['rt_min_topic_level'];

		$this->sort_topics = $this->config['rt_sort_start_time'] ? 'topic_time' : 'topic_last_post_time';
		// if user can set recent topic sorting order and it is set then use the preference
		if ($this->auth->acl_get('u_rt_sort_start_time') && isset($this->user->data['user_rt_sort_start_time']))
		{
			$this->sort_topics = $this->user->data['user_rt_sort_start_time'] ? 'topic_time' : 'topic_last_post_time';
		}

		$this->get_forum_list();
		// No forums to display
		if (count($this->forum_ids) == 0)
		{
			return;
		}

		//limit number of pages to be shown
		// compute as product of topics per page and max number of pages.
		// The count needs the forum list above, or it only counts m_approve forums (issue #195).
		$this->total_topics_limit = 0;
		if ((int) $this->config['rt_page_number'] == 0)
		{
			$this->total_topics_limit = $this->topics_per_page * (int) $this->config['rt_page_numbermax'];
		}
		else
		{
			$sql_array = $this->get_allowed_topics_sql($this->excluded_topics, $min_topic_level);
			$count_sql_array = $sql_array;
			$count_sql_array['SELECT'] = 'COUNT(t.topic_id) as topic_count';
			unset($count_sql_array['ORDER_BY']);
			$sql = $this->db->sql_build_query('SELECT', $count_sql_array);
			$result = $this->db->sql_query($sql);
			$this->total_topics_limit = (int) $this->db->sql_fetchfield('topic_count');
			$this->db->sql_freeresult($result);

		}

		$topics_count = $this->get_topic_list();

		if (count($this->topic_list) == 0)
		{
			return;
		}
		// If topics to display

		// Grab icons
		$this->icons = array();
		if ($this->obtain_icons)
		{
			$this->icons = $this->cache->obtain_icons();
		}

		// Borrowed from search.php
		$topic_tracking_info = array();
		foreach ($this->forums as $forum_id => $forum)
		{
			if ($this->user->data['is_registered'] && $this->config['load_db_lastread'])
			{
				$topic_tracking_info[$forum_id] = get_topic_tracking($forum_id, $forum['topic_list'], $forum['rowset'], array($forum_id => $forum['mark_time']), $forum_id ? false : $forum['topic_list']);
			}
			else if ($this->config['load_anon_lastread'] || $this->user->data['is_registered'])
			{
				$tracking_topics = $this->request->variable($this->config['cookie_name'] . '_track', '', true, request_interface::COOKIE);
				$tracking_topics = $tracking_topics ? tracking_unserialize($tracking_topics) : array();

				$topic_tracking_info[$forum_id] = get_complete_topic_tracking($forum_id, $forum['topic_list'], $forum_id ? false : $forum['topic_list']);

				if (!$this->user->data['is_registered'])
				{
					if (isset($tracking_topics['l']))
					{
						$this->user->data['user_lastmark'] =  ( (int) base_convert($tracking_topics['l'], 36, 10) + (int) $this->config['board_startdate']);
					}
					else
					{
						$this->user->data['user_lastmark'] = 0;
					}
				}
			}
		}

		$ads_index_code = false;
		if (!empty($this->config['rt_ads_enable']))
		{
			$ads_code = $this->config_text->get('rt_ads_code');
			if (!empty($ads_code))
			{
				$ads_index_code = str_replace('&', '&amp;', html_entity_decode($ads_code));
			}
		}

		/**
		 * Event to modify the advertisement code before it is assigned to the template
		 *
		 * @event avathar.recenttopics.modify_ads_code
		 * @var   string|false    ads_index_code    The advertisement HTML to render, or false if disabled
		 * @since 3.0.6
		 */
		$vars = ['ads_index_code'];
		extract($this->dispatcher->trigger_event('avathar.recenttopics.modify_ads_code', compact($vars)));

		$location_prefix = ($context === 'viewforum') ? 'S_VF_LOCATION_' : 'S_LOCATION_';

		$tpl_vars = array(
			'RT_SORT_START_TIME'                   => $this->sort_topics === 'topic_time',
			'S_RECENT_TOPICS'                      => true,
			$location_prefix . 'TOP'               => $this->location == 'RT_TOP',
			$location_prefix . 'BOTTOM'            => $this->location == 'RT_BOTTOM',
			'S_RT_SIDE_SHOW_DATE'                  => !empty($this->config['rt_side_show_date']),
			'NEWEST_POST_IMG'                      => $this->user->img('icon_topic_newest', 'VIEW_NEWEST_POST'),
			'LAST_POST_IMG'                        => $this->user->img('icon_topic_latest', 'VIEW_LATEST_POST'),
			'POLL_IMG'                             => $this->user->img('icon_topic_poll', 'TOPIC_POLL'),
			'ADS_INDEX_CODE'                       => $ads_index_code,
			'S_POSTLOVE'                           => $this->topic_likes_service !== null,
			strtoupper($tpl_loopname) . '_DISPLAY' => true,
		);

		if ($context !== 'viewforum')
		{
			$tpl_vars['S_LOCATION_SIDE'] = $this->location == 'RT_SIDE';
		}

		$this->template->assign_vars($tpl_vars);

		$this->fill_template($tpl_loopname, $topic_tracking_info, $topics_count);
	}

	/**
	 * Narrow the board's forums down to the ones this list may draw topics from.
	 *
	 * Two passes: first the forums the user may see at all, then a query dropping any whose
	 * forum_recent_topics flag the admin cleared in the ACP. The result lands in $forum_ids.
	 *
	 * @return void
	 */
	private function get_forum_list()
	{
		// Get the allowed forums: f_read grants full access; f_list_topics lets
		// the user see topic titles without reading content (issue #182).
		$forum_ary = array();
		$forum_read_ary = $this->auth->acl_getf('f_read');
		$forum_list_ary = $this->auth->acl_getf('f_list_topics');
		foreach ($forum_read_ary as $forum_id => $allowed)
		{
			if ($allowed['f_read'] || !empty($forum_list_ary[$forum_id]['f_list_topics']))
			{
				$forum_ary[] = (int) $forum_id;
			}
		}
		$this->forum_ids = array_unique($forum_ary);

		// phpBB grants f_read on a passworded forum regardless of the password, so drop the ones
		// this user has not unlocked, as the index, viewforum, search and feeds do (issue #193).
		$this->forum_ids = array_diff($this->forum_ids, $this->user->get_passworded_forums());

		if (count($this->forum_ids) > 1)
		{
			$sql = 'SELECT forum_id
					FROM ' . FORUMS_TABLE . '
					WHERE ' . $this->db->sql_in_set('forum_id', $this->forum_ids) . '
					AND forum_recent_topics = 1';

			$result = $this->db->sql_query($sql);

			$this->forum_ids = array();
			while ($row = $this->db->sql_fetchrow($result))
			{
				$this->forum_ids[] = $row['forum_id'];
			}
			$this->db->sql_freeresult($result);
			$this->forum_ids = array_unique($this->forum_ids);

		}
	}

	/**
	 * Select the topic ids for the current page into $topic_list.
	 *
	 * Takes one of two routes: phpBB's own get_unread_topics() when the user asked for unread only
	 * and is logged in, otherwise the custom query from get_allowed_topics_sql(). Either way the
	 * rows are walked in full so the total can be counted while only the current page is kept, and
	 * $forums, $obtain_icons and $rtstart are set up for the render step along the way.
	 *
	 * @return int Total number of topics available, used to size the pagination
	 */
	private function get_topic_list()
	{
		$this->rtstart = max(0, $this->rtstart);

		if ($this->total_topics_limit > 0)
		{
			$this->rtstart = min((int) $this->rtstart, $this->total_topics_limit);
		}

		$this->forums = $this->topic_list = array();
		$topics_count = 0;
		$this->obtain_icons = false;

		$min_topic_level = $this->config['rt_min_topic_level'];

		// Either use the phpBB core function to get unread topics, or the custom function for default behavior
		if ($this->unread_only && $this->user->data['user_id'] != ANONYMOUS)
		{
			// Get unread topics
			$sql_extra = ' AND ' . $this->db->sql_in_set('t.topic_id', $this->excluded_topics, true);
			$sql_extra .= ' AND ' . $this->content_visibility->get_forums_visibility_sql('topic', $this->forum_ids, $table_alias = 't.');
			$unread_topics = get_unread_topics(false, $sql_extra, '', $this->total_topics_limit);
			$this->rtstart = min(count($unread_topics) - 1 , (int) $this->rtstart);

			foreach ($unread_topics as $topic_id => $mark_time)
			{
				$topics_count++;
				if (($topics_count > $this->rtstart) && ($topics_count <= ($this->rtstart + $this->topics_per_page)))
				{
					$this->topic_list[] = $topic_id;
				}
			}
		}
		else
		{
			// Get allowed topics
			$sql_array = $this->get_allowed_topics_sql($this->excluded_topics, $min_topic_level);
			$count_sql_array = $sql_array;
			$count_sql_array['SELECT'] = 'COUNT(t.topic_id) as topic_count';
			unset($count_sql_array['ORDER_BY']);

			$sql = $this->db->sql_build_query('SELECT', $count_sql_array);
			$result = $this->db->sql_query($sql);
			$num_rows = (int) $this->db->sql_fetchfield('topic_count');
			$this->db->sql_freeresult($result);

			//load topics list
			$sql = $this->db->sql_build_query('SELECT', $sql_array);

			if ($this->total_topics_limit > 0)
			{
				$result = $this->db->sql_query_limit($sql, $this->total_topics_limit);
			}
			else
			{
				$result = $this->db->sql_query($sql);
			}

			if ($result != null)
			{
				$this->rtstart = min($num_rows - 1 , $this->rtstart);
			}
			else
			{
				$this->rtstart = 0;
			}

			while ($row = $this->db->sql_fetchrow($result))
			{
				$topics_count++;
				if (($topics_count > $this->rtstart) && ($topics_count <= ($this->rtstart + $this->topics_per_page)))
				{
					$this->topic_list[] = $row['topic_id'];

					$rowset[$row['topic_id']] = $row;
					if (!isset($this->forums[$row['forum_id']]) && $this->user->data['is_registered'] && $this->config['load_db_lastread'])
					{
						$this->forums[$row['forum_id']]['mark_time'] = $row['f_mark_time'];
					}
					$this->forums[$row['forum_id']]['topic_list'][] = $row['topic_id'];
					$this->forums[$row['forum_id']]['rowset'][$row['topic_id']] = & $rowset[$row['topic_id']];

					if ($row['icon_id'])
					{
						$this->obtain_icons = true;
					}
				}
			}
			$this->db->sql_freeresult($result);
		}
		return $topics_count;
	}

	/**
	 * Build the query that lists the topics this user is allowed to see.
	 *
	 * Used for guests and whenever unread-only is off, where phpBB's get_unread_topics() does not
	 * apply. Joins the tracking tables so read state and the user's own posts are known, and sorts
	 * by whichever of topic_time / topic_last_post_time the effective sort preference selected.
	 * Dispatches avathar.recenttopics.sql_pull_topics_list so other extensions can alter the query.
	 *
	 * @param  array $excluded_topics Topic ids to leave out, from the rt_anti_topics setting
	 * @param  int   $min_topic_level Lowest topic_type to include; above 0 restricts to stickies/announcements/globals
	 * @return array Query array for sql_build_query()
	 */
	private function get_allowed_topics_sql($excluded_topics, $min_topic_level)
	{
		// Get the allowed topics
		$sql_array = array(
			'SELECT'    => 't.forum_id, t.topic_id, t.topic_type, t.icon_id, tp.topic_posted, tt.mark_time, ft.mark_time as f_mark_time, t.' . $this->sort_topics . ' as sortcr ',
			'FROM'      => array(TOPICS_TABLE => 't'),
			'LEFT_JOIN' => array(
				array(
					'FROM' => array(TOPICS_TRACK_TABLE => 'tt'),
					'ON'   => 'tt.topic_id = t.topic_id AND tt.user_id = ' . (int) $this->user->data['user_id'],
				),
				array(
					'FROM' => array(FORUMS_TRACK_TABLE => 'ft'),
					'ON'   => 'ft.forum_id = t.forum_id AND ft.user_id = ' . (int) $this->user->data['user_id'],
				),
				array(
					'FROM' => array(TOPICS_POSTED_TABLE => 'tp'),
					'ON' => 'tp.topic_id = t.topic_id AND tp.user_id = ' . (int) $this->user->data['user_id'],
				),
			),
			'WHERE'     => $this->db->sql_in_set('t.topic_id', $excluded_topics, true) . '
					AND t.topic_status <> ' . ITEM_MOVED . '
					AND ' . $this->content_visibility->get_forums_visibility_sql('topic', $this->forum_ids, $table_alias = 't.'),
			'ORDER_BY'  => 't.' . $this->sort_topics . ' DESC',
		);

		// Check if we want all topics, or only stickies/announcements/globals
		if ($min_topic_level > 0)
		{
			$sql_array['WHERE'] .= ' AND t.topic_type >= ' . (int) $min_topic_level;
		}

		/**
		 * Event to modify the SQL query before the allowed topics list data is retrieved
		 *
		 * @event avathar.recenttopics.sql_pull_topics_list
		 * @var   array    sql_array        The SQL array
		 * @since 3.0.0
		 */
		$vars = array('sql_array');
		extract($this->dispatcher->trigger_event('avathar.recenttopics.sql_pull_topics_list', compact($vars)));

		return $sql_array;

	}

	/**
	 * Render the topic's first and last poster into the four username forms the template needs.
	 *
	 * @param  array $row Topic row
	 * @return array Plain name, colour, full HTML and profile URL for the topic author, then the same four for the last poster
	 */
	private function get_username_strings($row)
	{
		$topic_author       = get_username_string('username', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']);
		$topic_author_color = get_username_string('colour', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']);
		$topic_author_full  = get_username_string('full', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']);
		$u_topic_author     = get_username_string('profile', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']);
		$last_post_author        = get_username_string('username', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']);
		$last_post_author_colour = get_username_string('colour', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']);
		$last_post_author_full   = get_username_string('full', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']);
		$u_last_post_author      = get_username_string('profile', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']);
		return array($topic_author, $topic_author_color, $topic_author_full, $u_topic_author, $last_post_author, $last_post_author_colour, $last_post_author_full, $u_last_post_author);
	}

	/**
	 * Fetch the full topic rows for the ids picked by get_topic_list().
	 *
	 * Selects the whole topic record plus the forum name, and the parent-forum columns when the
	 * breadcrumb is enabled. Dispatches avathar.recenttopics.sql_pull_topics_data, followed by the
	 * deprecated paybas.* alias kept for extensions written against the original version.
	 *
	 * @return array Topic rows, at most one page worth
	 */
	private function get_topics_sql()
	{
		$sql_array = array(
			'SELECT'    => 't.*, f.forum_name, tp.topic_posted',
			'FROM'      => array(TOPICS_TABLE => 't'),
			'LEFT_JOIN' => array(
				array(
					'FROM' => array(FORUMS_TABLE => 'f'),
					'ON'   => 'f.forum_id = t.forum_id',
				),
				array(
					'FROM' => array(TOPICS_POSTED_TABLE => 'tp'),
					'ON' => 'tp.topic_id = t.topic_id AND tp.user_id = ' . (int) $this->user->data['user_id'],
				),
			),
			'WHERE'     => $this->db->sql_in_set('t.topic_id', $this->topic_list),
			'ORDER_BY'  => 't.' . $this->sort_topics . ' DESC',
		);
		if ($this->display_parent_forums)
		{
			$sql_array['SELECT'] .= ', f.parent_id, f.forum_parents, f.left_id, f.right_id';
		}
		/**
		 * Event to modify the SQL query before the topics data is retrieved
		 *
		 * @event avathar.recenttopics.sql_pull_topics_data
		 * @var   array    sql_array        The SQL array
		 * @since 3.0.0
		 */
		extract(
			$this->dispatcher->trigger_event(
				'avathar.recenttopics.sql_pull_topics_data',
				array('sql_array' => $sql_array)
			)
		);

		/**
		 * Backward-compat alias for vse/topicpreview, bb3mobi/lastpostavatar
		 *
		 * @event paybas.recenttopics.sql_pull_topics_data
		 * @var   array    sql_array        The SQL array
		 * @since 2.0.0
		 * @changed 3.0.5 Deprecated, will be removed in 3.1. Use avathar.recenttopics.sql_pull_topics_data instead
		 */
		extract(
			$this->dispatcher->trigger_event(
				'paybas.recenttopics.sql_pull_topics_data',
				array('sql_array' => $sql_array)
			)
		);

		$sql    = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, $this->topics_per_page);
		$rowset = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$rowset[] = $row;
		}
		$this->db->sql_freeresult($result);
		return $rowset;
	}

	/**
	 * Render the topics into the template block, one row at a time, then add the pagination.
	 *
	 * For each topic this resolves read state, folder image, moderation flags and URLs, and censors
	 * the titles. Three events let other extensions step in: modify_topics_list before the loop,
	 * modify_topictitle for the title prefix, and modify_tpl_ary for the finished row — each with a
	 * deprecated paybas.* alias alongside it.
	 *
	 * @param  string $tpl_loopname        Template block to write into
	 * @param  array  $topic_tracking_info Per-forum read-tracking data assembled by display_recent_topics()
	 * @param  int    $topics_count        Total topics available, used to size the pagination
	 * @return void
	 */
	private function fill_template($tpl_loopname, $topic_tracking_info, int $topics_count): void
	{
		// get topics from db
		$rowset = $this->get_topics_sql();
		$topic_icons = array();

		// Get postlove like counts if installed
		$topic_likes = [];
		if ($this->topic_likes_service !== null && !empty($this->topic_list))
		{
			$topic_likes = $this->topic_likes_service->get_topic_like_counts($this->topic_list);
		}
		// if topics returned by DB
		if (count($rowset))
		{
			/**
			 * Event to modify the topics list data before we start the display loop
			 *
			 * @event avathar.recenttopics.modify_topics_list
			 * @var   array    topic_list        Array of all the topic IDs
			 * @var   array    rowset            The full topics list array
			 * @since 3.0.0
			 */
			extract(
				$this->dispatcher->trigger_event(
					'avathar.recenttopics.modify_topics_list',
					array('topic_list' => $this->topic_list, 'rowset' => $rowset)
				)
			);

			/**
			 * Backward-compat alias for vse/topicpreview, rxu/thanks_for_posts, PayBas/PBWoW3ext
			 *
			 * @event paybas.recenttopics.modify_topics_list
			 * @var   array    topic_list        Array of all the topic IDs
			 * @var   array    rowset            The full topics list array
			 * @since 2.0.1
			 * @changed 3.0.5 Deprecated, will be removed in 3.1. Use avathar.recenttopics.modify_topics_list instead
			 */
			extract(
				$this->dispatcher->trigger_event(
					'paybas.recenttopics.modify_topics_list',
					array('topic_list' => $this->topic_list, 'rowset' => $rowset)
				)
			);

			foreach ($rowset as $row)
			{
				$topic_id = $row['topic_id'];
				$forum_id = $row['forum_id'];
				$s_type_switch_test = ($row['topic_type'] == POST_ANNOUNCE || $row['topic_type'] == POST_GLOBAL) ? 1 : 0;
				$replies            = $this->content_visibility->get_count('topic_posts', $row, $forum_id) - 1;
				if ($row['topic_status'] == ITEM_MOVED)
				{
					$topic_id = $row['topic_moved_id'];
					$unread_topic = false;
				}
				else
				{
					$unread_topic = (isset($topic_tracking_info[$forum_id][$row['topic_id']]) && $row['topic_last_post_time'] > $topic_tracking_info[$forum_id][$row['topic_id']]) ? true : false;
				}
				// Get folder img, topic status/type related information
				$folder_img = $folder_alt = $topic_type = '';
				if ($this->unread_only)
				{
					topic_status($row, $replies, true, $folder_img, $folder_alt, $topic_type);
					$unread_topic = true;
				}
				else
				{
					if (isset($topic_tracking_info[$forum_id][$row['topic_id']]) && $row['topic_last_post_time'] > $topic_tracking_info[$forum_id][$row['topic_id']])
					{
						topic_status($row, $replies, true, $folder_img, $folder_alt, $topic_type);
					}
					else
					{
						topic_status($row, $replies, false, $folder_img, $folder_alt, $topic_type);
					}
					if (isset($topic_tracking_info[$forum_id][$row['topic_id']]) && $row['topic_last_post_time'] > $topic_tracking_info[$forum_id][$row['topic_id']])
					{
						$unread_topic = true;
					}
					else
					{
						$unread_topic = false;
					}
				}
				$view_topic_url = append_sid("{$this->root_path}viewtopic.$this->phpEx", 'f=' . $forum_id . '&amp;t=' . $topic_id);
				$view_forum_url = append_sid("{$this->root_path}viewforum.$this->phpEx", 'f=' . $forum_id);
				$topic_unapproved = ($row['topic_visibility'] == ITEM_UNAPPROVED && $this->auth->acl_get('m_approve', $forum_id));
				$posts_unapproved = ($row['topic_visibility'] == ITEM_APPROVED && $row['topic_posts_unapproved'] && $this->auth->acl_get('m_approve', $forum_id));
				$u_mcp_queue   = ($topic_unapproved || $posts_unapproved) ? append_sid("{$this->root_path}mcp.$this->phpEx", 'i=queue&amp;mode=' . ($topic_unapproved ? 'approve_details' : 'unapproved_posts') . "&amp;t=$topic_id", true, $this->user->session_id) : '';
				$s_type_switch = ($row['topic_type'] == POST_ANNOUNCE || $row['topic_type'] == POST_GLOBAL) ? 1 : 0;

				if (!empty($this->icons[$row['icon_id']]))
				{
					$topic_icons[] = $topic_id;
				}
				topic_status($row, $replies, $unread_topic, $folder_img, $folder_alt, $topic_type);
				$topic_title = censor_text($row['topic_title']);
				$prefix      = '';

				/**
				 * Event to modify the topic title
				 *
				 * @event avathar.recenttopics.modify_topictitle
				 * @var   array    row      the forum row
				 * @var   string    prefix  the topic title prefix
				 * @since 3.0.0
				 */

				$vars = array('row', 'prefix');
				extract($this->dispatcher->trigger_event('avathar.recenttopics.modify_topictitle', compact($vars)));

				$topic_title = $prefix === '' ? $topic_title : $prefix . ' ' . $topic_title;
				$last_post_subject = censor_text($row['topic_last_post_subject']);
				if ($prefix != '')
				{
					$last_post_subject = $prefix . ' ' . $last_post_subject;
				}
				list($topic_author, $topic_author_color, $topic_author_full, $u_topic_author, $last_post_author, $last_post_author_colour, $last_post_author_full, $u_last_post_author) = $this->get_username_strings($row);
				//load language
				$this->language->add_lang('recenttopics', 'avathar/recenttopics');
				$tpl_ary = array(
					'FORUM_ID'                => $forum_id,
					'TOPIC_ID'                => $topic_id,
					'TOPIC_AUTHOR'            => $topic_author,
					'TOPIC_AUTHOR_COLOUR'     => $topic_author_color,
					'TOPIC_AUTHOR_FULL'       => $topic_author_full,
					'U_TOPIC_AUTHOR'          => $u_topic_author,
					'FIRST_POST_TIME'         => $this->user->format_date($row['topic_time']),
					'LAST_POST_SUBJECT'       => $last_post_subject,
					'LAST_POST_TIME'          => $this->user->format_date($row['topic_last_post_time']),
					'LAST_VIEW_TIME'          => $this->user->format_date($row['topic_last_view_time']),
					'LAST_POST_AUTHOR'        => $last_post_author,
					'LAST_POST_AUTHOR_COLOUR' => $last_post_author_colour,
					'LAST_POST_AUTHOR_FULL'   => $last_post_author_full,
					'U_LAST_POST_AUTHOR'      => $u_last_post_author,
					'REPLIES'     => $replies,
					'VIEWS'       => $row['topic_views'],
					'TOPIC_LIKES' => isset($topic_likes[$topic_id]) ? $topic_likes[$topic_id] : 0,
					'TOPIC_TITLE' => $topic_title,
					'FORUM_NAME'  => $row['forum_name'],
					'TOPIC_TYPE'           => $topic_type,
					'TOPIC_IMG_STYLE'      => $folder_img,
					'TOPIC_FOLDER_IMG'     => $this->user->img($folder_img, $folder_alt),
					'TOPIC_FOLDER_IMG_ALT' => $this->language->lang($folder_alt),
					'TOPIC_ICON_IMG'        => (!empty($this->icons[$row['icon_id']])) ? $this->icons[$row['icon_id']]['img'] : '',
					'TOPIC_ICON_IMG_WIDTH'  => (!empty($this->icons[$row['icon_id']])) ? $this->icons[$row['icon_id']]['width'] : '',
					'TOPIC_ICON_IMG_HEIGHT' => (!empty($this->icons[$row['icon_id']])) ? $this->icons[$row['icon_id']]['height'] : '',
					'ATTACH_ICON_IMG'       => ($this->auth->acl_get('u_download') && $this->auth->acl_get('f_download', $forum_id) && $row['topic_attachment']) ? $this->user->img('icon_topic_attach', $this->language->lang('TOTAL_ATTACHMENTS')) : '',
					'UNAPPROVED_IMG'        => ($topic_unapproved || $posts_unapproved) ? $this->user->img('icon_topic_unapproved', $topic_unapproved ? 'TOPIC_UNAPPROVED' : 'POSTS_UNAPPROVED') : '',
					'REPORTED_IMG'          => ($row['topic_reported'] && $this->auth->acl_get('m_report', $forum_id)) ? $this->user->img('icon_topic_reported', 'TOPIC_REPORTED') : '',
					'S_HAS_POLL'            => $row['poll_start'] ? true : false,
					'S_TOPIC_TYPE'        => $row['topic_type'],
					'S_UNREAD_TOPIC'      => $unread_topic,
					'S_TOPIC_REPORTED'    => $row['topic_reported'] && $this->auth->acl_get('m_report', $forum_id),
					'S_TOPIC_UNAPPROVED'  => $topic_unapproved,
					'S_POSTS_UNAPPROVED'  => $posts_unapproved,
					'S_POST_ANNOUNCE'     => $row['topic_type'] == POST_ANNOUNCE,
					'S_POST_GLOBAL'       => $row['topic_type'] == POST_GLOBAL,
					'S_POST_STICKY'       => $row['topic_type'] == POST_STICKY,
					'S_TOPIC_LOCKED'      => $row['topic_status'] == ITEM_LOCKED,
					'S_USER_POSTED'       => (isset($row['topic_posted']) && $row['topic_posted']) ? true : false,
					'S_TOPIC_MOVED'       => $row['topic_status'] == ITEM_MOVED,
					'S_TOPIC_TYPE_SWITCH' => ($s_type_switch == $s_type_switch_test) ? -1 : $s_type_switch_test,
					'U_NEWEST_POST' => $view_topic_url . '&amp;view=unread#unread',
					'U_LAST_POST'   => $view_topic_url . '&amp;p=' . $row['topic_last_post_id'] . '#p' . $row['topic_last_post_id'],
					'U_VIEW_TOPIC'  => $this->get_topic_link_url($view_topic_url, $row['topic_last_post_id']),
					'U_VIEW_FORUM'  => $view_forum_url,
					'U_MCP_REPORT'  => append_sid("{$this->root_path}mcp.$this->phpEx", 'i=reports&amp;mode=reports&amp;f=' . $forum_id . '&amp;t=' . $topic_id, true, $this->user->session_id),
					'U_MCP_QUEUE'   => $u_mcp_queue,
				);

				/**
				 * Modify the topic data before it is assigned to the template
				 *
				 * @event avathar.recenttopics.modify_tpl_ary
				 * @var   array    row            Array with topic data
				 * @var   array    tpl_ary        Template block array with topic data
				 * @since 3.0.0
				 */
				$vars = array('row', 'tpl_ary');
				extract($this->dispatcher->trigger_event('avathar.recenttopics.modify_tpl_ary', compact($vars)));

				/**
				 * Backward-compat alias for vse/topicpreview, rxu/thanks_for_posts,
				 * rmcgirr83/nationalflags, Dark1z/memberavatarstatus, tas2580/seourls,
				 * toxyy/anonymousposts, MuhClaren/timeago, bb3mobi/lastpostavatar
				 *
				 * @event paybas.recenttopics.modify_tpl_ary
				 * @var   array    row            Array with topic data
				 * @var   array    tpl_ary        Template block array with topic data
				 * @since 2.0.0
				 * @changed 3.0.5 Deprecated, will be removed in 3.1. Use avathar.recenttopics.modify_tpl_ary instead
				 */
				$vars = array('row', 'tpl_ary');
				extract($this->dispatcher->trigger_event('paybas.recenttopics.modify_tpl_ary', compact($vars)));

				$this->template->assign_block_vars($tpl_loopname, $tpl_ary);
				$this->pagination->generate_template_pagination($view_topic_url, $tpl_loopname . '.pagination', 'start', $replies + 1, $this->config['posts_per_page'], 1, true, true);
				if ($this->display_parent_forums)
				{
					$forum_parents = get_forum_parents($row);
					foreach ($forum_parents as $parent_id => $data)
					{
						$this->template->assign_block_vars(
							$tpl_loopname . '.parent_forums', array(
								'FORUM_ID'     => $parent_id,
								'FORUM_NAME'   => $data[0],
								'U_VIEW_FORUM' => append_sid("{$this->root_path}viewforum.$this->phpEx", 'f=' . $parent_id),
							)
						);
					}
				}
			}// end rowsset

			// Get URL-parameters for pagination
			$url_params    = explode('&', $this->user->page['query_string']);
			$append_params = array();
			foreach ($url_params as $param)
			{
				if (!$param)
				{
					continue;
				}
				if (strpos($param, '=') === false)
				{
					// Fix MSSTI Advanced BBCode MOD
					$append_params[$param] = '1';
					continue;
				}
				list($name, $value) = explode('=', $param);
				if ($name != $tpl_loopname . '_start')
				{
					$append_params[$name] = $value;
				}
			}
			$pagination_url = append_sid($this->root_path . $this->user->page['page_name'], $append_params);
			$this->pagination->generate_template_pagination($pagination_url, 'rt_pagination',
				$tpl_loopname . '_start', $topics_count, $this->topics_per_page, max(0, min((int) $this->rtstart, $this->total_topics_limit)));
			$this->template->assign_vars(
				array (
					'S_TOPIC_ICONS' => count($topic_icons) ? true : false,
				)
			);
		}// topics found
	}

	/**
	 * Get the topic link URL based on the rt_topic_link_to config setting
	 *
	 * The admin chooses where a topic title points: 1 jumps to the last post, 2 to the first
	 * unread one, anything else leaves the link on the first post.
	 *
	 * @param string $view_topic_url  Base topic URL (first post)
	 * @param int    $last_post_id    Last post ID in the topic
	 * @return string
	 */
	private function get_topic_link_url($view_topic_url, $last_post_id)
	{
		switch ((int) $this->config['rt_topic_link_to'])
		{
			case 1:
				return $view_topic_url . '&amp;p=' . $last_post_id . '#p' . $last_post_id;
			case 2:
				return $view_topic_url . '&amp;view=unread#unread';
			default:
				return $view_topic_url;
		}
	}
}
