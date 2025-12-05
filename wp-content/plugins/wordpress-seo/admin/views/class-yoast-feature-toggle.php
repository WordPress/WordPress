<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * Class representing a feature toggle.
 */
class Yoast_Feature_Toggle {

	/**
	 * Feature toggle identifier.
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * Name of the setting the feature toggle is associated with.
	 *
	 * @var string
	 */
	protected $setting = '';

	/**
	 * Whether the feature is premium or not.
	 *
	 * @var bool
	 */
	protected $premium = false;

	/**
	 * Whether the feature is in beta or not.
	 *
	 * @var bool
	 */
	protected $in_beta = false;

	/**
	 * The Premium version in which this feature has been added.
	 *
	 * @var string
	 */
	protected $premium_version = '';

	/**
	 * The languages in which this feature is supported.
	 * E.g. for language specific analysis support.
	 *
	 * If empty, the feature is considered to have support in all languages.
	 *
	 * @var string[]
	 */
	protected $supported_languages = [];

	/**
	 * Feature toggle label.
	 *
	 * @var string
	 */
	protected $label = '';

	/**
	 * URL to learn more about the feature.
	 *
	 * @var string
	 */
	protected $read_more_url = '';

	/**
	 * URL to learn more about the premium feature.
	 *
	 * @var string
	 */
	protected $premium_url = '';

	/**
	 * URL to buy premium.
	 *
	 * @var string
	 */
	protected $premium_upsell_url = '';

	/**
	 * Label for the learn more link.
	 *
	 * @var string
	 */
	protected $read_more_label = '';

	/**
	 * Additional help content for the feature.
	 *
	 * @var string
	 */
	protected $extra = '';

	/**
	 * Additional content to be rendered after the toggle.
	 *
	 * @var string
	 */
	protected $after = '';

	/**
	 * Value to specify the feature toggle order.
	 *
	 * @var int
	 */
	protected $order = 100;

	/**
	 * Disable the integration toggle.
	 *
	 * @var bool
	 */
	protected $disabled = false;

	/**
	 * Whether the feature is new or not.
	 *
	 * @var bool
	 */
	protected $new = false;

	/**
	 * Constructor.
	 *
	 * Sets the feature toggle arguments.
	 *
	 * @param array $args {
	 *     Feature toggle arguments.
	 *
	 *     @type string $name                Required. Feature toggle identifier.
	 *     @type string $setting             Required. Name of the setting the feature toggle is associated with.
	 *     @type string $disabled            Whether the feature is premium or not.
	 *     @type string $label               Feature toggle label.
	 *     @type string $read_more_url       URL to learn more about the feature. Default empty string.
	 *     @type string $premium_upsell_url  URL to buy premium. Default empty string.
	 *     @type string $read_more_label     Label for the learn more link. Default empty string.
	 *     @type string $extra               Additional help content for the feature. Default empty string.
	 *     @type int    $order               Value to specify the feature toggle order. A lower value indicates
	 *                                       a higher priority. Default 100.
	 *     @type bool   $disabled            Disable the integration toggle. Default false.
	 *     @type string $new                 Whether the feature is new or not.
	 *     @type bool   $in_beta             Whether the feature is in beta or not.
	 *     @type array  $supported_languages The languages that this feature supports.
	 *     @type string $premium_version     The Premium version in which this feature was added.
	 * }
	 *
	 * @throws InvalidArgumentException Thrown when a required argument is missing.
	 */
	public function __construct( array $args ) {
		$required_keys = [ 'name', 'setting' ];

		foreach ( $required_keys as $key ) {
			if ( empty( $args[ $key ] ) ) {
				/* translators: %s: argument name */
				throw new InvalidArgumentException( sprintf( __( '%s is a required feature toggle argument.', 'wordpress-seo' ), $key ) );
			}
		}

		foreach ( $args as $key => $value ) {
			if ( property_exists( $this, $key ) ) {
				$this->$key = $value;
			}
		}
	}

	/**
	 * Magic isset-er.
	 *
	 * @param string $key Key to check whether a value for it is set.
	 *
	 * @return bool True if set, false otherwise.
	 */
	public function __isset( $key ) {
		return isset( $this->$key );
	}

	/**
	 * Magic getter.
	 *
	 * @param string $key Key to get the value for.
	 *
	 * @return mixed Value for the key, or null if not set.
	 */
	public function __get( $key ) {
		if ( isset( $this->$key ) ) {
			return $this->$key;
		}

		return null;
	}

	/**
	 * Checks whether the feature for this toggle is enabled.
	 *
	 * @return bool True if the feature is enabled, false otherwise.
	 */
	public function is_enabled() {
		return (bool) WPSEO_Options::get( $this->setting );
	}
}
