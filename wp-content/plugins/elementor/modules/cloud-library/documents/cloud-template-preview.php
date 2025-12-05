<?php
namespace Elementor\Modules\CloudLibrary\Documents;

use Elementor\Core\Base\Document;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor preview library document.
 *
 * @since 3.29.0
 */
class Cloud_Template_Preview extends Document {

	const TYPE = 'cloud-template-preview';

	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['admin_tab_group'] = '';
		$properties['has_elements'] = true;
		$properties['is_editable'] = true;
		$properties['show_in_library'] = false;
		$properties['show_on_admin_bar'] = false;
		$properties['show_in_finder'] = false;
		$properties['register_type'] = true;
		$properties['support_conditions'] = false;
		$properties['support_page_layout'] = false;

		return $properties;
	}

	public static function get_type(): string {
		return self::TYPE;
	}

	public static function get_title(): string {
		return esc_html__( 'Cloud Template Preview', 'elementor' );
	}

	public static function get_plural_title(): string {
		return esc_html__( 'Cloud Template Previews', 'elementor' );
	}

	public function get_content( $with_css = false ) {
		return do_shortcode( parent::get_content( $with_css ) );
	}
}
