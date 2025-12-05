<?php

namespace Yoast\WP\SEO\Helpers;

/**
 * A helper object for author archives.
 */
class Asset_Helper {

	/**
	 * Recursively retrieves all dependency urls of a given handle.
	 *
	 * @param string $handle The handle.
	 *
	 * @return string[] All dependency urls of the given handle.
	 */
	public function get_dependency_urls_by_handle( $handle ) {
		$urls = [];

		foreach ( $this->get_dependency_handles( $handle ) as $other_handle ) {
			$urls[ $other_handle ] = $this->get_asset_url( $other_handle );
		}

		return $urls;
	}

	/**
	 * Recursively retrieves all dependencies of a given handle.
	 *
	 * @param string $handle The handle.
	 *
	 * @return string[] All dependencies of the given handle.
	 */
	public function get_dependency_handles( $handle ) {
		$scripts = \wp_scripts();

		if ( ! isset( $scripts->registered[ $handle ] ) ) {
			return [];
		}

		$obj  = $scripts->registered[ $handle ];
		$deps = $obj->deps;
		foreach ( $obj->deps as $other_handle ) {
			$nested_deps = $this->get_dependency_handles( $other_handle );
			if ( ! $nested_deps ) {
				continue;
			}

			// Place nested dependencies before primary dependencies, they need to be loaded first.
			$deps = \array_merge( $nested_deps, $deps );
		}

		// Array unique keeps the first of each element so dependencies will be as early as they're required.
		return \array_values( \array_unique( $deps ) );
	}

	/**
	 * Gets the URL of a given asset.
	 *
	 * This logic is copied from WP_Scripts::do_item as unfortunately that logic is not properly isolated.
	 *
	 * @param string $handle The handle of the asset.
	 *
	 * @return string|false The URL of the asset or false if the asset does not exist.
	 */
	public function get_asset_url( $handle ) {
		$scripts = \wp_scripts();

		if ( ! isset( $scripts->registered[ $handle ] ) ) {
			return false;
		}

		$obj = $scripts->registered[ $handle ];

		if ( $obj->ver === null ) {
			$ver = '';
		}
		else {
			$ver = ( $obj->ver ) ? $obj->ver : $scripts->default_version;
		}
		if ( isset( $scripts->args[ $handle ] ) ) {
			$ver = ( $ver ) ? $ver . '&amp;' . $scripts->args[ $handle ] : $scripts->args[ $handle ];
		}

		$src = $obj->src;

		if ( ! \preg_match( '|^(https?:)?//|', $src ) && ! ( $scripts->content_url && \strpos( $src, $scripts->content_url ) === 0 ) ) {
			$src = $scripts->base_url . $src;
		}

		if ( ! empty( $ver ) ) {
			$src = \add_query_arg( 'ver', $ver, $src );
		}

		/** This filter is documented in wp-includes/class.wp-scripts.php */
		return \esc_url( \apply_filters( 'script_loader_src', $src, $handle ) );
	}
}
