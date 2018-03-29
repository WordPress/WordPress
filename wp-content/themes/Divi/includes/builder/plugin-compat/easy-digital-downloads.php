<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for easy-digital-downloads
 * @since 0.7 (builder version)
 * @link https://easydigitaldownloads.com
 */
class ET_Builder_Plugin_Compat_Easy_Digital_Downloads extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor
	 */
	function __construct() {
		$this->plugin_id = "easy-digital-downloads/easy-digital-downloads.php";
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress
	 * Note: once this issue is fixed in future version, run version_compare() to limit the scope of the hooked fix
	 * Latest plugin version: 2.6.17
	 * @return void
	 */
	function init_hooks() {
		// Bail if there's no version found
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		// Up to: latest theme version
		if ( ! is_admin() ) {
			add_filter( 'edd_purchase_link_defaults',   array( $this, 'purchase_link_defaults' ) );
			add_filter( 'shortcode_atts_purchase_link', array( $this, 'purchase_link_defaults' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'add_compatibility_scripts' ), 15 );
		} else {
			add_filter( 'edd_checkout_button_purchase', array( $this, 'modify_edd_checkout_button_purchase' ) );
		}
	}

	/**
	 * Appended et_pb_button for various EDD button so it matches Divi styled button
	 * @param  array initial link configuration
	 * @return array moodified link configuration
	 */
	function purchase_link_defaults ( $args ) {
		if ( isset( $args['class'] ) ) {
			$args['class'] = $args['class'] . ' et_pb_button';
		}

		return $args;
	}

	/**
	 * Addded et_pb_button class for checkout button which has no attribute filter
	 * @param string of HTML of the button
	 * @return string of modified HTML of the button
	 */
	function modify_edd_checkout_button_purchase( $button ) {
		$button = str_replace( 'edd-submit', 'edd-submit et_pb_button', $button );

		return $button;
	}

	/**
	 * Added additional styling & scripts for EDD on Divi
	 * @return void
	 */
	function add_compatibility_scripts() {
		// Normalize UI for Divi Builder
		if ( et_is_builder_plugin_active() ) {
			wp_add_inline_style( 'et-builder-modules-style', "
				.et_divi_builder #et_builder_outer_content .edd_download_inner {
					padding: 0 8px 8px;
					margin: 0 0 10px;
				}

				.et_divi_builder #et_builder_outer_content .edd_download_excerpt p {
					margin-bottom: 25px;
				}

				.et_divi_builder #et_builder_outer_content .edd_purchase_submit_wrapper {
					margin-top: 15px;
					margin-bottom: 35px;
				}

				.et_divi_builder #et_builder_outer_content ul.edd-cart {
					border: 1px solid #eee;
					margin-top: 0.8em;
				}

				.et_divi_builder #et_builder_outer_content ul.edd-cart li {
					padding: 0.8em 1.387em;
					border-bottom: 1px solid #eee;
				}

				.et_divi_builder #et_builder_outer_content ul.edd-cart li:last-child {
					border-bottom: none;
				}

				.et_divi_builder #et_builder_outer_content ul.edd-cart .edd-cart-item span {
					padding: 0 3px;
				}

				.et_divi_builder #et_builder_outer_content ul.edd-cart .edd-cart-item a {
					text-decoration: underline !important;
				}

				.et_divi_builder #et_builder_outer_content .edd_cart_item_image {
					margin-right: 10px;
					display: inline-block;
					vertical-align: middle;
				}

				.et_divi_builder #et_builder_outer_content .edd-cart-meta.edd_subtotal,
				.et_divi_builder #et_builder_outer_content .edd-cart-meta.edd_total {
					background: #f9f9f9;
				}

				.et_divi_builder #et_builder_outer_content .cart_item.edd_checkout {
					padding: 1.387em;
				}

				.et_divi_builder #et_builder_outer_content .et_pb_module a.edd_cart_remove_item_btn {
					text-decoration: underline !important;
				}

				.et_divi_builder #et_builder_outer_content #edd_profile_editor_form .edd-select,
				.et_divi_builder #et_builder_outer_content #edd_profile_editor_form .edd-input {
					margin-bottom: 5px;
				}

				.et_divi_builder #et_builder_outer_content #edd_final_total_wrap {
					margin-bottom: 20px;
				}

				.et_divi_builder #et_builder_outer_content .et_pb_module .et_pb_button {
					border-bottom-style: solid;
					border-bottom-width: 2px;
				}

				.et_divi_builder #et_builder_outer_content .et_pb_module input.et_pb_button:hover {
					padding-right: 1em;
				}
			" );
		}

		// Re-styled button with Divi's button UI using javascript due to lack of filter
		wp_add_inline_script( 'et-builder-modules-script', "
			(function($){
				$(document).ready( function(){
					$('.cart_item.edd_checkout a, input[name=\"edd_register_submit\"], .edd_submit').addClass( 'et_pb_button' ).attr('style', 'padding-right: 1em;');
				});
			})(jQuery)
		");
	}
}
new ET_Builder_Plugin_Compat_Easy_Digital_Downloads;
