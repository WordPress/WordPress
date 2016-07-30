<?php
/**
 * Import and Export data.
 *
 * @package Core\Portability
 */

if ( ! function_exists( 'et_core_portability_register' ) ) :
/**
 * Register portability.
 *
 * This function should be called in an 'admin_init' action callback.
 *
 * @since 1.0.0
 *
 * @param string $context A unique ID used to register the portability arguments.
 *
 * @param array  $args {
 *      Array of arguments used to register the portability.
 *
 * 		@type string $name	  The name used in the various text string.
 * 		@type bool   $view	  Whether the assets and content should load or not.
 * 		      				  Example: `isset( $_GET['page'] ) && $_GET['page'] == 'example'`.
 * 		@type string $db	  The option_name from the wp_option table used to export and import data.
 * 		@type array  $include Optional. Array of all the options scritcly included. Options ids must be set
 *         					  as the array keys.
 *      @type array  $exclude Optional. Array of excluded options. Options ids must be set as the array keys.
 * }
 * @return bool.
 */
function et_core_portability_register( $context, $args ) {
	$defaults = array(
		'context' => $context,
		'name'    => false,
		'view'    => false,
		'type'    => false,
		'target'  => false,
		'include' => array(),
		'exclude' => array(),
	);

	$data = apply_filters( "et_core_portability_args_{$context}", (object) array_merge( $defaults, (array) $args ) );

	et_core_cache_set( $context, $data, 'et_core_portability' );

	// Stop here if not allowed.
	if ( function_exists( 'et_pb_is_allowed' ) && ! et_pb_is_allowed( array( 'portability', "{$data->context}_portability" ) ) ) {

		// Set view to false if not allowed.
		$data->view = false;
		et_core_cache_set( $context, $data, 'et_core_portability' );

		return;
	}

	if ( $data->view ) {
		et_core_portability_load( $context );
	}
}
endif;

if ( ! function_exists( 'et_core_portability_load' ) ) :
/**
 * Load Portability class.
 *
 * @since 1.0.0
 *
 * @param string $context A unique ID used to register the portability arguments.
 * @return bool Always return true.
 */
function et_core_portability_load( $context ) {
	require_once( ET_CORE_PATH . 'admin/includes/class-portability.php' );
	return new ET_Core_Portability( $context );
}
endif;

if ( ! function_exists( 'et_core_portability_link' ) ) :
/**
 * HTML link to trigger the portability modal.
 *
 * @since 1.0.0
 *
 * @param string $context    The context used to register the portability.
 * @param string $attributes Optional. Query string or array of attributes. Default empty.
 * @return bool Always return true.
 */
function et_core_portability_link( $context, $attributes = array() ) {
	$instance = et_core_cache_get( $context, 'et_core_portability' );

	if ( ! current_user_can( 'switch_themes' ) || ! ( isset( $instance->view ) && $instance->view ) ) {
		return;
	}

	$defaults = array(
		'title' => esc_attr__( 'Import & Export', ET_CORE_TEXTDOMAIN ),
	);
	$attributes = array_merge( $defaults, $attributes );

	// Forced attributes.
	$attributes['href'] = '#';
	$attributes['data-et-core-modal'] = "[data-et-core-portability='{$context}']";

	$string = '';

	foreach ( $attributes as $attribute => $value ) {
		if ( null !== $value ){
			$string .= esc_attr( $attribute ) . '="' . esc_attr( $value ) . '" ';
		}
	}

	return sprintf(
		'<a %1$s><span>%2$s</span></a>',
		trim( $string ),
		esc_html( $attributes['title'] )
	);
}
endif;

if ( ! function_exists( 'et_core_portability_ajax_import' ) ) :
/**
 * Ajax portability Import.
 *
 * @since 1.0.0
 *
 * @private
 */
function et_core_portability_ajax_import() {
	if ( ! isset( $_POST['context'] ) ) {
		wp_send_json_error();
	}

	if ( $portability = et_core_portability_load( sanitize_text_field( $_POST['context'] ) ) ) {
		$portability->import();
	}
}
endif;
add_action( 'wp_ajax_et_core_portability_import', 'et_core_portability_ajax_import' );

if ( ! function_exists( 'et_core_portability_ajax_export' ) ) :
/**
 * Ajax portability Export.
 *
 * @since 1.0.0
 *
 * @private
 */
function et_core_portability_ajax_export() {
	if ( ! isset( $_POST['context'] ) ) {
		wp_send_json_error();
	}

	if ( $portability = et_core_portability_load( sanitize_text_field( $_POST['context'] ) ) ) {
		$portability->export();
	}
}
endif;
add_action( 'wp_ajax_et_core_portability_export', 'et_core_portability_ajax_export' );

if ( ! function_exists( 'et_core_portability_ajax_cancel' ) ) :
/**
 * Cancel portability action.
 *
 * @since 1.0.0
 *
 * @private
 */
function et_core_portability_ajax_cancel() {
	if ( ! isset( $_POST['context'] ) || ( ! isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'et_core_portability_nonce' ) ) ) {
		wp_send_json_error();
	}

	if ( $portability = et_core_portability_load( sanitize_text_field( $_POST['context'] ) ) ) {
		$portability->delete_temp_files( true );
	}
}
endif;
add_action( 'wp_ajax_et_core_portability_cancel', 'et_core_portability_ajax_cancel' );

if ( ! function_exists( 'et_core_portability_export' ) ) :
/**
 * Portability export.
 *
 * @since 1.0.0
 *
 * @private
 */
function et_core_portability_export() {
	if ( ! ( isset( $_GET['et_core_portability'] ) && isset( $_GET['timestamp'] ) ) ) {
		return;
	}

	if ( $portability = et_core_portability_load( sanitize_text_field( $_GET['timestamp'] ) ) ) {
		$portability->download_export();
	}
}
endif;
add_action( 'admin_init', 'et_core_portability_export', 20 );
