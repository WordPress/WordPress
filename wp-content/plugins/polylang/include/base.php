<?php

/**
 * Base class for both admin and frontend
 *
 * @since 1.2
 */
abstract class PLL_Base {
	public $links_model, $model, $options;

	/**
	 * Constructor
	 *
	 * @since 1.2
	 *
	 * @param object $links_model
	 */
	public function __construct( &$links_model ) {
		$this->links_model = &$links_model;
		$this->model = &$links_model->model;
		$this->options = &$this->model->options;

		$GLOBALS['l10n_unloaded']['pll_string'] = true; // Short-circuit _load_textdomain_just_in_time() for 'pll_string' domain in WP 4.6+

		add_action( 'widgets_init', array( $this, 'widgets_init' ) );

		// User defined strings translations
		add_action( 'pll_language_defined', array( $this, 'load_strings_translations' ), 5 );
		add_action( 'change_locale', array( $this, 'load_strings_translations' ) ); // Since WP 4.7

		// Switch_to_blog
		add_action( 'switch_blog', array( $this, 'switch_blog' ), 10, 2 );
	}

	/**
	 * Registers our widgets
	 *
	 * @since 0.1
	 */
	public function widgets_init() {
		register_widget( 'PLL_Widget_Languages' );

		// Overwrites the calendar widget to filter posts by language
		if ( ! defined( 'PLL_WIDGET_CALENDAR' ) || PLL_WIDGET_CALENDAR ) {
			unregister_widget( 'WP_Widget_Calendar' );
			register_widget( 'PLL_Widget_Calendar' );
		}
	}

	/**
	 * Loads user defined strings translations
	 *
	 * @since 1.2
	 * @since 2.1.3 $locale parameter added.
	 *
	 * @param string $locale Locale. Defaults to current locale.
	 */
	public function load_strings_translations( $locale = '' ) {
		if ( empty( $locale ) ) {
			$locale = get_locale();
		}

		$language = $this->model->get_language( $locale );

		if ( ! empty( $language ) ) {
			$mo = new PLL_MO();
			$mo->import_from_db( $language );
			$GLOBALS['l10n']['pll_string'] = &$mo;
		} else {
			unset( $GLOBALS['l10n']['pll_string'] );
		}
	}

	/**
	 * Resets some variables when switching blog
	 * Applies only if Polylang is active on the new blog
	 *
	 * @since 1.5.1
	 *
	 * @param int $new_blog
	 * @param int $old_blog
	 * @return bool not used by WP but by child class
	 */
	public function switch_blog( $new_blog, $old_blog ) {
		$plugins = ( $sitewide_plugins = get_site_option( 'active_sitewide_plugins' ) ) && is_array( $sitewide_plugins ) ? array_keys( $sitewide_plugins ) : array();
		$plugins = array_merge( $plugins, get_option( 'active_plugins', array() ) );

		// 2nd test needed when Polylang is not networked activated
		// 3rd test needed when Polylang is networked activated and a new site is created
		if ( $new_blog != $old_blog && in_array( POLYLANG_BASENAME, $plugins ) && get_option( 'polylang' ) ) {
			$this->options = get_option( 'polylang' ); // Needed for menus
			$this->links_model = $this->model->get_links_model();
			return true;
		}
		return false;
	}

	/**
	 * Some backward compatibility with Polylang < 1.2
	 * Allows for example to call $polylang->get_languages_list() instead of $polylang->model->get_languages_list()
	 * This works but should be slower than the direct call, thus an error is triggered in debug mode
	 *
	 * @since 1.2
	 *
	 * @param string $func function name
	 * @param array  $args function arguments
	 */
	public function __call( $func, $args ) {
		foreach ( $this as $prop => &$obj ) {
			if ( is_object( $obj ) && method_exists( $obj, $func ) ) {
				if ( WP_DEBUG ) {
					$debug = debug_backtrace();
					$i = 1 + empty( $debug[1]['line'] ); // The file and line are in $debug[2] if the function was called using call_user_func
					trigger_error( sprintf(
						'%1$s was called incorrectly in %3$s on line %4$s: the call to $polylang->%1$s() has been deprecated in Polylang 1.2, use PLL()->%2$s->%1$s() instead.' . "\nError handler",
						$func, $prop, $debug[ $i ]['file'], $debug[ $i ]['line']
					) );
				}
				return call_user_func_array( array( $obj, $func ), $args );
			}
		}

		$debug = debug_backtrace();
		trigger_error( sprintf( 'Call to undefined function PLL()->%1$s() in %2$s on line %3$s' . "\nError handler", $func, $debug[0]['file'], $debug[0]['line'] ), E_USER_ERROR );
	}
}
