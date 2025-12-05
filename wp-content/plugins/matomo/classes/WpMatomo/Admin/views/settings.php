<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

use WpMatomo\Admin\AdminSettings;
use WpMatomo\Admin\AdminSettingsInterface;
use WpMatomo\Admin\Menu;
use WpMatomo\Capabilities;
use WpMatomo\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/** @var AdminSettingsInterface[] $setting_tabs */
/** @var AdminSettingsInterface $content_tab */
/** @var string $active_tab */
/** @var Settings $matomo_settings */
?>
<div class="wrap">
	<div id="icon-plugins" class="icon32"></div>
	<h1><?php matomo_header_icon(); ?><?php esc_html_e( 'Settings', 'matomo' ); ?></h1>
	<?php
	if ( $matomo_settings->is_network_enabled() && is_network_admin() ) {
		echo '<div class="notice notice-info is-dismissible"><br>You are running Matomo in network mode. This means below settings will be applied to all blogs in your network.<br><br></div>';
	} elseif ( $matomo_settings->is_network_enabled() && ! is_network_admin() ) {
		echo '<div class="notice notice-info is-dismissible"><br>';
		esc_html_e( 'You are running Matomo in network mode.', 'matomo' );
		echo ' ';
		echo 'Below settings aren\'t applied for all blogs but have to be configured for each blog separately. We are hoping to improve this in the future. Any setting within the Matomo admin is configured on a per blog basis as well. Only you as a Matomo super user can see these settings.<br><br></div>';
	}
	?>
	<h2 class="nav-tab-wrapper">
		<?php foreach ( $setting_tabs as $matomo_setting_slug => $matomo_setting_tab ) { ?>
			<a href="<?php echo esc_url( AdminSettings::make_url( $matomo_setting_slug ) ); ?>"
			   class="nav-tab <?php echo $active_tab === $matomo_setting_slug ? 'nav-tab-active' : ''; ?>"
			><?php echo esc_html( $matomo_setting_tab->get_title() ); ?></a>
		<?php } ?>

		<?php
		if ( current_user_can( Capabilities::KEY_SUPERUSER )
			 && ! is_network_admin() ) {
			?>
			<a href="<?php echo esc_url( Menu::get_matomo_goto_url( Menu::REPORTING_GOTO_ADMIN ) ); ?>" class="nav-tab"
			><?php esc_html_e( 'Matomo Admin', 'matomo' ); ?> <span class="dashicons-before dashicons-external"></span></a>

			<?php
		}
		?>
	</h2>

	<?php $content_tab->show_settings(); ?>
</div>
