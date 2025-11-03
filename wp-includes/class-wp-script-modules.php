<?php
/**
 * Script Modules API: WP_Script_Modules class.
 *
 * Native support for ES Modules and Import Maps.
 *
 * @package WordPress
 * @subpackage Script Modules
 */

/**
 * Core class used to register script modules.
 *
 * @since 6.5.0
 */
class WP_Script_Modules {
	/**
	 * Holds the registered script modules, keyed by script module identifier.
	 *
	 * @since 6.5.0
	 * @var array<string, array<string, mixed>>
	 */
	private $registered = array();

	/**
	 * An array of IDs for queued script modules.
	 *
	 * @since 6.9.0
	 * @var string[]
	 */
	private $queue = array();

	/**
	 * Holds the script module identifiers that have been printed.
	 *
	 * @since 6.9.0
	 * @var string[]
	 */
	private $done = array();

	/**
	 * Tracks whether the @wordpress/a11y script module is available.
	 *
	 * Some additional HTML is required on the page for the module to work. Track
	 * whether it's available to print at the appropriate time.
	 *
	 * @since 6.7.0
	 * @var bool
	 */
	private $a11y_available = false;

	/**
	 * Holds a mapping of dependents (as IDs) for a given script ID.
	 * Used to optimize recursive dependency tree checks.
	 *
	 * @since 6.9.0
	 * @var array<string, string[]>
	 */
	private $dependents_map = array();

	/**
	 * Holds the valid values for fetchpriority.
	 *
	 * @since 6.9.0
	 * @var string[]
	 */
	private $priorities = array(
		'low',
		'auto',
		'high',
	);

	/**
	 * Registers the script module if no script module with that script module
	 * identifier has already been registered.
	 *
	 * @since 6.5.0
	 * @since 6.9.0 Added the $args parameter.
	 *
	 * @param string            $id       The identifier of the script module. Should be unique. It will be used in the
	 *                                    final import map.
	 * @param string            $src      Optional. Full URL of the script module, or path of the script module relative
	 *                                    to the WordPress root directory. If it is provided and the script module has
	 *                                    not been registered yet, it will be registered.
	 * @param array             $deps     {
	 *                                        Optional. List of dependencies.
	 *
	 *                                        @type string|array ...$0 {
	 *                                            An array of script module identifiers of the dependencies of this script
	 *                                            module. The dependencies can be strings or arrays. If they are arrays,
	 *                                            they need an `id` key with the script module identifier, and can contain
	 *                                            an `import` key with either `static` or `dynamic`. By default,
	 *                                            dependencies that don't contain an `import` key are considered static.
	 *
	 *                                            @type string $id     The script module identifier.
	 *                                            @type string $import Optional. Import type. May be either `static` or
	 *                                                                 `dynamic`. Defaults to `static`.
	 *                                        }
	 *                                    }
	 * @param string|false|null $version  Optional. String specifying the script module version number. Defaults to false.
	 *                                    It is added to the URL as a query string for cache busting purposes. If $version
	 *                                    is set to false, the version number is the currently installed WordPress version.
	 *                                    If $version is set to null, no version is added.
	 * @param array             $args     {
	 *     Optional. An array of additional args. Default empty array.
	 *
	 *     @type bool                $in_footer     Whether to print the script module in the footer. Only relevant to block themes. Default 'false'. Optional.
	 *     @type 'auto'|'low'|'high' $fetchpriority Fetch priority. Default 'auto'. Optional.
	 * }
	 */
	public function register( string $id, string $src, array $deps = array(), $version = false, array $args = array() ) {
		if ( '' === $id ) {
			_doing_it_wrong( __METHOD__, __( 'Non-empty string required for id.' ), '6.9.0' );
			return;
		}

		if ( ! isset( $this->registered[ $id ] ) ) {
			$dependencies = array();
			foreach ( $deps as $dependency ) {
				if ( is_array( $dependency ) ) {
					if ( ! isset( $dependency['id'] ) || ! is_string( $dependency['id'] ) ) {
						_doing_it_wrong( __METHOD__, __( 'Missing required id key in entry among dependencies array.' ), '6.5.0' );
						continue;
					}
					$dependencies[] = array(
						'id'     => $dependency['id'],
						'import' => isset( $dependency['import'] ) && 'dynamic' === $dependency['import'] ? 'dynamic' : 'static',
					);
				} elseif ( is_string( $dependency ) ) {
					$dependencies[] = array(
						'id'     => $dependency,
						'import' => 'static',
					);
				} else {
					_doing_it_wrong( __METHOD__, __( 'Entries in dependencies array must be either strings or arrays with an id key.' ), '6.5.0' );
				}
			}

			$in_footer = isset( $args['in_footer'] ) && (bool) $args['in_footer'];

			$fetchpriority = 'auto';
			if ( isset( $args['fetchpriority'] ) ) {
				if ( $this->is_valid_fetchpriority( $args['fetchpriority'] ) ) {
					$fetchpriority = $args['fetchpriority'];
				} else {
					_doing_it_wrong(
						__METHOD__,
						sprintf(
							/* translators: 1: $fetchpriority, 2: $id */
							__( 'Invalid fetchpriority `%1$s` defined for `%2$s` during script registration.' ),
							is_string( $args['fetchpriority'] ) ? $args['fetchpriority'] : gettype( $args['fetchpriority'] ),
							$id
						),
						'6.9.0'
					);
				}
			}

			$this->registered[ $id ] = array(
				'src'           => $src,
				'version'       => $version,
				'dependencies'  => $dependencies,
				'in_footer'     => $in_footer,
				'fetchpriority' => $fetchpriority,
			);
		}
	}

	/**
	 * Gets IDs for queued script modules.
	 *
	 * @since 6.9.0
	 *
	 * @return string[] Script module IDs.
	 */
	public function get_queue(): array {
		return $this->queue;
	}

	/**
	 * Checks if the provided fetchpriority is valid.
	 *
	 * @since 6.9.0
	 *
	 * @param string|mixed $priority Fetch priority.
	 * @return bool Whether valid fetchpriority.
	 */
	private function is_valid_fetchpriority( $priority ): bool {
		return in_array( $priority, $this->priorities, true );
	}

	/**
	 * Sets the fetch priority for a script module.
	 *
	 * @since 6.9.0
	 *
	 * @param string              $id       Script module identifier.
	 * @param 'auto'|'low'|'high' $priority Fetch priority for the script module.
	 * @return bool Whether setting the fetchpriority was successful.
	 */
	public function set_fetchpriority( string $id, string $priority ): bool {
		if ( ! isset( $this->registered[ $id ] ) ) {
			return false;
		}

		if ( '' === $priority ) {
			$priority = 'auto';
		}

		if ( ! $this->is_valid_fetchpriority( $priority ) ) {
			_doing_it_wrong(
				__METHOD__,
				/* translators: %s: Invalid fetchpriority. */
				sprintf( __( 'Invalid fetchpriority: %s' ), $priority ),
				'6.9.0'
			);
			return false;
		}

		$this->registered[ $id ]['fetchpriority'] = $priority;
		return true;
	}

	/**
	 * Sets whether a script module should be printed in the footer.
	 *
	 * This is only relevant in block themes.
	 *
	 * @since 6.9.0
	 *
	 * @param string           $id        Script module identifier.
	 * @param bool             $in_footer Whether to print in the footer.
	 * @return bool Whether setting the printing location was successful.
	 */
	public function set_in_footer( string $id, bool $in_footer ): bool {
		if ( ! isset( $this->registered[ $id ] ) ) {
			return false;
		}
		$this->registered[ $id ]['in_footer'] = $in_footer;
		return true;
	}

	/**
	 * Marks the script module to be enqueued in the page.
	 *
	 * If a src is provided and the script module has not been registered yet, it
	 * will be registered.
	 *
	 * @since 6.5.0
	 * @since 6.9.0 Added the $args parameter.
	 *
	 * @param string            $id       The identifier of the script module. Should be unique. It will be used in the
	 *                                    final import map.
	 * @param string            $src      Optional. Full URL of the script module, or path of the script module relative
	 *                                    to the WordPress root directory. If it is provided and the script module has
	 *                                    not been registered yet, it will be registered.
	 * @param array             $deps     {
	 *                                        Optional. List of dependencies.
	 *
	 *                                        @type string|array ...$0 {
	 *                                            An array of script module identifiers of the dependencies of this script
	 *                                            module. The dependencies can be strings or arrays. If they are arrays,
	 *                                            they need an `id` key with the script module identifier, and can contain
	 *                                            an `import` key with either `static` or `dynamic`. By default,
	 *                                            dependencies that don't contain an `import` key are considered static.
	 *
	 *                                            @type string $id     The script module identifier.
	 *                                            @type string $import Optional. Import type. May be either `static` or
	 *                                                                 `dynamic`. Defaults to `static`.
	 *                                        }
	 *                                    }
	 * @param string|false|null $version  Optional. String specifying the script module version number. Defaults to false.
	 *                                    It is added to the URL as a query string for cache busting purposes. If $version
	 *                                    is set to false, the version number is the currently installed WordPress version.
	 *                                    If $version is set to null, no version is added.
	 * @param array             $args     {
	 *     Optional. An array of additional args. Default empty array.
	 *
	 *     @type bool                $in_footer     Whether to print the script module in the footer. Only relevant to block themes. Default 'false'. Optional.
	 *     @type 'auto'|'low'|'high' $fetchpriority Fetch priority. Default 'auto'. Optional.
	 * }
	 */
	public function enqueue( string $id, string $src = '', array $deps = array(), $version = false, array $args = array() ) {
		if ( '' === $id ) {
			_doing_it_wrong( __METHOD__, __( 'Non-empty string required for id.' ), '6.9.0' );
			return;
		}

		if ( ! in_array( $id, $this->queue, true ) ) {
			$this->queue[] = $id;
		}
		if ( ! isset( $this->registered[ $id ] ) && $src ) {
			$this->register( $id, $src, $deps, $version, $args );
		}
	}

	/**
	 * Unmarks the script module so it will no longer be enqueued in the page.
	 *
	 * @since 6.5.0
	 *
	 * @param string $id The identifier of the script module.
	 */
	public function dequeue( string $id ) {
		$this->queue = array_values( array_diff( $this->queue, array( $id ) ) );
	}

	/**
	 * Removes a registered script module.
	 *
	 * @since 6.5.0
	 *
	 * @param string $id The identifier of the script module.
	 */
	public function deregister( string $id ) {
		$this->dequeue( $id );
		unset( $this->registered[ $id ] );
	}

	/**
	 * Adds the hooks to print the import map, enqueued script modules and script
	 * module preloads.
	 *
	 * In classic themes, the script modules used by the blocks are not yet known
	 * when the `wp_head` actions is fired, so it needs to print everything in the
	 * footer.
	 *
	 * @since 6.5.0
	 */
	public function add_hooks() {
		$is_block_theme = wp_is_block_theme();
		$position       = $is_block_theme ? 'wp_head' : 'wp_footer';
		add_action( $position, array( $this, 'print_import_map' ) );
		if ( $is_block_theme ) {
			/*
			 * Modules can only be printed in the head for block themes because only with
			 * block themes will import map be fully populated by modules discovered by
			 * rendering the block template. In classic themes, modules are enqueued during
			 * template rendering, thus the import map must be printed in the footer,
			 * followed by all enqueued modules.
			 */
			add_action( 'wp_head', array( $this, 'print_head_enqueued_script_modules' ) );
		}
		add_action( 'wp_footer', array( $this, 'print_enqueued_script_modules' ) );
		add_action( $position, array( $this, 'print_script_module_preloads' ) );

		add_action( 'admin_print_footer_scripts', array( $this, 'print_import_map' ) );
		add_action( 'admin_print_footer_scripts', array( $this, 'print_enqueued_script_modules' ) );
		add_action( 'admin_print_footer_scripts', array( $this, 'print_script_module_preloads' ) );

		add_action( 'wp_footer', array( $this, 'print_script_module_data' ) );
		add_action( 'admin_print_footer_scripts', array( $this, 'print_script_module_data' ) );
		add_action( 'wp_footer', array( $this, 'print_a11y_script_module_html' ), 20 );
		add_action( 'admin_print_footer_scripts', array( $this, 'print_a11y_script_module_html' ), 20 );
	}

	/**
	 * Gets the highest fetch priority for the provided script IDs.
	 *
	 * @since 6.9.0
	 *
	 * @param string[] $ids Script module IDs.
	 * @return 'auto'|'low'|'high' Highest fetch priority for the provided script module IDs.
	 */
	private function get_highest_fetchpriority( array $ids ): string {
		static $high_priority_index = null;
		if ( null === $high_priority_index ) {
			$high_priority_index = count( $this->priorities ) - 1;
		}

		$highest_priority_index = 0;
		foreach ( $ids as $id ) {
			if ( isset( $this->registered[ $id ] ) ) {
				$highest_priority_index = (int) max(
					$highest_priority_index,
					(int) array_search( $this->registered[ $id ]['fetchpriority'], $this->priorities, true )
				);
				if ( $high_priority_index === $highest_priority_index ) {
					break;
				}
			}
		}

		return $this->priorities[ $highest_priority_index ];
	}

	/**
	 * Prints the enqueued script modules in head.
	 *
	 * This is only used in block themes.
	 *
	 * @since 6.9.0
	 */
	public function print_head_enqueued_script_modules() {
		foreach ( $this->get_sorted_dependencies( $this->queue ) as $id ) {
			if (
				isset( $this->registered[ $id ] ) &&
				! $this->registered[ $id ]['in_footer']
			) {
				// If any dependency is set to be printed in footer, skip printing this module in head.
				$dependencies = array_keys( $this->get_dependencies( array( $id ) ) );
				foreach ( $dependencies as $dependency_id ) {
					if (
						in_array( $dependency_id, $this->queue, true ) &&
						isset( $this->registered[ $dependency_id ] ) &&
						$this->registered[ $dependency_id ]['in_footer']
					) {
						continue 2;
					}
				}
				$this->print_script_module( $id );
			}
		}
	}

	/**
	 * Prints the enqueued script modules in footer.
	 *
	 * @since 6.5.0
	 */
	public function print_enqueued_script_modules() {
		foreach ( $this->get_sorted_dependencies( $this->queue ) as $id ) {
			$this->print_script_module( $id );
		}
	}

	/**
	 * Prints the enqueued script module using script tags with type="module"
	 * attributes.
	 *
	 * @since 6.9.0
	 *
	 * @param string $id The script module identifier.
	 */
	private function print_script_module( string $id ) {
		if ( in_array( $id, $this->done, true ) || ! in_array( $id, $this->queue, true ) ) {
			return;
		}

		$this->done[] = $id;

		$src = $this->get_src( $id );
		if ( '' === $src ) {
			return;
		}

		$attributes = array(
			'type' => 'module',
			'src'  => $src,
			'id'   => $id . '-js-module',
		);

		$script_module = $this->registered[ $id ];
		$dependents    = $this->get_recursive_dependents( $id );
		$fetchpriority = $this->get_highest_fetchpriority( array_merge( array( $id ), $dependents ) );
		if ( 'auto' !== $fetchpriority ) {
			$attributes['fetchpriority'] = $fetchpriority;
		}
		if ( $fetchpriority !== $script_module['fetchpriority'] ) {
			$attributes['data-wp-fetchpriority'] = $script_module['fetchpriority'];
		}
		wp_print_script_tag( $attributes );
	}

	/**
	 * Prints the static dependencies of the enqueued script modules using
	 * link tags with rel="modulepreload" attributes.
	 *
	 * If a script module is marked for enqueue, it will not be preloaded.
	 *
	 * @since 6.5.0
	 */
	public function print_script_module_preloads() {
		$dependency_ids = $this->get_sorted_dependencies( $this->queue, array( 'static' ) );
		foreach ( $dependency_ids as $id ) {
			// Don't preload if it's marked for enqueue.
			if ( in_array( $id, $this->queue, true ) ) {
				continue;
			}

			$src = $this->get_src( $id );
			if ( '' === $src ) {
				continue;
			}

			$enqueued_dependents   = array_intersect( $this->get_recursive_dependents( $id ), $this->queue );
			$highest_fetchpriority = $this->get_highest_fetchpriority( $enqueued_dependents );
			printf(
				'<link rel="modulepreload" href="%s" id="%s"',
				esc_url( $src ),
				esc_attr( $id . '-js-modulepreload' )
			);
			if ( 'auto' !== $highest_fetchpriority ) {
				printf( ' fetchpriority="%s"', esc_attr( $highest_fetchpriority ) );
			}
			if ( $highest_fetchpriority !== $this->registered[ $id ]['fetchpriority'] && 'auto' !== $this->registered[ $id ]['fetchpriority'] ) {
				printf( ' data-wp-fetchpriority="%s"', esc_attr( $this->registered[ $id ]['fetchpriority'] ) );
			}
			echo ">\n";
		}
	}

	/**
	 * Prints the import map using a script tag with a type="importmap" attribute.
	 *
	 * @since 6.5.0
	 */
	public function print_import_map() {
		$import_map = $this->get_import_map();
		if ( ! empty( $import_map['imports'] ) ) {
			wp_print_inline_script_tag(
				(string) wp_json_encode( $import_map, JSON_HEX_TAG | JSON_UNESCAPED_SLASHES ),
				array(
					'type' => 'importmap',
					'id'   => 'wp-importmap',
				)
			);
		}
	}

	/**
	 * Returns the import map array.
	 *
	 * @since 6.5.0
	 *
	 * @return array Array with an `imports` key mapping to an array of script module identifiers and their respective
	 *               URLs, including the version query.
	 */
	private function get_import_map(): array {
		$imports = array();
		foreach ( array_keys( $this->get_dependencies( $this->queue ) ) as $id ) {
			$src = $this->get_src( $id );
			if ( '' !== $src ) {
				$imports[ $id ] = $src;
			}
		}
		return array( 'imports' => $imports );
	}

	/**
	 * Retrieves the list of script modules marked for enqueue.
	 *
	 * Even though this is a private method and is unused in core, there are ecosystem plugins accessing it via the
	 * Reflection API. The ecosystem should rather use {@see self::get_queue()}.
	 *
	 * @since 6.5.0
	 *
	 * @return array<string, array> Script modules marked for enqueue, keyed by script module identifier.
	 */
	private function get_marked_for_enqueue(): array {
		return wp_array_slice_assoc(
			$this->registered,
			$this->queue
		);
	}

	/**
	 * Retrieves all the dependencies for the given script module identifiers, filtered by import types.
	 *
	 * It will consolidate an array containing a set of unique dependencies based
	 * on the requested import types: 'static', 'dynamic', or both. This method is
	 * recursive and also retrieves dependencies of the dependencies.
	 *
	 * @since 6.5.0
	 *
	 * @param string[] $ids          The identifiers of the script modules for which to gather dependencies.
	 * @param string[] $import_types Optional. Import types of dependencies to retrieve: 'static', 'dynamic', or both.
	 *                                         Default is both.
	 * @return array<string, array> List of dependencies, keyed by script module identifier.
	 */
	private function get_dependencies( array $ids, array $import_types = array( 'static', 'dynamic' ) ): array {
		$all_dependencies = array();
		$id_queue         = $ids;

		while ( ! empty( $id_queue ) ) {
			$id = array_shift( $id_queue );
			if ( ! isset( $this->registered[ $id ] ) ) {
				continue;
			}

			foreach ( $this->registered[ $id ]['dependencies'] as $dependency ) {
				if (
					! isset( $all_dependencies[ $dependency['id'] ] ) &&
					in_array( $dependency['import'], $import_types, true ) &&
					isset( $this->registered[ $dependency['id'] ] )
				) {
					$all_dependencies[ $dependency['id'] ] = $this->registered[ $dependency['id'] ];

					// Add this dependency to the list to get dependencies for.
					$id_queue[] = $dependency['id'];
				}
			}
		}

		return $all_dependencies;
	}

	/**
	 * Gets all dependents of a script module.
	 *
	 * This is not recursive.
	 *
	 * @since 6.9.0
	 *
	 * @see WP_Scripts::get_dependents()
	 *
	 * @param string $id The script ID.
	 * @return string[] Script module IDs.
	 */
	private function get_dependents( string $id ): array {
		// Check if dependents map for the handle in question is present. If so, use it.
		if ( isset( $this->dependents_map[ $id ] ) ) {
			return $this->dependents_map[ $id ];
		}

		$dependents = array();

		// Iterate over all registered scripts, finding dependents of the script passed to this method.
		foreach ( $this->registered as $registered_id => $args ) {
			if ( in_array( $id, wp_list_pluck( $args['dependencies'], 'id' ), true ) ) {
				$dependents[] = $registered_id;
			}
		}

		// Add the module's dependents to the map to ease future lookups.
		$this->dependents_map[ $id ] = $dependents;

		return $dependents;
	}

	/**
	 * Gets all recursive dependents of a script module.
	 *
	 * @since 6.9.0
	 *
	 * @see WP_Scripts::get_dependents()
	 *
	 * @param string $id The script ID.
	 * @return string[] Script module IDs.
	 */
	private function get_recursive_dependents( string $id ): array {
		$dependents = array();
		$id_queue   = array( $id );
		$processed  = array();

		while ( ! empty( $id_queue ) ) {
			$current_id = array_shift( $id_queue );

			// Skip unregistered or already-processed script modules.
			if ( ! isset( $this->registered[ $current_id ] ) || isset( $processed[ $current_id ] ) ) {
				continue;
			}

			// Mark as processed to guard against infinite loops from circular dependencies.
			$processed[ $current_id ] = true;

			// Find the direct dependents of the current script.
			foreach ( $this->get_dependents( $current_id ) as $dependent_id ) {
				// Only add the dependent if we haven't found it before.
				if ( ! isset( $dependents[ $dependent_id ] ) ) {
					$dependents[ $dependent_id ] = true;

					// Add dependency to the queue.
					$id_queue[] = $dependent_id;
				}
			}
		}

		return array_keys( $dependents );
	}

	/**
	 * Sorts the given script module identifiers based on their dependencies.
	 *
	 * It will return a list of script module identifiers sorted in the order
	 * they should be printed, so that dependencies are printed before the script
	 * modules that depend on them.
	 *
	 * @since 6.9.0
	 *
	 * @param string[] $ids          The identifiers of the script modules to sort.
	 * @param string[] $import_types Optional. Import types of dependencies to retrieve: 'static', 'dynamic', or both.
	 *                                         Default is both.
	 * @return string[] Sorted list of script module identifiers.
	 */
	private function get_sorted_dependencies( array $ids, array $import_types = array( 'static', 'dynamic' ) ): array {
		$sorted = array();

		foreach ( $ids as $id ) {
			$this->sort_item_dependencies( $id, $import_types, $sorted );
		}

		return array_unique( $sorted );
	}

	/**
	 * Recursively sorts the dependencies for a single script module identifier.
	 *
	 * @since 6.9.0
	 *
	 * @param string   $id           The identifier of the script module to sort.
	 * @param string[] $import_types Optional. Import types of dependencies to retrieve: 'static', 'dynamic', or both.
	 * @param string[] &$sorted      The array of sorted identifiers, passed by reference.
	 * @return bool True on success, false on failure (e.g., missing dependency).
	 */
	private function sort_item_dependencies( string $id, array $import_types, array &$sorted ): bool {
		// If already processed, don't do it again.
		if ( in_array( $id, $sorted, true ) ) {
			return true;
		}

		// If the item doesn't exist, fail.
		if ( ! isset( $this->registered[ $id ] ) ) {
			return false;
		}

		$dependency_ids = array();
		foreach ( $this->registered[ $id ]['dependencies'] as $dependency ) {
			if ( in_array( $dependency['import'], $import_types, true ) ) {
				$dependency_ids[] = $dependency['id'];
			}
		}

		// If the item requires dependencies that do not exist, fail.
		if ( count( array_diff( $dependency_ids, array_keys( $this->registered ) ) ) > 0 ) {
			return false;
		}

		// Recursively process dependencies.
		foreach ( $dependency_ids as $dependency_id ) {
			if ( ! $this->sort_item_dependencies( $dependency_id, $import_types, $sorted ) ) {
				// A dependency failed to resolve, so this branch fails.
				return false;
			}
		}

		// All dependencies are sorted, so we can now add the current item.
		$sorted[] = $id;

		return true;
	}

	/**
	 * Gets the versioned URL for a script module src.
	 *
	 * If $version is set to false, the version number is the currently installed
	 * WordPress version. If $version is set to null, no version is added.
	 * Otherwise, the string passed in $version is used.
	 *
	 * @since 6.5.0
	 *
	 * @param string $id The script module identifier.
	 * @return string The script module src with a version if relevant.
	 */
	private function get_src( string $id ): string {
		if ( ! isset( $this->registered[ $id ] ) ) {
			return '';
		}

		$script_module = $this->registered[ $id ];
		$src           = $script_module['src'];

		if ( '' !== $src ) {
			if ( false === $script_module['version'] ) {
				$src = add_query_arg( 'ver', get_bloginfo( 'version' ), $src );
			} elseif ( null !== $script_module['version'] ) {
				$src = add_query_arg( 'ver', $script_module['version'], $src );
			}
		}

		/**
		 * Filters the script module source.
		 *
		 * @since 6.5.0
		 *
		 * @param string $src Module source URL.
		 * @param string $id  Module identifier.
		 */
		$src = apply_filters( 'script_module_loader_src', $src, $id );
		if ( ! is_string( $src ) ) {
			$src = '';
		}

		return $src;
	}

	/**
	 * Print data associated with Script Modules.
	 *
	 * The data will be embedded in the page HTML and can be read by Script Modules on page load.
	 *
	 * @since 6.7.0
	 *
	 * Data can be associated with a Script Module via the
	 * {@see "script_module_data_{$module_id}"} filter.
	 *
	 * The data for a Script Module will be serialized as JSON in a script tag with an ID of the
	 * form `wp-script-module-data-{$module_id}`.
	 */
	public function print_script_module_data(): void {
		$modules = array();
		foreach ( array_unique( $this->queue ) as $id ) {
			if ( '@wordpress/a11y' === $id ) {
				$this->a11y_available = true;
			}
			$modules[ $id ] = true;
		}
		foreach ( array_keys( $this->get_import_map()['imports'] ) as $id ) {
			if ( '@wordpress/a11y' === $id ) {
				$this->a11y_available = true;
			}
			$modules[ $id ] = true;
		}

		foreach ( array_keys( $modules ) as $module_id ) {
			/**
			 * Filters data associated with a given Script Module.
			 *
			 * Script Modules may require data that is required for initialization or is essential
			 * to have immediately available on page load. These are suitable use cases for
			 * this data.
			 *
			 * The dynamic portion of the hook name, `$module_id`, refers to the Script Module ID
			 * that the data is associated with.
			 *
			 * This is best suited to pass essential data that must be available to the module for
			 * initialization or immediately on page load. It does not replace the REST API or
			 * fetching data from the client.
			 *
			 * Example:
			 *
			 *     add_filter(
			 *         'script_module_data_MyScriptModuleID',
			 *         function ( array $data ): array {
			 *             $data['dataForClient'] = 'ok';
			 *             return $data;
			 *         }
			 *     );
			 *
			 * If the filter returns no data (an empty array), nothing will be embedded in the page.
			 *
			 * The data for a given Script Module, if provided, will be JSON serialized in a script
			 * tag with an ID of the form `wp-script-module-data-{$module_id}`.
			 *
			 * The data can be read on the client with a pattern like this:
			 *
			 * Example:
			 *
			 *     const dataContainer = document.getElementById( 'wp-script-module-data-MyScriptModuleID' );
			 *     let data = {};
			 *     if ( dataContainer ) {
			 *         try {
			 *             data = JSON.parse( dataContainer.textContent );
			 *         } catch {}
			 *     }
			 *     // data.dataForClient === 'ok';
			 *     initMyScriptModuleWithData( data );
			 *
			 * @since 6.7.0
			 *
			 * @param array $data The data associated with the Script Module.
			 */
			$data = apply_filters( "script_module_data_{$module_id}", array() );

			if ( is_array( $data ) && array() !== $data ) {
				/*
				 * This data will be printed as JSON inside a script tag like this:
				 *   <script type="application/json"></script>
				 *
				 * A script tag must be closed by a sequence beginning with `</`. It's impossible to
				 * close a script tag without using `<`. We ensure that `<` is escaped and `/` can
				 * remain unescaped, so `</script>` will be printed as `\u003C/script\u00E3`.
				 *
				 *   - JSON_HEX_TAG: All < and > are converted to \u003C and \u003E.
				 *   - JSON_UNESCAPED_SLASHES: Don't escape /.
				 *
				 * If the page will use UTF-8 encoding, it's safe to print unescaped unicode:
				 *
				 *   - JSON_UNESCAPED_UNICODE: Encode multibyte Unicode characters literally (instead of as `\uXXXX`).
				 *   - JSON_UNESCAPED_LINE_TERMINATORS: The line terminators are kept unescaped when
				 *     JSON_UNESCAPED_UNICODE is supplied. It uses the same behaviour as it was
				 *     before PHP 7.1 without this constant. Available as of PHP 7.1.0.
				 *
				 * The JSON specification requires encoding in UTF-8, so if the generated HTML page
				 * is not encoded in UTF-8 then it's not safe to include those literals. They must
				 * be escaped to avoid encoding issues.
				 *
				 * @see https://www.rfc-editor.org/rfc/rfc8259.html for details on encoding requirements.
				 * @see https://www.php.net/manual/en/json.constants.php for details on these constants.
				 * @see https://html.spec.whatwg.org/#script-data-state for details on script tag parsing.
				 */
				$json_encode_flags = JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_LINE_TERMINATORS;
				if ( ! is_utf8_charset() ) {
					$json_encode_flags = JSON_HEX_TAG | JSON_UNESCAPED_SLASHES;
				}

				wp_print_inline_script_tag(
					(string) wp_json_encode(
						$data,
						$json_encode_flags
					),
					array(
						'type' => 'application/json',
						'id'   => "wp-script-module-data-{$module_id}",
					)
				);
			}
		}
	}

	/**
	 * @access private This is only intended to be called by the registered actions.
	 *
	 * @since 6.7.0
	 */
	public function print_a11y_script_module_html() {
		if ( ! $this->a11y_available ) {
			return;
		}
		echo '<div style="position:absolute;margin:-1px;padding:0;height:1px;width:1px;overflow:hidden;clip-path:inset(50%);border:0;word-wrap:normal !important;">'
			. '<p id="a11y-speak-intro-text" class="a11y-speak-intro-text" hidden>' . esc_html__( 'Notifications' ) . '</p>'
			. '<div id="a11y-speak-assertive" class="a11y-speak-region" aria-live="assertive" aria-relevant="additions text" aria-atomic="true"></div>'
			. '<div id="a11y-speak-polite" class="a11y-speak-region" aria-live="polite" aria-relevant="additions text" aria-atomic="true"></div>'
			. '</div>';
	}
}
