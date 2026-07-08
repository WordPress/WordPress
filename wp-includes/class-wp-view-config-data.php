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
 * `form` — and there are two ways to contribute:
 *
 * - The `update_*()` methods merge partial changes (patches) into what is
 *   already there, each covering one part of the configuration:
 *   `update_properties()` for `default_view`, `default_layouts`, and the
 *   `form` settings other than its `fields`; `update_view_list_items()` for
 *   the `view_list` entries, keyed by view `slug`; and `update_form_fields()`
 *   for the `form` fields, keyed by field `id`. This is what plugins should
 *   use: patches compose with core's configuration and with other plugins'.
 * - `set()` replaces a whole top-level key. It shouldn't be the default
 *   choice — a callback using it stops inheriting core's future changes to
 *   that key — but it's useful for cases like a post type that doesn't
 *   want the default form at all.
 *
 * Patches follow three shared rules: an associative array merges key by
 * key, a numerically indexed array replaces the current value wholesale,
 * and `null` deletes what it names — deleting a whole top-level key resets
 * it to its default. Each patch and each `set()` value also declares the
 * configuration schema version it was written against (currently 1), so a
 * future WordPress release that changes the configuration shape can migrate
 * existing patches forward instead of breaking them.
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
	 * Constructor.
	 *
	 * @since 7.1.0
	 *
	 * @param array $config The base configuration to contribute to.
	 */
	public function __construct( array $config ) {
		$this->config = $config;
	}

	/**
	 * Returns the current configuration array.
	 *
	 * @since 7.1.0
	 *
	 * @return array The configuration.
	 */
	public function get_config() {
		return $this->config;
	}

	/**
	 * Replaces a whole top-level key with a new value.
	 *
	 * It shouldn't be the default choice — a callback using it stops
	 * inheriting core's future changes to that key — but it's useful for
	 * cases like a post type that doesn't want the default form at all.
	 *
	 * A value that declares an unsupported schema version is rejected and
	 * does not replace anything.
	 *
	 * @since 7.1.0
	 *
	 * @param string $key     The configuration key to replace.
	 * @param mixed  $value   The new value.
	 * @param int    $version The schema version the value was authored against.
	 * @return WP_View_Config_Data The instance, for chaining.
	 */
	public function set( $key, $value, int $version ) {
		if ( ! $this->check_version( $version, __METHOD__ ) ) {
			return $this;
		}

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
			return $this;
		}

		$this->config[ $key ] = $value;
		return $this;
	}

	/**
	 * Merges a partial configuration into `default_view`, `default_layouts`,
	 * and the `form` settings other than its `fields`.
	 *
	 * An associative array merges key by key, a numerically indexed array
	 * replaces the current value wholesale, and `null` deletes the key it
	 * names; deleting a whole top-level key (any documented key, including
	 * `view_list`) resets it to its default.
	 *
	 * The keyed collections have dedicated methods and are rejected here: a
	 * non-null `view_list` value must go through `update_view_list_items()`,
	 * and a `fields` key inside a `form` value must go through
	 * `update_form_fields()`.
	 *
	 * A patch that declares an unsupported schema version is rejected and
	 * does not merge.
	 *
	 * @since 7.1.0
	 *
	 * @param array $patch   The partial configuration to merge.
	 * @param int   $version The schema version the patch was authored against.
	 * @return WP_View_Config_Data The instance, for chaining.
	 */
	public function update_properties( array $patch, int $version ) {
		if ( ! $this->check_version( $version, __METHOD__ ) ) {
			return $this;
		}

		foreach ( $patch as $key => $value ) {
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
			// A null patch value drops the whole key from the container rather
			// than assigning null.
			if ( null === $value ) {
				unset( $this->config[ $key ] );
				continue;
			}
			if ( 'view_list' === $key ) {
				_doing_it_wrong(
					__METHOD__,
					esc_html__( 'The "view_list" entries are patched by identity. Use update_view_list_items() instead.' ),
					'7.1.0'
				);
				continue;
			}
			if ( 'form' === $key ) {
				$value = $this->extract_form_properties( $value );
				// Nothing left to merge: the value was off-shape, or held only
				// the rejected `fields` key.
				if ( null === $value || array() === $value ) {
					continue;
				}
			}
			$this->config[ $key ] = $this->deep_merge( $this->config[ $key ] ?? array(), $value );
		}

		return $this;
	}

	/**
	 * Adds, updates, or removes `view_list` entries, keyed by view `slug`.
	 *
	 * Each patch key names the `slug` of the view it targets: a matching view
	 * merges in place and keeps its position (following the shared rules —
	 * e.g. the view's `filters`, being numerically indexed, replace
	 * wholesale), an unknown slug appends a new view to the end, and `null`
	 * removes the view. The patch key is the identity: a `slug` property
	 * inside the value is ignored. A `null` for a slug that is not found is a
	 * silent no-op — the view may have been removed by another callback or
	 * simply not apply to this entity.
	 *
	 * A patch that declares an unsupported schema version is rejected and
	 * does not merge.
	 *
	 * @since 7.1.0
	 *
	 * @param array $items   The view patches, keyed by slug.
	 * @param int   $version The schema version the patch was authored against.
	 * @return WP_View_Config_Data The instance, for chaining.
	 */
	public function update_view_list_items( array $items, int $version ) {
		if ( ! $this->check_version( $version, __METHOD__ ) ) {
			return $this;
		}

		if ( empty( $items ) ) {
			return $this;
		}
		if ( array_is_list( $items ) ) {
			_doing_it_wrong(
				__METHOD__,
				esc_html__( 'A view list patch must be keyed by view "slug".' ),
				'7.1.0'
			);
			return $this;
		}

		$view_list = isset( $this->config['view_list'] ) && is_array( $this->config['view_list'] ) ? $this->config['view_list'] : array();

		foreach ( $items as $slug => $value ) {
			// PHP casts numeric-string array keys to integers; identities are strings.
			$slug = (string) $slug;

			if ( null === $value ) {
				$view_list = array_values(
					array_filter(
						$view_list,
						static fn( $item ) => ! is_array( $item ) || ! isset( $item['slug'] ) || $item['slug'] !== $slug
					)
				);
				continue;
			}

			if ( ! is_array( $value ) || ( array() !== $value && array_is_list( $value ) ) ) {
				_doing_it_wrong(
					__METHOD__,
					esc_html__( 'Each view patch must be an associative array of view properties, or null to remove the view.' ),
					'7.1.0'
				);
				continue;
			}

			// The patch key is the identity.
			unset( $value['slug'] );

			$index = null;
			foreach ( $view_list as $i => $item ) {
				if ( is_array( $item ) && isset( $item['slug'] ) && $item['slug'] === $slug ) {
					$index = $i;
					break;
				}
			}

			if ( null === $index ) {
				$view_list[] = array_merge( array( 'slug' => $slug ), $value );
				continue;
			}
			// An empty patch value has nothing to merge (and deep_merge would
			// treat an empty array as a list, replacing the whole view).
			if ( array() !== $value ) {
				$view_list[ $index ] = $this->deep_merge( $view_list[ $index ], $value );
			}
		}

		$this->config['view_list'] = array_values( $view_list );

		return $this;
	}

	/**
	 * Adds, updates, or removes `form` fields, keyed by field `id`.
	 *
	 * Each patch key names the `id` of the field it targets, and the field is
	 * found wherever it lives — at the top level or nested inside a group's
	 * `children`. Fields are visited in document order and a group is checked
	 * before its own children, so when an id appears at both levels the group
	 * wins. A matching field merges in place, an unknown id appends a new field
	 * to the end of the top-level fields, and `null` removes the field. The
	 * patch key is the identity: an `id` property inside the value is ignored.
	 * A `null` for an id that is not found is a silent no-op — the field may
	 * have been removed by another callback or simply not apply to this
	 * entity.
	 *
	 * Inside a field patch, `children` follows the shared rules: an associative
	 * array merges into the group's children by id (appending unknown ones), a
	 * numerically indexed array replaces the children wholesale, and `null`
	 * deletes the key.
	 *
	 * A patch that declares an unsupported schema version is rejected and
	 * does not merge.
	 *
	 * @since 7.1.0
	 *
	 * @param array $fields  The field patches, keyed by field id.
	 * @param int   $version The schema version the patch was authored against.
	 * @return WP_View_Config_Data The instance, for chaining.
	 */
	public function update_form_fields( array $fields, int $version ) {
		if ( ! $this->check_version( $version, __METHOD__ ) ) {
			return $this;
		}

		if ( empty( $fields ) ) {
			return $this;
		}
		if ( array_is_list( $fields ) ) {
			_doing_it_wrong(
				__METHOD__,
				esc_html__( 'A fields patch must be keyed by field "id".' ),
				'7.1.0'
			);
			return $this;
		}

		if ( ! isset( $this->config['form'] ) || ! is_array( $this->config['form'] ) ) {
			$this->config['form'] = array();
		}
		$current = isset( $this->config['form']['fields'] ) && is_array( $this->config['form']['fields'] ) ? $this->config['form']['fields'] : array();

		$this->config['form']['fields'] = $this->merge_fields_by_identity( $current, $fields );

		return $this;
	}

	/**
	 * Validates a declared patch version, reporting misuse against the given
	 * public method.
	 *
	 * @since 7.1.0
	 *
	 * @param int    $version The declared version.
	 * @param string $method  The public method the patch was passed to.
	 * @return bool Whether the declared version is a supported schema version.
	 */
	private function check_version( int $version, $method ) {
		if ( $version >= 1 && $version <= self::LATEST_VERSION ) {
			return true;
		}

		_doing_it_wrong(
			esc_html( $method ),
			esc_html__( 'A view configuration contribution must declare a supported schema version.' ),
			'7.1.0'
		);

		return false;
	}

	/**
	 * Validates a `form` patch value for update_properties() and strips the
	 * `fields` key, which is managed by update_form_fields().
	 *
	 * @since 7.1.0
	 *
	 * @param mixed $value The incoming `form` patch value.
	 * @return array|null The form properties to merge, or null when the value
	 *                    is off-shape.
	 */
	private function extract_form_properties( $value ) {
		if ( ! is_array( $value ) || ( array() !== $value && array_is_list( $value ) ) ) {
			_doing_it_wrong(
				'WP_View_Config_Data::update_properties',
				esc_html__( 'A "form" patch must be an associative array of form properties.' ),
				'7.1.0'
			);
			return null;
		}
		if ( array_key_exists( 'fields', $value ) ) {
			_doing_it_wrong(
				'WP_View_Config_Data::update_properties',
				esc_html__( 'The form "fields" are patched by identity. Use update_form_fields() instead.' ),
				'7.1.0'
			);
			unset( $value['fields'] );
		}

		return $value;
	}

	/**
	 * Recursively merges two values.
	 *
	 * Associative arrays (maps) merge key by key and a null patch value deletes
	 * the key; lists and scalars are replaced wholesale by the incoming value,
	 * since lists without a defined identity cannot be merged member by member.
	 *
	 * @since 7.1.0
	 *
	 * @param mixed $current  The current value.
	 * @param mixed $incoming The incoming value.
	 * @return mixed The merged value.
	 */
	private function deep_merge( $current, $incoming ) {
		// An empty array counts as a list, so patching with array() empties
		// the key (e.g. 'filters' => array() clears the filters) rather than
		// being a no-op map merge.
		if ( ! is_array( $incoming ) || array_is_list( $incoming ) ) {
			return $incoming;
		}

		// Merge onto the current map, or onto an empty base when the current
		// value is absent, empty, or not a map, so null delete-markers in the
		// patch are consumed rather than stored as literal values (e.g.
		// array( 'layout' => null ) merged into an empty layouts entry yields
		// array(), not array( 'layout' => null )).
		$result = is_array( $current ) && ! array_is_list( $current ) ? $current : array();
		foreach ( $incoming as $key => $value ) {
			if ( null === $value ) {
				// A null patch value deletes the key.
				unset( $result[ $key ] );
				continue;
			}
			$result[ $key ] = $this->deep_merge(
				array_key_exists( $key, $result ) ? $result[ $key ] : array(),
				$value
			);
		}
		return $result;
	}

	/**
	 * Merges a map of field patches into a field list by identity.
	 *
	 * Shared by the top-level `form` fields and a group's `children`: a `null`
	 * value removes the matching field (recursing into children), a map value
	 * merges into the matching field wherever it lives, and an unknown id
	 * appends a new field to the end of this list. A `null` for an id that is
	 * not found is a silent no-op.
	 *
	 * @since 7.1.0
	 *
	 * @param array $current The current list of fields.
	 * @param array $patches The field patches, keyed by field id.
	 * @return array The merged list of fields.
	 */
	private function merge_fields_by_identity( array $current, array $patches ) {
		foreach ( $patches as $id => $value ) {
			// PHP casts numeric-string array keys to integers; identities are strings.
			$id = (string) $id;

			if ( null === $value ) {
				$current = $this->reject_fields( $current, array( $id ) );
				continue;
			}
			if ( ! is_array( $value ) || ( array() !== $value && array_is_list( $value ) ) ) {
				_doing_it_wrong(
					'WP_View_Config_Data::update_form_fields',
					esc_html__( 'Each field patch must be an associative array of field properties, or null to remove the field.' ),
					'7.1.0'
				);
				continue;
			}

			// The patch key is the identity.
			unset( $value['id'] );

			$merged = $this->merge_field_in_tree( $current, $id, $value );
			if ( null !== $merged ) {
				$current = $merged;
				continue;
			}
			// An unknown id appends: as a bare string reference when the patch
			// carries no overrides, as an array otherwise.
			$current[] = array() === $value ? $id : $this->merge_field_item( $id, $id, $value );
		}

		return $current;
	}

	/**
	 * Merges a field patch into the field carrying the given identity, wherever
	 * it lives in the tree.
	 *
	 * Fields are visited in document order and a group is checked before its
	 * own children, so when an id appears at both levels the group wins.
	 *
	 * @since 7.1.0
	 *
	 * @param array  $fields The list of fields to search.
	 * @param string $id     The identity of the field to patch.
	 * @param array  $value  The field patch.
	 * @return array|null The updated list, or null when the id was not found.
	 */
	private function merge_field_in_tree( array $fields, $id, array $value ) {
		foreach ( $fields as $index => $field ) {
			if ( $this->field_identity( $field ) === $id ) {
				$fields[ $index ] = $this->merge_field_item( $field, $id, $value );
				return $fields;
			}
			if ( is_array( $field ) && isset( $field['children'] ) && is_array( $field['children'] ) ) {
				$children = $this->merge_field_in_tree( $field['children'], $id, $value );
				if ( null !== $children ) {
					$fields[ $index ]['children'] = $children;
					return $fields;
				}
			}
		}

		return null;
	}

	/**
	 * Merges a field patch into an existing field.
	 *
	 * A bare string reference is promoted to an array so the overrides apply.
	 * The `children` key follows the same rules — a map merges into the
	 * group's children by id, a list replaces them wholesale, and `null`
	 * deletes the key — and every other key merges via deep_merge().
	 *
	 * @since 7.1.0
	 *
	 * @param array|string $existing The existing field.
	 * @param string       $id       The field identity.
	 * @param array        $value    The field patch.
	 * @return array|string The merged field.
	 */
	private function merge_field_item( $existing, $id, array $value ) {
		if ( ! is_array( $existing ) ) {
			// Nothing to apply: keep the bare string reference.
			if ( array() === $value ) {
				return $existing;
			}
			// Promote the reference so the incoming overrides apply.
			$existing = array( 'id' => $id );
		}

		foreach ( $value as $key => $item ) {
			if ( 'children' === $key ) {
				if ( null === $item ) {
					unset( $existing['children'] );
					continue;
				}
				if ( ! is_array( $item ) ) {
					_doing_it_wrong(
						'WP_View_Config_Data::update_form_fields',
						esc_html__( 'A "children" patch must be an associative array keyed by field id to merge, a numerically indexed array to replace the children wholesale, or null to delete the key.' ),
						'7.1.0'
					);
					continue;
				}
				// A list replaces the children wholesale (an empty array counts
				// as a list, clearing them)...
				if ( array_is_list( $item ) ) {
					$existing['children'] = $item;
					continue;
				}
				// ...and a map merges into them by identity.
				$children             = isset( $existing['children'] ) && is_array( $existing['children'] ) ? $existing['children'] : array();
				$existing['children'] = $this->merge_fields_by_identity( $children, $item );
				continue;
			}
			if ( null === $item ) {
				// A null patch value deletes the key.
				unset( $existing[ $key ] );
				continue;
			}
			$existing[ $key ] = $this->deep_merge(
				array_key_exists( $key, $existing ) ? $existing[ $key ] : array(),
				$item
			);
		}

		return $existing;
	}

	/**
	 * Returns a field list with the fields matching the given identities removed,
	 * recursing into group children.
	 *
	 * @since 7.1.0
	 *
	 * @param array    $fields The list of fields.
	 * @param string[] $ids    The identities of the fields to remove.
	 * @return array The list with the matching fields removed.
	 */
	private function reject_fields( array $fields, array $ids ) {
		$result = array();
		foreach ( $fields as $field ) {
			if ( in_array( $this->field_identity( $field ), $ids, true ) ) {
				continue;
			}
			if ( is_array( $field ) && isset( $field['children'] ) && is_array( $field['children'] ) ) {
				$field['children'] = $this->reject_fields( $field['children'], $ids );
			}
			$result[] = $field;
		}
		return $result;
	}

	/**
	 * Resolves the identity of a form field.
	 *
	 * A bare string is its own identity; an object is identified by its `id`.
	 *
	 * @since 7.1.0
	 *
	 * @param mixed $field The field.
	 * @return string|null The identity, or null if it cannot be resolved.
	 */
	private function field_identity( $field ) {
		if ( is_string( $field ) ) {
			return $field;
		}
		if ( is_array( $field ) && isset( $field['id'] ) && is_string( $field['id'] ) ) {
			return $field['id'];
		}
		return null;
	}
}
