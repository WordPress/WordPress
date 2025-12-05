<?php

namespace Yoast\WP\SEO\Builders;

use WPSEO_Image_Utils;
use Yoast\WP\SEO\Helpers\Image_Helper;
use Yoast\WP\SEO\Helpers\Indexable_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Post_Helper;
use Yoast\WP\SEO\Helpers\Url_Helper;
use Yoast\WP\SEO\Images\Application\Image_Content_Extractor;
use Yoast\WP\SEO\Models\Indexable;
use Yoast\WP\SEO\Models\SEO_Links;
use Yoast\WP\SEO\Repositories\Indexable_Repository;
use Yoast\WP\SEO\Repositories\SEO_Links_Repository;

/**
 * Indexable link builder.
 */
class Indexable_Link_Builder {

	/**
	 * The SEO links repository.
	 *
	 * @var SEO_Links_Repository
	 */
	protected $seo_links_repository;

	/**
	 * The url helper.
	 *
	 * @var Url_Helper
	 */
	protected $url_helper;

	/**
	 * The image helper.
	 *
	 * @var Image_Helper
	 */
	protected $image_helper;

	/**
	 * The indexable helper.
	 *
	 * @var Indexable_Helper
	 */
	protected $indexable_helper;

	/**
	 * The post helper.
	 *
	 * @var Post_Helper
	 */
	protected $post_helper;

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	protected $options_helper;

	/**
	 * The indexable repository.
	 *
	 * @var Indexable_Repository
	 */
	protected $indexable_repository;

	/**
	 * Class that finds all images in a content string and extracts them.
	 *
	 * @var Image_Content_Extractor
	 */
	private $image_content_extractor;

	/**
	 * Indexable_Link_Builder constructor.
	 *
	 * @param SEO_Links_Repository $seo_links_repository The SEO links repository.
	 * @param Url_Helper           $url_helper           The URL helper.
	 * @param Post_Helper          $post_helper          The post helper.
	 * @param Options_Helper       $options_helper       The options helper.
	 * @param Indexable_Helper     $indexable_helper     The indexable helper.
	 */
	public function __construct(
		SEO_Links_Repository $seo_links_repository,
		Url_Helper $url_helper,
		Post_Helper $post_helper,
		Options_Helper $options_helper,
		Indexable_Helper $indexable_helper,
		Image_Content_Extractor $image_content_extractor
	) {
		$this->seo_links_repository    = $seo_links_repository;
		$this->url_helper              = $url_helper;
		$this->post_helper             = $post_helper;
		$this->options_helper          = $options_helper;
		$this->indexable_helper        = $indexable_helper;
		$this->image_content_extractor = $image_content_extractor;
	}

	/**
	 * Sets the indexable repository.
	 *
	 * @required
	 *
	 * @param Indexable_Repository $indexable_repository The indexable repository.
	 * @param Image_Helper         $image_helper         The image helper.
	 *
	 * @return void
	 */
	public function set_dependencies( Indexable_Repository $indexable_repository, Image_Helper $image_helper ) {
		$this->indexable_repository = $indexable_repository;
		$this->image_helper         = $image_helper;
	}

	/**
	 * Builds the links for a post.
	 *
	 * @param Indexable $indexable The indexable.
	 * @param string    $content   The content. Expected to be unfiltered.
	 *
	 * @return SEO_Links[] The created SEO links.
	 */
	public function build( $indexable, $content ) {
		if ( ! $this->indexable_helper->should_index_indexable( $indexable ) ) {
			return [];
		}

		global $post;
		if ( $indexable->object_type === 'post' ) {
			$post_backup = $post;
			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- To setup the post we need to do this explicitly.
			$post = $this->post_helper->get_post( $indexable->object_id );
			\setup_postdata( $post );
			$content = \apply_filters( 'the_content', $content );
			\wp_reset_postdata();
			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- To setup the post we need to do this explicitly.
			$post = $post_backup;
		}

		$content = \str_replace( ']]>', ']]&gt;', $content );
		$links   = $this->gather_links( $content );
		$images  = $this->image_content_extractor->gather_images( $content );

		if ( empty( $links ) && empty( $images ) ) {
			$indexable->link_count = 0;
			$this->update_related_indexables( $indexable, [] );

			return [];
		}

		if ( ! empty( $images ) && ( $indexable->open_graph_image_source === 'first-content-image' || $indexable->twitter_image_source === 'first-content-image' ) ) {
			$this->update_first_content_image( $indexable, $images );
		}

		$links = $this->create_links( $indexable, $links, $images );

		$this->update_related_indexables( $indexable, $links );

		$indexable->link_count = $this->get_internal_link_count( $links );

		return $links;
	}

	/**
	 * Deletes all SEO links for an indexable.
	 *
	 * @param Indexable $indexable The indexable.
	 *
	 * @return void
	 */
	public function delete( $indexable ) {
		$links = ( $this->seo_links_repository->find_all_by_indexable_id( $indexable->id ) );
		$this->seo_links_repository->delete_all_by_indexable_id( $indexable->id );

		$linked_indexable_ids = [];
		foreach ( $links as $link ) {
			if ( $link->target_indexable_id ) {
				$linked_indexable_ids[] = $link->target_indexable_id;
			}
		}

		$this->update_incoming_links_for_related_indexables( $linked_indexable_ids );
	}

	/**
	 * Fixes existing SEO links that are supposed to have a target indexable but don't, because of prior indexable
	 * cleanup.
	 *
	 * @param Indexable $indexable The indexable to be the target of SEO Links.
	 *
	 * @return void
	 */
	public function patch_seo_links( Indexable $indexable ) {
		if ( ! empty( $indexable->id ) && ! empty( $indexable->object_id ) ) {
			$links = $this->seo_links_repository->find_all_by_target_post_id( $indexable->object_id );

			$updated_indexable = false;
			foreach ( $links as $link ) {
				if ( \is_a( $link, SEO_Links::class ) && empty( $link->target_indexable_id ) ) {
					// Since that post ID exists in an SEO link but has no target_indexable_id, it's probably because of prior indexable cleanup.
					$this->seo_links_repository->update_target_indexable_id( $link->id, $indexable->id );
					$updated_indexable = true;
				}
			}

			if ( $updated_indexable ) {
				$updated_indexable_id = [ $indexable->id ];
				$this->update_incoming_links_for_related_indexables( $updated_indexable_id );
			}
		}
	}

	/**
	 * Gathers all links from content.
	 *
	 * @param string $content The content.
	 *
	 * @return string[] An array of urls.
	 */
	protected function gather_links( $content ) {
		if ( \strpos( $content, 'href' ) === false ) {
			// Nothing to do.
			return [];
		}

		$links  = [];
		$regexp = '<a\s[^>]*href=("??)([^" >]*?)\1[^>]*>';
		// Used modifiers iU to match case insensitive and make greedy quantifiers lazy.
		if ( \preg_match_all( "/$regexp/iU", $content, $matches, \PREG_SET_ORDER ) ) {
			foreach ( $matches as $match ) {
				$links[] = \trim( $match[2], "'" );
			}
		}

		return $links;
	}

	/**
	 * Creates link models from lists of URLs and image sources.
	 *
	 * @param Indexable $indexable The indexable.
	 * @param string[]  $links     The link URLs.
	 * @param int[]     $images    The image sources.
	 *
	 * @return SEO_Links[] The link models.
	 */
	protected function create_links( $indexable, $links, $images ) {
		$home_url    = \wp_parse_url( \home_url() );
		$current_url = \wp_parse_url( $indexable->permalink );
		$links       = \array_map(
			function ( $link ) use ( $home_url, $indexable ) {
				return $this->create_internal_link( $link, $home_url, $indexable );
			},
			$links
		);
		// Filter out links to the same page with a fragment or query.
		$links = \array_filter(
			$links,
			function ( $link ) use ( $current_url ) {
				return $this->filter_link( $link, $current_url );
			}
		);

		$image_links = [];
		foreach ( $images as $image_url => $image_id ) {
			$image_links[] = $this->create_internal_link( $image_url, $home_url, $indexable, true, $image_id );
		}

		return \array_merge( $links, $image_links );
	}

	/**
	 * Get the post ID based on the link's type and its target's permalink.
	 *
	 * @param string $type      The type of link (either SEO_Links::TYPE_INTERNAL or SEO_Links::TYPE_INTERNAL_IMAGE).
	 * @param string $permalink The permalink of the link's target.
	 *
	 * @return int The post ID.
	 */
	protected function get_post_id( $type, $permalink ) {
		if ( $type === SEO_Links::TYPE_INTERNAL ) {
			return \url_to_postid( $permalink );
		}

		return $this->image_helper->get_attachment_by_url( $permalink );
	}

	/**
	 * Creates an internal link.
	 *
	 * @param string    $url       The url of the link.
	 * @param array     $home_url  The home url, as parsed by wp_parse_url.
	 * @param Indexable $indexable The indexable of the post containing the link.
	 * @param bool      $is_image  Whether or not the link is an image.
	 * @param int       $image_id  The ID of the internal image.
	 *
	 * @return SEO_Links The created link.
	 */
	protected function create_internal_link( $url, $home_url, $indexable, $is_image = false, $image_id = 0 ) {
		$parsed_url = \wp_parse_url( $url );
		$link_type  = $this->url_helper->get_link_type( $parsed_url, $home_url, $is_image );

		/**
		 * ORM representing a link in the SEO Links table.
		 *
		 * @var SEO_Links $model
		 */
		$model = $this->seo_links_repository->query()->create(
			[
				'url'          => $url,
				'type'         => $link_type,
				'indexable_id' => $indexable->id,
				'post_id'      => $indexable->object_id,
			]
		);

		$model->parsed_url = $parsed_url;

		if ( $model->type === SEO_Links::TYPE_INTERNAL ) {
			$permalink = $this->build_permalink( $url, $home_url );

			return $this->enhance_link_from_indexable( $model, $permalink );
		}

		if ( $model->type === SEO_Links::TYPE_INTERNAL_IMAGE ) {
			$permalink = $this->build_permalink( $url, $home_url );

			/** The `wpseo_force_creating_and_using_attachment_indexables` filter is documented in indexable-link-builder.php */
			if ( ! $this->options_helper->get( 'disable-attachment' ) || \apply_filters( 'wpseo_force_creating_and_using_attachment_indexables', false ) ) {
				$model = $this->enhance_link_from_indexable( $model, $permalink );
			}
			else {
				$target_post_id = ( $image_id !== 0 ) ? $image_id : WPSEO_Image_Utils::get_attachment_by_url( $permalink );

				if ( ! empty( $target_post_id ) ) {
					$model->target_post_id = $target_post_id;
				}
			}

			if ( $model->target_post_id ) {
				$file = \get_attached_file( $model->target_post_id );

				if ( $file ) {
					if ( \file_exists( $file ) ) {
						$model->size = \filesize( $file );
					}
					else {
						$model->size = null;
					}

					[ , $width, $height ] = \wp_get_attachment_image_src( $model->target_post_id, 'full' );
					$model->width         = $width;
					$model->height        = $height;
				}
				else {
					$model->width  = 0;
					$model->height = 0;
					$model->size   = 0;
				}
			}
		}

		return $model;
	}

	/**
	 * Enhances the link model with information from its indexable.
	 *
	 * @param SEO_Links $model     The link's model.
	 * @param string    $permalink The link's permalink.
	 *
	 * @return SEO_Links The enhanced link model.
	 */
	protected function enhance_link_from_indexable( $model, $permalink ) {
		$target = $this->indexable_repository->find_by_permalink( $permalink );

		if ( ! $target ) {
			// If target indexable cannot be found, create one based on the post's post ID.
			$post_id = $this->get_post_id( $model->type, $permalink );
			if ( $post_id && $post_id !== 0 ) {
				$target = $this->indexable_repository->find_by_id_and_type( $post_id, 'post' );
			}
		}

		if ( ! $target ) {
			return $model;
		}

		$model->target_indexable_id = $target->id;
		if ( $target->object_type === 'post' ) {
			$model->target_post_id = $target->object_id;
		}

		if ( $model->target_indexable_id ) {
			$model->language = $target->language;
			$model->region   = $target->region;
		}

		return $model;
	}

	/**
	 * Builds the link's permalink.
	 *
	 * @param string $url      The url of the link.
	 * @param array  $home_url The home url, as parsed by wp_parse_url.
	 *
	 * @return string The link's permalink.
	 */
	protected function build_permalink( $url, $home_url ) {
		$permalink = $this->get_permalink( $url, $home_url );

		if ( $this->url_helper->is_relative( $permalink ) ) {
			// Make sure we're checking against the absolute URL, and add a trailing slash if the site has a trailing slash in its permalink settings.
			$permalink = $this->url_helper->ensure_absolute_url( \user_trailingslashit( $permalink ) );
		}

		return $permalink;
	}

	/**
	 * Filters out links that point to the same page with a fragment or query.
	 *
	 * @param SEO_Links $link        The link.
	 * @param array     $current_url The url of the page the link is on, as parsed by wp_parse_url.
	 *
	 * @return bool Whether or not the link should be filtered.
	 */
	protected function filter_link( SEO_Links $link, $current_url ) {
		$url = $link->parsed_url;

		// Always keep external links.
		if ( $link->type === SEO_Links::TYPE_EXTERNAL ) {
			return true;
		}

		// Always keep links with an empty path or pointing to other pages.
		if ( isset( $url['path'] ) ) {
			return empty( $url['path'] ) || $url['path'] !== $current_url['path'];
		}

		// Only keep links to the current page without a fragment or query.
		return ( ! isset( $url['fragment'] ) && ! isset( $url['query'] ) );
	}

	/**
	 * Updates the link counts for related indexables.
	 *
	 * @param Indexable   $indexable The indexable.
	 * @param SEO_Links[] $links     The link models.
	 *
	 * @return void
	 */
	protected function update_related_indexables( $indexable, $links ) {
		// Old links were only stored by post id, so remove all old seo links for this post that have no indexable id.
		// This can be removed if we ever fully clear all seo links.
		if ( $indexable->object_type === 'post' ) {
			$this->seo_links_repository->delete_all_by_post_id_where_indexable_id_null( $indexable->object_id );
		}

		$updated_indexable_ids = [];
		$old_links             = $this->seo_links_repository->find_all_by_indexable_id( $indexable->id );

		$links_to_remove = $this->links_diff( $old_links, $links );
		$links_to_add    = $this->links_diff( $links, $old_links );

		if ( ! empty( $links_to_remove ) ) {
			$this->seo_links_repository->delete_many_by_id( \wp_list_pluck( $links_to_remove, 'id' ) );
		}

		if ( ! empty( $links_to_add ) ) {
			$this->seo_links_repository->insert_many( $links_to_add );
		}

		foreach ( $links_to_add as $link ) {
			if ( $link->target_indexable_id ) {
				$updated_indexable_ids[] = $link->target_indexable_id;
			}
		}
		foreach ( $links_to_remove as $link ) {
			if ( $link->target_indexable_id ) {
				$updated_indexable_ids[] = $link->target_indexable_id;
			}
		}

		$this->update_incoming_links_for_related_indexables( $updated_indexable_ids );
	}

	/**
	 * Creates a diff between two arrays of SEO links, based on urls.
	 *
	 * @param SEO_Links[] $links_a The array to compare.
	 * @param SEO_Links[] $links_b The array to compare against.
	 *
	 * @return SEO_Links[] Links that are in $links_a, but not in $links_b.
	 */
	protected function links_diff( $links_a, $links_b ) {
		return \array_udiff(
			$links_a,
			$links_b,
			static function ( SEO_Links $link_a, SEO_Links $link_b ) {
				return \strcmp( $link_a->url, $link_b->url );
			}
		);
	}

	/**
	 * Returns the number of internal links in an array of link models.
	 *
	 * @param SEO_Links[] $links The link models.
	 *
	 * @return int The number of internal links.
	 */
	protected function get_internal_link_count( $links ) {
		$internal_link_count = 0;

		foreach ( $links as $link ) {
			if ( $link->type === SEO_Links::TYPE_INTERNAL ) {
				++$internal_link_count;
			}
		}

		return $internal_link_count;
	}

	/**
	 * Returns a cleaned permalink for a given link.
	 *
	 * @param string $link     The raw URL.
	 * @param array  $home_url The home URL, as parsed by wp_parse_url.
	 *
	 * @return string The cleaned permalink.
	 */
	protected function get_permalink( $link, $home_url ) {
		// Get rid of the #anchor.
		$url_split = \explode( '#', $link );
		$link      = $url_split[0];

		// Get rid of URL ?query=string.
		$url_split = \explode( '?', $link );
		$link      = $url_split[0];

		// Set the correct URL scheme.
		$link = \set_url_scheme( $link, $home_url['scheme'] );

		// Add 'www.' if it is absent and should be there.
		if ( \strpos( $home_url['host'], 'www.' ) === 0 && \strpos( $link, '://www.' ) === false ) {
			$link = \str_replace( '://', '://www.', $link );
		}

		// Strip 'www.' if it is present and shouldn't be.
		if ( \strpos( $home_url['host'], 'www.' ) !== 0 ) {
			$link = \str_replace( '://www.', '://', $link );
		}

		return $link;
	}

	/**
	 * Updates incoming link counts for related indexables.
	 *
	 * @param int[] $related_indexable_ids The IDs of all related indexables.
	 *
	 * @return void
	 */
	protected function update_incoming_links_for_related_indexables( $related_indexable_ids ) {
		if ( empty( $related_indexable_ids ) ) {
			return;
		}

		$counts = $this->seo_links_repository->get_incoming_link_counts_for_indexable_ids( $related_indexable_ids );

		/**
		 * Fires to signal that incoming link counts for related indexables were updated.
		 *
		 * @param int[] $related_indexable_ids The related indexable Ids to this link change.
		 *
		 * @internal
		 */
		\do_action( 'wpseo_related_indexables_incoming_links_updated', $related_indexable_ids );

		foreach ( $counts as $count ) {
			$this->indexable_repository->update_incoming_link_count( $count['target_indexable_id'], $count['incoming'] );
		}
	}

	/**
	 * Updates the image ids when the indexable images are marked as first content image.
	 *
	 * @param Indexable         $indexable The indexable to change.
	 * @param array<string|int> $images    The image array.
	 *
	 * @return void
	 */
	public function update_first_content_image( Indexable $indexable, array $images ): void {
		$current_open_graph_image = $indexable->open_graph_image;
		$current_twitter_image    = $indexable->twitter_image;

		$first_content_image_url = \key( $images );
		$first_content_image_id  = \current( $images );

		if ( $indexable->open_graph_image_source === 'first-content-image' && $current_open_graph_image === $first_content_image_url && ! empty( $first_content_image_id ) ) {
			$indexable->open_graph_image_id = $first_content_image_id;
		}
		if ( $indexable->twitter_image_source === 'first-content-image' && $current_twitter_image === $first_content_image_url && ! empty( $first_content_image_id ) ) {
			$indexable->twitter_image_id = $first_content_image_id;
		}
	}
}
