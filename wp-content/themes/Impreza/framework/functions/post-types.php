<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

// Should be inited before the visual composer (that is 9)
$portfolio_slug = us_get_option( 'portfolio_slug', 'portfolio' );
add_action( 'init', 'us_create_post_types', 8 );
function us_create_post_types() {
	// Portfolio post type
	global $portfolio_slug;
	if ( $portfolio_slug == '' ) {
		$portfolio_rewrite = array( 'slug' => FALSE, 'with_front' => FALSE );
	} else {
		$portfolio_rewrite = array( 'slug' => untrailingslashit( $portfolio_slug ) );
	}
	register_post_type( 'us_portfolio', array(
		'labels' => array(
			'name' => __( 'Portfolio Items', 'us' ),
			'singular_name' => __( 'Portfolio Item', 'us' ),
			'add_new' => __( 'Add Portfolio Item', 'us' ),
		),
		'public' => TRUE,
		'rewrite' => $portfolio_rewrite,
		'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'comments' ),
		'can_export' => TRUE,
		'capability_type' => 'us_portfolio',
		'map_meta_cap' => TRUE,
		'menu_icon' => 'dashicons-images-alt',
	) );

	// Portfolio categories
	register_taxonomy( 'us_portfolio_category', array( 'us_portfolio' ), array(
		'hierarchical' => TRUE,
		'label' => __( 'Portfolio Categories', 'us' ),
		'singular_label' => __( 'Portfolio Category', 'us' ),
		'rewrite' => array( 'slug' => us_get_option( 'portfolio_category_slug', 'portfolio_category' ) ),
	) );

	// Portfolio slug may have changed, so we need to keep WP's rewrite rules fresh
	if ( get_transient( 'us_flush_rules' ) ) {
		flush_rewrite_rules();
		delete_transient( 'us_flush_rules' );
	}
}

add_filter( 'manage_us_portfolio_posts_columns', 'us_manage_portfolio_columns' );
function us_manage_portfolio_columns( $columns ) {
	$columns['us_portfolio_category'] = __( 'Categories', 'us' );
	if ( isset( $columns['comments'] ) ) {
		$title = $columns['comments'];
		unset( $columns['comments'] );
		$columns['comments'] = $title;
	}
	if ( isset( $columns['date'] ) ) {
		$title = $columns['date'];
		unset( $columns['date'] );
		$columns['date'] = $title;
	}

	return $columns;
}

add_action( 'manage_us_portfolio_posts_custom_column', 'us_manage_portfolio_custom_column', 10, 2 );
function us_manage_portfolio_custom_column( $column_name, $post_id ) {
	if ( $column_name == 'us_portfolio_category' ) {
		if ( ! $terms = get_the_terms( $post_id, $column_name ) ) {
			echo '<span class="na">&ndash;</span>';
		} else {
			$termlist = array();
			foreach ( $terms as $term ) {
				$termlist[] = '<a href="' . admin_url( 'edit.php?' . $column_name . '=' . $term->slug . '&post_type=us_portfolio' ) . ' ">' . $term->name . '</a>';
			}

			echo implode( ', ', $termlist );
		}
	}
}

// TODO Move to a separate plugin for proper action order, and remove page refreshes
add_action( 'admin_init', 'us_add_theme_caps' );
function us_add_theme_caps() {
	global $wp_post_types;
	$role = get_role( 'administrator' );
	$force_refresh = FALSE;
	$custom_post_types = array( 'us_portfolio', 'us_client' );
	foreach ( $custom_post_types as $post_type ) {
		if ( ! isset( $wp_post_types[ $post_type ] ) ) {
			continue;
		}
		foreach ( $wp_post_types[ $post_type ]->cap as $cap ) {
			if ( ! $role->has_cap( $cap ) ) {
				$role->add_cap( $cap );
				$force_refresh = TRUE;
			}
		}
	}
	if ( $force_refresh AND current_user_can( 'manage_options' ) AND ! isset( $_COOKIE['us_cap_page_refreshed'] ) ) {
		// To prevent infinite refreshes when the DB is not writable
		setcookie( 'us_cap_page_refreshed' );
		header( 'Refresh: 0' );
	}
}

if ( strpos( $portfolio_slug, '%us_portfolio_category%' ) !== FALSE ) {
	function us_portfolio_link( $post_link, $id = 0 ) {
		$post = get_post( $id );
		if ( is_object( $post ) ) {
			$terms = wp_get_object_terms( $post->ID, 'us_portfolio_category' );
			if ( $terms ) {
				return str_replace( '%us_portfolio_category%', $terms[0]->slug, $post_link );
			}
		}

		return $post_link;
	}

	add_filter( 'post_type_link', 'us_portfolio_link', 1, 3 );
} elseif ( $portfolio_slug == '' ) {
	function us_portfolio_remove_slug( $post_link, $post, $leavename ) {
		if ( 'us_portfolio' != $post->post_type || 'publish' != $post->post_status ) {
			return $post_link;
		}
		$post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );

		return $post_link;
	}

	add_filter( 'post_type_link', 'us_portfolio_remove_slug', 10, 3 );

	function us_portfolio_parse_request( $query ) {
		if ( ! $query->is_main_query() || 2 != count( $query->query ) || ! isset( $query->query['page'] ) ) {
			return;
		}
		if ( ! empty( $query->query['name'] ) ) {
			$query->set( 'post_type', array( 'post', 'us_portfolio', 'page' ) );
		}
	}

	add_action( 'pre_get_posts', 'us_portfolio_parse_request' );
}
