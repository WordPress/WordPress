<?php

namespace Yoast\WP\SEO\Generators;

use Yoast\WP\SEO\Context\Meta_Tags_Context;
use Yoast\WP\SEO\Helpers\Current_Page_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Pagination_Helper;
use Yoast\WP\SEO\Helpers\Post_Type_Helper;
use Yoast\WP\SEO\Helpers\Url_Helper;
use Yoast\WP\SEO\Models\Indexable;
use Yoast\WP\SEO\Repositories\Indexable_Repository;

/**
 * Represents the generator class for the breadcrumbs.
 */
class Breadcrumbs_Generator implements Generator_Interface {

	/**
	 * The indexable repository.
	 *
	 * @var Indexable_Repository
	 */
	private $repository;

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options;

	/**
	 * The current page helper.
	 *
	 * @var Current_Page_Helper
	 */
	private $current_page_helper;

	/**
	 * The post type helper.
	 *
	 * @var Post_Type_Helper
	 */
	private $post_type_helper;

	/**
	 * The URL helper.
	 *
	 * @var Url_Helper
	 */
	private $url_helper;

	/**
	 * The pagination helper.
	 *
	 * @var Pagination_Helper
	 */
	private $pagination_helper;

	/**
	 * Breadcrumbs_Generator constructor.
	 *
	 * @param Indexable_Repository $repository          The repository.
	 * @param Options_Helper       $options             The options helper.
	 * @param Current_Page_Helper  $current_page_helper The current page helper.
	 * @param Post_Type_Helper     $post_type_helper    The post type helper.
	 * @param Url_Helper           $url_helper          The URL helper.
	 * @param Pagination_Helper    $pagination_helper   The pagination helper.
	 */
	public function __construct(
		Indexable_Repository $repository,
		Options_Helper $options,
		Current_Page_Helper $current_page_helper,
		Post_Type_Helper $post_type_helper,
		Url_Helper $url_helper,
		Pagination_Helper $pagination_helper
	) {
		$this->repository          = $repository;
		$this->options             = $options;
		$this->current_page_helper = $current_page_helper;
		$this->post_type_helper    = $post_type_helper;
		$this->url_helper          = $url_helper;
		$this->pagination_helper   = $pagination_helper;
	}

	/**
	 * Generates the breadcrumbs.
	 *
	 * @param Meta_Tags_Context $context The meta tags context.
	 *
	 * @return array<array<int, string>> An array of associative arrays that each have a 'text' and a 'url'.
	 */
	public function generate( Meta_Tags_Context $context ) {
		$static_ancestors = [];
		$breadcrumbs_home = $this->options->get( 'breadcrumbs-home' );
		if ( $breadcrumbs_home !== '' && ! \in_array( $this->current_page_helper->get_page_type(), [ 'Home_Page', 'Static_Home_Page' ], true ) ) {
			$front_page_id = $this->current_page_helper->get_front_page_id();
			if ( $front_page_id === 0 ) {
				$home_page_ancestor = $this->repository->find_for_home_page();
				if ( \is_a( $home_page_ancestor, Indexable::class ) ) {
					$static_ancestors[] = $home_page_ancestor;
				}
			}
			else {
				$static_ancestor = $this->repository->find_by_id_and_type( $front_page_id, 'post' );
				if ( \is_a( $static_ancestor, Indexable::class ) && $static_ancestor->post_status !== 'unindexed' ) {
					$static_ancestors[] = $static_ancestor;
				}
			}
		}
		$page_for_posts = \get_option( 'page_for_posts' );
		if ( $this->should_have_blog_crumb( $page_for_posts, $context ) ) {
			$static_ancestor = $this->repository->find_by_id_and_type( $page_for_posts, 'post' );
			if ( \is_a( $static_ancestor, Indexable::class ) && $static_ancestor->post_status !== 'unindexed' ) {
				$static_ancestors[] = $static_ancestor;
			}
		}
		if (
			$context->indexable->object_type === 'post'
			&& $context->indexable->object_sub_type !== 'post'
			&& $context->indexable->object_sub_type !== 'page'
			&& $this->post_type_helper->has_archive( $context->indexable->object_sub_type )
		) {
			$static_ancestor = $this->repository->find_for_post_type_archive( $context->indexable->object_sub_type );
			if ( \is_a( $static_ancestor, Indexable::class ) ) {
				$static_ancestors[] = $static_ancestor;
			}
		}
		if ( $context->indexable->object_type === 'term' ) {
			$parent = $this->get_taxonomy_post_type_parent( $context->indexable->object_sub_type );
			if ( $parent && $parent !== 'post' && $this->post_type_helper->has_archive( $parent ) ) {
				$static_ancestor = $this->repository->find_for_post_type_archive( $parent );
				if ( \is_a( $static_ancestor, Indexable::class ) ) {
					$static_ancestors[] = $static_ancestor;
				}
			}
		}
		$indexables = [];
		if ( ! \in_array( $this->current_page_helper->get_page_type(), [ 'Home_Page', 'Static_Home_Page' ], true ) ) {
			// Get all ancestors of the indexable and append itself to get all indexables in the full crumb.
			$indexables = $this->repository->get_ancestors( $context->indexable );
		}
		$indexables[] = $context->indexable;

		if ( ! empty( $static_ancestors ) ) {
			\array_unshift( $indexables, ...$static_ancestors );
		}

		$indexables = \apply_filters( 'wpseo_breadcrumb_indexables', $indexables, $context );
		$indexables = \is_array( $indexables ) ? $indexables : [];
		$indexables = \array_filter(
			$indexables,
			static function ( $indexable ) {
				return \is_a( $indexable, Indexable::class );
			}
		);

		$crumbs = \array_map( [ $this, 'get_post_type_crumb' ], $indexables );

		if ( $breadcrumbs_home !== '' ) {
			$crumbs[0]['text'] = $breadcrumbs_home;
		}

		$crumbs = $this->add_paged_crumb( $crumbs, $context->indexable );

		/**
		 * Filter: 'wpseo_breadcrumb_links' - Allow the developer to filter the Yoast SEO breadcrumb links, add to them, change order, etc.
		 *
		 * @param array $crumbs The crumbs array.
		 */
		$filtered_crumbs = \apply_filters( 'wpseo_breadcrumb_links', $crumbs );

		// Basic check to make sure the filtered crumbs are in an array.
		if ( ! \is_array( $filtered_crumbs ) ) {
			\_doing_it_wrong(
				'Filter: \'wpseo_breadcrumb_links\'',
				'The `wpseo_breadcrumb_links` filter should return a multi-dimensional array.',
				'YoastSEO v20.0'
			);
		}
		else {
			$crumbs = $filtered_crumbs;
		}

		$filter_callback = static function ( $link_info, $index ) use ( $crumbs ) {
			/**
			 * Filter: 'wpseo_breadcrumb_single_link_info' - Allow developers to filter the Yoast SEO Breadcrumb link information.
			 *
			 * @param array $link_info The breadcrumb link information.
			 * @param int   $index     The index of the breadcrumb in the list.
			 * @param array $crumbs    The complete list of breadcrumbs.
			 */
			return \apply_filters( 'wpseo_breadcrumb_single_link_info', $link_info, $index, $crumbs );
		};
		return \array_map( $filter_callback, $crumbs, \array_keys( $crumbs ) );
	}

	/**
	 * Returns the modified post crumb.
	 *
	 * @param string[]  $crumb    The crumb.
	 * @param Indexable $ancestor The indexable.
	 *
	 * @return array<int, string> The crumb.
	 */
	private function get_post_crumb( $crumb, $ancestor ) {
		$crumb['id'] = $ancestor->object_id;

		return $crumb;
	}

	/**
	 * Adds the correct ID to the crumb array based on the ancestor provided.
	 *
	 * @param Indexable $ancestor The ancestor indexable.
	 *
	 * @return string[]
	 */
	private function get_post_type_crumb( Indexable $ancestor ) {
		$crumb = [
			'url'  => $ancestor->permalink,
			'text' => $ancestor->breadcrumb_title,
		];

		switch ( $ancestor->object_type ) {
			case 'post':
				$crumb = $this->get_post_crumb( $crumb, $ancestor );
				break;
			case 'post-type-archive':
				$crumb = $this->get_post_type_archive_crumb( $crumb, $ancestor );
				break;
			case 'term':
				$crumb = $this->get_term_crumb( $crumb, $ancestor );
				break;
			case 'system-page':
				$crumb = $this->get_system_page_crumb( $crumb, $ancestor );
				break;
			case 'user':
				$crumb = $this->get_user_crumb( $crumb, $ancestor );
				break;
			case 'date-archive':
				$crumb = $this->get_date_archive_crumb( $crumb );
				break;
			default:
				// Handle unknown object types (optional).
				break;
		}

		return $crumb;
	}

	/**
	 * Returns the modified post type crumb.
	 *
	 * @param string[]  $crumb    The crumb.
	 * @param Indexable $ancestor The indexable.
	 *
	 * @return string[] The crumb.
	 */
	private function get_post_type_archive_crumb( $crumb, $ancestor ) {
		$crumb['ptarchive'] = $ancestor->object_sub_type;

		return $crumb;
	}

	/**
	 * Returns the modified term crumb.
	 *
	 * @param string[]  $crumb    The crumb.
	 * @param Indexable $ancestor The indexable.
	 *
	 * @return array<int, string> The crumb.
	 */
	private function get_term_crumb( $crumb, $ancestor ) {
		$crumb['term_id']  = $ancestor->object_id;
		$crumb['taxonomy'] = $ancestor->object_sub_type;

		return $crumb;
	}

	/**
	 * Returns the modified system page crumb.
	 *
	 * @param string[]  $crumb    The crumb.
	 * @param Indexable $ancestor The indexable.
	 *
	 * @return string[] The crumb.
	 */
	private function get_system_page_crumb( $crumb, $ancestor ) {
		if ( $ancestor->object_sub_type === 'search-result' ) {
			$crumb['text'] = $this->options->get( 'breadcrumbs-searchprefix' ) . ' ' . \esc_html( \get_search_query() );
			$crumb['url']  = \get_search_link();
		}
		elseif ( $ancestor->object_sub_type === '404' ) {
			$crumb['text'] = $this->options->get( 'breadcrumbs-404crumb' );
		}

		return $crumb;
	}

	/**
	 * Returns the modified user crumb.
	 *
	 * @param string[]  $crumb    The crumb.
	 * @param Indexable $ancestor The indexable.
	 *
	 * @return string[] The crumb.
	 */
	private function get_user_crumb( $crumb, $ancestor ) {
		$display_name  = \get_the_author_meta( 'display_name', $ancestor->object_id );
		$crumb['text'] = $this->options->get( 'breadcrumbs-archiveprefix' ) . ' ' . $display_name;

		return $crumb;
	}

	/**
	 * Returns the modified date archive crumb.
	 *
	 * @param string[] $crumb The crumb.
	 *
	 * @return string[] The crumb.
	 */
	protected function get_date_archive_crumb( $crumb ) {
		$home_url = $this->url_helper->home();
		$prefix   = $this->options->get( 'breadcrumbs-archiveprefix' );

		if ( \is_day() ) {
			$day           = \esc_html( \get_the_date() );
			$crumb['url']  = $home_url . \get_the_date( 'Y/m/d' ) . '/';
			$crumb['text'] = $prefix . ' ' . $day;
		}
		elseif ( \is_month() ) {
			$month         = \esc_html( \trim( \single_month_title( ' ', false ) ) );
			$crumb['url']  = $home_url . \get_the_date( 'Y/m' ) . '/';
			$crumb['text'] = $prefix . ' ' . $month;
		}
		elseif ( \is_year() ) {
			$year          = \get_the_date( 'Y' );
			$crumb['url']  = $home_url . $year . '/';
			$crumb['text'] = $prefix . ' ' . $year;
		}

		return $crumb;
	}

	/**
	 * Returns whether or not a blog crumb should be added.
	 *
	 * @param int               $page_for_posts The page for posts ID.
	 * @param Meta_Tags_Context $context        The meta tags context.
	 *
	 * @return bool Whether or not a blog crumb should be added.
	 */
	protected function should_have_blog_crumb( $page_for_posts, $context ) {
		// When there is no page configured as blog page.
		if ( \get_option( 'show_on_front' ) !== 'page' || ! $page_for_posts ) {
			return false;
		}

		if ( $context->indexable->object_type === 'term' ) {
			$parent = $this->get_taxonomy_post_type_parent( $context->indexable->object_sub_type );
			return $parent === 'post';
		}

		if ( $this->options->get( 'breadcrumbs-display-blog-page' ) !== true ) {
			return false;
		}

		// When the current page is the home page, searchpage or isn't a singular post.
		if ( \is_home() || \is_search() || ! \is_singular( 'post' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Returns the post type parent of a given taxonomy.
	 *
	 * @param string $taxonomy The taxonomy.
	 *
	 * @return string|false The parent if it exists, false otherwise.
	 */
	protected function get_taxonomy_post_type_parent( $taxonomy ) {
		$parent = $this->options->get( 'taxonomy-' . $taxonomy . '-ptparent' );

		if ( empty( $parent ) || (string) $parent === '0' ) {
			return false;
		}

		return $parent;
	}

	/**
	 * Adds a crumb for the current page, if we're on an archive page or paginated post.
	 *
	 * @param string[]  $crumbs            The array of breadcrumbs.
	 * @param Indexable $current_indexable The current indexable.
	 *
	 * @return string[] The breadcrumbs.
	 */
	protected function add_paged_crumb( array $crumbs, $current_indexable ) {
		$is_simple_page = $this->current_page_helper->is_simple_page();

		// If we're not on a paged page do nothing.
		if ( ! $is_simple_page && ! $this->current_page_helper->is_paged() ) {
			return $crumbs;
		}

		// If we're not on a paginated post do nothing.
		if ( $is_simple_page && $current_indexable->number_of_pages === null ) {
			return $crumbs;
		}

		$current_page_number = $this->pagination_helper->get_current_page_number();
		if ( $current_page_number <= 1 ) {
			return $crumbs;
		}

		$crumbs[] = [
			'text' => \sprintf(
				/* translators: %s expands to the current page number */
				\__( 'Page %s', 'wordpress-seo' ),
				$current_page_number
			),
		];

		return $crumbs;
	}
}
