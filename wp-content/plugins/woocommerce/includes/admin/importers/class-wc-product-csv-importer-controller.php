<?php
/**
 * Class WC_Product_CSV_Importer_Controller file.
 *
 * @package WooCommerce\Admin\Importers
 */

use Automattic\WooCommerce\Utilities\I18nUtil;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Importer' ) ) {
	return;
}

/**
 * Product importer controller - handles file upload and forms in admin.
 *
 * @package     WooCommerce\Admin\Importers
 * @version     3.1.0
 */
class WC_Product_CSV_Importer_Controller {

	/**
	 * The path to the current file.
	 *
	 * @var string
	 */
	protected $file = '';

	/**
	 * The current import step.
	 *
	 * @var string
	 */
	protected $step = '';

	/**
	 * Progress steps.
	 *
	 * @var array
	 */
	protected $steps = array();

	/**
	 * Errors.
	 *
	 * @var array
	 */
	protected $errors = array();

	/**
	 * The current delimiter for the file being read.
	 *
	 * @var string
	 */
	protected $delimiter = ',';

	/**
	 * Whether to use previous mapping selections.
	 *
	 * @var bool
	 */
	protected $map_preferences = false;

	/**
	 * Whether to skip existing products.
	 *
	 * @var bool
	 */
	protected $update_existing = false;

	/**
	 * The character encoding to use to interpret the input file, or empty string for autodetect.
	 *
	 * @var string
	 */
	protected $character_encoding = 'UTF-8';

	/**
	 * Get importer instance.
	 *
	 * @param  string $file File to import.
	 * @param  array  $args Importer arguments.
	 * @return WC_Product_CSV_Importer
	 */
	public static function get_importer( $file, $args = array() ) {
		$importer_class = apply_filters( 'woocommerce_product_csv_importer_class', 'WC_Product_CSV_Importer' );
		$args           = apply_filters( 'woocommerce_product_csv_importer_args', $args, $importer_class );
		return new $importer_class( $file, $args );
	}

	/**
	 * Check whether a file is a valid CSV file.
	 *
	 * @param string $file File path.
	 * @param bool   $check_path Whether to also check the file is located in a valid location (Default: true).
	 * @return bool
	 */
	public static function is_file_valid_csv( $file, $check_path = true ) {
		return wc_is_file_valid_csv( $file, $check_path );
	}

	/**
	 * Get all the valid filetypes for a CSV file.
	 *
	 * @return array
	 */
	protected static function get_valid_csv_filetypes() {
		return apply_filters(
			'woocommerce_csv_product_import_valid_filetypes',
			array(
				'csv' => 'text/csv',
				'txt' => 'text/plain',
			)
		);
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		$default_steps = array(
			'upload'  => array(
				'name'    => __( 'Upload CSV file', 'woocommerce' ),
				'view'    => array( $this, 'upload_form' ),
				'handler' => array( $this, 'upload_form_handler' ),
			),
			'mapping' => array(
				'name'    => __( 'Column mapping', 'woocommerce' ),
				'view'    => array( $this, 'mapping_form' ),
				'handler' => '',
			),
			'import'  => array(
				'name'    => __( 'Import', 'woocommerce' ),
				'view'    => array( $this, 'import' ),
				'handler' => '',
			),
			'done'    => array(
				'name'    => __( 'Done!', 'woocommerce' ),
				'view'    => array( $this, 'done' ),
				'handler' => '',
			),
		);

		$this->steps = apply_filters( 'woocommerce_product_csv_importer_steps', $default_steps );

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$this->step               = isset( $_REQUEST['step'] ) ? sanitize_key( $_REQUEST['step'] ) : current( array_keys( $this->steps ) );
		$this->file               = isset( $_REQUEST['file'] ) ? wc_clean( wp_unslash( $_REQUEST['file'] ) ) : '';
		$this->update_existing    = isset( $_REQUEST['update_existing'] ) ? (bool) $_REQUEST['update_existing'] : false;
		$this->delimiter          = ! empty( $_REQUEST['delimiter'] ) ? wc_clean( wp_unslash( $_REQUEST['delimiter'] ) ) : ',';
		$this->map_preferences    = isset( $_REQUEST['map_preferences'] ) ? (bool) $_REQUEST['map_preferences'] : false;
		$this->character_encoding = isset( $_REQUEST['character_encoding'] ) ? wc_clean( wp_unslash( $_REQUEST['character_encoding'] ) ) : 'UTF-8';
		// phpcs:enable

		// Import mappings for CSV data.
		include_once dirname( __FILE__ ) . '/mappings/mappings.php';

		if ( $this->map_preferences ) {
			add_filter( 'woocommerce_csv_product_import_mapped_columns', array( $this, 'auto_map_user_preferences' ), 9999 );
		}
	}

	/**
	 * Get the URL for the next step's screen.
	 *
	 * @param string $step  slug (default: current step).
	 * @return string       URL for next step if a next step exists.
	 *                      Admin URL if it's the last step.
	 *                      Empty string on failure.
	 */
	public function get_next_step_link( $step = '' ) {
		if ( ! $step ) {
			$step = $this->step;
		}

		$keys = array_keys( $this->steps );

		if ( end( $keys ) === $step ) {
			return admin_url();
		}

		$step_index = array_search( $step, $keys, true );

		if ( false === $step_index ) {
			return '';
		}

		$params = array(
			'step'               => $keys[ $step_index + 1 ],
			'file'               => str_replace( DIRECTORY_SEPARATOR, '/', $this->file ),
			'delimiter'          => $this->delimiter,
			'update_existing'    => $this->update_existing,
			'map_preferences'    => $this->map_preferences,
			'character_encoding' => $this->character_encoding,
			'_wpnonce'           => wp_create_nonce( 'woocommerce-csv-importer' ), // wp_nonce_url() escapes & to &amp; breaking redirects.
		);

		return add_query_arg( $params );
	}

	/**
	 * Output header view.
	 */
	protected function output_header() {
		include dirname( __FILE__ ) . '/views/html-csv-import-header.php';
	}

	/**
	 * Output steps view.
	 */
	protected function output_steps() {
		include dirname( __FILE__ ) . '/views/html-csv-import-steps.php';
	}

	/**
	 * Output footer view.
	 */
	protected function output_footer() {
		include dirname( __FILE__ ) . '/views/html-csv-import-footer.php';
	}

	/**
	 * Add error message.
	 *
	 * @param string $message Error message.
	 * @param array  $actions List of actions with 'url' and 'label'.
	 */
	protected function add_error( $message, $actions = array() ) {
		$this->errors[] = array(
			'message' => $message,
			'actions' => $actions,
		);
	}

	/**
	 * Add error message.
	 */
	protected function output_errors() {
		if ( ! $this->errors ) {
			return;
		}

		foreach ( $this->errors as $error ) {
			echo '<div class="error inline">';
			echo '<p>' . esc_html( $error['message'] ) . '</p>';

			if ( ! empty( $error['actions'] ) ) {
				echo '<p>';
				foreach ( $error['actions'] as $action ) {
					echo '<a class="button button-primary" href="' . esc_url( $action['url'] ) . '">' . esc_html( $action['label'] ) . '</a> ';
				}
				echo '</p>';
			}
			echo '</div>';
		}
	}

	/**
	 * Dispatch current step and show correct view.
	 */
	public function dispatch() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! empty( $_POST['save_step'] ) && ! empty( $this->steps[ $this->step ]['handler'] ) ) {
			call_user_func( $this->steps[ $this->step ]['handler'], $this );
		}
		$this->output_header();
		$this->output_steps();
		$this->output_errors();
		call_user_func( $this->steps[ $this->step ]['view'], $this );
		$this->output_footer();
	}

	/**
	 * Output information about the uploading process.
	 */
	protected function upload_form() {
		$bytes      = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
		$size       = size_format( $bytes );
		$upload_dir = wp_upload_dir();

		include dirname( __FILE__ ) . '/views/html-product-csv-import-form.php';
	}

	/**
	 * Handle the upload form and store options.
	 */
	public function upload_form_handler() {
		check_admin_referer( 'woocommerce-csv-importer' );

		$file = $this->handle_upload();

		if ( is_wp_error( $file ) ) {
			$this->add_error( $file->get_error_message() );
			return;
		} else {
			$this->file = $file;
		}

		wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}

	/**
	 * Handles the CSV upload and initial parsing of the file to prepare for
	 * displaying author import options.
	 *
	 * @return string|WP_Error
	 */
	public function handle_upload() {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce already verified in WC_Product_CSV_Importer_Controller::upload_form_handler()
		$file_url = isset( $_POST['file_url'] ) ? wc_clean( wp_unslash( $_POST['file_url'] ) ) : '';

		if ( empty( $file_url ) ) {
			if ( ! isset( $_FILES['import'] ) ) {
				return new WP_Error( 'woocommerce_product_csv_importer_upload_file_empty', __( 'File is empty. Please upload something more substantial. This error could also be caused by uploads being disabled in your php.ini or by post_max_size being defined as smaller than upload_max_filesize in php.ini.', 'woocommerce' ) );
			}

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
			if ( ! self::is_file_valid_csv( wc_clean( wp_unslash( $_FILES['import']['name'] ) ), false ) ) {
				return new WP_Error( 'woocommerce_product_csv_importer_upload_file_invalid', __( 'Invalid file type. The importer supports CSV and TXT file formats.', 'woocommerce' ) );
			}

			$overrides = array(
				'test_form' => false,
				'mimes'     => self::get_valid_csv_filetypes(),
			);
			$import    = $_FILES['import']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$upload    = wp_handle_upload( $import, $overrides );

			if ( isset( $upload['error'] ) ) {
				return new WP_Error( 'woocommerce_product_csv_importer_upload_error', $upload['error'] );
			}

			// Construct the object array.
			$object = array(
				'post_title'     => basename( $upload['file'] ),
				'post_content'   => $upload['url'],
				'post_mime_type' => $upload['type'],
				'guid'           => $upload['url'],
				'context'        => 'import',
				'post_status'    => 'private',
			);

			// Save the data.
			$id = wp_insert_attachment( $object, $upload['file'] );

			/*
			 * Schedule a cleanup for one day from now in case of failed
			 * import or missing wp_import_cleanup() call.
			 */
			wp_schedule_single_event( time() + DAY_IN_SECONDS, 'importer_scheduled_cleanup', array( $id ) );

			return $upload['file'];
		} elseif (
			( 0 === stripos( realpath( ABSPATH . $file_url ), ABSPATH ) ) &&
			file_exists( ABSPATH . $file_url )
		) {
			if ( ! self::is_file_valid_csv( ABSPATH . $file_url ) ) {
				return new WP_Error( 'woocommerce_product_csv_importer_upload_file_invalid', __( 'Invalid file type. The importer supports CSV and TXT file formats.', 'woocommerce' ) );
			}

			return ABSPATH . $file_url;
		}
		// phpcs:enable

		return new WP_Error( 'woocommerce_product_csv_importer_upload_invalid_file', __( 'Please upload or provide the link to a valid CSV file.', 'woocommerce' ) );
	}

	/**
	 * Mapping step.
	 */
	protected function mapping_form() {
		check_admin_referer( 'woocommerce-csv-importer' );
		$args = array(
			'lines'              => 1,
			'delimiter'          => $this->delimiter,
			'character_encoding' => $this->character_encoding,
		);

		$importer     = self::get_importer( $this->file, $args );
		$headers      = $importer->get_raw_keys();
		$mapped_items = $this->auto_map_columns( $headers );
		$sample       = current( $importer->get_raw_data() );

		if ( empty( $sample ) ) {
			$this->add_error(
				__( 'The file is empty or using a different encoding than UTF-8, please try again with a new file.', 'woocommerce' ),
				array(
					array(
						'url'   => admin_url( 'edit.php?post_type=product&page=product_importer' ),
						'label' => __( 'Upload a new file', 'woocommerce' ),
					),
				)
			);

			// Force output the errors in the same page.
			$this->output_errors();
			return;
		}

		include_once dirname( __FILE__ ) . '/views/html-csv-import-mapping.php';
	}

	/**
	 * Import the file if it exists and is valid.
	 */
	public function import() {
		// Displaying this page triggers Ajax action to run the import with a valid nonce,
		// therefore this page needs to be nonce protected as well.
		check_admin_referer( 'woocommerce-csv-importer' );

		if ( ! self::is_file_valid_csv( $this->file ) ) {
			$this->add_error( __( 'Invalid file type. The importer supports CSV and TXT file formats.', 'woocommerce' ) );
			$this->output_errors();
			return;
		}

		if ( ! is_file( $this->file ) ) {
			$this->add_error( __( 'The file does not exist, please try again.', 'woocommerce' ) );
			$this->output_errors();
			return;
		}

		if ( ! empty( $_POST['map_from'] ) && ! empty( $_POST['map_to'] ) ) {
			$mapping_from = wc_clean( wp_unslash( $_POST['map_from'] ) );
			$mapping_to   = wc_clean( wp_unslash( $_POST['map_to'] ) );

			// Save mapping preferences for future imports.
			update_user_option( get_current_user_id(), 'woocommerce_product_import_mapping', $mapping_to );
		} else {
			wp_redirect( esc_url_raw( $this->get_next_step_link( 'upload' ) ) );
			exit;
		}

		wp_localize_script(
			'wc-product-import',
			'wc_product_import_params',
			array(
				'import_nonce'       => wp_create_nonce( 'wc-product-import' ),
				'mapping'            => array(
					'from' => $mapping_from,
					'to'   => $mapping_to,
				),
				'file'               => $this->file,
				'update_existing'    => $this->update_existing,
				'delimiter'          => $this->delimiter,
				'character_encoding' => $this->character_encoding,
			)
		);
		wp_enqueue_script( 'wc-product-import' );

		include_once dirname( __FILE__ ) . '/views/html-csv-import-progress.php';
	}

	/**
	 * Done step.
	 */
	protected function done() {
		check_admin_referer( 'woocommerce-csv-importer' );
		$imported            = isset( $_GET['products-imported'] ) ? absint( $_GET['products-imported'] ) : 0;
		$imported_variations = isset( $_GET['products-imported-variations'] ) ? absint( $_GET['products-imported-variations'] ) : 0;
		$updated             = isset( $_GET['products-updated'] ) ? absint( $_GET['products-updated'] ) : 0;
		$failed              = isset( $_GET['products-failed'] ) ? absint( $_GET['products-failed'] ) : 0;
		$skipped             = isset( $_GET['products-skipped'] ) ? absint( $_GET['products-skipped'] ) : 0;
		$file_name           = isset( $_GET['file-name'] ) ? sanitize_text_field( wp_unslash( $_GET['file-name'] ) ) : '';
		$errors              = array_filter( (array) get_user_option( 'product_import_error_log' ) );

		include_once dirname( __FILE__ ) . '/views/html-csv-import-done.php';
	}

	/**
	 * Columns to normalize.
	 *
	 * @param  array $columns List of columns names and keys.
	 * @return array
	 */
	protected function normalize_columns_names( $columns ) {
		$normalized = array();

		foreach ( $columns as $key => $value ) {
			$normalized[ strtolower( $key ) ] = $value;
		}

		return $normalized;
	}

	/**
	 * Auto map column names.
	 *
	 * @param  array $raw_headers Raw header columns.
	 * @param  bool  $num_indexes If should use numbers or raw header columns as indexes.
	 * @return array
	 */
	protected function auto_map_columns( $raw_headers, $num_indexes = true ) {
		$weight_unit_label    = I18nUtil::get_weight_unit_label( get_option( 'woocommerce_weight_unit', 'kg' ) );
		$dimension_unit_label = I18nUtil::get_dimensions_unit_label( get_option( 'woocommerce_dimension_unit', 'cm' ) );

		/*
		 * @hooked wc_importer_generic_mappings - 10
		 * @hooked wc_importer_wordpress_mappings - 10
		 * @hooked wc_importer_default_english_mappings - 100
		 */
		$default_columns = $this->normalize_columns_names(
			apply_filters(
				'woocommerce_csv_product_import_mapping_default_columns',
				array(
					__( 'ID', 'woocommerce' )             => 'id',
					__( 'Type', 'woocommerce' )           => 'type',
					__( 'SKU', 'woocommerce' )            => 'sku',
					__( 'Name', 'woocommerce' )           => 'name',
					__( 'Published', 'woocommerce' )      => 'published',
					__( 'Is featured?', 'woocommerce' )   => 'featured',
					__( 'Visibility in catalog', 'woocommerce' ) => 'catalog_visibility',
					__( 'Short description', 'woocommerce' ) => 'short_description',
					__( 'Description', 'woocommerce' )    => 'description',
					__( 'Date sale price starts', 'woocommerce' ) => 'date_on_sale_from',
					__( 'Date sale price ends', 'woocommerce' ) => 'date_on_sale_to',
					__( 'Tax status', 'woocommerce' )     => 'tax_status',
					__( 'Tax class', 'woocommerce' )      => 'tax_class',
					__( 'In stock?', 'woocommerce' )      => 'stock_status',
					__( 'Stock', 'woocommerce' )          => 'stock_quantity',
					__( 'Backorders allowed?', 'woocommerce' ) => 'backorders',
					__( 'Low stock amount', 'woocommerce' ) => 'low_stock_amount',
					__( 'Sold individually?', 'woocommerce' ) => 'sold_individually',
					/* translators: %s: Weight unit */
					sprintf( __( 'Weight (%s)', 'woocommerce' ), $weight_unit_label ) => 'weight',
					/* translators: %s: Length unit */
					sprintf( __( 'Length (%s)', 'woocommerce' ), $dimension_unit_label ) => 'length',
					/* translators: %s: Width unit */
					sprintf( __( 'Width (%s)', 'woocommerce' ), $dimension_unit_label ) => 'width',
					/* translators: %s: Height unit */
					sprintf( __( 'Height (%s)', 'woocommerce' ), $dimension_unit_label ) => 'height',
					__( 'Allow customer reviews?', 'woocommerce' ) => 'reviews_allowed',
					__( 'Purchase note', 'woocommerce' )  => 'purchase_note',
					__( 'Sale price', 'woocommerce' )     => 'sale_price',
					__( 'Regular price', 'woocommerce' )  => 'regular_price',
					__( 'Categories', 'woocommerce' )     => 'category_ids',
					__( 'Tags', 'woocommerce' )           => 'tag_ids',
					__( 'Shipping class', 'woocommerce' ) => 'shipping_class_id',
					__( 'Images', 'woocommerce' )         => 'images',
					__( 'Download limit', 'woocommerce' ) => 'download_limit',
					__( 'Download expiry days', 'woocommerce' ) => 'download_expiry',
					__( 'Parent', 'woocommerce' )         => 'parent_id',
					__( 'Upsells', 'woocommerce' )        => 'upsell_ids',
					__( 'Cross-sells', 'woocommerce' )    => 'cross_sell_ids',
					__( 'Grouped products', 'woocommerce' ) => 'grouped_products',
					__( 'External URL', 'woocommerce' )   => 'product_url',
					__( 'Button text', 'woocommerce' )    => 'button_text',
					__( 'Position', 'woocommerce' )       => 'menu_order',
				),
				$raw_headers
			)
		);

		$special_columns = $this->get_special_columns(
			$this->normalize_columns_names(
				apply_filters(
					'woocommerce_csv_product_import_mapping_special_columns',
					array(
						/* translators: %d: Attribute number */
						__( 'Attribute %d name', 'woocommerce' ) => 'attributes:name',
						/* translators: %d: Attribute number */
						__( 'Attribute %d value(s)', 'woocommerce' ) => 'attributes:value',
						/* translators: %d: Attribute number */
						__( 'Attribute %d visible', 'woocommerce' ) => 'attributes:visible',
						/* translators: %d: Attribute number */
						__( 'Attribute %d global', 'woocommerce' ) => 'attributes:taxonomy',
						/* translators: %d: Attribute number */
						__( 'Attribute %d default', 'woocommerce' ) => 'attributes:default',
						/* translators: %d: Download number */
						__( 'Download %d ID', 'woocommerce' ) => 'downloads:id',
						/* translators: %d: Download number */
						__( 'Download %d name', 'woocommerce' ) => 'downloads:name',
						/* translators: %d: Download number */
						__( 'Download %d URL', 'woocommerce' ) => 'downloads:url',
						/* translators: %d: Meta number */
						__( 'Meta: %s', 'woocommerce' ) => 'meta:',
					),
					$raw_headers
				)
			)
		);

		$headers = array();
		foreach ( $raw_headers as $key => $field ) {
			$normalized_field  = strtolower( $field );
			$index             = $num_indexes ? $key : $field;
			$headers[ $index ] = $normalized_field;

			if ( isset( $default_columns[ $normalized_field ] ) ) {
				$headers[ $index ] = $default_columns[ $normalized_field ];
			} else {
				foreach ( $special_columns as $regex => $special_key ) {
					// Don't use the normalized field in the regex since meta might be case-sensitive.
					if ( preg_match( $regex, $field, $matches ) ) {
						$headers[ $index ] = $special_key . $matches[1];
						break;
					}
				}
			}
		}

		return apply_filters( 'woocommerce_csv_product_import_mapped_columns', $headers, $raw_headers );
	}

	/**
	 * Map columns using the user's latest import mappings.
	 *
	 * @param  array $headers Header columns.
	 * @return array
	 */
	public function auto_map_user_preferences( $headers ) {
		$mapping_preferences = get_user_option( 'woocommerce_product_import_mapping' );

		if ( ! empty( $mapping_preferences ) && is_array( $mapping_preferences ) ) {
			return $mapping_preferences;
		}

		return $headers;
	}

	/**
	 * Sanitize special column name regex.
	 *
	 * @param  string $value Raw special column name.
	 * @return string
	 */
	protected function sanitize_special_column_name_regex( $value ) {
		return '/' . str_replace( array( '%d', '%s' ), '(.*)', trim( quotemeta( $value ) ) ) . '/i';
	}

	/**
	 * Get special columns.
	 *
	 * @param  array $columns Raw special columns.
	 * @return array
	 */
	protected function get_special_columns( $columns ) {
		$formatted = array();

		foreach ( $columns as $key => $value ) {
			$regex = $this->sanitize_special_column_name_regex( $key );

			$formatted[ $regex ] = $value;
		}

		return $formatted;
	}

	/**
	 * Get mapping options.
	 *
	 * @param  string $item Item name.
	 * @return array
	 */
	protected function get_mapping_options( $item = '' ) {
		// Get index for special column names.
		$index = $item;

		if ( preg_match( '/\d+/', $item, $matches ) ) {
			$index = $matches[0];
		}

		// Properly format for meta field.
		$meta = str_replace( 'meta:', '', $item );

		// Available options.
		$weight_unit_label    = I18nUtil::get_weight_unit_label( get_option( 'woocommerce_weight_unit', 'kg' ) );
		$dimension_unit_label = I18nUtil::get_dimensions_unit_label( get_option( 'woocommerce_dimension_unit', 'cm' ) );
		$options              = array(
			'id'                 => __( 'ID', 'woocommerce' ),
			'type'               => __( 'Type', 'woocommerce' ),
			'sku'                => __( 'SKU', 'woocommerce' ),
			'name'               => __( 'Name', 'woocommerce' ),
			'published'          => __( 'Published', 'woocommerce' ),
			'featured'           => __( 'Is featured?', 'woocommerce' ),
			'catalog_visibility' => __( 'Visibility in catalog', 'woocommerce' ),
			'short_description'  => __( 'Short description', 'woocommerce' ),
			'description'        => __( 'Description', 'woocommerce' ),
			'price'              => array(
				'name'    => __( 'Price', 'woocommerce' ),
				'options' => array(
					'regular_price'     => __( 'Regular price', 'woocommerce' ),
					'sale_price'        => __( 'Sale price', 'woocommerce' ),
					'date_on_sale_from' => __( 'Date sale price starts', 'woocommerce' ),
					'date_on_sale_to'   => __( 'Date sale price ends', 'woocommerce' ),
				),
			),
			'tax_status'         => __( 'Tax status', 'woocommerce' ),
			'tax_class'          => __( 'Tax class', 'woocommerce' ),
			'stock_status'       => __( 'In stock?', 'woocommerce' ),
			'stock_quantity'     => _x( 'Stock', 'Quantity in stock', 'woocommerce' ),
			'backorders'         => __( 'Backorders allowed?', 'woocommerce' ),
			'low_stock_amount'   => __( 'Low stock amount', 'woocommerce' ),
			'sold_individually'  => __( 'Sold individually?', 'woocommerce' ),
			/* translators: %s: weight unit */
			'weight'             => sprintf( __( 'Weight (%s)', 'woocommerce' ), $weight_unit_label ),
			'dimensions'         => array(
				'name'    => __( 'Dimensions', 'woocommerce' ),
				'options' => array(
					/* translators: %s: dimension unit */
					'length' => sprintf( __( 'Length (%s)', 'woocommerce' ), $dimension_unit_label ),
					/* translators: %s: dimension unit */
					'width'  => sprintf( __( 'Width (%s)', 'woocommerce' ), $dimension_unit_label ),
					/* translators: %s: dimension unit */
					'height' => sprintf( __( 'Height (%s)', 'woocommerce' ), $dimension_unit_label ),
				),
			),
			'category_ids'       => __( 'Categories', 'woocommerce' ),
			'tag_ids'            => __( 'Tags (comma separated)', 'woocommerce' ),
			'tag_ids_spaces'     => __( 'Tags (space separated)', 'woocommerce' ),
			'shipping_class_id'  => __( 'Shipping class', 'woocommerce' ),
			'images'             => __( 'Images', 'woocommerce' ),
			'parent_id'          => __( 'Parent', 'woocommerce' ),
			'upsell_ids'         => __( 'Upsells', 'woocommerce' ),
			'cross_sell_ids'     => __( 'Cross-sells', 'woocommerce' ),
			'grouped_products'   => __( 'Grouped products', 'woocommerce' ),
			'external'           => array(
				'name'    => __( 'External product', 'woocommerce' ),
				'options' => array(
					'product_url' => __( 'External URL', 'woocommerce' ),
					'button_text' => __( 'Button text', 'woocommerce' ),
				),
			),
			'downloads'          => array(
				'name'    => __( 'Downloads', 'woocommerce' ),
				'options' => array(
					'downloads:id' . $index   => __( 'Download ID', 'woocommerce' ),
					'downloads:name' . $index => __( 'Download name', 'woocommerce' ),
					'downloads:url' . $index  => __( 'Download URL', 'woocommerce' ),
					'download_limit'          => __( 'Download limit', 'woocommerce' ),
					'download_expiry'         => __( 'Download expiry days', 'woocommerce' ),
				),
			),
			'attributes'         => array(
				'name'    => __( 'Attributes', 'woocommerce' ),
				'options' => array(
					'attributes:name' . $index     => __( 'Attribute name', 'woocommerce' ),
					'attributes:value' . $index    => __( 'Attribute value(s)', 'woocommerce' ),
					'attributes:taxonomy' . $index => __( 'Is a global attribute?', 'woocommerce' ),
					'attributes:visible' . $index  => __( 'Attribute visibility', 'woocommerce' ),
					'attributes:default' . $index  => __( 'Default attribute', 'woocommerce' ),
				),
			),
			'reviews_allowed'    => __( 'Allow customer reviews?', 'woocommerce' ),
			'purchase_note'      => __( 'Purchase note', 'woocommerce' ),
			'meta:' . $meta      => __( 'Import as meta data', 'woocommerce' ),
			'menu_order'         => __( 'Position', 'woocommerce' ),
		);

		return apply_filters( 'woocommerce_csv_product_import_mapping_options', $options, $item );
	}
}
