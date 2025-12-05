<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */
/**
 * phpcs considers all of our variables as global and want them prefixed with matomo
 * phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
 */
use WpMatomo\Admin\AdminSettings;
use WpMatomo\Admin\GetStarted;
use WpMatomo\Admin\Menu;
use WpMatomo\Admin\TrackingSettings;
use WpMatomo\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** @var Settings $settings */
/** @var bool $can_user_edit */
/** @var bool $was_updated */
/** @var bool $show_this_page */

if ( empty( $show_this_page ) ) {
	echo '<meta http-equiv="refresh" content="0;url=' . esc_attr( menu_page_url( Menu::SLUG_ABOUT, false ) ) . '" />';
}
?>

<div class="wrap">
	<div id="icon-plugins" class="icon32"></div>

	<h1><?php esc_html_e( 'Start getting a full picture of your visitors', 'matomo' ); ?></h1>

	<?php
	if ( $was_updated ) {
		include 'update_notice_clear_cache.php';
	}
	?>

	<p>
	<?php require 'info_matomo_desc.php'; ?>
	</p>

	<hr/>

	<h1 style="font-size:1.4em">
		<?php esc_html_e( 'To start getting relevant reports and insights, complete these steps:', 'matomo' ); ?>
	</h1>

	<h2>1. <?php esc_html_e( 'Update your privacy page', 'matomo' ); ?></h2>

	<p>
		<?php esc_html_e( 'Before tracking any data, it\'s important to think about how you will respect the privacy of your users, and how you will ensure compliance with any relevant privacy guidelines, like the GDPR or ePrivacy Directive.', 'matomo' ); ?>
	</p>

	<p>
	<?php echo sprintf( esc_html__( 'Give your users the chance to opt-out of tracking by either adding the shortcode %1$s or by adding the "Matomo opt out" block to your privacy page.', 'matomo' ), '<code>[matomo_opt_out]</code>' ); ?>
	<?php echo sprintf( esc_html__( 'You can %1$stweak the opt-out to your liking - see the Privacy Settings%2$s.', 'matomo' ), '<a href="' . esc_url( AdminSettings::make_url( AdminSettings::TAB_PRIVACY ) ) . '">', '</a>' ); ?>
	</p>

	<p>
	<?php esc_html_e( 'You may also need to mention that you are using Matomo Analytics on your website.', 'matomo' ); ?>
	</p>
	<p>
	<?php echo sprintf( esc_html__( 'By %1$sdisabling cookies in the tracking settings%2$s, you might not need to ask for any cookie or tracking consent if the GDPR or ePrivacy applies to you (%3$slearn more%4$s).', 'matomo' ), '<a href="' . esc_url( AdminSettings::make_url( AdminSettings::TAB_TRACKING ) ) . '" target="_blank" rel="noreferrer noopener">', '</a>', '<a href="https://matomo.org/faq/new-to-piwik/how-do-i-use-matomo-analytics-without-consent-or-cookie-banner/" target="_blank" rel="noreferrer noopener">', '</a>' ); ?>
	</p>

	<?php if ( $settings->is_tracking_enabled() ) { ?>
		<h2>2. <?php esc_html_e( 'Tracking is enabled', 'matomo' ); ?> <span class="dashicons dashicons-yes"
																			 style="color: green;"></span></h2>
		<p><?php esc_html_e( 'Tracking should be working now and you don\'t have to do anything else to set up tracking.', 'matomo' ); ?>
			<a href="<?php echo esc_url( AdminSettings::make_url( AdminSettings::TAB_TRACKING ) ); ?>"><?php esc_html_e( 'Click here to optionally configure the tracking code to your liking (not required).', 'matomo' ); ?></a>
		</p>

	<?php } else { ?>
		<h2>2. <?php esc_html_e( 'Enable tracking', 'matomo' ); ?></h2>

		<p><?php esc_html_e( 'Enable tracking using the default configuration by clicking this button', 'matomo' ); ?>:</p>

		<form method="post"> <?php wp_nonce_field( GetStarted::NONCE_NAME ); ?>
			<input type="hidden" name="<?php echo esc_attr( GetStarted::FORM_NAME ); ?>[track_mode]"
				   value="<?php echo esc_attr( TrackingSettings::TRACK_MODE_DEFAULT ); ?>">
			<input type="submit" class="button-primary" value="<?php esc_html_e( 'Enable tracking now', 'matomo' ); ?>">
		</form>
	<?php } ?>

	<h2>3. <?php esc_html_e( 'Done', 'matomo' ); ?></h2>

	<p><?php esc_html_e( 'Once some data is tracked, your reports will soon appear!', 'matomo' ); ?></p>

	<form method="post">
		<?php wp_nonce_field( GetStarted::NONCE_NAME ); ?>
		<input type="hidden" name="<?php echo esc_attr( GetStarted::FORM_NAME ); ?>[show_get_started_page]"
			   value="no">
		<input type="submit" class="button-primary"
			   value="<?php esc_html_e( 'Hide this page', 'matomo' ); ?>">
	</form>
	<p>
		<br/>
	</p>
</div>
