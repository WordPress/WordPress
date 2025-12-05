<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * Registers the filter for filtering posts by cornerstone content.
 */
class WPSEO_Cornerstone_Filter extends WPSEO_Abstract_Post_Filter {

	/**
	 * Name of the meta value.
	 *
	 * @var string
	 */
	public const META_NAME = 'is_cornerstone';

	/**
	 * Registers the hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		parent::register_hooks();

		add_filter( 'wpseo_cornerstone_post_types', [ 'WPSEO_Post_Type', 'filter_attachment_post_type' ] );
		add_filter( 'wpseo_cornerstone_post_types', [ $this, 'filter_metabox_disabled' ] );
	}

	/**
	 * Returns the query value this filter uses.
	 *
	 * @return string The query value this filter uses.
	 */
	public function get_query_val() {
		return 'cornerstone';
	}

	/**
	 * Modify the query based on the seo_filter variable in $_GET.
	 *
	 * @param string $where Query variables.
	 *
	 * @return string The modified query.
	 */
	public function filter_posts( $where ) {
		if ( $this->is_filter_active() ) {
			global $wpdb;

			$where .= $wpdb->prepare(
				" AND {$wpdb->posts}.ID IN ( SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = '1' ) ",
				WPSEO_Meta::$meta_prefix . self::META_NAME
			);
		}

		return $where;
	}

	/**
	 * Filters the post types that have the metabox disabled.
	 *
	 * @param array $post_types The post types to filter.
	 *
	 * @return array The filtered post types.
	 */
	public function filter_metabox_disabled( $post_types ) {
		$filtered_post_types = [];
		foreach ( $post_types as $post_type_key => $post_type ) {
			if ( ! WPSEO_Post_Type::has_metabox_enabled( $post_type_key ) ) {
				continue;
			}

			$filtered_post_types[ $post_type_key ] = $post_type;
		}

		return $filtered_post_types;
	}

	/**
	 * Returns the label for this filter.
	 *
	 * @return string The label for this filter.
	 */
	protected function get_label() {
		return __( 'Cornerstone content', 'wordpress-seo' );
	}

	/**
	 * Returns a text explaining this filter.
	 *
	 * @return string|null The explanation.
	 */
	protected function get_explanation() {
		$post_type_object = get_post_type_object( $this->get_current_post_type() );

		if ( $post_type_object === null ) {
			return null;
		}

		return sprintf(
			/* translators: %1$s expands to the posttype label, %2$s expands anchor to blog post about cornerstone content, %3$s expands to </a> */
			__( 'Mark the most important %1$s as \'cornerstone content\' to improve your site structure. %2$sLearn more about cornerstone content%3$s.', 'wordpress-seo' ),
			strtolower( $post_type_object->labels->name ),
			'<a href="' . WPSEO_Shortlinker::get( 'https://yoa.st/1i9' ) . '" target="_blank">',
			'</a>'
		);
	}

	/**
	 * Returns the total amount of articles marked as cornerstone content.
	 *
	 * @return int
	 */
	protected function get_post_total() {
		global $wpdb;

		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT( 1 )
					FROM {$wpdb->postmeta}
					WHERE post_id IN( SELECT ID FROM {$wpdb->posts} WHERE post_type = %s ) AND
					meta_key = %s AND meta_value = '1'
				",
				$this->get_current_post_type(),
				WPSEO_Meta::$meta_prefix . self::META_NAME
			)
		);
	}

	/**
	 * Returns the post types to which this filter should be added.
	 *
	 * @return array The post types to which this filter should be added.
	 */
	protected function get_post_types() {
		/**
		 * Filter: 'wpseo_cornerstone_post_types' - Filters post types to exclude the cornerstone feature for.
		 *
		 * @param array $post_types The accessible post types to filter.
		 */
		$post_types = apply_filters( 'wpseo_cornerstone_post_types', parent::get_post_types() );
		if ( ! is_array( $post_types ) ) {
			return [];
		}

		return $post_types;
	}
}
