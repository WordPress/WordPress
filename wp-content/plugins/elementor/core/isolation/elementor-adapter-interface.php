<?php

namespace Elementor\Core\Isolation;

interface Elementor_Adapter_Interface {

	public function get_kit_settings();

	public function get_main_post();

	public function is_active_kit_default(): bool;

	public function get_count( $key ): ?int;

	public function set_count( $key, $count = 0 ): void;

	public function increment( $key ): void;

	public function is_key_allowed( $key ): bool;

	public function get_template_type( $template_id ): string;

	public function get_tier(): string;
}
