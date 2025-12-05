<?php

namespace Elementor\Core\Isolation;

use Elementor\Core\Common\Modules\Connect\Module as ConnectModule;
use Elementor\Plugin;
use Elementor\Modules\ElementorCounter\Module as Elementor_Counter_Module;
use Elementor\TemplateLibrary\Source_Local;
use Elementor\Utils;

class Elementor_Adapter implements Elementor_Adapter_Interface {

	public function get_kit_settings() {
		return Plugin::$instance->kits_manager->get_kit_for_frontend()->get_settings();
	}

	public function get_main_post() {
		return Plugin::$instance->kits_manager->get_kit_for_frontend()->get_main_post();
	}

	public function is_active_kit_default(): bool {
		$kit_id = Plugin::$instance->kits_manager->get_active_id();

		if ( false === $kit_id || null === $kit_id ) {
			return false;
		}

		return esc_html__( 'Default Kit', 'elementor' ) === get_post( $kit_id )->post_title;
	}

	public function get_count( $key ): ?int {
		return Elementor_Counter_Module::instance()->get_count( $key );
	}

	public function set_count( $key, $count = 0 ): void {
		Elementor_Counter_Module::instance()->set_count( $key, $count );
	}

	public function increment( $key ): void {
		Elementor_Counter_Module::instance()->increment( $key );
	}

	public function is_key_allowed( $key ): bool {
		return Elementor_Counter_Module::instance()->is_key_allowed( $key );
	}

	public function get_template_type( $template_id ): string {
		return Source_Local::get_template_type( $template_id );
	}

	public function get_tier(): string {
		return Utils::has_pro() ? ConnectModule::ACCESS_TIER_PRO_LEGACY : ConnectModule::ACCESS_TIER_FREE;
	}
}
