<?php

namespace HelloTheme\Modules\AdminHome\Components;

use HelloTheme\Includes\Script;
use HelloTheme\Includes\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Conversion_Banner {

	const DEFAULT_SELECTOR = '.wrap h1, .wrap h2';
	const SCRIPT_HANDLE = 'hello-conversion-banner';
	const NONCE_ACTION = 'ehe_cb_nonce';
	const OBJECT_NAME = 'ehe_cb';
	const USER_META_KEY = '_hello_elementor_install_notice';
	const AJAX_ACTION = 'ehe_dismiss_theme_notice';

	private function render_conversion_banner() {
		?>
		<div id="ehe-admin-cb" style="width: calc(100% - 48px)">
		</div>
		<?php
	}

	private function get_allowed_admin_pages(): array {
		return [
			'dashboard' => [ 'selector' => '#wpbody #wpbody-content .wrap h1' ],
			'update-core' => [ 'selector' => self::DEFAULT_SELECTOR ],
			'edit-post' => [ 'selector' => self::DEFAULT_SELECTOR ],
			'edit-category' => [ 'selector' => self::DEFAULT_SELECTOR ],
			'edit-post_tag' => [ 'selector' => self::DEFAULT_SELECTOR ],
			'upload' => [ 'selector' => self::DEFAULT_SELECTOR ],
			'media' => [ 'selector' => self::DEFAULT_SELECTOR ],
			'edit-page' => [ 'selector' => self::DEFAULT_SELECTOR ],
			'elementor_page_elementor-settings' => [ 'selector' => self::DEFAULT_SELECTOR ],
			'edit-elementor_library' => [
				'selector' => self::DEFAULT_SELECTOR,
				'before' => true,
			],
			'elementor_page_elementor-tools' => [
				'selector' => self::DEFAULT_SELECTOR,
			],
			'elementor_page_elementor-role-manager' => [
				'selector' => self::DEFAULT_SELECTOR,
			],
			'elementor_page_elementor-element-manager' => [
				'selector' => '.wrap h1, .wrap h3.wp-heading-inline',
			],
			'elementor_page_elementor-system-info' => [
				'selector' => '#wpbody #wpbody-content #elementor-system-info .elementor-system-info-header',
				'before' => true,
			],
			'elementor_library_page_e-floating-buttons' => [
				'selector' => '#wpbody-content .e-landing-pages-empty, .wrap h2',
				'before' => true,
			],
			'edit-e-floating-buttons' => [
				'selector' => self::DEFAULT_SELECTOR,
			],
			'edit-elementor_library_category' => [
				'selector' => self::DEFAULT_SELECTOR,
			],
			'themes' => [
				'selector' => self::DEFAULT_SELECTOR,
			],
			'nav-menus' => [
				'selector' => self::DEFAULT_SELECTOR,
			],
			'theme-editor' => [
				'selector' => self::DEFAULT_SELECTOR,
			],
			'plugins' => [
				'selector' => self::DEFAULT_SELECTOR,
			],
			'plugin-install' => [
				'selector' => self::DEFAULT_SELECTOR,
			],
			'plugin-editor' => [
				'selector' => self::DEFAULT_SELECTOR,
			],
			'users' => [
				'selector' => self::DEFAULT_SELECTOR,
			],
			'user' => [
				'selector' => self::DEFAULT_SELECTOR,
			],
			'profile' => [
				'selector' => self::DEFAULT_SELECTOR,
			],
			'tools' => [
				'selector' => self::DEFAULT_SELECTOR,
			],
			'import' => [
				'selector' => self::DEFAULT_SELECTOR,
			],
			'export' => [
				'selector' => self::DEFAULT_SELECTOR,
			],
			'site-health' => [
				'selector' => self::DEFAULT_SELECTOR,
			],
			'export-personal-data' => [
				'selector' => self::DEFAULT_SELECTOR,
			],
			'erase-personal-data' => [
				'selector' => self::DEFAULT_SELECTOR,
			],
			'options-general' => [
				'selector' => self::DEFAULT_SELECTOR,
			],
			'options-writing' => [
				'selector' => self::DEFAULT_SELECTOR,
			],
			'options-reading' => [
				'selector' => self::DEFAULT_SELECTOR,
			],
			'options-discussion' => [
				'selector' => self::DEFAULT_SELECTOR,
			],
			'options-media' => [
				'selector' => self::DEFAULT_SELECTOR,
			],
			'options-permalink' => [
				'selector' => self::DEFAULT_SELECTOR,
			],
			'options-privacy' => [
				'selector' => self::DEFAULT_SELECTOR,
			],
			'privacy-policy-guide' => [
				'selector' => self::DEFAULT_SELECTOR,
			],
		];
	}

	private function is_allowed_admin_page(): array {
		$current_screen = get_current_screen();

		if ( ! $current_screen ) {
			return [];
		}

		$allowed_pages = $this->get_allowed_admin_pages();
		$current_page = $current_screen->id;

		return $allowed_pages[ $current_page ] ?? [];
	}

	private function is_conversion_banner_active(): array {
		if ( get_user_meta( get_current_user_id(), self::USER_META_KEY, true ) ) {
			return [];
		}

		if ( Utils::has_pro() && Utils::is_elementor_active() ) {
			return [];
		}

		return $this->is_allowed_admin_page();
	}

	private function enqueue_scripts( array $conversion_banner_active ) {
		$script = new Script(
			self::SCRIPT_HANDLE,
			[ 'wp-util' ]
		);

		$script->enqueue();

		$is_installing_plugin_with_uploader = 'upload-plugin' === filter_input( INPUT_GET, 'action', FILTER_UNSAFE_RAW );

		wp_localize_script(
			self::SCRIPT_HANDLE,
			self::OBJECT_NAME,
			[
				'nonce' => wp_create_nonce( self::NONCE_ACTION ),
				'beforeWrap' => $is_installing_plugin_with_uploader,
				'data' => $conversion_banner_active,
			]
		);
	}

	public function dismiss_theme_notice() {
		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		update_user_meta( get_current_user_id(), self::USER_META_KEY, true );

		wp_send_json_success( [ 'message' => __( 'Notice dismissed.', 'hello-elementor' ) ] );
	}

	public function __construct() {

		add_action( 'wp_ajax_' . self::AJAX_ACTION, [ $this, 'dismiss_theme_notice' ] );

		add_action( 'current_screen', function () {
			$conversion_banner_active = $this->is_conversion_banner_active();
			if ( ! $conversion_banner_active ) {
				return;
			}

			add_action( 'in_admin_header', function () {
				$this->render_conversion_banner();
			}, 11 );

			add_action( 'admin_enqueue_scripts', function () use ( $conversion_banner_active ) {
				$this->enqueue_scripts( $conversion_banner_active );
			} );
		} );
	}
}
