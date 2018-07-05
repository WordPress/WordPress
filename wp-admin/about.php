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

<div class="changelog point-releases">
	<h3><?php echo _n( 'Maintenance and Security Release', 'Maintenance and Security Releases', 24 ); ?></h3>
	<p><?php printf( __( '<strong>Version %s</strong> addressed one security issue.' ), '4.1.24' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.1.24' ); ?>
	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
			'<strong>Version %1$s</strong> addressed some security issues.', 2 ), '4.1.23' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.1.23' ); ?>
	</p>
	<p><?php printf( __( '<strong>Version %s</strong> addressed one security issue.' ), '4.1.22' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.1.22' ); ?>
	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
			'<strong>Version %1$s</strong> addressed some security issues.', 4 ), '4.1.21' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.1.21' ); ?>
	</p>
	<p><?php printf( __( '<strong>Version %s</strong> addressed one security issue.' ), '4.1.20' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.1.20' ); ?>
	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
			'<strong>Version %1$s</strong> addressed some security issues.', 8 ), '4.1.19' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.1.19' ); ?>
	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
			'<strong>Version %1$s</strong> addressed some security issues.', 5 ), '4.1.18' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.1.18' ); ?>
	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed %2$s bug.',
			'<strong>Version %1$s</strong> addressed %2$s bugs.', 1 ), '4.1.17', number_format_i18n( 1 ) ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.1.17' ); ?>
	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
			'<strong>Version %1$s</strong> addressed some security issues.', 5 ), '4.1.16' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.1.16' ); ?>
	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
			'<strong>Version %1$s</strong> addressed some security issues.', 3 ), '4.1.15' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.1.15' ); ?>
	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
			'<strong>Version %1$s</strong> addressed some security issues.', 8 ), '4.1.14' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.1.14' ); ?>
	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
         '<strong>Version %1$s</strong> addressed some security issues.', 2 ), '4.1.13' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.1.13' ); ?>
	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
         '<strong>Version %1$s</strong> addressed some security issues.', 9 ), '4.1.12' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.1.12' ); ?>
	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
         '<strong>Version %1$s</strong> addressed some security issues.', 6 ), '4.1.11' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.1.11' ); ?>
 	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
         '<strong>Version %1$s</strong> addressed some security issues.', 2 ), '4.1.10' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.1.10' ); ?>
 	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
         '<strong>Version %1$s</strong> addressed some security issues.', 1 ), '4.1.9' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.1.9' ); ?>
 	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
         '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.', 2 ), '4.1.8', number_format_i18n( 2 ) ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.1.8' ); ?>
	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
         '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.', 4 ), '4.1.7', number_format_i18n( 4 ) ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.1.7' ); ?>
	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
         '<strong>Version %1$s</strong> addressed some security issues.', 2 ), '4.1.6' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.1.6' ); ?>
 	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
         '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.', 3 ), '4.1.5', number_format_i18n( 3 ) ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.1.5' ); ?>
	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
         '<strong>Version %1$s</strong> addressed some security issues.', 1 ), '4.1.4' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.1.4' ); ?>
 	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed %2$s bug.',
         '<strong>Version %1$s</strong> addressed %2$s bugs.', 1 ), '4.1.3', number_format_i18n( 1 ) ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.1.3' ); ?>
 	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
         '<strong>Version %1$s</strong> addressed some security issues.', 8 ), '4.1.2' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.1.2' ); ?>
 	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed %2$s bug.',
         '<strong>Version %1$s</strong> addressed %2$s bugs.', 21 ), '4.1.1', number_format_i18n( 21 ) ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.1.1' ); ?>
 	</p>
</div>

<div class="changelog headline-feature">
	<h2><?php _e( 'Introducing Twenty&nbsp;Fifteen' ); ?></h2>
	<div class="featured-image">
		<img src="//s.w.org/images/core/4.1/theme.png?0" />
	</div>

	<div class="feature-section">
		<div class="col">
			<h3><?php _e( 'Our newest default theme, Twenty Fifteen, is a blog-focused theme designed for clarity.' ); ?></h3>
			<p><?php printf( __( 'Twenty Fifteen has flawless language support, with help from <a href="%s">Google&#8217;s Noto font family</a>.' ), 'https://www.google.com/get/noto/' ); ?></p>
			<p><?php _e( 'The straightforward typography is readable on any screen size.' ); ?></p>
			 <p><?php _e( 'Your content always takes center stage, whether viewed on a phone, tablet, laptop, or desktop computer.' ); ?></p>
		</div>
		<div class="col">
			<img class="" src="//s.w.org/images/core/4.1/mobile.png?0" />
		</div>
	</div>

	<div class="clear"></div>
</div>

<hr />

<div class="changelog headline-feature dfw">
	<h2><?php _e( 'Distraction-free writing' ); ?></h2>
	<div class="feature-section">
		<div class="dfw-container">
			<img src="//s.w.org/images/core/4.1/focus.png?0" class="base-image" />
			<img src="//s.w.org/images/core/4.1/focus1.png?0" class="overlay-image fade-in" />
			<img src="//s.w.org/images/core/4.1/focus2.png?0" class="overlay-image fade-in" />
			<img src="//s.w.org/images/core/4.1/focus3.png?0" class="overlay-image from-left" />
		</div>
		<h3><em><?php _e( 'Just write.' ); ?></em></h3>
		<p><?php _e( 'Sometimes, you just need to concentrate on putting your thoughts into words. Try turning on <strong>distraction-free writing mode</strong>. When you start typing, all the distractions will fade away, letting you focus solely on your writing. All your editing tools instantly return when you need them.' ); ?></p>
	</div>
</div>

<hr />

<div class="changelog feature-list finer-points">
	<h2><?php _e( 'The Finer Points' ); ?></h2>

	<div class="feature-section col two-col">
		<div>
			<svg viewBox="-30 -30 160 160"><path d="M57.9,28.9h-7.9c-1.6,0-3.2,0.3-4.7,1c-1.5,0.7-2.7,1.6-3.7,2.7l-4.7-14.2H21.7L9.2,55.3h8.9l3.9-10.5h14.9v21.1H10.5 c-2.9,0-5.4-1-7.4-3.1C1,60.6,0,58.1,0,55.3V18.4c0-2.9,1-5.4,3.1-7.4c2.1-2.1,4.5-3.1,7.4-3.1h36.8c2.9,0,5.4,1,7.4,3.1 c2.1,2.1,3.1,4.5,3.1,7.4V28.9z M34.3,39.5H23.6l5.3-15.4L34.3,39.5z M52.6,34.2h36.8c2.9,0,5.4,1,7.4,3.1c2.1,2.1,3.1,4.5,3.1,7.4 v36.8c0,2.9-1,5.4-3.1,7.4c-2.1,2.1-4.5,3.1-7.4,3.1H52.6c-2.9,0-5.4-1-7.4-3.1c-2.1-2.1-3.1-4.5-3.1-7.4V44.7c0-2.9,1-5.4,3.1-7.4 C47.3,35.2,49.8,34.2,52.6,34.2z M90.8,60.5v-5.7H74.1V43.4H68v11.4H51.3v5.7h6.7c0.3,2.3,1.1,4.7,2.2,7.2c1.2,2.5,2.7,4.7,4.5,6.6 c-2.2,0.9-4.5,1.7-6.9,2.3s-4.1,0.9-5.2,0.9l0.3,1.4c0.2,0.9,0.4,2,0.6,3.3c0.2,1.3,0.3,2.3,0.2,3.1c2.2,0,4.9-0.6,8.1-1.7 c3.2-1.1,6.3-2.6,9.2-4.3c2.9,1.8,6,3.2,9.3,4.3c3.3,1.1,6.1,1.7,8.3,1.7c0-0.5,0-1.1,0.1-1.8c0.1-0.7,0.2-1.4,0.3-2 c0.1-0.7,0.2-1.3,0.3-1.9c0.1-0.6,0.2-1.1,0.3-1.4l0.1-0.6c-1.2,0-3-0.3-5.4-1c-2.5-0.7-4.8-1.4-7.1-2.3c1.8-2,3.3-4.2,4.4-6.6 s1.9-4.8,2.2-7.1H90.8z M70.7,70.7c-2.7-2.5-4.4-5.8-5.3-10.2h11c-0.9,4.4-2.7,7.7-5.3,10.2l-0.2,0.2 C70.8,70.8,70.8,70.7,70.7,70.7z"/></svg>
			<h4><?php _e( 'Choose a language' ); ?></h4>
			<p><?php
				$count = '<span id="translations-count">' . 40 . '</span>';
				$string = __( 'Right now, WordPress %1$s is already translated into %2$s languages, with more always in progress. You can switch to any translation on the <a href="%3$s">General Settings</a> screen.' );
				if ( ! current_user_can( 'manage_options' ) ) {
					$string = strip_tags( $string );
				}
				echo sprintf( $string, $display_version, $count, admin_url( 'options-general.php' ) );
			?></p>
		</div>

		<div class="last-feature">
			<svg viewBox="-30 -30 160 160"><path d="M35.3,26.5H5.9c-1.5,0-2.9-0.6-4.1-1.7C0.6,23.6,0,22.2,0,20.6c0-1.6,0.6-3,1.7-4.1c1.2-1.2,2.5-1.7,4.1-1.7h29.4 c1.6,0,3,0.6,4.1,1.7c1.2,1.2,1.7,2.5,1.7,4.1c0,1.6-0.6,3-1.7,4.1C38.3,25.9,36.9,26.5,35.3,26.5z M68.9,77.7 c-1.2,1.2-2.5,1.7-4.1,1.7H17.6c-1.6,0-3-0.6-4.1-1.7c-1.2-1.2-1.7-2.5-1.7-4.1V38.2c0-1.6,0.6-3,1.7-4.1c1.2-1.2,2.5-1.7,4.1-1.7 h47.1c1.6,0,3,0.6,4.1,1.7c1.2,1.2,1.7,2.5,1.7,4.1v35.3C70.6,75.1,70,76.5,68.9,77.7z M76.5,61.8L100,85.3V26.5L76.5,50V61.8z"/></svg>
			<h4><?php _e( 'Vine embeds' ); ?></h4>
			<p><?php printf( __( 'Embedding videos from Vine is as simple as pasting a URL onto its own line in a post. See the <a href="%s">full list</a> of supported embeds.' ), 'http://codex.wordpress.org/Embeds' ); ?></p>
		</div>

		<div>
			<svg viewBox="-30 -30 160 160"><path d="M61.4,78.6V61.4L72.9,50v40H10V27.1h45.7L44.3,38.6H21.4v40H61.4z M44.3,10H90v45.7L78.6,50V32.4l-32,31.9l-8.1-8.1 l34.8-34.9H50L44.3,10z"/></svg>
			<h4><?php _e( 'Log out everywhere' ); ?></h4>
			<p><?php printf( __( 'If you&#8217;ve ever worried you forgot to sign out from a shared computer, you can now go to <a href="%s">your profile</a> and log out everywhere.' ), get_edit_profile_url() ); ?></p>
		</div>

		<div class="last-feature">
			<svg viewBox="-30 -30 160 160"><path d="M35.1,30.1l4.7-5.8l46.4,46.4L80,75c-1.7,1.7-4.6,3.1-8.6,4.3c-4,1.1-7.7,1.7-11,1.7h-20L34,87.4 c-1.5,1.5-3.3,2.3-5.5,2.3c-2.1,0-3.9-0.8-5.5-2.3c-1.5-1.5-2.3-3.3-2.3-5.4c0-2.1,0.8-4,2.3-5.5l6.4-6.4v-20 c0-3.3,0.5-7,1.6-11.2C32.1,34.7,33.4,31.8,35.1,30.1z M76.2,21L59.6,37.7L49.9,28l16.7-16.7c0.9-0.9,2.1-1.2,3.7-0.8 c1.6,0.3,3,1.2,4.3,2.5c1.3,1.3,2.2,2.7,2.5,4.3C77.4,18.9,77.1,20.1,76.2,21z M72.4,50.5l16.7-16.7c0.9-0.9,2.1-1.2,3.7-0.9 c1.6,0.3,3,1.1,4.3,2.5c1.3,1.3,2.2,2.7,2.5,4.3c0.3,1.6,0,2.8-0.9,3.7L82,60.1L72.4,50.5z"/><path d="M10.9,40.4l3.4,6.8L21,48l-4.7,5.2l1.3,7.5l-6.8-3.4l-6.8,3.4l1.3-7.5L0.7,48l6.8-0.8L10.9,40.4z"/></svg>
			<h4><?php _e( 'Plugin recommendations' ); ?></h4>
			<p><?php
				$string = __( 'The <a href="%s">plugin installer</a> suggests plugins for you to try. Recommendations are based on the plugins you and other users have installed.' );
				if ( ! current_user_can( 'install_plugins' ) ) {
					$string = strip_tags( $string );
				}
				echo sprintf( $string, network_admin_url( 'plugin-install.php?tab=recommended' ) );
			?></p>
		</div>
	</div>
</div>

<hr />

<div class="changelog feature-list">
	<h2><?php _e( 'Under the Hood' ); ?></h2>

	<div class="feature-section col two-col">
		<div>
			<h4><?php _e( 'Complex Queries' ); ?></h4>
			<p><?php printf( __( 'Metadata, date, and term queries now support advanced conditional logic, like nested clauses and multiple operators &mdash; %s.' ), '<code>A&nbsp;AND&nbsp;(&nbsp;B&nbsp;OR&nbsp;C&nbsp;)</code>' ); ?></p>

			<h4><?php _e( 'Customizer API' ); ?></h4>
			<p><?php _e( 'Expanded JavaScript APIs in the customizer enable a new media experience as well as dynamic and contextual controls, sections, and panels.' ); ?></p>
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
