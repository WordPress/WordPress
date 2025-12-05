<?php
namespace Elementor\Core\Editor\Loader\Common;

use Elementor\Api;
use Elementor\Core\Debug\Loading_Inspection_Manager;
use Elementor\Core\Settings\Manager as SettingsManager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Modules\Apps\Module as AppsModule;
use Elementor\Core\Common\Modules\EventsManager\Module as EditorEventsModule;
use Elementor\Modules\Home\Module as Home_Module;
use Elementor\Plugin;
use Elementor\Settings;
use Elementor\Shapes;
use Elementor\Tools;
use Elementor\User;
use Elementor\Utils;
use Elementor\Core\Utils\Hints;
use Elementor\Core\Utils\Promotions\Filtered_Promotions_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Editor_Common_Scripts_Settings {
	public static function get() {
		$settings = SettingsManager::get_settings_managers_config();
		// Moved to document since 2.9.0.
		unset( $settings['page'] );

		$document = Plugin::$instance->documents->get_doc_or_auto_save( Plugin::$instance->editor->get_post_id() );
		$kits_manager = Plugin::$instance->kits_manager;

		$page_title_selector = $kits_manager->get_current_settings( 'page_title_selector' );
		$top_bar_connect_app = Plugin::$instance->common->get_component( 'connect' )->get_app( 'activate' ) ?? Plugin::$instance->common->get_component( 'connect' )->get_app( 'library' );

		$page_title_selector .= ', .elementor-page-title .elementor-heading-title';

		$client_env = [
			'initial_document' => $document->get_config(),
			'version' => ELEMENTOR_VERSION,
			'home_url' => home_url(),
			'admin_settings_url' => admin_url( 'admin.php?page=' . Home_Module::get_elementor_settings_page_id() ),
			'admin_tools_url' => admin_url( 'admin.php?page=' . Tools::PAGE_ID ),
			'admin_apps_url' => admin_url( 'admin.php?page=' . AppsModule::PAGE_ID ),
			'autosave_interval' => AUTOSAVE_INTERVAL,
			'tabs' => Plugin::$instance->controls_manager->get_tabs(),
			'controls' => Plugin::$instance->controls_manager->get_controls_data(),
			'elements' => Plugin::$instance->elements_manager->get_element_types_config(),
			'globals' => [
				'defaults_enabled' => [
					'colors' => $kits_manager->is_custom_colors_enabled(),
					'typography' => $kits_manager->is_custom_typography_enabled(),
				],
			],
			'icons' => [
				'libraries' => Icons_Manager::get_icon_manager_tabs_config(),
				'goProURL' => 'https://go.elementor.com/go-pro-icon-library/',
			],
			'fa4_to_fa5_mapping_url' => ELEMENTOR_ASSETS_URL . 'lib/font-awesome/migration/mapping.js',
			'settings' => $settings,
			'wp_editor' => static::get_wp_editor_config(),
			'settings_page_link' => Settings::get_url(),
			'tools_page_link' => Tools::get_url(),
			'tools_page_nonce' => wp_create_nonce( 'tools-page-from-editor' ),
			'elementor_site' => 'https://go.elementor.com/about-elementor/',
			'docs_elementor_site' => 'https://go.elementor.com/docs/',
			'help_the_content_url' => 'https://go.elementor.com/the-content-missing/',
			'help_flexbox_bc_url' => 'https://go.elementor.com/flexbox-layout-bc/',
			'elementPromotionURL' => 'https://go.elementor.com/go-pro-%s',
			'dynamicPromotionURL' => 'https://go.elementor.com/go-pro-dynamic-tag',
			'additional_shapes' => Shapes::get_additional_shapes_for_config(),
			'user' => [
				'restrictions' => Plugin::$instance->role_manager->get_user_restrictions_array(),
				'is_administrator' => current_user_can( 'manage_options' ),
				'introduction' => User::get_introduction_meta(),
				'dismissed_editor_notices' => User::get_dismissed_editor_notices(),
				'locale' => get_user_locale(),
				'top_bar' => [
					'connect_url' => $top_bar_connect_app->get_admin_url( 'authorize', [
						'utm_source' => 'editor-app',
						'utm_campaign' => 'connect-account',
						'utm_medium' => 'wp-dash',
						'utm_term' => '1.0.0',
						'utm_content' => 'cta-link',
						'source' => 'generic',
						'mode' => 'popup',
					] ),
					'my_elementor_url' => 'https://go.elementor.com/wp-dash-top-bar-account/',
				],
			],
			'preview' => [
				'help_preview_error_url' => 'https://go.elementor.com/preview-not-loaded/',
				'help_preview_http_error_url' => 'https://go.elementor.com/preview-not-loaded/#permissions',
				'help_preview_http_error_500_url' => 'https://go.elementor.com/500-error/',
				'debug_data' => Loading_Inspection_Manager::instance()->run_inspections(),
			],
			'locale' => get_locale(),
			'rich_editing_enabled' => filter_var( get_user_meta( get_current_user_id(), 'rich_editing', true ), FILTER_VALIDATE_BOOLEAN ),
			'page_title_selector' => $page_title_selector,
			'tinymceHasCustomConfig' => class_exists( 'Tinymce_Advanced' ) || class_exists( 'Advanced_Editor_Tools' ),
			'inlineEditing' => Plugin::$instance->widgets_manager->get_inline_editing_config(),
			'dynamicTags' => Plugin::$instance->dynamic_tags->get_config(),
			'ui' => [
				'defaultGenericFonts' => $kits_manager->get_current_settings( 'default_generic_fonts' ),
			],
			// Empty array for BC to avoid errors.
			'i18n' => [],
			// 'responsive' contains the custom breakpoints config introduced in Elementor v3.2.0
			'responsive' => [
				'breakpoints' => Plugin::$instance->breakpoints->get_breakpoints_config(),
				'icons_map' => Plugin::$instance->breakpoints->get_responsive_icons_classes_map(),
			],
			'promotion' => [
				'elements' => Plugin::$instance->editor->promotion->get_elements_promotion(),
				'integration' => [
					'ally-accessibility' => Hints::get_ally_action_data(),
				],
			],
			'editor_events' => EditorEventsModule::get_editor_events_config(),
			'promotions' => [
				'notes' => Filtered_Promotions_Manager::get_filtered_promotion_data(
					[ 'upgrade_url' => 'https://go.elementor.com/go-pro-notes/' ],
					'elementor/panel/notes/custom_promotion',
					'upgrade_url'
				),
			],
			'fontVariableRanges' => Group_Control_Typography::get_font_variable_ranges(),
		];

		if ( Plugin::$instance->experiments->is_feature_active( 'container' ) ) {
			$client_env['elementsPresets'] = Plugin::$instance->editor->get_elements_presets();
		}

		$is_admin_user_without_pro = current_user_can( 'manage_options' ) && ! Utils::has_pro();
		if ( $is_admin_user_without_pro ) {
			$client_env['integrationWidgets'] = array_merge(
				( isset( $client_env['integrationWidgets'] ) && is_array( $client_env['integrationWidgets'] ) ?
				$client_env['integrationWidgets'] :
				[] ), [
					[
						'categories' => '[ "general" ]',
						'icon' => 'eicon-accessibility',
						'name' => 'ally-accessibility',
						'title' => esc_html__( 'Ally Accessibility', 'elementor' ),
						'keywords' => [
							'Accessibility',
							'Usability',
							'Inclusive',
							'Statement',
							'WCAG',
							'Ally',
							'Complaince',
						],
					],
				],
			);
		}

		static::bc_move_document_filters();

		/**
		 * Localize editor settings.
		 *
		 * Filters the editor localized settings.
		 *
		 * @since 1.0.0
		 *
		 * @param array $client_env  Editor configuration.
		 * @param int   $post_id The ID of the current post being edited.
		 */
		$client_env = apply_filters( 'elementor/editor/localize_settings', $client_env );

		if ( $is_admin_user_without_pro ) {
			$client_env = self::ensure_pro_widgets( $client_env );
		}

		if ( ! empty( $client_env['promotionWidgets'] ) && is_array( $client_env['promotionWidgets'] ) ) {
			$client_env['promotionWidgets'] = self::ensure_numeric_keys( $client_env['promotionWidgets'] );
		}

		return $client_env;
	}

	private static function ensure_pro_widgets( array $client_env ) {
		$pro_widgets = Api::get_promotion_widgets();
		if ( ! isset( $client_env['promotionWidgets'] ) ) {
			$client_env['promotionWidgets'] = $pro_widgets;
		} else {
			$client_env['promotionWidgets'] = array_merge( $pro_widgets, $client_env['promotionWidgets'] );
		}
		return $client_env;
	}

	private static function ensure_numeric_keys( array $base_array ) {
		return array_values( $base_array );
	}

	private static function bc_move_document_filters() {
		global $wp_filter;

		$old_tag = 'elementor/editor/localize_settings';
		$new_tag = 'elementor/document/config';

		if ( ! has_filter( $old_tag ) ) {
			return;
		}

		foreach ( $wp_filter[ $old_tag ] as $priority => $filters ) {
			foreach ( $filters as $filter_id => $filter_args ) {
				if ( 2 === $filter_args['accepted_args'] ) {
					remove_filter( $old_tag, $filter_id, $priority );

					add_filter( $new_tag, $filter_args['function'], $priority, 2 );
				}
			}
		}
	}

	/**
	 * Get WordPress editor config.
	 *
	 * Config the default WordPress editor with custom settings for Elementor use.
	 *
	 * @since 1.9.0
	 * @access private
	 */
	private static function get_wp_editor_config() {
		// Remove all TinyMCE plugins.
		remove_all_filters( 'mce_buttons', 10 );
		remove_all_filters( 'mce_external_plugins', 10 );

		if ( ! class_exists( '\_WP_Editors', false ) ) {
			require ABSPATH . WPINC . '/class-wp-editor.php';
		}

		// WordPress 4.8 and higher
		if ( method_exists( '\_WP_Editors', 'print_tinymce_scripts' ) ) {
			\_WP_Editors::print_default_editor_scripts();
			\_WP_Editors::print_tinymce_scripts();
		}
		ob_start();

		wp_editor(
			'%%EDITORCONTENT%%',
			'elementorwpeditor',
			[
				'editor_class' => 'elementor-wp-editor',
				'editor_height' => 250,
				'drag_drop_upload' => true,
			]
		);

		$config = ob_get_clean();

		// Don't call \_WP_Editors methods again
		remove_action( 'admin_print_footer_scripts', [ '_WP_Editors', 'editor_js' ], 50 );
		remove_action( 'admin_print_footer_scripts', [ '_WP_Editors', 'print_default_editor_scripts' ], 45 );

		\_WP_Editors::editor_js();

		return $config;
	}
}
