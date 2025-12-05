<?php
namespace Elementor\Modules\NestedAccordion;

use Elementor\Plugin;
use Elementor\Core\Base\Module as BaseModule;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends BaseModule {

	public static function is_active() {
		return Plugin::$instance->experiments->is_feature_active( 'nested-elements', true );
	}

	public function get_name() {
		return 'nested-accordion';
	}

	public function __construct() {
		parent::__construct();

		add_action( 'elementor/frontend/after_register_styles', [ $this, 'register_styles' ] );

		add_action( 'elementor/editor/before_enqueue_scripts', function () {
			wp_enqueue_script( $this->get_name(), $this->get_js_assets_url( $this->get_name() ), [
				'nested-elements',
			], ELEMENTOR_VERSION, true );
		} );
	}

	/**
	 * Register styles.
	 *
	 * At build time, Elementor compiles `/modules/nested-accordion/assets/scss/frontend.scss`
	 * to `/assets/css/widget-nested-accordion.min.css`.
	 *
	 * @return void
	 */
	public function register_styles() {
		wp_register_style(
			'widget-nested-accordion',
			$this->get_css_assets_url( 'widget-nested-accordion', null, true, true ),
			[ 'elementor-frontend' ],
			ELEMENTOR_VERSION
		);
	}
}
