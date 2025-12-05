<?php
namespace Elementor\Modules\AtomicWidgets\Controls\Types;

use Elementor\Modules\AtomicWidgets\Base\Atomic_Control_Base;
use Elementor\Modules\AtomicWidgets\Image\Image_Sizes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Image_Control extends Atomic_Control_Base {
	private string $show_mode = 'all';

	public function set_show_mode( string $show_mode ): self {
		$this->show_mode = $show_mode;

		return $this;
	}

	public function get_type(): string {
		return 'image';
	}

	public function get_props(): array {
		return [
			'sizes' => Image_Sizes::get_all(),
			'showMode' => $this->show_mode,
		];
	}
}
