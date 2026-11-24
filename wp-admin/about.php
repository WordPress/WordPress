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

list( $display_version ) = explode( '-', wp_get_wp_version() );
$display_major_version   = '7.2';

$release_notes_url = sprintf(
	/* translators: %s: WordPress version number. */
	__( 'https://wordpress.org/documentation/wordpress-version/version-%s/' ),
	sanitize_title( $display_major_version )
);

$field_guide_url = sprintf(
	/* translators: %s: WordPress version number. */
	__( 'https://make.wordpress.org/core/wordpress-%s-field-guide/' ),
	sanitize_title( $display_major_version )
);

$release_page_url = sprintf(
	/* translators: %s: WordPress version number. */
	__( 'https://wordpress.org/download/releases/%s/' ),
	sanitize_title( $display_major_version )
);

$version_text = sprintf(
	/* translators: %s: Version number. */
	__( 'WordPress %s' ),
	$display_version
);

require_once ABSPATH . 'wp-admin/admin-header.php';
?>
	<div class="wrap about__container">

		<div class="about__header">
			<div class="about__header-title">
				<h1><?php echo esc_html( $version_text ); ?></h1>
			</div>
		</div>

		<nav class="about__header-navigation nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Secondary menu' ); ?>">
			<a href="about.php" class="nav-tab nav-tab-active" aria-current="page"><?php _e( 'What&#8217;s New' ); ?></a>
			<a href="credits.php" class="nav-tab"><?php _e( 'Credits' ); ?></a>
			<a href="freedoms.php" class="nav-tab"><?php _e( 'Freedoms' ); ?></a>
			<a href="privacy.php" class="nav-tab"><?php _e( 'Privacy' ); ?></a>
			<a href="contribute.php" class="nav-tab"><?php _e( 'Get Involved' ); ?></a>
		</nav>

		<div class="about__section">
			<div class="column is-left-padding-zero is-right-padding-zero">
				<h2>
					<?php
					printf(
						/* translators: %s: Version number. */
						__( 'Welcome to WordPress %s' ),
						$display_major_version
					);
					?>
				</h2>
				<p class="is-subheading"><?php _e( 'WordPress 7.2 gives your words a fresh place to start. Ipsum, the new default theme, is a minimal blank canvas for blogging that&#8217;s ready to make your own. Text shadows and form styling in Global Styles add new ways to shape how your site looks, all without writing custom CSS. A new /random/ address invites readers to rediscover your archive, Notes now support emoji reactions, and refreshed icons in the admin bring more visual consistency across WordPress.' ); ?></p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column is-vertically-aligned-center is-left-padding-zero">
				<h3><?php _e( 'Introducing Ipsum, the next default theme' ); ?></h3>
				<p>
					<strong><?php _e( 'A blank canvas, until real words arrive.' ); ?></strong><br />
					<?php _e( 'WordPress 7.2 introduces Ipsum, a minimal default theme built around blogging so your writing is the main focus. It starts simple on purpose, with various patterns to match how you want your content to shine and style variations that change the whole look in a click.' ); ?>
				</p>
			</div>
			<div class="column is-vertically-aligned-center is-right-padding-zero">
				<div class="about__image">
					<img src="https://s.w.org/images/core/7.2/about-feature-01.webp?ver=20261123" alt="" height="900" width="900" />
				</div>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column is-vertically-aligned-center is-left-padding-zero">
				<div class="about__image">
					<img loading="lazy" src="https://s.w.org/images/core/7.2/about-feature-02.webp?ver=20261123" alt="" height="900" width="900" />
				</div>
			</div>
			<div class="column is-vertically-aligned-center is-right-padding-zero">
				<h3><?php _e( 'Discover posts differently' ); ?></h3>
				<p>
					<strong><?php _e( 'One link, a different post every time.' ); ?></strong><br />
					<?php _e( 'WordPress 7.2 adds a /random/ address to every site. Visit it and you land on a randomly chosen post, giving readers a playful way to explore your archive and rediscover writing that deserves a second look.' ); ?>
				</p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column is-vertically-aligned-center is-left-padding-zero">
				<h3><?php _e( 'React on notes' ); ?></h3>
				<p>
					<strong><?php _e( 'Say it with an emoji.' ); ?></strong><br />
					<?php _e( 'Emoji reactions are now available in Notes. Acknowledge feedback, agree with a note, or celebrate a finished draft without writing a reply. Hover a reaction to see who left it, or click it to add your own.' ); ?>
				</p>
			</div>
			<div class="column is-vertically-aligned-center is-right-padding-zero">
				<div class="about__image">
					<img loading="lazy" src="https://s.w.org/images/core/7.2/about-feature-03.webp?ver=20261123" alt="" height="900" width="900" />
				</div>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column is-vertically-aligned-center is-left-padding-zero">
				<div class="about__image">
					<img loading="lazy" src="https://s.w.org/images/core/7.2/about-feature-04.webp?ver=20261123" alt="" height="900" width="900" />
				</div>
			</div>
			<div class="column is-vertically-aligned-center is-right-padding-zero">
				<h3><?php _e( 'Modern icons in admin' ); ?></h3>
				<p>
					<strong><?php _e( 'One icon set, everywhere.' ); ?></strong><br />
					<?php _e( 'WordPress 7.2 replaces the legacy Dashicons in the admin bar and sidebar with updated icons from the WordPress icon library. The dashboard now matches the icons you see in the editor, offering a more consistent, performant, and accessible experience.' ); ?>
				</p>
			</div>
		</div>

		<hr class="is-invisible is-large" />

		<div class="about__section has-2-columns">
			<div class="column is-left-padding-zero">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<path fill="#1e1e1e" d="M32.455 17.72a1.592 1.592 0 0 1 .599 2.195l-7.637 12.99a1.653 1.653 0 0 1-2.235.589 1.592 1.592 0 0 1-.599-2.195l7.637-12.99a1.653 1.653 0 0 1 2.235-.589ZM13.774 23.21a1.653 1.653 0 0 0-2.236.589 1.592 1.592 0 0 0 .6 2.195l.944.536c.783.444 1.783.18 2.235-.588a1.592 1.592 0 0 0-.599-2.196l-.944-.535ZM16.432 17.72a1.653 1.653 0 0 1 2.236.588l.545.928a1.592 1.592 0 0 1-.599 2.196 1.653 1.653 0 0 1-2.235-.588l-.546-.928a1.592 1.592 0 0 1 .6-2.196ZM25.637 16.5c0-.888-.733-1.607-1.637-1.607s-1.636.72-1.636 1.607v1.071c0 .888.732 1.608 1.636 1.608.904 0 1.637-.72 1.637-1.608V16.5Z"/>
						<path fill="#1e1e1e" fill-rule="evenodd" d="M4.91 27.75C4.91 17.395 13.455 9 24 9s19.091 8.395 19.091 18.75c0 3.909-1.22 7.542-3.305 10.548l-.488.702H8.702l-.488-.702A18.438 18.438 0 0 1 4.91 27.75ZM24 12.214c-8.736 0-15.818 6.956-15.818 15.536 0 2.943.832 5.692 2.277 8.036h27.082a15.25 15.25 0 0 0 2.277-8.036c0-8.58-7.082-15.536-15.818-15.536Z" clip-rule="evenodd"/>
					</svg>
				</div>
				<h3><?php _e( 'Performance' ); ?></h3>
				<p><?php _e( 'The admin feels faster in WordPress 7.2. Scripts and stylesheets now load individually instead of in large combined files, so browsers cache them better, and WordPress prefetches the screen you&#8217;re likely to open next. Image resizing, compression, and conversion in the browser now extend to the Media Library, easing the load on your server during uploads.' ); ?></p>
			</div>
			<div class="column is-right-padding-zero">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<path fill="#1e1e1e" d="M24 13.84c-.752 0-1.397-.287-1.936-.86a2.902 2.902 0 0 1-.809-2.06c0-.8.27-1.487.809-2.06S23.248 8 24 8c.753 0 1.398.287 1.937.86.54.573.809 1.26.809 2.06s-.27 1.487-.809 2.06-1.184.86-1.937.86ZM19.976 40V18.68a69.562 69.562 0 0 1-4.945-.56 45.877 45.877 0 0 1-4.57-.92l.565-2.4a46.79 46.79 0 0 0 6.356 1.14c2.106.227 4.312.34 6.618.34 2.307 0 4.513-.113 6.62-.34a46.786 46.786 0 0 0 6.355-1.14l.564 2.4c-1.454.373-2.977.68-4.57.92a69.55 69.55 0 0 1-4.945.56V40h-2.256V29.6h-3.535V40h-2.257Z"/>
					</svg>
				</div>
				<h3><?php _e( 'Accessibility' ); ?></h3>
				<p><?php _e( 'In this release, high impact changes include clearer keyboard focus in Windows High Contrast mode, improved keyboard navigation in the admin toolbar, and an option to turn off metabox rearranging to prevent accidental changes on touch devices. Ipsum ships with clear focus indicators, descriptive screen reader links, and transitions that respect reduced-motion preferences.' ); ?></p>
			</div>
		</div>

		<hr class="is-invisible is-large" style="margin-bottom:calc(2 * var(--gap));" />

		<div class="about__section has-2-columns is-wider-left has-subtle-background-color is-feature">
			<h3 class="is-section-header"><?php _e( 'And much more' ); ?></h3>
			<div class="column">
				<p>
					<?php
					printf(
						/* translators: %s: Version number. */
						__( 'For a comprehensive overview of all the new features and enhancements in WordPress %s, please visit the feature-showcase website.' ),
						$display_major_version
					);
					?>
				</p>
			</div>
			<div class="column aligncenter">
				<div class="about__image">
					<a href="<?php echo esc_url( $release_page_url ); ?>" class="button button-primary button-hero"><?php _e( 'See everything new' ); ?></a>
				</div>
			</div>
		</div>

		<hr class="is-large" style="margin-top:calc(2 * var(--gap));" />

		<div class="about__section has-3-columns">
			<div class="column about__image is-vertically-aligned-top is-left-padding-zero">
				<img src="<?php echo esc_url( admin_url( 'images/about-release-badge.svg?ver=7.2' ) ); ?>" alt="<?php echo esc_attr( $version_text ); ?>" height="280" width="280" />
			</div>
			<div class="column is-vertically-aligned-center is-right-padding-zero" style="grid-column-end:span 2">
				<h3>
					<?php
					printf(
						/* translators: %s: Version number. */
						__( 'Learn more about WordPress %s' ),
						$display_major_version
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
			<div class="column is-left-padding-zero">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<path fill="#1e1e1e" d="M32 15.5H16v3h16v-3ZM16 22h16v3H16v-3ZM28 28.5H16v3h12v-3Z"/>
						<path fill="#1e1e1e" fill-rule="evenodd" d="M34 8H14a4 4 0 0 0-4 4v24a4 4 0 0 0 4 4h20a4 4 0 0 0 4-4V12a4 4 0 0 0-4-4Zm-20 3h20a1 1 0 0 1 1 1v24a1 1 0 0 1-1 1H14a1 1 0 0 1-1-1V12a1 1 0 0 1 1-1Z" clip-rule="evenodd"/>
					</svg>
				</div>
				<h4 style="margin-top: calc(var(--gap) / 2); margin-bottom: calc(var(--gap) / 2);">
					<a href="<?php echo esc_url( $release_notes_url ); ?>">
						<?php
						printf(
							/* translators: %s: WordPress version number. */
							__( 'WordPress %s Release Notes' ),
							$display_major_version
						);
						?>
					</a>
				</h4>
				<p>
					<?php
					printf(
						/* translators: %s: WordPress version number. */
						__( 'Read the WordPress %s Release Notes for information on installation, enhancements, fixed issues, release contributors, learning resources, and the list of file changes.' ),
						$display_major_version
					);
					?>
				</p>
			</div>
			<div class="column is-right-padding-zero">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<path fill="#1e1e1e" stroke="#fff" stroke-width=".5" d="M26.5 24.25h13.75v11.5h-14v8h-3.5v-8H12.604L8.09 31.237a1.75 1.75 0 0 1 0-2.474l4.513-4.513H22.75v-4.5h-14V8.25h14v-4h3.5v4h10.146l4.513 4.513a1.75 1.75 0 0 1 0 2.474l-4.513 4.513H26.25v4.5h.25ZM12.25 16v.25h22.704l.073-.073 1.293-1.293a1.25 1.25 0 0 0 0-1.768l-1.293-1.293-.073-.073H12.25V16Zm1.723 16.177.073.073H36.75v-4.5H14.046l-.073.073-1.293 1.293a1.25 1.25 0 0 0 0 1.768l1.293 1.293Z"/>
					</svg>
				</div>
				<h4 style="margin-top: calc(var(--gap) / 2); margin-bottom: calc(var(--gap) / 2);">
					<a href="<?php echo esc_url( $field_guide_url ); ?>">
						<?php
						printf(
							/* translators: %s: WordPress version number. */
							__( 'WordPress %s Field Guide' ),
							$display_major_version
						);
						?>
					</a>
				</h4>
				<p>
					<?php
					printf(
						/* translators: %s: WordPress version number. */
						__( 'Explore the WordPress %s Field Guide. Learn about the changes in this release with detailed developer notes to help you build with WordPress.' ),
						$display_major_version
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
