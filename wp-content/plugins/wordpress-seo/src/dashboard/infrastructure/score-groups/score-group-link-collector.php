<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Dashboard\Infrastructure\Score_Groups;

use Yoast\WP\SEO\Dashboard\Domain\Content_Types\Content_Type;
use Yoast\WP\SEO\Dashboard\Domain\Score_Groups\Score_Groups_Interface;
use Yoast\WP\SEO\Dashboard\Domain\Taxonomies\Taxonomy;

/**
 * Getting links for score groups.
 */
class Score_Group_Link_Collector {

	/**
	 * Builds the view link of the score group.
	 *
	 * @param Score_Groups_Interface $score_group  The score group.
	 * @param Content_Type           $content_type The content type.
	 * @param Taxonomy|null          $taxonomy     The taxonomy of the term we might be filtering.
	 * @param int|null               $term_id      The ID of the term we might be filtering.
	 *
	 * @return string|null The view link of the score.
	 */
	public function get_view_link( Score_Groups_Interface $score_group, Content_Type $content_type, ?Taxonomy $taxonomy, ?int $term_id ): ?string {
		$posts_page = \admin_url( 'edit.php' );
		$args       = [
			'post_status'                  => 'publish',
			'post_type'                    => $content_type->get_name(),
			$score_group->get_filter_key() => $score_group->get_filter_value(),
		];

		if ( $taxonomy === null || $term_id === null ) {
			return \add_query_arg( $args, $posts_page );
		}

		$taxonomy_object = \get_taxonomy( $taxonomy->get_name() );
		$query_var       = $taxonomy_object->query_var;

		if ( ! $query_var ) {
			return null;
		}

		$term               = \get_term( $term_id );
		$args[ $query_var ] = $term->slug;

		return \add_query_arg( $args, $posts_page );
	}
}
