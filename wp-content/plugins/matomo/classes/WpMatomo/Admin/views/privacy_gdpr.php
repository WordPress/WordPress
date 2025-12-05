<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 * https://github.com/braekling/matomo
 *
 */

use WpMatomo\Admin\Menu;
use WpMatomo\Admin\PrivacySettings;
use WpMatomo\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/** @var Settings $matomo_settings */

?>

<h2><?php esc_html_e( 'Matomo ensures the privacy of your users and analytics data! YOU keep control of your data.', 'matomo' ); ?></h2>

<blockquote
		class="matomo-blockquote"><?php esc_html_e( 'One of Matomo\'s guiding principles: respecting privacy', 'matomo' ); ?></blockquote>
<p>
	<?php esc_html_e( 'Matomo Analytics is privacy by design. All data collected is stored only within your own MySQL database, no other business (or Matomo team member) can access any of this information, and logs or report data will never be sent to other servers by Matomo', 'matomo' ); ?>
	.

	<?php
	echo sprintf(
		esc_html__( 'The source code of the software is open-source so hundreds of people have reviewed it to ensure it is %1$ssecure%2$s and keeps your data private.', 'matomo' ),
		'<a href="https://matomo.org/security/" rel="noreferrer noopener">',
		'</a>'
	);
	?>
</p>
<?php if ( $matomo_settings->is_network_enabled() && is_network_admin() ) { ?>
	<h2>Configure privacy settings</h2>
	<p>
		Currently, privacy settings have to be configured on a per blog basis.
		IP addresses are anonmyised by default. Should you wish to change any privacy setting, please go to the Matomo
		privacy settings within each blog.
		We are hoping to improve this in the future.
	</p>
<?php } else { ?>

	<h2>
		<?php esc_html_e( 'Ways Matomo protects the privacy of your users and customers', 'matomo' ); ?>
	</h2>
	<p><?php esc_html_e( 'Although Matomo Analytics is a web analytics software that has a purpose to track user activity on your website, we take privacy very seriously.', 'matomo' ); ?></p>
	<p><?php esc_html_e( 'Privacy is a fundamental right so by using Matomo you can rest assured you have 100% control over that data and can protect your user\'s privacy as it\'s on your own server.', 'matomo' ); ?></p>

	<ul class="matomo-list">
		<li>
			<a href="<?php echo esc_url( Menu::get_matomo_goto_url( Menu::REPORTING_GOTO_ANONYMIZE_DATA ) ); ?>"><?php esc_html_e( 'Anonymise data and IP addresses', 'matomo' ); ?></a>
		</li>
		<li>
			<a href="<?php echo esc_url( Menu::get_matomo_goto_url( Menu::REPORTING_GOTO_DATA_RETENTION ) ); ?>"><?php esc_html_e( 'Configure data retention', 'matomo' ); ?></a>
		</li>
		<li>
			<a href="<?php echo esc_url( Menu::get_matomo_goto_url( Menu::REPORTING_GOTO_OPTOUT ) ); ?>"><?php esc_html_e( 'Matomo has an opt-out mechanism which lets users opt-out of web analytics tracking', 'matomo' ); ?></a>
			(<?php esc_html_e( 'see below for the shortcode', 'matomo' ); ?>)
		</li>
		<li>
			<a href="<?php echo esc_url( Menu::get_matomo_goto_url( Menu::REPORTING_GOTO_ASK_CONSENT ) ); ?>"><?php esc_html_e( 'Asking for consent', 'matomo' ); ?></a>
		</li>
		<li>
			<a href="<?php echo esc_url( Menu::get_matomo_goto_url( Menu::REPORTING_GOTO_GDPR_OVERVIEW ) ); ?>"><?php esc_html_e( 'GDPR overview', 'matomo' ); ?></a>
		</li>
		<li>
			<a href="<?php echo esc_url( Menu::get_matomo_goto_url( Menu::REPORTING_GOTO_GDPR_TOOLS ) ); ?>"><?php esc_html_e( 'GDPR tools', 'matomo' ); ?></a>
		</li>
	</ul>
<?php } ?>
<h2>
	<?php esc_html_e( 'Let users opt-out of tracking', 'matomo' ); ?>
</h2>
<p><?php esc_html_e( 'You have two options to embed the opt out iframe into your website:', 'matomo' ); ?></p>
<ul class="matomo-list">
	<li>
	<?php
	echo sprintf(
		esc_html__( 'Use the short code %1$s.', 'matomo' ),
		'<code>' . esc_html( PrivacySettings::EXAMPLE_MINIMAL ) . '</code>'
	);
	?>
	<br/>
	<?php esc_html_e( 'You can use these short code options:', 'matomo' ); ?>
<ul class="matomo-list">
	<li>language - eg de or
		en. <?php esc_html_e( 'By default the language is detected automatically based on the user\'s browser', 'matomo' ); ?></li>
</ul>

<?php esc_html_e( 'Example', 'matomo' ); ?>: <code><?php echo esc_html( PrivacySettings::EXAMPLE_FULL ); ?></code>
	</li>
	<li><?php esc_html_e( 'Or you can add the "Matomo opt out" block directly to your page.', 'matomo' ); ?></li>
</ul>
