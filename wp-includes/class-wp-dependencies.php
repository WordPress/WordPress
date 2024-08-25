<?php
/**
 * Dependencies API: WP_Dependencies base class
 *
 * @since 2.6.0
 *
 * @package WordPress
 * @subpackage Dependencies
 */

/**
 * Core base class extended to register items.
 *
 * @since 2.6.0
 *
 * @see _WP_Dependency
 */
#[AllowDynamicProperties]
class WP_Dependencies {
	/**
	 * An array of all registered dependencies keyed by handle.
	 *
	 * @since 2.6.8
	 *
	 * @var _WP_Dependency[]
	 */
	public $registered = array();

	/**
	 * An array of handles of queued dependencies.
	 *
	 * @since 2.6.8
	 *
	 * @var string[]
	 */
	public $queue = array();

	/**
	 * An array of handles of dependencies to queue.
	 *
	 * @since 2.6.0
	 *
	 * @var string[]
	 */
	public $to_do = array();

	/**
	 * An array of handles of dependencies already queued.
	 *
	 * @since 2.6.0
	 *
	 * @var string[]
	 */
	public $done = array();

	/**
	 * An array of additional arguments passed when a handle is registered.
	 *
	 * Arguments are appended to the item query string.
	 *
	 * @since 2.6.0
	 *
	 * @var array
	 */
	public $args = array();

	/**
	 * An array of dependency groups to enqueue.
	 *
	 * Each entry is keyed by handle and represents the integer group level or boolean
	 * false if the handle has no group.
	 *
	 * @since 2.8.0
	 *
	 * @var (int|false)[]
	 */
	public $groups = array();

	/**
	 * A handle group to enqueue.
	 *
	 * @since 2.8.0
	 *
	 * @deprecated 4.5.0
	 * @var int
	 */
	public $group = 0;

	/**
	 * Cached lookup array of flattened queued items and dependencies.
	 *
	 * @since 5.4.0
	 *
	 * @var array
	 */
	private $all_queued_deps;

	/**
	 * List of assets enqueued before details were registered.
	 *
	 * @since 5.9.0
	 *
	 * @var array
	 */
	private $queued_before_register = array();

	/**
	 * Processes the items and dependencies.
	 *
	 * Processes the items passed to it or the queue, and their dependencies.
	 *
	 * @since 2.6.0
	 * @since 2.8.0 Added the `$group` parameter.
	 *
	 * @param string|string[]|false $handles Optional. Items to be processed: queue (false),
	 *                                       single item (string), or multiple items (array of strings).
	 *                                       Default false.
	 * @param int|false             $group   Optional. Group level: level (int), no group (false).
	 * @return string[] Array of handles of items that have been processed.
	 */
	public function do_items( $handles = false, $group = false ) {
		/*
		 * If nothing is passed, print the queue. If a string is passed,
		 * print that item. If an array is passed, print those items.
		 */
		$handles = false === $handles ? $this->queue : (array) $handles;
		$this->all_deps( $handles );

		foreach ( $this->to_do as $key => $handle ) {
			if ( ! in_array( $handle, $this->done, true ) && isset( $this->registered[ $handle ] ) ) {
				/*
				 * Attempt to process the item. If successful,
				 * add the handle to the done array.
				 *
				 * Unset the item from the to_do array.
				 */
				if ( $this->do_item( $handle, $group ) ) {
					$this->done[] = $handle;
				}

				unset( $this->to_do[ $key ] );
			}
		}

		return $this->done;
	}

	/**
	 * Processes a dependency.
	 *
	 * @since 2.6.0
	 * @since 5.5.0 Added the `$group` parameter.
	 *
	 * @param string    $handle Name of the item. Should be unique.
	 * @param int|false $group  Optional. Group level: level (int), no group (false).
	 *                          Default false.
	 * @return bool True on success, false if not set.
	 */
	public function do_item( $handle, $group = false ) {
		return isset( $this->registered[ $handle ] );
	}

	/**
	 * Determines dependencies.
	 *
	 * Recursively builds an array of items to process taking
	 * dependencies into account. Does NOT catch infinite loops.
	 *
	 * @since 2.1.0
	 * @since 2.6.0 Moved from `WP_Scripts`.
	 * @since 2.8.0 Added the `$group` parameter.
	 *
	 * @param string|string[] $handles   Item handle (string) or item handles (array of strings).
	 * @param bool            $recursion Optional. Internal flag that function is calling itself.
	 *                                   Default false.
	 * @param int|false       $group     Optional. Group level: level (int), no group (false).
	 *                                   Default false.
	 * @return bool True on success, false on failure.
	 */
	public function all_deps( $handles, $recursion = false, $group = false ) {
		$handles = (array) $handles;
		if ( ! $handles ) {
			return false;
		}

		foreach ( $handles as $handle ) {
			$handle_parts = explode( '?', $handle );
			$handle       = $handle_parts[0];
			$queued       = in_array( $handle, $this->to_do, true );

			if ( in_array( $handle, $this->done, true ) ) { // Already done.
				continue;
			}

			$moved     = $this->set_group( $handle, $recursion, $group );
			$new_group = $this->groups[ $handle ];

			if ( $queued && ! $moved ) { // Already queued and in the right group.
				continue;
			}

			$keep_going = true;
			if ( ! isset( $this->registered[ $handle ] ) ) {
				$keep_going = false; // Item doesn't exist.
			} elseif ( $this->registered[ $handle ]->deps && array_diff( $this->registered[ $handle ]->deps, array_keys( $this->registered ) ) ) {
				$keep_going = false; // Item requires dependencies that don't exist.
			} elseif ( $this->registered[ $handle ]->deps && ! $this->all_deps( $this->registered[ $handle ]->deps, true, $new_group ) ) {
				$keep_going = false; // Item requires dependencies that don't exist.
			}

			if ( ! $keep_going ) { // Either item or its dependencies don't exist.
				if ( $recursion ) {
					return false; // Abort this branch.
				} else {
					continue; // We're at the top level. Move on to the next one.
				}
			}

			if ( $queued ) { // Already grabbed it and its dependencies.
				continue;
			}

			if ( isset( $handle_parts[1] ) ) {
				$this->args[ $handle ] = $handle_parts[1];
			}

			$this->to_do[] = $handle;
		}

		return true;
	}

	/**
	 * Register an item.
	 *
	 * Registers the item if no item of that name already exists.
	 *
	 * @since 2.1.0
	 * @since 2.6.0 Moved from `WP_Scripts`.
	 *
	 * @param string           $handle Name of the item. Should be unique.
	 * @param string|false     $src    Full URL of the item, or path of the item relative
	 *                                 to the WordPress root directory. If source is set to false,
	 *                                 the item is an alias of other items it depends on.
	 * @param string[]         $deps   Optional. An array of registered item handles this item depends on.
	 *                                 Default empty array.
	 * @param string|bool|null $ver    Optional. String specifying item version number, if it has one,
	 *                                 which is added to the URL as a query string for cache busting purposes.
	 *                                 If version is set to false, a version number is automatically added
	 *                                 equal to current installed WordPress version.
	 *                                 If set to null, no version is added.
	 * @param mixed            $args   Optional. Custom property of the item. NOT the class property $args.
	 *                                 Examples: $media, $in_footer.
	 * @return bool Whether the item has been registered. True on success, false on failure.
	 */
	public function add( $handle, $src, $deps = array(), $ver = false, $args = null ) {
		if ( isset( $this->registered[ $handle ] ) ) {
			return false;
		}
		$this->registered[ $handle ] = new _WP_Dependency( $handle, $src, $deps, $ver, $args );

		// If the item was enqueued before the details were registered, enqueue it now.
		if ( array_key_exists( $handle, $this->queued_before_register ) ) {
			if ( ! is_null( $this->queued_before_register[ $handle ] ) ) {
				$this->enqueue( $handle . '?' . $this->queued_before_register[ $handle ] );
			} else {
				$this->enqueue( $handle );
			}

			unset( $this->queued_before_register[ $handle ] );
		}

		return true;
	}

	/**
	 * Add extra item data.
	 *
	 * Adds data to a registered item.
	 *
	 * @since 2.6.0
	 *
	 * @param string $handle Name of the item. Should be unique.
	 * @param string $key    The data key.
	 * @param mixed  $value  The data value.
	 * @return bool True on success, false on failure.
	 */
	public function add_data( $handle, $key, $value ) {
		if ( ! isset( $this->registered[ $handle ] ) ) {
			return false;
		}

		return $this->registered[ $handle ]->add_data( $key, $value );
	}

	/**
	 * Get extra item data.
	 *
	 * Gets data associated with a registered item.
	 *
	 * @since 3.3.0
	 *
	 * @param string $handle Name of the item. Should be unique.
	 * @param string $key    The data key.
	 * @return mixed Extra item data (string), false otherwise.
	 */
	public function get_data( $handle, $key ) {
		if ( ! isset( $this->registered[ $handle ] ) ) {
			return false;
		}

		if ( ! isset( $this->registered[ $handle ]->extra[ $key ] ) ) {
			return false;
		}

		return $this->registered[ $handle ]->extra[ $key ];
	}

	/**
	 * Un-register an item or items.
	 *
	 * @since 2.1.0
	 * @since 2.6.0 Moved from `WP_Scripts`.
	 *
	 * @param string|string[] $handles Item handle (string) or item handles (array of strings).
	 */
	public function remove( $handles ) {
		foreach ( (array) $handles as $handle ) {
			unset( $this->registered[ $handle ] );
		}
	}

	/**
	 * Queue an item or items.
	 *
	 * Decodes handles and arguments, then queues handles and stores
	 * arguments in the class property $args. For example in extending
	 * classes, $args is appended to the item url as a query string.
	 * Note $args is NOT the $args property of items in the $registered array.
	 *
	 * @since 2.1.0
	 * @since 2.6.0 Moved from `WP_Scripts`.
	 *
	 * @param string|string[] $handles Item handle (string) or item handles (array of strings).
	 */
	public function enqueue( $handles ) {
		foreach ( (array) $handles as $handle ) {
			$handle = explode( '?', $handle );

			if ( ! in_array( $handle[0], $this->queue, true ) && isset( $this->registered[ $handle[0] ] ) ) {
				$this->queue[] = $handle[0];

				// Reset all dependencies so they must be recalculated in recurse_deps().
				$this->all_queued_deps = null;

				if ( isset( $handle[1] ) ) {
					$this->args[ $handle[0] ] = $handle[1];
				}
			} elseif ( ! isset( $this->registered[ $handle[0] ] ) ) {
				$this->queued_before_register[ $handle[0] ] = null; // $args

				if ( isset( $handle[1] ) ) {
					$this->queued_before_register[ $handle[0] ] = $handle[1];
				}
			}
		}
	}

	/**
	 * Dequeue an item or items.
	 *
	 * Decodes handles and arguments, then dequeues handles
	 * and removes arguments from the class property $args.
	 *
	 * @since 2.1.0
	 * @since 2.6.0 Moved from `WP_Scripts`.
	 *
	 * @param string|string[] $handles Item handle (string) or item handles (array of strings).
	 */
	public function dequeue( $handles ) {
		foreach ( (array) $handles as $handle ) {
			$handle = explode( '?', $handle );
			$key    = array_search( $handle[0], $this->queue, true );

			if ( false !== $key ) {
				// Reset all dependencies so they must be recalculated in recurse_deps().
				$this->all_queued_deps = null;

				unset( $this->queue[ $key ] );
				unset( $this->args[ $handle[0] ] );
			} elseif ( array_key_exists( $handle[0], $this->queued_before_register ) ) {
				unset( $this->queued_before_register[ $handle[0] ] );
			}
		}
	}

	/**
	 * Recursively search the passed dependency tree for a handle.
	 *
	 * @since 4.0.0
	 *
	 * @param string[] $queue  An array of queued _WP_Dependency handles.
	 * @param string   $handle Name of the item. Should be unique.
	 * @return bool Whether the handle is found after recursively searching the dependency tree.
	 */
	protected function recurse_deps( $queue, $handle ) {
		if ( isset( $this->all_queued_deps ) ) {
			return isset( $this->all_queued_deps[ $handle ] );
		}

		$all_deps = array_fill_keys( $queue, true );
		$queues   = array();
		$done     = array();

		while ( $queue ) {
			foreach ( $queue as $queued ) {
				if ( ! isset( $done[ $queued ] ) && isset( $this->registered[ $queued ] ) ) {
					$deps = $this->registered[ $queued ]->deps;
					if ( $deps ) {
						$all_deps += array_fill_keys( $deps, true );
						array_push( $queues, $deps );
					}
					$done[ $queued ] = true;
				}
			}
			$queue = array_pop( $queues );
		}

		$this->all_queued_deps = $all_deps;

		return isset( $this->all_queued_deps[ $handle ] );
	}

	/**
	 * Query the list for an item.
	 *
	 * @since 2.1.0
	 * @since 2.6.0 Moved from `WP_Scripts`.
	 *
	 * @param string $handle Name of the item. Should be unique.
	 * @param string $status Optional. Status of the item to query. Default 'registered'.
	 * @return bool|_WP_Dependency Found, or object Item data.
	 */
	public function query( $handle, $status = 'registered' ) {
		switch ( $status ) {
			case 'registered':
			case 'scripts': // Back compat.
				if ( isset( $this->registered[ $handle ] ) ) {
					return $this->registered[ $handle ];
				}
				return false;

			case 'enqueued':
			case 'queue': // Back compat.
				if ( in_array( $handle, $this->queue, true ) ) {
					return true;
				}
				return $this->recurse_deps( $this->queue, $handle );

			case 'to_do':
			case 'to_print': // Back compat.
				return in_array( $handle, $this->to_do, true );

			case 'done':
			case 'printed': // Back compat.
				return in_array( $handle, $this->done, true );
		}

		return false;
	}

	/**
	 * Set item group, unless already in a lower group.
	 *
	 * @since 2.8.0
	 *
	 * @param string    $handle    Name of the item. Should be unique.
	 * @param bool      $recursion Internal flag that calling function was called recursively.
	 * @param int|false $group     Group level: level (int), no group (false).
	 * @return bool Not already in the group or a lower group.
	 */
	public function set_group( $handle, $recursion, $group ) {
		$group = (int) $group;

		if ( isset( $this->groups[ $handle ] ) && $this->groups[ $handle ] <= $group ) {
			return false;
		}

		$this->groups[ $handle ] = $group;

		return true;
	}

	/**
	 * Get etag header for cache validation.
	 *
	 * @since 6.7.0
	 *
	 * @global string $wp_version The WordPress version string.
	 *
	 * @param string[] $load Array of script or style handles to load.
	 * @return string Etag header.
	 */
	public function get_etag( $load ) {
		/*
		 * Note: wp_get_wp_version() is not used here, as this file can be included
		 * via wp-admin/load-scripts.php or wp-admin/load-styles.php, in which case
		 * wp-includes/functions.php is not loaded.
		 */
		global $wp_version;

		$etag = "WP:{$wp_version};";

		foreach ( $load as $handle ) {
			if ( ! array_key_exists( $handle, $this->registered ) ) {
				continue;
			}

			$ver   = $this->registered[ $handle ]->ver ?? $wp_version;
			$etag .= "{$handle}:{$ver};";
		}

		/*
		 * This is not intended to be cryptographically secure, just a fast way to get
		 * a fixed length string based on the script versions. As this file does not
		 * load the full WordPress environment, it is not possible to use the salted
		 * wp_hash() function.
		 */
		return 'W/"' . md5( $etag ) . '"';
	}
}
