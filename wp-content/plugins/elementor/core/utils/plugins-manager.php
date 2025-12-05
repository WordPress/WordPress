<?php

namespace Elementor\Core\Utils;

require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';
require_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';

use Elementor\Plugin;
use Plugin_Upgrader;
use WP_Ajax_Upgrader_Skin;

class Plugins_Manager {

	/**
	 * @var Plugin_Upgrader
	 */
	private $upgrader;

	public function __construct( $upgrader = null ) {

		// For tests
		if ( $upgrader ) {
			$this->upgrader = $upgrader;
		} else {
			$skin = new WP_Ajax_Upgrader_Skin();
			$this->upgrader = new Plugin_Upgrader( $skin );
		}
	}

	/**
	 * Install plugin or an array of plugins.
	 *
	 * @since 3.6.2
	 *
	 * @param string|array $plugins
	 * @return array [ 'succeeded' => [] , 'failed' => [] ]
	 */
	public function install( $plugins ) {
		$succeeded = [];
		$failed = [];
		$already_installed_plugins = Plugin::$instance->wp->get_plugins();

		if ( ! is_array( $plugins ) ) {
			$plugins = [ $plugins ];
		}

		foreach ( $plugins as $plugin ) {
			if ( in_array( $plugin, $already_installed_plugins->keys()->all(), true ) ) {
				$succeeded[] = $plugin;
				continue;
			}

			$slug = $this->clean_slug( $plugin );

			$api = Plugin::$instance->wp->plugins_api('plugin_information',
				[
					'slug' => $slug,
					'fields' => [
						'short_description' => false,
						'sections' => false,
						'requires' => false,
						'rating' => false,
						'ratings' => false,
						'downloaded' => false,
						'last_updated' => false,
						'added' => false,
						'tags' => false,
						'compatibility' => false,
						'homepage' => false,
						'donate_link' => false,
					],
				]
			);

			if ( ! isset( $api->download_link ) ) {
				$failed[] = $plugin;
				continue;
			}

			$installation = $this->upgrader->install( $api->download_link );

			if ( $installation ) {
				$succeeded[] = $plugin;
			} else {
				$failed[] = $plugin;
			}
		}

		return [
			'succeeded' => $succeeded,
			'failed' => $failed,
		];
	}

	/**
	 * Activate plugin or array off plugins.
	 *
	 * @since 3.6.2
	 *
	 * @param array|string $plugins
	 * @return array [ 'succeeded' => [] , 'failed' => [] ]
	 */
	public function activate( $plugins ) {
		$succeeded = [];
		$failed = [];

		if ( ! is_array( $plugins ) ) {
			$plugins = [ $plugins ];
		}

		foreach ( $plugins as $plugin ) {
			if ( Plugin::$instance->wp->is_plugin_active( $plugin ) ) {
				$succeeded[] = $plugin;
				continue;
			}

			Plugin::$instance->wp->activate_plugin( $plugin );

			if ( Plugin::$instance->wp->is_plugin_active( $plugin ) ) {
				$succeeded[] = $plugin;
			} else {
				$failed[] = $plugin;
			}
		}

		return [
			'succeeded' => $succeeded,
			'failed' => $failed,
		];
	}

	private function clean_slug( $initial_slug ) {
		return explode( '/', $initial_slug )[0];
	}
}
