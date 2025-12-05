<?php

namespace Elementor\Modules\WpRest\Classes;

use Elementor\Core\Utils\Collection;
use Elementor\Modules\WpRest\Base\Query as Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Post_Query extends Base {
	const ENDPOINT = 'post';
	const SEARCH_FILTER_ACCEPTED_ARGS = 2;
	const DEFAULT_FORBIDDEN_POST_TYPES = [ 'e-floating-buttons', 'e-landing-page', 'elementor_library', 'attachment', 'revision', 'nav_menu_item', 'custom_css', 'customize_changeset' ];

	/**
	 * @param string    $search_term The original search query.
	 * @param \WP_Query $wp_query The WP_Query instance.
	 * @return string Modified search query.
	 */
	public function customize_post_query( string $search_term, \WP_Query $wp_query ) {
		$term = $wp_query->get( 'search_term' ) ?? '';
		$is_custom_search = $wp_query->get( 'custom_search' ) ?? false;

		if ( $is_custom_search && ! empty( $term ) ) {
			$escaped = esc_sql( $term );
			$search_term .= ' AND (';
			$search_term .= "post_title LIKE '%{$escaped}%'";
			if ( ctype_digit( $term ) ) {
				$search_term .= ' OR ID = ' . intval( $term );
			} else {
				$search_term .= " OR ID LIKE '%{$escaped}%'";
			}
			$search_term .= ')';
		}

		return $search_term;
	}

	/**
	 * @param \WP_REST_Request $request
	 * @return \WP_REST_Response
	 */
	protected function get( \WP_REST_Request $request ) {
		$params = $request->get_params();
		$term = trim( $params[ self::SEARCH_TERM_KEY ] ?? '' );

		if ( empty( $term ) ) {
			return new \WP_REST_Response( [
				'success' => true,
				'data' => [
					'value' => [],
				],
			], 200 );
		}

		$keys_format_map = $params[ self::KEYS_CONVERSION_MAP_KEY ];
		$requested_count = $params[ self::ITEMS_COUNT_KEY ] ?? 0;
		$validated_count = max( $requested_count, 1 );
		$post_count = min( $validated_count, self::MAX_RESPONSE_COUNT );
		$is_public_only = $params[ self::IS_PUBLIC_KEY ] ?? true;
		$post_types = $this->get_post_types_from_params( $params );

		$query_args = [
			'post_type' => array_keys( $post_types ),
			'numberposts' => $post_count,
			'suppress_filters' => false,
			'custom_search' => true,
			'search_term' => $term,
			'post_status' => $is_public_only ? 'publish' : 'any',
			'orderby' => 'ID',
			'order' => 'ASC',
		];

		if ( ! empty( $params[ self::META_QUERY_KEY ] ) && is_array( $params[ self::META_QUERY_KEY ] ) ) {
			$query_args['meta_query'] = $params[ self::META_QUERY_KEY ];
		}

		if ( ! empty( $params[ self::TAX_QUERY_KEY ] ) && is_array( $params[ self::TAX_QUERY_KEY ] ) ) {
			$query_args['tax_query'] = $params[ self::TAX_QUERY_KEY ];
		}

		$this->add_filter_to_customize_query();
		$posts = new Collection( get_posts( $query_args ) );
		$this->remove_filter_to_customize_query();

		$post_type_labels = ( new Collection( $post_types ) )
			->map( function ( $pt ) {
				return $pt->label;
			} )
			->all();

		return new \WP_REST_Response( [
			'success' => true,
			'data' => [
				'value' => $posts
					->map( function ( $post ) use ( $keys_format_map, $post_type_labels ) {
						$post_object = (array) $post;

						if ( isset( $post_object['post_type'] ) ) {
							$pt_name = $post_object['post_type'];
							if ( isset( $post_type_labels[ $pt_name ] ) ) {
								$post_object['post_type'] = $post_type_labels[ $pt_name ];
							}
						}

						return $this->translate_keys( $post_object, $keys_format_map );
					} )
					->all(),
			],
		], 200 );
	}

	/**
	 * @return void
	 */
	private function add_filter_to_customize_query() {
		$priority = self::SEARCH_FILTER_PRIORITY;
		$accepted_args = self::SEARCH_FILTER_ACCEPTED_ARGS;

		add_filter( 'posts_search', [ $this, 'customize_post_query' ], $priority, $accepted_args );
	}

	/**
	 * @return void
	 */
	private function remove_filter_to_customize_query() {
		$priority = self::SEARCH_FILTER_PRIORITY;
		$accepted_args = self::SEARCH_FILTER_ACCEPTED_ARGS;

		remove_filter( 'posts_search', [ $this, 'customize_post_query' ], $priority, $accepted_args );
	}

	protected function get_endpoint_registration_args(): array {
		return [
			self::INCLUDED_TYPE_KEY => [
				'description' => 'Included post types',
				'type' => 'array',
				'required' => false,
				'default' => null,
				'sanitize_callback' => fn ( ...$args ) => self::sanitize_string_array( ...$args ),
			],
			self::EXCLUDED_TYPE_KEY => [
				'description' => 'Post type to exclude',
				'type' => 'array',
				'required' => false,
				'default' => self::DEFAULT_FORBIDDEN_POST_TYPES,
				'sanitize_callback' => fn ( ...$args ) => self::sanitize_string_array( ...$args ),
			],
			self::SEARCH_TERM_KEY => [
				'description' => 'Posts to search',
				'type' => 'string',
				'required' => false,
				'default' => '',
				'sanitize_callback' => 'sanitize_text_field',
			],
			self::KEYS_CONVERSION_MAP_KEY => [
				'description' => 'Specify keys to extract and convert, i.e. ["key_1" => "new_key_1"].',
				'type' => 'array',
				'required' => false,
				'default' => [
					'ID' => 'id',
					'post_title' => 'label',
					'post_type' => 'groupLabel',
				],
				'sanitize_callback' => fn ( ...$args ) => self::sanitize_string_array( ...$args ),
			],
			self::ITEMS_COUNT_KEY => [
				'description' => 'Posts per page',
				'type' => 'integer',
				'required' => false,
				'default' => self::MAX_RESPONSE_COUNT,
			],
			self::IS_PUBLIC_KEY => [
				'description' => 'Whether to include only public post types',
				'type' => 'boolean',
				'required' => false,
				'default' => true,
			],
			self::META_QUERY_KEY => [
				'description' => 'WP_Query meta_query array',
				'type' => 'array',
				'required' => false,
				'default' => null,
				'sanitize_callback' => fn ( ...$args ) => self::sanitize_string_array( ...$args ),
			],
			self::TAX_QUERY_KEY => [
				'description' => 'WP_Query tax_query array',
				'type' => 'array',
				'required' => false,
				'default' => null,
				'sanitize_callback' => fn ( ...$args ) => self::sanitize_string_array( ...$args ),
			],
		];
	}

	protected static function get_allowed_param_keys(): array {
		return [
			self::EXCLUDED_TYPE_KEY,
			self::INCLUDED_TYPE_KEY,
			self::KEYS_CONVERSION_MAP_KEY,
			self::META_QUERY_KEY,
			self::TAX_QUERY_KEY,
			self::IS_PUBLIC_KEY,
			self::ITEMS_COUNT_KEY,
		];
	}

	protected static function get_keys_to_encode(): array {
		return [
			self::EXCLUDED_TYPE_KEY,
			self::INCLUDED_TYPE_KEY,
			self::KEYS_CONVERSION_MAP_KEY,
			self::META_QUERY_KEY,
			self::TAX_QUERY_KEY,
		];
	}

	private function get_post_types_from_params( $params ) {
		$included_types = $params[ self::INCLUDED_TYPE_KEY ];
		$excluded_types = $params[ self::EXCLUDED_TYPE_KEY ];
		$post_type_query_args = [
			'public' => true,
		];

		$post_types = get_post_types( $post_type_query_args, 'objects' );

		return Collection::make( $post_types )
				->filter( function ( $slug, $post_type ) use ( $included_types, $excluded_types ) {
					return ( empty( $included_types ) || in_array( $post_type, $included_types ) ) &&
						( empty( $excluded_types ) || ! in_array( $post_type, $excluded_types ) );
				} )->all();
	}
}
