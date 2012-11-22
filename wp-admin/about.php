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

<div class="about-text"><?php printf( ( 'Thank you for updating to the latest version! WordPress %s helps make your web publishing experience easier, faster, and more enjoyable than ever. Welcome aboard.' ), $display_version ); ?></div>

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
	<h3><?php echo ( 'Media Management' ); ?></h3>

	<div class="feature-section three-col">
		<img alt="" src="<?php echo esc_url( admin_url( 'images/screenshots/about-media.png' ) ); ?>" class="image-100" />

		<div>
			<h4><?php echo ( 'Easier interface' ); ?></h4>
			<p><?php echo ( 'Adding media to your posts is more streamlined than ever. It\'s a breeze to insert, align, caption, and link to media within your posts.' ); ?></p>
		</div>
		<div>
			<h4><?php echo ( 'Picturesque galleries' ); ?></h4>
			<p><?php echo ( 'Adding image galleries is easier with drag and drop reordering, inline caption editing, and simplified controls for the gallery layout.' ); ?></p>
		</div>
		<div class="last-feature">
			<h4><?php echo ( 'Attachment page editor' ); ?></h4>
			<p><?php echo ( 'You can crop, rotate and resize images right in the Media Library? There are now formatting controls for content on your attachment pages.' ); ?></p>
		</div>
	</div>
</div>

<div class="changelog">
	<h3><?php echo ( 'Smoother experience' ); ?></h3>

	<div class="feature-section">
		<h4><?php echo ( 'Simplified settings, better buttons, easier controls' ); ?></h4>
		<p><?php echo ( 'WordPress always aims to stay out of the way, and let you get on with publishing your content. You\'ll find simplified settings screens, more aesthetically pleasing buttons, and easier controls such as the improved color picker. They\'re the little details we hope you\'ll never notice.' ); ?></p>
		<div class="three-col-images">
			<img alt="" src="<?php echo esc_url( admin_url( 'images/screenshots/about-tinymce.png' ) ); ?>" class="image-30 first-feature" />
			<img alt="" src="<?php echo esc_url( admin_url( 'images/screenshots/about-buttons.png' ) ); ?>" class="image-30" />
			<img alt="" src="<?php echo esc_url( admin_url( 'images/screenshots/about-color-picker.png' ) ); ?>" class="image-30 last-feature" />
		</div>
	</div>
</div>

<div class="changelog">
	<h3><?php echo ( 'Twenty Twelve theme' ); ?></h3>

	<div class="feature-section images-stagger-right">
		<img alt="" src="<?php echo esc_url( admin_url( 'images/screenshots/about-twenty-twelve.png' ) ); ?>" class="image-66" />
		<h4><?php echo ( 'Beauty in its simplicity' ); ?></h4>
		<p><?php echo ( 'Twenty Twelve is an elegant, readable, and fully responsive theme that makes your site content look its best on any device.' ); ?></p>
		<p><?php echo ( 'Make the design of your site fully yours with a custom menu, custom header image, and custom background color or image.' ); ?></p>
	</div>
</div>

<div class="changelog">
	<h3><?php echo ( 'HiDPI Admin' ); ?></h3>

	<div class="feature-section images-stagger-right">
		<img alt="" src="<?php echo esc_url( admin_url( 'images/screenshots/about-hidef.png' ) ); ?>" class="image-66" />
		<h4><?php echo ( 'Retina all the things' ); ?></h4>
		<p><?php echo ( 'WordPress now looks as beautiful on HiDPI devices as native applications do. Icons, buttons, avatars and theme screenshots (where supported) are crystal clear and full of detail.' ); ?></p>
	</div>
</div>

<div class="changelog">
	<h3><?php echo ( 'Better Accessibility' ); ?></h3>

	<div class="feature-section images-stagger-right">
		<img alt="" src="<?php echo esc_url( admin_url( 'images/screenshots/about-accessibility2.png' ) ); ?>" class="image-30" />
		<img alt="" src="<?php echo esc_url( admin_url( 'images/screenshots/about-accessibility1.png' ) ); ?>" class="image-30" />
		<h4><?php echo ( 'For everyone' ); ?></h4>
		<p><?php echo ( 'WordPress has wider support for more devices than ever before. Users of screenreaders, touch devices, and mouse-less devices will all see improvements to ease of use and accessibility.' ); ?></p>
		<?php //TODO: Link to new Codex page listing the accessibility features of WordPress. ?>
	</div>
</div>

<div class="changelog">
	<h3><?php echo ( 'More Embed Support' ); ?></h3>

	<div class="feature-section">
		<h4><?php echo ( 'Instagram, SlideShare, and SoundCloud' ); ?></h4>
		<p><?php echo ( 'You can now embed content from Instagram, SlideShare, and SoundCloud without having to mess around copying and pasting embed codes. Just paste a URL from any of these sites onto its own line in your post and the content will automatically be embedded for you.' ); ?></p>
		<p><?php printf( ( 'This works for several other popular sites, too, such as YouTube, Flickr, and Vimeo. For more, see the Codex article on <a href="%s">Embeds</a>.' ), ( 'http://codex.wordpress.org/Embeds' ) ); ?></p>
		<div class="three-col-images">
			<img alt="" src="<?php echo esc_url( admin_url( 'images/screenshots/about-instagram.png' ) ); ?>" class="image-30 first-feature" />
			<img alt="" src="<?php echo esc_url( admin_url( 'images/screenshots/about-slideshare.png' ) ); ?>" class="image-30" />
			<img alt="" src="<?php echo esc_url( admin_url( 'images/screenshots/about-soundcloud.png' ) ); ?>" class="image-30 last-feature" />
		</div>
	</div>
</div>

<?php if ( current_user_can( 'install_plugins' ) ) { ?>

	<div class="changelog">
		<h3><?php echo ( 'Plugin Favorites' ); ?></h3>

		<div class="feature-section images-stagger-right">
			<img alt="" src="<?php echo esc_url( admin_url( 'images/screenshots/about-favorite-plugins.png' ) ); ?>" class="image-66" />
			<h4><?php echo ( 'Which plugins do you love?' ); ?></h4>
			<p><?php echo ( 'Wouldn\'t it be great to see a list of all your favorite plugins right in the Plugins menu in WordPress? Well, now you can.' ); ?></p>
			<p><?php printf( ( 'If you have marked plugins as favorites on WordPress.org, you can browse them from the Favorites tab on the <a href="%s">Install Plugins</a> screen.' ), admin_url( 'plugin-install.php?tab=favorites' ) ); ?></p>
		</div>
	</div>

<?php } ?>

<div class="changelog">
	<h3><?php echo ( 'Under the Hood' ); ?></h3>

	<div class="feature-section three-col">
		<div>
			<h4><?php echo ( 'Meta Query Additions' ); ?></h4>
			<p><?php echo ( 'The <code>WP_Comment_Query</code> and <code>WP_User_Query</code> classes now support meta queries just like <code>WP_Query</code>.' ); ?></p>

			<h4><?php echo ( 'Multisite Improvements' ); ?></h4>
			<p><?php echo ( '<code>switch_to_blog()</code> no longer obliterates the object cache, greatly improving performance. For new sites, Multisite now works when WordPress is installed in a subdirectory.' ); ?></p>
		</div>
		<div>
			<h4><?php echo ( 'WP_Post' ); ?></h4>
			<p><?php echo ( 'Post objects are now instances of the new <code>WP_Post</code> class which improves performance by loading selected properties on demand.' ); ?></p>

			<h4><?php echo ( 'XML-RPC API' ); ?></h4>
			<p><?php printf( ( 'The <a href="%s">WordPress API</a> is now enabled by default and enjoys the same security as the rest of core. It supports fetching users, profiles, post revisions, and searching posts.' ), ( 'http://codex.wordpress.org/XML-RPC_WordPress_API' ) ); ?></p>
		</div>
		<div class="last-feature">
			<h4><?php echo ( 'Image Editing API Abstractions' ); ?></h4>
			<p><?php echo ( 'The <code>WP_Image_Editor</code> class abstracts image editing functionality such as cropping and scaling, and uses ImageMagick when available.' ); ?></p>

			<h4><?php echo ( 'External Libraries' ); ?></h4>
			<p><?php printf( ('WordPress now includes the <a href="%1$s">Underscore</a> and <a href="%2$s">Backbone</a> JavaScript libraries. TinyMCE, jQuery, jQuery UI, jCrop, and SimplePie have all been updated to the latest versions.' ), 'http://underscorejs.org/', 'http://backbonejs.org/' ); ?></p>
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
