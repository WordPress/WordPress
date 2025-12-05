<?php

namespace Elementor\Modules\SiteNavigation\Data\Endpoints;

use Elementor\Data\V2\Base\Endpoint;
use Elementor\Plugin;
use Elementor\User;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Add_New_Post extends Endpoint {

	protected function register() {
		$args = [
			'post_type' => [
				'description' => 'Post type to create',
				'type' => 'string',
				'required' => false,
				'default' => 'post',
				'sanitize_callback' => function ( $value ) {
					return sanitize_text_field( $value );
				},
				'validate_callback' => 'rest_validate_request_arg',
			],
		];

		$this->register_items_route( \WP_REST_Server::CREATABLE, $args );
	}

	public function get_name() {
		return 'add-new-post';
	}

	public function get_format() {
		return 'site-navigation/add-new-post';
	}

	public function create_items( $request ) {
		$post_type = $request->get_param( 'post_type' );

		if ( ! $this->validate_post_type( $post_type ) ) {
			return new \WP_Error( 400, sprintf( 'Post type %s does not exist.', $post_type ), [ 'status' => 400 ] );
		}

		if ( ! User::is_current_user_can_edit_post_type( $post_type ) ) {
			return new \WP_Error( 401, sprintf( 'User dont have capability to create page of type - %s.', $post_type ), [ 'status' => 401 ] );
		}

		// Temporary solution for the fact that documents creation not using the actual registered post types.
		$post_type = $this->map_post_type( $post_type );

		$document = Plugin::$instance->documents->create( $post_type );

		if ( is_wp_error( $document ) ) {
			return new \WP_Error( 500, sprintf( 'Error while creating %s.', $post_type ) );
		}

		return [
			'id' => $document->get_main_id(),
			'edit_url' => $document->get_edit_url(),
		];
	}

	private function validate_post_type( $post_type ): bool {
		$post_types = get_post_types();

		return in_array( $post_type, $post_types );
	}

	/**
	 * Map post type to Elementor document type.
	 *
	 * @param $post_type
	 *
	 * @return string
	 */
	private function map_post_type( $post_type ): string {
		$post_type_map = [
			'page' => 'wp-page',
			'post' => 'wp-post',
		];

		return $post_type_map[ $post_type ] ?? $post_type;
	}
}
