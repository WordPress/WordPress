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
	 *
	 * @param array $data Data to filter.
	 * @return array Data for the Interactivity API script module.
	 */
	public function filter_script_module_interactivity_data( array $data ): array {
		if ( empty( $this->state_data ) && empty( $this->config_data ) ) {
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
	 */
	public function add_hooks() {
		add_filter( 'script_module_data_@wordpress/interactivity', array( $this, 'filter_script_module_interactivity_data' ) );
		add_filter( 'script_module_data_@wordpress/interactivity-router', array( $this, 'filter_script_module_interactivity_router_data' ) );
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
				list( $opening_tag_name, $directives_prefixes ) = end( $tag_stack );

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
				if ( 0 !== count( $p->get_attribute_names_with_prefix( 'data-wp-each-child' ) ) ) {
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
						list( $directive_prefix ) = $this->extract_prefix_and_suffix( $attribute_name );
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
		 * update the HTML on the client side during the hydration. It will also
		 * display a notice to the developer to inform them about the issue.
		 */
		if ( $unbalanced || 0 < count( $tag_stack ) ) {
			$tag_errored = 0 < count( $tag_stack ) ? end( $tag_stack )[0] : $tag_name;
			/* translators: %1s: Namespace processed, %2s: The tag that caused the error; could be any HTML tag.  */
			$message = sprintf( __( 'Interactivity directives failed to process in "%1$s" due to a missing "%2$s" end tag.' ), end( $this->namespace_stack ), $tag_errored );
			_doing_it_wrong( __METHOD__, $message, '6.6.0' );
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
	 *
	 * @param string|true $directive_value The directive attribute value string or `true` when it's a boolean attribute.
	 * @return mixed|null The result of the evaluation. Null if the reference path doesn't exist or the namespace is falsy.
	 */
	private function evaluate( $directive_value ) {
		$default_namespace = end( $this->namespace_stack );
		$context           = end( $this->context_stack );

		list( $ns, $path ) = $this->extract_directive_value( $directive_value, $default_namespace );
		if ( ! $ns || ! $path ) {
			/* translators: %s: The directive value referenced. */
			$message = sprintf( __( 'Namespace or reference path cannot be empty. Directive value referenced: %s' ), $directive_value );
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
		foreach ( $path_segments as $path_segment ) {
			if ( ( is_array( $current ) || $current instanceof ArrayAccess ) && isset( $current[ $path_segment ] ) ) {
				$current = $current[ $path_segment ];
			} elseif ( is_object( $current ) && isset( $current->$path_segment ) ) {
				$current = $current->$path_segment;
			} else {
				return null;
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
	 * Extracts the directive attribute name to separate and return the directive
	 * prefix and an optional suffix.
	 *
	 * The suffix is the string after the first double hyphen and the prefix is
	 * everything that comes before the suffix.
	 *
	 * Example:
	 *
	 *     extract_prefix_and_suffix( 'data-wp-interactive' )   => array( 'data-wp-interactive', null )
	 *     extract_prefix_and_suffix( 'data-wp-bind--src' )     => array( 'data-wp-bind', 'src' )
	 *     extract_prefix_and_suffix( 'data-wp-foo--and--bar' ) => array( 'data-wp-foo', 'and--bar' )
	 *
	 * @since 6.5.0
	 *
	 * @param string $directive_name The directive attribute name.
	 * @return array An array containing the directive prefix and optional suffix.
	 */
	private function extract_prefix_and_suffix( string $directive_name ): array {
		return explode( '--', $directive_name, 2 );
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
	 * Transforms a kebab-case string to camelCase.
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

		$attribute_value = $p->get_attribute( 'data-wp-context' );
		$namespace_value = end( $this->namespace_stack );

		// Separates the namespace from the context JSON object.
		list( $namespace_value, $decoded_json ) = is_string( $attribute_value ) && ! empty( $attribute_value )
			? $this->extract_directive_value( $attribute_value, $namespace_value )
			: array( $namespace_value, null );

		/*
		 * If there is a namespace, it adds a new context to the stack merging the
		 * previous context with the new one.
		 */
		if ( is_string( $namespace_value ) ) {
			$this->context_stack[] = array_replace_recursive(
				end( $this->context_stack ) !== false ? end( $this->context_stack ) : array(),
				array( $namespace_value => is_array( $decoded_json ) ? $decoded_json : array() )
			);
		} else {
			/*
			 * If there is no namespace, it pushes the current context to the stack.
			 * It needs to do so because the function pops out the current context
			 * from the stack whenever it finds a `data-wp-context`'s closing tag.
			 */
			$this->context_stack[] = end( $this->context_stack );
		}
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
			$all_bind_directives = $p->get_attribute_names_with_prefix( 'data-wp-bind--' );

			foreach ( $all_bind_directives as $attribute_name ) {
				list( , $bound_attribute ) = $this->extract_prefix_and_suffix( $attribute_name );
				if ( empty( $bound_attribute ) ) {
					return;
				}

				$attribute_value = $p->get_attribute( $attribute_name );
				$result          = $this->evaluate( $attribute_value );

				if (
					null !== $result &&
					(
						false !== $result ||
						( strlen( $bound_attribute ) > 5 && '-' === $bound_attribute[4] )
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
						( strlen( $bound_attribute ) > 5 && '-' === $bound_attribute[4] )
					) {
						$result = $result ? 'true' : 'false';
					}
					$p->set_attribute( $bound_attribute, $result );
				} else {
					$p->remove_attribute( $bound_attribute );
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

			foreach ( $all_class_directives as $attribute_name ) {
				list( , $class_name ) = $this->extract_prefix_and_suffix( $attribute_name );
				if ( empty( $class_name ) ) {
					return;
				}

				$attribute_value = $p->get_attribute( $attribute_name );
				$result          = $this->evaluate( $attribute_value );

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
			$all_style_attributes = $p->get_attribute_names_with_prefix( 'data-wp-style--' );

			foreach ( $all_style_attributes as $attribute_name ) {
				list( , $style_property ) = $this->extract_prefix_and_suffix( $attribute_name );
				if ( empty( $style_property ) ) {
					continue;
				}

				$directive_attribute_value = $p->get_attribute( $attribute_name );
				$style_property_value      = $this->evaluate( $directive_attribute_value );
				$style_attribute_value     = $p->get_attribute( 'style' );
				$style_attribute_value     = ( $style_attribute_value && ! is_bool( $style_attribute_value ) ) ? $style_attribute_value : '';

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
			$attribute_value = $p->get_attribute( 'data-wp-text' );
			$result          = $this->evaluate( $attribute_value );

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

			/*
			 * Initialize the `core/router` store.
			 * If the store is not initialized like this with minimal
			 * navigation object, the interactivity-router script module
			 * errors.
			 */
			$this->state(
				'core/router',
				array(
					'navigation' => new stdClass(),
				)
			);

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
	 *
	 * @param WP_Interactivity_API_Directives_Processor $p               The directives processor instance.
	 * @param string                                    $mode            Whether the processing is entering or exiting the tag.
	 * @param array                                     $tag_stack       The reference to the tag stack.
	 */
	private function data_wp_each_processor( WP_Interactivity_API_Directives_Processor $p, string $mode, array &$tag_stack ) {
		if ( 'enter' === $mode && 'TEMPLATE' === $p->get_tag() ) {
			$attribute_name   = $p->get_attribute_names_with_prefix( 'data-wp-each' )[0];
			$extracted_suffix = $this->extract_prefix_and_suffix( $attribute_name );
			$item_name        = isset( $extracted_suffix[1] ) ? $this->kebab_to_camel_case( $extracted_suffix[1] ) : 'item';
			$attribute_value  = $p->get_attribute( $attribute_name );
			$result           = $this->evaluate( $attribute_value );

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

			// Extracts the namespace from the directive attribute value.
			$namespace_value         = end( $this->namespace_stack );
			list( $namespace_value ) = is_string( $attribute_value ) && ! empty( $attribute_value )
				? $this->extract_directive_value( $attribute_value, $namespace_value )
				: array( $namespace_value, null );

			// Processes the inner content for each item of the array.
			$processed_content = '';
			foreach ( $result as $item ) {
				// Creates a new context that includes the current item of the array.
				$this->context_stack[] = array_replace_recursive(
					end( $this->context_stack ) !== false ? end( $this->context_stack ) : array(),
					array( $namespace_value => array( $item_name => $item ) )
				);

				// Processes the inner content with the new context.
				$processed_item = $this->_process_directives( $inner_content );

				if ( null === $processed_item ) {
					// If the HTML is unbalanced, stop processing it.
					array_pop( $this->context_stack );
					return;
				}

				// Adds the `data-wp-each-child` to each top-level tag.
				$i = new WP_Interactivity_API_Directives_Processor( $processed_item );
				while ( $i->next_tag() ) {
					$i->set_attribute( 'data-wp-each-child', true );
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
