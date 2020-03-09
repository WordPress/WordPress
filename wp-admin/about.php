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
					<span><?php echo $display_version; ?></span>
					<?php _e( 'WordPress' ); ?>
				</h1>
			</div>

			<div class="about__header-badge"></div>

			<div class="about__header-text">
				<p>
					<?php _e( 'Building more with blocks, faster and easier.' ); ?>
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
			<?php _e( 'Say hello to more and better.' ); ?>
			<br />
			<?php _e( 'More ways to make your pages come alive. With easier ways to get it all done and looking better than ever&mdash;and boosts in speed you can feel.' ); ?>
		</div>

		<div class="about__section is-feature">
			<p>
				<?php
				printf(
					/* translators: %s: The current WordPress version number. */
					__( 'Welcome to WordPress %s.' ),
					$display_version
				);
				?>
			</p>
			<p>
				<?php _e( 'Every major release adds more to the block editor.' ); ?>
			</p>
			<p>
				<?php _e( 'More ways to make posts and pages come alive with your best images.' ); ?>
			</p>
			<p>
				<?php _e( 'More ways to bring your visitors in, and keep them engaged, with the richness of embedded media from the web&#8217;s top services.' ); ?>
			</p>
			<p>
				<?php _e( 'More ways to make your vision real, and put blocks in the perfect place&mdash;even if a particular kind of block is new to you. More efficient processes.' ); ?>
			</p>
			<p>
				<?php _e( 'And more speed everywhere, so as you build sections or galleries, or just type in a line of prose, you can feel how much faster your work flows.' ); ?>
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
				<h2><?php _e( 'Two new blocks. And better blocks overall.' ); ?></h2>
				<ul>
					<li><?php _e( 'Two brand-new blocks: Social Icons and Buttons make adding interactive features fast and easy.' ); ?></li>
					<li><?php _e( 'New ways with color: Gradients in the Buttons and Cover block, toolbar access to color options in Rich Text blocks, and for the first time, color options in the Group and Columns blocks.' ); ?></li>
					<li><?php _e( 'Guess a whole lot less! Version 5.4 streamlines the whole process for placing and replacing multimedia in every block. Now it works the same way in almost every block!' ); ?></li>
					<li><?php _e( 'And if you&#8217;ve ever thought your image in the Media+Text block should link to something else&mdash;perhaps a picture of a brochure should download that brochure as a document? Well, now it can.' ); ?></li>
				</ul>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column is-vertically-aligned-center">
				<h2><?php _e( 'Cleaner UI, clearer navigation—and easier tabbing!' ); ?></h2>
				<ul>
					<li><?php _e( 'Clearer block navigation with block breadcrumbs. And easier selection once you get there.' ); ?></li>
					<li><?php _e( 'For when you need to navigate with the keyboard, better tabbing and focus. Plus, you can tab over to the sidebar of nearly any block.' ); ?></li>
					<li><?php _e( 'Speed! 14% faster loading of the editor, 51% faster time-to-type!' ); ?></li>
					<li><?php _e( 'Tips are gone. In their place, a Welcome Guide window you can bring up when you need it&mdash;and only when you need it&mdash;again and again.' ); ?></li>
					<li><?php _e( 'Know at a glance whether you&#8217;re in a block&#8217;s Edit or Navigation mode. Or, if you have restricted vision, your screen reader will tell you which mode you&#8217;re in.' ); ?></li>
				</ul>
				<p><?php _e( 'Of course, if you want to work with the very latest tools and features, install the <a href="https://wordpress.org/plugins/gutenberg/">Gutenberg plugin</a>. You&#8217;ll get to be the first to use new and exciting features in the block editor, before anyone else has seen them!' ); ?></p>
			</div>
			<div class="column is-edge-to-edge has-accent-background-color">
				<div class="about__image aligncenter">
					<img src="data:image/svg+xml;charset=utf8,%3Csvg width='500' height='500' viewbox='0 0 500 500' xmlns='http://www.w3.org/2000/svg'%3E%3Crect x='75' y='200' width='150' height='75' fill='%2344141E'/%3E%3Crect x='175' y='75' width='50' height='100' fill='%2385273B'/%3E%3Crect x='75' y='75' width='75' height='100' fill='%23F4EFE1'/%3E%3Crect x='250' y='200' width='175' height='75' fill='%2344141E'/%3E%3Crect x='350' y='75' width='75' height='100' fill='%2385273B'/%3E%3Crect x='250' y='75' width='75' height='100' fill='%23F4EFE1'/%3E%3Crect x='75' y='375' width='150' height='50' fill='%2344141E'/%3E%3Crect x='175' y='300' width='50' height='50' fill='%2385273B'/%3E%3Crect x='75' y='300' width='75' height='50' fill='%23F4EFE1'/%3E%3Crect x='250' y='372.5' width='175' height='52.5' fill='%2344141E'/%3E%3Crect x='350' y='300' width='75' height='50' fill='%2385273B'/%3E%3Crect x='250' y='300' width='75' height='50' fill='%23F4EFE1'/%3E%3C/svg%3E%0A" alt="">
				</div>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column is-edge-to-edge has-accent-background-color">
				<div class="about__image aligncenter">
					<img src="data:image/svg+xml;charset=utf8,%3Csvg width='660' height='818' viewbox='0 0 660 818' xmlns='http://www.w3.org/2000/svg'%3E%3Crect x='99' y='178' width='132' height='132' fill='%23F4EFE1'/%3E%3Crect x='231' y='310' width='99' height='99' fill='%2344141E'/%3E%3Crect x='330' y='409' width='132' height='132' fill='%23F4EFE1'/%3E%3Crect x='462' y='541' width='99' height='99' fill='%2344141E'/%3E%3C/svg%3E" alt="" />
				</div>
			</div>
			<div class="column is-vertically-aligned-center">
				<h2><?php _e( 'Your fundamental right: privacy' ); ?></h2>
				<p><?php _e( '5.4 helps with a variety of privacy issues around the world. So when users and stakeholders ask about regulatory compliance, or how your team handles user data, the answers should be a lot easier to get right.' ); ?></p>
				<p><?php _e( 'Take a look:' ); ?></p>
				<ul>
					<li><?php _e( 'Now personal data exports include users session information and users location data from the community events widget. Plus, a table of contents!' ); ?></li>
					<li><?php _e( 'See progress as you process export and erasure requests through the privacy tools.' ); ?></li>
					<li><?php _e( 'Plus, little enhancements throughout give the privacy tools a little cleaner look. Your eyes will thank you!' ); ?></li>
				</ul>
			</div>
		</div>

		<hr />

		<div class="about__section has-2-columns has-subtle-background-color">
			<h2 class="is-section-header"><?php _e( 'Just for developers' ); ?></h2>

			<div class="column">
				<h3><?php _e( 'Add custom fields to menu items—natively' ); ?></h3>
				<p>
					<?php _e( 'Two new actions let you add custom fields to menu items&mdash;without a plugin and without writing custom walkers.' ); ?>
				</p>
				<p>
					<?php
					printf(
						/* translators: %s: 'wp_nav_menu_item_custom_fields' action name. */
						__( 'On the Menus admin screen, %s fires just before the move buttons of a nav menu item in the menu editor.' ),
						'<code>wp_nav_menu_item_custom_fields</code>'
					);
					?>
				</p>
				<p>
					<?php
					printf(
						/* translators: %s: 'wp_nav_menu_item_custom_fields_customize_template' action name. */
						__( 'In the Customizer, %s fires at the end of the menu-items form-fields template.' ),
						'<code>wp_nav_menu_item_custom_fields_customize_template</code>'
					);
					?>
				</p>
				<p>
					<?php _e( 'Check your code and see where these new actions can replace your custom code, and if you&#8217;re concerned about duplication, add a check for the WordPress version.' ); ?>
				</p>
			</div>
			<div class="column">
				<h3><?php _e( 'Blocks! Simpler styling, new APIs and embeds' ); ?></h3>
				<ul>
					<li><?php _e( '<strong>Radically</strong> simpler block styling. Negative margins and default padding are gone! Now you can style blocks the way you need them. And, a refactor got rid of four redundant wrapper divs.' ); ?></li>
					<li><?php _e( 'If you build plugins, now you can register collections of your blocks by namespace across categories—a great way to get more brand visibility.' ); ?></li>
					<li><?php _e( 'Let users do more with two new APIs: block variations and gradients.' ); ?></li>
					<li><?php _e( 'In embeds, now the block editor supports TikTok—and CollegeHumor is gone.' ); ?></li>
				</ul>
			</div>
		</div>

		<div class="about__section is-feature">
			<p>
				<?php
				printf(
					/* translators: %s: WordPress 5.4 Field Guide link. */
					__( 'There&#8217;s lots more for developers to love in WordPress 5.4. To discover more and learn how to make these changes shine on your on your sites, themes, plugins and more, check the <a href="%s">WordPress 5.4 Field Guide</a>.' ),
					'https://make.wordpress.org/core/?p=80034'
				);
				?>
			</p>
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
