<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Metabox
 */

/**
 * Handles all things with the metabox in combination with the WordPress editor.
 */
class WPSEO_Metabox_Editor {

	/**
	 * Registers hooks to WordPress.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	public function register_hooks() {
		// For the Classic editor.
		add_filter( 'mce_css', [ $this, 'add_css_inside_editor' ] );
		// For the Block/Gutenberg editor.
		// See https://github.com/danielbachhuber/gutenberg-migration-guide/blob/master/filter-mce-css.md.
		add_action( 'enqueue_block_editor_assets', [ $this, 'add_editor_styles' ] );

		add_filter( 'tiny_mce_before_init', [ $this, 'add_custom_element' ] );
	}

	/**
	 * Adds our inside the editor CSS file to the list of CSS files to be loaded inside the editor.
	 *
	 * @param string $css_files The CSS files that WordPress wants to load inside the editor.
	 * @return string The CSS files WordPress wants to load and our CSS file.
	 */
	public function add_css_inside_editor( $css_files ) {
		$asset_manager = new WPSEO_Admin_Asset_Manager();
		$styles        = $asset_manager->special_styles();
		$inside_editor = $styles['inside-editor'];

		$asset_location = new WPSEO_Admin_Asset_SEO_Location( WPSEO_FILE );
		$url            = $asset_location->get_url( $inside_editor, WPSEO_Admin_Asset::TYPE_CSS );

		if ( $css_files === '' ) {
			$css_files = $url;
		}
		else {
			$css_files .= ',' . $url;
		}

		return $css_files;
	}

	/**
	 * Enqueues the CSS to use in the TinyMCE editor.
	 *
	 * @return void
	 */
	public function add_editor_styles() {
		$asset_manager = new WPSEO_Admin_Asset_Manager();
		$asset_manager->enqueue_style( 'inside-editor' );
	}

	/**
	 * Adds a custom element to the tinyMCE editor that we need for marking the content.
	 *
	 * @param array $tinymce_config The tinyMCE config as configured by WordPress.
	 *
	 * @return array The new tinyMCE config with our added custom elements.
	 */
	public function add_custom_element( $tinymce_config ) {
		if ( ! empty( $tinymce_config['custom_elements'] ) ) {
			$custom_elements = $tinymce_config['custom_elements'];

			$custom_elements .= ',~yoastmark';
		}
		else {
			$custom_elements = '~yoastmark';
		}

		$tinymce_config['custom_elements'] = $custom_elements;

		return $tinymce_config;
	}
}
