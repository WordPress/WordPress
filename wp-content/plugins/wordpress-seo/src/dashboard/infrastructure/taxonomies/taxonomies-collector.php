<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Dashboard\Infrastructure\Taxonomies;

use WP_Taxonomy;
use Yoast\WP\SEO\Dashboard\Domain\Taxonomies\Taxonomy;

/**
 * Class that collects taxonomies and relevant information.
 */
class Taxonomies_Collector {

	/**
	 * The taxonomy validator.
	 *
	 * @var Taxonomy_Validator
	 */
	private $taxonomy_validator;

	/**
	 * The constructor.
	 *
	 * @param Taxonomy_Validator $taxonomy_validator The taxonomy validator.
	 */
	public function __construct( Taxonomy_Validator $taxonomy_validator ) {
		$this->taxonomy_validator = $taxonomy_validator;
	}

	/**
	 * Returns a custom pair of taxonomy/content type, that's been given by users via hooks.
	 *
	 * @param string $content_type The content type we're hooking for.
	 *
	 * @return Taxonomy|null The hooked filtering taxonomy.
	 */
	public function get_custom_filtering_taxonomy( string $content_type ) {
		/**
		 * Filter: 'wpseo_{$content_type}_filtering_taxonomy' - Allows overriding which taxonomy filters the content type.
		 *
		 * @internal
		 *
		 * @param string $filtering_taxonomy The taxonomy that filters the content type.
		 */
		$filtering_taxonomy = \apply_filters( "wpseo_{$content_type}_filtering_taxonomy", '' );
		if ( $filtering_taxonomy !== '' ) {
			$taxonomy = $this->get_taxonomy( $filtering_taxonomy, $content_type );

			if ( $taxonomy ) {
				return $taxonomy;
			}

			\_doing_it_wrong(
				'Filter: \'wpseo_{$content_type}_filtering_taxonomy\'',
				'The `wpseo_{$content_type}_filtering_taxonomy` filter should return a public taxonomy, available in REST API, that is associated with that content type.',
				'YoastSEO v24.1'
			);
		}

		return null;
	}

	/**
	 * Returns the fallback, WP-native category taxonomy, if it's associated with the specific content type.
	 *
	 * @param string $content_type The content type.
	 *
	 * @return Taxonomy|null The taxonomy object for the category taxonomy.
	 */
	public function get_fallback_taxonomy( string $content_type ): ?Taxonomy {
		return $this->get_taxonomy( 'category', $content_type );
	}

	/**
	 * Returns the taxonomy object that filters a specific content type.
	 *
	 * @param string $taxonomy_name The name of the taxonomy we're going to build the object for.
	 * @param string $content_type  The content type that the taxonomy object is filtering.
	 *
	 * @return Taxonomy|null The taxonomy object.
	 */
	public function get_taxonomy( string $taxonomy_name, string $content_type ): ?Taxonomy {
		$taxonomy = \get_taxonomy( $taxonomy_name );

		if ( $this->taxonomy_validator->is_valid_taxonomy( $taxonomy, $content_type ) ) {
			return new Taxonomy( $taxonomy->name, $taxonomy->label, $this->get_taxonomy_rest_url( $taxonomy ) );
		}

		return null;
	}

	/**
	 * Builds the REST API URL for the taxonomy.
	 *
	 * @param WP_Taxonomy $taxonomy The taxonomy we want to build the REST API URL for.
	 *
	 * @return string The REST API URL for the taxonomy.
	 */
	protected function get_taxonomy_rest_url( WP_Taxonomy $taxonomy ): string {
		$rest_base = ( $taxonomy->rest_base ) ? $taxonomy->rest_base : $taxonomy->name;

		$rest_namespace = ( $taxonomy->rest_namespace ) ? $taxonomy->rest_namespace : 'wp/v2';

		return \rest_url( "{$rest_namespace}/{$rest_base}" );
	}
}
