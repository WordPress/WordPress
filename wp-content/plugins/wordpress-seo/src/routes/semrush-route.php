<?php

namespace Yoast\WP\SEO\Routes;

use WP_REST_Request;
use WP_REST_Response;
use Yoast\WP\SEO\Actions\SEMrush\SEMrush_Login_Action;
use Yoast\WP\SEO\Actions\SEMrush\SEMrush_Options_Action;
use Yoast\WP\SEO\Actions\SEMrush\SEMrush_Phrases_Action;
use Yoast\WP\SEO\Conditionals\SEMrush_Enabled_Conditional;
use Yoast\WP\SEO\Main;

/**
 * SEMrush_Route class.
 */
class SEMrush_Route implements Route_Interface {

	/**
	 * The SEMrush route prefix.
	 *
	 * @var string
	 */
	public const ROUTE_PREFIX = 'semrush';

	/**
	 * The authenticate route constant.
	 *
	 * @var string
	 */
	public const AUTHENTICATION_ROUTE = self::ROUTE_PREFIX . '/authenticate';

	/**
	 * The country code option route constant.
	 *
	 * @var string
	 */
	public const COUNTRY_CODE_OPTION_ROUTE = self::ROUTE_PREFIX . '/country_code';

	/**
	 * The request related keyphrases route constant.
	 *
	 * @var string
	 */
	public const RELATED_KEYPHRASES_ROUTE = self::ROUTE_PREFIX . '/related_keyphrases';

	/**
	 * The full login route constant.
	 *
	 * @var string
	 */
	public const FULL_AUTHENTICATION_ROUTE = Main::API_V1_NAMESPACE . '/' . self::AUTHENTICATION_ROUTE;

	/**
	 * The full country code option route constant.
	 *
	 * @var string
	 */
	public const FULL_COUNTRY_CODE_OPTION_ROUTE = Main::API_V1_NAMESPACE . '/' . self::COUNTRY_CODE_OPTION_ROUTE;

	/**
	 * The login action.
	 *
	 * @var SEMrush_Login_Action
	 */
	private $login_action;

	/**
	 * The options action.
	 *
	 * @var SEMrush_Options_Action
	 */
	private $options_action;

	/**
	 * The phrases action.
	 *
	 * @var SEMrush_Phrases_Action
	 */
	private $phrases_action;

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ SEMrush_Enabled_Conditional::class ];
	}

	/**
	 * SEMrush_Route constructor.
	 *
	 * @param SEMrush_Login_Action   $login_action   The login action.
	 * @param SEMrush_Options_Action $options_action The options action.
	 * @param SEMrush_Phrases_Action $phrases_action The phrases action.
	 */
	public function __construct(
		SEMrush_Login_Action $login_action,
		SEMrush_Options_Action $options_action,
		SEMrush_Phrases_Action $phrases_action
	) {
		$this->login_action   = $login_action;
		$this->options_action = $options_action;
		$this->phrases_action = $phrases_action;
	}

	/**
	 * Registers routes with WordPress.
	 *
	 * @return void
	 */
	public function register_routes() {
		$authentication_route_args = [
			'methods'             => 'POST',
			'callback'            => [ $this, 'authenticate' ],
			'permission_callback' => [ $this, 'can_use_semrush' ],
			'args'                => [
				'code' => [
					'validate_callback' => [ $this, 'has_valid_code' ],
					'required'          => true,
				],
			],
		];

		\register_rest_route( Main::API_V1_NAMESPACE, self::AUTHENTICATION_ROUTE, $authentication_route_args );

		$set_country_code_option_route_args = [
			'methods'             => 'POST',
			'callback'            => [ $this, 'set_country_code_option' ],
			'permission_callback' => [ $this, 'can_use_semrush' ],
			'args'                => [
				'country_code' => [
					'validate_callback' => [ $this, 'has_valid_country_code' ],
					'required'          => true,
				],
			],
		];

		\register_rest_route( Main::API_V1_NAMESPACE, self::COUNTRY_CODE_OPTION_ROUTE, $set_country_code_option_route_args );

		$related_keyphrases_route_args = [
			'methods'             => 'GET',
			'callback'            => [ $this, 'get_related_keyphrases' ],
			'permission_callback' => [ $this, 'can_use_semrush' ],
			'args'                => [
				'keyphrase' => [
					'validate_callback' => [ $this, 'has_valid_keyphrase' ],
					'required'          => true,
				],
				'country_code' => [
					'required' => true,
				],
			],
		];

		\register_rest_route( Main::API_V1_NAMESPACE, self::RELATED_KEYPHRASES_ROUTE, $related_keyphrases_route_args );
	}

	/**
	 * Authenticates with SEMrush.
	 *
	 * @param WP_REST_Request $request The request. This request should have a code param set.
	 *
	 * @return WP_REST_Response The response.
	 */
	public function authenticate( WP_REST_Request $request ) {
		$data = $this
			->login_action
			->authenticate( $request['code'] );

		return new WP_REST_Response( $data, $data->status );
	}

	/**
	 * Sets the SEMrush country code option.
	 *
	 * @param WP_REST_Request $request The request. This request should have a country code param set.
	 *
	 * @return WP_REST_Response The response.
	 */
	public function set_country_code_option( WP_REST_Request $request ) {
		$data = $this
			->options_action
			->set_country_code( $request['country_code'] );

		return new WP_REST_Response( $data, $data->status );
	}

	/**
	 * Checks if a valid code was returned.
	 *
	 * @param string $code The code to check.
	 *
	 * @return bool Whether or not the code is valid.
	 */
	public function has_valid_code( $code ) {
		return $code !== '';
	}

	/**
	 * Checks if a valid keyphrase is provided.
	 *
	 * @param string $keyphrase The keyphrase to check.
	 *
	 * @return bool Whether or not the keyphrase is valid.
	 */
	public function has_valid_keyphrase( $keyphrase ) {
		return \trim( $keyphrase ) !== '';
	}

	/**
	 * Gets the related keyphrases based on the passed keyphrase and database code.
	 *
	 * @param WP_REST_Request $request The request. This request should have a keyphrase and country_code param set.
	 *
	 * @return WP_REST_Response The response.
	 */
	public function get_related_keyphrases( WP_REST_Request $request ) {
		$data = $this
			->phrases_action
			->get_related_keyphrases(
				$request['keyphrase'],
				$request['country_code']
			);

		return new WP_REST_Response( $data, $data->status );
	}

	/**
	 * Checks if a valid country code was submitted.
	 *
	 * @param string $country_code The country code to check.
	 *
	 * @return bool Whether or not the country code is valid.
	 */
	public function has_valid_country_code( $country_code ) {
		return ( $country_code !== '' && \preg_match( '/^[a-z]{2}$/', $country_code ) === 1 );
	}

	/**
	 * Whether or not the current user is allowed to edit post/pages and thus use the SEMrush integration.
	 *
	 * @return bool Whether or not the current user is allowed to use SEMrush.
	 */
	public function can_use_semrush() {
		return \current_user_can( 'edit_posts' ) || \current_user_can( 'edit_pages' );
	}
}
