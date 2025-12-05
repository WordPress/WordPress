<?php

namespace Elementor\Modules\AtomicWidgets\Styles;

use Elementor\Core\Base\Document;
use Elementor\Core\Breakpoints\Breakpoint;
use Elementor\Core\Utils\Collection;
use Elementor\Modules\AtomicWidgets\Memo;
use Elementor\Modules\AtomicWidgets\Cache_Validity;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

const FONTS_KEY_PREFIX = 'elementor_atomic_styles_fonts-';

class Style_Fonts {
	private string $style_key;

	public function __construct( string $style_key ) {
		$this->style_key = $style_key;
	}

	public static function make( string $style_key ) {
		return new static( $style_key );
	}

	public function add( string $font ) {
		$style_fonts = $this->get_fonts();

		if ( ! in_array( $font, $style_fonts, true ) ) {
			$style_fonts[] = $font;

			$this->update_fonts( $style_fonts );
		}
	}

	public function get(): array {
		return $this->get_fonts();
	}

	public function clear() {
		$this->update_fonts( [] );
	}

	private function get_fonts(): array {
		$style_fonts_key = $this->get_key();
		return get_option( $style_fonts_key, [] );
	}

	private function update_fonts( array $fonts ) {
		$style_fonts_key = $this->get_key();
		update_option( $style_fonts_key, $fonts );
	}

	private function get_key(): string {
		return FONTS_KEY_PREFIX . $this->style_key;
	}
}
