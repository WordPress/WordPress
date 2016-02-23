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
	 * Unique string identifier for the setting.
	 *
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

	/**
	 * @var array
	 */
	protected $id_data = array();

	/**
	 * Whether or not preview() was called.
	 *
	 * @since 4.4.0
	 * @access protected
	 * @var bool
	 */
	protected $is_previewed = false;

	/**
	 * Cache of multidimensional values to improve performance.
	 *
	 * @since 4.4.0
	 * @access protected
	 * @var array
	 * @static
	 */
	protected static $aggregated_multidimensionals = array();

	/**
	 * Whether the multidimensional setting is aggregated.
	 *
	 * @since 4.4.0
	 * @access protected
	 * @var bool
	 */
	protected $is_multidimensional_aggregated = false;

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
			if ( isset( $args[ $key ] ) ) {
				$this->$key = $args[ $key ];
			}
		}

		$this->manager = $manager;
		$this->id = $id;

		// Parse the ID for array keys.
		$this->id_data['keys'] = preg_split( '/\[/', str_replace( ']', '', $this->id ) );
		$this->id_data['base'] = array_shift( $this->id_data['keys'] );

		// Rebuild the ID.
		$this->id = $this->id_data[ 'base' ];
		if ( ! empty( $this->id_data[ 'keys' ] ) ) {
			$this->id .= '[' . implode( '][', $this->id_data['keys'] ) . ']';
		}

		if ( $this->sanitize_callback ) {
			add_filter( "customize_sanitize_{$this->id}", $this->sanitize_callback, 10, 2 );
		}
		if ( $this->sanitize_js_callback ) {
			add_filter( "customize_sanitize_js_{$this->id}", $this->sanitize_js_callback, 10, 2 );
		}

		if ( 'option' === $this->type || 'theme_mod' === $this->type ) {
			// Other setting types can opt-in to aggregate multidimensional explicitly.
			$this->aggregate_multidimensional();

			// Allow option settings to indicate whether they should be autoloaded.
			if ( 'option' === $this->type && isset( $args['autoload'] ) ) {
				self::$aggregated_multidimensionals[ $this->type ][ $this->id_data['base'] ]['autoload'] = $args['autoload'];
			}
		}
	}

	/**
	 * Get parsed ID data for multidimensional setting.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return array {
	 *     ID data for multidimensional setting.
	 *
	 *     @type string $base ID base
	 *     @type array  $keys Keys for multidimensional array.
	 * }
	 */
	final public function id_data() {
		return $this->id_data;
	}

	/**
	 * Set up the setting for aggregated multidimensional values.
	 *
	 * When a multidimensional setting gets aggregated, all of its preview and update
	 * calls get combined into one call, greatly improving performance.
	 *
	 * @since 4.4.0
	 * @access protected
	 */
	protected function aggregate_multidimensional() {
		$id_base = $this->id_data['base'];
		if ( ! isset( self::$aggregated_multidimensionals[ $this->type ] ) ) {
			self::$aggregated_multidimensionals[ $this->type ] = array();
		}
		if ( ! isset( self::$aggregated_multidimensionals[ $this->type ][ $id_base ] ) ) {
			self::$aggregated_multidimensionals[ $this->type ][ $id_base ] = array(
				'previewed_instances'       => array(), // Calling preview() will add the $setting to the array.
				'preview_applied_instances' => array(), // Flags for which settings have had their values applied.
				'root_value'                => $this->get_root_value( array() ), // Root value for initial state, manipulated by preview and update calls.
			);
		}

		if ( ! empty( $this->id_data['keys'] ) ) {
			// Note the preview-applied flag is cleared at priority 9 to ensure it is cleared before a deferred-preview runs.
			add_action( "customize_post_value_set_{$this->id}", array( $this, '_clear_aggregated_multidimensional_preview_applied_flag' ), 9 );
			$this->is_multidimensional_aggregated = true;
		}
	}

	/**
	 * The ID for the current site when the preview() method was called.
	 *
	 * @since 4.2.0
	 * @access protected
	 * @var int
	 */
	protected $_previewed_blog_id;

	/**
	 * Return true if the current site is not the same as the previewed site.
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
	 * Add filters to supply the setting's value when accessed.
	 *
	 * If the setting already has a pre-existing value and there is no incoming
	 * post value for the setting, then this method will short-circuit since
	 * there is no change to preview.
	 *
	 * @since 3.4.0
	 * @since 4.4.0 Added boolean return value.
	 * @access public
	 *
	 * @return bool False when preview short-circuits due no change needing to be previewed.
	 */
	public function preview() {
		if ( ! isset( $this->_previewed_blog_id ) ) {
			$this->_previewed_blog_id = get_current_blog_id();
		}

		// Prevent re-previewing an already-previewed setting.
		if ( $this->is_previewed ) {
			return true;
		}

		$id_base = $this->id_data['base'];
		$is_multidimensional = ! empty( $this->id_data['keys'] );
		$multidimensional_filter = array( $this, '_multidimensional_preview_filter' );

		/*
		 * Check if the setting has a pre-existing value (an isset check),
		 * and if doesn't have any incoming post value. If both checks are true,
		 * then the preview short-circuits because there is nothing that needs
		 * to be previewed.
		 */
		$undefined = new stdClass();
		$needs_preview = ( $undefined !== $this->post_value( $undefined ) );
		$value = null;

		// Since no post value was defined, check if we have an initial value set.
		if ( ! $needs_preview ) {
			if ( $this->is_multidimensional_aggregated ) {
				$root = self::$aggregated_multidimensionals[ $this->type ][ $id_base ]['root_value'];
				$value = $this->multidimensional_get( $root, $this->id_data['keys'], $undefined );
			} else {
				$default = $this->default;
				$this->default = $undefined; // Temporarily set default to undefined so we can detect if existing value is set.
				$value = $this->value();
				$this->default = $default;
			}
			$needs_preview = ( $undefined === $value ); // Because the default needs to be supplied.
		}

		// If the setting does not need previewing now, defer to when it has a value to preview.
		if ( ! $needs_preview ) {
			if ( ! has_action( "customize_post_value_set_{$this->id}", array( $this, 'preview' ) ) ) {
				add_action( "customize_post_value_set_{$this->id}", array( $this, 'preview' ) );
			}
			return false;
		}

		switch ( $this->type ) {
			case 'theme_mod' :
				if ( ! $is_multidimensional ) {
					add_filter( "theme_mod_{$id_base}", array( $this, '_preview_filter' ) );
				} else {
					if ( empty( self::$aggregated_multidimensionals[ $this->type ][ $id_base ]['previewed_instances'] ) ) {
						// Only add this filter once for this ID base.
						add_filter( "theme_mod_{$id_base}", $multidimensional_filter );
					}
					self::$aggregated_multidimensionals[ $this->type ][ $id_base ]['previewed_instances'][ $this->id ] = $this;
				}
				break;
			case 'option' :
				if ( ! $is_multidimensional ) {
					add_filter( "pre_option_{$id_base}", array( $this, '_preview_filter' ) );
				} else {
					if ( empty( self::$aggregated_multidimensionals[ $this->type ][ $id_base ]['previewed_instances'] ) ) {
						// Only add these filters once for this ID base.
						add_filter( "option_{$id_base}", $multidimensional_filter );
						add_filter( "default_option_{$id_base}", $multidimensional_filter );
					}
					self::$aggregated_multidimensionals[ $this->type ][ $id_base ]['previewed_instances'][ $this->id ] = $this;
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

		$this->is_previewed = true;

		return true;
	}

	/**
	 * Clear out the previewed-applied flag for a multidimensional-aggregated value whenever its post value is updated.
	 *
	 * This ensures that the new value will get sanitized and used the next time
	 * that `WP_Customize_Setting::_multidimensional_preview_filter()`
	 * is called for this setting.
	 *
	 * @since 4.4.0
	 * @access private
	 * @see WP_Customize_Manager::set_post_value()
	 * @see WP_Customize_Setting::_multidimensional_preview_filter()
	 */
	final public function _clear_aggregated_multidimensional_preview_applied_flag() {
		unset( self::$aggregated_multidimensionals[ $this->type ][ $this->id_data['base'] ]['preview_applied_instances'][ $this->id ] );
	}

	/**
	 * Callback function to filter non-multidimensional theme mods and options.
	 *
	 * If switch_to_blog() was called after the preview() method, and the current
	 * site is now not the same site, then this method does a no-op and returns
	 * the original value.
	 *
	 * @since 3.4.0
	 *
	 * @param mixed $original Old value.
	 * @return mixed New or old value.
	 */
	public function _preview_filter( $original ) {
		if ( ! $this->is_current_blog_previewed() ) {
			return $original;
		}

		$undefined = new stdClass(); // Symbol hack.
		$post_value = $this->post_value( $undefined );
		if ( $undefined !== $post_value ) {
			$value = $post_value;
		} else {
			/*
			 * Note that we don't use $original here because preview() will
			 * not add the filter in the first place if it has an initial value
			 * and there is no post value.
			 */
			$value = $this->default;
		}
		return $value;
	}

	/**
	 * Callback function to filter multidimensional theme mods and options.
	 *
	 * For all multidimensional settings of a given type, the preview filter for
	 * the first setting previewed will be used to apply the values for the others.
	 *
	 * @since 4.4.0
	 * @access private
	 *
	 * @see WP_Customize_Setting::$aggregated_multidimensionals
	 * @param mixed $original Original root value.
	 * @return mixed New or old value.
	 */
	final public function _multidimensional_preview_filter( $original ) {
		if ( ! $this->is_current_blog_previewed() ) {
			return $original;
		}

		$id_base = $this->id_data['base'];

		// If no settings have been previewed yet (which should not be the case, since $this is), just pass through the original value.
		if ( empty( self::$aggregated_multidimensionals[ $this->type ][ $id_base ]['previewed_instances'] ) ) {
			return $original;
		}

		foreach ( self::$aggregated_multidimensionals[ $this->type ][ $id_base ]['previewed_instances'] as $previewed_setting ) {
			// Skip applying previewed value for any settings that have already been applied.
			if ( ! empty( self::$aggregated_multidimensionals[ $this->type ][ $id_base ]['preview_applied_instances'][ $previewed_setting->id ] ) ) {
				continue;
			}

			// Do the replacements of the posted/default sub value into the root value.
			$value = $previewed_setting->post_value( $previewed_setting->default );
			$root = self::$aggregated_multidimensionals[ $previewed_setting->type ][ $id_base ]['root_value'];
			$root = $previewed_setting->multidimensional_replace( $root, $previewed_setting->id_data['keys'], $value );
			self::$aggregated_multidimensionals[ $previewed_setting->type ][ $id_base ]['root_value'] = $root;

			// Mark this setting having been applied so that it will be skipped when the filter is called again.
			self::$aggregated_multidimensionals[ $previewed_setting->type ][ $id_base ]['preview_applied_instances'][ $previewed_setting->id ] = true;
		}

		return self::$aggregated_multidimensionals[ $this->type ][ $id_base ]['root_value'];
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
	 * Get the root value for a setting, especially for multidimensional ones.
	 *
	 * @since 4.4.0
	 * @access protected
	 *
	 * @param mixed $default Value to return if root does not exist.
	 * @return mixed
	 */
	protected function get_root_value( $default = null ) {
		$id_base = $this->id_data['base'];
		if ( 'option' === $this->type ) {
			return get_option( $id_base, $default );
		} else if ( 'theme_mod' ) {
			return get_theme_mod( $id_base, $default );
		} else {
			/*
			 * Any WP_Customize_Setting subclass implementing aggregate multidimensional
			 * will need to override this method to obtain the data from the appropriate
			 * location.
			 */
			return $default;
		}
	}

	/**
	 * Set the root value for a setting, especially for multidimensional ones.
	 *
	 * @since 4.4.0
	 * @access protected
	 *
	 * @param mixed $value Value to set as root of multidimensional setting.
	 * @return bool Whether the multidimensional root was updated successfully.
	 */
	protected function set_root_value( $value ) {
		$id_base = $this->id_data['base'];
		if ( 'option' === $this->type ) {
			$autoload = true;
			if ( isset( self::$aggregated_multidimensionals[ $this->type ][ $this->id_data['base'] ]['autoload'] ) ) {
				$autoload = self::$aggregated_multidimensionals[ $this->type ][ $this->id_data['base'] ]['autoload'];
			}
			return update_option( $id_base, $value, $autoload );
		} else if ( 'theme_mod' ) {
			set_theme_mod( $id_base, $value );
			return true;
		} else {
			/*
			 * Any WP_Customize_Setting subclass implementing aggregate multidimensional
			 * will need to override this method to obtain the data from the appropriate
			 * location.
			 */
			return false;
		}
	}

	/**
	 * Save the value of the setting, using the related API.
	 *
	 * @since 3.4.0
	 *
	 * @param mixed $value The value to update.
	 * @return bool The result of saving the value.
	 */
	protected function update( $value ) {
		$id_base = $this->id_data['base'];
		if ( 'option' === $this->type || 'theme_mod' === $this->type ) {
			if ( ! $this->is_multidimensional_aggregated ) {
				return $this->set_root_value( $value );
			} else {
				$root = self::$aggregated_multidimensionals[ $this->type ][ $id_base ]['root_value'];
				$root = $this->multidimensional_replace( $root, $this->id_data['keys'], $value );
				self::$aggregated_multidimensionals[ $this->type ][ $id_base ]['root_value'] = $root;
				return $this->set_root_value( $root );
			}
		} else {
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
			do_action( "customize_update_{$this->type}", $value, $this );

			return has_action( "customize_update_{$this->type}" );
		}
	}

	/**
	 * Deprecated method.
	 *
	 * @since 3.4.0
	 * @deprecated 4.4.0 Deprecated in favor of update() method.
	 */
	protected function _update_theme_mod() {
		_deprecated_function( __METHOD__, '4.4.0', __CLASS__ . '::update()' );
	}

	/**
	 * Deprecated method.
	 *
	 * @since 3.4.0
	 * @deprecated 4.4.0 Deprecated in favor of update() method.
	 */
	protected function _update_option() {
		_deprecated_function( __METHOD__, '4.4.0', __CLASS__ . '::update()' );
	}

	/**
	 * Fetch the value of the setting.
	 *
	 * @since 3.4.0
	 *
	 * @return mixed The value.
	 */
	public function value() {
		$id_base = $this->id_data['base'];
		$is_core_type = ( 'option' === $this->type || 'theme_mod' === $this->type );

		if ( ! $is_core_type && ! $this->is_multidimensional_aggregated ) {
			$value = $this->get_root_value( $this->default );

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
			$value = apply_filters( "customize_value_{$id_base}", $value );
		} else if ( $this->is_multidimensional_aggregated ) {
			$root_value = self::$aggregated_multidimensionals[ $this->type ][ $id_base ]['root_value'];
			$value = $this->multidimensional_get( $root_value, $this->id_data['keys'], $this->default );
		} else {
			$value = $this->get_root_value( $this->default );
		}
		return $value;
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
	 * @return mixed
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

/** WP_Customize_Filter_Setting class */
require_once( ABSPATH . WPINC . '/customize/class-wp-customize-filter-setting.php' );

/** WP_Customize_Header_Image_Setting class */
require_once( ABSPATH . WPINC . '/customize/class-wp-customize-header-image-setting.php' );

/** WP_Customize_Background_Image_Setting class */
require_once( ABSPATH . WPINC . '/customize/class-wp-customize-background-image-setting.php' );

/** WP_Customize_Nav_Menu_Item_Setting class */
require_once( ABSPATH . WPINC . '/customize/class-wp-customize-nav-menu-item-setting.php' );

/** WP_Customize_Nav_Menu_Setting class */
require_once( ABSPATH . WPINC . '/customize/class-wp-customize-nav-menu-setting.php' );
