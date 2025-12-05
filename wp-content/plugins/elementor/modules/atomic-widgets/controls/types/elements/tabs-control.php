<?php
namespace Elementor\Modules\AtomicWidgets\Controls\Types\Elements;

use Elementor\Modules\AtomicWidgets\Base\Element_Control_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Tabs_Control extends Element_Control_Base {
	public function get_type(): string {
		return 'tabs';
	}

	public function get_props(): array {
		return [];
	}
}
