<?php

namespace Elementor\Core\Isolation;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface Elementor_Counter_Adapter_Interface {
	public function get_count( $key ): ?int;

	public function set_count( $key, $count = 0 ): void;

	public function increment( $key ): void;

	public function is_key_allowed( $key ): bool;
}
