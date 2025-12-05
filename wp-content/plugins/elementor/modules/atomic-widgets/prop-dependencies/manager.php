<?php

namespace Elementor\Modules\AtomicWidgets\PropDependencies;

use Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Manager {

	const RELATION_OR = 'or';
	const RELATION_AND = 'and';

	const OPERATORS = [
		'lt',
		'lte',
		'eq',
		'ne',
		'gte',
		'gt',
		'exists',
		'not_exist',
		'in',
		'nin',
		'contains',
		'ncontains',
	];

	/**
	 * @var ?array{
	 *         relation: self::RELATION_OR|self::RELATION_AND,
	 *         terms: array{
	 *             operator: string,
	 *             path: array<string>,
	 *             value?: mixed,
	 *             newValue?: array
	 *         }
	 *     }
	 */
	private ?array $dependencies;

	public function __construct( string $relation = self::RELATION_OR ) {
		$this->new( $relation );

		return $this;
	}

	public static function make( string $relation = self::RELATION_OR ): self {
		return new self( $relation );
	}

	/**
	 * @param array<string, Prop_Type> $props_schema
	 * @return array<string, array<string>> Returns source prop path => array of dependent prop paths
	 */
	public static function get_source_to_dependents( array $props_schema ): array {
		$dependency_graph = self::build_dependency_graph( $props_schema );

		if ( self::has_circular_dependencies( $dependency_graph ) ) {
			Utils::safe_throw( 'Circular prop dependencies detected' );
		}

		return $dependency_graph;
	}

	/**
	 * @param $config array{
	 *  operator: string,
	 *  path: array<string>,
	 *  value?: mixed,
	 *  newValue?: array,
	 * }
	 * @return self
	 */
	public function where( array $config ): self {
		if ( ! isset( $config['operator'] ) || ! isset( $config['path'] ) ) {
			Utils::safe_throw( 'Term missing mandatory configurations' );
		}

		if ( ! in_array( $config['operator'], self::OPERATORS, true ) ) {
			Utils::safe_throw( "Invalid operator: {$config['operator']}." );
		}

		$term = [
			'operator' => $config['operator'],
			'path' => $config['path'],
			'value' => $config['value'] ?? null,
			'newValue' => $config['newValue'] ?? null,
		];

		if ( empty( $this->dependencies ) ) {
			$this->new();
		}

		$this->dependencies['terms'][] = $term;

		return $this;
	}

	private function new( string $relation = self::RELATION_OR ): self {
		if ( ! in_array( $relation, [ self::RELATION_OR, self::RELATION_AND ], true ) ) {
			Utils::safe_throw( "Invalid relation: $relation. Must be one of: " . implode( ', ', [ self::RELATION_OR, self::RELATION_AND ] ) );
		}

		$this->dependencies = [
			'relation' => $relation,
			'terms' => [],
		];

		return $this;
	}

	public function get(): ?array {
		return empty( $this->dependencies['terms'] ?? [] ) ? null : $this->dependencies;
	}

	/**
	 * @param array<string, Prop_Type>      $props_schema The props schema to analyze, where keys are prop names
	 * @param ?array<string>                $current_path The current property path being processed
	 * @param ?array<string, array<string>> $dependency_graph The dependency graph to build
	 */
	private static function build_dependency_graph( array $props_schema, ?array $current_path = [], ?array $dependency_graph = [] ): array {
		foreach ( $props_schema as $prop_name => $prop_type ) {
			$dependency_graph = self::build_nested_prop_dependency_graph( $prop_name, $prop_type, $current_path, $dependency_graph );
			$dependencies = $prop_type->get_dependencies();

			if ( ! $dependencies ) {
				continue;
			}

			foreach ( $dependencies['terms'] as $term ) {
				$dependency_graph = self::process_dependency_term( $term, $current_path, $prop_name, $dependency_graph );
			}
		}

		return $dependency_graph;
	}

	private static function build_nested_prop_dependency_graph( string $prop_name, Prop_Type $prop_type, array $current_path, array $dependency_graph ): array {
		$nested_prop_path = array_merge( $current_path, [ $prop_name ] );

		switch ( $prop_type->get_type() ) {
			case 'object':
				foreach ( $prop_type->get_shape() as $nested_prop_name => $nested_prop_type ) {
					$dependency_graph = self::build_dependency_graph( [ $nested_prop_name => $nested_prop_type ], $nested_prop_path, $dependency_graph );
				}
				break;

			case 'array':
				$item_prop_type = $prop_type->get_item_type();
				$dependency_graph = self::build_dependency_graph( [ $prop_name => $item_prop_type ], $current_path, $dependency_graph );
				break;

			case 'union':
				foreach ( $prop_type->get_prop_types() as $nested_prop_type ) {
					$dependency_graph = self::build_dependency_graph( [ $prop_name => $nested_prop_type ], $current_path, $dependency_graph );
				}
				break;
		}

		return $dependency_graph;
	}

	private static function process_dependency_term( array $term, array $current_path, string $prop_name, array $dependency_graph ): array {
		if ( self::is_term_nested( $term ) ) {
			foreach ( $term['terms'] as $nested_term ) {
				$dependency_graph = self::process_dependency_term( $nested_term, $current_path, $prop_name, $dependency_graph );
			}

			return $dependency_graph;
		}

		if ( ! isset( $term['path'] ) || empty( $term['path'] ) ) {
			Utils::safe_throw( 'Invalid term path in dependency.' );
		}

		$target_path = implode( '.', $term['path'] );
		$source = array_merge( $current_path, [ $prop_name ] );
		$source_path = implode( '.', $source );

		if ( ! isset( $dependency_graph[ $target_path ] ) ) {
			$dependency_graph[ $target_path ] = [];
		}

		if ( ! in_array( $source_path, $dependency_graph[ $target_path ] ) ) {
			$dependency_graph[ $target_path ][] = $source_path;
		}

		return $dependency_graph;
	}

	private static function has_circular_dependencies( array $dependency_graph ): bool {
		$visited_nodes = [];
		$current_path_stack = [];

		foreach ( array_keys( $dependency_graph ) as $node ) {
			if ( isset( $visited_nodes[ $node ] ) ) {
				continue;
			}

			if ( self::detect_cycle_from_node( $dependency_graph, $node, $visited_nodes, $current_path_stack ) ) {
				return true;
			}
		}

		return false;
	}

	private static function detect_cycle_from_node( array $dependency_graph, string $current_node, array &$visited_nodes, array &$current_path_stack ): bool {
		if ( isset( $current_path_stack[ $current_node ] ) ) {
			return true;
		}

		if ( isset( $visited_nodes[ $current_node ] ) ) {
			return false;
		}

		$visited_nodes[ $current_node ] = true;
		$current_path_stack[ $current_node ] = true;

		foreach ( $dependency_graph[ $current_node ] ?? [] as $dependent_node ) {
			$is_circular = self::detect_cycle_from_node( $dependency_graph, $dependent_node, $visited_nodes, $current_path_stack );

			if ( $is_circular ) {
				return true;
			}
		}

		unset( $current_path_stack[ $current_node ] );

		return false;
	}

	private static function is_term_nested( $term ): bool {
		return isset( $term['terms'] ) && is_array( $term['terms'] );
	}
}
