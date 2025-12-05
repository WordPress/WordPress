<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Editors\Domain\Analysis_Features;

/**
 * This class describes a list of analysis features.
 */
class Analysis_Features_List {

	/**
	 * The features.
	 *
	 * @var array<Analysis_Feature>
	 */
	private $features = [];

	/**
	 * Adds an analysis feature to the list.
	 *
	 * @param Analysis_Feature $feature The analysis feature to add.
	 *
	 * @return void
	 */
	public function add_feature( Analysis_Feature $feature ): void {
		$this->features[] = $feature;
	}

	/**
	 * Parses the feature list to a legacy ready array representation.
	 *
	 * @return array<string, bool> The list presented as a key value representation.
	 */
	public function parse_to_legacy_array(): array {
		$array = [];
		foreach ( $this->features as $feature ) {
			$array = \array_merge( $array, $feature->to_legacy_array() );
		}

		return $array;
	}

	/**
	 * Parses the feature list to an array representation.
	 *
	 * @return array<string, bool> The list presented as a key value representation.
	 */
	public function to_array(): array {
		$array = [];
		foreach ( $this->features as $feature ) {
			$array = \array_merge( $array, $feature->to_array() );
		}

		return $array;
	}
}
