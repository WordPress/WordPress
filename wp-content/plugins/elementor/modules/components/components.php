<?php

namespace Elementor\Modules\Components;

use Elementor\Core\Utils\Collection;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Components {
	private Collection $components;

	public static function make( array $components = [] ) {
		return new static( $components );
	}

	private function __construct( array $components = [] ) {
		$this->components = Collection::make( $components );
	}

	public function get_components() {
		return $this->components;
	}
}
