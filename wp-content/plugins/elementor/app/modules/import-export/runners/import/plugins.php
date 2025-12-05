<?php

namespace Elementor\App\Modules\ImportExport\Runners\Import;

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
		$plugins = $data['selected_plugins'];

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
