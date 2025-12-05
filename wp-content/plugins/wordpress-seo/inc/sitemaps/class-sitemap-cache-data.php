<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\XML_Sitemaps
 */

/**
 * Sitemap Cache Data object, manages sitemap data stored in cache.
 */
class WPSEO_Sitemap_Cache_Data implements Serializable, WPSEO_Sitemap_Cache_Data_Interface {

	/**
	 * Sitemap XML data.
	 *
	 * @var string
	 */
	private $sitemap = '';

	/**
	 * Status of the sitemap, usable or not.
	 *
	 * @var string
	 */
	private $status = self::UNKNOWN;

	/**
	 * Set the sitemap XML data
	 *
	 * @param string $sitemap XML Content of the sitemap.
	 *
	 * @return void
	 */
	public function set_sitemap( $sitemap ) {

		if ( ! is_string( $sitemap ) ) {
			$sitemap = '';
		}

		$this->sitemap = $sitemap;

		/*
		 * Empty sitemap is not usable.
		 */
		if ( ! empty( $sitemap ) ) {
			$this->set_status( self::OK );
		}
		else {
			$this->set_status( self::ERROR );
		}
	}

	/**
	 * Set the status of the sitemap, is it usable.
	 *
	 * @param bool|string $usable Is the sitemap usable or not.
	 *
	 * @return void
	 */
	public function set_status( $usable ) {

		if ( $usable === self::OK ) {
			$this->status = self::OK;

			return;
		}

		if ( $usable === self::ERROR ) {
			$this->status  = self::ERROR;
			$this->sitemap = '';

			return;
		}

		$this->status = self::UNKNOWN;
	}

	/**
	 * Is the sitemap usable.
	 *
	 * @return bool True if usable, False if bad or unknown.
	 */
	public function is_usable() {

		return $this->status === self::OK;
	}

	/**
	 * Get the XML content of the sitemap.
	 *
	 * @return string The content of the sitemap.
	 */
	public function get_sitemap() {

		return $this->sitemap;
	}

	/**
	 * Get the status of the sitemap.
	 *
	 * @return string Status of the sitemap, 'ok'/'error'/'unknown'.
	 */
	public function get_status() {

		return $this->status;
	}

	/**
	 * String representation of object.
	 *
	 * {@internal This magic method is only "magic" as of PHP 7.4 in which the magic method was introduced.}
	 *
	 * @link https://www.php.net/language.oop5.magic#object.serialize
	 * @link https://wiki.php.net/rfc/custom_object_serialization
	 *
	 * @since 17.8.0
	 *
	 * @return array The data to be serialized.
	 */
	public function __serialize() { // phpcs:ignore PHPCompatibility.FunctionNameRestrictions.NewMagicMethods.__serializeFound

		$data = [
			'status' => $this->status,
			'xml'    => $this->sitemap,
		];

		return $data;
	}

	/**
	 * Constructs the object.
	 *
	 * {@internal This magic method is only "magic" as of PHP 7.4 in which the magic method was introduced.}
	 *
	 * @link https://www.php.net/language.oop5.magic#object.serialize
	 * @link https://wiki.php.net/rfc/custom_object_serialization
	 *
	 * @since 17.8.0
	 *
	 * @param array $data The unserialized data to use to (re)construct the object.
	 *
	 * @return void
	 */
	public function __unserialize( $data ) { // phpcs:ignore PHPCompatibility.FunctionNameRestrictions.NewMagicMethods.__unserializeFound

		$this->set_sitemap( $data['xml'] );
		$this->set_status( $data['status'] );
	}

	/**
	 * String representation of object.
	 *
	 * {@internal The magic methods take precedence over the Serializable interface.
	 * This means that in practice, this method will now only be called on PHP < 7.4.
	 * For PHP 7.4 and higher, the magic methods will be used instead.}
	 *
	 * {@internal The Serializable interface is being phased out, in favour of the magic methods.
	 * This method should be deprecated and removed and the class should no longer
	 * implement the `Serializable` interface.
	 * This change, however, can't be made until the minimum PHP version goes up to PHP 7.4 or higher.}
	 *
	 * @link http://php.net/manual/en/serializable.serialize.php
	 * @link https://wiki.php.net/rfc/phase_out_serializable
	 *
	 * @since 5.1.0
	 *
	 * @return string The string representation of the object or null in C-format.
	 */
	public function serialize() {

		return serialize( $this->__serialize() );
	}

	/**
	 * Constructs the object.
	 *
	 * {@internal The magic methods take precedence over the Serializable interface.
	 * This means that in practice, this method will now only be called on PHP < 7.4.
	 * For PHP 7.4 and higher, the magic methods will be used instead.}
	 *
	 * {@internal The Serializable interface is being phased out, in favour of the magic methods.
	 * This method should be deprecated and removed and the class should no longer
	 * implement the `Serializable` interface.
	 * This change, however, can't be made until the minimum PHP version goes up to PHP 7.4 or higher.}
	 *
	 * @link http://php.net/manual/en/serializable.unserialize.php
	 * @link https://wiki.php.net/rfc/phase_out_serializable
	 *
	 * @since 5.1.0
	 *
	 * @param string $data The string representation of the object in C or O-format.
	 *
	 * @return void
	 */
	public function unserialize( $data ) {

		$data = unserialize( $data );
		$this->__unserialize( $data );
	}
}
