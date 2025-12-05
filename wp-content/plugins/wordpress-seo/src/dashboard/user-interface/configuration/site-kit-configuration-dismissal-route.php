<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\User_Interface\Configuration;

use Exception;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use Yoast\WP\SEO\Conditionals\Google_Site_Kit_Feature_Conditional;
use Yoast\WP\SEO\Dashboard\Infrastructure\Configuration\Permanently_Dismissed_Site_Kit_Configuration_Repository_Interface;
use Yoast\WP\SEO\Helpers\Capability_Helper;
use Yoast\WP\SEO\Main;
use Yoast\WP\SEO\Routes\Route_Interface;

/**
 * Registers a route to set whether the Site Kit configuration is permanently dismissed.
 *
 * @makePublic
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class Site_Kit_Configuration_Dismissal_Route implements Route_Interface {

	/**
	 *  The namespace for this route.
	 *
	 * @var string
	 */
	public const ROUTE_NAMESPACE = Main::API_V1_NAMESPACE;

	/**
	 *  The prefix for this route.
	 *
	 * @var string
	 */
	public const ROUTE_PREFIX = '/site_kit_configuration_permanent_dismissal';

	/**
	 * Holds the introductions collector instance.
	 *
	 * @var Permanently_Dismissed_Site_Kit_Configuration_Repository_Interface
	 */
	private $permanently_dismissed_site_kit_configuration_repository;

	/**
	 * Holds the capabilit helper instance.
	 *
	 * @var Capability_Helper
	 */
	private $capability_helper;

	/**
	 * The needed conditionals.
	 *
	 * @return array<string>
	 */
	public static function get_conditionals() {
		// This cannot have the Admin Conditional since it also needs to run in Rest requests.
		return [ Google_Site_Kit_Feature_Conditional::class ];
	}

	/**
	 * Constructs the class.
	 *
	 * @param Permanently_Dismissed_Site_Kit_Configuration_Repository_Interface $permanently_dismissed_site_kit_configuration_repository The repository.
	 * @param Capability_Helper                                                 $capability_helper                                       The capability helper.
	 */
	public function __construct(
		Permanently_Dismissed_Site_Kit_Configuration_Repository_Interface $permanently_dismissed_site_kit_configuration_repository,
		Capability_Helper $capability_helper
	) {
		$this->permanently_dismissed_site_kit_configuration_repository = $permanently_dismissed_site_kit_configuration_repository;
		$this->capability_helper                                       = $capability_helper;
	}

	/**
	 * Registers routes with WordPress.
	 *
	 * @return void
	 */
	public function register_routes() {
		\register_rest_route(
			self::ROUTE_NAMESPACE,
			self::ROUTE_PREFIX,
			[
				[
					'methods'             => 'POST',
					'callback'            => [ $this, 'set_site_kit_configuration_permanent_dismissal' ],
					'permission_callback' => [ $this, 'check_capabilities' ],
					'args'                => [
						'is_dismissed' => [
							'required'          => true,
							'type'              => 'bool',
							'sanitize_callback' => 'rest_sanitize_boolean',
						],

					],
				],
			]
		);
	}

	/**
	 * Sets whether the Site Kit configuration is permanently dismissed.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|WP_Error The success or failure response.
	 */
	public function set_site_kit_configuration_permanent_dismissal( WP_REST_Request $request ) {
		$is_dismissed = $request->get_param( 'is_dismissed' );

		try {
			$result = $this->permanently_dismissed_site_kit_configuration_repository->set_site_kit_configuration_dismissal( $is_dismissed );
		} catch ( Exception $exception ) {
			return new WP_Error(
				'wpseo_set_site_kit_configuration_permanent_dismissal_error',
				$exception->getMessage(),
				(object) []
			);
		}

			return new WP_REST_Response(
				[
					'success' => $result,
				],
				( $result ) ? 200 : 400
			);
	}

	/**
	 * Checks if the current user has the required capabilities.
	 *
	 * @return bool
	 */
	public function check_capabilities() {
		return $this->capability_helper->current_user_can( 'wpseo_manage_options' );
	}
}
