<?php
/**
 * Customize API: WP_Customize_Nav_Menu_Setting class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 4.4.0
 */

/**
 * Customize Setting to represent a nav_menu.
 *
 * Subclass of WP_Customize_Setting to represent a nav_menu taxonomy term, and
 * the IDs for the nav_menu_items associated with the nav menu.
 *
 * @since 4.3.0
 *
 * @see wp_get_nav_menu_object()
 * @see WP_Customize_Setting
 */
class WP_Customize_Nav_Menu_Setting extends WP_Customize_Setting {

	const ID_PATTERN = '/^nav_menu\[(?P<id>-?\d+)\]$/';

	const TAXONOMY = 'nav_menu';

	const TYPE = 'nav_menu';

	/**
	 * Setting type.
	 *
	 * @since 4.3.0
	 * @var string
	 */
	public $type = self::TYPE;

	/**
	 * Default setting value.
	 *
	 * @since 4.3.0
	 * @var array
	 *
	 * @see wp_get_nav_menu_object()
	 */
	public $default = array(
		'name'        => '',
		'description' => '',
		'parent'      => 0,
		'auto_add'    => false,
	);

	/**
	 * Default transport.
	 *
	 * @since 4.3.0
	 * @var string
	 */
	public $transport = 'postMessage';

	/**
	 * The term ID represented by this setting instance.
	 *
	 * A negative value represents a placeholder ID for a new menu not yet saved.
	 *
	 * @since 4.3.0
	 * @var int
	 */
	public $term_id;

	/**
	 * Previous (placeholder) term ID used before creating a new menu.
	 *
	 * This value will be exported to JS via the {@see 'customize_save_response'} filter
	 * so that JavaScript can update the settings to refer to the newly-assigned
	 * term ID. This value is always negative to indicate it does not refer to
	 * a real term.
	 *
	 * @since 4.3.0
	 * @var int
	 *
	 * @see WP_Customize_Nav_Menu_Setting::update()
	 * @see WP_Customize_Nav_Menu_Setting::amend_customize_save_response()
	 */
	public $previous_term_id;

	/**
	 * Whether or not update() was called.
	 *
	 * @since 4.3.0
	 * @var bool
	 */
	protected $is_updated = false;

	/**
	 * Status for calling the update method, used in customize_save_response filter.
	 *
	 * See {@see 'customize_save_response'}.
	 *
	 * When status is inserted, the placeholder term ID is stored in `$previous_term_id`.
	 * When status is error, the error is stored in `$update_error`.
	 *
	 * @since 4.3.0
	 * @var string updated|inserted|deleted|error
	 *
	 * @see WP_Customize_Nav_Menu_Setting::update()
	 * @see WP_Customize_Nav_Menu_Setting::amend_customize_save_response()
	 */
	public $update_status;

	/**
	 * Any error object returned by wp_update_nav_menu_object() when setting is updated.
	 *
	 * @since 4.3.0
	 * @var WP_Error
	 *
	 * @see WP_Customize_Nav_Menu_Setting::update()
	 * @see WP_Customize_Nav_Menu_Setting::amend_customize_save_response()
	 */
	public $update_error;

	/**
	 * Constructor.
	 *
	 * Any supplied $args override class property defaults.
	 *
	 * @since 4.3.0
	 *
	 * @param WP_Customize_Manager $manager Bootstrap Customizer instance.
	 * @param string               $id      An specific ID of the setting. Can be a
	 *                                      theme mod or option name.
	 * @param array                $args    Optional. Setting arguments.
	 *
	 * @throws Exception If $id is not valid for this setting type.
	 */
	public function __construct( WP_Customize_Manager $manager, $id, array $args = array() ) {
		if ( empty( $manager->nav_menus ) ) {
			throw new Exception( 'Expected WP_Customize_Manager::$nav_menus to be set.' );
		}

		if ( ! preg_match( self::ID_PATTERN, $id, $matches ) ) {
			throw new Exception( "Illegal widget setting ID: $id" );
		}

		$this->term_id = intval( $matches['id'] );

		parent::__construct( $manager, $id, $args );
	}

	/**
	 * Get the instance data for a given widget setting.
	 *
	 * @since 4.3.0
	 *
	 * @see wp_get_nav_menu_object()
	 *
	 * @return array Instance data.
	 */
	public function value() {
		if ( $this->is_previewed && $this->_previewed_blog_id === get_current_blog_id() ) {
			$undefined  = new stdClass(); // Symbol.
			$post_value = $this->post_value( $undefined );

			if ( $undefined === $post_value ) {
				$value = $this->_original_value;
			} else {
				$value = $post_value;
			}
		} else {
			$value = false;

			// Note that a term_id of less than one indicates a nav_menu not yet inserted.
			if ( $this->term_id > 0 ) {
				$term = wp_get_nav_menu_object( $this->term_id );

				if ( $term ) {
					$value = wp_array_slice_assoc( (array) $term, array_keys( $this->default ) );

					$nav_menu_options  = (array) get_option( 'nav_menu_options', array() );
					$value['auto_add'] = false;

					if ( isset( $nav_menu_options['auto_add'] ) && is_array( $nav_menu_options['auto_add'] ) ) {
						$value['auto_add'] = in_array( $term->term_id, $nav_menu_options['auto_add'] );
					}
				}
			}

			if ( ! is_array( $value ) ) {
				$value = $this->default;
			}
		}
		return $value;
	}

	/**
	 * Handle previewing the setting.
	 *
	 * @since 4.3.0
	 * @since 4.4.0 Added boolean return value
	 *
	 * @see WP_Customize_Manager::post_value()
	 *
	 * @return bool False if method short-circuited due to no-op.
	 */
	public function preview() {
		if ( $this->is_previewed ) {
			return false;
		}

		$undefined      = new stdClass();
		$is_placeholder = ( $this->term_id < 0 );
		$is_dirty       = ( $undefined !== $this->post_value( $undefined ) );
		if ( ! $is_placeholder && ! $is_dirty ) {
			return false;
		}

		$this->is_previewed       = true;
		$this->_original_value    = $this->value();
		$this->_previewed_blog_id = get_current_blog_id();

		add_filter( 'wp_get_nav_menus', array( $this, 'filter_wp_get_nav_menus' ), 10, 2 );
		add_filter( 'wp_get_nav_menu_object', array( $this, 'filter_wp_get_nav_menu_object' ), 10, 2 );
		add_filter( 'default_option_nav_menu_options', array( $this, 'filter_nav_menu_options' ) );
		add_filter( 'option_nav_menu_options', array( $this, 'filter_nav_menu_options' ) );

		return true;
	}

	/**
	 * Filters the wp_get_nav_menus() result to ensure the inserted menu object is included, and the deleted one is removed.
	 *
	 * @since 4.3.0
	 *
	 * @see wp_get_nav_menus()
	 *
	 * @param WP_Term[] $menus An array of menu objects.
	 * @param array     $args  An array of arguments used to retrieve menu objects.
	 * @return WP_Term[] Array of menu objects.
	 */
	public function filter_wp_get_nav_menus( $menus, $args ) {
		if ( get_current_blog_id() !== $this->_previewed_blog_id ) {
			return $menus;
		}

		$setting_value = $this->value();
		$is_delete     = ( false === $setting_value );
		$index         = -1;

		// Find the existing menu item's position in the list.
		foreach ( $menus as $i => $menu ) {
			if ( (int) $this->term_id === (int) $menu->term_id || (int) $this->previous_term_id === (int) $menu->term_id ) {
				$index = $i;
				break;
			}
		}

		if ( $is_delete ) {
			// Handle deleted menu by removing it from the list.
			if ( -1 !== $index ) {
				array_splice( $menus, $index, 1 );
			}
		} else {
			// Handle menus being updated or inserted.
			$menu_obj = (object) array_merge(
				array(
					'term_id'          => $this->term_id,
					'term_taxonomy_id' => $this->term_id,
					'slug'             => sanitize_title( $setting_value['name'] ),
					'count'            => 0,
					'term_group'       => 0,
					'taxonomy'         => self::TAXONOMY,
					'filter'           => 'raw',
				),
				$setting_value
			);

			array_splice( $menus, $index, ( -1 === $index ? 0 : 1 ), array( $menu_obj ) );
		}

		// Make sure the menu objects get re-sorted after an update/insert.
		if ( ! $is_delete && ! empty( $args['orderby'] ) ) {
			$menus = wp_list_sort(
				$menus,
				array(
					$args['orderby'] => 'ASC',
				)
			);
		}
		// @todo add support for $args['hide_empty'] === true

		return $menus;
	}

	/**
	 * Temporary non-closure passing of orderby value to function.
	 *
	 * @since 4.3.0
	 * @var string
	 *
	 * @see WP_Customize_Nav_Menu_Setting::filter_wp_get_nav_menus()
	 * @see WP_Customize_Nav_Menu_Setting::_sort_menus_by_orderby()
	 */
	protected $_current_menus_sort_orderby;

	/**
	 * Sort menu objects by the class-supplied orderby property.
	 *
	 * This is a workaround for a lack of closures.
	 *
	 * @since 4.3.0
	 * @deprecated 4.7.0 Use wp_list_sort()
	 *
	 * @param object $menu1
	 * @param object $menu2
	 * @return int
	 *
	 * @see WP_Customize_Nav_Menu_Setting::filter_wp_get_nav_menus()
	 */
	protected function _sort_menus_by_orderby( $menu1, $menu2 ) {
		_deprecated_function( __METHOD__, '4.7.0', 'wp_list_sort' );

		$key = $this->_current_menus_sort_orderby;
		return strcmp( $menu1->$key, $menu2->$key );
	}

	/**
	 * Filters the wp_get_nav_menu_object() result to supply the previewed menu object.
	 *
	 * Requesting a nav_menu object by anything but ID is not supported.
	 *
	 * @since 4.3.0
	 *
	 * @see wp_get_nav_menu_object()
	 *
	 * @param object|null $menu_obj Object returned by wp_get_nav_menu_object().
	 * @param string      $menu_id  ID of the nav_menu term. Requests by slug or name will be ignored.
	 * @return object|null
	 */
	public function filter_wp_get_nav_menu_object( $menu_obj, $menu_id ) {
		$ok = (
			get_current_blog_id() === $this->_previewed_blog_id
			&&
			is_int( $menu_id )
			&&
			$menu_id === $this->term_id
		);
		if ( ! $ok ) {
			return $menu_obj;
		}

		$setting_value = $this->value();

		// Handle deleted menus.
		if ( false === $setting_value ) {
			return false;
		}

		// Handle sanitization failure by preventing short-circuiting.
		if ( null === $setting_value ) {
			return $menu_obj;
		}

		$menu_obj = (object) array_merge(
			array(
				'term_id'          => $this->term_id,
				'term_taxonomy_id' => $this->term_id,
				'slug'             => sanitize_title( $setting_value['name'] ),
				'count'            => 0,
				'term_group'       => 0,
				'taxonomy'         => self::TAXONOMY,
				'filter'           => 'raw',
			),
			$setting_value
		);

		return $menu_obj;
	}

	/**
	 * Filters the nav_menu_options option to include this menu's auto_add preference.
	 *
	 * @since 4.3.0
	 *
	 * @param array $nav_menu_options Nav menu options including auto_add.
	 * @return array (Kaybe) modified nav menu options.
	 */
	public function filter_nav_menu_options( $nav_menu_options ) {
		if ( $this->_previewed_blog_id !== get_current_blog_id() ) {
			return $nav_menu_options;
		}

		$menu             = $this->value();
		$nav_menu_options = $this->filter_nav_menu_options_value(
			$nav_menu_options,
			$this->term_id,
			false === $menu ? false : $menu['auto_add']
		);

		return $nav_menu_options;
	}

	/**
	 * Sanitize an input.
	 *
	 * Note that parent::sanitize() erroneously does wp_unslash() on $value, but
	 * we remove that in this override.
	 *
	 * @since 4.3.0
	 *
	 * @param array $value The value to sanitize.
	 * @return array|false|null Null if an input isn't valid. False if it is marked for deletion.
	 *                          Otherwise the sanitized value.
	 */
	public function sanitize( $value ) {
		// Menu is marked for deletion.
		if ( false === $value ) {
			return $value;
		}

		// Invalid.
		if ( ! is_array( $value ) ) {
			return null;
		}

		$default = array(
			'name'        => '',
			'description' => '',
			'parent'      => 0,
			'auto_add'    => false,
		);
		$value   = array_merge( $default, $value );
		$value   = wp_array_slice_assoc( $value, array_keys( $default ) );

		$value['name']        = trim( esc_html( $value['name'] ) ); // This sanitization code is used in wp-admin/nav-menus.php.
		$value['description'] = sanitize_text_field( $value['description'] );
		$value['parent']      = max( 0, intval( $value['parent'] ) );
		$value['auto_add']    = ! empty( $value['auto_add'] );

		if ( '' === $value['name'] ) {
			$value['name'] = _x( '(unnamed)', 'Missing menu name.' );
		}

		/** This filter is documented in wp-includes/class-wp-customize-setting.php */
		return apply_filters( "customize_sanitize_{$this->id}", $value, $this );
	}

	/**
	 * Storage for data to be sent back to client in customize_save_response filter.
	 *
	 * See {@see 'customize_save_response'}.
	 *
	 * @since 4.3.0
	 * @var array
	 *
	 * @see WP_Customize_Nav_Menu_Setting::amend_customize_save_response()
	 */
	protected $_widget_nav_menu_updates = array();

	/**
	 * Create/update the nav_menu term for this setting.
	 *
	 * Any created menus will have their assigned term IDs exported to the client
	 * via the {@see 'customize_save_response'} filter. Likewise, any errors will be exported
	 * to the client via the customize_save_response() filter.
	 *
	 * To delete a menu, the client can send false as the value.
	 *
	 * @since 4.3.0
	 *
	 * @see wp_update_nav_menu_object()
	 *
	 * @param array|false $value {
	 *     The value to update. Note that slug cannot be updated via wp_update_nav_menu_object().
	 *     If false, then the menu will be deleted entirely.
	 *
	 *     @type string $name        The name of the menu to save.
	 *     @type string $description The term description. Default empty string.
	 *     @type int    $parent      The id of the parent term. Default 0.
	 *     @type bool   $auto_add    Whether pages will auto_add to this menu. Default false.
	 * }
	 * @return null|void
	 */
	protected function update( $value ) {
		if ( $this->is_updated ) {
			return;
		}

		$this->is_updated = true;
		$is_placeholder   = ( $this->term_id < 0 );
		$is_delete        = ( false === $value );

		add_filter( 'customize_save_response', array( $this, 'amend_customize_save_response' ) );

		$auto_add = null;
		if ( $is_delete ) {
			// If the current setting term is a placeholder, a delete request is a no-op.
			if ( $is_placeholder ) {
				$this->update_status = 'deleted';
			} else {
				$r = wp_delete_nav_menu( $this->term_id );

				if ( is_wp_error( $r ) ) {
					$this->update_status = 'error';
					$this->update_error  = $r;
				} else {
					$this->update_status = 'deleted';
					$auto_add            = false;
				}
			}
		} else {
			// Insert or update menu.
			$menu_data              = wp_array_slice_assoc( $value, array( 'description', 'parent' ) );
			$menu_data['menu-name'] = $value['name'];

			$menu_id              = $is_placeholder ? 0 : $this->term_id;
			$r                    = wp_update_nav_menu_object( $menu_id, wp_slash( $menu_data ) );
			$original_name        = $menu_data['menu-name'];
			$name_conflict_suffix = 1;
			while ( is_wp_error( $r ) && 'menu_exists' === $r->get_error_code() ) {
				$name_conflict_suffix += 1;
				/* translators: 1: Original menu name, 2: Duplicate count. */
				$menu_data['menu-name'] = sprintf( __( '%1$s (%2$d)' ), $original_name, $name_conflict_suffix );
				$r                      = wp_update_nav_menu_object( $menu_id, wp_slash( $menu_data ) );
			}

			if ( is_wp_error( $r ) ) {
				$this->update_status = 'error';
				$this->update_error  = $r;
			} else {
				if ( $is_placeholder ) {
					$this->previous_term_id = $this->term_id;
					$this->term_id          = $r;
					$this->update_status    = 'inserted';
				} else {
					$this->update_status = 'updated';
				}

				$auto_add = $value['auto_add'];
			}
		}

		if ( null !== $auto_add ) {
			$nav_menu_options = $this->filter_nav_menu_options_value(
				(array) get_option( 'nav_menu_options', array() ),
				$this->term_id,
				$auto_add
			);
			update_option( 'nav_menu_options', $nav_menu_options );
		}

		if ( 'inserted' === $this->update_status ) {
			// Make sure that new menus assigned to nav menu locations use their new IDs.
			foreach ( $this->manager->settings() as $setting ) {
				if ( ! preg_match( '/^nav_menu_locations\[/', $setting->id ) ) {
					continue;
				}

				$post_value = $setting->post_value( null );
				if ( ! is_null( $post_value ) && $this->previous_term_id === intval( $post_value ) ) {
					$this->manager->set_post_value( $setting->id, $this->term_id );
					$setting->save();
				}
			}

			// Make sure that any nav_menu widgets referencing the placeholder nav menu get updated and sent back to client.
			foreach ( array_keys( $this->manager->unsanitized_post_values() ) as $setting_id ) {
				$nav_menu_widget_setting = $this->manager->get_setting( $setting_id );
				if ( ! $nav_menu_widget_setting || ! preg_match( '/^widget_nav_menu\[/', $nav_menu_widget_setting->id ) ) {
					continue;
				}

				$widget_instance = $nav_menu_widget_setting->post_value(); // Note that this calls WP_Customize_Widgets::sanitize_widget_instance().
				if ( empty( $widget_instance['nav_menu'] ) || intval( $widget_instance['nav_menu'] ) !== $this->previous_term_id ) {
					continue;
				}

				$widget_instance['nav_menu'] = $this->term_id;
				$updated_widget_instance     = $this->manager->widgets->sanitize_widget_js_instance( $widget_instance );
				$this->manager->set_post_value( $nav_menu_widget_setting->id, $updated_widget_instance );
				$nav_menu_widget_setting->save();

				$this->_widget_nav_menu_updates[ $nav_menu_widget_setting->id ] = $updated_widget_instance;
			}
		}
	}

	/**
	 * Updates a nav_menu_options array.
	 *
	 * @since 4.3.0
	 *
	 * @see WP_Customize_Nav_Menu_Setting::filter_nav_menu_options()
	 * @see WP_Customize_Nav_Menu_Setting::update()
	 *
	 * @param array $nav_menu_options Array as returned by get_option( 'nav_menu_options' ).
	 * @param int   $menu_id          The term ID for the given menu.
	 * @param bool  $auto_add         Whether to auto-add or not.
	 * @return array (Maybe) modified nav_menu_otions array.
	 */
	protected function filter_nav_menu_options_value( $nav_menu_options, $menu_id, $auto_add ) {
		$nav_menu_options = (array) $nav_menu_options;
		if ( ! isset( $nav_menu_options['auto_add'] ) ) {
			$nav_menu_options['auto_add'] = array();
		}

		$i = array_search( $menu_id, $nav_menu_options['auto_add'] );
		if ( $auto_add && false === $i ) {
			array_push( $nav_menu_options['auto_add'], $this->term_id );
		} elseif ( ! $auto_add && false !== $i ) {
			array_splice( $nav_menu_options['auto_add'], $i, 1 );
		}

		return $nav_menu_options;
	}

	/**
	 * Export data for the JS client.
	 *
	 * @since 4.3.0
	 *
	 * @see WP_Customize_Nav_Menu_Setting::update()
	 *
	 * @param array $data Additional information passed back to the 'saved' event on `wp.customize`.
	 * @return array Export data.
	 */
	public function amend_customize_save_response( $data ) {
		if ( ! isset( $data['nav_menu_updates'] ) ) {
			$data['nav_menu_updates'] = array();
		}
		if ( ! isset( $data['widget_nav_menu_updates'] ) ) {
			$data['widget_nav_menu_updates'] = array();
		}

		$data['nav_menu_updates'][] = array(
			'term_id'          => $this->term_id,
			'previous_term_id' => $this->previous_term_id,
			'error'            => $this->update_error ? $this->update_error->get_error_code() : null,
			'status'           => $this->update_status,
			'saved_value'      => 'deleted' === $this->update_status ? null : $this->value(),
		);

		$data['widget_nav_menu_updates'] = array_merge(
			$data['widget_nav_menu_updates'],
			$this->_widget_nav_menu_updates
		);
		$this->_widget_nav_menu_updates  = array();

		return $data;
	}
}
