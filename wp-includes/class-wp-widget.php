<?php
/**
 * Widget API: WP_Widget base class
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 4.4.0
 */

/**
 * Core base class extended to register widgets.
 *
 * This class must be extended for each widget, and WP_Widget::widget() must be overridden.
 *
 * If adding widget options, WP_Widget::update() and WP_Widget::form() should also be overridden.
 *
 * @since 2.8.0
 * @since 4.4.0 Moved to its own file from wp-includes/widgets.php
 */
#[AllowDynamicProperties]
class WP_Widget {

	/**
	 * Root ID for all widgets of this type.
	 *
	 * @since 2.8.0
	 * @var mixed|string
	 */
	public $id_base;

	/**
	 * Name for this widget type.
	 *
	 * @since 2.8.0
	 * @var string
	 */
	public $name;

	/**
	 * Option name for this widget type.
	 *
	 * @since 2.8.0
	 * @var string
	 */
	public $option_name;

	/**
	 * Alt option name for this widget type.
	 *
	 * @since 2.8.0
	 * @var string
	 */
	public $alt_option_name;

	/**
	 * Option array passed to wp_register_sidebar_widget().
	 *
	 * @since 2.8.0
	 * @var array
	 */
	public $widget_options;

	/**
	 * Option array passed to wp_register_widget_control().
	 *
	 * @since 2.8.0
	 * @var array
	 */
	public $control_options;

	/**
	 * Unique ID number of the current instance.
	 *
	 * @since 2.8.0
	 * @var bool|int
	 */
	public $number = false;

	/**
	 * Unique ID string of the current instance (id_base-number).
	 *
	 * @since 2.8.0
	 * @var bool|string
	 */
	public $id = false;

	/**
	 * Whether the widget data has been updated.
	 *
	 * Set to true when the data is updated after a POST submit - ensures it does
	 * not happen twice.
	 *
	 * @since 2.8.0
	 * @var bool
	 */
	public $updated = false;

	//
	// Member functions that must be overridden by subclasses.
	//

	/**
	 * Echoes the widget content.
	 *
	 * Subclasses should override this function to generate their widget code.
	 *
	 * @since 2.8.0
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function widget( $args, $instance ) {
		die( 'function WP_Widget::widget() must be overridden in a subclass.' );
	}

	/**
	 * Updates a particular instance of a widget.
	 *
	 * This function should check that `$new_instance` is set correctly. The newly-calculated
	 * value of `$instance` should be returned. If false is returned, the instance won't be
	 * saved/updated.
	 *
	 * @since 2.8.0
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	/**
	 * Outputs the settings update form.
	 *
	 * @since 2.8.0
	 *
	 * @param array $instance Current settings.
	 * @return string Default return is 'noform'.
	 */
	public function form( $instance ) {
		echo '<p class="no-options-widget">' . __( 'There are no options for this widget.' ) . '</p>';
		return 'noform';
	}

	// Functions you'll need to call.

	/**
	 * PHP5 constructor.
	 *
	 * @since 2.8.0
	 *
	 * @param string $id_base         Base ID for the widget, lowercase and unique. If left empty,
	 *                                a portion of the widget's PHP class name will be used. Has to be unique.
	 * @param string $name            Name for the widget displayed on the configuration page.
	 * @param array  $widget_options  Optional. Widget options. See wp_register_sidebar_widget() for
	 *                                information on accepted arguments. Default empty array.
	 * @param array  $control_options Optional. Widget control options. See wp_register_widget_control() for
	 *                                information on accepted arguments. Default empty array.
	 */
	public function __construct( $id_base, $name, $widget_options = array(), $control_options = array() ) {
		if ( ! empty( $id_base ) ) {
			$id_base = strtolower( $id_base );
		} else {
			$id_base = preg_replace( '/(wp_)?widget_/', '', strtolower( get_class( $this ) ) );
		}

		$this->id_base         = $id_base;
		$this->name            = $name;
		$this->option_name     = 'widget_' . $this->id_base;
		$this->widget_options  = wp_parse_args(
			$widget_options,
			array(
				'classname'                   => str_replace( '\\', '_', $this->option_name ),
				'customize_selective_refresh' => false,
			)
		);
		$this->control_options = wp_parse_args( $control_options, array( 'id_base' => $this->id_base ) );
	}

	/**
	 * PHP4 constructor.
	 *
	 * @since 2.8.0
	 * @deprecated 4.3.0 Use __construct() instead.
	 *
	 * @see WP_Widget::__construct()
	 *
	 * @param string $id_base         Base ID for the widget, lowercase and unique. If left empty,
	 *                                a portion of the widget's PHP class name will be used. Has to be unique.
	 * @param string $name            Name for the widget displayed on the configuration page.
	 * @param array  $widget_options  Optional. Widget options. See wp_register_sidebar_widget() for
	 *                                information on accepted arguments. Default empty array.
	 * @param array  $control_options Optional. Widget control options. See wp_register_widget_control() for
	 *                                information on accepted arguments. Default empty array.
	 */
	public function WP_Widget( $id_base, $name, $widget_options = array(), $control_options = array() ) {
		_deprecated_constructor( 'WP_Widget', '4.3.0', get_class( $this ) );
		WP_Widget::__construct( $id_base, $name, $widget_options, $control_options );
	}

	/**
	 * Constructs name attributes for use in form() fields
	 *
	 * This function should be used in form() methods to create name attributes for fields
	 * to be saved by update()
	 *
	 * @since 2.8.0
	 * @since 4.4.0 Array format field names are now accepted.
	 *
	 * @param string $field_name Field name.
	 * @return string Name attribute for `$field_name`.
	 */
	public function get_field_name( $field_name ) {
		$pos = strpos( $field_name, '[' );

		if ( false !== $pos ) {
			// Replace the first occurrence of '[' with ']['.
			$field_name = '[' . substr_replace( $field_name, '][', $pos, strlen( '[' ) );
		} else {
			$field_name = '[' . $field_name . ']';
		}

		return 'widget-' . $this->id_base . '[' . $this->number . ']' . $field_name;
	}

	/**
	 * Constructs id attributes for use in WP_Widget::form() fields.
	 *
	 * This function should be used in form() methods to create id attributes
	 * for fields to be saved by WP_Widget::update().
	 *
	 * @since 2.8.0
	 * @since 4.4.0 Array format field IDs are now accepted.
	 *
	 * @param string $field_name Field name.
	 * @return string ID attribute for `$field_name`.
	 */
	public function get_field_id( $field_name ) {
		$field_name = str_replace( array( '[]', '[', ']' ), array( '', '-', '' ), $field_name );
		$field_name = trim( $field_name, '-' );

		return 'widget-' . $this->id_base . '-' . $this->number . '-' . $field_name;
	}

	/**
	 * Register all widget instances of this widget class.
	 *
	 * @since 2.8.0
	 */
	public function _register() {
		$settings = $this->get_settings();
		$empty    = true;

		// When $settings is an array-like object, get an intrinsic array for use with array_keys().
		if ( $settings instanceof ArrayObject || $settings instanceof ArrayIterator ) {
			$settings = $settings->getArrayCopy();
		}

		if ( is_array( $settings ) ) {
			foreach ( array_keys( $settings ) as $number ) {
				if ( is_numeric( $number ) ) {
					$this->_set( $number );
					$this->_register_one( $number );
					$empty = false;
				}
			}
		}

		if ( $empty ) {
			// If there are none, we register the widget's existence with a generic template.
			$this->_set( 1 );
			$this->_register_one();
		}
	}

	/**
	 * Sets the internal order number for the widget instance.
	 *
	 * @since 2.8.0
	 *
	 * @param int $number The unique order number of this widget instance compared to other
	 *                    instances of the same class.
	 */
	public function _set( $number ) {
		$this->number = $number;
		$this->id     = $this->id_base . '-' . $number;
	}

	/**
	 * Retrieves the widget display callback.
	 *
	 * @since 2.8.0
	 *
	 * @return callable Display callback.
	 */
	public function _get_display_callback() {
		return array( $this, 'display_callback' );
	}

	/**
	 * Retrieves the widget update callback.
	 *
	 * @since 2.8.0
	 *
	 * @return callable Update callback.
	 */
	public function _get_update_callback() {
		return array( $this, 'update_callback' );
	}

	/**
	 * Retrieves the form callback.
	 *
	 * @since 2.8.0
	 *
	 * @return callable Form callback.
	 */
	public function _get_form_callback() {
		return array( $this, 'form_callback' );
	}

	/**
	 * Determines whether the current request is inside the Customizer preview.
	 *
	 * If true -- the current request is inside the Customizer preview, then
	 * the object cache gets suspended and widgets should check this to decide
	 * whether they should store anything persistently to the object cache,
	 * to transients, or anywhere else.
	 *
	 * @since 3.9.0
	 *
	 * @global WP_Customize_Manager $wp_customize
	 *
	 * @return bool True if within the Customizer preview, false if not.
	 */
	public function is_preview() {
		global $wp_customize;
		return ( isset( $wp_customize ) && $wp_customize->is_preview() );
	}

	/**
	 * Generates the actual widget content (Do NOT override).
	 *
	 * Finds the instance and calls WP_Widget::widget().
	 *
	 * @since 2.8.0
	 *
	 * @param array     $args        Display arguments. See WP_Widget::widget() for information
	 *                               on accepted arguments.
	 * @param int|array $widget_args {
	 *     Optional. Internal order number of the widget instance, or array of multi-widget arguments.
	 *     Default 1.
	 *
	 *     @type int $number Number increment used for multiples of the same widget.
	 * }
	 */
	public function display_callback( $args, $widget_args = 1 ) {
		if ( is_numeric( $widget_args ) ) {
			$widget_args = array( 'number' => $widget_args );
		}

		$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
		$this->_set( $widget_args['number'] );
		$instances = $this->get_settings();

		if ( isset( $instances[ $this->number ] ) ) {
			$instance = $instances[ $this->number ];

			/**
			 * Filters the settings for a particular widget instance.
			 *
			 * Returning false will effectively short-circuit display of the widget.
			 *
			 * @since 2.8.0
			 *
			 * @param array     $instance The current widget instance's settings.
			 * @param WP_Widget $widget   The current widget instance.
			 * @param array     $args     An array of default widget arguments.
			 */
			$instance = apply_filters( 'widget_display_callback', $instance, $this, $args );

			if ( false === $instance ) {
				return;
			}

			$was_cache_addition_suspended = wp_suspend_cache_addition();
			if ( $this->is_preview() && ! $was_cache_addition_suspended ) {
				wp_suspend_cache_addition( true );
			}

			$this->widget( $args, $instance );

			if ( $this->is_preview() ) {
				wp_suspend_cache_addition( $was_cache_addition_suspended );
			}
		}
	}

	/**
	 * Handles changed settings (Do NOT override).
	 *
	 * @since 2.8.0
	 *
	 * @global array $wp_registered_widgets
	 *
	 * @param int $deprecated Not used.
	 */
	public function update_callback( $deprecated = 1 ) {
		global $wp_registered_widgets;

		$all_instances = $this->get_settings();

		// We need to update the data.
		if ( $this->updated ) {
			return;
		}

		if ( isset( $_POST['delete_widget'] ) && $_POST['delete_widget'] ) {
			// Delete the settings for this instance of the widget.
			if ( isset( $_POST['the-widget-id'] ) ) {
				$del_id = $_POST['the-widget-id'];
			} else {
				return;
			}

			if ( isset( $wp_registered_widgets[ $del_id ]['params'][0]['number'] ) ) {
				$number = $wp_registered_widgets[ $del_id ]['params'][0]['number'];

				if ( $this->id_base . '-' . $number == $del_id ) {
					unset( $all_instances[ $number ] );
				}
			}
		} else {
			if ( isset( $_POST[ 'widget-' . $this->id_base ] ) && is_array( $_POST[ 'widget-' . $this->id_base ] ) ) {
				$settings = $_POST[ 'widget-' . $this->id_base ];
			} elseif ( isset( $_POST['id_base'] ) && $_POST['id_base'] == $this->id_base ) {
				$num      = $_POST['multi_number'] ? (int) $_POST['multi_number'] : (int) $_POST['widget_number'];
				$settings = array( $num => array() );
			} else {
				return;
			}

			foreach ( $settings as $number => $new_instance ) {
				$new_instance = stripslashes_deep( $new_instance );
				$this->_set( $number );

				$old_instance = isset( $all_instances[ $number ] ) ? $all_instances[ $number ] : array();

				$was_cache_addition_suspended = wp_suspend_cache_addition();
				if ( $this->is_preview() && ! $was_cache_addition_suspended ) {
					wp_suspend_cache_addition( true );
				}

				$instance = $this->update( $new_instance, $old_instance );

				if ( $this->is_preview() ) {
					wp_suspend_cache_addition( $was_cache_addition_suspended );
				}

				/**
				 * Filters a widget's settings before saving.
				 *
				 * Returning false will effectively short-circuit the widget's ability
				 * to update settings.
				 *
				 * @since 2.8.0
				 *
				 * @param array     $instance     The current widget instance's settings.
				 * @param array     $new_instance Array of new widget settings.
				 * @param array     $old_instance Array of old widget settings.
				 * @param WP_Widget $widget       The current widget instance.
				 */
				$instance = apply_filters( 'widget_update_callback', $instance, $new_instance, $old_instance, $this );

				if ( false !== $instance ) {
					$all_instances[ $number ] = $instance;
				}

				break; // Run only once.
			}
		}

		$this->save_settings( $all_instances );
		$this->updated = true;
	}

	/**
	 * Generates the widget control form (Do NOT override).
	 *
	 * @since 2.8.0
	 *
	 * @param int|array $widget_args {
	 *     Optional. Internal order number of the widget instance, or array of multi-widget arguments.
	 *     Default 1.
	 *
	 *     @type int $number Number increment used for multiples of the same widget.
	 * }
	 * @return string|null
	 */
	public function form_callback( $widget_args = 1 ) {
		if ( is_numeric( $widget_args ) ) {
			$widget_args = array( 'number' => $widget_args );
		}

		$widget_args   = wp_parse_args( $widget_args, array( 'number' => -1 ) );
		$all_instances = $this->get_settings();

		if ( -1 == $widget_args['number'] ) {
			// We echo out a form where 'number' can be set later.
			$this->_set( '__i__' );
			$instance = array();
		} else {
			$this->_set( $widget_args['number'] );
			$instance = $all_instances[ $widget_args['number'] ];
		}

		/**
		 * Filters the widget instance's settings before displaying the control form.
		 *
		 * Returning false effectively short-circuits display of the control form.
		 *
		 * @since 2.8.0
		 *
		 * @param array     $instance The current widget instance's settings.
		 * @param WP_Widget $widget   The current widget instance.
		 */
		$instance = apply_filters( 'widget_form_callback', $instance, $this );

		$return = null;

		if ( false !== $instance ) {
			$return = $this->form( $instance );

			/**
			 * Fires at the end of the widget control form.
			 *
			 * Use this hook to add extra fields to the widget form. The hook
			 * is only fired if the value passed to the 'widget_form_callback'
			 * hook is not false.
			 *
			 * Note: If the widget has no form, the text echoed from the default
			 * form method can be hidden using CSS.
			 *
			 * @since 2.8.0
			 *
			 * @param WP_Widget $widget   The widget instance (passed by reference).
			 * @param null      $return   Return null if new fields are added.
			 * @param array     $instance An array of the widget's settings.
			 */
			do_action_ref_array( 'in_widget_form', array( &$this, &$return, $instance ) );
		}

		return $return;
	}

	/**
	 * Registers an instance of the widget class.
	 *
	 * @since 2.8.0
	 *
	 * @param int $number Optional. The unique order number of this widget instance
	 *                    compared to other instances of the same class. Default -1.
	 */
	public function _register_one( $number = -1 ) {
		wp_register_sidebar_widget(
			$this->id,
			$this->name,
			$this->_get_display_callback(),
			$this->widget_options,
			array( 'number' => $number )
		);

		_register_widget_update_callback(
			$this->id_base,
			$this->_get_update_callback(),
			$this->control_options,
			array( 'number' => -1 )
		);

		_register_widget_form_callback(
			$this->id,
			$this->name,
			$this->_get_form_callback(),
			$this->control_options,
			array( 'number' => $number )
		);
	}

	/**
	 * Saves the settings for all instances of the widget class.
	 *
	 * @since 2.8.0
	 *
	 * @param array $settings Multi-dimensional array of widget instance settings.
	 */
	public function save_settings( $settings ) {
		$settings['_multiwidget'] = 1;
		update_option( $this->option_name, $settings );
	}

	/**
	 * Retrieves the settings for all instances of the widget class.
	 *
	 * @since 2.8.0
	 *
	 * @return array Multi-dimensional array of widget instance settings.
	 */
	public function get_settings() {

		$settings = get_option( $this->option_name );

		if ( false === $settings ) {
			$settings = array();
			if ( isset( $this->alt_option_name ) ) {
				// Get settings from alternative (legacy) option.
				$settings = get_option( $this->alt_option_name, array() );

				// Delete the alternative (legacy) option as the new option will be created using `$this->option_name`.
				delete_option( $this->alt_option_name );
			}
			// Save an option so it can be autoloaded next time.
			$this->save_settings( $settings );
		}

		if ( ! is_array( $settings ) && ! ( $settings instanceof ArrayObject || $settings instanceof ArrayIterator ) ) {
			$settings = array();
		}

		if ( ! empty( $settings ) && ! isset( $settings['_multiwidget'] ) ) {
			// Old format, convert if single widget.
			$settings = wp_convert_widget_settings( $this->id_base, $this->option_name, $settings );
		}

		unset( $settings['_multiwidget'], $settings['__i__'] );

		return $settings;
	}
}
