<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

// if uninstall.php is not called by WordPress, die.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

require 'shared.php';

$matomo_is_using_multi_site    = function_exists( 'is_multisite' ) && is_multisite();
$matomo_settings               = new \WpMatomo\Settings();
$matomo_should_remove_all_data = $matomo_settings->should_delete_all_data_on_uninstall();

$matomo_uninstaller = new \WpMatomo\Uninstaller();
$matomo_uninstaller->uninstall( $matomo_should_remove_all_data );
