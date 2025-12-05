<?php
namespace Elementor\Modules\Home\Transformations;

use Elementor\Modules\Home\Transformations\Base\Transformations_Abstract;
use Elementor\Utils;
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Filter_Get_Started_By_License extends Transformations_Abstract {
	public bool $has_pro;

	public function __construct( $args ) {
		parent::__construct( $args );

		$this->has_pro = Utils::has_pro();
	}

	private function is_valid_item( $item ) {
		$has_pro_json_not_free = $this->has_pro && 'pro' === $item['license'][0];
		$is_not_pro_json_not_pro = ! $this->has_pro && 'free' === $item['license'][0];

		return $has_pro_json_not_free || $is_not_pro_json_not_pro;
	}

	public function transform( array $home_screen_data ): array {
		$new_get_started = [];

		foreach ( $home_screen_data['get_started'] as $index => $item ) {
			if ( $this->is_valid_item( $item ) ) {
				$new_get_started[] = $item;
			}
		}

		$home_screen_data['get_started'] = reset( $new_get_started );

		return $home_screen_data;
	}
}
