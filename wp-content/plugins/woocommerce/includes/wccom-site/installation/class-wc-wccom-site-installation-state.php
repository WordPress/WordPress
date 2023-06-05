<?php
/**
 * State for the WCCOM Site installation process.
 *
 * @package WooCommerce\WCCOM
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_WCCOM_Site_Installation_State class
 */
class WC_WCCOM_Site_Installation_State {
	/**
	 * The product ID.
	 *
	 * @var string
	 */
	protected $product_id;
	/**
	 * The idempotency key.
	 *
	 * @var string
	 */
	protected $idempotency_key;
	/**
	 * The last step name.
	 *
	 * @var string
	 */
	protected $last_step_name;
	/**
	 * The last step status.
	 *
	 * @var string
	 */
	protected $last_step_status;
	/**
	 * The last step error.
	 *
	 * @var string
	 */
	protected $last_step_error;

	/**
	 * The product type.
	 *
	 * @var string
	 */
	protected $product_type;
	/**
	 * The product name.
	 *
	 * @var string
	 */
	protected $product_name;
	/**
	 * The product slug.
	 *
	 * @var string
	 */
	protected $download_url;
	/**
	 * The path to the downloaded file.
	 *
	 * @var string
	 */
	protected $download_path;
	/**
	 * The path to the unpacked file.
	 *
	 * @var string
	 */
	protected $unpacked_path;
	/**
	 * The path to the installed file.
	 *
	 * @var string
	 */
	protected $installed_path;
	/**
	 * The plugin info for the already installed plugin.
	 *
	 * @var array
	 */
	protected $already_installed_plugin_info;
	/**
	 * The timestamp of the installation start.
	 *
	 * @var int
	 */
	protected $started_date;

	const STEP_STATUS_IN_PROGRESS = 'in-progress';
	const STEP_STATUS_FAILED      = 'failed';
	const STEP_STATUS_COMPLETED   = 'completed';

	/**
	 * Constructor.
	 *
	 * @param string $product_id The product ID.
	 */
	protected function __construct( $product_id ) {
		$this->product_id = $product_id;
	}

	/**
	 * Initiate an existing installation state.
	 *
	 * @param int    $product_id The product ID.
	 * @param string $idempotency_key The idempotency key.
	 * @param string $last_step_name The last step name.
	 * @param string $last_step_status The last step status.
	 * @param string $last_step_error The last step error.
	 * @param int    $started_date The timestamp of the installation start.
	 * @return WC_WCCOM_Site_Installation_State The instance.
	 */
	public static function initiate_existing( $product_id, $idempotency_key, $last_step_name, $last_step_status, $last_step_error, $started_date ) {
		$instance                   = new self( $product_id );
		$instance->idempotency_key  = $idempotency_key;
		$instance->last_step_name   = $last_step_name;
		$instance->last_step_status = $last_step_status;
		$instance->last_step_error  = $last_step_error;
		$instance->started_date     = $started_date;

		return $instance;
	}

	/**
	 * Initiate a new installation state.
	 *
	 * @param init   $product_id The product ID.
	 * @param string $idempotency_key The idempotency key.
	 * @return WC_WCCOM_Site_Installation_State The instance.
	 */
	public static function initiate_new( $product_id, $idempotency_key ) {
		$instance                  = new self( $product_id );
		$instance->idempotency_key = $idempotency_key;
		$instance->started_date    = time();

		return $instance;
	}

	/**
	 * Get the product ID.
	 *
	 * @return string
	 */
	public function get_product_id() {
		return $this->product_id;
	}

	/**
	 * Get the idempotency key.
	 *
	 * @return string
	 */
	public function get_idempotency_key() {
		return $this->idempotency_key;
	}

	/**
	 * Get the timestamp of the installation start.
	 *
	 * @return int
	 */
	public function get_last_step_name() {
		return $this->last_step_name;
	}

	/**
	 * Get the last step status.
	 *
	 * @return string
	 */
	public function get_last_step_status() {
		return $this->last_step_status;
	}

	/**
	 * Get the last step error.
	 *
	 * @return int
	 */
	public function get_last_step_error() {
		return $this->last_step_error;
	}

	/**
	 * Initiate a step.
	 *
	 * @param string $step_name Step name.
	 * @return void
	 */
	public function initiate_step( $step_name ) {
		$this->last_step_name   = $step_name;
		$this->last_step_status = self::STEP_STATUS_IN_PROGRESS;
	}

	/**
	 * Capture a successful installation of a step.
	 *
	 * @param string $step_name The step name.
	 */
	public function complete_step( $step_name ) {
		$this->last_step_name   = $step_name;
		$this->last_step_status = self::STEP_STATUS_COMPLETED;
	}

	/**
	 * Capture an installation failure.
	 *
	 * @param string $step_name  The step name.
	 * @param string $error_code The error code.
	 */
	public function capture_failure( $step_name, $error_code ) {
		$this->last_step_name   = $step_name;
		$this->last_step_error  = $error_code;
		$this->last_step_status = self::STEP_STATUS_FAILED;
	}

	/**
	 * Get the product type.
	 *
	 * @return string
	 */
	public function get_product_type() {
		return $this->product_type;
	}

	/**
	 * Set the product type.
	 *
	 * @param string $product_type The product type.
	 */
	public function set_product_type( $product_type ) {
		$this->product_type = $product_type;
	}
	/**
	 * Get the product name.
	 *
	 * @return string
	 */
	public function get_product_name() {
		return $this->product_name;
	}
	/**
	 * Set the product name.
	 *
	 * @param string $product_name The product name.
	 */
	public function set_product_name( $product_name ) {
		$this->product_name = $product_name;
	}
	/**
	 * Get the download URL.
	 *
	 * @return string
	 */
	public function get_download_url() {
		return $this->download_url;
	}
	/**
	 * Set the download URL.
	 *
	 * @param string $download_url The download URL.
	 */
	public function set_download_url( $download_url ) {
		$this->download_url = $download_url;
	}
	/**
	 * Get the path to the downloaded file.
	 *
	 * @return string
	 */
	public function get_download_path() {
		return $this->download_path;
	}
	/**
	 * Set the path to the downloaded file.
	 *
	 * @param string $download_path The path to the downloaded file.
	 */
	public function set_download_path( $download_path ) {
		$this->download_path = $download_path;
	}
	/**
	 * Get the path to the unpacked file.
	 *
	 * @return string
	 */
	public function get_unpacked_path() {
		return $this->unpacked_path;
	}
	/**
	 * Set the path to the unpacked file.
	 *
	 * @param string $unpacked_path The path to the unpacked file.
	 */
	public function set_unpacked_path( $unpacked_path ) {
		$this->unpacked_path = $unpacked_path;
	}
	/**
	 * Get the path to the installed file.
	 *
	 * @return string
	 */
	public function get_installed_path() {
		return $this->installed_path;
	}
	/**
	 * Set the path to the installed file.
	 *
	 * @param string $installed_path The path to the installed file.
	 */
	public function set_installed_path( $installed_path ) {
		$this->installed_path = $installed_path;
	}
	/**
	 * Get the plugin info for the already installed plugin.
	 *
	 * @return array
	 */
	public function get_already_installed_plugin_info() {
		return $this->already_installed_plugin_info;
	}
	/**
	 * Set the plugin info for the already installed plugin.
	 *
	 * @param array $plugin_info The plugin info.
	 */
	public function set_already_installed_plugin_info( $plugin_info ) {
		$this->already_installed_plugin_info = $plugin_info;
	}
	/**
	 * Get the timestamp of the installation start.
	 *
	 * @return int
	 */
	public function get_started_date() {
		return $this->started_date;
	}
}
