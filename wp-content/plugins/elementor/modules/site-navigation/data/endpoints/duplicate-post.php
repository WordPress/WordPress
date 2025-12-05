<?php

namespace Elementor\Modules\SiteNavigation\Data\Endpoints;

use Elementor\Data\V2\Base\Endpoint;
use Elementor\Plugin;
use Elementor\User;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Duplicate_Post extends Endpoint {

	protected function register() {
		$args = [
			'post_id' => [
				'description' => 'Post id to duplicate',
				'type' => 'integer',
				'required' => true,
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			],
			'title' => [
				'description' => 'Post title',
				'type' => 'string',
				'required' => false,
				'sanitize_callback' => function ( $value ) {
					return sanitize_text_field( $value );
				},
				'validate_callback' => 'rest_validate_request_arg',
			],
		];

		$this->register_items_route( \WP_REST_Server::CREATABLE, $args );
	}

	public function get_name() {
		return 'duplicate-post';
	}

	public function get_format() {
		return 'site-navigation/duplicate-post';
	}

	public function create_items( $request ) {
		$post_id = $request->get_param( 'post_id' );
		$post_title = $request->get_param( 'title' );

		$post = get_post( $post_id );

		if ( ! User::is_current_user_can_edit_post_type( $post->post_type ) ) {
			return new \WP_Error( 401, sprintf( 'User dont have capability to create page of type - %s.', $post->post_type ), [ 'status' => 401 ] );
		}

		if ( ! $post ) {
			return new \WP_Error( 500, 'Post not found' );
		}

		$new_post_id = $this->duplicate_post( $post, $post_title );

		if ( is_wp_error( $new_post_id ) ) {
			return new \WP_Error( 500, 'Error while duplicating post.' );
		}

		// Duplicate all post meta
		$this->duplicate_post_meta( $post_id, $new_post_id );

		// Duplicate all taxonomies
		$this->duplicate_post_taxonomies( $post_id, $new_post_id );

		return [
			'post_id' => $new_post_id,
		];
	}

	/**
	 * Duplicate post
	 *
	 * @param $post
	 *
	 * @return int|\WP_Error
	 */
	private function duplicate_post( $post, $post_title ) {
		$post_status = 'draft';
		$current_user = wp_get_current_user();
		$new_post_author = $current_user->ID;

		$args = [
			'comment_status' => $post->comment_status,
			'ping_status' => $post->ping_status,
			'post_author' => $new_post_author,
			'post_content' => $post->post_content,
			'post_excerpt' => $post->post_excerpt,
			'post_parent' => $post->post_parent,
			'post_password' => $post->post_password,
			'post_status' => $post_status,
			'post_title' => $post_title,
			'post_type' => $post->post_type,
			'to_ping' => $post->to_ping,
			'menu_order' => $post->menu_order,
		];

		return wp_insert_post( $args );
	}


	/**
	 * Duplicate the associated post meta to the new post ID.
	 *
	 * @param int $post_id
	 * @param int $new_post_id
	 */
	private function duplicate_post_meta( int $post_id, int $new_post_id ) {
		$post_meta = get_post_meta( $post_id );

		if ( empty( $post_meta ) || ! is_array( $post_meta ) ) {
			return;
		}

		foreach ( $post_meta as $key => $values ) {
			if ( '_wp_old_slug' === $key ) { // Ignore this meta key
				continue;
			}

			foreach ( $values as $value ) {
				$value = maybe_unserialize( $value );
				add_post_meta( $new_post_id, $key, wp_slash( $value ) );
			}
		}
	}

	/**
	 * Duplicate_post_taxonomies
	 *
	 * @param int $post_id
	 * @param int $new_post_id
	 */
	private function duplicate_post_taxonomies( $post_id, $new_post_id ) {
		$taxonomies = array_map( 'sanitize_text_field', get_object_taxonomies( get_post_type( $post_id ) ) );

		if ( empty( $taxonomies ) || ! is_array( $taxonomies ) ) {
			return;
		}

		foreach ( $taxonomies as $taxonomy ) {
			$post_terms = wp_get_object_terms( $post_id, $taxonomy, [ 'fields' => 'slugs' ] );

			if ( ! is_wp_error( $post_terms ) ) {
				wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
			}
		}
	}
}
