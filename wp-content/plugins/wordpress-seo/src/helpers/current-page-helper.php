<?php

namespace Yoast\WP\SEO\Helpers;

use WP_Post;
use Yoast\WP\SEO\Wrappers\WP_Query_Wrapper;

/**
 * A helper object for WordPress posts.
 */
class Current_Page_Helper {

	/**
	 * The WP Query wrapper.
	 *
	 * @var WP_Query_Wrapper
	 */
	private $wp_query_wrapper;

	/**
	 * Current_Page_Helper constructor.
	 *
	 * @codeCoverageIgnore It only sets dependencies.
	 *
	 * @param WP_Query_Wrapper $wp_query_wrapper The wrapper for WP_Query.
	 */
	public function __construct( WP_Query_Wrapper $wp_query_wrapper ) {
		$this->wp_query_wrapper = $wp_query_wrapper;
	}

	/**
	 * Returns the page type for the current request.
	 *
	 * @codeCoverageIgnore It just depends on other functions for its result.
	 *
	 * @return string Page type.
	 */
	public function get_page_type() {
		switch ( true ) {
			case $this->is_search_result():
				return 'Search_Result_Page';
			case $this->is_static_posts_page():
				return 'Static_Posts_Page';
			case $this->is_home_static_page():
				return 'Static_Home_Page';
			case $this->is_home_posts_page():
				return 'Home_Page';
			case $this->is_simple_page():
				return 'Post_Type';
			case $this->is_post_type_archive():
				return 'Post_Type_Archive';
			case $this->is_term_archive():
				return 'Term_Archive';
			case $this->is_author_archive():
				return 'Author_Archive';
			case $this->is_date_archive():
				return 'Date_Archive';
			case $this->is_404():
				return 'Error_Page';
		}

		return 'Fallback';
	}

	/**
	 * Checks if the currently opened page is a simple page.
	 *
	 * @return bool Whether the currently opened page is a simple page.
	 */
	public function is_simple_page() {
		return $this->get_simple_page_id() > 0;
	}

	/**
	 * Returns the id of the currently opened page.
	 *
	 * @return int The id of the currently opened page.
	 */
	public function get_simple_page_id() {
		if ( \is_singular() ) {
			return \get_the_ID();
		}

		if ( $this->is_posts_page() ) {
			return \get_option( 'page_for_posts' );
		}

		/**
		 * Filter: Allow changing the default page id.
		 *
		 * @param int $page_id The default page id.
		 */
		return \apply_filters( 'wpseo_frontend_page_type_simple_page_id', 0 );
	}

	/**
	 * Returns the id of the currently opened author archive.
	 *
	 * @codeCoverageIgnore It wraps WordPress functionality.
	 *
	 * @return int The id of the currently opened author archive.
	 */
	public function get_author_id() {
		$wp_query = $this->wp_query_wrapper->get_main_query();

		return $wp_query->get( 'author' );
	}

	/**
	 * Returns the id of the front page.
	 *
	 * @return int The id of the front page. 0 if the front page is not a static page.
	 */
	public function get_front_page_id() {
		if ( \get_option( 'show_on_front' ) !== 'page' ) {
			return 0;
		}

		return (int) \get_option( 'page_on_front' );
	}

	/**
	 * Returns the id of the currently opened term archive.
	 *
	 * @return int The id of the currently opened term archive.
	 */
	public function get_term_id() {
		$wp_query = $this->wp_query_wrapper->get_main_query();

		if ( $wp_query->is_tax() || $wp_query->is_tag() || $wp_query->is_category() ) {
			$queried_object = $wp_query->get_queried_object();
			if ( $queried_object && ! \is_wp_error( $queried_object ) ) {
				return $queried_object->term_id;
			}
		}

		return 0;
	}

	/**
	 * Returns the post type of the main query.
	 *
	 * @return string The post type of the main query.
	 */
	public function get_queried_post_type() {
		$wp_query  = $this->wp_query_wrapper->get_main_query();
		$post_type = $wp_query->get( 'post_type' );
		if ( \is_array( $post_type ) ) {
			$post_type = \reset( $post_type );
		}

		return $post_type;
	}

	/**
	 * Returns the permalink of the currently opened date archive.
	 * If the permalink was cached, it returns this permalink.
	 * If not, we call another function to get the permalink through wp_query.
	 *
	 * @return string The permalink of the currently opened date archive.
	 */
	public function get_date_archive_permalink() {
		static $date_archive_permalink;

		if ( isset( $date_archive_permalink ) ) {
			return $date_archive_permalink;
		}

		$date_archive_permalink = $this->get_non_cached_date_archive_permalink();

		return $date_archive_permalink;
	}

	/**
	 * Determine whether this is the homepage and shows posts.
	 *
	 * @return bool Whether or not the current page is the homepage that displays posts.
	 */
	public function is_home_posts_page() {
		$wp_query = $this->wp_query_wrapper->get_main_query();

		if ( ! $wp_query->is_home() ) {
			return false;
		}

		/*
		 * Whether the static page's `Homepage` option is actually not set to a page.
		 * Otherwise WordPress proceeds to handle the homepage as a `Your latest posts` page.
		 */
		if ( (int) \get_option( 'page_on_front' ) === 0 ) {
			return true;
		}

		return \get_option( 'show_on_front' ) === 'posts';
	}

	/**
	 * Determine whether this is the static frontpage.
	 *
	 * @return bool Whether or not the current page is a static frontpage.
	 */
	public function is_home_static_page() {
		$wp_query = $this->wp_query_wrapper->get_main_query();

		if ( ! $wp_query->is_front_page() ) {
			return false;
		}

		if ( \get_option( 'show_on_front' ) !== 'page' ) {
			return false;
		}

		return $wp_query->is_page( \get_option( 'page_on_front' ) );
	}

	/**
	 * Determine whether this is the static posts page.
	 *
	 * @return bool Whether or not the current page is a static posts page.
	 */
	public function is_static_posts_page() {
		$wp_query       = $this->wp_query_wrapper->get_main_query();
		$queried_object = $wp_query->get_queried_object();

		return (
			$wp_query->is_posts_page
			&& \is_a( $queried_object, WP_Post::class )
			&& $queried_object->post_type === 'page'
		);
	}

	/**
	 * Determine whether this is the statically set posts page, when it's not the frontpage.
	 *
	 * @return bool Whether or not it's a non-frontpage, statically set posts page.
	 */
	public function is_posts_page() {
		$wp_query = $this->wp_query_wrapper->get_main_query();

		if ( ! $wp_query->is_home() ) {
			return false;
		}

		return \get_option( 'show_on_front' ) === 'page';
	}

	/**
	 * Determine whether this is a post type archive.
	 *
	 * @codeCoverageIgnore It wraps WordPress functionality.
	 *
	 * @return bool Whether nor not the current page is a post type archive.
	 */
	public function is_post_type_archive() {
		$wp_query = $this->wp_query_wrapper->get_main_query();

		return $wp_query->is_post_type_archive();
	}

	/**
	 * Determine whether this is a term archive.
	 *
	 * @codeCoverageIgnore It wraps WordPress functionality.
	 *
	 * @return bool Whether nor not the current page is a term archive.
	 */
	public function is_term_archive() {
		$wp_query = $this->wp_query_wrapper->get_main_query();

		return $wp_query->is_tax || $wp_query->is_tag || $wp_query->is_category;
	}

	/**
	 * Determine whether this is an attachment page.
	 *
	 * @codeCoverageIgnore It wraps WordPress functionality.
	 *
	 * @return bool Whether nor not the current page is an attachment page.
	 */
	public function is_attachment() {
		$wp_query = $this->wp_query_wrapper->get_main_query();

		return $wp_query->is_attachment;
	}

	/**
	 * Determine whether this is an author archive.
	 *
	 * @codeCoverageIgnore It wraps WordPress functionality.
	 *
	 * @return bool Whether nor not the current page is an author archive.
	 */
	public function is_author_archive() {
		$wp_query = $this->wp_query_wrapper->get_main_query();

		return $wp_query->is_author();
	}

	/**
	 * Determine whether this is an date archive.
	 *
	 * @codeCoverageIgnore It wraps WordPress functionality.
	 *
	 * @return bool Whether nor not the current page is an date archive.
	 */
	public function is_date_archive() {
		$wp_query = $this->wp_query_wrapper->get_main_query();

		return $wp_query->is_date();
	}

	/**
	 * Determine whether this is a search result.
	 *
	 * @codeCoverageIgnore It wraps WordPress functionality.
	 *
	 * @return bool Whether nor not the current page is a search result.
	 */
	public function is_search_result() {
		$wp_query = $this->wp_query_wrapper->get_main_query();

		return $wp_query->is_search();
	}

	/**
	 * Determine whether this is a 404 page.
	 *
	 * @codeCoverageIgnore It wraps WordPress functionality.
	 *
	 * @return bool Whether nor not the current page is a 404 page.
	 */
	public function is_404() {
		$wp_query = $this->wp_query_wrapper->get_main_query();

		return $wp_query->is_404();
	}

	/**
	 * Checks if the current page is the post format archive.
	 *
	 * @codeCoverageIgnore It wraps WordPress functionality.
	 *
	 * @return bool Whether or not the current page is the post format archive.
	 */
	public function is_post_format_archive() {
		$wp_query = $this->wp_query_wrapper->get_main_query();

		return $wp_query->is_tax( 'post_format' );
	}

	/**
	 * Determine whether this page is an taxonomy archive page for multiple terms (url: /term-1,term2/).
	 *
	 * @return bool Whether or not the current page is an archive page for multiple terms.
	 */
	public function is_multiple_terms_page() {
		if ( ! $this->is_term_archive() ) {
			return false;
		}

		return $this->count_queried_terms() > 1;
	}

	/**
	 * Checks whether the current page is paged.
	 *
	 * @codeCoverageIgnore This method only calls a WordPress function.
	 *
	 * @return bool Whether the current page is paged.
	 */
	public function is_paged() {
		return \is_paged();
	}

	/**
	 * Checks if the current page is the front page.
	 *
	 * @codeCoverageIgnore It wraps WordPress functionality.
	 *
	 * @return bool Whether or not the current page is the front page.
	 */
	public function is_front_page() {
		$wp_query = $this->wp_query_wrapper->get_main_query();

		return $wp_query->is_front_page();
	}

	/**
	 * Retrieves the current admin page.
	 *
	 * @codeCoverageIgnore It only wraps a global WordPress variable.
	 *
	 * @return string The current page.
	 */
	public function get_current_admin_page() {
		global $pagenow;

		return $pagenow;
	}

	/**
	 * Check if the current opened page is a Yoast SEO page.
	 *
	 * @return bool True when current page is a yoast seo plugin page.
	 */
	public function is_yoast_seo_page() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( isset( $_GET['page'] ) && \is_string( $_GET['page'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are not processing form information, We are only using the variable in the strpos function.
			$current_page = \wp_unslash( $_GET['page'] );
			return \strpos( $current_page, 'wpseo_' ) === 0;
		}
		return false;
	}

	/**
	 * Returns the current Yoast SEO page.
	 * (E.g. the `page` query variable in the URL).
	 *
	 * @return string The current Yoast SEO page.
	 */
	public function get_current_yoast_seo_page() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( isset( $_GET['page'] ) && \is_string( $_GET['page'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
			return \sanitize_text_field( \wp_unslash( $_GET['page'] ) );
		}

		return '';
	}

	/**
	 * Checks if the current global post is the privacy policy page.
	 *
	 * @return bool current global post is set as privacy page
	 */
	public function current_post_is_privacy_policy() {
		global $post;

		if ( ! isset( $post->ID ) ) {
			return false;
		}

		return \intval( $post->ID ) === \intval( \get_option( 'wp_page_for_privacy_policy', false ) );
	}

	/**
	 * Returns the permalink of the currently opened date archive.
	 *
	 * @return string The permalink of the currently opened date archive.
	 */
	protected function get_non_cached_date_archive_permalink() {
		$date_archive_permalink = '';
		$wp_query               = $this->wp_query_wrapper->get_main_query();

		if ( $wp_query->is_day() ) {
			$date_archive_permalink = \get_day_link( $wp_query->get( 'year' ), $wp_query->get( 'monthnum' ), $wp_query->get( 'day' ) );
		}
		if ( $wp_query->is_month() ) {
			$date_archive_permalink = \get_month_link( $wp_query->get( 'year' ), $wp_query->get( 'monthnum' ) );
		}
		if ( $wp_query->is_year() ) {
			$date_archive_permalink = \get_year_link( $wp_query->get( 'year' ) );
		}

		return $date_archive_permalink;
	}

	/**
	 * Counts the total amount of queried terms.
	 *
	 * @codeCoverageIgnore This relies too much on WordPress dependencies.
	 *
	 * @return int The amoumt of queried terms.
	 */
	protected function count_queried_terms() {
		$wp_query = $this->wp_query_wrapper->get_main_query();
		$term     = $wp_query->get_queried_object();

		$queried_terms = $wp_query->tax_query->queried_terms;
		if ( $term === null || empty( $queried_terms[ $term->taxonomy ]['terms'] ) ) {
			return 0;
		}

		return \count( $queried_terms[ $term->taxonomy ]['terms'] );
	}

	/**
	 * Retrieves the current post id.
	 * Returns 0 if no post id is found.
	 *
	 * @return int The post id.
	 */
	public function get_current_post_id(): int {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are not processing form information, We are casting to an integer.
		if ( isset( $_GET['post'] ) && \is_string( $_GET['post'] ) && (int) \wp_unslash( $_GET['post'] ) > 0 ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are not processing form information, We are casting to an integer, also this is a helper function.
			return (int) \wp_unslash( $_GET['post'] );
		}
		return 0;
	}

	/**
	 * Retrieves the current post type.
	 *
	 * @return string The post type.
	 */
	public function get_current_post_type(): string {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( isset( $_GET['post_type'] ) && \is_string( $_GET['post_type'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
			return \sanitize_text_field( \wp_unslash( $_GET['post_type'] ) );
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Reason: should be done outside the helper function.
		if ( isset( $_POST['post_type'] ) && \is_string( $_POST['post_type'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Reason: should be done outside the helper function.
			return \sanitize_text_field( \wp_unslash( $_POST['post_type'] ) );
		}

		$post_id = $this->get_current_post_id();

		if ( $post_id ) {
			return \get_post_type( $post_id );
		}

		return 'post';
	}

	/**
	 * Retrieves the current taxonomy.
	 *
	 * @return string The taxonomy.
	 */
	public function get_current_taxonomy(): string {
		if ( ! isset( $_SERVER['REQUEST_METHOD'] ) || ! \in_array( $_SERVER['REQUEST_METHOD'], [ 'GET', 'POST' ], true ) ) {
			return '';
		}

		// phpcs:ignore WordPress.Security.NonceVerification -- Reason: We are not processing form information.
		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Reason: should be done outside the helper function.
			if ( isset( $_POST['taxonomy'] ) && \is_string( $_POST['taxonomy'] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Reason: should be done outside the helper function.
				return \sanitize_text_field( \wp_unslash( $_POST['taxonomy'] ) );
			}
			return '';
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( isset( $_GET['taxonomy'] ) && \is_string( $_GET['taxonomy'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
			return \sanitize_text_field( \wp_unslash( $_GET['taxonomy'] ) );
		}

		return '';
	}
}
