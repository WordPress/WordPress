<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

use Yoast\WP\SEO\Context\Meta_Tags_Context;
use Yoast\WP\SEO\Helpers\Score_Icon_Helper;
use Yoast\WP\SEO\Integrations\Admin\Admin_Columns_Cache_Integration;
use Yoast\WP\SEO\Surfaces\Values\Meta;

/**
 * Class WPSEO_Meta_Columns.
 */
class WPSEO_Meta_Columns {

	/**
	 * Holds the context objects for each indexable.
	 *
	 * @var Meta_Tags_Context[]
	 */
	protected $context = [];

	/**
	 * Holds the SEO analysis.
	 *
	 * @var WPSEO_Metabox_Analysis_SEO
	 */
	private $analysis_seo;

	/**
	 * Holds the readability analysis.
	 *
	 * @var WPSEO_Metabox_Analysis_Readability
	 */
	private $analysis_readability;

	/**
	 * Admin columns cache.
	 *
	 * @var Admin_Columns_Cache_Integration
	 */
	private $admin_columns_cache;

	/**
	 * Holds the Score_Icon_Helper.
	 *
	 * @var Score_Icon_Helper
	 */
	private $score_icon_helper;

	/**
	 * Holds the WPSEO_Admin_Asset_Manager instance.
	 *
	 * @var WPSEO_Admin_Asset_Manager
	 */
	private $admin_asset_manager;

	/**
	 * When page analysis is enabled, just initialize the hooks.
	 */
	public function __construct() {
		if ( apply_filters( 'wpseo_use_page_analysis', true ) === true ) {
			add_action( 'admin_init', [ $this, 'setup_hooks' ] );
		}

		$this->analysis_seo         = new WPSEO_Metabox_Analysis_SEO();
		$this->analysis_readability = new WPSEO_Metabox_Analysis_Readability();
		$this->admin_columns_cache  = YoastSEO()->classes->get( Admin_Columns_Cache_Integration::class );
		$this->score_icon_helper    = YoastSEO()->helpers->score_icon;
		$this->admin_asset_manager  = YoastSEO()->classes->get( WPSEO_Admin_Asset_Manager::class );
	}

	/**
	 * Sets up up the hooks.
	 *
	 * @return void
	 */
	public function setup_hooks() {
		$this->set_post_type_hooks();

		if ( $this->analysis_seo->is_enabled() ) {
			add_action( 'restrict_manage_posts', [ $this, 'posts_filter_dropdown' ] );
		}

		if ( $this->analysis_readability->is_enabled() ) {
			add_action( 'restrict_manage_posts', [ $this, 'posts_filter_dropdown_readability' ] );
		}

		add_filter( 'request', [ $this, 'column_sort_orderby' ] );
		add_filter( 'default_hidden_columns', [ $this, 'column_hidden' ], 10, 1 );
	}

	/**
	 * Adds the column headings for the SEO plugin for edit posts / pages overview.
	 *
	 * @param array $columns Already existing columns.
	 *
	 * @return array Array containing the column headings.
	 */
	public function column_heading( $columns ) {
		if ( $this->display_metabox() === false ) {
			return $columns;
		}

		$this->admin_asset_manager->enqueue_script( 'edit-page' );
		$this->admin_asset_manager->enqueue_style( 'edit-page' );

		$added_columns = [];

		if ( $this->analysis_seo->is_enabled() ) {
			$added_columns['wpseo-score'] = '<span class="yoast-column-seo-score yoast-column-header-has-tooltip" data-tooltip-text="'
											. esc_attr__( 'SEO score', 'wordpress-seo' )
											. '"><span class="screen-reader-text">'
											. __( 'SEO score', 'wordpress-seo' )
											. '</span></span>';
		}

		if ( $this->analysis_readability->is_enabled() ) {
			$added_columns['wpseo-score-readability'] = '<span class="yoast-column-readability yoast-column-header-has-tooltip" data-tooltip-text="'
														. esc_attr__( 'Readability score', 'wordpress-seo' )
														. '"><span class="screen-reader-text">'
														. __( 'Readability score', 'wordpress-seo' )
														. '</span></span>';
		}

		$added_columns['wpseo-title']    = __( 'SEO Title', 'wordpress-seo' );
		$added_columns['wpseo-metadesc'] = __( 'Meta Desc.', 'wordpress-seo' );

		if ( $this->analysis_seo->is_enabled() ) {
			$added_columns['wpseo-focuskw'] = __( 'Keyphrase', 'wordpress-seo' );
		}

		return array_merge( $columns, $added_columns );
	}

	/**
	 * Displays the column content for the given column.
	 *
	 * @param string $column_name Column to display the content for.
	 * @param int    $post_id     Post to display the column content for.
	 *
	 * @return void
	 */
	public function column_content( $column_name, $post_id ) {
		if ( $this->display_metabox() === false ) {
			return;
		}

		switch ( $column_name ) {
			case 'wpseo-score':
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Correctly escaped in render_score_indicator() method.
				echo $this->parse_column_score( $post_id );

				return;

			case 'wpseo-score-readability':
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Correctly escaped in render_score_indicator() method.
				echo $this->parse_column_score_readability( $post_id );

				return;

			case 'wpseo-title':
				$meta = $this->get_meta( $post_id );
				if ( $meta ) {
					echo esc_html( $meta->title );
				}

				return;

			case 'wpseo-metadesc':
				$metadesc_val = '';
				$meta         = $this->get_meta( $post_id );
				if ( $meta ) {
					$metadesc_val = $meta->meta_description;
				}
				if ( $metadesc_val === '' ) {
					echo '<span aria-hidden="true">&#8212;</span><span class="screen-reader-text">',
					/* translators: Hidden accessibility text. */
					esc_html__( 'Meta description not set.', 'wordpress-seo' ),
					'</span>';

					return;
				}

				echo esc_html( $metadesc_val );

				return;

			case 'wpseo-focuskw':
				$focuskw_val = WPSEO_Meta::get_value( 'focuskw', $post_id );

				if ( $focuskw_val === '' ) {
					echo '<span aria-hidden="true">&#8212;</span><span class="screen-reader-text">',
					/* translators: Hidden accessibility text. */
					esc_html__( 'Focus keyphrase not set.', 'wordpress-seo' ),
					'</span>';

					return;
				}

				echo esc_html( $focuskw_val );

				return;
		}
	}

	/**
	 * Indicates which of the SEO columns are sortable.
	 *
	 * @param array $columns Appended with their orderby variable.
	 *
	 * @return array Array containing the sortable columns.
	 */
	public function column_sort( $columns ) {
		if ( $this->display_metabox() === false ) {
			return $columns;
		}

		$columns['wpseo-metadesc'] = 'wpseo-metadesc';

		if ( $this->analysis_seo->is_enabled() ) {
			$columns['wpseo-focuskw'] = 'wpseo-focuskw';
			$columns['wpseo-score']   = 'wpseo-score';
		}

		if ( $this->analysis_readability->is_enabled() ) {
			$columns['wpseo-score-readability'] = 'wpseo-score-readability';
		}

		return $columns;
	}

	/**
	 * Hides the SEO title, meta description and focus keyword columns if the user hasn't chosen which columns to hide.
	 *
	 * @param array $hidden The hidden columns.
	 *
	 * @return array Array containing the columns to hide.
	 */
	public function column_hidden( $hidden ) {
		if ( ! is_array( $hidden ) ) {
			$hidden = [];
		}

		array_push( $hidden, 'wpseo-title', 'wpseo-metadesc' );

		if ( $this->analysis_seo->is_enabled() ) {
			$hidden[] = 'wpseo-focuskw';
		}

		return $hidden;
	}

	/**
	 * Adds a dropdown that allows filtering on the posts SEO Quality.
	 *
	 * @return void
	 */
	public function posts_filter_dropdown() {
		if ( ! $this->can_display_filter() ) {
			return;
		}

		$ranks = WPSEO_Rank::get_all_ranks();

		/* translators: Hidden accessibility text. */
		echo '<label class="screen-reader-text" for="wpseo-filter">' . esc_html__( 'Filter by SEO Score', 'wordpress-seo' ) . '</label>';
		echo '<select name="seo_filter" id="wpseo-filter">';

		// phpcs:ignore WordPress.Security.EscapeOutput -- Output is correctly escaped in the generate_option() method.
		echo $this->generate_option( '', __( 'All SEO Scores', 'wordpress-seo' ) );

		foreach ( $ranks as $rank ) {
			$selected = selected( $this->get_current_seo_filter(), $rank->get_rank(), false );

			// phpcs:ignore WordPress.Security.EscapeOutput -- Output is correctly escaped in the generate_option() method.
			echo $this->generate_option( $rank->get_rank(), $rank->get_drop_down_label(), $selected );
		}

		echo '</select>';
	}

	/**
	 * Adds a dropdown that allows filtering on the posts Readability Quality.
	 *
	 * @return void
	 */
	public function posts_filter_dropdown_readability() {
		if ( ! $this->can_display_filter() ) {
			return;
		}

		$ranks = WPSEO_Rank::get_all_readability_ranks();

		/* translators: Hidden accessibility text. */
		echo '<label class="screen-reader-text" for="wpseo-readability-filter">' . esc_html__( 'Filter by Readability Score', 'wordpress-seo' ) . '</label>';
		echo '<select name="readability_filter" id="wpseo-readability-filter">';

		// phpcs:ignore WordPress.Security.EscapeOutput -- Output is correctly escaped in the generate_option() method.
		echo $this->generate_option( '', __( 'All Readability Scores', 'wordpress-seo' ) );

		foreach ( $ranks as $rank ) {
			$selected = selected( $this->get_current_readability_filter(), $rank->get_rank(), false );

			// phpcs:ignore WordPress.Security.EscapeOutput -- Output is correctly escaped in the generate_option() method.
			echo $this->generate_option( $rank->get_rank(), $rank->get_drop_down_readability_labels(), $selected );
		}

		echo '</select>';
	}

	/**
	 * Generates an <option> element.
	 *
	 * @param string $value    The option's value.
	 * @param string $label    The option's label.
	 * @param string $selected HTML selected attribute for an option.
	 *
	 * @return string The generated <option> element.
	 */
	protected function generate_option( $value, $label, $selected = '' ) {
		return '<option ' . $selected . ' value="' . esc_attr( $value ) . '">' . esc_html( $label ) . '</option>';
	}

	/**
	 * Returns the meta object for a given post ID.
	 *
	 * @param int $post_id The post ID.
	 *
	 * @return Meta The meta object.
	 */
	protected function get_meta( $post_id ) {
		$indexable = $this->admin_columns_cache->get_indexable( $post_id );

		return YoastSEO()->meta->for_indexable( $indexable, 'Post_Type' );
	}

	/**
	 * Determines the SEO score filter to be later used in the meta query, based on the passed SEO filter.
	 *
	 * @param string $seo_filter The SEO filter to use to determine what further filter to apply.
	 *
	 * @return array The SEO score filter.
	 */
	protected function determine_seo_filters( $seo_filter ) {
		if ( $seo_filter === WPSEO_Rank::NO_FOCUS ) {
			return $this->create_no_focus_keyword_filter();
		}

		if ( $seo_filter === WPSEO_Rank::NO_INDEX ) {
			return $this->create_no_index_filter();
		}

		$rank = new WPSEO_Rank( $seo_filter );

		return $this->create_seo_score_filter( $rank->get_starting_score(), $rank->get_end_score() );
	}

	/**
	 * Determines the Readability score filter to the meta query, based on the passed Readability filter.
	 *
	 * @param string $readability_filter The Readability filter to use to determine what further filter to apply.
	 *
	 * @return array The Readability score filter.
	 */
	protected function determine_readability_filters( $readability_filter ) {
		if ( $readability_filter === WPSEO_Rank::NO_FOCUS ) {
			return $this->create_no_readability_scores_filter();
		}
		if ( $readability_filter === WPSEO_Rank::BAD ) {
			return $this->create_bad_readability_scores_filter();
		}
		$rank = new WPSEO_Rank( $readability_filter );

		return $this->create_readability_score_filter( $rank->get_starting_score(), $rank->get_end_score() );
	}

	/**
	 * Creates a keyword filter for the meta query, based on the passed Keyword filter.
	 *
	 * @param string $keyword_filter The keyword filter to use.
	 *
	 * @return array The keyword filter.
	 */
	protected function get_keyword_filter( $keyword_filter ) {
		return [
			'post_type' => get_query_var( 'post_type', 'post' ),
			'key'       => WPSEO_Meta::$meta_prefix . 'focuskw',
			'value'     => sanitize_text_field( $keyword_filter ),
		];
	}

	/**
	 * Determines whether the passed filter is considered to be valid.
	 *
	 * @param mixed $filter The filter to check against.
	 *
	 * @return bool Whether the filter is considered valid.
	 */
	protected function is_valid_filter( $filter ) {
		return ! empty( $filter ) && is_string( $filter );
	}

	/**
	 * Collects the filters and merges them into a single array.
	 *
	 * @return array Array containing all the applicable filters.
	 */
	protected function collect_filters() {
		$active_filters = [];

		$seo_filter             = $this->get_current_seo_filter();
		$readability_filter     = $this->get_current_readability_filter();
		$current_keyword_filter = $this->get_current_keyword_filter();

		if ( $this->is_valid_filter( $seo_filter ) ) {
			$active_filters = array_merge(
				$active_filters,
				$this->determine_seo_filters( $seo_filter )
			);
		}

		if ( $this->is_valid_filter( $readability_filter ) ) {
			$active_filters = array_merge(
				$active_filters,
				$this->determine_readability_filters( $readability_filter )
			);
		}

		if ( $this->is_valid_filter( $current_keyword_filter ) ) {
			/**
			 * Adapt the meta query used to filter the post overview on keyphrase.
			 *
			 * @internal
			 *
			 * @param array $keyphrase      The keyphrase used in the filter.
			 * @param array $keyword_filter The current keyword filter.
			 */
			$keyphrase_filter = apply_filters(
				'wpseo_change_keyphrase_filter_in_request',
				$this->get_keyword_filter( $current_keyword_filter ),
				$current_keyword_filter
			);

			if ( is_array( $keyphrase_filter ) ) {
				$active_filters = array_merge(
					$active_filters,
					[ $keyphrase_filter ]
				);
			}
		}

		/**
		 * Adapt the active applicable filters on the posts overview.
		 *
		 * @internal
		 *
		 * @param array $active_filters The current applicable filters.
		 */
		return apply_filters( 'wpseo_change_applicable_filters', $active_filters );
	}

	/**
	 * Modify the query based on the filters that are being passed.
	 *
	 * @param array $vars Query variables that need to be modified based on the filters.
	 *
	 * @return array Array containing the meta query to use for filtering the posts overview.
	 */
	public function column_sort_orderby( $vars ) {
		$collected_filters = $this->collect_filters();

		$order_by_column = $vars['orderby'];
		if ( isset( $order_by_column ) ) {
			// Based on the selected column, create a meta query.
			$order_by = $this->filter_order_by( $order_by_column );

			/**
			 * Adapt the order by part of the query on the posts overview.
			 *
			 * @internal
			 *
			 * @param array  $order_by        The current order by.
			 * @param string $order_by_column The current order by column.
			 */
			$order_by = apply_filters( 'wpseo_change_order_by', $order_by, $order_by_column );

			$vars = array_merge( $vars, $order_by );
		}

		return $this->build_filter_query( $vars, $collected_filters );
	}

	/**
	 * Retrieves the meta robots query values to be used within the meta query.
	 *
	 * @return array Array containing the query parameters regarding meta robots.
	 */
	protected function get_meta_robots_query_values() {
		return [
			'relation' => 'OR',
			[
				'key'     => WPSEO_Meta::$meta_prefix . 'meta-robots-noindex',
				'compare' => 'NOT EXISTS',
			],
			[
				'key'     => WPSEO_Meta::$meta_prefix . 'meta-robots-noindex',
				'value'   => '1',
				'compare' => '!=',
			],
		];
	}

	/**
	 * Determines the score filters to be used. If more than one is passed, it created an AND statement for the query.
	 *
	 * @param array $score_filters Array containing the score filters.
	 *
	 * @return array Array containing the score filters that need to be applied to the meta query.
	 */
	protected function determine_score_filters( $score_filters ) {
		if ( count( $score_filters ) > 1 ) {
			return array_merge( [ 'relation' => 'AND' ], $score_filters );
		}

		return $score_filters;
	}

	/**
	 * Retrieves the post type from the $_GET variable.
	 *
	 * @return string|null The sanitized current post type or null when the variable is not set in $_GET.
	 */
	public function get_current_post_type() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( isset( $_GET['post_type'] ) && is_string( $_GET['post_type'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
			return sanitize_text_field( wp_unslash( $_GET['post_type'] ) );
		}
		return null;
	}

	/**
	 * Retrieves the SEO filter from the $_GET variable.
	 *
	 * @return string|null The sanitized seo filter or null when the variable is not set in $_GET.
	 */
	public function get_current_seo_filter() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( isset( $_GET['seo_filter'] ) && is_string( $_GET['seo_filter'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
			return sanitize_text_field( wp_unslash( $_GET['seo_filter'] ) );
		}
		return null;
	}

	/**
	 * Retrieves the Readability filter from the $_GET variable.
	 *
	 * @return string|null The sanitized readability filter or null when the variable is not set in $_GET.
	 */
	public function get_current_readability_filter() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( isset( $_GET['readability_filter'] ) && is_string( $_GET['readability_filter'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
			return sanitize_text_field( wp_unslash( $_GET['readability_filter'] ) );
		}
		return null;
	}

	/**
	 * Retrieves the keyword filter from the $_GET variable.
	 *
	 * @return string|null The sanitized seo keyword filter or null when the variable is not set in $_GET.
	 */
	public function get_current_keyword_filter() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( isset( $_GET['seo_kw_filter'] ) && is_string( $_GET['seo_kw_filter'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
			return sanitize_text_field( wp_unslash( $_GET['seo_kw_filter'] ) );
		}
		return null;
	}

	/**
	 * Uses the vars to create a complete filter query that can later be executed to filter out posts.
	 *
	 * @param array $vars    Array containing the variables that will be used in the meta query.
	 * @param array $filters Array containing the filters that we need to apply in the meta query.
	 *
	 * @return array Array containing the complete filter query.
	 */
	protected function build_filter_query( $vars, $filters ) {
		// If no filters were applied, just return everything.
		if ( count( $filters ) === 0 ) {
			return $vars;
		}

		$result               = [ 'meta_query' => [] ];
		$result['meta_query'] = array_merge( $result['meta_query'], [ $this->determine_score_filters( $filters ) ] );

		$current_seo_filter = $this->get_current_seo_filter();

		// This only applies for the SEO score filter because it can because the SEO score can be altered by the no-index option.
		if ( $this->is_valid_filter( $current_seo_filter ) && ! in_array( $current_seo_filter, [ WPSEO_Rank::NO_INDEX ], true ) ) {
			$result['meta_query'] = array_merge( $result['meta_query'], [ $this->get_meta_robots_query_values() ] );
		}

		return array_merge( $vars, $result );
	}

	/**
	 * Creates a Readability score filter.
	 *
	 * @param number $low  The lower boundary of the score.
	 * @param number $high The higher boundary of the score.
	 *
	 * @return array<array<string>> The Readability Score filter.
	 */
	protected function create_readability_score_filter( $low, $high ) {
		return [
			[
				'key'     => WPSEO_Meta::$meta_prefix . 'content_score',
				'value'   => [ $low, $high ],
				'type'    => 'numeric',
				'compare' => 'BETWEEN',
			],
		];
	}

	/**
	 * Creates an SEO score filter.
	 *
	 * @param number $low  The lower boundary of the score.
	 * @param number $high The higher boundary of the score.
	 *
	 * @return array<array<string>> The SEO score filter.
	 */
	protected function create_seo_score_filter( $low, $high ) {
		return [
			[
				'key'     => WPSEO_Meta::$meta_prefix . 'linkdex',
				'value'   => [ $low, $high ],
				'type'    => 'numeric',
				'compare' => 'BETWEEN',
			],
		];
	}

	/**
	 * Creates a filter to retrieve posts that were set to no-index.
	 *
	 * @return array<array<string>> Array containin the no-index filter.
	 */
	protected function create_no_index_filter() {
		return [
			[
				'key'     => WPSEO_Meta::$meta_prefix . 'meta-robots-noindex',
				'value'   => '1',
				'compare' => '=',
			],
		];
	}

	/**
	 * Creates a filter to retrieve posts that have no keyword set.
	 *
	 * @return array<array<string>> Array containing the no focus keyword filter.
	 */
	protected function create_no_focus_keyword_filter() {
		return [
			[
				'key'     => WPSEO_Meta::$meta_prefix . 'linkdex',
				'value'   => 'needs-a-value-anyway',
				'compare' => 'NOT EXISTS',
			],
		];
	}

	/**
	 * Creates a filter to retrieve posts that have not been analyzed for readability yet.
	 *
	 * @return array<array<string>> Array containing the no readability filter.
	 */
	protected function create_no_readability_scores_filter() {
		// We check the existence of the Estimated Reading Time, because readability scores of posts that haven't been manually saved while Yoast SEO is active, don't exist, which is also the case for posts with not enough content.
		// Meanwhile, the ERT is a solid indicator of whether a post has ever been saved (aka, analyzed), so we're using that.
		$rank = new WPSEO_Rank( WPSEO_Rank::BAD );
		return [
			[
				'key'     => WPSEO_Meta::$meta_prefix . 'estimated-reading-time-minutes',
				'value'   => 'needs-a-value-anyway',
				'compare' => 'NOT EXISTS',
			],
			[
				'relation' => 'OR',
				[
					'key'     => WPSEO_Meta::$meta_prefix . 'content_score',
					'value'   => $rank->get_starting_score(),
					'type'    => 'numeric',
					'compare' => '<',
				],
				[
					'key'     => WPSEO_Meta::$meta_prefix . 'content_score',
					'value'   => 'needs-a-value-anyway',
					'compare' => 'NOT EXISTS',
				],
			],
		];
	}

	/**
	 * Creates a filter to retrieve posts that have bad readability scores, including those that have not enough content to have one.
	 *
	 * @return array<array<string>> Array containing the bad readability filter.
	 */
	protected function create_bad_readability_scores_filter() {
		$rank = new WPSEO_Rank( WPSEO_Rank::BAD );
		return [
			'relation' => 'OR',
			[
				'key'     => WPSEO_Meta::$meta_prefix . 'content_score',
				'value'   => [ $rank->get_starting_score(), $rank->get_end_score() ],
				'type'    => 'numeric',
				'compare' => 'BETWEEN',
			],
			[
				[
					'key'     => WPSEO_Meta::$meta_prefix . 'content_score',
					'value'   => 'needs-a-value-anyway',
					'compare' => 'NOT EXISTS',
				],
				[
					'key'     => WPSEO_Meta::$meta_prefix . 'estimated-reading-time-minutes',
					'compare' => 'EXISTS',
				],
			],
		];
	}

	/**
	 * Determines whether a particular post_id is of an indexable post type.
	 *
	 * @param string $post_id The post ID to check.
	 *
	 * @return bool Whether or not it is indexable.
	 */
	protected function is_indexable( $post_id ) {
		if ( ! empty( $post_id ) && ! $this->uses_default_indexing( $post_id ) ) {
			return WPSEO_Meta::get_value( 'meta-robots-noindex', $post_id ) === '2';
		}

		$post = get_post( $post_id );

		if ( is_object( $post ) ) {
			// If the option is false, this means we want to index it.
			return WPSEO_Options::get( 'noindex-' . $post->post_type, false ) === false;
		}

		return true;
	}

	/**
	 * Determines whether the given post ID uses the default indexing settings.
	 *
	 * @param int $post_id The post ID to check.
	 *
	 * @return bool Whether or not the default indexing is being used for the post.
	 */
	protected function uses_default_indexing( $post_id ) {
		return WPSEO_Meta::get_value( 'meta-robots-noindex', $post_id ) === '0';
	}

	/**
	 * Returns filters when $order_by is matched in the if-statement.
	 *
	 * @param string $order_by The ID of the column by which to order the posts.
	 *
	 * @return array<string> Array containing the order filters.
	 */
	private function filter_order_by( $order_by ) {
		switch ( $order_by ) {
			case 'wpseo-metadesc':
				return [
					// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Reason: Only used when user requests sorting.
					'meta_key' => WPSEO_Meta::$meta_prefix . 'metadesc',
					'orderby'  => 'meta_value',
				];

			case 'wpseo-focuskw':
				return [
					// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Reason: Only used when user requests sorting.
					'meta_key' => WPSEO_Meta::$meta_prefix . 'focuskw',
					'orderby'  => 'meta_value',
				];

			case 'wpseo-score':
				return [
					// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Reason: Only used when user requests sorting.
					'meta_key' => WPSEO_Meta::$meta_prefix . 'linkdex',
					'orderby'  => 'meta_value_num',
				];

			case 'wpseo-score-readability':
				return [
					// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Reason: Only used when user requests sorting.
					'meta_key' => WPSEO_Meta::$meta_prefix . 'content_score',
					'orderby'  => 'meta_value_num',
				];
		}

		return [];
	}

	/**
	 * Parses the score column.
	 *
	 * @param int $post_id The ID of the post for which to show the score.
	 *
	 * @return string The HTML for the SEO score indicator.
	 */
	private function parse_column_score( $post_id ) {
		$meta = $this->get_meta( $post_id );

		if ( $meta ) {
			return $this->score_icon_helper->for_seo( $meta->indexable, '', __( 'Post is set to noindex.', 'wordpress-seo' ) );
		}
	}

	/**
	 * Parsing the readability score column.
	 *
	 * @param int $post_id The ID of the post for which to show the readability score.
	 *
	 * @return string The HTML for the readability score indicator.
	 */
	private function parse_column_score_readability( $post_id ) {
		$meta = $this->get_meta( $post_id );
		if ( $meta ) {
			return $this->score_icon_helper->for_readability( $meta->indexable->readability_score );
		}
	}

	/**
	 * Sets up the hooks for the post_types.
	 *
	 * @return void
	 */
	private function set_post_type_hooks() {
		$post_types = WPSEO_Post_Type::get_accessible_post_types();

		if ( ! is_array( $post_types ) || $post_types === [] ) {
			return;
		}

		foreach ( $post_types as $post_type ) {
			if ( $this->display_metabox( $post_type ) === false ) {
				continue;
			}

			add_filter( 'manage_' . $post_type . '_posts_columns', [ $this, 'column_heading' ], 10, 1 );
			add_action( 'manage_' . $post_type . '_posts_custom_column', [ $this, 'column_content' ], 10, 2 );
			add_action( 'manage_edit-' . $post_type . '_sortable_columns', [ $this, 'column_sort' ], 10, 2 );
		}

		unset( $post_type );
	}

	/**
	 * Wraps the WPSEO_Metabox check to determine whether the metabox should be displayed either by
	 * choice of the admin or because the post type is not a public post type.
	 *
	 * @since 7.0
	 *
	 * @param string|null $post_type Optional. The post type to test, defaults to the current post post_type.
	 *
	 * @return bool Whether or not the meta box (and associated columns etc) should be hidden.
	 */
	private function display_metabox( $post_type = null ) {
		$current_post_type = $this->get_current_post_type();

		if ( ! isset( $post_type ) && ! empty( $current_post_type ) ) {
			$post_type = $current_post_type;
		}

		return WPSEO_Utils::is_metabox_active( $post_type, 'post_type' );
	}

	/**
	 * Determines whether or not filter dropdowns should be displayed.
	 *
	 * @return bool Whether or the current page can display the filter drop downs.
	 */
	public function can_display_filter() {
		if ( $GLOBALS['pagenow'] === 'upload.php' ) {
			return false;
		}

		if ( $this->display_metabox() === false ) {
			return false;
		}

		$screen = get_current_screen();
		if ( $screen === null ) {
			return false;
		}

		return WPSEO_Post_Type::is_post_type_accessible( $screen->post_type );
	}
}
