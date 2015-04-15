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

	<div class="feature-section">
		<div class="dfw-container">
			<img src="//s.w.org/images/core/4.1/focus.png" class="base-image" />
		</div>
		<h3><?php echo ( 'An easier way to share content' ); ?></h3>
		<p><?php printf( 'Clip it, edit it, publish it. Get familiar with the new and improved Press This. From the <a href="%s">Tools</a> menu, add Press This to your browser bookmark bar or your mobile device home screen. Once installed you can share your content with lightning speed. Sharing your favorite videos, images, and content has never been this fast or this easy.', admin_url( 'tools.php' ) ); ?></p>
		<p><?php _e( 'Drag the bookmarklet below to your bookmarks bar. Then, when you&#8217;re on a page you want to share, simply &#8220;press&#8221; it.' ); ?> [bookmarklet]</p>
	</div>

</div>

<hr />

<div class="changelog headline-feature">

	<div class="feature-section">
		<div class="col">
			<h3><?php echo ( 'Extended character support' ); ?></h3>
			<p><?php echo ( 'Writing in WordPress, whatever your language, just got better. WordPress 4.2 supports a host of new characters out-of-the-box, including native Chinese, Japanese, and Korean characters, musical and mathematical symbols, and hieroglyphs.' ); ?></p>
			<p><?php
				/* translators: 1: heart emoji, 2: frog face emoji, 3, monkey emoji, 4: pizza emoji, 5: Emoji Codex link */
				printf( 'Don&#8217;t use any of those characters? You can still have fun &mdash; emoji are now available in WordPress! Get creative and decorate your content with %1$s, %2$s, %3$s, %4$s, and all the many other <a href="%5$s">emoji</a>.', '&#x1F499', '&#x1F438', '&#x1F412', '&#x1F355', __( 'https://codex.wordpress.org/Emoji' ) );
			?></p>
		</div>
		<div class="col">
			<img class="" src="//s.w.org/images/core/4.1/mobile.png" />
		</div>
	</div>

	<div class="clear"></div>
</div>

<hr />

<div class="changelog customize">
	<div class="feature-section col three-col">
		<div>
			<?php
			echo wp_video_shortcode( array(
				'mp4'      => '//s.w.org/images/core/3.9/widgets.mp4',
				'ogv'      => '//s.w.org/images/core/3.9/widgets.ogv',
				'webm'     => '//s.w.org/images/core/3.9/widgets.webm',
				'loop'     => true,
				'height'   => 218
			) );
			?>
			<h4><?php echo ( 'Switch themes in the Customizer' ); ?></h4>
			<p><?php echo ( 'Browse and preview your installed themes from the Customizer. Make sure the theme looks great with <em>your</em> content, before it debuts on your site. ' ); ?></p>
		</div>
		<div>
			<?php
			echo wp_video_shortcode( array(
				'mp4'      => '//s.w.org/images/core/3.9/widgets.mp4',
				'ogv'      => '//s.w.org/images/core/3.9/widgets.ogv',
				'webm'     => '//s.w.org/images/core/3.9/widgets.webm',
				'loop'     => true,
				'height'   => 218
			) );
			?>
			<h4><?php echo ( 'Even more embeds' ); ?></h4>
			<p><?php echo ( 'Paste links from Tumblr.com and Kickstarter and watch them magically appear right in the editor. With every release, your publishing and editing experience get closer together.' ); ?></p>
		</div>
		<div class="last-feature">
			<?php
			echo wp_video_shortcode( array(
				'mp4'      => '//s.w.org/images/core/3.9/widgets.mp4',
				'ogv'      => '//s.w.org/images/core/3.9/widgets.ogv',
				'webm'     => '//s.w.org/images/core/3.9/widgets.webm',
				'loop'     => true,
				'height'   => 218
			) );
			?>
			<h4><?php echo ( 'Streamlined plugin updates' ); ?></h4>
			<p><?php echo ( 'Goodbye boring loading screen, hello smooth and simple plugin updates. Just click <em>Update Now</em> and watch the magic happen.' ); ?></p>
		</div>
	</div>
</div>

<hr />

<div class="changelog under-the-hood">
	<h3><?php _e( 'Under the Hood' ); ?></h3>

	<div class="feature-section col two-col">
		<div>
			<h4><?php echo ( 'utf8mb4 support' ); ?></h4>
			<p><?php echo ( 'Database character encoding has changed from utf8 to utf8mb4, which adds support for a whole range of new 4-byte characters.' ); ?></p>

			<h4><?php echo ( 'JavaScript accessibility' ); ?></h4>
			<p><?php echo ( 'You can now send audible notifications to screen readers in JavaScript with <code>wp.a11y.speak()</code>. Pass it a string, and an update will be sent to a dedicated ARIA live notifications area.' ); ?></p>
		</div>
		<div class="last-feature">
			<h4><?php echo ( 'Shared term splitting' ); ?></h4>
			<p><?php
				/* translators: 1: Term splitting guide link */
				printf ( 'Terms shared across multiple taxonomies will be split when one of them is updated. Find out more in the <a href="%1$s">Plugin Developer Handbook.</a>', 'https://developer.wordpress.org/plugins/taxonomy/working-with-split-terms-in-wp-4-2/' );
			?></p>

			<h4><?php echo ( 'Complex query ordering' ); ?></h4>
			<p><?php echo ( '<code>WP_Query</code>, <code>WP_Comment_Query</code>, and <code>WP_User_Query</code> now support complex ordering with named meta query clauses.' ); ?></p>
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
