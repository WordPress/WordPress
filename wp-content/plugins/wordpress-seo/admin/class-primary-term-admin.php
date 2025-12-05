<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * Adds the UI to change the primary term for a post.
 */
class WPSEO_Primary_Term_Admin implements WPSEO_WordPress_Integration {

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_filter( 'wpseo_content_meta_section_content', [ $this, 'add_input_fields' ] );

		add_action( 'admin_footer', [ $this, 'wp_footer' ], 10 );

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
	}

	/**
	 * Gets the current post ID.
	 *
	 * @return int The post ID.
	 */
	protected function get_current_id() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are not processing form information, We are casting to an integer.
		$post_id = isset( $_GET['post'] ) && is_string( $_GET['post'] ) ? (int) wp_unslash( $_GET['post'] ) : 0;

		if ( $post_id === 0 && isset( $GLOBALS['post_ID'] ) ) {
			$post_id = (int) $GLOBALS['post_ID'];
		}

		return $post_id;
	}

	/**
	 * Adds hidden fields for primary taxonomies.
	 *
	 * @param string $content The metabox content.
	 *
	 * @return string The HTML content.
	 */
	public function add_input_fields( $content ) {
		$taxonomies = $this->get_primary_term_taxonomies();

		foreach ( $taxonomies as $taxonomy ) {
			$content .= $this->primary_term_field( $taxonomy->name );
			$content .= wp_nonce_field( 'save-primary-term', WPSEO_Meta::$form_prefix . 'primary_' . $taxonomy->name . '_nonce', false, false );
		}
		return $content;
	}

	/**
	 * Generates the HTML for a hidden field for a primary taxonomy.
	 *
	 * @param string $taxonomy_name The taxonomy's slug.
	 *
	 * @return string The HTML for a hidden primary taxonomy field.
	 */
	protected function primary_term_field( $taxonomy_name ) {
		return sprintf(
			'<input class="yoast-wpseo-primary-term" type="hidden" id="%1$s" name="%2$s" value="%3$s" />',
			esc_attr( $this->generate_field_id( $taxonomy_name ) ),
			esc_attr( $this->generate_field_name( $taxonomy_name ) ),
			esc_attr( $this->get_primary_term( $taxonomy_name ) )
		);
	}

	/**
	 * Generates an id for a primary taxonomy's hidden field.
	 *
	 * @param string $taxonomy_name The taxonomy's slug.
	 *
	 * @return string The field id.
	 */
	protected function generate_field_id( $taxonomy_name ) {
		return 'yoast-wpseo-primary-' . $taxonomy_name;
	}

	/**
	 * Generates a name for a primary taxonomy's hidden field.
	 *
	 * @param string $taxonomy_name The taxonomy's slug.
	 *
	 * @return string The field id.
	 */
	protected function generate_field_name( $taxonomy_name ) {
		return WPSEO_Meta::$form_prefix . 'primary_' . $taxonomy_name . '_term';
	}

	/**
	 * Adds primary term templates.
	 *
	 * @return void
	 */
	public function wp_footer() {
		$taxonomies = $this->get_primary_term_taxonomies();

		if ( ! empty( $taxonomies ) ) {
			$this->include_js_templates();
		}
	}

	/**
	 * Enqueues all the assets needed for the primary term interface.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		global $pagenow;

		if ( ! WPSEO_Metabox::is_post_edit( $pagenow ) ) {
			return;
		}

		$taxonomies = $this->get_primary_term_taxonomies();

		// Only enqueue if there are taxonomies that need a primary term.
		if ( empty( $taxonomies ) ) {
			return;
		}

		$asset_manager = new WPSEO_Admin_Asset_Manager();
		$asset_manager->enqueue_style( 'primary-category' );

		$mapped_taxonomies = $this->get_mapped_taxonomies_for_js( $taxonomies );

		$data = [
			'taxonomies' => $mapped_taxonomies,
		];

		$asset_manager->localize_script( 'post-edit', 'wpseoPrimaryCategoryL10n', $data );
		$asset_manager->localize_script( 'post-edit-classic', 'wpseoPrimaryCategoryL10n', $data );
	}

	/**
	 * Gets the id of the primary term.
	 *
	 * @param string $taxonomy_name Taxonomy name for the term.
	 *
	 * @return int primary term id
	 */
	protected function get_primary_term( $taxonomy_name ) {
		$primary_term = new WPSEO_Primary_Term( $taxonomy_name, $this->get_current_id() );

		return $primary_term->get_primary_term();
	}

	/**
	 * Returns all the taxonomies for which the primary term selection is enabled.
	 *
	 * @param int|null $post_id Default current post ID.
	 * @return array
	 */
	protected function get_primary_term_taxonomies( $post_id = null ) {
		if ( $post_id === null ) {
			$post_id = $this->get_current_id();
		}

		$taxonomies = wp_cache_get( 'primary_term_taxonomies_' . $post_id, 'wpseo' );
		if ( $taxonomies !== false ) {
			return $taxonomies;
		}

		$taxonomies = $this->generate_primary_term_taxonomies( $post_id );

		wp_cache_set( 'primary_term_taxonomies_' . $post_id, $taxonomies, 'wpseo' );

		return $taxonomies;
	}

	/**
	 * Includes templates file.
	 *
	 * @return void
	 */
	protected function include_js_templates() {
		include_once WPSEO_PATH . 'admin/views/js-templates-primary-term.php';
	}

	/**
	 * Generates the primary term taxonomies.
	 *
	 * @param int $post_id ID of the post.
	 *
	 * @return array
	 */
	protected function generate_primary_term_taxonomies( $post_id ) {
		$post_type      = get_post_type( $post_id );
		$all_taxonomies = get_object_taxonomies( $post_type, 'objects' );
		$all_taxonomies = array_filter( $all_taxonomies, [ $this, 'filter_hierarchical_taxonomies' ] );

		/**
		 * Filters which taxonomies for which the user can choose the primary term.
		 *
		 * @param array  $taxonomies     An array of taxonomy objects that are primary_term enabled.
		 * @param string $post_type      The post type for which to filter the taxonomies.
		 * @param array  $all_taxonomies All taxonomies for this post types, even ones that don't have primary term
		 *                               enabled.
		 */
		$taxonomies = (array) apply_filters( 'wpseo_primary_term_taxonomies', $all_taxonomies, $post_type, $all_taxonomies );

		return $taxonomies;
	}

	/**
	 * Creates a map of taxonomies for localization.
	 *
	 * @param array $taxonomies The taxononmies that should be mapped.
	 *
	 * @return array The mapped taxonomies.
	 */
	protected function get_mapped_taxonomies_for_js( $taxonomies ) {
		return array_map( [ $this, 'map_taxonomies_for_js' ], $taxonomies );
	}

	/**
	 * Returns an array suitable for use in the javascript.
	 *
	 * @param stdClass $taxonomy The taxonomy to map.
	 *
	 * @return array The mapped taxonomy.
	 */
	private function map_taxonomies_for_js( $taxonomy ) {
		$primary_term = $this->get_primary_term( $taxonomy->name );

		if ( empty( $primary_term ) ) {
			$primary_term = '';
		}

		$terms = get_terms(
			[
				'taxonomy'               => $taxonomy->name,
				'update_term_meta_cache' => false,
				'fields'                 => 'id=>name',
			]
		);

		$mapped_terms_for_js = [];
		foreach ( $terms as $id => $name ) {
			$mapped_terms_for_js[] = [
				'id'   => $id,
				'name' => $name,
			];
		}

		return [
			'title'         => $taxonomy->labels->singular_name,
			'name'          => $taxonomy->name,
			'primary'       => $primary_term,
			'singularLabel' => $taxonomy->labels->singular_name,
			'fieldId'       => $this->generate_field_id( $taxonomy->name ),
			'restBase'      => ( $taxonomy->rest_base ) ? $taxonomy->rest_base : $taxonomy->name,
			'terms'         => $mapped_terms_for_js,
		];
	}

	/**
	 * Returns whether or not a taxonomy is hierarchical.
	 *
	 * @param stdClass $taxonomy Taxonomy object.
	 *
	 * @return bool
	 */
	private function filter_hierarchical_taxonomies( $taxonomy ) {
		return (bool) $taxonomy->hierarchical;
	}
}
