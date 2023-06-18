<?php
/**
 * REST API Options Controller
 *
 * Handles requests to get and update options in the wp_options table.
 */

namespace Automattic\WooCommerce\Admin\API;

defined( 'ABSPATH' ) || exit;

/**
 * Options Controller.
 *
 * @deprecated since 6.2.0
 *
 * @extends WC_REST_Data_Controller
 */
class Options extends \WC_REST_Data_Controller {
	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc-admin';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'options';

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_options' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
				'schema' => array( $this, 'get_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_options' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
				),
				'schema' => array( $this, 'get_item_schema' ),
			)
		);
	}

	/**
	 * Check if a given request has access to get options.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_item_permissions_check( $request ) {
		$params = explode( ',', $request['options'] );

		if ( ! isset( $request['options'] ) || ! is_array( $params ) ) {
			return new \WP_Error( 'woocommerce_rest_cannot_view', __( 'You must supply an array of options.', 'woocommerce' ), 500 );
		}

		foreach ( $params as $option ) {
			if ( ! $this->user_has_permission( $option, $request ) ) {
				return new \WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot view these options.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
			}
		}

		return true;
	}

	/**
	 * Check if the user has permission given an option name.
	 *
	 * @param  string          $option Option name.
	 * @param  WP_REST_Request $request Full details about the request.
	 * @param  bool            $is_update If the request is to update the option.
	 * @return boolean
	 */
	public function user_has_permission( $option, $request, $is_update = false ) {
		$permissions = $this->get_option_permissions( $request );

		if ( isset( $permissions[ $option ] ) ) {
			return $permissions[ $option ];
		}

		wc_deprecated_function( 'Automattic\WooCommerce\Admin\API\Options::' . ( $is_update ? 'update_options' : 'get_options' ), '3.1' );
		return current_user_can( 'manage_options' );
	}

	/**
	 * Check if a given request has access to update options.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function update_item_permissions_check( $request ) {
		$params = $request->get_json_params();

		if ( ! is_array( $params ) ) {
			return new \WP_Error( 'woocommerce_rest_cannot_update', __( 'You must supply an array of options and values.', 'woocommerce' ), 500 );
		}

		foreach ( $params as $option_name => $option_value ) {
			if ( ! $this->user_has_permission( $option_name, $request, true ) ) {
				return new \WP_Error( 'woocommerce_rest_cannot_update', __( 'Sorry, you cannot manage these options.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
			}
		}

		return true;
	}

	/**
	 * Get an array of options and respective permissions for the current user.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return array
	 */
	public function get_option_permissions( $request ) {
		$permissions = self::get_default_option_permissions();
		return apply_filters_deprecated( 'woocommerce_rest_api_option_permissions', array( $permissions, $request ), '3.1.0' );
	}

	/**
	 * Get the default available option permissions.
	 *
	 * @return array
	 */
	public static function get_default_option_permissions() {
		$is_woocommerce_admin    = \Automattic\WooCommerce\Internal\Admin\Homescreen::is_admin_user();
		$woocommerce_permissions = array(
			'woocommerce_setup_jetpack_opted_in',
			'woocommerce_stripe_settings',
			'woocommerce-ppcp-settings',
			'woocommerce_ppcp-gateway_setting',
			'woocommerce_demo_store',
			'woocommerce_demo_store_notice',
			'woocommerce_ces_tracks_queue',
			'woocommerce_navigation_intro_modal_dismissed',
			'woocommerce_shipping_dismissed_timestamp',
			'woocommerce_allow_tracking',
			'woocommerce_task_list_keep_completed',
			'woocommerce_task_list_prompt_shown',
			'woocommerce_default_homepage_layout',
			'woocommerce_setup_jetpack_opted_in',
			'woocommerce_no_sales_tax',
			'woocommerce_calc_taxes',
			'woocommerce_bacs_settings',
			'woocommerce_bacs_accounts',
			'woocommerce_task_list_prompt_shown',
			'woocommerce_settings_shipping_recommendations_hidden',
			'woocommerce_task_list_dismissed_tasks',
			'woocommerce_setting_payments_recommendations_hidden',
			'woocommerce_navigation_favorites_tooltip_hidden',
			'woocommerce_admin_transient_notices_queue',
			'woocommerce_task_list_welcome_modal_dismissed',
			'woocommerce_welcome_from_calypso_modal_dismissed',
			'woocommerce_task_list_hidden',
			'woocommerce_task_list_complete',
			'woocommerce_extended_task_list_hidden',
			'woocommerce_ces_shown_for_actions',
			'woocommerce_clear_ces_tracks_queue_for_page',
			'woocommerce_admin_install_timestamp',
			'woocommerce_task_list_tracked_completed_tasks',
			'woocommerce_show_marketplace_suggestions',
			'woocommerce_task_list_reminder_bar_hidden',
			'wc_connect_options',
			'woocommerce_admin_created_default_shipping_zones',
			'woocommerce_admin_reviewed_default_shipping_zones',
			'woocommerce_admin_reviewed_store_location_settings',
			'woocommerce_ces_product_feedback_shown',
			'woocommerce_marketing_overview_multichannel_banner_dismissed',
			'woocommerce_dimension_unit',
			'woocommerce_weight_unit',
			'woocommerce_ces_product_mvp_ces_action',
			'woocommerce_product_tour_modal_hidden',
			'woocommerce_revenue_report_date_tour_shown',
			'woocommerce_date_type',
		);

		$theme_permissions = array(
			'theme_mods_' . get_stylesheet() => current_user_can( 'edit_theme_options' ),
			'stylesheet'                     => current_user_can( 'edit_theme_options' ),
		);

		return array_merge(
			array_fill_keys( $theme_permissions, current_user_can( 'edit_theme_options' ) ),
			array_fill_keys( $woocommerce_permissions, $is_woocommerce_admin )
		);
	}

	/**
	 * Gets an array of options and respective values.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return array Options object with option values.
	 */
	public function get_options( $request ) {
		$params  = explode( ',', $request['options'] );
		$options = array();

		if ( ! is_array( $params ) ) {
			return array();
		}

		foreach ( $params as $option ) {
			$options[ $option ] = get_option( $option );
		}

		return $options;
	}

	/**
	 * Updates an array of objects.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return array Options object with a boolean if the option was updated.
	 */
	public function update_options( $request ) {
		$params  = $request->get_json_params();
		$updated = array();

		if ( ! is_array( $params ) ) {
			return array();
		}

		foreach ( $params as $key => $value ) {
			$updated[ $key ] = update_option( $key, $value );
		}

		return $updated;
	}

	/**
	 * Get the schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'options',
			'type'       => 'object',
			'properties' => array(
				'options' => array(
					'type'        => 'array',
					'description' => __( 'Array of options with associated values.', 'woocommerce' ),
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}
}
