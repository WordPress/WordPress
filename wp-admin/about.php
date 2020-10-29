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
<div class="wrap about-wrap">

<h1><?php printf( __( 'Welcome to WordPress&nbsp;%s' ), $display_version ); ?></h1>

<div class="about-text"><?php printf( __( 'Thank you for updating! WordPress %s helps you communicate and share, globally.' ), $display_version ); ?></div>

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
	<h3><?php echo _n( 'Maintenance and Security Release', 'Maintenance and Security Releases', 29 ); ?></h3>
	<p>
		<?php
		printf(
			/* translators: %s: WordPress version number */
			__( '<strong>Version %s</strong> addressed some security issues.' ),
			'4.2.29'
		);
		?>
		<?php
		printf(
			/* translators: %s: HelpHub URL */
			__( 'For more information, see <a href="%s">the release notes</a>.' ),
			sprintf(
				/* translators: %s: WordPress version */
				esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
				sanitize_title( '4.2.29' )
			)
		);
		?>
	</p>
	<p>
		<?php
		printf(
			/* translators: %s: WordPress version number */
			__( '<strong>Version %s</strong> addressed some security issues.' ),
			'4.2.28'
		);
		?>
		<?php
		printf(
			/* translators: %s: HelpHub URL */
			__( 'For more information, see <a href="%s">the release notes</a>.' ),
			sprintf(
				/* translators: %s: WordPress version */
				esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
				sanitize_title( '4.2.28' )
			)
		);
		?>
	</p>
	<p>
		<?php
		printf(
			/* translators: %s: WordPress version number */
			__( '<strong>Version %s</strong> addressed some security issues.' ),
			'4.2.27'
		);
		?>
		<?php
		printf(
			/* translators: %s: HelpHub URL */
			__( 'For more information, see <a href="%s">the release notes</a>.' ),
			sprintf(
				/* translators: %s: WordPress version */
				esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
				sanitize_title( '4.2.27' )
			)
		);
		?>
	</p>
	<p>
		<?php
		printf(
			/* translators: %s: WordPress version number */
			__( '<strong>Version %s</strong> addressed one security issue.' ),
			'4.2.26'
		);
		?>
		<?php
		printf(
			/* translators: %s: HelpHub URL */
			__( 'For more information, see <a href="%s">the release notes</a>.' ),
			sprintf(
				/* translators: %s: WordPress version */
				esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
				sanitize_title( '4.2.26' )
			)
		);
		?>
	</p>
	<p>
		<?php
		printf(
			/* translators: %s: WordPress version number */
			__( '<strong>Version %s</strong> addressed some security issues.' ),
			'4.2.25'
		);
		?>
		<?php
		printf(
			/* translators: %s: HelpHub URL */
			__( 'For more information, see <a href="%s">the release notes</a>.' ),
			sprintf(
				/* translators: %s: WordPress version */
				esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
				sanitize_title( '4.2.25' )
			)
		);
		?>
	</p>
	<p>
		<?php
		printf(
			/* translators: %s: WordPress version number */
			__( '<strong>Version %s</strong> addressed some security issues.' ),
			'4.2.24'
		);
		?>
		<?php
		printf(
			/* translators: %s: HelpHub URL */
			__( 'For more information, see <a href="%s">the release notes</a>.' ),
			sprintf(
				/* translators: %s: WordPress version */
				esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
				sanitize_title( '4.2.24' )
			)
		);
		?>
	</p>
	<p>
		<?php
		printf(
			/* translators: %s: WordPress version number */
			__( '<strong>Version %s</strong> addressed a security issue.' ),
			'4.2.23'
		);
		?>
		<?php
		printf(
			/* translators: %s: HelpHub URL */
			__( 'For more information, see <a href="%s">the release notes</a>.' ),
			sprintf(
				/* translators: %s: WordPress version */
				esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
				sanitize_title( '4.2.23' )
			)
		);
		?>
	</p>
	<p>
		<?php
		/* translators: %s: WordPress version number */
		printf( __( '<strong>Version %s</strong> addressed some security issues.' ), '4.2.22' );
		?>
		<?php
		/* translators: %s: Codex URL */
		printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.2.22' );
		?>
	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
			'<strong>Version %1$s</strong> addressed some security issues.', 1 ), '4.2.21' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.2.21' ); ?>
	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
			'<strong>Version %1$s</strong> addressed some security issues.', 2 ), '4.2.20' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.2.20' ); ?>
	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
			'<strong>Version %1$s</strong> addressed some security issues.', 1 ), '4.2.19' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.2.19' ); ?>
	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
			'<strong>Version %1$s</strong> addressed some security issues.', 4 ), '4.2.18' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.2.18' ); ?>
	</p>
	<p><?php printf( __( '<strong>Version %s</strong> addressed one security issue.' ), '4.2.17' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.2.17' ); ?>
	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
			'<strong>Version %1$s</strong> addressed some security issues.', 8 ), '4.2.16' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.2.16' ); ?>
	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
			'<strong>Version %1$s</strong> addressed some security issues.', 5 ), '4.2.15' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.2.15' ); ?>
	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed %2$s bug.',
			'<strong>Version %1$s</strong> addressed %2$s bugs.', 1 ), '4.2.14', number_format_i18n( 1 ) ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.2.14' ); ?>
	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
			'<strong>Version %1$s</strong> addressed some security issues.', 6 ), '4.2.13' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.2.13' ); ?>
	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
			'<strong>Version %1$s</strong> addressed some security issues.', 3 ), '4.2.12' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.2.12' ); ?>
	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
			'<strong>Version %1$s</strong> addressed some security issues.', 8 ), '4.2.11' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.2.11' ); ?>
	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
         '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.', 1 ), '4.2.10', number_format_i18n( 1 ) ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.2.10' ); ?>
	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
         '<strong>Version %1$s</strong> addressed some security issues.', 9 ), '4.2.9' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.2.9' ); ?>
	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
         '<strong>Version %1$s</strong> addressed some security issues.', 6 ), '4.2.8' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.2.8' ); ?>
 	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
         '<strong>Version %1$s</strong> addressed some security issues.', 2 ), '4.2.7' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.2.7' ); ?>
 	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
         '<strong>Version %1$s</strong> addressed some security issues.', 1 ), '4.2.6' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.2.6' ); ?>
 	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
         '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.', 2 ), '4.2.5', number_format_i18n( 2 ) ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.2.5' ); ?>
	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
         '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.', 4 ), '4.2.4', number_format_i18n( 4 ) ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.2.4' ); ?>
	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
         '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.', 20 ), '4.2.3', number_format_i18n( 20 ) ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.2.3' ); ?>
	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
         '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.', 13 ), '4.2.2', number_format_i18n( 13 ) ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.2.2' ); ?>
	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
         '<strong>Version %1$s</strong> addressed some security issues.', 1 ), '4.2.1' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.2.1' ); ?>
 	</p>
</div>

<div class="headline-feature feature-video">
	<embed type="application/x-shockwave-flash" src="https://v0.wordpress.com/player.swf?v=1.04" width="1000" height="560" wmode="direct" seamlesstabbing="true" allowfullscreen="true" allowscriptaccess="always" overstretch="true" flashvars="guid=e9kH4FzP&amp;isDynamicSeeking=true"></embed>
</div>

<hr />

<div class="feature-section two-col">
	<div class="col">
		<h3><?php _e( 'An easier way to share content' ); ?></h3>
		<p><?php printf( __( 'Clip it, edit it, publish it. Get familiar with the new and improved Press This. From the <a href="%s">Tools</a> menu, add Press This to your browser bookmark bar or your mobile device home screen. Once installed you can share your content with lightning speed. Sharing your favorite videos, images, and content has never been this fast or this easy.' ), admin_url( 'tools.php' ) ); ?></p>
		<p><?php _e( 'Drag the bookmarklet below to your bookmarks bar. Then, when you&#8217;re on a page you want to share, simply &#8220;press&#8221; it.' ); ?></p>

		<p class="pressthis-bookmarklet-demo">
			<a class="pressthis-bookmarklet" onclick="return false;" href="<?php echo htmlspecialchars( get_shortcut_link() ); ?>"><span><?php _e( 'Press This' ); ?></span></a>
		</p>
	</div>

	<div class="col">
		<img src="//s.w.org/images/core/4.2/press-this.jpg" />
	</div>
</div>

<div class="feature-section two-col">
	<div class="col">
		<img src="//s.w.org/images/core/4.2/unicode.png" />
	</div>
	<div class="col">
		<h3><?php _e( 'Extended character support' ); ?></h3>
		<p><?php _e( 'Writing in WordPress, whatever your language, just got better. WordPress 4.2 supports a host of new characters out-of-the-box, including native Chinese, Japanese, and Korean characters, musical and mathematical symbols, and hieroglyphs.' ); ?></p>
		<p><?php
			/* translators: 1: heart emoji, 2: frog face emoji, 3, monkey emoji, 4: pizza emoji, 5: Emoji Codex link */
			printf( __( 'Don&#8217;t use any of those characters? You can still have fun &mdash; emoji are now available in WordPress! Get creative and decorate your content with %1$s, %2$s, %3$s, %4$s, and all the many other <a href="%5$s">emoji</a>.' ), '&#x1F499', '&#x1F438', '&#x1F412', '&#x1F355', __( 'https://codex.wordpress.org/Emoji' ) );
		?></p>
	</div>
</div>

<div class="changelog feature-section three-col">
	<div>
		<img src="//s.w.org/images/core/4.2/theme-switcher.png" />
		<h3><?php _e( 'Switch themes in the Customizer' ); ?></h3>
		<p><?php _e( 'Browse and preview your installed themes from the Customizer. Make sure the theme looks great with <em>your</em> content, before it debuts on your site. ' ); ?></p>
	</div>
	<div>
		<img src="//s.w.org/images/core/4.2/embeds.png" />
		<h3><?php _e( 'Even more embeds' ); ?></h3>
		<p><?php _e( 'Paste links from Tumblr.com and Kickstarter and watch them magically appear right in the editor. With every release, your publishing and editing experience get closer together.' ); ?></p>
	</div>
	<div class="last-feature">
		<img src="//s.w.org/images/core/4.2/plugins.png" />
		<h3><?php _e( 'Streamlined plugin updates' ); ?></h3>
		<p><?php _e( 'Goodbye boring loading screen, hello smooth and simple plugin updates. Click <em>Update&nbsp;Now</em> and watch the magic happen.' ); ?></p>
	</div>
</div>

<div class="changelog under-the-hood feature-list">
	<h3><?php _e( 'Under the Hood' ); ?></h3>

	<div class="feature-section col two-col">
		<div>
			<h4><?php _e( 'utf8mb4 support' ); ?></h4>
			<p><?php _e( 'Database character encoding has changed from utf8 to utf8mb4, which adds support for a whole range of new 4-byte characters.' ); ?></p>

			<h4><?php _e( 'JavaScript accessibility' ); ?></h4>
			<p><?php
				/* translators: %s wp.a11y.speak() */
				printf( __( 'You can now send audible notifications to screen readers in JavaScript with %s. Pass it a string, and an update will be sent to a dedicated ARIA live notifications area.' ), '<code>wp.a11y.speak()</code>' );
			?></p>
		</div>
		<div class="last-feature">
			<h4><?php _e( 'Shared term splitting' ); ?></h4>
			<p><?php
				/* translators: 1: Term splitting guide link */
				printf( __( 'Terms shared across multiple taxonomies will be split when one of them is updated. Find out more in the <a href="%1$s">Plugin Developer Handbook</a>.' ), 'https://developer.wordpress.org/plugins/taxonomy/working-with-split-terms-in-wp-4-2/' );
			?></p>

			<h4><?php _e( 'Complex query ordering' ); ?></h4>
			<p><?php
				/* translators: 1: WP_Query, 2: WP_Comment_Query, 3: WP_User_Query */
				printf( __( '%1$s, %2$s, and %3$s now support complex ordering with named meta query clauses.' ), '<code>WP_Query</code>', '<code>WP_Comment_Query</code>', '<code>WP_User_Query</code>' );
			?></p>
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
