<?php

if ( ! defined( 'ET_BUILDER_PRODUCT_VERSION' ) ) {
	// Note, this will be updated automatically during grunt release task.
	define( 'ET_BUILDER_PRODUCT_VERSION', '3.0.60' );
}

if ( ! defined( 'ET_BUILDER_VERSION' ) ) {
	define( 'ET_BUILDER_VERSION', 0.7 );
}

if ( ! defined( 'ET_BUILDER_FORCE_CACHE_PURGE' ) ) {
	define( 'ET_BUILDER_FORCE_CACHE_PURGE', false );
}

// exclude predefined layouts from import
function et_remove_predefined_layouts_from_import( $posts ) {
	$processed_posts = $posts;

	if ( isset( $posts ) && is_array( $posts ) ) {
		$processed_posts = array();

		foreach ( $posts as $post ) {
			if ( isset( $post['postmeta'] ) && is_array( $post['postmeta'] ) ) {
				foreach ( $post['postmeta'] as $meta ) {
					if ( '_et_pb_predefined_layout' === $meta['key'] && 'on' === $meta['value'] )
						continue 2;
				}
			}

			$processed_posts[] = $post;
		}
	}

	return $processed_posts;
}
add_filter( 'wp_import_posts', 'et_remove_predefined_layouts_from_import', 5 );

// set the layout_type taxonomy to "layout" for layouts imported from old version of Divi.
function et_update_old_layouts_taxonomy( $posts ) {
	$processed_posts = $posts;

	if ( isset( $posts ) && is_array( $posts ) ) {
		$processed_posts = array();

		foreach ( $posts as $post ) {
			$update_built_for_post_type = false;

			if ( 'et_pb_layout' === $post['post_type'] ) {
				if ( ! isset( $post['terms'] ) ) {
					$post['terms'][] = array(
						'name'   => 'layout',
						'slug'   => 'layout',
						'domain' => 'layout_type'
					);
					$post['terms'][] = array(
						'name'   => 'not_global',
						'slug'   => 'not_global',
						'domain' => 'scope'
					);
				}

				$update_built_for_post_type = true;

				// check whether _et_pb_built_for_post_type custom field exists
				if ( ! empty( $post['postmeta'] ) ) {
					foreach ( $post['postmeta'] as $index => $value ) {
						if ( '_et_pb_built_for_post_type' === $value['key'] ) {
							$update_built_for_post_type = false;
						}
					}
				}
			}

			// set _et_pb_built_for_post_type value to 'page' if not exists
			if ( $update_built_for_post_type ) {
				$post['postmeta'][] = array(
					'key'   => '_et_pb_built_for_post_type',
					'value' => 'page',
				);
			}

			$processed_posts[] = $post;
		}
	}

	return $processed_posts;
}
add_filter( 'wp_import_posts', 'et_update_old_layouts_taxonomy', 10 );

// add custom filters for posts in the Divi Library
if ( ! function_exists( 'et_pb_add_layout_filters' ) ) :
function et_pb_add_layout_filters() {
	if ( isset( $_GET['post_type'] ) && 'et_pb_layout' === $_GET['post_type'] ) {
		$layout_categories = get_terms( 'layout_category' );
		$filter_category = array();
		$filter_category[''] = esc_html__( 'All Categories', 'et_builder' );

		if ( is_array( $layout_categories ) && ! empty( $layout_categories ) ) {
			foreach( $layout_categories as $category ) {
				$filter_category[$category->slug] = $category->name;
			}
		}

		$filter_layout_type = array(
			''        => esc_html__( 'All Layouts', 'et_builder' ),
			'module'  => esc_html__( 'Modules', 'et_builder' ),
			'row'     => esc_html__( 'Rows', 'et_builder' ),
			'section' => esc_html__( 'Sections', 'et_builder' ),
			'layout'  => esc_html__( 'Layouts', 'et_builder' ),
		);

		$filter_scope = array(
			''           => esc_html__( 'Global/not Global', 'et_builder' ),
			'global'     => esc_html__( 'Global', 'et_builder' ),
			'not_global' => esc_html__( 'not Global', 'et_builder' )
		);
		?>

		<select name="layout_type">
		<?php
			$selected = isset( $_GET['layout_type'] ) ? $_GET['layout_type'] : '';
			foreach ( $filter_layout_type as $value => $label ) {
				printf( '<option value="%1$s"%2$s>%3$s</option>',
					esc_attr( $value ),
					$value == $selected ? ' selected="selected"' : '',
					esc_html( $label )
				);
			} ?>
		</select>

		<select name="scope">
		<?php
			$selected = isset( $_GET['scope'] ) ? $_GET['scope'] : '';
			foreach ( $filter_scope as $value => $label ) {
				printf( '<option value="%1$s"%2$s>%3$s</option>',
					esc_attr( $value ),
					$value == $selected ? ' selected="selected"' : '',
					esc_html( $label )
				);
			} ?>
		</select>

		<select name="layout_category">
		<?php
			$selected = isset( $_GET['layout_category'] ) ? $_GET['layout_category'] : '';
			foreach ( $filter_category as $value => $label ) {
				printf( '<option value="%1$s"%2$s>%3$s</option>',
					esc_attr( $value ),
					$value == $selected ? ' selected="selected"' : '',
					esc_html( $label )
				);
			} ?>
		</select>
	<?php
	}
}
endif;
add_action( 'restrict_manage_posts', 'et_pb_add_layout_filters' );

// Add "Export Divi Layouts" button to the Divi Library page
if ( ! function_exists( 'et_pb_load_export_section' ) ) :
function et_pb_load_export_section(){
	$current_screen = get_current_screen();

	if ( 'edit-et_pb_layout' === $current_screen->id ) {
		// display wp error screen if library is disabled for current user
		if ( ! et_pb_is_allowed( 'divi_library' ) || ! et_pb_is_allowed( 'add_library' ) || ! et_pb_is_allowed( 'save_library' ) ) {
			wp_die( esc_html__( "you don't have sufficient permissions to access this page", 'et_builder' ) );
		}

		add_action( 'all_admin_notices', 'et_pb_export_layouts_interface' );
	}
}
endif;
add_action( 'load-edit.php', 'et_pb_load_export_section' );

// enqueue script to alter the options on library categories page
if ( ! function_exists( 'et_pb_edit_library_categories' ) ) :
function et_pb_edit_library_categories(){
	$current_screen = get_current_screen();

	if ( 'edit-layout_category' === $current_screen->id ) {
		// display wp error screen if library is disabled for current user
		if ( ! et_pb_is_allowed( 'divi_library' ) || ! et_pb_is_allowed( 'add_library' ) || ! et_pb_is_allowed( 'save_library' ) ) {
			wp_die( esc_html__( "you don't have sufficient permissions to access this page", 'et_builder' ) );
		}

		wp_enqueue_script( 'builder-library-category', ET_BUILDER_URI . '/scripts/library_category.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
	}
}
endif;
add_action( 'load-edit-tags.php', 'et_pb_edit_library_categories' );

// Check whether the library editor page should be displayed or not
function et_pb_check_library_permissions(){
	$current_screen = get_current_screen();

	if ( 'et_pb_layout' === $current_screen->id && ( ! et_pb_is_allowed( 'divi_library' ) || ! et_pb_is_allowed( 'save_library' ) ) ) {
		// display wp error screen if library is disabled for current user
		wp_die( esc_html__( "you don't have sufficient permissions to access this page", 'et_builder' ) );
	}
}
add_action( 'load-post.php', 'et_pb_check_library_permissions' );

// exclude premade layouts from the list of all templates in the library.
if ( ! function_exists( 'exclude_premade_layouts_library' ) ) :
function exclude_premade_layouts_library( $query ) {
	global $pagenow;
	$current_post_type = get_query_var( 'post_type' );

	if ( is_admin() && 'edit.php' === $pagenow && $current_post_type && 'et_pb_layout' === $current_post_type ) {
		$meta_query = array(
			array(
				'key'     => '_et_pb_predefined_layout',
				'value'   => 'on',
				'compare' => 'NOT EXISTS',
			),
		);

		$used_built_for_post_types = et_pb_get_used_built_for_post_types();
		if ( isset( $_GET['built_for'] ) && count( $used_built_for_post_types ) > 1 ) {
			$built_for_post_type = sanitize_text_field( $_GET['built_for'] );
			// get array of all standard post types if built_for is one of them
			$built_for_post_type_processed = in_array( $built_for_post_type, et_pb_get_standard_post_types() ) ? et_pb_get_standard_post_types() : $built_for_post_type;

			if ( in_array( $built_for_post_type, $used_built_for_post_types ) ) {
				$meta_query[] = array(
					'key'     => '_et_pb_built_for_post_type',
					'value'   => $built_for_post_type_processed,
					'compare' => 'IN',
				);
			}
		}

		$query->set( 'meta_query', $meta_query );
	}

	return $query;
}
endif;
add_action( 'pre_get_posts', 'exclude_premade_layouts_library' );

if ( ! function_exists( 'exclude_premade_layouts_library_count' ) ) :
/**
 * Post count for "mine" in post table relies to fixed value set by WP_Posts_List_Table->user_posts_count
 * Thus, exclude_premade_layouts_library() action doesn't automatically exclude premade layout and
 * it has to be late filtered via this exclude_premade_layouts_library_count()
 *
 * @see WP_Posts_List_Table->user_posts_count to see how mine post value is retrieved
 *
 * @param array
 * @return array
 */
function exclude_premade_layouts_library_count( $views ) {
	if ( isset( $views['mine'] ) ) {
		$current_user_id = get_current_user_id();

		if ( isset( $_GET['author'] ) && ( $_GET['author'] == $current_user_id ) ) {
			$class = 'current';

			// Reuse current $wp_query global
			global $wp_query;

			$mine_posts_count = $wp_query->found_posts;
		} else {
			$class = '';

			// Use WP_Query instead of plain MySQL SELECT because the custom field filtering uses
			// GROUP BY which needs FOUND_ROWS() and this has been automatically handled by WP_Query
			$query = new WP_Query( array(
				'post_type'  => 'et_pb_layout',
				'author'     => $current_user_id,
				'meta_query' => array(
					'key'     => '_et_pb_predefined_layout',
					'value'   => 'on',
					'compare' => 'NOT EXISTS',
				),
			) );

			$mine_posts_count = $query->found_posts;
		}

		$url = add_query_arg(
			array(
				'post_type' => 'et_pb_layout',
				'author'    => $current_user_id,
			),
			'edit.php'
		);

		$views['mine'] = sprintf(
			'<a href="%1$s" class="%2$s">%3$s <span class="count">(%4$s)</span></a>',
			esc_url( $url ),
			esc_attr( $class ),
			esc_html__( 'Mine', 'et_builder' ),
			esc_html( intval( $mine_posts_count ) )
		);
	}

	return $views;
}
endif;
add_filter( 'views_edit-et_pb_layout', 'exclude_premade_layouts_library_count' );

if ( ! function_exists( 'et_pb_get_font_icon_symbols' ) ) :
function et_pb_get_font_icon_symbols() {
	$symbols = array( '&amp;#x21;', '&amp;#x22;', '&amp;#x23;', '&amp;#x24;', '&amp;#x25;', '&amp;#x26;', '&amp;#x27;', '&amp;#x28;', '&amp;#x29;', '&amp;#x2a;', '&amp;#x2b;', '&amp;#x2c;', '&amp;#x2d;', '&amp;#x2e;', '&amp;#x2f;', '&amp;#x30;', '&amp;#x31;', '&amp;#x32;', '&amp;#x33;', '&amp;#x34;', '&amp;#x35;', '&amp;#x36;', '&amp;#x37;', '&amp;#x38;', '&amp;#x39;', '&amp;#x3a;', '&amp;#x3b;', '&amp;#x3c;', '&amp;#x3d;', '&amp;#x3e;', '&amp;#x3f;', '&amp;#x40;', '&amp;#x41;', '&amp;#x42;', '&amp;#x43;', '&amp;#x44;', '&amp;#x45;', '&amp;#x46;', '&amp;#x47;', '&amp;#x48;', '&amp;#x49;', '&amp;#x4a;', '&amp;#x4b;', '&amp;#x4c;', '&amp;#x4d;', '&amp;#x4e;', '&amp;#x4f;', '&amp;#x50;', '&amp;#x51;', '&amp;#x52;', '&amp;#x53;', '&amp;#x54;', '&amp;#x55;', '&amp;#x56;', '&amp;#x57;', '&amp;#x58;', '&amp;#x59;', '&amp;#x5a;', '&amp;#x5b;', '&amp;#x5c;', '&amp;#x5d;', '&amp;#x5e;', '&amp;#x5f;', '&amp;#x60;', '&amp;#x61;', '&amp;#x62;', '&amp;#x63;', '&amp;#x64;', '&amp;#x65;', '&amp;#x66;', '&amp;#x67;', '&amp;#x68;', '&amp;#x69;', '&amp;#x6a;', '&amp;#x6b;', '&amp;#x6c;', '&amp;#x6d;', '&amp;#x6e;', '&amp;#x6f;', '&amp;#x70;', '&amp;#x71;', '&amp;#x72;', '&amp;#x73;', '&amp;#x74;', '&amp;#x75;', '&amp;#x76;', '&amp;#x77;', '&amp;#x78;', '&amp;#x79;', '&amp;#x7a;', '&amp;#x7b;', '&amp;#x7c;', '&amp;#x7d;', '&amp;#x7e;', '&amp;#xe000;', '&amp;#xe001;', '&amp;#xe002;', '&amp;#xe003;', '&amp;#xe004;', '&amp;#xe005;', '&amp;#xe006;', '&amp;#xe007;', '&amp;#xe009;', '&amp;#xe00a;', '&amp;#xe00b;', '&amp;#xe00c;', '&amp;#xe00d;', '&amp;#xe00e;', '&amp;#xe00f;', '&amp;#xe010;', '&amp;#xe011;', '&amp;#xe012;', '&amp;#xe013;', '&amp;#xe014;', '&amp;#xe015;', '&amp;#xe016;', '&amp;#xe017;', '&amp;#xe018;', '&amp;#xe019;', '&amp;#xe01a;', '&amp;#xe01b;', '&amp;#xe01c;', '&amp;#xe01d;', '&amp;#xe01e;', '&amp;#xe01f;', '&amp;#xe020;', '&amp;#xe021;', '&amp;#xe022;', '&amp;#xe023;', '&amp;#xe024;', '&amp;#xe025;', '&amp;#xe026;', '&amp;#xe027;', '&amp;#xe028;', '&amp;#xe029;', '&amp;#xe02a;', '&amp;#xe02b;', '&amp;#xe02c;', '&amp;#xe02d;', '&amp;#xe02e;', '&amp;#xe02f;', '&amp;#xe030;', '&amp;#xe103;', '&amp;#xe0ee;', '&amp;#xe0ef;', '&amp;#xe0e8;', '&amp;#xe0ea;', '&amp;#xe101;', '&amp;#xe107;', '&amp;#xe108;', '&amp;#xe102;', '&amp;#xe106;', '&amp;#xe0eb;', '&amp;#xe010;', '&amp;#xe105;', '&amp;#xe0ed;', '&amp;#xe100;', '&amp;#xe104;', '&amp;#xe0e9;', '&amp;#xe109;', '&amp;#xe0ec;', '&amp;#xe0fe;', '&amp;#xe0f6;', '&amp;#xe0fb;', '&amp;#xe0e2;', '&amp;#xe0e3;', '&amp;#xe0f5;', '&amp;#xe0e1;', '&amp;#xe0ff;', '&amp;#xe031;', '&amp;#xe032;', '&amp;#xe033;', '&amp;#xe034;', '&amp;#xe035;', '&amp;#xe036;', '&amp;#xe037;', '&amp;#xe038;', '&amp;#xe039;', '&amp;#xe03a;', '&amp;#xe03b;', '&amp;#xe03c;', '&amp;#xe03d;', '&amp;#xe03e;', '&amp;#xe03f;', '&amp;#xe040;', '&amp;#xe041;', '&amp;#xe042;', '&amp;#xe043;', '&amp;#xe044;', '&amp;#xe045;', '&amp;#xe046;', '&amp;#xe047;', '&amp;#xe048;', '&amp;#xe049;', '&amp;#xe04a;', '&amp;#xe04b;', '&amp;#xe04c;', '&amp;#xe04d;', '&amp;#xe04e;', '&amp;#xe04f;', '&amp;#xe050;', '&amp;#xe051;', '&amp;#xe052;', '&amp;#xe053;', '&amp;#xe054;', '&amp;#xe055;', '&amp;#xe056;', '&amp;#xe057;', '&amp;#xe058;', '&amp;#xe059;', '&amp;#xe05a;', '&amp;#xe05b;', '&amp;#xe05c;', '&amp;#xe05d;', '&amp;#xe05e;', '&amp;#xe05f;', '&amp;#xe060;', '&amp;#xe061;', '&amp;#xe062;', '&amp;#xe063;', '&amp;#xe064;', '&amp;#xe065;', '&amp;#xe066;', '&amp;#xe067;', '&amp;#xe068;', '&amp;#xe069;', '&amp;#xe06a;', '&amp;#xe06b;', '&amp;#xe06c;', '&amp;#xe06d;', '&amp;#xe06e;', '&amp;#xe06f;', '&amp;#xe070;', '&amp;#xe071;', '&amp;#xe072;', '&amp;#xe073;', '&amp;#xe074;', '&amp;#xe075;', '&amp;#xe076;', '&amp;#xe077;', '&amp;#xe078;', '&amp;#xe079;', '&amp;#xe07a;', '&amp;#xe07b;', '&amp;#xe07c;', '&amp;#xe07d;', '&amp;#xe07e;', '&amp;#xe07f;', '&amp;#xe080;', '&amp;#xe081;', '&amp;#xe082;', '&amp;#xe083;', '&amp;#xe084;', '&amp;#xe085;', '&amp;#xe086;', '&amp;#xe087;', '&amp;#xe088;', '&amp;#xe089;', '&amp;#xe08a;', '&amp;#xe08b;', '&amp;#xe08c;', '&amp;#xe08d;', '&amp;#xe08e;', '&amp;#xe08f;', '&amp;#xe090;', '&amp;#xe091;', '&amp;#xe092;', '&amp;#xe0f8;', '&amp;#xe0fa;', '&amp;#xe0e7;', '&amp;#xe0fd;', '&amp;#xe0e4;', '&amp;#xe0e5;', '&amp;#xe0f7;', '&amp;#xe0e0;', '&amp;#xe0fc;', '&amp;#xe0f9;', '&amp;#xe0dd;', '&amp;#xe0f1;', '&amp;#xe0dc;', '&amp;#xe0f3;', '&amp;#xe0d8;', '&amp;#xe0db;', '&amp;#xe0f0;', '&amp;#xe0df;', '&amp;#xe0f2;', '&amp;#xe0f4;', '&amp;#xe0d9;', '&amp;#xe0da;', '&amp;#xe0de;', '&amp;#xe0e6;', '&amp;#xe093;', '&amp;#xe094;', '&amp;#xe095;', '&amp;#xe096;', '&amp;#xe097;', '&amp;#xe098;', '&amp;#xe099;', '&amp;#xe09a;', '&amp;#xe09b;', '&amp;#xe09c;', '&amp;#xe09d;', '&amp;#xe09e;', '&amp;#xe09f;', '&amp;#xe0a0;', '&amp;#xe0a1;', '&amp;#xe0a2;', '&amp;#xe0a3;', '&amp;#xe0a4;', '&amp;#xe0a5;', '&amp;#xe0a6;', '&amp;#xe0a7;', '&amp;#xe0a8;', '&amp;#xe0a9;', '&amp;#xe0aa;', '&amp;#xe0ab;', '&amp;#xe0ac;', '&amp;#xe0ad;', '&amp;#xe0ae;', '&amp;#xe0af;', '&amp;#xe0b0;', '&amp;#xe0b1;', '&amp;#xe0b2;', '&amp;#xe0b3;', '&amp;#xe0b4;', '&amp;#xe0b5;', '&amp;#xe0b6;', '&amp;#xe0b7;', '&amp;#xe0b8;', '&amp;#xe0b9;', '&amp;#xe0ba;', '&amp;#xe0bb;', '&amp;#xe0bc;', '&amp;#xe0bd;', '&amp;#xe0be;', '&amp;#xe0bf;', '&amp;#xe0c0;', '&amp;#xe0c1;', '&amp;#xe0c2;', '&amp;#xe0c3;', '&amp;#xe0c4;', '&amp;#xe0c5;', '&amp;#xe0c6;', '&amp;#xe0c7;', '&amp;#xe0c8;', '&amp;#xe0c9;', '&amp;#xe0ca;', '&amp;#xe0cb;', '&amp;#xe0cc;', '&amp;#xe0cd;', '&amp;#xe0ce;', '&amp;#xe0cf;', '&amp;#xe0d0;', '&amp;#xe0d1;', '&amp;#xe0d2;', '&amp;#xe0d3;', '&amp;#xe0d4;', '&amp;#xe0d5;', '&amp;#xe0d6;', '&amp;#xe0d7;', '&amp;#xe600;', '&amp;#xe601;', '&amp;#xe602;', '&amp;#xe603;', '&amp;#xe604;', '&amp;#xe605;', '&amp;#xe606;', '&amp;#xe607;', '&amp;#xe608;', '&amp;#xe609;', '&amp;#xe60a;', '&amp;#xe60b;', '&amp;#xe60c;', '&amp;#xe60d;', '&amp;#xe60e;', '&amp;#xe60f;', '&amp;#xe610;', '&amp;#xe611;', '&amp;#xe612;', '&amp;#xe008;', );

	$symbols = apply_filters( 'et_pb_font_icon_symbols', $symbols );

	return $symbols;
}
endif;

if ( ! function_exists( 'et_pb_get_font_icon_list' ) ) :
function et_pb_get_font_icon_list() {
	$output = is_customize_preview() ? et_pb_get_font_icon_list_items() : '<%= window.et_builder.font_icon_list_template() %>';

	$output = sprintf( '<ul class="et_font_icon">%1$s</ul>', $output );

	return $output;
}
endif;

if ( ! function_exists( 'et_pb_get_font_icon_list_items' ) ) :
function et_pb_get_font_icon_list_items() {
	$output = '';

	$symbols = et_pb_get_font_icon_symbols();

	foreach ( $symbols as $symbol ) {
		$output .= sprintf( '<li data-icon="%1$s"></li>', esc_attr( $symbol ) );
	}

	return $output;
}
endif;

if ( ! function_exists( 'et_pb_font_icon_list' ) ) :
function et_pb_font_icon_list() {
	echo et_pb_get_font_icon_list();
}
endif;

if ( ! function_exists( 'et_pb_get_font_down_icon_symbols' ) ) :
function et_pb_get_font_down_icon_symbols() {
	$symbols = array( '&amp;#x22;', '&amp;#x33;', '&amp;#x37;', '&amp;#x3b;', '&amp;#x3f;', '&amp;#x43;', '&amp;#x47;', '&amp;#xe03a;', '&amp;#xe044;', '&amp;#xe048;', '&amp;#xe04c;' );

	return $symbols;
}
endif;

if ( ! function_exists( 'et_pb_get_font_down_icon_list' ) ) :
function et_pb_get_font_down_icon_list() {
	$output = is_customize_preview() ? et_pb_get_font_down_icon_list_items() : '<%= window.et_builder.font_down_icon_list_template() %>';

	$output = sprintf( '<ul class="et_font_icon">%1$s</ul>', $output );

	return $output;
}
endif;

if ( ! function_exists( 'et_pb_get_font_down_icon_list_items' ) ) :
function et_pb_get_font_down_icon_list_items() {
	$output = '';

	$symbols = et_pb_get_font_down_icon_symbols();

	foreach ( $symbols as $symbol ) {
		$output .= sprintf( '<li data-icon="%1$s"></li>', esc_attr( $symbol ) );
	}

	return $output;
}
endif;

if ( ! function_exists( 'et_pb_font_down_icon_list' ) ) :
function et_pb_font_down_icon_list() {
	echo et_pb_get_font_down_icon_list();
}
endif;

/**
 * Processes font icon value for use on front-end
 *
 * @param string $font_icon        Font Icon ( exact value or in %%index_number%% format ).
 * @param string $symbols_function Optional. Name of the function that gets an array of font icon values.
 *                                 et_pb_get_font_icon_symbols function is used by default.
 * @return string $font_icon       Font Icon value
 */
if ( ! function_exists( 'et_pb_process_font_icon' ) ) :
function et_pb_process_font_icon( $font_icon, $symbols_function = 'default' ) {
	// the exact font icon value is saved
	if ( 1 !== preg_match( "/^%%/", trim( $font_icon ) ) ) {
		return $font_icon;
	}

	// the font icon value is saved in the following format: %%index_number%%
	$icon_index   = (int) str_replace( '%', '', $font_icon );
	$icon_symbols = 'default' === $symbols_function ? et_pb_get_font_icon_symbols() : call_user_func( $symbols_function );
	$font_icon    = isset( $icon_symbols[ $icon_index ] ) ? $icon_symbols[ $icon_index ] : '';

	return $font_icon;
}
endif;

if ( ! function_exists( 'et_builder_accent_color' ) ) :
function et_builder_accent_color( $default_color = '#7EBEC5' ) {
	$accent_color = ! et_is_builder_plugin_active() ? et_get_option( 'accent_color', $default_color ) : $default_color;

	return apply_filters( 'et_builder_accent_color', $accent_color );
}
endif;

if ( ! function_exists( 'et_builder_get_text_orientation_options' ) ) :
function et_builder_get_text_orientation_options() {
	$text_orientation_options = array(
		'left'      => esc_html__( 'Left', 'et_builder' ),
		'center'    => esc_html__( 'Center', 'et_builder' ),
		'right'     => esc_html__( 'Right', 'et_builder' ),
		'justified' => esc_html__( 'Justified', 'et_builder' ),
	);

	if ( is_rtl() ) {
		$text_orientation_options = array(
			'right'  => esc_html__( 'Right', 'et_builder' ),
			'center' => esc_html__( 'Center', 'et_builder' ),
		);
	}

	return apply_filters( 'et_builder_text_orientation_options', $text_orientation_options );
}
endif;

if ( ! function_exists( 'et_builder_get_gallery_settings' ) ) :
function et_builder_get_gallery_settings() {
	$output = sprintf(
		'<input type="button" class="button button-upload et-pb-gallery-button" value="%1$s" />',
		esc_attr__( 'Update Gallery', 'et_builder' )
	);

	return $output;
}
endif;

if ( ! function_exists( 'et_builder_get_nav_menus_options' ) ) :
function et_builder_get_nav_menus_options() {
	$nav_menus_options = array( 'none' => esc_html__( 'Select a menu', 'et_builder' ) );

	$nav_menus = wp_get_nav_menus( array( 'orderby' => 'name' ) );
	foreach ( (array) $nav_menus as $_nav_menu ) {
		$nav_menus_options[ $_nav_menu->term_id ] = $_nav_menu->name;
	}

	return apply_filters( 'et_builder_nav_menus_options', $nav_menus_options );
}
endif;

if ( ! function_exists( 'et_builder_generate_center_map_setting' ) ) :
function et_builder_generate_center_map_setting() {
	return '<div id="et_pb_map_center_map" class="et-pb-map et_pb_map_center_map"></div>';
}
endif;

if ( ! function_exists( 'et_builder_generate_pin_zoom_level_input' ) ) :
function et_builder_generate_pin_zoom_level_input() {
	return '<input class="et_pb_zoom_level" type="hidden" value="18" />';
}
endif;

/**
 * Define conditional tags needed for component's backend parser. This is used for FB's public facing update
 * mechanism to pass conditional tag to admin-ajax.php for component which relies to backend parsing. Backend
 * uses this conditional tags' key as well for sanitization
 *
 * @return array
 */
function et_fb_conditional_tag_params() {
	$is_rtl = is_rtl();

	if ( 'on' === et_get_option( 'divi_disable_translations', 'off' ) ) {
		$is_rtl = false;
	}

	$conditional_tags = array(
		'is_front_page'               => is_front_page(),
		'is_home_page'                => is_home() || is_front_page(),
		'is_search'                   => is_search(),
		'is_single'                   => is_single(),
		'is_singular'                 => is_singular(),
		'is_singular_project'         => is_singular( 'project' ),
		'is_rtl'                      => $is_rtl,
		'et_is_builder_plugin_active' => et_is_builder_plugin_active(),
		'is_user_logged_in'           => is_user_logged_in(),
		'et_is_ab_testing_active'     => et_is_ab_testing_active() ? 'yes' : 'no',
	);

	return apply_filters( 'et_fb_conditional_tag_params', $conditional_tags );
}


function _et_fb_get_app_preferences_defaults() {
	$app_preferences = array(
		'settings_bar_location'    => array(
			'type'    => 'string',
			'default' => 'bottom',
		),
		'modal_snap_location'    => array(
			'type'    => 'string',
			'default' => 'left',
		),
		'modal_snap'             => array(
			'type'    => 'bool',
			'default' => false,
		),
		'modal_fullscreen'       => array(
			'type'    => 'bool',
			'default' => false,
		),
		'modal_dimension_width'  => array(
			'type'    => 'int',
			'default' => 400,
		),
		'modal_dimension_height' => array(
			'type'    => 'int',
			'default' => 400,
		),
		'modal_position_x'       => array(
			'type'    => 'int',
			'default' => 30,
		),
		'modal_position_y'       => array(
			'type'    => 'int',
			'default' => 50,
		),
	);

	return apply_filters( 'et_fb_app_preferences_defaults', $app_preferences );
}

function et_fb_app_preferences() {
	$app_preferences = _et_fb_get_app_preferences_defaults();

	foreach ( $app_preferences as $preference_key => $preference ) {
		$app_preferences[ $preference_key ]['value'] = et_get_option( 'et_fb_pref_' . $preference_key, $preference['default'] );
	}

	return apply_filters( 'et_fb_app_preferences', $app_preferences );
}

/**
 * Define current-page related data that are needed by frontend builder. Backend parser also uses this
 * to sanitize updated value for computed data
 *
 * @return array
 */
function et_fb_current_page_params() {
	global $post, $authordata, $paged;

	// Get current page url
	$current_url  = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

	// Fallback for preview
	if ( empty( $authordata ) && isset( $post->post_author ) ) {
		$authordata = get_userdata( $post->post_author );
	}

	// Get comment count
	$comment_count = isset( $post->ID ) ? get_comments_number( $post->ID ) : 0;

	// WordPress' _n() only supports singular n plural, thus we do comment count to text manually
	if ( $comment_count === 0 ) {
		$comment_count_text = __( 'No Comments', 'et_builder' );
	} elseif ( $comment_count === 1 ) {
		$comment_count_text = __( '1 Comment', 'et_builder' );
	} else {
		$comment_count_text = sprintf( __( '%d Comments', 'et_builder' ), $comment_count );
	}

	// Get current page paginated data
	$et_paged = is_front_page() ? get_query_var( 'page' ) : get_query_var( 'paged' );

	$current_page = array(
		'url'                      => esc_url( $current_url ),
		'permalink'                => esc_url( remove_query_arg( 'et_fb', $current_url ) ),
		'backendBuilderUrl'        => esc_url( sprintf( admin_url('/post.php?post=%d&action=edit'), get_the_ID() ) ),
		'id'                       => isset( $post->ID ) ? $post->ID : false,
		'title'                    => esc_html( get_the_title() ),
		'thumbnailUrl'             => isset( $post->ID ) ? esc_url( get_the_post_thumbnail_url( $post->ID, 'large' ) ) : '',
		'authorName'               => esc_html( get_the_author() ),
		'authorUrl'                => isset( $authordata->ID ) && isset( $authordata->user_nicename ) ? esc_html( get_author_posts_url( $authordata->ID, $authordata->user_nicename ) ) : false,
		'authorUrlTitle'           => sprintf( esc_html__( 'Posts by %s', 'et_builder' ), get_the_author() ),
		'date'                     => intval( get_the_time('U') ),
		'categories'               => isset( $post->ID ) ? et_pb_get_post_categories( $post->ID ) : array(),
		'commentsPopup'            => esc_html( $comment_count_text ),
		'paged'                    => is_front_page() ? $et_paged : $paged,
		'post_modified'            => isset( $post->ID ) ? esc_attr( $post->post_modified ) : '',
	);

	return apply_filters( 'et_fb_current_page_params', $current_page );
}

function et_pb_process_computed_property() {
	if ( !isset( $_POST['et_pb_process_computed_property_nonce'] ) || !wp_verify_nonce( $_POST['et_pb_process_computed_property_nonce'], 'et_pb_process_computed_property_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	if ( ! isset( $_POST['depends_on'], $_POST['conditional_tags'], $_POST['current_page'] ) ) {
		// Shouldn't even be a possibility, but...
		die( -1 );
	}

	$depends_on       = $_POST['depends_on'];
	$conditional_tags = $_POST['conditional_tags'];
	$current_page     = $_POST['current_page'];

	// $_POST['depends_on'] is a single dimensional assoc array created by jQuery.ajax data param, sanatize each key and value, they will both be strings
	foreach ( $depends_on as $key => $value ) {

		// correctly sanitize the strings with %date variable. sanitize_text_field will strip the '%da' and '%date' will be saved as 'te'.
		$prepared_value = str_replace( '%date', '___et-fb-date___', $value );
		$sanitized_value = str_replace( '___et-fb-date___', '%date', sanitize_text_field( $prepared_value ) );

		$depends_on[ sanitize_text_field( $key ) ] = $sanitized_value;

	}
	$module_slug       = sanitize_text_field( $_POST['module_type'] );
	$post_type         = sanitize_text_field( $_POST['post_type'] );
	$computed_property = sanitize_text_field( $_POST['computed_property'] );

	// get all fields for module
	$fields = ET_Builder_Element::get_module_fields( $post_type, $module_slug );

	// make sure only valid fields are being passed through
	$depends_on       = array_intersect_key( $depends_on, $fields );
	$conditional_tags = array_intersect_key( $conditional_tags, et_fb_conditional_tag_params() );
	$current_page     = array_intersect_key( $current_page, et_fb_current_page_params() );

	// computed property field
	$field = $fields[ $computed_property ];

	$callback = $field['computed_callback'];

	if ( is_callable( $callback ) ) {
		die( json_encode( $callback( $depends_on, $conditional_tags, $current_page ) ) );
	} else {
		die( -1 );
	}
}
add_action( 'wp_ajax_et_pb_process_computed_property', 'et_pb_process_computed_property' );

function et_fb_process_to_shortcode( $object, $options = array(), $library_item_type = '' ) {
	$output = '';
	$_object = array();

	// do not proceed if $object is empty
	if ( empty( $object ) ) {
		return '';
	}

	$font_icon_fields = isset( $options['post_type'] ) ? ET_Builder_Element::get_font_icon_fields( $options['post_type'] ) : false;
	$structure_types = ET_Builder_Element::get_structure_module_slugs();

	if ( in_array( $library_item_type, array( 'module', 'row' ) ) ) {
		$excluded_elements = array();

		switch ( $library_item_type ) {
			case 'module':
				$excluded_elements = array( 'et_pb_section', 'et_pb_row', 'et_pb_column' );
				break;
			case 'row':
				$excluded_elements = array( 'et_pb_section' );
				break;
		}

		foreach ( $object as $item ) {
			// do not proceed if $item is empty
			if ( empty( $item ) ) {
				continue;
			}

			while ( in_array( $item['type'], $excluded_elements ) ) {
				$item = $item['content'][0];
			}

			$_object[] = $item;
		}
	} else {
		$_object = $object;
	}

	foreach ( $_object as $item ) {
		// do not proceed if $item is empty
		if ( empty( $item ) ) {
			continue;
		}
		$attributes = '';
		$content = '';
		$type = sanitize_text_field( $item['type'] );
		$type = esc_attr( $type );

		if ( ! empty( $item['raw_child_content'] ) ) {
			$content = stripslashes( $item['raw_child_content'] );
		}

		foreach ( $item['attrs'] as $attribute => $value ) {
			// ignore computed fields
			if ( '__' == substr( $attribute, 0, 2 ) ) {
				continue;
			}

			// Sanitize attribute
			$attribute = sanitize_text_field( $attribute );

			// Sanitize input properly
			if ( isset( $font_icon_fields[ $item['type'] ][ $attribute ] ) ) {
				$value = esc_attr( $value );
			} else {
				// allow the use of '<', '>' characters within Custom CSS settings
				if ( 0 !== strpos( $attribute, 'custom_css_' ) ) {
					$replace_pairs = array (
						'>' => '&gt;',
						'<' => '&lt;',
					);

					$value = strtr( $value, $replace_pairs );
				}
			}

			// handle content
			if ( in_array( $attribute, array('content_new', 'raw_content') ) ) {
				// do not override the content if item has raw_child_content
				if ( empty( $item['raw_child_content'] ) ) {
					$content = $value;

					if ( 'raw_content' == $attribute ) {
						$content = esc_html( $content );
					}

					$content = trim( $content );

					if ( !empty( $content ) && 'content_new' == $attribute ) {
						$content = "\n\n" . $content . "\n\n";
					}
				}
			} else if ( '' !== $value ) {
				// TODO, should we check for and handle default here? probably done in FB alredy...

				// Make sure double quotes are encoded, before adding values to shortcode
				$value = str_ireplace('"', '%22', $value);

				// Encode backslash for custom CSS-related attributes
				if ( 0 === strpos( $attribute, 'custom_css_' ) ) {
					$value = str_ireplace('\\', '%92', $value);
				}

				$attributes .= ' ' . esc_attr( $attribute ) . '="' . et_esc_previously( $value ) . '"';
			}
		}

		$attributes = str_replace( array( '[', ']' ), array( '%91', '%93' ), $attributes );

		// prefix sections with a fb_built attr flag
		if ( 'et_pb_section' == $type ) {
			$attributes = ' fb_built="1"' . $attributes;
		}

		// build shortcode
		// start the opening tag
		$output .= '[' . $type . $attributes;

		// close the opening tag, depending on self closing
		if ( empty( $content ) && ! isset( $item['content'] ) && ! in_array( $type, $structure_types ) ) {
			$open_tag_only = true;
			$output .= ' /]';
		} else {
			$open_tag_only = false;
			$output .= ']';
		}

		// if applicable, add inner content and close tag
		if ( ! $open_tag_only ) {
			if ( 'et_pb_section' === $type && isset( $item['attrs'] ) && isset( $item['attrs']['fullwidth'] ) && 'on' !== $item['attrs']['fullwidth'] && isset( $item['attrs']['specialty'] ) && 'on' !== $item['attrs']['specialty'] && ( ! isset( $item['content'] ) || ! is_array( $item['content'] ) ) ) {
				// insert empty row if saving empty Regular section to make it work correctly in BB
				$output .= '[et_pb_row admin_label="Row"][/et_pb_row]';
			} else if ( isset( $item['content'] ) && is_array( $item['content'] ) ) {
				$output .= et_fb_process_to_shortcode( $item['content'], $options );
			} else {
				if ( !empty( $content ) ) {
					$output .= $content;
				} else {
					if ( isset( $item['content'] ) ) {
						$_content = $item['content'];

						$_content = str_replace( '\\', '\\\\', $_content );

						// content of code modules should be escaped
						$output .= in_array( $type, array( 'et_pb_code', 'et_pb_fullwidth_code' ) ) ? esc_html( $_content ) : $_content;
					} else {
						$output .= '';
					}

				}
			}

			// add the closing tag
			$output .= '[/' . $type . ']';
		}
	}

	return $output;
}

function et_fb_ajax_render_shortcode() {
	if ( !isset( $_POST['et_pb_render_shortcode_nonce'] ) || !wp_verify_nonce( $_POST['et_pb_render_shortcode_nonce'], 'et_pb_render_shortcode_nonce' ) ) {
		wp_send_json_error();
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error();
	}

	$shortcode = et_fb_process_to_shortcode( $_POST['object'], $_POST['options'] );
	$output = do_shortcode( $shortcode );

	$styles = ET_Builder_Element::get_style();

	if ( ! empty( $styles ) ) {
		$output .= sprintf(
			'<style type="text/css" class="et-builder-advanced-style">
				%1$s
			</style>',
			$styles
		);
	}

	wp_send_json_success( $output );
}
add_action( 'wp_ajax_et_fb_ajax_render_shortcode', 'et_fb_ajax_render_shortcode' );

function et_fb_current_user_can_save( $post_id, $status = '' ) {
	if ( is_page( $post_id ) ) {
		if ( ! current_user_can( 'edit_pages' ) ) {
			return false;
		}

		if ( ! current_user_can( 'publish_pages' ) && 'publish' === $status ) {
			return false;
		}

		if ( ! current_user_can( 'edit_published_pages' ) && 'publish' === get_post_status( $post_id ) ) {
			return false;
		}

		if ( ! current_user_can( 'edit_others_pages' ) && ! current_user_can( 'edit_page', $post_id ) ) {
			return false;
		}
	} else {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return false;
		}

		if ( ! current_user_can( 'publish_posts' ) && 'publish' === $status ) {
			return false;
		}

		if ( ! current_user_can( 'edit_published_posts' ) && 'publish' === get_post_status( $post_id ) ) {
			return false;
		}

		if ( ! current_user_can( 'edit_others_posts' ) && ! current_user_can( 'edit_post', $post_id ) ) {
			return false;
		}
	}

	return true;
}

function et_fb_ajax_drop_autosave() {
	if ( !isset( $_POST['et_fb_drop_autosave_nonce'] ) || !wp_verify_nonce( $_POST['et_fb_drop_autosave_nonce'], 'et_fb_drop_autosave_nonce' ) ) {
		wp_send_json_error();
	}

	$post_id = absint( $_POST['post_id'] );

	if ( ! et_fb_current_user_can_save( $post_id ) ) {
		wp_send_json_error();
	}

	$post_author = get_current_user_id();
	$autosave = wp_get_post_autosave( $post_id, $post_author );

	$autosave_deleted = false;

	// delete builder settings autosave
	delete_post_meta( $post_id, "_et_builder_settings_autosave_{$post_author}" );

	if ( !empty( $autosave ) ) {
		wp_delete_post_revision( $autosave->ID );
		$autosave = wp_get_post_autosave( $post_id, $post_author );
		if ( empty( $autosave ) ) {
			$autosave_deleted = true;
		}
	} else {
		$autosave_deleted = true;
	}

	if ( $autosave_deleted ) {
		wp_send_json_success();
	} else {
		wp_send_json_error();
	}
}
add_action( 'wp_ajax_et_fb_ajax_drop_autosave', 'et_fb_ajax_drop_autosave' );

function et_fb_ajax_save() {
	if ( !isset( $_POST['et_fb_save_nonce'] ) || !wp_verify_nonce( $_POST['et_fb_save_nonce'], 'et_fb_save_nonce' ) ) {
		wp_send_json_error();
	}

	$post_id = absint( $_POST['post_id'] );

	if ( ! et_fb_current_user_can_save( $post_id, $_POST['options']['status'] ) ) {
		wp_send_json_error();
	}

	$shortcode_data = json_decode( stripslashes( $_POST['modules'] ), true );
	$layout_type = '';

	if ( isset( $_POST['layout_type'] ) ) {
		$layout_type = sanitize_text_field( $_POST['layout_type'] );
	}

	$post_content = et_fb_process_to_shortcode( $shortcode_data, $_POST['options'], $layout_type );

	// Store a copy of the sanitized post content in case wpkses alters it since that
	// would cause our check at the end of this function to fail.
	$sanitized_content = sanitize_post_field( 'post_content', $post_content, $post_id, 'db' );

	$update = wp_update_post( array(
		'ID'           => $post_id,
		'post_content' => $post_content,
		'post_status'  => esc_attr( $_POST['options']['status'] ),
	) );

	// update Global modules with selective sync
	if ( 'module' === $layout_type && isset( $_POST['unsyncedGlobalSettings'] ) && 'none' !== $_POST['unsyncedGlobalSettings'] ) {
		$unsynced_options = stripslashes( $_POST['unsyncedGlobalSettings'] );
		update_post_meta( $post_id, '_et_pb_excluded_global_options', sanitize_text_field( $unsynced_options ) );
	}

	// check if there is an autosave that is newer
	$post_author = get_current_user_id();
	// Store one autosave per author. If there is already an autosave, overwrite it.
	$autosave = wp_get_post_autosave( $post_id, $post_author );

	if ( !empty( $autosave ) ) {
		wp_delete_post_revision( $autosave->ID );
	}

	if ( isset($_POST['settings'] ) && is_array( $_POST['settings'] ) ) {
		et_builder_update_settings( $_POST['settings'], $post_id );
	}

	if ( isset($_POST['preferences'] ) && is_array( $_POST['preferences'] ) ) {
		$app_preferences = _et_fb_get_app_preferences_defaults();

		foreach( $app_preferences as $preference_key => $preference_data ) {

			$preference_value = isset( $_POST['preferences'][ $preference_key ] ) && isset( $_POST['preferences'][ $preference_key ]['value'] ) ? $_POST['preferences'][ $preference_key ]['value'] : $preference_data['default'];

			// sanitize based on type
			switch ( $preference_data['type'] ) {
				case 'int':
					$preference_value = absint( $preference_value );
					break;
				case 'bool':
					$preference_value = $preference_value === 'true' ? 'true' : 'false';
					break;
				default:
					$preference_value = sanitize_text_field( $preference_value );
					break;
			}

			et_update_option( 'et_fb_pref_' . $preference_key, $preference_value );
		}
	}

	if ( $update ) {
		if ( ! empty( $_POST['et_builder_version'] ) ) {
			update_post_meta( $post_id, '_et_builder_version', sanitize_text_field( $_POST['et_builder_version'] ) );
		}

		// Get saved post, verify its content against the one that is being sent
		$saved_post = get_post( $update );
		$saved_verification = $saved_post->post_content === stripslashes( $sanitized_content );

		wp_send_json_success( array(
			'status'            => get_post_status( $update ),
			'save_verification' => apply_filters( 'et_fb_ajax_save_verification_result', $saved_verification ),
		) );
	} else {
		wp_send_json_error();
	}
}
add_action( 'wp_ajax_et_fb_ajax_save', 'et_fb_ajax_save' );

function et_fb_save_layout() {
	if ( ! wp_verify_nonce( $_POST['et_fb_save_library_modules_nonce'], 'et_fb_save_library_modules_nonce' ) ){
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	if ( empty( $_POST['et_layout_name'] ) ) {
		die( -1 );
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
		'layout_content'       => isset( $_POST['et_layout_content'] ) ? et_fb_process_to_shortcode( json_decode( stripslashes( $_POST['et_layout_content'] ), true ) ) : '',
		'layout_name'          => isset( $_POST['et_layout_name'] ) ? sanitize_text_field( $_POST['et_layout_name'] ) : '',
	);

	$new_layout_meta = et_pb_submit_layout( $args );
	die( $new_layout_meta );
}
add_action( 'wp_ajax_et_fb_save_layout', 'et_fb_save_layout' );

function et_fb_prepare_shortcode() {
	if ( !isset( $_POST['et_fb_prepare_shortcode_nonce'] ) || !wp_verify_nonce( $_POST['et_fb_prepare_shortcode_nonce'], 'et_fb_prepare_shortcode_nonce' ) ) {
		wp_send_json_error();
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$result = isset( $_POST['et_page_content'] ) ? et_fb_process_to_shortcode( json_decode( stripslashes( $_POST['et_page_content'] ), true ) ) : '';

	die( json_encode( array( 'shortcode' => $result ) ) );
}
add_action( 'wp_ajax_et_fb_prepare_shortcode', 'et_fb_prepare_shortcode' );

function et_fb_update_layout() {
	if ( ! wp_verify_nonce( $_POST['et_fb_save_library_modules_nonce'], 'et_fb_save_library_modules_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$post_id = isset( $_POST['et_template_post_id'] ) ? $_POST['et_template_post_id'] : '';
	$post_content = json_decode( stripslashes( $_POST['et_layout_content'] ), true );
	$new_content = isset( $_POST['et_layout_content'] ) ? et_fb_process_to_shortcode( et_pb_builder_post_content_capability_check( $post_content ) ) : '';
	$excluded_global_options = isset( $_POST['et_excluded_global_options'] ) ? stripslashes( $_POST['et_excluded_global_options'] ) : array();
	$is_saving_global_module = isset( $_POST['et_saving_global_module'] ) ? sanitize_text_field( $_POST['et_saving_global_module'] ) : '';

	if ( '' !== $post_id ) {
		$update = array(
			'ID'           => absint( $post_id ),
			'post_content' => $new_content,
		);

		wp_update_post( $update );

		// update list of unsynced options for global module
		if ( 'true' === $is_saving_global_module ) {
			update_post_meta( absint( $post_id ), '_et_pb_excluded_global_options', sanitize_text_field( $excluded_global_options ) );
		}
	}

	die();
}
add_action( 'wp_ajax_et_fb_update_layout', 'et_fb_update_layout' );

if ( ! function_exists( 'et_builder_include_categories_option' ) ) :
function et_builder_include_categories_option( $args = array() ) {
	$defaults = apply_filters( 'et_builder_include_categories_defaults', array (
		'use_terms' => true,
		'term_name' => 'project_category',
	) );

	$args = wp_parse_args( $args, $defaults );

	$output = "\t" . "<% var et_pb_include_categories_temp = typeof et_pb_include_categories !== 'undefined' ? et_pb_include_categories.split( ',' ) : []; %>" . "\n";

	if ( $args['use_terms'] ) {
		$cats_array = get_terms( $args['term_name'] );
	} else {
		$cats_array = get_categories( apply_filters( 'et_builder_get_categories_args', 'hide_empty=0' ) );
	}

	if ( empty( $cats_array ) ) {
		$output = '<p>' . esc_html__( "You currently don't have any projects assigned to a category.", 'et_builder' ) . '</p>';
	}

	foreach ( $cats_array as $category ) {
		$contains = sprintf(
			'<%%= _.contains( et_pb_include_categories_temp, "%1$s" ) ? checked="checked" : "" %%>',
			esc_html( $category->term_id )
		);

		$output .= sprintf(
			'%4$s<label><input type="checkbox" name="et_pb_include_categories" value="%1$s"%3$s> %2$s</label><br/>',
			esc_attr( $category->term_id ),
			esc_html( $category->name ),
			$contains,
			"\n\t\t\t\t\t"
		);
	}

	$output = '<div id="et_pb_include_categories">' . $output . '</div>';

	return apply_filters( 'et_builder_include_categories_option_html', $output );
}
endif;

if ( ! function_exists( 'et_builder_include_categories_shop_option' ) ) :
function et_builder_include_categories_shop_option( $args = array() ) {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return '';
	}

	$defaults = apply_filters( 'et_builder_include_categories_shop_defaults', array (
		'use_terms' => true,
		'term_name' => 'product_category',
	) );

	$args = wp_parse_args( $args, $defaults );

	$output = "\t" . "<% var et_pb_include_categories_shop_temp = typeof et_pb_include_categories !== 'undefined' ? et_pb_include_categories.split( ',' ) : []; %>" . "\n";

	$cats_array = $args['use_terms'] ? get_terms( $args['term_name'] ) : get_categories( apply_filters( 'et_builder_get_categories_shop_args', 'hide_empty=0' ) );

	$output .= '<div id="et_pb_include_categories">';

	foreach ( $cats_array as $category ) {
		$contains = sprintf(
			'<%%= _.contains( et_pb_include_categories_shop_temp, "%1$s" ) ? checked="checked" : "" %%>',
			esc_html( $category->slug )
		);

		$output .= sprintf(
			'%4$s<label><input type="checkbox" name="et_pb_include_categories" value="%1$s"%3$s> %2$s</label><br/>',
			esc_attr( $category->slug ),
			esc_html( $category->name ),
			$contains,
			"\n\t\t\t\t\t"
		);
	}

	$output .= '</div>';

	return apply_filters( 'et_builder_include_categories_option_html', $output );
}
endif;

if ( ! function_exists( 'et_divi_get_projects' ) ) :
function et_divi_get_projects( $args = array() ) {
	$default_args = array(
		'post_type' => 'project',
	);
	$args = wp_parse_args( $args, $default_args );
	return new WP_Query( $args );
}
endif;

if ( ! function_exists( 'et_pb_extract_items' ) ) :
function et_pb_extract_items( $content ) {
	$output = $first_character = '';
	$lines = array_filter( explode( "\n", str_replace( array( '<p>', '</p>', '<br />' ), "\n", $content ) ) );
	foreach ( $lines as $line ) {
		$line = trim( $line );
		if ( '&#8211;' === substr( $line, 0, 7 ) ) {
			$line = '-' . substr( $line, 7 );
		}
		if ( '' === $line ) {
			continue;
		}
		$first_character = $line[0];
		if ( in_array( $first_character, array( '-', '+' ) ) ) {
			$line = trim( substr( $line, 1 ) );
		}
		$output .= sprintf( '[et_pb_pricing_item available="%2$s"]%1$s[/et_pb_pricing_item]',
			$line,
			( '-' === $first_character ? 'off' : 'on' )
		);
	}
	return do_shortcode( $output );
}
endif;

if ( ! function_exists( 'et_builder_process_range_value' ) ) :
function et_builder_process_range_value( $range, $option_type = '' ) {
	$range = trim( $range );
	$range_digit = floatval( $range );
	$range_string = str_replace( $range_digit, '', (string) $range );

	if ( '' === $range_string ) {
		$range_string = 'line_height' === $option_type && 3 >= $range_digit ? 'em' : 'px';
	}

	$result = $range_digit . $range_string;

	return apply_filters( 'et_builder_processed_range_value', $result, $range, $range_string );
}
endif;

if ( ! function_exists( 'et_builder_get_border_styles' ) ) :
function et_builder_get_border_styles() {
	$styles = array(
		'solid'  => esc_html__( 'Solid', 'et_builder' ),
		'dotted' => esc_html__( 'Dotted', 'et_builder' ),
		'dashed' => esc_html__( 'Dashed', 'et_builder' ),
		'double' => esc_html__( 'Double', 'et_builder' ),
		'groove' => esc_html__( 'Groove', 'et_builder' ),
		'ridge'  => esc_html__( 'Ridge', 'et_builder' ),
		'inset'  => esc_html__( 'Inset', 'et_builder' ),
		'outset' => esc_html__( 'Outset', 'et_builder' ),
	);

	return apply_filters( 'et_builder_border_styles', $styles );
}
endif;

if ( ! function_exists( 'et_builder_font_options' ) ) :
function et_builder_font_options() {
	$options         = array();

	$default_options = array( 'default' => array(
		'name' => esc_html__( 'Default', 'et_builder' ),
	) );
	$fonts           = array_merge( $default_options, et_builder_get_fonts() );

	foreach ( $fonts as $font_name => $font_settings ) {
		$options[ $font_name ] = 'default' !== $font_name ? $font_name : $font_settings['name'];
	}

	return $options;
}
endif;

if ( ! function_exists( 'et_builder_get_font_options_items' ) ) :
function et_builder_get_font_options_items() {
	$output = '';
	$font_options = et_builder_font_options();

	foreach ( $font_options as $key => $value ) {
		$output .= sprintf(
			'<option value="%1$s">%2$s</option>',
			esc_attr( $key ),
			esc_html( $value )
		);
	}

	return $output;
}
endif;

if ( ! function_exists( 'et_builder_set_element_font' ) ) :
function et_builder_set_element_font( $font, $use_important = false, $default = false ) {
	$style = '';

	if ( '' === $font ) {
		return $style;
	}

	$font_values = explode( '|', $font );
	$default = ! $default ? "||||" : $default;
	$font_values_default = explode( '|', $default );

	if ( ! empty( $font_values ) ) {
		$font_values       = array_map( 'trim', $font_values );
		$font_name         = $font_values[0];
		$is_font_bold      = 'on' === $font_values[1] ? true : false;
		$is_font_italic    = 'on' === $font_values[2] ? true : false;
		$is_font_uppercase = 'on' === $font_values[3] ? true : false;
		$is_font_underline = 'on' === $font_values[4] ? true : false;

		$font_name_default         = $font_values_default[0];
		$is_font_bold_default      = 'on' === $font_values_default[1] ? true : false;
		$is_font_italic_default    = 'on' === $font_values_default[2] ? true : false;
		$is_font_uppercase_default = 'on' === $font_values_default[3] ? true : false;
		$is_font_underline_default = 'on' === $font_values_default[4] ? true : false;

		if ( '' !== $font_name && $font_name_default !== $font_name ) {
			et_builder_enqueue_font( $font_name );

			$style .= et_builder_get_font_family( $font_name, $use_important ) . ' ';
		}

		$style .= et_builder_set_element_font_style( 'font-weight', $is_font_bold_default, $is_font_bold, 'normal', 'bold', $use_important );

		$style .= et_builder_set_element_font_style( 'font-style', $is_font_italic_default, $is_font_italic, 'none', 'italic', $use_important );

		$style .= et_builder_set_element_font_style( 'text-transform', $is_font_uppercase_default, $is_font_uppercase, 'none', 'uppercase', $use_important );

		$style .= et_builder_set_element_font_style( 'text-decoration', $is_font_underline_default, $is_font_underline, 'none', 'underline', $use_important );

		$style = rtrim( $style );
	}

	return $style;
}
endif;

if ( ! function_exists( 'et_builder_set_element_font_style' ) ) :
function et_builder_set_element_font_style( $property, $default, $value, $property_default, $property_value, $use_important ) {
	$style = "";

	if ( $value && ! $default ) {
		$style = sprintf(
			'%1$s: %2$s%3$s; ',
			esc_html( $property ),
			$property_value,
			( $use_important ? ' !important' : '' )
		);
	} elseif ( ! $value && $default ) {
		$style = sprintf(
			'%1$s: %2$s%3$s; ',
			esc_html( $property ),
			$property_default,
			( $use_important ? ' !important' : '' )
		);
	}

	return $style;
}
endif;

if ( ! function_exists( 'et_builder_get_element_style_css' ) ) :
function et_builder_get_element_style_css( $value, $property = 'margin', $use_important = false ) {
	$style = '';

	$values = explode( '|', $value );

	if ( ! empty( $values ) ) {
		$element_style = '';
		$i = 0;
		$values = array_map( 'trim', $values );
		$positions = array(
			'top',
			'right',
			'bottom',
			'left',
		);

		foreach ( $values as $element_style_value ) {
			if ( '' !== $element_style_value ) {
				$element_style .= sprintf(
					'%3$s-%1$s: %2$s%4$s; ',
					esc_attr( $positions[ $i ] ),
					esc_attr( et_builder_process_range_value( $element_style_value ) ),
					esc_attr( $property ),
					( $use_important ? ' !important' : '' )
				);
			}

			$i++;
		}

		$style .= rtrim( $element_style );
	}

	return $style;
}
endif;

if ( ! function_exists( 'et_builder_enqueue_font' ) ) :
function et_builder_enqueue_font( $font_name ) {
	$fonts = et_builder_get_fonts();
	$websafe_fonts = et_builder_get_websafe_fonts();
	$protocol = is_ssl() ? 'https' : 'http';

	// Skip enqueueing if font name is not found. Possibly happen if support for particular font need to be dropped
	if ( ! array_key_exists( $font_name, $fonts ) ) {
		return;
	}

	// Skip enqueueing for websafe fonts
	if ( array_key_exists( $font_name, $websafe_fonts ) ) {
		return;
	}

	if ( isset( $fonts[ $font_name ]['parent_font'] ) ){
		$font_name = $fonts[ $font_name ]['parent_font'];
	}
	$font_character_set = $fonts[ $font_name ]['character_set'];

	$query_args = array(
		'family' => sprintf( '%s:%s',
			str_replace( ' ', '+', $font_name ),
			apply_filters( 'et_builder_set_styles', $fonts[ $font_name ]['styles'], $font_name )
		),
		'subset' => apply_filters( 'et_builder_set_character_set', $font_character_set, $font_name ),
	);

	$font_name_slug = sprintf(
		'et-gf-%1$s',
		strtolower( str_replace( ' ', '-', $font_name ) )
	);

	wp_enqueue_style( $font_name_slug, esc_url( add_query_arg( $query_args, "$protocol://fonts.googleapis.com/css" ) ), array(), null );
}
endif;

if ( ! function_exists( 'et_pb_get_page_custom_css' ) ) :
function et_pb_get_page_custom_css() {
	$page_id          = apply_filters( 'et_pb_page_id_custom_css', get_the_ID() );
	$exclude_defaults = true;
	$page_settings    = ET_Builder_Settings::get_values( 'page', $page_id, $exclude_defaults );
	$selector_prefix  = et_is_builder_plugin_active() ? ' .et_divi_builder #et_builder_outer_content' : '';

	$output = get_post_meta( $page_id, '_et_pb_custom_css', true );

	if ( isset( $page_settings['et_pb_light_text_color'] ) ) {
		$output .= sprintf(
			'%2$s .et_pb_bg_layout_dark { color: %1$s !important; }',
			esc_html( $page_settings['et_pb_light_text_color'] ),
			esc_html( $selector_prefix )
		);
	}

	if ( isset( $page_settings['et_pb_dark_text_color'] ) ) {
		$output .= sprintf(
			'%2$s .et_pb_bg_layout_light { color: %1$s !important; }',
			esc_html( $page_settings['et_pb_dark_text_color'] ),
			esc_html( $selector_prefix )
		);
	}

	if ( isset( $page_settings['et_pb_content_area_background_color'] ) ) {
		$content_area_bg_selector = et_is_builder_plugin_active() ? $selector_prefix : ' .page.et_pb_pagebuilder_layout #main-content';
		$output .= sprintf(
			'%1$s { background-color: %2$s; }',
			esc_html( $content_area_bg_selector ),
			esc_html( $page_settings['et_pb_content_area_background_color'] )
		);
	}

	if ( isset( $page_settings['et_pb_section_background_color'] ) ) {
		$output .= sprintf(
			'%2$s .et_pb_section { background-color: %1$s; }',
			esc_html( $page_settings['et_pb_section_background_color'] ),
			esc_html( $selector_prefix )
		);
	}

	return apply_filters( 'et_pb_page_custom_css', $output );
}
endif;

if ( ! function_exists( 'et_pb_video_oembed_data_parse' ) ) :
function et_pb_video_oembed_data_parse( $return, $data, $url ) {
	if ( isset( $data->thumbnail_url ) ) {
		return esc_url( str_replace( array('https://', 'http://'), '//', $data->thumbnail_url ), array('http') );
	} else {
		return false;
	}
}
endif;

if ( ! function_exists( 'et_pb_check_oembed_provider' ) ) {
function et_pb_check_oembed_provider( $url ) {
	require_once( ABSPATH . WPINC . '/class-oembed.php' );
	$oembed = _wp_oembed_get_object();
	return $oembed->get_provider( esc_url( $url ), array( 'discover' => false ) );
}
}

if ( ! function_exists( 'et_pb_set_video_oembed_thumbnail_resolution' ) ) :
function et_pb_set_video_oembed_thumbnail_resolution( $image_src, $resolution = 'default' ) {
	// Replace YouTube video thumbnails to high resolution if the high resolution image exists.
	if ( 'high' === $resolution && false !== strpos( $image_src,  'hqdefault.jpg' ) ) {
		$high_res_image_src = str_replace( 'hqdefault.jpg', 'maxresdefault.jpg', $image_src );
		$protocol = is_ssl() ? 'https://' : 'http://';
		$processed_image_url = esc_url( str_replace( '//', $protocol, $high_res_image_src ), array('http', 'https') );
		$response = wp_remote_get( $processed_image_url, array( 'timeout' => 30 ) );

		// Youtube doesn't guarantee that high res image exists for any video, so we need to check whether it exists and fallback to default image in case of error
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return $image_src;
		}

		return $high_res_image_src;
	}

	return $image_src;
}
endif;

function et_builder_widgets_init(){
	$et_pb_widgets = get_theme_mod( 'et_pb_widgets' );

	if ( $et_pb_widgets['areas'] ) {
		foreach ( $et_pb_widgets['areas'] as $id => $name ) {
			register_sidebar( array(
				'name' => sanitize_text_field( $name ),
				'id' => sanitize_text_field( $id ),
				'before_widget' => '<div id="%1$s" class="et_pb_widget %2$s">',
				'after_widget' => '</div> <!-- end .et_pb_widget -->',
				'before_title' => '<h4 class="widgettitle">',
				'after_title' => '</h4>',
			) );
		}
	}
}

// call the widgets init at 'init' hook if Divi Builder plugin active
// this is needed because plugin loads the Divi builder at 'init' hook and 'widgets_init' is too early.
if ( et_is_builder_plugin_active() ) {
	add_action( 'init', 'et_builder_widgets_init', 20 );
} else {
	add_action( 'widgets_init', 'et_builder_widgets_init' );
}

function et_builder_get_widget_areas_list() {
	global $wp_registered_sidebars;

	$widget_areas = array();

	foreach ( $wp_registered_sidebars as $sidebar_key => $sidebar ) {
		$widget_areas[ $sidebar_key ] = array(
			'name' => $sidebar[ 'name' ]
		);
	}

	return $widget_areas;
}

if ( ! function_exists( 'et_builder_get_widget_areas' ) ) :
function et_builder_get_widget_areas() {
	$wp_registered_sidebars = et_builder_get_widget_areas_list();
	$et_pb_widgets = get_theme_mod( 'et_pb_widgets' );

	$output = '<select name="et_pb_area" id="et_pb_area">';

	foreach ( $wp_registered_sidebars as $id => $options ) {
		$selected = sprintf(
			'<%%= typeof( et_pb_area ) !== "undefined" && "%1$s" === et_pb_area ?  " selected=\'selected\'" : "" %%>',
			esc_html( $id )
		);

		$output .= sprintf(
			'<option value="%1$s"%2$s>%3$s</option>',
			esc_attr( $id ),
			$selected,
			esc_html( $options['name'] )
		);
	}

	$output .= '</select>';

	return $output;
}
endif;

if ( ! function_exists( 'et_pb_export_layouts_interface' ) ) :
function et_pb_export_layouts_interface() {
	if ( ! current_user_can( 'export' ) )
		wp_die( __( 'You do not have sufficient permissions to export the content of this site.', 'et_builder' ) );
	?>
	<a href="<?php echo admin_url( 'edit-tags.php?taxonomy=layout_category' ); ?>" id="et_load_category_page"><?php _e( 'Manage Categories', 'et_builder' ); ?></a>
	<?php
	echo et_core_portability_link( 'et_builder_layouts', array( 'class' => 'et-pb-portability-button' ) );
}
endif;

add_action( 'export_wp', 'et_pb_edit_export_query' );
function et_pb_edit_export_query() {
	add_filter( 'query', 'et_pb_edit_export_query_filter' );
}

function et_pb_edit_export_query_filter( $query ) {
	// Apply filter only once
	remove_filter( 'query', 'et_pb_edit_export_query_filter') ;

	global $wpdb;

	$content = ! empty( $_GET['content'] ) ? $_GET['content'] : '';

	if ( ET_BUILDER_LAYOUT_POST_TYPE !== $content ) {
		return $query;
	}

	$sql = '';
	$i = 0;
	$possible_types = array(
		'layout',
		'section',
		'row',
		'module',
		'fullwidth_section',
		'specialty_section',
		'fullwidth_module',
	);

	foreach ( $possible_types as $template_type ) {
		$selected_type = 'et_pb_template_' . $template_type;

		if ( isset( $_GET[ $selected_type ] ) ) {
			if ( 0 === $i ) {
				$sql = " AND ( {$wpdb->term_relationships}.term_taxonomy_id = %d";
			} else {
				$sql .= " OR {$wpdb->term_relationships}.term_taxonomy_id = %d";
			}

			$sql_args[] = (int) $_GET[ $selected_type ];

			$i++;
		}
	}

	if ( '' !== $sql ) {
		$sql  .= ' )';
		$sql = sprintf(
			'SELECT ID FROM %4$s
			 INNER JOIN %3$s ON ( %4$s.ID = %3$s.object_id )
			 WHERE %4$s.post_type = "%1$s"
			 AND %4$s.post_status != "auto-draft"
			 %2$s',
			ET_BUILDER_LAYOUT_POST_TYPE,
			$sql,
			$wpdb->term_relationships,
			$wpdb->posts
		);
		$query = $wpdb->prepare( $sql, $sql_args );
	}

	return $query;
}

function et_pb_setup_theme(){
	add_action( 'add_meta_boxes', 'et_pb_add_custom_box' );
}
add_action( 'init', 'et_pb_setup_theme', 11 );

/**
* The page builders require the WP Heartbeat script in order to function. We ensure the heartbeat
* is loaded with the page builders by scheduling this callback to run right before scripts
* are output to the footer. {@see 'admin_enqueue_scripts', 'wp_footer'}
*/
function et_builder_maybe_ensure_heartbeat_script() {
	// Don't perform any actions on 'wp_footer' if VB is not active
	if ( 'wp_footer' === current_filter() && empty( $_GET['et_fb'] ) ) {
		return;
	}

	// We have to check both 'registered' AND 'enqueued' to cover cases where heartbeat has been
	// de-registered because 'enqueued' will return `true` for a de-registered script at this stage.
	$heartbeat_okay = wp_script_is( 'heartbeat', 'registered' ) && wp_script_is( 'heartbeat', 'enqueued' );
	$autosave_okay  = wp_script_is( 'autosave', 'registered' ) && wp_script_is( 'autosave', 'enqueued' );

	if ( $heartbeat_okay && $autosave_okay ) {
		return;
	}

	$suffix = SCRIPT_DEBUG ? '' : '.min';

	if ( ! $heartbeat_okay ) {
		$heartbeat_src = "/wp-includes/js/heartbeat{$suffix}.js";
		wp_enqueue_script( 'heartbeat', $heartbeat_src, array( 'jquery' ), false, true );
		wp_localize_script( 'heartbeat', 'heartbeatSettings', apply_filters( 'heartbeat_settings', array() ) );
	}

	if ( ! $autosave_okay ) {
		$autosave_src = "/wp-includes/js/autosave{$suffix}.js";
		wp_enqueue_script( 'autosave', $autosave_src, array( 'heartbeat' ), false, true );
	}
}

// Enqueue dashicons in front-end if they are not enqueued (that happens when not logged in as admin)
function et_builder_maybe_enqueue_dashicons() {
	if ( wp_style_is( 'dashicons' ) ) {
		return;
	}

	wp_enqueue_style( 'dashicons' );
}
add_action( 'admin_print_scripts-post-new.php', 'et_builder_maybe_ensure_heartbeat_script', 9 );
add_action( 'admin_print_scripts-post.php', 'et_builder_maybe_ensure_heartbeat_script', 9 );
add_action( 'wp_enqueue_scripts', 'et_builder_maybe_enqueue_dashicons', 19 );
add_action( 'wp_footer', 'et_builder_maybe_ensure_heartbeat_script', 19 );

function et_builder_set_post_type( $post_type = '' ) {
	global $et_builder_post_type, $post;

	$et_builder_post_type = ! empty( $post_type ) ? $post_type : $post->post_type;
}

function et_pb_metabox_settings_save_details( $post_id, $post ){
	global $pagenow;

	if ( 'post.php' != $pagenow ) return $post_id;

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return $post_id;

	$post_type = get_post_type_object( $post->post_type );
	if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	if ( ! isset( $_POST['et_pb_settings_nonce'] ) || ! wp_verify_nonce( $_POST['et_pb_settings_nonce'], basename( __FILE__ ) ) )
		return $post_id;


	if ( isset( $_POST['et_pb_use_builder'] ) ) {
		update_post_meta( $post_id, '_et_pb_use_builder', sanitize_text_field( $_POST['et_pb_use_builder'] ) );

		if ( ! empty( $_POST['et_builder_version'] ) ) {
			update_post_meta( $post_id, '_et_builder_version', sanitize_text_field( $_POST['et_builder_version'] ) );
		}
	} else {
		delete_post_meta( $post_id, '_et_pb_use_builder' );
		delete_post_meta( $post_id, '_et_builder_version' );
	}



	// Only run split testing-related update sequence if split testing is allowed
	if ( et_pb_is_allowed( 'ab_testing' ) ) {
		// Delete split test settings' autosave
		delete_post_meta( $post_id, '_et_pb_use_ab_testing_draft' );
		delete_post_meta( $post_id, '_et_pb_ab_subjects_draft' );

		if ( isset( $_POST['et_pb_use_ab_testing'] ) && in_array( $_POST['et_pb_use_ab_testing'], array( 'on', 'off' ) ) ) {
			update_post_meta( $post_id, '_et_pb_use_ab_testing', sanitize_text_field( $_POST['et_pb_use_ab_testing'] ) );

			if ( 'on' === $_POST['et_pb_use_ab_testing'] ) {
				if ( ! get_post_meta( $post_id, '_et_pb_ab_testing_id', true ) ) {
					update_post_meta( $post_id, '_et_pb_ab_testing_id', rand() );
				}
			} else {
				delete_post_meta( $post_id, '_et_pb_ab_testing_id' );
				delete_post_meta( $post_id, 'et_pb_subjects_cache' );
				et_pb_ab_remove_stats( $post_id );
			}
		} else {
			delete_post_meta( $post_id, '_et_pb_use_ab_testing' );
			delete_post_meta( $post_id, '_et_pb_ab_testing_id' );
		}

		if ( isset( $_POST['et_pb_ab_subjects'] ) && '' !== $_POST['et_pb_ab_subjects'] ) {
			update_post_meta( $post_id, '_et_pb_ab_subjects', sanitize_text_field( et_prevent_duplicate_item( $_POST['et_pb_ab_subjects'], ',') ) );
		} else {
			delete_post_meta( $post_id, '_et_pb_ab_subjects' );
		}

		if ( isset( $_POST['et_pb_ab_goal_module'] ) && '' !== $_POST['et_pb_ab_goal_module'] ) {
			update_post_meta( $post_id, '_et_pb_ab_goal_module', sanitize_text_field( $_POST['et_pb_ab_goal_module'] ) );
		} else {
			delete_post_meta( $post_id, '_et_pb_ab_goal_module' );
		}

		if ( isset( $_POST['et_pb_ab_stats_refresh_interval'] ) && '' !== $_POST['et_pb_ab_stats_refresh_interval'] ) {
			update_post_meta( $post_id, '_et_pb_ab_stats_refresh_interval', sanitize_text_field( $_POST['et_pb_ab_stats_refresh_interval'] ) );
		} else {
			delete_post_meta( $post_id, '_et_pb_ab_stats_refresh_interval' );
		}
	}

	if ( isset( $_POST['et_pb_old_content'] ) ) {
		update_post_meta( $post_id, '_et_pb_old_content', $_POST['et_pb_old_content'] );
	} else {
		delete_post_meta( $post_id, '_et_pb_old_content' );
	}

	et_builder_update_settings( null, $post_id );

	if ( isset( $_POST['et_pb_unsynced_global_attrs'] ) ) {
		$unsynced_options_array = stripslashes( sanitize_text_field( $_POST['et_pb_unsynced_global_attrs'] ) );
		update_post_meta( $post_id, '_et_pb_excluded_global_options', $unsynced_options_array );
	}

	return $post_id;
}
add_action( 'save_post', 'et_pb_metabox_settings_save_details', 10, 2 );

function et_pb_set_et_saved_cookie( $post_id, $post ) {
	global $pagenow;

	if ( 'post.php' != $pagenow ) return $post_id;

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return $post_id;

	$post_type = get_post_type_object( $post->post_type );
	if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
		return $post_id;
	}

	if ( ! isset( $_POST['et_pb_settings_nonce'] ) || ! wp_verify_nonce( $_POST['et_pb_settings_nonce'], basename( __FILE__ ) ) ) {
		return $post_id;
	}

	// delete
	setcookie( 'et-saving-post-' . $post_id . '-bb', 'bb', time() - DAY_IN_SECONDS, SITECOOKIEPATH, false, is_ssl() );
	// set
	setcookie( 'et-saved-post-' . $post_id . '-bb', 'bb', time() + MINUTE_IN_SECONDS * 5, SITECOOKIEPATH, false, is_ssl() );
}

add_action( 'save_post', 'et_pb_set_et_saved_cookie', 10, 2 );

/**
 * Handling title-less & content-less switching from backend builder to normal editor
 * @param int   $maybe_empty whether the wp_insert_post content is empty or not
 * @param array $postarr all $_POST data that is being passed to wp_insert_post()
 * @return int  whether wp_insert_post content should be considered empty or not
 */
function et_pb_ensure_builder_activation_switching( $maybe_empty, $postarr ) {
	// Consider wp_insert_post() content is not empty if incoming et_pb_use_builder is `off` while currently saved _et_pb_use_builder value is `on`
	if ( isset( $postarr['et_pb_use_builder'] ) && 'off' === $postarr['et_pb_use_builder'] && isset( $postarr['post_ID'] ) && et_pb_is_pagebuilder_used( $postarr['post_ID'] ) ) {
		return false;
	}

	return $maybe_empty;
}
add_filter( 'wp_insert_post_empty_content', 'et_pb_ensure_builder_activation_switching', 10, 2 );

function et_pb_before_main_editor( $post ) {
	if ( ! in_array( $post->post_type, et_builder_get_builder_post_types() ) ) return;


	$_et_builder_use_builder   = get_post_meta( $post->ID, '_et_pb_use_builder', true );
	$is_builder_used           = 'on' === $_et_builder_use_builder;
	$last_builder_version_used = get_post_meta( $post->ID, '_et_builder_version', true ); // Examples: 'BB|Divi|3.0.30' 'VB|Divi|3.0.30'

	$_et_builder_use_ab_testing = get_post_meta( $post->ID, '_et_pb_use_ab_testing', true );
	$_et_builder_ab_stats_refresh_interval = et_pb_ab_get_refresh_interval( $post->ID );
	$_et_builder_ab_subjects = get_post_meta( $post->ID, '_et_pb_ab_subjects', true );
	$_et_builder_ab_goal_module = et_pb_ab_get_goal_module( $post->ID );

	$builder_always_enabled = apply_filters('et_builder_always_enabled', false, $post->post_type, $post );
	if ( $builder_always_enabled || 'et_pb_layout' === $post->post_type ) {
		$is_builder_used = true;
		$_et_builder_use_builder = 'on';
	}

	// Add button only if current user is allowed to use it otherwise display placeholder with all required data
	if ( et_pb_is_allowed( 'divi_builder_control' ) ) {
		$buttons = sprintf('<a href="#" id="et_pb_toggle_builder" data-builder="%2$s" data-editor="%3$s" class="button button-primary button-large%4$s%5$s">%1$s</a>',
			( $is_builder_used ? esc_html__( 'Use Default Editor', 'et_builder' ) : esc_html__( 'Use The Divi Builder', 'et_builder' ) ),
			esc_html__( 'Use The Divi Builder', 'et_builder' ),
			esc_html__( 'Use Default Editor', 'et_builder' ),
			( $is_builder_used ? ' et_pb_builder_is_used' : '' ),
			( $builder_always_enabled ? ' et_pb_hidden' : '' )
		);

		// add in the visual builder button only on appropriate post types
		if ( in_array( $post->post_type, et_builder_get_fb_post_types() ) && et_pb_is_allowed( 'use_visual_builder' ) && ! et_is_extra_library_layout( $post->ID ) ) {
			$buttons .= sprintf('<a href="%1$s" id="et_pb_fb_cta" class="button button-primary button-large" style="display: none;">%2$s</a>',
				esc_url( add_query_arg( 'et_fb', true, et_fb_prepare_ssl_link( get_the_permalink() ) ) ),
				esc_html__( 'Use Visual Builder', 'et_builder' )
			);
		}

		printf( '<div class="et_pb_toggle_builder_wrapper%1$s">%2$s</div><div id="et_pb_main_editor_wrap"%3$s>',
			( $is_builder_used ? ' et_pb_builder_is_used' : '' ),
			$buttons,
			( $is_builder_used ? ' class="et_pb_hidden"' : '' )
		);
	} else {
		printf( '<div class="et_pb_toggle_builder_wrapper%2$s"></div><div id="et_pb_main_editor_wrap"%1$s>',
			( $is_builder_used ? ' class="et_pb_hidden"' : '' ),
			( $is_builder_used ? ' et_pb_builder_is_used' : '' )
		);
	}

	?>
	<p class="et_pb_page_settings" style="display: none;">
		<?php wp_nonce_field( basename( __FILE__ ), 'et_pb_settings_nonce' ); ?>
		<input type="hidden" id="et_pb_last_post_modified" name="et_pb_last_post_modified" value="<?php echo esc_attr( $post->post_modified ); ?>" />
		<input type="hidden" id="et_pb_use_builder" name="et_pb_use_builder" value="<?php echo esc_attr( $_et_builder_use_builder ); ?>" />
		<input type="hidden" id="et_builder_version" name="et_builder_version" value="<?php echo esc_attr( $last_builder_version_used ); ?>" />
		<input type="hidden" autocomplete="off" id="et_pb_use_ab_testing" name="et_pb_use_ab_testing" value="<?php echo esc_attr( $_et_builder_use_ab_testing ); ?>">
		<input type="hidden" autocomplete="off" id="et_pb_ab_stats_refresh_interval" name="et_pb_ab_stats_refresh_interval" value="<?php echo esc_attr( $_et_builder_ab_stats_refresh_interval ); ?>">
		<input type="hidden" autocomplete="off" id="et_pb_ab_subjects" name="et_pb_ab_subjects" value="<?php echo esc_attr( $_et_builder_ab_subjects ); ?>">
		<input type="hidden" autocomplete="off" id="et_pb_ab_goal_module" name="et_pb_ab_goal_module" value="<?php echo esc_attr( $_et_builder_ab_goal_module ); ?>">
		<?php et_pb_builder_settings_hidden_inputs( $post->ID ); ?>
		<?php et_pb_builder_global_library_inputs( $post->ID ); ?>

		<textarea id="et_pb_old_content" name="et_pb_old_content"><?php echo esc_attr( get_post_meta( $post->ID, '_et_pb_old_content', true ) ); ?></textarea>
	</p>
	<?php
}
add_action( 'edit_form_after_title', 'et_pb_before_main_editor' );

function et_pb_after_main_editor( $post ) {
	if ( ! in_array( $post->post_type, et_builder_get_builder_post_types() ) ) return;
	echo '</div> <!-- #et_pb_main_editor_wrap -->';
}
add_action( 'edit_form_after_editor', 'et_pb_after_main_editor' );

function et_pb_admin_scripts_styles( $hook ) {
	global $typenow;

	//load css file for the Divi menu
	wp_enqueue_style( 'library-menu-styles', ET_BUILDER_URI . '/styles/library_menu.css', array(), ET_BUILDER_VERSION );

	if ( $hook === 'widgets.php' ) {
		wp_enqueue_script( 'et_pb_widgets_js', ET_BUILDER_URI . '/scripts/ext/widgets.js', array( 'jquery' ), ET_BUILDER_VERSION, true );

		wp_localize_script( 'et_pb_widgets_js', 'et_pb_options', apply_filters( 'et_pb_options_admin', array(
			'ajaxurl'       => admin_url( 'admin-ajax.php' ),
			'et_admin_load_nonce' => wp_create_nonce( 'et_admin_load_nonce' ),
			'widget_info'   => sprintf( '<div id="et_pb_widget_area_create"><p>%1$s.</p><p>%2$s.</p><p><label>%3$s <input id="et_pb_new_widget_area_name" value="" /></label><button class="button button-primary et_pb_create_widget_area">%4$s</button></p><p class="et_pb_widget_area_result"></p></div>',
				esc_html__( 'Here you can create new widget areas for use in the Sidebar module', 'et_builder' ),
				esc_html__( 'Note: Naming your widget area "sidebar 1", "sidebar 2", "sidebar 3", "sidebar 4" or "sidebar 5" will cause conflicts with this theme', 'et_builder' ),
				esc_html__( 'Widget Name', 'et_builder' ),
				esc_html__( 'Create', 'et_builder' )
			),
			'delete_string' => esc_html__( 'Delete', 'et_builder' ),
		) ) );

		wp_enqueue_style( 'et_pb_widgets_css', ET_BUILDER_URI . '/styles/widgets.css', array(), ET_BUILDER_VERSION );

		return;
	}

	if ( ! in_array( $hook, array( 'post-new.php', 'post.php' ) ) ) return;

	/*
	 * Load the builder javascript and css files for custom post types
	 * custom post types can be added using et_builder_post_types filter
	*/

	$post_types = et_builder_get_builder_post_types();

	if ( isset( $typenow ) && in_array( $typenow, $post_types ) ){
		et_pb_add_builder_page_js_css();
	}
}
add_action( 'admin_enqueue_scripts', 'et_pb_admin_scripts_styles', 10, 1 );

/**
 * Disable emoji detection script on edit page which has Backend Builder on it.
 * WordPress automatically replaces emoji with plain image for backward compatibility
 * on older browsers. This causes issue when emoji is used on header or other input
 * text field because (when the modal is saved, shortcode is generated, and emoji
 * is being replaced with plain image) it creates incorrect attribute markup
 * such as `title="I <img class="emoji" src="../heart.png" /> WP"` and causes
 * the whole input text value to be disappeared
 * @return void
 */
function et_pb_remove_emoji_detection_script() {
	global $pagenow;

	$disable_emoji_detection = false;

	// Disable emoji detection script on editing page which has Backend Builder
	// global $post isn't available at admin_init, so retrieve $post data manually
	if ( 'post.php' === $pagenow && isset( $_GET['post'] ) ) {
		$post_id   = (int) $_GET['post'];
		$post      = get_post( $post_id );
		$post_type = isset( $post->post_type ) ? $post->post_type : '';

		if ( in_array( $post_type, et_builder_get_builder_post_types() ) ) {
			$disable_emoji_detection = true;
		}
	}

	// Disable emoji detection script on post new page which has Backend Builder
	$has_post_type_query = isset( $_GET['post_type'] );
	if ( 'post-new.php' === $pagenow && ( ! $has_post_type_query || ( $has_post_type_query && in_array( $_GET['post_type'], et_builder_get_builder_post_types() ) ) ) ) {
		$disable_emoji_detection = true;
	}

	if ( $disable_emoji_detection ) {
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	}
}
add_action( 'admin_init', 'et_pb_remove_emoji_detection_script' );

/**
 * Disable emoji detection script on visual builder
 * WordPress automatically replaces emoji with plain image for backward compatibility
 * on older browsers. This causes issue when emoji is used on header or other input
 * text field because the staticize emoji creates HTML markup which appears to be
 * invalid on input[type="text"] field such as `title="I <img class="emoji"
 * src="../heart.png" /> WP"` and causes the input text value to be escaped and
 * disappeared
 * @return void
 */
function et_fb_remove_emoji_detection_script() {
	global $post;

	// Disable emoji detection script on visual builder. React's auto escaping will
	// remove all staticized emoji when being opened on modal's input field
	if ( isset( $post->ID ) && et_fb_is_enabled( $post->ID ) ) {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	}
}
add_action( 'wp', 'et_fb_remove_emoji_detection_script' );

function et_pb_fix_builder_shortcodes( $content ) {
	// if the builder is used for the page, get rid of random p tags
	if ( is_singular() && 'on' === get_post_meta( get_the_ID(), '_et_pb_use_builder', true ) ) {
		$content = et_pb_fix_shortcodes( $content );
	}

	return $content;
}
add_filter( 'the_content', 'et_pb_fix_builder_shortcodes' );

// generate the html for "Add new template" Modal in Library
if ( ! function_exists( 'et_pb_generate_new_layout_modal' ) ) {
	function et_pb_generate_new_layout_modal() {
		$template_type_option_output = '';
		$template_module_tabs_option_output = '';
		$template_global_option_output = '';
		$layout_cat_option_output = '';

		$template_type_options = apply_filters( 'et_pb_new_layout_template_types', array(
			'module'            => esc_html__( 'Module', 'et_builder' ),
			'fullwidth_module'  => esc_html__( 'Fullwidth Module', 'et_builder' ),
			'row'               => esc_html__( 'Row', 'et_builder' ),
			'section'           => esc_html__( 'Section', 'et_builder' ),
			'fullwidth_section' => esc_html__( 'Fullwidth Section', 'et_builder' ),
			'specialty_section' => esc_html__( 'Specialty Section', 'et_builder' ),
			'layout'            => esc_html__( 'Layout', 'et_builder' ),
		) );

		// construct output for the template type option
		if ( ! empty( $template_type_options ) ) {
			$template_type_option_output = sprintf(
				'<br><label>%1$s:</label>
				<select id="new_template_type">',
				esc_html__( 'Template Type', 'et_builder' )
			);

			foreach( $template_type_options as $option_id => $option_name ) {
				$template_type_option_output .= sprintf(
					'<option value="%1$s">%2$s</option>',
					esc_attr( $option_id ),
					esc_html( $option_name )
				);
			}

			$template_type_option_output .= '</select>';
		}

		$template_global_option_output = apply_filters( 'et_pb_new_layout_global_option', sprintf(
			'<br><label>%1$s<input type="checkbox" value="global" id="et_pb_template_global"></label>',
			esc_html__( 'Global', 'et_builder' )
		) );

		// construct output for the layout category option
		$layout_cat_option_output .= sprintf(
			'<br><label>%1$s</label>',
			esc_html__( 'Select category(ies) for new template or type a new name ( optional )', 'et_builder' )
		);

		$layout_categories = apply_filters( 'et_pb_new_layout_cats_array', get_terms( 'layout_category', array( 'hide_empty' => false ) ) );
		if ( is_array( $layout_categories ) && ! empty( $layout_categories ) ) {
			$layout_cat_option_output .= '<div class="layout_cats_container">';

			foreach( $layout_categories as $category ) {
				$layout_cat_option_output .= sprintf(
					'<label>%1$s<input type="checkbox" value="%2$s"/></label>',
					esc_html( $category->name ),
					esc_attr( $category->term_id )
				);
			}

			$layout_cat_option_output .= '</div>';
		}

		$layout_cat_option_output .= '<input type="text" value="" id="et_pb_new_cat_name" class="regular-text">';

		$output = sprintf(
			'<div class="et_pb_modal_overlay et_modal_on_top et_pb_new_template_modal">
				<div class="et_pb_prompt_modal">
					<h2>%1$s</h2>
					<div class="et_pb_prompt_modal_inside">
						<label>%2$s:</label>
							<input type="text" value="" id="et_pb_new_template_name" class="regular-text">
							%6$s
							%3$s
							%4$s
							%5$s
							%7$s
							<input id="et_builder_layout_built_for_post_type" type="hidden" value="page">
					</div>
					<a href="#"" class="et_pb_prompt_dont_proceed et-pb-modal-close"></a>
					<div class="et_pb_prompt_buttons">
						<br>
						<span class="spinner"></span>
						<input type="submit" class="et_pb_create_template button-primary et_pb_prompt_proceed">
					</div>
				</div>
			</div>',
			esc_html__( 'New Template Settings', 'et_builder' ),
			esc_html__( 'Template Name', 'et_builder' ),
			$template_type_option_output,
			$template_global_option_output,
			$layout_cat_option_output, //#5
			apply_filters( 'et_pb_new_layout_before_options', '' ),
			apply_filters( 'et_pb_new_layout_after_options', '' )
		);

		return apply_filters( 'et_pb_new_layout_modal_output', $output );
	}
}

/**
 * Get layout type of given post ID
 * @return string|bool
 */
if ( ! function_exists( 'et_pb_get_layout_type' ) ) :
function et_pb_get_layout_type( $post_id ) {
	// Get taxonomies
	$layout_type_data = wp_get_post_terms( $post_id, 'layout_type' );

	if ( empty( $layout_type_data ) ) {
		return false;
	}

	// Pluck name out of taxonomies
	$layout_type_array = wp_list_pluck( $layout_type_data, 'name' );

	// Logically, a layout only have one layout type.
	$layout_type = implode( "|", $layout_type_array );

	return $layout_type;
}
endif;

if ( ! function_exists( 'et_pb_is_wp_old_version' ) ) :
function et_pb_is_wp_old_version() {
	global $wp_version;

	$wp_major_version = substr( $wp_version, 0, 3 );

	if ( version_compare( $wp_major_version, '4.5', '<' ) ) {
		return true;
	}

	return false;
}
endif;

if ( ! function_exists( 'et_pb_is_wp_old_version' ) ) :
function et_pb_is_wp_old_version(){
	global $typenow, $post, $wp_version;

	$wp_major_version = substr( $wp_version, 0, 3 );

	if ( version_compare( $wp_major_version, '4.5', '<' ) ) {
		return true;
	}

	return false;
}
endif;

if ( ! function_exists( 'et_pb_add_builder_page_js_css' ) ) :
function et_pb_add_builder_page_js_css(){
	global $typenow, $post;


	// BEGIN Process shortcodes (for module settings migrations and Yoast SEO compatibility)
	// Get list of shortcodes that causes issue if being triggered in admin
	$conflicting_shortcodes = et_pb_admin_excluded_shortcodes();

	if ( ! empty( $conflicting_shortcodes ) ) {
		foreach ( $conflicting_shortcodes as $shortcode ) {
			remove_shortcode( $shortcode );
		}
	}

	// save the original content of $post variable
	$post_original = $post;
	// get the content for yoast
	$post_content_processed = do_shortcode( $post->post_content );
	// set the $post to the original content to make sure it wasn't changed by do_shortcode()
	$post = $post_original;
	// END Process shortcodes

	$is_global_template = '';
	$post_id = '';
	$post_type = $typenow;
	$selective_sync_status = '';
	$global_module_type = '';
	$excluded_global_options = array();

	// we need some post data when editing saved templates.
	if ( 'et_pb_layout' === $typenow ) {
		$template_scope = wp_get_object_terms( get_the_ID(), 'scope' );
		$template_type = wp_get_object_terms( get_the_ID(), 'layout_type' );
		$is_global_template = ! empty( $template_scope[0] ) ? $template_scope[0]->slug : 'regular';
		$global_module_type = ! empty( $template_type[0] ) ? $template_type[0]->slug : '';
		$post_id = get_the_ID();

		// Check whether it's a Global item's page and display wp error if Global items disabled for current user
		if ( ! et_pb_is_allowed( 'edit_global_library' ) && 'global' === $is_global_template ) {
			wp_die( esc_html__( "you don't have sufficient permissions to access this page", 'et_builder' ) );
		}

		if ( 'global' === $is_global_template ) {
			$excluded_global_options = get_post_meta( $post_id, '_et_pb_excluded_global_options' );
			$selective_sync_status = empty( $excluded_global_options ) ? '' : 'updated';
		}

		$built_for_post_type = get_post_meta( get_the_ID(), '_et_pb_built_for_post_type', true );
		$built_for_post_type = '' !== $built_for_post_type ? $built_for_post_type : 'page';
		$post_type = apply_filters( 'et_pb_built_for_post_type', $built_for_post_type, get_the_ID() );
	}

	// we need this data to create the filter when adding saved modules
	$layout_categories = get_terms( 'layout_category' );
	$layout_cat_data = array();
	$layout_cat_data_json = '';

	if ( is_array( $layout_categories ) && ! empty( $layout_categories ) ) {
		foreach( $layout_categories as $category ) {
			$layout_cat_data[] = array(
				'slug' => $category->slug,
				'name' => $category->name,
			);
		}
	}
	if ( ! empty( $layout_cat_data ) ) {
		$layout_cat_data_json = json_encode( $layout_cat_data );
	}

	// Set fixed protocol for preview URL to prevent cross origin issue
	$preview_scheme = is_ssl() ? 'https' : 'http';

	$preview_url = esc_url( home_url( '/' ) );

	if ( 'https' === $preview_scheme && ! strpos( $preview_url, 'https://' ) ) {
		$preview_url = str_replace( 'http://', 'https://', $preview_url );
	}

	// force update cache if et_pb_clear_templates_cache option is set to on
	$force_cache_value  = et_get_option( 'et_pb_clear_templates_cache', '' );
	$force_cache_update = '' !== $force_cache_value ? $force_cache_value : ET_BUILDER_FORCE_CACHE_PURGE;

	/**
	 * Whether or not the backend builder should clear its Backbone template cache.
	 *
	 * @param bool $force_cache_update
	 */
	$force_cache_update = apply_filters( 'et_pb_clear_template_cache', $force_cache_update );

	// delete et_pb_clear_templates_cache option it's not needed anymore
	et_delete_option( 'et_pb_clear_templates_cache' );

	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'underscore' );
	wp_enqueue_script( 'backbone' );

	if ( et_pb_enqueue_google_maps_script() ) {
		wp_enqueue_script( 'google-maps-api', esc_url( add_query_arg( array( 'key' => et_pb_get_google_api_key(), 'callback' => 'initMap' ), is_ssl() ? 'https://maps.googleapis.com/maps/api/js' : 'http://maps.googleapis.com/maps/api/js' ) ), array(), '3', true );
	}

	wp_enqueue_script( 'wp-color-picker' );
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker-alpha', ET_BUILDER_URI . '/scripts/ext/wp-color-picker-alpha.min.js', array( 'jquery', 'wp-color-picker' ), ET_BUILDER_VERSION, true );
	wp_register_script( 'chart', ET_BUILDER_URI . '/scripts/ext/chart.min.js', array(), ET_BUILDER_VERSION, true );
	wp_register_script( 'jquery-tablesorter', ET_BUILDER_URI . '/scripts/ext/jquery.tablesorter.min.js', array( 'jquery' ), ET_BUILDER_VERSION, true );

	// load 1.10.4 versions of jQuery-ui scripts if WP version is less than 4.5, load 1.11.4 version otherwise
	if ( et_pb_is_wp_old_version() ) {
		wp_enqueue_script( 'et_pb_admin_date_js', ET_BUILDER_URI . '/scripts/ext/jquery-ui-1.10.4.custom.min.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
	} else {
		wp_enqueue_script( 'et_pb_admin_date_js', ET_BUILDER_URI . '/scripts/ext/jquery-ui-1.11.4.custom.min.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
	}

	wp_enqueue_script( 'et_pb_admin_date_addon_js', ET_BUILDER_URI . '/scripts/ext/jquery-ui-timepicker-addon.js', array( 'et_pb_admin_date_js' ), ET_BUILDER_VERSION, true );

	wp_enqueue_script( 'validation', ET_BUILDER_URI . '/scripts/ext/jquery.validate.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
	wp_enqueue_script( 'minicolors', ET_BUILDER_URI . '/scripts/ext/jquery.minicolors.js', array( 'jquery' ), ET_BUILDER_VERSION, true );

	wp_enqueue_script( 'et_pb_cache_notice_js', ET_BUILDER_URI .'/scripts/cache_notice.js', array( 'jquery', 'et_pb_admin_js', 'et_pb_admin_global_js' ), ET_BUILDER_VERSION, true );

	wp_localize_script( 'et_pb_cache_notice_js', 'et_pb_notice_options', apply_filters( 'et_pb_notice_options_builder', array(
		'product_version'  => ET_BUILDER_PRODUCT_VERSION,
	) ) );

	wp_enqueue_script( 'et_pb_media_library', ET_BUILDER_URI . '/scripts/ext/media-library.js', array( 'media-editor' ), ET_BUILDER_VERSION, true );

	wp_enqueue_script( 'et_pb_admin_js', ET_BUILDER_URI .'/scripts/builder.js', array( 'jquery', 'jquery-ui-core', 'underscore', 'backbone', 'chart', 'jquery-tablesorter', 'et_pb_admin_global_js', 'et_pb_media_library' ), ET_BUILDER_VERSION, true );

	wp_localize_script( 'et_pb_admin_js', 'et_pb_options', apply_filters( 'et_pb_options_builder', array_merge( array(
		'debug'                                    => false,
		'ajaxurl'                                  => admin_url( 'admin-ajax.php' ),
		'home_url'                                 => home_url(),
		'cookie_path'                              => SITECOOKIEPATH,
		'preview_url'                              => add_query_arg( 'et_pb_preview', 'true', $preview_url ),
		'et_admin_load_nonce'                      => wp_create_nonce( 'et_admin_load_nonce' ),
		'images_uri'                               => ET_BUILDER_URI .'/images',
		'post_type'                                => $post_type,
		'et_builder_module_parent_shortcodes'      => ET_Builder_Element::get_parent_shortcodes( $post_type ),
		'et_builder_module_child_shortcodes'       => ET_Builder_Element::get_child_shortcodes( $post_type ),
		'et_builder_module_raw_content_shortcodes' => ET_Builder_Element::get_raw_content_shortcodes( $post_type ),
		'et_builder_modules'                       => ET_Builder_Element::get_modules_js_array( $post_type ),
		'et_builder_modules_count'                 => ET_Builder_Element::get_modules_count( $post_type ),
		'et_builder_modules_with_children'         => ET_Builder_Element::get_shortcodes_with_children( $post_type ),
		'et_builder_templates_amount'              => ET_BUILDER_AJAX_TEMPLATES_AMOUNT,
		'default_initial_column_type'              => apply_filters( 'et_builder_default_initial_column_type', '4_4' ),
		'default_initial_text_module'              => apply_filters( 'et_builder_default_initial_text_module', 'et_pb_text' ),
		'section_only_row_dragged_away'            => esc_html__( 'The section should have at least one row.', 'et_builder' ),
		'fullwidth_module_dragged_away'            => esc_html__( 'Fullwidth module can\'t be used outside of the Fullwidth Section.', 'et_builder' ),
		'stop_dropping_3_col_row'                  => esc_html__( '3 column row can\'t be used in this column.', 'et_builder' ),
		'preview_image'                            => esc_html__( 'Preview', 'et_builder' ),
		'empty_admin_label'                        => esc_html__( 'Module', 'et_builder' ),
		'video_module_image_error'                 => esc_html__( 'Still images cannot be generated from this video service and/or this video format', 'et_builder' ),
		'geocode_error'                            => esc_html__( 'Geocode was not successful for the following reason', 'et_builder' ),
		'geocode_error_2'                          => esc_html__( 'Geocoder failed due to', 'et_builder' ),
		'no_results'                               => esc_html__( 'No results found', 'et_builder' ),
		'all_tab_options_hidden'                   => esc_html__( 'No available options for this configuration.', 'et_builder' ),
		'update_global_module'                     => esc_html__( 'You\'re about to update global module. This change will be applied to all pages where you use this module. Press OK if you want to update this module', 'et_builder' ),
		'global_row_alert'                         => esc_html__( 'You cannot add global rows into global sections', 'et_builder' ),
		'global_module_alert'                      => esc_html__( 'You cannot add global modules into global sections or rows', 'et_builder' ),
		'all_cat_text'                             => esc_html__( 'All Categories', 'et_builder' ),
		'is_global_template'                       => $is_global_template,
		'selective_sync_status'                    => $selective_sync_status,
		'global_module_type'                       => $global_module_type,
		'excluded_global_options'                  => isset( $excluded_global_options[0] ) ? json_decode( $excluded_global_options[0] ) : array(),
		'template_post_id'                         => $post_id,
		'layout_categories'                        => $layout_cat_data_json,
		'map_pin_address_error'                    => esc_html__( 'Map Pin Address cannot be empty', 'et_builder' ),
		'map_pin_address_invalid'                  => esc_html__( 'Invalid Pin and address data. Please try again.', 'et_builder' ),
		'locked_section_permission_alert'          => esc_html__( 'You do not have permission to unlock this section.', 'et_builder' ),
		'locked_row_permission_alert'              => esc_html__( 'You do not have permission to unlock this row.', 'et_builder' ),
		'locked_module_permission_alert'           => esc_html__( 'You do not have permission to unlock this module.', 'et_builder' ),
		'locked_item_permission_alert'             => esc_html__( 'You do not have permission to perform this task.', 'et_builder' ),
		'localstorage_unavailability_alert'        => esc_html__( 'Unable to perform copy/paste process due to inavailability of localStorage feature in your browser. Please use latest modern browser (Chrome, Firefox, or Safari) to perform copy/paste process', 'et_builder' ),
		'invalid_color'                            => esc_html__( 'Invalid Color', 'et_builder' ),
		'et_pb_preview_nonce'                      => wp_create_nonce( 'et_pb_preview_nonce' ),
		'is_divi_library'                          => 'et_pb_layout' === $typenow ? 1 : 0,
		'layout_type'                              => 'et_pb_layout' === $typenow ? et_pb_get_layout_type( get_the_ID() ) : 0,
		'is_plugin_used'                           => et_is_builder_plugin_active(),
		'yoast_content'                            => et_is_yoast_seo_plugin_active() ? $post_content_processed : '',
		'ab_db_status'                             => true === et_pb_db_status_up_to_date() ? 'exists' : 'not_exists',
		'ab_testing_builder_nonce'                 => wp_create_nonce( 'ab_testing_builder_nonce' ),
		'page_color_palette'                       => get_post_meta( get_the_ID(), '_et_pb_color_palette', true ),
		'default_color_palette'                    => implode( '|', et_pb_get_default_color_palette() ),
		'page_section_bg_color'                    => get_post_meta( get_the_ID(), '_et_pb_section_background_color', true ),
		'page_gutter_width'                        => '' !== ( $saved_gutter_width = get_post_meta( get_the_ID(), '_et_pb_gutter_width', true ) ) ? $saved_gutter_width : et_get_option( 'gutter_width', 3 ),
		'product_version'                          => ET_BUILDER_PRODUCT_VERSION,
		'force_cache_purge'                        => $force_cache_update ? 'true' : 'false',
		'memory_limit_increased'                   => esc_html__( 'Your memory limit has been increased', 'et_builder' ),
		'memory_limit_not_increased'               => esc_html__( "Your memory limit can't be changed automatically", 'et_builder' ),
		'google_api_key'                           => et_pb_get_google_api_key(),
		'options_page_url'                         => et_pb_get_options_page_link(),
		'et_pb_google_maps_script_notice'          => et_pb_enqueue_google_maps_script(),
		'select_text'                              => esc_html__( 'Select', 'et_builder' ),
		'et_fb_autosave_nonce'                     => wp_create_nonce( 'et_fb_autosave_nonce' ),
		'et_builder_email_fetch_lists_nonce'       => wp_create_nonce( 'et_builder_email_fetch_lists_nonce' ),
		'et_builder_email_add_account_nonce'       => wp_create_nonce( 'et_builder_email_add_account_nonce' ),
		'et_builder_email_remove_account_nonce'    => wp_create_nonce( 'et_builder_email_remove_account_nonce' ),
		'et_pb_module_settings_migrations'         => ET_Builder_Module_Settings_Migration::$migrated,
	), et_pb_history_localization() ) ) );

	wp_localize_script( 'et_pb_admin_js', 'et_pb_ab_js_options', apply_filters( 'et_pb_ab_js_options', array(
		'test_id'                    => $post->ID,
		'has_report'                 => et_pb_ab_has_report( $post->ID ),
		'has_permission'             => et_pb_is_allowed( 'ab_testing' ),
		'refresh_interval_duration'  => et_pb_ab_get_refresh_interval_duration( $post->ID ),
		'refresh_interval_durations' => et_pb_ab_refresh_interval_durations(),
		'analysis_formula'           => et_pb_ab_get_analysis_formulas(),
		'have_conversions'           => et_pb_ab_get_modules_have_conversions(),
		'sales_title'                => esc_html__( 'Sales', 'et_builder' ),
		'force_cache_purge'          => $force_cache_update,
		'total_title'                => esc_html__( 'Total', 'et_builder' ),

		// Saved data
		'subjects_rank' => ( 'on' === get_post_meta( $post->ID, '_et_pb_use_builder', true ) ) ? et_pb_ab_get_saved_subjects_ranks( $post->ID ) : false,

		// Rank color
		'subjects_rank_color' => et_pb_ab_get_subject_rank_colors(),

		// Configuration
		'has_no_permission' => array(
			'title' => esc_html__( 'Unauthorized Action', 'et_builder' ),
			'desc' => esc_html__( 'You do not have permission to edit the module, row or section in this split test.', 'et_builder' ),
		),
		'select_ab_testing_subject' => array(
			'title' => esc_html__( 'Select Split Testing Subject', 'et_builder' ),
			'desc' => esc_html__( 'You have activated the Divi Leads Split Testing System. Using split testing, you can create different element variations on your page to find out which variation most positively affects the conversion rate of your desired goal. After closing this window, please click on the section, row or module that you would like to split test.', 'et_builder' ),
		),
		'select_ab_testing_goal' => array(
			'title' => esc_html__( 'Select Your Goal', 'et_builder' ),
			'desc'  => esc_html__( 'Congratulations, you have selected a split testing subject! Next you need to select your goal. After closing this window, please click the section, row or module that you want to use as your goal. Depending on the element you choose, Divi will track relevant conversion rates for clicks, reads or sales. For example, if you select a Call To Action module as your goal, then Divi will track how variations in your test subjects affect how often visitors read and click the button in your Call To Action module. The test subject itself can also be selected as your goal.', 'et_builder' ),
		),
		'configure_ab_testing_alternative' => array(
			'title' => esc_html__( 'Configure Subject Variations', 'et_builder' ),
			'desc'  => esc_html__( 'Congratulations, your split test is ready to go! You will notice that your split testing subject has been duplicated. Each split testing variation will be displayed to your visitors and statistics will be collected to figure out which variation results in the highest goal conversion rate. Your test will begin when you save this page.', 'et_builder' ),
		),
		'select_ab_testing_winner_first' => array(
			'title' => esc_html__( 'Select Split Testing Winner', 'et_builder' ),
			'desc'  => esc_html__( 'Before ending your split test, you must choose which split testing variation to keep. Please select your favorite or highest converting subject. Alternative split testing subjects will be removed.', 'et_builder' ),
		),
		'select_ab_testing_subject_first' => array(
			'title' => esc_html__( 'Select Split Testing Subject', 'et_builder' ),
			'desc'  => esc_html__( 'You need to select a split testing subject first.', 'et_builder' ),
		),
		'select_ab_testing_goal_first' => array(
			'title' => esc_html__( 'Select Split Testing Goal', 'et_builder' ),
			'desc'  => esc_html__( 'You need to select a split testing goal first. ', 'et_builder' ),
		),
		'cannot_select_subject_parent_as_goal' => array(
			'title' => esc_html__( 'Select A Different Goal', 'et_builder' ),
			'desc'  => esc_html__( 'This element cannot be used as a your split testing goal. Please select a different module, or section.', 'et_builder' ),
		),

		// Save to Library
		'cannot_save_app_layout_has_ab_testing' => array(
			'title' => esc_html__( 'Can\'t Save Layout', 'et_builder' ),
			'desc'  => esc_html__( 'You cannot save layout while a split test is running. Please end your split test and then try again.', 'et_builder' ),
		),

		'cannot_save_section_layout_has_ab_testing' => array(
			'title' => esc_html__( 'Can\'t Save Section', 'et_builder' ),
			'desc'  => esc_html__( 'You cannot save this section while a split test is running. Please end your split test and then try again.', 'et_builder' ),
		),

		'cannot_save_row_layout_has_ab_testing' => array(
			'title' => esc_html__( 'Can\'t Save Row', 'et_builder' ),
			'desc'  => esc_html__( 'You cannot save this row while a split test is running. Please end your split test and then try again.', 'et_builder' ),
		),

		'cannot_save_row_inner_layout_has_ab_testing' => array(
			'title' => esc_html__( 'Can\'t Save Row', 'et_builder' ),
			'desc'  => esc_html__( 'You cannot save this row while a split test is running. Please end your split test and then try again.', 'et_builder' ),
		),

		'cannot_save_module_layout_has_ab_testing' => array(
			'title' => esc_html__( 'Can\'t Save Module', 'et_builder' ),
			'desc'  => esc_html__( 'You cannot save this module while a split test is running. Please end your split test and then try again.', 'et_builder' ),
		),

		// Load / Clear Layout
		'cannot_load_layout_has_ab_testing' => array(
			'title' => esc_html__( 'Can\'t Load Layout', 'et_builder' ),
			'desc'  => esc_html__( 'You cannot load a new layout while a split test is running. Please end your split test and then try again.', 'et_builder' ),
		),
		'cannot_clear_layout_has_ab_testing' => array(
			'title' => esc_html__( 'Can\'t Clear Layout', 'et_builder' ),
			'desc'  => esc_html__( 'You cannot clear your layout while a split testing is running. Please end your split test before clearing your layout.', 'et_builder' ),
		),

		// Cannot Import / Export Layout (Portability)
		'cannot_import_export_layout_has_ab_testing' => array(
			'title' => esc_html__( "Can't Import/Export Layout", 'et_builder' ),
			'desc'  => esc_html__( 'You cannot import or export a layout while a split test is running. Please end your split test and then try again.', 'et_builder' ),
		),

		// Moving Goal / Subject
		'cannot_move_module_goal_out_from_subject' => array(
			'title' => esc_html__( 'Can\'t Move Goal', 'et_builder' ),
			'desc'  => esc_html__( 'Once set, a goal that has been placed inside a split testing subject cannot be moved outside the split testing subject. You can end your split test and start a new one if you would like to make this change.', 'et_builder' ),
		),
		'cannot_move_row_goal_out_from_subject' => array(
			'title' => esc_html__( 'Can\'t Move Goal', 'et_builder' ),
			'desc'  => esc_html__( 'Once set, a goal that has been placed inside a split testing subject cannot be moved outside the split testing subject. You can end your split test and start a new one if you would like to make this change.', 'et_builder' ),
		),
		'cannot_move_goal_into_subject' => array(
			'title' => esc_html__( 'Can\'t Move Goal', 'et_builder' ),
			'desc'  => esc_html__( 'A split testing goal cannot be moved inside of a split testing subject. To perform this action you must first end your split test.', 'et_builder' ),
		),
		'cannot_move_subject_into_goal' => array(
			'title' => esc_html__( 'Can\'t Move Subject', 'et_builder' ),
			'desc'  => esc_html__( 'A split testing subject cannot be moved inside of a split testing goal. To perform this action you must first end your split test.', 'et_builder' ),
		),

		// Cloning + Has Goal
		'cannot_clone_section_has_goal' => array(
			'title' => esc_html__( 'Can\'t Clone Section', 'et_builder' ),
			'desc'  => esc_html__( 'This section cannot be duplicated because it contains a split testing goal. Goals cannot be duplicated. You must first end your split test before performing this action.', 'et_builder' ),
		),
		'cannot_clone_row_has_goal' => array(
			'title' => esc_html__( 'Can\'t Clone Row', 'et_builder' ),
			'desc'  => esc_html__( 'This row cannot be duplicated because it contains a split testing goal. Goals cannot be duplicated. You must first end your split test before performing this action.', 'et_builder' ),
		),

		// Removing + Has Goal
		'cannot_remove_section_has_goal' => array(
			'title' => esc_html__( 'Can\'t Remove Section', 'et_builder' ),
			'desc'  => esc_html__( 'This section cannot be removed because it contains a split testing goal. Goals cannot be deleted. You must first end your split test before performing this action.', 'et_builder' ),
		),
		'cannot_remove_row_has_goal' => array(
			'title' => esc_html__( 'Can\'t Remove Row', 'et_builder' ),
			'desc'  => esc_html__( 'This row cannot be removed because it contains a split testing goal. Goals cannot be deleted. You must first end your split test before performing this action.', 'et_builder' ),
		),

		// Removing + Has Unremovable Subjects
		'cannot_remove_section_has_unremovable_subject' => array(
			'title' => esc_html__( 'Can\'t Remove Section', 'et_builder' ),
			'desc'  => esc_html__( 'Split testing requires at least 2 subject variations. This variation cannot be removed until additional variations have been added.', 'et_builder' ),
		),
		'cannot_remove_row_has_unremovable_subject' => array(
			'title' => esc_html__( 'Can\'t Remove Row', 'et_builder' ),
			'desc'  => esc_html__( 'Split testing requires at least 2 subject variations. This variation cannot be removed until additional variations have been added', 'et_builder' ),
		),

		// View stats summary table heading
		'view_stats_thead_titles' => array(
			'clicks' => array(
				esc_html__( 'ID', 'et_builder' ),
				esc_html__( 'Subject', 'et_builder' ),
				esc_html__( 'Impressions', 'et_builder' ),
				esc_html__( 'Clicks', 'et_builder' ),
				esc_html__( 'Clickthrough Rate', 'et_builder' ),
			),
			'reads' => array(
				esc_html__( 'ID', 'et_builder' ),
				esc_html__( 'Subject', 'et_builder' ),
				esc_html__( 'Impressions', 'et_builder' ),
				esc_html__( 'Reads', 'et_builder' ),
				esc_html__( 'Reading Rate', 'et_builder' ),
			),
			'bounces' => array(
				esc_html__( 'ID', 'et_builder' ),
				esc_html__( 'Subject', 'et_builder' ),
				esc_html__( 'Impressions', 'et_builder' ),
				esc_html__( 'Stays', 'et_builder' ),
				esc_html__( 'Bounce Rate', 'et_builder' ),
			),
			'engagements' => array(
				esc_html__( 'ID', 'et_builder' ),
				esc_html__( 'Subject', 'et_builder' ),
				esc_html__( 'Goal Views', 'et_builder' ),
				esc_html__( 'Goal Reads', 'et_builder' ),
				esc_html__( 'Engagement Rate', 'et_builder' ),
			),
			'conversions' => array(
				esc_html__( 'ID', 'et_builder' ),
				esc_html__( 'Subject', 'et_builder' ),
				esc_html__( 'Impressions', 'et_builder' ),
				esc_html__( 'Conversion Goals', 'et_builder' ),
				esc_html__( 'Conversion Rate', 'et_builder' ),
			),
			'shortcode_conversions' => array(
				esc_html__( 'ID', 'et_builder' ),
				esc_html__( 'Subject', 'et_builder' ),
				esc_html__( 'Impressions', 'et_builder' ),
				esc_html__( 'Shortcode Conversions', 'et_builder' ),
				esc_html__( 'Conversion Rate', 'et_builder' ),
			),
		),
	) ) );

	wp_localize_script( 'et_pb_admin_js', 'et_pb_help_options', apply_filters( 'et_pb_help_options', array(
		'shortcuts' => et_builder_get_shortcuts('bb'),
	) ) );

	et_core_load_main_fonts();

	wp_enqueue_style( 'et_pb_admin_css', ET_BUILDER_URI .'/styles/style.css', array(), ET_BUILDER_VERSION );
	wp_enqueue_style( 'et_pb_admin_date_css', ET_BUILDER_URI . '/styles/jquery-ui-1.10.4.custom.css', array(), ET_BUILDER_VERSION );

	wp_add_inline_style( 'et_pb_admin_css', et_pb_ab_get_subject_rank_colors_style() );
}
endif;

function et_pb_set_editor_available_cookie() {
	$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : false;

	$headers_sent = headers_sent();

	if ( et_builder_should_load_framework() && is_admin() && ! $headers_sent && !empty( $post_id ) ) {
		setcookie( 'et-editor-available-post-' . $post_id . '-bb', 'bb', time() + ( MINUTE_IN_SECONDS * 30 ), SITECOOKIEPATH, false, is_ssl() );
	}
}
add_action('admin_init', 'et_pb_set_editor_available_cookie');

/**
 * List of history meta.
 *
 * @return array History meta.
 */
function et_pb_history_localization() {
	return array(
		'verb' => array(
			'did'       => esc_html__( 'Did', 'et_builder' ),
			'added'     => esc_html__( 'Added', 'et_builder' ),
			'edited'    => esc_html__( 'Edited', 'et_builder' ),
			'removed'   => esc_html__( 'Removed', 'et_builder' ),
			'moved'     => esc_html__( 'Moved', 'et_builder' ),
			'expanded'  => esc_html__( 'Expanded', 'et_builder' ),
			'collapsed' => esc_html__( 'Collapsed', 'et_builder' ),
			'locked'    => esc_html__( 'Locked', 'et_builder' ),
			'unlocked'  => esc_html__( 'Unlocked', 'et_builder' ),
			'cloned'    => esc_html__( 'Cloned', 'et_builder' ),
			'cleared'   => esc_html__( 'Cleared', 'et_builder' ),
			'enabled'   => esc_html__( 'Enabled', 'et_builder' ),
			'disabled'  => esc_html__( 'Disabled', 'et_builder' ),
			'copied'    => esc_html__( 'Copied', 'et_builder' ),
			'cut'       => esc_html__( 'Cut', 'et_builder' ),
			'pasted'    => esc_html__( 'Pasted', 'et_builder' ),
			'renamed'   => esc_html__( 'Renamed', 'et_builder' ),
			'loaded'    => esc_html__( 'Loaded', 'et_builder' ),
			'turnon'    => esc_html__( 'Turned On', 'et_builder' ),
			'turnoff'   => esc_html__( 'Turned Off', 'et_builder' ),
		),
		'noun' => array(
			'section'           => esc_html__( 'Section', 'et_builder' ),
			'saved_section'     => esc_html__( 'Saved Section', 'et_builder' ),
			'fullwidth_section' => esc_html__( 'Fullwidth Section', 'et_builder' ),
			'specialty_section' => esc_html__( 'Specialty Section', 'et_builder' ),
			'column'            => esc_html__( 'Column', 'et_builder' ),
			'row'               => esc_html__( 'Row', 'et_builder' ),
			'saved_row'         => esc_html__( 'Saved Row', 'et_builder' ),
			'module'            => esc_html__( 'Module', 'et_builder' ),
			'saved_module'      => esc_html__( 'Saved Module', 'et_builder' ),
			'page'              => esc_html__( 'Page', 'et_builder' ),
			'layout'            => esc_html__( 'Layout', 'et_builder' ),
			'abtesting'         => esc_html__( 'Split Testing', 'et_builder' ),
			'settings'          => esc_html__( 'Settings', 'et_builder' ),
		),
		'addition' => array(
			'phone'   => esc_html__( 'on Phone', 'et_builder' ),
			'tablet'  => esc_html__( 'on Tablet', 'et_builder' ),
			'desktop' => esc_html__( 'on Desktop', 'et_builder' ),
		),
	);
}

function et_pb_add_custom_box() {
	$post_types = et_builder_get_builder_post_types();

	foreach ( $post_types as $post_type ){
		add_meta_box( ET_BUILDER_LAYOUT_POST_TYPE, esc_html__( 'The Divi Builder', 'et_builder' ), 'et_pb_pagebuilder_meta_box', $post_type, 'normal', 'high' );
	}
}

if ( ! function_exists( 'et_pb_get_the_author_posts_link' ) ) :
function et_pb_get_the_author_posts_link(){
	global $authordata, $post;

	// Fallback for preview
	if ( empty( $authordata ) && isset( $post->post_author ) ) {
		$authordata = get_userdata( $post->post_author );
	}

	// If $authordata is empty, don't continue
	if ( empty( $authordata ) ) {
		return;
	}

	$link = sprintf(
		'<a href="%1$s" title="%2$s" rel="author">%3$s</a>',
		esc_url( get_author_posts_url( $authordata->ID, $authordata->user_nicename ) ),
		esc_attr( sprintf( __( 'Posts by %s', 'et_builder' ), get_the_author() ) ),
		get_the_author()
	);
	return apply_filters( 'the_author_posts_link', $link );
}
endif;

if ( ! function_exists( 'et_pb_get_comments_popup_link' ) ) :
function et_pb_get_comments_popup_link( $zero = false, $one = false, $more = false ){
	$id = get_the_ID();
	$number = get_comments_number( $id );

	if ( 0 == $number && !comments_open() && !pings_open() ) return;

	if ( $number > 1 )
		$output = str_replace( '%', number_format_i18n( $number ), ( false === $more ) ? __( '% Comments', $themename ) : $more );
	elseif ( $number == 0 )
		$output = ( false === $zero ) ? __( 'No Comments', 'et_builder' ) : $zero;
	else // must be one
		$output = ( false === $one ) ? __( '1 Comment', 'et_builder' ) : $one;

	return '<span class="comments-number">' . '<a href="' . esc_url( get_permalink() . '#respond' ) . '">' . apply_filters( 'comments_number', esc_html( $output ), esc_html( $number ) ) . '</a>' . '</span>';
}
endif;

if ( ! function_exists( 'et_pb_postinfo_meta' ) ) :
function et_pb_postinfo_meta( $postinfo, $date_format, $comment_zero, $comment_one, $comment_more ){
	$postinfo_meta = '';

	if ( in_array( 'author', $postinfo ) )
		$postinfo_meta .= ' ' . esc_html__( 'by', 'et_builder' ) . ' <span class="author vcard">' . et_pb_get_the_author_posts_link() . '</span>';

	if ( in_array( 'date', $postinfo ) ) {
		if ( in_array( 'author', $postinfo ) ) $postinfo_meta .= ' | ';
		$postinfo_meta .= '<span class="published">' . esc_html( get_the_time( wp_unslash( $date_format ) ) ) . '</span>';
	}

	if ( in_array( 'categories', $postinfo ) ) {
		$categories_list = get_the_category_list(', ');

		// do not output anything if no categories retrieved
		if ( '' !== $categories_list ) {
			if ( in_array( 'author', $postinfo ) || in_array( 'date', $postinfo ) )	$postinfo_meta .= ' | ';

			$postinfo_meta .= $categories_list;
		}
	}

	if ( in_array( 'comments', $postinfo ) ){
		if ( in_array( 'author', $postinfo ) || in_array( 'date', $postinfo ) || in_array( 'categories', $postinfo ) ) $postinfo_meta .= ' | ';
		$postinfo_meta .= et_pb_get_comments_popup_link( $comment_zero, $comment_one, $comment_more );
	}

	return $postinfo_meta;
}
endif;


if ( ! function_exists( 'et_pb_fix_shortcodes' ) ){
	function et_pb_fix_shortcodes( $content, $decode_entities = false ){
		if ( $decode_entities ) {
			$content = et_builder_replace_code_content_entities( $content );
			$content = ET_Builder_Element::convert_smart_quotes_and_amp( $content );
			$content = html_entity_decode( $content, ENT_QUOTES );
		}

		$replace_tags_from_to = array (
			'<p>[' => '[',
			']</p>' => ']',
			']<br />' => ']',
			"<br />\n[" => '[',
		);

		return strtr( $content, $replace_tags_from_to );
	}
}

if ( ! function_exists( 'et_pb_load_global_module' ) ) {
	function et_pb_load_global_module( $global_id, $row_type = '' ) {
		$global_shortcode = '';

		if ( '' !== $global_id ) {
			$query = new WP_Query( array(
				'p'         => (int) $global_id,
				'post_type' => ET_BUILDER_LAYOUT_POST_TYPE
			) );

			wp_reset_postdata();
			if ( ! empty( $query->post ) ) {
				$global_shortcode = $query->post->post_content;

				if ( '' !== $row_type && 'et_pb_row_inner' === $row_type ) {
					$global_shortcode = str_replace( 'et_pb_row', 'et_pb_row_inner', $global_shortcode );
				}
			}
		}

		return $global_shortcode;
	}
}

if ( ! function_exists( 'et_pb_extract_shortcode_content' ) ) {
	function et_pb_extract_shortcode_content( $content, $shortcode_name ) {

		$start = strpos( $content, ']' ) + 1;
		$end = strrpos( $content, '[/' . $shortcode_name );

		if ( false !== $end ) {
			$content = substr( $content, $start, $end - $start );
		} else {
			$content = (bool) false;
		}

		return $content;
	}
}

if ( ! function_exists( 'et_pb_remove_shortcode_content' ) ) {
	function et_pb_remove_shortcode_content( $content, $shortcode_name ) {
		$shortcode_content = et_pb_extract_shortcode_content( $content, $shortcode_name );

		if ( $shortcode_content ) {
			return str_replace( $shortcode_content, '', $content );
		}

		return $content;
	}
}

if ( ! function_exists( 'et_pb_get_global_module_content' ) ) {
	function et_pb_get_global_module_content( $content, $shortcode_name ) {
		// Apply wpautop to all modules except for the Code module. Line-breaks in Code module processed differently
		$global_shortcode_content = in_array( $shortcode_name, array( 'et_pb_code', 'et_pb_fullwidth_code' ) ) ? et_pb_extract_shortcode_content( $content, $shortcode_name ) : et_pb_fix_shortcodes( wpautop( et_pb_extract_shortcode_content( $content, $shortcode_name ) ) );

		return $global_shortcode_content;
	}
}

function et_builder_get_columns() {
	$columns = array(
		'specialty' => array(
			'1_2,1_2' => array(
				'position' => '1,0',
				'columns'  => '2',
			),
			'1_2,1_2' => array(
				'position' => '0,1',
				'columns'  => '2',
			),
			'1_4,3_4' => array(
				'position' => '0,1',
				'columns'  => '3',
			),
			'3_4,1_4' => array(
				'position' => '1,0',
				'columns'  => '3',
			),
			'1_4,1_2,1_4' => array(
				'position' => '0,1,0',
				'columns'  => '2',
			),
			'1_2,1_4,1_4' => array(
				'position' => '1,0,0',
				'columns'  => '2',
			),
			'1_4,1_4,1_2' => array(
				'position' => '0,0,1',
				'columns'  => '2',
			),
			'1_3,2_3' => array(
				'position' => '0,1',
				'columns'  => '2',
			),
			'2_3,1_3' => array(
				'position' => '1,0',
				'columns'  => '2',
			),
		),
		'regular' => array(
			'4_4',
			'1_2,1_2',
			'1_3,1_3,1_3',
			'1_4,1_4,1_4,1_4',
			'2_3,1_3',
			'1_3,2_3',
			'1_4,3_4',
			'3_4,1_4',
			'1_2,1_4,1_4',
			'1_4,1_4,1_2',
			'1_4,1_2,1_4',
		)
	);

	return apply_filters( 'et_builder_get_columns', $columns );
}

function et_builder_get_columns_layout() {
	$layout_columns =
		'<% if ( typeof et_pb_specialty !== \'undefined\' && et_pb_specialty === \'on\' ) { %>
			<li data-layout="1_2,1_2" data-specialty="1,0" data-specialty_columns="2">
				<div class="et_pb_layout_column et_pb_column_layout_1_2 et_pb_variations et_pb_2_variations">
					<div class="et_pb_variation et_pb_variation_full"></div>
					<div class="et_pb_variation_row">
						<div class="et_pb_variation et_pb_variation_1_2"></div>
						<div class="et_pb_variation et_pb_variation_1_2"></div>
					</div>
				</div>
				<div class="et_pb_layout_column et_pb_column_layout_1_2 et_pb_specialty_column"></div>
			</li>

			<li data-layout="1_2,1_2" data-specialty="0,1" data-specialty_columns="2">
				<div class="et_pb_layout_column et_pb_column_layout_1_2 et_pb_specialty_column"></div>

				<div class="et_pb_layout_column et_pb_column_layout_1_2 et_pb_variations et_pb_2_variations">
					<div class="et_pb_variation et_pb_variation_full"></div>
					<div class="et_pb_variation_row">
						<div class="et_pb_variation et_pb_variation_1_2"></div>
						<div class="et_pb_variation et_pb_variation_1_2"></div>
					</div>
				</div>
			</li>

			<li data-layout="1_4,3_4" data-specialty="0,1" data-specialty_columns="3">
				<div class="et_pb_layout_column et_pb_column_layout_1_4 et_pb_specialty_column"></div>
				<div class="et_pb_layout_column et_pb_column_layout_3_4 et_pb_variations et_pb_3_variations">
					<div class="et_pb_variation et_pb_variation_full"></div>
					<div class="et_pb_variation_row">
						<div class="et_pb_variation et_pb_variation_1_2"></div>
						<div class="et_pb_variation et_pb_variation_1_2"></div>
					</div>
					<div class="et_pb_variation_row">
						<div class="et_pb_variation et_pb_variation_1_3"></div>
						<div class="et_pb_variation et_pb_variation_1_3"></div>
						<div class="et_pb_variation et_pb_variation_1_3"></div>
					</div>
				</div>
			</li>

			<li data-layout="3_4,1_4" data-specialty="1,0" data-specialty_columns="3">
				<div class="et_pb_layout_column et_pb_column_layout_3_4 et_pb_variations et_pb_3_variations">
					<div class="et_pb_variation et_pb_variation_full"></div>
					<div class="et_pb_variation_row">
						<div class="et_pb_variation et_pb_variation_1_2"></div>
						<div class="et_pb_variation et_pb_variation_1_2"></div>
					</div>
					<div class="et_pb_variation_row">
						<div class="et_pb_variation et_pb_variation_1_3"></div>
						<div class="et_pb_variation et_pb_variation_1_3"></div>
						<div class="et_pb_variation et_pb_variation_1_3"></div>
					</div>
				</div>
				<div class="et_pb_layout_column et_pb_column_layout_1_4 et_pb_specialty_column"></div>
			</li>

			<li data-layout="1_4,1_2,1_4" data-specialty="0,1,0" data-specialty_columns="2">
				<div class="et_pb_layout_column et_pb_column_layout_1_4 et_pb_specialty_column"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_2 et_pb_variations et_pb_2_variations">
					<div class="et_pb_variation et_pb_variation_full"></div>
					<div class="et_pb_variation_row">
						<div class="et_pb_variation et_pb_variation_1_2"></div>
						<div class="et_pb_variation et_pb_variation_1_2"></div>
					</div>
				</div>
				<div class="et_pb_layout_column et_pb_column_layout_1_4 et_pb_specialty_column"></div>
			</li>

			<li data-layout="1_2,1_4,1_4" data-specialty="1,0,0" data-specialty_columns="2">
				<div class="et_pb_layout_column et_pb_column_layout_1_2 et_pb_variations et_pb_2_variations">
					<div class="et_pb_variation et_pb_variation_full"></div>
					<div class="et_pb_variation_row">
						<div class="et_pb_variation et_pb_variation_1_2"></div>
						<div class="et_pb_variation et_pb_variation_1_2"></div>
					</div>
				</div>
				<div class="et_pb_layout_column et_pb_column_layout_1_4 et_pb_specialty_column"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_4 et_pb_specialty_column"></div>
			</li>

			<li data-layout="1_4,1_4,1_2" data-specialty="0,0,1" data-specialty_columns="2">
				<div class="et_pb_layout_column et_pb_column_layout_1_4 et_pb_specialty_column"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_4 et_pb_specialty_column"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_2 et_pb_variations et_pb_2_variations">
					<div class="et_pb_variation et_pb_variation_full"></div>
					<div class="et_pb_variation_row">
						<div class="et_pb_variation et_pb_variation_1_2"></div>
						<div class="et_pb_variation et_pb_variation_1_2"></div>
					</div>
				</div>
			</li>

			<li data-layout="1_3,2_3" data-specialty="0,1" data-specialty_columns="2">
				<div class="et_pb_layout_column et_pb_column_layout_1_3 et_pb_specialty_column"></div>
				<div class="et_pb_layout_column et_pb_column_layout_2_3 et_pb_variations et_pb_2_variations">
					<div class="et_pb_variation et_pb_variation_full"></div>
					<div class="et_pb_variation_row">
						<div class="et_pb_variation et_pb_variation_1_2"></div>
						<div class="et_pb_variation et_pb_variation_1_2"></div>
					</div>
				</div>
			</li>

			<li data-layout="2_3,1_3" data-specialty="1,0" data-specialty_columns="2">
				<div class="et_pb_layout_column et_pb_column_layout_2_3 et_pb_variations et_pb_2_variations">
					<div class="et_pb_variation et_pb_variation_full"></div>
					<div class="et_pb_variation_row">
						<div class="et_pb_variation et_pb_variation_1_2"></div>
						<div class="et_pb_variation et_pb_variation_1_2"></div>
					</div>
				</div>
				<div class="et_pb_layout_column et_pb_column_layout_1_3 et_pb_specialty_column"></div>
			</li>
		<% } else if ( typeof view !== \'undefined\' && typeof view.model.attributes.specialty_columns !== \'undefined\' ) { %>
			<li data-layout="4_4">
				<div class="et_pb_layout_column et_pb_column_layout_fullwidth"></div>
			</li>
			<li data-layout="1_2,1_2">
				<div class="et_pb_layout_column et_pb_column_layout_1_2"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_2"></div>
			</li>

			<% if ( view.model.attributes.specialty_columns === 3 ) { %>
				<li data-layout="1_3,1_3,1_3">
					<div class="et_pb_layout_column et_pb_column_layout_1_3"></div>
					<div class="et_pb_layout_column et_pb_column_layout_1_3"></div>
					<div class="et_pb_layout_column et_pb_column_layout_1_3"></div>
				</li>
			<% } %>
		<% } else { %>
			<li data-layout="4_4">
				<div class="et_pb_layout_column et_pb_column_layout_fullwidth"></div>
			</li>
			<li data-layout="1_2,1_2">
				<div class="et_pb_layout_column et_pb_column_layout_1_2"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_2"></div>
			</li>
			<li data-layout="1_3,1_3,1_3">
				<div class="et_pb_layout_column et_pb_column_layout_1_3"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_3"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_3"></div>
			</li>
			<li data-layout="1_4,1_4,1_4,1_4">
				<div class="et_pb_layout_column et_pb_column_layout_1_4"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_4"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_4"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_4"></div>
			</li>
			<li data-layout="2_3,1_3">
				<div class="et_pb_layout_column et_pb_column_layout_2_3"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_3"></div>
			</li>
			<li data-layout="1_3,2_3">
				<div class="et_pb_layout_column et_pb_column_layout_1_3"></div>
				<div class="et_pb_layout_column et_pb_column_layout_2_3"></div>
			</li>
			<li data-layout="1_4,3_4">
				<div class="et_pb_layout_column et_pb_column_layout_1_4"></div>
				<div class="et_pb_layout_column et_pb_column_layout_3_4"></div>
			</li>
			<li data-layout="3_4,1_4">
				<div class="et_pb_layout_column et_pb_column_layout_3_4"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_4"></div>
			</li>
			<li data-layout="1_2,1_4,1_4">
				<div class="et_pb_layout_column et_pb_column_layout_1_2"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_4"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_4"></div>
			</li>
			<li data-layout="1_4,1_4,1_2">
				<div class="et_pb_layout_column et_pb_column_layout_1_4"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_4"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_2"></div>
			</li>
			<li data-layout="1_4,1_2,1_4">
				<div class="et_pb_layout_column et_pb_column_layout_1_4"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_2"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_4"></div>
			</li>
	<%
		}
	%>';

	return apply_filters( 'et_builder_layout_columns', $layout_columns );
}

function et_pb_pagebuilder_meta_box() {
	global $typenow, $post;

	do_action( 'et_pb_before_page_builder' );

	echo '<div id="et_pb_hidden_editor">';
	wp_editor(
		'',
		'et_pb_content_new',
		array(
			'media_buttons' => true,
			'tinymce' => array(
				'wp_autoresize_on' => true
			)
		)
	);
	echo '</div>';

	printf(
		'<div id="et_pb_main_container" class="post-type-%1$s%2$s"></div>',
		esc_attr( $typenow ),
		! et_pb_is_allowed( 'move_module' ) ? ' et-pb-disable-sort' : ''
	);
	$rename_module_menu = sprintf(
		'<%% if ( this.hasOption( "rename" ) ) { %%>
			<li><a class="et-pb-right-click-rename" href="#">%1$s</a></li>
		<%% } %%>',
		esc_html__( 'Rename', 'et_builder' )
	);
	$copy_module_menu = sprintf(
		'<%% if ( this.hasOption( "copy" ) ) { %%>
			<li><a class="et-pb-right-click-copy" href="#">%1$s</a></li>
		<%% } %%>',
		esc_html__( 'Copy', 'et_builder' )
	);
	$paste_after_menu = sprintf(
		'<%% if ( this.hasOption( "paste-after" ) ) { %%>
			<li><a class="et-pb-right-click-paste-after" href="#">%1$s</a></li>
		<%% } %%>',
		esc_html__( 'Paste After', 'et_builder' )
	);
	$paste_menu_item = sprintf(
		'<%% if ( this.hasOption( "paste-column" ) ) { %%>
			<li><a class="et-pb-right-click-paste-column" href="#">%1$s</a></li>
		<%% } %%>',
		esc_html__( 'Paste', 'et_builder' )
	);
	$paste_app_menu_item = sprintf(
		'<%% if ( this.hasOption( "paste-app" ) ) { %%>
			<li><a class="et-pb-right-click-paste-app" href="#">%1$s</a></li>
		<%% } %%>',
		esc_html__( 'Paste', 'et_builder' )
	);
	$save_to_lib_menu = sprintf(
		'<%% if ( this.hasOption( "save-to-library") ) { %%>
			<li><a class="et-pb-right-click-save-to-library" href="#">%1$s</a></li>
		<%% } %%>',
		esc_html__( 'Save to Library', 'et_builder' )
	);
	$lock_unlock_menu = sprintf(
		'<%% if ( this.hasOption( "lock" ) ) { %%>
			<li><a class="et-pb-right-click-lock" href="#"><span class="unlock">%1$s</span><span class="lock">%2$s</span></a></li>
		<%% } %%>',
		esc_html__( 'Unlock', 'et_builder' ),
		esc_html__( 'Lock', 'et_builder' )
	);
	$enable_disable_menu = sprintf(
		'<%% if ( this.hasOption( "disable" ) ) { %%>
			<li><a class="et-pb-right-click-disable" href="#"><span class="enable">%1$s</span><span class="disable">%2$s</span></a>
				<span class="et_pb_disable_on_options"><span class="et_pb_disable_on_option et_pb_disable_on_phone"></span><span class="et_pb_disable_on_option et_pb_disable_on_tablet"></span><span class="et_pb_disable_on_option et_pb_disable_on_desktop"></span></span>
			</li>
		<%% } %%>',
		esc_html__( 'Enable', 'et_builder' ),
		esc_html__( 'Disable', 'et_builder' )
	);
	$start_ab_testing_menu = sprintf(
		'<%% if ( this.hasOption( "start-ab-testing") ) { %%>
			<li><a class="et-pb-right-click-start-ab-testing" href="#">%1$s</a></li>
		<%% } %%>',
		esc_html__( 'Split Test', 'et_builder' )
	);
	$end_ab_testing_menu = sprintf(
		'<%% if ( this.hasOption( "end-ab-testing") ) { %%>
			<li><a class="et-pb-right-click-end-ab-testing" href="#">%1$s</a></li>
		<%% } %%>',
		esc_html__( 'End Split Test', 'et_builder' )
	);
	$disable_global_menu = sprintf(
		'<%% if ( this.hasOption( "disable-global") ) { %%>
			<li><a class="et-pb-right-click-disable-global" href="#">%1$s</a></li>
		<%% } %%>',
		esc_html__( 'Disable Global', 'et_builder' )
	);
	// Right click options Template
	printf(
		'<script type="text/template" id="et-builder-right-click-controls-template">
		<ul class="options">
			<%% if ( "module" !== this.options.model.attributes.type || _.contains( %13$s, this.options.model.attributes.module_type ) ) { %%>
				%1$s

				%15$s

				%16$s

				%17$s

				%8$s

				<%% if ( this.hasOption( "undo" ) ) { %%>
				<li><a class="et-pb-right-click-undo" href="#">%9$s</a></li>
				<%% } %%>

				<%% if ( this.hasOption( "redo" ) ) { %%>
				<li><a class="et-pb-right-click-redo" href="#">%10$s</a></li>
				<%% } %%>

				%2$s

				%3$s

				<%% if ( this.hasOption( "collapse" ) ) { %%>
				<li><a class="et-pb-right-click-collapse" href="#"><span class="expand">%4$s</span><span class="collapse">%5$s</span></a></li>
				<%% } %%>

				%6$s

				%7$s

				%12$s

				%11$s

			<%% } %%>

			<%% if ( this.hasOption( "preview" ) ) { %%>
			<li><a class="et-pb-right-click-preview" href="#">%14$s</a></li>
			<%% } %%>
		</ul>
		</script>',
		et_pb_is_allowed( 'edit_module' ) && ( et_pb_is_allowed( 'general_settings' ) || et_pb_is_allowed( 'advanced_settings' ) || et_pb_is_allowed( 'custom_css_settings' ) ) ? $rename_module_menu : '',
		et_pb_is_allowed( 'disable_module' ) ? $enable_disable_menu : '',
		et_pb_is_allowed( 'lock_module' ) ? $lock_unlock_menu : '',
		esc_html__( 'Expand', 'et_builder' ),
		esc_html__( 'Collapse', 'et_builder' ), //#5
		et_pb_is_allowed( 'add_module' ) ? $copy_module_menu : '',
		et_pb_is_allowed( 'add_module' ) ? $paste_after_menu : '',
		et_pb_is_allowed( 'divi_library' ) && et_pb_is_allowed( 'save_library' ) ? $save_to_lib_menu : '',
		esc_html__( 'Undo', 'et_builder' ),
		esc_html__( 'Redo', 'et_builder' ), //#10
		et_pb_is_allowed( 'add_module' ) ? $paste_menu_item : '',
		et_pb_is_allowed( 'add_module' ) ? $paste_app_menu_item : '',
		et_pb_allowed_modules_list(),
		esc_html__( 'Preview', 'et_builder' ),
		et_pb_is_allowed( 'ab_testing' ) ? $start_ab_testing_menu : '', // #15
		et_pb_is_allowed( 'ab_testing' ) ? $end_ab_testing_menu : '',
		et_pb_is_allowed( 'edit_module' ) && et_pb_is_allowed( 'edit_global_library' ) ? $disable_global_menu : ''
	);

	// "Rename Module Admin Label" Modal Window Template
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-rename_admin_label">
			<div class="et_pb_prompt_modal">
				<a href="#" class="et_pb_prompt_dont_proceed et-pb-modal-close">
					<span>%1$s</span>
				</a>
				<div class="et_pb_prompt_buttons">
					<br/>
					<input type="submit" class="et_pb_prompt_proceed" value="%2$s" />
				</div>
			</div>
		</script>',
		esc_html__( 'Cancel', 'et_builder' ),
		esc_attr__( 'Save', 'et_builder' )
	);

	// "Rename Module Admin Label" Modal Content Template
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-rename_admin_label-text">
			<h3>%1$s</h3>
			<p>%2$s</p>

			<input type="text" value="" id="et_pb_new_admin_label" class="regular-text" />
		</script>',
		esc_html__( 'Rename', 'et_builder' ),
		esc_html__( 'Enter a new name for this module', 'et_builder' )
	);

	// Builder's Main Buttons
	$save_to_lib_button = sprintf(
		'<a href="#" class="et-pb-layout-buttons et-pb-layout-buttons-save" title="%1$s">
			<span>%2$s</span>
		</a>',
		esc_attr__( 'Save to Library', 'et_builder' ),
		esc_html__( 'Save to Library', 'et_builder' )
	);

	$load_from_lib_button = sprintf(
		'<a href="#" class="et-pb-layout-buttons et-pb-layout-buttons-load" title="%1$s">
			<span>%2$s</span>
		</a>',
		esc_attr__( 'Load From Library', 'et_builder' ),
		esc_html__( 'Load From Library', 'et_builder' )
	);

	$clear_layout_button = sprintf(
		'<a href="#" class="et-pb-layout-buttons et-pb-layout-buttons-clear" title="%1$s">
			<span>%2$s</span>
		</a>',
		esc_attr__( 'Clear Layout', 'et_builder' ),
		esc_html__( 'Clear Layout', 'et_builder' )
	);

	// Builder's History Buttons
	$history_button = sprintf(
		'<a href="#" class="et-pb-layout-buttons et-pb-layout-buttons-history" title="%1$s">
			<span class="icon"></span><span class="label">%2$s</span>
		</a>',
		esc_attr__( 'See History', 'et_builder' ),
		esc_html__( 'See History', 'et_builder' )
	);

	$redo_button = sprintf(
		'<a href="#" class="et-pb-layout-buttons et-pb-layout-buttons-redo" title="%1$s">
			<span class="icon"></span><span class="label">%2$s</span>
		</a>',
		esc_attr__( 'Redo', 'et_builder' ),
		esc_html__( 'Redo', 'et_builder' )
	);

	$undo_button = sprintf(
		'<a href="#" class="et-pb-layout-buttons et-pb-layout-buttons-undo" title="%1$s">
			<span class="icon"></span><span class="label">%2$s</span>
		</a>',
		esc_attr__( 'Undo', 'et_builder' ),
		esc_html__( 'Undo', 'et_builder' )
	);

	// App View Stats Button
	$view_ab_stats_button = sprintf(
		'<a href="#" class="et-pb-layout-buttons et-pb-layout-buttons-view-ab-stats" title="%1$s">
			<span class="icon"></span><span class="label">%2$s</span>
		</a>',
		esc_attr__( 'View Stats', 'et_builder' ),
		esc_html__( 'View Stats', 'et_builder' )
	);

	// App Settings Button
	$settings_button = sprintf(
		'<a href="#" class="et-pb-layout-buttons et-pb-layout-buttons-settings" title="%1$s">
			<span class="icon"></span><span class="label">%2$s</span>
		</a>',
		esc_attr__( 'Settings', 'et_builder' ),
		esc_html__( 'Settings', 'et_builder' )
	);

	// App Template
	printf(
		'<script type="text/template" id="et-builder-app-template">
			<div id="et_pb_layout_controls">
				%1$s
				%2$s
				%3$s
				%4$s
				%5$s
				%6$s
				%7$s
				%8$s
			</div>
			<div id="et-pb-histories-visualizer-overlay"></div>
			<ol id="et-pb-histories-visualizer"></ol>
		</script>',
		et_pb_is_allowed( 'divi_library' ) && et_pb_is_allowed( 'save_library' ) ? $save_to_lib_button : '',
		et_pb_is_allowed( 'divi_library' ) && et_pb_is_allowed( 'load_layout' ) && et_pb_is_allowed( 'add_library' ) && et_pb_is_allowed( 'add_module' ) ? $load_from_lib_button : '',
		et_pb_is_allowed( 'add_module' ) ? $clear_layout_button : '',
		$history_button,
		$redo_button,
		$undo_button,
		$view_ab_stats_button,
		$settings_button
	);

	// App Settings Buttons Template
	$builder_button_ab_testing_conditional = '( typeof et_pb_ab_goal === "undefined" || et_pb_ab_goal === "off" || typeof et_pb_ab_subject !== "undefined" )';

	$is_ab_active = isset( $post->ID ) && 'on' === get_post_meta( $post->ID, '_et_pb_use_ab_testing', true );

	$view_stats_active_class = $is_ab_active ? 'active' : '';

	$view_stats_button = sprintf(
		'<a href="#" class="et-pb-app-view-ab-stats-button %1$s" title="%2$s">
			<span class="icon">
				<object type="image/svg+xml" data="%3$s/images/stats.svg"></object>
			</span>
			<span class="label">%2$s</span>
		</a>',
		esc_attr( $view_stats_active_class ),
		esc_attr__( 'View Split Testing Stats', 'et_builder' ),
		esc_url( ET_BUILDER_URI )
	);

	$portability_class = 'et-pb-app-portability-button';

	if ( $is_ab_active ) {
		$portability_class .= ' et-core-disabled';
	}

	printf(
		'<script type="text/template" id="et-builder-app-settings-button-template">
			<a href="#" class="et-pb-app-settings-button" title="%1$s">
				<span class="icon">
					<object type="image/svg+xml" data="%5$s/images/menu.svg"></object>
				</span>
				<span class="label">%2$s</span>
			</a>
			%3$s
			%4$s
		</script>',
		esc_attr__( 'Settings', 'et_builder' ),
		esc_html__( 'Settings', 'et_builder' ),
		et_core_portability_link( 'et_builder', array( 'class' => $portability_class ) ),
		et_pb_is_allowed( 'ab_testing' ) ? $view_stats_button : '',
		esc_url( ET_BUILDER_URI )
	);

	$section_settings_button = sprintf(
		'<%% if ( ( typeof et_pb_template_type === \'undefined\' || \'section\' === et_pb_template_type || \'\' === et_pb_template_type )%3$s ) { %%>
			<a href="#" class="et-pb-settings et-pb-settings-section" title="%1$s"><span>%2$s</span></a>
		<%% } %%>',
		esc_attr__( 'Settings', 'et_builder' ),
		esc_html__( 'Settings', 'et_builder' ),
		! et_pb_is_allowed( 'edit_global_library' ) ? ' && typeof et_pb_global_module === "undefined"' : '' // do not display settings on global sections if not allowed for current user
	);
	$section_clone_button = sprintf(
		'%3$s
			<a href="#" class="et-pb-clone et-pb-clone-section" title="%1$s"><span>%2$s</span></a>
		%4$s',
		esc_attr__( 'Clone Section', 'et_builder' ),
		esc_html__( 'Clone Section', 'et_builder' ),
		'<% if ( ' . $builder_button_ab_testing_conditional . ' ) { %>',
		'<% } %>'
	);
	$section_remove_button = sprintf(
		'%3$s
			<a href="#" class="et-pb-remove et-pb-remove-section" title="%1$s"><span>%2$s</span></a>
		%4$s',
		esc_attr__( 'Delete Section', 'et_builder' ),
		esc_html__( 'Delete Section', 'et_builder' ),
		'<% if ( ' . $builder_button_ab_testing_conditional . ' ) { %>',
		'<% } %>'
	);
	$section_unlock_button = sprintf(
		'<a href="#" class="et-pb-unlock" title="%1$s"><span>%2$s</span></a>',
		esc_attr__( 'Unlock Section', 'et_builder' ),
		esc_html__( 'Unlock Section', 'et_builder' )
	);
	// Section Template
	$settings_controls = sprintf(
		'<div class="et-pb-controls">
			%1$s

			<%% if ( typeof et_pb_template_type === \'undefined\' || ( \'section\' !== et_pb_template_type && \'row\' !== et_pb_template_type && \'module\' !== et_pb_template_type ) ) { %%>
				%2$s
				%3$s
			<%% } %%>

			<a href="#" class="et-pb-expand" title="%4$s"><span>%5$s</span></a>
			%6$s
		</div>',
		et_pb_is_allowed( 'edit_module' ) && ( et_pb_is_allowed( 'general_settings' ) || et_pb_is_allowed( 'advanced_settings' ) || et_pb_is_allowed( 'custom_css_settings' ) ) ? $section_settings_button : '',
		et_pb_is_allowed( 'add_module' ) ? $section_clone_button : '',
		et_pb_is_allowed( 'add_module' ) ? $section_remove_button : '',
		esc_attr__( 'Expand Section', 'et_builder' ),
		esc_html__( 'Expand Section', 'et_builder' ),
		et_pb_is_allowed( 'lock_module' ) ? $section_unlock_button : ''
	);

	$add_from_lib_section = sprintf(
		'<span class="et-pb-section-add-saved">%1$s</span>',
		esc_html__( 'Add From Library', 'et_builder' )
	);

	$add_standard_section_button = sprintf(
		'<span class="et-pb-section-add-main">%1$s</span>',
		esc_html__( 'Standard Section', 'et_builder' )
	);
	$add_standard_section_button = apply_filters( 'et_builder_add_main_section_button', $add_standard_section_button );

	$add_fullwidth_section_button = sprintf(
		'<span class="et-pb-section-add-fullwidth">%1$s</span>',
		esc_html__( 'Fullwidth Section', 'et_builder' )
	);
	$add_fullwidth_section_button = apply_filters( 'et_builder_add_fullwidth_section_button', $add_fullwidth_section_button );

	$add_specialty_section_button = sprintf(
		'<span class="et-pb-section-add-specialty">%1$s</span>',
		esc_html__( 'Specialty Section', 'et_builder' )
	);
	$add_specialty_section_button = apply_filters( 'et_builder_add_specialty_section_button', $add_specialty_section_button );

	$settings_add_controls = sprintf(
		'<%% if ( typeof et_pb_template_type === \'undefined\' || ( \'section\' !== et_pb_template_type && \'row\' !== et_pb_template_type && \'module\' !== et_pb_template_type ) ) { %%>
			<a href="#" class="et-pb-section-add">
				%1$s
				%2$s
				%3$s
				%4$s
			</a>
		<%% } %%>',
		$add_standard_section_button,
		$add_fullwidth_section_button,
		$add_specialty_section_button,
		et_pb_is_allowed( 'divi_library' ) && et_pb_is_allowed( 'add_library' ) ? $add_from_lib_section : ''
	);

	$insert_first_row_button = sprintf(
		'<a href="#" class="et-pb-insert-row">
			<span>%1$s</span>
		</a>',
		esc_html__( 'Insert Row(s)', 'et_builder' )
	);

	printf(
		'<script type="text/template" id="et-builder-section-template">
			<div class="et-pb-right-click-trigger-overlay"></div>
			%1$s
			<div class="et-pb-section-content et-pb-data-cid%3$s%4$s" data-cid="<%%= cid %%>" data-skip="<%%= typeof( et_pb_skip_module ) === \'undefined\' ? \'false\' : \'true\' %%>">
				%5$s
			</div>
			%2$s
			<div class="et-pb-locked-overlay et-pb-locked-overlay-section"></div>
			<span class="et-pb-section-title"><%%= admin_label.replace( /%%22/g, "&quot;" ).replace( /%%91/g, "&#91;" ).replace( /%%93/g, "&#93;" ) %%></span>
		</script>',
		apply_filters( 'et_builder_section_settings_controls', $settings_controls ),
		et_pb_is_allowed( 'add_module' ) ? apply_filters( 'et_builder_section_add_controls', $settings_add_controls ) : '',
		! et_pb_is_allowed( 'move_module' ) ? ' et-pb-disable-sort' : '',
		! et_pb_is_allowed( 'edit_global_library' )
			? sprintf( '<%%= typeof et_pb_global_module !== \'undefined\' ? \' et-pb-disable-sort\' : \'\' %%>' )
			: '',
		et_pb_is_allowed( 'add_module' ) ? $insert_first_row_button : ''
	);

	$row_settings_button = sprintf(
		'<%% if ( ( typeof et_pb_template_type === \'undefined\' || et_pb_template_type !== \'module\' )%3$s ) { %%>
			<a href="#" class="et-pb-settings et-pb-settings-row" title="%1$s"><span>%2$s</span></a>
		<%% } %%>',
		esc_attr__( 'Settings', 'et_builder' ),
		esc_html__( 'Settings', 'et_builder' ),
		! et_pb_is_allowed( 'edit_global_library' ) ? ' && ( typeof et_pb_global_module === "undefined" || "" === et_pb_global_module ) && ( typeof et_pb_global_parent === "undefined" || "" === et_pb_global_parent )' : '' // do not display settings button on global rows if not allowed for current user
	);
	$row_clone_button = sprintf(
		'%3$s
			<a href="#" class="et-pb-clone et-pb-clone-row" title="%1$s"><span>%2$s</span></a>
		%4$s',
		esc_attr__( 'Clone Row', 'et_builder' ),
		esc_html__( 'Clone Row', 'et_builder' ),
		! et_pb_is_allowed( 'edit_global_library' ) ? '<% if ( ( typeof et_pb_global_parent === "undefined" || "" === et_pb_global_parent ) && '. $builder_button_ab_testing_conditional .' ) { %>' : '<% if ( ' . $builder_button_ab_testing_conditional . ' ) { %>', // do not display clone button on rows within global sections if not allowed for current user
		'<% } %>'
	);
	$row_remove_button = sprintf(
		'%3$s
			<a href="#" class="et-pb-remove et-pb-remove-row" title="%1$s"><span>%2$s</span></a>
		%4$s',
		esc_attr__( 'Delete Row', 'et_builder' ),
		esc_html__( 'Delete Row', 'et_builder' ),
		! et_pb_is_allowed( 'edit_global_library' ) ? '<% if ( ( typeof et_pb_global_parent === "undefined" || "" === et_pb_global_parent  ) && '. $builder_button_ab_testing_conditional .') { %>' : '<% if ( ' . $builder_button_ab_testing_conditional . ' ) { %>', // do not display clone button on rows within global sections if not allowed for current user
		'<% } %>'
	);
	$row_change_structure_button = sprintf(
		'%3$s
			<a href="#" class="et-pb-change-structure" title="%1$s"><span>%2$s</span></a>
		%4$s',
		esc_attr__( 'Change Structure', 'et_builder' ),
		esc_html__( 'Change Structure', 'et_builder' ),
		! et_pb_is_allowed( 'edit_global_library' ) ? '<% if ( ( typeof et_pb_global_module === "undefined" || "" === et_pb_global_module ) && ( typeof et_pb_global_parent === "undefined" || "" === et_pb_global_parent ) ) { %>' : '', // do not display change structure button on global rows if not allowed for current user
		! et_pb_is_allowed( 'edit_global_library' ) ? '<% } %>' : ''
	);
	$row_unlock_button = sprintf(
		'<a href="#" class="et-pb-unlock" title="%1$s"><span>%2$s</span></a>',
		esc_attr__( 'Unlock Row', 'et_builder' ),
		esc_html__( 'Unlock Row', 'et_builder' )
	);
	// Row Template
	$settings = sprintf(
		'<div class="et-pb-controls">
			%1$s
		<%% if ( typeof et_pb_template_type === \'undefined\' || \'section\' === et_pb_template_type ) { %%>
			%2$s
		<%% }

		if ( typeof et_pb_template_type === \'undefined\' || et_pb_template_type !== \'module\' ) { %%>
			%4$s
		<%% }

		if ( typeof et_pb_template_type === \'undefined\' || \'section\' === et_pb_template_type ) { %%>
			%3$s
		<%% } %%>

		<a href="#" class="et-pb-expand" title="%5$s"><span>%6$s</span></a>
		%7$s
		</div>',
		et_pb_is_allowed( 'edit_module' ) && ( et_pb_is_allowed( 'general_settings' ) || et_pb_is_allowed( 'advanced_settings' ) || et_pb_is_allowed( 'custom_css_settings' ) ) ? $row_settings_button : '',
		et_pb_is_allowed( 'add_module' ) ? $row_clone_button : '',
		et_pb_is_allowed( 'add_module' ) ? $row_remove_button : '',
		et_pb_is_allowed( 'edit_module' ) && ( et_pb_is_allowed( 'general_settings' ) || et_pb_is_allowed( 'advanced_settings' ) || et_pb_is_allowed( 'custom_css_settings' ) ) ? $row_change_structure_button : '',
		esc_attr__( 'Expand Row', 'et_builder' ),
		esc_html__( 'Expand Row', 'et_builder' ),
		et_pb_is_allowed( 'lock_module' ) ? $row_unlock_button : ''
	);

	$row_class = sprintf(
		'class="et-pb-row-content et-pb-data-cid%1$s%2$s <%%= typeof et_pb_template_type !== \'undefined\' && \'module\' === et_pb_template_type ? \' et_pb_hide_insert\' : \'\' %%>"',
		! et_pb_is_allowed( 'move_module' ) ? ' et-pb-disable-sort' : '',
		! et_pb_is_allowed( 'edit_global_library' )
			? sprintf( '<%%= typeof et_pb_global_parent !== \'undefined\' || typeof et_pb_global_module !== \'undefined\' ? \' et-pb-disable-sort\' : \'\' %%>' )
			: ''
	);

	$data_skip = 'data-skip="<%= typeof( et_pb_skip_module ) === \'undefined\' ? \'false\' : \'true\' %>"';

	$add_row_button = sprintf(
		'<%% if ( ( typeof et_pb_template_type === \'undefined\' || \'section\' === et_pb_template_type )%2$s ) { %%>
			<a href="#" class="et-pb-row-add">
				<span>%1$s</span>
			</a>
		<%% } %%>',
		esc_html__( 'Add Row', 'et_builder' ),
		! et_pb_is_allowed( 'edit_global_library' ) ? ' && typeof et_pb_global_parent === "undefined"' : '' // do not display add row buton on global sections if not allowed for current user
	);

	$insert_column_button = sprintf(
		'<a href="#" class="et-pb-insert-column">
			<span>%1$s</span>
		</a>',
		esc_html__( 'Insert Column(s)', 'et_builder' )
	);

	printf(
		'<script type="text/template" id="et-builder-row-template">
			<div class="et-pb-right-click-trigger-overlay"></div>
			%1$s
			<div data-cid="<%%= cid %%>" %2$s %3$s>
				<div class="et-pb-row-container"></div>
				%4$s
			</div>
			%5$s
			<div class="et-pb-locked-overlay et-pb-locked-overlay-row"></div>
			<span class="et-pb-row-title"><%%= admin_label.replace( /%%22/g, "&quot;" ).replace( /%%91/g, "&#91;" ).replace( /%%93/g, "&#93;" ) %%></span>
		</script>',
		apply_filters( 'et_builder_row_settings_controls', $settings ),
		$row_class,
		$data_skip,
		et_pb_is_allowed( 'add_module' ) ? $insert_column_button : '',
		et_pb_is_allowed( 'add_module' ) ? $add_row_button : ''
	);


	// Module Block Template
	$clone_button = sprintf(
		'<%% if ( ( typeof et_pb_template_type === \'undefined\' || et_pb_template_type !== \'module\' )%3$s && _.contains(%4$s, module_type) && '. $builder_button_ab_testing_conditional .' ) { %%>
			<a href="#" class="et-pb-clone et-pb-clone-module" title="%1$s">
				<span>%2$s</span>
			</a>
		<%% } %%>',
		esc_attr__( 'Clone Module', 'et_builder' ),
		esc_html__( 'Clone Module', 'et_builder' ),
		! et_pb_is_allowed( 'edit_global_library' ) ? ' &&  ( typeof et_pb_global_parent === "undefined" || "" === et_pb_global_parent )' : '',
		et_pb_allowed_modules_list()
	);
	$remove_button = sprintf(
		'<%% if ( ( typeof et_pb_template_type === \'undefined\' || et_pb_template_type !== \'module\' )%3$s && _.contains(%4$s, module_type) && '. $builder_button_ab_testing_conditional .' ) { %%>
			<a href="#" class="et-pb-remove et-pb-remove-module" title="%1$s">
				<span>%2$s</span>
			</a>
		<%% } %%>',
		esc_attr__( 'Remove Module', 'et_builder' ),
		esc_html__( 'Remove Module', 'et_builder' ),
		! et_pb_is_allowed( 'edit_global_library' ) ? ' &&  ( typeof et_pb_global_parent === "undefined" || "" === et_pb_global_parent )' : '',
		et_pb_allowed_modules_list()
	);
	$unlock_button = sprintf(
		'<%% if ( typeof et_pb_template_type === \'undefined\' || et_pb_template_type !== \'module\' ) { %%>
			<a href="#" class="et-pb-unlock" title="%1$s">
				<span>%2$s</span>
			</a>
		<%% } %%>',
		esc_html__( 'Unlock Module', 'et_builder' ),
		esc_attr__( 'Unlock Module', 'et_builder' )
	);
	$settings_button = sprintf(
		'<%% if (%3$s _.contains( %4$s, module_type ) ) { %%>
			<a href="#" class="et-pb-settings" title="%1$s">
				<span>%2$s</span>
			</a>
		<%% } %%>',
		esc_attr__( 'Module Settings', 'et_builder' ),
		esc_html__( 'Module Settings', 'et_builder' ),
		! et_pb_is_allowed( 'edit_global_library' ) ? ' ( typeof et_pb_global_parent === "undefined" || "" === et_pb_global_parent ) && ( typeof et_pb_global_module === "undefined" || "" === et_pb_global_module ) &&' : '',
		et_pb_allowed_modules_list()
	);

	printf(
		'<script type="text/template" id="et-builder-block-module-template">
			%1$s
			%2$s
			%3$s
			%4$s
			<span class="et-pb-module-title"><%%= admin_label.replace( /%%22/g, "&quot;" ).replace( /%%91/g, "&#91;" ).replace( /%%93/g, "&#93;" ) %%></span>
		</script>',
		et_pb_is_allowed( 'edit_module' ) && ( et_pb_is_allowed( 'general_settings' ) || et_pb_is_allowed( 'advanced_settings' ) || et_pb_is_allowed( 'custom_css_settings' ) ) ? $settings_button : '',
		et_pb_is_allowed( 'add_module' ) ? $clone_button : '',
		et_pb_is_allowed( 'add_module' ) ? $remove_button : '',
		et_pb_is_allowed( 'lock_module' ) ? $unlock_button : ''
	);


	// Modal Template
	$save_exit_button = sprintf(
		'<a href="#" class="et-pb-modal-save button button-primary">
			<span>%1$s</span>
		</a>',
		esc_html__( 'Save & Exit', 'et_builder' )
	);

	$save_template_button = sprintf(
		'<%% if ( typeof et_pb_template_type === \'undefined\' || \'\' === et_pb_template_type ) { %%>
			<a href="#" class="et-pb-modal-save-template button">
				<span>%1$s</span>
			</a>
		<%% } %%>',
		esc_html__( 'Save & Add To Library', 'et_builder' )
	);

	$preview_template_button = sprintf(
		'<a href="#" class="et-pb-modal-preview-template button">
			<span class="icon"></span>
			<span class="label">%1$s</span>
		</a>',
		esc_html__( 'Preview', 'et_builder' )
	);

	$can_edit_or_has_modal_view_tab = et_pb_is_allowed( 'edit_module' ) && ( et_pb_is_allowed( 'general_settings' ) || et_pb_is_allowed( 'advanced_settings' ) || et_pb_is_allowed( 'custom_css_settings' ) );

	printf(
		'<script type="text/template" id="et-builder-modal-template">
			<div class="et-pb-modal-container%6$s">

				<a href="#" class="et-pb-modal-close">
					<span>%1$s</span>
				</a>

			<%% if ( ! ( typeof open_view !== \'undefined\' && open_view === \'column_specialty_settings\' ) && typeof type !== \'undefined\' && ( type === \'module\' || type === \'section\' || type === \'row_inner\' || ( type === \'row\' && typeof open_view === \'undefined\' ) ) ) { %%>
				<div class="et-pb-modal-bottom-container%4$s">
					%2$s
					%5$s
					%3$s
				</div>
			<%% } %%>

			</div>
		</script>',
		esc_html__( 'Cancel', 'et_builder' ),
		et_pb_is_allowed( 'divi_library' ) && et_pb_is_allowed( 'save_library' ) ? $save_template_button : '',
		$can_edit_or_has_modal_view_tab ? $save_exit_button : '',
		! et_pb_is_allowed( 'divi_library' ) || ! et_pb_is_allowed( 'save_library' ) ? ' et_pb_single_button' : '',
		$preview_template_button,
		$can_edit_or_has_modal_view_tab ? '' : ' et_pb_no_editing'
	);


	// Column Settings Template
	$columns_number =
		'<% if ( view.model.attributes.specialty_columns === 3 ) { %>
			3
		<% } else { %>
			2
		<% } %>';
	$data_specialty_columns = sprintf(
		'<%% if ( typeof view !== \'undefined\' && typeof view.model.attributes.specialty_columns !== \'undefined\' ) { %%>
			data-specialty_columns="%1$s"
		<%% } %%>',
		$columns_number
	);

	$saved_row_tab = sprintf(
		'<li class="et-pb-saved-module" data-open_tab="et-pb-saved-modules-tab">
			<a href="#">%1$s</a>
		</li>',
		esc_html__( 'Add From Library', 'et_builder' )
	);
	$saved_row_container = '<% if ( ( typeof change_structure === \'undefined\' || \'true\' !== change_structure ) && ( typeof et_pb_specialty === \'undefined\' || et_pb_specialty !== \'on\' ) ) { %>
								<div class="et-pb-main-settings et-pb-main-settings-full et-pb-saved-modules-tab"></div>
							<% } %>';
	printf(
		'<script type="text/template" id="et-builder-column-settings-template">

			<h3 class="et-pb-settings-heading" data-current_row="<%%= cid %%>">%1$s</h3>

		<%% if ( ( typeof change_structure === \'undefined\' || \'true\' !== change_structure ) && ( typeof et_pb_specialty === \'undefined\' || et_pb_specialty !== \'on\' ) ) { %%>
			<ul class="et-pb-options-tabs-links et-pb-saved-modules-switcher" %2$s>
				<li class="et-pb-saved-module et-pb-options-tabs-links-active" data-open_tab="et-pb-new-modules-tab" data-content_loaded="true">
					<a href="#">%3$s</a>
				</li>
				%4$s
			</ul>
		<%% } %%>

			<div class="et-pb-main-settings et-pb-main-settings-full et-pb-new-modules-tab active-container">
				<ul class="et-pb-column-layouts">
					%5$s
				</ul>
			</div>

			%6$s

		</script>',
		esc_html__( 'Insert Columns', 'et_builder' ),
		$data_specialty_columns,
		esc_html__( 'New Row', 'et_builder' ),
		et_pb_is_allowed( 'divi_library' ) && et_pb_is_allowed( 'add_library' ) ? $saved_row_tab : '',
		et_builder_get_columns_layout(),
		et_pb_is_allowed( 'divi_library' ) && et_pb_is_allowed( 'add_library' ) ? $saved_row_container : ''
	);

	// "Add Module" Template
	$fullwidth_class =
		'<% if ( typeof module.fullwidth_only !== \'undefined\' && module.fullwidth_only === \'on\' ) { %> et_pb_fullwidth_only_module<% } %>';
	$saved_modules_tab = sprintf(
		'<li class="et-pb-saved-module" data-open_tab="et-pb-saved-modules-tab">
			<a href="#">%1$s</a>
		</li>',
		esc_html__( 'Add From Library', 'et_builder' )
	);
	$saved_modules_container = '<div class="et-pb-main-settings et-pb-main-settings-full et-pb-saved-modules-tab"></div>';
	printf(
		'<script type="text/template" id="et-builder-modules-template">
			<h3 class="et-pb-settings-heading">%1$s</h3>

			<ul class="et-pb-options-tabs-links et-pb-saved-modules-switcher">
				<li class="et-pb-new-module et-pb-options-tabs-links-active" data-open_tab="et-pb-all-modules-tab">
					<a href="#">%2$s</a>
				</li>

				%3$s
			</ul>

			<div class="et-pb-main-settings et-pb-main-settings-full et-pb-all-modules-tab active-container">
				<ul class="et-pb-all-modules">
				<%% _.each(modules, function(module) { %%>
					<%% if ( "et_pb_row" !== module.label && "et_pb_section" !== module.label && "et_pb_column" !== module.label && "et_pb_row_inner" !== module.label && _.contains(%6$s, module.label ) ) { %%>
						<li class="<%%= module.label %%>%4$s">
							<span class="et_module_title"><%%= module.title %%></span>
						</li>
					<%% } %%>
				<%% }); %%>
				</ul>
			</div>

			%5$s
		</script>',
		esc_html__( 'Insert Module', 'et_builder' ),
		esc_html__( 'New Module', 'et_builder' ),
		et_pb_is_allowed( 'divi_library' ) && et_pb_is_allowed( 'add_library' ) ? $saved_modules_tab : '',
		$fullwidth_class,
		et_pb_is_allowed( 'divi_library' ) && et_pb_is_allowed( 'add_library' ) ? $saved_modules_container : '',
		et_pb_allowed_modules_list()
	);


	// Load Layout Template
	printf(
		'<script type="text/template" id="et-builder-load_layout-template">
			<h3 class="et-pb-settings-heading">%1$s</h3>

		<%% if ( typeof display_switcher !== \'undefined\' && display_switcher === \'on\' ) { %%>
			<ul class="et-pb-options-tabs-links et-pb-saved-modules-switcher">
				<li class="et-pb-new-module et-pb-options-tabs-links-active" data-open_tab="et-pb-all-modules-tab">
					<a href="#">%2$s</a>
				</li>
				<li class="et-pb-saved-module" data-open_tab="et-pb-saved-modules-tab">
					<a href="#">%3$s</a>
				</li>
			</ul>
		<%% } %%>

		<%% if ( typeof display_switcher !== \'undefined\' && display_switcher === \'on\' ) { %%>
			<div class="et-pb-main-settings et-pb-main-settings-full et-pb-all-modules-tab active-container"></div>
			<div class="et-pb-main-settings et-pb-main-settings-full et-pb-saved-modules-tab" style="display: none;"></div>
		<%% } else { %%>
			<div class="et-pb-main-settings et-pb-main-settings-full et-pb-saved-modules-tab active-container"></div>
		<%% } %%>
		</script>',
		esc_html__( 'Load Layout', 'et_builder' ),
		esc_html__( 'Predefined Layouts', 'et_builder' ),
		esc_html__( 'Add From Library', 'et_builder' )
	);

	$insert_module_button = sprintf(
		'%2$s
		<a href="#" class="et-pb-insert-module<%%= typeof et_pb_template_type === \'undefined\' || \'module\' !== et_pb_template_type ? \'\' : \' et_pb_hidden_button\' %%>">
			<span>%1$s</span>
		</a>
		%3$s',
		esc_html__( 'Insert Module(s)', 'et_builder' ),
		! et_pb_is_allowed( 'edit_global_library' ) ? '<% if ( typeof et_pb_global_parent === "undefined" ) { %>' : '',
		! et_pb_is_allowed( 'edit_global_library' ) ? '<% } %>' : ''
	);
	// Column Template
	printf(
		'<script type="text/template" id="et-builder-column-template">
			%1$s
		</script>',
		et_pb_is_allowed( 'add_module' ) ? $insert_module_button : ''
	);

	// Insert Row(s)
	$insert_row_button = sprintf(
		'<a href="#" class="et-pb-insert-row">
			<span>%1$s</span>
		</a>',
		esc_html__( 'Insert Row(s)', 'et_builder' )
	);

	// Insert Row Template
	printf(
		'<script type="text/template" id="et-builder-specialty-column-template">
			%1$s
		</script>',
		et_pb_is_allowed( 'add_module' ) ? $insert_row_button : ''
	);

	// Advanced Settings Buttons Module
	printf(
		'<script type="text/template" id="et-builder-advanced-setting">
			<a href="#" class="et-pb-advanced-setting-remove">
				<span>%1$s</span>
			</a>

			<a href="#" class="et-pb-advanced-setting-options">
				<span>%2$s</span>
			</a>

			<a href="#" class="et-pb-clone et-pb-advanced-setting-clone">
				<span>%3$s</span>
			</a>
		</script>',
		esc_html__( 'Delete', 'et_builder' ),
		esc_html__( 'Settings', 'et_builder' ),
		esc_html__( 'Clone Module', 'et_builder' )
	);

	// Advanced Settings Modal Buttons Template
	printf(
		'<script type="text/template" id="et-builder-advanced-setting-edit">
			<div class="et-pb-modal-container">
				<a href="#" class="et-pb-modal-close">
					<span>%1$s</span>
				</a>

				<div class="et-pb-modal-bottom-container">
					<a href="#" class="et-pb-modal-save">
						<span>%2$s</span>
					</a>
				</div>
			</div>
		</script>',
		esc_html__( 'Cancel', 'et_builder' ),
		esc_html__( 'Save', 'et_builder' )
	);


	// "Deactivate Builder" Modal Message Template
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-deactivate_builder-text">
			<h3>%1$s</h3>
			<p>%2$s</p>
			<p>%3$s</p>
		</script>',
		esc_html__( 'Disable Builder', 'et_builder' ),
		esc_html__( 'All content created in the Divi Builder will be lost. Previous content will be restored.', 'et_builder' ),
		esc_html__( 'Do you wish to proceed?', 'et_builder' )
	);


	// "Clear Layout" Modal Window Template
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-clear_layout-text">
			<h3>%1$s</h3>
			<p>%2$s</p>
			<p>%3$s</p>
		</script>',
		esc_html__( 'Clear Layout', 'et_builder' ),
		esc_html__( 'All of your current page content will be lost.', 'et_builder' ),
		esc_html__( 'Do you wish to proceed?', 'et_builder' )
	);


	// "Reset Advanced Settings" Modal Template
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-reset_advanced_settings-text">
			<p>%1$s</p>
			<p>%2$s</p>
		</script>',
		esc_html__( 'All advanced module settings in will be lost.', 'et_builder' ),
		esc_html__( 'Do you wish to proceed?', 'et_builder' )
	);


	// "Save Layout" Modal Window Template
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-save_layout">
			<div class="et_pb_prompt_modal">
				<a href="#" class="et_pb_prompt_dont_proceed et-pb-modal-close">
					<span>%1$s</span>
				</a>
				<div class="et_pb_prompt_buttons">
					<br/>
					<input type="submit" class="et_pb_prompt_proceed" value="%2$s" />
				</div>
			</div>
		</script>',
		esc_html__( 'Cancel', 'et_builder' ),
		esc_html__( 'Save', 'et_builder' )
	);


	// "Save Layout" Modal Content Template
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-save_layout-text">
			<h3>%1$s</h3>
			<p>%2$s</p>

			<label>%3$s</label>
			<input type="text" value="" id="et_pb_new_layout_name" class="regular-text" />
		</script>',
		esc_html__( 'Save To Library', 'et_builder' ),
		esc_html__( 'Save your current page to the Divi Library for later use.', 'et_builder' ),
		esc_html__( 'Layout Name:', 'et_builder' )
	);


	// "Save Template" Modal Window Layout
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-save_template">
			<div class="et_pb_prompt_modal et_pb_prompt_modal_save_library">
				<div class="et_pb_prompt_buttons">
					<br/>
					<input type="submit" class="et_pb_prompt_proceed" value="%1$s" />
				</div>
			</div>
		</script>',
		esc_attr__( 'Save And Add To Library', 'et_builder' )
	);


	// "Save Template" Content Layout
	$layout_categories = get_terms( 'layout_category', array( 'hide_empty' => false ) );
	$categories_output = sprintf( '<div class="et-pb-option"><label>%1$s</label>',
		esc_html__( 'Add To Categories:', 'et_builder' )
	);

	if ( is_array( $layout_categories ) && ! empty( $layout_categories ) ) {
		$categories_output .= '<div class="et-pb-option-container layout_cats_container">';
		foreach( $layout_categories as $category ) {
			$categories_output .= sprintf( '<label>%1$s<input type="checkbox" value="%2$s"/></label>',
				esc_html( $category->name ),
				esc_attr( $category->term_id )
			);
		}
		$categories_output .= '</div></div>';
	}

	$categories_output .= sprintf( '
		<div class="et-pb-option">
			<label>%1$s:</label>
			<div class="et-pb-option-container">
				<input type="text" value="" id="et_pb_new_cat_name" class="regular-text" />
			</div>
		</div>',
		esc_html__( 'Create New Category', 'et_builder' )
	);

	$general_checkbox = sprintf(
		'<label>
			%1$s <input type="checkbox" value="general" id="et_pb_template_general" checked />
		</label>',
		esc_html__( 'Include General settings', 'et_builder' )
	);
	$advanced_checkbox = sprintf(
		'<label>
			%1$s <input type="checkbox" value="advanced" id="et_pb_template_advanced" checked />
		</label>',
		esc_html__( 'Include Advanced Design settings', 'et_builder' )
	);
	$css_checkbox = sprintf(
		'<label>
			%1$s <input type="checkbox" value="css" id="et_pb_template_css" checked />
		</label>',
		esc_html__( 'Include Custom CSS', 'et_builder' )
	);

	printf(
		'<script type="text/template" id="et-builder-prompt-modal-save_template-text">
			<div class="et-pb-main-settings">
				<p>%1$s</p>

				<div class="et-pb-option">
					<label>%2$s:</label>

					<div class="et-pb-option-container">
						<input type="text" value="" id="et_pb_new_template_name" class="regular-text" />
					</div>
				</div>

			<%% if ( \'global\' !== is_global && \'global\' !== is_global_child ) { %%>
				<div class="et-pb-option">
					<label>%3$s</label>

					<div class="et-pb-option-container">
						<label>
							%4$s <input type="checkbox" value="" id="et_pb_template_global" />
						</label>
					</div>
				</div>
			<%% } %%>

				%5$s
			</div>
		</script>',
		esc_html__( 'Here you can save the current item and add it to your Divi Library for later use as well.', 'et_builder' ),
		esc_html__( 'Template Name', 'et_builder' ),
		esc_html__( 'Save as Global:', 'et_builder' ),
		esc_html__( 'Make this a global item', 'et_builder' ),
		$categories_output
	);


	// Prompt Modal Window Template
	printf(
		'<script type="text/template" id="et-builder-prompt-modal">
			<div class="et_pb_prompt_modal">
				<a href="#" class="et_pb_prompt_dont_proceed et-pb-modal-close">
					<span>%1$s<span>
				</a>

				<div class="et_pb_prompt_buttons">
					<a href="#" class="et_pb_prompt_proceed">%2$s</a>
				</div>
			</div>
		</script>',
		esc_html__( 'No', 'et_builder' ),
		esc_html__( 'Yes', 'et_builder' )
	);

	// "Open Settings" Modal Window Template
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-open_settings">
			<div class="et_pb_prompt_modal">
				<a href="#" class="et_pb_prompt_dont_proceed et-pb-modal-close">
					<span>%1$s</span>
				</a>
				<div class="et_pb_prompt_buttons">
					<br/>
					<input type="submit" class="et_pb_prompt_proceed" value="%2$s" />
				</div>
			</div>
		</script>',
		esc_html__( 'Cancel', 'et_builder' ),
		esc_html__( 'Save', 'et_builder' )
	);

	// "Open Settings" Modal Content Template
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-open_settings-text">
			<h3>%1$s</h3>
			<div class="et_pb_prompt_fields">
			%2$s
			</div><!-- .et_pb_prompt_fields -->
		</script>',
		esc_html__( 'Divi Builder Settings', 'et_builder' ),
		et_pb_get_builder_settings_fields( ET_Builder_Settings::get_fields() )
	);

	/**
	 * "Turn off Split Testing" Modal Window Template
	 */
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-turn_off_ab_testing">
			<div class="et_pb_prompt_modal">
				<a href="#" class="et_pb_prompt_dont_proceed et-pb-modal-close">
					<span>%1$s</span>
				</a>
				<div class="et_pb_prompt_buttons">
					<br/>
					<input type="submit" class="et_pb_prompt_proceed" value="%2$s" />
				</div>
			</div>
		</script>',
		esc_html__( 'Cancel', 'et_builder' ),
		esc_html__( 'Yes', 'et_builder' )
	);

	// "Turn off Split Testing" Modal Content Template
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-turn_off_ab_testing-text">
			<h3>%1$s</h3>
			<p>%2$s</p>
			<p>%3$s</p>
		</script>',
		esc_html__( 'End Split Test?', 'et_builder' ),
		esc_html__( 'Upon ending your split test, you will be asked to select which subject variation you would like to keep. Remaining subjects will be removed.', 'et_builder' ),
		esc_html__( 'Note: this process cannot be undone.', 'et_builder' )
	);

	/**
	 * Split Testing Alert :: Modal Window Template
	 */
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-ab_testing_alert">
			<div class="et_pb_prompt_modal">
				<div class="et_pb_prompt_buttons">
					<br/>
					<input type="submit" class="et_pb_prompt_proceed" value="%1$s" />
				</div>
			</div>
		</script>',
		esc_html__( 'Ok', 'et_builder' )
	);

	// Split Testing Alert :: Modal Content Template
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-ab_testing_alert-text">
			<%% if ( ! _.isUndefined( et_pb_ab_js_options[id] ) ) { %%>
				<h3><%%= et_pb_ab_js_options[id].title %%></h3>
				<p><%%= et_pb_ab_js_options[id].desc %%></p>
			<%% } else { %%>
				<h3>%1$s</h3>
				<p>%2$s</p>
			<%% } %%>
		</script>',
		esc_html__( 'An Error Occurred', 'et_builder' ),
		esc_html__( 'For some reason, you cannot perform this task.', 'et_builder' )
	);

	/**
	 * Split Testing Alert Yes/No :: Modal Window Template
	 */
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-ab_testing_alert_yes_no">
			<div class="et_pb_prompt_modal">
				<div class="et_pb_prompt_buttons">
					<br/>
					<button class="et_pb_prompt_proceed_alternative et_pb_prompt_cancel">%1$s</button>
					<input type="submit" class="et_pb_prompt_proceed has_alternative has_cancel_alternative" value="%2$s" />
				</div>
			</div>
		</script>',
		esc_html__( 'Cancel', 'et_builder' ),
		esc_html__( 'Proceed', 'et_builder' )
	);

	// Split Testing Alert Yes/No :: Modal Content Template
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-ab_testing_alert_yes_no-text">
			<%% if ( ! _.isUndefined( et_pb_ab_js_options[id] ) ) { %%>
				<h3><%%= et_pb_ab_js_options[id].title %%></h3>
				<p><%%= et_pb_ab_js_options[id].desc %%></p>
			<%% } else { %%>
				<h3>%1$s</h3>
				<p>%2$s</p>
			<%% } %%>
		</script>',
		esc_html__( 'An Error Occurred', 'et_builder' ),
		esc_html__( 'For some reason, you cannot perform this task.', 'et_builder' )
	);

	/**
	 * Splir Testing :: Set global item winner status
	 */
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-set_global_subject_winner">
			<div class="et_pb_prompt_modal">
				<div class="et_pb_prompt_buttons">
					<br/>
					<button class="et_pb_prompt_proceed_alternative">%1$s</button>
					<input type="submit" class="et_pb_prompt_proceed has_alternative" value="%2$s" />
				</div>
			</div>
		</script>',
		esc_html__( 'Save as Global Item', 'et_builder' ),
		esc_html__( 'Save', 'et_builder' )
	);

	// Split Testing :: Set global item winner status template
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-set_global_subject_winner-text">
			<h3>%1$s</h3>
			<p>%2$s</p>
			<ol>
				<li>%3$s</li>
				<li>%4$s</li>
			</ol>
		</script>',
		esc_html__( 'Set Winner Status', 'et_builder' ),
		esc_html__( 'You were using global item as split testing winner. Consequently, you have to choose between:', 'et_builder' ),
		esc_html__( 'Save winner as global item (selected subject will be synced and your global item will be updated in the Divi Library)', 'et_builder' ),
		esc_html__( 'Save winner as non-global item (selected subject will no longer be a global item and your changes will not modify the global item)', 'et_builder' )
	);

	/**
	 * Split Testing :: View Stats Template
	 */
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-view_ab_stats">
			<div class="et_pb_prompt_modal et_pb_ab_view_stats">
				<a href="#" class="et_pb_prompt_dont_proceed et-pb-modal-close">
					<span>%1$s</span>
				</a>
			</div>
		</script>',
		esc_html__( 'Cancel', 'et_builder' )
	);

	$view_stats_tabs = "";

	foreach ( et_pb_ab_get_analysis_types() as $analysis ) {
		$view_stats_tabs .= sprintf(
			'<div class="view-stats-tab tab-%1$s" data-analysis="%1$s">
				<ul class="et-pb-ab-view-stats-time-filter">
					<li><a href="#" data-duration="day">%2$s</a></li>
					<li><a href="#" data-duration="week">%3$s</a></li>
					<li><a href="#" data-duration="month">%4$s</a></li>
					<li><a href="#" data-duration="all">%5$s</a></li>
				</ul><!-- .et-pb-ab-view-stats-time-filter -->

				<ul class="et-pb-ab-view-stats-subjects-filter">
				</ul><!-- .et-pb-ab-view-stats-subjects-filter -->

				<div class="view-stats-main-stats">
					<canvas id="ab-testing-stats-%1$s" class="ab-testing-stats" width="913" height="330"></canvas>
				</div>

				<h2 class="sub-heading">%6$s</h2>
				<div class="view-stats-table-wrapper">
					<table id="view-stats-table-%1$s" class="view-stats-table">
						<thead></thead>
						<tbody></tbody>
						<tfoot></tfoot>
					</table>
				</div><!-- .view-stats-table-wrapper -->
				<div class="view-stats-pie-wrapper">
					<canvas id="ab-testing-stats-pie-%1$s" class="ab-testing-stats-pie" width="200" height="200"></canvas>
					<ul class="ab-testing-stats-pie-legends">
					</ul><!-- .ab-testing-stats-pie-legends -->
				</div><!-- .view-stats-pie-wrapper -->
				<div class="no-stats">
					<span class="icon">
						<object type="image/svg+xml" data="%7$s/images/stats-no-data.svg"></object>
					</span>
					<h2>%8$s</h2>
					<p>%9$s</p>
				</div><!-- .no-stats -->
			</div>',
			esc_attr( $analysis ),
			esc_html__( 'Last 24 Hours', 'et_builder' ),
			esc_html__( 'Last 7 Days', 'et_builder' ),
			esc_html__( 'Last Month', 'et_builder' ),
			esc_html__( 'All Time', 'et_builder' ),
			esc_html__( 'Summary &amp; Data', 'et_builder' ),
			esc_url( ET_BUILDER_URI ),
			esc_html__( 'Statistics are still being collected for this time frame', 'et_builder' ),
			esc_html__( 'Stats will be displayed upon sufficient data collection', 'et_builder' )
		);
	}

	// Split Testing :: View Stats content
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-view_ab_stats-text">
			<h3>%1$s</h3>
			<ul class="et-pb-options-tabs-links">
				<li class="et_pb_options_tab_ab_stat_conversion et-pb-options-tabs-links-active" data-analysis="conversions">
					<a href="#">%6$s</a>
				</li>
				<li class="et_pb_options_tab_ab_stat_clicks" data-analysis="clicks">
					<a href="#">%2$s</a>
				</li>
				<li class="et_pb_options_tab_ab_stat_reads" data-analysis="reads">
					<a href="#">%3$s</a>
				</li>
				<li class="et_pb_options_tab_ab_stat_bounces" data-analysis="bounces">
					<a href="#">%4$s</a>
				</li>
				<li class="et_pb_options_tab_ab_stat_engagements" data-analysis="engagements">
					<a href="#">%5$s</a>
				</li>
				<li class="et_pb_options_tab_ab_stat_shortcode_conversions" data-analysis="shortcode_conversions">
					<a href="#">%13$s</a>
				</li>
				<li class="et_pb_ab_refresh_button">
					<a href="#" class="et-pb-ab-refresh-stats" title="%11$s">
						<span class="icon"></span><span class="label">%12$s</span>
					</a>
				</li>
			</ul><!-- .et-pb-options-tabs-links -->
			<div class="et-pb-ab-view-stats-content has-data">
				%7$s
			</div>
			<div class="et-pb-ab-view-stats-content no-data">
				<span class="icon">
					<object type="image/svg+xml" data="%8$s/images/stats-no-data.svg"></object>
				</span>
				<h2>%9$s</h2>
				<p>%10$s</p>
			</div>
			<div class="et_pb_prompt_buttons">
				<input type="submit" class="et_pb_prompt_proceed" value="%14$s">
			</div>
		</script>',
		esc_html__( 'Split Testing Statistics', 'et_builder' ),
		esc_html__( 'Clicks', 'et_builder' ),
		esc_html__( 'Reads', 'et_builder' ),
		esc_html__( 'Bounces', 'et_builder' ),
		esc_html__( 'Goal Engagement', 'et_builder' ), // 5
		esc_html__( 'Conversions', 'et_builder' ),
		$view_stats_tabs,
		esc_url( ET_BUILDER_URI ),
		esc_html__( 'Statistics are being collected', 'et_builder' ),
		esc_html__( 'Stats will be displayed upon sufficient data collection', 'et_builder' ), // 10
		esc_attr__( 'Refresh Stats', 'et_builder' ),
		esc_html__( 'Refresh Stats', 'et_builder' ),
		esc_html__( 'Shortcode Conversions', 'et_builder' ),
		esc_attr__( 'End Split Test &amp; Pick Winner', 'et_builder' )
	);

	// "Add Specialty Section" Button Template
	printf(
		'<script type="text/template" id="et-builder-add-specialty-section-button">
			<a href="#" class="et-pb-section-add-specialty et-pb-add-specialty-template" data-is_template="true">%1$s</a>
		</script>',
		esc_html__( 'Add Specialty Section', 'et_builder' )
	);


	// Saved Entry Template
	echo
		'<script type="text/template" id="et-builder-saved-entry">
			<a class="et_pb_saved_entry_item"><%= title %></a>
		</script>';


	// Font Icons Template
	printf(
		'<script type="text/template" id="et-builder-google-fonts-options-items">
			%1$s
		</script>',
		et_builder_get_font_options_items()
	);


	// Font Icons Template
	printf(
		'<script type="text/template" id="et-builder-font-icon-list-items">
			%1$s
		</script>',
		et_pb_get_font_icon_list_items()
	);

	// Histories Visualizer Item Template
	printf(
		'<script type="text/template" id="et-builder-histories-visualizer-item-template">
			<li id="et-pb-history-<%%= this.options.get( "timestamp" ) %%>" class="<%%= this.options.get( "current_active_history" ) ? "active" : "undo"  %%>" data-timestamp="<%%= this.options.get( "timestamp" )  %%>">
				<span class="datetime"><%%= this.options.get( "datetime" )  %%></span>
				<span class="verb"> <%%= this.getVerb()  %%></span>
				<span class="noun"> <%%= this.getNoun()  %%></span>
				<%% if ( typeof this.getAddition === "function" && "" !== this.getAddition() ) { %%>
					<span class="addition"> <%%= this.getAddition() %%></span>
				<%% } %%>
			</li>
		</script>'
	);

	// Font Down Icons Template
	printf(
		'<script type="text/template" id="et-builder-font-down-icon-list-items">
			%1$s
		</script>',
		et_pb_get_font_down_icon_list_items()
	);

	printf(
		'<script type="text/template" id="et-builder-preview-icons-template">
			<ul class="et-pb-preview-screensize-switcher">
				<li><a href="#" class="et-pb-preview-mobile" data-width="375"><span class="label">%1$s</span></a></li>
				<li><a href="#" class="et-pb-preview-tablet" data-width="768"><span class="label">%2$s</span></a></li>
				<li><a href="#" class="et-pb-preview-desktop active"><span class="label">%3$s</span></a></li>
			</ul>
		</script>',
		esc_html__( 'Mobile', 'et_builder' ),
		esc_html__( 'Tablet', 'et_builder' ),
		esc_html__( 'Desktop', 'et_builder' )
	);

	printf(
		'<script type="text/template" id="et-builder-options-tabs-links-template">
			<ul class="et-pb-options-tabs-links">
				<%% _.each(this.et_builder_template_options.tabs.options, function(tab, index) { %%>
					<li class="et_pb_options_tab_<%%= tab.slug %%><%%= \'1\' === index ? \' et-pb-options-tabs-links-active\' : \'\' %%>">
						<a href="#"><%%= tab.label %%></a>
					</li>
				<%% }); %%>
			</ul>
		</script>'
	);

	printf(
		'<script type="text/template" id="et-builder-mobile-options-tabs-template">
			<div class="et_pb_mobile_settings_tabs">
				<a href="#" class="et_pb_mobile_settings_tab et_pb_mobile_settings_active_tab" data-settings_tab="desktop">
					%1$s
				</a>
				<a href="#" class="et_pb_mobile_settings_tab" data-settings_tab="tablet">
					%2$s
				</a>
				<a href="#" class="et_pb_mobile_settings_tab" data-settings_tab="phone">
					%3$s
				</a>
			</div>
		</script>',
		esc_html__( 'Desktop', 'et_builder' ),
		esc_html__( 'Tablet', 'et_builder' ),
		esc_html__( 'Smartphone', 'et_builder' )
	);

	printf(
		'<script type="text/template" id="et-builder-padding-inputs-template">
			<label>
				<%%= this.et_builder_template_options.padding.options.label %%>
				<input type="text" class="et_custom_margin et_custom_margin_<%%= this.et_builder_template_options.padding.options.side %%><%%= this.et_builder_template_options.padding.options.class %%><%%= \'need_mobile\' === this.et_builder_template_options.padding.options.need_mobile ? \' et_pb_setting_mobile et_pb_setting_mobile_desktop et_pb_setting_mobile_active\' : \'\' %%>"<%%= \'need_mobile\' === this.et_builder_template_options.padding.options.need_mobile ? \' data-device="desktop"\' : \'\' %%> />
				<%% if ( \'need_mobile\' === this.et_builder_template_options.padding.options.need_mobile ) { %%>
					<input type="text" class="et_custom_margin et_pb_setting_mobile et_pb_setting_mobile_tablet et_custom_margin_<%%= this.et_builder_template_options.padding.options.side %%><%%= this.et_builder_template_options.padding.options.class %%>" data-device="tablet" />
					<input type="text" class="et_custom_margin et_pb_setting_mobile et_pb_setting_mobile_phone et_custom_margin_<%%= this.et_builder_template_options.padding.options.side %%><%%= this.et_builder_template_options.padding.options.class %%>" data-device="phone" />
				<%% } %%>
			</label>
		</script>'
	);

	printf(
		'<script type="text/template" id="et-builder-yes-no-button-template">
			<div class="et_pb_yes_no_button et_pb_off_state">
				<span class="et_pb_value_text et_pb_on_value"><%%= this.et_builder_template_options.yes_no_button.options.on %%></span>
				<span class="et_pb_button_slider"></span>
				<span class="et_pb_value_text et_pb_off_value"><%%= this.et_builder_template_options.yes_no_button.options.off %%></span>
			</div>
		</script>'
	);

	printf(
		'<script type="text/template" id="et-builder-font-buttons-option-template">
			<%% _.each(this.et_builder_template_options.font_buttons.options, function(font_button) { %%>
				<div class="et_builder_<%%= font_button %%>_font et_builder_font_style mce-widget mce-btn">
					<button type="button">
						<i class="mce-ico mce-i-<%%= font_button %%>"></i>
					</button>
				</div>
			<%% }); %%>
		</script>'
	);

	printf(
		'<script type="text/template" id="et-builder-failure-notice-template">
			%1$s
		</script>',
		et_builder_get_failure_notification_modal()
	);

	printf(
		'<script type="text/template" id="et-builder-cache-notice-template">
			%1$s
		</script>',
		et_builder_get_cache_notification_modal()
	);

	// Help Template
	printf(
		'<script type="text/template" id="et-builder-help-template">
			<h3 class="et-pb-settings-heading">%1$s</h3>

			<ul class="et-pb-options-tabs-links et-pb-help-switcher">
				<li class="et-pb-new-module et-pb-options-tabs-links-active" data-open_tab="et-pb-shortcuts-tab">
					<a href="#">%2$s</a>
				</li>
			</ul>

			<div class="et-pb-main-settings et-pb-main-settings-full et-pb-shortcuts-tab active-container"></div>
		</script>',
		esc_html__( 'Divi Builder Helper', 'et_builder' ),
		esc_html__( 'Shortcuts', 'et_builder' )
	);


	do_action( 'et_pb_after_page_builder' );
}

/**
 * Returns builder settings markup
 *
 * @param array   builder settings' configuration
 * @return string builder settings' markup
 */
function et_pb_get_builder_settings_fields( $options ) {
	$outputs = '';
	$defaults = et_pb_get_builder_settings_configuration_default();

	foreach ( $options as $option ) {
		$option           = wp_parse_args( $option, $defaults );
		$type             = $option['type'];
		$field_list_class = $type;
		$affecting        = ! empty( $option['affects'] ) ? implode( '|', $option['affects'] ) : '';

		if ( $option['depends_show_if'] ) {
			$field_list_class .= ' et-pb-display-conditionally';
		}

		if ( isset( $option['class'] ) ) {
			$field_list_class .= ' ' . $option['class'];
		}

		$outputs .= sprintf(
			'<div class="et_pb_prompt_field_list et-pb-option-container %1$s" data-id="%2$s" data-type="%3$s" data-autoload="%4$s" data-affects="%5$s" data-visibility-dependency="%6$s">',
			esc_attr( $field_list_class ),
			esc_attr( $option['id'] ),
			esc_attr( $type ),
			esc_attr( $option['autoload'] ),
			esc_attr( $affecting ),
			esc_attr( $option['depends_show_if'] )
		);

		switch ( $option['type'] ) {
			case 'yes_no_button' :
				$outputs .= sprintf('<label>%2$s</label>
						<div class="et_pb_prompt_field">
							<div class="et_pb_yes_no_button_wrapper ">
								<div class="et_pb_yes_no_button et_pb_off_state">
									<span class="et_pb_value_text et_pb_on_value">%3$s</span>
									<span class="et_pb_button_slider"></span>
									<span class="et_pb_value_text et_pb_off_value">%4$s</span>
								</div>

								<select name="%1$s" id="%1$s" class="et-pb-main-setting regular-text">
									<option value="off">%5$s</option>
									<option value="on">%6$s</option>
								</select>
							</div><span class="et-pb-reset-setting"></span>
						</div>',
					esc_attr( $option['id'] ),
					esc_html( $option['label'] ),
					isset( $option['values'] ) ? esc_html( $option['values']['yes'] ) : esc_html__( 'Yes', 'et_builder' ),
					isset( $option['values'] ) ? esc_html( $option['values']['no'] ) : esc_html__( 'No', 'et_builder' ),
					esc_html__( 'Off', 'et_builder' ),
					esc_html__( 'On', 'et_builder' )
				);
				break;

			case 'textarea' :
				$outputs .= sprintf( '<label for="%1$s">%2$s</label>
						<div class="et_pb_prompt_field">
							<textarea id="%1$s" name="%1$s"%3$s></textarea>
						</div>',
					esc_attr( $option['id'] ),
					esc_html( $option['label'] ),
					isset( $option['readonly'] ) && 'readonly' === $option['readonly'] ? ' readonly' : ''
				);
				break;

			case 'colorpalette' :
				$outputs .= sprintf( '<label>%1$s</label><div class="et_pb_prompt_field">', esc_html( $option['label'] ) );

				$outputs .= '<div class="et_pb_colorpalette_overview">';

					for ($colorpalette_index = 1; $colorpalette_index < 9; $colorpalette_index++ ) {
						$outputs     .= sprintf( '<span class="colorpalette-item colorpalette-item-%1$s" data-index="%1$s"></span>', esc_attr( $colorpalette_index ) );
					}

				$outputs .= '</div>';

				for ($colorpicker_index = 1; $colorpicker_index < 9; $colorpicker_index++ ) {
					$outputs .= sprintf('<div class="colorpalette-colorpicker" data-index="%2$s">
							<input id="%1$s-%2$s" name="%1$s-%2$s" data-index="%2$s" type="text" class="input-colorpalette-colorpicker" data-alpha="true" />
						</div>',
						esc_attr( $option['id'] ),
						esc_attr( $colorpicker_index )
					);
				}

				$outputs .= '</div>';

				break;

			case 'color-alpha' :
				$outputs .= sprintf( '<label for="%1$s">%2$s</label>
						<div class="et_pb_prompt_field">
							<input id="%1$s" name="%1$s" type="text" class="input-colorpicker" data-alpha="true" data-default-color="%3$s" />
						</div>',
					esc_attr( $option['id'] ),
					esc_html( $option['label'] ),
					esc_attr( $option['default'] )
				);
				break;

			case 'range' :
				$outputs .= sprintf( '<label for="%1$s">%2$s</label>
						<div class="et_pb_prompt_field">
							<input id="%1$s" name="%1$s" type="range" class="range" step="%3$s" min="%4$s" max="%5$s" />
						</div>',
					esc_attr( $option['id'] ),
					esc_html( $option['label'] ),
					esc_attr( $option['range_settings']['step'] ),
					esc_attr( $option['range_settings']['min'] ),
					esc_attr( $option['range_settings']['max'] )
				);
				break;

			case 'select' :
				$options = '';

				foreach( $option['options'] as $value => $text ) {
					$options .= sprintf( '<option value="%1$s">%2$s</option>',
						esc_attr( $value ),
						esc_html( $text )
					);
				}

				$outputs .= sprintf( '<label for="%1$s">%2$s</label>
						<div class="et_pb_prompt_field">
							<select id="%1$s" name="%1$s">
								%3$s
							</select>
						</div>',
					esc_attr( $option['id'] ),
					esc_html( $option['label'] ),
					$options
				);
				break;
		}

		$outputs .= sprintf( '</div><!-- .et_pb_prompt_field_list.et-pb-option-container.%1$s -->', esc_attr( $option['type'] ) );
	}

	return $outputs;
}

/**
 * Prints hidden inputs for passing settings data to database
 *
 * @return void
 */
function et_pb_builder_settings_hidden_inputs( $post_id ) {
	$settings = ET_Builder_Settings::get_fields();
	$defaults = et_pb_get_builder_settings_configuration_default();

	foreach ( $settings as $setting ) {
		$setting = wp_parse_args( $setting, $defaults );

		if ( ! $setting['autoload'] ) {
			continue;
		}

		$id            = '_' . $setting['id'];
		$meta_key      = isset( $setting['meta_key'] ) ? $setting['meta_key'] : $id;
		$value         = get_post_meta( $post_id, $meta_key, true );

		if ( ( ! $value || '' === $value ) && $setting['default'] ) {
			$value = $setting['default'];
		}

		printf(
			'<input type="hidden" id="%1$s" name="%1$s" value="%2$s" />',
			esc_attr( $id ),
			esc_attr( $value )
		);
	}
}

/**
 * Prints hidden inputs for passing global modules data to database
 *
 * @return void
 */
function et_pb_builder_global_library_inputs( $post_id ) {
	global $typenow;

	if ( 'et_pb_layout' !== $typenow ) {
		return;
	}

	$template_scope = wp_get_object_terms( get_the_ID(), 'scope' );
	$template_type = wp_get_object_terms( get_the_ID(), 'layout_type' );
	$is_global_template = ! empty( $template_scope[0] ) ? $template_scope[0]->slug : 'regular';
	$template_type_slug = ! empty( $template_type[0] ) ? $template_type[0]->slug : '';

	if ( 'global' !== $is_global_template || 'module' !== $template_type_slug ) {
		return;
	}

	$excluded_global_options = get_post_meta( $post_id, '_et_pb_excluded_global_options' );

	printf(
		'<input type="hidden" id="et_pb_unsynced_global_attrs" name="et_pb_unsynced_global_attrs" value="%1$s" />',
		isset( $excluded_global_options[0] ) ? esc_attr( $excluded_global_options[0] ) : json_encode( array() )
	);
}

/**
 * Returns array of default builder settings configuration item
 *
 * @return array
 */
function et_pb_get_builder_settings_configuration_default() {
	return array(
		'id'              => '',
		'type'            => '',
		'label'           => '',
		'min'             => '',
		'max'             => '',
		'step'            => '',
		'autoload'        => true,
		'default'         => false,
		'affects'         => array(),
		'depends_show_if' => false,
	);
}

function et_builder_update_settings( $settings, $post_id = 'global' ) {
	$is_global = 'global' === $post_id;
	$is_BB     = null === $settings;
	$settings  = $is_BB ? $_POST : $settings;
	$fields    = $is_global ? ET_Builder_Settings::get_fields( 'builder' ) : ET_Builder_Settings::get_fields();

	foreach ( (array) $settings as $setting_key => $setting_value ) {
		$setting_key = $is_BB ? substr( $setting_key, 1 ) : $setting_key;

		// Verify setting key
		if ( ! isset( $fields[ $setting_key ] ) || ! isset( $fields[ $setting_key ]['type'] ) ) {
			continue;
		}

		// Auto-formatting subjects' value format
		if ( 'et_pb_ab_subjects' === $setting_key && is_array( $setting_value ) ) {
			$setting_value = implode(',', $setting_value );
		}

		// TODO Possibly move sanitization.php to builder dir
		// Sanitize value
		switch ( $fields[ $setting_key ]['type'] ) {
			case 'colorpalette':
				$palette_colors = explode('|', $setting_value);
				$setting_value = implode('|', array_map('et_sanitize_alpha_color', $palette_colors ) );
				break;

			case 'range':
				$setting_value = absint( $setting_value );
				$range_min     = isset( $fields[ $setting_key ]['range_settings'] ) && isset( $fields[ $setting_key ]['range_settings']['min'] ) ?
					absint( $fields[ $setting_key ]['range_settings']['min'] ) : -1;
				$range_max     = isset( $fields[ $setting_key ]['range_settings'] ) && isset( $fields[ $setting_key ]['range_settings']['max'] ) ?
					absint( $fields[ $setting_key ]['range_settings']['max'] ) : -1;

				if ( $setting_value < $range_min || $range_max < $setting_value ) {
					continue;
				}

				break;

			case 'color-alpha':
				$setting_value = et_sanitize_alpha_color( $setting_value );
				break;

			case 'textarea':
				$setting_value = sanitize_textarea_field( $setting_value );
				break;

			default:
				$setting_value = sanitize_text_field( $setting_value );
				break;
		}

		// check whether or not the defined value === default value
		$is_default = isset( $fields[ $setting_key ]['default'] ) && $setting_value === $fields[ $setting_key ]['default'];

		// Auto-formatting split test status' meta key
		if ( 'et_pb_enable_ab_testing' === $setting_key ) {
			$setting_key = 'et_pb_use_ab_testing';
		}

		/**
		 * Fires before updating a builder setting in the database.
		 *
		 * @param string     $setting_key   The option name/id.
		 * @param string     $setting_value The new option value.
		 * @param string|int $post_id       The post id or 'global' for global settings.
		 */
		do_action( 'et_builder_settings_update_option', $setting_key, $setting_value, $post_id );

		// Prepare key
		$meta_key = isset( $fields[ $setting_key ]['meta_key'] ) ? $fields[ $setting_key ]['meta_key'] : "_{$setting_key}";

		// remove if value is default
		if ( $is_default ) {
			$is_global ? et_delete_option( $setting_key ) : delete_post_meta( $post_id, $meta_key );
		} else {
			// Update
			$is_global ? et_update_option( $setting_key, $setting_value ) : update_post_meta( $post_id, $meta_key, $setting_value );
		}

		// Removing autosave
		delete_post_meta( $post_id, "{$meta_key}_draft" );
	}

	// Removing builder settings autosave
	$current_user_id = get_current_user_id();

	delete_post_meta( $post_id, "_et_builder_settings_autosave_{$current_user_id}");
}

/**
 * Returns array of default color pallete
 *
 * @return array default color palette
 */
function et_pb_get_default_color_palette( $post_id = 0 ) {
	$default_palette = array(
		'#000000',
		'#FFFFFF',
		'#E02B20',
		'#E09900',
		'#EDF000',
		'#7CDA24',
		'#0C71C3',
		'#8300E9',
	);

	$saved_global_palette = et_get_option( 'divi_color_palette', false );

	$palette = $saved_global_palette && '' !== str_replace( '|', '', $saved_global_palette ) ? explode( '|', $saved_global_palette ) : $default_palette;

	return apply_filters( 'et_pb_get_default_color_palette', $palette, $post_id );
}

/**
 * Modify builder editor's TinyMCE configuration
 *
 * @return array
 */
function et_pb_content_new_mce_config( $mceInit, $editor_id ) {
	if ( 'et_pb_content_new' === $editor_id && isset( $mceInit['toolbar1'] ) ) {
		// Get toolbar as array
		$toolbar1 = explode(',', $mceInit['toolbar1'] );

		// Look for read more (wp_more)'s array' key
		$wp_more_key = array_search( 'wp_more', $toolbar1 );

		if ( $wp_more_key ) {
			unset( $toolbar1[ $wp_more_key ] );
		}

		// Update toolbar1 configuration
		$mceInit['toolbar1'] = implode(',', $toolbar1 );
	}

	return $mceInit;
}
add_filter( 'tiny_mce_before_init', 'et_pb_content_new_mce_config', 10, 2 );

/**
 * Get post format with filterable output
 *
 * @todo once WordPress provide filter for get_post_format() output, this function can be retired
 * @see get_post_format()
 *
 * @return mixed string|bool string of post format or false for default
 */
function et_pb_post_format() {
	return apply_filters( 'et_pb_post_format', get_post_format(), get_the_ID() );
}

/**
 * Return post format into false when using pagebuilder
 *
 * @return mixed string|bool string of post format or false for default
 */
function et_pb_post_format_in_pagebuilder( $post_format, $post_id ) {

	if ( et_pb_is_pagebuilder_used( $post_id ) ) {
		return false;
	}

	return $post_format;
}
add_filter( 'et_pb_post_format', 'et_pb_post_format_in_pagebuilder', 10, 2 );

function et_aweber_authorization_option() {
	wp_enqueue_script( 'divi-advanced-options', ET_BUILDER_URI . '/scripts/advanced_options.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
	wp_localize_script( 'divi-advanced-options', 'et_advanced_options', array(
		'et_admin_load_nonce'      => wp_create_nonce( 'et_admin_load_nonce' ),
		'aweber_connecting'        => esc_html__( 'Connecting...', 'et_builder' ),
		'aweber_failed'            => esc_html__( 'Connection failed', 'et_builder' ),
		'aweber_remove_connection' => esc_html__( 'Removing connection...', 'et_builder' ),
		'aweber_done'              => esc_html__( 'Done', 'et_builder' ),
	) );
	wp_enqueue_style( 'divi-advanced-options', ET_BUILDER_URI . '/styles/advanced_options.css', array(), ET_BUILDER_VERSION );

	$app_id = 'b17f3351';

	$aweber_auth_endpoint = 'https://auth.aweber.com/1.0/oauth/authorize_app/' . $app_id;

	$hide_style = ' style="display: none;"';

	$aweber_connection_established = et_get_option( 'divi_aweber_consumer_key', false ) && et_get_option( 'divi_aweber_consumer_secret', false ) && et_get_option( 'divi_aweber_access_key', false ) && et_get_option( 'divi_aweber_access_secret', false );

	$output = sprintf(
		'<div id="et_aweber_connection">
			<ul id="et_aweber_authorization"%4$s>
				<li>%1$s</li>
				<li>
					<p>%2$s</p>
					<p><textarea id="et_aweber_authentication_code" name="et_aweber_authentication_code"></textarea></p>

					<p><button class="et_make_connection button button-primary button-large">%3$s</button></p>
				</li>
			</ul>

			<div id="et_aweber_remove_connection"%5$s>
				<p>%6$s</p>
				<p><button class="et_remove_connection button button-primary button-large">%7$s</button></p>
			</div>
		</div>',
		sprintf( '%1$s <a href="%2$s" target="_blank">%3$s</a>',
			esc_html__( 'Step 1:', 'et_builder' ),
			esc_url( $aweber_auth_endpoint ),
			esc_html__( 'Generate authorization code', 'et_builder' )
		),
		esc_html__( 'Step 2: Paste in the authorization code and click "Make a connection" button: ', 'et_builder' ),
		esc_html__( 'Make a connection', 'et_builder' ),
		( $aweber_connection_established ? $hide_style : ''  ),
		( ! $aweber_connection_established ? $hide_style : ''  ),
		esc_html__( 'Aweber is set up properly. You can remove connection here if you wish.', 'et_builder' ),
		esc_html__( 'Remove the connection', 'et_builder' )
	);

	echo $output;
}

if ( ! function_exists( 'et_pb_get_audio_player' ) ) :
function et_pb_get_audio_player() {
	$output = sprintf(
		'<div class="et_audio_container">
			%1$s
		</div> <!-- .et_audio_container -->',
		do_shortcode( '[audio]' )
	);

	return $output;
}
endif;

/*
 * Displays post audio, quote and link post formats content
 */
if ( ! function_exists( 'et_divi_post_format_content' ) ) :
function et_divi_post_format_content() {
	$post_format = et_pb_post_format();

	$text_color_class = et_divi_get_post_text_color();

	$inline_style = et_divi_get_post_bg_inline_style();

	switch ( $post_format ) {
		case 'audio' :
			printf(
				'<div class="et_audio_content%4$s"%5$s>
					<h2><a href="%3$s">%1$s</a></h2>
					%2$s
				</div> <!-- .et_audio_content -->',
				esc_html( get_the_title() ),
				et_pb_get_audio_player(),
				esc_url( get_permalink() ),
				esc_attr( $text_color_class ),
				$inline_style
			);

			break;
		case 'quote' :
			printf(
				'<div class="et_quote_content%4$s"%5$s>
					%1$s
					<a href="%2$s" class="et_quote_main_link">%3$s</a>
				</div> <!-- .et_quote_content -->',
				et_get_blockquote_in_content(),
				esc_url( get_permalink() ),
				esc_html__( 'Read more', 'et_builder' ),
				esc_attr( $text_color_class ),
				$inline_style
			);

			break;
		case 'link' :
			printf(
				'<div class="et_link_content%5$s"%6$s>
					<h2><a href="%2$s">%1$s</a></h2>
					<a href="%3$s" class="et_link_main_url">%4$s</a>
				</div> <!-- .et_link_content -->',
				esc_html( get_the_title() ),
				esc_url( get_permalink() ),
				esc_url( et_get_link_url() ),
				esc_html( et_get_link_url() ),
				esc_attr( $text_color_class ),
				$inline_style
			);

			break;
	}
}
endif;

/**
 * Extract and return the first blockquote from content.
 */
if ( ! function_exists( 'et_get_blockquote_in_content' ) ) :
function et_get_blockquote_in_content() {
	global $more;
	$more_default = $more;
	$more = 1;

	remove_filter( 'the_content', 'et_remove_blockquote_from_content' );

	$content = apply_filters( 'the_content', get_the_content() );

	add_filter( 'the_content', 'et_remove_blockquote_from_content' );

	$more = $more_default;

	if ( preg_match( '/<blockquote>(.+?)<\/blockquote>/is', $content, $matches ) ) {
		return $matches[0];
	} else {
		return false;
	}
}
endif;

if ( ! function_exists( 'et_get_link_url' ) ) :
function et_get_link_url() {
	if ( '' !== ( $link_url = get_post_meta( get_the_ID(), '_format_link_url', true ) ) ) {
		return $link_url;
	}

	$content = get_the_content();
	$has_url = get_url_in_content( $content );

	return ( $has_url ) ? $has_url : apply_filters( 'the_permalink', get_permalink() );
}
endif;

if ( ! function_exists( 'et_get_first_video' ) ) :
function et_get_first_video() {
	$first_url    = '';
	$first_video  = '';
	$video_width  = (int) apply_filters( 'et_blog_video_width', 1080 );
	$video_height = (int) apply_filters( 'et_blog_video_height', 630 );

	$i = 0;

	preg_match_all( '|^\s*https?://[^\s"]+\s*$|im', get_the_content(), $urls );

	foreach ( $urls[0] as $url ) {
		$i++;

		if ( 1 === $i ) {
			$first_url = trim( $url );
		}

		$oembed = wp_oembed_get( esc_url( $url ) );

		if ( !$oembed ) {
			continue;
		}

		$first_video = $oembed;
		$first_video = preg_replace( '/<embed /', '<embed wmode="transparent" ', $first_video );
		$first_video = preg_replace( '/<\/object>/','<param name="wmode" value="transparent" /></object>', $first_video );

		break;
	}

	if ( '' === $first_video ) {
		$content = get_the_content();

		if ( ! has_shortcode( $content, 'video' ) && ! empty( $first_url ) ) {
			$video_shortcode = sprintf( '[video src="%1$s" /]', esc_attr( $first_url ) );
			$content = str_replace( $first_url, $video_shortcode, $content );
		}

		if ( has_shortcode( $content, 'video' ) ) {
			$regex = get_shortcode_regex();
			preg_match( "/{$regex}/s", $content, $match );

			$first_video = preg_replace( "/width=\"[0-9]*\"/", "width=\"{$video_width}\"", $match[0] );
			$first_video = preg_replace( "/height=\"[0-9]*\"/", "height=\"{$video_height}\"", $first_video );

			add_filter( 'the_content', 'et_delete_post_video' );

			$first_video = do_shortcode( et_pb_fix_shortcodes( $first_video ) );
		}
	}

	return ( '' !== $first_video ) ? $first_video : false;
}
endif;

if ( ! function_exists( 'et_delete_post_video' ) ) :
/*
 * Removes the first video shortcode from content on single pages since it is displayed
 * at the top of the page. This will also remove the video shortcode url from archive pages content
 */
function et_delete_post_video( $content ) {
	if ( has_post_format( 'video' ) ) :
		$regex = get_shortcode_regex();
		preg_match_all( "/{$regex}/s", $content, $matches );

		// $matches[2] holds an array of shortcodes names in the post
		foreach ( $matches[2] as $key => $shortcode_match ) {
			if ( 'video' === $shortcode_match ) {
				$content = str_replace( $matches[0][$key], '', $content );
				if ( is_single() && is_main_query() ) {
					break;
				}
			}
		}
	endif;

	return $content;
}
endif;

if ( ! function_exists( 'et_delete_post_first_video' ) ) :
function et_delete_post_first_video( $content ) {
	if ( 'video' === et_pb_post_format() && false !== ( $first_video = et_get_first_video() ) ) {
		preg_match_all( '|^\s*https?:\/\/[^\s"]+\s*|im', $content, $urls );

		if ( ! empty( $urls[0] ) ) {
			$content = str_replace( $urls[0], '', $content );
		}
	}

	return $content;
}
endif;

/**
 * Fix JetPack post excerpt shortcode issue.
 */
function et_jetpack_post_excerpt( $results ) {
	foreach ( $results as $key => $post ) {
		if ( isset( $post['excerpt'] ) ) {
			// Remove ET shortcodes from JetPack excerpt.
			$results[$key]['excerpt'] = preg_replace( '#\[et_pb(.*)\]#', '', $post['excerpt'] );
		}
	}
	return $results;
}
add_filter( 'jetpack_relatedposts_returned_results', 'et_jetpack_post_excerpt' );

/**
 * Adds a Divi gallery type when the Jetpack plugin is enabled
 */
function et_jetpack_gallery_type( $types ) {
	$types['divi'] = 'Divi';
	return $types;
}
add_filter( 'jetpack_gallery_types', 'et_jetpack_gallery_type' );

if ( ! function_exists( 'et_get_gallery_attachments' ) ) :
/**
 * Fetch the gallery attachments
 */
function et_get_gallery_attachments( $attr ) {
	// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( ! $attr['orderby'] ) {
			unset( $attr['orderby'] );
		}
	}
	$html5 = current_theme_supports( 'html5', 'gallery' );
	$atts = shortcode_atts( array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => get_the_ID() ? get_the_ID() : 0,
		'itemtag'    => $html5 ? 'figure'     : 'dl',
		'icontag'    => $html5 ? 'div'        : 'dt',
		'captiontag' => $html5 ? 'figcaption' : 'dd',
		'columns'    => 3,
		'size'       => 'thumbnail',
		'include'    => '',
		'exclude'    => '',
		'link'       => '',
	), $attr, 'gallery' );

	$id = intval( $atts['id'] );
	if ( 'RAND' == $atts['order'] ) {
		$atts['orderby'] = 'none';
	}
	if ( ! empty( $atts['include'] ) ) {
		$_attachments = get_posts( array(
			'include'        => $atts['include'],
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'order'          => $atts['order'],
			'orderby'        => $atts['orderby'],
		) );

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[ $val->ID ] = $_attachments[ $key ];
		}
	} elseif ( ! empty( $atts['exclude'] ) ) {
		$attachments = get_children( array(
			'post_parent'    => $id,
			'exclude'        => $atts['exclude'],
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'order'          => $atts['order'],
			'orderby'        => $atts['orderby'],
		) );
	} else {
		$attachments = get_children( array(
			'post_parent'    => $id,
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'order'          => $atts['order'],
			'orderby'        => $atts['orderby'],
		) );
	}

	return $attachments;
}
endif;

/**
 * Generate the HTML for custom gallery layouts
 */
function et_gallery_layout( $val, $attr ) {
	// check to see if the gallery output is already rewritten
	if ( ! empty( $val ) ) {
		return $val;
	}

	if ( et_is_builder_plugin_active() ) {
		return $val;
	}

	if ( ! apply_filters( 'et_gallery_layout_enable', false ) ) {
		return $val;
	}

	$output = '';

	if ( ! is_singular() && ! et_pb_is_pagebuilder_used( get_the_ID() ) && ! is_et_pb_preview() ) {
		$attachments = et_get_gallery_attachments( $attr );
		$gallery_output = '';
		foreach ( $attachments as $attachment ) {
			$attachment_image = wp_get_attachment_url( $attachment->ID, 'et-pb-post-main-image-fullwidth' );
			$gallery_output .= sprintf(
				'<div class="et_pb_slide" style="background: url(%1$s);"></div>',
				esc_attr( $attachment_image )
			);
		}
		$output = sprintf(
			'<div class="et_pb_slider et_pb_slider_fullwidth_off et_pb_gallery_post_type">
				<div class="et_pb_slides">
					%1$s
				</div>
			</div>',
			$gallery_output
		);

	} else {
		if ( ! isset( $attr['type'] ) || ! in_array( $attr['type'], array( 'rectangular', 'square', 'circle', 'rectangle' ) ) ) {
			$attachments = et_get_gallery_attachments( $attr );
			$gallery_output = '';
			foreach ( $attachments as $attachment ) {
				$gallery_output .= sprintf(
					'<li class="et_gallery_item et_pb_gallery_image">
						<a href="%1$s" title="%3$s">
							<span class="et_portfolio_image">
								%2$s
								<span class="et_overlay"></span>
							</span>
						</a>
						%4$s
					</li>',
					esc_url( wp_get_attachment_url( $attachment->ID, 'full' ) ),
					wp_get_attachment_image( $attachment->ID, 'et-pb-portfolio-image' ),
					esc_attr( $attachment->post_title ),
					! empty( $attachment->post_excerpt )
						? sprintf( '<p class="et_pb_gallery_caption">%1$s</p>', esc_html( $attachment->post_excerpt ) )
						: ''
				);
			}
			$output = sprintf(
				'<ul class="et_post_gallery clearfix">
					%1$s
				</ul>',
				$gallery_output
			);
		}
	}
	return $output;
}
add_filter( 'post_gallery', 'et_gallery_layout', 1000, 2 );

if ( ! function_exists( 'et_pb_gallery_images' ) ) :
function et_pb_gallery_images( $force_gallery_layout = '' ) {
	if ( 'slider' === $force_gallery_layout ) {
		$attachments = get_post_gallery( get_the_ID(), false );
		$gallery_output = '';
		$output = '';
		$images_array = ! empty( $attachments['ids'] ) ? explode( ',', $attachments['ids'] ) : array();

		if ( empty ( $images_array ) ) {
			return $output;
		}

		foreach ( $images_array as $attachment ) {
			$image_src = wp_get_attachment_url( $attachment, 'et-pb-post-main-image-fullwidth' );
			$gallery_output .= sprintf(
				'<div class="et_pb_slide" style="background: url(%1$s);"></div>',
				esc_url( $image_src )
			);
		}
		printf(
			'<div class="et_pb_slider et_pb_slider_fullwidth_off et_pb_gallery_post_type">
				<div class="et_pb_slides">
					%1$s
				</div>
			</div>',
			$gallery_output
		);
	} else {
		add_filter( 'et_gallery_layout_enable', 'et_gallery_layout_turn_on' );
		printf( do_shortcode( '%1$s' ), get_post_gallery() );
		remove_filter( 'et_gallery_layout_enable', 'et_gallery_layout_turn_on' );
	}
}
endif;

/**
 * Used to always use divi gallery on et_pb_gallery_images
 */
function et_gallery_layout_turn_on() {
	return true;
}

/*
 * Remove Elegant Builder plugin filter, that activates visual mode on each page load in WP-Admin
 */
function et_pb_remove_lb_plugin_force_editor_mode() {
	remove_filter( 'wp_default_editor', 'et_force_tmce_editor' );
}
add_action( 'admin_init', 'et_pb_remove_lb_plugin_force_editor_mode' );

/**
 *
 * Generates array of all Role options
 *
 */
function et_pb_all_role_options() {
	// get all the modules and build array of capabilities for them
	$all_modules_array = ET_Builder_Element::get_modules_array();
	$module_capabilies = array();

	foreach ( $all_modules_array as $module => $module_details ) {
		if ( ! in_array( $module_details['label'], array( 'et_pb_section', 'et_pb_row', 'et_pb_row_inner', 'et_pb_column' ) ) ) {
			$module_capabilies[ $module_details['label'] ] = array(
				'name'    => sanitize_text_field( $module_details['title'] ),
				'default' => 'on',
			);
		}
	}

	// we need to display some options only when theme activated
	$theme_only_options = ! et_is_builder_plugin_active()
		? array(
			'theme_customizer' => array(
				'name'           => esc_html__( 'Theme Customizer', 'et_builder' ),
				'default'        => 'on',
				'applicability'  => array( 'administrator' ),
			),
			'module_customizer' => array(
				'name'           => esc_html__( 'Module Customizer', 'et_builder' ),
				'default'        => 'on',
				'applicability'  => array( 'administrator' ),
			),
			'page_options' => array(
				'name'    => esc_html__( 'Page Options', 'et_builder' ),
				'default' => 'on',
			),
		)
		: array();

	$all_role_options = array(
		'general_capabilities' => array(
			'section_title' => '',
			'options'       => array(
				'theme_options' => array(
					'name'           => et_is_builder_plugin_active() ? esc_html__( 'Plugin Options', 'et_builder' ) : esc_html__( 'Theme Options', 'et_builder' ),
					'default'        => 'on',
					'applicability'  => array( 'administrator' ),
				),
				'divi_library' => array(
					'name'    => esc_html__( 'Divi Library', 'et_builder' ),
					'default' => 'on',
				),
				'ab_testing' => array(
					'name'    => esc_html__( 'Split Testing', 'et_builder' ),
					'default' => 'on',
				),
			),
		),
		'builder_capabilities' => array(
			'section_title' => esc_html__( 'Builder Interface', 'et_builder'),
			'options'       => array(
				'add_module' => array(
					'name'    => esc_html__( 'Add/Delete Item', 'et_builder' ),
					'default' => 'on',
				),
				'edit_module' => array(
					'name'    => esc_html__( 'Edit Item', 'et_builder' ),
					'default' => 'on',
				),
				'move_module' => array(
					'name'    => esc_html__( 'Move Item', 'et_builder' ),
					'default' => 'on',
				),
				'disable_module' => array(
					'name'    => esc_html__( 'Disable Item', 'et_builder' ),
					'default' => 'on',
				),
				'lock_module' => array(
					'name'    => esc_html__( 'Lock Item', 'et_builder' ),
					'default' => 'on',
				),
				'divi_builder_control' => array(
					'name'    => esc_html__( 'Toggle Divi Builder', 'et_builder' ),
					'default' => 'on',
				),
				'load_layout' => array(
					'name'    => esc_html__( 'Load Layout', 'et_builder' ),
					'default' => 'on',
				),
				'use_visual_builder' => array(
					'name'    => esc_html__( 'Use Visual Builder', 'et_builder' ),
					'default' => 'on',
				),
			),
		),
		'library_capabilities' => array(
			'section_title' => esc_html__( 'Library Settings', 'et_builder' ),
			'options'       => array(
				'save_library' => array(
					'name'    => esc_html__( 'Save To Library', 'et_builder' ),
					'default' => 'on',
				),
				'add_library' => array(
					'name'    => esc_html__( 'Add From Library', 'et_builder' ),
					'default' => 'on',
				),
				'edit_global_library' => array(
					'name'    => esc_html__( 'Edit Global Items', 'et_builder' ),
					'default' => 'on',
				),
			),
		),
		'module_tabs' => array(
			'section_title' => esc_html__( 'Settings Tabs', 'et_builder' ),
			'options'       => array(
				'general_settings' => array(
					'name'    => esc_html__( 'Content Settings', 'et_builder' ),
					'default' => 'on',
				),
				'advanced_settings' => array(
					'name'    => esc_html__( 'Design Settings', 'et_builder' ),
					'default' => 'on',
				),
				'custom_css_settings' => array(
					'name'    => esc_html__( 'Advanced Settings', 'et_builder' ),
					'default' => 'on',
				),
			),
		),
		'general_module_capabilities' => array(
			'section_title' => esc_html__( 'Settings Types', 'et_builder' ),
			'options'       => array(
				'edit_colors' => array(
					'name'    => esc_html__( 'Edit Colors', 'et_builder' ),
					'default' => 'on',
				),
				'edit_content' => array(
					'name'    => esc_html__( 'Edit Content', 'et_builder' ),
					'default' => 'on',
				),
				'edit_fonts' => array(
					'name'    => esc_html__( 'Edit Fonts', 'et_builder' ),
					'default' => 'on',
				),
				'edit_buttons' => array(
					'name'    => esc_html__( 'Edit Buttons', 'et_builder' ),
					'default' => 'on',
				),
				'edit_layout' => array(
					'name'    => esc_html__( 'Edit Layout', 'et_builder' ),
					'default' => 'on',
				),
				'edit_configuration' => array(
					'name'    => esc_html__( 'Edit Configuration', 'et_builder' ),
					'default' => 'on',
				),
			),
		),
		'module_capabilies' => array(
			'section_title' => esc_html__( 'Module Use', 'et_builder' ),
			'options'       => $module_capabilies,
		),
	);

	$all_role_options['general_capabilities']['options'] = array_merge( $all_role_options['general_capabilities']['options'], $theme_only_options );

	// Set portability capabilities.
	$registered_portabilities = et_core_cache_get_group( 'et_core_portability' );

	if ( ! empty( $registered_portabilities ) ) {
		$all_role_options['general_capabilities']['options']['portability'] = array(
			'name'    => esc_html__( 'Portability', 'et_builder' ),
			'default' => 'on',
		);
		$all_role_options['portability'] = array(
			'section_title' => esc_html__( 'Portability', 'et_builder' ),
			'options'       => array(),
		);

		// Dynamically create an option foreach portability.
		foreach ( $registered_portabilities as $portability_context => $portability_instance ) {
			$all_role_options['portability']['options']["{$portability_context}_portability"] = array(
				'name'    => esc_html( $portability_instance->name ),
				'default' => 'on',
			);
		}
	}

	return $all_role_options;
}

/**
 *
 * Prints the admin page for Role Editor
 *
 */
function et_pb_display_role_editor() {
	$all_role_options = et_pb_all_role_options();
	$option_tabs = '';
	$menu_tabs = '';
	$builder_roles_array = et_pb_get_all_roles_list();

	foreach( $builder_roles_array as $role => $role_title ) {
		$option_tabs .= et_pb_generate_roles_tab( $all_role_options, $role );

		$menu_tabs .= sprintf(
			'<a href="#" class="et-pb-layout-buttons%4$s" data-open_tab="et_pb_role-%3$s_options" title="%1$s">
				<span>%2$s</span>
			</a>',
			esc_attr( $role_title ),
			esc_html( $role_title ),
			esc_attr( $role ),
			'administrator' === $role ? ' et_pb_roles_active_menu' : ''
		);
	}

	printf(
		'<div class="et_pb_roles_main_container">
			<a href="#" id="et_pb_save_roles" class="button button-primary button-large">%3$s</a>
			<h3 class="et_pb_roles_title"><span>%2$s</span></h3>
			<div id="et_pb_main_container" class="post-type-page">
				<div id="et_pb_layout_controls">
					%1$s
					<a href="#" class="et-pb-layout-buttons et-pb-layout-buttons-reset" title="Reset all settings">
						<span class="icon"></span><span class="label">Reset</span>
					</a>
					%4$s
				</div>
			</div>
			<div class="et_pb_roles_container_all">
				%5$s
			</div>
		</div>',
		$menu_tabs,
		esc_html__( 'Divi Role Editor', 'et_builder' ),
		esc_html__( 'Save Divi Roles', 'et_builder' ),
		et_core_portability_link( 'et_pb_roles', array( 'class' => 'et-pb-layout-buttons et-pb-portability-button' ) ),
		$option_tabs
	);
}

/**
 *
 * Generates the options tab for specified role.
 *
 * @return string
 */
function et_pb_generate_roles_tab( $all_role_options, $role ) {
	$form_sections = '';

	// generate all sections of the form for current role.
	if ( ! empty( $all_role_options ) ) {
		foreach( $all_role_options as $capability_id => $capability_options ) {
			$form_sections .= sprintf(
				'<div class="et_pb_roles_section_container">
					%1$s
					<div class="et_pb_roles_options_internal">
						%2$s
					</div>
				</div>',
				! empty( $capability_options['section_title'] )
					? sprintf( '<h4 class="et_pb_roles_divider">%1$s <span class="et_pb_toggle_all"></span></h4>', esc_html( $capability_options['section_title'] ) )
					: '',
				et_pb_generate_capabilities_output( $capability_options['options'], $role )
			);
		}
	}

	$output = sprintf(
		'<div class="et_pb_roles_options_container et_pb_role-%2$s_options%3$s">
			<p class="et_pb_roles_notice">%1$s</p>
			<form id="et_pb_%2$s_role" data-role_id="%2$s">
				%4$s
			</form>
		</div>',
		esc_html__( 'Using the Divi Role Editor, you can limit the types of actions that can be taken by WordPress users of different roles. This is a great way to limit the functionality available to your customers or guest authors to ensure that they only have the necessary options available to them.', 'et_builder' ),
		esc_attr( $role ),
		'administrator' === $role ? ' active-container' : '',
		$form_sections // #4
	);

	return $output;
}

/**
 *
 * Generates the enable/disable buttons list based on provided capabilities array and role
 *
 * @return string
 */
function et_pb_generate_capabilities_output( $cap_array, $role ) {
	$output = '';
	$saved_capabilities = get_option( 'et_pb_role_settings', array() );

	if ( ! empty( $cap_array ) ) {
		foreach ( $cap_array as $capability => $capability_details ) {
			if ( empty( $capability_details['applicability'] ) || ( ! empty( $capability_details['applicability'] ) && in_array( $role, $capability_details['applicability'] ) ) ) {
				$output .= sprintf(
					'<div class="et_pb_capability_option">
						<span class="et_pb_capability_title">%4$s</span>
						<div class="et_pb_yes_no_button_wrapper">
							<div class="et_pb_yes_no_button et_pb_on_state">
								<span class="et_pb_value_text et_pb_on_value">%1$s</span>
								<span class="et_pb_button_slider"></span>
								<span class="et_pb_value_text et_pb_off_value">%2$s</span>
							</div>
							<select name="%3$s" id="%3$s" class="et-pb-main-setting regular-text">
								<option value="on" %5$s>Yes</option>
								<option value="off" %6$s>No</option>
							</select>
						</div>
					</div>',
					esc_html__( 'Enabled', 'et_builder' ),
					esc_html__( 'Disabled', 'et_builder' ),
					esc_attr( $capability ),
					esc_html( $capability_details['name'] ),
					! empty( $saved_capabilities[$role][$capability] ) ? selected( 'on', $saved_capabilities[$role][$capability], false ) : selected( 'on', $capability_details['default'], false ),
					! empty( $saved_capabilities[$role][$capability] ) ? selected( 'off', $saved_capabilities[$role][$capability], false ) : selected( 'off', $capability_details['default'], false )
				);
			}
		}
	}

	return $output;
}

/**
 *
 * Loads scripts and styles for Role Editor Admin page
 *
 */
function et_pb_load_roles_admin( $hook ) {
	// load scripts only on role editor page

	if ( apply_filters( 'et_pb_load_roles_admin_hook', 'divi_page_et_divi_role_editor' ) !== $hook ) {
		return;
	}

	et_core_load_main_fonts();
	wp_enqueue_style( 'builder-roles-editor-styles', ET_BUILDER_URI . '/styles/roles_style.css', array( 'et-core-admin' ), ET_BUILDER_VERSION );
	wp_enqueue_script( 'builder-roles-editor-scripts', ET_BUILDER_URI . '/scripts/roles_admin.js', array( 'jquery', 'et_pb_admin_global_js' ), ET_BUILDER_VERSION, true );
	wp_localize_script( 'builder-roles-editor-scripts', 'et_pb_roles_options', array(
		'ajaxurl'        => admin_url( 'admin-ajax.php' ),
		'et_roles_nonce' => wp_create_nonce( 'et_roles_nonce' ),
		'modal_title'    => esc_html__( 'Reset Roles', 'et_builder' ),
		'modal_message'  => esc_html__( 'All of your current role settings will be set to defaults. Do you wish to proceed?', 'et_builder' ),
		'modal_yes'      => esc_html__( 'Yes', 'et_builder' ),
		'modal_no'       => esc_html__( 'no', 'et_builder' ),
	) );
}
add_action( 'admin_enqueue_scripts', 'et_pb_load_roles_admin' );

/**
 * Generates the array of allowed modules in jQuery Array format
 * @return string
 */
function et_pb_allowed_modules_list( $role = '' ) {
	global $typenow;
	// always return empty array if user doesn't have the edit_posts capability
	if ( ! current_user_can( 'edit_posts' ) ) {
		return "[]";
	}

	$saved_capabilities = et_pb_get_role_settings();
	$role = '' === $role ? et_pb_get_current_user_role() : $role;

	$all_modules_array = ET_Builder_Element::get_modules_array( $typenow );

	$saved_modules_capabilities = isset( $saved_capabilities[ $role ] ) ? $saved_capabilities[ $role ] : array();

	$alowed_modules = "[";
	foreach ( $all_modules_array as $module => $module_details ) {
		if ( ! in_array( $module_details['label'], array( 'et_pb_section', 'et_pb_row', 'et_pb_row_inner', 'et_pb_column' ) ) ) {
			// Add module into the list if it's not saved or if it's saved not with "off" state
			if ( ! isset( $saved_modules_capabilities[ $module_details['label'] ] ) || ( isset( $saved_modules_capabilities[ $module_details['label'] ] ) && 'off' !== $saved_modules_capabilities[ $module_details['label'] ] ) ) {
				$alowed_modules .= "'" . $module_details['label'] . "',";
			}
		}
	}

	$alowed_modules .= "]";

	return $alowed_modules;
}

if ( ! function_exists( 'et_divi_get_post_text_color' ) ) {
	function et_divi_get_post_text_color() {
		$text_color_class = '';

		$post_format = et_pb_post_format();

		if ( in_array( $post_format, array( 'audio', 'link', 'quote' ) ) ) {
			$text_color_class = ( $text_color = get_post_meta( get_the_ID(), '_et_post_bg_layout', true ) ) ? $text_color : 'light';
			$text_color_class = ' et_pb_text_color_' . $text_color_class;
		}

		return $text_color_class;
	}
}

if ( ! function_exists( 'et_divi_get_post_bg_inline_style' ) ) {
	function et_divi_get_post_bg_inline_style() {
		$inline_style = '';

		$post_id = get_the_ID();

		$post_use_bg_color = get_post_meta( $post_id, '_et_post_use_bg_color', true )
			? true
			: false;
		$post_bg_color  = ( $bg_color = get_post_meta( $post_id, '_et_post_bg_color', true ) ) && '' !== $bg_color
			? $bg_color
			: '#ffffff';

		if ( $post_use_bg_color ) {
			$inline_style = sprintf( ' style="background-color: %1$s;"', esc_html( $post_bg_color ) );
		}

		return $inline_style;
	}
}

function et_remove_blockquote_from_content( $content ) {
	if ( 'quote' !== et_pb_post_format() ) {
		return $content;
	}

	$content = preg_replace( '/<blockquote>(.+?)<\/blockquote>/is', '', $content, 1 );

	return $content;
}
add_filter( 'the_content', 'et_remove_blockquote_from_content' );

/**
 * Register rewrite rule and tag for preview page
 * @return void
 */
function et_pb_register_preview_endpoint() {
	add_rewrite_tag( '%et_pb_preview%', 'true' );
}
add_action( 'init', 'et_pb_register_preview_endpoint', 11 );

/**
 * Flush rewrite rules to fix the issue "preg_match" issue with 2.5
 * @return void
 */
function et_pb_maybe_flush_rewrite_rules() {
	et_builder_maybe_flush_rewrite_rules( '2_5_flush_rewrite_rules' );
}
add_action( 'init', 'et_pb_maybe_flush_rewrite_rules', 9 );

/**
 * Register template for preview page
 * @return string path to template file
 */
function et_pb_register_preview_page( $template ) {
	global $wp_query;

	if ( 'true' === $wp_query->get( 'et_pb_preview' ) && isset( $_GET['et_pb_preview_nonce'] ) ) {
		show_admin_bar( false );

		return ET_BUILDER_DIR . 'template-preview.php';
	}

	return $template;
}
add_action( 'template_include', 'et_pb_register_preview_page' );

/*
 * do_shortcode() replaces square brackers with html entities,
 * convert them back to make sure js code works ok
 */
if ( ! function_exists( 'et_builder_replace_code_content_entities' ) ) :
function et_builder_replace_code_content_entities( $content ) {
	$content = str_replace( '&#091;', '[', $content );
	$content = str_replace( '&#093;', ']', $content );
	$content = str_replace( '&#215;', 'x', $content );

	return $content;
}
endif;

/*
 * we use placeholders to preserve the line-breaks,
 * convert them back to \n
 */
if ( ! function_exists( 'et_builder_convert_line_breaks' ) ) :
function et_builder_convert_line_breaks( $content, $line_breaks_format = "\n"  ) {
	$content = str_replace( array( '<!– [et_pb_line_break_holder] –>', '<!-- [et_pb_line_break_holder] -->', '||et_pb_line_break_holder||' ), $line_breaks_format, $content );

	return $content;
}
endif;

// adjust the number of all layouts displayed on library page to exclude predefined layouts
function et_pb_fix_count_library_items( $counts ) {
	// do nothing if get_current_screen function doesn't exists at this point to avoid php errors in some plugins.
	if ( ! function_exists( 'get_current_screen' ) ) {
		return $counts;
	}

	$current_screen = get_current_screen();

	if ( isset( $current_screen->id ) && 'edit-et_pb_layout' === $current_screen->id && isset( $counts->publish ) ) {
		// perform query to get all the not predefined layouts
		$query = new WP_Query( array(
			'meta_query'      => array(
				array(
					'key'     => '_et_pb_predefined_layout',
					'value'   => 'on',
					'compare' => 'NOT EXISTS',
				),
			),
			'post_type'       => ET_BUILDER_LAYOUT_POST_TYPE,
			'posts_per_page'  => '-1',
		) );

		// set the $counts->publish = amount of non predefined layouts
		$counts->publish = isset( $query->post_count ) ? (int) $query->post_count : 0;
	}

	return $counts;
}
add_filter( 'wp_count_posts', 'et_pb_fix_count_library_items' );

function et_pb_generate_mobile_options_tabs() {
	$mobile_settings_tabs = '<%= window.et_builder.mobile_tabs_output() %>';

	return $mobile_settings_tabs;
}

// Generates the css code for responsive options.
// Uses array of values for each device as input parameter and css_selector with property to apply the css
function et_pb_generate_responsive_css( $values_array, $css_selector, $css_property, $function_name, $additional_css = '' ) {
	if ( ! empty( $values_array ) ) {
		foreach( $values_array as $device => $current_value ) {
			if ( '' === $current_value ) {
				continue;
			}

			$declaration = '';

			// value can be provided as a string or array in following format - array( 'property_1' => 'value_1', 'property_2' => 'property_2', ... , 'property_n' => 'value_n' )
			if ( is_array( $current_value ) && ! empty( $current_value ) ) {
				foreach( $current_value as $this_property => $this_value ) {
					if ( '' === $this_value ) {
						continue;
					}

					$declaration .= sprintf(
						'%1$s: %2$s%3$s',
						$this_property,
						esc_html( et_builder_process_range_value( $this_value ) ),
						'' !== $additional_css ? $additional_css : ';'
					);
				}
			} else {
				$declaration = sprintf(
					'%1$s: %2$s%3$s',
					$css_property,
					esc_html( et_builder_process_range_value( $current_value ) ),
					'' !== $additional_css ? $additional_css : ';'
				);
			}

			if ( '' === $declaration ) {
				continue;
			}

			$style = array(
				'selector'    => $css_selector,
				'declaration' => $declaration,
			);

			if ( 'desktop' !== $device ) {
				$current_media_query = 'tablet' === $device ? 'max_width_980' : 'max_width_767';
				$style['media_query'] = ET_Builder_Element::get_media_query( $current_media_query );
			}

			ET_Builder_Element::set_style( $function_name, $style );
		}
	}
}

function et_pb_custom_search( $query = false ) {
	if ( is_admin() || ! is_a( $query, 'WP_Query' ) || ! $query->is_search ) {
		return;
	}

	if ( isset( $_GET['et_pb_searchform_submit'] ) ) {
		$postTypes = array();
		if ( ! isset($_GET['et_pb_include_posts'] ) && ! isset( $_GET['et_pb_include_pages'] ) ) $postTypes = array( 'post' );
		if ( isset( $_GET['et_pb_include_pages'] ) ) $postTypes = array( 'page' );
		if ( isset( $_GET['et_pb_include_posts'] ) ) $postTypes[] = 'post';
		$query->set( 'post_type', $postTypes );

		if ( ! empty( $_GET['et_pb_search_cat'] ) ) {
			$categories_array = explode( ',', $_GET['et_pb_search_cat'] );
			$query->set( 'category__not_in', $categories_array );
		}

		if ( isset( $_GET['et-posts-count'] ) ) {
			$query->set( 'posts_per_page', (int) $_GET['et-posts-count'] );
		}
	}
}
add_action( 'pre_get_posts', 'et_pb_custom_search' );

if ( ! function_exists( 'et_custom_comments_display' ) ) :
function et_custom_comments_display( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;

	$default_avatar = get_option( 'avatar_default' ) ? get_option( 'avatar_default' ) : 'mystery'; ?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment-body clearfix">
			<div class="comment_avatar">
				<?php echo get_avatar( $comment, $size = '80', esc_attr( $default_avatar ), esc_attr( get_comment_author() ) ); ?>
			</div>

			<div class="comment_postinfo">
				<?php printf( '<span class="fn">%s</span>', get_comment_author_link() ); ?>
				<span class="comment_date">
				<?php
					/* translators: 1: date, 2: time */
					printf( esc_html__( 'on %1$s at %2$s', 'et_builder' ), get_comment_date(), get_comment_time() );
				?>
				</span>
				<?php edit_comment_link( esc_html__( '(Edit)', 'et_builder' ), ' ' ); ?>
			<?php
				$et_comment_reply_link = get_comment_reply_link( array_merge( $args, array(
					'reply_text' => esc_html__( 'Reply', 'et_builder' ),
					'depth'      => (int) $depth,
					'max_depth'  => (int) $args['max_depth'],
				) ) );
			?>
			</div> <!-- .comment_postinfo -->

			<div class="comment_area">
				<?php if ( '0' == $comment->comment_approved ) : ?>
					<em class="moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'et_builder' ) ?></em>
					<br />
				<?php endif; ?>

				<div class="comment-content clearfix">
				<?php
					comment_text();
					if ( $et_comment_reply_link ) echo '<span class="reply-container">' . $et_comment_reply_link . '</span>';
				?>
				</div> <!-- end comment-content-->
			</div> <!-- end comment_area-->
		</article> <!-- .comment-body -->
<?php }
endif;

/* Exclude library related taxonomies from Yoast SEO Sitemap */
function et_wpseo_sitemap_exclude_taxonomy( $value, $taxonomy ) {
	$excluded = array( 'scope', 'module_width', 'layout_type', 'layout_category', 'layout' );

	if ( in_array( $taxonomy, $excluded ) ) {
		return true;
	}

	return false;
}
add_filter( 'wpseo_sitemap_exclude_taxonomy', 'et_wpseo_sitemap_exclude_taxonomy', 10, 2 );

/**
 * Is Yoast SEO plugin active?
 *
 * @return bool  True - if the plugin is active
 */
if ( ! function_exists( 'et_is_yoast_seo_plugin_active' ) ) :
function et_is_yoast_seo_plugin_active() {
	return class_exists( 'WPSEO_Options' );
}
endif;

/**
 * Modify comment count for preview screen. Somehow WordPress' get_comments_number() doesn't get correct $post_id
 * param and doesn't have proper fallback to global $post if $post_id variable isn't found. This causes incorrect
 * comment count in preview screen
 * @see get_comments_number()
 * @see get_comments_number_text()
 * @see comments_number()
 * @return string
 */
function et_pb_preview_comment_count( $count, $post_id ) {
	if ( is_et_pb_preview() ) {
		global $post;
		$count = isset( $post->comment_count ) ? $post->comment_count : $count;
	}

	return $count;
}
add_filter( 'get_comments_number', 'et_pb_preview_comment_count', 10, 2 );

/**
 * List of shortcodes that triggers error if being used in admin
 *
 * @return array shortcode tag
 */
function et_pb_admin_excluded_shortcodes() {
	$shortcodes = array();

	// Triggers issue if Sensei and YOAST SEO are activated
	if ( et_is_yoast_seo_plugin_active() && function_exists( 'Sensei' ) ) {
		$shortcodes[] = 'usercourses';
	}

	// WPL real estate prints unwanted on-page JS that caused an issue on BB
	if ( class_exists( 'wpl_extensions' ) ) {
		$shortcodes[] = 'WPL';
	}

	return apply_filters( 'et_pb_admin_excluded_shortcodes', $shortcodes );
}

/**
 * Get GMT offset string that can be used for parsing date into correct timestamp
 *
 * @return string
 */
function et_pb_get_gmt_offset_string() {
	$gmt_offset        = get_option( 'gmt_offset' );
	$gmt_divider       = '-' === substr( $gmt_offset, 0, 1 ) ? '-' : '+';
	$gmt_offset_hour   = str_pad( abs( intval( $gmt_offset ) ), 2, "0", STR_PAD_LEFT );
	$gmt_offset_minute = str_pad( ( ( abs( $gmt_offset ) * 100 ) % 100 ) * ( 60 / 100 ), 2, "0", STR_PAD_LEFT );
	$gmt_offset_string = "GMT{$gmt_divider}{$gmt_offset_hour}{$gmt_offset_minute}";

	return $gmt_offset_string;
}

/**
 * Get post's category label and permalink to be used on frontend
 *
 * @param int    post ID
 * @return array categories
 */
function et_pb_get_post_categories( $post_id ) {
	$categories      = get_the_category( $post_id );
	$post_categories = array();

	if ( ! empty( $categories ) ) {
		foreach ( $categories as $category ) {
			$post_categories[ $category->cat_ID ] = array(
				'id'        => $category->cat_ID,
				'label'     => $category->cat_name,
				'permalink' => get_category_link( $category->cat_ID ),
			);
		}
	}

	return $post_categories;
}

/**
 * Add "Use Visual Builder" link to WP-Admin bar
 *
 * @return void
 */
function et_fb_add_admin_bar_link() {
	if ( ( ! is_singular( et_builder_get_builder_post_types() ) && ! et_builder_used_in_wc_shop() ) || ! et_pb_is_allowed( 'use_visual_builder' ) ) {
		return;
	}

	global $wp_admin_bar, $wp_the_query;

	$post_id = get_the_ID();

	// WooCommerce Shop Page replaces main query, thus it has to be normalized
	if ( et_builder_used_in_wc_shop() && method_exists( $wp_the_query, 'get_queried_object' ) && isset( $wp_the_query->get_queried_object()->ID ) ) {
		$post_id = $wp_the_query->get_queried_object()->ID;
	}

	$is_divi_library = 'et_pb_layout' === get_post_type( $post_id );

	$page_url = $is_divi_library ? get_edit_post_link( $post_id ) : get_permalink( $post_id );

	// Don't add the link, if Frontend Builder has been loaded already
	if ( et_fb_is_enabled() ) {
		$wp_admin_bar->add_menu( array(
			'id'    => 'et-disable-visual-builder',
			'title' => esc_html__( 'Exit Visual Builder', 'et_builder' ),
			'href'  => esc_url( $page_url ),
		) );

		return;
	}

	$current_object = $wp_the_query->get_queried_object();

	if ( ! current_user_can( 'edit_post', $current_object->ID ) ) {
		return;
	}

	$use_visual_builder_url = et_pb_is_pagebuilder_used( $post_id ) ?
		add_query_arg( 'et_fb', '1', et_fb_prepare_ssl_link( $page_url ) ) :
		add_query_arg( array(
			'et_fb_activation_nonce' => wp_create_nonce( 'et_fb_activation_nonce_' . $post_id ),
		), $page_url );

	$wp_admin_bar->add_menu( array(
		'id'    => 'et-use-visual-builder',
		'title' => esc_html__( 'Enable Visual Builder', 'et_builder' ),
		'href'  => esc_url( $use_visual_builder_url ),
	) );
}
add_action( 'admin_bar_menu', 'et_fb_add_admin_bar_link', 999 );

/**
 * Retrieve and process saved Layouts.
 * It different than the function which retrieves saved Sections, Rows and Modules from library because layouts require different processing
 *
 * @return array
 */
function et_fb_get_saved_layouts() {
	if ( ! wp_verify_nonce( $_POST['et_fb_retrieve_library_modules_nonce'], 'et_fb_retrieve_library_modules_nonce' ) ){
		die(-1);
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$post_type = ! empty( $_POST['et_post_type'] ) ? sanitize_text_field( $_POST['et_post_type'] ) : 'post';
	$layouts_type = ! empty( $_POST['et_load_layouts_type'] ) ? sanitize_text_field( $_POST['et_load_layouts_type'] ) : 'all';
	$start_from = ! empty( $_POST['et_templates_start_page'] ) ? sanitize_text_field( $_POST['et_templates_start_page'] ) : 0;

	$post_type = apply_filters( 'et_pb_show_all_layouts_built_for_post_type', $post_type, $layouts_type );

	$all_layouts_data = et_pb_retrieve_templates( 'layout', '', 'false', '0', $post_type, $layouts_type, array( $start_from, 50 ) );
	$all_layouts_data_processed = $all_layouts_data;
	$next_page = 'none';

	if ( 0 !== $start_from && empty( $all_layouts_data ) ) {
		$all_layouts_data_processed = array();
	} else {
		if ( empty( $all_layouts_data ) ) {
			$all_layouts_data_processed = array( 'error' => esc_html__( 'You have not saved any items to your Divi Library yet. Once an item has been saved to your library, it will appear here for easy use.', 'et_builder' ) );
		} else {
			foreach( $all_layouts_data as $index => $data ) {
				$all_layouts_data_processed[ $index ]['shortcode'] = et_fb_process_shortcode( $data['shortcode'] );
			}
			$next_page = $start_from + 50;
		}
	}

	$json_templates = json_encode( array( 'templates_data' => $all_layouts_data_processed, 'next_page' => $next_page ) );

	die( $json_templates );
}

add_action( 'wp_ajax_et_fb_get_saved_layouts', 'et_fb_get_saved_layouts' );

function et_fb_process_imported_content() {
	if ( ! isset( $_POST['et_fb_process_imported_data_nonce'] ) || ! wp_verify_nonce( $_POST['et_fb_process_imported_data_nonce'], 'et_fb_process_imported_data_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$processed_shortcode = et_fb_process_shortcode( stripslashes( $_POST['et_raw_shortcode'] ) );

	die( json_encode( $processed_shortcode ) );
}
add_action( 'wp_ajax_et_fb_process_imported_content', 'et_fb_process_imported_content' );

function et_fb_retrieve_builder_data() {
	if ( ! isset( $_POST['et_fb_helper_nonce'] ) || ! wp_verify_nonce( $_POST['et_fb_helper_nonce'], 'et_fb_backend_helper_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$post_type = ! empty( $_POST['et_post_type'] ) ? sanitize_text_field( $_POST['et_post_type'] ) : 'post';
	$post_id = ! empty( $_POST['et_post_id'] ) ? sanitize_text_field( $_POST['et_post_id'] ) : '';
	$layout_type = ! empty( $_POST['et_layout_type'] ) ? sanitize_text_field( $_POST['et_layout_type'] ) : '';

	$fields_data = array();
	$fields_data['custom_css'] = ET_Builder_Element::get_custom_css_fields( $post_type );
	$fields_data['advanced_fields'] = ET_Builder_Element::get_advanced_fields( $post_type );
	$fields_data['general_fields'] = ET_Builder_Element::get_general_fields( $post_type );
	$fields_data['fields_defaults'] = ET_Builder_Element::get_fields_defaults( $post_type );
	$fields_data['defaults'] = ET_Builder_Element::get_defaults( $post_type );
	$fields_data['optionsToggles'] = ET_Builder_Element::get_toggles( $post_type );
	$fields_data['contact_form_input_defaults'] = et_fb_process_shortcode( sprintf(
		'[et_pb_contact_field field_title="%1$s" field_type="input" field_id="Name" required_mark="on" fullwidth_field="off" /][et_pb_contact_field field_title="%2$s" field_type="email" field_id="Email" required_mark="on" fullwidth_field="off" /][et_pb_contact_field field_title="%3$s" field_type="text" field_id="Message" required_mark="on" fullwidth_field="on" /]',
		esc_attr__( 'Name', 'et_builder' ),
		esc_attr__( 'Email Address', 'et_builder' ),
		esc_attr__( 'Message', 'et_builder' )
	) );

	$post_data = get_post( $post_id );
	$post_data_post_modified = date( 'U', strtotime( $post_data->post_modified ) );
	$post_content = $post_data->post_content;

	// if autosave exists here, return it with the real content, autosave.js and getServerSavedPostData() will look for it
	$current_user_id = get_current_user_id();
	// Store one autosave per author. If there is already an autosave, overwrite it.
	$autosave = wp_get_post_autosave( $post_id, $current_user_id );

	if ( !empty( $autosave ) ) {
		$autosave_post_modified = date( 'U', strtotime( $autosave->post_modified ) );

		if ( $autosave_post_modified > $post_data_post_modified ) {
			$fields_data['autosave_shortcode_object'] = et_fb_process_shortcode( $autosave->post_content );
			$fields_data['has_newer_autosave'] = true;
		} else {
			$fields_data['has_newer_autosave'] = false;
		}
		// Delete the autosave, becuase we will present the option to use the autosave to the user, and they will use it or not
		// we need to delete the db copy now
		wp_delete_post_revision( $autosave->ID );
	}

	switch ( $layout_type ) {
		case 'module':
			$use_fullwidth_section = false !== strpos( $post_content, '[et_pb_fullwidth_' ) ? true : false;

			if ( ! $use_fullwidth_section ) {
				$post_content = sprintf( '[et_pb_row][et_pb_column type="4_4"]%1$s[/et_pb_column][/et_pb_row]', $post_content );
			}

			$post_content = sprintf(
				'[et_pb_section%2$s]%1$s[/et_pb_section]',
				$post_content,
				$use_fullwidth_section ? ' fullwidth="on"' : ''
			);

			break;
		case 'row':
			$post_content = '[et_pb_section]' . $post_content . '[/et_pb_section]';
			break;
	}

	/**
	 * Filters the raw post content when the Visual Builder is loaded.
	 *
	 * @param string $post_content The raw/unprocessed post content.
	 */
	$post_content = apply_filters( 'et_fb_load_raw_post_content', $post_content );

	$fields_data['shortcode_object'] = et_fb_process_shortcode( $post_content );

	die( json_encode( $fields_data ) );
}
add_action( 'wp_ajax_et_fb_retrieve_builder_data', 'et_fb_retrieve_builder_data' );

function et_pb_get_options_page_link() {
	if ( et_is_builder_plugin_active() ) {
		return admin_url( 'admin.php?page=et_divi_options#tab_et_dashboard_tab_content_api_main' );
	}

	return apply_filters( 'et_pb_theme_options_link', admin_url( 'admin.php?page=et_divi_options' ) );
}

/*
 * Process builder shortcode into object
 *
 * The standard do_shortcode filter should be removed, and
 * this function hooked instead
 *
 * This function is very similar to `do_shortcode`,
 * with the main differences being:
 *  - Its main design is to allow recursive array to be built out of wp shortcode
 *  - Allows shortcode callback to return an array rather than a string
 *  - It tracks the inner `index` / `_i` of each child shortcode to the passed content, which is used in the address creation as well
 *  - It uses and passes `$address` & `$parent_address`, which are used by FB app
 */
function et_fb_process_shortcode( $content, $parent_address = '', $global_parent = '', $global_parent_type = '' ) {
	global $shortcode_tags;

	if ( false === strpos( $content, '[' ) ) {
		return $content;
	}

	// Find all registered tag names in $content.
	preg_match_all( '@\[([^<>&/\[\]\x00-\x20=]++)@', $content, $matches );
	$tagnames = array_intersect( array_keys( $shortcode_tags ), $matches[1] );

	$pattern = get_shortcode_regex( $tagnames );
	$content = preg_match_all("/$pattern/", $content, $matches, PREG_SET_ORDER);

	$_matches = array();
	$_index = 0;
	foreach ( $matches as $match ) {
		$tag = $match[2];

		// reset global parent data to calculate it correctly for next modules
		if ( $global_parent_type === $tag && '' !== $global_parent ) {
			$global_parent = '';
			$global_parent_type = '';
		}

		$attr = shortcode_parse_atts( $match[3] );

		if ( ! is_array( $attr ) ) {
			$attr = array();
		}

		$index = $_index++;
		$address = isset( $parent_address ) && '' !== $parent_address ? (string) $parent_address . '.' . (string) $index : (string) $index;

		// set global parent and global parent tag if current module is global and can be a parent
		$possible_global_parents = array( 'et_pb_section', 'et_pb_row', 'et_pb_row_inner' );
		if ( '' === $global_parent && in_array( $tag, $possible_global_parents ) ) {
			$global_parent = isset( $attr['global_module'] ) ? $attr['global_module'] : '';
			$global_parent_type = $tag;
		}

		$attr['_i'] = $index;
		$attr['_address'] = $address;

		// Flag that the shortcode object is being built.
		$GLOBALS['et_fb_processing_shortcode_object'] = true;

		if ( isset( $match[5] ) ) {
			$output = call_user_func( $shortcode_tags[$tag], $attr, $match[5], $tag, $parent_address, $global_parent, $global_parent_type );
		} else {
			// self-closing tag
			$output = call_user_func( $shortcode_tags[$tag], $attr, null, $tag );
		}

		$_matches[] = $output;
	}

	// Turn off the flag since the shortcode object is done being built.
	et_fb_reset_shortcode_object_processing();

	return $_matches;
}

/**
 * Use shortcode tag which renders the content to correctly display its properties
 */
function et_fb_prepare_tag( $tag ) {
	// List of aliases
	$aliases = apply_filters( 'et_fb_prepare_tag_aliases', array(
		'et_pb_accordion_item' => 'et_pb_toggle',
	));

	return isset( $aliases[ $tag ] ) ? $aliases[ $tag ] : $tag;
}

if ( ! function_exists( 'et_strip_shortcodes' ) ) :
function et_strip_shortcodes( $content, $truncate_post_based_shortcodes_only = false ) {
	global $shortcode_tags;

	$content = trim( $content );

	$strip_content_shortcodes = array(
		'et_pb_code',
		'et_pb_fullwidth_code'
	);

	// list of post-based shortcodes
	if ( $truncate_post_based_shortcodes_only ) {
		$strip_content_shortcodes = array(
			'et_pb_post_slider',
			'et_pb_fullwidth_post_slider',
			'et_pb_blog',
			'et_pb_comments',
		);
	}

	foreach ( $strip_content_shortcodes as $shortcode_name ) {
		$regex = sprintf(
			'(\[%1$s[^\]]*\][^\[]*\[\/%1$s\]|\[%1$s[^\]]*\])',
			esc_html( $shortcode_name )
		);

		$content = preg_replace( $regex, '', $content );
	}

	// do not proceed if we need to truncate post-based shortcodes only
	if ( $truncate_post_based_shortcodes_only ) {
		return $content;
	}

	$shortcode_tag_names = array();
	foreach ( $shortcode_tags as $shortcode_tag_name => $shortcode_tag_cb ) {
		if ( 0 !== strpos( $shortcode_tag_name, 'et_pb_' ) ) {
			continue;
		}

		$shortcode_tag_names[] = $shortcode_tag_name;
	}

	$et_shortcodes = implode( '|', $shortcode_tag_names );

	$regex_opening_shortcodes = sprintf( '(\[(%1$s)[^\]]+\])', esc_html( $et_shortcodes ) );
	$regex_closing_shortcodes = sprintf( '(\[\/(%1$s)\])', esc_html( $et_shortcodes ) );

	$content = preg_replace( $regex_opening_shortcodes, '', $content );
	$content = preg_replace( $regex_closing_shortcodes, '', $content );

	return $content;
}
endif;

function et_fb_reset_shortcode_object_processing() {
	$GLOBALS['et_fb_processing_shortcode_object'] = false;
}

add_action( 'et_fb_enqueue_assets', 'et_fb_backend_helpers' );

if ( ! function_exists( 'et_builder_maybe_flush_rewrite_rules' ) ) :
function et_builder_maybe_flush_rewrite_rules( $setting_name ) {
	if ( et_get_option( $setting_name ) ) {
		return;
	}

	flush_rewrite_rules();

	et_update_option( $setting_name, 'done' );
}
endif;

/**
 * Flush rewrite rules to fix the issue Layouts, not being visible on front-end,
 * if pretty permalinks were enabled
 * @return void
 */
function et_pb_maybe_flush_3_0_rewrite_rules() {
	et_builder_maybe_flush_rewrite_rules( '3_0_flush_rewrite_rules_2' );
}
add_action( 'init', 'et_pb_maybe_flush_3_0_rewrite_rules', 9 );

/**
 * Get list of shortcut available on BB and FB
 * @param string (fb|bb) shortcut mode
 * @return array shortcut list
 */
if ( ! function_exists( 'et_builder_get_shortcuts' ) ) :
function et_builder_get_shortcuts( $on = 'fb' ) {
	$shortcuts = apply_filters('et_builder_get_shortcuts', array(
		'page' => array(
			'page_title' => array(
				'title' => esc_html__( 'Page Shortcuts', 'et_builder' ),
				'on' => array(
					'fb',
					'bb',
				),
			),
			'undo' => array(
				'kbd'  => array( 'super', 'z' ),
				'desc' => esc_html__( 'Undo', 'et_builder' ),
				'on' => array(
					'fb',
					'bb',
				),
			),
			'redo' => array(
				'kbd'  => array( 'super', 'y' ),
				'desc' => esc_html__( 'Redo', 'et_builder' ),
				'on' => array(
					'fb',
					'bb',
				),
			),
			'save' => array(
				'kbd'  => array( 'super', 's' ),
				'desc' => esc_html__( 'Save Page', 'et_builder' ),
				'on' => array(
					'fb',
					'bb',
				),
			),
			'save_as_draft' => array(
				'kbd'  => array( 'super', 'shift' , 's'),
				'desc' => esc_html__( 'Save Page As Draft', 'et_builder' ),
				'on' => array(
					'fb',
					'bb',
				),
			),
			'exit' => array(
				'kbd'  => array( 'super', 'e' ),
				'desc' => esc_html__( 'Exit Visual Builder', 'et_builder' ),
				'on' => array(
					'fb',
				),
			),
			'exit_to_backend_builder' => array(
				'kbd'  => array( 'super', 'shift', 'e' ),
				'desc' => esc_html__( 'Exit To Backend Builder', 'et_builder' ),
				'on' => array(
					'fb',
				),
			),
			'toggle_settings_bar' => array(
				'kbd'  => array( 't' ),
				'desc' => esc_html__( 'Toggle Settings Bar', 'et_builder' ),
				'on' => array(
					'fb',
				),
			),
			'open_page_settings' => array(
				'kbd'  => array( 'o' ),
				'desc' => esc_html__( 'Open Page Settings', 'et_builder' ),
				'on' => array(
					'fb',
					'bb',
				),
			),
			'open_history' => array(
				'kbd'  => array( 'h' ),
				'desc' => esc_html__( 'Open History Window', 'et_builder' ),
				'on' => array(
					'fb',
					'bb',
				),
			),
			'open_portability' => array(
				'kbd'  => array( 'p' ),
				'desc' => esc_html__( 'Open Portability Window', 'et_builder' ),
				'on' => array(
					'fb',
					'bb',
				),
			),
			'zoom_in' => array(
				'kbd'  => array( 'super', '+' ),
				'desc' => esc_html__( 'Responsive Zoom In', 'et_builder' ),
				'on' => array(
					'fb',
				),
			),
			'zoom_out' => array(
				'kbd'  => array( 'super', '-' ),
				'desc' => esc_html__( 'Responsive Zoom Out', 'et_builder' ),
				'on' => array(
					'fb',
				),
			),
			'help' => array(
				'kbd'  => array( '?' ),
				'desc' => esc_html__( 'List All Shortcuts', 'et_builder' ),
				'on' => array(
					'fb',
					'bb',
				),
			),
		),
		'inline' => array(
			'inline_title' => array(
				'title' => esc_html__( 'Inline Editor Shortcuts', 'et_builder' ),
				'on' => array(
					'fb',
				),
			),
			'escape' => array(
				'kbd'  => array( 'esc' ),
				'desc' => esc_html__( 'Exit Inline Editor', 'et_builder' ),
				'on' => array(
					'fb',
				),
			),
		),
		'module' => array(
			'module_title' => array(
				'title' => esc_html__( 'Module Shortcuts', 'et_builder' ),
				'on' => array(
					'fb',
					'bb',
				),
			),
			'module_copy' => array(
				'kbd'  => array( 'super', 'c' ),
				'desc' => esc_html__( 'Copy Module', 'et_builder' ),
				'on' => array(
					'fb',
					'bb',
				),
			),
			'module_cut' => array(
				'kbd'  => array( 'super', 'x' ),
				'desc' => esc_html__( 'Cut Module', 'et_builder' ),
				'on' => array(
					'fb',
					'bb',
				),
			),
			'module_paste' => array(
				'kbd'  => array( 'super', 'v' ),
				'desc' => esc_html__( 'Paste Module', 'et_builder' ),
				'on' => array(
					'fb',
					'bb',
				),
			),
			'module_copy_styles' => array(
				'kbd'  => array( 'super', 'alt', 'c' ),
				'desc' => esc_html__( 'Copy Module Styles', 'et_builder' ),
				'on' => array(
					'fb',
				),
			),
			'module_paste_styles' => array(
				'kbd'  => array( 'super', 'alt', 'v' ),
				'desc' => esc_html__( 'Paste Module Styles', 'et_builder' ),
				'on' => array(
					'fb',
				),
			),
			'module_lock' => array(
				'kbd'  => array( 'l' ),
				'desc' => esc_html__( 'Lock Module', 'et_builder' ),
				'on' => array(
					'fb',
					'bb',
				),
			),
			'module_disable' => array(
				'kbd'  => array( 'd' ),
				'desc' => esc_html__( 'Disable Module', 'et_builder' ),
				'on' => array(
					'fb',
					'bb',
				),
			),
			'drag_auto_copy' => array(
				'kbd'  => array( 'alt', 'module move' ),
				'desc' => esc_html__( 'Move and copy module into dropped location', 'et_builder' ),
				'on' => array(
					'fb',
				),
			),
			'column_change_structure' => array(
				'kbd'  => array( 'c', array( '1', '2', '3', '4', '5', '...' ) ),
				'desc' => esc_html__( 'Change Column Structure', 'et_builder' ),
				'on' => array(
					'fb',
					'bb',
				),
			),
			'row_make_fullwidth' => array(
				'kbd'  => array( 'r', 'f' ),
				'desc' => esc_html__( 'Make Row Fullwidth', 'et_builder' ),
				'on' => array(
					'fb',
				),
			),
			'row_edit_gutter' => array(
				'kbd'  => array( 'g', array( '1', '2', '3', '4' ) ),
				'desc' => esc_html__( 'Change Gutter Width', 'et_builder' ),
				'on' => array(
					'fb',
				),
			),
			'add_new_row' => array(
				'kbd'  => array( 'r', array( '1', '2', '3', '4', '5', '...') ),
				'desc' => esc_html__( 'Add New Row', 'et_builder' ),
				'on' => array(
					'fb',
					'bb',
				),
			),
			'add_new_section' => array(
				'kbd'  => array( 's', array( '1', '2', '3' ) ),
				'desc' => esc_html__( 'Add New Section', 'et_builder' ),
				'on' => array(
					'fb',
					'bb',
				),
			),
			'resize_padding_auto_opposite' => array(
				'kbd'  => array( 'shift', 'Drag Padding' ),
				'desc' => esc_html__( 'Restrict padding to 10px increments', 'et_builder' ),
				'on' => array(
					'fb',
				),
			),
			'resize_padding_limited' => array(
				'kbd'  => array( 'alt', 'Drag Padding' ),
				'desc' => esc_html__( 'Padding limited to opposing value', 'et_builder' ),
				'on' => array(
					'fb',
				),
			),
			'resize_padding_10' => array(
				'kbd'  => array( 'shift', 'alt', 'Drag Padding' ),
				'desc' => esc_html__( 'Mirror padding on both sides', 'et_builder' ),
				'on' => array(
					'fb',
				),
			),
			'increase_padding_row' => array(
				'kbd'  => array( 'r', array( 'left', 'right', 'up', 'down' ) ),
				'desc' => esc_html__( 'Increase Row Padding', 'et_builder' ),
				'on' => array(
					'fb',
				),
			),
			'decrease_padding_row' => array(
				'kbd'  => array( 'r', 'alt', array( 'left', 'right', 'up', 'down' ) ),
				'desc' => esc_html__( 'Decrease Row Padding', 'et_builder' ),
				'on' => array(
					'fb',
				),
			),
			'increase_padding_section' => array(
				'kbd'  => array( 's', array( 'left', 'right', 'up', 'down' ) ),
				'desc' => esc_html__( 'Increase Section Padding', 'et_builder' ),
				'on' => array(
					'fb',
				),
			),
			'decrease_padding_section' => array(
				'kbd'  => array( 's', 'alt', array( 'left', 'right', 'up', 'down' ) ),
				'desc' => esc_html__( 'Decrease Section Padding', 'et_builder' ),
				'on' => array(
					'fb',
				),
			),
			'increase_padding_row_10' => array(
				'kbd'  => array( 'r', 'shift', array( 'left', 'right', 'up', 'down' ) ),
				'desc' => esc_html__( 'Increase Row Padding By 10px', 'et_builder' ),
				'on' => array(
					'fb',
				),
			),
			'decrease_padding_row_10' => array(
				'kbd'  => array( 'r', 'alt', 'shift', array( 'left', 'right', 'up', 'down' ) ),
				'desc' => esc_html__( 'Decrease Row Padding By 10px', 'et_builder' ),
				'on' => array(
					'fb',
				),
			),
			'increase_padding_section_10' => array(
				'kbd'  => array( 's', 'shift', array( 'left', 'right', 'up', 'down' ) ),
				'desc' => esc_html__( 'Increase Section Padding By 10px', 'et_builder' ),
				'on' => array(
					'fb',
				),
			),
			'decrease_padding_section_10' => array(
				'kbd'  => array( 's', 'alt', 'shift', array( 'left', 'right', 'up', 'down' ) ),
				'desc' => esc_html__( 'Decrease Section Padding By 10px', 'et_builder' ),
				'on' => array(
					'fb',
				),
			),
		),
		'modal' => array(
			'modal_title' => array(
				'title' => esc_html__( 'Modal Shortcuts', 'et_builder' ),
				'on' => array(
					'fb',
					'bb',
				),
			),
			'escape' => array(
				'kbd'  => array( 'esc' ),
				'desc' => esc_html__( 'Close Modal', 'et_builder' ),
				'on' => array(
					'fb',
					'bb',
				),
			),
			'save_changes' => array(
				'kbd'  => array( 'enter' ),
				'desc' => esc_html__( 'Save Changes', 'et_builder' ),
				'on' => array(
					'fb',
					'bb',
				),
			),
			'undo' => array(
				'kbd'  => array( 'super', 'z' ),
				'desc' => esc_html__( 'Undo', 'et_builder' ),
				'on' => array(
					'fb',
				),
			),
			'redo' => array(
				'kbd'  => array( 'super', 'shift', 'z' ),
				'desc' => esc_html__( 'Redo', 'et_builder' ),
				'on' => array(
					'fb',
				),
			),
			'switch_tabs' => array(
				'kbd'  => array( 'shift', 'tab' ),
				'desc' => esc_html__( 'Switch Tabs', 'et_builder' ),
				'on' => array(
					'fb',
					'bb',
				),
			),
			'toggle_expand' => array(
				'kbd'  => array( 'super', 'enter' ),
				'desc' => esc_html__( 'Expand Modal Fullscreen', 'et_builder' ),
				'on' => array(
					'fb',
				),
			),
			'toggle_snap' => array(
				'kbd'  => array( 'super', array( 'left', 'right' ) ),
				'desc' => esc_html__( 'Snap Modal Left / Right', 'et_builder' ),
				'on' => array(
					'fb',
				),
			),
		),
	) );

	// Filter shortcuts
	$filtered_shortcuts = array();

	foreach ($shortcuts as $group_key => $group) {
		foreach ($group as $shortcut_key => $shortcut) {
			if ( in_array( $on, $shortcut['on'] ) ) {
				$filtered_shortcuts[ $group_key ][ $shortcut_key ] = $shortcut;
			}
		}
	}

	return $filtered_shortcuts;
}
endif;

/**
 * Parsed *_last_edited value and determine wheter the passed string means it has responsive value or not
 * *_last_edited holds two values (responsive status and last opened tabs) in the following format: status|last_opened_tab
 * @param string last_edited data
 * @return bool
 */
if ( ! function_exists( 'et_pb_get_responsive_status' ) ) :
function et_pb_get_responsive_status( $last_edited ) {
	$parsed_last_edited = is_string( $last_edited ) ? explode( '|', $last_edited ) : array( 'off', 'desktop' );

	return isset( $parsed_last_edited[0] ) ? $parsed_last_edited[0] === 'on' : false;
}
endif;

/**
 * Get unit of given value
 * @param string string with unit
 * @return string unit name
 */
if ( ! function_exists( 'et_pb_get_value_unit' ) ) :
function et_pb_get_value_unit( $value ) {
	$value                 = isset( $value ) ? $value : '';
	$valid_one_char_units  = array( "%" );
	$valid_two_chars_units = array( "em", "px", "cm", "mm", "in", "pt", "pc", "ex", "vh", "vw" );
	$important             = "!important";
	$important_length      = strlen( $important );
	$value_length          = strlen( $value );

	if ( $value === '' || is_numeric( $value ) ) {
		return 'px';
	}

	if ( substr( $value, ( 0 - $important_length ), $important_length ) === $important ) {
		$value_length = $value_length - $important_length;
		$value = substr( $value, 0, $value_length ).trim();
	}

	if ( in_array( substr( $value, -1, 1 ), $valid_one_char_units ) ) {
		return '%';
	}

	if ( in_array( substr( $value, -2, 2 ), $valid_two_chars_units ) ) {
		return substr( $value, -2, 2 );
	}

	return 'px';
}
endif;

/**
 * Sanitized value and its unit
 * @param mixed
 * @param string
 * @param string|bool
 *
 * @return string sanitized input and its unit
 */
if ( ! function_exists( 'et_sanitize_input_unit' ) ) :
function et_sanitize_input_unit( $value = '', $auto_important = false, $default_unit = false ) {
	$value                   = (string) $value;
	$valid_one_char_units    = array( '%' );
	$valid_two_chars_units   = array( 'em', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' );
	$valid_three_chars_units = array( 'deg' );
	$important               = '!important';
	$important_length        = strlen( $important );
	$has_important           = false;
	$value_length            = strlen( $value );
	$unit_value;

	// Check for important
	if ( substr( $value, ( 0 - $important_length ), $important_length ) === $important ) {
		$has_important = true;
		$value_length = $value_length - $important_length;
		$value = trim( substr( $value, 0, $value_length ) );
	}

	if ( in_array( substr( $value, -1, 1 ), $valid_one_char_units ) ) {
		$unit_value = floatval( $value ) . '%';

		// Re-add !important tag
		if ( $has_important && ! $auto_important ) {
			$unit_value = $unit_value . ' ' . $important;
		}

		return $unit_value;
	}

	if ( in_array( substr( $value, -2, 2 ), $valid_two_chars_units ) ) {
		$unit_value = floatval( $value ) . substr( $value, -2, 2 );

		// Re-add !important tag
		if ( $has_important && ! $auto_important ) {
			$unit_value = $unit_value . ' ' . $important;
		}

		return $unit_value;
	}

	if ( in_array( substr( $value, -3, 3 ), $valid_three_chars_units ) ) {
		$unit_value = floatval( $value ) . substr( $value, -3, 3 );

		// Re-add !important tag
		if ( $has_important && ! $auto_important ) {
			$unit_value = $unit_value . ' ' . $important;
		}

		return $unit_value;
	}

	$result = floatval( $value );

	if ( 'no_default_unit' === $default_unit ) {
		return $result;
	}

	if ( $default_unit ) {
		return $result . $default_unit;
	}

	if ( ! $default_unit ) {
		$result .= 'px';
	}

	// Return and automatically append px (default value)
	return $result;
}
endif;
