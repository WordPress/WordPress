<?php

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if ( !class_exists( 'Plugin_Upgrader' ) ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
}

class Bagberry_Plugin_Upgrader_Skin extends Plugin_Upgrader_Skin {
	public function feedback( $string, ...$args) {
	}
	public function header() {
	}
 
	public function footer() {
	}

	public function set_result( $result) {
		$this->result = false;
	}
}

class Agni_Plugins_Installer {

	public function __construct() {

	}

	public static function pluginsList() {
		
		$available_plugins = array(
			array(
				'name'                  => 'WooCommerce',
				'slug'                  => 'woocommerce',
				'source'                => '',
				'required'              => false,
				'version'               => '6.7.0',
				'force_activation'      => false,
				'force_deactivation'    => false,
				'installation_path'     => 'woocommerce/woocommerce.php',
				'external_url'          => esc_url('wordpress.org/plugins/woocommerce/'),
			)
		);

		return $available_plugins;
	}

	public static function install_activate_plugins( WP_REST_Request $request ) {

		$result = '';

		$params = $request->get_params();

		$pluginSlug = $params['plugin'];

		if ( !empty( $pluginSlug ) ) {

			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$installed_plugins = get_plugins();

			$plugin_info = array_filter( $installed_plugins, function( $existing_plugin_slug) use( $pluginSlug) {
				if ( $existing_plugin_slug === $pluginSlug ) {
					return true;
				}
			}, ARRAY_FILTER_USE_KEY);


			// print_r($plugin_info);

			if ( !empty( $plugin_info ) ) {
				foreach ($plugin_info as $installedPluginPath => $value) {

					// echo 'Displaying activate';
					$result = self::activatePlugin( $installedPluginPath );
					
					if ( is_wp_error( $result ) ) {
						wp_send_json_error( esc_html( $result->get_error_message() ) );
					}
				}
			} else {
				$result = self::installFreePlugin( $pluginSlug );

			}

			// wp_send_json( json_encode( $installed_plugins ) );
			// wp_send_json_success( 'hello ' . $pluginSlug );

		}
		
	}


	public static function activatePlugin( $pluginSlug ) {
		$result = activate_plugin( $pluginSlug );

		if ( is_wp_error($result) ) {
			wp_send_json_error( esc_html__( 'Failed to activate plugin', 'bagberry' ) );
		} else {

			$active_plugins = get_option('active_plugins');

			wp_send_json_success( $active_plugins );
		}
	}



	public static function installFreePlugin( $pluginSlug) {

		require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	   
		$source = '';

		$pluginsList = self::pluginsList();

		foreach ($pluginsList as $key => $plugin) {
			if ( $plugin['installation_path'] === $pluginSlug ) {
				// $source = $plugin['source'];
				if ( empty( $plugin['source'] ) ) {
					$args = array(
						'slug' => $plugin['slug'],
						'fields' => array(
							'version' => true,
							'icons' => true
						)
					);
			
					$repo_plugin_info = plugins_api( 'plugin_information', $args );                    
					$source = $repo_plugin_info->download_link;
					
				} else {
					$source = $plugin['source'];
				}
			}
		}

		$skin = new Bagberry_Plugin_Upgrader_Skin(); 
		$upgrader = new Plugin_Upgrader( $skin );

		$install = $upgrader->install( $source );

		
		if ( $install ) {
			$result = activate_plugin( $pluginSlug );
					
			if ( is_wp_error( $result ) ) {
				wp_send_json_error( esc_html( $result->get_error_message() ) );
			} else {

				$active_plugins = get_option('active_plugins');

				wp_send_json_success( $active_plugins );
			}
		} else {
			wp_send_json_error( esc_html__( 'Failed to install plugin', 'bagberry' ) );
		}
	}

}

$agni_plugins_installer = new Agni_Plugins_Installer();
