<?php

namespace Elementor\Modules\LinkInBio\Widgets;

use Elementor\Modules\LinkInBio\Base\Widget_Link_In_Bio_Base;
use Elementor\Modules\LinkInBio\Classes\Render\Core_Render;
use Elementor\Modules\LinkInBio\Module as ConversionCenterModule;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Link in Bio widget.
 *
 * Elementor widget that displays an image, a bio, up to 4 CTA links and up to 5 icons.
 *
 * @since 3.23.0
 */
class Link_In_Bio extends Widget_Link_In_Bio_Base {

	public function get_name(): string {
		return 'link-in-bio';
	}

	public function get_title(): string {
		return esc_html__( 'Minimalist', 'elementor' );
	}
}
