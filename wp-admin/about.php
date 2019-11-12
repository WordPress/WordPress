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
	<div class="wrap about__container">

		<div class="about__header">
			<div class="about__header-title">
				<h1>
					<span><?php echo $display_version; ?></span>
					<?php _e( 'WordPress' ); ?>
				</h1>
			</div>

			<div class="about__header-badge"></div>

			<div class="about__header-text">
				<p>
					<?php
					printf(
						/* translators: %s: The current WordPress version number. */
						__( 'Introducing our most refined user experience with the improved block editor in WordPress %s!' ),
						$display_version
					);
					?>
				</p>
			</div>

			<nav class="about__header-navigation nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Secondary menu' ); ?>">
				<a href="about.php" class="nav-tab nav-tab-active" aria-current="page"><?php _e( 'What&#8217;s New' ); ?></a>
				<a href="credits.php" class="nav-tab"><?php _e( 'Credits' ); ?></a>
				<a href="freedoms.php" class="nav-tab"><?php _e( 'Freedoms' ); ?></a>
				<a href="privacy.php" class="nav-tab"><?php _e( 'Privacy' ); ?></a>
			</nav>
		</div>

		<div class="about__section is-feature">
			<p>
				<?php _e( '5.3 expands and refines the block editor introduced in WordPress 5.0 with a new block, more intuitive interactions, and improved accessibility. New features in the editor increase design freedoms, provide additional layout options and style variations to allow designers complete control over the look of a site. This release also introduces the Twenty Twenty theme giving the user more design flexibility and integration with the block editor. Creating beautiful web pages and advanced layouts has never been easier.' ); ?>
			</p>
		</div>

		<hr />

		<div class="about__section has-2-columns">
			<div class="column is-edge-to-edge has-accent-background-color">
				<div class="about__image aligncenter">
					<img src="data:image/svg+xml;charset=utf8,%3Csvg width='660' height='818' viewbox='0 0 660 818' xmlns='http://www.w3.org/2000/svg'%3E%3Crect x='99' y='178' width='132' height='132' fill='%23F4EFE1'/%3E%3Crect x='231' y='310' width='99' height='99' fill='%2344141E'/%3E%3Crect x='330' y='409' width='132' height='132' fill='%23F4EFE1'/%3E%3Crect x='462' y='541' width='99' height='99' fill='%2344141E'/%3E%3C/svg%3E" alt="" />
				</div>
			</div>
			<div class="column is-vertically-aligned-center">
				<h2><?php _e( 'Block Editor Improvements' ); ?></h2>
				<p>
					<?php _e( 'This enhancement-focused update introduces over 150 new features and usability improvements, including improved large image support for uploading non-optimized, high-resolution pictures taken from your smartphone or other high-quality cameras. Combined with larger default image sizes, pictures always look their best.' ); ?>
				</p>

				<p>
					<?php _e( 'Accessibility improvements include the integration of block editor styles in the admin interface. These improved styles fix many accessibility issues: color contrast on form fields and buttons, consistency between editor and admin interfaces, new snackbar notices, standardizing to the default WordPress color scheme, and the introduction of Motion to make interacting with your blocks feel swift and natural. For people who use a keyboard to navigate the dashboard, the block editor now has a Navigation mode. This lets you jump from block to block without tabbing through every part of the block controls.' ); ?>
				</p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column is-vertically-aligned-center">
				<h2><?php _e( 'Expanded Design Flexibility' ); ?></h2>
				<p>
					<?php
					printf(
						/* translators: %s: The current WordPress version number. */
						__( 'WordPress %s adds even more robust tools for creating amazing designs.' ),
						$display_version
					);
					?>
				</p>
				<ul>
					<li><?php _e( 'The new Group block lets you easily divide your page into colorful sections' ); ?></li>
					<li><?php _e( 'The Columns block now supports fixed column widths' ); ?></li>
					<li><?php _e( 'The new Predefined layouts make it a cinch to arrange content into advanced designs' ); ?></li>
					<li><?php _e( 'Heading blocks now offer controls for text color' ); ?></li>
					<li><?php _e( 'Additional style options allow you to set your preferred style for any block that supports this feature' ); ?></li>
				</ul>
			</div>
			<div class="column is-edge-to-edge has-accent-background-color">
				<div class="about__image aligncenter">
					<img src="data:image/svg+xml;charset=utf8,%3Csvg width='500' height='500' viewbox='0 0 500 500' xmlns='http://www.w3.org/2000/svg'%3E%3Crect x='75' y='200' width='150' height='75' fill='%2344141E'/%3E%3Crect x='175' y='75' width='50' height='100' fill='%2385273B'/%3E%3Crect x='75' y='75' width='75' height='100' fill='%23F4EFE1'/%3E%3Crect x='250' y='200' width='175' height='75' fill='%2344141E'/%3E%3Crect x='350' y='75' width='75' height='100' fill='%2385273B'/%3E%3Crect x='250' y='75' width='75' height='100' fill='%23F4EFE1'/%3E%3Crect x='75' y='375' width='150' height='50' fill='%2344141E'/%3E%3Crect x='175' y='300' width='50' height='50' fill='%2385273B'/%3E%3Crect x='75' y='300' width='75' height='50' fill='%23F4EFE1'/%3E%3Crect x='250' y='372.5' width='175' height='52.5' fill='%2344141E'/%3E%3Crect x='350' y='300' width='75' height='50' fill='%2385273B'/%3E%3Crect x='250' y='300' width='75' height='50' fill='%23F4EFE1'/%3E%3C/svg%3E%0A" alt="">
				</div>
			</div>
		</div>

		<div class="about__section has-2-columns has-subtle-background-color">
			<div class="column is-vertically-aligned-center">
				<h2><?php _e( 'Introducing Twenty Twenty' ); ?></h2>
				<p><?php _e( 'As the block editor celebrates its first birthday, we are proud that Twenty Twenty is designed with flexibility at its core. Show off your services or products with a combination of columns, groups, and media blocks. Set your content to wide or full alignment for dynamic and engaging layouts. Or let your thoughts be the star with a centered content column!' ); ?></p>

				<p>
					<?php
					printf(
						/* translators: %s: Link to the Inter font website. */
						__( 'As befits a theme called Twenty Twenty, clarity and readability is also a big focus. The theme includes the typeface <a href="%s">Inter</a>, designed by Rasmus Andersson. Inter comes in a Variable Font version, a first for default themes, which keeps load times short by containing all weights and styles of Inter in just two font files.' ),
						'https://rsms.me/inter/'
					);
					?>
				</p>
			</div>
			<div class="column is-edge-to-edge">
				<div class="about__image aligncenter">
					<img src="https://s.w.org/images/core/5.3/twentytwenty-mobile.png" alt="" />
				</div>
			</div>
		</div>

		<div class="about__section has-subtle-background-color">
			<div class="column is-edge-to-edge">
				<div class="about__image aligncenter">
					<img src="https://s.w.org/images/core/5.3/twentytwenty-desktop.png" alt="" />
				</div>
			</div>
		</div>

		<hr />

		<div class="about__section has-3-columns">
			<h2 class="is-section-header"><?php _e( 'Improvements for Everyone' ); ?></h2>

			<div class="column">
				<h3><?php _e( 'Automatic Image Rotation' ); ?></h3>
				<p><?php _e( 'Your images will be correctly rotated upon upload according to the embedded orientation data. This feature was first proposed nine years ago and made possible through the perserverance of many dedicated contributors.' ); ?></p>
			</div>
			<div class="column">
				<h3><?php _e( 'Site Health Checks' ); ?></h3>
				<p><?php _e( 'The improvements introduced in 5.3 make it even easier to identify issues. Expanded recommendations highlight areas that may need troubleshooting on your site from the Health Check screen.' ); ?></p>
			</div>
			<div class="column">
				<h3><?php _e( 'Admin Email Verification' ); ?></h3>
				<p><?php _e( 'You’ll now be periodically asked to confirm that your admin email address is up to date when you log in as an administrator. This reduces the chance of getting locked out of your site if you change your email address.' ); ?></p>
			</div>
		</div>

		<div class="about__section">
			<div class="column is-edge-to-edge">
				<div class="about__image aligncenter">
					<img src="data:image/svg+xml;charset=utf8,%3Csvg width='1000' height='498' viewbox='0 0 1000 498' xmlns='http://www.w3.org/2000/svg'%3E%3Crect x='865.463' y='36.8596' width='133.8' height='132.326' fill='%23942F44'/%3E%3Crect x='865.463' y='180.98' width='133.8' height='132.326' fill='%23942F44'/%3E%3Crect x='866.2' y='328.05' width='133.8' height='132.694' fill='%23942F44'/%3E%3Crect y='331.736' width='405.455' height='134.169' fill='%234E1521'/%3E%3Crect y='36.8596' width='405.455' height='129.008' fill='%234E1521'/%3E%3Crect y='184.298' width='387.025' height='133.8' fill='%234E1521'/%3E%3Crect x='719.13' y='34.6479' width='133.8' height='428.677' fill='%23BD3854'/%3E%3Crect x='571.323' y='18.4297' width='133.8' height='423.885' fill='%23BD3854'/%3E%3Crect x='423.516' y='35.0164' width='133.8' height='425.728' fill='%23BD3854'/%3E%3C/svg%3E" alt="" />
				</div>
			</div>
		</div>

		<hr />

		<div class="about__section has-2-columns has-subtle-background-color">
			<h2 class="is-section-header"><?php _e( 'For Developers' ); ?></h2>

			<div class="column">
				<h3><?php _e( 'Date/Time Component Fixes' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: Link to the date/time developer notes. */
						__( 'Developers can now work with <a href="%s">dates and timezones</a> in a more reliable way. Date and time functionality has received a number of new API functions for unified timezone retrieval and PHP interoperability, as well as many bug fixes.' ),
						'https://make.wordpress.org/core/2019/09/23/date-time-improvements-wp-5-3/'
					);
					?>
				</p>
			</div>
			<div class="column">
				<h3><?php _e( 'PHP 7.4 Compatibility' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: Link to the PHP 7 developer notes. */
						__( 'WordPress 5.3 aims to fully support PHP 7.4. This release contains <a href="%s">multiple changes</a> to remove deprecated functionality and ensure compatibility. WordPress continues to encourage all users to run the latest and greatest versions of PHP.' ),
						'https://make.wordpress.org/core/2019/10/11/wordpress-and-php-7-4/'
					);
					?>
				</p>
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

/* translators: %s: WordPress version number. */
__( '<strong>Version %s</strong> addressed one security issue.' );
/* translators: %s: WordPress version number. */
__( '<strong>Version %s</strong> addressed some security issues.' );

/* translators: 1: WordPress version number, 2: Plural number of bugs. */
_n_noop(
	'<strong>Version %1$s</strong> addressed %2$s bug.',
	'<strong>Version %1$s</strong> addressed %2$s bugs.'
);

/* translators: 1: WordPress version number, 2: Plural number of bugs. Singular security issue. */
_n_noop(
	'<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bug.',
	'<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bugs.'
);

/* translators: 1: WordPress version number, 2: Plural number of bugs. More than one security issue. */
_n_noop(
	'<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
	'<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.'
);

/* translators: %s: Documentation URL. */
__( 'For more information, see <a href="%s">the release notes</a>.' );
