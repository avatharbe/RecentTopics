<?php
/**
 *
 * @package Recent Topics Extension
 * @copyright (c) 2026 Andreas Vandenberghe
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Static checks on the ACP template's assets.
 *
 * See: https://github.com/avatharbe/RecentTopics/issues/200
 */

namespace avathar\recenttopics\tests\acp;

class acp_template_test extends \phpbb_test_case
{
	/** @var string Extension root */
	private $ext_root;

	protected function setUp(): void
	{
		parent::setUp();
		$this->ext_root = dirname(__DIR__, 2);
	}

	/**
	 * Opening the ACP page must not make the admin's browser call Patreon's CDN (#200).
	 */
	public function test_acp_template_loads_no_external_patreon_image()
	{
		$template = file_get_contents($this->ext_root . '/adm/style/acp_recenttopics.html');

		$this->assertStringNotContainsString('patreon.com/external', $template);
		$this->assertStringContainsString('{{ U_PATREON_BUTTON }}', $template);
	}

	/**
	 * The bundled button the template points to ships with the extension.
	 */
	public function test_patreon_button_is_bundled()
	{
		$file = $this->ext_root . '/adm/style/images/become_a_patron_button.png';

		$this->assertFileExists($file);

		// getimagesize() is core PHP; mime_content_type() needs ext/fileinfo, which the Windows CI job lacks
		$info = getimagesize($file);
		$this->assertSame('image/png', $info['mime']);
		$this->assertSame([217, 51], [$info[0], $info[1]]);
	}
}
