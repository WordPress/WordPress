<?php
/**
 * REST API Themes Controller
 *
 * Handles requests to /themes
 */

namespace Automattic\WooCommerce\Admin\API;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Overrides\ThemeUpgrader;
use Automattic\WooCommerce\Admin\Overrides\ThemeUpgraderSkin;

/**
 * Themes controller.
 *
 * @internal
 * @extends WC_REST_Data_Controller
 */
class Themes extends \WC_REST_Data_Controller {
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
	protected $rest_base = 'themes';

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'upload_theme' ),
					'permission_callback' => array( $this, 'upload_theme_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Check whether a given request has permission to edit upload plugins/themes.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function upload_theme_permissions_check( $request ) {
		if ( ! current_user_can( 'upload_themes' ) ) {
			return new \WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you are not allowed to install themes on this site.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Upload and install a theme.
	 *
	 * @param  WP_REST_Request $request Request data.
	 * @return WP_Error|WP_REST_Response
	 */
	public function upload_theme( $request ) {
		if (
			! isset( $_FILES['pluginzip'] ) || ! isset( $_FILES['pluginzip']['tmp_name'] ) || ! is_uploaded_file( $_FILES['pluginzip']['tmp_name'] ) || ! is_file( $_FILES['pluginzip']['tmp_name'] ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash,  WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			return new \WP_Error( 'woocommerce_rest_invalid_file', __( 'Specified file failed upload test.', 'woocommerce' ) );
		}

		include_once ABSPATH . 'wp-admin/includes/file.php';
		include_once ABSPATH . '/wp-admin/includes/admin.php';
		include_once ABSPATH . '/wp-admin/includes/theme-install.php';
		include_once ABSPATH . '/wp-admin/includes/theme.php';
		include_once ABSPATH . '/wp-admin/includes/class-wp-upgrader.php';
		include_once ABSPATH . '/wp-admin/includes/class-theme-upgrader.php';

		$_GET['package'] = true;
		$file_upload     = new \File_Upload_Upgrader( 'pluginzip', 'package' );
		$upgrader        = new ThemeUpgrader( new ThemeUpgraderSkin() );
		$install         = $upgrader->install( $file_upload->package );

		if ( $install || is_wp_error( $install ) ) {
			$file_upload->cleanup();
		}

		if ( ! is_wp_error( $install ) && isset( $install['destination_name'] ) ) {
			$theme  = $install['destination_name'];
			$result = array(
				'status'  => 'success',
				'message' => $upgrader->strings['process_success'],
				'theme'   => $theme,
			);

			/**
			 * Fires when a theme is successfully installed.
			 *
			 * @param string $theme The theme name.
			 */
			do_action( 'woocommerce_theme_installed', $theme );
		} else {
			if ( is_wp_error( $install ) && $install->get_error_code() ) {
				$error_message = isset( $upgrader->strings[ $install->get_error_code() ] ) ? $upgrader->strings[ $install->get_error_code() ] : $install->get_error_data();
			} else {
				$error_message = $upgrader->strings['process_failed'];
			}

			$result = array(
				'status'  => 'error',
				'message' => $error_message,
			);
		}

		$response = $this->prepare_item_for_response( $result, $request );
		$data     = $this->prepare_response_for_collection( $response );

		return rest_ensure_response( $data );
	}


	/**
	 * Prepare the data object for response.
	 *
	 * @param object          $item Data object.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response $response Response data.
	 */
	public function prepare_item_for_response( $item, $request ) {
		$data     = $this->add_additional_fields_to_object( $item, $request );
		$data     = $this->filter_response_by_context( $data, 'view' );
		$response = rest_ensure_response( $data );

		/**
		 * Filter the list returned from the API.
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param array            $item     The original item.
		 * @param WP_REST_Request  $request  Request used to generate the response.
		 */
		return apply_filters( 'woocommerce_rest_prepare_themes', $response, $item, $request );
	}


	/**
	 * Get the schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'upload_theme',
			'type'       => 'object',
			'properties' => array(
				'status'  => array(
					'description' => __( 'Theme installation status.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'message' => array(
					'description' => __( 'Theme installation message.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'theme'   => array(
					'description' => __( 'Uploaded theme.', 'woocommerce' ),
					'type'        => 'object',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Get the query params for collections.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params['context']   = $this->get_context_param( array( 'default' => 'view' ) );
		$params['pluginzip'] = array(
			'description'       => __( 'A zip file of the theme to be uploaded.', 'woocommerce' ),
			'type'              => 'file',
			'validate_callback' => 'rest_validate_request_arg',
		);

		return apply_filters( 'woocommerce_rest_themes_collection_params', $params );
	}
}
