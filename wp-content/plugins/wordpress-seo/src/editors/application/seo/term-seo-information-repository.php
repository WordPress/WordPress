<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Editors\Application\Seo;

use WP_Term;
use Yoast\WP\SEO\Editors\Framework\Seo\Terms\Abstract_Term_Seo_Data_Provider;

/**
 * The repository to get term related SEO data.
 *
 * @makePublic
 */
class Term_Seo_Information_Repository {

	/**
	 * The term.
	 *
	 * @var WP_Term
	 */
	private $term;

	/**
	 * The data providers.
	 *
	 * @var Abstract_Term_Seo_Data_Provider
	 */
	private $seo_data_providers;

	/**
	 * The constructor.
	 *
	 * @param Abstract_Term_Seo_Data_Provider ...$seo_data_providers The providers.
	 */
	public function __construct( Abstract_Term_Seo_Data_Provider ...$seo_data_providers ) {
		$this->seo_data_providers = $seo_data_providers;
	}

	/**
	 * The term.
	 *
	 * @param WP_Term $term The term.
	 *
	 * @return void
	 */
	public function set_term( WP_Term $term ): void {
		$this->term = $term;
	}

	/**
	 * Method to return the compiled SEO data.
	 *
	 * @return array<string> The specific seo data.
	 */
	public function get_seo_data(): array {
		$array = [];
		foreach ( $this->seo_data_providers as $data_provider ) {
			$data_provider->set_term( $this->term );
			$array = \array_merge( $array, $data_provider->get_data()->to_legacy_array() );
		}

		return $array;
	}
}
