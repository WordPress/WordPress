<?php
/**
 * WC_Report_Taxes_By_Code
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin/Reports
 * @version     2.1.0
 */
class WC_Report_Taxes_By_Code extends WC_Admin_Report {

	/**
	 * Get the legend for the main chart sidebar
	 * @return array
	 */
	public function get_chart_legend() {
		$legend   = array();

		return array();
	}

	/**
	 * Output an export link
	 */
	public function get_export_button() {
		$current_range = ! empty( $_GET['range'] ) ? $_GET['range'] : 'last_month';
		?>
		<a
			href="#"
			download="report-<?php echo $current_range; ?>-<?php echo date_i18n( 'Y-m-d', current_time('timestamp') ); ?>.csv"
			class="export_csv"
			data-export="table"
		>
			<?php _e( 'Export CSV', 'woocommerce' ); ?>
		</a>
		<?php
	}

	/**
	 * Output the report
	 */
	public function output_report() {
		global $woocommerce, $wpdb, $wp_locale;

		$ranges = array(
			'year'         => __( 'Year', 'woocommerce' ),
			'last_month'   => __( 'Last Month', 'woocommerce' ),
			'month'        => __( 'This Month', 'woocommerce' ),
		);

		$current_range = ! empty( $_GET['range'] ) ? $_GET['range'] : 'last_month';

		if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) )
			$current_range = 'last_month';

		$this->calculate_current_range( $current_range );

		$hide_sidebar = true;

		include( WC()->plugin_path() . '/includes/admin/views/html-report-by-date.php');
	}

	/**
	 * Get the main chart
	 * @return string
	 */
	public function get_main_chart() {
		global $wpdb;

		$tax_rows = $this->get_order_report_data( array(
			'data' => array(
				'order_item_name' => array(
					'type'     => 'order_item',
					'function' => '',
					'name'     => 'tax_rate'
				),
				'tax_amount' => array(
					'type'            => 'order_item_meta',
					'order_item_type' => 'tax',
					'function'        => '',
					'name'            => 'tax_amount'
				),
				'shipping_tax_amount' => array(
					'type'            => 'order_item_meta',
					'order_item_type' => 'tax',
					'function'        => '',
					'name'            => 'shipping_tax_amount'
				),
				'rate_id' => array(
					'type'            => 'order_item_meta',
					'order_item_type' => 'tax',
					'function'        => '',
					'name'            => 'rate_id'
				)
			),
			'where' => array(
				array(
					'key'      => 'order_item_type',
					'value'    => 'tax',
					'operator' => '='
				),
				array(
					'key'      => 'order_item_name',
					'value'    => '',
					'operator' => '!='
				)
			),
			'order_by'     => 'post_date ASC',
			'query_type'   => 'get_results',
			'filter_range' => true
		) );
		?>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php _e( 'Tax', 'woocommerce' ); ?></th>
					<th><?php _e( 'Rate', 'woocommerce' ); ?></th>
					<th class="total_row"><?php _e( 'Number of orders', 'woocommerce' ); ?></th>
					<th class="total_row"><?php _e( 'Tax Amount', 'woocommerce' ); ?> <a class="tips" data-tip="<?php esc_attr_e( 'This is the sum of the "Tax Rows" tax amount within your orders.', 'woocommerce' ); ?>" href="#">[?]</a></th>
					<th class="total_row"><?php _e( 'Shipping Tax Amount', 'woocommerce' ); ?> <a class="tips" data-tip="<?php esc_attr_e( 'This is the sum of the "Tax Rows" shipping tax amount within your orders.', 'woocommerce' ); ?>" href="#">[?]</a></th>
					<th class="total_row"><?php _e( 'Total Tax', 'woocommerce' ); ?> <a class="tips" data-tip="<?php esc_attr_e( 'This is the total tax for the rate (shipping tax + product tax).', 'woocommerce' ); ?>" href="#">[?]</a></th>
				</tr>
			</thead>
			<?php if ( $tax_rows ) : ?>
				<tfoot>
					<tr>
						<th scope="row" colspan="3"><?php _e( 'Total', 'woocommerce' ); ?></th>
						<th class="total_row"><?php echo wc_price( wc_round_tax_total( array_sum( wp_list_pluck( (array) $tax_rows, 'tax_amount' ) ) ) ); ?></th>
						<th class="total_row"><?php echo wc_price( wc_round_tax_total( array_sum( wp_list_pluck( (array) $tax_rows, 'shipping_tax_amount' ) ) ) ); ?></th>
						<th class="total_row"><strong><?php echo wc_price( wc_round_tax_total( array_sum( wp_list_pluck( (array) $tax_rows, 'tax_amount' ) ) + array_sum( wp_list_pluck( (array) $tax_rows, 'shipping_tax_amount' ) ) ) ); ?></strong></th>
					</tr>
				</tfoot>
				<tbody>
					<?php
					$grouped_tax_tows = array();

					foreach ( $tax_rows as $tax_row ) {
						if ( ! isset( $grouped_tax_tows[ $tax_row->rate_id ] ) ) {
							$grouped_tax_tows[ $tax_row->rate_id ] = (object) array(
								'tax_rate'            => $tax_row->tax_rate,
								'total_orders'        => 0,
								'tax_amount'          => 0,
								'shipping_tax_amount' => 0
							);
						}
						
						$grouped_tax_tows[ $tax_row->rate_id ]->total_orders ++; 
						$grouped_tax_tows[ $tax_row->rate_id ]->tax_amount += wc_round_tax_total( $tax_row->tax_amount );
						$grouped_tax_tows[ $tax_row->rate_id ]->shipping_tax_amount += wc_round_tax_total( $tax_row->shipping_tax_amount );
					}

					foreach ( $grouped_tax_tows as $rate_id => $tax_row ) {
						$rate = $wpdb->get_var( $wpdb->prepare( "SELECT tax_rate FROM {$wpdb->prefix}woocommerce_tax_rates WHERE tax_rate_id = %d;", $rate_id ) );
						?>
						<tr>
							<th scope="row"><?php echo $tax_row->tax_rate; ?></th>
							<td><?php echo $rate; ?>%</td>
							<td class="total_row"><?php echo $tax_row->total_orders; ?></td>
							<td class="total_row"><?php echo wc_price( $tax_row->tax_amount ); ?></td>
							<td class="total_row"><?php echo wc_price( $tax_row->shipping_tax_amount ); ?></td>
							<td class="total_row"><?php echo wc_price( $tax_row->tax_amount + $tax_row->shipping_tax_amount ); ?></td>
						</tr>
						<?php
					}
					?>
				</tbody>
			<?php else : ?>
				<tbody>
					<tr>
						<td><?php _e( 'No taxes found in this period', 'woocommerce' ); ?></td>
					</tr>
				</tbody>
			<?php endif; ?>
		</table>
		<?php
	}
}