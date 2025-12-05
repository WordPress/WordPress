<?php

namespace Elementor\Modules\GlobalClasses;

use Elementor\Modules\GlobalClasses\Usage\Applied_Global_Classes_Usage;
use Elementor\Core\Utils\Api\Error_Builder;
use Elementor\Core\Utils\Api\Response_Builder;
use Elementor\Modules\GlobalClasses\Database\Migrations\Add_Capabilities;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Global_Classes_REST_API {
	const API_NAMESPACE = 'elementor/v1';
	const API_BASE = 'global-classes';
	const API_BASE_USAGE = self::API_BASE . '/usage';
	const MAX_ITEMS = 100;
	const LABEL_PREFIX = 'DUP_';
	const MAX_LABEL_LENGTH = 50;
	private $repository = null;

	public function register_hooks() {
		add_action( 'rest_api_init', fn() => $this->register_routes() );
	}

	private function get_repository() {
		if ( ! $this->repository ) {
			$this->repository = new Global_Classes_Repository();
		}

		return $this->repository;
	}

	private function register_routes() {
		register_rest_route( self::API_NAMESPACE, '/' . self::API_BASE, [
			[
				'methods' => 'GET',
				'callback' => fn( $request ) => $this->route_wrapper( fn() => $this->all( $request ) ),
				'permission_callback' => fn() => is_user_logged_in(),
				'args' => [
					'context' => [
						'type' => 'string',
						'required' => false,
						'default' => Global_Classes_Repository::CONTEXT_FRONTEND,
						'enum' => [
							Global_Classes_Repository::CONTEXT_FRONTEND,
							Global_Classes_Repository::CONTEXT_PREVIEW,
						],
					],
				],
			],
		] );

		register_rest_route( self::API_NAMESPACE, '/' . self::API_BASE_USAGE, [
			[
				'callback' => fn() => $this->route_wrapper( fn() => $this->get_usage() ),
				'permission_callback' => fn() => current_user_can( 'manage_options' ),
				'args' => [
					'context' => [
						'type' => 'string',
						'required' => false,
						'default' => Global_Classes_Repository::CONTEXT_FRONTEND,
						'enum' => [
							Global_Classes_Repository::CONTEXT_FRONTEND,
							Global_Classes_Repository::CONTEXT_PREVIEW,
						],
					],
				],
			],
		] );

		register_rest_route( self::API_NAMESPACE, '/' . self::API_BASE, [
			[
				'methods' => 'PUT',
				'callback' => fn( $request ) => $this->route_wrapper( fn() => $this->put( $request ) ),
				'permission_callback' => fn() => current_user_can( Add_Capabilities::UPDATE_CLASS ),
				'args' => [
					'context' => [
						'type' => 'string',
						'required' => false,
						'default' => Global_Classes_Repository::CONTEXT_FRONTEND,
						'enum' => [
							Global_Classes_Repository::CONTEXT_FRONTEND,
							Global_Classes_Repository::CONTEXT_PREVIEW,
						],
					],
					'changes' => [
						'type' => 'object',
						'required' => true,
						'additionalProperties' => false,
						'properties' => [
							'added' => [
								'type' => 'array',
								'required' => true,
								'items' => [ 'type' => 'string' ],
							],
							'deleted' => [
								'type' => 'array',
								'required' => true,
								'items' => [ 'type' => 'string' ],
							],
							'modified' => [
								'type' => 'array',
								'required' => true,
								'items' => [ 'type' => 'string' ],
							],
						],
					],
					'items' => [
						'required' => true,
						'type' => 'object',
						'additionalProperties' => [
							'type' => 'object',
							'properties' => [
								'id' => [
									'type' => 'string',
									'required' => true,
								],
								'variants' => [
									'type' => 'array',
									'required' => true,
								],
								'type' => [
									'type' => 'string',
									'enum' => [ 'class' ],
									'required' => true,
								],
								'label' => [
									'type' => 'string',
									'required' => true,
								],
							],
						],
					],
					'order' => [
						'required' => true,
						'type' => 'array',
						'items' => [
							'type' => 'string',
						],
					],
				],
			],
		] );
	}

	private function all( \WP_REST_Request $request ) {
		$context = $request->get_param( 'context' );

		$classes = $this->get_repository()->context( $context )->all();

		return Response_Builder::make( (object) $classes->get_items()->all() )
			->set_meta( [ 'order' => $classes->get_order()->all() ] )
			->build();
	}

	private function get_usage() {
		$classes_usage = ( new Applied_Global_Classes_Usage() )->get_detailed_usage();

		return Response_Builder::make( (object) $classes_usage )->build();
	}

	private function put( \WP_REST_Request $request ) {
		$context = $request->get_param( 'context' );
		$changes = $request->get_param( 'changes' ) ?? [];
		$new_added_items_ids = $changes['added'] ?? [];
		$parser = Global_Classes_Parser::make();
		$existing_labels = Global_Classes_Repository::make()
			->context( $context )
			->all()
			->get_items()
			->map( function ( $item ) {
				return $item['label'];
			} )
		->all();

		$items_result = $parser->parse_items(
			$request->get_param( 'items' )
		);

		$items_count = count( $items_result->unwrap() );

		if ( $items_count > self::MAX_ITEMS ) {
			return Error_Builder::make( 'global_classes_limit_exceeded' )
				->set_status( 400 )
				->set_meta([
					'current_count' => $items_count,
					'max_allowed' => self::MAX_ITEMS,
				])
				->set_message(sprintf(
					/* translators: %d: Maximum allowed items. */
					__( 'Global classes limit exceeded. Maximum allowed: %d', 'elementor' ),
					self::MAX_ITEMS
				))
				->build();
		}

		if ( ! $items_result->is_valid() ) {
			return Error_Builder::make( 'invalid_items' )
				->set_status( 400 )
				->set_message( 'Invalid items: ' . $items_result->errors()->to_string() )
				->build();
		}

		$order_result = $parser->parse_order(
			$request->get_param( 'order' ),
			$items_result->unwrap()
		);

		if ( ! $order_result->is_valid() ) {
			return Error_Builder::make( 'invalid_order' )
				->set_status( 400 )
				->set_message( 'Invalid order: ' . $order_result->errors()->to_string() )
				->build();
		}

		$repository = $this->get_repository()
			->context( $request->get_param( 'context' ) );

		$changes_resolver = Global_Classes_Changes_Resolver::make(
			$repository,
			$changes,
		);

		$duplicated_labels = Global_Classes_Parser::check_for_duplicate_labels(
			$existing_labels,
			$items_result->unwrap(),
			$new_added_items_ids
		);

		$final_items = $items_result->unwrap();
		$duplicate_validation_result = null;

		if ( ! empty( $duplicated_labels ) ) {
			$modified_labels = $this->handle_duplicates( $duplicated_labels, $existing_labels );
			$duplicate_validation_result = $modified_labels;
			foreach ( $modified_labels as $item_id => $labels ) {
					$final_items[ $item_id ]['label'] = $labels['modified'];
			}
		}

		$repository->put(
			$changes_resolver->resolve_items( $final_items ),
			$changes_resolver->resolve_order( $order_result->unwrap() ),
		);

		if ( $duplicate_validation_result ) {
			$response_data = [
				'code' => 'DUPLICATED_LABEL',
				'modifiedLabels' => $duplicate_validation_result,
			];
			return Response_Builder::make( $response_data )->build();
		}

		return Response_Builder::make()->no_content()->build();
	}

	private function route_wrapper( callable $cb ) {
		try {
			$response = $cb();
		} catch ( \Exception $e ) {
			return Error_Builder::make( 'unexpected_error' )
				->set_message( __( 'Something went wrong', 'elementor' ) )
				->build();
		}

		return $response;
	}

	private function handle_duplicates( array $duplicate_labels, array $existing_labels ) {

		$modified_labels = [];

		foreach ( $duplicate_labels as $duplicate_label ) {
			$item_id = $duplicate_label['item_id'];
			$original_label = $duplicate_label['label'];

			$modified_label = $this->generate_unique_label( $original_label, $existing_labels );

			$modified_labels[ $item_id ] = [
				'original' => $original_label,
				'modified' => $modified_label,
			];
		}

		return $modified_labels;
	}


	private function generate_unique_label( $original_label, $existing_labels ) {
		$prefix = self::LABEL_PREFIX;
		$max_length = self::MAX_LABEL_LENGTH;

		$has_prefix = strpos( $original_label, $prefix ) === 0;

		if ( $has_prefix ) {
			$base_label = substr( $original_label, strlen( $prefix ) );

			$counter = 1;
			$new_label = $prefix . $base_label . $counter;

			while ( in_array( $new_label, $existing_labels, true ) ) {
				++$counter;
				$new_label = $prefix . $base_label . $counter;
			}

			if ( strlen( $new_label ) > $max_length ) {
				$available_length = $max_length - strlen( $prefix . $counter );
				$base_label = substr( $base_label, 0, $available_length );
				$new_label = $prefix . $base_label . $counter;
			}
		} else {
			$new_label = $prefix . $original_label;

			if ( strlen( $new_label ) > $max_length ) {
				$available_length = $max_length - strlen( $prefix );
				$new_label = $prefix . substr( $original_label, 0, $available_length );
			}

			$counter = 1;
			$base_label = substr( $original_label, 0, $available_length ?? strlen( $original_label ) );

			while ( in_array( $new_label, $existing_labels, true ) ) {
				$new_label = $prefix . $base_label . $counter;

				if ( strlen( $new_label ) > $max_length ) {
					$available_length = $max_length - strlen( $prefix . $counter );
					$base_label = substr( $original_label, 0, $available_length );
					$new_label = $prefix . $base_label . $counter;
				}

				++$counter;
			}
		}

		return $new_label;
	}
}
