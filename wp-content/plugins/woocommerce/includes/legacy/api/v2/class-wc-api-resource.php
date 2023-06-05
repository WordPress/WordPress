<?php
/**
 * WooCommerce API Resource class
 *
 * Provides shared functionality for resource-specific API classes
 *
 * @author      WooThemes
 * @category    API
 * @package     WooCommerce\RestApi
 * @since       2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_API_Resource {

	/** @var WC_API_Server the API server */
	protected $server;

	/** @var string sub-classes override this to set a resource-specific base route */
	protected $base;

	/**
	 * Setup class
	 *
	 * @since 2.1
	 * @param WC_API_Server $server
	 */
	public function __construct( WC_API_Server $server ) {

		$this->server = $server;

		// automatically register routes for sub-classes
		add_filter( 'woocommerce_api_endpoints', array( $this, 'register_routes' ) );

		// maybe add meta to top-level resource responses
		foreach ( array( 'order', 'coupon', 'customer', 'product', 'report' ) as $resource ) {
			add_filter( "woocommerce_api_{$resource}_response", array( $this, 'maybe_add_meta' ), 15, 2 );
		}

		$response_names = array(
			'order',
			'coupon',
			'customer',
			'product',
			'report',
			'customer_orders',
			'customer_downloads',
			'order_note',
			'order_refund',
			'product_reviews',
			'product_category',
		);

		foreach ( $response_names as $name ) {

			/**
			 * Remove fields from responses when requests specify certain fields
			 * note these are hooked at a later priority so data added via
			 * filters (e.g. customer data to the order response) still has the
			 * fields filtered properly
			 */
			add_filter( "woocommerce_api_{$name}_response", array( $this, 'filter_response_fields' ), 20, 3 );
		}
	}

	/**
	 * Validate the request by checking:
	 *
	 * 1) the ID is a valid integer
	 * 2) the ID returns a valid post object and matches the provided post type
	 * 3) the current user has the proper permissions to read/edit/delete the post
	 *
	 * @since 2.1
	 * @param string|int $id the post ID
	 * @param string $type the post type, either `shop_order`, `shop_coupon`, or `product`
	 * @param string $context the context of the request, either `read`, `edit` or `delete`
	 * @return int|WP_Error valid post ID or WP_Error if any of the checks fails
	 */
	protected function validate_request( $id, $type, $context ) {

		if ( 'shop_order' === $type || 'shop_coupon' === $type || 'shop_webhook' === $type ) {
			$resource_name = str_replace( 'shop_', '', $type );
		} else {
			$resource_name = $type;
		}

		$id = absint( $id );

		// Validate ID
		if ( empty( $id ) ) {
			return new WP_Error( "woocommerce_api_invalid_{$resource_name}_id", sprintf( __( 'Invalid %s ID', 'woocommerce' ), $type ), array( 'status' => 404 ) );
		}

		// Only custom post types have per-post type/permission checks
		if ( 'customer' !== $type ) {

			$post = get_post( $id );

			if ( null === $post ) {
				return new WP_Error( "woocommerce_api_no_{$resource_name}_found", sprintf( __( 'No %1$s found with the ID equal to %2$s', 'woocommerce' ), $resource_name, $id ), array( 'status' => 404 ) );
			}

			// For checking permissions, product variations are the same as the product post type
			$post_type = ( 'product_variation' === $post->post_type ) ? 'product' : $post->post_type;

			// Validate post type
			if ( $type !== $post_type ) {
				return new WP_Error( "woocommerce_api_invalid_{$resource_name}", sprintf( __( 'Invalid %s', 'woocommerce' ), $resource_name ), array( 'status' => 404 ) );
			}

			// Validate permissions
			switch ( $context ) {

				case 'read':
					if ( ! $this->is_readable( $post ) ) {
						return new WP_Error( "woocommerce_api_user_cannot_read_{$resource_name}", sprintf( __( 'You do not have permission to read this %s', 'woocommerce' ), $resource_name ), array( 'status' => 401 ) );
					}
					break;

				case 'edit':
					if ( ! $this->is_editable( $post ) ) {
						return new WP_Error( "woocommerce_api_user_cannot_edit_{$resource_name}", sprintf( __( 'You do not have permission to edit this %s', 'woocommerce' ), $resource_name ), array( 'status' => 401 ) );
					}
					break;

				case 'delete':
					if ( ! $this->is_deletable( $post ) ) {
						return new WP_Error( "woocommerce_api_user_cannot_delete_{$resource_name}", sprintf( __( 'You do not have permission to delete this %s', 'woocommerce' ), $resource_name ), array( 'status' => 401 ) );
					}
					break;
			}
		}

		return $id;
	}

	/**
	 * Add common request arguments to argument list before WP_Query is run
	 *
	 * @since 2.1
	 * @param array $base_args required arguments for the query (e.g. `post_type`, etc)
	 * @param array $request_args arguments provided in the request
	 * @return array
	 */
	protected function merge_query_args( $base_args, $request_args ) {

		$args = array();

		// date
		if ( ! empty( $request_args['created_at_min'] ) || ! empty( $request_args['created_at_max'] ) || ! empty( $request_args['updated_at_min'] ) || ! empty( $request_args['updated_at_max'] ) ) {

			$args['date_query'] = array();

			// resources created after specified date
			if ( ! empty( $request_args['created_at_min'] ) ) {
				$args['date_query'][] = array( 'column' => 'post_date_gmt', 'after' => $this->server->parse_datetime( $request_args['created_at_min'] ), 'inclusive' => true );
			}

			// resources created before specified date
			if ( ! empty( $request_args['created_at_max'] ) ) {
				$args['date_query'][] = array( 'column' => 'post_date_gmt', 'before' => $this->server->parse_datetime( $request_args['created_at_max'] ), 'inclusive' => true );
			}

			// resources updated after specified date
			if ( ! empty( $request_args['updated_at_min'] ) ) {
				$args['date_query'][] = array( 'column' => 'post_modified_gmt', 'after' => $this->server->parse_datetime( $request_args['updated_at_min'] ), 'inclusive' => true );
			}

			// resources updated before specified date
			if ( ! empty( $request_args['updated_at_max'] ) ) {
				$args['date_query'][] = array( 'column' => 'post_modified_gmt', 'before' => $this->server->parse_datetime( $request_args['updated_at_max'] ), 'inclusive' => true );
			}
		}

		// search
		if ( ! empty( $request_args['q'] ) ) {
			$args['s'] = $request_args['q'];
		}

		// resources per response
		if ( ! empty( $request_args['limit'] ) ) {
			$args['posts_per_page'] = $request_args['limit'];
		}

		// resource offset
		if ( ! empty( $request_args['offset'] ) ) {
			$args['offset'] = $request_args['offset'];
		}

		// order (ASC or DESC, ASC by default)
		if ( ! empty( $request_args['order'] ) ) {
			$args['order'] = $request_args['order'];
		}

		// orderby
		if ( ! empty( $request_args['orderby'] ) ) {
			$args['orderby'] = $request_args['orderby'];

			// allow sorting by meta value
			if ( ! empty( $request_args['orderby_meta_key'] ) ) {
				$args['meta_key'] = $request_args['orderby_meta_key'];
			}
		}

		// allow post status change
		if ( ! empty( $request_args['post_status'] ) ) {
			$args['post_status'] = $request_args['post_status'];
			unset( $request_args['post_status'] );
		}

		// filter by a list of post id
		if ( ! empty( $request_args['in'] ) ) {
			$args['post__in'] = explode( ',', $request_args['in'] );
			unset( $request_args['in'] );
		}

		// filter by a list of post id
		if ( ! empty( $request_args['in'] ) ) {
			$args['post__in'] = explode( ',', $request_args['in'] );
			unset( $request_args['in'] );
		}

		// resource page
		$args['paged'] = ( isset( $request_args['page'] ) ) ? absint( $request_args['page'] ) : 1;

		$args = apply_filters( 'woocommerce_api_query_args', $args, $request_args );

		return array_merge( $base_args, $args );
	}

	/**
	 * Add meta to resources when requested by the client. Meta is added as a top-level
	 * `<resource_name>_meta` attribute (e.g. `order_meta`) as a list of key/value pairs
	 *
	 * @since 2.1
	 * @param array $data the resource data
	 * @param object $resource the resource object (e.g WC_Order)
	 * @return mixed
	 */
	public function maybe_add_meta( $data, $resource ) {

		if ( isset( $this->server->params['GET']['filter']['meta'] ) && 'true' === $this->server->params['GET']['filter']['meta'] && is_object( $resource ) ) {

			// don't attempt to add meta more than once
			if ( preg_grep( '/[a-z]+_meta/', array_keys( $data ) ) ) {
				return $data;
			}

			// define the top-level property name for the meta
			switch ( get_class( $resource ) ) {

				case 'WC_Order':
					$meta_name = 'order_meta';
					break;

				case 'WC_Coupon':
					$meta_name = 'coupon_meta';
					break;

				case 'WP_User':
					$meta_name = 'customer_meta';
					break;

				default:
					$meta_name = 'product_meta';
					break;
			}

			if ( is_a( $resource, 'WP_User' ) ) {

				// customer meta
				$meta = (array) get_user_meta( $resource->ID );

			} else {

				// coupon/order/product meta
				$meta = (array) get_post_meta( $resource->get_id() );
			}

			foreach ( $meta as $meta_key => $meta_value ) {

				// don't add hidden meta by default
				if ( ! is_protected_meta( $meta_key ) ) {
					$data[ $meta_name ][ $meta_key ] = maybe_unserialize( $meta_value[0] );
				}
			}
		}

		return $data;
	}

	/**
	 * Restrict the fields included in the response if the request specified certain only certain fields should be returned
	 *
	 * @since 2.1
	 * @param array $data the response data
	 * @param object $resource the object that provided the response data, e.g. WC_Coupon or WC_Order
	 * @param array|string the requested list of fields to include in the response
	 * @return array response data
	 */
	public function filter_response_fields( $data, $resource, $fields ) {

		if ( ! is_array( $data ) || empty( $fields ) ) {
			return $data;
		}

		$fields = explode( ',', $fields );
		$sub_fields = array();

		// get sub fields
		foreach ( $fields as $field ) {

			if ( false !== strpos( $field, '.' ) ) {

				list( $name, $value ) = explode( '.', $field );

				$sub_fields[ $name ] = $value;
			}
		}

		// iterate through top-level fields
		foreach ( $data as $data_field => $data_value ) {

			// if a field has sub-fields and the top-level field has sub-fields to filter
			if ( is_array( $data_value ) && in_array( $data_field, array_keys( $sub_fields ) ) ) {

				// iterate through each sub-field
				foreach ( $data_value as $sub_field => $sub_field_value ) {

					// remove non-matching sub-fields
					if ( ! in_array( $sub_field, $sub_fields ) ) {
						unset( $data[ $data_field ][ $sub_field ] );
					}
				}
			} else {

				// remove non-matching top-level fields
				if ( ! in_array( $data_field, $fields ) ) {
					unset( $data[ $data_field ] );
				}
			}
		}

		return $data;
	}

	/**
	 * Delete a given resource
	 *
	 * @since 2.1
	 * @param int $id the resource ID
	 * @param string $type the resource post type, or `customer`
	 * @param bool $force true to permanently delete resource, false to move to trash (not supported for `customer`)
	 * @return array|WP_Error
	 */
	protected function delete( $id, $type, $force = false ) {

		if ( 'shop_order' === $type || 'shop_coupon' === $type ) {
			$resource_name = str_replace( 'shop_', '', $type );
		} else {
			$resource_name = $type;
		}

		if ( 'customer' === $type ) {

			$result = wp_delete_user( $id );

			if ( $result ) {
				return array( 'message' => __( 'Permanently deleted customer', 'woocommerce' ) );
			} else {
				return new WP_Error( 'woocommerce_api_cannot_delete_customer', __( 'The customer cannot be deleted', 'woocommerce' ), array( 'status' => 500 ) );
			}
		} else {

			// delete order/coupon/webhook
			$result = ( $force ) ? wp_delete_post( $id, true ) : wp_trash_post( $id );

			if ( ! $result ) {
				return new WP_Error( "woocommerce_api_cannot_delete_{$resource_name}", sprintf( __( 'This %s cannot be deleted', 'woocommerce' ), $resource_name ), array( 'status' => 500 ) );
			}

			if ( $force ) {
				return array( 'message' => sprintf( __( 'Permanently deleted %s', 'woocommerce' ), $resource_name ) );
			} else {
				$this->server->send_status( '202' );

				return array( 'message' => sprintf( __( 'Deleted %s', 'woocommerce' ), $resource_name ) );
			}
		}
	}


	/**
	 * Checks if the given post is readable by the current user
	 *
	 * @since 2.1
	 * @see WC_API_Resource::check_permission()
	 * @param WP_Post|int $post
	 * @return bool
	 */
	protected function is_readable( $post ) {

		return $this->check_permission( $post, 'read' );
	}

	/**
	 * Checks if the given post is editable by the current user
	 *
	 * @since 2.1
	 * @see WC_API_Resource::check_permission()
	 * @param WP_Post|int $post
	 * @return bool
	 */
	protected function is_editable( $post ) {

		return $this->check_permission( $post, 'edit' );

	}

	/**
	 * Checks if the given post is deletable by the current user
	 *
	 * @since 2.1
	 * @see WC_API_Resource::check_permission()
	 * @param WP_Post|int $post
	 * @return bool
	 */
	protected function is_deletable( $post ) {

		return $this->check_permission( $post, 'delete' );
	}

	/**
	 * Checks the permissions for the current user given a post and context
	 *
	 * @since 2.1
	 * @param WP_Post|int $post
	 * @param string $context the type of permission to check, either `read`, `write`, or `delete`
	 * @return bool true if the current user has the permissions to perform the context on the post
	 */
	private function check_permission( $post, $context ) {

		if ( ! is_a( $post, 'WP_Post' ) ) {
			$post = get_post( $post );
		}

		if ( is_null( $post ) ) {
			return false;
		}

		$post_type = get_post_type_object( $post->post_type );

		if ( 'read' === $context ) {
			return ( 'revision' !== $post->post_type && current_user_can( $post_type->cap->read_private_posts, $post->ID ) );
		} elseif ( 'edit' === $context ) {
			return current_user_can( $post_type->cap->edit_post, $post->ID );
		} elseif ( 'delete' === $context ) {
			return current_user_can( $post_type->cap->delete_post, $post->ID );
		} else {
			return false;
		}
	}
}
