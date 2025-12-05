<?php

namespace Elementor\App\Modules\ImportExportCustomization\Runners\Import;

use Elementor\Core\Utils\Collection;
use Elementor\Core\Utils\Plugins_Manager;
use Elementor\Core\Utils\Str;

class Plugins extends Import_Runner_Base {

	/**
	 * @var Plugins_Manager
	 */
	private $plugins_manager;

	public function __construct( $plugins_manager = null ) {
		if ( $plugins_manager ) {
			$this->plugins_manager = $plugins_manager;
		} else {
			$this->plugins_manager = new Plugins_Manager();
		}
	}

	public static function get_name(): string {
		return 'plugins';
	}

	public function should_import( array $data ) {
		return (
			isset( $data['include'] ) &&
			in_array( 'plugins', $data['include'], true ) &&
			! empty( $data['manifest']['plugins'] ) &&
			! empty( $data['selected_plugins'] )
		);
	}

	public function import( array $data, array $imported_data ) {
		$customization = $data['customization']['plugins'] ?? null;

		if ( $customization ) {
			$enabled_plugin_keys = Collection::make( $customization )->filter()->keys();

			$plugins = Collection::make( $data['selected_plugins'] )
				->filter( function( $plugin_data, $plugin_key ) use ( $enabled_plugin_keys ) {
					return $enabled_plugin_keys->contains( $plugin_data['plugin'] );
				} )
				->values();
		} else {
			$plugins = $data['selected_plugins'];
		}

		$plugins_collection = ( new Collection( $plugins ) )
			->map( function ( $item ) {
				if ( ! Str::ends_with( $item['plugin'], '.php' ) ) {
					$item['plugin'] .= '.php';
				}
				return $item;
			} );

		$slugs = $plugins_collection
			->map( function ( $item ) {
				return $item['plugin'];
			} )
			->all();

		if ( ! function_exists( 'request_filesystem_credentials' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		}

		$installed = $this->plugins_manager->install( $slugs );
		$activated = $this->plugins_manager->activate( $installed['succeeded'] );

		$ordered_activated_plugins = $plugins_collection
			->filter( function ( $item ) use ( $activated ) {
				return in_array( $item['plugin'], $activated['succeeded'], true );
			} )
			->map( function ( $item ) {
				return $item['name'];
			} )
			->all();

		$result['plugins'] = $ordered_activated_plugins;

		return $result;
	}
}
