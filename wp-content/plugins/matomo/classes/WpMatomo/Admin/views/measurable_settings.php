<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}
?>

<script>
	window.addEventListener(
		'DOMContentLoaded',
		function () {
			window.iFrameResize( { log: <?php echo defined( 'WP_DEBUG' ) && WP_DEBUG ? 'true' : 'false'; ?>, bodyPadding: '0 0 16px 0' }, '#plugin_measurable_settings' );

			window.addEventListener('message', (e) => {
				if (e.data === 'open-matomo-admin') {
					document.querySelector('#openMatomoAdminLink').click();
				}
			});
		}
	);
</script>

<a href="<?php echo esc_url( $home_url . '/wp-content/plugins/matomo/app/index.php?module=CoreAdminHome&action=generalSettings' ); ?>" style="display:none;" id="openMatomoAdminLink"></a>

<p>
	<em>
		<?php echo esc_html__( 'Settings not loading?', 'matomo' ); ?>
		<a href="<?php echo esc_url( $home_url . '/wp-content/plugins/matomo/app/index.php?idSite=' . rawurlencode( $idsite ) . '&module=WordPress&action=showMeasurableSettings&plugin=' . rawurlencode( $plugin_name ) ); ?>" target="_blank">
			<?php echo esc_html__( 'Click this link to open them in a new window.', 'matomo' ); ?>
		</a>
	</em>
</p>

<iframe
	id="plugin_measurable_settings"
	title="<?php echo esc_attr__( 'Plugin Settings for', 'matomo' ); ?> <?php echo esc_attr( $plugin_display_name ); ?>"
	style="width:100%;margin-top:1em;"
	src="<?php echo esc_url( $home_url . '/wp-content/plugins/matomo/app/index.php?idSite=' . rawurlencode( $idsite ) . '&module=WordPress&action=showMeasurableSettings&plugin=' . rawurlencode( $plugin_name ) ); ?>"
></iframe>
