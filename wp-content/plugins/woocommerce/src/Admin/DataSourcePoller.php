<?php

namespace Automattic\WooCommerce\Admin;

/**
 * Specs data source poller class.
 * This handles polling specs from JSON endpoints, and
 * stores the specs in to the database as an option.
 */
abstract class DataSourcePoller {

	/**
	 * Get class instance.
	 */
	abstract public static function get_instance();

	/**
	 * Name of data sources filter.
	 */
	const FILTER_NAME = 'data_source_poller_data_sources';

	/**
	 * Name of data source specs filter.
	 */
	const FILTER_NAME_SPECS = 'data_source_poller_specs';

	/**
	 * Id of DataSourcePoller.
	 *
	 * @var string
	 */
	protected $id = array();

	/**
	 * Default data sources array.
	 *
	 * @var array
	 */
	protected $data_sources = array();

	/**
	 * Default args.
	 *
	 * @var array
	 */
	protected $args = array();

	/**
	 * The logger instance.
	 *
	 * @var WC_Logger|null
	 */
	protected static $logger = null;

	/**
	 * Constructor.
	 *
	 * @param string $id id of DataSourcePoller.
	 * @param array  $data_sources urls for data sources.
	 * @param array  $args Options for DataSourcePoller.
	 */
	public function __construct( $id, $data_sources = array(), $args = array() ) {
		$this->data_sources = $data_sources;
		$this->id           = $id;

		$arg_defaults = array(
			'spec_key'         => 'id',
			'transient_name'   => 'woocommerce_admin_' . $id . '_specs',
			'transient_expiry' => 7 * DAY_IN_SECONDS,
		);
		$this->args   = wp_parse_args( $args, $arg_defaults );
	}

	/**
	 * Get the logger instance.
	 *
	 * @return WC_Logger
	 */
	protected static function get_logger() {
		if ( is_null( self::$logger ) ) {
			self::$logger = wc_get_logger();
		}

		return self::$logger;
	}

	/**
	 * Returns the key identifier of spec, this can easily be overwritten. Defaults to id.
	 *
	 * @param mixed $spec a JSON parsed spec coming from the JSON feed.
	 * @return string|boolean
	 */
	protected function get_spec_key( $spec ) {
		$key = $this->args['spec_key'];
		if ( isset( $spec->$key ) ) {
			return $spec->$key;
		}
		return false;
	}

	/**
	 * Reads the data sources for specs and persists those specs.
	 *
	 * @return array list of specs.
	 */
	public function get_specs_from_data_sources() {
		$locale      = get_user_locale();
		$specs_group = get_transient( $this->args['transient_name'] ) ?? array();
		$specs       = isset( $specs_group[ $locale ] ) ? $specs_group[ $locale ] : array();

		if ( ! is_array( $specs ) || empty( $specs ) ) {
			$this->read_specs_from_data_sources();
			$specs_group = get_transient( $this->args['transient_name'] );
			$specs       = isset( $specs_group[ $locale ] ) ? $specs_group[ $locale ] : array();
		}
		$specs = apply_filters( self::FILTER_NAME_SPECS, $specs, $this->id );
		return $specs !== false ? $specs : array();
	}

	/**
	 * Reads the data sources for specs and persists those specs.
	 *
	 * @return bool Whether any specs were read.
	 */
	public function read_specs_from_data_sources() {
		$specs        = array();
		$data_sources = apply_filters( self::FILTER_NAME, $this->data_sources, $this->id );

		// Note that this merges the specs from the data sources based on the
		// id - last one wins.
		foreach ( $data_sources as $url ) {
			$specs_from_data_source = self::read_data_source( $url );
			$this->merge_specs( $specs_from_data_source, $specs, $url );
		}

		$specs_group            = get_transient( $this->args['transient_name'] ) ?? array();
		$locale                 = get_user_locale();
		$specs_group[ $locale ] = $specs;
		// Persist the specs as a transient.
		set_transient(
			$this->args['transient_name'],
			$specs_group,
			$this->args['transient_expiry']
		);
		return count( $specs ) !== 0;
	}

	/**
	 * Delete the specs transient.
	 *
	 * @return bool success of failure of transient deletion.
	 */
	public function delete_specs_transient() {
		return delete_transient( $this->args['transient_name'] );
	}

	/**
	 * Read a single data source and return the read specs
	 *
	 * @param string $url The URL to read the specs from.
	 *
	 * @return array The specs that have been read from the data source.
	 */
	protected static function read_data_source( $url ) {
		$logger_context = array( 'source' => $url );
		$logger         = self::get_logger();
		$response       = wp_remote_get(
			add_query_arg(
				'locale',
				get_user_locale(),
				$url
			),
			array(
				'user-agent' => 'WooCommerce/' . WC_VERSION . '; ' . home_url( '/' ),
			)
		);

		if ( is_wp_error( $response ) || ! isset( $response['body'] ) ) {
			$logger->error(
				'Error getting data feed',
				$logger_context
			);
			// phpcs:ignore
			$logger->error( print_r( $response, true ), $logger_context );

			return [];
		}

		$body  = $response['body'];
		$specs = json_decode( $body );

		if ( $specs === null ) {
			$logger->error(
				'Empty response in data feed',
				$logger_context
			);

			return [];
		}

		if ( ! is_array( $specs ) ) {
			$logger->error(
				'Data feed is not an array',
				$logger_context
			);

			return [];
		}

		return $specs;
	}

	/**
	 * Merge the specs.
	 *
	 * @param Array  $specs_to_merge_in The specs to merge in to $specs.
	 * @param Array  $specs             The list of specs being merged into.
	 * @param string $url               The url of the feed being merged in (for error reporting).
	 */
	protected function merge_specs( $specs_to_merge_in, &$specs, $url ) {
		foreach ( $specs_to_merge_in as $spec ) {
			if ( ! $this->validate_spec( $spec, $url ) ) {
				continue;
			}

			$id           = $this->get_spec_key( $spec );
			$specs[ $id ] = $spec;
		}
	}

	/**
	 * Validate the spec.
	 *
	 * @param object $spec The spec to validate.
	 * @param string $url  The url of the feed that provided the spec.
	 *
	 * @return bool The result of the validation.
	 */
	protected function validate_spec( $spec, $url ) {
		$logger         = self::get_logger();
		$logger_context = array( 'source' => $url );

		if ( ! $this->get_spec_key( $spec ) ) {
			$logger->error(
				'Spec is invalid because the id is missing in feed',
				$logger_context
			);
			// phpcs:ignore
			$logger->error( print_r( $spec, true ), $logger_context );

			return false;
		}

		return true;
	}
}
