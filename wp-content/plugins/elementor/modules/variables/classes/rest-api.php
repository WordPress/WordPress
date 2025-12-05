<?php

namespace Elementor\Modules\Variables\Classes;

use Exception;
use WP_Error;
use WP_REST_Response;
use WP_REST_Request;
use WP_REST_Server;
use Elementor\Plugin;
use Elementor\Modules\Variables\Module as Variables_Module;
use Elementor\Modules\Variables\Storage\Repository as Variables_Repository;
use Elementor\Modules\Variables\Storage\Exceptions\VariablesLimitReached;
use Elementor\Modules\Variables\Storage\Exceptions\RecordNotFound;
use Elementor\Modules\Variables\Storage\Exceptions\DuplicatedLabel;
use Elementor\Modules\Variables\Storage\Exceptions\BatchOperationFailed;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Rest_Api {
	const API_NAMESPACE = 'elementor/v1';
	const API_BASE = 'variables';
	const HTTP_OK = 200;
	const HTTP_CREATED = 201;
	const HTTP_BAD_REQUEST = 400;
	const HTTP_NOT_FOUND = 404;
	const HTTP_SERVER_ERROR = 500;
	const MAX_ID_LENGTH = 64;
	const MAX_LABEL_LENGTH = 50;
	const MAX_VALUE_LENGTH = 512;
	private Variables_Repository $variables_repository;

	public function __construct( Variables_Repository $variables_repository ) {
		$this->variables_repository = $variables_repository;
	}

	public function enough_permissions_to_perform_ro_action() {
		return current_user_can( 'edit_posts' );
	}

	public function enough_permissions_to_perform_rw_action() {
		return current_user_can( 'manage_options' );
	}

	public function register_routes() {
		register_rest_route( self::API_NAMESPACE, '/' . self::API_BASE . '/list', [
			'methods' => WP_REST_Server::READABLE,
			'callback' => [ $this, 'get_variables' ],
			'permission_callback' => [ $this, 'enough_permissions_to_perform_ro_action' ],
		] );

		register_rest_route( self::API_NAMESPACE, '/' . self::API_BASE . '/create', [
			'methods' => WP_REST_Server::CREATABLE,
			'callback' => [ $this, 'create_variable' ],
			'permission_callback' => [ $this, 'enough_permissions_to_perform_rw_action' ],
			'args' => [
				'type' => [
					'required' => true,
					'type' => 'string',
					'validate_callback' => [ $this, 'is_valid_variable_type' ],
					'sanitize_callback' => [ $this, 'trim_and_sanitize_text_field' ],
				],
				'label' => [
					'required' => true,
					'type' => 'string',
					'validate_callback' => [ $this, 'is_valid_variable_label' ],
					'sanitize_callback' => [ $this, 'trim_and_sanitize_text_field' ],
				],
				'value' => [
					'required' => true,
					'type' => 'string',
					'validate_callback' => [ $this, 'is_valid_variable_value' ],
					'sanitize_callback' => [ $this, 'trim_and_sanitize_text_field' ],
				],
			],
		] );

		register_rest_route( self::API_NAMESPACE, '/' . self::API_BASE . '/update', [
			'methods' => WP_REST_Server::EDITABLE,
			'callback' => [ $this, 'update_variable' ],
			'permission_callback' => [ $this, 'enough_permissions_to_perform_rw_action' ],
			'args' => [
				'id' => [
					'required' => true,
					'type' => 'string',
					'validate_callback' => [ $this, 'is_valid_variable_id' ],
					'sanitize_callback' => [ $this, 'trim_and_sanitize_text_field' ],
				],
				'label' => [
					'required' => true,
					'type' => 'string',
					'validate_callback' => [ $this, 'is_valid_variable_label' ],
					'sanitize_callback' => [ $this, 'trim_and_sanitize_text_field' ],
				],
				'value' => [
					'required' => true,
					'type' => 'string',
					'validate_callback' => [ $this, 'is_valid_variable_value' ],
					'sanitize_callback' => [ $this, 'trim_and_sanitize_text_field' ],
				],
				'order' => [
					'required' => false,
					'type' => 'integer',
					'validate_callback' => [ $this, 'is_valid_order' ],
				],
			],
		] );

		register_rest_route( self::API_NAMESPACE, '/' . self::API_BASE . '/delete', [
			'methods' => WP_REST_Server::EDITABLE,
			'callback' => [ $this, 'delete_variable' ],
			'permission_callback' => [ $this, 'enough_permissions_to_perform_rw_action' ],
			'args' => [
				'id' => [
					'required' => true,
					'type' => 'string',
					'validate_callback' => [ $this, 'is_valid_variable_id' ],
					'sanitize_callback' => [ $this, 'trim_and_sanitize_text_field' ],
				],
			],
		] );

		register_rest_route( self::API_NAMESPACE, '/' . self::API_BASE . '/restore', [
			'methods' => WP_REST_Server::EDITABLE,
			'callback' => [ $this, 'restore_variable' ],
			'permission_callback' => [ $this, 'enough_permissions_to_perform_rw_action' ],
			'args' => [
				'id' => [
					'required' => true,
					'type' => 'string',
					'validate_callback' => [ $this, 'is_valid_variable_id' ],
					'sanitize_callback' => [ $this, 'trim_and_sanitize_text_field' ],
				],
				'label' => [
					'required' => false,
					'type' => 'string',
					'validate_callback' => [ $this, 'is_valid_variable_label' ],
					'sanitize_callback' => [ $this, 'trim_and_sanitize_text_field' ],
				],
				'value' => [
					'required' => false,
					'type' => 'string',
					'validate_callback' => [ $this, 'is_valid_variable_value' ],
					'sanitize_callback' => [ $this, 'trim_and_sanitize_text_field' ],
				],
			],
		] );

		register_rest_route( self::API_NAMESPACE, '/' . self::API_BASE . '/batch', [
			'methods' => WP_REST_Server::CREATABLE,
			'callback' => [ $this, 'process_batch' ],
			'permission_callback' => [ $this, 'enough_permissions_to_perform_rw_action' ],
			'args' => [
				'watermark' => [
					'required' => true,
					'type' => 'integer',
					'validate_callback' => [ $this, 'is_valid_watermark' ],
				],
				'operations' => [
					'required' => true,
					'type' => 'array',
					'validate_callback' => [ $this, 'is_valid_operations_array' ],
				],
			],
		] );
	}

	public function trim_and_sanitize_text_field( $value ) {
		return trim( sanitize_text_field( $value ) );
	}

	public function is_valid_variable_id( $id ) {
		$id = trim( $id );

		if ( empty( $id ) ) {
			return new WP_Error(
				'invalid_variable_id_empty',
				__( 'ID cannot be empty', 'elementor' )
			);
		}

		if ( self::MAX_ID_LENGTH < strlen( $id ) ) {
			return new WP_Error( 'invalid_variable_id_length', sprintf(
				/* translators: %d: Maximum ID length. */
				__( 'ID cannot exceed %d characters', 'elementor' ),
				self::MAX_ID_LENGTH
			) );
		}

		return true;
	}

	public function is_valid_variable_type( $type ) {
		$allowed_types = array_keys( Variables_Module::instance()->get_variable_types_registry()->all() );

		return in_array( $type, $allowed_types, true );
	}

	public function is_valid_variable_label( $label ) {
		$label = trim( $label );

		if ( empty( $label ) ) {
			return new WP_Error(
				'invalid_variable_label_empty',
				__( 'Label cannot be empty', 'elementor' )
			);
		}

		if ( self::MAX_LABEL_LENGTH < strlen( $label ) ) {
			return new WP_Error( 'invalid_variable_label_length', sprintf(
				/* translators: %d: Maximum label length. */
				__( 'Label cannot exceed %d characters', 'elementor' ),
				self::MAX_LABEL_LENGTH
			) );
		}

		return true;
	}

	public function is_valid_order( $order ) {
		if ( ! is_numeric( $order ) || $order < 0 ) {
			return new WP_Error(
				'invalid_order',
				__( 'Order must be a non-negative integer', 'elementor' )
			);
		}

		return true;
	}

	public function is_valid_variable_value( $value ) {
		$value = trim( $value );

		if ( empty( $value ) ) {
			return new WP_Error(
				'invalid_variable_value_empty',
				__( 'Value cannot be empty', 'elementor' )
			);
		}

		if ( self::MAX_VALUE_LENGTH < strlen( $value ) ) {
			return new WP_Error( 'invalid_variable_value_length', sprintf(
				/* translators: %d: Maximum value length. */
				__( 'Value cannot exceed %d characters', 'elementor' ),
				self::MAX_VALUE_LENGTH
			) );
		}

		return true;
	}

	public function create_variable( WP_REST_Request $request ) {
		try {
			return $this->create_new_variable( $request );
		} catch ( Exception $e ) {
			return $this->error_response( $e );
		}
	}

	protected function clear_cache() {
		Plugin::$instance->files_manager->clear_cache();
	}

	private function create_new_variable( WP_REST_Request $request ) {
		$type = $request->get_param( 'type' );
		$label = $request->get_param( 'label' );
		$value = $request->get_param( 'value' );

		$result = $this->variables_repository->create( [
			'type' => $type,
			'label' => $label,
			'value' => $value,
		] );

		$this->clear_cache();

		return $this->success_response( [
			'variable' => $result['variable'],
			'watermark' => $result['watermark'],
		], self::HTTP_CREATED );
	}

	public function update_variable( WP_REST_Request $request ) {
		try {
			return $this->update_existing_variable( $request );
		} catch ( Exception $e ) {
			return $this->error_response( $e );
		}
	}

	private function update_existing_variable( WP_REST_Request $request ) {
		$id = $request->get_param( 'id' );
		$label = $request->get_param( 'label' );
		$value = $request->get_param( 'value' );
		$order = $request->get_param( 'order' );

		$update_data = [
			'label' => $label,
			'value' => $value,
		];

		if ( null !== $order ) {
			$update_data['order'] = $order;
		}

		$result = $this->variables_repository->update( $id, $update_data );

		$this->clear_cache();

		return $this->success_response( [
			'variable' => $result['variable'],
			'watermark' => $result['watermark'],
		] );
	}

	public function delete_variable( WP_REST_Request $request ) {
		try {
			return $this->delete_existing_variable( $request );
		} catch ( Exception $e ) {
			return $this->error_response( $e );
		}
	}

	private function delete_existing_variable( WP_REST_Request $request ) {
		$id = $request->get_param( 'id' );

		$result = $this->variables_repository->delete( $id );

		$this->clear_cache();

		return $this->success_response( [
			'variable' => $result['variable'],
			'watermark' => $result['watermark'],
		] );
	}

	public function restore_variable( WP_REST_Request $request ) {
		try {
			return $this->restore_existing_variable( $request );
		} catch ( Exception $e ) {
			return $this->error_response( $e );
		}
	}

	private function restore_existing_variable( WP_REST_Request $request ) {
		$id = $request->get_param( 'id' );

		$overrides = [];

		$label = $request->get_param( 'label' );

		if ( $label ) {
			$overrides['label'] = $label;
		}

		$value = $request->get_param( 'value' );

		if ( $value ) {
			$overrides['value'] = $value;
		}

		$result = $this->variables_repository->restore( $id, $overrides );

		$this->clear_cache();

		return $this->success_response( [
			'variable' => $result['variable'],
			'watermark' => $result['watermark'],
		] );
	}

	public function get_variables() {
		try {
			return $this->list_of_variables();
		} catch ( Exception $e ) {
			return $this->error_response( $e );
		}
	}

	private function list_of_variables() {
		$db_record = $this->variables_repository->load();

		return $this->success_response( [
			'variables' => $db_record['data'] ?? [],
			'total' => count( $db_record['data'] ),
			'watermark' => $db_record['watermark'],
		] );
	}

	private function success_response( $payload, $status_code = null ) {
		return new WP_REST_Response( [
			'success' => true,
			'data' => $payload,
		], $status_code ?? self::HTTP_OK );
	}

	private function error_response( Exception $e ) {
		if ( $e instanceof VariablesLimitReached ) {
			return $this->prepare_error_response(
				self::HTTP_BAD_REQUEST,
				'invalid_variable_limit_reached',
				__( 'Reached the maximum number of variables', 'elementor' )
			);
		}

		if ( $e instanceof DuplicatedLabel ) {
			return $this->prepare_error_response(
				self::HTTP_BAD_REQUEST,
				'duplicated_label',
				__( 'Variable label already exists', 'elementor' )
			);
		}

		if ( $e instanceof RecordNotFound ) {
			return $this->prepare_error_response(
				self::HTTP_NOT_FOUND,
				'variable_not_found',
				__( 'Variable not found', 'elementor' )
			);
		}

		return $this->prepare_error_response(
			self::HTTP_SERVER_ERROR,
			'unexpected_server_error',
			__( 'Unexpected server error', 'elementor' )
		);
	}

	private function prepare_error_response( $status_code, $error, $message ) {
		return new WP_REST_Response( [
			'code' => $error,
			'message' => $message,
			'data' => [
				'status' => $status_code,
			],
		], $status_code );
	}

	public function is_valid_watermark( $watermark ) {
		if ( ! is_numeric( $watermark ) || $watermark < 0 ) {
			return new WP_Error(
				'invalid_watermark',
				__( 'Watermark must be a non-negative integer', 'elementor' )
			);
		}

		return true;
	}


	public function is_valid_operations_array( $operations ) {
		if ( ! is_array( $operations ) || empty( $operations ) ) {
			return new WP_Error(
				'invalid_operations_empty',
				__( 'Operations array cannot be empty', 'elementor' )
			);
		}

		foreach ( $operations as $index => $operation ) {
			if ( ! is_array( $operation ) || ! isset( $operation['type'] ) ) {
				return new WP_Error(
					'invalid_operation_structure',
					sprintf(
						/* translators: %d: operation index */
						__( 'Invalid operation structure at index %d', 'elementor' ),
						$index
					)
				);
			}

			$allowed_types = [ 'create', 'update', 'delete', 'restore', 'reorder' ];

			if ( ! in_array( $operation['type'], $allowed_types, true ) ) {
				return new WP_Error(
					'invalid_operation_type',
					sprintf(
						/* translators: %d: operation index */
						__( 'Invalid operation type at index %d', 'elementor' ),
						$index
					)
				);
			}
		}

		return true;
	}

	public function process_batch( WP_REST_Request $request ) {
		try {
			return $this->process_batch_operations( $request );
		} catch ( Exception $e ) {
			return $this->batch_error_response( $e );
		}
	}

	private function process_batch_operations( WP_REST_Request $request ) {
		$watermark = $request->get_param( 'watermark' );
		$operations = $request->get_param( 'operations' );

		$result = $this->variables_repository->process_atomic_batch( $operations, $watermark );

		$this->clear_cache();

		return $this->success_response( $result );
	}


	private function batch_error_response( Exception $e ) {
		if ( $e instanceof BatchOperationFailed ) {
			$error_details = $e->getErrorDetails();
			$batch_error_context = $this->determine_batch_error_context( $error_details );

			return new WP_REST_Response( [
				'success' => false,
				'code' => $batch_error_context['code'],
				'message' => $batch_error_context['message'],
				'data' => $batch_error_context['filtered_errors'],
			], self::HTTP_BAD_REQUEST );
		}

		return $this->error_response( $e );
	}

	private function determine_batch_error_context( array $error_details ) {
		$error_config = [
			'invalid_variable_limit_reached' => [
				'batch_code' => 'batch_variables_limit_reached',
				'batch_message' => __( 'Batch operation failed: Reached the maximum number of variables', 'elementor' ),
				'status' => self::HTTP_BAD_REQUEST,
				'message' => __( 'Reached the maximum number of variables', 'elementor' ),
			],
			'duplicated_label' => [
				'batch_code' => 'batch_duplicated_label',
				'batch_message' => __( 'Batch operation failed: Variable labels already exist', 'elementor' ),
				'status' => self::HTTP_BAD_REQUEST,
				'message' => __( 'Variable label already exists', 'elementor' ),
			],
			'variable_not_found' => [
				'batch_code' => 'batch_variables_not_found',
				'batch_message' => __( 'Batch operation failed: Variables not found', 'elementor' ),
				'status' => self::HTTP_NOT_FOUND,
				'message' => __( 'Variable not found', 'elementor' ),
			],
		];

		$grouped_errors = [];

		foreach ( $error_details as $id => $error_detail ) {
			$error_code = $error_detail['code'] ?? '';

			if ( isset( $error_config[ $error_code ] ) ) {
				$config = $error_config[ $error_code ];
				$grouped_errors[ $error_code ][ $id ] = [
					'status' => $config['status'],
					'message' => $config['message'],
				];
			} else {
				$grouped_errors['unknown'][ $id ] = [
					'status' => self::HTTP_SERVER_ERROR,
					'message' => $error_detail['message'] ?? __( 'Unexpected error', 'elementor' ),
				];
			}
		}

		foreach ( $error_config as $error_code => $config ) {
			if ( ! empty( $grouped_errors[ $error_code ] ) ) {
				return [
					'code' => $config['batch_code'],
					'message' => $config['batch_message'],
					'filtered_errors' => $grouped_errors[ $error_code ],
				];
			}
		}

		return [
			'code' => 'batch_operation_failed',
			'message' => __( 'Batch operation failed', 'elementor' ),
			'filtered_errors' => $grouped_errors['unknown'] ?? [],
		];
	}
}
