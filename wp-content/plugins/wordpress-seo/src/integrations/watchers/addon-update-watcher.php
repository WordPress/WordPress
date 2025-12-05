<?php

namespace Yoast\WP\SEO\Integrations\Watchers;

use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Enables Yoast add-on auto updates when Yoast SEO is enabled and the other way around.
 *
 * Also removes the auto-update toggles from the Yoast SEO add-ons.
 */
class Addon_Update_Watcher implements Integration_Interface {

	/**
	 * ID string used by WordPress to identify the free plugin.
	 *
	 * @var string
	 */
	public const WPSEO_FREE_PLUGIN_ID = 'wordpress-seo/wp-seo.php';

	/**
	 * A list of Yoast add-on identifiers.
	 *
	 * @var string[]
	 */
	public const ADD_ON_PLUGIN_FILES = [
		'wordpress-seo-premium/wp-seo-premium.php',
		'wpseo-video/video-seo.php',
		'wpseo-local/local-seo.php', // When installing Local through a released zip, the path is different from the path on a dev environment.
		'wpseo-woocommerce/wpseo-woocommerce.php',
		'wpseo-news/wpseo-news.php',
		'acf-content-analysis-for-yoast-seo/yoast-acf-analysis.php', // When installing ACF for Yoast through a released zip, the path is different from the path on a dev environment.
	];

	/**
	 * Registers the hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'add_site_option_auto_update_plugins', [ $this, 'call_toggle_auto_updates_with_empty_array' ], 10, 2 );
		\add_action( 'update_site_option_auto_update_plugins', [ $this, 'toggle_auto_updates_for_add_ons' ], 10, 3 );
		\add_filter( 'plugin_auto_update_setting_html', [ $this, 'replace_auto_update_toggles_of_addons' ], 10, 2 );
		\add_action( 'activated_plugin', [ $this, 'maybe_toggle_auto_updates_for_new_install' ] );
	}

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return string[] The conditionals.
	 */
	public static function get_conditionals() {
		return [ Admin_Conditional::class ];
	}

	/**
	 * Replaces the auto-update toggle links for the Yoast add-ons
	 * with a text explaining that toggling the Yoast SEO auto-update setting
	 * automatically toggles the one for the setting for the add-ons as well.
	 *
	 * @param string $old_html The old HTML.
	 * @param string $plugin   The plugin.
	 *
	 * @return string The new HTML, with the auto-update toggle link replaced.
	 */
	public function replace_auto_update_toggles_of_addons( $old_html, $plugin ) {
		if ( ! \is_string( $old_html ) ) {
			return $old_html;
		}

		$not_a_yoast_addon = ! \in_array( $plugin, self::ADD_ON_PLUGIN_FILES, true );

		if ( $not_a_yoast_addon ) {
			return $old_html;
		}

		$auto_updated_plugins = \get_site_option( 'auto_update_plugins' );

		if ( $this->are_auto_updates_enabled( self::WPSEO_FREE_PLUGIN_ID, $auto_updated_plugins ) ) {
			return \sprintf(
				'<em>%s</em>',
				\sprintf(
				/* Translators: %1$s resolves to Yoast SEO. */
					\esc_html__( 'Auto-updates are enabled based on this setting for %1$s.', 'wordpress-seo' ),
					'Yoast SEO'
				)
			);
		}

		return \sprintf(
			'<em>%s</em>',
			\sprintf(
			/* Translators: %1$s resolves to Yoast SEO. */
				\esc_html__( 'Auto-updates are disabled based on this setting for %1$s.', 'wordpress-seo' ),
				'Yoast SEO'
			)
		);
	}

	/**
	 * Handles the situation where the auto_update_plugins option did not previously exist.
	 *
	 * @param string      $option The name of the option that is being created.
	 * @param array|mixed $value  The new (and first) value of the option that is being created.
	 *
	 * @return void
	 */
	public function call_toggle_auto_updates_with_empty_array( $option, $value ) {
		if ( $option !== 'auto_update_plugins' ) {
			return;
		}

		$this->toggle_auto_updates_for_add_ons( $option, $value, [] );
	}

	/**
	 * Enables premium auto updates when free are enabled and the other way around.
	 *
	 * @param string $option    The name of the option that has been updated.
	 * @param array  $new_value The new value of the `auto_update_plugins` option.
	 * @param array  $old_value The old value of the `auto_update_plugins` option.
	 *
	 * @return void
	 */
	public function toggle_auto_updates_for_add_ons( $option, $new_value, $old_value ) {
		if ( $option !== 'auto_update_plugins' ) {
			// If future versions of WordPress change this filter's behavior, our behavior should stay consistent.
			return;
		}

		if ( ! \is_array( $old_value ) || ! \is_array( $new_value ) ) {
			return;
		}

		$auto_updates_are_enabled  = $this->are_auto_updates_enabled( self::WPSEO_FREE_PLUGIN_ID, $new_value );
		$auto_updates_were_enabled = $this->are_auto_updates_enabled( self::WPSEO_FREE_PLUGIN_ID, $old_value );

		if ( $auto_updates_are_enabled === $auto_updates_were_enabled ) {
			// Auto-updates for Yoast SEO have stayed the same, so have neither been enabled or disabled.
			return;
		}

		$auto_updates_have_been_enabled = $auto_updates_are_enabled && ! $auto_updates_were_enabled;

		if ( $auto_updates_have_been_enabled ) {
			$this->enable_auto_updates_for_addons( $new_value );
			return;
		}
		else {
			$this->disable_auto_updates_for_addons( $new_value );
			return;
		}

		if ( ! $auto_updates_are_enabled ) {
			return;
		}

		$auto_updates_have_been_removed = false;
		foreach ( self::ADD_ON_PLUGIN_FILES as $addon ) {
			if ( ! $this->are_auto_updates_enabled( $addon, $new_value ) ) {
				$auto_updates_have_been_removed = true;
				break;
			}
		}

		if ( $auto_updates_have_been_removed ) {
			$this->enable_auto_updates_for_addons( $new_value );
		}
	}

	/**
	 * Trigger a change in the auto update detection whenever a new Yoast addon is activated.
	 *
	 * @param string $plugin The plugin that is activated.
	 *
	 * @return void
	 */
	public function maybe_toggle_auto_updates_for_new_install( $plugin ) {
		$not_a_yoast_addon = ! \in_array( $plugin, self::ADD_ON_PLUGIN_FILES, true );

		if ( $not_a_yoast_addon ) {
			return;
		}

		$enabled_auto_updates = \get_site_option( 'auto_update_plugins' );
		$this->toggle_auto_updates_for_add_ons( 'auto_update_plugins', $enabled_auto_updates, [] );
	}

	/**
	 * Enables auto-updates for all addons.
	 *
	 * @param string[] $auto_updated_plugins The current list of auto-updated plugins.
	 *
	 * @return void
	 */
	protected function enable_auto_updates_for_addons( $auto_updated_plugins ) {
		$plugins = \array_unique( \array_merge( $auto_updated_plugins, self::ADD_ON_PLUGIN_FILES ) );
		\update_site_option( 'auto_update_plugins', $plugins );
	}

	/**
	 * Disables auto-updates for all addons.
	 *
	 * @param string[] $auto_updated_plugins The current list of auto-updated plugins.
	 *
	 * @return void
	 */
	protected function disable_auto_updates_for_addons( $auto_updated_plugins ) {
		$plugins = \array_values( \array_diff( $auto_updated_plugins, self::ADD_ON_PLUGIN_FILES ) );
		\update_site_option( 'auto_update_plugins', $plugins );
	}

	/**
	 * Checks whether auto updates for a plugin are enabled.
	 *
	 * @param string $plugin_id            The plugin ID.
	 * @param array  $auto_updated_plugins The array of auto updated plugins.
	 *
	 * @return bool Whether auto updates for a plugin are enabled.
	 */
	protected function are_auto_updates_enabled( $plugin_id, $auto_updated_plugins ) {
		if ( $auto_updated_plugins === false || ! \is_array( $auto_updated_plugins ) ) {
			return false;
		}

		return \in_array( $plugin_id, $auto_updated_plugins, true );
	}
}
