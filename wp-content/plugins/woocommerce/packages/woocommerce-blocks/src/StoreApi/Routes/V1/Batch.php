<?php
namespace Automattic\WooCommerce\StoreApi\Routes\V1;

use Automattic\WooCommerce\StoreApi\Routes\RouteInterface;
use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Batch Route class.
 */
class Batch extends AbstractRoute implements RouteInterface {
	/**
	 * The route identifier.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'batch';

	/**
	 * The schema item identifier.
	 *
	 * @var string
	 */
	const SCHEMA_TYPE = 'batch';

	/**
	 * Get the path of this REST route.
	 *
	 * @return string
	 */
	public function get_path() {
		return '/batch';
	}

	/**
	 * Get arguments for this REST route.
	 *
	 * @return array An array of endpoints.
	 */
	public function get_args() {
		return array(
			'callback'            => [ $this, 'get_response' ],
			'methods'             => 'POST',
			'permission_callback' => '__return_true',
			'args'                => array(
				'validation' => array(
					'type'    => 'string',
					'enum'    => array( 'require-all-validate', 'normal' ),
					'default' => 'normal',
				),
				'requests'   => array(
					'required' => true,
					'type'     => 'array',
					'maxItems' => 25,
					'items'    => array(
						'type'       => 'object',
						'properties' => array(
							'method'  => array(
								'type'    => 'string',
								'enum'    => array( 'POST', 'PUT', 'PATCH', 'DELETE' ),
								'default' => 'POST',
							),
							'path'    => array(
								'type'     => 'string',
								'required' => true,
							),
							'body'    => array(
								'type'                 => 'object',
								'properties'           => array(),
								'additionalProperties' => true,
							),
							'headers' => array(
								'type'                 => 'object',
								'properties'           => array(),
								'additionalProperties' => array(
									'type'  => array( 'string', 'array' ),
									'items' => array(
										'type' => 'string',
									),
								),
							),
						),
					),
				),
			),
		);
	}

	/**
	 * Get the route response.
	 *
	 * @see WP_REST_Server::serve_batch_request_v1
	 * https://developer.wordpress.org/reference/classes/wp_rest_server/serve_batch_request_v1/
	 *
	 * @throws RouteException On error.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function get_response( WP_REST_Request $request ) {
		try {
			foreach ( $request['requests'] as $args ) {
				if ( ! stristr( $args['path'], 'wc/store' ) ) {
					throw new RouteException( 'woocommerce_rest_invalid_path', __( 'Invalid path provided.', 'woocommerce' ), 400 );
				}
			}
			$response = rest_get_server()->serve_batch_request_v1( $request );
		} catch ( RouteException $error ) {
			$response = $this->get_route_error_response( $error->getErrorCode(), $error->getMessage(), $error->getCode(), $error->getAdditionalData() );
		} catch ( \Exception $error ) {
			$response = $this->get_route_error_response( 'woocommerce_rest_unknown_server_error', $error->getMessage(), 500 );
		}

		if ( is_wp_error( $response ) ) {
			$response = $this->error_to_response( $response );
		}

		$nonce = wp_create_nonce( 'wc_store_api' );

		$response->header( 'Nonce', $nonce );
		$response->header( 'X-WC-Store-API-Nonce', $nonce );
		$response->header( 'Nonce-Timestamp', time() );
		$response->header( 'User-ID', get_current_user_id() );

		return $response;
	}
}
