<?php
/**
 * Turnstile module main file
 */

include_once path_join( __DIR__, 'service.php' );


add_action( 'wpcf7_init', 'wpcf7_turnstile_register_service', 35, 0 );

/**
 * Registers the Turnstile service.
 */
function wpcf7_turnstile_register_service() {
	$integration = WPCF7_Integration::get_instance();

	$integration->add_service( 'turnstile',
		WPCF7_Turnstile::get_instance()
	);
}


add_action( 'wp_enqueue_scripts', 'wpcf7_turnstile_enqueue_scripts', 10, 0 );

/**
 * Enqueues the Turnstile script.
 */
function wpcf7_turnstile_enqueue_scripts() {
	$service = WPCF7_Turnstile::get_instance();

	if ( ! $service->is_active() ) {
		return;
	}

	wp_enqueue_script(
		'cloudflare-turnstile',
		'https://challenges.cloudflare.com/turnstile/v0/api.js',
		array(),
		null,
		array(
			'strategy' => 'async',
		)
	);

	wp_add_inline_script(
		'cloudflare-turnstile',
		"document.addEventListener( 'wpcf7submit', e => turnstile.reset() );"
	);
}


add_action( 'wpcf7_init', 'wpcf7_add_form_tag_turnstile', 10, 0 );

/**
 * Registers the Turnstile form-tag type.
 */
function wpcf7_add_form_tag_turnstile() {
	$service = WPCF7_Turnstile::get_instance();

	if ( ! $service->is_active() ) {
		wpcf7_add_form_tag(
			'turnstile',
			'__return_empty_string',
			array(
				'display-block' => true,
			)
		);

		return;
	}

	wpcf7_add_form_tag(
		'turnstile',
		'wpcf7_turnstile_form_tag_handler',
		array(
			'display-block' => true,
			'singular' => true,
		)
	);
}


/**
 * The Turnstile form-tag handler.
 */
function wpcf7_turnstile_form_tag_handler( $tag ) {
	$service = WPCF7_Turnstile::get_instance();

	if ( ! $service->is_active() ) {
		return;
	}

	return sprintf(
		'<div %s></div>',
		wpcf7_format_atts( array(
			'class' => 'wpcf7-turnstile cf-turnstile',
			'data-sitekey' => $service->get_sitekey(),
			'data-response-field-name' => '_wpcf7_turnstile_response',
			'data-action' => $tag->get_option(
				'action', '[-0-9a-zA-Z_]{1,32}', true
			),
			'data-appearance' => $tag->get_option(
				'appearance', '(always|execute|interaction-only)', true
			),
			'data-size' => $tag->get_option(
				'size', '(normal|flexible|compact)', true
			),
			'data-theme' => $tag->get_option( 'theme', '(light|dark|auto)', true ),
			'data-language' => $tag->get_option( 'language', '[a-z-]{2,5}', true ),
			'data-tabindex' => $tag->get_option( 'tabindex', 'signed_int', true ),
		) )
	);
}


add_filter( 'wpcf7_form_elements', 'wpcf7_turnstile_prepend_widget', 10, 1 );

/**
 * Prepends a Turnstile widget to the form content if the form template
 * does not include a Turnstile form-tag.
 */
function wpcf7_turnstile_prepend_widget( $content ) {
	$service = WPCF7_Turnstile::get_instance();

	if ( ! $service->is_active() ) {
		return $content;
	}

	$contact_form = WPCF7_ContactForm::get_current();
	$manager = WPCF7_FormTagsManager::get_instance();

	$tags = $contact_form->scan_form_tags( array(
		'type' => 'turnstile',
	) );

	if ( empty( $tags ) ) {
		$content = $manager->replace_all( '[turnstile]' ) . "\n\n" . $content;
	}

	return $content;
}


add_filter( 'wpcf7_spam', 'wpcf7_turnstile_verify_response', 9, 2 );

/**
 * Verifies the Turnstile response token.
 *
 * @param bool $spam The spam/ham status inherited from preceding callbacks.
 * @param WPCF7_Submission $submission The submission object.
 * @return bool True if the submitter is a bot, false if a human.
 */
function wpcf7_turnstile_verify_response( $spam, $submission ) {
	if ( $spam ) {
		return $spam;
	}

	$service = WPCF7_Turnstile::get_instance();

	if ( ! $service->is_active() ) {
		return $spam;
	}

	$token = wpcf7_superglobal_post( '_wpcf7_turnstile_response' );

	if ( $service->verify( $token ) ) { // Human
		$spam = false;
	} else { // Bot
		$spam = true;

		if ( '' === $token ) {
			$submission->add_spam_log( array(
				'agent' => 'turnstile',
				'reason' => __( 'Turnstile token is empty.', 'contact-form-7' ),
			) );
		} else {
			$submission->add_spam_log( array(
				'agent' => 'turnstile',
				'reason' => __( 'Turnstile validation failed.', 'contact-form-7' ),
			) );
		}
	}

	return $spam;
}


add_filter(
	'wpcf7_flamingo_inbound_message_parameters',
	'wpcf7_flamingo_inbound_message_parameters_turnstile',
	10, 1
);

/**
 * Passes response data from Turnstile siteverify API to Flamingo.
 */
function wpcf7_flamingo_inbound_message_parameters_turnstile( $params ) {
	$meta = null;

	if ( $submission = WPCF7_Submission::get_instance() ) {
		$meta = $submission->pull( 'turnstile' );
	}

	if ( isset( $meta ) ) {
		$params['meta']['turnstile'] = wp_json_encode( $meta );
	}

	return $params;
}
