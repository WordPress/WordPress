<?php
/**
 * Sales by category report functionality
 *
 * @package WooCommerce\Admin\Reporting
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WC_Report_Sales_By_Category
 *
 * @package     WooCommerce\Admin\Reports
 * @version     2.1.0
 */
class WC_Report_Sales_By_Category extends WC_Admin_Report {

	/**
	 * Chart colors.
	 *
	 * @var array
	 */
	public $chart_colours = array();

	/**
	 * Categories ids.
	 *
	 * @var array
	 */
	public $show_categories = array();

	/**
	 * Item sales.
	 *
	 * @var array
	 */
	private $item_sales = array();

	/**
	 * Item sales and times.
	 *
	 * @var array
	 */
	private $item_sales_and_times = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( isset( $_GET['show_categories'] ) ) {
			$this->show_categories = is_array( $_GET['show_categories'] ) ? array_map( 'absint', $_GET['show_categories'] ) : array( absint( $_GET['show_categories'] ) );
		}
	}

	/**
	 * Get all product ids in a category (and its children).
	 *
	 * @param  int $category_id Category ID.
	 * @return array
	 */
	public function get_products_in_category( $category_id ) {
		$term_ids    = get_term_children( $category_id, 'product_cat' );
		$term_ids[]  = $category_id;
		$product_ids = get_objects_in_term( $term_ids, 'product_cat' );

		return array_unique( apply_filters( 'woocommerce_report_sales_by_category_get_products_in_category', $product_ids, $category_id ) );
	}

	/**
	 * Get the legend for the main chart sidebar.
	 *
	 * @return array
	 */
	public function get_chart_legend() {

		if ( empty( $this->show_categories ) ) {
			return array();
		}

		$legend = array();
		$index  = 0;

		foreach ( $this->show_categories as $category ) {

			$category    = get_term( $category, 'product_cat' );
			$total       = 0;
			$product_ids = $this->get_products_in_category( $category->term_id );

			foreach ( $product_ids as $id ) {

				if ( isset( $this->item_sales[ $id ] ) ) {
					$total += $this->item_sales[ $id ];
				}
			}

			$legend[] = array(
				/* translators: 1: total items sold 2: category name */
				'title'            => sprintf( __( '%1$s sales in %2$s', 'woocommerce' ), '<strong>' . wc_price( $total ) . '</strong>', $category->name ),
				'color'            => isset( $this->chart_colours[ $index ] ) ? $this->chart_colours[ $index ] : $this->chart_colours[0],
				'highlight_series' => $index,
			);

			$index++;
		}

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

		$this->chart_colours = array( '#3498db', '#34495e', '#1abc9c', '#2ecc71', '#f1c40f', '#e67e22', '#e74c3c', '#2980b9', '#8e44ad', '#2c3e50', '#16a085', '#27ae60', '#f39c12', '#d35400', '#c0392b' );

		$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( wp_unslash( $_GET['range'] ) ) : '7day';

		if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) ) {
			$current_range = '7day';
		}

		$this->check_current_range_nonce( $current_range );
		$this->calculate_current_range( $current_range );

		// Get item sales data.
		if ( ! empty( $this->show_categories ) ) {
			$order_items = $this->get_order_report_data(
				array(
					'data'         => array(
						'_product_id' => array(
							'type'            => 'order_item_meta',
							'order_item_type' => 'line_item',
							'function'        => '',
							'name'            => 'product_id',
						),
						'_line_total' => array(
							'type'            => 'order_item_meta',
							'order_item_type' => 'line_item',
							'function'        => 'SUM',
							'name'            => 'order_item_amount',
						),
						'post_date'   => array(
							'type'     => 'post_data',
							'function' => '',
							'name'     => 'post_date',
						),
					),
					'group_by'     => 'ID, product_id, post_date',
					'query_type'   => 'get_results',
					'filter_range' => true,
				)
			);

			$this->item_sales           = array();
			$this->item_sales_and_times = array();

			if ( is_array( $order_items ) ) {

				foreach ( $order_items as $order_item ) {

					switch ( $this->chart_groupby ) {
						case 'day':
							$time = strtotime( gmdate( 'Ymd', strtotime( $order_item->post_date ) ) ) * 1000;
							break;
						case 'month':
						default:
							$time = strtotime( gmdate( 'Ym', strtotime( $order_item->post_date ) ) . '01' ) * 1000;
							break;
					}

					$this->item_sales_and_times[ $time ][ $order_item->product_id ] = isset( $this->item_sales_and_times[ $time ][ $order_item->product_id ] ) ? $this->item_sales_and_times[ $time ][ $order_item->product_id ] + $order_item->order_item_amount : $order_item->order_item_amount;

					$this->item_sales[ $order_item->product_id ] = isset( $this->item_sales[ $order_item->product_id ] ) ? $this->item_sales[ $order_item->product_id ] + $order_item->order_item_amount : $order_item->order_item_amount;
				}
			}
		}

		include WC()->plugin_path() . '/includes/admin/views/html-report-by-date.php';
	}

	/**
	 * Get chart widgets.
	 *
	 * @return array
	 */
	public function get_chart_widgets() {

		return array(
			array(
				'title'    => __( 'Categories', 'woocommerce' ),
				'callback' => array( $this, 'category_widget' ),
			),
		);
	}

	/**
	 * Output category widget.
	 */
	public function category_widget() {

		$categories = get_terms( 'product_cat', array( 'orderby' => 'name' ) );
		?>
		<form method="GET">
			<div>
				<select multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select categories&hellip;', 'woocommerce' ); ?>" class="wc-enhanced-select" id="show_categories" name="show_categories[]" style="width: 205px;">
					<?php
						$r                 = array();
						$r['pad_counts']   = 1;
						$r['hierarchical'] = 1;
						$r['hide_empty']   = 1;
						$r['value']        = 'id';
						$r['selected']     = $this->show_categories;

						include_once WC()->plugin_path() . '/includes/walkers/class-wc-product-cat-dropdown-walker.php';

						echo wc_walk_category_dropdown_tree( $categories, 0, $r ); // @codingStandardsIgnoreLine
					?>
				</select>
				<?php // @codingStandardsIgnoreStart ?>
				<a href="#" class="select_none"><?php esc_html_e( 'None', 'woocommerce' ); ?></a>
				<a href="#" class="select_all"><?php esc_html_e( 'All', 'woocommerce' ); ?></a>
				<button type="submit" class="submit button" value="<?php esc_attr_e( 'Show', 'woocommerce' ); ?>"><?php esc_html_e( 'Show', 'woocommerce' ); ?></button>
				<input type="hidden" name="range" value="<?php echo ( ! empty( $_GET['range'] ) ) ? esc_attr( wp_unslash( $_GET['range'] ) ) : ''; ?>" />
				<input type="hidden" name="start_date" value="<?php echo ( ! empty( $_GET['start_date'] ) ) ? esc_attr( wp_unslash( $_GET['start_date'] ) ) : ''; ?>" />
				<input type="hidden" name="end_date" value="<?php echo ( ! empty( $_GET['end_date'] ) ) ? esc_attr( wp_unslash( $_GET['end_date'] ) ) : ''; ?>" />
				<input type="hidden" name="page" value="<?php echo ( ! empty( $_GET['page'] ) ) ? esc_attr( wp_unslash( $_GET['page'] ) ) : ''; ?>" />
				<input type="hidden" name="tab" value="<?php echo ( ! empty( $_GET['tab'] ) ) ? esc_attr( wp_unslash( $_GET['tab'] ) ) : ''; ?>" />
				<input type="hidden" name="report" value="<?php echo ( ! empty( $_GET['report'] ) ) ? esc_attr( wp_unslash( $_GET['report'] ) ) : ''; ?>" />
				<?php // @codingStandardsIgnoreEnd ?>
			</div>
			<script type="text/javascript">
				jQuery(function(){
					// Select all/None
					jQuery( '.chart-widget' ).on( 'click', '.select_all', function() {
						jQuery(this).closest( 'div' ).find( 'select option' ).attr( 'selected', 'selected' );
						jQuery(this).closest( 'div' ).find('select').trigger( 'change' );
						return false;
					});

					jQuery( '.chart-widget').on( 'click', '.select_none', function() {
						jQuery(this).closest( 'div' ).find( 'select option' ).prop( 'selected', false );
						jQuery(this).closest( 'div' ).find('select').trigger( 'change' );
						return false;
					});
				});
			</script>
		</form>
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

		if ( empty( $this->show_categories ) ) {
			?>
			<div class="chart-container">
				<p class="chart-prompt"><?php esc_html_e( 'Choose a category to view stats', 'woocommerce' ); ?></p>
			</div>
			<?php
		} else {
			$chart_data = array();
			$index      = 0;

			foreach ( $this->show_categories as $category ) {

				$category            = get_term( $category, 'product_cat' );
				$product_ids         = $this->get_products_in_category( $category->term_id );
				$category_chart_data = array();

				for ( $i = 0; $i <= $this->chart_interval; $i ++ ) {

					$interval_total = 0;

					switch ( $this->chart_groupby ) {
						case 'day':
							$time = strtotime( gmdate( 'Ymd', strtotime( "+{$i} DAY", $this->start_date ) ) ) * 1000;
							break;
						case 'month':
						default:
							$time = strtotime( gmdate( 'Ym', strtotime( "+{$i} MONTH", $this->start_date ) ) . '01' ) * 1000;
							break;
					}

					foreach ( $product_ids as $id ) {

						if ( isset( $this->item_sales_and_times[ $time ][ $id ] ) ) {
							$interval_total += $this->item_sales_and_times[ $time ][ $id ];
						}
					}

					$category_chart_data[] = array( $time, (float) wc_format_decimal( $interval_total, wc_get_price_decimals() ) );
				}

				$chart_data[ $category->term_id ]['category'] = $category->name;
				$chart_data[ $category->term_id ]['data']     = $category_chart_data;

				$index++;
			}
			?>
			<div class="chart-container">
				<div class="chart-placeholder main"></div>
			</div>
			<?php // @codingStandardsIgnoreStart ?>
			<script type="text/javascript">
				var main_chart;

				jQuery(function(){
					var drawGraph = function( highlight ) {
						var series = [
							<?php
								$index = 0;
								foreach ( $chart_data as $data ) {
									$color  = isset( $this->chart_colours[ $index ] ) ? $this->chart_colours[ $index ] : $this->chart_colours[0];
									$width  = $this->barwidth / sizeof( $chart_data );
									$offset = ( $width * $index );
									$series = $data['data'];

									foreach ( $series as $key => $series_data ) {
										$series[ $key ][0] = $series_data[0] + $offset;
									}

									$series = wp_json_encode( $series );

									echo '{
											label: "' . esc_js( $data['category'] ) . '",
											data: JSON.parse( decodeURIComponent( "' . rawurlencode( $series ) . '" ) ),
											color: "' . $color . '",
											bars: {
												fillColor: "' . $color . '",
												fill: true,
												show: true,
												lineWidth: 1,
												align: "center",
												barWidth: ' . $width * 0.75 . ',
												stack: false
											},
											' . $this->get_currency_tooltip() . ',
											enable_tooltip: true,
											prepend_label: true
										},';
									$index++;
								}
							?>
						];

						if ( highlight !== 'undefined' && series[ highlight ] ) {
							highlight_series = series[ highlight ];

							highlight_series.color = '#9c5d90';

							if ( highlight_series.bars ) {
								highlight_series.bars.fillColor = '#9c5d90';
							}

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
									reserveSpace: true,
									position: "bottom",
									tickColor: 'transparent',
									mode: "time",
									timeformat: "<?php echo ( 'day' === $this->chart_groupby ) ? '%d %b' : '%b'; ?>",
									monthNames: JSON.parse( decodeURIComponent( '<?php echo rawurlencode( wp_json_encode( array_values( $wp_locale->month_abbrev ) ) ); ?>' ) ),
									tickLength: 1,
									minTickSize: [1, "<?php echo $this->chart_groupby; ?>"],
									tickSize: [1, "<?php echo $this->chart_groupby; ?>"],
									font: {
										color: "#aaa"
									}
								} ],
								yaxes: [
									{
										min: 0,
										tickDecimals: 2,
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
			<?php // @codingStandardsIgnoreEnd ?>
			<?php
		}
	}
}
