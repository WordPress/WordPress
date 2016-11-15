<?php
/**
 * WP_Theme Class
 *
 * @package WordPress
 * @subpackage Theme
 * @since 3.4.0
 */
final class WP_Theme implements ArrayAccess {

	/**
	 * Whether the theme has been marked as updateable.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var bool
	 *
	 * @see WP_MS_Themes_List_Table
	 */
	public $update = false;

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
		'classic'         => 'WordPress Classic',
		'default'         => 'WordPress Default',
		'twentyten'       => 'Twenty Ten',
		'twentyeleven'    => 'Twenty Eleven',
		'twentytwelve'    => 'Twenty Twelve',
		'twentythirteen'  => 'Twenty Thirteen',
		'twentyfourteen'  => 'Twenty Fourteen',
		'twentyfifteen'   => 'Twenty Fifteen',
		'twentysixteen'   => 'Twenty Sixteen',
		'twentyseventeen' => 'Twenty Seventeen',
	);

	/**
	 * Renamed theme tags.
	 *
	 * @static
	 * @access private
	 * @var array
	 */
	private static $tag_map = array(
		'fixed-width'    => 'fixed-layout',
		'flexible-width' => 'fluid-layout',
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
	 *
	 * @access private
	 * @var string
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
	 * In the case of a child theme, this is directory name of the child theme.
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
	 * URL to the theme root, usually an absolute URL to wp-content/themes
	 *
	 * @access private
	 * var string
	 */
	private $theme_root_uri;

	/**
	 * Flag for whether the theme's textdomain is loaded.
	 *
	 * @access private
	 * @var bool
	 */
	private $textdomain_loaded;

	/**
	 * Stores an md5 hash of the theme root, to function as the cache key.
	 *
	 * @access private
	 * @var string
	 */
	private $cache_hash;

	/**
	 * Flag for whether the themes cache bucket should be persistently cached.
	 *
	 * Default is false. Can be set with the {@see 'wp_cache_themes_persistently'} filter.
	 *
	 * @static
	 * @access private
	 * @var bool
	 */
	private static $persistently_cache;

	/**
	 * Expiration time for the themes cache bucket.
	 *
	 * By default the bucket is not cached, so this value is useless.
	 *
	 * @static
	 * @access private
	 * @var bool
	 */
	private static $cache_expiration = 1800;

	/**
	 * Constructor for WP_Theme.
	 *
	 * @global array $wp_theme_directories
	 *
	 * @param string $theme_dir Directory of the theme within the theme_root.
	 * @param string $theme_root Theme root.
	 * @param WP_Error|void $_child If this theme is a parent theme, the child may be passed for validation purposes.
	 */
	public function __construct( $theme_dir, $theme_root, $_child = null ) {
		global $wp_theme_directories;

		// Initialize caching on first run.
		if ( ! isset( self::$persistently_cache ) ) {
			/** This action is documented in wp-includes/theme.php */
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

		// Correct a situation where the theme is 'some-directory/some-theme' but 'some-directory' was passed in as part of the theme root instead.
		if ( ! in_array( $theme_root, (array) $wp_theme_directories ) && in_array( dirname( $theme_root ), (array) $wp_theme_directories ) ) {
			$this->stylesheet = basename( $this->theme_root ) . '/' . $this->stylesheet;
			$this->theme_root = dirname( $theme_root );
		}

		$this->cache_hash = md5( $this->theme_root . '/' . $this->stylesheet );
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
			if ( ! file_exists( $this->theme_root . '/' . $this->stylesheet ) )
				$this->errors = new WP_Error( 'theme_not_found', sprintf( __( 'The theme directory "%s" does not exist.' ), esc_html( $this->stylesheet ) ) );
			else
				$this->errors = new WP_Error( 'theme_no_stylesheet', __( 'Stylesheet is missing.' ) );
			$this->template = $this->stylesheet;
			$this->cache_add( 'theme', array( 'headers' => $this->headers, 'errors' => $this->errors, 'stylesheet' => $this->stylesheet, 'template' => $this->template ) );
			if ( ! file_exists( $this->theme_root ) ) // Don't cache this one.
				$this->errors->add( 'theme_root_missing', __( 'ERROR: The themes directory is either empty or doesn&#8217;t exist. Please check your installation.' ) );
			return;
		} elseif ( ! is_readable( $this->theme_root . '/' . $theme_file ) ) {
			$this->headers['Name'] = $this->stylesheet;
			$this->errors = new WP_Error( 'theme_stylesheet_not_readable', __( 'Stylesheet is not readable.' ) );
			$this->template = $this->stylesheet;
			$this->cache_add( 'theme', array( 'headers' => $this->headers, 'errors' => $this->errors, 'stylesheet' => $this->stylesheet, 'template' => $this->template ) );
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

		// (If template is set from cache [and there are no errors], we know it's good.)
		if ( ! $this->template && ! ( $this->template = $this->headers['Template'] ) ) {
			$this->template = $this->stylesheet;
			if ( ! file_exists( $this->theme_root . '/' . $this->stylesheet . '/index.php' ) ) {
				$error_message = sprintf(
					/* translators: 1: index.php, 2: Codex URL, 3: style.css */
					__( 'Template is missing. Standalone themes need to have a %1$s template file. <a href="%2$s">Child themes</a> need to have a Template header in the %3$s stylesheet.' ),
					'<code>index.php</code>',
					__( 'https://codex.wordpress.org/Child_Themes' ),
					'<code>style.css</code>'
				);
				$this->errors = new WP_Error( 'theme_no_index', $error_message );
				$this->cache_add( 'theme', array( 'headers' => $this->headers, 'errors' => $this->errors, 'stylesheet' => $this->stylesheet, 'template' => $this->template ) );
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
				$this->errors = new WP_Error( 'theme_no_parent', sprintf( __( 'The parent theme is missing. Please install the "%s" parent theme.' ), esc_html( $this->template ) ) );
				$this->cache_add( 'theme', array( 'headers' => $this->headers, 'errors' => $this->errors, 'stylesheet' => $this->stylesheet, 'template' => $this->template ) );
				$this->parent = new WP_Theme( $this->template, $this->theme_root, $this );
				return;
			}
		}

		// Set the parent, if we're a child theme.
		if ( $this->template != $this->stylesheet ) {
			// If we are a parent, then there is a problem. Only two generations allowed! Cancel things out.
			if ( $_child instanceof WP_Theme && $_child->template == $this->stylesheet ) {
				$_child->parent = null;
				$_child->errors = new WP_Error( 'theme_parent_invalid', sprintf( __( 'The "%s" theme is not a valid parent theme.' ), esc_html( $_child->template ) ) );
				$_child->cache_add( 'theme', array( 'headers' => $_child->headers, 'errors' => $_child->errors, 'stylesheet' => $_child->stylesheet, 'template' => $_child->template ) );
				// The two themes actually reference each other with the Template header.
				if ( $_child->stylesheet == $this->template ) {
					$this->errors = new WP_Error( 'theme_parent_invalid', sprintf( __( 'The "%s" theme is not a valid parent theme.' ), esc_html( $this->template ) ) );
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
	public function __toString() {
		return (string) $this->display('Name');
	}

	/**
	 * __isset() magic method for properties formerly returned by current_theme_info()
	 *
	 * @staticvar array $properties
	 *
	 * @param string $offset Property to check if set.
	 * @return bool Whether the given property is set.
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
	 *
	 * @param string $offset Property to get.
	 * @return mixed Property value.
	 */
	public function __get( $offset ) {
		switch ( $offset ) {
			case 'name' :
			case 'title' :
				return $this->get('Name');
			case 'version' :
				return $this->get('Version');
			case 'parent_theme' :
				return $this->parent() ? $this->parent()->get('Name') : '';
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
	 *
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet( $offset, $value ) {}

	/**
	 * Method to implement ArrayAccess for keys formerly returned by get_themes()
	 *
	 * @param mixed $offset
	 */
	public function offsetUnset( $offset ) {}

	/**
	 * Method to implement ArrayAccess for keys formerly returned by get_themes()
	 *
	 * @staticvar array $keys
	 *
	 * @param mixed $offset
	 * @return bool
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
	 * Method to implement ArrayAccess for keys formerly returned by get_themes().
	 *
	 * Author, Author Name, Author URI, and Description did not previously return
	 * translated data. We are doing so now as it is safe to do. However, as
	 * Name and Title could have been used as the key for get_themes(), both remain
	 * untranslated for back compatibility. This means that ['Name'] is not ideal,
	 * and care should be taken to use `$theme::display( 'Name' )` to get a properly
	 * translated header.
	 *
	 * @param mixed $offset
	 * @return mixed
	 */
	public function offsetGet( $offset ) {
		switch ( $offset ) {
			case 'Name' :
			case 'Title' :
				/*
				 * See note above about using translated data. get() is not ideal.
				 * It is only for backward compatibility. Use display().
				 */
				return $this->get('Name');
			case 'Author' :
				return $this->display( 'Author');
			case 'Author Name' :
				return $this->display( 'Author', false);
			case 'Author URI' :
				return $this->display('AuthorURI');
			case 'Description' :
				return $this->display( 'Description');
			case 'Version' :
			case 'Status' :
				return $this->get( $offset );
			case 'Template' :
				return $this->get_template();
			case 'Stylesheet' :
				return $this->get_stylesheet();
			case 'Template Files' :
				return $this->get_files( 'php', 1, true );
			case 'Stylesheet Files' :
				return $this->get_files( 'css', 0, false );
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
				return $this->parent() ? $this->parent()->get('Name') : '';
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
	 * @return WP_Error|false WP_Error if there are errors, or false.
	 */
	public function errors() {
		return is_wp_error( $this->errors ) ? $this->errors : false;
	}

	/**
	 * Whether the theme exists.
	 *
	 * A theme with errors exists. A theme with the error of 'theme_not_found',
	 * meaning that the theme's directory was not found, does not exist.
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @return bool Whether the theme exists.
	 */
	public function exists() {
		return ! ( $this->errors() && in_array( 'theme_not_found', $this->errors()->get_error_codes() ) );
	}

	/**
	 * Returns reference to the parent theme.
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @return WP_Theme|false Parent theme, or false if the current theme is not a child theme.
	 */
	public function parent() {
		return isset( $this->parent ) ? $this->parent : false;
	}

	/**
	 * Adds theme data to cache.
	 *
	 * Cache entries keyed by the theme and the type of data.
	 *
	 * @since 3.4.0
	 * @access private
	 *
	 * @param string $key Type of data to store (theme, screenshot, headers, post_templates)
	 * @param string $data Data to store
	 * @return bool Return value from wp_cache_add()
	 */
	private function cache_add( $key, $data ) {
		return wp_cache_add( $key . '-' . $this->cache_hash, $data, 'themes', self::$cache_expiration );
	}

	/**
	 * Gets theme data from cache.
	 *
	 * Cache entries are keyed by the theme and the type of data.
	 *
	 * @since 3.4.0
	 * @access private
	 *
	 * @param string $key Type of data to retrieve (theme, screenshot, headers, post_templates)
	 * @return mixed Retrieved data
	 */
	private function cache_get( $key ) {
		return wp_cache_get( $key . '-' . $this->cache_hash, 'themes' );
	}

	/**
	 * Clears the cache for the theme.
	 *
	 * @since 3.4.0
	 * @access public
	 */
	public function cache_delete() {
		foreach ( array( 'theme', 'screenshot', 'headers', 'post_templates' ) as $key )
			wp_cache_delete( $key . '-' . $this->cache_hash, 'themes' );
		$this->template = $this->textdomain_loaded = $this->theme_root_uri = $this->parent = $this->errors = $this->headers_sanitized = $this->name_translated = null;
		$this->headers = array();
		$this->__construct( $this->stylesheet, $this->theme_root );
	}

	/**
	 * Get a raw, unformatted theme header.
	 *
	 * The header is sanitized, but is not translated, and is not marked up for display.
	 * To get a theme header for display, use the display() method.
	 *
	 * Use the get_template() method, not the 'Template' header, for finding the template.
	 * The 'Template' header is only good for what was written in the style.css, while
	 * get_template() takes into account where WordPress actually located the theme and
	 * whether it is actually valid.
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @param string $header Theme header. Name, Description, Author, Version, ThemeURI, AuthorURI, Status, Tags.
	 * @return string|false String on success, false on failure.
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
	 * Gets a theme header, formatted and translated for display.
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @param string $header Theme header. Name, Description, Author, Version, ThemeURI, AuthorURI, Status, Tags.
	 * @param bool $markup Optional. Whether to mark up the header. Defaults to true.
	 * @param bool $translate Optional. Whether to translate the header. Defaults to true.
	 * @return string|false Processed header, false on failure.
	 */
	public function display( $header, $markup = true, $translate = true ) {
		$value = $this->get( $header );
		if ( false === $value ) {
			return false;
		}

		if ( $translate && ( empty( $value ) || ! $this->load_textdomain() ) )
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
	 * @since 3.4.0
	 * @access private
	 *
	 * @staticvar array $header_tags
	 * @staticvar array $header_tags_with_a
	 *
	 * @param string $header Theme header. Name, Description, Author, Version, ThemeURI, AuthorURI, Status, Tags.
	 * @param string $value Value to sanitize.
	 * @return mixed
	 */
	private function sanitize_header( $header, $value ) {
		switch ( $header ) {
			case 'Status' :
				if ( ! $value ) {
					$value = 'publish';
					break;
				}
				// Fall through otherwise.
			case 'Name' :
				static $header_tags = array(
					'abbr'    => array( 'title' => true ),
					'acronym' => array( 'title' => true ),
					'code'    => true,
					'em'      => true,
					'strong'  => true,
				);
				$value = wp_kses( $value, $header_tags );
				break;
			case 'Author' :
				// There shouldn't be anchor tags in Author, but some themes like to be challenging.
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
				$value = esc_url_raw( $value );
				break;
			case 'Tags' :
				$value = array_filter( array_map( 'trim', explode( ',', strip_tags( $value ) ) ) );
				break;
			case 'Version' :
				$value = strip_tags( $value );
				break;
		}

		return $value;
	}

	/**
	 * Mark up a theme header.
	 *
     * @since 3.4.0
	 * @access private
	 *
	 * @staticvar string $comma
	 *
	 * @param string $header Theme header. Name, Description, Author, Version, ThemeURI, AuthorURI, Status, Tags.
	 * @param string $value Value to mark up.
	 * @param string $translate Whether the header has been translated.
	 * @return string Value, marked up.
	 */
	private function markup_header( $header, $value, $translate ) {
		switch ( $header ) {
			case 'Name' :
				if ( empty( $value ) )
					$value = $this->get_stylesheet();
				break;
			case 'Description' :
				$value = wptexturize( $value );
				break;
			case 'Author' :
				if ( $this->get('AuthorURI') ) {
					$value = sprintf( '<a href="%1$s">%2$s</a>', $this->display( 'AuthorURI', true, $translate ), $value );
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
			case 'ThemeURI' :
			case 'AuthorURI' :
				$value = esc_url( $value );
				break;
		}

		return $value;
	}

	/**
	 * Translate a theme header.
	 *
	 * @since 3.4.0
	 * @access private
	 *
	 * @staticvar array $tags_list
	 *
	 * @param string $header Theme header. Name, Description, Author, Version, ThemeURI, AuthorURI, Status, Tags.
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
				if ( empty( $value ) || ! function_exists( 'get_theme_feature_list' ) ) {
					return $value;
				}

				static $tags_list;
				if ( ! isset( $tags_list ) ) {
					$tags_list = array(
						// As of 4.6, deprecated tags which are only used to provide translation for older themes.
						'black' => __( 'Black' ), 'blue' => __( 'Blue' ), 'brown'  => __( 'Brown' ),
						'gray' => __( 'Gray' ), 'green'  => __( 'Green' ), 'orange' => __( 'Orange' ),
						'pink' => __( 'Pink' ), 'purple' => __( 'Purple' ), 'red' => __( 'Red' ),
						'silver' => __( 'Silver' ), 'tan' => __( 'Tan' ), 'white' => __( 'White' ),
						'yellow' => __( 'Yellow' ), 'dark' => __( 'Dark' ), 'light' => __( 'Light' ),
						'fixed-layout' => __( 'Fixed Layout' ), 'fluid-layout' => __( 'Fluid Layout' ),
						'responsive-layout' => __( 'Responsive Layout' ), 'blavatar' => __( 'Blavatar' ),
						'photoblogging' => __( 'Photoblogging' ), 'seasonal' => __( 'Seasonal' ),
					);

					$feature_list = get_theme_feature_list( false ); // No API
					foreach ( $feature_list as $tags ) {
						$tags_list += $tags;
					}
				}

				foreach ( $value as &$tag ) {
					if ( isset( $tags_list[ $tag ] ) ) {
						$tag = $tags_list[ $tag ];
					} elseif ( isset( self::$tag_map[ $tag ] ) ) {
						$tag = $tags_list[ self::$tag_map[ $tag ] ];
					}
				}

				return $value;

			default :
				$value = translate( $value, $this->get('TextDomain') );
		}
		return $value;
	}

	/**
	 * The directory name of the theme's "stylesheet" files, inside the theme root.
	 *
	 * In the case of a child theme, this is directory name of the child theme.
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
		if ( $this->errors() && in_array( 'theme_root_missing', $this->errors()->get_error_codes() ) )
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
		if ( $this->parent() )
			$theme_root = $this->parent()->theme_root;
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
		return $this->get_theme_root_uri() . '/' . str_replace( '%2F', '/', rawurlencode( $this->stylesheet ) );
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
		if ( $this->parent() )
			$theme_root_uri = $this->parent()->get_theme_root_uri();
		else
			$theme_root_uri = $this->get_theme_root_uri();

		return $theme_root_uri . '/' . str_replace( '%2F', '/', rawurlencode( $this->template ) );
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
	 * This is typically the absolute URL to wp-content/themes. This forms the basis
	 * for all other URLs returned by WP_Theme, so we pass it to the public function
	 * get_theme_root_uri() and allow it to run the {@see 'theme_root_uri'} filter.
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @return string Theme root URI.
	 */
	public function get_theme_root_uri() {
		if ( ! isset( $this->theme_root_uri ) )
			$this->theme_root_uri = get_theme_root_uri( $this->stylesheet, $this->theme_root );
		return $this->theme_root_uri;
	}

	/**
	 * Returns the main screenshot file for the theme.
	 *
	 * The main screenshot is called screenshot.png. gif and jpg extensions are also allowed.
	 *
	 * Screenshots for a theme must be in the stylesheet directory. (In the case of child
	 * themes, parent theme screenshots are not inherited.)
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @param string $uri Type of URL to return, either 'relative' or an absolute URI. Defaults to absolute URI.
	 * @return string|false Screenshot file. False if the theme does not have a screenshot.
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
		return false;
	}

	/**
	 * Return files in the theme's directory.
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @param mixed $type Optional. Array of extensions to return. Defaults to all files (null).
	 * @param int $depth Optional. How deep to search for files. Defaults to a flat scan (0 depth). -1 depth is infinite.
	 * @param bool $search_parent Optional. Whether to return parent files. Defaults to false.
	 * @return array Array of files, keyed by the path to the file relative to the theme's directory, with the values
	 * 	             being absolute paths.
	 */
	public function get_files( $type = null, $depth = 0, $search_parent = false ) {
		$files = (array) self::scandir( $this->get_stylesheet_directory(), $type, $depth );

		if ( $search_parent && $this->parent() )
			$files += (array) self::scandir( $this->get_template_directory(), $type, $depth );

		return $files;
	}

	/**
	 * Returns the theme's post templates.
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @return array Array of page templates, keyed by filename and post type,
	 *               with the value of the translated header name.
	 */
	public function get_post_templates() {
		// If you screw up your current theme and we invalidate your parent, most things still work. Let it slide.
		if ( $this->errors() && $this->errors()->get_error_codes() !== array( 'theme_parent_invalid' ) ) {
			return array();
		}

		$post_templates = $this->cache_get( 'post_templates' );

		if ( ! is_array( $post_templates ) ) {
			$post_templates = array();

			$files = (array) $this->get_files( 'php', 1 );

			foreach ( $files as $file => $full_path ) {
				if ( ! preg_match( '|Template Name:(.*)$|mi', file_get_contents( $full_path ), $header ) ) {
					continue;
				}

				$types = array( 'page' );
				if ( preg_match( '|Template Post Type:(.*)$|mi', file_get_contents( $full_path ), $type ) ) {
					$types = explode( ',', _cleanup_header_comment( $type[1] ) );
				}

				foreach ( $types as $type ) {
					$type = sanitize_key( $type );
					if ( ! isset( $post_templates[ $type ] ) ) {
						$post_templates[ $type ] = array();
					}

					$post_templates[ $type ][ $file ] = _cleanup_header_comment( $header[1] );
				}
			}

			$this->cache_add( 'post_templates', $post_templates );
		}

		if ( $this->load_textdomain() ) {
			foreach ( $post_templates as &$post_type ) {
				foreach ( $post_type as &$post_template ) {
					$post_template = $this->translate_header( 'Template Name', $post_template );
				}
			}
		}

		return $post_templates;
	}

	/**
	 * Returns the theme's post templates for a given post type.
	 *
	 * @since 3.4.0
	 * @since 4.7.0 Added the `$post_type` parameter.
	 * @access public
	 *
	 * @param WP_Post|null $post      Optional. The post being edited, provided for context.
	 * @param string       $post_type Optional. Post type to get the templates for. Default 'page'.
	 *                                If a post is provided, its post type is used.
	 * @return array Array of page templates, keyed by filename, with the value of the translated header name.
	 */
	public function get_page_templates( $post = null, $post_type = 'page' ) {
		if ( $post ) {
			$post_type = get_post_type( $post );
		}

		$post_templates = $this->get_post_templates();
		$post_templates = isset( $post_templates[ $post_type ] ) ? $post_templates[ $post_type ] : array();

		if ( $this->parent() ) {
			$post_templates += $this->parent()->get_page_templates( $post, $post_type );
		}

		/**
		 * Filters list of page templates for a theme.
		 *
		 * The dynamic portion of the hook name, `$post_type`, refers to the post type.
		 *
		 * @since 3.9.0
		 * @since 4.4.0 Converted to allow complete control over the `$page_templates` array.
		 * @since 4.7.0 Added the `$post_type` parameter.
		 *
		 * @param array        $post_templates Array of page templates. Keys are filenames,
		 *                                     values are translated names.
		 * @param WP_Theme     $this           The theme object.
		 * @param WP_Post|null $post           The post being edited, provided for context, or null.
		 * @param string       $post_type      Post type to get the templates for.
		 */
		return (array) apply_filters( "theme_{$post_type}_templates", $post_templates, $this, $post, $post_type );
	}

	/**
	 * Scans a directory for files of a certain extension.
	 *
	 * @since 3.4.0
	 *
	 * @static
	 * @access private
	 *
	 * @param string            $path          Absolute path to search.
	 * @param array|string|null $extensions    Optional. Array of extensions to find, string of a single extension,
	 *                                         or null for all extensions. Default null.
	 * @param int               $depth         Optional. How many levels deep to search for files. Accepts 0, 1+, or
	 *                                         -1 (infinite depth). Default 0.
	 * @param string            $relative_path Optional. The basename of the absolute path. Used to control the
	 *                                         returned path for the found files, particularly when this function
	 *                                         recurses to lower depths. Default empty.
	 * @return array|false Array of files, keyed by the path to the file relative to the `$path` directory prepended
	 *                     with `$relative_path`, with the values being absolute paths. False otherwise.
	 */
	private static function scandir( $path, $extensions = null, $depth = 0, $relative_path = '' ) {
		if ( ! is_dir( $path ) )
			return false;

		if ( $extensions ) {
			$extensions = (array) $extensions;
			$_extensions = implode( '|', $extensions );
		}

		$relative_path = trailingslashit( $relative_path );
		if ( '/' == $relative_path )
			$relative_path = '';

		$results = scandir( $path );
		$files = array();

		foreach ( $results as $result ) {
			if ( '.' == $result[0] )
				continue;
			if ( is_dir( $path . '/' . $result ) ) {
				if ( ! $depth || 'CVS' == $result )
					continue;
				$found = self::scandir( $path . '/' . $result, $extensions, $depth - 1 , $relative_path . $result );
				$files = array_merge_recursive( $files, $found );
			} elseif ( ! $extensions || preg_match( '~\.(' . $_extensions . ')$~', $result ) ) {
				$files[ $relative_path . $result ] = $path . '/' . $result;
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
	 * @return bool True if the textdomain was successfully loaded or has already been loaded.
	 * 	False if no textdomain was specified in the file headers, or if the domain could not be loaded.
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
		else
			$path .= '/languages';

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
	 * @param int $blog_id Optional. Ignored if only network-wide settings are checked. Defaults to current site.
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
	 * Determines the latest WordPress default theme that is installed.
	 *
	 * This hits the filesystem.
	 *
	 * @return WP_Theme|false Object, or false if no theme is installed, which would be bad.
	 */
	public static function get_core_default_theme() {
		foreach ( array_reverse( self::$default_themes ) as $slug => $name ) {
			$theme = wp_get_theme( $slug );
			if ( $theme->exists() ) {
				return $theme;
			}
		}
		return false;
	}

	/**
	 * Returns array of stylesheet names of themes allowed on the site or network.
	 *
	 * @since 3.4.0
	 *
	 * @static
	 * @access public
	 *
	 * @param int $blog_id Optional. ID of the site. Defaults to the current site.
	 * @return array Array of stylesheet names.
	 */
	public static function get_allowed( $blog_id = null ) {
		/**
		 * Filters the array of themes allowed on the network.
		 *
		 * Site is provided as context so that a list of network allowed themes can
		 * be filtered further.
		 *
		 * @since 4.5.0
		 *
		 * @param array $allowed_themes An array of theme stylesheet names.
		 * @param int   $blog_id        ID of the site.
		 */
		$network = (array) apply_filters( 'network_allowed_themes', self::get_allowed_on_network(), $blog_id );
		return $network + self::get_allowed_on_site( $blog_id );
	}

	/**
	 * Returns array of stylesheet names of themes allowed on the network.
	 *
	 * @since 3.4.0
	 *
	 * @static
	 * @access public
	 *
	 * @staticvar array $allowed_themes
	 *
	 * @return array Array of stylesheet names.
	 */
	public static function get_allowed_on_network() {
		static $allowed_themes;
		if ( ! isset( $allowed_themes ) ) {
			$allowed_themes = (array) get_site_option( 'allowedthemes' );
		}

		/**
		 * Filters the array of themes allowed on the network.
		 *
		 * @since MU
		 *
		 * @param array $allowed_themes An array of theme stylesheet names.
		 */
		$allowed_themes = apply_filters( 'allowed_themes', $allowed_themes );

		return $allowed_themes;
	}

	/**
	 * Returns array of stylesheet names of themes allowed on the site.
	 *
	 * @since 3.4.0
	 *
	 * @static
	 * @access public
	 *
	 * @staticvar array $allowed_themes
	 *
	 * @param int $blog_id Optional. ID of the site. Defaults to the current site.
	 * @return array Array of stylesheet names.
	 */
	public static function get_allowed_on_site( $blog_id = null ) {
		static $allowed_themes = array();

		if ( ! $blog_id || ! is_multisite() )
			$blog_id = get_current_blog_id();

		if ( isset( $allowed_themes[ $blog_id ] ) ) {
			/**
			 * Filters the array of themes allowed on the site.
			 *
			 * @since 4.5.0
			 *
			 * @param array $allowed_themes An array of theme stylesheet names.
			 * @param int   $blog_id        ID of the site. Defaults to current site.
			 */
			return (array) apply_filters( 'site_allowed_themes', $allowed_themes[ $blog_id ], $blog_id );
		}

		$current = $blog_id == get_current_blog_id();

		if ( $current ) {
			$allowed_themes[ $blog_id ] = get_option( 'allowedthemes' );
		} else {
			switch_to_blog( $blog_id );
			$allowed_themes[ $blog_id ] = get_option( 'allowedthemes' );
			restore_current_blog();
		}

		// This is all super old MU back compat joy.
		// 'allowedthemes' keys things by stylesheet. 'allowed_themes' keyed things by name.
		if ( false === $allowed_themes[ $blog_id ] ) {
			if ( $current ) {
				$allowed_themes[ $blog_id ] = get_option( 'allowed_themes' );
			} else {
				switch_to_blog( $blog_id );
				$allowed_themes[ $blog_id ] = get_option( 'allowed_themes' );
				restore_current_blog();
			}

			if ( ! is_array( $allowed_themes[ $blog_id ] ) || empty( $allowed_themes[ $blog_id ] ) ) {
				$allowed_themes[ $blog_id ] = array();
			} else {
				$converted = array();
				$themes = wp_get_themes();
				foreach ( $themes as $stylesheet => $theme_data ) {
					if ( isset( $allowed_themes[ $blog_id ][ $theme_data->get('Name') ] ) )
						$converted[ $stylesheet ] = true;
				}
				$allowed_themes[ $blog_id ] = $converted;
			}
			// Set the option so we never have to go through this pain again.
			if ( is_admin() && $allowed_themes[ $blog_id ] ) {
				if ( $current ) {
					update_option( 'allowedthemes', $allowed_themes[ $blog_id ] );
					delete_option( 'allowed_themes' );
				} else {
					switch_to_blog( $blog_id );
					update_option( 'allowedthemes', $allowed_themes[ $blog_id ] );
					delete_option( 'allowed_themes' );
					restore_current_blog();
				}
			}
		}

		/** This filter is documented in wp-includes/class-wp-theme.php */
		return (array) apply_filters( 'site_allowed_themes', $allowed_themes[ $blog_id ], $blog_id );
	}

	/**
	 * Enables a theme for all sites on the current network.
	 *
	 * @since 4.6.0
	 * @access public
	 * @static
	 *
	 * @param string|array $stylesheets Stylesheet name or array of stylesheet names.
	 */
	public static function network_enable_theme( $stylesheets ) {
		if ( ! is_multisite() ) {
			return;
		}

		if ( ! is_array( $stylesheets ) ) {
			$stylesheets = array( $stylesheets );
		}

		$allowed_themes = get_site_option( 'allowedthemes' );
		foreach ( $stylesheets as $stylesheet ) {
			$allowed_themes[ $stylesheet ] = true;
		}

		update_site_option( 'allowedthemes', $allowed_themes );
	}

	/**
	 * Disables a theme for all sites on the current network.
	 *
	 * @since 4.6.0
	 * @access public
	 * @static
	 *
	 * @param string|array $stylesheets Stylesheet name or array of stylesheet names.
	 */
	public static function network_disable_theme( $stylesheets ) {
		if ( ! is_multisite() ) {
			return;
		}

		if ( ! is_array( $stylesheets ) ) {
			$stylesheets = array( $stylesheets );
		}

		$allowed_themes = get_site_option( 'allowedthemes' );
		foreach ( $stylesheets as $stylesheet ) {
			if ( isset( $allowed_themes[ $stylesheet ] ) ) {
				unset( $allowed_themes[ $stylesheet ] );
			}
		}

		update_site_option( 'allowedthemes', $allowed_themes );
	}

	/**
	 * Sorts themes by name.
	 *
	 * @since 3.4.0
	 *
	 * @static
	 * @access public
	 *
	 * @param array $themes Array of themes to sort, passed by reference.
	 */
	public static function sort_by_name( &$themes ) {
		if ( 0 === strpos( get_user_locale(), 'en_' ) ) {
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
	 *
	 * @static
	 * @access private
	 *
	 * @param string $a First name.
	 * @param string $b Second name.
	 * @return int Negative if `$a` falls lower in the natural order than `$b`. Zero if they fall equally.
	 *             Greater than 0 if `$a` falls higher in the natural order than `$b`. Used with usort().
	 */
	private static function _name_sort( $a, $b ) {
		return strnatcasecmp( $a->headers['Name'], $b->headers['Name'] );
	}

	/**
	 * Name sort (with translation).
	 *
	 * @since 3.4.0
	 *
	 * @static
	 * @access private
	 *
	 * @param string $a First name.
	 * @param string $b Second name.
	 * @return int Negative if `$a` falls lower in the natural order than `$b`. Zero if they fall equally.
	 *             Greater than 0 if `$a` falls higher in the natural order than `$b`. Used with usort().
	 */
	private static function _name_sort_i18n( $a, $b ) {
		// Don't mark up; Do translate.
		return strnatcasecmp( $a->display( 'Name', false, true ), $b->display( 'Name', false, true ) );
	}
}
