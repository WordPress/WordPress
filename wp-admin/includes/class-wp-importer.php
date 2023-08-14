<?php
/**
 * WP_Importer base class
 */
#[AllowDynamicProperties]
class WP_Importer {
	/**
	 * Class Constructor
	 */
	public function __construct() {}

	/**
	 * Returns array with imported permalinks from WordPress database.
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @param string $importer_name
	 * @param string $blog_id
	 * @return array
	 */
	public function get_imported_posts( $importer_name, $blog_id ) {
		global $wpdb;

		$hashtable = array();

		$limit  = 100;
		$offset = 0;

		// Grab all posts in chunks.
		do {
			$meta_key = $importer_name . '_' . $blog_id . '_permalink';
			$sql      = $wpdb->prepare( "SELECT post_id, meta_value FROM $wpdb->postmeta WHERE meta_key = %s LIMIT %d,%d", $meta_key, $offset, $limit );
			$results  = $wpdb->get_results( $sql );

			// Increment offset.
			$offset = ( $limit + $offset );

			if ( ! empty( $results ) ) {
				foreach ( $results as $r ) {
					// Set permalinks into array.
					$hashtable[ $r->meta_value ] = (int) $r->post_id;
				}
			}
		} while ( count( $results ) === $limit );

		return $hashtable;
	}

	/**
	 * Returns count of imported permalinks from WordPress database.
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @param string $importer_name
	 * @param string $blog_id
	 * @return int
	 */
	public function count_imported_posts( $importer_name, $blog_id ) {
		global $wpdb;

		$count = 0;

		// Get count of permalinks.
		$meta_key = $importer_name . '_' . $blog_id . '_permalink';
		$sql      = $wpdb->prepare( "SELECT COUNT( post_id ) AS cnt FROM $wpdb->postmeta WHERE meta_key = %s", $meta_key );

		$result = $wpdb->get_results( $sql );

		if ( ! empty( $result ) ) {
			$count = (int) $result[0]->cnt;
		}

		return $count;
	}

	/**
	 * Sets array with imported comments from WordPress database.
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @param string $blog_id
	 * @return array
	 */
	public function get_imported_comments( $blog_id ) {
		global $wpdb;

		$hashtable = array();

		$limit  = 100;
		$offset = 0;

		// Grab all comments in chunks.
		do {
			$sql     = $wpdb->prepare( "SELECT comment_ID, comment_agent FROM $wpdb->comments LIMIT %d,%d", $offset, $limit );
			$results = $wpdb->get_results( $sql );

			// Increment offset.
			$offset = ( $limit + $offset );

			if ( ! empty( $results ) ) {
				foreach ( $results as $r ) {
					// Explode comment_agent key.
					list ( $comment_agent_blog_id, $source_comment_id ) = explode( '-', $r->comment_agent );

					$source_comment_id = (int) $source_comment_id;

					// Check if this comment came from this blog.
					if ( (int) $blog_id === (int) $comment_agent_blog_id ) {
						$hashtable[ $source_comment_id ] = (int) $r->comment_ID;
					}
				}
			}
		} while ( count( $results ) === $limit );

		return $hashtable;
	}

	/**
	 * @param int $blog_id
	 * @return int|void
	 */
	public function set_blog( $blog_id ) {
		if ( is_numeric( $blog_id ) ) {
			$blog_id = (int) $blog_id;
		} else {
			$blog   = 'http://' . preg_replace( '#^https?://#', '', $blog_id );
			$parsed = parse_url( $blog );
			if ( ! $parsed || empty( $parsed['host'] ) ) {
				fwrite( STDERR, "Error: can not determine blog_id from $blog_id\n" );
				exit;
			}
			if ( empty( $parsed['path'] ) ) {
				$parsed['path'] = '/';
			}
			$blogs = get_sites(
				array(
					'domain' => $parsed['host'],
					'number' => 1,
					'path'   => $parsed['path'],
				)
			);
			if ( ! $blogs ) {
				fwrite( STDERR, "Error: Could not find blog\n" );
				exit;
			}
			$blog    = array_shift( $blogs );
			$blog_id = (int) $blog->blog_id;
		}

		if ( function_exists( 'is_multisite' ) ) {
			if ( is_multisite() ) {
				switch_to_blog( $blog_id );
			}
		}

		return $blog_id;
	}

	/**
	 * @param int $user_id
	 * @return int|void
	 */
	public function set_user( $user_id ) {
		if ( is_numeric( $user_id ) ) {
			$user_id = (int) $user_id;
		} else {
			$user_id = (int) username_exists( $user_id );
		}

		if ( ! $user_id || ! wp_set_current_user( $user_id ) ) {
			fwrite( STDERR, "Error: can not find user\n" );
			exit;
		}

		return $user_id;
	}

	/**
	 * Sorts by strlen, longest string first.
	 *
	 * @param string $a
	 * @param string $b
	 * @return int
	 */
	public function cmpr_strlen( $a, $b ) {
		return strlen( $b ) - strlen( $a );
	}

	/**
	 * Gets URL.
	 *
	 * @param string $url
	 * @param string $username
	 * @param string $password
	 * @param bool   $head
	 * @return array
	 */
	public function get_page( $url, $username = '', $password = '', $head = false ) {
		// Increase the timeout.
		add_filter( 'http_request_timeout', array( $this, 'bump_request_timeout' ) );

		$headers = array();
		$args    = array();
		if ( true === $head ) {
			$args['method'] = 'HEAD';
		}
		if ( ! empty( $username ) && ! empty( $password ) ) {
			$headers['Authorization'] = 'Basic ' . base64_encode( "$username:$password" );
		}

		$args['headers'] = $headers;

		return wp_safe_remote_request( $url, $args );
	}

	/**
	 * Bumps up the request timeout for http requests.
	 *
	 * @param int $val
	 * @return int
	 */
	public function bump_request_timeout( $val ) {
		return 60;
	}

	/**
	 * Checks if user has exceeded disk quota.
	 *
	 * @return bool
	 */
	public function is_user_over_quota() {
		if ( function_exists( 'upload_is_user_over_quota' ) ) {
			if ( upload_is_user_over_quota() ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Replaces newlines, tabs, and multiple spaces with a single space.
	 *
	 * @param string $text
	 * @return string
	 */
	public function min_whitespace( $text ) {
		return preg_replace( '|[\r\n\t ]+|', ' ', $text );
	}

	/**
	 * Resets global variables that grow out of control during imports.
	 *
	 * @since 3.0.0
	 *
	 * @global wpdb  $wpdb       WordPress database abstraction object.
	 * @global int[] $wp_actions
	 */
	public function stop_the_insanity() {
		global $wpdb, $wp_actions;
		// Or define( 'WP_IMPORTING', true );
		$wpdb->queries = array();
		// Reset $wp_actions to keep it from growing out of control.
		$wp_actions = array();
	}
}

/**
 * Returns value of command line params.
 * Exits when a required param is not set.
 *
 * @param string $param
 * @param bool   $required
 * @return mixed
 */
function get_cli_args( $param, $required = false ) {
	$args = $_SERVER['argv'];
	if ( ! is_array( $args ) ) {
		$args = array();
	}

	$out = array();

	$last_arg = null;
	$return   = null;

	$il = count( $args );

	for ( $i = 1, $il; $i < $il; $i++ ) {
		if ( (bool) preg_match( '/^--(.+)/', $args[ $i ], $match ) ) {
			$parts = explode( '=', $match[1] );
			$key   = preg_replace( '/[^a-z0-9]+/', '', $parts[0] );

			if ( isset( $parts[1] ) ) {
				$out[ $key ] = $parts[1];
			} else {
				$out[ $key ] = true;
			}

			$last_arg = $key;
		} elseif ( (bool) preg_match( '/^-([a-zA-Z0-9]+)/', $args[ $i ], $match ) ) {
			for ( $j = 0, $jl = strlen( $match[1] ); $j < $jl; $j++ ) {
				$key         = $match[1][ $j ];
				$out[ $key ] = true;
			}

			$last_arg = $key;
		} elseif ( null !== $last_arg ) {
			$out[ $last_arg ] = $args[ $i ];
		}
	}

	// Check array for specified param.
	if ( isset( $out[ $param ] ) ) {
		// Set return value.
		$return = $out[ $param ];
	}

	// Check for missing required param.
	if ( ! isset( $out[ $param ] ) && $required ) {
		// Display message and exit.
		echo "\"$param\" parameter is required but was not specified\n";
		exit;
	}

	return $return;
}
