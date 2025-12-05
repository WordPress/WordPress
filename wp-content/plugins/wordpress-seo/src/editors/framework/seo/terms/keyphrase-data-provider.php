<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Editors\Framework\Seo\Terms;

use WPSEO_Taxonomy_Meta;
use Yoast\WP\SEO\Editors\Domain\Seo\Keyphrase;
use Yoast\WP\SEO\Editors\Domain\Seo\Seo_Plugin_Data_Interface;
use Yoast\WP\SEO\Editors\Framework\Seo\Keyphrase_Interface;

/**
 * Describes if the keyphrase SEO data.
 */
class Keyphrase_Data_Provider extends Abstract_Term_Seo_Data_Provider implements Keyphrase_Interface {

	/**
	 * Counting the number of given keyphrase used for other term than given term_id.
	 *
	 * @return array<string>
	 */
	public function get_focus_keyphrase_usage(): array {
		$focuskp = WPSEO_Taxonomy_Meta::get_term_meta( $this->term, $this->term->taxonomy, 'focuskw' );

		return WPSEO_Taxonomy_Meta::get_keyword_usage( $focuskp, $this->term->term_id, $this->term->taxonomy );
	}

	/**
	 * Method to return the keyphrase domain object with SEO data.
	 *
	 * @return Seo_Plugin_Data_Interface The specific seo data.
	 */
	public function get_data(): Seo_Plugin_Data_Interface {
		$keyphrase_usage = $this->get_focus_keyphrase_usage();
		return new Keyphrase( $keyphrase_usage, [] );
	}
}
