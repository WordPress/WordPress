<?php
/**
 * Product > Reviews
 */

namespace Automattic\WooCommerce\Internal\Admin\ProductReviews;

use WC_Product;
use WP_Comment;
use WP_Comments_List_Table;
use WP_List_Table;
use WP_Post;

/**
 * Handles the Product Reviews page.
 */
class ReviewsListTable extends WP_List_Table {

	/**
	 * Memoization flag to determine if the current user can edit the current review.
	 *
	 * @var bool
	 */
	private $current_user_can_edit_review = false;

	/**
	 * Memoization flag to determine if the current user can moderate reviews.
	 *
	 * @var bool
	 */
	private $current_user_can_moderate_reviews;

	/**
	 * Current rating of reviews to display.
	 *
	 * @var int
	 */
	private $current_reviews_rating = 0;

	/**
	 * Current product the reviews should be displayed for.
	 *
	 * @var WC_Product|null Product or null for all products.
	 */
	private $current_product_for_reviews;

	/**
	 * Constructor.
	 *
	 * @param array|string $args Array or string of arguments.
	 */
	public function __construct( $args = [] ) {
		parent::__construct(
			wp_parse_args(
				$args,
				[
					'plural'   => 'product-reviews',
					'singular' => 'product-review',
				]
			)
		);

		$this->current_user_can_moderate_reviews = current_user_can( Reviews::get_capability( 'moderate' ) );
	}

	/**
	 * Prepares reviews for display.
	 *
	 * @return void
	 */
	public function prepare_items() : void {

		$this->set_review_status();
		$this->set_review_type();
		$this->current_reviews_rating = isset( $_REQUEST['review_rating'] ) ? absint( $_REQUEST['review_rating'] ) : 0;
		$this->set_review_product();

		$args = [
			'number'    => $this->get_per_page(),
			'post_type' => 'product',
		];

		// Include the order & orderby arguments.
		$args = wp_parse_args( $this->get_sort_arguments(), $args );
		// Handle the review item types filter.
		$args = wp_parse_args( $this->get_filter_type_arguments(), $args );
		// Handle the reviews rating filter.
		$args = wp_parse_args( $this->get_filter_rating_arguments(), $args );
		// Handle the review product filter.
		$args = wp_parse_args( $this->get_filter_product_arguments(), $args );
		// Include the review status arguments.
		$args = wp_parse_args( $this->get_status_arguments(), $args );
		// Include the search argument.
		$args = wp_parse_args( $this->get_search_arguments(), $args );
		// Include the offset argument.
		$args = wp_parse_args( $this->get_offset_arguments(), $args );

		/**
		 * Provides an opportunity to alter the comment query arguments used within
		 * the product reviews admin list table.
		 *
		 * @since 7.0.0
		 *
		 * @param array $args Comment query args.
		 */
		$args     = (array) apply_filters( 'woocommerce_product_reviews_list_table_prepare_items_args', $args );
		$comments = get_comments( $args );

		update_comment_cache( $comments );

		$this->items = $comments;

		$this->set_pagination_args(
			[
				'total_items' => get_comments( $this->get_total_comments_arguments( $args ) ),
				'per_page'    => $this->get_per_page(),
			]
		);
	}

	/**
	 * Returns the number of items to show per page.
	 *
	 * @return int Customized per-page value if available, or 20 as the default.
	 */
	protected function get_per_page() : int {
		return $this->get_items_per_page( 'edit_comments_per_page' );
	}

	/**
	 * Sets the product to filter reviews by.
	 *
	 * @return void
	 */
	protected function set_review_product() : void {

		$product_id = isset( $_REQUEST['product_id'] ) ? absint( $_REQUEST['product_id'] ) : null;
		$product = $product_id ? wc_get_product( $product_id ) : null;

		if ( $product instanceof WC_Product ) {
			$this->current_product_for_reviews = $product;
		}
	}

	/**
	 * Sets the `$comment_status` global based on the current request.
	 *
	 * @global string $comment_status
	 *
	 * @return void
	 */
	protected function set_review_status() : void {
		global $comment_status;

		$comment_status = sanitize_text_field( wp_unslash( $_REQUEST['comment_status'] ?? 'all' ) ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

		if ( ! in_array( $comment_status, [ 'all', 'moderated', 'approved', 'spam', 'trash' ], true ) ) {
			$comment_status = 'all'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		}
	}

	/**
	 * Sets the `$comment_type` global based on the current request.
	 *
	 * @global string $comment_type
	 *
	 * @return void
	 */
	protected function set_review_type() : void {
		global $comment_type;

		$review_type = sanitize_text_field( wp_unslash( $_REQUEST['review_type'] ?? 'all' ) );

		if ( 'all' !== $review_type && ! empty( $review_type ) ) {
			$comment_type = $review_type; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		}
	}

	/**
	 * Builds the `orderby` and `order` arguments based on the current request.
	 *
	 * @return array
	 */
	protected function get_sort_arguments() : array {
		$orderby = sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ?? '' ) );
		$order   = sanitize_text_field( wp_unslash( $_REQUEST['order'] ?? '' ) );

		$args = [];

		if ( ! in_array( $orderby, $this->get_sortable_columns(), true ) ) {
			$orderby = 'comment_date_gmt';
		}

		// If ordering by "rating", then we need to adjust to sort by meta value.
		if ( 'rating' === $orderby ) {
			$orderby          = 'meta_value_num';
			$args['meta_key'] = 'rating';
		}

		if ( ! in_array( strtolower( $order ), [ 'asc', 'desc' ], true ) ) {
			$order = 'desc';
		}

		return wp_parse_args(
			[
				'orderby' => $orderby,
				'order'   => strtolower( $order ),
			],
			$args
		);
	}

	/**
	 * Builds the `type` argument based on the current request.
	 *
	 * @return array
	 */
	protected function get_filter_type_arguments() : array {

		$args      = [];
		$item_type = isset( $_REQUEST['review_type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['review_type'] ) ) : 'all';

		if ( 'all' === $item_type ) {
			return $args;
		}

		$args['type'] = $item_type;

		return $args;
	}

	/**
	 * Builds the `meta_query` arguments based on the current request.
	 *
	 * @return array
	 */
	protected function get_filter_rating_arguments() : array {

		$args = [];

		if ( empty( $this->current_reviews_rating ) ) {
			return $args;
		}

		$args['meta_query'] = [
			[
				'key'     => 'rating',
				'value'   => (int) $this->current_reviews_rating,
				'compare' => '=',
				'type'    => 'NUMERIC',
			],
		];

		return $args;
	}

	/**
	 * Gets the `post_id` argument based on the current request.
	 *
	 * @return array
	 */
	public function get_filter_product_arguments() : array {

		$args = [];

		if ( $this->current_product_for_reviews instanceof WC_Product ) {
			$args['post_id'] = $this->current_product_for_reviews->get_id();
		}

		return $args;
	}

	/**
	 * Gets the `status` argument based on the current request.
	 *
	 * @return array
	 */
	protected function get_status_arguments() : array {
		$args = [];

		global $comment_status;

		if ( ! empty( $comment_status ) && 'all' !== $comment_status && array_key_exists( $comment_status, $this->get_status_filters() ) ) {
			$args['status'] = $this->convert_status_to_query_value( $comment_status );
		}

		return $args;
	}

	/**
	 * Gets the `search` argument based on the current request.
	 *
	 * @return array
	 */
	protected function get_search_arguments() : array {
		$args = [];

		if ( ! empty( $_REQUEST['s'] ) ) {
			$args['search'] = sanitize_text_field( wp_unslash( $_REQUEST['s'] ) );
		}

		return $args;
	}

	/**
	 * Returns the `offset` argument based on the current request.
	 *
	 * @return array
	 */
	protected function get_offset_arguments() : array {
		$args = [];

		if ( isset( $_REQUEST['start'] ) ) {
			$args['offset'] = absint( wp_unslash( $_REQUEST['start'] ) );
		} else {
			$args['offset'] = ( $this->get_pagenum() - 1 ) * $this->get_per_page();
		}

		return $args;
	}

	/**
	 * Returns the arguments used to count the total number of comments.
	 *
	 * @param array $default_query_args Query args for the main request.
	 * @return array
	 */
	protected function get_total_comments_arguments( array $default_query_args ) : array {
		return wp_parse_args(
			[
				'count'  => true,
				'offset' => 0,
				'number' => 0,
			],
			$default_query_args
		);
	}

	/**
	 * Displays the product reviews HTML table.
	 *
	 * Reimplements {@see WP_Comment_::display()} but we change the ID to match the one output by {@see WP_Comments_List_Table::display()}.
	 * This will automatically handle additional CSS for consistency with the comments page.
	 *
	 * @return void
	 */
	public function display() : void {
		$this->display_tablenav( 'top' );

		$this->screen->render_screen_reader_content( 'heading_list' );

		?>
		<table class="wp-list-table <?php echo esc_attr( implode( ' ', $this->get_table_classes() ) ); ?>">
			<thead>
			<tr>
				<?php $this->print_column_headers(); ?>
			</tr>
			</thead>
			<tbody id="the-comment-list" data-wp-lists="list:comment">
			<?php $this->display_rows_or_placeholder(); ?>
			</tbody>
			<tfoot>
			<tr>
				<?php $this->print_column_headers( false ); ?>
			</tr>
			</tfoot>
		</table>
		<?php

		$this->display_tablenav( 'bottom' );
	}

	/**
	 * Render a single row HTML.
	 *
	 * @global WP_Post $post
	 * @global WP_Comment $comment
	 *
	 * @param WP_Comment|mixed $item Review or reply being rendered.
	 * @return void
	 */
	public function single_row( $item ) : void {
		global $post, $comment;

		// Overrides the comment global for properly rendering rows.
		$comment           = $item; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$the_comment_class = (string) wp_get_comment_status( $comment->comment_ID );
		$the_comment_class = implode( ' ', get_comment_class( $the_comment_class, $comment->comment_ID, $comment->comment_post_ID ) );
		// Sets the post for the product in context.
		$post = get_post( $comment->comment_post_ID ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

		$this->current_user_can_edit_review = current_user_can( 'edit_comment', $comment->comment_ID );

		?>
		<tr id="comment-<?php echo esc_attr( $comment->comment_ID ); ?>" class="comment <?php echo esc_attr( $the_comment_class ); ?>">
			<?php $this->single_row_columns( $comment ); ?>
		</tr>
		<?php
	}

	/**
	 * Generate and display row actions links.
	 *
	 * @see WP_Comments_List_Table::handle_row_actions() for consistency.
	 *
	 * @global string $comment_status Status for the current listed comments.
	 *
	 * @param WP_Comment|mixed $item        The product review or reply in context.
	 * @param string|mixed     $column_name Current column name.
	 * @param string|mixed     $primary     Primary column name.
	 * @return string
	 */
	protected function handle_row_actions( $item, $column_name, $primary ) : string {
		global $comment_status;

		if ( $primary !== $column_name || ! $this->current_user_can_edit_review ) {
			return '';
		}

		$review_status = wp_get_comment_status( $item );

		$url = add_query_arg(
			[
				'c' => urlencode( $item->comment_ID ),
			],
			admin_url( 'comment.php' )
		);

		$approve_url   = wp_nonce_url( add_query_arg( 'action', 'approvecomment', $url ), "approve-comment_$item->comment_ID" );
		$unapprove_url = wp_nonce_url( add_query_arg( 'action', 'unapprovecomment', $url ), "approve-comment_$item->comment_ID" );
		$spam_url      = wp_nonce_url( add_query_arg( 'action', 'spamcomment', $url ), "delete-comment_$item->comment_ID" );
		$unspam_url    = wp_nonce_url( add_query_arg( 'action', 'unspamcomment', $url ), "delete-comment_$item->comment_ID" );
		$trash_url     = wp_nonce_url( add_query_arg( 'action', 'trashcomment', $url ), "delete-comment_$item->comment_ID" );
		$untrash_url   = wp_nonce_url( add_query_arg( 'action', 'untrashcomment', $url ), "delete-comment_$item->comment_ID" );
		$delete_url    = wp_nonce_url( add_query_arg( 'action', 'deletecomment', $url ), "delete-comment_$item->comment_ID" );

		$actions = [
			'approve'   => '',
			'unapprove' => '',
			'reply'     => '',
			'quickedit' => '',
			'edit'      => '',
			'spam'      => '',
			'unspam'    => '',
			'trash'     => '',
			'untrash'   => '',
			'delete'    => '',
		];

		if ( $comment_status && 'all' !== $comment_status ) {
			if ( 'approved' === $review_status ) {
				$actions['unapprove'] = sprintf(
					'<a href="%s" data-wp-lists="%s" class="vim-u vim-destructive aria-button-if-js" aria-label="%s">%s</a>',
					esc_url( $unapprove_url ),
					esc_attr( "delete:the-comment-list:comment-{$item->comment_ID}:e7e7d3:action=dim-comment&amp;new=unapproved" ),
					esc_attr__( 'Unapprove this review', 'woocommerce' ),
					esc_html__( 'Unapprove', 'woocommerce' )
				);
			} elseif ( 'unapproved' === $review_status ) {
				$actions['approve'] = sprintf(
					'<a href="%s" data-wp-lists="%s" class="vim-a vim-destructive aria-button-if-js" aria-label="%s">%s</a>',
					esc_url( $approve_url ),
					esc_attr( "delete:the-comment-list:comment-{$item->comment_ID}:e7e7d3:action=dim-comment&amp;new=approved" ),
					esc_attr__( 'Approve this review', 'woocommerce' ),
					esc_html__( 'Approve', 'woocommerce' )
				);
			}
		} else {
			$actions['approve'] = sprintf(
				'<a href="%s" data-wp-lists="%s" class="vim-a aria-button-if-js" aria-label="%s">%s</a>',
				esc_url( $approve_url ),
				esc_attr( "dim:the-comment-list:comment-{$item->comment_ID}:unapproved:e7e7d3:e7e7d3:new=approved" ),
				esc_attr__( 'Approve this review', 'woocommerce' ),
				esc_html__( 'Approve', 'woocommerce' )
			);

			$actions['unapprove'] = sprintf(
				'<a href="%s" data-wp-lists="%s" class="vim-u aria-button-if-js" aria-label="%s">%s</a>',
				esc_url( $unapprove_url ),
				esc_attr( "dim:the-comment-list:comment-{$item->comment_ID}:unapproved:e7e7d3:e7e7d3:new=unapproved" ),
				esc_attr__( 'Unapprove this review', 'woocommerce' ),
				esc_html__( 'Unapprove', 'woocommerce' )
			);
		}

		if ( 'spam' !== $review_status ) {
			$actions['spam'] = sprintf(
				'<a href="%s" data-wp-lists="%s" class="vim-s vim-destructive aria-button-if-js" aria-label="%s">%s</a>',
				esc_url( $spam_url ),
				esc_attr( "delete:the-comment-list:comment-{$item->comment_ID}::spam=1" ),
				esc_attr__( 'Mark this review as spam', 'woocommerce' ),
				/* translators: "Mark as spam" link. */
				esc_html_x( 'Spam', 'verb', 'woocommerce' )
			);
		} else {
			$actions['unspam'] = sprintf(
				'<a href="%s" data-wp-lists="%s" class="vim-z vim-destructive aria-button-if-js" aria-label="%s">%s</a>',
				esc_url( $unspam_url ),
				esc_attr( "delete:the-comment-list:comment-{$item->comment_ID}:66cc66:unspam=1" ),
				esc_attr__( 'Restore this review from the spam', 'woocommerce' ),
				esc_html_x( 'Not Spam', 'review', 'woocommerce' )
			);
		}

		if ( 'trash' === $review_status ) {
			$actions['untrash'] = sprintf(
				'<a href="%s" data-wp-lists="%s" class="vim-z vim-destructive aria-button-if-js" aria-label="%s">%s</a>',
				esc_url( $untrash_url ),
				esc_attr( "delete:the-comment-list:comment-{$item->comment_ID}:66cc66:untrash=1" ),
				esc_attr__( 'Restore this review from the Trash', 'woocommerce' ),
				esc_html__( 'Restore', 'woocommerce' )
			);
		}

		if ( 'spam' === $review_status || 'trash' === $review_status || ! EMPTY_TRASH_DAYS ) {
			$actions['delete'] = sprintf(
				'<a href="%s" data-wp-lists="%s" class="delete vim-d vim-destructive aria-button-if-js" aria-label="%s">%s</a>',
				esc_url( $delete_url ),
				esc_attr( "delete:the-comment-list:comment-{$item->comment_ID}::delete=1" ),
				esc_attr__( 'Delete this review permanently', 'woocommerce' ),
				esc_html__( 'Delete Permanently', 'woocommerce' )
			);
		} else {
			$actions['trash'] = sprintf(
				'<a href="%s" data-wp-lists="%s" class="delete vim-d vim-destructive aria-button-if-js" aria-label="%s">%s</a>',
				esc_url( $trash_url ),
				esc_attr( "delete:the-comment-list:comment-{$item->comment_ID}::trash=1" ),
				esc_attr__( 'Move this review to the Trash', 'woocommerce' ),
				esc_html_x( 'Trash', 'verb', 'woocommerce' )
			);
		}

		if ( 'spam' !== $review_status && 'trash' !== $review_status ) {
			$actions['edit'] = sprintf(
				'<a href="%s" aria-label="%s">%s</a>',
				esc_url(
					add_query_arg(
						[
							'action' => 'editcomment',
							'c'      => urlencode( $item->comment_ID ),
						],
						admin_url( 'comment.php' )
					)
				),
				esc_attr__( 'Edit this review', 'woocommerce' ),
				esc_html__( 'Edit', 'woocommerce' )
			);

			$format = '<button type="button" data-comment-id="%d" data-post-id="%d" data-action="%s" class="%s button-link" aria-expanded="false" aria-label="%s">%s</button>';

			$actions['quickedit'] = sprintf(
				$format,
				esc_attr( $item->comment_ID ),
				esc_attr( $item->comment_post_ID ),
				'edit',
				'vim-q comment-inline',
				esc_attr__( 'Quick edit this review inline', 'woocommerce' ),
				esc_html__( 'Quick Edit', 'woocommerce' )
			);

			$actions['reply'] = sprintf(
				$format,
				esc_attr( $item->comment_ID ),
				esc_attr( $item->comment_post_ID ),
				'replyto',
				'vim-r comment-inline',
				esc_attr__( 'Reply to this review', 'woocommerce' ),
				esc_html__( 'Reply', 'woocommerce' )
			);
		}

		$always_visible = 'excerpt' === get_user_setting( 'posts_list_mode', 'list' );

		$output = '<div class="' . ( $always_visible ? 'row-actions visible' : 'row-actions' ) . '">';

		$i = 0;

		foreach ( array_filter( $actions ) as $action => $link ) {
			++$i;

			if ( ( ( 'approve' === $action || 'unapprove' === $action ) && 2 === $i ) || 1 === $i ) {
				$sep = '';
			} else {
				$sep = ' | ';
			}

			if ( ( 'reply' === $action || 'quickedit' === $action ) && ! wp_doing_ajax() ) {
				$action .= ' hide-if-no-js';
			} elseif ( ( 'untrash' === $action && 'trash' === $review_status ) || ( 'unspam' === $action && 'spam' === $review_status ) ) {
				if ( '1' === get_comment_meta( $item->comment_ID, '_wp_trash_meta_status', true ) ) {
					$action .= ' approve';
				} else {
					$action .= ' unapprove';
				}
			}

			$output .= "<span class='$action'>$sep$link</span>";
		}

		$output .= '</div>';
		$output .= '<button type="button" class="toggle-row"><span class="screen-reader-text">' . esc_html__( 'Show more details', 'woocommerce' ) . '</span></button>';

		return $output;
	}

	/**
	 * Gets the columns for the table.
	 *
	 * @return array Table columns and their headings.
	 */
	public function get_columns() : array {
		$columns = [
			'cb'       => '<input type="checkbox" />',
			'type'     => _x( 'Type', 'review type', 'woocommerce' ),
			'author'   => __( 'Author', 'woocommerce' ),
			'rating'   => __( 'Rating', 'woocommerce' ),
			'comment'  => _x( 'Review', 'column name', 'woocommerce' ),
			'response' => __( 'Product', 'woocommerce' ),
			'date'     => _x( 'Submitted on', 'column name', 'woocommerce' ),
		];

		/**
		 * Filters the table columns.
		 *
		 * @since 6.7.0
		 *
		 * @param array $columns
		 */
		return (array) apply_filters( 'woocommerce_product_reviews_table_columns', $columns );
	}

	/**
	 * Gets the name of the default primary column.
	 *
	 * @return string Name of the primary colum.
	 */
	protected function get_primary_column_name() : string {
		return 'comment';
	}

	/**
	 * Gets a list of sortable columns.
	 *
	 * Key is the column ID and value is which database column we perform the sorting on.
	 * The `rating` column uses a unique key instead, as that requires sorting by meta value.
	 *
	 * @return array
	 */
	protected function get_sortable_columns() : array {
		return [
			'author'   => 'comment_author',
			'response' => 'comment_post_ID',
			'date'     => 'comment_date_gmt',
			'type'     => 'comment_type',
			'rating'   => 'rating',
		];
	}

	/**
	 * Returns a list of available bulk actions.
	 *
	 * @global string $comment_status
	 *
	 * @return array
	 */
	protected function get_bulk_actions() : array {
		global $comment_status;

		$actions = [];

		if ( in_array( $comment_status, [ 'all', 'approved' ], true ) ) {
			$actions['unapprove'] = __( 'Unapprove', 'woocommerce' );
		}

		if ( in_array( $comment_status, [ 'all', 'moderated' ], true ) ) {
			$actions['approve'] = __( 'Approve', 'woocommerce' );
		}

		if ( in_array( $comment_status, [ 'all', 'moderated', 'approved', 'trash' ], true ) ) {
			$actions['spam'] = _x( 'Mark as spam', 'review', 'woocommerce' );
		}

		if ( 'trash' === $comment_status ) {
			$actions['untrash'] = __( 'Restore', 'woocommerce' );
		} elseif ( 'spam' === $comment_status ) {
			$actions['unspam'] = _x( 'Not spam', 'review', 'woocommerce' );
		}

		if ( in_array( $comment_status, [ 'trash', 'spam' ], true ) || ! EMPTY_TRASH_DAYS ) {
			$actions['delete'] = __( 'Delete permanently', 'woocommerce' );
		} else {
			$actions['trash'] = __( 'Move to Trash', 'woocommerce' );
		}

		return $actions;
	}

	/**
	 * Returns the current action select in bulk actions menu.
	 *
	 * This is overridden in order to support `delete_all` for use in {@see ReviewsListTable::process_bulk_action()}
	 *
	 * {@see WP_Comments_List_Table::current_action()} for reference.
	 *
	 * @return string|false
	 */
	public function current_action() {
		if ( isset( $_REQUEST['delete_all'] ) || isset( $_REQUEST['delete_all2'] ) ) {
			return 'delete_all';
		}

		return parent::current_action();
	}

	/**
	 * Processes the bulk actions.
	 *
	 * @return void
	 */
	public function process_bulk_action() : void {

		if ( ! $this->current_user_can_moderate_reviews ) {
			return;
		}

		if ( $this->current_action() ) {
			check_admin_referer( 'bulk-product-reviews' );

			$query_string = remove_query_arg( [ 'page', '_wpnonce' ], wp_unslash( ( $_SERVER['QUERY_STRING'] ?? '' ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			// Replace current nonce with bulk-comments nonce.
			$comments_nonce = wp_create_nonce( 'bulk-comments' );
			$query_string   = add_query_arg( '_wpnonce', $comments_nonce, $query_string );

			// Redirect to edit-comments.php, which will handle processing the action for us.
			wp_safe_redirect( esc_url_raw( admin_url( 'edit-comments.php?' . $query_string ) ) );
			exit;
		} elseif ( ! empty( $_GET['_wp_http_referer'] ) ) {

			wp_safe_redirect( remove_query_arg( [ '_wp_http_referer', '_wpnonce' ] ) );
			exit;
		}
	}

	/**
	 * Returns an array of supported statuses and their labels.
	 *
	 * @return array
	 */
	protected function get_status_filters() : array {
		return [
			/* translators: %s: Number of reviews. */
			'all'       => _nx_noop(
				'All <span class="count">(%s)</span>',
				'All <span class="count">(%s)</span>',
				'product reviews',
				'woocommerce'
			),
			/* translators: %s: Number of reviews. */
			'moderated' => _nx_noop(
				'Pending <span class="count">(%s)</span>',
				'Pending <span class="count">(%s)</span>',
				'product reviews',
				'woocommerce'
			),
			/* translators: %s: Number of reviews. */
			'approved'  => _nx_noop(
				'Approved <span class="count">(%s)</span>',
				'Approved <span class="count">(%s)</span>',
				'product reviews',
				'woocommerce'
			),
			/* translators: %s: Number of reviews. */
			'spam'      => _nx_noop(
				'Spam <span class="count">(%s)</span>',
				'Spam <span class="count">(%s)</span>',
				'product reviews',
				'woocommerce'
			),
			/* translators: %s: Number of reviews. */
			'trash'     => _nx_noop(
				'Trash <span class="count">(%s)</span>',
				'Trash <span class="count">(%s)</span>',
				'product reviews',
				'woocommerce'
			),
		];
	}

	/**
	 * Returns the available status filters.
	 *
	 * @see WP_Comments_List_Table::get_views() for consistency.
	 *
	 * @global int    $post_id
	 * @global string $comment_status
	 * @global string $comment_type
	 *
	 * @return array An associative array of fully-formed comment status links. Includes 'All', 'Pending', 'Approved', 'Spam', and 'Trash'.
	 */
	protected function get_views() : array {
		global $post_id, $comment_status, $comment_type;

		$status_links = [];

		$status_labels = $this->get_status_filters();

		if ( ! EMPTY_TRASH_DAYS ) {
			unset( $status_labels['trash'] );
		}

		$link = $this->get_view_url( (string) $comment_type, (int) $post_id );

		foreach ( $status_labels as $status => $label ) {
			$current_link_attributes = '';

			if ( $status === $comment_status ) {
				$current_link_attributes = ' class="current" aria-current="page"';
			}

			$link = add_query_arg( 'comment_status', urlencode( $status ), $link );

			$number_reviews_for_status = $this->get_review_count( $status, (int) $post_id );

			$count_html = sprintf(
				'<span class="%s-count">%s</span>',
				( 'moderated' === $status ) ? 'pending' : $status,
				number_format_i18n( $number_reviews_for_status )
			);

			$status_links[ $status ] = '<a href="' . esc_url( $link ) . '"' . $current_link_attributes . '>' . sprintf( translate_nooped_plural( $label, $number_reviews_for_status ), $count_html ) . '</a>';
		}

		return $status_links;
	}

	/**
	 * Gets the base URL for a view, excluding the status (that should be appended).
	 *
	 * @param string $comment_type Comment type filter.
	 * @param int    $post_id      Current post ID.
	 * @return string
	 */
	protected function get_view_url( string $comment_type, int $post_id ) : string {
		$link = Reviews::get_reviews_page_url();

		if ( ! empty( $comment_type ) && 'all' !== $comment_type ) {
			$link = add_query_arg( 'comment_type', urlencode( $comment_type ), $link );
		}
		if ( ! empty( $post_id ) ) {
			$link = add_query_arg( 'p', absint( $post_id ), $link );
		}

		return $link;
	}

	/**
	 * Gets the number of reviews (including review replies) for a given status.
	 *
	 * @param string $status     Status key from {@see ReviewsListTable::get_status_filters()}.
	 * @param int    $product_id ID of the product if we're filtering by product in this request. Otherwise, `0` for no product filters.
	 * @return int
	 */
	protected function get_review_count( string $status, int $product_id ) : int {
		return (int) get_comments(
			[
				'type__in'  => [ 'review', 'comment' ],
				'status'    => $this->convert_status_to_query_value( $status ),
				'post_type' => 'product',
				'post_id'   => $product_id,
				'count'     => true,
			]
		);
	}

	/**
	 * Converts a status key into its equivalent `comment_approved` database column value.
	 *
	 * @param string $status Status key from {@see ReviewsListTable::get_status_filters()}.
	 * @return string
	 */
	protected function convert_status_to_query_value( string $status ) : string {
		// These keys exactly match the database column.
		if ( in_array( $status, [ 'spam', 'trash' ], true ) ) {
			return $status;
		}

		switch ( $status ) {
			case 'moderated':
				return '0';
			case 'approved':
				return '1';
			default:
				return 'all';
		}
	}

	/**
	 * Outputs the text to display when there are no reviews to display.
	 *
	 * @see WP_List_Table::no_items()
	 *
	 * @global string $comment_status
	 *
	 * @return void
	 */
	public function no_items() : void {
		global $comment_status;

		if ( 'moderated' === $comment_status ) {
			esc_html_e( 'No reviews awaiting moderation.', 'woocommerce' );
		} else {
			esc_html_e( 'No reviews found.', 'woocommerce' );
		}
	}

	/**
	 * Renders the checkbox column.
	 *
	 * @param WP_Comment|mixed $item Review or reply being rendered.
	 * @return void
	 */
	protected function column_cb( $item ) : void {

		ob_start();

		if ( $this->current_user_can_edit_review ) {
			?>
			<label class="screen-reader-text" for="cb-select-<?php echo esc_attr( $item->comment_ID ); ?>"><?php esc_html_e( 'Select review', 'woocommerce' ); ?></label>
			<input
				id="cb-select-<?php echo esc_attr( $item->comment_ID ); ?>"
				type="checkbox"
				name="delete_comments[]"
				value="<?php echo esc_attr( $item->comment_ID ); ?>"
			/>
			<?php
		}

		echo $this->filter_column_output( 'cb', ob_get_clean(), $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Renders the review column.
	 *
	 * @see WP_Comments_List_Table::column_comment() for consistency.
	 *
	 * @param WP_Comment|mixed $item Review or reply being rendered.
	 * @return void
	 */
	protected function column_comment( $item ) : void {

		$in_reply_to = $this->get_in_reply_to_review_text( $item );

		ob_start();

		if ( $in_reply_to ) {
			echo $in_reply_to . '<br><br>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		printf(
			'%1$s%2$s%3$s',
			'<div class="comment-text">',
			get_comment_text( $item->comment_ID ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'</div>'
		);

		if ( $this->current_user_can_edit_review ) {
			?>
			<div id="inline-<?php echo esc_attr( $item->comment_ID ); ?>" class="hidden">
				<textarea class="comment" rows="1" cols="1"><?php echo esc_textarea( $item->comment_content ); ?></textarea>
				<div class="author-email"><?php echo esc_attr( $item->comment_author_email ); ?></div>
				<div class="author"><?php echo esc_attr( $item->comment_author ); ?></div>
				<div class="author-url"><?php echo esc_attr( $item->comment_author_url ); ?></div>
				<div class="comment_status"><?php echo esc_html( $item->comment_approved ); ?></div>
			</div>
			<?php
		}

		echo $this->filter_column_output( 'comment', ob_get_clean(), $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Gets the in-reply-to-review text.
	 *
	 * @param WP_Comment|mixed $reply Reply to review.
	 * @return string
	 */
	private function get_in_reply_to_review_text( $reply ) : string {

		$review = $reply->comment_parent ? get_comment( $reply->comment_parent ) : null;

		if ( ! $review ) {
			return '';
		}

		$parent_review_link = get_comment_link( $review );
		$review_author_name = get_comment_author( $review );

		return sprintf(
			/* translators: %s: Parent review link with review author name. */
			ent2ncr( __( 'In reply to %s.', 'woocommerce' ) ),
			'<a href="' . esc_url( $parent_review_link ) . '">' . esc_html( $review_author_name ) . '</a>'
		);
	}

	/**
	 * Renders the author column.
	 *
	 * @see WP_Comments_List_Table::column_author() for consistency.
	 *
	 * @param WP_Comment|mixed $item Review or reply being rendered.
	 * @return void
	 */
	protected function column_author( $item ) : void {
		global $comment_status;

		$author_url = $this->get_item_author_url();
		$author_url_display = $this->get_item_author_url_for_display( $author_url );

		if ( get_option( 'show_avatars' ) ) {
			$author_avatar = get_avatar( $item, 32, 'mystery' );
		} else {
			$author_avatar = '';
		}

		ob_start();

		echo '<strong>' . $author_avatar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		comment_author();
		echo '</strong><br>';

		if ( ! empty( $author_url ) ) :

			?>
			<a title="<?php echo esc_attr( $author_url ); ?>" href="<?php echo esc_url( $author_url ); ?>" rel="noopener noreferrer"><?php echo esc_html( $author_url_display ); ?></a>
			<br>
			<?php

		endif;

		if ( $this->current_user_can_edit_review ) :

			if ( ! empty( $item->comment_author_email ) && is_email( $item->comment_author_email ) ) :

				?>
				<a href="mailto:<?php echo esc_attr( $item->comment_author_email ); ?>"><?php echo esc_html( $item->comment_author_email ); ?></a><br>
				<?php

			endif;

			$link = add_query_arg(
				[
					's'    => urlencode( get_comment_author_IP( $item->comment_ID ) ),
					'page' => Reviews::MENU_SLUG,
					'mode' => 'detail',
				],
				'admin.php'
			);

			if ( 'spam' === $comment_status ) :
				$link = add_query_arg( [ 'comment_status' => 'spam' ], $link );
			endif;

			?>
			<a href="<?php echo esc_url( $link ); ?>"><?php comment_author_IP( $item->comment_ID ); ?></a>
			<?php

		endif;

		echo $this->filter_column_output( 'author', ob_get_clean(), $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Gets the item author URL.
	 *
	 * @return string
	 */
	private function get_item_author_url() : string {

		$author_url = get_comment_author_url();
		$protocols = [ 'https://', 'http://' ];

		if ( in_array( $author_url, $protocols ) ) {
			$author_url = '';
		}

		return $author_url;
	}

	/**
	 * Gets the item author URL for display.
	 *
	 * @param string $author_url The review or reply author URL (raw).
	 * @return string
	 */
	private function get_item_author_url_for_display( $author_url ) : string {

		$author_url_display = untrailingslashit( preg_replace( '|^http(s)?://(www\.)?|i', '', $author_url ) );

		if ( strlen( $author_url_display ) > 50 ) {
			$author_url_display = wp_html_excerpt( $author_url_display, 49, '&hellip;' );
		}

		return $author_url_display;
	}

	/**
	 * Renders the "submitted on" column.
	 *
	 * Note that the output is consistent with {@see WP_Comments_List_Table::column_date()}.
	 *
	 * @param WP_Comment|mixed $item Review or reply being rendered.
	 * @return void
	 */
	protected function column_date( $item ) : void {

		$submitted = sprintf(
			/* translators: 1 - Product review date, 2: Product review time. */
			__( '%1$s at %2$s', 'woocommerce' ),
			/* translators: Review date format. See https://www.php.net/manual/datetime.format.php */
			get_comment_date( __( 'Y/m/d', 'woocommerce' ), $item ),
			/* translators: Review time format. See https://www.php.net/manual/datetime.format.php */
			get_comment_date( __( 'g:i a', 'woocommerce' ), $item )
		);

		ob_start();

		?>
		<div class="submitted-on">
			<?php

			if ( 'approved' === wp_get_comment_status( $item ) && ! empty( $item->comment_post_ID ) ) :
				printf(
					'<a href="%1$s">%2$s</a>',
					esc_url( get_comment_link( $item ) ),
					esc_html( $submitted )
				);
			else :
				echo esc_html( $submitted );
			endif;

			?>
		</div>
		<?php

		echo $this->filter_column_output( 'date', ob_get_clean(), $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Renders the product column.
	 *
	 * @see WP_Comments_List_Table::column_response() for consistency.
	 *
	 * @param WP_Comment|mixed $item Review or reply being rendered.
	 * @return void
	 */
	protected function column_response( $item ) : void {
		$product_post = get_post();

		ob_start();

		if ( $product_post ) :

			?>
			<div class="response-links">
				<?php

				if ( current_user_can( 'edit_product', $product_post->ID ) ) :
					$post_link  = "<a href='" . esc_url( get_edit_post_link( $product_post->ID ) ) . "' class='comments-edit-item-link'>";
					$post_link .= esc_html( get_the_title( $product_post->ID ) ) . '</a>';
				else :
					$post_link = esc_html( get_the_title( $product_post->ID ) );
				endif;

				echo $post_link; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

				$post_type_object = get_post_type_object( $product_post->post_type );

				?>
				<a href="<?php echo esc_url( get_permalink( $product_post->ID ) ); ?>" class="comments-view-item-link">
					<?php echo esc_html( $post_type_object->labels->view_item ); ?>
				</a>
				<span class="post-com-count-wrapper post-com-count-<?php echo esc_attr( $product_post->ID ); ?>">
					<?php $this->comments_bubble( $product_post->ID, get_pending_comments_num( $product_post->ID ) ); ?>
				</span>
			</div>
			<?php

		endif;

		echo $this->filter_column_output( 'response', ob_get_clean(), $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Renders the type column.
	 *
	 * @param WP_Comment|mixed $item Review or reply being rendered.
	 * @return void
	 */
	protected function column_type( $item ) : void {

		$type = 'review' === $item->comment_type
			? '&#9734;&nbsp;' . __( 'Review', 'woocommerce' )
			: __( 'Reply', 'woocommerce' );

		echo $this->filter_column_output( 'type', esc_html( $type ), $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Renders the rating column.
	 *
	 * @param WP_Comment|mixed $item Review or reply being rendered.
	 * @return void
	 */
	protected function column_rating( $item ) : void {
		$rating = get_comment_meta( $item->comment_ID, 'rating', true );

		ob_start();

		if ( ! empty( $rating ) && is_numeric( $rating ) ) {
			$rating = (int) $rating;

			$accessibility_label = sprintf(
				/* translators: 1: number representing a rating */
				__( '%1$d out of 5', 'woocommerce' ),
				$rating
			);

			$stars = str_repeat( '&#9733;', $rating );
			$stars .= str_repeat( '&#9734;', 5 - $rating );

			?>
			<span aria-label="<?php echo esc_attr( $accessibility_label ); ?>"><?php echo esc_html( $stars ); ?></span>
			<?php
		}

		echo $this->filter_column_output( 'rating', ob_get_clean(), $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Renders any custom columns.
	 *
	 * @param WP_Comment|mixed $item        Review or reply being rendered.
	 * @param string|mixed     $column_name Name of the column being rendered.
	 * @return void
	 */
	protected function column_default( $item, $column_name ) : void {

		ob_start();

		/**
		 * Fires when the default column output is displayed for a single row.
		 *
		 * This action can be used to render custom columns that have been added.
		 *
		 * @since 6.7.0
		 *
		 * @param WP_Comment $item The review or reply being rendered.
		 */
		do_action( 'woocommerce_product_reviews_table_column_' . $column_name, $item );

		echo $this->filter_column_output( $column_name, ob_get_clean(), $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Runs a filter hook for a given column content.
	 *
	 * @param string|mixed     $column_name The column being output.
	 * @param string|mixed     $output      The output content (may include HTML).
	 * @param WP_Comment|mixed $item        The review or reply being rendered.
	 * @return string
	 */
	protected function filter_column_output( $column_name, $output, $item ) : string {

		/**
		 * Filters the output of a column.
		 *
		 * @since 6.7.0
		 *
		 * @param string     $output The column output.
		 * @param WP_Comment $item   The product review being rendered.
		 */
		return (string) apply_filters( 'woocommerce_product_reviews_table_column_' . $column_name . '_content', $output, $item );
	}

	/**
	 * Renders the extra controls to be displayed between bulk actions and pagination.
	 *
	 * @global string $comment_status
	 * @global string $comment_type
	 *
	 * @param string|mixed $which Position (top or bottom).
	 * @return void
	 */
	protected function extra_tablenav( $which ) : void {
		global $comment_status, $comment_type;

		echo '<div class="alignleft actions">';

		if ( 'top' === $which ) {

			ob_start();

			echo '<input type="hidden" name="comment_status" value="' . esc_attr( $comment_status ?? 'all' ) . '" />';

			$this->review_type_dropdown( $comment_type );
			$this->review_rating_dropdown( $this->current_reviews_rating );
			$this->product_search( $this->current_product_for_reviews );

			echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			submit_button( __( 'Filter', 'woocommerce' ), '', 'filter_action', false, [ 'id' => 'post-query-submit' ] );
		}

		if ( ( 'spam' === $comment_status || 'trash' === $comment_status ) && $this->has_items() && $this->current_user_can_moderate_reviews ) {

			wp_nonce_field( 'bulk-destroy', '_destroy_nonce' );

			$title = 'spam' === $comment_status
				? esc_attr__( 'Empty Spam', 'woocommerce' )
				: esc_attr__( 'Empty Trash', 'woocommerce' );

			submit_button( $title, 'apply', 'delete_all', false );
		}

		echo '</div>';
	}

	/**
	 * Displays a review type drop-down for filtering reviews in the Product Reviews list table.
	 *
	 * @see WP_Comments_List_Table::comment_type_dropdown() for consistency.
	 *
	 * @param string|mixed $current_type The current comment item type slug.
	 * @return void
	 */
	protected function review_type_dropdown( $current_type ) : void {
		/**
		 * Sets the possible options used in the Product Reviews List Table's filter-by-review-type
		 * selector.
		 *
		 * @since 7.0.0
		 *
		 * @param array Map of possible review types.
		 */
		$item_types = apply_filters(
			'woocommerce_product_reviews_list_table_item_types',
			array(
				'all'     => __( 'All types', 'woocommerce' ),
				'comment' => __( 'Replies', 'woocommerce' ),
				'review'  => __( 'Reviews', 'woocommerce' ),
			)
		);

		?>
		<label class="screen-reader-text" for="filter-by-review-type"><?php esc_html_e( 'Filter by review type', 'woocommerce' ); ?></label>
		<select id="filter-by-review-type" name="review_type">
			<?php foreach ( $item_types as $type => $label ) : ?>
				<option value="<?php echo esc_attr( $type ); ?>" <?php selected( $type, $current_type ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Displays a review rating drop-down for filtering reviews in the Product Reviews list table.
	 *
	 * @param int|string|mixed $current_rating Rating to display reviews for.
	 * @return void
	 */
	public function review_rating_dropdown( $current_rating ) : void {

		$rating_options = [
			'0' => __( 'All ratings', 'woocommerce' ),
			'1' => '&#9733;',
			'2' => '&#9733;&#9733;',
			'3' => '&#9733;&#9733;&#9733;',
			'4' => '&#9733;&#9733;&#9733;&#9733;',
			'5' => '&#9733;&#9733;&#9733;&#9733;&#9733;',
		];

		?>
		<label class="screen-reader-text" for="filter-by-review-rating"><?php esc_html_e( 'Filter by review rating', 'woocommerce' ); ?></label>
		<select id="filter-by-review-rating" name="review_rating">
			<?php foreach ( $rating_options as $rating => $label ) : ?>
				<?php

				$title = 0 === (int) $rating
					? $label
					: sprintf(
						/* translators: %s: Star rating (1-5). */
						__( '%s-star rating', 'woocommerce' ),
						$rating
					);

				?>
				<option value="<?php echo esc_attr( $rating ); ?>" <?php selected( $rating, (string) $current_rating ); ?> title="<?php echo esc_attr( $title ); ?>"><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Displays a product search input for filtering reviews by product in the Product Reviews list table.
	 *
	 * @param WC_Product|null $current_product The current product (or null when displaying all reviews).
	 * @return void
	 */
	protected function product_search( ?WC_Product $current_product ) : void {
		?>
		<label class="screen-reader-text" for="filter-by-product"><?php esc_html_e( 'Filter by product', 'woocommerce' ); ?></label>
		<select
			id="filter-by-product"
			class="wc-product-search"
			name="product_id"
			style="width: 200px;"
			data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>"
			data-action="woocommerce_json_search_products"
			data-allow_clear="true">
			<?php if ( $current_product instanceof WC_Product ) : ?>
				<option value="<?php echo esc_attr( $current_product->get_id() ); ?>" selected="selected"><?php echo esc_html( $current_product->get_formatted_name() ); ?></option>
			<?php endif; ?>
		</select>
		<?php
	}

	/**
	 * Displays a review count bubble.
	 *
	 * Based on {@see WP_List_Table::comments_bubble()}, but overridden, so we can customize the URL and text output.
	 *
	 * @param int|mixed $post_id          The product ID.
	 * @param int|mixed $pending_comments Number of pending reviews.
	 *
	 * @return void
	 */
	protected function comments_bubble( $post_id, $pending_comments ) : void {
		$approved_review_count = get_comments_number();

		$approved_reviews_number = number_format_i18n( $approved_review_count );
		$pending_reviews_number  = number_format_i18n( $pending_comments );

		$approved_only_phrase = sprintf(
			/* translators: %s: Number of reviews. */
			_n( '%s review', '%s reviews', $approved_review_count, 'woocommerce' ),
			$approved_reviews_number
		);

		$approved_phrase = sprintf(
			/* translators: %s: Number of reviews. */
			_n( '%s approved review', '%s approved reviews', $approved_review_count, 'woocommerce' ),
			$approved_reviews_number
		);

		$pending_phrase = sprintf(
			/* translators: %s: Number of reviews. */
			_n( '%s pending review', '%s pending reviews', $pending_comments, 'woocommerce' ),
			$pending_reviews_number
		);

		if ( ! $approved_review_count && ! $pending_comments ) {
			// No reviews at all.
			printf(
				'<span aria-hidden="true">&#8212;</span><span class="screen-reader-text">%s</span>',
				esc_html__( 'No reviews', 'woocommerce' )
			);
		} elseif ( $approved_review_count && 'trash' === get_post_status( $post_id ) ) {
			// Don't link the comment bubble for a trashed product.
			printf(
				'<span class="post-com-count post-com-count-approved"><span class="comment-count-approved" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></span>',
				esc_html( $approved_reviews_number ),
				$pending_comments ? esc_html( $approved_phrase ) : esc_html( $approved_only_phrase )
			);
		} elseif ( $approved_review_count ) {
			// Link the comment bubble to approved reviews.
			printf(
				'<a href="%s" class="post-com-count post-com-count-approved"><span class="comment-count-approved" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></a>',
				esc_url(
					add_query_arg(
						[
							'product_id'     => urlencode( $post_id ),
							'comment_status' => 'approved',
						],
						Reviews::get_reviews_page_url()
					)
				),
				esc_html( $approved_reviews_number ),
				$pending_comments ? esc_html( $approved_phrase ) : esc_html( $approved_only_phrase )
			);
		} else {
			// Don't link the comment bubble when there are no approved reviews.
			printf(
				'<span class="post-com-count post-com-count-no-comments"><span class="comment-count comment-count-no-comments" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></span>',
				esc_html( $approved_reviews_number ),
				$pending_comments ? esc_html__( 'No approved reviews', 'woocommerce' ) : esc_html__( 'No reviews', 'woocommerce' )
			);
		}

		if ( $pending_comments ) {
			printf(
				'<a href="%s" class="post-com-count post-com-count-pending"><span class="comment-count-pending" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></a>',
				esc_url(
					add_query_arg(
						[
							'product_id'     => urlencode( $post_id ),
							'comment_status' => 'moderated',
						],
						Reviews::get_reviews_page_url()
					)
				),
				esc_html( $pending_reviews_number ),
				esc_html( $pending_phrase )
			);
		} else {
			printf(
				'<span class="post-com-count post-com-count-pending post-com-count-no-pending"><span class="comment-count comment-count-no-pending" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></span>',
				esc_html( $pending_reviews_number ),
				$approved_review_count ? esc_html__( 'No pending reviews', 'woocommerce' ) : esc_html__( 'No reviews', 'woocommerce' )
			);
		}
	}

}
