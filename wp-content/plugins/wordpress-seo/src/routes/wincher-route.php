<?php

namespace Yoast\WP\SEO\Routes;

use WP_REST_Request;
use WP_REST_Response;
use Yoast\WP\SEO\Actions\Wincher\Wincher_Account_Action;
use Yoast\WP\SEO\Actions\Wincher\Wincher_Keyphrases_Action;
use Yoast\WP\SEO\Actions\Wincher\Wincher_Login_Action;
use Yoast\WP\SEO\Conditionals\Wincher_Enabled_Conditional;
use Yoast\WP\SEO\Main;

/**
 * Wincher_Route class.
 */
class Wincher_Route implements Route_Interface {

	/**
	 * The Wincher route prefix.
	 *
	 * @var string
	 */
	public const ROUTE_PREFIX = 'wincher';

	/**
	 * The authorize route constant.
	 *
	 * @var string
	 */
	public const AUTHORIZATION_URL_ROUTE = self::ROUTE_PREFIX . '/authorization-url';

	/**
	 * The authenticate route constant.
	 *
	 * @var string
	 */
	public const AUTHENTICATION_ROUTE = self::ROUTE_PREFIX . '/authenticate';

	/**
	 * The track bulk keyphrases route constant.
	 *
	 * @var string
	 */
	public const KEYPHRASES_TRACK_ROUTE = self::ROUTE_PREFIX . '/keyphrases/track';

	/**
	 * The keyphrases route constant.
	 *
	 * @var string
	 */
	public const TRACKED_KEYPHRASES_ROUTE = self::ROUTE_PREFIX . '/keyphrases';

	/**
	 * The untrack keyphrase route constant.
	 *
	 * @var string
	 */
	public const UNTRACK_KEYPHRASE_ROUTE = self::ROUTE_PREFIX . '/keyphrases/untrack';

	/**
	 * The check limit route constant.
	 *
	 * @var string
	 */
	public const CHECK_LIMIT_ROUTE = self::ROUTE_PREFIX . '/account/limit';

	/**
	 * The upgrade campaign route constant.
	 *
	 * @var string
	 */
	public const UPGRADE_CAMPAIGN_ROUTE = self::ROUTE_PREFIX . '/account/upgrade-campaign';

	/**
	 * The login action.
	 *
	 * @var Wincher_Login_Action
	 */
	private $login_action;

	/**
	 * The account action.
	 *
	 * @var Wincher_Account_Action
	 */
	private $account_action;

	/**
	 * The keyphrases action.
	 *
	 * @var Wincher_Keyphrases_Action
	 */
	private $keyphrases_action;

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ Wincher_Enabled_Conditional::class ];
	}

	/**
	 * Wincher_Route constructor.
	 *
	 * @param Wincher_Login_Action      $login_action      The login action.
	 * @param Wincher_Account_Action    $account_action    The account action.
	 * @param Wincher_Keyphrases_Action $keyphrases_action The keyphrases action.
	 */
	public function __construct(
		Wincher_Login_Action $login_action,
		Wincher_Account_Action $account_action,
		Wincher_Keyphrases_Action $keyphrases_action
	) {
		$this->login_action      = $login_action;
		$this->account_action    = $account_action;
		$this->keyphrases_action = $keyphrases_action;
	}

	/**
	 * Registers routes with WordPress.
	 *
	 * @return void
	 */
	public function register_routes() {
		$authorize_route_args = [
			'methods'             => 'GET',
			'callback'            => [ $this, 'get_authorization_url' ],
			'permission_callback' => [ $this, 'can_use_wincher' ],
		];
		\register_rest_route( Main::API_V1_NAMESPACE, self::AUTHORIZATION_URL_ROUTE, $authorize_route_args );

		$authentication_route_args = [
			'methods'             => 'POST',
			'callback'            => [ $this, 'authenticate' ],
			'permission_callback' => [ $this, 'can_use_wincher' ],
			'args'                => [
				'code' => [
					'validate_callback' => [ $this, 'has_valid_code' ],
					'required'          => true,
				],
				'websiteId' => [
					'validate_callback' => [ $this, 'has_valid_website_id' ],
					'required'          => true,
				],
			],
		];

		\register_rest_route( Main::API_V1_NAMESPACE, self::AUTHENTICATION_ROUTE, $authentication_route_args );

		$track_keyphrases_route_args = [
			'methods'             => 'POST',
			'callback'            => [ $this, 'track_keyphrases' ],
			'permission_callback' => [ $this, 'can_use_wincher' ],
			'args'                => [
				'keyphrases' => [
					'required'          => true,
				],
			],
		];

		\register_rest_route( Main::API_V1_NAMESPACE, self::KEYPHRASES_TRACK_ROUTE, $track_keyphrases_route_args );

		$get_keyphrases_route_args = [
			'methods'             => 'POST',
			'callback'            => [ $this, 'get_tracked_keyphrases' ],
			'permission_callback' => [ $this, 'can_use_wincher' ],
			'args'                => [
				'keyphrases' => [
					'required' => false,
				],
				'permalink' => [
					'required' => false,
				],
				'startAt' => [
					'required' => false,
				],
			],
		];

		\register_rest_route( Main::API_V1_NAMESPACE, self::TRACKED_KEYPHRASES_ROUTE, $get_keyphrases_route_args );

		$delete_keyphrase_route_args = [
			'methods'             => 'DELETE',
			'callback'            => [ $this, 'untrack_keyphrase' ],
			'permission_callback' => [ $this, 'can_use_wincher' ],
		];

		\register_rest_route( Main::API_V1_NAMESPACE, self::UNTRACK_KEYPHRASE_ROUTE, $delete_keyphrase_route_args );

		$check_limit_route_args = [
			'methods'             => 'GET',
			'callback'            => [ $this, 'check_limit' ],
			'permission_callback' => [ $this, 'can_use_wincher' ],
		];

		\register_rest_route( Main::API_V1_NAMESPACE, self::CHECK_LIMIT_ROUTE, $check_limit_route_args );

		$get_upgrade_campaign_route_args = [
			'methods'             => 'GET',
			'callback'            => [ $this, 'get_upgrade_campaign' ],
			'permission_callback' => [ $this, 'can_use_wincher' ],
		];

		\register_rest_route( Main::API_V1_NAMESPACE, self::UPGRADE_CAMPAIGN_ROUTE, $get_upgrade_campaign_route_args );
	}

	/**
	 * Returns the authorization URL.
	 *
	 * @return WP_REST_Response The response.
	 */
	public function get_authorization_url() {
		$data = $this->login_action->get_authorization_url();
		return new WP_REST_Response( $data, $data->status );
	}

	/**
	 * Authenticates with Wincher.
	 *
	 * @param WP_REST_Request $request The request. This request should have a code param set.
	 *
	 * @return WP_REST_Response The response.
	 */
	public function authenticate( WP_REST_Request $request ) {
		$data = $this
			->login_action
			->authenticate( $request['code'], (string) $request['websiteId'] );

		return new WP_REST_Response( $data, $data->status );
	}

	/**
	 * Posts keyphrases to track.
	 *
	 * @param WP_REST_Request $request The request. This request should have a code param set.
	 *
	 * @return WP_REST_Response The response.
	 */
	public function track_keyphrases( WP_REST_Request $request ) {
		$limits = $this->account_action->check_limit();

		if ( $limits->status !== 200 ) {
			return new WP_REST_Response( $limits, $limits->status );
		}

		$data = $this->keyphrases_action->track_keyphrases( $request['keyphrases'], $limits );

		return new WP_REST_Response( $data, $data->status );
	}

	/**
	 * Gets the tracked keyphrases via POST.
	 * This is done via POST, so we don't potentially run into URL limit issues when a lot of long keyphrases are tracked.
	 *
	 * @param WP_REST_Request $request The request. This request should have a code param set.
	 *
	 * @return WP_REST_Response The response.
	 */
	public function get_tracked_keyphrases( WP_REST_Request $request ) {
		$data = $this->keyphrases_action->get_tracked_keyphrases( $request['keyphrases'], $request['permalink'], $request['startAt'] );

		return new WP_REST_Response( $data, $data->status );
	}

	/**
	 * Untracks the tracked keyphrase.
	 *
	 * @param WP_REST_Request $request The request. This request should have a code param set.
	 *
	 * @return WP_REST_Response The response.
	 */
	public function untrack_keyphrase( WP_REST_Request $request ) {
		$data = $this->keyphrases_action->untrack_keyphrase( $request['keyphraseID'] );

		return new WP_REST_Response( $data, $data->status );
	}

	/**
	 * Checks the account limit.
	 *
	 * @return WP_REST_Response The response.
	 */
	public function check_limit() {
		$data = $this->account_action->check_limit();
		return new WP_REST_Response( $data, $data->status );
	}

	/**
	 * Gets the upgrade campaign.
	 * If it's not a free user, no campaign is returned.
	 *
	 * @return WP_REST_Response The response.
	 */
	public function get_upgrade_campaign() {
		$data = $this->account_action->get_upgrade_campaign();
		return new WP_REST_Response( $data, $data->status );
	}

	/**
	 * Checks if a valid code was returned.
	 *
	 * @param string $code The code to check.
	 *
	 * @return bool Whether the code is valid.
	 */
	public function has_valid_code( $code ) {
		return $code !== '';
	}

	/**
	 * Checks if a valid website_id was returned.
	 *
	 * @param int $website_id The website_id to check.
	 *
	 * @return bool Whether the website_id is valid.
	 */
	public function has_valid_website_id( $website_id ) {
		return ! empty( $website_id ) && \is_int( $website_id );
	}

	/**
	 * Whether the current user is allowed to publish post/pages and thus use the Wincher integration.
	 *
	 * @return bool Whether the current user is allowed to use Wincher.
	 */
	public function can_use_wincher() {
		return \current_user_can( 'publish_posts' ) || \current_user_can( 'publish_pages' );
	}
}
