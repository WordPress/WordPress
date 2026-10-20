<?php
/**
 * Entity Actions API: WP_Entity_Actions_Registry class
 *
 * @package WordPress
 * @subpackage Entity Actions
 * @since 7.2.0
 */

/**
 * Holds the actions registered for entities.
 *
 * The registry maps an entity, identified by its kind and name, to its
 * registered action definitions keyed by id, in registration order, each
 * with the origin that registered it, and to the script modules registered
 * for it, each with the ids of the actions it applies to. register() adds
 * actions and validates the definitions before storing them: an action
 * that cannot be stored is reported and skipped, and the rest of the call
 * is stored. To change a registered action, unregister it and register it
 * again.
 *
 * Actions are registered on the `wp_entity_actions_api_init` action, and only
 * there, with wp_register_entity_actions() and wp_unregister_entity_actions():
 * register() and unregister() refuse to run while the action is not
 * firing.
 *
 * The registry is filled lazily: the first time its actions are read, it
 * fires the `wp_entity_actions_api_init` action, on which the actions of every
 * entity are registered. A read before `init` has completed is refused,
 * see initialize().
 *
 * Once the action has fired the registry does not change: every reader of
 * a request, the REST controller as well as the import map of the editor
 * script, sees the same actions.
 *
 * @since 7.2.0
 *
 * @access private
 */
class WP_Entity_Actions_Registry {

	/**
	 * The values DataViews supports for the properties of an action that
	 * take one of a set of values, see packages/dataviews/src/types/dataviews.ts.
	 *
	 * @since 7.2.0
	 *
	 * @var array<string, string[]>
	 */
	const SUPPORTED_VALUES = array(
		'context'   => array( 'list' ),
		'modalSize' => array( 'small', 'medium', 'large', 'fill' ),
	);

	/**
	 * Registered actions, as kind => name => array of action id => definition.
	 *
	 * Kept nested rather than under a joined `{$kind}/{$name}` key: both parts
	 * may contain any character, so no separator keeps two entities apart.
	 *
	 * @since 7.2.0
	 *
	 * @var array<string, array<string, array<string, array>>>
	 */
	protected $actions = array();

	/**
	 * Registered script modules, as kind => name => array of module id =>
	 * list of the ids of the actions the module applies to, in registration
	 * order.
	 *
	 * @since 7.2.0
	 *
	 * @var array<string, array<string, array<string, string[]>>>
	 */
	protected $action_modules = array();

	/**
	 * Whether the `wp_entity_actions_api_init` action has fired since the registry
	 * was created.
	 *
	 * @since 7.2.0
	 *
	 * @var bool
	 */
	protected $initialized = false;

	/**
	 * Whether the registry is firing the `wp_entity_actions_api_init` action, the
	 * only time register() and unregister() run.
	 *
	 * @since 7.2.0
	 *
	 * @var bool
	 */
	protected $initializing = false;

	/**
	 * The singleton instance.
	 *
	 * @since 7.2.0
	 *
	 * @var WP_Entity_Actions_Registry|null
	 */
	protected static $instance = null;

	/**
	 * Returns the singleton instance.
	 *
	 * @since 7.2.0
	 *
	 * @return WP_Entity_Actions_Registry The registry.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Registers actions for an entity.
	 *
	 * The actions must be new: an action with the id of a registered action
	 * is reported and skipped, and so is every definition after the first of
	 * an id that appears more than once in the call. The rest of the actions
	 * of the call are registered: an action another plugin got to first does
	 * not cost a plugin the others. A definition that is not an array with a
	 * non-empty string `id` is reported and skipped the same way. To replace
	 * a registered action, unregister it first. The script module, if any,
	 * applies to every action the call registers.
	 *
	 * A value DataViews does not support is reported, and a property only a
	 * script module can provide is reported and removed, see
	 * report_invalid_values().
	 *
	 * The origin is stored as the `origin` property of each action, as
	 * `registeredBy`, replacing any `origin` the definition sets.
	 *
	 * @since 7.2.0
	 *
	 * @param string      $origin        Who registers the actions: `core`, or
	 *                                   the slug of the plugin or theme.
	 * @param string      $kind          The entity kind (e.g. `postType`).
	 * @param string      $name          The entity name (e.g. `page`).
	 * @param array[]     $actions       The list of action definitions, each with an `id`.
	 * @param string|null $script_module The id of the script module providing
	 *                                   the JavaScript parts of the actions, if any.
	 * @return string[] The ids of the actions registered, in the order of the
	 *                  call. Empty when none is, when called outside the
	 *                  `wp_entity_actions_api_init` action, or when an argument is
	 *                  invalid.
	 */
	public function register( $origin, $kind, $name, $actions, $script_module = null ) {
		if ( ! $this->validate_arguments( __METHOD__, $origin, $kind, $name, $actions, $script_module ) ) {
			return array();
		}
		$actions = $this->skip_invalid_definitions( __METHOD__, $kind, $name, $actions );
		if ( ! $actions ) {
			return array();
		}

		$new_actions    = array();
		$duplicated     = array();
		$registered_ids = array();
		foreach ( $actions as $action ) {
			$id = $action['id'];
			if ( isset( $this->actions[ $kind ][ $name ][ $id ] ) ) {
				$registered_ids[ $id ] = $id;
			} elseif ( isset( $new_actions[ $id ] ) ) {
				$duplicated[ $id ] = $id;
			} else {
				$new_actions[ $id ] = $action;
			}
		}

		if ( $duplicated ) {
			_doing_it_wrong(
				__METHOD__,
				sprintf(
					/* translators: 1: Entity kind, e.g. postType. 2: Entity name, e.g. page. 3: Comma-separated list of action ids. */
					__( 'An action can only be registered once. These actions of %1$s "%2$s" appear more than once in the same call: %3$s. Only their first definition is registered.' ),
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
					/* translators: 1: Entity kind, e.g. postType. 2: Entity name, e.g. page. 3: Comma-separated list of action ids. */
					__( 'An action can only be registered once. These actions of %1$s "%2$s" are skipped because they are already registered: %3$s. Unregister an action first to replace it.' ),
					$kind,
					$name,
					implode( ', ', $registered_ids )
				),
				'7.2.0'
			);
		}

		if ( ! $new_actions ) {
			return array();
		}
		$new_actions = $this->report_invalid_values( __METHOD__, $kind, $name, $new_actions );

		foreach ( $new_actions as $action ) {
			$this->actions[ $kind ][ $name ][ $action['id'] ] = array_merge(
				$action,
				array(
					'origin' => array(
						'registeredBy' => $origin,
					),
				)
			);
		}
		$ids = array_column( $new_actions, 'id' );
		$this->add_action_module( $kind, $name, $ids, $script_module );

		return $ids;
	}

	/**
	 * Reports the values of action definitions that DataViews does not
	 * support, and the properties only a script module can provide, with
	 * _doing_it_wrong().
	 *
	 * A value DataViews does not support is otherwise silent: an action
	 * whose `context` is not `list` shows on no screen. Such a value is
	 * kept, like the field types the fields registry reports.
	 * `callback`, `RenderModal`, and `isEligible` are functions and
	 * components, which only a script module can provide: a value set on
	 * the server would be called on the client and fail, so it is removed.
	 *
	 * @since 7.2.0
	 *
	 * @param string  $method  The calling method, for the notice.
	 * @param string  $kind    The entity kind.
	 * @param string  $name    The entity name.
	 * @param array[] $actions The action definitions, keyed by id.
	 * @return array[] The definitions, without the properties only a script
	 *                 module can provide.
	 */
	protected function report_invalid_values( $method, $kind, $name, $actions ) {
		$invalid = array();
		foreach ( $actions as $id => $action ) {
			foreach ( self::SUPPORTED_VALUES as $property => $values ) {
				if ( array_key_exists( $property, $action ) && ! in_array( $action[ $property ], $values, true ) ) {
					$invalid[] = sprintf(
						'%1$s (%2$s)',
						$id,
						wp_sprintf(
							/* translators: 1: Property name, e.g. context. 2: List of the values DataViews supports. */
							__( '%1$s is not one of %2$l.' ),
							$property,
							$values
						)
					);
				}
			}
			foreach ( array( 'callback', 'RenderModal', 'isEligible' ) as $property ) {
				if ( array_key_exists( $property, $action ) ) {
					unset( $actions[ $id ][ $property ] );
					$invalid[] = sprintf(
						/* translators: 1: Action id. 2: Property name, e.g. callback. */
						__( '%1$s (%2$s can only come from a script module, so it is removed.)' ),
						$id,
						$property
					);
				}
			}
		}
		if ( $invalid ) {
			_doing_it_wrong(
				$method,
				sprintf(
					/* translators: 1: Entity kind, e.g. postType. 2: Entity name, e.g. page. 3: Comma-separated list of action ids, each with the error of a value. */
					__( 'These actions of %1$s "%2$s" have values DataViews does not support: %3$s' ),
					$kind,
					$name,
					implode( ', ', $invalid )
				),
				'7.2.0'
			);
		}

		return $actions;
	}

	/**
	 * Unregisters actions of an entity.
	 *
	 * Unregistering an action removes its registered definition and drops
	 * it from the script modules that applied to it (a module left with no
	 * action is forgotten). Unregistering every action forgets the entity:
	 * its registered actions and script modules.
	 *
	 * Like register(), it only runs on the `wp_entity_actions_api_init` action, and
	 * an invalid entity is reported and unregisters nothing. An id that is
	 * not a non-empty string is reported and skipped, like a definition
	 * register() cannot store.
	 *
	 * @since 7.2.0
	 *
	 * @param string        $kind The entity kind (e.g. `postType`).
	 * @param string        $name The entity name (e.g. `page`).
	 * @param string[]|null $ids  The ids of the actions to unregister.
	 *                            Default null, every action of the entity.
	 * @return array[] The list of the definitions unregistered, in
	 *                 registration order, as get_registered() returns them.
	 *                 Empty when none of the actions is registered, when
	 *                 called outside the `wp_entity_actions_api_init` action, or
	 *                 when the entity is invalid.
	 */
	public function unregister( $kind, $name, $ids = null ) {
		if ( ! $this->doing_entity_actions_api_init( __METHOD__ ) || ! $this->validate_entity( __METHOD__, $kind, $name ) ) {
			return array();
		}

		if ( null === $ids ) {
			$unregistered = array_values( $this->actions[ $kind ][ $name ] ?? array() );
			unset( $this->actions[ $kind ][ $name ], $this->action_modules[ $kind ][ $name ] );
			$this->forget_empty_entity( $kind, $name );
			return $unregistered;
		}

		$ids = $this->skip_invalid_ids( __METHOD__, $kind, $name, (array) $ids );
		if ( ! $ids ) {
			return array();
		}

		$unregistered = array_intersect_key( $this->actions[ $kind ][ $name ] ?? array(), array_flip( $ids ) );
		if ( empty( $unregistered ) ) {
			return array();
		}

		// A script module only ever applies to registered actions: register()
		// adds it for the actions it stores, and actions leave both lists
		// together here. So the actions removed are all the registry loses,
		// and the modules are not returned: the ones that applied to them are
		// listed by get_registered_action_modules(), read beforehand.
		$ids = array_keys( $unregistered );
		foreach ( $ids as $id ) {
			unset( $this->actions[ $kind ][ $name ][ $id ] );
		}
		foreach ( $this->action_modules[ $kind ][ $name ] ?? array() as $module => $module_ids ) {
			$remaining = array_values( array_diff( $module_ids, $ids ) );
			if ( empty( $remaining ) ) {
				unset( $this->action_modules[ $kind ][ $name ][ $module ] );
			} else {
				$this->action_modules[ $kind ][ $name ][ $module ] = $remaining;
			}
		}
		$this->forget_empty_entity( $kind, $name );

		return array_values( $unregistered );
	}

	/**
	 * Returns the actions registered for an entity.
	 *
	 * @since 7.2.0
	 *
	 * @param string $kind The entity kind (e.g. `postType`).
	 * @param string $name The entity name (e.g. `page`).
	 * @return array[] The list of action definitions, in registration order.
	 *                 Empty when the entity is invalid.
	 */
	public function get_registered( $kind, $name ) {
		if ( ! $this->validate_entity( __METHOD__, $kind, $name ) ) {
			return array();
		}
		$this->initialize();
		return array_values( $this->actions[ $kind ][ $name ] ?? array() );
	}

	/**
	 * Returns the actions registered for every entity.
	 *
	 * @since 7.2.0
	 *
	 * @return array<string, array<string, array[]>> The lists of action
	 *                                               definitions, keyed by
	 *                                               kind, then by name.
	 */
	public function get_all_registered() {
		$this->initialize();
		return array_map(
			static function ( $entities ) {
				return array_map( 'array_values', $entities );
			},
			$this->actions
		);
	}

	/**
	 * Returns the script modules registered for an entity.
	 *
	 * @since 7.2.0
	 *
	 * @param string $kind The entity kind (e.g. `postType`).
	 * @param string $name The entity name (e.g. `page`).
	 * @return array<string, string[]> The ids of the actions each module
	 *                                 applies to, keyed by module id, in
	 *                                 registration order. Empty when the
	 *                                 entity is invalid.
	 */
	public function get_registered_action_modules( $kind, $name ) {
		if ( ! $this->validate_entity( __METHOD__, $kind, $name ) ) {
			return array();
		}
		$this->initialize();
		return $this->action_modules[ $kind ][ $name ] ?? array();
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
	public function get_all_registered_action_modules() {
		$this->initialize();
		return array_map(
			static function ( $entities ) {
				return array_map(
					static function ( $modules ) {
						// A numeric module id is an integer as an array key.
						return array_map( 'strval', array_keys( $modules ) );
					},
					$entities
				);
			},
			$this->action_modules
		);
	}

	/**
	 * Fires the `wp_entity_actions_api_init` action the first time the registry
	 * is read.
	 *
	 * The read is refused until `init` has completed. The registry holds the
	 * actions of post types, and post types are not known until then: core
	 * registers its own on `init` at priority 0, plugins theirs at the
	 * default priority, and supports are added and removed on `init` too.
	 * The actions of a post type can derive from its supports, and the
	 * action fires once, so a read before or during `init` would fix them
	 * from the post types registered so far for the rest of the request.
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
				__( 'The registered actions cannot be read until the `init` action has completed: the post types and supports they derive from are registered on `init`. Read them once `init` has completed, or hook `wp_entity_actions_api_init`.' ),
				'7.2.0'
			);
			return;
		}

		// Set before firing, so a callback reading the registry to inspect
		// the actions it unregisters does not fire the action again.
		$this->initialized = true;

		$this->initializing = true;
		try {
			/**
			 * Fires the first time the registered actions are read, after `init`.
			 *
			 * Register or remove actions here, with wp_register_entity_actions() and
			 * wp_unregister_entity_actions(): it is the only time they run.
			 *
			 * The first read happens wherever the actions are needed: while
			 * handling a REST request as well as on `admin_footer` of the pages
			 * that load the editor script. A callback should register actions
			 * and nothing else; script modules, styles, and their enqueue hooks
			 * belong on `init`.
			 *
			 * @since 7.2.0
			 */
			do_action( 'wp_entity_actions_api_init' );
		} finally {
			$this->initializing = false;
		}
	}

	/**
	 * Checks that the registry is firing the `wp_entity_actions_api_init` action,
	 * as register() and unregister() require.
	 *
	 * Outside the action a registration would either come before the
	 * registrations made on it, and take the ids they register, or after
	 * the actions have been read and the import map of the editor script
	 * built from them. Refusing keeps the registry immutable once read,
	 * even when the action is fired again with do_action(), which is why
	 * the registry tracks its own firing rather than check doing_action().
	 *
	 * @since 7.2.0
	 *
	 * @param string $method The calling method, for the notice.
	 * @return bool Whether the action is firing.
	 */
	protected function doing_entity_actions_api_init( $method ) {
		if ( $this->initializing ) {
			return true;
		}

		_doing_it_wrong(
			$method,
			__( 'Actions can only be registered and unregistered on the `wp_entity_actions_api_init` action.' ),
			'7.2.0'
		);
		return false;
	}

	/**
	 * Validates the arguments of register().
	 *
	 * @since 7.2.0
	 *
	 * @param string $method        The calling method, for the notice.
	 * @param mixed  $origin        The origin.
	 * @param mixed  $kind          The entity kind.
	 * @param mixed  $name          The entity name.
	 * @param mixed  $actions       The action definitions.
	 * @param mixed  $script_module The script module id, if any.
	 * @return bool Whether the call may proceed: the `wp_entity_actions_api_init`
	 *              action is firing and the arguments are valid.
	 */
	protected function validate_arguments( $method, $origin, $kind, $name, $actions, $script_module ) {
		if ( ! $this->doing_entity_actions_api_init( $method ) ) {
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

		if ( ! is_array( $actions ) || empty( $actions ) || ! array_is_list( $actions ) ) {
			_doing_it_wrong(
				$method,
				__( 'The actions must be a list of non-empty action definitions.' ),
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
	 * like a duplicated action, an invalid definition does not cost the call
	 * its other actions.
	 *
	 * @since 7.2.0
	 *
	 * @param string  $method  The calling method, for the notice.
	 * @param string  $kind    The entity kind.
	 * @param string  $name    The entity name.
	 * @param array[] $actions The list of action definitions.
	 * @return array[] The valid definitions, in the order of the call.
	 */
	protected function skip_invalid_definitions( $method, $kind, $name, $actions ) {
		$valid   = array();
		$invalid = array();
		foreach ( $actions as $position => $action ) {
			if ( is_array( $action ) && isset( $action['id'] ) && is_string( $action['id'] ) && '' !== $action['id'] ) {
				$valid[] = $action;
			} else {
				$invalid[] = '#' . ( $position + 1 );
			}
		}

		if ( $invalid ) {
			$message = sprintf(
				/* translators: 1: Entity kind, e.g. postType. 2: Entity name, e.g. page. 3: Comma-separated list of positions, e.g. #2, #4. */
				__( 'Every action definition must be an array with a non-empty string `id`. These definitions of %1$s "%2$s" are skipped: %3$s.' ),
				$kind,
				$name,
				implode( ', ', $invalid )
			);
			if ( $valid ) {
				$message .= ' ' . __( 'The rest of the actions of the call are stored.' );
			}
			_doing_it_wrong( $method, $message, '7.2.0' );
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
	 * @param array  $ids    The ids of the actions.
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
			$message = sprintf(
				/* translators: 1: Entity kind, e.g. postType. 2: Entity name, e.g. page. 3: Comma-separated list of positions, e.g. #2, #4. */
				__( 'Every action id must be a non-empty string. These ids of %1$s "%2$s" are skipped: %3$s.' ),
				$kind,
				$name,
				implode( ', ', $invalid )
			);
			if ( $valid ) {
				$message .= ' ' . __( 'The rest of the actions of the call are unregistered.' );
			}
			_doing_it_wrong( $method, $message, '7.2.0' );
		}

		return $valid;
	}

	/**
	 * Applies a script module to actions of an entity, after the actions it
	 * already applies to.
	 *
	 * @since 7.2.0
	 *
	 * @param string      $kind          The entity kind.
	 * @param string      $name          The entity name.
	 * @param string[]    $ids           The ids of the actions.
	 * @param string|null $script_module The id of the script module, if any.
	 */
	protected function add_action_module( $kind, $name, $ids, $script_module ) {
		if ( null === $script_module ) {
			return;
		}

		$module_ids = $this->action_modules[ $kind ][ $name ][ $script_module ] ?? array();
		foreach ( $ids as $id ) {
			if ( ! in_array( $id, $module_ids, true ) ) {
				$module_ids[] = $id;
			}
		}
		$this->action_modules[ $kind ][ $name ][ $script_module ] = $module_ids;
	}

	/**
	 * Forgets an entity left without actions or script modules, and its kind
	 * once no entity of the kind is left, so the registry only lists
	 * entities that have some.
	 *
	 * @since 7.2.0
	 *
	 * @param string $kind The entity kind.
	 * @param string $name The entity name.
	 */
	protected function forget_empty_entity( $kind, $name ) {
		if ( empty( $this->actions[ $kind ][ $name ] ) ) {
			unset( $this->actions[ $kind ][ $name ] );
		}
		if ( empty( $this->actions[ $kind ] ) ) {
			unset( $this->actions[ $kind ] );
		}
		if ( empty( $this->action_modules[ $kind ][ $name ] ) ) {
			unset( $this->action_modules[ $kind ][ $name ] );
		}
		if ( empty( $this->action_modules[ $kind ] ) ) {
			unset( $this->action_modules[ $kind ] );
		}
	}
}
