<?php

namespace Elementor\Core\Common\Modules\Finder\Categories;

use Elementor\Core\Base\Document;
use Elementor\Core\Common\Modules\Finder\Base_Category;
use Elementor\Plugin;
use Elementor\TemplateLibrary\Source_Local;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Edit Category
 *
 * Provides items related to editing of posts/pages/templates etc.
 */
class Edit extends Base_Category {

	/**
	 * Get title.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Edit', 'elementor' );
	}

	public function get_id() {
		return 'edit';
	}

	/**
	 * Is dynamic.
	 *
	 * Determine if the category is dynamic.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @return bool
	 */
	public function is_dynamic() {
		return true;
	}

	/**
	 * Get category items.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	public function get_category_items( array $options = [] ) {
		$post_types = get_post_types( [
			'exclude_from_search' => false,
		] );

		$post_types[] = Source_Local::CPT;

		$document_types = Plugin::$instance->documents->get_document_types( [
			'is_editable' => true,
			'show_in_finder' => true,
		] );

		$recently_edited_query_args = [
			'no_found_rows' => true,
			'post_type' => $post_types,
			'post_status' => [ 'publish', 'draft', 'private', 'pending', 'future' ],
			'posts_per_page' => '10',
			'meta_query' => [
				[
					'key' => '_elementor_edit_mode',
					'value' => 'builder',
				],
				[
					'relation' => 'or',
					[
						'key' => Document::TYPE_META_KEY,
						'compare' => 'NOT EXISTS',
					],
					[
						'key' => Document::TYPE_META_KEY,
						'value' => array_keys( $document_types ),
					],
				],
			],
			'orderby' => 'modified',
			's' => $options['filter'],
		];

		$recently_edited_query = new \WP_Query( $recently_edited_query_args );

		$items = [];

		/** @var \WP_Post $post */
		foreach ( $recently_edited_query->posts as $post ) {
			$document = Plugin::$instance->documents->get( $post->ID );

			if ( ! $document ) {
				continue;
			}

			$is_template = Source_Local::CPT === $post->post_type;

			$description = $document->get_title();

			$icon = 'document-file';

			if ( $is_template ) {
				$description = esc_html__( 'Template', 'elementor' ) . ' / ' . $description;

				$icon = 'post-title';
			}

			$items[] = [
				'icon' => $icon,
				'title' => esc_html( $post->post_title ),
				'description' => $description,
				'url' => $document->get_edit_url(),
				'actions' => [
					[
						'name' => 'view',
						'url' => $document->get_permalink(),
						'icon' => 'preview-medium',
					],
				],
			];
		}

		return $items;
	}
}
