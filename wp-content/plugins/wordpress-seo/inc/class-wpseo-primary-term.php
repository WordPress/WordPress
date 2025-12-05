<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO
 */

/**
 * Represents a post's primary term.
 */
class WPSEO_Primary_Term {

	/**
	 * Taxonomy name for the term.
	 *
	 * @var string
	 */
	protected $taxonomy_name;

	/**
	 * Post ID for the term.
	 *
	 * @var int
	 */
	protected $post_ID;

	/**
	 * The taxonomy this term is part of.
	 *
	 * @param string $taxonomy_name Taxonomy name for the term.
	 * @param int    $post_id       Post ID for the term.
	 */
	public function __construct( $taxonomy_name, $post_id ) {
		$this->taxonomy_name = $taxonomy_name;
		$this->post_ID       = $post_id;
	}

	/**
	 * Returns the primary term ID.
	 *
	 * @return int|bool
	 */
	public function get_primary_term() {
		$primary_term = get_post_meta( $this->post_ID, WPSEO_Meta::$meta_prefix . 'primary_' . $this->taxonomy_name, true );

		if ( ! $primary_term ) {
			return false;
		}

		$terms = $this->get_terms();

		if ( ! in_array( (int) $primary_term, wp_list_pluck( $terms, 'term_id' ), true ) ) {
			$primary_term = false;
		}

		$primary_term = (int) $primary_term;
		return ( $primary_term ) ? ( $primary_term ) : false;
	}

	/**
	 * Sets the new primary term ID.
	 *
	 * @param int $new_primary_term New primary term ID.
	 *
	 * @return void
	 */
	public function set_primary_term( $new_primary_term ) {
		update_post_meta( $this->post_ID, WPSEO_Meta::$meta_prefix . 'primary_' . $this->taxonomy_name, $new_primary_term );
	}

	/**
	 * Get the terms for the current post ID.
	 * When $terms is not an array, set $terms to an array.
	 *
	 * @return array
	 */
	protected function get_terms() {
		$terms = get_the_terms( $this->post_ID, $this->taxonomy_name );

		if ( ! is_array( $terms ) ) {
			$terms = [];
		}

		return $terms;
	}
}
