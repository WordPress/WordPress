<?php
namespace Elementor\Modules\Home\Classes;

use Elementor\Core\Isolation\Wordpress_Adapter;
use Elementor\Core\Isolation\Plugin_Status_Adapter;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Transformations_Manager {

	private static $cached_data = [];

	private const TRANSFORMATIONS = [
		'Create_New_Page_Url',
		'Filter_Plugins',
		'Filter_Get_Started_By_License',
		'Filter_Sidebar_Promotion_By_License',
		'Filter_Condition_Introduction_Meta',
		'Create_Site_Settings_Url',
		'Filter_Top_Section_By_License',
	];

	protected array $home_screen_data;

	protected Wordpress_Adapter $wordpress_adapter;

	protected Plugin_Status_Adapter $plugin_status_adapter;

	protected array $transformation_classes = [];

	public function __construct( $home_screen_data ) {
		$this->home_screen_data = $home_screen_data;
		$this->wordpress_adapter = new Wordpress_Adapter();
		$this->plugin_status_adapter = new Plugin_Status_Adapter( $this->wordpress_adapter );
		$this->transformation_classes = $this->get_transformation_classes();
	}

	public function run_transformations(): array {
		if ( ! empty( self::$cached_data ) ) {
			return self::$cached_data;
		}

		$transformations = self::TRANSFORMATIONS;

		foreach ( $transformations as $transformation_id ) {
			$this->home_screen_data = $this->transformation_classes[ $transformation_id ]->transform( $this->home_screen_data );
		}

		self::$cached_data = $this->home_screen_data;

		return $this->home_screen_data;
	}

	private function get_transformation_classes(): array {
		$classes = [];

		$transformations = self::TRANSFORMATIONS;

		$arguments = [
			'wordpress_adapter' => $this->wordpress_adapter,
			'plugin_status_adapter' => $this->plugin_status_adapter,
		];

		foreach ( $transformations as $transformation_id ) {
			$class_name = '\\Elementor\\Modules\\Home\\Transformations\\' . $transformation_id;
			$classes[ $transformation_id ] = new $class_name( $arguments );
		}

		return $classes;
	}
}
