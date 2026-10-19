<?php
/**
 * Entity Fields API: WP_Entity_Fields_Registry class
 *
 * @package WordPress
 * @subpackage Entity Fields API
 * @since 7.2.0
 */

/**
 * Holds the fields registered for entities.
 *
 * The registry maps an entity, identified by its kind and name, to its
 * registered field definitions keyed by id, in registration order, each
 * with the origins that registered and updated it, and to the script modules
 * registered for it, each with the ids of the fields it applies to.
 * register() adds fields, update() changes registered ones, and both
 * validate the definitions before storing them: a field that cannot be
 * stored is reported and skipped, and the rest of the call is stored.
 * register_collection() adds the fields a folder declares, see it for the
 * format.
 *
 * Fields are registered on the `wp_entity_fields_api_init` action, and only
 * there, with wp_register_entity_fields(), wp_update_entity_fields(), and
 * wp_unregister_entity_fields(): register(), update(), and unregister() refuse
 * to run while the action is not firing.
 *
 * The registry is filled lazily: the first time its fields are read, it
 * fires the `wp_entity_fields_api_init` action, on which the default fields of every
 * post type and the fields of plugins are registered. A read before `init`
 * has completed is refused, see initialize().
 *
 * Once the action has fired the registry does not change: every reader of
 * a request, the REST controller as well as the import map of the editor
 * script, sees the same fields.
 *
 * @since 7.2.0
 *
 * @access private
 */
class WP_Entity_Fields_Registry {

	/**
	 * Registered fields, as kind => name => array of field id => definition.
	 *
	 * Kept nested rather than under a joined `{$kind}/{$name}` key: both parts
	 * may contain any character, so no separator keeps two entities apart.
	 *
	 * @since 7.2.0
	 * @var array<string, array<string, array<string, array>>>
	 */
	protected $fields = array();

	/**
	 * Registered script modules, as kind => name => array of module id =>
	 * list of the ids of the fields the module applies to, in registration
	 * order.
	 *
	 * @since 7.2.0
	 * @var array<string, array<string, array<string, string[]>>>
	 */
	protected $field_modules = array();

	/**
	 * Whether the `wp_entity_fields_api_init` action has fired since the registry
	 * was created.
	 *
	 * @since 7.2.0
	 * @var bool
	 */
	protected $initialized = false;

	/**
	 * The singleton instance.
	 *
	 * @since 7.2.0
	 * @var WP_Entity_Fields_Registry|null
	 */
	protected static $instance = null;

	/**
	 * Returns the singleton instance.
	 *
	 * @since 7.2.0
	 *
	 * @return WP_Entity_Fields_Registry The registry.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Registers fields for an entity.
	 *
	 * The fields must be new: a field with the id of a registered field is
	 * reported and skipped, and so is every definition after the first of an
	 * id that appears more than once in the call. The rest of the fields of
	 * the call are registered: a field another plugin got to first does not
	 * cost a plugin the others. A definition that is not an array with a
	 * non-empty string `id` is reported and skipped the same way. To change
	 * a registered field, see update(); to replace it, unregister it first.
	 * The script module, if any, applies to every field the call registers.
	 * A field whose `type` DataViews does not provide is reported and
	 * registered anyway.
	 *
	 * The origin is stored as the `origin` property of each field, as
	 * `registeredBy`, replacing any `origin` the definition sets.
	 *
	 * @since 7.2.0
	 *
	 * @param string      $origin        Who registers the fields: `core`, or
	 *                                   the slug of the plugin or theme.
	 * @param string      $kind          The entity kind (e.g. `postType`).
	 * @param string      $name          The entity name (e.g. `page`).
	 * @param array[]     $fields        The list of field definitions, each with an `id`.
	 * @param string|null $script_module The id of the script module providing
	 *                                   the JavaScript parts of the fields, if any.
	 * @return string[] The ids of the fields registered, in the order of the
	 *                  call. Empty when none is, when called outside the
	 *                  `wp_entity_fields_api_init` action, or when an argument is
	 *                  invalid.
	 */
	public function register( $origin, $kind, $name, $fields, $script_module = null ) {
		if ( ! $this->validate_arguments( __METHOD__, $origin, $kind, $name, $fields, $script_module ) ) {
			return array();
		}
		$fields = $this->skip_invalid_definitions( __METHOD__, $kind, $name, $fields );
		if ( ! $fields ) {
			return array();
		}

		$new_fields     = array();
		$duplicated     = array();
		$registered_ids = array();
		foreach ( $fields as $field ) {
			$id = $field['id'];
			if ( isset( $this->fields[ $kind ][ $name ][ $id ] ) ) {
				$registered_ids[ $id ] = $id;
			} elseif ( isset( $new_fields[ $id ] ) ) {
				$duplicated[ $id ] = $id;
			} else {
				$new_fields[ $id ] = $field;
			}
		}

		if ( $duplicated ) {
			_doing_it_wrong(
				__METHOD__,
				sprintf(
					/* translators: 1: Entity kind, e.g. postType. 2: Entity name, e.g. page. 3: Comma-separated list of field ids. */
					__( 'A field can only be registered once. These fields of %1$s "%2$s" appear more than once in the same call: %3$s. Only their first definition is registered.' ),
					$kind,
					$name,
					implode( ', ', $duplicated )
				),
				'7.2.0'
			);
		}

		if ( $registered_ids ) {
			_doing_it_wrong(
				__METHOD__,
				sprintf(
					/* translators: 1: Entity kind, e.g. postType. 2: Entity name, e.g. page. 3: Comma-separated list of field ids. 4: update() */
					__( 'A field can only be registered once. These fields of %1$s "%2$s" are skipped because they are already registered: %3$s. Use %4$s to change a registered field, or unregister it first to replace it.' ),
					$kind,
					$name,
					implode( ', ', $registered_ids ),
					'<code>update()</code>'
				),
				'7.2.0'
			);
		}

		if ( ! $new_fields ) {
			return array();
		}
		$this->report_unknown_types( __METHOD__, $kind, $name, $new_fields );

		foreach ( $new_fields as $field ) {
			$this->fields[ $kind ][ $name ][ $field['id'] ] = array_merge(
				$field,
				array(
					'origin' => array(
						'registeredBy' => $origin,
						'updatedBy'    => array(),
					),
				)
			);
		}
		$ids = array_column( $new_fields, 'id' );
		$this->add_field_module( $kind, $name, $ids, $script_module );

		return $ids;
	}

	/**
	 * Updates registered fields of an entity.
	 *
	 * Each definition is merged into the registered field with its id,
	 * property by property; the field keeps its position. The fields must be
	 * registered: a field that is not is reported and skipped, and so is a
	 * definition that is not an array with a non-empty string `id`; the rest
	 * of the fields of the call are updated. The script module, if any,
	 * applies to every field the call updates, on top of the modules the
	 * fields have. A field whose `type` DataViews does not provide is
	 * reported and updated anyway.
	 *
	 * The origin is appended to the `updatedBy` list of the `origin` property
	 * of each field, once; the rest of the `origin` property cannot be
	 * updated, and any `origin` the definition sets is ignored.
	 *
	 * Like register(), it only runs on the `wp_entity_fields_api_init` action.
	 *
	 * @since 7.2.0
	 *
	 * @param string      $origin        Who updates the fields: `core`, or
	 *                                   the slug of the plugin or theme.
	 * @param string      $kind          The entity kind (e.g. `postType`).
	 * @param string      $name          The entity name (e.g. `page`).
	 * @param array[]     $fields        The list of partial field definitions,
	 *                                   each with the `id` of a registered field.
	 * @param string|null $script_module The id of the script module providing
	 *                                   the JavaScript parts of the fields, if any.
	 * @return string[] The ids of the fields updated, in the order of the
	 *                  call. Empty when none is, when called outside the
	 *                  `wp_entity_fields_api_init` action, or when an argument is
	 *                  invalid.
	 */
	public function update( $origin, $kind, $name, $fields, $script_module = null ) {
		if ( ! $this->validate_arguments( __METHOD__, $origin, $kind, $name, $fields, $script_module ) ) {
			return array();
		}
		$fields = $this->skip_invalid_definitions( __METHOD__, $kind, $name, $fields );
		if ( ! $fields ) {
			return array();
		}
		$this->report_unknown_types( __METHOD__, $kind, $name, $fields );

		$ids              = array();
		$unregistered_ids = array();
		foreach ( $fields as $field ) {
			$id = $field['id'];
			if ( ! isset( $this->fields[ $kind ][ $name ][ $id ] ) ) {
				$unregistered_ids[ $id ] = $id;
				continue;
			}

			$registered   = $this->fields[ $kind ][ $name ][ $id ];
			$field_origin = $registered['origin'];
			if ( ! in_array( $origin, $field_origin['updatedBy'], true ) ) {
				$field_origin['updatedBy'][] = $origin;
			}

			$this->fields[ $kind ][ $name ][ $id ] = array_merge( $registered, $field, array( 'origin' => $field_origin ) );
			if ( ! in_array( $id, $ids, true ) ) {
				$ids[] = $id;
			}
		}

		if ( $unregistered_ids ) {
			_doing_it_wrong(
				__METHOD__,
				sprintf(
					/* translators: 1: Entity kind, e.g. postType. 2: Entity name, e.g. page. 3: Comma-separated list of field ids. 4: register() */
					__( 'Only registered fields can be updated. These fields of %1$s "%2$s" are skipped because they are not registered: %3$s. Use %4$s to add a field.' ),
					$kind,
					$name,
					implode( ', ', $unregistered_ids ),
					'<code>register()</code>'
				),
				'7.2.0'
			);
		}

		if ( $ids ) {
			$this->add_field_module( $kind, $name, $ids, $script_module );
		}

		return $ids;
	}

	/**
	 * Unregisters fields of an entity.
	 *
	 * Unregistering a field removes its registered definition and
	 * drops it from the script modules that applied to it (a module left with
	 * no field is forgotten). Unregistering every field forgets the entity:
	 * its registered fields and script modules.
	 *
	 * Like register(), it only runs on the `wp_entity_fields_api_init` action, and an
	 * invalid entity is reported and unregisters nothing. An id that is not a
	 * non-empty string is reported and skipped, like a definition register()
	 * cannot store.
	 *
	 * @since 7.2.0
	 *
	 * @param string        $kind The entity kind (e.g. `postType`).
	 * @param string        $name The entity name (e.g. `page`).
	 * @param string[]|null $ids  The ids of the fields to unregister. Default
	 *                            null, every field of the entity.
	 * @return array[] The list of the definitions unregistered, in
	 *                 registration order, as get_registered() returns them.
	 *                 Empty when none of the fields is registered, when
	 *                 called outside the `wp_entity_fields_api_init` action, or when
	 *                 the entity is invalid.
	 */
	public function unregister( $kind, $name, $ids = null ) {
		if ( ! $this->doing_entity_fields_api_init( __METHOD__ ) || ! $this->validate_entity( __METHOD__, $kind, $name ) ) {
			return array();
		}

		if ( null === $ids ) {
			$unregistered = array_values( $this->fields[ $kind ][ $name ] ?? array() );
			unset( $this->fields[ $kind ][ $name ], $this->field_modules[ $kind ][ $name ] );
			$this->forget_empty_entity( $kind, $name );
			return $unregistered;
		}

		$ids = $this->skip_invalid_ids( __METHOD__, $kind, $name, (array) $ids );
		if ( ! $ids ) {
			return array();
		}

		$unregistered = array_intersect_key( $this->fields[ $kind ][ $name ] ?? array(), array_flip( $ids ) );
		if ( empty( $unregistered ) ) {
			return array();
		}

		// A script module only ever applies to registered fields: register()
		// and update() add it for fields they store, and fields leave both
		// lists together here. So the fields removed are all the registry
		// loses, and the modules are not returned: the ones that applied to
		// them are listed by get_registered_field_modules(), read beforehand.
		$ids = array_keys( $unregistered );
		foreach ( $ids as $id ) {
			unset( $this->fields[ $kind ][ $name ][ $id ] );
		}
		foreach ( $this->field_modules[ $kind ][ $name ] ?? array() as $module => $module_ids ) {
			$remaining = array_values( array_diff( $module_ids, $ids ) );
			if ( empty( $remaining ) ) {
				unset( $this->field_modules[ $kind ][ $name ][ $module ] );
			} else {
				$this->field_modules[ $kind ][ $name ][ $module ] = $remaining;
			}
		}
		$this->forget_empty_entity( $kind, $name );

		return array_values( $unregistered );
	}

	/**
	 * Registers the fields of a field collection.
	 *
	 * A collection is a folder whose `index.php` returns its configuration, with
	 * a folder per field whose `field.php` returns the serializable part of the
	 * field (see get_collection_fields()). Each file is required without
	 * sharing the variables of the caller, see require_file(). The JavaScript
	 * parts of the fields, if any, ship in the script module of the
	 * collection. The configuration is an array with these keys:
	 *
	 * - `origin` (required): who registers the fields: `core`, or the slug of
	 *   the plugin or theme. See register().
	 * - `kind` (required): the entity kind, e.g. `postType`.
	 * - `name` (required): the entity name, e.g. `wp_template`.
	 * - `module` (optional): the id of the script module providing the
	 *   JavaScript parts of the fields. Every field of the collection is
	 *   registered with it.
	 *
	 * The collection registers its fields on the entity, after the fields
	 * registered on it before, in the alphabetical order of their folders.
	 * register() skips a field already registered for the entity, and
	 * registers the rest of the collection. Like register(), it does not check
	 * that the entity exists: the `/wp/v2/entity-fields` route only serves the fields
	 * of the entities the REST API exposes.
	 *
	 * Fields that apply to several entities, such as the defaults every post
	 * type derives from its supports, are registered in code: a
	 * `wp_entity_fields_api_init` callback can read their definitions from files with
	 * get_collection_fields() and register them on each entity with
	 * register().
	 *
	 * Like register(), it only runs on the `wp_entity_fields_api_init` action. An
	 * invalid configuration is reported with _doing_it_wrong() and skipped. So
	 * is an invalid `field.php`, see get_collection_fields(): the rest of the
	 * fields of the collection are registered.
	 *
	 * @since 7.2.0
	 *
	 * @param string $directory The directory of the collection.
	 * @return bool Whether the collection is valid and all of its fields are
	 *              registered. True as well when it has no fields; false when
	 *              a `field.php` is skipped, or when called outside the
	 *              `wp_entity_fields_api_init` action.
	 */
	public function register_collection( $directory ) {
		if ( ! $this->doing_entity_fields_api_init( __METHOD__ ) ) {
			return false;
		}

		$collection = $this->get_collection( __METHOD__, $directory );
		if ( null === $collection ) {
			return false;
		}

		if ( empty( $collection['fields'] ) ) {
			return ! $collection['skipped'];
		}
		$registered = $this->register( $collection['origin'], $collection['kind'], $collection['name'], $collection['fields'], $collection['module'] );

		return ! $collection['skipped'] && count( $registered ) === count( $collection['fields'] );
	}

	/**
	 * Returns the fields of a field collection.
	 *
	 * A collection holds a folder per field, whose `field.php` returns the
	 * serializable part of the field, see register_collection().
	 *
	 * @since 7.2.0
	 *
	 * @param string $directory The directory of the collection.
	 * @return array<string, array> The fields of the collection, keyed by id, in
	 *                              the alphabetical order of their folders. The
	 *                              id of a field is the name of its folder,
	 *                              unless its `field.php` sets an `id`, and it
	 *                              comes first in the field. A `field.php`
	 *                              that does not return an array with a
	 *                              non-empty string id, or whose id an earlier
	 *                              folder took, is reported and skipped.
	 */
	public function get_collection_fields( $directory ) {
		return $this->read_collection_fields( __METHOD__, $directory )['fields'];
	}

	/**
	 * Returns the fields registered for an entity.
	 *
	 * @since 7.2.0
	 *
	 * @param string $kind The entity kind (e.g. `postType`).
	 * @param string $name The entity name (e.g. `page`).
	 * @return array[] The list of field definitions, in registration order.
	 */
	public function get_registered( $kind, $name ) {
		$this->initialize();
		return array_values( $this->fields[ $kind ][ $name ] ?? array() );
	}

	/**
	 * Returns the fields registered for every entity.
	 *
	 * @since 7.2.0
	 *
	 * @return array<string, array<string, array[]>> The lists of field
	 *                                               definitions, keyed by
	 *                                               kind, then by name.
	 */
	public function get_all_registered() {
		$this->initialize();
		return array_map(
			static function ( $entities ) {
				return array_map( 'array_values', $entities );
			},
			$this->fields
		);
	}

	/**
	 * Returns the script modules registered for an entity.
	 *
	 * @since 7.2.0
	 *
	 * @param string $kind The entity kind (e.g. `postType`).
	 * @param string $name The entity name (e.g. `page`).
	 * @return array<string, string[]> The ids of the fields each module
	 *                                 applies to, keyed by module id, in
	 *                                 registration order.
	 */
	public function get_registered_field_modules( $kind, $name ) {
		$this->initialize();
		return $this->field_modules[ $kind ][ $name ] ?? array();
	}

	/**
	 * Returns the ids of the script modules registered for every entity.
	 *
	 * @since 7.2.0
	 *
	 * @return array<string, array<string, string[]>> The lists of module ids,
	 *                                                keyed by kind, then by
	 *                                                name.
	 */
	public function get_all_registered_field_modules() {
		$this->initialize();
		return array_map(
			static function ( $entities ) {
				return array_map( 'array_keys', $entities );
			},
			$this->field_modules
		);
	}

	/**
	 * Fires the `wp_entity_fields_api_init` action the first time the registry
	 * is read.
	 *
	 * The read is refused until `init` has completed. The registry holds the
	 * fields of post types, and post types are not known until then: core
	 * registers its own on `init` at priority 0, plugins theirs at the default
	 * priority, and supports are added and removed on `init` too. The default
	 * fields of a post type derive from its supports, and the action fires
	 * once, so a read before or during `init` would fix the defaults from the
	 * post types registered so far for the rest of the request.
	 *
	 * did_action() is true from the first `init` callback on, hence the
	 * doing_action() check for the reads made while it runs.
	 *
	 * @since 7.2.0
	 */
	protected function initialize() {
		if ( $this->initialized ) {
			return;
		}

		if ( ! did_action( 'init' ) || doing_action( 'init' ) ) {
			_doing_it_wrong(
				__METHOD__,
				sprintf(
					/* translators: 1: init, 2: wp_entity_fields_api_init */
					__( 'The registered fields cannot be read until the %1$s action has completed: the post types and supports they derive from are registered on %1$s. Read them once %1$s has completed, or hook %2$s.' ),
					'<code>init</code>',
					'<code>wp_entity_fields_api_init</code>'
				),
				'7.2.0'
			);
			return;
		}

		// Set before firing, so a callback reading the registry to inspect
		// the fields it updates does not fire the action again.
		$this->initialized = true;

		/**
		 * Fires the first time the registered fields are read, after `init`.
		 *
		 * Register or adjust fields here, with wp_register_entity_fields(),
		 * wp_update_entity_fields(), and wp_unregister_entity_fields(): it is the only
		 * time they run.
		 *
		 * The first read happens wherever the fields are needed: while
		 * handling a REST request as well as on `admin_footer` of the pages
		 * that load the editor script. A callback should register fields and
		 * nothing else; script modules, styles, and their enqueue hooks
		 * belong on `init`.
		 *
		 * @since 7.2.0
		 */
		do_action( 'wp_entity_fields_api_init' );
	}

	/**
	 * Checks that the `wp_entity_fields_api_init` action is firing, as register(),
	 * update(), and unregister() require.
	 *
	 * Outside the action a registration would either come before the
	 * defaults, which could then not be registered, or after the fields have been read
	 * and the import map of the editor script built from them. Refusing
	 * keeps the registry immutable once read.
	 *
	 * @since 7.2.0
	 *
	 * @param string $method The calling method, for the notice.
	 * @return bool Whether the action is firing.
	 */
	protected function doing_entity_fields_api_init( $method ) {
		if ( doing_action( 'wp_entity_fields_api_init' ) ) {
			return true;
		}

		_doing_it_wrong(
			$method,
			sprintf(
				/* translators: %s: wp_entity_fields_api_init */
				__( 'Fields can only be registered, updated, and unregistered on the %s action.' ),
				'<code>wp_entity_fields_api_init</code>'
			),
			'7.2.0'
		);
		return false;
	}

	/**
	 * Reports the fields whose `type` is not one the REST schema of the
	 * fields lists, with _doing_it_wrong(). Those are the types DataViews
	 * provides a control for: a misspelt type is otherwise silent, the field
	 * renders without a control. A missing `type` is fine.
	 *
	 * @since 7.2.0
	 *
	 * @param string  $method The calling method, for the notice.
	 * @param string  $kind   The entity kind.
	 * @param string  $name   The entity name.
	 * @param array[] $fields The field definitions.
	 */
	protected function report_unknown_types( $method, $kind, $name, $fields ) {
		$type_schema = ( new WP_REST_Entity_Fields_Controller() )->get_item_schema()['properties']['fields']['items']['properties']['type'];
		$unknown     = array();
		foreach ( $fields as $field ) {
			if ( ! isset( $field['type'] ) ) {
				continue;
			}
			$valid = rest_validate_value_from_schema( $field['type'], $type_schema, 'type' );
			if ( is_wp_error( $valid ) ) {
				$unknown[] = sprintf( '%s (%s)', $field['id'], $valid->get_error_message() );
			}
		}
		if ( ! $unknown ) {
			return;
		}
		_doing_it_wrong(
			$method,
			sprintf(
				/* translators: 1: Entity kind, e.g. postType. 2: Entity name, e.g. page. 3: Comma-separated list of field ids, each with the error of its type. */
				__( 'These fields of %1$s "%2$s" have a type DataViews does not provide, so they render without a control: %3$s' ),
				$kind,
				$name,
				implode( ', ', $unknown )
			),
			'7.2.0'
		);
	}

	/**
	 * Validates the arguments of register() and update().
	 *
	 * @since 7.2.0
	 *
	 * @param string $method        The calling method, for the notice.
	 * @param mixed  $origin        The origin.
	 * @param mixed  $kind          The entity kind.
	 * @param mixed  $name          The entity name.
	 * @param mixed  $fields        The field definitions.
	 * @param mixed  $script_module The script module id, if any.
	 * @return bool Whether the call may proceed: the `wp_entity_fields_api_init` action
	 *              is firing and the arguments are valid.
	 */
	protected function validate_arguments( $method, $origin, $kind, $name, $fields, $script_module ) {
		if ( ! $this->doing_entity_fields_api_init( $method ) ) {
			return false;
		}

		if ( ! is_string( $origin ) || '' === $origin ) {
			_doing_it_wrong(
				$method,
				__( 'The origin must be a non-empty string.' ),
				'7.2.0'
			);
			return false;
		}

		if ( ! $this->validate_entity( $method, $kind, $name ) ) {
			return false;
		}

		if ( ! is_array( $fields ) || empty( $fields ) || ! array_is_list( $fields ) ) {
			_doing_it_wrong(
				$method,
				__( 'The fields must be a list of non-empty field definitions.' ),
				'7.2.0'
			);
			return false;
		}

		if ( null !== $script_module && ( ! is_string( $script_module ) || '' === $script_module ) ) {
			_doing_it_wrong(
				$method,
				__( 'The script module must be the id of a script module.' ),
				'7.2.0'
			);
			return false;
		}

		return true;
	}

	/**
	 * Checks that the entity kind and name are non-empty strings, reporting
	 * them with _doing_it_wrong() otherwise.
	 *
	 * @since 7.2.0
	 *
	 * @param string $method The calling method, for the notice.
	 * @param mixed  $kind   The entity kind.
	 * @param mixed  $name   The entity name.
	 * @return bool Whether both are valid.
	 */
	protected function validate_entity( $method, $kind, $name ) {
		foreach ( array( $kind, $name ) as $argument ) {
			if ( ! is_string( $argument ) || '' === $argument ) {
				_doing_it_wrong(
					$method,
					__( 'The entity kind and the entity name must be non-empty strings.' ),
					'7.2.0'
				);
				return false;
			}
		}
		return true;
	}

	/**
	 * Drops the definitions that are not an array with a non-empty string
	 * `id`, reporting their positions in the call with _doing_it_wrong():
	 * like a duplicated field, an invalid definition does not cost the call
	 * its other fields.
	 *
	 * @since 7.2.0
	 *
	 * @param string  $method The calling method, for the notice.
	 * @param string  $kind   The entity kind.
	 * @param string  $name   The entity name.
	 * @param array[] $fields The list of field definitions.
	 * @return array[] The valid definitions, in the order of the call.
	 */
	protected function skip_invalid_definitions( $method, $kind, $name, $fields ) {
		$valid   = array();
		$invalid = array();
		foreach ( $fields as $position => $field ) {
			if ( is_array( $field ) && ! empty( $field['id'] ) && is_string( $field['id'] ) ) {
				$valid[] = $field;
			} else {
				$invalid[] = '#' . ( $position + 1 );
			}
		}

		if ( $invalid ) {
			_doing_it_wrong(
				$method,
				sprintf(
					/* translators: 1: Entity kind, e.g. postType. 2: Entity name, e.g. page. 3: Comma-separated list of positions, e.g. #2, #4. 4: id */
					__( 'Every field definition must be an array with a non-empty string %4$s. These definitions of %1$s "%2$s" are skipped: %3$s. The rest of the fields of the call are stored.' ),
					$kind,
					$name,
					implode( ', ', $invalid ),
					'<code>id</code>'
				),
				'7.2.0'
			);
		}

		return $valid;
	}

	/**
	 * Drops the ids that are not a non-empty string, reporting their
	 * positions in the call with _doing_it_wrong(): like an invalid
	 * definition, an invalid id does not cost the call its other ids.
	 *
	 * @since 7.2.0
	 *
	 * @param string $method The calling method, for the notice.
	 * @param string $kind   The entity kind.
	 * @param string $name   The entity name.
	 * @param array  $ids    The ids of the fields.
	 * @return string[] The valid ids, in the order of the call.
	 */
	protected function skip_invalid_ids( $method, $kind, $name, $ids ) {
		$valid   = array();
		$invalid = array();
		foreach ( array_values( $ids ) as $position => $id ) {
			if ( is_string( $id ) && '' !== $id ) {
				$valid[] = $id;
			} else {
				$invalid[] = '#' . ( $position + 1 );
			}
		}

		if ( $invalid ) {
			_doing_it_wrong(
				$method,
				sprintf(
					/* translators: 1: Entity kind, e.g. postType. 2: Entity name, e.g. page. 3: Comma-separated list of positions, e.g. #2, #4. */
					__( 'Every field id must be a non-empty string. These ids of %1$s "%2$s" are skipped: %3$s. The rest of the fields of the call are unregistered.' ),
					$kind,
					$name,
					implode( ', ', $invalid )
				),
				'7.2.0'
			);
		}

		return $valid;
	}

	/**
	 * Reads and validates a field collection, see register_collection() for
	 * its format.
	 *
	 * An invalid configuration is reported with _doing_it_wrong(): the
	 * collection is skipped.
	 *
	 * @since 7.2.0
	 *
	 * @param string $method    The calling method, for the notice.
	 * @param string $directory The directory of the collection.
	 * @return array|null The configuration of the collection, with the `module`
	 *                    key set, its valid `fields` as a list, and `skipped`
	 *                    telling whether a `field.php` was skipped, see
	 *                    read_collection_fields(); or null when the
	 *                    configuration is invalid.
	 */
	protected function get_collection( $method, $directory ) {
		$slug   = basename( $directory );
		$file   = $directory . '/index.php';
		$config = is_file( $file ) ? self::require_file( $file ) : null;
		$error  = null;

		if ( ! is_array( $config ) ) {
			$error = sprintf(
				/* translators: %s: index.php */
				__( 'The %s of a field collection must return an array.' ),
				'<code>index.php</code>'
			);
		} elseif ( ! isset( $config['origin'] ) || ! is_string( $config['origin'] ) || '' === $config['origin'] ) {
			$error = sprintf(
				/* translators: %s: origin */
				__( 'The %s of a field collection must be a non-empty string.' ),
				'<code>origin</code>'
			);
		} elseif ( ! isset( $config['kind'] ) || ! is_string( $config['kind'] ) || '' === $config['kind'] ) {
			$error = sprintf(
				/* translators: %s: kind */
				__( 'The %s of a field collection must be a non-empty string.' ),
				'<code>kind</code>'
			);
		} elseif ( ! isset( $config['name'] ) || ! is_string( $config['name'] ) || '' === $config['name'] ) {
			$error = sprintf(
				/* translators: %s: name */
				__( 'The %s of a field collection must be a non-empty string.' ),
				'<code>name</code>'
			);
		} elseif ( isset( $config['module'] ) && ( ! is_string( $config['module'] ) || '' === $config['module'] ) ) {
			$error = sprintf(
				/* translators: %s: module */
				__( 'The %s of a field collection must be the id of a script module.' ),
				'<code>module</code>'
			);
		}

		if ( null !== $error ) {
			_doing_it_wrong(
				$method,
				/* translators: 1: The name of a field collection. 2: The error. */
				sprintf( __( 'The field collection "%1$s" is skipped. %2$s' ), $slug, $error ),
				'7.2.0'
			);
			return null;
		}

		$read = $this->read_collection_fields( $method, $directory );

		return array_merge(
			$config,
			array(
				'module'  => $config['module'] ?? null,
				'fields'  => array_values( $read['fields'] ),
				'skipped' => $read['skipped'],
			)
		);
	}

	/**
	 * Reads the fields of a field collection, see get_collection_fields().
	 *
	 * The id of a field is used as a key, so it is checked before: a
	 * `field.php` that does not return an array with a non-empty string id
	 * is reported with _doing_it_wrong() and skipped, and so is one whose
	 * id an earlier folder of the collection took. The rest of the fields
	 * are read.
	 *
	 * @since 7.2.0
	 *
	 * @param string $method    The calling method, for the notice.
	 * @param string $directory The directory of the collection.
	 * @return array {
	 *     The fields of the collection.
	 *
	 *     @type array<string, array> $fields  The valid fields, keyed by id,
	 *                                         in the alphabetical order of
	 *                                         their folders.
	 *     @type bool                 $skipped Whether a `field.php` was skipped.
	 * }
	 */
	protected function read_collection_fields( $method, $directory ) {
		$files = glob( $directory . '/*/field.php' );
		if ( empty( $files ) ) {
			return array(
				'fields'  => array(),
				'skipped' => false,
			);
		}
		sort( $files, SORT_STRING );

		$fields     = array();
		$invalid    = array();
		$duplicated = array();
		foreach ( $files as $file ) {
			$folder = basename( dirname( $file ) );
			$field  = self::require_file( $file );
			if ( ! is_array( $field ) ) {
				$invalid[] = $folder;
				continue;
			}

			// The id comes first, like in the fields registered directly.
			$field = array_merge( array( 'id' => $folder ), $field );
			$id    = $field['id'];
			if ( ! is_string( $id ) || '' === $id ) {
				$invalid[] = $folder;
			} elseif ( isset( $fields[ $id ] ) ) {
				$duplicated[] = $folder;
			} else {
				$fields[ $id ] = $field;
			}
		}

		$collection = basename( $directory );
		if ( $invalid ) {
			_doing_it_wrong(
				$method,
				sprintf(
					/* translators: 1: The name of a field collection. 2: Comma-separated list of folder names. 3: field.php 4: id */
					__( 'The %3$s of a field must return an array, with a non-empty string %4$s if it sets one. These fields of the field collection "%1$s" are skipped: %2$s.' ),
					$collection,
					implode( ', ', $invalid ),
					'<code>field.php</code>',
					'<code>id</code>'
				),
				'7.2.0'
			);
		}

		if ( $duplicated ) {
			_doing_it_wrong(
				$method,
				sprintf(
					/* translators: 1: The name of a field collection. 2: Comma-separated list of folder names. */
					__( 'A field can only be defined once in a field collection. These fields of the field collection "%1$s" are skipped because an earlier folder defines their id: %2$s.' ),
					$collection,
					implode( ', ', $duplicated )
				),
				'7.2.0'
			);
		}

		return array(
			'fields'  => $fields,
			'skipped' => $invalid || $duplicated,
		);
	}

	/**
	 * Requires a file without sharing the variables of the caller: this is
	 * about variable scope.
	 *
	 * @since 7.2.0
	 *
	 * @param string $file The path of the file.
	 * @return mixed The return value of the file.
	 */
	protected static function require_file( $file ) {
		return require $file;
	}

	/**
	 * Applies a script module to fields of an entity, after the fields it
	 * already applies to.
	 *
	 * @since 7.2.0
	 *
	 * @param string      $kind          The entity kind.
	 * @param string      $name          The entity name.
	 * @param string[]    $ids           The ids of the fields.
	 * @param string|null $script_module The id of the script module, if any.
	 */
	protected function add_field_module( $kind, $name, $ids, $script_module ) {
		if ( null === $script_module ) {
			return;
		}

		$module_ids = $this->field_modules[ $kind ][ $name ][ $script_module ] ?? array();
		foreach ( $ids as $id ) {
			if ( ! in_array( $id, $module_ids, true ) ) {
				$module_ids[] = $id;
			}
		}
		$this->field_modules[ $kind ][ $name ][ $script_module ] = $module_ids;
	}

	/**
	 * Forgets an entity left without fields or script modules, and its kind
	 * once no entity of the kind is left, so the registry only lists
	 * entities that have some.
	 *
	 * @since 7.2.0
	 *
	 * @param string $kind The entity kind.
	 * @param string $name The entity name.
	 */
	protected function forget_empty_entity( $kind, $name ) {
		if ( empty( $this->fields[ $kind ][ $name ] ) ) {
			unset( $this->fields[ $kind ][ $name ] );
		}
		if ( empty( $this->fields[ $kind ] ) ) {
			unset( $this->fields[ $kind ] );
		}
		if ( empty( $this->field_modules[ $kind ][ $name ] ) ) {
			unset( $this->field_modules[ $kind ][ $name ] );
		}
		if ( empty( $this->field_modules[ $kind ] ) ) {
			unset( $this->field_modules[ $kind ] );
		}
	}
}
