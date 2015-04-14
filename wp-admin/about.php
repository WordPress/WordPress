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

<div class="about-text"><?php printf( ( 'Thank you for updating! WordPress %s helps you communicate and share, globally.' ), $display_version ); ?></div>

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

<div class="changelog headline-feature dfw">
	<h2>[video]</h2>

	<h2><?php echo ( 'An easier way to share content' ); ?></h2>
	<div class="feature-section">
		<div class="dfw-container">
			<img src="//s.w.org/images/core/4.1/focus.png" class="base-image" />
		</div>
		<h3><em><?php echo ( 'Press This modernized' ); ?></em></h3>
		<p><?php echo ( 'Use Press This to clip text, images and videos from any web page. Then edit and add more directly from Press This before you save or publish it in a post on your site.' ); ?></p>
	</div>
</div>

<hr />

<div class="changelog headline-feature">

	<div class="feature-section">
		<div class="col">
			<h3><?php echo ( 'Extended Character Support' ); ?></h3>
			<p><?php echo ( 'WordPress now supports displaying a host of new characters included with Unicode 7.0, extending support for native Chinese, Japanese, and Korean characters, musical and mathematical symbols, hieroglyphs, and of course, emoji.' ); ?></p>
			<p><?php echo ( 'Whether you’re using special characters or not, you can still get creative and decorate your content with emoji hearts, kittens, ice-cream cones, and musical instruments.' ); ?></p>
		</div>
		<div class="col">
			<img class="" src="//s.w.org/images/core/4.1/mobile.png" />
		</div>
	</div>

	<div class="clear"></div>
</div>

<hr />

<div class="changelog customize">
	<div class="feature-section col two-col">
		<div>
			<?php
			echo wp_video_shortcode( array(
				'mp4'      => '//s.w.org/images/core/3.9/widgets.mp4',
				'ogv'      => '//s.w.org/images/core/3.9/widgets.ogv',
				'webm'     => '//s.w.org/images/core/3.9/widgets.webm',
				'loop'     => true,
				'autoplay' => true,
				'width'    => 499
			) );
			?>
			<h4><?php echo ( 'Switch themes in the Customizer' ); ?></h4>
			<p><?php echo ( 'Browse and live preview your installed themes in the Customizer. Make sure everything looks just how you want it with your content before the new theme makes its debut on your site.' ); ?></p>
		</div>
		<div class="last-feature">
			<?php
			echo wp_video_shortcode( array(
				'mp4'      => '//s.w.org/images/core/3.9/widgets.mp4',
				'ogv'      => '//s.w.org/images/core/3.9/widgets.ogv',
				'webm'     => '//s.w.org/images/core/3.9/widgets.webm',
				'loop'     => true,
				'autoplay' => true,
				'width'    => 499
			) );
			?>
			<h4><?php echo ( 'Streamlined plugin updates' ); ?></h4>
			<p><?php echo ( 'Plugin updates are smooth and simple. No more boring loading screen &mdash; just click <em>Update Now</em> on any number of plugins and wait for the success messages. Easy!' ); ?></p>
		</div>
	</div>
</div>

<hr />

<div class="changelog under-the-hood">
	<h3><?php _e( 'Under the Hood' ); ?></h3>

	<div class="feature-section col three-col">
		<div>
			<h4><?php echo ( 'Tumblr & Kickstarter oEmbed support' ); ?></h4>
			<p><?php echo ( 'Give your favorite Tumblr posts and Kickstarter campaigns a boost by embedding them in a post or page.' ); ?></p>

			<h4><?php echo ( 'JavaScript accessibility' ); ?></h4>
			<p><?php echo ( 'New <code>wp.a11y.speak</code> functionality lets your JavaScript talk to screen readers with ARIA live notifications.' ); ?></p>
		</div>
		<div>
			<h4><?php echo ( 'utf8mb4 Support' ); ?></h4>
			<p><?php echo ( 'If your system supports it, we&#8217;ve changed from utf8 to utf8mb4 character encoding in the database, which adds support for a whole range of new 4-byte characters.' ); ?></p>

			<h4><?php echo ( 'Complex Query Ordering' ); ?></h4>
			<p><?php echo ( '<code>WP_Query</code>, <code>WP_Comment_Query</code>, and <code>WP_User_Query</code> now support complex ordering with named meta query clauses.' ); ?></p>
		</div>
		<div class="last-feature">
			<h4><?php echo ( 'Taxonomy Roadmap' ); ?></h4>
			<p><?php echo ( 'Terms previously shared across multiple taxonomies will be &#8220;split&#8221; when one of them is updated, creating a new row in the database for each.' ); ?></p>
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

<script>
jQuery(document).ready( function($) {
	$.ajax( 'https://api.wordpress.org/translations/core/1.0/?version=4.1',
		{ 'type' : 'HEAD' } ).done( function( data, textStatus, jqXHR ) {
			var count = jqXHR.getResponseHeader( 'X-Translations-Count' );
			if ( count ) {
				$( '#translations-count' ).text( count );
			}
	});
});
</script>
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
