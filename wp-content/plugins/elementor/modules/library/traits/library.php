<?php
namespace Elementor\Modules\Library\Traits;

use Elementor\TemplateLibrary\Source_Local;

/**
 * Elementor Library Trait
 *
 * This trait is used by all Library Documents and Landing Pages.
 *
 * @since 3.1.0
 */
trait Library {
	/**
	 * Print Admin Column Type
	 *
	 * Runs on WordPress' 'manage_{custom post type}_posts_custom_column' hook to modify each row's content.
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function print_admin_column_type() {
		$admin_filter_url = admin_url( Source_Local::ADMIN_MENU_SLUG . '&elementor_library_type=' . $this->get_name() );
		// PHPCS - Not a user input
		printf( '<a href="%s">%s</a>', $admin_filter_url, $this->get_title() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Save document type.
	 *
	 * Set new/updated document type.
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function save_template_type() {
		parent::save_template_type();

		wp_set_object_terms( $this->post->ID, $this->get_name(), Source_Local::TAXONOMY_TYPE_SLUG );
	}
}
