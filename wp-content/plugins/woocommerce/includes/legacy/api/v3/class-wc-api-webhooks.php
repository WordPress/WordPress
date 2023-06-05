<?php
/**
 * WooCommerce API Webhooks class
 *
 * Handles requests to the /webhooks endpoint
 *
 * @author   WooThemes
 * @category API
 * @package  WooCommerce\RestApi
 * @since    2.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_API_Webhooks extends WC_API_Resource {

	/** @var string $base the route base */
	protected $base = '/webhooks';

	/**
	 * Register the routes for this class
	 *
	 * @since 2.2
	 * @param array $routes
	 * @return array
	 */
	public function register_routes( $routes ) {

		# GET|POST /webhooks
		$routes[ $this->base ] = array(
			array( array( $this, 'get_webhooks' ),     WC_API_Server::READABLE ),
			array( array( $this, 'create_webhook' ),   WC_API_Server::CREATABLE | WC_API_Server::ACCEPT_DATA ),
		);

		# GET /webhooks/count
		$routes[ $this->base . '/count' ] = array(
			array( array( $this, 'get_webhooks_count' ), WC_API_Server::READABLE ),
		);

		# GET|PUT|DELETE /webhooks/<id>
		$routes[ $this->base . '/(?P<id>\d+)' ] = array(
			array( array( $this, 'get_webhook' ),  WC_API_Server::READABLE ),
			array( array( $this, 'edit_webhook' ), WC_API_Server::EDITABLE | WC_API_Server::ACCEPT_DATA ),
			array( array( $this, 'delete_webhook' ), WC_API_Server::DELETABLE ),
		);

		# GET /webhooks/<id>/deliveries
		$routes[ $this->base . '/(?P<webhook_id>\d+)/deliveries' ] = array(
			array( array( $this, 'get_webhook_deliveries' ), WC_API_Server::READABLE ),
		);

		# GET /webhooks/<webhook_id>/deliveries/<id>
		$routes[ $this->base . '/(?P<webhook_id>\d+)/deliveries/(?P<id>\d+)' ] = array(
			array( array( $this, 'get_webhook_delivery' ), WC_API_Server::READABLE ),
		);

		return $routes;
	}

	/**
	 * Get all webhooks
	 *
	 * @since 2.2
	 *
	 * @param array $fields
	 * @param array $filter
	 * @param string $status
	 * @param int $page
	 *
	 * @return array
	 */
	public function get_webhooks( $fields = null, $filter = array(), $status = null, $page = 1 ) {

		if ( ! empty( $status ) ) {
			$filter['status'] = $status;
		}

		$filter['page'] = $page;

		$query = $this->query_webhooks( $filter );

		$webhooks = array();

		foreach ( $query['results'] as $webhook_id ) {
			$webhooks[] = current( $this->get_webhook( $webhook_id, $fields ) );
		}

		$this->server->add_pagination_headers( $query['headers'] );

		return array( 'webhooks' => $webhooks );
	}

	/**
	 * Get the webhook for the given ID
	 *
	 * @since 2.2
	 * @param int $id webhook ID
	 * @param array $fields
	 * @return array|WP_Error
	 */
	public function get_webhook( $id, $fields = null ) {

		// ensure webhook ID is valid & user has permission to read
		$id = $this->validate_request( $id, 'shop_webhook', 'read' );

		if ( is_wp_error( $id ) ) {
			return $id;
		}

		$webhook = wc_get_webhook( $id );

		$webhook_data = array(
			'id'           => $webhook->get_id(),
			'name'         => $webhook->get_name(),
			'status'       => $webhook->get_status(),
			'topic'        => $webhook->get_topic(),
			'resource'     => $webhook->get_resource(),
			'event'        => $webhook->get_event(),
			'hooks'        => $webhook->get_hooks(),
			'delivery_url' => $webhook->get_delivery_url(),
			'created_at'   => $this->server->format_datetime( $webhook->get_date_created() ? $webhook->get_date_created()->getTimestamp() : 0, false, false ), // API gives UTC times.
			'updated_at'   => $this->server->format_datetime( $webhook->get_date_modified() ? $webhook->get_date_modified()->getTimestamp() : 0, false, false ), // API gives UTC times.
		);

		return array( 'webhook' => apply_filters( 'woocommerce_api_webhook_response', $webhook_data, $webhook, $fields, $this ) );
	}

	/**
	 * Get the total number of webhooks
	 *
	 * @since 2.2
	 *
	 * @param string $status
	 * @param array $filter
	 *
	 * @return array|WP_Error
	 */
	public function get_webhooks_count( $status = null, $filter = array() ) {
		try {
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				throw new WC_API_Exception( 'woocommerce_api_user_cannot_read_webhooks_count', __( 'You do not have permission to read the webhooks count', 'woocommerce' ), 401 );
			}

			if ( ! empty( $status ) ) {
				$filter['status'] = $status;
			}

			$query = $this->query_webhooks( $filter );

			return array( 'count' => $query['headers']->total );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Create an webhook
	 *
	 * @since 2.2
	 *
	 * @param array $data parsed webhook data
	 *
	 * @return array|WP_Error
	 */
	public function create_webhook( $data ) {

		try {
			if ( ! isset( $data['webhook'] ) ) {
				throw new WC_API_Exception( 'woocommerce_api_missing_webhook_data', sprintf( __( 'No %1$s data specified to create %1$s', 'woocommerce' ), 'webhook' ), 400 );
			}

			$data = $data['webhook'];

			// permission check
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				throw new WC_API_Exception( 'woocommerce_api_user_cannot_create_webhooks', __( 'You do not have permission to create webhooks.', 'woocommerce' ), 401 );
			}

			$data = apply_filters( 'woocommerce_api_create_webhook_data', $data, $this );

			// validate topic
			if ( empty( $data['topic'] ) || ! wc_is_webhook_valid_topic( strtolower( $data['topic'] ) ) ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_webhook_topic', __( 'Webhook topic is required and must be valid.', 'woocommerce' ), 400 );
			}

			// validate delivery URL
			if ( empty( $data['delivery_url'] ) || ! wc_is_valid_url( $data['delivery_url'] ) ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_webhook_delivery_url', __( 'Webhook delivery URL must be a valid URL starting with http:// or https://', 'woocommerce' ), 400 );
			}

			$webhook_data = apply_filters( 'woocommerce_new_webhook_data', array(
				'post_type'     => 'shop_webhook',
				'post_status'   => 'publish',
				'ping_status'   => 'closed',
				'post_author'   => get_current_user_id(),
				'post_password' => 'webhook_' . wp_generate_password(),
				'post_title'    => ! empty( $data['name'] ) ? $data['name'] : sprintf( __( 'Webhook created on %s', 'woocommerce' ), (new DateTime('now'))->format( _x( 'M d, Y @ h:i A', 'Webhook created on date parsed by DateTime::format', 'woocommerce' ) ) ),
			), $data, $this );

			$webhook = new WC_Webhook();

			$webhook->set_name( $webhook_data['post_title'] );
			$webhook->set_user_id( $webhook_data['post_author'] );
			$webhook->set_status( 'publish' === $webhook_data['post_status'] ? 'active' : 'disabled' );
			$webhook->set_topic( $data['topic'] );
			$webhook->set_delivery_url( $data['delivery_url'] );
			$webhook->set_secret( ! empty( $data['secret'] ) ? $data['secret'] : wp_generate_password( 50, true, true ) );
			$webhook->set_api_version( 'legacy_v3' );
			$webhook->save();

			$webhook->deliver_ping();

			// HTTP 201 Created
			$this->server->send_status( 201 );

			do_action( 'woocommerce_api_create_webhook', $webhook->get_id(), $this );

			return $this->get_webhook( $webhook->get_id() );

		} catch ( WC_API_Exception $e ) {

			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Edit a webhook
	 *
	 * @since 2.2
	 *
	 * @param int $id webhook ID
	 * @param array $data parsed webhook data
	 *
	 * @return array|WP_Error
	 */
	public function edit_webhook( $id, $data ) {

		try {
			if ( ! isset( $data['webhook'] ) ) {
				throw new WC_API_Exception( 'woocommerce_api_missing_webhook_data', sprintf( __( 'No %1$s data specified to edit %1$s', 'woocommerce' ), 'webhook' ), 400 );
			}

			$data = $data['webhook'];

			$id = $this->validate_request( $id, 'shop_webhook', 'edit' );

			if ( is_wp_error( $id ) ) {
				return $id;
			}

			$data = apply_filters( 'woocommerce_api_edit_webhook_data', $data, $id, $this );

			$webhook = wc_get_webhook( $id );

			// update topic
			if ( ! empty( $data['topic'] ) ) {

				if ( wc_is_webhook_valid_topic( strtolower( $data['topic'] ) ) ) {

					$webhook->set_topic( $data['topic'] );

				} else {
					throw new WC_API_Exception( 'woocommerce_api_invalid_webhook_topic', __( 'Webhook topic must be valid.', 'woocommerce' ), 400 );
				}
			}

			// update delivery URL
			if ( ! empty( $data['delivery_url'] ) ) {
				if ( wc_is_valid_url( $data['delivery_url'] ) ) {

					$webhook->set_delivery_url( $data['delivery_url'] );

				} else {
					throw new WC_API_Exception( 'woocommerce_api_invalid_webhook_delivery_url', __( 'Webhook delivery URL must be a valid URL starting with http:// or https://', 'woocommerce' ), 400 );
				}
			}

			// update secret
			if ( ! empty( $data['secret'] ) ) {
				$webhook->set_secret( $data['secret'] );
			}

			// update status
			if ( ! empty( $data['status'] ) ) {
				$webhook->set_status( $data['status'] );
			}

			// update name
			if ( ! empty( $data['name'] ) ) {
				$webhook->set_name( $data['name'] );
			}

			$webhook->save();

			do_action( 'woocommerce_api_edit_webhook', $webhook->get_id(), $this );

			return $this->get_webhook( $webhook->get_id() );

		} catch ( WC_API_Exception $e ) {

			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Delete a webhook
	 *
	 * @since 2.2
	 * @param int $id webhook ID
	 * @return array|WP_Error
	 */
	public function delete_webhook( $id ) {

		$id = $this->validate_request( $id, 'shop_webhook', 'delete' );

		if ( is_wp_error( $id ) ) {
			return $id;
		}

		do_action( 'woocommerce_api_delete_webhook', $id, $this );

		$webhook = wc_get_webhook( $id );

		return $webhook->delete( true );
	}

	/**
	 * Helper method to get webhook post objects
	 *
	 * @since 2.2
	 * @param array $args Request arguments for filtering query.
	 * @return array
	 */
	private function query_webhooks( $args ) {
		$args = $this->merge_query_args( array(), $args );

		$args['limit'] = isset( $args['posts_per_page'] ) ? intval( $args['posts_per_page'] ) : intval( get_option( 'posts_per_page' ) );

		if ( empty( $args['offset'] ) ) {
			$args['offset'] = 1 < $args['paged'] ? ( $args['paged'] - 1 ) * $args['limit'] : 0;
		}

		$page = $args['paged'];
		unset( $args['paged'], $args['posts_per_page'] );

		if ( isset( $args['s'] ) ) {
			$args['search'] = $args['s'];
			unset( $args['s'] );
		}

		// Post type to webhook status.
		if ( ! empty( $args['post_status'] ) ) {
			$args['status'] = $args['post_status'];
			unset( $args['post_status'] );
		}

		if ( ! empty( $args['post__in'] ) ) {
			$args['include'] = $args['post__in'];
			unset( $args['post__in'] );
		}

		if ( ! empty( $args['date_query'] ) ) {
			foreach ( $args['date_query'] as $date_query ) {
				if ( 'post_date_gmt' === $date_query['column'] ) {
					$args['after']  = isset( $date_query['after'] ) ? $date_query['after'] : null;
					$args['before'] = isset( $date_query['before'] ) ? $date_query['before'] : null;
				} elseif ( 'post_modified_gmt' === $date_query['column'] ) {
					$args['modified_after']  = isset( $date_query['after'] ) ? $date_query['after'] : null;
					$args['modified_before'] = isset( $date_query['before'] ) ? $date_query['before'] : null;
				}
			}

			unset( $args['date_query'] );
		}

		$args['paginate'] = true;

		// Get the webhooks.
		$data_store = WC_Data_Store::load( 'webhook' );
		$results    = $data_store->search_webhooks( $args );

		// Get total items.
		$headers              = new stdClass;
		$headers->page        = $page;
		$headers->total       = $results->total;
		$headers->is_single   = $args['limit'] > $headers->total;
		$headers->total_pages = $results->max_num_pages;

		return array(
			'results' => $results->webhooks,
			'headers' => $headers,
		);
	}

	/**
	 * Get deliveries for a webhook
	 *
	 * @since 2.2
	 * @deprecated 3.3.0 Webhooks deliveries logs now uses logging system.
	 * @param string $webhook_id webhook ID
	 * @param string|null $fields fields to include in response
	 * @return array|WP_Error
	 */
	public function get_webhook_deliveries( $webhook_id, $fields = null ) {

		// Ensure ID is valid webhook ID
		$webhook_id = $this->validate_request( $webhook_id, 'shop_webhook', 'read' );

		if ( is_wp_error( $webhook_id ) ) {
			return $webhook_id;
		}

		return array( 'webhook_deliveries' => array() );
	}

	/**
	 * Get the delivery log for the given webhook ID and delivery ID
	 *
	 * @since 2.2
	 * @deprecated 3.3.0 Webhooks deliveries logs now uses logging system.
	 * @param string $webhook_id webhook ID
	 * @param string $id delivery log ID
	 * @param string|null $fields fields to limit response to
	 *
	 * @return array|WP_Error
	 */
	public function get_webhook_delivery( $webhook_id, $id, $fields = null ) {
		try {
			// Validate webhook ID
			$webhook_id = $this->validate_request( $webhook_id, 'shop_webhook', 'read' );

			if ( is_wp_error( $webhook_id ) ) {
				return $webhook_id;
			}

			$id = absint( $id );

			if ( empty( $id ) ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_webhook_delivery_id', __( 'Invalid webhook delivery ID.', 'woocommerce' ), 404 );
			}

			$webhook = new WC_Webhook( $webhook_id );

			$log = 0;

			if ( ! $log ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_webhook_delivery_id', __( 'Invalid webhook delivery.', 'woocommerce' ), 400 );
			}

			return array( 'webhook_delivery' => apply_filters( 'woocommerce_api_webhook_delivery_response', array(), $id, $fields, $log, $webhook_id, $this ) );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Validate the request by checking:
	 *
	 * 1) the ID is a valid integer.
	 * 2) the ID returns a valid post object and matches the provided post type.
	 * 3) the current user has the proper permissions to read/edit/delete the post.
	 *
	 * @since 3.3.0
	 * @param string|int $id The post ID
	 * @param string $type The post type, either `shop_order`, `shop_coupon`, or `product`.
	 * @param string $context The context of the request, either `read`, `edit` or `delete`.
	 * @return int|WP_Error Valid post ID or WP_Error if any of the checks fails.
	 */
	protected function validate_request( $id, $type, $context ) {
		$id = absint( $id );

		// Validate ID.
		if ( empty( $id ) ) {
			return new WP_Error( "woocommerce_api_invalid_webhook_id", sprintf( __( 'Invalid %s ID', 'woocommerce' ), $type ), array( 'status' => 404 ) );
		}

		$webhook = wc_get_webhook( $id );

		if ( null === $webhook ) {
			return new WP_Error( "woocommerce_api_no_webhook_found", sprintf( __( 'No %1$s found with the ID equal to %2$s', 'woocommerce' ), 'webhook', $id ), array( 'status' => 404 ) );
		}

		// Validate permissions.
		switch ( $context ) {

			case 'read':
				if ( ! current_user_can( 'manage_woocommerce' ) ) {
					return new WP_Error( "woocommerce_api_user_cannot_read_webhook", sprintf( __( 'You do not have permission to read this %s', 'woocommerce' ), 'webhook' ), array( 'status' => 401 ) );
				}
				break;

			case 'edit':
				if ( ! current_user_can( 'manage_woocommerce' ) ) {
					return new WP_Error( "woocommerce_api_user_cannot_edit_webhook", sprintf( __( 'You do not have permission to edit this %s', 'woocommerce' ), 'webhook' ), array( 'status' => 401 ) );
				}
				break;

			case 'delete':
				if ( ! current_user_can( 'manage_woocommerce' ) ) {
					return new WP_Error( "woocommerce_api_user_cannot_delete_webhook", sprintf( __( 'You do not have permission to delete this %s', 'woocommerce' ), 'webhook' ), array( 'status' => 401 ) );
				}
				break;
		}

		return $id;
	}
}
