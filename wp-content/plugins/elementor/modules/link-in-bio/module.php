<?php

namespace Elementor\Modules\LinkInBio;

use Elementor\Core\Base\Module as BaseModule;
use Elementor\Core\Experiments\Manager;
use Elementor\Plugin;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends BaseModule {

	const WIDGET_HAS_CUSTOM_BREAKPOINTS = true;

	public function get_name(): string {
		return 'link-in-bio';
	}

	public function get_widgets(): array {
		return [
			'Link_In_Bio',
		];
	}

	public function __construct() {
		parent::__construct();

		add_action( 'elementor/frontend/after_register_styles', [ $this, 'register_styles' ] );
	}

	/**
	 * Register styles.
	 *
	 * At build time, Elementor compiles `/modules/link-in-bio/assets/scss/widgets/*.scss`
	 * to `/assets/css/widget-*.min.css`.
	 *
	 * @return void
	 */
	public function register_styles() {
		$direction_suffix = is_rtl() ? '-rtl' : '';
		$widget_styles = $this->get_widgets_style_list();
		$has_custom_breakpoints = Plugin::$instance->breakpoints->has_custom_breakpoints();

		foreach ( $widget_styles as $widget_style_name => $widget_has_responsive_style ) {
			$should_load_responsive_css = $widget_has_responsive_style ? $has_custom_breakpoints : false;

			wp_register_style(
				$widget_style_name,
				$this->get_frontend_file_url( "{$widget_style_name}{$direction_suffix}.min.css", $should_load_responsive_css ),
				[ 'elementor-frontend' ],
				$should_load_responsive_css ? null : ELEMENTOR_VERSION
			);
		}
	}

	private function get_widgets_style_list(): array {
		return [
			'widget-link-in-bio' => self::WIDGET_HAS_CUSTOM_BREAKPOINTS, // TODO: Remove in v3.27.0 [ED-15717]
			'widget-link-in-bio-base' => self::WIDGET_HAS_CUSTOM_BREAKPOINTS,
			'widget-link-in-bio-var-2' => ! self::WIDGET_HAS_CUSTOM_BREAKPOINTS,
			'widget-link-in-bio-var-3' => ! self::WIDGET_HAS_CUSTOM_BREAKPOINTS,
			'widget-link-in-bio-var-4' => ! self::WIDGET_HAS_CUSTOM_BREAKPOINTS,
			'widget-link-in-bio-var-5' => ! self::WIDGET_HAS_CUSTOM_BREAKPOINTS,
			'widget-link-in-bio-var-7' => ! self::WIDGET_HAS_CUSTOM_BREAKPOINTS,
		];
	}
}
