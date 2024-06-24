<?php
/**
 * About This Version administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';

// Used in the HTML title tag.
/* translators: Page title of the About WordPress page in the admin. */
$title = _x( 'About', 'page title' );

list( $display_version ) = explode( '-', get_bloginfo( 'version' ) );

require_once ABSPATH . 'wp-admin/admin-header.php';
?>
	<div class="wrap about__container">

		<div class="about__header">
			<div class="about__header-title">
				<h1>
					<?php
					printf(
						/* translators: %s: Version number. */
						__( 'WordPress %s' ),
						$display_version
					);
					?>
				</h1>
			</div>

			<div class="about__header-text"></div>
		</div>

		<nav class="about__header-navigation nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Secondary menu' ); ?>">
			<a href="about.php" class="nav-tab nav-tab-active" aria-current="page"><?php _e( 'What&#8217;s New' ); ?></a>
			<a href="credits.php" class="nav-tab"><?php _e( 'Credits' ); ?></a>
			<a href="freedoms.php" class="nav-tab"><?php _e( 'Freedoms' ); ?></a>
			<a href="privacy.php" class="nav-tab"><?php _e( 'Privacy' ); ?></a>
			<a href="contribute.php" class="nav-tab"><?php _e( 'Get Involved' ); ?></a>
		</nav>

		<div class="about__section changelog has-subtle-background-color">
			<div class="column">
				<h2><?php _e( 'Maintenance and Security Releases' ); ?></h2>
				<p>
					<?php
					printf(
						__( '<strong>Version %s</strong> addressed some security issues.' ),
						'6.4.5'
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL. */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version. */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '6.4.5' )
						)
					);
					?>
				</p>

				<p>
					<?php
					printf(
						/* translators: 1: WordPress version number, 2: Plural number of bugs. */
						_n(
							'<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bug.',
							'<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bugs.',
							12
						),
						'6.4.4',
						'12'
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL. */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version. */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '6.4.4' )
						)
					);
					?>
				</p>

				<p>
					<?php
					printf(
						/* translators: 1: WordPress version number, 2: Plural number of bugs. */
						_n(
							'<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
							'<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.',
							21
						),
						'6.4.3',
						'21'
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL. */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version. */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '6.4.3' )
						)
					);
					?>
				</p>

				<p>
					<?php
					printf(
						/* translators: 1: WordPress version number, 2: Plural number of bugs. */
						_n(
							'<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bug.',
							'<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bugs.',
							7
						),
						'6.4.2',
						'7'
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL. */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version. */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '6.4.2' )
						)
					);
					?>
				</p>

				<p>
					<?php
					printf(
						/* translators: 1: WordPress version number, 2: Plural number of bugs. */
						_n(
							'<strong>Version %1$s</strong> addressed %2$s bug.',
							'<strong>Version %1$s</strong> addressed %2$s bugs.',
							4
						),
						'6.4.1',
						'4'
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL. */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version. */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '6.4.1' )
						)
					);
					?>
				</p>
			</div>
		</div>

		<div class="about__section">
			<div class="column">
				<h2 class="aligncenter">
					<?php
					printf(
						/* translators: %s: Version number. */
						__( 'Welcome to WordPress %s' ),
						$display_version
					);
					?>
				</h2>
				<p class="is-subheading">
					<?php _e( 'Every version of WordPress empowers your creative freedom, and WordPress 6.4 is no different. New features and upgrades to your site editing, design, and writing experience allow your ideas to take shape seamlessly. Elevate your site-building journey with the flexibility and power of WordPress 6.4.' ); ?>
				</p>
			</div>
		</div>

		<div class="about__section has-2-columns has-accent-4-background-color is-wider-right">
			<div class="column is-vertically-aligned-center">
				<h3><?php _e( 'Say hello to<br>Twenty Twenty-Four' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: Introduction to Twenty Twenty-Four link. */
						__( 'Experience the latest advancements in site editing with <a href="%s">Twenty Twenty-Four</a>. Built with three distinct use cases in mind, the versatility of the new default theme makes it an ideal choice for almost any type of website. Dive into its collection of templates and patterns and unlock a world of creative possibilities with just a few tweaks.' ),
						__( 'https://make.wordpress.org/core/2023/08/24/introducing-twenty-twenty-four/' )
					);
					?>
				</p>
			</div>
			<div class="column is-vertically-aligned-bottom is-edge-to-edge">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.4/1-Twenty-Twenty-Four.webp" alt="" height="600" width="600" />
				</div>
			</div>
		</div>

		<div class="about__section has-3-columns">
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.4/2-image-lightbox.webp" alt="" height="270" width="270" />
				</div>
				<h3 class="is-smaller-heading" style="margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Add a lightbox effect to images' ); ?></h3>
				<p><?php _e( 'Turn lightbox functionality on for interactive, full-screen images with a simple click. Apply it globally or to specific images to customize the viewing experience.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.4/3-categorize-patterns.webp" alt="" height="270" width="270" />
				</div>
				<h3 class="is-smaller-heading" style="margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Categorize and filter patterns' ); ?></h3>
				<p><?php _e( 'Organize your synced and unsynced patterns with categories. Explore advanced filtering in the Patterns section of the inserter to find them all more intuitively.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.4/4-command-palette.webp" alt="" height="270" width="270" />
				</div>
				<h3 class="is-smaller-heading" style="margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Get more done with the Command Palette' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: Command palette improvement link. */
						__( 'Enjoy <a href="%s">a refreshed design and more commands</a> to find what you\'re looking for, perform tasks efficiently, and save time as you create.' ),
						__( 'https://make.wordpress.org/core/2023/09/12/core-editor-improvement-commanding-the-command-palette/' )
					);
					?>
				</p>
			</div>
		</div>

		<div class="about__section has-3-columns">
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.4/5-renaming-groups.webp" alt="" height="270" width="270" />
				</div>
				<h3 class="is-smaller-heading" style="margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Rename Group blocks' ); ?></h3>
				<p><?php _e( 'Set custom names for Group blocks to easily organize and differentiate parts of your content. These names will be visible in List View.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.4/6-image-preview.webp" alt="" height="270" width="270" />
				</div>
				<h3 class="is-smaller-heading" style="margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Image previews in List View' ); ?></h3>
				<p><?php _e( 'New media previews for Gallery and Image blocks in List View let you visualize and locate at a glance where images on your content are.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.4/7-import-export-patterns.webp" alt="" height="270" width="270" />
				</div>
				<h3 class="is-smaller-heading" style="margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Share patterns across sites' ); ?></h3>
				<p><?php _e( 'Need to use your custom patterns on another site? It\'s simple! Import and export them as JSON files from the Site Editor\'s patterns view.' ); ?></p>
			</div>
		</div>

		<div class="about__section has-2-columns has-subtle-background-color is-wider-left">
			<div class="column is-vertically-aligned-center">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.4/8-captured-toolbar.webp" alt="" height="434" width="536" />
				</div>
			</div>
			<div class="column is-vertically-aligned-center">
				<h3><?php _e( 'Enjoy new writing improvements' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: New enhancements link. */
						__( '<a href="%s">New enhancements</a> ensure your content creation journey is smooth. Find new keyboard shortcuts in List View, refined list merging, and enhanced control over link settings. A revamped and cohesive toolbar experience for Navigation, List, and Quote blocks lets you efficiently work with the tooling options you need.' ),
						__( 'https://make.wordpress.org/core/2023/10/05/core-editor-improvement-ensuring-excellence-in-the-writing-experience/' )
					);
					?>
				</p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column is-vertically-aligned-center">
				<h3><?php _e( 'Build your creative vision with more design tools' ); ?></h3>
				<p><?php _e( 'Get creative with new background images in Group blocks and ensure consistent image dimensions with placeholder aspect ratios. Do you want to add buttons to your Navigation block? You can now do it conveniently without custom CSS. If you\'re working with synced patterns, alignment settings stay intact for a seamless pattern creation experience.' ); ?></p>
			</div>
			<div class="column is-vertically-aligned-center">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.4/9-design-tools.webp" alt="" height="355" width="436" />
				</div>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column is-vertically-aligned-center">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.4/10-block-hooks.webp" alt="" height="436" width="436" />
				</div>
			</div>
			<div class="column is-vertically-aligned-center">
				<h3><?php _e( 'Introducing Block Hooks' ); ?></h3>
				<p><?php _e( 'Block Hooks is a new powerful feature that enables plugins to auto-insert blocks into content relative to another block. Think of it as recommendations to make your work with blocks more intuitive. A new "Plugins" panel gives you complete control to match them to your needs—add, dismiss, and rearrange Block Hooks as desired.' ); ?></p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#CFCABE"/>
						<path d="M25.7781 16.8569L25.8 22.8573L28.9984 22.8572C29.805 22.8572 30.2796 23.6339 29.8204 24.2024L23.8213 31.6292C23.2633 32.3201 22.2013 31.9819 22.2013 31.1416L22.2 25.1481H19.0016C18.1961 25.1481 17.7212 24.3733 18.1782 23.8047L24.1496 16.3722C24.7055 15.6804 25.7749 16.0169 25.7781 16.8569Z" fill="#151515"/>
					</svg>
				</div>
				<h3 style="margin-top:calc(var(--gap) * 0.75);margin-bottom:calc(var(--gap) * 0.5)"><?php _e( 'Performance' ); ?></h3>
				<p><?php _e( 'WordPress 6.4 includes more than 100 performance updates for a faster and more efficient experience. Enhancements focus on template loading performance for Block Themes and Classic Themes, usage of the script loading strategies “defer” and “async” in core, blocks, and themes, and optimization of autoloaded options.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#CFCABE"/>
						<path d="M24 18.285C23.55 18.285 23.1638 18.1237 22.8413 17.8012C22.5188 17.4788 22.3575 17.0925 22.3575 16.6425C22.3575 16.1925 22.5188 15.8062 22.8413 15.4837C23.1638 15.1612 23.55 15 24 15C24.45 15 24.8363 15.1612 25.1588 15.4837C25.4813 15.8062 25.6425 16.1925 25.6425 16.6425C25.6425 17.0925 25.4813 17.4788 25.1588 17.8012C24.8363 18.1237 24.45 18.285 24 18.285ZM21.5925 33V21.0075C20.5725 20.9325 19.5863 20.8275 18.6338 20.6925C17.6813 20.5575 16.77 20.385 15.9 20.175L16.2375 18.825C17.5125 19.125 18.78 19.3387 20.04 19.4662C21.3 19.5938 22.62 19.6575 24 19.6575C25.38 19.6575 26.7 19.5938 27.96 19.4662C29.22 19.3387 30.4875 19.125 31.7625 18.825L32.1 20.175C31.23 20.385 30.3187 20.5575 29.3663 20.6925C28.4137 20.8275 27.4275 20.9325 26.4075 21.0075V33H25.0575V27.15H22.9425V33H21.5925Z" fill="#151515"/>
					</svg>
				</div>
				<h3 style="margin-top:calc(var(--gap) * 0.75);margin-bottom:calc(var(--gap) * 0.5)"><?php _e( 'Accessibility' ); ?></h3>
				<p><?php _e( 'Every release is committed to making WordPress accessible to everyone. 6.4 brings List View improvements and aria-label support for the Navigation block, among other highlights. The admin user interface (UI) includes enhancements to button placements, "Add New" menu items context, and Site Health spoken messages.' ); ?></p>
			</div>
		</div>

		<div class="about__section has-3-columns">
			<div class="column about__image is-vertically-aligned-top">
				<img src="<?php echo esc_url( admin_url( 'images/about-release-badge.svg?ver=6.4' ) ); ?>" alt="" height="270" width="270" />
			</div>
			<div class="column is-vertically-aligned-center" style="grid-column-end:span 2">
				<h3>
					<?php
					printf(
						/* translators: %s: Version number. */
						__( 'Learn more about WordPress %s' ),
						$display_version
					);
					?>
				</h3>
				<p>
					<?php
					printf(
						/* translators: 1: Learn WordPress link, 2: Workshops link. */
						__( '<a href="%1$s">Learn WordPress</a> is a free resource for new and experienced WordPress users. Learn is stocked with how-to videos on using various features in WordPress, <a href="%2$s">interactive workshops</a> for exploring topics in-depth, and lesson plans for diving deep into specific areas of WordPress.' ),
						'https://learn.wordpress.org/',
						'https://learn.wordpress.org/online-workshops/'
					);
					?>
				</p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#CFCABE"/>
						<path d="M23 34v-4h-5l-2.293-2.293a1 1 0 0 1 0-1.414L18 24h5v-2h-7v-6h7v-2h2v2h5l2.293 2.293a1 1 0 0 1 0 1.414L30 22h-5v2h7v6h-7v4h-2Zm-5-14h11.175l.646-.646a.5.5 0 0 0 0-.708L29.175 18H18v2Zm.825 8H30v-2H18.825l-.646.646a.5.5 0 0 0 0 .708l.646.646Z" fill="#151515"/>
					</svg>
				</div>
				<p style="margin-top:calc(var(--gap) / 2);">
					<?php
					printf(
						/* translators: 1: WordPress Field Guide link, 2: WordPress version number. */
						__( 'Explore the <a href="%1$s">WordPress %2$s Field Guide</a>. Learn about the changes in this release with detailed developer notes to help you build with WordPress.' ),
						__( 'https://make.wordpress.org/core/2023/10/23/wordpress-6-4-field-guide/' ),
						'6.4'
					);
					?>
				</p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#CFCABE"/>
						<path d="M28 19.75h-8v1.5h8v-1.5ZM20 23h8v1.5h-8V23ZM26 26.25h-6v1.5h6v-1.5Z" fill="#151515"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M29 16H19a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V18a2 2 0 0 0-2-2Zm-10 1.5h10a.5.5 0 0 1 .5.5v12a.5.5 0 0 1-.5.5H19a.5.5 0 0 1-.5-.5V18a.5.5 0 0 1 .5-.5Z" fill="#151515"/>
					</svg>
				</div>
				<p style="margin-top:calc(var(--gap) / 2);">
					<?php
					printf(
						/* translators: 1: WordPress Release Notes link, 2: WordPress version number. */
						__( '<a href="%1$s">Read the WordPress %2$s Release Notes</a> for information on installation, enhancements, fixed issues, release contributors, learning resources, and the list of file changes.' ),
						sprintf(
							/* translators: %s: WordPress version number. */
							esc_url( __( 'https://wordpress.org/documentation/wordpress-version/version-%s/' ) ),
							'6-4'
						),
						'6.4'
					);
					?>
				</p>
			</div>
		</div>

		<hr class="is-large" />

		<div class="return-to-dashboard">
			<?php
			if ( isset( $_GET['updated'] ) && current_user_can( 'update_core' ) ) {
				printf(
					'<a href="%1$s">%2$s</a> | ',
					esc_url( self_admin_url( 'update-core.php' ) ),
					is_multisite() ? __( 'Go to Updates' ) : __( 'Go to Dashboard &rarr; Updates' )
				);
			}

			printf(
				'<a href="%1$s">%2$s</a>',
				esc_url( self_admin_url() ),
				is_blog_admin() ? __( 'Go to Dashboard &rarr; Home' ) : __( 'Go to Dashboard' )
			);
			?>
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

/* translators: %s: The major version of WordPress for this branch. */
__( 'This is the final release of WordPress %s' );

/* translators: The localized WordPress download URL. */
__( 'https://wordpress.org/download/' );
