<?php
/**
 * REST API MarketingChannels Controller
 *
 * Handles requests to /marketing/channels.
 */

namespace Automattic\WooCommerce\Admin\API;

use Automattic\WooCommerce\Admin\Marketing\MarketingChannelInterface;
use Automattic\WooCommerce\Admin\Marketing\MarketingChannels as MarketingChannelsService;
use WC_REST_Controller;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

defined( 'ABSPATH' ) || exit;

/**
 * MarketingChannels Controller.
 *
 * @internal
 * @extends WC_REST_Controller
 * @since x.x.x
 */
class MarketingChannels extends WC_REST_Controller {

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
	protected $rest_base = 'marketing/channels';

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
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Check whether a given request has permission to view marketing channels.
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
	 * Return installed marketing channels.
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

		$channels = $marketing_channels_service->get_registered_channels();

		$responses = [];
		foreach ( $channels as $item ) {
			$response    = $this->prepare_item_for_response( $item, $request );
			$responses[] = $this->prepare_response_for_collection( $response );
		}

		return rest_ensure_response( $responses );
	}

	/**
	 * Prepares the item for the REST response.
	 *
	 * @param MarketingChannelInterface $item    WordPress representation of the item.
	 * @param WP_REST_Request           $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function prepare_item_for_response( $item, $request ) {
		$data = [
			'slug'                    => $item->get_slug(),
			'is_setup_completed'      => $item->is_setup_completed(),
			'settings_url'            => $item->get_setup_url(),
			'name'                    => $item->get_name(),
			'description'             => $item->get_description(),
			'product_listings_status' => $item->get_product_listings_status(),
			'errors_count'            => $item->get_errors_count(),
			'icon'                    => $item->get_icon_url(),
		];

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
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
			'title'      => 'marketing_channel',
			'type'       => 'object',
			'properties' => [
				'slug'                    => [
					'description' => __( 'Unique identifier string for the marketing channel extension, also known as the plugin slug.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
				'name'                    => [
					'description' => __( 'Name of the marketing channel.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
				'description'             => [
					'description' => __( 'Description of the marketing channel.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
				'icon'                    => [
					'description' => __( 'Path to the channel icon.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
				'is_setup_completed'      => [
					'type'        => 'boolean',
					'description' => __( 'Whether or not the marketing channel is set up.', 'woocommerce' ),
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
				'settings_url'            => [
					'description' => __( 'URL to the settings page, or the link to complete the setup/onboarding if the channel has not been set up yet.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
				'product_listings_status' => [
					'description' => __( 'Status of the marketing channel\'s product listings.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
				'errors_count'            => [
					'description' => __( 'Number of channel issues/errors (e.g. account-related errors, product synchronization issues, etc.).', 'woocommerce' ),
					'type'        => 'string',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
			],
		];

		return $this->add_additional_fields_schema( $schema );
	}
}
