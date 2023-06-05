<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WC_Report_Taxes_By_Date
 *
 * @author      WooThemes
 * @category    Admin
 * @package     WooCommerce\Admin\Reports
 * @version     2.1.0
 */
class WC_Report_Taxes_By_Date extends WC_Admin_Report {

	/**
	 * Get the legend for the main chart sidebar.
	 *
	 * @return array
	 */
	public function get_chart_legend() {
		return array();
	}

	/**
	 * Output an export link.
	 */
	public function get_export_button() {

		$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : 'last_month';
		?>
		<a
			href="#"
			download="report-<?php echo esc_attr( $current_range ); ?>-<?php echo date_i18n( 'Y-m-d', current_time( 'timestamp' ) ); ?>.csv"
			class="export_csv"
			data-export="table"
		>
			<?php _e( 'Export CSV', 'woocommerce' ); ?>
		</a>
		<?php
	}

	/**
	 * Output the report.
	 */
	public function output_report() {

		$ranges = array(
			'year'       => __( 'Year', 'woocommerce' ),
			'last_month' => __( 'Last month', 'woocommerce' ),
			'month'      => __( 'This month', 'woocommerce' ),
		);

		$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : 'last_month';

		if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) ) {
			$current_range = 'last_month';
		}

		$this->check_current_range_nonce( $current_range );
		$this->calculate_current_range( $current_range );

		$hide_sidebar = true;

		include WC()->plugin_path() . '/includes/admin/views/html-report-by-date.php';
	}

	/**
	 * Get the main chart.
	 */
	public function get_main_chart() {
		$query_data = array(
			'_order_tax'          => array(
				'type'     => 'meta',
				'function' => 'SUM',
				'name'     => 'tax_amount',
			),
			'_order_shipping_tax' => array(
				'type'     => 'meta',
				'function' => 'SUM',
				'name'     => 'shipping_tax_amount',
			),
			'_order_total'        => array(
				'type'     => 'meta',
				'function' => 'SUM',
				'name'     => 'total_sales',
			),
			'_order_shipping'     => array(
				'type'     => 'meta',
				'function' => 'SUM',
				'name'     => 'total_shipping',
			),
			'ID'                  => array(
				'type'     => 'post_data',
				'function' => 'COUNT',
				'name'     => 'total_orders',
				'distinct' => true,
			),
			'post_date'           => array(
				'type'     => 'post_data',
				'function' => '',
				'name'     => 'post_date',
			),
		);

		// We exclude on-hold orders are they are still pending payment.
		$tax_rows_orders = $this->get_order_report_data(
			array(
				'data'         => $query_data,
				'group_by'     => $this->group_by_query,
				'order_by'     => 'post_date ASC',
				'query_type'   => 'get_results',
				'filter_range' => true,
				'order_types'  => wc_get_order_types( 'sales-reports' ),
				'order_status' => array( 'completed', 'processing', 'refunded' ),
			)
		);

		$tax_rows_full_refunds = $this->get_order_report_data(
			array(
				'data'                => array(
					'ID'          => array(
						'type'     => 'post_data',
						'distinct' => true,
						'function' => '',
						'name'     => 'ID',
					),
					'post_parent' => array(
						'type'     => 'post_data',
						'function' => '',
						'name'     => 'post_parent',
					),
					'post_date'   => array(
						'type'     => 'post_data',
						'function' => '',
						'name'     => 'post_date',
					),
				),
				'query_type'          => 'get_results',
				'filter_range'        => true,
				'order_types'         => array( 'shop_order_refund' ),
				'parent_order_status' => array( 'refunded' ),
			)
		);

		$tax_rows_partial_refunds = $this->get_order_report_data(
			array(
				'data'                => $query_data,
				'group_by'            => $this->group_by_query,
				'order_by'            => 'post_date ASC',
				'query_type'          => 'get_results',
				'filter_range'        => true,
				'order_types'         => array( 'shop_order_refund' ),
				'parent_order_status' => array( 'completed', 'processing' ), // Partial refunds inside refunded orders should be ignored.
			)
		);

		$tax_rows = array();

		foreach ( $tax_rows_orders + $tax_rows_partial_refunds as $tax_row ) {
			$key              = date( ( 'month' === $this->chart_groupby ) ? 'Ym' : 'Ymd', strtotime( $tax_row->post_date ) );
			$tax_rows[ $key ] = isset( $tax_rows[ $key ] ) ? $tax_rows[ $key ] : (object) array(
				'tax_amount'          => 0,
				'shipping_tax_amount' => 0,
				'total_sales'         => 0,
				'total_shipping'      => 0,
				'total_orders'        => 0,
			);
		}

		foreach ( $tax_rows_orders as $tax_row ) {
			$key                                    = date( ( 'month' === $this->chart_groupby ) ? 'Ym' : 'Ymd', strtotime( $tax_row->post_date ) );
			$tax_rows[ $key ]->total_orders        += $tax_row->total_orders;
			$tax_rows[ $key ]->tax_amount          += $tax_row->tax_amount;
			$tax_rows[ $key ]->shipping_tax_amount += $tax_row->shipping_tax_amount;
			$tax_rows[ $key ]->total_sales         += $tax_row->total_sales;
			$tax_rows[ $key ]->total_shipping      += $tax_row->total_shipping;
		}

		foreach ( $tax_rows_partial_refunds as $tax_row ) {
			$key                                    = date( ( 'month' === $this->chart_groupby ) ? 'Ym' : 'Ymd', strtotime( $tax_row->post_date ) );
			$tax_rows[ $key ]->tax_amount          += $tax_row->tax_amount;
			$tax_rows[ $key ]->shipping_tax_amount += $tax_row->shipping_tax_amount;
			$tax_rows[ $key ]->total_sales         += $tax_row->total_sales;
			$tax_rows[ $key ]->total_shipping      += $tax_row->total_shipping;
		}

		foreach ( $tax_rows_full_refunds as $tax_row ) {
			$key              = date( ( 'month' === $this->chart_groupby ) ? 'Ym' : 'Ymd', strtotime( $tax_row->post_date ) );
			$tax_rows[ $key ] = isset( $tax_rows[ $key ] ) ? $tax_rows[ $key ] : (object) array(
				'tax_amount'          => 0,
				'shipping_tax_amount' => 0,
				'total_sales'         => 0,
				'total_shipping'      => 0,
				'total_orders'        => 0,
			);
			$parent_order     = wc_get_order( $tax_row->post_parent );

			if ( $parent_order ) {
				$tax_rows[ $key ]->tax_amount          += $parent_order->get_cart_tax() * -1;
				$tax_rows[ $key ]->shipping_tax_amount += $parent_order->get_shipping_tax() * -1;
				$tax_rows[ $key ]->total_sales         += $parent_order->get_total() * -1;
				$tax_rows[ $key ]->total_shipping      += $parent_order->get_shipping_total() * -1;
			}
		}
		?>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php _e( 'Period', 'woocommerce' ); ?></th>
					<th class="total_row"><?php _e( 'Number of orders', 'woocommerce' ); ?></th>
					<th class="total_row"><?php _e( 'Total sales', 'woocommerce' ); ?> <?php echo wc_help_tip( __( "This is the sum of the 'Order total' field within your orders.", 'woocommerce' ) ); ?></th>
					<th class="total_row"><?php _e( 'Total shipping', 'woocommerce' ); ?> <?php echo wc_help_tip( __( "This is the sum of the 'Shipping total' field within your orders.", 'woocommerce' ) ); ?></th>
					<th class="total_row"><?php _e( 'Total tax', 'woocommerce' ); ?> <?php echo wc_help_tip( __( 'This is the total tax for the rate (shipping tax + product tax).', 'woocommerce' ) ); ?></th>
					<th class="total_row"><?php _e( 'Net profit', 'woocommerce' ); ?> <?php echo wc_help_tip( __( 'Total sales minus shipping and tax.', 'woocommerce' ) ); ?></th>
				</tr>
			</thead>
			<?php if ( ! empty( $tax_rows ) ) : ?>
				<tbody>
					<?php
					foreach ( $tax_rows as $date => $tax_row ) {
						$gross     = $tax_row->total_sales - $tax_row->total_shipping;
						$total_tax = $tax_row->tax_amount + $tax_row->shipping_tax_amount;
						?>
						<tr>
							<th scope="row">
								<?php echo ( 'month' === $this->chart_groupby ) ? date_i18n( 'F', strtotime( $date . '01' ) ) : date_i18n( get_option( 'date_format' ), strtotime( $date ) ); ?>
							</th>
							<td class="total_row"><?php echo $tax_row->total_orders; ?></td>
							<td class="total_row"><?php echo wc_price( $gross ); ?></td>
							<td class="total_row"><?php echo wc_price( $tax_row->total_shipping ); ?></td>
							<td class="total_row"><?php echo wc_price( $total_tax ); ?></td>
							<td class="total_row"><?php echo wc_price( $gross - $total_tax ); ?></td>
						</tr>
						<?php
					}
					?>
				</tbody>
				<tfoot>
					<?php
						$gross     = array_sum( wp_list_pluck( (array) $tax_rows, 'total_sales' ) ) - array_sum( wp_list_pluck( (array) $tax_rows, 'total_shipping' ) );
						$total_tax = array_sum( wp_list_pluck( (array) $tax_rows, 'tax_amount' ) ) + array_sum( wp_list_pluck( (array) $tax_rows, 'shipping_tax_amount' ) );
					?>
					<tr>
						<th scope="row"><?php _e( 'Totals', 'woocommerce' ); ?></th>
						<th class="total_row"><?php echo array_sum( wp_list_pluck( (array) $tax_rows, 'total_orders' ) ); ?></th>
						<th class="total_row"><?php echo wc_price( $gross ); ?></th>
						<th class="total_row"><?php echo wc_price( array_sum( wp_list_pluck( (array) $tax_rows, 'total_shipping' ) ) ); ?></th>
						<th class="total_row"><?php echo wc_price( $total_tax ); ?></th>
						<th class="total_row"><?php echo wc_price( $gross - $total_tax ); ?></th>
					</tr>
				</tfoot>
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
