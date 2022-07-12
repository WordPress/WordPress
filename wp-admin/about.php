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
					<?php _e( 'WordPress' ); ?>
					<span class="screen-reader-text"><?php echo $display_version; ?></span>
				</h1>
			</div>

			<div class="about__header-text"></div>

			<nav class="about__header-navigation nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Secondary menu' ); ?>">
				<a href="about.php" class="nav-tab nav-tab-active" aria-current="page"><?php _e( 'What&#8217;s New' ); ?></a>
				<a href="credits.php" class="nav-tab"><?php _e( 'Credits' ); ?></a>
				<a href="freedoms.php" class="nav-tab"><?php _e( 'Freedoms' ); ?></a>
				<a href="privacy.php" class="nav-tab"><?php _e( 'Privacy' ); ?></a>
			</nav>
		</div>

		<div class="about__section changelog">
			<div class="column">
				<h2><?php _e( 'Maintenance and Security Releases' ); ?></h2>
				<p>
					<?php
					printf(
						/* translators: 1: WordPress version number, 2: Plural number of bugs. */
						_n(
							'<strong>Version %1$s</strong> addressed %2$s bug.',
							'<strong>Version %1$s</strong> addressed %2$s bugs.',
							31
						),
						'6.0.1',
						number_format_i18n( 31 )
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL. */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version. */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '6.0.1' )
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
					<?php _e( 'WordPress 6.0 includes more than 500 enhancements and 400 bug fixes. This page highlights several key advancements aimed at making your WordPress content-creating and site-building experience more feature-rich and intuitive. You will also find resources for developers and anyone wanting a deeper understanding of WordPress.' ); ?>
				</p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column about__image is-vertically-aligned-top">
				<img src="https://s.w.org/images/core/6.0/about-60-writing-improvements.png" alt="" />
			</div>
			<div class="column">
				<h3>
					<?php _e( 'Enhanced Writing Experience' ); ?>
				</h3>
				<p>
					<?php _e( 'Writing improvements abound, whether you&#8217;re writing a brand new post or adding elements to an existing page. Explore more ways to streamline your content creation process, including:' ); ?>
				</p>
				<ul>
					<li><?php _e( 'Select text across multiple blocks and edit it all at once.' ); ?></li>
					<li><?php _e( 'Type two open brackets <code>[[</code> to quickly access the link menu.' ); ?></li>
					<li><?php _e( 'Keep existing styles when you transform some blocks from one kind to another—from a Paragraph block to a Code block, for instance.' ); ?></li>
					<li><?php _e( 'Create customized buttons and any new buttons you make will retain the style customizations automatically.' ); ?></li>
					<li><?php _e( 'Make tag clouds and social icons even more appealing with updated settings and controls, and a new outline style for the tag cloud.' ); ?></li>
				</ul>
			</div>
		</div>

		<div class="about__section has-2-columns is-wider-right">
			<div class="column">
				<h3>
					<?php _e( 'Style Switching' ); ?>
				</h3>
				<p>
					<?php _e( 'Block themes now include the option to contain multiple style variations. This expands the new Style system even further and enables shortcuts to switch the look and feel of your site all within a single theme. You can change both the available settings, like the font weight, and the style options, like the default color palette. Change the look and feel of your site with just a few clicks.' ); ?>
				</p>
			</div>
			<div class="column about__image is-vertically-aligned-top">
				<img src="https://s.w.org/images/core/6.0/about-60-style-switching.gif" alt="" />
			</div>
		</div>

		<div class="about__section has-1-column">
			<div class="column about__image is-vertically-aligned-top">
				<img src="https://s.w.org/images/core/6.0/about-60-templates.png" alt="" />
			</div>
			<div class="column" style="padding-bottom:0">
				<h3>
					<?php _e( 'More Template Choices' ); ?>
				</h3>
				<p>
					<?php _e( 'WordPress 6.0 includes five new template options: author, date, categories, tag, and taxonomy. These additional templates provide greater flexibility for content creators. Tailor each with the tools you already know or with the following new options in this release.' ); ?>
				</p>
			</div>
		</div>

		<div class="about__section has-3-columns">
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.0/about-60-sub-feature-1.png" alt="" />
				</div>
				<p><?php _e( 'Featured images can be used in the cover block.' ); ?></p>
				<p><?php _e( 'New featured image sizing controls make it easier to get the results you want.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.0/about-60-sub-feature-2.png" alt="" />
				</div>
				<p><?php _e( 'While editing a template, at the root, or between blocks, the quick inserter shows you patterns and template parts to help you work faster and discover new layout options.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.0/about-60-sub-feature-3.png" alt="" />
				</div>
				<p><?php _e( 'The query block supports filtering on multiple authors, support for custom taxonomies, and support for customizing what is shown when there are no results.' ); ?></p>
			</div>
		</div>

		<div class="about__section has-2-columns is-wider-left">
			<div class="column about__image is-vertically-aligned-top">
				<img src="https://s.w.org/images/core/6.0/about-60-integrated-patterns.png" alt="" />
			</div>
			<div class="column">
				<h3>
					<?php _e( 'Integrated Patterns' ); ?>
				</h3>
				<p>
					<?php
					printf(
						/* translators: %s Working with Patterns dev note link. */
						__( 'Patterns will now appear when you need them in even more places, like in the quick inserter or when creating a new header or footer. If you&#8217;re a block theme author, you can even <a href="%s">register patterns from the Pattern Directory using <code>theme.json</code></a>, enabling you to prioritize specific patterns that are most helpful to your theme&#8217;s users.' ),
						'https://make.wordpress.org/core/2022/05/02/new-features-for-working-with-patterns-and-themes-in-wordpress-6-0/'
					);
					?>
				</p>
			</div>
		</div>

		<div class="about__section has-2-columns is-wider-right">
			<div class="column">
				<h3>
					<?php _e( 'Better List View' ); ?>
				</h3>
				<p>
					<?php _e( 'New keyboard shortcuts enable you to select multiple blocks from the list view, modify them in bulk, and drag-and-drop them within the list. List View can be opened and closed easily; it comes collapsed by default and it automatically expands to the current selection whenever you select a block.' ); ?>
				</p>
			</div>
			<div class="column about__image is-vertically-aligned-top">
				<img src="https://s.w.org/images/core/6.0/about-60-list-view.png" alt="" />
			</div>
		</div>

		<div class="about__section has-2-columns is-wider-left">
			<div class="column about__image is-vertically-aligned-top">
				<img src="https://s.w.org/images/core/6.0/about-60-block-locking-controls.png" alt="" />
			</div>
			<div class="column">
				<h3>
					<?php _e( 'Block Locking Controls' ); ?>
				</h3>
				<p>
					<?php _e( 'Now you can lock your blocks. Choose to disable the option to move a block, remove a block, or both. This simplifies project handover, allowing your clients to unleash their creativity without worrying about accidentally breaking their site in the process.' ); ?>
				</p>
			</div>
		</div>

		<hr class="is-large" />

		<div class="about__section" style="margin-bottom:0;">
			<div class="column">
				<h2 class="aligncenter">
					<?php _e( 'Additional Design Tools' ); ?>
				</h2>
				<p class="is-subheading aligncenter">
					<?php _e( 'Design tools grow more powerful and intuitive with each release.' ); ?><br />
					<?php _e( 'Some highlights for 6.0 include:' ); ?>
				</p>
			</div>
		</div>

		<div class="about__section has-3-columns" style="margin-bottom:0;">
			<div class="column">
				<div class="about__image aligncenter">
					<svg width="33" height="32" viewBox="0 0 33 32" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" clip-rule="evenodd" d="M15.203 6.892c.776-.963 1.297-1.559 1.297-1.559s.521.596 1.297 1.559c2.266 2.81 6.703 8.75 6.703 12.155 0 4.572-3.18 7.62-8 7.62s-8-3.048-8-7.62c0-3.404 4.437-9.345 6.703-12.155Zm1.297 1.58a64.727 64.727 0 0 0-2.361 3.15c-.972 1.388-1.911 2.87-2.6 4.248-.72 1.44-1.039 2.52-1.039 3.177 0 1.805.616 3.164 1.573 4.077.965.921 2.441 1.542 4.427 1.542 1.986 0 3.462-.621 4.427-1.542.957-.913 1.573-2.272 1.573-4.077 0-.657-.32-1.738-1.039-3.177-.689-1.378-1.628-2.86-2.6-4.247A64.727 64.727 0 0 0 16.5 8.47Z" fill="#1E1E1E"/>
					</svg>
				</div>
				<p><?php _e( 'A new color panel design saves space, but still shows your options at a glance.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image aligncenter">
					<svg width="33" height="32" viewBox="0 0 33 32" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M4.5 9.333v13.334h2.667V9.333H4.5ZM9.833 6.667V4h13.334v2.667H9.833ZM25.833 9.333v13.334H28.5V9.333h-2.667ZM23.167 28v-2.667H9.833V28h13.334Z" fill="#1E1E1E"/>
					</svg>
				</div>
				<p><?php _e( 'New border controls offer a simpler way to set your border exactly as you like it.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image aligncenter">
					<svg width="41" height="40" viewBox="0 0 41 40" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
						<circle cx="20.5" cy="20" r="12" fill="#fff"/>
						<circle cx="20.5" cy="20" r="12" fill="url(#a)"/>
						<circle cx="20.5" cy="20" r="12" stroke="#1E1E1E" stroke-width="2"/>
						<defs>
							<pattern id="a" patternContentUnits="objectBoundingBox" width=".385" height=".385">
								<use xlink:href="#b" transform="scale(.01923)"/>
							</pattern>
							<image id="b" width="20" height="20" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAAfUlEQVQ4Ee2TSw6AMAhES4/C/a/EWTAuptFJR4m6MFE25dO88ikWEdkmkpnNzEbE3Yd+pHQV3MLUnZlfAmeXK74vAtdpPin96jRVEu8fimWxiRGx2xwuGZtVLvms14iXgZyRsn+g6oz28ye53UNMF0+WgZwJAHyWgZwJg2AvIbMZwqZwPC4AAAAASUVORK5CYII="/>
						</defs>
					</svg>
				</div>
				<p><?php _e( 'Transparency levels for your colors allow for even more creative color options.' ); ?></p>
			</div>
		</div>

		<div class="about__section has-3-columns">
			<div class="column">
				<div class="about__image aligncenter">
					<svg width="33" height="32" viewBox="0 0 33 32" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" clip-rule="evenodd" d="M15.165 7.335h9.333c.369 0 .667.298.667.667v9.333a.667.667 0 0 1-.667.667h-4v-3.333a2.667 2.667 0 0 0-2.666-2.667h-3.334v-4c0-.369.299-.667.667-.667Zm-2.667 4.667v-4a2.667 2.667 0 0 1 2.667-2.667h9.333a2.667 2.667 0 0 1 2.667 2.667v9.333a2.667 2.667 0 0 1-2.667 2.667h-4v4a2.667 2.667 0 0 1-2.666 2.667H8.499a2.667 2.667 0 0 1-2.667-2.667V14.67a2.667 2.667 0 0 1 2.667-2.667h4Zm6 8v4a.667.667 0 0 1-.666.667H8.499a.667.667 0 0 1-.667-.667V14.67c0-.368.299-.667.667-.667h4v3.333a2.667 2.667 0 0 0 2.666 2.667H18.5Zm0-2h-3.333a.667.667 0 0 1-.667-.667v-3.333h3.334c.368 0 .667.299.667.667v3.333Z" fill="#1E1E1E"/>
					</svg>
				</div>
				<p><?php _e( 'Control gaps, margins, typography, and more on a collection of blocks, all at once, in the Group block.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image aligncenter">
					<svg width="33" height="32" viewBox="0 0 33 32" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M12.833 8.667h-7v2h7c.368 0 .667.299.667.667v9.333a.667.667 0 0 1-.667.667h-7v2h7a2.667 2.667 0 0 0 2.667-2.667v-9.333a2.667 2.667 0 0 0-2.667-2.667ZM20.167 8.667h7v2h-7a.667.667 0 0 0-.667.667v9.333c0 .368.299.667.667.667h7v2h-7a2.667 2.667 0 0 1-2.667-2.667v-9.333a2.667 2.667 0 0 1 2.667-2.667Z" fill="#1E1E1E"/>
					</svg>
				</div>
				<p><?php _e( 'Switch between stack, row, and group variations to position groups of blocks with more layout flexibility.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image aligncenter">
					<svg width="33" height="32" viewBox="0 0 33 32" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M28.5 10.667v14.667A2.665 2.665 0 0 1 25.835 28H8.5" stroke="#1E1E1E" stroke-width="2"/>
						<rect x="5.5" y="5" width="18" height="18" rx="1.167" stroke="#1E1E1E" stroke-width="2"/>
						<path d="M5.834 18.667 10.786 16l3.715 1.778 4.333-3.111 4.333 3.111" stroke="#1E1E1E" stroke-width="2" stroke-linejoin="round"/>
					</svg>
				</div>
				<p><?php _e( 'Use the gap support functionality in the Gallery block to create different looks–from adding spacing between all images, to removing spacing altogether.' ); ?></p>
			</div>
		</div>

		<hr class="is-large" />

		<div class="about__section has-2-columns is-wider-right">
			<div class="column about__image is-vertically-aligned-top">
				<a href="https://www.youtube.com/watch?v=oe452WcY7fA">
					<img src="https://s.w.org/images/core/6.0/about-60-video.png?ver=6.0" alt="<?php echo esc_attr( __( 'Exploring WordPress 6.0 video' ) ); ?>" />
				</a>
			</div>
			<div class="column">
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
						/* translators: %s: 6.0 overview video link. */
						__( 'See WordPress 6.0 in action! <a href="%s">Watch a brief overview video</a> highlighting some of the major features debuting in WordPress 6.0.' ),
						'https://www.youtube.com/watch?v=oe452WcY7fA'
					);
					?>
				</p>
			</div>
		</div>

		<div class="about__section has-3-columns">
			<div class="column" style="padding-top:0">
				<p>
					<?php
					printf(
						/* translators: 1: Learn WordPress workshops link, 2: Learn WordPress social learning link. */
						__( 'Explore <a href="%1$s">learn.wordpress.org/&#8203;workshops</a> for quick how-to videos and lots more on new features in WordPress. Or join a live <a href="%2$s">interactive online learning session</a> on a specific WordPress topic.' ),
						'https://learn.wordpress.org/workshops/',
						'https://learn.wordpress.org/social-learning/'
					);
					?>
				</p>
			</div>
			<div class="column" style="padding-top:0">
				<p>
					<?php
					printf(
						/* translators: %s: WordPress 6.0 Field Guide link. */
						__( 'Check out the latest version of the <a href="%s">WordPress Field Guide</a>. It is overflowing with detailed developer notes to help you build with WordPress.' ),
						__( 'https://make.wordpress.org/core/2022/05/03/wordpress-6-0-field-guide/' )
					);
					?>
				</p>
			</div>
			<div class="column" style="padding-top:0">
				<p>
					<?php
					printf(
						/* translators: %s: WordPress 6.0 Release Notes link. */
						__( '<a href="%s">Read the WordPress 6.0 Release Notes</a> for more information on the included enhancements and issues fixed, installation information, developer notes and resources, release contributors, and the list of file changes in this release.' ),
						sprintf(
							/* translators: %s: WordPress version. */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							'6-0'
						)
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
