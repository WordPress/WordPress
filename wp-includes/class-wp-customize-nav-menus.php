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
	 *
	 * @access public
	 * @var WP_Customize_Manager
	 */
	public $manager;

	/**
	 * Previewed Menus.
	 *
	 * @since 4.3.0
	 *
	 * @access public
	 * @var array
	 */
	public $previewed_menus;

	/**
	 * Constructor.
	 *
	 * @since 4.3.0
	 *
	 * @access public
	 * @param object $manager An instance of the WP_Customize_Manager class.
	 */
	public function __construct( $manager ) {
		$this->previewed_menus = array();
		$this->manager         = $manager;

		add_action( 'wp_ajax_load-available-menu-items-customizer', array( $this, 'ajax_load_available_items' ) );
		add_action( 'wp_ajax_search-available-menu-items-customizer', array( $this, 'ajax_search_available_items' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'customize_register', array( $this, 'customize_register' ), 11 ); // Needs to run after core Navigation section is set up.
		add_filter( 'customize_dynamic_setting_args', array( $this, 'filter_dynamic_setting_args' ), 10, 2 );
		add_filter( 'customize_dynamic_setting_class', array( $this, 'filter_dynamic_setting_class' ), 10, 3 );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'print_templates' ) );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'available_items_template' ) );
		add_action( 'customize_preview_init', array( $this, 'customize_preview_init' ) );
	}

	/**
	 * Ajax handler for loading available menu items.
	 *
	 * @since 4.3.0
	 */
	public function ajax_load_available_items() {
		check_ajax_referer( 'customize-menus', 'customize-menus-nonce' );

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Error: invalid user capabilities.' ) ) );
		}
		if ( empty( $_POST['obj_type'] ) || empty( $_POST['type'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Missing obj_type or type param.' ) ) );
		}

		$obj_type = sanitize_key( $_POST['obj_type'] );
		if ( ! in_array( $obj_type, array( 'post_type', 'taxonomy' ) ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid obj_type param: ' . $obj_type ) ) );
		}
		$taxonomy_or_post_type = sanitize_key( $_POST['type'] );
		$page = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 0;
		$items = array();

		if ( 'post_type' === $obj_type ) {
			if ( ! get_post_type_object( $taxonomy_or_post_type ) ) {
				wp_send_json_error( array( 'message' => __( 'Unknown post type.' ) ) );
			}

			if ( 0 === $page && 'page' === $taxonomy_or_post_type ) {
				// Add "Home" link. Treat as a page, but switch to custom on add.
				$items[] = array(
					'id'         => 'home',
					'title'      => _x( 'Home', 'nav menu home label' ),
					'type'       => 'custom',
					'type_label' => __( 'Custom Link' ),
					'object'     => '',
					'url'        => home_url(),
				);
			}

			$posts = get_posts( array(
				'numberposts' => 10,
				'offset'      => 10 * $page,
				'orderby'     => 'date',
				'order'       => 'DESC',
				'post_type'   => $taxonomy_or_post_type,
			) );
			foreach ( $posts as $post ) {
				$items[] = array(
					'id'         => "post-{$post->ID}",
					'title'      => html_entity_decode( get_the_title( $post ), ENT_QUOTES, get_bloginfo( 'charset' ) ),
					'type'       => 'post_type',
					'type_label' => get_post_type_object( $post->post_type )->labels->singular_name,
					'object'     => $post->post_type,
					'object_id'  => (int) $post->ID,
				);
			}
		} elseif ( 'taxonomy' === $obj_type ) {
			$terms = get_terms( $taxonomy_or_post_type, array(
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
				wp_send_json_error( array( 'message' => wp_strip_all_tags( $terms->get_error_message(), true ) ) );
			}

			foreach ( $terms as $term ) {
				$items[] = array(
					'id'         => "term-{$term->term_id}",
					'title'      => html_entity_decode( $term->name, ENT_QUOTES, get_bloginfo( 'charset' ) ),
					'type'       => 'taxonomy',
					'type_label' => get_taxonomy( $term->taxonomy )->labels->singular_name,
					'object'     => $term->taxonomy,
					'object_id'  => $term->term_id,
				);
			}
		}

		wp_send_json_success( array( 'items' => $items ) );
	}

	/**
	 * Ajax handler for searching available menu items.
	 *
	 * @since 4.3.0
	 */
	public function ajax_search_available_items() {
		check_ajax_referer( 'customize-menus', 'customize-menus-nonce' );

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Error: invalid user capabilities.' ) ) );
		}
		if ( empty( $_POST['search'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Error: missing search parameter.' ) ) );
		}

		$p = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 0;
		if ( $p < 1 ) {
			$p = 1;
		}

		$s = sanitize_text_field( wp_unslash( $_POST['search'] ) );
		$results = $this->search_available_items_query( array( 'pagenum' => $p, 's' => $s ) );

		if ( empty( $results ) ) {
			wp_send_json_error( array( 'message' => __( 'No results found.' ) ) );
		} else {
			wp_send_json_success( array( 'items' => $results ) );
		}
	}

	/**
	 * Performs post queries for available-item searching.
	 *
	 * Based on WP_Editor::wp_link_query().
	 *
	 * @since 4.3.0
	 *
	 * @param array $args Optional. Accepts 'pagenum' and 's' (search) arguments.
	 * @return array Results.
	 */
	public function search_available_items_query( $args = array() ) {
		$results = array();

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

		// Query posts.
		$get_posts = new WP_Query( $query );

		// Check if any posts were found.
		if ( $get_posts->post_count ) {
			foreach ( $get_posts->posts as $post ) {
				$results[] = array(
					'id'         => 'post-' . $post->ID,
					'type'       => 'post_type',
					'type_label' => $post_type_objects[ $post->post_type ]->labels->singular_name,
					'object'     => $post->post_type,
					'object_id'  => intval( $post->ID ),
					'title'      => html_entity_decode( get_the_title( $post ), ENT_QUOTES, get_bloginfo( 'charset' ) ),
				);
			}
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
				$results[] = array(
					'id'         => 'term-' . $term->term_id,
					'type'       => 'taxonomy',
					'type_label' => get_taxonomy( $term->taxonomy )->labels->singular_name,
					'object'     => $term->taxonomy,
					'object_id'  => intval( $term->term_id ),
					'title'      => html_entity_decode( $term->name, ENT_QUOTES, get_bloginfo( 'charset' ) ),
				);
			}
		}

		return $results;
	}

	/**
	 * Enqueue scripts and styles for Customizer pane.
	 *
	 * @since 4.3.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'customize-nav-menus' );
		wp_enqueue_script( 'customize-nav-menus' );

		$temp_nav_menu_setting      = new WP_Customize_Nav_Menu_Setting( $this->manager, 'nav_menu[-1]' );
		$temp_nav_menu_item_setting = new WP_Customize_Nav_Menu_Item_Setting( $this->manager, 'nav_menu_item[-1]' );

		// Pass data to JS.
		$settings = array(
			'nonce'                => wp_create_nonce( 'customize-menus' ),
			'allMenus'             => wp_get_nav_menus(),
			'itemTypes'            => $this->available_item_types(),
			'l10n'                 => array(
				'untitled'          => _x( '(no label)', 'Missing menu item navigation label.' ),
				'custom_label'      => __( 'Custom Link' ),
				/* translators: %s: Current menu location */
				'menuLocation'      => __( '(Currently set to: %s)' ),
				'deleteWarn'        => __( 'You are about to permanently delete this menu. "Cancel" to stop, "OK" to delete.' ),
				'itemAdded'         => __( 'Menu item added' ),
				'itemDeleted'       => __( 'Menu item deleted' ),
				'menuAdded'         => __( 'Menu created' ),
				'menuDeleted'       => __( 'Menu deleted' ),
				'movedUp'           => __( 'Menu item moved up' ),
				'movedDown'         => __( 'Menu item moved down' ),
				'movedLeft'         => __( 'Menu item moved out of submenu' ),
				'movedRight'        => __( 'Menu item is now a sub-item' ),
				/* translators: %s: &#9656 is the unicode right-pointing triangle */
				'customizingMenus'  => __( 'Customizing &#9656; Menus' ),
				/* translators: %s: title of menu item which is invalid */
				'invalidTitleTpl'   => __( '%s (Invalid)' ),
				/* translators: %s: title of menu item in draft status */
				'pendingTitleTpl'   => __( '%s (Pending)' ),
				'taxonomyTermLabel' => __( 'Taxonomy' ),
				'postTypeLabel'     => __( 'Post Type' ),
			),
			'menuItemTransport'    => 'postMessage',
			'phpIntMax'            => PHP_INT_MAX,
			'defaultSettingValues' => array(
				'nav_menu'      => $temp_nav_menu_setting->default,
				'nav_menu_item' => $temp_nav_menu_item_setting->default,
			),
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
	 * Filter a dynamic setting's constructor args.
	 *
	 * For a dynamic setting to be registered, this filter must be employed
	 * to override the default false value with an array of args to pass to
	 * the WP_Customize_Setting constructor.
	 *
	 * @since 4.3.0
	 *
	 * @param false|array $setting_args The arguments to the WP_Customize_Setting constructor.
	 * @param string      $setting_id   ID for dynamic setting, usually coming from `$_POST['customized']`.
	 * @return array|false
	 */
	public function filter_dynamic_setting_args( $setting_args, $setting_id ) {
		if ( preg_match( WP_Customize_Nav_Menu_Setting::ID_PATTERN, $setting_id ) ) {
			$setting_args = array(
				'type' => WP_Customize_Nav_Menu_Setting::TYPE,
			);
		} elseif ( preg_match( WP_Customize_Nav_Menu_Item_Setting::ID_PATTERN, $setting_id ) ) {
			$setting_args = array(
				'type' => WP_Customize_Nav_Menu_Item_Setting::TYPE,
			);
		}
		return $setting_args;
	}

	/**
	 * Allow non-statically created settings to be constructed with custom WP_Customize_Setting subclass.
	 *
	 * @since 4.3.0
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
	 */
	public function customize_register() {

		// Require JS-rendered control types.
		$this->manager->register_panel_type( 'WP_Customize_Nav_Menus_Panel' );
		$this->manager->register_control_type( 'WP_Customize_Nav_Menu_Control' );
		$this->manager->register_control_type( 'WP_Customize_Nav_Menu_Name_Control' );
		$this->manager->register_control_type( 'WP_Customize_Nav_Menu_Item_Control' );

		// Create a panel for Menus.
		$this->manager->add_panel( new WP_Customize_Nav_Menus_Panel( $this->manager, 'nav_menus', array(
			'title'       => __( 'Menus' ),
			'description' => '<p>' . __( 'This panel is used for managing navigation menus for content you have already published on your site. You can create menus and add items for existing content such as pages, posts, categories, tags, formats, or custom links.' ) . '</p><p>' . __( 'Menus can be displayed in locations defined by your theme or in widget areas by adding a "Custom Menu" widget.' ) . '</p>',
			'priority'    => 100,
			// 'theme_supports' => 'menus|widgets', @todo allow multiple theme supports
		) ) );
		$menus = wp_get_nav_menus();

		// Menu loactions.
		$locations     = get_registered_nav_menus();
		$num_locations = count( array_keys( $locations ) );
		$description   = '<p>' . sprintf( _n( 'Your theme contains %s menu location. Select which menu you would like to use.', 'Your theme contains %s menu locations. Select which menu appears in each location.', $num_locations ), number_format_i18n( $num_locations ) );
		$description  .= '</p><p>' . __( 'You can also place menus in widget areas with the Custom Menu widget.' ) . '</p>';

		$this->manager->add_section( 'menu_locations', array(
			'title'       => __( 'Menu Locations' ),
			'panel'       => 'nav_menus',
			'priority'    => 5,
			'description' => $description,
		) );

		// @todo if ( ! $menus ) : make a "default" menu
		if ( $menus ) {
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
					) );
				}

				$this->manager->add_control( new WP_Customize_Nav_Menu_Location_Control( $this->manager, $setting_id, array(
					'label'       => $description,
					'location_id' => $location,
					'section'     => 'menu_locations',
					'choices'     => $choices,
				) ) );
			}
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
			$this->manager->add_setting( new WP_Customize_Nav_Menu_Setting( $this->manager, $nav_menu_setting_id ) );

			// Add the menu contents.
			$menu_items = (array) wp_get_nav_menu_items( $menu_id );

			foreach ( array_values( $menu_items ) as $i => $item ) {

				// Create a setting for each menu item (which doesn't actually manage data, currently).
				$menu_item_setting_id = 'nav_menu_item[' . $item->ID . ']';
				$this->manager->add_setting( new WP_Customize_Nav_Menu_Item_Setting( $this->manager, $menu_item_setting_id ) );

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

		$this->manager->add_setting( 'new_menu_name', array(
			'type'      => 'new_menu',
			'default'   => '',
			'transport' => 'postMessage',
		) );

		$this->manager->add_control( 'new_menu_name', array(
			'label'       => '',
			'section'     => 'add_menu',
			'type'        => 'text',
			'input_attrs' => array(
				'class'       => 'menu-name-field',
				'placeholder' => __( 'New menu name' ),
			),
		) );

		$this->manager->add_setting( 'create_new_menu', array(
			'type' => 'new_menu',
		) );

		$this->manager->add_control( new WP_New_Menu_Customize_Control( $this->manager, 'create_new_menu', array(
			'section' => 'add_menu',
		) ) );
	}

	/**
	 * Get the base10 intval.
	 *
	 * This is used as a setting's sanitize_callback; we can't use just plain
	 * intval because the second argument is not what intval() expects.
	 *
	 * @since 4.3.0
	 *
	 * @param mixed $value Number to convert.
	 *
	 * @return int
	 */
	public function intval_base10( $value ) {
		return intval( $value, 10 );
	}

	/**
	 * Return an array of all the available item types.
	 *
	 * @since 4.3.0
	 */
	public function available_item_types() {
		$items = array(
			'postTypes'  => array(),
			'taxonomies' => array(),
		);

		$post_types = get_post_types( array( 'show_in_nav_menus' => true ), 'objects' );
		foreach ( $post_types as $slug => $post_type ) {
			$items['postTypes'][ $slug ] = array(
				'label' => $post_type->labels->singular_name,
			);
		}

		$taxonomies = get_taxonomies( array( 'show_in_nav_menus' => true ), 'objects' );
		foreach ( $taxonomies as $slug => $taxonomy ) {
			if ( 'post_format' === $taxonomy && ! current_theme_supports( 'post-formats' ) ) {
				continue;
			}
			$items['taxonomies'][ $slug ] = array(
				'label' => $taxonomy->labels->singular_name,
			);
		}
		return $items;
	}

	/**
	 * Print the JavaScript templates used to render Menu Customizer components.
	 *
	 * Templates are imported into the JS use wp.template.
	 *
	 * @since 4.3.0
	 */
	public function print_templates() {
		?>
		<script type="text/html" id="tmpl-available-menu-item">
			<div id="menu-item-tpl-{{ data.id }}" class="menu-item-tpl" data-menu-item-id="{{ data.id }}">
				<dl class="menu-item-bar">
					<dt class="menu-item-handle">
						<span class="item-type">{{ data.type_label }}</span>
						<span class="item-title">{{ data.title || wp.customize.Menus.data.l10n.untitled }}</span>
						<button type="button" class="not-a-button item-add"><span class="screen-reader-text"><?php _e( 'Add Menu Item' ) ?></span></button>
					</dt>
				</dl>
			</div>
		</script>

		<script type="text/html" id="tmpl-available-menu-item-type">
			<div id="available-menu-items-{{ data.type }}" class="accordion-section">
				<h4 class="accordion-section-title">{{ data.type_label }}</h4>
				<div class="accordion-section-content">
				</div>
			</div>
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
					<input type="text" id="menu-items-search" placeholder="<?php esc_attr_e( 'Search menu items&hellip;' ) ?>" />
					<span class="spinner"></span>
				</div>
				<div class="accordion-section-content" data-type="search"></div>
			</div>
			<div id="new-custom-menu-item" class="accordion-section">
				<h4 class="accordion-section-title"><?php _e( 'Custom Links' ); ?><button type="button" class="not-a-button"><span class="screen-reader-text"><?php _e( 'Toggle' ); ?></span></button></h4>
				<div class="accordion-section-content">
					<input type="hidden" value="custom" id="custom-menu-item-type" name="menu-item[-1][menu-item-type]" />
					<p id="menu-item-url-wrap">
						<label class="howto" for="custom-menu-item-url">
							<span><?php _e( 'URL' ); ?></span>
							<input id="custom-menu-item-url" name="menu-item[-1][menu-item-url]" type="text" class="code menu-item-textbox" value="http://">
						</label>
					</p>
					<p id="menu-item-name-wrap">
						<label class="howto" for="custom-menu-item-name">
							<span><?php _e( 'Link Text' ); ?></span>
							<input id="custom-menu-item-name" name="menu-item[-1][menu-item-title]" type="text" class="regular-text menu-item-textbox">
						</label>
					</p>
					<p class="button-controls">
						<span class="add-to-menu">
							<input type="submit" class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu' ); ?>" name="add-custom-menu-item" id="custom-menu-item-submit">
							<span class="spinner"></span>
						</span>
					</p>
				</div>
			</div>
			<?php

			// @todo: consider using add_meta_box/do_accordion_section and making screen-optional?
			// Containers for per-post-type item browsing; items added with JS.
			$post_types = get_post_types( array( 'show_in_nav_menus' => true ), 'object' );
			if ( $post_types ) :
				foreach ( $post_types as $type ) :
					?>
					<div id="available-menu-items-<?php echo esc_attr( $type->name ); ?>" class="accordion-section">
						<h4 class="accordion-section-title"><?php echo esc_html( $type->label ); ?> <span class="spinner"></span> <button type="button" class="not-a-button"><span class="screen-reader-text"><?php _e( 'Toggle' ); ?></span></button></h4>
						<div class="accordion-section-content" data-type="<?php echo esc_attr( $type->name ); ?>" data-obj_type="post_type"></div>
					</div>
				<?php
				endforeach;
			endif;

			$taxonomies = get_taxonomies( array( 'show_in_nav_menus' => true ), 'object' );
			if ( $taxonomies ) :
				foreach ( $taxonomies as $tax ) :
					?>
					<div id="available-menu-items-<?php echo esc_attr( $tax->name ); ?>" class="accordion-section">
						<h4 class="accordion-section-title"><?php echo esc_html( $tax->label ); ?> <span class="spinner"></span> <button type="button" class="not-a-button"><span class="screen-reader-text"><?php _e( 'Toggle' ); ?></span></button></h4>
						<div class="accordion-section-content" data-type="<?php echo esc_attr( $tax->name ); ?>" data-obj_type="taxonomy"></div>
					</div>
				<?php
				endforeach;
			endif;
			?>
		</div><!-- #available-menu-items -->
	<?php
	}

	// Start functionality specific to partial-refresh of menu changes in Customizer preview.
	const RENDER_AJAX_ACTION = 'customize_render_menu_partial';
	const RENDER_NONCE_POST_KEY = 'render-menu-nonce';
	const RENDER_QUERY_VAR = 'wp_customize_menu_render';

	/**
	 * The number of wp_nav_menu() calls which have happened in the preview.
	 *
	 * @since 4.3.0
	 *
	 * @var int
	 */
	public $preview_nav_menu_instance_number = 0;

	/**
	 * Nav menu args used for each instance.
	 *
	 * @since 4.3.0
	 *
	 * @var array
	 */
	public $preview_nav_menu_instance_args = array();

	/**
	 * Add hooks for the Customizer preview.
	 *
	 * @since 4.3.0
	 */
	public function customize_preview_init() {
		add_action( 'template_redirect', array( $this, 'render_menu' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'customize_preview_enqueue_deps' ) );

		if ( ! isset( $_REQUEST[ self::RENDER_QUERY_VAR ] ) ) {
			add_filter( 'wp_nav_menu_args', array( $this, 'filter_wp_nav_menu_args' ), 1000 );
			add_filter( 'wp_nav_menu', array( $this, 'filter_wp_nav_menu' ), 10, 2 );
		}
	}

	/**
	 * Keep track of the arguments that are being passed to wp_nav_menu().
	 *
	 * @since 4.3.0
	 *
	 * @see wp_nav_menu()
	 *
	 * @param array $args  An array containing wp_nav_menu() arguments.
	 * @return array
	 */
	public function filter_wp_nav_menu_args( $args ) {
		$this->preview_nav_menu_instance_number += 1;
		$args['instance_number'] = $this->preview_nav_menu_instance_number;

		$can_partial_refresh = (
			$args['echo']
			&&
			is_string( $args['fallback_cb'] )
			&&
			is_string( $args['walker'] )
		);
		$args['can_partial_refresh'] = $can_partial_refresh;

		if ( ! $can_partial_refresh ) {
			unset( $args['fallback_cb'] );
			unset( $args['walker'] );
		}

		ksort( $args );
		$args['args_hash'] = $this->hash_nav_menu_args( $args );

		$this->preview_nav_menu_instance_args[ $this->preview_nav_menu_instance_number ] = $args;
		return $args;
	}

	/**
	 * Prepare wp_nav_menu() calls for partial refresh. Wraps output in container for refreshing.
	 *
	 * @since 4.3.0
	 *
	 * @see wp_nav_menu()
	 *
	 * @param string $nav_menu_content The HTML content for the navigation menu.
	 * @param object $args             An object containing wp_nav_menu() arguments.
	 * @return null
	 */
	public function filter_wp_nav_menu( $nav_menu_content, $args ) {
		if ( ! empty( $args->can_partial_refresh ) && ! empty( $args->instance_number ) ) {
			$nav_menu_content = sprintf(
				'<div id="partial-refresh-menu-container-%1$d" class="partial-refresh-menu-container" data-instance-number="%1$d">%2$s</div>',
				$args->instance_number,
				$nav_menu_content
			);
		}
		return $nav_menu_content;
	}

	/**
	 * Hash (hmac) the arguments with the nonce and secret auth key to ensure they
	 * are not tampered with when submitted in the Ajax request.
	 *
	 * @since 4.3.0
	 *
	 * @param array $args The arguments to hash.
	 * @return string
	 */
	public function hash_nav_menu_args( $args ) {
		return wp_hash( wp_create_nonce( self::RENDER_AJAX_ACTION ) . serialize( $args ) );
	}

	/**
	 * Enqueue scripts for the Customizer preview.
	 *
	 * @since 4.3.0
	 */
	public function customize_preview_enqueue_deps() {
		wp_enqueue_script( 'customize-preview-nav-menus' );
		wp_enqueue_style( 'customize-preview' );

		add_action( 'wp_print_footer_scripts', array( $this, 'export_preview_data' ) );
	}

	/**
	 * Export data from PHP to JS.
	 *
	 * @since 4.3.0
	 */
	public function export_preview_data() {

		// Why not wp_localize_script? Because we're not localizing, and it forces values into strings.
		$exports = array(
			'renderQueryVar'        => self::RENDER_QUERY_VAR,
			'renderNonceValue'      => wp_create_nonce( self::RENDER_AJAX_ACTION ),
			'renderNoncePostKey'    => self::RENDER_NONCE_POST_KEY,
			'requestUri'            => '/',
			'theme'                 => array(
				'stylesheet' => $this->manager->get_stylesheet(),
				'active'     => $this->manager->is_theme_active(),
			),
			'previewCustomizeNonce' => wp_create_nonce( 'preview-customize_' . $this->manager->get_stylesheet() ),
			'navMenuInstanceArgs'   => $this->preview_nav_menu_instance_args,
		);

		if ( ! empty( $_SERVER['REQUEST_URI'] ) ) {
			$exports['requestUri'] = esc_url_raw( home_url( wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
		}

		printf( '<script>var _wpCustomizePreviewNavMenusExports = %s;</script>', wp_json_encode( $exports ) );
	}

	/**
	 * Render a specific menu via wp_nav_menu() using the supplied arguments.
	 *
	 * @since 4.3.0
	 *
	 * @see wp_nav_menu()
	 */
	public function render_menu() {
		if ( empty( $_POST[ self::RENDER_QUERY_VAR ] ) ) {
			return;
		}

		$this->manager->remove_preview_signature();

		if ( empty( $_POST[ self::RENDER_NONCE_POST_KEY ] ) ) {
			wp_send_json_error( 'missing_nonce_param' );
		}

		if ( ! is_customize_preview() ) {
			wp_send_json_error( 'expected_customize_preview' );
		}

		if ( ! check_ajax_referer( self::RENDER_AJAX_ACTION, self::RENDER_NONCE_POST_KEY, false ) ) {
			wp_send_json_error( 'nonce_check_fail' );
		}

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error( 'unauthorized' );
		}

		if ( ! isset( $_POST['wp_nav_menu_args'] ) ) {
			wp_send_json_error( 'missing_param' );
		}

		if ( ! isset( $_POST['wp_nav_menu_args_hash'] ) ) {
			wp_send_json_error( 'missing_param' );
		}

		$wp_nav_menu_args = json_decode( wp_unslash( $_POST['wp_nav_menu_args'] ), true );
		if ( ! is_array( $wp_nav_menu_args ) ) {
			wp_send_json_error( 'wp_nav_menu_args_not_array' );
		}

		$wp_nav_menu_args_hash = sanitize_text_field( wp_unslash( $_POST['wp_nav_menu_args_hash'] ) );
		if ( ! hash_equals( $this->hash_nav_menu_args( $wp_nav_menu_args ), $wp_nav_menu_args_hash ) ) {
			wp_send_json_error( 'wp_nav_menu_args_hash_mismatch' );
		}

		$wp_nav_menu_args['echo'] = false;
		wp_send_json_success( wp_nav_menu( $wp_nav_menu_args ) );
	}
}
