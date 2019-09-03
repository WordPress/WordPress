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
				'success'
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
					'success'
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
 * Generate a single group for the personal data export report.
 *
 * @since 4.9.6
 *
 * @param array $group_data {
 *     The group data to render.
 *
 *     @type string $group_label  The user-facing heading for the group, e.g. 'Comments'.
 *     @type array  $items        {
 *         An array of group items.
 *
 *         @type array  $group_item_data  {
 *             An array of name-value pairs for the item.
 *
 *             @type string $name   The user-facing name of an item name-value pair, e.g. 'IP Address'.
 *             @type string $value  The user-facing value of an item data pair, e.g. '50.60.70.0'.
 *         }
 *     }
 * }
 * @return string The HTML for this group and its items.
 */
function wp_privacy_generate_personal_data_export_group_html( $group_data ) {
	$group_html = '<h2>' . esc_html( $group_data['group_label'] ) . '</h2>';

	if ( ! empty( $group_data['group_description'] ) ) {
		$group_html .= '<p>' . esc_html( $group_data['group_description'] ) . '</p>';
	}

	$group_html .= '<div>';

	foreach ( (array) $group_data['items'] as $group_item_id => $group_item_data ) {
		$group_html .= '<table>';
		$group_html .= '<tbody>';

		foreach ( (array) $group_item_data as $group_item_datum ) {
			$value = $group_item_datum['value'];
			// If it looks like a link, make it a link.
			if ( false === strpos( $value, ' ' ) && ( 0 === strpos( $value, 'http://' ) || 0 === strpos( $value, 'https://' ) ) ) {
				$value = '<a href="' . esc_url( $value ) . '">' . esc_html( $value ) . '</a>';
			}

			$group_html .= '<tr>';
			$group_html .= '<th>' . esc_html( $group_item_datum['name'] ) . '</th>';
			$group_html .= '<td>' . wp_kses( $value, 'personal_data_export' ) . '</td>';
			$group_html .= '</tr>';
		}

		$group_html .= '</tbody>';
		$group_html .= '</table>';
	}

	$group_html .= '</div>';

	return $group_html;
}

/**
 * Generate the personal data export file.
 *
 * @since 4.9.6
 *
 * @param int $request_id The export request ID.
 */
function wp_privacy_generate_personal_data_export_file( $request_id ) {
	if ( ! class_exists( 'ZipArchive' ) ) {
		wp_send_json_error( __( 'Unable to generate export file. ZipArchive not available.' ) );
	}

	// Get the request data.
	$request = wp_get_user_request_data( $request_id );

	if ( ! $request || 'export_personal_data' !== $request->action_name ) {
		wp_send_json_error( __( 'Invalid request ID when generating export file.' ) );
	}

	$email_address = $request->email;

	if ( ! is_email( $email_address ) ) {
		wp_send_json_error( __( 'Invalid email address when generating export file.' ) );
	}

	// Create the exports folder if needed.
	$exports_dir = wp_privacy_exports_dir();
	$exports_url = wp_privacy_exports_url();

	if ( ! wp_mkdir_p( $exports_dir ) ) {
		wp_send_json_error( __( 'Unable to create export folder.' ) );
	}

	// Protect export folder from browsing.
	$index_pathname = $exports_dir . 'index.html';
	if ( ! file_exists( $index_pathname ) ) {
		$file = fopen( $index_pathname, 'w' );
		if ( false === $file ) {
			wp_send_json_error( __( 'Unable to protect export folder from browsing.' ) );
		}
		fwrite( $file, '<!-- Silence is golden. -->' );
		fclose( $file );
	}

	$stripped_email       = str_replace( '@', '-at-', $email_address );
	$stripped_email       = sanitize_title( $stripped_email ); // slugify the email address
	$obscura              = wp_generate_password( 32, false, false );
	$file_basename        = 'wp-personal-data-file-' . $stripped_email . '-' . $obscura;
	$html_report_filename = $file_basename . '.html';
	$html_report_pathname = wp_normalize_path( $exports_dir . $html_report_filename );
	$file                 = fopen( $html_report_pathname, 'w' );
	if ( false === $file ) {
		wp_send_json_error( __( 'Unable to open export file (HTML report) for writing.' ) );
	}

	$title = sprintf(
		/* translators: %s: User's email address. */
		__( 'Personal Data Export for %s' ),
		$email_address
	);

	// Open HTML.
	fwrite( $file, "<!DOCTYPE html>\n" );
	fwrite( $file, "<html>\n" );

	// Head.
	fwrite( $file, "<head>\n" );
	fwrite( $file, "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />\n" );
	fwrite( $file, "<style type='text/css'>" );
	fwrite( $file, 'body { color: black; font-family: Arial, sans-serif; font-size: 11pt; margin: 15px auto; width: 860px; }' );
	fwrite( $file, 'table { background: #f0f0f0; border: 1px solid #ddd; margin-bottom: 20px; width: 100%; }' );
	fwrite( $file, 'th { padding: 5px; text-align: left; width: 20%; }' );
	fwrite( $file, 'td { padding: 5px; }' );
	fwrite( $file, 'tr:nth-child(odd) { background-color: #fafafa; }' );
	fwrite( $file, '</style>' );
	fwrite( $file, '<title>' );
	fwrite( $file, esc_html( $title ) );
	fwrite( $file, '</title>' );
	fwrite( $file, "</head>\n" );

	// Body.
	fwrite( $file, "<body>\n" );

	// Heading.
	fwrite( $file, '<h1>' . esc_html__( 'Personal Data Export' ) . '</h1>' );

	// And now, all the Groups.
	$groups = get_post_meta( $request_id, '_export_data_grouped', true );

	// First, build an "About" group on the fly for this report.
	$about_group = array(
		/* translators: Header for the About section in a personal data export. */
		'group_label'       => _x( 'About', 'personal data group label' ),
		/* translators: Description for the About section in a personal data export. */
		'group_description' => _x( 'Overview of export report.', 'personal data group description' ),
		'items'             => array(
			'about-1' => array(
				array(
					'name'  => _x( 'Report generated for', 'email address' ),
					'value' => $email_address,
				),
				array(
					'name'  => _x( 'For site', 'website name' ),
					'value' => get_bloginfo( 'name' ),
				),
				array(
					'name'  => _x( 'At URL', 'website URL' ),
					'value' => get_bloginfo( 'url' ),
				),
				array(
					'name'  => _x( 'On', 'date/time' ),
					'value' => current_time( 'mysql' ),
				),
			),
		),
	);

	// Merge in the special about group.
	$groups = array_merge( array( 'about' => $about_group ), $groups );

	// Now, iterate over every group in $groups and have the formatter render it in HTML.
	foreach ( (array) $groups as $group_id => $group_data ) {
		fwrite( $file, wp_privacy_generate_personal_data_export_group_html( $group_data ) );
	}

	fwrite( $file, "</body>\n" );

	// Close HTML.
	fwrite( $file, "</html>\n" );
	fclose( $file );

	/*
	 * Now, generate the ZIP.
	 *
	 * If an archive has already been generated, then remove it and reuse the
	 * filename, to avoid breaking any URLs that may have been previously sent
	 * via email.
	 */
	$error            = false;
	$archive_url      = get_post_meta( $request_id, '_export_file_url', true );
	$archive_pathname = get_post_meta( $request_id, '_export_file_path', true );

	if ( empty( $archive_pathname ) || empty( $archive_url ) ) {
		$archive_filename = $file_basename . '.zip';
		$archive_pathname = $exports_dir . $archive_filename;
		$archive_url      = $exports_url . $archive_filename;

		update_post_meta( $request_id, '_export_file_url', $archive_url );
		update_post_meta( $request_id, '_export_file_path', wp_normalize_path( $archive_pathname ) );
	}

	if ( ! empty( $archive_pathname ) && file_exists( $archive_pathname ) ) {
		wp_delete_file( $archive_pathname );
	}

	$zip = new ZipArchive;
	if ( true === $zip->open( $archive_pathname, ZipArchive::CREATE ) ) {
		if ( ! $zip->addFile( $html_report_pathname, 'index.html' ) ) {
			$error = __( 'Unable to add data to export file.' );
		}

		$zip->close();

		if ( ! $error ) {
			/**
			 * Fires right after all personal data has been written to the export file.
			 *
			 * @since 4.9.6
			 *
			 * @param string $archive_pathname     The full path to the export file on the filesystem.
			 * @param string $archive_url          The URL of the archive file.
			 * @param string $html_report_pathname The full path to the personal data report on the filesystem.
			 * @param int    $request_id           The export request ID.
			 */
			do_action( 'wp_privacy_personal_data_export_file_created', $archive_pathname, $archive_url, $html_report_pathname, $request_id );
		}
	} else {
		$error = __( 'Unable to open export file (archive) for writing.' );
	}

	// And remove the HTML file.
	unlink( $html_report_pathname );

	if ( $error ) {
		wp_send_json_error( $error );
	}
}

/**
 * Send an email to the user with a link to the personal data export file
 *
 * @since 4.9.6
 *
 * @param int $request_id The request ID for this personal data export.
 * @return true|WP_Error True on success or `WP_Error` on failure.
 */
function wp_privacy_send_personal_data_export_email( $request_id ) {
	// Get the request data.
	$request = wp_get_user_request_data( $request_id );

	if ( ! $request || 'export_personal_data' !== $request->action_name ) {
		return new WP_Error( 'invalid_request', __( 'Invalid request ID when sending personal data export email.' ) );
	}

	// Localize message content for user; fallback to site default for visitors.
	if ( ! empty( $request->user_id ) ) {
		$locale = get_user_locale( $request->user_id );
	} else {
		$locale = get_locale();
	}

	$switched_locale = switch_to_locale( $locale );

	/** This filter is documented in wp-includes/functions.php */
	$expiration      = apply_filters( 'wp_privacy_export_expiration', 3 * DAY_IN_SECONDS );
	$expiration_date = date_i18n( get_option( 'date_format' ), time() + $expiration );

	/* translators: Do not translate EXPIRATION, LINK, SITENAME, SITEURL: those are placeholders. */
	$email_text = __(
		'Howdy,

Your request for an export of personal data has been completed. You may
download your personal data by clicking on the link below. For privacy
and security, we will automatically delete the file on ###EXPIRATION###,
so please download it before then.

###LINK###

Regards,
All at ###SITENAME###
###SITEURL###'
	);

	/**
	 * Filters the text of the email sent with a personal data export file.
	 *
	 * The following strings have a special meaning and will get replaced dynamically:
	 * ###EXPIRATION###         The date when the URL will be automatically deleted.
	 * ###LINK###               URL of the personal data export file for the user.
	 * ###SITENAME###           The name of the site.
	 * ###SITEURL###            The URL to the site.
	 *
	 * @since 4.9.6
	 *
	 * @param string $email_text     Text in the email.
	 * @param int    $request_id     The request ID for this personal data export.
	 */
	$content = apply_filters( 'wp_privacy_personal_data_email_content', $email_text, $request_id );

	$email_address   = $request->email;
	$export_file_url = get_post_meta( $request_id, '_export_file_url', true );
	$site_name       = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	$site_url        = home_url();

	$content = str_replace( '###EXPIRATION###', $expiration_date, $content );
	$content = str_replace( '###LINK###', esc_url_raw( $export_file_url ), $content );
	$content = str_replace( '###EMAIL###', $email_address, $content );
	$content = str_replace( '###SITENAME###', $site_name, $content );
	$content = str_replace( '###SITEURL###', esc_url_raw( $site_url ), $content );

	$mail_success = wp_mail(
		$email_address,
		sprintf(
			/* translators: Personal data export notification email subject. %s: Site title. */
			__( '[%s] Personal Data Export' ),
			$site_name
		),
		$content
	);

	if ( $switched_locale ) {
		restore_previous_locale();
	}

	if ( ! $mail_success ) {
		return new WP_Error( 'privacy_email_error', __( 'Unable to send personal data export email.' ) );
	}

	return true;
}

/**
 * Intercept personal data exporter page Ajax responses in order to assemble the personal data export file.
 * @see wp_privacy_personal_data_export_page
 * @since 4.9.6
 *
 * @param array  $response        The response from the personal data exporter for the given page.
 * @param int    $exporter_index  The index of the personal data exporter. Begins at 1.
 * @param string $email_address   The email address of the user whose personal data this is.
 * @param int    $page            The page of personal data for this exporter. Begins at 1.
 * @param int    $request_id      The request ID for this personal data export.
 * @param bool   $send_as_email   Whether the final results of the export should be emailed to the user.
 * @param string $exporter_key    The slug (key) of the exporter.
 * @return array The filtered response.
 */
function wp_privacy_process_personal_data_export_page( $response, $exporter_index, $email_address, $page, $request_id, $send_as_email, $exporter_key ) {
	/* Do some simple checks on the shape of the response from the exporter.
	 * If the exporter response is malformed, don't attempt to consume it - let it
	 * pass through to generate a warning to the user by default Ajax processing.
	 */
	if ( ! is_array( $response ) ) {
		return $response;
	}

	if ( ! array_key_exists( 'done', $response ) ) {
		return $response;
	}

	if ( ! array_key_exists( 'data', $response ) ) {
		return $response;
	}

	if ( ! is_array( $response['data'] ) ) {
		return $response;
	}

	// Get the request data.
	$request = wp_get_user_request_data( $request_id );

	if ( ! $request || 'export_personal_data' !== $request->action_name ) {
		wp_send_json_error( __( 'Invalid request ID when merging exporter data.' ) );
	}

	$export_data = array();

	// First exporter, first page? Reset the report data accumulation array.
	if ( 1 === $exporter_index && 1 === $page ) {
		update_post_meta( $request_id, '_export_data_raw', $export_data );
	} else {
		$export_data = get_post_meta( $request_id, '_export_data_raw', true );
	}

	// Now, merge the data from the exporter response into the data we have accumulated already.
	$export_data = array_merge( $export_data, $response['data'] );
	update_post_meta( $request_id, '_export_data_raw', $export_data );

	// If we are not yet on the last page of the last exporter, return now.
	/** This filter is documented in wp-admin/includes/ajax-actions.php */
	$exporters        = apply_filters( 'wp_privacy_personal_data_exporters', array() );
	$is_last_exporter = $exporter_index === count( $exporters );
	$exporter_done    = $response['done'];
	if ( ! $is_last_exporter || ! $exporter_done ) {
		return $response;
	}

	// Last exporter, last page - let's prepare the export file.

	// First we need to re-organize the raw data hierarchically in groups and items.
	$groups = array();
	foreach ( (array) $export_data as $export_datum ) {
		$group_id    = $export_datum['group_id'];
		$group_label = $export_datum['group_label'];

		$group_description = '';
		if ( ! empty( $export_datum['group_description'] ) ) {
			$group_description = $export_datum['group_description'];
		}

		if ( ! array_key_exists( $group_id, $groups ) ) {
			$groups[ $group_id ] = array(
				'group_label'       => $group_label,
				'group_description' => $group_description,
				'items'             => array(),
			);
		}

		$item_id = $export_datum['item_id'];
		if ( ! array_key_exists( $item_id, $groups[ $group_id ]['items'] ) ) {
			$groups[ $group_id ]['items'][ $item_id ] = array();
		}

		$old_item_data                            = $groups[ $group_id ]['items'][ $item_id ];
		$merged_item_data                         = array_merge( $export_datum['data'], $old_item_data );
		$groups[ $group_id ]['items'][ $item_id ] = $merged_item_data;
	}

	// Then save the grouped data into the request.
	delete_post_meta( $request_id, '_export_data_raw' );
	update_post_meta( $request_id, '_export_data_grouped', $groups );

	/**
	 * Generate the export file from the collected, grouped personal data.
	 *
	 * @since 4.9.6
	 *
	 * @param int $request_id The export request ID.
	 */
	do_action( 'wp_privacy_personal_data_export_file', $request_id );

	// Clear the grouped data now that it is no longer needed.
	delete_post_meta( $request_id, '_export_data_grouped' );

	// If the destination is email, send it now.
	if ( $send_as_email ) {
		$mail_success = wp_privacy_send_personal_data_export_email( $request_id );
		if ( is_wp_error( $mail_success ) ) {
			wp_send_json_error( $mail_success->get_error_message() );
		}

		// Update the request to completed state when the export email is sent.
		_wp_privacy_completed_request( $request_id );
	} else {
		// Modify the response to include the URL of the export file so the browser can fetch it.
		$export_file_url = get_post_meta( $request_id, '_export_file_url', true );
		if ( ! empty( $export_file_url ) ) {
			$response['url'] = $export_file_url;
		}
	}

	return $response;
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
