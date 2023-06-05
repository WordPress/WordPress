<?php
/**
 * Admin View: Page - Status Logs
 *
 * @package WooCommerce\Admin\Logs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<?php if ( $logs ) : ?>
	<div id="log-viewer-select">
		<div class="alignleft">
			<h2>
				<?php echo esc_html( $viewed_log ); ?>
				<?php if ( ! empty( $viewed_log ) ) : ?>
					<a class="page-title-action" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'handle' => sanitize_title( $viewed_log ) ), admin_url( 'admin.php?page=wc-status&tab=logs' ) ), 'remove_log' ) ); ?>" class="button"><?php esc_html_e( 'Delete log', 'woocommerce' ); ?></a>
				<?php endif; ?>
			</h2>
		</div>
		<div class="alignright">
			<form action="<?php echo esc_url( admin_url( 'admin.php?page=wc-status&tab=logs' ) ); ?>" method="post">
				<select class="wc-enhanced-select" name="log_file">
					<?php foreach ( $logs as $log_key => $log_file ) : ?>
						<?php
							$timestamp = filemtime( WC_LOG_DIR . $log_file );
							$date      = sprintf(
								/* translators: 1: last access date 2: last access time 3: last access timezone abbreviation */
								__( '%1$s at %2$s %3$s', 'woocommerce' ),
								wp_date( wc_date_format(), $timestamp ),
								wp_date( wc_time_format(), $timestamp ),
								wp_date( 'T', $timestamp )
							);
						?>
						<option value="<?php echo esc_attr( $log_key ); ?>" <?php selected( sanitize_title( $viewed_log ), $log_key ); ?>><?php echo esc_html( $log_file ); ?> (<?php echo esc_html( $date ); ?>)</option>
					<?php endforeach; ?>
				</select>
				<button type="submit" class="button" value="<?php esc_attr_e( 'View', 'woocommerce' ); ?>"><?php esc_html_e( 'View', 'woocommerce' ); ?></button>
			</form>
		</div>
		<div class="clear"></div>
	</div>
	<div id="log-viewer">
		<pre><?php echo esc_html( file_get_contents( WC_LOG_DIR . $viewed_log ) ); ?></pre>
	</div>
<?php else : ?>
	<div class="updated woocommerce-message inline"><p><?php esc_html_e( 'There are currently no logs to view.', 'woocommerce' ); ?></p></div>
<?php endif; ?>
