<?php
/**
 * WordPress Customize Setting classes
 *
 * @package WordPress
 * @subpackage Customize
 * @since 3.4.0
 */

/**
 * Customize Setting class.
 *
 * Handles saving and sanitizing of settings.
 *
 * @since 3.4.0
 *
 * @see WP_Customize_Manager
 */
class WP_Customize_Setting {
	/**
	 * @access public
	 * @var WP_Customize_Manager
	 */
	public $manager;

	/**
	 * @access public
	 * @var string
	 */
	public $id;

	/**
	 * @access public
	 * @var string
	 */
	public $type = 'theme_mod';

	/**
	 * Capability required to edit this setting.
	 *
	 * @var string
	 */
	public $capability = 'edit_theme_options';

	/**
	 * Feature a theme is required to support to enable this setting.
	 *
	 * @access public
	 * @var string
	 */
	public $theme_supports  = '';
	public $default         = '';
	public $transport       = 'refresh';

	/**
	 * Server-side sanitization callback for the setting's value.
	 *
	 * @var callback
	 */
	public $sanitize_callback    = '';
	public $sanitize_js_callback = '';

	/**
	 * Whether or not the setting is initially dirty when created.
	 *
	 * This is used to ensure that a setting will be sent from the pane to the
	 * preview when loading the Customizer. Normally a setting only is synced to
	 * the preview if it has been changed. This allows the setting to be sent
	 * from the start.
	 *
	 * @since 4.2.0
	 * @access public
	 * @var bool
	 */
	public $dirty = false;

	protected $id_data = array();

	/**
	 * Constructor.
	 *
	 * Any supplied $args override class property defaults.
	 *
	 * @since 3.4.0
	 *
	 * @param WP_Customize_Manager $manager
	 * @param string               $id      An specific ID of the setting. Can be a
	 *                                      theme mod or option name.
	 * @param array                $args    Setting arguments.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		$keys = array_keys( get_object_vars( $this ) );
		foreach ( $keys as $key ) {
			if ( isset( $args[ $key ] ) )
				$this->$key = $args[ $key ];
		}

		$this->manager = $manager;
		$this->id = $id;

		// Parse the ID for array keys.
		$this->id_data[ 'keys' ] = preg_split( '/\[/', str_replace( ']', '', $this->id ) );
		$this->id_data[ 'base' ] = array_shift( $this->id_data[ 'keys' ] );

		// Rebuild the ID.
		$this->id = $this->id_data[ 'base' ];
		if ( ! empty( $this->id_data[ 'keys' ] ) )
			$this->id .= '[' . implode( '][', $this->id_data[ 'keys' ] ) . ']';

		if ( $this->sanitize_callback )
			add_filter( "customize_sanitize_{$this->id}", $this->sanitize_callback, 10, 2 );

		if ( $this->sanitize_js_callback )
			add_filter( "customize_sanitize_js_{$this->id}", $this->sanitize_js_callback, 10, 2 );
	}

	/**
	 * The ID for the current blog when the preview() method was called.
	 *
	 * @since 4.2.0
	 * @access protected
	 * @var int
	 */
	protected $_previewed_blog_id;

	/**
	 * Return true if the current blog is not the same as the previewed blog.
	 *
	 * @since 4.2.0
	 * @access public
	 *
	 * @return bool If preview() has been called.
	 */
	public function is_current_blog_previewed() {
		if ( ! isset( $this->_previewed_blog_id ) ) {
			return false;
		}
		return ( get_current_blog_id() === $this->_previewed_blog_id );
	}

	/**
	 * Original non-previewed value stored by the preview method.
	 *
	 * @see WP_Customize_Setting::preview()
	 * @since 4.1.1
	 * @var mixed
	 */
	protected $_original_value;

	/**
	 * Handle previewing the setting.
	 *
	 * @since 3.4.0
	 */
	public function preview() {
		if ( ! isset( $this->_original_value ) ) {
			$this->_original_value = $this->value();
		}
		if ( ! isset( $this->_previewed_blog_id ) ) {
			$this->_previewed_blog_id = get_current_blog_id();
		}

		switch( $this->type ) {
			case 'theme_mod' :
				add_filter( 'theme_mod_' . $this->id_data[ 'base' ], array( $this, '_preview_filter' ) );
				break;
			case 'option' :
				if ( empty( $this->id_data[ 'keys' ] ) )
					add_filter( 'pre_option_' . $this->id_data[ 'base' ], array( $this, '_preview_filter' ) );
				else {
					add_filter( 'option_' . $this->id_data[ 'base' ], array( $this, '_preview_filter' ) );
					add_filter( 'default_option_' . $this->id_data[ 'base' ], array( $this, '_preview_filter' ) );
				}
				break;
			default :

				/**
				 * Fires when the {@see WP_Customize_Setting::preview()} method is called for settings
				 * not handled as theme_mods or options.
				 *
				 * The dynamic portion of the hook name, `$this->id`, refers to the setting ID.
				 *
				 * @since 3.4.0
				 *
				 * @param WP_Customize_Setting $this {@see WP_Customize_Setting} instance.
				 */
				do_action( "customize_preview_{$this->id}", $this );

				/**
				 * Fires when the {@see WP_Customize_Setting::preview()} method is called for settings
				 * not handled as theme_mods or options.
				 *
				 * The dynamic portion of the hook name, `$this->type`, refers to the setting type.
				 *
				 * @since 4.1.0
				 *
				 * @param WP_Customize_Setting $this {@see WP_Customize_Setting} instance.
				 */
				do_action( "customize_preview_{$this->type}", $this );
		}
	}

	/**
	 * Callback function to filter the theme mods and options.
	 *
	 * If switch_to_blog() was called after the preview() method, and the current
	 * blog is now not the same blog, then this method does a no-op and returns
	 * the original value.
	 *
	 * @since 3.4.0
	 * @uses WP_Customize_Setting::multidimensional_replace()
	 *
	 * @param mixed $original Old value.
	 * @return mixed New or old value.
	 */
	public function _preview_filter( $original ) {
		if ( ! $this->is_current_blog_previewed() ) {
			return $original;
		}

		$undefined = new stdClass(); // symbol hack
		$post_value = $this->post_value( $undefined );
		if ( $undefined === $post_value ) {
			$value = $this->_original_value;
		} else {
			$value = $post_value;
		}

		return $this->multidimensional_replace( $original, $this->id_data['keys'], $value );
	}

	/**
	 * Check user capabilities and theme supports, and then save
	 * the value of the setting.
	 *
	 * @since 3.4.0
	 *
	 * @return false|void False if cap check fails or value isn't set.
	 */
	final public function save() {
		$value = $this->post_value();

		if ( ! $this->check_capabilities() || ! isset( $value ) )
			return false;

		/**
		 * Fires when the WP_Customize_Setting::save() method is called.
		 *
		 * The dynamic portion of the hook name, `$this->id_data['base']` refers to
		 * the base slug of the setting name.
		 *
		 * @since 3.4.0
		 *
		 * @param WP_Customize_Setting $this {@see WP_Customize_Setting} instance.
		 */
		do_action( 'customize_save_' . $this->id_data[ 'base' ], $this );

		$this->update( $value );
	}

	/**
	 * Fetch and sanitize the $_POST value for the setting.
	 *
	 * @since 3.4.0
	 *
	 * @param mixed $default A default value which is used as a fallback. Default is null.
	 * @return mixed The default value on failure, otherwise the sanitized value.
	 */
	final public function post_value( $default = null ) {
		return $this->manager->post_value( $this, $default );
	}

	/**
	 * Sanitize an input.
	 *
	 * @since 3.4.0
	 *
	 * @param string|array $value The value to sanitize.
	 * @return string|array|null Null if an input isn't valid, otherwise the sanitized value.
	 */
	public function sanitize( $value ) {
		$value = wp_unslash( $value );

		/**
		 * Filter a Customize setting value in un-slashed form.
		 *
		 * @since 3.4.0
		 *
		 * @param mixed                $value Value of the setting.
		 * @param WP_Customize_Setting $this  WP_Customize_Setting instance.
		 */
		return apply_filters( "customize_sanitize_{$this->id}", $value, $this );
	}

	/**
	 * Save the value of the setting, using the related API.
	 *
	 * @since 3.4.0
	 *
	 * @param mixed $value The value to update.
	 * @return mixed The result of saving the value.
	 */
	protected function update( $value ) {
		switch( $this->type ) {
			case 'theme_mod' :
				return $this->_update_theme_mod( $value );

			case 'option' :
				return $this->_update_option( $value );

			default :

				/**
				 * Fires when the {@see WP_Customize_Setting::update()} method is called for settings
				 * not handled as theme_mods or options.
				 *
				 * The dynamic portion of the hook name, `$this->type`, refers to the type of setting.
				 *
				 * @since 3.4.0
				 *
				 * @param mixed                $value Value of the setting.
				 * @param WP_Customize_Setting $this  WP_Customize_Setting instance.
				 */
				return do_action( 'customize_update_' . $this->type, $value, $this );
		}
	}

	/**
	 * Update the theme mod from the value of the parameter.
	 *
	 * @since 3.4.0
	 *
	 * @param mixed $value The value to update.
	 */
	protected function _update_theme_mod( $value ) {
		// Handle non-array theme mod.
		if ( empty( $this->id_data[ 'keys' ] ) ) {
			set_theme_mod( $this->id_data[ 'base' ], $value );
			return;
		}
		// Handle array-based theme mod.
		$mods = get_theme_mod( $this->id_data[ 'base' ] );
		$mods = $this->multidimensional_replace( $mods, $this->id_data[ 'keys' ], $value );
		if ( isset( $mods ) ) {
			set_theme_mod( $this->id_data[ 'base' ], $mods );
		}
	}

	/**
	 * Update the option from the value of the setting.
	 *
	 * @since 3.4.0
	 *
	 * @param mixed $value The value to update.
	 * @return bool The result of saving the value.
	 */
	protected function _update_option( $value ) {
		// Handle non-array option.
		if ( empty( $this->id_data[ 'keys' ] ) )
			return update_option( $this->id_data[ 'base' ], $value );

		// Handle array-based options.
		$options = get_option( $this->id_data[ 'base' ] );
		$options = $this->multidimensional_replace( $options, $this->id_data[ 'keys' ], $value );
		if ( isset( $options ) )
			return update_option( $this->id_data[ 'base' ], $options );
	}

	/**
	 * Fetch the value of the setting.
	 *
	 * @since 3.4.0
	 *
	 * @return mixed The value.
	 */
	public function value() {
		// Get the callback that corresponds to the setting type.
		switch( $this->type ) {
			case 'theme_mod' :
				$function = 'get_theme_mod';
				break;
			case 'option' :
				$function = 'get_option';
				break;
			default :

				/**
				 * Filter a Customize setting value not handled as a theme_mod or option.
				 *
				 * The dynamic portion of the hook name, `$this->id_date['base']`, refers to
				 * the base slug of the setting name.
				 *
				 * For settings handled as theme_mods or options, see those corresponding
				 * functions for available hooks.
				 *
				 * @since 3.4.0
				 *
				 * @param mixed $default The setting default value. Default empty.
				 */
				return apply_filters( 'customize_value_' . $this->id_data[ 'base' ], $this->default );
		}

		// Handle non-array value
		if ( empty( $this->id_data[ 'keys' ] ) )
			return $function( $this->id_data[ 'base' ], $this->default );

		// Handle array-based value
		$values = $function( $this->id_data[ 'base' ] );
		return $this->multidimensional_get( $values, $this->id_data[ 'keys' ], $this->default );
	}

	/**
	 * Sanitize the setting's value for use in JavaScript.
	 *
	 * @since 3.4.0
	 *
	 * @return mixed The requested escaped value.
	 */
	public function js_value() {

		/**
		 * Filter a Customize setting value for use in JavaScript.
		 *
		 * The dynamic portion of the hook name, `$this->id`, refers to the setting ID.
		 *
		 * @since 3.4.0
		 *
		 * @param mixed                $value The setting value.
		 * @param WP_Customize_Setting $this  {@see WP_Customize_Setting} instance.
		 */
		$value = apply_filters( "customize_sanitize_js_{$this->id}", $this->value(), $this );

		if ( is_string( $value ) )
			return html_entity_decode( $value, ENT_QUOTES, 'UTF-8');

		return $value;
	}

	/**
	 * Validate user capabilities whether the theme supports the setting.
	 *
	 * @since 3.4.0
	 *
	 * @return bool False if theme doesn't support the setting or user can't change setting, otherwise true.
	 */
	final public function check_capabilities() {
		if ( $this->capability && ! call_user_func_array( 'current_user_can', (array) $this->capability ) )
			return false;

		if ( $this->theme_supports && ! call_user_func_array( 'current_theme_supports', (array) $this->theme_supports ) )
			return false;

		return true;
	}

	/**
	 * Multidimensional helper function.
	 *
	 * @since 3.4.0
	 *
	 * @param $root
	 * @param $keys
	 * @param bool $create Default is false.
	 * @return array|void Keys are 'root', 'node', and 'key'.
	 */
	final protected function multidimensional( &$root, $keys, $create = false ) {
		if ( $create && empty( $root ) )
			$root = array();

		if ( ! isset( $root ) || empty( $keys ) )
			return;

		$last = array_pop( $keys );
		$node = &$root;

		foreach ( $keys as $key ) {
			if ( $create && ! isset( $node[ $key ] ) )
				$node[ $key ] = array();

			if ( ! is_array( $node ) || ! isset( $node[ $key ] ) )
				return;

			$node = &$node[ $key ];
		}

		if ( $create ) {
			if ( ! is_array( $node ) ) {
				// account for an array overriding a string or object value
				$node = array();
			}
			if ( ! isset( $node[ $last ] ) ) {
				$node[ $last ] = array();
			}
		}

		if ( ! isset( $node[ $last ] ) )
			return;

		return array(
			'root' => &$root,
			'node' => &$node,
			'key'  => $last,
		);
	}

	/**
	 * Will attempt to replace a specific value in a multidimensional array.
	 *
	 * @since 3.4.0
	 *
	 * @param $root
	 * @param $keys
	 * @param mixed $value The value to update.
	 * @return
	 */
	final protected function multidimensional_replace( $root, $keys, $value ) {
		if ( ! isset( $value ) )
			return $root;
		elseif ( empty( $keys ) ) // If there are no keys, we're replacing the root.
			return $value;

		$result = $this->multidimensional( $root, $keys, true );

		if ( isset( $result ) )
			$result['node'][ $result['key'] ] = $value;

		return $root;
	}

	/**
	 * Will attempt to fetch a specific value from a multidimensional array.
	 *
	 * @since 3.4.0
	 *
	 * @param $root
	 * @param $keys
	 * @param mixed $default A default value which is used as a fallback. Default is null.
	 * @return mixed The requested value or the default value.
	 */
	final protected function multidimensional_get( $root, $keys, $default = null ) {
		if ( empty( $keys ) ) // If there are no keys, test the root.
			return isset( $root ) ? $root : $default;

		$result = $this->multidimensional( $root, $keys );
		return isset( $result ) ? $result['node'][ $result['key'] ] : $default;
	}

	/**
	 * Will attempt to check if a specific value in a multidimensional array is set.
	 *
	 * @since 3.4.0
	 *
	 * @param $root
	 * @param $keys
	 * @return bool True if value is set, false if not.
	 */
	final protected function multidimensional_isset( $root, $keys ) {
		$result = $this->multidimensional_get( $root, $keys );
		return isset( $result );
	}
}

/**
 * A setting that is used to filter a value, but will not save the results.
 *
 * Results should be properly handled using another setting or callback.
 *
 * @since 3.4.0
 *
 * @see WP_Customize_Setting
 */
class WP_Customize_Filter_Setting extends WP_Customize_Setting {

	/**
	 * @since 3.4.0
	 */
	public function update( $value ) {}
}

/**
 * A setting that is used to filter a value, but will not save the results.
 *
 * Results should be properly handled using another setting or callback.
 *
 * @since 3.4.0
 *
 * @see WP_Customize_Setting
 */
final class WP_Customize_Header_Image_Setting extends WP_Customize_Setting {
	public $id = 'header_image_data';

	/**
	 * @since 3.4.0
	 *
	 * @global Custom_Image_Header $custom_image_header
	 *
	 * @param $value
	 */
	public function update( $value ) {
		global $custom_image_header;

		// If the value doesn't exist (removed or random),
		// use the header_image value.
		if ( ! $value )
			$value = $this->manager->get_setting('header_image')->post_value();

		if ( is_array( $value ) && isset( $value['choice'] ) )
			$custom_image_header->set_header_image( $value['choice'] );
		else
			$custom_image_header->set_header_image( $value );
	}
}

/**
 * Customizer Background Image Setting class.
 *
 * @since 3.4.0
 *
 * @see WP_Customize_Setting
 */
final class WP_Customize_Background_Image_Setting extends WP_Customize_Setting {
	public $id = 'background_image_thumb';

	/**
	 * @since 3.4.0
	 *
	 * @param $value
	 */
	public function update( $value ) {
		remove_theme_mod( 'background_image_thumb' );
	}
}

/**
 * Customize Setting to represent a nav_menu.
 *
 * Subclass of WP_Customize_Setting to represent a nav_menu taxonomy term, and
 * the IDs for the nav_menu_items associated with the nav menu.
 *
 * @since 4.3.0
 *
 * @see wp_get_nav_menu_items()
 * @see WP_Customize_Setting
 */
class WP_Customize_Nav_Menu_Item_Setting extends WP_Customize_Setting {

	const ID_PATTERN = '/^nav_menu_item\[(?P<id>-?\d+)\]$/';

	const POST_TYPE = 'nav_menu_item';

	const TYPE = 'nav_menu_item';

	/**
	 * Setting type.
	 *
	 * @since 4.3.0
	 *
	 * @var string
	 */
	public $type = self::TYPE;

	/**
	 * Default setting value.
	 *
	 * @since 4.3.0
	 *
	 * @see wp_setup_nav_menu_item()
	 * @var array
	 */
	public $default = array(
		// The $menu_item_data for wp_update_nav_menu_item().
		'object_id'        => 0,
		'object'           => '', // Taxonomy name.
		'menu_item_parent' => 0, // A.K.A. menu-item-parent-id; note that post_parent is different, and not included.
		'position'         => 0, // A.K.A. menu_order.
		'type'             => 'custom', // Note that type_label is not included here.
		'title'            => '',
		'url'              => '',
		'target'           => '',
		'attr_title'       => '',
		'description'      => '',
		'classes'          => '',
		'xfn'              => '',
		'status'           => 'publish',
		'original_title'   => '',
		'nav_menu_term_id' => 0, // This will be supplied as the $menu_id arg for wp_update_nav_menu_item().
		// @todo also expose invalid?
	);

	/**
	 * Default transport.
	 *
	 * @since 4.3.0
	 *
	 * @var string
	 */
	public $transport = 'postMessage';

	/**
	 * The post ID represented by this setting instance. This is the db_id.
	 *
	 * A negative value represents a placeholder ID for a new menu not yet saved.
	 *
	 * @todo Should this be $db_id, and also use this for WP_Customize_Nav_Menu_Setting::$term_id
	 *
	 * @since 4.3.0
	 *
	 * @var int
	 */
	public $post_id;

	/**
	 * Previous (placeholder) post ID used before creating a new menu item.
	 *
	 * This value will be exported to JS via the customize_save_response filter
	 * so that JavaScript can update the settings to refer to the newly-assigned
	 * post ID. This value is always negative to indicate it does not refer to
	 * a real post.
	 *
	 * @since 4.3.0
	 *
	 * @see WP_Customize_Nav_Menu_Item_Setting::update()
	 * @see WP_Customize_Nav_Menu_Item_Setting::amend_customize_save_response()
	 *
	 * @var int
	 */
	public $previous_post_id;

	/**
	 * When previewing or updating a menu item, this stores the previous nav_menu_term_id
	 * which ensures that we can apply the proper filters.
	 *
	 * @since 4.3.0
	 *
	 * @var int
	 */
	public $original_nav_menu_term_id;

	/**
	 * Whether or not preview() was called.
	 *
	 * @since 4.3.0
	 *
	 * @var bool
	 */
	protected $is_previewed = false;

	/**
	 * Whether or not update() was called.
	 *
	 * @since 4.3.0
	 *
	 * @var bool
	 */
	protected $is_updated = false;

	/**
	 * Status for calling the update method, used in customize_save_response filter.
	 *
	 * When status is inserted, the placeholder post ID is stored in $previous_post_id.
	 * When status is error, the error is stored in $update_error.
	 *
	 * @since 4.3.0
	 *
	 * @see WP_Customize_Nav_Menu_Item_Setting::update()
	 * @see WP_Customize_Nav_Menu_Item_Setting::amend_customize_save_response()
	 *
	 * @var string updated|inserted|deleted|error
	 */
	public $update_status;

	/**
	 * Any error object returned by wp_update_nav_menu_item() when setting is updated.
	 *
	 * @since 4.3.0
	 *
	 * @see WP_Customize_Nav_Menu_Item_Setting::update()
	 * @see WP_Customize_Nav_Menu_Item_Setting::amend_customize_save_response()
	 *
	 * @var WP_Error
	 */
	public $update_error;

	/**
	 * Constructor.
	 *
	 * Any supplied $args override class property defaults.
	 *
	 * @since 4.3.0
	 *
	 * @param WP_Customize_Manager $manager Manager instance.
	 * @param string               $id      An specific ID of the setting. Can be a
	 *                                      theme mod or option name.
	 * @param array                $args    Optional. Setting arguments.
	 * @throws Exception If $id is not valid for this setting type.
	 */
	public function __construct( WP_Customize_Manager $manager, $id, array $args = array() ) {
		if ( empty( $manager->nav_menus ) ) {
			throw new Exception( 'Expected WP_Customize_Manager::$nav_menus to be set.' );
		}

		if ( ! preg_match( self::ID_PATTERN, $id, $matches ) ) {
			throw new Exception( "Illegal widget setting ID: $id" );
		}

		$this->post_id = intval( $matches['id'] );

		$menu = $this->value();
		$this->original_nav_menu_term_id = $menu['nav_menu_term_id'];

		parent::__construct( $manager, $id, $args );
	}

	/**
	 * Get the instance data for a given widget setting.
	 *
	 * @since 4.3.0
	 *
	 * @see wp_setup_nav_menu_item()
	 *
	 * @return array
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

			// Note that a ID of less than one indicates a nav_menu not yet inserted.
			if ( $this->post_id > 0 ) {
				$post = get_post( $this->post_id );
				if ( $post && self::POST_TYPE === $post->post_type ) {
					$item  = wp_setup_nav_menu_item( $post );
					$value = wp_array_slice_assoc(
						(array) $item,
						array_keys( $this->default )
					);
					$value['position']       = $item->menu_order;
					$value['status']         = $item->post_status;
					$value['original_title'] = '';

					$menus = wp_get_post_terms( $post->ID, WP_Customize_Nav_Menu_Setting::TAXONOMY, array(
						'fields' => 'ids',
					) );

					if ( ! empty( $menus ) ) {
						$value['nav_menu_term_id'] = array_shift( $menus );
					} else {
						$value['nav_menu_term_id'] = 0;
					}

					if ( 'post_type' === $value['type'] ) {
						$original_title = get_the_title( $value['object_id'] );
					} else if ( 'taxonomy' === $value['type'] ) {
						$original_title = get_term_field( 'name', $value['object_id'], $value['object'], 'raw' );
						if ( is_wp_error( $original_title ) ) {
							$original_title = '';
						}
					}

					if ( ! empty( $original_title ) ) {
						$value['original_title'] = $original_title;
					}
				}
			}

			if ( ! is_array( $value ) ) {
				$value = $this->default;
			}
		}

		if ( is_array( $value ) ) {
			foreach ( array( 'object_id', 'menu_item_parent', 'nav_menu_term_id' ) as $key ) {
				$value[ $key ] = intval( $value[ $key ] );
			}
		}

		return $value;
	}

	/**
	 * Handle previewing the setting.
	 *
	 * @since 4.3.0
	 *
	 * @see WP_Customize_Manager::post_value()
	 */
	public function preview() {
		if ( $this->is_previewed ) {
			return;
		}

		$this->is_previewed              = true;
		$this->_original_value           = $this->value();
		$this->original_nav_menu_term_id = $this->_original_value['nav_menu_term_id'];
		$this->_previewed_blog_id        = get_current_blog_id();

		add_filter( 'wp_get_nav_menu_items', array( $this, 'filter_wp_get_nav_menu_items' ), 10, 3 );

		$sort_callback = array( __CLASS__, 'sort_wp_get_nav_menu_items' );
		if ( ! has_filter( 'wp_get_nav_menu_items', $sort_callback ) ) {
			add_filter( 'wp_get_nav_menu_items', array( __CLASS__, 'sort_wp_get_nav_menu_items' ), 1000, 3 );
		}

		// @todo Add get_post_metadata filters for plugins to add their data.
	}

	/**
	 * Filter the wp_get_nav_menu_items() result to supply the previewed menu items.
	 *
	 * @since 4.3.0
	 *
	 * @see wp_get_nav_menu_items()
	 *
	 * @param array  $items An array of menu item post objects.
	 * @param object $menu  The menu object.
	 * @param array  $args  An array of arguments used to retrieve menu item objects.
	 * @return array Array of menu items,
	 */
	function filter_wp_get_nav_menu_items( $items, $menu, $args ) {
		$this_item = $this->value();
		$current_nav_menu_term_id = $this_item['nav_menu_term_id'];
		unset( $this_item['nav_menu_term_id'] );

		$should_filter = (
			$menu->term_id === $this->original_nav_menu_term_id
			||
			$menu->term_id === $current_nav_menu_term_id
		);
		if ( ! $should_filter ) {
			return $items;
		}

		// Handle deleted menu item, or menu item moved to another menu.
		$should_remove = (
			false === $this_item
			||
			(
				$this->original_nav_menu_term_id === $menu->term_id
				&&
				$current_nav_menu_term_id !== $this->original_nav_menu_term_id
			)
		);
		if ( $should_remove ) {
			$filtered_items = array();
			foreach ( $items as $item ) {
				if ( $item->db_id !== $this->post_id ) {
					$filtered_items[] = $item;
				}
			}
			return $filtered_items;
		}

		$mutated = false;
		$should_update = (
			is_array( $this_item )
			&&
			$current_nav_menu_term_id === $menu->term_id
		);
		if ( $should_update ) {
			foreach ( $items as $item ) {
				if ( $item->db_id === $this->post_id ) {
					foreach ( get_object_vars( $this->value_as_wp_post_nav_menu_item() ) as $key => $value ) {
						$item->$key = $value;
					}
					$mutated = true;
				}
			}

			// Not found so we have to append it..
			if ( ! $mutated ) {
				$items[] = $this->value_as_wp_post_nav_menu_item();
			}
		}

		return $items;
	}

	/**
	 * Re-apply the tail logic also applied on $items by wp_get_nav_menu_items().
	 *
	 * @since 4.3.0
	 *
	 * @see wp_get_nav_menu_items()
	 *
	 * @param array  $items An array of menu item post objects.
	 * @param object $menu  The menu object.
	 * @param array  $args  An array of arguments used to retrieve menu item objects.
	 * @return array Array of menu items,
	 */
	static function sort_wp_get_nav_menu_items( $items, $menu, $args ) {
		// @todo We should probably re-apply some constraints imposed by $args.
		unset( $args['include'] );

		// Remove invalid items only in frontend.
		if ( ! is_admin() ) {
			$items = array_filter( $items, '_is_valid_nav_menu_item' );
		}

		if ( ARRAY_A === $args['output'] ) {
			$GLOBALS['_menu_item_sort_prop'] = $args['output_key'];
			usort( $items, '_sort_nav_menu_items' );
			$i = 1;

			foreach ( $items as $k => $item ) {
				$items[ $k ]->$args['output_key'] = $i++;
			}
		}

		return $items;
	}

	/**
	 * Get the value emulated into a WP_Post and set up as a nav_menu_item.
	 *
	 * @since 4.3.0
	 *
	 * @return WP_Post With {@see wp_setup_nav_menu_item()} applied.
	 */
	public function value_as_wp_post_nav_menu_item() {
		$item = (object) $this->value();
		unset( $item->nav_menu_term_id );

		$item->post_status = $item->status;
		unset( $item->status );

		$item->post_type = 'nav_menu_item';
		$item->menu_order = $item->position;
		unset( $item->position );

		$item->post_author = get_current_user_id();

		if ( $item->title ) {
			$item->post_title = $item->title;
		}

		$item->ID = $this->post_id;
		$post = new WP_Post( (object) $item );
		$post = wp_setup_nav_menu_item( $post );

		return $post;
	}

	/**
	 * Sanitize an input.
	 *
	 * Note that parent::sanitize() erroneously does wp_unslash() on $value, but
	 * we remove that in this override.
	 *
	 * @since 4.3.0
	 *
	 * @param array $menu_item_value The value to sanitize.
	 * @return array|false|null Null if an input isn't valid. False if it is marked for deletion. Otherwise the sanitized value.
	 */
	public function sanitize( $menu_item_value ) {
		// Menu is marked for deletion.
		if ( false === $menu_item_value ) {
			return $menu_item_value;
		}

		// Invalid.
		if ( ! is_array( $menu_item_value ) ) {
			return null;
		}

		$default = array(
			'object_id'        => 0,
			'object'           => '',
			'menu_item_parent' => 0,
			'position'         => 0,
			'type'             => 'custom',
			'title'            => '',
			'url'              => '',
			'target'           => '',
			'attr_title'       => '',
			'description'      => '',
			'classes'          => '',
			'xfn'              => '',
			'status'           => 'publish',
			'original_title'   => '',
			'nav_menu_term_id' => 0,
		);
		$menu_item_value = array_merge( $default, $menu_item_value );
		$menu_item_value = wp_array_slice_assoc( $menu_item_value, array_keys( $default ) );
		$menu_item_value['position'] = max( 0, intval( $menu_item_value['position'] ) );

		foreach ( array( 'object_id', 'menu_item_parent', 'nav_menu_term_id' ) as $key ) {
			// Note we need to allow negative-integer IDs for previewed objects not inserted yet.
			$menu_item_value[ $key ] = intval( $menu_item_value[ $key ] );
		}

		foreach ( array( 'type', 'object', 'target' ) as $key ) {
			$menu_item_value[ $key ] = sanitize_key( $menu_item_value[ $key ] );
		}

		foreach ( array( 'xfn', 'classes' ) as $key ) {
			$value = $menu_item_value[ $key ];
			if ( ! is_array( $value ) ) {
				$value = explode( ' ', $value );
			}
			$menu_item_value[ $key ] = implode( ' ', array_map( 'sanitize_html_class', $value ) );
		}

		foreach ( array( 'title', 'attr_title', 'description', 'original_title' ) as $key ) {
			// @todo Should esc_attr() the attr_title as well?
			$menu_item_value[ $key ] = sanitize_text_field( $menu_item_value[ $key ] );
		}

		$menu_item_value['url'] = esc_url_raw( $menu_item_value['url'] );
		if ( ! get_post_status_object( $menu_item_value['status'] ) ) {
			$menu_item_value['status'] = 'publish';
		}

		/** This filter is documented in wp-includes/class-wp-customize-setting.php */
		return apply_filters( "customize_sanitize_{$this->id}", $menu_item_value, $this );
	}

	/**
	 * Create/update the nav_menu_item post for this setting.
	 *
	 * Any created menu items will have their assigned post IDs exported to the client
	 * via the customize_save_response filter. Likewise, any errors will be exported
	 * to the client via the customize_save_response() filter.
	 *
	 * To delete a menu, the client can send false as the value.
	 *
	 * @since 4.3.0
	 *
	 * @see wp_update_nav_menu_item()
	 *
	 * @param array|false $value The menu item array to update. If false, then the menu item will be deleted entirely.
	 *                           See {@see WP_Customize_Nav_Menu_Item_Setting::$default} for what the value should
	 *                           consist of.
	 * @return void
	 */
	protected function update( $value ) {
		if ( $this->is_updated ) {
			return;
		}

		$this->is_updated = true;
		$is_placeholder   = ( $this->post_id < 0 );
		$is_delete        = ( false === $value );

		add_filter( 'customize_save_response', array( $this, 'amend_customize_save_response' ) );

		if ( $is_delete ) {
			// If the current setting post is a placeholder, a delete request is a no-op.
			if ( $is_placeholder ) {
				$this->update_status = 'deleted';
			} else {
				$r = wp_delete_post( $this->post_id, true );

				if ( false === $r ) {
					$this->update_error  = new WP_Error( 'delete_failure' );
					$this->update_status = 'error';
				} else {
					$this->update_status = 'deleted';
				}
				// @todo send back the IDs for all associated nav menu items deleted, so these settings (and controls) can be removed from Customizer?
			}
		} else {

			// Handle saving menu items for menus that are being newly-created.
			if ( $value['nav_menu_term_id'] < 0 ) {
				$nav_menu_setting_id = sprintf( 'nav_menu[%s]', $value['nav_menu_term_id'] );
				$nav_menu_setting    = $this->manager->get_setting( $nav_menu_setting_id );

				if ( ! $nav_menu_setting || ! ( $nav_menu_setting instanceof WP_Customize_Nav_Menu_Setting ) ) {
					$this->update_status = 'error';
					$this->update_error  = new WP_Error( 'unexpected_nav_menu_setting' );
					return;
				}

				if ( false === $nav_menu_setting->save() ) {
					$this->update_status = 'error';
					$this->update_error  = new WP_Error( 'nav_menu_setting_failure' );
				}

				if ( $nav_menu_setting->previous_term_id !== intval( $value['nav_menu_term_id'] ) ) {
					$this->update_status = 'error';
					$this->update_error  = new WP_Error( 'unexpected_previous_term_id' );
					return;
				}

				$value['nav_menu_term_id'] = $nav_menu_setting->term_id;
			}

			// Handle saving a nav menu item that is a child of a nav menu item being newly-created.
			if ( $value['menu_item_parent'] < 0 ) {
				$parent_nav_menu_item_setting_id = sprintf( 'nav_menu_item[%s]', $value['menu_item_parent'] );
				$parent_nav_menu_item_setting    = $this->manager->get_setting( $parent_nav_menu_item_setting_id );

				if ( ! $parent_nav_menu_item_setting || ! ( $parent_nav_menu_item_setting instanceof WP_Customize_Nav_Menu_Item_Setting ) ) {
					$this->update_status = 'error';
					$this->update_error  = new WP_Error( 'unexpected_nav_menu_item_setting' );
					return;
				}

				if ( false === $parent_nav_menu_item_setting->save() ) {
					$this->update_status = 'error';
					$this->update_error  = new WP_Error( 'nav_menu_item_setting_failure' );
				}

				if ( $parent_nav_menu_item_setting->previous_post_id !== intval( $value['menu_item_parent'] ) ) {
					$this->update_status = 'error';
					$this->update_error  = new WP_Error( 'unexpected_previous_post_id' );
					return;
				}

				$value['menu_item_parent'] = $parent_nav_menu_item_setting->post_id;
			}

			// Insert or update menu.
			$menu_item_data = array(
				'menu-item-object-id'   => $value['object_id'],
				'menu-item-object'      => $value['object'],
				'menu-item-parent-id'   => $value['menu_item_parent'],
				'menu-item-position'    => $value['position'],
				'menu-item-type'        => $value['type'],
				'menu-item-title'       => $value['title'],
				'menu-item-url'         => $value['url'],
				'menu-item-description' => $value['description'],
				'menu-item-attr-title'  => $value['attr_title'],
				'menu-item-target'      => $value['target'],
				'menu-item-classes'     => $value['classes'],
				'menu-item-xfn'         => $value['xfn'],
				'menu-item-status'      => $value['status'],
			);

			$r = wp_update_nav_menu_item(
				$value['nav_menu_term_id'],
				$is_placeholder ? 0 : $this->post_id,
				$menu_item_data
			);

			if ( is_wp_error( $r ) ) {
				$this->update_status = 'error';
				$this->update_error = $r;
			} else {
				if ( $is_placeholder ) {
					$this->previous_post_id = $this->post_id;
					$this->post_id = $r;
					$this->update_status = 'inserted';
				} else {
					$this->update_status = 'updated';
				}
			}
		}

	}

	/**
	 * Export data for the JS client.
	 *
	 * @since 4.3.0
	 *
	 * @see WP_Customize_Nav_Menu_Item_Setting::update()
	 *
	 * @param array $data Additional information passed back to the 'saved' event on `wp.customize`.
	 * @return array
	 */
	function amend_customize_save_response( $data ) {
		if ( ! isset( $data['nav_menu_item_updates'] ) ) {
			$data['nav_menu_item_updates'] = array();
		}

		$data['nav_menu_item_updates'][] = array(
			'post_id'          => $this->post_id,
			'previous_post_id' => $this->previous_post_id,
			'error'            => $this->update_error ? $this->update_error->get_error_code() : null,
			'status'           => $this->update_status,
		);

		return $data;
	}
}

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
	 *
	 * @var string
	 */
	public $type = self::TYPE;

	/**
	 * Default setting value.
	 *
	 * @since 4.3.0
	 *
	 * @see wp_get_nav_menu_object()
	 *
	 * @var array
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
	 *
	 * @var string
	 */
	public $transport = 'postMessage';

	/**
	 * The term ID represented by this setting instance.
	 *
	 * A negative value represents a placeholder ID for a new menu not yet saved.
	 *
	 * @since 4.3.0
	 *
	 * @var int
	 */
	public $term_id;

	/**
	 * Previous (placeholder) term ID used before creating a new menu.
	 *
	 * This value will be exported to JS via the customize_save_response filter
	 * so that JavaScript can update the settings to refer to the newly-assigned
	 * term ID. This value is always negative to indicate it does not refer to
	 * a real term.
	 *
	 * @since 4.3.0
	 *
	 * @see WP_Customize_Nav_Menu_Setting::update()
	 * @see WP_Customize_Nav_Menu_Setting::amend_customize_save_response()
	 *
	 * @var int
	 */
	public $previous_term_id;

	/**
	 * Whether or not preview() was called.
	 *
	 * @since 4.3.0
	 *
	 * @var bool
	 */
	protected $is_previewed = false;

	/**
	 * Whether or not update() was called.
	 *
	 * @since 4.3.0
	 *
	 * @var bool
	 */
	protected $is_updated = false;

	/**
	 * Status for calling the update method, used in customize_save_response filter.
	 *
	 * When status is inserted, the placeholder term ID is stored in $previous_term_id.
	 * When status is error, the error is stored in $update_error.
	 *
	 * @since 4.3.0
	 *
	 * @see WP_Customize_Nav_Menu_Setting::update()
	 * @see WP_Customize_Nav_Menu_Setting::amend_customize_save_response()
	 *
	 * @var string updated|inserted|deleted|error
	 */
	public $update_status;

	/**
	 * Any error object returned by wp_update_nav_menu_object() when setting is updated.
	 *
	 * @since 4.3.0
	 *
	 * @see WP_Customize_Nav_Menu_Setting::update()
	 * @see WP_Customize_Nav_Menu_Setting::amend_customize_save_response()
	 *
	 * @var WP_Error
	 */
	public $update_error;

	/**
	 * Constructor.
	 *
	 * Any supplied $args override class property defaults.
	 *
	 * @since 4.3.0
	 *
	 * @param WP_Customize_Manager $manager Manager instance.
	 * @param string               $id      An specific ID of the setting. Can be a
	 *                                      theme mod or option name.
	 * @param array                $args    Optional. Setting arguments.
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
	 * @return array
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
	 *
	 * @see WP_Customize_Manager::post_value()
	 */
	public function preview() {
		if ( $this->is_previewed ) {
			return;
		}

		$this->is_previewed       = true;
		$this->_original_value    = $this->value();
		$this->_previewed_blog_id = get_current_blog_id();

		add_filter( 'wp_get_nav_menu_object', array( $this, 'filter_wp_get_nav_menu_object' ), 10, 2 );
		add_filter( 'default_option_nav_menu_options', array( $this, 'filter_nav_menu_options' ) );
		add_filter( 'option_nav_menu_options', array( $this, 'filter_nav_menu_options' ) );
	}

	/**
	 * Filter the wp_get_nav_menu_object() result to supply the previewed menu object.
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
	function filter_wp_get_nav_menu_object( $menu_obj, $menu_id ) {
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

		$menu_obj = (object) array_merge( array(
				'term_id'          => $this->term_id,
				'term_taxonomy_id' => $this->term_id,
				'slug'             => sanitize_title( $setting_value['name'] ),
				'count'            => 0,
				'term_group'       => 0,
				'taxonomy'         => self::TAXONOMY,
				'filter'           => 'raw',
			), $setting_value );

		return $menu_obj;
	}

	/**
	 * Filter the nav_menu_options option to include this menu's auto_add preference.
	 *
	 * @since 4.3.0
	 *
	 * @param array $nav_menu_options Nav menu options including auto_add.
	 * @return array
	 */
	function filter_nav_menu_options( $nav_menu_options ) {
		if ( $this->_previewed_blog_id !== get_current_blog_id() ) {
			return $nav_menu_options;
		}

		$menu = $this->value();
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
	 * @return array|false|null Null if an input isn't valid. False if it is marked for deletion. Otherwise the sanitized value.
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
		$value = array_merge( $default, $value );
		$value = wp_array_slice_assoc( $value, array_keys( $default ) );

		$value['name']        = trim( esc_html( $value['name'] ) ); // This sanitization code is used in wp-admin/nav-menus.php.
		$value['description'] = sanitize_text_field( $value['description'] );
		$value['parent']      = max( 0, intval( $value['parent'] ) );
		$value['auto_add']    = ! empty( $value['auto_add'] );

		/** This filter is documented in wp-includes/class-wp-customize-setting.php */
		return apply_filters( "customize_sanitize_{$this->id}", $value, $this );
	}

	/**
	 * Create/update the nav_menu term for this setting.
	 *
	 * Any created menus will have their assigned term IDs exported to the client
	 * via the customize_save_response filter. Likewise, any errors will be exported
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
	 * @return void
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
					$auto_add = false;
				}
			}
		} else {
			// Insert or update menu.
			$menu_data = wp_array_slice_assoc( $value, array( 'description', 'parent' ) );
			if ( isset( $value['name'] ) ) {
				$menu_data['menu-name'] = $value['name'];
			}

			$r = wp_update_nav_menu_object( $is_placeholder ? 0 : $this->term_id, $menu_data );
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

		// Make sure that new menus assigned to nav menu locations use their new IDs.
		if ( 'inserted' === $this->update_status ) {
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
		}
	}

	/**
	 * Update a nav_menu_options array.
	 *
	 * @since 4.3.0
	 *
	 * @see WP_Customize_Nav_Menu_Setting::filter_nav_menu_options()
	 * @see WP_Customize_Nav_Menu_Setting::update()
	 *
	 * @param array $nav_menu_options Array as returned by get_option( 'nav_menu_options' ).
	 * @param int   $menu_id          The term ID for the given menu.
	 * @param bool  $auto_add         Whether to auto-add or not.
	 * @return array
	 */
	protected function filter_nav_menu_options_value( $nav_menu_options, $menu_id, $auto_add ) {
		$nav_menu_options = (array) $nav_menu_options;
		if ( ! isset( $nav_menu_options['auto_add'] ) ) {
			$nav_menu_options['auto_add'] = array();
		}

		$i = array_search( $menu_id, $nav_menu_options['auto_add'] );
		if ( $auto_add && false === $i ) {
			array_push( $nav_menu_options['auto_add'], $this->term_id );
		} else if ( ! $auto_add && false !== $i ) {
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
	 * @return array
	 */
	function amend_customize_save_response( $data ) {
		if ( ! isset( $data['nav_menu_updates'] ) ) {
			$data['nav_menu_updates'] = array();
		}

		$data['nav_menu_updates'][] = array(
			'term_id'          => $this->term_id,
			'previous_term_id' => $this->previous_term_id,
			'error'            => $this->update_error ? $this->update_error->get_error_code() : null,
			'status'           => $this->update_status,
		);

		return $data;
	}
}
