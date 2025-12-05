<?php

namespace Elementor\Core\Utils\ImportExport;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Url {

	/**
	 * Migrate url to the current permalink structure.
	 * The function will also check and change absolute url to relative one by the base url.
	 * This is currently supports only "Post Name" permalink structure to any permalink structure.
	 *
	 * @param string      $url The url that should be migrated.
	 * @param string|Null $base_url The base url that should be clean from the url.
	 *
	 * @return string The migrated url || the $url if it couldn't find a match in the current permalink structure.
	 */
	public static function migrate( $url, $base_url = '' ) {
		$full_url = $url;

		if ( ! empty( $base_url ) ) {
			$base_url = preg_quote( $base_url, '/' );
			$url = preg_replace( "/^{$base_url}/", '', $url );
		}

		$parsed_url = wp_parse_url( $url );

		if ( $url === $full_url && ! empty( $parsed_url['host'] ) ) {
			return $full_url;
		}

		if ( ! empty( $parsed_url['path'] ) ) {
			$page = get_page_by_path( $parsed_url['path'] );

			if ( ! $page ) {
				return $full_url;
			}

			$permalink = get_permalink( $page->ID );
		}

		if ( empty( $permalink ) ) {
			return $full_url;
		}

		if ( ! empty( $parsed_url['query'] ) ) {
			parse_str( $parsed_url['query'], $parsed_query );

			// Clean WP permalinks query args to prevent collision with the new permalink.
			unset( $parsed_query['p'] );
			unset( $parsed_query['page_id'] );

			$permalink = add_query_arg( $parsed_query, $permalink );
		}

		if ( ! empty( $parsed_url['fragment'] ) ) {
			$permalink .= '#' . $parsed_url['fragment'];
		}

		return wp_make_link_relative( $permalink );
	}
}
