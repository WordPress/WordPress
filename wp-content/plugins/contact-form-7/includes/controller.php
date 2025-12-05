<?php
/**
 * Controller for front-end requests, scripts, and styles
 */


add_action(
	'parse_request',
	'wpcf7_control_init',
	20, 0
);

/**
 * Handles a submission in non-Ajax mode.
 */
function wpcf7_control_init() {
	if ( WPCF7_Submission::is_restful() ) {
		return;
	}

	if (
		$id = (int) wpcf7_superglobal_post( '_wpcf7' ) and
		$contact_form = wpcf7_contact_form( $id )
	) {
		$contact_form->submit();
	}
}


/**
 * Registers main scripts and styles.
 */
add_action(
	'wp_enqueue_scripts',
	static function () {
		$assets = include wpcf7_plugin_path( 'includes/js/index.asset.php' );

		$assets = wp_parse_args( $assets, array(
			'dependencies' => array(),
			'version' => WPCF7_VERSION,
		) );

		wp_register_script(
			'contact-form-7',
			wpcf7_plugin_url( 'includes/js/index.js' ),
			array_merge(
				$assets['dependencies'],
				array( 'swv' )
			),
			$assets['version'],
			array( 'in_footer' => true )
		);

		wp_set_script_translations( 'contact-form-7', 'contact-form-7' );

		wp_register_script(
			'contact-form-7-html5-fallback',
			wpcf7_plugin_url( 'includes/js/html5-fallback.js' ),
			array( 'jquery-ui-datepicker' ),
			WPCF7_VERSION,
			array( 'in_footer' => true )
		);

		if ( wpcf7_load_js() ) {
			wpcf7_enqueue_scripts();
		}

		wp_register_style(
			'contact-form-7',
			wpcf7_plugin_url( 'includes/css/styles.css' ),
			array(),
			WPCF7_VERSION,
			'all'
		);

		wp_register_style(
			'contact-form-7-rtl',
			wpcf7_plugin_url( 'includes/css/styles-rtl.css' ),
			array( 'contact-form-7' ),
			WPCF7_VERSION,
			'all'
		);

		wp_register_style(
			'jquery-ui-smoothness',
			wpcf7_plugin_url(
				'includes/js/jquery-ui/themes/smoothness/jquery-ui.min.css'
			),
			array(),
			'1.12.1',
			'screen'
		);

		if ( wpcf7_load_css() ) {
			wpcf7_enqueue_styles();
		}
	},
	10, 0
);


/**
 * Enqueues scripts.
 */
function wpcf7_enqueue_scripts() {
	wp_enqueue_script( 'contact-form-7' );

	$wpcf7_obj = array(
		'api' => array(
			'root' => sanitize_url( get_rest_url() ),
			'namespace' => 'contact-form-7/v1',
		),
	);

	if ( defined( 'WP_CACHE' ) and WP_CACHE ) {
		$wpcf7_obj = array_merge( $wpcf7_obj, array(
			'cached' => 1,
		) );
	}

	wp_add_inline_script( 'contact-form-7',
		sprintf(
			'var wpcf7 = %s;',
			wp_json_encode( $wpcf7_obj, JSON_PRETTY_PRINT )
		),
		'before'
	);

	do_action( 'wpcf7_enqueue_scripts' );
}


/**
 * Returns true if the main script is enqueued.
 */
function wpcf7_script_is() {
	return wp_script_is( 'contact-form-7' );
}


/**
 * Enqueues styles.
 */
function wpcf7_enqueue_styles() {
	wp_enqueue_style( 'contact-form-7' );

	if ( wpcf7_is_rtl() ) {
		wp_enqueue_style( 'contact-form-7-rtl' );
	}

	do_action( 'wpcf7_enqueue_styles' );
}


/**
 * Returns true if the main stylesheet is enqueued.
 */
function wpcf7_style_is() {
	return wp_style_is( 'contact-form-7' );
}


add_action(
	'wp_enqueue_scripts',
	'wpcf7_html5_fallback',
	20, 0
);

/**
 * Enqueues scripts and styles for the HTML5 fallback.
 */
function wpcf7_html5_fallback() {
	if ( ! wpcf7_support_html5_fallback() ) {
		return;
	}

	if ( wpcf7_script_is() ) {
		wp_enqueue_script( 'contact-form-7-html5-fallback' );
	}

	if ( wpcf7_style_is() ) {
		wp_enqueue_style( 'jquery-ui-smoothness' );
	}
}
