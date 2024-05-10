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
	 * @var array[]
	 */
	private $registered = array();

	/**
	 * Holds the script module identifiers that were enqueued before registered.
	 *
	 * @since 6.5.0
	 * @var array<string, true>
	 */
	private $enqueued_before_registered = array();

	/**
	 * Registers the script module if no script module with that script module
	 * identifier has already been registered.
	 *
	 * @since 6.5.0
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
	 */
	public function register( string $id, string $src, array $deps = array(), $version = false ) {
		if ( ! isset( $this->registered[ $id ] ) ) {
			$dependencies = array();
			foreach ( $deps as $dependency ) {
				if ( is_array( $dependency ) ) {
					if ( ! isset( $dependency['id'] ) ) {
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

			$this->registered[ $id ] = array(
				'src'          => $src,
				'version'      => $version,
				'enqueue'      => isset( $this->enqueued_before_registered[ $id ] ),
				'dependencies' => $dependencies,
			);
		}
	}

	/**
	 * Marks the script module to be enqueued in the page.
	 *
	 * If a src is provided and the script module has not been registered yet, it
	 * will be registered.
	 *
	 * @since 6.5.0
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
	 */
	public function enqueue( string $id, string $src = '', array $deps = array(), $version = false ) {
		if ( isset( $this->registered[ $id ] ) ) {
			$this->registered[ $id ]['enqueue'] = true;
		} elseif ( $src ) {
			$this->register( $id, $src, $deps, $version );
			$this->registered[ $id ]['enqueue'] = true;
		} else {
			$this->enqueued_before_registered[ $id ] = true;
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
		if ( isset( $this->registered[ $id ] ) ) {
			$this->registered[ $id ]['enqueue'] = false;
		}
		unset( $this->enqueued_before_registered[ $id ] );
	}

	/**
	 * Removes a registered script module.
	 *
	 * @since 6.5.0
	 *
	 * @param string $id The identifier of the script module.
	 */
	public function deregister( string $id ) {
		unset( $this->registered[ $id ] );
		unset( $this->enqueued_before_registered[ $id ] );
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
		$position = wp_is_block_theme() ? 'wp_head' : 'wp_footer';
		add_action( $position, array( $this, 'print_import_map' ) );
		add_action( $position, array( $this, 'print_enqueued_script_modules' ) );
		add_action( $position, array( $this, 'print_script_module_preloads' ) );

		add_action( 'admin_print_footer_scripts', array( $this, 'print_import_map' ) );
		add_action( 'admin_print_footer_scripts', array( $this, 'print_enqueued_script_modules' ) );
		add_action( 'admin_print_footer_scripts', array( $this, 'print_script_module_preloads' ) );
	}

	/**
	 * Prints the enqueued script modules using script tags with type="module"
	 * attributes.
	 *
	 * @since 6.5.0
	 */
	public function print_enqueued_script_modules() {
		foreach ( $this->get_marked_for_enqueue() as $id => $script_module ) {
			wp_print_script_tag(
				array(
					'type' => 'module',
					'src'  => $this->get_src( $id ),
					'id'   => $id . '-js-module',
				)
			);
		}
	}

	/**
	 * Prints the the static dependencies of the enqueued script modules using
	 * link tags with rel="modulepreload" attributes.
	 *
	 * If a script module is marked for enqueue, it will not be preloaded.
	 *
	 * @since 6.5.0
	 */
	public function print_script_module_preloads() {
		foreach ( $this->get_dependencies( array_keys( $this->get_marked_for_enqueue() ), array( 'static' ) ) as $id => $script_module ) {
			// Don't preload if it's marked for enqueue.
			if ( true !== $script_module['enqueue'] ) {
				echo sprintf(
					'<link rel="modulepreload" href="%s" id="%s">',
					esc_url( $this->get_src( $id ) ),
					esc_attr( $id . '-js-modulepreload' )
				);
			}
		}
	}

	/**
	 * Prints the import map using a script tag with a type="importmap" attribute.
	 *
	 * @since 6.5.0
	 *
	 * @global WP_Scripts $wp_scripts The WP_Scripts object for printing the polyfill.
	 */
	public function print_import_map() {
		$import_map = $this->get_import_map();
		if ( ! empty( $import_map['imports'] ) ) {
			global $wp_scripts;
			if ( isset( $wp_scripts ) ) {
				wp_print_inline_script_tag(
					wp_get_script_polyfill(
						$wp_scripts,
						array(
							'HTMLScriptElement.supports && HTMLScriptElement.supports("importmap")' => 'wp-polyfill-importmap',
						)
					),
					array(
						'id' => 'wp-load-polyfill-importmap',
					)
				);
			}
			wp_print_inline_script_tag(
				wp_json_encode( $import_map, JSON_HEX_TAG | JSON_HEX_AMP ),
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
		foreach ( $this->get_dependencies( array_keys( $this->get_marked_for_enqueue() ) ) as $id => $script_module ) {
			$imports[ $id ] = $this->get_src( $id );
		}
		return array( 'imports' => $imports );
	}

	/**
	 * Retrieves the list of script modules marked for enqueue.
	 *
	 * @since 6.5.0
	 *
	 * @return array[] Script modules marked for enqueue, keyed by script module identifier.
	 */
	private function get_marked_for_enqueue(): array {
		$enqueued = array();
		foreach ( $this->registered as $id => $script_module ) {
			if ( true === $script_module['enqueue'] ) {
				$enqueued[ $id ] = $script_module;
			}
		}
		return $enqueued;
	}

	/**
	 * Retrieves all the dependencies for the given script module identifiers,
	 * filtered by import types.
	 *
	 * It will consolidate an array containing a set of unique dependencies based
	 * on the requested import types: 'static', 'dynamic', or both. This method is
	 * recursive and also retrieves dependencies of the dependencies.
	 *
	 * @since 6.5.0
	 *
	 * @param string[] $ids          The identifiers of the script modules for which to gather dependencies.
	 * @param string[] $import_types Optional. Import types of dependencies to retrieve: 'static', 'dynamic', or both.
	 *                               Default is both.
	 * @return array[] List of dependencies, keyed by script module identifier.
	 */
	private function get_dependencies( array $ids, array $import_types = array( 'static', 'dynamic' ) ) {
		return array_reduce(
			$ids,
			function ( $dependency_script_modules, $id ) use ( $import_types ) {
				$dependencies = array();
				foreach ( $this->registered[ $id ]['dependencies'] as $dependency ) {
					if (
					in_array( $dependency['import'], $import_types, true ) &&
					isset( $this->registered[ $dependency['id'] ] ) &&
					! isset( $dependency_script_modules[ $dependency['id'] ] )
					) {
						$dependencies[ $dependency['id'] ] = $this->registered[ $dependency['id'] ];
					}
				}
				return array_merge( $dependency_script_modules, $dependencies, $this->get_dependencies( array_keys( $dependencies ), $import_types ) );
			},
			array()
		);
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

		if ( false === $script_module['version'] ) {
			$src = add_query_arg( 'ver', get_bloginfo( 'version' ), $src );
		} elseif ( null !== $script_module['version'] ) {
			$src = add_query_arg( 'ver', $script_module['version'], $src );
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

		return $src;
	}
}
