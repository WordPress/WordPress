<?php
/**
 * WooCommerce API Coupons Class
 *
 * Handles requests to the /coupons endpoint
 *
 * @author      WooThemes
 * @category    API
 * @package     WooCommerce/API
 * @since       2.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class WC_API_Coupons extends WC_API_Resource {

	/** @var string $base the route base */
	protected $base = '/coupons';

	/**
	 * Register the routes for this class
	 *
	 * GET /coupons
	 * GET /coupons/count
	 * GET /coupons/<id>
	 *
	 * @since 2.1
	 * @param array $routes
	 * @return array
	 */
	public function register_routes( $routes ) {

		# GET /coupons
		$routes[ $this->base ] = array(
			array( array( $this, 'get_coupons' ),     WC_API_Server::READABLE ),
		);

		# GET /coupons/count
		$routes[ $this->base . '/count'] = array(
			array( array( $this, 'get_coupons_count' ), WC_API_Server::READABLE ),
		);

		# GET /coupons/<id>
		$routes[ $this->base . '/(?P<id>\d+)' ] = array(
			array( array( $this, 'get_coupon' ),  WC_API_Server::READABLE ),
		);

		# GET /coupons/code/<code>, note that coupon codes can contain spaces, dashes and underscores
		$routes[ $this->base . '/code/(?P<code>\w[\w\s\-]*)' ] = array(
			array( array( $this, 'get_coupon_by_code' ), WC_API_Server::READABLE ),
		);

		return $routes;
	}

	/**
	 * Get all coupons
	 *
	 * @since 2.1
	 * @param string $fields
	 * @param array $filter
	 * @param int $page
	 * @return array
	 */
	public function get_coupons( $fields = null, $filter = array(), $page = 1 ) {

		$filter['page'] = $page;

		$query = $this->query_coupons( $filter );

		$coupons = array();

		foreach( $query->posts as $coupon_id ) {

			if ( ! $this->is_readable( $coupon_id ) )
				continue;

			$coupons[] = current( $this->get_coupon( $coupon_id, $fields ) );
		}

		$this->server->add_pagination_headers( $query );

		return array( 'coupons' => $coupons );
	}

	/**
	 * Get the coupon for the given ID
	 *
	 * @since 2.1
	 * @param int $id the coupon ID
	 * @param string $fields fields to include in response
	 * @return array|WP_Error
	 */
	public function get_coupon( $id, $fields = null ) {
		global $wpdb;

		$id = $this->validate_request( $id, 'shop_coupon', 'read' );

		if ( is_wp_error( $id ) )
			return $id;

		// get the coupon code
		$code = $wpdb->get_var( $wpdb->prepare( "SELECT post_title FROM $wpdb->posts WHERE id = %s AND post_type = 'shop_coupon' AND post_status = 'publish'", $id ) );

		if ( is_null( $code ) )
			return new WP_Error( 'woocommerce_api_invalid_coupon_id', __( 'Invalid coupon ID', 'woocommerce' ), array( 'status' => 404 ) );

		$coupon = new WC_Coupon( $code );

		$coupon_post = get_post( $coupon->id );

		$coupon_data = array(
			'id'                           => $coupon->id,
			'code'                         => $coupon->code,
			'type'                         => $coupon->type,
			'created_at'                   => $this->server->format_datetime( $coupon_post->post_date_gmt ),
			'updated_at'                   => $this->server->format_datetime( $coupon_post->post_modified_gmt ),
			'amount'                       => wc_format_decimal( $coupon->amount, 2 ),
			'individual_use'               => ( 'yes' === $coupon->individual_use ),
			'product_ids'                  => array_map( 'absint', (array) $coupon->product_ids ),
			'exclude_product_ids'          => array_map( 'absint', (array) $coupon->exclude_product_ids ),
			'usage_limit'                  => ( ! empty( $coupon->usage_limit ) ) ? $coupon->usage_limit : null,
			'usage_limit_per_user'         => ( ! empty( $coupon->usage_limit_per_user ) ) ? $coupon->usage_limit_per_user : null,
			'limit_usage_to_x_items'       => (int) $coupon->limit_usage_to_x_items,
			'usage_count'                  => (int) $coupon->usage_count,
			'expiry_date'                  => $this->server->format_datetime( $coupon->expiry_date ),
			'apply_before_tax'             => $coupon->apply_before_tax(),
			'enable_free_shipping'         => $coupon->enable_free_shipping(),
			'product_category_ids'         => array_map( 'absint', (array) $coupon->product_categories ),
			'exclude_product_category_ids' => array_map( 'absint', (array) $coupon->exclude_product_categories ),
			'exclude_sale_items'           => $coupon->exclude_sale_items(),
			'minimum_amount'               => wc_format_decimal( $coupon->minimum_amount, 2 ),
			'customer_emails'              => $coupon->customer_email,
		);

		return array( 'coupon' => apply_filters( 'woocommerce_api_coupon_response', $coupon_data, $coupon, $fields, $this->server ) );
	}

	/**
	 * Get the total number of coupons
	 *
	 * @since 2.1
	 * @param array $filter
	 * @return array
	 */
	public function get_coupons_count( $filter = array() ) {

		$query = $this->query_coupons( $filter );

		if ( ! current_user_can( 'read_private_shop_coupons' ) )
			return new WP_Error( 'woocommerce_api_user_cannot_read_coupons_count', __( 'You do not have permission to read the coupons count', 'woocommerce' ), array( 'status' => 401 ) );

		return array( 'count' => (int) $query->found_posts );
	}

	/**
	 * Get the coupon for the given code
	 *
	 * @since 2.1
	 * @param string $code the coupon code
	 * @param string $fields fields to include in response
	 * @return int|WP_Error
	 */
	public function get_coupon_by_code( $code, $fields = null ) {
		global $wpdb;

		$id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $wpdb->posts WHERE post_title = %s AND post_type = 'shop_coupon' AND post_status = 'publish'", $code ) );

		if ( is_null( $id ) )
			return new WP_Error( 'woocommerce_api_invalid_coupon_code', __( 'Invalid coupon code', 'woocommerce' ), array( 'status' => 404 ) );

		return $this->get_coupon( $id, $fields );
	}

	/**
	 * Create a coupon
	 *
	 * @TODO implement in 2.2
	 * @param array $data
	 * @return array
	 */
	public function create_coupon( $data ) {

		return array();
	}

	/**
	 * Edit a coupon
	 *
	 * @TODO implement in 2.2
	 * @param int $id the coupon ID
	 * @param array $data
	 * @return array
	 */
	public function edit_coupon( $id, $data ) {

		$id = $this->validate_request( $id, 'shop_coupon', 'edit' );

		if ( is_wp_error( $id ) )
			return $id;

		return $this->get_coupon( $id );
	}

	/**
	 * Delete a coupon
	 *
	 * @TODO enable along with PUT/POST in 2.2
	 * @param int $id the coupon ID
	 * @param bool $force true to permanently delete coupon, false to move to trash
	 * @return array
	 */
	public function delete_coupon( $id, $force = false ) {

		$id = $this->validate_request( $id, 'shop_coupon', 'delete' );

		if ( is_wp_error( $id ) )
			return $id;

		return $this->delete( $id, 'shop_coupon', ( 'true' === $force ) );
	}

	/**
	 * Helper method to get coupon post objects
	 *
	 * @since 2.1
	 * @param array $args request arguments for filtering query
	 * @return WP_Query
	 */
	private function query_coupons( $args ) {

		// set base query arguments
		$query_args = array(
			'fields'      => 'ids',
			'post_type'   => 'shop_coupon',
			'post_status' => 'publish',
		);

		$query_args = $this->merge_query_args( $query_args, $args );

		return new WP_Query( $query_args );
	}

}
