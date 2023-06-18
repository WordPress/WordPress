<?php
/**
 * Base class for of auto-insert options.
 *
 * @package wpcode
 */

/**
 * Abstract class WPCode_Auto_Insert_Type.
 */
abstract class WPCode_Auto_Insert_Type {
	/**
	 * The auto-insert label.
	 *
	 * @var string
	 */
	public $label;

	/**
	 * An array of locations.
	 * This is an array of unique locations where snippets can be executed in the form
	 * of key => label where the keys should be unique for all the options across
	 * all child classes as those will be used as taxonomy terms to store the
	 * relationship between snippets and their location.
	 *
	 * @var array
	 */
	public $locations;

	/**
	 * Terms of the locations for this type.
	 *
	 * @var array
	 */
	public $locations_terms;

	/**
	 * All the snippets for this location.
	 *
	 * @var array
	 */
	public $snippets;

	/**
	 * For which code type this insert is available.
	 * By default, all.
	 *
	 * @var string
	 */
	public $code_type = 'all';

	/**
	 * If we should skip the cache set this to false.
	 *
	 * @var bool
	 */
	protected $use_cache = true;

	/**
	 * Display a label next to the optgroup title.
	 *
	 * @var string
	 */
	public $label_pill = '';

	/**
	 * Title of the upgrade prompt.
	 *
	 * @var string
	 */
	public $upgrade_title = '';

	/**
	 * Text of the upgrade prompt.
	 *
	 * @var string
	 */
	public $upgrade_text = '';

	/**
	 * URL of the upgrade prompt (CTA) with UTM.
	 *
	 * @var string
	 */
	public $upgrade_link = '';

	/**
	 * Text for the CTA Button.
	 *
	 * @var string
	 */
	public $upgrade_button = '';

	/**
	 * Category used for displaying this type in the admin.
	 *
	 * @var string
	 */
	public $category = '';

	/**
	 * Start the auto insertion.
	 */
	public function __construct() {
		$this->init();
		$this->register_type();
		/**
		 * Constant to enable safe mode.
		 * Filter to allow disabling auto insert.
		 */
		if ( defined( 'WPCODE_SAFE_MODE' ) && WPCODE_SAFE_MODE ) {
			return;
		}
		if ( ! apply_filters( 'wpcode_do_auto_insert', true ) ) {
			return;
		}

		$this->add_start_hook();
	}

	/**
	 * Register this instance to the global auto-insert types.
	 *
	 * @return void
	 */
	private function register_type() {
		wpcode()->auto_insert->register_type( $this );
	}

	/**
	 * Init function that is specific to each auto-insert type.
	 *
	 * @return void
	 */
	abstract public function init();

	/**
	 * Give child classes a chance to load on a different hook.
	 *
	 * @return void
	 */
	protected function add_start_hook() {
		add_action( 'wp', array( $this, 'maybe_run_hooks' ) );
	}

	/**
	 * Check if conditions are met before calling the hooks.
	 *
	 * @return void
	 */
	public function maybe_run_hooks() {
		if ( ! $this->conditions() ) {
			return;
		}
		// Go through relevant hooks and output based on settings.
		$this->hooks();
	}

	/**
	 * Conditions that have to be met for the class to do its thing.
	 * For example, in the single post class we'll check if is_single
	 * and only then will we change the_content.
	 *
	 * @return bool
	 */
	public function conditions() {
		// Most types only run on the frontend.
		return ! is_admin();
	}

	/**
	 * Hooks specific to this type of auto-insertion.
	 *
	 * @return void
	 */
	public function hooks() {
	}

	/**
	 * Get an array of options for the admin.
	 * Check if the insert type is compatible with the code type.
	 *
	 * @return array
	 */
	public function get_locations() {
		return isset( $this->locations ) ? $this->locations : array();
	}

	/**
	 * Query snippets by location.
	 *
	 * @param string $location The location slug.
	 *
	 * @return WPCode_Snippet[]
	 */
	public function get_snippets_for_location( $location ) {
		$snippets = $this->get_snippets();

		$snippets_for_location = isset( $snippets[ $location ] ) ? $snippets[ $location ] : array();

		return apply_filters(
			'wpcode_get_snippets_for_location',
			wpcode()->conditional_logic->check_snippets_conditions( $snippets_for_location ),
			$location
		);
	}

	/**
	 * Get the snippets for this type and query on demand.
	 *
	 * @return array
	 */
	public function get_snippets() {
		if ( ! isset( $this->snippets ) ) {
			$this->load_all_snippets_for_type();
		}

		return $this->snippets;
	}

	/**
	 * Load all the snippets for this type and group them by location.
	 * This can be further improved by separating the snippet loading and loading
	 * all the relevant snippets for a screen at once (regardless of type) or just loading all the
	 * active snippets in 1 query.
	 *
	 * @return void
	 */
	public function load_all_snippets_for_type() {

		if ( $this->use_cache() ) {
			$this->snippets = $this->get_snippets_from_cache();

			return;
		}

		$terms = $this->get_locations_ids();
		if ( empty( $terms ) ) {
			// If no terms are yet set we don't have to load anything as
			// no snippet has been added to the current type.
			$this->snippets = array();

			return;
		}
		$args = array(
			'post_type'      => 'wpcode',
			'posts_per_page' => - 1,
			'tax_query'      => array( //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'wpcode_location',
					'terms'    => $terms,
				),
			),
			'post_status'    => 'publish',
		);
		add_filter( 'posts_clauses', array( $this, 'include_term_in_post' ) );
		$snippets_query = new WP_Query( $args );
		remove_filter( 'posts_clauses', array( $this, 'include_term_in_post' ) );
		$snippets = $snippets_query->posts;

		// Get the terms that are defined and then assign found snippets to their respective taxonomies
		// so that they can be picked up by id later without having to query again.
		$location_terms = $this->get_location_terms();
		foreach ( $location_terms as $location_key => $location_term ) {
			$term_id                         = $location_term->term_taxonomy_id;
			$this->snippets[ $location_key ] = array();
			// Until we update to PHP 5.3 this is the easiest way to do this.
			foreach ( $snippets as $snippet ) {
				if ( isset( $snippet->term_taxonomy_id ) && absint( $snippet->term_taxonomy_id ) === $term_id ) {
					$this->snippets[ $location_key ][] = new WPCode_Snippet( $snippet );
				}
			}
		}
	}

	/**
	 * Get snippets from cache split by relevant locations for this type.
	 *
	 * @return array
	 */
	public function get_snippets_from_cache() {
		$cached_snippets = wpcode()->cache->get_cached_snippets();
		$type_snippets   = array();
		foreach ( $this->locations as $location => $label ) {
			if ( array_key_exists( $location, $cached_snippets ) ) {
				$type_snippets[ $location ] = $cached_snippets[ $location ];
			} else {
				$type_snippets[ $location ] = array();
			}
		}

		return $type_snippets;
	}

	/**
	 * Get the ids of the loaded location terms.
	 *
	 * @return int[]
	 */
	public function get_locations_ids() {
		$terms = $this->get_location_terms();
		$ids   = array();
		foreach ( $terms as $term ) {
			$ids[] = $term->term_id;
		}

		return $ids;
	}

	/**
	 * Get the location terms.
	 *
	 * @return WP_Term[]
	 */
	public function get_location_terms() {
		if ( ! isset( $this->locations_terms ) ) {
			$this->load_locations_terms();
		}

		return $this->locations_terms;
	}

	/**
	 * Query the location terms using get_terms and store them in the instance.
	 *
	 * @return void
	 */
	public function load_locations_terms() {
		$terms = get_terms(
			array(
				'taxonomy' => 'wpcode_location',
				'slug'     => array_keys( $this->locations ),
			)
		);

		$this->locations_terms = array();

		if ( is_wp_error( $terms ) ) {
			// If the terms don't exist, bail early.
			return;
		}

		foreach ( $terms as $term ) {
			$this->locations_terms[ $term->slug ] = $term;
		}
	}

	/**
	 * Change the clauses for our specific query to include the term id in the resulting
	 * WP_Post object so that we can group the results by the type locations.
	 *
	 * @param array $clauses Array of clauses for the SQL query.
	 *
	 * @return mixed
	 */
	public function include_term_in_post( $clauses ) {
		global $wpdb;

		$clauses['fields']  .= ", {$wpdb->term_relationships}.term_taxonomy_id";
		$clauses['groupby'] = '';

		return $clauses;
	}

	/**
	 * Return the type label.
	 *
	 * @return string
	 */
	public function get_label() {
		return $this->label;
	}

	/**
	 * Grab the use cache value allowing filtering.
	 *
	 * @return bool
	 */
	public function use_cache() {
		return boolval( apply_filters( 'wpcode_use_auto_insert_cache', $this->use_cache ) );
	}

	/**
	 * Get the snippets for a location and echo them executed.
	 *
	 * @param string $location_name The location to grab snippets for.
	 *
	 * @return void
	 */
	public function output_location( $location_name ) {
		$snippets = $this->get_snippets_for_location( $location_name );
		foreach ( $snippets as $snippet ) {
			echo wpcode()->execute->get_snippet_output( $snippet );
		}
	}

	/**
	 * Get the snippets for a location and return them executed.
	 *
	 * @param string $location_name The location to grab snippets for.
	 *
	 * @return string
	 */
	public function get_location( $location_name ) {
		$content  = '';
		$snippets = $this->get_snippets_for_location( $location_name );
		foreach ( $snippets as $snippet ) {
			$content .= wpcode()->execute->get_snippet_output( $snippet );
		}

		return $content;
	}
}
