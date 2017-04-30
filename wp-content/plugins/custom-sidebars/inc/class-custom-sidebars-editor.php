<?php

add_action( 'cs_init', array( 'CustomSidebarsEditor', 'instance' ) );

/**
 * Provides all the functionality for editing sidebars on the widgets page.
 */
class CustomSidebarsEditor extends CustomSidebars {

	/**
	 * Returns the singleton object.
	 *
	 * @since  2.0
	 */
	public static function instance() {
		static $Inst = null;

		if ( null === $Inst ) {
			$Inst = new CustomSidebarsEditor();
		}

		return $Inst;
	}

	/**
	 * Constructor is private -> singleton.
	 *
	 * @since  2.0
	 */
	private function __construct() {
		if ( is_admin() ) {
			// Add the sidebar metabox to posts.
			add_action(
				'add_meta_boxes',
				array( $this, 'add_meta_box' )
			);

			// Save the options from the sidebars-metabox.
			add_action(
				'save_post',
				array( $this, 'store_replacements' )
			);

			// Handle ajax requests.
			add_action(
				'cs_ajax_request',
				array( $this, 'handle_ajax' )
			);
		}
	}

	/**
	 * Handles the ajax requests.
	 */
	public function handle_ajax( $action ) {
		$req = (object) array(
			'status' => 'ERR',
		);
		$is_json = true;
		$handle_it = false;
		$view_file = '';

		$sb_id = @$_POST['sb'];

		switch ( $action ) {
			case 'get':
			case 'save':
			case 'delete':
			case 'get-location':
			case 'set-location':
			case 'replaceable':
				$handle_it = true;
				$req->status = 'OK';
				$req->action = $action;
				$req->id = $sb_id;
				break;
		}

		// The ajax request was not meant for us...
		if ( ! $handle_it ) {
			return false;
		}

		$sb_data = self::get_sidebar( $sb_id );

		if ( ! current_user_can( self::$cap_required ) ) {
			$req = self::req_err(
				$req,
				__( 'You do not have permission for this', CSB_LANG )
			);
		} else {
			switch ( $action ) {
				// Return details for the specified sidebar.
				case 'get':
					$req->sidebar = $sb_data;
					break;

				// Save or insert the specified sidebar.
				case 'save':
					$req = $this->save_item( $req, $_POST );
					break;

				// Delete the specified sidebar.
				case 'delete':
					$req->sidebar = $sb_data;
					$req = $this->delete_item( $req );
					break;

				// Get the location data.
				case 'get-location':
					$req->sidebar = $sb_data;
					$req = $this->get_location_data( $req );
					break;

				// Update the location data.
				case 'set-location':
					$req->sidebar = $sb_data;
					$req = $this->set_location_data( $req );
					break;

				// Toggle theme sidebar replaceable-flag.
				case 'replaceable':
					$req = $this->set_replaceable( $req );
					break;
			}
		}

		// Make the ajax response either as JSON or plain text.
		if ( $is_json ) {
			self::json_response( $req );
		} else {
			ob_start();
			include CSB_VIEWS_DIR . $view_file;
			$resp = ob_get_clean();

			self::plain_response( $resp );
		}
	}

	/**
	 * Saves the item specified by $data array and populates the response
	 * object. When $req->id is empty a new sidebar will be created. Otherwise
	 * the existing sidebar is updated.
	 *
	 * @since  2.0
	 * @param  object $req Initial response object.
	 * @param  array $data Sidebar data to save (typically this is $_POST).
	 * @return object Updated response object.
	 */
	private function save_item( $req, $data )  {
		$sidebars = self::get_custom_sidebars();
		$sb_id = $req->id;
		$sb_name = substr( stripslashes( trim( @$data['name'] ) ), 0, 40 );
		$sb_desc = stripslashes( trim( @$_POST['description'] ) );

		if ( empty( $sb_name ) ) {
			return self::req_err(
				$req,
				__( 'Sidebar-name cannot be empty', CSB_LANG )
			);
		}

		if ( empty( $sb_id ) ) {
			// Create a new sidebar.
			$action = 'insert';
			$num = count( $sidebars );
			do {
				$num += 1;
				$sb_id = self::$sidebar_prefix . $num;
			} while ( self::get_sidebar( $sb_id, 'cust' ) );

			$sidebar = array(
				'id' => $sb_id,
			);
		} else {
			// Update existing sidebar
			$action = 'update';
			$sidebar = self::get_sidebar( $sb_id, 'cust' );

			if ( ! $sidebar ) {
				return self::req_err(
					$req,
					__( 'The sidebar does not exist', CSB_LANG )
				);
			}
		}

		if ( strlen( $sb_desc ) > 200 ) {
			$sb_desc = substr( $sb_desc, 0, 200 );
		}

		// Populate the sidebar object.
		$sidebar['name'] = $sb_name;
		$sidebar['description'] = $sb_desc;
		$sidebar['before_widget'] = stripslashes( trim( @$_POST['before_widget'] ) );
		$sidebar['after_widget'] = stripslashes( trim( @$_POST['after_widget'] ) );
		$sidebar['before_title'] = stripslashes( trim( @$_POST['before_title'] ) );
		$sidebar['after_title'] = stripslashes( trim( @$_POST['after_title'] ) );

		if ( $action == 'insert' ) {
			$sidebars[] = $sidebar;
			$req->message = sprintf(
				__( 'Created new sidebar <strong>%1$s</strong>', CSB_LANG ),
				esc_html( $sidebar['name'] )
			);
		} else {
			$found = false;
			foreach ( $sidebars as $ind => $item ) {
				if ( $item['id'] == $sb_id ) {
					$req->message = sprintf(
						__( 'Updated sidebar <strong>%1$s</strong>', CSB_LANG ),
						esc_html( $sidebar['name'] )
					);
					$sidebars[ $ind ] = $sidebar;
					$found = true;
					break;
				}
			}
			if ( ! $found ) {
				return self::req_err(
					$req,
					__( 'The sidebar was not found', CSB_LANG )
				);
			}
		}

		// Save the changes.
		self::set_custom_sidebars( $sidebars );
		self::refresh_sidebar_widgets();

		$req->action = $action;
		$req->data = $sidebar;

		return $req;
	}

	/**
	 * Delete the specified sidebar and update the response object.
	 *
	 * @since  2.0
	 * @param  object $req Initial response object.
	 * @return object Updated response object.
	 */
	private function delete_item( $req ) {
		$sidebars = self::get_custom_sidebars();
		$sidebar = self::get_sidebar( $req->id, 'cust' );

		if ( ! $sidebar ) {
			return self::req_err(
				$req,
				__( 'The sidebar does not exist', CSB_LANG )
			);
		}

		$found = false;
		foreach ( $sidebars as $ind => $item ) {
			if ( $item['id'] == $req->id ) {
				$found = true;
				$req->message = sprintf(
					__( 'Deleted sidebar <strong>%1$s</strong>', CSB_LANG ),
					esc_html( $req->sidebar['name'] )
				);
				unset( $sidebars[ $ind ] );
				break;
			}
		}

		if ( ! $found ) {
			return self::req_err(
				$req,
				__( 'The sidebar was not found', CSB_LANG )
			);
		}

		// Save the changes.
		self::set_custom_sidebars( $sidebars );
		self::refresh_sidebar_widgets();

		return $req;
	}

	/**
	 * Save the repaceable flag of a theme sidebar.
	 *
	 * @since  2.0
	 * @param  object $req Initial response object.
	 * @return object Updated response object.
	 */
	private function set_replaceable( $req ) {
		$state = @$_POST['state'];

		$options = self::get_options();
		if ( 'true' === $state ) {
			$req->replaceable = true;
			if ( ! in_array( $req->id, $options['modifiable'] ) ) {
				$options['modifiable'][] = $req->id;
			}
		} else {
			$req->replaceable = false;
			for ( $i = count( $options['modifiable'] ) - 1; $i >= 0; $i -= 1 ) {
				if ( $options['modifiable'][$i] == $req->id ) {
					unset( $options['modifiable'][$i] );
					break;
				}
			}
		}
		$options['modifiable'] = array_values( $options['modifiable'] );
		self::set_options( $options );

		return $req;
	}

	/**
	 * Populates the response object for the "get-location" ajax call.
	 * Location data defines where a custom sidebar is displayed, i.e. on which
	 * pages it is used and which theme-sidebars are replaced.
	 *
	 * @since  2.0
	 * @param  object $req Initial response object.
	 * @return object Updated response object.
	 */
	private function get_location_data( $req ) {
		$defaults = self::get_options();
		$raw_posttype = self::get_post_types( 'objects' );
		$raw_cat = self::get_all_categories();

		$archive_type = array(
			'_blog' => __( 'Front Page', CSB_LANG ),
			'_search' => __( 'Search Results', CSB_LANG ),
			'_authors' => __( 'Author Archives', CSB_LANG ),
			'_tags' => __( 'Tag Archives', CSB_LANG ),
			'_date' => __( 'Date Archives', CSB_LANG ),
		);

		// Collect required data for all posttypes.
		$posttypes = array();
		foreach ( $raw_posttype as $item ) {
			$sel_single = @$defaults['post_type_single'][$item->name];

			$posttypes[ $item->name ] = array(
				'name' => $item->labels->name,
				'single' => self::get_array( $sel_single ),
			);
		}

		// Extract the data from categories list that we need.
		$categories = array();
		foreach ( $raw_cat as $item ) {
			$sel_single = @$defaults['category_single'][$item->term_id];
			$sel_archive = @$defaults['category_archive'][$item->term_id];

			$categories[ $item->term_id ] = array(
				'name' => $item->name,
				'count' => $item->count,
				'single' => self::get_array( $sel_single ),
				'archive' => self::get_array( $sel_archive ),
			);
		}

		// Build a list of archive types.
		$archives = array(); // Start with a copy of the posttype list.
		foreach ( $raw_posttype as $item ) {
			$sel_archive = @$defaults['post_type_archive'][$item->name];

			$label = sprintf(
				__( '%1$s Archives', CSB_LANG ),
				$item->labels->singular_name
			);

			$archives[ $item->name ] = array(
				'name' => $label,
				'archive' => self::get_array( $sel_archive ),
			);
		}

		foreach ( $archive_type as $key => $name ) {
			$sel_archive = @$defaults[ substr( $key, 1 ) ];

			$archives[ $key ] = array(
				'name' => $name,
				'archive' => self::get_array( $sel_archive ),
			);
		}

		$req->replaceable = $defaults['modifiable'];
		$req->posttypes = $posttypes;
		$req->categories = $categories;
		$req->archives = $archives;
		return $req;
	}

	/**
	 * Save location data for a single sidebar and populate the response object.
	 * Location data defines where a custom sidebar is displayed, i.e. on which
	 * pages it is used and which theme-sidebars are replaced.
	 *
	 * @since  2.0
	 * @param  object $req Initial response object.
	 * @return object Updated response object.
	 */
	private function set_location_data( $req ) {
		$options = self::get_options();
		$sidebars = $options['modifiable'];
		$raw_posttype = self::get_post_types( 'objects' );
		$raw_cat = self::get_all_categories();
		$data = @$_POST['cs'];
		$special_arc = array(
			'blog',
			'tags',
			'authors',
			'search',
			'date',
		);

		// == Update the options

		foreach ( $sidebars as $sb_id ) {
			// Post-type settings.
			foreach ( $raw_posttype as $item ) {
				$pt = $item->name;
				if (
					is_array( @$data['pt'][$sb_id] ) &&
					in_array( $pt, $data['pt'][$sb_id] )
				) {
					$options['post_type_single'][$pt][$sb_id] = $req->id;
				} else
				if (
					isset( $options['post_type_single'][$pt][$sb_id] ) &&
					$options['post_type_single'][$pt][$sb_id] == $req->id
				) {
					unset( $options['post_type_single'][$pt][$sb_id] );
				}

				if (
					is_array( @$data['arc'][$sb_id] ) &&
					in_array( $pt, $data['arc'][$sb_id] )
				) {
					$options['post_type_archive'][$pt][$sb_id] = $req->id;
				} else
				if (
					isset( $options['post_type_archive'][$pt][$sb_id] ) &&
					$options['post_type_archive'][$pt][$sb_id] == $req->id
				) {
					unset( $options['post_type_archive'][$pt][$sb_id] );
				}
			}

			// Category settings.
			foreach ( $raw_cat as $item ) {
				$cat = $item->term_id;
				if (
					is_array( @$data['cat'][$sb_id] ) &&
					in_array( $cat, $data['cat'][$sb_id] )
				) {
					$options['category_single'][$cat][$sb_id] = $req->id;
				} else
				if (
					isset( $options['category_single'][$cat][$sb_id] ) &&
					$options['category_single'][$cat][$sb_id] == $req->id
				) {
					unset( $options['category_single'][$cat][$sb_id] );
				}

				if (
					is_array( @$data['arc-cat'][$sb_id] ) &&
					in_array( $cat, $data['arc-cat'][$sb_id] )
				) {
					$options['category_archive'][$cat][$sb_id] = $req->id;
				} else
				if (
					isset( $options['category_archive'][$cat][$sb_id] ) &&
					$options['category_archive'][$cat][$sb_id] == $req->id
				) {
					unset( $options['category_archive'][$cat][$sb_id] );
				}
			}

			foreach ( $special_arc as $key ) {
				if (
					is_array( @$data['arc'][$sb_id] ) &&
					in_array( '_' . $key, $data['arc'][$sb_id] )
				) {
					$options[$key][$sb_id] = $req->id;
				} else
				if (
					isset( $options[$key][$sb_id] ) &&
					$options[$key][$sb_id] == $req->id
				) {
					unset( $options[$key][$sb_id] );
				}
			}
		}

		$req->message = sprintf(
			__( 'Updated sidebar <strong>%1$s</strong> settings.', CSB_LANG ),
			esc_html( $req->sidebar['name'] )
		);
		self::set_options( $options );
		return $req;
	}

	/**
	 * Registers the "Sidebars" meta box in the post-editor.
	 */
	public function add_meta_box() {
		global $post;

		$post_type = get_post_type( $post );
		if ( ! $post_type ) { return false; }
		if ( ! self::supported_post_type( $post_type ) ) { return false; }

		/**
		 * Option that can be set in wp-config.php to remove the custom sidebar
		 * meta box for certain post types.
		 *
		 * @since  2.0
		 *
		 * @option bool TRUE will hide all meta boxes.
		 */
		if (
			defined( 'CUSTOM_SIDEBAR_DISABLE_METABOXES' ) &&
			CUSTOM_SIDEBAR_DISABLE_METABOXES == true
		) {
			return false;
		}

		$pt_obj = get_post_type_object( $post_type );
		if ( $pt_obj->publicly_queryable || $pt_obj->public ) {
			add_meta_box(
				'customsidebars-mb',
				__( 'Sidebars', CSB_LANG ),
				array( $this, 'print_metabox' ),
				$post_type,
				'side'
			);
		}
	}

	/**
	 * Renders the Custom Sidebars meta box in the post-editor.
	 */
	public function print_metabox() {
		global $post, $wp_registered_sidebars;

		$replacements = self::get_replacements( $post->ID );

		$available = $wp_registered_sidebars;
		ksort( $available );
		$sidebars = self::get_options( 'modifiable' );
		$selected = array();
		if ( ! empty( $sidebars ) ) {
			foreach ( $sidebars as $s ) {
				if ( isset( $replacements[$s] ) ) {
					$selected[$s] = $replacements[$s];
				} else {
					$selected[$s] = '';
				}
			}
		}

		include CSB_VIEWS_DIR . 'metabox.php';
	}


	public function store_replacements( $post_id ) {
		global $action;

		if ( ! current_user_can( self::$cap_required ) ) {
			return;
		}

		/*
		 * Verify if this is an auto save routine. If it is our form has not
		 * been submitted, so we dont want to do anything
		 * (Copied and pasted from wordpress add_metabox_tutorial)
		 */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		/*
		 * Make sure we are editing the post normaly, if we are bulk editing or
		 * quick editing, no sidebar data is recieved and the sidebars would
		 * be deleted.
		 */
		if ( $action != 'editpost' ) {
			return $post_id;
		}

		// Make sure meta is added to the post, not a revision.
		if ( $the_post = wp_is_post_revision( $post_id ) ) {
			$post_id = $the_post;
		}

		$sidebars = self::get_options( 'modifiable' );
		$data = array();
		if ( ! empty( $sidebars ) ) {
			foreach ( $sidebars as $sb_id ) {
				if ( isset( $_POST[ 'cs_replacement_' . $sb_id ] ) ) {
					$replacement = $_POST[ 'cs_replacement_' . $sb_id ];
					if ( ! empty( $replacement ) && $replacement != '' ) {
						$data[ $sb_id ] = $replacement;
					}
				}
			}
		}

		self::set_post_meta( $post_id, $data );
	}

};
