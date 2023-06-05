<?php
namespace Automattic\WooCommerce\Blocks\Domain\Services;

/**
 * Service class that handles the feature flags.
 *
 * @internal
 */
class FeatureGating {

	/**
	 * Current flag value.
	 *
	 * @var int
	 */
	private $flag;

	const EXPERIMENTAL_FLAG   = 3;
	const FEATURE_PLUGIN_FLAG = 2;
	const CORE_FLAG           = 1;

	/**
	 * Current environment
	 *
	 * @var string
	 */
	private $environment;

	const PRODUCTION_ENVIRONMENT  = 'production';
	const DEVELOPMENT_ENVIRONMENT = 'development';
	const TEST_ENVIRONMENT        = 'test';

	/**
	 * Constructor
	 *
	 * @param int    $flag        Hardcoded flag value. Useful for tests.
	 * @param string $environment Hardcoded environment value. Useful for tests.
	 */
	public function __construct( $flag = 0, $environment = 'unset' ) {
		$this->flag        = $flag;
		$this->environment = $environment;
		$this->load_flag();
		$this->load_environment();
	}

	/**
	 * Set correct flag.
	 */
	public function load_flag() {
		if ( 0 === $this->flag ) {
			$default_flag = defined( 'WC_BLOCKS_IS_FEATURE_PLUGIN' ) ? self::FEATURE_PLUGIN_FLAG : self::CORE_FLAG;
			if ( file_exists( __DIR__ . '/../../../blocks.ini' ) ) {
				$allowed_flags = [ self::EXPERIMENTAL_FLAG, self::FEATURE_PLUGIN_FLAG, self::CORE_FLAG ];
				$woo_options   = parse_ini_file( __DIR__ . '/../../../blocks.ini' );
				$this->flag    = is_array( $woo_options ) && in_array( intval( $woo_options['woocommerce_blocks_phase'] ), $allowed_flags, true ) ? $woo_options['woocommerce_blocks_phase'] : $default_flag;
			} else {
				$this->flag = $default_flag;
			}
		}
	}

		/**
		 * Set correct environment.
		 */
	public function load_environment() {
		if ( 'unset' === $this->environment ) {
			if ( file_exists( __DIR__ . '/../../../blocks.ini' ) ) {
				$allowed_environments = [ self::PRODUCTION_ENVIRONMENT, self::DEVELOPMENT_ENVIRONMENT, self::TEST_ENVIRONMENT ];
				$woo_options          = parse_ini_file( __DIR__ . '/../../../blocks.ini' );
				$this->environment    = is_array( $woo_options ) && in_array( $woo_options['woocommerce_blocks_env'], $allowed_environments, true ) ? $woo_options['woocommerce_blocks_env'] : self::PRODUCTION_ENVIRONMENT;
			} else {
				$this->environment = self::PRODUCTION_ENVIRONMENT;
			}
		}
	}

	/**
	 * Returns the current flag value.
	 *
	 * @return int
	 */
	public function get_flag() {
		return $this->flag;
	}

	/**
	 * Checks if we're executing the code in an experimental build mode.
	 *
	 * @return boolean
	 */
	public function is_experimental_build() {
		return $this->flag >= self::EXPERIMENTAL_FLAG;
	}

	/**
	 * Checks if we're executing the code in an feature plugin or experimental build mode.
	 *
	 * @return boolean
	 */
	public function is_feature_plugin_build() {
		return $this->flag >= self::FEATURE_PLUGIN_FLAG;
	}

	/**
	 * Returns the current environment value.
	 *
	 * @return string
	 */
	public function get_environment() {
		return $this->environment;
	}

	/**
	 * Checks if we're executing the code in an development environment.
	 *
	 * @return boolean
	 */
	public function is_development_environment() {
		return self::DEVELOPMENT_ENVIRONMENT === $this->environment;
	}

	/**
	 * Checks if we're executing the code in a production environment.
	 *
	 * @return boolean
	 */
	public function is_production_environment() {
		return self::PRODUCTION_ENVIRONMENT === $this->environment;
	}

	/**
	 * Checks if we're executing the code in a test environment.
	 *
	 * @return boolean
	 */
	public function is_test_environment() {
		return self::TEST_ENVIRONMENT === $this->environment;
	}

	/**
	 * Returns core flag value.
	 *
	 * @return number
	 */
	public static function get_core_flag() {
		return self::CORE_FLAG;
	}

	/**
	 * Returns feature plugin flag value.
	 *
	 * @return number
	 */
	public static function get_feature_plugin_flag() {
		return self::FEATURE_PLUGIN_FLAG;
	}

	/**
	 * Returns experimental flag value.
	 *
	 * @return number
	 */
	public static function get_experimental_flag() {
		return self::EXPERIMENTAL_FLAG;
	}

}
