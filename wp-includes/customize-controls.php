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

$theme = wp_get_theme();
$screenshot = $theme->get_screenshot( 'absolute' );

// Let's roll.
@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));

wp_user_settings();
_wp_admin_html_begin();

$admin_title = sprintf( __( '%1$s &#8212; WordPress' ), strip_tags( sprintf( __( 'Customize %s' ), $theme['Name'] ) ) );
?><title><?php echo $admin_title; ?></title><?php

do_action( 'customize_controls_print_styles' );
do_action( 'customize_controls_print_scripts' );
?>
</head>
<body>
	<form id="customize-controls" method="post" class="wrap" target="_parent" action="<?php echo esc_url( add_query_arg( 'save', '1', admin_url( 'themes.php' ) ) ); ?>">
		<?php wp_nonce_field( 'customize_controls' ); ?>
		<input type="hidden" name="customize" value="on" />
		<input type="hidden" id="customize-template" name="template" value="<?php echo esc_attr( $theme['Template'] ); ?>" />
		<input type="hidden" id="customize-stylesheet" name="stylesheet" value="<?php echo esc_attr( $theme['Stylesheet'] ); ?>" />

		<div id="customize-info" class="customize-section">
			<p>
				<strong class="theme-name"><?php echo $theme['Name']; ?></strong>
				<span class="theme-by"><?php printf( __( 'By %s' ), $theme['Author'] ); ?></span>
			</p>
			<?php if ( $screenshot ) : ?>
				<img class="theme-screenshot" src="<?php echo esc_url( $screenshot ); ?>" />
			<?php endif; ?>
		</div>

		<div id="customize-theme-controls"><ul>
			<?php
			foreach ( $this->sections as $section )
				$section->render();
			?>
		</ul></div>

		<div id="customize-footer" class="customize-section">
			<?php
			submit_button( __( 'Save' ), 'primary', 'save', false );
			?>
		</div>
	</form>
	<div id="customize-preview">
		<iframe name="customize-target"></iframe>
	</div>
	<?php

	do_action( 'customize_controls_print_footer_scripts' );

	$settings = array(
		'preview' => esc_url( home_url( '/' ) ),
		'values'  => array(),
		'prefix'  => WP_Customize_Setting::name_prefix,
	);

	foreach ( $this->settings as $id => $setting ) {
		$settings['values'][ $id ] = $setting->value();
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
