<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php'; // since WP 3.1
}

/**
 * A class to create the strings translations table
 * Thanks to Matt Van Andel ( http://www.mattvanandel.com ) for its plugin "Custom List Table Example" !
 *
 * @since 0.6
 */
class PLL_Table_String extends WP_List_Table {
	protected $languages, $strings, $groups, $selected_group;

	/**
	 * Constructor
	 *
	 * @since 0.6
	 *
	 * @param array $languages list of languages
	 */
	function __construct( $languages ) {
		parent::__construct( array(
			'plural' => 'Strings translations', // Do not translate ( used for css class )
			'ajax'   => false,
		) );

		$this->languages = $languages;
		$this->strings = PLL_Admin_Strings::get_strings();
		$this->groups = array_unique( wp_list_pluck( $this->strings, 'context' ) );
		$this->selected_group = empty( $_GET['group'] ) || ! in_array( $_GET['group'], $this->groups ) ? -1 : $_GET['group'];

		add_action( 'mlang_action_string-translation', array( $this, 'save_translations' ) );
	}

	/**
	 * Displays the item information in a column ( default case )
	 *
	 * @since 0.6
	 *
	 * @param array  $item
	 * @param string $column_name
	 * @return string
	 */
	function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	/**
	 * Displays the checkbox in first column
	 *
	 * @since 1.1
	 *
	 * @param array $item
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<label class="screen-reader-text" for="cb-select-%1$s">%2$s</label><input id="cb-select-%1$s" type="checkbox" name="strings[]" value="%1$s" %3$s />',
			esc_attr( $item['row'] ),
			/* translators:  accessibility text, %s is a string potentially in any language */
			sprintf( __( 'Select %s' ), format_to_edit( $item['string'] ) ),
			empty( $item['icl'] ) ? 'disabled' : '' // Only strings registered with WPML API can be removed
		);
	}

	/**
	 * Displays the string to translate
	 *
	 * @since 1.0
	 *
	 * @param array $item
	 * @return string
	 */
	function column_string( $item ) {
		return format_to_edit( $item['string'] ); // Don't interpret special chars for the string column
	}

	/**
	 * Displays the translations to edit
	 *
	 * @since 0.6
	 *
	 * @param array $item
	 * @return string
	 */
	function column_translations( $item ) {
		$languages = array_combine( wp_list_pluck( $this->languages, 'slug' ), wp_list_pluck( $this->languages, 'name' ) );
		$out = '';

		foreach ( $item['translations'] as $key => $translation ) {
			$input_type = $item['multiline'] ?
				'<textarea name="translation[%1$s][%2$s]" id="%1$s-%2$s">%4$s</textarea>' :
				'<input type="text" name="translation[%1$s][%2$s]" id="%1$s-%2$s" value="%4$s" />';
			$out .= sprintf( '<div class="translation"><label for="%1$s-%2$s">%3$s</label>' . $input_type . '</div>' . "\n",
				esc_attr( $key ),
				esc_attr( $item['row'] ),
				esc_html( $languages[ $key ] ),
			format_to_edit( $translation ) ); // Don't interpret special chars
		}

		return $out;
	}

	/**
	 * Gets the list of columns
	 *
	 * @since 0.6
	 *
	 * @return array the list of column titles
	 */
	function get_columns() {
		return array(
			'cb'           => '<input type="checkbox" />', // Checkbox
			'string'       => esc_html__( 'String', 'polylang' ),
			'name'         => esc_html__( 'Name', 'polylang' ),
			'context'      => esc_html__( 'Group', 'polylang' ),
			'translations' => esc_html__( 'Translations', 'polylang' ),
		);
	}

	/**
	 * Gets the list of sortable columns
	 *
	 * @since 0.6
	 *
	 * @return array
	 */
	function get_sortable_columns() {
		return array(
			'string'  => array( 'string', false ),
			'name'    => array( 'name', false ),
			'context' => array( 'context', false ),
		);
	}

	/**
	 * Gets the name of the default primary column.
	 *
	 * @since 2.1
	 *
	 * @return string Name of the default primary column, in this case, 'string'.
	 */
	protected function get_default_primary_column_name() {
		return 'string';
	}

	/**
	 * Sort items
	 *
	 * @since 0.6
	 *
	 * @param object $a The first object to compare
	 * @param object $b The second object to compare
	 * @return int -1 or 1 if $a is considered to be respectively less than or greater than $b.
	 */
	protected function usort_reorder( $a, $b ) {
			$result = strcmp( $a[ $_GET['orderby'] ], $b[ $_GET['orderby'] ] ); // determine sort order
			return ( empty( $_GET['order'] ) || 'asc' === $_GET['order'] ) ? $result : -$result; // send final sort direction to usort
	}

	/**
	 * Prepares the list of items for displaying
	 *
	 * @since 0.6
	 */
	function prepare_items() {
		$data = $this->strings;

		// Filter for search string
		$s = empty( $_GET['s'] ) ? '' : wp_unslash( $_GET['s'] );
		foreach ( $data as $key => $row ) {
			if ( ( -1 !== $this->selected_group && $row['context'] !== $this->selected_group ) || ( ! empty( $s ) && stripos( $row['name'], $s ) === false && stripos( $row['string'], $s ) === false ) ) {
				unset( $data[ $key ] );
			}
		}

		// Load translations
		foreach ( $this->languages as $language ) {
			// Filters by language if requested
			if ( ( $lg = get_user_meta( get_current_user_id(), 'pll_filter_content', true ) ) && $language->slug !== $lg ) {
				continue;
			}

			$mo = new PLL_MO();
			$mo->import_from_db( $language );
			foreach ( $data as $key => $row ) {
				$data[ $key ]['translations'][ $language->slug ] = $mo->translate( $row['string'] );
				$data[ $key ]['row'] = $key; // Store the row number for convenience
			}
		}

		$per_page = $this->get_items_per_page( 'pll_strings_per_page' );
		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );

		if ( ! empty( $_GET['orderby'] ) ) { // No sort by default
			usort( $data, array( $this, 'usort_reorder' ) );
		}

		$total_items = count( $data );
		$this->items = array_slice( $data, ( $this->get_pagenum() - 1 ) * $per_page, $per_page );

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page ),
		) );
	}

	/**
	 * Get the list of possible bulk actions
	 *
	 * @since 1.1
	 *
	 * @return array
	 */
	function get_bulk_actions() {
		return array( 'delete' => __( 'Delete', 'polylang' ) );
	}

	/**
	 * Get the current action selected from the bulk actions dropdown.
	 * overrides parent function to avoid submit button to trigger bulk actions
	 *
	 * @since 1.8
	 *
	 * @return string|false The action name or False if no action was selected
	 */
	public function current_action() {
		return empty( $_POST['submit'] ) ? parent::current_action() : false;
	}

	/**
	 * Displays the dropdown list to filter strings per group
	 *
	 * @since 1.1
	 *
	 * @param string $which only 'top' is supported
	 */
	function extra_tablenav( $which ) {
		if ( 'top' !== $which ) {
			return;
		}

		echo '<div class="alignleft actions">';
		printf(
			'<label class="screen-reader-text" for="select-group" >%s</label>',
			/* translators: accessibility text */
			esc_html__( 'Filter by group', 'polylang' )
		);
		echo '<select id="select-group" name="group">' . "\n";
		printf(
			'<option value="-1"%s>%s</option>' . "\n",
			-1 === $this->group_selected ? ' selected="selected"' : '',
			esc_html__( 'View all groups', 'polylang' )
		);

		foreach ( $this->groups as $group ) {
			printf(
				'<option value="%s"%s>%s</option>' . "\n",
				esc_attr( urlencode( $group ) ),
				$this->selected_group === $group ? ' selected="selected"' : '',
				esc_html( $group )
			);
		}
		echo '</select>' . "\n";

		submit_button( __( 'Filter' ), 'button', 'filter_action', false, array( 'id' => 'post-query-submit' ) );
		echo '</div>';
	}

	/**
	 * Saves the strings translations in DB
	 * Optionaly clean the DB
	 *
	 * @since 1.9
	 */
	public function save_translations() {
		check_admin_referer( 'string-translation', '_wpnonce_string-translation' );

		if ( ! empty( $_POST['submit'] ) ) {
			foreach ( $this->languages as $language ) {
				if ( empty( $_POST['translation'][ $language->slug ] ) ) { // In case the language filter is active ( thanks to John P. Bloch )
					continue;
				}

				$mo = new PLL_MO();
				$mo->import_from_db( $language );

				foreach ( $_POST['translation'][ $language->slug ] as $key => $translation ) {
					/**
					 * Filter the string translation before it is saved in DB
					 * Allows to sanitize strings registered with pll_register_string
					 *
					 * @since 1.6
					 *
					 * @param string $translation the string translation
					 * @param string $name        the name as defined in pll_register_string
					 * @param string $context     the context as defined in pll_register_string
					 */
					$translation = apply_filters( 'pll_sanitize_string_translation', $translation, $this->strings[ $key ]['name'], $this->strings[ $key ]['context'] );
					$mo->add_entry( $mo->make_entry( $this->strings[ $key ]['string'], $translation ) );
				}

				// Clean database ( removes all strings which were registered some day but are no more )
				if ( ! empty( $_POST['clean'] ) ) {
					$new_mo = new PLL_MO();

					foreach ( $this->strings as $string ) {
						$new_mo->add_entry( $mo->make_entry( $string['string'], $mo->translate( $string['string'] ) ) );
					}
				}

				isset( $new_mo ) ? $new_mo->export_to_db( $language ) : $mo->export_to_db( $language );
			}

			add_settings_error( 'general', 'pll_strings_translations_updated', __( 'Translations updated.', 'polylang' ), 'updated' );

			/**
			 * Fires after the strings translations are saved in DB
			 *
			 * @since 1.2
			 */
			do_action( 'pll_save_strings_translations' );
		}

		// Unregisters strings registered through WPML API
		if ( $this->current_action() === 'delete' && ! empty( $_POST['strings'] ) && function_exists( 'icl_unregister_string' ) ) {
			foreach ( $_POST['strings'] as $key ) {
				icl_unregister_string( $this->strings[ $key ]['context'], $this->strings[ $key ]['name'] );
			}
		}

		// To refresh the page ( possible thanks to the $_GET['noheader']=true )
		$args = array_intersect_key( $_REQUEST, array_flip( array( 's', 'paged', 'group' ) ) );
		if ( ! empty( $_GET['paged'] ) && ! empty( $_POST['submit'] ) ) {
			$args['paged'] = (int) $_GET['paged']; // Don't rely on $_REQUEST['paged'] or $_POST['paged']. See #14
		}
		if ( ! empty( $args['s'] ) ) {
			$args['s'] = urlencode( $args['s'] ); // Searched string needs to be encoded as it comes from $_POST
		}
		PLL_Settings::redirect( $args );
	}
}
