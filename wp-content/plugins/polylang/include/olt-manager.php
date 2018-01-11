<?php

/**
 * It is best practice that plugins do nothing before plugins_loaded is fired
 * so it is what Polylang intends to do
 * but some plugins load their text domain as soon as loaded, thus before plugins_loaded is fired
 * this class differs text domain loading until the language is defined
 * either in a plugins_loaded action or in a wp action ( when the language is set from content on frontend )
 *
 * @since 1.2
 */
class PLL_OLT_Manager {
	static protected $instance; // For singleton
	protected $default_locale;
	protected $list_textdomains = array(); // All text domains
	public $labels = array(); // Post types and taxonomies labels to translate

	/**
	 * Constructor: setups relevant filters
	 *
	 * @since 1.2
	 */
	public function __construct() {
		// Allows Polylang to be the first plugin loaded ;-)
		add_filter( 'pre_update_option_active_plugins', array( $this, 'make_polylang_first' ) );
		add_filter( 'pre_update_option_active_sitewide_plugins', array( $this, 'make_polylang_first' ) );

		// Overriding load text domain only on front since WP 4.7
		// FIXME test get_user_locale for backward compatibility with WP < 4.7
		if ( is_admin() && function_exists( 'get_user_locale' ) ) {
			return;
		}

		// Saves the default locale before we start any language manipulation
		$this->default_locale = get_locale();

		// Filters for text domain management
		add_filter( 'load_textdomain_mofile', array( $this, 'load_textdomain_mofile' ), 10, 2 );
		add_filter( 'gettext', array( $this, 'gettext' ), 10, 3 );
		add_filter( 'gettext_with_context', array( $this, 'gettext_with_context' ), 10, 4 );

		if ( ! Polylang::is_ajax_on_front() ) {
			// Loads text domains
			add_action( 'pll_language_defined', array( $this, 'load_textdomains' ), 2 ); // After PLL_Frontend::pll_language_defined
			add_action( 'pll_no_language_defined', array( $this, 'load_textdomains' ) );
		}
	}

	/**
	 * Access to the single instance of the class
	 *
	 * @since 1.7
	 *
	 * @return object
	 */
	static public function instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Loads text domains
	 *
	 * @since 0.1
	 */
	public function load_textdomains() {
		// Our load_textdomain_mofile filter has done its job. let's remove it before calling load_textdomain
		remove_filter( 'load_textdomain_mofile', array( $this, 'load_textdomain_mofile' ), 10, 2 );
		remove_filter( 'gettext', array( $this, 'gettext' ), 10, 3 );
		remove_filter( 'gettext_with_context', array( $this, 'gettext_with_context' ), 10, 4 );
		$new_locale = get_locale();

		// Don't try to save time for en_US as some users have theme written in another language
		// Now we can load all overridden text domains with the right language
		if ( ! empty( $this->list_textdomains ) ) {

			// Since WP 4.7 we need to reset the internal cache of _get_path_to_translation when switching from any locale to en_US
			// See WP_Locale_Switcher::change_locale()
			// FIXME test _get_path_to_translation for backward compatibility with WP < 4.7
			if ( function_exists( '_get_path_to_translation' ) ) {
				_get_path_to_translation( null, true );
			}

			foreach ( $this->list_textdomains as $textdomain ) {
				// Since WP 4.6, plugins translations are first loaded from wp-content/languages
				if ( ! load_textdomain( $textdomain['domain'], str_replace( "{$this->default_locale}.mo", "$new_locale.mo", $textdomain['mo'] ) ) ) {
					// Since WP 3.5 themes may store languages files in /wp-content/languages/themes
					if ( ! load_textdomain( $textdomain['domain'], WP_LANG_DIR . "/themes/{$textdomain['domain']}-$new_locale.mo" ) ) {
						// Since WP 3.7 plugins may store languages files in /wp-content/languages/plugins
						load_textdomain( $textdomain['domain'], WP_LANG_DIR . "/plugins/{$textdomain['domain']}-$new_locale.mo" );
					}
				}
			}
		}

		// First remove taxonomies and post_types labels that we don't need to translate
		$taxonomies = get_taxonomies( array( '_pll' => true ) );
		$post_types = get_post_types( array( '_pll' => true ) );

		// We don't need to translate core taxonomies and post types labels when setting the language from the url
		// As they will be translated when registered the second time
		if ( ! did_action( 'setup_theme' ) ) {
			$taxonomies = array_merge( get_taxonomies( array( '_builtin' => true ) ), $taxonomies );
			$post_types = array_merge( get_post_types( array( '_builtin' => true ) ), $post_types );
		}

		// Translate labels of post types and taxonomies
		foreach ( array_diff_key( $GLOBALS['wp_taxonomies'], array_flip( $taxonomies ) ) as $tax ) {
			$this->translate_labels( $tax );
		}
		foreach ( array_diff_key( $GLOBALS['wp_post_types'], array_flip( $post_types ) ) as $pt ) {
			$this->translate_labels( $pt );
		}

		// Act only if the language has not been set early ( before default textdomain loading and $wp_locale creation )
		if ( did_action( 'after_setup_theme' ) ) {
			// Reinitializes wp_locale for weekdays and months
			unset( $GLOBALS['wp_locale'] );
			$GLOBALS['wp_locale'] = new WP_Locale();
		}

		/**
		 * Fires after the post types and taxonomies labels have been translated
		 * This allows plugins to translate text the same way we do for post types and taxonomies labels
		 *
		 * @since 1.2
		 *
		 * @param array $labels list of strings to trnaslate
		 */
		do_action_ref_array( 'pll_translate_labels', array( &$this->labels ) );

		// Free memory
		unset( $this->default_locale, $this->list_textdomains, $this->labels );
	}

	/**
	 * FIXME: Backward compatibility with Polylang for WooCommerce < 0.3.4
	 * To remove in Polylang 2.1
	 *
	 * @since 0.1
	 *
	 * @param bool   $bool   not used
	 * @param string $domain text domain name
	 * @param string $mofile translation file name
	 * @return bool
	 */
	public function mofile( $bool, $domain, $mofile ) {
		return $bool;
	}

	/**
	 * Saves all text domains in a table for later usage
	 * It replaces the 'override_load_textdomain' filter used since 0.1
	 *
	 * @since 2.0.4
	 *
	 * @param string $mofile translation file name
	 * @param string $domain text domain name
	 * @return bool
	 */
	public function load_textdomain_mofile( $mofile, $domain ) {
		// On multisite, 2 files are sharing the same domain so we need to distinguish them
		if ( 'default' === $domain && false !== strpos( $mofile, '/ms-' ) ) {
			$this->list_textdomains['ms-default'] = array( 'mo' => $mofile, 'domain' => $domain );
		} else {
			$this->list_textdomains[ $domain ] = array( 'mo' => $mofile, 'domain' => $domain );
		}
		return ''; // Hack to prevent WP loading text domains as we will load them all later
	}

	/**
	 * Saves post types and taxonomies labels for a later usage
	 *
	 * @since 0.9
	 *
	 * @param string $translation not used
	 * @param string $text        string to translate
	 * @param string $domain      text domain
	 * @return string unmodified $translation
	 */
	public function gettext( $translation, $text, $domain ) {
		if ( is_string( $text ) ) { // Avoid a warning with some buggy plugins which pass an array
			$this->labels[ $text ] = array( 'domain' => $domain );
		}
		return $translation;
	}

	/**
	 * Saves post types and taxonomies labels for a later usage
	 *
	 * @since 0.9
	 *
	 * @param string $translation not used
	 * @param string $text        string to translate
	 * @param string $context     some comment to describe the context of string to translate
	 * @param string $domain      text domain
	 * @return string unmodified $translation
	 */
	public function gettext_with_context( $translation, $text, $context, $domain ) {
		$this->labels[ $text ] = array( 'domain' => $domain, 'context' => $context );
		return $translation;
	}

	/**
	 * Translates post types and taxonomies labels once the language is known
	 *
	 * @since 0.9
	 *
	 * @param object $type either a post type or a taxonomy
	 */
	public function translate_labels( $type ) {
		// Use static array to avoid translating several times the same ( default ) labels
		static $translated = array();

		foreach ( $type->labels as $key => $label ) {
			if ( is_string( $label ) && isset( $this->labels[ $label ] ) ) {
				if ( empty( $translated[ $label ] ) ) {
					$type->labels->$key = $translated[ $label ] = isset( $this->labels[ $label ]['context'] ) ?
						_x( $label, $this->labels[ $label ]['context'], $this->labels[ $label ]['domain'] ) :
						__( $label, $this->labels[ $label ]['domain'] );
				}
				else {
					$type->labels->$key = $translated[ $label ];
				}
			}
		}
	}

	/**
	 * Allows Polylang to be the first plugin loaded ;- )
	 *
	 * @since 1.2
	 *
	 * @param array $plugins list of active plugins
	 * @return array list of active plugins
	 */
	public function make_polylang_first( $plugins ) {
		if ( $key = array_search( POLYLANG_BASENAME, $plugins ) ) {
			unset( $plugins[ $key ] );
			array_unshift( $plugins, POLYLANG_BASENAME );
		}
		return $plugins;
	}
}
