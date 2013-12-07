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

<h1><?php printf( __( 'Welcome to WordPress&#160;%s' ), $display_version ); ?></h1>

<div class="about-text"><?php printf( __( 'Thank you for updating to WordPress %s, the most beautiful WordPress&#160;yet.' ), $display_version ); ?></div>

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
	<h2 class="about-headline-callout"><?php echo ( 'Introducing a modern new&#160;design' ); ?></h2>
	<img class="about-overview-img" src="<?php echo admin_url( 'images/about-overview.png' ); ?>" />
	<div class="feature-section col three-col about-updates">
		<div class="col-1">
			<img src="images/about-modern-aesthetics.png" />
			<h3><?php echo ( 'Modern aesthetic' ); ?></h3>
			<p><?php echo ( 'The new admin has a fresh, uncluttered design that puts clarity and simplicity ahead of visual flourishes.' ); ?></p>
		</div>
		<div class="col-2">
			<img src="images/about-typography.png" />
			<h3><?php echo ( 'Clean typography' ); ?></h3>
			<p><?php echo ( 'Open Sans is Open Source. Our new typography is simple, friendly, and optimized for web and mobile interfaces.' ); ?></p>
		</div>
		<div class="col-3 last-feature">
			<img src="images/about-contrast.png" />
			<h3><?php echo ( 'Refined contrast' ); ?></h3>
			<p><?php echo ( 'What good is beautiful design if you can&#8217;t see it? Improved contrast gives you a better reading experience.' ); ?></p>
		</div>
	</div>
</div>

<hr class="flush-top">

<div class="changelog">
	<div class="feature-section col two-col">
		<div>
			<h3><?php echo ( 'WordPress on every&#160;device' ); ?></h3>
			<p><?php echo ( 'Whether you&#8217;re on your smartphone or tablet, your notebook or desktop, WordPress looks great on every device. Now you can update your website wherever you are.' ); ?></p>
			<h4><?php echo ( 'High definition is here' ); ?></h4>
			<p><?php echo ( 'WordPress is sharper than ever; vector icons mean no more blurry edges. You get the best viewing experience no matter what type of device you use.' ); ?></p>
		</div>
		<div class="last-feature about-colors-img">
			<img src="<?php echo admin_url( 'images/about-colors.png' ); ?>" />
		</div>
	</div>
</div>

<hr class="flush-top">

<div class="changelog about-colors">
	<div class="feature-section col one-col">
		<div>
			<h3><?php echo ( 'Pick a color' ); ?></h3>
			<p><?php echo ( 'We&#8217;ve included four color schemes so that you can pick your favorite. Choose from any of the schemes below to change it in an instant.' ); ?></p>
			<?php $user_id = get_current_user_id(); ?>
			<?php if ( count($_wp_admin_css_colors) > 1 && has_action('admin_color_scheme_picker') ) :?>
				<?php 
				wp_nonce_field('update-user_' . $user_id);
				/** This action is documented in wp-admin/user-edit.php */
				do_action( 'admin_color_scheme_picker' ); 
				?>
			<?php else : ?>
				<img src="<?php echo admin_url( 'images/about-color-schemes.png' ); ?>" />
			<?php endif; ?>
			<p><?php printf( ( 'To change your color scheme later, just <a href="%1$s">visit your profile settings</a>.' ), get_edit_profile_url( $user_id ) ); ?></p>
		</div>
	</div>
</div>

<hr class="flush-top">

<div class="changelog">
	<div class="feature-section col two-col">
		<div>
			<h3><?php echo ( 'A new theme experience' ); ?></h3>
			<p><?php echo ( 'Finding and installing the right theme has never been easier.' ); ?></p>
			<h4><?php echo ( 'Better browsing' ); ?></h4>
			<p><?php echo ( 'Focus is placed on what&#8217;s important - your theme&#8217;s design. Search through your themes at a glance and add new ones with a click.' ); ?></p>
			<h4><?php echo ( 'Dive into the details' ); ?></h4>
			<p><?php echo ( 'If you need information about any of your themes just click to discover more. Sit back and use your keyboard&#8217;s navigation arrows to flip through every theme you&#8217;ve got.' ); ?></p>
			<h4><?php echo ( 'Stay updated' ); ?></h4>
			<p><?php echo ( 'You can tell in an instant if a theme needs updated and, like so many things in WordPress, updating it takes just a second.' ); ?></p>
		</div>
		<div class="last-feature about-themes-img">
			<img src="<?php echo admin_url( 'images/about-themes.png' ); ?>" />
		</div>
	</div>
</div>

<hr class="flush-top">

<div class="changelog">
	<h2 class="about-headline-callout"><?php echo ( 'Twenty Fourteen, a sleek new magazine&#160;theme' ); ?></h2>
	<img src="<?php echo admin_url( 'images/about-twentyfourteen.png' ); ?>" />

	<div class="feature-section col one-col center-col">
		<div>
			<h3><?php echo ( 'Turn your blog into a&#160;magazine' ); ?></h3>
			<p><?php echo ( 'With a striking design that does not compromise on our trademark simplicity, Twenty Fourteen is our boldest default theme. Choose a grid or a slider to display featured content on your homepage. Customize your homepage with three widget areas or change your layout with two page templates.' ); ?></p>
			<p><?php echo ( 'Creating a magazine website with WordPress has never been easier.' ); ?></p>
		</div>
	</div>
</div>

<hr class="flush-top">

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
