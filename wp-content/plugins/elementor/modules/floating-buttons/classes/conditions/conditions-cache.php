<?php

namespace Elementor\Modules\FloatingButtons\Classes\Conditions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Conditions_Cache {

	const CONDITIONS_CACHE_META_KEY = 'elementor_pro_theme_builder_conditions';

	const CONDITION_TYPE = 'floating_buttons';

	public function remove_from_cache( int $post_id ): void {
		$conditions = $this->get_conditions();

		if ( isset( $conditions[ self::CONDITION_TYPE ][ $post_id ] ) ) {
			unset( $conditions[ self::CONDITION_TYPE ][ $post_id ] );

			if ( empty( $conditions[ self::CONDITION_TYPE ] ) ) {
				unset( $conditions[ self::CONDITION_TYPE ] );
			}

			$this->update_conditions( $conditions );
		}
	}

	public function add_to_cache( int $post_id, array $condition_rules = [ 'include/general' ] ): void {
		$conditions = $this->get_conditions();

		if ( ! isset( $conditions[ self::CONDITION_TYPE ] ) ) {
			$conditions[ self::CONDITION_TYPE ] = [];
		}

		$conditions[ self::CONDITION_TYPE ][ $post_id ] = $condition_rules;

		$this->update_conditions( $conditions );
	}

	private function get_conditions(): array {
		return get_option( self::CONDITIONS_CACHE_META_KEY, [] );
	}

	private function update_conditions( array $conditions ): void {
		update_option( self::CONDITIONS_CACHE_META_KEY, $conditions );
	}
}
