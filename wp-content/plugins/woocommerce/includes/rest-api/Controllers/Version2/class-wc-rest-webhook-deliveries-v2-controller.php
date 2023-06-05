<?php
/**
 * REST API Webhooks controller
 *
 * Handles requests to the /webhooks/<webhook_id>/deliveries endpoint.
 *
 * @package WooCommerce\RestApi
 * @since   2.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * REST API Webhook Deliveries controller class.
 *
 * @deprecated 3.3.0 Webhooks deliveries logs now uses logging system.
 * @package WooCommerce\RestApi
 * @extends WC_REST_Webhook_Deliveries_V1_Controller
 */
class WC_REST_Webhook_Deliveries_V2_Controller extends WC_REST_Webhook_Deliveries_V1_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v2';

	/**
	 * Prepare a single webhook delivery output for response.
	 *
	 * @param  stdClass        $log Delivery log object.
	 * @param  WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function prepare_item_for_response( $log, $request ) {
		$data = (array) $log;

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		$response->add_links( $this->prepare_links( $log ) );

		/**
		 * Filter webhook delivery object returned from the REST API.
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param stdClass         $log      Delivery log object used to create response.
		 * @param WP_REST_Request  $request  Request object.
		 */
		return apply_filters( 'woocommerce_rest_prepare_webhook_delivery', $response, $log, $request );
	}

	/**
	 * Get the Webhook's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'webhook_delivery',
			'type'       => 'object',
			'properties' => array(
				'id'               => array(
					'description' => __( 'Unique identifier for the resource.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'duration'         => array(
					'description' => __( 'The delivery duration, in seconds.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'summary'          => array(
					'description' => __( 'A friendly summary of the response including the HTTP response code, message, and body.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'request_url'      => array(
					'description' => __( 'The URL where the webhook was delivered.', 'woocommerce' ),
					'type'        => 'string',
					'format'      => 'uri',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'request_headers'  => array(
					'description' => __( 'Request headers.', 'woocommerce' ),
					'type'        => 'array',
					'context'     => array( 'view' ),
					'readonly'    => true,
					'items'       => array(
						'type' => 'string',
					),
				),
				'request_body'     => array(
					'description' => __( 'Request body.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'response_code'    => array(
					'description' => __( 'The HTTP response code from the receiving server.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'response_message' => array(
					'description' => __( 'The HTTP response message from the receiving server.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'response_headers' => array(
					'description' => __( 'Array of the response headers from the receiving server.', 'woocommerce' ),
					'type'        => 'array',
					'context'     => array( 'view' ),
					'readonly'    => true,
					'items'       => array(
						'type' => 'string',
					),
				),
				'response_body'    => array(
					'description' => __( 'The response body from the receiving server.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'date_created'     => array(
					'description' => __( "The date the webhook delivery was logged, in the site's timezone.", 'woocommerce' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'date_created_gmt' => array(
					'description' => __( 'The date the webhook delivery was logged, as GMT.', 'woocommerce' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}
}
