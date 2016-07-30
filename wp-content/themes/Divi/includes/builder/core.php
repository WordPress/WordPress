<?php

if ( ! function_exists( 'et_builder_should_load_framework' ) ) :
function et_builder_should_load_framework() {
	global $pagenow;

	$is_admin = is_admin();
	$action_hook = $is_admin ? 'wp_loaded' : 'wp';
	$required_admin_pages = array( 'edit.php', 'post.php', 'post-new.php', 'admin.php', 'customize.php', 'edit-tags.php', 'admin-ajax.php', 'export.php' ); // list of admin pages where we need to load builder files
	$specific_filter_pages = array( 'edit.php', 'admin.php', 'edit-tags.php' ); // list of admin pages where we need more specific filtering

	$is_edit_library_page = 'edit.php' === $pagenow && isset( $_GET['post_type'] ) && 'et_pb_layout' === $_GET['post_type'];
	$is_role_editor_page = 'admin.php' === $pagenow && isset( $_GET['page'] ) && apply_filters( 'et_divi_role_editor_page', 'et_divi_role_editor' ) === $_GET['page'];
	$is_import_page = 'admin.php' === $pagenow && isset( $_GET['import'] ) && 'wordpress' === $_GET['import']; // Page Builder files should be loaded on import page as well to register the et_pb_layout post type properly
	$is_edit_layout_category_page = 'edit-tags.php' === $pagenow && isset( $_GET['taxonomy'] ) && 'layout_category' === $_GET['taxonomy'];

	if ( ! $is_admin || ( $is_admin && in_array( $pagenow, $required_admin_pages ) && ( ! in_array( $pagenow, $specific_filter_pages ) || $is_edit_library_page || $is_role_editor_page || $is_edit_layout_category_page || $is_import_page ) ) ) {
		return true;
	} else {
		return false;
	}

}
endif;

function et_builder_register_layouts(){
	$labels = array(
		'name'               => esc_html_x( 'Layouts', 'Layout type general name', 'et_builder' ),
		'singular_name'      => esc_html_x( 'Layout', 'Layout type singular name', 'et_builder' ),
		'add_new'            => esc_html_x( 'Add New', 'Layout item', 'et_builder' ),
		'add_new_item'       => esc_html__( 'Add New Layout', 'et_builder' ),
		'edit_item'          => esc_html__( 'Edit Layout', 'et_builder' ),
		'new_item'           => esc_html__( 'New Layout', 'et_builder' ),
		'all_items'          => esc_html__( 'All Layouts', 'et_builder' ),
		'view_item'          => esc_html__( 'View Layout', 'et_builder' ),
		'search_items'       => esc_html__( 'Search Layouts', 'et_builder' ),
		'not_found'          => esc_html__( 'Nothing found', 'et_builder' ),
		'not_found_in_trash' => esc_html__( 'Nothing found in Trash', 'et_builder' ),
		'parent_item_colon'  => '',
	);

	$args = array(
		'labels'             => $labels,
		'public'             => false,
		'show_ui'            => true,
		'show_in_menu'       => false,
		'publicly_queryable' => false,
		'can_export'         => true,
		'query_var'          => false,
		'has_archive'        => false,
		'capability_type'    => 'post',
		'map_meta_cap'       => true,
		'hierarchical'       => false,
		'supports'           => array( 'title', 'editor', 'revisions' ),
	);

	// Cannot use is_et_pb_preview() because it's too early
	if ( isset( $_GET['et_pb_preview'] ) && ( isset( $_GET['et_pb_preview_nonce'] ) && wp_verify_nonce( $_GET['et_pb_preview_nonce'], 'et_pb_preview_nonce' ) ) ) {
		$args['publicly_queryable'] = true;
	}

	if ( ! defined( 'ET_BUILDER_LAYOUT_POST_TYPE' ) ) {
		define( 'ET_BUILDER_LAYOUT_POST_TYPE', 'et_pb_layout' );
	}

	register_post_type( ET_BUILDER_LAYOUT_POST_TYPE, apply_filters( 'et_pb_layout_args', $args ) );

	$labels = array(
		'name'              => esc_html__( 'Scope', 'et_builder' )
	);

	register_taxonomy( 'scope', array( 'et_pb_layout' ), array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => false,
		'show_admin_column' => false,
		'query_var'         => true,
		'show_in_nav_menus' => false,
	) );

	$labels = array(
		'name'              => esc_html__( 'Layout Type', 'et_builder' )
	);

	register_taxonomy( 'layout_type', array( 'et_pb_layout' ), array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => false,
		'show_admin_column' => true,
		'query_var'         => true,
		'show_in_nav_menus' => false,
	) );

	$labels = array(
		'name'              => esc_html__( 'Module Width', 'et_builder' )
	);

	register_taxonomy( 'module_width', array( 'et_pb_layout' ), array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => false,
		'show_admin_column' => false,
		'query_var'         => true,
		'show_in_nav_menus' => false,
	) );

	$labels = array(
		'name'              => esc_html__( 'Category', 'et_builder' )
	);

	register_taxonomy( 'layout_category', array( 'et_pb_layout' ), array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'show_in_nav_menus' => false,
	) );
}

if ( et_builder_should_load_framework() ) {
	et_builder_register_layouts();
}

function et_pb_video_get_oembed_thumbnail() {
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$video_url = esc_url( $_POST['et_video_url'] );
	if ( false !== wp_oembed_get( $video_url ) ) {
		// Get image thumbnail
		add_filter( 'oembed_dataparse', 'et_pb_video_oembed_data_parse', 10, 3 );
		// Save thumbnail
		$image_src = wp_oembed_get( $video_url );
		// Set back to normal
		remove_filter( 'oembed_dataparse', 'et_pb_video_oembed_data_parse', 10, 3 );
		if ( '' === $image_src ) {
			die( -1 );
		}
		echo esc_url( $image_src );
	} else {
		die( -1 );
	}
	die();
}
add_action( 'wp_ajax_et_pb_video_get_oembed_thumbnail', 'et_pb_video_get_oembed_thumbnail' );

if ( ! function_exists( 'et_pb_video_oembed_data_parse' ) ) :
function et_pb_video_oembed_data_parse( $return, $data, $url ) {
	if ( isset( $data->thumbnail_url ) ) {
		return esc_url( str_replace( array('https://', 'http://'), '//', $data->thumbnail_url ), array('http') );
	} else {
		return false;
	}
}
endif;

function et_pb_add_widget_area(){
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) die(-1);

	if ( ! current_user_can( 'manage_options' ) ) {
		die( -1 );
	}

	$et_pb_widgets = get_theme_mod( 'et_pb_widgets' );

	$number = $et_pb_widgets ? intval( $et_pb_widgets['number'] ) + 1 : 1;

	$et_widget_area_name = sanitize_text_field( $_POST['et_widget_area_name'] );
	$et_pb_widgets['areas']['et_pb_widget_area_' . $number] = $et_widget_area_name;
	$et_pb_widgets['number'] = $number;

	set_theme_mod( 'et_pb_widgets', $et_pb_widgets );

	et_pb_force_regenerate_templates();

	printf( et_get_safe_localization( __( '<strong>%1$s</strong> widget area has been created. You can create more areas, once you finish update the page to see all the areas.', 'et_builder' ) ),
		esc_html( $et_widget_area_name )
	);

	die();
}
add_action( 'wp_ajax_et_pb_add_widget_area', 'et_pb_add_widget_area' );

function et_pb_remove_widget_area(){
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) die(-1);

	if ( ! current_user_can( 'manage_options' ) ) {
		die( -1 );
	}

	$et_pb_widgets = get_theme_mod( 'et_pb_widgets' );

	$et_widget_area_name = sanitize_text_field( $_POST['et_widget_area_name'] );
	unset( $et_pb_widgets['areas'][ $et_widget_area_name ] );

	set_theme_mod( 'et_pb_widgets', $et_pb_widgets );

	et_pb_force_regenerate_templates();

	die( esc_html( $et_widget_area_name ) );
}
add_action( 'wp_ajax_et_pb_remove_widget_area', 'et_pb_remove_widget_area' );

function et_pb_current_user_can_lock() {
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) die( -1 );

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$permission = et_pb_is_allowed( 'lock_module' );
	$permission = json_encode( $permission );

	die( $permission );
}
add_action( 'wp_ajax_et_pb_current_user_can_lock', 'et_pb_current_user_can_lock' );

function et_builder_get_builder_post_types() {
	return apply_filters( 'et_builder_post_types', array(
		'page',
		'project',
		'et_pb_layout',
		'post',
	) );
}

/**
 * Check whether the specified capability allowed for current user
 * @return bool
 */
function et_pb_is_allowed( $capabilities, $role = '' ) {
	$saved_capabilities = et_pb_get_role_settings();
	$role = '' === $role ? et_pb_get_current_user_role() : $role;

	foreach ( (array) $capabilities as $capability ) {
		if ( ! empty( $saved_capabilities[ $role ][ $capability ] ) && 'off' === $saved_capabilities[ $role ][ $capability ] ) {
			return false;
		}
	}

	return true;
}

/**
 * Gets the array of role settings
 * @return string
 */
function et_pb_get_role_settings() {
	global $et_pb_role_settings;

	// if we don't have saved global variable, then get the value from WPDB
	$et_pb_role_settings = isset( $et_pb_role_settings ) ? $et_pb_role_settings : get_option( 'et_pb_role_settings', array() );

	return $et_pb_role_settings;
}

/**
 * Determines the current user role
 * @return string
 */
function et_pb_get_current_user_role() {
	$current_user = wp_get_current_user();
	$user_roles = $current_user->roles;

	$role = ! empty( $user_roles ) ? $user_roles[0] : '';

	return $role;
}

function et_pb_show_all_layouts_built_for_post_type( $post_type ) {
	$similar_post_types = array(
		'post',
		'page',
		'project',
	);

	if ( in_array( $post_type, $similar_post_types ) ) {
		return $similar_post_types;
	}

	return $post_type;
}
add_filter( 'et_pb_show_all_layouts_built_for_post_type', 'et_pb_show_all_layouts_built_for_post_type' );

function et_pb_show_all_layouts() {
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) die(-1);

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	printf( '
		<label for="et_pb_load_layout_replace">
			<input name="et_pb_load_layout_replace" type="checkbox" id="et_pb_load_layout_replace" %2$s/>
			%1$s
		</label>',
		esc_html__( 'Replace the existing content with loaded layout', 'et_builder' ),
		checked( get_theme_mod( 'et_pb_replace_content', 'on' ), 'on', false )
	);

	$post_type = ! empty( $_POST['et_layouts_built_for_post_type'] ) ? sanitize_text_field( $_POST['et_layouts_built_for_post_type'] ) : 'post';
	$layouts_type = ! empty( $_POST['et_load_layouts_type'] ) ? sanitize_text_field( $_POST['et_load_layouts_type'] ) : 'predefined';

	$predefined_operator = 'predefined' === $layouts_type ? 'EXISTS' : 'NOT EXISTS';

	$post_type = apply_filters( 'et_pb_show_all_layouts_built_for_post_type', $post_type, $layouts_type );

	$query_args = array(
		'meta_query'      => array(
			'relation' => 'AND',
			array(
				'key'     => '_et_pb_predefined_layout',
				'value'   => 'on',
				'compare' => $predefined_operator,
			),
			array(
				'key'     => '_et_pb_built_for_post_type',
				'value'   => $post_type,
				'compare' => 'IN',
			),
		),
		'tax_query' => array(
			array(
				'taxonomy' => 'layout_type',
				'field'    => 'slug',
				'terms'    => array( 'section', 'row', 'module', 'fullwidth_section', 'specialty_section', 'fullwidth_module' ),
				'operator' => 'NOT IN',
			),
		),
		'post_type'       => ET_BUILDER_LAYOUT_POST_TYPE,
		'posts_per_page'  => '-1',
		'suppress_filters' => 'predefined' === $layouts_type,
	);

	$query = new WP_Query( $query_args );

	if ( $query->have_posts() ) :

		echo '<ul class="et-pb-all-modules et-pb-load-layouts">';

		while ( $query->have_posts() ) : $query->the_post();

			printf( '<li class="et_pb_text" data-layout_id="%2$s">%1$s<span class="et_pb_layout_buttons"><a href="#" class="button-primary et_pb_layout_button_load">%3$s</a>%4$s</span></li>',
				esc_html( get_the_title() ),
				esc_attr( get_the_ID() ),
				esc_html__( 'Load', 'et_builder' ),
				'predefined' !== $layouts_type ?
					sprintf( '<a href="#" class="button et_pb_layout_button_delete">%1$s</a>',
						esc_html__( 'Delete', 'et_builder' )
					)
					: ''
			);

		endwhile;

		echo '</ul>';
	endif;

	wp_reset_postdata();

	die();
}
add_action( 'wp_ajax_et_pb_show_all_layouts', 'et_pb_show_all_layouts' );

function et_pb_get_saved_templates() {
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) die(-1);

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$templates_data = array();

	$layout_type = ! empty( $_POST['et_layout_type'] ) ? sanitize_text_field( $_POST['et_layout_type'] ) : 'layout';
	$module_width = ! empty( $_POST['et_module_width'] ) && 'module' === $layout_type ? sanitize_text_field( $_POST['et_module_width'] ) : '';
	$additional_condition = '' !== $module_width ?
		array(
				'taxonomy' => 'module_width',
				'field'    => 'slug',
				'terms'    =>  $module_width,
			) : '';
	$is_global = ! empty( $_POST['et_is_global'] ) ? sanitize_text_field( $_POST['et_is_global'] ) : 'false';
	$global_operator = 'global' === $is_global ? 'IN' : 'NOT IN';

	$meta_query = array();
	$specialty_query = ! empty( $_POST['et_specialty_columns'] ) && 'row' === $layout_type ? sanitize_text_field( $_POST['et_specialty_columns'] ) : '0';

	if ( '0' !== $specialty_query ) {
		$columns_val = '3' === $specialty_query ? array( '4_4', '1_2,1_2', '1_3,1_3,1_3' ) : array( '4_4', '1_2,1_2' );
		$meta_query[] = array(
			'key'     => '_et_pb_row_layout',
			'value'   => $columns_val,
			'compare' => 'IN',
		);
	}

	$post_type = ! empty( $_POST['et_post_type'] ) ? sanitize_text_field( $_POST['et_post_type'] ) : 'post';
	$post_type = apply_filters( 'et_pb_show_all_layouts_built_for_post_type', $post_type, $layout_type );
	$meta_query[] = array(
		'key'     => '_et_pb_built_for_post_type',
		'value'   => $post_type,
		'compare' => 'IN',
	);


	$query = new WP_Query( array(
		'tax_query' => array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'layout_type',
				'field'    => 'slug',
				'terms'    =>  $layout_type,
			),
			array(
				'taxonomy' => 'scope',
				'field'    => 'slug',
				'terms'    => array( 'global' ),
				'operator' => $global_operator,
			),
			$additional_condition,
		),
		'post_type'       => ET_BUILDER_LAYOUT_POST_TYPE,
		'posts_per_page'  => '-1',
		'meta_query'      => $meta_query,
	) );

	wp_reset_postdata();

	if ( ! empty ( $query->posts ) ) {
		foreach( $query->posts as $single_post ) {

			if ( 'module' === $layout_type ) {
				$module_type = get_post_meta( $single_post->ID, '_et_pb_module_type', true );
			} else {
				$module_type = '';
			}

			// add only modules allowed for current user
			if ( '' === $module_type || et_pb_is_allowed( $module_type ) ) {
				$categories = wp_get_post_terms( $single_post->ID, 'layout_category' );
				$categories_processed = array();

				if ( ! empty( $categories ) ) {
					foreach( $categories as $category_data ) {
						$categories_processed[] = esc_html( $category_data->slug );
					}
				}

				$templates_data[] = array(
					'ID'          => $single_post->ID,
					'title'       => esc_html( $single_post->post_title ),
					'shortcode'   => $single_post->post_content,
					'is_global'   => $is_global,
					'layout_type' => $layout_type,
					'module_type' => $module_type,
					'categories'  => $categories_processed,
				);
			}
		}
	}
	if ( empty( $templates_data ) ) {
		$templates_data = array( 'error' => esc_html__( 'You have not saved any items to your Divi Library yet. Once an item has been saved to your library, it will appear here for easy use.', 'et_builder' ) );
	}

	$json_templates = json_encode( $templates_data );

	die( $json_templates );
}
add_action( 'wp_ajax_et_pb_get_saved_templates', 'et_pb_get_saved_templates' );

function et_pb_add_template_meta() {
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) die(-1);

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$post_id = ! empty( $_POST['et_post_id'] ) ? sanitize_text_field( $_POST['et_post_id'] ) : '';
	$value = ! empty( $_POST['et_meta_value'] ) ? sanitize_text_field( $_POST['et_meta_value'] ) : '';
	$custom_field = ! empty( $_POST['et_custom_field'] ) ? sanitize_text_field( $_POST['et_custom_field'] ) : '';

	if ( '' !== $post_id ){
		update_post_meta( $post_id, $custom_field, $value );
	}
}
add_action( 'wp_ajax_et_pb_add_template_meta', 'et_pb_add_template_meta' );

if ( ! function_exists( 'et_pb_add_new_layout' ) ) {
	function et_pb_add_new_layout() {
		if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) die( -1 );

		if ( ! current_user_can( 'edit_posts' ) ) {
			die( -1 );
		}

		$fields_data = isset( $_POST['et_layout_options'] ) ? $_POST['et_layout_options'] : '';

		if ( '' === $fields_data ) {
			die();
		}

		$fields_data_json = str_replace( '\\', '',  $fields_data );
		$fields_data_array = json_decode( $fields_data_json, true );
		$processed_data_array = array();

		// prepare array with fields data in convenient format
		if ( ! empty( $fields_data_array ) ) {
			foreach ( $fields_data_array as $index => $field_data ) {
				$processed_data_array[ $field_data['field_id'] ] = $field_data['field_val'];
			}
		}

		$processed_data_array = apply_filters( 'et_pb_new_layout_data_from_form', $processed_data_array, $fields_data_array );

		if ( empty( $processed_data_array ) ) {
			die();
		}

		$args = array(
			'layout_type'          => ! empty( $processed_data_array['new_template_type'] ) ? sanitize_text_field( $processed_data_array['new_template_type'] ) : 'layout',
			'layout_selected_cats' => ! empty( $processed_data_array['selected_cats'] ) ? sanitize_text_field( $processed_data_array['selected_cats'] ) : '',
			'built_for_post_type'  => ! empty( $processed_data_array['et_builder_layout_built_for_post_type'] ) ? sanitize_text_field( $processed_data_array['et_builder_layout_built_for_post_type'] ) : 'page',
			'layout_new_cat'       => ! empty( $processed_data_array['et_pb_new_cat_name'] ) ? sanitize_text_field( $processed_data_array['et_pb_new_cat_name'] ) : '',
			'columns_layout'       => ! empty( $processed_data_array['et_columns_layout'] ) ? sanitize_text_field( $processed_data_array['et_columns_layout'] ) : '0',
			'module_type'          => ! empty( $processed_data_array['et_module_type'] ) ? sanitize_text_field( $processed_data_array['et_module_type'] ) : 'et_pb_unknown',
			'layout_scope'         => ! empty( $processed_data_array['et_pb_template_global'] ) ? sanitize_text_field( $processed_data_array['et_pb_template_global'] ) : 'not_global',
			'module_width'         => 'regular',
			'layout_content'       => ! empty( $processed_data_array['template_shortcode'] ) ? $processed_data_array['template_shortcode'] : '',
			'layout_name'          => ! empty( $processed_data_array['et_pb_new_template_name'] ) ? sanitize_text_field( $processed_data_array['et_pb_new_template_name'] ) : '',
		);

		// construct the initial shortcode for new layout
		switch ( $args['layout_type'] ) {
			case 'row' :
				$args['layout_content'] = '[et_pb_row template_type="row"][/et_pb_row]';
				break;
			case 'section' :
				$args['layout_content'] = '[et_pb_section template_type="section"][et_pb_row][/et_pb_row][/et_pb_section]';
				break;
			case 'module' :
				$args['layout_content'] = sprintf( '[et_pb_module_placeholder selected_tabs="%1$s"]', ! empty( $processed_data_array['selected_tabs'] ) ? $processed_data_array['selected_tabs'] : 'all' );
				break;
			case 'fullwidth_module' :
				$args['layout_content'] = sprintf( '[et_pb_fullwidth_module_placeholder selected_tabs="%1$s"]', ! empty( $processed_data_array['selected_tabs'] ) ? $processed_data_array['selected_tabs'] : 'all' );
				$args['module_width'] = 'fullwidth';
				$args['layout_type'] = 'module';
				break;
			case 'fullwidth_section' :
				$args['layout_content'] = '[et_pb_section template_type="section" fullwidth="on"][/et_pb_section]';
				$args['layout_type'] = 'section';
				break;
			case 'specialty_section' :
				$args['layout_content'] = '[et_pb_section template_type="section" specialty="on" skip_module="true" specialty_placeholder="true"][/et_pb_section]';
				$args['layout_type'] = 'section';
				break;
		}

		$new_layout_meta = et_pb_submit_layout( apply_filters( 'et_pb_new_layout_args', $args ) );
		die( $new_layout_meta );
	}
}
add_action( 'wp_ajax_et_pb_add_new_layout', 'et_pb_add_new_layout' );

if ( ! function_exists( 'et_pb_submit_layout' ) ) {
	function et_pb_submit_layout( $args ) {
		if ( empty( $args ) ) {
			return;
		}

		$layout_cats_processed = array();

		if ( '' !== $args['layout_selected_cats'] ) {
			$layout_cats_array = explode( ',', $args['layout_selected_cats'] );
			$layout_cats_processed = array_map( 'intval', $layout_cats_array );
		}

		$meta = array();

		if ( 'row' === $args['layout_type'] && '0' !== $args['columns_layout'] ) {
			$meta = array_merge( $meta, array( '_et_pb_row_layout' => $args['columns_layout'] ) );
		}

		if ( 'module' === $args['layout_type'] ) {
			$meta = array_merge( $meta, array( '_et_pb_module_type' => $args['module_type'] ) );
		}

		//et_layouts_built_for_post_type
		$meta = array_merge( $meta, array( '_et_pb_built_for_post_type' => $args['built_for_post_type'] ) );

		$tax_input = array(
			'scope'           => $args['layout_scope'],
			'layout_type'     => $args['layout_type'],
			'module_width'    => $args['module_width'],
			'layout_category' => $layout_cats_processed,
		);

		$new_layout_id = et_pb_create_layout( $args['layout_name'], $args['layout_content'], $meta, $tax_input, $args['layout_new_cat'] );
		$new_post_data['post_id'] = $new_layout_id;

		$new_post_data['edit_link'] = htmlspecialchars_decode( get_edit_post_link( $new_layout_id ) );
		$json_post_data = json_encode( $new_post_data );

		return $json_post_data;
	}
}

if ( ! function_exists( 'et_pb_create_layout' ) ) :
function et_pb_create_layout( $name, $content, $meta = array(), $tax_input = array(), $new_category = '' ) {
	$layout = array(
		'post_title'   => sanitize_text_field( $name ),
		'post_content' => $content,
		'post_status'  => 'publish',
		'post_type'    => ET_BUILDER_LAYOUT_POST_TYPE,
	);

	$layout_id = wp_insert_post( $layout );

	if ( !empty( $meta ) ) {
		foreach ( $meta as $meta_key => $meta_value ) {
			add_post_meta( $layout_id, $meta_key, sanitize_text_field( $meta_value ) );
		}
	}
	if ( '' !== $new_category ) {
		$new_term_id = wp_insert_term( $new_category, 'layout_category' );
		$tax_input['layout_category'][] = (int) $new_term_id['term_id'];
	}

	if ( ! empty( $tax_input ) ) {
		foreach( $tax_input as $taxonomy => $terms ) {
			wp_set_post_terms( $layout_id, $terms, $taxonomy );
		}
	}

	return $layout_id;
}
endif;

function et_pb_save_layout() {
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) die( -1 );

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	if ( empty( $_POST['et_layout_name'] ) ) {
		die();
	}

	$args = array(
		'layout_type'          => isset( $_POST['et_layout_type'] ) ? sanitize_text_field( $_POST['et_layout_type'] ) : 'layout',
		'layout_selected_cats' => isset( $_POST['et_layout_cats'] ) ? sanitize_text_field( $_POST['et_layout_cats'] ) : '',
		'built_for_post_type'  => isset( $_POST['et_post_type'] ) ? sanitize_text_field( $_POST['et_post_type'] ) : 'page',
		'layout_new_cat'       => isset( $_POST['et_layout_new_cat'] ) ? sanitize_text_field( $_POST['et_layout_new_cat'] ) : '',
		'columns_layout'       => isset( $_POST['et_columns_layout'] ) ? sanitize_text_field( $_POST['et_columns_layout'] ) : '0',
		'module_type'          => isset( $_POST['et_module_type'] ) ? sanitize_text_field( $_POST['et_module_type'] ) : 'et_pb_unknown',
		'layout_scope'         => isset( $_POST['et_layout_scope'] ) ? sanitize_text_field( $_POST['et_layout_scope'] ) : 'not_global',
		'module_width'         => isset( $_POST['et_module_width'] ) ? sanitize_text_field( $_POST['et_module_width'] ) : 'regular',
		'layout_content'       => isset( $_POST['et_layout_content'] ) ? $_POST['et_layout_content'] : '',
		'layout_name'          => isset( $_POST['et_layout_name'] ) ? sanitize_text_field( $_POST['et_layout_name'] ) : '',
	);

	$new_layout_meta = et_pb_submit_layout( $args );
	die( $new_layout_meta );
}
add_action( 'wp_ajax_et_pb_save_layout', 'et_pb_save_layout' );

function et_pb_get_global_module() {
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) die( -1 );

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$post_id = isset( $_POST['et_global_id'] ) ? $_POST['et_global_id'] : '';

	if ( '' !== $post_id ) {
		$query = new WP_Query( array(
			'p'         => (int) $post_id,
			'post_type' => ET_BUILDER_LAYOUT_POST_TYPE
		) );

		wp_reset_postdata();

		if ( !empty( $query->post ) ) {
			$global_shortcode['shortcode'] = $query->post->post_content;
		}
	}

	if ( empty( $global_shortcode ) ) {
		$global_shortcode['error'] = 'nothing';
	}

	$json_post_data = json_encode( $global_shortcode );

	die( $json_post_data );
}
add_action( 'wp_ajax_et_pb_get_global_module', 'et_pb_get_global_module' );

function et_pb_update_layout() {
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) die( -1 );

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$post_id = isset( $_POST['et_template_post_id'] ) ? $_POST['et_template_post_id'] : '';
	$new_content = isset( $_POST['et_layout_content'] ) ? et_pb_builder_post_content_capability_check( $_POST['et_layout_content'] ) : '';

	if ( '' !== $post_id ) {
		$update = array(
			'ID'           => $post_id,
			'post_content' => $new_content,
		);

		wp_update_post( $update );
	}

	die();
}
add_action( 'wp_ajax_et_pb_update_layout', 'et_pb_update_layout' );

function _et_pb_sanitize_code_module_content_regex( $matches ) {
	$sanitized_content = wp_kses_post( htmlspecialchars_decode( $matches[1] ) );
	$sanitized_shortcode = str_replace( $matches[1], $sanitized_content, $matches[0] );
	return $sanitized_shortcode;
}

function et_pb_builder_post_content_capability_check( $content) {
	if ( ! current_user_can( 'unfiltered_html' ) ) {
		$content = preg_replace_callback('/\[et_pb_code .*\](.*)\[\/et_pb_code\]/mis', '_et_pb_sanitize_code_module_content_regex', $content );
		$content = preg_replace_callback('/\[et_pb_fullwidth_code .*\](.*)\[\/et_pb_fullwidth_code\]/mis', '_et_pb_sanitize_code_module_content_regex', $content );
	}

	return $content;
}
add_filter( 'content_save_pre', 'et_pb_builder_post_content_capability_check' );

function et_pb_load_layout() {
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) die( -1 );

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$layout_id = (int) $_POST['et_layout_id'];

	if ( '' === $layout_id ) die( -1 );

	$replace_content = isset( $_POST['et_replace_content'] ) && 'on' === $_POST['et_replace_content'] ? 'on' : 'off';

	set_theme_mod( 'et_pb_replace_content', $replace_content );

	$layout = get_post( $layout_id );

	if ( $layout )
		echo $layout->post_content;

	die();
}
add_action( 'wp_ajax_et_pb_load_layout', 'et_pb_load_layout' );

function et_pb_delete_layout() {
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_others_posts' ) ) {
		die( -1 );
	}

	$layout_id = (int) $_POST['et_layout_id'];

	if ( '' === $layout_id ) die( -1 );

	wp_delete_post( $layout_id );

	die();
}
add_action( 'wp_ajax_et_pb_delete_layout', 'et_pb_delete_layout' );

function et_pb_get_backbone_templates() {
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$post_type = sanitize_text_field( $_POST['et_post_type'] );
	$start_from = isset( $_POST['et_templates_start_from'] ) ? sanitize_text_field( $_POST['et_templates_start_from'] ) : 0;
	$amount = ET_BUILDER_AJAX_TEMPLATES_AMOUNT;

	// get the portion of templates
	$result = json_encode( ET_Builder_Element::output_templates( $post_type, $start_from, $amount ) );

	die( $result );
}
add_action( 'wp_ajax_et_pb_get_backbone_templates', 'et_pb_get_backbone_templates' );

function et_pb_get_backbone_template() {
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$module_slugs = json_decode( str_replace( '\\', '', sanitize_text_field( $_POST['et_modules_slugs'] ) ) );
	$post_type   = sanitize_text_field( $_POST['et_post_type'] );

	// get the portion of templates for specified slugs
	$result = json_encode( ET_Builder_Element::get_modules_templates( $post_type, $module_slugs->missing_modules_array ) );

	die( $result );
}
add_action( 'wp_ajax_et_pb_get_backbone_template', 'et_pb_get_backbone_template' );

function et_pb_submit_subscribe_form() {
	if ( ! wp_verify_nonce( $_POST['et_frontend_nonce'], 'et_frontend_nonce' ) ) die( json_encode( array( 'error' => esc_html__( 'Configuration error', 'et_builder' ) ) ) );

	$service = sanitize_text_field( $_POST['et_service'] );

	$list_id = sanitize_text_field( $_POST['et_list_id'] );

	$email = sanitize_email( $_POST['et_email'] );

	$firstname = sanitize_text_field( $_POST['et_firstname'] );

	if ( '' === $firstname ) die( json_encode( array( 'error' => esc_html__( 'Please enter first name', 'et_builder' ) ) ) );

	if ( ! is_email( $email ) ) die( json_encode( array( 'error' => esc_html__( 'Incorrect email', 'et_builder' ) ) ) );

	if ( '' == $list_id ) die( json_encode( array( 'error' => esc_html__( 'Configuration error: List is not defined', 'et_builder' ) ) ) );

	$success_message = sprintf(
		'<h2 class="et_pb_subscribed">%s</h2>',
		esc_html__( 'Subscribed - look for the confirmation email!', 'et_builder' )
	);

	switch ( $service ) {
		case 'mailchimp' :
			$lastname = sanitize_text_field( $_POST['et_lastname'] );
			$email = array( 'email' => $email );

			if ( ! class_exists( 'MailChimp_Divi' ) )
				require_once( ET_BUILDER_DIR . 'subscription/mailchimp/mailchimp.php' );

			if ( et_is_builder_plugin_active() ) {
				$mailchimp_api_option = get_option( 'et_pb_builder_options' );
				$mailchimp_api_key = isset( $mailchimp_api_option['newsletter_main_mailchimp_key'] ) ? $mailchimp_api_option['newsletter_main_mailchimp_key'] : '';
			} else {
				$mailchimp_api_key = et_get_option( 'divi_mailchimp_api_key' );
			}

			if ( '' === $mailchimp_api_key ) die( json_encode( array( 'error' => esc_html__( 'Configuration error: api key is not defined', 'et_builder' ) ) ) );
				$mailchimp = new MailChimp_Divi( $mailchimp_api_key );

				$subscribe_args = array(
					'id'         => $list_id,
					'email'      => $email,
					'merge_vars' => array(
						'FNAME'  => $firstname,
						'LNAME'  => $lastname,
					),
				);

				$retval =  $mailchimp->call('lists/subscribe', $subscribe_args );

				if ( 200 !== wp_remote_retrieve_response_code( $retval ) ) {
					if ( '214' === wp_remote_retrieve_header( $retval, 'x-mailchimp-api-error-code' ) ) {
						$mailchimp_message = json_decode( wp_remote_retrieve_body( $retval ), true );
						$error_message = isset( $mailchimp_message['error'] ) ? $mailchimp_message['error'] : wp_remote_retrieve_body( $retval );
						$result = json_encode( array( 'success' => esc_html( $error_message ) ) );
					} else {
						$result = json_encode( array( 'success' => esc_html( wp_remote_retrieve_response_message( $retval ) ) ) );
					}
				} else {
					$result = json_encode( array( 'success' => $success_message ) );
				}

			die( $result );
			break;
		case 'aweber' :
			if ( ! class_exists( 'AWeberAPI' ) ) {
				require_once( ET_BUILDER_DIR . 'subscription/aweber/aweber_api.php' );
			}

			$account = et_pb_get_aweber_account();

			if ( ! $account ) {
				die( json_encode( array( 'error' => esc_html__( 'Aweber: Wrong configuration data', 'et_builder' ) ) ) );
			}

			try {
				$list_url = "/accounts/{$account->id}/lists/{$list_id}";
				$list = $account->loadFromUrl( $list_url );

				$new_subscriber = $list->subscribers->create(
					array(
						'email' => $email,
						'name'  => $firstname,
					)
				);

				die( json_encode( array( 'success' => $success_message ) ) );
			} catch ( Exception $exc ) {
				die( json_encode( array( 'error' => esc_html( $exc->message ) ) ) );
			}

			break;
	}

	die();
}
add_action( 'wp_ajax_et_pb_submit_subscribe_form', 'et_pb_submit_subscribe_form' );
add_action( 'wp_ajax_nopriv_et_pb_submit_subscribe_form', 'et_pb_submit_subscribe_form' );

/*
 * Is Builder plugin active?
 *
 * @return bool  True - if the plugin is active
 */
if ( ! function_exists( 'et_is_builder_plugin_active' ) ) :
function et_is_builder_plugin_active() {
	return (bool) defined( 'ET_BUILDER_PLUGIN_ACTIVE' );
}
endif;

if ( ! function_exists( 'et_pb_get_aweber_account' ) ) :
function et_pb_get_aweber_account() {
	if ( ! class_exists( 'AWeberAPI' ) ) {
		require_once( ET_BUILDER_DIR . 'subscription/aweber/aweber_api.php' );
	}
	if ( et_is_builder_plugin_active() ) {
		$aweber_api_option = get_option( 'et_pb_builder_options' );
		$consumer_key = isset( $aweber_api_option['aweber_consumer_key'] ) ? $aweber_api_option['aweber_consumer_key'] : '';
		$consumer_secret = isset( $aweber_api_option['aweber_consumer_secret'] ) ? $aweber_api_option['aweber_consumer_secret'] : '';
		$access_key = isset( $aweber_api_option['aweber_access_key'] ) ? $aweber_api_option['aweber_access_key'] : '';
		$access_secret = isset( $aweber_api_option['aweber_access_secret'] ) ? $aweber_api_option['aweber_access_secret'] : '';
	} else {
		$consumer_key = et_get_option( 'divi_aweber_consumer_key' );
		$consumer_secret = et_get_option( 'divi_aweber_consumer_secret' );
		$access_key = et_get_option( 'divi_aweber_access_key' );
		$access_secret = et_get_option( 'divi_aweber_access_secret' );
	}

	if ( ! empty( $consumer_key ) && ! empty( $consumer_secret ) && ! empty( $access_key ) && ! empty( $access_secret ) ) {
		try {
			// Aweber requires curl extension to be enabled
			if ( ! function_exists( 'curl_init' ) ) {
				return false;
			}

			$aweber = new AWeberAPI( $consumer_key, $consumer_secret );

			if ( ! $aweber ) {
				return false;
			}

			$account = $aweber->getAccount( $access_key, $access_secret );
		} catch ( Exception $exc ) {
			return false;
		}
	} else {
		return false;
	}

	return $account;
}
endif;

function et_aweber_submit_authorization_code() {
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) {
		die( esc_html__( 'Nonce failed.', 'et_builder' ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		die( -1 );
	}

	$et_authorization_code = sanitize_text_field( $_POST['et_authorization_code'] );

	if ( '' === $et_authorization_code ) {
		die( esc_html__( 'Authorization code is empty.', 'et_builder' ) );
	}

	if ( ! class_exists( 'AWeberAPI' ) ) {
		require_once( ET_BUILDER_DIR . 'subscription/aweber/aweber_api.php' );
	}

	try {
		$auth = AWeberAPI::getDataFromAweberID( $et_authorization_code );

		if ( ! ( is_array( $auth ) && 4 === count( $auth ) ) ) {
			die ( esc_html__( 'Authorization code is invalid. Try regenerating it and paste in the new code.', 'et_builder' ) );
		}

		list( $consumer_key, $consumer_secret, $access_key, $access_secret ) = $auth;

		et_update_option( 'divi_aweber_consumer_key', $consumer_key );
		et_update_option( 'divi_aweber_consumer_secret', $consumer_secret );
		et_update_option( 'divi_aweber_access_key', $access_key );
		et_update_option( 'divi_aweber_access_secret', $access_secret );

		die( 'success' );
	} catch ( AWeberAPIException $exc ) {
		printf(
			'<p>%4$s.</p>
			<ul>
				<li>%5$s: %1$s</li>
				<li>%6$s: %2$s</li>
				<li>%7$s: %3$s</li>
			</ul>',
			esc_html( $exc->type ),
			esc_html( $exc->message ),
			esc_html( $exc->documentation_url ),
			esc_html__( 'Aweber API Exception', 'et_builder' ),
			esc_html__( 'Type', 'et_builder' ),
			esc_html__( 'Message', 'et_builder' ),
			esc_html__( 'Documentation', 'et_builder' )
		);
	}

	die();
}
add_action( 'wp_ajax_et_aweber_submit_authorization_code', 'et_aweber_submit_authorization_code' );

function et_aweber_remove_connection() {
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) {
		die( esc_html__( 'Nonce failed', 'et_builder' ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		die( -1 );
	}

	et_delete_option( 'divi_aweber_consumer_key' );
	et_delete_option( 'divi_aweber_consumer_secret' );
	et_delete_option( 'divi_aweber_access_key' );
	et_delete_option( 'divi_aweber_access_secret' );

	die( 'success' );
}
add_action( 'wp_ajax_et_aweber_remove_connection', 'et_aweber_remove_connection' );

/**
 * Saves the Role Settings into WP database
 * @return void
 */
function et_pb_save_role_settings() {
	if ( ! wp_verify_nonce( $_POST['et_pb_save_roles_nonce'] , 'et_roles_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		die( -1 );
	}

	// handle received data and convert json string to array
	$data_json = str_replace( '\\', '' ,  $_POST['et_pb_options_all'] );
	$data = json_decode( $data_json, true );
	$processed_options = array();

	// convert settings string for each role into array and save it into et_pb_role_settings option
	if ( ! empty( $data ) ) {
		foreach( $data as $role => $settings ) {
			parse_str( $data[ $role ], $processed_options[ $role ] );
		}
	}

	update_option( 'et_pb_role_settings', $processed_options );

	die();
}
add_action( 'wp_ajax_et_pb_save_role_settings', 'et_pb_save_role_settings' );

function et_pb_execute_content_shortcodes() {
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$unprocessed_data = str_replace( '\\', '', $_POST['et_pb_unprocessed_data'] );

	echo do_shortcode( $unprocessed_data );

	die();
}
add_action( 'wp_ajax_et_pb_execute_content_shortcodes', 'et_pb_execute_content_shortcodes' );

if ( ! function_exists( 'et_pb_register_posttypes' ) ) :
function et_pb_register_posttypes() {
	$labels = array(
		'name'               => esc_html__( 'Projects', 'et_builder' ),
		'singular_name'      => esc_html__( 'Project', 'et_builder' ),
		'add_new'            => esc_html__( 'Add New', 'et_builder' ),
		'add_new_item'       => esc_html__( 'Add New Project', 'et_builder' ),
		'edit_item'          => esc_html__( 'Edit Project', 'et_builder' ),
		'new_item'           => esc_html__( 'New Project', 'et_builder' ),
		'all_items'          => esc_html__( 'All Projects', 'et_builder' ),
		'view_item'          => esc_html__( 'View Project', 'et_builder' ),
		'search_items'       => esc_html__( 'Search Projects', 'et_builder' ),
		'not_found'          => esc_html__( 'Nothing found', 'et_builder' ),
		'not_found_in_trash' => esc_html__( 'Nothing found in Trash', 'et_builder' ),
		'parent_item_colon'  => '',
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'can_export'         => true,
		'show_in_nav_menus'  => true,
		'query_var'          => true,
		'has_archive'        => true,
		'rewrite'            => apply_filters( 'et_project_posttype_rewrite_args', array(
			'feeds'      => true,
			'slug'       => 'project',
			'with_front' => false,
		) ),
		'capability_type'    => 'post',
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'author', 'editor', 'thumbnail', 'excerpt', 'comments', 'revisions', 'custom-fields' ),
	);

	register_post_type( 'project', apply_filters( 'et_project_posttype_args', $args ) );

	$labels = array(
		'name'              => esc_html__( 'Project Categories', 'et_builder' ),
		'singular_name'     => esc_html__( 'Project Category', 'et_builder' ),
		'search_items'      => esc_html__( 'Search Categories', 'et_builder' ),
		'all_items'         => esc_html__( 'All Categories', 'et_builder' ),
		'parent_item'       => esc_html__( 'Parent Category', 'et_builder' ),
		'parent_item_colon' => esc_html__( 'Parent Category:', 'et_builder' ),
		'edit_item'         => esc_html__( 'Edit Category', 'et_builder' ),
		'update_item'       => esc_html__( 'Update Category', 'et_builder' ),
		'add_new_item'      => esc_html__( 'Add New Category', 'et_builder' ),
		'new_item_name'     => esc_html__( 'New Category Name', 'et_builder' ),
		'menu_name'         => esc_html__( 'Categories', 'et_builder' ),
	);

	register_taxonomy( 'project_category', array( 'project' ), array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
	) );

	$labels = array(
		'name'              => esc_html__( 'Project Tags', 'et_builder' ),
		'singular_name'     => esc_html__( 'Project Tag', 'et_builder' ),
		'search_items'      => esc_html__( 'Search Tags', 'et_builder' ),
		'all_items'         => esc_html__( 'All Tags', 'et_builder' ),
		'parent_item'       => esc_html__( 'Parent Tag', 'et_builder' ),
		'parent_item_colon' => esc_html__( 'Parent Tag:', 'et_builder' ),
		'edit_item'         => esc_html__( 'Edit Tag', 'et_builder' ),
		'update_item'       => esc_html__( 'Update Tag', 'et_builder' ),
		'add_new_item'      => esc_html__( 'Add New Tag', 'et_builder' ),
		'new_item_name'     => esc_html__( 'New Tag Name', 'et_builder' ),
		'menu_name'         => esc_html__( 'Tags', 'et_builder' ),
	);

	register_taxonomy( 'project_tag', array( 'project' ), array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
	) );
}
endif;

function et_admin_backbone_templates_being_loaded() {
	if ( ! is_admin() ) {
		return false;
	}

	if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		return false;
	}

	if ( ! isset( $_POST['action'] ) || 'et_pb_get_backbone_templates' !== $_POST['action'] ) {
		return false;
	}

	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) {
		return false;
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		return false;
	}

	return true;
}

if ( ! function_exists( 'et_pb_attempt_memory_limit_increase' ) ) :
function et_pb_attempt_memory_limit_increase() {
	if ( ! isset( $_POST['et_admin_load_nonce'] ) || ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		die( -1 );
	}

	if ( et_increase_memory_limit() ) {
		et_update_option( 'set_memory_limit', '1' );

		die( json_encode( array(
			'success' => true,
		) ) );
	} else {
		die( json_encode( array(
			'error' => true,
		) ) );
	}

	die();
}
endif;

add_action( 'wp_ajax_et_pb_increase_memory_limit', 'et_pb_attempt_memory_limit_increase' );

if ( ! function_exists( 'et_reset_memory_limit_increase' ) ) :
function et_reset_memory_limit_increase() {
	if ( ! isset( $_POST['et_builder_reset_memory_limit_nonce'] ) || ! wp_verify_nonce( $_POST['et_builder_reset_memory_limit_nonce'], 'et_builder_reset_memory_limit_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		die( -1 );
	}

	if ( et_get_option( 'set_memory_limit' ) ) {
		et_delete_option( 'set_memory_limit' );
	}

	die( 'success' );
}
endif;

add_action( 'wp_ajax_et_reset_memory_limit_increase', 'et_reset_memory_limit_increase' );

if ( ! function_exists( 'et_builder_get_cache_notification_modal' ) ) :
function et_builder_get_cache_notification_modal() {
	$cache_plugin_message = '';

	if ( false !== ( $cache_plugin = et_pb_detect_cache_plugins() ) ) {
		$cache_plugin_message = sprintf(
			esc_html__( 'You are using the %1$s plugin. We recommend clearing the plugin cache after updating your theme.', 'et_builder' ),
			esc_html( $cache_plugin['name'] )
		);

		$cache_plugin_message = '<p>' . $cache_plugin_message . '</p>';

		$cache_plugin_message .= sprintf(
			'<a href="%1$s" class="et_builder_modal_action_button" target="_blank">%2$s</a>',
			esc_url( admin_url( $cache_plugin['page'] ) ),
			esc_html__( 'Clear Plugin Cache', 'et_builder' )
		);
	}

	$browser_cache_message = '<p>' . esc_html__( 'Builder files may also be cached in your browser. Please clear your browser cache.', 'et_builder' ) . '</p>';

	$browser_cache_message .= sprintf(
		'<a href="http://www.refreshyourcache.com/en/home/" class="et_builder_modal_action_button" target="_blank">%1$s</a>',
		esc_html__( 'Clear Browser Cache', 'et_builder' )
	);

	$output = sprintf(
		'<div class="et_pb_modal_overlay et_modal_on_top et_pb_failure_notification_modal et_pb_new_template_modal">
			<div class="et_pb_prompt_modal">
				<h2>%1$s</h2>

				<div class="et_pb_prompt_modal_inside">
					<p>%2$s</p>
					%4$s
					%5$s
					<p>%6$s</p>
				</div>

				<a href="#"" class="et_pb_prompt_dont_proceed et-pb-modal-close"></a>

				<div class="et_pb_prompt_buttons">
					<br>
					<span class="spinner"></span>
					<a href="#" class="et_pb_reload_builder button-primary et_pb_prompt_proceed">%3$s</a>
				</div>
			</div>
		</div>',
		esc_html__( 'Builder Cache Warning', 'et_builder' ),
		esc_html__( 'The Divi Builder has been updated, however your browser is loading an old cached version of the builder. Loading old files can cause the builder to malfunction.', 'et_builder' ),
		esc_html__( 'Reload The Builder', 'et_builder' ),
		$cache_plugin_message,
		$browser_cache_message,
		esc_html__( 'If you have cleared your plugin cache and browser cache, but still get this warning, then your files may be cached at the DNS or Server level. Contact your host or CDN for assistance.', 'et_builder' )
	);

	return $output;
}
endif;

if ( ! function_exists( 'et_builder_get_failure_notification_modal' ) ) :
function et_builder_get_failure_notification_modal() {
	$warnings = et_builder_get_warnings();

	if ( false === $warnings ) {
		return '';
	}

	$messages = '';
	$i = 1;

	foreach( $warnings as $warning ) {
		$messages .= sprintf(
			'<p><strong>%1$s. </strong>%2$s</p>',
			esc_html( $i ),
			$warning
		);

		$i++;
	}

	$output = sprintf(
		'<div class="et_pb_modal_overlay et_modal_on_top et_pb_failure_notification_modal et_pb_new_template_modal">
			<div class="et_pb_prompt_modal">
				<h2>%1$s</h2>

				<div class="et_pb_prompt_modal_inside">
					<p><strong>Oops, it looks like the Divi Builder failed to load. Performing the following actions may help solve the problem.</strong></p>

					%2$s
				</div>

				<a href="#"" class="et_pb_prompt_dont_proceed et-pb-modal-close"></a>

				<div class="et_pb_prompt_buttons">
					<br>
					<span class="spinner"></span>
					<a href="#" class="et_pb_reload_builder button-primary et_pb_prompt_proceed">%3$s</a>
				</div>
			</div>
		</div>',
		esc_html__( 'Divi Builder Timeout', 'et_builder' ),
		$messages,
		esc_html__( 'Reload The Builder', 'et_builder' )
	);

	return $output;
}
endif;

if ( ! function_exists( 'et_builder_get_warnings' ) ) :
function et_builder_get_warnings() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return false;
	}

	$warnings = array();


	// WP_DEBUG check
	if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {
		$warnings[] = sprintf(
			'%1$s. <a href="https://codex.wordpress.org/Debugging_in_WordPress" class="et_builder_modal_action_button" target="_blank">%2$s</a>',
			esc_html__( 'You have WP_DEBUG enabled. Please disable this setting in wp-config.php', 'et_builder' ),
			esc_html__( 'Disable Debug Mode', 'et_builder' )
		);
	}


	// Plugins check
	$third_party_plugins_active = false;

	$excluded_plugins = array(
		'wordpress-importer/wordpress-importer.php',
		'divi-builder/divi-builder.php',
		'elegant-themes-updater/elegant-themes-updater.php',
		'et-security-patcher/et-security-patcher.php',
	);

	$active_plugins = get_option( 'active_plugins' );

	if ( is_array( $active_plugins ) && ! empty( $active_plugins ) ) {
		foreach ( $active_plugins as $plugin ) {
			if ( in_array( $plugin, $excluded_plugins ) ) {
				continue;
			}

			$third_party_plugins_active = true;

			break;
		}
	}

	if ( $third_party_plugins_active ) {
		$warnings[] = sprintf(
			'%1$s <a href="%3$s" class="et_builder_modal_action_button" target="_blank">%2$s</a>',
			esc_html__( 'You are using third party plugins. Try disabling each plugin to see if one is causing a conflict.', 'et_builder' ),
			esc_html__( 'Manage Your Plugins', 'et_builder' ),
			esc_url( admin_url( 'plugins.php' ) )
		);
	}


	// WordPress update check
	require_once( ABSPATH . 'wp-admin/includes/update.php' );

	$updates = get_core_updates();

	if ( isset( $updates[0]->response ) && 'latest' != $updates[0]->response ) {
		$warnings[] = sprintf(
			'%1$s <a href="%3$s" class="et_builder_modal_action_button" target="_blank">%2$s</a>',
			esc_html__( 'You are using an outdated version of WordPress. Please upgrade.', 'et_builder' ),
			esc_html__( 'Upgrade WordPress', 'et_builder' ),
			esc_url( admin_url( 'update-core.php' ) )
		);
	}


	// Memory check
	global $et_current_memory_limit;

	if ( ! empty( $et_current_memory_limit ) && intval( $et_current_memory_limit ) < 128 ) {
		$class = ' et_builder_increase_memory';

		if ( true === strpos( ini_get( 'disable_functions' ), 'ini_set' ) ) {
			$class = '';
		}

		$warnings[] = sprintf(
			'%1$s. <a href="http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" class="et_builder_modal_action_button%3$s" target="_blank">%2$s</a>',
			esc_html__( 'Please increase your PHP Memory Limit to 128M. You can return the value to default via the Divi Theme Options in the future', 'et_builder' ),
			esc_html__( 'Increase Your Memory Limit Now', 'et_builder' ),
			esc_attr( $class )
		);
	}


	// Version check
	$et_update_themes = get_site_transient( 'et_update_themes' );

	if ( is_object( $et_update_themes ) && isset( $et_update_themes->response ) ) {
		$theme_info = wp_get_theme();

		if ( is_child_theme() ) {
			$theme_info = wp_get_theme( $theme_info->parent_theme );
		}

		$name    = $theme_info->get( 'Name' );
		$version = $theme_info->get( 'Version' );

		if ( isset( $et_update_themes->response[ $name ] ) && isset( $et_update_themes->response[ $name ]['new_version'] ) && version_compare( $version, $et_update_themes->response[ $name ]['new_version'], '<' ) ) {
			$warnings[] = sprintf(
				'%1$s <a href="%3$s" class="et_builder_modal_action_button" target="_blank">%2$s</a>',
				sprintf(
					esc_html__( 'You are using an outdated version of the theme. The latest version is %1$s', 'et_builder' ),
					esc_html( $et_update_themes->response[ $name ]['new_version'] )
				),
				esc_html__( 'Upgrade', 'et_builder' ),
				esc_url( admin_url( 'themes.php' ) )
			);
		}
	}

	if ( empty( $warnings ) ) {
		return false;
	}

	return $warnings;
}
endif;

if ( ! function_exists( 'et_increase_memory_limit' ) ) :
function et_increase_memory_limit() {
	if ( ! is_admin() ) {
		return;
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	if ( true === strpos( ini_get( 'disable_functions' ), 'ini_set' ) ) {
		return false;
	}

	if ( @ini_set( 'memory_limit', '128M' ) ) {
		return true;
	}

	return false;
}
endif;

if ( ! function_exists( 'et_maybe_increase_memory_limit' ) ) :
function et_maybe_increase_memory_limit() {
	global $pagenow;

	if ( ! is_admin() ) {
		return;
	}

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return;
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	if ( empty( $pagenow ) ) {
		return;
	}

	// increase memory limit on Edit Post page only
	if ( ! in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
		return;
	}

	/**
	 * check if a user clicked "Increase Memory Limit" button
	 * in the "Failure Notification" modal window
	 */
	if ( ! et_should_memory_limit_increase() ) {
		return;
	}

	et_increase_memory_limit();
}
endif;
add_action( 'admin_init', 'et_maybe_increase_memory_limit' );

if ( ! function_exists( 'et_should_memory_limit_increase' ) ) :
function et_should_memory_limit_increase() {
	if ( '1' === ( $memory_limit = et_get_option( 'set_memory_limit' ) ) ) {
		return true;
	}

	return false;
}
endif;

if ( ! function_exists( 'et_reset_memory_limit_increase_setting' ) ) :
function et_reset_memory_limit_increase_setting() {
	wp_enqueue_script( 'et-builder-reset-memory-limit-increase', ET_BUILDER_URI . '/scripts/reset_memory_limit_increase_setting.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
	wp_localize_script( 'et-builder-reset-memory-limit-increase', 'et_reset_memory_limit_increase', array(
		'et_builder_reset_memory_limit_nonce' => wp_create_nonce( 'et_builder_reset_memory_limit_nonce' ),
	) );

	printf(
		'<button class="et_disable_memory_limit_increase button button-primary button-large">%1$s</button>',
		esc_html__( 'Disable Memory Limit Increase' )
	);
}
endif;

if ( ! function_exists( 'et_pb_detect_cache_plugins' ) ) :
/**
 * Detect the activated cache plugins and return the link to plugin options and return its page link or false
 * @return string or bool
 */
function et_pb_detect_cache_plugins() {
	if ( function_exists( 'edd_w3edge_w3tc_activate_license' ) ) {
		return array(
			'name' => 'W3 Total Cache',
			'page' => 'admin.php?page=w3tc_pgcache',
		);
	}

	if ( function_exists( 'wpsupercache_activate' ) ) {
		return array(
			'name' => 'WP Super Cache',
			'page' => 'options-general.php?page=wpsupercache',
		);
	}

	if ( class_exists( 'HyperCache' ) ) {
		return array(
			'name' => 'Hyper Cache',
			'page' => 'options-general.php?page=hyper-cache%2Foptions.php',
		);
	}

	if ( class_exists( '\zencache\plugin' ) ) {
		return array(
			'name' => 'ZenCache',
			'page' => 'admin.php?page=zencache',
		);
	}

	if ( class_exists( 'WpFastestCache' ) ) {
		return array(
			'name' => 'WP Fastest Cache',
			'page' => 'admin.php?page=WpFastestCacheOptions',
		);
	}

	if ( '1' === get_option( 'wordfenceActivated' ) ) {
		return array(
			'name' => 'Wordfence',
			'page' => 'admin.php?page=WordfenceSitePerf',
		);
	}

	if ( function_exists( 'cachify_autoload' ) ) {
		return array(
			'name' => 'Cachify',
			'page' => 'options-general.php?page=cachify',
		);
	}

	if ( class_exists( 'FlexiCache' ) ) {
		return array(
			'name' => 'FlexiCache',
			'page' => 'options-general.php?page=flexicache',
		);
	}

	if ( function_exists( 'rocket_init' ) ) {
		return array(
			'name' => 'WP Rocket',
			'page' => 'options-general.php?page=wprocket',
		);
	}

	if ( function_exists( 'cloudflare_init' ) ) {
		return array(
			'name' => 'CloudFlare',
			'page' => 'options-general.php?page=cloudflare',
		);
	}

	return false;
}
endif;

function et_pb_force_regenerate_templates() {
	// add option to indicate that templates cache should be updated in case of term added/removed/updated
	et_update_option( 'et_pb_clear_templates_cache', 'on' );
}

add_action( 'created_term', 'et_pb_force_regenerate_templates' );
add_action( 'edited_term', 'et_pb_force_regenerate_templates' );
add_action( 'delete_term', 'et_pb_force_regenerate_templates' );

function et_pb_ab_get_current_ab_module_id( $test_id, $subject_index = false ) {
	$all_subjects = false !== ( $all_subjects_raw = get_post_meta( $test_id, '_et_pb_ab_subjects' , true ) ) ? explode( ',', $all_subjects_raw ) : array();

	if ( false === $subject_index ) {
		$current_subject_index = false !== ( $saved_next_subject = get_post_meta( $test_id, '_et_pb_ab_next_subject' , true ) ) ? (int) $saved_next_subject : 0;
	} else {
		$current_subject_index = $subject_index;
	}

	if ( empty( $all_subjects ) ) {
		return 0;
	}

	if ( ! isset( $all_subjects[ $current_subject_index ] ) ) {
		return $all_subjects[0];
	}

	return $all_subjects[ $current_subject_index ];
}

function et_pb_ab_get_saved_ab_module_id( $test_id, $client_id ) {
	global $wpdb;

	$table_name = $wpdb->prefix . 'et_divi_ab_testing_clients';
	$saved_module_id = false;

	if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name ) {
		// construct sql query to get saved module ID from db for current client
		$sql = "SELECT subject_id FROM $table_name WHERE test_id = %d AND client_id = %s";
		$sql_args = array(
			intval( $test_id ),
			sanitize_text_field( $client_id ),
		);

		$saved_module_data = $wpdb->get_results( $wpdb->prepare( $sql, $sql_args ), ARRAY_A );

		if ( empty( $saved_module_data ) ) {
			return false;
		}

		$saved_module_id = $saved_module_data[0]['subject_id'];
	}

	return $saved_module_id;
}

function et_pb_ab_increment_current_ab_module_id( $test_id, $user_unique_id ) {
	global $wpdb;

	$table_name = $wpdb->prefix . 'et_divi_ab_testing_clients';
	$all_subjects = false !== ( $all_subjects_raw = get_post_meta( $test_id, '_et_pb_ab_subjects' , true ) ) ? explode( ',', $all_subjects_raw ) : array();
	$current_subject_index = false !== ( $saved_next_subject = get_post_meta( $test_id, '_et_pb_ab_next_subject' , true ) ) ? (int) $saved_next_subject : 0;

	if ( empty( $all_subjects ) ) {
		return;
	}

	// sanitize and set vars
	$test_id = intval( $test_id );
	$current_subject_index = intval( $current_subject_index );
	$user_unique_id = sanitize_text_field( $user_unique_id );

	// update the subject id in db for the current client and current test
	$wpdb->insert(
		$table_name,
		array(
			'test_id'    => $test_id,
			'subject_id' => $current_subject_index,
			'client_id'  => $user_unique_id,
		),
		array(
			'%d', // test_id
			'%d', // subject_id
			'%s', // client_id
		)
	);

	// increment the index of next subject, set to 0 if it's a last subject in the list
	$next_subject_index = ( count( $all_subjects ) - 1 ) < ( $current_subject_index + 1 ) ? 0 : $current_subject_index + 1;

	update_post_meta( $test_id, '_et_pb_ab_next_subject' , $next_subject_index );
}

/**
 * Add the record into Split testing log table
 *
 * @return void
 */
function et_pb_add_stats_record( $stats_data_array ) {
	global $wpdb;

	$table_name = $wpdb->prefix . 'et_divi_ab_testing_stats';

	$record_date = current_time( 'mysql' );
	$client_unique_id = et_pb_get_visitor_id();

	// sanitize and set vars
	$test_id = intval( $stats_data_array['test_id'] );
	$subject_id = intval( $stats_data_array['subject_id'] );
	$record_type = sanitize_text_field( $stats_data_array['record_type'] );
	$record_date = sanitize_text_field( $record_date );

	// construct sql query to find out whether or not event logged for current visitor
	$sql = "SELECT COUNT(*) FROM $table_name WHERE test_id = %d AND subject_id = %d AND event = %s AND client_id = %s";
	$sql_args = array(
		$test_id,
		$subject_id,
		$record_type,
		$client_unique_id
	);

	// do not proceed if event already logged for current visitor
	if ( 0 < $wpdb->get_var( $wpdb->prepare( $sql, $sql_args ) ) ) {
		return;
	}

	$wpdb->insert(
		$table_name,
		array(
			'record_date' => $record_date,
			'test_id'     => $test_id,
			'subject_id'  => $subject_id,
			'event'       => $record_type,
			'client_id'   => $client_unique_id,
		),
		array(
			'%s', // record_date
			'%d', // test_id
			'%d', // subject_id
			'%s', // event
			'%s', // client_id
		)
	);
}


function et_pb_ab_get_subject_id() {
	if ( isset( $_POST['et_frontend_nonce'] ) && ! wp_verify_nonce( $_POST['et_frontend_nonce'], 'et_frontend_nonce' ) ) {
		die( -1 );
	}

	$test_id = intval( $_POST['et_pb_ab_test_id'] );

	$user_unique_id = et_pb_get_visitor_id();
	$saved_module_id = et_pb_ab_get_saved_ab_module_id( $test_id, $user_unique_id );

	$current_ab_module_id = et_pb_ab_get_current_ab_module_id( $test_id, $saved_module_id );
	$current_ab_module_id = intval( $current_ab_module_id );

	if ( false === $saved_module_id ) {
		// log the view_page event
		et_pb_add_stats_record( array(
				'test_id'     => $test_id,
				'subject_id'  => $current_ab_module_id,
				'record_type' => 'view_page',
			)
		);

		// increment the module id for the next time
		et_pb_ab_increment_current_ab_module_id( $test_id, $user_unique_id );
	}

	// retrieve the cached subjects HTML
	$subjects_cache = get_post_meta( $test_id, 'et_pb_subjects_cache', true );

	$result = array(
		'id'      => $current_ab_module_id,
		'content' => isset( $subjects_cache[ $current_ab_module_id ] ) ? $subjects_cache[ $current_ab_module_id ] : '',
	);

	die( json_encode( $result ) );
}
add_action( 'wp_ajax_et_pb_ab_get_subject_id', 'et_pb_ab_get_subject_id' );
add_action( 'wp_ajax_nopriv_et_pb_ab_get_subject_id', 'et_pb_ab_get_subject_id' );

/**
 * Generate the user id which is md5 hash from IP Address
 * @return string
 */
function et_pb_get_visitor_id() {
	$user_ip_addr = $_SERVER['REMOTE_ADDR'];

	/**
	 * Properly determine the user ID if proxy is used.
	 * Check whether the $_SERVER['HTTP_X_FORWARDED_FOR'] or $_SERVER['X_FORWARDED_FOR'] exist and use it
	 * Otherwise use $_SERVER['REMOTE_ADDR']
	 */
	if ( ! empty( $_SERVER['X_FORWARDED_FOR'] ) ) {
		$x_forwarded_array = explode( ',', $_SERVER['X_FORWARDED_FOR'] );

		if ( ! empty( $x_forwarded_array ) ) {
			$user_ip_addr = trim( $x_forwarded_array[0] );
		}
	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$http_x_forwarded_array = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );

		if ( ! empty( $http_x_forwarded_array ) ) {
			$user_ip_addr = trim( $http_x_forwarded_array[0] );
		}
	}

	// return the md5 hash from user IP address
	return md5( sanitize_text_field( $user_ip_addr ) );
}

/**
 * Register Builder portabilities.
 *
 * @since To define
 *
 * @return bool Always return true.
 */
function et_pb_register_builder_portabilities() {
	global $shortname;

	// Don't overwrite global.
	$_shortname = empty( $shortname ) ? 'divi' : $shortname;

	// Make sure the Portability is loaded.
	et_core_load_component( 'portability' );

	// Register the Roles Editor portability.
	et_core_portability_register( 'et_pb_roles', array(
		'name'   => esc_html__( 'Divi Role Editor Settings', 'et_builder' ),
		'type'   => 'options',
		'target' => 'et_pb_role_settings',
		'view'   => ( isset( $_GET['page'] ) && $_GET['page'] === "et_{$_shortname}_role_editor" ),
	) );

	// Register the Builder individual layouts portability.
	et_core_portability_register( 'et_builder', array(
		'name' =>  esc_html__( 'Divi Builder Layout', 'et_builder' ),
		'type' => 'post',
		'view' => ( function_exists( 'et_builder_should_load_framework' ) && et_builder_should_load_framework() ),
	) );

	// Register the Builder Layouts Post Type portability.
	et_core_portability_register( 'et_builder_layouts', array(
		'name' => esc_html__( 'Divi Builder Layouts', 'et_builder' ),
		'type'   => 'post_type',
		'target' => ET_BUILDER_LAYOUT_POST_TYPE,
		'view'   => ( isset( $_GET['post_type'] ) && $_GET['post_type'] === ET_BUILDER_LAYOUT_POST_TYPE ),
	) );
}
add_action( 'admin_init', 'et_pb_register_builder_portabilities' );

/**
 * Modify the portability export WP query.
 *
 * @since To define
 *
 * @return string New query.
 */
function et_pb_modify_portability_export_wp_query( $query ) {
	// Exclude predefined layout from export.
	return array_merge( $query, array(
		'meta_query' => array(
			'relation' => 'OR',
			array(
				'key'     => '_et_pb_predefined_layout',
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'     => '_et_pb_predefined_layout',
				'value'   => 'on',
				'compare' => 'NOT LIKE',
			),
		),
	) );
}
add_filter( 'et_core_portability_export_wp_query_et_builder_layouts', 'et_pb_modify_portability_export_wp_query' );

/**
 * Check whether current page is pagebuilder preview page
 * @return bool
 */
function is_et_pb_preview() {
	global $wp_query;
	return ( 'true' === $wp_query->get( 'et_pb_preview' ) && isset( $_GET['et_pb_preview_nonce'] ) );
}

if ( ! function_exists( 'et_pb_is_pagebuilder_used' ) ) :
function et_pb_is_pagebuilder_used( $page_id ) {
	return ( 'on' === get_post_meta( $page_id, '_et_pb_use_builder', true ) );
}
endif;

if ( ! function_exists( 'et_builder_get_font_family' ) ) :
function et_builder_get_font_family( $font_name, $use_important = false ) {
	$fonts = et_builder_get_fonts();

	$font_style = $font_weight = '';

	$font_name_ms = isset( $fonts[ $font_name ] ) && isset( $fonts[ $font_name ]['add_ms_version'] ) ? "'{$font_name} MS', " : "";

	if ( isset( $fonts[ $font_name ]['parent_font'] ) ){
		$font_style = $fonts[ $font_name ]['styles'];
		$font_name = $fonts[ $font_name ]['parent_font'];
	}

	if ( '' !== $font_style ) {
		$font_weight = sprintf( ' font-weight: %1$s;', esc_html( $font_style ) );
	}

	$style = sprintf( 'font-family: \'%1$s\', %5$s%2$s%3$s;%4$s',
		esc_html( $font_name ),
		isset( $fonts[ $font_name ] ) ? et_builder_get_websafe_font_stack( $fonts[ $font_name ]['type'] ) : "",
		( $use_important ? ' !important' : '' ),
		$font_weight,
		$font_name_ms
	);

	return $style;
}
endif;

if ( ! function_exists( 'et_builder_get_fonts' ) ) :
function et_builder_get_fonts( $settings = array() ) {
	$defaults = array(
		'prepend_standard_fonts' => true,
	);

	$settings = wp_parse_args( $settings, $defaults );

	$fonts = $settings['prepend_standard_fonts']
		? array_merge( et_builder_get_websafe_fonts(), et_builder_get_google_fonts() )
		: array_merge( et_builder_get_google_fonts(), et_builder_get_websafe_fonts() );

	return $fonts;
}
endif;

if ( ! function_exists( 'et_builder_get_websafe_font_stack' ) ) :
function et_builder_get_websafe_font_stack( $type = 'sans-serif' ) {
	$font_stack = '';

	switch ( $type ) {
		case 'sans-serif':
			$font_stack = 'Helvetica, Arial, Lucida, sans-serif';
			break;
		case 'serif':
			$font_stack = 'Georgia, "Times New Roman", serif';
			break;
		case 'cursive':
			$font_stack = 'cursive';
			break;
	}

	return $font_stack;
}
endif;

if ( ! function_exists( 'et_builder_get_websafe_fonts' ) ) :
function et_builder_get_websafe_fonts() {
	$websafe_fonts = array(
		'Georgia' => array(
			'styles' 		=> '300italic,400italic,600italic,700italic,800italic,400,300,600,700,800',
			'character_set' => 'cyrillic,greek,latin',
			'type'			=> 'serif',
		),
		'Times New Roman' => array(
			'styles' 		=> '300italic,400italic,600italic,700italic,800italic,400,300,600,700,800',
			'character_set' => 'arabic,cyrillic,greek,hebrew,latin',
			'type'			=> 'serif',
		),
		'Arial' => array(
			'styles' 		=> '300italic,400italic,600italic,700italic,800italic,400,300,600,700,800',
			'character_set' => 'arabic,cyrillic,greek,hebrew,latin',
			'type'			=> 'sans-serif',
		),
		'Trebuchet' => array(
			'styles' 		=> '300italic,400italic,600italic,700italic,800italic,400,300,600,700,800',
			'character_set' => 'cyrillic,latin',
			'type'			=> 'sans-serif',
			'add_ms_version'=> true,
		),
		'Verdana' => array(
			'styles' 		=> '300italic,400italic,600italic,700italic,800italic,400,300,600,700,800',
			'character_set' => 'cyrillic,latin',
			'type'			=> 'sans-serif',
		),
	);

	$_websafe_fonts = array();

	foreach ( $websafe_fonts as $font_name => $settings ) {
		$settings['standard'] = true;

		$_websafe_fonts[ $font_name ] = $settings;
	}

	$websafe_fonts = $_websafe_fonts;

	return apply_filters( 'et_websafe_fonts', $websafe_fonts );
}
endif;

if ( ! function_exists( 'et_builder_get_google_fonts' ) ) :
function et_builder_get_google_fonts() {
	$google_fonts = array(
		'Abel' => array(
			'styles' 		=> '400',
			'character_set' => 'latin',
			'type'			=> 'sans-serif',
		),
		'Amatic SC' => array(
			'styles' 		=> '400,700',
			'character_set' => 'latin',
			'type'			=> 'cursive',
		),
		'Arimo' => array(
			'styles' 		=> '400,400italic,700italic,700',
			'character_set' => 'latin,cyrillic-ext,latin-ext,greek-ext,cyrillic,greek,vietnamese',
			'type'			=> 'sans-serif',
		),
		'Arvo' => array(
			'styles' 		=> '400,400italic,700,700italic',
			'character_set' => 'latin',
			'type'			=> 'serif',
		),
		'Bevan' => array(
			'styles' 		=> '400',
			'character_set' => 'latin',
			'type'			=> 'cursive',
		),
		'Bitter' => array(
			'styles' 		=> '400,400italic,700',
			'character_set' => 'latin,latin-ext',
			'type'			=> 'serif',
		),
		'Black Ops One' => array(
			'styles' 		=> '400',
			'character_set' => 'latin,latin-ext',
			'type'			=> 'cursive',
		),
		'Boogaloo' => array(
			'styles' 		=> '400',
			'character_set' => 'latin',
			'type'			=> 'cursive',
		),
		'Bree Serif' => array(
			'styles' 		=> '400',
			'character_set' => 'latin,latin-ext',
			'type'			=> 'serif',
		),
		'Calligraffitti' => array(
			'styles' 		=> '400',
			'character_set' => 'latin',
			'type'			=> 'cursive',
		),
		'Cantata One' => array(
			'styles' 		=> '400',
			'character_set' => 'latin,latin-ext',
			'type'			=> 'serif',
		),
		'Cardo' => array(
			'styles' 		=> '400,400italic,700',
			'character_set' => 'latin,greek-ext,greek,latin-ext',
			'type'			=> 'serif',
		),
		'Changa One' => array(
			'styles' 		=> '400,400italic',
			'character_set' => 'latin',
			'type'			=> 'cursive',
		),
		'Cherry Cream Soda' => array(
			'styles' 		=> '400',
			'character_set' => 'latin',
			'type'			=> 'cursive',
		),
		'Chewy' => array(
			'styles' 		=> '400',
			'character_set' => 'latin',
			'type'			=> 'cursive',
		),
		'Comfortaa' => array(
			'styles' 		=> '400,300,700',
			'character_set' => 'latin,cyrillic-ext,greek,latin-ext,cyrillic',
			'type'			=> 'cursive',
		),
		'Coming Soon' => array(
			'styles' 		=> '400',
			'character_set' => 'latin',
			'type'			=> 'cursive',
		),
		'Covered By Your Grace' => array(
			'styles' 		=> '400',
			'character_set' => 'latin',
			'type'			=> 'cursive',
		),
		'Crafty Girls' => array(
			'styles' 		=> '400',
			'character_set' => 'latin',
			'type'			=> 'cursive',
		),
		'Crete Round' => array(
			'styles' 		=> '400,400italic',
			'character_set' => 'latin,latin-ext',
			'type'			=> 'serif',
		),
		'Crimson Text' => array(
			'styles' 		=> '400,400italic,600,600italic,700,700italic',
			'character_set' => 'latin',
			'type'			=> 'serif',
		),
		'Cuprum' => array(
			'styles' 		=> '400,400italic,700italic,700',
			'character_set' => 'latin,latin-ext,cyrillic',
			'type'			=> 'sans-serif',
		),
		'Dancing Script' => array(
			'styles' 		=> '400,700',
			'character_set' => 'latin',
			'type'			=> 'cursive',
		),
		'Dosis' => array(
			'styles' 		=> '400,200,300,500,600,700,800',
			'character_set' => 'latin,latin-ext',
			'type'			=> 'sans-serif',
		),
		'Droid Sans' => array(
			'styles' 		=> '400,700',
			'character_set' => 'latin',
			'type'			=> 'sans-serif',
		),
		'Droid Serif' => array(
			'styles' 		=> '400,400italic,700,700italic',
			'character_set' => 'latin',
			'type'			=> 'serif',
		),
		'Francois One' => array(
			'styles' 		=> '400',
			'character_set' => 'latin,latin-ext',
			'type'			=> 'sans-serif',
		),
		'Fredoka One' => array(
			'styles' 		=> '400',
			'character_set' => 'latin',
			'type'			=> 'cursive',
		),
		'The Girl Next Door' => array(
			'styles' 		=> '400',
			'character_set' => 'latin',
			'type'			=> 'cursive',
		),
		'Gloria Hallelujah' => array(
			'styles' 		=> '400',
			'character_set' => 'latin',
			'type'			=> 'cursive',
		),
		'Happy Monkey' => array(
			'styles' 		=> '400',
			'character_set' => 'latin,latin-ext',
			'type'			=> 'cursive',
		),
		'Indie Flower' => array(
			'styles' 		=> '400',
			'character_set' => 'latin',
			'type'			=> 'cursive',
		),
		'Josefin Slab' => array(
			'styles' 		=> '400,100,100italic,300,300italic,400italic,600,700,700italic,600italic',
			'character_set' => 'latin',
			'type'			=> 'serif',
		),
		'Judson' => array(
			'styles' 		=> '400,400italic,700',
			'character_set' => 'latin',
			'type'			=> 'serif',
		),
		'Kreon' => array(
			'styles' 		=> '400,300,700',
			'character_set' => 'latin',
			'type'			=> 'serif',
		),
		'Lato' => array(
			'styles' 		=> '400,100,100italic,300,300italic,400italic,700,700italic,900,900italic',
			'character_set' => 'latin',
			'type'			=> 'sans-serif',
		),
		'Lato Light' => array(
			'parent_font' => 'Lato',
			'styles'      => '300',
		),
		'Leckerli One' => array(
			'styles' 		=> '400',
			'character_set' => 'latin',
			'type'			=> 'cursive',
		),
		'Lobster' => array(
			'styles' 		=> '400',
			'character_set' => 'latin,cyrillic-ext,latin-ext,cyrillic',
			'type'			=> 'cursive',
		),
		'Lobster Two' => array(
			'styles' 		=> '400,400italic,700,700italic',
			'character_set' => 'latin',
			'type'			=> 'cursive',
		),
		'Lora' => array(
			'styles' 		=> '400,400italic,700,700italic',
			'character_set' => 'latin',
			'type'			=> 'serif',
		),
		'Luckiest Guy' => array(
			'styles' 		=> '400',
			'character_set' => 'latin',
			'type'			=> 'cursive',
		),
		'Merriweather' => array(
			'styles' 		=> '400,300,900,700',
			'character_set' => 'latin',
			'type'			=> 'serif',
		),
		'Metamorphous' => array(
			'styles' 		=> '400',
			'character_set' => 'latin,latin-ext',
			'type'			=> 'cursive',
		),
		'Montserrat' => array(
			'styles' 		=> '400,700',
			'character_set' => 'latin',
			'type'			=> 'sans-serif',
		),
		'Noticia Text' => array(
			'styles' 		=> '400,400italic,700,700italic',
			'character_set' => 'latin,vietnamese,latin-ext',
			'type'			=> 'serif',
		),
		'Nova Square' => array(
			'styles' 		=> '400',
			'character_set' => 'latin',
			'type'			=> 'cursive',
		),
		'Nunito' => array(
			'styles' 		=> '400,300,700',
			'character_set' => 'latin',
			'type'			=> 'sans-serif',
		),
		'Old Standard TT' => array(
			'styles' 		=> '400,400italic,700',
			'character_set' => 'latin',
			'type'			=> 'serif',
		),
		'Open Sans' => array(
			'styles' 		=> '300italic,400italic,600italic,700italic,800italic,400,300,600,700,800',
			'character_set' => 'latin,cyrillic-ext,greek-ext,greek,vietnamese,latin-ext,cyrillic',
			'type'			=> 'sans-serif',
		),
		'Open Sans Condensed' => array(
			'styles' 		=> '300,300italic,700',
			'character_set' => 'latin,cyrillic-ext,latin-ext,greek-ext,greek,vietnamese,cyrillic',
			'type'			=> 'sans-serif',
		),
		'Open Sans Light' => array(
			'parent_font' => 'Open Sans',
			'styles'      => '300',
		),
		'Oswald' => array(
			'styles' 		=> '400,300,700',
			'character_set' => 'latin,latin-ext',
			'type'			=> 'sans-serif',
		),
		'Pacifico' => array(
			'styles' 		=> '400',
			'character_set' => 'latin',
			'type'			=> 'cursive',
		),
		'Passion One' => array(
			'styles' 		=> '400,700,900',
			'character_set' => 'latin,latin-ext',
			'type'			=> 'cursive',
		),
		'Patrick Hand' => array(
			'styles' 		=> '400',
			'character_set' => 'latin,vietnamese,latin-ext',
			'type'			=> 'cursive',
		),
		'Permanent Marker' => array(
			'styles' 		=> '400',
			'character_set' => 'latin',
			'type'			=> 'cursive',
		),
		'Play' => array(
			'styles' 		=> '400,700',
			'character_set' => 'latin,cyrillic-ext,cyrillic,greek-ext,greek,latin-ext',
			'type'			=> 'sans-serif',
		),
		'Playfair Display' => array(
			'styles' 		=> '400,400italic,700,700italic,900italic,900',
			'character_set' => 'latin,latin-ext,cyrillic',
			'type'			=> 'serif',
		),
		'Poiret One' => array(
			'styles' 		=> '400',
			'character_set' => 'latin,latin-ext,cyrillic',
			'type'			=> 'cursive',
		),
		'PT Sans' => array(
			'styles' 		=> '400,400italic,700,700italic',
			'character_set' => 'latin,latin-ext,cyrillic',
			'type'			=> 'sans-serif',
		),
		'PT Sans Narrow' => array(
			'styles' 		=> '400,700',
			'character_set' => 'latin,latin-ext,cyrillic',
			'type'			=> 'sans-serif',
		),
		'PT Serif' => array(
			'styles' 		=> '400,400italic,700,700italic',
			'character_set' => 'latin,cyrillic',
			'type'			=> 'serif',
		),
		'Raleway' => array(
			'styles' 		=> '400,100,200,300,600,500,700,800,900',
			'character_set' => 'latin',
			'type'			=> 'sans-serif',
		),
		'Raleway Light' => array(
			'parent_font' => 'Raleway',
			'styles'      => '300',
		),
		'Reenie Beanie' => array(
			'styles' 		=> '400',
			'character_set' => 'latin',
			'type'			=> 'cursive',
		),
		'Righteous' => array(
			'styles' 		=> '400',
			'character_set' => 'latin,latin-ext',
			'type'			=> 'cursive',
		),
		'Roboto' => array(
			'styles' 		=> '400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic',
			'character_set' => 'latin,cyrillic-ext,latin-ext,cyrillic,greek-ext,greek,vietnamese',
			'type'			=> 'sans-serif',
		),
		'Roboto Condensed' => array(
			'styles' 		=> '400,300,300italic,400italic,700,700italic',
			'character_set' => 'latin,cyrillic-ext,latin-ext,greek-ext,cyrillic,greek,vietnamese',
			'type'			=> 'sans-serif',
		),
		'Roboto Light' => array(
			'parent_font' => 'Roboto',
			'styles'      => '100',
		),
		'Rock Salt' => array(
			'styles' 		=> '400',
			'character_set' => 'latin',
			'type'			=> 'cursive',
		),
		'Rokkitt' => array(
			'styles' 		=> '400,700',
			'character_set' => 'latin',
			'type'			=> 'serif',
		),
		'Sanchez' => array(
			'styles' 		=> '400,400italic',
			'character_set' => 'latin,latin-ext',
			'type'			=> 'serif',
		),
		'Satisfy' => array(
			'styles' 		=> '400',
			'character_set' => 'latin',
			'type'			=> 'cursive',
		),
		'Schoolbell' => array(
			'styles' 		=> '400',
			'character_set' => 'latin',
			'type'			=> 'cursive',
		),
		'Shadows Into Light' => array(
			'styles' 		=> '400',
			'character_set' => 'latin',
			'type'			=> 'cursive',
		),
		'Source Sans Pro' => array(
			'styles' 		=> '400,200,200italic,300,300italic,400italic,600,600italic,700,700italic,900,900italic',
			'character_set' => 'latin,latin-ext',
			'type'			=> 'sans-serif',
		),
		'Source Sans Pro Light' => array(
			'parent_font' => 'Source Sans Pro',
			'styles'      => '300',
		),
		'Special Elite' => array(
			'styles' 		=> '400',
			'character_set' => 'latin',
			'type'			=> 'cursive',
		),
		'Squada One' => array(
			'styles' 		=> '400',
			'character_set' => 'latin',
			'type'			=> 'cursive',
		),
		'Tangerine' => array(
			'styles' 		=> '400,700',
			'character_set' => 'latin',
			'type'			=> 'cursive',
		),
		'Ubuntu' => array(
			'styles' 		=> '400,300,300italic,400italic,500,500italic,700,700italic',
			'character_set' => 'latin,cyrillic-ext,cyrillic,greek-ext,greek,latin-ext',
			'type'			=> 'sans-serif',
		),
		'Unkempt' => array(
			'styles' 		=> '400,700',
			'character_set' => 'latin',
			'type'			=> 'cursive',
		),
		'Vollkorn' => array(
			'styles' 		=> '400,400italic,700italic,700',
			'character_set' => 'latin',
			'type'			=> 'serif',
		),
		'Walter Turncoat' => array(
			'styles' 		=> '400',
			'character_set' => 'latin',
			'type'			=> 'cursive',
		),
		'Yanone Kaffeesatz' => array(
			'styles' 		=> '400,200,300,700',
			'character_set' => 'latin,latin-ext',
			'type'			=> 'sans-serif',
		),
	);

	return apply_filters( 'et_builder_google_fonts', $google_fonts );
}
endif;

if ( ! function_exists( 'et_pb_get_mailchimp_lists' ) ) :
function et_pb_get_mailchimp_lists( $regenerate_mailchimp_list = 'off' ) {
	$lists = array();

	if ( et_is_builder_plugin_active() ) {
		$mailchimp_api_option = get_option( 'et_pb_builder_options' );
		$mailchimp_api_key = isset( $mailchimp_api_option['newsletter_main_mailchimp_key'] ) ? $mailchimp_api_option['newsletter_main_mailchimp_key'] : '';
	} else {
		$mailchimp_api_key = et_get_option( 'divi_mailchimp_api_key' );
		$regenerate_mailchimp_list = et_get_option( 'divi_regenerate_mailchimp_lists', 'false' );
	}

	if ( empty( $mailchimp_api_key ) || false === strpos( $mailchimp_api_key, '-' ) ) {
		return false;
	}

	$et_pb_mailchimp_lists = get_transient( 'et_pb_mailchimp_lists' );

	if ( 'on' === $regenerate_mailchimp_list || false === $et_pb_mailchimp_lists ) {
		if ( ! class_exists( 'MailChimp_Divi' ) ) {
			require_once( ET_BUILDER_DIR . 'subscription/mailchimp/mailchimp.php' );
		}

		try {
			$mailchimp = new MailChimp_Divi( $mailchimp_api_key );
			$retval = $mailchimp->call( 'lists/list', array( 'limit' => 100 ) );
			$retval_body = json_decode( wp_remote_retrieve_body( $retval ), true );
			$retrieved_lists = isset( $retval_body['data'] ) ? $retval_body['data'] : array();

			if ( 200 !== wp_remote_retrieve_response_code( $retval ) || empty( $retval_body['data'] ) || ! is_array( $retval_body['data'] ) ) {
				return $et_pb_mailchimp_lists;
			}

			// if there is more than 100 lists in account, then perform additional calls to retrieve all the lists.
			if ( ! empty( $retval_body['total'] ) && 100 < $retval_body['total'] ) {
				// determine how many requests we need to retrieve all the lists
				$total_pages = ceil( $retval_body['total'] / 100 );

				for ( $i = 1; $i <= $total_pages; $i++ ) {
					$retval_additional = $mailchimp->call( 'lists/list', array(
							'limit' => 100,
							'start' => $i,
						)
					);

					if ( ! empty( $retval_additional ) && empty( $retval_additional['errors'] ) ) {
						if ( ! empty( $retval_additional['data'] ) ) {
							$retrieved_lists = array_merge( $retrieved_lists, $retval_additional['data'] );
						}
					}
				}
			}

			if ( ! empty( $retrieved_lists ) ) {
				foreach ( $retrieved_lists as $list ) {
					$lists[$list['id']] = $list['name'];
				}
			}

			set_transient( 'et_pb_mailchimp_lists', $lists, 60*60*24 );
		} catch ( Exception $exc ) {
			$lists = $et_pb_mailchimp_lists;
		}

		return $lists;
	} else {
		return $et_pb_mailchimp_lists;
	}
}
endif;

if ( ! function_exists( 'et_pb_get_aweber_lists' ) ) :
function et_pb_get_aweber_lists( $regenerate_aweber_list = 'off' ) {
	$lists = array();

	$account = et_pb_get_aweber_account();

	if ( ! et_is_builder_plugin_active() ) {
		$regenerate_aweber_list = et_get_option( 'divi_regenerate_aweber_lists', 'false' );
	}

	if ( ! $account ) {
		return false;
	}

	if ( 'on' === $regenerate_aweber_list || false === ( $et_pb_aweber_lists = get_transient( 'et_pb_aweber_lists' ) ) ) {

		if ( ! class_exists( 'AWeberAPI' ) ) {
			require_once( ET_BUILDER_DIR . 'subscription/aweber/aweber_api.php' );
		}

		$aweber_lists = $account->lists;

		if ( isset( $aweber_lists ) ) {
			foreach ( $aweber_lists as $list ) {
				$lists[$list->id] = $list->name;
			}
		}

		set_transient( 'et_pb_aweber_lists', $lists, 60*60*24 );
	} else {
		$lists = $et_pb_aweber_lists;
	}

	return $lists;
}
endif;

if ( ! function_exists( 'et_pb_register_global_js' ) ) :
function et_pb_register_global_js() {
	wp_register_script( 'et_pb_admin_global_js', ET_BUILDER_URI . '/scripts/admin_global_functions.js', array(), ET_BUILDER_VERSION, true );
}
endif;
add_action( 'admin_enqueue_scripts', 'et_pb_register_global_js' );