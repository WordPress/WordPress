<?php
/**
 * WC_Report_Coupon_Usage
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin/Reports
 * @version     2.1.0
 */
class WC_Report_Coupon_Usage extends WC_Admin_Report {

	public $chart_colours = array();
	public $coupon_codes = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		if ( isset( $_GET['coupon_codes'] ) && is_array( $_GET['coupon_codes'] ) ) {
			$this->coupon_codes = array_filter( array_map( 'sanitize_text_field', $_GET['coupon_codes'] ) );
		} elseif ( isset( $_GET['coupon_codes'] ) ) {
			$this->coupon_codes = array_filter( array( sanitize_text_field( $_GET['coupon_codes'] ) ) );
		}
	}

	/**
	 * Get the legend for the main chart sidebar
	 * @return array
	 */
	public function get_chart_legend() {
		$legend   = array();

		$total_discount 	= $this->get_order_report_data( array(
			'data' => array(
				'discount_amount' => array(
					'type'            => 'order_item_meta',
					'order_item_type' => 'coupon',
					'function'        => 'SUM',
					'name'            => 'discount_amount'
				)
			),
			'where' => array(
				array(
					'type'     => 'order_item',
					'key'      => 'order_item_name',
					'value'    => $this->coupon_codes,
					'operator' => 'IN'
				)
			),
			'query_type'   => 'get_var',
			'filter_range' => true
		) );

		$total_coupons    = absint( $this->get_order_report_data( array(
			'data' => array(
				'order_item_name' => array(
					'type'            => 'order_item',
					'order_item_type' => 'coupon',
					'function'        => 'COUNT',
					'name'            => 'order_coupon_count'
				)
			),
			'where' => array(
				array(
					'type'     => 'order_item',
					'key'      => 'order_item_name',
					'value'    => $this->coupon_codes,
					'operator' => 'IN'
				)
			),
			'query_type'   => 'get_var',
			'filter_range' => true
		) ) );

		$legend[] = array(
			'title' => sprintf( __( '%s discounts in total', 'woocommerce' ), '<strong>' . wc_price( $total_discount ) . '</strong>' ),
			'color' => $this->chart_colours['discount_amount'],
			'highlight_series' => 1
		);

		$legend[] = array(
			'title' => sprintf( __( '%s coupons used in total', 'woocommerce' ), '<strong>' . $total_coupons . '</strong>' ),
			'color' => $this->chart_colours['coupon_count' ],
			'highlight_series' => 0
		);

		return $legend;
	}

	/**
	 * Output the report
	 */
	public function output_report() {
		$ranges = array(
			'year'         => __( 'Year', 'woocommerce' ),
			'last_month'   => __( 'Last Month', 'woocommerce' ),
			'month'        => __( 'This Month', 'woocommerce' ),
			'7day'         => __( 'Last 7 Days', 'woocommerce' )
		);

		$this->chart_colours = array(
			'discount_amount' => '#3498db',
			'coupon_count'    => '#d4d9dc',
		);

		$current_range = ! empty( $_GET['range'] ) ? $_GET['range'] : '7day';

		if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) ) {
			$current_range = '7day';
		}

		$this->calculate_current_range( $current_range );

		include( WC()->plugin_path() . '/includes/admin/views/html-report-by-date.php');
	}

	/**
	 * [get_chart_widgets description]
	 * @return array
	 */
	public function get_chart_widgets() {
		$widgets = array();

		$widgets[] = array(
			'title'    => '',
			'callback' => array( $this, 'coupons_widget' )
		);

		return $widgets;
	}

	/**
	 * Product selection
	 * @return void
	 */
	public function coupons_widget() {
		?>
		<h4 class="section_title"><span><?php _e( 'Filter by coupon', 'woocommerce' ); ?></span></h4>
		<div class="section">
			<form method="GET">
				<div>
					<?php
						$used_coupons = $this->get_order_report_data( array(
							'data' => array(
								'order_item_name' => array(
									'type'            => 'order_item',
									'order_item_type' => 'coupon',
									'function'        => '',
									'distinct'        => true,
									'name'            => 'order_item_name'
								)
							),
							'where' => array(
								array(
									'key'      => 'order_item_type',
									'value'    => 'coupon',
									'operator' => '='
								)
							),
							'query_type'   => 'get_col',
							'filter_range' => false
						) );

						if ( $used_coupons ) :
					?>
						<select id="coupon_codes" name="coupon_codes" class="chosen_select" data-placeholder="<?php _e( 'Choose coupons&hellip;', 'woocommerce' ); ?>" style="width:100%;">
							<option value=""><?php _e( 'All coupons', 'woocommerce' ); ?></option>
							<?php
								foreach ( $used_coupons as $coupon ) {
									echo '<option value="' . esc_attr( $coupon ) . '" ' . selected( in_array( $coupon, $this->coupon_codes ), true, false ) . '>' . $coupon . '</option>';
								}
							 ?>
						</select>
						<input type="submit" class="submit button" value="<?php _e( 'Show', 'woocommerce' ); ?>" />
						<input type="hidden" name="range" value="<?php if ( ! empty( $_GET['range'] ) ) echo esc_attr( $_GET['range'] ) ?>" />
						<input type="hidden" name="start_date" value="<?php if ( ! empty( $_GET['start_date'] ) ) echo esc_attr( $_GET['start_date'] ) ?>" />
						<input type="hidden" name="end_date" value="<?php if ( ! empty( $_GET['end_date'] ) ) echo esc_attr( $_GET['end_date'] ) ?>" />
						<input type="hidden" name="page" value="<?php if ( ! empty( $_GET['page'] ) ) echo esc_attr( $_GET['page'] ) ?>" />
						<input type="hidden" name="tab" value="<?php if ( ! empty( $_GET['tab'] ) ) echo esc_attr( $_GET['tab'] ) ?>" />
						<input type="hidden" name="report" value="<?php if ( ! empty( $_GET['report'] ) ) echo esc_attr( $_GET['report'] ) ?>" />
						<script type="text/javascript">
							jQuery(function() {
								jQuery('select.chosen_select').chosen();
							});
						</script>
					<?php else : ?>
						<span><?php _e( 'No used coupons found', 'woocommerce' ); ?></span>
					<?php endif; ?>
				</div>
			</form>
		</div>
		<h4 class="section_title"><span><?php _e( 'Most Popular', 'woocommerce' ); ?></span></h4>
		<div class="section">
			<table cellspacing="0">
				<?php
				$most_popular = $this->get_order_report_data( array(
					'data' => array(
						'order_item_name' => array(
							'type'            => 'order_item',
							'order_item_type' => 'coupon',
							'function'        => '',
							'name'            => 'coupon_code'
						),
						'order_item_id' => array(
							'type'            => 'order_item',
							'order_item_type' => 'coupon',
							'function'        => 'COUNT',
							'name'            => 'coupon_count'
						),
					),
					'where' => array(
						array(
							'type'     => 'order_item',
							'key'      => 'order_item_type',
							'value'    => 'coupon',
							'operator' => '='
						)
					),
					'order_by'     => 'coupon_count DESC',
					'group_by'     => 'order_item_name',
					'limit'        => 12,
					'query_type'   => 'get_results',
					'filter_range' => true
				) );

				if ( $most_popular ) {
					foreach ( $most_popular as $coupon ) {
						echo '<tr class="' . ( in_array( $coupon->coupon_code, $this->coupon_codes ) ? 'active' : '' ) . '">
							<td class="count" width="1%">' . $coupon->coupon_count . '</td>
							<td class="name"><a href="' . add_query_arg( 'coupon_codes', $coupon->coupon_code ) . '">' . $coupon->coupon_code . '</a></td>
						</tr>';
					}
				} else {
					echo '<tr><td colspan="2">' . __( 'No coupons found in range', 'woocommerce' ) . '</td></tr>';
				}
				?>
			</table>
		</div>
		<h4 class="section_title"><span><?php _e( 'Most Discount', 'woocommerce' ); ?></span></h4>
		<div class="section">
			<table cellspacing="0">
				<?php
				$most_discount = $this->get_order_report_data( array(
					'data' => array(
						'order_item_name' => array(
							'type'            => 'order_item',
							'order_item_type' => 'coupon',
							'function'        => '',
							'name'            => 'coupon_code'
						),
						'discount_amount' => array(
							'type'            => 'order_item_meta',
							'order_item_type' => 'coupon',
							'function'        => 'SUM',
							'name'            => 'discount_amount'
						)
					),
					'where' => array(
						array(
							'type'     => 'order_item',
							'key'      => 'order_item_type',
							'value'    => 'coupon',
							'operator' => '='
						)
					),
					'order_by'     => 'discount_amount DESC',
					'group_by'     => 'order_item_name',
					'limit'        => 12,
					'query_type'   => 'get_results',
					'filter_range' => true
				) );

				if ( $most_discount ) {
					foreach ( $most_discount as $coupon ) {
						echo '<tr class="' . ( in_array( $coupon->coupon_code, $this->coupon_codes ) ? 'active' : '' ) . '">
							<td class="count" width="1%">' . wc_price( $coupon->discount_amount ) . '</td>
							<td class="name"><a href="' . add_query_arg( 'coupon_codes', $coupon->coupon_code ) . '">' . $coupon->coupon_code . '</a></td>
						</tr>';
					}
				} else {
					echo '<tr><td colspan="3">' . __( 'No coupons found in range', 'woocommerce' ) . '</td></tr>';
				}
				?>
			</table>
		</div>
		<script type="text/javascript">
			jQuery('.section_title').click(function(){
				var next_section = jQuery(this).next('.section');

				if ( jQuery(next_section).is(':visible') )
					return false;

				jQuery('.section:visible').slideUp();
				jQuery('.section_title').removeClass('open');
				jQuery(this).addClass('open').next('.section').slideDown();

				return false;
			});
			jQuery('.section').slideUp( 100, function() {
				<?php if ( empty( $this->coupon_codes ) ) : ?>
					jQuery('.section_title:eq(1)').click();
				<?php else : ?>
					jQuery('.section_title:eq(0)').click();
				<?php endif; ?>
			});
		</script>
		<?php
	}

	/**
	 * Output an export link
	 */
	public function get_export_button() {
		$current_range = ! empty( $_GET['range'] ) ? $_GET['range'] : '7day';
		?>
		<a
			href="#"
			download="report-<?php echo $current_range; ?>-<?php echo date_i18n( 'Y-m-d', current_time('timestamp') ); ?>.csv"
			class="export_csv"
			data-export="chart"
			data-xaxes="<?php _e( 'Date', 'woocommerce' ); ?>"
			data-groupby="<?php echo $this->chart_groupby; ?>"
		>
			<?php _e( 'Export CSV', 'woocommerce' ); ?>
		</a>
		<?php
	}

	/**
	 * Get the main chart
	 * @return string
	 */
	public function get_main_chart() {
		global $wp_locale;

		// Get orders and dates in range - we want the SUM of order totals, COUNT of order items, COUNT of orders, and the date
		$order_coupon_counts  = $this->get_order_report_data( array(
			'data' => array(
				'order_item_name' => array(
					'type'            => 'order_item',
					'order_item_type' => 'coupon',
					'function'        => 'COUNT',
					'name'            => 'order_coupon_count'
				),
				'post_date' => array(
					'type'     => 'post_data',
					'function' => '',
					'name'     => 'post_date'
				),
			),
			'where' => array(
				array(
					'type'     => 'order_item',
					'key'      => 'order_item_name',
					'value'    => $this->coupon_codes,
					'operator' => 'IN'
				)
			),
			'group_by'     => $this->group_by_query,
			'order_by'     => 'post_date ASC',
			'query_type'   => 'get_results',
			'filter_range' => true
		) );

		$order_discount_amounts = $this->get_order_report_data( array(
			'data' => array(
				'discount_amount' => array(
					'type'            => 'order_item_meta',
					'order_item_type' => 'coupon',
					'function'        => 'SUM',
					'name'            => 'discount_amount'
				),
				'post_date' => array(
					'type'     => 'post_data',
					'function' => '',
					'name'     => 'post_date'
				),
			),
			'where' => array(
				array(
					'type'     => 'order_item',
					'key'      => 'order_item_name',
					'value'    => $this->coupon_codes,
					'operator' => 'IN'
				)
			),
			'group_by'     => $this->group_by_query . ', order_item_name',
			'order_by'     => 'post_date ASC',
			'query_type'   => 'get_results',
			'filter_range' => true
		) );

		// Prepare data for report
		$order_coupon_counts = $this->prepare_chart_data( $order_coupon_counts, 'post_date', 'order_coupon_count' , $this->chart_interval, $this->start_date, $this->chart_groupby );
		$order_discount_amounts = $this->prepare_chart_data( $order_discount_amounts, 'post_date', 'discount_amount', $this->chart_interval, $this->start_date, $this->chart_groupby );

		// Encode in json format
		$chart_data = json_encode( array(
			'order_coupon_counts'   => array_values( $order_coupon_counts ),
			'order_discount_amounts' => array_values( $order_discount_amounts )
		) );
		?>
		<div class="chart-container">
			<div class="chart-placeholder main"></div>
		</div>
		<script type="text/javascript">
			var main_chart;

			jQuery(function(){
				var order_data = jQuery.parseJSON( '<?php echo $chart_data; ?>' );

				var drawGraph = function( highlight ) {
					var series = [
						{
							label: "<?php echo esc_js( __( 'Number of coupons used', 'woocommerce' ) ) ?>",
							data: order_data.order_coupon_counts,
							color: '<?php echo $this->chart_colours['coupon_count' ]; ?>',
							bars: { fillColor: '<?php echo $this->chart_colours['coupon_count' ]; ?>', fill: true, show: true, lineWidth: 0, barWidth: <?php echo $this->barwidth; ?> * 0.5, align: 'center' },
							shadowSize: 0,
							hoverable: false
						},
						{
							label: "<?php echo esc_js( __( 'Discount amount', 'woocommerce' ) ) ?>",
							data: order_data.order_discount_amounts,
							yaxis: 2,
							color: '<?php echo $this->chart_colours['discount_amount']; ?>',
							points: { show: true, radius: 5, lineWidth: 3, fillColor: '#fff', fill: true },
							lines: { show: true, lineWidth: 4, fill: false },
							shadowSize: 0,
							prepend_tooltip: "<?php echo get_woocommerce_currency_symbol(); ?>"
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
								timeformat: "<?php if ( $this->chart_groupby == 'day' ) echo '%d %b'; else echo '%b'; ?>",
								monthNames: <?php echo json_encode( array_values( $wp_locale->month_abbrev ) ) ?>,
								tickLength: 1,
								minTickSize: [1, "<?php echo $this->chart_groupby; ?>"],
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

					jQuery('.chart-placeholder').resize();
				}

				drawGraph();

				jQuery('.highlight_series').hover(
					function() {
						drawGraph( jQuery(this).data('series') );
					},
					function() {
						drawGraph();
					}
				);
			});
		</script>
		<?php
	}
}
