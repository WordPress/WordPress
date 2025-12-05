<?php

namespace Elementor\Modules\FloatingButtons\Documents;

use Elementor\Core\Base\Document;
use Elementor\Core\DocumentTypes\PageBase;
use Elementor\Modules\FloatingButtons\Module;
use Elementor\Modules\Library\Traits\Library as Library_Trait;
use Elementor\Modules\FloatingButtons\Module as Floating_Buttons_Module;
use Elementor\Modules\PageTemplates\Module as Page_Templates_Module;
use Elementor\Plugin;
use Elementor\TemplateLibrary\Source_Local;
use Elementor\Utils as ElementorUtils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Floating_Buttons extends PageBase {
	use Library_Trait;

	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['support_kit'] = true;
		$properties['support_site_editor'] = false;
		$properties['cpt'] = [ Floating_Buttons_Module::CPT_FLOATING_BUTTONS ];
		$properties['show_navigator'] = false;
		$properties['allow_adding_widgets'] = false;
		$properties['support_page_layout'] = false;
		$properties['library_close_title'] = esc_html__( 'Go To Dashboard', 'elementor' );
		$properties['publish_button_title'] = esc_html__( 'After publishing this widget, you will be able to set it as visible on the entire site in the Admin Table.', 'elementor' );
		$properties['allow_closing_remote_library'] = false;

		return $properties;
	}

	public static function get_floating_element_type( $post_id ) {
		$meta = get_post_meta( $post_id, Floating_Buttons_Module::FLOATING_ELEMENTS_TYPE_META_KEY, true );
		return $meta ? $meta : 'floating-buttons';
	}

	public static function is_editing_existing_floating_buttons_page() {
		$action = ElementorUtils::get_super_global_value( $_GET, 'action' );
		$post_id = ElementorUtils::get_super_global_value( $_GET, 'post' );

		return 'elementor' === $action && static::is_floating_buttons_type_meta_key( $post_id );
	}

	public static function is_creating_floating_buttons_page() {
		$action = ElementorUtils::get_super_global_value( $_POST, 'action' ); //phpcs:ignore WordPress.Security.NonceVerification.Missing
		$post_id = ElementorUtils::get_super_global_value( $_POST, 'editor_post_id' ); //phpcs:ignore WordPress.Security.NonceVerification.Missing

		return 'elementor_ajax' === $action && static::is_floating_buttons_type_meta_key( $post_id );
	}

	public static function is_floating_buttons_type_meta_key( $post_id ) {
		return Module::FLOATING_BUTTONS_DOCUMENT_TYPE === get_post_meta( $post_id, Document::TYPE_META_KEY, true );
	}

	public function print_content() {
		$plugin = \Elementor\Plugin::$instance;

		if ( $plugin->preview->is_preview_mode( $this->get_main_id() ) ) {
			// PHPCS - the method builder_wrapper is safe.
			echo $plugin->preview->builder_wrapper( '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			// PHPCS - the method get_content is safe.
			echo $this->get_content(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	public function get_location() {
		return self::get_property( 'location' );
	}

	public static function get_type() {
		return Floating_Buttons_Module::FLOATING_BUTTONS_DOCUMENT_TYPE;
	}

	public static function register_post_fields_control( $document ) {}

	public static function register_hide_title_control( $document ) {}

	public function get_name() {
		return Floating_Buttons_Module::FLOATING_BUTTONS_DOCUMENT_TYPE;
	}

	public function filter_admin_row_actions( $actions ) {
		unset( $actions['edit'] );
		unset( $actions['inline hide-if-no-js'] );
		$built_with_elementor = parent::filter_admin_row_actions( [] );

		if ( isset( $actions['trash'] ) ) {
			$delete = $actions['trash'];
			unset( $actions['trash'] );
			$actions['trash'] = $delete;
		}

		if ( 'publish' === $this->get_post()->post_status ) {
			$actions = $this->set_as_entire_site( $actions );
		}

		return $built_with_elementor + $actions;
	}

	public static function get_meta_query_for_floating_buttons( string $floating_element_type ): array {
		$meta_query = [
			'relation' => 'AND',
			[
				'key' => '_elementor_conditions',
				'compare' => 'EXISTS',
			],
		];

		if ( 'floating-buttons' === $floating_element_type ) {
			$meta_query[] = [
				'relation' => 'OR',
				[
					'key' => Module::FLOATING_ELEMENTS_TYPE_META_KEY,
					'compare' => 'NOT EXISTS',
				],
				[
					'key' => Module::FLOATING_ELEMENTS_TYPE_META_KEY,
					'value' => 'floating-buttons',
				],
			];
		} else {
			$meta_query[] = [
				'key' => Module::FLOATING_ELEMENTS_TYPE_META_KEY,
				'value' => $floating_element_type,
			];
		}

		return $meta_query;
	}

	/**
	 * Tries to find the post id of the floating element that is set as entire site.
	 * If found, returns the post id, otherwise returns 0.
	 *
	 * @param string $floating_element_type
	 *
	 * @return int
	 */
	public static function get_set_as_entire_site_post_id( string $floating_element_type ): int {
		static $types = [];

		if ( isset( $types[ $floating_element_type ] ) ) {
			return $types[ $floating_element_type ];
		}

		$query = new \WP_Query( [
			'post_type' => Floating_Buttons_Module::CPT_FLOATING_BUTTONS,
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'fields' => 'ids',
			'no_found_rows' => true,
			'update_post_term_cache' => false,
			'meta_query' => static::get_meta_query_for_floating_buttons( $floating_element_type ),
		] );

		foreach ( $query->get_posts() as $post_id ) {
			$conditions = get_post_meta( $post_id, '_elementor_conditions', true );

			if ( ! $conditions ) {
				continue;
			}

			if ( in_array( 'include/general', $conditions ) ) {
				$types[ $floating_element_type ] = $post_id;
				return $post_id;
			}
		}

		return 0;
	}

	public function set_as_entire_site( $actions ) {
		$floating_element_type = static::get_floating_element_type( $this->get_main_id() );
		$current_set_as_entire_site_post_id = static::get_set_as_entire_site_post_id( $floating_element_type );

		if ( $current_set_as_entire_site_post_id === $this->get_main_id() ) {
			$actions['set_as_entire_site'] = sprintf(
				'<a style="color:red;" href="?post=%s&action=remove_from_entire_site&_wpnonce=%s">%s</a>',
				$this->get_post()->ID,
				wp_create_nonce( 'remove_from_entire_site_' . $this->get_post()->ID ),
				esc_html__( 'Remove From Entire Site', 'elementor' )
			);
		} else {
			$actions['set_as_entire_site'] = sprintf(
				'<a href="?post=%s&action=set_as_entire_site&_wpnonce=%s">%s</a>',
				$this->get_post()->ID,
				wp_create_nonce( 'set_as_entire_site_' . $this->get_post()->ID ),
				esc_html__( 'Set as Entire Site', 'elementor' )
			);
		}

		return $actions;
	}

	public static function get_title() {
		return esc_html__( 'Floating Element', 'elementor' );
	}

	public static function get_plural_title() {
		return esc_html__( 'Floating Elements', 'elementor' );
	}

	public static function get_create_url() {
		return parent::get_create_url() . '#library';
	}

	public function save( $data ) {
		if ( empty( $data['settings']['template'] ) ) {
			$data['settings']['template'] = Page_Templates_Module::TEMPLATE_CANVAS;
		}

		return parent::save( $data );
	}

	public function admin_columns_content( $column_name ) {
		if ( 'elementor_library_type' === $column_name ) {
			$admin_filter_url = admin_url( Source_Local::ADMIN_MENU_SLUG . '&elementor_library_type=' . $this->get_name() );
			$meta = static::get_floating_element_type( $this->get_main_id() );
			printf( '<a href="%s">%s</a>', $admin_filter_url, Module::get_floating_elements_types()[ $meta ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	public function get_edit_url() {
		return add_query_arg(
			[
				'post' => $this->get_main_id(),
				'action' => 'elementor',
				'floating_element' => get_post_meta(
					$this->get_main_id(),
					Module::FLOATING_ELEMENTS_TYPE_META_KEY,
					true
				),
			],
			admin_url( 'post.php' )
		);
	}

	protected function get_remote_library_config() {
		$config = [
			'type' => 'floating_button',
			'default_route' => 'templates/floating-buttons',
			'autoImportSettings' => true,
		];

		return array_replace_recursive( parent::get_remote_library_config(), $config );
	}
}
