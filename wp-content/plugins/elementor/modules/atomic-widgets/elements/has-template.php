<?php

namespace Elementor\Modules\AtomicWidgets\Elements;

use Elementor\Modules\AtomicWidgets\TemplateRenderer\Template_Renderer;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * @mixin Has_Atomic_Base
 */
trait Has_Template {

	public function get_initial_config() {
		$config = parent::get_initial_config();

		$config['twig_main_template'] = $this->get_main_template();
		$config['twig_templates'] = $this->get_templates_contents();

		return $config;
	}

	protected function render() {
		try {
			$renderer = Template_Renderer::instance();

			foreach ( $this->get_templates() as $name => $path ) {
				if ( $renderer->is_registered( $name ) ) {
					continue;
				}

				$renderer->register( $name, $path );
			}

			$context = [
				'id' => $this->get_id(),
				'type' => $this->get_name(),
				'settings' => $this->get_atomic_settings(),
				'base_styles' => $this->get_base_styles_dictionary(),
			];

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $renderer->render( $this->get_main_template(), $context );
		} catch ( \Exception $e ) {
			if ( Utils::is_elementor_debug() ) {
				throw $e;
			}
		}
	}

	protected function get_templates_contents() {
		return array_map(
			fn ( $path ) => Utils::file_get_contents( $path ),
			$this->get_templates()
		);
	}

	protected function get_main_template() {
		$templates = $this->get_templates();

		if ( count( $templates ) > 1 ) {
			Utils::safe_throw( 'When having more than one template, you should override this method to return the main template.' );

			return null;
		}

		foreach ( $templates as $key => $path ) {
			// Returns first key in the array.
			return $key;
		}

		return null;
	}

	abstract protected function get_templates(): array;
}
