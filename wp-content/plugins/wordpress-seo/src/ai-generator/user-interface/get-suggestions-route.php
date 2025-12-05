<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\AI_Generator\User_Interface;

use RuntimeException;
use WP_REST_Request;
use WP_REST_Response;
use Yoast\WP\SEO\AI_Generator\Application\Suggestions_Provider;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Payment_Required_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Remote_Request_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Too_Many_Requests_Exception;
use Yoast\WP\SEO\Conditionals\AI_Conditional;
use Yoast\WP\SEO\Conditionals\No_Conditionals;
use Yoast\WP\SEO\Main;
use Yoast\WP\SEO\Routes\Route_Interface;

/**
 * Registers a route to get suggestions from the AI API
 *
 * @makePublic
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class Get_Suggestions_Route implements Route_Interface {

	use No_Conditionals;
	use Route_Permission_Trait;

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
	public const ROUTE_PREFIX = '/ai_generator/get_suggestions';

	/**
	 * The suggestions provider instance.
	 *
	 * @var Suggestions_Provider
	 */
	private $suggestions_provider;

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array<string> The conditionals.
	 */
	public static function get_conditionals() {
		return [ AI_Conditional::class ];
	}

	/**
	 * Class constructor.
	 *
	 * @param Suggestions_Provider $suggestions_provider The suggestions provider instance.
	 */
	public function __construct( Suggestions_Provider $suggestions_provider ) {
		$this->suggestions_provider = $suggestions_provider;
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
				'methods'             => 'POST',
				'args'                => [
					'type'            => [
						'required'    => true,
						'type'        => 'string',
						'enum'        => [
							'seo-title',
							'meta-description',
							'product-seo-title',
							'product-meta-description',
							'product-taxonomy-seo-title',
							'product-taxonomy-meta-description',
							'taxonomy-seo-title',
							'taxonomy-meta-description',
						],
						'description' => 'The type of suggestion requested.',
					],
					'prompt_content'  => [
						'required'    => true,
						'type'        => 'string',
						'description' => 'The content needed by the prompt to ask for suggestions.',
					],
					'focus_keyphrase' => [
						'required'    => true,
						'type'        => 'string',
						'description' => 'The focus keyphrase associated to the post.',
					],
					'language'        => [
						'required'    => true,
						'type'        => 'string',
						'description' => 'The language the post is written in.',
					],
					'platform'        => [
						'required'    => true,
						'type'        => 'string',
						'enum'        => [
							'Google',
							'Facebook',
							'Twitter',
						],
						'description' => 'The platform the post is intended for.',
					],
					'editor' => [
						'required'    => true,
						'type'        => 'string',
						'enum'        => [
							'classic',
							'elementor',
							'gutenberg',
						],
						'description' => 'The current editor.',
					],
				],
				'callback'            => [ $this, 'get_suggestions' ],
				'permission_callback' => [ $this, 'check_permissions' ],
			]
		);
	}

	/**
	 * Runs the callback to get AI-generated suggestions.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response The response of the get_suggestions action.
	 */
	public function get_suggestions( WP_REST_Request $request ): WP_REST_Response {
		try {
			$user = \wp_get_current_user();
			$data = $this->suggestions_provider->get_suggestions(
				$user,
				$request->get_param( 'type' ),
				$request->get_param( 'prompt_content' ),
				$request->get_param( 'focus_keyphrase' ),
				$request->get_param( 'language' ),
				$request->get_param( 'platform' ),
				$request->get_param( 'editor' )
			);
		} catch ( Remote_Request_Exception $e ) {
			$message = [
				'message'         => $e->getMessage(),
				'errorIdentifier' => $e->get_error_identifier(),
			];
			if ( $e instanceof Payment_Required_Exception || $e instanceof Too_Many_Requests_Exception ) {
				$message['missingLicenses'] = $e->get_missing_licenses();
			}
			return new WP_REST_Response(
				$message,
				$e->getCode()
			);
		} catch ( RuntimeException $e ) {
			return new WP_REST_Response( 'Failed to get suggestions.', 500 );
		}

		return new WP_REST_Response( $data );
	}
}
