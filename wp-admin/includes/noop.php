<?php
/**
 * Noop functions for load-scripts.php and load-styles.php.
 *
 * @package WordPress
 * @subpackage Administration
 * @since 4.4.0
 */

if ( ! function_exists( '__' ) ):
/**
 * @ignore
 */
function __() {}
endif;

if ( ! function_exists( '_x' ) ):
/**
 * @ignore
 */
function _x() {}
endif;

if ( ! function_exists( 'add_filter' ) ):
/**
 * @ignore
 */
function add_filter() {}
endif;

if ( ! function_exists( 'esc_attr' ) ):
/**
 * @ignore
 */
function esc_attr() {}
endif;

if ( ! function_exists( 'apply_filters' ) ):
/**
 * @ignore
 */
function apply_filters() {}
endif;

if ( ! function_exists( 'get_option' ) ):
/**
 * @ignore
 */
function get_option() {}
endif;

if ( ! function_exists( 'is_lighttpd_before_150' ) ):
/**
 * @ignore
 */
function is_lighttpd_before_150() {}
endif;

if ( ! function_exists( 'did_action' ) ):
/**
 * @ignore
 */
function add_action() {}
endif;

if ( ! function_exists( 'did_action' ) ):
/**
 * @ignore
 */
function did_action() {}
endif;

if ( ! function_exists( 'do_action_ref_array' ) ):
/**
 * @ignore
 */
function do_action_ref_array() {}
endif;

if ( ! function_exists( 'get_bloginfo' ) ):
/**
 * @ignore
 */
function get_bloginfo() {}
endif;

if ( ! function_exists( 'is_admin' ) ):
/**
 * @ignore
 */
function is_admin() {return true;}
endif;

if ( ! function_exists( 'site_url' ) ):
/**
 * @ignore
 */
function site_url() {}
endif;

if ( ! function_exists( 'admin_url' ) ):
/**
 * @ignore
 */
function admin_url() {}
endif;

if ( ! function_exists( 'home_url' ) ):
/**
 * @ignore
 */
function home_url() {}
endif;

if ( ! function_exists( 'includes_url' ) ):
/**
 * @ignore
 */
function includes_url() {}
endif;

if ( ! function_exists( 'wp_guess_url' ) ):
/**
 * @ignore
 */
function wp_guess_url() {}
endif;

if ( ! function_exists( 'json_encode' ) ) :
/**
 * @ignore
 */
function json_encode() {}
endif;

function get_file( $path ) {

	if ( function_exists('realpath') ) {
		$path = realpath( $path );
	}

	if ( ! $path || ! @is_file( $path ) ) {
		return '';
	}

	return @file_get_contents( $path );
}