<?php
/**
 * REST API Coupons Controller
 *
 * Handles requests to /coupons/*
 */

namespace Automattic\WooCommerce\Admin\API;

defined( 'ABSPATH' ) || exit;

/**
 * Coupons controller.
 *
 * @internal
 * @extends WC_REST_Coupons_Controller
 */
class Coupons extends \WC_REST_Coupons_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc-analytics';

	/**
	 * Get the query params for collections.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params           = parent::get_collection_params();
		$params['search'] = array(
			'description'       => __( 'Limit results to coupons with codes matching a given string.', 'woocommerce' ),
			'type'              => 'string',
			'validate_callback' => 'rest_validate_request_arg',
		);
		return $params;
	}


	/**
	 * Add coupon code searching to the WC API.
	 *
	 * @param WP_REST_Request $request Request data.
	 * @return array
	 */
	protected function prepare_objects_query( $request ) {
		$args = parent::prepare_objects_query( $request );

		if ( ! empty( $request['search'] ) ) {
			$args['search'] = $request['search'];
			$args['s']      = false;
		}

		return $args;
	}

	/**
	 * Get a collection of posts and add the code search option to WP_Query.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		add_filter( 'posts_where', array( __CLASS__, 'add_wp_query_search_code_filter' ), 10, 2 );
		$response = parent::get_items( $request );
		remove_filter( 'posts_where', array( __CLASS__, 'add_wp_query_search_code_filter' ), 10 );
		return $response;
	}

	/**
	 * Add code searching to the WP Query
	 *
	 * @internal
	 * @param string $where Where clause used to search posts.
	 * @param object $wp_query WP_Query object.
	 * @return string
	 */
	public static function add_wp_query_search_code_filter( $where, $wp_query ) {
		global $wpdb;

		$search = $wp_query->get( 'search' );
		if ( $search ) {
			$code_like = '%' . $wpdb->esc_like( $search ) . '%';
			$where    .= $wpdb->prepare( "AND {$wpdb->posts}.post_title LIKE %s", $code_like );
		}

		return $where;
	}
}
