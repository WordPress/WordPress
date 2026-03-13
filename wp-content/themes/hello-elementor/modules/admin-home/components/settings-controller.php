<?php

namespace HelloTheme\Modules\AdminHome\Components;

use HelloTheme\Includes\Script;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Settings_Controller {

	const SETTINGS_FILTER_NAME = 'hello-plus-theme/settings';
	const SETTINGS_PAGE_SLUG = 'hello-elementor-settings';
	const SETTING_PREFIX = 'hello_elementor_settings';
	const SETTINGS = [
		'DESCRIPTION_META_TAG' => '_description_meta_tag',
		'SKIP_LINK'            => '_skip_link',
		'HEADER_FOOTER'        => '_header_footer',
		'PAGE_TITLE'           => '_page_title',
		'HELLO_STYLE'          => '_hello_style',
		'HELLO_THEME'          => '_hello_theme',
	];

	public static function get_settings_mapping(): array {
		return array_map(
			function ( $key ) {
				return self::SETTING_PREFIX . $key;
			},
			self::SETTINGS
		);
	}

	public static function get_settings(): array {

		$settings = array_map( function ( $key ) {
			return self::get_option( $key ) === 'true';
		}, self::get_settings_mapping() );

		return apply_filters( self::SETTINGS_FILTER_NAME, $settings );
	}

	protected static function get_option( string $option_name, $default_value = false ) {
		$option = get_option( $option_name, $default_value );

		return apply_filters( self::SETTINGS_FILTER_NAME . '/' . $option_name, $option );
	}

	public function legacy_register_settings() {
		$this->register_settings();
		$this->apply_settings();
	}

	public function apply_setting( $setting, $tweak_callback ) {
		$option = get_option( $setting );

		if ( isset( $option ) && ( 'true' === $option ) && is_callable( $tweak_callback ) ) {
			$tweak_callback();
		}

	}

	public function apply_settings( $settings_group = self::SETTING_PREFIX, $settings = self::SETTINGS ) {

		$this->apply_setting(
			$settings_group . $settings['DESCRIPTION_META_TAG'],
			function () {
				remove_action( 'wp_head', 'hello_elementor_add_description_meta_tag' );
			}
		);

		$this->apply_setting(
			$settings_group . $settings['SKIP_LINK'],
			function () {
				add_filter( 'hello_elementor_enable_skip_link', '__return_false' );
			}
		);

		$this->apply_setting(
			$settings_group . $settings['HEADER_FOOTER'],
			function () {
				add_filter( 'hello_elementor_header_footer', '__return_false' );
			}
		);

		$this->apply_setting(
			$settings_group . $settings['PAGE_TITLE'],
			function () {
				add_filter( 'hello_elementor_page_title', '__return_false' );
			}
		);

		$this->apply_setting(
			$settings_group . $settings['HELLO_STYLE'],
			function () {
				add_filter( 'hello_elementor_enqueue_style', '__return_false' );
			}
		);

		$this->apply_setting(
			$settings_group . $settings['HELLO_THEME'],
			function () {
				add_filter( 'hello_elementor_enqueue_theme_style', '__return_false' );
			}
		);
	}

	public function register_settings( $settings_group = self::SETTING_PREFIX, $settings = self::SETTINGS ) {
		foreach ( $settings as $setting_value ) {
			register_setting(
				$settings_group,
				$settings_group . $setting_value,
				[
					'default' => '',
					'show_in_rest' => true,
					'type' => 'string',
				]
			);
		}
	}

	public function enqueue_hello_plus_settings_scripts() {
		$screen = get_current_screen();

		if ( ! str_ends_with( $screen->id, '_page_' . self::SETTINGS_PAGE_SLUG ) ) {
			return;
		}

		$script = new Script(
			'hello-elementor-settings',
			[ 'wp-util' ]
		);

		$script->enqueue();
	}

	public function register_settings_page( $parent_slug ): void {
		add_submenu_page(
			$parent_slug,
			__( 'Settings', 'hello-elementor' ),
			__( 'Settings', 'hello-elementor' ),
			'manage_options',
			self::SETTINGS_PAGE_SLUG,
			[ $this, 'render_settings_page' ]
		);
	}

	public function render_settings_page(): void {
		echo '<div id="ehe-admin-settings"></div>';
	}

	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_hello_plus_settings_scripts' ] );
		add_action( 'hello-plus-theme/admin-menu', [ $this, 'register_settings_page' ], 10, 1 );
	}
}
