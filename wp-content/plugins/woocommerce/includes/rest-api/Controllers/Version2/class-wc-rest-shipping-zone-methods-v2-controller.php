<?php
/**
 * REST API Shipping Zone Methods controller
 *
 * Handles requests to the /shipping/zones/<id>/methods endpoint.
 *
 * @package WooCommerce\RestApi
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * REST API Shipping Zone Methods class.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_Shipping_Zones_Controller_Base
 */
class WC_REST_Shipping_Zone_Methods_V2_Controller extends WC_REST_Shipping_Zones_Controller_Base {

	/**
	 * Register the routes for Shipping Zone Methods.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace, '/' . $this->rest_base . '/(?P<zone_id>[\d]+)/methods', array(
				'args'   => array(
					'zone_id' => array(
						'description' => __( 'Unique ID for the zone.', 'woocommerce' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
					'args'                => array_merge(
						$this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ), array(
							'method_id' => array(
								'required'    => true,
								'readonly'    => false,
								'description' => __( 'Shipping method ID.', 'woocommerce' ),
							),
						)
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace, '/' . $this->rest_base . '/(?P<zone_id>[\d]+)/methods/(?P<instance_id>[\d]+)', array(
				'args'   => array(
					'zone_id'     => array(
						'description' => __( 'Unique ID for the zone.', 'woocommerce' ),
						'type'        => 'integer',
					),
					'instance_id' => array(
						'description' => __( 'Unique ID for the instance.', 'woocommerce' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'update_items_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'delete_items_permissions_check' ),
					'args'                => array(
						'force' => array(
							'default'     => false,
							'type'        => 'boolean',
							'description' => __( 'Whether to bypass trash and force deletion.', 'woocommerce' ),
						),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Get a single Shipping Zone Method.
	 *
	 * @param WP_REST_Request $request Request data.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_item( $request ) {
		$zone = $this->get_zone( $request['zone_id'] );

		if ( is_wp_error( $zone ) ) {
			return $zone;
		}

		$instance_id = (int) $request['instance_id'];
		$methods     = $zone->get_shipping_methods();
		$method      = false;

		foreach ( $methods as $method_obj ) {
			if ( $instance_id === $method_obj->instance_id ) {
				$method = $method_obj;
				break;
			}
		}

		if ( false === $method ) {
			return new WP_Error( 'woocommerce_rest_shipping_zone_method_invalid', __( 'Resource does not exist.', 'woocommerce' ), array( 'status' => 404 ) );
		}

		$data = $this->prepare_item_for_response( $method, $request );

		return rest_ensure_response( $data );
	}

	/**
	 * Get all Shipping Zone Methods.
	 *
	 * @param WP_REST_Request $request Request data.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_items( $request ) {
		$zone = $this->get_zone( $request['zone_id'] );

		if ( is_wp_error( $zone ) ) {
			return $zone;
		}

		$methods = $zone->get_shipping_methods();
		$data    = array();

		foreach ( $methods as $method_obj ) {
			$method = $this->prepare_item_for_response( $method_obj, $request );
			$data[] = $method;
		}

		return rest_ensure_response( $data );
	}

	/**
	 * Create a new shipping zone method instance.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Request|WP_Error
	 */
	public function create_item( $request ) {
		$method_id = $request['method_id'];
		$zone      = $this->get_zone( $request['zone_id'] );
		if ( is_wp_error( $zone ) ) {
			return $zone;
		}

		$instance_id = $zone->add_shipping_method( $method_id );
		$methods     = $zone->get_shipping_methods();
		$method      = false;
		foreach ( $methods as $method_obj ) {
			if ( $instance_id === $method_obj->instance_id ) {
				$method = $method_obj;
				break;
			}
		}

		if ( false === $method ) {
			return new WP_Error( 'woocommerce_rest_shipping_zone_not_created', __( 'Resource cannot be created.', 'woocommerce' ), array( 'status' => 500 ) );
		}

		$method = $this->update_fields( $instance_id, $method, $request );
		if ( is_wp_error( $method ) ) {
			return $method;
		}

		$data = $this->prepare_item_for_response( $method, $request );
		return rest_ensure_response( $data );
	}

	/**
	 * Delete a shipping method instance.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function delete_item( $request ) {
		$zone = $this->get_zone( $request['zone_id'] );
		if ( is_wp_error( $zone ) ) {
			return $zone;
		}

		$instance_id = (int) $request['instance_id'];
		$force       = $request['force'];

		$methods = $zone->get_shipping_methods();
		$method  = false;

		foreach ( $methods as $method_obj ) {
			if ( $instance_id === $method_obj->instance_id ) {
				$method = $method_obj;
				break;
			}
		}

		if ( false === $method ) {
			return new WP_Error( 'woocommerce_rest_shipping_zone_method_invalid', __( 'Resource does not exist.', 'woocommerce' ), array( 'status' => 404 ) );
		}

		$method = $this->update_fields( $instance_id, $method, $request );
		if ( is_wp_error( $method ) ) {
			return $method;
		}

		$request->set_param( 'context', 'view' );
		$response = $this->prepare_item_for_response( $method, $request );

		// Actually delete.
		if ( $force ) {
			$zone->delete_shipping_method( $instance_id );
		} else {
			return new WP_Error( 'rest_trash_not_supported', __( 'Shipping methods do not support trashing.', 'woocommerce' ), array( 'status' => 501 ) );
		}

		/**
		 * Fires after a product review is deleted via the REST API.
		 *
		 * @param object           $method
		 * @param WP_REST_Response $response        The response data.
		 * @param WP_REST_Request  $request         The request sent to the API.
		 */
		do_action( 'rest_delete_product_review', $method, $response, $request );

		return $response;
	}

	/**
	 * Update A Single Shipping Zone Method.
	 *
	 * @param WP_REST_Request $request Request data.
	 * @return WP_REST_Response|WP_Error
	 */
	public function update_item( $request ) {
		$zone = $this->get_zone( $request['zone_id'] );
		if ( is_wp_error( $zone ) ) {
			return $zone;
		}

		$instance_id = (int) $request['instance_id'];
		$methods     = $zone->get_shipping_methods();
		$method      = false;

		foreach ( $methods as $method_obj ) {
			if ( $instance_id === $method_obj->instance_id ) {
				$method = $method_obj;
				break;
			}
		}

		if ( false === $method ) {
			return new WP_Error( 'woocommerce_rest_shipping_zone_method_invalid', __( 'Resource does not exist.', 'woocommerce' ), array( 'status' => 404 ) );
		}

		$method = $this->update_fields( $instance_id, $method, $request );
		if ( is_wp_error( $method ) ) {
			return $method;
		}

		$data = $this->prepare_item_for_response( $method, $request );
		return rest_ensure_response( $data );
	}

	/**
	 * Updates settings, order, and enabled status on create.
	 *
	 * @param int                $instance_id Instance ID.
	 * @param WC_Shipping_Method $method      Shipping method data.
	 * @param WP_REST_Request    $request     Request data.
	 *
	 * @return WC_Shipping_Method
	 */
	public function update_fields( $instance_id, $method, $request ) {
		global $wpdb;

		// Update settings if present.
		if ( isset( $request['settings'] ) ) {
			$method->init_instance_settings();
			$instance_settings = $method->instance_settings;
			$errors_found      = false;
			foreach ( $method->get_instance_form_fields() as $key => $field ) {
				if ( isset( $request['settings'][ $key ] ) ) {
					if ( is_callable( array( $this, 'validate_setting_' . $field['type'] . '_field' ) ) ) {
						$value = $this->{'validate_setting_' . $field['type'] . '_field'}( $request['settings'][ $key ], $field );
					} else {
						$value = $this->validate_setting_text_field( $request['settings'][ $key ], $field );
					}
					if ( is_wp_error( $value ) ) {
						$errors_found = true;
						break;
					}
					$instance_settings[ $key ] = $value;
				}
			}

			if ( $errors_found ) {
				return new WP_Error( 'rest_setting_value_invalid', __( 'An invalid setting value was passed.', 'woocommerce' ), array( 'status' => 400 ) );
			}

			update_option( $method->get_instance_option_key(), apply_filters( 'woocommerce_shipping_' . $method->id . '_instance_settings_values', $instance_settings, $method ) );
		}

		// Update order.
		if ( isset( $request['order'] ) ) {
			$wpdb->update( "{$wpdb->prefix}woocommerce_shipping_zone_methods", array( 'method_order' => absint( $request['order'] ) ), array( 'instance_id' => absint( $instance_id ) ) );
			$method->method_order = absint( $request['order'] );
		}

		// Update if this method is enabled or not.
		if ( isset( $request['enabled'] ) ) {
			if ( $wpdb->update( "{$wpdb->prefix}woocommerce_shipping_zone_methods", array( 'is_enabled' => $request['enabled'] ), array( 'instance_id' => absint( $instance_id ) ) ) ) {
				do_action( 'woocommerce_shipping_zone_method_status_toggled', $instance_id, $method->id, $request['zone_id'], $request['enabled'] );
				$method->enabled = ( true === $request['enabled'] ? 'yes' : 'no' );
			}
		}

		return $method;
	}

	/**
	 * Prepare the Shipping Zone Method for the REST response.
	 *
	 * @param array           $item Shipping Zone Method.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response $response
	 */
	public function prepare_item_for_response( $item, $request ) {
		$method = array(
			'id'                 => $item->instance_id,
			'instance_id'        => $item->instance_id,
			'title'              => $item->instance_settings['title'],
			'order'              => $item->method_order,
			'enabled'            => ( 'yes' === $item->enabled ),
			'method_id'          => $item->id,
			'method_title'       => $item->method_title,
			'method_description' => $item->method_description,
			'settings'           => $this->get_settings( $item ),
		);

		$context = empty( $request['context'] ) ? 'view' : $request['context'];
		$data    = $this->add_additional_fields_to_object( $method, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		$response->add_links( $this->prepare_links( $request['zone_id'], $item->instance_id ) );

		$response = $this->prepare_response_for_collection( $response );

		return $response;
	}

	/**
	 * Return settings associated with this shipping zone method instance.
	 *
	 * @param WC_Shipping_Method $item Shipping method data.
	 *
	 * @return array
	 */
	public function get_settings( $item ) {
		$item->init_instance_settings();
		$settings = array();
		foreach ( $item->get_instance_form_fields() as $id => $field ) {
			$data = array(
				'id'          => $id,
				'label'       => $field['title'],
				'description' => empty( $field['description'] ) ? '' : $field['description'],
				'type'        => $field['type'],
				'value'       => $item->instance_settings[ $id ],
				'default'     => empty( $field['default'] ) ? '' : $field['default'],
				'tip'         => empty( $field['description'] ) ? '' : $field['description'],
				'placeholder' => empty( $field['placeholder'] ) ? '' : $field['placeholder'],
			);
			if ( ! empty( $field['options'] ) ) {
				$data['options'] = $field['options'];
			}
			$settings[ $id ] = $data;
		}
		return $settings;
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param int $zone_id Given Shipping Zone ID.
	 * @param int $instance_id Given Shipping Zone Method Instance ID.
	 * @return array Links for the given Shipping Zone Method.
	 */
	protected function prepare_links( $zone_id, $instance_id ) {
		$base  = '/' . $this->namespace . '/' . $this->rest_base . '/' . $zone_id;
		$links = array(
			'self'       => array(
				'href' => rest_url( $base . '/methods/' . $instance_id ),
			),
			'collection' => array(
				'href' => rest_url( $base . '/methods' ),
			),
			'describes'  => array(
				'href' => rest_url( $base ),
			),
		);

		return $links;
	}

	/**
	 * Get the Shipping Zone Methods schema, conforming to JSON Schema
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'shipping_zone_method',
			'type'       => 'object',
			'properties' => array(
				'id'                 => array(
					'description' => __( 'Shipping method instance ID.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'instance_id'        => array(
					'description' => __( 'Shipping method instance ID.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'title'              => array(
					'description' => __( 'Shipping method customer facing title.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'order'              => array(
					'description' => __( 'Shipping method sort order.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
				),
				'enabled'            => array(
					'description' => __( 'Shipping method enabled status.', 'woocommerce' ),
					'type'        => 'boolean',
					'context'     => array( 'view', 'edit' ),
				),
				'method_id'          => array(
					'description' => __( 'Shipping method ID.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'method_title'       => array(
					'description' => __( 'Shipping method title.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'method_description' => array(
					'description' => __( 'Shipping method description.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'settings'           => array(
					'description' => __( 'Shipping method settings.', 'woocommerce' ),
					'type'        => 'object',
					'context'     => array( 'view', 'edit' ),
					'properties'  => array(
						'id'          => array(
							'description' => __( 'A unique identifier for the setting.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true,
						),
						'label'       => array(
							'description' => __( 'A human readable label for the setting used in interfaces.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true,
						),
						'description' => array(
							'description' => __( 'A human readable description for the setting used in interfaces.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true,
						),
						'type'        => array(
							'description' => __( 'Type of setting.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
							'enum'        => array( 'text', 'email', 'number', 'color', 'password', 'textarea', 'select', 'multiselect', 'radio', 'image_width', 'checkbox' ),
							'readonly'    => true,
						),
						'value'       => array(
							'description' => __( 'Setting value.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'default'     => array(
							'description' => __( 'Default value for the setting.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true,
						),
						'tip'         => array(
							'description' => __( 'Additional help text shown to the user about the setting.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true,
						),
						'placeholder' => array(
							'description' => __( 'Placeholder text to be displayed in text inputs.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true,
						),
					),
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}
}
