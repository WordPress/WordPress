<?php
/**
 * About This Version administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

/* translators: Page title of the About WordPress page in the admin. */
$title = _x( 'About', 'page title' );

list( $display_version ) = explode( '-', get_bloginfo( 'version' ) );

include( ABSPATH . 'wp-admin/admin-header.php' );
?>
	<div class="wrap about-wrap full-width-layout">
		<h1>
			<?php
			/* translators: %s: The current WordPress version number */
			printf( __( 'Welcome to WordPress&nbsp;%s' ), $display_version );
			?>
		</h1>

		<p class="about-text"><?php printf( __( 'Congratulations on updating to WordPress 5.2! This update makes it easier than ever to fix your site if something goes wrong.' ), $display_version ); ?></p>

		<div class="wp-badge">
			<?php
			/* translators: %s: The current WordPress version number */
			printf( __( 'Version %s' ), $display_version );
			?>
		</div>

		<nav class="nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Secondary menu' ); ?>">
			<a href="about.php" class="nav-tab nav-tab-active"><?php _e( 'What&#8217;s New' ); ?></a>
			<a href="credits.php" class="nav-tab"><?php _e( 'Credits' ); ?></a>
			<a href="freedoms.php" class="nav-tab"><?php _e( 'Freedoms' ); ?></a>
			<a href="freedoms.php?privacy-notice" class="nav-tab"><?php _e( 'Privacy' ); ?></a>
		</nav>

		<h2 class="feature-section-header"><?php _e( 'Keeping Your Site Safe' ); ?></h2>

		<div class="feature-section headline-feature one-col">
			<div class="col">
				<p><?php _e( 'WordPress 5.2 gives you even more robust tools for identifying and fixing configuration issues and fatal errors. Whether you are a developer helping clients or you manage your site solo, these tools can help get you the right information when you need it.' ); ?></p>
				<div class="inline-svg">
					<img src="https://make.wordpress.org/core/files/2019/04/site-safe.png" alt="">
				</div>
			</div>
		</div>

		<hr />

		<div class="feature-section one-col is-wide wp-clearfix">
			<div class="col">
				<h3><?php _e( 'Site Health Check' ); ?></h3>
				<div class="inline-svg alignright">
					<img src="https://make.wordpress.org/core/files/2019/04/health-check.png" alt="">
				</div>
				<p>
					<?php
					printf(
						/* translators: 1: link to the WordPress 5.1 release post 2: link to /wp-admin/site-health.php 3: link to /wp-admin/site-health.php?tab=debug */
						__( 'Building on the <a href="%1$s">Site Health</a> features introduced in 5.1, this release adds two new pages to help debug common configuration issues. It also adds space where developers can include debugging information for site maintainers. Check your site <a href="%2$s">status</a>, and learn how to <a href="%3$s">debug</a> issues.' ),
						__( 'https://wordpress.org/news/2019/02/betty/' ),
						admin_url( 'site-health.php' ),
						admin_url( 'site-health.php?tab=debug' )
					);
					?>
				</p>
			</div>
		</div>

		<hr />

		<div class="feature-section one-col is-wide wp-clearfix">
			<div class="col">
				<div class="inline-svg alignleft">
					<img src="https://make.wordpress.org/core/files/2019/04/error-protection.png" alt="">
				</div>
				<h3><?php _e( 'PHP Error Protection' ); ?></h3>
				<p><?php _e( 'This administrator-focused update will let you safely fix or manage fatal errors without requiring developer time. It features better handling of the so-called “white screen of death,” and a way to enter recovery mode,  which pauses error-causing plugins or themes.' ); ?></p>
			</div>
		</div>

		<hr />

		<h3 class="under-the-hood-header"><?php _e( 'Improvements for Everyone' ); ?></h3>

		<div class="under-the-hood feature-section two-col">
			<div class="col cta">
				<h4><?php _e( 'Accessibility Updates' ); ?></h4>
				<p><?php _e( 'A number of changes work together to improve contextual awareness and keyboard navigation flow for those using screen readers and other assistive technologies.' ); ?></p>
			</div>
			<div class="col cta">
				<h4><?php _e( 'New Dashboard Icons' ); ?></h4>
				<p><?php _e( 'Thirteen new icons include Instagram, a suite of icons for BuddyPress, and rotated Earth icons for global inclusion. Find them in the Dashboard and have some fun!' ); ?></p>
			</div>
		</div>

		<hr />

		<h3 class="under-the-hood-header"><?php _e( 'Developer Happiness' ); ?></h3>

		<div class="under-the-hood feature-section two-col">
			<div class="col">
				<h4><a href="https://make.wordpress.org/core/2019/03/26/coding-standards-updates-for-php-5-6/"><?php _e( 'PHP Version Bump' ); ?></a></h4>
				<p><?php _e( 'The minimum supported PHP version is now 5.6.20. As of WordPress 5.2, themes and plugins can safely take advantage of namespaces, anonymous functions, and more!' ); ?></p>
			</div>
			<div class="col">
				<h4><a href="https://make.wordpress.org/core/2019/04/24/developer-focused-privacy-updates-in-5-2/"><?php _e( 'Privacy Updates' ); ?></a></h4>
				<p><?php _e( 'A new theme page template, a conditional function, and two CSS classes make designing and customizing the Privacy Policy page easier.' ); ?></p>
			</div>
		</div>
		<div class="under-the-hood feature-section two-col">
			<div class="col">
				<h4><a href="https://make.wordpress.org/core/2019/04/24/miscellaneous-developer-updates-in-5-2/"><?php _e( 'New Body Tag Hook' ); ?></a></h4>
				<p>
					<?php
					printf(
						/* translators: 1: wp_body_open 2: <body> */
						__( '5.2 introduces a <code>%1$s</code> hook, which lets themes support injecting code right at the beginning of the <code>%2$s</code> element.' ),
						'wp_body_open',
						'&lt;body&gt;'
					);
					?>
				</p>
			</div>
			<div class="col">
				<h4><a href="https://make.wordpress.org/core/2019/03/25/building-javascript/"><?php _e( 'Building JavaScript' ); ?></a></h4>
				<p><?php _e( 'With the addition of webpack and Babel configurations in the @wordpress/scripts package, developers won&#8217;t have to worry about setting up complex build tools to write modern JavaScript.' ); ?></p>
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
