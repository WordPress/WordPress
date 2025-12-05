<?php

namespace Yoast\WP\SEO\Services\Indexables;

use Yoast\WP\SEO\Models\Indexable;
use Yoast\WP\SEO\Values\Indexables\Indexable_Builder_Versions;

/**
 * Handles version control for Indexables.
 */
class Indexable_Version_Manager {

	/**
	 * Stores the version of each Indexable type.
	 *
	 * @var Indexable_Builder_Versions The current versions of all indexable builders.
	 */
	protected $indexable_builder_versions;

	/**
	 * Indexable_Version_Manager constructor.
	 *
	 * @param Indexable_Builder_Versions $indexable_builder_versions The current versions of all indexable builders.
	 */
	public function __construct( Indexable_Builder_Versions $indexable_builder_versions ) {
		$this->indexable_builder_versions = $indexable_builder_versions;
	}

	/**
	 * Determines if an Indexable has a lower version than the builder for that Indexable's type.
	 *
	 * @param Indexable $indexable The Indexable to check.
	 *
	 * @return bool True if the given version is older than the current latest version.
	 */
	public function indexable_needs_upgrade( $indexable ) {
		if ( ( ! $indexable )
			|| ( ! \is_a( $indexable, Indexable::class ) )
		) {
			return false;
		}

		return $this->needs_upgrade( $indexable->object_type, $indexable->version );
	}

	/**
	 * Determines if an Indexable version for the type is lower than the current version for that Indexable type.
	 *
	 * @param string $object_type       The Indexable's object type.
	 * @param int    $indexable_version The Indexable's version.
	 *
	 * @return bool True if the given version is older than the current latest version.
	 */
	protected function needs_upgrade( $object_type, $indexable_version ) {
		$current_indexable_builder_version = $this->indexable_builder_versions->get_latest_version_for_type( $object_type );

		// If the Indexable's version is below the current version, that Indexable needs updating.
		return $indexable_version < $current_indexable_builder_version;
	}

	/**
	 * Sets an Indexable's version to the latest version.
	 *
	 * @param Indexable $indexable The Indexable to update.
	 *
	 * @return Indexable
	 */
	public function set_latest( $indexable ) {
		if ( ! $indexable ) {
			return $indexable;
		}

		$indexable->version = $this->indexable_builder_versions->get_latest_version_for_type( $indexable->object_type );

		return $indexable;
	}
}
