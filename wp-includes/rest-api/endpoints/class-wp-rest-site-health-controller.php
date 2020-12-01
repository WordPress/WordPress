<?php
/**
 * REST API: WP_REST_Site_Health_Controller class
 *
 * @package WordPress
 * @subpackage REST_API
 * @since 5.6.0
 */

/**
 * Core class for interacting with Site Health tests.
 *
 * @since 5.6.0
 *
 * @see WP_REST_Controller
 */
class WP_REST_Site_Health_Controller extends WP_REST_Controller {

	/**
	 * An instance of the site health class.
	 *
	 * @since 5.6.0
	 *
	 * @var WP_Site_Health
	 */
	private $site_health;

	/**
	 * Site Health controller constructor.
	 *
	 * @since 5.6.0
	 *
	 * @param WP_Site_Health $site_health An instance of the site health class.
	 */
	public function __construct( $site_health ) {
		$this->namespace = 'wp-site-health/v1';
		$this->rest_base = 'tests';

		$this->site_health = $site_health;
	}

	/**
	 * Registers API routes.
	 *
	 * @since 5.6.0
	 *
	 * @see register_rest_route()
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			sprintf(
				'/%s/%s',
				$this->rest_base,
				'background-updates'
			),
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'test_background_updates' ),
					'permission_callback' => function () {
						return $this->validate_request_permission( 'background_updates' );
					},
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			sprintf(
				'/%s/%s',
				$this->rest_base,
				'loopback-requests'
			),
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'test_loopback_requests' ),
					'permission_callback' => function () {
						return $this->validate_request_permission( 'loopback_requests' );
					},
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			sprintf(
				'/%s/%s',
				$this->rest_base,
				'dotorg-communication'
			),
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'test_dotorg_communication' ),
					'permission_callback' => function () {
						return $this->validate_request_permission( 'dotorg_communication' );
					},
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			sprintf(
				'/%s/%s',
				$this->rest_base,
				'authorization-header'
			),
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'test_authorization_header' ),
					'permission_callback' => function () {
						return $this->validate_request_permission( 'authorization_header' );
					},
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			sprintf(
				'/%s',
				'directory-sizes'
			),
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_directory_sizes' ),
				'permission_callback' => function() {
					return $this->validate_request_permission( 'debug_enabled' ) && ! is_multisite();
				},
			)
		);
	}

	/**
	 * Validates if the current user can request this REST endpoint.
	 *
	 * @since 5.6.0
	 *
	 * @param string $check The endpoint check being ran.
	 * @return bool
	 */
	protected function validate_request_permission( $check ) {
		$default_capability = 'view_site_health_checks';

		/**
		 * Filters the capability needed to run a given Site Health check.
		 *
		 * @since 5.6.0
		 *
		 * @param string $default_capability The default capability required for this check.
		 * @param string $check              The Site Health check being performed.
		 */
		$capability = apply_filters( "site_health_test_rest_capability_{$check}", $default_capability, $check );

		return current_user_can( $capability );
	}

	/**
	 * Checks if background updates work as expected.
	 *
	 * @since 5.6.0
	 *
	 * @return array
	 */
	public function test_background_updates() {
		$this->load_admin_textdomain();
		return $this->site_health->get_test_background_updates();
	}

	/**
	 * Checks that the site can reach the WordPress.org API.
	 *
	 * @since 5.6.0
	 *
	 * @return array
	 */
	public function test_dotorg_communication() {
		$this->load_admin_textdomain();
		return $this->site_health->get_test_dotorg_communication();
	}

	/**
	 * Checks that loopbacks can be performed.
	 *
	 * @since 5.6.0
	 *
	 * @return array
	 */
	public function test_loopback_requests() {
		$this->load_admin_textdomain();
		return $this->site_health->get_test_loopback_requests();
	}

	/**
	 * Checks that the authorization header is valid.
	 *
	 * @since 5.6.0
	 *
	 * @return array
	 */
	public function test_authorization_header() {
		$this->load_admin_textdomain();
		return $this->site_health->get_test_authorization_header();
	}

	/**
	 * Gets the current directory sizes for this install.
	 *
	 * @since 5.6.0
	 *
	 * @return array|WP_Error
	 */
	public function get_directory_sizes() {
		if ( ! class_exists( 'WP_Debug_Data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-debug-data.php';
		}

		$this->load_admin_textdomain();

		$sizes_data = WP_Debug_Data::get_sizes();
		$all_sizes  = array( 'raw' => 0 );

		foreach ( $sizes_data as $name => $value ) {
			$name = sanitize_text_field( $name );
			$data = array();

			if ( isset( $value['size'] ) ) {
				if ( is_string( $value['size'] ) ) {
					$data['size'] = sanitize_text_field( $value['size'] );
				} else {
					$data['size'] = (int) $value['size'];
				}
			}

			if ( isset( $value['debug'] ) ) {
				if ( is_string( $value['debug'] ) ) {
					$data['debug'] = sanitize_text_field( $value['debug'] );
				} else {
					$data['debug'] = (int) $value['debug'];
				}
			}

			if ( ! empty( $value['raw'] ) ) {
				$data['raw'] = (int) $value['raw'];
			}

			$all_sizes[ $name ] = $data;
		}

		if ( isset( $all_sizes['total_size']['debug'] ) && 'not available' === $all_sizes['total_size']['debug'] ) {
			return new WP_Error( 'not_available', __( 'Directory sizes could not be returned.' ), array( 'status' => 500 ) );
		}

		return $all_sizes;
	}

	/**
	 * Loads the admin textdomain for Site Health tests.
	 *
	 * The {@see WP_Site_Health} class is defined in WP-Admin, while the REST API operates in a front-end context.
	 * This means that the translations for Site Health won't be loaded by default in {@see load_default_textdomain()}.
	 *
	 * @since 5.6.0
	 */
	protected function load_admin_textdomain() {
		// Accounts for inner REST API requests in the admin.
		if ( ! is_admin() ) {
			$locale = determine_locale();
			load_textdomain( 'default', WP_LANG_DIR . "/admin-$locale.mo" );
		}
	}

	/**
	 * Gets the schema for each site health test.
	 *
	 * @since 5.6.0
	 *
	 * @return array The test schema.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->schema;
		}

		$this->schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'wp-site-health-test',
			'type'       => 'object',
			'properties' => array(
				'test'        => array(
					'type'        => 'string',
					'description' => __( 'The name of the test being run.' ),
					'readonly'    => true,
				),
				'label'       => array(
					'type'        => 'string',
					'description' => __( 'A label describing the test.' ),
					'readonly'    => true,
				),
				'status'      => array(
					'type'        => 'string',
					'description' => __( 'The status of the test.' ),
					'enum'        => array( 'good', 'recommended', 'critical' ),
					'readonly'    => true,
				),
				'badge'       => array(
					'type'        => 'object',
					'description' => __( 'The category this test is grouped in.' ),
					'properties'  => array(
						'label' => array(
							'type'     => 'string',
							'readonly' => true,
						),
						'color' => array(
							'type'     => 'string',
							'enum'     => array( 'blue', 'orange', 'red', 'green', 'purple', 'gray' ),
							'readonly' => true,
						),
					),
					'readonly'    => true,
				),
				'description' => array(
					'type'        => 'string',
					'description' => __( 'A more descriptive explanation of what the test looks for, and why it is important for the user.' ),
					'readonly'    => true,
				),
				'actions'     => array(
					'type'        => 'string',
					'description' => __( 'HTML containing an action to direct the user to where they can resolve the issue.' ),
					'readonly'    => true,
				),
			),
		);

		return $this->schema;
	}
}
