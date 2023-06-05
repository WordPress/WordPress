<?php
/**
 * WooCommerce API Customers Class
 *
 * Handles requests to the /customers endpoint
 *
 * @author      WooThemes
 * @category    API
 * @package     WooCommerce\RestApi
 * @since       2.1
 * @version     2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_API_Customers extends WC_API_Resource {

	/** @var string $base the route base */
	protected $base = '/customers';

	/** @var string $created_at_min for date filtering */
	private $created_at_min = null;

	/** @var string $created_at_max for date filtering */
	private $created_at_max = null;

	/**
	 * Setup class, overridden to provide customer data to order response
	 *
	 * @since 2.1
	 * @param WC_API_Server $server
	 */
	public function __construct( WC_API_Server $server ) {

		parent::__construct( $server );

		// add customer data to order responses
		add_filter( 'woocommerce_api_order_response', array( $this, 'add_customer_data' ), 10, 2 );

		// modify WP_User_Query to support created_at date filtering
		add_action( 'pre_user_query', array( $this, 'modify_user_query' ) );
	}

	/**
	 * Register the routes for this class
	 *
	 * GET /customers
	 * GET /customers/count
	 * GET /customers/<id>
	 * GET /customers/<id>/orders
	 *
	 * @since 2.1
	 * @param array $routes
	 * @return array
	 */
	public function register_routes( $routes ) {

		# GET /customers
		$routes[ $this->base ] = array(
			array( array( $this, 'get_customers' ),     WC_API_SERVER::READABLE ),
		);

		# GET /customers/count
		$routes[ $this->base . '/count' ] = array(
			array( array( $this, 'get_customers_count' ), WC_API_SERVER::READABLE ),
		);

		# GET /customers/<id>
		$routes[ $this->base . '/(?P<id>\d+)' ] = array(
			array( array( $this, 'get_customer' ),  WC_API_SERVER::READABLE ),
		);

		# GET /customers/<id>/orders
		$routes[ $this->base . '/(?P<id>\d+)/orders' ] = array(
			array( array( $this, 'get_customer_orders' ), WC_API_SERVER::READABLE ),
		);

		return $routes;
	}

	/**
	 * Get all customers
	 *
	 * @since 2.1
	 * @param array $fields
	 * @param array $filter
	 * @param int $page
	 * @return array
	 */
	public function get_customers( $fields = null, $filter = array(), $page = 1 ) {

		$filter['page'] = $page;

		$query = $this->query_customers( $filter );

		$customers = array();

		foreach ( $query->get_results() as $user_id ) {

			if ( ! $this->is_readable( $user_id ) ) {
				continue;
			}

			$customers[] = current( $this->get_customer( $user_id, $fields ) );
		}

		$this->server->add_pagination_headers( $query );

		return array( 'customers' => $customers );
	}

	/**
	 * Get the customer for the given ID
	 *
	 * @since 2.1
	 * @param int $id the customer ID
	 * @param string $fields
	 * @return array|WP_Error
	 */
	public function get_customer( $id, $fields = null ) {
		global $wpdb;

		$id = $this->validate_request( $id, 'customer', 'read' );

		if ( is_wp_error( $id ) ) {
			return $id;
		}

		$customer      = new WC_Customer( $id );
		$last_order    = $customer->get_last_order();
		$customer_data = array(
			'id'               => $customer->get_id(),
			'created_at'       => $this->server->format_datetime( $customer->get_date_created() ? $customer->get_date_created()->getTimestamp() : 0 ), // API gives UTC times.
			'email'            => $customer->get_email(),
			'first_name'       => $customer->get_first_name(),
			'last_name'        => $customer->get_last_name(),
			'username'         => $customer->get_username(),
			'last_order_id'    => is_object( $last_order ) ? $last_order->get_id() : null,
			'last_order_date'  => is_object( $last_order ) ? $this->server->format_datetime( $last_order->get_date_created() ? $last_order->get_date_created()->getTimestamp() : 0 ) : null, // API gives UTC times.
			'orders_count'     => $customer->get_order_count(),
			'total_spent'      => wc_format_decimal( $customer->get_total_spent(), 2 ),
			'avatar_url'       => $customer->get_avatar_url(),
			'billing_address'  => array(
				'first_name' => $customer->get_billing_first_name(),
				'last_name'  => $customer->get_billing_last_name(),
				'company'    => $customer->get_billing_company(),
				'address_1'  => $customer->get_billing_address_1(),
				'address_2'  => $customer->get_billing_address_2(),
				'city'       => $customer->get_billing_city(),
				'state'      => $customer->get_billing_state(),
				'postcode'   => $customer->get_billing_postcode(),
				'country'    => $customer->get_billing_country(),
				'email'      => $customer->get_billing_email(),
				'phone'      => $customer->get_billing_phone(),
			),
			'shipping_address' => array(
				'first_name' => $customer->get_shipping_first_name(),
				'last_name'  => $customer->get_shipping_last_name(),
				'company'    => $customer->get_shipping_company(),
				'address_1'  => $customer->get_shipping_address_1(),
				'address_2'  => $customer->get_shipping_address_2(),
				'city'       => $customer->get_shipping_city(),
				'state'      => $customer->get_shipping_state(),
				'postcode'   => $customer->get_shipping_postcode(),
				'country'    => $customer->get_shipping_country(),
			),
		);

		return array( 'customer' => apply_filters( 'woocommerce_api_customer_response', $customer_data, $customer, $fields, $this->server ) );
	}

	/**
	 * Get the total number of customers
	 *
	 * @since 2.1
	 * @param array $filter
	 * @return array|WP_Error
	 */
	public function get_customers_count( $filter = array() ) {

		$query = $this->query_customers( $filter );

		if ( ! current_user_can( 'list_users' ) ) {
			return new WP_Error( 'woocommerce_api_user_cannot_read_customers_count', __( 'You do not have permission to read the customers count', 'woocommerce' ), array( 'status' => 401 ) );
		}

		return array( 'count' => count( $query->get_results() ) );
	}


	/**
	 * Create a customer
	 *
	 * @param array $data
	 * @return array|WP_Error
	 */
	public function create_customer( $data ) {

		if ( ! current_user_can( 'create_users' ) ) {
			return new WP_Error( 'woocommerce_api_user_cannot_create_customer', __( 'You do not have permission to create this customer', 'woocommerce' ), array( 'status' => 401 ) );
		}

		return array();
	}

	/**
	 * Edit a customer
	 *
	 * @param int $id the customer ID
	 * @param array $data
	 * @return array|WP_Error
	 */
	public function edit_customer( $id, $data ) {

		$id = $this->validate_request( $id, 'customer', 'edit' );

		if ( ! is_wp_error( $id ) ) {
			return $id;
		}

		return $this->get_customer( $id );
	}

	/**
	 * Delete a customer
	 *
	 * @param int $id the customer ID
	 * @return array|WP_Error
	 */
	public function delete_customer( $id ) {

		$id = $this->validate_request( $id, 'customer', 'delete' );

		if ( ! is_wp_error( $id ) ) {
			return $id;
		}

		return $this->delete( $id, 'customer' );
	}

	/**
	 * Get the orders for a customer
	 *
	 * @since 2.1
	 * @param int $id the customer ID
	 * @param string $fields fields to include in response
	 * @return array|WP_Error
	 */
	public function get_customer_orders( $id, $fields = null ) {
		global $wpdb;

		$id = $this->validate_request( $id, 'customer', 'read' );

		if ( is_wp_error( $id ) ) {
			return $id;
		}

		$order_ids = wc_get_orders( array(
			'customer' => $id,
			'limit'    => -1,
			'orderby'  => 'date',
			'order'    => 'ASC',
			'return'   => 'ids',
		) );

		if ( empty( $order_ids ) ) {
			return array( 'orders' => array() );
		}

		$orders = array();

		foreach ( $order_ids as $order_id ) {
			$orders[] = current( WC()->api->WC_API_Orders->get_order( $order_id, $fields ) );
		}

		return array( 'orders' => apply_filters( 'woocommerce_api_customer_orders_response', $orders, $id, $fields, $order_ids, $this->server ) );
	}

	/**
	 * Helper method to get customer user objects
	 *
	 * Note that WP_User_Query does not have built-in pagination so limit & offset are used to provide limited
	 * pagination support
	 *
	 * @since 2.1
	 * @param array $args request arguments for filtering query
	 * @return WP_User_Query
	 */
	private function query_customers( $args = array() ) {

		// default users per page
		$users_per_page = get_option( 'posts_per_page' );

		// set base query arguments
		$query_args = array(
			'fields'  => 'ID',
			'role'    => 'customer',
			'orderby' => 'registered',
			'number'  => $users_per_page,
		);

		// search
		if ( ! empty( $args['q'] ) ) {
			$query_args['search'] = $args['q'];
		}

		// limit number of users returned
		if ( ! empty( $args['limit'] ) ) {

			$query_args['number'] = absint( $args['limit'] );

			$users_per_page = absint( $args['limit'] );
		}

		// page
		$page = ( isset( $args['page'] ) ) ? absint( $args['page'] ) : 1;

		// offset
		if ( ! empty( $args['offset'] ) ) {
			$query_args['offset'] = absint( $args['offset'] );
		} else {
			$query_args['offset'] = $users_per_page * ( $page - 1 );
		}

		// created date
		if ( ! empty( $args['created_at_min'] ) ) {
			$this->created_at_min = $this->server->parse_datetime( $args['created_at_min'] );
		}

		if ( ! empty( $args['created_at_max'] ) ) {
			$this->created_at_max = $this->server->parse_datetime( $args['created_at_max'] );
		}

		$query = new WP_User_Query( $query_args );

		// helper members for pagination headers
		$query->total_pages = ceil( $query->get_total() / $users_per_page );
		$query->page = $page;

		return $query;
	}

	/**
	 * Add customer data to orders
	 *
	 * @since 2.1
	 * @param $order_data
	 * @param $order
	 * @return array
	 */
	public function add_customer_data( $order_data, $order ) {

		if ( 0 == $order->get_user_id() ) {

			// add customer data from order
			$order_data['customer'] = array(
				'id'               => 0,
				'email'            => $order->get_billing_email(),
				'first_name'       => $order->get_billing_first_name(),
				'last_name'        => $order->get_billing_last_name(),
				'billing_address'  => array(
					'first_name' => $order->get_billing_first_name(),
					'last_name'  => $order->get_billing_last_name(),
					'company'    => $order->get_billing_company(),
					'address_1'  => $order->get_billing_address_1(),
					'address_2'  => $order->get_billing_address_2(),
					'city'       => $order->get_billing_city(),
					'state'      => $order->get_billing_state(),
					'postcode'   => $order->get_billing_postcode(),
					'country'    => $order->get_billing_country(),
					'email'      => $order->get_billing_email(),
					'phone'      => $order->get_billing_phone(),
				),
				'shipping_address' => array(
					'first_name' => $order->get_shipping_first_name(),
					'last_name'  => $order->get_shipping_last_name(),
					'company'    => $order->get_shipping_company(),
					'address_1'  => $order->get_shipping_address_1(),
					'address_2'  => $order->get_shipping_address_2(),
					'city'       => $order->get_shipping_city(),
					'state'      => $order->get_shipping_state(),
					'postcode'   => $order->get_shipping_postcode(),
					'country'    => $order->get_shipping_country(),
				),
			);

		} else {

			$order_data['customer'] = current( $this->get_customer( $order->get_user_id() ) );
		}

		return $order_data;
	}

	/**
	 * Modify the WP_User_Query to support filtering on the date the customer was created
	 *
	 * @since 2.1
	 * @param WP_User_Query $query
	 */
	public function modify_user_query( $query ) {

		if ( $this->created_at_min ) {
			$query->query_where .= sprintf( " AND user_registered >= STR_TO_DATE( '%s', '%%Y-%%m-%%d %%h:%%i:%%s' )", esc_sql( $this->created_at_min ) );
		}

		if ( $this->created_at_max ) {
			$query->query_where .= sprintf( " AND user_registered <= STR_TO_DATE( '%s', '%%Y-%%m-%%d %%h:%%i:%%s' )", esc_sql( $this->created_at_max ) );
		}
	}

	/**
	 * Validate the request by checking:
	 *
	 * 1) the ID is a valid integer
	 * 2) the ID returns a valid WP_User
	 * 3) the current user has the proper permissions
	 *
	 * @since 2.1
	 * @see WC_API_Resource::validate_request()
	 * @param string|int $id the customer ID
	 * @param string $type the request type, unused because this method overrides the parent class
	 * @param string $context the context of the request, either `read`, `edit` or `delete`
	 * @return int|WP_Error valid user ID or WP_Error if any of the checks fails
	 */
	protected function validate_request( $id, $type, $context ) {

		$id = absint( $id );

		// validate ID
		if ( empty( $id ) ) {
			return new WP_Error( 'woocommerce_api_invalid_customer_id', __( 'Invalid customer ID', 'woocommerce' ), array( 'status' => 404 ) );
		}

		// non-existent IDs return a valid WP_User object with the user ID = 0
		$customer = new WP_User( $id );

		if ( 0 === $customer->ID ) {
			return new WP_Error( 'woocommerce_api_invalid_customer', __( 'Invalid customer', 'woocommerce' ), array( 'status' => 404 ) );
		}

		// validate permissions
		switch ( $context ) {

			case 'read':
				if ( ! current_user_can( 'list_users' ) ) {
					return new WP_Error( 'woocommerce_api_user_cannot_read_customer', __( 'You do not have permission to read this customer', 'woocommerce' ), array( 'status' => 401 ) );
				}
				break;

			case 'edit':
				if ( ! current_user_can( 'edit_users' ) ) {
					return new WP_Error( 'woocommerce_api_user_cannot_edit_customer', __( 'You do not have permission to edit this customer', 'woocommerce' ), array( 'status' => 401 ) );
				}
				break;

			case 'delete':
				if ( ! current_user_can( 'delete_users' ) ) {
					return new WP_Error( 'woocommerce_api_user_cannot_delete_customer', __( 'You do not have permission to delete this customer', 'woocommerce' ), array( 'status' => 401 ) );
				}
				break;
		}

		return $id;
	}

	/**
	 * Check if the current user can read users
	 *
	 * @since 2.1
	 * @see WC_API_Resource::is_readable()
	 * @param int|WP_Post $post unused
	 * @return bool true if the current user can read users, false otherwise
	 */
	protected function is_readable( $post ) {

		return current_user_can( 'list_users' );
	}
}
