<?php
/**
 * Admin View: Page - Status
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_tab = ! empty( $_REQUEST['tab'] ) ? sanitize_title( $_REQUEST['tab'] ) : 'status';
$tabs        = array(
	'status' => __( 'System status', 'woocommerce' ),
	'tools'  => __( 'Tools', 'woocommerce' ),
	'logs'   => __( 'Logs', 'woocommerce' ),
);
$tabs        = apply_filters( 'woocommerce_admin_status_tabs', $tabs );
?>
<div class="wrap woocommerce">
	<nav class="nav-tab-wrapper woo-nav-tab-wrapper">
		<?php
		foreach ( $tabs as $name => $label ) {
			echo '<a href="' . admin_url( 'admin.php?page=wc-status&tab=' . $name ) . '" class="nav-tab ';
			if ( $current_tab == $name ) {
				echo 'nav-tab-active';
			}
			echo '">' . $label . '</a>';
		}
		?>
	</nav>
	<h1 class="screen-reader-text"><?php echo esc_html( $tabs[ $current_tab ] ); ?></h1>
	<?php
	switch ( $current_tab ) {
		case 'tools':
			WC_Admin_Status::status_tools();
			break;
		case 'logs':
			WC_Admin_Status::status_logs();
			break;
		default:
			if ( array_key_exists( $current_tab, $tabs ) && has_action( 'woocommerce_admin_status_content_' . $current_tab ) ) {
				do_action( 'woocommerce_admin_status_content_' . $current_tab );
			} else {
				WC_Admin_Status::status_report();
			}
			break;
	}
	?>
</div>
