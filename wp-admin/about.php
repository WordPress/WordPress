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
		'src'         => array(
			'mp4'  => 'https://cldup.com/2k26HVNP6P.mp4',
			'ogv'  => '',
			'webm' => '',
		),
		'heading'     => 'Formatting Shortcuts',

		/* Translators: 1: asterisks; 2: number sign; */
		'description' => sprintf( 'Your writing flow just got faster with new formatting shortcuts in WordPress 4.3. Use asterisks to create lists and number signs to make a heading. No more breaking your flow; your text looks great with a %1$s and a %2$s.', '<code>*</code>', '<code>#</code>' ),
	),
	array(
		'src'         => 'https://cldup.com/k23oK-g_v1.jpg',
		'heading'     => 'Menus in the Customizer',
		'description' => 'Create your menu, update it, and preview it in the customizer, before sharing it with the world. With every release, it becomes easier and faster to build your site from the front-end. And a streamlined customizer design mean a mobile-first, accessibility ready interface.',
	),
	array(
		'src'         => 'https://cldup.com/t1HCztI0PR.jpg',
		'heading'     => 'Better Passwords',
		'description' => 'Keep your site more secure with WordPress’ improved approach to passwords. Instead of receiving passwords via email, you’ll get a password reset link. When you add new users to your site, WordPress will automatically generate a secure password.',
	),
	array(
		'src'         => 'https://cldup.com/8LxuMwmsvE.jpg',
		'heading'     => 'Site Icons',
		'description' => 'Site icons represent your site in browser tabs, bookmark menus, and on the home screen of mobile devices. Add your unique site icon in the customizer; it will even stay in place when you switch themes. Make your whole site reflect your brand.',
	),
);
shuffle( $major_features );

$minor_features = array(
	array(
		'src'         => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA0MDAgNDAwIj48cGF0aCBmaWxsPSIjMDBhMGQyIiBkPSJNNTAgMjE1aDI0MHYzMEg1MHpNNTAgMjc1aDI0MHYzMEg1MHpNNTAgMTU1aDI0MHYzMEg1MHpNNTAgOTVoMjQwdjMwSDUwek0zMTAuMSA5NWwxOS45IDMwIDIwLjEtMzAiLz48L3N2Zz4=',
		'heading'     => 'A smoother admin experience',
		'description' => 'Refinements to the list view across the admin make your WordPress more accessible and easier to work with on any device.',
	),
	array(
		'src'         => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMCAyMCI+PHBhdGggZmlsbD0iIzAwYTBkMiIgZD0iTTUgMmgxMHEuODIgMCAxLjQxLjU5VDE3IDR2OHEwIC44Mi0uNTkgMS40MVQxNSAxNGgtMmwtNSA1di01SDVxLS44MiAwLTEuNDEtLjU5VDMgMTJWNHEwLS44Mi41OS0xLjQxVDUgMnptOC41IDguNUwxMSA4bDIuNS0yLjUtMS0xTDEwIDcgNy41IDQuNWwtMSAxTDkgOGwtMi41IDIuNSAxIDFMMTAgOWwyLjUgMi41eiIvPjwvc3ZnPg==',
		'heading'     => 'Comments turned off on pages',
		'description' => 'All new pages that you create will have comments turned off. Keep discussions to your blog, right where they’re supposed to happen.',
	),
	array(
		'src'         => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAzMiAzMiI+PHBhdGggZmlsbD0iIzAwYTBkMiIgZD0iTTI5LjMyOCA1LjcxMnEuMDQ4LS4xNDQuMDk2LS4zODR0LS4wNjQtLjgxNi0uNTI4LS45NzZxLS4zODQtLjM2OC0uODcyLS40NjR0LS43OTIgMGwtLjI4OC4wOHEtMS40NTYuNzItNS44OCAzLjczNnQtNi4zOTIgNS4xNzZxLS43MzYuODMyLTEuNDA4IDIuMzJ0LS44OCAzIC41NDQgMi4zOTJxLjgzMi43MzYgMi4zNDQuNTc2dDMuMDcyLS44MjQgMi4yNDgtMS4zNTJxMi4xNDQtMi4xNDQgNS4xNjgtNi42NTZ0My42MzItNS44MDh6TTIuMjQgMjguMjRxMS4wNTYtLjY4OCAxLjcxMi0xLjUyOHQuOTUyLTEuNjE2LjU0NC0xLjUyLjcyLTEuNDggMS4yNC0xLjI4cTEuMDg4LS44IDIuNTA0LS43MDR0Mi40MjQgMS4xNjhxLjgxNi44OC44MjQgMi42NHQtMS4wOCAyLjg5NnEtMS4yMTYgMS4xMi0yLjkwNCAxLjYyNHQtMy40MjQuNDI0LTMuNTEyLS42MjR6Ii8+PC9zdmc+',
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

		<div class="about-text"><?php printf( 'Thank you for updating! WordPress %s: faster workflow, easier customization, strong by default.', $display_version ); ?></div>
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
				<div class="media-container">
					<?php
					// Video.
					if ( is_array( $feature['src'] ) ) :
						echo wp_video_shortcode( array(
							'mp4'      => $feature['src']['mp4'],
							'ogv'      => $feature['src']['ogv'],
							'webm'     => $feature['src']['webm'],
							'loop'     => true,
							'autoplay' => true,
							'width'    => 500,
							'height'   => 284
						) );

					// Image.
					else:
					?>
					<img src="<?php echo esc_url( $feature['src'] ); ?>" />
					<?php endif; ?>
				</div>
				<h3><?php echo $feature['heading']; ?></h3>
				<p><?php echo $feature['description']; ?></p>
			</div>
			<?php endforeach; ?>
		</div>

		<div class="feature-section three-col">
			<?php foreach ( $minor_features as $feature ) : ?>
			<div class="col">
				<div class="svg-container">
					<img src="<?php echo esc_attr( $feature['src'] ); ?>" />
				</div>
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
					<h4><?php echo $feature['heading']; ?></h4>
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
