<?php
/**
 * About This Version administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

if ( ! wp_is_mobile() ) {
	wp_enqueue_style( 'wp-mediaelement' );
	wp_enqueue_script( 'wp-mediaelement' );
	wp_localize_script( 'mediaelement', '_wpmejsSettings', array(
		'pluginPath'        => includes_url( 'js/mediaelement/', 'relative' ),
		'pauseOtherPlayers' => '',
	) );
}

/**
 * Replaces the height and width attributes with values for full size.
 *
 * wp_video_shortcode() limits the width to 640px.
 *
 * @since 4.6.0
 * @ignore
 *
 * @param $output Video shortcode HTML output.
 * @return string Filtered HTML content to display video.
 */
function _wp_override_admin_video_width_limit( $output ) {
	return str_replace( array( '640', '384' ), array( '1050', '630' ), $output );
}

$video_url = 'https://videopress.com/embed/GbdhpGF3?hd=true';
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

		<p class="about-text"><?php printf( __( 'Thank you for updating to the latest version. WordPress %s changes a lot behind the scenes to make your WordPress experience even better!' ), $display_version ); ?></p>
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
					'4.6.18'
				);
				?>
				<?php
				printf(
					/* translators: %s: HelpHub URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					sprintf(
					/* translators: %s: WordPress version */
						esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
						sanitize_title( '4.6.18' )
					)
				);
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: %s: WordPress version number */
					__( '<strong>Version %s</strong> addressed one security issue.' ),
					'4.6.17'
				);
				?>
				<?php
				printf(
					/* translators: %s: HelpHub URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					sprintf(
					/* translators: %s: WordPress version */
						esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
						sanitize_title( '4.6.17' )
					)
				);
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: %s: WordPress version number */
					__( '<strong>Version %s</strong> addressed some security issues.' ),
					'4.6.16'
				);
				?>
				<?php
				printf(
					/* translators: %s: HelpHub URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					sprintf(
					/* translators: %s: WordPress version */
						esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
						sanitize_title( '4.6.16' )
					)
				);
				?>
			</p>
			<p>
				<?php
				printf(
				/* translators: %s: WordPress version number */
					__( '<strong>Version %s</strong> addressed some security issues.' ),
					'4.6.15'
				);
				?>
				<?php
				printf(
				/* translators: %s: HelpHub URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					sprintf(
					/* translators: %s: WordPress version */
						esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
						sanitize_title( '4.6.15' )
					)
				);
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: %s: WordPress version number */
					__( '<strong>Version %s</strong> addressed some security issues.' ),
					'4.6.14'
				);
				?>
				<?php
				printf(
					/* translators: %s: HelpHub URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					sprintf(
						/* translators: %s: WordPress version */
						esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
						sanitize_title( '4.6.14' )
					)
				);
				?>
			</p>
			<p>
				<?php
				/* translators: %s: WordPress version number */
				printf( __( '<strong>Version %s</strong> addressed some security issues.' ), '4.6.13' );
				?>
				<?php
				/* translators: %s: Codex URL */
				printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.6.13' );
				?>
			</p>
			<p><?php printf( __( '<strong>Version %s</strong> addressed one security issue.' ), '4.6.12' ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.6.12' ); ?>
			</p>
			<p><?php printf( __( '<strong>Version %s</strong> addressed some security issues.' ), '4.6.11' ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.6.11' ); ?>
			</p>
			<p><?php printf( __( '<strong>Version %s</strong> addressed one security issue.' ), '4.6.10' ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.6.10' ); ?>
			</p>
			<p><?php printf( __( '<strong>Version %s</strong> addressed some security issues.' ), '4.6.9' ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.6.9' ); ?>
			</p>
			<p><?php printf( __( '<strong>Version %s</strong> addressed one security issue.' ), '4.6.8' ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.6.8' ); ?>
			</p>
			<p><?php printf( __( '<strong>Version %s</strong> addressed some security issues.' ), '4.6.7' ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.6.7' ); ?>
			</p>
			<p><?php printf( __( '<strong>Version %s</strong> addressed some security issues.' ), '4.6.6' ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.6.6' ); ?>
			</p>
			<p><?php printf( _n( '<strong>Version %1$s</strong> addressed %2$s bug.',
					'<strong>Version %1$s</strong> addressed %2$s bugs.', 1 ), '4.6.5', number_format_i18n( 1 ) ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.6.5' ); ?>
			</p>
			<p><?php printf( __( '<strong>Version %s</strong> addressed some security issues.' ), '4.6.4' ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.6.4' ); ?>
			</p>
			<p><?php printf( __( '<strong>Version %s</strong> addressed some security issues.' ), '4.6.3' ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.6.3' ); ?>
			</p>
			<p><?php printf( __( '<strong>Version %s</strong> addressed some security issues.' ), '4.6.2' ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.6.2' ); ?>
			</p>
			<p><?php printf( _n( '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
				'<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.', 15 ), '4.6.1', number_format_i18n( 15 ) ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.6.1' ); ?>
			</p>
		</div>

		<div class="headline-feature feature-video">
			<iframe width="1050" height="591" src="<?php echo esc_url( $video_url ); ?>" frameborder="0" allowfullscreen></iframe>
			<script src="https://videopress.com/videopress-iframe.js"></script>
		</div>

		<hr>

		<div class="streamlined-updates feature-section one-col">
			<h2><?php _e( 'Streamlined Updates' ); ?></h2>
			<p><?php _e( 'Don&#8217;t lose your place: stay on the same page while you update, install, and delete your plugins and themes.' ); ?></p>
			<?php
			if ( ! wp_is_mobile() ) {
				add_filter( 'wp_video_shortcode', '_wp_override_admin_video_width_limit' );
				echo wp_video_shortcode( array(
					'mp4'      => 'https://s.w.org/images/core/4.6/streamlined-updates.mp4',
					'webm'     => 'https://s.w.org/images/core/4.6/streamlined-updates.webm',
					'poster'   => 'https://s.w.org/images/core/4.6/streamlined-updates-2000.png?v1',
					'loop'     => true,
					'autoplay' => true,
					'width'    => 1050,
					'height'   => 630,
					'class'    => 'wp-video-shortcode feature-video',
				) );
				remove_filter( 'wp_video_shortcode', '_wp_override_admin_video_width_limit' );
			} else {
				echo '<img src="https://s.w.org/images/core/4.6/streamlined-updates-1057.png?v1" alt="" srcset="https://s.w.org/images/core/4.6/streamlined-updates-1664.png?v1 1664w, https://s.w.org/images/core/4.6/streamlined-updates-200.png?v1 200w, https://s.w.org/images/core/4.6/streamlined-updates-1057.png?v1 1057w, https://s.w.org/images/core/4.6/streamlined-updates-2000.png?v1 2000w"  sizes="(max-width: 500px) calc(100vw - 40px), (max-width: 782px) calc(100vw - 70px), (max-width: 959px) calc(100vw - 116px), (max-width: 1290px) calc(100vw - 240px), 1050px" />';
			}
			?>
		</div>

		<hr />

		<div class="native-fonts feature-section one-col">
			<h2><?php _e( 'Native Fonts' ); ?></h2>
			<p><?php _e( 'The WordPress dashboard now takes advantage of the fonts you already have, making it load faster and letting you feel more at home on whatever device you use.' ); ?></p>
			<img src="https://s.w.org/images/core/4.6/native-fonts-992.png?v1" alt="" srcset="https://cldup.com/Hqmo5VLb-E.png?v1 922w, https://s.w.org/images/core/4.6/native-fonts-200.png?v1 200w,https://s.w.org/images/core/4.6/native-fonts-371.png?v1 371w,https://s.w.org/images/core/4.6/native-fonts-510.png?v1 510w, https://s.w.org/images/core/4.6/native-fonts-560.png?v1 560w, https://s.w.org/images/core/4.6/native-fonts-781.png?v1 781w, https://s.w.org/images/core/4.6/native-fonts-2000.png?v1 2000w" sizes="(max-width: 500px) calc(100vw - 40px), (max-width: 782px) calc(100vw - 70px), (max-width: 959px) calc(100vw - 116px), (max-width: 1290px) calc(100vw - 240px), 1050px"/>
		</div>

		<hr />

		<div class="feature-section two-col">
			<h2><?php _e( 'Editor Improvements' ); ?></h2>
			<div class="col">
				<img src="https://s.w.org/images/core/4.6/inline-link-checker-608.png?v1" alt="" srcset="https://s.w.org/images/core/4.6/inline-link-checker-789.png?v1 789w, https://s.w.org/images/core/4.6/inline-link-checker-200.png?v1 200w, https://s.w.org/images/core/4.6/inline-link-checker-384.png?v1 384w, https://s.w.org/images/core/4.6/inline-link-checker-608.png?v1 608w, https://s.w.org/images/core/4.6/inline-link-checker-992.png?v1 992w" sizes="(max-width: 500px) calc(100vw - 40px), (max-width: 781px) calc((100vw - 70px) * .466), (max-width: 959px) calc((100vw - 116px) * .469), (max-width: 1290px) calc((100vw - 240px) * .472), 496px"/>
				<h3><?php _e( 'Inline Link Checker' ); ?></h3>
				<p><?php
					printf(
						/* translators: %s: Home URL appended with 'wordpress.org'  */
						__( 'Ever accidentally made a link to %s? Now WordPress automatically checks to make sure you didn&#8217;t.' ),
						home_url( 'wordpress.org' )
					);
				?></p>
			</div>
			<div class="col">
				<img src="https://s.w.org/images/core/4.6/content-recovery-561.png?v1" alt="" srcset="https://s.w.org/images/core/4.6/content-recovery-701.png?v1 701w, https://s.w.org/images/core/4.6/content-recovery-200.png?v1 200w, https://s.w.org/images/core/4.6/content-recovery-400.png?v1 400w, https://s.w.org/images/core/4.6/content-recovery-561.png?v1 561w, https://s.w.org/images/core/4.6/content-recovery-992.png?v1 992w" sizes="(max-width: 500px) calc(100vw - 40px), (max-width: 781px) calc((100vw - 70px) * .466), (max-width: 959px) calc((100vw - 116px) * .469), (max-width: 1290px) calc((100vw - 240px) * .472), 496px"/>
				<h3><?php _e( 'Content Recovery' ); ?></h3>
				<p><?php _e( 'As you type, WordPress saves your content to the browser. Recovering saved content is even easier with WordPress 4.6.' ); ?></p>
			</div>
		</div>

		<hr />

		<div class="changelog">
			<h2><?php _e( 'Under the Hood' ); ?></h2>

			<div class="under-the-hood three-col">
				<div class="col">
					<h3><?php _e( 'Resource Hints' ); ?></h3>
					<p><?php
						printf(
							/* translators: %s: https://make.wordpress.org/core/2016/07/06/resource-hints-in-4-6/ */
							__( '<a href="%s">Resource hints help browsers</a> decide which resources to fetch and preprocess. WordPress 4.6 adds them automatically for your styles and scripts making your site even faster.' ),
							'https://make.wordpress.org/core/2016/07/06/resource-hints-in-4-6/'
						);
					?></p>
				</div>
				<div class="col">
					<h3><?php _e( 'Robust Requests' ); ?></h3>
					<p><?php _e( 'The HTTP API now leverages the Requests library, improving HTTP standard support and adding case-insensitive headers, parallel HTTP requests, and support for Internationalized Domain Names.' ); ?></p>
				</div>
				<div class="col">
					<h3><?php
						/* translators: 1: WP_Term_Query, 2: WP_Post_Type */
						printf( __( '%1$s and %2$s' ), '<code>WP_Term_Query</code>', '<code>WP_Post_Type</code>' );
					?></h3>
					<p><?php
						printf(
							/* translators: 1: WP_Term_Query, 2: WP_Post_Type */
							__( 'A new %1$s class adds flexibility to query term information while a new %2$s object makes interacting with post types more predictable.' ),
							'<code>WP_Term_Query</code>',
							'<code>WP_Post_Type</code>'
						);
					?></p>
				</div>
			</div>

			<div class="under-the-hood three-col">
				<div class="col">
					<h3><?php _e( 'Meta Registration API' ); ?></h3>
					<p><?php
						printf(
							/* translators: %s: https://make.wordpress.org/core/2016/07/08/enhancing-register_meta-in-4-6/  */
							__( 'The Meta Registration API <a href="%s">has been expanded</a> to support types, descriptions, and REST API visibility.' ),
							'https://make.wordpress.org/core/2016/07/08/enhancing-register_meta-in-4-6/'
						);
					?></p>
				</div>
				<div class="col">
					<h3><?php _e( 'Translations On Demand' ); ?></h3>
					<p><?php _e( 'WordPress will install and use the newest language packs for your plugins and themes as soon as they&#8217;re available from <a href="https://translate.wordpress.org/">WordPress.org&#8217;s community of translators</a>.' ); ?></p>
				</div>
				<div class="col">
					<h3><?php _e( 'JavaScript Library Updates' ); ?></h3>
					<p><?php _e( 'Masonry 3.3.2, imagesLoaded 3.2.0, MediaElement.js 2.22.0, TinyMCE 4.4.1, and Backbone.js 1.3.3 are bundled.' ); ?></p>
				</div>
			</div>

			<div class="under-the-hood two-col">
				<div class="col">
					<h3><?php _e( 'Customizer APIs for Setting Validation and Notifications' ); ?></h3>
					<p><?php _e( 'Settings now have an <a href="https://make.wordpress.org/core/2016/07/05/customizer-apis-in-4-6-for-setting-validation-and-notifications/">API for enforcing validation constraints</a>. Likewise, customizer controls now support notifications, which are used to display validation errors instead of failing silently.' ); ?></p>
				</div>
				<div class="col">
					<h3><?php _e( 'Multisite, now faster than ever' ); ?></h3>
					<p><?php
						printf(
							/* translators: 1: WP_Site_Query, 2: WP_Network_Query */
							__( 'Cached and comprehensive site queries improve your network admin experience. The addition of %1$s and %2$s help craft advanced queries with less effort.' ),
							'<code>WP_Site_Query</code>',
							'<code>WP_Network_Query</code>'
						);
					?></p>
				</div>
			</div>
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
