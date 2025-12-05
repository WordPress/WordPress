<?php

namespace elementor\modules\home\transformations;

use Elementor\Core\Common\Modules\Connect\Module as ConnectModule;
use Elementor\Modules\Home\Transformations\Base\Transformations_Abstract;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Filter_Top_Section_By_License extends Transformations_Abstract {
	public bool $has_pro;
	private array $supported_tiers;

	public function __construct( array $args = [] ) {
		parent::__construct( $args );

		$this->has_pro = Utils::has_pro();
		$this->supported_tiers = [
			ConnectModule::ACCESS_TIER_FREE,
			ConnectModule::ACCESS_TIER_PRO_LEGACY,
		];
	}

	private function is_valid_item( $item ) {
		if ( isset( $item['license'] ) ) {
			$item_tier = $item['license'][0];
			$user_tier = $this->get_tier();

			return $this->validate_tier( $item_tier, $user_tier );
		}

		return false;
	}

	private function validate_tier( $item_tier, $user_tier ): bool {
		if ( $user_tier === $item_tier ) {
			return true;
		}

		$is_item_tier_free = ConnectModule::ACCESS_TIER_FREE === $item_tier;
		$is_valid = $this->has_pro !== $is_item_tier_free;

		return $is_valid && in_array( $item_tier, $this->supported_tiers, true );
	}

	public function transform( array $home_screen_data ): array {
		$new_top = [];

		foreach ( $home_screen_data['top_with_licences'] as $index => $item ) {
			if ( $this->is_valid_item( $item ) ) {
				$new_top = $item;
				break;
			}
		}

		$home_screen_data['top_with_licences'] = $new_top;

		return $home_screen_data;
	}
}
