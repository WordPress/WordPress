<?php
namespace Elementor\Modules\CompatibilityTag;

use Elementor\Plugin;
use Elementor\Core\Utils\Version;
use Elementor\Core\Utils\Collection;
use Elementor\Core\Base\Module as BaseModule;
use Elementor\Modules\System_Info\Module as System_Info;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Base_Module extends BaseModule {
	const MODULE_NAME = 'compatibility-tag';

	/**
	 * @var Compatibility_Tag
	 */
	private $compatibility_tag_service;

	/**
	 * @return string
	 */
	public function get_name() {
		return static::MODULE_NAME;
	}

	/**
	 * @return Compatibility_Tag
	 */
	private function get_compatibility_tag_service() {
		if ( ! $this->compatibility_tag_service ) {
			$this->compatibility_tag_service = new Compatibility_Tag( $this->get_plugin_header() );
		}

		return $this->compatibility_tag_service;
	}

	/**
	 * Add allowed headers to plugins.
	 *
	 * @param array $headers
	 * @param       $compatibility_tag_header
	 *
	 * @return array
	 */
	protected function enable_elementor_headers( array $headers, $compatibility_tag_header ) {
		$headers[] = $compatibility_tag_header;

		return $headers;
	}

	/**
	 * @return Collection
	 */
	protected function get_plugins_to_check() {
		return $this->get_plugins_with_header();
	}

	/**
	 * Append a compatibility message to the update plugin warning.
	 *
	 * @param array $args
	 */
	protected function on_plugin_update_message( array $args ) {
		$new_version = Version::create_from_string( $args['new_version'] );

		if ( $new_version->compare( '=', $args['Version'], Version::PART_MAJOR_2 ) ) {
			return;
		}

		$plugins = $this->get_plugins_to_check();
		$plugins_compatibility = $this->get_compatibility_tag_service()->check( $new_version, $plugins->keys()->all() );

		$plugins = $plugins->filter( function ( $data, $plugin_name ) use ( $plugins_compatibility ) {
			return Compatibility_Tag::COMPATIBLE !== $plugins_compatibility[ $plugin_name ];
		} );

		if ( $plugins->is_empty() ) {
			return;
		}

		include __DIR__ . '/views/plugin-update-message-compatibility.php';
	}

	/**
	 * Get all plugins with specific header.
	 *
	 * @return Collection
	 */
	private function get_plugins_with_header() {
		return Plugin::$instance->wp
			->get_plugins()
			->filter( function ( array $plugin ) {
				return ! empty( $plugin[ $this->get_plugin_header() ] );
			} );
	}

	/**
	 * @return string
	 */
	abstract protected function get_plugin_header();

	/**
	 * @return string
	 */
	abstract protected function get_plugin_label();

	/**
	 * @return string
	 */
	abstract protected function get_plugin_name();

	/**
	 * @return string
	 */
	abstract protected function get_plugin_version();

	/**
	 * Base_Module constructor.
	 */
	public function __construct() {
		add_filter( 'extra_plugin_headers', function ( array $headers ) {
			return $this->enable_elementor_headers( $headers, $this->get_plugin_header() );
		} );

		add_action( 'in_plugin_update_message-' . $this->get_plugin_name(), function ( array $args ) {
			$this->on_plugin_update_message( $args );
		}, 11 /* After the warning message for backup */ );

		add_action( 'elementor/system_info/get_allowed_reports', function () {
			$plugin_short_name = basename( $this->get_plugin_name(), '.php' );

			System_Info::add_report(
				"{$plugin_short_name}_compatibility",
				[
					'file_name' => __DIR__ . '/compatibility-tag-report.php',
					'class_name' => __NAMESPACE__ . '\Compatibility_Tag_Report',
					'fields' => [
						'compatibility_tag_service' => $this->get_compatibility_tag_service(),
						'plugin_label' => $this->get_plugin_label(),
						'plugin_version' => Version::create_from_string( $this->get_plugin_version() ),
						'plugins_to_check' => $this->get_plugins_to_check()
							->only( get_option( 'active_plugins' ) )
							->keys()
							->all(),
					],
				]
			);
		} );
	}
}
