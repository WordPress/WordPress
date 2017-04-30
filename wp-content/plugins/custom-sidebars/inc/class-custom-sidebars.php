<?php

// Load additional Pro-modules.
require_once CSB_INC_DIR . 'class-custom-sidebars-widgets.php';
require_once CSB_INC_DIR . 'class-custom-sidebars-editor.php';
require_once CSB_INC_DIR . 'class-custom-sidebars-replacer.php';


/**
 * Main plugin file.
 * The CustomSidebars class encapsulates all our plugin logic.
 */
class CustomSidebars {
	/**
	 * Prefix used for the sidebar-ID of custom sidebars. This is also used to
	 * distinguish theme sidebars from custom sidebars.
	 * @var  string
	 */
	static protected $sidebar_prefix = 'cs-';

	/**
	 * Capability required to use *any* of the plugin features. If user does not
	 * have this capability then he will not see any change on admin dashboard.
	 * @var  string
	 */
	static protected $cap_required = 'switch_themes';

	/**
	 * URL to the documentation/info page of the pro plugin
	 * @var  string
	 */
	static public $pro_url = 'http://premium.wpmudev.org/project/custom-sidebars-pro/';


	/**
	 * Returns the singleton instance of the custom sidebars class.
	 *
	 * @since  2.0
	 */
	static public function instance() {
		static $Inst = null;

		if ( null === $Inst ) {
			$Inst = new CustomSidebars();
		}

		return $Inst;
	}

	/**
	 * Private, since it is a singleton.
	 * We directly initialize sidebar options when class is created.
	 */
	private function __construct() {
		/**
		 * ID of the WP-Pointer used to introduce the plugin upon activation
		 *
		 * ========== Pointer ==========
		 *  Internal ID:  wpmudcs1 [WPMUDev CustomSidebars 1]
		 *  Point at:     #menu-appearance (Appearance menu item)
		 *  Title:        Custom Sidebars
		 *  Description:  Create and edit custom sidebars in your widget screen!
		 * -------------------------------------------------------------------------
		 */
		TheLib::pointer(
			'wpmudcs1',                               // Internal Pointer-ID
			'#menu-appearance',                       // Point at
			__( 'Custom Sidebars', CSB_LANG ),    // Title
			sprintf(
				__(
					'Now you can create and edit custom sidebars in your ' .
					'<a href="%1$s">Widgets screen</a>!', CSB_LANG
				),
				admin_url( 'widgets.php' )
			)                                         // Body
		);

		// Load the text domain for the plugin
		TheLib::translate_plugin( CSB_LANG, CSB_LANG_DIR );

		// Load javascripts/css files
		TheLib::add_ui( 'core', 'widgets.php' );
		TheLib::add_ui( 'scrollbar', 'widgets.php' );
		TheLib::add_ui( 'select', 'widgets.php' );
		TheLib::add_ui( CSB_JS_URL . 'cs.min.js', 'widgets.php' );
		TheLib::add_ui( CSB_CSS_URL . 'cs.css', 'widgets.php' );

		// AJAX actions
		add_action( 'wp_ajax_cs-ajax', array( $this, 'ajax_handler' ) );

		// Extensions use this hook to initialize themselfs.
		do_action( 'cs_init' );

		// Display a message after import.
		if ( isset( $_GET['cs-msg'] ) ) {
			$msg = base64_decode( $_GET['cs-msg'] );
			TheLib::message( $msg );
		}

		// Free version only
		add_action( 'in_widget_form', array( $this, 'in_widget_form' ), 10, 1 );
	}

	// =========================================================================
	// == DATA ACCESS
	// =========================================================================


	/**
	 *
	 * ==1== PLUGIN OPTIONS
	 *   Option-Key: cs_modifiable
	 *
	 *   {
	 *       // Sidebars that can be replaced:
	 *       'modifiable': [
	 *           'sidebar_1',
	 *           'sidebar_2'
	 *       ],
	 *
	 *       // Default replacements:
	 *       'post_type_single': [ // Former "defaults"
	 *           'post_type1': <replacement-def>,
	 *           'post_type2': <replacement-def>
	 *       ],
	 *       'post_type_archive': [  // Former "post_type_pages"
	 *           'post_type1': <replacement-def>,
	 *           'post_type2': <replacement-def>
	 *       ],
	 *       'category_single': [ // Former "category_posts"
	 *           'category_id1': <replacement-def>,
	 *           'category_id2': <replacement-def>
	 *       ],
	 *       'category_archive': [ // Former "category_pages"
	 *           'category_id1': <replacement-def>,
	 *           'category_id2': <replacement-def>
	 *       ],
	 *       'blog': <replacement-def>,
	 *       'tags': <replacement-def>,
	 *       'authors': <replacement-def>,
	 *       'search': <replacement-def>,
	 *       'date': <replacement-def>
	 *   }
	 *
	 * ==2== REPLACEMENT-DEF
	 *   Meta-Key: _cs_replacements
	 *   Option-Key: cs_modifiable <replacement-def>
	 *
	 *   {
	 *       'sidebar_1': 'custom_sb_id1',
	 *       'sidebar_2': 'custom_sb_id2'
	 *   }
	 *
	 * ==3== SIDEBAR DEFINITION
	 *   Option-Key: cs_sidebars
	 *
	 *   Array of these objects
	 *   {
	 *       id: '', // sidebar-id
	 *       name: '',
	 *       description: '',
	 *       before_title: '',
	 *       after_title: '',
	 *       before_widget: '',
	 *       after_widget: ''
	 *   }
	 *
	 * ==4== WIDGET LIST
	 *   Option-Key: sidebars_widgets
	 *
	 *   {
	 *       'sidebar_id': [
	 *           'widget_id1',
	 *           'widget_id2'
	 *       ],
	 *       'sidebar_2': [
	 *       ],
	 *       'sidebar_3': [
	 *           'widget_id1',
	 *           'widget_id3'
	 *       ],
	 *   }
	 */


	/**
	 * If the specified variable is an array it will be returned. Otherwise
	 * an empty array is returned.
	 *
	 * @since  2.0
	 * @param  mixed $val1 Value that maybe is an array.
	 * @param  mixed $val2 Optional, Second value that maybe is an array.
	 * @return array
	 */
	static public function get_array( $val1, $val2 = array() ) {
		if ( is_array( $val1 ) ) {
			return $val1;
		} else if ( is_array( $val2 ) ) {
			return $val2;
		} else {
			return array();
		}
	}

	/**
	 * Returns a list with sidebars that were marked as "modifiable".
	 * Also contains information on the default replacements of these sidebars.
	 *
	 * Option-Key: 'cs_modifiable' (1)
	 */
	static public function get_options( $key = null ) {
		static $Options = null;
		$need_update = false;

		if ( null === $Options ) {
			$Options = get_option( 'cs_modifiable', array() );
			if ( ! is_array( $Options ) ) {
				$Options = array();
			}

			// List of modifiable sidebars.
			if ( ! is_array( @$Options['modifiable'] ) ) {
				// By default we make ALL theme sidebars replaceable:
				$all = self::get_sidebars( 'theme' );
				$Options['modifiable'] = array_keys( $all );
				$need_update = true;
			}

			/**
			 * In version 2.0 four config values have been renamed and are
			 * migrated in the following block:
			 */

			// Single/Archive pages - new names
			$Options['post_type_single'] = self::get_array(
				@$Options['post_type_single'], // new name
				@$Options['defaults']          // old name
			);
			$Options['post_type_archive'] = self::get_array(
				@$Options['post_type_archive'], // new name
				@$Options['post_type_pages']    // old name
			);
			$Options['category_single'] = self::get_array(
				@$Options['category_single'], // new name
				@$Options['category_posts']   // old name
			);
			$Options['category_archive'] = self::get_array(
				@$Options['category_archive'], // new name
				@$Options['category_pages']    // old name
			);

			// Remove old item names from the array.
			if ( isset( $Options['defaults'] ) ) {
				unset( $Options['defaults'] );
				$need_update = true;
			}
			if ( isset( $Options['post_type_pages'] ) ) {
				unset( $Options['post_type_pages'] );
				$need_update = true;
			}
			if ( isset( $Options['category_posts'] ) ) {
				unset( $Options['category_posts'] );
				$need_update = true;
			}
			if ( isset( $Options['category_pages'] ) ) {
				unset( $Options['category_pages'] );
				$need_update = true;
			}

			// Special archive pages
			$Options['blog'] = self::get_array( @$Options['blog'] );
			$Options['tags'] = self::get_array( @$Options['tags'] );
			$Options['authors'] = self::get_array( @$Options['authors'] );
			$Options['search'] = self::get_array( @$Options['search'] );
			$Options['date'] = self::get_array( @$Options['date'] );

			$Options = self::validate_options( $Options );

			if ( $need_update ) {
				self::set_options( $Options );
			}
		}

		if ( ! empty( $key ) ) {
			return @$Options[ $key ];
		} else {
			return $Options;
		}
	}

	/**
	 * Saves the sidebar options to DB.
	 *
	 * Option-Key: 'cs_modifiable' (1)
	 * @since  2.0
	 * @param  array $value The options array.
	 */
	static public function set_options( $value ) {
		// Permission check.
		if ( ! current_user_can( self::$cap_required ) ) {
			return;
		}

		update_option( 'cs_modifiable', $value );
	}

	/**
	 * Removes invalid settings from the options array.
	 *
	 * @since  1.0.4
	 * @param  array $data This array will be validated and returned.
	 * @return array
	 */
	static public function validate_options( $data = null ) {
		$data = (is_object( $data ) ? (array) $data : $data );
		if ( ! is_array( $data ) ) {
			return array();
		}
		$valid = array_keys( self::get_sidebars( 'theme' ) );
		$current = self::get_array( @$data['modifiable'] );

		// Get all the sidebars that are modifiable AND exist.
		$modifiable = array_intersect( $valid, $current );
		$data['modifiable'] = $modifiable;

		return $data;
	}

	/**
	 * Returns a list with all custom sidebars that were created by the user.
	 * Array of custom sidebars
	 *
	 * Option-Key: 'cs_sidebars' (3)
	 */
	static public function get_custom_sidebars() {
		$sidebars = get_option( 'cs_sidebars', array() );
		if ( ! is_array( $sidebars ) ) {
			$sidebars = array();
		}

		// Remove invalid items.
		foreach ( $sidebars as $key => $data ) {
			if ( ! is_array( $data ) ) {
				unset( $sidebars[ $key ] );
			}
		}

		return $sidebars;
	}

	/**
	 * Saves the custom sidebars to DB.
	 *
	 * Option-Key: 'cs_sidebars' (3)
	 * @since  2.0
	 */
	static public function set_custom_sidebars( $value ) {
		// Permission check.
		if ( ! current_user_can( self::$cap_required ) ) {
			return;
		}

		update_option( 'cs_sidebars', $value );
	}

	/**
	 * Returns a list of all registered sidebars including a list of their
	 * widgets (this is stored inside a WordPress core option).
	 *
	 * Option-Key: 'sidebars_widgets' (4)
	 * @since  2.0
	 */
	static public function get_sidebar_widgets() {
		return get_option( 'sidebars_widgets', array() );
	}

	/**
	 * Update the WordPress core settings for sidebar widgets:
	 * 1. Add empty widget information for new sidebars.
	 * 2. Remove widget information for sidebars that no longer exist.
	 *
	 * Option-Key: 'sidebars_widgets' (4)
	 */
	static public function refresh_sidebar_widgets() {
		// Contains an array of all sidebars and widgets inside each sidebar.
		$widgetized_sidebars = self::get_sidebar_widgets();

		$cs_sidebars = self::get_custom_sidebars();
		$delete_widgetized_sidebars = array();


		foreach ( $widgetized_sidebars as $id => $bar ) {
			if ( substr( $id, 0, 3 ) == self::$sidebar_prefix ) {
				$found = FALSE;
				foreach ( $cs_sidebars as $csbar ) {
					if ( $csbar['id'] == $id ) {
						$found = TRUE;
					}
				}
				if ( ! $found ) {
					$delete_widgetized_sidebars[] = $id;
				}
			}
		}

		$all_ids = array_keys( $widgetized_sidebars );
		foreach ( $cs_sidebars as $cs ) {
			$sb_id = $cs['id'];
			if ( ! in_array( $sb_id, $all_ids ) ) {
				$widgetized_sidebars[$sb_id] = array();
			}
		}

		foreach ( $delete_widgetized_sidebars as $id ) {
			unset( $widgetized_sidebars[$id] );
		}

		update_option( 'sidebars_widgets', $widgetized_sidebars );
	}

	/**
	 * Returns the custom sidebar metadata of a single post.
	 *
	 * Meta-Key: '_cs_replacements' (2)
	 * @since  2.0
	 */
	static public function get_post_meta( $post_id ) {
		$data = get_post_meta( $post_id, '_cs_replacements', TRUE );
		if ( ! is_array( $data ) ) {
			$data = array();
		}
		return $data;
	}

	/**
	 * Saves custom sidebar metadata to a single post.
	 *
	 * Meta-Key: '_cs_replacements' (2)
	 * @since  2.0
	 * @param int $post_id
	 * @param array $data When array is empty the meta data will be deleted.
	 */
	static public function set_post_meta( $post_id, $data ) {
		if ( ! empty( $data ) ) {
			update_post_meta( $post_id, '_cs_replacements', $data );
		} else {
			delete_post_meta( $post_id, '_cs_replacements' );
		}
	}

	/**
	 * Returns a list of all sidebars available.
	 * Depending on the parameter this will be either all sidebars or only
	 * sidebars defined by the current theme.
	 *
	 * @param string $type [all|cust|theme] What kind of sidebars to return.
	 */
	static public function get_sidebars( $type = 'theme' ) {
		global $wp_registered_sidebars;
		$allsidebars = $wp_registered_sidebars;
		$result = array();

		// Remove inactive sidebars.
		foreach ( $allsidebars as $sb_id => $sidebar ) {
			if ( false !== strpos( $sidebar['class'], 'inactive-sidebar' ) ) {
				unset( $allsidebars[$sb_id] );
			}
		}

		ksort( $allsidebars );
		if ( $type == 'all' ) {
			$result = $allsidebars;
		} else if ( $type == 'cust' ) {
			foreach ( $allsidebars as $key => $sb ) {
				// Only keep custom sidebars in the results.
				if ( substr( $key, 0, 3 ) == self::$sidebar_prefix ) {
					$result[$key] = $sb;
				}
			}
		} else if ( $type == 'theme' ) {
			foreach ( $allsidebars as $key => $sb ) {
				// Remove custom sidebars from results.
				if ( substr( $key, 0, 3 ) != self::$sidebar_prefix ) {
					$result[$key] = $sb;
				}
			}
		}

		return $result;
	}

	/**
	 * Returns the sidebar with the specified ID.
	 * Sidebar can be both a custom sidebar or theme sidebar.
	 *
	 * @param string $id Sidebar-ID.
	 * @param string $type [all|cust|theme] What kind of sidebars to check.
	 */
	static public function get_sidebar( $id, $type = 'all' ) {
		if ( empty( $id ) ) { return false; }

		// Get all sidebars
		$sidebars = self::get_sidebars( $type );

		if ( isset( $sidebars[ $id ] ) ) {
			return $sidebars[ $id ];
		} else {
			return false;
		}
	}

	/**
	 * Get sidebar replacement information for a single post.
	 */
	static public function get_replacements( $postid ) {
		$replacements = self::get_post_meta( $postid );
		if ( ! is_array( $replacements ) ) {
			$replacements = array();
		} else {
			$replacements = $replacements;
		}
		return $replacements;
	}

	/**
	 * Returns true, when the specified post type supports custom sidebars.
	 *
	 * @since  2.0
	 * @param  object|string $posttype The posttype to validate. Either the
	 *                posttype name or the full posttype object.
	 * @return bool
	 */
	static public function supported_post_type( $posttype ) {
		$Ignored_types = null;
		$Response = array();

		if ( null === $Ignored_types ) {
			$Ignored_types = get_post_types(
				array( 'public' => false ),
				'names'
			);
			$Ignored_types[] = 'attachment';
		}

		if ( is_object( $posttype ) ) {
			$posttype = $posttype->name;
		}

		if ( ! isset( $Response[ $posttype ] ) ) {
			$response = ! in_array( $posttype, $Ignored_types );

			/**
			 * Filters the support-flag. The flag defines if the posttype supports
			 * custom sidebars or not.
			 *
			 * @since  2.0
			 *
			 * @param  bool $response Flag if the posttype is supported.
			 * @param  string $posttype Name of the posttype that is checked.
			 */
			$response = apply_filters( 'cs_support_posttype', $response, $posttype );
			$Response[ $posttype ] = $response;
		}

		return $Response[ $posttype ];
	}

	/**
	 * Returns a list of all post types that support custom sidebars.
	 *
	 * @uses   self::supported_post_type()
	 * @param  string $type [names|objects] Defines details of return data.
	 * @return array List of posttype names or objects, depending on the param.
	 */
	static public function get_post_types( $type = 'names' ) {
		$Valid = array();

		if ( $type != 'objects' ) {
			$type = 'names';
		}

		if ( ! isset( $Valid[ $type ] ) ) {
			$all = get_post_types( array(), $type );
			$Valid[ $type ] = array();

			foreach ( $all as $post_type ) {
				if ( self::supported_post_type( $post_type ) ) {
					$Valid[ $type ][] = $post_type;
				}
			}
		}

		return $Valid[ $type ];
	}

	/**
	 * Returns an array of all categories.
	 *
	 * @since  2.0
	 * @return array List of categories, including empty ones.
	 */
	static public function get_all_categories() {
		$args = array(
			'hide_empty' => 0,
			'taxonomy' => 'category',
		);

		return get_categories( $args );
	}

	/**
	 * Returns a sorted list of all category terms of the current post.
	 * This information is used to find sidebar replacements.
	 *
	 * @uses  self::cmp_cat_level()
	 */
	static public function get_sorted_categories( $post_id = null ) {
		static $Sorted = array();

		// Return categories of current post when no post_id is specified.
		$post_id = empty( $post_id ) ? get_the_ID() : $post_id;

		if ( ! isset( $Sorted[ $post_id ] ) ) {
			$Sorted[ $post_id ] = get_the_category( $post_id );
			@usort( $Sorted[ $post_id ], array( self, 'cmp_cat_level' ) );
		}
		return $Sorted[ $post_id ];
	}

	/**
	 * Helper function used to sort categories.
	 *
	 * @uses  self::get_category_level()
	 */
	static public function cmp_cat_level( $cat1, $cat2 ) {
		$l1 = self::get_category_level( $cat1->cat_ID );
		$l2 = self::get_category_level( $cat2->cat_ID );
		if ( $l1 == $l2 ) {
			return strcasecmp( $cat1->name, $cat1->name );
		} else {
			return $l1 < $l2 ? 1 : -1;
		}
	}

	/**
	 * Helper function used to sort categories.
	 */
	static public function get_category_level( $catid ) {
		if ( $catid == 0 ) {
			return 0;
		}

		$cat = get_category( $catid );
		return 1 + self::get_category_level( $cat->category_parent );
	}


	// =========================================================================
	// == ACTION HOOKS
	// =========================================================================


	/**
	 * Callback for in_widget_form action
	 *
	 * Free version only
	 *
	 * @since 2.0.1
	 */
	public function in_widget_form( $widget ) {
		?>
		<input type="hidden" name="csb-buttons" value="0" />
		<?php if ( ! isset( $_POST[ 'csb-buttons' ] ) ) : ?>
			<div class="csb-pro-layer csb-pro-<?php echo esc_attr( $widget->id ); ?>">
				<a href="#" class="button csb-clone-button"><?php _e( 'Clone', CSB_LANG ); ?></a>
				<a href="#" class="button csb-visibility-button"><span class="dashicons dashicons-visibility"></span> <?php _e( 'Visibility', CSB_LANG ); ?></a>
				<a href="<?php echo esc_url( CustomSidebars::$pro_url ); ?>" target="_blank" class="pro-info">
				<?php printf(
					__( 'Pro Version Features', CSB_LANG ),
					CustomSidebars::$pro_url
				); ?>
				</a>
			</div>
		<?php
		endif;
	}


	// =========================================================================
	// == AJAX FUNCTIONS
	// =========================================================================


	/**
	 * Output JSON data and die()
	 *
	 * @since  1.0.0
	 */
	static protected function json_response( $obj ) {
		// Flush any output that was made prior to this function call
		while ( 0 < ob_get_level() ) { ob_end_clean(); }

		header( 'Content-Type: application/json' );
		echo json_encode( $obj );
		die();
	}

	/**
	 * Output HTML data and die()
	 *
	 * @since  2.0
	 */
	static protected function plain_response( $data ) {
		// Flush any output that was made prior to this function call
		while ( 0 < ob_get_level() ) { ob_end_clean(); }

		header( 'Content-Type: text/plain' );
		echo $data;
		die();
	}

	/**
	 * Sets the response object to ERR state with the specified message/reason.
	 *
	 * @since  2.0
	 * @param  object $req Initial response object.
	 * @param  string $message Error message or reason; already translated.
	 * @return object Updated response object.
	 */
	static protected function req_err( $req, $message ) {
		$req->status = 'ERR';
		$req->message = $message;
		return $req;
	}

	/**
	 * All Ajax request are handled by this function.
	 * It analyzes the post-data and calls the required functions to execute
	 * the requested action.
	 *
	 * --------------------------------
	 *
	 * IMPORTANT! ANY SERVER RESPONSE MUST BE MADE VIA ONE OF THESE FUNCTIONS!
	 * Using direct `echo` or include an html file will not work.
	 *
	 *    self::json_response( $obj )
	 *    self::plain_response( $text )
	 *
	 * --------------------------------
	 *
	 * @since  1.0.0
	 */
	public function ajax_handler() {
		// Permission check.
		if ( ! current_user_can( self::$cap_required ) ) {
			return;
		}

		// Try to disable debug output for ajax handlers of this plugin.
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			defined( 'WP_DEBUG_DISPLAY' ) || define( 'WP_DEBUG_DISPLAY', false );
			defined( 'WP_DEBUG_LOG' ) || define( 'WP_DEBUG_LOG', true );
		}
		// Catch any unexpected output via output buffering.
		ob_start();

		$action = @$_POST['do'];

		/**
		 * Notify all extensions about the ajax call.
		 *
		 * @since  2.0
		 * @param  string $action The specified ajax action.
		 */
		do_action( 'cs_ajax_request', $action );
	}
};
