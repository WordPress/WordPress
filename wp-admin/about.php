<?php
/**
 * About This Version administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

wp_enqueue_script( 'underscore' );

/* translators: Page title of the About WordPress page in the admin. */
$title = _x( 'About', 'page title' );

list( $display_version ) = explode( '-', get_bloginfo( 'version' ) );

wp_enqueue_style( 'wp-block-library' );

include( ABSPATH . 'wp-admin/admin-header.php' );
?>
	<div class="wrap about-wrap full-width-layout">
		<h1><?php printf( __( 'Welcome to WordPress&nbsp;%s' ), $display_version ); ?></h1>

		<p class="about-text"><?php printf( __( 'Thank you for updating to the latest version!' ), $display_version ); ?></p>

		<?php
		if (
			// Was the Gutenberg plugin installed before upgrading to 5.0.x?
			get_option( 'upgrade_500_was_gutenberg_active' ) == '1' &&
			current_user_can( 'activate_plugins' ) &&
			// Has it not been reactivated since?
			is_plugin_inactive( 'gutenberg/gutenberg.php' ) &&
			// Is it still installed?
			file_exists( WP_PLUGIN_DIR . '/gutenberg/gutenberg.php' )
		) :
			?>
			<div class="about-text" style="font-style:italic;">
				<?php
				printf(
					/* translators: 1: WordPress version, 2: HTML start tag of link, 3: HTML end tag of link */
					__( 'The Gutenberg plugin has been deactivated, as the features are now included in WordPress %1$s by default. If you&#8217;d like to continue to test the upcoming changes in the WordPress editing experience, please %2$sreactivate the Gutenberg plugin%3$s.' ),
					$display_version,
					'<a href="' . esc_url( self_admin_url( 'plugins.php?s=gutenberg&plugin_status=all' ) ) . '">',
					'</a>'
				);
				?>
			</div>
		<?php elseif ( ! file_exists( WP_PLUGIN_DIR . '/classic-editor/classic-editor.php' ) ) : ?>
			<p class="about-text">
				&#x2139; <a href="#classic-editor"><?php _e( 'Learn how to keep using the old editor.' ); ?></a>
			</p>
		<?php endif; ?>

		<div class="wp-badge"><?php printf( __( 'Version %s' ), $display_version ); ?></div>

		<h2 class="nav-tab-wrapper wp-clearfix">
			<a href="about.php" class="nav-tab nav-tab-active"><?php _e( 'What&#8217;s New' ); ?></a>
			<a href="credits.php" class="nav-tab"><?php _e( 'Credits' ); ?></a>
			<a href="freedoms.php" class="nav-tab"><?php _e( 'Freedoms' ); ?></a>
			<a href="freedoms.php?privacy-notice" class="nav-tab"><?php _e( 'Privacy' ); ?></a>
		</h2>

		<div class="feature-section one-col">
			<div class="col">
				<h2><?php _e( 'A Little Better Every Day' ); ?></h2>
			</div>
		</div>

		<div class="feature-section one-col">
			<div class="col">
				<picture>
					<img src="https://make.wordpress.org/core/files/2019/01/Servehappy-Notice-with-custom-link.png" alt="" width="200">
				</picture>
				<p><?php _e( 'You&#8217;ve successfully upgraded to WordPress 5.1! Following WordPress 5.0&#8212;a major release which introduced the new block editor&#8212;5.1 focuses on polish, in particular by improving overall performance of the editor. In addition, this release paves the way for a better, faster, and more secure WordPress with some essential tools for site administrators and developers.' ); ?></p>
			</div>
		</div>

		<div class="feature-section one-col">
			<div class="col">
				<h2><?php _e( 'Site Health' ); ?></h2>
				<p><?php printf( __( 'With security and speed in mind, this release introduces WordPress&#8217;s first <a href="%s">Site Health</a> features. WordPress will start showing notices to administrators of sites that run long-outdated versions of PHP, which is the programming language that powers WordPress.' ), 'https://make.wordpress.org/core/2019/01/14/php-site-health-mechanisms-in-5-1/' ); ?></p>

				<p><?php _e( 'When installing new plugins, WordPress&#8217;s Site Health features will check whether a plugin requires a version of PHP incompatible with your site. If so, WordPress will prevent you from installing that plugin.' ); ?></p>
			</div>
		</div>


		<div class="feature-section one-col cta">
			<?php
			$response = wp_check_php_version();
			if ( $response && isset( $response['is_acceptable'] ) && ! $response['is_acceptable'] && current_user_can( 'update_php' ) ) :
				?>
				<p><em><?php _e( 'WordPress has detected your site is running an outdated version of PHP. You will see this notice on your dashboard with instructions for contacting your host.' ); ?></em></p>
			<?php endif; ?>

			<div class="col">
				<a class="button button-primary button-hero" href=""><?php _e( 'Learn more about updating PHP' ); ?></a>
			</div>
		</div>

		<div class="feature-section one-col">
			<div class="col">
				<h2><?php _e( 'Editor Performance' ); ?></h2>
				<p><?php _e( 'Introduced in WordPress 5.0, the new block editor continues to improve. Most significantly, WordPress 5.1 includes solid performance improvements within the editor. The editor should feel a little quicker to start, and typing should feel smoother. Nevertheless, expect more performance improvements in the next releases.' ); ?></p>
			</div>
		</div>

		<?php if ( current_user_can( 'edit_posts' ) ) { ?>
			<div class="feature-section one-col cta">
				<div class="col">
					<a class="button button-primary button-hero" href="<?php echo esc_url( admin_url( 'post-new.php' ) ); ?>"><?php _e( 'Build your first post' ); ?></a>
				</div>
			</div>
		<?php } ?>

		<hr />

		<div class="under-the-hood feature-section">
			<div class="col">
				<h2><?php _e( 'Developer Happiness' ); ?></h2>
			</div>
		</div>

		<div class="under-the-hood feature-section three-col">
			<div class="col">
				<h3><?php _e( 'Multisite Metadata' ); ?></h3>
				<p>
					<?php _e( '5.1 introduces a new database table to store metadata associated with sites and allows for the storage of arbitrary site data relevant in a multisite / network context.' ); ?>
					<br>
					<?php printf( __( '<a href="%s">Read more.</a>' ), 'https://make.wordpress.org/core/2019/01/28/multisite-support-for-site-metadata-in-5-1/' ); ?>
				</p>
			</div>
			<div class="col">
				<h3><?php _e( 'Cron API' ); ?></h3>
				<p>
					<?php _e( 'The Cron API has been updated with new functions to assist with returning data and includes new filters for modifying cron storage. Other changes in behavior affect cron spawning on servers running FastCGI and PHP-FPM versions 7.0.16 and above.' ); ?>
					<br>
					<?php printf( __( '<a href="%s">Read more.</a>' ), 'https://make.wordpress.org/core/2019/01/09/cron-improvements-with-php-fpm-in-wordpress-5-1/' ); ?>
				</p>
			</div>
			<div class="col">
				<h3><?php _e( 'New JS Build Processes' ); ?></h3>
				<p>
					<?php _e( 'WordPress 5.1 features a new JavaScript build option, following the large reorganization of code started in the 5.0 release.' ); ?>
					<br>
					<?php printf( __( '<a href="%s">Read more.</a>' ), 'https://make.wordpress.org/core/2018/05/16/preparing-wordpress-for-a-javascript-future-part-1-build-step-and-folder-reorganization/' ); ?>
				</p>
			</div>
			<div class="col">
				<h3><?php _e( 'Other Developer Goodness' ); ?></h3>
				<p>
					<?php _e( 'Miscellaneous improvements include updates to values for the <code>WP_DEBUG_LOG</code> constant, new test config file constant in the test suite, new plugin action hooks, short-circuit filters for <code>wp_unique_post_slug()</code> and <code>WP_User_Query</code> and <code>count_users()</code>, a new <code>human_readable_duration</code> function, improved taxonomy metabox sanitization, limited <code>LIKE</code> support for meta keys when using <code>WP_Meta_Query</code>, a new “doing it wrong” notice when registering REST API endpoints, and more!' ); ?>
					<br>
					<?php printf( __( '<a href="%s">Read more.</a>' ), 'https://make.wordpress.org/core/2019/01/23/miscellaneous-developer-focused-changes-in-5-1/' ); ?>
				</p>
			</div>
		</div>

		<div class="under-the-hood feature-section one-col cta">
			<div class="col">
				<a class="button button-primary button-hero" href="<?php echo esc_url( 'https://developer.wordpress.org/' ); ?>"><?php _e( 'Learn how to get started' ); ?></a>
			</div>
		</div>

		<hr />

		<?php if ( ! file_exists( WP_PLUGIN_DIR . '/classic-editor/classic-editor.php' ) ) : ?>
			<div class="feature-section one-col" id="classic-editor">
				<div class="col">
					<h2><?php _e( 'Keep it Classic' ); ?></h2>
				</div>
			</div>

			<div class="full-width">
				<picture>
					<source type="image/webp" media="(max-width: 782px)" srcset="https://s.w.org/images/core/5.0/classic/Classic%20Mobile1x.webp 1x, https://s.w.org/images/core/5.0/classic/Classic%20Mobile.webp 2x" />
					<source media="(max-width: 782px)" srcset="https://s.w.org/images/core/5.0/classic/Classic%20Mobile1x.jpg 1x, https://s.w.org/images/core/5.0/classic/Classic%20Mobile.jpg 2x" />
					<source type="image/webp" srcset="https://s.w.org/images/core/5.0/classic/Classic@1x.webp 1x, https://s.w.org/images/core/5.0/classic/Classic.webp 2x" />
					<img src="https://s.w.org/images/core/5.0/classic/Classic@1x.jpg" srcset="https://s.w.org/images/core/5.0/classic/Classic@1x.jpg 1x, https://s.w.org/images/core/5.0/header/Classic.jpg 2x" alt="">
				</picture>
			</div>

			<div class="feature-section one-col">
				<div class="col">
					<p><?php _e( 'Prefer to stick with the familiar Classic Editor? No problem! Support for the Classic Editor plugin will remain in WordPress through 2021.' ); ?></p>
					<p><?php _e( 'The Classic Editor plugin restores the previous WordPress editor and the Edit Post screen. It lets you keep using plugins that extend it, add old-style meta boxes, or otherwise depend on the previous editor. To install, visit your plugins page and click the &#8220;Install Now&#8221; button next to &#8220;Classic Editor&#8221;. After the plugin finishes installing, click &#8220;Activate&#8221;. That’s it!' ); ?></p>
					<p><?php _e( 'Note to users of assistive technology: if you experience usability issues with the block editor, we recommend you continue to use the Classic Editor.' ); ?></p>
					<?php if ( current_user_can( 'install_plugins' ) ) { ?>
						<div class="col cta">
							<a class="button button-primary button-hero" href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'plugin-install.php?tab=favorites&user=wordpressdotorg&save=0' ), 'save_wporg_username_' . get_current_user_id() ) ); ?>"><?php _e( 'Install the Classic Editor' ); ?></a>
						</div>
					<?php } ?>
				</div>
			</div>

			<hr />
		<?php endif; ?>

		<div class="return-to-dashboard">
			<?php if ( current_user_can( 'update_core' ) && isset( $_GET['updated'] ) ) : ?>
				<a href="<?php echo esc_url( self_admin_url( 'update-core.php' ) ); ?>">
					<?php is_multisite() ? _e( 'Return to Updates' ) : _e( 'Return to Dashboard &rarr; Updates' ); ?>
				</a> |
			<?php endif; ?>
			<a href="<?php echo esc_url( self_admin_url() ); ?>"><?php is_blog_admin() ? _e( 'Go to Dashboard &rarr; Home' ) : _e( 'Go to Dashboard' ); ?></a>
		</div>
	</div>

	<script>
		(function( $ ) {
			$( function() {
				var $window = $( window );
				var $adminbar = $( '#wpadminbar' );
				var $sections = $( '.floating-header-section' );
				var offset = 0;

				// Account for Admin bar.
				if ( $adminbar.length ) {
					offset += $adminbar.height();
				}

				function setup() {
					$sections.each( function( i, section ) {
						var $section = $( section );
						// If the title is long, switch the layout
						var $title = $section.find( 'h2' );
						if ( $title.innerWidth() > 300 ) {
							$section.addClass( 'has-long-title' );
						}
					} );
				}

				var adjustScrollPosition = _.throttle( function adjustScrollPosition() {
					$sections.each( function( i, section ) {
						var $section = $( section );
						var $header = $section.find( 'h2' );
						var width = $header.innerWidth();
						var height = $header.innerHeight();

						if ( $section.hasClass( 'has-long-title' ) ) {
							return;
						}

						var sectionStart = $section.offset().top - offset;
						var sectionEnd = sectionStart + $section.innerHeight();
						var scrollPos = $window.scrollTop();

						// If we're scrolled into a section, stick the header
						if ( scrollPos >= sectionStart && scrollPos < sectionEnd - height ) {
							$header.css( {
								position: 'fixed',
								top: offset + 'px',
								bottom: 'auto',
								width: width + 'px'
							} );
						// If we're at the end of the section, stick the header to the bottom
						} else if ( scrollPos >= sectionEnd - height && scrollPos < sectionEnd ) {
							$header.css( {
								position: 'absolute',
								top: 'auto',
								bottom: 0,
								width: width + 'px'
							} );
						// Unstick the header
						} else {
							$header.css( {
								position: 'static',
								top: 'auto',
								bottom: 'auto',
								width: 'auto'
							} );
						}
					} );
				}, 100 );

				function enableFixedHeaders() {
					if ( $window.width() > 782 ) {
						setup();
						adjustScrollPosition();
						$window.on( 'scroll', adjustScrollPosition );
					} else {
						$window.off( 'scroll', adjustScrollPosition );
						$sections.find( '.section-header' )
							.css( {
								width: 'auto'
							} );
						$sections.find( 'h2' )
							.css( {
								position: 'static',
								top: 'auto',
								bottom: 'auto',
								width: 'auto'
							} );
					}
				}
				$( window ).resize( enableFixedHeaders );
				enableFixedHeaders();
			} );
		})( jQuery );
	</script>

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
_n_noop(
	'<strong>Version %1$s</strong> addressed %2$s bug.',
	'<strong>Version %1$s</strong> addressed %2$s bugs.'
);

/* translators: 1: WordPress version number, 2: plural number of bugs. Singular security issue. */
_n_noop(
	'<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bug.',
	'<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bugs.'
);

/* translators: 1: WordPress version number, 2: plural number of bugs. More than one security issue. */
_n_noop(
	'<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
	'<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.'
);

/* translators: %s: Codex URL */
__( 'For more information, see <a href="%s">the release notes</a>.' );
