<?php

namespace Automattic\WooCommerce\Internal\Utilities;

/**
 * Provides an easy method of assessing URLs, including filepaths (which will be silently
 * converted to a file:// URL if provided).
 */
class URL {
	/**
	 * Components of the URL being assessed.
	 *
	 * The keys match those potentially returned by the parse_url() function, except
	 * that they are always defined and 'drive' (Windows drive letter) has been added.
	 *
	 * @var string|null[]
	 */
	private $components = array(
		'drive'    => null,
		'fragment' => null,
		'host'     => null,
		'pass'     => null,
		'path'     => null,
		'port'     => null,
		'query'    => null,
		'scheme'   => null,
		'user'     => null,
	);

	/**
	 * If the URL (or filepath) is absolute.
	 *
	 * @var bool
	 */
	private $is_absolute;

	/**
	 * If the URL (or filepath) represents a directory other than the root directory.
	 *
	 * This is useful at different points in the process, when deciding whether to re-apply
	 * a trailing slash at the end of processing or when we need to calculate how many
	 * directory traversals are needed to form a (grand-)parent URL.
	 *
	 * @var bool
	 */
	private $is_non_root_directory;

	/**
	 * The components of the URL's path.
	 *
	 * For instance, in the case of "file:///srv/www/wp.site" (noting that a file URL has
	 * no host component) this would contain:
	 *
	 *     [ "srv", "www", "wp.site" ]
	 *
	 * In the case of a non-file URL such as "https://example.com/foo/bar/baz" (noting the
	 * host is not part of the path) it would contain:
	 *
	 *    [ "foo", "bar", "baz" ]
	 *
	 * @var array
	 */
	private $path_parts = array();

	/**
	 * The URL.
	 *
	 * @var string
	 */
	private $url;

	/**
	 * Creates and processes the provided URL (or filepath).
	 *
	 * @throws URLException If the URL (or filepath) is seriously malformed.
	 *
	 * @param string $url The URL (or filepath).
	 */
	public function __construct( string $url ) {
		$this->url = $url;
		$this->preprocess();
		$this->process_path();
	}

	/**
	 * Makes all slashes forward slashes, converts filepaths to file:// URLs, and
	 * other processing to help with comprehension of filepaths.
	 *
	 * @throws URLException If the URL is seriously malformed.
	 */
	private function preprocess() {
		// For consistency, all slashes should be forward slashes.
		$this->url = str_replace( '\\', '/', $this->url );

		// Windows: capture the drive letter if provided.
		if ( preg_match( '#^(file://)?([a-z]):/(?!/).*#i', $this->url, $matches ) ) {
			$this->components['drive'] = $matches[2];
		}

		/*
		 * If there is no scheme, assume and prepend "file://". An exception is made for cases where the URL simply
		 * starts with exactly two forward slashes, which indicates 'any scheme' (most commonly, that is used when
		 * there is freedom to switch between 'http' and 'https').
		 */
		if ( ! preg_match( '#^[a-z]+://#i', $this->url ) && ! preg_match( '#^//(?!/)#', $this->url ) ) {
			$this->url = 'file://' . $this->url;
		}

		$parsed_components = wp_parse_url( $this->url );

		// If we received a really badly formed URL, let's go no further.
		if ( false === $parsed_components ) {
			throw new URLException(
				sprintf(
				/* translators: %s is the URL. */
					__( '%s is not a valid URL.', 'woocommerce' ),
					$this->url
				)
			);
		}

		$this->components = array_merge( $this->components, $parsed_components );

		// File URLs cannot have a host. However, the initial path segment *or* the Windows drive letter
		// (if present) may be incorrectly be interpreted as the host name.
		if ( 'file' === $this->components['scheme'] && ! empty( $this->components['host'] ) ) {
			// If we do not have a drive letter, then simply merge the host and the path together.
			if ( null === $this->components['drive'] ) {
				$this->components['path'] = $this->components['host'] . ( $this->components['path'] ?? '' );
			}

			// Restore the host to null in this situation.
			$this->components['host'] = null;
		}
	}

	/**
	 * Simplifies the path if possible, by resolving directory traversals to the extent possible
	 * without touching the filesystem.
	 */
	private function process_path() {
		$segments                    = explode( '/', $this->components['path'] );
		$this->is_absolute           = substr( $this->components['path'], 0, 1 ) === '/' || ! empty( $this->components['host'] );
		$this->is_non_root_directory = substr( $this->components['path'], -1, 1 ) === '/' && strlen( $this->components['path'] ) > 1;
		$resolve_traversals          = 'file' !== $this->components['scheme'] || $this->is_absolute;
		$retain_traversals           = false;

		// Clean the path.
		foreach ( $segments as $part ) {
			// Drop empty segments.
			if ( strlen( $part ) === 0 || '.' === $part ) {
				continue;
			}

			// Directory traversals created with percent-encoding syntax should also be detected.
			$is_traversal = str_ireplace( '%2e', '.', $part ) === '..';

			// Resolve directory traversals (if allowed: see further comment relating to this).
			if ( $resolve_traversals && $is_traversal ) {
				if ( count( $this->path_parts ) > 0 && ! $retain_traversals ) {
					$this->path_parts = array_slice( $this->path_parts, 0, count( $this->path_parts ) - 1 );
					continue;
				} elseif ( $this->is_absolute ) {
					continue;
				}
			}

			/*
			 * Consider allowing directory traversals to be resolved (ie, the process that converts 'foo/bar/../baz' to
			 * 'foo/baz').
			 *
			 * 1. For this decision point, we are only concerned with relative filepaths (in all other cases,
			 *    $resolve_traversals will already be true).
			 * 2. This is a 'one time' and unidirectional operation. We only wish to flip from false to true, and we
			 *    never wish to do this more than once.
			 * 3. We only flip the switch after we have examined all leading '..' traversal segments.
			 */
			if ( false === $resolve_traversals && '..' !== $part && 'file' === $this->components['scheme'] && ! $this->is_absolute ) {
				$resolve_traversals = true;
			}

			/*
			 * Set a flag indicating that traversals should be retained. This is done to ensure we don't prematurely
			 * discard traversals at the start of the path.
			 */
			$retain_traversals = $resolve_traversals && '..' === $part;

			// Retain this part of the path.
			$this->path_parts[] = $part;
		}

		// Protect against empty relative paths.
		if ( count( $this->path_parts ) === 0 && ! $this->is_absolute ) {
			$this->path_parts = array( '.' );
			$this->is_non_root_directory = true;
		}

		// Reform the path from the processed segments, appending a leading slash if it is absolute and restoring
		// the Windows drive letter if we have one.
		$this->components['path'] = ( $this->is_absolute ? '/' : '' ) . implode( '/', $this->path_parts ) . ( $this->is_non_root_directory ? '/' : '' );
	}

	/**
	 * Returns the processed URL as a string.
	 *
	 * @return string
	 */
	public function __toString(): string {
		return $this->get_url();
	}

	/**
	 * Returns all possible parent URLs for the current URL.
	 *
	 * @return string[]
	 */
	public function get_all_parent_urls(): array {
		$max_parent = count( $this->path_parts );
		$parents    = array();

		/*
		 * If we are looking at a relative path that begins with at least one traversal (example: "../../foo")
		 * then we should only return one parent URL (otherwise, we'd potentially have to return an infinite
		 * number of parent URLs since we can't know how far the tree extends).
		 */
		if ( $max_parent > 0 && ! $this->is_absolute && '..' === $this->path_parts[0] ) {
			$max_parent = 1;
		}

		for ( $level = 1; $level <= $max_parent; $level++ ) {
			$parents[] = $this->get_parent_url( $level );
		}

		return $parents;
	}

	/**
	 * Outputs the parent URL.
	 *
	 * For example, if $this->get_url() returns "https://example.com/foo/bar/baz" then
	 * this method will return "https://example.com/foo/bar/".
	 *
	 * When a grand-parent is needed, the optional $level parameter can be used. By default
	 * this is set to 1 (parent). 2 will yield the grand-parent, 3 will yield the great
	 * grand-parent, etc.
	 *
	 * If a level is specified that exceeds the number of path segments, this method will
	 * return false.
	 *
	 * @param int $level Used to indicate the level of parent.
	 *
	 * @return string|false
	 */
	public function get_parent_url( int $level = 1 ) {
		if ( $level < 1 ) {
			$level = 1;
		}

		$parts_count               = count( $this->path_parts );
		$parent_path_parts_to_keep = $parts_count - $level;

		/*
		 * With the exception of file URLs, we do not allow obtaining (grand-)parent directories that require
		 * us to describe them using directory traversals. For example, given "http://hostname/foo/bar/baz.png" we do
		 * not permit determining anything more than 2 levels up (we cannot go beyond "http://hostname/").
		 */
		if ( 'file' !== $this->components['scheme'] && $parent_path_parts_to_keep < 0 ) {
			return false;
		}

		// In the specific case of an absolute filepath describing the root directory, there can be no parent.
		if ( 'file' === $this->components['scheme'] && $this->is_absolute && empty( $this->path_parts ) ) {
			return false;
		}

		// Handle cases where the path starts with one or more 'dot segments'. Since the path has already been
		// processed, we can be confident that any such segments are at the start of the path.
		if ( $parts_count > 0 && ( '.' === $this->path_parts[0] || '..' === $this->path_parts[0] ) ) {
			// Determine the index of the last dot segment (ex: given the path '/../../foo' it would be 1).
			$single_dots   = array_keys( $this->path_parts, '.', true );
			$double_dots   = array_keys( $this->path_parts, '..', true );
			$max_dot_index = max( array_merge( $single_dots, $double_dots ) );

			// Prepend the required number of traversals and discard unnessary trailing segments.
			$last_traversal = $max_dot_index + ( $this->is_non_root_directory ? 1 : 0 );
			$parent_path    = str_repeat( '../', $level ) . join( '/', array_slice( $this->path_parts, 0, $last_traversal ) );
		} elseif ( $parent_path_parts_to_keep < 0 ) {
			// For relative filepaths only, we use traversals to describe the requested parent.
			$parent_path = untrailingslashit( str_repeat( '../', $parent_path_parts_to_keep * -1 ) );
		} else {
			// Otherwise, in a very simple case, we just remove existing parts.
			$parent_path = implode( '/', array_slice( $this->path_parts, 0, $parent_path_parts_to_keep ) );
		}

		if ( $this->is_relative() && '' === $parent_path ) {
			$parent_path = '.';
		}

		// Append a trailing slash, since a parent is always a directory. The only exception is the current working directory.
		$parent_path .= '/';

		// For absolute paths, apply a leading slash (does not apply if we have a root path).
		if ( $this->is_absolute && 0 !== strpos( $parent_path, '/' ) ) {
			$parent_path = '/' . $parent_path;
		}

		// Form the parent URL (ditching the query and fragment, if set).
		$parent_url = $this->get_url(
			array(
				'path'     => $parent_path,
				'query'    => null,
				'fragment' => null,
			)
		);

		// We process the parent URL through a fresh instance of this class, for consistency.
		return ( new self( $parent_url ) )->get_url();
	}

	/**
	 * Outputs the processed URL.
	 *
	 * Borrows from https://www.php.net/manual/en/function.parse-url.php#106731
	 *
	 * @param array $component_overrides If provided, these will override values set in $this->components.
	 *
	 * @return string
	 */
	public function get_url( array $component_overrides = array() ): string {
		$components = array_merge( $this->components, $component_overrides );

		$scheme = null !== $components['scheme'] ? $components['scheme'] . '://' : '//';
		$host   = null !== $components['host'] ? $components['host'] : '';
		$port   = null !== $components['port'] ? ':' . $components['port'] : '';
		$path   = $this->get_path( $components['path'] );

		// Special handling for hostless URLs (typically, filepaths) referencing the current working directory.
		if ( '' === $host && ( '' === $path || '.' === $path ) ) {
			$path = './';
		}

		$user      = null !== $components['user'] ? $components['user'] : '';
		$pass      = null !== $components['pass'] ? ':' . $components['pass'] : '';
		$user_pass = ( ! empty( $user ) || ! empty( $pass ) ) ? $user . $pass . '@' : '';

		$query    = null !== $components['query'] ? '?' . $components['query'] : '';
		$fragment = null !== $components['fragment'] ? '#' . $components['fragment'] : '';

		return $scheme . $user_pass . $host . $port . $path . $query . $fragment;
	}

	/**
	 * Outputs the path. Especially useful if it was a a regular filepath that was passed in originally.
	 *
	 * @param string $path_override If provided this will be used as the URL path. Does not impact drive letter.
	 *
	 * @return string
	 */
	public function get_path( string $path_override = null ): string {
		return ( $this->components['drive'] ? $this->components['drive'] . ':' : '' ) . ( $path_override ?? $this->components['path'] );
	}

	/**
	 * Indicates if the URL or filepath was absolute.
	 *
	 * @return bool True if absolute, else false.
	 */
	public function is_absolute(): bool {
		return $this->is_absolute;
	}

	/**
	 * Indicates if the URL or filepath was relative.
	 *
	 * @return bool True if relative, else false.
	 */
	public function is_relative(): bool {
		return ! $this->is_absolute;
	}
}
