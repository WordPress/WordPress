<?php
/**
 * REST API: WP_REST_Attachments_Controller class
 *
 * @package WordPress
 * @subpackage REST_API
 * @since 4.7.0
 */

/**
 * Core controller used to access attachments via the REST API.
 *
 * @since 4.7.0
 *
 * @see WP_REST_Posts_Controller
 */
class WP_REST_Attachments_Controller extends WP_REST_Posts_Controller {

	/**
	 * Determines the allowed query_vars for a get_items() response and
	 * prepares for WP_Query.
	 *
	 * @since 4.7.0
	 * @access protected
	 *
	 * @param array           $prepared_args Optional. Array of prepared arguments. Default empty array.
	 * @param WP_REST_Request $request       Optional. Request to prepare items for.
	 * @return array Array of query arguments.
	 */
	protected function prepare_items_query( $prepared_args = array(), $request = null ) {
		$query_args = parent::prepare_items_query( $prepared_args, $request );

		if ( empty( $query_args['post_status'] ) ) {
			$query_args['post_status'] = 'inherit';
		}

		$media_types = $this->get_media_types();

		if ( ! empty( $request['media_type'] ) && isset( $media_types[ $request['media_type'] ] ) ) {
			$query_args['post_mime_type'] = $media_types[ $request['media_type'] ];
		}

		if ( ! empty( $request['mime_type'] ) ) {
			$parts = explode( '/', $request['mime_type'] );
			if ( isset( $media_types[ $parts[0] ] ) && in_array( $request['mime_type'], $media_types[ $parts[0] ], true ) ) {
				$query_args['post_mime_type'] = $request['mime_type'];
			}
		}

		return $query_args;
	}

	/**
	 * Checks if a given request has access to create an attachment.
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|true Boolean true if the attachment may be created, or a WP_Error if not.
	 */
	public function create_item_permissions_check( $request ) {
		$ret = parent::create_item_permissions_check( $request );

		if ( ! $ret || is_wp_error( $ret ) ) {
			return $ret;
		}

		if ( ! current_user_can( 'upload_files' ) ) {
			return new WP_Error( 'rest_cannot_create', __( 'Sorry, you are not allowed to upload media on this site.' ), array( 'status' => 400 ) );
		}

		// Attaching media to a post requires ability to edit said post.
		if ( ! empty( $request['post'] ) ) {
			$parent = get_post( (int) $request['post'] );
			$post_parent_type = get_post_type_object( $parent->post_type );

			if ( ! current_user_can( $post_parent_type->cap->edit_post, $request['post'] ) ) {
				return new WP_Error( 'rest_cannot_edit', __( 'Sorry, you are not allowed to upload media to this post.' ), array( 'status' => rest_authorization_required_code() ) );
			}
		}

		return true;
	}

	/**
	 * Creates a single attachment.
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response Response object on success, WP_Error object on failure.
	 */
	public function create_item( $request ) {

		if ( ! empty( $request['post'] ) && in_array( get_post_type( $request['post'] ), array( 'revision', 'attachment' ), true ) ) {
			return new WP_Error( 'rest_invalid_param', __( 'Invalid parent type.' ), array( 'status' => 400 ) );
		}

		// Get the file via $_FILES or raw data.
		$files = $request->get_file_params();
		$headers = $request->get_headers();

		if ( ! empty( $files ) ) {
			$file = $this->upload_from_file( $files, $headers );
		} else {
			$file = $this->upload_from_data( $request->get_body(), $headers );
		}

		if ( is_wp_error( $file ) ) {
			return $file;
		}

		$name       = basename( $file['file'] );
		$name_parts = pathinfo( $name );
		$name       = trim( substr( $name, 0, -(1 + strlen( $name_parts['extension'] ) ) ) );

		$url     = $file['url'];
		$type    = $file['type'];
		$file    = $file['file'];

		// use image exif/iptc data for title and caption defaults if possible
		$image_meta = @wp_read_image_metadata( $file );

		if ( ! empty( $image_meta ) ) {
			if ( empty( $request['title'] ) && trim( $image_meta['title'] ) && ! is_numeric( sanitize_title( $image_meta['title'] ) ) ) {
				$request['title'] = $image_meta['title'];
			}

			if ( empty( $request['caption'] ) && trim( $image_meta['caption'] ) ) {
				$request['caption'] = $image_meta['caption'];
			}
		}

		$attachment = $this->prepare_item_for_database( $request );
		$attachment->file = $file;
		$attachment->post_mime_type = $type;
		$attachment->guid = $url;

		if ( empty( $attachment->post_title ) ) {
			$attachment->post_title = preg_replace( '/\.[^.]+$/', '', basename( $file ) );
		}

		$id = wp_insert_post( wp_slash( (array) $attachment ), true );

		if ( is_wp_error( $id ) ) {
			if ( 'db_update_error' === $id->get_error_code() ) {
				$id->add_data( array( 'status' => 500 ) );
			} else {
				$id->add_data( array( 'status' => 400 ) );
			}
			return $id;
		}

		$attachment = get_post( $id );

		/**
		 * Fires after a single attachment is created or updated via the REST API.
		 *
		 * @since 4.7.0
		 *
		 * @param WP_Post         $attachment Inserted or updated attachment
		 *                                    object.
		 * @param WP_REST_Request $request    The request sent to the API.
		 * @param bool            $creating   True when creating an attachment, false when updating.
		 */
		do_action( 'rest_insert_attachment', $attachment, $request, true );

		// Include admin functions to get access to wp_generate_attachment_metadata().
		require_once ABSPATH . 'wp-admin/includes/admin.php';

		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );

		if ( isset( $request['alt_text'] ) ) {
			update_post_meta( $id, '_wp_attachment_image_alt', sanitize_text_field( $request['alt_text'] ) );
		}

		$fields_update = $this->update_additional_fields_for_object( $attachment, $request );

		if ( is_wp_error( $fields_update ) ) {
			return $fields_update;
		}

		$request->set_param( 'context', 'edit' );
		$response = $this->prepare_item_for_response( $attachment, $request );
		$response = rest_ensure_response( $response );
		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $id ) ) );

		return $response;
	}

	/**
	 * Updates a single attachment.
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response Response object on success, WP_Error object on failure.
	 */
	public function update_item( $request ) {
		if ( ! empty( $request['post'] ) && in_array( get_post_type( $request['post'] ), array( 'revision', 'attachment' ), true ) ) {
			return new WP_Error( 'rest_invalid_param', __( 'Invalid parent type.' ), array( 'status' => 400 ) );
		}

		$response = parent::update_item( $request );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response = rest_ensure_response( $response );
		$data = $response->get_data();

		if ( isset( $request['alt_text'] ) ) {
			update_post_meta( $data['id'], '_wp_attachment_image_alt', $request['alt_text'] );
		}

		$attachment = get_post( $request['id'] );

		/* This action is documented in wp-includes/rest-api/endpoints/class-wp-rest-attachments-controller.php */
		do_action( 'rest_insert_attachment', $data, $request, false );

		$fields_update = $this->update_additional_fields_for_object( $attachment, $request );

		if ( is_wp_error( $fields_update ) ) {
			return $fields_update;
		}

		$request->set_param( 'context', 'edit' );
		$response = $this->prepare_item_for_response( $attachment, $request );
		$response = rest_ensure_response( $response );

		return $response;
	}

	/**
	 * Prepares a single attachment for create or update.
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_Error|stdClass $prepared_attachment Post object.
	 */
	protected function prepare_item_for_database( $request ) {
		$prepared_attachment = parent::prepare_item_for_database( $request );

		// Attachment caption (post_excerpt internally)
		if ( isset( $request['caption'] ) ) {
			if ( is_string( $request['caption'] ) ) {
				$prepared_attachment->post_excerpt = $request['caption'];
			} elseif ( isset( $request['caption']['raw'] ) ) {
				$prepared_attachment->post_excerpt = $request['caption']['raw'];
			}
		}

		// Attachment description (post_content internally)
		if ( isset( $request['description'] ) ) {
			if ( is_string( $request['description'] ) ) {
				$prepared_attachment->post_content = $request['description'];
			} elseif ( isset( $request['description']['raw'] ) ) {
				$prepared_attachment->post_content = $request['description']['raw'];
			}
		}

		if ( isset( $request['post'] ) ) {
			$prepared_attachment->post_parent = (int) $request['post'];
		}

		return $prepared_attachment;
	}

	/**
	 * Prepares a single attachment output for response.
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @param WP_Post         $post    Attachment object.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response Response object.
	 */
	public function prepare_item_for_response( $post, $request ) {
		$response = parent::prepare_item_for_response( $post, $request );
		$data = $response->get_data();

		$data['description'] = array(
			'raw'       => $post->post_content,
			/** This filter is documented in wp-includes/post-template.php */
			'rendered'  => apply_filters( 'the_content', $post->post_content ),
		);

		/** This filter is documented in wp-includes/post-template.php */
		$caption = apply_filters( 'the_excerpt', apply_filters( 'get_the_excerpt', $post->post_excerpt, $post ) );
		$data['caption'] = array(
			'raw'       => $post->post_excerpt,
			'rendered'  => $caption,
		);

		$data['alt_text']      = get_post_meta( $post->ID, '_wp_attachment_image_alt', true );
		$data['media_type']    = wp_attachment_is_image( $post->ID ) ? 'image' : 'file';
		$data['mime_type']     = $post->post_mime_type;
		$data['media_details'] = wp_get_attachment_metadata( $post->ID );
		$data['post']          = ! empty( $post->post_parent ) ? (int) $post->post_parent : null;
		$data['source_url']    = wp_get_attachment_url( $post->ID );

		// Ensure empty details is an empty object.
		if ( empty( $data['media_details'] ) ) {
			$data['media_details'] = new stdClass;
		} elseif ( ! empty( $data['media_details']['sizes'] ) ) {

			foreach ( $data['media_details']['sizes'] as $size => &$size_data ) {

				if ( isset( $size_data['mime-type'] ) ) {
					$size_data['mime_type'] = $size_data['mime-type'];
					unset( $size_data['mime-type'] );
				}

				// Use the same method image_downsize() does.
				$image_src = wp_get_attachment_image_src( $post->ID, $size );
				if ( ! $image_src ) {
					continue;
				}

				$size_data['source_url'] = $image_src[0];
			}

			$full_src = wp_get_attachment_image_src( $post->ID, 'full' );

			if ( ! empty( $full_src ) ) {
				$data['media_details']['sizes']['full'] = array(
					'file'       => wp_basename( $full_src[0] ),
					'width'      => $full_src[1],
					'height'     => $full_src[2],
					'mime_type'  => $post->post_mime_type,
					'source_url' => $full_src[0],
				);
			}
		} else {
			$data['media_details']['sizes'] = new stdClass;
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';

		$data = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		$response->add_links( $this->prepare_links( $post ) );

		/**
		 * Filters an attachment returned from the REST API.
		 *
		 * Allows modification of the attachment right before it is returned.
		 *
		 * @since 4.7.0
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param WP_Post          $post     The original attachment post.
		 * @param WP_REST_Request  $request  Request used to generate the response.
		 */
		return apply_filters( 'rest_prepare_attachment', $response, $post, $request );
	}

	/**
	 * Retrieves the attachment's schema, conforming to JSON Schema.
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @return array Item schema as an array.
	 */
	public function get_item_schema() {

		$schema = parent::get_item_schema();

		$schema['properties']['alt_text'] = array(
			'description'     => __( 'Alternative text to display when attachment is not displayed.' ),
			'type'            => 'string',
			'context'         => array( 'view', 'edit', 'embed' ),
			'arg_options'     => array(
				'sanitize_callback' => 'sanitize_text_field',
			),
		);

		$schema['properties']['caption'] = array(
			'description' => __( 'The attachment caption.' ),
			'type'        => 'object',
			'context'     => array( 'view', 'edit', 'embed' ),
			'arg_options' => array(
				'sanitize_callback' => null, // Note: sanitization implemented in self::prepare_item_for_database()
			),
			'properties'  => array(
				'raw' => array(
					'description' => __( 'Caption for the attachment, as it exists in the database.' ),
					'type'        => 'string',
					'context'     => array( 'edit' ),
				),
				'rendered' => array(
					'description' => __( 'HTML caption for the attachment, transformed for display.' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
			),
		);

		$schema['properties']['description'] = array(
			'description' => __( 'The attachment description.' ),
			'type'        => 'object',
			'context'     => array( 'view', 'edit' ),
			'arg_options' => array(
				'sanitize_callback' => null, // Note: sanitization implemented in self::prepare_item_for_database()
			),
			'properties'  => array(
				'raw' => array(
					'description' => __( 'Description for the object, as it exists in the database.' ),
					'type'        => 'string',
					'context'     => array( 'edit' ),
				),
				'rendered' => array(
					'description' => __( 'HTML description for the object, transformed for display.' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
			),
		);

		$schema['properties']['media_type'] = array(
			'description'     => __( 'Attachment type.' ),
			'type'            => 'string',
			'enum'            => array( 'image', 'file' ),
			'context'         => array( 'view', 'edit', 'embed' ),
			'readonly'        => true,
		);

		$schema['properties']['mime_type'] = array(
			'description'     => __( 'The attachment MIME type.' ),
			'type'            => 'string',
			'context'         => array( 'view', 'edit', 'embed' ),
			'readonly'        => true,
		);

		$schema['properties']['media_details'] = array(
			'description'     => __( 'Details about the media file, specific to its type.' ),
			'type'            => 'object',
			'context'         => array( 'view', 'edit', 'embed' ),
			'readonly'        => true,
		);

		$schema['properties']['post'] = array(
			'description'     => __( 'The ID for the associated post of the attachment.' ),
			'type'            => 'integer',
			'context'         => array( 'view', 'edit' ),
		);

		$schema['properties']['source_url'] = array(
			'description'     => __( 'URL to the original attachment file.' ),
			'type'            => 'string',
			'format'          => 'uri',
			'context'         => array( 'view', 'edit', 'embed' ),
			'readonly'        => true,
		);

		unset( $schema['properties']['password'] );

		return $schema;
	}

	/**
	 * Handles an upload via raw POST data.
	 *
	 * @since 4.7.0
	 * @access protected
	 *
	 * @param array $data    Supplied file data.
	 * @param array $headers HTTP headers from the request.
	 * @return array|WP_Error Data from wp_handle_sideload().
	 */
	protected function upload_from_data( $data, $headers ) {
		if ( empty( $data ) ) {
			return new WP_Error( 'rest_upload_no_data', __( 'No data supplied.' ), array( 'status' => 400 ) );
		}

		if ( empty( $headers['content_type'] ) ) {
			return new WP_Error( 'rest_upload_no_content_type', __( 'No Content-Type supplied.' ), array( 'status' => 400 ) );
		}

		if ( empty( $headers['content_disposition'] ) ) {
			return new WP_Error( 'rest_upload_no_content_disposition', __( 'No Content-Disposition supplied.' ), array( 'status' => 400 ) );
		}

		$filename = self::get_filename_from_disposition( $headers['content_disposition'] );

		if ( empty( $filename ) ) {
			return new WP_Error( 'rest_upload_invalid_disposition', __( 'Invalid Content-Disposition supplied. Content-Disposition needs to be formatted as `attachment; filename="image.png"` or similar.' ), array( 'status' => 400 ) );
		}

		if ( ! empty( $headers['content_md5'] ) ) {
			$content_md5 = array_shift( $headers['content_md5'] );
			$expected    = trim( $content_md5 );
			$actual      = md5( $data );

			if ( $expected !== $actual ) {
				return new WP_Error( 'rest_upload_hash_mismatch', __( 'Content hash did not match expected.' ), array( 'status' => 412 ) );
			}
		}

		// Get the content-type.
		$type = array_shift( $headers['content_type'] );

		/** Include admin functions to get access to wp_tempnam() and wp_handle_sideload() */
		require_once ABSPATH . 'wp-admin/includes/admin.php';

		// Save the file.
		$tmpfname = wp_tempnam( $filename );

		$fp = fopen( $tmpfname, 'w+' );

		if ( ! $fp ) {
			return new WP_Error( 'rest_upload_file_error', __( 'Could not open file handle.' ), array( 'status' => 500 ) );
		}

		fwrite( $fp, $data );
		fclose( $fp );

		// Now, sideload it in.
		$file_data = array(
			'error'    => null,
			'tmp_name' => $tmpfname,
			'name'     => $filename,
			'type'     => $type,
		);

		$overrides = array(
			'test_form' => false,
		);

		$sideloaded = wp_handle_sideload( $file_data, $overrides );

		if ( isset( $sideloaded['error'] ) ) {
			@unlink( $tmpfname );

			return new WP_Error( 'rest_upload_sideload_error', $sideloaded['error'], array( 'status' => 500 ) );
		}

		return $sideloaded;
	}

	/**
	 * Parses filename from a Content-Disposition header value.
	 *
	 * As per RFC6266:
	 *
	 *     content-disposition = "Content-Disposition" ":"
	 *                            disposition-type *( ";" disposition-parm )
	 *
	 *     disposition-type    = "inline" | "attachment" | disp-ext-type
	 *                         ; case-insensitive
	 *     disp-ext-type       = token
	 *
	 *     disposition-parm    = filename-parm | disp-ext-parm
	 *
	 *     filename-parm       = "filename" "=" value
	 *                         | "filename*" "=" ext-value
	 *
	 *     disp-ext-parm       = token "=" value
	 *                         | ext-token "=" ext-value
	 *     ext-token           = <the characters in token, followed by "*">
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @link http://tools.ietf.org/html/rfc2388
	 * @link http://tools.ietf.org/html/rfc6266
	 *
	 * @param string[] $disposition_header List of Content-Disposition header values.
	 * @return string|null Filename if available, or null if not found.
	 */
	public static function get_filename_from_disposition( $disposition_header ) {
		// Get the filename.
		$filename = null;

		foreach ( $disposition_header as $value ) {
			$value = trim( $value );

			if ( strpos( $value, ';' ) === false ) {
				continue;
			}

			list( $type, $attr_parts ) = explode( ';', $value, 2 );

			$attr_parts = explode( ';', $attr_parts );
			$attributes = array();

			foreach ( $attr_parts as $part ) {
				if ( strpos( $part, '=' ) === false ) {
					continue;
				}

				list( $key, $value ) = explode( '=', $part, 2 );

				$attributes[ trim( $key ) ] = trim( $value );
			}

			if ( empty( $attributes['filename'] ) ) {
				continue;
			}

			$filename = trim( $attributes['filename'] );

			// Unquote quoted filename, but after trimming.
			if ( substr( $filename, 0, 1 ) === '"' && substr( $filename, -1, 1 ) === '"' ) {
				$filename = substr( $filename, 1, -1 );
			}
		}

		return $filename;
	}

	/**
	 * Retrieves the query params for collections of attachments.
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @return array Query parameters for the attachment collection as an array.
	 */
	public function get_collection_params() {
		$params = parent::get_collection_params();
		$params['status']['default'] = 'inherit';
		$params['status']['items']['enum'] = array( 'inherit', 'private', 'trash' );
		$media_types = $this->get_media_types();

		$params['media_type'] = array(
			'default'           => null,
			'description'       => __( 'Limit result set to attachments of a particular media type.' ),
			'type'              => 'string',
			'enum'              => array_keys( $media_types ),
		);

		$params['mime_type'] = array(
			'default'     => null,
			'description' => __( 'Limit result set to attachments of a particular MIME type.' ),
			'type'        => 'string',
		);

		return $params;
	}

	/**
	 * Validates whether the user can query private statuses.
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @param mixed           $value     Status value.
	 * @param WP_REST_Request $request   Request object.
	 * @param string          $parameter Additional parameter to pass for validation.
	 * @return WP_Error|bool True if the user may query, WP_Error if not.
	 */
	public function validate_user_can_query_private_statuses( $value, $request, $parameter ) {
		if ( 'inherit' === $value ) {
			return true;
		}

		return parent::validate_user_can_query_private_statuses( $value, $request, $parameter );
	}

	/**
	 * Handles an upload via multipart/form-data ($_FILES).
	 *
	 * @since 4.7.0
	 * @access protected
	 *
	 * @param array $files   Data from the `$_FILES` superglobal.
	 * @param array $headers HTTP headers from the request.
	 * @return array|WP_Error Data from wp_handle_upload().
	 */
	protected function upload_from_file( $files, $headers ) {
		if ( empty( $files ) ) {
			return new WP_Error( 'rest_upload_no_data', __( 'No data supplied.' ), array( 'status' => 400 ) );
		}

		// Verify hash, if given.
		if ( ! empty( $headers['content_md5'] ) ) {
			$content_md5 = array_shift( $headers['content_md5'] );
			$expected    = trim( $content_md5 );
			$actual      = md5_file( $files['file']['tmp_name'] );

			if ( $expected !== $actual ) {
				return new WP_Error( 'rest_upload_hash_mismatch', __( 'Content hash did not match expected.' ), array( 'status' => 412 ) );
			}
		}

		// Pass off to WP to handle the actual upload.
		$overrides = array(
			'test_form'   => false,
		);

		// Bypasses is_uploaded_file() when running unit tests.
		if ( defined( 'DIR_TESTDATA' ) && DIR_TESTDATA ) {
			$overrides['action'] = 'wp_handle_mock_upload';
		}

		/** Include admin functions to get access to wp_handle_upload() */
		require_once ABSPATH . 'wp-admin/includes/admin.php';

		$file = wp_handle_upload( $files['file'], $overrides );

		if ( isset( $file['error'] ) ) {
			return new WP_Error( 'rest_upload_unknown_error', $file['error'], array( 'status' => 500 ) );
		}

		return $file;
	}

	/**
	 * Retrieves the supported media types.
	 *
	 * Media types are considered the MIME type category.
	 *
	 * @since 4.7.0
	 * @access protected
	 *
	 * @return array Array of supported media types.
	 */
	protected function get_media_types() {
		$media_types = array();

		foreach ( get_allowed_mime_types() as $mime_type ) {
			$parts = explode( '/', $mime_type );

			if ( ! isset( $media_types[ $parts[0] ] ) ) {
				$media_types[ $parts[0] ] = array();
			}

			$media_types[ $parts[0] ][] = $mime_type;
		}

		return $media_types;
	}

}
