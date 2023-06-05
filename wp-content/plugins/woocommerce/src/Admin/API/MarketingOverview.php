<?php
/**
 * REST API Marketing Overview Controller
 *
 * Handles requests to /marketing/overview.
 */

namespace Automattic\WooCommerce\Admin\API;

use Automattic\WooCommerce\Admin\Marketing\InstalledExtensions;
use Automattic\WooCommerce\Admin\PluginsHelper;

defined( 'ABSPATH' ) || exit;

/**
 * Marketing Overview Controller.
 *
 * @internal
 * @extends WC_REST_Data_Controller
 */
class MarketingOverview extends \WC_REST_Data_Controller {

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
	protected $rest_base = 'marketing/overview';

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/activate-plugin',
			array(
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'activate_plugin' ),
					'permission_callback' => array( $this, 'install_plugins_permissions_check' ),
					'args'                => array(
						'plugin' => array(
							'required'          => true,
							'type'              => 'string',
							'validate_callback' => 'rest_validate_request_arg',
							'sanitize_callback' => 'sanitize_title_with_dashes',
						),
					),
				),
				'schema' => array( $this, 'get_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/installed-plugins',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_installed_plugins' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Return installed marketing extensions data.
	 *
	 * @param \WP_REST_Request $request Request data.
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function activate_plugin( $request ) {
		$plugin_slug = $request->get_param( 'plugin' );

		if ( ! PluginsHelper::is_plugin_installed( $plugin_slug ) ) {
			return new \WP_Error( 'woocommerce_rest_invalid_plugin', __( 'Invalid plugin.', 'woocommerce' ), 404 );
		}

		$result = activate_plugin( PluginsHelper::get_plugin_path_from_slug( $plugin_slug ) );

		if ( ! is_null( $result ) ) {
			return new \WP_Error( 'woocommerce_rest_invalid_plugin', __( 'The plugin could not be activated.', 'woocommerce' ), 500 );
		}

		// IMPORTANT - Don't return the active plugins data here.
		// Instead we will get that data in a separate request to ensure they are loaded.
		return rest_ensure_response(
			array(
				'status' => 'success',
			)
		);
	}

	/**
	 * Check if a given request has access to manage plugins.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_Error|boolean
	 */
	public function install_plugins_permissions_check( $request ) {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return new \WP_Error( 'woocommerce_rest_cannot_update', __( 'Sorry, you cannot manage plugins.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Return installed marketing extensions data.
	 *
	 * @param \WP_REST_Request $request Request data.
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function get_installed_plugins( $request ) {
		return rest_ensure_response( InstalledExtensions::get_data() );
	}

}
