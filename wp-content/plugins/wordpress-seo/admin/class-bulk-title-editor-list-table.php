<?php
/**
 * @package WPSEO\Admin\Bulk Editor
 * @since      1.5.0
 */

/**
 * Implements table for bulk title editing.
 */
class WPSEO_Bulk_Title_Editor_List_Table extends WPSEO_Bulk_List_Table {

	/**
	 * Current type for this class will be title
	 *
	 * @var string
	 */
	protected $page_type = 'title';


	/**
	 * Settings with are used in __construct
	 *
	 * @var array
	 */
	protected $settings = array(
		'singular' => 'wpseo_bulk_title',
		'plural'   => 'wpseo_bulk_titles',
		'ajax'     => true,
	);

	/**
	 * The field in the database where meta field is saved.
	 * @var string
	 */
	protected $target_db_field = 'title';

	/**
	 * The columns shown on the table
	 *
	 * @return array
	 */
	public function get_columns() {

		$columns = array(
			'col_existing_yoast_seo_title' => __( 'Existing Yoast SEO Title', 'wordpress-seo' ),
			'col_new_yoast_seo_title'      => __( 'New Yoast SEO Title', 'wordpress-seo' ),
		);

		return $this->merge_columns( $columns );
	}

	/**
	 * Parse the title columns
	 *
	 * @param string $column_name
	 * @param object $record
	 * @param string $attributes
	 *
	 * @return string
	 */
	protected function parse_page_specific_column( $column_name, $record, $attributes ) {

		// Fill meta data if exists in $this->meta_data.
		$meta_data = ( ! empty( $this->meta_data[ $record->ID ] ) ) ? $this->meta_data[ $record->ID ] : array();

		switch ( $column_name ) {
			case 'col_existing_yoast_seo_title':
				// TODO inconsistent echo/return behavior R.
				echo $this->parse_meta_data_field( $record->ID, $attributes );
				break;

			case 'col_new_yoast_seo_title':
				return sprintf(
					'<input type="text" id="%1$s" name="%1$s" class="wpseo-new-title" data-id="%2$s" />',
					'wpseo-new-title-' . $record->ID,
					$record->ID
				);
				break;
		}

		unset( $meta_data );
	}


} /* End of class */
