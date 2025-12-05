<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Formatter
 */

use Yoast\WP\SEO\Config\Schema_Types;
use Yoast\WP\SEO\Editors\Application\Analysis_Features\Enabled_Analysis_Features_Repository;
use Yoast\WP\SEO\Editors\Application\Integrations\Integration_Information_Repository;

/**
 * This class forces needed methods for the metabox localization.
 */
class WPSEO_Metabox_Formatter {

	/**
	 * Object that provides formatted values.
	 *
	 * @var WPSEO_Metabox_Formatter_Interface
	 */
	private $formatter;

	/**
	 * Setting the formatter property.
	 *
	 * @param WPSEO_Metabox_Formatter_Interface $formatter Object that provides the formatted values.
	 */
	public function __construct( WPSEO_Metabox_Formatter_Interface $formatter ) {
		$this->formatter = $formatter;
	}

	/**
	 * Returns the values.
	 *
	 * @return array<string, string|array<string|int|bool>|bool|int>
	 */
	public function get_values() {
		$defaults = $this->get_defaults();
		$values   = $this->formatter->get_values();

		return ( $values + $defaults );
	}

	/**
	 * Returns array with all the values always needed by a scraper object.
	 *
	 * @return array<string, string|array<string|int|bool>|bool|int> Default settings for the metabox.
	 */
	private function get_defaults() {
		$schema_types = new Schema_Types();

		$defaults = [
			'author_name'                        => get_the_author_meta( 'display_name' ),
			'keyword_usage'                      => [],
			'title_template'                     => '',
			'metadesc_template'                  => '',
			'schema'                             => [
				'displayFooter'      => WPSEO_Capability_Utils::current_user_can( 'wpseo_manage_options' ),
				'pageTypeOptions'    => $schema_types->get_page_type_options(),
				'articleTypeOptions' => $schema_types->get_article_type_options(),
			],
			'twitterCardType'                    => 'summary_large_image',
			/**
			 * Filter to determine if the markers should be enabled or not.
			 *
			 * @param bool $showMarkers Should the markers being enabled. Default = true.
			 */
			'show_markers'                       => apply_filters( 'wpseo_enable_assessment_markers', true ),
		];

		$integration_information_repo = YoastSEO()->classes->get( Integration_Information_Repository::class );

		$enabled_integrations  = $integration_information_repo->get_integration_information();
		$defaults              = array_merge( $defaults, $enabled_integrations );
		$enabled_features_repo = YoastSEO()->classes->get( Enabled_Analysis_Features_Repository::class );

		$enabled_features = $enabled_features_repo->get_enabled_features()->parse_to_legacy_array();
		return array_merge( $defaults, $enabled_features );
	}
}
