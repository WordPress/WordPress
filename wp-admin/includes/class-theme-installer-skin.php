<?php
/**
 * Upgrader API: Theme_Installer_Skin class
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 4.6.0
 */

/**
 * Theme Installer Skin for the WordPress Theme Installer.
 *
 * @since 2.8.0
 * @since 4.6.0 Moved to its own file from wp-admin/includes/class-wp-upgrader-skins.php.
 *
 * @see WP_Upgrader_Skin
 */
class Theme_Installer_Skin extends WP_Upgrader_Skin {
	public $api;
	public $type;

	/**
	 * @param array $args
	 */
	public function __construct( $args = array() ) {
		$defaults = array(
			'type'  => 'web',
			'url'   => '',
			'theme' => '',
			'nonce' => '',
			'title' => '',
		);
		$args     = wp_parse_args( $args, $defaults );

		$this->type = $args['type'];
		$this->api  = isset( $args['api'] ) ? $args['api'] : array();

		parent::__construct( $args );
	}

	/**
	 */
	public function before() {
		if ( ! empty( $this->api ) ) {
			$this->upgrader->strings['process_success'] = sprintf( $this->upgrader->strings['process_success_specific'], $this->api->name, $this->api->version );
		}
	}

	/**
	 */
	public function after() {
		if ( empty( $this->upgrader->result['destination_name'] ) ) {
			return;
		}

		$theme_info = $this->upgrader->theme_info();
		if ( empty( $theme_info ) ) {
			return;
		}

		$name       = $theme_info->display( 'Name' );
		$stylesheet = $this->upgrader->result['destination_name'];
		$template   = $theme_info->get_template();

		$activate_link = add_query_arg(
			array(
				'action'     => 'activate',
				'template'   => urlencode( $template ),
				'stylesheet' => urlencode( $stylesheet ),
			), admin_url( 'themes.php' )
		);
		$activate_link = wp_nonce_url( $activate_link, 'switch-theme_' . $stylesheet );

		$install_actions = array();

		if ( current_user_can( 'edit_theme_options' ) && current_user_can( 'customize' ) ) {
			$customize_url              = add_query_arg(
				array(
					'theme'  => urlencode( $stylesheet ),
					'return' => urlencode( admin_url( 'web' === $this->type ? 'theme-install.php' : 'themes.php' ) ),
				),
				admin_url( 'customize.php' )
			);
			$install_actions['preview'] = '<a href="' . esc_url( $customize_url ) . '" class="hide-if-no-customize load-customize"><span aria-hidden="true">' . __( 'Live Preview' ) . '</span><span class="screen-reader-text">' . sprintf( __( 'Live Preview &#8220;%s&#8221;' ), $name ) . '</span></a>';
		}
		$install_actions['activate'] = '<a href="' . esc_url( $activate_link ) . '" class="activatelink"><span aria-hidden="true">' . __( 'Activate' ) . '</span><span class="screen-reader-text">' . sprintf( __( 'Activate &#8220;%s&#8221;' ), $name ) . '</span></a>';

		if ( is_network_admin() && current_user_can( 'manage_network_themes' ) ) {
			$install_actions['network_enable'] = '<a href="' . esc_url( wp_nonce_url( 'themes.php?action=enable&amp;theme=' . urlencode( $stylesheet ), 'enable-theme_' . $stylesheet ) ) . '" target="_parent">' . __( 'Network Enable' ) . '</a>';
		}

		if ( $this->type == 'web' ) {
			$install_actions['themes_page'] = '<a href="' . self_admin_url( 'theme-install.php' ) . '" target="_parent">' . __( 'Return to Theme Installer' ) . '</a>';
		} elseif ( current_user_can( 'switch_themes' ) || current_user_can( 'edit_theme_options' ) ) {
			$install_actions['themes_page'] = '<a href="' . self_admin_url( 'themes.php' ) . '" target="_parent">' . __( 'Return to Themes page' ) . '</a>';
		}

		if ( ! $this->result || is_wp_error( $this->result ) || is_network_admin() || ! current_user_can( 'switch_themes' ) ) {
			unset( $install_actions['activate'], $install_actions['preview'] );
		}

		/**
		 * Filters the list of action links available following a single theme installation.
		 *
		 * @since 2.8.0
		 *
		 * @param array    $install_actions Array of theme action links.
		 * @param object   $api             Object containing WordPress.org API theme data.
		 * @param string   $stylesheet      Theme directory name.
		 * @param WP_Theme $theme_info      Theme object.
		 */
		$install_actions = apply_filters( 'install_theme_complete_actions', $install_actions, $this->api, $stylesheet, $theme_info );
		if ( ! empty( $install_actions ) ) {
			$this->feedback( implode( ' | ', (array) $install_actions ) );
		}
	}
}
