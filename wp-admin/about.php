<?php
/**
 * About This Version administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';

wp_enqueue_script( 'wp-components' );
wp_enqueue_style( 'wp-components' );

/* translators: Page title of the About WordPress page in the admin. */
$title = _x( 'About', 'page title' );

list( $display_version ) = explode( '-', get_bloginfo( 'version' ) );

require_once ABSPATH . 'wp-admin/admin-header.php';
?>
	<div class="wrap about__container">

		<div class="about__header">
			<div class="about__header-image">
				<img alt="<?php _e( 'Code is Poetry' ); ?>" src="<?php echo admin_url( 'images/about-badge.svg' ); ?>" />
			</div>

			<div class="about__header-title">
				<p>
					<?php _e( 'WordPress' ); ?>
					<?php echo $display_version; ?>
				</p>
			</div>

			<div class="about__header-text">
				<?php _e( 'Jazz up your stories in an editor that’s cleaner, crisper, and does more to get out of your way.' ); ?>
			</div>

			<nav class="about__header-navigation nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Secondary menu' ); ?>">
				<a href="about.php" class="nav-tab nav-tab-active" aria-current="page"><?php _e( 'What&#8217;s New' ); ?></a>
				<a href="credits.php" class="nav-tab"><?php _e( 'Credits' ); ?></a>
				<a href="freedoms.php" class="nav-tab"><?php _e( 'Freedoms' ); ?></a>
				<a href="privacy.php" class="nav-tab"><?php _e( 'Privacy' ); ?></a>
			</nav>
		</div>

		<div class="about__section is-feature has-subtle-background-color">
			<div class="column">
				<h1 class="is-smaller-heading">
					<?php
					printf(
						/* translators: %s: The current WordPress version number. */
						__( 'Step into WordPress %s.' ),
						$display_version
					);
					?>
				</h1>
				<p>
					<?php
					_e( 'With this new version, WordPress brings you fresh colors. The editor helps you work in a few places you couldn’t before—at least, not without getting into code or hiring a pro. The controls you use most, like changing font sizes, are in more places—right where you need them. And layout changes that should be simple, like full-height images, are even simpler to make.' );
					?>
				</p>
			</div>
		</div>

		<hr />

		<div class="about__section changelog">
			<div class="column has-border has-subtle-background-color">
				<h2 class="is-smaller-heading"><?php _e( 'Maintenance and Security Releases' ); ?></h2>
				<p>
					<?php
					printf(
						/* translators: %s: WordPress version number. */
						__( '<strong>Version %s</strong> addressed some security issues.' ),
						'5.7.8'
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL. */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version. */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '5.7.8' )
						)
					);
					?>
				</p>

				<p>
					<?php
					printf(
						/* translators: %s: WordPress version number. */
						__( '<strong>Version %s</strong> addressed some security issues.' ),
						'5.7.7'
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL. */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version. */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '5.7.7' )
						)
					);
					?>
				</p>

				<p>
					<?php
					printf(
						/* translators: %s: WordPress version number. */
						__( '<strong>Version %s</strong> addressed some security issues.' ),
						'5.7.6'
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL. */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version. */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '5.7.6' )
						)
					);
					?>
				</p>

				<p>
					<?php
					printf(
						/* translators: %s: WordPress version number. */
						__( '<strong>Version %s</strong> addressed some security issues.' ),
						'5.7.5'
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL. */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version. */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '5.7.5' )
						)
					);
					?>
				</p>

				<p>
					<?php
					printf(
						/* translators: %s: WordPress version number. */
						__( '<strong>Version %s</strong> addressed one security issue.' ),
						'5.7.4'
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL. */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version. */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '5.7.4' )
						)
					);
					?>
				</p>

				<p>
					<?php
					printf(
						/* translators: 1: WordPress version number, 2: Plural number of bugs. More than one security issue. */
						_n(
							'<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
							'<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.',
							5
						),
						'5.7.3',
						number_format_i18n( 5 )
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL. */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version. */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '5.7.3' )
						)
					);
					?>
				</p>

				<p>
					<?php
					printf(
						/* translators: %s: WordPress version number. */
						__( '<strong>Version %s</strong> addressed one security issue.' ),
						'5.7.2'
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL. */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version. */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '5.7.2' )
						)
					);
					?>
				</p>

				<p>
					<?php
					printf(
						/* translators: 1: WordPress version number, 2: Plural number of bugs. More than one security issue. */
						_n(
							'<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
							'<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.',
							26
						),
						'5.7.1',
						number_format_i18n( 26 )
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL. */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version. */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '5.7.1' )
						)
					);
					?>
				</p>
			</div>
		</div>

		<hr />

		<div class="about__section has-2-columns">
			<h2 class="is-section-header is-smaller-heading">
				<?php _e( 'Now the editor is easier to use' ); ?>
			</h2>
			<div class="column">
				<p>
					<?php
					_e( '<strong>Font-size adjustment in more places:</strong> now, font-size controls are right where you need them in the List and Code blocks. No more trekking to another screen to make that single change!' );
					?>
				</p>
				<p>
					<?php
					_e( '<strong>Reusable blocks:</strong> several enhancements make reusable blocks more stable and easier to use. And now they save automatically with the post when you click the Update button.' );
					?>
				</p>
				<p>
					<?php
					_e( '<strong>Inserter drag-and-drop:</strong> drag blocks and block patterns from the inserter right into your post.' );
					?>
				</p>
			</div>
			<div class="column about__image">
				<video controls>
					<source src="https://s.w.org/images/core/5.7/about-57-drag-drop-image.mp4" type="video/mp4" />
					<source src="https://s.w.org/images/core/5.7/about-57-drag-drop-image.webm" type="video/webm" />
				</video>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<h2 class="is-section-header is-smaller-heading">
				<?php _e( 'You can do more without writing custom code' ); ?>
			</h2>
			<div class="column">
				<p>
					<?php
					_e( '<strong>Full-height alignment:</strong> have you ever wanted to make a block, like the Cover block, fill the whole window? Now you can.' );
					?>
				</p>
				<p>
					<?php
					_e( '<strong>Buttons block:</strong> now you can choose a vertical or a horizontal layout. And you can set the width of a button to a preset percentage.' );
					?>
				</p>
				<p>
					<?php
					_e( '<strong>Social Icons block:</strong> now you can change the size of the icons.' );
					?>
				</p>
			</div>
			<div class="column about__image">
				<img src="https://s.w.org/images/core/5.7/about-57-cover.jpg" alt="" />
			</div>
		</div>

		<hr />

		<div class="about__section has-subtle-background-color">
			<div class="column">
				<h2 class="is-smaller-heading"><?php _e( 'A Simpler Default Color Palette' ); ?></h2>
			</div>
		</div>

		<div class="about__section has-subtle-background-color">
			<figure class="column about__image" id="about-image-comparison">
				<div class="about__image-comparison no-js">
					<img src="https://s.w.org/images/core/5.7/about-57-color-old.png" alt="<?php esc_attr_e( 'Dashboard with old color scheme.' ); ?>" />
					<div class="about__image-comparison-resize">
						<img src="https://s.w.org/images/core/5.7/about-57-color-new.png" alt="<?php esc_attr_e( 'Dashboard with new color scheme.' ); ?>" />
					</div>
				</div>
				<figcaption><?php _e( 'Above, the Dashboard before and after the color update in 5.7.' ); ?></figcaption>
			</figure>
		</div>

		<div class="about__section has-2-columns has-subtle-background-color">
			<div class="column">
				<p>
					<?php
					printf(
						/* translators: %s: WCAG information link. */
						__( 'This new streamlined color palette collapses all the colors that used to be in the WordPress source code down to seven core colors and a range of 56 shades that meet the <a href="%s">WCAG 2.0 AA recommended contrast ratio</a> against white or black.' ),
						'https://www.w3.org/WAI/WCAG2AAA-Conformance'
					);
					?>
				</p>
				<p>
					<?php
					_e( 'The colors are perceptually uniform from light to dark in each range, which means they start at white and get darker by the same amount with each step.' );
					?>
				</p>
			</div>
			<div class="column">
				<p>
					<?php
					_e( 'Half the range has a 4.5 or higher contrast ratio against black, and the other half maintains the same contrast against white.' );
					?>
				</p>
				<p>
					<?php
					printf(
						/* translators: %s: Color palette dev note link. */
						__( 'Find the new palette in the default WordPress Dashboard color scheme, and use it when you’re building themes, plugins, or any other components. For all the details, <a href="%s">check out the Color Palette dev note</a>.' ),
						'https://make.wordpress.org/core/2021/02/23/standardization-of-wp-admin-colors-in-wordpress-5-7'
					);
					?>
				</p>
			</div>
		</div>

		<div class="about__section has-subtle-background-color">
			<div class="column about__image">
				<picture>
					<source media="(max-width: 600px)" srcset="<?php echo admin_url( 'images/about-color-palette-vert.svg' ); ?>" />
					<img alt="" src="<?php echo admin_url( 'images/about-color-palette.svg' ); ?>" />
				</picture>
			</div>
		</div>

		<hr />

		<div class="about__section has-2-columns">
			<div class="column">
				<h3><?php _e( 'From HTTP to HTTPS in a single click' ); ?></h3>
				<p><?php _e( 'Starting now, switching a site from HTTP to HTTPS is a one-click move. WordPress will automatically update database URLs when you make the switch. No more hunting and guessing!' ); ?></p>
				<h3><?php _e( 'New Robots API' ); ?></h3>
				<p>
					<?php
					_e( 'The new Robots API lets you include the filter directives in the robots meta tag, and the API includes the <code>max-image-preview: large</code> directive by default. That means search engines can show bigger image previews, which can boost your traffic (unless the site is marked <em>not-public</em>).' );
					?>
				</p>
			</div>
			<div class="column">
				<h3><?php _e( 'Ongoing cleanup after update to jQuery 3.5.1' ); ?></h3>
				<p><?php _e( 'For years jQuery helped make things move on the screen in ways the basic tools couldn’t—but that keeps changing, and so does jQuery.' ); ?></p>
				<p><?php _e( 'In 5.7, jQuery gets more focused and less intrusive, with fewer messages in the console.' ); ?></p>
				<h3><?php _e( 'Lazy-load your iframes' ); ?></h3>
				<p><?php _e( 'Now it’s simple to let iframes lazy-load. By default, WordPress will add a <code>loading="lazy"</code> attribute to iframe tags when both width and height are specified.' ); ?></p>
			</div>
		</div>

		<hr class="is-small" />

		<div class="about__section">
			<div class="column">
				<h3><?php _e( 'Check the Field Guide for more!' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: WordPress 5.7 Field Guide link. */
						__( 'Check out the latest version of the WordPress Field Guide. It highlights developer notes for each change you may want to be aware of. <a href="%s">WordPress 5.7 Field Guide.</a>' ),
						'https://make.wordpress.org/core/2021/02/23/wordpress-5-7-field-guide'
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

<script>
	window.addEventListener( 'load', function() {
		var createElement = wp.element.createElement;
		var Fragment = wp.element.Fragment;
		var render = wp.element.render;
		var useState = wp.element.useState;
		var ResizableBox = wp.components.ResizableBox;

		var container = document.getElementById( 'about-image-comparison' );
		var images = container ? container.querySelectorAll( 'img' ) : [];
		if ( ! images.length ) {
			// Something's wrong, return early.
			return;
		}

		var beforeImage = images.item( 0 );
		var afterImage = images.item( 1 );
		var caption = container.querySelector( 'figcaption' ).innerText;

		function ImageComparison( props ) {
			var stateHelper = useState( props.width );
			var width = stateHelper[0];
			var setWidth = stateHelper[1];

			return createElement(
				'div',
				{
					className: 'about__image-comparison'
				},
				createElement( 'img', { src: beforeImage.src, alt: beforeImage.alt } ),
				createElement(
					ResizableBox,
					{
						size: {
							width: width,
							height: props.height
						},
						onResizeStop: function( event, direction, elt, delta ) {
							setWidth( parseInt( width + delta.width, 10 ) );
						},
						showHandle: true,
						enable: {
							top: false,
							right: ! wp.i18n.isRTL(),
							bottom: false,
							left: wp.i18n.isRTL(),
						},
						className: 'about__image-comparison-resize'
					},
					createElement(
						'div',
						{
							style: { width: '100%', height: '100%', overflow: 'hidden' }
						},
						createElement('img', { src: afterImage.src, alt: afterImage.alt } )
					)
				)
			);
		}

		render(
			createElement(
				Fragment,
				{},
				createElement(
					ImageComparison,
					{
						width: beforeImage.clientWidth / 2,
						height: beforeImage.clientHeight
					}
				),
				createElement( 'figcaption', {}, caption )
			),
			container
		);
	} );
</script>
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
