<?php

namespace Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks;

use Automattic\WooCommerce\Admin\Features\Features;
use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Task;
use Automattic\WooCommerce\Internal\Admin\RemoteFreeExtensions\Init as RemoteFreeExtensions;

/**
 * Marketing Task
 */
class Marketing extends Task {
	/**
	 * ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return 'marketing';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		if ( true === $this->get_parent_option( 'use_completed_title' ) ) {
			if ( $this->is_complete() ) {
				return __( 'You added sales channels', 'woocommerce' );
			}
			return __( 'Get more sales', 'woocommerce' );
		}
		return __( 'Set up marketing tools', 'woocommerce' );
	}

	/**
	 * Content.
	 *
	 * @return string
	 */
	public function get_content() {
		return __(
			'Add recommended marketing tools to reach new customers and grow your business',
			'woocommerce'
		);
	}

	/**
	 * Time.
	 *
	 * @return string
	 */
	public function get_time() {
		return __( '2 minutes', 'woocommerce' );
	}

	/**
	 * Task completion.
	 *
	 * @return bool
	 */
	public function is_complete() {
		return self::has_installed_extensions();
	}

	/**
	 * Task visibility.
	 *
	 * @return bool
	 */
	public function can_view() {
		return Features::is_enabled( 'remote-free-extensions' ) && count( self::get_plugins() ) > 0;
	}

	/**
	 * Get the marketing plugins.
	 *
	 * @return array
	 */
	public static function get_plugins() {
		$bundles = RemoteFreeExtensions::get_extensions(
			array(
				'task-list/reach',
				'task-list/grow',
			)
		);

		return array_reduce(
			$bundles,
			function( $plugins, $bundle ) {
				$visible = array();
				foreach ( $bundle['plugins'] as $plugin ) {
					if ( $plugin->is_visible ) {
						$visible[] = $plugin;
					}
				}
				return array_merge( $plugins, $visible );
			},
			array()
		);
	}

	/**
	 * Check if the store has installed marketing extensions.
	 *
	 * @return bool
	 */
	public static function has_installed_extensions() {
		$plugins   = self::get_plugins();
		$remaining = array();
		$installed = array();

		foreach ( $plugins as $plugin ) {
			if ( ! $plugin->is_installed ) {
				$remaining[] = $plugin;
			} else {
				$installed[] = $plugin;
			}
		}

		// Make sure the task has been actioned and a marketing extension has been installed.
		if ( count( $installed ) > 0 && Task::is_task_actioned( 'marketing' ) ) {
			return true;
		}

		return false;
	}
}
