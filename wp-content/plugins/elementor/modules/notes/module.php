<?php
namespace Elementor\Modules\Notes;

use Elementor\Core\Base\Module as BaseModule;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends BaseModule {

	public function get_name() {
		return 'notes';
	}

	/**
	 * Enqueue the module scripts.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			'elementor-notes',
			$this->get_js_assets_url( 'notes' ),
			[ 'elementor-editor' ],
			ELEMENTOR_VERSION,
			true
		);

		wp_set_script_translations( 'elementor-notes', 'elementor' );
	}

	/**
	 * Enqueue the module styles.
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			'elementor-notes',
			$this->get_css_assets_url( 'modules/notes/editor' ),
			[ 'elementor-editor' ],
			ELEMENTOR_VERSION
		);
	}

	/**
	 * @return bool
	 */
	public static function is_active() {
		return ! Utils::has_pro();
	}

	/**
	 * Initialize the Notes module.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();

		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'enqueue_styles' ] );
	}
}
