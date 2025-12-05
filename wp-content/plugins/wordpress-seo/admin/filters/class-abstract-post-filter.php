<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Filters
 */

/**
 * Class WPSEO_Abstract_Post_Filter.
 */
abstract class WPSEO_Abstract_Post_Filter implements WPSEO_WordPress_Integration {

	/**
	 * The filter's query argument.
	 *
	 * @var string
	 */
	public const FILTER_QUERY_ARG = 'yoast_filter';

	/**
	 * Modify the query based on the FILTER_QUERY_ARG variable in $_GET.
	 *
	 * @param string $where Query variables.
	 *
	 * @return string The modified query.
	 */
	abstract public function filter_posts( $where );

	/**
	 * Returns the query value this filter uses.
	 *
	 * @return string The query value this filter uses.
	 */
	abstract public function get_query_val();

	/**
	 * Returns the total number of posts that match this filter.
	 *
	 * @return int The total number of posts that match this filter.
	 */
	abstract protected function get_post_total();

	/**
	 * Returns the label for this filter.
	 *
	 * @return string The label for this filter.
	 */
	abstract protected function get_label();

	/**
	 * Registers the hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'admin_init', [ $this, 'add_filter_links' ], 11 );

		add_filter( 'posts_where', [ $this, 'filter_posts' ] );

		if ( $this->is_filter_active() ) {
			add_action( 'restrict_manage_posts', [ $this, 'render_hidden_input' ] );
		}

		if ( $this->is_filter_active() ) {
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_explanation_assets' ] );
		}
	}

	/**
	 * Adds the filter links to the view_edit screens to give the user a filter link.
	 *
	 * @return void
	 */
	public function add_filter_links() {
		foreach ( $this->get_post_types() as $post_type ) {
			add_filter( 'views_edit-' . $post_type, [ $this, 'add_filter_link' ] );
		}
	}

	/**
	 * Enqueues the necessary assets to display a filter explanation.
	 *
	 * @return void
	 */
	public function enqueue_explanation_assets() {
		$explanation = $this->get_explanation();

		if ( $explanation === null ) {
			return;
		}

		$asset_manager = new WPSEO_Admin_Asset_Manager();
		$asset_manager->enqueue_script( 'filter-explanation' );
		$asset_manager->enqueue_style( 'filter-explanation' );
		$asset_manager->localize_script(
			'filter-explanation',
			'yoastFilterExplanation',
			[ 'text' => $explanation ]
		);
	}

	/**
	 * Adds a filter link to the views.
	 *
	 * @param array<string, string> $views Array with the views.
	 *
	 * @return array<string, string> Array of views including the added view.
	 */
	public function add_filter_link( $views ) {
		$views[ 'yoast_' . $this->get_query_val() ] = sprintf(
			'<a href="%1$s"%2$s>%3$s</a> (%4$s)',
			esc_url( $this->get_filter_url() ),
			( $this->is_filter_active() ) ? ' class="current" aria-current="page"' : '',
			$this->get_label(),
			$this->get_post_total()
		);

		return $views;
	}

	/**
	 * Returns a text explaining this filter. Null if no explanation is necessary.
	 *
	 * @return string|null The explanation or null.
	 */
	protected function get_explanation() {
		return null;
	}

	/**
	 * Renders a hidden input to preserve this filter's state when using sub-filters.
	 *
	 * @return void
	 */
	public function render_hidden_input() {
		echo '<input type="hidden" name="' . esc_attr( self::FILTER_QUERY_ARG ) . '" value="' . esc_attr( $this->get_query_val() ) . '">';
	}

	/**
	 * Returns an url to edit.php with post_type and this filter as the query arguments.
	 *
	 * @return string The url to activate this filter.
	 */
	protected function get_filter_url() {
		$query_args = [
			self::FILTER_QUERY_ARG => $this->get_query_val(),
			'post_type'            => $this->get_current_post_type(),
		];

		return add_query_arg( $query_args, 'edit.php' );
	}

	/**
	 * Returns true when the filter is active.
	 *
	 * @return bool Whether the filter is active.
	 */
	protected function is_filter_active() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( isset( $_GET[ self::FILTER_QUERY_ARG ] ) && is_string( $_GET[ self::FILTER_QUERY_ARG ] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
			return sanitize_text_field( wp_unslash( $_GET[ self::FILTER_QUERY_ARG ] ) ) === $this->get_query_val();
		}
		return false;
	}

	/**
	 * Returns the current post type.
	 *
	 * @return string The current post type.
	 */
	protected function get_current_post_type() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( isset( $_GET['post_type'] ) && is_string( $_GET['post_type'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
			$post_type = sanitize_text_field( wp_unslash( $_GET['post_type'] ) );
			if ( ! empty( $post_type ) ) {
				return $post_type;
			}
		}
		return 'post';
	}

	/**
	 * Returns the post types to which this filter should be added.
	 *
	 * @return array The post types to which this filter should be added.
	 */
	protected function get_post_types() {
		return WPSEO_Post_Type::get_accessible_post_types();
	}

	/**
	 * Checks if the post type is supported.
	 *
	 * @param string $post_type Post type to check against.
	 *
	 * @return bool True when it is supported.
	 */
	protected function is_supported_post_type( $post_type ) {
		return in_array( $post_type, $this->get_post_types(), true );
	}
}
