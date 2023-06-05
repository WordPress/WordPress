<?php
/**
 * Admin View: Notice - Update
 *
 * @package WooCommerce\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$update_url = wp_nonce_url(
	add_query_arg( 'do_update_woocommerce', 'true', admin_url( 'admin.php?page=wc-settings' ) ),
	'wc_db_update',
	'wc_db_update_nonce'
);

?>
<div id="message" class="updated woocommerce-message wc-connect">
	<p>
		<strong><?php esc_html_e( 'WooCommerce database update required', 'woocommerce' ); ?></strong>
	</p>
	<p>
		<?php
			esc_html_e( 'WooCommerce has been updated! To keep things running smoothly, we have to update your database to the newest version.', 'woocommerce' );

			/* translators: 1: Link to docs 2: Close link. */
			printf( ' ' . esc_html__( 'The database update process runs in the background and may take a little while, so please be patient. Advanced users can alternatively update via %1$sWP CLI%2$s.', 'woocommerce' ), '<a href="https://github.com/woocommerce/woocommerce/wiki/Upgrading-the-database-using-WP-CLI">', '</a>' );
		?>
	</p>
	<p class="submit">
		<a href="<?php echo esc_url( $update_url ); ?>" class="wc-update-now button-primary">
			<?php esc_html_e( 'Update WooCommerce Database', 'woocommerce' ); ?>
		</a>
		<a href="https://docs.woocommerce.com/document/how-to-update-woocommerce/" class="button-secondary">
			<?php esc_html_e( 'Learn more about updates', 'woocommerce' ); ?>
		</a>
	</p>
</div>
