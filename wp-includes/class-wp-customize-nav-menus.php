<?php
/**
 * WordPress Customize Nav Menus classes
 *
 * @package WordPress
 * @subpackage Customize
 * @since 4.3.0
 */

/**
 * Customize Nav Menus class.
 *
 * Implements menu management in the Customizer.
 *
 * @since 4.3.0
 *
 * @see WP_Customize_Manager
 */
final class WP_Customize_Nav_Menus {

	/**
	 * WP_Customize_Manager instance.
	 *
	 * @since 4.3.0
	 * @access public
	 * @var WP_Customize_Manager
	 */
	public $manager;

	/**
	 * Previewed Menus.
	 *
	 * @since 4.3.0
	 * @access public
	 * @var array
	 */
	public $previewed_menus;

	/**
	 * Constructor.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @param object $manager An instance of the WP_Customize_Manager class.
	 */
	public function __construct( $manager ) {
		$this->previewed_menus = array();
		$this->manager         = $manager;

		// See https://github.com/xwp/wp-customize-snapshots/blob/962586659688a5b1fd9ae93618b7ce2d4e7a421c/php/class-customize-snapshot-manager.php#L469-L499
		add_action( 'customize_register', array( $this, 'customize_register' ), 11 );
		add_filter( 'customize_dynamic_setting_args', array( $this, 'filter_dynamic_setting_args' ), 10, 2 );
		add_filter( 'customize_dynamic_setting_class', array( $this, 'filter_dynamic_setting_class' ), 10, 3 );

		// Skip remaining hooks when the user can't manage nav menus anyway.
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		add_filter( 'customize_refresh_nonces', array( $this, 'filter_nonces' ) );
		add_action( 'wp_ajax_load-available-menu-items-customizer', array( $this, 'ajax_load_available_items' ) );
		add_action( 'wp_ajax_search-available-menu-items-customizer', array( $this, 'ajax_search_available_items' ) );
		add_action( 'wp_ajax_customize-nav-menus-insert-auto-draft', array( $this, 'ajax_insert_auto_draft_post' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'print_templates' ) );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'available_items_template' ) );
		add_action( 'customize_preview_init', array( $this, 'customize_preview_init' ) );
		add_action( 'customize_preview_init', array( $this, 'make_auto_draft_status_previewable' ) );
		add_action( 'customize_save_nav_menus_created_posts', array( $this, 'save_nav_menus_created_posts' ) );

		// Selective Refresh partials.
		add_filter( 'customize_dynamic_partial_args', array( $this, 'customize_dynamic_partial_args' ), 10, 2 );
	}

	/**
	 * Adds a nonce for customizing menus.
	 *
	 * @since 4.5.0
	 * @access public
	 *
	 * @param array $nonces Array of nonces.
	 * @return array $nonces Modified array of nonces.
	 */
	public function filter_nonces( $nonces ) {
		$nonces['customize-menus'] = wp_create_nonce( 'customize-menus' );
		return $nonces;
	}

	/**
	 * Ajax handler for loading available menu items.
	 *
	 * @since 4.3.0
	 * @access public
	 */
	public function ajax_load_available_items() {
		check_ajax_referer( 'customize-menus', 'customize-menus-nonce' );

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_die( -1 );
		}

		$all_items = array();
		$item_types = array();
		if ( isset( $_POST['item_types'] ) && is_array( $_POST['item_types'] ) ) {
			$item_types = wp_unslash( $_POST['item_types'] );
		} elseif ( isset( $_POST['type'] ) && isset( $_POST['object'] ) ) { // Back compat.
			$item_types[] = array(
				'type' => wp_unslash( $_POST['type'] ),
				'object' => wp_unslash( $_POST['object'] ),
				'page' => empty( $_POST['page'] ) ? 0 : absint( $_POST['page'] ),
			);
		} else {
			wp_send_json_error( 'nav_menus_missing_type_or_object_parameter' );
		}

		foreach ( $item_types as $item_type ) {
			if ( empty( $item_type['type'] ) || empty( $item_type['object'] ) ) {
				wp_send_json_error( 'nav_menus_missing_type_or_object_parameter' );
			}
			$type = sanitize_key( $item_type['type'] );
			$object = sanitize_key( $item_type['object'] );
			$page = empty( $item_type['page'] ) ? 0 : absint( $item_type['page'] );
			$items = $this->load_available_items_query( $type, $object, $page );
			if ( is_wp_error( $items ) ) {
				wp_send_json_error( $items->get_error_code() );
			}
			$all_items[ $item_type['type'] . ':' . $item_type['object'] ] = $items;
		}

		wp_send_json_success( array( 'items' => $all_items ) );
	}

	/**
	 * Performs the post_type and taxonomy queries for loading available menu items.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @param string $type   Optional. Accepts any custom object type and has built-in support for
	 *                         'post_type' and 'taxonomy'. Default is 'post_type'.
	 * @param string $object Optional. Accepts any registered taxonomy or post type name. Default is 'page'.
	 * @param int    $page   Optional. The page number used to generate the query offset. Default is '0'.
	 * @return WP_Error|array Returns either a WP_Error object or an array of menu items.
	 */
	public function load_available_items_query( $type = 'post_type', $object = 'page', $page = 0 ) {
		$items = array();

		if ( 'post_type' === $type ) {
			$post_type = get_post_type_object( $object );
			if ( ! $post_type ) {
				return new WP_Error( 'nav_menus_invalid_post_type' );
			}

			if ( 0 === $page && 'page' === $object ) {
				// Add "Home" link. Treat as a page, but switch to custom on add.
				$items[] = array(
					'id'         => 'home',
					'title'      => _x( 'Home', 'nav menu home label' ),
					'type'       => 'custom',
					'type_label' => __( 'Custom Link' ),
					'object'     => '',
					'url'        => home_url(),
				);
			} elseif ( 'post' !== $object && 0 === $page && $post_type->has_archive ) {
				// Add a post type archive link.
				$items[] = array(
					'id'         => $object . '-archive',
					'title'      => $post_type->labels->archives,
					'type'       => 'post_type_archive',
					'type_label' => __( 'Post Type Archive' ),
					'object'     => $object,
					'url'        => get_post_type_archive_link( $object ),
				);
			}

			// Prepend posts with nav_menus_created_posts on first page.
			$posts = array();
			if ( 0 === $page && $this->manager->get_setting( 'nav_menus_created_posts' ) ) {
				foreach ( $this->manager->get_setting( 'nav_menus_created_posts' )->value() as $post_id ) {
					$auto_draft_post = get_post( $post_id );
					if ( $post_type->name === $auto_draft_post->post_type ) {
						$posts[] = $auto_draft_post;
					}
				}
			}

			$posts = array_merge( $posts, get_posts( array(
				'numberposts' => 10,
				'offset'      => 10 * $page,
				'orderby'     => 'date',
				'order'       => 'DESC',
				'post_type'   => $object,
			) ) );

			foreach ( $posts as $post ) {
				$post_title = $post->post_title;
				if ( '' === $post_title ) {
					/* translators: %d: ID of a post */
					$post_title = sprintf( __( '#%d (no title)' ), $post->ID );
				}
				$items[] = array(
					'id'         => "post-{$post->ID}",
					'title'      => html_entity_decode( $post_title, ENT_QUOTES, get_bloginfo( 'charset' ) ),
					'type'       => 'post_type',
					'type_label' => get_post_type_object( $post->post_type )->labels->singular_name,
					'object'     => $post->post_type,
					'object_id'  => intval( $post->ID ),
					'url'        => get_permalink( intval( $post->ID ) ),
				);
			}
		} elseif ( 'taxonomy' === $type ) {
			$terms = get_terms( $object, array(
				'child_of'     => 0,
				'exclude'      => '',
				'hide_empty'   => false,
				'hierarchical' => 1,
				'include'      => '',
				'number'       => 10,
				'offset'       => 10 * $page,
				'order'        => 'DESC',
				'orderby'      => 'count',
				'pad_counts'   => false,
			) );
			if ( is_wp_error( $terms ) ) {
				return $terms;
			}

			foreach ( $terms as $term ) {
				$items[] = array(
					'id'         => "term-{$term->term_id}",
					'title'      => html_entity_decode( $term->name, ENT_QUOTES, get_bloginfo( 'charset' ) ),
					'type'       => 'taxonomy',
					'type_label' => get_taxonomy( $term->taxonomy )->labels->singular_name,
					'object'     => $term->taxonomy,
					'object_id'  => intval( $term->term_id ),
					'url'        => get_term_link( intval( $term->term_id ), $term->taxonomy ),
				);
			}
		}

		/**
		 * Filters the available menu items.
		 *
		 * @since 4.3.0
		 *
		 * @param array  $items  The array of menu items.
		 * @param string $type   The object type.
		 * @param string $object The object name.
		 * @param int    $page   The current page number.
		 */
		$items = apply_filters( 'customize_nav_menu_available_items', $items, $type, $object, $page );

		return $items;
	}

	/**
	 * Ajax handler for searching available menu items.
	 *
	 * @since 4.3.0
	 * @access public
	 */
	public function ajax_search_available_items() {
		check_ajax_referer( 'customize-menus', 'customize-menus-nonce' );

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_die( -1 );
		}

		if ( empty( $_POST['search'] ) ) {
			wp_send_json_error( 'nav_menus_missing_search_parameter' );
		}

		$p = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 0;
		if ( $p < 1 ) {
			$p = 1;
		}

		$s = sanitize_text_field( wp_unslash( $_POST['search'] ) );
		$items = $this->search_available_items_query( array( 'pagenum' => $p, 's' => $s ) );

		if ( empty( $items ) ) {
			wp_send_json_error( array( 'message' => __( 'No results found.' ) ) );
		} else {
			wp_send_json_success( array( 'items' => $items ) );
		}
	}

	/**
	 * Performs post queries for available-item searching.
	 *
	 * Based on WP_Editor::wp_link_query().
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @param array $args Optional. Accepts 'pagenum' and 's' (search) arguments.
	 * @return array Menu items.
	 */
	public function search_available_items_query( $args = array() ) {
		$items = array();

		$post_type_objects = get_post_types( array( 'show_in_nav_menus' => true ), 'objects' );
		$query = array(
			'post_type'              => array_keys( $post_type_objects ),
			'suppress_filters'       => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'post_status'            => 'publish',
			'posts_per_page'         => 20,
		);

		$args['pagenum'] = isset( $args['pagenum'] ) ? absint( $args['pagenum'] ) : 1;
		$query['offset'] = $args['pagenum'] > 1 ? $query['posts_per_page'] * ( $args['pagenum'] - 1 ) : 0;

		if ( isset( $args['s'] ) ) {
			$query['s'] = $args['s'];
		}

		$posts = array();

		// Prepend list of posts with nav_menus_created_posts search results on first page.
		$nav_menus_created_posts_setting = $this->manager->get_setting( 'nav_menus_created_posts' );
		if ( 1 === $args['pagenum'] && $nav_menus_created_posts_setting && count( $nav_menus_created_posts_setting ) > 0 ) {
			$stub_post_query = new WP_Query( array_merge(
				$query,
				array(
					'post_status' => 'auto-draft',
					'post__in' => $nav_menus_created_posts_setting->value(),
					'posts_per_page' => -1,
				)
			) );
			$posts = array_merge( $posts, $stub_post_query->posts );
		}

		// Query posts.
		$get_posts = new WP_Query( $query );
		$posts = array_merge( $posts, $get_posts->posts );

		// Create items for posts.
		foreach ( $posts as $post ) {
			$post_title = $post->post_title;
			if ( '' === $post_title ) {
				/* translators: %d: ID of a post */
				$post_title = sprintf( __( '#%d (no title)' ), $post->ID );
			}
			$items[] = array(
				'id'         => 'post-' . $post->ID,
				'title'      => html_entity_decode( $post_title, ENT_QUOTES, get_bloginfo( 'charset' ) ),
				'type'       => 'post_type',
				'type_label' => $post_type_objects[ $post->post_type ]->labels->singular_name,
				'object'     => $post->post_type,
				'object_id'  => intval( $post->ID ),
				'url'        => get_permalink( intval( $post->ID ) ),
			);
		}

		// Query taxonomy terms.
		$taxonomies = get_taxonomies( array( 'show_in_nav_menus' => true ), 'names' );
		$terms = get_terms( $taxonomies, array(
			'name__like' => $args['s'],
			'number'     => 20,
			'offset'     => 20 * ($args['pagenum'] - 1),
		) );

		// Check if any taxonomies were found.
		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$items[] = array(
					'id'         => 'term-' . $term->term_id,
					'title'      => html_entity_decode( $term->name, ENT_QUOTES, get_bloginfo( 'charset' ) ),
					'type'       => 'taxonomy',
					'type_label' => get_taxonomy( $term->taxonomy )->labels->singular_name,
					'object'     => $term->taxonomy,
					'object_id'  => intval( $term->term_id ),
					'url'        => get_term_link( intval( $term->term_id ), $term->taxonomy ),
				);
			}
		}

		/**
		 * Filters the available menu items during a search request.
		 *
		 * @since 4.5.0
		 *
		 * @param array $items The array of menu items.
		 * @param array $args  Includes 'pagenum' and 's' (search) arguments.
		 */
		$items = apply_filters( 'customize_nav_menu_searched_items', $items, $args );

		return $items;
	}

	/**
	 * Enqueue scripts and styles for Customizer pane.
	 *
	 * @since 4.3.0
	 * @access public
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'customize-nav-menus' );
		wp_enqueue_script( 'customize-nav-menus' );

		$temp_nav_menu_setting      = new WP_Customize_Nav_Menu_Setting( $this->manager, 'nav_menu[-1]' );
		$temp_nav_menu_item_setting = new WP_Customize_Nav_Menu_Item_Setting( $this->manager, 'nav_menu_item[-1]' );

		// Pass data to JS.
		$settings = array(
			'allMenus'             => wp_get_nav_menus(),
			'itemTypes'            => $this->available_item_types(),
			'l10n'                 => array(
				'untitled'          => _x( '(no label)', 'missing menu item navigation label' ),
				'unnamed'           => _x( '(unnamed)', 'Missing menu name.' ),
				'custom_label'      => __( 'Custom Link' ),
				'page_label'        => get_post_type_object( 'page' )->labels->singular_name,
				/* translators: %s: menu location */
				'menuLocation'      => _x( '(Currently set to: %s)', 'menu' ),
				'menuNameLabel'     => __( 'Menu Name' ),
				'itemAdded'         => __( 'Menu item added' ),
				'itemDeleted'       => __( 'Menu item deleted' ),
				'menuAdded'         => __( 'Menu created' ),
				'menuDeleted'       => __( 'Menu deleted' ),
				'movedUp'           => __( 'Menu item moved up' ),
				'movedDown'         => __( 'Menu item moved down' ),
				'movedLeft'         => __( 'Menu item moved out of submenu' ),
				'movedRight'        => __( 'Menu item is now a sub-item' ),
				/* translators: &#9656; is the unicode right-pointing triangle, and %s is the section title in the Customizer */
				'customizingMenus'  => sprintf( __( 'Customizing &#9656; %s' ), esc_html( $this->manager->get_panel( 'nav_menus' )->title ) ),
				/* translators: %s: title of menu item which is invalid */
				'invalidTitleTpl'   => __( '%s (Invalid)' ),
				/* translators: %s: title of menu item in draft status */
				'pendingTitleTpl'   => __( '%s (Pending)' ),
				'itemsFound'        => __( 'Number of items found: %d' ),
				'itemsFoundMore'    => __( 'Additional items found: %d' ),
				'itemsLoadingMore'  => __( 'Loading more results... please wait.' ),
				'reorderModeOn'     => __( 'Reorder mode enabled' ),
				'reorderModeOff'    => __( 'Reorder mode closed' ),
				'reorderLabelOn'    => esc_attr__( 'Reorder menu items' ),
				'reorderLabelOff'   => esc_attr__( 'Close reorder mode' ),
			),
			'settingTransport'     => 'postMessage',
			'phpIntMax'            => PHP_INT_MAX,
			'defaultSettingValues' => array(
				'nav_menu'      => $temp_nav_menu_setting->default,
				'nav_menu_item' => $temp_nav_menu_item_setting->default,
			),
			'locationSlugMappedToName' => get_registered_nav_menus(),
		);

		$data = sprintf( 'var _wpCustomizeNavMenusSettings = %s;', wp_json_encode( $settings ) );
		wp_scripts()->add_data( 'customize-nav-menus', 'data', $data );

		// This is copied from nav-menus.php, and it has an unfortunate object name of `menus`.
		$nav_menus_l10n = array(
			'oneThemeLocationNoMenus' => null,
			'moveUp'       => __( 'Move up one' ),
			'moveDown'     => __( 'Move down one' ),
			'moveToTop'    => __( 'Move to the top' ),
			/* translators: %s: previous item name */
			'moveUnder'    => __( 'Move under %s' ),
			/* translators: %s: previous item name */
			'moveOutFrom'  => __( 'Move out from under %s' ),
			/* translators: %s: previous item name */
			'under'        => __( 'Under %s' ),
			/* translators: %s: previous item name */
			'outFrom'      => __( 'Out from under %s' ),
			/* translators: 1: item name, 2: item position, 3: total number of items */
			'menuFocus'    => __( '%1$s. Menu item %2$d of %3$d.' ),
			/* translators: 1: item name, 2: item position, 3: parent item name */
			'subMenuFocus' => __( '%1$s. Sub item number %2$d under %3$s.' ),
		);
		wp_localize_script( 'nav-menu', 'menus', $nav_menus_l10n );
	}

	/**
	 * Filters a dynamic setting's constructor args.
	 *
	 * For a dynamic setting to be registered, this filter must be employed
	 * to override the default false value with an array of args to pass to
	 * the WP_Customize_Setting constructor.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @param false|array $setting_args The arguments to the WP_Customize_Setting constructor.
	 * @param string      $setting_id   ID for dynamic setting, usually coming from `$_POST['customized']`.
	 * @return array|false
	 */
	public function filter_dynamic_setting_args( $setting_args, $setting_id ) {
		if ( preg_match( WP_Customize_Nav_Menu_Setting::ID_PATTERN, $setting_id ) ) {
			$setting_args = array(
				'type'      => WP_Customize_Nav_Menu_Setting::TYPE,
				'transport' => 'postMessage',
			);
		} elseif ( preg_match( WP_Customize_Nav_Menu_Item_Setting::ID_PATTERN, $setting_id ) ) {
			$setting_args = array(
				'type'      => WP_Customize_Nav_Menu_Item_Setting::TYPE,
				'transport' => 'postMessage',
			);
		}
		return $setting_args;
	}

	/**
	 * Allow non-statically created settings to be constructed with custom WP_Customize_Setting subclass.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @param string $setting_class WP_Customize_Setting or a subclass.
	 * @param string $setting_id    ID for dynamic setting, usually coming from `$_POST['customized']`.
	 * @param array  $setting_args  WP_Customize_Setting or a subclass.
	 * @return string
	 */
	public function filter_dynamic_setting_class( $setting_class, $setting_id, $setting_args ) {
		unset( $setting_id );

		if ( ! empty( $setting_args['type'] ) && WP_Customize_Nav_Menu_Setting::TYPE === $setting_args['type'] ) {
			$setting_class = 'WP_Customize_Nav_Menu_Setting';
		} elseif ( ! empty( $setting_args['type'] ) && WP_Customize_Nav_Menu_Item_Setting::TYPE === $setting_args['type'] ) {
			$setting_class = 'WP_Customize_Nav_Menu_Item_Setting';
		}
		return $setting_class;
	}

	/**
	 * Add the customizer settings and controls.
	 *
	 * @since 4.3.0
	 * @access public
	 */
	public function customize_register() {

		// Preview settings for nav menus early so that the sections and controls will be added properly.
		$nav_menus_setting_ids = array();
		foreach ( array_keys( $this->manager->unsanitized_post_values() ) as $setting_id ) {
			if ( preg_match( '/^(nav_menu_locations|nav_menu|nav_menu_item)\[/', $setting_id ) ) {
				$nav_menus_setting_ids[] = $setting_id;
			}
		}
		$this->manager->add_dynamic_settings( $nav_menus_setting_ids );
		if ( ! $this->manager->doing_ajax( 'customize_save' ) ) {
			foreach ( $nav_menus_setting_ids as $setting_id ) {
				$setting = $this->manager->get_setting( $setting_id );
				if ( $setting ) {
					$setting->preview();
				}
			}
		}

		// Require JS-rendered control types.
		$this->manager->register_panel_type( 'WP_Customize_Nav_Menus_Panel' );
		$this->manager->register_control_type( 'WP_Customize_Nav_Menu_Control' );
		$this->manager->register_control_type( 'WP_Customize_Nav_Menu_Name_Control' );
		$this->manager->register_control_type( 'WP_Customize_Nav_Menu_Auto_Add_Control' );
		$this->manager->register_control_type( 'WP_Customize_Nav_Menu_Item_Control' );

		// Create a panel for Menus.
		$description = '<p>' . __( 'This panel is used for managing navigation menus for content you have already published on your site. You can create menus and add items for existing content such as pages, posts, categories, tags, formats, or custom links.' ) . '</p>';
		if ( current_theme_supports( 'widgets' ) ) {
			/* translators: URL to the widgets panel of the customizer */
			$description .= '<p>' . sprintf( __( 'Menus can be displayed in locations defined by your theme or in <a href="%s">widget areas</a> by adding a &#8220;Custom Menu&#8221; widget.' ), "javascript:wp.customize.panel( 'widgets' ).focus();" ) . '</p>';
		} else {
			$description .= '<p>' . __( 'Menus can be displayed in locations defined by your theme.' ) . '</p>';
		}
		$this->manager->add_panel( new WP_Customize_Nav_Menus_Panel( $this->manager, 'nav_menus', array(
			'title'       => __( 'Menus' ),
			'description' => $description,
			'priority'    => 100,
			// 'theme_supports' => 'menus|widgets', @todo allow multiple theme supports
		) ) );
		$menus = wp_get_nav_menus();

		// Menu locations.
		$locations     = get_registered_nav_menus();
		$num_locations = count( array_keys( $locations ) );
		if ( 1 == $num_locations ) {
			$description = '<p>' . __( 'Your theme supports one menu. Select which menu you would like to use.' ) . '</p>';
		} else {
			/* translators: %s: number of menu locations */
			$description = '<p>' . sprintf( _n( 'Your theme supports %s menu. Select which menu appears in each location.', 'Your theme supports %s menus. Select which menu appears in each location.', $num_locations ), number_format_i18n( $num_locations ) ) . '</p>';
		}
		if ( current_theme_supports( 'widgets' ) ) {
			/* translators: URL to the widgets panel of the customizer */
			$description .= '<p>' . sprintf( __( 'You can also place menus in <a href="%s">widget areas</a> with the &#8220;Custom Menu&#8221; widget.' ), "javascript:wp.customize.panel( 'widgets' ).focus();" ) . '</p>';
		}

		$this->manager->add_section( 'menu_locations', array(
			'title'       => __( 'Menu Locations' ),
			'panel'       => 'nav_menus',
			'priority'    => 5,
			'description' => $description,
		) );

		$choices = array( '0' => __( '&mdash; Select &mdash;' ) );
		foreach ( $menus as $menu ) {
			$choices[ $menu->term_id ] = wp_html_excerpt( $menu->name, 40, '&hellip;' );
		}

		foreach ( $locations as $location => $description ) {
			$setting_id = "nav_menu_locations[{$location}]";

			$setting = $this->manager->get_setting( $setting_id );
			if ( $setting ) {
				$setting->transport = 'postMessage';
				remove_filter( "customize_sanitize_{$setting_id}", 'absint' );
				add_filter( "customize_sanitize_{$setting_id}", array( $this, 'intval_base10' ) );
			} else {
				$this->manager->add_setting( $setting_id, array(
					'sanitize_callback' => array( $this, 'intval_base10' ),
					'theme_supports'    => 'menus',
					'type'              => 'theme_mod',
					'transport'         => 'postMessage',
					'default'           => 0,
				) );
			}

			$this->manager->add_control( new WP_Customize_Nav_Menu_Location_Control( $this->manager, $setting_id, array(
				'label'       => $description,
				'location_id' => $location,
				'section'     => 'menu_locations',
				'choices'     => $choices,
			) ) );
		}

		// Register each menu as a Customizer section, and add each menu item to each menu.
		foreach ( $menus as $menu ) {
			$menu_id = $menu->term_id;

			// Create a section for each menu.
			$section_id = 'nav_menu[' . $menu_id . ']';
			$this->manager->add_section( new WP_Customize_Nav_Menu_Section( $this->manager, $section_id, array(
				'title'     => html_entity_decode( $menu->name, ENT_QUOTES, get_bloginfo( 'charset' ) ),
				'priority'  => 10,
				'panel'     => 'nav_menus',
			) ) );

			$nav_menu_setting_id = 'nav_menu[' . $menu_id . ']';
			$this->manager->add_setting( new WP_Customize_Nav_Menu_Setting( $this->manager, $nav_menu_setting_id, array(
				'transport' => 'postMessage',
			) ) );

			// Add the menu contents.
			$menu_items = (array) wp_get_nav_menu_items( $menu_id );

			foreach ( array_values( $menu_items ) as $i => $item ) {

				// Create a setting for each menu item (which doesn't actually manage data, currently).
				$menu_item_setting_id = 'nav_menu_item[' . $item->ID . ']';

				$value = (array) $item;
				if ( empty( $value['post_title'] ) ) {
					$value['title'] = '';
				}

				$value['nav_menu_term_id'] = $menu_id;
				$this->manager->add_setting( new WP_Customize_Nav_Menu_Item_Setting( $this->manager, $menu_item_setting_id, array(
					'value'     => $value,
					'transport' => 'postMessage',
				) ) );

				// Create a control for each menu item.
				$this->manager->add_control( new WP_Customize_Nav_Menu_Item_Control( $this->manager, $menu_item_setting_id, array(
					'label'    => $item->title,
					'section'  => $section_id,
					'priority' => 10 + $i,
				) ) );
			}

			// Note: other controls inside of this section get added dynamically in JS via the MenuSection.ready() function.
		}

		// Add the add-new-menu section and controls.
		$this->manager->add_section( new WP_Customize_New_Menu_Section( $this->manager, 'add_menu', array(
			'title'    => __( 'Add a Menu' ),
			'panel'    => 'nav_menus',
			'priority' => 999,
		) ) );

		$this->manager->add_control( 'new_menu_name', array(
			'label'       => '',
			'section'     => 'add_menu',
			'type'        => 'text',
			'settings'    => array(),
			'input_attrs' => array(
				'class'       => 'menu-name-field',
				'placeholder' => __( 'New menu name' ),
			),
		) );

		$this->manager->add_control( new WP_Customize_New_Menu_Control( $this->manager, 'create_new_menu', array(
			'section'  => 'add_menu',
			'settings' => array(),
		) ) );

		$this->manager->add_setting( new WP_Customize_Filter_Setting( $this->manager, 'nav_menus_created_posts', array(
			'transport' => 'postMessage',
			'type' => 'option', // To prevent theme prefix in changeset.
			'default' => array(),
			'sanitize_callback' => array( $this, 'sanitize_nav_menus_created_posts' ),
		) ) );
	}

	/**
	 * Get the base10 intval.
	 *
	 * This is used as a setting's sanitize_callback; we can't use just plain
	 * intval because the second argument is not what intval() expects.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @param mixed $value Number to convert.
	 * @return int Integer.
	 */
	public function intval_base10( $value ) {
		return intval( $value, 10 );
	}

	/**
	 * Return an array of all the available item types.
	 *
	 * @since 4.3.0
	 * @since 4.7.0  Each array item now includes a `$type_label` in in addition to `$title`, `$type`, and `$object`.
	 * @access public
	 *
	 * @return array The available menu item types.
	 */
	public function available_item_types() {
		$item_types = array();

		$post_types = get_post_types( array( 'show_in_nav_menus' => true ), 'objects' );
		if ( $post_types ) {
			foreach ( $post_types as $slug => $post_type ) {
				$item_types[] = array(
					'title'  => $post_type->labels->name,
					'type_label' => $post_type->labels->singular_name,
					'type' => 'post_type',
					'object' => $post_type->name,
				);
			}
		}

		$taxonomies = get_taxonomies( array( 'show_in_nav_menus' => true ), 'objects' );
		if ( $taxonomies ) {
			foreach ( $taxonomies as $slug => $taxonomy ) {
				if ( 'post_format' === $taxonomy && ! current_theme_supports( 'post-formats' ) ) {
					continue;
				}
				$item_types[] = array(
					'title' => $taxonomy->labels->name,
					'type_label' => $taxonomy->labels->singular_name,
					'type' => 'taxonomy',
					'object' => $taxonomy->name,
				);
			}
		}

		/**
		 * Filters the available menu item types.
		 *
		 * @since 4.3.0
		 * @since 4.7.0  Each array item now includes a `$type_label` in in addition to `$title`, `$type`, and `$object`.
		 *
		 * @param array $item_types Custom menu item types.
		 */
		$item_types = apply_filters( 'customize_nav_menu_available_item_types', $item_types );

		return $item_types;
	}

	/**
	 * Add a new `auto-draft` post.
	 *
	 * @access public
	 * @since 4.7.0
	 *
	 * @param array $postarr {
	 *     Post array. Note that post_status is overridden to be `auto-draft`.
	 *
	 *     @var string $post_title   Post title. Required.
	 *     @var string $post_type    Post type. Required.
	 *     @var string $post_name    Post name.
	 *     @var string $post_content Post content.
	 * }
	 * @return WP_Post|WP_Error Inserted auto-draft post object or error.
	 */
	public function insert_auto_draft_post( $postarr ) {
		if ( ! isset( $postarr['post_type'] ) || ! post_type_exists( $postarr['post_type'] )  ) {
			return new WP_Error( 'unknown_post_type', __( 'Unknown post type' ) );
		}
		if ( empty( $postarr['post_title'] ) ) {
			return new WP_Error( 'empty_title', __( 'Empty title' ) );
		}
		if ( ! empty( $postarr['post_status'] ) ) {
			return new WP_Error( 'status_forbidden', __( 'Status is forbidden' ) );
		}

		$postarr['post_status'] = 'auto-draft';

		// Auto-drafts are allowed to have empty post_names, so it has to be explicitly set.
		if ( empty( $postarr['post_name'] ) ) {
			$postarr['post_name'] = sanitize_title( $postarr['post_title'] );
		}
		if ( ! isset( $postarr['meta_input'] ) ) {
			$postarr['meta_input'] = array();
		}
		$postarr['meta_input']['_customize_draft_post_name'] = $postarr['post_name'];
		unset( $postarr['post_name'] );

		add_filter( 'wp_insert_post_empty_content', '__return_false', 1000 );
		$r = wp_insert_post( wp_slash( $postarr ), true );
		remove_filter( 'wp_insert_post_empty_content', '__return_false', 1000 );

		if ( is_wp_error( $r ) ) {
			return $r;
		} else {
			return get_post( $r );
		}
	}

	/**
	 * Ajax handler for adding a new auto-draft post.
	 *
	 * @access public
	 * @since 4.7.0
	 */
	public function ajax_insert_auto_draft_post() {
		if ( ! check_ajax_referer( 'customize-menus', 'customize-menus-nonce', false ) ) {
			wp_send_json_error( 'bad_nonce', 400 );
		}

		if ( ! current_user_can( 'customize' ) ) {
			wp_send_json_error( 'customize_not_allowed', 403 );
		}

		if ( empty( $_POST['params'] ) || ! is_array( $_POST['params'] ) ) {
			wp_send_json_error( 'missing_params', 400 );
		}

		$params = wp_unslash( $_POST['params'] );
		$illegal_params = array_diff( array_keys( $params ), array( 'post_type', 'post_title' ) );
		if ( ! empty( $illegal_params ) ) {
			wp_send_json_error( 'illegal_params', 400 );
		}

		$params = array_merge(
			array(
				'post_type' => '',
				'post_title' => '',
			),
			$params
		);

		if ( empty( $params['post_type'] ) || ! post_type_exists( $params['post_type'] ) ) {
			status_header( 400 );
			wp_send_json_error( 'missing_post_type_param' );
		}

		$post_type_object = get_post_type_object( $params['post_type'] );
		if ( ! current_user_can( $post_type_object->cap->create_posts ) || ! current_user_can( $post_type_object->cap->publish_posts ) ) {
			status_header( 403 );
			wp_send_json_error( 'insufficient_post_permissions' );
		}

		$params['post_title'] = trim( $params['post_title'] );
		if ( '' === $params['post_title'] ) {
			status_header( 400 );
			wp_send_json_error( 'missing_post_title' );
		}

		$r = $this->insert_auto_draft_post( $params );
		if ( is_wp_error( $r ) ) {
			$error = $r;
			if ( ! empty( $post_type_object->labels->singular_name ) ) {
				$singular_name = $post_type_object->labels->singular_name;
			} else {
				$singular_name = __( 'Post' );
			}

			$data = array(
				/* translators: %1$s is the post type name and %2$s is the error message. */
				'message' => sprintf( __( '%1$s could not be created: %2$s' ), $singular_name, $error->get_error_message() ),
			);
			wp_send_json_error( $data );
		} else {
			$post = $r;
			$data = array(
				'post_id' => $post->ID,
				'url'     => get_permalink( $post->ID ),
			);
			wp_send_json_success( $data );
		}
	}

	/**
	 * Print the JavaScript templates used to render Menu Customizer components.
	 *
	 * Templates are imported into the JS use wp.template.
	 *
	 * @since 4.3.0
	 * @access public
	 */
	public function print_templates() {
		?>
		<script type="text/html" id="tmpl-available-menu-item">
			<li id="menu-item-tpl-{{ data.id }}" class="menu-item-tpl" data-menu-item-id="{{ data.id }}">
				<div class="menu-item-bar">
					<div class="menu-item-handle">
						<span class="item-type" aria-hidden="true">{{ data.type_label }}</span>
						<span class="item-title" aria-hidden="true">
							<span class="menu-item-title<# if ( ! data.title ) { #> no-title<# } #>">{{ data.title || wp.customize.Menus.data.l10n.untitled }}</span>
						</span>
						<button type="button" class="button-link item-add">
							<span class="screen-reader-text"><?php
								/* translators: 1: Title of a menu item, 2: Type of a menu item */
								printf( __( 'Add to menu: %1$s (%2$s)' ), '{{ data.title || wp.customize.Menus.data.l10n.untitled }}', '{{ data.type_label }}' );
							?></span>
						</button>
					</div>
				</div>
			</li>
		</script>

		<script type="text/html" id="tmpl-menu-item-reorder-nav">
			<div class="menu-item-reorder-nav">
				<?php
				printf(
					'<button type="button" class="menus-move-up">%1$s</button><button type="button" class="menus-move-down">%2$s</button><button type="button" class="menus-move-left">%3$s</button><button type="button" class="menus-move-right">%4$s</button>',
					__( 'Move up' ),
					__( 'Move down' ),
					__( 'Move one level up' ),
					__( 'Move one level down' )
				);
				?>
			</div>
		</script>
	<?php
	}

	/**
	 * Print the html template used to render the add-menu-item frame.
	 *
	 * @since 4.3.0
	 * @access public
	 */
	public function available_items_template() {
		?>
		<div id="available-menu-items" class="accordion-container">
			<div class="customize-section-title">
				<button type="button" class="customize-section-back" tabindex="-1">
					<span class="screen-reader-text"><?php _e( 'Back' ); ?></span>
				</button>
				<h3>
					<span class="customize-action">
						<?php
							/* translators: &#9656; is the unicode right-pointing triangle, and %s is the section title in the Customizer */
							printf( __( 'Customizing &#9656; %s' ), esc_html( $this->manager->get_panel( 'nav_menus' )->title ) );
						?>
					</span>
					<?php _e( 'Add Menu Items' ); ?>
				</h3>
			</div>
			<div id="available-menu-items-search" class="accordion-section cannot-expand">
				<div class="accordion-section-title">
					<label class="screen-reader-text" for="menu-items-search"><?php _e( 'Search Menu Items' ); ?></label>
					<input type="text" id="menu-items-search" placeholder="<?php esc_attr_e( 'Search menu items&hellip;' ) ?>" aria-describedby="menu-items-search-desc" />
					<p class="screen-reader-text" id="menu-items-search-desc"><?php _e( 'The search results will be updated as you type.' ); ?></p>
					<span class="spinner"></span>
				</div>
				<div class="search-icon" aria-hidden="true"></div>
				<button type="button" class="clear-results"><span class="screen-reader-text"><?php _e( 'Clear Results' ); ?></span></button>
				<ul class="accordion-section-content available-menu-items-list" data-type="search"></ul>
			</div>
			<?php

			// Ensure the page post type comes first in the list.
			$item_types = $this->available_item_types();
			$page_item_type = null;
			foreach ( $item_types as $i => $item_type ) {
				if ( isset( $item_type['object'] ) && 'page' === $item_type['object'] ) {
					$page_item_type = $item_type;
					unset( $item_types[ $i ] );
				}
			}

			$this->print_custom_links_available_menu_item();
			if ( $page_item_type ) {
				$this->print_post_type_container( $page_item_type );
			}
			// Containers for per-post-type item browsing; items are added with JS.
			foreach ( $item_types as $item_type ) {
				$this->print_post_type_container( $item_type );
			}
			?>
		</div><!-- #available-menu-items -->
	<?php
	}

	/**
	 * Print the markup for new menu items.
	 *
	 * To be used in the template #available-menu-items.
	 *
	 * @since 4.7.0
	 * @access private
	 *
	 * @param array $available_item_type Menu item data to output, including title, type, and label.
	 * @return void
	 */
	protected function print_post_type_container( $available_item_type ) {
		$id = sprintf( 'available-menu-items-%s-%s', $available_item_type['type'], $available_item_type['object'] );
		?>
		<div id="<?php echo esc_attr( $id ); ?>" class="accordion-section">
			<h4 class="accordion-section-title" role="presentation">
				<?php echo esc_html( $available_item_type['title'] ); ?>
				<span class="spinner"></span>
				<span class="no-items"><?php _e( 'No items' ); ?></span>
				<button type="button" class="button-link" aria-expanded="false">
					<span class="screen-reader-text"><?php
						/* translators: %s: Title of a section with menu items */
						printf( __( 'Toggle section: %s' ), esc_html( $available_item_type['title'] ) ); ?></span>
					<span class="toggle-indicator" aria-hidden="true"></span>
				</button>
			</h4>
			<div class="accordion-section-content">
				<?php if ( 'post_type' === $available_item_type['type'] ) : ?>
					<?php $post_type_obj = get_post_type_object( $available_item_type['object'] ); ?>
					<?php if ( current_user_can( $post_type_obj->cap->create_posts ) && current_user_can( $post_type_obj->cap->publish_posts ) ) : ?>
						<div class="new-content-item">
							<input type="text" class="create-item-input" placeholder="<?php echo esc_attr( $post_type_obj->labels->add_new_item ); ?>">
							<button type="button" class="button add-content"><?php _e( 'Add' ); ?></button>
						</div>
					<?php endif; ?>
				<?php endif; ?>
				<ul class="available-menu-items-list" data-type="<?php echo esc_attr( $available_item_type['type'] ); ?>" data-object="<?php echo esc_attr( $available_item_type['object'] ); ?>" data-type_label="<?php echo esc_attr( isset( $available_item_type['type_label'] ) ? $available_item_type['type_label'] : $available_item_type['type'] ); ?>"></ul>
			</div>
		</div>
		<?php
	}

	/**
	 * Print the markup for available menu item custom links.
	 *
	 * @since 4.7.0
	 * @access private
	 *
	 * @return void
	 */
	protected function print_custom_links_available_menu_item() {
		?>
		<div id="new-custom-menu-item" class="accordion-section">
			<h4 class="accordion-section-title" role="presentation">
				<?php _e( 'Custom Links' ); ?>
				<button type="button" class="button-link" aria-expanded="false">
					<span class="screen-reader-text"><?php _e( 'Toggle section: Custom Links' ); ?></span>
					<span class="toggle-indicator" aria-hidden="true"></span>
				</button>
			</h4>
			<div class="accordion-section-content customlinkdiv">
				<input type="hidden" value="custom" id="custom-menu-item-type" name="menu-item[-1][menu-item-type]" />
				<p id="menu-item-url-wrap" class="wp-clearfix">
					<label class="howto" for="custom-menu-item-url"><?php _e( 'URL' ); ?></label>
					<input id="custom-menu-item-url" name="menu-item[-1][menu-item-url]" type="text" class="code menu-item-textbox" value="http://">
				</p>
				<p id="menu-item-name-wrap" class="wp-clearfix">
					<label class="howto" for="custom-menu-item-name"><?php _e( 'Link Text' ); ?></label>
					<input id="custom-menu-item-name" name="menu-item[-1][menu-item-title]" type="text" class="regular-text menu-item-textbox">
				</p>
				<p class="button-controls">
					<span class="add-to-menu">
						<input type="submit" class="button submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu' ); ?>" name="add-custom-menu-item" id="custom-menu-item-submit">
						<span class="spinner"></span>
					</span>
				</p>
			</div>
		</div>
		<?php
	}

	//
	// Start functionality specific to partial-refresh of menu changes in Customizer preview.
	//

	/**
	 * Nav menu args used for each instance, keyed by the args HMAC.
	 *
	 * @since 4.3.0
	 * @access public
	 * @var array
	 */
	public $preview_nav_menu_instance_args = array();

	/**
	 * Filters arguments for dynamic nav_menu selective refresh partials.
	 *
	 * @since 4.5.0
	 * @access public
	 *
	 * @param array|false $partial_args Partial args.
	 * @param string      $partial_id   Partial ID.
	 * @return array Partial args.
	 */
	public function customize_dynamic_partial_args( $partial_args, $partial_id ) {

		if ( preg_match( '/^nav_menu_instance\[[0-9a-f]{32}\]$/', $partial_id ) ) {
			if ( false === $partial_args ) {
				$partial_args = array();
			}
			$partial_args = array_merge(
				$partial_args,
				array(
					'type'                => 'nav_menu_instance',
					'render_callback'     => array( $this, 'render_nav_menu_partial' ),
					'container_inclusive' => true,
					'settings'            => array(), // Empty because the nav menu instance may relate to a menu or a location.
					'capability'          => 'edit_theme_options',
				)
			);
		}

		return $partial_args;
	}

	/**
	 * Add hooks for the Customizer preview.
	 *
	 * @since 4.3.0
	 * @access public
	 */
	public function customize_preview_init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'customize_preview_enqueue_deps' ) );
		add_filter( 'wp_nav_menu_args', array( $this, 'filter_wp_nav_menu_args' ), 1000 );
		add_filter( 'wp_nav_menu', array( $this, 'filter_wp_nav_menu' ), 10, 2 );
		add_filter( 'wp_footer', array( $this, 'export_preview_data' ), 1 );
		add_filter( 'customize_render_partials_response', array( $this, 'export_partial_rendered_nav_menu_instances' ) );
	}

	/**
	 * Make the auto-draft status protected so that it can be queried.
	 *
	 * @since 4.7.0
	 * @access public
	 */
	public function make_auto_draft_status_previewable() {
		global $wp_post_statuses;
		$wp_post_statuses['auto-draft']->protected = true;
	}

	/**
	 * Sanitize post IDs for auto-draft posts created for nav menu items to be published.
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @param array $value Post IDs.
	 * @returns array Post IDs.
	 */
	public function sanitize_nav_menus_created_posts( $value ) {
		$post_ids = array();
		foreach ( wp_parse_id_list( $value ) as $post_id ) {
			if ( empty( $post_id ) ) {
				continue;
			}
			$post = get_post( $post_id );
			if ( 'auto-draft' !== $post->post_status ) {
				continue;
			}
			$post_type_obj = get_post_type_object( $post->post_type );
			if ( ! $post_type_obj ) {
				continue;
			}
			if ( ! current_user_can( $post_type_obj->cap->publish_posts ) || ! current_user_can( $post_type_obj->cap->edit_post, $post_id ) ) {
				continue;
			}
			$post_ids[] = $post->ID;
		}
		return $post_ids;
	}

	/**
	 * Publish the auto-draft posts that were created for nav menu items.
	 *
	 * The post IDs will have been sanitized by already by
	 * `WP_Customize_Nav_Menu_Items::sanitize_nav_menus_created_posts()` to
	 * remove any post IDs for which the user cannot publish or for which the
	 * post is not an auto-draft.
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @param WP_Customize_Setting $setting Customizer setting object.
	 */
	public function save_nav_menus_created_posts( $setting ) {
		$post_ids = $setting->post_value();
		if ( ! empty( $post_ids ) ) {
			foreach ( $post_ids as $post_id ) {
				$target_status = 'attachment' === get_post_type( $post_id ) ? 'inherit' : 'publish';
				$args = array(
					'ID' => $post_id,
					'post_status' => $target_status,
				);
				$post_name = get_post_meta( $post_id, '_customize_draft_post_name', true );
				if ( $post_name ) {
					$args['post_name'] = $post_name;
				}

				// Note that wp_publish_post() cannot be used because unique slugs need to be assigned.
				wp_update_post( wp_slash( $args ) );

				delete_post_meta( $post_id, '_customize_draft_post_name' );
			}
		}
	}

	/**
	 * Keep track of the arguments that are being passed to wp_nav_menu().
	 *
	 * @since 4.3.0
	 * @access public
	 * @see wp_nav_menu()
	 * @see WP_Customize_Widgets_Partial_Refresh::filter_dynamic_sidebar_params()
	 *
	 * @param array $args An array containing wp_nav_menu() arguments.
	 * @return array Arguments.
	 */
	public function filter_wp_nav_menu_args( $args ) {
		/*
		 * The following conditions determine whether or not this instance of
		 * wp_nav_menu() can use selective refreshed. A wp_nav_menu() can be
		 * selective refreshed if...
		 */
		$can_partial_refresh = (
			// ...if wp_nav_menu() is directly echoing out the menu (and thus isn't manipulating the string after generated),
			! empty( $args['echo'] )
			&&
			// ...and if the fallback_cb can be serialized to JSON, since it will be included in the placement context data,
			( empty( $args['fallback_cb'] ) || is_string( $args['fallback_cb'] ) )
			&&
			// ...and if the walker can also be serialized to JSON, since it will be included in the placement context data as well,
			( empty( $args['walker'] ) || is_string( $args['walker'] ) )
			// ...and if it has a theme location assigned or an assigned menu to display,
			&& (
				! empty( $args['theme_location'] )
				||
				( ! empty( $args['menu'] ) && ( is_numeric( $args['menu'] ) || is_object( $args['menu'] ) ) )
			)
			&&
			// ...and if the nav menu would be rendered with a wrapper container element (upon which to attach data-* attributes).
			(
				! empty( $args['container'] )
				||
				( isset( $args['items_wrap'] ) && '<' === substr( $args['items_wrap'], 0, 1 ) )
			)
		);
		$args['can_partial_refresh'] = $can_partial_refresh;

		$exported_args = $args;

		// Empty out args which may not be JSON-serializable.
		if ( ! $can_partial_refresh ) {
			$exported_args['fallback_cb'] = '';
			$exported_args['walker'] = '';
		}

		/*
		 * Replace object menu arg with a term_id menu arg, as this exports better
		 * to JS and is easier to compare hashes.
		 */
		if ( ! empty( $exported_args['menu'] ) && is_object( $exported_args['menu'] ) ) {
			$exported_args['menu'] = $exported_args['menu']->term_id;
		}

		ksort( $exported_args );
		$exported_args['args_hmac'] = $this->hash_nav_menu_args( $exported_args );

		$args['customize_preview_nav_menus_args'] = $exported_args;
		$this->preview_nav_menu_instance_args[ $exported_args['args_hmac'] ] = $exported_args;
		return $args;
	}

	/**
	 * Prepares wp_nav_menu() calls for partial refresh.
	 *
	 * Injects attributes into container element.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @see wp_nav_menu()
	 *
	 * @param string $nav_menu_content The HTML content for the navigation menu.
	 * @param object $args             An object containing wp_nav_menu() arguments.
	 * @return null
	 */
	public function filter_wp_nav_menu( $nav_menu_content, $args ) {
		if ( isset( $args->customize_preview_nav_menus_args['can_partial_refresh'] ) && $args->customize_preview_nav_menus_args['can_partial_refresh'] ) {
			$attributes = sprintf( ' data-customize-partial-id="%s"', esc_attr( 'nav_menu_instance[' . $args->customize_preview_nav_menus_args['args_hmac'] . ']' ) );
			$attributes .= ' data-customize-partial-type="nav_menu_instance"';
			$attributes .= sprintf( ' data-customize-partial-placement-context="%s"', esc_attr( wp_json_encode( $args->customize_preview_nav_menus_args ) ) );
			$nav_menu_content = preg_replace( '#^(<\w+)#', '$1 ' . $attributes, $nav_menu_content, 1 );
		}
		return $nav_menu_content;
	}

	/**
	 * Hashes (hmac) the nav menu arguments to ensure they are not tampered with when
	 * submitted in the Ajax request.
	 *
	 * Note that the array is expected to be pre-sorted.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @param array $args The arguments to hash.
	 * @return string Hashed nav menu arguments.
	 */
	public function hash_nav_menu_args( $args ) {
		return wp_hash( serialize( $args ) );
	}

	/**
	 * Enqueue scripts for the Customizer preview.
	 *
	 * @since 4.3.0
	 * @access public
	 */
	public function customize_preview_enqueue_deps() {
		wp_enqueue_script( 'customize-preview-nav-menus' ); // Note that we have overridden this.
		wp_enqueue_style( 'customize-preview' );
	}

	/**
	 * Exports data from PHP to JS.
	 *
	 * @since 4.3.0
	 * @access public
	 */
	public function export_preview_data() {

		// Why not wp_localize_script? Because we're not localizing, and it forces values into strings.
		$exports = array(
			'navMenuInstanceArgs' => $this->preview_nav_menu_instance_args,
		);
		printf( '<script>var _wpCustomizePreviewNavMenusExports = %s;</script>', wp_json_encode( $exports ) );
	}

	/**
	 * Export any wp_nav_menu() calls during the rendering of any partials.
	 *
	 * @since 4.5.0
	 * @access public
	 *
	 * @param array $response Response.
	 * @return array Response.
	 */
	public function export_partial_rendered_nav_menu_instances( $response ) {
		$response['nav_menu_instance_args'] = $this->preview_nav_menu_instance_args;
		return $response;
	}

	/**
	 * Render a specific menu via wp_nav_menu() using the supplied arguments.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @see wp_nav_menu()
	 *
	 * @param WP_Customize_Partial $partial       Partial.
	 * @param array                $nav_menu_args Nav menu args supplied as container context.
	 * @return string|false
	 */
	public function render_nav_menu_partial( $partial, $nav_menu_args ) {
		unset( $partial );

		if ( ! isset( $nav_menu_args['args_hmac'] ) ) {
			// Error: missing_args_hmac.
			return false;
		}

		$nav_menu_args_hmac = $nav_menu_args['args_hmac'];
		unset( $nav_menu_args['args_hmac'] );

		ksort( $nav_menu_args );
		if ( ! hash_equals( $this->hash_nav_menu_args( $nav_menu_args ), $nav_menu_args_hmac ) ) {
			// Error: args_hmac_mismatch.
			return false;
		}

		ob_start();
		wp_nav_menu( $nav_menu_args );
		$content = ob_get_clean();

		return $content;
	}
}
