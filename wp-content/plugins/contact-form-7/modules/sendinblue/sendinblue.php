<?php
/**
 * Brevo module main file
 *
 * @link https://contactform7.com/sendinblue-integration/
 */

wpcf7_include_module_file( 'sendinblue/service.php' );
wpcf7_include_module_file( 'sendinblue/contact-form-properties.php' );
wpcf7_include_module_file( 'sendinblue/doi.php' );


add_action( 'wpcf7_init', 'wpcf7_sendinblue_register_service', 10, 0 );

/**
 * Registers the Sendinblue service.
 */
function wpcf7_sendinblue_register_service() {
	$integration = WPCF7_Integration::get_instance();

	$integration->add_service( 'sendinblue',
		WPCF7_Sendinblue::get_instance()
	);
}


add_action( 'wpcf7_submit', 'wpcf7_sendinblue_submit', 10, 2 );

/**
 * Callback to the wpcf7_submit action hook. Creates a contact
 * based on the submission.
 */
function wpcf7_sendinblue_submit( $contact_form, $result ) {
	if ( $contact_form->in_demo_mode() ) {
		return;
	}

	$service = WPCF7_Sendinblue::get_instance();

	if ( ! $service->is_active() ) {
		return;
	}

	if ( empty( $result['posted_data_hash'] ) ) {
		return;
	}

	if ( empty( $result['status'] )
	or ! in_array( $result['status'], array( 'mail_sent', 'mail_failed' ), true ) ) {
		return;
	}

	$submission = WPCF7_Submission::get_instance();

	$consented = true;

	foreach ( $contact_form->scan_form_tags( 'feature=name-attr' ) as $tag ) {
		if ( $tag->has_option( 'consent_for:sendinblue' )
		and null == $submission->get_posted_data( $tag->name ) ) {
			$consented = false;
			break;
		}
	}

	if ( ! $consented ) {
		return;
	}

	$prop = wp_parse_args(
		$contact_form->prop( 'sendinblue' ),
		array(
			'enable_contact_list' => false,
			'contact_lists' => array(),
			'enable_transactional_email' => false,
			'email_template' => 0,
		)
	);

	if ( ! $prop['enable_contact_list'] ) {
		return;
	}

	$attributes = wpcf7_sendinblue_collect_parameters();

	$params = array(
		'contact' => array(),
		'email' => array(),
	);

	if ( ! empty( $attributes['EMAIL'] ) or ! empty( $attributes['SMS'] ) ) {
		$params['contact'] = apply_filters(
			'wpcf7_sendinblue_contact_parameters',
			array(
				'email' => $attributes['EMAIL'],
				'attributes' => (object) $attributes,
				'listIds' => (array) $prop['contact_lists'],
				'updateEnabled' => false,
			)
		);
	}

	if ( $prop['enable_transactional_email'] and $prop['email_template'] ) {
		$first_name = isset( $attributes['FIRSTNAME'] )
			? trim( $attributes['FIRSTNAME'] )
			: '';

		$last_name = isset( $attributes['LASTNAME'] )
			? trim( $attributes['LASTNAME'] )
			: '';

		if ( $first_name or $last_name ) {
			$email_to_name = sprintf(
				/* translators: 1: first name, 2: last name */
				_x( '%1$s %2$s', 'personal name', 'contact-form-7' ),
				$first_name,
				$last_name
			);
		} else {
			$email_to_name = '';
		}

		$params['email'] = apply_filters(
			'wpcf7_sendinblue_email_parameters',
			array(
				'templateId' => absint( $prop['email_template'] ),
				'to' => array(
					array(
						'name' => $email_to_name,
						'email' => $attributes['EMAIL'],
					),
				),
				'params' => (object) $attributes,
				'tags' => array( 'Contact Form 7' ),
			)
		);
	}

	if ( is_email( $attributes['EMAIL'] ) ) {
		$token = null;

		do_action_ref_array( 'wpcf7_doi', array(
			'wpcf7_sendinblue',
			array(
				'email_to' => $attributes['EMAIL'],
				'properties' => $params,
			),
			&$token,
		) );

		if ( isset( $token ) ) {
			return;
		}
	}

	if ( ! empty( $params['contact'] ) ) {
		$contact_id = $service->create_contact( $params['contact'] );

		if ( $contact_id and ! empty( $params['email'] ) ) {
			$service->send_email( $params['email'] );
		}
	}
}


/**
 * Collects parameters for Sendinblue contact data based on submission.
 *
 * @return array Sendinblue contact parameters.
 */
function wpcf7_sendinblue_collect_parameters() {
	$params = array();

	$submission = WPCF7_Submission::get_instance();

	foreach ( (array) $submission->get_posted_data() as $name => $val ) {
		$name = strtoupper( $name );

		if ( 'YOUR-' === substr( $name, 0, 5 ) ) {
			$name = substr( $name, 5 );
		}

		if ( $val ) {
			$params += array(
				$name => $val,
			);
		}
	}

	if ( isset( $params['SMS'] ) ) {
		$sms = trim( implode( ' ', (array) $params['SMS'] ) );
		$sms = preg_replace( '/[#*].*$/', '', $sms ); // Remove extension

		$is_international = false ||
			str_starts_with( $sms, '+' ) ||
			str_starts_with( $sms, '00' );

		if ( $is_international ) {
			$sms = preg_replace( '/^[+0]+/', '', $sms );
		}

		$sms = preg_replace( '/[^0-9]/', '', $sms );

		if ( $is_international and 6 < strlen( $sms ) and strlen( $sms ) < 16 ) {
			$params['SMS'] = '+' . $sms;
		} else { // Invalid telephone number
			unset( $params['SMS'] );
		}
	}

	if ( isset( $params['NAME'] ) ) {
		$your_name = implode( ' ', (array) $params['NAME'] );
		$your_name = explode( ' ', $your_name );

		if ( ! isset( $params['LASTNAME'] ) ) {
			$params['LASTNAME'] = implode(
				' ',
				array_slice( $your_name, 1 )
			);
		}

		if ( ! isset( $params['FIRSTNAME'] ) ) {
			$params['FIRSTNAME'] = implode(
				' ',
				array_slice( $your_name, 0, 1 )
			);
		}
	}

	$params = array_map(
		function ( $param ) {
			if ( is_array( $param ) ) {
				$param = wpcf7_array_flatten( $param );
				$param = reset( $param );
			}

			return $param;
		},
		$params
	);

	$params = apply_filters(
		'wpcf7_sendinblue_collect_parameters',
		$params
	);

	return $params;
}
