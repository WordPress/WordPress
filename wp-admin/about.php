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
			<div class="about__header-text">
				<?php _e( 'Speed. Search. Security.' ); ?>
			</div>

			<div class="about__header-title">
				<p>
					<?php _e( 'WordPress' ); ?>
					<span><?php echo $display_version; ?></span>
				</p>
			</div>

			<nav class="about__header-navigation nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Secondary menu' ); ?>">
				<a href="about.php" class="nav-tab nav-tab-active" aria-current="page"><?php _e( 'What&#8217;s New' ); ?></a>
				<a href="credits.php" class="nav-tab"><?php _e( 'Credits' ); ?></a>
				<a href="freedoms.php" class="nav-tab"><?php _e( 'Freedoms' ); ?></a>
				<a href="privacy.php" class="nav-tab"><?php _e( 'Privacy' ); ?></a>
			</nav>
		</div>

		<div class="about__section is-feature has-subtle-background-color">
			<h1>
				<?php
				printf(
					/* translators: %s: The current WordPress version number. */
					__( 'Welcome to WordPress %s.' ),
					$display_version
				);
				?>
			</h1>
			<p>
				<?php
				printf(
					/* translators: %s: The current WordPress version number. */
					__( 'In WordPress %s, your site gets new power in three major areas: speed, search, and security.' ),
					$display_version
				);
				?>
			</p>
		</div>

		<hr />

		<div class="about__section has-subtle-background-color">
			<div class="column">
				<h2><?php _e( 'Maintenance and Security Releases' ); ?></h2>
				<p>
					<?php
					printf(
						/* translators: %s: WordPress version number */
						__( '<strong>Version %s</strong> addressed one security issue.' ),
						'5.5.5'
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '5.5.5' )
						)
					);
					?>
				</p>
				<p>
					<?php
					printf(
						/* translators: %s: WordPress version number */
						__( '<strong>Version %s</strong> addressed some security issues.' ),
						'5.5.4'
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '5.5.4' )
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
							1
						),
						'5.5.3',
						number_format_i18n( 1 )
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL. */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version. */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '5.5.3' )
						)
					);
					?>
				</p>
				<p>
					<?php
					printf(
						/* translators: 1: WordPress version number, 2: plural number of bugs. More than one security issue. */
						_n(
							'<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
							'<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.',
							14
						),
						'5.5.2',
						number_format_i18n( 14 )
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '5.5.2' )
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
							44
						),
						'5.5.1',
						number_format_i18n( 44 )
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL. */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version. */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '5.5.1' )
						)
					);
					?>
				</p>
			</div>
		</div>

		<hr />

		<div class="about__section has-1-column">
			<div class="column">
				<h2><?php _e( 'Speed' ); ?></h2>
				<p><strong><?php _e( 'Posts and pages feel faster, thanks to lazy-loaded images.' ); ?></strong></p>
				<p><?php _e( 'Images give your story a lot of impact, but they can sometimes make your site seem slow.' ); ?></p>
				<p><?php _e( 'In WordPress 5.5, images wait to load until they’re just about to scroll into view. The technical term is ‘lazy loading’.' ); ?></p>
				<p><?php _e( 'On mobile, lazy loading can also keep browsers from loading files meant for other devices. That can save your readers money on data — and help preserve battery life.' ); ?></p>
			</div>
		</div>

		<div class="about__section has-1-column">
			<div class="column">
				<h2><?php _ex( 'Search', 'sitemap' ); ?></h2>
				<p><strong><?php _e( 'Say hello to your new sitemap.' ); ?></strong></p>
				<p><?php _e( 'WordPress sites work well with search engines.' ); ?></p>
				<p><?php _e( 'Now, by default, WordPress 5.5 includes an XML sitemap that helps search engines discover your most important pages from the very minute you go live.' ); ?></p>
				<p><?php _e( 'So more people will find your site sooner, giving you more time to engage, retain and convert them to subscribers, customers or whatever fits your definition of success.' ); ?></p>
			</div>
		</div>

		<hr />

		<div class="about__section has-2-columns has-accent-background-color is-wider-right">
			<div class="column">
				<h2><?php _e( 'Security' ); ?></h2>
				<p><strong><?php _e( 'Auto-updates for Plugins and Themes' ); ?></strong></p>
				<p><?php _e( 'Now you can set plugins and themes to update automatically — or not! — in the WordPress admin. So you always know your site is running the latest code available.' ); ?></p>
				<p><?php _e( 'You can also turn auto-updates on or off for each plugin or theme you have installed — all on the same screens you’ve always used.' ); ?></p>
				<p><strong><?php _e( 'Update by uploading ZIP files' ); ?></strong></p>
				<p><?php _e( 'If updating plugins and themes manually is your thing, now that’s easier too — just upload a ZIP file.' ); ?></p>
			</div>
			<div class="column about__image is-vertically-aligned-center">
				<figure aria-labelledby="about-security" class="about__image">
					<video controls poster="https://s.w.org/images/core/5.5/auto-updates-poster.png">
						<source src="https://s.w.org/images/core/5.5/auto-updates.mp4" type="video/mp4" />
						<source src="https://s.w.org/images/core/5.5/auto-updates.webm" type="video/webm" />
					</video>
					<figcaption id="about-security" class="screen-reader-text"><?php _e( 'Video: Installed plugin screen, which shows a new column, Automatic Updates. In this column are buttons that say "Enable auto-updates". When clicked, the auto-updates feature is turned on for that plugin, and the button switches to say "Disable auto-updates".' ); ?></figcaption>
				</figure>
			</div>
		</div>

		<hr />

		<div class="about__section has-subtle-background-color">
			<div class="column">
				<h2><?php _e( 'Highlights from the block editor' ); ?></h2>
				<p><?php _e( 'Once again, the latest WordPress release packs a long list of exciting new features for the block editor. For example:' ); ?></p>
			</div>
		</div>
		<div class="about__section has-2-columns has-subtle-background-color">
			<div class="column about__image is-vertically-aligned-center">
				<figure aria-labelledby="about-block-pattern" class="about__image">
					<video controls poster="https://s.w.org/images/core/5.5/block-patterns-poster.png">
						<source src="https://s.w.org/images/core/5.5/block-patterns.mp4" type="video/mp4" />
						<source src="https://s.w.org/images/core/5.5/block-patterns.webm" type="video/webm" />
					</video>
					<figcaption id="about-block-pattern" class="screen-reader-text"><?php _e( 'Video: In the editor, the block inserter shows two tabs, Blocks and Patterns. The Patterns tab is selected. There are different block layouts in this tab. After scrolling through options including buttons and columns, a pattern called "Large header with a heading" is chosen. This adds a cover block, which is customized with a photo and the name of the WordPress 5.5 jazz musician.' ); ?></figcaption>
				</figure>
				<hr />
				<figure aria-labelledby="about-image-editor" class="about__image">
					<video controls poster="https://s.w.org/images/core/5.5/inline-image-editing-poster.png">
						<source src="https://s.w.org/images/core/5.5/inline-image-editing.mp4" type="video/mp4" />
						<source src="https://s.w.org/images/core/5.5/inline-image-editing-1.webm" type="video/webm" />
					</video>
					<figcaption id="about-image-editor" class="screen-reader-text"><?php _e( 'Video: An image is added with an image block. In the block toolbar, an icon called "Crop" is selected, which changes the toolbar to show image resizing tools. First, zoom is used to zoom into the center of the image. Next, aspect ratio is clicked. This shows a dropdown of common aspect ratios. Square is chosen, and the image is moved within the new square outline. The crop is completed by clicking "Apply."' ); ?></figcaption>
				</figure>
			</div>
			<div class="column">
				<h3><?php _e( 'Block patterns' ); ?></h3>
				<p><?php _e( 'New block patterns make it simple and fun to create complex, beautiful layouts, using combinations of text and media that you can mix and match to fit your story.' ); ?></p>
				<p><?php _e( 'You will also find block patterns in a wide variety of plugins and themes, with more added all the time. Pick any of them from a single place — just click and go!' ); ?></p>
				<h3><?php _e( 'Inline image editing' ); ?></h3>
				<p><?php _e( 'Crop, rotate, and zoom your photos right from the image block. If you spend a lot of time on images, this could save you hours!' ); ?></p>

				<h3><?php _e( 'The New Block Directory' ); ?></h3>
				<p><?php _e( 'Now it’s easier than ever to find the block you need. The new block directory is built right into the block editor, so you can install new block types to your site without ever leaving the editor.' ); ?></p>

				<h3><?php _e( 'And so much more.' ); ?></h3>
				<p><?php _e( 'The highlights above are a tiny fraction of the new block editor features you’ve just installed. Open the block editor and enjoy!' ); ?></p>
			</div>
		</div>

		<hr />

		<div class="about__section has-1-column">
			<div class="column">
				<h2><?php _e( 'Accessibility' ); ?></h2>
				<p><?php _e( 'Every release adds improvements to the accessible publishing experience, and that remains true for WordPress 5.5.' ); ?></p>
				<p><?php _e( 'Now you can copy links in media screens and modal dialogs with a button, instead of trying to highlight a line of text.' ); ?></p>
				<p><?php _e( 'You can also move meta boxes with the keyboard, and edit images in WordPress with your assistive device, as it can read you the instructions in the image editor.' ); ?></p>
			</div>
		</div>

		<hr />

		<div class="about__section has-subtle-background-color has-2-columns">
			<header class="is-section-header">
				<h2><?php _e( 'For developers' ); ?></h2>
				<p><?php _e( '5.5 also brings a big box of changes just for developers.' ); ?></p>
			</header>
			<div class="column">
				<h3><?php _e( 'Server-side registered blocks in the REST API' ); ?></h3>
				<p><?php _e( 'The addition of block types endpoints means that JavaScript apps (like the block editor) can retrieve definitions for any blocks registered on the server.' ); ?></p>
			</div>
			<div class="column">
				<h3><?php _e( 'Dashicons' ); ?></h3>
				<p><?php _e( 'The Dashicons library has received its final update in 5.5. It adds 39 block editor icons along with 26 others.' ); ?></p>
			</div>
		</div>

		<div class="about__section has-subtle-background-color has-2-columns">
			<div class="column">
				<h3><?php _e( 'Defining environments' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: 'wp_get_environment_type' function name. */
						__( 'WordPress now has a standardized way to define a site’s environment type (staging, production, etc). Retrieve that type with %s and execute only the appropriate code.' ),
						'<code>wp_get_environment_type()</code>'
					);
					?>
				</p>
			</div>
			<div class="column">
				<h3><?php _e( 'Passing data to template files' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %1$s: 'get_header' function name, %2$s: 'get_template_part' function name, %3$s: '$args' variable name. */
						__( 'The template loading functions (%1$s, %2$s, etc.) have a new %3$s argument. So now you can pass an entire array’s worth of data to those templates.' ),
						'<code>get_header()</code>',
						'<code>get_template_part()</code>',
						'<code>$args</code>'
					);
					?>
				</p>
			</div>
		</div>

		<div class="about__section has-subtle-background-color">
			<div class="column">
				<h3><?php _e( 'More changes for developers' ); ?></h3>
				<ul>
					<li><?php _e( 'The PHPMailer library just got a major update, going from version 5.2.27 to 6.1.6.' ); ?></li>
					<li>
						<?php
						printf(
							/* translators: %s: 'redirect_guess_404_permalink' function name. */
							__( 'Now get more fine-grained control of %s.' ),
							'<code>redirect_guess_404_permalink()</code>'
						);
						?>
					</li>
					<li>
						<?php
						printf(
							/* translators: %s: 'wp_opcache_invalidate' function name. */
							__( 'Sites that use PHP’s OPcache will see more reliable cache invalidation, thanks to the new %s function during updates (including to plugins and themes).' ),
							'<code>wp_opcache_invalidate()</code>'
						);
						?>
					</li>
					<li><?php _e( 'Custom post types associated with the category taxonomy can now opt-in to supporting the default term.' ); ?></li>
					<li>
						<?php
						printf(
							/* translators: %s: 'register_taxonomy' function name. */
							__( 'Default terms can now be specified for custom taxonomies in %s.' ),
							'<code>register_taxonomy()</code>'
						);
						?>
					</li>
					<li>
						<?php
						printf(
							/* translators: %s: 'register_meta' function name. */
							__( 'The REST API now officially supports specifying default metadata values through %s.' ),
							'<code>register_meta()</code>'
						);
						?>
					</li>
					<li><?php _e( 'You will find updated versions of these bundled libraries: SimplePie, Twemoji, Masonry, imagesLoaded, getID3, Moment.js, and clipboard.js.' ); ?></li>
				</ul>
			</div>
		</div>

		<hr class="is-small" />

		<div class="about__section">
			<div class="column">
				<h3><?php _e( 'Check the Field Guide for more!' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: WordPress 5.5 Field Guide link. */
						__( 'There’s a lot more for developers to love in WordPress 5.5. To discover more and learn how to make these changes shine on your sites, themes, plugins and more, check the <a href="%s">WordPress 5.5 Field Guide.</a>' ),
						'https://make.wordpress.org/core/wordpress-5-5-field-guide/'
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

require_once ABSPATH . 'wp-admin/admin-footer.php';

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
