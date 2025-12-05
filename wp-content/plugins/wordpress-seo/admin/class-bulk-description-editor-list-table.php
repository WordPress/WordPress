<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Bulk Editor
 * @since   1.5.0
 */

/**
 * Implements table for bulk description editing.
 */
class WPSEO_Bulk_Description_List_Table extends WPSEO_Bulk_List_Table {

	/**
	 * Current type for this class will be (meta) description.
	 *
	 * @var string
	 */
	protected $page_type = 'description';

	/**
	 * Settings with are used in __construct.
	 *
	 * @var array
	 */
	protected $settings = [
		'singular' => 'wpseo_bulk_description',
		'plural'   => 'wpseo_bulk_descriptions',
		'ajax'     => true,
	];

	/**
	 * The field in the database where meta field is saved.
	 *
	 * @var string
	 */
	protected $target_db_field = 'metadesc';

	/**
	 * The columns shown on the table.
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = [
			'col_existing_yoast_seo_metadesc' => __( 'Existing Yoast Meta Description', 'wordpress-seo' ),
			'col_new_yoast_seo_metadesc'      => __( 'New Yoast Meta Description', 'wordpress-seo' ),
		];

		return $this->merge_columns( $columns );
	}

	/**
	 * Parse the metadescription.
	 *
	 * @param string $column_name Column name.
	 * @param object $record      Data object.
	 * @param string $attributes  HTML attributes.
	 *
	 * @return string
	 */
	protected function parse_page_specific_column( $column_name, $record, $attributes ) {
		switch ( $column_name ) {
			case 'col_new_yoast_seo_metadesc':
				return sprintf(
					'<textarea id="%1$s" name="%1$s" class="wpseo-new-metadesc" data-id="%2$s" aria-labelledby="col_new_yoast_seo_metadesc"></textarea>',
					esc_attr( 'wpseo-new-metadesc-' . $record->ID ),
					esc_attr( $record->ID )
				);

			case 'col_existing_yoast_seo_metadesc':
				// @todo Inconsistent return/echo behavior R.
				// I traced the escaping of the attributes to WPSEO_Bulk_List_Table::column_attributes. Alexander.
				// The output of WPSEO_Bulk_List_Table::parse_meta_data_field is properly escaped.
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $this->parse_meta_data_field( $record->ID, $attributes );
				break;
		}
	}
}
