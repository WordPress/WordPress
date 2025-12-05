<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

interface AdminSettingsInterface {
	public function get_title();

	public function show_settings();
}
