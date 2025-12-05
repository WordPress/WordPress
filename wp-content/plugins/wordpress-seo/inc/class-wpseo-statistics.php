<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Internals
 */

/**
 * Class that generates interesting statistics about things.
 */
class WPSEO_Statistics {

	/**
	 * Returns the post count for a certain SEO rank.
	 *
	 * @todo Merge/DRY this with the logic virtually the same in WPSEO_Metabox::column_sort_orderby().
	 *
	 * @param WPSEO_Rank $rank The SEO rank to get the post count for.
	 *
	 * @return int
	 */
	public function get_post_count( $rank ) {
		if ( $rank->get_rank() === WPSEO_Rank::NO_FOCUS ) {
			$posts = [
				'meta_query' => [
					'relation' => 'OR',
					[
						'key'     => WPSEO_Meta::$meta_prefix . 'focuskw',
						'value'   => 'needs-a-value-anyway',
						'compare' => 'NOT EXISTS',
					],
				],
			];
		}
		elseif ( $rank->get_rank() === WPSEO_Rank::NO_INDEX ) {
			$posts = [
				'meta_key'   => WPSEO_Meta::$meta_prefix . 'meta-robots-noindex',
				'meta_value' => '1',
				'compare'    => '=',
			];
		}
		else {
			$posts = [
				'meta_key'     => WPSEO_Meta::$meta_prefix . 'linkdex',
				'meta_value'   => [ $rank->get_starting_score(), $rank->get_end_score() ],
				'meta_compare' => 'BETWEEN',
				'meta_type'    => 'NUMERIC',
			];
		}

		$posts['fields']      = 'ids';
		$posts['post_status'] = 'publish';

		if ( current_user_can( 'edit_others_posts' ) === false ) {
			$posts['author'] = get_current_user_id();
		}

		$posts = new WP_Query( $posts );

		return (int) $posts->found_posts;
	}
}
