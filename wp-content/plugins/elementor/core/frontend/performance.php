<?php
namespace Elementor\Core\Frontend;

use Elementor\Plugin;

class Performance {

	private static $use_style_controls = false;

	private static $is_frontend = null;

	public static function set_use_style_controls( bool $is_use ): void {
		static::$use_style_controls = $is_use;
	}

	public static function is_use_style_controls(): bool {
		return static::$use_style_controls;
	}

	public static function should_optimize_controls() {
		if ( null === static::$is_frontend ) {
			static::$is_frontend = (
				! is_admin()
				&& ! Plugin::$instance->preview->is_preview_mode()
			);
		}

		return static::$is_frontend;
	}
}
