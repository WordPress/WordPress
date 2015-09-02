<?php
/**
 * Theme Customize Screen.
 *
 * @package WordPress
 * @subpackage Customize
 * @since 3.4.0
 */

define( 'IFRAME_REQUEST', true );

/** Load WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

if ( ! current_user_can( 'customize' ) ) {
	wp_die(
		'<h1>' . __( 'Cheatin&#8217; uh?' ) . '</h1>' .
		'<p>' . __( 'You are not allowed to customize the appearance of this site.' ) . '</p>',
		403
	);
}

wp_reset_vars( array( 'url', 'return' ) );
$url = wp_unslash( $url );
$url = wp_validate_redirect( $url, home_url( '/' ) );
if ( $return ) {
	$return = wp_unslash( $return );
	$return = remove_query_arg( wp_removable_query_args(), $return );
	$return = wp_validate_redirect( $return );
}
if ( ! $return ) {
	if ( $url ) {
		$return = $url;
	} elseif ( current_user_can( 'edit_theme_options' ) || current_user_can( 'switch_themes' ) ) {
		$return = admin_url( 'themes.php' );
	} else {
		$return = admin_url();
	}
}

/**
 * @global WP_Scripts           $wp_scripts
 * @global WP_Customize_Manager $wp_customize
 */
global $wp_scripts, $wp_customize;

$registered = $wp_scripts->registered;
$wp_scripts = new WP_Scripts;
$wp_scripts->registered = $registered;

add_action( 'customize_controls_print_scripts',        'print_head_scripts', 20 );
add_action( 'customize_controls_print_footer_scripts', '_wp_footer_scripts'     );
add_action( 'customize_controls_print_styles',         'print_admin_styles', 20 );

/**
 * Fires when Customizer controls are initialized, before scripts are enqueued.
 *
 * @since 3.4.0
 */
do_action( 'customize_controls_init' );

wp_enqueue_script( 'customize-controls' );
wp_enqueue_style( 'customize-controls' );

/**
 * Enqueue Customizer control scripts.
 *
 * @since 3.4.0
 */
do_action( 'customize_controls_enqueue_scripts' );

// Let's roll.
@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));

wp_user_settings();
_wp_admin_html_begin();

$body_class = 'wp-core-ui wp-customizer js';

if ( wp_is_mobile() ) :
	$body_class .= ' mobile';

	?><meta name="viewport" id="viewport-meta" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=1.2" /><?php
endif;

$is_ios = wp_is_mobile() && preg_match( '/iPad|iPod|iPhone/', $_SERVER['HTTP_USER_AGENT'] );

if ( $is_ios ) {
	$body_class .= ' ios';
}

if ( is_rtl() ) {
	$body_class .= ' rtl';
}
$body_class .= ' locale-' . sanitize_html_class( strtolower( str_replace( '_', '-', get_locale() ) ) );

if ( $wp_customize->is_theme_active() ) {
	$document_title_tmpl = _x( 'Customize: %s', 'Placeholder is the document title from the preview' );
} else {
	$document_title_tmpl = _x( 'Live Preview: %s', 'Placeholder is the document title from the preview' );
}
$document_title_tmpl = html_entity_decode( $document_title_tmpl, ENT_QUOTES, 'UTF-8' ); // because exported to JS and assigned to document.title
$admin_title = sprintf( $document_title_tmpl, __( 'Loading&hellip;' ) );

?><title><?php echo $admin_title; ?></title>

<script type="text/javascript">
var ajaxurl = '<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>';
</script>

<?php
/**
 * Fires when Customizer control styles are printed.
 *
 * @since 3.4.0
 */
do_action( 'customize_controls_print_styles' );

/**
 * Fires when Customizer control scripts are printed.
 *
 * @since 3.4.0
 */
do_action( 'customize_controls_print_scripts' );
?>
</head>
<body class="<?php echo esc_attr( $body_class ); ?>">
<div class="wp-full-overlay expanded">
	<form id="customize-controls" class="wrap wp-full-overlay-sidebar">
		<div id="customize-header-actions" class="wp-full-overlay-header">
			<?php
			$save_text = $wp_customize->is_theme_active() ? __( 'Save &amp; Publish' ) : __( 'Save &amp; Activate' );
			submit_button( $save_text, 'primary save', 'save', false );
			?>
			<span class="spinner"></span>
			<a class="customize-controls-preview-toggle" href="#">
				<span class="controls"><?php _e( 'Customize' ); ?></span>
				<span class="preview"><?php _e( 'Preview' ); ?></span>
			</a>
			<a class="customize-controls-close" href="<?php echo esc_url( $return ); ?>">
				<span class="screen-reader-text"><?php _e( 'Cancel' ); ?></span>
			</a>
		</div>

		<div id="widgets-right"><!-- For Widget Customizer, many widgets try to look for instances under div#widgets-right, so we have to add that ID to a container div in the Customizer for compat -->
		<div class="wp-full-overlay-sidebar-content" tabindex="-1">
			<div id="customize-info" class="accordion-section customize-info">
				<div class="accordion-section-title" aria-label="<?php esc_attr_e( 'Customizer Options' ); ?>">
					<span class="preview-notice"><?php
						echo sprintf( __( 'You are customizing %s' ), '<strong class="panel-title site-title">' . get_bloginfo( 'name' ) . '</strong>' );
					?></span>
					<button class="customize-help-toggle dashicons dashicons-editor-help" aria-expanded="false"><span class="screen-reader-text"><?php _e( 'Help' ); ?></span></button>
				</div>
				<div class="customize-panel-description"><?php
					_e( 'The Customizer allows you to preview changes to your site before publishing them. You can also navigate to different pages on your site to preview them.' );
				?></div>
			</div>

			<div id="customize-theme-controls">
				<ul><?php // Panels and sections are managed here via JavaScript ?></ul>
			</div>
		</div>
		</div>

		<div id="customize-footer-actions" class="wp-full-overlay-footer">
			<button type="button" class="collapse-sidebar button-secondary" aria-expanded="true" aria-label="<?php esc_attr_e( 'Collapse Sidebar' ); ?>">
				<span class="collapse-sidebar-arrow"></span>
				<span class="collapse-sidebar-label"><?php _e( 'Collapse' ); ?></span>
			</button>
		</div>
	</form>
	<div id="customize-preview" class="wp-full-overlay-main"></div>
	<?php

	// Render Panel, Section, and Control templates.
	$wp_customize->render_panel_templates();
	$wp_customize->render_section_templates();
	$wp_customize->render_control_templates();

	/**
	 * Print Customizer control scripts in the footer.
	 *
	 * @since 3.4.0
	 */
	do_action( 'customize_controls_print_footer_scripts' );

	/*
	 * If the frontend and the admin are served from the same domain, load the
	 * preview over ssl if the Customizer is being loaded over ssl. This avoids
	 * insecure content warnings. This is not attempted if the admin and frontend
	 * are on different domains to avoid the case where the frontend doesn't have
	 * ssl certs. Domain mapping plugins can allow other urls in these conditions
	 * using the customize_allowed_urls filter.
	 */

	$allowed_urls = array( home_url('/') );
	$admin_origin = parse_url( admin_url() );
	$home_origin  = parse_url( home_url() );
	$cross_domain = ( strtolower( $admin_origin[ 'host' ] ) != strtolower( $home_origin[ 'host' ] ) );

	if ( is_ssl() && ! $cross_domain )
		$allowed_urls[] = home_url( '/', 'https' );

	/**
	 * Filter the list of URLs allowed to be clicked and followed in the Customizer preview.
	 *
	 * @since 3.4.0
	 *
	 * @param array $allowed_urls An array of allowed URLs.
	 */
	$allowed_urls = array_unique( apply_filters( 'customize_allowed_urls', $allowed_urls ) );

	$login_url = add_query_arg( array(
		'interim-login' => 1,
		'customize-login' => 1
	), wp_login_url() );

	// Prepare Customizer settings to pass to JavaScript.
	$settings = array(
		'theme'    => array(
			'stylesheet' => $wp_customize->get_stylesheet(),
			'active'     => $wp_customize->is_theme_active(),
		),
		'url'      => array(
			'preview'       => esc_url_raw( $url ? $url : home_url( '/' ) ),
			'parent'        => esc_url_raw( admin_url() ),
			'activated'     => esc_url_raw( home_url( '/' ) ),
			'ajax'          => esc_url_raw( admin_url( 'admin-ajax.php', 'relative' ) ),
			'allowed'       => array_map( 'esc_url_raw', $allowed_urls ),
			'isCrossDomain' => $cross_domain,
			'home'          => esc_url_raw( home_url( '/' ) ),
			'login'         => esc_url_raw( $login_url ),
		),
		'browser'  => array(
			'mobile' => wp_is_mobile(),
			'ios'    => $is_ios,
		),
		'settings' => array(),
		'controls' => array(),
		'panels'   => array(),
		'sections' => array(),
		'nonce'    => array(
			'save'    => wp_create_nonce( 'save-customize_' . $wp_customize->get_stylesheet() ),
			'preview' => wp_create_nonce( 'preview-customize_' . $wp_customize->get_stylesheet() )
		),
		'autofocus' => array(),
		'documentTitleTmpl' => $document_title_tmpl,
	);

	// Prepare Customize Setting objects to pass to JavaScript.
	foreach ( $wp_customize->settings() as $id => $setting ) {
		if ( $setting->check_capabilities() ) {
			$settings['settings'][ $id ] = array(
				'value'     => $setting->js_value(),
				'transport' => $setting->transport,
				'dirty'     => $setting->dirty,
			);
		}
	}

	// Prepare Customize Control objects to pass to JavaScript.
	foreach ( $wp_customize->controls() as $id => $control ) {
		if ( $control->check_capabilities() ) {
			$settings['controls'][ $id ] = $control->json();
		}
	}

	// Prepare Customize Section objects to pass to JavaScript.
	foreach ( $wp_customize->sections() as $id => $section ) {
		if ( $section->check_capabilities() ) {
			$settings['sections'][ $id ] = $section->json();
		}
	}

	// Prepare Customize Panel objects to pass to JavaScript.
	foreach ( $wp_customize->panels() as $panel_id => $panel ) {
		if ( $panel->check_capabilities() ) {
			$settings['panels'][ $panel_id ] = $panel->json();
			foreach ( $panel->sections as $section_id => $section ) {
				if ( $section->check_capabilities() ) {
					$settings['sections'][ $section_id ] = $section->json();
				}
			}
		}
	}

	// Pass to frontend the Customizer construct being deeplinked
	if ( isset( $_GET['autofocus'] ) ) {
		$autofocus = wp_unslash( $_GET['autofocus'] );
		if ( is_array( $autofocus ) ) {
			foreach ( $autofocus as $type => $id ) {
				if ( isset( $settings[ $type . 's' ][ $id ] ) ) {
					$settings['autofocus'][ $type ] = $id;
				}
			}
		}
	}

	?>
	<script type="text/javascript">
		var _wpCustomizeSettings = <?php echo wp_json_encode( $settings ); ?>;
	</script>
</div>
</body>
</html>
