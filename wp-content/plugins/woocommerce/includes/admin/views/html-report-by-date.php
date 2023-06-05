<?php
/**
 * Admin View: Report by Date (with date filters)
 *
 * @package WooCommerce\Admin\Reporting
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div id="poststuff" class="woocommerce-reports-wide">
	<div class="postbox">

	<?php if ( 'custom' === $current_range && isset( $_GET['start_date'], $_GET['end_date'] ) ) : ?>
		<h3 class="screen-reader-text">
			<?php
			/* translators: 1: start date 2: end date */
			printf(
				esc_html__( 'From %1$s to %2$s', 'woocommerce' ),
				esc_html( wc_clean( wp_unslash( $_GET['start_date'] ) ) ),
				esc_html( wc_clean( wp_unslash( $_GET['end_date'] ) ) )
			);
			?>
		</h3>
	<?php else : ?>
		<h3 class="screen-reader-text"><?php echo esc_html( $ranges[ $current_range ] ); ?></h3>
	<?php endif; ?>

		<div class="stats_range">
			<?php $this->get_export_button(); ?>
			<ul>
				<?php
				foreach ( $ranges as $range => $name ) {
					echo '<li class="' . ( $current_range == $range ? 'active' : '' ) . '"><a href="' . esc_url( remove_query_arg( array( 'start_date', 'end_date' ), add_query_arg( 'range', $range ) ) ) . '">' . esc_html( $name ) . '</a></li>';
				}
				?>
				<li class="custom <?php echo ( 'custom' === $current_range ) ? 'active' : ''; ?>">
					<?php esc_html_e( 'Custom:', 'woocommerce' ); ?>
					<form method="GET">
						<div>
							<?php
							// Maintain query string.
							foreach ( $_GET as $key => $value ) {
								if ( is_array( $value ) ) {
									foreach ( $value as $v ) {
										echo '<input type="hidden" name="' . esc_attr( sanitize_text_field( $key ) ) . '[]" value="' . esc_attr( sanitize_text_field( $v ) ) . '" />';
									}
								} else {
									echo '<input type="hidden" name="' . esc_attr( sanitize_text_field( $key ) ) . '" value="' . esc_attr( sanitize_text_field( $value ) ) . '" />';
								}
							}
							?>
							<input type="hidden" name="range" value="custom" />
							<input type="text" size="11" placeholder="yyyy-mm-dd" value="<?php echo ( ! empty( $_GET['start_date'] ) ) ? esc_attr( wp_unslash( $_GET['start_date'] ) ) : ''; ?>" name="start_date" class="range_datepicker from" autocomplete="off" /><?php //@codingStandardsIgnoreLine ?>
							<span>&ndash;</span>
							<input type="text" size="11" placeholder="yyyy-mm-dd" value="<?php echo ( ! empty( $_GET['end_date'] ) ) ? esc_attr( wp_unslash( $_GET['end_date'] ) ) : ''; ?>" name="end_date" class="range_datepicker to" autocomplete="off" /><?php //@codingStandardsIgnoreLine ?>
							<button type="submit" class="button" value="<?php esc_attr_e( 'Go', 'woocommerce' ); ?>"><?php esc_html_e( 'Go', 'woocommerce' ); ?></button>
							<?php wp_nonce_field( 'custom_range', 'wc_reports_nonce', false ); ?>
						</div>
					</form>
				</li>
			</ul>
		</div>
		<?php if ( empty( $hide_sidebar ) ) : ?>
			<div class="inside chart-with-sidebar">
				<div class="chart-sidebar">
					<?php if ( $legends = $this->get_chart_legend() ) : ?>
						<ul class="chart-legend">
							<?php foreach ( $legends as $legend ) : ?>
								<?php // @codingStandardsIgnoreStart ?>
								<li style="border-color: <?php echo $legend['color']; ?>" <?php if ( isset( $legend['highlight_series'] ) ) echo 'class="highlight_series ' . ( isset( $legend['placeholder'] ) ? 'tips' : '' ) . '" data-series="' . esc_attr( $legend['highlight_series'] ) . '"'; ?> data-tip="<?php echo isset( $legend['placeholder'] ) ? $legend['placeholder'] : ''; ?>">
									<?php echo $legend['title']; ?>
								</li>
								<?php // @codingStandardsIgnoreEnd ?>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
					<ul class="chart-widgets">
						<?php foreach ( $this->get_chart_widgets() as $widget ) : ?>
							<li class="chart-widget">
								<?php if ( $widget['title'] ) : ?>
									<h4><?php echo esc_html( $widget['title'] ); ?></h4>
								<?php endif; ?>
								<?php call_user_func( $widget['callback'] ); ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<div class="main">
					<?php $this->get_main_chart(); ?>
				</div>
			</div>
		<?php else : ?>
			<div class="inside">
				<?php $this->get_main_chart(); ?>
			</div>
		<?php endif; ?>
	</div>
</div>
