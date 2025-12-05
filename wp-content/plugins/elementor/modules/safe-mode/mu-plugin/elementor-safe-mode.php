<?php
/**
 * Plugin Name: Elementor Safe Mode
 * Description: Safe Mode allows you to troubleshoot issues by only loading the editor, without loading the theme or any other plugin.
 * Plugin URI: https://elementor.com/?utm_source=safe-mode&utm_campaign=plugin-uri&utm_medium=wp-dash
 * Author: Elementor.com
 * Version: 1.0.0
 * Author URI: https://elementor.com/?utm_source=safe-mode&utm_campaign=author-uri&utm_medium=wp-dash
 *
 * Text Domain: elementor
 *
 * @package Elementor
 * @category Safe Mode
 *
 * Elementor is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * Elementor is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Safe_Mode {

	const OPTION_ENABLED = 'elementor_safe_mode';
	const OPTION_TOKEN = self::OPTION_ENABLED . '_token';

	public function is_enabled() {
		return get_option( self::OPTION_ENABLED );
	}

	public function is_valid_token() {
		$token = isset( $_COOKIE[ self::OPTION_TOKEN ] )
			? wp_kses_post( wp_unslash( $_COOKIE[ self::OPTION_TOKEN ] ) )
			: null;

		if ( $token && get_option( self::OPTION_TOKEN ) === $token ) {
			return true;
		}

		return false;
	}

	public function is_requested() {
		return ! empty( $_REQUEST['elementor-mode'] ) && 'safe' === $_REQUEST['elementor-mode'];
	}

	public function is_editor() {
		return is_admin() && isset( $_GET['action'] ) && 'elementor' === $_GET['action'];
	}

	public function is_editor_preview() {
		return isset( $_GET['elementor-preview'] );
	}

	public function is_editor_ajax() {
		// PHPCS - There is already nonce verification in the Ajax Manager
		return is_admin() && isset( $_POST['action'] ) && 'elementor_ajax' === $_POST['action']; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	}

	public function add_hooks() {
		add_filter( 'pre_option_active_plugins', function () {
			return get_option( 'elementor_safe_mode_allowed_plugins' );
		} );

		add_filter( 'pre_option_stylesheet', function () {
			return 'elementor-safe';
		} );

		add_filter( 'pre_option_template', function () {
			return 'elementor-safe';
		} );

		add_action( 'elementor/init', function () {
			do_action( 'elementor/safe_mode/init' );
		} );
	}

	/**
	 * Plugin row meta.
	 *
	 * Adds row meta links to the plugin list table
	 *
	 * Fired by `plugin_row_meta` filter.
	 *
	 * @access public
	 *
	 * @param array  $plugin_meta An array of the plugin's metadata, including
	 *                            the version, author, author URI, and plugin URI.
	 * @param string $plugin_file Path to the plugin file, relative to the plugins
	 *                            directory.
	 *
	 * @return array An array of plugin row meta links.
	 */
	public function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
		if ( basename( __FILE__ ) === $plugin_file ) {
			$row_meta = [
				'docs' => '<a href="https://go.elementor.com/safe-mode/" target="_blank">' . esc_html__( 'Learn More', 'elementor' ) . '</a>',
			];

			$plugin_meta = array_merge( $plugin_meta, $row_meta );
		}

		return $plugin_meta;
	}

	public function __construct() {
		add_filter( 'plugin_row_meta', [ $this, 'plugin_row_meta' ], 10, 4 );

		$enabled_type = $this->is_enabled();

		if ( ! $enabled_type || ! $this->is_valid_token() ) {
			return;
		}

		if ( ! $this->is_requested() && 'global' !== $enabled_type ) {
			return;
		}

		if ( ! $this->is_editor() && ! $this->is_editor_preview() && ! $this->is_editor_ajax() ) {
			return;
		}

		$this->add_hooks();
	}
}

new Safe_Mode();
