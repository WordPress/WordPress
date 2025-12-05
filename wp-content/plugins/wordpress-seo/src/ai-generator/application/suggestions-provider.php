<?php

namespace Yoast\WP\SEO\AI_Generator\Application;

use RuntimeException;
use WP_User;
use Yoast\WP\SEO\AI_Authorization\Application\Token_Manager;
use Yoast\WP\SEO\AI_Consent\Application\Consent_Handler;
use Yoast\WP\SEO\AI_Generator\Domain\Suggestion;
use Yoast\WP\SEO\AI_Generator\Domain\Suggestions_Bucket;
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
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Response;
use Yoast\WP\SEO\Helpers\User_Helper;

/**
 * The class that handles the suggestions from the AI API.
 */
class Suggestions_Provider {

	/**
	 * The consent handler instance.
	 *
	 * @var Consent_Handler
	 */
	private $consent_handler;

	/**
	 * The request handler instance.
	 *
	 * @var Request_Handler
	 */
	private $request_handler;

	/**
	 * The token manager instance.
	 *
	 * @var Token_Manager
	 */
	private $token_manager;

	/**
	 * The user helper instance.
	 *
	 * @var User_Helper
	 */
	private $user_helper;

	/**
	 * Class constructor.
	 *
	 * @param Consent_Handler $consent_handler The consent handler instance.
	 * @param Request_Handler $request_handler The request handler instance.
	 * @param Token_Manager   $token_manager   The token manager instance.
	 * @param User_Helper     $user_helper     The user helper instance.
	 */
	public function __construct(
		Consent_Handler $consent_handler,
		Request_Handler $request_handler,
		Token_Manager $token_manager,
		User_Helper $user_helper
	) {
		$this->consent_handler = $consent_handler;
		$this->request_handler = $request_handler;
		$this->token_manager   = $token_manager;
		$this->user_helper     = $user_helper;
	}

	// phpcs:disable Squiz.Commenting.FunctionCommentThrowTag.WrongNumber -- PHPCS doesn't take into account exceptions thrown in called methods.

	/**
	 * Method used to generate suggestions through AI.
	 *
	 * @param WP_User $user                  The WP user.
	 * @param string  $suggestion_type       The type of the requested suggestion.
	 * @param string  $prompt_content        The excerpt taken from the post.
	 * @param string  $focus_keyphrase       The focus keyphrase associated to the post.
	 * @param string  $language              The language of the post.
	 * @param string  $platform              The platform the post is intended for.
	 * @param string  $editor                The current editor.
	 * @param bool    $retry_on_unauthorized Whether to retry when unauthorized (mechanism to retry once).
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
	 * @throws RuntimeException Unable to retrieve the access token.
	 * @return string[] The suggestions.
	 */
	public function get_suggestions(
		WP_User $user,
		string $suggestion_type,
		string $prompt_content,
		string $focus_keyphrase,
		string $language,
		string $platform,
		string $editor,
		bool $retry_on_unauthorized = true
	): array {
		$token = $this->token_manager->get_or_request_access_token( $user );

		$request_body    = [
			'service' => 'openai',
			'user_id' => (string) $user->ID,
			'subject' => [
				'content'         => $prompt_content,
				'focus_keyphrase' => $focus_keyphrase,
				'language'        => $language,
				'platform'        => $platform,
			],
		];
		$request_headers = [
			'Authorization' => "Bearer $token",
			'X-Yst-Cohort'  => $editor,
		];

		try {
			$response = $this->request_handler->handle( new Request( "/openai/suggestions/$suggestion_type", $request_body, $request_headers ) );
		} catch ( Unauthorized_Exception $exception ) {
			// Delete the stored JWT tokens, as they appear to be no longer valid.
			$this->user_helper->delete_meta( $user->ID, '_yoast_wpseo_ai_generator_access_jwt' );
			$this->user_helper->delete_meta( $user->ID, '_yoast_wpseo_ai_generator_refresh_jwt' );

			if ( ! $retry_on_unauthorized ) {
				throw $exception;
			}

			// Try again once more by fetching a new set of tokens and trying the suggestions endpoint again.
			return $this->get_suggestions( $user, $suggestion_type, $prompt_content, $focus_keyphrase, $language, $platform, $editor, false );
		} catch ( Forbidden_Exception $exception ) {
			// Follow the API in the consent being revoked (Use case: user sent an e-mail to revoke?).
			// phpcs:disable WordPress.Security.EscapeOutput.ExceptionNotEscaped -- false positive.
			$this->consent_handler->revoke_consent( $user->ID );
			throw new Forbidden_Exception( 'CONSENT_REVOKED', $exception->getCode() );
			// phpcs:enable WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}

		return $this->build_suggestions_array( $response )->to_array();
	}

	// phpcs:enable Squiz.Commenting.FunctionCommentThrowTag.WrongNumber

	/**
	 * Generates the list of 5 suggestions to return.
	 *
	 * @param Response $response The response from the API.
	 *
	 * @return Suggestions_Bucket The array of suggestions.
	 */
	public function build_suggestions_array( Response $response ): Suggestions_Bucket {
		$suggestions_bucket = new Suggestions_Bucket();
		$json               = \json_decode( $response->get_body() );
		if ( $json === null || ! isset( $json->choices ) ) {
			return $suggestions_bucket;
		}
		foreach ( $json->choices as $suggestion ) {
			$suggestions_bucket->add_suggestion( new Suggestion( $suggestion->text ) );
		}

		return $suggestions_bucket;
	}
}
