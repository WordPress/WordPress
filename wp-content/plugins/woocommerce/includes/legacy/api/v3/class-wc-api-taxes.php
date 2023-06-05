<?php
/**
 * WooCommerce API Taxes Class
 *
 * Handles requests to the /taxes endpoint
 *
 * @author   WooThemes
 * @category API
 * @package  WooCommerce\RestApi
 * @since    2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_API_Taxes extends WC_API_Resource {

	/** @var string $base the route base */
	protected $base = '/taxes';

	/**
	 * Register the routes for this class
	 *
	 * GET /taxes
	 * GET /taxes/count
	 * GET /taxes/<id>
	 *
	 * @since 2.1
	 * @param array $routes
	 * @return array
	 */
	public function register_routes( $routes ) {

		# GET/POST /taxes
		$routes[ $this->base ] = array(
			array( array( $this, 'get_taxes' ), WC_API_Server::READABLE ),
			array( array( $this, 'create_tax' ), WC_API_Server::CREATABLE | WC_API_Server::ACCEPT_DATA ),
		);

		# GET /taxes/count
		$routes[ $this->base . '/count' ] = array(
			array( array( $this, 'get_taxes_count' ), WC_API_Server::READABLE ),
		);

		# GET/PUT/DELETE /taxes/<id>
		$routes[ $this->base . '/(?P<id>\d+)' ] = array(
			array( array( $this, 'get_tax' ), WC_API_Server::READABLE ),
			array( array( $this, 'edit_tax' ), WC_API_SERVER::EDITABLE | WC_API_SERVER::ACCEPT_DATA ),
			array( array( $this, 'delete_tax' ), WC_API_SERVER::DELETABLE ),
		);

		# GET/POST /taxes/classes
		$routes[ $this->base . '/classes' ] = array(
			array( array( $this, 'get_tax_classes' ), WC_API_Server::READABLE ),
			array( array( $this, 'create_tax_class' ), WC_API_Server::CREATABLE | WC_API_Server::ACCEPT_DATA ),
		);

		# GET /taxes/classes/count
		$routes[ $this->base . '/classes/count' ] = array(
			array( array( $this, 'get_tax_classes_count' ), WC_API_Server::READABLE ),
		);

		# GET /taxes/classes/<slug>
		$routes[ $this->base . '/classes/(?P<slug>\w[\w\s\-]*)' ] = array(
			array( array( $this, 'delete_tax_class' ), WC_API_SERVER::DELETABLE ),
		);

		# POST|PUT /taxes/bulk
		$routes[ $this->base . '/bulk' ] = array(
			array( array( $this, 'bulk' ), WC_API_Server::EDITABLE | WC_API_Server::ACCEPT_DATA ),
		);

		return $routes;
	}

	/**
	 * Get all taxes
	 *
	 * @since 2.5.0
	 *
	 * @param string $fields
	 * @param array  $filter
	 * @param string $class
	 * @param int    $page
	 *
	 * @return array
	 */
	public function get_taxes( $fields = null, $filter = array(), $class = null, $page = 1 ) {
		if ( ! empty( $class ) ) {
			$filter['tax_rate_class'] = $class;
		}

		$filter['page'] = $page;

		$query = $this->query_tax_rates( $filter );

		$taxes = array();

		foreach ( $query['results'] as $tax ) {
			$taxes[] = current( $this->get_tax( $tax->tax_rate_id, $fields ) );
		}

		// Set pagination headers
		$this->server->add_pagination_headers( $query['headers'] );

		return array( 'taxes' => $taxes );
	}

	/**
	 * Get the tax for the given ID
	 *
	 * @since 2.5.0
	 *
	 * @param int $id The tax ID
	 * @param string $fields fields to include in response
	 *
	 * @return array|WP_Error
	 */
	public function get_tax( $id, $fields = null ) {
		global $wpdb;

		try {
			$id = absint( $id );

			// Permissions check
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				throw new WC_API_Exception( 'woocommerce_api_user_cannot_read_tax', __( 'You do not have permission to read tax rate', 'woocommerce' ), 401 );
			}

			// Get tax rate details
			$tax = WC_Tax::_get_tax_rate( $id );

			if ( is_wp_error( $tax ) || empty( $tax ) ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_tax_id', __( 'A tax rate with the provided ID could not be found', 'woocommerce' ), 404 );
			}

			$tax_data = array(
				'id'       => (int) $tax['tax_rate_id'],
				'country'  => $tax['tax_rate_country'],
				'state'    => $tax['tax_rate_state'],
				'postcode' => '',
				'city'     => '',
				'rate'     => $tax['tax_rate'],
				'name'     => $tax['tax_rate_name'],
				'priority' => (int) $tax['tax_rate_priority'],
				'compound' => (bool) $tax['tax_rate_compound'],
				'shipping' => (bool) $tax['tax_rate_shipping'],
				'order'    => (int) $tax['tax_rate_order'],
				'class'    => $tax['tax_rate_class'] ? $tax['tax_rate_class'] : 'standard',
			);

			// Get locales from a tax rate
			$locales = $wpdb->get_results( $wpdb->prepare( "
				SELECT location_code, location_type
				FROM {$wpdb->prefix}woocommerce_tax_rate_locations
				WHERE tax_rate_id = %d
			", $id ) );

			if ( ! is_wp_error( $tax ) && ! is_null( $tax ) ) {
				foreach ( $locales as $locale ) {
					$tax_data[ $locale->location_type ] = $locale->location_code;
				}
			}

			return array( 'tax' => apply_filters( 'woocommerce_api_tax_response', $tax_data, $tax, $fields, $this ) );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Create a tax
	 *
	 * @since 2.5.0
	 *
	 * @param array $data
	 *
	 * @return array|WP_Error
	 */
	public function create_tax( $data ) {
		try {
			if ( ! isset( $data['tax'] ) ) {
				throw new WC_API_Exception( 'woocommerce_api_missing_tax_data', sprintf( __( 'No %1$s data specified to create %1$s', 'woocommerce' ), 'tax' ), 400 );
			}

			// Check permissions
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				throw new WC_API_Exception( 'woocommerce_api_user_cannot_create_tax', __( 'You do not have permission to create tax rates', 'woocommerce' ), 401 );
			}

			$data = apply_filters( 'woocommerce_api_create_tax_data', $data['tax'], $this );

			$tax_data = array(
				'tax_rate_country'  => '',
				'tax_rate_state'    => '',
				'tax_rate'          => '',
				'tax_rate_name'     => '',
				'tax_rate_priority' => 1,
				'tax_rate_compound' => 0,
				'tax_rate_shipping' => 1,
				'tax_rate_order'    => 0,
				'tax_rate_class'    => '',
			);

			foreach ( $tax_data as $key => $value ) {
				$new_key = str_replace( 'tax_rate_', '', $key );
				$new_key = 'tax_rate' === $new_key ? 'rate' : $new_key;

				if ( isset( $data[ $new_key ] ) ) {
					if ( in_array( $new_key, array( 'compound', 'shipping' ) ) ) {
						$tax_data[ $key ] = $data[ $new_key ] ? 1 : 0;
					} else {
						$tax_data[ $key ] = $data[ $new_key ];
					}
				}
			}

			// Create tax rate
			$id = WC_Tax::_insert_tax_rate( $tax_data );

			// Add locales
			if ( ! empty( $data['postcode'] ) ) {
				WC_Tax::_update_tax_rate_postcodes( $id, wc_clean( $data['postcode'] ) );
			}

			if ( ! empty( $data['city'] ) ) {
				WC_Tax::_update_tax_rate_cities( $id, wc_clean( $data['city'] ) );
			}

			do_action( 'woocommerce_api_create_tax', $id, $data );

			$this->server->send_status( 201 );

			return $this->get_tax( $id );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Edit a tax
	 *
	 * @since 2.5.0
	 *
	 * @param int $id The tax ID
	 * @param array $data
	 *
	 * @return array|WP_Error
	 */
	public function edit_tax( $id, $data ) {
		try {
			if ( ! isset( $data['tax'] ) ) {
				throw new WC_API_Exception( 'woocommerce_api_missing_tax_data', sprintf( __( 'No %1$s data specified to edit %1$s', 'woocommerce' ), 'tax' ), 400 );
			}

			// Check permissions
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				throw new WC_API_Exception( 'woocommerce_api_user_cannot_edit_tax', __( 'You do not have permission to edit tax rates', 'woocommerce' ), 401 );
			}

			$data = $data['tax'];

			// Get current tax rate data
			$tax = $this->get_tax( $id );

			if ( is_wp_error( $tax ) ) {
				$error_data = $tax->get_error_data();
				throw new WC_API_Exception( $tax->get_error_code(), $tax->get_error_message(), $error_data['status'] );
			}

			$current_data   = $tax['tax'];
			$data           = apply_filters( 'woocommerce_api_edit_tax_data', $data, $this );
			$tax_data       = array();
			$default_fields = array(
				'tax_rate_country',
				'tax_rate_state',
				'tax_rate',
				'tax_rate_name',
				'tax_rate_priority',
				'tax_rate_compound',
				'tax_rate_shipping',
				'tax_rate_order',
				'tax_rate_class',
			);

			foreach ( $data as $key => $value ) {
				$new_key = 'rate' === $key ? 'tax_rate' : 'tax_rate_' . $key;

				// Check if the key is valid
				if ( ! in_array( $new_key, $default_fields ) ) {
					continue;
				}

				// Test new data against current data
				if ( $value === $current_data[ $key ] ) {
					continue;
				}

				// Fix compound and shipping values
				if ( in_array( $key, array( 'compound', 'shipping' ) ) ) {
					$value = $value ? 1 : 0;
				}

				$tax_data[ $new_key ] = $value;
			}

			// Update tax rate
			WC_Tax::_update_tax_rate( $id, $tax_data );

			// Update locales
			if ( ! empty( $data['postcode'] ) && $current_data['postcode'] != $data['postcode'] ) {
				WC_Tax::_update_tax_rate_postcodes( $id, wc_clean( $data['postcode'] ) );
			}

			if ( ! empty( $data['city'] ) && $current_data['city'] != $data['city'] ) {
				WC_Tax::_update_tax_rate_cities( $id, wc_clean( $data['city'] ) );
			}

			do_action( 'woocommerce_api_edit_tax_rate', $id, $data );

			return $this->get_tax( $id );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Delete a tax
	 *
	 * @since 2.5.0
	 *
	 * @param int $id The tax ID
	 *
	 * @return array|WP_Error
	 */
	public function delete_tax( $id ) {
		global $wpdb;

		try {
			// Check permissions
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				throw new WC_API_Exception( 'woocommerce_api_user_cannot_delete_tax', __( 'You do not have permission to delete tax rates', 'woocommerce' ), 401 );
			}

			$id = absint( $id );

			WC_Tax::_delete_tax_rate( $id );

			if ( 0 === $wpdb->rows_affected ) {
				throw new WC_API_Exception( 'woocommerce_api_cannot_delete_tax', __( 'Could not delete the tax rate', 'woocommerce' ), 401 );
			}

			return array( 'message' => sprintf( __( 'Deleted %s', 'woocommerce' ), 'tax' ) );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Get the total number of taxes
	 *
	 * @since 2.5.0
	 *
	 * @param string $class
	 * @param array $filter
	 *
	 * @return array|WP_Error
	 */
	public function get_taxes_count( $class = null, $filter = array() ) {
		try {
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				throw new WC_API_Exception( 'woocommerce_api_user_cannot_read_taxes_count', __( 'You do not have permission to read the taxes count', 'woocommerce' ), 401 );
			}

			if ( ! empty( $class ) ) {
				$filter['tax_rate_class'] = $class;
			}

			$query = $this->query_tax_rates( $filter, true );

			return array( 'count' => (int) $query['headers']->total );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Helper method to get tax rates objects
	 *
	 * @since 2.5.0
	 *
	 * @param  array $args
	 * @param  bool  $count_only
	 *
	 * @return array
	 */
	protected function query_tax_rates( $args, $count_only = false ) {
		global $wpdb;

		$results = '';

		// Set args
		$args = $this->merge_query_args( $args, array() );

		$query = "
			SELECT tax_rate_id
			FROM {$wpdb->prefix}woocommerce_tax_rates
			WHERE 1 = 1
		";

		// Filter by tax class
		if ( ! empty( $args['tax_rate_class'] ) ) {
			$tax_rate_class = esc_sql( 'standard' !== $args['tax_rate_class'] ? sanitize_title( $args['tax_rate_class'] ) : '' );
			$query .= " AND tax_rate_class = '$tax_rate_class'";
		}

		// Order tax rates
		$order_by = ' ORDER BY tax_rate_order';

		// Pagination
		$per_page   = absint( isset( $args['posts_per_page'] ) ? $args['posts_per_page'] : get_option( 'posts_per_page' ) );
		$offset     = 1 < $args['paged'] ? ( $args['paged'] - 1 ) * $per_page : 0;
		$pagination = sprintf( ' LIMIT %d, %d', $offset, $per_page );

		if ( ! $count_only ) {
			$results = $wpdb->get_results( $query . $order_by . $pagination );
		}

		$wpdb->get_results( $query );
		$headers              = new stdClass;
		$headers->page        = $args['paged'];
		$headers->total       = (int) $wpdb->num_rows;
		$headers->is_single   = $per_page > $headers->total;
		$headers->total_pages = ceil( $headers->total / $per_page );

		return array(
			'results' => $results,
			'headers' => $headers,
		);
	}

	/**
	 * Bulk update or insert taxes
	 * Accepts an array with taxes in the formats supported by
	 * WC_API_Taxes->create_tax() and WC_API_Taxes->edit_tax()
	 *
	 * @since 2.5.0
	 *
	 * @param array $data
	 *
	 * @return array|WP_Error
	 */
	public function bulk( $data ) {
		try {
			if ( ! isset( $data['taxes'] ) ) {
				throw new WC_API_Exception( 'woocommerce_api_missing_taxes_data', sprintf( __( 'No %1$s data specified to create/edit %1$s', 'woocommerce' ), 'taxes' ), 400 );
			}

			$data  = $data['taxes'];
			$limit = apply_filters( 'woocommerce_api_bulk_limit', 100, 'taxes' );

			// Limit bulk operation
			if ( count( $data ) > $limit ) {
				throw new WC_API_Exception( 'woocommerce_api_taxes_request_entity_too_large', sprintf( __( 'Unable to accept more than %s items for this request.', 'woocommerce' ), $limit ), 413 );
			}

			$taxes = array();

			foreach ( $data as $_tax ) {
				$tax_id = 0;

				// Try to get the tax rate ID
				if ( isset( $_tax['id'] ) ) {
					$tax_id = intval( $_tax['id'] );
				}

				if ( $tax_id ) {

					// Tax rate exists / edit tax rate
					$edit = $this->edit_tax( $tax_id, array( 'tax' => $_tax ) );

					if ( is_wp_error( $edit ) ) {
						$taxes[] = array(
							'id'    => $tax_id,
							'error' => array( 'code' => $edit->get_error_code(), 'message' => $edit->get_error_message() ),
						);
					} else {
						$taxes[] = $edit['tax'];
					}
				} else {

					// Tax rate don't exists / create tax rate
					$new = $this->create_tax( array( 'tax' => $_tax ) );

					if ( is_wp_error( $new ) ) {
						$taxes[] = array(
							'id'    => $tax_id,
							'error' => array( 'code' => $new->get_error_code(), 'message' => $new->get_error_message() ),
						);
					} else {
						$taxes[] = $new['tax'];
					}
				}
			}

			return array( 'taxes' => apply_filters( 'woocommerce_api_taxes_bulk_response', $taxes, $this ) );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Get all tax classes
	 *
	 * @since 2.5.0
	 *
	 * @param string $fields
	 *
	 * @return array|WP_Error
	 */
	public function get_tax_classes( $fields = null ) {
		try {
			// Permissions check
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				throw new WC_API_Exception( 'woocommerce_api_user_cannot_read_tax_classes', __( 'You do not have permission to read tax classes', 'woocommerce' ), 401 );
			}

			$tax_classes = array();

			// Add standard class
			$tax_classes[] = array(
				'slug' => 'standard',
				'name' => __( 'Standard rate', 'woocommerce' ),
			);

			$classes = WC_Tax::get_tax_classes();

			foreach ( $classes as $class ) {
				$tax_classes[] = apply_filters( 'woocommerce_api_tax_class_response', array(
					'slug' => sanitize_title( $class ),
					'name' => $class,
				), $class, $fields, $this );
			}

			return array( 'tax_classes' => apply_filters( 'woocommerce_api_tax_classes_response', $tax_classes, $classes, $fields, $this ) );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Create a tax class.
	 *
	 * @since 2.5.0
	 *
	 * @param array $data
	 *
	 * @return array|WP_Error
	 */
	public function create_tax_class( $data ) {
		try {
			if ( ! isset( $data['tax_class'] ) ) {
				throw new WC_API_Exception( 'woocommerce_api_missing_tax_class_data', sprintf( __( 'No %1$s data specified to create %1$s', 'woocommerce' ), 'tax_class' ), 400 );
			}

			// Check permissions
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				throw new WC_API_Exception( 'woocommerce_api_user_cannot_create_tax_class', __( 'You do not have permission to create tax classes', 'woocommerce' ), 401 );
			}

			$data = $data['tax_class'];

			if ( empty( $data['name'] ) ) {
				throw new WC_API_Exception( 'woocommerce_api_missing_tax_class_name', sprintf( __( 'Missing parameter %s', 'woocommerce' ), 'name' ), 400 );
			}

			$name      = sanitize_text_field( $data['name'] );
			$tax_class = WC_Tax::create_tax_class( $name );

			if ( is_wp_error( $tax_class ) ) {
				return new WP_Error( 'woocommerce_api_' . $tax_class->get_error_code(), $tax_class->get_error_message(), 401 );
			}

			do_action( 'woocommerce_api_create_tax_class', $tax_class['slug'], $data );

			$this->server->send_status( 201 );

			return array(
				'tax_class' => $tax_class,
			);
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Delete a tax class
	 *
	 * @since 2.5.0
	 *
	 * @param int $slug The tax class slug
	 *
	 * @return array|WP_Error
	 */
	public function delete_tax_class( $slug ) {
		global $wpdb;

		try {
			// Check permissions
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				throw new WC_API_Exception( 'woocommerce_api_user_cannot_delete_tax_class', __( 'You do not have permission to delete tax classes', 'woocommerce' ), 401 );
			}

			$slug      = sanitize_title( $slug );
			$tax_class = WC_Tax::get_tax_class_by( 'slug', $slug );
			$deleted   = WC_Tax::delete_tax_class_by( 'slug', $slug );

			if ( is_wp_error( $deleted ) || ! $deleted ) {
				throw new WC_API_Exception( 'woocommerce_api_cannot_delete_tax_class', __( 'Could not delete the tax class', 'woocommerce' ), 401 );
			}

			return array( 'message' => sprintf( __( 'Deleted %s', 'woocommerce' ), 'tax_class' ) );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Get the total number of tax classes
	 *
	 * @since 2.5.0
	 *
	 * @return array|WP_Error
	 */
	public function get_tax_classes_count() {
		try {
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				throw new WC_API_Exception( 'woocommerce_api_user_cannot_read_tax_classes_count', __( 'You do not have permission to read the tax classes count', 'woocommerce' ), 401 );
			}

			$total = count( WC_Tax::get_tax_classes() ) + 1; // +1 for Standard Rate

			return array( 'count' => $total );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}
}
