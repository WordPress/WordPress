<?php

namespace Elementor\Modules\AtomicWidgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Cache_Validity {
	const CACHE_KEY_PREFIX = 'elementor_atomic_cache_validity-';

	public function is_valid( array $keys ): bool {
		$root = array_shift( $keys );

		$state_item = get_option( self::CACHE_KEY_PREFIX . $root, null );

		if ( ! empty( $keys ) ) {
			if ( ! $state_item ) {
				return false;
			}

			$state_item = $this->get_nested_item( $state_item, $keys );
		}

		return $state_item ? $state_item['state'] : false;
	}

	public function get_meta( array $keys ) {
		$root = array_shift( $keys );

		$state_item = get_option( self::CACHE_KEY_PREFIX . $root, null );

		if ( ! $state_item ) {
			return null;
		}

		$state_item = $this->get_nested_item( $state_item, $keys );

		return isset( $state_item['meta'] ) ? $state_item['meta'] : null;
	}

	public function invalidate( array $keys ): void {
		$root = array_shift( $keys );

		$state_item = get_option( self::CACHE_KEY_PREFIX . $root, [
			'state' => false,
			'meta' => null,
			'children' => [],
		] );

		$current_item = &$state_item;

		if ( ! empty( $keys ) ) {
			$current_item = &$this->get_nested_item( $current_item, $keys );
		}

		$current_item['state'] = false;
		$current_item['meta'] = null;

		$this->invalidate_nested_items( $current_item );

		update_option( self::CACHE_KEY_PREFIX . $root, $state_item );
	}

	public function validate( array $keys, $meta = null ): void {
		$root = array_shift( $keys );

		$state_item = get_option( self::CACHE_KEY_PREFIX . $root, [
			'state' => false,
			'children' => [],
		] );

		$current_item = &$state_item;

		if ( ! empty( $keys ) ) {
			$current_item = &$this->get_nested_item( $current_item, $keys );
		}

		$current_item['state'] = true;
		$current_item['meta'] = $meta;

		update_option( self::CACHE_KEY_PREFIX . $root, $state_item );
	}


	/**
	 * @param array{state: boolean, meta: array<string, mixed> | null, children: array<string, self>} $root_item
	 * @param array<string>                                                                           $keys
	 * @return array{state: boolean, meta: array<string, mixed> | null, children: array<string, self>}
	 */
	private function &get_nested_item( array &$root_item, array $keys ): array {
		$current_item = &$root_item;

		while ( ! empty( $keys ) ) {
			$key = array_shift( $keys );

			if ( ! isset( $current_item['children'][ $key ] ) ) {
				$current_item['children'][ $key ] = [
					'state' => false,
					'meta' => null,
					'children' => [],
				];
			}

			$current_item = &$current_item['children'][ $key ];
		}

		return $current_item;
	}

	private function invalidate_nested_items( array &$root_item ): void {
		foreach ( $root_item['children'] as &$child_item ) {
			$child_item['state'] = false;
			$child_item['meta'] = null;

			$this->invalidate_nested_items( $child_item );
		}
	}
}
