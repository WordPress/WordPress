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

		<p class="about-text"><?php printf( __( 'Thank you for updating to the latest version! WordPress %s is a user-focused release with updates that will give you peace of mind.' ), $display_version ); ?></p>
		<div class="wp-badge"><?php printf( __( 'Version %s' ), $display_version ); ?></div>

		<h2 class="nav-tab-wrapper wp-clearfix">
			<a href="about.php" class="nav-tab nav-tab-active"><?php _e( 'What&#8217;s New' ); ?></a>
			<a href="credits.php" class="nav-tab"><?php _e( 'Credits' ); ?></a>
			<a href="freedoms.php" class="nav-tab"><?php _e( 'Freedoms' ); ?></a>
		</h2>

		<div class="headline-feature feature-video">
			<iframe width="1050" height="591" src="<?php echo esc_url( $video_url ); ?>" frameborder="0" allowfullscreen></iframe>
			<script src="https://videopress.com/videopress-iframe.js"></script>
		</div>

		<hr />

		<div class="feature-section one-col">
			<h2><?php _e( 'An Update with End Users in Mind!' ); ?></h2>
			<p class="lead-description"><?php _e( 'WordPress 4.8 adds new some great new features: gear up for a more intuitive WordPress! Though some updates are tiny (TinyMCE, that is &mdash; see what we did there?) they&#8217;ve been developed with you in mind by hundreds of Core Contributors and Committers.' ); ?></p>
			<p><?php _e( 'Get ready for new features you&#8217;ll welcome like an old friend: link improvements, three new media widgets, an updated text widget, and an upgraded news section in your dashboard. Navigate with purpose, intuitively in and out of text links. Embrace several new media widgets covering images, audio, and video, and an enhancement to the text widget which supports visual editing.' ); ?></p>
			<blockquote>
				<p><?php _e( '&#8220;The last time a new widget was introduced, Vuvuzelas were a thing, Angry Birds started taking over phones, and WordPress stopped shipping with Kubrick. Seven years and 17 releases without new widgets were enough. Time to spice up your sidebar!&#8221;' ); ?></p>
				<p><cite><?php _e( 'The WordPress Team' ); ?></cite></p>
			</blockquote>
			<p><?php _e( 'A revamp of the dashboard news widget brings in nearby and upcoming events including Meetups and WordCamps. Never miss a WordPress Meetup or Camp near you again! ' ); ?></p>
		</div>

		<hr />

		<div class="feature-section two-col">
			<div class="col">
				<h3><?php _e( 'Link Boundaries' ); ?></h3>
				<p><?php _e( 'Have you ever tried updating a link or the text around a link, but you can&#8217;t seem to edit it correctly? You try to add a word after the link, and your new text also ends up linked. You try to add more words to a link but they end up outside the link. Frustrating! It was an annoying, confusing experience. This new feature streamlines that process. You&#8217;ll be happier. We promise.' ); ?></p>
			</div>
			<div class="col">
				<?php
				echo wp_video_shortcode( array(
					'mp4'      => 'https://s.w.org/images/core/4.7/starter-content-v1.mp4',
					'poster'   => 'https://s.w.org/images/core/4.7/starter-content.jpg?v2',
					'width'    => 1140,
					'height'   => 624,
					// 'class'    => 'wp-video-shortcode feature-video',
				) );
				?>
			</div>
		</div>

		<div class="feature-section two-col">
			<div class="col">
				<h3><?php _e( 'Image Widget' ); ?></h3>
				<?php
				echo wp_video_shortcode( array(
					'mp4'      => 'https://s.w.org/images/core/4.7/edit-shortcuts-v1.mp4',
					'poster'   => 'https://s.w.org/images/core/4.7/edit-shortcuts.jpg?v2',
					'width'    => 2520,
					'height'   => 1454,
					// 'class'    => 'wp-video-shortcode feature-video',
				) );
				?>
				<p><?php _e( 'Adding an image to a widget used to be a multistep process requiring visits to multiple screens. Now, adding an image is achievable for any WordPress user without hiring a developer. (Don&#8217;t tell them we told you that.) Simply insert your image right within the widget settings &mdash; try adding a headshot and brief bio &mdash; and see it appear&hellip; automatically.' );?></p>
			</div>
			<div class="col">
				<h3><?php _e( 'Video Widget' ); ?></h3>
				<?php
				echo wp_video_shortcode( array(
					'mp4'      => 'https://s.w.org/images/core/4.7/header-video-v1.mp4',
					'poster'   => 'https://s.w.org/images/core/4.7/header-video.jpg?v2',
					'width'    => 2520,
					'height'   => 1454,
					// 'class'    => 'wp-video-shortcode feature-video',
				) );
				?>
				<p><?php _e( 'A welcome video in a sidebar widget is a great way to humanize the branding of your website. People follow blogs from people they know and like and buy from brands they trust. Add a video to your media library and include it in your sidebar lickety-split; and you, too, can be liked and trusted instantly.' ); ?></p>
			</div>
		</div>

		<div class="feature-section two-col">
			<div class="col">
				<h3><?php _e( 'Audio Widget' ); ?></h3>
				<img src="https://s.w.org/images/core/4.7/nav-menus-760.jpg?v2" srcset="https://s.w.org/images/core/4.7/nav-menus-760.jpg?v2 760w, https://s.w.org/images/core/4.7/nav-menus-280.jpg?v2 280w, https://s.w.org/images/core/4.7/nav-menus-536.jpg?v2 536w, https://s.w.org/images/core/4.7/nav-menus-745.jpg?v2 745w" sizes="(max-width: 500px) calc(100vw - 40px), (max-width: 781px) calc((100vw - 70px) * .466), (max-width: 959px) calc((100vw - 116px) * .469), (max-width: 1290px) calc((100vw - 240px) * .472), 496px" alt="" />
				<p><?php _e( 'Are you a podcaster or musician? Adding a widget with your audio file has never been easier. Upload your audio file to the media library, go to the widget settings, select your file, and you&#8217;re done. This would be a great way to add a more intimate welcome message, too!' );?></p>
			</div>
			<div class="col">
				<h3><?php _e( 'Rich Text Widget' ); ?></h3>
				<img src="https://s.w.org/images/core/4.7/css-760.jpg?v2" srcset="https://s.w.org/images/core/4.7/css-760.jpg?v2 760w, https://s.w.org/images/core/4.7/css-280.jpg?v2 280w, https://s.w.org/images/core/4.7/css-547.jpg?v2 547w" sizes="(max-width: 500px) calc(100vw - 40px), (max-width: 781px) calc((100vw - 70px) * .466), (max-width: 959px) calc((100vw - 116px) * .469), (max-width: 1290px) calc((100vw - 240px) * .472), 496px" alt="" />
				<p><?php _e( 'This feature deserves a ticker-tape parade down Main Street &mdash; no more Googling to remember how to bold text! This user-friendly feature adds rich-text editing capabilities to text widgets, just like the Visual Editor that we&#8217;re all familiar with. Add a widget anywhere, and format away. Create lists, add emphasis with bold or italics, and easily insert links, no HTML necessary. Have fun with your new-found formatting powers &mdash; try to use them for good!' ); ?></p>
			</div>
		</div>

		<div class="feature-section one-col">
			<div class="col">
				<h3><?php _e( 'Nearby WordPress Events' ); ?></h3>
				<p><?php _e( 'This is one of our favorite features. While you are in your dashboard (because you&#8217;re running updates and writing posts, right?) you can see all upcoming WordCamps and WordPress Meetups &mdash; localized to you.' ); ?></p>
				<p><?php
					printf(
						/* translators: 1: Link to meetup.com, 2: Link to central.wordcamp.org */
						__( 'Not everyone has the time to go to %1$s or %2$s to find the next WordCamp. Many randomly discover WordPress events on Twitter. WordPress now brings you the events you need to continue improving your WordPress skills, meet friends, and, of course, publish!' ),
						'<a href="https://meetup.com/pro/wordpress">meetup.com/pro/wordpress</a>',
						'<a href="https://central.wordcamp.org/schedule">central.wordcamp.org/schedule</a>'
					);
				?></p>
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
					<h3><a href="https://make.wordpress.org/core/2016/11/03/post-type-templates-in-4-7/"><?php _e( 'Post Type Templates' ); ?></a></h3>
					<p><?php _e( 'By opening up the page template functionality to all post types, theme developers have even more flexibility with the WordPress template hierarchy.' ); ?></p>
				</div>
				<div class="col">
					<h3><?php _e( 'More Theme API Goodies' ); ?></h3>
					<p><?php
						printf(
							/* translators: %s: https://make.wordpress.org/core/2016/09/09/new-functions-hooks-and-behaviour-for-theme-developers-in-wordpress-4-7/  */
							__( 'WordPress 4.7 includes <a href="%s">new functions, hooks, and behavior</a> for theme developers.' ),
							'https://make.wordpress.org/core/2016/09/09/new-functions-hooks-and-behaviour-for-theme-developers-in-wordpress-4-7/'
						);
					?></p>
				</div>
				<div class="col">
					<h3><a href="https://make.wordpress.org/core/2016/10/04/custom-bulk-actions/"><?php _e( 'Custom Bulk Actions' ); ?></a></h3>
					<p><?php _e( 'List tables, now with more than bulk edit and delete.' ); ?></p>
				</div>
			</div>

			<div class="under-the-hood three-col">
				<div class="col">
					<h3><a href="https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/"><code>WP_Hook</code></a></h3>
					<p><?php
						printf(
							/* translators: %s: https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/  */
							__( 'The code that lies beneath actions and filters has been overhauled and modernized, fixing bugs along the way.' ),
							'https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/'
						);
					?></p>
				</div>
				<div class="col">
					<h3><?php _e( 'Settings Registration API' ); ?></h3>
					<p><?php
						printf(
							/* translators: 1: register_setting(), 2: https://make.wordpress.org/core/2016/10/26/registering-your-settings-in-wordpress-4-7/ */
							__( '%1$s <a href="%2$s">has been enhanced</a> to include type, description, and REST API visibility.' ),
							'<code>register_setting()</code>',
							'https://make.wordpress.org/core/2016/10/26/registering-your-settings-in-wordpress-4-7/'
						);
					?></p>
				</div>
				<div class="col">
					<h3><a href="https://make.wordpress.org/core/2016/10/12/customize-changesets-technical-design-decisions/"><?php _e( 'Customize Changesets' ); ?></a></h3>
					<p><?php _e( 'Customize changesets make changes in the customizer persistent, like autosave drafts. They also make exciting new features like starter content possible.' ); ?></p>
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
