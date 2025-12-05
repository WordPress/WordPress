<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Statistics
 */

/**
 * Class WPSEO_Statistics_Service.
 */
class WPSEO_Statistics_Service {

	/**
	 * Cache transient id.
	 *
	 * @var string
	 */
	public const CACHE_TRANSIENT_KEY = 'wpseo-statistics-totals';

	/**
	 * Class that generates interesting statistics about things.
	 *
	 * @var WPSEO_Statistics
	 */
	protected $statistics;

	/**
	 * Statistics labels.
	 *
	 * @var string[]
	 */
	protected $labels;

	/**
	 * WPSEO_Statistics_Service contructor.
	 *
	 * @param WPSEO_Statistics $statistics The statistics class to retrieve statistics from.
	 */
	public function __construct( WPSEO_Statistics $statistics ) {
		$this->statistics = $statistics;
	}

	/**
	 * Fetches statistics by REST request.
	 *
	 * @return WP_REST_Response The response object.
	 */
	public function get_statistics() {
		// Switch to the user locale with fallback to the site locale.
		switch_to_locale( get_user_locale() );

		$this->labels = $this->labels();
		$statistics   = $this->statistic_items();

		$data = [
			'header'     => $this->get_header_from_statistics( $statistics ),
			'seo_scores' => $statistics['scores'],
		];

		return new WP_REST_Response( $data );
	}

	/**
	 * Gets a header summarizing the given statistics results.
	 *
	 * @param array $statistics The statistics results.
	 *
	 * @return string The header summing up the statistics results.
	 */
	private function get_header_from_statistics( array $statistics ) {
		// Personal interpretation to allow release, should be looked at later.
		if ( $statistics['division'] === false ) {
			return __( 'You don\'t have any published posts, your SEO scores will appear here once you make your first post!', 'wordpress-seo' );
		}

		if ( $statistics['division']['good'] > 0.66 ) {
			return __( 'Hey, your SEO is doing pretty well! Check out the stats:', 'wordpress-seo' );
		}

		return __( 'Below are your published posts\' SEO scores. Now is as good a time as any to start improving some of your posts!', 'wordpress-seo' );
	}

	/**
	 * An array representing items to be added to the At a Glance dashboard widget.
	 *
	 * @return array The statistics for the current user.
	 */
	private function statistic_items() {
		$transient = $this->get_transient();
		$user_id   = get_current_user_id();

		if ( isset( $transient[ $user_id ] ) ) {
			return $transient[ $user_id ];
		}

		return $this->set_statistic_items_for_user( $transient, $user_id );
	}

	/**
	 * Gets the statistics transient value. Returns array if transient wasn't set.
	 *
	 * @return array|mixed Returns the transient or an empty array if the transient doesn't exist.
	 */
	private function get_transient() {
		$transient = get_transient( self::CACHE_TRANSIENT_KEY );

		if ( $transient === false ) {
			return [];
		}

		return $transient;
	}

	/**
	 * Set the statistics transient cache for a specific user.
	 *
	 * @param array $transient The current stored transient with the cached data.
	 * @param int   $user      The user's ID to assign the retrieved values to.
	 *
	 * @return array The statistics transient for the user.
	 */
	private function set_statistic_items_for_user( $transient, $user ) {
		$scores   = $this->get_seo_scores_with_post_count();
		$division = $this->get_seo_score_division( $scores );

		$transient[ $user ] = [
			// Use array_values because array_filter may return non-zero indexed arrays.
			'scores'   => array_values( array_filter( $scores, [ $this, 'filter_items' ] ) ),
			'division' => $division,
		];

		set_transient( self::CACHE_TRANSIENT_KEY, $transient, DAY_IN_SECONDS );

		return $transient[ $user ];
	}

	/**
	 * Gets the division of SEO scores.
	 *
	 * @param array $scores The SEO scores.
	 *
	 * @return array|bool The division of SEO scores, false if there are no posts.
	 */
	private function get_seo_score_division( array $scores ) {
		$total    = 0;
		$division = [];

		foreach ( $scores as $score ) {
			$total += $score['count'];
		}

		if ( $total === 0 ) {
			return false;
		}

		foreach ( $scores as $score ) {
			$division[ $score['seo_rank'] ] = ( $score['count'] / $total );
		}

		return $division;
	}

	/**
	 * Get all SEO ranks and data associated with them.
	 *
	 * @return array An array of SEO scores and associated data.
	 */
	private function get_seo_scores_with_post_count() {
		$ranks = WPSEO_Rank::get_all_ranks();

		return array_map( [ $this, 'map_rank_to_widget' ], $ranks );
	}

	/**
	 * Converts a rank to data usable in the dashboard widget.
	 *
	 * @param WPSEO_Rank $rank The rank to map.
	 *
	 * @return array The mapped rank.
	 */
	private function map_rank_to_widget( WPSEO_Rank $rank ) {
		return [
			'seo_rank' => $rank->get_rank(),
			'label'    => $this->get_label_for_rank( $rank ),
			'count'    => $this->statistics->get_post_count( $rank ),
			'link'     => $this->get_link_for_rank( $rank ),
		];
	}

	/**
	 * Returns a dashboard widget label to use for a certain rank.
	 *
	 * @param WPSEO_Rank $rank The rank to return a label for.
	 *
	 * @return string The label for the rank.
	 */
	private function get_label_for_rank( WPSEO_Rank $rank ) {
		return $this->labels[ $rank->get_rank() ];
	}

	/**
	 * Determines the labels for the various scoring ranks that are known within Yoast SEO.
	 *
	 * @return array Array containing the translatable labels.
	 */
	private function labels() {
		return [
			WPSEO_Rank::NO_FOCUS => sprintf(
				/* translators: %1$s expands to an opening strong tag, %2$s expands to a closing strong tag */
				__( 'Posts %1$swithout%2$s a focus keyphrase', 'wordpress-seo' ),
				'<strong>',
				'</strong>'
			),
			WPSEO_Rank::BAD      => sprintf(
				/* translators: %s expands to the score */
				__( 'Posts with the SEO score: %s', 'wordpress-seo' ),
				'<strong>' . __( 'Needs improvement', 'wordpress-seo' ) . '</strong>'
			),
			WPSEO_Rank::OK       => sprintf(
				/* translators: %s expands to the score */
				__( 'Posts with the SEO score: %s', 'wordpress-seo' ),
				'<strong>' . __( 'OK', 'wordpress-seo' ) . '</strong>'
			),
			WPSEO_Rank::GOOD     => sprintf(
				/* translators: %s expands to the score */
				__( 'Posts with the SEO score: %s', 'wordpress-seo' ),
				'<strong>' . __( 'Good', 'wordpress-seo' ) . '</strong>'
			),
			WPSEO_Rank::NO_INDEX => __( 'Posts that should not show up in search results', 'wordpress-seo' ),
		];
	}

	/**
	 * Filter items if they have a count of zero.
	 *
	 * @param array $item The item to potentially filter out.
	 *
	 * @return bool Whether or not the count is zero.
	 */
	private function filter_items( $item ) {
		return $item['count'] !== 0;
	}

	/**
	 * Returns a link for the overview of posts of a certain rank.
	 *
	 * @param WPSEO_Rank $rank The rank to return a link for.
	 *
	 * @return string The link that shows an overview of posts with that rank.
	 */
	private function get_link_for_rank( WPSEO_Rank $rank ) {
		if ( current_user_can( 'edit_others_posts' ) === false ) {
			return esc_url( admin_url( 'edit.php?post_status=publish&post_type=post&seo_filter=' . $rank->get_rank() . '&author=' . get_current_user_id() ) );
		}

		return esc_url( admin_url( 'edit.php?post_status=publish&post_type=post&seo_filter=' . $rank->get_rank() ) );
	}
}
