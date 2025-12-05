<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * Determines the recommended replacement variables based on the context.
 */
class WPSEO_Admin_Recommended_Replace_Vars {

	/**
	 * The recommended replacement variables.
	 *
	 * @var array
	 */
	protected $recommended_replace_vars = [
		// Posts types.
		'page'                     => [ 'sitename', 'title', 'sep', 'primary_category' ],
		'post'                     => [ 'sitename', 'title', 'sep', 'primary_category' ],
		// Homepage.
		'homepage'                 => [ 'sitename', 'sitedesc', 'sep' ],
		// Custom post type.
		'custom_post_type'         => [ 'sitename', 'title', 'sep' ],

		// Taxonomies.
		'category'                 => [ 'sitename', 'term_title', 'sep', 'term_hierarchy' ],
		'post_tag'                 => [ 'sitename', 'term_title', 'sep' ],
		'post_format'              => [ 'sitename', 'term_title', 'sep', 'page' ],

		// Custom taxonomy.
		'term-in-custom-taxonomy'  => [ 'sitename', 'term_title', 'sep', 'term_hierarchy' ],

		// Settings - archive pages.
		'author_archive'           => [ 'sitename', 'title', 'sep', 'page' ],
		'date_archive'             => [ 'sitename', 'sep', 'date', 'page' ],
		'custom-post-type_archive' => [ 'sitename', 'title', 'sep', 'page' ],

		// Settings - special pages.
		'search'                   => [ 'sitename', 'searchphrase', 'sep', 'page' ],
		'404'                      => [ 'sitename', 'sep' ],
	];

	/**
	 * Determines the page type of the current term.
	 *
	 * @param string $taxonomy The taxonomy name.
	 *
	 * @return string The page type.
	 */
	public function determine_for_term( $taxonomy ) {
		$recommended_replace_vars = $this->get_recommended_replacevars();
		if ( array_key_exists( $taxonomy, $recommended_replace_vars ) ) {
			return $taxonomy;
		}

		return 'term-in-custom-taxonomy';
	}

	/**
	 * Determines the page type of the current post.
	 *
	 * @param WP_Post $post A WordPress post instance.
	 *
	 * @return string The page type.
	 */
	public function determine_for_post( $post ) {
		if ( $post instanceof WP_Post === false ) {
			return 'post';
		}

		if ( $post->post_type === 'page' && $this->is_homepage( $post ) ) {
			return 'homepage';
		}

		$recommended_replace_vars = $this->get_recommended_replacevars();
		if ( array_key_exists( $post->post_type, $recommended_replace_vars ) ) {
			return $post->post_type;
		}

		return 'custom_post_type';
	}

	/**
	 * Determines the page type for a post type.
	 *
	 * @param string $post_type The name of the post_type.
	 * @param string $fallback  The page type to fall back to.
	 *
	 * @return string The page type.
	 */
	public function determine_for_post_type( $post_type, $fallback = 'custom_post_type' ) {
		$page_type                   = $post_type;
		$recommended_replace_vars    = $this->get_recommended_replacevars();
		$has_recommended_replacevars = $this->has_recommended_replace_vars( $recommended_replace_vars, $page_type );

		if ( ! $has_recommended_replacevars ) {
			return $fallback;
		}

		return $page_type;
	}

	/**
	 * Determines the page type for an archive page.
	 *
	 * @param string $name     The name of the archive.
	 * @param string $fallback The page type to fall back to.
	 *
	 * @return string The page type.
	 */
	public function determine_for_archive( $name, $fallback = 'custom-post-type_archive' ) {
		$page_type                   = $name . '_archive';
		$recommended_replace_vars    = $this->get_recommended_replacevars();
		$has_recommended_replacevars = $this->has_recommended_replace_vars( $recommended_replace_vars, $page_type );

		if ( ! $has_recommended_replacevars ) {
			return $fallback;
		}

		return $page_type;
	}

	/**
	 * Retrieves the recommended replacement variables for the given page type.
	 *
	 * @param string $page_type The page type.
	 *
	 * @return array The recommended replacement variables.
	 */
	public function get_recommended_replacevars_for( $page_type ) {
		$recommended_replace_vars     = $this->get_recommended_replacevars();
		$has_recommended_replace_vars = $this->has_recommended_replace_vars( $recommended_replace_vars, $page_type );

		if ( ! $has_recommended_replace_vars ) {
			return [];
		}

		return $recommended_replace_vars[ $page_type ];
	}

	/**
	 * Retrieves the recommended replacement variables.
	 *
	 * @return array The recommended replacement variables.
	 */
	public function get_recommended_replacevars() {
		/**
		 * Filter: Adds the possibility to add extra recommended replacement variables.
		 *
		 * @param array $additional_replace_vars Empty array to add the replacevars to.
		 */
		$recommended_replace_vars = apply_filters( 'wpseo_recommended_replace_vars', $this->recommended_replace_vars );

		if ( ! is_array( $recommended_replace_vars ) ) {
			return $this->recommended_replace_vars;
		}

		return $recommended_replace_vars;
	}

	/**
	 * Returns whether the given page type has recommended replace vars.
	 *
	 * @param array  $recommended_replace_vars The recommended replace vars
	 *                                         to check in.
	 * @param string $page_type                The page type to check.
	 *
	 * @return bool True if there are associated recommended replace vars.
	 */
	private function has_recommended_replace_vars( $recommended_replace_vars, $page_type ) {
		if ( ! isset( $recommended_replace_vars[ $page_type ] ) ) {
			return false;
		}

		if ( ! is_array( $recommended_replace_vars[ $page_type ] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Determines whether or not a post is the homepage.
	 *
	 * @param WP_Post $post The WordPress global post object.
	 *
	 * @return bool True if the given post is the homepage.
	 */
	private function is_homepage( $post ) {
		if ( $post instanceof WP_Post === false ) {
			return false;
		}

		/*
		 * The page on front returns a string with normal WordPress interaction, while the post ID is an int.
		 * This way we make sure we always compare strings.
		 */
		$post_id       = (int) $post->ID;
		$page_on_front = (int) get_option( 'page_on_front' );

		return get_option( 'show_on_front' ) === 'page' && $page_on_front === $post_id;
	}
}
