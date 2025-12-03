<?php
/**
 * Server-side rendering of the `core/form` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/form` block on server.
 *
 * @param array  $attributes The block attributes.
 * @param string $content The saved content.
 *
 * @return string The content of the block being rendered.
 */
function gutenberg_render_block_core_form( $attributes, $content ) {
	wp_enqueue_script_module( '@wordpress/block-library/form/view' );

	$processed_content = new WP_HTML_Tag_Processor( $content );
	$processed_content->next_tag( 'form' );

	// Get the action for this form.
	$action = '';
	if ( isset( $attributes['action'] ) ) {
		$action = str_replace(
			array( '{SITE_URL}', '{ADMIN_URL}' ),
			array( site_url(), admin_url() ),
			$attributes['action']
		);
	}
	$processed_content->set_attribute( 'action', esc_attr( $action ) );

	// Add the method attribute. If it is not set, default to `post`.
	$method = empty( $attributes['method'] ) ? 'post' : $attributes['method'];
	$processed_content->set_attribute( 'method', $method );

	$extra_fields = apply_filters( 'render_block_core_form_extra_fields', '', $attributes );

	return str_replace(
		'</form>',
		$extra_fields . '</form>',
		$processed_content->get_updated_html()
	);
}

/**
 * Adds extra fields to the form.
 *
 * If the form is a comment form, adds the post ID as a hidden field,
 * to allow the comment to be associated with the post.
 *
 * @param string $extra_fields The extra fields.
 * @param array  $attributes   The block attributes.
 *
 * @return string The extra fields.
 */
function gutenberg_block_core_form_extra_fields_comment_form( $extra_fields, $attributes ) {
	if ( ! empty( $attributes['action'] ) && str_ends_with( $attributes['action'], '/wp-comments-post.php' ) ) {
		$extra_fields .= '<input type="hidden" name="comment_post_ID" value="' . get_the_ID() . '" id="comment_post_ID">';
	}
	return $extra_fields;
}
add_filter( 'render_block_core_form_extra_fields', 'gutenberg_block_core_form_extra_fields_comment_form', 10, 2 );

/**
 * Sends an email if the form is a contact form.
 */
function gutenberg_block_core_form_send_email() {
	check_ajax_referer( 'wp-block-form' );

	// Get the POST data.
	$params = wp_unslash( $_POST );
	// Start building the email content.
	$content = sprintf(
		/* translators: %s: The request URI. */
		__( 'Form submission from %1$s' ) . '</br>',
		'<a href="' . esc_url( get_site_url( null, $params['_wp_http_referer'] ) ) . '">' . get_bloginfo( 'name' ) . '</a>'
	);

	$skip_fields = array( 'formAction', '_ajax_nonce', 'action', '_wp_http_referer' );
	foreach ( $params as $key => $value ) {
		if ( in_array( $key, $skip_fields, true ) ) {
			continue;
		}
		$content .= sanitize_key( $key ) . ': ' . wp_kses_post( $value ) . '</br>';
	}

	// Filter the email content.
	$content = apply_filters( 'render_block_core_form_email_content', $content, $params );

	// Send the email.
	$result = wp_mail(
		str_replace( 'mailto:', '', $params['formAction'] ),
		__( 'Form submission' ),
		$content
	);

	if ( ! $result ) {
		wp_send_json_error( $result );
	}
	wp_send_json_success( $result );
}
add_action( 'wp_ajax_wp_block_form_email_submit', 'gutenberg_block_core_form_send_email' );
add_action( 'wp_ajax_nopriv_wp_block_form_email_submit', 'gutenberg_block_core_form_send_email' );

/**
 * Send the data export/remove request if the form is a privacy-request form.
 */
function gutenberg_block_core_form_privacy_form() {
	// Get the POST data.
	$params = wp_unslash( $_POST );

	// Bail early if not a form submission, or if the nonce is not valid.
	if ( empty( $params['wp-action'] )
		|| 'wp_privacy_send_request' !== $params['wp-action']
		|| empty( $params['wp-privacy-request'] )
		|| '1' !== $params['wp-privacy-request']
		|| empty( $params['email'] )
	) {
		return;
	}

	// Get the request types.
	$request_types  = _wp_privacy_action_request_types();
	$requests_found = array();
	foreach ( $request_types as $request_type ) {
		if ( ! empty( $params[ $request_type ] ) ) {
			$requests_found[] = $request_type;
		}
	}

	// Bail early if no requests were found.
	if ( empty( $requests_found ) ) {
		return;
	}

	// Process the requests.
	$actions_errored   = array();
	$actions_performed = array();
	foreach ( $requests_found as $action_name ) {
		// Get the request ID.
		$request_id = wp_create_user_request( $params['email'], $action_name );

		// Bail early if the request ID is invalid.
		if ( is_wp_error( $request_id ) ) {
			$actions_errored[] = $action_name;
			continue;
		}

		// Send the request email.
		wp_send_user_request( $request_id );
		$actions_performed[] = $action_name;
	}

	/**
	 * Determine whether the core/form-submission-notification block should be shown.
	 *
	 * @param bool   $show       Whether to show the core/form-submission-notification block.
	 * @param array  $attributes The block attributes.
	 *
	 * @return bool Whether to show the core/form-submission-notification block.
	 */
	$show_notification = static function ( $show, $attributes ) use ( $actions_performed, $actions_errored ) {
		switch ( $attributes['type'] ) {
			case 'success':
				return ! empty( $actions_performed ) && empty( $actions_errored );

			case 'error':
				return ! empty( $actions_errored );

			default:
				return $show;
		}
	};

	// Add filter to show the core/form-submission-notification block.
	add_filter( 'show_form_submission_notification_block', $show_notification, 10, 2 );
}
add_action( 'wp', 'gutenberg_block_core_form_privacy_form' );

/**
 * Registers the `core/form` block on server.
 */
function gutenberg_register_block_core_form() {
	if ( ! gutenberg_is_experiment_enabled( 'gutenberg-form-blocks' ) ) {
		return;
	}
	register_block_type_from_metadata(
		__DIR__ . '/form',
		array(
			'render_callback' => 'gutenberg_render_block_core_form',
		)
	);
}
add_action( 'init', 'gutenberg_register_block_core_form', 20 );
