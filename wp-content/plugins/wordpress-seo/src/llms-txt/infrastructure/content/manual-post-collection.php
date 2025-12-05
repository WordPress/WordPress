<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Llms_Txt\Infrastructure\Content;

use WP_Post;
use Yoast\WP\SEO\Helpers\Indexable_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Llms_Txt\Domain\Content\Post_Collection_Interface;
use Yoast\WP\SEO\Llms_Txt\Domain\Content_Types\Content_Type_Entry;
use Yoast\WP\SEO\Repositories\Indexable_Repository;
use Yoast\WP\SEO\Surfaces\Meta_Surface;

/**
 * The class that handles the manual post collection. Based on either indexables or WP_Query.
 */
class Manual_Post_Collection implements Post_Collection_Interface {

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * The indexable helper.
	 *
	 * @var Indexable_Helper
	 */
	private $indexable_helper;

	/**
	 * The indexable repository.
	 *
	 * @var Indexable_Repository
	 */
	private $indexable_repository;

	/**
	 * The meta surface.
	 *
	 * @var Meta_Surface
	 */
	private $meta;

	/**
	 * Constructor.
	 *
	 * @param Options_Helper       $options_helper       The options helper.
	 * @param Indexable_Helper     $indexable_helper     The indexable helper.
	 * @param Indexable_Repository $indexable_repository The indexable repository.
	 * @param Meta_Surface         $meta                 The meta surface.
	 */
	public function __construct(
		Options_Helper $options_helper,
		Indexable_Helper $indexable_helper,
		Indexable_Repository $indexable_repository,
		Meta_Surface $meta
	) {
		$this->options_helper       = $options_helper;
		$this->indexable_helper     = $indexable_helper;
		$this->indexable_repository = $indexable_repository;
		$this->meta                 = $meta;
	}

	/**
	 * The post method to get all relevant content type entries
	 *
	 * @return array<int, array<Content_Type_Entry>> The posts that are relevant for the LLMs.txt.
	 */
	public function get_posts(): array {
		$posts = [];
		$pages = [
			'about_us_page',
			'contact_page',
			'terms_page',
			'privacy_policy_page',
			'shop_page',
		];

		foreach ( $pages as $page ) {
			$page_id = $this->options_helper->get( $page );
			if ( ! empty( $page_id ) ) {
				$post = $this->get_content_type_entry( $page_id );

				if ( $post !== null ) {
					$posts[] = $post;
				}
				else {
					$this->options_helper->set( $page, 0 );
				}
			}
		}
		$other_pages    = $this->options_helper->get( 'other_included_pages' );
		$filtered_pages = [];
		if ( ! empty( $other_pages ) ) {
			foreach ( $other_pages as $page_id ) {
				$post = $this->get_content_type_entry( $page_id );

				if ( $post !== null ) {
					$posts[]          = $post;
					$filtered_pages[] = $page_id;
				}
			}

			if ( \count( $filtered_pages ) !== \count( $other_pages ) ) {
				$this->options_helper->set( 'other_included_pages', $filtered_pages );
			}
		}

		return $posts;
	}

	/**
	 * Gets the content entries.
	 *
	 * @param int $page_id The id of the page.
	 *
	 * @return Content_Type_Entry The content type entry.
	 */
	public function get_content_type_entry( int $page_id ): ?Content_Type_Entry {
		if ( $this->indexable_helper->should_index_indexables() ) {
			$post = $this->get_content_type_entry_for_indexable( $page_id );
		}
		else {
			$post = $this->get_content_type_entry_wp_query( $page_id );
		}

		return $post;
	}

	/**
	 * Gets the content entries with WP query.
	 *
	 * @param int $page_id The id of the page.
	 *
	 * @return Content_Type_Entry The content type entry.
	 */
	public function get_content_type_entry_wp_query( int $page_id ): ?Content_Type_Entry {
		$page = \get_post( $page_id );

		if ( $page !== null && $page->post_password === '' && $page->post_status === 'publish' ) {

			return Content_Type_Entry::from_post( $page, \get_permalink( $page->ID ) );
		}

		return null;
	}

	/**
	 * Gets the content entries with indexables.
	 *
	 * @param int $page_id The id of the page.
	 *
	 * @return Content_Type_Entry The content type entry.
	 */
	public function get_content_type_entry_for_indexable( int $page_id ): ?Content_Type_Entry {
		$indexable = $this->indexable_repository->find_by_id_and_type( $page_id, 'post' );
		if ( $indexable && ( $indexable->is_public === null || $indexable->is_public ) ) {
			$indexable_meta = $this->meta->for_indexable( $indexable );
			if ( $indexable_meta->post instanceof WP_Post ) {
				return Content_Type_Entry::from_meta( $indexable_meta );
			}
		}

		return null;
	}
}
