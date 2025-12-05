<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\AI_Authorization\User_Interface;

/**
 * Registers the callback route used in the authorization process.
 *
 * @makePublic
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class Callback_Route extends Abstract_Callback_Route {
	/**
	 *  The prefix for this route.
	 *
	 * @var string
	 */
	public const ROUTE_PREFIX = '/ai_generator/callback';

	/**
	 * Registers routes with WordPress.
	 *
	 * @return void
	 */
	public function register_routes() {
		\register_rest_route(
			parent::ROUTE_NAMESPACE,
			self::ROUTE_PREFIX,
			[
				'methods'             => 'POST',
				'args'                => [
					'access_jwt'     => [
						'required'    => true,
						'type'        => 'string',
						'description' => 'The access JWT.',
					],
					'refresh_jwt'    => [
						'required'    => true,
						'type'        => 'string',
						'description' => 'The JWT to be used when the access JWT needs to be refreshed.',
					],
					'code_challenge' => [
						'required'    => true,
						'type'        => 'string',
						'description' => 'The SHA266 of the verification code used to check the authenticity of a callback call.',
					],
					'user_id'        => [
						'required'    => true,
						'type'        => 'integer',
						'description' => 'The id of the user associated to the code verifier.',
					],
				],
				'callback'            => [ $this, 'callback' ],
				'permission_callback' => '__return_true',
			]
		);
	}
}
