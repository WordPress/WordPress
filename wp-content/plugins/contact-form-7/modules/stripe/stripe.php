<?php
/**
 * Stripe module main file
 *
 * @link https://contactform7.com/stripe-integration/
 */

wpcf7_include_module_file( 'stripe/service.php' );
wpcf7_include_module_file( 'stripe/api.php' );


add_action(
	'wpcf7_init',
	'wpcf7_stripe_register_service',
	50, 0
);

/**
 * Registers the Stripe service.
 */
function wpcf7_stripe_register_service() {
	$integration = WPCF7_Integration::get_instance();

	$integration->add_service( 'stripe',
		WPCF7_Stripe::get_instance()
	);
}


add_action(
	'wpcf7_enqueue_scripts',
	'wpcf7_stripe_enqueue_scripts',
	10, 0
);

/**
 * Enqueues scripts and styles for the Stripe module.
 */
function wpcf7_stripe_enqueue_scripts() {
	$service = WPCF7_Stripe::get_instance();

	if ( ! $service->is_active() ) {
		return;
	}

	wp_enqueue_style( 'wpcf7-stripe',
		wpcf7_plugin_url( 'modules/stripe/style.css' ),
		array(), WPCF7_VERSION, 'all'
	);

	wp_register_script(
		'stripe',
		'https://js.stripe.com/v3/',
		array(),
		null,
		array( 'in_footer' => true )
	);

	$assets = include wpcf7_plugin_path( 'modules/stripe/index.asset.php' );

	$assets = wp_parse_args( $assets, array(
		'dependencies' => array(),
		'version' => WPCF7_VERSION,
	) );

	wp_enqueue_script(
		'wpcf7-stripe',
		wpcf7_plugin_url( 'modules/stripe/index.js' ),
		array_merge(
			$assets['dependencies'],
			array(
				'wp-polyfill',
				'contact-form-7',
				'stripe',
			)
		),
		$assets['version'],
		array( 'in_footer' => true )
	);

	$api_keys = $service->get_api_keys();

	if ( $api_keys['publishable'] ) {
		wp_add_inline_script( 'wpcf7-stripe',
			sprintf(
				'var wpcf7_stripe = %s;',
				wp_json_encode( array(
					'publishable_key' => $api_keys['publishable'],
				), JSON_PRETTY_PRINT )
			),
			'before'
		);
	}
}


add_filter(
	'wpcf7_skip_spam_check',
	'wpcf7_stripe_skip_spam_check',
	10, 2
);

/**
 * Skips the spam check if it is not necessary.
 *
 * @return bool True if the spam check is not necessary.
 */
function wpcf7_stripe_skip_spam_check( $skip_spam_check, $submission ) {
	$service = WPCF7_Stripe::get_instance();

	if ( ! $service->is_active() ) {
		return $skip_spam_check;
	}

	$pi_id = (string) wpcf7_superglobal_post( '_wpcf7_stripe_payment_intent' );

	if ( $pi_id ) {
		$payment_intent = $service->api()->retrieve_payment_intent( $pi_id );

		if (
			isset( $payment_intent['metadata']['wpcf7_submission_timestamp'] )
		) {
			// This PI has already been used. Ignore.
			return $skip_spam_check;
		}

		if (
			isset( $payment_intent['status'] ) and
			'succeeded' === $payment_intent['status']
		) {
			$submission->push( 'payment_intent', $pi_id );

			$service->api()->update_payment_intent( $pi_id, array(
				'metadata' => array_merge( $payment_intent['metadata'], array(
					'wpcf7_submission_timestamp' => $submission->get_meta( 'timestamp' ),
				) ),
			) );
		}
	}

	if (
		! empty( $submission->pull( 'payment_intent' ) ) and
		$submission->verify_posted_data_hash()
	) {
		$skip_spam_check = true;
	}

	return $skip_spam_check;
}


add_filter(
	'wpcf7_spam',
	'wpcf7_stripe_verify_payment_intent',
	6, 2
);

/**
 * Verifies submitted Stripe Payment Intent ID.
 */
function wpcf7_stripe_verify_payment_intent( $spam, $submission ) {
	$service = WPCF7_Stripe::get_instance();

	if ( ! $service->is_active() ) {
		return $spam;
	}

	$pi_id = (string) wpcf7_superglobal_post( '_wpcf7_stripe_payment_intent' );

	if ( $pi_id ) {
		$payment_intent = $service->api()->retrieve_payment_intent( $pi_id );

		if (
			! $payment_intent or
			isset( $payment_intent['metadata']['wpcf7_submission_timestamp'] )
		) {
			$spam = true;

			$submission->add_spam_log( array(
				'agent' => 'stripe',
				'reason' => __(
					'Invalid Stripe Payment Intent ID detected.',
					'contact-form-7'
				),
			) );
		}
	}

	return $spam;
}


add_action(
	'wpcf7_before_send_mail',
	'wpcf7_stripe_before_send_mail',
	10, 3
);

/**
 * Creates Stripe's Payment Intent.
 */
function wpcf7_stripe_before_send_mail( $contact_form, &$abort, $submission ) {
	$service = WPCF7_Stripe::get_instance();

	if ( ! $service->is_active() ) {
		return;
	}

	$tags = $contact_form->scan_form_tags( array( 'type' => 'stripe' ) );

	if ( ! $tags ) {
		return;
	}

	if ( ! empty( $submission->pull( 'payment_intent' ) ) ) {
		return;
	}

	$tag = $tags[0];
	$amount = $tag->get_option( 'amount', 'int', true );
	$currency = $tag->get_option( 'currency', '[a-zA-Z]{3}', true );

	$payment_intent_params = apply_filters(
		'wpcf7_stripe_payment_intent_parameters',
		array(
			'amount' => $amount ? absint( $amount ) : null,
			'currency' => $currency ? strtolower( $currency ) : null,
			'receipt_email' => $submission->get_posted_data( 'your-email' ),
		)
	);

	$payment_intent = $service->api()->create_payment_intent(
		$payment_intent_params
	);

	if ( $payment_intent ) {
		$submission->add_result_props( array(
			'stripe' => array(
				'payment_intent' => array(
					'id' => $payment_intent['id'],
					'client_secret' => $payment_intent['client_secret'],
				),
			),
		) );

		$submission->set_status( 'payment_required' );

		$submission->set_response(
			__( 'Payment is required. Please pay by credit card.', 'contact-form-7' )
		);
	}

	$abort = true;
}


/**
 * Returns payment link URL.
 *
 * @param string $pi_id Payment Intent ID.
 * @return string The URL.
 */
function wpcf7_stripe_get_payment_link( $pi_id ) {
	return sprintf(
		'https://dashboard.stripe.com/payments/%s',
		urlencode( $pi_id )
	);
}


add_filter(
	'wpcf7_special_mail_tags',
	'wpcf7_stripe_smt',
	10, 4
);

/**
 * Registers the [_stripe_payment_link] special mail-tag.
 */
function wpcf7_stripe_smt( $output, $tag_name, $html, $mail_tag = null ) {
	if ( '_stripe_payment_link' === $tag_name ) {
		$submission = WPCF7_Submission::get_instance();

		$pi_id = $submission->pull( 'payment_intent' );

		if ( ! empty( $pi_id ) ) {
			$output = wpcf7_stripe_get_payment_link( $pi_id );
		}
	}

	return $output;
}


add_filter(
	'wpcf7_flamingo_inbound_message_parameters',
	'wpcf7_stripe_add_flamingo_inbound_message_params',
	10, 1
);

/**
 * Adds Stripe-related meta data to Flamingo Inbound Message parameters.
 */
function wpcf7_stripe_add_flamingo_inbound_message_params( $args ) {
	$submission = WPCF7_Submission::get_instance();

	$pi_id = $submission->pull( 'payment_intent' );

	if ( empty( $pi_id ) ) {
		return $args;
	}

	$pi_link = wpcf7_stripe_get_payment_link( $pi_id );

	$meta = (array) $args['meta'];

	$meta['stripe_payment_link'] = $pi_link;

	$args['meta'] = $meta;

	return $args;
}


add_action(
	'wpcf7_init',
	'wpcf7_add_form_tag_stripe',
	10, 0
);

/**
 * Registers the stripe form-tag handler.
 */
function wpcf7_add_form_tag_stripe() {
	wpcf7_add_form_tag(
		'stripe',
		'wpcf7_stripe_form_tag_handler',
		array(
			'display-block' => true,
			'singular' => true,
		)
	);
}


/**
 * Defines the stripe form-tag handler.
 *
 * @return string HTML content that replaces a stripe form-tag.
 */
function wpcf7_stripe_form_tag_handler( $tag ) {
	$card_element = sprintf(
		'<div %s></div>',
		wpcf7_format_atts( array(
			'class' => 'card-element wpcf7-form-control',
			'aria-invalid' => 'false',
		) )
	);

	$card_element = sprintf(
		'<div class="wpcf7-form-control-wrap hidden">%s</div>',
		$card_element
	);

	$button_1_label = __( 'Proceed to checkout', 'contact-form-7' );

	if ( isset( $tag->values[0] ) ) {
		$button_1_label = trim( $tag->values[0] );
	}

	$button_1 = sprintf(
		'<button %1$s>%2$s</button>',
		wpcf7_format_atts( array(
			'type' => 'submit',
			'class' => 'first',
		) ),
		esc_html( $button_1_label )
	);

	$button_2_label = __( 'Complete payment', 'contact-form-7' );

	if ( isset( $tag->values[1] ) ) {
		$button_2_label = trim( $tag->values[1] );
	}

	$button_2 = sprintf(
		'<button %1$s>%2$s</button>',
		wpcf7_format_atts( array(
			'type' => 'button',
			'class' => 'second hidden',
		) ),
		esc_html( $button_2_label )
	);

	$buttons = sprintf(
		'<span class="buttons has-spinner">%1$s %2$s</span>',
		$button_1, $button_2
	);

	return sprintf(
		'<div class="wpcf7-stripe">%1$s %2$s %3$s</div>',
		$card_element,
		$buttons,
		'<input type="hidden" name="_wpcf7_stripe_payment_intent" value="" />'
	);
}
