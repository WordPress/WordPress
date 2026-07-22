<?php
/**
 * WP_View_Config_Data class
 *
 * @package WordPress
 * @since 7.1.0
 */

/**
 * Holds an entity's view configuration while it is being built.
 *
 * An instance of this class is what `get_entity_view_config_{$kind}_{$name}`
 * filter callbacks receive: a callback changes the configuration by calling
 * methods on the instance and returning it. The configuration has four
 * top-level keys — `default_view`, `default_layouts`, `view_list`, and
 * `form` — and there are three ways to contribute. They form a gradient of how
 * deep the replacement reaches:
 *
 * - The `merge()` method merges partial changes (patches) into what is already
 *   there: `default_view`, `default_layouts`, and the `form` settings by key,
 *   and the `view_list` entries by view `slug` identity. This is what plugins
 *   should use: patches compose with core's configuration and with other
 *   plugins'.
 * - `replace()` applies a patch the same way `merge()` does, with one
 *   difference: a list in the patch replaces the current list wholesale
 *   instead of merging into it by member identity. It shouldn't be the
 *   default choice — a callback that replaces a list stops inheriting core's
 *   future additions to it — but it's useful when a contributor needs to pin
 *   a list to an exact set of members.
 * - `set()` goes one step further: it replaces each top-level key the patch
 *   names wholesale, dropping whatever that key held instead of merging into
 *   it. It's for a callback that owns a key outright and wants to pin it to an
 *   exact shape without the inherited default leaking through a key-by-key
 *   merge.
 *
 * All three touch only the top-level keys the patch names — an omitted key
 * keeps whatever it had, and a top-level `null` value drops the key it names,
 * which resets it to its default. They differ only in how deep the replacement
 * reaches once a key is named: `merge()` and `replace()` merge the value in
 * key by key (an associative array merges member by member, a nested `null`
 * deletes just that leaf, a scalar replaces just that value), while `set()`
 * swaps the whole value. A nested `null` deletes just the leaf it names in
 * every case. Each patch also declares the configuration schema
 * version it was written against (currently 1), so a future WordPress release
 * that changes the configuration shape can migrate existing patches forward
 * instead of breaking them.
 *
 * Where those three write values, `remove()` deletes them: it takes a spec of
 * names — a list to delete entries at a level, or a nested map to reach deeper —
 * and prunes just what it names, mirroring the configuration's shape all the way
 * down to individual list members.
 *
 * @since 7.1.0
 */
class WP_View_Config_Data {

	/**
	 * The latest supported configuration schema version.
	 *
	 * @since 7.1.0
	 * @var int
	 */
	const LATEST_VERSION = 1;

	/**
	 * The documented top-level configuration keys.
	 *
	 * @since 7.1.0
	 * @var string[]
	 */
	const CONFIG_KEYS = array( 'default_view', 'default_layouts', 'view_list', 'form' );

	/**
	 * The configuration being contributed to.
	 *
	 * @since 7.1.0
	 * @var array
	 */
	private $config;

	/**
	 * The default configuration.
	 *
	 * @since 7.1.0
	 * @var array
	 */
	private $defaults;

	/**
	 * Constructor.
	 *
	 * @since 7.1.0
	 *
	 * @param array $config The base configuration to contribute to.
	 */
	public function __construct( array $config ) {
		$this->config   = $config;
		$this->defaults = $config;
	}

	/**
	 * Returns the current configuration array.
	 *
	 * Deliberately private: filter callbacks receive the container, not the
	 * materialized configuration, so they cannot read the built result and
	 * become coupled to a specific configuration shape or schema version. Only
	 * the class itself reconciles the container back into an array.
	 *
	 * @since 7.1.0
	 *
	 * @return array The configuration.
	 */
	private function get_data() {
		return $this->config;
	}

	/**
	 * Applies the entity view configuration filter and returns the result.
	 *
	 * Exposes the container through the dynamic
	 * `get_entity_view_config_{$kind}_{$name}` filter so that core and third
	 * parties can provide the configuration for a specific entity, then
	 * reconciles the filtered container back into a plain configuration array,
	 * limited to the documented configuration keys.
	 *
	 * @since 7.1.0
	 *
	 * @param string $kind The entity kind (e.g. `postType`).
	 * @param string $name The entity name (e.g. `page`).
	 * @return array The filtered configuration, limited to the documented keys.
	 */
	public function apply_filters( $kind, $name ) {
		/**
		 * Filters the view configuration for a given entity.
		 *
		 * The dynamic portions of the hook name, `$kind` and `$name`, refer to the
		 * entity kind (e.g. `postType`) and the entity name (e.g. `page`).
		 *
		 * Callbacks receive a WP_View_Config_Data object and change the
		 * configuration through its methods. Each write method takes the schema
		 * version the change was authored against as its second argument,
		 * and returns the object for chaining:
		 *
		 * - `merge( $patch, $version )` merges a partial change into the current
		 *   configuration. It touches only the top-level keys the patch names, and
		 *   merges each named value into the current one by shape: a scalar
		 *   replaces, an associative array merges key by key, and a list merges by
		 *   member identity (`id`, `slug`, or `field`). A `null` value drops the
		 *   key it names, resetting it to its default.
		 * - `replace( $patch, $version )` applies a patch exactly like `merge()`,
		 *   but swaps any list it names wholesale instead of merging that list by
		 *   member identity.
		 * - `set( $patch, $version )` also touches only the keys the patch names,
		 *   but swaps each named value in wholesale, dropping whatever the key held
		 *   before — for a callback that owns those keys outright.
		 * - `remove( $spec, $version )` deletes named properties. The spec mirrors
		 *   the configuration shape: a list of names deletes entries at that level,
		 *   and a nested map recurses to prune from within a named value, down to
		 *   individual list members.
		 *
		 * A change that declares an unsupported schema version is rejected and does
		 * not alter anything. Callbacks mutate the container in place, so there is no
		 * need to return it; any returned value is ignored. Callbacks must not replace
		 * the container with a different value, as later callbacks receive whatever the
		 * the previous one returned.
		 *
		 * @since 7.1.0
		 *
		 * @param WP_View_Config_Data $data   The view configuration container
		 *                                    for the entity, exposing the
		 *                                    `default_view`, `default_layouts`,
		 *                                    `view_list`, and `form` keys.
		 * @param array               $entity {
		 *     The entity the configuration is built for.
		 *
		 *     @type string $kind The entity kind.
		 *     @type string $name The entity name.
		 * }
		 */
		apply_filters(
			"get_entity_view_config_{$kind}_{$name}",
			$this,
			array(
				'kind' => $kind,
				'name' => $name,
			)
		);

		// Discard any keys the filter introduced that are not part of the
		// documented configuration shape.
		return array_intersect_key( $this->get_data(), array_flip( self::CONFIG_KEYS ) );
	}

	/**
	 * Replaces whole top-level keys, leaving the rest of the configuration alone.
	 *
	 * Like merge() and replace(), set() applies a patch of top-level keys and
	 * touches only the keys the patch names: a key the patch omits keeps whatever
	 * it had, and a `null` value drops the key it names (which resets it to its
	 * default). The difference is depth — where merge() and replace() merge a
	 * named key's value into the current one key by key, set() swaps the whole
	 * value in wholesale, dropping whatever the key held before. A `null` nested
	 * within that value still drops the property it names, so set() honours
	 * nulls at every depth just as merge() and replace() do.
	 *
	 * Use it when a callback owns a key outright and wants to pin it to an exact
	 * shape, without the inherited default leaking through a key-by-key merge.
	 *
	 * A patch that declares an unsupported schema version is rejected and does
	 * not change anything.
	 *
	 * @since 7.1.0
	 *
	 * @param array $patch   The partial configuration whose named keys to replace.
	 * @param int   $version The schema version the patch was authored against.
	 * @return WP_View_Config_Data The instance, for chaining.
	 */
	public function set( array $patch, int $version ) {
		return $this->apply( $patch, $version, __METHOD__, 'set' );
	}

	/**
	 * Removes named properties from the configuration, leaving the rest alone.
	 *
	 * Where merge(), replace(), and set() take a patch of *values* to write,
	 * remove() takes a spec of *names* to delete, and its shape mirrors the
	 * configuration it prunes:
	 *
	 * - A list of names deletes each named entry from the value at that level: a
	 *   key from an associative array, or the member with a matching identity
	 *   (`id`, `slug`, `field`, or a bare scalar) from a list.
	 * - An associative array maps a name to a nested spec, recursing into that
	 *   entry's value to delete from within it.
	 *
	 * Naming a top-level configuration key is the one exception: like a `null`
	 * value in a patch, it resets that key to its default rather than dropping it
	 * outright, so top-level removal and top-level `null` compose the same way.
	 *
	 * So `array( 'default_view' )` resets the whole `default_view` key to its
	 * default, `array( 'default_view' => array( 'sort' ) )` drops just its `sort`
	 * property, and `array( 'default_view' => array( 'fields' => array( 'f2' ) ) )`
	 * drops the `f2` member from its `fields` list. A name that is not present is
	 * ignored, and a list is renumbered after a member is removed.
	 *
	 * A spec that declares an unsupported schema version is rejected and does not
	 * change anything.
	 *
	 * @since 7.1.0
	 *
	 * @param array $spec    The names to remove, keyed to match the configuration shape.
	 * @param int   $version The schema version the spec was authored against.
	 * @return WP_View_Config_Data The instance, for chaining.
	 */
	public function remove( array $spec, int $version ) {
		if ( $version <= 0 || $version > self::LATEST_VERSION ) {
			_doing_it_wrong(
				__METHOD__,
				esc_html__( 'A view configuration patch must declare a supported schema version.' ),
				'7.1.0'
			);

			return $this;
		}

		// A flat list names top-level keys to reset; a map recurses into each
		// named key to prune from within its value.
		$spec_is_list = array_is_list( $spec );
		foreach ( $spec as $spec_key => $spec_value ) {
			$key = $spec_is_list ? $spec_value : $spec_key;

			if ( ! in_array( $key, self::CONFIG_KEYS, true ) ) {
				_doing_it_wrong(
					__METHOD__,
					sprintf(
						/* translators: %s: the configuration key. */
						esc_html__( '"%s" is not a documented view configuration key.' ),
						esc_html( $key )
					),
					'7.1.0'
				);
				continue;
			}

			if ( $spec_is_list ) {
				// Removing a top-level key resets it to its default, just as a
				// null patch value does.
				$this->config[ $key ] = $this->defaults[ $key ] ?? array();
			} elseif ( array_key_exists( $key, $this->config ) ) {
				$this->config[ $key ] = $this->remove_properties( $this->config[ $key ], $spec_value );
			}
		}

		return $this;
	}

	/**
	 * Replaces list values while merging the rest of a partial configuration.
	 *
	 * Takes the same arguments as merge() and applies the patch the same way,
	 * with one difference: a list in the patch replaces the current list
	 * wholesale instead of merging into it by member identity. Associative
	 * arrays still merge key by key, `null` still drops what it names, and a
	 * scalar still replaces the current value.
	 *
	 * It shouldn't be the default choice — a callback that replaces a list
	 * stops inheriting core's future additions to it — but it's useful when a
	 * contributor needs to pin a list to an exact set of members.
	 *
	 * A patch that declares an unsupported schema version is rejected and does
	 * not change anything.
	 *
	 * @since 7.1.0
	 *
	 * @param array $patch   The partial configuration to apply.
	 * @param int   $version The schema version the patch was authored against.
	 * @return WP_View_Config_Data The instance, for chaining.
	 */
	public function replace( array $patch, int $version ) {
		return $this->apply( $patch, $version, __METHOD__, 'replace' );
	}

	/**
	 * Merges a partial configuration into the existing one.
	 *
	 * Applies a patch of top-level keys and touches only the keys the patch
	 * names: a key the patch omits keeps whatever it had, and a `null` value
	 * drops the key it names (which resets it to its default). Each named key's
	 * value is then merged into the current one by value shape:
	 *
	 * - a scalar replaces the current value;
	 * - an associative array merges key by key, with a nested `null` deleting
	 *   just the leaf it names;
	 * - a list merges into the current list by member identity.
	 *
	 * Identity is the member's value cast to a string: a bare scalar is its own
	 * identity, and a map is identified by the value of the first of the
	 * well-known identity keys (`id`, `slug`, `field`) it carries. A member
	 * whose identity matches one already present merges into it in place, keeping
	 * its position; a member with no identity is appended to the end of the list.
	 *
	 * For example, given this patch:
	 *
	 * ```php
	 * array(
	 *   'default_view' => array( 'search' => 'new search', 'fields' => array( 'newField' ) ),
	 *   'default_layouts' => array( 'grid' => array( 'layout' => array( 'badgeFields' => array( 'newField' ) ) ) ),
	 *   'view_list' => array( array( 'slug' => 'table', 'title' => 'New title' ) ),
	 * )
	 * ```
	 *
	 * - default_view will be updated so the search string is 'new search' and the newField is appended to the list of fields.
	 * - default_layouts will be updated so that newField is appended to the badgeFields.
	 * - view_list will be updated so that the view with slug 'table' has its title changed to 'New title'.
	 *
	 * A patch that declares an unsupported schema version is rejected and does
	 * not change anything.
	 *
	 * @since 7.1.0
	 *
	 * @param array $patch   The partial configuration to merge.
	 * @param int   $version The schema version the patch was authored against.
	 * @return WP_View_Config_Data The instance, for chaining.
	 */
	public function merge( array $patch, int $version ) {
		return $this->apply( $patch, $version, __METHOD__, 'merge' );
	}

	/**
	 * Applies a patch to the configuration, top-level key by top-level key.
	 *
	 * Shared by merge(), replace(), and set(); the three differ only in how the
	 * value of a named key is applied, which is carried by $mode:
	 *
	 * - `merge`   merges the value into the current one, lists by member identity;
	 * - `replace` merges the value in the same way but swaps lists wholesale;
	 * - `set`     swaps the whole value in wholesale, without merging.
	 *
	 * In every mode a top-level `null` resets the key it names to its default, a
	 * nested `null` drops the property it names, and an omitted key is left
	 * untouched, so all three treat nulls the same way at every depth.
	 *
	 * @since 7.1.0
	 *
	 * @param array  $patch   The partial configuration to apply.
	 * @param int    $version The schema version the patch was authored against.
	 * @param string $method  The public method the patch was passed to, for misuse reporting.
	 * @param string $mode    How to apply each named key's value: `merge`, `replace`, or `set`.
	 * @return WP_View_Config_Data The instance, for chaining.
	 */
	private function apply( array $patch, int $version, $method, $mode ) {
		if ( $version <= 0 || $version > self::LATEST_VERSION ) {
			_doing_it_wrong(
				esc_html( $method ),
				esc_html__( 'A view configuration patch must declare a supported schema version.' ),
				'7.1.0'
			);

			return $this;
		}

		foreach ( $patch as $key => $value ) {
			if ( ! in_array( $key, self::CONFIG_KEYS, true ) ) {
				_doing_it_wrong(
					esc_html( $method ),
					sprintf(
						/* translators: %s: the configuration key. */
						esc_html__( '"%s" is not a documented view configuration key.' ),
						esc_html( $key )
					),
					'7.1.0'
				);
				continue;
			}

			// A null patch value makes the top-level property reset to defaults.
			if ( null === $value ) {
				$this->config[ $key ] = $this->defaults[ $key ] ?? array();
				continue;
			}

			// set() swaps the whole value in; merge()/replace() merge it into the
			// current one, differing only in how they treat lists. In every mode a
			// nested null still drops the property it names.
			$this->config[ $key ] = 'set' === $mode
				? $this->strip_nulls( $value )
				: $this->merge_properties( $this->config[ $key ] ?? array(), $value, 'replace' === $mode );
		}

		return $this;
	}

	/**
	 * Recursively drops every property whose value is `null` from a value.
	 *
	 * set() swaps a named key's value in wholesale rather than merging it into
	 * the current one, so it has no existing leaf for a nested `null` to delete
	 * the way merge() and replace() do. Stripping nulls here gives a nested
	 * `null` the same "drop the property it names" meaning under set() that it
	 * carries everywhere else. The same applies to a list replace() swaps in
	 * wholesale. A list is renumbered after a member is removed so removed
	 * entries do not leave gaps.
	 *
	 * @since 7.1.0
	 *
	 * @param mixed $value The value to strip nulls from.
	 * @return mixed The value with every `null` property removed, recursively.
	 */
	private function strip_nulls( $value ) {
		if ( ! is_array( $value ) ) {
			return $value;
		}

		$result = array();
		foreach ( $value as $key => $item ) {
			// A null value drops the property it names.
			if ( null === $item ) {
				continue;
			}

			$result[ $key ] = $this->strip_nulls( $item );
		}

		// Renumber a list so a removed member does not leave a gap.
		return array_is_list( $value ) ? array_values( $result ) : $result;
	}

	/**
	 * Merges an incoming value into the current one, recursing by value shape.
	 *
	 * This is the core of the merge algorithm and is applied at every nesting
	 * level: a scalar (or `null`) in $incoming replaces $current outright, an
	 * associative array merges key by key (recursing here for each key, with a
	 * `null` value deleting that key), and a list either replaces $current
	 * wholesale ($replace_lists) or merges into it by member identity. The
	 * $replace_lists flag is carried down through associative nesting so that,
	 * under replace(), every list reached along the way is swapped wholesale.
	 *
	 * @since 7.1.0
	 *
	 * @param mixed $current       The current value.
	 * @param mixed $incoming      The incoming value.
	 * @param bool  $replace_lists Whether a list in $incoming replaces the current list
	 *                             wholesale instead of merging into it by member identity.
	 * @return mixed The merged value.
	 */
	private function merge_properties( $current, $incoming, $replace_lists ) {
		// Scalar properties are merged as-is.
		if ( ! is_array( $incoming ) ) {
			return $incoming;
		}

		// Numerical indexed arrays are expected to be lists (sequential integer keys starting at 0).
		if ( array_is_list( $incoming ) ) {
			// replace() takes an incoming list as-is; merge() merges it by member identity.
			if ( $replace_lists ) {
				// As-is except for nulls: a list swapped in wholesale has no
				// existing leaf for a null to delete (the same rationale as
				// set()), so a null member is dropped rather than stored.
				return $this->strip_nulls( $incoming );
			}
			return $this->merge_list_by_identity(
				is_array( $current ) && array_is_list( $current ) ? $current : array(),
				$incoming
			);
		}

		// Consider any other array as associative (keys are strings).
		$result = is_array( $current ) && ! array_is_list( $current ) ? $current : array();
		foreach ( $incoming as $key => $value ) {
			// A null patch value deletes the property.
			if ( null === $value ) {
				unset( $result[ $key ] );
				continue;
			}

			$result[ $key ] = $this->merge_properties(
				array_key_exists( $key, $result ) ? $result[ $key ] : array(),
				$value,
				$replace_lists
			);
		}

		return $result;
	}

	/**
	 * Removes the properties a spec names from the current value.
	 *
	 * The mirror of merge_properties(), applied at every nesting level: a list in
	 * $spec names entries to delete from $current — associative keys are unset,
	 * and list members are matched by identity (list_item_identity) and dropped —
	 * while an associative $spec recurses into each named entry to prune from
	 * within it. A name absent from $current is ignored, and a list is renumbered
	 * after members are removed so it keeps sequential keys.
	 *
	 * @since 7.1.0
	 *
	 * @param mixed $current The current value.
	 * @param mixed $spec    The names to remove from it.
	 * @return mixed The pruned value.
	 */
	private function remove_properties( $current, $spec ) {
		if ( ! is_array( $current ) || ! is_array( $spec ) ) {
			return $current;
		}

		$current_is_list = array_is_list( $current );

		if ( array_is_list( $spec ) ) {
			// Each entry names something to delete from the current value.
			foreach ( $spec as $name ) {
				if ( $current_is_list ) {
					$current = $this->remove_list_member( $current, $name );
				} else {
					unset( $current[ $name ] );
				}
			}
		} else {
			// Each key names an entry to recurse into and prune from within.
			foreach ( $spec as $name => $subspec ) {
				if ( $current_is_list ) {
					foreach ( $current as $index => $member ) {
						if ( $this->list_item_identity( $member ) === (string) $name ) {
							$current[ $index ] = $this->remove_properties( $member, $subspec );
							break;
						}
					}
				} elseif ( array_key_exists( $name, $current ) ) {
					$current[ $name ] = $this->remove_properties( $current[ $name ], $subspec );
				}
			}
		}

		// Renumber so a list from which a member was removed keeps sequential keys.
		return $current_is_list ? array_values( $current ) : $current;
	}

	/**
	 * Removes the first list member matching an identity, leaving the rest.
	 *
	 * @since 7.1.0
	 *
	 * @param array $members  The current list.
	 * @param mixed $identity The identity of the member to remove.
	 * @return array The list with the matching member removed, if any.
	 */
	private function remove_list_member( array $members, $identity ) {
		foreach ( $members as $index => $member ) {
			if ( $this->list_item_identity( $member ) === (string) $identity ) {
				unset( $members[ $index ] );
				break;
			}
		}

		return $members;
	}

	/**
	 * Merges an incoming list into the current one by member identity.
	 *
	 * A member of the incoming list whose identity matches one already present
	 * merges into it in place, keeping its position; an unmatched member is
	 * appended to the end, except a literal `null`, which carries no identity
	 * and holds nothing to merge and so is dropped. A matched member's contents
	 * merge recursively with the same rules (merge_properties), so the
	 * identity-aware merge applies at
	 * any nesting level: each key named by the patch is substituted while the
	 * others are left intact, and a list nested inside a member merges by
	 * identity just like the list it lives in.
	 *
	 * @since 7.1.0
	 *
	 * @param array $current  The current list.
	 * @param array $incoming The incoming list.
	 * @return array The merged list.
	 */
	private function merge_list_by_identity( array $current, array $incoming ) {
		$result = $current;
		foreach ( $incoming as $item ) {
			// A null member carries no identity and holds nothing to merge,
			// so it is dropped rather than appended as a literal null.
			if ( null === $item ) {
				continue;
			}

			$identity = $this->list_item_identity( $item );

			// Find the index of the existing member with the same identity, if any.
			// If there's none, append the incoming member to the end of the list.
			$index = null;
			if ( null !== $identity ) {
				foreach ( $result as $i => $existing ) {
					if ( $this->list_item_identity( $existing ) === $identity ) {
						$index = $i;
						break;
					}
				}
			}
			if ( null === $index ) {
				$result[] = $item;
				continue;
			}

			// Otherwise, merge the incoming member into the existing one in place.
			$result[ $index ] = $this->merge_properties( $result[ $index ], $item, false );
		}

		return $result;
	}

	/**
	 * Resolves the identity used to match a list member against another.
	 *
	 * The identity is simply the member's value cast to a string, regardless of
	 * which key carries it: a bare scalar is its own identity, and a map is
	 * identified by the value of the first of the well-known identity keys
	 * (`id`, `slug`, `field`) it carries. Because the key is not part of
	 * the identity, a bare field like `'f3'` matches any map carrying that
	 * value, whether it appears as `array( 'id' => 'f3' )`,
	 * `array( 'slug' => 'f3' )`, and so on — this lets the same shorthand target
	 * lists keyed by different fields. Casting to string keeps numeric
	 * identities matching whether they arrive as an int or a string. Anything
	 * else (e.g. a nested list) has no identity and never matches, so it is
	 * always appended.
	 *
	 * @since 7.1.0
	 *
	 * @param mixed $item The list member.
	 * @return string|null The identity, or null when the member has none.
	 */
	private function list_item_identity( $item ) {
		if ( is_scalar( $item ) ) {
			return (string) $item;
		}

		if ( is_array( $item ) && ! array_is_list( $item ) ) {
			foreach ( array( 'id', 'slug', 'field' ) as $key ) {
				if ( isset( $item[ $key ] ) && is_scalar( $item[ $key ] ) ) {
					return (string) $item[ $key ];
				}
			}
		}

		return null;
	}
}
