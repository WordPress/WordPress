<?php
/**
 * Ajax handlers for the admin.
 *
 * @package WPCode
 */

add_action( 'wp_ajax_wpcode_update_snippet_status', 'wpcode_update_snippet_status' );
add_action( 'wp_ajax_wpcode_search_terms', 'wpcode_search_terms' );
add_action( 'wp_ajax_wpcode_generate_snippet', 'wpcode_generate_snippet' );
add_action( 'wp_ajax_wpcode_save_generated_snippet', 'wpcode_save_generated_snippet' );
add_action( 'wp_ajax_wpcode_verify_ssl', 'wpcode_verify_ssl' );

/**
 * Handles toggling a snippet status from the admin.
 *
 * @return void
 */
function wpcode_update_snippet_status() {
	check_ajax_referer( 'wpcode_admin' );

	if ( ! current_user_can( 'wpcode_activate_snippets' ) ) {
		wpcode()->error->add_error(
			array(
				'message' => __( 'You are not allowed to change snippet status, please contact your webmaster.', 'insert-headers-and-footers' ),
				'type'    => 'permissions',
			)
		);
		$active = false;
	} else {
		if ( empty( $_POST['snippet_id'] ) ) {
			return;
		}
		$snippet_id = absint( $_POST['snippet_id'] );
		$active     = isset( $_POST['active'] ) && 'true' === $_POST['active'];

		$snippet = new WPCode_Snippet( $snippet_id );
		if ( $active ) {
			$snippet->activate();
		} else {
			$snippet->deactivate();
		}
	}

	if ( ! isset( $snippet->active ) || $active !== $snippet->active ) {
		$error_message = sprintf(
		// Translators: formatted error code.
			__( 'Snippet not %2$s, the following error was encountered: %1$s', 'insert-headers-and-footers' ),
			'<code>' . wpcode()->error->get_last_error_message() . '</code>',
			$active ? _x( 'activated', 'Snippet status change', 'insert-headers-and-footers' ) : _x( 'deactivated', 'Snippet status change', 'insert-headers-and-footers' )
		);
		// We failed to activate it, so it's an error.
		wp_send_json_error(
			array(
				'message' => $error_message,
			)
		);
	}
	exit;
}

/**
 * Ajax handler to search for terms through all the public taxonomies.
 *
 * @return void
 */
function wpcode_search_terms() {
	check_ajax_referer( 'wpcode_admin' );

	if ( ! current_user_can( 'wpcode_edit_snippets' ) ) {
		wp_send_json_error();
	}

	$term = isset( $_GET['term'] ) ? sanitize_text_field( wp_unslash( $_GET['term'] ) ) : '';

	$public_taxonomies = get_taxonomies(
		array(
			'public' => true,
		)
	);

	$terms = get_terms(
		array(
			'search'     => $term,
			'taxonomy'   => $public_taxonomies,
			'hide_empty' => false,
		)
	);

	$results = array();

	foreach ( $terms as $term ) {
		$results[] = array(
			'id'   => $term->term_id,
			'text' => $term->name,
		);
	}

	wp_send_json(
		array(
			'results' => $results,
		)
	);
}

/**
 * Ajax handler for the generator.
 *
 * @return void
 */
function wpcode_generate_snippet() {

	check_ajax_referer( 'wpcode_generate', 'nonce' );

	if ( ! current_user_can( 'wpcode_edit_snippets' ) ) {
		wp_send_json_error();
	}

	$generator_type = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';

	$generator = wpcode()->generator->get_type( $generator_type );

	if ( ! $generator ) {
		wp_send_json_error();
	}

	$snippet_code = $generator->process_form_data( $_POST );

	wp_send_json( $snippet_code );
}

/**
 * Take the values from a generated snippet and save as a new snippet.
 *
 * @return void
 */
function wpcode_save_generated_snippet() {

	check_ajax_referer( 'wpcode_generate', 'nonce' );

	// If the current user can't edit snippets they should not be trying this.
	if ( ! current_user_can( 'wpcode_edit_snippets' ) ) {
		wp_send_json_error();
	}

	$generator_type = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
	$generator      = wpcode()->generator->get_type( $generator_type );
	// If a snippet id is passed, let's attempt to update it.
	$snippet_id = isset( $_POST['snippet_id'] ) ? absint( $_POST['snippet_id'] ) : '';

	if ( ! $generator ) {
		wp_send_json_error();
	}

	$snippet_code = $generator->process_form_data( $_POST );

	$snippet_data = array(
		// Translators: this an auto-generated title for when a snippet is saved from the generator.
		'title'          => sprintf( __( 'Generated Snippet %s', 'insert-headers-and-footers' ), $generator->get_title() ),
		'code'           => $snippet_code,
		'code_type'      => $generator->get_code_type(),
		'tags'           => $generator->get_tags(),
		'location'       => $generator->get_location(),
		'generator'      => $generator->get_name(),
		'generator_data' => $generator->get_generator_data(),
		'auto_insert'    => $generator->get_auto_insert(),
	);

	// If a snippet id is passed, let's attempt to update the snippet.
	if ( ! empty( $snippet_id ) ) {
		$snippet = new WPCode_Snippet( $snippet_id );
		// Let's make sure this is an id for a snippet.
		if ( null !== $snippet->get_post_data() ) {
			$snippet_data['id']     = $snippet_id;
			$snippet_data['active'] = false;
			// Don't change the title of an existing snippet.
			unset( $snippet_data['title'] );
		}
	}

	$new_snippet = new WPCode_Snippet( $snippet_data );

	$new_snippet_id = $new_snippet->save();

	wp_send_json_success(
		array(
			'url' => add_query_arg(
				array(
					'page'       => 'wpcode-snippet-manager',
					'snippet_id' => $new_snippet_id,
				),
				admin_url( 'admin.php' )
			),
		)
	);

}

/**
 * Ajax handler to verify that the current web host can successfully
 * make outbound SSL connections.
 *
 * @return void
 */
function wpcode_verify_ssl() {
	if ( ! current_user_can( 'wpcode_edit_snippets' ) ) {
		wp_send_json_error();
	}

	$response = wp_remote_post( 'https://wpcode.com' );

	if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
		wp_send_json_success(
			array(
				'msg' => esc_html__( 'Success! Your server can make SSL connections.', 'insert-headers-and-footers' ),
			)
		);
	}

	wp_send_json_error(
		array(
			'msg'   => esc_html__( 'There was an error and the connection failed. Please contact your web host with the technical details below.', 'insert-headers-and-footers' ),
			'debug' => '<pre>' . print_r( map_deep( $response, 'wp_strip_all_tags' ), true ) . '</pre>', // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
		)
	);
}
