<?php
/**
 * REST API MarketingCampaignTypes Controller
 *
 * Handles requests to /marketing/campaign-types.
 */

namespace Automattic\WooCommerce\Admin\API;

use Automattic\WooCommerce\Admin\Marketing\MarketingCampaignType;
use Automattic\WooCommerce\Admin\Marketing\MarketingChannels as MarketingChannelsService;
use WC_REST_Controller;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

defined( 'ABSPATH' ) || exit;

/**
 * MarketingCampaignTypes Controller.
 *
 * @internal
 * @extends WC_REST_Controller
 * @since x.x.x
 */
class MarketingCampaignTypes extends WC_REST_Controller {

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
	protected $rest_base = 'marketing/campaign-types';

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Retrieves the query params for the collections.
	 *
	 * @return array Query parameters for the collection.
	 */
	public function get_collection_params() {
		$params = parent::get_collection_params();
		unset( $params['search'] );

		return $params;
	}

	/**
	 * Check whether a given request has permission to view marketing campaigns.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! wc_rest_check_manager_permissions( 'settings', 'read' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Returns an aggregated array of marketing campaigns for all active marketing channels.
	 *
	 * @param WP_REST_Request $request Request data.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		/**
		 * MarketingChannels class.
		 *
		 * @var MarketingChannelsService $marketing_channels_service
		 */
		$marketing_channels_service = wc_get_container()->get( MarketingChannelsService::class );

		// Aggregate the supported campaign types from all registered marketing channels.
		$responses = [];
		foreach ( $marketing_channels_service->get_registered_channels() as $channel ) {
			foreach ( $channel->get_supported_campaign_types() as $campaign_type ) {
				$response    = $this->prepare_item_for_response( $campaign_type, $request );
				$responses[] = $this->prepare_response_for_collection( $response );
			}
		}

		return rest_ensure_response( $responses );
	}

	/**
	 * Prepares the item for the REST response.
	 *
	 * @param MarketingCampaignType $item    WordPress representation of the item.
	 * @param WP_REST_Request       $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function prepare_item_for_response( $item, $request ) {
		$data = [
			'id'          => $item->get_id(),
			'name'        => $item->get_name(),
			'description' => $item->get_description(),
			'channel'     => [
				'slug' => $item->get_channel()->get_slug(),
				'name' => $item->get_channel()->get_name(),
			],
			'create_url'  => $item->get_create_url(),
			'icon_url'    => $item->get_icon_url(),
		];

		$context = $request['context'] ?? 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
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
			'title'      => 'marketing_campaign_type',
			'type'       => 'object',
			'properties' => [
				'id'          => [
					'description' => __( 'The unique identifier for the marketing campaign type.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
				'name'        => [
					'description' => __( 'Name of the marketing campaign type.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
				'description' => [
					'description' => __( 'Description of the marketing campaign type.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
				'channel'     => [
					'description' => __( 'The marketing channel that this campaign type belongs to.', 'woocommerce' ),
					'type'        => 'object',
					'context'     => [ 'view' ],
					'readonly'    => true,
					'properties'  => [
						'slug' => [
							'description' => __( 'The unique identifier of the marketing channel that this campaign type belongs to.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => [ 'view' ],
							'readonly'    => true,
						],
						'name' => [
							'description' => __( 'The name of the marketing channel that this campaign type belongs to.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => [ 'view' ],
							'readonly'    => true,
						],
					],
				],
				'create_url'  => [
					'description' => __( 'URL to the create campaign page for this campaign type.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
				'icon_url'    => [
					'description' => __( 'URL to an image/icon for the campaign type.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
			],
		];

		return $this->add_additional_fields_schema( $schema );
	}


}
