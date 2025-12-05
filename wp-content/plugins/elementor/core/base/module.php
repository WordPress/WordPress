<?php
namespace Elementor\Core\Base;

use Elementor\Plugin;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor module.
 *
 * An abstract class that provides the needed properties and methods to
 * manage and handle modules in inheriting classes.
 *
 * @since 1.7.0
 * @abstract
 */
abstract class Module extends Base_Object {

	/**
	 * Module class reflection.
	 *
	 * Holds the information about a class.
	 *
	 * @since 1.7.0
	 * @access private
	 *
	 * @var \ReflectionClass
	 */
	private $reflection;

	/**
	 * Module components.
	 *
	 * Holds the module components.
	 *
	 * @since 1.7.0
	 * @access private
	 *
	 * @var array
	 */
	private $components = [];

	/**
	 * Module instance.
	 *
	 * Holds the module instance.
	 *
	 * @since 1.7.0
	 * @access protected
	 *
	 * @var Module
	 */
	protected static $_instances = [];

	/**
	 * Get module name.
	 *
	 * Retrieve the module name.
	 *
	 * @since 1.7.0
	 * @access public
	 * @abstract
	 *
	 * @return string Module name.
	 */
	abstract public function get_name();

	/**
	 * Instance.
	 *
	 * Ensures only one instance of the module class is loaded or can be loaded.
	 *
	 * @since 1.7.0
	 * @access public
	 * @static
	 *
	 * @return $this An instance of the class.
	 */
	public static function instance() {
		$class_name = static::class_name();

		if ( empty( static::$_instances[ $class_name ] ) ) {
			static::$_instances[ $class_name ] = new static();
		}

		return static::$_instances[ $class_name ];
	}

	/**
	 * @since 2.0.0
	 * @access public
	 * @static
	 */
	public static function is_active() {
		return true;
	}

	/**
	 * Class name.
	 *
	 * Retrieve the name of the class.
	 *
	 * @since 1.7.0
	 * @access public
	 * @static
	 */
	public static function class_name() {
		return get_called_class();
	}

	public static function get_experimental_data() {
		return [];
	}

	/**
	 * Clone.
	 *
	 * Disable class cloning and throw an error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object. Therefore, we don't want the object to be cloned.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function __clone() {
		_doing_it_wrong(
			__FUNCTION__,
			sprintf( 'Cloning instances of the singleton "%s" class is forbidden.', get_class( $this ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'1.0.0'
		);
	}

	/**
	 * Wakeup.
	 *
	 * Disable unserializing of the class.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function __wakeup() {
		_doing_it_wrong(
			__FUNCTION__,
			sprintf( 'Unserializing instances of the singleton "%s" class is forbidden.', get_class( $this ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'1.0.0'
		);
	}

	/**
	 * @since 2.0.0
	 * @access public
	 */
	public function get_reflection() {
		if ( null === $this->reflection ) {
			$this->reflection = new \ReflectionClass( $this );
		}

		return $this->reflection;
	}

	/**
	 * Add module component.
	 *
	 * Add new component to the current module.
	 *
	 * @since 1.7.0
	 * @access public
	 *
	 * @param string $id       Component ID.
	 * @param mixed  $instance An instance of the component.
	 */
	public function add_component( $id, $instance ) {
		$this->components[ $id ] = $instance;
	}

	/**
	 * @since 2.3.0
	 * @access public
	 * @return Module[]
	 */
	public function get_components() {
		return $this->components;
	}

	/**
	 * Get module component.
	 *
	 * Retrieve the module component.
	 *
	 * @since 1.7.0
	 * @access public
	 *
	 * @param string $id Component ID.
	 *
	 * @return mixed An instance of the component, or `false` if the component
	 *               doesn't exist.
	 */
	public function get_component( $id ) {
		if ( isset( $this->components[ $id ] ) ) {
			return $this->components[ $id ];
		}

		return false;
	}

	/**
	 * Get assets url.
	 *
	 * @since 2.3.0
	 * @access protected
	 *
	 * @param string $file_name
	 * @param string $file_extension
	 * @param string $relative_url Optional. Default is null.
	 * @param string $add_min_suffix Optional. Default is 'default'.
	 *
	 * @return string
	 */
	final protected function get_assets_url( $file_name, $file_extension, $relative_url = null, $add_min_suffix = 'default' ) {
		static $is_test_mode = null;

		if ( null === $is_test_mode ) {
			$is_test_mode = Utils::is_script_debug() || Utils::is_elementor_tests();
		}

		if ( ! $relative_url ) {
			$relative_url = $this->get_assets_relative_url() . $file_extension . '/';
		}

		$url = $this->get_assets_base_url() . $relative_url . $file_name;

		if ( 'default' === $add_min_suffix ) {
			$add_min_suffix = ! $is_test_mode;
		}

		if ( $add_min_suffix ) {
			$url .= '.min';
		}

		return $url . '.' . $file_extension;
	}

	/**
	 * Get js assets url
	 *
	 * @since 2.3.0
	 * @access protected
	 *
	 * @param string $file_name
	 * @param string $relative_url Optional. Default is null.
	 * @param string $add_min_suffix Optional. Default is 'default'.
	 *
	 * @return string
	 */
	final protected function get_js_assets_url( $file_name, $relative_url = null, $add_min_suffix = 'default' ) {
		return $this->get_assets_url( $file_name, 'js', $relative_url, $add_min_suffix );
	}

	/**
	 * Get css assets url
	 *
	 * @since 2.3.0
	 * @access protected
	 *
	 * @param string $file_name
	 * @param string $relative_url         Optional. Default is null.
	 * @param string $add_min_suffix       Optional. Default is 'default'.
	 * @param bool   $add_direction_suffix Optional. Default is `false`.
	 *
	 * @return string
	 */
	final protected function get_css_assets_url( $file_name, $relative_url = null, $add_min_suffix = 'default', $add_direction_suffix = false ) {
		static $direction_suffix = null;

		if ( ! $direction_suffix ) {
			$direction_suffix = is_rtl() ? '-rtl' : '';
		}

		if ( $add_direction_suffix ) {
			$file_name .= $direction_suffix;
		}

		return $this->get_assets_url( $file_name, 'css', $relative_url, $add_min_suffix );
	}

	/**
	 * Get Frontend File URL
	 *
	 * Returns the URL for the CSS file to be loaded in the front end. If requested via the second parameter, a custom
	 * file is generated based on a passed template file name. Otherwise, the URL for the default CSS file is returned.
	 *
	 * @since 3.24.0
	 *
	 * @access public
	 *
	 * @param string  $file_name
	 * @param boolean $has_custom_breakpoints
	 *
	 * @return string frontend file URL
	 */
	public function get_frontend_file_url( $file_name, $has_custom_breakpoints ) {
		return Plugin::$instance->frontend->get_frontend_file_url( $file_name, $has_custom_breakpoints );
	}

	/**
	 * Get assets base url
	 *
	 * @since 2.6.0
	 * @access protected
	 *
	 * @return string
	 */
	protected function get_assets_base_url() {
		return ELEMENTOR_URL;
	}

	/**
	 * Get assets relative url
	 *
	 * @since 2.3.0
	 * @access protected
	 *
	 * @return string
	 */
	protected function get_assets_relative_url() {
		return 'assets/';
	}

	/**
	 * Get the module's associated widgets.
	 *
	 * @return string[]
	 */
	protected function get_widgets() {
		return [];
	}

	/**
	 * Initialize the module related widgets.
	 */
	public function init_widgets() {
		$widget_manager = Plugin::instance()->widgets_manager;

		foreach ( $this->get_widgets() as $widget ) {
			$class_name = $this->get_reflection()->getNamespaceName() . '\Widgets\\' . $widget;

			$widget_manager->register( new $class_name() );
		}
	}

	public function __construct() {
		add_action( 'elementor/widgets/register', [ $this, 'init_widgets' ] );
	}
}
