<?php
/**
 *
 * @package Recent Topics Extension
 * @copyright (c) 2015 PayBas
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Based on the original NV Recent Topics by Joas Schilling (nickvergessen)
 */

namespace avathar\recenttopics\event;

use phpbb\auth\auth;
use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\language\language;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\user;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener for the per-user Recent Topics settings.
 *
 * Adds the extension's fields to UCP > Board preferences > Edit display options, reading and writing
 * the user_rt_* columns, and gives new accounts the board-wide defaults on registration. Each field is
 * shown and saved only if the user holds the matching u_rt_* permission.
 */
class ucp_listener implements EventSubscriberInterface
{
	/** Index-page block locations a user may choose */
	const LOCATIONS = ['RT_TOP', 'RT_BOTTOM', 'RT_SIDE'];

	/** Forum-view block locations a user may choose; there is no side column there */
	const VIEWFORUM_LOCATIONS = ['RT_TOP', 'RT_BOTTOM'];

	/** Range for the number of topics per page, matching the UCP form's max */
	const NUMBER_MIN = 1;
	const NUMBER_MAX = 999;

	/**
	* @var auth
	*/
	protected $auth;

	/**
	* @var config
	*/
	protected $config;

	/**
	* @var request
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
	 * @var language
	 */
	protected $language;

	/* @var driver_interface */
	protected $db;

	/**
	 * ucp_listener constructor.
	 *
	 * @param auth             $auth
	 * @param config           $config
	 * @param request          $request
	 * @param template         $template
	 * @param user             $user
	 * @param language         $language
	 * @param driver_interface $db
	 */
	public function __construct(auth $auth,
		config $config,
		request $request,
		template $template,
		user $user,
		language $language,
		driver_interface $db
	)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->language = $language;
		$this->db = $db;
	}

	/**
	 * Map the phpBB core events this listener hooks onto the methods that handle them.
	 *
	 * @return array Event name => method name
	 */
	public static function getSubscribedEvents()
	{
		return array(
		'core.ucp_prefs_view_data'        => 'ucp_prefs_get_data',
		'core.ucp_prefs_view_update_data' => 'ucp_prefs_set_data',
		'core.ucp_register_register_after' => 'ucp_register_set_data'
		);
	}

	/**
	 * Collect and display the user's Recent Topics preferences in UCP > Board preferences > Edit display options.
	 *
	 * Reads each rt_* setting from the request (falling back to the value stored on the user) into
	 * $event['data'] for ucp_prefs_set_data(), then, on a plain page view, assigns the template vars
	 * for the fields this user is permitted to see.
	 *
	 * @param  \phpbb\event\data $event Event object; reads ['submit'], reads and writes ['data']
	 * @return void
	 */
	public function ucp_prefs_get_data($event)
	{
		// Request the user option vars and add them to the data array
		$event['data'] = array_merge(
			$event['data'], array(
			'rt_enable'          => $this->request->variable('rt_enable', (int) $this->user->data['user_rt_enable']),
			'rt_location'        => $this->valid_location(
				$this->request->variable('rt_location', $this->user->data['user_rt_location']),
				self::LOCATIONS, $this->user->data['user_rt_location'], $this->config['rt_location']
			),
			'rt_viewforum_location' => $this->valid_location(
				$this->request->variable('rt_viewforum_location', $this->user->data['user_rt_viewforum_location']),
				self::VIEWFORUM_LOCATIONS, $this->user->data['user_rt_viewforum_location'], $this->config['rt_viewforum_location']
			),
			'rt_number'          => max(self::NUMBER_MIN, min(self::NUMBER_MAX,
				$this->request->variable('rt_number', (int) $this->user->data['user_rt_number'])
			)),
			'rt_sort_start_time' => $this->request->variable('rt_sort_start_time', (int) $this->user->data['user_rt_sort_start_time']),
			'rt_unread_only'     => $this->request->variable('rt_unread_only', (int) $this->user->data['user_rt_unread_only']),
			)
		);

		// Output the data vars to the template (except on form submit)
		if (!$event['submit'] && $this->auth->acl_get('u_rt_view'))
		{
			$this->language->add_lang('recenttopics_ucp', 'avathar/recenttopics');

			$template_vars = array();

			// if authorised for one of these then set ucp master template variable to true
			if ($this->auth->acl_get('u_rt_enable') || $this->auth->acl_get('u_rt_location') || $this->auth->acl_get('u_rt_number') || $this->auth->acl_get('u_rt_sort_start_time') || $this->auth->acl_get('u_rt_unread_only'))
			{
				$template_vars += array(
				'S_RT_SHOW' => true,
				);
			}

			if ($this->auth->acl_get('u_rt_enable'))
			{
				$template_vars += array(
				'A_RT_ENABLE' => true,
				'S_RT_ENABLE' => $event['data']['rt_enable'],
				);
			}

			if ($this->auth->acl_get('u_rt_location'))
			{

				$template_vars += array(
					'A_RT_LOCATION' => true,
				);

				$display_types = array (
					'RT_TOP'    => $this->language->lang('RT_TOP'),
					'RT_BOTTOM' => $this->language->lang('RT_BOTTOM'),
					'RT_SIDE'   => $this->language->lang('RT_SIDE'),
				);

				foreach ($display_types as $key => $display_type)
				{
					$this->template->assign_block_vars(
						'location_row',
						array(
							'VALUE'    => $key,
							'SELECTED' => ($event['data']['rt_location'] == $key) ? ' selected="selected"' : '',
							'OPTION'   => $display_type,
						)
					);
				}

				// Viewforum location (top/bottom only)
				if ($this->config['rt_viewforum'])
				{
					$template_vars += array(
						'A_RT_VF_LOCATION' => true,
					);

					$vf_display_types = array (
						'RT_TOP'    => $this->language->lang('RT_TOP'),
						'RT_BOTTOM' => $this->language->lang('RT_BOTTOM'),
					);

					foreach ($vf_display_types as $key => $display_type)
					{
						$this->template->assign_block_vars(
							'vf_location_row',
							array(
								'VALUE'    => $key,
								'SELECTED' => ($event['data']['rt_viewforum_location'] == $key) ? ' selected="selected"' : '',
								'OPTION'   => $display_type,
							)
						);
					}
				}
			}

			if ($this->auth->acl_get('u_rt_number'))
			{
				$template_vars += array(
					'A_RT_NUMBER' => true,
					'RT_NUMBER' => $event['data']['rt_number'],
				);
			}

			if ($this->auth->acl_get('u_rt_sort_start_time'))
			{
				$template_vars += array(
				'A_RT_SORT_START_TIME' => true,
				'S_RT_SORT_START_TIME' => $event['data']['rt_sort_start_time'],
				);
			}

			if ($this->auth->acl_get('u_rt_unread_only'))
			{
				$template_vars += array(
				'A_RT_UNREAD_ONLY' => true,
				'S_RT_UNREAD_ONLY' => $event['data']['rt_unread_only'],
				);
			}

			$this->template->assign_vars($template_vars);
		}
	}

	/**
	 * Persist the user's Recent Topics preferences from UCP > Board preferences > Edit display options.
	 *
	 * Fired just before phpBB's UPDATE on the users table, so mapping the rt_* values collected by
	 * ucp_prefs_get_data() onto their user_rt_* columns here saves them in that same query
	 * The submitted values arrive in $event['data'], where ucp_prefs_get_data() placed them; the keys are
	 * the form's rt_* names, which this method translates to the user_rt_* column names.
	 *
	 * @param  \phpbb\event\data $event Event object; reads ['data'], writes ['sql_ary']
	 * @return void
	 */
	public function ucp_prefs_set_data($event)
	{
		$sql_ary = array();

		if ($this->auth->acl_get('u_rt_enable'))
		{
			$sql_ary['user_rt_enable'] = $event['data']['rt_enable'];
		}

		if ($this->auth->acl_get('u_rt_location'))
		{
			$sql_ary['user_rt_location'] = $event['data']['rt_location'];
			$sql_ary['user_rt_viewforum_location'] = $event['data']['rt_viewforum_location'];
		}

		if ($this->auth->acl_get('u_rt_number'))
		{
			$sql_ary['user_rt_number'] = $event['data']['rt_number'];
		}

		if ($this->auth->acl_get('u_rt_sort_start_time'))
		{
			$sql_ary['user_rt_sort_start_time'] = $event['data']['rt_sort_start_time'];
		}

		if ($this->auth->acl_get('u_rt_unread_only'))
		{
			$sql_ary['user_rt_unread_only'] = $event['data']['rt_unread_only'];
		}

		$event['sql_ary'] = array_merge($event['sql_ary'], $sql_ary);
	}

	/**
	 * Return a submitted block location if it is one of the allowed options (issue #198).
	 *
	 * Otherwise keep the user's stored location, or use the board default if that is not valid either.
	 *
	 * @param  string $submitted Location from the request
	 * @param  array  $allowed   Valid locations for this setting
	 * @param  string $stored    The user's current location
	 * @param  string $default   The board-wide default location
	 * @return string
	 */
	private function valid_location($submitted, array $allowed, $stored, $default)
	{
		foreach ([$submitted, $stored, $default] as $location)
		{
			if (in_array($location, $allowed, true))
			{
				return $location;
			}
		}

		return $allowed[0];
	}

	/**
	 * set a newly registered account's Recent Topics preferences from default.
	 *
	 * Listens to core.ucp_register_register_after, which fires after user_add() has inserted the row
	 * and carries the new user_id, so the user_rt_* columns are written by a second UPDATE of our own.
	 * core.ucp_register_data_after fires during form validation with no user_id (issue #196).
	 *
	 * @param  \phpbb\event\data $event Event object; reads ['user_id']
	 * @return void
	 */
	public function ucp_register_set_data($event)
	{

		$sql_ary = array(
			'user_rt_enable'              => (int) $this->config['rt_index'],
			'user_rt_sort_start_time'     => (int) $this->config['rt_sort_start_time'],
			'user_rt_unread_only'         => (int) $this->config['rt_unread_only'],
			'user_rt_location'            => $this->config['rt_location'],
			'user_rt_viewforum_location'  => $this->config['rt_viewforum_location'],
			'user_rt_number'              => ((int) $this->config['rt_number'] > 0 ? (int) $this->config['rt_number'] : 5)
		);

		$sql = 'UPDATE ' . USERS_TABLE . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
			WHERE user_id = ' . (int) $event['user_id'];

		$this->db->sql_query($sql);
	}
}
