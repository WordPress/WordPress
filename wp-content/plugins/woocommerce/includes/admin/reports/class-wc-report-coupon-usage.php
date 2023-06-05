<?php
/**
 * Coupon usage report functionality
 *
 * @package WooCommerce\Admin\Reports
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WC_Report_Coupon_Usage
 *
 * @package     WooCommerce\Admin\Reports
 * @version     2.1.0
 */
class WC_Report_Coupon_Usage extends WC_Admin_Report {

	/**
	 * Chart colors.
	 *
	 * @var array
	 */
	public $chart_colours = array();

	/**
	 * Coupon codes.
	 *
	 * @var array
	 */
	public $coupon_codes = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( isset( $_GET['coupon_codes'] ) && is_array( $_GET['coupon_codes'] ) ) {
			$this->coupon_codes = array_filter( array_map( 'sanitize_text_field', wp_unslash( $_GET['coupon_codes'] ) ) );
		} elseif ( isset( $_GET['coupon_codes'] ) ) {
			$this->coupon_codes = array_filter( array( sanitize_text_field( wp_unslash( $_GET['coupon_codes'] ) ) ) );
		}
	}

	/**
	 * Get the legend for the main chart sidebar.
	 *
	 * @return array
	 */
	public function get_chart_legend() {
		$legend = array();

		$total_discount_query = array(
			'data'         => array(
				'discount_amount' => array(
					'type'            => 'order_item_meta',
					'order_item_type' => 'coupon',
					'function'        => 'SUM',
					'name'            => 'discount_amount',
				),
			),
			'where'        => array(
				array(
					'key'      => 'order_item_type',
					'value'    => 'coupon',
					'operator' => '=',
				),
			),
			'query_type'   => 'get_var',
			'filter_range' => true,
			'order_types'  => wc_get_order_types( 'order-count' ),
		);

		$total_coupons_query = array(
			'data'         => array(
				'order_item_id' => array(
					'type'            => 'order_item',
					'order_item_type' => 'coupon',
					'function'        => 'COUNT',
					'name'            => 'order_coupon_count',
				),
			),
			'where'        => array(
				array(
					'key'      => 'order_item_type',
					'value'    => 'coupon',
					'operator' => '=',
				),
			),
			'query_type'   => 'get_var',
			'filter_range' => true,
			'order_types'  => wc_get_order_types( 'order-count' ),
		);

		if ( ! empty( $this->coupon_codes ) ) {
			$coupon_code_query = array(
				'type'     => 'order_item',
				'key'      => 'order_item_name',
				'value'    => $this->coupon_codes,
				'operator' => 'IN',
			);

			$total_discount_query['where'][] = $coupon_code_query;
			$total_coupons_query['where'][]  = $coupon_code_query;
		}

		$total_discount = $this->get_order_report_data( $total_discount_query );
		$total_coupons  = absint( $this->get_order_report_data( $total_coupons_query ) );

		$legend[] = array(
			/* translators: %s: discount amount */
			'title'            => sprintf( __( '%s discounts in total', 'woocommerce' ), '<strong>' . wc_price( $total_discount ) . '</strong>' ),
			'color'            => $this->chart_colours['discount_amount'],
			'highlight_series' => 1,
		);

		$legend[] = array(
			/* translators: %s: coupons amount */
			'title'            => sprintf( __( '%s coupons used in total', 'woocommerce' ), '<strong>' . $total_coupons . '</strong>' ),
			'color'            => $this->chart_colours['coupon_count'],
			'highlight_series' => 0,
		);

		return $legend;
	}

	/**
	 * Output the report.
	 */
	public function output_report() {

		$ranges = array(
			'year'       => __( 'Year', 'woocommerce' ),
			'last_month' => __( 'Last month', 'woocommerce' ),
			'month'      => __( 'This month', 'woocommerce' ),
			'7day'       => __( 'Last 7 days', 'woocommerce' ),
		);

		$this->chart_colours = array(
			'discount_amount' => '#3498db',
			'coupon_count'    => '#d4d9dc',
		);

		$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( wp_unslash( $_GET['range'] ) ) : '7day';

		if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) ) {
			$current_range = '7day';
		}

		$this->check_current_range_nonce( $current_range );
		$this->calculate_current_range( $current_range );

		include WC()->plugin_path() . '/includes/admin/views/html-report-by-date.php';
	}

	/**
	 * Get chart widgets.
	 *
	 * @return array
	 */
	public function get_chart_widgets() {
		$widgets = array();

		$widgets[] = array(
			'title'    => '',
			'callback' => array( $this, 'coupons_widget' ),
		);

		return $widgets;
	}

	/**
	 * Output coupons widget.
	 */
	public function coupons_widget() {
		?>
		<h4 class="section_title"><span><?php esc_html_e( 'Filter by coupon', 'woocommerce' ); ?></span></h4>
		<div class="section">
			<form method="GET">
				<div>
					<?php
					$used_coupons = $this->get_order_report_data(
						array(
							'data'         => array(
								'order_item_name' => array(
									'type'            => 'order_item',
									'order_item_type' => 'coupon',
									'function'        => '',
									'distinct'        => true,
									'name'            => 'order_item_name',
								),
							),
							'where'        => array(
								array(
									'key'      => 'order_item_type',
									'value'    => 'coupon',
									'operator' => '=',
								),
							),
							'query_type'   => 'get_col',
							'filter_range' => false,
						)
					);

					if ( ! empty( $used_coupons ) && is_array( $used_coupons ) ) :
						?>
						<select id="coupon_codes" name="coupon_codes" class="wc-enhanced-select" data-placeholder="<?php esc_attr_e( 'Choose coupons&hellip;', 'woocommerce' ); ?>" style="width:100%;">
							<option value=""><?php esc_html_e( 'All coupons', 'woocommerce' ); ?></option>
							<?php
							foreach ( $used_coupons as $coupon ) {
								echo '<option value="' . esc_attr( $coupon ) . '"' . wc_selected( $coupon, $this->coupon_codes ) . '>' . esc_html( $coupon ) . '</option>';
							}
							?>
						</select>
						<?php // @codingStandardsIgnoreStart ?>
						<button type="submit" class="submit button" value="<?php esc_attr_e( 'Show', 'woocommerce' ); ?>"><?php esc_html_e( 'Show', 'woocommerce' ); ?></button>
						<input type="hidden" name="range" value="<?php echo ( ! empty( $_GET['range'] ) ) ? esc_attr( wp_unslash( $_GET['range'] ) ) : ''; ?>" />
						<input type="hidden" name="start_date" value="<?php echo ( ! empty( $_GET['start_date'] ) ) ? esc_attr( wp_unslash( $_GET['start_date'] ) ) : ''; ?>" />
						<input type="hidden" name="end_date" value="<?php echo ( ! empty( $_GET['end_date'] ) ) ? esc_attr( wp_unslash( $_GET['end_date'] ) ) : ''; ?>" />
						<input type="hidden" name="page" value="<?php echo ( ! empty( $_GET['page'] ) ) ? esc_attr( wp_unslash( $_GET['page'] ) ) : ''; ?>" />
						<input type="hidden" name="tab" value="<?php echo ( ! empty( $_GET['tab'] ) ) ? esc_attr( wp_unslash( $_GET['tab'] ) ) : ''; ?>" />
						<input type="hidden" name="report" value="<?php echo ( ! empty( $_GET['report'] ) ) ? esc_attr( wp_unslash( $_GET['report'] ) ) : ''; ?>" />
						<?php // @codingStandardsIgnoreEnd ?>
					<?php else : ?>
						<span><?php esc_html_e( 'No used coupons found', 'woocommerce' ); ?></span>
					<?php endif; ?>
				</div>
			</form>
		</div>
		<h4 class="section_title"><span><?php esc_html_e( 'Most popular', 'woocommerce' ); ?></span></h4>
		<div class="section">
			<table cellspacing="0">
				<?php
				$most_popular = $this->get_order_report_data(
					array(
						'data'         => array(
							'order_item_name' => array(
								'type'            => 'order_item',
								'order_item_type' => 'coupon',
								'function'        => '',
								'name'            => 'coupon_code',
							),
							'order_item_id'   => array(
								'type'            => 'order_item',
								'order_item_type' => 'coupon',
								'function'        => 'COUNT',
								'name'            => 'coupon_count',
							),
						),
						'where'        => array(
							array(
								'type'     => 'order_item',
								'key'      => 'order_item_type',
								'value'    => 'coupon',
								'operator' => '=',
							),
						),
						'order_by'     => 'coupon_count DESC',
						'group_by'     => 'order_item_name',
						'limit'        => 12,
						'query_type'   => 'get_results',
						'filter_range' => true,
					)
				);

				if ( ! empty( $most_popular ) && is_array( $most_popular ) ) {
					foreach ( $most_popular as $coupon ) {
						echo '<tr class="' . ( in_array( $coupon->coupon_code, $this->coupon_codes ) ? 'active' : '' ) . '">
							<td class="count" width="1%">' . esc_html( $coupon->coupon_count ) . '</td>
							<td class="name"><a href="' . esc_url( add_query_arg( 'coupon_codes', $coupon->coupon_code ) ) . '">' . esc_html( $coupon->coupon_code ) . '</a></td>
						</tr>';
					}
				} else {
					echo '<tr><td colspan="2">' . esc_html__( 'No coupons found in range', 'woocommerce' ) . '</td></tr>';
				}
				?>
			</table>
		</div>
		<h4 class="section_title"><span><?php esc_html_e( 'Most discount', 'woocommerce' ); ?></span></h4>
		<div class="section">
			<table cellspacing="0">
				<?php
				$most_discount = $this->get_order_report_data(
					array(
						'data'         => array(
							'order_item_name' => array(
								'type'            => 'order_item',
								'order_item_type' => 'coupon',
								'function'        => '',
								'name'            => 'coupon_code',
							),
							'discount_amount' => array(
								'type'            => 'order_item_meta',
								'order_item_type' => 'coupon',
								'function'        => 'SUM',
								'name'            => 'discount_amount',
							),
						),
						'where'        => array(
							array(
								'type'     => 'order_item',
								'key'      => 'order_item_type',
								'value'    => 'coupon',
								'operator' => '=',
							),
						),
						'order_by'     => 'discount_amount DESC',
						'group_by'     => 'order_item_name',
						'limit'        => 12,
						'query_type'   => 'get_results',
						'filter_range' => true,
					)
				);

				if ( ! empty( $most_discount ) && is_array( $most_discount ) ) {
					foreach ( $most_discount as $coupon ) {
						// @codingStandardsIgnoreStart
						echo '<tr class="' . ( in_array( $coupon->coupon_code, $this->coupon_codes ) ? 'active' : '' ) . '">
							<td class="count" width="1%">' . wc_price( $coupon->discount_amount ) . '</td>
							<td class="name"><a href="' . esc_url( add_query_arg( 'coupon_codes', $coupon->coupon_code ) ) . '">' . esc_html( $coupon->coupon_code ) . '</a></td>
						</tr>';
						// @codingStandardsIgnoreEnd
					}
				} else {
					echo '<tr><td colspan="3">' . esc_html__( 'No coupons found in range', 'woocommerce' ) . '</td></tr>';
				}
				?>
			</table>
		</div>
		<script type="text/javascript">
			jQuery( '.section_title' ).on( 'click', function() {
				var next_section = jQuery( this ).next( '.section' );

				if ( jQuery( next_section ).is( ':visible' ) ) {
					return false;
				}

				jQuery( '.section:visible' ).slideUp();
				jQuery( '.section_title' ).removeClass( 'open' );
				jQuery( this ).addClass( 'open' ).next( '.section' ).slideDown();

				return false;
			} );
			jQuery( '.section' ).slideUp( 100, function() {
				<?php if ( empty( $this->coupon_codes ) ) : ?>
					jQuery( '.section_title:eq(1)' ).trigger( 'click' );
				<?php else : ?>
					jQuery( '.section_title:eq(0)' ).trigger( 'click' );
				<?php endif; ?>
			} );
		</script>
		<?php
	}

	/**
	 * Output an export link.
	 */
	public function get_export_button() {
		$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( wp_unslash( $_GET['range'] ) ) : '7day';
		?>
		<a
			href="#"
			download="report-<?php echo esc_attr( $current_range ); ?>-<?php echo esc_attr( date_i18n( 'Y-m-d', current_time( 'timestamp' ) ) ); ?>.csv"
			class="export_csv"
			data-export="chart"
			data-xaxes="<?php esc_attr_e( 'Date', 'woocommerce' ); ?>"
			data-groupby="<?php echo esc_attr( $this->chart_groupby ); ?>"
		>
			<?php esc_html_e( 'Export CSV', 'woocommerce' ); ?>
		</a>
		<?php
	}

	/**
	 * Get the main chart.
	 */
	public function get_main_chart() {
		global $wp_locale;

		// Get orders and dates in range - we want the SUM of order totals, COUNT of order items, COUNT of orders, and the date.
		$order_coupon_counts_query = array(
			'data'         => array(
				'order_item_name' => array(
					'type'            => 'order_item',
					'order_item_type' => 'coupon',
					'function'        => 'COUNT',
					'name'            => 'order_coupon_count',
				),
				'post_date'       => array(
					'type'     => 'post_data',
					'function' => '',
					'name'     => 'post_date',
				),
			),
			'where'        => array(
				array(
					'key'      => 'order_item_type',
					'value'    => 'coupon',
					'operator' => '=',
				),
			),
			'group_by'     => $this->group_by_query,
			'order_by'     => 'post_date ASC',
			'query_type'   => 'get_results',
			'filter_range' => true,
			'order_types'  => wc_get_order_types( 'order-count' ),
		);

		$order_discount_amounts_query = array(
			'data'         => array(
				'discount_amount' => array(
					'type'            => 'order_item_meta',
					'order_item_type' => 'coupon',
					'function'        => 'SUM',
					'name'            => 'discount_amount',
				),
				'post_date'       => array(
					'type'     => 'post_data',
					'function' => '',
					'name'     => 'post_date',
				),
			),
			'where'        => array(
				array(
					'key'      => 'order_item_type',
					'value'    => 'coupon',
					'operator' => '=',
				),
			),
			'group_by'     => $this->group_by_query . ', order_item_name',
			'order_by'     => 'post_date ASC',
			'query_type'   => 'get_results',
			'filter_range' => true,
			'order_types'  => wc_get_order_types( 'order-count' ),
		);

		if ( ! empty( $this->coupon_codes ) ) {
			$coupon_code_query = array(
				'type'     => 'order_item',
				'key'      => 'order_item_name',
				'value'    => $this->coupon_codes,
				'operator' => 'IN',
			);

			$order_coupon_counts_query['where'][]    = $coupon_code_query;
			$order_discount_amounts_query['where'][] = $coupon_code_query;
		}

		$order_coupon_counts    = $this->get_order_report_data( $order_coupon_counts_query );
		$order_discount_amounts = $this->get_order_report_data( $order_discount_amounts_query );

		// Prepare data for report.
		$order_coupon_counts    = $this->prepare_chart_data( $order_coupon_counts, 'post_date', 'order_coupon_count', $this->chart_interval, $this->start_date, $this->chart_groupby );
		$order_discount_amounts = $this->prepare_chart_data( $order_discount_amounts, 'post_date', 'discount_amount', $this->chart_interval, $this->start_date, $this->chart_groupby );

		// Encode in json format.
		$chart_data = wp_json_encode(
			array(
				'order_coupon_counts'    => array_values( $order_coupon_counts ),
				'order_discount_amounts' => array_values( $order_discount_amounts ),
			)
		);
		?>
		<div class="chart-container">
			<div class="chart-placeholder main"></div>
		</div>
		<script type="text/javascript">
			var main_chart;

			jQuery(function(){
				var order_data = JSON.parse( decodeURIComponent( '<?php echo rawurlencode( $chart_data ); ?>' ) );

				var drawGraph = function( highlight ) {
					var series = [
						{
							label: "<?php echo esc_js( __( 'Number of coupons used', 'woocommerce' ) ); ?>",
							data: order_data.order_coupon_counts,
							color: '<?php echo esc_js( $this->chart_colours['coupon_count'] ); ?>',
							bars: { fillColor: '<?php echo esc_js( $this->chart_colours['coupon_count'] ); ?>', fill: true, show: true, lineWidth: 0, barWidth: <?php echo esc_js( $this->barwidth ); ?> * 0.5, align: 'center' },
							shadowSize: 0,
							hoverable: false
						},
						{
							label: "<?php echo esc_js( __( 'Discount amount', 'woocommerce' ) ); ?>",
							data: order_data.order_discount_amounts,
							yaxis: 2,
							color: '<?php echo esc_js( $this->chart_colours['discount_amount'] ); ?>',
							points: { show: true, radius: 5, lineWidth: 3, fillColor: '#fff', fill: true },
							lines: { show: true, lineWidth: 4, fill: false },
							shadowSize: 0,
							<?php echo $this->get_currency_tooltip(); ?><?php // @codingStandardsIgnoreLine ?>
						}
					];

					if ( highlight !== 'undefined' && series[ highlight ] ) {
						highlight_series = series[ highlight ];

						highlight_series.color = '#9c5d90';

						if ( highlight_series.bars )
							highlight_series.bars.fillColor = '#9c5d90';

						if ( highlight_series.lines ) {
							highlight_series.lines.lineWidth = 5;
						}
					}

					main_chart = jQuery.plot(
						jQuery('.chart-placeholder.main'),
						series,
						{
							legend: {
								show: false
							},
							grid: {
								color: '#aaa',
								borderColor: 'transparent',
								borderWidth: 0,
								hoverable: true
							},
							xaxes: [ {
								color: '#aaa',
								position: "bottom",
								tickColor: 'transparent',
								mode: "time",
								timeformat: "<?php echo ( 'day' === $this->chart_groupby ) ? '%d %b' : '%b'; ?>",
								monthNames: JSON.parse( decodeURIComponent( '<?php echo rawurlencode( wp_json_encode( array_values( $wp_locale->month_abbrev ) ) ); ?>' ) ),
								tickLength: 1,
								minTickSize: [1, "<?php echo esc_js( $this->chart_groupby ); ?>"],
								font: {
									color: "#aaa"
								}
							} ],
							yaxes: [
								{
									min: 0,
									minTickSize: 1,
									tickDecimals: 0,
									color: '#ecf0f1',
									font: { color: "#aaa" }
								},
								{
									position: "right",
									min: 0,
									tickDecimals: 2,
									alignTicksWithAxis: 1,
									color: 'transparent',
									font: { color: "#aaa" }
								}
							],
						}
					);

					jQuery('.chart-placeholder').trigger( 'resize' );
				}

				drawGraph();

				jQuery('.highlight_series').on( 'mouseenter',
					function() {
						drawGraph( jQuery(this).data('series') );
					} ).on( 'mouseleave',
					function() {
						drawGraph();
					}
				);
			});
		</script>
		<?php
	}
}
