<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\AI_Authorization\User_Interface;

use RuntimeException;
use WP_REST_Request;
use WP_REST_Response;
use Yoast\WP\SEO\AI_Authorization\Infrastructure\Access_Token_User_Meta_Repository_Interface;
use Yoast\WP\SEO\AI_Authorization\Infrastructure\Code_Verifier_User_Meta_Repository_Interface;
use Yoast\WP\SEO\AI_Authorization\Infrastructure\Refresh_Token_User_Meta_Repository_Interface;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Unauthorized_Exception;
use Yoast\WP\SEO\Conditionals\AI_Conditional;
use Yoast\WP\SEO\Main;
use Yoast\WP\SEO\Routes\Route_Interface;

/**
 * The base class for the callback routes.
 */
abstract class Abstract_Callback_Route implements Route_Interface {

	/**
	 *  The namespace for this route.
	 *
	 * @var string
	 */
	public const ROUTE_NAMESPACE = Main::API_V1_NAMESPACE;

	/**
	 * The access token repository instance.
	 *
	 * @var Access_Token_User_Meta_Repository_Interface
	 */
	protected $access_token_repository;

	/**
	 * The refresh token repository instance.
	 *
	 * @var Refresh_Token_User_Meta_Repository_Interface
	 */
	protected $refresh_token_repository;

	/**
	 * The code verifier instance.
	 *
	 * @var Code_Verifier_User_Meta_Repository_Interface
	 */
	protected $code_verifier_repository;

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array<string> The conditionals.
	 */
	public static function get_conditionals() {
		return [ AI_Conditional::class ];
	}

	/**
	 * Callback_Route constructor.
	 *
	 * @param Access_Token_User_Meta_Repository_Interface  $access_token_repository  The access token repository instance.
	 * @param Refresh_Token_User_Meta_Repository_Interface $refresh_token_repository The refresh token repository instance.
	 * @param Code_Verifier_User_Meta_Repository_Interface $code_verifier_repository The code verifier instance.
	 */
	public function __construct( Access_Token_User_Meta_Repository_Interface $access_token_repository, Refresh_Token_User_Meta_Repository_Interface $refresh_token_repository, Code_Verifier_User_Meta_Repository_Interface $code_verifier_repository ) {
		$this->access_token_repository  = $access_token_repository;
		$this->refresh_token_repository = $refresh_token_repository;
		$this->code_verifier_repository = $code_verifier_repository;
	}

	// phpcs:disable Squiz.Commenting.FunctionCommentThrowTag.WrongNumber -- PHPCS doesn't take into account exceptions thrown in called methods.

	/**
	 * Runs the callback to store connection credentials and the tokens locally.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response The response of the callback action.
	 *
	 * @throws Unauthorized_Exception If the code challenge  is not valid.
	 * @throws RuntimeException If the verification code is not found.
	 */
	public function callback( WP_REST_Request $request ): WP_REST_Response {
		$user_id = $request->get_param( 'user_id' );
		try {
			$code_verifier = $this->code_verifier_repository->get_code_verifier( $user_id );

			if ( $request->get_param( 'code_challenge' ) !== \hash( 'sha256', $code_verifier->get_code() ) ) {
				throw new Unauthorized_Exception( 'Unauthorized' );
			}

			$this->access_token_repository->store_token( $user_id, $request->get_param( 'access_jwt' ) );
			$this->refresh_token_repository->store_token( $user_id, $request->get_param( 'refresh_jwt' ) );
			$this->code_verifier_repository->delete_code_verifier( $user_id );
		} catch ( Unauthorized_Exception | RuntimeException $e ) {
			return new WP_REST_Response( 'Unauthorized.', 401 );
		}

		return new WP_REST_Response(
			[
				'message'       => 'Tokens successfully stored.',
				'code_verifier' => $code_verifier->get_code(),
			]
		);
	}

	// phpcs:enable Squiz.Commenting.FunctionCommentThrowTag.WrongNumber -- PHPCS doesn't take into account exceptions thrown in called methods.
}
