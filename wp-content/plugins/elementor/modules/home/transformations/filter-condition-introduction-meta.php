<?php
namespace Elementor\Modules\Home\Transformations;

use Elementor\Modules\Home\Transformations\Base\Transformations_Abstract;
use Elementor\User;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Filter_Condition_Introduction_Meta extends Transformations_Abstract {

	public array $introduction_meta_data;

	public function __construct( $args ) {
		parent::__construct( $args );

		$this->introduction_meta_data = User::get_introduction_meta() ?? [];
	}

	public function transform( array $home_screen_data ): array {
		$introduction_meta_conditions = $this->get_introduction_meta_conditions( $home_screen_data );
		$active_addons = $this->get_activated_addons( $introduction_meta_conditions );
		$home_screen_data['add_ons']['repeater'] = $this->get_inactive_addons( $home_screen_data, $active_addons );

		return $home_screen_data;
	}

	private function get_introduction_meta_conditions( $home_screen_data ): array {
		$add_ons = $home_screen_data['add_ons']['repeater'];

		$conditions = [];

		foreach ( $add_ons as $add_on ) {
			if ( array_key_exists( 'condition', $add_on ) && 'introduction_meta' === $add_on['condition']['key'] ) {
				$conditions[ $add_on['title'] ] = $add_on['condition']['value'];
			}
		}

		return $conditions;
	}

	private function get_activated_addons( $conditions ): array {
		$active_addons = [];

		foreach ( $conditions as $add_on_title => $introduction_meta_value ) {
			if ( ! empty( $this->introduction_meta_data[ $introduction_meta_value ] ) ) {
				$active_addons[] = $add_on_title;
			}
		}

		return $active_addons;
	}

	private function get_inactive_addons( $home_screen_data, $active_addons ): array {
		$add_ons = $home_screen_data['add_ons']['repeater'];
		$inactive_add_ons = [];

		foreach ( $add_ons as $add_on ) {
			if ( ! in_array( $add_on['title'], $active_addons ) ) {
				$inactive_add_ons[] = $add_on;
			}
		}

		return $inactive_add_ons;
	}
}
