<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo;

use Exception;
use Piwik\API\Request;
use Piwik\API\ResponseBuilder;
use Piwik\Common;
use WP_Error;
use WP_REST_Request;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}
/**
 * phpcs:disable WordPress.Security.NonceVerification.Missing
 */
class API {
	const VERSION = 'matomo/v1';

	const ROUTE_HIT = 'hit';

	public function register_hooks() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	public function register_routes() {
		register_rest_route(
			self::VERSION,
			'/' . self::ROUTE_HIT . '/',
			[
				'methods'             => [ 'GET', 'POST' ],
				'permission_callback' => '__return_true',
				'callback'            => [ $this, 'hit' ],
			]
		);
		$this->register_route( 'API', 'getProcessedReport' );
		$this->register_route( 'API', 'getReportMetadata' );
		$this->register_route( 'API', 'getMatomoVersion' );
		$this->register_route( 'API', 'getMetadata' );
		$this->register_route( 'API', 'getSegmentsMetadata' );
		$this->register_route( 'API', 'getWidgetMetadata' );
		$this->register_route( 'API', 'getRowEvolution' );
		$this->register_route( 'API', 'getSuggestedValuesForSegment' );
		$this->register_route( 'API', 'getSettings' );
		$this->register_route( 'Annotations', 'add' );
		$this->register_route( 'Annotations', 'getAll' );
		$this->register_route( 'CoreAdminHome', 'invalidateArchivedReports' );
		$this->register_route( 'CoreAdminHome', 'runScheduledTasks' );
		$this->register_route( 'CoreAdminHome', 'runCronArchiving' );
		$this->register_route( 'Dashboard', 'getDashboards' );
		$this->register_route( 'ImageGraph', 'get' );
		$this->register_route( 'VisitsSummary', 'getVisits' );
		$this->register_route( 'VisitsSummary', 'getUniqueVisitors' );
		$this->register_route( 'LanguagesManager', 'getAvailableLanguages' );
		$this->register_route( 'LanguagesManager', 'getAvailableLanguagesInfo' );
		$this->register_route( 'LanguagesManager', 'getAvailableLanguageNames' );
		$this->register_route( 'LanguagesManager', 'getLanguageForUser' );
		$this->register_route( 'Live', 'getCounters' );
		$this->register_route( 'Live', 'getLastVisitsDetails' );
		$this->register_route( 'Live', 'getVisitorProfile' );
		$this->register_route( 'Live', 'getMostRecentVisitorId' );
		$this->register_route( 'PrivacyManager', 'deleteDataSubjects' );
		$this->register_route( 'PrivacyManager', 'exportDataSubjects' );
		$this->register_route( 'PrivacyManager', 'anonymizeSomeRawData' );
		$this->register_route( 'ScheduledReports', 'getReports' );
		$this->register_route( 'ScheduledReports', 'sendReport' );
		$this->register_route( 'SegmentEditor', 'add' );
		$this->register_route( 'SegmentEditor', 'update' );
		$this->register_route( 'SegmentEditor', 'delete' );
		$this->register_route( 'SegmentEditor', 'get' );
		$this->register_route( 'SegmentEditor', 'getAll' );
		$this->register_route( 'SitesManager', 'getAllSites' );
		$this->register_route( 'SitesManager', 'getAllSitesId' );
		$this->register_route( 'SitesManager', 'getSitesIdWithAtLeastViewAccess' );
		$this->register_route( 'UsersManager', 'getUsers' );
		$this->register_route( 'UsersManager', 'getUsersLogin' );
		$this->register_route( 'UsersManager', 'getUser' );
		$this->register_route( 'Goals', 'getGoals' );
		$this->register_route( 'VisitsSummary', 'get' );

		// todo ideally we would make here work /goal/12345 to get goalId 12345
		$this->register_route( 'Goals', 'getGoal' );
		$this->register_route( 'Goals', 'addGoal' );
		$this->register_route( 'Goals', 'updateGoal' );
		$this->register_route( 'Goals', 'deleteGoal' );

		$this->register_route( 'TagManager', 'getContainerTags' );
		$this->register_route( 'TagManager', 'addContainerTag' );
		$this->register_route( 'TagManager', 'getContainerTriggers' );
		$this->register_route( 'TagManager', 'addContainerTrigger' );
		$this->register_route( 'TagManager', 'getContainerVariables' );
		$this->register_route( 'TagManager', 'addContainerVariable' );
		$this->register_route( 'TagManager', 'addContainer' );
		$this->register_route( 'TagManager', 'getContainer' );
		$this->register_route( 'TagManager', 'getContainers' );
		$this->register_route( 'TagManager', 'getContainerVersions' );
		$this->register_route( 'TagManager', 'createContainerVersion' );
		$this->register_route( 'TagManager', 'publishContainerVersion' );
	}

	public function hit() {
		if ( ( empty( $_GET ) || isset( $_GET['rest_route'] ) ) && empty( $_POST ) && empty( $_POST['idsite'] ) && empty( $_GET['idsite'] ) ) {
			// todo if uploads dir is not writable, we may want to generate the matomo.js here and save it as an
			// option... then we could also save it compressed
			$paths         = new Paths();
			$path          = $paths->get_matomo_js_upload_path();
			$wp_filesystem = $paths->get_file_system();
			header( 'Content-Type: application/javascript' );
			header( 'Content-Length: ' . ( filesize( $path ) ) );
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $wp_filesystem->get_contents( $paths->get_upload_base_dir() . '/matomo.js' ); // Reading the file into the output buffer
			exit;
		}
		include_once plugin_dir_path( MATOMO_ANALYTICS_FILE ) . 'app/piwik.php';
		exit;
	}

	public function execute_api_method( WP_REST_Request $request ) {
		$attributes = $request->get_attributes();
		$method     = $attributes['matomoModule'] . '.' . $attributes['matomoMethod'];

		$with_idsite = true;

		return $this->execute_request( $method, $with_idsite, $request->get_params() );
	}

	/**
	 * @param string $method
	 *
	 * @return string
	 * @internal
	 * for tests only
	 */
	public function to_snake_case( $method ) {
		preg_match_all( '!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $method, $matches );

		$snake_case = $matches[0];

		foreach ( $snake_case as &$match ) {
			if ( strtoupper( $match ) === $match ) {
				$match = strtolower( $match );
			} else {
				$match = lcfirst( $match );
			}
		}

		return implode( '_', $snake_case );
	}

	/**
	 * @api
	 */
	public function register_route( $api_module, $api_method ) {
		$methods                 = [
			'get'        => 'GET',
			'edit'       => 'PUT',
			'update'     => 'PUT',
			'create'     => 'POST',
			'add'        => 'POST',
			'anonymize'  => 'POST',
			'invalidate' => 'POST',
			'run'        => 'POST',
			'send'       => 'POST',
			'publish'    => 'POST',
			'delete'     => 'DELETE',
			'remove'     => 'DELETE',
		];
		$starts_with_keep_prefix = [ 'anonymize', 'invalidate', 'run', 'send', 'publish' ];

		$method        = 'GET';
		$wp_api_module = $this->to_snake_case( $api_module );
		$wp_api_action = $this->to_snake_case( $api_method );

		foreach ( $methods as $method_starts_with => $method_to_use ) {
			if ( strpos( $api_method, $method_starts_with ) === 0 ) {
				$method = $method_to_use;
				if ( ! in_array( $method_starts_with, $starts_with_keep_prefix, true ) ) {
					$new_action = trim( ltrim( substr( $wp_api_action, strlen( $method_starts_with ) ), '_' ) );
					if ( ! empty( $new_action ) ) {
						$wp_api_action = $new_action;
					}
				}
				break;
			}
		}

		$methods = [ $method ];

		register_rest_route(
			self::VERSION,
			'/' . $wp_api_module . '/' . $wp_api_action . '/',
			[
				'methods'             => $methods,
				'callback'            => [ $this, 'execute_api_method' ],
				'permission_callback' => '__return_true', // permissions are checked in the method itself
				'matomoModule'        => $api_module,
				'matomoMethod'        => $api_method,
			]
		);
	}

	private function execute_request( $api_method, $with_idsite, $params ) {
		if ( $with_idsite ) {
			$site   = new Site();
			$idsite = $site->get_current_matomo_site_id();

			if ( ! $idsite ) {
				return new WP_Error( 'Site not found. Make sure it is synced' );
			}

			$params['idSite']  = $idsite;
			$params['idsite']  = $idsite;
			$params['idsites'] = $idsite;
			$params['idSites'] = $idsite;
		}

		// ensure user is authenticated through WordPress!
		unset( $_GET['token_auth'] );
		unset( $_POST['token_auth'] );

		Bootstrap::do_bootstrap();

		// refs https://github.com/matomo-org/matomo-for-wordpress/issues/370 ensuring segment will be used from default request when
		// creating new request object and not the encoded segment
		if ( isset( $params['segment'] ) ) {
			if ( isset( $_GET['segment'] ) || isset( $_POST['segment'] ) ) {
				unset( $params['segment'] ); // matomo will read the segment from default request
			} elseif ( ! empty( $params['segment'] ) && is_string( $params['segment'] ) ) {
				// manually unsanitize this value
				$params['segment'] = Common::unsanitizeInputValue( $params['segment'] );
			}
		}

		$output_format    = empty( $params['format'] ) ? 'json' : $params['format'];
		$params['format'] = 'original';

		try {
			$result = Request::processRequest( $api_method, $params );

			$response_builder = new ResponseBuilder( $output_format, $params );
			$response_builder->disableDataTablePostProcessor(); // done within Request, we don't need to use it again

			$result = $response_builder->getResponse( $result );

			if ( 'json' === $output_format ) {
				// WordPress always JSON encodes the result of REST API methods, so sending format=json to Matomo
				// results in double JSON encoding the result. so if format=json is detected, we have to parse the
				// the JSON before handing it over to WordPress.
				$result = json_decode( $result, true );

				// scalar values are returned as is currently
				if ( array_key_exists( 'value', $result ) && count( $result ) === 1 ) {
					$result = $result['value'];
				}
			}
		} catch ( Exception $e ) {
			$code = 'matomo_error';
			if ( $e->getCode() ) {
				$code .= '_' . $code;
			}
			if ( get_class( $e ) !== 'Exception' ) {
				$code = str_replace( 'piwik', 'matomo', $this->to_snake_case( get_class( $e ) ) );
			}

			return new WP_Error( $code, $e->getMessage() );
		}

		return $result;
	}
}
