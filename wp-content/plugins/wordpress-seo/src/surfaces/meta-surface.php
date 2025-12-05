<?php

namespace Yoast\WP\SEO\Surfaces;

use Yoast\WP\SEO\Context\Meta_Tags_Context;
use Yoast\WP\SEO\Helpers\Indexable_Helper;
use Yoast\WP\SEO\Memoizers\Meta_Tags_Context_Memoizer;
use Yoast\WP\SEO\Models\Indexable;
use Yoast\WP\SEO\Repositories\Indexable_Repository;
use Yoast\WP\SEO\Surfaces\Values\Meta;
use Yoast\WP\SEO\Wrappers\WP_Rewrite_Wrapper;
use YoastSEO_Vendor\Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Meta_Surface class.
 *
 * Surface for the indexables.
 */
class Meta_Surface {

	/**
	 * The container.
	 *
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * The memoizer for the meta tags context.
	 *
	 * @var Meta_Tags_Context_Memoizer
	 */
	private $context_memoizer;

	/**
	 * The indexable repository.
	 *
	 * @var Indexable_Repository
	 */
	private $repository;

	/**
	 * Holds the WP rewrite wrapper instance.
	 *
	 * @var WP_Rewrite_Wrapper
	 */
	private $wp_rewrite_wrapper;

	/**
	 * The indexable helper.
	 *
	 * @var Indexable_Helper
	 */
	private $indexable_helper;

	/**
	 * Meta_Surface constructor.
	 *
	 * @param ContainerInterface         $container            The DI container.
	 * @param Meta_Tags_Context_Memoizer $context_memoizer     The meta tags context memoizer.
	 * @param Indexable_Repository       $indexable_repository The indexable repository.
	 * @param WP_Rewrite_Wrapper         $wp_rewrite_wrapper   The WP rewrite wrapper.
	 * @param Indexable_Helper           $indexable_helper     The indexable helper.
	 */
	public function __construct(
		ContainerInterface $container,
		Meta_Tags_Context_Memoizer $context_memoizer,
		Indexable_Repository $indexable_repository,
		WP_Rewrite_Wrapper $wp_rewrite_wrapper,
		Indexable_Helper $indexable_helper
	) {
		$this->container          = $container;
		$this->context_memoizer   = $context_memoizer;
		$this->repository         = $indexable_repository;
		$this->wp_rewrite_wrapper = $wp_rewrite_wrapper;
		$this->indexable_helper   = $indexable_helper;
	}

	/**
	 * Returns the meta tags context for the current page.
	 *
	 * @return Meta The meta values.
	 */
	public function for_current_page() {
		return $this->build_meta( $this->context_memoizer->for_current_page() );
	}

	/**
	 * Returns the meta tags context for the home page.
	 *
	 * @return Meta|false The meta values. False if none could be found.
	 */
	public function for_home_page() {
		$front_page_id = (int) \get_option( 'page_on_front' );
		if ( \get_option( 'show_on_front' ) === 'page' && $front_page_id !== 0 ) {
			$indexable = $this->repository->find_by_id_and_type( $front_page_id, 'post' );

			if ( ! $indexable ) {
				return false;
			}

			return $this->build_meta( $this->context_memoizer->get( $indexable, 'Static_Home_Page' ) );
		}

		$indexable = $this->repository->find_for_home_page();

		if ( ! $indexable ) {
			return false;
		}

		return $this->build_meta( $this->context_memoizer->get( $indexable, 'Home_Page' ) );
	}

	/**
	 * Returns the meta tags context for the posts page.
	 *
	 * @return Meta|false The meta values. False if none could be found.
	 */
	public function for_posts_page() {
		$posts_page_id = (int) \get_option( 'page_for_posts' );
		if ( $posts_page_id !== 0 ) {
			$indexable = $this->repository->find_by_id_and_type( $posts_page_id, 'post' );

			if ( ! $indexable ) {
				return false;
			}

			return $this->build_meta( $this->context_memoizer->get( $indexable, 'Static_Posts_Page' ) );
		}

		$indexable = $this->repository->find_for_home_page();

		if ( ! $indexable ) {
			return false;
		}

		return $this->build_meta( $this->context_memoizer->get( $indexable, 'Home_Page' ) );
	}

	/**
	 * Returns the meta tags context for a post type archive.
	 *
	 * @param string|null $post_type Optional. The post type to get the archive meta for. Defaults to the current post type.
	 *
	 * @return Meta|false The meta values. False if none could be found.
	 */
	public function for_post_type_archive( $post_type = null ) {
		if ( $post_type === null ) {
			$post_type = \get_post_type();
		}

		$indexable = $this->repository->find_for_post_type_archive( $post_type );

		if ( ! $indexable ) {
			return false;
		}

		return $this->build_meta( $this->context_memoizer->get( $indexable, 'Post_Type_Archive' ) );
	}

	/**
	 * Returns the meta tags context for the search result page.
	 *
	 * @return Meta|false The meta values. False if none could be found.
	 */
	public function for_search_result() {
		$indexable = $this->repository->find_for_system_page( 'search-result' );

		if ( ! $indexable ) {
			return false;
		}

		return $this->build_meta( $this->context_memoizer->get( $indexable, 'Search_Result_Page' ) );
	}

	/**
	 * Returns the meta tags context for the search result page.
	 *
	 * @return Meta|false The meta values. False if none could be found.
	 */
	public function for_404() {
		$indexable = $this->repository->find_for_system_page( '404' );

		if ( ! $indexable ) {
			return false;
		}

		return $this->build_meta( $this->context_memoizer->get( $indexable, 'Error_Page' ) );
	}

	/**
	 * Returns the meta tags context for a post.
	 *
	 * @param int $id The ID of the post.
	 *
	 * @return Meta|false The meta values. False if none could be found.
	 */
	public function for_post( $id ) {
		$indexable = $this->repository->find_by_id_and_type( $id, 'post' );

		if ( ! $indexable ) {
			return false;
		}

		return $this->build_meta( $this->context_memoizer->get( $indexable, 'Post_Type' ) );
	}

	/**
	 * Returns the meta tags context for a number of posts.
	 *
	 * @param int[] $ids The IDs of the posts.
	 *
	 * @return Meta[]|false The meta values. False if none could be found.
	 */
	public function for_posts( $ids ) {
		$indexables = $this->repository->find_by_multiple_ids_and_type( $ids, 'post' );

		if ( empty( $indexables ) ) {
			return false;
		}

		// Remove all false values.
		$indexables = \array_filter( $indexables );

		return \array_map(
			function ( $indexable ) {
				return $this->build_meta( $this->context_memoizer->get( $indexable, 'Post_Type' ) );
			},
			$indexables
		);
	}

	/**
	 * Returns the meta tags context for a term.
	 *
	 * @param int $id The ID of the term.
	 *
	 * @return Meta|false The meta values. False if none could be found.
	 */
	public function for_term( $id ) {
		$indexable = $this->repository->find_by_id_and_type( $id, 'term' );

		if ( ! $indexable ) {
			return false;
		}

		return $this->build_meta( $this->context_memoizer->get( $indexable, 'Term_Archive' ) );
	}

	/**
	 * Returns the meta tags context for an author.
	 *
	 * @param int $id The ID of the author.
	 *
	 * @return Meta|false The meta values. False if none could be found.
	 */
	public function for_author( $id ) {
		$indexable = $this->repository->find_by_id_and_type( $id, 'user' );

		if ( ! $indexable ) {
			return false;
		}

		return $this->build_meta( $this->context_memoizer->get( $indexable, 'Author_Archive' ) );
	}

	/**
	 * Returns the meta for an indexable.
	 *
	 * @param Indexable   $indexable The indexable.
	 * @param string|null $page_type Optional. The page type if already known.
	 *
	 * @return Meta|false The meta values. False if none could be found.
	 */
	public function for_indexable( $indexable, $page_type = null ) {

		if ( ! \is_a( $indexable, Indexable::class ) ) {
			return false;
		}
		if ( $page_type === null ) {
			$page_type = $this->indexable_helper->get_page_type_for_indexable( $indexable );
		}

		return $this->build_meta( $this->context_memoizer->get( $indexable, $page_type ) );
	}

	/**
	 * Returns the meta for an indexable.
	 *
	 * @param Indexable[] $indexables The indexables.
	 * @param string|null $page_type  Optional. The page type if already known.
	 *
	 * @return Meta|false The meta values. False if none could be found.
	 */
	public function for_indexables( $indexables, $page_type = null ) {
		$closure = function ( $indexable ) use ( $page_type ) {
			$this_page_type = $page_type;
			if ( $this_page_type === null ) {
				$this_page_type = $this->indexable_helper->get_page_type_for_indexable( $indexable );
			}

			return $this->build_meta( $this->context_memoizer->get( $indexable, $this_page_type ) );
		};

		return \array_map( $closure, $indexables );
	}

	/**
	 * Returns the meta tags context for a url.
	 *
	 * @param string $url The url of the page. Required to be relative to the site url.
	 *
	 * @return Meta|false The meta values. False if none could be found.
	 */
	public function for_url( $url ) {
		$url_parts  = \wp_parse_url( $url );
		$site_parts = \wp_parse_url( \site_url() );

		if ( ( ! \is_array( $url_parts ) || ! \is_array( $site_parts ) )
			|| ! isset( $url_parts['host'], $url_parts['path'], $site_parts['host'], $site_parts['scheme'] )
		) {
			return false;
		}

		if ( $url_parts['host'] !== $site_parts['host'] ) {
			return false;
		}
		// Ensure the scheme is consistent with values in the DB.
		$url = $site_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'];

		if ( $this->is_date_archive_url( $url ) ) {
			$indexable = $this->repository->find_for_date_archive();
		}
		else {
			$indexable = $this->repository->find_by_permalink( $url );
		}

		// If we still don't have an indexable abort, the WP globals could be anything so we can't use the unknown indexable.
		if ( ! $indexable ) {
			return false;
		}
		$page_type = $this->indexable_helper->get_page_type_for_indexable( $indexable );

		if ( $page_type === false ) {
			return false;
		}

		return $this->build_meta( $this->context_memoizer->get( $indexable, $page_type ) );
	}

	/**
	 * Checks if a given URL is a date archive URL.
	 *
	 * @param string $url The url.
	 *
	 * @return bool
	 */
	protected function is_date_archive_url( $url ) {
		$path = \wp_parse_url( $url, \PHP_URL_PATH );
		if ( $path === null ) {
			return false;
		}

		$path         = \ltrim( $path, '/' );
		$wp_rewrite   = $this->wp_rewrite_wrapper->get();
		$date_rewrite = $wp_rewrite->generate_rewrite_rules( $wp_rewrite->get_date_permastruct(), \EP_DATE );
		$date_rewrite = \apply_filters( 'date_rewrite_rules', $date_rewrite );

		foreach ( (array) $date_rewrite as $match => $query ) {
			if ( \preg_match( "#^$match#", $path ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Creates a new meta value object
	 *
	 * @param Meta_Tags_Context $context The meta tags context.
	 *
	 * @return Meta The meta value
	 */
	protected function build_meta( Meta_Tags_Context $context ) {
		return new Meta( $context, $this->container );
	}
}
