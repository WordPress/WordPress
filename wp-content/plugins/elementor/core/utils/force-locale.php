<?php
namespace Elementor\Core\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Force translation to use a specific locale.
 *
 * A hacky class to force any translation functions in the call-stack between the
 * `force()` & `reset()` methods to use a specific locale.
 */
class Force_Locale {

	/**
	 * @var string Locale to force (e.g. `he_IL`).
	 */
	private $new_locale;

	/**
	 * @var string Original locale before forcing.
	 */
	private $original_locale;

	/**
	 * @var \WP_Textdomain_Registry
	 */
	private $original_textdomain_registry;

	/**
	 * @var \Closure Filter reference `pre_determine_locale`.
	 */
	private $filter;

	public function __construct( $new_locale, $original_locale = null ) {
		$this->new_locale = $new_locale;

		$this->original_locale = $original_locale ? $original_locale : determine_locale();

		$this->filter = function() use ( $new_locale ) {
			return $new_locale;
		};
	}

	/**
	 * Force the translations to use a specific locale.
	 *
	 * @return void
	 */
	public function force() {
		switch_to_locale( $this->new_locale );

		/**
		 * Reset the \WP_Textdomain_Registry instance to clear its cache.
		 *
		 * @see https://github.com/WordPress/wordpress-develop/blob/799d7dc86f5b07b17f7a418948fc851bd2fc334b/src/wp-includes/class-wp-textdomain-registry.php#L179-L187
		 * @see https://github.com/WordPress/wordpress-develop/blob/799d7dc86f5b07b17f7a418948fc851bd2fc334b/tests/phpunit/tests/l10n/wpLocaleSwitcher.php#L19-L31
		 */
		$this->reset_textdomain_registry();

		/**
		 * Reset l10n in order to clear the translations cache.
		 *
		 * @see https://github.com/WordPress/wordpress-develop/blob/2437ef5130f10153bc4fffa412d4f37e65e3d66b/src/wp-includes/l10n.php#L1324
		 * @see https://github.com/WordPress/wordpress-develop/blob/2437ef5130f10153bc4fffa412d4f37e65e3d66b/src/wp-includes/l10n.php#L1222
		 * @see https://github.com/WordPress/wordpress-develop/blob/2437ef5130f10153bc4fffa412d4f37e65e3d66b/src/wp-includes/l10n.php#L821
		 */
		$this->reset_l10n();

		/**
		 * Force the translations of `$new_locale` to be loaded.
		 *
		 * @see https://github.com/WordPress/wordpress-develop/blob/2437ef5130f10153bc4fffa412d4f37e65e3d66b/src/wp-includes/l10n.php#L1294
		 */
		add_filter( 'pre_determine_locale', $this->filter );
	}

	/**
	 * Restore the original locale and cleanup filters, etc.
	 *
	 * @return void
	 */
	public function restore() {
		$this->restore_textdomain_registry();

		$this->reset_l10n();

		switch_to_locale( $this->original_locale );

		remove_filter( 'pre_determine_locale', $this->filter );
	}

	private function reset_textdomain_registry() {
		if ( ! class_exists( '\WP_Textdomain_Registry' ) ) {
			return;
		}

		/** @var \WP_Textdomain_Registry $wp_textdomain_registry */
		global $wp_textdomain_registry;

		$this->original_textdomain_registry = $wp_textdomain_registry;

		$wp_textdomain_registry = new \WP_Textdomain_Registry();
	}

	private function restore_textdomain_registry() {
		if ( ! $this->original_textdomain_registry ) {
			return;
		}

		/** @var \WP_Textdomain_Registry $wp_textdomain_registry */
		global $wp_textdomain_registry;

		$wp_textdomain_registry = $this->original_textdomain_registry;
	}

	/**
	 * Reset the l10n global variables.
	 *
	 * @return void
	 */
	private function reset_l10n() {
		global $l10n, $l10n_unloaded;

		if ( is_array( $l10n ) ) {
			foreach ( $l10n as $domain => $l10n_data ) {
				unset( $l10n[ $domain ] );
			}
		}

		if ( is_array( $l10n_unloaded ) ) {
			foreach ( $l10n_unloaded as $domain => $l10n_unloaded_data ) {
				unset( $l10n_unloaded[ $domain ] );
			}
		}
	}
}
