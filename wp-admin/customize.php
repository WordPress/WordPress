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
		'<p>' . __( 'Sorry, you are not allowed to customize this site.' ) . '</p>',
		403
	);
}

wp_reset_vars( array( 'url', 'return', 'autofocus' ) );
if ( ! empty( $url ) ) {
	$wp_customize->set_preview_url( wp_unslash( $url ) );
}
if ( ! empty( $return ) ) {
	$wp_customize->set_return_url( wp_unslash( $return ) );
}
if ( ! empty( $autofocus ) && is_array( $autofocus ) ) {
	$wp_customize->set_autofocus( wp_unslash( $autofocus ) );
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

if ( $wp_customize->is_ios() ) {
	$body_class .= ' ios';
}

if ( is_rtl() ) {
	$body_class .= ' rtl';
}
$body_class .= ' locale-' . sanitize_html_class( strtolower( str_replace( '_', '-', get_locale() ) ) );

$admin_title = sprintf( $wp_customize->get_document_title_template(), __( 'Loading&hellip;' ) );

?><title><?php echo $admin_title; ?></title>

<script type="text/javascript">
var ajaxurl = <?php echo wp_json_encode( admin_url( 'admin-ajax.php', 'relative' ) ); ?>;
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
			<button type="button" class="customize-controls-preview-toggle">
				<span class="controls"><?php _e( 'Customize' ); ?></span>
				<span class="preview"><?php _e( 'Preview' ); ?></span>
			</button>
			<a class="customize-controls-close" href="<?php echo esc_url( $wp_customize->get_return_url() ); ?>">
				<span class="screen-reader-text"><?php _e( 'Close the Customizer and go back to the previous page' ); ?></span>
			</a>
		</div>

		<div id="widgets-right" class="wp-clearfix"><!-- For Widget Customizer, many widgets try to look for instances under div#widgets-right, so we have to add that ID to a container div in the Customizer for compat -->
		<div class="wp-full-overlay-sidebar-content" tabindex="-1">
			<div id="customize-info" class="accordion-section customize-info">
				<div class="accordion-section-title">
					<span class="preview-notice"><?php
						echo sprintf( __( 'You are customizing %s' ), '<strong class="panel-title site-title">' . get_bloginfo( 'name', 'display' ) . '</strong>' );
					?></span>
					<button type="button" class="customize-help-toggle dashicons dashicons-editor-help" aria-expanded="false"><span class="screen-reader-text"><?php _e( 'Help' ); ?></span></button>
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
			<?php $previewable_devices = $wp_customize->get_previewable_devices(); ?>
			<?php if ( ! empty( $previewable_devices ) ) : ?>
			<div class="devices">
				<?php foreach ( (array) $previewable_devices as $device => $settings ) : ?>
					<?php
					if ( empty( $settings['label'] ) ) {
						continue;
					}
					$active = ! empty( $settings['default'] );
					$class = 'preview-' . $device;
					if ( $active ) {
						$class .= ' active';
					}
					?>
					<button type="button" class="<?php echo esc_attr( $class ); ?>" aria-pressed="<?php echo esc_attr( $active ) ?>" data-device="<?php echo esc_attr( $device ); ?>">
						<span class="screen-reader-text"><?php echo esc_html( $settings['label'] ); ?></span>
					</button>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
			<button type="button" class="collapse-sidebar button-secondary" aria-expanded="true" aria-label="<?php esc_attr_e( 'Collapse Sidebar' ); ?>">
				<span class="collapse-sidebar-arrow"></span>
				<span class="collapse-sidebar-label"><?php _e( 'Collapse' ); ?></span>
			</button>
		</div>
	</form>
	<div id="customize-preview" class="wp-full-overlay-main"></div>
	<?php

	/**
	 * Prints templates, control scripts, and settings in the footer.
	 *
	 * @since 3.4.0
	 */
	do_action( 'customize_controls_print_footer_scripts' );
	?>
</div>
</body>
</html>
