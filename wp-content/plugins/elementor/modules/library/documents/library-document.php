<?php
namespace Elementor\Modules\Library\Documents;

use Elementor\Core\Base\Document;
use Elementor\Modules\Library\Traits\Library;
use Elementor\TemplateLibrary\Source_Local;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor library document.
 *
 * Elementor library document handler class is responsible for handling
 * a document of the library type.
 *
 * @since 2.0.0
 */
abstract class Library_Document extends Document {

	// Library Document Trait
	use Library;

	/**
	 * The taxonomy type slug for the library document.
	 */
	const TAXONOMY_TYPE_SLUG = 'elementor_library_type';

	/**
	 * The customization group for Kit Export.
	 */
	const EXPORT_GROUP = 'site-templates';

	/**
	 * Get document properties.
	 *
	 * Retrieve the document properties.
	 *
	 * @since 2.0.0
	 * @access public
	 * @static
	 *
	 * @return array Document properties.
	 */
	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['admin_tab_group'] = 'library';
		$properties['show_in_library'] = true;
		$properties['register_type'] = true;
		$properties['cpt'] = [ Source_Local::CPT ];
		$properties['export_group'] = static::EXPORT_GROUP;

		return $properties;
	}

	/**
	 * Get initial config.
	 *
	 * Retrieve the current element initial configuration.
	 *
	 * Adds more configuration on top of the controls list and the tabs assigned
	 * to the control. This method also adds element name, type, icon and more.
	 *
	 * @since 2.9.0
	 * @access protected
	 *
	 * @return array The initial config.
	 */
	public function get_initial_config() {
		$config = parent::get_initial_config();

		$config['library'] = [
			'save_as_same_type' => true,
		];

		return $config;
	}

	public function get_content( $with_css = false ) {
		return do_shortcode( parent::get_content( $with_css ) );
	}
}
