<?php
/**
 * REST API Settings controller
 *
 * Handles requests to the /settings endpoints.
 *
 * @package WooCommerce\RestApi
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * REST API Settings controller class.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_Controller
 */
class WC_REST_Settings_V2_Controller extends WC_REST_Controller {

	/**
	 * WP REST API namespace/version.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v2';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'settings';

	/**
	 * Register routes.
	 *
	 * @since 3.0.0
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace, '/' . $this->rest_base, array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Get all settings groups items.
	 *
	 * @since  3.0.0
	 * @param  WP_REST_Request $request Request data.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		$groups = apply_filters( 'woocommerce_settings_groups', array() );
		if ( empty( $groups ) ) {
			return new WP_Error( 'rest_setting_groups_empty', __( 'No setting groups have been registered.', 'woocommerce' ), array( 'status' => 500 ) );
		}

		$defaults        = $this->group_defaults();
		$filtered_groups = array();
		foreach ( $groups as $group ) {
			$sub_groups = array();
			foreach ( $groups as $_group ) {
				if ( ! empty( $_group['parent_id'] ) && $group['id'] === $_group['parent_id'] ) {
					$sub_groups[] = $_group['id'];
				}
			}
			$group['sub_groups'] = $sub_groups;

			$group = wp_parse_args( $group, $defaults );
			if ( ! is_null( $group['id'] ) && ! is_null( $group['label'] ) ) {
				$group_obj  = $this->filter_group( $group );
				$group_data = $this->prepare_item_for_response( $group_obj, $request );
				$group_data = $this->prepare_response_for_collection( $group_data );

				$filtered_groups[] = $group_data;
			}
		}

		$response = rest_ensure_response( $filtered_groups );
		return $response;
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param string $group_id Group ID.
	 * @return array Links for the given group.
	 */
	protected function prepare_links( $group_id ) {
		$base  = '/' . $this->namespace . '/' . $this->rest_base;
		$links = array(
			'options' => array(
				'href' => rest_url( trailingslashit( $base ) . $group_id ),
			),
		);

		return $links;
	}

	/**
	 * Prepare a report sales object for serialization.
	 *
	 * @since  3.0.0
	 * @param array           $item Group object.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response $response Response data.
	 */
	public function prepare_item_for_response( $item, $request ) {
		$context = empty( $request['context'] ) ? 'view' : $request['context'];
		$data    = $this->add_additional_fields_to_object( $item, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		$response = rest_ensure_response( $data );

		$response->add_links( $this->prepare_links( $item['id'] ) );

		return $response;
	}

	/**
	 * Filters out bad values from the groups array/filter so we
	 * only return known values via the API.
	 *
	 * @since 3.0.0
	 * @param  array $group Group.
	 * @return array
	 */
	public function filter_group( $group ) {
		return array_intersect_key(
			$group,
			array_flip( array_filter( array_keys( $group ), array( $this, 'allowed_group_keys' ) ) )
		);
	}

	/**
	 * Callback for allowed keys for each group response.
	 *
	 * @since  3.0.0
	 * @param  string $key Key to check.
	 * @return boolean
	 */
	public function allowed_group_keys( $key ) {
		return in_array( $key, array( 'id', 'label', 'description', 'parent_id', 'sub_groups' ) );
	}

	/**
	 * Returns default settings for groups. null means the field is required.
	 *
	 * @since  3.0.0
	 * @return array
	 */
	protected function group_defaults() {
		return array(
			'id'          => null,
			'label'       => null,
			'description' => '',
			'parent_id'   => '',
			'sub_groups'  => array(),
		);
	}

	/**
	 * Makes sure the current user has access to READ the settings APIs.
	 *
	 * @since  3.0.0
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! wc_rest_check_manager_permissions( 'settings', 'read' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Get the groups schema, conforming to JSON Schema.
	 *
	 * @since  3.0.0
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'setting_group',
			'type'       => 'object',
			'properties' => array(
				'id'          => array(
					'description' => __( 'A unique identifier that can be used to link settings together.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'label'       => array(
					'description' => __( 'A human readable label for the setting used in interfaces.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'description' => array(
					'description' => __( 'A human readable description for the setting used in interfaces.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'parent_id'   => array(
					'description' => __( 'ID of parent grouping.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'sub_groups'  => array(
					'description' => __( 'IDs for settings sub groups.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}
}
