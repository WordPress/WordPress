<?php
/**
 * REST API: Gutenberg_REST_Attachments_Controller_6_9 class
 *
 * @package gutenberg
 */

/**
 * Controller which provides REST endpoint for retrieving attachments.
 * This overrides the core WP_REST_Attachments_Controller to provide
 * support for filtering by multiple media types.
 *
 * @since 6.9.0
 *
 * @see WP_REST_Attachments_Controller
 */
class Gutenberg_REST_Attachments_Controller_6_9 extends WP_REST_Attachments_Controller {

	/**
	 * Determines the allowed query_vars for a get_items() response and
	 * prepares for WP_Query.
	 *
	 * This overrides the parent method to add support for filtering by
	 * multiple media types, which was added in WordPress 6.9.
	 *
	 * @since 4.7.0
	 * @since 6.9.0 Added orderby_mime_type filter to add custom ordering.
	 * @since 6.9.0 Extends the `media_type` and `mime_type` request arguments to support array values.
	 *
	 * @param array           $prepared_args Optional. Array of prepared arguments. Default empty array.
	 * @param WP_REST_Request $request       Optional. Request to prepare items for.
	 * @return array Array of query arguments.
	 */
	protected function prepare_items_query( $prepared_args = array(), $request = null ) {
		// Store array parameters that we'll handle separately.
		$media_type_array = null;
		$mime_type_array  = null;

		if ( ! empty( $request['media_type'] ) && is_array( $request['media_type'] ) ) {
			$media_type_array = $request['media_type'];
			unset( $request['media_type'] );
		}

		if ( ! empty( $request['mime_type'] ) && is_array( $request['mime_type'] ) ) {
			$mime_type_array = $request['mime_type'];
			unset( $request['mime_type'] );
		}

		$query_args = parent::prepare_items_query( $prepared_args, $request );

		// Restore the array parameters to the request.
		if ( null !== $media_type_array ) {
			$request['media_type'] = $media_type_array;
		}

		if ( null !== $mime_type_array ) {
			$request['mime_type'] = $mime_type_array;
		}

		if ( empty( $query_args['post_status'] ) ) {
			$query_args['post_status'] = 'inherit';
		}

		$all_mime_types = array();
		$media_types    = $this->get_media_types();

		if ( null !== $media_type_array ) {
			foreach ( $media_type_array as $type ) {
				if ( isset( $media_types[ $type ] ) ) {
					$all_mime_types = array_merge( $all_mime_types, $media_types[ $type ] );
				}
			}
		}

		if ( null !== $mime_type_array ) {
			foreach ( $mime_type_array as $mime_type ) {
				$parts = explode( '/', $mime_type );
				if ( isset( $media_types[ $parts[0] ] ) && in_array( $mime_type, $media_types[ $parts[0] ], true ) ) {
					$all_mime_types[] = $mime_type;
				}
			}
		}

		if ( ! empty( $all_mime_types ) ) {
			$query_args['post_mime_type'] = array_values( array_unique( $all_mime_types ) );
		}

		// Filter query clauses to include filenames.
		if ( isset( $query_args['s'] ) ) {
			add_filter( 'wp_allow_query_attachment_by_filename', '__return_true' );
		}

		return $query_args;
	}

	/**
	 * Retrieves the query params for collections of attachments.
	 *
	 * @since 4.7.0
	 * @since 6.9.0 Extends the `media_type` and `mime_type` request arguments to support array values.
	 *
	 * @return array Query parameters for the attachment collection as an array.
	 */
	public function get_collection_params() {
		$params                            = parent::get_collection_params();
		$params['status']['default']       = 'inherit';
		$params['status']['items']['enum'] = array( 'inherit', 'private', 'trash' );
		$media_types                       = array_keys( $this->get_media_types() );

		$params['media_type'] = array(
			'default'     => null,
			'description' => __( 'Limit result set to attachments of a particular media type or media types.' ),
			'type'        => 'array',
			'items'       => array(
				'type' => 'string',
				'enum' => $media_types,
			),
		);

		$params['mime_type'] = array(
			'default'     => null,
			'description' => __( 'Limit result set to attachments of a particular MIME type or MIME types.' ),
			'type'        => 'array',
			'items'       => array(
				'type' => 'string',
			),
		);

		return $params;
	}
}
