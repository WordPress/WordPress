<?php
/**
 * REST API Order Notes controller
 *
 * Handles requests to the /orders/<order_id>/notes endpoint.
 *
 * @package WooCommerce\RestApi
 * @since   2.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * REST API Order Notes controller class.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_Order_Notes_V1_Controller
 */
class WC_REST_Order_Notes_V2_Controller extends WC_REST_Order_Notes_V1_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v2';

	/**
	 * Get order notes from an order.
	 *
	 * @param WP_REST_Request $request Request data.
	 *
	 * @return array|WP_Error
	 */
	public function get_items( $request ) {
		$order = wc_get_order( (int) $request['order_id'] );

		if ( ! $order || $this->post_type !== $order->get_type() ) {
			return new WP_Error( "woocommerce_rest_{$this->post_type}_invalid_id", __( 'Invalid order ID.', 'woocommerce' ), array( 'status' => 404 ) );
		}

		$args = array(
			'post_id' => $order->get_id(),
			'approve' => 'approve',
			'type'    => 'order_note',
		);

		// Allow filter by order note type.
		if ( 'customer' === $request['type'] ) {
			$args['meta_query'] = array( // WPCS: slow query ok.
				array(
					'key'     => 'is_customer_note',
					'value'   => 1,
					'compare' => '=',
				),
			);
		} elseif ( 'internal' === $request['type'] ) {
			$args['meta_query'] = array( // WPCS: slow query ok.
				array(
					'key'     => 'is_customer_note',
					'compare' => 'NOT EXISTS',
				),
			);
		}

		remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );

		$notes = get_comments( $args );

		add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );

		$data = array();
		foreach ( $notes as $note ) {
			$order_note = $this->prepare_item_for_response( $note, $request );
			$order_note = $this->prepare_response_for_collection( $order_note );
			$data[]     = $order_note;
		}

		return rest_ensure_response( $data );
	}

	/**
	 * Prepare a single order note output for response.
	 *
	 * @param WP_Comment      $note Order note object.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response $response Response data.
	 */
	public function prepare_item_for_response( $note, $request ) {
		$data = array(
			'id'               => (int) $note->comment_ID,
			'date_created'     => wc_rest_prepare_date_response( $note->comment_date ),
			'date_created_gmt' => wc_rest_prepare_date_response( $note->comment_date_gmt ),
			'note'             => $note->comment_content,
			'customer_note'    => (bool) get_comment_meta( $note->comment_ID, 'is_customer_note', true ),
		);

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		$response->add_links( $this->prepare_links( $note ) );

		/**
		 * Filter order note object returned from the REST API.
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param WP_Comment       $note     Order note object used to create response.
		 * @param WP_REST_Request  $request  Request object.
		 */
		return apply_filters( 'woocommerce_rest_prepare_order_note', $response, $note, $request );
	}

	/**
	 * Get the Order Notes schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'order_note',
			'type'       => 'object',
			'properties' => array(
				'id'               => array(
					'description' => __( 'Unique identifier for the resource.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'date_created'     => array(
					'description' => __( "The date the order note was created, in the site's timezone.", 'woocommerce' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'date_created_gmt' => array(
					'description' => __( 'The date the order note was created, as GMT.', 'woocommerce' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'note'             => array(
					'description' => __( 'Order note content.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'customer_note'    => array(
					'description' => __( 'If true, the note will be shown to customers and they will be notified. If false, the note will be for admin reference only.', 'woocommerce' ),
					'type'        => 'boolean',
					'default'     => false,
					'context'     => array( 'view', 'edit' ),
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Get the query params for collections.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params            = array();
		$params['context'] = $this->get_context_param( array( 'default' => 'view' ) );
		$params['type']    = array(
			'default'           => 'any',
			'description'       => __( 'Limit result to customers or internal notes.', 'woocommerce' ),
			'type'              => 'string',
			'enum'              => array( 'any', 'customer', 'internal' ),
			'sanitize_callback' => 'sanitize_key',
			'validate_callback' => 'rest_validate_request_arg',
		);

		return $params;
	}
}
