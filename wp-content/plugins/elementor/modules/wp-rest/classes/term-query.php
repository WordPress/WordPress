<?php

namespace Elementor\Modules\WpRest\Classes;

use Elementor\Core\Utils\Collection;
use Elementor\Modules\WpRest\Base\Query as Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Term_Query extends Base {
	const ENDPOINT = 'term';
	const SEARCH_FILTER_ACCEPTED_ARGS = 3;

	/**
	 * @param array $clauses Associative array of the clauses for the query.
	 * @param array $taxonomies Array of taxonomy names.
	 * @param array $args The args passed to 'get_terms()'.
	 * @return array Modified clauses.
	 */
	public function customize_terms_query( $clauses, $taxonomies, $args ) {
		if ( ! $args['custom_search'] ) {
			return $clauses;
		}

		if ( is_numeric( $args['name__like'] ) ) {
			$clauses['where'] = '(' . $clauses['where'] . ' OR t.term_id = ' . $args['name__like'] . ')';
		}

		if ( empty( $args['excluded_taxonomies'] ) ) {
			return $clauses;
		}

		$excluded_taxonomies = $args['excluded_taxonomies'];
		$escaped = array_map( 'esc_sql', $excluded_taxonomies );
		$list = "'" . implode( "','", $escaped ) . "'";
		$clauses['where'] .= " AND tt.taxonomy NOT IN ({$list})";

		return $clauses;
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

		$included_taxonomies = $params[ self::INCLUDED_TYPE_KEY ];
		$excluded_taxonomies = $params[ self::EXCLUDED_TYPE_KEY ];

		$keys_format_map = $params[ self::KEYS_CONVERSION_MAP_KEY ];

		$requested_count = $params[ self::ITEMS_COUNT_KEY ] ?? 0;
		$validated_count = max( $requested_count, 1 );
		$count = min( $validated_count, self::MAX_RESPONSE_COUNT );

		$should_hide_empty = $params[ self::HIDE_EMPTY_KEY ] ?? false;

		$query_args = [
			'number' => $count,
			'name__like' => $term,
			'hide_empty' => $should_hide_empty,
			'taxonomy' => ! empty( $included_taxonomies ) ? $included_taxonomies : null,
			'excluded_taxonomies' => $excluded_taxonomies ?? [],
			'suppress_filter' => false,
			'custom_search' => true,
		];

		if ( ! empty( $params[ self::META_QUERY_KEY ] ) && is_array( $params[ self::META_QUERY_KEY ] ) ) {
			$query_args['meta_query'] = $params[ self::META_QUERY_KEY ];
		}

		$this->add_filter_to_customize_query();
		$terms = new Collection( get_terms( $query_args ) );
		$this->remove_filter_to_customize_query();

		$term_group_labels = $terms
			->reduce( function ( $term_types, $term ) {
				if ( ! isset( $term_types[ $term->taxonomy ] ) ) {
					$taxonomy = get_taxonomy( $term->taxonomy );
					$term_types[ $term->taxonomy ] = $taxonomy->labels->name ?? $term->labels;
				}

				return $term_types;
			}, [] );

		return new \WP_REST_Response( [
			'success' => true,
			'data' => [
				'value' => $terms
					->map( function ( $term ) use ( $keys_format_map, $term_group_labels ) {
						$term_object = (array) $term;

						if ( isset( $term_object['taxonomy'] ) ) {
							$group_name = $term_object['taxonomy'];

							if ( isset( $term_group_labels[ $group_name ] ) ) {
								$term_object['taxonomy'] = $term_group_labels[ $group_name ];
							}
						}

						return $this->translate_keys( $term_object, $keys_format_map );
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

		add_filter( 'terms_clauses', [ $this, 'customize_terms_query' ], $priority, $accepted_args );
	}

	/**
	 * @return void
	 */
	private function remove_filter_to_customize_query() {
		$priority = self::SEARCH_FILTER_PRIORITY;
		$accepted_args = self::SEARCH_FILTER_ACCEPTED_ARGS;

		remove_filter( 'terms_clauses', [ $this, 'customize_terms_query' ], $priority, $accepted_args );
	}

	/**
	 * @return array
	 */
	protected function get_endpoint_registration_args(): array {
		return [
			self::INCLUDED_TYPE_KEY => [
				'description' => 'Included taxonomy containing terms (categories, tags, etc...)',
				'type' => 'array',
				'required' => false,
				'default' => null,
				'sanitize_callback' => fn ( ...$args ) => self::sanitize_string_array( ...$args ),
			],
			self::EXCLUDED_TYPE_KEY => [
				'description' => 'Excluded taxonomy containing terms (categories, tags, etc...)',
				'type' => 'array',
				'required' => false,
				'default' => null,
				'sanitize_callback' => fn ( ...$args ) => self::sanitize_string_array( ...$args ),
			],
			self::SEARCH_TERM_KEY => [
				'description' => 'Terms to search',
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
					'term_id' => 'id',
					'name' => 'label',
					'taxonomy' => 'groupLabel',
				],
				'sanitize_callback' => fn ( ...$args ) => self::sanitize_string_array( ...$args ),
			],
			self::ITEMS_COUNT_KEY => [
				'description' => 'Terms per request',
				'type' => 'integer',
				'required' => false,
				'default' => self::MAX_RESPONSE_COUNT,
			],
			self::HIDE_EMPTY_KEY => [
				'description' => 'Whether to include only public terms',
				'type' => 'boolean',
				'required' => false,
				'default' => false,
			],
			self::META_QUERY_KEY => [
				'description' => 'WP_Query meta_query array',
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
}
