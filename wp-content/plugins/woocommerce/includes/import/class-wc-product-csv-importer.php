<?php
/**
 * WooCommerce Product CSV importer
 *
 * @package WooCommerce\Import
 * @version 3.1.0
 */

use Automattic\WooCommerce\Utilities\ArrayUtil;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Include dependencies.
 */
if ( ! class_exists( 'WC_Product_Importer', false ) ) {
	include_once dirname( __FILE__ ) . '/abstract-wc-product-importer.php';
}

if ( ! class_exists( 'WC_Product_CSV_Importer_Controller', false ) ) {
	include_once WC_ABSPATH . 'includes/admin/importers/class-wc-product-csv-importer-controller.php';
}

/**
 * WC_Product_CSV_Importer Class.
 */
class WC_Product_CSV_Importer extends WC_Product_Importer {

	/**
	 * Tracks current row being parsed.
	 *
	 * @var integer
	 */
	protected $parsing_raw_data_index = 0;

	/**
	 * Initialize importer.
	 *
	 * @param string $file   File to read.
	 * @param array  $params Arguments for the parser.
	 */
	public function __construct( $file, $params = array() ) {
		$default_args = array(
			'start_pos'        => 0, // File pointer start.
			'end_pos'          => -1, // File pointer end.
			'lines'            => -1, // Max lines to read.
			'mapping'          => array(), // Column mapping. csv_heading => schema_heading.
			'parse'            => false, // Whether to sanitize and format data.
			'update_existing'  => false, // Whether to update existing items.
			'delimiter'        => ',', // CSV delimiter.
			'prevent_timeouts' => true, // Check memory and time usage and abort if reaching limit.
			'enclosure'        => '"', // The character used to wrap text in the CSV.
			'escape'           => "\0", // PHP uses '\' as the default escape character. This is not RFC-4180 compliant. This disables the escape character.
		);

		$this->params = wp_parse_args( $params, $default_args );
		$this->file   = $file;

		if ( isset( $this->params['mapping']['from'], $this->params['mapping']['to'] ) ) {
			$this->params['mapping'] = array_combine( $this->params['mapping']['from'], $this->params['mapping']['to'] );
		}

		// Import mappings for CSV data.
		include_once dirname( dirname( __FILE__ ) ) . '/admin/importers/mappings/mappings.php';

		$this->read_file();
	}

	/**
	 * Convert a string from the input encoding to UTF-8.
	 *
	 * @param string $value The string to convert.
	 * @return string The converted string.
	 */
	private function adjust_character_encoding( $value ) {
		$encoding = $this->params['character_encoding'];
		return 'UTF-8' === $encoding ? $value : mb_convert_encoding( $value, 'UTF-8', $encoding );
	}

	/**
	 * Read file.
	 */
	protected function read_file() {
		if ( ! WC_Product_CSV_Importer_Controller::is_file_valid_csv( $this->file ) ) {
			wp_die( esc_html__( 'Invalid file type. The importer supports CSV and TXT file formats.', 'woocommerce' ) );
		}

		$handle = fopen( $this->file, 'r' ); // @codingStandardsIgnoreLine.

		if ( false !== $handle ) {
			$this->raw_keys = array_map( 'trim', fgetcsv( $handle, 0, $this->params['delimiter'], $this->params['enclosure'], $this->params['escape'] ) ); // @codingStandardsIgnoreLine

			if ( ArrayUtil::is_truthy( $this->params, 'character_encoding' ) ) {
				$this->raw_keys = array_map( array( $this, 'adjust_character_encoding' ), $this->raw_keys );
			}

			// Remove line breaks in keys, to avoid mismatch mapping of keys.
			$this->raw_keys = wc_clean( wp_unslash( $this->raw_keys ) );

			// Remove BOM signature from the first item.
			if ( isset( $this->raw_keys[0] ) ) {
				$this->raw_keys[0] = $this->remove_utf8_bom( $this->raw_keys[0] );
			}

			if ( 0 !== $this->params['start_pos'] ) {
				fseek( $handle, (int) $this->params['start_pos'] );
			}

			while ( 1 ) {
				$row = fgetcsv( $handle, 0, $this->params['delimiter'], $this->params['enclosure'], $this->params['escape'] ); // @codingStandardsIgnoreLine

				if ( false !== $row ) {
					if ( ArrayUtil::is_truthy( $this->params, 'character_encoding' ) ) {
						$row = array_map( array( $this, 'adjust_character_encoding' ), $row );
					}

					$this->raw_data[]                                 = $row;
					$this->file_positions[ count( $this->raw_data ) ] = ftell( $handle );

					if ( ( $this->params['end_pos'] > 0 && ftell( $handle ) >= $this->params['end_pos'] ) || 0 === --$this->params['lines'] ) {
						break;
					}
				} else {
					break;
				}
			}

			$this->file_position = ftell( $handle );
		}

		if ( ! empty( $this->params['mapping'] ) ) {
			$this->set_mapped_keys();
		}

		if ( $this->params['parse'] ) {
			$this->set_parsed_data();
		}
	}

	/**
	 * Remove UTF-8 BOM signature.
	 *
	 * @param string $string String to handle.
	 *
	 * @return string
	 */
	protected function remove_utf8_bom( $string ) {
		if ( 'efbbbf' === substr( bin2hex( $string ), 0, 6 ) ) {
			$string = substr( $string, 3 );
		}

		return $string;
	}

	/**
	 * Set file mapped keys.
	 */
	protected function set_mapped_keys() {
		$mapping = $this->params['mapping'];

		foreach ( $this->raw_keys as $key ) {
			$this->mapped_keys[] = isset( $mapping[ $key ] ) ? $mapping[ $key ] : $key;
		}
	}

	/**
	 * Parse relative field and return product ID.
	 *
	 * Handles `id:xx` and SKUs.
	 *
	 * If mapping to an id: and the product ID does not exist, this link is not
	 * valid.
	 *
	 * If mapping to a SKU and the product ID does not exist, a temporary object
	 * will be created so it can be updated later.
	 *
	 * @param string $value Field value.
	 *
	 * @return int|string
	 */
	public function parse_relative_field( $value ) {
		global $wpdb;

		if ( empty( $value ) ) {
			return '';
		}

		// IDs are prefixed with id:.
		if ( preg_match( '/^id:(\d+)$/', $value, $matches ) ) {
			$id = intval( $matches[1] );

			// If original_id is found, use that instead of the given ID since a new placeholder must have been created already.
			$original_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_original_id' AND meta_value = %s;", $id ) ); // WPCS: db call ok, cache ok.

			if ( $original_id ) {
				return absint( $original_id );
			}

			// See if the given ID maps to a valid product allready.
			$existing_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type IN ( 'product', 'product_variation' ) AND ID = %d;", $id ) ); // WPCS: db call ok, cache ok.

			if ( $existing_id ) {
				return absint( $existing_id );
			}

			// If we're not updating existing posts, we may need a placeholder product to map to.
			if ( ! $this->params['update_existing'] ) {
				$product = wc_get_product_object( 'simple' );
				$product->set_name( 'Import placeholder for ' . $id );
				$product->set_status( 'importing' );
				$product->add_meta_data( '_original_id', $id, true );
				$id = $product->save();
			}

			return $id;
		}

		$id = wc_get_product_id_by_sku( $value );

		if ( $id ) {
			return $id;
		}

		try {
			$product = wc_get_product_object( 'simple' );
			$product->set_name( 'Import placeholder for ' . $value );
			$product->set_status( 'importing' );
			$product->set_sku( $value );
			$id = $product->save();

			if ( $id && ! is_wp_error( $id ) ) {
				return $id;
			}
		} catch ( Exception $e ) {
			return '';
		}

		return '';
	}

	/**
	 * Parse the ID field.
	 *
	 * If we're not doing an update, create a placeholder product so mapping works
	 * for rows following this one.
	 *
	 * @param string $value Field value.
	 *
	 * @return int
	 */
	public function parse_id_field( $value ) {
		global $wpdb;

		$id = absint( $value );

		if ( ! $id ) {
			return 0;
		}

		// See if this maps to an ID placeholder already.
		$original_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_original_id' AND meta_value = %s;", $id ) ); // WPCS: db call ok, cache ok.

		if ( $original_id ) {
			return absint( $original_id );
		}

		// Not updating? Make sure we have a new placeholder for this ID.
		if ( ! $this->params['update_existing'] ) {
			$mapped_keys      = $this->get_mapped_keys();
			$sku_column_index = absint( array_search( 'sku', $mapped_keys, true ) );
			$row_sku          = isset( $this->raw_data[ $this->parsing_raw_data_index ][ $sku_column_index ] ) ? $this->raw_data[ $this->parsing_raw_data_index ][ $sku_column_index ] : '';
			$id_from_sku      = $row_sku ? wc_get_product_id_by_sku( $row_sku ) : '';

			// If row has a SKU, make sure placeholder was not made already.
			if ( $id_from_sku ) {
				return $id_from_sku;
			}

			$product = wc_get_product_object( 'simple' );
			$product->set_name( 'Import placeholder for ' . $id );
			$product->set_status( 'importing' );
			$product->add_meta_data( '_original_id', $id, true );

			// If row has a SKU, make sure placeholder has it too.
			if ( $row_sku ) {
				$product->set_sku( $row_sku );
			}
			$id = $product->save();
		}

		return $id && ! is_wp_error( $id ) ? $id : 0;
	}

	/**
	 * Parse relative comma-delineated field and return product ID.
	 *
	 * @param string $value Field value.
	 *
	 * @return array
	 */
	public function parse_relative_comma_field( $value ) {
		if ( empty( $value ) ) {
			return array();
		}

		return array_filter( array_map( array( $this, 'parse_relative_field' ), $this->explode_values( $value ) ) );
	}

	/**
	 * Parse a comma-delineated field from a CSV.
	 *
	 * @param string $value Field value.
	 *
	 * @return array
	 */
	public function parse_comma_field( $value ) {
		if ( empty( $value ) && '0' !== $value ) {
			return array();
		}

		$value = $this->unescape_data( $value );
		return array_map( 'wc_clean', $this->explode_values( $value ) );
	}

	/**
	 * Parse a field that is generally '1' or '0' but can be something else.
	 *
	 * @param string $value Field value.
	 *
	 * @return bool|string
	 */
	public function parse_bool_field( $value ) {
		if ( '0' === $value ) {
			return false;
		}

		if ( '1' === $value ) {
			return true;
		}

		// Don't return explicit true or false for empty fields or values like 'notify'.
		return wc_clean( $value );
	}

	/**
	 * Parse a float value field.
	 *
	 * @param string $value Field value.
	 *
	 * @return float|string
	 */
	public function parse_float_field( $value ) {
		if ( '' === $value ) {
			return $value;
		}

		// Remove the ' prepended to fields that start with - if needed.
		$value = $this->unescape_data( $value );

		return floatval( $value );
	}

	/**
	 * Parse the stock qty field.
	 *
	 * @param string $value Field value.
	 *
	 * @return float|string
	 */
	public function parse_stock_quantity_field( $value ) {
		if ( '' === $value ) {
			return $value;
		}

		// Remove the ' prepended to fields that start with - if needed.
		$value = $this->unescape_data( $value );

		return wc_stock_amount( $value );
	}

	/**
	 * Parse the tax status field.
	 *
	 * @param string $value Field value.
	 *
	 * @return string
	 */
	public function parse_tax_status_field( $value ) {
		if ( '' === $value ) {
			return $value;
		}

		// Remove the ' prepended to fields that start with - if needed.
		$value = $this->unescape_data( $value );

		if ( 'true' === strtolower( $value ) || 'false' === strtolower( $value ) ) {
			$value = wc_string_to_bool( $value ) ? 'taxable' : 'none';
		}

		return wc_clean( $value );
	}

	/**
	 * Parse a category field from a CSV.
	 * Categories are separated by commas and subcategories are "parent > subcategory".
	 *
	 * @param string $value Field value.
	 *
	 * @return array of arrays with "parent" and "name" keys.
	 */
	public function parse_categories_field( $value ) {
		if ( empty( $value ) ) {
			return array();
		}

		$row_terms  = $this->explode_values( $value );
		$categories = array();

		foreach ( $row_terms as $row_term ) {
			$parent = null;
			$_terms = array_map( 'trim', explode( '>', $row_term ) );
			$total  = count( $_terms );

			foreach ( $_terms as $index => $_term ) {
				// Don't allow users without capabilities to create new categories.
				if ( ! current_user_can( 'manage_product_terms' ) ) {
					break;
				}

				$term = wp_insert_term( $_term, 'product_cat', array( 'parent' => intval( $parent ) ) );

				if ( is_wp_error( $term ) ) {
					if ( $term->get_error_code() === 'term_exists' ) {
						// When term exists, error data should contain existing term id.
						$term_id = $term->get_error_data();
					} else {
						break; // We cannot continue on any other error.
					}
				} else {
					// New term.
					$term_id = $term['term_id'];
				}

				// Only requires assign the last category.
				if ( ( 1 + $index ) === $total ) {
					$categories[] = $term_id;
				} else {
					// Store parent to be able to insert or query categories based in parent ID.
					$parent = $term_id;
				}
			}
		}

		return $categories;
	}

	/**
	 * Parse a tag field from a CSV.
	 *
	 * @param string $value Field value.
	 *
	 * @return array
	 */
	public function parse_tags_field( $value ) {
		if ( empty( $value ) ) {
			return array();
		}

		$value = $this->unescape_data( $value );
		$names = $this->explode_values( $value );
		$tags  = array();

		foreach ( $names as $name ) {
			$term = get_term_by( 'name', $name, 'product_tag' );

			if ( ! $term || is_wp_error( $term ) ) {
				$term = (object) wp_insert_term( $name, 'product_tag' );
			}

			if ( ! is_wp_error( $term ) ) {
				$tags[] = $term->term_id;
			}
		}

		return $tags;
	}

	/**
	 * Parse a tag field from a CSV with space separators.
	 *
	 * @param string $value Field value.
	 *
	 * @return array
	 */
	public function parse_tags_spaces_field( $value ) {
		if ( empty( $value ) ) {
			return array();
		}

		$value = $this->unescape_data( $value );
		$names = $this->explode_values( $value, ' ' );
		$tags  = array();

		foreach ( $names as $name ) {
			$term = get_term_by( 'name', $name, 'product_tag' );

			if ( ! $term || is_wp_error( $term ) ) {
				$term = (object) wp_insert_term( $name, 'product_tag' );
			}

			if ( ! is_wp_error( $term ) ) {
				$tags[] = $term->term_id;
			}
		}

		return $tags;
	}

	/**
	 * Parse a shipping class field from a CSV.
	 *
	 * @param string $value Field value.
	 *
	 * @return int
	 */
	public function parse_shipping_class_field( $value ) {
		if ( empty( $value ) ) {
			return 0;
		}

		$term = get_term_by( 'name', $value, 'product_shipping_class' );

		if ( ! $term || is_wp_error( $term ) ) {
			$term = (object) wp_insert_term( $value, 'product_shipping_class' );
		}

		if ( is_wp_error( $term ) ) {
			return 0;
		}

		return $term->term_id;
	}

	/**
	 * Parse images list from a CSV. Images can be filenames or URLs.
	 *
	 * @param string $value Field value.
	 *
	 * @return array
	 */
	public function parse_images_field( $value ) {
		if ( empty( $value ) ) {
			return array();
		}

		$images    = array();
		$separator = apply_filters( 'woocommerce_product_import_image_separator', ',' );

		foreach ( $this->explode_values( $value, $separator ) as $image ) {
			if ( stristr( $image, '://' ) ) {
				$images[] = esc_url_raw( $image );
			} else {
				$images[] = sanitize_file_name( $image );
			}
		}

		return $images;
	}

	/**
	 * Parse dates from a CSV.
	 * Dates requires the format YYYY-MM-DD and time is optional.
	 *
	 * @param string $value Field value.
	 *
	 * @return string|null
	 */
	public function parse_date_field( $value ) {
		if ( empty( $value ) ) {
			return null;
		}

		if ( preg_match( '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])([ 01-9:]*)$/', $value ) ) {
			// Don't include the time if the field had time in it.
			return current( explode( ' ', $value ) );
		}

		return null;
	}

	/**
	 * Parse backorders from a CSV.
	 *
	 * @param string $value Field value.
	 *
	 * @return string
	 */
	public function parse_backorders_field( $value ) {
		if ( empty( $value ) ) {
			return 'no';
		}

		$value = $this->parse_bool_field( $value );

		if ( 'notify' === $value ) {
			return 'notify';
		} elseif ( is_bool( $value ) ) {
			return $value ? 'yes' : 'no';
		}

		return 'no';
	}

	/**
	 * Just skip current field.
	 *
	 * By default is applied wc_clean() to all not listed fields
	 * in self::get_formatting_callback(), use this method to skip any formatting.
	 *
	 * @param string $value Field value.
	 *
	 * @return string
	 */
	public function parse_skip_field( $value ) {
		return $value;
	}

	/**
	 * Parse download file urls, we should allow shortcodes here.
	 *
	 * Allow shortcodes if present, otherwise esc_url the value.
	 *
	 * @param string $value Field value.
	 *
	 * @return string
	 */
	public function parse_download_file_field( $value ) {
		// Absolute file paths.
		if ( 0 === strpos( $value, 'http' ) ) {
			return esc_url_raw( $value );
		}
		// Relative and shortcode paths.
		return wc_clean( $value );
	}

	/**
	 * Parse an int value field
	 *
	 * @param int $value field value.
	 *
	 * @return int
	 */
	public function parse_int_field( $value ) {
		// Remove the ' prepended to fields that start with - if needed.
		$value = $this->unescape_data( $value );

		return intval( $value );
	}

	/**
	 * Parse a description value field
	 *
	 * @param string $description field value.
	 *
	 * @return string
	 */
	public function parse_description_field( $description ) {
		$parts = explode( "\\\\n", $description );
		foreach ( $parts as $key => $part ) {
			$parts[ $key ] = str_replace( '\n', "\n", $part );
		}

		return implode( '\\\n', $parts );
	}

	/**
	 * Parse the published field. 1 is published, 0 is private, -1 is draft.
	 * Alternatively, 'true' can be used for published and 'false' for draft.
	 *
	 * @param string $value Field value.
	 *
	 * @return float|string
	 */
	public function parse_published_field( $value ) {
		if ( '' === $value ) {
			return $value;
		}

		// Remove the ' prepended to fields that start with - if needed.
		$value = $this->unescape_data( $value );

		if ( 'true' === strtolower( $value ) || 'false' === strtolower( $value ) ) {
			return wc_string_to_bool( $value ) ? 1 : -1;
		}

		return floatval( $value );
	}

	/**
	 * Deprecated get formatting callback method.
	 *
	 * @deprecated 4.3.0
	 * @return array
	 */
	protected function get_formating_callback() {
		return $this->get_formatting_callback();
	}

	/**
	 * Get formatting callback.
	 *
	 * @since 4.3.0
	 * @return array
	 */
	protected function get_formatting_callback() {

		/**
		 * Columns not mentioned here will get parsed with 'wc_clean'.
		 * column_name => callback.
		 */
		$data_formatting = array(
			'id'                => array( $this, 'parse_id_field' ),
			'type'              => array( $this, 'parse_comma_field' ),
			'published'         => array( $this, 'parse_published_field' ),
			'featured'          => array( $this, 'parse_bool_field' ),
			'date_on_sale_from' => array( $this, 'parse_date_field' ),
			'date_on_sale_to'   => array( $this, 'parse_date_field' ),
			'name'              => array( $this, 'parse_skip_field' ),
			'short_description' => array( $this, 'parse_description_field' ),
			'description'       => array( $this, 'parse_description_field' ),
			'manage_stock'      => array( $this, 'parse_bool_field' ),
			'low_stock_amount'  => array( $this, 'parse_stock_quantity_field' ),
			'backorders'        => array( $this, 'parse_backorders_field' ),
			'stock_status'      => array( $this, 'parse_bool_field' ),
			'sold_individually' => array( $this, 'parse_bool_field' ),
			'width'             => array( $this, 'parse_float_field' ),
			'length'            => array( $this, 'parse_float_field' ),
			'height'            => array( $this, 'parse_float_field' ),
			'weight'            => array( $this, 'parse_float_field' ),
			'reviews_allowed'   => array( $this, 'parse_bool_field' ),
			'purchase_note'     => 'wp_filter_post_kses',
			'price'             => 'wc_format_decimal',
			'regular_price'     => 'wc_format_decimal',
			'stock_quantity'    => array( $this, 'parse_stock_quantity_field' ),
			'category_ids'      => array( $this, 'parse_categories_field' ),
			'tag_ids'           => array( $this, 'parse_tags_field' ),
			'tag_ids_spaces'    => array( $this, 'parse_tags_spaces_field' ),
			'shipping_class_id' => array( $this, 'parse_shipping_class_field' ),
			'images'            => array( $this, 'parse_images_field' ),
			'parent_id'         => array( $this, 'parse_relative_field' ),
			'grouped_products'  => array( $this, 'parse_relative_comma_field' ),
			'upsell_ids'        => array( $this, 'parse_relative_comma_field' ),
			'cross_sell_ids'    => array( $this, 'parse_relative_comma_field' ),
			'download_limit'    => array( $this, 'parse_int_field' ),
			'download_expiry'   => array( $this, 'parse_int_field' ),
			'product_url'       => 'esc_url_raw',
			'menu_order'        => 'intval',
			'tax_status'        => array( $this, 'parse_tax_status_field' ),
		);

		/**
		 * Match special column names.
		 */
		$regex_match_data_formatting = array(
			'/attributes:value*/'    => array( $this, 'parse_comma_field' ),
			'/attributes:visible*/'  => array( $this, 'parse_bool_field' ),
			'/attributes:taxonomy*/' => array( $this, 'parse_bool_field' ),
			'/downloads:url*/'       => array( $this, 'parse_download_file_field' ),
			'/meta:*/'               => 'wp_kses_post', // Allow some HTML in meta fields.
		);

		$callbacks = array();

		// Figure out the parse function for each column.
		foreach ( $this->get_mapped_keys() as $index => $heading ) {
			$callback = 'wc_clean';

			if ( isset( $data_formatting[ $heading ] ) ) {
				$callback = $data_formatting[ $heading ];
			} else {
				foreach ( $regex_match_data_formatting as $regex => $callback ) {
					if ( preg_match( $regex, $heading ) ) {
						$callback = $callback;
						break;
					}
				}
			}

			$callbacks[] = $callback;
		}

		return apply_filters( 'woocommerce_product_importer_formatting_callbacks', $callbacks, $this );
	}

	/**
	 * Check if strings starts with determined word.
	 *
	 * @param string $haystack Complete sentence.
	 * @param string $needle   Excerpt.
	 *
	 * @return bool
	 */
	protected function starts_with( $haystack, $needle ) {
		return substr( $haystack, 0, strlen( $needle ) ) === $needle;
	}

	/**
	 * Expand special and internal data into the correct formats for the product CRUD.
	 *
	 * @param array $data Data to import.
	 *
	 * @return array
	 */
	protected function expand_data( $data ) {
		$data = apply_filters( 'woocommerce_product_importer_pre_expand_data', $data );

		// Images field maps to image and gallery id fields.
		if ( isset( $data['images'] ) ) {
			$images               = $data['images'];
			$data['raw_image_id'] = array_shift( $images );

			if ( ! empty( $images ) ) {
				$data['raw_gallery_image_ids'] = $images;
			}
			unset( $data['images'] );
		}

		// Type, virtual and downloadable are all stored in the same column.
		if ( isset( $data['type'] ) ) {
			$data['type']         = array_map( 'strtolower', $data['type'] );
			$data['virtual']      = in_array( 'virtual', $data['type'], true );
			$data['downloadable'] = in_array( 'downloadable', $data['type'], true );

			// Convert type to string.
			$data['type'] = current( array_diff( $data['type'], array( 'virtual', 'downloadable' ) ) );

			if ( ! $data['type'] ) {
				$data['type'] = 'simple';
			}
		}

		// Status is mapped from a special published field.
		if ( isset( $data['published'] ) ) {
			$published = $data['published'];
			if ( is_float( $published ) ) {
				$published = (int) $published;
			}

			$statuses       = array(
				-1 => 'draft',
				0  => 'private',
				1  => 'publish',
			);
			$data['status'] = $statuses[ $published ] ?? 'draft';

			// Fix draft status of variations.
			if ( 'variation' === ( $data['type'] ?? null ) && -1 === $published ) {
				$data['status'] = 'publish';
			}

			unset( $data['published'] );
		}

		if ( isset( $data['stock_quantity'] ) ) {
			if ( '' === $data['stock_quantity'] ) {
				$data['manage_stock'] = false;
				$data['stock_status'] = isset( $data['stock_status'] ) ? $data['stock_status'] : true;
			} else {
				$data['manage_stock'] = true;
			}
		}

		// Stock is bool or 'backorder'.
		if ( isset( $data['stock_status'] ) ) {
			if ( 'backorder' === $data['stock_status'] ) {
				$data['stock_status'] = 'onbackorder';
			} else {
				$data['stock_status'] = $data['stock_status'] ? 'instock' : 'outofstock';
			}
		}

		// Prepare grouped products.
		if ( isset( $data['grouped_products'] ) ) {
			$data['children'] = $data['grouped_products'];
			unset( $data['grouped_products'] );
		}

		// Tag ids.
		if ( isset( $data['tag_ids_spaces'] ) ) {
			$data['tag_ids'] = $data['tag_ids_spaces'];
			unset( $data['tag_ids_spaces'] );
		}

		// Handle special column names which span multiple columns.
		$attributes = array();
		$downloads  = array();
		$meta_data  = array();

		foreach ( $data as $key => $value ) {
			if ( $this->starts_with( $key, 'attributes:name' ) ) {
				if ( ! empty( $value ) ) {
					$attributes[ str_replace( 'attributes:name', '', $key ) ]['name'] = $value;
				}
				unset( $data[ $key ] );

			} elseif ( $this->starts_with( $key, 'attributes:value' ) ) {
				$attributes[ str_replace( 'attributes:value', '', $key ) ]['value'] = $value;
				unset( $data[ $key ] );

			} elseif ( $this->starts_with( $key, 'attributes:taxonomy' ) ) {
				$attributes[ str_replace( 'attributes:taxonomy', '', $key ) ]['taxonomy'] = wc_string_to_bool( $value );
				unset( $data[ $key ] );

			} elseif ( $this->starts_with( $key, 'attributes:visible' ) ) {
				$attributes[ str_replace( 'attributes:visible', '', $key ) ]['visible'] = wc_string_to_bool( $value );
				unset( $data[ $key ] );

			} elseif ( $this->starts_with( $key, 'attributes:default' ) ) {
				if ( ! empty( $value ) ) {
					$attributes[ str_replace( 'attributes:default', '', $key ) ]['default'] = $value;
				}
				unset( $data[ $key ] );

			} elseif ( $this->starts_with( $key, 'downloads:id' ) ) {
				if ( ! empty( $value ) ) {
					$downloads[ str_replace( 'downloads:id', '', $key ) ]['id'] = $value;
				}
				unset( $data[ $key ] );

			} elseif ( $this->starts_with( $key, 'downloads:name' ) ) {
				if ( ! empty( $value ) ) {
					$downloads[ str_replace( 'downloads:name', '', $key ) ]['name'] = $value;
				}
				unset( $data[ $key ] );

			} elseif ( $this->starts_with( $key, 'downloads:url' ) ) {
				if ( ! empty( $value ) ) {
					$downloads[ str_replace( 'downloads:url', '', $key ) ]['url'] = $value;
				}
				unset( $data[ $key ] );

			} elseif ( $this->starts_with( $key, 'meta:' ) ) {
				$meta_data[] = array(
					'key'   => str_replace( 'meta:', '', $key ),
					'value' => $value,
				);
				unset( $data[ $key ] );
			}
		}

		if ( ! empty( $attributes ) ) {
			// Remove empty attributes and clear indexes.
			foreach ( $attributes as $attribute ) {
				if ( empty( $attribute['name'] ) ) {
					continue;
				}

				$data['raw_attributes'][] = $attribute;
			}
		}

		if ( ! empty( $downloads ) ) {
			$data['downloads'] = array();

			foreach ( $downloads as $key => $file ) {
				if ( empty( $file['url'] ) ) {
					continue;
				}

				$data['downloads'][] = array(
					'download_id' => isset( $file['id'] ) ? $file['id'] : null,
					'name'        => $file['name'] ? $file['name'] : wc_get_filename_from_url( $file['url'] ),
					'file'        => $file['url'],
				);
			}
		}

		if ( ! empty( $meta_data ) ) {
			$data['meta_data'] = $meta_data;
		}

		return $data;
	}

	/**
	 * Map and format raw data to known fields.
	 */
	protected function set_parsed_data() {
		$parse_functions = $this->get_formatting_callback();
		$mapped_keys     = $this->get_mapped_keys();
		$use_mb          = function_exists( 'mb_convert_encoding' );

		// Parse the data.
		foreach ( $this->raw_data as $row_index => $row ) {
			// Skip empty rows.
			if ( ! count( array_filter( $row ) ) ) {
				continue;
			}

			$this->parsing_raw_data_index = $row_index;

			$data = array();

			do_action( 'woocommerce_product_importer_before_set_parsed_data', $row, $mapped_keys );

			foreach ( $row as $id => $value ) {
				// Skip ignored columns.
				if ( empty( $mapped_keys[ $id ] ) ) {
					continue;
				}

				// Convert UTF8.
				if ( $use_mb ) {
					$encoding = mb_detect_encoding( $value, mb_detect_order(), true );
					if ( $encoding ) {
						$value = mb_convert_encoding( $value, 'UTF-8', $encoding );
					} else {
						$value = mb_convert_encoding( $value, 'UTF-8', 'UTF-8' );
					}
				} else {
					$value = wp_check_invalid_utf8( $value, true );
				}

				$data[ $mapped_keys[ $id ] ] = call_user_func( $parse_functions[ $id ], $value );
			}

			/**
			 * Filter product importer parsed data.
			 *
			 * @param array $parsed_data Parsed data.
			 * @param WC_Product_Importer $importer Importer instance.
			 *
			 * @since
			 */
			$this->parsed_data[] = apply_filters( 'woocommerce_product_importer_parsed_data', $this->expand_data( $data ), $this );
		}
	}

	/**
	 * Get a string to identify the row from parsed data.
	 *
	 * @param array $parsed_data Parsed data.
	 *
	 * @return string
	 */
	protected function get_row_id( $parsed_data ) {
		$id       = isset( $parsed_data['id'] ) ? absint( $parsed_data['id'] ) : 0;
		$sku      = isset( $parsed_data['sku'] ) ? esc_attr( $parsed_data['sku'] ) : '';
		$name     = isset( $parsed_data['name'] ) ? esc_attr( $parsed_data['name'] ) : '';
		$row_data = array();

		if ( $name ) {
			$row_data[] = $name;
		}
		if ( $id ) {
			/* translators: %d: product ID */
			$row_data[] = sprintf( __( 'ID %d', 'woocommerce' ), $id );
		}
		if ( $sku ) {
			/* translators: %s: product SKU */
			$row_data[] = sprintf( __( 'SKU %s', 'woocommerce' ), $sku );
		}

		return implode( ', ', $row_data );
	}

	/**
	 * Process importer.
	 *
	 * Do not import products with IDs or SKUs that already exist if option
	 * update existing is false, and likewise, if updating products, do not
	 * process rows which do not exist if an ID/SKU is provided.
	 *
	 * @return array
	 */
	public function import() {
		$this->start_time = time();
		$index            = 0;
		$update_existing  = $this->params['update_existing'];
		$data             = array(
			'imported' => array(),
			'failed'   => array(),
			'updated'  => array(),
			'skipped'  => array(),
		);

		foreach ( $this->parsed_data as $parsed_data_key => $parsed_data ) {
			do_action( 'woocommerce_product_import_before_import', $parsed_data );

			$id         = isset( $parsed_data['id'] ) ? absint( $parsed_data['id'] ) : 0;
			$sku        = isset( $parsed_data['sku'] ) ? $parsed_data['sku'] : '';
			$id_exists  = false;
			$sku_exists = false;

			if ( $id ) {
				$product   = wc_get_product( $id );
				$id_exists = $product && 'importing' !== $product->get_status();
			}

			if ( $sku ) {
				$id_from_sku = wc_get_product_id_by_sku( $sku );
				$product     = $id_from_sku ? wc_get_product( $id_from_sku ) : false;
				$sku_exists  = $product && 'importing' !== $product->get_status();
			}

			if ( $id_exists && ! $update_existing ) {
				$data['skipped'][] = new WP_Error(
					'woocommerce_product_importer_error',
					esc_html__( 'A product with this ID already exists.', 'woocommerce' ),
					array(
						'id'  => $id,
						'row' => $this->get_row_id( $parsed_data ),
					)
				);
				continue;
			}

			if ( $sku_exists && ! $update_existing ) {
				$data['skipped'][] = new WP_Error(
					'woocommerce_product_importer_error',
					esc_html__( 'A product with this SKU already exists.', 'woocommerce' ),
					array(
						'sku' => esc_attr( $sku ),
						'row' => $this->get_row_id( $parsed_data ),
					)
				);
				continue;
			}

			if ( $update_existing && ( isset( $parsed_data['id'] ) || isset( $parsed_data['sku'] ) ) && ! $id_exists && ! $sku_exists ) {
				$data['skipped'][] = new WP_Error(
					'woocommerce_product_importer_error',
					esc_html__( 'No matching product exists to update.', 'woocommerce' ),
					array(
						'id'  => $id,
						'sku' => esc_attr( $sku ),
						'row' => $this->get_row_id( $parsed_data ),
					)
				);
				continue;
			}

			$result = $this->process_item( $parsed_data );

			if ( is_wp_error( $result ) ) {
				$result->add_data( array( 'row' => $this->get_row_id( $parsed_data ) ) );
				$data['failed'][] = $result;
			} elseif ( $result['updated'] ) {
				$data['updated'][] = $result['id'];
			} else {
				$data['imported'][] = $result['id'];
			}

			$index ++;

			if ( $this->params['prevent_timeouts'] && ( $this->time_exceeded() || $this->memory_exceeded() ) ) {
				$this->file_position = $this->file_positions[ $index ];
				break;
			}
		}

		return $data;
	}
}
