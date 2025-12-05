<?php

namespace Elementor\Modules\SiteNavigation\Data\Endpoints;

use Elementor\Core\Base\Document;
use Elementor\Core\Kits\Documents\Kit;
use Elementor\Data\V2\Base\Endpoint;
use Elementor\Plugin;
use Elementor\TemplateLibrary\Source_Local;
use Elementor\Utils;
use WP_REST_Server;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Recent_Posts extends Endpoint {

	public function register_items_route( $methods = WP_REST_Server::READABLE, $args = [] ) {
		$args = [
			'posts_per_page' => [
				'description' => 'Number of posts to return',
				'type' => 'integer',
				'required' => true,
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			],
			'post_type' => [
				'description' => 'Post types to retrieve',
				'type' => 'array',
				'required' => false,
				'default' => [ 'page', 'post', Source_Local::CPT ],
				'sanitize_callback' => 'rest_sanitize_array',
				'validate_callback' => 'rest_validate_request_arg',
			],
			'post__not_in' => [
				'description' => 'Post id`s to exclude',
				'type' => 'array',
				'required' => [],
				'sanitize_callback' => 'wp_parse_id_list',
				'validate_callback' => 'rest_validate_request_arg',
			],
		];

		parent::register_items_route( $methods, $args );
	}

	public function get_name() {
		return 'recent-posts';
	}

	public function get_format() {
		return 'site-navigation/recent-posts';
	}

	public function get_items( $request ) {
		$args = [
			'posts_per_page' => $request->get_param( 'posts_per_page' ),
			'post_type' => $request->get_param( 'post_type' ),
			'fields' => 'ids',
			'meta_query' => [
				[
					'key' => Document::TYPE_META_KEY,
					'value' => Kit::get_type(), // Exclude kits.
					'compare' => '!=',
				],
			],
		];

		$exclude = $request->get_param( 'post__not_in' );

		if ( ! empty( $exclude ) ) {
			$args['post__not_in'] = $exclude;
		}

		$recently_edited_query = Utils::get_recently_edited_posts_query( $args );

		$recent = [];

		foreach ( $recently_edited_query->posts as $id ) {
			$document = Plugin::$instance->documents->get( $id );

			$recent[] = [
				'id' => $id,
				'title' => get_the_title( $id ),
				'edit_url' => $document->get_edit_url(),
				'date_modified' => get_post_timestamp( $id, 'modified' ),
				'type' => [
					'post_type' => get_post_type( $id ),
					'doc_type' => $document->get_name(),
					'label' => $document->get_title(),
				],
				'user_can' => [
					'edit' => current_user_can( 'edit_post', $id ),
				],
			];
		}

		return $recent;
	}
}
