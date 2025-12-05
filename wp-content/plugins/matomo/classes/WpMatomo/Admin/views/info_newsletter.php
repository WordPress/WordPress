<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

use WpMatomo\Admin\Info;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/** @var bool $signedup_newsletter */
/** @var bool $show_newsletter */

if ( $signedup_newsletter ) {
	?>
	<div class="notice notice-success is-dismissible">
		<p><?php esc_html_e( 'Thank you for signing up to our newsletter.', 'matomo' ); ?></p>
	</div>
	<?php
	return;
}
if ( ! $show_newsletter ) {
	return;
}
?>

<div class="notice notice-success">
	<h2><?php esc_html_e( 'Newsletter', 'matomo' ); ?></h2>
	<form method="post">
		<p>
			<?php wp_nonce_field( Info::NONCE_NAME ); ?>
			<input type="checkbox" id="<?php echo esc_attr( Info::FORM_NAME ); ?>" name="<?php echo esc_attr( Info::FORM_NAME ); ?>" value="1">
			<label for="<?php echo esc_attr( Info::FORM_NAME ); ?>">
				<?php esc_html_e( 'Subscribe to our newsletter to receive regular information about Matomo, web analytics, and privacy. You can unsubscribe from it any time.', 'matomo' ); ?>
				<?php esc_html_e( 'This service uses MadMimi.', 'matomo' ); ?>
				<?php echo sprintf( esc_html__( 'Learn more about it on our %1$sPrivacy Policy page%2$s.', 'matomo' ), '<a href="https://matomo.org/privacy-policy/" target="_blank" rel="noreferrer noopener">', '</a>' ); ?>
			</label>
			<br><br>
			<input type="submit" class="button-secondary" value="<?php esc_attr_e( 'Subscribe', 'matomo' ); ?>">
		</p>
	</form>
</div>
