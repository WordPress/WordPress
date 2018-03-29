<?php

if ( ! function_exists( 'et_core_init' ) ):
function et_core_init() {
	ET_Core_PageResource::startup();

	if ( defined( 'ET_CORE_UPDATED' ) ) {
		global $wp_rewrite;
		add_action( 'shutdown', array( $wp_rewrite, 'flush_rules' ) );

		update_option( 'et_core_page_resource_remove_all', true );
	}

	$cache_dir = ET_Core_PageResource::get_cache_directory();

	if ( file_exists( $cache_dir . '/DONOTCACHEPAGE' ) ) {
		! defined( 'DONOTCACHEPAGE' ) ? define( 'DONOTCACHEPAGE', true ) : '';
		@unlink( $cache_dir . '/DONOTCACHEPAGE' );
	}

	if ( get_option( 'et_core_page_resource_remove_all' ) ) {
		ET_Core_PageResource::remove_static_resources( 'all', 'all', true );
	}
}
endif;


if ( ! function_exists( 'et_core_clear_wp_cache' ) ):
function et_core_clear_wp_cache( $post_id = '' ) {
	et_core_security_check( 'edit_posts' );

	// General Use Cache Plugins (typically use only one)
	if ( isset( $GLOBALS['comet_cache'] ) ) {
		// Comet Cache
		comet_cache::clear();

	} else if ( function_exists( 'rocket_clean_post' ) ) {
		// WP Rocket
		'' !== $post_id ? rocket_clean_post( $post_id ) : rocket_clean_domain();

	} else if ( has_action( 'w3tc_flush_post' ) ) {
		// W3 Total Cache
		'' !== $post_id ? do_action( 'w3tc_flush_post', $post_id ) : do_action( 'w3tc_flush_posts' );

	} else if ( function_exists( 'clear_post_supercache' ) ) {
		// WP Super Cache
		include_once WPCACHEHOME . 'wp-cache-phase1.php';
		include_once WPCACHEHOME . 'wp-cache-phase2.php';
		if ( function_exists('wp_cache_debug') && function_exists('wp_cache_clear_cache_on_menu') ) {
			'' !== $post_id ? clear_post_supercache( $post_id ) : wp_cache_clear_cache_on_menu();
		}
	} else if ( isset( $GLOBALS['wp_fastest_cache'] ) ) {
		// WP Fastest Cache
		'' !== $post_id ? $GLOBALS['wp_fastest_cache']->singleDeleteCache( $post_id ) : $GLOBALS['wp_fastest_cache']->deleteCache();

	} else if ( has_action('ce_clear_cache') ) {
		// WordPress Cache Enabler
		'' !== $post_id ? do_action( 'ce_clear_post_cache', $post_id ) : do_action('ce_clear_cache');

	} else if ( class_exists( 'LiteSpeed_Cache' ) ) {
		// LiteSpeed Cache
		$litespeed = LiteSpeed_Cache::get_instance();
		'' !== $post_id ? $litespeed->purge_post( $post_id ) : $litespeed->purge_all();

	} else if ( class_exists( 'HyperCache' ) ) {
		// Hyper Cache
		'' !== $post_id ? HyperCache::$instance->clean_post( $post_id ) : HyperCache::$instance->clean();
	}

	// Hosting Provider Caching
	if ( function_exists( 'pantheon_wp_clear_edge_keys' ) ) {
		// Pantheon Advanced Page Cache
		'' !== $post_id ? pantheon_wp_clear_edge_keys( array( "post-{$post_id}" ) ) : pantheon_wp_clear_edge_all();

	} else if ( function_exists( 'sg_cachepress_purge_cache' ) ) {
		// Siteground
		global $sg_cachepress_supercacher;
		$sg_cachepress_supercacher->purge_cache( true );

	} else if ( class_exists( 'WpeCommon' ) ) {
		// WP Engine
		WpeCommon::purge_memcached();
		WpeCommon::clear_maxcdn_cache();
		WpeCommon::purge_varnish_cache();
		WpeCommon::instance()->purge_object_cache();

	} else if ( class_exists( 'Endurance_Page_Cache' ) ) {
		// Bluehost
		wp_doing_ajax() ? ET_Core_LIB_BluehostCache::get_instance()->clear( $post_id ) : do_action( 'epc_purge' );
	}

	// Complimentary Performance Plugins
	if ( class_exists( 'autoptimizeCache' ) ) {
		// Autoptimize
		autoptimizeCache::clearall();
	}
}
endif;


if ( ! function_exists( 'et_core_get_nonces' ) ):
/**
 * Returns the nonces for this component group.
 *
 * @return string[]
 */
function et_core_get_nonces() {
	static $nonces = null;

	return $nonces ? $nonces : $nonces = array(
		'clear_page_resources_nonce' => wp_create_nonce( 'clear_page_resources' ),
	);
}
endif;


if ( ! function_exists( 'et_core_page_resource_clear' ) ):
/**
 * Ajax handler for clearing cached page resources.
 */
function et_core_page_resource_clear() {
	et_core_security_check( 'manage_options', 'clear_page_resources' );

	if ( empty( $_POST['et_post_id'] ) ) {
		et_core_die();
	}

	$post_id = sanitize_key( $_POST['et_post_id'] );
	$owner   = sanitize_key( $_POST['et_owner'] );

	ET_Core_PageResource::remove_static_resources( $post_id, $owner );
}
add_action( 'wp_ajax_et_core_page_resource_clear', 'et_core_page_resource_clear' );
endif;


if ( ! function_exists( 'et_core_page_resource_fallback' ) ):
/**
 * Handles page resource fallback requests.
 */
function et_core_page_resource_fallback() {
	if ( empty( $_GET['et_core_page_resource'] ) ) {
		return;
	}

	if ( is_admin() && ! is_customize_preview() ) {
		return;
	}

	$resource_id = sanitize_text_field( $_GET['et_core_page_resource'] );
	$pattern     = '/et-(\w+)-([\w-]+)-cached-inline-(?>styles|scripts)(\d+)/';
	$has_matches = preg_match( $pattern, $resource_id, $matches );

	if ( $has_matches && $resource = et_core_page_resource_get( $matches[1], $matches[2], $matches[3] ) ) {
		if ( $resource->has_file() ) {
			wp_redirect( $resource->URL );
			die();
		}
	}

	status_header( 404 );
	nocache_headers();
	die();
}
add_action( 'init', 'et_core_page_resource_fallback', 0 );
endif;


if ( ! function_exists( 'et_core_page_resource_get' ) ):
/**
 * Get a page resource instance.
 *
 * @param string     $owner    The owner of the instance (core|divi|builder|bloom|monarch|custom).
 * @param string     $slug     A string that uniquely identifies the resource.
 * @param string|int $post_id  The post id that the resource is associated with or `global`.
 *                             If `null`, the return value of {@link get_the_ID()} will be used.
 * @param string     $type     The resource type (style|script). Default: `style`.
 * @param string     $location Where the resource should be output (head|footer). Default: `head-late`.
 *
 * @return ET_Core_PageResource
 */
function et_core_page_resource_get( $owner, $slug, $post_id = null, $priority = 10, $location = 'head-late', $type = 'style' ) {
	$post_id = $post_id ? $post_id : et_core_page_resource_get_the_ID();
	$_slug   = "et-{$owner}-{$slug}-cached-inline-{$type}s";

	$all_resources = ET_Core_PageResource::get_resources();

	return isset( $all_resources[ $_slug ] )
		? $all_resources[ $_slug ]
		: new ET_Core_PageResource( $owner, $slug, $post_id, $priority, $location, $type );
}
endif;


if ( ! function_exists( 'et_core_page_resource_maybe_output_fallback_script' ) ):
function et_core_page_resource_maybe_output_fallback_script() {
	if ( is_admin() && ! is_customize_preview() ) {
		return;
	}

	if ( function_exists( 'et_get_option' ) && 'off' === et_get_option( 'et_pb_static_css_file', 'on' ) ) {
		return;
	}

	$POST_ID = et_core_page_resource_get_the_ID();

	if ( 'off' === get_post_meta( $POST_ID, '_et_pb_static_css_file', true ) ) {
		return;
	}

	$SITE_URL = get_site_url();
	$SCRIPT   = file_get_contents( ET_CORE_PATH . 'admin/js/page-resource-fallback.min.js' );

	print( "<script>var et_site_url='{$SITE_URL}';var et_post_id={$POST_ID};{$SCRIPT}</script>" );
}
add_action( 'wp_head', 'et_core_page_resource_maybe_output_fallback_script', 0 );
endif;


if ( ! function_exists( 'et_core_page_resource_get_the_ID' ) ):
function et_core_page_resource_get_the_ID() {
	static $post_id = null;

	if ( null !== $post_id ) {
		return $post_id;
	}

	return $post_id = apply_filters( 'et_core_page_resource_current_post_id', get_the_ID() );
}
endif;


if ( ! function_exists( 'et_core_page_resource_register_fallback_query' ) ):
function et_core_page_resource_register_fallback_query() {
	add_rewrite_tag( '%et_core_page_resource%', '([\w\d-]+)' );
}
add_action( 'init', 'et_core_page_resource_register_fallback_query', 11 );
endif;


if ( ! function_exists( 'et_core_page_resource_updated_post_meta_cb' ) ):
function et_core_page_resource_updated_post_meta_cb( $meta_id, $object_id, $meta_key, $_meta_value ) {
	$watching_keys = array(
		'sb_divi_fe_layout_overrides', // Divi Layout Injector Plugin
	);

	if ( in_array( $meta_key, $watching_keys ) && current_user_can( 'edit_posts' ) ) {
		ET_Core_PageResource::remove_static_resources( $object_id, 'all' );
	}
}
add_action( 'updated_post_meta', 'et_core_page_resource_updated_post_meta_cb', 10, 4 );
endif;


if ( ! function_exists( 'et_core_page_resource_updated_option_cb' ) ):
function et_core_page_resource_updated_option_cb( $option, $old_value, $value ) {
	$clear_cache       = false;
	$watching_prefixes = array(
		'sb_divi_fe', // Divi Layout Injector Plugin
	);

	foreach( $watching_prefixes as $prefix ) {
		if ( 0 === strpos( $option, $prefix ) ) {
			$clear_cache = true;
			break;
		}
	}

	if ( $clear_cache && current_user_can( 'edit_posts' ) ) {
		ET_Core_PageResource::remove_static_resources( 'all', 'all' );
	}
}
add_action( 'updated_option', 'et_core_page_resource_updated_option_cb', 10, 3 );
endif;
