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

if ( current_user_can( 'install_plugins' ) ) {
	add_thickbox();
	wp_enqueue_script( 'plugin-install' );
}

if ( current_user_can( 'customize' ) ) {
	wp_enqueue_script( 'customize-loader' );
}

$video_url = 'https://videopress.com/embed/J44FHXvg?hd=true';
$locale    = str_replace( '_', '-', get_locale() );
list( $locale ) = explode( '-', $locale );
if ( 'en' !== $locale ) {
	$video_url = add_query_arg( 'defaultLangCode', $locale, $video_url );
}

wp_oembed_add_host_js();

$title = __( 'About' );

list( $display_version ) = explode( '-', $wp_version );

include( ABSPATH . 'wp-admin/admin-header.php' );
?>
	<div class="wrap about-wrap">
		<h1><?php printf( __( 'Welcome to WordPress&nbsp;%s' ), $display_version ); ?></h1>

		<div class="about-text"><?php printf( __( 'Thank you for updating! WordPress %s streamlines your workflow, whether you&#8217;re writing or building your site.' ), $display_version ); ?></div>
		<div class="wp-badge"><?php printf( __( 'Version %s' ), $display_version ); ?></div>

		<h2 class="nav-tab-wrapper wp-clearfix">
			<a href="about.php" class="nav-tab nav-tab-active"><?php _e( 'What&#8217;s New' ); ?></a>
			<a href="credits.php" class="nav-tab"><?php _e( 'Credits' ); ?></a>
			<a href="freedoms.php" class="nav-tab"><?php _e( 'Freedoms' ); ?></a>
		</h2>

		<div class="headline-feature feature-video" style="background-color:#191E23;">
			<p>Video Here</p>
		</div>

		<hr>

		<div class="feature-section two-col">
			<h2><?php _e( 'Editing Improvements' ); ?></h2>
			<div class="col">
				<img src="https://cldup.com/klO9vWGiT3.png" alt="" />
				<h3><?php _e( 'Inline Linking' ); ?></h3>
				<p><?php _e( 'Stay focused on your writing with a less distracting interface that keeps you in place and allows you to easily link to your content.' ); ?></p>
			</div>
			<div class="col">
				<img src="https://cldup.com/TE-OBMWHkX.png" alt="" />
				<h3><?php _e( 'Formatting Shortcuts' ); ?></h3>
				<p><?php _e( 'Do you enjoy using formatting shortcuts for lists and headings? Now they&#8217;re even more useful, with horizontal lines and <code>&lt;code&gt;</code>.' ); ?></p>
			</div>
		</div>

		<hr />

		<div class="feature-section two-col">
			<h2><?php _e( 'Customization Improvements' ); ?></h2>
			<div class="col">
				<img src="https://cldup.com/0iRJNVbt4G.png" alt="" />
				<h3><?php _e( 'Live Responsive Previews' ); ?></h3>
				<p><?php _e( 'Make sure your site looks great on all screens!' ); ?>
					<?php
					if ( current_user_can( 'customize' ) ) {
						$customize_url = admin_url( 'customize.php' );
						printf(
							/* translators: %s: URL to customizer */
							__( 'Preview mobile, tablet, and desktop views directly in the <a href="%s" class="load-customize">customizer</a>.' ),
							esc_url( $customize_url )
						);
					} else {
						_e( 'Preview mobile, tablet, and desktop views directly in the customizer.' );
					}
				?></p>
			</div>
			<div class="col">
				<img src="https://cldup.com/HWDA8xR_8G.png" alt="" />
				<h3><?php _e( 'Custom Logos' ); ?></h3>
				<p><?php _e( 'Themes can now support logos for your business or brand.' ); ?>
					<?php
					if ( current_theme_supports( 'custom-logo' ) && current_user_can( 'customize' ) ) {
						printf(
							/* translators: %s: URL to Site Identity section of the customizer */
							__( 'Your theme supports custom logos! Try it out right now in the <a href="%s" class="load-customize">Site Identity</a> section of the customizer.' ),
							esc_url( add_query_arg( array( 'autofocus' => array( 'section' => 'title_tagline' ) ), $customize_url ) )
						);
					} else {
						_e( 'The Twenty Fifteen and Twenty Sixteen themes have been updated to support custom logos, which can be found in the Site Identity section of the customizer.' );
					}
					?></p>
			</div>
		</div>

		<hr />

		<div class="changelog">
			<h2><?php _e( 'Under the Hood' ); ?></h2>

			<div class="under-the-hood three-col">
				<div class="col">
					<h3><?php _e( 'Smart Image Resizing' ); ?></h3>
					<p><?php
						printf(
							/* translators: %s: URL to the development post of the new feature */
							__( 'Generated images now load up to 50&#37; faster with no noticeable quality loss. <a href="%s">It&#8217;s really cool</a>.' ),
							'https://make.wordpress.org/core/2016/03/12/performance-improvements-for-images-in-wordpress-4-5/'
						);
					?></p>
				</div>
				<div class="col">
					<h3><?php _e( 'Selective Refresh' ); ?></h3>
					<p><?php
						printf(
							/* translators: %s: URL to the development post of the new feature */
							__( 'The customizer now supports a <a href="%s">comprehensive framework</a> for rendering parts of the preview without rewriting your PHP code in JavaScript.' ),
							'https://make.wordpress.org/core/2016/02/16/selective-refresh-in-the-customizer/'
						);
						if ( current_user_can( 'customize' ) && current_user_can( 'edit_theme_options' ) ) {
							if ( current_theme_supports( 'menus' ) && ! current_theme_supports( 'customize-selective-refresh-widgets' ) ) {
								printf(
									/* translators: %s: URL to Menus section of the customizer  */
									' ' . __( 'See it in action with <a href="%s" class="load-customize">Menus</a>.' ),
									esc_url( add_query_arg( array( 'autofocus' => array( 'panel' => 'nav_menus' ) ), $customize_url ) )
								);
							} elseif ( current_theme_supports( 'customize-selective-refresh-widgets' ) ) { // If widgets are supported, menus are also because of the menus widget.
								printf(
									/* translators: 1: URL to Menus section of the customizer, 2: URL to Widgets section of the customizer */
									' ' . __( 'See it in action with <a href="%1$s" class="load-customize">Menus</a> or <a href="%2$s" class="load-customize">Widgets</a>.' ),
									esc_url( add_query_arg( array( 'autofocus' => array( 'panel' => 'nav_menus' ) ), $customize_url ) ),
									esc_url( add_query_arg( array( 'autofocus' => array( 'panel' => 'widgets' ) ), $customize_url ) )
								);
							}
						}
					?></p>
				</div>
				<div class="col">
					<h3><?php _e( 'JavaScript Library Updates' ); ?></h3>
					<p><?php _e( 'jQuery 1.12.3, jQuery Migrate 1.4.0, Backbone 1.2.3, and Underscore 1.8.3 are bundled.' ); ?></p>
				</div>
			</div>

			<div class="under-the-hood two-col">
				<div class="col">
					<h3><?php _e( 'Script Loader Improvements' ); ?></h3>
					<p><?php
						printf(
							/* translators: %s: wp_add_inline_script() */
							__( 'Better support has been added for script header/footer dependencies. New %s enables adding extra code to registered scripts.' ),
							'<code><a href="https://make.wordpress.org/core/2016/03/08/enhanced-script-loader-in-wordpress-4-5/">wp_add_inline_script()</a></code>'
						);
					?></p>
				</div>
				<div class="col">
					<h3><?php _e( 'Better Embed Templates' ); ?></h3>
					<p><?php
						printf(
							/* translators: %s: URL to the development post of the new feature */
							__( 'Embed templates have been split into parts and can be <a href="%s">directly overridden by themes</a> via the template hierarchy.' ),
							'https://make.wordpress.org/core/2016/03/11/embeds-changes-in-wordpress-4-5/'
						);
					?></p>
				</div>
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

__( 'Maintenance Release' );
__( 'Maintenance Releases' );

__( 'Security Release' );
__( 'Security Releases' );

__( 'Maintenance and Security Release' );
__( 'Maintenance and Security Releases' );

/* translators: %s: WordPress version number */
__( '<strong>Version %s</strong> addressed one security issue.' );
/* translators: %s: WordPress version number */
__( '<strong>Version %s</strong> addressed some security issues.' );

/* translators: 1: WordPress version number, 2: plural number of bugs. */
_n_noop( '<strong>Version %1$s</strong> addressed %2$s bug.',
         '<strong>Version %1$s</strong> addressed %2$s bugs.' );

/* translators: 1: WordPress version number, 2: plural number of bugs. Singular security issue. */
_n_noop( '<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bug.',
         '<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bugs.' );

/* translators: 1: WordPress version number, 2: plural number of bugs. More than one security issue. */
_n_noop( '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
         '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.' );

/* translators: %s: Codex URL */
__( 'For more information, see <a href="%s">the release notes</a>.' );
