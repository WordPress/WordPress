<?php
/**
 * REST API Onboarding Profile Controller
 *
 * Handles requests to /onboarding/profile
 */

namespace Automattic\WooCommerce\Admin\API;

defined( 'ABSPATH' ) || exit;

use ActionScheduler;
use Automattic\WooCommerce\Admin\PluginsHelper;
use Automattic\WooCommerce\Admin\PluginsInstallLoggers\AsynPluginsInstallLogger;
use WC_REST_Data_Controller;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Onboarding Plugins controller.
 *
 * @internal
 * @extends WC_REST_Data_Controller
 */
class OnboardingPlugins extends WC_REST_Data_Controller {
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
	protected $rest_base = 'onboarding/plugins';

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/install-async',
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'install_async' ),
					'permission_callback' => array( $this, 'can_install_plugins' ),
					'args'                => array(
						'plugins' => array(
							'description'       => 'A list of plugins to install',
							'type'              => 'array',
							'items'             => 'string',
							'sanitize_callback' => function ( $value ) {
								return array_map(
									function ( $value ) {
										return sanitize_text_field( $value );
									},
									$value
								);
							},
							'required'          => true,
						),
					),
				),
				'schema' => array( $this, 'get_install_async_schema' ),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/install-and-activate',
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'install_and_activate' ),
					'permission_callback' => array( $this, 'can_install_and_activate_plugins' ),

				),
				'schema' => array( $this, 'get_install_activate_schema' ),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/scheduled-installs/(?P<job_id>\w+)',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_scheduled_installs' ),
					'permission_callback' => array( $this, 'can_install_plugins' ),
				),
				'schema' => array( $this, 'get_install_async_schema' ),
			)
		);
	}

	/**
	 * Install and activate a plugin.
	 *
	 * @param WP_REST_Request $request WP Request object.
	 *
	 * @return WP_REST_Response
	 */
	public function install_and_activate( WP_REST_Request $request ) {
		$response             = array();
		$response['install']  = PluginsHelper::install_plugins( $request->get_param( 'plugins' ) );
		$response['activate'] = PluginsHelper::activate_plugins( $response['install']['installed'] );

		return new WP_REST_Response( $response );
	}

	/**
	 * Queue plugin install request.
	 *
	 * @param WP_REST_Request $request WP_REST_Request object.
	 *
	 * @return array
	 */
	public function install_async( WP_REST_Request $request ) {
		$plugins = $request->get_param( 'plugins' );
		$job_id  = uniqid();

		WC()->queue()->add( 'woocommerce_plugins_install_async_callback', array( $plugins, $job_id ) );

		$plugin_status = array();
		foreach ( $plugins as $plugin ) {
			$plugin_status[ $plugin ] = array(
				'status' => 'pending',
				'errors' => array(),
			);
		}

		return array(
			'job_id'  => $job_id,
			'status'  => 'pending',
			'plugins' => $plugin_status,
		);
	}

	/**
	 * Returns current status of given job.
	 *
	 * @param WP_REST_Request $request WP_REST_Request object.
	 *
	 * @return array|WP_REST_Response
	 */
	public function get_scheduled_installs( WP_REST_Request $request ) {
		$job_id = $request->get_param( 'job_id' );

		$actions = WC()->queue()->search(
			array(
				'hook'    => 'woocommerce_plugins_install_async_callback',
				'search'  => $job_id,
				'orderby' => 'date',
				'order'   => 'DESC',
			)
		);

		$actions = array_filter(
			PluginsHelper::get_action_data( $actions ),
			function( $action ) use ( $job_id ) {
				return $action['job_id'] === $job_id;
			}
		);

		if ( empty( $actions ) ) {
			return new WP_REST_Response( null, 404 );
		}

		$response = array(
			'job_id' => $actions[0]['job_id'],
			'status' => $actions[0]['status'],
		);

		$option = get_option( 'woocommerce_onboarding_plugins_install_async_' . $job_id );
		if ( isset( $option['plugins'] ) ) {
			$response['plugins'] = $option['plugins'];
		}

		return $response;
	}

	/**
	 * Check whether the current user has permission to install plugins
	 *
	 * @return WP_Error|boolean
	 */
	public function can_install_plugins() {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return new WP_Error(
				'woocommerce_rest_cannot_update',
				__( 'Sorry, you cannot manage plugins.', 'woocommerce' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Check whether the current user has permission to install and activate plugins
	 *
	 * @return WP_Error|boolean
	 */
	public function can_install_and_activate_plugins() {
		if ( ! current_user_can( 'install_plugins' ) || ! current_user_can( 'activate_plugins' ) ) {
			return new WP_Error(
				'woocommerce_rest_cannot_update',
				__( 'Sorry, you cannot manage plugins.', 'woocommerce' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * JSON Schema for both install-async and scheduled-installs endpoints.
	 *
	 * @return array
	 */
	public function get_install_async_schema() {
		return array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'Install Async Schema',
			'type'       => 'object',
			'properties' => array(
				'type'       => 'object',
				'properties' => array(
					'job_id' => 'integer',
					'status' => array(
						'type' => 'string',
						'enum' => array( 'pending', 'complete', 'failed' ),
					),
				),
			),
		);
	}

	/**
	 * JSON Schema for install-and-activate endpoint.
	 *
	 * @return array
	 */
	public function get_install_activate_schema() {
		$error_schema = array(
			'type'              => 'object',
			'patternProperties' => array(
				'^.*$' => array(
					'type' => 'string',
				),
			),
			'items'             => array(
				'type' => 'string',
			),
		);

		$install_schema = array(
			'type'       => 'object',
			'properties' => array(
				'installed' => array(
					'type'  => 'array',
					'items' => array(
						'type' => 'string',
					),
				),
				'results'   => array(
					'type'  => 'array',
					'items' => array(
						'type' => 'string',
					),
				),
				'errors'    => array(
					'type'       => 'object',
					'properties' => array(
						'errors'     => $error_schema,
						'error_data' => $error_schema,
					),
				),
			),
		);

		$activate_schema = array(
			'type'       => 'object',
			'properties' => array(
				'activated' => array(
					'type'  => 'array',
					'items' => array(
						'type' => 'string',
					),
				),
				'active'    => array(
					'type'  => 'array',
					'items' => array(
						'type' => 'string',
					),
				),
				'errors'    => array(
					'type'       => 'object',
					'properties' => array(
						'errors'     => $error_schema,
						'error_data' => $error_schema,
					),
				),
			),
		);

		return array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'Install and Activate Schema',
			'type'       => 'object',
			'properties' => array(
				'type'       => 'object',
				'properties' => array(
					'install'  => $install_schema,
					'activate' => $activate_schema,
				),
			),
		);
	}
}
