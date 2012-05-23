<?php
/**
 * Customize Controls
 *
 * @package WordPress
 * @subpackage Customize
 * @since 3.4.0
 */

require_once( './admin.php' );
if ( ! current_user_can( 'edit_theme_options' ) )
	die( 'Cap check failed' );

global $wp_scripts, $wp_customize;

if ( ! $wp_customize->is_preview() )
	die( 'is_preview() failed' );

wp_reset_vars( array( 'theme' ) );

if ( ! $theme )
	$theme = get_stylesheet();

$registered = $wp_scripts->registered;
$wp_scripts = new WP_Scripts;
$wp_scripts->registered = $registered;

add_action( 'customize_controls_print_scripts',        'print_head_scripts', 20 );
add_action( 'customize_controls_print_footer_scripts', '_wp_footer_scripts'     );
add_action( 'customize_controls_print_styles',         'print_admin_styles', 20 );

do_action( 'customize_controls_init' );

wp_enqueue_script( 'customize-controls' );
wp_enqueue_style( 'customize-controls' );

do_action( 'customize_controls_enqueue_scripts' );

// Let's roll.
@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));

wp_user_settings();
_wp_admin_html_begin();

$admin_title = sprintf( __( '%1$s &#8212; WordPress' ), strip_tags( sprintf( __( 'Customize %s' ), $wp_customize->theme()->display('Name') ) ) );
?><title><?php echo $admin_title; ?></title><?php

do_action( 'customize_controls_print_styles' );
do_action( 'customize_controls_print_scripts' );
?>
</head>
<body class="wp-full-overlay">
	<form id="customize-controls" class="wrap wp-full-overlay-sidebar">
		<?php wp_nonce_field( 'customize_controls' ); ?>
		<div id="customize-header-actions" class="wp-full-overlay-header">
			<a class="back" href="<?php echo esc_url( admin_url( 'themes.php' ) ); ?>">
				<?php printf( __( '&larr; Return to %s' ), __('Manage Themes') ); ?>
			</a>
		</div>

		<div class="wp-full-overlay-sidebar-content">
			<div id="customize-info" class="customize-section">
				<div class="customize-section-title">
					<span class="preview-notice"><?php _e('You are previewing'); ?></span>
					<strong class="theme-name"><?php echo $wp_customize->theme()->display('Name'); ?></strong>
				</div>
				<div class="customize-section-content">
					<?php if ( $screenshot = $wp_customize->theme()->get_screenshot() ) : ?>
						<img class="theme-screenshot" src="<?php echo esc_url( $screenshot ); ?>" />
					<?php endif; ?>

					<?php if ( $wp_customize->theme()->get('Description') ): ?>
						<div class="theme-description"><?php echo $wp_customize->theme()->display('Description'); ?></div>
					<?php endif; ?>
				</div>
			</div>

			<div id="customize-theme-controls"><ul>
				<?php
				foreach ( $wp_customize->sections() as $section )
					$section->maybe_render();
				?>
			</ul></div>
		</div>

		<div id="customize-footer-actions" class="wp-full-overlay-footer">
			<?php
			$save_text = $wp_customize->is_theme_active() ? __('Save') : __('Save and Activate');
			submit_button( $save_text, 'primary', 'save', false );
			?>
			<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" />

			<a href="#" class="collapse-sidebar button-secondary" title="<?php esc_attr_e('Collapse Sidebar'); ?>">
				<span class="collapse-sidebar-label"><?php _e('Collapse'); ?></span>
				<span class="collapse-sidebar-arrow"></span>
			</a>
		</div>
	</form>
	<div id="customize-preview" class="wp-full-overlay-main"></div>
	<?php

	do_action( 'customize_controls_print_footer_scripts' );

	// If the frontend and the admin are served from the same domain, load the
	// preview over ssl if the customizer is being loaded over ssl. This avoids
	// insecure content warnings. This is not attempted if the admin and frontend
	// are on different domains to avoid the case where the frontend doesn't have
	// ssl certs. Domain mapping plugins can force ssl in these conditions using
	// the customize_preview_link filter.
	$admin_origin = parse_url( admin_url() );
	$home_origin = parse_url( home_url() );
	$scheme = null;
	if ( is_ssl() && ( $admin_origin[ 'host' ] == $home_origin[ 'host' ] ) )
		$scheme = 'https';

	$preview_url = apply_filters( 'customize_preview_link',  home_url( '/', $scheme ) );

	$settings = array(
		'theme'    => array(
			'stylesheet' => $wp_customize->get_stylesheet(),
			'active'     => $wp_customize->is_theme_active(),
		),
		'url'      => array(
			'preview'  => esc_url( $preview_url ),
			'parent'   => esc_url( admin_url() ),
			'ajax'     => esc_url( admin_url( 'admin-ajax.php', 'relative' ) ),
		),
		'settings' => array(),
		'controls' => array(),
	);

	foreach ( $wp_customize->settings() as $id => $setting ) {
		$settings['settings'][ $id ] = array(
			'value'     => $setting->js_value(),
			'transport' => $setting->transport,
		);
	}

	foreach ( $wp_customize->controls() as $id => $control ) {
		$control->to_json();
		$settings['controls'][ $id ] = $control->json;
	}

	?>
	<script type="text/javascript">
		var _wpCustomizeSettings = <?php echo json_encode( $settings ); ?>;
	</script>
</body>
</html>
