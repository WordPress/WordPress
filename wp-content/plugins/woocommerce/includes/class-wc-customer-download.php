<?php
/**
 * Class for customer download permissions.
 *
 * @package WooCommerce\Classes
 * @version 3.0.0
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Customer download class.
 */
class WC_Customer_Download extends WC_Data implements ArrayAccess {

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'customer_download';

	/**
	 * Download Data array.
	 *
	 * @since 3.0.0
	 * @var array
	 */
	protected $data = array(
		'download_id'         => '',
		'product_id'          => 0,
		'user_id'             => 0,
		'user_email'          => '',
		'order_id'            => 0,
		'order_key'           => '',
		'downloads_remaining' => '',
		'access_granted'      => null,
		'access_expires'      => null,
		'download_count'      => 0,
	);

	/**
	 * Constructor.
	 *
	 * @param int|object|array $download Download ID, instance or data.
	 */
	public function __construct( $download = 0 ) {
		parent::__construct( $download );

		if ( is_numeric( $download ) && $download > 0 ) {
			$this->set_id( $download );
		} elseif ( $download instanceof self ) {
			$this->set_id( $download->get_id() );
		} elseif ( is_object( $download ) && ! empty( $download->permission_id ) ) {
			$this->set_id( $download->permission_id );
			$this->set_props( (array) $download );
			$this->set_object_read( true );
		} else {
			$this->set_object_read( true );
		}

		$this->data_store = WC_Data_Store::load( 'customer-download' );

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
	 * Get download id.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_download_id( $context = 'view' ) {
		return $this->get_prop( 'download_id', $context );
	}

	/**
	 * Get product id.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return integer
	 */
	public function get_product_id( $context = 'view' ) {
		return $this->get_prop( 'product_id', $context );
	}

	/**
	 * Get user id.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return integer
	 */
	public function get_user_id( $context = 'view' ) {
		return $this->get_prop( 'user_id', $context );
	}

	/**
	 * Get user_email.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_user_email( $context = 'view' ) {
		return $this->get_prop( 'user_email', $context );
	}

	/**
	 * Get order_id.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return integer
	 */
	public function get_order_id( $context = 'view' ) {
		return $this->get_prop( 'order_id', $context );
	}

	/**
	 * Get order_key.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_order_key( $context = 'view' ) {
		return $this->get_prop( 'order_key', $context );
	}

	/**
	 * Get downloads_remaining.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return integer|string
	 */
	public function get_downloads_remaining( $context = 'view' ) {
		return $this->get_prop( 'downloads_remaining', $context );
	}

	/**
	 * Get access_granted.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return WC_DateTime|null Object if the date is set or null if there is no date.
	 */
	public function get_access_granted( $context = 'view' ) {
		return $this->get_prop( 'access_granted', $context );
	}

	/**
	 * Get access_expires.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return WC_DateTime|null Object if the date is set or null if there is no date.
	 */
	public function get_access_expires( $context = 'view' ) {
		return $this->get_prop( 'access_expires', $context );
	}

	/**
	 * Get download_count.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return integer
	 */
	public function get_download_count( $context = 'view' ) {
		// Check for count of download logs.
		$data_store       = WC_Data_Store::load( 'customer-download-log' );
		$download_log_ids = $data_store->get_download_logs_for_permission( $this->get_id() );

		$download_log_count = 0;
		if ( ! empty( $download_log_ids ) ) {
			$download_log_count = count( $download_log_ids );
		}

		// Check download count in prop.
		$download_count_prop = $this->get_prop( 'download_count', $context );

		// Return the larger of the two in case they differ.
		// If logs are removed for some reason, we should still respect the
		// count stored in the prop.
		return max( $download_log_count, $download_count_prop );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set download id.
	 *
	 * @param string $value Download ID.
	 */
	public function set_download_id( $value ) {
		$this->set_prop( 'download_id', $value );
	}
	/**
	 * Set product id.
	 *
	 * @param int $value Product ID.
	 */
	public function set_product_id( $value ) {
		$this->set_prop( 'product_id', absint( $value ) );
	}

	/**
	 * Set user id.
	 *
	 * @param int $value User ID.
	 */
	public function set_user_id( $value ) {
		$this->set_prop( 'user_id', absint( $value ) );
	}

	/**
	 * Set user_email.
	 *
	 * @param int $value User email.
	 */
	public function set_user_email( $value ) {
		$this->set_prop( 'user_email', sanitize_email( $value ) );
	}

	/**
	 * Set order_id.
	 *
	 * @param int $value Order ID.
	 */
	public function set_order_id( $value ) {
		$this->set_prop( 'order_id', absint( $value ) );
	}

	/**
	 * Set order_key.
	 *
	 * @param string $value Order key.
	 */
	public function set_order_key( $value ) {
		$this->set_prop( 'order_key', $value );
	}

	/**
	 * Set downloads_remaining.
	 *
	 * @param integer|string $value Amount of downloads remaining.
	 */
	public function set_downloads_remaining( $value ) {
		$this->set_prop( 'downloads_remaining', '' === $value ? '' : absint( $value ) );
	}

	/**
	 * Set access_granted.
	 *
	 * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
	 */
	public function set_access_granted( $date = null ) {
		$this->set_date_prop( 'access_granted', $date );
	}

	/**
	 * Set access_expires.
	 *
	 * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
	 */
	public function set_access_expires( $date = null ) {
		$this->set_date_prop( 'access_expires', $date );
	}

	/**
	 * Set download_count.
	 *
	 * @param int $value Download count.
	 */
	public function set_download_count( $value ) {
		$this->set_prop( 'download_count', absint( $value ) );
	}

	/**
	 * Track a download on this permission.
	 *
	 * @since 3.3.0
	 * @throws Exception When permission ID is invalid.
	 * @param int    $user_id         Id of the user performing the download.
	 * @param string $user_ip_address IP Address of the user performing the download.
	 */
	public function track_download( $user_id = null, $user_ip_address = null ) {
		global $wpdb;

		// Must have a permission_id to track download log.
		if ( ! ( $this->get_id() > 0 ) ) {
			throw new Exception( __( 'Invalid permission ID.', 'woocommerce' ) );
		}

		// Increment download count, and decrement downloads remaining.
		// Use SQL to avoid possible issues with downloads in quick succession.
		// If downloads_remaining is blank, leave it blank (unlimited).
		// Also, ensure downloads_remaining doesn't drop below zero.
		$query = $wpdb->prepare(
			"
UPDATE {$wpdb->prefix}woocommerce_downloadable_product_permissions
SET download_count = download_count + 1,
downloads_remaining = IF( downloads_remaining = '', '', GREATEST( 0, downloads_remaining - 1 ) )
WHERE permission_id = %d",
			$this->get_id()
		);
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$wpdb->query( $query );

		// Re-read this download from the data store to pull updated counts.
		$this->data_store->read( $this );

		// Track download in download log.
		$download_log = new WC_Customer_Download_Log();
		$download_log->set_timestamp( time() );
		$download_log->set_permission_id( $this->get_id() );

		if ( ! is_null( $user_id ) ) {
			$download_log->set_user_id( $user_id );
		}

		if ( ! is_null( $user_ip_address ) ) {
			$download_log->set_user_ip_address( $user_ip_address );
		}

		$download_log->save();
	}

	/*
	|--------------------------------------------------------------------------
	| ArrayAccess/Backwards compatibility.
	|--------------------------------------------------------------------------
	*/

	/**
	 * OffsetGet.
	 *
	 * @param mixed $offset Offset.
	 * @return mixed
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet( $offset ) {
		if ( is_callable( array( $this, "get_$offset" ) ) ) {
			return $this->{"get_$offset"}();
		}
	}

	/**
	 * OffsetSet.
	 *
	 * @param mixed $offset Offset.
	 * @param mixed $value  Value.
	 */
	#[\ReturnTypeWillChange]
	public function offsetSet( $offset, $value ) {
		if ( is_callable( array( $this, "set_$offset" ) ) ) {
			$this->{"set_$offset"}( $value );
		}
	}

	/**
	 * OffsetUnset
	 *
	 * @param mixed $offset Offset.
	 */
	#[\ReturnTypeWillChange]
	public function offsetUnset( $offset ) {
		if ( is_callable( array( $this, "set_$offset" ) ) ) {
			$this->{"set_$offset"}( '' );
		}
	}

	/**
	 * OffsetExists.
	 *
	 * @param mixed $offset Offset.
	 * @return bool
	 */
	#[\ReturnTypeWillChange]
	public function offsetExists( $offset ) {
		return in_array( $offset, array_keys( $this->data ), true );
	}

	/**
	 * Magic __isset method for backwards compatibility. Legacy properties which could be accessed directly in the past.
	 *
	 * @param  string $key Key name.
	 * @return bool
	 */
	public function __isset( $key ) {
		return in_array( $key, array_keys( $this->data ), true );
	}

	/**
	 * Magic __get method for backwards compatibility. Maps legacy vars to new getters.
	 *
	 * @param  string $key Key name.
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( is_callable( array( $this, "get_$key" ) ) ) {
			return $this->{"get_$key"}( '' );
		}
	}
}
