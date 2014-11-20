<?php
/**
 * About This Version administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

wp_enqueue_style( 'wp-mediaelement' );
wp_enqueue_script( 'wp-mediaelement' );
wp_localize_script( 'mediaelement', '_wpmejsSettings', array(
	'pluginPath' => includes_url( 'js/mediaelement/', 'relative' ),
	'pauseOtherPlayers' => ''
) );

$title = __( 'About' );

list( $display_version ) = explode( '-', $wp_version );

include( ABSPATH . 'wp-admin/admin-header.php' );
?>
<!--[if lt IE 9]><script>document.createElement('audio');document.createElement('video');</script><![endif]-->
<div class="wrap about-wrap">

<h1><?php printf( __( 'Welcome to WordPress&nbsp;%s' ), $display_version ); ?></h1>

<div class="about-text"><?php printf( __( 'Thank you for updating! WordPress %s brings you a smoother writing and management experience.' ), $display_version ); ?></div>

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
	<h3><?php echo _n( 'Maintenance and Security Release', 'Maintenance and Security Releases', 1 ); ?></h3>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
         '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.', 23 ), '4.0.1', number_format_i18n( 23 ) ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'http://codex.wordpress.org/Version_4.0.1' ); ?>
 	</p>
</div>

<div class="changelog">
	<div class="about-overview">
		<?php if ( ( $locale = get_locale() ) && 'en_' === substr( $locale, 0, 3 ) ) : ?>
			<embed src="//v.wordpress.com/bUdzKMro" type="application/x-shockwave-flash" width="640" height="360" allowscriptaccess="always" allowfullscreen="true" wmode="transparent"></embed>
		<?php else : ?>
			<img class="about-overview-img" src="//s.w.org/images/core/4.0/wp40.png" width="640" height="360" />
		<?php endif; ?>
	</div>

	<hr />

	<div class="feature-section col two-col">
		<div class="col-1">
			<h3><?php _e( 'Manage your media with style' ); ?></h3>
			<p><?php _e( 'Explore your uploads in a beautiful, endless grid. A new details preview makes viewing and editing any amount of media in sequence a snap.' ); ?></p>
		</div>
		<div class="col-2 last-feature">
			<img src="//s.w.org/images/core/4.0/media.jpg" />
		</div>
	</div>

	<hr />

	<div class="feature-section col two-col">
		<div class="col-1">
			<div class="about-video about-video-embed">
				<?php
					echo wp_video_shortcode( array(
						'mp4'      => '//s.w.org/images/core/4.0/embed.mp4',
						'ogv'      => '//s.w.org/images/core/4.0/embed.ogv',
						'webm'      => '//s.w.org/images/core/4.0/embed.webm',
						'loop'     => true,
						'autoplay' => true,
						'width'    => 500,
						'height'   => 352
					) );
				?>
			</div>
		</div>
		<div class="col-2 last-feature">
			<h3><?php _e( 'Working with embeds has never been easier' ); ?></h3>
			<p><?php _e( 'Paste in a YouTube URL on a new line, and watch it magically become an embedded video. Now try it with a tweet. Oh yeah &#8212; embedding has become a visual experience. The editor shows a true preview of your embedded content, saving you time and giving you confidence.' ); ?></p>
			<p><?php _e( 'We&#8217;ve expanded the services supported by default, too &#8212; you can embed videos from CollegeHumor, playlists from YouTube, and talks from TED. <a href="http://codex.wordpress.org/Embeds">Check out all of the embeds</a> that WordPress supports.' ); ?></p>
		</div>
	</div>

	<hr />

	<div class="feature-section col two-col">
		<div class="col-1">
			<h3><?php _e( 'Focus on your content' ); ?></h3>
			<p><?php _e( 'Writing and editing is smoother and more immersive with an editor that expands to fit your content as you write, and keeps the formatting tools available at all times.' ); ?></p>
		</div>
		<div class="col-2 last-feature">
			<div class="about-video about-video-focus">
				<?php
					echo wp_video_shortcode( array(
						'mp4'      => '//s.w.org/images/core/4.0/focus.mp4',
						'ogv'      => '//s.w.org/images/core/4.0/focus.ogv',
						'webm'      => '//s.w.org/images/core/4.0/focus.webm',
						'loop'     => true,
						'autoplay' => true,
						'width'    => 500,
						'height'   => 281
					) );
				?>
			</div>
		</div>
	</div>

	<hr />

	<div class="feature-section col two-col">
		<div class="col-1">
			<img src="//s.w.org/images/core/4.0/plugins.png" />
		</div>
		<div class="col-2 last-feature">
			<h3 class="higher"><?php _e( 'Finding the right plugin' ); ?></h3>
			<p><?php _e( 'There are more than 30,000 free and open source plugins in the WordPress plugin directory. WordPress 4.0 makes it easier to find the right one for your needs, with new metrics, improved search, and a more visual browsing experience.' ); ?></p>
			<a href="<?php echo admin_url( 'plugin-install.php' ); ?>" class="button button-large button-primary"><?php _e( 'Browse plugins' ); ?></a>
		</div>
	</div>
</div>

<hr />

<div class="changelog under-the-hood">
	<h3><?php _e( 'Under the Hood' ); ?></h3>

	<div class="feature-section col three-col">
		<div>
		<h4><?php _e( 'Customizer API' ); ?></h4>
			<p><?php _e( 'Contexts, panels, and a wider array of controls are now supported in the customizer.' ); ?></p>
		</div>
		<div>
			<h4><?php _e( 'Query Ordering' ); ?></h4>
			<p><?php
				/* translators: 1: "ORDER BY" (SQL), 2: "WP_Query" */
				printf( __( 'Developers have more flexibility creating %1$s clauses through %2$s.' ), '<code>ORDER&nbsp;BY</code>', '<code>WP_Query</code>' );
			?></p>
		</div>
		<div class="last-feature">
			<h4><?php _e( 'External Libraries' ); ?></h4>
			<p><?php _e( 'Updated libraries: TinyMCE&nbsp;4.1.3, jQuery&nbsp;1.11.1, MediaElement&nbsp;2.15.' ); ?></p>
		</div>
	</div>

	<hr />

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
