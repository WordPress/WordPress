<?php

namespace Elementor\Modules\AtomicOptIn;

use Elementor\Core\Isolation\Elementor_Adapter;
use Elementor\Core\Isolation\Elementor_Adapter_Interface;
use Elementor\Modules\ElementorCounter\Module as Elementor_Counter;
use Elementor\Utils;

class WelcomeScreen {
	private Elementor_Adapter_Interface $elementor_adapter;

	public function __construct() {
		$this->elementor_adapter = new Elementor_Adapter();
	}

	public function init() {
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'maybe_enqueue_welcome_popover' ] );
	}

	public function maybe_enqueue_welcome_popover(): void {
		if ( $this->is_first_or_second_editor_visit() ) {
			return;
		}

		if ( $this->has_welcome_popover_been_displayed() ) {
			return;
		}

		$this->enqueue_scripts();
		$this->set_welcome_popover_as_displayed();
	}

	private function is_first_or_second_editor_visit(): bool {
		if ( ! $this->elementor_adapter ) {
			return false;
		}

		$editor_visit_count = $this->elementor_adapter->get_count( Elementor_Counter::EDITOR_COUNTER_KEY );
		return $editor_visit_count < 3;
	}

	private function has_welcome_popover_been_displayed(): bool {
		return get_user_meta( $this->get_current_user_id(), Module::WELCOME_POPOVER_DISPLAYED_OPTION, true );
	}

	private function set_welcome_popover_as_displayed(): void {
		update_user_meta( $this->get_current_user_id(), Module::WELCOME_POPOVER_DISPLAYED_OPTION, true );
	}

	private function enqueue_scripts() {
		$min_suffix = Utils::is_script_debug() ? '' : '.min';

		wp_enqueue_script(
			Module::MODULE_NAME . '-welcome',
			ELEMENTOR_ASSETS_URL . 'js/editor-v4-welcome-opt-in' . $min_suffix . '.js',
			[
				'react',
				'react-dom',
				'elementor-common',
				'elementor-v2-ui',
			],
			ELEMENTOR_VERSION,
			true
		);

		wp_set_script_translations( Module::MODULE_NAME . '-welcome', 'elementor' );
	}


	private function get_current_user_id(): int {
		$current_user = wp_get_current_user();
		return $current_user->ID ?? 0;
	}
}
