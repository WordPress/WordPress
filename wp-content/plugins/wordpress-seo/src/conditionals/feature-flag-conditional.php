<?php

namespace Yoast\WP\SEO\Conditionals;

/**
 * Abstract class for creating conditionals based on feature flags.
 */
abstract class Feature_Flag_Conditional implements Conditional {

	/**
	 * Returns whether or not this conditional is met.
	 *
	 * @return bool Whether or not the conditional is met.
	 */
	public function is_met() {
		$feature_flag = \strtoupper( $this->get_feature_flag() );

		return \defined( 'YOAST_SEO_' . $feature_flag ) && \constant( 'YOAST_SEO_' . $feature_flag ) === true;
	}

	/**
	 * Returns the name of the feature flag.
	 * 'YOAST_SEO_' is automatically prepended to it and it will be uppercased.
	 *
	 * @return string the name of the feature flag.
	 */
	abstract protected function get_feature_flag();

	/**
	 * Returns the feature name.
	 *
	 * @return string the name of the feature flag.
	 */
	public function get_feature_name() {
		return $this->get_feature_flag();
	}
}
