<?php

namespace Elementor\Modules\GlobalClasses\Usage;

/**
 * Tracks usage of a specific global CSS class across multiple Elementor documents.
 */
class Css_Class_Usage {

	/** @var string */
	private string $class_id;

	/** @var int */
	private int $total = 0;

	/**
	 * @var array<int, array{
	 *     title: string,
	 *     total: int,
	 *     elements: string[],
	 *     type?: string
	 * }>
	 */
	private array $pages = [];

	/**
	 * Constructor.
	 *
	 * @param string $class_id Global CSS class ID.
	 */
	public function __construct( string $class_id ) {
		$this->class_id = $class_id;
	}

	/**
	 * Track usage of this class on a specific document and element.
	 *
	 * @param int         $page_id        Document ID.
	 * @param string      $page_title     Document title.
	 * @param string      $element_id     Element ID using this class.
	 * @param string|null $document_type  Optional document type (e.g. header, footer, etc).
	 */
	public function track_usage( int $page_id, string $page_title, string $element_id, ?string $document_type = null ): void {
		++$this->total;

		if ( ! isset( $this->pages[ $page_id ] ) ) {
			$this->pages[ $page_id ] = [
				'title'    => $page_title,
				'total'    => 0,
				'elements' => [],
			];

			if ( $document_type ) {
				$this->pages[ $page_id ]['type'] = $document_type;
			}
		}

		++$this->pages[ $page_id ]['total'];
		$this->pages[ $page_id ]['elements'][] = $element_id;
	}

	/**
	 * Merge usage data from another instance with the same class ID.
	 *
	 * @param Css_Class_Usage $other The other usage object to merge in.
	 *
	 * @throws \InvalidArgumentException If the class IDs do not match.
	 */
	public function merge( Css_Class_Usage $other ): void {
		if ( $other->get_class_id() !== $this->class_id ) {
			throw new \InvalidArgumentException( 'Mismatched class ID' );
		}

		$this->total += $other->get_total_usage();

		foreach ( $other->get_pages() as $page_id => $data ) {
			if ( ! isset( $this->pages[ $page_id ] ) ) {
				$this->pages[ $page_id ] = $data;
			} else {
				$this->pages[ $page_id ]['total'] += $data['total'];
				$this->pages[ $page_id ]['elements'] = array_merge(
					$this->pages[ $page_id ]['elements'],
					$data['elements']
				);

				if ( empty( $this->pages[ $page_id ]['type'] ) && ! empty( $data['type'] ) ) {
					$this->pages[ $page_id ]['type'] = $data['type'];
				}
			}
		}
	}

	/**
	 * Get the global class ID this instance tracks.
	 *
	 * @return string
	 */
	public function get_class_id(): string {
		return $this->class_id;
	}

	/**
	 * Get the total number of elements using this class.
	 *
	 * @return int
	 */
	public function get_total_usage(): int {
		return $this->total;
	}

	/**
	 * Get a map of document usages.
	 *
	 * @return array<int, array{title: string, total: int, elements: string[], type?: string}>
	 */
	public function get_pages(): array {
		return $this->pages;
	}
}
