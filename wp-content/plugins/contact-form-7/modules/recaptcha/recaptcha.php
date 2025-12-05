<?php
/**
 * reCAPTCHA module main file
 *
 * @link https://contactform7.com/recaptcha/
 */

wpcf7_include_module_file( 'recaptcha/service.php' );


add_action( 'wpcf7_init', 'wpcf7_recaptcha_register_service', 40, 0 );

/**
 * Registers the reCAPTCHA service.
 */
function wpcf7_recaptcha_register_service() {
	$integration = WPCF7_Integration::get_instance();

	$integration->add_service( 'recaptcha',
		WPCF7_RECAPTCHA::get_instance()
	);
}


add_action(
	'wp_enqueue_scripts',
	'wpcf7_recaptcha_enqueue_scripts',
	20, 0
);

/**
 * Enqueues frontend scripts for reCAPTCHA.
 */
function wpcf7_recaptcha_enqueue_scripts() {
	$service = WPCF7_RECAPTCHA::get_instance();

	if ( ! $service->is_active() ) {
		return;
	}

	$url = 'https://www.google.com/recaptcha/api.js';

	if ( apply_filters( 'wpcf7_use_recaptcha_net', false ) ) {
		$url = 'https://www.recaptcha.net/recaptcha/api.js';
	}

	wp_register_script( 'google-recaptcha',
		add_query_arg(
			array(
				'render' => $service->get_sitekey(),
			),
			$url
		),
		array(),
		'3.0',
		array( 'in_footer' => true )
	);

	$assets = include wpcf7_plugin_path( 'modules/recaptcha/index.asset.php' );

	$assets = wp_parse_args( $assets, array(
		'dependencies' => array(),
		'version' => WPCF7_VERSION,
	) );

	wp_register_script(
		'wpcf7-recaptcha',
		wpcf7_plugin_url( 'modules/recaptcha/index.js' ),
		array_merge(
			$assets['dependencies'],
			array(
				'google-recaptcha',
				'wp-polyfill',
			)
		),
		$assets['version'],
		array( 'in_footer' => true )
	);

	wp_enqueue_script( 'wpcf7-recaptcha' );

	$wpcf7_recaptcha_obj = array(
		'sitekey' => $service->get_sitekey(),
		'actions' => apply_filters( 'wpcf7_recaptcha_actions', array(
			'homepage' => 'homepage',
			'contactform' => 'contactform',
		) ),
	);

	wp_add_inline_script( 'wpcf7-recaptcha',
		sprintf(
			'var wpcf7_recaptcha = %s;',
			wp_json_encode( $wpcf7_recaptcha_obj, JSON_PRETTY_PRINT )
		),
		'before'
	);
}


add_filter(
	'wpcf7_form_hidden_fields',
	'wpcf7_recaptcha_add_hidden_fields',
	100, 1
);

/**
 * Adds hidden form field for reCAPTCHA.
 */
function wpcf7_recaptcha_add_hidden_fields( $fields ) {
	$service = WPCF7_RECAPTCHA::get_instance();

	if ( ! $service->is_active() ) {
		return $fields;
	}

	return array_merge( $fields, array(
		'_wpcf7_recaptcha_response' => '',
	) );
}


add_filter( 'wpcf7_spam', 'wpcf7_recaptcha_verify_response', 9, 2 );

/**
 * Verifies reCAPTCHA token on the server side.
 */
function wpcf7_recaptcha_verify_response( $spam, $submission ) {
	if ( $spam ) {
		return $spam;
	}

	$service = WPCF7_RECAPTCHA::get_instance();

	if ( ! $service->is_active() ) {
		return $spam;
	}

	$token = wpcf7_superglobal_post( '_wpcf7_recaptcha_response' );

	if ( $service->verify( $token ) ) { // Human
		$spam = false;
	} else { // Bot
		$spam = true;

		if ( '' === $token ) {
			$submission->add_spam_log( array(
				'agent' => 'recaptcha',
				'reason' => __( 'reCAPTCHA response token is empty.', 'contact-form-7' ),
			) );
		} else {
			$submission->add_spam_log( array(
				'agent' => 'recaptcha',
				'reason' => sprintf(
					/* translators: 1: value of reCAPTCHA score 2: value of reCAPTCHA threshold */
					__( 'reCAPTCHA score (%1$.2f) is lower than the threshold (%2$.2f).', 'contact-form-7' ),
					$service->get_last_score(),
					$service->get_threshold()
				),
			) );
		}
	}

	return $spam;
}


add_action( 'wpcf7_init', 'wpcf7_recaptcha_add_form_tag_recaptcha', 10, 0 );

/**
 * Registers form-tag types for reCAPTCHA.
 */
function wpcf7_recaptcha_add_form_tag_recaptcha() {
	$service = WPCF7_RECAPTCHA::get_instance();

	if ( ! $service->is_active() ) {
		return;
	}

	wpcf7_add_form_tag( 'recaptcha',
		'__return_empty_string', // no output
		array( 'display-block' => true )
	);
}


add_action( 'wpcf7_upgrade', 'wpcf7_upgrade_recaptcha_v2_v3', 10, 2 );

/**
 * Adds warnings for users upgrading from reCAPTCHA v2 to v3.
 */
function wpcf7_upgrade_recaptcha_v2_v3( $new_ver, $old_ver ) {
	if ( version_compare( '5.1-dev', $old_ver, '<=' ) ) {
		return;
	}

	$service = WPCF7_RECAPTCHA::get_instance();

	if ( ! $service->is_active() or $service->get_global_sitekey() ) {
		return;
	}

	// Maybe v2 keys are used now. Warning necessary.
	WPCF7::update_option( 'recaptcha_v2_v3_warning', true );
	WPCF7::update_option( 'recaptcha', null );
}


add_action( 'wpcf7_admin_menu', 'wpcf7_admin_init_recaptcha_v2_v3', 10, 0 );

/**
 * Adds filters and actions for warnings.
 */
function wpcf7_admin_init_recaptcha_v2_v3() {
	if ( ! WPCF7::get_option( 'recaptcha_v2_v3_warning' ) ) {
		return;
	}

	add_filter(
		'wpcf7_admin_menu_change_notice',
		'wpcf7_admin_menu_change_notice_recaptcha_v2_v3',
		10, 1
	);

	add_action(
		'wpcf7_admin_warnings',
		'wpcf7_admin_warnings_recaptcha_v2_v3',
		5, 3
	);
}


/**
 * Increments the admin menu counter for the Integration page.
 */
function wpcf7_admin_menu_change_notice_recaptcha_v2_v3( $counts ) {
	$counts['wpcf7-integration'] += 1;
	return $counts;
}


/**
 * Prints warnings on the admin screen.
 */
function wpcf7_admin_warnings_recaptcha_v2_v3( $page, $action, $object ) {
	if ( 'wpcf7-integration' !== $page ) {
		return;
	}

	wp_admin_notice(
		sprintf(
			/* translators: %s: link labeled 'reCAPTCHA (v3)' */
			__( 'API keys for reCAPTCHA v3 are different from those for v2; keys for v2 do not work with the v3 API. You need to register your sites again to get new keys for v3. For details, see %s.', 'contact-form-7' ),
			wpcf7_link(
				__( 'https://contactform7.com/recaptcha/', 'contact-form-7' ),
				__( 'reCAPTCHA (v3)', 'contact-form-7' )
			)
		),
		array( 'type' => 'warning' )
	);
}
