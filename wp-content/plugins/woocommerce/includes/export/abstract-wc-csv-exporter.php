<?php
/**
 * Handles CSV export.
 *
 * @package  WooCommerce\Export
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_CSV_Exporter Class.
 */
abstract class WC_CSV_Exporter {

	/**
	 * Type of export used in filter names.
	 *
	 * @var string
	 */
	protected $export_type = '';

	/**
	 * Filename to export to.
	 *
	 * @var string
	 */
	protected $filename = 'wc-export.csv';

	/**
	 * Batch limit.
	 *
	 * @var integer
	 */
	protected $limit = 50;

	/**
	 * Number exported.
	 *
	 * @var integer
	 */
	protected $exported_row_count = 0;

	/**
	 * Raw data to export.
	 *
	 * @var array
	 */
	protected $row_data = array();

	/**
	 * Total rows to export.
	 *
	 * @var integer
	 */
	protected $total_rows = 0;

	/**
	 * Columns ids and names.
	 *
	 * @var array
	 */
	protected $column_names = array();

	/**
	 * List of columns to export, or empty for all.
	 *
	 * @var array
	 */
	protected $columns_to_export = array();

	/**
	 * The delimiter parameter sets the field delimiter (one character only).
	 *
	 * @var string
	 */
	protected $delimiter = ',';

	/**
	 * Prepare data that will be exported.
	 */
	abstract public function prepare_data_to_export();

	/**
	 * Return an array of supported column names and ids.
	 *
	 * @since 3.1.0
	 * @return array
	 */
	public function get_column_names() {
		return apply_filters( "woocommerce_{$this->export_type}_export_column_names", $this->column_names, $this );
	}

	/**
	 * Set column names.
	 *
	 * @since 3.1.0
	 * @param array $column_names Column names array.
	 */
	public function set_column_names( $column_names ) {
		$this->column_names = array();

		foreach ( $column_names as $column_id => $column_name ) {
			$this->column_names[ wc_clean( $column_id ) ] = wc_clean( $column_name );
		}
	}

	/**
	 * Return an array of columns to export.
	 *
	 * @since 3.1.0
	 * @return array
	 */
	public function get_columns_to_export() {
		return $this->columns_to_export;
	}

	/**
	 * Return the delimiter to use in CSV file
	 *
	 * @since 3.9.0
	 * @return string
	 */
	public function get_delimiter() {
		return apply_filters( "woocommerce_{$this->export_type}_export_delimiter", $this->delimiter );
	}

	/**
	 * Set columns to export.
	 *
	 * @since 3.1.0
	 * @param array $columns Columns array.
	 */
	public function set_columns_to_export( $columns ) {
		$this->columns_to_export = array_map( 'wc_clean', $columns );
	}

	/**
	 * See if a column is to be exported or not.
	 *
	 * @since 3.1.0
	 * @param  string $column_id ID of the column being exported.
	 * @return boolean
	 */
	public function is_column_exporting( $column_id ) {
		$column_id         = strstr( $column_id, ':' ) ? current( explode( ':', $column_id ) ) : $column_id;
		$columns_to_export = $this->get_columns_to_export();

		if ( empty( $columns_to_export ) ) {
			return true;
		}

		if ( in_array( $column_id, $columns_to_export, true ) || 'meta' === $column_id ) {
			return true;
		}

		return false;
	}

	/**
	 * Return default columns.
	 *
	 * @since 3.1.0
	 * @return array
	 */
	public function get_default_column_names() {
		return array();
	}

	/**
	 * Do the export.
	 *
	 * @since 3.1.0
	 */
	public function export() {
		$this->prepare_data_to_export();
		$this->send_headers();
		$this->send_content( chr( 239 ) . chr( 187 ) . chr( 191 ) . $this->export_column_headers() . $this->get_csv_data() );
		die();
	}

	/**
	 * Set the export headers.
	 *
	 * @since 3.1.0
	 */
	public function send_headers() {
		if ( function_exists( 'gc_enable' ) ) {
			gc_enable(); // phpcs:ignore PHPCompatibility.FunctionUse.NewFunctions.gc_enableFound
		}
		if ( function_exists( 'apache_setenv' ) ) {
			@apache_setenv( 'no-gzip', 1 ); // @codingStandardsIgnoreLine
		}
		@ini_set( 'zlib.output_compression', 'Off' ); // @codingStandardsIgnoreLine
		@ini_set( 'output_buffering', 'Off' ); // @codingStandardsIgnoreLine
		@ini_set( 'output_handler', '' ); // @codingStandardsIgnoreLine
		ignore_user_abort( true );
		wc_set_time_limit( 0 );
		wc_nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=' . $this->get_filename() );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );
	}

	/**
	 * Set filename to export to.
	 *
	 * @param  string $filename Filename to export to.
	 */
	public function set_filename( $filename ) {
		$this->filename = sanitize_file_name( str_replace( '.csv', '', $filename ) . '.csv' );
	}

	/**
	 * Generate and return a filename.
	 *
	 * @return string
	 */
	public function get_filename() {
		return sanitize_file_name( apply_filters( "woocommerce_{$this->export_type}_export_get_filename", $this->filename ) );
	}

	/**
	 * Set the export content.
	 *
	 * @since 3.1.0
	 * @param string $csv_data All CSV content.
	 */
	public function send_content( $csv_data ) {
		echo $csv_data; // @codingStandardsIgnoreLine
	}

	/**
	 * Get CSV data for this export.
	 *
	 * @since 3.1.0
	 * @return string
	 */
	protected function get_csv_data() {
		return $this->export_rows();
	}

	/**
	 * Export column headers in CSV format.
	 *
	 * @since 3.1.0
	 * @return string
	 */
	protected function export_column_headers() {
		$columns    = $this->get_column_names();
		$export_row = array();
		$buffer     = fopen( 'php://output', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen
		ob_start();

		foreach ( $columns as $column_id => $column_name ) {
			if ( ! $this->is_column_exporting( $column_id ) ) {
				continue;
			}
			$export_row[] = $this->format_data( $column_name );
		}

		$this->fputcsv( $buffer, $export_row );

		return ob_get_clean();
	}

	/**
	 * Get data that will be exported.
	 *
	 * @since 3.1.0
	 * @return array
	 */
	protected function get_data_to_export() {
		return $this->row_data;
	}

	/**
	 * Export rows in CSV format.
	 *
	 * @since 3.1.0
	 * @return string
	 */
	protected function export_rows() {
		$data   = $this->get_data_to_export();
		$buffer = fopen( 'php://output', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen
		ob_start();

		array_walk( $data, array( $this, 'export_row' ), $buffer );

		return apply_filters( "woocommerce_{$this->export_type}_export_rows", ob_get_clean(), $this );
	}

	/**
	 * Export rows to an array ready for the CSV.
	 *
	 * @since 3.1.0
	 * @param array    $row_data Data to export.
	 * @param string   $key Column being exported.
	 * @param resource $buffer Output buffer.
	 */
	protected function export_row( $row_data, $key, $buffer ) {
		$columns    = $this->get_column_names();
		$export_row = array();

		foreach ( $columns as $column_id => $column_name ) {
			if ( ! $this->is_column_exporting( $column_id ) ) {
				continue;
			}
			if ( isset( $row_data[ $column_id ] ) ) {
				$export_row[] = $this->format_data( $row_data[ $column_id ] );
			} else {
				$export_row[] = '';
			}
		}

		$this->fputcsv( $buffer, $export_row );

		++ $this->exported_row_count;
	}

	/**
	 * Get batch limit.
	 *
	 * @since 3.1.0
	 * @return int
	 */
	public function get_limit() {
		return apply_filters( "woocommerce_{$this->export_type}_export_batch_limit", $this->limit, $this );
	}

	/**
	 * Set batch limit.
	 *
	 * @since 3.1.0
	 * @param int $limit Limit to export.
	 */
	public function set_limit( $limit ) {
		$this->limit = absint( $limit );
	}

	/**
	 * Get count of records exported.
	 *
	 * @since 3.1.0
	 * @return int
	 */
	public function get_total_exported() {
		return $this->exported_row_count;
	}

	/**
	 * Escape a string to be used in a CSV context
	 *
	 * Malicious input can inject formulas into CSV files, opening up the possibility
	 * for phishing attacks and disclosure of sensitive information.
	 *
	 * Additionally, Excel exposes the ability to launch arbitrary commands through
	 * the DDE protocol.
	 *
	 * @see http://www.contextis.com/resources/blog/comma-separated-vulnerabilities/
	 * @see https://hackerone.com/reports/72785
	 *
	 * @since 3.1.0
	 * @param string $data CSV field to escape.
	 * @return string
	 */
	public function escape_data( $data ) {
		$active_content_triggers = array( '=', '+', '-', '@' );

		if ( in_array( mb_substr( $data, 0, 1 ), $active_content_triggers, true ) ) {
			$data = "'" . $data;
		}

		return $data;
	}

	/**
	 * Format and escape data ready for the CSV file.
	 *
	 * @since 3.1.0
	 * @param  string $data Data to format.
	 * @return string
	 */
	public function format_data( $data ) {
		if ( ! is_scalar( $data ) ) {
			if ( is_a( $data, 'WC_Datetime' ) ) {
				$data = $data->date( 'Y-m-d G:i:s' );
			} else {
				$data = ''; // Not supported.
			}
		} elseif ( is_bool( $data ) ) {
			$data = $data ? 1 : 0;
		}

		$use_mb = function_exists( 'mb_convert_encoding' );

		if ( $use_mb ) {
			$encoding = mb_detect_encoding( $data, 'UTF-8, ISO-8859-1', true );
			$data     = 'UTF-8' === $encoding ? $data : utf8_encode( $data );
		}

		return $this->escape_data( $data );
	}

	/**
	 * Format term ids to names.
	 *
	 * @since 3.1.0
	 * @param  array  $term_ids Term IDs to format.
	 * @param  string $taxonomy Taxonomy name.
	 * @return string
	 */
	public function format_term_ids( $term_ids, $taxonomy ) {
		$term_ids = wp_parse_id_list( $term_ids );

		if ( ! count( $term_ids ) ) {
			return '';
		}

		$formatted_terms = array();

		if ( is_taxonomy_hierarchical( $taxonomy ) ) {
			foreach ( $term_ids as $term_id ) {
				$formatted_term = array();
				$ancestor_ids   = array_reverse( get_ancestors( $term_id, $taxonomy ) );

				foreach ( $ancestor_ids as $ancestor_id ) {
					$term = get_term( $ancestor_id, $taxonomy );
					if ( $term && ! is_wp_error( $term ) ) {
						$formatted_term[] = $term->name;
					}
				}

				$term = get_term( $term_id, $taxonomy );

				if ( $term && ! is_wp_error( $term ) ) {
					$formatted_term[] = $term->name;
				}

				$formatted_terms[] = implode( ' > ', $formatted_term );
			}
		} else {
			foreach ( $term_ids as $term_id ) {
				$term = get_term( $term_id, $taxonomy );

				if ( $term && ! is_wp_error( $term ) ) {
					$formatted_terms[] = $term->name;
				}
			}
		}

		return $this->implode_values( $formatted_terms );
	}

	/**
	 * Implode CSV cell values using commas by default, and wrapping values
	 * which contain the separator.
	 *
	 * @since  3.2.0
	 * @param  array $values Values to implode.
	 * @return string
	 */
	protected function implode_values( $values ) {
		$values_to_implode = array();

		foreach ( $values as $value ) {
			$value               = (string) is_scalar( $value ) ? html_entity_decode( $value, ENT_QUOTES ) : '';
			$values_to_implode[] = str_replace( ',', '\\,', $value );
		}

		return implode( ', ', $values_to_implode );
	}

	/**
	 * Write to the CSV file, ensuring escaping works across versions of
	 * PHP.
	 *
	 * PHP 5.5.4 uses '\' as the default escape character. This is not RFC-4180 compliant.
	 * \0 disables the escape character.
	 *
	 * @see https://bugs.php.net/bug.php?id=43225
	 * @see https://bugs.php.net/bug.php?id=50686
	 * @see https://github.com/woocommerce/woocommerce/issues/19514
	 * @since 3.4.0
	 * @see https://github.com/woocommerce/woocommerce/issues/24579
	 * @since 3.9.0
	 * @param resource $buffer Resource we are writing to.
	 * @param array    $export_row Row to export.
	 */
	protected function fputcsv( $buffer, $export_row ) {

		if ( version_compare( PHP_VERSION, '5.5.4', '<' ) ) {
			ob_start();
			$temp = fopen( 'php://output', 'w' ); // @codingStandardsIgnoreLine
    		fputcsv( $temp, $export_row, $this->get_delimiter(), '"' ); // @codingStandardsIgnoreLine
			fclose( $temp ); // @codingStandardsIgnoreLine
			$row = ob_get_clean();
			$row = str_replace( '\\"', '\\""', $row );
			fwrite( $buffer, $row ); // @codingStandardsIgnoreLine
		} else {
			fputcsv( $buffer, $export_row, $this->get_delimiter(), '"', "\0" ); // @codingStandardsIgnoreLine
		}
	}
}
