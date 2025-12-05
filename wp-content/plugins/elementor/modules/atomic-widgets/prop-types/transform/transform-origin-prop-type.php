<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes\Transform;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Traits\Dimensional_Prop_Type;
use Elementor\Modules\AtomicWidgets\Styles\Size_Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Transform_Origin_Prop_Type extends Object_Prop_Type {
	use Dimensional_Prop_Type;

	public static function get_key(): string {
		return 'transform-origin';
	}

	protected function get_default_value_by_bind( $bind ): ?array {
		return 'z' === $bind
			? null
			: [
				'size' => 50,
				'unit' => Size_Constants::UNIT_PERCENT,
			];
	}

	protected function units( $bind ): array {
		return 'z' === $bind
			? [ Size_Constants::UNIT_PX, Size_Constants::UNIT_EM, Size_Constants::UNIT_REM ]
			: [ Size_Constants::UNIT_PX, Size_Constants::UNIT_PERCENT, Size_Constants::UNIT_EM, Size_Constants::UNIT_REM ];
	}
}
