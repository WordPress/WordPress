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

<div class="about-text"><?php printf( __( 'Thank you for updating to the latest version! WordPress %s is more polished and enjoyable than ever before. We hope you like it.' ), $display_version ); ?></div>

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
         '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.', 12 ), '3.5.2', number_format_i18n( 12 ) ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'http://codex.wordpress.org/Version_3.5.2' ); ?>
 	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
         '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.', 37 ), '3.5.1', number_format_i18n( 37 ) ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'http://codex.wordpress.org/Version_3.5.1' ); ?>
 	</p>
</div>

<div class="changelog">
	<h3><?php _e( 'New Media Manager' ); ?></h3>

	<div class="feature-section col two-col">
		<img alt="" src="<?php echo esc_url( admin_url( 'images/screenshots/about-media.png' ) ); ?>" class="image-100" />

		<div>
			<h4><?php _e( 'Beautiful Interface' ); ?></h4>
			<p><?php _e( 'Adding media has been streamlined with an all-new experience, making it a breeze to upload files and place them into your posts.' ); ?></p>
		</div>
		<div class="last-feature">
			<h4><?php _e( 'Picturesque Galleries' ); ?></h4>
			<p><?php _e( 'Creating image galleries is faster with drag and drop reordering, inline caption editing, and simplified controls for layout.' ); ?></p>
		</div>
	</div>
</div>

<div class="changelog">
	<h3><?php _e( 'New Default Theme' ); ?></h3>

	<div class="feature-section images-stagger-right">
		<img alt="" src="<?php echo esc_url( admin_url( 'images/screenshots/about-twenty-twelve.png' ) ); ?>" class="image-66" />
		<h4><?php _e( 'Introducing Twenty Twelve' ); ?></h4>
		<p><?php _e( 'The newest default theme for WordPress is simple, flexible, and elegant.' ); ?></p>
		<p><?php _e( 'What makes it really shine are the design details, like the gorgeous Open Sans typeface and a fully responsive design that looks great on any device.' ); ?></p>
		<p><?php _e( 'Naturally, Twenty Twelve supports all the theme features you’ve come to know and love, but it is also designed to be as great for a website as it is for a blog.' ); ?></p>
	</div>
</div>

<div class="changelog">
	<h3><?php _e( 'Retina Ready' ); ?></h3>

	<div class="feature-section images-stagger-right">
		<img alt="" src="<?php echo esc_url( admin_url( 'images/screenshots/about-retina.png' ) ); ?>" class="image-66" />
		<h4><?php _e( 'So Sharp You Can&#8217;t See the Pixels' ); ?></h4>
		<p><?php _e( 'The WordPress dashboard now looks beautiful on high-resolution screens like those found on the iPad, Kindle Fire HD, Nexus 10, and MacBook Pro with Retina Display. Icons and other visual elements are crystal clear and full of detail.' ); ?></p>
	</div>
</div>

<div class="changelog">
	<h3><?php _e( 'Smoother Experience' ); ?></h3>

	<div class="feature-section images-stagger-right">
		<img alt="" src="<?php echo esc_url( admin_url( 'images/screenshots/about-color-picker.png' ) ); ?>" class="image-30" />
		<h4><?php _e( 'Better Accessibility' ); ?></h4>
		<p><?php _e( 'WordPress supports more usage modes than ever before. Screenreaders, touch devices, and mouseless workflows all have improved ease of use and accessibility.' ); ?></p>

		<h4><?php _e( 'More Polish' ); ?></h4>
		<p><?php _e( 'A number of screens and controls have been refined. For example, a new color picker makes it easier for you to choose that perfect shade of blue.' ); ?></p>
	</div>
</div>

<div class="changelog">
	<h3><?php _e( 'Under the Hood' ); ?></h3>

	<div class="feature-section col three-col">
		<div>
			<h4><?php _e( 'Meta Query Additions' ); ?></h4>
			<p><?php _e( 'The <code>WP_Comment_Query</code> and <code>WP_User_Query</code> classes now support meta queries just like <code>WP_Query.</code> Meta queries now support querying for objects without a particular meta key.' ); ?></p>
		</div>
		<div>
			<h4><?php _e( 'Post Objects' ); ?></h4>
			<p><?php _e( 'Post objects are now instances of a <code>WP_Post</code> class, which improves performance by loading selected properties on demand.' ); ?></p>
		</div>
		<div class="last-feature">
			<h4><?php _e( 'Image Editing API' ); ?></h4>
			<p><?php _e( 'The <code>WP_Image_Editor</code> class abstracts image editing functionality such as cropping and scaling, and uses ImageMagick when available.' ); ?></p>
		</div>
	</div>

	<div class="feature-section col three-col">
		<div>
			<h4><?php _e( 'Multisite Improvements' ); ?></h4>
			<p><?php _e( '<code>switch_to_blog()</code> is now significantly faster and more reliable.' ); ?></p>
		</div>
		<div>
			<h4><?php _e( 'XML-RPC API' ); ?></h4>
			<p><?php printf( __( 'The <a href="%s">WordPress API</a> is now always enabled, and supports fetching users, editing profiles, managing post revisions, and searching posts.' ), __( 'http://codex.wordpress.org/XML-RPC_WordPress_API' ) ); ?></p>
		</div>
		<div class="last-feature">
			<h4><?php _e( 'External Libraries' ); ?></h4>
			<p><?php printf( __( 'WordPress now includes the <a href="%1$s">Underscore</a> and <a href="%2$s">Backbone</a> JavaScript libraries. TinyMCE, jQuery, jQuery UI, and SimplePie have all been updated to the latest versions.' ), 'http://underscorejs.org/', 'http://backbonejs.org/' ); ?></p>
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
