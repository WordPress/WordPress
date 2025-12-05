<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor autoloader.
 *
 * Elementor autoloader handler class is responsible for loading the different
 * classes needed to run the plugin.
 *
 * @since 1.6.0
 */
class Autoloader {

	/**
	 * Classes map.
	 *
	 * Maps Elementor classes to file names.
	 *
	 * @since 1.6.0
	 * @access private
	 * @static
	 *
	 * @var array Classes used by elementor.
	 */
	private static $classes_map;

	/**
	 * Classes aliases.
	 *
	 * Maps Elementor classes to aliases.
	 *
	 * @since 1.6.0
	 * @access private
	 * @static
	 *
	 * @var array Classes aliases.
	 */
	private static $classes_aliases;

	/**
	 * Default path for autoloader.
	 *
	 * @var string
	 */
	private static $default_path;

	/**
	 * Default namespace for autoloader.
	 *
	 * @var string
	 */
	private static $default_namespace;

	/**
	 * Run autoloader.
	 *
	 * Register a function as `__autoload()` implementation.
	 *
	 * @param string $default_path
	 * @param string $default_namespace
	 *
	 * @since 1.6.0
	 * @access public
	 * @static
	 */
	public static function run( $default_path = '', $default_namespace = '' ) {
		if ( '' === $default_path ) {
			$default_path = ELEMENTOR_PATH;
		}

		if ( '' === $default_namespace ) {
			$default_namespace = __NAMESPACE__;
		}

		self::$default_path = $default_path;
		self::$default_namespace = $default_namespace;

		spl_autoload_register( [ __CLASS__, 'autoload' ] );
	}

	/**
	 * Get classes aliases.
	 *
	 * Retrieve the classes aliases names.
	 *
	 * @since 1.6.0
	 * @access public
	 * @static
	 *
	 * @return array Classes aliases.
	 */
	public static function get_classes_aliases() {
		if ( ! self::$classes_aliases ) {
			self::init_classes_aliases();
		}

		return self::$classes_aliases;
	}

	public static function get_classes_map() {
		if ( ! self::$classes_map ) {
			self::init_classes_map();
		}

		return self::$classes_map;
	}

	private static function init_classes_map() {
		self::$classes_map = [
			'Api' => 'includes/api.php',
			'Base_Control' => 'includes/controls/base.php',
			'Base_Data_Control' => 'includes/controls/base-data.php',
			'Base_UI_Control' => 'includes/controls/base-ui.php',
			'Beta_Testers' => 'includes/beta-testers.php',
			'Compatibility' => 'includes/compatibility.php',
			'Conditions' => 'includes/conditions.php',
			'Controls_Manager' => 'includes/managers/controls.php',
			'Controls_Stack' => 'includes/base/controls-stack.php',
			'Sub_Controls_Stack' => 'includes/base/sub-controls-stack.php',
			'DB' => 'includes/db.php',
			'Elements_Manager' => 'includes/managers/elements.php',
			'Embed' => 'includes/embed.php',
			'Fonts' => 'includes/fonts.php',
			'Frontend' => 'includes/frontend.php',
			'Group_Control_Base' => 'includes/controls/groups/base.php',
			'Group_Control_Interface' => 'includes/interfaces/group-control.php',
			'Has_Validation' => 'includes/interfaces/has-validation.php',
			'Heartbeat' => 'includes/heartbeat.php',
			'Images_Manager' => 'includes/managers/image.php',
			'Maintenance' => 'includes/maintenance.php',
			'Maintenance_Mode' => 'includes/maintenance-mode.php',
			'Preview' => 'includes/preview.php',
			'Rollback' => 'includes/rollback.php',
			'Settings' => 'includes/settings/settings.php',
			'Settings_Controls' => 'includes/settings/controls.php',
			'Settings_Validations' => 'includes/settings/validations.php',
			'Settings_Page' => 'includes/settings/settings-page.php',
			'Shapes' => 'includes/shapes.php',
			'Skins_Manager' => 'includes/managers/skins.php',
			'Icons_Manager' => 'includes/managers/icons.php',
			'Stylesheet' => 'includes/stylesheet.php',
			'System_Info\Main' => 'includes/settings/system-info/main.php',
			'TemplateLibrary\Classes\Import_Images' => 'includes/template-library/classes/class-import-images.php',
			'TemplateLibrary\Classes\Media_Collector' => 'includes/template-library/classes/class-media-collector.php',
			'TemplateLibrary\Classes\Media_Mapper' => 'includes/template-library/classes/class-media-mapper.php',
			'TemplateLibrary\Forms\New_Template_Form' => 'includes/template-library/forms/new-template-form.php',
			'TemplateLibrary\Manager' => 'includes/template-library/manager.php',
			'TemplateLibrary\Source_Base' => 'includes/template-library/sources/base.php',
			'TemplateLibrary\Source_Local' => 'includes/template-library/sources/local.php',
			'TemplateLibrary\Source_Remote' => 'includes/template-library/sources/remote.php',
			'TemplateLibrary\Source_Cloud' => 'includes/template-library/sources/cloud.php',
			'Tools' => 'includes/settings/tools.php',
			'Container\Container' => 'includes/container/container.php',
			'Tracker' => 'includes/tracker.php',
			'User' => 'includes/user.php',
			'User_Data' => 'includes/user-data.php',
			'Utils' => 'includes/utils.php',
			'Widget_WordPress' => 'includes/widgets/wordpress.php',
			'Widgets_Manager' => 'includes/managers/widgets.php',
			'WordPress_Widgets_Manager' => 'includes/managers/wordpress-widgets.php',
		];

		$controls_names = Controls_Manager::get_controls_names();

		$controls_names = array_merge( $controls_names, [
			'base_multiple',
			'base_units',
		] );

		foreach ( $controls_names as $control_name ) {
			$class_name = 'Control_' . self::normalize_class_name( $control_name, '_' );

			self::$classes_map[ $class_name ] = 'includes/controls/' . str_replace( '_', '-', $control_name ) . '.php';
		}

		$controls_groups_names = Controls_Manager::get_groups_names();

		foreach ( $controls_groups_names as $group_name ) {
			$class_name = 'Group_Control_' . self::normalize_class_name( $group_name, '_' );

			self::$classes_map[ $class_name ] = 'includes/controls/groups/' . $group_name . '.php';
		}
	}

	/**
	 * Normalize Class Name
	 *
	 * Used to convert control names to class names.
	 *
	 * @param string $class_name
	 * @param string $delimiter
	 *
	 * @return mixed
	 */
	private static function normalize_class_name( $class_name, $delimiter = ' ' ) {
		return ucwords( str_replace( '-', '_', $class_name ), $delimiter );
	}

	/**
	 * Init classes aliases.
	 *
	 * When Elementor classes renamed or moved to different folders, developers
	 * can still use the old names by setting an alias.
	 *
	 * While in deprecation period both classes will work. When the deprecation
	 * period ends, the alies should be removed from the list of aliases.
	 *
	 * Usage:
	 *
	 *  self::$classes_aliases = [
	 *    'Namespace\OldClassName' => [
	 *      'replacement' => 'Namespace\NewClassName',
	 *      'version' => '3.0.0',
	 *    ],
	 *    'Namespace\OldModule\ClassName' => [
	 *      'replacement' => 'Namespace\NewModule\ClassName',
	 *      'version' => '3.5.0',
	 *    ],
	 *  ];
	 *
	 * @access private
	 * @static
	 *
	 * @return void
	 */
	private static function init_classes_aliases() {
		self::$classes_aliases = [
			'System_Info\Main' => [
				'replacement' => 'Modules\System_Info\Module',
				'version' => '2.9.0',
			],
			'System_Info\Classes\Abstracts\Base_Reporter' => [
				'replacement' => 'Modules\System_Info\Reporters\Base',
				'version' => '2.9.0',
			],
			'System_Info\Classes\Server_Reporter' => [
				'replacement' => 'Modules\System_Info\Reporters\Server',
				'version' => '2.9.0',
			],
			'System_Info\Classes\MU_Plugins_Reporter' => [
				'replacement' => 'Modules\System_Info\Reporters\MU_Plugins',
				'version' => '2.9.0',
			],
			'System_Info\Classes\Network_Plugins_Reporter' => [
				'replacement' => 'Modules\System_Info\Reporters\Network_Plugins',
				'version' => '2.9.0',
			],
			'System_Info\Classes\Plugins_Reporter' => [
				'replacement' => 'Modules\System_Info\Reporters\Plugins',
				'version' => '2.9.0',
			],
			'System_Info\Classes\Theme_Reporter' => [
				'replacement' => 'Modules\System_Info\Reporters\Theme',
				'version' => '2.9.0',
			],
			'System_Info\Classes\User_Reporter' => [
				'replacement' => 'Modules\System_Info\Reporters\User',
				'version' => '2.9.0',
			],
			'System_Info\Helpers\Model_Helper' => [
				'replacement' => 'Modules\System_Info\Helpers\Model_Helper',
				'version' => '2.9.0',
			],
		];
	}

	/**
	 * Load class.
	 *
	 * For a given class name, require the class file.
	 *
	 * @since 1.6.0
	 * @access private
	 * @static
	 *
	 * @param string $relative_class_name Class name.
	 */
	private static function load_class( $relative_class_name ) {
		$classes_map = self::get_classes_map();

		if ( isset( $classes_map[ $relative_class_name ] ) ) {
			$filename = self::$default_path . '/' . $classes_map[ $relative_class_name ];
		} else {
			$filename = strtolower(
				preg_replace(
					[ '/([a-z])([A-Z])/', '/_/', '/\\\/' ],
					[ '$1-$2', '-', DIRECTORY_SEPARATOR ],
					$relative_class_name
				)
			);

			$filename = self::$default_path . $filename . '.php';
		}

		if ( is_readable( $filename ) ) {
			require $filename;
		}
	}

	/**
	 * Autoload.
	 *
	 * For a given class, check if it exist and load it.
	 *
	 * @since 1.6.0
	 * @access private
	 * @static
	 *
	 * @param string $class_name Class name.
	 */
	private static function autoload( $class_name ) {
		if ( 0 !== strpos( $class_name, self::$default_namespace . '\\' ) ) {
			return;
		}

		$relative_class_name = preg_replace( '/^' . self::$default_namespace . '\\\/', '', $class_name );

		$classes_aliases = self::get_classes_aliases();

		$has_class_alias = isset( $classes_aliases[ $relative_class_name ] );

		// Backward Compatibility: Save old class name for set an alias after the new class is loaded.
		if ( $has_class_alias ) {
			$alias_data = $classes_aliases[ $relative_class_name ];

			$relative_class_name = $alias_data['replacement'];
		}

		$final_class_name = self::$default_namespace . '\\' . $relative_class_name;

		if ( ! class_exists( $final_class_name ) ) {
			self::load_class( $relative_class_name );
		}

		if ( $has_class_alias ) {
			class_alias( $final_class_name, $class_name );

			Utils::handle_deprecation( $class_name, $alias_data['version'], $final_class_name );
		}
	}
}
