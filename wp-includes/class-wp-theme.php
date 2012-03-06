<?php
/**
 * WP_Theme Class
 *
 * @package WordPress
 * @subpackage Theme
 */

final class WP_Theme implements ArrayAccess {

	/**
	 * Headers for style.css files.
	 *
	 * @static
	 * @access private
	 * @var array
	 */
	private static $file_headers = array(
		'Name'        => 'Theme Name',
		'ThemeURI'    => 'Theme URI',
		'Description' => 'Description',
		'Author'      => 'Author',
		'AuthorURI'   => 'Author URI',
		'Version'     => 'Version',
		'Template'    => 'Template',
		'Status'      => 'Status',
		'Tags'        => 'Tags',
		'TextDomain'  => 'Text Domain',
		'DomainPath'  => 'Domain Path',
	);

	/**
	 * Default themes.
	 *
	 * @static
	 * @access private
	 * @var array
	 */
	private static $default_themes = array(
		'classic'      => 'WordPress Classic',
		'default'      => 'WordPress Default',
		'twentyten'    => 'Twenty Ten',
		'twentyeleven' => 'Twenty Eleven',
	);

	/**
	 * Absolute path to the theme root, usually wp-content/themes
	 *
	 * @access private
	 * @var string
	 */
	private $theme_root;

	/**
	 * Header data from the theme's style.css file.
	 *
	 * @access private
	 * @var array
	 */
	private $headers = array();

	/**
	 * Header data from the theme's style.css file after being sanitized.
	 *
	 * @access private
	 * @var array
	 */
	private $headers_sanitized;

	/**
	 * Header name from the theme's style.css after being translated.
	 *
	 * Cached due to sorting functions running over the translated name.
	 */
	private $name_translated;

	/**
	 * Errors encountered when initializing the theme.
	 *
	 * @access private
	 * @var WP_Error
	 */
	private $errors;

	/**
	 * The directory name of the theme's files, inside the theme root.
	 *
	 * In the case of a child theme, this is directory name of the the child theme.
	 * Otherwise, 'stylesheet' is the same as 'template'.
	 *
	 * @access private
	 * @var string
	 */
	private $stylesheet;

	/**
	 * The directory name of the theme's files, inside the theme root.
	 *
	 * In the case of a child theme, this is the directory name of the parent theme.
	 * Otherwise, 'template' is the same as 'stylesheet'.
	 *
	 * @access private
	 * @var string
	 */
	private $template;

	/**
	 * A reference to the parent theme, in the case of a child theme.
	 *
	 * @access private
	 * @var WP_Theme
	 */
	private $parent;

	/**
	 * Flag for whether the theme's textdomain is loaded.
	 *
	 * @access private
	 * @var bool
	 */
	private $textdomain_loaded;

	/**
	 * Flag for whether the themes cache bucket should be persistently cached.
	 *
	 * Default is false. Can be set with the wp_cache_themes_persistently filter.
	 *
	 * @access private
	 * @var bool
	 */
	private static $persistently_cache;

	/**
	 * Expiration time for the themes cache bucket.
	 *
	 * By default the bucket is not cached, so this value is useless.
	 *
	 * @access private
	 * @var bool
	 */
	private static $cache_expiration = 7200;

	/**
	 * Constructor for WP_Theme.
	 *
	 * @param string $theme_dir Directory of the theme within the theme_root.
	 * @param string $theme_root Theme root.
	 * @param WP_Error|null $child If this theme is a parent theme, the child may be passed for validation purposes.
	 */
	public function __construct( $theme_dir, $theme_root, $child = null ) {

		// Initialize caching on first run.
		if ( ! isset( self::$persistently_cache ) ) {
			self::$persistently_cache = apply_filters( 'wp_cache_themes_persistently', false, 'WP_Theme' );
			if ( self::$persistently_cache ) {
				wp_cache_add_global_groups( 'themes' );
				if ( is_int( self::$persistently_cache ) )
					self::$cache_expiration = self::$persistently_cache;
			} else {
				wp_cache_add_non_persistent_groups( 'themes' );
			}
		}

		$this->theme_root = $theme_root;
		$this->stylesheet = $theme_dir;
		$theme_file = $this->stylesheet . '/style.css';

		$cache = $this->cache_get( 'theme' );

		if ( is_array( $cache ) ) {
			foreach ( array( 'errors', 'headers', 'template' ) as $key ) {
				if ( isset( $cache[ $key ] ) )
					$this->$key = $cache[ $key ];
			}
			if ( $this->errors )
				return;
			if ( isset( $cache['theme_root_template'] ) )
				$theme_root_template = $cache['theme_root_template'];
		} elseif ( ! file_exists( $this->theme_root . '/' . $theme_file ) ) {
			$this->headers['Name'] = $this->stylesheet;
			$this->errors = new WP_Error( 'theme_no_stylesheet', __( 'Stylesheet is missing.' ) );
			$this->cache_add( 'theme', array( 'headers' => $this->headers, 'errors' => $this->errors, 'stylesheet' => $this->stylesheet ) );
			if ( ! file_exists( $this->theme_root ) ) // Don't cache this one.
				$this->errors->add( 'theme_root_missing', __( 'ERROR: The themes directory is either empty or doesn&#8217;t exist. Please check your installation.' ) );
			return;
		} elseif ( ! is_readable( $this->theme_root . '/' . $theme_file ) ) {
			$this->headers['Name'] = $this->stylesheet;
			$this->errors = new WP_Error( 'theme_stylesheet_not_readable', __( 'Stylesheet is not readable.' ) );
			$this->cache_add( 'theme', array( 'headers' => $this->headers, 'errors' => $this->errors, 'stylesheet' => $this->stylesheet ) );
			return;
		} else {
			$this->headers = get_file_data( $this->theme_root . '/' . $theme_file, self::$file_headers, 'theme' );
			// Default themes always trump their pretenders.
			// Properly identify default themes that are inside a directory within wp-content/themes.
			if ( $default_theme_slug = array_search( $this->headers['Name'], self::$default_themes ) ) {
				if ( basename( $this->stylesheet ) != $default_theme_slug )
					$this->headers['Name'] .= '/' . $this->stylesheet;
			}
		}

		// (If template is set from cache, we know it's good.)
		if ( ! $this->template && ! ( $this->template = $this->get('Template') ) ) {
			if ( file_exists( $this->theme_root . '/' . $this->stylesheet . '/index.php' ) ) {
				$this->template = $this->stylesheet;
			} else {
				$this->errors = new WP_Error( 'theme_no_index', __( 'Template is missing.' ) );
				$this->cache_add( 'theme', array( 'headers' => $this->headers, 'errors' => $this->errors, 'stylesheet' => $this->stylesheet ) );
				return;
			}
		}

		// If we got our data from cache, we can assume that 'template' is pointing to the right place.
		if ( ! is_array( $cache ) && $this->template != $this->stylesheet && ! file_exists( $this->theme_root . '/' . $this->template . '/index.php' ) ) {
			// If we're in a directory of themes inside /themes, look for the parent nearby.
			// wp-content/themes/directory-of-themes/*
			$parent_dir = dirname( $this->stylesheet );
			if ( '.' != $parent_dir && file_exists( $this->theme_root . '/' . $parent_dir . '/' . $this->template . '/index.php' ) ) {
				$this->template = $parent_dir . '/' . $this->template;
			} elseif ( ( $directories = search_theme_directories() ) && isset( $directories[ $this->template ] ) ) {
				// Look for the template in the search_theme_directories() results, in case it is in another theme root.
				// We don't look into directories of themes, just the theme root.
				$theme_root_template = $directories[ $this->template ]['theme_root'];
			} else {
				// Parent theme is missing.
				$this->errors = new WP_Error( 'theme_no_parent', sprintf( __( 'The parent theme is missing. Please install the "%s" parent theme.' ), $this->template ) );
				$this->cache_add( 'theme', array( 'headers' => $this->headers, 'errors' => $this->errors, 'stylesheet' => $this->stylesheet, 'template' => $this->template ) );
				return;
			}
		}

		// Set the parent, if we're a child theme.
		if ( $this->template != $this->stylesheet ) {
			// If we are a parent, then there is a problem. Only two generations allowed! Cancel things out.
			if ( is_a( $child, 'WP_Theme' ) && $child->template == $this->stylesheet ) {
				$child->parent = null;
				$child->errors = new WP_Error( 'theme_parent_invalid', sprintf( __( 'The "%s" theme is not a valid parent theme.' ), $child->template ) );
				$child->cache_add( 'theme', array( 'headers' => $child->headers, 'errors' => $child->errors, 'stylesheet' => $child->stylesheet, 'template' => $child->template ) );
				// The two themes actually reference each other with the Template header.
				if ( $child->stylesheet == $this->template ) {
					$this->errors = new WP_Error( 'theme_parent_invalid', sprintf( __( 'The "%s" theme is not a valid parent theme.' ), $this->template ) );
					$this->cache_add( 'theme', array( 'headers' => $this->headers, 'errors' => $this->errors, 'stylesheet' => $this->stylesheet, 'template' => $this->template ) );
				}
				return;
			}
			// Set the parent. Pass the current instance so we can do the crazy checks above and assess errors.
			$this->parent = new WP_Theme( $this->template, isset( $theme_root_template ) ? $theme_root_template : $this->theme_root, $this );
		}

		// We're good. If we didn't retrieve from cache, set it.
		if ( ! is_array( $cache ) ) {
			$cache = array( 'headers' => $this->headers, 'errors' => $this->errors, 'stylesheet' => $this->stylesheet, 'template' => $this->template );
			// If the parent theme is in another root, we'll want to cache this. Avoids an entire branch of filesystem calls above.
			if ( isset( $theme_root_template ) )
				$cache['theme_root_template'] = $theme_root_template;
			$this->cache_add( 'theme', $cache );
		}
	}

	/**
	 * When converting the object to a string, the theme name is returned.
	 *
	 * @return string Theme name, ready for display (translated)
	 */
	function __toString() {
		return (string) $this->display('Name');
	}

	/**
	 * __isset() magic method for properties formerly returned by current_theme_info()
	 */
	public function __isset( $offset ) {
		static $properties = array(
			'name', 'title', 'version', 'parent_theme', 'template_dir', 'stylesheet_dir', 'template', 'stylesheet',
			'screenshot', 'description', 'author', 'tags', 'theme_root', 'theme_root_uri',
		);

		return in_array( $offset, $properties );
	}

	/**
	 * __get() magic method for properties formerly returned by current_theme_info()
	 */
	public function __get( $offset ) {
		switch ( $offset ) {
			case 'name' :
			case 'title' :
				return $this->get('Name');
			case 'version' :
				return $this->get('Version');
			case 'parent_theme' :
				return $this->parent ? $this->parent->get('Name') : '';
			case 'template_dir' :
				return $this->get_template_directory();
			case 'stylesheet_dir' :
				return $this->get_stylesheet_directory();
			case 'template' :
				return $this->get_template();
			case 'stylesheet' :
				return $this->get_stylesheet();
			case 'screenshot' :
				return $this->get_screenshot( 'relative' );
			// 'author' and 'description' did not previously return translated data.
			case 'description' :
				return $this->display('Description');
			case 'author' :
				return $this->display('Author');
			case 'tags' :
				return $this->get( 'Tags' );
			case 'theme_root' :
				return $this->get_theme_root();
			case 'theme_root_uri' :
				return $this->get_theme_root_uri();
			// For cases where the array was converted to an object.
			default :
				return $this->offsetGet( $offset );
		}
	}

	/**
	 * Method to implement ArrayAccess for keys formerly returned by get_themes()
	 */
	public function offsetSet( $offset, $value ) {}

	/**
	 * Method to implement ArrayAccess for keys formerly returned by get_themes()
	 */
	public function offsetUnset( $offset ) {}

	/**
	 * Method to implement ArrayAccess for keys formerly returned by get_themes()
	 */
	public function offsetExists( $offset ) {
		static $keys = array(
			'Name', 'Version', 'Status', 'Title', 'Author', 'Author Name', 'Author URI', 'Description',
			'Template', 'Stylesheet', 'Template Files', 'Stylesheet Files', 'Template Dir', 'Stylesheet Dir',
			 'Screenshot', 'Tags', 'Theme Root', 'Theme Root URI', 'Parent Theme',
		);

		return in_array( $offset, $keys );
	}

	/**
	 * Method to implement ArrayAccess for keys formerly returned by get_themes()
	 */
	public function offsetGet( $offset ) {
		switch ( $offset ) {
			case 'Name' :
			case 'Version' :
			case 'Status' :
				return $this->get( $offset );
			case 'Title' :
				return $this->get('Name');
			// Author, Author Name, Author URI, and Description did not
			// previously return translated data. We are doing so now.
			// Title and Name could have been used as the key for get_themes(),
			// so both to remain untranslated for back compatibility.
			case 'Author' :
				return $this->display( 'Author');
			case 'Author Name' :
				return $this->display( 'Author', false);
			case 'Author URI' :
				return $this->display('AuthorURI');
			case 'Description' :
				return $this->display( 'Description');
			case 'Template' :
				return $this->get_template();
			case 'Stylesheet' :
				return $this->get_stylesheet();
			case 'Template Files' :
				$files = $this->get_files('php');
				foreach ( $files as &$file )
					$file = $this->theme_root . '/' . $file;
				return $files;
			case 'Stylesheet Files' :
				$files = $this->get_files('css');
				foreach ( $files as &$file )
					$file = $this->theme_root . '/' . $file;
				return $files;
			case 'Template Dir' :
				return $this->get_template_directory();
			case 'Stylesheet Dir' :
				return $this->get_stylesheet_directory();
			case 'Screenshot' :
				return $this->get_screenshot( 'relative' );
			case 'Tags' :
				return $this->get('Tags');
			case 'Theme Root' :
				return $this->get_theme_root();
			case 'Theme Root URI' :
				return $this->get_theme_root_uri();
			case 'Parent Theme' :
				return $this->parent ? $this->parent->get('Name') : '';
			default :
				return null;
		}
	}

	/**
	 * Returns errors property.
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @return WP_Error|bool WP_Error if there are errors, or false.
	 */
	public function errors() {
		return is_wp_error( $this->errors ) ? $this->errors : false;
	}

	/**
	 * Returns reference to the parent theme.
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @return WP_Theme|bool Parent theme, or false if the current theme is not a child theme.
	 */
	public function parent() {
		return isset( $this->parent ) ? $this->parent : false;
	}

	/**
	 * Adds theme data to cache.
	 *
	 * Cache entries keyed by the theme and the type of data.
	 *
	 * @access private
	 * @since 3.4.0
	 *
	 * @param string $key Type of data to store (theme, screenshot, screenshot_count, files, headers)
	 * @param string $data Data to store
	 * @return bool Return value from wp_cache_add()
	 */
	private function cache_add( $key, $data ) {
		return wp_cache_add( $key . '-' . $this->theme_root . '/' . $this->stylesheet, $data, 'themes', self::$cache_expiration );
	}

	/**
	 * Gets theme data from cache.
	 *
	 * Cache entries are keyed by the theme and the type of data.
	 *
	 * @access private
	 * @since 3.4.0
	 *
	 * @param string $key Type of data to retrieve (theme, screenshot, screenshot_count, files, headers)
	 * @return mixed Retrieved data
	 */
	private function cache_get( $key ) {
		return wp_cache_get( $key . '-' . $this->theme_root . '/' . $this->stylesheet, 'themes' );
	}

	/**
	 * Clears the cache for the theme.
	 *
	 * @access public
	 * @since 3.4.0
	 */
	public function cache_delete() {
		foreach ( array( 'theme', 'screenshot', 'screenshot_count', 'files', 'headers' ) as $key )
			wp_cache_delete( $key . '-' . $this->theme_root . '/' . $this->stylesheet, 'themes' );
	}

	/**
	 * Gets a theme header.
	 *
	 * The header is sanitized.
	 *
	 * @access public
	 * @since 3.4.0
	 *
	 * @param string $header Theme header. Name, Description, Author, Version, ThemeURI, AuthorURI, Status.
	 * @return string String on success, false on failure.
	 */
	public function get( $header ) {
		if ( ! isset( $this->headers[ $header ] ) )
			return false;

		if ( ! isset( $this->headers_sanitized ) ) {
			$this->headers_sanitized = $this->cache_get( 'headers' );
			if ( ! is_array( $this->headers_sanitized ) )
				$this->headers_sanitized = array();
		}

		if ( isset( $this->headers_sanitized[ $header ] ) )
			return $this->headers_sanitized[ $header ];

		// If themes are a persistent group, sanitize everything and cache it. One cache add is better than many cache sets.
		if ( self::$persistently_cache ) {
			foreach ( array_keys( $this->headers ) as $_header )
				$this->headers_sanitized[ $_header ] = $this->sanitize_header( $_header, $this->headers[ $_header ] );
			$this->cache_add( 'headers', $this->headers_sanitized );
		} else {
			$this->headers_sanitized[ $header ] = $this->sanitize_header( $header, $this->headers[ $header ] );
		}

		return $this->headers_sanitized[ $header ];
	}

	/**
	 * Gets a theme header ready for display (marked up, translated).
	 *
	 * @access public
	 * @since 3.4.0
	 *
	 * @param string $header Theme header. Name, Description, Author, Version, ThemeURI, AuthorURI, Status.
	 * @param bool $markup Optional. Whether to mark up the header. Defaults to true.
	 * @param bool $translate Optional. Whether to translate the header. Defaults to true.
	 * @return string Processed header, false on failure.
	 */
	public function display( $header, $markup = true, $translate = true ) {
		$value = $this->get( $header );
		if ( false === $value || '' === $value )
			return $value;

		if ( ! $this->load_textdomain() )
			$translate = false;

		if ( $translate )
			$value = $this->translate_header( $header, $value );

		if ( $markup )
			$value = $this->markup_header( $header, $value, $translate );

		return $value;
	}

	/**
	 * Sanitize a theme header.
	 *
	 * @param string $header Theme header. Name, Description, Author, Version, ThemeURI, AuthorURI, Status.
	 * @param string $value Value to sanitize.
	 */
	private function sanitize_header( $header, $value ) {
		switch ( $header ) {
			case 'Status' :
				if ( ! $value ) {
					$value = 'public';
					break;
				}
				// Fall through otherwise.
			case 'Name' :
			case 'Author' :
				static $header_tags = array(
					'abbr'    => array( 'title' => true ),
					'acronym' => array( 'title' => true ),
					'code'    => true,
					'em'      => true,
					'strong'  => true,
				);
				$value = wp_kses( $value, $header_tags );
				break;
			case 'Description' :
				static $header_tags_with_a = array(
					'a'       => array( 'href' => true, 'title' => true ),
					'abbr'    => array( 'title' => true ),
					'acronym' => array( 'title' => true ),
					'code'    => true,
					'em'      => true,
					'strong'  => true,
				);
				$value = wp_kses( $value, $header_tags_with_a );
				break;
			case 'ThemeURI' :
			case 'AuthorURI' :
				$value = esc_url( $value );
				break;
			case 'Tags' :
				$value = array_filter( array_map( 'trim', explode( ',', strip_tags( $value ) ) ) );
				break;
		}

		return $value;
	}

	/**
	 * Mark up a theme header.
	 *
	 * @access private
	 * @since 3.4.0
	 *
	 * @param string $header Theme header. Name, Description, Author, Version, ThemeURI, AuthorURI, Status.
	 * @param string $value Value to mark up.
	 * @param string $translate Whether the header has been translated.
	 * @return string Value, marked up.
	 */
	private function markup_header( $header, $value, $translate ) {
		switch ( $header ) {
			case 'Description' :
				$value = wptexturize( $value );
				break;
			case 'Author' :
				if ( $this->get('AuthorURI') ) {
					static $attr = null;
					if ( ! isset( $attr ) )
						$attr = esc_attr__( 'Visit author homepage' );
					$value = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', $this->display( 'AuthorURI', true, $translate ), $attr, $value );
				} elseif ( ! $value ) {
					$value = __( 'Anonymous' );
				}
				break;
			case 'Tags' :
				static $comma = null;
				if ( ! isset( $comma ) ) {
					/* translators: used between list items, there is a space after the comma */
					$comma = __( ', ' );
				}
				$value = implode( $comma, $value );
				break;
		}

		return $value;
	}

	/**
	 * Translate a theme header.
	 *
	 * @access private
	 * @since 3.4.0
	 *
	 * @param string $header Theme header. Name, Description, Author, Version, ThemeURI, AuthorURI, Status.
	 * @param string $value Value to translate.
	 * @return string Translated value.
	 */
	private function translate_header( $header, $value ) {
		switch ( $header ) {
			case 'Name' :
				// Cached for sorting reasons.
				if ( isset( $this->name_translated ) )
					return $this->name_translated;
				$this->name_translated = translate( $value, $this->get('TextDomain' ) );
				return $this->name_translated;
			case 'Tags' :
				if ( empty( $value ) )
					return $value;

				static $tags_list;
				if ( ! isset( $tags_list ) ) {
					$tags_list = array();
					$feature_list = get_theme_feature_list( false ); // No API
					foreach ( $feature_list as $tags )
						$tags_list += $tags;
				}

				foreach ( $value as &$tag ) {
					if ( isset( $tags_list[ $tag ] ) )
						$tag = $tags_list[ $tag ];
				}

				return $value;
				break;
			default :
				$value = translate( $value, $this->get('TextDomain') );
		}
		return $value;
	}

	/**
	 * The directory name of the theme's "stylesheet" files, inside the theme root.
	 *
	 * In the case of a child theme, this is directory name of the the child theme.
	 * Otherwise, get_stylesheet() is the same as get_template().
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @return string Stylesheet
	 */
	public function get_stylesheet() {
		return $this->stylesheet;
	}

	/**
	 * The directory name of the theme's "template" files, inside the theme root.
	 *
	 * In the case of a child theme, this is the directory name of the parent theme.
	 * Otherwise, the get_template() is the same as get_stylesheet().
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @return string Template
	 */
	public function get_template() {
		return $this->template;
	}

	/**
	 * Whether a theme is a child theme.
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @return bool True if a theme is a child theme, false otherwise.
	 */
	public function is_child_theme() {
		return $this->template !== $this->stylesheet;
	}

	/**
	 * Returns the absolute path to the directory of a theme's "stylesheet" files.
	 *
	 * In the case of a child theme, this is the absolute path to the directory
	 * of the child theme's files.
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @return string Absolute path of the stylesheet directory.
	 */
	public function get_stylesheet_directory() {
		if ( $this->errors && in_array( 'theme_root_missing', $this->errors->get_error_codes() ) )
			return '';

		return $this->theme_root . '/' . $this->stylesheet;
	}

	/**
	 * Returns the absolute path to the directory of a theme's "template" files.
	 *
	 * In the case of a child theme, this is the absolute path to the directory
	 * of the parent theme's files.
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @return string Absolute path of the template directory.
	 */
	public function get_template_directory() {
		if ( $this->parent )
			$theme_root = $this->parent->theme_root;
		else
			$theme_root = $this->theme_root;

		return $theme_root . '/' . $this->template;
	}

	/**
	 * Returns the URL to the directory of a theme's "stylesheet" files.
	 *
	 * In the case of a child theme, this is the URL to the directory of the
	 * child theme's files.
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @return string URL to the stylesheet directory.
	 */
	public function get_stylesheet_directory_uri() {
		return $this->get_theme_root_uri() . '/' . $this->stylesheet;
	}

	/**
	 * Returns the URL to the directory of a theme's "template" files.
	 *
	 * In the case of a child theme, this is the URL to the directory of the
	 * parent theme's files.
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @return string URL to the template directory.
	 */
	public function get_template_directory_uri() {
		if ( $this->parent )
			$theme_root_uri = $this->parent->get_theme_root_uri();
		else
			$theme_root_uri = $this->get_theme_root_uri();

		return $theme_root . '/' . $this->template;
	}

	/**
	 * The absolute path to the directory of the theme root.
	 *
	 * This is typically the absolute path to wp-content/themes.
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @return string Theme root.
	 */
	public function get_theme_root() {
		return $this->theme_root;
	}

	/**
	 * Returns the URL to the directory of the theme root.
	 *
	 * This is typically the absolute path to wp-content/themes.
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @return string Theme root URI.
	 */
	public function get_theme_root_uri() {
		if ( 0 === strpos( WP_CONTENT_DIR, $this->theme_root ) )
			return str_replace( WP_CONTENT_DIR, content_url(), $this->theme_root );
		// Give up, send it off to the filter.
		return get_theme_root_uri( $this->stylesheet );
	}

	/**
	 * Returns the main screenshot file for the theme.
	 *
	 * The main screenshot is called screenshot.png. gif and jpg extensions are also allowed.
	 *
	 * Screenshots for a theme must be in the stylesheet directory. (In the case of a child
	 * theme, a parent theme's screenshots are inherited.)
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @param string $uri Type of URL to return, either 'relative' or an absolute URI. Defaults to absolute URI.
	 * @return mixed Screenshot file. False if the theme does not have a screenshot.
	 */
	public function get_screenshot( $uri = 'uri' ) {
		$screenshot = $this->cache_get( 'screenshot' );
		if ( $screenshot ) {
			if ( 'relative' == $uri )
				return $screenshot;
			return $this->get_stylesheet_directory_uri() . '/' . $screenshot;
		} elseif ( 0 === $screenshot ) {
			return false;
		}

		foreach ( array( 'png', 'gif', 'jpg', 'jpeg' ) as $ext ) {
			if ( file_exists( $this->get_stylesheet_directory() . "/screenshot.$ext" ) ) {
				$this->cache_add( 'screenshot', 'screenshot.' . $ext );
				if ( 'relative' == $uri )
					return 'screenshot.' . $ext;
				return $this->get_stylesheet_directory_uri() . '/' . 'screenshot.' . $ext;
			}
		}

		$this->cache_add( 'screenshot', 0 );
		$this->cache_add( 'screenshot_count', 0 );
		return false;
	}

	/**
	 * Returns the number of screenshots for a theme.
	 *
	 * The first screenshot may be called screenshot.png, .gif, or .jpg. Subsequent
	 * screenshots can be screenshot-2.png, screenshot-3.png, etc. The count must
	 * be consecutive for screenshots to be counted, and all screenshots beyond the
	 * initial one must be image/png files.
	 *
	 * @see WP_Theme::get_screenshot()
	 * @since 3.4.0
	 * @access public
	 *
	 * @return int Number of screenshots. Can be 0.
	 */
	public function get_screenshot_count() {
		$screenshot_count = $this->cache_get( 'screenshot_count' );
		if ( is_numeric( $screenshot_count ) )
			return $screenshot_count;

		// This will set the screenshot cache.
		// If there is no screenshot, the screenshot_count cache will also be set.
		if ( ! $screenshot = $this->get_screenshot( 'relative' ) )
			return 0;

		$prefix = $this->get_stylesheet() . '/screenshot-';
		$files = self::scandir( $this->get_stylesheet_directory(), $this->get_stylesheet(), 'png', 0 );

		$screenshot_count = 1;
		while ( in_array( $prefix . ( $screenshot_count + 1 ) . '.png', $files['png'] ) )
			$screenshot_count++;

		$this->cache_add( 'screenshot_count', $screenshot_count );
		return $screenshot_count;
	}

	/**
	 * Returns an array of screenshot filenames.
	 *
	 * @see WP_Theme::get_screenshot()
	 * @see WP_Theme::get_screenshot_count()
	 * @since 3.4.0
	 * @access public
	 *
	 * @param string $uri Type of URL to return, either 'relative' or an absolute URI. Defaults to absolute URI.
	 * @return array Screenshots. Empty array if no screenshors are found.
	 */
	public function get_screenshots( $uri = 'uri' ) {
		if ( ! $count = $this->get_screenshot_count() )
			return array();

		$pre = 'relative' == $uri ? '' : $this->get_stylesheet_directory_uri() . '/';

		$screenshots = array( $pre . $this->get_screenshot( 'relative' ) );
		for ( $i = 2; $i <= $count; $i++ )
			$screenshots[] = $pre . 'screenshot-' . $i . '.png';
		return $screenshots;
	}

	/**
	 * Return files in the template and stylesheet directories.
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @param string|null $type Optional. Type of files to return, either 'php' or 'css'. Defaults to null, for both.
	 * @return array If a specific $type is requested, returns an array of PHP files. If no $type is requested,
	 * 	returns an array, with the keys being the file types, and the values being an array of files for those type.
	 */
	public function get_files( $type = null, $include_parent_files = false ) {
		$files = $this->cache_get( 'files' );
		if ( ! is_array( $files ) ) {
			if ( $include_parent_files || ! $this->is_child_theme() )
				// Template files can be one level down for the purposes of the theme editor, so this should be $depth = 1.
				// Todo: We ignore this for now, but this is why the branching is weird.
				$files = (array) self::scandir( $this->get_template_directory(), $this->get_template(), array( 'php', 'css' ) );
			if ( $this->is_child_theme() )
				$files = array_merge_recursive( $files, (array) self::scandir( $this->get_stylesheet_directory(), $this->get_stylesheet(), array( 'php', 'css' ) ) );
			foreach ( $files as &$group )
				sort( $group );
			$this->cache_add( 'files', $files );
		}

		if ( null === $type )
			return $files;
		elseif ( isset( $files[ $type ] ) )
			return $files[ $type ];

		return array();
	}

	public function get_page_templates() {
		// If you screw up your current theme and we invalidate your parent, most things still work. Let it slide.
		if ( $this->errors() && $this->errors()->get_error_codes() !== array( 'theme_parent_invalid' ) )
			return array();

		$page_templates = $this->cache_get( 'page_templates' );
		if ( is_array( $page_templates ) )
			return $page_templates;
		$page_templates = array();

		$files = (array) self::scandir( $this->get_template_directory(), $this->get_template_directory(), 'php' );
		if ( $this->is_child_theme() )
			$files = array_merge_recursive( $files, (array) self::scandir( $this->get_stylesheet_directory(), $this->get_stylesheet_directory(), 'php' ) );

		foreach ( $files['php'] as $file ) {
			$headers = get_file_data( $file, array( 'Template Name' => 'Template Name' ) );
			if ( empty( $headers['Template Name'] ) )
				continue;
			$page_templates[ basename( $file ) ] = $this->translate_header( 'Template Name', $headers['Template Name'] );
		}

		$this->cache_add( 'page_templates', $page_templates );
		return $page_templates;
	}

	/**
	 * Scans a directory for files of a certain extension.
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @param string $path Absolute path to search.
	 * @param string $relative_path The basename of the absolute path. Used to control the returned path
	 * 	for the found files, particularly when this function recurses to lower depths.
	 * @param array|string $extensions Array of extensions to find, or string of a single extension.
	 * @depth int How deep to search for files. Optional, defaults to a flat scan (0 depth).
	 */
	private static function scandir( $path, $relative_path, $extensions, $depth = 0 ) {
		if ( is_array( $extensions ) )
			$extensions = implode( '|', $extensions );

		if ( ! is_dir( $path ) )
			return false;

		$results = scandir( $path );
		$files = array();

		foreach ( $results as $result ) {
			if ( '.' == $result || '..' == $result )
				continue;
			if ( is_dir( $path . '/' . $result ) ) {
				if ( ! $depth )
					continue;
				$found = self::scandir( $path . '/' . $result, $relative_path . '/' . $result, $extensions, $depth - 1 );
				$files = array_merge_recursive( $files, $found );
			} elseif ( preg_match( '~\.(' . $extensions . ')$~', $result, $match ) ) {
				if ( ! isset( $files[ $match[1] ] ) )
					$files[ $match[1] ] = array( $relative_path . '/'. $result );
				else
					$files[ $match[1] ][] = $relative_path . '/' . $result;
			}
		}
		return $files;
	}

	/**
	 * Loads the theme's textdomain.
	 *
	 * Translation files are not inherited from the parent theme. Todo: if this fails for the
	 * child theme, it should probably try to load the parent theme's translations.
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @return True if the textdomain was successfully loaded or has already been loaded. False if
	 * 	no textdomain was specified in the file headers, or if the domain could not be loaded.
	 */
	public function load_textdomain() {
		if ( isset( $this->textdomain_loaded ) )
			return $this->textdomain_loaded;

		$textdomain = $this->get('TextDomain');
		if ( ! $textdomain ) {
			$this->textdomain_loaded = false;
			return false;
		}

		if ( is_textdomain_loaded( $textdomain ) ) {
			$this->textdomain_loaded = true;
			return true;
		}

		$path = $this->get_stylesheet_directory();
		if ( $domainpath = $this->get('DomainPath') )
			$path .= $domainpath;

		$this->textdomain_loaded = load_theme_textdomain( $textdomain, $path );
		return $this->textdomain_loaded;
	}

	/**
	 * Whether the theme is allowed (multisite only).
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @param string $check Optional. Whether to check only the 'network'-wide settings, the 'site'
	 * 	settings, or 'both'. Defaults to 'both'.
	 * @param int $blog_id Optional. Ignored if only network-wide settings are checked. Defaults to current blog.
	 * @return bool Whether the theme is allowed for the network. Returns true in single-site.
	 */
	public function is_allowed( $check = 'both', $blog_id = null ) {
		if ( ! is_multisite() )
			return true;

		if ( 'both' == $check || 'network' == $check ) {
			$allowed = self::get_allowed_on_network();
			if ( ! empty( $allowed[ $this->get_stylesheet() ] ) )
				return true;
		}

		if ( 'both' == $check || 'site' == $check ) {
			$allowed = self::get_allowed_on_site( $blog_id );
			if ( ! empty( $allowed[ $this->get_stylesheet() ] ) )
				return true;
		}

		return false;
	}

	/**
	 * Returns array of stylesheet names of themes allowed on the site or network.
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @param int $blog_id Optional. Defaults to current blog.
	 * @return array Array of stylesheet names.
	 */
	public static function get_allowed( $blog_id = null ) {
		return array_merge( self::get_allowed_on_network(), self::get_allowed_on_site( $blog_id ) );
	}

	/**
	 * Returns array of stylesheet names of themes allowed on the network.
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @return array Array of stylesheet names.
	 */
	public static function get_allowed_on_network() {
		static $allowed_themes;
		if ( ! isset( $allowed_themes ) )
			$allowed_themes = (array) get_site_option( 'allowedthemes' );
		return $allowed_themes;
	}

	/**
	 * Returns array of stylesheet names of themes allowed on the site.
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @param int $blog_id Optional. Defaults to current blog.
	 * @return array Array of stylesheet names.
	 */
	public static function get_allowed_on_site( $blog_id = null ) {
		static $allowed_themes = array();
		if ( ! $blog_id )
			$blog_id = get_current_blog_id();

		if ( ! isset( $allowed_themes[ $blog_id ] ) ) {
			if ( $blog_id == get_current_blog_id() )
				$allowed_themes[ $blog_id ] = (array) get_option( 'allowedthemes' );
			else
				$allowed_themes[ $blog_id ] = (array) get_blog_option( $blog_id, 'allowedthemes' );
		}

		return $allowed_themes[ $blog_id ];
	}

	/**
	 * Sort themes by name.
	 */
	public static function sort_by_name( &$themes ) {
		if ( 0 === strpos( get_locale(), 'en_' ) ) {
			uasort( $themes, array( 'WP_Theme', '_name_sort' ) );
		} else {
			uasort( $themes, array( 'WP_Theme', '_name_sort_i18n' ) );
		}
	}

	/**
	 * Callback function for usort() to naturally sort themes by name.
	 *
	 * Accesses the Name header directly from the class for maximum speed.
	 * Would choke on HTML but we don't care enough to slow it down with strip_tags().
	 *
	 * @since 3.4.0
	 * @access public
	 */
	private static function _name_sort( $a, $b ) {
		return strnatcasecmp( $a->headers['Name'], $b->headers['Name'] );
	}

	/**
	 * Name sort (with translation).
	 *
	 * @since 3.4.0
	 * @access public
	 */
	private static function _name_sort_i18n( $a, $b ) {
		// Don't mark up; Do translate.
		return strnatcasecmp( $a->display( 'Name', false, true ), $b->display( 'Name', false, true ) );
	}
}
