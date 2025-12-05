<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * Represents the utils for the admin.
 */
class WPSEO_Admin_Utils {

	/**
	 * Gets the install URL for the passed plugin slug.
	 *
	 * @param string $slug The slug to create an install link for.
	 *
	 * @return string The install URL. Empty string if the current user doesn't have the proper capabilities.
	 */
	public static function get_install_url( $slug ) {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return '';
		}

		return wp_nonce_url(
			self_admin_url( 'update.php?action=install-plugin&plugin=' . dirname( $slug ) ),
			'install-plugin_' . dirname( $slug )
		);
	}

	/**
	 * Gets the activation URL for the passed plugin slug.
	 *
	 * @param string $slug The slug to create an activation link for.
	 *
	 * @return string The activation URL. Empty string if the current user doesn't have the proper capabilities.
	 */
	public static function get_activation_url( $slug ) {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return '';
		}

		return wp_nonce_url(
			self_admin_url( 'plugins.php?action=activate&plugin_status=all&paged=1&s&plugin=' . $slug ),
			'activate-plugin_' . $slug
		);
	}

	/**
	 * Creates a link if the passed plugin is deemend a directly-installable plugin.
	 *
	 * @param array $plugin The plugin to create the link for.
	 *
	 * @return string The link to the plugin install. Returns the title if the plugin is deemed a Premium product.
	 */
	public static function get_install_link( $plugin ) {
		$install_url = self::get_install_url( $plugin['slug'] );

		if ( $install_url === '' || ( isset( $plugin['premium'] ) && $plugin['premium'] === true ) ) {
			return $plugin['title'];
		}

		return sprintf(
			'<a href="%s">%s</a>',
			$install_url,
			$plugin['title']
		);
	}

	/**
	 * Gets a visually hidden accessible message for links that open in a new browser tab.
	 *
	 * @return string The visually hidden accessible message.
	 */
	public static function get_new_tab_message() {
		return sprintf(
			'<span class="screen-reader-text">%s</span>',
			/* translators: Hidden accessibility text. */
			esc_html__( '(Opens in a new browser tab)', 'wordpress-seo' )
		);
	}
}
