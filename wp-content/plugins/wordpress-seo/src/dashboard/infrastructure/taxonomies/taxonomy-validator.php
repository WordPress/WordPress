<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Dashboard\Infrastructure\Taxonomies;

use WP_Taxonomy;

/**
 * Class that validates taxonomies.
 */
class Taxonomy_Validator {

	/**
	 * Returns whether the taxonomy in question is valid and associated with a given content type.
	 *
	 * @param WP_Taxonomy|false|null $taxonomy     The taxonomy to check.
	 * @param string                 $content_type The name of the content type to check.
	 *
	 * @return bool Whether the taxonomy in question is valid.
	 */
	public function is_valid_taxonomy( $taxonomy, string $content_type ): bool {
		return \is_a( $taxonomy, 'WP_Taxonomy' )
			&& $taxonomy->public
			&& $taxonomy->show_in_rest
			&& \in_array( $taxonomy->name, \get_object_taxonomies( $content_type ), true );
	}
}
