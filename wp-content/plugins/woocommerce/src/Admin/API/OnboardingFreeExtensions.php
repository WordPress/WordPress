<?php
/**
 * REST API Onboarding Free Extensions Controller
 *
 * Handles requests to /onboarding/free-extensions
 */

namespace Automattic\WooCommerce\Admin\API;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Internal\Admin\RemoteFreeExtensions\Init as RemoteFreeExtensions;
use WC_REST_Data_Controller;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Onboarding Payments Controller.
 *
 * @internal
 * @extends WC_REST_Data_Controller
 */
class OnboardingFreeExtensions extends WC_REST_Data_Controller {

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
	protected $rest_base = 'onboarding/free-extensions';

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_available_extensions' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Check whether a given request has permission to read onboarding profile data.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! wc_rest_check_manager_permissions( 'settings', 'read' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Return available payment methods.
	 *
	 * @param WP_REST_Request $request Request data.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_available_extensions( $request ) {
		$extensions = RemoteFreeExtensions::get_extensions();
		/**
		* Remove Jetpack when woocommerce_suggest_jetpack is false.
		 *
		 * @since 7.8
		*/
		if ( false === apply_filters( 'woocommerce_suggest_jetpack', true ) ) {
			foreach ( $extensions as &$extension ) {
				$extension['plugins'] = array_filter(
					$extension['plugins'],
					function( $plugin ) {
						return 'jetpack' !== $plugin->key;
					}
				);
			}
		}

		return new WP_REST_Response( $extensions );
	}

}
