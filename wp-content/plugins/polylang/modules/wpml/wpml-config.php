<?php

/**
 * Reads and interprets the file wpml-config.xml
 * See http://wpml.org/documentation/support/language-configuration-files/
 * The language switcher configuration is not interpreted
 *
 * @since 1.0
 */
class PLL_WPML_Config {
	static protected $instance; // For singleton
	protected $xmls, $options;

	/**
	 * Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {
		if ( extension_loaded( 'simplexml' ) ) {
			$this->init();
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
	 * Finds the wpml-config.xml files to parse and setup filters
	 *
	 * @since 1.0
	 */
	public function init() {
		$this->xmls = array();

		// Plugins
		// Don't forget sitewide active plugins thanks to Reactorshop http://wordpress.org/support/topic/polylang-and-yoast-seo-plugin/page/2?replies=38#post-4801829
		$plugins = ( is_multisite() && $sitewide_plugins = get_site_option( 'active_sitewide_plugins' ) ) && is_array( $sitewide_plugins ) ? array_keys( $sitewide_plugins ) : array();
		$plugins = array_merge( $plugins, get_option( 'active_plugins', array() ) );

		foreach ( $plugins as $plugin ) {
			if ( file_exists( $file = WP_PLUGIN_DIR . '/' . dirname( $plugin ) . '/wpml-config.xml' ) && false !== $xml = simplexml_load_file( $file ) ) {
				$this->xmls[ dirname( $plugin ) ] = $xml;
			}
		}

		// Theme
		if ( file_exists( $file = ( $template = get_template_directory() ) . '/wpml-config.xml' ) && false !== $xml = simplexml_load_file( $file ) ) {
			$this->xmls[ get_template() ] = $xml;
		}

		// Child theme
		if ( ( $stylesheet = get_stylesheet_directory() ) !== $template && file_exists( $file = $stylesheet . '/wpml-config.xml' ) && false !== $xml = simplexml_load_file( $file ) ) {
			$this->xmls[ get_stylesheet() ] = $xml;
		}

		// Custom
		if ( file_exists( $file = PLL_LOCAL_DIR . '/wpml-config.xml' ) && false !== $xml = simplexml_load_file( $file ) ) {
			$this->xmls['Polylang'] = $xml;
		}

		if ( ! empty( $this->xmls ) ) {
			add_filter( 'pll_copy_post_metas', array( $this, 'copy_post_metas' ), 10, 2 );
			add_filter( 'pll_get_post_types', array( $this, 'translate_types' ), 10, 2 );
			add_filter( 'pll_get_taxonomies', array( $this, 'translate_taxonomies' ), 10, 2 );

			foreach ( $this->xmls as $context => $xml ) {
				foreach ( $xml->xpath( 'admin-texts/key' ) as $key ) {
					$attributes = $key->attributes();
					$name = (string) $attributes['name'];
					if ( PLL() instanceof PLL_Frontend ) {
						$this->options[ $name ] = $key;
						add_filter( 'option_' . $name, array( $this, 'translate_strings' ) );
					} else {
						$this->register_string_recursive( $context, get_option( $name ), $key );
					}
				}
			}
		}
	}

	/**
	 * Adds custom fields to the list of metas to copy when creating a new translation
	 *
	 * @since 1.0
	 *
	 * @param array $metas the list of custom fields to copy or synchronize
	 * @param bool  $sync  true for sync, false for copy
	 * @return array the list of custom fields to copy or synchronize
	 */
	public function copy_post_metas( $metas, $sync ) {
		foreach ( $this->xmls as $xml ) {
			foreach ( $xml->xpath( 'custom-fields/custom-field' ) as $cf ) {
				$attributes = $cf->attributes();
				if ( 'copy' == $attributes['action'] || ( ! $sync && in_array( $attributes['action'], array( 'translate', 'copy-once' ) ) ) ) {
					$metas[] = (string) $cf;
				} else {
					$metas = array_diff( $metas, array( (string) $cf ) );
				}
			}
		}
		return $metas;
	}

	/**
	 * Language and translation management for custom post types
	 *
	 * @since 1.0
	 *
	 * @param array $types list of post type names for which Polylang manages language and translations
	 * @param bool  $hide  true when displaying the list in Polylang settings
	 * @return array list of post type names for which Polylang manages language and translations
	 */
	public function translate_types( $types, $hide ) {
		foreach ( $this->xmls as $xml ) {
			foreach ( $xml->xpath( 'custom-types/custom-type' ) as $pt ) {
				$attributes = $pt->attributes();
				if ( 1 == $attributes['translate'] && ! $hide ) {
					$types[ (string) $pt ] = (string) $pt;
				} else {
					unset( $types[ (string) $pt ] ); // The theme/plugin author decided what to do with the post type so don't allow the user to change this
				}
			}
		}
		return $types;
	}

	/**
	 * Language and translation management for custom taxonomies
	 *
	 * @since 1.0
	 *
	 * @param array $taxonomies list of taxonomy names for which Polylang manages language and translations
	 * @param bool  $hide       true when displaying the list in Polylang settings
	 * @return array list of taxonomy names for which Polylang manages language and translations
	 */
	public function translate_taxonomies( $taxonomies, $hide ) {
		foreach ( $this->xmls as $xml ) {
			foreach ( $xml->xpath( 'taxonomies/taxonomy' ) as $tax ) {
				$attributes = $tax->attributes();
				if ( 1 == $attributes['translate'] && ! $hide ) {
					$taxonomies[ (string) $tax ] = (string) $tax;
				} else {
					unset( $taxonomies[ (string) $tax ] ); // the theme/plugin author decided what to do with the taxonomy so don't allow the user to change this
				}
			}
		}
		return $taxonomies;
	}

	/**
	 * Translates the strings for an option
	 *
	 * @since 1.0
	 *
	 * @param array|string $value Either a string to translate or a list of strings to translate
	 * @return array|string translated string(s)
	 */
	public function translate_strings( $value ) {
		$option = substr( current_filter(), 7 );
		return $this->translate_strings_recursive( $value, $this->options[ $option ] );
	}

	/**
	 * Recursively registers strings for a serialized option
	 *
	 * @since 1.0
	 *
	 * @param string $context the group in which the strings will be registered
	 * @param array  $options
	 * @param object $key XML node
	 */
	protected function register_string_recursive( $context, $options, $key ) {
		$children = $key->children();
		if ( count( $children ) ) {
			foreach ( $children as $child ) {
				$attributes = $child->attributes();
				$name = (string) $attributes['name'];
				if ( '*' === $name && is_array( $options ) ) {
					foreach ( $options as $n => $option ) {
						$this->register_wildcard_options_recursive( $context, $option, $n );
					}
				} elseif ( isset( $options[ $name ] ) ) {
					$this->register_string_recursive( $context, $options[ $name ], $child );
				}
			}
		} else {
			$attributes = $key->attributes();
			pll_register_string( (string) $attributes['name'], $options, $context, true ); // Multiline as in WPML
		}
	}

	/**
	 * Recursively registers strings with a wildcard
	 *
	 * @since 2.1
	 *
	 * @param string $context the group in which the strings will be registered
	 * @param array  $options
	 * @param string $name    Option name
	 */
	protected function register_wildcard_options_recursive( $context, $options, $name ) {
		if ( is_array( $options ) ) {
			foreach ( $options as $n => $option ) {
				$this->register_wildcard_options_recursive( $context, $option, $n );
			}
		} else {
			pll_register_string( $name, $options, $context );
		}
	}

	/**
	 * Recursively translates strings for a serialized option
	 *
	 * @since 1.0
	 *
	 * @param array|string $values either a string to translate or a list of strings to translate
	 * @param object       $key    XML node
	 * @return array|string translated string(s)
	 */
	protected function translate_strings_recursive( $values, $key ) {
		$children = $key->children();
		if ( count( $children ) ) {
			foreach ( $children as $child ) {
				$attributes = $child->attributes();
				$name = (string) $attributes['name'];
				if ( '*' === $name && is_array( $values ) ) {
					foreach ( $values as $n => $v ) {
						$values[ $n ] = $this->translate_wildcard_options_recursive( $v, $n );
					}
				} elseif ( isset( $values[ $name ] ) ) {
					$values[ $name ] = $this->translate_strings_recursive( $values[ $name ], $child );
				}
			}
		} else {
			$values = pll__( $values );
		}
		return $values;
	}

	/**
	 * Recursively translates strings registered by a wildcard
	 *
	 * @since 2.1
	 *
	 * @param array|string $options Either a string to translate or a list of strings to translate
	 * @param string       $name    Option name
	 * @return array|string Translated string(s)
	 */
	protected function translate_wildcard_options_recursive( $options, $name ) {
		if ( is_array( $options ) ) {
			foreach ( $options as $n => $option ) {
				$options[ $n ] = $this->translate_wildcard_options_recursive( $option, $n );
			}
		} else {
			$options = pll__( $options );
		}
		return $options;
	}
}
