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

<div class="about-text"><?php printf( __( 'Thank you for updating to the latest version! Using WordPress %s will improve your looks, personality, and web publishing experience. Okay, just the last one, but still. :)' ), $display_version ); ?></div>

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

<div class="changelog">
	<h3><?php echo ( 'Live Theme Previews' ); ?></h3>

	<div class="feature-section images-stagger-right">
		<img src="<?php echo admin_url( 'images/screenshots/theme-customizer.png' ); ?>" class="image-50" />
		<h4><?php echo ( 'Try on New Themes' ); ?></h4>
		<p><?php echo ( 'Gone are the days of rushing to update your header, background, and the like as soon as you activate a new theme. You can now customize these options <strong>before</strong> activating a new theme. Note: This feature is available for installed themes only.' ); ?></p>

		<h4><?php echo ( 'Customize Current Theme' ); ?></h4>
		<p><?php echo ( 'Satisfy your curiosity and try on a fresh coat of paint --- you can also use the live preview mode to customize your current theme. Look for the Customize link on the Themes screen.' ); ?></p>
	</div>
</div>

<div class="changelog">
	<h3><?php echo ( 'Custom Headers' ); ?></h3>
	
	<div class="feature-section">
		<h4><?php echo ( 'Flexible Sizes' ); ?></h4>
		<p><?php echo ( 'You can decide for yourself how tall or wide your custom header image should be. From now on, themes will provide a recommended image size for custom headers rather than a fixed requirement. Note: this feature requires <a href="http://codex.wordpress.org/Custom_Headers">theme support</a>.' ); ?></p>
		<img src="<?php echo admin_url( 'images/screenshots/flex-header-1.png' ); ?>" class="image-30" />
		<img src="<?php echo admin_url( 'images/screenshots/flex-header-2.png' ); ?>" class="image-30" />
		<img src="<?php echo admin_url( 'images/screenshots/flex-header-3.png' ); ?>" class="image-30" />
	</div>

	<div class="feature-section images-stagger-right">
		<img src="<?php echo admin_url( 'images/screenshots/flex-header-media-library.png' ); ?>" class="image-50" />
		<h4><?php echo ( 'Choose from Media Library' ); ?></h4>
		<p><?php echo ( 'Tired of re-uploading the same custom header image every time you check out a new theme? Now you can choose header images from your media library for easier customization.' ); ?></p>
	</div>
</div>

<div class="changelog">
	<h3><?php echo ( 'Better Captions' ); ?></h3>

	<div class="feature-section images-stagger-right">
		<img src="<?php echo admin_url( 'images/screenshots/captions-1.png' ); ?>" class="image-30" />
		<img src="<?php echo admin_url( 'images/screenshots/captions-2.png' ); ?>" class="image-30" />
		<h4><?php echo ( 'HTML Support' ); ?></h4>
		<p><?php echo ( 'Basic HTML support has been added to the caption field in the image uploader. This allows you to add links --- great for photo credits or licensing details --- and basic formatting such as bold and italicized text.' ); ?></p>
	</div>
</div>

<div class="changelog">
	<h3><?php echo ( 'Under the Hood' ); ?></h3>

	<div class="feature-section three-col">
		<div>
			<h4><?php echo ( 'Themes API' ); ?></h4>
			<p><?php echo ( 'WP_Theme, wp_get_themes(), wp_get_theme(). Faster, uses less memory, make uses of persistent caching.' ); ?></p>
			
			<h4><?php echo ( 'Faster Main Query' ); ?></h4>
			<p><?php echo ( 'Post query optimized to avoid table scans.' ); ?></p>
		</div>
		<div>
			<h4><?php echo ( 'Custom Header and Backound API' ); ?></h4>
			<p><?php  echo ( 'Custom header and background API relocated into the theme support API.' ); ?></p>
			
			<h4><?php echo ( 'Faster I18N' ); ?></h4>
			<p><?php echo ( 'The number of strings loaded on the front end were greatly reduced, resulting in faster front page load times for I18N users.' ); ?></p>
		</div>
		<div class="last-feature">
			<h4><?php echo ( 'WP_Screen API' ); ?></h4>
			<p><?php echo ( 'More methods, more convenience!' ); ?></p>
			
			<h4><?php echo ( 'External Libraries' ); ?></h4>
			<p><?php echo ( 'jQuery, jQuery UI, TinyMCE, Plupload, PHPMailer, SimplePie, and other libraries were updated. jQuery UI Touch Punch was introduced.' ); ?></p>
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
