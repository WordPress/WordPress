<?php

namespace Elementor\Modules\Variables\Storage;

use Elementor\Core\Kits\Documents\Kit;
use Elementor\Modules\AtomicWidgets\Utils;
use Elementor\Modules\Variables\Storage\Exceptions\DuplicatedLabel;
use Elementor\Modules\Variables\Storage\Exceptions\RecordNotFound;
use Elementor\Modules\Variables\Storage\Exceptions\VariablesLimitReached;
use Elementor\Modules\Variables\Storage\Exceptions\FatalError;
use Elementor\Modules\Variables\Storage\Exceptions\BatchOperationFailed;
use Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Repository {
	const TOTAL_VARIABLES_COUNT = 100;
	const FORMAT_VERSION_V1 = 1;
	const VARIABLES_META_KEY = '_elementor_global_variables';
	private Kit $kit;

	public function __construct( Kit $kit ) {
		$this->kit = $kit;
	}

	/**
	 * @throws VariablesLimitReached If database connection fails or query execution errors occur.
	 */
	private function assert_if_variables_limit_reached( array $db_record ) {
		$variables_in_use = 0;

		foreach ( $db_record['data'] as $variable ) {
			if ( isset( $variable['deleted'] ) && $variable['deleted'] ) {
				continue;
			}

			++$variables_in_use;
		}

		if ( self::TOTAL_VARIABLES_COUNT < $variables_in_use ) {
			throw new VariablesLimitReached( 'Total variables count limit reached' );
		}
	}

	/**
	 * @throws DuplicatedLabel If variable creation fails or validation errors occur.
	 */
	private function assert_if_variable_label_is_duplicated( array $db_record, array $variable = [] ) {
		foreach ( $db_record['data'] as $id => $existing_variable ) {
			if ( isset( $existing_variable['deleted'] ) && $existing_variable['deleted'] ) {
				continue;
			}

			if ( isset( $variable['id'] ) && $variable['id'] === $id ) {
				continue;
			}

			if ( ! isset( $variable['label'] ) || ! isset( $existing_variable['label'] ) ) {
				continue;
			}

			if ( strtolower( $existing_variable['label'] ) === strtolower( $variable['label'] ) ) {
				throw new DuplicatedLabel( 'Variable label already exists' );
			}
		}
	}

	public function variables(): array {
		$db_record = $this->load();

		return $db_record['data'] ?? [];
	}

	public function load(): array {
		$db_record = $this->kit->get_json_meta( static::VARIABLES_META_KEY );

		if ( is_array( $db_record ) && ! empty( $db_record ) ) {
			return $db_record;
		}

		return $this->get_default_meta();
	}

	/**
	 * @throws FatalError If variable update fails or validation errors occur.
	 */
	public function create( array $variable ) {
		$db_record = $this->load();

		$list_of_variables = $db_record['data'] ?? [];

		$id = $this->new_id_for( $list_of_variables );
		$new_variable = $this->extract_from( $variable, [
			'type',
			'label',
			'value',
			'order',
		] );

		if ( ! isset( $new_variable['order'] ) ) {
			$new_variable['order'] = $this->get_next_order( $list_of_variables );
		}

		$this->assert_if_variable_label_is_duplicated( $db_record, $new_variable );

		$list_of_variables[ $id ] = $new_variable;
		$db_record['data'] = $list_of_variables;

		$this->assert_if_variables_limit_reached( $db_record );

		$watermark = $this->save( $db_record );

		if ( false === $watermark ) {
			throw new FatalError( 'Failed to create variable' );
		}

		return [
			'variable' => array_merge( [ 'id' => $id ], $list_of_variables[ $id ] ),
			'watermark' => $watermark,
		];
	}

	/**
	 * @throws RecordNotFound If variable deletion fails or database errors occur.
	 * @throws FatalError If variable deletion fails or database errors occur.
	 */
	public function update( string $id, array $variable ) {
		$db_record = $this->load();

		$list_of_variables = $db_record['data'] ?? [];

		if ( ! isset( $list_of_variables[ $id ] ) ) {
			throw new RecordNotFound( 'Variable not found' );
		}

		$updated_variable = array_merge( $list_of_variables[ $id ], $this->extract_from( $variable, [
			'label',
			'value',
			'order',
		] ) );

		$this->assert_if_variable_label_is_duplicated( $db_record, array_merge( $updated_variable, [ 'id' => $id ] ) );

		$list_of_variables[ $id ] = $updated_variable;
		$db_record['data'] = $list_of_variables;

		$watermark = $this->save( $db_record );

		if ( false === $watermark ) {
			throw new FatalError( 'Failed to update variable' );
		}

		return [
			'variable' => array_merge( [ 'id' => $id ], $list_of_variables[ $id ] ),
			'watermark' => $watermark,
		];
	}

	/**
	 * @throws RecordNotFound If bulk operation fails or validation errors occur.
	 * @throws FatalError If bulk operation fails or validation errors occur.
	 */
	public function delete( string $id ) {
		$db_record = $this->load();

		$list_of_variables = $db_record['data'] ?? [];

		if ( ! isset( $list_of_variables[ $id ] ) ) {
			throw new RecordNotFound( 'Variable not found' );
		}

		$list_of_variables[ $id ]['deleted'] = true;
		$list_of_variables[ $id ]['deleted_at'] = $this->now();

		$db_record['data'] = $list_of_variables;

		$watermark = $this->save( $db_record );

		if ( false === $watermark ) {
			throw new FatalError( 'Failed to delete variable' );
		}

		return [
			'variable' => array_merge( [ 'id' => $id ], $list_of_variables[ $id ] ),
			'watermark' => $watermark,
		];
	}

	/**
	 * @throws RecordNotFound If export operation fails or data serialization errors occur.
	 * @throws FatalError If export operation fails or data serialization errors occur.
	 */
	public function restore( string $id, $overrides = [] ) {
		$db_record = $this->load();

		$list_of_variables = $db_record['data'] ?? [];

		if ( ! isset( $list_of_variables[ $id ] ) ) {
			throw new RecordNotFound( 'Variable not found' );
		}

		$restored_variable = $this->extract_from( $list_of_variables[ $id ], [
			'label',
			'value',
			'type',
			'order',
		] );

		if ( array_key_exists( 'label', $overrides ) ) {
			$restored_variable['label'] = $overrides['label'];
		}

		if ( array_key_exists( 'value', $overrides ) ) {
			$restored_variable['value'] = $overrides['value'];
		}

		$this->assert_if_variable_label_is_duplicated( $db_record, array_merge( $restored_variable, [ 'id' => $id ] ) );

		$list_of_variables[ $id ] = $restored_variable;
		$db_record['data'] = $list_of_variables;

		$this->assert_if_variables_limit_reached( $db_record );

		$watermark = $this->save( $db_record );

		if ( false === $watermark ) {
			throw new FatalError( 'Failed to restore variable' );
		}

		return [
			'variable' => array_merge( [ 'id' => $id ], $restored_variable ),
			'watermark' => $watermark,
		];
	}

	/**
	 * Process multiple operations atomically
	 *
	 * @throws BatchOperationFailed If batch operation fails or validation errors occur.
	 * @throws FatalError If batch operation fails or validation errors occur.
	 */
	public function process_atomic_batch( array $operations, int $expected_watermark ): array {
		$db_record = $this->load();
		$results = [];
		$errors = [];

		foreach ( $operations as $index => $operation ) {
			try {
				$result = $this->process_single_operation( $db_record, $operation );
				$results[] = $result;
			} catch ( Exception $e ) {
				$operation_id = $this->get_operation_identifier( $operation, $index );
				$errors[ $operation_id ] = [
					'status' => $this->get_error_status_code( $e ),
					'code' => $this->get_error_code( $e ),
					'message' => $e->getMessage(),
				];
			}
		}

		if ( ! empty( $errors ) ) {
			$error_details = [];

			foreach ( $errors as $operation_id => $error ) {
				$error_details[ esc_html( $operation_id ) ] = [
					'status' => (int) $error['status'],
					'code' => $error['code'],
					'message' => esc_html( $error['message'] ),
				];
			}

			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			throw new BatchOperationFailed( 'Batch operation failed', $error_details );
		}

		$watermark = $this->save( $db_record );

		if ( false === $watermark ) {
			throw new FatalError( 'Failed to save batch operations' );
		}

		return [
			'success' => true,
			'watermark' => $watermark,
			'results' => $results,
		];
	}

	private function process_single_operation( array &$db_record, array $operation ): array {
		switch ( $operation['type'] ) {
			case 'create':
				return $this->process_create_operation( $db_record, $operation );

			case 'update':
				return $this->process_update_operation( $db_record, $operation );

			case 'delete':
				return $this->process_delete_operation( $db_record, $operation );

			case 'restore':
				return $this->process_restore_operation( $db_record, $operation );

			default:
				throw new BatchOperationFailed( 'Invalid operation type: ' . esc_html( $operation['type'] ), [] );
		}
	}

	private function process_create_operation( array &$db_record, array $operation ): array {
		$variable_data = $operation['variable'];

		$temp_id = $variable_data['id'] ?? null;
		$new_variable = $this->extract_from( $variable_data, [ 'type', 'label', 'value', 'order' ] );

		if ( ! isset( $new_variable['order'] ) ) {
			$new_variable['order'] = $this->get_next_order( $db_record['data'] );
		}

		$this->assert_if_variable_label_is_duplicated( $db_record, $new_variable );

		$this->assert_if_variables_limit_reached( $db_record );

		$id = $this->new_id_for( $db_record['data'] );
		$now = $this->now();

		$new_variable['created_at'] = $now;
		$new_variable['updated_at'] = $now;

		$db_record['data'][ $id ] = $new_variable;

		return [
			'id' => $id,
			'type' => 'create',
			'variable' => array_merge( [ 'id' => $id ], $new_variable ),
			'temp_id' => $temp_id,
		];
	}

	private function process_update_operation( array &$db_record, array $operation ): array {
		$id = $operation['id'];
		$variable_data = $operation['variable'];

		if ( ! isset( $db_record['data'][ $id ] ) ) {
			throw new \Elementor\Modules\Variables\Storage\Exceptions\RecordNotFound( 'Variable not found' );
		}

		$updated_fields = $this->extract_from( $variable_data, [ 'label', 'value', 'order' ] );
		$updated_variable = array_merge( $db_record['data'][ $id ], $updated_fields );
		$updated_variable['updated_at'] = $this->now();

		$this->assert_if_variable_label_is_duplicated( $db_record, array_merge( $updated_variable, [ 'id' => $id ] ) );

		$db_record['data'][ $id ] = $updated_variable;

		return [
			'id' => $id,
			'type' => 'update',
			'variable' => array_merge( [ 'id' => $id ], $updated_variable ),
		];
	}

	private function process_delete_operation( array &$db_record, array $operation ): array {
		$id = $operation['id'];

		if ( ! isset( $db_record['data'][ $id ] ) ) {
			throw new RecordNotFound( 'Variable not found' );
		}

		$db_record['data'][ $id ]['deleted'] = true;
		$db_record['data'][ $id ]['deleted_at'] = $this->now();

		return [
			'id' => $id,
			'type' => 'delete',
			'deleted' => true,
		];
	}

	private function process_restore_operation( array &$db_record, array $operation ): array {
		$id = $operation['id'];

		if ( ! isset( $db_record['data'][ $id ] ) ) {
			throw new RecordNotFound( 'Variable not found' );
		}

		$overrides = [];

		if ( isset( $operation['label'] ) ) {
			$overrides['label'] = $operation['label'];
		}

		if ( isset( $operation['value'] ) ) {
			$overrides['value'] = $operation['value'];
		}

		$restored_variable = $this->extract_from( $db_record['data'][ $id ], [ 'label', 'value', 'type' ] );
		$restored_variable = array_merge( $restored_variable, $overrides );
		$restored_variable['updated_at'] = $this->now();

		$this->assert_if_variable_label_is_duplicated( $db_record, array_merge( $restored_variable, [ 'id' => $id ] ) );

		$this->assert_if_variables_limit_reached( $db_record );

		$db_record['data'][ $id ] = $restored_variable;

		return [
			'id' => $id,
			'type' => 'restore',
			'variable' => array_merge( [ 'id' => $id ], $restored_variable ),
		];
	}

	private function get_operation_identifier( array $operation, int $index ): string {
		if ( 'create' === $operation['type'] && isset( $operation['variable']['id'] ) ) {
			return $operation['variable']['id'];
		}

		if ( isset( $operation['id'] ) ) {
			return $operation['id'];
		}

		return "operation_{$index}";
	}

	private function get_error_status_code( Exception $e ): int {
		if ( $e instanceof RecordNotFound ) {
			return 404;
		}

		if ( $e instanceof DuplicatedLabel || $e instanceof VariablesLimitReached ) {
			return 400;
		}

		return 500;
	}

	private function get_error_code( Exception $e ): string {
		if ( $e instanceof VariablesLimitReached ) {
			return 'invalid_variable_limit_reached';
		}

		if ( $e instanceof DuplicatedLabel ) {
			return 'duplicated_label';
		}

		if ( $e instanceof RecordNotFound ) {
			return 'variable_not_found';
		}

		return 'unexpected_server_error';
	}

	private function save( array $db_record ) {
		if ( PHP_INT_MAX === $db_record['watermark'] ) {
			$db_record['watermark'] = 0;
		}

		++$db_record['watermark'];

		if ( $this->kit->update_json_meta( static::VARIABLES_META_KEY, $db_record ) ) {
			return $db_record['watermark'];
		}

		return false;
	}

	private function new_id_for( array $list_of_variables ): string {
		return Utils::generate_id( 'e-gv-', array_keys( $list_of_variables ) );
	}

	private function now(): string {
		return gmdate( 'Y-m-d H:i:s' );
	}

	private function extract_from( array $source, array $fields ): array {
		return array_intersect_key( $source, array_flip( $fields ) );
	}

	private function get_default_meta(): array {
		return [
			'data' => [],
			'watermark' => 0,
			'version' => self::FORMAT_VERSION_V1,
		];
	}

	private function get_next_order( array $list_of_variables ): int {
		$highest_order = 0;

		foreach ( $list_of_variables as $variable ) {
			if ( isset( $variable['deleted'] ) && $variable['deleted'] ) {
				continue;
			}

			if ( isset( $variable['order'] ) && $variable['order'] > $highest_order ) {
				$highest_order = $variable['order'];
			}
		}

		return $highest_order + 1;
	}
}
