<?php
/**
 * About This Version administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( './admin.php' );

$title = __( 'About' );

list( $display_version ) = explode( '-', $wp_version );

include( ABSPATH . 'wp-admin/admin-header.php' );
?>
<div class="wrap about-wrap">

<h1><?php printf( __( 'Welcome to WordPress %s' ), $display_version ); ?></h1>

<div class="about-text"><?php printf( __( 'Thank you for updating to the latest version! WordPress %s is already making your website better, faster, and more attractive, just like you!' ), $display_version ); ?></div>

<div class="wp-badge"><?php printf( __( 'Version %s' ), $display_version ); ?></div>

<h2 class="nav-tab-wrapper">
	<a href="about.php" class="nav-tab nav-tab-active">
		<?php _e( 'What&#8217;s New' ); ?>
	</a><a href="credits.php" class="nav-tab">
		<?php _e( 'Credits' ); ?>
	</a><a href="freedoms.php" class="nav-tab">
		<?php _e( 'Freedoms' ); ?>
	</a>
</h2>

<div class="changelog point-releases">
	<h3><?php echo _n( 'Maintenance and Security Release', 'Maintenance and Security Releases', 2 ); ?></h3>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
         '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.', 20 ), '3.4.2', number_format_i18n( 20 ) ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'http://codex.wordpress.org/Version_3.4.2' ); ?>
 	</p>

	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
         '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.', 21 ), '3.4.1', number_format_i18n( 21 ) ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'http://codex.wordpress.org/Version_3.4.1' ); ?>
 	</p>
</div>

<div class="changelog">
	<h3><?php _e( 'Live Theme Previews' ); ?></h3>

	<div class="feature-section images-stagger-right">
		<img src="<?php echo esc_url( admin_url( 'images/screenshots/theme-customizer.png' ) ); ?>" class="image-50" />
		<h4><?php _e( 'Try on New Themes' ); ?></h4>
		<p><?php _e( 'Gone are the days of rushing to update your header, background, and the like as soon as you activate a new theme. You can now customize these options <strong>before</strong> activating a new theme. Note: this feature is available for installed themes only.' ); ?></p>

		<h4><?php _e( 'Customize Current Theme' ); ?></h4>
		<p><?php _e( 'Satisfy your curiosity and try on a fresh coat of paint &mdash; you can also use the live preview mode to customize your current theme. Look for the Customize link on the Themes screen.' ); ?></p>
	</div>
</div>

<div class="changelog">
	<h3><?php _e( 'Custom Headers' ); ?></h3>

	<div class="feature-section">
		<h4><?php _e( 'Flexible Sizes' ); ?></h4>
		<p><?php _e( 'You can decide for yourself how tall or wide your custom header image should be. From now on, themes will provide a recommended image size for custom headers rather than a fixed requirement. Note: this feature requires <a href="http://codex.wordpress.org/Custom_Headers">theme support</a>.' ); ?></p>
		<div class="three-col-images">
			<img src="<?php echo esc_url( admin_url( 'images/screenshots/flex-header-1.png' ) ); ?>" class="image-30 first-feature" />
			<img src="<?php echo esc_url( admin_url( 'images/screenshots/flex-header-2.png' ) ); ?>" class="image-30" />
			<img src="<?php echo esc_url( admin_url( 'images/screenshots/flex-header-3.png' ) ); ?>" class="image-30 last-feature" />
		</div>
	</div>

	<div class="feature-section images-stagger-right">
		<img src="<?php echo esc_url( admin_url( 'images/screenshots/flex-header-media-library.png' ) ); ?>" class="image-50" />
		<h4><?php _e( 'Choose from Media Library' ); ?></h4>
		<p><?php _e( 'Tired of re-uploading the same custom header image every time you check out a new theme? Now you can choose header images from your media library for easier customization.' ); ?></p>
	</div>
</div>

<div class="changelog">
	<h3><?php _e( 'Twitter Embeds' ); ?></h3>

	<div class="feature-section images-stagger-right">
		<img src="<?php echo esc_url( admin_url( 'images/screenshots/twitter-embed-1.png' ) ); ?>" class="image-30" />
		<img src="<?php echo esc_url( admin_url( 'images/screenshots/twitter-embed-2.png' ) ); ?>" class="image-30" />
		<h4><?php _e( 'Share Tweets with Style' ); ?></h4>
		<p><?php _e( 'You can now embed individual tweets in posts. It includes action links that allow readers to reply to, retweet, and favorite the tweet without leaving your site. Just paste a tweet URL on its own line.' ); ?></p>
		<p><?php printf( __( 'This works with URLs from some other sites, too. For more, see the Codex article on <a href="%s">Embeds</a>.' ), __( 'http://codex.wordpress.org/Embeds' ) ); ?></p>
	</div>

</div>


<div class="changelog">
	<h3><?php _e( 'Better Captions' ); ?></h3>

	<div class="feature-section images-stagger-right">
		<img src="<?php echo esc_url( admin_url( 'images/screenshots/captions-1.png' ) ); ?>" class="image-30" />
		<img src="<?php echo esc_url( admin_url( 'images/screenshots/captions-2.png' ) ); ?>" class="image-30" />
		<h4><?php _e( 'HTML Support' ); ?></h4>
		<p><?php _e( 'Basic HTML support has been added to the caption field in the image uploader. This allows you to add links &mdash; great for photo credits or licensing details &mdash; and basic formatting such as bold and italicized text.' ); ?></p>
	</div>
</div>

<div class="changelog">
	<h3><?php _e( 'Under the Hood' ); ?></h3>

	<div class="feature-section three-col">
		<div>
			<h4><?php _e( 'Faster WP_Query' ); ?></h4>
			<p><?php _e( 'Post queries have been optimized to improve performance, especially for sites with large databases.' ); ?></p>

			<h4><?php _e( 'Faster Translations' ); ?></h4>
			<p><?php _e( 'The number of strings loaded on the front end was greatly reduced, resulting in faster front page load times for localized installations.' ); ?> <?php _e( 'Also, better support for East Asian languages, right-to-left languages, theme translations, and more.' ); ?></p>
		</div>
		<div>
			<h4><?php _e( 'Themes API' ); ?></h4>
			<p><?php _e( 'WP_Theme, wp_get_themes(), wp_get_theme(). Faster, uses less memory, makes use of persistent caching.' ); ?></p>

			<h4><?php _e( 'Custom Header and Background API' ); ?></h4>
			<p><?php  _e( 'Custom header and background API relocated into the theme support API.' ); ?></p>
		</div>
		<div class="last-feature">
			<h4><?php _e( 'XML-RPC API' ); ?></h4>
			<p><?php printf( __( 'A new <a href="%s">WordPress API</a> that supports custom content types and taxonomies, as well as dozens of other bug fixes and improvements.' ), __( 'http://codex.wordpress.org/XML-RPC_WordPress_API' ) ); ?></p>

			<h4><?php _e( 'External Libraries' ); ?></h4>
			<p><?php _e( 'jQuery, jQuery UI, TinyMCE, Plupload, PHPMailer, SimplePie, and other libraries were updated. jQuery UI Touch Punch was introduced.' ); ?></p>
		</div>
	</div>
</div>

<div class="return-to-dashboard">
	<?php if ( current_user_can( 'update_core' ) && isset( $_GET['updated'] ) ) : ?>
	<a href="<?php echo esc_url( self_admin_url( 'update-core.php' ) ); ?>"><?php
		is_multisite() ? _e( 'Return to Updates' ) : _e( 'Return to Dashboard &rarr; Updates' );
	?></a> |
	<?php endif; ?>
	<a href="<?php echo esc_url( self_admin_url() ); ?>"><?php
		is_blog_admin() ? _e( 'Go to Dashboard &rarr; Home' ) : _e( 'Go to Dashboard' ); ?></a>
</div>

</div>
<?php

include( ABSPATH . 'wp-admin/admin-footer.php' );

// These are strings we may use to describe maintenance/security releases, where we aim for no new strings.
return;

_n_noop( 'Maintenance Release', 'Maintenance Releases' );
_n_noop( 'Security Release', 'Security Releases' );
_n_noop( 'Maintenance and Security Release', 'Maintenance and Security Releases' );

/* translators: 1: WordPress version number. */
_n_noop( '<strong>Version %1$s</strong> addressed a security issue.',
         '<strong>Version %1$s</strong> addressed some security issues.' );

/* translators: 1: WordPress version number, 2: plural number of bugs. */
_n_noop( '<strong>Version %1$s</strong> addressed %2$s bug.',
         '<strong>Version %1$s</strong> addressed %2$s bugs.' );

/* translators: 1: WordPress version number, 2: plural number of bugs. Singular security issue. */
_n_noop( '<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bug.',
         '<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bugs.' );

/* translators: 1: WordPress version number, 2: plural number of bugs. More than one security issue. */
_n_noop( '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
         '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.' );

__( 'For more information, see <a href="%s">the release notes</a>.' );
