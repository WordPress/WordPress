<?php

namespace Elementor\Modules\Components;

use Elementor\Core\Utils\Api\Error_Builder;
use Elementor\Core\Utils\Api\Response_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Components_REST_API {
	const API_NAMESPACE = 'elementor/v1';
	const API_BASE = 'components';
	const STYLES_ROUTE = 'styles';
	const MAX_COMPONENTS = 50;

	private $repository = null;

	public function register_hooks() {
		add_action( 'rest_api_init', fn() => $this->register_routes() );
	}

	private function get_repository() {
		if ( ! $this->repository ) {
			$this->repository = new Components_Repository();
		}

		return $this->repository;
	}

	private function register_routes() {
		register_rest_route( self::API_NAMESPACE, '/' . self::API_BASE, [
			[
				'methods' => 'GET',
				'callback' => fn() => $this->route_wrapper( fn() => $this->get_components() ),
				'permission_callback' => fn() => is_user_logged_in(),
			],
		] );

		register_rest_route( self::API_NAMESPACE, '/' . self::API_BASE . '/' . self::STYLES_ROUTE, [
			[
				'methods' => 'GET',
				'callback' => fn() => $this->route_wrapper( fn() => $this->get_styles() ),
				'permission_callback' => fn() => is_user_logged_in(),
			],
		] );

		register_rest_route( self::API_NAMESPACE, '/' . self::API_BASE, [
			[
				'methods' => 'POST',
				'callback' => fn( $request ) => $this->route_wrapper( fn() => $this->create_component( $request ) ),
				'permission_callback' => fn() => current_user_can( 'manage_options' ),
				'args' => [
					'name' => [
						'type' => 'string',
						'required' => true,
					],
					'content' => [
						'type' => 'array',
						'required' => true,
						'items' => [
							'type' => 'object',
						],
					],
				],
			],
		] );
	}

	private function get_components() {
		$components = $this->get_repository()->all();

		$components_list = $components->get_components()->map( fn( $component ) => [
			'id' => $component['id'],
			'name' => $component['name'],
		])->all();

		return Response_Builder::make( $components_list )->build();
	}

	private function get_styles() {
		$components = $this->get_repository()->all();

		$styles = [];
		$components->get_components()->each( function( $component ) use ( &$styles ) {
			$styles[ $component['id'] ] = $component['styles'];
		} );

		return Response_Builder::make( $styles )->build();
	}
	private function create_component( \WP_REST_Request $request ) {
		$components = $this->get_repository()->all();
		$components_count = $components->get_components()->count();

		if ( $components_count >= static::MAX_COMPONENTS ) {
			return Error_Builder::make( 'components_limit_exceeded' )
				->set_status( 400 )
				->set_message( sprintf(
					/* translators: %d: maximum components limit. */
					__( 'Components limit exceeded. Maximum allowed: %d', 'elementor' ),
					static::MAX_COMPONENTS
				) )
				->build();
		}

		$parser = Components_Parser::make();

		$name_result = $parser->parse_name( $request->get_param( 'name' ), $components->get_components()->map( fn( $component ) => $component['name'] )->all() );

		if ( ! $name_result->is_valid() ) {
			return Error_Builder::make( 'invalid_name' )
				->set_status( 400 )
				->set_message( 'Invalid component name: ' . $name_result->errors()->to_string() )
				->build();
		}

		$name = $name_result->unwrap();
		// The content is validated & sanitized in the document save process.
		$content = $request->get_param( 'content' );

		try {
			$component_id = $this->get_repository()->create( $name, $content );

			return Response_Builder::make( [ 'component_id' => $component_id ] )->set_status( 201 )->build();
		} catch ( \Exception $e ) {
			$error_message = $e->getMessage();

			$invalid_elements_structure_error = str_contains( $error_message, 'Invalid data' );
			$atomic_styles_validation_error = str_contains( $error_message, 'Styles validation failed' );
			$atomic_settings_validation_error = str_contains( $error_message, 'Settings validation failed' );

			if ( $invalid_elements_structure_error || $atomic_styles_validation_error || $atomic_settings_validation_error ) {
				return Error_Builder::make( 'content_validation_failed' )
											->set_status( 400 )
											->set_message( $error_message )
											->build();
			}

			throw $e;
		}
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
}
