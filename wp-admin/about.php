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

include( ABSPATH . 'wp-admin/admin-header.php' );
?>
<div class="wrap about-wrap">

<h1><?php printf( __( 'Welcome to WordPress %s' ), $display_version ); ?></h1>

<div class="about-text"><?php printf( __( 'Thank you for updating to the latest version. WordPress %s makes your writing experience even better.' ), $display_version ); ?></div>

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
	<h3><?php _e( 'Colorful New Theme' ); ?></h3>

	<div class="feature-section images-stagger-right">
		<img alt="" src="<?php echo is_ssl() ? 'https://' : '//s.'; ?>wordpress.org/images/core/3.6/twentythirteen.png" class="image-66" />
		<h4><?php _e( 'Introducing Twenty Thirteen' ); ?></h4>
		<p><?php printf( __( "The new default theme puts focus on your content with a colorful, single-column design made for media-rich blogging." ) ); ?></p>
		<p><?php _e( 'Inspired by modern art, Twenty Thirteen features quirky details, beautiful typography, and bold, high-contrast colors &mdash; all with a flexible layout that looks great on any device, big or small.' ); ?></p>
	</div>
</div>

<div class="changelog">
	<h3><?php _e( 'Write with Confidence' ); ?></h3>

	<div class="feature-section images-stagger-right">
		<img alt="" src="<?php echo is_ssl() ? 'https://' : '//s.'; ?>wordpress.org/images/core/3.6/revisions.png" class="image-66" />
		<h4><?php _e( 'Explore Revisions' ); ?></h4>
		<p></p>
		<p><?php _e( 'From the first word you write, WordPress saves every change. Each revision is always at your fingertips. Text is highlighted as you scroll through revisions at lightning speed, so you can see what changes have been made along the way.' ); ?></p>
		<p><?php _e( 'It&#8217;s easy to compare two revisions from any point in time, and to restore a revision and go back to writing. Now you can be confident that no mistake is permanent.' ); ?></p>
	</div>

	<div class="feature-section col two-col">
		<div>
			<h4><?php _e( 'Improved Autosaves' ); ?></h4>
			<p><?php _e( 'Never lose a word you&#8217;ve written. Autosaving is now even better; whether your power goes out, your browser crashes, or you lose your internet connection, your content is safe.' ); ?></p>
		</div>
		<div class="last-feature">
			<h4><?php _e( 'Better Post Locking' ); ?></h4>
			<p><?php _e( 'Always know who&#8217;s editing with live updates that appear in the list of posts. And if someone leaves for lunch with a post open, you can take over where they left off.' ); ?></p>
		</div>
	</div>
</div>

<div class="changelog">
	<h3><?php _e( 'Support for Audio and Video' ); ?></h3>

	<div class="feature-section images-stagger-right">
		<div class="video image-66"><?php
			$sample_video = ( is_ssl() ? 'https://' : 'http://s.' ) . 'wordpress.org/images/core/3.6/sample-video';
			$args = array(
				'mp4' => "$sample_video.mp4",
				'ogv' => "$sample_video.ogv",
				'width' => 625,
				'height' => 360,
			);
			// Opera 12 (Presto, pre-Chromium) fails to load ogv properly
			// when combined with ME.js. Works fine in Opera 15.
			// Don't serve ogv to Opera 12 to avoid complete brokeness.
			if ( $GLOBALS['is_opera'] )
				unset( $args['ogv'] );
			// Our current ME.js API is limited to shortcodes in posts.
			echo wp_video_shortcode( $args );
		?></div>
		<h4><?php _e( 'New Media Player' ); ?></h4>
		<p><?php _e( 'Share your audio and video with the new built-in HTML5 media player. Upload files using the media manager and embed them in your posts.' ); ?></p>

		<h4><?php _e( 'Embed Music from Spotify, Rdio, and SoundCloud' ); ?></h4>
		<p><?php _e( 'Embed songs and albums from your favorite artists, or playlists you&#8217;ve mixed yourself. It&#8217;s as simple as pasting a URL into a post on its own line.' ); ?></p>
		<p><?php printf( __( '(Love another service? Check out all of the <a href="%s">embeds</a> that WordPress supports.)' ), 'http://codex.wordpress.org/Embeds' ); ?></p>
	</div>
</div>

<div class="changelog">
	<h3><?php _e( 'Under the Hood' ); ?></h3>

	<div class="feature-section col three-col">
		<div>
			<h4><?php _e( 'Audio/Video API' ); ?></h4>
			<p><?php _e( 'The new audio/video APIs give developers access to powerful media metadata, like ID3 tags.' ); ?></p>
		</div>
		<div>
			<h4><?php _e( 'Semantic Markup' ); ?></h4>
			<p><?php _e( 'Themes can now choose improved HTML5 markup for comment forms, search forms, and comment lists.' ); ?></p>
		</div>
		<div class="last-feature">
			<h4><?php _e( 'JavaScript Utilities' ); ?></h4>
			<p><?php _e( 'Handy JavaScript utilities ease common tasks like Ajax requests, templating, and Backbone view management.' ); ?></p>
		</div>
	</div>

	<div class="feature-section col three-col">
		<div>
			<h4><?php _e( 'Shortcode Improvements' ); ?></h4>
			<p><?php _e( 'Search content for shortcodes with <code>has_shortcode()</code> and adjust shortcode attributes with a new filter.' ); ?></p>
		</div>
		<div>
			<h4><?php _e( 'Revision Control' ); ?></h4>
			<p><?php _e( 'Fine-grained revision controls allow you to keep a different number of revisions for each post type.' ); ?></p>
		</div>
		<div class="last-feature">
			<h4><?php _e( 'External Libraries' ); ?></h4>
			<p><?php
				/* translators: placeholders 2, 3 and 4 are version numbers */
				printf( __( 'New and updated libraries: <a href="%1$s">MediaElement.js</a>, jQuery %2$s, jQuery UI %3$s, jQuery Migrate, Backbone %4$s.' ), 'http://mediaelementjs.com/', '1.10.2', '1.10.3', '1.0' ); ?></p>
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
