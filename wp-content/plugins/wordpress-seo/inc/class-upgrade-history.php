<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Internal
 */

/**
 * This class handles storing the current options for future reference.
 *
 * This should only be used during an upgrade routine.
 */
class WPSEO_Upgrade_History {

	/**
	 * Option to use to store/retrieve data from.
	 *
	 * @var string
	 */
	protected $option_name = 'wpseo_upgrade_history';

	/**
	 * WPSEO_Upgrade_History constructor.
	 *
	 * @param string|null $option_name Optional. Custom option to use to store/retrieve history from.
	 */
	public function __construct( $option_name = null ) {
		if ( $option_name !== null ) {
			$this->option_name = $option_name;
		}
	}

	/**
	 * Retrieves the content of the history items currently stored.
	 *
	 * @return array<array<string>> The contents of the history option.
	 */
	public function get() {
		$data = get_option( $this->get_option_name(), [] );
		if ( ! is_array( $data ) ) {
			return [];
		}

		return $data;
	}

	/**
	 * Adds a new history entry in the storage.
	 *
	 * @param string        $old_version  The version we are upgrading from.
	 * @param string        $new_version  The version we are upgrading to.
	 * @param array<string> $option_names The options that need to be stored.
	 *
	 * @return void
	 */
	public function add( $old_version, $new_version, array $option_names ) {
		$option_data = [];
		if ( $option_names !== [] ) {
			$option_data = $this->get_options_data( $option_names );
		}

		// Retrieve current history.
		$data = $this->get();

		// Add new entry.
		$data[ time() ] = [
			'options'     => $option_data,
			'old_version' => $old_version,
			'new_version' => $new_version,
		];

		// Store the data.
		$this->set( $data );
	}

	/**
	 * Retrieves the data for the specified option names from the database.
	 *
	 * @param array<string> $option_names The option names to retrieve.
	 *
	 * @return array<int|string, array<string|int|bool|float>> The retrieved data.
	 */
	protected function get_options_data( array $option_names ) {
		$wpdb = $this->get_wpdb();

		$results = $wpdb->get_results(
			$wpdb->prepare(
				'
				SELECT %i, %i FROM ' . $wpdb->options . ' WHERE
				%i IN ( ' . implode( ',', array_fill( 0, count( $option_names ), '%s' ) ) . ' )
				',
				array_merge( [ 'option_value', 'option_name', 'option_name' ], $option_names )
			),
			ARRAY_A
		);

		$data = [];
		foreach ( $results as $result ) {
			$data[ $result['option_name'] ] = maybe_unserialize( $result['option_value'] );
		}

		return $data;
	}

	/**
	 * Stores the new history state.
	 *
	 * @param array<array<string>> $data The data to store.
	 *
	 * @return void
	 */
	protected function set( array $data ) {
		// This should not be autoloaded!
		update_option( $this->get_option_name(), $data, false );
	}

	/**
	 * Retrieves the WPDB object.
	 *
	 * @return wpdb The WPDB object to use.
	 */
	protected function get_wpdb() {
		global $wpdb;

		return $wpdb;
	}

	/**
	 * Retrieves the option name to store the history in.
	 *
	 * @return string The option name to store the history in.
	 */
	protected function get_option_name() {
		return $this->option_name;
	}
}
