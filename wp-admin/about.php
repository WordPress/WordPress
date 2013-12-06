<?php
/**
 * About This Version administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

$title = __( 'About' );

list( $display_version ) = explode( '-', $wp_version );

wp_enqueue_script( 'about' );

include( ABSPATH . 'wp-admin/admin-header.php' );
?>
<div class="wrap about-wrap">

<h1><?php printf( __( 'Welcome to WordPress %s' ), $display_version ); ?></h1>

<div class="about-text"><?php printf( __( 'Thank you for updating to WordPress 3.8! We&rsquo;re happy to bring you the most beautiful WordPress yet.' ), $display_version ); ?></div>

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
	<h2 class="about-headline-callout"><?php echo ( 'Introducing a new, modern admin design' ); ?></h2>
	<img class="about-overview-img" src="<?php echo admin_url( 'images/about-overview.png' ); ?>" />

	<div class="feature-section col three-col about-updates">
		<div class="col-1">
			<p style="margin-top: 20px; background-color: grey; padding: 1em; color: white; min-height: 150px;">Image</p>
			<h3><?php echo ( 'Modern aesthetics' ); ?></h3>
			<p><?php echo ( 'Goodbye decoration, hello simplicity. We removed extraneous details, focusing on a cleaner, more streamlined admin design.' ); ?></p>
		</div>
		<div class="col-2">
			<p style="margin-top: 20px; background-color: grey; padding: 1em; color: white; min-height: 150px;">Image</p>
			<h3><?php echo ( 'Improved typography' ); ?></h3>
			<p><?php echo ( 'You might notice the type is a little bit bigger. We improved the typography, crafting a better reading experience.' ); ?></p>
		</div>
		<div class="col-3 last-feature">
			<p style="margin-top: 20px; background-color: grey; padding: 1em; color: white; min-height: 150px;">Image</p>
			<h3><?php echo ( 'Higher contrast' ); ?></h3>
			<p><?php echo ( 'With bigger typography and both high and low contrast color schemes, our new admin design is great for users of all ages.' ); ?></p>
		</div>
	</div>
</div>

<hr>

<!-- Image example -->
<!-- <img alt="" src="<?php echo admin_url( 'images/about-search-2x.png' ); ?>" /> --> 

<div class="changelog">
	<div class="feature-section col two-col">
		<div>
			<h3><?php echo ( 'Take WordPress with you anywhere with our responsive design' ); ?></h3>
			<p><?php echo ( 'The WordPress admin is now completely responsive: you can work on your website easily from your smartphone or tablet. The full power of WordPress is at your fingertips, even when you’re on the go.' ); ?></p>
			<h4><?php echo ( 'Naturally HiDPI' ); ?></h4>
			<p><?php echo ( 'No more blurry edges &#8212; with the inclusion of vector icons and graphics, the admin is now entirely HiDPI, so you get the best viewing experience no matter what kind of computer or mobile device you use.' ); ?></p>
		</div>
		<div class="last-feature about-colors-img">
			<img src="<?php echo admin_url( 'images/about-colors.png' ); ?>" />
		</div>
	</div>
</div>

<hr class="flushtopdivider">

<div class="changelog about-colors">
	<div class="feature-section col one-col">
		<div>
			<h3><?php echo ( 'Now with more color' ); ?></h3>
			<p><?php echo ( 'Your admin is not longer monochromatic &#8212; we&#8217;ve brought some more color to keep it looking fresh. You now have the option of four different default color schemes.' ); ?></p>
			<p><?php echo ( 'Try them out below:' ); ?></p>
			<img src="https://i.cloudup.com/NBlGusRk0H.png" style="border: 2px solid red; max-width: 99%; margin: 0;" />
			<p><?php echo ( 'You can change your color scheme at any time from your profile page.' ); ?></p>
		</div>
	</div>
</div>

<hr>

<div class="changelog">
	<div class="feature-section col two-col">
		<div>
			<h3><?php echo ( 'A new theme experience' ); ?></h3>
			<p><?php echo ( 'A sleeker, faster, and more visual organization of your themes that is responsive.' ); ?></p>
			<h4><?php echo ( 'Browse better' ); ?></h4>
			<p><?php echo ( 'Enjoy a focused experience with theme screenshots at the center. Quickly search through your themes or add new ones.' ); ?></p>
			<h4><?php echo ( 'Dive into the details' ); ?></h4>
			<p><?php echo ( 'Expand a theme to see more information and preview it. Use the arrow navigation to quickly swift through your themes.' ); ?></p>
			<h4><?php echo ( 'Easier updates' ); ?></h4>
			<p><?php echo ( 'Identify immediately when a theme update is available.' ); ?></p>
		</div>
		<div class="last-feature about-themes-img">
			<img src="<?php echo admin_url( 'images/about-themes.png' ); ?>" />
		</div>
	</div>
</div>

<hr class="flushtopdivider">

<div class="changelog">
	<h2 class="about-headline-callout"><?php echo ( 'Twenty Fourteen, a sleek new magazine theme' ); ?></h2>
	<img src="<?php echo admin_url( 'images/about-twentyfourteen.png' ); ?>" />

	<div class="feature-section col one-col">
		<div>
			<h3><?php echo ( 'Our new default theme lets you create a responsive magazine website with an elegant, modern design.' ); ?></h3>
			<p><?php echo ( 'Feature your favorite homepage content in either a grid or a slider. Use the three widget areas to customize your website, and change your content&#8217;s layout with a full width page template and a contributor page to show of your authors.' ); ?></p>
			<p><?php echo ( 'Creating a magazine website with WordPress has never been easier.' ); ?></p>
		</div>
	</div>
</div>

<hr>

<div class="changelog">
	<h3><?php echo ( 'Under the Hood' ); ?></h3>

	<div class="feature-section col three-col">
		<div>
			<h4><?php echo ( 'Meta query fixes' ); ?></h4>
			<p><?php echo ( 'Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Vestibulum id ligula porta felis.' ); ?></p>
		</div>
		<div>
			<h4><?php echo ( 'Automated RTL styles' ); ?></h4>
			<p><?php echo ( 'Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Vestibulum id ligula porta felis.' ); ?></p>
		</div>
		<div class="last-feature">
			<h4><?php echo ( 'Improved customizer' ); ?></h4>
			<p><?php echo ( 'Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Vestibulum id ligula porta felis.' ); ?></p>
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
