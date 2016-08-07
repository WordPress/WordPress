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

/**
 * Replaces the height and width attributes with values for full size.
 *
 * wp_video_shortcode() limits the width to 640px.
 *
 * @since 4.6.0
 * @ignore
 *
 * @param $output Video shortcode HTML output.
 * @return string Filtered HTML content to display video.
 */
function _wp_override_admin_video_width_limit( $output ) {
	return str_replace( array( '640', '384' ), array( '1050', '630' ), $output );
}

$video_url = 'https://videopress.com/embed/scFdjVo6?hd=true';
$locale    = str_replace( '_', '-', get_locale() );
list( $locale ) = explode( '-', $locale );
if ( 'en' !== $locale ) {
	$video_url = add_query_arg( 'defaultLangCode', $locale, $video_url );
}

$title = __( 'About' );

list( $display_version ) = explode( '-', $wp_version );

include( ABSPATH . 'wp-admin/admin-header.php' );
?>
	<div class="wrap about-wrap">
		<h1><?php printf( __( 'Welcome to WordPress&nbsp;%s' ), $display_version ); ?></h1>

		<p class="about-text"><?php printf( ( 'Thank you for updating to the latest version. WordPress %s changes a lot behind the scenes to make your WordPress experience even better!' ), $display_version ); ?></p>
		<div class="wp-badge"><?php printf( __( 'Version %s' ), $display_version ); ?></div>

		<h2 class="nav-tab-wrapper wp-clearfix">
			<a href="about.php" class="nav-tab nav-tab-active"><?php _e( 'What&#8217;s New' ); ?></a>
			<a href="credits.php" class="nav-tab"><?php _e( 'Credits' ); ?></a>
			<a href="freedoms.php" class="nav-tab"><?php _e( 'Freedoms' ); ?></a>
		</h2>

		<div class="headline-feature feature-video" style="background-color:#191E23;">
			<?php /*
			<iframe width="1050" height="591" src="<?php echo esc_url( $video_url ); ?>" frameborder="0" allowfullscreen></iframe>
			<script src="https://videopress.com/videopress-iframe.js"></script>
			*/ ?>
		</div>

		<hr>

		<div class="streamlined-updates feature-section one-col">
			<h2><?php echo( 'Streamlined Updates' ); ?></h2>
			<p><?php echo( 'Inline Updates replaces progress updates with a simpler and more straight forward experience when installing, updating, and deleting plugins and themes.' ); ?></p>
			<?php
			if ( ! wp_is_mobile() ) {
				add_filter( 'wp_video_shortcode', '_wp_override_admin_video_width_limit' );
				echo wp_video_shortcode( array(
					'mp4'      => 'https://cldup.com/NlOEbKLT_6.mp4',
					'ogv'      => 'https://cldup.com/0XzDZMlYwb.ogv',
					'webm'     => 'https://cldup.com/ngOx9w9VlE.webm',
					'poster'   => 'https://cldup.com/c0kfjoVcFo.png',
					'loop'     => true,
					'autoplay' => true,
					'width'    => 1050,
					'height'   => 630,
					'class'    => 'wp-video-shortcode feature-video',
				) );
				remove_filter( 'wp_video_shortcode', '_wp_override_admin_video_width_limit' );
			} else {
				echo '<img src="https://cldup.com/c0kfjoVcFo.png" alt="" srcset=""/>';
			}
			?>
		</div>

		<hr />

		<div class="native-fonts feature-section one-col">
			<h2><?php echo( 'Native Fonts' ); ?></h2>
			<p><?php echo( 'The WordPress dashboard now uses the fonts that come with your device, allowing it to load faster and feel more like a native application.' ); ?></p>
			<img src="https://cldup.com/bCuNzRdtHm.png" alt="" srcset=""/>
		</div>

		<hr>

		<div class="feature-section two-col">
			<h2><?php echo( 'Editor Improvements' ); ?></h2>
			<div class="col">
				<img src="https://cldup.com/Kz3FL4I9iB.png" alt="" srcset="https://cldup.com/Kz3FL4I9iB.png 1000w, https://cldup.com/Kz3FL4I9iB.png 800w, https://cldup.com/Kz3FL4I9iB.png 680w, https://cldup.com/Kz3FL4I9iB.png 560w, https://cldup.com/Kz3FL4I9iB.png 400w, https://cldup.com/Kz3FL4I9iB.png 280w" sizes="(max-width: 500px) calc(100vw - 40px), (max-width: 781px) calc((100vw - 70px) * .466), (max-width: 959px) calc((100vw - 116px) * .469), (max-width: 1290px) calc((100vw - 240px) * .472), 496px"/>
				<h3><?php echo( 'Broken Link Checker' ); ?></h3>
				<p><?php echo( 'Links are the foundation of the Internet&colon; when they break, so does the web. Now when you edit a post, you instantly see when a link you add is broken.' ); ?></p>
			</div>
			<div class="col">
				<img src="https://cldup.com/fxzqZFrDxo.png" alt="" srcset="https://cldup.com/fxzqZFrDxo.png 1000w, https://cldup.com/fxzqZFrDxo.png 800w, https://cldup.com/fxzqZFrDxo.png 680w, https://cldup.com/fxzqZFrDxo.png 560w, https://cldup.com/fxzqZFrDxo.png 400w, https://cldup.com/fxzqZFrDxo.png 280w" sizes="(max-width: 500px) calc(100vw - 40px), (max-width: 781px) calc((100vw - 70px) * .466), (max-width: 959px) calc((100vw - 116px) * .469), (max-width: 1290px) calc((100vw - 240px) * .472), 496px"/>
				<h3><?php echo( 'Content Recovery' ); ?></h3>
				<p><?php echo( 'As you type, WordPress saves your content to the browser. Recovering saved content is even easier with WordPress 4.6.' ); ?></p>
			</div>
		</div>

		<hr />

		<div class="changelog">
			<h2><?php echo( 'Under the Hood' ); ?></h2>

			<div class="under-the-hood three-col">
				<div class="col">
					<h3><?php echo( 'Performance Everywhere' ); ?></h3>
					<p><?php echo( 'A brand new technology is going to boost your site&#8217;s performance. Resource hints allow browsers to perform background tasks, WordPress 4.6 adds them automatically for your styles and scripts.' ); ?></p>
				</div>
				<div class="col">
					<h3><?php echo( 'Robust Requests' ); ?></h3>
					<p><?php echo( 'The HTTP API now leverages the Requests library, improving HTTP standard support and adding case-insensitive headers, parallel HTTP requests, and support for Internationalized Domain Names.' ); ?></p>
				</div>
				<div class="col">
					<h3><?php
						/* translators: 1: WP_Term_Query, 2: WP_Post_Type */
						printf( ( '%1$s and %2$s' ), '<code>WP_Term_Query</code>', '<code>WP_Post_Type</code>' );
					?></h3>
					<p><?php
						printf(
							/* translators: 1: WP_Term_Query, 2: WP_Post_Type */
							( 'A new %1$s class adds flexibility to query term information and a new %2$s object makes interacting with post types more predictable and intuitive in code.' ),
							'<code>WP_Term_Query</code>',
							'<code>WP_Post_Type</code>'
						);
					?></p>
				</div>
			</div>

			<div class="under-the-hood three-col">
				<div class="col">
					<h3><?php echo( 'Meta Registration API' ); ?></h3>
					<p><?php echo( 'The Meta Registration API has been expanded to support types, descriptions, and REST API visibility.' ); ?></p>
				</div>
				<div class="col">
					<h3><?php echo( 'Timely Translations' ); ?></h3>
					<p><?php echo( 'Preference is now given to <a href="https://translate.wordpress.org/">community translations</a> for plugins and themes served from WordPress.org which allows WordPress to load them just-in-time.' ); ?></p>
				</div>
				<div class="col">
					<h3><?php echo( 'JavaScript Library Updates' ); ?></h3>
					<p><?php echo( 'Masonry 3.3.2, imagesLoaded 3.2.0, MediaElement.js 2.22.0, TinyMCE 4.4.1, and Backbone.js 1.3.3 are bundled.' ); ?></p>
				</div>
			</div>

			<div class="under-the-hood two-col">
				<div class="col">
					<h3><?php echo( 'Customizer APIs for Setting Validation and Notifications' ); ?></h3>
					<p><?php echo( 'Settings now have an API for enforcing validation constraints. Likewise Customizer controls now support notifications which are used to display validation errors instead of failing silently.' ); ?></p>
				</div>
				<div class="col">
					<h3><?php echo( 'Multisite, now faster than ever' ); ?></h3>
					<p><?php
						/* translators: 1: WP_Site_Query, 2: WP_Network_Query */
						printf(
							( 'Cached and comprehensive site queries improve your multisite admin experience. Also, %1$s and %2$s make crafting robust queries simpler.' ),
							'<code>WP_Site_Query</code>',
							'<code>WP_Network_Query</code>'
						);
						?></p>
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
