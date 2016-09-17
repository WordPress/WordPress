<?php
/**
 * Upgrader API: Plugin_Installer_Skin class
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 4.6.0
 */

/**
 * Plugin Installer Skin for WordPress Plugin Installer.
 *
 * @since 2.8.0
 * @since 4.6.0 Moved to its own file from wp-admin/includes/class-wp-upgrader-skins.php.
 *
 * @see WP_Upgrader_Skin
 */
class Plugin_Installer_Skin extends WP_Upgrader_Skin {
	public $api;
	public $type;

	/**
	 *
	 * @param array $args
	 */
	public function __construct($args = array()) {
		$defaults = array( 'type' => 'web', 'url' => '', 'plugin' => '', 'nonce' => '', 'title' => '' );
		$args = wp_parse_args($args, $defaults);

		$this->type = $args['type'];
		$this->api = isset($args['api']) ? $args['api'] : array();

		parent::__construct($args);
	}

	/**
	 * @access public
	 */
	public function before() {
		if ( !empty($this->api) )
			$this->upgrader->strings['process_success'] = sprintf( __('Successfully installed the plugin <strong>%s %s</strong>.'), $this->api->name, $this->api->version);
	}

	/**
	 * @access public
	 */
	public function after() {
		$plugin_file = $this->upgrader->plugin_info();

		$install_actions = array();

		$from = isset($_GET['from']) ? wp_unslash( $_GET['from'] ) : 'plugins';

		if ( 'import' == $from )
			$install_actions['activate_plugin'] = '<a class="button button-primary" href="' . wp_nonce_url( 'plugins.php?action=activate&amp;from=import&amp;plugin=' . urlencode( $plugin_file ), 'activate-plugin_' . $plugin_file ) . '" target="_parent">' . __( 'Activate Plugin &amp; Run Importer' ) . '</a>';
		else
			$install_actions['activate_plugin'] = '<a class="button button-primary" href="' . wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . urlencode( $plugin_file ), 'activate-plugin_' . $plugin_file ) . '" target="_parent">' . __( 'Activate Plugin' ) . '</a>';

		if ( is_multisite() && current_user_can( 'manage_network_plugins' ) ) {
			$install_actions['network_activate'] = '<a class="button button-primary" href="' . wp_nonce_url( 'plugins.php?action=activate&amp;networkwide=1&amp;plugin=' . urlencode( $plugin_file ), 'activate-plugin_' . $plugin_file ) . '" target="_parent">' . __( 'Network Activate' ) . '</a>';
			unset( $install_actions['activate_plugin'] );
		}

		if ( 'import' == $from ) {
			$install_actions['importers_page'] = '<a href="' . admin_url( 'import.php' ) . '" target="_parent">' . __( 'Return to Importers' ) . '</a>';
		} elseif ( $this->type == 'web' ) {
			$install_actions['plugins_page'] = '<a href="' . self_admin_url( 'plugin-install.php' ) . '" target="_parent">' . __( 'Return to Plugin Installer' ) . '</a>';
		} elseif ( 'upload' == $this->type && 'plugins' == $from ) {
			$install_actions['plugins_page'] = '<a href="' . self_admin_url( 'plugin-install.php' ) . '">' . __( 'Return to Plugin Installer' ) . '</a>';
		} else {
			$install_actions['plugins_page'] = '<a href="' . self_admin_url( 'plugins.php' ) . '" target="_parent">' . __( 'Return to Plugins page' ) . '</a>';
		}

		if ( ! $this->result || is_wp_error($this->result) ) {
			unset( $install_actions['activate_plugin'], $install_actions['network_activate'] );
		} elseif ( ! current_user_can( 'activate_plugins' ) ) {
			unset( $install_actions['activate_plugin'] );
		}

		/**
		 * Filters the list of action links available following a single plugin installation.
		 *
		 * @since 2.7.0
		 *
		 * @param array  $install_actions Array of plugin action links.
		 * @param object $api             Object containing WordPress.org API plugin data. Empty
		 *                                for non-API installs, such as when a plugin is installed
		 *                                via upload.
		 * @param string $plugin_file     Path to the plugin file.
		 */
		$install_actions = apply_filters( 'install_plugin_complete_actions', $install_actions, $this->api, $plugin_file );

		if ( ! empty( $install_actions ) ) {
			$this->feedback( implode( ' ', (array) $install_actions ) );
		}
	}
}
