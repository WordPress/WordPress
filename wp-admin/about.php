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

			<div class="about__header-text">
				<?php _e( 'Build the site you&#8217;ve always wanted &#8212; with blocks' ); ?>
			</div>

			<nav class="about__header-navigation nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Secondary menu' ); ?>">
				<a href="about.php" class="nav-tab nav-tab-active" aria-current="page"><?php _e( 'What&#8217;s New' ); ?></a>
				<a href="credits.php" class="nav-tab"><?php _e( 'Credits' ); ?></a>
				<a href="freedoms.php" class="nav-tab"><?php _e( 'Freedoms' ); ?></a>
				<a href="privacy.php" class="nav-tab"><?php _e( 'Privacy' ); ?></a>
			</nav>
		</div>

		<div class="about__section changelog">
			<div class="column">
				<h2><?php _e( 'Maintenance Release' ); ?></h2>
				<p>
					<?php
					printf(
						/* translators: 1: WordPress version number, 2: plural number of bugs. */
						_n(
							'<strong>Version %1$s</strong> addressed %2$s bug.',
							'<strong>Version %1$s</strong> addressed %2$s bugs.',
							82
						),
						'5.9.1',
						number_format_i18n( 82 )
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL. */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version. */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '5.9.1' )
						)
					);
					?>
				</p>
			</div>
		</div>

		<hr class="is-large" />

		<div class="about__section">
			<h2 class="aligncenter">
				<?php _e( 'Full Site Editing is here' ); ?>
			</h2>
			<p class="aligncenter is-subheading">
				<?php _e( 'It puts you in control of your whole site, right in the WordPress Admin.' ); ?>
			</p>
		</div>

		<hr />

		<div class="about__section has-2-columns has-gutters is-wider-left">
			<div class="column about__image is-vertically-aligned-center is-edge-to-edge">
				<img src="https://s.w.org/images/core/5.9/twenty-twenty-two.png" alt="" />
			</div>
			<div class="column is-edge-to-edge">
				<h3>
					<?php _e( 'Say hello to Twenty Twenty&#8209;Two' ); ?>
				</h3>
				<p>
					<?php _e( 'And say hello to the first default block theme in the history of WordPress. This is more than just a new default theme. It&#8217;s a brand-new way to work with WordPress themes.' ); ?>
				</p>
				<p>
					<?php _e( 'Block themes put a wide array of visual choices in your hands, from color schemes and typeface combinations to page templates and image filters &#8212; all together, in the site editing interface. By making changes in one place, you can give Twenty Twenty&#8209;Two the same look and feel as your brand or other websites &#8212; or take your site&#8217;s look in another direction.' ); ?>
				</p>
				<?php if ( current_user_can( 'switch_themes' ) ) : ?>
				<p>
					<?php
					printf(
						/* translators: %s: Link to Themes screen. */
						__( 'The Twenty Twenty&#8209;Two theme is already available to you. It came installed with WordPress 5.9, and you will find it with <a href="%s">your other installed themes</a>.' ),
						admin_url( 'themes.php' )
					);
					?>
				</p>
				<?php endif; ?>
			</div>
		</div>

		<div class="about__section has-2-columns has-gutters is-wider-right">
			<div class="column is-edge-to-edge">
				<h3>
					<?php _e( 'Your personal paintbox awaits' ); ?>
				</h3>
				<p>
					<?php _e( 'More block themes built for full site editing features are in the Theme Directory alongside the Twenty Twenty&#8209;Two theme, just waiting to be explored. Expect more to come!' ); ?>
				</p>
				<p>
					<?php _e( 'When you use any of those new themes, you no longer need the Customizer. Instead, you have all the power of the Styles interface inside the Site Editor. Just as in Twenty Twenty&#8209;Two, you build your site&#8217;s look and feel there, with the tools you need for the job in a fluid interface that practically comes alive in your hands.' ); ?>
				</p>
			</div>
			<div class="column about__image is-vertically-aligned-center is-edge-to-edge">
				<img src="https://s.w.org/images/core/5.9/global-styles.png" alt="" />
			</div>
		</div>

		<div class="about__section has-2-columns has-gutters is-wider-left">
			<div class="column about__image is-vertically-aligned-center is-edge-to-edge">
				<img src="https://s.w.org/images/core/5.9/navigation-block.png" alt="" />
			</div>
			<div class="column is-edge-to-edge">
				<h3>
					<?php _e( 'The Navigation block' ); ?>
				</h3>
				<p>
					<?php _e( 'Blocks come to site navigation, the heart of user experience.' ); ?>
				</p>
				<p>
					<?php _e( 'The new Navigation block gives you the power to choose: an always-on responsive menu or one that adapts to your user&#8217;s screen size. Whatever you create, know it&#8217;s there to reuse wherever you like, whether in a brand new template or after switching themes.' ); ?>
				</p>
			</div>
		</div>

		<hr class="is-large" />

		<div class="about__section">
			<h2 class="aligncenter">
				<?php _e( 'More improvements and updates' ); ?>
			</h2>
			<p class="aligncenter is-subheading">
				<?php _e( 'Do you love to blog or produce content? New tweaks to the publishing flow help you say more, faster.' ); ?>
			</p>
		</div>

		<hr />

		<div class="about__section has-2-columns has-gutters is-wider-left">
			<div class="column about__image is-vertically-aligned-center is-edge-to-edge">
				<img src="https://s.w.org/images/core/5.9/block-controls.png" alt="" />
			</div>
			<div class="column is-edge-to-edge">
				<h3>
					<?php _e( 'Better block controls' ); ?>
				</h3>
				<p>
					<?php _e( 'WordPress 5.9 features new typography tools, flexible layout controls, and finer control over details like spacing, borders, and more &#8212; to help you get not just the look, but the polish that says you care about details.' ); ?>
				</p>
			</div>
		</div>

		<div class="about__section has-2-columns has-gutters is-wider-right">
			<div class="column is-edge-to-edge">
				<h3>
					<?php _e( 'The power of patterns' ); ?>
				</h3>
				<p>
					<?php _e( 'The WordPress Pattern Directory is the home of a wide range of block patterns built to save you time and add core site functionality. And you can edit them as you see fit. Need something different in the header or footer for your theme? Swap it out with a new one in a few clicks.' ); ?>
				</p>
				<p>
					<?php _e( 'With a near full-screen view that draws you in to see fine details, the Pattern Explorer makes it easy to compare patterns and choose the one your users will expect.' ); ?>
				</p>
			</div>
			<div class="column about__image is-vertically-aligned-center is-edge-to-edge">
				<img src="https://s.w.org/images/core/5.9/pattern-explorer.png" alt="" />
			</div>
		</div>

		<div class="about__section has-2-columns has-gutters is-wider-left">
			<div class="column about__image is-vertically-aligned-center is-edge-to-edge">
				<img src="https://s.w.org/images/core/5.9/list-view.png" alt="" />
			</div>
			<div class="column is-edge-to-edge">
				<h3>
					<?php _e( 'A revamped List View' ); ?>
				</h3>
				<p>
					<?php _e( 'In 5.9, the List View lets you drag and drop your content exactly where you want it. Managing complex documents is easier, too: simple controls let you expand and collapse sections as you build your site &#8212; and add HTML anchors to your blocks to help users get around the page.' ); ?>
				</p>
			</div>
		</div>

		<div class="about__section has-2-columns has-gutters is-wider-right">
			<div class="column is-edge-to-edge">
				<h3>
					<?php _e( 'A better Gallery block' ); ?>
				</h3>
				<p>
					<?php _e( 'Treat every image in a Gallery block the same way you would treat it in the Image block.' ); ?>
				</p>
				<p>
					<?php _e( 'Style every image in your gallery differently from the next (with different crops, or duotones, for instance) or make them all the same. And change the layout with drag-and-drop.' ); ?>
				</p>
			</div>
			<div class="column about__image is-vertically-aligned-center is-edge-to-edge">
				<img src="https://s.w.org/images/core/5.9/gallery-block.png" alt="" />
			</div>
		</div>

		<hr class="is-large" />

		<div class="about__section">
			<h2 class="aligncenter" style="margin-bottom:0;">
				<?php
				printf(
					/* translators: %s: Version number. */
					__( 'WordPress %s for developers' ),
					$display_version
				);
				?>
			</h2>
		</div>

		<div class="about__section has-gutters has-2-columns">
			<div class="column is-edge-to-edge">
				<h3>
					<?php _e( 'Introducing block themes' ); ?>
				</h3>
				<p>
					<?php
					printf(
						/* translators: %s: Block-based themes dev note link. */
						__( 'A new way to build themes: Block themes use blocks to define the templates that structure your entire site. The new templates and template parts are defined in HTML and use the custom styling offered in theme.json. More information is available in the <a href="%s">block themes dev note</a>.' ),
						'https://make.wordpress.org/core/2022/01/04/block-themes-a-new-way-to-build-themes-in-wordpress-5-9/'
					);
					?>
				</p>
			</div>
			<div class="column is-edge-to-edge">
				<h3>
					<?php _e( 'Multiple stylesheets for a block' ); ?>
				</h3>
				<p>
					<?php
					printf(
						/* translators: %s: Multiple stylesheets dev note link. */
						__( 'Now you can register more than one stylesheet per block. You can use this to share styles across blocks you write, or to load styles for individual blocks, so your styles are only loaded when the block is used. Find out more about <a href="%s">using multiple stylesheets in a block</a>.' ),
						'https://make.wordpress.org/core/2021/12/15/using-multiple-stylesheets-per-block/'
					);
					?>
				</p>
			</div>
		</div>
		<div class="about__section has-gutters has-2-columns">
			<div class="column is-edge-to-edge">
				<h3>
					<?php _e( 'Block&#8209;level locking' ); ?>
				</h3>
				<p>
					<?php _e( 'Now you can lock any block (or a few of them) in a pattern, just by adding a lock attribute to its settings in block.json &#8212; leaving the rest of the pattern free for site editors to adapt to their content.' ); ?>
				</p>
			</div>
			<div class="column is-edge-to-edge">
				<h3>
					<?php _e( 'A refactored Gallery block' ); ?>
				</h3>
				<p>
					<?php
					printf(
						/* translators: %s: Gallery Refactor dev note link. */
						__( 'The changes to the Gallery block listed above are the result of a near-complete refactoring. Have you built a plugin or theme on the Gallery block functionality? Be sure to read the <a href="%s">Gallery block compatibility dev note</a>.' ),
						'https://make.wordpress.org/core/2021/08/20/gallery-block-refactor-dev-note/'
					);
					?>
				</p>
			</div>
		</div>

		<hr class="is-large" />

		<div class="about__section has-subtle-background-color has-2-columns is-wider-right">
			<div class="column about__image is-vertically-aligned-center">
				<img src="https://s.w.org/images/core/5.9/learn-video.png" alt="" />
			</div>
			<div class="column">
				<h3><?php _e( 'Learn more about the new features in 5.9' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: Learn WordPress link. */
						__( 'Want to dive into 5.9 but don&#8217;t know where to start? Visit <a href="%s">learn.wordpress.org</a> for expanding resources on new features in WordPress 5.9.' ),
						'https://learn.wordpress.org'
					);
					?>
				</p>
			</div>
		</div>

		<hr class="is-large" />

		<div class="about__section">
			<div class="column">
				<h3><?php _e( 'Check the Field Guide for more!' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: WordPress 5.9 Field Guide link. */
						__( 'Check out the latest version of the WordPress Field Guide. It highlights developer notes for each change you may want to be aware of. <a href="%s">WordPress 5.9 Field Guide.</a>' ),
						'https://make.wordpress.org/core/2022/01/10/wordpress-5-9-field-guide/'
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
