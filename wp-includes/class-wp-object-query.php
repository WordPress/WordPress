<?php
/**
 * WordPress Query class.
 *
 * Abstract class for handling advanced queries
 *
 * @package WordPress
 * @since 3.1.0
 */
class WP_Object_Query {
	/*
	 * Populates the $meta_query property
	 *
	 * @access protected
	 * @since 3.1.0
	 *
	 * @param array $qv The query variables
	 */
	function parse_meta_query( &$qv ) {
		$meta_query = array();

		// Simple query needs to be first for orderby=meta_value to work correctly
		foreach ( array( 'key', 'value', 'compare', 'type' ) as $key ) {
			if ( !empty( $qv[ "meta_$key" ] ) )
				$meta_query[0][ $key ] = $qv[ "meta_$key" ];
		}

		if ( !empty( $qv['meta_query'] ) && is_array( $qv['meta_query'] ) ) {
			$meta_query = array_merge( $meta_query, $qv['meta_query'] );
		}

		$qv['meta_query'] = $meta_query;
	}
}

?>
