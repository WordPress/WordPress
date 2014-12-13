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

<div class="about-text"><?php printf( __( 'Thank you for updating! WordPress %s helps you focus on your writing, and the new default theme lets you show it off in style.' ), $display_version ); ?></div>

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
	<h2 class="about-headline-callout"><?php _e( 'Introducing Twenty Fifteen' ); ?></h2>
	<div class="about-overview">
		<img class="about-overview-img" src="//s.w.org/images/core/3.8/twentyfourteen.jpg?1" />
	</div>

	<div class="feature-section col three-col">
		<div class="col-1">
			<p><?php echo ( 'Our 2015 default theme is blog-focused and designed for clarity. Twenty Fifteen&#8127;s straightforward typography is readable on any screen size.' ); ?></p>
		</div>
		<div class="col-2">
			<p><?php _e( 'Your content always takes center stage, whether viewed on a phone, tablet, laptop, or desktop computer.' ); ?></p>
		</div>
		<div class="col-3 last-feature">
			<p><?php printf( ( 'Twenty Fifteen has great language support, with help from <a href="%s">Google&#8217;s Noto font family</a>.' ), 'https://www.google.com/get/noto/' ); ?></p>
		</div>
	</div>

</div>

<hr />

<div class="changelog">
	<h2 class="about-headline-callout"><?php _e( 'Distraction-free writing' ); ?></h2>
	<div class="feature-section">
		<p><?php echo ( '<em>Just write.</em> Sometimes, you just need to concentrate on putting your thoughts into words. Try turning on <strong>writing mode</strong>. When you start typing, all the distractions will fade away, letting you focus solely on your writing. All your editing tools instantly return when you need them.' ); ?></p>
	</div>
</div>

<hr />

<div class="changelog under-the-hood">
	<h3><?php _e( 'The Finer Points' ); ?></h3>

	<div class="feature-section col two-col">
		<div>
			<h4><?php _e( 'Choose a language' ); ?></h4>
			<p><?php
				$count = '<span id="translations-count">' . 40 . '</span>';
				$string = __( 'Right now, WordPress %1$s is already translated into %2$s languages, with more always in progress. You can switch to any translation on the <a href="%3$s">General Settings</a> screen.' );
				if ( ! current_user_can( 'manage_options' ) ) {
					$string = strip_tags( $string );
				}
				echo sprintf( $string, $display_version, $count, admin_url( 'options-general.php' ) );
			?></p>

			<h4><?php _e( 'Log out everywhere' ); ?></h4>
			<p><?php printf( ( 'There&#8217;s a new tool on <a href="%s">your profile</a> that logs you out everywhere, for those times you forget to log off a shared computer.' ), get_edit_profile_url() ); ?></p>
		</div>

		<div class="last-feature">
			<h4><?php _e( 'Vine embeds' ); ?></h4>
			<p><?php printf( ( 'Embedding videos from Vine is as simple as pasting a URL onto its own line in a post. For more, see the Codex article on <a href="%s">Embeds</a>.' ), 'http://codex.wordpress.org/Embeds' ); ?></p>

			<h4><?php _e( 'Plugin recommendations' ); ?></h4>
			<p><?php
				$string = ( 'The <a href="%s">plugin installer</a> now offers a list of plugins you may want to try, based on others who have similar plugins installed as you.' );
				if ( ! current_user_can( 'install_plugins' ) ) {
					$string = strip_tags( $string );
				}
				echo sprintf( $string, network_admin_url( 'plugin-install.php?tab=recommended' ) );
			?></p>
		</div>
	</div>
</div>

<hr />

<div class="changelog under-the-hood">
	<h3><?php _e( 'Under the Hood' ); ?></h3>

	<div class="feature-section col two-col">
		<div>
			<h4><?php _e( 'Complex Queries' ); ?></h4>
			<p><?php printf( __( 'Metadata, date, and term queries now support advanced conditional logic, like nested clauses and multiple operators &mdash; <code>%s</code>.' ), 'A&nbsp;AND&nbsp;(&nbsp;B&nbsp;OR&nbsp;C&nbsp;)' ); ?></p>

			<h4><?php _e( 'Customizer API' ); ?></h4>
			<p><?php echo ( 'The customizer now supports conditionally showing panels and sections based on the page being previewed.' ); ?></p>
		</div>
		<div class="last-feature">
			<h4><?php
				/* translators: %s: "<title>" tag */
				printf( __( '%s tags in themes' ), '<code>&lt;title&gt;</code>' );
			?></h4>
			<p><?php
				printf( __( '%s tells WordPress to handle the complexities of document titles.' ), "<code>add_theme_support( 'title-tag' )</code>" );
			?></p>

			<h4><?php _e( 'Developer Reference' ); ?></h4>
			<p><?php printf( __( 'Continued improvements to inline code documentation have made the <a href="%s">developer reference</a> more complete than ever.' ), 'https://developer.wordpress.org/reference/' ); ?></p>
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
