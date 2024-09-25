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

		add_action( 'wp_footer', array( $this, 'print_script_module_data' ) );
		add_action( 'admin_print_footer_scripts', array( $this, 'print_script_module_data' ) );
		add_action( 'wp_footer', array( $this, 'print_a11y_script_module_html' ), 20 );
		add_action( 'admin_print_footer_scripts', array( $this, 'print_a11y_script_module_html' ), 20 );
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
	 */
	public function print_import_map() {
		$import_map = $this->get_import_map();
		if ( ! empty( $import_map['imports'] ) ) {
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
		foreach ( array_keys( $this->get_marked_for_enqueue() ) as $id ) {
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
			 * @example
			 *   add_filter(
			 *     'script_module_data_MyScriptModuleID',
			 *     function ( array $data ): array {
			 *       $data['script-needs-this-data'] = 'ok';
			 *       return $data;
			 *     }
			 *   );
			 *
			 * If the filter returns no data (an empty array), nothing will be embedded in the page.
			 *
			 * The data for a given Script Module, if provided, will be JSON serialized in a script
			 * tag with an ID of the form `wp-script-module-data-{$module_id}`.
			 *
			 * The data can be read on the client with a pattern like this:
			 *
			 * @example
			 *   const dataContainer = document.getElementById( 'wp-script-module-data-MyScriptModuleID' );
			 *   let data = {};
			 *   if ( dataContainer ) {
			 *     try {
			 *       data = JSON.parse( dataContainer.textContent );
			 *     } catch {}
			 *   }
			 *   initMyScriptModuleWithData( data );
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
					wp_json_encode(
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
