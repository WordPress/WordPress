<?php
namespace Elementor\Modules\ElementManager;

use Elementor\Core\Utils\Promotions\Filtered_Promotions_Manager;
use Elementor\Modules\Usage\Module as Usage_Module;
use Elementor\Api;
use Elementor\Plugin;
use Elementor\User;
use Elementor\Utils;
use Elementor\Core\Utils\Promotions\Validate_Promotion;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Ajax {

	const ELEMENT_MANAGER_PROMOTION_URL = 'https://go.elementor.com/go-pro-element-manager/';

	const FREE_TO_PRO_PERMISSIONS_PROMOTION_URL = 'https://go.elementor.com/go-pro-element-manager-permissions/';

	const PRO_TO_ADVANCED_PERMISSIONS_PROMOTION_URL = 'https://go.elementor.com/go-pro-advanced-element-manager-permissions/';

	public function register_endpoints() {
		add_action( 'wp_ajax_elementor_element_manager_get_admin_app_data', [ $this, 'ajax_get_admin_page_data' ] );
		add_action( 'wp_ajax_elementor_element_manager_save_disabled_elements', [ $this, 'ajax_save_disabled_elements' ] );
		add_action( 'wp_ajax_elementor_element_manager_get_widgets_usage', [ $this, 'ajax_get_widgets_usage' ] );
	}

	public function ajax_get_admin_page_data() {
		$this->verify_permission();
		$this->force_enabled_all_elements();

		$widgets = [];
		$plugins = [];

		foreach ( Plugin::$instance->widgets_manager->get_widget_types() as $widget ) {
			$widget_title = sanitize_user( $widget->get_title() );
			if ( empty( $widget_title ) || ! $widget->show_in_panel() ) {
				continue;
			}

			$plugin_name = $this->get_plugin_name_from_widget_instance( $widget );

			if ( ! in_array( $plugin_name, $plugins ) ) {
				$plugins[] = $plugin_name;
			}

			$widgets[] = [
				'name' => $widget->get_name(),
				'plugin' => $plugin_name,
				'title' => $widget_title,
				'icon' => $widget->get_icon(),
			];
		}

		$notice_id = 'e-element-manager-intro-1';

		$data = [
			'disabled_elements' => Options::get_disabled_elements(),
			'promotion_widgets' => [],
			'widgets' => $widgets,
			'plugins' => $plugins,
			'notice_data' => [
				'notice_id' => $notice_id,
				'is_viewed' => User::is_user_notice_viewed( $notice_id ),
				'nonce' => wp_create_nonce( 'elementor_set_admin_notice_viewed' ),
			],
			'promotion_data' => [
				'manager_permissions' => [
					'pro' => $this->get_element_manager_promotion(
						[
							'text' => esc_html__( 'Upgrade Now', 'elementor' ),
							'url' => self::FREE_TO_PRO_PERMISSIONS_PROMOTION_URL,
						],
						'pro_permissions'
					),
					'advanced' => $this->get_element_manager_promotion(
						[
							'text' => esc_html__( 'Upgrade Now', 'elementor' ),
							'url' => self::PRO_TO_ADVANCED_PERMISSIONS_PROMOTION_URL,
						],
						'advanced_permissions'
					),
				],
				'element_manager' => $this->get_element_manager_promotion(
					[
						'text' => esc_html__( 'Upgrade Now', 'elementor' ),
						'url' => self::ELEMENT_MANAGER_PROMOTION_URL,
					],
					'element_manager'
				),
			],
		];

		if ( ! Utils::has_pro() ) {
			$data['promotion_widgets'] = Api::get_promotion_widgets();
		}

		$data['additional_data'] = apply_filters( 'elementor/element_manager/admin_app_data/additional_data', [] );

		wp_send_json_success( $data );
	}

	private function get_element_manager_promotion( $promotion_data, $filter_id ): array {

		return Filtered_Promotions_Manager::get_filtered_promotion_data( $promotion_data, 'elementor/element_manager/admin_app_data/promotion_data/' . $filter_id, 'url' );
	}

	private function verify_permission() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( esc_html__( 'You do not have permission to edit these settings.', 'elementor' ) );
		}

		$nonce = Utils::get_super_global_value( $_POST, 'nonce' ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'e-element-manager-app' ) ) {
			wp_send_json_error( esc_html__( 'Invalid nonce.', 'elementor' ) );
		}
	}

	private function force_enabled_all_elements() {
		remove_all_filters( 'elementor/widgets/is_widget_enabled' );
	}

	private function get_plugin_name_from_widget_instance( $widget ) {
		if ( in_array( 'wordpress', $widget->get_categories() ) ) { // phpcs:ignore WordPress.WP.CapitalPDangit.MisspelledInText
			return esc_html__( 'WordPress Widgets', 'elementor' );
		}

		$class_reflection = new \ReflectionClass( $widget );

		$plugin_basename = plugin_basename( $class_reflection->getFileName() );

		$plugin_directory = strtok( $plugin_basename, '/' );

		$plugins_data = get_plugins( '/' . $plugin_directory );
		$plugin_data = array_shift( $plugins_data );

		return $plugin_data['Name'] ?? esc_html__( 'Unknown', 'elementor' );
	}

	public function ajax_save_disabled_elements() {
		$this->verify_permission();

		$elements = Utils::get_super_global_value( $_POST, 'widgets' ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

		if ( empty( $elements ) ) {
			wp_send_json_error( esc_html__( 'No elements to save.', 'elementor' ) );
		}

		$disabled_elements = json_decode( $elements );

		if ( ! is_array( $disabled_elements ) ) {
			wp_send_json_error( esc_html__( 'Unexpected elements data.', 'elementor' ) );
		}

		Options::update_disabled_elements( $disabled_elements );

		do_action( 'elementor/element_manager/save_disabled_elements' );

		wp_send_json_success();
	}

	public function ajax_get_widgets_usage() {
		$this->verify_permission();

		/** @var Usage_Module $usage_module */
		$usage_module = Usage_Module::instance();
		$usage_module->recalc_usage();

		$widgets_usage = [];
		foreach ( $usage_module->get_formatted_usage( 'raw' ) as $data ) {
			foreach ( $data['elements'] as $element => $count ) {
				if ( ! isset( $widgets_usage[ $element ] ) ) {
					$widgets_usage[ $element ] = 0;
				}

				$widgets_usage[ $element ] += $count;
			}
		}

		wp_send_json_success( $widgets_usage );
	}
}
