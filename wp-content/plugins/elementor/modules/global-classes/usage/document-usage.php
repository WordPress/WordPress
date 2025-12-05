<?php

namespace Elementor\Modules\GlobalClasses\Usage;

use Elementor\Plugin;
use Elementor\Core\Base\Document as ElementorDocument;
use Elementor\Modules\GlobalClasses\Global_Classes_Repository;

/**
 * Tracks usage of global CSS classes within a specific Elementor document.
 */
class Document_Usage {


	/** @var ElementorDocument */
	private ElementorDocument $document;

	/** @var array<string, Css_Class_Usage> */
	private array $usages = [];

	/**
	 * Constructor.
	 *
	 * @param ElementorDocument $document The Elementor document object.
	 */
	public function __construct( ElementorDocument $document ) {
		$this->document = $document;
	}

	/**
	 * Analyze the document to find and record usage of global CSS classes.
	 */
	public function analyze(): void {
		$page_id       = $this->document->get_main_id();
		$page_title    = $this->document->get_post()->post_title;
		$class_ids     = $this->get_all_global_class_ids();
		$elements_data = $this->document->get_elements_raw_data() ?? [];

		$document_type = $this->document->get_type();
		if ( empty( $document_type ) ) {
			$document_type = get_post_type( $page_id ) ?? 'unknown';
		}

		if ( empty( $elements_data ) ) {
			return;
		}

		Plugin::$instance->db->iterate_data(
			$elements_data,
			function ( $element_data ) use ( $class_ids, $page_id, $page_title, $document_type ) {
				$class_values = $element_data['settings']['classes']['value'] ?? [];

				if ( empty( $class_values ) || ! is_array( $class_values ) ) {
					return;
				}

				foreach ( $class_values as $class_id ) {
					if ( ! in_array( $class_id, $class_ids, true ) ) {
						continue;
					}

					if ( ! isset( $this->usages[ $class_id ] ) ) {
						$this->usages[ $class_id ] = new Css_Class_Usage( $class_id );
					}

					$this->usages[ $class_id ]->track_usage(
						$page_id,
						$page_title,
						$element_data['id'] ?? 'unknown',
						$document_type
					);
				}
			}
		);
	}

	/**
	 * Get all recorded usages of global CSS classes in this document.
	 *
	 * @return array<string, Css_Class_Usage>
	 */
	public function get_usages(): array {
		return $this->usages;
	}

	/**֜
	 * Retrieve all registered global CSS class IDs.
	 *
	 * @return string[]
	 */
	protected function get_all_global_class_ids(): array {
		return Global_Classes_Repository::make()
			->all()
			->get_items()
			->filter( fn( $item ) => ! empty( $item['id'] ?? null ) )
			->keys()
			->all();
	}
}
