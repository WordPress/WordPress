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
	<h3><?php echo ( 'Customizer' ); ?></h3>

	<div class="feature-section images-stagger-right">
		<img src="<?php echo admin_url( 'images/screenshots/theme-customizer.png' ); ?>" class="image-50" />
		<h4><?php echo ( 'All In One Place' ); ?></h4>
		<p><?php echo ( 'OMG, this one thing does multiple things, on one screen.' ); ?></p>

		<h4><?php echo ( 'Live Preview' ); ?></h4>
		<p><?php echo ( 'Immediate feedback on what your butchery looks like.' ); ?></p>

		<h4><?php echo ( 'Commitment Issues?' ); ?></h4>
		<p><?php echo ( 'Before activating that new theme, customize and test drive it.' ); ?></p>
		<p><?php echo ( 'Before activating that new theme, customize and test drive it. Before activating that new theme, customize and test drive it. Before activating that new theme, customize and test drive it. Before activating that new theme, customize and test drive it. Before activating that new theme, customize and test drive it. Before activating that new theme, customize and test drive it. ' ); ?></p>
	</div>
</div>

<div class="changelog">
	<h3><?php echo ( 'Custom Headers' ); ?></h3>
	
	<div class="feature-section">
		<h4><?php echo ( 'Flexible Sizes' ); ?></h4>
		<p><?php echo ( 'If your theme elects, image headers can be made at variable height and even width. Pick the size that suits you.' ); ?></p>
		<img src="<?php echo admin_url( 'images/screenshots/flex-header-1.png' ); ?>" class="image-30" />
		<img src="<?php echo admin_url( 'images/screenshots/flex-header-2.png' ); ?>" class="image-30" />
		<img src="<?php echo admin_url( 'images/screenshots/flex-header-3.png' ); ?>" class="image-30" />
	</div>

	<div class="feature-section images-stagger-right">
		<img src="<?php echo admin_url( 'images/screenshots/flex-header-media-library.png' ); ?>" class="image-50" />
		<h4><?php echo ( 'Media Library Support' ); ?></h4>
		<p><?php echo ( 'Lorem ipsum dolor sit amet consectetuer porttitor ut consectetuer mus ante. Odio at tortor pretium felis vel laoreet magnis leo Phasellus quis. Non ut volutpat nisl Lorem est sit tortor vel rutrum id. Curabitur Vestibulum neque ut nibh ante congue nunc dictum leo nibh. ' ); ?></p>
	</div>
</div>

<div class="changelog">
	<h3><?php echo ( 'Better Captions' ); ?></h3>

	<div class="feature-section images-stagger-right">
		<img src="<?php echo admin_url( 'images/screenshots/captions-1.png' ); ?>" class="image-30" />
		<img src="<?php echo admin_url( 'images/screenshots/captions-2.png' ); ?>" class="image-30" />
		<h4><?php echo ( 'HTML Support' ); ?></h4>
		<p><?php echo ( 'Lorem ipsum dolor sit amet consectetuer porttitor ut consectetuer mus ante. Odio at tortor pretium felis vel laoreet magnis leo Phasellus quis. Non ut volutpat nisl Lorem est sit tortor vel rutrum id. Curabitur Vestibulum neque ut nibh ante congue nunc dictum leo nibh. Nulla libero neque elit Nam nulla eros non justo eget pede. Pede ullamcorper metus justo adipiscing massa nulla Phasellus et nisl hendrerit.' ); ?></p>
	</div>
</div>


<div class="changelog">
	<h3><?php echo ( 'Mobile/Touch' ); ?></h3>

	<div class="feature-section">
		<h4><?php echo ( 'Drag, Swipe, Tap, Pat, Pat, Pat Happy Little Clouds' ); ?></h4>
		<p><?php echo ( 'Engage your fingers with a more touchable admin. Engage your fingers with a more touchable admin. Engage your fingers with a more touchable admin. Engage your fingers with a more touchable admin. ' ); ?></p>

		<h4><?php echo ( 'Size Responsive' ); ?></h4>
		<p><?php echo ( 'From mobile to tablet. From mobile to tablet. From mobile to tablet. From mobile to tablet. From mobile to tablet. From mobile to tablet. ' ); ?></p>

		<h4><?php echo ( 'Blog Anywhere' ); ?></h4>
		<p><?php echo ( 'Picture of hipster in cafe criticizing the coffee via his tablet. Picture of hipster in cafe criticizing the coffee via his tablet. Picture of hipster in cafe criticizing the coffee via his tablet. Picture of hipster in cafe criticizing the coffee via his tablet. Picture of hipster in cafe criticizing the coffee via his tablet. ' ); ?></p>

		<h4><?php echo ( 'Couch Couch Couch!' ); ?></h4>
		<p><?php echo ( 'Picture of happy tablet user blogging from couch while watching sitcoms. Picture of happy tablet user blogging from couch while watching sitcoms. Picture of happy tablet user blogging from couch while watching sitcoms. Picture of happy tablet user blogging from couch while watching sitcoms. ' ); ?></p>
	</div>
</div>

<div class="changelog">
	<h3><?php echo ( 'XML-RPC API' ); ?></h3>

	<div class="feature-section three-col">
		<div>
			<h4><?php echo ( 'In your About page, talking nonsense.' ); ?></h4>
			<p><?php echo ( 'Synergy!' ); ?></p>

			<h4><?php echo ( 'Remote Procedures' ); ?></h4>
			<p><?php echo ( 'Want you to call. Do not pretend you lost the number.' ); ?></p>
		</div>
		<div>
			<h4><?php echo ( 'Mobile Mobile Mobile!' ); ?></h4>
			<p><?php echo ( 'Picture of a happy mobile app user (like the one above) who has no idea that his/her phone is engaging in XML-RPC.' ); ?></p>
		</div>
		<div class="last-feature">
			<h4><?php echo ( 'Armageddon It' ); ?></h4>
			<p><?php echo ( 'Picture of a block of XML-RPC API code glimpsed over of the shoulder of a henchman in a lab coat who is getting ready to upload this doomsday snippet to every satellite over the Tri-State Area!' ); ?></p>
		</div>
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
