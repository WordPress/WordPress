<?php
/**
 * WordPress Administration Privacy Tools API.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Resend an existing request and return the result.
 *
 * @since 4.9.6
 * @access private
 *
 * @param int $request_id Request ID.
 * @return bool|WP_Error Returns true/false based on the success of sending the email, or a WP_Error object.
 */
function _wp_privacy_resend_request( $request_id ) {
	$request_id = absint( $request_id );
	$request    = get_post( $request_id );

	if ( ! $request || 'user_request' !== $request->post_type ) {
		return new WP_Error( 'privacy_request_error', __( 'Invalid request.' ) );
	}

	$result = wp_send_user_request( $request_id );

	if ( is_wp_error( $result ) ) {
		return $result;
	} elseif ( ! $result ) {
		return new WP_Error( 'privacy_request_error', __( 'Unable to initiate confirmation request.' ) );
	}

	return true;
}

/**
 * Marks a request as completed by the admin and logs the current timestamp.
 *
 * @since 4.9.6
 * @access private
 *
 * @param  int          $request_id Request ID.
 * @return int|WP_Error $result Request ID on success or WP_Error.
 */
function _wp_privacy_completed_request( $request_id ) {
	$request_id = absint( $request_id );
	$request    = wp_get_user_request_data( $request_id );

	if ( ! $request ) {
		return new WP_Error( 'privacy_request_error', __( 'Invalid request.' ) );
	}

	update_post_meta( $request_id, '_wp_user_request_completed_timestamp', time() );

	$result = wp_update_post(
		array(
			'ID'          => $request_id,
			'post_status' => 'request-completed',
		)
	);

	return $result;
}

/**
 * Handle list table actions.
 *
 * @since 4.9.6
 * @access private
 */
function _wp_personal_data_handle_actions() {
	if ( isset( $_POST['privacy_action_email_retry'] ) ) {
		check_admin_referer( 'bulk-privacy_requests' );

		$request_id = absint( current( array_keys( (array) wp_unslash( $_POST['privacy_action_email_retry'] ) ) ) );
		$result     = _wp_privacy_resend_request( $request_id );

		if ( is_wp_error( $result ) ) {
			add_settings_error(
				'privacy_action_email_retry',
				'privacy_action_email_retry',
				$result->get_error_message(),
				'error'
			);
		} else {
			add_settings_error(
				'privacy_action_email_retry',
				'privacy_action_email_retry',
				__( 'Confirmation request sent again successfully.' ),
				'updated'
			);
		}
	} elseif ( isset( $_POST['action'] ) ) {
		$action = ! empty( $_POST['action'] ) ? sanitize_key( wp_unslash( $_POST['action'] ) ) : '';

		switch ( $action ) {
			case 'add_export_personal_data_request':
			case 'add_remove_personal_data_request':
				check_admin_referer( 'personal-data-request' );

				if ( ! isset( $_POST['type_of_action'], $_POST['username_or_email_for_privacy_request'] ) ) {
					add_settings_error(
						'action_type',
						'action_type',
						__( 'Invalid action.' ),
						'error'
					);
				}
				$action_type               = sanitize_text_field( wp_unslash( $_POST['type_of_action'] ) );
				$username_or_email_address = sanitize_text_field( wp_unslash( $_POST['username_or_email_for_privacy_request'] ) );
				$email_address             = '';

				if ( ! in_array( $action_type, _wp_privacy_action_request_types(), true ) ) {
					add_settings_error(
						'action_type',
						'action_type',
						__( 'Invalid action.' ),
						'error'
					);
				}

				if ( ! is_email( $username_or_email_address ) ) {
					$user = get_user_by( 'login', $username_or_email_address );
					if ( ! $user instanceof WP_User ) {
						add_settings_error(
							'username_or_email_for_privacy_request',
							'username_or_email_for_privacy_request',
							__( 'Unable to add this request. A valid email address or username must be supplied.' ),
							'error'
						);
					} else {
						$email_address = $user->user_email;
					}
				} else {
					$email_address = $username_or_email_address;
				}

				if ( empty( $email_address ) ) {
					break;
				}

				$request_id = wp_create_user_request( $email_address, $action_type );

				if ( is_wp_error( $request_id ) ) {
					add_settings_error(
						'username_or_email_for_privacy_request',
						'username_or_email_for_privacy_request',
						$request_id->get_error_message(),
						'error'
					);
					break;
				} elseif ( ! $request_id ) {
					add_settings_error(
						'username_or_email_for_privacy_request',
						'username_or_email_for_privacy_request',
						__( 'Unable to initiate confirmation request.' ),
						'error'
					);
					break;
				}

				wp_send_user_request( $request_id );

				add_settings_error(
					'username_or_email_for_privacy_request',
					'username_or_email_for_privacy_request',
					__( 'Confirmation request initiated successfully.' ),
					'updated'
				);
				break;
		}
	}
}

/**
 * Cleans up failed and expired requests before displaying the list table.
 *
 * @since 4.9.6
 * @access private
 */
function _wp_personal_data_cleanup_requests() {
	/** This filter is documented in wp-includes/user.php */
	$expires = (int) apply_filters( 'user_request_key_expiration', DAY_IN_SECONDS );

	$requests_query = new WP_Query(
		array(
			'post_type'      => 'user_request',
			'posts_per_page' => -1,
			'post_status'    => 'request-pending',
			'fields'         => 'ids',
			'date_query'     => array(
				array(
					'column' => 'post_modified_gmt',
					'before' => $expires . ' seconds ago',
				),
			),
		)
	);

	$request_ids = $requests_query->posts;

	foreach ( $request_ids as $request_id ) {
		wp_update_post(
			array(
				'ID'            => $request_id,
				'post_status'   => 'request-failed',
				'post_password' => '',
			)
		);
	}
}

/**
 * Mark erasure requests as completed after processing is finished.
 *
 * This intercepts the Ajax responses to personal data eraser page requests, and
 * monitors the status of a request. Once all of the processing has finished, the
 * request is marked as completed.
 *
 * @since 4.9.6
 *
 * @see wp_privacy_personal_data_erasure_page
 *
 * @param array  $response      The response from the personal data eraser for
 *                              the given page.
 * @param int    $eraser_index  The index of the personal data eraser. Begins
 *                              at 1.
 * @param string $email_address The email address of the user whose personal
 *                              data this is.
 * @param int    $page          The page of personal data for this eraser.
 *                              Begins at 1.
 * @param int    $request_id    The request ID for this personal data erasure.
 * @return array The filtered response.
 */
function wp_privacy_process_personal_data_erasure_page( $response, $eraser_index, $email_address, $page, $request_id ) {
	/*
	 * If the eraser response is malformed, don't attempt to consume it; let it
	 * pass through, so that the default Ajax processing will generate a warning
	 * to the user.
	 */
	if ( ! is_array( $response ) ) {
		return $response;
	}

	if ( ! array_key_exists( 'done', $response ) ) {
		return $response;
	}

	if ( ! array_key_exists( 'items_removed', $response ) ) {
		return $response;
	}

	if ( ! array_key_exists( 'items_retained', $response ) ) {
		return $response;
	}

	if ( ! array_key_exists( 'messages', $response ) ) {
		return $response;
	}

	$request = wp_get_user_request_data( $request_id );

	if ( ! $request || 'remove_personal_data' !== $request->action_name ) {
		wp_send_json_error( __( 'Invalid request ID when processing eraser data.' ) );
	}

	/** This filter is documented in wp-admin/includes/ajax-actions.php */
	$erasers        = apply_filters( 'wp_privacy_personal_data_erasers', array() );
	$is_last_eraser = count( $erasers ) === $eraser_index;
	$eraser_done    = $response['done'];

	if ( ! $is_last_eraser || ! $eraser_done ) {
		return $response;
	}

	_wp_privacy_completed_request( $request_id );

	/**
	 * Fires immediately after a personal data erasure request has been marked completed.
	 *
	 * @since 4.9.6
	 *
	 * @param int $request_id The privacy request post ID associated with this request.
	 */
	do_action( 'wp_privacy_personal_data_erased', $request_id );

	return $response;
}
