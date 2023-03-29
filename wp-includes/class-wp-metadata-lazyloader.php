<?php
/**
 * Meta API: WP_Metadata_Lazyloader class
 *
 * @package WordPress
 * @subpackage Meta
 * @since 4.5.0
 */

/**
 * Core class used for lazy-loading object metadata.
 *
 * When loading many objects of a given type, such as posts in a WP_Query loop, it often makes
 * sense to prime various metadata caches at the beginning of the loop. This means fetching all
 * relevant metadata with a single database query, a technique that has the potential to improve
 * performance dramatically in some cases.
 *
 * In cases where the given metadata may not even be used in the loop, we can improve performance
 * even more by only priming the metadata cache for affected items the first time a piece of metadata
 * is requested - ie, by lazy-loading it. So, for example, comment meta may not be loaded into the
 * cache in the comments section of a post until the first time get_comment_meta() is called in the
 * context of the comment loop.
 *
 * WP uses the WP_Metadata_Lazyloader class to queue objects for metadata cache priming. The class
 * then detects the relevant get_*_meta() function call, and queries the metadata of all queued objects.
 *
 * Do not access this class directly. Use the wp_metadata_lazyloader() function.
 *
 * @since 4.5.0
 */
#[AllowDynamicProperties]
class WP_Metadata_Lazyloader {
	/**
	 * Pending objects queue.
	 *
	 * @since 4.5.0
	 * @var array
	 */
	protected $pending_objects;

	/**
	 * Settings for supported object types.
	 *
	 * @since 4.5.0
	 * @var array
	 */
	protected $settings = array();

	/**
	 * Constructor.
	 *
	 * @since 4.5.0
	 */
	public function __construct() {
		$this->settings = array(
			'term'    => array(
				'filter'   => 'get_term_metadata',
				'callback' => array( $this, 'lazyload_meta_callback' ),
			),
			'comment' => array(
				'filter'   => 'get_comment_metadata',
				'callback' => array( $this, 'lazyload_meta_callback' ),
			),
		);
	}

	/**
	 * Adds objects to the metadata lazy-load queue.
	 *
	 * @since 4.5.0
	 *
	 * @param string $object_type Type of object whose meta is to be lazy-loaded. Accepts 'term' or 'comment'.
	 * @param array  $object_ids  Array of object IDs.
	 * @return void|WP_Error WP_Error on failure.
	 */
	public function queue_objects( $object_type, $object_ids ) {
		if ( ! isset( $this->settings[ $object_type ] ) ) {
			return new WP_Error( 'invalid_object_type', __( 'Invalid object type.' ) );
		}

		$type_settings = $this->settings[ $object_type ];

		if ( ! isset( $this->pending_objects[ $object_type ] ) ) {
			$this->pending_objects[ $object_type ] = array();
		}

		foreach ( $object_ids as $object_id ) {
			// Keyed by ID for faster lookup.
			if ( ! isset( $this->pending_objects[ $object_type ][ $object_id ] ) ) {
				$this->pending_objects[ $object_type ][ $object_id ] = 1;
			}
		}

		add_filter( $type_settings['filter'], $type_settings['callback'], 10, 5 );

		/**
		 * Fires after objects are added to the metadata lazy-load queue.
		 *
		 * @since 4.5.0
		 *
		 * @param array                  $object_ids  Array of object IDs.
		 * @param string                 $object_type Type of object being queued.
		 * @param WP_Metadata_Lazyloader $lazyloader  The lazy-loader object.
		 */
		do_action( 'metadata_lazyloader_queued_objects', $object_ids, $object_type, $this );
	}

	/**
	 * Resets lazy-load queue for a given object type.
	 *
	 * @since 4.5.0
	 *
	 * @param string $object_type Object type. Accepts 'comment' or 'term'.
	 * @return void|WP_Error WP_Error on failure.
	 */
	public function reset_queue( $object_type ) {
		if ( ! isset( $this->settings[ $object_type ] ) ) {
			return new WP_Error( 'invalid_object_type', __( 'Invalid object type.' ) );
		}

		$type_settings = $this->settings[ $object_type ];

		$this->pending_objects[ $object_type ] = array();
		remove_filter( $type_settings['filter'], $type_settings['callback'] );
	}

	/**
	 * Lazy-loads term meta for queued terms.
	 *
	 * This method is public so that it can be used as a filter callback. As a rule, there
	 * is no need to invoke it directly.
	 *
	 * @since 4.5.0
	 * @deprecated 6.3.0 Use WP_Metadata_Lazyloader::lazyload_meta_callback() instead.
	 *
	 * @param mixed $check The `$check` param passed from the 'get_term_metadata' hook.
	 * @return mixed In order not to short-circuit `get_metadata()`. Generally, this is `null`, but it could be
	 *               another value if filtered by a plugin.
	 */
	public function lazyload_term_meta( $check ) {
		_deprecated_function( __METHOD__, '6.3.0', 'WP_Metadata_Lazyloader::lazyload_meta_callback' );
		return $this->lazyload_meta_callback( $check, 0, '', false, 'term' );
	}

	/**
	 * Lazy-loads comment meta for queued comments.
	 *
	 * This method is public so that it can be used as a filter callback. As a rule, there is no need to invoke it
	 * directly, from either inside or outside the `WP_Query` object.
	 *
	 * @since 4.5.0
	 * @deprecated 6.3.0 Use WP_Metadata_Lazyloader::lazyload_meta_callback() instead.
	 *
	 * @param mixed $check The `$check` param passed from the {@see 'get_comment_metadata'} hook.
	 * @return mixed The original value of `$check`, so as not to short-circuit `get_comment_metadata()`.
	 */
	public function lazyload_comment_meta( $check ) {
		_deprecated_function( __METHOD__, '6.3.0', 'WP_Metadata_Lazyloader::lazyload_meta_callback' );
		return $this->lazyload_meta_callback( $check, 0, '', false, 'comment' );
	}

	/**
	 * Lazy-loads meta for queued objects.
	 *
	 * This method is public so that it can be used as a filter callback. As a rule, there
	 * is no need to invoke it directly.
	 *
	 * @since 6.3.0
	 *
	 * @param mixed  $check     The `$check` param passed from the 'get_*_metadata' hook.
	 * @param int    $object_id ID of the object metadata is for.
	 * @param string $meta_key  Unused.
	 * @param bool   $single    Unused.
	 * @param string $meta_type Type of object metadata is for. Accepts 'post', 'comment', 'term', 'user',
	 *                          or any other object type with an associated meta table.
	 * @return mixed In order not to short-circuit `get_metadata()`. Generally, this is `null`, but it could be
	 *               another value if filtered by a plugin.
	 */
	public function lazyload_meta_callback( $check, $object_id, $meta_key, $single, $meta_type ) {
		if ( empty( $this->pending_objects[ $meta_type ] ) ) {
			return $check;
		}

		$object_ids = array_keys( $this->pending_objects[ $meta_type ] );
		if ( $object_id && ! in_array( $object_id, $object_ids, true ) ) {
			$object_ids[] = $object_id;
		}

		update_meta_cache( $meta_type, $object_ids );

		// No need to run again for this set of objects.
		$this->reset_queue( $meta_type );

		return $check;
	}
}
