<?php
/**
 *
 * @package Recent Topics Extension
 * @copyright (c) 2015 PayBas
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Based on the original NV Recent Topics by Joas Schilling (nickvergessen)
 */

namespace avathar\recenttopics;

/**
 * Extension class for custom enable/disable/purge actions
 */
class ext extends \phpbb\extension\base
{
	const RT_VERSION = '3.0.12';
	const MIN_PHP_VERSION = '8.1.0';
	const MIN_PHPBB_VERSION = '3.3.0';

	/**
	 * Check whether the extension can be enabled.
	 *
	 * @return bool|array True if enableable, or an array of error language keys otherwise
	 */
	public function is_enableable()
	{
		$errors = [];

		$user = $this->container->get('user');
		$user->add_lang_ext('avathar/recenttopics', 'recenttopics');

		if (version_compare(PHP_VERSION, self::MIN_PHP_VERSION, '<'))
		{
			$errors[] = $user->lang('RECENTTOPICS_PHP_VERSION_FAIL', self::MIN_PHP_VERSION, PHP_VERSION);
		}

		if (phpbb_version_compare(PHPBB_VERSION, self::MIN_PHPBB_VERSION, '<'))
		{
			$errors[] = $user->lang('RECENTTOPICS_PHPBB_VERSION_FAIL', self::MIN_PHPBB_VERSION, PHPBB_VERSION);
		}

		return empty($errors) ? true : $errors;
	}
}
