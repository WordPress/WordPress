<?php
/**
 * Privacy Policy Guide Screen.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'manage_privacy_options' ) ) {
	wp_die( __( 'Sorry, you are not allowed to manage privacy options on this site.' ) );
}

if ( ! class_exists( 'WP_Privacy_Policy_Content' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-privacy-policy-content.php';
}

// Used in the HTML title tag.
$title = __( 'Privacy Policy Guide' );

add_filter(
	'admin_body_class',
	static function ( $body_class ) {
		$body_class .= ' privacy-settings ';

		return $body_class;
	}
);

wp_enqueue_script( 'privacy-tools' );

require_once ABSPATH . 'wp-admin/admin-header.php';

?>
<div class="privacy-settings-header">
	<div class="privacy-settings-title-section">
		<h1>
			<?php _e( 'Privacy' ); ?>
		</h1>
	</div>

	<nav class="privacy-settings-tabs-wrapper hide-if-no-js" aria-label="<?php esc_attr_e( 'Secondary menu' ); ?>">
		<a href="<?php echo esc_url( admin_url( 'options-privacy.php' ) ); ?>" class="privacy-settings-tab">
			<?php
			/* translators: Tab heading for Site Health Status page. */
			_ex( 'Settings', 'Privacy Settings' );
			?>
		</a>

		<a href="<?php echo esc_url( admin_url( 'options-privacy.php?tab=policyguide' ) ); ?>" class="privacy-settings-tab active" aria-current="true">
			<?php
			/* translators: Tab heading for Site Health Status page. */
			_ex( 'Policy Guide', 'Privacy Settings' );
			?>
		</a>
	</nav>
</div>

<hr class="wp-header-end">

<?php
wp_admin_notice(
	__( 'The Privacy Settings require JavaScript.' ),
	array(
		'type'               => 'error',
		'additional_classes' => array( 'hide-if-js' ),
	)
);
?>

<div class="privacy-settings-body hide-if-no-js">
	<h2><?php _e( 'Privacy Policy Guide' ); ?></h2>
	<h3 class="section-title"><?php _e( 'Introduction' ); ?></h3>
	<p><?php _e( 'This text template will help you to create your website&#8217;s privacy policy.' ); ?></p>
	<p><?php _e( 'The template contains a suggestion of sections you most likely will need. Under each section heading, you will find a short summary of what information you should provide, which will help you to get started. Some sections include suggested policy content, others will have to be completed with information from your theme and plugins.' ); ?></p>
	<p><?php _e( 'Please edit your privacy policy content, making sure to delete the summaries, and adding any information from your theme and plugins. Once you publish your policy page, remember to add it to your navigation menu.' ); ?></p>
	<p><?php _e( 'It is your responsibility to write a comprehensive privacy policy, to make sure it reflects all national and international legal requirements on privacy, and to keep your policy current and accurate.' ); ?></p>
	<div class="privacy-settings-accordion">
		<h4 class="privacy-settings-accordion-heading">
			<button aria-expanded="false" class="privacy-settings-accordion-trigger" aria-controls="privacy-settings-accordion-block-privacy-policy-guide" type="button">
				<span class="title"><?php _e( 'Privacy Policy Guide' ); ?></span>
				<span class="icon"></span>
			</button>
		</h4>
		<div id="privacy-settings-accordion-block-privacy-policy-guide" class="privacy-settings-accordion-panel" hidden="hidden">
			<?php
			$content = WP_Privacy_Policy_Content::get_default_content( true, false );
			echo $content;
			?>
		</div>
	</div>
	<hr class="hr-separator">
	<h3 class="section-title"><?php _e( 'Policies' ); ?></h3>
	<div class="privacy-settings-accordion wp-privacy-policy-guide">
		<?php WP_Privacy_Policy_Content::privacy_policy_guide(); ?>
	</div>
</div>
<?php

require_once ABSPATH . 'wp-admin/admin-footer.php';
