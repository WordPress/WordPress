<?php

foreach( array( 'edit', 'post', 'post-new' ) as $hook ) {
	add_action( "admin_head-{$hook}.php", 'et_builder_library_custom_styles' );
}

//remove "edit" action from the bulk changes on et_pb_layout editor screen
function builder_customize_bulk( $actions ) {
	unset( $actions['edit'] );

	return $actions;
}
add_filter( 'bulk_actions-edit-et_pb_layout', 'builder_customize_bulk' );


function et_pb_get_used_built_for_post_types() {
	global $wpdb;

	$built_for_post_types = $wpdb->get_col(
		"SELECT DISTINCT( meta_value )
		FROM $wpdb->postmeta
		WHERE meta_key = '_et_pb_built_for_post_type'
		AND meta_value IS NOT NULL
		AND meta_value != ''
		"
	);

	return $built_for_post_types;
}

function et_pb_layout_restrict_manage_posts() {
	global $pagenow;

	if ( ! is_admin() || 'edit.php' !== $pagenow || ! isset( $_GET['post_type'] ) || 'et_pb_layout' !== $_GET['post_type'] ) {
		return;
	}

	$used_built_for_post_types = et_pb_get_used_built_for_post_types();

	if ( count( $used_built_for_post_types ) <= 1 ) {
		return;
	}

	$built_for_post_type_request = isset( $_GET['built_for'] ) ? sanitize_text_field( $_GET['built_for'] ) : '';

	if ( ! in_array( $built_for_post_type_request, $used_built_for_post_types ) ) {
		$built_for_post_type_request = '';
	}

	?>
	<select name="built_for">
		<option><?php esc_html_e( 'Built For Any', 'et_builder' ); ?></option>
		<?php $is_default_added = false; ?>
		<?php foreach ( $used_built_for_post_types as $built_for_post_type ) { ?>
		<?php $is_default_post_type = in_array( $built_for_post_type, et_pb_get_standard_post_types() );
			// do not add default post types into the menu if it was added already
			if ( $is_default_post_type && $is_default_added ) {
				continue;
			}
			?>
			<?php $built_for_post_type_display = apply_filters( 'et_pb_built_for_post_type_display', $built_for_post_type ); ?>
			<option value="<?php echo esc_attr( $built_for_post_type ); ?>" <?php selected( $built_for_post_type_request, $built_for_post_type ); ?>><?php echo esc_html( ucwords( $built_for_post_type_display ) ); ?></option>
		<?php
			$is_default_added = $is_default_post_type ? true : $is_default_added;
		} ?>
	</select>
	<?php
}
add_action( 'restrict_manage_posts', 'et_pb_layout_restrict_manage_posts' );

function et_pb_layout_manage_posts_columns( $columns ) {
	$_new_columns = array();
	foreach ( $columns as $column_key => $column ) {
		$_new_columns[ $column_key ] = $column;

		if ( 'taxonomy-layout_type' === $column_key ) {
			$_new_columns['built_for'] = esc_html__( 'Built For', 'et_builder' );
			$_new_columns['layout_global'] = esc_html__( 'Global Layout', 'et_builder' );
		}
	}

	return $_new_columns;
}
add_filter( 'manage_et_pb_layout_posts_columns', 'et_pb_layout_manage_posts_columns' );

function et_pb_built_for_post_type_display( $post_type ) {
	$standard_post_types = et_pb_get_standard_post_types();

	if ( in_array( $post_type, $standard_post_types ) ) {
		return esc_html__( 'Standard', 'et_builder' );
	}

	return $post_type;
}

add_filter( 'et_pb_layout_built_for_post_type_column', 'et_pb_built_for_post_type_display' );
add_filter( 'et_pb_built_for_post_type_display', 'et_pb_built_for_post_type_display' );

function et_pb_get_standard_post_types() {
	$standard_post_types = apply_filters( 'et_pb_standard_post_types', array(
		'post',
		'page',
		'project',
	) );

	return $standard_post_types;
}

function et_pb_layout_manage_posts_custom_column( $column_key, $post_id ) {
	switch ( $column_key ) {
		case 'built_for':
			$built_for = get_post_meta( $post_id, '_et_pb_built_for_post_type', true );
			$built_for = apply_filters( 'et_pb_layout_built_for_post_type_column', $built_for, $post_id );
			echo esc_html( ucwords( $built_for ) );
			break;
		case 'layout_global':
			$template_scope = wp_get_object_terms( $post_id, 'scope' );
			$is_global_template = ! empty( $template_scope[0] ) ? $template_scope[0]->slug : 'regular';
			$is_global_template = str_replace( '_', ' ', $is_global_template );
			echo esc_html( ucwords( $is_global_template ) );
			break;
	}
}
add_action( 'manage_et_pb_layout_posts_custom_column', 'et_pb_layout_manage_posts_custom_column', 10, 2 );

function et_update_old_layouts_tax() {
	$layouts_updated = get_theme_mod( 'et_pb_layouts_updated', 'no' );

	if ( 'yes' !== $layouts_updated ) {
		$query = new WP_Query( array(
			'meta_query'      => array(
				'relation' => 'AND',
				array(
					'key'     => '_et_pb_predefined_layout',
					'value'   => 'on',
					'compare' => 'NOT EXISTS',
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
		) );

		wp_reset_postdata();

		if ( ! empty ( $query->posts ) ) {
			foreach( $query->posts as $single_post ) {

				$defined_layout_type = wp_get_post_terms( $single_post->ID, 'layout_type' );

				if ( empty( $defined_layout_type ) ) {
					wp_set_post_terms( $single_post->ID, 'layout', 'layout_type' );
				}
			}
		}

		set_theme_mod( 'et_pb_layouts_updated', 'yes' );
	}
}
add_action( 'admin_init', 'et_update_old_layouts_tax' );

// update existing layouts to support _et_pb_built_for_post_type
function et_update_layouts_built_for_post_types() {
	$layouts_updated = get_theme_mod( 'et_updated_layouts_built_for_post_types', 'no' );
	if ( 'yes' !== $layouts_updated ) {
		$query = new WP_Query( array(
			'meta_query'      => array(
				'relation' => 'AND',
				array(
					'key'     => '_et_pb_built_for_post_type',
					'compare' => 'NOT EXISTS',
				),
			),
			'post_type'       => ET_BUILDER_LAYOUT_POST_TYPE,
			'posts_per_page'  => '-1',
		) );

		wp_reset_postdata();

		if ( ! empty ( $query->posts ) ) {
			foreach( $query->posts as $single_post ) {
				update_post_meta( $single_post->ID, '_et_pb_built_for_post_type', 'page' );
			}
		}

		set_theme_mod( 'et_updated_layouts_built_for_post_types', 'yes' );
	}
}
add_action( 'admin_init', 'et_update_layouts_built_for_post_types' );

function et_builder_library_custom_styles() {
	global $typenow;

	et_core_load_main_fonts();

	wp_enqueue_style( 'et-builder-notification-popup-styles', ET_BUILDER_URI . '/styles/notification_popup_styles.css' );

	if ( 'et_pb_layout' === $typenow ) {
		$new_layout_modal = et_pb_generate_new_layout_modal();

		wp_enqueue_style( 'library-styles', ET_BUILDER_URI . '/styles/library_pages.css', array( 'et-core-admin' ), ET_BUILDER_PRODUCT_VERSION );

		wp_enqueue_script( 'library-scripts', ET_BUILDER_URI . '/scripts/library_scripts.js', array( 'jquery', 'et_pb_admin_global_js' ), ET_BUILDER_PRODUCT_VERSION );
		wp_localize_script( 'library-scripts', 'et_pb_new_template_options', array(
				'ajaxurl'       => admin_url( 'admin-ajax.php' ),
				'et_admin_load_nonce' => wp_create_nonce( 'et_admin_load_nonce' ),
				'modal_output'  => $new_layout_modal,
			)
		);
	} else {
		wp_enqueue_script( 'et-builder-failure-notice', ET_BUILDER_URI . '/scripts/failure_notice.js', array( 'jquery' ), ET_BUILDER_PRODUCT_VERSION );
	}
}

define( 'ET_BUILDER_PREDEFINED_LAYOUTS_VERSION', 2 );

function et_pb_update_predefined_layouts() {
	// don't do anything if layouts version have been updated and layouts exist
	if ( 'on' === get_theme_mod( 'et_pb_predefined_layouts_version_' . ET_BUILDER_PREDEFINED_LAYOUTS_VERSION ) && ( et_pb_predefined_layouts_exist() ) ) {
		return;
	}

	// delete default layouts
	// delete all default layouts w/o new built_for meta
	et_pb_delete_predefined_layouts();
	// delete all default layouts w/ new built_for meta
	et_pb_delete_predefined_layouts('page');

	// add predefined layouts
	et_pb_add_predefined_layouts();

	set_theme_mod( 'et_pb_predefined_layouts_version_' . ET_BUILDER_PREDEFINED_LAYOUTS_VERSION, 'on' );
}
add_action( 'admin_init', 'et_pb_update_predefined_layouts' );

// check whether at least 1 predefined layout exists in DB and return its ID
if ( ! function_exists( 'et_pb_predefined_layouts_exist' ) ) :
function et_pb_predefined_layouts_exist() {
	$args = array(
		'posts_per_page' => 1,
		'post_type'      => ET_BUILDER_LAYOUT_POST_TYPE,
		'meta_query'      => array(
			'relation' => 'AND',
			array(
				'key'     => '_et_pb_predefined_layout',
				'value'   => 'on',
				'compare' => 'EXISTS',
			),
			array(
				'key'     => '_et_pb_built_for_post_type',
				'value'   => 'page',
				'compare' => 'IN',
			)
		),
	);

	$predefined_layout = get_posts( $args );

	if ( ! $predefined_layout ) {
		return false;
	}

	return $predefined_layout[0]->ID;
}
endif;

if ( ! function_exists( 'et_pb_delete_predefined_layouts' ) ) :
function et_pb_delete_predefined_layouts( $built_for_post_type = '' ) {
	$args = array(
		'posts_per_page' => -1,
		'post_type'      => ET_BUILDER_LAYOUT_POST_TYPE,
		'meta_query'      => array(
			'relation' => 'AND',
			array(
				'key'     => '_et_pb_predefined_layout',
				'value'   => 'on',
				'compare' => 'EXISTS',
			),
		),
	);

	if ( ! empty( $built_for_post_type ) ) {
		$args['meta_query'][] = array(
			'key'     => '_et_pb_built_for_post_type',
			'value'   => $built_for_post_type,
			'compare' => 'IN',
		);
	} else {
		$args['meta_query'][] = array(
			'key'     => '_et_pb_built_for_post_type',
			'compare' => 'NOT EXISTS',
		);
	}

	$predefined_layouts = get_posts( $args );

	if ( $predefined_layouts ) {
		foreach ( $predefined_layouts as $predefined_layout ) {
			if ( isset( $predefined_layout->ID ) ) {
				wp_delete_post( $predefined_layout->ID, true );
			}
		}
	}
}
endif;

if ( ! function_exists( 'et_pb_add_predefined_layouts' ) ) :
function et_pb_add_predefined_layouts() {
	$et_builder_layouts = et_pb_get_predefined_layouts();

	$meta = array(
		'_et_pb_predefined_layout'   => 'on',
		'_et_pb_built_for_post_type' => 'page',
	);

	if ( isset( $et_builder_layouts ) && is_array( $et_builder_layouts ) ) {
		foreach ( $et_builder_layouts as $et_builder_layout ) {
			et_pb_create_layout( $et_builder_layout['name'], $et_builder_layout['content'], $meta );
		}
	}

	set_theme_mod( 'et_pb_predefined_layouts_added', 'on' );
}
endif;

if ( ! function_exists( 'et_pb_get_predefined_layouts' ) ) :
function et_pb_get_predefined_layouts() {
	$layouts = array();

	$layouts[] = array(
		'name'    => esc_html__( 'Homepage Basic', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section fullwidth="off" specialty="off"][et_pb_row][et_pb_column type="4_4"][et_pb_slider admin_label="Slider" show_arrows="on" show_pagination="on" auto="off" parallax="off"][et_pb_slide heading="Welcome To My Website" button_text="Enter" button_link="#" background_color="#27c9b9" alignment="center" background_layout="dark"]Lorem ipsum dolor sit amet, consectetur adipiscing elit. In in risus eget lectus suscipit malesuada. Maecenas ut urna mollis, aliquam eros at, laoreet metus.[/et_pb_slide][/et_pb_slider][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="on" specialty="off" background_color="#f7f7f7" inner_shadow="on" parallax="off"][et_pb_fullwidth_header admin_label="Fullwidth Header" title="We Are a Company of Passionate Designers and Developers" background_layout="light" text_orientation="center" /][/et_pb_section][et_pb_section fullwidth="off" specialty="off" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="1_4"][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum" url_new_window="off" animation="top" background_layout="light" text_orientation="center" use_icon="on" font_icon="h" icon_color="#a8a8a8" use_circle="on" circle_color="#ffffff" use_circle_border="on" circle_border_color="#e0e0e0" icon_placement="top"]Divi will change the way you build websites forever. The advanced page builder makes it possible to build truly dynamic pages without learning code.[/et_pb_blurb][/et_pb_column][et_pb_column type="1_4"][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum" url_new_window="off" animation="top" background_layout="light" text_orientation="center" use_icon="on" font_icon="" icon_color="#a8a8a8" use_circle="on" circle_color="#ffffff" use_circle_border="on" circle_border_color="#e0e0e0" icon_placement="top"]Divi will change the way you build websites forever. The advanced page builder makes it possible to build truly dynamic pages without learning code.[/et_pb_blurb][/et_pb_column][et_pb_column type="1_4"][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum" url_new_window="off" animation="top" background_layout="light" text_orientation="center" use_icon="on" font_icon="v" icon_color="#a8a8a8" use_circle="on" circle_color="#ffffff" use_circle_border="on" circle_border_color="#e0e0e0" icon_placement="top"]Divi will change the way you build websites forever. The advanced page builder makes it possible to build truly dynamic pages without learning code.[/et_pb_blurb][/et_pb_column][et_pb_column type="1_4"][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum" url_new_window="off" animation="top" background_layout="light" text_orientation="center" use_icon="on" font_icon="g" icon_color="#a8a8a8" use_circle="on" circle_color="#ffffff" use_circle_border="on" circle_border_color="#e0e0e0" icon_placement="top"]Divi will change the way you build websites forever. The advanced page builder makes it possible to build truly dynamic pages without learning code.[/et_pb_blurb][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="4_4"][et_pb_cta admin_label="Call To Action" title="Drop Me a Line" button_url="#" button_text="Contact" background_color="#2ea3f2" use_background_color="on" background_layout="dark" text_orientation="center"]Lorem ipsum dolor sit amet, consectetur adipiscing elit. In in risus eget lectus suscipit malesuada. Maecenas ut urna mollis, aliquam eros at, laoreet metus.[/et_pb_cta][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
	);


	$layouts[] = array(
		'name'    => esc_html__( 'Homepage Shop', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section fullwidth="on" specialty="off"][et_pb_fullwidth_slider admin_label="Fullwidth Slider" show_arrows="on" show_pagination="on" auto="off" parallax="off"][et_pb_slide heading="Welcome to Our Shop" button_text="Shop Now" background_color="#0194f3" image="https://elegantthemesimages.com/images/premade/d2-placeholder-510px.png" alignment="center" background_layout="dark" button_link="#"]Lorem ipsum dolor sit amet, consectetur adipiscing elit. In in risus eget lectus suscipit malesuada. Maecenas ut urna mollis, aliquam eros at, laoreet metus.[/et_pb_slide][/et_pb_fullwidth_slider][/et_pb_section][et_pb_section inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="4_4"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]<h1>Featured Products</h1>[/et_pb_text][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="4_4"][et_pb_shop admin_label="Shop" type="featured" posts_number="4" columns="4" orderby="menu_order" /][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="on" background_color="#f7f7f7" inner_shadow="off" parallax="off"][et_pb_column type="3_4" specialty_columns="3"][et_pb_row_inner][et_pb_column_inner type="4_4"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]<h1>Recent Products</h1>[/et_pb_text][et_pb_shop admin_label="Shop" type="recent" posts_number="6" columns="3" orderby="date" /][/et_pb_column_inner][/et_pb_row_inner][et_pb_row_inner][et_pb_column_inner type="1_2"][et_pb_cta admin_label="Call To Action" title="Holiday Special Sale" button_text="Shop Now" background_color="#108bf5" use_background_color="on" background_layout="dark" text_orientation="center" button_url="#"]Cras rutrum blandit sem, molestie consequat erat luctus vel. Cras nunc est, laoreet sit amet ligula et, eleifend commodo dui.[/et_pb_cta][/et_pb_column_inner][et_pb_column_inner type="1_2"][et_pb_cta admin_label="Call To Action" title="Become a Vendor" button_text="Learn More" background_color="#27c9b9" use_background_color="on" background_layout="dark" text_orientation="center" button_url="#"]Cras rutrum blandit sem, molestie consequat erat luctus vel. Cras nunc est, laoreet sit amet ligula et, eleifend commodo dui.[/et_pb_cta][/et_pb_column_inner][/et_pb_row_inner][/et_pb_column][et_pb_column type="1_4"][et_pb_sidebar admin_label="Sidebar" orientation="right" background_layout="light" /][/et_pb_column][/et_pb_section][et_pb_section fullwidth="off" specialty="off" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="4_4"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]
<h1>What Our Customers are Saying</h1>
[/et_pb_text][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="1_2"][et_pb_testimonial admin_label="Testimonial" author="Lorem Ipsum" url_new_window="off" portrait_url="https://elegantthemesimages.com/images/premade/d2-placeholder-225px.png" quote_icon="off" use_background_color="on" background_color="#f5f5f5" background_layout="light" text_orientation="left"]"Cras rutrum blandit sem, molestie consequat erat luctus vel. Cras nunc est, laoreet sit amet ligula et, eleifend commodo dui. Vivamus id blandit nisi, eu mattis odio."[/et_pb_testimonial][/et_pb_column][et_pb_column type="1_2"][et_pb_testimonial admin_label="Testimonial" author="Lorem Ipsum" url_new_window="off" portrait_url="https://elegantthemesimages.com/images/premade/d2-placeholder-225px.png" quote_icon="off" use_background_color="on" background_color="#f5f5f5" background_layout="light" text_orientation="left"]"Cras rutrum blandit sem, molestie consequat erat luctus vel. Cras nunc est, laoreet sit amet ligula et, eleifend commodo dui. Vivamus id blandit nisi, eu mattis odio."[/et_pb_testimonial][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="1_2"][et_pb_testimonial admin_label="Testimonial" author="Lorem Ipsum" url_new_window="off" portrait_url="https://elegantthemesimages.com/images/premade/d2-placeholder-225px.png" quote_icon="off" use_background_color="on" background_color="#f5f5f5" background_layout="light" text_orientation="left"]"Cras rutrum blandit sem, molestie consequat erat luctus vel. Cras nunc est, laoreet sit amet ligula et, eleifend commodo dui. Vivamus id blandit nisi, eu mattis odio."[/et_pb_testimonial][/et_pb_column][et_pb_column type="1_2"][et_pb_testimonial admin_label="Testimonial" author="Lorem Ipsum" url_new_window="off" portrait_url="https://elegantthemesimages.com/images/premade/d2-placeholder-225px.png" quote_icon="off" use_background_color="on" background_color="#f5f5f5" background_layout="light" text_orientation="left"]"Cras rutrum blandit sem, molestie consequat erat luctus vel. Cras nunc est, laoreet sit amet ligula et, eleifend commodo dui. Vivamus id blandit nisi, eu mattis odio."[/et_pb_testimonial][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_color="#27c9b9" inner_shadow="off" parallax="on"][et_pb_row][et_pb_column type="4_4"][et_pb_cta admin_label="Call To Action" title="Browse Our Full Shop" button_url="#" button_text="Enter" use_background_color="off" background_color="#108bf5" background_layout="dark" text_orientation="center"]Cras rutrum blandit sem, molestie consequat erat luctus vel. Cras nunc est, laoreet sit amet ligula et, eleifend commodo dui.[/et_pb_cta][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
	);


	$layouts[] = array(
		'name'    => esc_html__( 'Homepage Portfolio', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section fullwidth="on" specialty="off" background_color="#2e2e2e" inner_shadow="off" parallax="off"][et_pb_fullwidth_slider admin_label="Fullwidth Slider" show_arrows="on" show_pagination="on" auto="on" parallax="on"][et_pb_slide background_image="https://elegantthemesimages.com/images/premade/d2-placeholder-1920.png" background_color="#ffffff" alignment="center" background_layout="dark" heading="Hello! Welcome To My Online Portfolio" /][et_pb_slide background_color="#f84b48" alignment="center" background_layout="dark" heading="Project Title" button_text="View Project" /][et_pb_slide background_color="#23a1f5" alignment="center" background_layout="dark" heading="Project Title" button_text="View Project" /][et_pb_slide background_color="#27c8b8" alignment="center" background_layout="dark" heading="Project Title" button_text="View Project" /][/et_pb_fullwidth_slider][et_pb_fullwidth_portfolio admin_label="Fullwidth Portfolio" fullwidth="on" show_title="on" show_date="on" background_layout="dark" auto="off" /][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_color="#f7f7f7" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="1_2"][et_pb_image admin_label="Image" src="https://elegantthemesimages.com/images/premade/d2-placeholder-510px.png" show_in_lightbox="off" url_new_window="off" animation="left" /][/et_pb_column][et_pb_column type="1_2"][et_pb_divider admin_label="Divider" color="#ffffff" show_divider="off" height="40" /][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum" url_new_window="off" use_icon="on" font_icon="" icon_color="#ffffff" use_circle="on" circle_color="#2ea3f2" use_circle_border="off" circle_border_color="#2ea3f2" icon_placement="left" animation="top" background_layout="light" text_orientation="center"]Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc aliquam justo et nibh venenatis aliquet.[/et_pb_blurb][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum" url_new_window="off" use_icon="on" font_icon="" icon_color="#ffffff" use_circle="on" circle_color="#2ea3f2" use_circle_border="off" circle_border_color="#2ea3f2" icon_placement="left" animation="top" background_layout="light" text_orientation="center"]Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc aliquam justo et nibh venenatis aliquet.[/et_pb_blurb][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum" url_new_window="off" use_icon="on" font_icon="" icon_color="#ffffff" use_circle="on" circle_color="#2ea3f2" use_circle_border="off" circle_border_color="#2ea3f2" icon_placement="left" animation="top" background_layout="light" text_orientation="center"]Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc aliquam justo et nibh venenatis aliquet.[/et_pb_blurb][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="1_4"][et_pb_number_counter admin_label="Number Counter" title="Coding Languages" number="7" percent_sign="off" background_layout="light" /][/et_pb_column][et_pb_column type="1_4"][et_pb_number_counter admin_label="Number Counter" title="Loyal Clients" number="65" percent_sign="off" background_layout="light" /][/et_pb_column][et_pb_column type="1_4"][et_pb_number_counter admin_label="Number Counter" title="International Awards" number="12" percent_sign="off" background_layout="light" /][/et_pb_column][et_pb_column type="1_4"][et_pb_number_counter admin_label="Number Counter" title="Years of Experience" number="10" percent_sign="off" background_layout="light" /][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_color="#27c8b8" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="4_4"][et_pb_cta admin_label="Call To Action" title="View My Full Portfolio" button_url="#" button_text="Enter" background_color="#2caaca" use_background_color="off" background_layout="dark" text_orientation="center"]Vivamus ipsum velit, ullamcorper quis nibh non, molestie tempus sapien. Mauris ultrices, felis ut eleifend auctor, leo felis vehicula quam, ut accumsan augue nunc at nisl. Cras venenatis ac lorema ac tincidunt.[/et_pb_cta][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
	);


	$layouts[] = array(
		'name'    => esc_html__( 'Homepage Company', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section fullwidth="on" specialty="off"][et_pb_fullwidth_slider admin_label="Fullwidth Slider" show_arrows="on" show_pagination="on" auto="off" parallax="off"][et_pb_slide heading="Our Company" button_text="Features" button_link="https://elegantthemes.com/preview/Divi2/features/" background_color="#8d1bf4" alignment="center" background_layout="dark" image="https://elegantthemesimages.com/images/premade/d2-300px.png" background_image="https://elegantthemesimages.com/images/premade/d2-placeholder-1920.png"]Quisque eleifend orci sit amet est semper, iaculis tempor mi volutpat. Phasellus consectetur justo sed tristique molestie. Cras lectus quam, vehicula eu dictum a, sollicitudin id velit.[/et_pb_slide][et_pb_slide heading="Slide Title" button_text="Learn More" button_link="#" background_color="#f84c48" alignment="center" background_layout="dark"]Quisque eleifend orci sit amet est semper, iaculis tempor mi volutpat. Phasellus consectetur justo sed tristique molestie. Cras lectus quam, vehicula eu dictum a, sollicitudin id velit.[/et_pb_slide][/et_pb_fullwidth_slider][/et_pb_section][et_pb_section][et_pb_row][et_pb_column type="1_3"][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum" url_new_window="off" use_icon="off" icon_color="#2ea3f2" use_circle="off" circle_color="#2ea3f2" use_circle_border="off" circle_border_color="#2ea3f2" image="https://elegantthemesimages.com/images/premade/d2-placeholder-320px.jpg" icon_placement="top" animation="top" background_layout="light" text_orientation="center"]Cras semper dictum lectus ac bibendum. Sed id massa vel lorem laoreet molestie. Nullam vulputate lacus at mauris molestie porttitor.[/et_pb_blurb][/et_pb_column][et_pb_column type="1_3"][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum" url_new_window="off" use_icon="off" icon_color="#2ea3f2" use_circle="off" circle_color="#2ea3f2" use_circle_border="off" circle_border_color="#2ea3f2" image="https://elegantthemesimages.com/images/premade/d2-placeholder-320px.jpg" icon_placement="top" animation="top" background_layout="light" text_orientation="center"]Cras semper dictum lectus ac bibendum. Sed id massa vel lorem laoreet molestie. Nullam vulputate lacus at mauris molestie porttitor.[/et_pb_blurb][/et_pb_column][et_pb_column type="1_3"][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum" url_new_window="off" use_icon="off" icon_color="#2ea3f2" use_circle="off" circle_color="#2ea3f2" use_circle_border="off" circle_border_color="#2ea3f2" image="https://elegantthemesimages.com/images/premade/d2-placeholder-320px.jpg" icon_placement="top" animation="top" background_layout="light" text_orientation="center"]Cras semper dictum lectus ac bibendum. Sed id massa vel lorem laoreet molestie. Nullam vulputate lacus at mauris molestie porttitor.[/et_pb_blurb][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="on" specialty="off" background_color="#4b4b4b" inner_shadow="on" parallax="off"][et_pb_fullwidth_portfolio admin_label="Fullwidth Portfolio" title="Recent Work" fullwidth="on" show_title="on" show_date="on" background_layout="dark" auto="off" /][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_color="#eeeeee" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="1_4"][et_pb_image admin_label="Image" src="https://elegantthemesimages.com/images/premade/et-logo.png" show_in_lightbox="off" url_new_window="off" animation="top" /][/et_pb_column][et_pb_column type="1_4"][et_pb_image admin_label="Image" src="https://elegantthemesimages.com/images/premade/et-logo.png" show_in_lightbox="off" url_new_window="off" animation="top" /][/et_pb_column][et_pb_column type="1_4"][et_pb_image admin_label="Image" src="https://elegantthemesimages.com/images/premade/et-logo.png" show_in_lightbox="off" url_new_window="off" animation="top" /][/et_pb_column][et_pb_column type="1_4"][et_pb_image admin_label="Image" src="https://elegantthemesimages.com/images/premade/et-logo.png" show_in_lightbox="off" url_new_window="off" animation="top" /][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="1_2"][et_pb_testimonial admin_label="Testimonial" author="Lorem Ipsum" company_name="Company" url_new_window="off" portrait_url="https://elegantthemesimages.com/images/premade/d2-placeholder-225px.png" quote_icon="off" use_background_color="on" background_color="#f5f5f5" background_layout="light" text_orientation="left" job_title="Job Role" url="#"]Aenean consectetur ipsum ante, vel egestas enim tincidunt quis. Pellentesque vitae congue neque, vel mattis ante.[/et_pb_testimonial][/et_pb_column][et_pb_column type="1_2"][et_pb_testimonial admin_label="Testimonial" author="Lorem Ipsum" company_name="Company" url_new_window="off" portrait_url="https://elegantthemesimages.com/images/premade/d2-placeholder-225px.png" quote_icon="off" use_background_color="on" background_color="#f5f5f5" background_layout="light" text_orientation="left" job_title="Job Role" url="#"]Aenean consectetur ipsum ante, vel egestas enim tincidunt quis. Pellentesque vitae congue neque, vel mattis ante.[/et_pb_testimonial][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="1_2"][et_pb_testimonial admin_label="Testimonial" author="Lorem Ipsum" company_name="Company" url_new_window="off" portrait_url="https://elegantthemesimages.com/images/premade/d2-placeholder-225px.png" quote_icon="off" use_background_color="on" background_color="#f5f5f5" background_layout="light" text_orientation="left" job_title="Job Role" url="#"]Aenean consectetur ipsum ante, vel egestas enim tincidunt quis. Pellentesque vitae congue neque, vel mattis ante.[/et_pb_testimonial][/et_pb_column][et_pb_column type="1_2"][et_pb_testimonial admin_label="Testimonial" author="Lorem Ipsum" company_name="Company" url_new_window="off" portrait_url="https://elegantthemesimages.com/images/premade/d2-placeholder-225px.png" quote_icon="off" use_background_color="on" background_color="#f5f5f5" background_layout="light" text_orientation="left" job_title="Job Role" url="#"]Aenean consectetur ipsum ante, vel egestas enim tincidunt quis. Pellentesque vitae congue neque, vel mattis ante.[/et_pb_testimonial][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="on" specialty="off"][et_pb_fullwidth_map admin_label="Fullwidth Map" zoom_level="8" address_lat="37.43410184255073" address_lng="-122.04768412931253"][et_pb_map_pin title="Elegant Themes" pin_address="San Francisco, CA, USA" pin_address_lat="37.7749295" pin_address_lng="-122.41941550000001" /][et_pb_map_pin title="Lorem Ipsum" pin_address="San Jose, CA, USA" pin_address_lat="37.3393857" pin_address_lng="-121.89495549999998" /][/et_pb_fullwidth_map][/et_pb_section][et_pb_section fullwidth="off" specialty="off" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="4_4"][et_pb_contact_form admin_label="Contact Form" captcha="off" title="Contact Us" /][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
	);


	$layouts[] = array(
		'name'    => esc_html__( 'Homepage Corporate', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section fullwidth="on" specialty="off" inner_shadow="off" parallax="off"][et_pb_fullwidth_slider admin_label="Fullwidth Slider" show_arrows="on" show_pagination="on" auto="off" parallax="off"][et_pb_slide heading="Our Company" button_text="Learn More" button_link="#" background_color="#f7f7f7" image="https://elegantthemesimages.com/images/premade/d2-placeholder-510px.png" alignment="center" background_layout="light"]Changing the way you build websites. Aenean consectetur ipsum ante, vel egestas enim tincidunt quis. Pellentesque vitae congue neque, vel mattis ante. In vitae tempus nunc. Etiam adipiscing enim sed condimentum ultrices.[/et_pb_slide][/et_pb_fullwidth_slider][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_color="#3a4149" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="1_4"][et_pb_circle_counter admin_label="Circle Counter" title="Sales & Marketing" number="70" percent_sign="on" background_layout="dark" /][/et_pb_column][et_pb_column type="1_4"][et_pb_circle_counter admin_label="Circle Counter" title="Brand & Identity" number="90" percent_sign="on" background_layout="dark" /][/et_pb_column][et_pb_column type="1_4"][et_pb_circle_counter admin_label="Circle Counter" title="Web Design" number="80" percent_sign="on" background_layout="dark" /][/et_pb_column][et_pb_column type="1_4"][et_pb_circle_counter admin_label="Circle Counter" title="App Development" number="50" percent_sign="on" background_layout="dark" /][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="2_3"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]<h2>What We Offer</h2>[/et_pb_text][et_pb_tabs admin_label="Tabs"][et_pb_tab title="Overview"]Aenean consectetur ipsum ante, vel egestas enim tincidunt quis. Pellentesque vitae congue neque, vel mattis ante. In vitae tempus nunc. Etiam adipiscing enim sed condimentum ultrices. Aenean consectetur ipsum ante, vel egestas enim tincidunt quis. Pellentesque vitae congue neque, vel mattis ante.[/et_pb_tab][et_pb_tab title="Mission Statement"]Aenean consectetur ipsum ante, vel egestas enim tincidunt quis. Pellentesque vitae congue neque, vel mattis ante. In vitae tempus nunc. Etiam adipiscing enim sed condimentum ultrices. Aenean consectetur ipsum ante, vel egestas enim tincidunt quis. Pellentesque vitae congue neque, vel mattis ante.[/et_pb_tab][et_pb_tab title="Culture"]Aenean consectetur ipsum ante, vel egestas enim tincidunt quis. Pellentesque vitae congue neque, vel mattis ante. In vitae tempus nunc. Etiam adipiscing enim sed condimentum ultrices. Aenean consectetur ipsum ante, vel egestas enim tincidunt quis. Pellentesque vitae congue neque, vel mattis ante.[/et_pb_tab][/et_pb_tabs][/et_pb_column][et_pb_column type="1_3"][et_pb_divider admin_label="Divider" color="#ffffff" show_divider="off" height="50" /][et_pb_counters admin_label="Bar Counters" background_layout="light" background_color="#f4f4f4"][et_pb_counter percent="80"]Brand Consulting[/et_pb_counter][et_pb_counter percent="45"]Marketing Campaigns [/et_pb_counter][et_pb_counter percent="95"]Custom Website Design[/et_pb_counter][/et_pb_counters][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="4_4"][et_pb_divider admin_label="Divider" color="#eaeaea" show_divider="on" height="30" /][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="1_2"][et_pb_image admin_label="Image" src="https://elegantthemesimages.com/images/premade/d2-placeholder-510px.png" show_in_lightbox="off" url_new_window="off" animation="left" /][/et_pb_column][et_pb_column type="1_2"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]<h1>Our Work Flow</h1>[/et_pb_text][et_pb_blurb admin_label="Blurb" title="Lorem Upsum" url_new_window="off" use_icon="on" font_icon="" icon_color="#ffffff" use_circle="on" use_circle_border="off" circle_border_color="#2ea3f2" icon_placement="left" animation="top" background_layout="light" text_orientation="center"]Aenean consectetur ipsum ante, vel egestas enim tincidunt quis. Pellentesque vitae congue neque, vel mattis ante.[/et_pb_blurb][et_pb_blurb admin_label="Blurb" title="Lorem Upsum" url_new_window="off" use_icon="on" font_icon="" icon_color="#ffffff" use_circle="on" use_circle_border="off" circle_border_color="#2ea3f2" icon_placement="left" animation="top" background_layout="light" text_orientation="center"]Aenean consectetur ipsum ante, vel egestas enim tincidunt quis. Pellentesque vitae congue neque, vel mattis ante.[/et_pb_blurb][et_pb_blurb admin_label="Blurb" title="Lorem Upsum" url_new_window="off" use_icon="on" font_icon="" icon_color="#ffffff" use_circle="on" use_circle_border="off" circle_border_color="#2ea3f2" icon_placement="left" animation="top" background_layout="light" text_orientation="center"]Aenean consectetur ipsum ante, vel egestas enim tincidunt quis. Pellentesque vitae congue neque, vel mattis ante.[/et_pb_blurb][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_color="#212a34" inner_shadow="off" parallax="off" background_image="https://www.elegantthemesimages.com/images/premade/d2-placeholder-1920.png"][et_pb_row][et_pb_column type="4_4"][et_pb_divider admin_label="Divider" color="#ffffff" show_divider="off" height="20" /][et_pb_blurb admin_label="Blurb" url_new_window="off" use_icon="off" icon_color="#2ea3f2" use_circle="off" circle_color="#2ea3f2" use_circle_border="off" circle_border_color="#2ea3f2" image="https://elegantthemesimages.com/images/premade/d2-300px.png" icon_placement="top" animation="top" background_layout="light" text_orientation="center" /][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="1_2"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]<h2>Frequently Asked Questions</h2>
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc aliquam justo et nibh venenatis aliquet. Morbi mollis mollis pellentesque. Aenean vitae erat velit. Maecenas urna sapien, dignissim a augue vitae, porttitor luctus urna. Morbi scelerisque semper congue. Donec vitae congue quam. Pellentesque convallis est a eros porta, ut porttitor magna convallis.

Donec quis felis imperdiet, vestibulum est ut, pulvinar dolor. Mauris laoreet varius sem, tempus congue nibh elementum facilisis. Aliquam ut odio risus. Mauris consectetur mi et ante aliquam, eget posuere urna semper. Vestibulum vestibulum rhoncus enim, id iaculis eros commodo non.[/et_pb_text][/et_pb_column][et_pb_column type="1_2"][et_pb_accordion admin_label="Accordion"][et_pb_accordion_item title="What kind of clients do you work with?"]Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc aliquam justo et nibh venenatis aliquet.[/et_pb_accordion_item][et_pb_accordion_item title="What is your turn around time?"]Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc aliquam justo et nibh venenatis aliquet. Morbi mollis mollis pellentesque. Aenean vitae erat velit. Maecenas urna sapien, dignissim a augue vitae, porttitor luctus urna.[/et_pb_accordion_item][et_pb_accordion_item title="Do you have an affiliate program?"]Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc aliquam justo et nibh venenatis aliquet. Morbi mollis mollis pellentesque. Aenean vitae erat velit. Maecenas urna sapien, dignissim a augue vitae, porttitor luctus urna.[/et_pb_accordion_item][/et_pb_accordion][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="4_4"][et_pb_divider admin_label="Divider" color="#eaeaea" show_divider="on" height="30" /][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="1_3"][et_pb_testimonial admin_label="Testimonial" author="Lorem Ipsum" url_new_window="off" quote_icon="on" use_background_color="on" background_color="#f5f5f5" background_layout="light" text_orientation="center"]Aenean consectetur ipsum ante, vel egestas enim tincidunt quis. Pellentesque vitae congue neque, vel mattis ante. In vitae tempus nunc. Etiam adipiscing enim sed condimentum ultrices. Aenean consectetur ipsum ante, vel egestas enim tincidunt qu[/et_pb_testimonial][/et_pb_column][et_pb_column type="1_3"][et_pb_testimonial admin_label="Testimonial" author="Lorem Ipsum" url_new_window="off" quote_icon="on" use_background_color="on" background_color="#f5f5f5" background_layout="light" text_orientation="center"]Aenean consectetur ipsum ante, vel egestas enim tincidunt quis. Pellentesque vitae congue neque, vel mattis ante. In vitae tempus nunc. Etiam adipiscing enim sed condimentum ultrices. Aenean consectetur ipsum ante, vel egestas enim tincidunt qu[/et_pb_testimonial][/et_pb_column][et_pb_column type="1_3"][et_pb_testimonial admin_label="Testimonial" author="Lorem Ipsum" url_new_window="off" quote_icon="on" use_background_color="on" background_color="#f5f5f5" background_layout="light" text_orientation="center"]Aenean consectetur ipsum ante, vel egestas enim tincidunt quis. Pellentesque vitae congue neque, vel mattis ante. In vitae tempus nunc. Etiam adipiscing enim sed condimentum ultrices. Aenean consectetur ipsum ante, vel egestas enim tincidunt qu[/et_pb_testimonial][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_color="#f74b47" inner_shadow="on" parallax="off"][et_pb_row][et_pb_column type="4_4"][et_pb_cta admin_label="Call To Action" button_url="#" button_text="Email" use_background_color="off" background_color="#2ea3f2" background_layout="dark" text_orientation="center" title="Don't Be Shy"]Drop us a line anytime, and one of our customer service reps will respond to you as soon as possible[/et_pb_cta][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
	);


	$layouts[] = array(
		'name'    => esc_html__( 'Homepage Extended', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section fullwidth="on"][et_pb_fullwidth_slider admin_label="Fullwidth Slider" show_arrows="on" show_pagination="on" auto="off" parallax="on"][et_pb_slide heading="Welcome To Our Website" button_text="Learn More" button_link="#" background_color="#ffffff" alignment="center" background_layout="dark" video_bg_width="1920" video_bg_height="638" background_image="https://elegantthemesimages.com/images/premade/d2-placeholder-1920.png"]Vivamus ipsum velit, ullamcorper quis nibh non, molestie tempus sapien. Mauris ultrices, felis ut eleifend auctor, leo felis vehicula quam, ut accumsan augue nunc at nisl. Cras venenatis ac lorema ac tincidunt. Mauris ultrices, felis ut eleifend auctor, leo felis vehicula quam, ut accumsan augue.[/et_pb_slide][et_pb_slide heading="Sky's The Limit" background_color="#444444" image="https://elegantthemesimages.com/images/premade/d2-placeholder-510px.png" alignment="center" background_layout="dark" button_text="A Closer Look" button_link="#"]Vivamus ipsum velit, ullamcorper quis nibh non, molestie tempus sapien. Mauris ultrices, felis ut eleifend auctor, leo felis vehicula quam, ut accumsan augue nunc at nisl.[/et_pb_slide][/et_pb_fullwidth_slider][/et_pb_section][et_pb_section fullwidth="off"][et_pb_row][et_pb_column type="1_3"][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum Dolor" url_new_window="off" animation="off" background_layout="light" text_orientation="left" icon_placement="left" font_icon="" use_icon="on" use_circle="off" use_circle_border="off" icon_color="#7c4dd5" circle_color="#7c4dd5" circle_border_color="#2caaca"]Vestibulum lobortis. Donec at euismod nibh, eu bibendum quam.[/et_pb_blurb][/et_pb_column][et_pb_column type="1_3"][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum Dolor" url_new_window="off" animation="off" background_layout="light" text_orientation="left" icon_placement="left" font_icon="" use_icon="on" use_circle="off" use_circle_border="off" icon_color="#7c4dd5" circle_color="#7c4dd5" circle_border_color="#2caaca"]Vestibulum lobortis. Donec at euismod nibh, eu bibendum quam.[/et_pb_blurb][/et_pb_column][et_pb_column type="1_3"][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum Dolor" url_new_window="off" animation="off" background_layout="light" text_orientation="left" icon_placement="left" font_icon="" use_icon="on" use_circle="off" use_circle_border="off" icon_color="#7c4dd5" circle_color="#7c4dd5" circle_border_color="#2caaca"]Vestibulum lobortis. Donec at euismod nibh, eu bibendum quam.[/et_pb_blurb][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="1_3"][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum Dolor" url_new_window="off" animation="off" background_layout="light" text_orientation="left" icon_placement="left" font_icon="" use_icon="on" use_circle="off" use_circle_border="off" icon_color="#7c4dd5" circle_color="#7c4dd5" circle_border_color="#2caaca"]Vestibulum lobortis. Donec at euismod nibh, eu bibendum quam.[/et_pb_blurb][/et_pb_column][et_pb_column type="1_3"][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum Dolor" url_new_window="off" animation="off" background_layout="light" text_orientation="left" icon_placement="left" font_icon="" use_icon="on" use_circle="off" use_circle_border="off" icon_color="#7c4dd5" circle_color="#7c4dd5" circle_border_color="#2caaca"]Vestibulum lobortis. Donec at euismod nibh, eu bibendum quam.[/et_pb_blurb][/et_pb_column][et_pb_column type="1_3"][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum Dolor" url_new_window="off" animation="off" background_layout="light" text_orientation="left" icon_placement="left" font_icon="" use_icon="on" use_circle="off" use_circle_border="off" icon_color="#7c4dd5" circle_color="#7c4dd5" circle_border_color="#2caaca"]Vestibulum lobortis. Donec at euismod nibh, eu bibendum quam.[/et_pb_blurb][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" background_color="#27c9b8" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="4_4"][et_pb_cta admin_label="Call To Action" button_url="#" button_text="Get Started" background_color="#7ebec5" use_background_color="off" background_layout="dark" text_orientation="center"]</p><h1>Building a website has never been so fun.</h1><p>[/et_pb_cta][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" background_color="#27323a" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="4_4"][et_pb_text admin_label="Text" background_layout="dark" text_orientation="center"]</p><h1>Lorem Ipsum Dolor.</h1><p>Vestibulum lobortis. Donec at euismod nibh, eu bibendum quam. Nullam non gravida purus dolor ipsum amet sit. Nec  eleifend tincidunt nisi.Vestibulum lobortis. Donec at euismod nibh, eu bibendum quam.</p><p>[/et_pb_text][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="4_4"][et_pb_image admin_label="Image" src="https://elegantthemesimages.com/images/premade/d2-placeholder-1080px.jpg" url_new_window="off" animation="right" show_in_lightbox="off" /][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="1_3"][et_pb_text admin_label="Text" background_layout="dark" text_orientation="left"]</p><h3>Lorem Ipsum</h3><p><span style="color: #bbbbbb;">Vestibulum lobortis. Donec at euismod nibh, eu ibendum quam. Nullam non gravida puruipsum amet sdum it. Nec ele bulum lobortis. Donec at euismod nibh, eu biben</span></p><p>[/et_pb_text][/et_pb_column][et_pb_column type="1_3"][et_pb_text admin_label="Text" background_layout="dark" text_orientation="left"]</p><h3>Lorem Ipsum</h3><p><span style="color: #bbbbbb;">Vestibulum lobortis. Donec at euismod nibh, eu ibendum quam. Nullam non gravida puruipsum amet sdum it. Nec ele bulum lobortis. Donec at euismod nibh, eu biben</span></p><p>[/et_pb_text][/et_pb_column][et_pb_column type="1_3"][et_pb_text admin_label="Text" background_layout="dark" text_orientation="left"]</p><h3>Lorem Ipsum</h3><p><span style="color: #bbbbbb;">Vestibulum lobortis. Donec at euismod nibh, eu ibendum quam. Nullam non gravida puruipsum amet sdum it. Nec ele bulum lobortis. Donec at euismod nibh, eu biben</span></p><p>[/et_pb_text][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" background_color="#22262e" inner_shadow="off" parallax="on"][et_pb_row][et_pb_column type="1_4"][et_pb_number_counter admin_label="Number Counter" title="Lorem Ipsum" number="2700" percent_sign="off" counter_color="#815ab4" background_layout="dark" /][/et_pb_column][et_pb_column type="1_4"][et_pb_number_counter admin_label="Number Counter" title="Lorem Ipsum" number="30" percent_sign="off" counter_color="#2caaca" background_layout="dark" /][/et_pb_column][et_pb_column type="1_4"][et_pb_number_counter admin_label="Number Counter" title="Lorem Ipsum" number="87" percent_sign="off" counter_color="#35bbaa" background_layout="dark" /][/et_pb_column][et_pb_column type="1_4"][et_pb_number_counter admin_label="Number Counter" title="Lorem Ipsum" number="999" percent_sign="off" counter_color="#ef6462" background_layout="dark" /][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="on"][et_pb_fullwidth_portfolio admin_label="Fullwidth Portfolio" fullwidth="on" show_title="on" show_date="on" background_layout="dark" auto="on" /][et_pb_fullwidth_slider admin_label="Fullwidth Slider" show_arrows="on" show_pagination="on" auto="off" parallax="off"][et_pb_slide heading="Slide Title Here" button_text="Shop Now" button_link="https://elegantthemes.com/preview/Divi2/shop-extended/" background_color="#1a86cf" alignment="center" background_layout="dark" image="https://elegantthemesimages.com/images/premade/d2-placeholder-510px.png"]Lorem ipsum dolor sit amet, consectetur adipiscing elit. In in risus eget lectus suscipit malesuada. Maecenas ut urna mollis, aliquam eros at, laoreet metus.[/et_pb_slide][et_pb_slide heading="Slide Title Here" alignment="center" background_layout="dark" background_image="https://elegantthemesimages.com/images/premade/d2-placeholder-1920.png" background_color="#ffffff"]Lorem ipsum dolor sit amet, consectetur adipiscing elit. In in risus eget lectus suscipit malesuada. Maecenas ut urna mollis, aliquam er
os at, laoreet metus.[/et_pb_slide][/et_pb_fullwidth_slider][/et_pb_section][et_pb_section][et_pb_row][et_pb_column type="4_4"][et_pb_text admin_label="Text" background_layout="light" text_orientation="center"]</p><h1>Core Features</h1><p>[/et_pb_text][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="1_3"][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum Dolor" url_new_window="off" image="https://elegantthemesimages.com/images/premade/builder-blurbs-builder.jpg" animation="bottom" background_layout="light" text_orientation="center" use_icon="off" icon_color="#2caaca" use_circle="off" circle_color="#2caaca" use_circle_border="off" circle_border_color="#2caaca" icon_placement="top"]Donec at euismod nibh, eu bibendum quam. Nullam non gravida purus, nec  eleifend tincidunt nisi. Fusce at purus in massa laoreet[/et_pb_blurb][/et_pb_column][et_pb_column type="1_3"][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum Dolor" url_new_window="off" image="https://elegantthemesimages.com/images/premade/builder-blurbs-layouts.jpg" animation="bottom" background_layout="light" text_orientation="center" use_icon="off" icon_color="#2caaca" use_circle="off" circle_color="#2caaca" use_circle_border="off" circle_border_color="#2caaca" icon_placement="top" url="https://elegantthemes.com/preview/Divi2/features/#predefined"]Donec at euismod nibh, eu bibendum quam. Nullam non gravida purus, nec  eleifend tincidunt nisi. Fusce at purus in massa laoreet[/et_pb_blurb][/et_pb_column][et_pb_column type="1_3"][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum Dolor" url_new_window="off" image="https://elegantthemesimages.com/images/premade/builder-blurbs-export.jpg" animation="bottom" background_layout="light" text_orientation="center" use_icon="off" icon_color="#2caaca" use_circle="off" circle_color="#2caaca" use_circle_border="off" circle_border_color="#2caaca" icon_placement="top" url="https://elegantthemes.com/preview/Divi2/features/#layouts"]Donec at euismod nibh, eu bibendum quam. Nullam non gravida purus, nec  eleifend tincidunt nisi. Fusce at purus in massa laoreet[/et_pb_blurb][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="1_3"][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum Dolor" url_new_window="off" image="https://elegantthemesimages.com/images/premade/builder-blurbs-modules.jpg" animation="bottom" background_layout="light" text_orientation="center" icon_placement="top" use_icon="off" use_circle="off" use_circle_border="off" icon_color="#2caaca" circle_color="#2caaca" circle_border_color="#2caaca"]Donec at euismod nibh, eu bibendum quam. Nullam non gravida purus, nec  eleifend tincidunt nisi. Fusce at purus in massa laoreet[/et_pb_blurb][/et_pb_column][et_pb_column type="1_3"][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum Dolor" url_new_window="off" image="https://elegantthemesimages.com/images/premade/builder-blurbs-mobile.jpg" animation="bottom" background_layout="light" text_orientation="center" use_icon="off" icon_color="#2caaca" use_circle="off" circle_color="#2caaca" use_circle_border="off" circle_border_color="#2caaca" icon_placement="top" url="https://elegantthemes.com/preview/Divi2/features/#mobile"]Donec at euismod nibh, eu bibendum quam. Nullam non gravida purus, nec  eleifend tincidunt nisi. Fusce at purus in massa laoreet[/et_pb_blurb][/et_pb_column][et_pb_column type="1_3"][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum Dolor" url_new_window="off" image="https://elegantthemesimages.com/images/premade/builder-blurbs-commerce.jpg" animation="bottom" background_layout="light" text_orientation="center" use_icon="off" icon_color="#2caaca" use_circle="off" circle_color="#2caaca" use_circle_border="off" circle_border_color="#2caaca" icon_placement="top"]Donec at euismod nibh, eu bibendum quam. Nullam non gravida purus, nec  eleifend tincidunt nisi. Fusce at purus in massa laoreet[/et_pb_blurb][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="on"][et_pb_fullwidth_slider admin_label="Fullwidth Slider" show_arrows="on" show_pagination="on" auto="off" parallax="on"][et_pb_slide heading="Slide Title Here" button_text="Our Work" button_link="#" background_image="https://elegantthemesimages.com/images/premade/d2-placeholder-1920.png" background_color="#ffffff" alignment="center" background_layout="dark"]Vestibulum lobortis. Donec at euismod nibh, eu bibendum quam. Nullam non gravida purus, nec  eleifend tincidunt nisi.Vestibulum lobortis. Donec at euismod nibh, eu bibendum quam. Nullam non gravida purus, nec  eleifend tincidunt nisi.[/et_pb_slide][/et_pb_fullwidth_slider][/et_pb_section][et_pb_section fullwidth="off" background_color="#283139" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="4_4"][et_pb_text admin_label="Text" background_layout="dark" text_orientation="center"]</p><h1>Versatile Layout Options</h1><p>Vestibulum lobortis. Donec at euismod nibh, eu bibendum quam. Nullam non gravida purus dolor ipsum amet sit.</p><p>[/et_pb_text][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="1_3"][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum Dolor" url_new_window="off" icon_placement="left" font_icon="R" use_icon="on" use_circle="off" use_circle_border="off" icon_color="#ec6d5f" circle_color="#2caaca" circle_border_color="#2caaca" animation="bottom" background_layout="dark" text_orientation="center"]<span style="color: #bbbbbb;">Donec at euismod nibh, eu bibendum.[/et_pb_blurb][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum Dolor" url_new_window="off" icon_placement="left" font_icon="R" use_icon="on" use_circle="off" use_circle_border="off" icon_color="#1fa0e3" circle_color="#2caaca" circle_border_color="#2caaca" animation="right" background_layout="dark" text_orientation="center"]<span style="color: #bbbbbb;">Donec at euismod nibh, eu bibendum.[/et_pb_blurb][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum Dolor" url_new_window="off" icon_placement="left" font_icon="R" use_icon="on" use_circle="off" use_circle_border="off" icon_color="#47bfa4" circle_color="#2caaca" circle_border_color="#2caaca" animation="top" background_layout="dark" text_orientation="center"]<span style="color: #bbbbbb;">Donec at euismod nibh, eu bibendum.[/et_pb_blurb][/et_pb_column][et_pb_column type="1_3"][et_pb_image admin_label="Image" src="https://elegantthemesimages.com/images/premade/d2-placeholder-320px.jpg" url_new_window="off" animation="bottom" show_in_lightbox="off" /][/et_pb_column][et_pb_column type="1_3"][et_pb_image admin_label="Image" src="https://elegantthemesimages.com/images/premade/d2-placeholder-320px.jpg" url_new_window="off" animation="bottom" show_in_lightbox="off" /][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" background_color="#ec6d5f" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="4_4"][et_pb_cta admin_label="Call To Action" background_color="#7ebec5" use_background_color="off" background_layout="dark" text_orientation="center" button_url="#" button_text="Join Now"]</p><h1>Look No Further. Get Started Today</h1><p>[/et_pb_cta][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
	);


	$layouts[] = array(
		'name'    => esc_html__( 'Page Fullwidth', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section fullwidth="on" specialty="off" inner_shadow="off" parallax="off" background_color="#2ea3f2"][et_pb_fullwidth_header admin_label="Fullwidth Header" title="Page Title" subhead="Here is a basic page layout with no sidebar" background_layout="dark" text_orientation="left" /][/et_pb_section][et_pb_section][et_pb_row][et_pb_column type="4_4"][et_pb_text admin_label="Text"]
<h2>Just A Standard Page</h2>
Nunc et vestibulum velit. Suspendisse euismod eros vel urna bibendum gravida. Phasellus et metus nec dui ornare molestie. In consequat urna sed tincidunt euismod. Praesent non pharetra arcu, at tincidunt sapien. Nullam lobortis ultricies bibendum. Duis elit leo, porta vel nisl in, ullamcorper scelerisque velit. Fusce volutpat purus dolor, vel pulvinar dui porttitor sed. Phasellus ac odio eu quam varius elementum sit amet euismod justo.

Sed sit amet blandit ipsum, et consectetur libero. Integer convallis at metus quis molestie. Morbi vitae odio ut ante molestie scelerisque. Aliquam erat volutpat. Vivamus dignissim fringilla semper. Aliquam imperdiet dui a purus pellentesque, non ornare ipsum blandit. Sed imperdiet elit in quam egestas lacinia nec sit amet dui. Cras malesuada tincidunt ante, in luctus tellus hendrerit at. Duis massa mauris, bibendum a mollis a, laoreet quis elit. Nulla pulvinar vestibulum est, in viverra nisi malesuada vel. Nam ut ipsum quis est faucibus mattis eu ut turpis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas nunc felis, venenatis in fringilla vel, tempus in turpis. Mauris aliquam dictum dolor at varius. Fusce sed vestibulum metus. Vestibulum dictum ultrices nulla sit amet fermentum.

[/et_pb_text][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="1_2"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]
<h3>Lorem Ipsum Dolor</h3>
Nunc et vestibulum velit. Suspendisse euismod eros vel urna bibendum gravida. Phasellus et metus nec dui ornare molestie. In consequat urna sed tincidunt euismod. Praesent non pharetra arcu, at tincidunt sapien. Nullam lobortis ultricies bibendum. Duis elit leo, porta vel nisl in, ullamcorper scelerisque velit. Fusce volutpat purus dolor, vel pulvinar dui porttitor sed. Phasellus ac odio eu quam varius elementum sit amet euismod justo.

[/et_pb_text][/et_pb_column][et_pb_column type="1_2"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]
<h3>Lorem Ipsum Dolor</h3>
Nunc et vestibulum velit. Suspendisse euismod eros vel urna bibendum gravida. Phasellus et metus nec dui ornare molestie. In consequat urna sed tincidunt euismod. Praesent non pharetra arcu, at tincidunt sapien. Nullam lobortis ultricies bibendum. Duis elit leo, porta vel nisl in, ullamcorper scelerisque velit. Fusce volutpat purus dolor, vel pulvinar dui porttitor sed. Phasellus ac odio eu quam varius elementum sit amet euismod justo.

[/et_pb_text][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="1_3"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]
<h4>Lorem Ipsum Dolor</h4>
Nunc et vestibulum velit. Suspendisse euismod eros vel urna bibendum gravida. Phasellus et metus nec dui ornare molestie. In consequat urna sed tincidunt euismod. Praesent non pharetra arcu, at tincidunt sapien. Nullam lobortis ultricies bibendum. Duis elit leo, porta vel nisl in, ullamcorper scelerisque velit. Fusce volutpat purus dolor, vel pulvinar dui porttitor sed. Phasellus ac odio eu quam varius elementum sit amet euismod justo.

[/et_pb_text][/et_pb_column][et_pb_column type="1_3"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]
<h4>Lorem Ipsum Dolor</h4>
Nunc et vestibulum velit. Suspendisse euismod eros vel urna bibendum gravida. Phasellus et metus nec dui ornare molestie. In consequat urna sed tincidunt euismod. Praesent non pharetra arcu, at tincidunt sapien. Nullam lobortis ultricies bibendum. Duis elit leo, porta vel nisl in, ullamcorper scelerisque velit. Fusce volutpat purus dolor, vel pulvinar dui porttitor sed. Phasellus ac odio eu quam varius elementum sit amet euismod justo.

[/et_pb_text][/et_pb_column][et_pb_column type="1_3"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]
<h4>Lorem Ipsum Dolor</h4>
Nunc et vestibulum velit. Suspendisse euismod eros vel urna bibendum gravida. Phasellus et metus nec dui ornare molestie. In consequat urna sed tincidunt euismod. Praesent non pharetra arcu, at tincidunt sapien. Nullam lobortis ultricies bibendum. Duis elit leo, porta vel nisl in, ullamcorper scelerisque velit. Fusce volutpat purus dolor, vel pulvinar dui porttitor sed. Phasellus ac odio eu quam varius elementum sit amet euismod justo.

[/et_pb_text][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
	);


	$layouts[] = array(
		'name'    => esc_html__( 'Page Right Sidebar', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section fullwidth="on" specialty="off" background_color="#2ea3f2" inner_shadow="on" parallax="off"][et_pb_fullwidth_header admin_label="Fullwidth Header" title="Page Title" subhead="Here is a basic page layout with a right sidebar" background_layout="dark" text_orientation="left" /][/et_pb_section][et_pb_section fullwidth="off" specialty="on"][et_pb_column type="3_4" specialty_columns="3"][et_pb_row_inner][et_pb_column_inner type="4_4"][et_pb_text admin_label="Text"]
<h2>Just A Standard Page</h2>
Nunc et vestibulum velit. Suspendisse euismod eros vel urna bibendum gravida. Phasellus et metus nec dui ornare molestie. In consequat urna sed tincidunt euismod. Praesent non pharetra arcu, at tincidunt sapien. Nullam lobortis ultricies bibendum. Duis elit leo, porta vel nisl in, ullamcorper scelerisque velit. Fusce volutpat purus dolor, vel pulvinar dui porttitor sed. Phasellus ac odio eu quam varius elementum sit amet euismod justo.

Sed sit amet blandit ipsum, et consectetur libero. Integer convallis at metus quis molestie. Morbi vitae odio ut ante molestie scelerisque. Aliquam erat volutpat. Vivamus dignissim fringilla semper. Aliquam imperdiet dui a purus pellentesque, non ornare ipsum blandit. Sed imperdiet elit in quam egestas lacinia nec sit amet dui. Cras malesuada tincidunt ante, in luctus tellus hendrerit at. Duis massa mauris, bibendum a mollis a, laoreet quis elit. Nulla pulvinar vestibulum est, in viverra nisi malesuada vel. Nam ut ipsum quis est faucibus mattis eu ut turpis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas nunc felis, venenatis in fringilla vel, tempus in turpis. Mauris aliquam dictum dolor at varius. Fusce sed vestibulum metus. Vestibulum dictum ultrices nulla sit amet fermentum.

[/et_pb_text][/et_pb_column_inner][/et_pb_row_inner][et_pb_row_inner][et_pb_column_inner type="1_2"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]
<h3>Lorem Ipsum Dolor</h3>
Nunc et vestibulum velit. Suspendisse euismod eros vel urna bibendum gravida. Phasellus et metus nec dui ornare molestie. In consequat urna sed tincidunt euismod. Praesent non pharetra arcu, at tincidunt sapien. Nullam lobortis ultricies bibendum. Duis elit leo, porta vel nisl in, ullamcorper scelerisque velit. Fusce volutpat purus dolor, vel pulvinar dui porttitor sed. Phasellus ac odio eu quam varius elementum sit amet euismod justo.

[/et_pb_text][/et_pb_column_inner][et_pb_column_inner type="1_2"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]
<h3>Lorem Ipsum Dolor</h3>
Nunc et vestibulum velit. Suspendisse euismod eros vel urna bibendum gravida. Phasellus et metus nec dui ornare molestie. In consequat urna sed tincidunt euismod. Praesent non pharetra arcu, at tincidunt sapien. Nullam lobortis ultricies bibendum. Duis elit leo, porta vel nisl in, ullamcorper scelerisque velit. Fusce volutpat purus dolor, vel pulvinar dui porttitor sed. Phasellus ac odio eu quam varius elementum sit amet euismod justo.

[/et_pb_text][/et_pb_column_inner][/et_pb_row_inner][et_pb_row_inner][et_pb_column_inner type="1_3"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]
<h4>Lorem Ipsum Dolor</h4>
Nunc et vestibulum velit. Suspendisse euismod eros vel urna bibendum gravida. Phasellus et metus nec dui ornare molestie. In consequat urna sed tincidunt euismod. Praesent non pharetra arcu, at tincidunt sapien. Nullam lobortis ultricies bibendum. Duis elit leo, porta vel nisl in, ullamcorper scelerisque velit. Fusce volutpat purus dolor, vel pulvinar dui porttitor sed. Phasellus ac odio eu quam varius elementum sit amet euismod justo.

[/et_pb_text][/et_pb_column_inner][et_pb_column_inner type="1_3"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]
<h4>Lorem Ipsum Dolor</h4>
Nunc et vestibulum velit. Suspendisse euismod eros vel urna bibendum gravida. Phasellus et metus nec dui ornare molestie. In consequat urna sed tincidunt euismod. Praesent non pharetra arcu, at tincidunt sapien. Nullam lobortis ultricies bibendum. Duis elit leo, porta vel nisl in, ullamcorper scelerisque velit. Fusce volutpat purus dolor, vel pulvinar dui porttitor sed. Phasellus ac odio eu quam varius elementum sit amet euismod justo.

[/et_pb_text][/et_pb_column_inner][et_pb_column_inner type="1_3"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]
<h4>Lorem Ipsum Dolor</h4>
Nunc et vestibulum velit. Suspendisse euismod eros vel urna bibendum gravida. Phasellus et metus nec dui ornare molestie. In consequat urna sed tincidunt euismod. Praesent non pharetra arcu, at tincidunt sapien. Nullam lobortis ultricies bibendum. Duis elit leo, porta vel nisl in, ullamcorper scelerisque velit. Fusce volutpat purus dolor, vel pulvinar dui porttitor sed. Phasellus ac odio eu quam varius elementum sit amet euismod justo.

[/et_pb_text][/et_pb_column_inner][/et_pb_row_inner][/et_pb_column][et_pb_column type="1_4"][et_pb_sidebar admin_label="Sidebar" orientation="right" background_layout="light" /][/et_pb_column][/et_pb_section]
EOT
	);


	$layouts[] = array(
		'name'    => esc_html__( 'Page Left Sidebar', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section fullwidth="on" specialty="off" background_color="#2ea3f2" inner_shadow="off" parallax="off"][et_pb_fullwidth_header admin_label="Fullwidth Header" title="Page With Left Sidebar" subhead="Here is a basic page layout with a left sidebar" background_layout="dark" text_orientation="left" /][/et_pb_section][et_pb_section fullwidth="off" specialty="on"][et_pb_column type="1_4"][et_pb_sidebar admin_label="Sidebar" orientation="left" background_layout="light" /][/et_pb_column][et_pb_column type="3_4" specialty_columns="3"][et_pb_row_inner][et_pb_column_inner type="4_4"][et_pb_text admin_label="Text"]
<h2>Just A Standard Page</h2>
Nunc et vestibulum velit. Suspendisse euismod eros vel urna bibendum gravida. Phasellus et metus nec dui ornare molestie. In consequat urna sed tincidunt euismod. Praesent non pharetra arcu, at tincidunt sapien. Nullam lobortis ultricies bibendum. Duis elit leo, porta vel nisl in, ullamcorper scelerisque velit. Fusce volutpat purus dolor, vel pulvinar dui porttitor sed. Phasellus ac odio eu quam varius elementum sit amet euismod justo.

Sed sit amet blandit ipsum, et consectetur libero. Integer convallis at metus quis molestie. Morbi vitae odio ut ante molestie scelerisque. Aliquam erat volutpat. Vivamus dignissim fringilla semper. Aliquam imperdiet dui a purus pellentesque, non ornare ipsum blandit. Sed imperdiet elit in quam egestas lacinia nec sit amet dui. Cras malesuada tincidunt ante, in luctus tellus hendrerit at. Duis massa mauris, bibendum a mollis a, laoreet quis elit. Nulla pulvinar vestibulum est, in viverra nisi malesuada vel. Nam ut ipsum quis est faucibus mattis eu ut turpis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas nunc felis, venenatis in fringilla vel, tempus in turpis. Mauris aliquam dictum dolor at varius. Fusce sed vestibulum metus. Vestibulum dictum ultrices nulla sit amet fermentum.

[/et_pb_text][/et_pb_column_inner][/et_pb_row_inner][et_pb_row_inner][et_pb_column_inner type="1_2"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]
<h3>Lorem Ipsum Dolor</h3>
Nunc et vestibulum velit. Suspendisse euismod eros vel urna bibendum gravida. Phasellus et metus nec dui ornare molestie. In consequat urna sed tincidunt euismod. Praesent non pharetra arcu, at tincidunt sapien. Nullam lobortis ultricies bibendum. Duis elit leo, porta vel nisl in, ullamcorper scelerisque velit. Fusce volutpat purus dolor, vel pulvinar dui porttitor sed. Phasellus ac odio eu quam varius elementum sit amet euismod justo.

[/et_pb_text][/et_pb_column_inner][et_pb_column_inner type="1_2"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]
<h3>Lorem Ipsum Dolor</h3>
Nunc et vestibulum velit. Suspendisse euismod eros vel urna bibendum gravida. Phasellus et metus nec dui ornare molestie. In consequat urna sed tincidunt euismod. Praesent non pharetra arcu, at tincidunt sapien. Nullam lobortis ultricies bibendum. Duis elit leo, porta vel nisl in, ullamcorper scelerisque velit. Fusce volutpat purus dolor, vel pulvinar dui porttitor sed. Phasellus ac odio eu quam varius elementum sit amet euismod justo.

[/et_pb_text][/et_pb_column_inner][/et_pb_row_inner][et_pb_row_inner][et_pb_column_inner type="1_3"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]
<h4>Lorem Ipsum Dolor</h4>
Nunc et vestibulum velit. Suspendisse euismod eros vel urna bibendum gravida. Phasellus et metus nec dui ornare molestie. In consequat urna sed tincidunt euismod. Praesent non pharetra arcu, at tincidunt sapien. Nullam lobortis ultricies bibendum. Duis elit leo, porta vel nisl in, ullamcorper scelerisque velit. Fusce volutpat purus dolor, vel pulvinar dui porttitor sed. Phasellus ac odio eu quam varius elementum sit amet euismod justo.

[/et_pb_text][/et_pb_column_inner][et_pb_column_inner type="1_3"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]
<h4>Lorem Ipsum Dolor</h4>
Nunc et vestibulum velit. Suspendisse euismod eros vel urna bibendum gravida. Phasellus et metus nec dui ornare molestie. In consequat urna sed tincidunt euismod. Praesent non pharetra arcu, at tincidunt sapien. Nullam lobortis ultricies bibendum. Duis elit leo, porta vel nisl in, ullamcorper scelerisque velit. Fusce volutpat purus dolor, vel pulvinar dui porttitor sed. Phasellus ac odio eu quam varius elementum sit amet euismod justo.

[/et_pb_text][/et_pb_column_inner][et_pb_column_inner type="1_3"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]
<h4>Lorem Ipsum Dolor</h4>
Nunc et vestibulum velit. Suspendisse euismod eros vel urna bibendum gravida. Phasellus et metus nec dui ornare molestie. In consequat urna sed tincidunt euismod. Praesent non pharetra arcu, at tincidunt sapien. Nullam lobortis ultricies bibendum. Duis elit leo, porta vel nisl in, ullamcorper scelerisque velit. Fusce volutpat purus dolor, vel pulvinar dui porttitor sed. Phasellus ac odio eu quam varius elementum sit amet euismod justo.

[/et_pb_text][/et_pb_column_inner][/et_pb_row_inner][/et_pb_column][/et_pb_section]
EOT
	);


	$layouts[] = array(
		'name'    => esc_html__( 'Page Dual Sidebars', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section fullwidth="on" specialty="off" background_color="#2ea3f2" inner_shadow="off" parallax="off"][et_pb_fullwidth_header admin_label="Fullwidth Header" title="Page With Dual Sidebars" subhead="Here is a basic page layout with dual sidebars" background_layout="dark" text_orientation="left" /][/et_pb_section][et_pb_section fullwidth="off" specialty="on"][et_pb_column type="1_4"][et_pb_sidebar admin_label="Sidebar" orientation="left" background_layout="light" /][/et_pb_column][et_pb_column type="1_2" specialty_columns="2"][et_pb_row_inner][et_pb_column_inner type="4_4"][et_pb_text admin_label="Text"]
<h2>Just A Standard Page</h2>
Nunc et vestibulum velit. Suspendisse euismod eros vel urna bibendum gravida. Phasellus et metus nec dui ornare molestie. In consequat urna sed tincidunt euismod. Praesent non pharetra arcu, at tincidunt sapien. Nullam lobortis ultricies bibendum. Duis elit leo, porta vel nisl in, ullamcorper scelerisque velit. Fusce volutpat purus dolor, vel pulvinar dui porttitor sed. Phasellus ac odio eu quam varius elementum sit amet euismod justo.

Sed sit amet blandit ipsum, et consectetur libero. Integer convallis at metus quis molestie. Morbi vitae odio ut ante molestie scelerisque. Aliquam erat volutpat. Vivamus dignissim fringilla semper. Aliquam imperdiet dui a purus pellentesque, non ornare ipsum blandit. Sed imperdiet elit in quam egestas lacinia nec sit amet dui. Cras malesuada tincidunt ante, in luctus tellus hendrerit at. Duis massa mauris, bibendum a mollis a, laoreet quis elit. Nulla pulvinar vestibulum est, in viverra nisi malesuada vel. Nam ut ipsum quis est faucibus mattis eu ut turpis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas nunc felis, venenatis in fringilla vel, tempus in turpis. Mauris aliquam dictum dolor at varius. Fusce sed vestibulum metus. Vestibulum dictum ultrices nulla sit amet fermentum.

[/et_pb_text][/et_pb_column_inner][/et_pb_row_inner][et_pb_row_inner][et_pb_column_inner type="1_2"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]
<h3>Lorem Ipsum Dolor</h3>
Nunc et vestibulum velit. Suspendisse euismod eros vel urna bibendum gravida. Phasellus et metus nec dui ornare molestie. In consequat urna sed tincidunt euismod. Praesent non pharetra arcu, at tincidunt sapien. Nullam lobortis ultricies bibendum. Duis elit leo, porta vel nisl in, ullamcorper scelerisque velit. Fusce volutpat purus dolor, vel pulvinar dui porttitor sed. Phasellus ac odio eu quam varius elementum sit amet euismod justo.

[/et_pb_text][/et_pb_column_inner][et_pb_column_inner type="1_2"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]
<h3>Lorem Ipsum Dolor</h3>
Nunc et vestibulum velit. Suspendisse euismod eros vel urna bibendum gravida. Phasellus et metus nec dui ornare molestie. In consequat urna sed tincidunt euismod. Praesent non pharetra arcu, at tincidunt sapien. Nullam lobortis ultricies bibendum. Duis elit leo, porta vel nisl in, ullamcorper scelerisque velit. Fusce volutpat purus dolor, vel pulvinar dui porttitor sed. Phasellus ac odio eu quam varius elementum sit amet euismod justo.

[/et_pb_text][/et_pb_column_inner][/et_pb_row_inner][/et_pb_column][et_pb_column type="1_4"][et_pb_sidebar admin_label="Sidebar" orientation="right" background_layout="light" /][/et_pb_column][/et_pb_section]
EOT
	);


	$layouts[] = array(
		'name'    => esc_html__( 'Portfolio Grid', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section fullwidth="on" specialty="off" background_image="https://elegantthemesimages.com/images/premade/d2-placeholder-1920.png" inner_shadow="off" parallax="off"][et_pb_fullwidth_header admin_label="Fullwidth Header" title="My Work" subhead="Your Subtitle Goes Here" background_layout="dark" text_orientation="left" /][/et_pb_section][et_pb_section inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="4_4"][et_pb_filterable_portfolio admin_label="Filterable Portfolio" fullwidth="off" posts_number="12" show_title="on" show_categories="on" show_pagination="off" background_layout="light" /][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_color="#f7f7f7" inner_shadow="on" parallax="off"][et_pb_row][et_pb_column type="4_4"][et_pb_cta admin_label="Call To Action" title="Like What You See?" button_url="#" button_text="Contact Me" use_background_color="off" background_color="#108bf5" background_layout="light" text_orientation="center" /][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
	);


	$layouts[] = array(
		'name'    => esc_html__( 'Portfolio 1 Column', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section fullwidth="on" specialty="off" background_image="https://elegantthemesimages.com/images/premade/d2-placeholder-1920.png" inner_shadow="off" parallax="on"][et_pb_fullwidth_header admin_label="Fullwidth Header" title="My Work" subhead="Your Subtitle Goes Here" background_layout="dark" text_orientation="left" /][/et_pb_section][et_pb_section fullwidth="off" specialty="off"][et_pb_row][et_pb_column type="4_4"][et_pb_portfolio admin_label="Portfolio" fullwidth="on" posts_number="4" show_title="on" show_categories="on" show_pagination="on" background_layout="light" /][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_color="#2ea3f2" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="4_4"][et_pb_cta admin_label="Call To Action" title="I Love Working With Creative Minds" button_url="#" button_text="Contact Me" background_color="#2caaca" use_background_color="off" background_layout="dark" text_orientation="center"]If you are interested in working together, send me an inquiry and I will get back to you as soon as I can![/et_pb_cta][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
	);


	$layouts[] = array(
		'name'    => esc_html__( 'Portfolio Fullwidth Carousel', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section fullwidth="on" specialty="off"][et_pb_fullwidth_slider admin_label="Fullwidth Slider" show_arrows="on" show_pagination="on" auto="on" parallax="off"][et_pb_slide background_image="https://elegantthemesimages.com/images/premade/d2-placeholder-1920.png" background_color="#ffffff" alignment="center" background_layout="dark" /][et_pb_slide background_image="https://elegantthemesimages.com/images/premade/d2-placeholder-1920.png" background_color="#ffffff" alignment="center" background_layout="dark" /][et_pb_slide background_image="https://elegantthemesimages.com/images/premade/d2-placeholder-1920.png" background_color="#ffffff" alignment="center" background_layout="dark" /][/et_pb_fullwidth_slider][et_pb_fullwidth_portfolio admin_label="Fullwidth Portfolio" fullwidth="on" show_title="on" show_date="on" background_layout="light" auto="off" /][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_color="#2ea3f2" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="4_4"][et_pb_cta admin_label="Call To Action" title="Let's Build Something Together" button_url="#" button_text="Contact Me" use_background_color="off" background_color="#2ea3f2" background_layout="dark" text_orientation="center" /][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
	);


	$layouts[] = array(
		'name'    => esc_html__( 'Portfolio Fullwidth Grid', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section fullwidth="on" specialty="off" background_color="#2ea3f2" inner_shadow="off" parallax="off"][et_pb_fullwidth_portfolio admin_label="Fullwidth Portfolio" fullwidth="off" show_title="on" show_date="on" background_layout="dark" auto="off" /][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_color="#2ea3f2" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="4_4"][et_pb_cta admin_label="Call To Action" title="Interested In Working On A Project?" button_url="#" button_text="Contact Me" use_background_color="off" background_color="#2ea3f2" background_layout="dark" text_orientation="center" /][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
	);


	$layouts[] = array(
		'name'    => esc_html__( 'Project Extended', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section background_color="#3a3a3a" inner_shadow="off" parallax="on"][et_pb_row][et_pb_column type="4_4"][et_pb_blurb admin_label="Blurb" url_new_window="off" image="https://elegantthemesimages.com/images/premade/d2-placeholder-320px.jpg" animation="bottom" background_layout="light" text_orientation="center" use_icon="off" icon_color="#45c4ec" use_circle="off" circle_color="#45c4ec" use_circle_border="off" circle_border_color="#45c4ec" icon_placement="top" /][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="4_4"][et_pb_text admin_label="Text" background_layout="dark" text_orientation="center"]<h1 style="font-size: 72px; font-weight: 300;">Your Project Name</h1>[/et_pb_text][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="1_2"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]<h2>The Challenge</h2>
Vivamus ipsum velit, ullamcorper quis nibh non, molestie tempus sapien. Mauris ultrices, felis ut eleifend auctor, leo felis vehicula quam, ut accumsan augue nunc at nisl. Vivamus ipsum velit, ullamcorper quis nibh non, molestie tempus sapien. Mauris ultrices, felis ut eleifend auctor, leo felis vehicula quam, ut accumsan augue nunc at nisl.[/et_pb_text][/et_pb_column][et_pb_column type="1_2"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]<h2>The Solution</h2>
Vivamus ipsum velit, ullamcorper quis nibh non, molestie tempus sapien. Mauris ultrices, felis ut eleifend auctor, leo felis vehicula quam, ut accumsan augue nunc at nisl. Vivamus ipsum velit, ullamcorper quis nibh non, molestie tempus sapien. Mauris ultrices, felis ut eleifend auctor, leo felis vehicula quam, ut accumsan augue nunc at nisl.[/et_pb_text][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="on" specialty="off" inner_shadow="off" parallax="on"][et_pb_fullwidth_slider admin_label="Fullwidth Slider" show_arrows="on" show_pagination="on" auto="off" parallax="on"][et_pb_slide heading="Complete Corporate Identity" background_image="https://elegantthemesimages.com/images/premade/d2-placeholder-1920.png" background_color="#ffffff" alignment="center" background_layout="dark" /][et_pb_slide heading="We Rethought Everything" background_color="#2ea3f2" alignment="center" background_layout="dark" /][/et_pb_fullwidth_slider][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_color="#353535" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="1_4"][et_pb_number_counter admin_label="Number Counter" title="Corporate Rebranding" number="70" percent_sign="on" background_layout="dark" counter_color="#2ea3f2" /][/et_pb_column][et_pb_column type="1_4"][et_pb_number_counter admin_label="Number Counter" title="Website Redesign" number="30" percent_sign="on" background_layout="dark" /][/et_pb_column][et_pb_column type="1_4"][et_pb_number_counter admin_label="Number Counter" title="Day Turnaround" number="60" percent_sign="off" background_layout="dark" /][/et_pb_column][et_pb_column type="1_4"][et_pb_number_counter admin_label="Number Counter" title="Amazing Result" number="1" percent_sign="off" background_layout="dark" /][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_color="#2ea3f2" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="1_2"][et_pb_divider admin_label="Divider" color="#ffffff" show_divider="off" height="90" /][et_pb_text admin_label="Text" background_layout="dark" text_orientation="left"]<h1>Mobile Site Boosted Sales By 50%</h1>[/et_pb_text][et_pb_blurb admin_label="Blurb" title="Mobile Refresh" url_new_window="off" use_icon="on" font_icon="" icon_color="#ffffff" use_circle="off" circle_color="#2caaca" use_circle_border="off" circle_border_color="#2caaca" icon_placement="left" animation="right" background_layout="dark" text_orientation="left"]The Challenge Vivamus ipsum velit, ullamcorper quis nibh non, molestie tempus sapien. Mauris ultrices, felis ut eleifend auctor[/et_pb_blurb][et_pb_blurb admin_label="Blurb" title="Rebuilt From the Inside Out" url_new_window="off" use_icon="on" font_icon="" icon_color="#ffffff" use_circle="off" circle_color="#2caaca" use_circle_border="off" circle_border_color="#2caaca" icon_placement="left" animation="right" background_layout="dark" text_orientation="left"]The Challenge Vivamus ipsum velit, ullamcorper quis nibh non, molestie tempus sapien. Mauris ultrices, felis ut eleifend auctor[/et_pb_blurb][et_pb_blurb admin_label="Blurb" title="Extensive Demographic Studies" url_new_window="off" use_icon="on" font_icon="" icon_color="#ffffff" use_circle="off" circle_color="#2caaca" use_circle_border="off" circle_border_color="#2caaca" icon_placement="left" animation="right" background_layout="dark" text_orientation="left"]The Challenge Vivamus ipsum velit, ullamcorper quis nibh non, molestie tempus sapien. Mauris ultrices, felis ut eleifend auctor[/et_pb_blurb][/et_pb_column][et_pb_column type="1_2"][et_pb_image admin_label="Image" src="https://elegantthemesimages.com/images/premade/mobile-lockup.png" url_new_window="off" animation="left" show_in_lightbox="off" /][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_color="#353535" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="4_4"][et_pb_divider admin_label="Divider" color="#ffffff" show_divider="off" height="60" /][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="1_2"][et_pb_counters admin_label="Bar Counters" background_layout="light" background_color="#2e2e2e"][et_pb_counter percent="80"]Mobile Sales[/et_pb_counter][et_pb_counter percent="50"]Website Traffic[/et_pb_counter][et_pb_counter percent="75"]Conversion Rate[/et_pb_counter][et_pb_counter percent="60"]Email Subscribers[/et_pb_counter][/et_pb_counters][/et_pb_column][et_pb_column type="1_2"][et_pb_cta admin_label="Call To Action" title="The Results Were Amazing" button_url="#" button_text="Live Project" use_background_color="off" background_color="#2ea3f2" background_layout="dark" text_orientation="left"]Vivamus ipsum velit, ullamcorper quis nibh non, molestie tempus sapien. Mauris ultrices, felis ut eleifend auctor, leo felis vehicula quam, ut accumsan augue nunc at nisl. Vivamus ipsum velit, ullamcorper quis nibh non, molestie tempus sapien. Mauris ultrices, felis ut eleifend auctor, leo felis vehicula quam, ut accumsan augue nunc at nisl.[/et_pb_cta][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="4_4"][et_pb_divider admin_label="Divider" color="#ffffff" show_divider="off" height="60" /][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="on" specialty="off" inner_shadow="off" parallax="on"][et_pb_fullwidth_slider admin_label="Fullwidth Slider" show_arrows="on" show_pagination="on" auto="off" parallax="on"][et_pb_slide heading="We Rethought Everything" background_color="#2ea3f2" alignment="center" background_layout="dark" /][et_pb_slide heading="Complete Corporate Identity" background_image="https://elegantthemesimages.com/images/premade/d2-placeholder-1920.png" background_color="#ffffff" alignment="center" background_layout="dark" /][/et_pb_fullwidth_slider][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_color="#f7f7f7" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="4_4"][et_pb_cta admin_label="Call To Action" title="Interested In Working With Us?" button_url="#" button_text="Get In Touch" use_background_color="off" background_color="#2ea3f2" background_layout="light" text_orientation="center" /][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
	);


	$layouts[] = array(
		'name'    => esc_html__( 'Project Extended 2', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section][et_pb_row][et_pb_column type="4_4"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]<h1>Your Project Name</h1>[/et_pb_text][et_pb_image admin_label="Image" src="https://elegantthemesimages.com/images/premade/d2-placeholder-1920.png" url_new_window="off" animation="fade_in" show_in_lightbox="off" /][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="3_4"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]<h4>Project Description</h4>
Vivamus ipsum velit, ullamcorper quis nibh non, molestie tempus sapien. Mauris ultrices, felis ut eleifend auctor, leo felis vehicula quam, ut accumsan augue nunc at nisl. Vivamus ipsum velit, ullamcorper quis nibh non, molestie tempus sapien. Mauris ultrices, felis ut eleifend auctor, leo felis vehicula quam, ut accumsan augue at nisl. Vivamus ipsum velit, ullamcorper quis nibh non, molestie tempus sapien.[/et_pb_text][/et_pb_column][et_pb_column type="1_4"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]<h4>Project Details</h4>
<strong>Client </strong>Client Name
<strong>Date </strong>Date of Completion
<strong>Skills </strong>Branding, Web Design
<strong>View </strong>elegantthemes.com[/et_pb_text][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_color="#f7f7f7" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="1_2"][et_pb_image admin_label="Image" src="https://elegantthemesimages.com/images/premade/d2-placeholder-510px.png" url_new_window="off" animation="left" show_in_lightbox="off" /][/et_pb_column][et_pb_column type="1_2"][et_pb_divider admin_label="Divider" color="#ffffff" show_divider="off" height="60" /][et_pb_cta admin_label="Call To Action" title="Project Feature" button_url="#" button_text="Live Project" background_color="#2caaca" use_background_color="off" background_layout="light" text_orientation="left"]Vivamus ipsum velit, ullamcorper quis nibh non, molestie tempus sapien. Mauris ultrices, felis ut eleifend auctor, leo felis vehicula quam, ut accumsan augue nunc at nisl quis nibh non, molestie tempus sapien.[/et_pb_cta][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="1_3"][et_pb_divider admin_label="Divider" color="#ffffff" show_divider="off" height="100" /][et_pb_cta admin_label="Call To Action" title="Project Feature" button_url="#" button_text="See More" background_color="#2caaca" use_background_color="off" background_layout="light" text_orientation="right"]Vivamus ipsum velit, ullamcorper quis nibh, molestie tempus sapien. Mauris ultrices, felis ut eleifend auctor, leo felis vehicula quam, ut accumsan augue nunc at nisl.[/et_pb_cta][/et_pb_column][et_pb_column type="2_3"][et_pb_image admin_label="Image" src="https://elegantthemesimages.com/images/premade/d2-placeholder-700px.jpg" url_new_window="off" animation="right" show_in_lightbox="off" /][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_image="https://elegantthemesimages.com/images/premade/d2-placeholder-1920.png" inner_shadow="off" parallax="on"][et_pb_row][et_pb_column type="4_4"][et_pb_cta admin_label="Call To Action" title="Like What You See?" button_url="#" button_text="Contact Us" background_color="#2caaca" use_background_color="off" background_layout="dark" text_orientation="center" /][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
	);


	$layouts[] = array(
		'name'    => esc_html__( 'Blog Masonry', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section fullwidth="on" specialty="off" background_color="#2ea3f2" inner_shadow="on" parallax="off"][et_pb_fullwidth_header admin_label="Fullwidth Header" title="Welcome to My Blog" subhead="Here is a masonry blog layout with no sidebar" background_layout="dark" text_orientation="left" /][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_color="#f7f7f7" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="4_4"][et_pb_blog admin_label="Blog" fullwidth="off" posts_number="18" meta_date="M j, Y" show_thumbnail="on" show_content="off" show_author="on" show_date="on" show_categories="on" show_pagination="on" background_layout="light" /][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
	);


	$layouts[] = array(
		'name'    => esc_html__( 'Blog Standard', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section fullwidth="on" specialty="off" background_color="#2ea3f2" inner_shadow="on" parallax="off"][et_pb_fullwidth_header admin_label="Fullwidth Header" title="Welcome to My Blog" subhead="Here is a basic blog layout with a right sidebar" background_layout="dark" text_orientation="left" /][/et_pb_section][et_pb_section fullwidth="off" specialty="on"][et_pb_column type="3_4" specialty_columns="3"][et_pb_row_inner][et_pb_column_inner type="4_4"][et_pb_blog admin_label="Blog" fullwidth="on" posts_number="6" meta_date="M j, Y" show_thumbnail="on" show_content="off" show_author="on" show_date="on" show_categories="on" show_pagination="on" background_layout="light" /][/et_pb_column_inner][/et_pb_row_inner][/et_pb_column][et_pb_column type="1_4"][et_pb_sidebar admin_label="Sidebar" orientation="right" background_layout="light" /][/et_pb_column][/et_pb_section]
EOT
	);


	$layouts[] = array(
		'name'    => esc_html__( 'Shop Basic', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section fullwidth="on" specialty="off" background_color="#f84b48" inner_shadow="off" parallax="off"][et_pb_fullwidth_header admin_label="Fullwidth Header" title="Welcome to Our Shop" subhead="Divi gives you the power to run a full-fledged online storefront." background_layout="dark" text_orientation="left" /][/et_pb_section][et_pb_section fullwidth="off" specialty="off"][et_pb_row][et_pb_column type="4_4"][et_pb_shop admin_label="Shop" type="recent" posts_number="12" columns="4" orderby="menu_order" /][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="4_4"][et_pb_cta admin_label="Call To Action" title="News & Events" button_url="#" button_text="Follow" use_background_color="on" background_color="#57ccc4" background_layout="dark" text_orientation="center"]Lorem ipsum dolor sit amet, consectetur adipiscing elit. In in risus eget lectus suscipit malesuada.[/et_pb_cta][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
	);


	$layouts[] = array(
		'name'    => esc_html__( 'Shop Extended', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section fullwidth="on" specialty="off" background_color="#b2ede0" inner_shadow="off" parallax="off"][et_pb_fullwidth_slider admin_label="Fullwidth Slider" show_arrows="on" show_pagination="on" auto="off" parallax="off"][et_pb_slide heading="Our Shop" button_text="Shop Now" button_link="#" background_color="#81dfde" alignment="center" background_layout="dark" image="https://elegantthemesimages.com/images/premade/d2-placeholder-510px.png"]Divi gives you the power to easily run a full-fledged online storefront. With the Divi Builder, you can create gorgeous shop pages, such as this one.[/et_pb_slide][/et_pb_fullwidth_slider][/et_pb_section][et_pb_section fullwidth="off" specialty="on"][et_pb_column type="3_4" specialty_columns="3"][et_pb_row_inner][et_pb_column_inner type="4_4"][et_pb_shop admin_label="Shop" type="recent" posts_number="6" columns="3" orderby="menu_order" /][/et_pb_column_inner][/et_pb_row_inner][et_pb_row_inner][et_pb_column_inner type="1_2"][et_pb_cta admin_label="Call To Action" title="Summer Sale!" button_url="#" button_text="Shop Now" background_color="#ed5362" use_background_color="on" background_layout="dark" text_orientation="center"]For a limited time only, all of our vintage products are 50% off! Don't miss your chance to save big on these wonderful items.[/et_pb_cta][/et_pb_column_inner][et_pb_column_inner type="1_2"][et_pb_cta admin_label="Call To Action" title="Buy 2 Get 1 Free" button_url="#" button_text="Coupon Code" background_color="#57ccc4" use_background_color="on" background_layout="dark" text_orientation="center"]For a limited time only, if you buy two of any item, you get the 3rd for free! Click below to redeem the coupon code to use at checkout.[/et_pb_cta][/et_pb_column_inner][/et_pb_row_inner][/et_pb_column][et_pb_column type="1_4"][et_pb_sidebar admin_label="Sidebar" orientation="right" background_layout="light" /][/et_pb_column][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_color="#f7f7f7" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="4_4"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]<h1>Our Most Popular Items</h1>[/et_pb_text][et_pb_shop admin_label="Shop" type="best_selling" posts_number="4" columns="4" orderby="menu_order" /][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_color="#57ccc4" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="4_4"][et_pb_cta admin_label="Call To Action" title="View All of Our On-Sale Items" button_url="#" background_color="#2caaca" use_background_color="off" background_layout="dark" text_orientation="center" button_text="Shop Now"]For a limited time only, all of our vintage products are 50% off! Don’t miss your chance to save big on these wonderful items.[/et_pb_cta][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
	);


	$layouts[] = array(
		'name'    => esc_html__( 'Splash Page', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section background_color="#2ea3f2" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="4_4"][et_pb_divider admin_label="Divider" color="#ffffff" show_divider="off" height="150" /][et_pb_blurb admin_label="Blurb" url_new_window="off" image="https://elegantthemesimages.com/images/premade/d2-300px.png" animation="bottom" background_layout="dark" text_orientation="center" use_icon="off" icon_color="#108bf5" use_circle="off" circle_color="#108bf5" use_circle_border="off" circle_border_color="#108bf5" icon_placement="top"]<h1></h1>[/et_pb_blurb][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="1_3"][et_pb_text admin_label="Text" background_layout="dark" text_orientation="center"]<h4><strong>Lorem Ipsum Dolor</strong></h4>
Aenean consectetur ipsum ante, vel egestas enim tincidunt quis. Pellentesque vitae congue neque, vel mattis ante.[/et_pb_text][/et_pb_column][et_pb_column type="1_3"][et_pb_text admin_label="Text" background_layout="dark" text_orientation="center"]<h4><strong>Lorem Ipsum Dolor</strong></h4>
Aenean consectetur ipsum ante, vel egestas enim tincidunt quis. Pellentesque vitae congue neque, vel mattis ante.[/et_pb_text][/et_pb_column][et_pb_column type="1_3"][et_pb_text admin_label="Text" background_layout="dark" text_orientation="center"]<h4><strong>Lorem Ipsum Dolor</strong></h4>
Aenean consectetur ipsum ante, vel egestas enim tincidunt quis. Pellentesque vitae congue neque, vel mattis ante.[/et_pb_text][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="4_4"][et_pb_cta admin_label="Call To Action" button_text="Enter" background_color="#2caaca" use_background_color="off" background_layout="dark" text_orientation="center" button_url="#" /][et_pb_divider admin_label="Divider" color="#ffffff" show_divider="off" height="400" /][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
	);


	$layouts[] = array(
		'name'    => esc_html__( 'Maintenance Mode', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="4_4"][et_pb_divider admin_label="Divider" color="#ffffff" show_divider="off" height="60" /][et_pb_blurb admin_label="Blurb" url_new_window="off" image="https://elegantthemesimages.com/images/premade/builder-blurbs-builder.jpg" animation="top" background_layout="light" text_orientation="center" use_icon="off" icon_color="#2ea3f2" use_circle="off" circle_color="#2ea3f2" use_circle_border="off" circle_border_color="#2ea3f2" icon_placement="top" /][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_color="#f7f7f7" inner_shadow="on" parallax="off"][et_pb_row][et_pb_column type="4_4"][et_pb_text admin_label="Text" background_layout="light" text_orientation="center"]<h1>We will Be back Soon</h1>
This is an example of a blank page with no header or footer.[/et_pb_text][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="1_3"][et_pb_blurb admin_label="Blurb" title="Undergoing Maintenance" url_new_window="off" use_icon="on" font_icon="" icon_color="#63cde3" use_circle="on" circle_color="#f7f7f7" use_circle_border="on" circle_border_color="#2ea3f2" icon_placement="top" animation="top" background_layout="light" text_orientation="center"]Divi is here to stay, and you can rest easy knowing that our team will be updating and improving it for years to come.[/et_pb_blurb][/et_pb_column][et_pb_column type="1_3"][et_pb_blurb admin_label="Blurb" title="Feature Updates" url_new_window="off" use_icon="on" font_icon="" icon_color="#63cde3" use_circle="on" circle_color="#f7f7f7" use_circle_border="on" circle_border_color="#2ea3f2" icon_placement="top" animation="top" background_layout="light" text_orientation="center"]Divi is here to stay, and you can rest easy knowing that our team will be updating and improving it for years to come.[/et_pb_blurb][/et_pb_column][et_pb_column type="1_3"][et_pb_blurb admin_label="Blurb" title="Bug Fixes" url_new_window="off" use_icon="on" font_icon="" icon_color="#63cde3" use_circle="on" circle_color="#f7f7f7" use_circle_border="on" circle_border_color="#2ea3f2" icon_placement="top" animation="top" background_layout="light" text_orientation="center"]Divi is here to stay, and you can rest easy knowing that our team will be updating and improving it for years to come.[/et_pb_blurb][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off"][et_pb_row][et_pb_column type="4_4"][et_pb_cta admin_label="Call To Action" button_url="#" button_text="Contact Us" background_color="#2caaca" use_background_color="off" background_layout="light" text_orientation="center" /][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
	);


	$layouts[] = array(
		'name'    => esc_html__( 'Coming Soon', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section inner_shadow="off" parallax="off" background_color="#8d1bf4"][et_pb_row][et_pb_column type="4_4"][et_pb_divider admin_label="Divider" color="#ffffff" show_divider="off" height="70" /][et_pb_countdown_timer admin_label="Countdown Timer" date_time="05/31/2014 05:15" background_layout="dark" background_color="#e03e3e" use_background_color="off" title="This Site Is Coming Soon" /][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="4_4"][et_pb_signup admin_label="Subscribe" title="Sign Up to Receive Updates" button_text="Submit" background_color="#6e15c2" use_background_color="on" mailchimp_list="none" background_layout="dark" text_orientation="left" provider="mailchimp" aweber_list="none"]Integer accumsan leo non nisi sollicitudin, sit amet eleifend dolor mollis. Donec sagittis posuere commodo. Aenean sed convallis lectus. Vivamus et nisi posuere erat aliquet adipiscing in non libero. Integer ornare dui at molestie dictum. Vivamus id aliquam urna. Duis quis fermentum lacus. Sed viverra dui leo, non auctor nisi porttitor a. Nunc a tristique lectus.[/et_pb_signup][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="4_4"][et_pb_cta admin_label="Call To Action" button_url="#" button_text="Contact Us" background_color="#2caaca" use_background_color="off" background_layout="dark" text_orientation="center" /][et_pb_divider admin_label="Divider" color="#ffffff" show_divider="off" height="600" /][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
	);


	$layouts[] = array(
		'name'    => esc_html__( 'Landing Page', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section background_color="#27323a" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="4_4"][et_pb_text admin_label="Text" background_layout="dark" text_orientation="center"]<h1 style="font-size: 72px;">My Website</h1>
<h2><em>My Tagline</em></h2>[/et_pb_text][et_pb_image admin_label="Image" src="https://elegantthemesimages.com/images/premade/d2-placeholder-1080px.jpg" show_in_lightbox="off" url_new_window="off" animation="fade_in" /][et_pb_cta admin_label="Call To Action" title="Lorem ipsum dolor sit amet consectetur." button_url="#" button_text="Learn More" use_background_color="off" background_color="#2ea3f2" background_layout="dark" text_orientation="center" /][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section background_color="#313f55" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="1_3"][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum Dolor" url_new_window="off" use_icon="off" icon_color="#2ea3f2" use_circle="off" circle_color="#2ea3f2" use_circle_border="off" circle_border_color="#2ea3f2" image="https://elegantthemesimages.com/images/premade/d2-placeholder-320px.jpg" icon_placement="top" animation="top" background_layout="dark" text_orientation="center"]Aenean consectetur ipsum ante, vel egestas enim tincidunt quis.[/et_pb_blurb][/et_pb_column][et_pb_column type="1_3"][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum Dolor" url_new_window="off" use_icon="off" icon_color="#2ea3f2" use_circle="off" circle_color="#2ea3f2" use_circle_border="off" circle_border_color="#2ea3f2" image="https://elegantthemesimages.com/images/premade/d2-placeholder-320px.jpg" icon_placement="top" animation="top" background_layout="dark" text_orientation="center"]Aenean consectetur ipsum ante, vel egestas enim tincidunt quis.[/et_pb_blurb][/et_pb_column][et_pb_column type="1_3"][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum Dolor" url_new_window="off" use_icon="off" icon_color="#2ea3f2" use_circle="off" circle_color="#2ea3f2" use_circle_border="off" circle_border_color="#2ea3f2" image="https://elegantthemesimages.com/images/premade/d2-placeholder-320px.jpg" icon_placement="top" animation="top" background_layout="dark" text_orientation="center"]Aenean consectetur ipsum ante, vel egestas enim tincidunt quis.[/et_pb_blurb][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section background_color="#27323a" inner_shadow="on" parallax="off"][et_pb_row][et_pb_column type="4_4"][et_pb_image admin_label="Image" src="https://elegantthemesimages.com/images/premade/d2-placeholder-1080px.jpg" show_in_lightbox="off" url_new_window="off" animation="right" /][et_pb_cta admin_label="Call To Action" button_url="#" button_text="Get Started" use_background_color="off" background_color="#2ea3f2" background_layout="light" text_orientation="center" /][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
	);


	$layouts[] = array(
		'name'    => esc_html__( 'About Me', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section fullwidth="on" specialty="off"][et_pb_fullwidth_slider admin_label="Fullwidth Slider" show_arrows="on" show_pagination="on" auto="off" parallax="on"][et_pb_slide heading="My Name" background_image="https://elegantthemesimages.com/images/premade/d2-placeholder-1920.png" background_color="#ffffff" alignment="center" background_layout="dark"]Subheading[/et_pb_slide][/et_pb_fullwidth_slider][/et_pb_section][et_pb_section fullwidth="off" specialty="off" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="1_3"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]<h1>This is My Story</h1>
Curabitur quis dui volutpat, cursus eros ut, commodo elit. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Ut id est euismod, rhoncus nunc quis, lobortis turpis. Tam sociis natoque. Curabitur quis dui volutpat, cursus eros ut, commodo elit. Cum sociis natoque penatibus et magnis dis parturient montes.[/et_pb_text][/et_pb_column][et_pb_column type="2_3"][et_pb_counters admin_label="Bar Counters" background_layout="light" background_color="#dddddd" bar_bg_color="#2ea3f2"][et_pb_counter percent="80"]Brand Strategy[/et_pb_counter][et_pb_counter percent="60"]Internet Marketing[/et_pb_counter][et_pb_counter percent="50"]App Development[/et_pb_counter][et_pb_counter percent="90"]Customer Happiness[/et_pb_counter][/et_pb_counters][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="on" specialty="off" background_color="#108bf5" inner_shadow="off" parallax="off"][et_pb_fullwidth_header admin_label="Fullwidth Header" title="My Recent Work" background_layout="dark" text_orientation="center" /][et_pb_fullwidth_portfolio admin_label="Fullwidth Portfolio" fullwidth="on" show_title="on" show_date="on" background_layout="light" auto="on" /][/et_pb_section]
EOT
	);


	$layouts[] = array(
		'name'    => esc_html__( 'About Us', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section fullwidth="on" specialty="off"][et_pb_fullwidth_slider admin_label="Fullwidth Slider" show_arrows="on" show_pagination="on" auto="off" parallax="on"][et_pb_slide heading="Our Company" button_text="Learn More" button_link="#" background_image="https://elegantthemesimages.com/images/premade/d2-placeholder-1920.png" background_color="#ffffff" alignment="center" background_layout="dark"]Our Company Tagline lorem ipsum dolor sit amet.[/et_pb_slide][/et_pb_fullwidth_slider][/et_pb_section][et_pb_section inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="1_4"][et_pb_blurb admin_label="Blurb" title="A Digital Agency" url_new_window="off" use_icon="on" font_icon="" icon_color="#7c8d9b" use_circle="off" circle_color="#2ea3f2" use_circle_border="off" circle_border_color="#2ea3f2" icon_placement="top" animation="top" background_layout="light" text_orientation="center"]Curabitur quis dui volutpat, cursus eros elut commodo elit cum sociis natoque penatibus[/et_pb_blurb][/et_pb_column][et_pb_column type="1_4"][et_pb_blurb admin_label="Blurb" title="Forward Thinking" url_new_window="off" use_icon="on" font_icon="" icon_color="#7c8d9b" use_circle="off" circle_color="#2ea3f2" use_circle_border="off" circle_border_color="#2ea3f2" icon_placement="top" animation="top" background_layout="light" text_orientation="center"]Curabitur quis dui volutpat, cursus eros elut commodo elit cum sociis natoque penatibus[/et_pb_blurb][/et_pb_column][et_pb_column type="1_4"][et_pb_blurb admin_label="Blurb" title="Problem Solvers" url_new_window="off" use_icon="on" font_icon="" icon_color="#7c8d9b" use_circle="off" circle_color="#2ea3f2" use_circle_border="off" circle_border_color="#2ea3f2" icon_placement="top" animation="top" background_layout="light" text_orientation="center"]Curabitur quis dui volutpat, cursus eros elut commodo elit cum sociis natoque penatibus[/et_pb_blurb][/et_pb_column][et_pb_column type="1_4"][et_pb_blurb admin_label="Blurb" title="Customer Support" url_new_window="off" use_icon="on" font_icon="" icon_color="#7c8d9b" use_circle="off" circle_color="#2ea3f2" use_circle_border="off" circle_border_color="#2ea3f2" icon_placement="top" animation="top" background_layout="light" text_orientation="center"]Curabitur quis dui volutpat, cursus eros elut commodo elit cum sociis natoque penatibus[/et_pb_blurb][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_color="#f7f7f7" inner_shadow="on" parallax="off"][et_pb_row][et_pb_column type="1_3"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]<h1>Our Story</h1>
Curabitur quis dui volutpat, cursus eros ut, commodo elit. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Ut id est euismod, rhoncus nunc quis, lobortis turpis. Tam sociis natoque. Curabitur quis dui volutpat, cursus eros ut, commodo elit. Cum sociis natoque penatibus et magnis dis parturient montes.[/et_pb_text][/et_pb_column][et_pb_column type="2_3"][et_pb_counters admin_label="Bar Counters" background_layout="light" background_color="#dddddd" bar_bg_color="#2ea3f2"][et_pb_counter percent="80"]Brand Strategy[/et_pb_counter][et_pb_counter percent="60"]Internet Marketing[/et_pb_counter][et_pb_counter percent="50"]App Development[/et_pb_counter][et_pb_counter percent="90"]Customer Happiness[/et_pb_counter][/et_pb_counters][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off"][et_pb_row][et_pb_column type="1_3"][et_pb_team_member admin_label="Team Member" name="Team Member 1" position="Company Role" image_url="https://elegantthemesimages.com/images/premade/d2-placeholder-320px.jpg" animation="fade_in" background_layout="light" facebook_url="#" twitter_url="#" google_url="#" linkedin_url="#"]Aenean consectetur ipsum ante, vel egestas enim tincidunt quis. Pellentesque vitae congue neque, vel mattis ante. In vitae tempus nunc.[/et_pb_team_member][/et_pb_column][et_pb_column type="1_3"][et_pb_team_member admin_label="Team Member" name="Team Member 1" position="Company Role" image_url="https://elegantthemesimages.com/images/premade/d2-placeholder-320px.jpg" animation="fade_in" background_layout="light" facebook_url="#" twitter_url="#" google_url="#" linkedin_url="#"]Aenean consectetur ipsum ante, vel egestas enim tincidunt quis. Pellentesque vitae congue neque, vel mattis ante. In vitae tempus nunc.[/et_pb_team_member][/et_pb_column][et_pb_column type="1_3"][et_pb_team_member admin_label="Team Member" name="Team Member 1" position="Company Role" image_url="https://elegantthemesimages.com/images/premade/d2-placeholder-320px.jpg" animation="fade_in" background_layout="light" facebook_url="#" twitter_url="#" google_url="#" linkedin_url="#"]Aenean consectetur ipsum ante, vel egestas enim tincidunt quis. Pellentesque vitae congue neque, vel mattis ante. In vitae tempus nunc.[/et_pb_team_member][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
	);


	$layouts[] = array(
		'name'    => esc_html__( 'Contact Us', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section fullwidth="on" specialty="off"][et_pb_fullwidth_map admin_label="Fullwidth Map" zoom_level="9" address_lat="37.77492949999972" address_lng="-122.41941550000001"][et_pb_map_pin title="Headquarters" pin_address="San Francisco, CA, USA" pin_address_lat="37.7749295" pin_address_lng="-122.41941550000001" /][/et_pb_fullwidth_map][/et_pb_section][et_pb_section fullwidth="off"][et_pb_row][et_pb_column type="2_3"][et_pb_contact_form admin_label="Contact Form" captcha="off" title="Get In Touch" /][/et_pb_column][et_pb_column type="1_3"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]<h3>More Info</h3>
<p>sit amet, consectetur adipiscing elit. Integer placerat metus id orci facilisis, in luctus eros laoreet. Mauris interdum augue varius, faucibus massa id, imperdiet tortor. Donec vel tortor molestie, hendrerit sem a, hendrerit arcu. Aliquam erat volutpat. Proin varius eros eros, non condimentum nis.</p>

<strong>Address:</strong> 890 Lorem Ipsum Street #12
San Francisco, California 65432

<strong>Phone:</strong> 123.4567.890

<strong>Business Hours:</strong> 8a-6:30p M-F, 9a-2p S-S[/et_pb_text][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
	);


	$layouts[] = array(
		'name'    => esc_html__( 'Our Team', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section background_color="#6aceb6" inner_shadow="on" fullwidth="on"]
[et_pb_fullwidth_header title="About Our Team" subhead="Your subtitle goes right here." background_layout="dark"][/et_pb_fullwidth_header]
[/et_pb_section]

[et_pb_section]
[et_pb_row]
[et_pb_column type="1_3"]
[et_pb_image src="https://www.elegantthemesimages.com/images/premade_image_800x600.png" animation="left"][/et_pb_image]
[et_pb_text]
<h2>Nick Roach</h2>
<em>President, CEO, Theme UI/UX Designer</em>
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent mattis nec nisi non luctus. Donec aliquam non nisi ut rutrum. In sit amet vestibulum felis, id aliquet ipsum. Vestibulum feugiat lacinia aliquet.
[/et_pb_text]
[et_pb_counters]
[et_pb_counter percent="50"]Design & UX[/et_pb_counter]
[et_pb_counter percent="80"]Web Programming[/et_pb_counter]
[et_pb_counter percent="10"]Internet Marketing[/et_pb_counter]
[/et_pb_counters]
[/et_pb_column]

[et_pb_column type="1_3"]
[et_pb_image src="https://www.elegantthemesimages.com/images/premade_image_800x600.png" animation="top"][/et_pb_image]
[et_pb_text]
<h2>Kenny Sing</h2>
<em>Lead Graphic Designers</em>
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent mattis nec nisi non luctus. Donec aliquam non nisi ut rutrum. In sit amet vestibulum felis, id aliquet ipsum. Vestibulum feugiat lacinia aliquet.
[/et_pb_text]
[et_pb_counters]
[et_pb_counter percent="85"]Photoshop[/et_pb_counter]
[et_pb_counter percent="70"]After Effects[/et_pb_counter]
[et_pb_counter percent="50"]Illustrator[/et_pb_counter]
[/et_pb_counters]
[/et_pb_column]

[et_pb_column type="1_3"]
[et_pb_image src="https://www.elegantthemesimages.com/images/premade_image_800x600.png" animation="right"][/et_pb_image]
[et_pb_text]
<h2>Mitch Skolnik</h2>
<em>Community Manager</em>
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent mattis nec nisi non luctus. Donec aliquam non nisi ut rutrum. In sit amet vestibulum felis, id aliquet ipsum. Vestibulum feugiat lacinia aliquet.
[/et_pb_text]
[et_pb_counters]
[et_pb_counter percent="80"]Customer Happiness[/et_pb_counter]
[et_pb_counter percent="30"]Tech Support[/et_pb_counter]
[et_pb_counter percent="50"]Community Management[/et_pb_counter]
[/et_pb_counters]
[/et_pb_column]
[/et_pb_row]
[/et_pb_section]

[et_pb_section background_color="#2d3743" inner_shadow="on"]
[et_pb_row]
[et_pb_column type="1_4"]
[et_pb_blurb background_layout="dark" image="https://www.elegantthemesimages.com/images/premade_blurb_5.png"  title="Timely Support"]Vestibulum lobortis. Donec at euismod nibh, eu bibendum quam. Nullam non gravida purus, nec eleifend tincidunt nisi. Fusce at purus in massa laoreet.[/et_pb_blurb]
[/et_pb_column]
[et_pb_column type="1_4"]
[et_pb_blurb background_layout="dark" image="https://www.elegantthemesimages.com/images/premade_blurb_6.png"  title="Innovative Ideas"]Vestibulum lobortis. Donec at euismod nibh, eu bibendum quam. Nullam non gravida purus, nec eleifend tincidunt nisi. Fusce at purus in massa laoreet.[/et_pb_blurb]
[/et_pb_column]
[et_pb_column type="1_4"]
[et_pb_blurb background_layout="dark" image="https://www.elegantthemesimages.com/images/premade_blurb_7.png"  title="Advanced Technology"]Vestibulum lobortis. Donec at euismod nibh, eu bibendum quam. Nullam non gravida purus, nec eleifend tincidunt nisi. Fusce at purus in massa laoreet.[/et_pb_blurb]
[/et_pb_column]
[et_pb_column type="1_4"]
[et_pb_blurb background_layout="dark" image="https://www.elegantthemesimages.com/images/premade_blurb_8.png"  title="Clear Communication"]Vestibulum lobortis. Donec at euismod nibh, eu bibendum quam. Nullam non gravida purus, nec eleifend tincidunt nisi. Fusce at purus in massa laoreet.[/et_pb_blurb]
[/et_pb_column]
[/et_pb_row]
[/et_pb_section]

[et_pb_section background_color="#f5f5f5" inner_shadow="on"]
[et_pb_row]
[et_pb_column type="4_4"]
[et_pb_text text_orientation="center"]<h2>Recent Blog Posts</h2>
Learn from the top thought leaders in the industry.
[/et_pb_text]
[/et_pb_column]
[/et_pb_row]
[et_pb_row]
[et_pb_column type="4_4"]
[et_pb_blog fullwidth="off" show_pagination="off" posts_number="3" meta_date="M j, Y" show_thumbnail="on" show_content="off" show_author="on" show_date="on" show_categories="on"][/et_pb_blog]
[/et_pb_column]
[/et_pb_row]
[/et_pb_section]

[et_pb_section]
[et_pb_row]
[et_pb_column type="4_4"]
[et_pb_text text_orientation="center"]<h2>Recent Projects</h2>
Learn from the top thought leaders in the industry.
[/et_pb_text]
[/et_pb_column]
[/et_pb_row]
[et_pb_row]
[et_pb_column type="4_4"]
[et_pb_portfolio categories="Portfolio" fullwidth="off"][/et_pb_portfolio]
[/et_pb_column]
[/et_pb_row]
[/et_pb_section]

[et_pb_section background_color="#7EBEC5"]
[et_pb_row]
[et_pb_column type="4_4"]
[et_pb_cta title="Don't Be Shy. Get In Touch." button_url="#" button_text="Contact Us" background_layout="dark" background_color="none"]
If you are interested in working together, send us an inquiry and we will get back to you as soon as we can!
[/et_pb_cta]
[/et_pb_column]
[/et_pb_row]
[/et_pb_section]
EOT
	);


	$layouts[] = array(
		'name'    => esc_html__( 'Creative Agency', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section fullwidth="on" specialty="off"][et_pb_fullwidth_slider admin_label="Fullwidth Slider" show_arrows="on" show_pagination="on" auto="off" parallax="off"][et_pb_slide heading="WE ARE A CREATIVE AGENCY" button_text="Our Work" button_link="https://elegantthemes.com/preview/Divi2/fullwidth-grid/" background_image="https://elegantthemesimages.com/images/premade/d2-placeholder-1920.png" background_color="#ffffff" alignment="center" background_layout="dark" /][/et_pb_fullwidth_slider][/et_pb_section][et_pb_section inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="1_4"][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum" url_new_window="off" image="https://elegantthemesimages.com/images/premade/builder-blurbs-mobile.jpg" animation="top" background_layout="light" text_orientation="center" use_icon="off" icon_color="#108bf5" use_circle="off" circle_color="#108bf5" use_circle_border="off" circle_border_color="#108bf5" icon_placement="top"]Divi will change the way you build websites forever. The advanced page builder makes it possible to build truly dynamic pages without learning code.[/et_pb_blurb][/et_pb_column][et_pb_column type="1_4"][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum" url_new_window="off" image="https://elegantthemesimages.com/images/premade/builder-blurbs-export.jpg" animation="top" background_layout="light" text_orientation="center" use_icon="off" icon_color="#108bf5" use_circle="off" circle_color="#108bf5" use_circle_border="off" circle_border_color="#108bf5" icon_placement="top"]Divi will change the way you build websites forever. The advanced page builder makes it possible to build truly dynamic pages without learning code.[/et_pb_blurb][/et_pb_column][et_pb_column type="1_4"][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum" url_new_window="off" image="https://elegantthemesimages.com/images/premade/builder-blurbs-layouts.jpg" animation="top" background_layout="light" text_orientation="center" use_icon="off" icon_color="#108bf5" use_circle="off" circle_color="#108bf5" use_circle_border="off" circle_border_color="#108bf5" icon_placement="top"]Divi will change the way you build websites forever. The advanced page builder makes it possible to build truly dynamic pages without learning code.[/et_pb_blurb][/et_pb_column][et_pb_column type="1_4"][et_pb_blurb admin_label="Blurb" title="Lorem Ipsum" url_new_window="off" image="https://elegantthemesimages.com/images/premade/builder-blurbs-commerce.jpg" animation="top" background_layout="light" text_orientation="center" use_icon="off" icon_color="#108bf5" use_circle="off" circle_color="#108bf5" use_circle_border="off" circle_border_color="#108bf5" icon_placement="top"]Divi will change the way you build websites forever. The advanced page builder makes it possible to build truly dynamic pages without learning code.[/et_pb_blurb][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_color="#f7f7f7" inner_shadow="on" parallax="off"][et_pb_row][et_pb_column type="4_4"][et_pb_text admin_label="Text" background_layout="light" text_orientation="center"]<h1>OUR LATEST WORK</h1>[/et_pb_text][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="4_4"][et_pb_portfolio admin_label="Portfolio" fullwidth="off" posts_number="8" show_title="on" show_categories="off" show_pagination="off" background_layout="light" /][et_pb_cta admin_label="Call To Action" button_url="#" button_text="Full Portfolio" use_background_color="off" background_color="#2ea3f2" background_layout="light" text_orientation="center" /][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_color="#222b34" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="4_4"][et_pb_text admin_label="Text" background_layout="dark" text_orientation="center"]<h1>MEET THE CREW</h1>[/et_pb_text][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="1_3"][et_pb_team_member admin_label="Team Member" name="Lorem Ipsum" position="Company Role" image_url="https://elegantthemesimages.com/images/premade/d2-placeholder-320px.jpg" animation="fade_in" background_layout="dark" /][et_pb_team_member admin_label="Team Member" name="Lorem Ipsum" position="Company Role" image_url="https://elegantthemesimages.com/images/premade/d2-placeholder-320px.jpg" animation="fade_in" background_layout="dark" /][/et_pb_column][et_pb_column type="1_3"][et_pb_team_member admin_label="Team Member" name="Lorem Ipsum" position="Company Role" image_url="https://elegantthemesimages.com/images/premade/d2-placeholder-320px.jpg" animation="fade_in" background_layout="dark" /][et_pb_team_member admin_label="Team Member" name="Lorem Ipsum" position="Company Role" image_url="https://elegantthemesimages.com/images/premade/d2-placeholder-320px.jpg" animation="fade_in" background_layout="dark" /][/et_pb_column][et_pb_column type="1_3"][et_pb_team_member admin_label="Team Member" name="Lorem Ipsum" position="Company Role" image_url="https://elegantthemesimages.com/images/premade/d2-placeholder-320px.jpg" animation="fade_in" background_layout="dark" /][et_pb_team_member admin_label="Team Member" name="Lorem Ipsum" position="Company Role" image_url="https://elegantthemesimages.com/images/premade/d2-placeholder-320px.jpg" animation="fade_in" background_layout="dark" /][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="4_4"][et_pb_cta admin_label="Call To Action" button_url="#" button_text="Full Profiles" use_background_color="off" background_color="#2ea3f2" background_layout="dark" text_orientation="center" /][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off"][et_pb_row][et_pb_column type="4_4"][et_pb_text admin_label="Text" background_layout="light" text_orientation="center"]<h1>OUR CLIENTS</h1>[/et_pb_text][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="1_4"][et_pb_image admin_label="Image" src="https://www.elegantthemesimages.com/images/premade/et-logo.png" url_new_window="off" animation="left" show_in_lightbox="off" /][/et_pb_column][et_pb_column type="1_4"][et_pb_image admin_label="Image" src="https://www.elegantthemesimages.com/images/premade/et-logo.png" url_new_window="off" animation="left" show_in_lightbox="off" /][/et_pb_column][et_pb_column type="1_4"][et_pb_image admin_label="Image" src="https://www.elegantthemesimages.com/images/premade/et-logo.png" url_new_window="off" animation="left" show_in_lightbox="off" /][/et_pb_column][et_pb_column type="1_4"][et_pb_image admin_label="Image" src="https://www.elegantthemesimages.com/images/premade/et-logo.png" url_new_window="off" animation="left" show_in_lightbox="off" /][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="1_4"][et_pb_image admin_label="Image" src="https://www.elegantthemesimages.com/images/premade/et-logo.png" url_new_window="off" animation="left" show_in_lightbox="off" /][/et_pb_column][et_pb_column type="1_4"][et_pb_image admin_label="Image" src="https://www.elegantthemesimages.com/images/premade/et-logo.png" url_new_window="off" animation="left" show_in_lightbox="off" /][/et_pb_column][et_pb_column type="1_4"][et_pb_image admin_label="Image" src="https://www.elegantthemesimages.com/images/premade/et-logo.png" url_new_window="off" animation="left" show_in_lightbox="off" /][/et_pb_column][et_pb_column type="1_4"][et_pb_image admin_label="Image" src="https://www.elegantthemesimages.com/images/premade/et-logo.png" url_new_window="off" animation="left" show_in_lightbox="off" /][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="4_4"][et_pb_cta admin_label="Call To Action" button_url="#" button_text="Full List" use_background_color="off" background_color="#2ea3f2" background_layout="light" text_orientation="center" /][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off" inner_shadow="off" parallax="off" background_color="#2ea3f2"][et_pb_row][et_pb_column type="4_4"][et_pb_signup admin_label="Subscribe" provider="mailchimp" mailc
himp_list="none" aweber_list="3423452" button_text="Sign Me Up" use_background_color="off" background_color="#2ea3f2" background_layout="dark" text_orientation="left" title="Connect With Us"]Aenean consectetur ipsum ante, vel egestas enim tincidunt quis. Pellentesque vitae congue neque, vel mattis ante. In vitae tempus nunc. Etiam adipiscing enim sed condimentum ultrices. Cras rutrum blandit sem, molestie consequat erat luctus vel. Cras nunc est, laoreet sit amet ligula et, eleifend commodo dui. Vivamus id blandit nisi, eu mattis odio. Nulla facilisi. Aenean in mi odio. Etiam adipiscing enim sed condimentum ultrices.[/et_pb_signup][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
	);


	$layouts[] = array(
		'name'    => esc_html__( 'Sales Page', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section fullwidth="on" specialty="off" inner_shadow="off" parallax="off"][et_pb_fullwidth_slider admin_label="Fullwidth Slider" show_arrows="on" show_pagination="on" auto="off" parallax="off"][et_pb_slide heading="A Brand New Product" background_color="#efefef" image="https://elegantthemesimages.com/images/premade/d2-placeholder-510px.png" alignment="center" background_layout="light" button_text="Buy Now"]The Divi Builder allows you to create beautiful and unique layouts visually, without touching a single line of code.[/et_pb_slide][/et_pb_fullwidth_slider][/et_pb_section][et_pb_section fullwidth="off" specialty="off"][et_pb_row][et_pb_column type="1_3"][et_pb_blurb admin_label="Blurb" title="Gorgeous Design" url_new_window="off" use_icon="on" font_icon="" icon_color="#2ea3f2" use_circle="off" circle_color="#108bf5" use_circle_border="off" circle_border_color="#108bf5" icon_placement="left" animation="top" background_layout="light" text_orientation="center"]Vestibulum lobortis. Donec at euismod nibh, eu bibendum quam.[/et_pb_blurb][/et_pb_column][et_pb_column type="1_3"][et_pb_blurb admin_label="Blurb" title="Drag & Drop Builder" url_new_window="off" use_icon="on" font_icon="1" icon_color="#2ea3f2" use_circle="off" circle_color="#108bf5" use_circle_border="off" circle_border_color="#108bf5" icon_placement="left" animation="top" background_layout="light" text_orientation="center"]Vestibulum lobortis. Donec at euismod nibh, eu bibendum quam.[/et_pb_blurb][/et_pb_column][et_pb_column type="1_3"][et_pb_blurb admin_label="Blurb" title="Fully Responsive" url_new_window="off" use_icon="on" font_icon="" icon_color="#2ea3f2" use_circle="off" circle_color="#108bf5" use_circle_border="off" circle_border_color="#108bf5" icon_placement="left" animation="top" background_layout="light" text_orientation="center"]Vestibulum lobortis. Donec at euismod nibh, eu bibendum quam.[/et_pb_blurb][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_color="#f3f3f3" inner_shadow="on" parallax="off"][et_pb_row][et_pb_column type="4_4"][et_pb_text admin_label="Text" background_layout="light" text_orientation="center"]<h1>Plans and Pricing</h1>
Lorem ipsum dolor sit amet, consectetur adipiscing elit. In in risus eget lectus suscipit malesuada. Maecenas ut urna mollis, aliquam eros at, laoreet metus. Proin ac eros eros. Suspendisse auctor, eros ac sollicitudin vulputate.[/et_pb_text][et_pb_divider admin_label="Divider" color="#ffffff" show_divider="off" height="60" /][et_pb_pricing_tables admin_label="Pricing Table"][et_pb_pricing_table featured="off" title="Basic" currency="$" per="yr" sum="39" button_url="https://elegantthemes.com/" button_text="Sign Up"]+Access to <a href="https://elegantthemes.com/preview/Divi/module-pricing-tables/#">All Themes</a>
+Perpetual Theme Updates
-Premium Technical Support
-Access to <a href="https://elegantthemes.com/preview/Divi/module-pricing-tables/#">All Plugins</a>
-Layered Photoshop Files
-No Yearly Fees[/et_pb_pricing_table][et_pb_pricing_table featured="off" title="Personal" currency="$" per="yr" sum="69" button_url="https://elegantthemes.com/" button_text="Sign Up"]+Access to <a href="https://elegantthemes.com/preview/Divi/module-pricing-tables/#">All Themes</a>
+Perpetual Theme Updates
+Premium Technical Support
-Access to <a href="https://elegantthemes.com/preview/Divi/module-pricing-tables/#">All Plugins</a>
-Layered Photoshop Files
-No Yearly Fees[/et_pb_pricing_table][et_pb_pricing_table featured="on" title="Developer" subtitle="Best Value" currency="$" per="yr" sum="89" button_url="https://elegantthemes.com/" button_text="Sign Up"]+Access to <a href="https://elegantthemes.com/preview/Divi/module-pricing-tables/#">All Themes</a>
+Perpetual Theme Updates
+Premium Technical Support
+Access to <a href="https://elegantthemes.com/preview/Divi/module-pricing-tables/#">All Plugins</a>
+Layered Photoshop Files
-No Yearly Fees[/et_pb_pricing_table][et_pb_pricing_table featured="off" title="Lifetime" currency="$" sum="249" button_url="https://elegantthemes.com/" button_text="Sign Up"]+Access to <a href="https://elegantthemes.com/preview/Divi/module-pricing-tables/#">All Themes</a>
+Perpetual Theme Updates
+Premium Technical Support
+Access to <a href="https://elegantthemes.com/preview/Divi/module-pricing-tables/#">All Plugins</a>
+Layered Photoshop Files
+No Yearly Fees[/et_pb_pricing_table][/et_pb_pricing_tables][et_pb_divider admin_label="Divider" color="#ffffff" show_divider="off" height="60" /][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off"][et_pb_row][et_pb_column type="4_4"][et_pb_text admin_label="Text" background_layout="light" text_orientation="center"]<h1>What Our Customers Are Saying</h1>
Don't just take it from us, let our customers do the talking![/et_pb_text][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="1_3"][et_pb_testimonial admin_label="Testimonial" author="Luke Chapman" url_new_window="off" portrait_url="https://elegantthemesimages.com/images/premade/d2-placeholder-225px.png" quote_icon="off" use_background_color="on" background_color="#f5f5f5" background_layout="light" text_orientation="left"]"Lorem ipsum dolor sit amet, consectetur adipiscing elit. In in risus eget lectus suscipit malesuada. Maecenas ut urna mollis, aliquam eros at, laoreet metus. Proin ac eros eros. Suspendisse auctor, eros ac sollicitudin vulputate, urna arcu sodales quam, eget faucibus eros ante nec enim.

Etiam quis eros in enim molestie tempus a non urna. Suspendisse nibh massa, tristique sit amet interdum non, fermentum in quam. "[/et_pb_testimonial][/et_pb_column][et_pb_column type="1_3"][et_pb_testimonial admin_label="Testimonial" author="Luke Chapman" url_new_window="off" portrait_url="https://elegantthemesimages.com/images/premade/d2-placeholder-225px.png" quote_icon="off" use_background_color="on" background_color="#f5f5f5" background_layout="light" text_orientation="left"]"Lorem ipsum dolor sit amet, consectetur adipiscing elit. In in risus eget lectus suscipit malesuada. Maecenas ut urna mollis, aliquam eros at, laoreet metus. Proin ac eros eros. Suspendisse auctor, eros ac sollicitudin vulputate, urna arcu sodales quam, eget faucibus eros ante nec enim.

Etiam quis eros in enim molestie tempus a non urna. Suspendisse nibh massa, tristique sit amet interdum non, fermentum in quam. "[/et_pb_testimonial][/et_pb_column][et_pb_column type="1_3"][et_pb_testimonial admin_label="Testimonial" author="Luke Chapman" url_new_window="off" portrait_url="https://elegantthemesimages.com/images/premade/d2-placeholder-225px.png" quote_icon="off" use_background_color="on" background_color="#f5f5f5" background_layout="light" text_orientation="left"]"Lorem ipsum dolor sit amet, consectetur adipiscing elit. In in risus eget lectus suscipit malesuada. Maecenas ut urna mollis, aliquam eros at, laoreet metus. Proin ac eros eros. Suspendisse auctor, eros ac sollicitudin vulputate, urna arcu sodales quam, eget faucibus eros ante nec enim.

Etiam quis eros in enim molestie tempus a non urna. Suspendisse nibh massa, tristique sit amet interdum non, fermentum in quam. "[/et_pb_testimonial][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_color="#eeeeee" inner_shadow="on" parallax="off"][et_pb_row][et_pb_column type="1_4"][et_pb_image admin_label="Image" src="https://elegantthemesimages.com/images/premade/et-logo.png" show_in_lightbox="off" url_new_window="off" animation="bottom" /][/et_pb_column][et_pb_column type="1_4"][et_pb_image admin_label="Image" src="https://elegantthemesimages.com/images/premade/et-logo.png" show_in_lightbox="off" url_new_window="off" animation="bottom" /][/et_pb_column][et_pb_column type="1_4"][et_pb_image admin_label="Image" src="https://elegantthemesimages.com/images/premade/et-logo.png" show_in_lightbox="off" url_new_window="off" animation="bottom" /][/et_pb_column][et_pb_column type="1_4"][et_pb_image admin_label="Image" src="https://elegantthemesimages.com/images/premade/et-logo.png" show_in_lightbox="off" url_new_window="off" animation="bottom" /][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off"][et_pb_row][et_pb_column type="4_4"][et_pb_text admin_label="Text" background_layout="light" text_orientation="center"]<h1>Frequently Asked Questions</h1>[/et_pb_text][et_pb_toggle admin_label="Toggle" title="Can I use the themes on multiple sites?" open="off"]Yes, you are free to use our themes on as many websites as you like. We do not place any restrictions on how many times you can download or use a theme, nor do we limit the number of domains that you can install our themes to.[/et_pb_toggle][et_pb_toggle admin_label="Toggle" title="What is your refund policy?" open="on"]We offer no-questions-asked refunds to all customers within 30 days of your purchase. If you are not satisfied with our product, then simply send us an email and we will refund your purchase right away. Our goal has always been to create a happy, thriving community. If you are not thrilled with the product or are not enjoying the experience, then we have no interest in forcing you to stay an unhappy member.[/et_pb_toggle][et_pb_toggle admin_label="Toggle" title="What are Photoshop Files?" open="off"]Elegant Themes offers two different packages: Personal and Developer. The Personal Subscription is ideal for the average user while the Developers License is meant for experienced designers who wish to customize their themes using the original Photoshop files. Photoshop files are the original design files that were used to create the theme. They can be opened using Adobe Photoshop and edited, and prove very useful for customers wishing to change their theme's design in some way.[/et_pb_toggle][et_pb_toggle admin_label="Toggle" title="Can I upgrade after signing up?" open="off"]Yes, you can upgrade at any time after signing up. When you log in as a "personal" subscriber, you will see a notice regarding your current package and instructions on how to upgrade.[/et_pb_toggle][et_pb_toggle admin_label="Toggle" title="Can I use your themes with WP.com?" open="off"]Unfortunately WordPress.com does not allow the use of custom themes. If you would like to use a custom theme of any kind, you will need to purchase your own hosting account and install the free software from WordPress.org. If you are looking for great WordPress hosting, we recommend giving HostGator a try.[/et_pb_toggle][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_image="https://elegantthemesimages.com/images/premade/d2-placeholder-1920.png" inner_shadow="on" parallax="off"][et_pb_row][et_pb_column type="4_4"][et_pb_cta admin_label="Call To Action" title="Don't Be Shy" button_url="#" button_text="Get In Touch" use_background_color="off" background_color="#108bf5" background_layout="dark" text_orientation="center"]If we didn't answer all of your questions, feel free to drop us a line anytime.[/et_pb_cta][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
	);


	$layouts[] = array(
		'name'    => esc_html__( 'Case Study', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section background_color="#2ea3f2" inner_shadow="off" parallax="on"][et_pb_row][et_pb_column type="4_4"][et_pb_blurb admin_label="Blurb" url_new_window="off" image="https://elegantthemesimages.com/images/premade/d2-300px.png" animation="bottom" background_layout="light" text_orientation="center" use_icon="off" icon_color="#45c4ec" use_circle="off" circle_color="#45c4ec" use_circle_border="off" circle_border_color="#45c4ec" icon_placement="top" /][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="4_4"][et_pb_text admin_label="Text" background_layout="dark" text_orientation="center"]<h1 style="font-size: 72px; font-weight: 300;">Divi Case Study</h1>[/et_pb_text][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="1_2"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]<h2>The Challenge</h2>
Vivamus ipsum velit, ullamcorper quis nibh non, molestie tempus sapien. Mauris ultrices, felis ut eleifend auctor, leo felis vehicula quam, ut accumsan augue nunc at nisl. Vivamus ipsum velit, ullamcorper quis nibh non, molestie tempus sapien. Mauris ultrices, felis ut eleifend auctor, leo felis vehicula quam, ut accumsan augue nunc at nisl.[/et_pb_text][/et_pb_column][et_pb_column type="1_2"][et_pb_text admin_label="Text" background_layout="light" text_orientation="left"]<h2>The Solution</h2>
Vivamus ipsum velit, ullamcorper quis nibh non, molestie tempus sapien. Mauris ultrices, felis ut eleifend auctor, leo felis vehicula quam, ut accumsan augue nunc at nisl. Vivamus ipsum velit, ullamcorper quis nibh non, molestie tempus sapien. Mauris ultrices, felis ut eleifend auctor, leo felis vehicula quam, ut accumsan augue nunc at nisl.[/et_pb_text][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="on" specialty="off" inner_shadow="off" parallax="on"][et_pb_fullwidth_slider admin_label="Fullwidth Slider" show_arrows="on" show_pagination="on" auto="off" parallax="on"][et_pb_slide heading="Complete Corporate Identity" background_image="https://elegantthemesimages.com/images/premade/d2-placeholder-1920.png" background_color="#ffffff" alignment="center" background_layout="dark" /][et_pb_slide heading="We Rethought Everything" background_image="https://elegantthemesimages.com/images/premade/d2-placeholder-1920.png" background_color="#ffffff" alignment="center" background_layout="dark" /][/et_pb_fullwidth_slider][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_color="#353535" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="1_4"][et_pb_number_counter admin_label="Number Counter" title="Corporate Rebranding" number="70" percent_sign="on" background_layout="dark" counter_color="#2ea3f2" /][/et_pb_column][et_pb_column type="1_4"][et_pb_number_counter admin_label="Number Counter" title="Website Redesign" number="30" percent_sign="on" background_layout="dark" /][/et_pb_column][et_pb_column type="1_4"][et_pb_number_counter admin_label="Number Counter" title="Day Turnaround" number="60" percent_sign="off" background_layout="dark" /][/et_pb_column][et_pb_column type="1_4"][et_pb_number_counter admin_label="Number Counter" title="Amazing Result" number="1" percent_sign="off" background_layout="dark" /][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_color="#2ea3f2" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="1_2"][et_pb_divider admin_label="Divider" color="#ffffff" show_divider="off" height="90" /][et_pb_text admin_label="Text" background_layout="dark" text_orientation="left"]<h1>Mobile Site Boosted Sales By 50%</h1>[/et_pb_text][et_pb_blurb admin_label="Blurb" title="Mobile Refresh" url_new_window="off" use_icon="on" font_icon="" icon_color="#ffffff" use_circle="off" circle_color="#2caaca" use_circle_border="off" circle_border_color="#2caaca" icon_placement="left" animation="right" background_layout="dark" text_orientation="left"]The Challenge Vivamus ipsum velit, ullamcorper quis nibh non, molestie tempus sapien. Mauris ultrices, felis ut eleifend auctor[/et_pb_blurb][et_pb_blurb admin_label="Blurb" title="Rebuilt From the Inside Out" url_new_window="off" use_icon="on" font_icon="" icon_color="#ffffff" use_circle="off" circle_color="#2caaca" use_circle_border="off" circle_border_color="#2caaca" icon_placement="left" animation="right" background_layout="dark" text_orientation="left"]The Challenge Vivamus ipsum velit, ullamcorper quis nibh non, molestie tempus sapien. Mauris ultrices, felis ut eleifend auctor[/et_pb_blurb][et_pb_blurb admin_label="Blurb" title="Extensive Demographic Studies" url_new_window="off" use_icon="on" font_icon="" icon_color="#ffffff" use_circle="off" circle_color="#2caaca" use_circle_border="off" circle_border_color="#2caaca" icon_placement="left" animation="right" background_layout="dark" text_orientation="left"]The Challenge Vivamus ipsum velit, ullamcorper quis nibh non, molestie tempus sapien. Mauris ultrices, felis ut eleifend auctor[/et_pb_blurb][/et_pb_column][et_pb_column type="1_2"][et_pb_image admin_label="Image" src="https://elegantthemesimages.com/images/premade/mobile-lockup.png" url_new_window="off" animation="left" show_in_lightbox="off" /][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_color="#353535" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="4_4"][et_pb_divider admin_label="Divider" color="#ffffff" show_divider="off" height="60" /][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="1_2"][et_pb_counters admin_label="Bar Counters" background_layout="light" background_color="#2e2e2e"][et_pb_counter percent="80"]Mobile Sales[/et_pb_counter][et_pb_counter percent="50"]Website Traffic[/et_pb_counter][et_pb_counter percent="75"]Conversion Rate[/et_pb_counter][et_pb_counter percent="60"]Email Subscribers[/et_pb_counter][/et_pb_counters][/et_pb_column][et_pb_column type="1_2"][et_pb_cta admin_label="Call To Action" title="The Results Were Amazing" button_url="#" button_text="Live Project" use_background_color="off" background_color="#2ea3f2" background_layout="dark" text_orientation="left"]Vivamus ipsum velit, ullamcorper quis nibh non, molestie tempus sapien. Mauris ultrices, felis ut eleifend auctor, leo felis vehicula quam, ut accumsan augue nunc at nisl. Vivamus ipsum velit, ullamcorper quis nibh non, molestie tempus sapien. Mauris ultrices, felis ut eleifend auctor, leo felis vehicula quam, ut accumsan augue nunc at nisl.[/et_pb_cta][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="4_4"][et_pb_divider admin_label="Divider" color="#ffffff" show_divider="off" height="60" /][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="on" specialty="off" inner_shadow="off" parallax="off" background_color="#2e2e2e"][et_pb_fullwidth_portfolio admin_label="Fullwidth Portfolio" fullwidth="on" show_title="on" show_date="off" background_layout="dark" auto="off" title="Related Case Studies" /][/et_pb_section]
EOT
	);


	$layouts[] = array(
		'name'    => esc_html__( 'Product Features', 'et_builder' ),
		'content' => <<<EOT
[et_pb_section background_color="#132c47" inner_shadow="off" parallax="on"][et_pb_row][et_pb_column type="4_4"][et_pb_divider admin_label="Divider" color="#ffffff" show_divider="off" height="60" /][et_pb_text admin_label="Text" background_layout="dark" text_orientation="center"]<h1 style="font-size: 52px;">Product Features</h1>[/et_pb_text][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="4_4"][et_pb_image admin_label="Image" src="https://elegantthemesimages.com/images/premade/d2-placeholder-1920.png" url_new_window="off" animation="bottom" show_in_lightbox="off" /][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="1_4"][et_pb_blurb admin_label="Blurb" title="Advanced Page Builder" url_new_window="off" animation="top" background_layout="dark" text_orientation="center" use_icon="on" use_circle="on" circle_color="#0d2035" use_circle_border="off" circle_border_color="#2caaca" icon_placement="top" font_icon="" icon_color="#2ea3f2"]Divi will change the way you build websites forever. The advanced page builder makes it possible to build truly dynamic pages without learning code.[/et_pb_blurb][/et_pb_column][et_pb_column type="1_4"][et_pb_blurb admin_label="Blurb" title="Key Elements" url_new_window="off" animation="top" background_layout="dark" text_orientation="center" use_icon="on" use_circle="on" circle_color="#0d2035" use_circle_border="off" circle_border_color="#2caaca" icon_placement="top" font_icon="" icon_color="#2ad4e0"]The builder comes packed with tons of great modules, and more are on the way! Combine and arrange them in any order. The possibilities are countless.[/et_pb_blurb][/et_pb_column][et_pb_column type="1_4"][et_pb_blurb admin_label="Blurb" title="Target Audience" url_new_window="off" animation="top" background_layout="dark" text_orientation="center" use_icon="on" icon_color="#9633e8" use_circle="on" circle_color="#0d2035" use_circle_border="off" circle_border_color="#2caaca" icon_placement="top" font_icon=""]Divi’s layout has been designed with mobile devices in mind. No matter how you use it, and no matter how you view it, your website is going to look great.[/et_pb_blurb][/et_pb_column][et_pb_column type="1_4"][et_pb_blurb admin_label="Blurb" title="Strategy" url_new_window="off" image="https://elegantthemes.com/preview/Divi2/wp-content/uploads/2014/04/blurb-icon-updates.png" animation="top" background_layout="dark" text_orientation="center" use_icon="on" icon_color="#d85fd6" use_circle="on" circle_color="#0d2035" use_circle_border="off" circle_border_color="#2caaca" icon_placement="top" font_icon=""]Divi is here to stay, and you can rest easy knowing that our team will be updating and improving it for years to come. Build on top of a powerful foundation.[/et_pb_blurb][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" inner_shadow="off" parallax="off" module_id="builder"][et_pb_row][et_pb_column type="4_4"][et_pb_text admin_label="Text" background_layout="light" text_orientation="center"]<h1>Advanced Drag & Drop Builder</h1>
The Divi Builder was made with user experience at the forefront of its priorities. The way it is broken up into sections, rows, columns and widgets, really allows you to understand and edit the structure of your page. Your editing controls are pulled out of the main content area so that you get a clear and concise representation of how your modules fit into your page layout.[/et_pb_text][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="4_4"][et_pb_image admin_label="Image" src="https://elegantthemesimages.com/images/premade/d2-placeholder-1080px.jpg" url_new_window="off" animation="right" show_in_lightbox="off" /][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="on" specialty="off" inner_shadow="off" parallax="on" module_id="backgrounds"][et_pb_fullwidth_slider admin_label="Fullwidth Slider" show_arrows="on" show_pagination="on" auto="off" parallax="off"][et_pb_slide heading="All The Right Things" background_color="#ffffff" alignment="center" background_layout="dark" background_image="https://elegantthemesimages.com/images/premade/d2-placeholder-1920.png"]Vestibulum lobortis. Donec at euismod nibh, eu bibendum quam. Nullam non gravida purus, nec  eleifend tincidunt nisi.Vestibulum lobortis. Donec at euismod nibh, eu bibendum quam. Nullam non gravida purus, nec  eleifend tincidunt nisi.[/et_pb_slide][/et_pb_fullwidth_slider][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_color="#283139" inner_shadow="off" parallax="off" module_id="mobile"][et_pb_row][et_pb_column type="4_4"][et_pb_text admin_label="Text" background_layout="dark" text_orientation="center"]
<h1>Fully Responsive Layouts</h1>
We know that your website needs to be accessible and readable on all devices. We made Divi fully responsive so that your designs look great no matter what. With the builder, you design your desktop website, and we make sure that Divi does the heavy lifting for you.

[/et_pb_text][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="4_4"][et_pb_image admin_label="Image" src="https://elegantthemesimages.com/images/premade/d2-placeholder-1080px.jpg" url_new_window="off" animation="left" show_in_lightbox="off" /][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off" inner_shadow="off" parallax="off" module_id="layouts"][et_pb_row][et_pb_column type="1_2"][et_pb_image admin_label="Image" src="https://elegantthemesimages.com/images/premade/d2-placeholder-510px.png" url_new_window="off" animation="right" show_in_lightbox="off" /][/et_pb_column][et_pb_column type="1_2"][et_pb_divider admin_label="Divider" color="#ffffff" show_divider="off" height="70" /][et_pb_cta admin_label="Call To Action" title="Product Feature" button_url="#" button_text="Learn More" use_background_color="off" background_color="#2caaca" background_layout="light" text_orientation="left"]Divi Ships with a tone of great premade layouts to get you started with a homepage, a portfolio, an eCommerce Storefront, and much more! Check out the theme demo to preview a few of these premade layouts. We've even realeased layout packs along the way for portfolios and business focused websites.[/et_pb_cta][/et_pb_column][/et_pb_row][et_pb_row][et_pb_column type="1_2"][et_pb_divider admin_label="Divider" color="#ffffff" show_divider="off" height="40" /][et_pb_cta admin_label="Call To Action" title="Product Feature" button_url="#" button_text="Learn More" use_background_color="off" background_color="#2caaca" background_layout="light" text_orientation="right"]Divi Ships with a tone of great premade layouts to get you started with a homepage, a portfolio, an eCommerce Storefront, and much more! Check out the theme demo to preview a few of these premade layouts. We've even realeased layout packs along the way for portfolios and business focused websites.[/et_pb_cta][/et_pb_column][et_pb_column type="1_2"][et_pb_image admin_label="Image" src="https://elegantthemesimages.com/images/premade/d2-placeholder-510px.png" url_new_window="off" animation="left" show_in_lightbox="off" /][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fullwidth="off" specialty="off" background_color="#f74b47" inner_shadow="off" parallax="off"][et_pb_row][et_pb_column type="4_4"][et_pb_cta admin_label="Call To Action" title="Signup Today For Instant Access" button_url="#" button_text="Join Today" use_background_color="off" background_color="#2ea3f2" background_layout="dark" text_orientation="center"]Join today and get access to Divi, as well as our other countless themes and plugins.[/et_pb_cta][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
	);

	return $layouts;
}
endif;
