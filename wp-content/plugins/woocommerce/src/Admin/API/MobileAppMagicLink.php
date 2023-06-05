<?php
/**
 * REST API Data countries controller.
 *
 * Handles requests to the /mobile-app endpoint.
 */

namespace Automattic\WooCommerce\Admin\API;

defined( 'ABSPATH' ) || exit;

use Automattic\Jetpack\Connection\Manager as Jetpack_Connection_Manager;

/**
 * REST API Data countries controller class.
 *
 * @internal
 * @extends WC_REST_Data_Controller
 */
class MobileAppMagicLink extends \WC_REST_Data_Controller {

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
	protected $rest_base = 'mobile-app';

	/**
	 * Register routes.
	 *
	 * @since 7.0.0
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/send-magic-link',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'send_magic_link' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		parent::register_routes();
	}

	/**
	 * Sends request to generate magic link email.
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function send_magic_link() {
		// Attempt to get email from Jetpack.
		if ( class_exists( Jetpack_Connection_Manager::class ) ) {
			$jetpack_connection_manager = new Jetpack_Connection_Manager();
			if ( $jetpack_connection_manager->is_active() ) {
				if ( class_exists( 'Jetpack_IXR_Client' ) ) {
					$xml = new \Jetpack_IXR_Client(
						array(
							'user_id' => get_current_user_id(),
						)
					);

					$xml->query( 'jetpack.sendMobileMagicLink', array( 'app' => 'woocommerce' ) );
					if ( $xml->isError() ) {
						return new \WP_Error(
							'error_sending_mobile_magic_link',
							sprintf(
								'%s: %s',
								$xml->getErrorCode(),
								$xml->getErrorMessage()
							)
						);
					}

					return rest_ensure_response(
						array(
							'code' => 'success',
						)
					);
				}
			}
		}

		return new \WP_Error( 'jetpack_not_connected', __( 'Jetpack is not connected.', 'woocommerce' ) );
	}
}
