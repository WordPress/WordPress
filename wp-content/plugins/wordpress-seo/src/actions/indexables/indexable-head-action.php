<?php

namespace Yoast\WP\SEO\Actions\Indexables;

use Yoast\WP\SEO\Surfaces\Meta_Surface;
use Yoast\WP\SEO\Surfaces\Values\Meta;

/**
 * Get head action for indexables.
 */
class Indexable_Head_Action {

	/**
	 * Caches the output.
	 *
	 * @var mixed
	 */
	protected $cache;

	/**
	 * The meta surface.
	 *
	 * @var Meta_Surface
	 */
	private $meta_surface;

	/**
	 * Indexable_Head_Action constructor.
	 *
	 * @param Meta_Surface $meta_surface The meta surface.
	 */
	public function __construct( Meta_Surface $meta_surface ) {
		$this->meta_surface = $meta_surface;
	}

	/**
	 * Retrieves the head for a url.
	 *
	 * @param string $url The url to get the head for.
	 *
	 * @return object Object with head and status properties.
	 */
	public function for_url( $url ) {
		if ( $url === \trailingslashit( \get_home_url() ) ) {
			return $this->with_404_fallback( $this->with_cache( 'home_page' ) );
		}
		return $this->with_404_fallback( $this->with_cache( 'url', $url ) );
	}

	/**
	 * Retrieves the head for a post.
	 *
	 * @param int $id The id.
	 *
	 * @return object Object with head and status properties.
	 */
	public function for_post( $id ) {
		return $this->with_404_fallback( $this->with_cache( 'post', $id ) );
	}

	/**
	 * Retrieves the head for a term.
	 *
	 * @param int $id The id.
	 *
	 * @return object Object with head and status properties.
	 */
	public function for_term( $id ) {
		return $this->with_404_fallback( $this->with_cache( 'term', $id ) );
	}

	/**
	 * Retrieves the head for an author.
	 *
	 * @param int $id The id.
	 *
	 * @return object Object with head and status properties.
	 */
	public function for_author( $id ) {
		return $this->with_404_fallback( $this->with_cache( 'author', $id ) );
	}

	/**
	 * Retrieves the head for a post type archive.
	 *
	 * @param int $type The id.
	 *
	 * @return object Object with head and status properties.
	 */
	public function for_post_type_archive( $type ) {
		return $this->with_404_fallback( $this->with_cache( 'post_type_archive', $type ) );
	}

	/**
	 * Retrieves the head for the posts page.
	 *
	 * @return object Object with head and status properties.
	 */
	public function for_posts_page() {
		return $this->with_404_fallback( $this->with_cache( 'posts_page' ) );
	}

	/**
	 * Retrieves the head for the 404 page. Always sets the status to 404.
	 *
	 * @return object Object with head and status properties.
	 */
	public function for_404() {
		$meta = $this->with_cache( '404' );

		if ( ! $meta ) {
			return (object) [
				'html'   => '',
				'json'   => [],
				'status' => 404,
			];
		}

		$head = $meta->get_head();

		return (object) [
			'html'   => $head->html,
			'json'   => $head->json,
			'status' => 404,
		];
	}

	/**
	 * Retrieves the head for a successful page load.
	 *
	 * @param object $head The calculated Yoast head.
	 *
	 * @return object The presentations and status code 200.
	 */
	protected function for_200( $head ) {
		return (object) [
			'html'   => $head->html,
			'json'   => $head->json,
			'status' => 200,
		];
	}

	/**
	 * Returns the head with 404 fallback
	 *
	 * @param Meta|false $meta The meta object.
	 *
	 * @return object The head response.
	 */
	protected function with_404_fallback( $meta ) {
		if ( $meta === false ) {
			return $this->for_404();
		}
		else {
			return $this->for_200( $meta->get_head() );
		}
	}

	/**
	 * Retrieves a value from the meta surface cached.
	 *
	 * @param string $type     The type of value to retrieve.
	 * @param string $argument Optional. The argument for the value.
	 *
	 * @return Meta The meta object.
	 */
	protected function with_cache( $type, $argument = '' ) {
		if ( ! isset( $this->cache[ $type ][ $argument ] ) ) {
			$this->cache[ $type ][ $argument ] = \call_user_func( [ $this->meta_surface, "for_$type" ], $argument );
		}

		return $this->cache[ $type ][ $argument ];
	}
}
