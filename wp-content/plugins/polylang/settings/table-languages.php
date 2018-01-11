<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php'; // since WP 3.1
}

/**
 * A class to create the languages table in Polylang settings
 * Thanks to Matt Van Andel ( http://www.mattvanandel.com ) for its plugin "Custom List Table Example" !
 *
 * @since 0.1
 */
class PLL_Table_Languages extends WP_List_Table {

	/**
	 * Constructor
	 *
	 * @since 0.1
	 */
	function __construct() {
		parent::__construct( array(
			'plural' => 'Languages', // Do not translate ( used for css class )
			'ajax'   => false,
		) );
	}

	/**
	 * Generates content for a single row of the table
	 *
	 * @since 1.8
	 *
	 * @param object $item The current item
	 */
	public function single_row( $item ) {
		/**
		 * Filter the list of classes assigned a row in the languages list table
		 *
		 * @since 1.8
		 *
		 * @param array  $classes list of class names
		 * @param object $item    the current item
		 */
		$classes = apply_filters( 'pll_languages_row_classes', array(), $item );
		echo '<tr' . ( empty( $classes ) ? '>' : ' class="' . esc_attr( implode( ' ', $classes ) ) . '">' );
		$this->single_row_columns( $item );
		echo '</tr>';
	}

	/**
	 * Displays the item information in a column ( default case )
	 *
	 * @since 0.1
	 *
	 * @param object $item
	 * @param string $column_name
	 * @return string
	 */
	function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'locale':
			case 'slug':
				return esc_html( $item->$column_name );

			case 'term_group':
			case 'count':
				return (int) $item->$column_name;

			default:
				return $item->$column_name; // flag
		}
	}

	/**
	 * Displays the item information in the column 'name'
	 * Displays the edit and delete action links
	 *
	 * @since 0.1
	 *
	 * @param object $item
	 * @return string
	 */
	function column_name( $item ) {
		return sprintf(
			'<a title="%s" href="%s">%s</a>',
			esc_attr__( 'Edit this language', 'polylang' ),
			esc_url( admin_url( 'admin.php?page=mlang&amp;pll_action=edit&amp;lang=' . $item->term_id ) ),
			esc_html( $item->name )
		);
	}

	/**
	 * Displays the item information in the default language
	 * Displays the 'make default' action link
	 *
	 * @since 1.8
	 *
	 * @param object $item
	 * @return string
	 */
	function column_default_lang( $item ) {
		$options = get_option( 'polylang' );

		if ( $options['default_lang'] != $item->slug ) {
			$s = sprintf('
				<div class="row-actions"><span class="default-lang">
				<a class="icon-default-lang" title="%1$s" href="%2$s"><span class="screen-reader-text">%3$s</span></a>
				</span></div>',
				esc_attr__( 'Select as default language', 'polylang' ),
				wp_nonce_url( '?page=mlang&amp;pll_action=default-lang&amp;noheader=true&amp;lang=' . $item->term_id, 'default-lang' ),
				/* translators: accessibility text, %s is a native language name */
				esc_html( sprintf( __( 'Choose %s as default language', 'polylang' ), $item->name ) )
			);

			/**
			 * Filter the default language row action in the languages list table
			 *
			 * @since 1.8
			 *
			 * @param string $s    html markup of the action
			 * @param object $item
			 */
			$s = apply_filters( 'pll_default_lang_row_action', $s, $item );
		} else {
			$s = sprintf(
				'<span class="icon-default-lang"><span class="screen-reader-text">%1$s</span></span>',
				/* translators: accessibility text */
				esc_html__( 'Default language', 'polylang' )
			);
			$actions = array();
		}

		return $s;
	}

	/**
	 * Gets the list of columns
	 *
	 * @since 0.1
	 *
	 * @return array the list of column titles
	 */
	function get_columns() {
		return array(
			'name'         => esc_html__( 'Full name', 'polylang' ),
			'locale'       => esc_html__( 'Locale', 'polylang' ),
			'slug'         => esc_html__( 'Code', 'polylang' ),
			'default_lang' => sprintf( '<span title="%1$s" class="icon-default-lang"><span class="screen-reader-text">%2$s</span></span>', esc_attr__( 'Default language', 'polylang' ), esc_html__( 'Default language', 'polylang' ) ),
			'term_group'   => esc_html__( 'Order', 'polylang' ),
			'flag'         => esc_html__( 'Flag', 'polylang' ),
			'count'        => esc_html__( 'Posts', 'polylang' ),
		);
	}

	/**
	 * Gets the list of sortable columns
	 *
	 * @since 0.1
	 *
	 * @return array
	 */
	function get_sortable_columns() {
		return array(
			'name'       => array( 'name', true ), // sorted by name by default
			'locale'     => array( 'locale', false ),
			'slug'       => array( 'slug', false ),
			'term_group' => array( 'term_group', false ),
			'count'      => array( 'count', false ),
		);
	}

	/**
	 * Gets the name of the default primary column.
	 *
	 * @since 2.1
	 *
	 * @return string Name of the default primary column, in this case, 'name'.
	 */
	protected function get_default_primary_column_name() {
		return 'name';
	}

	/**
	 * Generates and display row actions links for the list table.
	 *
	 * @since 1.8
	 *
	 * @param object $item        The item being acted upon.
	 * @param string $column_name Current column name.
	 * @param string $primary     Primary column name.
	 * @return string The row actions output.
	 */
	protected function handle_row_actions( $item, $column_name, $primary ) {
		if ( $primary !== $column_name ) {
			return '';
		}

		$actions = array(
			'edit'   => sprintf(
				'<a title="%s" href="%s">%s</a>',
				esc_attr__( 'Edit this language', 'polylang' ),
				esc_url( admin_url( 'admin.php?page=mlang&amp;pll_action=edit&amp;lang=' . $item->term_id ) ),
				esc_html__( 'Edit', 'polylang' )
			),
			'delete' => sprintf(
				'<a title="%s" href="%s" onclick = "return confirm( \'%s\' );">%s</a>',
				esc_attr__( 'Delete this language and all its associated data', 'polylang' ),
				wp_nonce_url( '?page=mlang&amp;pll_action=delete&amp;noheader=true&amp;lang=' . $item->term_id, 'delete-lang' ),
				esc_js( __( 'You are about to permanently delete this language. Are you sure?', 'polylang' ) ),
				esc_html__( 'Delete', 'polylang' )
			),
		);

		/**
		 * Filter the list of row actions in the languages list table
		 *
		 * @since 1.8
		 *
		 * @param array  $actions list of html markup actions
		 * @param object $item
		 */
		$actions = apply_filters( 'pll_languages_row_actions', $actions, $item );

		return $this->row_actions( $actions );
	}

	/**
	 * Sort items
	 *
	 * @since 0.1
	 *
	 * @param object $a The first object to compare
	 * @param object $b The second object to compare
	 * @return int -1 or 1 if $a is considered to be respectively less than or greater than $b.
	 */
	protected function usort_reorder( $a, $b ) {
		$orderby = ! empty( $_GET['orderby'] ) ? $_GET['orderby'] : 'name';
		// Determine sort order
		if ( is_numeric( $a->$orderby ) ) {
			$result = $a->$orderby > $b->$orderby ? 1 : -1;
		} else {
			$result = strcmp( $a->$orderby, $b->$orderby );
		}
		// Send final sort direction to usort
		return ( empty( $_GET['order'] ) || 'asc' == $_GET['order'] ) ? $result : -$result;
	}

	/**
	 * Prepares the list of items for displaying
	 *
	 * @since 0.1
	 *
	 * @param array $data
	 */
	function prepare_items( $data = array() ) {
		$per_page = $this->get_items_per_page( 'pll_lang_per_page' );
		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );

		usort( $data, array( $this, 'usort_reorder' ) );

		$total_items = count( $data );
		$this->items = array_slice( $data, ( $this->get_pagenum() - 1 ) * $per_page, $per_page );

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page ),
		) );
	}
}
