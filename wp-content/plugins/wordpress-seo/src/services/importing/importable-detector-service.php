<?php

namespace Yoast\WP\SEO\Services\Importing;

use Yoast\WP\SEO\Actions\Importing\Importing_Action_Interface;

/**
 * Detects if any data from other SEO plugins is available for importing.
 */
class Importable_Detector_Service {

	/**
	 * All known import actions
	 *
	 * @var array|Importing_Action_Interface[]
	 */
	protected $importers;

	/**
	 * Importable_Detector_Service constructor.
	 *
	 * @param Importing_Action_Interface ...$importers All of the known importers.
	 */
	public function __construct( Importing_Action_Interface ...$importers ) {
		$this->importers = $importers;
	}

	/**
	 * Returns the detected importers that have data to work with.
	 *
	 * @param string|null $plugin The plugin name of the importer.
	 * @param string|null $type   The type of the importer.
	 *
	 * @return array The detected importers that have data to work with.
	 */
	public function detect_importers( $plugin = null, $type = null ) {
		$detectors = $this->filter_actions( $this->importers, $plugin, $type );

		$detected = [];
		foreach ( $detectors as $detector ) {
			if ( $detector->is_enabled() && $detector->get_type() !== 'cleanup' && ! $detector->get_completed() && $detector->get_limited_unindexed_count( 1 ) > 0 ) {
				$detected[ $detector->get_plugin() ][] = $detector->get_type();
			}
		}

		return $detected;
	}

	/**
	 * Returns the detected cleanups that have data to work with.
	 *
	 * @param string|null $plugin The plugin name of the cleanup.
	 *
	 * @return array The detected importers that have data to work with.
	 */
	public function detect_cleanups( $plugin = null ) {
		$detectors = $this->filter_actions( $this->importers, $plugin, 'cleanup' );

		$detected = [];
		foreach ( $detectors as $detector ) {
			if ( $detector->is_enabled() && ! $detector->get_completed() && $detector->get_limited_unindexed_count( 1 ) > 0 ) {
				$detected[ $detector->get_plugin() ][] = $detector->get_type();
			}
		}

		return $detected;
	}

	/**
	 * Filters all import actions from a list that do not match the given Plugin or Type.
	 *
	 * @param Importing_Action_Interface[] $all_actions The complete list of actions.
	 * @param string|null                  $plugin      The Plugin name whose actions to keep.
	 * @param string|null                  $type        The type of actions to keep.
	 *
	 * @return array
	 */
	public function filter_actions( $all_actions, $plugin = null, $type = null ) {
		return \array_filter(
			$all_actions,
			static function ( $action ) use ( $plugin, $type ) {
				return $action->is_compatible_with( $plugin, $type );
			}
		);
	}
}
