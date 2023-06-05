<?php
/**
 * Evaluates the spec and returns a status.
 */

namespace Automattic\WooCommerce\Internal\Admin\RemoteFreeExtensions;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\PluginsHelper;
use Automattic\WooCommerce\Admin\RemoteInboxNotifications\RuleEvaluator;

/**
 * Evaluates the extension and returns it.
 */
class EvaluateExtension {
	/**
	 * Evaluates the extension and returns it.
	 *
	 * @param object $extension The extension to evaluate.
	 * @return object The evaluated extension.
	 */
	public static function evaluate( $extension ) {
		global $wp_version;
		$rule_evaluator = new RuleEvaluator();

		if ( isset( $extension->is_visible ) ) {
			$is_visible            = $rule_evaluator->evaluate( $extension->is_visible );
			$extension->is_visible = $is_visible;
		} else {
			$extension->is_visible = true;
		}

		// Run PHP and WP version chcecks.
		if ( true === $extension->is_visible ) {
			if ( isset( $extension->min_php_version ) && ! version_compare( PHP_VERSION, $extension->min_php_version, '>=' ) ) {
				$extension->is_visible = false;
			}

			if ( isset( $extension->min_wp_version ) && ! version_compare( $wp_version, $extension->min_wp_version, '>=' ) ) {
				$extension->is_visible = false;
			}
		}

		$installed_plugins       = PluginsHelper::get_installed_plugin_slugs();
		$activated_plugins       = PluginsHelper::get_active_plugin_slugs();
		$extension->is_installed = in_array( explode( ':', $extension->key )[0], $installed_plugins, true );
		$extension->is_activated = in_array( explode( ':', $extension->key )[0], $activated_plugins, true );

		return $extension;
	}
}
