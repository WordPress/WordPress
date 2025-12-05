<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Editors\Framework\Site;

use WP_Taxonomy;
use WP_Term;

/**
 * The Term_Site_Information class.
 */
class Term_Site_Information extends Base_Site_Information {

	/**
	 * The taxonomy.
	 *
	 * @var WP_Taxonomy|false
	 */
	private $taxonomy;

	/**
	 * The term.
	 *
	 * @var WP_Term|string|false
	 */
	private $term;

	/**
	 *  Sets the term for the information object and retrieves its taxonomy.
	 *
	 * @param WP_Term|string|false $term The term.
	 *
	 * @return void
	 */
	public function set_term( $term ) {
		$this->term     = $term;
		$this->taxonomy = \get_taxonomy( $term->taxonomy );
	}

	/**
	 * Returns term specific site information together with the generic site information.
	 *
	 * @return array<string, string|string[]>
	 */
	public function get_site_information(): array {
		$data = [
			'search_url'    => $this->search_url(),
			'post_edit_url' => $this->edit_url(),
			'base_url'      => $this->base_url_for_js(),
		];

		return \array_merge_recursive( $data, parent::get_site_information() );
	}

	/**
	 * Returns term specific site information together with the generic site information.
	 *
	 * @return array<string, array<string, string>>
	 */
	public function get_legacy_site_information(): array {
		$data = [
			'metabox' => [
				'search_url'    => $this->search_url(),
				'post_edit_url' => $this->edit_url(),
				'base_url'      => $this->base_url_for_js(),
			],
		];

		return \array_merge_recursive( $data, parent::get_legacy_site_information() );
	}

	/**
	 * Returns the url to search for keyword for the taxonomy.
	 *
	 * @return string
	 */
	private function search_url(): string {
		return \admin_url( 'edit-tags.php?taxonomy=' . $this->term->taxonomy . '&seo_kw_filter={keyword}' );
	}

	/**
	 * Returns the url to edit the taxonomy.
	 *
	 * @return string
	 */
	private function edit_url(): string {
		return \admin_url( 'term.php?action=edit&taxonomy=' . $this->term->taxonomy . '&tag_ID={id}' );
	}

	/**
	 * Returns a base URL for use in the JS, takes permalink structure into account.
	 *
	 * @return string
	 */
	private function base_url_for_js(): string {
		$base_url = \home_url( '/', null );
		if ( ! $this->options_helper->get( 'stripcategorybase', false ) ) {
			if ( $this->taxonomy->rewrite ) {
				$base_url = \trailingslashit( $base_url . $this->taxonomy->rewrite['slug'] );
			}
		}

		return $base_url;
	}
}
