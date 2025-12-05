<?php

namespace Yoast\WP\SEO\User_Meta\User_Interface;

use Yoast\WP\SEO\Conditionals\No_Conditionals;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\User_Meta\Application\Additional_Contactmethods_Collector;

/**
 * Handles registering and saving additional contactmethods for users.
 */
class Additional_Contactmethods_Integration implements Integration_Interface {

	use No_Conditionals;

	/**
	 * The additional contactmethods collector.
	 *
	 * @var Additional_Contactmethods_Collector
	 */
	private $additional_contactmethods_collector;

	/**
	 * The constructor.
	 *
	 * @param Additional_Contactmethods_Collector $additional_contactmethods_collector The additional contactmethods collector.
	 */
	public function __construct( Additional_Contactmethods_Collector $additional_contactmethods_collector ) {
		$this->additional_contactmethods_collector = $additional_contactmethods_collector;
	}

	/**
	 * Registers action hook.
	 *
	 * @return void
	 */
	public function register_hooks(): void {
		\add_filter( 'user_contactmethods', [ $this, 'update_contactmethods' ] );
		\add_filter( 'update_user_metadata', [ $this, 'stop_storing_empty_metadata' ], 10, 4 );
	}

	/**
	 * Updates the contactmethods with an additional set of social profiles.
	 *
	 * These are used with the Facebook author, rel="author", Twitter cards implementation, but also in the `sameAs` schema attributes.
	 *
	 * @param array<string, string> $contactmethods Currently set contactmethods.
	 *
	 * @return array<string, string> Contactmethods with added contactmethods.
	 */
	public function update_contactmethods( $contactmethods ) {
		$additional_contactmethods = $this->additional_contactmethods_collector->get_additional_contactmethods_objects();

		return \array_merge( ( $contactmethods ?? [] ), $additional_contactmethods );
	}

	/**
	 * Returns a check value, which will stop empty contactmethods from going into the database.
	 *
	 * @param bool|null $check      Whether to allow updating metadata for the given type.
	 * @param int       $object_id  ID of the object metadata is for.
	 * @param string    $meta_key   Metadata key.
	 * @param mixed     $meta_value Metadata value. Must be serializable if non-scalar.
	 *
	 * @return false|null False for when we are to filter out empty metadata, null for no filtering.
	 */
	public function stop_storing_empty_metadata( $check, $object_id, $meta_key, $meta_value ) {
		$additional_contactmethods = $this->additional_contactmethods_collector->get_additional_contactmethods_keys();

		if ( \in_array( $meta_key, $additional_contactmethods, true ) && $meta_value === '' ) {
			\delete_user_meta( $object_id, $meta_key );
			return false;
		}

		return $check;
	}
}
