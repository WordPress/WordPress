<?php

namespace Yoast\WP\SEO\Helpers;

/**
 * The Import Cursor Helper.
 */
class Import_Cursor_Helper {

	/**
	 * The Options_Helper.
	 *
	 * @var Options_Helper
	 */
	public $options;

	/**
	 * Class constructor.
	 *
	 * @param Options_Helper $options The options helper.
	 */
	public function __construct( Options_Helper $options ) {
		$this->options = $options;
	}

	/**
	 * Returns the stored cursor value.
	 *
	 * @param string $cursor_id     The cursor id.
	 * @param mixed  $default_value The default value if no cursor has been set yet.
	 *
	 * @return int The stored cursor value.
	 */
	public function get_cursor( $cursor_id, $default_value = 0 ) {
		$import_cursors = $this->options->get( 'import_cursors', [] );

		return ( isset( $import_cursors[ $cursor_id ] ) ) ? $import_cursors[ $cursor_id ] : $default_value;
	}

	/**
	 * Stores the current cursor value.
	 *
	 * @param string $cursor_id        The cursor id.
	 * @param int    $last_imported_id The id of the lastly imported entry.
	 *
	 * @return void
	 */
	public function set_cursor( $cursor_id, $last_imported_id ) {
		$current_cursors = $this->options->get( 'import_cursors', [] );

		if ( ! isset( $current_cursors[ $cursor_id ] ) || $current_cursors[ $cursor_id ] < $last_imported_id ) {
			$current_cursors[ $cursor_id ] = $last_imported_id;
			$this->options->set( 'import_cursors', $current_cursors );
		}
	}
}
