<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="notice notice-info is-dismissible" id="matomo-referral">
	<p>
		<?php esc_html_e( 'Like Matomo? We would really appreciate if you took 1 minute to rate us.', 'matomo' ); ?>

		<a href="https://wordpress.org/support/plugin/matomo/reviews/?rate=5#new-post" target="_blank"
		   rel="noreferrer noopener"
		   class="button matomo-dismiss-forever"><?php esc_html_e( 'Rate Matomo', 'matomo' ); ?></a>
	</p>
	<div style="clear:both;"></div>
</div>
