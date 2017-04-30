<?php
// Based on Jigsaw plugin by Jared Novack (http://jigsaw.upstatement.com/)
class TheLib {

	// --- Start of 5.2 compatibility functions

	/**
	 * Internal data collection used to pass arguments to callback functions.
	 * Only used for 5.2 version as alternative to closures.
	 * @var array
	 */
	static protected $data = array();

	static protected function _have( $key ) {
		return isset( self::$data[ $key ] );
	}

	static protected function _add( $key, $value ) {
		if ( ! is_array( @self::$data[ $key ] ) ) {
			self::$data[ $key ] = array();
		}
		self::$data[ $key ][] = $value;
	}

	static protected function _get( $key ) {
		if ( ! is_array( @self::$data[ $key ] ) ) {
			self::$data[ $key ] = array();
		}
		return self::$data[ $key ];
	}

	// --- End of 5.2 compatibility functions


	/**
	 * Returns the full URL to an internal CSS file of the code library.
	 *
	 * @since  1.0.0
	 * @param  string $file The filename, relative to this plugins folder.
	 * @return string
	 */
	static protected function css_url( $file ) {
		static $Url = null;
		if ( null === $Url ) {
			$Url = plugins_url( 'css/', __FILE__ );
		}
		return $Url . $file;
	}

	/**
	 * Returns the full URL to an internal JS file of the code library.
	 *
	 * @since  1.0.0
	 * @param  string $file The filename, relative to this plugins folder.
	 * @return string
	 */
	static protected function js_url( $file ) {
		static $Url = null;
		if ( null === $Url ) {
			$Url = plugins_url( 'js/', __FILE__ );
		}
		return $Url . $file;
	}

	/**
	 * Returns the full path to an internal php partial of the code library.
	 *
	 * @since  1.0.0
	 * @param  string $file The filename, relative to this plugins folder.
	 * @return string
	 */
	static protected function include_path( $file ) {
		static $Path = null;
		if ( null === $Path ) {
			$basedir = dirname( __FILE__ ) . '/';
			$Path = $basedir . 'inc/';
		}
		return $Path . $file;
	}

	/**
	 * Enqueue core UI files (CSS/JS).
	 *
	 * Defined modules:
	 *  - core
	 *  - scrollbar
	 *  - select
	 *
	 * @since  1.0.0
	 * @param  string $modules The module to load.
	 * @param  string $onpage A page hook; files will only be loaded on this page.
	 */
	static public function add_ui( $module = 'core', $onpage = null ) {
		switch ( $module ) {
			case 'core':
				self::add_css( self::css_url( 'wpmu-ui.css' ), $onpage );
				self::add_js( self::js_url( 'wpmu-ui.min.js' ), $onpage );
				break;

			case 'scrollbar':
				self::add_js( self::js_url( 'tiny-scrollbar.min.js' ), $onpage );
				break;

			case 'select':
				self::add_css( self::css_url( 'select2.css' ), $onpage );
				self::add_js( self::js_url( 'select2.min.js' ), $onpage );
				break;

			default:
				$ext = strrchr( $module, '.' );
				if ( '.css' === $ext ) {
					self::add_css( $module, $onpage );
				} else if ( '.js' === $ext ) {
					self::add_js( $module, $onpage );
				}
		}
	}

	/**
	 * Enqueue a javascript file.
	 *
	 * @since  1.0.0
	 * @param  string $url Full URL to the javascript file.
	 * @param  string $onpage A page hook; files will only be loaded on this page.
	 */
	static public function add_js( $url, $onpage ) {
		self::add_admin_js_or_css( $url, 'js', $onpage = null );
	}

	/**
	 * Enqueue a css file.
	 *
	 * @since  1.0.0
	 * @param  string $url Full URL to the css filename.
	 * @param  string $onpage A page hook; files will only be loaded on this page.
	 */
	static public function add_css( $url, $onpage ) {
		self::add_admin_js_or_css( $url, 'css', $onpage = null );
	}

	/**
	 * Enqueues either a css or javascript file
	 *
	 * @since  1.0.0
	 * @param  string $url Full URL to the CSS or Javascript file.
	 * @param  string $type File-type [css|js]
	 * @param  string $onpage A page hook; files will only be loaded on this page.
	 */
	static public function add_admin_js_or_css( $url, $type = 'css', $onpage = null ) {
		if ( ! is_admin() ) {
			return;
		}

		// Get the filename from the URL, then sanitize it and prefix "wpmu-"
		$urlparts = explode( '?', $url, 2 );
		$alias = 'wpmu-' . sanitize_title( basename( reset( $urlparts ) ) );
		$onpage = empty( $onpage ) ? '' : $onpage;

		$item = compact( 'url', 'alias', 'onpage' );
		if ( 'css' == $type || 'style' == $type ) {
			self::_have( 'css' ) || add_action(
				'admin_enqueue_scripts',
				array( __CLASS__, 'enqueue_style_callback' ),
				15 // Load custom styles a bit later than core styles.
			);
			self::_add( 'css', $item );
		} else {
			self::_have( 'js' ) || add_action(
				'admin_enqueue_scripts',
				array( __CLASS__, 'enqueue_script_callback' ),
				15 // Load custom scripts a bit later than core scripts.
			);
			self::_add( 'js', $item );
		}
	}

	/**
	 * Action hook for enqueue style (for PHP <5.3 only)
	 *
	 * @since  1.0.1
	 * @param  string $hook The current admin page that is rendered.
	 */
	static public function enqueue_style_callback( $hook ) {
		$items = self::_get( 'css' );
		foreach ( $items as $item ) {
			extract( $item ); // url, alias, onpage
			if ( '' !== $onpage && $hook != $onpage ) { continue; }
			wp_enqueue_style( $alias, $url );
		}
	}

	/**
	 * Action hook for enqueue script (for PHP <5.3 only)
	 *
	 * @since  1.0.1
	 * @param  string $hook The current admin page that is rendered.
	 */
	static public function enqueue_script_callback( $hook ) {
		$items = self::_get( 'js' );
		foreach ( $items as $item ) {
			extract( $item ); // url, alias, onpage
			if ( '' !== $onpage && $hook != $onpage ) { continue; }
			wp_enqueue_script( $alias, $url, array( 'jquery' ), false, true );
		}
	}



	/**
	 * Displays a WordPress pointer on the current admin screen.
	 *
	 * @since  1.0.0
	 * @param  string $pointer_id Internal ID of the pointer, make sure it is unique!
	 * @param  string $html_el HTML element to point to (e.g. '#menu-appearance')
	 * @param  string $title The title of the pointer.
	 * @param  string $body Text of the pointer.
	 */
	static public function pointer( $pointer_id, $html_el, $title, $body ) {
		if ( ! is_admin() ) {
			return;
		}

		self::_have( 'init_pointer' ) || add_action(
			'init',
			array( __CLASS__, 'init_pointer' )
		);
		self::_add( 'init_pointer', compact( 'pointer_id', 'html_el', 'title', 'body' ) );
	}

	/**
	 * Action handler for plugins_loaded. This decides if the pointer will be displayed.
	 *
	 * @since  1.0.2
	 */
	static public function init_pointer() {
		$items = self::_get( 'init_pointer' );
		foreach ( $items as $item ) {
			extract( $item );

			// Find out which pointer IDs this user has already seen.
			$seen = (string) get_user_meta(
				get_current_user_id(),
				'dismissed_wp_pointers',
				true
			);
			$seen_list = explode( ',', $seen );

			// Handle our first pointer announcing the plugin's new settings screen.
			if ( ! in_array( $pointer_id, $seen_list ) ) {
				self::_have( 'pointer' ) || add_action(
					'admin_print_footer_scripts',
					array( __CLASS__, 'pointer_print_scripts' )
				);
				self::_have( 'pointer' ) || add_action(
					'admin_enqueue_scripts',
					array( __CLASS__, 'enqueue_pointer' )
				);
				self::_add( 'pointer', $item );
			}
		}
	}

	/**
	 * Enqueue wp-pointer (for PHP <5.3 only)
	 *
	 * @since  1.0.1
	 */
	static public function enqueue_pointer() {
		// Load the JS/CSS for WP Pointers
		wp_enqueue_script( 'wp-pointer' );
		wp_enqueue_style( 'wp-pointer' );
	}

	/**
	 * Action hook for admin footer scripts (for PHP <5.3 only)
	 *
	 * @since  1.0.1
	 */
	static public function pointer_print_scripts() {
		$items = self::_get( 'pointer' );
		foreach ( $items as $item ) {
			extract( $item ); // pointer_id, html_el, title, body
			include self::include_path( 'pointer.php' );
		}
	}


	/**
	 * Display an admin notice.
	 *
	 * @since  1.0.0
	 * @param  string $text Text to display.
	 * @param  string $class Message-type [updated|error]
	 */
	static public function message( $text, $class = 'updated' ) {
		if ( 'green' == $class || 'update' == $class || 'ok' == $class ) {
			$class = 'updated';
		}
		if ( 'red' == $class || 'err' == $class ) {
			$class = 'error';
		}
		self::_have( 'message' ) || add_action(
			'admin_notices',
			array( __CLASS__, 'admin_notice_callback' ),
			1
		);
		self::_add( 'message', compact( 'text', 'class' ) );
	}


	/**
	 * Action hook for admin notices (for PHP <5.3 only)
	 *
	 * @since  1.0.1
	 */
	static public function admin_notice_callback() {
		$items = self::_get( 'message' );
		foreach ( $items as $item ) {
			extract( $item ); // text, class
			echo '<div class="' . esc_attr( $class ) . '"><p>' . $text . '</p></div>';
		}
	}


	/**
	 * Short way to load the textdomain of a plugin.
	 *
	 * @since  1.0.0
	 * @param  string $domain Translations will be mapped to this domain.
	 * @param  string $rel_dir Path to the dictionary folder; relative to ABSPATH.
	 */
	static public function translate_plugin( $domain, $rel_dir ) {
		self::_have( 'textdomain' ) || add_action(
			'plugins_loaded',
			array( __CLASS__, 'translate_plugin_callback' )
		);
		self::_add( 'textdomain', compact( 'domain', 'rel_dir' ) );
	}


	/**
	 * Create function callback for load textdomain (for PHP <5.3 only)
	 *
	 * @since  1.0.1
	 */
	static public function translate_plugin_callback() {
		$items = self::_get( 'textdomain' );
		foreach ( $items as $item ) {
			extract( $item ); // domain, rel_dir
			load_plugin_textdomain( $domain, false, $rel_dir );
		}
	}
};