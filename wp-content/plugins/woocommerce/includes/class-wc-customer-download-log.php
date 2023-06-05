<?php
/**
 * Class for customer download logs.
 *
 * @package WooCommerce\Classes
 * @version 3.3.0
 * @since   3.3.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Customer download log class.
 */
class WC_Customer_Download_Log extends WC_Data {

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'customer_download_log';

	/**
	 * Download Log Data array.
	 *
	 * @var array
	 */
	protected $data = array(
		'timestamp'       => null,
		'permission_id'   => 0,
		'user_id'         => null,
		'user_ip_address' => null,
	);

	/**
	 * Constructor.
	 *
	 * @param int|object|array $download_log Download log ID.
	 */
	public function __construct( $download_log = 0 ) {
		parent::__construct( $download_log );

		if ( is_numeric( $download_log ) && $download_log > 0 ) {
			$this->set_id( $download_log );
		} elseif ( $download_log instanceof self ) {
			$this->set_id( $download_log->get_id() );
		} elseif ( is_object( $download_log ) && ! empty( $download_log->download_log_id ) ) {
			$this->set_id( $download_log->download_log_id );
			$this->set_props( (array) $download_log );
			$this->set_object_read( true );
		} else {
			$this->set_object_read( true );
		}

		$this->data_store = WC_Data_Store::load( 'customer-download-log' );

		if ( $this->get_id() > 0 ) {
			$this->data_store->read( $this );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get timestamp.
	 *
	 * @param  string $context Get context.
	 * @return WC_DateTime|null Object if the date is set or null if there is no date.
	 */
	public function get_timestamp( $context = 'view' ) {
		return $this->get_prop( 'timestamp', $context );
	}

	/**
	 * Get permission id.
	 *
	 * @param  string $context Get context.
	 * @return integer
	 */
	public function get_permission_id( $context = 'view' ) {
		return $this->get_prop( 'permission_id', $context );
	}

	/**
	 * Get user id.
	 *
	 * @param  string $context Get context.
	 * @return integer
	 */
	public function get_user_id( $context = 'view' ) {
		return $this->get_prop( 'user_id', $context );
	}

	/**
	 * Get user ip address.
	 *
	 * @param  string $context Get context.
	 * @return string
	 */
	public function get_user_ip_address( $context = 'view' ) {
		return $this->get_prop( 'user_ip_address', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set timestamp.
	 *
	 * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
	 */
	public function set_timestamp( $date = null ) {
		$this->set_date_prop( 'timestamp', $date );
	}

	/**
	 * Set permission id.
	 *
	 * @param int $value Value to set.
	 */
	public function set_permission_id( $value ) {
		$this->set_prop( 'permission_id', absint( $value ) );
	}

	/**
	 * Set user id.
	 *
	 * @param int $value Value to set.
	 */
	public function set_user_id( $value ) {
		$this->set_prop( 'user_id', absint( $value ) );
	}

	/**
	 * Set user ip address.
	 *
	 * @param string $value Value to set.
	 */
	public function set_user_ip_address( $value ) {
		$this->set_prop( 'user_ip_address', $value );
	}
}
