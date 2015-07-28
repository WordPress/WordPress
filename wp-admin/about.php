<?php
/**
 * About This Version administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

wp_enqueue_style( 'wp-mediaelement' );
wp_enqueue_script( 'wp-mediaelement' );
wp_localize_script( 'mediaelement', '_wpmejsSettings', array(
	'pluginPath' => includes_url( 'js/mediaelement/', 'relative' ),
	'pauseOtherPlayers' => ''
) );

$title = __( 'About' );

list( $display_version ) = explode( '-', $wp_version );

include( ABSPATH . 'wp-admin/admin-header.php' );

$major_features = array(
	array(
		'src'         => '',
		'heading'     => 'Formatting Shortcuts',

		/* Translators: 1: asterisks; 2: number sign; */
		'description' => sprintf( 'Your writing flow just got faster with new formatting shortcuts in WordPress 4.3. Use asterisks to create lists and number signs to make a heading. No more breaking your flow; your text looks great with a %1$s and a %2$.', '<code>*</code>', '<code>#</code>' ),
	),
	array(
		'src'         => '',
		'heading'     => 'Menus in the Customizer',
		'description' => 'Create your menu, update it, and preview it in the customizer, before sharing it with the world. With every release, it becomes easier and faster to build your site from the front-end. And a streamlined customizer design mean a mobile-first, accessibility ready interface.',
	),
	array(
		'src'         => '',
		'heading'     => 'Better Passwords',
		'description' => 'Keep your site more secure with WordPress’ improved approach to passwords. Instead of receiving passwords via email, you’ll get a password reset link. When you add new users to your site, WordPress will automatically generate a secure password.',
	),
	array(
		'src'         => '',
		'heading'     => 'Site Icons',
		'description' => 'Site icons represent your site in browser tabs, bookmark menus, and on the home screen of mobile devices. Add your unique site icon in the customizer; it will even stay in place when you switch themes. Make your whole site reflect your brand.',
	),
);
shuffle( $major_features );

$minor_features = array(
	array(
		'src'         => '',
		'heading'     => 'A smoother admin experience',
		'description' => 'Refinements to the list view across the admin make your WordPress more accessible and easier to work with on any device.',
	),
	array(
		'src'         => '',
		'heading'     => 'Comments turned off on pages',
		'description' => 'All new pages that you create will have comments turned off. Keep discussions to your blog, right where they’re supposed to happen.',
	),
	array(
		'src'         => '',
		'heading'     => 'Customize your site quickly' ,
		'description' => 'Wherever you are on the front-end, you can click the customize button in the toolbar to swiftly make changes to your site.',
	),
);

$tech_features = array(
	array(
		'heading'     => 'Taxonomy Roadmap',
		'description' => 'Terms shared across multiple taxonomies are now split into separate terms.',
	),
	array(
		'heading'     => 'Template Hierarchy',

		/* Translators: 1: singular.php; 2: single.php; 3:page.php */
		'description' => sprintf( 'Added %1$s as a fallback for %2$s and %3$s', '<code>singular.php</code>', '<code>single.php</code>', '<code>page.php</code>.' ),
	),
	array(
		'heading'     => 'List table',
		'description' => 'List tables now can (and often should) have a primary column defined.',
	),
);

?>
	<div class="wrap about-wrap">
		<h1><?php printf( __( 'Welcome to WordPress&nbsp;%s' ), $display_version ); ?></h1>

		<div class="about-text"><?php /* @TODO Fun tag line. */ printf( 'Thank you for updating to WordPress %s!', $display_version ); ?></div>
		<div class="wp-badge"><?php printf( __( 'Version %s' ), $display_version ); ?></div>

		<h2 class="nav-tab-wrapper">
			<a href="about.php" class="nav-tab nav-tab-active"><?php _e( 'What&#8217;s New' ); ?></a>
			<a href="credits.php" class="nav-tab"><?php _e( 'Credits' ); ?></a>
			<a href="freedoms.php" class="nav-tab"><?php _e( 'Freedoms' ); ?></a>
		</h2>

		<div class="headline-feature feature-video">
			<!-- [4.3 video] -->
		</div>

		<hr/>

		<div class="feature-section two-col">
			<?php foreach ( $major_features as $feature ) : ?>
			<div class="col">
				<img src="<?php echo esc_url( $feature['src'] ); ?>" />
				<h3><?php echo $feature['heading']; ?></h3>
				<p><?php echo $feature['description']; ?></p>
			</div>
			<?php endforeach; ?>
		</div>

		<div class="feature-section three-col">
			<?php foreach ( $minor_features as $feature ) : ?>
			<div class="col">
				<img src="<?php echo esc_url( $feature['src'] ); ?>" />
				<h3><?php echo $feature['heading']; ?></h3>
				<p><?php echo $feature['description']; ?></p>
			</div>
			<?php endforeach; ?>
		</div>

		<div class="changelog">
			<h3><?php _e( 'Under the Hood' ); ?></h3>

			<div class="feature-section under-the-hood three-col">
				<?php foreach ( $tech_features as $feature ) : ?>
				<div class="col">
					<h3><?php echo $feature['heading']; ?></h3>
					<p><?php echo $feature['description']; ?></p>
				</div>
				<?php endforeach; ?>
			</div>

			<div class="return-to-dashboard">
				<?php if ( current_user_can( 'update_core' ) && isset( $_GET['updated'] ) ) : ?>
					<a href="<?php echo esc_url( self_admin_url( 'update-core.php' ) ); ?>">
						<?php is_multisite() ? _e( 'Return to Updates' ) : _e( 'Return to Dashboard &rarr; Updates' ); ?>
					</a> |
				<?php endif; ?>
				<a href="<?php echo esc_url( self_admin_url() ); ?>"><?php is_blog_admin() ? _e( 'Go to Dashboard &rarr; Home' ) : _e( 'Go to Dashboard' ); ?></a>
			</div>

		</div>
	</div>
<?php

include( ABSPATH . 'wp-admin/admin-footer.php' );

// These are strings we may use to describe maintenance/security releases, where we aim for no new strings.
return;

_n_noop( 'Maintenance Release', 'Maintenance Releases' );
_n_noop( 'Security Release', 'Security Releases' );
_n_noop( 'Maintenance and Security Release', 'Maintenance and Security Releases' );

/* translators: 1: WordPress version number. */
_n_noop( '<strong>Version %1$s</strong> addressed a security issue.',
         '<strong>Version %1$s</strong> addressed some security issues.' );

/* translators: 1: WordPress version number, 2: plural number of bugs. */
_n_noop( '<strong>Version %1$s</strong> addressed %2$s bug.',
         '<strong>Version %1$s</strong> addressed %2$s bugs.' );

/* translators: 1: WordPress version number, 2: plural number of bugs. Singular security issue. */
_n_noop( '<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bug.',
         '<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bugs.' );

/* translators: 1: WordPress version number, 2: plural number of bugs. More than one security issue. */
_n_noop( '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
         '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.' );

__( 'For more information, see <a href="%s">the release notes</a>.' );
