<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Editors\Domain\Analysis_Features;

/**
 * This class describes a single Analysis feature and if it is enabled.
 */
class Analysis_Feature {

	/**
	 * If the feature is enabled.
	 *
	 * @var bool
	 */
	private $is_enabled;

	/**
	 * What the identifier of the feature is.
	 *
	 * @var string
	 */
	private $name;

	/**
	 * What the identifier is for the old script data array.
	 *
	 * @var string
	 */
	private $legacy_key;

	/**
	 * The constructor.
	 *
	 * @param bool   $is_enabled If the feature is enabled.
	 * @param string $name       What the identifier of the feature is.
	 * @param string $legacy_key What the identifier is for the old script data array.
	 */
	public function __construct( bool $is_enabled, string $name, string $legacy_key ) {
		$this->is_enabled = $is_enabled;
		$this->name       = $name;
		$this->legacy_key = $legacy_key;
	}

	/**
	 * If the feature is enabled.
	 *
	 * @return bool If the feature is enabled.
	 */
	public function is_enabled(): bool {
		return $this->is_enabled;
	}

	/**
	 * Gets the identifier.
	 *
	 * @return string The feature identifier.
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Return this object represented by a key value array.
	 *
	 * @return array<string, bool> Returns the name and if the feature is enabled.
	 */
	public function to_array(): array {
		return [ $this->name => $this->is_enabled ];
	}

	/**
	 * Returns this object represented by a key value structure that is compliant with the script data array.
	 *
	 * @return array<string, bool> Returns the legacy key and if the feature is enabled.
	 */
	public function to_legacy_array(): array {
		return [ $this->legacy_key => $this->is_enabled ];
	}
}
