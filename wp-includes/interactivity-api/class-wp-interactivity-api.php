<?php
/**
 * Interactivity API: WP_Interactivity_API class.
 *
 * @package WordPress
 * @subpackage Interactivity API
 * @since 6.5.0
 */

/**
 * Class used to process the Interactivity API on the server.
 *
 * @since 6.5.0
 */
final class WP_Interactivity_API {
	/**
	 * Holds the mapping of directive attribute names to their processor methods.
	 *
	 * @since 6.5.0
	 * @var array
	 */
	private static $directive_processors = array(
		'data-wp-interactive'   => 'data_wp_interactive_processor',
		'data-wp-router-region' => 'data_wp_router_region_processor',
		'data-wp-context'       => 'data_wp_context_processor',
		'data-wp-bind'          => 'data_wp_bind_processor',
		'data-wp-class'         => 'data_wp_class_processor',
		'data-wp-style'         => 'data_wp_style_processor',
		'data-wp-text'          => 'data_wp_text_processor',
		/*
		 * `data-wp-each` needs to be processed in the last place because it moves
		 * the cursor to the end of the processed items to prevent them to be
		 * processed twice.
		 */
		'data-wp-each'          => 'data_wp_each_processor',
	);

	/**
	 * Holds the initial state of the different Interactivity API stores.
	 *
	 * This state is used during the server directive processing. Then, it is
	 * serialized and sent to the client as part of the interactivity data to be
	 * recovered during the hydration of the client interactivity stores.
	 *
	 * @since 6.5.0
	 * @var array
	 */
	private $state_data = array();

	/**
	 * Holds the configuration required by the different Interactivity API stores.
	 *
	 * This configuration is serialized and sent to the client as part of the
	 * interactivity data and can be accessed by the client interactivity stores.
	 *
	 * @since 6.5.0
	 * @var array
	 */
	private $config_data = array();

	/**
	 * Keeps track of all derived state closures accessed during server-side rendering.
	 *
	 * This data is serialized and sent to the client as part of the interactivity
	 * data, and is handled later in the client to support derived state props that
	 * are lazily hydrated.
	 *
	 * @since 6.9.0
	 * @var array
	 */
	private $derived_state_closures = array();

	/**
	 * Flag that indicates whether the `data-wp-router-region` directive has
	 * been found in the HTML and processed.
	 *
	 * The value is saved in a private property of the WP_Interactivity_API
	 * instance instead of using a static variable inside the processor
	 * function, which would hold the same value for all instances
	 * independently of whether they have processed any
	 * `data-wp-router-region` directive or not.
	 *
	 * @since 6.5.0
	 * @var bool
	 */
	private $has_processed_router_region = false;

	/**
	 * Set of script modules that can be loaded after client-side navigation.
	 *
	 * @since 6.9.0
	 * @var array<string, true>
	 */
	private $script_modules_that_can_load_on_client_navigation = array();

	/**
	 * Stack of namespaces defined by `data-wp-interactive` directives, in
	 * the order they are processed.
	 *
	 * This is only available during directive processing, otherwise it is `null`.
	 *
	 * @since 6.6.0
	 * @var array<string>|null
	 */
	private $namespace_stack = null;

	/**
	 * Stack of contexts defined by `data-wp-context` directives, in
	 * the order they are processed.
	 *
	 * This is only available during directive processing, otherwise it is `null`.
	 *
	 * @since 6.6.0
	 * @var array<array<mixed>>|null
	 */
	private $context_stack = null;

	/**
	 * Representation in array format of the element currently being processed.
	 *
	 * This is only available during directive processing, otherwise it is `null`.
	 *
	 * @since 6.7.0
	 * @var array{attributes: array<string, string|bool>}|null
	 */
	private $current_element = null;

	/**
	 * Gets and/or sets the initial state of an Interactivity API store for a
	 * given namespace.
	 *
	 * If state for that store namespace already exists, it merges the new
	 * provided state with the existing one.
	 *
	 * When no namespace is specified, it returns the state defined for the
	 * current value in the internal namespace stack during a `process_directives` call.
	 *
	 * @since 6.5.0
	 * @since 6.6.0 The `$store_namespace` param is optional.
	 *
	 * @param string $store_namespace Optional. The unique store namespace identifier.
	 * @param array  $state           Optional. The array that will be merged with the existing state for the specified
	 *                                store namespace.
	 * @return array The current state for the specified store namespace. This will be the updated state if a $state
	 *               argument was provided.
	 */
	public function state( ?string $store_namespace = null, ?array $state = null ): array {
		if ( ! $store_namespace ) {
			if ( $state ) {
				_doing_it_wrong(
					__METHOD__,
					__( 'The namespace is required when state data is passed.' ),
					'6.6.0'
				);
				return array();
			}
			if ( null !== $store_namespace ) {
				_doing_it_wrong(
					__METHOD__,
					__( 'The namespace should be a non-empty string.' ),
					'6.6.0'
				);
				return array();
			}
			if ( null === $this->namespace_stack ) {
				_doing_it_wrong(
					__METHOD__,
					__( 'The namespace can only be omitted during directive processing.' ),
					'6.6.0'
				);
				return array();
			}

			$store_namespace = end( $this->namespace_stack );
		}
		if ( ! isset( $this->state_data[ $store_namespace ] ) ) {
			$this->state_data[ $store_namespace ] = array();
		}
		if ( is_array( $state ) ) {
			$this->state_data[ $store_namespace ] = array_replace_recursive(
				$this->state_data[ $store_namespace ],
				$state
			);
		}
		return $this->state_data[ $store_namespace ];
	}

	/**
	 * Gets and/or sets the configuration of the Interactivity API for a given
	 * store namespace.
	 *
	 * If configuration for that store namespace exists, it merges the new
	 * provided configuration with the existing one.
	 *
	 * @since 6.5.0
	 *
	 * @param string $store_namespace The unique store namespace identifier.
	 * @param array  $config          Optional. The array that will be merged with the existing configuration for the
	 *                                specified store namespace.
	 * @return array The configuration for the specified store namespace. This will be the updated configuration if a
	 *               $config argument was provided.
	 */
	public function config( string $store_namespace, array $config = array() ): array {
		if ( ! isset( $this->config_data[ $store_namespace ] ) ) {
			$this->config_data[ $store_namespace ] = array();
		}
		if ( is_array( $config ) ) {
			$this->config_data[ $store_namespace ] = array_replace_recursive(
				$this->config_data[ $store_namespace ],
				$config
			);
		}
		return $this->config_data[ $store_namespace ];
	}

	/**
	 * Prints the serialized client-side interactivity data.
	 *
	 * Encodes the config and initial state into JSON and prints them inside a
	 * script tag of type "application/json". Once in the browser, the state will
	 * be parsed and used to hydrate the client-side interactivity stores and the
	 * configuration will be available using a `getConfig` utility.
	 *
	 * @since 6.5.0
	 *
	 * @deprecated 6.7.0 Client data passing is handled by the {@see "script_module_data_{$module_id}"} filter.
	 */
	public function print_client_interactivity_data() {
		_deprecated_function( __METHOD__, '6.7.0' );
	}

	/**
	 * Set client-side interactivity-router data.
	 *
	 * Once in the browser, the state will be parsed and used to hydrate the client-side
	 * interactivity stores and the configuration will be available using a `getConfig` utility.
	 *
	 * @since 6.7.0
	 *
	 * @param array $data Data to filter.
	 * @return array Data for the Interactivity Router script module.
	 */
	public function filter_script_module_interactivity_router_data( array $data ): array {
		if ( ! isset( $data['i18n'] ) ) {
			$data['i18n'] = array();
		}
		$data['i18n']['loading'] = __( 'Loading page, please wait.' );
		$data['i18n']['loaded']  = __( 'Page Loaded.' );
		return $data;
	}

	/**
	 * Set client-side interactivity data.
	 *
	 * Once in the browser, the state will be parsed and used to hydrate the client-side
	 * interactivity stores and the configuration will be available using a `getConfig` utility.
	 *
	 * @since 6.7.0
	 * @since 6.9.0 Serializes derived state props accessed during directive processing.
	 *
	 * @param array $data Data to filter.
	 * @return array Data for the Interactivity API script module.
	 */
	public function filter_script_module_interactivity_data( array $data ): array {
		if (
			empty( $this->state_data ) &&
			empty( $this->config_data ) &&
			empty( $this->derived_state_closures )
		) {
			return $data;
		}

		$config = array();
		foreach ( $this->config_data as $key => $value ) {
			if ( ! empty( $value ) ) {
				$config[ $key ] = $value;
			}
		}
		if ( ! empty( $config ) ) {
			$data['config'] = $config;
		}

		$state = array();
		foreach ( $this->state_data as $key => $value ) {
			if ( ! empty( $value ) ) {
				$state[ $key ] = $value;
			}
		}
		if ( ! empty( $state ) ) {
			$data['state'] = $state;
		}

		$derived_props = array();
		foreach ( $this->derived_state_closures as $key => $value ) {
			if ( ! empty( $value ) ) {
				$derived_props[ $key ] = $value;
			}
		}
		if ( ! empty( $derived_props ) ) {
			$data['derivedStateClosures'] = $derived_props;
		}

		return $data;
	}

	/**
	 * Returns the latest value on the context stack with the passed namespace.
	 *
	 * When the namespace is omitted, it uses the current namespace on the
	 * namespace stack during a `process_directives` call.
	 *
	 * @since 6.6.0
	 *
	 * @param string $store_namespace Optional. The unique store namespace identifier.
	 */
	public function get_context( ?string $store_namespace = null ): array {
		if ( null === $this->context_stack ) {
			_doing_it_wrong(
				__METHOD__,
				__( 'The context can only be read during directive processing.' ),
				'6.6.0'
			);
			return array();
		}

		if ( ! $store_namespace ) {
			if ( null !== $store_namespace ) {
				_doing_it_wrong(
					__METHOD__,
					__( 'The namespace should be a non-empty string.' ),
					'6.6.0'
				);
				return array();
			}

			$store_namespace = end( $this->namespace_stack );
		}

		$context = end( $this->context_stack );

		return ( $store_namespace && $context && isset( $context[ $store_namespace ] ) )
			? $context[ $store_namespace ]
			: array();
	}

	/**
	 * Returns an array representation of the current element being processed.
	 *
	 * The returned array contains a copy of the element attributes.
	 *
	 * @since 6.7.0
	 *
	 * @return array{attributes: array<string, string|bool>}|null Current element.
	 */
	public function get_element(): ?array {
		if ( null === $this->current_element ) {
			_doing_it_wrong(
				__METHOD__,
				__( 'The element can only be read during directive processing.' ),
				'6.7.0'
			);
		}

		return $this->current_element;
	}

	/**
	 * Registers the `@wordpress/interactivity` script modules.
	 *
	 * @deprecated 6.7.0 Script Modules registration is handled by {@see wp_default_script_modules()}.
	 *
	 * @since 6.5.0
	 */
	public function register_script_modules() {
		_deprecated_function( __METHOD__, '6.7.0', 'wp_default_script_modules' );
	}

	/**
	 * Adds the necessary hooks for the Interactivity API.
	 *
	 * @since 6.5.0
	 * @since 6.9.0 Adds support for client-side navigation in script modules.
	 */
	public function add_hooks() {
		add_filter( 'script_module_data_@wordpress/interactivity', array( $this, 'filter_script_module_interactivity_data' ) );
		add_filter( 'script_module_data_@wordpress/interactivity-router', array( $this, 'filter_script_module_interactivity_router_data' ) );
		add_filter( 'wp_script_attributes', array( $this, 'add_load_on_client_navigation_attribute_to_script_modules' ), 10, 1 );
	}

	/**
	 * Adds the `data-wp-router-options` attribute to script modules that
	 * support client-side navigation.
	 *
	 * This method filters the script attributes to include loading instructions
	 * for the Interactivity API router, indicating which modules can be loaded
	 * during client-side navigation.
	 *
	 * @since 6.9.0
	 *
	 * @param array<string, string|true>|mixed $attributes The script tag attributes.
	 * @return array The modified script tag attributes.
	 */
	public function add_load_on_client_navigation_attribute_to_script_modules( $attributes ) {
		if (
			is_array( $attributes ) &&
			isset( $attributes['type'], $attributes['id'] ) &&
			'module' === $attributes['type'] &&
			array_key_exists(
				preg_replace( '/-js-module$/', '', $attributes['id'] ),
				$this->script_modules_that_can_load_on_client_navigation
			)
		) {
			$attributes['data-wp-router-options'] = wp_json_encode( array( 'loadOnClientNavigation' => true ) );
		}
		return $attributes;
	}

	/**
	 * Marks a script module as compatible with client-side navigation.
	 *
	 * This method registers a script module to be loaded during client-side
	 * navigation in the Interactivity API router. Script modules marked with
	 * this method will have the `loadOnClientNavigation` option enabled in the
	 * `data-wp-router-options` directive.
	 *
	 * @since 6.9.0
	 *
	 * @param string $script_module_id The script module identifier.
	 */
	public function add_client_navigation_support_to_script_module( string $script_module_id ) {
		$this->script_modules_that_can_load_on_client_navigation[ $script_module_id ] = true;
	}

	/**
	 * Processes the interactivity directives contained within the HTML content
	 * and updates the markup accordingly.
	 *
	 * @since 6.5.0
	 *
	 * @param string $html The HTML content to process.
	 * @return string The processed HTML content. It returns the original content when the HTML contains unbalanced tags.
	 */
	public function process_directives( string $html ): string {
		if ( ! str_contains( $html, 'data-wp-' ) ) {
			return $html;
		}

		$this->namespace_stack = array();
		$this->context_stack   = array();

		$result = $this->_process_directives( $html );

		$this->namespace_stack = null;
		$this->context_stack   = null;

		return null === $result ? $html : $result;
	}

	/**
	 * Processes the interactivity directives contained within the HTML content
	 * and updates the markup accordingly.
	 *
	 * It uses the WP_Interactivity_API instance's context and namespace stacks,
	 * which are shared between all calls.
	 *
	 * This method returns null if the HTML contains unbalanced tags.
	 *
	 * @since 6.6.0
	 *
	 * @param string $html The HTML content to process.
	 * @return string|null The processed HTML content. It returns null when the HTML contains unbalanced tags.
	 */
	private function _process_directives( string $html ) {
		$p          = new WP_Interactivity_API_Directives_Processor( $html );
		$tag_stack  = array();
		$unbalanced = false;

		$directive_processor_prefixes          = array_keys( self::$directive_processors );
		$directive_processor_prefixes_reversed = array_reverse( $directive_processor_prefixes );

		/*
		 * Save the current size for each stack to restore them in case
		 * the processing finds unbalanced tags.
		 */
		$namespace_stack_size = count( $this->namespace_stack );
		$context_stack_size   = count( $this->context_stack );

		while ( $p->next_tag( array( 'tag_closers' => 'visit' ) ) ) {
			$tag_name = $p->get_tag();

			/*
			 * Directives inside SVG and MATH tags are not processed,
			 * as they are not compatible with the Tag Processor yet.
			 * We still process the rest of the HTML.
			 */
			if ( 'SVG' === $tag_name || 'MATH' === $tag_name ) {
				if ( $p->get_attribute_names_with_prefix( 'data-wp-' ) ) {
					/* translators: 1: SVG or MATH HTML tag, 2: Namespace of the interactive block. */
					$message = sprintf( __( 'Interactivity directives were detected on an incompatible %1$s tag when processing "%2$s". These directives will be ignored in the server side render.' ), $tag_name, end( $this->namespace_stack ) );
					_doing_it_wrong( __METHOD__, $message, '6.6.0' );
				}
				$p->skip_to_tag_closer();
				continue;
			}

			if ( $p->is_tag_closer() ) {
				list( $opening_tag_name, $directives_prefixes ) = ! empty( $tag_stack ) ? end( $tag_stack ) : array( null, null );

				if ( 0 === count( $tag_stack ) || $opening_tag_name !== $tag_name ) {

					/*
					 * If the tag stack is empty or the matching opening tag is not the
					 * same than the closing tag, it means the HTML is unbalanced and it
					 * stops processing it.
					 */
					$unbalanced = true;
					break;
				} else {
					// Remove the last tag from the stack.
					array_pop( $tag_stack );
				}
			} else {
				$each_child_attrs = $p->get_attribute_names_with_prefix( 'data-wp-each-child' );
				if ( null === $each_child_attrs ) {
					continue;
				}

				if ( 0 !== count( $each_child_attrs ) ) {
					/*
					 * If the tag has a `data-wp-each-child` directive, jump to its closer
					 * tag because those tags have already been processed.
					 */
					$p->next_balanced_tag_closer_tag();
					continue;
				} else {
					$directives_prefixes = array();

					// Checks if there is a server directive processor registered for each directive.
					foreach ( $p->get_attribute_names_with_prefix( 'data-wp-' ) as $attribute_name ) {
						$parsed_directive = $this->parse_directive_name( $attribute_name );
						if ( empty( $parsed_directive ) ) {
							continue;
						}
						$directive_prefix = 'data-wp-' . $parsed_directive['prefix'];
						if ( array_key_exists( $directive_prefix, self::$directive_processors ) ) {
							$directives_prefixes[] = $directive_prefix;
						}
					}

					/*
					 * If this tag will visit its closer tag, it adds it to the tag stack
					 * so it can process its closing tag and check for unbalanced tags.
					 */
					if ( $p->has_and_visits_its_closer_tag() ) {
						$tag_stack[] = array( $tag_name, $directives_prefixes );
					}
				}
			}
			/*
			 * If the matching opener tag didn't have any directives, it can skip the
			 * processing.
			 */
			if ( 0 === count( $directives_prefixes ) ) {
				continue;
			}

			// Directive processing might be different depending on if it is entering the tag or exiting it.
			$modes = array(
				'enter' => ! $p->is_tag_closer(),
				'exit'  => $p->is_tag_closer() || ! $p->has_and_visits_its_closer_tag(),
			);

			// Get the element attributes to include them in the element representation.
			$element_attrs = array();
			$attr_names    = $p->get_attribute_names_with_prefix( '' ) ?? array();

			foreach ( $attr_names as $name ) {
				$element_attrs[ $name ] = $p->get_attribute( $name );
			}

			// Assign the current element right before running its directive processors.
			$this->current_element = array(
				'attributes' => $element_attrs,
			);

			foreach ( $modes as $mode => $should_run ) {
				if ( ! $should_run ) {
					continue;
				}

				/*
				 * Sorts the attributes by the order of the `directives_processor` array
				 * and checks what directives are present in this element.
				 */
				$existing_directives_prefixes = array_intersect(
					'enter' === $mode ? $directive_processor_prefixes : $directive_processor_prefixes_reversed,
					$directives_prefixes
				);
				foreach ( $existing_directives_prefixes as $directive_prefix ) {
					$func = is_array( self::$directive_processors[ $directive_prefix ] )
						? self::$directive_processors[ $directive_prefix ]
						: array( $this, self::$directive_processors[ $directive_prefix ] );

					call_user_func_array( $func, array( $p, $mode, &$tag_stack ) );
				}
			}

			// Clear the current element.
			$this->current_element = null;
		}

		if ( $unbalanced ) {
			// Reset the namespace and context stacks to their previous values.
			array_splice( $this->namespace_stack, $namespace_stack_size );
			array_splice( $this->context_stack, $context_stack_size );
		}

		/*
		 * It returns null if the HTML is unbalanced because unbalanced HTML is
		 * not safe to process. In that case, the Interactivity API runtime will
		 * update the HTML on the client side during the hydration. It will display
		 * a notice to the developer in the console to inform them about the issue.
		 */
		if ( $unbalanced || 0 < count( $tag_stack ) ) {
			return null;
		}

		return $p->get_updated_html();
	}

	/**
	 * Evaluates the reference path passed to a directive based on the current
	 * store namespace, state and context.
	 *
	 * @since 6.5.0
	 * @since 6.6.0 The function now adds a warning when the namespace is null, falsy, or the directive value is empty.
	 * @since 6.6.0 Removed `default_namespace` and `context` arguments.
	 * @since 6.6.0 Add support for derived state.
	 * @since 6.9.0 Recieve $entry as an argument instead of the directive value string.
	 *
	 * @param array $entry An array containing a whole directive entry with its namespace, value, suffix, or unique ID.
	 * @return mixed|null The result of the evaluation. Null if the reference path doesn't exist or the namespace is falsy.
	 */
	private function evaluate( $entry ) {
		$context                               = end( $this->context_stack );
		['namespace' => $ns, 'value' => $path] = $entry;

		if ( ! $ns || ! $path ) {
			/* translators: %s: The directive value referenced. */
			$message = sprintf( __( 'Namespace or reference path cannot be empty. Directive value referenced: %s' ), json_encode( $entry ) );
			_doing_it_wrong( __METHOD__, $message, '6.6.0' );
			return null;
		}

		$store = array(
			'state'   => $this->state_data[ $ns ] ?? array(),
			'context' => $context[ $ns ] ?? array(),
		);

		// Checks if the reference path is preceded by a negation operator (!).
		$should_negate_value = '!' === $path[0];
		$path                = $should_negate_value ? substr( $path, 1 ) : $path;

		// Extracts the value from the store using the reference path.
		$path_segments = explode( '.', $path );
		$current       = $store;
		foreach ( $path_segments as $index => $path_segment ) {
			/*
			 * Special case for numeric arrays and strings. Add length
			 * property mimicking JavaScript behavior.
			 *
			 * @since 6.8.0
			 */
			if ( 'length' === $path_segment ) {
				if ( is_array( $current ) && array_is_list( $current ) ) {
					$current = count( $current );
					break;
				}

				if ( is_string( $current ) ) {
					/*
					 * Differences in encoding between PHP strings and
					 * JavaScript mean that it's complicated to calculate
					 * the string length JavaScript would see from PHP.
					 * `strlen` is a reasonable approximation.
					 *
					 * Users that desire a more precise length likely have
					 * more precise needs than "bytelength" and should
					 * implement their own length calculation in derived
					 * state taking into account encoding and their desired
					 * output (codepoints, graphemes, bytes, etc.).
					 */
					$current = strlen( $current );
					break;
				}
			}

			if ( ( is_array( $current ) || $current instanceof ArrayAccess ) && isset( $current[ $path_segment ] ) ) {
				$current = $current[ $path_segment ];
			} elseif ( is_object( $current ) && isset( $current->$path_segment ) ) {
				$current = $current->$path_segment;
			} else {
				$current = null;
				break;
			}

			if ( $current instanceof Closure ) {
				/*
				 * This state getter's namespace is added to the stack so that
				 * `state()` or `get_config()` read that namespace when called
				 * without specifying one.
				 */
				array_push( $this->namespace_stack, $ns );
				try {
					$current = $current();

					/*
					 * Tracks derived state properties that are accessed during
					 * rendering.
					 *
					 * @since 6.9.0
					 */
					$this->derived_state_closures[ $ns ] = $this->derived_state_closures[ $ns ] ?? array();

					// Builds path for the current property and add it to tracking if not already present.
					$current_path = implode( '.', array_slice( $path_segments, 0, $index + 1 ) );
					if ( ! in_array( $current_path, $this->derived_state_closures[ $ns ], true ) ) {
						$this->derived_state_closures[ $ns ][] = $current_path;
					}
				} catch ( Throwable $e ) {
					_doing_it_wrong(
						__METHOD__,
						sprintf(
							/* translators: 1: Path pointing to an Interactivity API state property, 2: Namespace for an Interactivity API store. */
							__( 'Uncaught error executing a derived state callback with path "%1$s" and namespace "%2$s".' ),
							$path,
							$ns
						),
						'6.6.0'
					);
					return null;
				} finally {
					// Remove the property's namespace from the stack.
					array_pop( $this->namespace_stack );
				}
			}
		}

		// Returns the opposite if it contains a negation operator (!).
		return $should_negate_value ? ! $current : $current;
	}

	/**
	 * Parse the directive name to extract the following parts:
	 * - Prefix: The main directive name without "data-wp-".
	 * - Suffix: An optional suffix used during directive processing, extracted after the first double hyphen "--".
	 * - Unique ID: An optional unique identifier, extracted after the first triple hyphen "---".
	 *
	 * This function has an equivalent version for the client side.
	 * See `parseDirectiveName` in https://github.com/WordPress/gutenberg/blob/trunk/packages/interactivity/src/vdom.ts.:
	 *
	 * See examples in the function unit tests `test_parse_directive_name`.
	 *
	 * @since 6.9.0
	 *
	 * @param string $directive_name The directive attribute name.
	 * @return array An array containing the directive prefix, optional suffix, and optional unique ID.
	 */
	private function parse_directive_name( string $directive_name ): ?array {
		// Remove the first 8 characters (assumes "data-wp-" prefix)
		$name = substr( $directive_name, 8 );

		// Check for invalid characters (anything not a-z, 0-9, -, or _)
		if ( preg_match( '/[^a-z0-9\-_]/i', $name ) ) {
			return null;
		}

		// Find the first occurrence of '--' to separate the prefix
		$suffix_index = strpos( $name, '--' );

		if ( false === $suffix_index ) {
			return array(
				'prefix'    => $name,
				'suffix'    => null,
				'unique_id' => null,
			);
		}

		$prefix    = substr( $name, 0, $suffix_index );
		$remaining = substr( $name, $suffix_index );

		// If remaining starts with '---' but not '----', it's a unique_id
		if ( '---' === substr( $remaining, 0, 3 ) && '-' !== ( $remaining[3] ?? '' ) ) {
			return array(
				'prefix'    => $prefix,
				'suffix'    => null,
				'unique_id' => '---' !== $remaining ? substr( $remaining, 3 ) : null,
			);
		}

		// Otherwise, remove the first two dashes for a potential suffix
		$suffix = substr( $remaining, 2 );

		// Look for '---' in the suffix for a unique_id
		$unique_id_index = strpos( $suffix, '---' );

		if ( false !== $unique_id_index && '-' !== ( $suffix[ $unique_id_index + 3 ] ?? '' ) ) {
			$unique_id = substr( $suffix, $unique_id_index + 3 );
			$suffix    = substr( $suffix, 0, $unique_id_index );
			return array(
				'prefix'    => $prefix,
				'suffix'    => empty( $suffix ) ? null : $suffix,
				'unique_id' => empty( $unique_id ) ? null : $unique_id,
			);
		}

		return array(
			'prefix'    => $prefix,
			'suffix'    => empty( $suffix ) ? null : $suffix,
			'unique_id' => null,
		);
	}

	/**
	 * Parses and extracts the namespace and reference path from the given
	 * directive attribute value.
	 *
	 * If the value doesn't contain an explicit namespace, it returns the
	 * default one. If the value contains a JSON object instead of a reference
	 * path, the function tries to parse it and return the resulting array. If
	 * the value contains strings that represent booleans ("true" and "false"),
	 * numbers ("1" and "1.2") or "null", the function also transform them to
	 * regular booleans, numbers and `null`.
	 *
	 * Example:
	 *
	 *     extract_directive_value( 'actions.foo', 'myPlugin' )                      => array( 'myPlugin', 'actions.foo' )
	 *     extract_directive_value( 'otherPlugin::actions.foo', 'myPlugin' )         => array( 'otherPlugin', 'actions.foo' )
	 *     extract_directive_value( '{ "isOpen": false }', 'myPlugin' )              => array( 'myPlugin', array( 'isOpen' => false ) )
	 *     extract_directive_value( 'otherPlugin::{ "isOpen": false }', 'myPlugin' ) => array( 'otherPlugin', array( 'isOpen' => false ) )
	 *
	 * @since 6.5.0
	 *
	 * @param string|true $directive_value   The directive attribute value. It can be `true` when it's a boolean
	 *                                       attribute.
	 * @param string|null $default_namespace Optional. The default namespace if none is explicitly defined.
	 * @return array An array containing the namespace in the first item and the JSON, the reference path, or null on the
	 *               second item.
	 */
	private function extract_directive_value( $directive_value, $default_namespace = null ): array {
		if ( empty( $directive_value ) || is_bool( $directive_value ) ) {
			return array( $default_namespace, null );
		}

		// Replaces the value and namespace if there is a namespace in the value.
		if ( 1 === preg_match( '/^([\w\-_\/]+)::./', $directive_value ) ) {
			list($default_namespace, $directive_value) = explode( '::', $directive_value, 2 );
		}

		/*
		 * Tries to decode the value as a JSON object. If it fails and the value
		 * isn't `null`, it returns the value as it is. Otherwise, it returns the
		 * decoded JSON or null for the string `null`.
		 */
		$decoded_json = json_decode( $directive_value, true );
		if ( null !== $decoded_json || 'null' === $directive_value ) {
			$directive_value = $decoded_json;
		}

		return array( $default_namespace, $directive_value );
	}

	/**
	 * Parse the HTML element and get all the valid directives with the given prefix.
	 *
	 * @since 6.9.0
	 *
	 * @param WP_Interactivity_API_Directives_Processor $p      The directives processor instance.
	 * @param string                                    $prefix The directive prefix to filter by.
	 * @return array An array of entries containing the directive namespace, value, suffix, and unique ID.
	 */
	private function get_directive_entries( WP_Interactivity_API_Directives_Processor $p, string $prefix ) {
		$directive_attributes = $p->get_attribute_names_with_prefix( 'data-wp-' . $prefix );
		$entries              = array();
		foreach ( $directive_attributes as $attribute_name ) {
			[ 'prefix' => $attr_prefix, 'suffix' => $suffix, 'unique_id' => $unique_id] = $this->parse_directive_name( $attribute_name );
			// Ensure it is the desired directive.
			if ( $prefix !== $attr_prefix ) {
				continue;
			}
			list( $namespace, $value ) = $this->extract_directive_value( $p->get_attribute( $attribute_name ), end( $this->namespace_stack ) );
			$entries[]                 = array(
				'namespace' => $namespace,
				'value'     => $value,
				'suffix'    => $suffix,
				'unique_id' => $unique_id,
			);
		}
		// Sort directive entries to ensure stable ordering with the client.
		// Put nulls first, then sort by suffix and finally by uniqueIds.
		usort(
			$entries,
			function ( $a, $b ) {
				$a_suffix = $a['suffix'] ?? '';
				$b_suffix = $b['suffix'] ?? '';
				if ( $a_suffix !== $b_suffix ) {
					return $a_suffix < $b_suffix ? -1 : 1;
				}
				$a_id = $a['unique_id'] ?? '';
				$b_id = $b['unique_id'] ?? '';
				if ( $a_id === $b_id ) {
					return 0;
				}
				return $a_id > $b_id ? 1 : -1;
			}
		);
		return $entries;
	}

	/**
	 * Transforms a kebab-case string to camelCase.
	 *
	 * @since 6.5.0
	 *
	 * @param string $str The kebab-case string to transform to camelCase.
	 * @return string The transformed camelCase string.
	 */
	private function kebab_to_camel_case( string $str ): string {
		return lcfirst(
			preg_replace_callback(
				'/(-)([a-z])/',
				function ( $matches ) {
					return strtoupper( $matches[2] );
				},
				strtolower( rtrim( $str, '-' ) )
			)
		);
	}

	/**
	 * Processes the `data-wp-interactive` directive.
	 *
	 * It adds the default store namespace defined in the directive value to the
	 * stack so that it's available for the nested interactivity elements.
	 *
	 * @since 6.5.0
	 *
	 * @param WP_Interactivity_API_Directives_Processor $p    The directives processor instance.
	 * @param string                                    $mode Whether the processing is entering or exiting the tag.
	 */
	private function data_wp_interactive_processor( WP_Interactivity_API_Directives_Processor $p, string $mode ) {
		// When exiting tags, it removes the last namespace from the stack.
		if ( 'exit' === $mode ) {
			array_pop( $this->namespace_stack );
			return;
		}

		// Tries to decode the `data-wp-interactive` attribute value.
		$attribute_value = $p->get_attribute( 'data-wp-interactive' );

		/*
		 * Pushes the newly defined namespace or the current one if the
		 * `data-wp-interactive` definition was invalid or does not contain a
		 * namespace. It does so because the function pops out the current namespace
		 * from the stack whenever it finds a `data-wp-interactive`'s closing tag,
		 * independently of whether the previous `data-wp-interactive` definition
		 * contained a valid namespace.
		 */
		$new_namespace = null;
		if ( is_string( $attribute_value ) && ! empty( $attribute_value ) ) {
			$decoded_json = json_decode( $attribute_value, true );
			if ( is_array( $decoded_json ) ) {
				$new_namespace = $decoded_json['namespace'] ?? null;
			} else {
				$new_namespace = $attribute_value;
			}
		}
		$this->namespace_stack[] = ( $new_namespace && 1 === preg_match( '/^([\w\-_\/]+)/', $new_namespace ) )
			? $new_namespace
			: end( $this->namespace_stack );
	}

	/**
	 * Processes the `data-wp-context` directive.
	 *
	 * It adds the context defined in the directive value to the stack so that
	 * it's available for the nested interactivity elements.
	 *
	 * @since 6.5.0
	 *
	 * @param WP_Interactivity_API_Directives_Processor $p               The directives processor instance.
	 * @param string                                    $mode            Whether the processing is entering or exiting the tag.
	 */
	private function data_wp_context_processor( WP_Interactivity_API_Directives_Processor $p, string $mode ) {
		// When exiting tags, it removes the last context from the stack.
		if ( 'exit' === $mode ) {
			array_pop( $this->context_stack );
			return;
		}

		$entries = $this->get_directive_entries( $p, 'context' );
		$context = end( $this->context_stack ) !== false ? end( $this->context_stack ) : array();
		foreach ( $entries as $entry ) {
			if ( null !== $entry['suffix'] ) {
				continue;
			}

			$context = array_replace_recursive(
				$context,
				array( $entry['namespace'] => is_array( $entry['value'] ) ? $entry['value'] : array() )
			);
		}
		$this->context_stack[] = $context;
	}

	/**
	 * Processes the `data-wp-bind` directive.
	 *
	 * It updates or removes the bound attributes based on the evaluation of its
	 * associated reference.
	 *
	 * @since 6.5.0
	 *
	 * @param WP_Interactivity_API_Directives_Processor $p               The directives processor instance.
	 * @param string                                    $mode            Whether the processing is entering or exiting the tag.
	 */
	private function data_wp_bind_processor( WP_Interactivity_API_Directives_Processor $p, string $mode ) {
		if ( 'enter' === $mode ) {
			$entries = $this->get_directive_entries( $p, 'bind' );
			foreach ( $entries as $entry ) {
				if ( empty( $entry['suffix'] ) || null !== $entry['unique_id'] ) {
						return;
				}

				$result = $this->evaluate( $entry );

				if (
					null !== $result &&
					(
						false !== $result ||
						( strlen( $entry['suffix'] ) > 5 && '-' === $entry['suffix'][4] )
					)
				) {
					/*
					 * If the result of the evaluation is a boolean and the attribute is
					 * `aria-` or `data-, convert it to a string "true" or "false". It
					 * follows the exact same logic as Preact because it needs to
					 * replicate what Preact will later do in the client:
					 * https://github.com/preactjs/preact/blob/ea49f7a0f9d1ff2c98c0bdd66aa0cbc583055246/src/diff/props.js#L131C24-L136
					 */
					if (
						is_bool( $result ) &&
						( strlen( $entry['suffix'] ) > 5 && '-' === $entry['suffix'][4] )
					) {
						$result = $result ? 'true' : 'false';
					}
					$p->set_attribute( $entry['suffix'], $result );
				} else {
					$p->remove_attribute( $entry['suffix'] );
				}
			}
		}
	}

	/**
	 * Processes the `data-wp-class` directive.
	 *
	 * It adds or removes CSS classes in the current HTML element based on the
	 * evaluation of its associated references.
	 *
	 * @since 6.5.0
	 *
	 * @param WP_Interactivity_API_Directives_Processor $p               The directives processor instance.
	 * @param string                                    $mode            Whether the processing is entering or exiting the tag.
	 */
	private function data_wp_class_processor( WP_Interactivity_API_Directives_Processor $p, string $mode ) {
		if ( 'enter' === $mode ) {
			$all_class_directives = $p->get_attribute_names_with_prefix( 'data-wp-class--' );
			$entries              = $this->get_directive_entries( $p, 'class' );
			foreach ( $entries as $entry ) {
				if ( empty( $entry['suffix'] ) ) {
					continue;
				}
				$class_name = isset( $entry['unique_id'] ) && $entry['unique_id']
					? "{$entry['suffix']}---{$entry['unique_id']}"
					: $entry['suffix'];

				if ( empty( $class_name ) ) {
					return;
				}

				$result = $this->evaluate( $entry );

				if ( $result ) {
					$p->add_class( $class_name );
				} else {
					$p->remove_class( $class_name );
				}
			}
		}
	}

	/**
	 * Processes the `data-wp-style` directive.
	 *
	 * It updates the style attribute value of the current HTML element based on
	 * the evaluation of its associated references.
	 *
	 * @since 6.5.0
	 *
	 * @param WP_Interactivity_API_Directives_Processor $p               The directives processor instance.
	 * @param string                                    $mode            Whether the processing is entering or exiting the tag.
	 */
	private function data_wp_style_processor( WP_Interactivity_API_Directives_Processor $p, string $mode ) {
		if ( 'enter' === $mode ) {
			$entries = $this->get_directive_entries( $p, 'style' );
			foreach ( $entries as $entry ) {
				$style_property = $entry['suffix'];
				if ( empty( $style_property ) || null !== $entry['unique_id'] ) {
					continue;
				}

				$style_property_value  = $this->evaluate( $entry );
				$style_attribute_value = $p->get_attribute( 'style' );
				$style_attribute_value = ( $style_attribute_value && ! is_bool( $style_attribute_value ) ) ? $style_attribute_value : '';

				/*
				 * Checks first if the style property is not falsy and the style
				 * attribute value is not empty because if it is, it doesn't need to
				 * update the attribute value.
				 */
				if ( $style_property_value || $style_attribute_value ) {
					$style_attribute_value = $this->merge_style_property( $style_attribute_value, $style_property, $style_property_value );
					/*
					 * If the style attribute value is not empty, it sets it. Otherwise,
					 * it removes it.
					 */
					if ( ! empty( $style_attribute_value ) ) {
						$p->set_attribute( 'style', $style_attribute_value );
					} else {
						$p->remove_attribute( 'style' );
					}
				}
			}
		}
	}

	/**
	 * Merges an individual style property in the `style` attribute of an HTML
	 * element, updating or removing the property when necessary.
	 *
	 * If a property is modified, the old one is removed and the new one is added
	 * at the end of the list.
	 *
	 * @since 6.5.0
	 *
	 * Example:
	 *
	 *     merge_style_property( 'color:green;', 'color', 'red' )      => 'color:red;'
	 *     merge_style_property( 'background:green;', 'color', 'red' ) => 'background:green;color:red;'
	 *     merge_style_property( 'color:green;', 'color', null )       => ''
	 *
	 * @param string            $style_attribute_value The current style attribute value.
	 * @param string            $style_property_name   The style property name to set.
	 * @param string|false|null $style_property_value  The value to set for the style property. With false, null or an
	 *                                                 empty string, it removes the style property.
	 * @return string The new style attribute value after the specified property has been added, updated or removed.
	 */
	private function merge_style_property( string $style_attribute_value, string $style_property_name, $style_property_value ): string {
		$style_assignments    = explode( ';', $style_attribute_value );
		$result               = array();
		$style_property_value = ! empty( $style_property_value ) ? rtrim( trim( $style_property_value ), ';' ) : null;
		$new_style_property   = $style_property_value ? $style_property_name . ':' . $style_property_value . ';' : '';

		// Generates an array with all the properties but the modified one.
		foreach ( $style_assignments as $style_assignment ) {
			if ( empty( trim( $style_assignment ) ) ) {
				continue;
			}
			list( $name, $value ) = explode( ':', $style_assignment );
			if ( trim( $name ) !== $style_property_name ) {
				$result[] = trim( $name ) . ':' . trim( $value ) . ';';
			}
		}

		// Adds the new/modified property at the end of the list.
		$result[] = $new_style_property;

		return implode( '', $result );
	}

	/**
	 * Processes the `data-wp-text` directive.
	 *
	 * It updates the inner content of the current HTML element based on the
	 * evaluation of its associated reference.
	 *
	 * @since 6.5.0
	 *
	 * @param WP_Interactivity_API_Directives_Processor $p               The directives processor instance.
	 * @param string                                    $mode            Whether the processing is entering or exiting the tag.
	 */
	private function data_wp_text_processor( WP_Interactivity_API_Directives_Processor $p, string $mode ) {
		if ( 'enter' === $mode ) {
			$entries     = $this->get_directive_entries( $p, 'text' );
			$valid_entry = null;
			// Get the first valid `data-wp-text` entry without suffix or unique ID.
			foreach ( $entries as $entry ) {
				if ( null === $entry['suffix'] && null === $entry['unique_id'] && ! empty( $entry['value'] ) ) {
					$valid_entry = $entry;
					break;
				}
			}
			if ( null === $valid_entry ) {
				return;
			}
			$result = $this->evaluate( $valid_entry );

			/*
			 * Follows the same logic as Preact in the client and only changes the
			 * content if the value is a string or a number. Otherwise, it removes the
			 * content.
			 */
			if ( is_string( $result ) || is_numeric( $result ) ) {
				$p->set_content_between_balanced_tags( esc_html( $result ) );
			} else {
				$p->set_content_between_balanced_tags( '' );
			}
		}
	}

	/**
	 * Returns the CSS styles for animating the top loading bar in the router.
	 *
	 * @since 6.5.0
	 *
	 * @return string The CSS styles for the router's top loading bar animation.
	 */
	private function get_router_animation_styles(): string {
		return <<<CSS
			.wp-interactivity-router-loading-bar {
				position: fixed;
				top: 0;
				left: 0;
				margin: 0;
				padding: 0;
				width: 100vw;
				max-width: 100vw !important;
				height: 4px;
				background-color: #000;
				opacity: 0
			}
			.wp-interactivity-router-loading-bar.start-animation {
				animation: wp-interactivity-router-loading-bar-start-animation 30s cubic-bezier(0.03, 0.5, 0, 1) forwards
			}
			.wp-interactivity-router-loading-bar.finish-animation {
				animation: wp-interactivity-router-loading-bar-finish-animation 300ms ease-in
			}
			@keyframes wp-interactivity-router-loading-bar-start-animation {
				0% { transform: scaleX(0); transform-origin: 0 0; opacity: 1 }
				100% { transform: scaleX(1); transform-origin: 0 0; opacity: 1 }
			}
			@keyframes wp-interactivity-router-loading-bar-finish-animation {
				0% { opacity: 1 }
				50% { opacity: 1 }
				100% { opacity: 0 }
			}
CSS;
	}

	/**
	 * Deprecated.
	 *
	 * @since 6.5.0
	 * @deprecated 6.7.0 Use {@see WP_Interactivity_API::print_router_markup} instead.
	 */
	public function print_router_loading_and_screen_reader_markup() {
		_deprecated_function( __METHOD__, '6.7.0', 'WP_Interactivity_API::print_router_markup' );

		// Call the new method.
		$this->print_router_markup();
	}

	/**
	 * Outputs markup for the @wordpress/interactivity-router script module.
	 *
	 * This method prints a div element representing a loading bar visible during
	 * navigation.
	 *
	 * @since 6.7.0
	 */
	public function print_router_markup() {
		echo <<<HTML
			<div
				class="wp-interactivity-router-loading-bar"
				data-wp-interactive="core/router"
				data-wp-class--start-animation="state.navigation.hasStarted"
				data-wp-class--finish-animation="state.navigation.hasFinished"
			></div>
HTML;
	}

	/**
	 * Processes the `data-wp-router-region` directive.
	 *
	 * It renders in the footer a set of HTML elements to notify users about
	 * client-side navigations. More concretely, the elements added are 1) a
	 * top loading bar to visually inform that a navigation is in progress
	 * and 2) an `aria-live` region for accessible navigation announcements.
	 *
	 * @since 6.5.0
	 *
	 * @param WP_Interactivity_API_Directives_Processor $p               The directives processor instance.
	 * @param string                                    $mode            Whether the processing is entering or exiting the tag.
	 */
	private function data_wp_router_region_processor( WP_Interactivity_API_Directives_Processor $p, string $mode ) {
		if ( 'enter' === $mode && ! $this->has_processed_router_region ) {
			$this->has_processed_router_region = true;

			// Enqueues as an inline style.
			wp_register_style( 'wp-interactivity-router-animations', false );
			wp_add_inline_style( 'wp-interactivity-router-animations', $this->get_router_animation_styles() );
			wp_enqueue_style( 'wp-interactivity-router-animations' );

			// Adds the necessary markup to the footer.
			add_action( 'wp_footer', array( $this, 'print_router_markup' ) );
		}
	}

	/**
	 * Processes the `data-wp-each` directive.
	 *
	 * This directive gets an array passed as reference and iterates over it
	 * generating new content for each item based on the inner markup of the
	 * `template` tag.
	 *
	 * @since 6.5.0
	 * @since 6.9.0 Include the list path in the rendered `data-wp-each-child` directives.
	 *
	 * @param WP_Interactivity_API_Directives_Processor $p               The directives processor instance.
	 * @param string                                    $mode            Whether the processing is entering or exiting the tag.
	 * @param array                                     $tag_stack       The reference to the tag stack.
	 */
	private function data_wp_each_processor( WP_Interactivity_API_Directives_Processor $p, string $mode, array &$tag_stack ) {
		if ( 'enter' === $mode && 'TEMPLATE' === $p->get_tag() ) {
			$entries = $this->get_directive_entries( $p, 'each' );
			if ( count( $entries ) > 1 || empty( $entries ) ) {
				// There should be only one `data-wp-each` directive per template tag.
				return;
			}
			$entry = $entries[0];
			if ( null !== $entry['unique_id'] ) {
				return;
			}
			$item_name = isset( $entry['suffix'] ) ? $this->kebab_to_camel_case( $entry['suffix'] ) : 'item';
			$result    = $this->evaluate( $entry );

			// Gets the content between the template tags and leaves the cursor in the closer tag.
			$inner_content = $p->get_content_between_balanced_template_tags();

			// Checks if there is a manual server-side directive processing.
			$template_end = 'data-wp-each: template end';
			$p->set_bookmark( $template_end );
			$p->next_tag();
			$manual_sdp = $p->get_attribute( 'data-wp-each-child' );
			$p->seek( $template_end ); // Rewinds to the template closer tag.
			$p->release_bookmark( $template_end );

			/*
			 * It doesn't process in these situations:
			 * - Manual server-side directive processing.
			 * - Empty or non-array values.
			 * - Associative arrays because those are deserialized as objects in JS.
			 * - Templates that contain top-level texts because those texts can't be
			 *   identified and removed in the client.
			 */
			if (
				$manual_sdp ||
				empty( $result ) ||
				! is_array( $result ) ||
				! array_is_list( $result ) ||
				! str_starts_with( trim( $inner_content ), '<' ) ||
				! str_ends_with( trim( $inner_content ), '>' )
			) {
				array_pop( $tag_stack );
				return;
			}

			// Processes the inner content for each item of the array.
			$processed_content = '';
			foreach ( $result as $item ) {
				// Creates a new context that includes the current item of the array.
				$this->context_stack[] = array_replace_recursive(
					end( $this->context_stack ) !== false ? end( $this->context_stack ) : array(),
					array( $entry['namespace'] => array( $item_name => $item ) )
				);

				// Processes the inner content with the new context.
				$processed_item = $this->_process_directives( $inner_content );

				if ( null === $processed_item ) {
					// If the HTML is unbalanced, stop processing it.
					array_pop( $this->context_stack );
					return;
				}

				/*
				 * Adds the `data-wp-each-child` directive to each top-level tag
				 * rendered by this `data-wp-each` directive. The value is the
				 * `data-wp-each` directive's namespace and path.
				 *
				 * Nested `data-wp-each` directives could render
				 * `data-wp-each-child` elements at the top level as well, and
				 * they should be overwritten.
				 *
				 * @since 6.9.0
				 */
				$i = new WP_Interactivity_API_Directives_Processor( $processed_item );
				while ( $i->next_tag() ) {
					$i->set_attribute( 'data-wp-each-child', $entry['namespace'] . '::' . $entry['value'] );
					$i->next_balanced_tag_closer_tag();
				}
				$processed_content .= $i->get_updated_html();

				// Removes the current context from the stack.
				array_pop( $this->context_stack );
			}

			// Appends the processed content after the tag closer of the template.
			$p->append_content_after_template_tag_closer( $processed_content );

			// Pops the last tag because it skipped the closing tag of the template tag.
			array_pop( $tag_stack );
		}
	}
}
