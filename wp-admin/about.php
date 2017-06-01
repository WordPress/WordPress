<?php
/**
 * About This Version administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

if ( ! wp_is_mobile() ) {
	wp_enqueue_style( 'wp-mediaelement' );
	wp_enqueue_script( 'wp-mediaelement' );
	wp_localize_script( 'mediaelement', '_wpmejsSettings', array(
		'pluginPath'        => includes_url( 'js/mediaelement/', 'relative' ),
		'pauseOtherPlayers' => '',
	) );
}

$video_url = 'https://videopress.com/embed/AHz0Ca46?hd=true';
$lang_code = str_replace( '_', '-', get_user_locale() );
list( $lang_code ) = explode( '-', $lang_code );
if ( 'en' !== $lang_code ) {
	$video_url = add_query_arg( 'defaultLangCode', $lang_code, $video_url );
}

$title = __( 'About' );

list( $display_version ) = explode( '-', get_bloginfo( 'version' ) );

include( ABSPATH . 'wp-admin/admin-header.php' );
?>
	<div class="wrap about-wrap">
		<h1><?php printf( __( 'Welcome to WordPress&nbsp;%s' ), $display_version ); ?></h1>

		<p class="about-text"><?php printf( __( 'Thank you for updating to the latest version! WordPress %s adds more ways for you to express yourself and represent your brand.' ), $display_version ); ?></p>
		<div class="wp-badge"><?php printf( __( 'Version %s' ), $display_version ); ?></div>

		<h2 class="nav-tab-wrapper wp-clearfix">
			<a href="about.php" class="nav-tab nav-tab-active"><?php _e( 'What&#8217;s New' ); ?></a>
			<a href="credits.php" class="nav-tab"><?php _e( 'Credits' ); ?></a>
			<a href="freedoms.php" class="nav-tab"><?php _e( 'Freedoms' ); ?></a>
		</h2>

		<div class="feature-section one-col">
			<div class="col">
				<h2><?php _e( 'An Update with You in Mind' ); ?></h2>
				<p class="lead-description"><?php _e( 'WordPress 4.8 adds some great new features. Gear up for a more intuitive WordPress!' ); ?></p>
				<p><?php _e( 'Though some updates seem minor, they&#8217;ve been built by hundreds of contributors with <em>you</em> in mind. Get ready for new features you&#8217;ll welcome like an old friend: link improvements, <em>three</em> new media widgets covering images, audio, and video, an updated text widget that supports visual editing, and an upgraded news section in your dashboard which brings in nearby and upcoming WordPress events.' ); ?></p>
			</div>
		</div>

		<hr />

		<h2><?php _e( 'Exciting Widget Updates' ); ?></h2>

		<div class="headline-feature one-col">
			<div class="col">
				<picture>
					<!-- Large image -->
					<source media="(min-width: 1050px)" srcset="https://cldup.com/-951havc3C.png" />
					<!-- Medium image -->
					<source media="(min-width: 601px)" srcset="https://cldup.com/60ktdYzv0l.png" />
					<!-- Small image -->
					<img src="https://cldup.com/mwvU0Zi5wW.png" alt="" />
				</picture>
			</div>
		</div>

		<div class="feature-section two-col">
			<div class="col">
				<h3><?php _e( 'Image Widget' ); ?></h3>
				<p><?php _e( 'Adding an image to a widget is now a simple task that is achievable for any WordPress user without needing to know code. Simply insert your image right within the widget settings. Try adding something like a headshot or a photo of your latest weekend adventure &mdash; and see it appear automatically.' );?></p>
			</div>
			<div class="col">
				<h3><?php _e( 'Video Widget' ); ?></h3>
				<p><?php _e( 'A welcome video is a great way to humanize the branding of your website. You can now add any video from the Media Library to a sidebar on your site with the new Video widget. Use this to showcase a welcome video to introduce visitors to your site or promote your latest and greatest content.' ); ?></p>
			</div>
			<div class="col">
				<h3><?php _e( 'Audio Widget' ); ?></h3>
				<p><?php _e( 'Are you a podcaster, musician, or avid blogger? Adding a widget with your audio file has never been easier. Upload your audio file to the Media Library, go to the widget settings, select your file, and you&#8217;re ready for listeners. This would be a easy way to add a more personal welcome message, too!' );?></p>
			</div>
			<div class="col">
				<h3><?php _e( 'Rich Text Widget' ); ?></h3>
				<p><?php _e( 'This feature deserves a parade down the center of town! Rich-text editing capabilities are now native for Text widgets. Add a widget anywhere and format away. Create lists, add emphasis, and quickly and easily insert links. Have fun with your newfound formatting powers, and watch what you can accomplish in a short amount of time.' ); ?></p>
			</div>
		</div>

		<hr />

		<div class="feature-section two-col">
			<div class="col">
				<h3><?php _e( 'Link Boundaries' ); ?></h3>
				<p><?php _e( 'Have you ever tried updating a link, or the text around a link, and found you can&#8217;t seem to edit it correctly? When you edit the text after the link, your new text also ends up linked. Or you edit the text in the link, but your text ends up outside of it. This can be frustrating! With link boundaries, a great new feature, the process is streamlined and your links will work well. You’ll be happier. We promise.' ); ?></p>
			</div>
			<div class="col">
				<?php
				echo wp_video_shortcode( array(
					'mp4'      => 'https://cldup.com/LHwC1lL_jC.mp4',
					'poster'   => 'https://cldup.com/wx7uMMXJor.png',
					'width'    => 1140,
					'height'   => 624,
					// 'class'    => 'wp-video-shortcode feature-video',
				) );
				?>
			</div>
		</div>

		<hr />

		<div class="feature-section two-col">
			<div class="col">
				<h3><?php _e( 'Nearby WordPress Events' ); ?></h3>

				<p><?php _e( 'Did you know that WordPress has a thriving offline community with groups meeting regularly in more than 400 cities around the world? WordPress now draws your attention to the events that help you continue improving your WordPress skills, meet friends, and, of course, publish!' ); ?></p>

				<p><?php _e( 'This is quickly becoming one of our favorite features. While you are in the dashboard (because you&#8217;re running updates and writing posts, right?) all upcoming WordCamps and WordPress Meetups &mdash; local to you &mdash; will be displayed.' ); ?>

				<p><?php _e( 'Being part of the community can help you improve your WordPress skills and network with people you wouldn&#8217;t otherwise meet. Now you can easily find your local events just by logging in to your dashboard and looking at the new Events and News dashboard widget.' ); ?>
			</div>
			<div class="col">
				<img src="https://cldup.com/GuISab3_X1.png" alt="" />
			</div>
		</div>

		<hr />

		<div class="changelog">
			<h2><?php
				printf(
					/* translators: %s: smiling face with smiling eyes emoji */
					__( 'Even More Developer Happiness %s' ),
					'&#x1F60A'
				);
			?></h2>

			<div class="under-the-hood three-col">
				<div class="col">
					<h3><a href="https://make.wordpress.org/core/2017/05/17/cleaner-headings-in-the-admin-screens/"><?php _e( 'More Accessible Admin Panel Headings' ); ?></a></h3>
					<p><?php _e( 'New CSS rules mean extraneous content (like &ldquo;Add New&rdquo; links) no longer need to be included in admin-area headings. These panel headings improve the experience for people using assistive technologies.' ); ?></p>
				</div>
				<div class="col">
					<h3><a href="https://make.wordpress.org/core/2017/05/22/removal-of-core-embedding-support-for-wmv-and-wma-file-formats/"><?php _e( 'Removal of Core Support for WMV and WMA Files' ); ?></a></h3>
					<p><?php _e( 'As fewer and fewer browsers support Silverlight, file formats which require the presence of the Silverlight plugin are being removed from core support. Files will still display as a download link, but will no longer be embedded automatically.' ); ?></p>
				</div>
				<div class="col">
					<h3><a href="https://make.wordpress.org/core/2017/05/22/multisite-focused-changes-in-4-8/"><?php _e( 'Multisite Updates' ); ?></a></h3>
					<p><?php _e( 'New capabilities have been introduced to 4.8 with an eye towards removing calls to <code>is_super_admin()</code>. Additionally, new hooks and tweaks to more granularly control site and user counts per network have been added.' ); ?></p>
				</div>
				<div class="col">
					<h3><a href="https://make.wordpress.org/core/2017/05/23/addition-of-tinymce-to-the-text-widget/"><?php _e( 'Text-Editor JavaScript API' ); ?></a></h3>
					<p><?php _e( 'With the addition of TinyMCE to the text widget in 4.8 comes a new JavaScript API for instantiating the editor after page load. This can be used to add an editor instance to any text area, and customize it with buttons and functions. Great for plugin authors!' ); ?></p>
				</div>
				<div class="col">
					<h3><a href="https://make.wordpress.org/core/2017/05/26/media-widgets-for-images-video-and-audio/"><?php _e( 'Media Widgets API' ); ?></a></h3>
					<p><?php _e( 'The introduction of a new base media widget REST API schema to 4.8 opens up possibilities for even more media widgets (like galleries or playlists) in the future. The three new media widgets are powered by a shared base class that covers most of the interactions with the media modal. That class also makes it easier to create new media widgets and paves the way for more to come.' ); ?></p>
				</div>
				<div class="col">
					<h3><a href="https://make.wordpress.org/core/2017/05/16/customizer-sidebar-width-is-now-variable/"><?php _e( 'Customizer Width Variable' ); ?></a></h3>
					<p><?php _e( 'Rejoice! New responsive breakpoints have been added to the customizer sidebar to make it wider on high-resolution screens. Customizer controls should use percentage-based widths instead of pixels.' ); ?></p>
				</div>
			</div>
		</div>

		<hr />

		<div class="return-to-dashboard">
			<?php if ( current_user_can( 'update_core' ) && isset( $_GET['updated'] ) ) : ?>
				<a href="<?php echo esc_url( self_admin_url( 'update-core.php' ) ); ?>">
					<?php is_multisite() ? _e( 'Return to Updates' ) : _e( 'Return to Dashboard &rarr; Updates' ); ?>
				</a> |
			<?php endif; ?>
			<a href="<?php echo esc_url( self_admin_url() ); ?>"><?php is_blog_admin() ? _e( 'Go to Dashboard &rarr; Home' ) : _e( 'Go to Dashboard' ); ?></a>
		</div>

	</div>
<?php

include( ABSPATH . 'wp-admin/admin-footer.php' );

// These are strings we may use to describe maintenance/security releases, where we aim for no new strings.
return;

__( 'Maintenance Release' );
__( 'Maintenance Releases' );

__( 'Security Release' );
__( 'Security Releases' );

__( 'Maintenance and Security Release' );
__( 'Maintenance and Security Releases' );

/* translators: %s: WordPress version number */
__( '<strong>Version %s</strong> addressed one security issue.' );
/* translators: %s: WordPress version number */
__( '<strong>Version %s</strong> addressed some security issues.' );

/* translators: 1: WordPress version number, 2: plural number of bugs. */
_n_noop( '<strong>Version %1$s</strong> addressed %2$s bug.',
         '<strong>Version %1$s</strong> addressed %2$s bugs.' );

/* translators: 1: WordPress version number, 2: plural number of bugs. Singular security issue. */
_n_noop( '<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bug.',
         '<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bugs.' );

/* translators: 1: WordPress version number, 2: plural number of bugs. More than one security issue. */
_n_noop( '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
         '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.' );

/* translators: %s: Codex URL */
__( 'For more information, see <a href="%s">the release notes</a>.' );
