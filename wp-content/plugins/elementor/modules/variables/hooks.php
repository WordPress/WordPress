<?php

namespace Elementor\Modules\Variables;

use Elementor\Modules\Variables\Classes\Variable_Types_Registry;
use Elementor\Modules\Variables\PropTypes\Color_Variable_Prop_Type;
use Elementor\Modules\Variables\PropTypes\Font_Variable_Prop_Type;
use Elementor\Plugin;
use Elementor\Core\Files\CSS\Post as Post_CSS;
use Elementor\Modules\Variables\Classes\CSS_Renderer as Variables_CSS_Renderer;
use Elementor\Modules\Variables\Classes\Fonts;
use Elementor\Modules\Variables\Classes\Rest_Api as Variables_API;
use Elementor\Modules\Variables\Storage\Repository as Variables_Repository;
use Elementor\Modules\Variables\Classes\Style_Schema;
use Elementor\Modules\Variables\Classes\Style_Transformers;
use Elementor\Modules\Variables\Classes\Variables;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Hooks {
	const PACKAGES = [
		'editor-variables',
	];

	public function register() {
		$this->register_styles_transformers()
			->register_packages()
			->filter_for_style_schema()
			->register_css_renderer()
			->register_fonts()
			->register_api_endpoints()
			->register_variable_types();

		return $this;
	}

	private function register_variable_types() {
		add_action( 'elementor/variables/register', function ( Variable_Types_Registry $registry ) {
			$registry->register( Color_Variable_Prop_Type::get_key(), new Color_Variable_Prop_Type() );
			$registry->register( Font_Variable_Prop_Type::get_key(), new Font_Variable_Prop_Type() );
		} );

		return $this;
	}

	private function register_packages() {
		add_filter( 'elementor/editor/v2/packages', function ( $packages ) {
			return array_merge( $packages, self::PACKAGES );
		} );

		return $this;
	}

	private function register_styles_transformers() {
		add_action( 'elementor/atomic-widgets/styles/transformers/register', function ( $registry ) {
			Variables::init( $this->variables_repository() );
			( new Style_Transformers() )->append_to( $registry );
		} );

		return $this;
	}

	private function filter_for_style_schema() {
		add_filter( 'elementor/atomic-widgets/styles/schema', function ( array $schema ) {
			return ( new Style_Schema() )->augment( $schema );
		} );

		return $this;
	}

	private function css_renderer() {
		return new Variables_CSS_Renderer( $this->variables_repository() );
	}

	private function register_css_renderer() {
		add_action( 'elementor/css-file/post/parse', function ( Post_CSS $post_css ) {
			if ( ! Plugin::$instance->kits_manager->is_kit( $post_css->get_post_id() ) ) {
				return;
			}

			$post_css->get_stylesheet()->add_raw_css(
				$this->css_renderer()->raw_css()
			);
		} );

		return $this;
	}

	private function fonts() {
		return new Fonts( $this->variables_repository() );
	}

	private function register_fonts() {
		add_action( 'elementor/css-file/post/parse', function ( $post_css ) {
			$this->fonts()->append_to( $post_css );
		} );

		return $this;
	}

	private function rest_api() {
		return new Variables_API( $this->variables_repository() );
	}

	private function register_api_endpoints() {
		add_action( 'rest_api_init', function () {
			$this->rest_api()->register_routes();
		} );

		return $this;
	}

	private function variables_repository() {
		return new Variables_Repository(
			Plugin::$instance->kits_manager->get_active_kit()
		);
	}
}
