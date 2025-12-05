<?php

namespace WpMatomo\WpStatistics\Importers\Actions;

use DeviceDetector\Parser\Client\Browser;
use DeviceDetector\Parser\OperatingSystem;
use Piwik\Common;
use Piwik\Plugins\DevicesDetection\Archiver;
use Piwik\Date;
use WpMatomo\WpStatistics\DataConverters\BrowsersConverter;
use WpMatomo\WpStatistics\DataConverters\PlatformConverter;
/**
 * @package WpMatomo
 * @subpackage WpStatisticsImport
 */
class DeviceDetectionImporter extends RecordImporter implements ActionsInterface {

	const PLUGIN_NAME = 'DevicesDetection';

	public function import_records( Date $date ) {
		$this->importBrowsers( $date );
		$this->importPlateform( $date );
	}

	/**
	 * @param Date $date
	 *
	 * @return void
	 */
	private function importBrowsers( Date $date ) {
		$devices = $this->get_visitors( $date );
		if ( array_key_exists( 'no_data', $devices ) && $devices['no_data'] ) {
			$devices = array();
		}
		$this->convertBrowsersInMatomo( $devices );
		$devices = BrowsersConverter::convert( $devices );
		$this->logger->debug( 'Import {nb_browsers} browsers...', [ 'nb_browsers' => $devices->getRowsCount() ] );
		$this->insert_record( Archiver::BROWSER_RECORD_NAME, $devices );
		Common::destroy( $devices );
	}

	private function convertPlatformsInMatomo( &$visitors ) {
		// convert codification
		$platform_ids   = array_keys( OperatingSystem::getAvailableOperatingSystems() );
		$platform_names = array_values( OperatingSystem::getAvailableOperatingSystems() );
		// we do not have the version with wpstatistics, so set an empty version
		array_walk(
			$platform_ids,
			function( &$item1, $key ) {
				$item1 = $item1 . ';';
			}
		);
		$platform_ids   = array_merge( $platform_ids, [ 'MAC;OS X' ] );
		$platform_names = array_merge( $platform_names, [ 'OS X' ] );
		foreach ( $visitors as $id => $visitor ) {
			$platform = $this->get_platform( $visitor );
			if ( in_array( $platform, $platform_names, true ) ) {
				$visitors[ $id ]['platform'] = str_replace( $platform_names, $platform_ids, $platform );
			} else {
				$visitors[ $id ]['platform'] = 'UNK;UNK';
			}
		}
	}

	private function get_platform( $visitor ) {
		if ( isset( $visitor['os'] ) ) {
			return $visitor['os']['name'];
		}

		return $visitor['platform'];
	}

	private function convertBrowsersInMatomo( &$devices ) {
		// convert codification
		$device_ids   = array_keys( Browser::getAvailableBrowsers() );
		$device_names = array_values( Browser::getAvailableBrowsers() );
		// we do not have the version with wpstatistics, so set an empty version
		$device_ids   = array_merge( [ '', '', 'FM', 'MS', 'SB', 'IM' ], $device_ids );
		$device_names = array_merge( [ 'Microsoft Office', 'Unknown', 'Firefox Mobile', 'Silk', 'Samsung Internet', 'Mobile Internet Explorer' ], $device_names );
		foreach ( $devices as $id => $device ) {
			if ( in_array( $device['browser']['name'], $device_names, true ) ) {
				$devices[ $id ]['browser']['name'] = str_replace( $device_names, $device_ids, $device['browser']['name'] );
			}
		}
	}
	/**
	 * @param Date $date
	 */
	private function importPlateform( Date $date ) {
		$platforms = $this->get_visitors( $date );
		$this->convertPlatformsInMatomo( $platforms );
		$platforms = PlatformConverter::convert( $platforms );
		$this->logger->debug( 'Import {nb_platform} platforms...', [ 'nb_platform' => $platforms->getRowsCount() ] );
		$this->insert_record( Archiver::OS_VERSION_RECORD_NAME, $platforms );
		Common::destroy( $platforms );
	}
}
