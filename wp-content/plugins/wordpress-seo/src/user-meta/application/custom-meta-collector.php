<?php

namespace Yoast\WP\SEO\User_Meta\Application;

use Yoast\WP\SEO\User_Meta\Domain\Custom_Meta_Interface;

/**
 * The collector to get custom user meta.
 *
 * @makePublic
 */
class Custom_Meta_Collector {

	/**
	 * All custom meta.
	 *
	 * @var array<Custom_Meta_Interface>
	 */
	private $custom_meta;

	/**
	 * The constructor.
	 *
	 * @param Custom_Meta_Interface ...$custom_meta All custom meta.
	 */
	public function __construct( Custom_Meta_Interface ...$custom_meta ) {
		$this->custom_meta = $custom_meta;
	}

	/**
	 * Returns all the custom meta.
	 *
	 * @return array<Custom_Meta_Interface> All the custom meta.
	 */
	public function get_custom_meta(): array {
		return $this->custom_meta;
	}

	/**
	 * Returns all the custom meta, sorted by rendering priority.
	 *
	 * @return array<Custom_Meta_Interface> All the custom meta, sorted by rendering priority.
	 */
	public function get_sorted_custom_meta(): array {
		$custom_meta = $this->get_custom_meta();

		\usort(
			$custom_meta,
			static function ( Custom_Meta_Interface $a, Custom_Meta_Interface $b ) {
				return ( $a->get_render_priority() <=> $b->get_render_priority() );
			}
		);

		return $custom_meta;
	}

	/**
	 * Returns the custom meta that can't be empty.
	 *
	 * @return array<string> The custom meta that can't be empty.
	 */
	public function get_non_empty_custom_meta(): array {
		$non_empty_custom_meta = [];
		foreach ( $this->custom_meta as $custom_meta ) {
			if ( ! $custom_meta->is_empty_allowed() ) {
				$non_empty_custom_meta[] = $custom_meta->get_key();
			}
		}

		return $non_empty_custom_meta;
	}
}
