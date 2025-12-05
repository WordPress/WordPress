<?php

namespace Elementor\Modules\GlobalClasses;

use Elementor\Core\Utils\Collection;

class Global_Classes {
	private Collection $items;
	private Collection $order;

	public static function make( array $items = [], array $order = [] ) {
		return new static( $items, $order );
	}

	private function __construct( array $data = [], array $order = [] ) {
		$this->items = Collection::make( $data );
		$this->order = Collection::make( $order );
	}

	public function get_items() {
		return $this->items;
	}

	public function get_order() {
		return $this->order;
	}

	public function get() {
		return [
			'items' => $this->get_items()->all(),
			'order' => $this->get_order()->all(),
		];
	}
}
