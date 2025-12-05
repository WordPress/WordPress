<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $wp_version;

$body_classes = [
	'elementor-editor-active',
	'wp-version-' . str_replace( '.', '-', $wp_version ),
];

if ( is_rtl() ) {
	$body_classes[] = 'rtl';
}

if ( ! Plugin::$instance->role_manager->user_can( 'design' ) ) {
	$body_classes[] = 'elementor-editor-content-only';
}

$notice = Plugin::$instance->editor->notice_bar->get_notice();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title><?php printf(
		/* translators: %s: Page title. */
		esc_html__( 'Edit "%s" with Elementor', 'elementor' ),
		esc_html( get_the_title() )
	); ?></title>
	<?php wp_head(); ?>
	<script>
		var ajaxurl = '<?php Utils::print_unescaped_internal_string( admin_url( 'admin-ajax.php', 'relative' ) ); ?>';
	</script>
</head>
<body class="<?php echo esc_attr( implode( ' ', $body_classes ) ); ?>">
<?php
if ( isset( $body_file_path ) ) {
	include $body_file_path;
}
?>
<?php
	wp_footer();
	/** This action is documented in wp-admin/admin-footer.php */
	do_action( 'admin_print_footer_scripts' );
?>
</body>
</html>
