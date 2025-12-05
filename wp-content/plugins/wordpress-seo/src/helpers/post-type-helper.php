<?php

namespace Yoast\WP\SEO\Helpers;

use WP_Post_Type;

/**
 * A helper object for post types.
 */
class Post_Type_Helper {

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	protected $options_helper;

	/**
	 * Post_Type_Helper constructor.
	 *
	 * @param Options_Helper $options_helper The options helper.
	 */
	public function __construct( Options_Helper $options_helper ) {
		$this->options_helper = $options_helper;
	}

	/**
	 * Checks if the request post type is public and indexable.
	 *
	 * @codeCoverageIgnore We have to write test when this method contains own code.
	 *
	 * @param string $post_type_name The name of the post type to lookup.
	 *
	 * @return bool True when post type is set to index.
	 */
	public function is_indexable( $post_type_name ) {
		if ( $this->options_helper->get( 'disable-' . $post_type_name, false ) ) {
			return false;
		}

		return ( $this->options_helper->get( 'noindex-' . $post_type_name, false ) === false );
	}

	/**
	 * Checks if the request post type has the Yoast Metabox enabled.
	 *
	 * @param string $post_type_name The name of the post type to lookup.
	 *
	 * @return bool True if metabox is enabled.
	 */
	public function has_metabox( $post_type_name ) {
		return ( $this->options_helper->get( 'display-metabox-pt-' . $post_type_name, true ) === true );
	}

	/**
	 * Returns an array with the public post types.
	 *
	 * @codeCoverageIgnore It only wraps a WordPress function.
	 *
	 * @param string $output The output type to use.
	 *
	 * @return array Array with all the public post_types.
	 */
	public function get_public_post_types( $output = 'names' ) {
		return \get_post_types( [ 'public' => true ], $output );
	}

	/**
	 * Returns an array with the accessible post types.
	 *
	 * An accessible post type is a post type that is public and isn't set as no-index (robots).
	 *
	 * @return array Array with all the accessible post_types.
	 */
	public function get_accessible_post_types() {
		$post_types = \get_post_types( [ 'public' => true ] );
		$post_types = \array_filter( $post_types, 'is_post_type_viewable' );

		/**
		 * Filter: 'wpseo_accessible_post_types' - Allow changing the accessible post types.
		 *
		 * @param array $post_types The public post types.
		 */
		$post_types = \apply_filters( 'wpseo_accessible_post_types', $post_types );

		// When the array gets messed up somewhere.
		if ( ! \is_array( $post_types ) ) {
			return [];
		}

		return $post_types;
	}

	/**
	 * Returns an array of post types that are excluded from being indexed for the
	 * indexables.
	 *
	 * @return array The excluded post types.
	 */
	public function get_excluded_post_types_for_indexables() {
		/**
		 * Filter: 'wpseo_indexable_excluded_post_types' - Allows excluding posts of a certain post
		 * type from being saved to the indexable table.
		 *
		 * @param array $excluded_post_types The currently excluded post types that indexables will not be created for.
		 */
		$excluded_post_types = \apply_filters( 'wpseo_indexable_excluded_post_types', [] );

		// Failsafe, to always make sure that `excluded_post_types` is an array.
		if ( ! \is_array( $excluded_post_types ) ) {
			return [];
		}

		return $excluded_post_types;
	}

	/**
	 * Checks if the post type is excluded.
	 *
	 * @param string $post_type The post type to check.
	 *
	 * @return bool If the post type is exclude.
	 */
	public function is_excluded( $post_type ) {
		return \in_array( $post_type, $this->get_excluded_post_types_for_indexables(), true );
	}

	/**
	 * Checks if the post type with the given name has an archive page.
	 *
	 * @param WP_Post_Type|string $post_type The name of the post type to check.
	 *
	 * @return bool True when the post type has an archive page.
	 */
	public function has_archive( $post_type ) {
		if ( \is_string( $post_type ) ) {
			$post_type = \get_post_type_object( $post_type );
		}

		return ( ! empty( $post_type->has_archive ) );
	}

	/**
	 * Returns the post types that should be indexed.
	 *
	 * @return array The post types that should be indexed.
	 */
	public function get_indexable_post_types() {
		$public_post_types   = $this->get_public_post_types();
		$excluded_post_types = $this->get_excluded_post_types_for_indexables();

		$included_post_types = \array_diff( $public_post_types, $excluded_post_types );

		return $this->filter_included_post_types( $included_post_types );
	}

	/**
	 * Returns all indexable post types with archive pages.
	 *
	 * @return array All post types which are indexable and have archive pages.
	 */
	public function get_indexable_post_archives() {
		return \array_filter( $this->get_indexable_post_type_objects(), [ $this, 'has_archive' ] );
	}

	/**
	 * Filters the post types that are included to be indexed.
	 *
	 * @param array $included_post_types The post types that are included to be indexed.
	 *
	 * @return array The filtered post types that are included to be indexed.
	 */
	protected function filter_included_post_types( $included_post_types ) {
		/**
		 * Filter: 'wpseo_indexable_forced_included_post_types' - Allows force including posts of a certain post
		 * type to be saved to the indexable table.
		 *
		 * @param array $included_post_types The currently included post types that indexables will be created for.
		 */
		$filtered_included_post_types = \apply_filters( 'wpseo_indexable_forced_included_post_types', $included_post_types );

		if ( ! \is_array( $filtered_included_post_types ) ) {
			// If the filter got misused, let's return the unfiltered array.
			return \array_values( $included_post_types );
		}

		// Add sanity check to make sure everything is an actual post type.
		foreach ( $filtered_included_post_types as $key => $post_type ) {
			if ( ! \post_type_exists( $post_type ) ) {
				unset( $filtered_included_post_types[ $key ] );
			}
		}

		// `array_values`, to make sure that the keys are reset.
		return \array_values( $filtered_included_post_types );
	}

	/**
	 * Checks if the given post type should be indexed.
	 *
	 * @param string $post_type The post type that is checked.
	 *
	 * @return bool
	 */
	public function is_of_indexable_post_type( $post_type ) {
		$public_types = $this->get_indexable_post_types();
		if ( ! \in_array( $post_type, $public_types, true ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Checks if the archive of a post type is indexable.
	 *
	 * @param string $post_type The post type to check.
	 *
	 * @return bool if the archive is indexable.
	 */
	public function is_post_type_archive_indexable( $post_type ) {
		$public_type_objects = $this->get_indexable_post_archives();
		$public_types        = \array_map(
			static function ( $post_type_object ) {
				return $post_type_object->name;
			},
			$public_type_objects
		);

		return \in_array( $post_type, $public_types, true );
	}

	/**
	 * Returns an array of complete post type objects for all indexable post types.
	 *
	 * @return array List of indexable post type objects.
	 */
	public function get_indexable_post_type_objects() {
		$post_type_objects    = [];
		$indexable_post_types = $this->get_indexable_post_types();
		foreach ( $indexable_post_types as $post_type ) {
			$post_type_object = \get_post_type_object( $post_type );
			if ( ! empty( $post_type_object ) ) {
				$post_type_objects[ $post_type ] = $post_type_object;
			}
		}

		return $post_type_objects;
	}
}
