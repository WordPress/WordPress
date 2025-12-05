<?php

namespace Yoast\WP\SEO\Generators\Schema;

use Yoast\WP\SEO\Config\Schema_IDs;

/**
 * Returns schema Website data.
 */
class Website extends Abstract_Schema_Piece {

	/**
	 * Determines whether or not a piece should be added to the graph.
	 *
	 * @return bool
	 */
	public function is_needed() {
		return true;
	}

	/**
	 * Outputs code to allow recognition of the internal search engine.
	 *
	 * @return array Website data blob.
	 */
	public function generate() {
		$data = [
			'@type'       => 'WebSite',
			'@id'         => $this->context->site_url . Schema_IDs::WEBSITE_HASH,
			'url'         => $this->context->site_url,
			'name'        => $this->helpers->schema->html->smart_strip_tags( $this->context->site_name ),
			'description' => \get_bloginfo( 'description' ),
		];

		if ( $this->context->site_represents_reference ) {
			$data['publisher'] = $this->context->site_represents_reference;
		}

		$data = $this->add_alternate_name( $data );
		$data = $this->internal_search_section( $data );
		$data = $this->helpers->schema->language->add_piece_language( $data );

		return $data;
	}

	/**
	 * Returns an alternate name if one was specified in the Yoast SEO settings.
	 *
	 * @param array $data The website data array.
	 *
	 * @return array
	 */
	private function add_alternate_name( $data ) {
		if ( $this->context->alternate_site_name !== '' ) {
			$data['alternateName'] = $this->helpers->schema->html->smart_strip_tags( $this->context->alternate_site_name );
		}

		return $data;
	}

	/**
	 * Adds the internal search JSON LD code to the homepage if it's not disabled.
	 *
	 * @link https://developers.google.com/search/docs/data-types/sitelinks-searchbox
	 *
	 * @param array $data The website data array.
	 *
	 * @return array
	 */
	private function internal_search_section( $data ) {
		/**
		 * Filter: 'disable_wpseo_json_ld_search' - Allow disabling of the json+ld output.
		 *
		 * @param bool $display_search Whether or not to display json+ld search on the frontend.
		 */
		if ( \apply_filters( 'disable_wpseo_json_ld_search', false ) ) {
			return $data;
		}

		/**
		 * Filter: 'wpseo_json_ld_search_url' - Allows filtering of the search URL for Yoast SEO.
		 *
		 * @param string $search_url The search URL for this site with a `{search_term_string}` variable.
		 */
		$search_url = \apply_filters( 'wpseo_json_ld_search_url', $this->context->site_url . '?s={search_term_string}' );

		$data['potentialAction'][] = [
			'@type'       => 'SearchAction',
			'target'      => [
				'@type'       => 'EntryPoint',
				'urlTemplate' => $search_url,
			],
			'query-input' => [
				'@type'          => 'PropertyValueSpecification',
				'valueRequired'  => true,
				'valueName'      => 'search_term_string',
			],
		];

		return $data;
	}
}
