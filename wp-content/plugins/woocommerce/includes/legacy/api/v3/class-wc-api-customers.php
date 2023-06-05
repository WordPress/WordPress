<?php
/**
 * WooCommerce API Customers Class
 *
 * Handles requests to the /customers endpoint
 *
 * @author   WooThemes
 * @category API
 * @package  WooCommerce\RestApi
 * @since    2.2
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
	 * @since 2.2
	 * @param array $routes
	 * @return array
	 */
	public function register_routes( $routes ) {

		# GET/POST /customers
		$routes[ $this->base ] = array(
			array( array( $this, 'get_customers' ),   WC_API_SERVER::READABLE ),
			array( array( $this, 'create_customer' ), WC_API_SERVER::CREATABLE | WC_API_Server::ACCEPT_DATA ),
		);

		# GET /customers/count
		$routes[ $this->base . '/count' ] = array(
			array( array( $this, 'get_customers_count' ), WC_API_SERVER::READABLE ),
		);

		# GET/PUT/DELETE /customers/<id>
		$routes[ $this->base . '/(?P<id>\d+)' ] = array(
			array( array( $this, 'get_customer' ),    WC_API_SERVER::READABLE ),
			array( array( $this, 'edit_customer' ),   WC_API_SERVER::EDITABLE | WC_API_SERVER::ACCEPT_DATA ),
			array( array( $this, 'delete_customer' ), WC_API_SERVER::DELETABLE ),
		);

		# GET /customers/email/<email>
		$routes[ $this->base . '/email/(?P<email>.+)' ] = array(
			array( array( $this, 'get_customer_by_email' ), WC_API_SERVER::READABLE ),
		);

		# GET /customers/<id>/orders
		$routes[ $this->base . '/(?P<id>\d+)/orders' ] = array(
			array( array( $this, 'get_customer_orders' ), WC_API_SERVER::READABLE ),
		);

		# GET /customers/<id>/downloads
		$routes[ $this->base . '/(?P<id>\d+)/downloads' ] = array(
			array( array( $this, 'get_customer_downloads' ), WC_API_SERVER::READABLE ),
		);

		# POST|PUT /customers/bulk
		$routes[ $this->base . '/bulk' ] = array(
			array( array( $this, 'bulk' ), WC_API_Server::EDITABLE | WC_API_Server::ACCEPT_DATA ),
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
	 * @param array $fields
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
			'last_update'      => $this->server->format_datetime( $customer->get_date_modified() ? $customer->get_date_modified()->getTimestamp() : 0 ), // API gives UTC times.
			'email'            => $customer->get_email(),
			'first_name'       => $customer->get_first_name(),
			'last_name'        => $customer->get_last_name(),
			'username'         => $customer->get_username(),
			'role'             => $customer->get_role(),
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
	 * Get the customer for the given email
	 *
	 * @since 2.1
	 *
	 * @param string $email the customer email
	 * @param array $fields
	 *
	 * @return array|WP_Error
	 */
	public function get_customer_by_email( $email, $fields = null ) {
		try {
			if ( is_email( $email ) ) {
				$customer = get_user_by( 'email', $email );
				if ( ! is_object( $customer ) ) {
					throw new WC_API_Exception( 'woocommerce_api_invalid_customer_email', __( 'Invalid customer email', 'woocommerce' ), 404 );
				}
			} else {
				throw new WC_API_Exception( 'woocommerce_api_invalid_customer_email', __( 'Invalid customer email', 'woocommerce' ), 404 );
			}

			return $this->get_customer( $customer->ID, $fields );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Get the total number of customers
	 *
	 * @since 2.1
	 *
	 * @param array $filter
	 *
	 * @return array|WP_Error
	 */
	public function get_customers_count( $filter = array() ) {
		try {
			if ( ! current_user_can( 'list_users' ) ) {
				throw new WC_API_Exception( 'woocommerce_api_user_cannot_read_customers_count', __( 'You do not have permission to read the customers count', 'woocommerce' ), 401 );
			}

			$query = $this->query_customers( $filter );

			return array( 'count' => $query->get_total() );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Get customer billing address fields.
	 *
	 * @since  2.2
	 * @return array
	 */
	protected function get_customer_billing_address() {
		$billing_address = apply_filters( 'woocommerce_api_customer_billing_address', array(
			'first_name',
			'last_name',
			'company',
			'address_1',
			'address_2',
			'city',
			'state',
			'postcode',
			'country',
			'email',
			'phone',
		) );

		return $billing_address;
	}

	/**
	 * Get customer shipping address fields.
	 *
	 * @since  2.2
	 * @return array
	 */
	protected function get_customer_shipping_address() {
		$shipping_address = apply_filters( 'woocommerce_api_customer_shipping_address', array(
			'first_name',
			'last_name',
			'company',
			'address_1',
			'address_2',
			'city',
			'state',
			'postcode',
			'country',
		) );

		return $shipping_address;
	}

	/**
	 * Add/Update customer data.
	 *
	 * @since 2.2
	 * @param int $id the customer ID
	 * @param array $data
	 * @param WC_Customer $customer
	 */
	protected function update_customer_data( $id, $data, $customer ) {

		// Customer first name.
		if ( isset( $data['first_name'] ) ) {
			$customer->set_first_name( wc_clean( $data['first_name'] ) );
		}

		// Customer last name.
		if ( isset( $data['last_name'] ) ) {
			$customer->set_last_name( wc_clean( $data['last_name'] ) );
		}

		// Customer billing address.
		if ( isset( $data['billing_address'] ) ) {
			foreach ( $this->get_customer_billing_address() as $field ) {
				if ( isset( $data['billing_address'][ $field ] ) ) {
					if ( is_callable( array( $customer, "set_billing_{$field}" ) ) ) {
						$customer->{"set_billing_{$field}"}( $data['billing_address'][ $field ] );
					} else {
						$customer->update_meta_data( 'billing_' . $field, wc_clean( $data['billing_address'][ $field ] ) );
					}
				}
			}
		}

		// Customer shipping address.
		if ( isset( $data['shipping_address'] ) ) {
			foreach ( $this->get_customer_shipping_address() as $field ) {
				if ( isset( $data['shipping_address'][ $field ] ) ) {
					if ( is_callable( array( $customer, "set_shipping_{$field}" ) ) ) {
						$customer->{"set_shipping_{$field}"}( $data['shipping_address'][ $field ] );
					} else {
						$customer->update_meta_data( 'shipping_' . $field, wc_clean( $data['shipping_address'][ $field ] ) );
					}
				}
			}
		}

		do_action( 'woocommerce_api_update_customer_data', $id, $data, $customer );
	}

	/**
	 * Create a customer
	 *
	 * @since 2.2
	 *
	 * @param array $data
	 *
	 * @return array|WP_Error
	 */
	public function create_customer( $data ) {
		try {
			if ( ! isset( $data['customer'] ) ) {
				throw new WC_API_Exception( 'woocommerce_api_missing_customer_data', sprintf( __( 'No %1$s data specified to create %1$s', 'woocommerce' ), 'customer' ), 400 );
			}

			$data = $data['customer'];

			// Checks with can create new users.
			if ( ! current_user_can( 'create_users' ) ) {
				throw new WC_API_Exception( 'woocommerce_api_user_cannot_create_customer', __( 'You do not have permission to create this customer', 'woocommerce' ), 401 );
			}

			$data = apply_filters( 'woocommerce_api_create_customer_data', $data, $this );

			// Checks with the email is missing.
			if ( ! isset( $data['email'] ) ) {
				throw new WC_API_Exception( 'woocommerce_api_missing_customer_email', sprintf( __( 'Missing parameter %s', 'woocommerce' ), 'email' ), 400 );
			}

			// Create customer.
			$customer = new WC_Customer;
			$customer->set_username( ! empty( $data['username'] ) ? $data['username'] : '' );
			$customer->set_password( ! empty( $data['password'] ) ? $data['password'] : '' );
			$customer->set_email( $data['email'] );
			$customer->save();

			if ( ! $customer->get_id() ) {
				throw new WC_API_Exception( 'woocommerce_api_user_cannot_create_customer', __( 'This resource cannot be created.', 'woocommerce' ), 400 );
			}

			// Added customer data.
			$this->update_customer_data( $customer->get_id(), $data, $customer );
			$customer->save();

			do_action( 'woocommerce_api_create_customer', $customer->get_id(), $data );

			$this->server->send_status( 201 );

			return $this->get_customer( $customer->get_id() );
		} catch ( Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Edit a customer
	 *
	 * @since 2.2
	 *
	 * @param int $id the customer ID
	 * @param array $data
	 *
	 * @return array|WP_Error
	 */
	public function edit_customer( $id, $data ) {
		try {
			if ( ! isset( $data['customer'] ) ) {
				throw new WC_API_Exception( 'woocommerce_api_missing_customer_data', sprintf( __( 'No %1$s data specified to edit %1$s', 'woocommerce' ), 'customer' ), 400 );
			}

			$data = $data['customer'];

			// Validate the customer ID.
			$id = $this->validate_request( $id, 'customer', 'edit' );

			// Return the validate error.
			if ( is_wp_error( $id ) ) {
				throw new WC_API_Exception( $id->get_error_code(), $id->get_error_message(), 400 );
			}

			$data = apply_filters( 'woocommerce_api_edit_customer_data', $data, $this );

			$customer = new WC_Customer( $id );

			// Customer email.
			if ( isset( $data['email'] ) ) {
				$customer->set_email( $data['email'] );
			}

			// Customer password.
			if ( isset( $data['password'] ) ) {
				$customer->set_password( $data['password'] );
			}

			// Update customer data.
			$this->update_customer_data( $customer->get_id(), $data, $customer );

			$customer->save();

			do_action( 'woocommerce_api_edit_customer', $customer->get_id(), $data );

			return $this->get_customer( $customer->get_id() );
		} catch ( Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Delete a customer
	 *
	 * @since 2.2
	 * @param int $id the customer ID
	 * @return array|WP_Error
	 */
	public function delete_customer( $id ) {

		// Validate the customer ID.
		$id = $this->validate_request( $id, 'customer', 'delete' );

		// Return the validate error.
		if ( is_wp_error( $id ) ) {
			return $id;
		}

		do_action( 'woocommerce_api_delete_customer', $id, $this );

		return $this->delete( $id, 'customer' );
	}

	/**
	 * Get the orders for a customer
	 *
	 * @since 2.1
	 * @param int $id the customer ID
	 * @param string $fields fields to include in response
	 * @param array $filter filters
	 * @return array|WP_Error
	 */
	public function get_customer_orders( $id, $fields = null, $filter = array() ) {
		$id = $this->validate_request( $id, 'customer', 'read' );

		if ( is_wp_error( $id ) ) {
			return $id;
		}

		$filter['customer_id'] = $id;
		$orders = WC()->api->WC_API_Orders->get_orders( $fields, $filter, null, -1 );

		return $orders;
	}

	/**
	 * Get the available downloads for a customer
	 *
	 * @since 2.2
	 * @param int $id the customer ID
	 * @param string $fields fields to include in response
	 * @return array|WP_Error
	 */
	public function get_customer_downloads( $id, $fields = null ) {
		$id = $this->validate_request( $id, 'customer', 'read' );

		if ( is_wp_error( $id ) ) {
			return $id;
		}

		$downloads  = array();
		$_downloads = wc_get_customer_available_downloads( $id );

		foreach ( $_downloads as $key => $download ) {
			$downloads[] = array(
				'download_url'        => $download['download_url'],
				'download_id'         => $download['download_id'],
				'product_id'          => $download['product_id'],
				'download_name'       => $download['download_name'],
				'order_id'            => $download['order_id'],
				'order_key'           => $download['order_key'],
				'downloads_remaining' => $download['downloads_remaining'],
				'access_expires'      => $download['access_expires'] ? $this->server->format_datetime( $download['access_expires'] ) : null,
				'file'                => $download['file'],
			);
		}

		return array( 'downloads' => apply_filters( 'woocommerce_api_customer_downloads_response', $downloads, $id, $fields, $this->server ) );
	}

	/**
	 * Helper method to get customer user objects
	 *
	 * Note that WP_User_Query does not have built-in pagination so limit & offset are used to provide limited
	 * pagination support
	 *
	 * The filter for role can only be a single role in a string.
	 *
	 * @since 2.3
	 * @param array $args request arguments for filtering query
	 * @return WP_User_Query
	 */
	private function query_customers( $args = array() ) {

		// default users per page
		$users_per_page = get_option( 'posts_per_page' );

		// Set base query arguments
		$query_args = array(
			'fields'  => 'ID',
			'role'    => 'customer',
			'orderby' => 'registered',
			'number'  => $users_per_page,
		);

		// Custom Role
		if ( ! empty( $args['role'] ) ) {
			$query_args['role'] = $args['role'];

			// Show users on all roles
			if ( 'all' === $query_args['role'] ) {
				unset( $query_args['role'] );
			}
		}

		// Search
		if ( ! empty( $args['q'] ) ) {
			$query_args['search'] = $args['q'];
		}

		// Limit number of users returned
		if ( ! empty( $args['limit'] ) ) {
			if ( -1 == $args['limit'] ) {
				unset( $query_args['number'] );
			} else {
				$query_args['number'] = absint( $args['limit'] );
				$users_per_page       = absint( $args['limit'] );
			}
		} else {
			$args['limit'] = $query_args['number'];
		}

		// Page
		$page = ( isset( $args['page'] ) ) ? absint( $args['page'] ) : 1;

		// Offset
		if ( ! empty( $args['offset'] ) ) {
			$query_args['offset'] = absint( $args['offset'] );
		} else {
			$query_args['offset'] = $users_per_page * ( $page - 1 );
		}

		// Created date
		if ( ! empty( $args['created_at_min'] ) ) {
			$this->created_at_min = $this->server->parse_datetime( $args['created_at_min'] );
		}

		if ( ! empty( $args['created_at_max'] ) ) {
			$this->created_at_max = $this->server->parse_datetime( $args['created_at_max'] );
		}

		// Order (ASC or DESC, ASC by default)
		if ( ! empty( $args['order'] ) ) {
			$query_args['order'] = $args['order'];
		}

		// Order by
		if ( ! empty( $args['orderby'] ) ) {
			$query_args['orderby'] = $args['orderby'];

			// Allow sorting by meta value
			if ( ! empty( $args['orderby_meta_key'] ) ) {
				$query_args['meta_key'] = $args['orderby_meta_key'];
			}
		}

		$query = new WP_User_Query( $query_args );

		// Helper members for pagination headers
		$query->total_pages = ( -1 == $args['limit'] ) ? 1 : ceil( $query->get_total() / $users_per_page );
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
			$query->query_where .= sprintf( " AND user_registered >= STR_TO_DATE( '%s', '%%Y-%%m-%%d %%H:%%i:%%s' )", esc_sql( $this->created_at_min ) );
		}

		if ( $this->created_at_max ) {
			$query->query_where .= sprintf( " AND user_registered <= STR_TO_DATE( '%s', '%%Y-%%m-%%d %%H:%%i:%%s' )", esc_sql( $this->created_at_max ) );
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
	 * @param integer $id the customer ID
	 * @param string $type the request type, unused because this method overrides the parent class
	 * @param string $context the context of the request, either `read`, `edit` or `delete`
	 * @return int|WP_Error valid user ID or WP_Error if any of the checks fails
	 */
	protected function validate_request( $id, $type, $context ) {

		try {
			$id = absint( $id );

			// validate ID
			if ( empty( $id ) ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_customer_id', __( 'Invalid customer ID', 'woocommerce' ), 404 );
			}

			// non-existent IDs return a valid WP_User object with the user ID = 0
			$customer = new WP_User( $id );

			if ( 0 === $customer->ID ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_customer', __( 'Invalid customer', 'woocommerce' ), 404 );
			}

			// validate permissions
			switch ( $context ) {

				case 'read':
					if ( ! current_user_can( 'list_users' ) ) {
						throw new WC_API_Exception( 'woocommerce_api_user_cannot_read_customer', __( 'You do not have permission to read this customer', 'woocommerce' ), 401 );
					}
					break;

				case 'edit':
					if ( ! wc_rest_check_user_permissions( 'edit', $customer->ID ) ) {
						throw new WC_API_Exception( 'woocommerce_api_user_cannot_edit_customer', __( 'You do not have permission to edit this customer', 'woocommerce' ), 401 );
					}
					break;

				case 'delete':
					if ( ! wc_rest_check_user_permissions( 'delete', $customer->ID ) ) {
						throw new WC_API_Exception( 'woocommerce_api_user_cannot_delete_customer', __( 'You do not have permission to delete this customer', 'woocommerce' ), 401 );
					}
					break;
			}

			return $id;
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
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

	/**
	 * Bulk update or insert customers
	 * Accepts an array with customers in the formats supported by
	 * WC_API_Customers->create_customer() and WC_API_Customers->edit_customer()
	 *
	 * @since 2.4.0
	 *
	 * @param array $data
	 *
	 * @return array|WP_Error
	 */
	public function bulk( $data ) {

		try {
			if ( ! isset( $data['customers'] ) ) {
				throw new WC_API_Exception( 'woocommerce_api_missing_customers_data', sprintf( __( 'No %1$s data specified to create/edit %1$s', 'woocommerce' ), 'customers' ), 400 );
			}

			$data  = $data['customers'];
			$limit = apply_filters( 'woocommerce_api_bulk_limit', 100, 'customers' );

			// Limit bulk operation
			if ( count( $data ) > $limit ) {
				throw new WC_API_Exception( 'woocommerce_api_customers_request_entity_too_large', sprintf( __( 'Unable to accept more than %s items for this request.', 'woocommerce' ), $limit ), 413 );
			}

			$customers = array();

			foreach ( $data as $_customer ) {
				$customer_id = 0;

				// Try to get the customer ID
				if ( isset( $_customer['id'] ) ) {
					$customer_id = intval( $_customer['id'] );
				}

				if ( $customer_id ) {

					// Customer exists / edit customer
					$edit = $this->edit_customer( $customer_id, array( 'customer' => $_customer ) );

					if ( is_wp_error( $edit ) ) {
						$customers[] = array(
							'id'    => $customer_id,
							'error' => array( 'code' => $edit->get_error_code(), 'message' => $edit->get_error_message() ),
						);
					} else {
						$customers[] = $edit['customer'];
					}
				} else {

					// Customer don't exists / create customer
					$new = $this->create_customer( array( 'customer' => $_customer ) );

					if ( is_wp_error( $new ) ) {
						$customers[] = array(
							'id'    => $customer_id,
							'error' => array( 'code' => $new->get_error_code(), 'message' => $new->get_error_message() ),
						);
					} else {
						$customers[] = $new['customer'];
					}
				}
			}

			return array( 'customers' => apply_filters( 'woocommerce_api_customers_bulk_response', $customers, $this ) );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}
}
