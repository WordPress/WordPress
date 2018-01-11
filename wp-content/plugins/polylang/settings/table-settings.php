<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php'; // since WP 3.1
}

/**
 * A class to create a table to list all settings modules
 *
 * @since 1.8
 */
class PLL_Table_Settings extends WP_List_Table {

	/**
	 * Constructor
	 *
	 * @since 1.8
	 */
	function __construct() {
		parent::__construct( array(
			'plural' => 'Settings', // Do not translate ( used for css class )
			'ajax'   => false,
		) );
	}

	/**
	 * Get the table classes for styling
	 *
	 * @since 1.8
	 */
	protected function get_table_classes() {
		return array( 'wp-list-table', 'widefat', 'plugins', 'pll-settings' ); // get the style of the plugins list table + one specific class
	}

	/**
	 * Displays a single row
	 *
	 * @since 1.8
	 *
	 * @param object $item PLL_Settings_Module object
	 */
	public function single_row( $item ) {
		// Classes to reuse css from the plugins list table
		$classes = $item->is_active() ? 'active' : 'inactive';
		if ( $message = $item->get_upgrade_message() ) {
			$classes .= ' update';
		}

		// Display the columns
		printf( '<tr id="pll-module-%s" class="%s">', esc_attr( $item->module ), esc_attr( $classes ) );
		$this->single_row_columns( $item );
		echo '</tr>';

		// Display an upgrade message if there is any, reusing css from the plugins updates
		if ( $message = $item->get_upgrade_message() ) {
			printf( '
				<tr class="plugin-update-tr">
					<td colspan="3" class="plugin-update colspanchange">%s</td>
				</tr>',
				sprintf(
					version_compare( $GLOBALS['wp_version'], '4.6', '<' ) ?
						'<div class="update-message">%s</div>' : // backward compatibility with WP < 4.6
						'<div class="update-message notice inline notice-warning notice-alt"><p>%s</p></div>',
					$message
				)
			);
		}

		// The settings if there are
		// "inactive" class to reuse css from the plugins list table
		if ( $form = $item->get_form() ) {
			printf( '
				<tr id="pll-configure-%s" class="pll-configure inactive inline-edit-row" style="display: none;">
					<td colspan="3">
						<legend>%s</legend>
						%s
						<p class="submit inline-edit-save">
							%s
						</p>
					</td>
				</tr>',
				esc_attr( $item->module ), esc_html( $item->title ), $form, implode( $item->get_buttons() )
			);
		}
	}

	/**
	 * Generates the columns for a single row of the table
	 *
	 * @since 1.8
	 *
	 * @param object $item The current item
	 */
	protected function single_row_columns( $item ) {
		list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

		foreach ( $columns as $column_name => $column_display_name ) {
			$classes = "$column_name column-$column_name";
			if ( $primary === $column_name ) {
				$classes .= ' column-primary';
			}

			if ( in_array( $column_name, $hidden ) ) {
				$classes .= ' hidden';
			}

			if ( 'cb' == $column_name ) {
				echo '<th scope="row" class="check-column">';
				echo $this->column_cb( $item );
				echo '</th>';
			}
			else {
				printf( '<td class="%s">', esc_attr( $classes ) );
				echo $this->column_default( $item, $column_name );
				echo '</td>';
			}
		}
	}

	/**
	 * Displays the item information in a column ( default case )
	 *
	 * @since 1.8
	 *
	 * @param object $item
	 * @param string $column_name
	 * @return string
	 */
	protected function column_default( $item, $column_name ) {
		if ( 'plugin-title' == $column_name ) {
			return sprintf( '<strong>%s</strong>', esc_html( $item->title ) ) . $this->row_actions( $item->get_action_links(), true /*always visible*/ );
		}
		return $item->$column_name;
	}

	/**
	 * Gets the list of columns
	 *
	 * @since 1.8
	 *
	 * @return array the list of column titles
	 */
	public function get_columns() {
		return array(
			'cb'           => '', // For the 4px border inherited from plugins when the module is activated
			'plugin-title' => esc_html__( 'Module', 'polylang' ), // plugin-title for styling
			'description'  => esc_html__( 'Description', 'polylang' ),
		);
	}

	/**
	 * Gets the name of the primary column.
	 *
	 * @since 1.8
	 *
	 * @return string The name of the primary column.
	 */
	protected function get_primary_column_name() {
		return 'plugin-title';
	}

	/**
	 * Prepares the list of items for displaying
	 *
	 * @since 1.8
	 *
	 * @param array $items
	 */
	public function prepare_items( $items = array() ) {
		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns(), $this->get_primary_column_name() );
		$this->items = $items;
	}

	/**
	 * Avoids displaying an empty tablenav
	 *
	 * @since 2.1
	 *
	 * @param string $which 'top' or 'bottom'
	 */
	protected function display_tablenav( $which ) {}
}
