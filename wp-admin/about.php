<?php
/**
 * About This Version administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';

<<<<<<< main
// Used in the HTML title tag.
=======
wp_enqueue_script( 'underscore' );

>>>>>>> upstream/5.0-branch
/* translators: Page title of the About WordPress page in the admin. */
$title = _x( 'About', 'page title' );

list( $display_version ) = explode( '-', get_bloginfo( 'version' ) );

<<<<<<< main
require_once ABSPATH . 'wp-admin/admin-header.php';
=======
wp_enqueue_style( 'wp-block-library' );

include( ABSPATH . 'wp-admin/admin-header.php' );
>>>>>>> upstream/5.0-branch
?>
	<div class="wrap about__container">

<<<<<<< main
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
=======
		<p class="about-text"><?php printf( __( 'Thank you for updating to the latest version! WordPress %s introduces a robust new content creation experience.' ), $display_version ); ?></p>

		<?php if (
			// Was the Gutenberg plugin installed before upgrading to 5.0.x?
			get_option( 'upgrade_500_was_gutenberg_active' ) == '1'  &&
			current_user_can( 'activate_plugins' ) &&
			// Has it not been reactivated since?
			is_plugin_inactive( 'gutenberg/gutenberg.php' ) &&
			// Is it still installed?
			file_exists( WP_PLUGIN_DIR . '/gutenberg/gutenberg.php' )
		) : ?>
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
>>>>>>> upstream/5.0-branch

		<nav class="about__header-navigation nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Secondary menu' ); ?>">
			<a href="about.php" class="nav-tab nav-tab-active" aria-current="page"><?php _e( 'What&#8217;s New' ); ?></a>
			<a href="credits.php" class="nav-tab"><?php _e( 'Credits' ); ?></a>
			<a href="freedoms.php" class="nav-tab"><?php _e( 'Freedoms' ); ?></a>
<<<<<<< main
			<a href="privacy.php" class="nav-tab"><?php _e( 'Privacy' ); ?></a>
			<a href="contribute.php" class="nav-tab"><?php _e( 'Get Involved' ); ?></a>
		</nav>

		<div class="about__section aligncenter">
			<div class="column">
				<h2>
					<?php
					printf(
						/* translators: %s: Version number. */
						__( 'Welcome to WordPress %s' ),
						$display_version
					);
					?>
				</h2>
				<p class="is-subheading">
					<?php _e( 'Create beautiful and compelling websites more efficiently than ever. Whether you want to build an entire site without coding or are a developer looking to customize every detail, WordPress 6.3 has something for you.' ); ?>
				</p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column is-vertically-aligned-center">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.3/1-site-editor.webp" alt="" height="436" width="436" />
				</div>
			</div>
			<div class="column is-vertically-aligned-center">
				<h3><?php _e( 'Do everything in the Site Editor' ); ?></h3>
				<p><?php _e( 'WordPress 6.3 brings your content, templates, and patterns together in the Site Editor for the first time. Add pages, browse style variations, create synced patterns, and enjoy fine-tuned control over your navigation menus. No more time wasted switching across different site areas—now you can focus on what matters most. Creation to completion, all in one place.' ); ?></p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column is-vertically-aligned-center">
				<h3><?php _e( 'Create and sync patterns' ); ?></h3>
				<p><?php _e( 'Arrange blocks in unlimited ways and save them as Patterns for use throughout your site. You can even specify whether to sync your patterns (previously referred to as “reusable blocks”) so that one change applies to all parts of your site. Or, utilize patterns as a starting point with the ability to customize each instance.' ); ?></p>
			</div>
			<div class="column is-vertically-aligned-center">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.3/2-create-patterns.webp" alt="" height="436" width="436" />
				</div>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column is-vertically-aligned-center">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.3/3-command-palette.webp" alt="" height="436" width="436" />
				</div>
=======
			<a href="freedoms.php?privacy-notice" class="nav-tab"><?php _e( 'Privacy' ); ?></a>
		</h2>

		<div class="changelog point-releases">
			<h3><?php _e( 'Maintenance and Security Releases' ); ?></h3>
			<p>
				<?php
				printf(
					/* translators: %s: WordPress version number */
					__( '<strong>Version %s</strong> addressed some security issues.' ),
					'5.0.19'
				);
				?>
				<?php
				printf(
					/* translators: %s: HelpHub URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					sprintf(
						/* translators: %s: WordPress version */
						esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
						sanitize_title( '5.0.19' )
					)
				);
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: %s: WordPress version number */
					__( '<strong>Version %s</strong> addressed some security issues.' ),
					'5.0.18'
				);
				?>
				<?php
				printf(
					/* translators: %s: HelpHub URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					sprintf(
						/* translators: %s: WordPress version */
						esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
						sanitize_title( '5.0.18' )
					)
				);
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: %s: WordPress version number */
					__( '<strong>Version %s</strong> addressed some security issues.' ),
					'5.0.17'
				);
				?>
				<?php
				printf(
					/* translators: %s: HelpHub URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					sprintf(
						/* translators: %s: WordPress version */
						esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
						sanitize_title( '5.0.17' )
					)
				);
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: %s: WordPress version number */
					__( '<strong>Version %s</strong> addressed one security issue.' ),
					'5.0.16'
				);
				?>
				<?php
				printf(
					/* translators: %s: HelpHub URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					sprintf(
						/* translators: %s: WordPress version */
						esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
						sanitize_title( '5.0.16' )
					)
				);
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: %s: WordPress version number */
					__( '<strong>Version %s</strong> addressed some security issues.' ),
					'5.0.15'
				);
				?>
				<?php
				printf(
					/* translators: %s: HelpHub URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					sprintf(
						/* translators: %s: WordPress version */
						esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
						sanitize_title( '5.0.15' )
					)
				);
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: %s: WordPress version number */
					__( '<strong>Version %s</strong> addressed some security issues.' ),
					'5.0.14'
				);
				?>
				<?php
				printf(
					/* translators: %s: HelpHub URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					sprintf(
						/* translators: %s: WordPress version */
						esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
						sanitize_title( '5.0.14' )
					)
				);
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: %s: WordPress version number */
					__( '<strong>Version %s</strong> addressed one security issue.' ),
					'5.0.13'
				);
				?>
				<?php
				printf(
					/* translators: %s: HelpHub URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					sprintf(
						/* translators: %s: WordPress version */
						esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
						sanitize_title( '5.0.13' )
					)
				);
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: %s: WordPress version number */
					__( '<strong>Version %s</strong> addressed some security issues.' ),
					'5.0.12'
				);
				?>
				<?php
				printf(
					/* translators: %s: HelpHub URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					sprintf(
						/* translators: %s: WordPress version */
						esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
						sanitize_title( '5.0.12' )
					)
				);
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: %s: WordPress version number */
					__( '<strong>Version %s</strong> addressed some security issues.' ),
					'5.0.11'
				);
				?>
				<?php
				printf(
					/* translators: %s: HelpHub URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					sprintf(
						/* translators: %s: WordPress version */
						esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
						sanitize_title( '5.0.11' )
					)
				);
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: %s: WordPress version number */
					__( '<strong>Version %s</strong> addressed some security issues.' ),
					'5.0.10'
				);
				?>
				<?php
				printf(
					/* translators: %s: HelpHub URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					sprintf(
						/* translators: %s: WordPress version */
						esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
						sanitize_title( '5.0.10' )
					)
				);
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: %s: WordPress version number */
					__( '<strong>Version %s</strong> addressed some security issues.' ),
					'5.0.9'
				);
				?>
				<?php
				printf(
					/* translators: %s: HelpHub URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					sprintf(
						/* translators: %s: WordPress version */
						esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
						sanitize_title( '5.0.9' )
					)
				);
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: %s: WordPress version number */
					__( '<strong>Version %s</strong> addressed some security issues.' ),
					'5.0.8'
				);
				?>
				<?php
				printf(
					/* translators: %s: HelpHub URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					sprintf(
						/* translators: %s: WordPress version */
						esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
						sanitize_title( '5.0.8' )
					)
				);
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: %s: WordPress version number */
					__( '<strong>Version %s</strong> addressed some security issues.' ),
					'5.0.7'
				);
				?>
				<?php
				printf(
					/* translators: %s: HelpHub URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					sprintf(
						/* translators: %s: WordPress version */
						esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
						sanitize_title( '5.0.7' )
					)
				);
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: %s: WordPress version number */
					__( '<strong>Version %s</strong> addressed some security issues.' ),
					'5.0.6'
				);
				?>
				<?php
				printf(
					/* translators: %s: HelpHub URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					sprintf(
						/* translators: %s: WordPress version */
						esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
						sanitize_title( '5.0.6' )
					)
				);
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: %s: WordPress version number */
					__( '<strong>Version %s</strong> addressed some security issues.' ),
					'5.0.4'
				);
				?>
				<?php
				printf(
					/* translators: %s: HelpHub URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					sprintf(
						/* translators: %s: WordPress version */
						esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
						sanitize_title( '5.0.4' )
					)
				);
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: 1: WordPress version number, 2: plural number of bugs. */
					_n(
						'<strong>Version %1$s</strong> addressed %2$s bug.',
						'<strong>Version %1$s</strong> addressed %2$s bugs.',
						44
					),
					'5.0.3',
					number_format_i18n( 44 )
				);
				?>
				<?php
				/* translators: %s: Codex URL */
				printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_5.0.3' );
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: 1: WordPress version number, 2: plural number of bugs. */
					_n(
						'<strong>Version %1$s</strong> addressed %2$s bug.',
						'<strong>Version %1$s</strong> addressed %2$s bugs.',
						73
					),
					'5.0.2',
					number_format_i18n( 73 )
				);
				?>
				<?php
				/* translators: %s: Codex URL */
				printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_5.0.2' );
				?>
			</p>
			<p>
				<?php
				/* translators: %s: WordPress version number */
				printf( __( '<strong>Version %s</strong> addressed some security issues.' ), '5.0.1' );
				?>
				<?php
				/* translators: %s: Codex URL */
				printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_5.0.1' );
				?>
			</p>
		</div>

		<div class="feature-section one-col">
			<div class="col">
				<h2><?php _e( 'Say Hello to the New Editor' ); ?></h2>
			</div>
		</div>

		<div class="full-width">
			<picture>
				<source type="image/webp" media="(max-width: 782px)" srcset="https://s.w.org/images/core/5.0/header/Gutenberg%20Mobile1x.webp 1x, https://s.w.org/images/core/5.0/header/Gutenberg%20Mobile.webp 2x" />
				<source media="(max-width: 782px)" srcset="https://s.w.org/images/core/5.0/header/Gutenberg%20Mobile1x.jpg 1x, https://s.w.org/images/core/5.0/header/Gutenberg%20Mobile.jpg 2x" />
				<source type="image/webp" srcset="https://s.w.org/images/core/5.0/header/Gutenberg1x.webp 1x, https://s.w.org/images/core/5.0/header/Gutenberg.webp 2x" />
				<img src="https://s.w.org/images/core/5.0/header/Gutenberg1x.jpg" srcset="https://s.w.org/images/core/5.0/header/Gutenberg1x.jpg 1x, https://s.w.org/images/core/5.0/header/Gutenberg.jpg 2x" alt="">
			</picture>
		</div>

		<div class="feature-section one-col">
			<div class="col">
				<p><?php _e( 'You&#8217;ve successfully upgraded to WordPress 5.0! We’ve made some big changes to the editor. Our new block-based editor is the first step toward an exciting new future with a streamlined editing experience across your site. You’ll have more flexibility with how content is displayed, whether you are building your first site, revamping your blog, or write code for a living.' ); ?></p>
			</div>
		</div>

		<div class="feature-section four-col">
			<div class="col">
				<figure>
					<picture>
						<source type="image/webp" srcset="https://s.w.org/images/core/5.0/features/Plugins1x.webp 1x, https://s.w.org/images/core/5.0/features/Plugins.webp 2x">
						<img src="https://s.w.org/images/core/5.0/features/Plugins1x.jpg" srcset="https://s.w.org/images/core/5.0/features/Plugins1x.jpg 1x, https://s.w.org/images/core/5.0/features/Plugins.jpg 2x" alt="" width="250" height="250" />
					</picture>
					<figcaption><?php _e( 'Do more with fewer plugins.' ); ?></figcaption>
				</figure>
			</div>
			<div class="col">
				<figure>
					<picture>
						<source type="image/webp" srcset="https://s.w.org/images/core/5.0/features/Layout1x.webp 1x, https://s.w.org/images/core/5.0/features/Layout.webp 2x">
						<img src="https://s.w.org/images/core/5.0/features/Layout1x.jpg" srcset="https://s.w.org/images/core/5.0/features/Layout1x.jpg 1x, https://s.w.org/images/core/5.0/features/Layout.jpg 2x" alt="" width="250" height="250" />
					</picture>
					<figcaption><?php _e( 'Create modern, multimedia-heavy layouts.' ); ?></figcaption>
				</figure>
			</div>
			<div class="col">
				<figure>
					<picture>
						<source type="image/webp" srcset="https://s.w.org/images/core/5.0/features/Responsive1x.webp 1x, https://s.w.org/images/core/5.0/features/Responsive.webp 2x">
						<img src="https://s.w.org/images/core/5.0/features/Responsive1x.jpg" srcset="https://s.w.org/images/core/5.0/features/Responsive1x.jpg 1x, https://s.w.org/images/core/5.0/features/Responsive.jpg 2x" alt="" width="250" height="250" />
					</picture>
					<figcaption><?php _e( 'Work across all screen sizes and devices.' ); ?></figcaption>
				</figure>
			</div>
			<div class="col">
				<figure>
					<picture>
						<source type="image/webp" srcset="https://s.w.org/images/core/5.0/features/Editor%20Styles1x.webp 1x, https://s.w.org/images/core/5.0/features/Editor%20Styles.webp 2x">
						<img src="https://s.w.org/images/core/5.0/features/Editor%20Styles1x.jpg" srcset="https://s.w.org/images/core/5.0/features/Editor%20Styles1x.jpg 1x, https://s.w.org/images/core/5.0/features/Editor%20Styles.jpg 2x" alt="" width="250" height="250" />
					</picture>
					<figcaption><?php _e( 'Trust that your editor looks like your website.' ); ?></figcaption>
				</figure>
>>>>>>> upstream/5.0-branch
			</div>
			<div class="column is-vertically-aligned-center">
				<h3><?php _e( 'Work faster with the Command Palette' ); ?></h3>
				<p><?php _e( 'Switch to a specific template or open your editor preferences with a new tool that helps you quickly navigate expanded functionality. With simple keyboard shortcuts (⌘+k on Mac or Ctrl+k on Windows), clicking the sidebar search icon in Site View, or clicking the Title Bar, get where you need to go and do what you need to do in seconds.' ); ?></p>
			</div>
		</div>

<<<<<<< main
		<div class="about__section has-2-columns">
			<div class="column is-vertically-aligned-center">
				<h3><?php _e( 'Sharpen your designs with new tools' ); ?></h3>
				<p><?php _e( 'New design controls bring more versatility for fine-tuning designs, starting with the ability to customize your caption&#8217;s styles from the Styles Interface without coding. You can manage your duotone filters in Styles for supported blocks and pick from the options provided by your theme or disable them entirely. The Cover block gets added settings for text color, layout controls, and border options, making this powerful block even more handy.' ); ?></p>
			</div>
			<div class="column is-vertically-aligned-center">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.3/4-design-tools.webp" alt="" height="436" width="436" />
				</div>
			</div>
		</div>

		<div class="about__section has-3-columns">
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.3/5-style-revisions.webp" alt="" height="270" width="270" />
				</div>
				<h3 class="is-smaller-heading" style="margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Track design changes with Style Revisions' ); ?></h3>
				<p><?php _e( 'You can now see how your site looked at a specific time. Visualize these revisions in a timeline and access a one-click option to restore prior styles.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.3/6-footnotes-block.webp" alt="" height="270" width="270" />
				</div>
				<h3 class="is-smaller-heading" style="margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Annotate with the Footnotes block' ); ?></h3>
				<p><?php _e( 'Footnotes add convenient annotations throughout your content. Now you can add and link footnotes for any paragraph.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.3/7-details-block.webp" alt="" height="270" width="270" />
				</div>
				<h3 class="is-smaller-heading" style="margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Show or hide content with the Details block' ); ?></h3>
				<p><?php _e( 'Use the block to avoid spoiling a surprise, create an interactive Q&A section, or hide a long paragraph under a heading.' ); ?></p>
			</div>
		</div>

		<hr class="is-large" />

		<div class="about__section has-2-columns">
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#151515"/>
						<path d="M25.7781 16.8569L25.8 22.8573L28.9984 22.8572C29.805 22.8572 30.2796 23.6339 29.8204 24.2024L23.8213 31.6292C23.2633 32.3201 22.2013 31.9819 22.2013 31.1416L22.2 25.1481H19.0016C18.1961 25.1481 17.7212 24.3733 18.1782 23.8047L24.1496 16.3722C24.7055 15.6804 25.7749 16.0169 25.7781 16.8569Z" fill="white"/>
					</svg>
				</div>
				<h3 style="margin-top:calc(var(--gap) * 0.75);margin-bottom:calc(var(--gap) * 0.5)"><?php _e( 'Performance gets a boost' ); ?></h3>
				<p><?php _e( 'WordPress 6.3 has 170+ performance updates, including defer and async support for the Scripts API and fetchpriority support for images. These improvements can improve your website&#8217;s load time as perceived by visitors, along with block template resolution, image lazy-loading, and the emoji loader.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#151515"/>
						<path d="M24 18.285C23.55 18.285 23.1638 18.1237 22.8413 17.8012C22.5188 17.4788 22.3575 17.0925 22.3575 16.6425C22.3575 16.1925 22.5188 15.8062 22.8413 15.4837C23.1638 15.1612 23.55 15 24 15C24.45 15 24.8363 15.1612 25.1588 15.4837C25.4813 15.8062 25.6425 16.1925 25.6425 16.6425C25.6425 17.0925 25.4813 17.4788 25.1588 17.8012C24.8363 18.1237 24.45 18.285 24 18.285ZM21.5925 33V21.0075C20.5725 20.9325 19.5863 20.8275 18.6338 20.6925C17.6813 20.5575 16.77 20.385 15.9 20.175L16.2375 18.825C17.5125 19.125 18.78 19.3387 20.04 19.4662C21.3 19.5938 22.62 19.6575 24 19.6575C25.38 19.6575 26.7 19.5938 27.96 19.4662C29.22 19.3387 30.4875 19.125 31.7625 18.825L32.1 20.175C31.23 20.385 30.3187 20.5575 29.3663 20.6925C28.4137 20.8275 27.4275 20.9325 26.4075 21.0075V33H25.0575V27.15H22.9425V33H21.5925Z" fill="white"/>
					</svg>
=======
		<div class="feature-section one-col">
			<div class="col">
				<h2><?php _e( 'Building with Blocks' ); ?></h2>
				<p><?php _e( 'The new block-based editor won&#8217;t change the way any of your content looks to your visitors. What it will do is let you insert any type of multimedia in a snap and rearrange to your heart&#8217;s content. Each piece of content will be in its own block; a distinct wrapper for easy maneuvering. If you&#8217;re more of an HTML and CSS sort of person, then the blocks won&#8217;t stand in your way. WordPress is here to simplify the process, not the outcome.' ); ?></p>
				<video controls>
					<source src="https://s.w.org/images/core/5.0/videos/add-block.mp4" type="video/mp4">
					<source src="https://s.w.org/images/core/5.0/videos/add-block.webm" type="video/webm">
					<p><?php printf( __('Your browser doesn&#8217;t support HTML5 video. Here is a %1$slink to the video%2$s instead.'), '<a href="https://wordpress.org/gutenberg/files/2018/11/add-block.mp4">', '</a>'); ?></p>
				</video>
				<p><?php _e( 'We have tons of blocks available by default, and more get added by the community every day. Here are a few of the blocks to help you get started:' ); ?></p>
			</div>
		</div>

		<div class="feature-section eight-col">
			<div class="col">
				<figure>
					<picture>
						<source type="image/webp" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Paragraph@1x.webp 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Paragraph.webp 2x" />
						<img src="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Paragraph@1x.jpg" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Paragraph@1x.jpg 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Paragraph.jpg 2x" alt=""/>
					</picture>
					<figcaption><?php _e( 'Paragraph' ); ?></figcaption>
				</figure>
			</div>
			<div class="col">
				<figure>
					<picture>
						<source type="image/webp" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Heading@1x.webp 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Heading.webp 2x" />
						<img src="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Heading@1x.jpg" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Heading@1x.jpg 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Heading.jpg 2x" alt=""/>
					</picture>
					<figcaption><?php _e( 'Heading' ); ?></figcaption>
				</figure>
			</div>
			<div class="col">
				<figure>
					<picture>
						<source type="image/webp" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Preformatted@1x.webp 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Preformatted.webp 2x" />
						<img src="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Preformatted@1x.jpg" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Preformatted@1x.jpg 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Preformatted.jpg 2x" alt=""/>
					</picture>
					<figcaption><?php _e( 'Preformatted' ); ?></figcaption>
				</figure>
			</div>
			<div class="col">
				<figure>
					<picture>
						<source type="image/webp" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Quote@1x.webp 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Quote.webp 2x" />
						<img src="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Quote@1x.jpg" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Quote@1x.jpg 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Quote.jpg 2x" alt=""/>
					</picture>
					<figcaption><?php _e( 'Quote' ); ?></figcaption>
				</figure>
			</div>
			<div class="col">
				<figure>
					<picture>
						<source type="image/webp" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Image@1x.webp 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Image.webp 2x" />
						<img src="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Image@1x.jpg" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Image@1x.jpg 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Image.jpg 2x" alt=""/>
					</picture>
					<figcaption><?php _e( 'Image' ); ?></figcaption>
				</figure>
			</div>
			<div class="col">
				<figure>
					<picture>
						<source type="image/webp" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Gallery@1x.webp 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Gallery.webp 2x" />
						<img src="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Gallery@1x.jpg" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Gallery@1x.jpg 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Gallery.jpg 2x" alt=""/>
					</picture>
					<figcaption><?php _e( 'Gallery' ); ?></figcaption>
				</figure>
			</div>
			<div class="col">
				<figure>
					<picture>
						<source type="image/webp" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Cover%20Image@1x.webp 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Cover%20Image.webp 2x" />
						<img src="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Cover%20Image@1x.jpg" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Cover%20Image@1x.jpg 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Cover%20Image.jpg 2x" alt=""/>
					</picture>
					<figcaption><?php _e( 'Cover' ); ?></figcaption>
				</figure>
			</div>
			<div class="col">
				<figure>
					<picture>
						<source type="image/webp" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Video@1x.webp 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Video.webp 2x" />
						<img src="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Video@1x.jpg" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Video@1x.jpg 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Video.jpg 2x" alt=""/>
					</picture>
					<figcaption><?php _e( 'Video' ); ?></figcaption>
				</figure>
			</div>
			<div class="col">
				<figure>
					<picture>
						<source type="image/webp" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Audio@1x.webp 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Audio.webp 2x" />
						<img src="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Audio@1x.jpg" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Audio@1x.jpg 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Audio.jpg 2x" alt=""/>
					</picture>
					<figcaption><?php _e( 'Audio' ); ?></figcaption>
				</figure>
			</div>
			<div class="col">
				<figure>
					<picture>
						<source type="image/webp" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Column@1x.webp 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Column.webp 2x" />
						<img src="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Column@1x.jpg" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Column@1x.jpg 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Column.jpg 2x" alt=""/>
					</picture>
					<figcaption><?php _e( 'Columns' ); ?></figcaption>
				</figure>
			</div>
			<div class="col">
				<figure>
					<picture>
						<source type="image/webp" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20File@1x.webp 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20File.webp 2x" />
						<img src="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20File@1x.jpg" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20File@1x.jpg 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20File.jpg 2x" alt=""/>
					</picture>
					<figcaption><?php _e( 'File' ); ?></figcaption>
				</figure>
			</div>
			<div class="col">
				<figure>
					<picture>
						<source type="image/webp" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Code@1x.webp 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Code.webp 2x" />
						<img src="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Code@1x.jpg" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Code@1x.jpg 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Code.jpg 2x" alt=""/>
					</picture>
					<figcaption><?php _e( 'Code' ); ?></figcaption>
				</figure>
			</div>
			<div class="col">
				<figure>
					<picture>
						<source type="image/webp" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20List@1x.webp 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20List.webp 2x" />
						<img src="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20List@1x.jpg" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20List@1x.jpg 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20List.jpg 2x" alt=""/>
					</picture>
					<figcaption><?php _e( 'List' ); ?></figcaption>
				</figure>
			</div>
			<div class="col">
				<figure>
					<picture>
						<source type="image/webp" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Button@1x.webp 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Button.webp 2x" />
						<img src="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Button@1x.jpg" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Button@1x.jpg 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Button.jpg 2x" alt=""/>
					</picture>
					<figcaption><?php _e( 'Button' ); ?></figcaption>
				</figure>
			</div>
			<div class="col">
				<figure>
					<picture>
						<source type="image/webp" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Embeds@1x.webp 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Embeds.webp 2x" />
						<img src="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Embeds@1x.jpg" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Embeds@1x.jpg 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20Embeds.jpg 2x" alt=""/>
					</picture>
					<figcaption><?php _e( 'Embeds' ); ?></figcaption>
				</figure>
			</div>
			<div class="col">
				<figure>
					<picture>
						<source type="image/webp" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20More@1x.webp 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20More.webp 2x" />
						<img src="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20More@1x.jpg" srcset="https://s.w.org/images/core/5.0/blocks/Block%20Icon%20More@1x.jpg 1x, https://s.w.org/images/core/5.0/blocks/Block%20Icon%20More.jpg 2x" alt=""/>
					</picture>
					<figcaption><?php _e( 'More' ); ?></figcaption>
				</figure>
			</div>
		</div>

		<div class="feature-section one-col">
			<div class="col">
				<h2><?php _e( 'Freedom to Build, Freedom to Write' ); ?></h2>
				<p><?php _e( 'This new editing experience provides a more consistent treatment of design as well as content. If you&#8217;re building client sites, you can create reusable blocks. This lets your clients add new content anytime, while still maintaining a consistent look and feel.' ); ?></p>
				<video controls>
					<source src="https://s.w.org/images/core/5.0/videos/build.mp4" type="video/mp4">
					<source src="https://s.w.org/images/core/5.0/videos/build.webm" type="video/webm">
					<p><?php printf( __('Your browser doesn&#8217;t support HTML5 video. Here is a %1$slink to the video%2$s instead.'), '<a href="https://wordpress.org/gutenberg/files/2018/11/build.mp4">', '</a>'); ?></p>
				</video>
			</div>
		</div>

		<?php if ( current_user_can( 'edit_posts' ) ) { ?>
			<div class="feature-section one-col cta">
				<div class="col">
					<a class="button button-primary button-hero" href="<?php echo esc_url( admin_url( 'post-new.php' ) ); ?>"><?php _e( 'Build your first post' ); ?></a>
>>>>>>> upstream/5.0-branch
				</div>
				<h3 style="margin-top:calc(var(--gap) * 0.75);margin-bottom:calc(var(--gap) * 0.5)"><?php _e( 'Accessibility remains a core focus' ); ?></h3>
				<p><?php _e( 'Incorporating more than 50 accessibility improvements across the platform, WordPress 6.3 is more accessible than ever. Improved labeling, optimized tab and arrow-key navigation, revised heading hierarchy, and new controls in the admin image editor allow those using assistive technologies to navigate more easily.' ); ?></p>
			</div>
		<?php } ?>


		<hr />

		<div class="feature-section one-col">
			<div class="col">
				<h2><?php _e( 'A Stunning New Default Theme' ); ?></h2>
			</div>
		</div>

<<<<<<< main
		<hr class="is-large" />

		<div class="about__section has-3-columns">
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#151515"/>
						<path d="M15.9996 25C16.6704 25.3354 16.6703 25.3357 16.6702 25.3359L16.673 25.3305C16.6762 25.3242 16.6818 25.3135 16.6899 25.2985C16.7059 25.2686 16.7316 25.2218 16.7669 25.1608C16.8377 25.0385 16.9469 24.8592 17.0954 24.6419C17.3931 24.2062 17.8444 23.624 18.4543 23.0431C19.6731 21.8824 21.4972 20.75 23.9996 20.75C26.502 20.75 28.3261 21.8824 29.5449 23.0431C30.1549 23.624 30.6061 24.2062 30.9038 24.6419C31.0523 24.8592 31.1615 25.0385 31.2323 25.1608C31.2676 25.2218 31.2933 25.2686 31.3093 25.2985C31.3174 25.3135 31.323 25.3242 31.3262 25.3305L31.3291 25.3359C31.3289 25.3357 31.3288 25.3354 31.9996 25C32.6704 24.6646 32.6703 24.6643 32.6701 24.664L32.6688 24.6614L32.6662 24.6563L32.6583 24.6408C32.6517 24.6282 32.6427 24.6108 32.631 24.5892C32.6078 24.5459 32.5744 24.4852 32.5306 24.4096C32.4432 24.2584 32.3141 24.0471 32.1423 23.7956C31.7994 23.2938 31.2819 22.626 30.5794 21.9569C29.1731 20.6176 26.9972 19.25 23.9996 19.25C21.002 19.25 18.8261 20.6176 17.4199 21.9569C16.7174 22.626 16.1998 23.2938 15.8569 23.7956C15.6851 24.0471 15.556 24.2584 15.4686 24.4096C15.4248 24.4852 15.3914 24.5459 15.3682 24.5892C15.3566 24.6108 15.3475 24.6282 15.3409 24.6408L15.333 24.6563L15.3304 24.6614L15.3295 24.6632C15.3293 24.6635 15.3288 24.6646 15.9996 25ZM23.9996 28C25.9326 28 27.4996 26.433 27.4996 24.5C27.4996 22.567 25.9326 21 23.9996 21C22.0666 21 20.4996 22.567 20.4996 24.5C20.4996 26.433 22.0666 28 23.9996 28Z" fill="white"/>
					</svg>
				</div>
				<h3 class="is-smaller-heading" style="margin-top:calc(var(--gap) / 2);margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Preview block themes' ); ?></h3>
				<p><?php _e( 'Experience block themes before you switch and preview the Site Editor, with options to customize directly before committing to a new theme.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#151515"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M30.5 19H17.5C17.2239 19 17 19.2239 17 19.5V28.5C17 28.7761 17.2239 29 17.5 29H30.5C30.7761 29 31 28.7761 31 28.5V19.5C31 19.2239 30.7761 19 30.5 19ZM17.5 17.5H30.5C31.6046 17.5 32.5 18.3954 32.5 19.5V28.5C32.5 29.6046 31.6046 30.5 30.5 30.5H17.5C16.3954 30.5 15.5 29.6046 15.5 28.5V19.5C15.5 18.3954 16.3954 17.5 17.5 17.5ZM18.5 20.5H19.25H22V22H20V24H18.5V21.25V20.5ZM28.75 27.5H29.5V26.75V24H28V26L26 26V27.5L28.75 27.5Z" fill="white"/>
					</svg>
				</div>
				<h3 class="is-smaller-heading" style="margin-top:calc(var(--gap) / 2);margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Set aspect ratio on images' ); ?></h3>
				<p><?php _e( 'Specify your aspect ratios and ensure design integrity, especially when using images in patterns.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#151515"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M29.7499 14.9862L29.7499 14.0059L28.7939 14.279L28.7939 14.279L28.7928 14.2793L28.7928 14.2793L28.7821 14.2824L28.7506 14.2915C28.7234 14.2994 28.6837 14.3111 28.633 14.3261C28.5317 14.3562 28.386 14.4001 28.2068 14.4559C27.8488 14.5673 27.3551 14.7265 26.8128 14.9179C25.7533 15.2918 24.4209 15.8181 23.5839 16.3761C21.9787 17.4462 21.2703 18.4251 20.8568 19.1143C20.6874 19.3967 20.4992 19.8705 20.3226 20.3793C20.1388 20.9091 19.9461 21.5414 19.7753 22.1819C19.6047 22.8215 19.453 23.4805 19.3543 24.0621C19.2758 24.5242 19.2192 24.9991 19.2374 25.387L18.3036 27.7216C18.1498 28.1062 18.3368 28.5427 18.7214 28.6965C19.106 28.8503 19.5425 28.6633 19.6963 28.2787L20.3141 26.7342C20.635 26.7063 21.0572 26.6435 21.5194 26.5579C22.173 26.4369 22.9606 26.26 23.7647 26.0349C24.567 25.8102 25.3994 25.5337 26.137 25.2105C26.8594 24.8939 27.5557 24.5051 28.0303 24.0305C28.539 23.5218 28.8442 22.8139 29.0434 22.0898C29.2457 21.3547 29.36 20.5222 29.4345 19.6922C29.5006 18.9555 29.5374 18.1945 29.5718 17.4814L29.585 17.2087C29.6156 16.5862 29.6469 16.0134 29.6961 15.5127C29.7299 15.3451 29.7487 15.1792 29.7499 15.0162C29.7501 15.0062 29.7501 14.9962 29.7499 14.9862ZM21.8114 24.9706L22.8071 23.7536L27.0303 19.5305C27.386 19.1747 27.7182 18.8155 28.0194 18.4588C27.998 18.8284 27.973 19.1965 27.9405 19.5581C27.8692 20.353 27.7647 21.0831 27.5972 21.6918C27.4267 22.3114 27.211 22.7285 26.9697 22.9698C26.6943 23.2451 26.2031 23.5438 25.5349 23.8366C24.8818 24.1228 24.1205 24.3775 23.3603 24.5904C22.8227 24.7409 22.2925 24.8686 21.8114 24.9706ZM18 32.0002H26V30.5002H18V32.0002Z" fill="white"/>
					</svg>
=======
		<div class="full-width">
			<figure>
				<picture>
					<source type="image/webp" media="(max-width: 782px)" srcset="https://s.w.org/images/core/5.0/twenty%20nineteen/twenty-nineteen-mobile@1x.webp 1x, https://s.w.org/images/core/5.0/twenty%20nineteen/twenty-nineteen-mobile.webp 2x" />
					<source media="(max-width: 782px)" srcset="https://s.w.org/images/core/5.0/twenty%20nineteen/twenty-nineteen-mobile@1x.jpg 1x, https://s.w.org/images/core/5.0/twenty%20nineteen/twenty-nineteen-mobile.jpg 2x" />
					<source type="image/webp" srcset="https://s.w.org/images/core/5.0/twenty%20nineteen/twenty-nineteen@1x.webp 1x, https://s.w.org/images/core/5.0/twenty%20nineteen/twenty-nineteen.webp 2x" />
					<img src="https://s.w.org/images/core/5.0/twenty%20nineteen/twenty-nineteen@1x.jpg" srcset="https://s.w.org/images/core/5.0/twenty%20nineteen/twenty-nineteen@1x.jpg 1x, https://s.w.org/images/core/5.0/header/twenty-nineteen.jpg 2x" alt="">
				</picture>

				<figcaption><?php _e( 'The front-end of Twenty Nineteen on the left, and how it looks in the editor on the right.' ); ?></figcaption>
			</figure>
		</div>

		<div class="feature-section one-col">
			<div class="col">
				<p><?php _e( 'Introducing Twenty Nineteen, a new default theme that shows off the power of the new editor.' ); ?></p>
			</div>
		</div>

		<div class="feature-section three-col">
			<div class="col">
				<picture>
					<source type="image/webp" srcset="https://s.w.org/images/core/5.0/twenty%20nineteen/block%20editor@1x.webp 1x, https://s.w.org/images/core/5.0/twenty%20nineteen/block%20editor.webp 2x" />
					<img src="https://s.w.org/images/core/5.0/twenty%20nineteen/block%20editor@1x.jpg" srcset="https://s.w.org/images/core/5.0/twenty%20nineteen/block%20editor@1x.jpg 1x, https://s.w.org/images/core/5.0/twenty%20nineteen/block%20editor.jpg 2x" alt="">
				</picture>
				<h3><?php _e( 'Designed for the block editor' ); ?></h3>
				<p><?php _e( 'Twenty Nineteen features custom styles for the blocks available by default in 5.0. It makes extensive use of editor styles throughout the theme. That way, what you create in your content editor is what you see on the front of your site.' ); ?></p>
			</div>
			<div class="col">
				<picture>
					<source type="image/webp" srcset="https://s.w.org/images/core/5.0/twenty%20nineteen/typography@1x.webp 1x, https://s.w.org/images/core/5.0/twenty%20nineteen/typography.webp 2x" />
					<img src="https://s.w.org/images/core/5.0/twenty%20nineteen/typography@1x.jpg" srcset="https://s.w.org/images/core/5.0/twenty%20nineteen/typography@1x.jpg 1x, https://s.w.org/images/core/5.0/twenty%20nineteen/typography.jpg 2x" alt="">
				</picture>
				<h3><?php _e( 'Simple, type-driven layout' ); ?></h3>
				<p><?php _e( 'Featuring ample whitespace, and modern sans-serif headlines paired with classic serif body text, Twenty Nineteen is built to be beautiful on the go. It uses system fonts to increase loading speed. No more long waits on slow networks!' ); ?></p>
			</div>
			<div class="col">
				<img src="https://s.w.org/images/core/5.0/twenty%20nineteen/twenty-nineteen-versatile.gif" alt="">
				<h3><?php _e( 'Versatile design for all sites' ); ?></h3>
				<p><?php _e( 'Twenty Nineteen is designed to work for a wide variety of use cases. Whether you’re running a photo blog, launching a new business, or supporting a non-profit, Twenty Nineteen is flexible enough to fit your needs.' ); ?></p>
			</div>
		</div>

		<?php if ( current_user_can( 'customize' ) ) { ?>
			<div class="feature-section one-col cta">
				<div class="col">
					<a class="button button-primary button-hero load-customize hide-if-no-customize" href="<?php echo esc_url( admin_url( 'customize.php?theme=twentynineteen' ) ); ?>"><?php _e( 'Give Twenty Nineteen a try' ); ?></a>
>>>>>>> upstream/5.0-branch
				</div>
				<h3 class="is-smaller-heading" style="margin-top:calc(var(--gap) / 2);margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Build your site distraction-free' ); ?></h3>
				<p><?php _e( 'Distraction-free designing is now available in the Site Editor.' ); ?></p>
			</div>
		<?php } ?>

		<hr />

		<div class="under-the-hood feature-section">
			<div class="col">
				<h2><?php _e( 'Developer Happiness' ); ?></h2>
			</div>
		</div>

<<<<<<< main
		<div class="about__section has-3-columns">
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#151515"/>
						<path fill="#fff" fill-rule="evenodd" d="m20.014 21.168 3.988-3.418 3.988 3.418-.976 1.14-3.012-2.582-3.012 2.581-.976-1.139Z" clip-rule="evenodd"/>
						<path fill="#fff" d="M16 29h16v-1.5H16V29Z"/>
					</svg>
				</div>
				<h3 class="is-smaller-heading" style="margin-top:calc(var(--gap) / 2);margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Rediscover the Top Toolbar' ); ?></h3>
				<p><?php _e( 'A revamped top toolbar offers parent selectors for nested blocks, options when selecting multiple blocks, and a new interface embedded into the title bar with new functionality in mind.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#151515"/>
						<path d="M15 17.5H26V19H15V17.5Z" fill="white"/>
						<path d="M18.5 23H29.5V24.5H18.5V23Z" fill="white"/>
						<path d="M33 28.5H22V30H33V28.5Z" fill="white"/>
					</svg>
				</div>
				<h3 class="is-smaller-heading" style="margin-top:calc(var(--gap) / 2);margin-bottom:calc(var(--gap) / 4);"><?php _e( 'List View improvements' ); ?></h3>
				<p><?php _e( 'Drag and drop to every content layer and delete any block you would like in the updated List View.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#151515"/>
						<path d="M27.7 17.2L33.3 22.8C34 23.4 34 24.6 33.2 25.3L27.6 30.9C27.3 31.2 26.8 31.4 26.4 31.4C26 31.4 25.5 31.2 25.2 30.9L19.6 25.3C18.9 24.6 18.9 23.5 19.6 22.8L25.2 17.2C25.9 16.5 27 16.5 27.7 17.2Z" fill="white"/>
						<path d="M22 17.5L15.7 23.8C15.6 23.9 15.6 24.1 15.8 24.1L22.1 30.4L21 31.5L14.7 25.3C14 24.6 14 23.5 14.7 22.8L21 16.5L22 17.5Z" fill="white"/>
					</svg>
				</div>
				<h3 class="is-smaller-heading" style="margin-top:calc(var(--gap) / 2);margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Build templates with Patterns' ); ?></h3>
				<p><?php _e( 'Create unique patterns to jumpstart template creation with a new modal enabling access to pattern selection.' ); ?></p>
			</div>
		</div>

		<hr />

		<div class="about__section has-3-columns">
			<div class="column about__image is-vertically-aligned-top">
				<img src="<?php echo esc_url( admin_url( 'images/about-release-badge.svg?ver=6.3' ) ); ?>" alt="" height="270" width="270" />
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
						__( '<a href="%1$s">Learn WordPress</a> is a free resource for new and experienced WordPress users. Learn is stocked with how-to videos on using various features in WordPress, <a href="%2$s">interactive events</a> for exploring topics in-depth, and lesson plans for diving deep into specific areas of WordPress.' ),
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
						<rect width="48" height="48" rx="4" fill="#151515"/>
						<path d="M23 34v-4h-5l-2.293-2.293a1 1 0 0 1 0-1.414L18 24h5v-2h-7v-6h7v-2h2v2h5l2.293 2.293a1 1 0 0 1 0 1.414L30 22h-5v2h7v6h-7v4h-2Zm-5-14h11.175l.646-.646a.5.5 0 0 0 0-.708L29.175 18H18v2Zm.825 8H30v-2H18.825l-.646.646a.5.5 0 0 0 0 .708l.646.646Z" fill="#fff"/>
					</svg>
				</div>
				<p style="margin-top:calc(var(--gap) / 2);">
					<?php
					printf(
						/* translators: %s: WordPress Field Guide link. */
						__( 'Check out the latest version of the <a href="%s">WordPress Field Guide</a>. It is overflowing with detailed developer notes to help you build with WordPress.' ),
						__( 'https://make.wordpress.org/core/2023/07/18/wordpress-6-3-field-guide/' )
					);
					?>
				</p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#151515"/>
						<path d="M28 19.75h-8v1.5h8v-1.5ZM20 23h8v1.5h-8V23ZM26 26.25h-6v1.5h6v-1.5Z" fill="#fff"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M29 16H19a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V18a2 2 0 0 0-2-2Zm-10 1.5h10a.5.5 0 0 1 .5.5v12a.5.5 0 0 1-.5.5H19a.5.5 0 0 1-.5-.5V18a.5.5 0 0 1 .5-.5Z" fill="#fff"/>
					</svg>
=======
		<div class="under-the-hood feature-section three-col">
			<div class="col">
				<picture>
					<source type="image/webp" srcset="https://s.w.org/images/core/5.0/devs/Protect1x.webp 1x, https://s.w.org/images/core/5.0/devs/Protect.webp 2x" />
					<img src="https://s.w.org/images/core/5.0/devs/Protect1x.jpg" srcset="https://s.w.org/images/core/5.0/devs/Protect1x.jpg 1x, https://s.w.org/images/core/5.0/devs/Protect.jpg 2x" alt="">
				</picture>
				<h3><?php _e( 'Protect' ); ?></h3>
				<p><?php _e( 'Blocks provide a comfortable way for users to change content directly, while also ensuring the content structure cannot be easily disturbed by accidental code edits. This allows the developer to control the output, building polished and semantic markup that is preserved through edits and not easily broken.' ); ?></p>
			</div>
			<div class="col">
				<picture>
					<source type="image/webp" srcset="https://s.w.org/images/core/5.0/devs/Compose1x.webp 1x, https://s.w.org/images/core/5.0/devs/Compose.webp 2x" />
					<img src="https://s.w.org/images/core/5.0/devs/Compose1x.jpg" srcset="https://s.w.org/images/core/5.0/devs/Compose1x.jpg 1x, https://s.w.org/images/core/5.0/devs/Compose.jpg 2x" alt="">
				</picture>
				<h3><?php _e( 'Compose' ); ?></h3>
				<p><?php _e( 'Take advantage of a wide collection of APIs and interface components to easily create blocks with intuitive controls for your clients. Utilizing these components not only speeds up development work but also provide a more consistent, usable, and accessible interface to all users.' ); ?></p>
			</div>
			<div class="col">
				<picture>
					<source type="image/webp" srcset="https://s.w.org/images/core/5.0/devs/Create1x.webp 1x, https://s.w.org/images/core/5.0/devs/Create.webp 2x" />
					<img src="https://s.w.org/images/core/5.0/devs/Create1x.jpg" srcset="https://s.w.org/images/core/5.0/devs/Create1x.jpg 1x, https://s.w.org/images/core/5.0/devs/Create.jpg 2x" alt="">
				</picture>
				<h3><?php _e( 'Create' ); ?></h3>
				<p><?php _e( 'The new block paradigm opens up a path of exploration and imagination when it comes to solving user needs. With the unified block insertion flow, it&#8217;s easier for your clients and customers to find and use blocks for all types of content. Developers can focus on executing their vision and providing rich editing experiences, rather than fussing with difficult APIs.' ); ?></p>
			</div>
		</div>

		<div class="under-the-hood feature-section one-col cta">
			<div class="col">
				<a class="button button-primary button-hero" href="<?php echo esc_url( 'https://wordpress.org/gutenberg/handbook/' ); ?>"><?php _e( 'Learn how to get started' ); ?></a>
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
>>>>>>> upstream/5.0-branch
				</div>
				<p style="margin-top:calc(var(--gap) / 2);">
					<?php
					printf(
						/* translators: 1: WordPress Release Notes link, 2: WordPress version number. */
						__( '<a href="%1$s">Read the WordPress %2$s Release Notes</a> for more information on the included enhancements and issues fixed, installation information, developer notes and resources, release contributors, and the list of file changes in this release.' ),
						sprintf(
							/* translators: %s: WordPress version number. */
							esc_url( __( 'https://wordpress.org/documentation/wordpress-version/version-%s/' ) ),
							'6-3'
						),
						'6.3'
					);
					?>
				</p>
			</div>

<<<<<<< main
		<hr class="is-large" />
=======
			<hr />
		<?php endif; ?>
>>>>>>> upstream/5.0-branch

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

<<<<<<< main
<?php require_once ABSPATH . 'wp-admin/admin-footer.php'; ?>
=======
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
>>>>>>> upstream/5.0-branch

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

<<<<<<< main
=======
/* translators: %s: Codex URL */
__( 'For more information, see <a href="%s">the release notes</a>.' );

/* translators: 1: WordPress version number, 2: Link to update WordPress */
__( 'Important! Your version of WordPress (%1$s) is no longer supported, you will not receive any security updates for your website. To keep your site secure, please <a href="%2$s">update to the latest version of WordPress</a>.' );

/* translators: 1: WordPress version number, 2: Link to update WordPress */
__( 'Important! Your version of WordPress (%1$s) will stop receiving security updates in the near future. To keep your site secure, please <a href="%2$s">update to the latest version of WordPress</a>.' );

/* translators: %s: The major version of WordPress for this branch. */
__( 'This is the final release of WordPress %s' );

>>>>>>> upstream/5.0-branch
/* translators: The localized WordPress download URL. */
__( 'https://wordpress.org/download/' );
