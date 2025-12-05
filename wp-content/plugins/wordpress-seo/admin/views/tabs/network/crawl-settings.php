<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Views
 *
 * @uses Yoast_Form $yform Form object.
 */

if ( ! defined( 'WPSEO_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

$feature_toggles = Yoast_Feature_Toggles::instance()->get_all();

?>
<h2><?php esc_html_e( 'Crawl settings', 'wordpress-seo' ); ?></h2>
<div class="yoast-measure">
	<?php
	printf(
		/* translators: %s expands to Yoast SEO */
		esc_html__( 'This tab allows you to selectively disable %s features for all sites in the network. By default all features are enabled, which allows site admins to choose for themselves if they want to toggle a feature on or off for their site. When you disable a feature here, site admins will not be able to use that feature at all.', 'wordpress-seo' ),
		'Yoast SEO'
	);

	echo '<p style="margin: 0.5em 0 1em;">';
	printf(
		/* translators: %1$s opens the link to the Yoast.com article about Crawl settings, %2$s closes the link, */
		esc_html__( '%1$sLearn more about crawl settings.%2$s', 'wordpress-seo' ),
		'<a href="' . esc_url( WPSEO_Shortlinker::get( 'https://yoa.st/crawl-settings' ) ) . '" target="_blank" rel="noopener noreferrer">',
		'</a>'
	);
	echo '</p>';

	/**
	 * Fires when displaying the crawl cleanup network tab.
	 *
	 * @param Yoast_Form $yform The yoast form object.
	 */
	do_action( 'wpseo_settings_tab_crawl_cleanup_network', $yform );
	?>
</div>
<?php
/*
 * Required to prevent our settings framework from saving the default because the field
 * isn't explicitly set when saving the Dashboard page.
 */
$yform->hidden( 'show_onboarding_notice', 'wpseo_show_onboarding_notice' );
