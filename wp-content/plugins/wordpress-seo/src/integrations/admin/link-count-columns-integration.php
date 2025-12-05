<?php

namespace Yoast\WP\SEO\Integrations\Admin;

use WP_Query;
use wpdb;
use Yoast\WP\Lib\Model;
use Yoast\WP\SEO\Actions\Indexing\Post_Link_Indexing_Action;
use Yoast\WP\SEO\Conditionals\Admin\Posts_Overview_Or_Ajax_Conditional;
use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Conditionals\Should_Index_Links_Conditional;
use Yoast\WP\SEO\Helpers\Post_Type_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Link_Count_Columns_Integration class.
 */
class Link_Count_Columns_Integration implements Integration_Interface {

	/**
	 * Partial column name.
	 *
	 * @var string
	 */
	public const COLUMN_LINKED = 'linked';

	/**
	 * Partial column name.
	 *
	 * @var string
	 */
	public const COLUMN_LINKS = 'links';

	/**
	 * The post type helper.
	 *
	 * @var Post_Type_Helper
	 */
	protected $post_type_helper;

	/**
	 * The database object.
	 *
	 * @var wpdb
	 */
	protected $wpdb;

	/**
	 * The post link builder.
	 *
	 * @var Post_Link_Indexing_Action
	 */
	protected $post_link_indexing_action;

	/**
	 * The admin columns cache.
	 *
	 * @var Admin_Columns_Cache_Integration
	 */
	protected $admin_columns_cache;

	/**
	 * {@inheritDoc}
	 */
	public static function get_conditionals() {
		return [
			Admin_Conditional::class,
			Posts_Overview_Or_Ajax_Conditional::class,
			Should_Index_Links_Conditional::class,
		];
	}

	/**
	 * Link_Count_Columns_Integration constructor
	 *
	 * @param Post_Type_Helper                $post_type_helper          The post type helper.
	 * @param wpdb                            $wpdb                      The wpdb object.
	 * @param Post_Link_Indexing_Action       $post_link_indexing_action The post link indexing action.
	 * @param Admin_Columns_Cache_Integration $admin_columns_cache       The admin columns cache.
	 */
	public function __construct(
		Post_Type_Helper $post_type_helper,
		wpdb $wpdb,
		Post_Link_Indexing_Action $post_link_indexing_action,
		Admin_Columns_Cache_Integration $admin_columns_cache
	) {
		$this->post_type_helper          = $post_type_helper;
		$this->wpdb                      = $wpdb;
		$this->post_link_indexing_action = $post_link_indexing_action;
		$this->admin_columns_cache       = $admin_columns_cache;
	}

	/**
	 * {@inheritDoc}
	 */
	public function register_hooks() {
		\add_filter( 'posts_clauses', [ $this, 'order_by_links' ], 1, 2 );
		\add_filter( 'posts_clauses', [ $this, 'order_by_linked' ], 1, 2 );

		\add_action( 'admin_init', [ $this, 'register_init_hooks' ] );

		// Adds a filter to exclude the attachments from the link count.
		\add_filter( 'wpseo_link_count_post_types', [ 'WPSEO_Post_Type', 'filter_attachment_post_type' ] );
	}

	/**
	 * Register hooks that need to be registered after `init` due to all post types not yet being registered.
	 *
	 * @return void
	 */
	public function register_init_hooks() {
		$public_post_types = \apply_filters( 'wpseo_link_count_post_types', $this->post_type_helper->get_accessible_post_types() );

		if ( ! \is_array( $public_post_types ) || empty( $public_post_types ) ) {
			return;
		}

		foreach ( $public_post_types as $post_type ) {
			\add_filter( 'manage_' . $post_type . '_posts_columns', [ $this, 'add_post_columns' ] );
			\add_action( 'manage_' . $post_type . '_posts_custom_column', [ $this, 'column_content' ], 10, 2 );
			\add_filter( 'manage_edit-' . $post_type . '_sortable_columns', [ $this, 'column_sort' ] );
		}
	}

	/**
	 * Adds the columns for the post overview.
	 *
	 * @param array $columns Array with columns.
	 *
	 * @return array The extended array with columns.
	 */
	public function add_post_columns( $columns ) {
		if ( ! \is_array( $columns ) ) {
			return $columns;
		}

		$columns[ 'wpseo-' . self::COLUMN_LINKS ] = \sprintf(
			'<span class="yoast-linked-to yoast-column-header-has-tooltip" data-tooltip-text="%1$s"><span class="screen-reader-text">%2$s</span></span>',
			\esc_attr__( 'Number of outgoing internal links in this post.', 'wordpress-seo' ),
			/* translators: Hidden accessibility text. */
			\esc_html__( 'Outgoing internal links', 'wordpress-seo' )
		);

		if ( $this->post_link_indexing_action->get_total_unindexed() === 0 ) {
			$columns[ 'wpseo-' . self::COLUMN_LINKED ] = \sprintf(
				'<span class="yoast-linked-from yoast-column-header-has-tooltip" data-tooltip-text="%1$s"><span class="screen-reader-text">%2$s</span></span>',
				\esc_attr__( 'Number of internal links linking to this post.', 'wordpress-seo' ),
				/* translators: Hidden accessibility text. */
				\esc_html__( 'Received internal links', 'wordpress-seo' )
			);
		}

		return $columns;
	}

	/**
	 * Modifies the query pieces to allow ordering column by links to post.
	 *
	 * @param array    $pieces Array of Query pieces.
	 * @param WP_Query $query  The Query on which to apply.
	 *
	 * @return array
	 */
	public function order_by_linked( $pieces, $query ) {
		if ( $query->get( 'orderby' ) !== 'wpseo-' . self::COLUMN_LINKED ) {
			return $pieces;
		}

		return $this->build_sort_query_pieces( $pieces, $query, 'incoming_link_count' );
	}

	/**
	 * Modifies the query pieces to allow ordering column by links to post.
	 *
	 * @param array    $pieces Array of Query pieces.
	 * @param WP_Query $query  The Query on which to apply.
	 *
	 * @return array
	 */
	public function order_by_links( $pieces, $query ) {
		if ( $query->get( 'orderby' ) !== 'wpseo-' . self::COLUMN_LINKS ) {
			return $pieces;
		}

		return $this->build_sort_query_pieces( $pieces, $query, 'link_count' );
	}

	/**
	 * Builds the pieces for a sorting query.
	 *
	 * @param array    $pieces Array of Query pieces.
	 * @param WP_Query $query  The Query on which to apply.
	 * @param string   $field  The field in the table to JOIN on.
	 *
	 * @return array Modified Query pieces.
	 */
	protected function build_sort_query_pieces( $pieces, $query, $field ) {
		// We only want our code to run in the main WP query.
		if ( ! $query->is_main_query() ) {
			return $pieces;
		}

		// Get the order query variable - ASC or DESC.
		$order = \strtoupper( $query->get( 'order' ) );

		// Make sure the order setting qualifies. If not, set default as ASC.
		if ( ! \in_array( $order, [ 'ASC', 'DESC' ], true ) ) {
			$order = 'ASC';
		}

		$table = Model::get_table_name( 'Indexable' );

		$pieces['join']   .= " LEFT JOIN $table AS yoast_indexable ON yoast_indexable.object_id = {$this->wpdb->posts}.ID AND yoast_indexable.object_type = 'post' ";
		$pieces['orderby'] = "yoast_indexable.$field $order, FIELD( {$this->wpdb->posts}.post_status, 'publish' ) $order, {$pieces['orderby']}";

		return $pieces;
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
		$indexable = $this->admin_columns_cache->get_indexable( $post_id );
		// Nothing to output if we don't have the value.
		if ( $indexable === false ) {
			return;
		}

		switch ( $column_name ) {
			case 'wpseo-' . self::COLUMN_LINKS:
				echo (int) $indexable->link_count;
				return;
			case 'wpseo-' . self::COLUMN_LINKED:
				if ( \get_post_status( $post_id ) === 'publish' ) {
					echo (int) $indexable->incoming_link_count;
				}
		}
	}

	/**
	 * Sets the sortable columns.
	 *
	 * @param array $columns Array with sortable columns.
	 *
	 * @return array The extended array with sortable columns.
	 */
	public function column_sort( array $columns ) {
		$columns[ 'wpseo-' . self::COLUMN_LINKS ]  = 'wpseo-' . self::COLUMN_LINKS;
		$columns[ 'wpseo-' . self::COLUMN_LINKED ] = 'wpseo-' . self::COLUMN_LINKED;

		return $columns;
	}
}
