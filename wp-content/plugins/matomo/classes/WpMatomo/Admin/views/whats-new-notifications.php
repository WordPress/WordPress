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

/** @var array $matomo_notifications */
/** @var int[] $matomo_statuses */

foreach ( $matomo_notifications as $matomo_notification_id => $matomo_notification ) { ?>
<div class="notice notice-info matomo-whats-new is-dismissible" data-notification-id="<?php echo esc_attr( $matomo_notification_id ); ?>">
	<?php
		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $matomo_notification['message'];
	?>
</div>
<?php } ?>
