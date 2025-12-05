<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

use Yoast\WP\SEO\Helpers\Score_Icon_Helper;
use Yoast\WP\SEO\Repositories\Indexable_Repository;

/**
 * This class adds columns to the taxonomy table.
 */
class WPSEO_Taxonomy_Columns {

	/**
	 * The SEO analysis.
	 *
	 * @var WPSEO_Metabox_Analysis_SEO
	 */
	private $analysis_seo;

	/**
	 * The readability analysis.
	 *
	 * @var WPSEO_Metabox_Analysis_Readability
	 */
	private $analysis_readability;

	/**
	 * The current taxonomy.
	 *
	 * @var string
	 */
	private $taxonomy;

	/**
	 * Holds the Indexable_Repository.
	 *
	 * @var Indexable_Repository
	 */
	protected $indexable_repository;

	/**
	 * Holds the Score_Icon_Helper.
	 *
	 * @var Score_Icon_Helper
	 */
	protected $score_icon_helper;

	/**
	 * WPSEO_Taxonomy_Columns constructor.
	 */
	public function __construct() {

		$this->taxonomy = $this->get_taxonomy();

		if ( ! empty( $this->taxonomy ) ) {
			add_filter( 'manage_edit-' . $this->taxonomy . '_columns', [ $this, 'add_columns' ] );
			add_filter( 'manage_' . $this->taxonomy . '_custom_column', [ $this, 'parse_column' ], 10, 3 );
		}

		$this->analysis_seo         = new WPSEO_Metabox_Analysis_SEO();
		$this->analysis_readability = new WPSEO_Metabox_Analysis_Readability();
		$this->indexable_repository = YoastSEO()->classes->get( Indexable_Repository::class );
		$this->score_icon_helper    = YoastSEO()->helpers->score_icon;
	}

	/**
	 * Adds an SEO score column to the terms table, right after the description column.
	 *
	 * @param array $columns Current set columns.
	 *
	 * @return array
	 */
	public function add_columns( array $columns ) {
		if ( $this->display_metabox( $this->taxonomy ) === false ) {
			return $columns;
		}

		$new_columns = [];

		foreach ( $columns as $column_name => $column_value ) {
			$new_columns[ $column_name ] = $column_value;

			if ( $column_name === 'description' && $this->analysis_seo->is_enabled() ) {
				$new_columns['wpseo-score'] = '<span class="yoast-tooltip yoast-tooltip-n yoast-tooltip-alt" data-label="' . esc_attr__( 'SEO score', 'wordpress-seo' ) . '"><span class="yoast-column-seo-score yoast-column-header-has-tooltip"><span class="screen-reader-text">'
											. __( 'SEO score', 'wordpress-seo' ) . '</span></span></span>';
			}

			if ( $column_name === 'description' && $this->analysis_readability->is_enabled() ) {
				$new_columns['wpseo-score-readability'] = '<span class="yoast-tooltip yoast-tooltip-n yoast-tooltip-alt" data-label="' . esc_attr__( 'Readability score', 'wordpress-seo' ) . '"><span class="yoast-column-readability yoast-column-header-has-tooltip"><span class="screen-reader-text">'
														. __( 'Readability score', 'wordpress-seo' ) . '</span></span></span>';
			}
		}

		return $new_columns;
	}

	/**
	 * Parses the column.
	 *
	 * @param string $content     The current content of the column.
	 * @param string $column_name The name of the column.
	 * @param int    $term_id     ID of requested taxonomy.
	 *
	 * @return string
	 */
	public function parse_column( $content, $column_name, $term_id ) {

		switch ( $column_name ) {
			case 'wpseo-score':
				return $this->get_score_value( $term_id );

			case 'wpseo-score-readability':
				return $this->get_score_readability_value( $term_id );
		}

		return $content;
	}

	/**
	 * Retrieves the taxonomy from the $_GET or $_POST variable.
	 *
	 * @return string|null The current taxonomy or null when it is not set.
	 */
	public function get_current_taxonomy() {
		// phpcs:disable WordPress.Security.NonceVerification.Missing,WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			if ( isset( $_POST['taxonomy'] ) && is_string( $_POST['taxonomy'] ) ) {
				return sanitize_text_field( wp_unslash( $_POST['taxonomy'] ) );
			}
		}
		elseif ( isset( $_GET['taxonomy'] ) && is_string( $_GET['taxonomy'] ) ) {
			return sanitize_text_field( wp_unslash( $_GET['taxonomy'] ) );
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing,WordPress.Security.NonceVerification.Recommended
		return null;
	}

	/**
	 * Returns the posted/get taxonomy value if it is set.
	 *
	 * @return string|null
	 */
	private function get_taxonomy() {
		// phpcs:disable WordPress.Security.NonceVerification.Missing,WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( wp_doing_ajax() ) {
			if ( isset( $_POST['taxonomy'] ) && is_string( $_POST['taxonomy'] ) ) {
				return sanitize_text_field( wp_unslash( $_POST['taxonomy'] ) );
			}
		}
		elseif ( isset( $_GET['taxonomy'] ) && is_string( $_GET['taxonomy'] ) ) {
			return sanitize_text_field( wp_unslash( $_GET['taxonomy'] ) );
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing,WordPress.Security.NonceVerification.Recommended
		return null;
	}

	/**
	 * Parses the value for the score column.
	 *
	 * @param int $term_id ID of requested term.
	 *
	 * @return string
	 */
	private function get_score_value( $term_id ) {
		$indexable = $this->indexable_repository->find_by_id_and_type( (int) $term_id, 'term' );

		return $this->score_icon_helper->for_seo( $indexable, '', __( 'Term is set to noindex.', 'wordpress-seo' ) );
	}

	/**
	 * Parses the value for the readability score column.
	 *
	 * @param int $term_id ID of the requested term.
	 *
	 * @return string The HTML for the readability score indicator.
	 */
	private function get_score_readability_value( $term_id ) {
		$score = (int) WPSEO_Taxonomy_Meta::get_term_meta( $term_id, $this->taxonomy, 'content_score' );

		return $this->score_icon_helper->for_readability( $score );
	}

	/**
	 * Check if the taxonomy is indexable.
	 *
	 * @param mixed $term The current term.
	 *
	 * @return bool Whether the term is indexable.
	 */
	private function is_indexable( $term ) {
		// When the no_index value is not empty and not default, check if its value is index.
		$no_index = WPSEO_Taxonomy_Meta::get_term_meta( $term->term_id, $this->taxonomy, 'noindex' );

		// Check if the default for taxonomy is empty (this will be index).
		if ( ! empty( $no_index ) && $no_index !== 'default' ) {
			return ( $no_index === 'index' );
		}

		if ( is_object( $term ) ) {
			$no_index_key = 'noindex-tax-' . $term->taxonomy;

			// If the option is false, this means we want to index it.
			return WPSEO_Options::get( $no_index_key, false ) === false;
		}

		return true;
	}

	/**
	 * Wraps the WPSEO_Metabox check to determine whether the metabox should be displayed either by
	 * choice of the admin or because the taxonomy is not public.
	 *
	 * @since 7.0
	 *
	 * @param string|null $taxonomy Optional. The taxonomy to test, defaults to the current taxonomy.
	 *
	 * @return bool Whether the meta box (and associated columns etc) should be hidden.
	 */
	private function display_metabox( $taxonomy = null ) {
		$current_taxonomy = $this->get_current_taxonomy();

		if ( ! isset( $taxonomy ) && ! empty( $current_taxonomy ) ) {
			$taxonomy = $current_taxonomy;
		}

		return WPSEO_Utils::is_metabox_active( $taxonomy, 'taxonomy' );
	}
}
