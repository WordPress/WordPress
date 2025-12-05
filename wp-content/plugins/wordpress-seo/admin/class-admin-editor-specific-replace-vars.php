<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * Determines the editor specific replacement variables.
 */
class WPSEO_Admin_Editor_Specific_Replace_Vars {

	/**
	 * Holds the editor specific replacements variables.
	 *
	 * @var array The editor specific replacement variables.
	 */
	protected $replacement_variables = [
		// Posts types.
		'page'                     => [ 'id', 'pt_single', 'pt_plural', 'parent_title' ],
		'post'                     => [ 'id', 'term404', 'pt_single', 'pt_plural' ],
		// Custom post type.
		'custom_post_type'         => [ 'id', 'term404', 'pt_single', 'pt_plural', 'parent_title' ],
		// Settings - archive pages.
		'custom-post-type_archive' => [ 'pt_single', 'pt_plural' ],

		// Taxonomies.
		'category'                 => [ 'term_title', 'term_description', 'category_description', 'parent_title', 'term_hierarchy' ],
		'post_tag'                 => [ 'term_title', 'term_description', 'tag_description' ],
		'post_format'              => [ 'term_title' ],
		// Custom taxonomy.
		'term-in-custom-taxonomy'  => [ 'term_title', 'term_description', 'category_description', 'parent_title', 'term_hierarchy' ],

		// Settings - special pages.
		'search'                   => [ 'searchphrase' ],
	];

	/**
	 * WPSEO_Admin_Editor_Specific_Replace_Vars constructor.
	 */
	public function __construct() {
		$this->add_for_page_types(
			[ 'page', 'post', 'custom_post_type' ],
			WPSEO_Custom_Fields::get_custom_fields()
		);

		$this->add_for_page_types(
			[ 'post', 'term-in-custom-taxonomy' ],
			WPSEO_Custom_Taxonomies::get_custom_taxonomies()
		);
	}

	/**
	 * Retrieves the editor specific replacement variables.
	 *
	 * @return array The editor specific replacement variables.
	 */
	public function get() {
		/**
		 * Filter: Adds the possibility to add extra editor specific replacement variables.
		 *
		 * @param array $replacement_variables Array of editor specific replace vars.
		 */
		$replacement_variables = apply_filters(
			'wpseo_editor_specific_replace_vars',
			$this->replacement_variables
		);

		if ( ! is_array( $replacement_variables ) ) {
			$replacement_variables = $this->replacement_variables;
		}

		return array_filter( $replacement_variables, 'is_array' );
	}

	/**
	 * Retrieves the generic replacement variable names.
	 *
	 * Which are the replacement variables without the editor specific ones.
	 *
	 * @param array $replacement_variables Possibly generic replacement variables.
	 *
	 * @return array The generic replacement variable names.
	 */
	public function get_generic( $replacement_variables ) {
		$shared_variables = array_diff(
			$this->extract_names( $replacement_variables ),
			$this->get_unique_replacement_variables()
		);

		return array_values( $shared_variables );
	}

	/**
	 * Determines the page type of the current term.
	 *
	 * @param string $taxonomy The taxonomy name.
	 *
	 * @return string The page type.
	 */
	public function determine_for_term( $taxonomy ) {
		$replacement_variables = $this->get();
		if ( array_key_exists( $taxonomy, $replacement_variables ) ) {
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

		$replacement_variables = $this->get();
		if ( array_key_exists( $post->post_type, $replacement_variables ) ) {
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
		if ( ! $this->has_for_page_type( $post_type ) ) {
			return $fallback;
		}

		return $post_type;
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
		$page_type = $name . '_archive';

		if ( ! $this->has_for_page_type( $page_type ) ) {
			return $fallback;
		}

		return $page_type;
	}

	/**
	 * Adds the replavement variables for the given page types.
	 *
	 * @param array $page_types                   Page types to add variables for.
	 * @param array $replacement_variables_to_add The variables to add.
	 *
	 * @return void
	 */
	protected function add_for_page_types( array $page_types, array $replacement_variables_to_add ) {
		if ( empty( $replacement_variables_to_add ) ) {
			return;
		}

		$replacement_variables_to_add = array_fill_keys( $page_types, $replacement_variables_to_add );
		$replacement_variables        = $this->replacement_variables;

		$this->replacement_variables = array_merge_recursive( $replacement_variables, $replacement_variables_to_add );
	}

	/**
	 * Extracts the names from the given replacements variables.
	 *
	 * @param array $replacement_variables Replacement variables to extract the name from.
	 *
	 * @return array Extracted names.
	 */
	protected function extract_names( $replacement_variables ) {
		$extracted_names = [];

		foreach ( $replacement_variables as $replacement_variable ) {
			if ( empty( $replacement_variable['name'] ) ) {
				continue;
			}

			$extracted_names[] = $replacement_variable['name'];
		}

		return $extracted_names;
	}

	/**
	 * Returns whether the given page type has editor specific replace vars.
	 *
	 * @param string $page_type The page type to check.
	 *
	 * @return bool True if there are associated editor specific replace vars.
	 */
	protected function has_for_page_type( $page_type ) {
		$replacement_variables = $this->get();

		return ( ! empty( $replacement_variables[ $page_type ] ) && is_array( $replacement_variables[ $page_type ] ) );
	}

	/**
	 * Merges all editor specific replacement variables into one array and removes duplicates.
	 *
	 * @return array The list of unique editor specific replacement variables.
	 */
	protected function get_unique_replacement_variables() {
		$merged_replacement_variables = call_user_func_array( 'array_merge', array_values( $this->get() ) );

		return array_unique( $merged_replacement_variables );
	}
}
