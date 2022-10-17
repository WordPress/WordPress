<?php
/**
 * About This Version administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';

/* translators: Page title of the About WordPress page in the admin. */
$title = _x( 'About', 'page title' );

list( $display_version ) = explode( '-', get_bloginfo( 'version' ) );

require_once ABSPATH . 'wp-admin/admin-header.php';
?>
	<div class="wrap about__container">

		<div class="about__header">
			<div class="about__header-title">
				<h1>
					<?php _e( 'WordPress' ); ?>
					<?php echo $display_version; ?>
				</h1>
			</div>

			<div class="about__header-text">
				<?php _e( 'The next stop on the road to full site editing' ); ?>
			</div>

			<nav class="about__header-navigation nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Secondary menu' ); ?>">
				<a href="about.php" class="nav-tab nav-tab-active" aria-current="page"><?php _e( 'What&#8217;s New' ); ?></a>
				<a href="credits.php" class="nav-tab"><?php _e( 'Credits' ); ?></a>
				<a href="freedoms.php" class="nav-tab"><?php _e( 'Freedoms' ); ?></a>
				<a href="privacy.php" class="nav-tab"><?php _e( 'Privacy' ); ?></a>
			</nav>
		</div>

		<hr />

		<div class="about__section changelog">
			<div class="column">
				<h2><?php _e( 'Maintenance and Security Releases' ); ?></h2>
				<p>
					<?php
					printf(
						/* translators: %s: WordPress version number. */
						__( '<strong>Version %s</strong> addressed some security issues.' ),
						'5.8.6'
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL. */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version. */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '5.8.6' )
						)
					);
					?>
				</p>
				<p>
					<?php
					printf(
						/* translators: %s: WordPress version number. */
						__( '<strong>Version %s</strong> addressed some security issues.' ),
						'5.8.5'
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL. */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version. */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '5.8.5' )
						)
					);
					?>
				</p>
				<p>
					<?php
					printf(
						/* translators: %s: WordPress version number. */
						__( '<strong>Version %s</strong> addressed some security issues.' ),
						'5.8.4'
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL. */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version. */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '5.8.4' )
						)
					);
					?>
				</p>
				<p>
					<?php
					printf(
						/* translators: %s: WordPress version number. */
						__( '<strong>Version %s</strong> addressed some security issues.' ),
						'5.8.3'
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL. */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version. */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '5.8.3' )
						)
					);
					?>
				</p>
				<p>
					<?php
					printf(
						/* translators: 1: WordPress version number, 2: plural number of bugs. */
						_n(
							'<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bug.',
							'<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bugs.',
							2
						),
						'5.8.2',
						number_format_i18n( 2 )
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '5.8.2' )
						)
					);
					?>
				</p>
				<p>
					<?php
					printf(
						/* translators: 1: WordPress version number, 2: plural number of bugs. */
						_n(
							'<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bug.',
							'<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bugs.',
							60
						),
						'5.8.1',
						number_format_i18n( 60 )
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '5.8.1' )
						)
					);
					?>
				</p>
			</div>
		</div>

		<hr class="is-large" />

		<div class="about__section">
			<h2 class="aligncenter">
				<?php _e( 'Three Essential Powerhouses' ); ?>
			</h2>
		</div>

		<div class="about__section has-2-columns is-wider-left">
			<div class="column about__image is-vertically-aligned-center">
				<img src="https://s.w.org/images/core/5.8/about-widgets-blocks.png" alt="" />
			</div>
			<div class="column">
				<h3>
					<?php _e( 'Manage Widgets with Blocks' ); ?>
				</h3>
				<p>
					<?php
					printf(
						/* translators: %s: Widgets dev note link. */
						__( 'After months of hard work, the power of blocks has come to both the Block Widgets Editor and the Customizer. Now you can add blocks both in widget areas across your site and with live preview through the Customizer. This opens up new possibilities to create content: from no-code mini layouts to the vast library of core and third-party blocks. For our developers, you can find more details in the <a href="%s">Widgets dev note</a>.' ),
						'https://make.wordpress.org/core/2021/06/29/block-based-widgets-editor-in-wordpress-5-8/'
					);
					?>
				</p>
			</div>
		</div>

		<div class="about__section has-2-columns is-wider-right">
			<div class="column">
				<h3>
					<?php _e( 'Display Posts with New Blocks and Patterns' ); ?>
				</h3>
				<p>
					<?php _e( 'The Query Loop Block makes it possible to display posts based on specified parameters; like a PHP loop without the code. Easily display posts from a specific category, to do things like create a portfolio or a page full of your favorite recipes. Think of it as a more complex and powerful Latest Posts Block! Plus, pattern suggestions make it easier than ever to create a list of posts with the design you want.' ); ?>
				</p>
			</div>
			<div class="column about__image is-vertically-aligned-center">
				<img src="https://s.w.org/images/core/5.8/about-query-loop.png" alt="" />
			</div>
		</div>

		<div class="about__section has-2-columns is-wider-left">
			<div class="column about__image is-vertically-aligned-center">
				<img src="https://s.w.org/images/core/5.8/about-template.png" alt="" />
			</div>
			<div class="column">
				<h3>
					<?php _e( 'Edit the Templates Around Posts' ); ?>
				</h3>
				<p>
					<?php
					_e( 'You can use the familiar block editor to edit templates that hold your content—simply activate a block theme or a theme that has opted in for this feature. Switch from editing your posts to editing your pages and back again, all while using a familiar block editor. There are more than 20 new blocks available within compatible themes. Read more about this feature and how to experiment with it in the release notes.' );
					?>
				</p>
			</div>
		</div>

		<hr class="is-large" />

		<div class="about__section">
			<h2 class="aligncenter">
				<?php _e( 'Three Workflow Helpers' ); ?>
			</h2>
		</div>

		<div class="about__section has-2-columns is-wider-left">
			<div class="column about__image is-vertically-aligned-center">
				<img src="https://s.w.org/images/core/5.8/about-list-view.png" alt="" />
			</div>
			<div class="column">
				<h3>
					<?php _e( 'Overview of the Page Structure' ); ?>
				</h3>
				<p>
					<?php
					_e( 'Sometimes you need a simple landing page, but sometimes you need something a little more robust. As blocks increase, patterns emerge, and content creation gets easier, new solutions are needed to make complex content easy to navigate. List View is the best way to jump between layers of content and nested blocks. Since the List View gives you an overview of all the blocks in your content, you can now navigate quickly to the precise block you need. Ready to focus completely on your content? Toggle it on or off to suit your workflow.' );
					?>
				</p>
			</div>
		</div>

		<div class="about__section has-2-columns is-wider-right">
			<div class="column">
				<h3>
					<?php _e( 'Suggested Patterns for Blocks' ); ?>
				</h3>
				<p>
					<?php
					_e( 'Starting in this release the Pattern Transformations tool will suggest block patterns based on the block you are using. Right now, you can give it a try in the Query Block and Social Icon Block. As more patterns are added, you will be able to get inspiration for how to style your site without ever leaving the editor!' );
					?>
				</p>
			</div>
			<div class="column about__image is-vertically-aligned-center">
				<img src="https://s.w.org/images/core/5.8/about-pattern-suggestions.png" alt="" />
			</div>
		</div>

		<div class="about__section has-2-columns is-wider-left">
			<div class="column about__image is-vertically-aligned-center">
				<img src="https://s.w.org/images/core/5.8/about-duotone.png" alt="" />
			</div>
			<div class="column">
				<h3>
					<?php _e( 'Style and Colorize Images' ); ?>
				</h3>
				<p>
					<?php
					_e( 'Colorize your image and cover blocks with duotone filters! Duotone can add a pop of color to your designs and style your images (or videos in the cover block) to integrate well with your themes. You can think of the duotone effect as a black and white filter, but instead of the shadows being black and the highlights being white, you pick your own colors for the shadows and highlights. There’s more to learn about how it works in the documentation.' );
					?>
				</p>
			</div>
		</div>

		<hr class="is-large" />

		<div class="about__section">
			<h2 class="aligncenter" style="margin-bottom:0;">
				<?php _e( 'For Developers to Explore' ); ?>
			</h2>
			<div class="column about__image is-vertically-aligned-center">
				<picture>
					<source srcset="https://s.w.org/images/core/5.8/about-theme-json.png, https://s.w.org/images/core/5.8/about-theme-json-2x.png 2x" />
					<img src="https://s.w.org/images/core/5.8/about-theme-json.png" alt="" />
				</picture>
			</div>
		</div>

		<div class="about__section has-1-column">
			<div class="column">
				<h3>
					<?php _e( 'Theme.json' ); ?>
				</h3>
				<p>
					<?php
					printf(
						/* translators: %s: Theme.json dev note link. */
						__( 'Introducing the Global Styles and Global Settings APIs: control the editor settings, available customization tools, and style blocks using a theme.json file in the active theme. This configuration file enables or disables features and sets default styles for both a website and blocks. If you build themes, you can experiment with this early iteration of a useful new feature. For more about what is currently available and how it works, <a href="%s">check out this dev note</a>.' ),
						'https://make.wordpress.org/core/2021/06/25/introducing-theme-json-in-wordpress-5-8/'
					);
					?>
				</p>
			</div>
		</div>

		<div class="about__section has-3-columns">
			<div class="column">
				<h3>
					<?php _e( 'Dropping support for Internet Explorer 11' ); ?>
				</h3>
				<p>
					<?php
					printf(
						/* translators: %s: Link to Browse Happy. */
						__( 'Support for Internet Explorer 11 has been dropped as of this release. This means you may have issues managing your site that will not be fixed in the future. If you are currently using IE11, it is strongly recommended that you <a href="%s">switch to a more modern browser</a>.' ),
						'https://browsehappy.com/'
					);
					?>
				</p>
			</div>
			<div class="column">
				<h3>
					<?php _e( 'Adding support for WebP' ); ?>
				</h3>
				<p>
					<?php
					_e( 'WebP is a modern image format that provides improved lossless and lossy compression for images on the web. WebP images are around 30% smaller on average than their JPEG or PNG equivalents, resulting in sites that are faster and use less bandwidth.' );
					?>
				</p>
			</div>
			<div class="column">
				<h3>
					<?php _e( 'Adding Additional Block Supports' ); ?>
				</h3>
				<p>
					<?php
					printf(
						/* translators: %1$s: Link to 5.6's block dev notes. %2$s: Link to 5.7's block dev notes. %3$s: Link to 5.8's block dev notes. */
						__( 'Expanding on previously implemented block supports in WordPress <a href="%1$s">5.6</a> and <a href="%2$s">5.7</a>, WordPress 5.8 introduces several new block support flags and new options to customize your registered blocks. More information is available in the <a href="%3$s">block supports dev note</a>.' ),
						'https://make.wordpress.org/core/2020/11/18/block-supports-in-wordpress-5-6/',
						'https://make.wordpress.org/core/2021/02/24/changes-to-block-editor-components-and-blocks/',
						'https://make.wordpress.org/core/2021/06/25/block-supports-api-updates-for-wordpress-5-8/'
					);
					?>
				</p>
			</div>
		</div>

		<hr class="is-small" />

		<div class="about__section">
			<div class="column">
				<h3><?php _e( 'Check the Field Guide for more!' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: WordPress 5.8 Field Guide link. */
						__( 'Check out the latest version of the WordPress Field Guide. It highlights developer notes for each change you may want to be aware of. <a href="%s">WordPress 5.8 Field Guide.</a>' ),
						'https://make.wordpress.org/core/2021/07/03/wordpress-5-8-field-guide/'
					);
					?>
				</p>
			</div>
		</div>

		<hr />

		<div class="return-to-dashboard">
			<?php if ( current_user_can( 'update_core' ) && isset( $_GET['updated'] ) ) : ?>
				<a href="<?php echo esc_url( self_admin_url( 'update-core.php' ) ); ?>">
					<?php is_multisite() ? _e( 'Go to Updates' ) : _e( 'Go to Dashboard &rarr; Updates' ); ?>
				</a> |
			<?php endif; ?>
			<a href="<?php echo esc_url( self_admin_url() ); ?>"><?php is_blog_admin() ? _e( 'Go to Dashboard &rarr; Home' ) : _e( 'Go to Dashboard' ); ?></a>
		</div>
	</div>

<?php require_once ABSPATH . 'wp-admin/admin-footer.php'; ?>

<?php

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

/* translators: 1: WordPress version number, 2: Link to update WordPress */
__( 'Important! Your version of WordPress (%1$s) is no longer supported, you will not receive any security updates for your website. To keep your site secure, please <a href="%2$s">update to the latest version of WordPress</a>.' );

/* translators: 1: WordPress version number, 2: Link to update WordPress */
__( 'Important! Your version of WordPress (%1$s) will stop receiving security updates in the near future. To keep your site secure, please <a href="%2$s">update to the latest version of WordPress</a>.' );
