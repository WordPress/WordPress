<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Metabox
 */

/**
 * Represents the inclusive language analysis.
 */
class WPSEO_Metabox_Analysis_Inclusive_Language implements WPSEO_Metabox_Analysis {

	/**
	 * Whether this analysis is enabled.
	 *
	 * @return bool Whether or not this analysis is enabled.
	 */
	public function is_enabled() {
		return $this->is_globally_enabled() && $this->is_user_enabled() && $this->is_current_version_supported()
				&& YoastSEO()->helpers->language->has_inclusive_language_support( WPSEO_Language_Utils::get_language( get_locale() ) );
	}

	/**
	 * Whether or not this analysis is enabled by the user.
	 *
	 * @return bool Whether or not this analysis is enabled by the user.
	 */
	public function is_user_enabled() {
		return ! get_the_author_meta( 'wpseo_inclusive_language_analysis_disable', get_current_user_id() );
	}

	/**
	 * Whether or not this analysis is enabled globally.
	 *
	 * @return bool Whether or not this analysis is enabled globally.
	 */
	public function is_globally_enabled() {
		return WPSEO_Options::get( 'inclusive_language_analysis_active', false );
	}

	/**
	 * Whether the inclusive language analysis should be loaded in Free.
	 *
	 * It should always be loaded when Premium is not active. If Premium is active, it depends on the version. Some Premium
	 * versions also have inclusive language code (when it was still a Premium only feature) which would result in rendering
	 * the analysis twice. In those cases, the analysis should be only loaded from the Premium side.
	 *
	 * @return bool Whether or not the inclusive language analysis should be loaded.
	 */
	private function is_current_version_supported() {
		$is_premium      = YoastSEO()->helpers->product->is_premium();
		$premium_version = YoastSEO()->helpers->product->get_premium_version();

		return ! $is_premium
			|| version_compare( $premium_version, '19.6-RC0', '>=' )
			|| version_compare( $premium_version, '19.2', '==' );
	}
}
