<?php
/**
 * Update/Install Plugin/Theme network administration panel.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.1.0
 */

if ( isset( $_GET['action'] ) && in_array( $_GET['action'], array( 'update-selected', 'activate-plugin', 'update-selected-themes' ) ) )
	define( 'IFRAME_REQUEST', true );

/** Load WordPress Administration Bootstrap */
require_once( './admin.php' );

if ( ! is_multisite() )
	wp_die( __( 'Multisite support is not enabled.' ) );

require( '../update.php' );
