<?php
namespace Elementor;

use Elementor\Core\Base\Base_Object;
use Elementor\Core\DynamicTags\Manager;
use Elementor\Core\Breakpoints\Manager as Breakpoints_Manager;
use Elementor\Core\Frontend\Performance;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor controls stack.
 *
 * An abstract class that provides the needed properties and methods to
 * manage and handle controls in the editor panel to inheriting classes.
 *
 * @since 1.4.0
 * @abstract
 */
abstract class Controls_Stack extends Base_Object {

	/**
	 * Responsive 'desktop' device name.
	 *
	 * @deprecated 3.4.0
	 */
	const RESPONSIVE_DESKTOP = 'desktop';

	/**
	 * Responsive 'tablet' device name.
	 *
	 * @deprecated 3.4.0
	 */
	const RESPONSIVE_TABLET = 'tablet';

	/**
	 * Responsive 'mobile' device name.
	 *
	 * @deprecated 3.4.0
	 */
	const RESPONSIVE_MOBILE = 'mobile';

	/**
	 * Generic ID.
	 *
	 * Holds the unique ID.
	 *
	 * @access private
	 *
	 * @var string
	 */
	private $id;

	private $active_settings;

	private $parsed_active_settings;

	/**
	 * Parsed Dynamic Settings.
	 *
	 * @access private
	 *
	 * @var null|array
	 */
	private $parsed_dynamic_settings;

	/**
	 * Raw Data.
	 *
	 * Holds all the raw data including the element type, the child elements,
	 * the user data.
	 *
	 * @access private
	 *
	 * @var null|array
	 */
	private $data;

	/**
	 * The configuration.
	 *
	 * Holds the configuration used to generate the Elementor editor. It includes
	 * the element name, icon, categories, etc.
	 *
	 * @access private
	 *
	 * @var null|array
	 */
	private $config;

	/**
	 * The additional configuration.
	 *
	 * Holds additional configuration that has been set using `set_config` method.
	 * The `config` property is not modified directly while using the method because
	 * it's used to check whether the initial config already loaded (in `get_config`).
	 * After the initial config loaded, the additional config is merged into it.
	 *
	 * @access private
	 *
	 * @var null|array
	 */
	private $additional_config = [];

	/**
	 * Current section.
	 *
	 * Holds the current section while inserting a set of controls sections.
	 *
	 * @access private
	 *
	 * @var null|array
	 */
	private $current_section;

	/**
	 * Current tab.
	 *
	 * Holds the current tab while inserting a set of controls tabs.
	 *
	 * @access private
	 *
	 * @var null|array
	 */
	private $current_tab;

	/**
	 * Current popover.
	 *
	 * Holds the current popover while inserting a set of controls.
	 *
	 * @access private
	 *
	 * @var null|array
	 */
	private $current_popover;

	/**
	 * Injection point.
	 *
	 * Holds the injection point in the stack where the control will be inserted.
	 *
	 * @access private
	 *
	 * @var null|array
	 */
	private $injection_point;


	/**
	 * Data sanitized.
	 *
	 * @access private
	 *
	 * @var bool
	 */
	private $settings_sanitized = false;

	/**
	 * Element render attributes.
	 *
	 * Holds all the render attributes of the element. Used to store data like
	 * the HTML class name and the class value, or HTML element ID name and value.
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $render_attributes = [];

	/**
	 * Get element name.
	 *
	 * Retrieve the element name.
	 *
	 * @since 1.4.0
	 * @access public
	 * @abstract
	 *
	 * @return string The name.
	 */
	abstract public function get_name();

	/**
	 * Get unique name.
	 *
	 * Some classes need to use unique names, this method allows you to create
	 * them. By default it retrieves the regular name.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @return string Unique name.
	 */
	public function get_unique_name() {
		return $this->get_name();
	}

	/**
	 * Get element ID.
	 *
	 * Retrieve the element generic ID.
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @return string The ID.
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get element ID.
	 *
	 * Retrieve the element generic ID as integer.
	 *
	 * @since 1.8.0
	 * @access public
	 *
	 * @return string The converted ID.
	 */
	public function get_id_int() {
		/** We ignore possible notices, in order to support elements created prior to v1.8.0 and might include
		 *  non-base 16 characters as part of their ID.
		 */
		return @hexdec( $this->id );
	}

	/**
	 * Get widget number.
	 *
	 * Get the first three numbers of the element converted ID.
	 *
	 * @since 3.16
	 * @access public
	 *
	 * @return string The widget number.
	 */
	public function get_widget_number(): string {
		return substr( $this->get_id_int(), 0, 3 );
	}

	/**
	 * Get the type.
	 *
	 * Retrieve the type, e.g. 'stack', 'section', 'widget' etc.
	 *
	 * @since 1.4.0
	 * @access public
	 * @static
	 *
	 * @return string The type.
	 */
	public static function get_type() {
		return 'stack';
	}

	/**
	 * @since 2.9.0
	 * @access public
	 *
	 * @return bool
	 */
	public function is_editable() {
		return true;
	}

	/**
	 * Get current section.
	 *
	 * When inserting new controls, this method will retrieve the current section.
	 *
	 * @since 1.7.1
	 * @access public
	 *
	 * @return null|array Current section.
	 */
	public function get_current_section() {
		return $this->current_section;
	}

	/**
	 * Get current tab.
	 *
	 * When inserting new controls, this method will retrieve the current tab.
	 *
	 * @since 1.7.1
	 * @access public
	 *
	 * @return null|array Current tab.
	 */
	public function get_current_tab() {
		return $this->current_tab;
	}

	/**
	 * Get controls.
	 *
	 * Retrieve all the controls or, when requested, a specific control.
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @param string $control_id The ID of the requested control. Optional field,
	 *                           when set it will return a specific control.
	 *                           Default is null.
	 *
	 * @return mixed Controls list.
	 */
	public function get_controls( $control_id = null ) {
		$stack = $this->get_stack();

		if ( null !== $control_id ) {
			$control_data = self::get_items( $stack['controls'], $control_id );
			if ( null === $control_data && ! empty( $stack['style_controls'] ) ) {
				$control_data = self::get_items( $stack['style_controls'], $control_id );
			}

			return $control_data;
		}

		$controls = $stack['controls'];

		if ( Performance::is_use_style_controls() && ! empty( $stack['style_controls'] ) ) {
			$controls += $stack['style_controls'];
		}

		return self::get_items( $controls, $control_id );
	}

	/**
	 * Get active controls.
	 *
	 * Retrieve an array of active controls that meet the condition field.
	 *
	 * If specific controls was given as a parameter, retrieve active controls
	 * from that list, otherwise check for all the controls available.
	 *
	 * @since 1.4.0
	 * @since 2.0.9 Added the `controls` and the `settings` parameters.
	 * @access public
	 * @deprecated 3.0.0
	 *
	 * @param array $controls Optional. An array of controls. Default is null.
	 * @param array $settings Optional. Controls settings. Default is null.
	 *
	 * @return array Active controls.
	 */
	public function get_active_controls( ?array $controls = null, ?array $settings = null ) {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.0.0' );

		if ( ! $controls ) {
			$controls = $this->get_controls();
		}

		if ( ! $settings ) {
			$settings = $this->get_controls_settings();
		}

		$active_controls = array_reduce(
			array_keys( $controls ), function( $active_controls, $control_key ) use ( $controls, $settings ) {
				$control = $controls[ $control_key ];

				if ( $this->is_control_visible( $control, $settings, $controls ) ) {
					$active_controls[ $control_key ] = $control;
				}

				return $active_controls;
			}, []
		);

		return $active_controls;
	}

	/**
	 * Get controls settings.
	 *
	 * Retrieve the settings for all the controls that represent them.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return array Controls settings.
	 */
	public function get_controls_settings() {
		return array_intersect_key( $this->get_settings(), $this->get_controls() );
	}

	/**
	 * Add new control to stack.
	 *
	 * Register a single control to allow the user to set/update data.
	 *
	 * This method should be used inside `register_controls()`.
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @param string $id      Control ID.
	 * @param array  $args    Control arguments.
	 * @param array  $options Optional. Control options. Default is an empty array.
	 *
	 * @return bool True if control added, False otherwise.
	 */
	public function add_control( $id, array $args, $options = [] ) {
		$default_options = [
			'overwrite' => false,
			'position' => null,
		];

		if ( isset( $args['scheme'] ) ) {
			$args['global'] = [
				'default' => Plugin::$instance->kits_manager->convert_scheme_to_global( $args['scheme'] ),
			];

			unset( $args['scheme'] );
		}

		$options = array_merge( $default_options, $options );

		if ( $options['position'] ) {
			$this->start_injection( $options['position'] );
		}

		if ( $this->injection_point ) {
			$options['index'] = $this->injection_point['index']++;
		}

		if ( empty( $args['type'] ) || ! in_array( $args['type'], [ Controls_Manager::SECTION, Controls_Manager::WP_WIDGET ], true ) ) {
			$args = $this->handle_control_position( $args, $id, $options['overwrite'] );
		}

		if ( $options['position'] ) {
			$this->end_injection();
		}

		unset( $options['position'] );

		if ( $this->current_popover ) {
			$args['popover'] = [];

			if ( ! $this->current_popover['initialized'] ) {
				$args['popover']['start'] = true;

				$this->current_popover['initialized'] = true;
			}
		}

		if ( Performance::should_optimize_controls() ) {
			$ui_controls = [
				Controls_Manager::RAW_HTML,
				Controls_Manager::DIVIDER,
				Controls_Manager::HEADING,
				Controls_Manager::BUTTON,
				Controls_Manager::ALERT,
				Controls_Manager::NOTICE,
				Controls_Manager::DEPRECATED_NOTICE,
			];

			if ( ! empty( $args['type'] ) && ! empty( $args['section'] ) && in_array( $args['type'], $ui_controls ) ) {
				$args = [
					'type' => $args['type'],
					'section' => $args['section'],
				];
			}

			unset(
				$args['label_block'],
				$args['label'],
				$args['title'],
				$args['tab'],
				$args['options'],
				$args['placeholder'],
				$args['separator'],
				$args['size_units'],
				$args['range'],
				$args['toggle'],
				$args['ai'],
				$args['classes'],
				$args['style_transfer'],
				$args['show_label'],
				$args['description'],
				$args['label_on'],
				$args['label_off'],
				$args['labels'],
				$args['handles'],
				$args['editor_available'],
			);
		}

		return Plugin::$instance->controls_manager->add_control_to_stack( $this, $id, $args, $options );
	}

	/**
	 * Remove control from stack.
	 *
	 * Unregister an existing control and remove it from the stack.
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @param string $control_id Control ID.
	 *
	 * @return bool|\WP_Error
	 */
	public function remove_control( $control_id ) {
		return Plugin::$instance->controls_manager->remove_control_from_stack( $this->get_unique_name(), $control_id );
	}

	/**
	 * Update control in stack.
	 *
	 * Change the value of an existing control in the stack. When you add new
	 * control you set the `$args` parameter, this method allows you to update
	 * the arguments by passing new data.
	 *
	 * @since 1.4.0
	 * @since 1.8.1 New `$options` parameter added.
	 *
	 * @access public
	 *
	 * @param string $control_id Control ID.
	 * @param array  $args       Control arguments. Only the new fields you want
	 *                           to update.
	 * @param array  $options    Optional. Some additional options. Default is
	 *                           an empty array.
	 *
	 * @return bool
	 */
	public function update_control( $control_id, array $args, array $options = [] ) {
		$is_updated = Plugin::$instance->controls_manager->update_control_in_stack( $this, $control_id, $args, $options );

		if ( ! $is_updated ) {
			return false;
		}

		$control = $this->get_controls( $control_id );

		if ( Controls_Manager::SECTION === $control['type'] ) {
			$section_args = $this->get_section_args( $control_id );

			$section_controls = $this->get_section_controls( $control_id );

			foreach ( $section_controls as $section_control_id => $section_control ) {
				$this->update_control( $section_control_id, $section_args, $options );
			}
		}

		return true;
	}

	/**
	 * Get stack.
	 *
	 * Retrieve the stack of controls.
	 *
	 * @since 1.9.2
	 * @access public
	 *
	 * @return array Stack of controls.
	 */
	public function get_stack() {
		$stack = Plugin::$instance->controls_manager->get_element_stack( $this );

		if ( null === $stack ) {
			$this->init_controls();

			return Plugin::$instance->controls_manager->get_element_stack( $this );
		}

		return $stack;
	}

	/**
	 * Get position information.
	 *
	 * Retrieve the position while injecting data, based on the element type.
	 *
	 * @since 1.7.0
	 * @access public
	 *
	 * @param array $position {
	 *     The injection position.
	 *
	 *     @type string $type     Injection type, either `control` or `section`.
	 *                            Default is `control`.
	 *     @type string $at       Where to inject. If `$type` is `control` accepts
	 *                            `before` and `after`. If `$type` is `section`
	 *                            accepts `start` and `end`. Default values based on
	 *                            the `type`.
	 *     @type string $of       Control/Section ID.
	 *     @type array  $fallback Fallback injection position. When the position is
	 *                            not found it will try to fetch the fallback
	 *                            position.
	 * }
	 *
	 * @return bool|array Position info.
	 */
	final public function get_position_info( array $position ) {
		$default_position = [
			'type' => 'control',
			'at' => 'after',
		];

		if ( ! empty( $position['type'] ) && 'section' === $position['type'] ) {
			$default_position['at'] = 'end';
		}

		$position = array_merge( $default_position, $position );

		if (
			( 'control' === $position['type'] && in_array( $position['at'], [ 'start', 'end' ], true ) ) ||
			( 'section' === $position['type'] && in_array( $position['at'], [ 'before', 'after' ], true ) )
		) {
			_doing_it_wrong( sprintf( '%s::%s', get_called_class(), __FUNCTION__ ), 'Invalid position arguments. Use `before` / `after` for control or `start` / `end` for section.', '1.7.0' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			return false;
		}

		$target_control_index = $this->get_control_index( $position['of'] );

		if ( false === $target_control_index ) {
			if ( ! empty( $position['fallback'] ) ) {
				return $this->get_position_info( $position['fallback'] );
			}

			return false;
		}

		$target_section_index = $target_control_index;

		$registered_controls = $this->get_controls();

		$controls_keys = array_keys( $registered_controls );

		while ( Controls_Manager::SECTION !== $registered_controls[ $controls_keys[ $target_section_index ] ]['type'] ) {
			--$target_section_index;
		}

		if ( 'section' === $position['type'] ) {
			++$target_control_index;

			if ( 'end' === $position['at'] ) {
				while ( Controls_Manager::SECTION !== $registered_controls[ $controls_keys[ $target_control_index ] ]['type'] ) {
					if ( ++$target_control_index >= count( $registered_controls ) ) {
						break;
					}
				}
			}
		}

		$target_control = $registered_controls[ $controls_keys[ $target_control_index ] ];

		if ( 'after' === $position['at'] ) {
			++$target_control_index;
		}

		$section_id = $registered_controls[ $controls_keys[ $target_section_index ] ]['name'];

		$position_info = [
			'index' => $target_control_index,
			'section' => $this->get_section_args( $section_id ),
		];

		if ( ! empty( $target_control['tabs_wrapper'] ) ) {
			$position_info['tab'] = [
				'tabs_wrapper' => $target_control['tabs_wrapper'],
				'inner_tab' => $target_control['inner_tab'],
			];
		}

		return $position_info;
	}

	/**
	 * Get control key.
	 *
	 * Retrieve the key of the control based on a given index of the control.
	 *
	 * @since 1.9.2
	 * @access public
	 *
	 * @param string $control_index Control index.
	 *
	 * @return int Control key.
	 */
	final public function get_control_key( $control_index ) {
		$registered_controls = $this->get_controls();

		$controls_keys = array_keys( $registered_controls );

		return $controls_keys[ $control_index ];
	}

	/**
	 * Get control index.
	 *
	 * Retrieve the index of the control based on a given key of the control.
	 *
	 * @since 1.7.6
	 * @access public
	 *
	 * @param string $control_key Control key.
	 *
	 * @return false|int Control index.
	 */
	final public function get_control_index( $control_key ) {
		$controls = $this->get_controls();

		$controls_keys = array_keys( $controls );

		return array_search( $control_key, $controls_keys );
	}

	/**
	 * Get section controls.
	 *
	 * Retrieve all controls under a specific section.
	 *
	 * @since 1.7.6
	 * @access public
	 *
	 * @param string $section_id Section ID.
	 *
	 * @return array Section controls
	 */
	final public function get_section_controls( $section_id ) {
		$section_index = $this->get_control_index( $section_id );

		$section_controls = [];

		$registered_controls = $this->get_controls();

		$controls_keys = array_keys( $registered_controls );

		while ( true ) {
			++$section_index;

			if ( ! isset( $controls_keys[ $section_index ] ) ) {
				break;
			}

			$control_key = $controls_keys[ $section_index ];

			if ( Controls_Manager::SECTION === $registered_controls[ $control_key ]['type'] ) {
				break;
			}

			$section_controls[ $control_key ] = $registered_controls[ $control_key ];
		}

		return $section_controls;
	}

	/**
	 * Add new group control to stack.
	 *
	 * Register a set of related controls grouped together as a single unified
	 * control. For example grouping together like typography controls into a
	 * single, easy-to-use control.
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @param string $group_name Group control name.
	 * @param array  $args       Group control arguments. Default is an empty array.
	 * @param array  $options    Optional. Group control options. Default is an
	 *                           empty array.
	 */
	final public function add_group_control( $group_name, array $args = [], array $options = [] ) {
		$group = Plugin::$instance->controls_manager->get_control_groups( $group_name );

		if ( ! $group ) {
			wp_die( sprintf( '%s::%s: Group "%s" not found.', get_called_class(), __FUNCTION__, $group_name ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		$group->add_controls( $this, $args, $options );
	}

	/**
	 * Get style controls.
	 *
	 * Retrieve style controls for all active controls or, when requested, from
	 * a specific set of controls.
	 *
	 * @since 1.4.0
	 * @since 2.0.9 Added the `settings` parameter.
	 * @access public
	 * @deprecated 3.0.0
	 *
	 * @param array $controls Optional. Controls list. Default is null.
	 * @param array $settings Optional. Controls settings. Default is null.
	 *
	 * @return array Style controls.
	 */
	final public function get_style_controls( ?array $controls = null, ?array $settings = null ) {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.0.0' );

		$controls = $this->get_active_controls( $controls, $settings );

		$style_controls = [];

		foreach ( $controls as $control_name => $control ) {
			$control_obj = Plugin::$instance->controls_manager->get_control( $control['type'] );

			if ( ! $control_obj instanceof Base_Data_Control ) {
				continue;
			}

			$control = array_merge( $control_obj->get_settings(), $control );

			if ( $control_obj instanceof Control_Repeater ) {
				$style_fields = [];

				foreach ( $this->get_settings( $control_name ) as $item ) {
					$style_fields[] = $this->get_style_controls( $control['fields'], $item );
				}

				$control['style_fields'] = $style_fields;
			}

			if ( ! empty( $control['selectors'] ) || ! empty( $control['dynamic'] ) || ! empty( $control['style_fields'] ) ) {
				$style_controls[ $control_name ] = $control;
			}
		}

		return $style_controls;
	}

	/**
	 * Get tabs controls.
	 *
	 * Retrieve all the tabs assigned to the control.
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @return array Tabs controls.
	 */
	final public function get_tabs_controls() {
		return $this->get_stack()['tabs'];
	}

	/**
	 * Add new responsive control to stack.
	 *
	 * Register a set of controls to allow editing based on user screen size.
	 * This method registers one or more controls per screen size/device, depending on the current Responsive Control
	 * Duplication Mode. There are 3 control duplication modes:
	 * * 'off' - Only a single control is generated. In the Editor, this control is duplicated in JS.
	 * * 'on' - Multiple controls are generated, one control per enabled device/breakpoint + a default/desktop control.
	 * * 'dynamic' - If the control includes the `'dynamic' => 'active' => true` property - the control is duplicated,
	 *               once for each device/breakpoint + default/desktop.
	 *               If the control doesn't include the `'dynamic' => 'active' => true` property - the control is not duplicated.
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @param string $id      Responsive control ID.
	 * @param array  $args    Responsive control arguments.
	 * @param array  $options Optional. Responsive control options. Default is
	 *                        an empty array.
	 */
	final public function add_responsive_control( $id, array $args, $options = [] ) {
		$args['responsive'] = [];

		$active_breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();

		$devices = Plugin::$instance->breakpoints->get_active_devices_list( [
			'reverse' => true,
			'desktop_first' => true,
		] );

		if ( isset( $args['devices'] ) ) {
			$devices = array_intersect( $devices, $args['devices'] );

			$args['responsive']['devices'] = $devices;

			unset( $args['devices'] );
		}

		$control_to_check = $args;

		if ( ! empty( $options['overwrite'] ) ) {
			$existing_control = Plugin::$instance->controls_manager->get_control_from_stack( $this->get_unique_name(), $id );

			if ( ! is_wp_error( $existing_control ) ) {
				$control_to_check = $existing_control;
			}
		}

		$responsive_duplication_mode = Plugin::$instance->breakpoints->get_responsive_control_duplication_mode();
		$additional_breakpoints_active = Plugin::$instance->experiments->is_feature_active( 'additional_custom_breakpoints' );
		$control_is_dynamic = ! empty( $control_to_check['dynamic']['active'] );
		$is_frontend_available = ! empty( $control_to_check['frontend_available'] );
		$has_prefix_class = ! empty( $control_to_check['prefix_class'] );

		// If the new responsive controls experiment is active, create only one control - duplicates per device will
		// be created in JS in the Editor.
		if (
			$additional_breakpoints_active
			&& ( 'off' === $responsive_duplication_mode || ( 'dynamic' === $responsive_duplication_mode && ! $control_is_dynamic ) )
			// Some responsive controls need responsive settings to be available to the widget handler, even when empty.
			&& ! $is_frontend_available
			&& ! $has_prefix_class
		) {
			$args['is_responsive'] = true;

			if ( ! empty( $options['overwrite'] ) ) {
				$this->update_control( $id, $args, [
					'recursive' => ! empty( $options['recursive'] ),
				] );
			} else {
				$this->add_control( $id, $args, $options );
			}

			return;
		}

		if ( isset( $args['default'] ) ) {
			$args['desktop_default'] = $args['default'];

			unset( $args['default'] );
		}

		foreach ( $devices as $device_name ) {
			$control_args = $args;

			// Set parent using the name from previous iteration.
			if ( isset( $control_name ) ) {
				// If $control_name end with _widescreen use desktop name instead.
				$control_args['parent'] = '_widescreen' === substr( $control_name, -strlen( '_widescreen' ) ) ? $id : $control_name;
			} else {
				$control_args['parent'] = null;
			}

			if ( isset( $control_args['device_args'] ) ) {
				if ( ! empty( $control_args['device_args'][ $device_name ] ) ) {
					$control_args = array_merge( $control_args, $control_args['device_args'][ $device_name ] );
				}

				unset( $control_args['device_args'] );
			}

			if ( ! empty( $args['prefix_class'] ) ) {
				$device_to_replace = Breakpoints_Manager::BREAKPOINT_KEY_DESKTOP === $device_name ? '' : '-' . $device_name;

				$control_args['prefix_class'] = sprintf( $args['prefix_class'], $device_to_replace );
			}

			$direction = 'max';

			if ( Breakpoints_Manager::BREAKPOINT_KEY_DESKTOP !== $device_name ) {
				$direction = $active_breakpoints[ $device_name ]->get_direction();
			}

			$control_args['responsive'][ $direction ] = $device_name;

			if ( isset( $control_args['min_affected_device'] ) ) {
				if ( ! empty( $control_args['min_affected_device'][ $device_name ] ) ) {
					$control_args['responsive']['min'] = $control_args['min_affected_device'][ $device_name ];
				}

				unset( $control_args['min_affected_device'] );
			}

			if ( isset( $control_args[ $device_name . '_default' ] ) ) {
				$control_args['default'] = $control_args[ $device_name . '_default' ];
			}

			foreach ( $devices as $device ) {
				unset( $control_args[ $device . '_default' ] );
			}

			$id_suffix = Breakpoints_Manager::BREAKPOINT_KEY_DESKTOP === $device_name ? '' : '_' . $device_name;
			$control_name = $id . $id_suffix;

			// Set this control as child of previous iteration control.
			if ( ! empty( $control_args['parent'] ) ) {
				$this->update_control( $control_args['parent'], [ 'inheritors' => [ $control_name ] ] );
			}

			if ( ! empty( $options['overwrite'] ) ) {
				$this->update_control( $control_name, $control_args, [
					'recursive' => ! empty( $options['recursive'] ),
				] );
			} else {
				$this->add_control( $control_name, $control_args, $options );
			}
		}
	}

	/**
	 * Update responsive control in stack.
	 *
	 * Change the value of an existing responsive control in the stack. When you
	 * add new control you set the `$args` parameter, this method allows you to
	 * update the arguments by passing new data.
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @param string $id      Responsive control ID.
	 * @param array  $args    Responsive control arguments.
	 * @param array  $options Optional. Additional options.
	 */
	final public function update_responsive_control( $id, array $args, array $options = [] ) {
		$this->add_responsive_control( $id, $args, [
			'overwrite' => true,
			'recursive' => ! empty( $options['recursive'] ),
		] );
	}

	/**
	 * Remove responsive control from stack.
	 *
	 * Unregister an existing responsive control and remove it from the stack.
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @param string $id Responsive control ID.
	 */
	final public function remove_responsive_control( $id ) {
		$devices = Plugin::$instance->breakpoints->get_active_devices_list( [ 'reverse' => true ] );

		foreach ( $devices as $device_name ) {
			$id_suffix = Breakpoints_Manager::BREAKPOINT_KEY_DESKTOP === $device_name ? '' : '_' . $device_name;

			$this->remove_control( $id . $id_suffix );
		}
	}

	/**
	 * Get class name.
	 *
	 * Retrieve the name of the current class.
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @return string Class name.
	 */
	final public function get_class_name() {
		return get_called_class();
	}

	/**
	 * Get the config.
	 *
	 * Retrieve the config or, if non set, use the initial config.
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @return array|null The config.
	 */
	final public function get_config() {
		if ( null === $this->config ) {
			// TODO: This is for backwards compatibility starting from 2.9.0.
			// This if statement should be removed when the method is hard-deprecated.
			if ( $this->has_own_method( '_get_initial_config', self::class ) ) {
				Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( '_get_initial_config', '2.9.0', __CLASS__ . '::get_initial_config()' );

				$this->config = $this->_get_initial_config();
			} else {
				$this->config = $this->get_initial_config();
			}

			foreach ( $this->additional_config as $key => $value ) {
				if ( isset( $this->config[ $key ] ) ) {
					$this->config[ $key ] = wp_parse_args( $value, $this->config[ $key ] );
				} else {
					$this->config[ $key ] = $value;
				}
			}
		}

		return $this->config;
	}

	/**
	 * Set a config property.
	 *
	 * Set a specific property of the config list for this controls-stack.
	 *
	 * @param string $key
	 * @param string $value
	 * @since 3.5.0
	 * @access public
	 */
	public function set_config( $key, $value ) {
		if ( isset( $this->additional_config[ $key ] ) ) {
			$this->additional_config[ $key ] = wp_parse_args( $value, $this->additional_config[ $key ] );
		} else {
			$this->additional_config[ $key ] = $value;
		}
	}

	/**
	 * Get frontend settings keys.
	 *
	 * Retrieve settings keys for all frontend controls.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @return array Settings keys for each control.
	 */
	final public function get_frontend_settings_keys() {
		$controls = [];

		foreach ( $this->get_controls() as $control ) {
			if ( ! empty( $control['frontend_available'] ) ) {
				$controls[] = $control['name'];
			}
		}

		return $controls;
	}

	/**
	 * Get controls pointer index.
	 *
	 * Retrieve pointer index where the next control should be added.
	 *
	 * While using injection point, it will return the injection point index.
	 * Otherwise index of the last control plus one.
	 *
	 * @since 1.9.2
	 * @access public
	 *
	 * @return int Controls pointer index.
	 */
	public function get_pointer_index() {
		if ( null !== $this->injection_point ) {
			return $this->injection_point['index'];
		}

		return count( $this->get_controls() );
	}

	/**
	 * Get the raw data.
	 *
	 * Retrieve all the items or, when requested, a specific item.
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @param string $item Optional. The requested item. Default is null.
	 *
	 * @return mixed The raw data.
	 */
	public function get_data( $item = null ) {
		if ( ! $this->settings_sanitized && ( ! $item || 'settings' === $item ) ) {
			$this->data['settings'] = $this->sanitize_settings( $this->data['settings'] );

			$this->settings_sanitized = true;
		}

		return self::get_items( $this->data, $item );
	}

	/**
	 * @param null $setting
	 * @param null $settings
	 * @return array|mixed|null
	 * @since 2.0.14
	 * @access public
	 */
	public function get_parsed_dynamic_settings( $setting = null, $settings = null ) {
		if ( null === $settings ) {
			$settings = $this->get_settings();
		}

		if ( null === $this->parsed_dynamic_settings ) {
			$this->parsed_dynamic_settings = $this->parse_dynamic_settings( $settings );
		}

		return self::get_items( $this->parsed_dynamic_settings, $setting );
	}

	/**
	 * Get active settings.
	 *
	 * Retrieve the settings from all the active controls.
	 *
	 * @param array|null $settings Optional. Controls settings. Default is null.
	 * @param array|null $controls Optional. An array of controls. Default is null.
	 *
	 * @return array Active settings.
	 * @since 2.1.0 Added the `controls` and the `settings` parameters.
	 * @access public
	 *
	 * @since 1.4.0
	 */
	public function get_active_settings( $settings = null, $controls = null ) {
		$is_first_request = ! $settings && ! $this->active_settings;

		if ( ! $settings ) {
			if ( $this->active_settings ) {
				return $this->active_settings;
			}

			$settings = $this->get_controls_settings();

			$controls = $this->get_controls();
		}

		$active_settings = [];

		$controls_objs = Plugin::$instance->controls_manager->get_controls();

		foreach ( $settings as $setting_key => $setting ) {
			if ( ! isset( $controls[ $setting_key ] ) ) {
				$active_settings[ $setting_key ] = $setting;

				continue;
			}

			$control = $controls[ $setting_key ];

			if ( $this->is_control_visible( $control, $settings, $controls ) ) {
				$control_obj = $controls_objs[ $control['type'] ] ?? null;

				if ( $control_obj instanceof Control_Repeater ) {
					foreach ( $setting as & $item ) {
						$item = $this->get_active_settings( $item, $control['fields'] );
					}
				}

				$active_settings[ $setting_key ] = $setting;
			} else {
				$active_settings[ $setting_key ] = null;
			}
		}

		if ( $is_first_request ) {
			$this->active_settings = $active_settings;
		}

		return $active_settings;
	}

	/**
	 * Get settings for display.
	 *
	 * Retrieve all the settings or, when requested, a specific setting for display.
	 *
	 * Unlike `get_settings()` method, this method retrieves only active settings
	 * that passed all the conditions, rendered all the shortcodes and all the dynamic
	 * tags.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param string $setting_key Optional. The key of the requested setting.
	 *                            Default is null.
	 *
	 * @return mixed The settings.
	 */
	public function get_settings_for_display( $setting_key = null ) {
		if ( ! $this->parsed_active_settings ) {
			$this->parsed_active_settings = $this->get_active_settings( $this->get_parsed_dynamic_settings(), $this->get_controls() );
		}

		return self::get_items( $this->parsed_active_settings, $setting_key );
	}

	/**
	 * Parse dynamic settings.
	 *
	 * Retrieve the settings with rendered dynamic tags.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param array $settings     Optional. The requested setting. Default is null.
	 * @param array $controls     Optional. The controls array. Default is null.
	 * @param array $all_settings Optional. All the settings. Default is null.
	 *
	 * @return array The settings with rendered dynamic tags.
	 */
	public function parse_dynamic_settings( $settings, $controls = null, $all_settings = null ) {
		if ( null === $all_settings ) {
			$all_settings = $this->get_settings();
		}

		if ( null === $controls ) {
			$controls = $this->get_controls();
		}

		$controls_objs = Plugin::$instance->controls_manager->get_controls();

		foreach ( $controls as $control ) {
			$control_name = $control['name'];
			$control_obj = $controls_objs[ $control['type'] ] ?? null;

			if ( ! $control_obj instanceof Base_Data_Control ) {
				continue;
			}

			if ( $control_obj instanceof Control_Repeater ) {
				if ( ! isset( $settings[ $control_name ] ) ) {
					continue;
				}

				foreach ( $settings[ $control_name ] as & $field ) {
					$field = $this->parse_dynamic_settings( $field, $control['fields'], $field );
				}

				continue;
			}

			$dynamic_settings = $control_obj->get_settings( 'dynamic' );

			if ( ! $dynamic_settings ) {
				$dynamic_settings = [];
			}

			if ( ! empty( $control['dynamic'] ) ) {
				$dynamic_settings = array_merge( $dynamic_settings, $control['dynamic'] );
			}

			if ( empty( $dynamic_settings ) || ! isset( $all_settings[ Manager::DYNAMIC_SETTING_KEY ][ $control_name ] ) ) {
				continue;
			}

			if ( ! empty( $dynamic_settings['active'] ) && ! empty( $all_settings[ Manager::DYNAMIC_SETTING_KEY ][ $control_name ] ) ) {
				$parsed_value = $control_obj->parse_tags( $all_settings[ Manager::DYNAMIC_SETTING_KEY ][ $control_name ], $dynamic_settings );

				$dynamic_property = ! empty( $dynamic_settings['property'] ) ? $dynamic_settings['property'] : null;

				if ( $dynamic_property ) {
					$settings[ $control_name ][ $dynamic_property ] = $parsed_value;
				} else {
					$settings[ $control_name ] = $parsed_value;
				}
			}
		}

		return $settings;
	}

	/**
	 * Get frontend settings.
	 *
	 * Retrieve the settings for all frontend controls.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @return array Frontend settings.
	 */
	public function get_frontend_settings() {
		$frontend_settings = array_intersect_key( $this->get_settings_for_display(), array_flip( $this->get_frontend_settings_keys() ) );

		foreach ( $frontend_settings as $key => $setting ) {
			if ( in_array( $setting, [ null, '' ], true ) ) {
				unset( $frontend_settings[ $key ] );
			}
		}

		return $frontend_settings;
	}

	/**
	 * Filter controls settings.
	 *
	 * Receives controls, settings and a callback function to filter the settings by
	 * and returns filtered settings.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param callable $callback The callback function.
	 * @param array    $settings Optional. Control settings. Default is an empty
	 *                           array.
	 * @param array    $controls Optional. Controls list. Default is an empty
	 *                           array.
	 *
	 * @return array Filtered settings.
	 */
	public function filter_controls_settings( callable $callback, array $settings = [], array $controls = [] ) {
		if ( ! $settings ) {
			$settings = $this->get_settings();
		}

		if ( ! $controls ) {
			$controls = $this->get_controls();
		}

		return array_reduce(
			array_keys( $settings ), function( $filtered_settings, $setting_key ) use ( $controls, $settings, $callback ) {
				if ( isset( $controls[ $setting_key ] ) ) {
					$result = $callback( $settings[ $setting_key ], $controls[ $setting_key ] );

					if ( null !== $result ) {
						$filtered_settings[ $setting_key ] = $result;
					}
				}

				return $filtered_settings;
			}, []
		);
	}

	/**
	 * Get Responsive Control Device Suffix
	 *
	 * @deprecated 3.7.6 Use `Elementor\Controls_Manager::get_responsive_control_device_suffix()` instead.
	 * @param array $control
	 * @return string $device suffix
	 */
	protected function get_responsive_control_device_suffix( $control ) {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.7.6', 'Elementor\Controls_Manager::get_responsive_control_device_suffix()' );

		return Controls_Manager::get_responsive_control_device_suffix( $control );
	}

	/**
	 * Whether the control is visible or not.
	 *
	 * Used to determine whether the control is visible or not.
	 *
	 * @param array $control The control.
	 * @param null  $values Optional. Condition values. Default is null.
	 * @param null  $controls
	 * @return bool Whether the control is visible.
	 * @since 1.4.0
	 * @access public
	 */
	public function is_control_visible( $control, $values = null, $controls = null ) {
		if ( null === $values ) {
			$values = $this->get_settings();
		}

		if ( ! empty( $control['conditions'] ) && ! Conditions::check( $control['conditions'], $values ) ) {
			return false;
		}

		if ( empty( $control['condition'] ) ) {
			return true;
		}

		if ( ! $controls ) {
			$controls = $this->get_controls();
		}

		foreach ( $control['condition'] as $condition_key => $condition_value ) {
			preg_match( '/([a-z_\-0-9]+)(?:\[([a-z_]+)])?(!?)$/i', $condition_key, $condition_key_parts );

			$pure_condition_key = $condition_key_parts[1];
			$condition_sub_key = $condition_key_parts[2];
			$is_negative_condition = (bool) $condition_key_parts[3];

			if ( ! isset( $values[ $pure_condition_key ] ) || null === $values[ $pure_condition_key ] ) {
				return false;
			}

			$are_control_and_condition_responsive = isset( $control['responsive'] ) && ! empty( $controls[ $pure_condition_key ]['responsive'] );
			$condition_name_to_check = $pure_condition_key;

			if ( $are_control_and_condition_responsive ) {
				$device_suffix = Controls_Manager::get_responsive_control_device_suffix( $control );

				$condition_name_to_check = $pure_condition_key . $device_suffix;

				// If the control is not desktop, and a conditioning control for the corresponding device exists, use it.
				$instance_value = $values[ $pure_condition_key . $device_suffix ] ?? $values[ $pure_condition_key ];
			} else {
				$instance_value = $values[ $pure_condition_key ];
			}

			if ( $condition_sub_key && is_array( $instance_value ) ) {
				if ( ! isset( $instance_value[ $condition_sub_key ] ) ) {
					return false;
				}

				$instance_value = $instance_value[ $condition_sub_key ];
			}

			if ( ! $instance_value ) {
				$parent = isset( $controls[ $condition_name_to_check ]['parent'] ) ? $controls[ $condition_name_to_check ]['parent'] : false;

				while ( $parent ) {
					$instance_value = $values[ $parent ];

					if ( $instance_value ) {
						if ( ! is_array( $instance_value ) ) {
							break;
						}

						if ( $condition_sub_key && isset( $instance_value[ $condition_sub_key ] ) ) {
							$instance_value = $instance_value[ $condition_sub_key ];

							if ( '' !== $instance_value ) {
								break;
							}
						}
					}

					$parent = isset( $controls[ $parent ]['parent'] ) ? $controls[ $parent ]['parent'] : false;
				}
			}

			/**
			 * If the $condition_value is a non empty array - check if the $condition_value contains the $instance_value,
			 * If the $instance_value is a non empty array - check if the $instance_value contains the $condition_value
			 * otherwise check if they are equal. ( and give the ability to check if the value is an empty array )
			 */
			if ( is_array( $condition_value ) && ! empty( $condition_value ) ) {
				$is_contains = in_array( $instance_value, $condition_value, true );
			} elseif ( is_array( $instance_value ) && ! empty( $instance_value ) ) {
				$is_contains = in_array( $condition_value, $instance_value, true );
			} else {
				$is_contains = $instance_value === $condition_value;
			}

			if (
				( $is_negative_condition && $is_contains ) ||
				( ! $is_negative_condition && ! $is_contains )
			) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Start controls section.
	 *
	 * Used to add a new section of controls. When you use this method, all the
	 * registered controls from this point will be assigned to this section,
	 * until you close the section using `end_controls_section()` method.
	 *
	 * This method should be used inside `register_controls()`.
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @param string $section_id Section ID.
	 * @param array  $args       Section arguments Optional.
	 */
	public function start_controls_section( $section_id, array $args = [] ) {
		$stack_name = $this->get_name();

		/**
		 * Before section start.
		 *
		 * Fires before Elementor section starts in the editor panel.
		 *
		 * @since 1.4.0
		 *
		 * @param Controls_Stack $this       The control.
		 * @param string         $section_id Section ID.
		 * @param array          $args       Section arguments.
		 */
		do_action( 'elementor/element/before_section_start', $this, $section_id, $args );

		/**
		 * Before section start.
		 *
		 * Fires before Elementor section starts in the editor panel.
		 *
		 * The dynamic portions of the hook name, `$stack_name` and `$section_id`, refers to the stack name and section ID, respectively.
		 *
		 * @since 1.4.0
		 *
		 * @param Controls_Stack $this The control.
		 * @param array          $args Section arguments.
		 */
		do_action( "elementor/element/{$stack_name}/{$section_id}/before_section_start", $this, $args );

		if ( $this->should_manually_trigger_common_action( $stack_name ) ) {
			do_action( "elementor/element/common/{$section_id}/before_section_start", $this, $args );
		}

		$args['type'] = Controls_Manager::SECTION;

		$this->add_control( $section_id, $args );

		if ( null !== $this->current_section ) {
			wp_die( sprintf( 'Elementor: You can\'t start a section before the end of the previous section "%s".', $this->current_section['section'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		$this->current_section = $this->get_section_args( $section_id );

		if ( $this->injection_point ) {
			$this->injection_point['section'] = $this->current_section;
		}

		/**
		 * After section start.
		 *
		 * Fires after Elementor section starts in the editor panel.
		 *
		 * @since 1.4.0
		 *
		 * @param Controls_Stack $this       The control.
		 * @param string         $section_id Section ID.
		 * @param array          $args       Section arguments.
		 */
		do_action( 'elementor/element/after_section_start', $this, $section_id, $args );

		/**
		 * After section start.
		 *
		 * Fires after Elementor section starts in the editor panel.
		 *
		 * The dynamic portions of the hook name, `$stack_name` and `$section_id`, refers to the stack name and section ID, respectively.
		 *
		 * @since 1.4.0
		 *
		 * @param Controls_Stack $this The control.
		 * @param array          $args Section arguments.
		 */
		do_action( "elementor/element/{$stack_name}/{$section_id}/after_section_start", $this, $args );

		if ( $this->should_manually_trigger_common_action( $stack_name ) ) {
			do_action( "elementor/element/common/{$section_id}/after_section_start", $this, $args );
		}
	}

	/**
	 * End controls section.
	 *
	 * Used to close an existing open controls section. When you use this method
	 * it stops adding new controls to this section.
	 *
	 * This method should be used inside `register_controls()`.
	 *
	 * @since 1.4.0
	 * @access public
	 */
	public function end_controls_section() {
		$stack_name = $this->get_name();

		// Save the current section for the action.
		$current_section = $this->current_section;
		$section_id = $current_section['section'];
		$args = [
			'tab' => $current_section['tab'],
		];

		/**
		 * Before section end.
		 *
		 * Fires before Elementor section ends in the editor panel.
		 *
		 * @since 1.4.0
		 *
		 * @param Controls_Stack $this       The control.
		 * @param string         $section_id Section ID.
		 * @param array          $args       Section arguments.
		 */
		do_action( 'elementor/element/before_section_end', $this, $section_id, $args );

		/**
		 * Before section end.
		 *
		 * Fires before Elementor section ends in the editor panel.
		 *
		 * The dynamic portions of the hook name, `$stack_name` and `$section_id`, refers to the stack name and section ID, respectively.
		 *
		 * @since 1.4.0
		 *
		 * @param Controls_Stack $this The control.
		 * @param array          $args Section arguments.
		 */
		do_action( "elementor/element/{$stack_name}/{$section_id}/before_section_end", $this, $args );

		if ( $this->should_manually_trigger_common_action( $stack_name ) ) {
			do_action( "elementor/element/common/{$section_id}/before_section_end", $this, $args );
		}

		$this->current_section = null;

		/**
		 * After section end.
		 *
		 * Fires after Elementor section ends in the editor panel.
		 *
		 * @since 1.4.0
		 *
		 * @param Controls_Stack $this       The control.
		 * @param string         $section_id Section ID.
		 * @param array          $args       Section arguments.
		 */
		do_action( 'elementor/element/after_section_end', $this, $section_id, $args );

		/**
		 * After section end.
		 *
		 * Fires after Elementor section ends in the editor panel.
		 *
		 * The dynamic portions of the hook name, `$stack_name` and `$section_id`, refers to the stack name and section ID, respectively.
		 *
		 * @since 1.4.0
		 *
		 * @param Controls_Stack $this The control.
		 * @param array          $args Section arguments.
		 */
		do_action( "elementor/element/{$stack_name}/{$section_id}/after_section_end", $this, $args );

		if ( $this->should_manually_trigger_common_action( $stack_name ) ) {
			do_action( "elementor/element/common/{$section_id}/after_section_end", $this, $args );
		}
	}

	/**
	 * Should manually trigger common action.
	 *
	 * With the Optimized Markup experiment, the Advanced Tab has been split to maintain backward compatibility:
	 * - 'common' refers to the existing Advanced Tab.
	 * - 'common-optimized' refers to the new Advanced Tab for optimized widgets.
	 *
	 * Third-party developers may have used hooks like 'elementor/element/common/_section_background/before_section_end'
	 * to add controls to the Advanced Tab. However, this hook will now only work on widgets that are not optimized.
	 *
	 * This method checks whether the 'elementor/element/common/...' hooks should be manually executed
	 * to prevent third parties from needing to add equivalent hooks for 'elementor/element/common-optimized/...'.
	 *
	 * @todo Remove this method and the manual execution of 'common' hooks when the feature is merged.
	 *
	 * @access private
	 *
	 * @param string $stack_name Stack name.
	 *
	 * @return bool
	 */
	private function should_manually_trigger_common_action( $stack_name ): bool {
		return 'common-optimized' === $stack_name && Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	/**
	 * Start controls tabs.
	 *
	 * Used to add a new set of tabs inside a section. You should use this
	 * method before adding new individual tabs using `start_controls_tab()`.
	 * Each tab added after this point will be assigned to this group of tabs,
	 * until you close it using `end_controls_tabs()` method.
	 *
	 * This method should be used inside `register_controls()`.
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @param string $tabs_id Tabs ID.
	 * @param array  $args    Tabs arguments.
	 */
	public function start_controls_tabs( $tabs_id, array $args = [] ) {
		if ( null !== $this->current_tab ) {
			wp_die( sprintf( 'Elementor: You can\'t start tabs before the end of the previous tabs "%s".', $this->current_tab['tabs_wrapper'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		$args['type'] = Controls_Manager::TABS;

		$this->add_control( $tabs_id, $args );

		$this->current_tab = [
			'tabs_wrapper' => $tabs_id,
		];

		foreach ( [ 'condition', 'conditions' ] as $key ) {
			if ( ! empty( $args[ $key ] ) ) {
				$this->current_tab[ $key ] = $args[ $key ];
			}
		}

		if ( $this->injection_point ) {
			$this->injection_point['tab'] = $this->current_tab;
		}
	}

	/**
	 * End controls tabs.
	 *
	 * Used to close an existing open controls tabs. When you use this method it
	 * stops adding new controls to this tabs.
	 *
	 * This method should be used inside `register_controls()`.
	 *
	 * @since 1.4.0
	 * @access public
	 */
	public function end_controls_tabs() {
		$this->current_tab = null;
	}

	/**
	 * Start controls tab.
	 *
	 * Used to add a new tab inside a group of tabs. Use this method before
	 * adding new individual tabs using `start_controls_tab()`.
	 * Each tab added after this point will be assigned to this group of tabs,
	 * until you close it using `end_controls_tab()` method.
	 *
	 * This method should be used inside `register_controls()`.
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @param string $tab_id Tab ID.
	 * @param array  $args   Tab arguments.
	 */
	public function start_controls_tab( $tab_id, $args ) {
		if ( ! empty( $this->current_tab['inner_tab'] ) ) {
			wp_die( sprintf( 'Elementor: You can\'t start a tab before the end of the previous tab "%s".', $this->current_tab['inner_tab'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		$args['type'] = Controls_Manager::TAB;
		$args['tabs_wrapper'] = $this->current_tab['tabs_wrapper'];

		$this->add_control( $tab_id, $args );

		$this->current_tab['inner_tab'] = $tab_id;

		if ( $this->injection_point ) {
			$this->injection_point['tab']['inner_tab'] = $this->current_tab['inner_tab'];
		}
	}

	/**
	 * End controls tab.
	 *
	 * Used to close an existing open controls tab. When you use this method it
	 * stops adding new controls to this tab.
	 *
	 * This method should be used inside `register_controls()`.
	 *
	 * @since 1.4.0
	 * @access public
	 */
	public function end_controls_tab() {
		unset( $this->current_tab['inner_tab'] );
	}

	/**
	 * Start popover.
	 *
	 * Used to add a new set of controls in a popover. When you use this method,
	 * all the registered controls from this point will be assigned to this
	 * popover, until you close the popover using `end_popover()` method.
	 *
	 * This method should be used inside `register_controls()`.
	 *
	 * @since 1.9.0
	 * @access public
	 */
	final public function start_popover() {
		$this->current_popover = [
			'initialized' => false,
		];
	}

	/**
	 * End popover.
	 *
	 * Used to close an existing open popover. When you use this method it stops
	 * adding new controls to this popover.
	 *
	 * This method should be used inside `register_controls()`.
	 *
	 * @since 1.9.0
	 * @access public
	 */
	final public function end_popover() {
		$this->current_popover = null;

		$last_control_key = $this->get_control_key( $this->get_pointer_index() - 1 );

		$args = [
			'popover' => [
				'end' => true,
			],
		];

		$options = [
			'recursive' => true,
		];

		$this->update_control( $last_control_key, $args, $options );
	}

	/**
	 * Add render attribute.
	 *
	 * Used to add attributes to a specific HTML element.
	 *
	 * The HTML tag is represented by the element parameter, then you need to
	 * define the attribute key and the attribute key. The final result will be:
	 * `<element attribute_key="attribute_value">`.
	 *
	 * Example usage:
	 *
	 * `$this->add_render_attribute( 'wrapper', 'class', 'custom-widget-wrapper-class' );`
	 * `$this->add_render_attribute( 'widget', 'id', 'custom-widget-id' );`
	 * `$this->add_render_attribute( 'button', [ 'class' => 'custom-button-class', 'id' => 'custom-button-id' ] );`
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array|string $element   The HTML element.
	 * @param array|string $key       Optional. Attribute key. Default is null.
	 * @param array|string $value     Optional. Attribute value. Default is null.
	 * @param bool         $overwrite Optional. Whether to overwrite existing
	 *                                attribute. Default is false, not to overwrite.
	 *
	 * @return self Current instance of the element.
	 */
	public function add_render_attribute( $element, $key = null, $value = null, $overwrite = false ) {
		if ( is_array( $element ) ) {
			foreach ( $element as $element_key => $attributes ) {
				$this->add_render_attribute( $element_key, $attributes, null, $overwrite );
			}

			return $this;
		}

		if ( is_array( $key ) ) {
			foreach ( $key as $attribute_key => $attributes ) {
				$this->add_render_attribute( $element, $attribute_key, $attributes, $overwrite );
			}

			return $this;
		}

		if ( empty( $this->render_attributes[ $element ][ $key ] ) ) {
			$this->render_attributes[ $element ][ $key ] = [];
		}

		settype( $value, 'array' );

		if ( $overwrite ) {
			$this->render_attributes[ $element ][ $key ] = $value;
		} else {
			$this->render_attributes[ $element ][ $key ] = array_merge( $this->render_attributes[ $element ][ $key ], $value );
		}

		return $this;
	}

	/**
	 * Get Render Attributes
	 *
	 * Used to retrieve render attribute.
	 *
	 * The returned array is either all elements and their attributes if no `$element` is specified, an array of all
	 * attributes of a specific element or a specific attribute properties if `$key` is specified.
	 *
	 * Returns null if one of the requested parameters isn't set.
	 *
	 * @since 2.2.6
	 * @access public
	 * @param string $element
	 * @param string $key
	 *
	 * @return array
	 */
	public function get_render_attributes( $element = '', $key = '' ) {
		$attributes = $this->render_attributes;

		if ( $element ) {
			if ( ! isset( $attributes[ $element ] ) ) {
				return null;
			}

			$attributes = $attributes[ $element ];

			if ( $key ) {
				if ( ! isset( $attributes[ $key ] ) ) {
					return null;
				}

				$attributes = $attributes[ $key ];
			}
		}

		return $attributes;
	}

	/**
	 * Set render attribute.
	 *
	 * Used to set the value of the HTML element render attribute or to update
	 * an existing render attribute.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array|string $element The HTML element.
	 * @param array|string $key     Optional. Attribute key. Default is null.
	 * @param array|string $value   Optional. Attribute value. Default is null.
	 *
	 * @return self Current instance of the element.
	 */
	public function set_render_attribute( $element, $key = null, $value = null ) {
		return $this->add_render_attribute( $element, $key, $value, true );
	}

	/**
	 * Remove render attribute.
	 *
	 * Used to remove an element (with its keys and their values), key (with its values),
	 * or value/s from an HTML element's render attribute.
	 *
	 * @since 2.7.0
	 * @access public
	 *
	 * @param string       $element       The HTML element.
	 * @param string       $key           Optional. Attribute key. Default is null.
	 * @param array|string $values   Optional. Attribute value/s. Default is null.
	 */
	public function remove_render_attribute( $element, $key = null, $values = null ) {
		if ( $key && ! isset( $this->render_attributes[ $element ][ $key ] ) ) {
			return;
		}

		if ( $values ) {
			$values = (array) $values;

			$this->render_attributes[ $element ][ $key ] = array_diff( $this->render_attributes[ $element ][ $key ], $values );

			return;
		}

		if ( $key ) {
			unset( $this->render_attributes[ $element ][ $key ] );

			return;
		}

		if ( isset( $this->render_attributes[ $element ] ) ) {
			unset( $this->render_attributes[ $element ] );
		}
	}

	/**
	 * Get render attribute string.
	 *
	 * Used to retrieve the value of the render attribute.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $element The element.
	 *
	 * @return string Render attribute string, or an empty string if the attribute
	 *                is empty or not exist.
	 */
	public function get_render_attribute_string( $element ) {
		if ( empty( $this->render_attributes[ $element ] ) ) {
			return '';
		}

		return Utils::render_html_attributes( $this->render_attributes[ $element ] );
	}

	/**
	 * Print render attribute string.
	 *
	 * Used to output the rendered attribute.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param array|string $element The element.
	 */
	public function print_render_attribute_string( $element ) {
		Utils::print_unescaped_internal_string( $this->get_render_attribute_string( $element ) );
	}

	/**
	 * Print element template.
	 *
	 * Used to generate the element template on the editor.
	 *
	 * @since 2.0.0
	 * @access public
	 */
	public function print_template() {
		ob_start();

		// TODO: This is for backwards compatibility starting from 2.9.0
		// This `if` statement should be removed when the method is removed.
		if ( $this->has_own_method( '_content_template', self::class ) ) {
			Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( '_content_template', '2.9.0', __CLASS__ . '::content_template()' );

			$this->_content_template();
		} else {
			$this->content_template();
		}

		$template_content = ob_get_clean();

		$element_type = $this->get_type();

		/**
		 * Template content.
		 *
		 * Filters the controls stack template content before it's printed in the editor.
		 *
		 * The dynamic portion of the hook name, `$element_type`, refers to the element type.
		 *
		 * @since 1.0.0
		 *
		 * @param string         $content_template The controls stack template in the editor.
		 * @param Controls_Stack $this             The controls stack.
		 */
		$template_content = apply_filters( "elementor/{$element_type}/print_template", $template_content, $this );

		if ( empty( $template_content ) ) {
			return;
		}
		?>
		<script type="text/html" id="tmpl-elementor-<?php echo esc_attr( $this->get_name() ); ?>-content">
			<?php $this->print_template_content( $template_content ); ?>
		</script>
		<?php
	}

	/**
	 * On import update dynamic content (e.g. post and term IDs).
	 *
	 * @since 3.8.0
	 *
	 * @param array      $config   The config of the passed element.
	 * @param array      $data     The data that requires updating/replacement when imported.
	 * @param array|null $controls The available controls.
	 *
	 * @return array Element data.
	 */
	public static function on_import_update_dynamic_content( array $config, array $data, $controls = null ): array {
		return $config;
	}

	/**
	 * Start injection.
	 *
	 * Used to inject controls and sections to a specific position in the stack.
	 *
	 * When you use this method, all the registered controls and sections will
	 * be injected to a specific position in the stack, until you stop the
	 * injection using `end_injection()` method.
	 *
	 * @since 1.7.1
	 * @access public
	 *
	 * @param array $position {
	 *     The position where to start the injection.
	 *
	 *     @type string $type Injection type, either `control` or `section`.
	 *                        Default is `control`.
	 *     @type string $at   Where to inject. If `$type` is `control` accepts
	 *                        `before` and `after`. If `$type` is `section`
	 *                        accepts `start` and `end`. Default values based on
	 *                        the `type`.
	 *     @type string $of   Control/Section ID.
	 * }
	 */
	final public function start_injection( array $position ) {
		if ( $this->injection_point ) {
			wp_die( 'A controls injection is already opened. Please close current injection before starting a new one (use `end_injection`).' );
		}

		$this->injection_point = $this->get_position_info( $position );
	}

	/**
	 * End injection.
	 *
	 * Used to close an existing opened injection point.
	 *
	 * When you use this method it stops adding new controls and sections to
	 * this point and continue to add controls to the regular position in the
	 * stack.
	 *
	 * @since 1.7.1
	 * @access public
	 */
	final public function end_injection() {
		$this->injection_point = null;
	}

	/**
	 * Get injection point.
	 *
	 * Retrieve the injection point in the stack where new controls and sections
	 * will be inserted.
	 *
	 * @since 1.9.2
	 * @access public
	 *
	 * @return array|null An array when an injection point is defined, null
	 *                    otherwise.
	 */
	final public function get_injection_point() {
		return $this->injection_point;
	}

	/**
	 * Register controls.
	 *
	 * Used to add new controls to any element type. For example, external
	 * developers use this method to register controls in a widget.
	 *
	 * Should be inherited and register new controls using `add_control()`,
	 * `add_responsive_control()` and `add_group_control()`, inside control
	 * wrappers like `start_controls_section()`, `start_controls_tabs()` and
	 * `start_controls_tab()`.
	 *
	 * @since 1.4.0
	 * @access protected
	 * @deprecated 3.1.0 Use `register_controls()` method instead.
	 */
	protected function _register_controls() {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.1.0', 'register_controls()' );

		$this->register_controls();
	}

	/**
	 * Register controls.
	 *
	 * Used to add new controls to any element type. For example, external
	 * developers use this method to register controls in a widget.
	 *
	 * Should be inherited and register new controls using `add_control()`,
	 * `add_responsive_control()` and `add_group_control()`, inside control
	 * wrappers like `start_controls_section()`, `start_controls_tabs()` and
	 * `start_controls_tab()`.
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	protected function register_controls() {}

	/**
	 * Get default data.
	 *
	 * Retrieve the default data. Used to reset the data on initialization.
	 *
	 * @since 1.4.0
	 * @access protected
	 *
	 * @return array Default data.
	 */
	protected function get_default_data() {
		return [
			'id' => 0,
			'settings' => [],
		];
	}

	/**
	 * @since 2.3.0
	 * @access protected
	 */
	protected function get_init_settings() {
		$settings = $this->get_data( 'settings' );

		$controls_objs = Plugin::$instance->controls_manager->get_controls();

		foreach ( $this->get_controls() as $control ) {
			$control_obj = $controls_objs[ $control['type'] ] ?? null;

			if ( ! $control_obj instanceof Base_Data_Control ) {
				continue;
			}

			$control = array_merge_recursive( $control_obj->get_settings(), $control );

			$settings[ $control['name'] ] = $control_obj->get_value( $control, $settings );
		}

		return $settings;
	}

	/**
	 * Get initial config.
	 *
	 * Retrieve the current element initial configuration - controls list and
	 * the tabs assigned to the control.
	 *
	 * @since 2.9.0
	 * @access protected
	 *
	 * @return array The initial config.
	 */
	protected function get_initial_config() {
		return [
			'controls' => $this->get_controls(),
		];
	}

	/**
	 * Get initial config.
	 *
	 * Retrieve the current element initial configuration - controls list and
	 * the tabs assigned to the control.
	 *
	 * @since 1.4.0
	 * @deprecated 2.9.0 Use `get_initial_config()` method instead.
	 * @access protected
	 *
	 * @return array The initial config.
	 */
	protected function _get_initial_config() {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '2.9.0', 'get_initial_config()' );

		return $this->get_initial_config();
	}

	/**
	 * Get section arguments.
	 *
	 * Retrieve the section arguments based on section ID.
	 *
	 * @since 1.4.0
	 * @access protected
	 *
	 * @param string $section_id Section ID.
	 *
	 * @return array Section arguments.
	 */
	protected function get_section_args( $section_id ) {
		$section_control = $this->get_controls( $section_id );

		$section_args_keys = [ 'tab', 'condition' ];

		$args = array_intersect_key( $section_control, array_flip( $section_args_keys ) );

		$args['section'] = $section_id;

		return $args;
	}

	/**
	 * Render element.
	 *
	 * Generates the final HTML on the frontend.
	 *
	 * @since 2.0.0
	 * @access protected
	 */
	protected function render() {}

	/**
	 * Render element in static mode.
	 *
	 * If not inherent will call the base render.
	 */
	protected function render_static() {
		$this->render();
	}

	/**
	 * Determine the render logic.
	 */
	protected function render_by_mode() {
		if ( Plugin::$instance->frontend->is_static_render_mode() ) {
			$this->render_static();

			return;
		}

		$this->render();
	}

	/**
	 * Print content template.
	 *
	 * Used to generate the content template on the editor, using a
	 * Backbone JavaScript template.
	 *
	 * @access protected
	 * @since 2.0.0
	 *
	 * @param string $template_content Template content.
	 */
	protected function print_template_content( $template_content ) {
		Utils::print_unescaped_internal_string( $template_content );
	}

	/**
	 * Render element output in the editor.
	 *
	 * Used to generate the live preview, using a Backbone JavaScript template.
	 *
	 * @since 2.9.0
	 * @access protected
	 */
	protected function content_template() {}

	/**
	 * Render element output in the editor.
	 *
	 * Used to generate the live preview, using a Backbone JavaScript template.
	 *
	 * @since 2.0.0
	 * @deprecated 2.9.0 Use `content_template()` method instead.
	 * @access protected
	 */
	protected function _content_template() {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '2.9.0', 'content_template()' );

		$this->content_template();
	}

	/**
	 * Initialize controls.
	 *
	 * Register the all controls added by `register_controls()`.
	 *
	 * @since 2.0.0
	 * @access protected
	 */
	protected function init_controls() {
		Plugin::$instance->controls_manager->open_stack( $this );

		// TODO: This is for backwards compatibility starting from 2.9.0
		// This `if` statement should be removed when the method is removed.
		if ( $this->has_own_method( '_register_controls', self::class ) ) {
			Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( '_register_controls', '3.1.0', __CLASS__ . '::register_controls()' );

			$this->_register_controls();
		} else {
			$this->register_controls();
		}
	}

	protected function handle_control_position( array $args, $control_id, $overwrite ) {
		if ( isset( $args['type'] ) && in_array( $args['type'], [ Controls_Manager::SECTION, Controls_Manager::WP_WIDGET ], true ) ) {
			return $args;
		}

		$target_section_args = $this->current_section;

		$target_tab = $this->current_tab;

		if ( $this->injection_point ) {
			$target_section_args = $this->injection_point['section'];

			if ( ! empty( $this->injection_point['tab'] ) ) {
				$target_tab = $this->injection_point['tab'];
			}
		}

		if ( null !== $target_section_args ) {
			if ( ! empty( $args['section'] ) || ! empty( $args['tab'] ) ) {
				_doing_it_wrong( sprintf( '%s::%s', get_called_class(), __FUNCTION__ ), sprintf( 'Cannot redeclare control with `tab` or `section` args inside section "%s".', $control_id ), '1.0.0' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			$args = array_replace_recursive( $target_section_args, $args );

			if ( null !== $target_tab ) {
				$args = array_replace_recursive( $target_tab, $args );
			}
		} elseif ( empty( $args['section'] ) && ( ! $overwrite || is_wp_error( Plugin::$instance->controls_manager->get_control_from_stack( $this->get_unique_name(), $control_id ) ) ) ) {
			if ( ! Performance::should_optimize_controls() ) {
				wp_die( sprintf( '%s::%s: Cannot add a control outside of a section (use `start_controls_section`).', get_called_class(), __FUNCTION__ ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		return $args;
	}

	/**
	 * Initialize the class.
	 *
	 * Set the raw data, the ID and the parsed settings.
	 *
	 * @since 2.9.0
	 * @access protected
	 *
	 * @param array $data Initial data.
	 */
	protected function init( $data ) {
		$this->data = array_merge( $this->get_default_data(), $data );

		$this->id = $data['id'];
	}

	/**
	 * Initialize the class.
	 *
	 * Set the raw data, the ID and the parsed settings.
	 *
	 * @since 1.4.0
	 * @deprecated 2.9.0 Use `init()` method instead.
	 * @access protected
	 *
	 * @param array $data Initial data.
	 */
	protected function _init( $data ) {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '2.9.0', 'init()' );

		$this->init( $data );
	}

	/**
	 * Sanitize initial data.
	 *
	 * Performs settings cleaning and sanitization.
	 *
	 * @since 2.1.5
	 * @access private
	 *
	 * @param array $settings Settings to sanitize.
	 * @param array $controls Optional. An array of controls. Default is an
	 *                        empty array.
	 *
	 * @return array Sanitized settings.
	 */
	private function sanitize_settings( array $settings, array $controls = [] ) {
		if ( ! $controls ) {
			$controls = $this->get_controls();
		}

		foreach ( $controls as $control ) {
			$control_obj = Plugin::$instance->controls_manager->get_control( $control['type'] );

			if ( $control_obj instanceof Control_Repeater ) {
				if ( empty( $settings[ $control['name'] ] ) ) {
					continue;
				}

				foreach ( $settings[ $control['name'] ] as $index => $repeater_row_data ) {
					$sanitized_row_data = $this->sanitize_settings( $repeater_row_data, $control['fields'] );

					$settings[ $control['name'] ][ $index ] = $sanitized_row_data;
				}

				continue;
			}

			$is_dynamic = isset( $settings[ Manager::DYNAMIC_SETTING_KEY ][ $control['name'] ] );

			if ( ! $is_dynamic ) {
				continue;
			}

			$value_to_check = $settings[ Manager::DYNAMIC_SETTING_KEY ][ $control['name'] ];

			$tag_text_data = Plugin::$instance->dynamic_tags->tag_text_to_tag_data( $value_to_check );

			if ( ! Plugin::$instance->dynamic_tags->get_tag_info( $tag_text_data['name'] ) ) {
				unset( $settings[ Manager::DYNAMIC_SETTING_KEY ][ $control['name'] ] );
			}
		}

		return $settings;
	}

	/**
	 * Controls stack constructor.
	 *
	 * Initializing the control stack class using `$data`. The `$data` is required
	 * for a normal instance. It is optional only for internal `type instance`.
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @param array $data Optional. Control stack data. Default is an empty array.
	 */
	public function __construct( array $data = [] ) {
		if ( $data ) {
			// TODO: This is for backwards compatibility starting from 2.9.0
			// This if statement should be removed when the method is hard-deprecated.
			if ( $this->has_own_method( '_init', self::class ) ) {
				Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( '_init', '2.9.0', __CLASS__ . '::init()' );

				$this->_init( $data );
			} else {
				$this->init( $data );
			}
		}
	}
}
