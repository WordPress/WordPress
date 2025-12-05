<?php

namespace Yoast\WP\SEO\User_Meta\Application;

use Yoast\WP\SEO\User_Meta\Infrastructure\Cleanup_Repository;

/**
 * Service with all usermeta cleanup queries.
 */
class Cleanup_Service {

	/**
	 * The additional contactmethods collector.
	 *
	 * @var Additional_Contactmethods_Collector
	 */
	private $additional_contactmethods_collector;

	/**
	 * The custom meta collector.
	 *
	 * @var Custom_Meta_Collector
	 */
	private $custom_meta_collector;

	/**
	 * The cleanup repository.
	 *
	 * @var Cleanup_Repository
	 */
	private $cleanup_repository;

	/**
	 * The constructor.
	 *
	 * @param Additional_Contactmethods_Collector $additional_contactmethods_collector The additional contactmethods collector.
	 * @param Custom_Meta_Collector               $custom_meta_collector               The custom meta collector.
	 * @param Cleanup_Repository                  $cleanup_repository                  The cleanup repository.
	 */
	public function __construct(
		Additional_Contactmethods_Collector $additional_contactmethods_collector,
		Custom_Meta_Collector $custom_meta_collector,
		Cleanup_Repository $cleanup_repository
	) {
		$this->additional_contactmethods_collector = $additional_contactmethods_collector;
		$this->custom_meta_collector               = $custom_meta_collector;
		$this->cleanup_repository                  = $cleanup_repository;
	}

	/**
	 * Deletes selected empty usermeta.
	 *
	 * @param int $limit The limit we'll apply to the cleanups.
	 *
	 * @return int|bool The number of rows that was deleted or false if the query failed.
	 */
	public function cleanup_selected_empty_usermeta( int $limit ) {
		$meta_to_check = $this->get_meta_to_check();

		return $this->cleanup_repository->delete_empty_usermeta_query( $meta_to_check, $limit );
	}

	/**
	 * Gets which meta are going to be checked for emptiness.
	 *
	 * @return array<string> The meta to be checked for emptiness.
	 */
	private function get_meta_to_check() {
		$additional_contactmethods = $this->additional_contactmethods_collector->get_additional_contactmethods_keys();
		$custom_meta               = $this->custom_meta_collector->get_non_empty_custom_meta();

		return \array_merge( $additional_contactmethods, $custom_meta );
	}
}
