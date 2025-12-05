<?php
namespace Elementor\Core\Breakpoints;

use Elementor\Core\Base\Module;
use Elementor\Core\Kits\Documents\Tabs\Settings_Layout;
use Elementor\Core\Responsive\Files\Frontend;
use Elementor\Modules\DevTools\Deprecation;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Manager extends Module {

	const BREAKPOINT_SETTING_PREFIX = 'viewport_';
	const BREAKPOINT_KEY_MOBILE = 'mobile';
	const BREAKPOINT_KEY_MOBILE_EXTRA = 'mobile_extra';
	const BREAKPOINT_KEY_TABLET = 'tablet';
	const BREAKPOINT_KEY_TABLET_EXTRA = 'tablet_extra';
	const BREAKPOINT_KEY_LAPTOP = 'laptop';
	const BREAKPOINT_KEY_DESKTOP = 'desktop';
	const BREAKPOINT_KEY_WIDESCREEN = 'widescreen';

	/**
	 * Breakpoints
	 *
	 * An array containing instances of the all of the system's available breakpoints.
	 *
	 * @since 3.2.0
	 * @access private
	 *
	 * @var Breakpoint[]
	 */
	private $breakpoints;

	/**
	 * Active Breakpoints
	 *
	 * An array containing instances of the enabled breakpoints.
	 *
	 * @since 3.2.0
	 * @access private
	 *
	 * @var Breakpoint[]
	 */
	private $active_breakpoints;

	/**
	 * Responsive Control Duplication Mode.
	 *
	 * Determines the current responsive control generation mode.
	 * Options are:
	 * -- 'on': Responsive controls are duplicated in `add_responsive_control()`.
	 * -- 'off': Responsive controls are NOT duplicated in `add_responsive_control()`.
	 * -- 'dynamic': Responsive controls are only duplicated if their config contains `'dynamic' => 'active' => true`.
	 *
	 * When generating Post CSS, the mode is set to 'on'. When generating Dynamic CSS, the mode is set to 'dynamic'.
	 *
	 * default value is 'off'.
	 *
	 * @since 3.4.0
	 * @access private
	 *
	 * @var string
	 */
	private $responsive_control_duplication_mode = 'off';

	private $icons_map;

	/**
	 * Has Custom Breakpoints
	 *
	 * A flag that holds a cached value that indicates if there are active custom-breakpoints.
	 *
	 * @since 3.5.0
	 * @access private
	 *
	 * @var boolean
	 */
	private $has_custom_breakpoints;

	public function get_name() {
		return 'breakpoints';
	}

	/**
	 * Get Breakpoints
	 *
	 * Retrieve the array containing instances of all breakpoints existing in the system, or a single breakpoint if a
	 * name is passed.
	 *
	 * @since 3.2.0
	 *
	 * @param $breakpoint_name
	 * @return Breakpoint[]|Breakpoint
	 */
	public function get_breakpoints( $breakpoint_name = null ) {
		if ( ! $this->breakpoints ) {
			$this->init_breakpoints();
		}
		return self::get_items( $this->breakpoints, $breakpoint_name );
	}

	/**
	 * Get Active Breakpoints
	 *
	 * Retrieve the array of --enabled-- breakpoints, or a single breakpoint if a name is passed.
	 *
	 * @since 3.2.0
	 *
	 * @param string|null $breakpoint_name
	 * @return Breakpoint[]|Breakpoint
	 */
	public function get_active_breakpoints( $breakpoint_name = null ) {
		if ( ! $this->active_breakpoints ) {
			$this->init_active_breakpoints();
		}

		return self::get_items( $this->active_breakpoints, $breakpoint_name );
	}

	/**
	 * Get Active Devices List
	 *
	 * Retrieve an array containing the keys of all active devices, including 'desktop'.
	 *
	 * @since 3.2.0
	 *
	 * @param array $args
	 * @return array
	 */
	public function get_active_devices_list( $args = [] ) {
		$default_args = [
			'add_desktop' => true,
			'reverse' => false,
			'desktop_first' => false,
		];

		$args = array_merge( $default_args, $args );

		$active_devices = array_keys( Plugin::$instance->breakpoints->get_active_breakpoints() );

		if ( $args['add_desktop'] ) {
			// Insert the 'desktop' device in the correct position.
			if ( ! $args['desktop_first'] && in_array( 'widescreen', $active_devices, true ) ) {
				$widescreen_index = array_search( 'widescreen', $active_devices, true );

				array_splice( $active_devices, $widescreen_index, 0, [ 'desktop' ] );
			} else {
				$active_devices[] = 'desktop';
			}
		}

		if ( $args['reverse'] ) {
			$active_devices = array_reverse( $active_devices );
		}

		return $active_devices;
	}

	/** Has Custom Breakpoints
	 *
	 * Checks whether there are currently custom breakpoints saved in the database.
	 * Returns true if there are breakpoint values saved in the active kit.
	 * If the user has activated any additional custom breakpoints (mobile extra, tablet extra, laptop, widescreen) -
	 * that is considered as having custom breakpoints.
	 *
	 * @since 3.2.0
	 *
	 * @return boolean
	 */
	public function has_custom_breakpoints() {
		if ( isset( $this->has_custom_breakpoints ) ) {
			return $this->has_custom_breakpoints;
		}

		$breakpoints = $this->get_active_breakpoints();

		$additional_breakpoints = [
			self::BREAKPOINT_KEY_MOBILE_EXTRA,
			self::BREAKPOINT_KEY_TABLET_EXTRA,
			self::BREAKPOINT_KEY_LAPTOP,
			self::BREAKPOINT_KEY_WIDESCREEN,
		];

		foreach ( $breakpoints as $breakpoint_name => $breakpoint ) {
			if ( in_array( $breakpoint_name, $additional_breakpoints, true ) ) {
				$this->has_custom_breakpoints = true;

				return true;
			}

			/** @var Breakpoint $breakpoint */
			if ( $breakpoint->is_custom() ) {
				$this->has_custom_breakpoints = true;

				return true;
			}
		}

		$this->has_custom_breakpoints = false;

		return false;
	}

	/**
	 * Get Device Min Breakpoint
	 *
	 * For a given device, return the minimum possible breakpoint. Except for the cases of mobile and widescreen
	 * devices, A device's min breakpoint is determined by the previous device's max breakpoint + 1px.
	 *
	 * @since 3.2.0
	 *
	 * @param string $device_name
	 * @return int the min breakpoint of the passed device
	 */
	public function get_device_min_breakpoint( $device_name ) {
		if ( 'desktop' === $device_name ) {
			return $this->get_desktop_min_point();
		}

		$active_breakpoints = $this->get_active_breakpoints();
		$current_device_breakpoint = $active_breakpoints[ $device_name ];

		// Since this method is called multiple times, usage of class variables is to memory and processing time.
		// Get only the keys for active breakpoints.
		$breakpoint_keys = array_keys( $active_breakpoints );

		if ( $breakpoint_keys[0] === $device_name ) {
			// For the lowest breakpoint, the min point is always 320.
			$min_breakpoint = 320;
		} elseif ( 'min' === $current_device_breakpoint->get_direction() ) {
			// 'min-width' breakpoints only have a minimum point. The breakpoint value itself the device min point.
			$min_breakpoint = $current_device_breakpoint->get_value();
		} else {
			// This block handles all other devices.
			$device_name_index = array_search( $device_name, $breakpoint_keys, true );

			$previous_index = $device_name_index - 1;
			$previous_breakpoint_key = $breakpoint_keys[ $previous_index ];
			/** @var Breakpoint $previous_breakpoint */
			$previous_breakpoint = $active_breakpoints[ $previous_breakpoint_key ];

			$min_breakpoint = $previous_breakpoint->get_value() + 1;
		}

		return $min_breakpoint;
	}

	/**
	 * Get Desktop Min Breakpoint
	 *
	 * Returns the minimum possible breakpoint for the default (desktop) device.
	 *
	 * @since 3.2.0
	 *
	 * @return int the min breakpoint of the passed device
	 */
	public function get_desktop_min_point() {
		$active_breakpoints = $this->get_active_breakpoints();
		$desktop_previous_device = $this->get_desktop_previous_device_key();

		return $active_breakpoints[ $desktop_previous_device ]->get_value() + 1;
	}

	public function refresh() {
		unset( $this->has_custom_breakpoints );

		$this->init_breakpoints();
		$this->init_active_breakpoints();
	}

	/**
	 * Get Responsive Icons Classes Map
	 *
	 * If a $device parameter is passed, this method retrieves the device's icon class list (the ones attached to the `<i>`
	 * element). If no parameter is passed, it returns an array of devices containing each device's icon class list.
	 *
	 * This method was created because 'mobile_extra' and 'tablet_extra' breakpoint icons need to be tilted by 90
	 * degrees, and this tilt is achieved in CSS via the class `eicon-tilted`.
	 *
	 * @since 3.4.0
	 *
	 * @return array|string
	 */
	public function get_responsive_icons_classes_map( $device = null ) {
		if ( ! $this->icons_map ) {
			$this->icons_map = [
				'mobile' => 'eicon-device-mobile',
				'mobile_extra' => 'eicon-device-mobile eicon-tilted',
				'tablet' => 'eicon-device-tablet',
				'tablet_extra' => 'eicon-device-tablet eicon-tilted',
				'laptop' => 'eicon-device-laptop',
				'desktop' => 'eicon-device-desktop',
				'widescreen' => 'eicon-device-wide',
			];
		}

		return self::get_items( $this->icons_map, $device );
	}

	/**
	 * Get Default Config
	 *
	 * Retrieve the default breakpoints config array. The 'selector' property is used for CSS generation (the
	 * Stylesheet::add_device() method).
	 *
	 * @return array
	 */
	public static function get_default_config() {
		return [
			self::BREAKPOINT_KEY_MOBILE => [
				'label' => esc_html__( 'Mobile Portrait', 'elementor' ),
				'default_value' => 767,
				'direction' => 'max',
			],
			self::BREAKPOINT_KEY_MOBILE_EXTRA => [
				'label' => esc_html__( 'Mobile Landscape', 'elementor' ),
				'default_value' => 880,
				'direction' => 'max',
			],
			self::BREAKPOINT_KEY_TABLET => [
				'label' => esc_html__( 'Tablet Portrait', 'elementor' ),
				'default_value' => 1024,
				'direction' => 'max',
			],
			self::BREAKPOINT_KEY_TABLET_EXTRA => [
				'label' => esc_html__( 'Tablet Landscape', 'elementor' ),
				'default_value' => 1200,
				'direction' => 'max',
			],
			self::BREAKPOINT_KEY_LAPTOP => [
				'label' => esc_html__( 'Laptop', 'elementor' ),
				'default_value' => 1366,
				'direction' => 'max',
			],
			self::BREAKPOINT_KEY_WIDESCREEN => [
				'label' => esc_html__( 'Widescreen', 'elementor' ),
				'default_value' => 2400,
				'direction' => 'min',
			],
		];
	}

	/**
	 * Get Breakpoints Config
	 *
	 * Iterates over an array of all of the system's breakpoints (both active and inactive), queries each breakpoint's
	 * class instance, and generates an array containing data on each breakpoint: its label, current value, direction
	 * ('min'/'max') and whether it is enabled or not.
	 *
	 * @return array
	 */
	public function get_breakpoints_config() {
		$breakpoints = $this->get_breakpoints();

		$config = [];

		foreach ( $breakpoints as $breakpoint_name => $breakpoint ) {
			$config[ $breakpoint_name ] = [
				'label' => $breakpoint->get_label(),
				'value' => $breakpoint->get_value(),
				'default_value' => $breakpoint->get_default_value(),
				'direction' => $breakpoint->get_direction(),
				'is_enabled' => $breakpoint->is_enabled(),
			];
		}

		return $config;
	}

	/**
	 * Get Responsive Control Duplication Mode
	 *
	 * Retrieve the value of the $responsive_control_duplication_mode private class variable.
	 * See the variable's PHPDoc for details.
	 *
	 * @since 3.4.0
	 * @access public
	 */
	public function get_responsive_control_duplication_mode() {
		return $this->responsive_control_duplication_mode;
	}

	/**
	 * Set Responsive Control Duplication Mode
	 *
	 * Sets  the value of the $responsive_control_duplication_mode private class variable.
	 * See the variable's PHPDoc for details.
	 *
	 * @since 3.4.0
	 *
	 * @access public
	 * @param string $mode
	 */
	public function set_responsive_control_duplication_mode( $mode ) {
		$this->responsive_control_duplication_mode = $mode;
	}

	/**
	 * Get Stylesheet Templates Path
	 *
	 * @since 3.2.0
	 * @access public
	 * @static
	 */
	public static function get_stylesheet_templates_path() {
		return ELEMENTOR_ASSETS_PATH . 'css/templates/';
	}

	/**
	 * Compile Stylesheet Templates
	 *
	 * @since 3.2.0
	 * @access public
	 * @static
	 */
	public static function compile_stylesheet_templates() {
		foreach ( self::get_stylesheet_templates() as $file_name => $template_path ) {
			$file = new Frontend( $file_name, $template_path );

			$file->update();
		}
	}

	/**
	 * Init Breakpoints
	 *
	 * Creates the breakpoints array, containing instances of each breakpoint. Returns an array of ALL breakpoints,
	 * both enabled and disabled.
	 *
	 * @since 3.2.0
	 */
	private function init_breakpoints() {
		$breakpoints = [];

		$setting_prefix = self::BREAKPOINT_SETTING_PREFIX;

		$active_breakpoint_keys = [
			$setting_prefix . self::BREAKPOINT_KEY_MOBILE,
			$setting_prefix . self::BREAKPOINT_KEY_TABLET,
		];

		if ( Plugin::$instance->experiments->is_feature_active( 'additional_custom_breakpoints' ) ) {
			$kit_active_id = Plugin::$instance->kits_manager->get_active_id();
			// Get the breakpoint settings saved in the kit directly from the DB to avoid initializing the kit too early.
			$raw_kit_settings = get_post_meta( $kit_active_id, '_elementor_page_settings', true );

			// If there is an existing kit with an active breakpoints value saved, use it.
			if ( isset( $raw_kit_settings[ Settings_Layout::ACTIVE_BREAKPOINTS_CONTROL_ID ] ) ) {
				$active_breakpoint_keys = $raw_kit_settings[ Settings_Layout::ACTIVE_BREAKPOINTS_CONTROL_ID ];
			}
		}

		$default_config = self::get_default_config();

		foreach ( $default_config as $breakpoint_name => $breakpoint_config ) {
			$args = [ 'name' => $breakpoint_name ] + $breakpoint_config;

			// Make sure the two default breakpoints (mobile, tablet) are always enabled.
			if ( self::BREAKPOINT_KEY_MOBILE === $breakpoint_name || self::BREAKPOINT_KEY_TABLET === $breakpoint_name ) {
				// Make sure the default Mobile and Tablet breakpoints are always enabled.
				$args['is_enabled'] = true;
			} else {
				// If the breakpoint is in the active breakpoints array, make sure it's instantiated as enabled.
				$args['is_enabled'] = in_array( $setting_prefix . $breakpoint_name, $active_breakpoint_keys, true );
			}

			$breakpoints[ $breakpoint_name ] = new Breakpoint( $args );
		}

		$this->breakpoints = $breakpoints;
	}

	/**
	 * Init Active Breakpoints
	 *
	 * Create/Refresh the array of --enabled-- breakpoints.
	 *
	 * @since 3.2.0
	 */
	private function init_active_breakpoints() {
		$this->active_breakpoints = array_filter( $this->get_breakpoints(), function( $breakpoint ) {
			/** @var Breakpoint $breakpoint */
			return $breakpoint->is_enabled();
		} );
	}

	private function get_desktop_previous_device_key() {
		$config_array_keys = array_keys( $this->get_active_breakpoints() );
		$num_of_devices = count( $config_array_keys );

		// If the widescreen breakpoint is active, the device that's previous to desktop is the last one before
		// widescreen.
		if ( self::BREAKPOINT_KEY_WIDESCREEN === $config_array_keys[ $num_of_devices - 1 ] ) {
			$desktop_previous_device = $config_array_keys[ $num_of_devices - 2 ];
		} else {
			// If the widescreen breakpoint isn't active, we just take the last device returned by the config.
			$desktop_previous_device = $config_array_keys[ $num_of_devices - 1 ];
		}

		return $desktop_previous_device;
	}

	/**
	 * Get Stylesheet Templates
	 *
	 * @since 3.2.0
	 * @access private
	 * @static
	 */
	private static function get_stylesheet_templates() {
		$templates_paths = glob( self::get_stylesheet_templates_path() . '*.css' );

		$templates = [];

		foreach ( $templates_paths as $template_path ) {
			$file_name = 'custom-' . basename( $template_path );

			$templates[ $file_name ] = $template_path;
		}

		$deprecated_hook = 'elementor/core/responsive/get_stylesheet_templates';
		$replacement_hook = 'elementor/core/breakpoints/get_stylesheet_template';

		/**
		 * @type Deprecation $deprecation_module
		 */
		$deprecation_module = Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation;

		// TODO: REMOVE THIS DEPRECATED HOOK IN ELEMENTOR v3.10.0/v4.0.0
		$templates = $deprecation_module->apply_deprecated_filter( $deprecated_hook, [ $templates ], '3.2.0', $replacement_hook );

		return apply_filters( $replacement_hook, $templates );
	}
}
