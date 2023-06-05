<?php
/**
 * Products > Reviews
 */

namespace Automattic\WooCommerce\Internal\Admin\ProductReviews;

use Automattic\WooCommerce\Internal\Traits\AccessiblePrivateMethods;
use WP_Ajax_Response;
use WP_Comment;
use WP_Screen;

/**
 * Handles backend logic for the Reviews component.
 */
class Reviews {

	use AccessiblePrivateMethods;

	/**
	 * Admin page identifier.
	 */
	const MENU_SLUG = 'product-reviews';

	/**
	 * Reviews page hook name.
	 *
	 * @var string|null
	 */
	protected $reviews_page_hook = null;

	/**
	 * Reviews list table instance.
	 *
	 * @var ReviewsListTable|null
	 */
	protected $reviews_list_table;

	/**
	 * Constructor.
	 */
	public function __construct() {

		self::add_action( 'admin_menu', [ $this, 'add_reviews_page' ] );
		self::add_action( 'admin_enqueue_scripts', [ $this, 'load_javascript' ] );

		// These ajax callbacks need a low priority to ensure they run before their WordPress core counterparts.
		self::add_action( 'wp_ajax_edit-comment', [ $this, 'handle_edit_review' ], -1 );
		self::add_action( 'wp_ajax_replyto-comment', [ $this, 'handle_reply_to_review' ], -1 );

		self::add_filter( 'parent_file', [ $this, 'edit_review_parent_file' ] );
		self::add_filter( 'gettext', [ $this, 'edit_comments_screen_text' ], 10, 2 );
		self::add_action( 'admin_notices', [ $this, 'display_notices' ] );
	}

	/**
	 * Gets the required capability to access the reviews page and manage product reviews.
	 *
	 * @param string $context The context for which the capability is needed (e.g. `view` or `moderate`).
	 * @return string
	 */
	public static function get_capability( string $context = 'view' ) : string {

		/**
		 * Filters whether the current user can manage product reviews.
		 *
		 * This is aligned to {@see \wc_rest_check_product_reviews_permissions()}
		 *
		 * @since 6.7.0
		 *
		 * @param string $capability The capability (defaults to `moderate_comments` for viewing and `edit_products` for editing).
		 * @param string $context    The context for which the capability is needed.
		 */
		return (string) apply_filters( 'woocommerce_product_reviews_page_capability', $context === 'view' ? 'moderate_comments' : 'edit_products', $context );
	}

	/**
	 * Registers the Product Reviews submenu page.
	 *
	 * @return void
	 */
	private function add_reviews_page() : void {

		$this->reviews_page_hook = add_submenu_page(
			'edit.php?post_type=product',
			__( 'Reviews', 'woocommerce' ),
			__( 'Reviews', 'woocommerce' ) . $this->get_pending_count_bubble(),
			static::get_capability(),
			static::MENU_SLUG,
			[ $this, 'render_reviews_list_table' ]
		);

		self::add_action( "load-{$this->reviews_page_hook}", array( $this, 'load_reviews_screen' ) );
	}

	/**
	 * Retrieves the URL to the product reviews page.
	 *
	 * @return string
	 */
	public static function get_reviews_page_url() : string {
		return add_query_arg(
			[
				'post_type' => 'product',
				'page'      => static::MENU_SLUG,
			],
			admin_url( 'edit.php' )
		);
	}

	/**
	 * Determines whether the current page is the reviews page.
	 *
	 * @global WP_Screen $current_screen
	 *
	 * @return bool
	 */
	public function is_reviews_page() : bool {
		global $current_screen;

		return isset( $current_screen->base ) && $current_screen->base === 'product_page_' . static::MENU_SLUG;
	}

	/**
	 * Loads the JavaScript required for inline replies and quick edit.
	 *
	 * @return void
	 */
	private function load_javascript() : void {
		if ( $this->is_reviews_page() ) {
			wp_enqueue_script( 'admin-comments' );
			enqueue_comment_hotkeys_js();
		}
	}

	/**
	 * Determines if the object is a review or a reply to a review.
	 *
	 * @param WP_Comment|mixed $object Object to check.
	 * @return bool
	 */
	protected function is_review_or_reply( $object ) : bool {

		$is_review_or_reply = $object instanceof WP_Comment && in_array( $object->comment_type, [ 'review', 'comment' ], true ) && get_post_type( $object->comment_post_ID ) === 'product';

		/**
		 * Filters whether the object is a review or a reply to a review.
		 *
		 * @since 6.7.0
		 *
		 * @param bool             $is_review_or_reply Whether the object in context is a review or a reply to a review.
		 * @param WP_Comment|mixed $object             The object in context.
		 */
		return (bool) apply_filters( 'woocommerce_product_reviews_is_product_review_or_reply', $is_review_or_reply, $object );
	}

	/**
	 * Ajax callback for editing a review.
	 *
	 * This functionality is taken from {@see wp_ajax_edit_comment()} and is largely copy and pasted. The only thing
	 * we want to change is the review row HTML in the response. WordPress core uses a comment list table and we need
	 * to use our own {@see ReviewsListTable} class to support our custom columns.
	 *
	 * This ajax callback is registered with a lower priority than WordPress core's so that our code can run
	 * first. If the supplied comment ID is not a review or a reply to a review, then we `return` early from this method
	 * to allow the WordPress core callback to take over.
	 *
	 * @return void
	 */
	private function handle_edit_review(): void {
		// Don't interfere with comment functionality relating to the reviews meta box within the product editor.
		if ( sanitize_text_field( wp_unslash( $_POST['mode'] ?? '' ) ) === 'single' ) {
			return;
		}

		check_ajax_referer( 'replyto-comment', '_ajax_nonce-replyto-comment' );

		$comment_id = isset( $_POST['comment_ID'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['comment_ID'] ) ) : 0;

		if ( empty( $comment_id ) || ! current_user_can( 'edit_comment', $comment_id ) ) {
			wp_die( -1 );
		}

		$review = get_comment( $comment_id );

		// Bail silently if this is not a review, or a reply to a review. That allows `wp_ajax_edit_comment()` to handle any further actions.
		if ( ! $this->is_review_or_reply( $review ) ) {
			return;
		}

		if ( empty( $review->comment_ID ) ) {
			wp_die( -1 );
		}

		if ( empty( $_POST['content'] ) ) {
			wp_die( esc_html__( 'Error: Please type your review text.', 'woocommerce' ) );
		}

		if ( isset( $_POST['status'] ) ) {
			$_POST['comment_status'] = sanitize_text_field( wp_unslash( $_POST['status'] ) );
		}

		$updated = edit_comment();
		if ( is_wp_error( $updated ) ) {
			wp_die( esc_html( $updated->get_error_message() ) );
		}

		$position      = isset( $_POST['position'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['position'] ) ) : -1;
		$wp_list_table = $this->make_reviews_list_table();

		ob_start();
		$wp_list_table->single_row( $review );
		$review_list_item = ob_get_clean();

		$x = new WP_Ajax_Response();

		$x->add(
			array(
				'what'     => 'edit_comment',
				'id'       => $review->comment_ID,
				'data'     => $review_list_item,
				'position' => $position,
			)
		);

		$x->send();
	}

	/**
	 * Ajax callback for replying to a review inline.
	 *
	 * This functionality is taken from {@see wp_ajax_replyto_comment()} and is largely copy and pasted. The only thing
	 * we want to change is the review row HTML in the response. WordPress core uses a comment list table and we need
	 * to use our own {@see ReviewsListTable} class to support our custom columns.
	 *
	 * This ajax callback is registered with a lower priority than WordPress core's so that our code can run
	 * first. If the supplied comment ID is not a review or a reply to a review, then we `return` early from this method
	 * to allow the WordPress core callback to take over.
	 *
	 * @return void
	 */
	private function handle_reply_to_review() : void {
		// Don't interfere with comment functionality relating to the reviews meta box within the product editor.
		if ( sanitize_text_field( wp_unslash( $_POST['mode'] ?? '' ) ) === 'single' ) {
			return;
		}

		check_ajax_referer( 'replyto-comment', '_ajax_nonce-replyto-comment' );

		$comment_post_ID = isset( $_POST['comment_post_ID'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['comment_post_ID'] ) ) : 0; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		$post            = get_post( $comment_post_ID ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

		if ( ! $post ) {
			wp_die( -1 );
		}

		// Inline Review replies will use the `detail` mode. If that's not what we have, then let WordPress core take over.
		if ( isset( $_REQUEST['mode'] ) && $_REQUEST['mode'] === 'dashboard' ) {
			return;
		}

		// If this is not a a reply to a review, bail silently to let WordPress core take over.
		if ( get_post_type( $post ) !== 'product' ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $comment_post_ID ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
			wp_die( -1 );
		}

		if ( empty( $post->post_status ) ) {
			wp_die( 1 );
		} elseif ( in_array( $post->post_status, array( 'draft', 'pending', 'trash' ), true ) ) {
			wp_die( esc_html__( 'Error: You can\'t reply to a review on a draft product.', 'woocommerce' ) );
		}

		$user = wp_get_current_user();

		if ( $user->exists() ) {
			$user_ID              = $user->ID;
			$comment_author       = wp_slash( $user->display_name );
			$comment_author_email = wp_slash( $user->user_email );
			$comment_author_url   = wp_slash( $user->user_url );
			// WordPress core already sanitizes `content` during the `pre_comment_content` hook, which is why it's not needed here, {@see wp_filter_comment()} and {@see kses_init_filters()}.
			$comment_content = isset( $_POST['content'] ) ? wp_unslash( $_POST['content'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$comment_type    = isset( $_POST['comment_type'] ) ? sanitize_text_field( wp_unslash( $_POST['comment_type'] ) ) : 'comment';

			if ( current_user_can( 'unfiltered_html' ) ) {
				if ( ! isset( $_POST['_wp_unfiltered_html_comment'] ) ) {
					$_POST['_wp_unfiltered_html_comment'] = '';
				}

				if ( wp_create_nonce( 'unfiltered-html-comment' ) != $_POST['_wp_unfiltered_html_comment'] ) {
					kses_remove_filters(); // Start with a clean slate.
					kses_init_filters();   // Set up the filters.
					remove_filter( 'pre_comment_content', 'wp_filter_post_kses' );
					add_filter( 'pre_comment_content', 'wp_filter_kses' );
				}
			}
		} else {
			wp_die( esc_html__( 'Sorry, you must be logged in to reply to a review.', 'woocommerce' ) );
		}

		if ( $comment_content === '' ) {
			wp_die( esc_html__( 'Error: Please type your reply text.', 'woocommerce' ) );
		}

		$comment_parent = 0;

		if ( isset( $_POST['comment_ID'] ) ) {
			$comment_parent = absint( wp_unslash( $_POST['comment_ID'] ) );
		}

		$comment_auto_approved = false;
		$commentdata           = compact( 'comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'comment_parent', 'user_ID' );

		// Automatically approve parent comment.
		if ( ! empty( $_POST['approve_parent'] ) ) {
			$parent = get_comment( $comment_parent );

			if ( $parent && $parent->comment_approved === '0' && $parent->comment_post_ID === $comment_post_ID ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
				if ( ! current_user_can( 'edit_comment', $parent->comment_ID ) ) {
					wp_die( -1 );
				}

				if ( wp_set_comment_status( $parent, 'approve' ) ) {
					$comment_auto_approved = true;
				}
			}
		}

		$comment_id = wp_new_comment( $commentdata );

		if ( is_wp_error( $comment_id ) ) {
			wp_die( esc_html( $comment_id->get_error_message() ) );
		}

		$comment = get_comment( $comment_id );

		if ( ! $comment ) {
			wp_die( 1 );
		}

		$position = ( isset( $_POST['position'] ) && (int) $_POST['position'] ) ? (int) $_POST['position'] : '-1';

		ob_start();
		$wp_list_table = $this->make_reviews_list_table();
		$wp_list_table->single_row( $comment );
		$comment_list_item = ob_get_clean();

		$response = array(
			'what'     => 'comment',
			'id'       => $comment->comment_ID,
			'data'     => $comment_list_item,
			'position' => $position,
		);

		$counts                   = wp_count_comments();
		$response['supplemental'] = array(
			'in_moderation'        => $counts->moderated,
			'i18n_comments_text'   => sprintf(
			/* translators: %s: Number of reviews. */
				_n( '%s Review', '%s Reviews', $counts->approved, 'woocommerce' ),
				number_format_i18n( $counts->approved )
			),
			'i18n_moderation_text' => sprintf(
			/* translators: %s: Number of reviews. */
				_n( '%s Review in moderation', '%s Reviews in moderation', $counts->moderated, 'woocommerce' ),
				number_format_i18n( $counts->moderated )
			),
		);

		if ( $comment_auto_approved && isset( $parent ) ) {
			$response['supplemental']['parent_approved'] = $parent->comment_ID;
			$response['supplemental']['parent_post_id']  = $parent->comment_post_ID;
		}

		$x = new WP_Ajax_Response();
		$x->add( $response );
		$x->send();
	}

	/**
	 * Displays notices on the Reviews page.
	 *
	 * @return void
	 */
	protected function display_notices() : void {

		if ( $this->is_reviews_page() ) {
			$this->maybe_display_reviews_bulk_action_notice();
		}
	}

	/**
	 * May display the bulk action admin notice.
	 *
	 * @return void
	 */
	protected function maybe_display_reviews_bulk_action_notice() : void {

		$messages = $this->get_bulk_action_notice_messages();

		echo ! empty( $messages ) ? '<div id="moderated" class="updated"><p>' . implode( "<br/>\n", $messages ) . '</p></div>' : '';  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Gets the applicable bulk action admin notice messages.
	 *
	 * @return array
	 */
	protected function get_bulk_action_notice_messages() : array {

		$approved   = isset( $_REQUEST['approved'] ) ? (int) $_REQUEST['approved'] : 0;
		$unapproved = isset( $_REQUEST['unapproved'] ) ? (int) $_REQUEST['unapproved'] : 0;
		$deleted    = isset( $_REQUEST['deleted'] ) ? (int) $_REQUEST['deleted'] : 0;
		$trashed    = isset( $_REQUEST['trashed'] ) ? (int) $_REQUEST['trashed'] : 0;
		$untrashed  = isset( $_REQUEST['untrashed'] ) ? (int) $_REQUEST['untrashed'] : 0;
		$spammed    = isset( $_REQUEST['spammed'] ) ? (int) $_REQUEST['spammed'] : 0;
		$unspammed  = isset( $_REQUEST['unspammed'] ) ? (int) $_REQUEST['unspammed'] : 0;

		$messages = [];

		if ( $approved > 0 ) {
			/* translators: %s is an integer higher than 0 (1, 2, 3...) */
			$messages[] = sprintf( _n( '%s review approved', '%s reviews approved', $approved, 'woocommerce' ), $approved );
		}

		if ( $unapproved > 0 ) {
			/* translators: %s is an integer higher than 0 (1, 2, 3...) */
			$messages[] = sprintf( _n( '%s review unapproved', '%s reviews unapproved', $unapproved, 'woocommerce' ), $unapproved );
		}

		if ( $spammed > 0 ) {
			$ids = isset( $_REQUEST['ids'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['ids'] ) ) : 0;
			/* translators: %s is an integer higher than 0 (1, 2, 3...) */
			$messages[] = sprintf( _n( '%s review marked as spam.', '%s reviews marked as spam.', $spammed, 'woocommerce' ), $spammed ) . ' <a href="' . esc_url( wp_nonce_url( "edit-comments.php?doaction=undo&action=unspam&ids=$ids", 'bulk-comments' ) ) . '">' . __( 'Undo', 'woocommerce' ) . '</a><br />';
		}

		if ( $unspammed > 0 ) {
			/* translators: %s is an integer higher than 0 (1, 2, 3...) */
			$messages[] = sprintf( _n( '%s review restored from the spam', '%s reviews restored from the spam', $unspammed, 'woocommerce' ), $unspammed );
		}

		if ( $trashed > 0 ) {
			$ids = isset( $_REQUEST['ids'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['ids'] ) ) : 0;
			/* translators: %s is an integer higher than 0 (1, 2, 3...) */
			$messages[] = sprintf( _n( '%s review moved to the Trash.', '%s reviews moved to the Trash.', $trashed, 'woocommerce' ), $trashed ) . ' <a href="' . esc_url( wp_nonce_url( "edit-comments.php?doaction=undo&action=untrash&ids=$ids", 'bulk-comments' ) ) . '">' . __( 'Undo', 'woocommerce' ) . '</a><br />';
		}

		if ( $untrashed > 0 ) {
			/* translators: %s is an integer higher than 0 (1, 2, 3...) */
			$messages[] = sprintf( _n( '%s review restored from the Trash', '%s reviews restored from the Trash', $untrashed, 'woocommerce' ), $untrashed );
		}

		if ( $deleted > 0 ) {
			/* translators: %s is an integer higher than 0 (1, 2, 3...) */
			$messages[] = sprintf( _n( '%s review permanently deleted', '%s reviews permanently deleted', $deleted, 'woocommerce' ), $deleted );
		}

		return $messages;
	}

	/**
	 * Counts the number of pending product reviews/replies, and returns the notification bubble if there's more than zero.
	 *
	 * @return string Empty string if there are no pending reviews, or bubble HTML if there are.
	 */
	protected function get_pending_count_bubble() : string {
		$count = (int) get_comments(
			[
				'type__in'  => [ 'review', 'comment' ],
				'status'    => '0',
				'post_type' => 'product',
				'count'     => true,
			]
		);

		/**
		 * Provides an opportunity to alter the pending comment count used within
		 * the product reviews admin list table.
		 *
		 * @since 7.0.0
		 *
		 * @param array $count Current count of comments pending review.
		 */
		$count = apply_filters( 'woocommerce_product_reviews_pending_count', $count );

		if ( empty( $count ) ) {
			return '';
		}

		return ' <span class="awaiting-mod count-' . esc_attr( $count ) . '"><span class="pending-count">' . esc_html( $count ) . '</span></span>';
	}

	/**
	 * Highlights Product -> Reviews admin menu item when editing a review or a reply to a review.
	 *
	 * @global string $submenu_file
	 *
	 * @param string|mixed $parent_file Parent menu item.
	 * @return string
	 */
	protected function edit_review_parent_file( $parent_file ) {
		global $submenu_file, $current_screen;

		if ( isset( $current_screen->id, $_GET['c'] ) && $current_screen->id === 'comment' ) {

			$comment_id = absint( $_GET['c'] );
			$comment    = get_comment( $comment_id );

			if ( isset( $comment->comment_parent ) && $comment->comment_parent > 0 ) {
				$comment = get_comment( $comment->comment_parent );
			}

			if ( isset( $comment->comment_post_ID ) && get_post_type( $comment->comment_post_ID ) === 'product' ) {
				$parent_file  = 'edit.php?post_type=product';
				$submenu_file = 'product-reviews'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			}
		}

		return $parent_file;
	}

	/**
	 * Replaces Edit/Moderate Comment title/headline with Edit Review, when editing/moderating a review.
	 *
	 * @param  string|mixed $translation Translated text.
	 * @param  string|mixed $text        Text to translate.
	 * @return string|mixed              Translated text.
	 */
	protected function edit_comments_screen_text( $translation, $text ) {
		global $comment;

		// Bail out if not a text we should replace.
		if ( ! in_array( $text, [ 'Edit Comment', 'Moderate Comment' ], true ) ) {
			return $translation;
		}

		// Try to get comment from query params when not in context already.
		if ( ! $comment && isset( $_GET['action'], $_GET['c'] ) && $_GET['action'] === 'editcomment' ) {
			$comment_id = absint( $_GET['c'] );
			$comment    = get_comment( $comment_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		}

		$is_reply = isset( $comment->comment_parent ) && $comment->comment_parent > 0;

		// Only replace the translated text if we are editing a comment left on a product (ie. a review).
		if ( isset( $comment->comment_post_ID ) && get_post_type( $comment->comment_post_ID ) === 'product' ) {
			if ( $text === 'Edit Comment' ) {
				$translation = $is_reply
					? __( 'Edit Review Reply', 'woocommerce' )
					: __( 'Edit Review', 'woocommerce' );
			} elseif ( $text === 'Moderate Comment' ) {
				$translation = $is_reply
					? __( 'Moderate Review Reply', 'woocommerce' )
					: __( 'Moderate Review', 'woocommerce' );
			}
		}

		return $translation;
	}

	/**
	 * Returns a new instance of `ReviewsListTable`, with the screen argument specified.
	 *
	 * @return ReviewsListTable
	 */
	protected function make_reviews_list_table() : ReviewsListTable {
		return new ReviewsListTable( [ 'screen' => $this->reviews_page_hook ? $this->reviews_page_hook : 'product_page_product-reviews' ] );
	}

	/**
	 * Initializes the list table.
	 *
	 * @return void
	 */
	protected function load_reviews_screen() : void {
		$this->reviews_list_table = $this->make_reviews_list_table();
		$this->reviews_list_table->process_bulk_action();
	}

	/**
	 * Renders the Reviews page.
	 *
	 * @return void
	 */
	public function render_reviews_list_table() : void {

		$this->reviews_list_table->prepare_items();

		ob_start();

		?>
		<div class="wrap">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

			<?php $this->reviews_list_table->views(); ?>

			<form id="reviews-filter" method="get">
				<?php $page = isset( $_REQUEST['page'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) : static::MENU_SLUG; ?>

				<input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>" />
				<input type="hidden" name="post_type" value="product" />
				<input type="hidden" name="pagegen_timestamp" value="<?php echo esc_attr( current_time( 'mysql', true ) ); ?>" />

				<?php $this->reviews_list_table->search_box( __( 'Search Reviews', 'woocommerce' ), 'reviews' ); ?>

				<?php $this->reviews_list_table->display(); ?>
			</form>
		</div>
		<?php
		wp_comment_reply( '-1', true, 'detail' );
		wp_comment_trashnotice();

		/**
		 * Filters the contents of the product reviews list table output.
		 *
		 * @since 6.7.0
		 *
		 * @param string           $output             The HTML output of the list table.
		 * @param ReviewsListTable $reviews_list_table The reviews list table instance.
		 */
		echo apply_filters( 'woocommerce_product_reviews_list_table', ob_get_clean(), $this->reviews_list_table ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
