<?php

namespace Yoast\WP\SEO\Introductions\Application;

trait Version_Trait {

	/**
	 * Determines whether the version is between a min (inclusive) and max (exclusive).
	 *
	 * @param string $version     The version to compare.
	 * @param string $min_version The minimum version.
	 * @param string $max_version The maximum version.
	 *
	 * @return bool Whether the version is between a min and max.
	 */
	private function is_version_between( $version, $min_version, $max_version ) {
		return ( \version_compare( $version, $min_version, '>=' )
			&& \version_compare( $version, $max_version, '<' )
		);
	}
}
