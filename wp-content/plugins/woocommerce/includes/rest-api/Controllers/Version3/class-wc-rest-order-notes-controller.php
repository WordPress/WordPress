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
 * @extends WC_REST_Order_Notes_V2_Controller
 */
class WC_REST_Order_Notes_Controller extends WC_REST_Order_Notes_V2_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v3';

	/**
	 * Prepare a single order note output for response.
	 *
	 * @param WP_Comment      $note    Order note object.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response $response Response data.
	 */
	public function prepare_item_for_response( $note, $request ) {
		$data = array(
			'id'               => (int) $note->comment_ID,
			'author'           => __( 'woocommerce', 'woocommerce' ) === $note->comment_author ? 'system' : $note->comment_author,
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
	 * Create a single order note.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function create_item( $request ) {
		if ( ! empty( $request['id'] ) ) {
			/* translators: %s: post type */
			return new WP_Error( "woocommerce_rest_{$this->post_type}_exists", sprintf( __( 'Cannot create existing %s.', 'woocommerce' ), $this->post_type ), array( 'status' => 400 ) );
		}

		$order = wc_get_order( (int) $request['order_id'] );

		if ( ! $order || $this->post_type !== $order->get_type() ) {
			return new WP_Error( 'woocommerce_rest_order_invalid_id', __( 'Invalid order ID.', 'woocommerce' ), array( 'status' => 404 ) );
		}

		// Create the note.
		$note_id = $order->add_order_note( $request['note'], $request['customer_note'], $request['added_by_user'] );

		if ( ! $note_id ) {
			return new WP_Error( 'woocommerce_api_cannot_create_order_note', __( 'Cannot create order note, please try again.', 'woocommerce' ), array( 'status' => 500 ) );
		}

		$note = get_comment( $note_id );
		$this->update_additional_fields_for_object( $note, $request );

		/**
		 * Fires after a order note is created or updated via the REST API.
		 *
		 * @param WP_Comment      $note      New order note object.
		 * @param WP_REST_Request $request   Request object.
		 * @param boolean         $creating  True when creating item, false when updating.
		 */
		do_action( 'woocommerce_rest_insert_order_note', $note, $request, true );

		$request->set_param( 'context', 'edit' );
		$response = $this->prepare_item_for_response( $note, $request );
		$response = rest_ensure_response( $response );
		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '/%s/%s/%d', $this->namespace, str_replace( '(?P<order_id>[\d]+)', $order->get_id(), $this->rest_base ), $note_id ) ) );

		return $response;
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
				'author'           => array(
					'description' => __( 'Order note author.', 'woocommerce' ),
					'type'        => 'string',
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
				'added_by_user'    => array(
					'description' => __( 'If true, this note will be attributed to the current user. If false, the note will be attributed to the system.', 'woocommerce' ),
					'type'        => 'boolean',
					'default'     => false,
					'context'     => array( 'edit' ),
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}
}
