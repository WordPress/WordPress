<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo;

/**
 * Customizations for the WordPress plugins page.
 */
class PluginAdminOverrides {
	/**
	 * @var Settings
	 */
	private $settings;

	public function __construct( $settings ) {
		$this->settings = $settings;
	}

	public function register_hooks() {
		if ( $this->is_plugins_php_page() ) {
			add_action( 'admin_footer', [ $this, 'add_data_deletion_notice_if_plugins_php' ] );
		}
	}

	public function add_data_deletion_notice_if_plugins_php() {
		$note                          = esc_html__( 'Note', 'matomo' );
		$change_settings_url           = home_url( '/wp-admin/admin.php?page=matomo-settings&tab=advanced' );
		$change_data_deletion_settings = esc_html__( 'Change data deletion settings.', 'matomo' );

		if ( $this->settings->should_delete_all_data_on_uninstall() ) {
			$deletion_setting_notice = esc_html__( 'Data will be permanently deleted upon plugin deletion.', 'matomo' );
		} else {
			$deletion_setting_notice = esc_html__( 'Data will %1$snot%2$s be deleted upon plugin deletion.', 'matomo' );
			$deletion_setting_notice = sprintf( $deletion_setting_notice, '<strong style="display:inline;">', '</strong>' );
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo <<<EOF
<script>
jQuery(document).ready(
  function () {
    var \$title = jQuery('body.plugins-php tr[data-slug="matomo"] td.plugin-title > strong:first-child');
    \$title.after('<p><span style="margin: 0 2px 2px 0; display: inline-block; vertical-align: middle;">ℹ️</span> $note: $deletion_setting_notice<br/><a href="$change_settings_url" id="mwp-data-deletion-settings">$change_data_deletion_settings</a></p>');
  }
);
</script>
EOF;
	}

	private function is_plugins_php_page() {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$request_uri      = wp_unslash( isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '' );
		$plugins_php_path = wp_parse_url( home_url(), PHP_URL_PATH ) . '/wp-admin/plugins.php';
		$current_path     = wp_parse_url( $request_uri, PHP_URL_PATH );

		return $plugins_php_path === $current_path;
	}
}
