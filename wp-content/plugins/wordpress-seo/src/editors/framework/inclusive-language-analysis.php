<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Editors\Framework;

use Yoast\WP\SEO\Editors\Domain\Analysis_Features\Analysis_Feature_Interface;
use Yoast\WP\SEO\Helpers\Language_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Product_Helper;

/**
 * Describes the inclusive language analysis feature.
 */
class Inclusive_Language_Analysis implements Analysis_Feature_Interface {

	public const NAME = 'inclusiveLanguageAnalysis';

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * The language helper.
	 *
	 * @var Language_Helper
	 */
	private $language_helper;

	/**
	 * The product helper.
	 *
	 * @var Product_Helper
	 */
	private $product_helper;

	/**
	 * The constructor.
	 *
	 * @param Options_Helper  $options_helper  The options helper.
	 * @param Language_Helper $language_helper The language helper.
	 * @param Product_Helper  $product_helper  The product helper.
	 */
	public function __construct(
		Options_Helper $options_helper,
		Language_Helper $language_helper,
		Product_Helper $product_helper
	) {
		$this->options_helper  = $options_helper;
		$this->language_helper = $language_helper;
		$this->product_helper  = $product_helper;
	}

	/**
	 * If this analysis is enabled.
	 *
	 * @return bool If this analysis is enabled.
	 */
	public function is_enabled(): bool {
		return $this->is_globally_enabled() && $this->is_user_enabled() && $this->is_current_version_supported()
				&& $this->language_helper->has_inclusive_language_support( $this->language_helper->get_language() );
	}

	/**
	 * If this analysis is enabled by the user.
	 *
	 * @return bool If this analysis is enabled by the user.
	 */
	private function is_user_enabled(): bool {
		return ! \get_user_meta( \get_current_user_id(), 'wpseo_inclusive_language_analysis_disable', true );
	}

	/**
	 * If this analysis is enabled globally.
	 *
	 * @return bool If this analysis is enabled globally.
	 */
	private function is_globally_enabled(): bool {
		return (bool) $this->options_helper->get( 'inclusive_language_analysis_active', false );
	}

	/**
	 * If the inclusive language analysis should be loaded in Free.
	 *
	 * It should always be loaded when Premium is not active. If Premium is active, it depends on the version. Some
	 * Premium versions also have inclusive language code (when it was still a Premium only feature) which would result
	 * in rendering the analysis twice. In those cases, the analysis should be only loaded from the Premium side.
	 *
	 * @return bool If the inclusive language analysis should be loaded.
	 */
	private function is_current_version_supported(): bool {
		$is_premium      = $this->product_helper->is_premium();
		$premium_version = $this->product_helper->get_premium_version();

		return ! $is_premium
				|| \version_compare( $premium_version, '19.6-RC0', '>=' )
				|| \version_compare( $premium_version, '19.2', '==' );
	}

	/**
	 * Gets the name.
	 *
	 * @return string The name.
	 */
	public function get_name(): string {
		return self::NAME;
	}

	/**
	 * Gets the legacy key.
	 *
	 * @return string The legacy key.
	 */
	public function get_legacy_key(): string {
		return 'inclusiveLanguageAnalysisActive';
	}
}
