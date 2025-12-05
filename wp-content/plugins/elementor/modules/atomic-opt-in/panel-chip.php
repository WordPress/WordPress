<?php

namespace Elementor\Modules\AtomicOptIn;

use Elementor\Utils;

class PanelChip {
	public function init() {
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	public function enqueue_scripts() {
		$min_suffix = Utils::is_script_debug() ? '' : '.min';

		wp_enqueue_script(
			'editor-v4-opt-in-alphachip',
			ELEMENTOR_ASSETS_URL . 'js/editor-v4-opt-in-alphachip' . $min_suffix . '.js',
			[
				'react',
				'react-dom',
				'elementor-common',
				'elementor-v2-ui',
			],
			ELEMENTOR_VERSION,
			true
		);

		wp_set_script_translations( 'editor-v4-opt-in-alphachip', 'elementor' );
	}
}
