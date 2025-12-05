<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Bulk Editor
 * @since   1.5.0
 */

/**
 * Implements table for bulk title editing.
 */
class WPSEO_Bulk_Title_Editor_List_Table extends WPSEO_Bulk_List_Table {

	/**
	 * Current type for this class will be title.
	 *
	 * @var string
	 */
	protected $page_type = 'title';

	/**
	 * Settings with are used in __construct.
	 *
	 * @var array
	 */
	protected $settings = [
		'singular' => 'wpseo_bulk_title',
		'plural'   => 'wpseo_bulk_titles',
		'ajax'     => true,
	];

	/**
	 * The field in the database where meta field is saved.
	 *
	 * @var string
	 */
	protected $target_db_field = 'title';

	/**
	 * The columns shown on the table.
	 *
	 * @return array
	 */
	public function get_columns() {

		$columns = [
			/* translators: %1$s expands to Yoast SEO */
			'col_existing_yoast_seo_title' => sprintf( __( 'Existing %1$s Title', 'wordpress-seo' ), 'Yoast SEO' ),
			/* translators: %1$s expands to Yoast SEO */
			'col_new_yoast_seo_title'      => sprintf( __( 'New %1$s Title', 'wordpress-seo' ), 'Yoast SEO' ),
		];

		return $this->merge_columns( $columns );
	}

	/**
	 * Parse the title columns.
	 *
	 * @param string $column_name Column name.
	 * @param object $record      Data object.
	 * @param string $attributes  HTML attributes.
	 *
	 * @return string
	 */
	protected function parse_page_specific_column( $column_name, $record, $attributes ) {

		// Fill meta data if exists in $this->meta_data.
		$meta_data = ( ! empty( $this->meta_data[ $record->ID ] ) ) ? $this->meta_data[ $record->ID ] : [];

		switch ( $column_name ) {
			case 'col_existing_yoast_seo_title':
				// @todo Inconsistent return/echo behavior R.
				// I traced the escaping of the attributes to WPSEO_Bulk_List_Table::column_attributes.
				// The output of WPSEO_Bulk_List_Table::parse_meta_data_field is properly escaped.
				// phpcs:ignore WordPress.Security.EscapeOutput
				echo $this->parse_meta_data_field( $record->ID, $attributes );
				break;

			case 'col_new_yoast_seo_title':
				return sprintf(
					'<input type="text" id="%1$s" name="%1$s" class="wpseo-new-title" data-id="%2$s" aria-labelledby="col_new_yoast_seo_title" />',
					'wpseo-new-title-' . $record->ID,
					$record->ID
				);
		}

		unset( $meta_data );
	}
}
