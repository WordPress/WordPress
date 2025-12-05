<?php
namespace Elementor;

use Elementor\Core\Frontend\Performance;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor controls manager.
 *
 * Elementor controls manager handler class is responsible for registering and
 * initializing all the supported controls, both regular controls and the group
 * controls.
 *
 * @since 1.0.0
 */
class Controls_Manager {

	/**
	 * Content tab.
	 */
	const TAB_CONTENT = 'content';

	/**
	 * Style tab.
	 */
	const TAB_STYLE = 'style';

	/**
	 * Advanced tab.
	 */
	const TAB_ADVANCED = 'advanced';

	/**
	 * Responsive tab.
	 */
	const TAB_RESPONSIVE = 'responsive';

	/**
	 * Layout tab.
	 */
	const TAB_LAYOUT = 'layout';

	/**
	 * Settings tab.
	 */
	const TAB_SETTINGS = 'settings';

	/**
	 * Text control.
	 */
	const TEXT = 'text';

	/**
	 * Number control.
	 */
	const NUMBER = 'number';

	/**
	 * Textarea control.
	 */
	const TEXTAREA = 'textarea';

	/**
	 * Select control.
	 */
	const SELECT = 'select';

	/**
	 * Switcher control.
	 */
	const SWITCHER = 'switcher';

	/**
	 * Button control.
	 */
	const BUTTON = 'button';

	/**
	 * Hidden control.
	 */
	const HIDDEN = 'hidden';

	/**
	 * Heading control.
	 */
	const HEADING = 'heading';

	/**
	 * Raw HTML control.
	 */
	const RAW_HTML = 'raw_html';

	/**
	 * Notice control.
	 */
	const NOTICE = 'notice';

	/**
	 * Deprecated Notice control.
	 */
	const DEPRECATED_NOTICE = 'deprecated_notice';

	/**
	 * Alert control.
	 */
	const ALERT = 'alert';

	/**
	 * Popover Toggle control.
	 */
	const POPOVER_TOGGLE = 'popover_toggle';

	/**
	 * Section control.
	 */
	const SECTION = 'section';

	/**
	 * Tab control.
	 */
	const TAB = 'tab';

	/**
	 * Tabs control.
	 */
	const TABS = 'tabs';

	/**
	 * Divider control.
	 */
	const DIVIDER = 'divider';

	/**
	 * Color control.
	 */
	const COLOR = 'color';

	/**
	 * Media control.
	 */
	const MEDIA = 'media';

	/**
	 * Slider control.
	 */
	const SLIDER = 'slider';

	/**
	 * Dimensions control.
	 */
	const DIMENSIONS = 'dimensions';

	/**
	 * Choose control.
	 */
	const CHOOSE = 'choose';

	/**
	 * Visual_Choice control.
	 */
	const VISUAL_CHOICE = 'visual_choice';

	/**
	 * WYSIWYG control.
	 */
	const WYSIWYG = 'wysiwyg';

	/**
	 * Code control.
	 */
	const CODE = 'code';

	/**
	 * Font control.
	 */
	const FONT = 'font';

	/**
	 * Image dimensions control.
	 */
	const IMAGE_DIMENSIONS = 'image_dimensions';

	/**
	 * WordPress widget control.
	 */
	const WP_WIDGET = 'wp_widget';

	/**
	 * URL control.
	 */
	const URL = 'url';

	/**
	 * Repeater control.
	 */
	const REPEATER = 'repeater';

	/**
	 * Icon control.
	 */
	const ICON = 'icon';

	/**
	 * Icons control.
	 */
	const ICONS = 'icons';

	/**
	 * Gallery control.
	 */
	const GALLERY = 'gallery';

	/**
	 * Structure control.
	 */
	const STRUCTURE = 'structure';

	/**
	 * Select2 control.
	 */
	const SELECT2 = 'select2';

	/**
	 * Date/Time control.
	 */
	const DATE_TIME = 'date_time';

	/**
	 * Box shadow control.
	 */
	const BOX_SHADOW = 'box_shadow';

	/**
	 * Text shadow control.
	 */
	const TEXT_SHADOW = 'text_shadow';

	/**
	 * Entrance animation control.
	 */
	const ANIMATION = 'animation';

	/**
	 * Hover animation control.
	 */
	const HOVER_ANIMATION = 'hover_animation';

	/**
	 * Exit animation control.
	 */
	const EXIT_ANIMATION = 'exit_animation';

	/**
	 * Gaps control.
	 */
	const GAPS = 'gaps';

	/**
	 * Controls.
	 *
	 * Holds the list of all the controls. Default is `null`.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var Base_Control[]
	 */
	private $controls = null;

	/**
	 * Control groups.
	 *
	 * Holds the list of all the control groups. Default is an empty array.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var Group_Control_Base[]
	 */
	private $control_groups = [];

	/**
	 * Control stacks.
	 *
	 * Holds the list of all the control stacks. Default is an empty array.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var array
	 */
	private $stacks = [];

	/**
	 * Tabs.
	 *
	 * Holds the list of all the tabs.
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 *
	 * @var array
	 */
	private static $tabs;

	/**
	 * Has stacks cache been cleared.
	 *
	 * Boolean flag used to determine whether the controls manager stack cache has been cleared once during the current runtime.
	 *
	 * @since 3.13.0
	 * @access private
	 * @static
	 *
	 * @var array
	 */
	private $has_stacks_cache_been_cleared = false;

	/**
	 * Init tabs.
	 *
	 * Initialize control tabs.
	 *
	 * @since 1.6.0
	 * @access private
	 * @static
	 */
	private static function init_tabs() {
		self::$tabs = [
			self::TAB_CONTENT => esc_html__( 'Content', 'elementor' ),
			self::TAB_STYLE => esc_html__( 'Style', 'elementor' ),
			self::TAB_ADVANCED => esc_html__( 'Advanced', 'elementor' ),
			self::TAB_RESPONSIVE => esc_html__( 'Responsive', 'elementor' ),
			self::TAB_LAYOUT => esc_html__( 'Layout', 'elementor' ),
			self::TAB_SETTINGS => esc_html__( 'Settings', 'elementor' ),
		];
	}

	/**
	 * Get tabs.
	 *
	 * Retrieve the tabs of the current control.
	 *
	 * @since 1.6.0
	 * @access public
	 * @static
	 *
	 * @return array Control tabs.
	 */
	public static function get_tabs() {
		if ( ! self::$tabs ) {
			self::init_tabs();
		}

		return self::$tabs;
	}

	/**
	 * Add tab.
	 *
	 * This method adds a new tab to the current control.
	 *
	 * @since 1.6.0
	 * @access public
	 * @static
	 *
	 * @param string $tab_name  Tab name.
	 * @param string $tab_label Tab label.
	 */
	public static function add_tab( $tab_name, $tab_label = '' ) {
		if ( ! self::$tabs ) {
			self::init_tabs();
		}

		if ( isset( self::$tabs[ $tab_name ] ) ) {
			return;
		}

		self::$tabs[ $tab_name ] = $tab_label;
	}

	public static function get_groups_names() {
		// Group name must use "-" instead of "_"
		return [
			'background',
			'border',
			'typography',
			'image-size',
			'box-shadow',
			'css-filter',
			'text-shadow',
			'flex-container',
			'grid-container',
			'flex-item',
			'text-stroke',
		];
	}

	public static function get_controls_names() {
		return [
			self::TEXT,
			self::NUMBER,
			self::TEXTAREA,
			self::SELECT,
			self::SWITCHER,

			self::BUTTON,
			self::HIDDEN,
			self::HEADING,
			self::RAW_HTML,
			self::POPOVER_TOGGLE,
			self::SECTION,
			self::TAB,
			self::TABS,
			self::DIVIDER,
			self::DEPRECATED_NOTICE,
			self::ALERT,
			self::NOTICE,

			self::COLOR,
			self::MEDIA,
			self::SLIDER,
			self::DIMENSIONS,
			self::CHOOSE,
			self::VISUAL_CHOICE,
			self::WYSIWYG,
			self::CODE,
			self::FONT,
			self::IMAGE_DIMENSIONS,
			self::GAPS,

			self::WP_WIDGET,

			self::URL,
			self::REPEATER,
			self::ICON,
			self::ICONS,
			self::GALLERY,
			self::STRUCTURE,
			self::SELECT2,
			self::DATE_TIME,
			self::BOX_SHADOW,
			self::TEXT_SHADOW,
			self::ANIMATION,
			self::HOVER_ANIMATION,
			self::EXIT_ANIMATION,
		];
	}

	/**
	 * Register controls.
	 *
	 * This method creates a list of all the supported controls by requiring the
	 * control files and initializing each one of them.
	 *
	 * The list of supported controls includes the regular controls and the group
	 * controls.
	 *
	 * External developers can register new controls by hooking to the
	 * `elementor/controls/controls_registered` action.
	 *
	 * @since 3.1.0
	 * @access private
	 */
	private function register_controls() {
		$this->controls = [];

		foreach ( self::get_controls_names() as $control_id ) {
			$control_class_id = str_replace( ' ', '_', ucwords( str_replace( '_', ' ', $control_id ) ) );
			$class_name = __NAMESPACE__ . '\Control_' . $control_class_id;

			$this->register( new $class_name() );
		}

		// Group Controls
		foreach ( self::get_groups_names() as $group_name ) {
			$group_class_id = str_replace( ' ', '_', ucwords( str_replace( '-', ' ', $group_name ) ) );
			$class_name = __NAMESPACE__ . '\Group_Control_' . $group_class_id;

			$this->control_groups[ $group_name ] = new $class_name();
		}

		/**
		 * After controls registered.
		 *
		 * Fires after Elementor controls are registered.
		 *
		 * @since 1.0.0
		 * @deprecated 3.5.0 Use `elementor/controls/register` hook instead.
		 *
		 * @param Controls_Manager $this The controls manager.
		 */
		// TODO: Uncomment when Pro uses the new hook.
		// Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->do_deprecated_action(
		// 'elementor/controls/controls_registered',
		// [ $this ],
		// '3.5.0',
		// 'elementor/controls/register'
		// );

		do_action( 'elementor/controls/controls_registered', $this );

		/**
		 * After controls registered.
		 *
		 * Fires after Elementor controls are registered.
		 *
		 * @since 3.5.0
		 *
		 * @param Controls_Manager $this The controls manager.
		 */
		do_action( 'elementor/controls/register', $this );
	}

	/**
	 * Register control.
	 *
	 * This method adds a new control to the controls list. It adds any given
	 * control to any given control instance.
	 *
	 * @since 1.0.0
	 * @access public
	 * @deprecated 3.5.0 Use `register()` method instead.
	 *
	 * @param string       $control_id       Control ID.
	 * @param Base_Control $control_instance Control instance, usually the
	 *                                       current instance.
	 */
	public function register_control( $control_id, Base_Control $control_instance ) {
		// TODO: Uncomment when Pro uses the new hook.
		// Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function(
		// __METHOD__,
		// '3.5.0',
		// 'register()'
		// );

		$this->register( $control_instance, $control_id );
	}

	/**
	 * Register control.
	 *
	 * This method adds a new control to the controls list. It adds any given
	 * control to any given control instance.
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @param Base_Control $control_instance Control instance, usually the current instance.
	 * @param string       $control_id       Control ID. Deprecated parameter.
	 *
	 * @return void
	 */
	public function register( Base_Control $control_instance, $control_id = null ) {

		// TODO: For BC. Remove in the future.
		if ( $control_id ) {
			Plugin::instance()->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_argument(
				'$control_id', '3.5.0'
			);
		} else {
			$control_id = $control_instance->get_type();
		}

		$this->controls[ $control_id ] = $control_instance;
	}

	/**
	 * Unregister control.
	 *
	 * This method removes control from the controls list.
	 *
	 * @since 1.0.0
	 * @access public
	 * @deprecated 3.5.0 Use `unregister()` method instead.
	 *
	 * @param string $control_id Control ID.
	 *
	 * @return bool True if the control was removed, False otherwise.
	 */
	public function unregister_control( $control_id ) {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function(
			__METHOD__,
			'3.5.0',
			'unregister()'
		);

		return $this->unregister( $control_id );
	}

	/**
	 * Unregister control.
	 *
	 * This method removes control from the controls list.
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @param string $control_id Control ID.
	 *
	 * @return bool Whether the controls has been unregistered.
	 */
	public function unregister( $control_id ) {
		if ( ! isset( $this->controls[ $control_id ] ) ) {
			return false;
		}

		unset( $this->controls[ $control_id ] );

		return true;
	}

	/**
	 * Get controls.
	 *
	 * Retrieve the controls list from the current instance.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return Base_Control[] Controls list.
	 */
	public function get_controls() {
		if ( null === $this->controls ) {
			$this->register_controls();
		}

		return $this->controls;
	}

	/**
	 * Get control.
	 *
	 * Retrieve a specific control from the current controls instance.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $control_id Control ID.
	 *
	 * @return bool|Base_Control Control instance, or False otherwise.
	 */
	public function get_control( $control_id ) {
		$controls = $this->get_controls();

		return isset( $controls[ $control_id ] ) ? $controls[ $control_id ] : false;
	}

	/**
	 * Get controls data.
	 *
	 * Retrieve all the registered controls and all the data for each control.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Control data.
	 *
	 *    @type array $name Control data.
	 * }
	 */
	public function get_controls_data() {
		$controls_data = [];

		foreach ( $this->get_controls() as $name => $control ) {
			$controls_data[ $name ] = $control->get_settings();
		}

		return $controls_data;
	}

	/**
	 * Render controls.
	 *
	 * Generate the final HTML for all the registered controls using the element
	 * template.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render_controls() {
		foreach ( $this->get_controls() as $control ) {
			$control->print_template();
		}
	}

	/**
	 * Get control groups.
	 *
	 * Retrieve a specific group for a given ID, or a list of all the control
	 * groups.
	 *
	 * If the given group ID is wrong, it will return `null`. When the ID valid,
	 * it will return the group control instance. When no ID was given, it will
	 * return all the control groups.
	 *
	 * @since 1.0.10
	 * @access public
	 *
	 * @param string $id Optional. Group ID. Default is null.
	 *
	 * @return null|Group_Control_Base|Group_Control_Base[]
	 */
	public function get_control_groups( $id = null ) {
		if ( $id ) {
			return isset( $this->control_groups[ $id ] ) ? $this->control_groups[ $id ] : null;
		}

		return $this->control_groups;
	}

	/**
	 * Add group control.
	 *
	 * This method adds a new group control to the control groups list. It adds
	 * any given group control to any given group control instance.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string             $id       Group control ID.
	 * @param Group_Control_Base $instance Group control instance, usually the
	 *                                     current instance.
	 *
	 * @return Group_Control_Base Group control instance.
	 */
	public function add_group_control( $id, $instance ) {
		$this->control_groups[ $id ] = $instance;

		return $instance;
	}

	/**
	 * Enqueue control scripts and styles.
	 *
	 * Used to register and enqueue custom scripts and styles used by the control.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function enqueue_control_scripts() {
		foreach ( $this->get_controls() as $control ) {
			$control->enqueue();
		}
	}

	/**
	 * Open new stack.
	 *
	 * This method adds a new stack to the control stacks list. It adds any
	 * given stack to the current control instance.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param Controls_Stack $controls_stack Controls stack.
	 */
	public function open_stack( Controls_Stack $controls_stack ) {
		$stack_id = $controls_stack->get_unique_name();

		$this->stacks[ $stack_id ] = [
			'tabs' => [],
			'controls' => [],
			'style_controls' => [],
			'responsive_control_duplication_mode' => Plugin::$instance->breakpoints->get_responsive_control_duplication_mode(),
		];
	}

	/**
	 * Remove existing stack from the stacks cache
	 *
	 * Removes the stack of a passed instance from the Controls Manager's stacks cache.
	 *
	 * @param Controls_Stack $controls_stack
	 * @return void
	 */
	public function delete_stack( Controls_Stack $controls_stack ) {
		$stack_id = $controls_stack->get_unique_name();

		unset( $this->stacks[ $stack_id ] );
	}

	/**
	 * Add control to stack.
	 *
	 * This method adds a new control to the stack.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param Controls_Stack $element      Element stack.
	 * @param string         $control_id   Control ID.
	 * @param array          $control_data Control data.
	 * @param array          $options      Optional. Control additional options.
	 *                                     Default is an empty array.
	 *
	 * @return bool True if control added, False otherwise.
	 */
	public function add_control_to_stack( Controls_Stack $element, $control_id, $control_data, $options = [] ) {
		$default_options = [
			'overwrite' => false,
			'index' => null,
		];

		$control_type = 'controls';
		if ( Performance::should_optimize_controls() && $this->is_style_control( $control_data ) ) {
			$control_type = 'style_controls';
		}

		$options = array_merge( $default_options, $options );

		$default_args = [
			'type' => self::TEXT,
			'tab' => self::TAB_CONTENT,
		];

		$control_data['name'] = $control_id;

		$control_data = array_merge( $default_args, $control_data );

		$control_type_instance = $this->get_control( $control_data['type'] );

		if ( ! $control_type_instance ) {
			_doing_it_wrong( sprintf( '%1$s::%2$s', __CLASS__, __FUNCTION__ ), sprintf( 'Control type "%s" not found.', esc_html( $control_data['type'] ) ), '1.0.0' );
			return false;
		}

		if ( $control_type_instance instanceof Has_Validation ) {
			try {
				$control_type_instance->validate( $control_data );
			} catch ( \Exception $e ) {
				_doing_it_wrong( sprintf( '%1$s::%2$s', __CLASS__, __FUNCTION__ ), esc_html( $e->getMessage() ), '3.23.0' );
				return false;
			}
		}

		if ( $control_type_instance instanceof Base_Data_Control ) {
			$control_default_value = $control_type_instance->get_default_value();

			if ( is_array( $control_default_value ) ) {
				$control_data['default'] = isset( $control_data['default'] ) ? array_merge( $control_default_value, $control_data['default'] ) : $control_default_value;
			} else {
				$control_data['default'] = isset( $control_data['default'] ) ? $control_data['default'] : $control_default_value;
			}
		}

		$stack_id = $element->get_unique_name();

		if ( ! $options['overwrite'] && isset( $this->stacks[ $stack_id ][ $control_type ][ $control_id ] ) ) {
			_doing_it_wrong( sprintf( '%1$s::%2$s', __CLASS__, __FUNCTION__ ), sprintf( 'Cannot redeclare control with same name "%s".', esc_html( $control_id ) ), '1.0.0' );

			return false;
		}

		$tabs = self::get_tabs();

		if ( ! isset( $tabs[ $control_data['tab'] ] ) ) {
			$control_data['tab'] = $default_args['tab'];
		}

		$this->stacks[ $stack_id ]['tabs'][ $control_data['tab'] ] = $tabs[ $control_data['tab'] ];

		$this->stacks[ $stack_id ][ $control_type ][ $control_id ] = $control_data;

		if ( null !== $options['index'] ) {
			$controls = $this->stacks[ $stack_id ][ $control_type ];

			$controls_keys = array_keys( $controls );

			array_splice( $controls_keys, $options['index'], 0, $control_id );

			$this->stacks[ $stack_id ][ $control_type ] = array_merge( array_flip( $controls_keys ), $controls );
		}

		return true;
	}

	/**
	 * Remove control from stack.
	 *
	 * This method removes a control a the stack.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string       $stack_id   Stack ID.
	 * @param array|string $control_id The ID of the control to remove.
	 *
	 * @return bool|\WP_Error True if the stack was removed, False otherwise.
	 */
	public function remove_control_from_stack( $stack_id, $control_id ) {
		if ( is_array( $control_id ) ) {
			foreach ( $control_id as $id ) {
				$this->remove_control_from_stack( $stack_id, $id );
			}

			return true;
		}

		if ( empty( $this->stacks[ $stack_id ]['controls'][ $control_id ] ) ) {
			return new \WP_Error( 'Cannot remove not-exists control.' );
		}

		unset( $this->stacks[ $stack_id ]['controls'][ $control_id ] );

		return true;
	}

	/**
	 * Has Stacks Cache Been Cleared.
	 *
	 * @since 3.13.0
	 * @access public
	 * @return bool True if the CSS requires to clear the controls stack cache, False otherwise.
	 */
	public function has_stacks_cache_been_cleared() {
		return $this->has_stacks_cache_been_cleared;
	}

	/**
	 * Clear stack.
	 * This method clears the stack.
	 *
	 * @since 3.13.0
	 * @access public
	 */
	public function clear_stack_cache() {
		$this->stacks = [];
		$this->has_stacks_cache_been_cleared = true;
	}

	/**
	 * Get control from stack.
	 *
	 * Retrieve a specific control for a given a specific stack.
	 *
	 * If the given control does not exist in the stack, or the stack does not
	 * exist, it will return `WP_Error`. Otherwise, it will retrieve the control
	 * from the stack.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param string $stack_id   Stack ID.
	 * @param string $control_id Control ID.
	 *
	 * @return array|\WP_Error The control, or an error.
	 */
	public function get_control_from_stack( $stack_id, $control_id ) {
		$stack_data = $this->get_stacks( $stack_id );

		if ( ! empty( $stack_data['controls'][ $control_id ] ) ) {
			return $stack_data['controls'][ $control_id ];
		}

		if ( ! empty( $stack_data['style_controls'][ $control_id ] ) ) {
			return $stack_data['style_controls'][ $control_id ];
		}

		return new \WP_Error( 'Cannot get a not-exists control.' );
	}

	/**
	 * Update control in stack.
	 *
	 * This method updates the control data for a given stack.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param Controls_Stack $element      Element stack.
	 * @param string         $control_id   Control ID.
	 * @param array          $control_data Control data.
	 * @param array          $options      Optional. Control additional options.
	 *                                     Default is an empty array.
	 *
	 * @return bool True if control updated, False otherwise.
	 */
	public function update_control_in_stack( Controls_Stack $element, $control_id, $control_data, array $options = [] ) {
		$old_control_data = $this->get_control_from_stack( $element->get_unique_name(), $control_id );

		if ( is_wp_error( $old_control_data ) ) {
			return false;
		}

		if ( ! empty( $options['recursive'] ) ) {
			$control_data = array_replace_recursive( $old_control_data, $control_data );
		} else {
			$control_data = array_merge( $old_control_data, $control_data );
		}

		return $this->add_control_to_stack( $element, $control_id, $control_data, [
			'overwrite' => true,
		] );
	}

	/**
	 * Get stacks.
	 *
	 * Retrieve a specific stack for the list of stacks.
	 *
	 * If the given stack is wrong, it will return `null`. When the stack valid,
	 * it will return the the specific stack. When no stack was given, it will
	 * return all the stacks.
	 *
	 * @since 1.7.1
	 * @access public
	 *
	 * @param string $stack_id Optional. stack ID. Default is null.
	 *
	 * @return null|array A list of stacks.
	 */
	public function get_stacks( $stack_id = null ) {
		if ( $stack_id ) {
			if ( isset( $this->stacks[ $stack_id ] ) ) {
				return $this->stacks[ $stack_id ];
			}

			return null;
		}

		return $this->stacks;
	}

	/**
	 * Get element stack.
	 *
	 * Retrieve a specific stack for the list of stacks from the current instance.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param Controls_Stack $controls_stack  Controls stack.
	 *
	 * @return null|array Stack data if it exists, `null` otherwise.
	 */
	public function get_element_stack( Controls_Stack $controls_stack ) {
		$stack_id = $controls_stack->get_unique_name();

		if ( ! isset( $this->stacks[ $stack_id ] ) ) {
			return null;
		}

		if ( $this->should_clean_stack( $this->stacks[ $stack_id ] ) ) {
			$this->delete_stack( $controls_stack );
			return null;
		}

		return $this->stacks[ $stack_id ];
	}

	/**
	 * Add custom CSS controls.
	 *
	 * This method adds a new control for the "Custom CSS" feature. The free
	 * version of elementor uses this method to display an upgrade message to
	 * Elementor Pro.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param Controls_Stack $controls_stack .
	 * @param string         $tab
	 * @param array          $additional_messages
	 */
	public function add_custom_css_controls( Controls_Stack $controls_stack, $tab = self::TAB_ADVANCED, $additional_messages = [] ) {
		$controls_stack->start_controls_section(
			'section_custom_css_pro',
			[
				'label' => esc_html__( 'Custom CSS', 'elementor' ),
				'tab' => $tab,
			]
		);

		$messages = [
			esc_html__( 'Custom CSS lets you add CSS code to any widget, and see it render live right in the editor.', 'elementor' ),
		];

		if ( $additional_messages ) {
			$messages = array_merge( $messages, $additional_messages );
		}

		$controls_stack->add_control(
			'custom_css_pro',
			[
				'type' => self::RAW_HTML,
				'raw' => $this->get_teaser_template( [
					'title' => esc_html__( 'Meet Our Custom CSS', 'elementor' ),
					'messages' => $messages,
					'link' => 'https://go.elementor.com/go-pro-custom-css/',
				] ),
			]
		);

		$controls_stack->end_controls_section();
	}

	/**
	 * Add Page Transitions controls.
	 *
	 * This method adds a new control for the "Page Transitions" feature. The Core
	 * version of elementor uses this method to display an upgrade message to
	 * Elementor Pro.
	 *
	 * @param Controls_Stack $controls_stack .
	 * @param string         $tab
	 * @param array          $additional_messages
	 *
	 * @return void
	 */
	public function add_page_transitions_controls( Controls_Stack $controls_stack, $tab = self::TAB_ADVANCED, $additional_messages = [] ) {
		$controls_stack->start_controls_section(
			'section_page_transitions_teaser',
			[
				'label' => esc_html__( 'Page Transitions', 'elementor' ),
				'tab' => $tab,
			]
		);

		$messages = [
			esc_html__( 'Page Transitions let you style entrance and exit animations between pages as well as display loader until your page assets load.', 'elementor' ),
		];

		if ( $additional_messages ) {
			$messages = array_merge( $messages, $additional_messages );
		}

		$controls_stack->add_control(
			'page_transitions_teaser',
			[
				'type' => self::RAW_HTML,
				'raw' => $this->get_teaser_template( [
					'title' => esc_html__( 'Meet Page Transitions', 'elementor' ),
					'messages' => $messages,
					'link' => 'https://go.elementor.com/go-pro-page-transitions/',
				] ),
			]
		);

		$controls_stack->end_controls_section();
	}

	public function get_teaser_template( $texts ) {
		ob_start();
		?>
		<div class="elementor-nerd-box">
			<img class="elementor-nerd-box-icon" src="<?php echo esc_url( ELEMENTOR_ASSETS_URL . 'images/go-pro.svg' ); ?>" loading="lazy" alt="<?php echo esc_attr__( 'Upgrade', 'elementor' ); ?>" />
			<div class="elementor-nerd-box-title"><?php Utils::print_unescaped_internal_string( $texts['title'] ); ?></div>
			<?php foreach ( $texts['messages'] as $message ) { ?>
				<div class="elementor-nerd-box-message"><?php Utils::print_unescaped_internal_string( $message ); ?></div>
			<?php }

			// Show the upgrade button only if the user doesn't have Pro.
			if ( $texts['link'] && ! Utils::has_pro() ) { ?>
				<a class="elementor-button go-pro" href="<?php echo esc_url( ( $texts['link'] ) ); ?>" target="_blank">
					<?php echo esc_html__( 'Upgrade Now', 'elementor' ); ?>
				</a>
			<?php } ?>
		</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Get Responsive Control Device Suffix
	 *
	 * @param array $control
	 * @return string $device suffix
	 */
	public static function get_responsive_control_device_suffix( array $control ): string {
		if ( ! empty( $control['responsive']['max'] ) ) {
			$query_device = $control['responsive']['max'];
		} elseif ( ! empty( $control['responsive']['min'] ) ) {
			$query_device = $control['responsive']['min'];
		} else {
			return '';
		}

		return 'desktop' === $query_device ? '' : '_' . $query_device;
	}

	/**
	 * Add custom attributes controls.
	 *
	 * This method adds a new control for the "Custom Attributes" feature. The free
	 * version of elementor uses this method to display an upgrade message to
	 * Elementor Pro.
	 *
	 * @param Controls_Stack $controls_stack .
	 * @param string         $tab
	 * @since 2.8.3
	 * @access public
	 */
	public function add_custom_attributes_controls( Controls_Stack $controls_stack, string $tab = self::TAB_ADVANCED ) {
		$controls_stack->start_controls_section(
			'section_custom_attributes_pro',
			[
				'label' => esc_html__( 'Attributes', 'elementor' ),
				'tab' => $tab,
			]
		);

		$controls_stack->add_control(
			'custom_attributes_pro',
			[
				'type' => self::RAW_HTML,
				'raw' => $this->get_teaser_template( [
					'title' => esc_html__( 'Meet Our Attributes', 'elementor' ),
					'messages' => [
						esc_html__( 'Attributes lets you add custom HTML attributes to any element.', 'elementor' ),
					],
					'link' => 'https://go.elementor.com/go-pro-custom-attributes/',
				] ),
			]
		);

		$controls_stack->end_controls_section();
	}

	/**
	 * Check if a stack should be cleaned by the current responsive control duplication mode.
	 *
	 * @param array $stack
	 * @return bool
	 */
	private function should_clean_stack( $stack ): bool {
		if ( ! isset( $stack['responsive_control_duplication_mode'] ) ) {
			return false;
		}

		$stack_duplication_mode = $stack['responsive_control_duplication_mode'];

		// This array provides a convenient way to map human-readable mode names to numeric values for comparison.
		// If the current stack's mode is greater than or equal to the current mode, then we shouldn't clean the stack.
		$modes = [
			'off' => 1,
			'dynamic' => 2,
			'on' => 3,
		];

		if ( ! isset( $modes[ $stack_duplication_mode ] ) ) {
			return false;
		}

		$current_duplication_mode = Plugin::$instance->breakpoints->get_responsive_control_duplication_mode();

		if ( $modes[ $stack_duplication_mode ] >= $modes[ $current_duplication_mode ] ) {
			return false;
		}

		return true;
	}

	public function add_display_conditions_controls( Controls_Stack $controls_stack ) {
		if ( Utils::has_pro() ) {
			return;
		}

		ob_start();
		?>
		<div class="e-control-display-conditions-promotion__wrapper">
			<div class="e-control-display-conditions-promotion__description">
				<span class="e-control-display-conditions-promotion__text">
					<?php echo esc_html__( 'Display Conditions', 'elementor' ); ?>
				</span>
				<span class="e-control-display-conditions-promotion__lock-wrapper">
					<i class="eicon-lock e-control-display-conditions-promotion"></i>
				</span>
			</div>
			<i class="eicon-flow e-control-display-conditions-promotion"></i>
		</div>
		<?php
		$control_template = ob_get_clean();

		$controls_stack->add_control(
			'display_conditions_pro',
			[
				'type'      => self::RAW_HTML,
				'separator' => 'before',
				'raw'       => $control_template,
			]
		);
	}

	public function add_motion_effects_promotion_control( Controls_Stack $controls_stack ) {
		if ( Utils::has_pro() ) {
			return;
		}

		$controls_stack->add_control(
			'scrolling_effects_pro',
			[
				'type'      => self::RAW_HTML,
				'separator' => 'before',
				'raw'       => $this->promotion_switcher_control( esc_html__( 'Scrolling Effects', 'elementor' ), 'scrolling-effects' ),
			]
		);

		$controls_stack->add_control(
			'mouse_effects_pro',
			[
				'type'      => self::RAW_HTML,
				'separator' => 'before',
				'raw'       => $this->promotion_switcher_control( esc_html__( 'Mouse Effects', 'elementor' ), 'mouse-effects' ),
			]
		);

		$controls_stack->add_control(
			'sticky_pro',
			[
				'type'      => self::RAW_HTML,
				'separator' => 'before',
				'raw'       => $this->promotion_select_control( esc_html__( 'Sticky', 'elementor' ), 'sticky-effects' ),
			]
		);

		$controls_stack->add_control(
			'motion_effects_promotion_divider',
			[
				'type' => self::DIVIDER,
			]
		);
	}

	private function promotion_switcher_control( $title, $id ): string {
		return '<div class="elementor-control-type-switcher elementor-label-inline e-control-motion-effects-promotion__wrapper">
			<div class="elementor-control-content">
				<div class="elementor-control-field">
					<label>
						' . $title . '
					</label>
					<span class="e-control-motion-effects-promotion__lock-wrapper">
						<i class="eicon-lock"></i>
					</span>
					<div class="elementor-control-input-wrapper">
						<label class="elementor-switch elementor-control-unit-2 e-control-' . $id . '-promotion">
							<input type="checkbox" class="elementor-switch-input" disabled>
							<span class="elementor-switch-label" data-off="Off"></span>
							<span class="elementor-switch-handle"></span>
						</label>
					</div>
				</div>
			</div>
		</div>';
	}

	private function promotion_select_control( $title, $id ): string {
		return '<div class="elementor-control-type-select elementor-label-inline e-control-motion-effects-promotion__wrapper">
			<div class="elementor-control-content">
				<div class="elementor-control-field ">
					<label for="sticky-motion-effect-pro">
						' . $title . '
					</label>
					<span class="e-control-motion-effects-promotion__lock-wrapper">
						<i class="eicon-lock"></i>
					</span>
					<div class="elementor-control-input-wrapper elementor-control-unit-5 e-control-' . $id . '-promotion">
					<div class="select-promotion elementor-control-unit-5">None</div>
					</div>
				</div>
			</div>
		</div>';
	}

	private function is_style_control( $control_data ): bool {
		$frontend_available = $control_data['frontend_available'] ?? false;
		if ( $frontend_available ) {
			return false;
		}

		if ( ! empty( $control_data['control_type'] ) && 'content' === $control_data['control_type'] ) {
			return false;
		}

		if ( ! empty( $control_data['prefix_class'] ) ) {
			return false;
		}

		$render_type = $control_data['render_type'] ?? '';
		if ( 'template' === $render_type ) {
			return false;
		}

		if ( 'ui' === $render_type ) {
			return true;
		}

		if ( ! empty( $control_data['selectors'] ) ) {
			return true;
		}

		return false;
	}
}
