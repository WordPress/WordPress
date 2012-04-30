<?php
/**
 * Customize Controls
 *
 * @package WordPress
 * @subpackage Customize
 * @since 3.4.0
 */

if ( ! defined( 'ABSPATH' ) )
	die;

global $wp_scripts;

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

$admin_title = sprintf( __( '%1$s &#8212; WordPress' ), strip_tags( sprintf( __( 'Customize %s' ), $this->theme->display('Name') ) ) );
?><title><?php echo $admin_title; ?></title><?php

do_action( 'customize_controls_print_styles' );
do_action( 'customize_controls_print_scripts' );
?>
</head>
<body class="wp-full-overlay">
	<form id="customize-controls" class="wrap wp-full-overlay-sidebar">
		<?php wp_nonce_field( 'customize_controls' ); ?>
		<div id="customize-header-actions" class="customize-section wp-full-overlay-header">
			<a class="back" href="<?php echo esc_url( admin_url( 'themes.php' ) ); ?>">
				<?php printf( __( '&larr; Return to %s' ), __('Manage Themes') ); ?>
			</a>
		</div>

		<div id="customize-info" class="customize-section">
			<div class="customize-section-title">
				<span class="preview-notice"><?php _e('You are previewing'); ?></span>
				<strong class="theme-name"><?php echo $this->theme->display('Name'); ?></strong>
			</div>
			<div class="customize-section-content">
				<?php if ( $screenshot = $this->theme->get_screenshot() ) : ?>
					<img class="theme-screenshot" src="<?php echo esc_url( $screenshot ); ?>" />
				<?php endif; ?>

				<?php if ( $this->theme->get('Description') ): ?>
					<div class="theme-description"><?php echo $this->theme->display('Description'); ?></div>
				<?php endif; ?>
			</div>
		</div>

		<div id="customize-theme-controls"><ul>
			<?php
			foreach ( $this->sections as $section )
				$section->maybe_render();
			?>
		</ul></div>

		<div id="customize-footer-actions" class="customize-section wp-full-overlay-footer">
			<?php
			$save_text = $this->get_stylesheet() == $this->original_stylesheet ? __('Save') : __('Save and Activate');
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

	// Check current scheme and load the preview with the same scheme
	$scheme = is_ssl() ? 'https' : 'http';
	$settings = array(
		'theme'    => $this->get_stylesheet(),
		'preview'  => esc_url( home_url( '/', $scheme ) ),
		'settings' => array(),
		'controls' => array(),
		'parent'   => esc_url( admin_url() ),
		'ajax'     => esc_url( admin_url( 'admin-ajax.php', 'relative' ) ),
	);

	foreach ( $this->settings as $id => $setting ) {
		$settings['settings'][ $id ] = array(
			'value'     => $setting->value(),
			'transport' => $setting->transport,
		);
	}

	foreach ( $this->controls as $id => $control ) {
		$control->to_json();
		$settings['controls'][ $id ] = $control->json;
	}

	?>
	<script type="text/javascript">
		(function() {
			if ( typeof wp === 'undefined' || ! wp.customize )
				return;

			wp.customize.settings = <?php echo json_encode( $settings ); ?>;
		})();
	</script>
</body>
</html>
