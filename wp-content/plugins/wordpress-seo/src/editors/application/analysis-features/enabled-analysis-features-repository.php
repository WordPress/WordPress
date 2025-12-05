<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Editors\Application\Analysis_Features;

use Yoast\WP\SEO\Editors\Domain\Analysis_Features\Analysis_Feature;
use Yoast\WP\SEO\Editors\Domain\Analysis_Features\Analysis_Feature_Interface;
use Yoast\WP\SEO\Editors\Domain\Analysis_Features\Analysis_Features_List;

/**
 * The repository to get all enabled features.
 *
 * @makePublic
 */
class Enabled_Analysis_Features_Repository {

	/**
	 * All plugin features.
	 *
	 * @var Analysis_Feature_Interface[]
	 */
	private $plugin_features;

	/**
	 * The list of analysis features.
	 *
	 * @var Analysis_Features_List
	 */
	private $enabled_analysis_features;

	/**
	 * The constructor.
	 *
	 * @param Analysis_Feature_Interface ...$plugin_features All analysis objects.
	 */
	public function __construct( Analysis_Feature_Interface ...$plugin_features ) {
		$this->enabled_analysis_features = new Analysis_Features_List();
		$this->plugin_features           = $plugin_features;
	}

	/**
	 * Returns the analysis list.
	 *
	 * @return Analysis_Features_List The analysis list.
	 */
	public function get_enabled_features(): Analysis_Features_List {
		if ( \count( $this->enabled_analysis_features->parse_to_legacy_array() ) === 0 ) {
			foreach ( $this->plugin_features as $plugin_feature ) {
				$analysis_feature = new Analysis_Feature( $plugin_feature->is_enabled(), $plugin_feature->get_name(), $plugin_feature->get_legacy_key() );
				$this->enabled_analysis_features->add_feature( $analysis_feature );
			}
		}

		return $this->enabled_analysis_features;
	}

	/**
	 * Returns the analysis list for the given names.
	 *
	 * @param array<string> $feature_names The feature names to include.
	 *
	 * @return Analysis_Features_List The analysis list.
	 */
	public function get_features_by_keys( array $feature_names ): Analysis_Features_List {
		$enabled_analysis_features = new Analysis_Features_List();

		foreach ( $this->plugin_features as $plugin_feature ) {
			if ( \in_array( $plugin_feature->get_name(), $feature_names, true ) ) {
				$analysis_feature = new Analysis_Feature( $plugin_feature->is_enabled(), $plugin_feature->get_name(), $plugin_feature->get_legacy_key() );
				$enabled_analysis_features->add_feature( $analysis_feature );
			}
		}

		return $enabled_analysis_features;
	}
}
