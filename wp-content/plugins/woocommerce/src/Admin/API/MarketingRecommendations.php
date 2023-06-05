<?php
/**
 * REST API MarketingRecommendations Controller
 *
 * Handles requests to /marketing/recommendations.
 */

namespace Automattic\WooCommerce\Admin\API;

use Automattic\WooCommerce\Internal\Admin\Marketing\MarketingSpecs;
use WC_REST_Controller;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

defined( 'ABSPATH' ) || exit;

/**
 * MarketingRecommendations Controller.
 *
 * @internal
 * @extends WC_REST_Controller
 * @since x.x.x
 */
class MarketingRecommendations extends WC_REST_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc-admin';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'marketing/recommendations';

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_items' ],
					'permission_callback' => [ $this, 'get_items_permissions_check' ],
					'args'                => [
						'category' => [
							'type'              => 'string',
							'validate_callback' => 'rest_validate_request_arg',
							'sanitize_callback' => 'sanitize_title_with_dashes',
							'enum'              => [ 'channels', 'extensions' ],
							'required'          => true,
						],
					],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);
	}

	/**
	 * Check whether a given request has permission to view marketing recommendations.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot view marketing channels.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Retrieves a collection of recommendations.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		/**
		 * MarketingSpecs class.
		 *
		 * @var MarketingSpecs $marketing_specs
		 */
		$marketing_specs = wc_get_container()->get( MarketingSpecs::class );

		$category = $request->get_param( 'category' );
		if ( 'channels' === $category ) {
			$items = $marketing_specs->get_recommended_marketing_channels();
		} elseif ( 'extensions' === $category ) {
			$items = $marketing_specs->get_recommended_marketing_extensions_excluding_channels();
		} else {
			return new WP_Error( 'woocommerce_rest_invalid_category', __( 'The specified category for recommendations is invalid. Allowed values: "channels", "extensions".', 'woocommerce' ), array( 'status' => 400 ) );
		}

		$responses = [];
		foreach ( $items as $item ) {
			$response    = $this->prepare_item_for_response( $item, $request );
			$responses[] = $this->prepare_response_for_collection( $response );
		}

		return rest_ensure_response( $responses );
	}

	/**
	 * Prepares the item for the REST response.
	 *
	 * @param array           $item    WordPress representation of the item.
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function prepare_item_for_response( $item, $request ) {
		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $item, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		return rest_ensure_response( $data );
	}

	/**
	 * Retrieves the item's schema, conforming to JSON Schema.
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		$schema = [
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'marketing_recommendation',
			'type'       => 'object',
			'properties' => [
				'title'          => [
					'type'     => 'string',
					'context'  => [ 'view' ],
					'readonly' => true,
				],
				'description'    => [
					'type'     => 'string',
					'context'  => [ 'view' ],
					'readonly' => true,
				],
				'url'            => [
					'type'     => 'string',
					'context'  => [ 'view' ],
					'readonly' => true,
				],
				'direct_install' => [
					'type'     => 'string',
					'context'  => [ 'view' ],
					'readonly' => true,
				],
				'icon'           => [
					'type'     => 'string',
					'context'  => [ 'view' ],
					'readonly' => true,
				],
				'product'        => [
					'type'     => 'string',
					'context'  => [ 'view' ],
					'readonly' => true,
				],
				'plugin'         => [
					'type'     => 'string',
					'context'  => [ 'view' ],
					'readonly' => true,
				],
				'categories'     => [
					'type'     => 'array',
					'context'  => [ 'view' ],
					'readonly' => true,
					'items'    => [
						'type' => 'string',
					],
				],
				'subcategories'  => [
					'type'     => 'array',
					'context'  => [ 'view' ],
					'readonly' => true,
					'items'    => [
						'type'       => 'object',
						'context'    => [ 'view' ],
						'readonly'   => true,
						'properties' => [
							'slug' => [
								'type'     => 'string',
								'context'  => [ 'view' ],
								'readonly' => true,
							],
							'name' => [
								'type'     => 'string',
								'context'  => [ 'view' ],
								'readonly' => true,
							],
						],
					],
				],
				'tags'           => [
					'type'     => 'array',
					'context'  => [ 'view' ],
					'readonly' => true,
					'items'    => [
						'type'       => 'object',
						'context'    => [ 'view' ],
						'readonly'   => true,
						'properties' => [
							'slug' => [
								'type'     => 'string',
								'context'  => [ 'view' ],
								'readonly' => true,
							],
							'name' => [
								'type'     => 'string',
								'context'  => [ 'view' ],
								'readonly' => true,
							],
						],
					],
				],
			],
		];

		return $this->add_additional_fields_schema( $schema );
	}
}
