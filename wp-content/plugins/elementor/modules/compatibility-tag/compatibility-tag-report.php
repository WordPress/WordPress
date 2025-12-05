<?php
namespace Elementor\Modules\CompatibilityTag;

use Elementor\Plugin;
use Elementor\Core\Utils\Version;
use Elementor\Core\Utils\Collection;
use Elementor\Modules\System_Info\Reporters\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Compatibility_Tag_Report extends Base {
	/**
	 * @var Compatibility_Tag
	 */
	protected $compatibility_tag_service;

	/**
	 * @var Version
	 */
	protected $plugin_version;

	/**
	 * @var string
	 */
	protected $plugin_label;

	/**
	 * @var array
	 */
	protected $plugins_to_check;

	/**
	 * Compatibility_Tag_Report constructor.
	 *
	 * @param $properties
	 */
	public function __construct( $properties ) {
		parent::__construct( $properties );

		$this->compatibility_tag_service = $this->_properties['fields']['compatibility_tag_service'];
		$this->plugin_label = $this->_properties['fields']['plugin_label'];
		$this->plugin_version = $this->_properties['fields']['plugin_version'];
		$this->plugins_to_check = $this->_properties['fields']['plugins_to_check'];
	}

	/**
	 * The title of the report
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->plugin_label . ' - Compatibility Tag';
	}

	/**
	 * Report fields
	 *
	 * @return string[]
	 */
	public function get_fields() {
		return [
			'report_data' => '',
		];
	}

	/**
	 * Report data.
	 *
	 * @return string[]
	 */
	public function get_report_data() {
		$compatibility_status = $this->compatibility_tag_service->check(
			$this->plugin_version,
			$this->plugins_to_check
		);

		return [
			'value' => $compatibility_status,
		];
	}

	public function get_html_report_data() {
		$compatibility_status = $this->compatibility_tag_service->check(
			$this->plugin_version,
			$this->plugins_to_check
		);

		$compatibility_status = $this->get_html_from_compatibility_status( $compatibility_status );

		return [
			'value' => $compatibility_status,
		];
	}

	public function get_raw_report_data() {
		$compatibility_status = $this->compatibility_tag_service->check(
			$this->plugin_version,
			$this->plugins_to_check
		);

		$compatibility_status = $this->get_raw_from_compatibility_status( $compatibility_status );

		return [
			'value' => $compatibility_status,
		];
	}

	/**
	 * Merge compatibility status with the plugins data.
	 *
	 * @param array $compatibility_status
	 *
	 * @return Collection
	 */
	private function merge_compatibility_status_with_plugins( array $compatibility_status ) {
		$labels = $this->get_report_labels();

		$compatibility_status = ( new Collection( $compatibility_status ) )
			->map( function ( $value ) use ( $labels ) {
				$status = isset( $labels[ $value ] ) ? $labels[ $value ] : esc_html__( 'Unknown', 'elementor' );

				return [ 'compatibility_status' => $status ];
			} );

		return Plugin::$instance->wp
			->get_plugins()
			->only( $compatibility_status->keys()->all() )
			->merge_recursive( $compatibility_status );
	}

	/**
	 * Format compatibility status into HTML.
	 *
	 * @param array $compatibility_status
	 *
	 * @return string
	 */
	private function get_html_from_compatibility_status( array $compatibility_status ) {
		return $this->merge_compatibility_status_with_plugins( $compatibility_status )
			->map( function ( array $plugin ) {
				return "<tr><td> {$plugin['Name']} </td><td> {$plugin['compatibility_status']} </td></tr>";
			} )
			->implode( '' );
	}

	/**
	 * Format compatibility status into raw string.
	 *
	 * @param array $compatibility_status
	 *
	 * @return string
	 */
	private function get_raw_from_compatibility_status( array $compatibility_status ) {
		return PHP_EOL . $this->merge_compatibility_status_with_plugins( $compatibility_status )
			->map( function ( array $plugin ) {
				return "\t{$plugin['Name']}: {$plugin['compatibility_status']}";
			} )
			->implode( PHP_EOL );
	}

	/**
	 * @return array
	 */
	private function get_report_labels() {
		return [
			Compatibility_Tag::COMPATIBLE   => esc_html__( 'Compatible', 'elementor' ),
			Compatibility_Tag::INCOMPATIBLE => esc_html__( 'Incompatible', 'elementor' ),
			Compatibility_Tag::HEADER_NOT_EXISTS => esc_html__( 'Compatibility not specified', 'elementor' ),
			Compatibility_Tag::INVALID_VERSION => esc_html__( 'Compatibility unknown', 'elementor' ),
			Compatibility_Tag::PLUGIN_NOT_EXISTS => esc_html__( 'Error', 'elementor' ),
		];
	}
}
