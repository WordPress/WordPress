<?php

namespace Yoast\WP\SEO\Generators\Schema;

use WP_Post;
use Yoast\WP\SEO\Config\Schema_IDs;

/**
 * Returns schema WebPage data.
 */
class WebPage extends Abstract_Schema_Piece {

	/**
	 * Determines whether or not a piece should be added to the graph.
	 *
	 * @return bool
	 */
	public function is_needed() {
		if ( $this->context->indexable->object_type === 'unknown' ) {
			return false;
		}
		return ! ( $this->context->indexable->object_type === 'system-page' && $this->context->indexable->object_sub_type === '404' );
	}

	/**
	 * Returns WebPage schema data.
	 *
	 * @return array<string|array<string>> WebPage schema data.
	 */
	public function generate() {
		$data = [
			'@type'      => $this->context->schema_page_type,
			'@id'        => $this->context->main_schema_id,
			'url'        => $this->context->canonical,
			'name'       => $this->helpers->schema->html->smart_strip_tags( $this->context->title ),
			'isPartOf'   => [
				'@id' => $this->context->site_url . Schema_IDs::WEBSITE_HASH,
			],
		];

		if ( empty( $this->context->canonical ) && \is_search() ) {
			$data['url'] = $this->build_search_url();
		}

		if ( $this->helpers->current_page->is_front_page() ) {
			if ( $this->context->site_represents_reference ) {
				$data['about'] = $this->context->site_represents_reference;
			}
		}

		$data = $this->add_image( $data );

		if ( $this->context->indexable->object_type === 'post' ) {
			$data['datePublished'] = $this->helpers->date->format( $this->context->post->post_date_gmt );

			if ( \strtotime( $this->context->post->post_modified_gmt ) > \strtotime( $this->context->post->post_date_gmt ) ) {
				$data['dateModified'] = $this->helpers->date->format( $this->context->post->post_modified_gmt );
			}

			if ( $this->context->indexable->object_sub_type === 'post' ) {
				$data = $this->add_author( $data, $this->context->post );
			}
		}

		if ( ! empty( $this->context->description ) ) {
			$data['description'] = $this->helpers->schema->html->smart_strip_tags( $this->context->description );
		}

		if ( $this->add_breadcrumbs() ) {
			$data['breadcrumb'] = [
				'@id' => $this->context->canonical . Schema_IDs::BREADCRUMB_HASH,
			];
		}

		if ( ! empty( $this->context->main_entity_of_page ) ) {
			$data['mainEntity'] = $this->context->main_entity_of_page;
		}

		$data = $this->helpers->schema->language->add_piece_language( $data );
		$data = $this->add_potential_action( $data );

		return $data;
	}

	/**
	 * Adds an author property to the $data if the WebPage is not represented.
	 *
	 * @param array<string|array<string>> $data The WebPage schema.
	 * @param WP_Post                     $post The post the context is representing.
	 *
	 * @return array<string|array<string>> The WebPage schema.
	 */
	public function add_author( $data, $post ) {
		if ( $this->context->site_represents === false ) {
			$data['author'] = [ '@id' => $this->helpers->schema->id->get_user_schema_id( $post->post_author, $this->context ) ];
		}

		return $data;
	}

	/**
	 * If we have an image, make it the primary image of the page.
	 *
	 * @param array<string|array<string>> $data WebPage schema data.
	 *
	 * @return array<string|array<string>>
	 */
	public function add_image( $data ) {
		if ( $this->context->has_image ) {
			$data['primaryImageOfPage'] = [ '@id' => $this->context->canonical . Schema_IDs::PRIMARY_IMAGE_HASH ];
			$data['image']              = [ '@id' => $this->context->canonical . Schema_IDs::PRIMARY_IMAGE_HASH ];
			$data['thumbnailUrl']       = $this->context->main_image_url;
		}
		return $data;
	}

	/**
	 * Determine if we should add a breadcrumb attribute.
	 *
	 * @return bool
	 */
	private function add_breadcrumbs() {
		if ( $this->context->indexable->object_type === 'system-page' && $this->context->indexable->object_sub_type === '404' ) {
			return false;
		}

		return true;
	}

	/**
	 * Adds the potential action property to the WebPage Schema piece.
	 *
	 * @param array<string|array<string>> $data The WebPage data.
	 *
	 * @return array<string|array<string>> The WebPage data with the potential action added.
	 */
	private function add_potential_action( $data ) {
		$url = $this->context->canonical;
		if ( $data['@type'] === 'CollectionPage' || ( \is_array( $data['@type'] ) && \in_array( 'CollectionPage', $data['@type'], true ) ) ) {
			return $data;
		}

		/**
		 * Filter: 'wpseo_schema_webpage_potential_action_target' - Allows filtering of the schema WebPage potentialAction target.
		 *
		 * @param array<string> $targets The URLs for the WebPage potentialAction target.
		 */
		$targets = \apply_filters( 'wpseo_schema_webpage_potential_action_target', [ $url ] );

		$data['potentialAction'][] = [
			'@type'  => 'ReadAction',
			'target' => $targets,
		];

		return $data;
	}

	/**
	 * Creates the search URL for use when if there is no canonical.
	 *
	 * @return string Search URL.
	 */
	private function build_search_url() {
		return $this->context->site_url . '?s=' . \rawurlencode( \get_search_query() );
	}
}
