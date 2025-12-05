<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Alerts\Infrastructure\Default_SEO_Data;

use Yoast\WP\SEO\Helpers\Options_Helper;

/**
 * Class that collects default SEO data.
 *
 * @makePublic
 */
class Default_SEO_Data_Collector {

	/**
	 * Holds the Options_Helper instance.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * The constructor.
	 *
	 * @param Options_Helper $options_helper The options helper.
	 */
	public function __construct( Options_Helper $options_helper ) {
		$this->options_helper = $options_helper;
	}

	/**
	 * Returns the posts with default SEO title in their most recent.
	 *
	 * @return string[] The posts with default SEO title in their most recent.
	 */
	public function get_posts_with_default_seo_title(): array {
		return $this->options_helper->get( 'default_seo_title', [] );
	}

	/**
	 * Returns the posts with default SEO description in their most recent.
	 *
	 * @return string[] The posts with default SEO description in their most recent.
	 */
	public function get_posts_with_default_seo_description(): array {
		return $this->options_helper->get( 'default_seo_meta_desc', [] );
	}
}
