<?php
/**
 * REST API MarketingCampaigns Controller
 *
 * Handles requests to /marketing/campaigns.
 */

namespace Automattic\WooCommerce\Admin\API;

use Automattic\WooCommerce\Admin\Marketing\MarketingCampaign;
use Automattic\WooCommerce\Admin\Marketing\MarketingChannels as MarketingChannelsService;
use Automattic\WooCommerce\Admin\Marketing\Price;
use WC_REST_Controller;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

defined( 'ABSPATH' ) || exit;

/**
 * MarketingCampaigns Controller.
 *
 * @internal
 * @extends WC_REST_Controller
 * @since x.x.x
 */
class MarketingCampaigns extends WC_REST_Controller {

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
	protected $rest_base = 'marketing/campaigns';

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

		// Aggregate the campaigns from all registered marketing channels.
		$responses = [];
		foreach ( $marketing_channels_service->get_registered_channels() as $channel ) {
			foreach ( $channel->get_campaigns() as $campaign ) {
				$response    = $this->prepare_item_for_response( $campaign, $request );
				$responses[] = $this->prepare_response_for_collection( $response );
			}
		}

		// Pagination.
		$page              = $request['page'];
		$items_per_page    = $request['per_page'];
		$offset            = ( $page - 1 ) * $items_per_page;
		$paginated_results = array_slice( $responses, $offset, $items_per_page );

		$response = rest_ensure_response( $paginated_results );

		$total_campaigns = count( $responses );
		$max_pages       = ceil( $total_campaigns / $items_per_page );
		$response->header( 'X-WP-Total', $total_campaigns );
		$response->header( 'X-WP-TotalPages', (int) $max_pages );

		// Add previous and next page links to response header.
		$request_params = $request->get_query_params();
		$base           = add_query_arg( urlencode_deep( $request_params ), rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ) );
		if ( $page > 1 ) {
			$prev_page = $page - 1;
			if ( $prev_page > $max_pages ) {
				$prev_page = $max_pages;
			}
			$prev_link = add_query_arg( 'page', $prev_page, $base );
			$response->link_header( 'prev', $prev_link );
		}
		if ( $max_pages > $page ) {
			$next_page = $page + 1;
			$next_link = add_query_arg( 'page', $next_page, $base );
			$response->link_header( 'next', $next_link );
		}

		return $response;
	}

	/**
	 * Prepares the item for the REST response.
	 *
	 * @param MarketingCampaign $item    WordPress representation of the item.
	 * @param WP_REST_Request   $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function prepare_item_for_response( $item, $request ) {
		$data = [
			'id'         => $item->get_id(),
			'channel'    => $item->get_type()->get_channel()->get_slug(),
			'title'      => $item->get_title(),
			'manage_url' => $item->get_manage_url(),
		];

		if ( $item->get_cost() instanceof Price ) {
			$data['cost'] = [
				'value'    => wc_format_decimal( $item->get_cost()->get_value() ),
				'currency' => $item->get_cost()->get_currency(),
			];
		}

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
			'title'      => 'marketing_campaign',
			'type'       => 'object',
			'properties' => [
				'id'         => [
					'description' => __( 'The unique identifier for the marketing campaign.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
				'channel'    => [
					'description' => __( 'The unique identifier for the marketing channel that this campaign belongs to.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
				'title'      => [
					'description' => __( 'Title of the marketing campaign.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
				'manage_url' => [
					'description' => __( 'URL to the campaign management page.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
				'cost'       => [
					'description' => __( 'Cost of the marketing campaign.', 'woocommerce' ),
					'context'     => [ 'view' ],
					'readonly'    => true,
					'type'        => 'object',
					'properties'  => [
						'value'    => [
							'type'     => 'string',
							'context'  => [ 'view' ],
							'readonly' => true,
						],
						'currency' => [
							'type'     => 'string',
							'context'  => [ 'view' ],
							'readonly' => true,
						],
					],
				],
			],
		];

		return $this->add_additional_fields_schema( $schema );
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


}
