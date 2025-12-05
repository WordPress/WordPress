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
<style>
	.matomo-marketplace-wizard {
		width: 100%;
		max-width: 700px;
		background-color: white;
		box-shadow: 0 1px 2px rgba(0,0,0,.3);
		border-radius: 3px;
		padding-top: 48px;
		position: relative;
		margin: 32px auto 0 auto;
	}

	.matomo-marketplace-wizard-header {
		position: absolute;
		border-top-left-radius: 3px;
		border-top-right-radius: 3px;
		top: 0;
		left: 0;
		right: 0;
		height: 48px;
		background-color: #e9e9e9;
	}

	.matomo-marketplace-wizard-logo {
		top: -14px;
		left: calc(50% - 38px);
		position: absolute;
		border-radius: 50%;
		background-color: white;
		width: 72px;
		height: 72px;
		display: flex;
		align-items: center;
		justify-content: center;
		border: 2px solid #ccc;
	}

	.matomo-marketplace-wizard-body {
		padding: 24px;
	}

	.matomo-marketplace-wizard-logo img {
		width: 64px;
	}

	.matomo-marketplace-wizard .wizard-steps-header {
		display: flex;
		flex-direction: row;
		align-items: flex-start;
	}

	.matomo-marketplace-wizard .wizard-steps-header .step-title {
		text-transform: uppercase;
		flex: 1;
		color: #888;
	}

	.matomo-marketplace-wizard .wizard-steps-header .divider {
		width: 33px;
	}

	.matomo-marketplace-wizard .wizard-steps {
		display: flex;
		flex-direction: row;
		align-items: stretch;
	}

	.matomo-marketplace-wizard .wizard-steps .step {
		flex: 1;
		padding-right: 32px;
		display: flex;
		flex-direction: column;
		justify-content: space-between;
		align-items: flex-start;
		padding-bottom: 6px;
	}

	.matomo-marketplace-wizard .wizard-steps .divider {
		width: 1px;
		background-color: #aaa;
		margin: 0 16px;
	}

	.matomo-marketplace-wizard .wizard-footer p{
		font-size: 0.9em;
		margin-top: 24px;
	}
</style>
<div class="matomo-marketplace-wizard" data-current-step="0">
	<div class="matomo-marketplace-wizard-header">
		<div class="matomo-marketplace-wizard-logo">
			<img alt="Matomo Logo" src="<?php echo esc_attr( $matomo_logo_big ); ?>" />
		</div>
	</div>

	<div class="matomo-marketplace-wizard-body">
		<?php if ( $user_can_upload_plugins && ! $is_plugin_installed ) { ?>
		<h1><?php esc_html_e( 'Setup the Matomo Marketplace in two easy steps', 'matomo' ); ?></h1>

		<div class="wizard-steps-header">
			<p class="step-title">Step 1</p>
			<div class="divider"></div>
			<p class="step-title">Step 2</p>
		</div>
		<div class="wizard-steps">
			<div class="step">

				<p><?php echo sprintf( esc_html__( 'Download the %1$sMatomo Marketplace for WordPress%2$s plugin.', 'matomo' ), '<em>', '</em>' ); ?></p>

				<a class="button-primary download-plugin" rel="noreferrer noopener" target="_blank" href="https://builds.matomo.org/matomo-marketplace-for-wordpress-latest.zip">
					<?php esc_html_e( 'Download', 'matomo' ); ?>
				</a>
			</div>

			<div class="divider"></div>

			<div class="step">
				<p><?php esc_html_e( 'Upload and install the plugin.', 'matomo' ); ?></p>

				<a class="button-primary open-plugin-upload" target="_blank" href="plugin-install.php?tab=upload">
					<?php esc_html_e( 'Go to plugins admin', 'matomo' ); ?> →
				</a>
			</div>
		</div>
		<div class="wizard-footer">
			<p><em>
				<?php
				echo sprintf(
					esc_html__( 'Don\'t want to use the Matomo Marketplace? You can download Matomo plugins directly on %1$sour marketplace%2$s, but keep in mind, you won\'t receive automatic updates unless you use the Matomo Marketplace plugin.', 'matomo' ),
					'<a target="_blank" rel="noreferrer noopener" href="https://plugins.matomo.org/?wp=1">',
					'</a>'
				);
				?>
			</em></p>
			<p class="wizard-waiting-for" style="display:none;">
				<strong><?php esc_html_e( 'Waiting for plugin activation...', 'matomo' ); ?></strong>
			</p>
			<p class="wizard-reloading" style="display:none;">
				<strong><?php esc_html_e( 'Loading marketplace...', 'matomo' ); ?></strong>
			</p>
		</div>
		<?php } elseif ( $user_can_activate_plugins && $is_plugin_installed ) { ?>
			<h1><?php esc_html_e( 'Activate the Matomo Marketplace for WordPress plugin', 'matomo' ); ?></h1>

			<p><?php esc_html_e( 'The Matomo Marketplace plugin is installed but not active. Activate it by clicking the button below.', 'matomo' ); ?></p>

			<p>
				<a class="button-primary activate-plugin" rel="noreferrer noopener" href="">
					<?php esc_html_e( 'Activate', 'matomo' ); ?>
				</a>
			</p>
			<p class="wizard-waiting-for" style="display:none;">
				<?php esc_html_e( 'Waiting for plugin activation...', 'matomo' ); ?>
			</p>
			<p class="wizard-reloading" style="display:none;">
				<?php esc_html_e( 'Loading marketplace...', 'matomo' ); ?>
			</p>
		<?php } else { ?>
		<p>
			<?php
			echo sprintf(
				esc_html__( 'To manage Matomo plugins from the Matomo Marketplace, the %1$sMatomo Marketplace for WordPress%2$s must be installed.', 'matomo' ),
				'<a href="https://matomo.org/faq/wordpress/how-do-i-install-a-matomo-marketplace-plugin-in-matomo-for-wordpress/" target="_blank" rel="noreferrer noopener">',
				'</a>'
			);
			?>
		</p>
		<p><?php esc_html_e( 'Unfortunately, you do not appear to have the ability to upload plugin archives. Please ask your WordPress site administrator to complete this setup for you.', 'matomo' ); ?></p>
		<?php } ?>
	</div>
</div>
