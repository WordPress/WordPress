<?php
namespace Elementor\Core\DocumentTypes;

use Elementor\Core\Base\Document;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Page extends PageBase {

	const URL_TYPE = 'site_settings';

	const SITE_IDENTITY_TAB = 'settings-site-identity';

	/**
	 * Get Properties
	 *
	 * Return the page document configuration properties.
	 *
	 * @access public
	 * @static
	 *
	 * @return array
	 */
	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['cpt'] = [ 'page' ];
		$properties['support_kit'] = true;

		return $properties;
	}

	/**
	 * Get Type
	 *
	 * Return the page document type.
	 *
	 * @return string
	 */
	public static function get_type() {
		return 'wp-page';
	}

	/**
	 * Get Title
	 *
	 * Return the page document title.
	 *
	 * @access public
	 * @static
	 *
	 * @return string
	 */
	public static function get_title() {
		return esc_html__( 'Page', 'elementor' );
	}

	/**
	 * Get Plural Title
	 *
	 * Return the page document plural title.
	 *
	 * @access public
	 * @static
	 *
	 * @return string
	 */
	public static function get_plural_title() {
		return esc_html__( 'Pages', 'elementor' );
	}

	public static function get_site_settings_url_config( $active_tab_id = null ) {
		$existing_elementor_page = self::get_elementor_page();
		$site_settings_url = $existing_elementor_page
			? self::get_elementor_edit_url( $existing_elementor_page->ID, [ 'active-tab' => $active_tab_id ] )
			: self::get_create_new_editor_page_url( $active_tab_id );

		return [
			'new_page' => empty( $existing_elementor_page ),
			'url' => $site_settings_url,
			'type' => static::URL_TYPE,
		];
	}

	public static function get_create_new_editor_page_url( $active_tab = null ): string {
		$active_kit_id = Plugin::$instance->kits_manager->get_active_id();
		$args = [];

		if ( ! empty( $active_kit_id ) ) {
			$args['active-document'] = $active_kit_id;
		}

		if ( $active_tab ) {
			$args['active-tab'] = $active_tab;
		}

		return add_query_arg( $args, Plugin::$instance->documents->get_create_new_post_url( 'page' ) );
	}

	private static function get_elementor_edit_url( int $post_id, $args = [] ): string {
		$page = new self( [ 'post_id' => $post_id ] );
		$url = add_query_arg( $args, $page->get_edit_url() );

		if ( Plugin::$instance->kits_manager->get_active_id() ) {
			return $url . '#e:run:panel/global/open';
		}

		return $url;
	}

	public static function get_elementor_page() {
		return get_pages( [
			'post_status' => [ 'publish', 'draft' ],
			'meta_key' => Document::BUILT_WITH_ELEMENTOR_META_KEY,
			'sort_order' => 'asc',
			'sort_column' => 'post_date',
			'number' => 1,
		] )[0] ?? null;
	}
}
