<?php

namespace Yoast\WP\SEO\AI_Authorization\Application;

use RuntimeException;
use WP_User;
use WPSEO_Utils;
use Yoast\WP\SEO\AI_Authorization\Infrastructure\Access_Token_User_Meta_Repository_Interface;
use Yoast\WP\SEO\AI_Authorization\Infrastructure\Code_Verifier_User_Meta_Repository;
use Yoast\WP\SEO\AI_Authorization\Infrastructure\Refresh_Token_User_Meta_Repository_Interface;
use Yoast\WP\SEO\AI_Consent\Application\Consent_Handler;
use Yoast\WP\SEO\AI_Generator\Infrastructure\WordPress_URLs;
use Yoast\WP\SEO\AI_HTTP_Request\Application\Request_Handler;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Bad_Request_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Forbidden_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Internal_Server_Error_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Not_Found_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Payment_Required_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Request_Timeout_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Service_Unavailable_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Too_Many_Requests_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Unauthorized_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Request;
use Yoast\WP\SEO\Helpers\User_Helper;

/**
 * Class Token_Manager
 * Handles the management of JWT tokens used in the authorization process.
 *
 * @makePublic
 */
class Token_Manager implements Token_Manager_Interface {

	/**
	 * The access token repository.
	 *
	 * @var Access_Token_User_Meta_Repository_Interface
	 */
	private $access_token_repository;

	/**
	 * The code verifier service.
	 *
	 * @var Code_Verifier_Handler
	 */
	private $code_verifier;

	/**
	 * The consent handler.
	 *
	 * @var Consent_Handler
	 */
	private $consent_handler;

	/**
	 * The refresh token repository.
	 *
	 * @var Refresh_Token_User_Meta_Repository_Interface
	 */
	private $refresh_token_repository;

	/**
	 * The user helper.
	 *
	 * @var User_Helper
	 */
	private $user_helper;

	/**
	 * The code verifier repository.
	 *
	 * @var Code_Verifier_User_Meta_Repository
	 */
	private $code_verifier_repository;

	/**
	 * The URLs service.
	 *
	 * @var WordPress_URLs
	 */
	private $urls;

	/**
	 * The request handler.
	 *
	 * @var Request_Handler
	 */
	private $request_handler;

	/**
	 * Token_Manager constructor.
	 *
	 * @param Access_Token_User_Meta_Repository_Interface  $access_token_repository  The access token repository.
	 * @param Code_Verifier_Handler                        $code_verifier            The code verifier service.
	 * @param Consent_Handler                              $consent_handler          The consent handler.
	 * @param Refresh_Token_User_Meta_Repository_Interface $refresh_token_repository The refresh token repository.
	 * @param User_Helper                                  $user_helper              The user helper.
	 * @param Request_Handler                              $request_handler          The request handler.
	 * @param Code_Verifier_User_Meta_Repository           $code_verifier_repository The code verifier repository.
	 * @param WordPress_URLs                               $urls                     The URLs service.
	 */
	public function __construct(
		Access_Token_User_Meta_Repository_Interface $access_token_repository,
		Code_Verifier_Handler $code_verifier,
		Consent_Handler $consent_handler,
		Refresh_Token_User_Meta_Repository_Interface $refresh_token_repository,
		User_Helper $user_helper,
		Request_Handler $request_handler,
		Code_Verifier_User_Meta_Repository $code_verifier_repository,
		WordPress_URLs $urls
	) {
		$this->access_token_repository  = $access_token_repository;
		$this->code_verifier            = $code_verifier;
		$this->consent_handler          = $consent_handler;
		$this->refresh_token_repository = $refresh_token_repository;
		$this->user_helper              = $user_helper;
		$this->request_handler          = $request_handler;
		$this->code_verifier_repository = $code_verifier_repository;
		$this->urls                     = $urls;
	}

	// phpcs:disable Squiz.Commenting.FunctionCommentThrowTag.WrongNumber -- PHPCS doesn't take into account exceptions thrown in called methods.

	/**
	 * Invalidates the access token.
	 *
	 * @param string $user_id The user ID.
	 *
	 * @return void
	 *
	 * @throws Bad_Request_Exception Bad_Request_Exception.
	 * @throws Internal_Server_Error_Exception Internal_Server_Error_Exception.
	 * @throws Not_Found_Exception Not_Found_Exception.
	 * @throws Payment_Required_Exception Payment_Required_Exception.
	 * @throws Request_Timeout_Exception Request_Timeout_Exception.
	 * @throws Service_Unavailable_Exception Service_Unavailable_Exception.
	 * @throws Too_Many_Requests_Exception Too_Many_Requests_Exception.
	 * @throws RuntimeException Unable to retrieve the access token.
	 */
	public function token_invalidate( string $user_id ): void {
		try {
			$access_jwt = $this->access_token_repository->get_token( $user_id );
		} catch ( RuntimeException $e ) {
			$access_jwt = '';
		}

		$request_body    = [
			'user_id' => (string) $user_id,
		];
		$request_headers = [
			'Authorization' => "Bearer $access_jwt",
		];

		try {
			$this->request_handler->handle(
				new Request(
					'/token/invalidate',
					$request_body,
					$request_headers
				)
			);
		} catch ( Unauthorized_Exception | Forbidden_Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch -- Reason: Ignored on purpose.
			// If the credentials in our request were already invalid, our job is done and we continue to remove the tokens client-side.
		}

		// Delete the stored JWT tokens.
		$this->user_helper->delete_meta( $user_id, '_yoast_wpseo_ai_generator_access_jwt' );
		$this->user_helper->delete_meta( $user_id, '_yoast_wpseo_ai_generator_refresh_jwt' );
	}

	/**
	 * Requests a new set of JWT tokens.
	 *
	 * Requests a new JWT access and refresh token for a user from the Yoast AI Service and stores it in the database
	 * under usermeta. The storing of the token happens in a HTTP callback that is triggered by this request.
	 *
	 * @param WP_User $user The WP user.
	 *
	 * @return void
	 *
	 * @throws Bad_Request_Exception Bad_Request_Exception.
	 * @throws Forbidden_Exception Forbidden_Exception.
	 * @throws Internal_Server_Error_Exception Internal_Server_Error_Exception.
	 * @throws Not_Found_Exception Not_Found_Exception.
	 * @throws Payment_Required_Exception Payment_Required_Exception.
	 * @throws Request_Timeout_Exception Request_Timeout_Exception.
	 * @throws Service_Unavailable_Exception Service_Unavailable_Exception.
	 * @throws Too_Many_Requests_Exception Too_Many_Requests_Exception.
	 * @throws Unauthorized_Exception Unauthorized_Exception.
	 */
	public function token_request( WP_User $user ): void {
		// Ensure the user has given consent.
		if ( $this->user_helper->get_meta( $user->ID, '_yoast_wpseo_ai_consent', true ) !== '1' ) {
			// phpcs:disable WordPress.Security.EscapeOutput.ExceptionNotEscaped -- false positive.
			$this->consent_handler->revoke_consent( $user->ID );
			throw new Forbidden_Exception( 'CONSENT_REVOKED', 403 );

			// phpcs:enable WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}

		// Generate a code verifier and store it in the database.
		$code_verifier = $this->code_verifier->generate( $user->user_email );
		$this->code_verifier_repository->store_code_verifier( $user->ID, $code_verifier->get_code(), $code_verifier->get_created_at() );

		$request_body = [
			'service'              => 'openai',
			'code_challenge'       => \hash( 'sha256', $code_verifier->get_code() ),
			'license_site_url'     => WPSEO_Utils::get_home_url(),
			'user_id'              => (string) $user->ID,
			'callback_url'         => $this->urls->get_callback_url(),
			'refresh_callback_url' => $this->urls->get_refresh_callback_url(),
		];

		$this->request_handler->handle( new Request( '/token/request', $request_body ) );

		// The callback saves the metadata. Because that is in another session, we need to delete the current cache here. Or we may get the old token.
		\wp_cache_delete( $user->ID, 'user_meta' );
	}

	/**
	 * Refreshes the JWT access token.
	 *
	 * Refreshes a stored JWT access token for a user with the Yoast AI Service and stores it in the database under
	 * usermeta. The storing of the token happens in a HTTP callback that is triggered by this request.
	 *
	 * @param WP_User $user The WP user.
	 *
	 * @return void
	 *
	 * @throws Bad_Request_Exception Bad_Request_Exception.
	 * @throws Forbidden_Exception Forbidden_Exception.
	 * @throws Internal_Server_Error_Exception Internal_Server_Error_Exception.
	 * @throws Not_Found_Exception Not_Found_Exception.
	 * @throws Payment_Required_Exception Payment_Required_Exception.
	 * @throws Request_Timeout_Exception Request_Timeout_Exception.
	 * @throws Service_Unavailable_Exception Service_Unavailable_Exception.
	 * @throws Too_Many_Requests_Exception Too_Many_Requests_Exception.
	 * @throws Unauthorized_Exception Unauthorized_Exception.
	 * @throws RuntimeException Unable to retrieve the refresh token.
	 */
	public function token_refresh( WP_User $user ): void {
		$refresh_jwt = $this->refresh_token_repository->get_token( $user->ID );

		// Generate a code verifier and store it in the database.
		$code_verifier = $this->code_verifier->generate( $user->ID, $user->user_email );
		$this->code_verifier_repository->store_code_verifier( $user->ID, $code_verifier->get_code(), $code_verifier->get_created_at() );

		$request_body    = [
			'code_challenge' => \hash( 'sha256', $code_verifier->get_code() ),
		];
		$request_headers = [
			'Authorization' => "Bearer $refresh_jwt",
		];

		$this->request_handler->handle( new Request( '/token/refresh', $request_body, $request_headers ) );

		// The callback saves the metadata. Because that is in another session, we need to delete the current cache here. Or we may get the old token.
		\wp_cache_delete( $user->ID, 'user_meta' );
	}

	/**
	 * Checks whether the token has expired.
	 *
	 * @param string $jwt The JWT.
	 *
	 * @return bool Whether the token has expired.
	 */
	public function has_token_expired( string $jwt ): bool {
		$parts = \explode( '.', $jwt );
		if ( \count( $parts ) !== 3 ) {
			// Headers, payload and signature parts are not detected.
			return true;
		}

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode -- Reason: Decoding the payload of the JWT.
		$payload = \base64_decode( $parts[1] );
		$json    = \json_decode( $payload );
		if ( $json === null || ! isset( $json->exp ) ) {
			return true;
		}

		// Ensure exp is a valid numeric value.
		if ( ! \is_numeric( $json->exp ) ) {
			return true;
		}

		return $json->exp < \time();
	}

	/**
	 * Retrieves the access token.
	 *
	 * @param WP_User $user The WP user.
	 *
	 * @return string The access token.
	 *
	 * @throws Bad_Request_Exception Bad_Request_Exception.
	 * @throws Forbidden_Exception Forbidden_Exception.
	 * @throws Internal_Server_Error_Exception Internal_Server_Error_Exception.
	 * @throws Not_Found_Exception Not_Found_Exception.
	 * @throws Payment_Required_Exception Payment_Required_Exception.
	 * @throws Request_Timeout_Exception Request_Timeout_Exception.
	 * @throws Service_Unavailable_Exception Service_Unavailable_Exception.
	 * @throws Too_Many_Requests_Exception Too_Many_Requests_Exception.
	 * @throws Unauthorized_Exception Unauthorized_Exception.
	 * @throws RuntimeException Unable to retrieve the access or refresh token.
	 */
	public function get_or_request_access_token( WP_User $user ): string {
		$access_jwt = $this->user_helper->get_meta( $user->ID, '_yoast_wpseo_ai_generator_access_jwt', true );
		if ( ! \is_string( $access_jwt ) || $access_jwt === '' ) {
			$this->token_request( $user );
			$access_jwt = $this->access_token_repository->get_token( $user->ID );
		}
		elseif ( $this->has_token_expired( $access_jwt ) ) {
			try {
				$this->token_refresh( $user );
			} catch ( Unauthorized_Exception $exception ) {
				$this->token_request( $user );
			} catch ( Forbidden_Exception $exception ) {
				// Follow the API in the consent being revoked (Use case: user sent an e-mail to revoke?).
				// phpcs:disable WordPress.Security.EscapeOutput.ExceptionNotEscaped -- false positive.
				$this->consent_handler->revoke_consent( $user->ID );
				throw new Forbidden_Exception( 'CONSENT_REVOKED', 403 );
				// phpcs:enable WordPress.Security.EscapeOutput.ExceptionNotEscaped
			}
			$access_jwt = $this->access_token_repository->get_token( $user->ID );
		}

		return $access_jwt;
	}

	// phpcs:enable Squiz.Commenting.FunctionCommentThrowTag.WrongNumber
}
