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
				<h2><?php _e( 'Maintenance and Security Release' ); ?></h2>
				<p>
					<?php
					printf(
						/* translators: 1: WordPress version number, 2: Plural number of bugs. */
						_n(
							'<strong>Version %1$s</strong> addressed %2$s bug.',
							'<strong>Version %1$s</strong> addressed %2$s bugs.',
							16
						),
						'6.6.1',
						'16'
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL. */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version. */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '6.6.1' )
						)
					);
					?>
				</p>
			</div>
		</div>

		<div class="about__section">
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
					<?php _e( 'Create and deploy beautiful and coherent design elements across your sites with WordPress 6.6. A new rollback option for auto-updating plugins gives you control, flexibility, and peace of mind.' ); ?>
				</p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column is-vertically-aligned-center">
				<h3><?php _e( 'Color palettes & font sets' ); ?></h3>
				<p><strong><?php _e( 'Add more design options to any block theme.' ); ?></strong> <?php _e( 'Block theme authors can create unlimited individual color or font sets to offer more specific design options within the same theme. These sets provide more contained design possibilities, allowing for customization without changing the site&#8217;s broader styling, beyond color or typography settings.' ); ?></p>
			</div>
			<div class="column is-vertically-aligned-center">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.6/color-palettes.webp" alt="" height="436" width="436" />
				</div>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column is-vertically-aligned-center">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.6/page-previews.webp" alt="" height="436" width="436" />
				</div>
			</div>
			<div class="column is-vertically-aligned-center">
				<h3><?php _e( 'Quick previews for pages' ); ?></h3>
				<p><strong><?php _e( 'Simplify your workflow with a new layout built for pages.' ); ?></strong> <?php _e( 'See all of your pages and a preview of any selected page before you edit via a new side-by-side layout in the Site Editor.' ); ?></p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column is-vertically-aligned-center">
				<h3><?php _e( 'Rollbacks for plugin auto-updates' ); ?></h3>
				<p><strong><?php _e( 'Auto-update your plugins with peace of mind.' ); ?></strong> <?php _e( 'Enjoy the ease of plugin auto-updates with the safety of rollbacks if anything goes wrong, improving your site&#8217;s security while minimizing potential downtime.' ); ?></p>
			</div>
			<div class="column is-vertically-aligned-center">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.6/feature-rollbacks.webp" alt="" height="436" width="436" />
				</div>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column is-vertically-aligned-center">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.6/overrides.webp" alt="" height="436" width="436" />
				</div>
			</div>
			<div class="column is-vertically-aligned-center">
				<h3><?php _e( 'Overrides' ); ?></h3>
				<p><strong><?php _e( 'Add the ability to customize content in synced patterns.' ); ?></strong> <?php _e( 'Allow specific pieces of content to be customized in each instance of a synced pattern while keeping a consistent style for all instances, simplifying future updates. Currently, you can set overrides for Heading, Paragraph, Button, and Image blocks.' ); ?></p>
			</div>
		</div>

		<hr class="is-invisible is-large" />

		<div class="about__section has-2-columns">
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4"/>
						<path d="M28.4287 20.6507C28.8387 20.8874 28.9791 21.4116 28.7424 21.8215L24.7424 28.7498C24.5057 29.1597 23.9815 29.3002 23.5715 29.0635C23.1616 28.8268 23.0211 28.3026 23.2578 27.8926L27.2578 20.9644C27.4945 20.5544 28.0187 20.414 28.4287 20.6507Z" fill="#1e1e1e"/>
						<path d="M18.6433 23.579C18.2333 23.3423 17.7091 23.4828 17.4724 23.8927C17.2357 24.3027 17.3761 24.8269 17.7861 25.0636L18.281 25.3493C18.691 25.586 19.2152 25.4456 19.4519 25.0356C19.6886 24.6256 19.5481 24.1014 19.1381 23.8647L18.6433 23.579Z" fill="#1e1e1e"/>
						<path d="M20.0358 20.6508C20.4458 20.4141 20.97 20.5546 21.2067 20.9645L21.4924 21.4594C21.7291 21.8694 21.5887 22.3936 21.1787 22.6303C20.7687 22.867 20.2445 22.7265 20.0078 22.3166L19.7221 21.8217C19.4854 21.4117 19.6259 20.8875 20.0358 20.6508Z" fill="#1e1e1e"/>
						<path d="M24.8571 20C24.8571 19.5266 24.4734 19.1429 24 19.1429C23.5266 19.1429 23.1429 19.5266 23.1429 20V20.5714C23.1429 21.0448 23.5266 21.4286 24 21.4286C24.4734 21.4286 24.8571 21.0448 24.8571 20.5714V20Z" fill="#1e1e1e"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M14 26C14 20.4772 18.4772 16 24 16C29.5228 16 34 20.4772 34 26C34 28.0846 33.3612 30.0225 32.2686 31.6256L32.0135 32H15.9865L15.7314 31.6256C14.6388 30.0225 14 28.0846 14 26ZM24 17.7143C19.4239 17.7143 15.7143 21.4239 15.7143 26C15.7143 27.5698 16.1501 29.0357 16.9072 30.2857H31.0928C31.8499 29.0357 32.2857 27.5698 32.2857 26C32.2857 21.4239 28.5761 17.7143 24 17.7143Z" fill="#1e1e1e"/>
					</svg>
				</div>
				<h3><?php _e( 'Performance updates' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %1$s: code-formatted "WP_Theme_JSON", %2$s: code-formatted "data-wp-on-async", %%: escaped percent sign, leave as %%. */
						__( 'WordPress 6.6 includes important updates like removing redundant %1$s calls, disabling autoload for large options, eliminating unnecessary polyfill dependencies, lazy loading post embeds, introducing the %2$s directive, and a 33%% reduction in template loading time in the editor.' ),
						'<code>WP_Theme_JSON</code>',
						'<code>data-wp-on-async</code>'
					);
					?>
				</p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4"/>
						<path d="M24 18.285C23.55 18.285 23.1637 18.1237 22.8412 17.8012C22.5187 17.4788 22.3575 17.0925 22.3575 16.6425C22.3575 16.1925 22.5187 15.8062 22.8412 15.4837C23.1637 15.1612 23.55 15 24 15C24.45 15 24.8362 15.1612 25.1587 15.4837C25.4812 15.8062 25.6425 16.1925 25.6425 16.6425C25.6425 17.0925 25.4812 17.4788 25.1587 17.8012C24.8362 18.1237 24.45 18.285 24 18.285ZM21.5925 33V21.0075C20.5725 20.9325 19.5862 20.8275 18.6337 20.6925C17.6812 20.5575 16.77 20.385 15.9 20.175L16.2375 18.825C17.5125 19.125 18.78 19.3387 20.04 19.4662C21.3 19.5938 22.62 19.6575 24 19.6575C25.38 19.6575 26.7 19.5938 27.96 19.4662C29.22 19.3387 30.4875 19.125 31.7625 18.825L32.1 20.175C31.23 20.385 30.3187 20.5575 29.3662 20.6925C28.4137 20.8275 27.4275 20.9325 26.4075 21.0075V33H25.0575V27.15H22.9425V33H21.5925Z" fill="#1e1e1e"/>
					</svg>
				</div>
				<h3><?php _e( 'Accessibility improvements' ); ?></h3>
				<p><?php _e( '55+ accessibility fixes and enhancements focus on foundational aspects of the WordPress experience, particularly the data views component powering the new site editing experience and areas like the Inserter that provide a key way of interacting with blocks and patterns.' ); ?></p>
			</div>
		</div>

		<hr class="is-invisible is-large" style="margin-bottom:calc(2 * var(--gap));" />

		<div class="about__section has-2-columns is-wider-left is-feature" style="background-color:var(--background);border-radius:var(--border-radius);">
			<h3 class="is-section-header"><?php _e( 'And much more' ); ?></h3>
			<div class="column">
				<p><?php _e( 'For a comprehensive overview of all the new features and enhancements in WordPress 6.6, please visit the feature-showcase website.' ); ?></p>
			</div>
			<div class="column aligncenter">
				<div class="about__image">
					<a href="<?php echo esc_url( __( 'https://wordpress.org/download/releases/6-6/' ) ); ?>" class="button button-primary button-hero"><?php _e( 'See everything new' ); ?></a>
				</div>
			</div>
		</div>

		<hr class="is-large" style="margin-top:calc(2 * var(--gap));" />

		<div class="about__section has-3-columns">
			<div class="column about__image is-vertically-aligned-top">
				<img src="<?php echo esc_url( admin_url( 'images/about-release-badge.svg?ver=6.6' ) ); ?>" alt="" height="280" width="280" />
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
						<rect width="48" height="48" rx="4"/>
						<path d="M23 34v-4h-5l-2.293-2.293a1 1 0 0 1 0-1.414L18 24h5v-2h-7v-6h7v-2h2v2h5l2.293 2.293a1 1 0 0 1 0 1.414L30 22h-5v2h7v6h-7v4h-2Zm-5-14h11.175l.646-.646a.5.5 0 0 0 0-.708L29.175 18H18v2Zm.825 8H30v-2H18.825l-.646.646a.5.5 0 0 0 0 .708l.646.646Z" fill="#1e1e1e"/>
					</svg>
				</div>
				<p style="margin-top:calc(var(--gap) / 2);">
					<?php
					printf(
						/* translators: 1: WordPress Field Guide link, 2: WordPress version number. */
						__( 'Explore the <a href="%1$s">WordPress %2$s Field Guide</a>. Learn about the changes in this release with detailed developer notes to help you build with WordPress.' ),
						esc_url( __( 'https://make.wordpress.org/core/wordpress-6-6-field-guide/' ) ),
						'6.6'
					);
					?>
				</p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4"/>
						<path d="M28 19.75h-8v1.5h8v-1.5ZM20 23h8v1.5h-8V23ZM26 26.25h-6v1.5h6v-1.5Z" fill="#151515"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M29 16H19a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V18a2 2 0 0 0-2-2Zm-10 1.5h10a.5.5 0 0 1 .5.5v12a.5.5 0 0 1-.5.5H19a.5.5 0 0 1-.5-.5V18a.5.5 0 0 1 .5-.5Z" fill="#1e1e1e"/>
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
							'6-6'
						),
						'6.6'
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
