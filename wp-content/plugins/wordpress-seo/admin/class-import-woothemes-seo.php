<?php
/**
 * @package WPSEO\Admin\Import\External
 */

/**
 * Class WPSEO_Import_WooThemes_SEO
 *
 * Class with functionality to import Yoast SEO settings from WooThemes SEO
 */
class WPSEO_Import_WooThemes_SEO extends WPSEO_Import_External {

	/**
	 * Class constructor
	 */
	public function __construct() {
		parent::__construct();

		$this->import_home();
		$this->import_option( 'seo_woo_single_layout', 'post' );
		$this->import_option( 'seo_woo_page_layout', 'page' );
		$this->import_archive_option();
		$this->import_custom_values( 'seo_woo_meta_home_desc', 'metadesc-home-wpseo' );
		$this->import_custom_values( 'seo_woo_meta_home_key', 'metakey-home-wpseo' );
		$this->import_metas();

		update_option( 'wpseo_titles', $this->options );

		$this->set_msg( __( 'WooThemes SEO framework settings &amp; data successfully imported.', 'wordpress-seo' ) );
	}

	/**
	 * Holds the WPSEO Title Options
	 *
	 * @var array
	 */
	private $options;

	/**
	 * Import options.
	 *
	 * @param string $option
	 * @param string $post_type
	 */
	private function import_option( $option, $post_type ) {
		switch ( get_option( $option ) ) {
			case 'a':
				$this->options[ 'title-' . $post_type ] = '%%title%% %%sep%% %%sitename%%';
				break;
			case 'b':
				$this->options[ 'title-' . $post_type ] = '%%title%%';
				break;
			case 'c':
				$this->options[ 'title-' . $post_type ] = '%%sitename%% %%sep%% %%title%%';
				break;
			case 'd':
				$this->options[ 'title-' . $post_type ] = '%%title%% %%sep%% %%sitedesc%%';
				break;
			case 'e':
				$this->options[ 'title-' . $post_type ] = '%%sitename%% %%sep%% %%title%% %%sep%% %%sitedesc%%';
				break;
		}
		$this->perhaps_delete( $option );
	}

	/**
	 * Import the archive layout for all taxonomies
	 */
	private function import_archive_option() {
		$reinstate_replace = false;
		if ( $this->replace ) {
			$this->replace     = false;
			$reinstate_replace = true;
		}
		$taxonomies = get_taxonomies( array( 'public' => true ), 'names' );
		if ( is_array( $taxonomies ) && $taxonomies !== array() ) {
			foreach ( $taxonomies as $tax ) {
				$this->import_option( 'seo_woo_archive_layout', 'tax-' . $tax );
			}
		}
		if ( $reinstate_replace ) {
			$this->replace = true;
			$this->perhaps_delete( 'seo_woo_archive_layout' );
		}
	}

	/**
	 * Import custom descriptions and meta keys
	 *
	 * @param string $option
	 * @param string $key
	 */
	private function import_custom_values( $option, $key ) {
		// Import the custom homepage description.
		if ( 'c' == get_option( $option ) ) {
			$this->options[ $key ] = get_option( $option . '_custom' );
		}
		$this->perhaps_delete( $option );
		$this->perhaps_delete( $option . '_custom' );
	}

	/**
	 * Imports the WooThemes SEO homepage settings
	 */
	private function import_home() {
		switch ( get_option( 'seo_woo_home_layout' ) ) {
			case 'a':
				$this->options['title-home-wpseo'] = '%%sitename%% %%sep%% %%sitedesc%%';
				break;
			case 'b':
				$this->options['title-home-wpseo'] = '%%sitename%% ' . get_option( 'seo_woo_paged_var' ) . ' %%pagenum%%';
				break;
			case 'c':
				$this->options['title-home-wpseo'] = '%%sitedesc%%';
				break;
		}
		$this->perhaps_delete( 'seo_woo_home_layout' );
	}

	/**
	 * Import meta values if they're applicable
	 */
	private function import_metas() {
		WPSEO_Meta::replace_meta( 'seo_follow', WPSEO_Meta::$meta_prefix . 'meta-robots-nofollow', $this->replace );
		WPSEO_Meta::replace_meta( 'seo_noindex', WPSEO_Meta::$meta_prefix . 'meta-robots-noindex', $this->replace );

		// If WooSEO is set to use the Woo titles, import those.
		if ( 'true' == get_option( 'seo_woo_wp_title' ) ) {
			WPSEO_Meta::replace_meta( 'seo_title', WPSEO_Meta::$meta_prefix . 'title', $this->replace );
		}

		// If WooSEO is set to use the Woo meta descriptions, import those.
		if ( 'b' == get_option( 'seo_woo_meta_single_desc' ) ) {
			WPSEO_Meta::replace_meta( 'seo_description', WPSEO_Meta::$meta_prefix . 'metadesc', $this->replace );
		}

		// If WooSEO is set to use the Woo meta keywords, import those.
		if ( 'b' == get_option( 'seo_woo_meta_single_key' ) ) {
			WPSEO_Meta::replace_meta( 'seo_keywords', WPSEO_Meta::$meta_prefix . 'metakeywords', $this->replace );
		}

		foreach ( array( 'seo_woo_wp_title', 'seo_woo_meta_single_desc', 'seo_woo_meta_single_key' ) as $option ) {
			$this->perhaps_delete( $option );
		}
	}
}
