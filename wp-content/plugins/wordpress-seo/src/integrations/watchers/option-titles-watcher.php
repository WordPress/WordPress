<?php

namespace Yoast\WP\SEO\Integrations\Watchers;

use Yoast\WP\Lib\Model;
use Yoast\WP\SEO\Conditionals\Migrations_Conditional;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\WordPress\Wrapper;

/**
 * Watcher for the titles option.
 *
 * Represents the option titles watcher.
 */
class Option_Titles_Watcher implements Integration_Interface {

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'update_option_wpseo_titles', [ $this, 'check_option' ], 10, 2 );
	}

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return array<Migrations_Conditional>
	 */
	public static function get_conditionals() {
		return [ Migrations_Conditional::class ];
	}

	/**
	 * Checks if one of the relevant options has been changed.
	 *
	 * @param array<string|int|bool> $old_value The old value of the option.
	 * @param array<string|int|bool> $new_value The new value of the option.
	 *
	 * @return bool Whether or not the ancestors are removed.
	 */
	public function check_option( $old_value, $new_value ) {
		// If this is the first time saving the option, thus when value is false.
		if ( $old_value === false ) {
			$old_value = [];
		}

		if ( ! \is_array( $old_value ) || ! \is_array( $new_value ) ) {
			return false;
		}

		$relevant_keys = $this->get_relevant_keys();
		if ( empty( $relevant_keys ) ) {
			return false;
		}

		$post_types = [];

		foreach ( $relevant_keys as $post_type => $relevant_option ) {
			// If both values aren't set they haven't changed.
			if ( ! isset( $old_value[ $relevant_option ] ) && ! isset( $new_value[ $relevant_option ] ) ) {
				continue;
			}

			if ( $old_value[ $relevant_option ] !== $new_value[ $relevant_option ] ) {
				$post_types[] = $post_type;
			}
		}

		return $this->delete_ancestors( $post_types );
	}

	/**
	 * Retrieves the relevant keys.
	 *
	 * @return array<string> Array with the relevant keys.
	 */
	protected function get_relevant_keys() {
		$post_types = \get_post_types( [ 'public' => true ], 'names' );
		if ( ! \is_array( $post_types ) || $post_types === [] ) {
			return [];
		}

		$relevant_keys = [];
		foreach ( $post_types as $post_type ) {
			$relevant_keys[ $post_type ] = 'post_types-' . $post_type . '-maintax';
		}

		return $relevant_keys;
	}

	/**
	 * Removes the ancestors for given post types.
	 *
	 * @param array<string> $post_types The post types to remove hierarchy for.
	 *
	 * @return bool True when delete query was successful.
	 */
	protected function delete_ancestors( $post_types ) {
		if ( empty( $post_types ) ) {
			return false;
		}

		$wpdb            = Wrapper::get_wpdb();
		$hierarchy_table = Model::get_table_name( 'Indexable_Hierarchy' );
		$indexable_table = Model::get_table_name( 'Indexable' );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Delete query.
		$result = $wpdb->query(
			$wpdb->prepare(
				"
				DELETE FROM %i
				WHERE indexable_id IN(
					SELECT id
					FROM %i
					WHERE object_type = 'post'
					AND object_sub_type IN( " . \implode( ', ', \array_fill( 0, \count( $post_types ), '%s' ) ) . ' )
				)',
				$hierarchy_table,
				$indexable_table,
				...$post_types
			)
		);

		return $result !== false;
	}
}
