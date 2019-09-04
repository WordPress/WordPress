<?php
/**
 * About This Version administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

if ( current_user_can( 'customize' ) ) {
	wp_enqueue_script( 'customize-loader' );
}

$video_url = 'https://videopress.com/embed/scFdjVo6?hd=true';
$locale    = str_replace( '_', '-', get_locale() );
list( $locale ) = explode( '-', $locale );
if ( 'en' !== $locale ) {
	$video_url = add_query_arg( 'defaultLangCode', $locale, $video_url );
}

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

		<div class="changelog point-releases">
			<h3><?php _e( 'Maintenance and Security Releases' ); ?></h3>
			<p>
				<?php
				printf(
					/* translators: %s: WordPress version number */
					__( '<strong>Version %s</strong> addressed some security issues.' ),
					'4.5.18'
				);
				?>
				<?php
				printf(
					/* translators: %s: HelpHub URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					sprintf(
						/* translators: %s: WordPress version */
						esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
						sanitize_title( '4.5.18' )
					)
				);
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: %s: WordPress version number */
					__( '<strong>Version %s</strong> addressed some security issues.' ),
					'4.5.17'
				);
				?>
				<?php
				printf(
					/* translators: %s: HelpHub URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					sprintf(
						/* translators: %s: WordPress version */
						esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
						sanitize_title( '4.5.17' )
					)
				);
				?>
			</p>
			<p>
				<?php
				/* translators: %s: WordPress version number */
				printf( __( '<strong>Version %s</strong> addressed some security issues.' ), '4.5.16' );
				?>
				<?php
				/* translators: %s: Codex URL */
				printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.5.16' );
				?>
			</p>
			<p><?php printf( __( '<strong>Version %s</strong> addressed one security issue.' ), '4.5.15' ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.5.15' ); ?>
			</p>
			<p><?php printf( __( '<strong>Version %s</strong> addressed some security issues.' ), '4.5.14' ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.5.14' ); ?>
			</p>
			<p><?php printf( __( '<strong>Version %s</strong> addressed one security issue.' ), '4.5.13' ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.5.13' ); ?>
			</p>
			<p><?php printf( __( '<strong>Version %s</strong> addressed some security issues.' ), '4.5.12' ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.5.12' ); ?>
			</p>
			<p><?php printf( __( '<strong>Version %s</strong> addressed one security issue.' ), '4.5.11' ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.5.11' ); ?>
			</p>
			<p><?php printf( __( '<strong>Version %s</strong> addressed some security issues.' ), '4.5.10' ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.5.10' ); ?>
			</p>
			<p><?php printf( __( '<strong>Version %s</strong> addressed some security issues.' ), '4.5.9' ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.5.9' ); ?>
			</p>
			<p><?php printf( _n( '<strong>Version %1$s</strong> addressed %2$s bug.',
					'<strong>Version %1$s</strong> addressed %2$s bugs.', 1 ), '4.5.8', number_format_i18n( 1 ) ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.5.8' ); ?>
			</p>
			<p><?php printf( __( '<strong>Version %s</strong> addressed some security issues.' ), '4.5.7' ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.5.7' ); ?>
			</p>
			<p><?php printf( __( '<strong>Version %s</strong> addressed some security issues.' ), '4.5.6' ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.5.6' ); ?>
			</p>
			<p><?php printf( __( '<strong>Version %s</strong> addressed some security issues.' ), '4.5.5' ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.5.5' ); ?>
			</p>
			<p><?php printf( __( '<strong>Version %s</strong> addressed some security issues.' ), '4.5.4' ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.5.4' ); ?>
			</p>
			<p><?php printf( _n( '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
				'<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.', 17 ), '4.5.3', number_format_i18n( 17 ) ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.5.3' ); ?>
			</p>
			<p><?php printf( __( '<strong>Version %s</strong> addressed some security issues.' ), '4.5.2' ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.5.2' ); ?>
			</p>
			<p><?php printf( _n( '<strong>Version %1$s</strong> addressed %2$s bug.',
				'<strong>Version %1$s</strong> addressed %2$s bugs.', 12 ), '4.5.1', number_format_i18n( 12 ) ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.5.1' ); ?>
			</p>
		</div>

		<div class="headline-feature feature-video" style="background-color:#191E23;">
			<iframe width="1050" height="591" src="<?php echo esc_url( $video_url ); ?>" frameborder="0" allowfullscreen></iframe>
			<script src="https://videopress.com/videopress-iframe.js"></script>
		</div>

		<hr>

		<div class="feature-section two-col">
			<h2><?php _e( 'Editing Improvements' ); ?></h2>
			<div class="col">
				<img src="https://s.w.org/images/core/4.5/link-edit-560.png" alt="" srcset="https://s.w.org/images/core/4.5/link-edit-1000.png 1000w, https://s.w.org/images/core/4.5/link-edit-800.png 800w, https://s.w.org/images/core/4.5/link-edit-680.png 680w, https://s.w.org/images/core/4.5/link-edit-560.png 560w, https://s.w.org/images/core/4.5/link-edit-400.png 400w, https://s.w.org/images/core/4.5/link-edit-280.png 280w" sizes="(max-width: 500px) calc(100vw - 40px), (max-width: 781px) calc((100vw - 70px) * .466), (max-width: 959px) calc((100vw - 116px) * .469), (max-width: 1290px) calc((100vw - 240px) * .472), 496px"/>
				<h3><?php _e( 'Inline Linking' ); ?></h3>
				<p><?php _e( 'Stay focused on your writing with a less distracting interface that keeps you in place and allows you to easily link to your content.' ); ?></p>
			</div>
			<div class="col">
				<img src="https://s.w.org/images/core/4.5/formatting-560.png" alt="" srcset="https://s.w.org/images/core/4.5/formatting-1000.png 1000w, https://s.w.org/images/core/4.5/formatting-800.png 800w, https://s.w.org/images/core/4.5/formatting-680.png 680w, https://s.w.org/images/core/4.5/formatting-560.png 560w, https://s.w.org/images/core/4.5/formatting-400.png 400w, https://s.w.org/images/core/4.5/formatting-280.png 280w" sizes="(max-width: 500px) calc(100vw - 40px), (max-width: 781px) calc((100vw - 70px) * .466), (max-width: 959px) calc((100vw - 116px) * .469), (max-width: 1290px) calc((100vw - 240px) * .472), 496px"/>
				<h3><?php _e( 'Formatting Shortcuts' ); ?></h3>
				<p><?php _e( 'Do you enjoy using formatting shortcuts for lists and headings? Now they&#8217;re even more useful, with horizontal lines and <code>&lt;code&gt;</code>.' ); ?></p>
			</div>
		</div>

		<hr />

		<div class="feature-section two-col">
			<h2><?php _e( 'Customization Improvements' ); ?></h2>
			<div class="col">
				<img src="https://s.w.org/images/core/4.5/preview-icons-560.png" alt="" srcset="https://s.w.org/images/core/4.5/preview-icons-1000.png 1000w, https://s.w.org/images/core/4.5/preview-icons-800.png 800w, https://s.w.org/images/core/4.5/preview-icons-680.png 680w, https://s.w.org/images/core/4.5/preview-icons-560.png 560w, https://s.w.org/images/core/4.5/preview-icons-400.png 400w, https://s.w.org/images/core/4.5/preview-icons-280.png 280w" sizes="(max-width: 500px) calc(100vw - 40px), (max-width: 781px) calc((100vw - 70px) * .466), (max-width: 959px) calc((100vw - 116px) * .469), (max-width: 1290px) calc((100vw - 240px) * .472), 496px"/>
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
				<img src="https://s.w.org/images/core/4.5/custom-logos-560.png" alt="" srcset="https://s.w.org/images/core/4.5/custom-logos-1000.png 1000w, https://s.w.org/images/core/4.5/custom-logos-800.png 800w, https://s.w.org/images/core/4.5/custom-logos-680.png 680w, https://s.w.org/images/core/4.5/custom-logos-560.png 560w, https://s.w.org/images/core/4.5/custom-logos-400.png 400w, https://s.w.org/images/core/4.5/custom-logos-280.png 280w" sizes="(max-width: 500px) calc(100vw - 40px), (max-width: 781px) calc((100vw - 70px) * .466), (max-width: 959px) calc((100vw - 116px) * .469), (max-width: 1290px) calc((100vw - 240px) * .472), 496px"/>
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
