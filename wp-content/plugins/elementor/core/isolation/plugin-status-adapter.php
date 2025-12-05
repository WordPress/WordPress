<?php
namespace Elementor\Core\Isolation;

class Plugin_Status_Adapter implements Plugin_Status_Adapter_Interface {

	public Wordpress_Adapter_Interface $wordpress_adapter;

	public function __construct( Wordpress_Adapter_Interface $wordpress_adapter ) {
		$this->wordpress_adapter = $wordpress_adapter;
	}

	public function is_plugin_installed( $plugin_path ): bool {
		$installed_plugins = $this->wordpress_adapter->get_plugins();

		return isset( $installed_plugins[ $plugin_path ] );
	}

	public function get_install_plugin_url( $plugin_path ): string {
		$slug = dirname( $plugin_path );
		$admin_base_url = $this->wordpress_adapter->self_admin_url( 'update.php' );

		$admin_url = add_query_arg( [
			'action' => 'install-plugin',
			'plugin' => $slug,
		], $admin_base_url );

		return $this->wordpress_adapter->wp_nonce_url( $admin_url, 'install-plugin_' . $slug );
	}

	public function get_activate_plugin_url( $plugin_path ): string {
		$admin_base_url = $this->wordpress_adapter->self_admin_url( 'plugins.php' );

		$admin_url = add_query_arg( [
			'action' => 'activate',
			'plugin' => $plugin_path,
			'plugin_status' => 'all',
			'paged' => 1,
			's' => '',
		], $admin_base_url );

		return $this->wordpress_adapter->wp_nonce_url( $admin_url, 'activate-plugin_' . $plugin_path );
	}
}
