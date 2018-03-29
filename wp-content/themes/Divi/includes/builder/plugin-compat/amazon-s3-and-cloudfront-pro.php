<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for Amazon S3 Offload
 *
 * @since 3.0.49
 *
 * @link https://wordpress.org/plugins/amazon-s3-and-cloudfront/
 */
class ET_Builder_Plugin_Compat_WP_Offload_S3_Pro extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor
	 */
	function __construct() {
		$this->plugin_id = 'amazon-s3-and-cloudfront-pro/amazon-s3-and-cloudfront-pro.php';

		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress
	 *
	 * Latest plugin version: 1.1.6
	 *
	 * @return void
	 */
	function init_hooks() {
		// Bail if there's no version found
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		// Up to: latest theme version
		add_action( 'et_fb_ajax_save_verification_result', array( $this, 'override_fb_ajax_save_verification' )  );
	}

	/**
	 * @param bool $verification
	 *
	 * @return bool
	 */
	function override_fb_ajax_save_verification( $verification ) {
		return true;
	}
}

new ET_Builder_Plugin_Compat_WP_Offload_S3_Pro();
