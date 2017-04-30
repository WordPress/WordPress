<?php
/**
 * Admin Reports
 *
 * Functions used for displaying sales and customer reports in admin.
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin/Reports
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Admin_Reports' ) ) :

/**
 * WC_Admin_Reports Class
 */
class WC_Admin_Reports {

	/**
	 * Handles output of the reports page in admin.
	 */
	public static function output() {
		$reports        = self::get_reports();
		$first_tab      = array_keys( $reports );
		$current_tab    = ! empty( $_GET['tab'] ) ? sanitize_title( $_GET['tab'] ) : $first_tab[0];
		$current_report = isset( $_GET['report'] ) ? sanitize_title( $_GET['report'] ) : current( array_keys( $reports[ $current_tab ]['reports'] ) );

		include_once( 'reports/class-wc-admin-report.php' );
		include_once( 'views/html-admin-page-reports.php' );
	}

	/**
	 * Returns the definitions for the reports to show in admin.
	 *
	 * @return array
	 */
	public static function get_reports() {
		$reports = array(
			'orders'     => array(
				'title'  => __( 'Orders', 'woocommerce' ),
				'reports' => array(
					"sales_by_date"    => array(
						'title'       => __( 'Sales by date', 'woocommerce' ),
						'description' => '',
						'hide_title'  => true,
						'callback'    => array( __CLASS__, 'get_report' )
					),
					"sales_by_product"     => array(
						'title'       => __( 'Sales by product', 'woocommerce' ),
						'description' => '',
						'hide_title'  => true,
						'callback'    => array( __CLASS__, 'get_report' )
					),
					"sales_by_category" => array(
						'title'       => __( 'Sales by category', 'woocommerce' ),
						'description' => '',
						'hide_title'  => true,
						'callback'    => array( __CLASS__, 'get_report' )
					),
					"coupon_usage" => array(
						'title'       => __( 'Coupons by date', 'woocommerce' ),
						'description' => '',
						'hide_title'  => true,
						'callback'    => array( __CLASS__, 'get_report' )
					)
				)
			),
			'customers' => array(
				'title'  => __( 'Customers', 'woocommerce' ),
				'reports' => array(
					"customers" => array(
						'title'       => __( 'Customers vs. Guests', 'woocommerce' ),
						'description' => '',
						'hide_title'  => true,
						'callback'    => array( __CLASS__, 'get_report' )
					),
					"customer_list" => array(
						'title'       => __( 'Customer List', 'woocommerce' ),
						'description' => '',
						'hide_title'  => true,
						'callback'    => array( __CLASS__, 'get_report' )
					),
				)
			),
			'stock'     => array(
				'title'  => __( 'Stock', 'woocommerce' ),
				'reports' => array(
					"low_in_stock" => array(
						'title'       => __( 'Low in stock', 'woocommerce' ),
						'description' => '',
						'hide_title'  => true,
						'callback'    => array( __CLASS__, 'get_report' )
					),
					"out_of_stock" => array(
						'title'       => __( 'Out of stock', 'woocommerce' ),
						'description' => '',
						'hide_title'  => true,
						'callback'    => array( __CLASS__, 'get_report' )
					),
					"most_stocked" => array(
						'title'       => __( 'Most Stocked', 'woocommerce' ),
						'description' => '',
						'hide_title'  => true,
						'callback'    => array( __CLASS__, 'get_report' )
					),
				)
			)
		);

		if ( get_option( 'woocommerce_calc_taxes' ) == 'yes' ) {
			$reports['taxes'] = array(
				'title'  => __( 'Taxes', 'woocommerce' ),
				'reports' => array(
					"taxes_by_code" => array(
						'title'       => __( 'Taxes by code', 'woocommerce' ),
						'description' => '',
						'hide_title'  => true,
						'callback'    => array( __CLASS__, 'get_report' )
					),
					"taxes_by_date" => array(
						'title'       => __( 'Taxes by date', 'woocommerce' ),
						'description' => '',
						'hide_title'  => true,
						'callback'    => array( __CLASS__, 'get_report' )
					),
				)
			);
		}

		$reports = apply_filters( 'woocommerce_admin_reports', $reports );
		$reports = apply_filters( 'woocommerce_reports_charts', $reports ); // Backwards compat

		foreach ( $reports as $key => $report_group ) {
			if ( isset( $reports[ $key ]['charts'] ) ) {
				$reports[ $key ]['reports'] = $reports[ $key ]['charts'];
			}

			foreach ( $reports[ $key ]['reports'] as $report_key => $report ) {
				if ( isset( $reports[ $key ]['reports'][ $report_key ]['function'] ) ) {
					$reports[ $key ]['reports'][ $report_key ]['callback'] = $reports[ $key ]['reports'][ $report_key ]['function'];
				}
			}
		}

		return $reports;
	}

	/**
	 * Get a report from our reports subfolder
	 */
	public static function get_report( $name ) {
		$name  = sanitize_title( str_replace( '_', '-', $name ) );
		$class = 'WC_Report_' . str_replace( '-', '_', $name );

		include_once( 'reports/class-wc-report-' . $name . '.php' );

		if ( ! class_exists( $class ) )
			return;

		$report = new $class();
		$report->output_report();
	}
}

endif;