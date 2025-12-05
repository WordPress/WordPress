<?php

namespace Elementor\Modules\GlobalClasses\Usage;

use Elementor\Modules\GlobalClasses\Global_Classes_Repository;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Collects and exposes usage data for all global CSS classes across Elementor documents.
 */
class Applied_Global_Classes_Usage {

	/**
	 * Document types that should be excluded from usage reporting.
	 *
	 * @var string[]
	 */
	private array $excluded_types = [ 'e-flexbox', 'template' ];

	/**
	 * Tracks usage for each global class.
	 *
	 * @var array<string, Css_Class_Usage>
	 */
	private array $class_usages = [];

	/**
	 * Returns the total usage count per class ID (excluding template-only classes).
	 *
	 * @return array<string, int>
	 */
	public function get(): array {
		$this->build_class_usages();

		$result = [];
		foreach ( $this->class_usages as $class_id => $usage ) {
			if ( $usage->get_total_usage() > 0 ) {
				$result[ $class_id ] = $usage->get_total_usage();
			}
		}

		return $result;
	}

	/**
	 * Returns detailed usage information per class ID.
	 * Each class ID maps to a list of document usages (excluding excluded types).
	 *
	 * @return array<string, array{
	 *     pageId: int,
	 *     title: string,
	 *     type: string,
	 *     total: int,
	 *     elements: string[]
	 * }>
	 */
	public function get_detailed_usage(): array {
		$this->build_class_usages();

		$result = [];

		foreach ( $this->class_usages as $class_id => $usage ) {
			$pages = $usage->get_pages();

			$filtered_pages = array_filter(
				$pages,
				fn( $page_data ) => ! in_array( $page_data['type'], $this->excluded_types, true )
			);

			if ( empty( $filtered_pages ) ) {
				continue;
			}

			foreach ( $filtered_pages as $page_id => $page_data ) {
				$result[ $class_id ][] = [
					'pageId'   => $page_id,
					'title'    => $page_data['title'],
					'type'     => $page_data['type'],
					'total'    => $page_data['total'],
					'elements' => $page_data['elements'],
				];
			}
		}

		return $result;
	}

	/**
	 * Builds the internal usage map from all Elementor documents.
	 *
	 * This method initializes and aggregates class usage from all relevant documents,
	 * merging duplicate class IDs found in multiple pages.
	 */
	private function build_class_usages(): void {
		$this->class_usages = [];

		$class_ids = Global_Classes_Repository::make()
												->all()
												->get_items()
												->keys()
												->all();

		Plugin::$instance->db->iterate_elementor_documents(
			function ( $document ) use ( $class_ids ) {
				$usage = new Document_Usage( $document );
				$usage->analyze();

				foreach ( $usage->get_usages() as $class_id => $class_usage ) {
					if ( ! in_array( $class_id, $class_ids, true ) ) {
						continue;
					}

					if ( ! isset( $this->class_usages[ $class_id ] ) ) {
						$this->class_usages[ $class_id ] = $class_usage;
					} else {
						$this->class_usages[ $class_id ]->merge( $class_usage );
					}
				}
			}
		);
	}
}
