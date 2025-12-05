<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Formatter
 */

use Yoast\WP\SEO\Editors\Application\Seo\Term_Seo_Information_Repository;

/**
 * This class provides data for the term metabox by return its values for localization.
 */
class WPSEO_Term_Metabox_Formatter implements WPSEO_Metabox_Formatter_Interface {

	/**
	 * The term the metabox formatter is for.
	 *
	 * @var WP_Term|stdClass
	 */
	private $term;

	/**
	 * The term's taxonomy.
	 *
	 * @var stdClass
	 */
	private $taxonomy;

	/**
	 * Whether we must return social templates values.
	 *
	 * @var bool
	 */
	private $use_social_templates = false;

	/**
	 * Array with the WPSEO_Titles options.
	 *
	 * @var array
	 */
	protected $options;

	/**
	 * WPSEO_Taxonomy_Scraper constructor.
	 *
	 * @param stdClass         $taxonomy Taxonomy.
	 * @param WP_Term|stdClass $term     Term.
	 */
	public function __construct( $taxonomy, $term ) {
		$this->taxonomy = $taxonomy;
		$this->term     = $term;

		$this->use_social_templates = $this->use_social_templates();
	}

	/**
	 * Determines whether the social templates should be used.
	 *
	 * @return bool Whether the social templates should be used.
	 */
	public function use_social_templates() {
		return WPSEO_Options::get( 'opengraph', false ) === true;
	}

	/**
	 * Returns the translated values.
	 *
	 * @return array
	 */
	public function get_values() {
		$values = [];

		// Todo: a column needs to be added on the termpages to add a filter for the keyword, so this can be used in the focus keyphrase doubles.
		if ( is_object( $this->term ) && property_exists( $this->term, 'taxonomy' ) ) {
			$values = [
				'taxonomy'                    => $this->term->taxonomy,
				'semrushIntegrationActive'    => 0,
				'wincherIntegrationActive'    => 0,
				'isInsightsEnabled'           => $this->is_insights_enabled(),
			];

			$repo = YoastSEO()->classes->get( Term_Seo_Information_Repository::class );
			$repo->set_term( $this->term );
			$values = ( $repo->get_seo_data() + $values );
		}

		return $values;
	}

	/**
	 * Determines whether the insights feature is enabled for this taxonomy.
	 *
	 * @return bool
	 */
	protected function is_insights_enabled() {
		return WPSEO_Options::get( 'enable_metabox_insights', false );
	}
}
