<?php

namespace Elementor\Modules\Variables\Classes;

use Elementor\Plugin;
use Elementor\Core\Files\CSS\Post as Post_CSS;
use Elementor\Modules\Variables\PropTypes\Font_Variable_Prop_Type;
use Elementor\Modules\Variables\Storage\Repository as Variables_Repository;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Fonts {
	private Variables_Repository $repository;

	public function __construct( Variables_Repository $repository ) {
		$this->repository = $repository;
	}

	public function append_to( Post_CSS $post_css ) {
		if ( ! Plugin::$instance->kits_manager->is_kit( $post_css->get_post_id() ) ) {
			return;
		}

		$list_of_variables = $this->repository->variables();

		foreach ( $list_of_variables as $variable ) {
			if ( Font_Variable_Prop_Type::get_key() !== $variable['type'] ) {
				continue;
			}

			$font_family = sanitize_text_field( $variable['value'] ?? '' );

			if ( empty( $font_family ) ) {
				continue;
			}

			$post_css->add_font( $font_family );
		}

		return $this;
	}
}
