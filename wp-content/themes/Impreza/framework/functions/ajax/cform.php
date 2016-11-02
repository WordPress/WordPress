<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Ajax method for sending contact form via us_feedback shortcode
 */
add_action( 'wp_ajax_nopriv_us_ajax_cform', 'us_ajax_cform' );
add_action( 'wp_ajax_us_ajax_cform', 'us_ajax_cform' );
function us_ajax_cform() {
	$post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
	if ( $post_id <= 0 ) {
		wp_send_json_error();
	}
	$post = get_post( $post_id );
	if ( empty( $post ) ) {
		wp_send_json_error();
	}

	$form_index = isset( $_POST['form_index'] ) ? intval( $_POST['form_index'] ) : 1;

	// Retrieving the relevant shortcode from the page to get options
	$post_content = $post->post_content;
	preg_match_all( '~\[(vc_contact_form|us_cform)(.*?)\]~', $post_content, $matches );

	if ( ! isset( $matches[0][ $form_index - 1 ] ) ) {
		wp_send_json_error();
	}

	// Getting the relevant shortcode options
	$shortcode = $matches[0][ $form_index - 1 ];
	// For proper shortcode_parse_atts behaviour
	$shortcode = substr_replace( $shortcode, ' ]', - 1 );
	$shortcode_name = $matches[1][ $form_index - 1 ];
	$shortcode_atts = shortcode_parse_atts( $shortcode );

	// Compatibility with older versions (applying migrations)
	if ( class_exists( 'US_Migration' ) ) {
		$method_name = 'translate_' . $shortcode_name;
		foreach ( US_Migration::instance()->translators as $version => $translator ) {
			if ( method_exists( $translator, $method_name ) ) {
				$translator->{$method_name}( $shortcode_name, $shortcode_atts );
			}
		}
	}

	$shortcode_atts = shortcode_atts( array(
		'receiver_email' => '',
		'name_field' => 'required',
		'email_field' => 'required',
		'phone_field' => 'required',
		'message_field' => 'required',
		'captcha_field' => 'hidden',
	), $shortcode_atts );

	// Validating fields
	$errors = array();
	$fields = us_config( 'cform.fields', array() );
	$filled_fields_count = 0;
	foreach ( $fields as $field_name => $field ) {
		if ( ! empty( $_POST[ $field_name ] ) ) {
			$filled_fields_count ++;
		}
		if ( ! isset( $shortcode_atts[ $field_name . '_field' ] ) OR $shortcode_atts[ $field_name . '_field' ] != 'required' ) {
			continue;
		}
		if ( $field['type'] == 'captcha' ) {
			$captcha = isset( $_POST['captcha'] ) ? stripslashes( $_POST['captcha'] ) : NULL;
			$captcha_hash = isset( $_POST['captcha_hash'] ) ? stripslashes( $_POST['captcha_hash'] ) : NULL;
			$is_valid = ( $captcha_hash === md5( $captcha . NONCE_SALT ) );
		} elseif ( $field['type'] == 'email' ) {
			$is_valid = ( isset( $_POST[ $field_name ] ) AND filter_var( $_POST[ $field_name ], FILTER_VALIDATE_EMAIL ) );
		} else {
			$is_valid = ( isset( $_POST[ $field_name ] ) AND ! empty( $_POST[ $field_name ] ) );
		}
		if ( ! $is_valid ) {
			$errors[ $field_name ] = $field['error'];
		}
	}

	if ( $filled_fields_count == 0 ) {
		$errors['empty_message'] = us_config( 'cform.error.empty_message', '' );
	}

	if ( ! empty( $errors ) ) {
		wp_send_json_error( $errors );
	}

	$email_to = get_option( 'admin_email' );
	if ( ! empty( $shortcode_atts['receiver_email'] ) ) {
		$email_to = array_map( 'sanitize_email', explode( ',', $shortcode_atts['receiver_email'] ) );
	}

	$headers = array();

	if ( isset( $fields['name'] ) AND isset( $shortcode_atts['name_field'] ) AND $shortcode_atts['name_field'] != 'hidden' AND $_POST['name'] != '' ) {
		$body_name_part = '<b>' . sanitize_text_field( stripslashes( $_POST['name'] ) ) . '</b>';
	} else {
		$body_name_part = __( 'Someone', 'us' );
	}

	if ( isset( $fields['email'] ) AND isset( $shortcode_atts['email_field'] ) AND $shortcode_atts['email_field'] != 'hidden' AND $_POST['email'] != '' ) {
		if ( is_rtl() ) {
			$body_email_part = '<b>' . sanitize_email( stripslashes( $_POST['email'] ) ) . '</b> :' . __( 'Email', 'us' );
		} else {
			$body_email_part = __( 'Email', 'us' ) . ': <b>' . sanitize_email( stripslashes( $_POST['email'] ) ) . '</b>';
		}

		$headers[] = 'Reply-To: ' . sanitize_email( stripslashes( $_POST['email'] ) );
	} else {
		$body_email_part = '';
	}

	if ( isset( $fields['phone'] ) AND isset( $shortcode_atts['phone_field'] ) AND $shortcode_atts['phone_field'] != 'hidden' AND $_POST['phone'] != '' ) {
		if ( is_rtl() ) {
			$body_phone_part = '<b>' . sanitize_text_field( stripslashes( $_POST['phone'] ) ) . '</b> :' . __( 'Phone Number', 'us' );
		} else {
			$body_phone_part = __( 'Phone Number', 'us' ) . ': <b>' . sanitize_text_field( stripslashes( $_POST['phone'] ) ) . '</b>';
		}
	} else {
		$body_phone_part = '';
	}

	if ( isset( $fields['message'] ) AND isset( $shortcode_atts['message_field'] ) AND $shortcode_atts['message_field'] != 'hidden' AND $_POST['message'] != '' ) {
		$body_message_part = "<p>" . sanitize_text_field( stripslashes( $_POST['message'] ) ) . "</p>";
	} else {
		$body_message_part = '';
	}

	if ( $body_phone_part != '' OR $body_email_part != '' ) {
		if ( $body_phone_part != '' AND $body_email_part != '' ) {
			if ( is_rtl() ) {
				$body_contacts_part = ' (' . $body_email_part . ' ' . $body_phone_part . ') ';
			} else {
				$body_contacts_part = ' (' . $body_phone_part . ', ' . $body_email_part . ') ';
			}
		} else {
			$body_contacts_part = ' (' . $body_phone_part . $body_email_part . ') ';
		}
	} else {
		$body_contacts_part = ' ';
	}

	$email_body = '<p>' . __( 'You received a message from', 'us' ) . ' <a href="' . site_url() . '">' . get_bloginfo( 'name' ) . '</a></p>';
	if ( is_rtl() ) {
		$email_body .= '<p>';
		$email_body .= ( $body_message_part != '' ) ? ':' : '.';
		$email_body .= _x( 'wrote', 'smb wrote a message', 'us' ) . $body_contacts_part . $body_name_part . '</p>';
	} else {
		$email_body .= '<p>' . $body_name_part . $body_contacts_part . _x( 'wrote', 'smb wrote a message', 'us' );
		$email_body .= ( $body_message_part != '' ) ? ':' : '.';
		$email_body .= '</p>';
	}
	$email_body .= $body_message_part;

	$email_subject = sprintf( us_config( 'cform.subject' ), get_bloginfo( 'name' ) );

	if ( empty( $email_body ) ) {
		wp_send_json_error( us_config( 'cform.error.empty_message' ) );
	}
	if ( is_rtl() ) {
		$email_body = '<div style="direction: rtl; unicode-bidi: embed;">' . $email_body . '</div>';
	}

	add_filter( 'wp_mail_content_type', 'us_set_html_content_type' );
	function us_set_html_content_type() {
		return 'text/html';
	}

	// Send attempt
	$success = wp_mail( $email_to, $email_subject, $email_body, $headers );
	if ( $success ) {
		wp_send_json_success( us_config( 'cform.success' ) );
	} else {
		wp_send_json_error( us_config( 'cform.error.other' ) );
	}
}
