<?php

namespace Yoast\WP\SEO\Routes;

use WP_REST_Request;
use WP_REST_Response;
use Yoast\WP\SEO\Conditionals\No_Conditionals;
use Yoast\WP\SEO\Main;

/**
 * Meta_Search_Route class
 */
class Meta_Search_Route implements Route_Interface {

	use No_Conditionals;

	/**
	 * Represents meta search route.
	 *
	 * @var string
	 */
	public const META_SEARCH_ROUTE = '/meta/search';

	/**
	 * Registers routes with WordPress.
	 *
	 * @return void
	 */
	public function register_routes() {
		$route = [
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'search_meta' ],
				'permission_callback' => [ $this, 'permission_check' ],
			],
		];

		\register_rest_route( Main::API_V1_NAMESPACE, self::META_SEARCH_ROUTE, $route );
	}

	/**
	 * Performs the permission check.
	 *
	 * @param WP_REST_Request $request The request.
	 *
	 * @return bool
	 */
	public function permission_check( $request ) {
		if ( ! isset( $request['post_id'] ) ) {
			return false;
		}

		$post_type        = \get_post_type( $request['post_id'] );
		$post_type_object = \get_post_type_object( $post_type );

		return \current_user_can( $post_type_object->cap->edit_posts );
	}

	/**
	 * Searches meta fields of a given post.
	 *
	 * @param WP_REST_Request $request The REST request.
	 *
	 * @return WP_REST_Response
	 */
	public function search_meta( $request ) {
		$post_id = $request['post_id'];
		$query   = $request['query'];
		$meta    = \get_post_custom( $post_id );
		$matches = [];

		foreach ( $meta as $key => $values ) {
			if ( \substr( $key, 0, \strlen( $query ) ) !== $query ) {
				continue;
			}

			if ( empty( $query ) && \substr( $key, 0, 1 ) === '_' ) {
				continue;
			}

			// Skip custom field values that are serialized.
			if ( \is_serialized( $values[0] ) ) {
				continue;
			}

			$matches[] = [
				'key'   => $key,
				'value' => $values[0],
			];

			if ( \count( $matches ) >= 25 ) {
				break;
			}
		}

		return \rest_ensure_response( [ 'meta' => $matches ] );
	}
}
