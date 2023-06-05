<?php
/**
 * REST API WC System Status controller
 *
 * Handles requests to the /system_status endpoint.
 *
 * @package WooCommerce\RestApi
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Internal\WCCom\ConnectionHelper;
use Automattic\WooCommerce\Internal\ProductDownloads\ApprovedDirectories\Register as Download_Directories;
use Automattic\WooCommerce\Internal\DataStores\Orders\DataSynchronizer as Order_DataSynchronizer;
use Automattic\WooCommerce\Utilities\OrderUtil;
/**
 * System status controller class.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_Controller
 */
class WC_REST_System_Status_V2_Controller extends WC_REST_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v2';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'system_status';

	/**
	 * Register cache cleaner
	 *
	 * Handles all the cache cleaning for this endpoint. We need to register
	 * these functions before the routes are registered, so this function gets
	 * called from Server.php
	 */
	public static function register_cache_clean() {
		// Clear the theme cache if we switch themes or our theme is upgraded.
		add_action( 'switch_theme', array( __CLASS__, 'clean_theme_cache' ) );
		add_action( 'activate_plugin', array( __CLASS__, 'clean_plugin_cache' ) );
		add_action( 'deactivate_plugin', array( __CLASS__, 'clean_plugin_cache' ) );
		add_action(
			'upgrader_process_complete',
			function( $upgrader, $extra ) {
				if ( ! $extra || ! $extra['type'] ) {
					return;
				}

				// Clear the cache if woocommerce is updated.
				if ( 'plugin' === $extra['type'] ) {
					\WC_REST_System_Status_V2_Controller::clean_theme_cache();
					\WC_REST_System_Status_V2_Controller::clean_plugin_cache();
					return;
				}

				if ( 'theme' === $extra['type'] ) {
					\WC_REST_System_Status_V2_Controller::clean_theme_cache();
					return;
				}
			},
			10,
			2
		);
	}

	/**
	 * Register the route for /system_status
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Check whether a given request has permission to view system status.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! wc_rest_check_manager_permissions( 'system_status', 'read' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}
		return true;
	}

	/**
	 * Get a system status info, by section.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		$fields   = $this->get_fields_for_response( $request );
		$mappings = $this->get_item_mappings_per_fields( $fields );
		$response = $this->prepare_item_for_response( $mappings, $request );

		return rest_ensure_response( $response );
	}

	/**
	 * Get the system status schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'system_status',
			'type'       => 'object',
			'properties' => array(
				'environment'        => array(
					'description' => __( 'Environment.', 'woocommerce' ),
					'type'        => 'object',
					'context'     => array( 'view' ),
					'readonly'    => true,
					'properties'  => array(
						'home_url'                  => array(
							'description' => __( 'Home URL.', 'woocommerce' ),
							'type'        => 'string',
							'format'      => 'uri',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'site_url'                  => array(
							'description' => __( 'Site URL.', 'woocommerce' ),
							'type'        => 'string',
							'format'      => 'uri',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'version'                   => array(
							'description' => __( 'WooCommerce version.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'log_directory'             => array(
							'description' => __( 'Log directory.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'log_directory_writable'    => array(
							'description' => __( 'Is log directory writable?', 'woocommerce' ),
							'type'        => 'boolean',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'wp_version'                => array(
							'description' => __( 'WordPress version.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'wp_multisite'              => array(
							'description' => __( 'Is WordPress multisite?', 'woocommerce' ),
							'type'        => 'boolean',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'wp_memory_limit'           => array(
							'description' => __( 'WordPress memory limit.', 'woocommerce' ),
							'type'        => 'integer',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'wp_debug_mode'             => array(
							'description' => __( 'Is WordPress debug mode active?', 'woocommerce' ),
							'type'        => 'boolean',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'wp_cron'                   => array(
							'description' => __( 'Are WordPress cron jobs enabled?', 'woocommerce' ),
							'type'        => 'boolean',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'language'                  => array(
							'description' => __( 'WordPress language.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'server_info'               => array(
							'description' => __( 'Server info.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'php_version'               => array(
							'description' => __( 'PHP version.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'php_post_max_size'         => array(
							'description' => __( 'PHP post max size.', 'woocommerce' ),
							'type'        => 'integer',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'php_max_execution_time'    => array(
							'description' => __( 'PHP max execution time.', 'woocommerce' ),
							'type'        => 'integer',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'php_max_input_vars'        => array(
							'description' => __( 'PHP max input vars.', 'woocommerce' ),
							'type'        => 'integer',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'curl_version'              => array(
							'description' => __( 'cURL version.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'suhosin_installed'         => array(
							'description' => __( 'Is SUHOSIN installed?', 'woocommerce' ),
							'type'        => 'boolean',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'max_upload_size'           => array(
							'description' => __( 'Max upload size.', 'woocommerce' ),
							'type'        => 'integer',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'mysql_version'             => array(
							'description' => __( 'MySQL version.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'mysql_version_string'      => array(
							'description' => __( 'MySQL version string.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'default_timezone'          => array(
							'description' => __( 'Default timezone.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'fsockopen_or_curl_enabled' => array(
							'description' => __( 'Is fsockopen/cURL enabled?', 'woocommerce' ),
							'type'        => 'boolean',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'soapclient_enabled'        => array(
							'description' => __( 'Is SoapClient class enabled?', 'woocommerce' ),
							'type'        => 'boolean',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'domdocument_enabled'       => array(
							'description' => __( 'Is DomDocument class enabled?', 'woocommerce' ),
							'type'        => 'boolean',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'gzip_enabled'              => array(
							'description' => __( 'Is GZip enabled?', 'woocommerce' ),
							'type'        => 'boolean',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'mbstring_enabled'          => array(
							'description' => __( 'Is mbstring enabled?', 'woocommerce' ),
							'type'        => 'boolean',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'remote_post_successful'    => array(
							'description' => __( 'Remote POST successful?', 'woocommerce' ),
							'type'        => 'boolean',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'remote_post_response'      => array(
							'description' => __( 'Remote POST response.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'remote_get_successful'     => array(
							'description' => __( 'Remote GET successful?', 'woocommerce' ),
							'type'        => 'boolean',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'remote_get_response'       => array(
							'description' => __( 'Remote GET response.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
					),
				),
				'database'           => array(
					'description' => __( 'Database.', 'woocommerce' ),
					'type'        => 'object',
					'context'     => array( 'view' ),
					'readonly'    => true,
					'properties'  => array(
						'wc_database_version'    => array(
							'description' => __( 'WC database version.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'database_prefix'        => array(
							'description' => __( 'Database prefix.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'maxmind_geoip_database' => array(
							'description' => __( 'MaxMind GeoIP database.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'database_tables'        => array(
							'description' => __( 'Database tables.', 'woocommerce' ),
							'type'        => 'array',
							'context'     => array( 'view' ),
							'readonly'    => true,
							'items'       => array(
								'type' => 'string',
							),
						),
					),
				),
				'active_plugins'     => array(
					'description' => __( 'Active plugins.', 'woocommerce' ),
					'type'        => 'array',
					'context'     => array( 'view' ),
					'readonly'    => true,
					'items'       => array(
						'type' => 'string',
					),
				),
				'inactive_plugins'   => array(
					'description' => __( 'Inactive plugins.', 'woocommerce' ),
					'type'        => 'array',
					'context'     => array( 'view' ),
					'readonly'    => true,
					'items'       => array(
						'type' => 'string',
					),
				),
				'dropins_mu_plugins' => array(
					'description' => __( 'Dropins & MU plugins.', 'woocommerce' ),
					'type'        => 'array',
					'context'     => array( 'view' ),
					'readonly'    => true,
					'items'       => array(
						'type' => 'string',
					),
				),
				'theme'              => array(
					'description' => __( 'Theme.', 'woocommerce' ),
					'type'        => 'object',
					'context'     => array( 'view' ),
					'readonly'    => true,
					'properties'  => array(
						'name'                    => array(
							'description' => __( 'Theme name.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'version'                 => array(
							'description' => __( 'Theme version.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'version_latest'          => array(
							'description' => __( 'Latest version of theme.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'author_url'              => array(
							'description' => __( 'Theme author URL.', 'woocommerce' ),
							'type'        => 'string',
							'format'      => 'uri',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'is_child_theme'          => array(
							'description' => __( 'Is this theme a child theme?', 'woocommerce' ),
							'type'        => 'boolean',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'has_woocommerce_support' => array(
							'description' => __( 'Does the theme declare WooCommerce support?', 'woocommerce' ),
							'type'        => 'boolean',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'has_woocommerce_file'    => array(
							'description' => __( 'Does the theme have a woocommerce.php file?', 'woocommerce' ),
							'type'        => 'boolean',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'has_outdated_templates'  => array(
							'description' => __( 'Does this theme have outdated templates?', 'woocommerce' ),
							'type'        => 'boolean',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'overrides'               => array(
							'description' => __( 'Template overrides.', 'woocommerce' ),
							'type'        => 'array',
							'context'     => array( 'view' ),
							'readonly'    => true,
							'items'       => array(
								'type' => 'string',
							),
						),
						'parent_name'             => array(
							'description' => __( 'Parent theme name.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'parent_version'          => array(
							'description' => __( 'Parent theme version.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'parent_author_url'       => array(
							'description' => __( 'Parent theme author URL.', 'woocommerce' ),
							'type'        => 'string',
							'format'      => 'uri',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
					),
				),
				'settings'           => array(
					'description' => __( 'Settings.', 'woocommerce' ),
					'type'        => 'object',
					'context'     => array( 'view' ),
					'readonly'    => true,
					'properties'  => array(
						'api_enabled'                    => array(
							'description' => __( 'REST API enabled?', 'woocommerce' ),
							'type'        => 'boolean',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'force_ssl'                      => array(
							'description' => __( 'SSL forced?', 'woocommerce' ),
							'type'        => 'boolean',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'currency'                       => array(
							'description' => __( 'Currency.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'currency_symbol'                => array(
							'description' => __( 'Currency symbol.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'currency_position'              => array(
							'description' => __( 'Currency position.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'thousand_separator'             => array(
							'description' => __( 'Thousand separator.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'decimal_separator'              => array(
							'description' => __( 'Decimal separator.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'number_of_decimals'             => array(
							'description' => __( 'Number of decimals.', 'woocommerce' ),
							'type'        => 'integer',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'geolocation_enabled'            => array(
							'description' => __( 'Geolocation enabled?', 'woocommerce' ),
							'type'        => 'boolean',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'taxonomies'                     => array(
							'description' => __( 'Taxonomy terms for product/order statuses.', 'woocommerce' ),
							'type'        => 'array',
							'context'     => array( 'view' ),
							'readonly'    => true,
							'items'       => array(
								'type' => 'string',
							),
						),
						'product_visibility_terms'       => array(
							'description' => __( 'Terms in the product visibility taxonomy.', 'woocommerce' ),
							'type'        => 'array',
							'context'     => array( 'view' ),
							'readonly'    => true,
							'items'       => array(
								'type' => 'string',
							),
						),
						'wccom_connected'                => array(
							'description' => __( 'Is store connected to WooCommerce.com?', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'enforce_approved_download_dirs' => array(
							'description' => __( 'Enforce approved download directories?', 'woocommerce' ),
							'type'        => 'boolean',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'HPOS_feature_screen_enabled'    => array(
							'description' => __( 'Is HPOS feature screen enabled?', 'woocommerce' ),
							'type'        => 'boolean',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'HPOS_enabled'                   => array(
							'description' => __( 'Is HPOS enabled?', 'woocommerce' ),
							'type'        => 'boolean',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'order_datastore'                => array(
							'description' => __( 'Order datastore.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'HPOS_sync_enabled'              => array(
							'description' => __( 'Is HPOS sync enabled?', 'woocommerce' ),
							'type'        => 'boolean',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
					),
				),
				'security'           => array(
					'description' => __( 'Security.', 'woocommerce' ),
					'type'        => 'object',
					'context'     => array( 'view' ),
					'readonly'    => true,
					'properties'  => array(
						'secure_connection' => array(
							'description' => __( 'Is the connection to your store secure?', 'woocommerce' ),
							'type'        => 'boolean',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'hide_errors'       => array(
							'description' => __( 'Hide errors from visitors?', 'woocommerce' ),
							'type'        => 'boolean',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
					),
				),
				'pages'              => array(
					'description' => __( 'WooCommerce pages.', 'woocommerce' ),
					'type'        => 'array',
					'context'     => array( 'view' ),
					'readonly'    => true,
					'items'       => array(
						'type' => 'string',
					),
				),
				'post_type_counts'   => array(
					'description' => __( 'Total post count.', 'woocommerce' ),
					'type'        => 'array',
					'context'     => array( 'view' ),
					'readonly'    => true,
					'items'       => array(
						'type' => 'string',
					),
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Return an array of sections and the data associated with each.
	 *
	 * @deprecated 3.9.0
	 * @return array
	 */
	public function get_item_mappings() {
		return array(
			'environment'        => $this->get_environment_info(),
			'database'           => $this->get_database_info(),
			'active_plugins'     => $this->get_active_plugins(),
			'inactive_plugins'   => $this->get_inactive_plugins(),
			'dropins_mu_plugins' => $this->get_dropins_mu_plugins(),
			'theme'              => $this->get_theme_info(),
			'settings'           => $this->get_settings(),
			'security'           => $this->get_security_info(),
			'pages'              => $this->get_pages(),
			'post_type_counts'   => $this->get_post_type_counts(),
		);
	}

	/**
	 * Return an array of sections and the data associated with each.
	 *
	 * @since 3.9.0
	 * @param array $fields List of fields to be included on the response.
	 * @return array
	 */
	public function get_item_mappings_per_fields( $fields ) {
		$items = array();

		foreach ( $fields as $field ) {
			// If we're looking for a sub-property, like environment.version we need
			// to extract the first-level property here so we know which function to run.
			list( $prop ) = explode( '.', $field, 2 );
			switch ( $prop ) {
				case 'environment':
					$items['environment'] = $this->get_environment_info_per_fields( $fields );
					break;
				case 'database':
					$items['database'] = $this->get_database_info();
					break;
				case 'active_plugins':
					$items['active_plugins'] = $this->get_active_plugins();
					break;
				case 'inactive_plugins':
					$items['inactive_plugins'] = $this->get_inactive_plugins();
					break;
				case 'dropins_mu_plugins':
					$items['dropins_mu_plugins'] = $this->get_dropins_mu_plugins();
					break;
				case 'theme':
					$items['theme'] = $this->get_theme_info();
					break;
				case 'settings':
					$items['settings'] = $this->get_settings();
					break;
				case 'security':
					$items['security'] = $this->get_security_info();
					break;
				case 'pages':
					$items['pages'] = $this->get_pages();
					break;
				case 'post_type_counts':
					$items['post_type_counts'] = $this->get_post_type_counts();
					break;
			}
		}

		return $items;
	}

	/**
	 * Get array of environment information. Includes thing like software
	 * versions, and various server settings.
	 *
	 * @deprecated 3.9.0
	 * @return array
	 */
	public function get_environment_info() {
		return $this->get_environment_info_per_fields( array( 'environment' ) );
	}

	/**
	 * Check if field item exists.
	 *
	 * @since 3.9.0
	 * @param string $section Fields section.
	 * @param array  $items List of items to check for.
	 * @param array  $fields List of fields to be included on the response.
	 * @return bool
	 */
	private function check_if_field_item_exists( $section, $items, $fields ) {
		if ( ! in_array( $section, $fields, true ) ) {
			return false;
		}

		$exclude = array();
		foreach ( $fields as $field ) {
			$values = explode( '.', $field );

			if ( $section !== $values[0] || empty( $values[1] ) ) {
				continue;
			}

			$exclude[] = $values[1];
		}

		return 0 <= count( array_intersect( $items, $exclude ) );
	}

	/**
	 * Get array of environment information. Includes thing like software
	 * versions, and various server settings.
	 *
	 * @param array $fields List of fields to be included on the response.
	 * @return array
	 */
	public function get_environment_info_per_fields( $fields ) {
		global $wpdb;

		$enable_remote_post = $this->check_if_field_item_exists( 'environment', array( 'remote_post_successful', 'remote_post_response' ), $fields );
		$enable_remote_get  = $this->check_if_field_item_exists( 'environment', array( 'remote_get_successful', 'remote_get_response' ), $fields );

		// Figure out cURL version, if installed.
		$curl_version = '';
		if ( function_exists( 'curl_version' ) ) {
			$curl_version = curl_version();
			$curl_version = $curl_version['version'] . ', ' . $curl_version['ssl_version'];
		} elseif ( extension_loaded( 'curl' ) ) {
			$curl_version = __( 'cURL installed but unable to retrieve version.', 'woocommerce' );
		}

		// WP memory limit.
		$wp_memory_limit = wc_let_to_num( WP_MEMORY_LIMIT );
		if ( function_exists( 'memory_get_usage' ) ) {
			$wp_memory_limit = max( $wp_memory_limit, wc_let_to_num( @ini_get( 'memory_limit' ) ) ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		}

		// Test POST requests.
		$post_response_successful = null;
		$post_response_code       = null;
		if ( $enable_remote_post ) {
			$post_response_code = get_transient( 'woocommerce_test_remote_post' );

			if ( false === $post_response_code || is_wp_error( $post_response_code ) ) {
				$response = wp_safe_remote_post(
					'https://www.paypal.com/cgi-bin/webscr',
					array(
						'timeout'     => 10,
						'user-agent'  => 'WooCommerce/' . WC()->version . '; ' . get_bloginfo( 'url' ),
						'httpversion' => '1.1',
						'body'        => array(
							'cmd' => '_notify-validate',
						),
					)
				);
				if ( ! is_wp_error( $response ) ) {
					$post_response_code = $response['response']['code'];
				}
				set_transient( 'woocommerce_test_remote_post', $post_response_code, HOUR_IN_SECONDS );
			}

			$post_response_successful = ! is_wp_error( $post_response_code ) && $post_response_code >= 200 && $post_response_code < 300;
		}

		// Test GET requests.
		$get_response_successful = null;
		$get_response_code       = null;
		if ( $enable_remote_get ) {
			$get_response_code = get_transient( 'woocommerce_test_remote_get' );

			if ( false === $get_response_code || is_wp_error( $get_response_code ) ) {
				$response = wp_safe_remote_get(
					'https://woocommerce.com/wc-api/product-key-api?request=ping&network=' . ( is_multisite() ? '1' : '0' ),
					array(
						'user-agent' => 'WooCommerce/' . WC()->version . '; ' . get_bloginfo( 'url' ),
					)
				);
				if ( ! is_wp_error( $response ) ) {
					$get_response_code = $response['response']['code'];
				}
				set_transient( 'woocommerce_test_remote_get', $get_response_code, HOUR_IN_SECONDS );
			}

			$get_response_successful = ! is_wp_error( $get_response_code ) && $get_response_code >= 200 && $get_response_code < 300;
		}

		$database_version = wc_get_server_database_version();

		// Return all environment info. Described by JSON Schema.
		return array(
			'home_url'                  => get_option( 'home' ),
			'site_url'                  => get_option( 'siteurl' ),
			'version'                   => WC()->version,
			'log_directory'             => WC_LOG_DIR,
			'log_directory_writable'    => (bool) @fopen( WC_LOG_DIR . 'test-log.log', 'a' ), // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_read_fopen
			'wp_version'                => get_bloginfo( 'version' ),
			'wp_multisite'              => is_multisite(),
			'wp_memory_limit'           => $wp_memory_limit,
			'wp_debug_mode'             => ( defined( 'WP_DEBUG' ) && WP_DEBUG ),
			'wp_cron'                   => ! ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ),
			'language'                  => get_locale(),
			'external_object_cache'     => wp_using_ext_object_cache(),
			'server_info'               => isset( $_SERVER['SERVER_SOFTWARE'] ) ? wc_clean( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '',
			'php_version'               => phpversion(),
			'php_post_max_size'         => wc_let_to_num( ini_get( 'post_max_size' ) ),
			'php_max_execution_time'    => (int) ini_get( 'max_execution_time' ),
			'php_max_input_vars'        => (int) ini_get( 'max_input_vars' ),
			'curl_version'              => $curl_version,
			'suhosin_installed'         => extension_loaded( 'suhosin' ),
			'max_upload_size'           => wp_max_upload_size(),
			'mysql_version'             => $database_version['number'],
			'mysql_version_string'      => $database_version['string'],
			'default_timezone'          => date_default_timezone_get(),
			'fsockopen_or_curl_enabled' => ( function_exists( 'fsockopen' ) || function_exists( 'curl_init' ) ),
			'soapclient_enabled'        => class_exists( 'SoapClient' ),
			'domdocument_enabled'       => class_exists( 'DOMDocument' ),
			'gzip_enabled'              => is_callable( 'gzopen' ),
			'mbstring_enabled'          => extension_loaded( 'mbstring' ),
			'remote_post_successful'    => $post_response_successful,
			'remote_post_response'      => is_wp_error( $post_response_code ) ? $post_response_code->get_error_message() : $post_response_code,
			'remote_get_successful'     => $get_response_successful,
			'remote_get_response'       => is_wp_error( $get_response_code ) ? $get_response_code->get_error_message() : $get_response_code,
		);
	}

	/**
	 * Add prefix to table.
	 *
	 * @param string $table Table name.
	 * @return stromg
	 */
	protected function add_db_table_prefix( $table ) {
		global $wpdb;
		return $wpdb->prefix . $table;
	}

	/**
	 * Get array of database information. Version, prefix, and table existence.
	 *
	 * @return array
	 */
	public function get_database_info() {
		global $wpdb;

		$tables        = array();
		$database_size = array();

		// It is not possible to get the database name from some classes that replace wpdb (e.g., HyperDB)
		// and that is why this if condition is needed.
		if ( defined( 'DB_NAME' ) ) {
			$database_table_information = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT
					    table_name AS 'name',
						engine AS 'engine',
					    round( ( data_length / 1024 / 1024 ), 2 ) 'data',
					    round( ( index_length / 1024 / 1024 ), 2 ) 'index'
					FROM information_schema.TABLES
					WHERE table_schema = %s
					ORDER BY name ASC;",
					DB_NAME
				)
			);

			// WC Core tables to check existence of.
			$core_tables = apply_filters(
				'woocommerce_database_tables',
				array(
					'woocommerce_sessions',
					'woocommerce_api_keys',
					'woocommerce_attribute_taxonomies',
					'woocommerce_downloadable_product_permissions',
					'woocommerce_order_items',
					'woocommerce_order_itemmeta',
					'woocommerce_tax_rates',
					'woocommerce_tax_rate_locations',
					'woocommerce_shipping_zones',
					'woocommerce_shipping_zone_locations',
					'woocommerce_shipping_zone_methods',
					'woocommerce_payment_tokens',
					'woocommerce_payment_tokenmeta',
					'woocommerce_log',
				)
			);

			/**
			 * Adding the prefix to the tables array, for backwards compatibility.
			 *
			 * If we changed the tables above to include the prefix, then any filters against that table could break.
			 */
			$core_tables = array_map( array( $this, 'add_db_table_prefix' ), $core_tables );

			/**
			 * Organize WooCommerce and non-WooCommerce tables separately for display purposes later.
			 *
			 * To ensure we include all WC tables, even if they do not exist, pre-populate the WC array with all the tables.
			 */
			$tables = array(
				'woocommerce' => array_fill_keys( $core_tables, false ),
				'other'       => array(),
			);

			$database_size = array(
				'data'  => 0,
				'index' => 0,
			);

			$site_tables_prefix = $wpdb->get_blog_prefix( get_current_blog_id() );
			$global_tables      = $wpdb->tables( 'global', true );
			foreach ( $database_table_information as $table ) {
				// Only include tables matching the prefix of the current site, this is to prevent displaying all tables on a MS install not relating to the current.
				if ( is_multisite() && 0 !== strpos( $table->name, $site_tables_prefix ) && ! in_array( $table->name, $global_tables, true ) ) {
					continue;
				}
				$table_type = in_array( $table->name, $core_tables, true ) ? 'woocommerce' : 'other';

				$tables[ $table_type ][ $table->name ] = array(
					'data'   => $table->data,
					'index'  => $table->index,
					'engine' => $table->engine,
				);

				$database_size['data']  += $table->data;
				$database_size['index'] += $table->index;
			}
		}

		// Return all database info. Described by JSON Schema.
		return array(
			'wc_database_version'    => get_option( 'woocommerce_db_version' ),
			'database_prefix'        => $wpdb->prefix,
			'maxmind_geoip_database' => '',
			'database_tables'        => $tables,
			'database_size'          => $database_size,
		);
	}

	/**
	 * Get array of counts of objects. Orders, products, etc.
	 *
	 * @return array
	 */
	public function get_post_type_counts() {
		global $wpdb;

		$post_type_counts = $wpdb->get_results( "SELECT post_type AS 'type', count(1) AS 'count' FROM {$wpdb->posts} GROUP BY post_type;" );

		return is_array( $post_type_counts ) ? $post_type_counts : array();
	}

	/**
	 * Get a list of plugins active on the site.
	 *
	 * @return array
	 */
	public function get_active_plugins() {
		$active_plugins_data = get_transient( 'wc_system_status_active_plugins' );

		if ( false === $active_plugins_data ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';

			if ( ! function_exists( 'get_plugin_data' ) ) {
				return array();
			}

			$active_plugins = (array) get_option( 'active_plugins', array() );
			if ( is_multisite() ) {
				$network_activated_plugins = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
				$active_plugins            = array_merge( $active_plugins, $network_activated_plugins );
			}

			$active_plugins_data = array();

			foreach ( $active_plugins as $plugin ) {
				$data                  = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
				$active_plugins_data[] = $this->format_plugin_data( $plugin, $data );
			}

			set_transient( 'wc_system_status_active_plugins', $active_plugins_data, HOUR_IN_SECONDS );
		}

		return $active_plugins_data;
	}

	/**
	 * Get a list of inplugins active on the site.
	 *
	 * @return array
	 */
	public function get_inactive_plugins() {
		$plugins_data = get_transient( 'wc_system_status_inactive_plugins' );

		if ( false === $plugins_data ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';

			if ( ! function_exists( 'get_plugins' ) ) {
				return array();
			}

			$plugins        = get_plugins();
			$active_plugins = (array) get_option( 'active_plugins', array() );

			if ( is_multisite() ) {
				$network_activated_plugins = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
				$active_plugins            = array_merge( $active_plugins, $network_activated_plugins );
			}

			$plugins_data = array();

			foreach ( $plugins as $plugin => $data ) {
				if ( in_array( $plugin, $active_plugins, true ) ) {
					continue;
				}
				$plugins_data[] = $this->format_plugin_data( $plugin, $data );
			}

			set_transient( 'wc_system_status_inactive_plugins', $plugins_data, HOUR_IN_SECONDS );
		}

		return $plugins_data;
	}

	/**
	 * Format plugin data, including data on updates, into a standard format.
	 *
	 * @since 3.6.0
	 * @param string $plugin Plugin directory/file.
	 * @param array  $data Plugin data from WP.
	 * @return array Formatted data.
	 */
	protected function format_plugin_data( $plugin, $data ) {
		require_once ABSPATH . 'wp-admin/includes/update.php';

		if ( ! function_exists( 'get_plugin_updates' ) ) {
			return array();
		}

		// Use WP API to lookup latest updates for plugins. WC_Helper injects updates for premium plugins.
		if ( empty( $this->available_updates ) ) {
			$this->available_updates = get_plugin_updates();
		}

		$version_latest = $data['Version'];

		// Find latest version.
		if ( isset( $this->available_updates[ $plugin ]->update->new_version ) ) {
			$version_latest = $this->available_updates[ $plugin ]->update->new_version;
		}

		return array(
			'plugin'            => $plugin,
			'name'              => $data['Name'],
			'version'           => $data['Version'],
			'version_latest'    => $version_latest,
			'url'               => $data['PluginURI'],
			'author_name'       => $data['AuthorName'],
			'author_url'        => esc_url_raw( $data['AuthorURI'] ),
			'network_activated' => $data['Network'],
		);
	}

	/**
	 * Get a list of Dropins and MU plugins.
	 *
	 * @since 3.6.0
	 * @return array
	 */
	public function get_dropins_mu_plugins() {
		$plugins = get_transient( 'wc_system_status_dropins_mu_plugins' );

		if ( false === $plugins ) {
			$dropins = get_dropins();
			$plugins = array(
				'dropins'    => array(),
				'mu_plugins' => array(),
			);
			foreach ( $dropins as $key => $dropin ) {
				$plugins['dropins'][] = array(
					'plugin' => $key,
					'name'   => $dropin['Name'],
				);
			}

			$mu_plugins = get_mu_plugins();
			foreach ( $mu_plugins as $plugin => $mu_plugin ) {
				$plugins['mu_plugins'][] = array(
					'plugin'      => $plugin,
					'name'        => $mu_plugin['Name'],
					'version'     => $mu_plugin['Version'],
					'url'         => $mu_plugin['PluginURI'],
					'author_name' => $mu_plugin['AuthorName'],
					'author_url'  => esc_url_raw( $mu_plugin['AuthorURI'] ),
				);
			}

			set_transient( 'wc_system_status_dropins_mu_plugins', $plugins, HOUR_IN_SECONDS );
		}

		return $plugins;
	}

	/**
	 * Get info on the current active theme, info on parent theme (if presnet)
	 * and a list of template overrides.
	 *
	 * @return array
	 */
	public function get_theme_info() {
		$theme_info = get_transient( 'wc_system_status_theme_info' );

		if ( false === $theme_info ) {
			$active_theme = wp_get_theme();

			// Get parent theme info if this theme is a child theme, otherwise
			// pass empty info in the response.
			if ( is_child_theme() ) {
				$parent_theme      = wp_get_theme( $active_theme->template );
				$parent_theme_info = array(
					'parent_name'           => $parent_theme->name,
					'parent_version'        => $parent_theme->version,
					'parent_version_latest' => WC_Admin_Status::get_latest_theme_version( $parent_theme ),
					'parent_author_url'     => $parent_theme->{'Author URI'},
				);
			} else {
				$parent_theme_info = array(
					'parent_name'           => '',
					'parent_version'        => '',
					'parent_version_latest' => '',
					'parent_author_url'     => '',
				);
			}

			/**
			 * Scan the theme directory for all WC templates to see if our theme
			 * overrides any of them.
			 */
			$override_files     = array();
			$outdated_templates = false;
			$scan_files         = WC_Admin_Status::scan_template_files( WC()->plugin_path() . '/templates/' );

			// Include *-product_<cat|tag> templates for backwards compatibility.
			$scan_files[] = 'content-product_cat.php';
			$scan_files[] = 'taxonomy-product_cat.php';
			$scan_files[] = 'taxonomy-product_tag.php';

			foreach ( $scan_files as $file ) {
				$located = apply_filters( 'wc_get_template', $file, $file, array(), WC()->template_path(), WC()->plugin_path() . '/templates/' );

				if ( file_exists( $located ) ) {
					$theme_file = $located;
				} elseif ( file_exists( get_stylesheet_directory() . '/' . $file ) ) {
					$theme_file = get_stylesheet_directory() . '/' . $file;
				} elseif ( file_exists( get_stylesheet_directory() . '/' . WC()->template_path() . $file ) ) {
					$theme_file = get_stylesheet_directory() . '/' . WC()->template_path() . $file;
				} elseif ( file_exists( get_template_directory() . '/' . $file ) ) {
					$theme_file = get_template_directory() . '/' . $file;
				} elseif ( file_exists( get_template_directory() . '/' . WC()->template_path() . $file ) ) {
					$theme_file = get_template_directory() . '/' . WC()->template_path() . $file;
				} else {
					$theme_file = false;
				}

				if ( ! empty( $theme_file ) ) {
					$core_file = $file;

					// Update *-product_<cat|tag> template name before searching in core.
					if ( false !== strpos( $core_file, '-product_cat' ) || false !== strpos( $core_file, '-product_tag' ) ) {
						$core_file = str_replace( '_', '-', $core_file );
					}

					$core_version  = WC_Admin_Status::get_file_version( WC()->plugin_path() . '/templates/' . $core_file );
					$theme_version = WC_Admin_Status::get_file_version( $theme_file );
					if ( $core_version && ( empty( $theme_version ) || version_compare( $theme_version, $core_version, '<' ) ) ) {
						if ( ! $outdated_templates ) {
							$outdated_templates = true;
						}
					}
					$override_files[] = array(
						'file'         => str_replace( WP_CONTENT_DIR . '/themes/', '', $theme_file ),
						'version'      => $theme_version,
						'core_version' => $core_version,
					);
				}
			}

			$active_theme_info = array(
				'name'                    => $active_theme->name,
				'version'                 => $active_theme->version,
				'version_latest'          => WC_Admin_Status::get_latest_theme_version( $active_theme ),
				'author_url'              => esc_url_raw( $active_theme->{'Author URI'} ),
				'is_child_theme'          => is_child_theme(),
				'has_woocommerce_support' => current_theme_supports( 'woocommerce' ),
				'has_woocommerce_file'    => ( file_exists( get_stylesheet_directory() . '/woocommerce.php' ) || file_exists( get_template_directory() . '/woocommerce.php' ) ),
				'has_outdated_templates'  => $outdated_templates,
				'overrides'               => $override_files,
			);

			$theme_info = array_merge( $active_theme_info, $parent_theme_info );
			set_transient( 'wc_system_status_theme_info', $theme_info, HOUR_IN_SECONDS );
		}

		return $theme_info;
	}

	/**
	 * Clear the system status theme cache
	 */
	public static function clean_theme_cache() {
		delete_transient( 'wc_system_status_theme_info' );
	}

	/**
	 * Clear the system status plugin caches
	 */
	public static function clean_plugin_cache() {
		delete_transient( 'wc_system_status_active_plugins' );
		delete_transient( 'wc_system_status_inactive_plugins' );
		delete_transient( 'wc_system_status_dropins_mu_plugins' );
	}

	/**
	 * Get some setting values for the site that are useful for debugging
	 * purposes. For full settings access, use the settings api.
	 *
	 * @return array
	 */
	public function get_settings() {
		// Get a list of terms used for product/order taxonomies.
		$term_response = array();
		$terms         = get_terms( 'product_type', array( 'hide_empty' => 0 ) );
		foreach ( $terms as $term ) {
			$term_response[ $term->slug ] = strtolower( $term->name );
		}

		// Get a list of terms used for product visibility.
		$product_visibility_terms = array();
		$terms                    = get_terms( 'product_visibility', array( 'hide_empty' => 0 ) );
		foreach ( $terms as $term ) {
			$product_visibility_terms[ $term->slug ] = strtolower( $term->name );
		}

		// Return array of useful settings for debugging.
		return array(
			'api_enabled'                    => 'yes' === get_option( 'woocommerce_api_enabled' ),
			'force_ssl'                      => 'yes' === get_option( 'woocommerce_force_ssl_checkout' ),
			'currency'                       => get_woocommerce_currency(),
			'currency_symbol'                => get_woocommerce_currency_symbol(),
			'currency_position'              => get_option( 'woocommerce_currency_pos' ),
			'thousand_separator'             => wc_get_price_thousand_separator(),
			'decimal_separator'              => wc_get_price_decimal_separator(),
			'number_of_decimals'             => wc_get_price_decimals(),
			'geolocation_enabled'            => in_array(
				get_option( 'woocommerce_default_customer_address' ),
				array(
					'geolocation_ajax',
					'geolocation',
				),
				true
			),
			'taxonomies'                     => $term_response,
			'product_visibility_terms'       => $product_visibility_terms,
			'woocommerce_com_connected'      => ConnectionHelper::is_connected() ? 'yes' : 'no',
			'enforce_approved_download_dirs' => wc_get_container()->get( Download_Directories::class )->get_mode() === Download_Directories::MODE_ENABLED,
			'order_datastore'                => WC_Data_Store::load( 'order' )->get_current_class_name(),
			'HPOS_feature_screen_enabled'    => wc_get_container()->get( Automattic\WooCommerce\Internal\Features\FeaturesController::class )->feature_is_enabled( 'custom_order_tables' ),
			'HPOS_enabled'                   => OrderUtil::custom_orders_table_usage_is_enabled(),
			'HPOS_sync_enabled'              => wc_get_container()->get( Order_DataSynchronizer::class )->data_sync_is_enabled(),
		);
	}

	/**
	 * Returns security tips.
	 *
	 * @return array
	 */
	public function get_security_info() {
		$check_page = wc_get_page_permalink( 'shop' );
		return array(
			'secure_connection' => 'https' === substr( $check_page, 0, 5 ),
			'hide_errors'       => ! ( defined( 'WP_DEBUG' ) && defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG && WP_DEBUG_DISPLAY ) || 0 === intval( ini_get( 'display_errors' ) ),
		);
	}

	/**
	 * Returns a mini-report on WC pages and if they are configured correctly:
	 * Present, visible, and including the correct shortcode or block.
	 *
	 * @return array
	 */
	public function get_pages() {
		// WC pages to check against.
		$check_pages = array(
			_x( 'Shop base', 'Page setting', 'woocommerce' ) => array(
				'option'    => 'woocommerce_shop_page_id',
				'shortcode' => '',
				'block'     => '',
			),
			_x( 'Cart', 'Page setting', 'woocommerce' ) => array(
				'option'    => 'woocommerce_cart_page_id',
				'shortcode' => '[' . apply_filters( 'woocommerce_cart_shortcode_tag', 'woocommerce_cart' ) . ']',
				'block'     => 'woocommerce/cart',
			),
			_x( 'Checkout', 'Page setting', 'woocommerce' ) => array(
				'option'    => 'woocommerce_checkout_page_id',
				'shortcode' => '[' . apply_filters( 'woocommerce_checkout_shortcode_tag', 'woocommerce_checkout' ) . ']',
				'block'     => 'woocommerce/checkout',
			),
			_x( 'My account', 'Page setting', 'woocommerce' ) => array(
				'option'    => 'woocommerce_myaccount_page_id',
				'shortcode' => '[' . apply_filters( 'woocommerce_my_account_shortcode_tag', 'woocommerce_my_account' ) . ']',
				'block'     => '',
			),
			_x( 'Terms and conditions', 'Page setting', 'woocommerce' ) => array(
				'option'    => 'woocommerce_terms_page_id',
				'shortcode' => '',
				'block'     => '',
			),
		);

		$pages_output = array();
		foreach ( $check_pages as $page_name => $values ) {
			$page_id            = get_option( $values['option'] );
			$page_set           = false;
			$page_exists        = false;
			$page_visible       = false;
			$shortcode_present  = false;
			$shortcode_required = false;
			$block_present      = false;
			$block_required     = false;

			// Page checks.
			if ( $page_id ) {
				$page_set = true;
			}
			if ( get_post( $page_id ) ) {
				$page_exists = true;
			}
			if ( 'publish' === get_post_status( $page_id ) ) {
				$page_visible = true;
			}

			// Shortcode checks.
			if ( $values['shortcode'] && get_post( $page_id ) ) {
				$shortcode_required = true;
				$page               = get_post( $page_id );
				if ( strstr( $page->post_content, $values['shortcode'] ) ) {
					$shortcode_present = true;
				}
			}

			// Block checks.
			if ( $values['block'] && get_post( $page_id ) ) {
				$block_required = true;
				$block_present = WC_Blocks_Utils::has_block_in_page( $page_id, $values['block'] );
			}

			// Wrap up our findings into an output array.
			$pages_output[] = array(
				'page_name'          => $page_name,
				'page_id'            => $page_id,
				'page_set'           => $page_set,
				'page_exists'        => $page_exists,
				'page_visible'       => $page_visible,
				'shortcode'          => $values['shortcode'],
				'block'              => $values['block'],
				'shortcode_required' => $shortcode_required,
				'shortcode_present'  => $shortcode_present,
				'block_present'      => $block_present,
				'block_required'     => $block_required,
			);
		}

		return $pages_output;
	}

	/**
	 * Get any query params needed.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		return array(
			'context' => $this->get_context_param( array( 'default' => 'view' ) ),
		);
	}

	/**
	 * Prepare the system status response
	 *
	 * @param  array           $system_status System status data.
	 * @param  WP_REST_Request $request       Request object.
	 * @return WP_REST_Response
	 */
	public function prepare_item_for_response( $system_status, $request ) {
		$data = $this->add_additional_fields_to_object( $system_status, $request );
		$data = $this->filter_response_by_context( $data, 'view' );

		$response = rest_ensure_response( $data );

		/**
		 * Filter the system status returned from the REST API.
		 *
		 * @param WP_REST_Response   $response The response object.
		 * @param mixed              $system_status System status
		 * @param WP_REST_Request    $request  Request object.
		 */
		return apply_filters( 'woocommerce_rest_prepare_system_status', $response, $system_status, $request );
	}
}
