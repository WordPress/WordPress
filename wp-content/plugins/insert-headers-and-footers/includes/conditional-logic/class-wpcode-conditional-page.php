<?php
/**
 * Class that handles conditional logic related to pages.
 *
 * @package WPCode
 */

/**
 * The WPCode_Conditional_Page class.
 */
class WPCode_Conditional_Page extends WPCode_Conditional_Type {

	/**
	 * The type unique name (slug).
	 *
	 * @var string
	 */
	public $name = 'page';

	/**
	 * Set the translatable label.
	 *
	 * @return void
	 */
	protected function set_label() {
		$this->label = __( 'Page', 'insert-headers-and-footers' );
	}

	/**
	 * Set the type options for the admin mainly.
	 *
	 * @return void
	 */
	public function load_type_options() {
		$this->options = array(
			'type_of_page'  => array(
				'label'    => __( 'Type of page', 'insert-headers-and-footers' ),
				'type'     => 'select',
				'options'  => array(
					array(
						'label' => __( 'Homepage', 'insert-headers-and-footers' ),
						'value' => 'is_front_page',
					),
					array(
						'label' => __( 'Archive', 'insert-headers-and-footers' ),
						'value' => 'is_archive',
					),
					array(
						'label' => __( 'Single post/page', 'insert-headers-and-footers' ),
						'value' => 'is_single',
					),
					array(
						'label' => __( 'Search page', 'insert-headers-and-footers' ),
						'value' => 'is_search',
					),
					array(
						'label' => __( '404 page', 'insert-headers-and-footers' ),
						'value' => 'is_404',
					),
					array(
						'label' => __( 'Author page', 'insert-headers-and-footers' ),
						'value' => 'is_author',
					),
				),
				'callback' => array( $this, 'get_type_of_page' ),
			),
			'post_type'     => array(
				'label'    => __( 'Post type', 'insert-headers-and-footers' ),
				'type'     => 'select',
				'options'  => $this->get_post_types(),
				'callback' => array( $this, 'get_current_post_type' ),
			),
			'referrer'      => array(
				'label'    => __( 'Referrer', 'insert-headers-and-footers' ),
				'type'     => 'text',
				'callback' => array( $this, 'get_referer' ),
			),
			'taxonomy_page' => array(
				'label'    => __( 'Taxonomy page', 'insert-headers-and-footers' ),
				'type'     => 'select',
				'options'  => $this->get_taxonomies_options(),
				'callback' => array( $this, 'get_taxonomy' ),
			),
			'taxonomy_term' => array(
				'label'           => __( 'Taxonomy term', 'insert-headers-and-footers' ),
				'type'            => 'ajax',
				'options'         => 'wpcode_search_terms',
				'callback'        => array( $this, 'get_term' ),
				'labels_callback' => array( $this, 'get_taxonomy_term_labels' ),
			),
			'page_url'      => array(
				'label'    => __( 'Page URL', 'insert-headers-and-footers' ),
				'type'     => 'text',
				'callback' => array( $this, 'get_page_url' ),
			),
		);
	}

	/**
	 * Get a list of options with post types.
	 *
	 * @return array
	 */
	protected function get_post_types() {
		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		$options    = array();
		foreach ( $post_types as $post_type ) {
			$options[] = array(
				'label' => $post_type->label,
				'value' => $post_type->name,
			);
		}

		return $options;
	}

	/**
	 * Get a list of taxonomy types.
	 *
	 * @return array
	 */
	protected function get_taxonomies_options() {
		$taxonomies = get_taxonomies(
			array(
				'public' => true,
			),
			'objects'
		);
		$options    = array();
		foreach ( $taxonomies as $taxonomy ) {
			if ( 'post_format' === $taxonomy->name ) {
				continue;
			}
			$options[] = array(
				// Translators: this is the name of the taxonomy.
				'label' => $taxonomy->labels->singular_name,
				'value' => $taxonomy->name,
			);
		}

		return $options;
	}

	/**
	 * Get the type of page.
	 *
	 * @return string
	 */
	public function get_type_of_page() {
		global $wp_query;

		if ( ! isset( $wp_query ) ) {
			return '';
		}
		if ( is_front_page() || is_home() ) {
			return 'is_front_page';
		}
		if ( is_singular() ) {
			return 'is_single';
		}
		if ( is_archive() ) {
			return 'is_archive';
		}
		if ( is_search() ) {
			return 'is_search';
		}
		if ( is_404() ) {
			return 'is_404';
		}
		if ( is_author() ) {
			return 'is_author';
		}

		return '';
	}

	/**
	 * Get the current page post type, if any.
	 *
	 * @return string
	 */
	public function get_current_post_type() {
		return get_post_type();
	}

	/**
	 * Get the referrer from PHP.
	 *
	 * @return string
	 */
	public function get_referer() {
		return isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '';
	}

	/**
	 * Get the page URL.
	 *
	 * @return string
	 */
	public function get_page_url() {
		global $wp;

		return isset( $wp->request ) ? trailingslashit( home_url( $wp->request ) ) : '';
	}

	/**
	 * Check if the current page is a taxonomy page and if yes get the taxonomy name.
	 *
	 * @return string
	 */
	public function get_taxonomy() {
		global $wp_query;
		if ( is_null( $wp_query ) ) {
			return '';
		}
		$queried_object = get_queried_object();

		return isset( $queried_object->taxonomy ) ? $queried_object->taxonomy : '';
	}

	/**
	 * Get the term of the current page, if any.
	 *
	 * @return array
	 */
	public function get_term() {
		global $wp_query;
		if ( is_null( $wp_query ) ) {
			return array();
		}
		if ( is_tax() ) {
			$queried_object = get_queried_object();

			return isset( $queried_object->term_id ) ? array( $queried_object->term_id ) : array();
		}
		if ( is_singular() ) {
			return get_terms(
				array(
					'object_ids' => array( get_the_ID() ),
					'fields'     => 'ids',
				)
			);
		}

		return array();
	}

	/**
	 * Get the term labels for the taxonomy term value loading in the admin form.
	 *
	 * @param array $values The values that are selected.
	 *
	 * @return array
	 */
	public function get_taxonomy_term_labels( $values ) {
		$labels = array();
		foreach ( $values as $term_id ) {
			$term = get_term( $term_id );
			if ( is_null( $term ) || is_wp_error( $term ) ) {
				continue;
			}
			$labels[] = array(
				'value' => $term_id,
				'label' => $term->name,
			);
		}

		return $labels;
	}
}

new WPCode_Conditional_Page();
