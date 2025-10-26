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
	 * Whether the controller supports batching.
	 *
	 * @since 5.9.0
	 * @var false
	 */
	protected $allow_batch = false;

	/**
	 * Registers the routes for attachments.
	 *
	 * @since 5.3.0
	 *
	 * @see register_rest_route()
	 */
	public function register_routes() {
		parent::register_routes();
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)/post-process',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'post_process_item' ),
				'permission_callback' => array( $this, 'post_process_item_permissions_check' ),
				'args'                => array(
					'id'     => array(
						'description' => __( 'Unique identifier for the attachment.' ),
						'type'        => 'integer',
					),
					'action' => array(
						'type'     => 'string',
						'enum'     => array( 'create-image-subsizes' ),
						'required' => true,
					),
				),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)/edit',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'edit_media_item' ),
				'permission_callback' => array( $this, 'edit_media_item_permissions_check' ),
				'args'                => $this->get_edit_media_item_args(),
			)
		);
	}

	/**
	 * Determines the allowed query_vars for a get_items() response and
	 * prepares for WP_Query.
	 *
	 * @since 4.7.0
	 * @since 6.9.0 Extends the `media_type` and `mime_type` request arguments to support array values.
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

		$all_mime_types = array();
		$media_types    = $this->get_media_types();

		if ( ! empty( $request['media_type'] ) && is_array( $request['media_type'] ) ) {
			foreach ( $request['media_type'] as $type ) {
				if ( isset( $media_types[ $type ] ) ) {
					$all_mime_types = array_merge( $all_mime_types, $media_types[ $type ] );
				}
			}
		}

		if ( ! empty( $request['mime_type'] ) && is_array( $request['mime_type'] ) ) {
			foreach ( $request['mime_type'] as $mime_type ) {
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
	 * Checks if a given request has access to create an attachment.
	 *
	 * @since 4.7.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error Boolean true if the attachment may be created, or a WP_Error if not.
	 */
	public function create_item_permissions_check( $request ) {
		$ret = parent::create_item_permissions_check( $request );

		if ( ! $ret || is_wp_error( $ret ) ) {
			return $ret;
		}

		if ( ! current_user_can( 'upload_files' ) ) {
			return new WP_Error(
				'rest_cannot_create',
				__( 'Sorry, you are not allowed to upload media on this site.' ),
				array( 'status' => 400 )
			);
		}

		// Attaching media to a post requires ability to edit said post.
		if ( ! empty( $request['post'] ) && ! current_user_can( 'edit_post', (int) $request['post'] ) ) {
			return new WP_Error(
				'rest_cannot_edit',
				__( 'Sorry, you are not allowed to upload media to this post.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}
		$files = $request->get_file_params();

		/**
		 * Filter whether the server should prevent uploads for image types it doesn't support. Default true.
		 *
		 * Developers can use this filter to enable uploads of certain image types. By default image types that are not
		 * supported by the server are prevented from being uploaded.
		 *
		 * @since 6.8.0
		 *
		 * @param bool        $check_mime Whether to prevent uploads of unsupported image types.
		 * @param string|null $mime_type  The mime type of the file being uploaded (if available).
		 */
		$prevent_unsupported_uploads = apply_filters( 'wp_prevent_unsupported_mime_type_uploads', true, isset( $files['file']['type'] ) ? $files['file']['type'] : null );

		// If the upload is an image, check if the server can handle the mime type.
		if (
			$prevent_unsupported_uploads &&
			isset( $files['file']['type'] ) &&
			str_starts_with( $files['file']['type'], 'image/' )
		) {
			// List of non-resizable image formats.
			$editor_non_resizable_formats = array(
				'image/svg+xml',
			);

			// Check if the image editor supports the type or ignore if it isn't a format resizable by an editor.
			if (
				! in_array( $files['file']['type'], $editor_non_resizable_formats, true ) &&
				! wp_image_editor_supports( array( 'mime_type' => $files['file']['type'] ) )
			) {
				return new WP_Error(
					'rest_upload_image_type_not_supported',
					__( 'The web server cannot generate responsive image sizes for this image. Convert it to JPEG or PNG before uploading.' ),
					array( 'status' => 400 )
				);
			}
		}

		return true;
	}

	/**
	 * Creates a single attachment.
	 *
	 * @since 4.7.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, WP_Error object on failure.
	 */
	public function create_item( $request ) {
		if ( ! empty( $request['post'] ) && in_array( get_post_type( $request['post'] ), array( 'revision', 'attachment' ), true ) ) {
			return new WP_Error(
				'rest_invalid_param',
				__( 'Invalid parent type.' ),
				array( 'status' => 400 )
			);
		}

		$insert = $this->insert_attachment( $request );

		if ( is_wp_error( $insert ) ) {
			return $insert;
		}

		$schema = $this->get_item_schema();

		// Extract by name.
		$attachment_id = $insert['attachment_id'];
		$file          = $insert['file'];

		if ( isset( $request['alt_text'] ) ) {
			update_post_meta( $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( $request['alt_text'] ) );
		}

		if ( ! empty( $schema['properties']['featured_media'] ) && isset( $request['featured_media'] ) ) {
			$thumbnail_update = $this->handle_featured_media( $request['featured_media'], $attachment_id );

			if ( is_wp_error( $thumbnail_update ) ) {
				return $thumbnail_update;
			}
		}

		if ( ! empty( $schema['properties']['meta'] ) && isset( $request['meta'] ) ) {
			$meta_update = $this->meta->update_value( $request['meta'], $attachment_id );

			if ( is_wp_error( $meta_update ) ) {
				return $meta_update;
			}
		}

		$attachment    = get_post( $attachment_id );
		$fields_update = $this->update_additional_fields_for_object( $attachment, $request );

		if ( is_wp_error( $fields_update ) ) {
			return $fields_update;
		}

		$terms_update = $this->handle_terms( $attachment_id, $request );

		if ( is_wp_error( $terms_update ) ) {
			return $terms_update;
		}

		$request->set_param( 'context', 'edit' );

		/**
		 * Fires after a single attachment is completely created or updated via the REST API.
		 *
		 * @since 5.0.0
		 *
		 * @param WP_Post         $attachment Inserted or updated attachment object.
		 * @param WP_REST_Request $request    Request object.
		 * @param bool            $creating   True when creating an attachment, false when updating.
		 */
		do_action( 'rest_after_insert_attachment', $attachment, $request, true );

		wp_after_insert_post( $attachment, false, null );

		if ( wp_is_serving_rest_request() ) {
			/*
			 * Set a custom header with the attachment_id.
			 * Used by the browser/client to resume creating image sub-sizes after a PHP fatal error.
			 */
			header( 'X-WP-Upload-Attachment-ID: ' . $attachment_id );
		}

		// Include media and image functions to get access to wp_generate_attachment_metadata().
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		/*
		 * Post-process the upload (create image sub-sizes, make PDF thumbnails, etc.) and insert attachment meta.
		 * At this point the server may run out of resources and post-processing of uploaded images may fail.
		 */
		wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $file ) );

		$response = $this->prepare_item_for_response( $attachment, $request );
		$response = rest_ensure_response( $response );
		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $attachment_id ) ) );

		return $response;
	}

	/**
	 * Inserts the attachment post in the database. Does not update the attachment meta.
	 *
	 * @since 5.3.0
	 *
	 * @param WP_REST_Request $request
	 * @return array|WP_Error
	 */
	protected function insert_attachment( $request ) {
		// Get the file via $_FILES or raw data.
		$files   = $request->get_file_params();
		$headers = $request->get_headers();

		$time = null;

		// Matches logic in media_handle_upload().
		if ( ! empty( $request['post'] ) ) {
			$post = get_post( $request['post'] );
			// The post date doesn't usually matter for pages, so don't backdate this upload.
			if ( $post && 'page' !== $post->post_type && substr( $post->post_date, 0, 4 ) > 0 ) {
				$time = $post->post_date;
			}
		}

		if ( ! empty( $files ) ) {
			$file = $this->upload_from_file( $files, $headers, $time );
		} else {
			$file = $this->upload_from_data( $request->get_body(), $headers, $time );
		}

		if ( is_wp_error( $file ) ) {
			return $file;
		}

		$name       = wp_basename( $file['file'] );
		$name_parts = pathinfo( $name );
		$name       = trim( substr( $name, 0, -( 1 + strlen( $name_parts['extension'] ) ) ) );

		$url  = $file['url'];
		$type = $file['type'];
		$file = $file['file'];

		// Include image functions to get access to wp_read_image_metadata().
		require_once ABSPATH . 'wp-admin/includes/image.php';

		// Use image exif/iptc data for title and caption defaults if possible.
		$image_meta = wp_read_image_metadata( $file );

		if ( ! empty( $image_meta ) ) {
			if ( empty( $request['title'] ) && trim( $image_meta['title'] ) && ! is_numeric( sanitize_title( $image_meta['title'] ) ) ) {
				$request['title'] = $image_meta['title'];
			}

			if ( empty( $request['caption'] ) && trim( $image_meta['caption'] ) ) {
				$request['caption'] = $image_meta['caption'];
			}
		}

		$attachment = $this->prepare_item_for_database( $request );

		$attachment->post_mime_type = $type;
		$attachment->guid           = $url;

		// If the title was not set, use the original filename.
		if ( empty( $attachment->post_title ) && ! empty( $files['file']['name'] ) ) {
			// Remove the file extension (after the last `.`)
			$tmp_title = substr( $files['file']['name'], 0, strrpos( $files['file']['name'], '.' ) );

			if ( ! empty( $tmp_title ) ) {
				$attachment->post_title = $tmp_title;
			}
		}

		// Fall back to the original approach.
		if ( empty( $attachment->post_title ) ) {
			$attachment->post_title = preg_replace( '/\.[^.]+$/', '', wp_basename( $file ) );
		}

		// $post_parent is inherited from $attachment['post_parent'].
		$id = wp_insert_attachment( wp_slash( (array) $attachment ), $file, 0, true, false );

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
		 * @param WP_Post         $attachment Inserted or updated attachment object.
		 * @param WP_REST_Request $request    The request sent to the API.
		 * @param bool            $creating   True when creating an attachment, false when updating.
		 */
		do_action( 'rest_insert_attachment', $attachment, $request, true );

		return array(
			'attachment_id' => $id,
			'file'          => $file,
		);
	}

	/**
	 * Determines the featured media based on a request param.
	 *
	 * @since 6.5.0
	 *
	 * @param int $featured_media Featured Media ID.
	 * @param int $post_id        Post ID.
	 * @return bool|WP_Error Whether the post thumbnail was successfully deleted, otherwise WP_Error.
	 */
	protected function handle_featured_media( $featured_media, $post_id ) {
		$post_type         = get_post_type( $post_id );
		$thumbnail_support = current_theme_supports( 'post-thumbnails', $post_type ) && post_type_supports( $post_type, 'thumbnail' );

		// Similar check as in wp_insert_post().
		if ( ! $thumbnail_support && get_post_mime_type( $post_id ) ) {
			if ( wp_attachment_is( 'audio', $post_id ) ) {
				$thumbnail_support = post_type_supports( 'attachment:audio', 'thumbnail' ) || current_theme_supports( 'post-thumbnails', 'attachment:audio' );
			} elseif ( wp_attachment_is( 'video', $post_id ) ) {
				$thumbnail_support = post_type_supports( 'attachment:video', 'thumbnail' ) || current_theme_supports( 'post-thumbnails', 'attachment:video' );
			}
		}

		if ( $thumbnail_support ) {
			return parent::handle_featured_media( $featured_media, $post_id );
		}

		return new WP_Error(
			'rest_no_featured_media',
			sprintf(
				/* translators: %s: attachment mime type */
				__( 'This site does not support post thumbnails on attachments with MIME type %s.' ),
				get_post_mime_type( $post_id )
			),
			array( 'status' => 400 )
		);
	}

	/**
	 * Updates a single attachment.
	 *
	 * @since 4.7.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, WP_Error object on failure.
	 */
	public function update_item( $request ) {
		if ( ! empty( $request['post'] ) && in_array( get_post_type( $request['post'] ), array( 'revision', 'attachment' ), true ) ) {
			return new WP_Error(
				'rest_invalid_param',
				__( 'Invalid parent type.' ),
				array( 'status' => 400 )
			);
		}

		$attachment_before = get_post( $request['id'] );
		$response          = parent::update_item( $request );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response = rest_ensure_response( $response );
		$data     = $response->get_data();

		if ( isset( $request['alt_text'] ) ) {
			update_post_meta( $data['id'], '_wp_attachment_image_alt', $request['alt_text'] );
		}

		$attachment = get_post( $request['id'] );

		if ( ! empty( $schema['properties']['featured_media'] ) && isset( $request['featured_media'] ) ) {
			$thumbnail_update = $this->handle_featured_media( $request['featured_media'], $attachment->ID );

			if ( is_wp_error( $thumbnail_update ) ) {
				return $thumbnail_update;
			}
		}

		$fields_update = $this->update_additional_fields_for_object( $attachment, $request );

		if ( is_wp_error( $fields_update ) ) {
			return $fields_update;
		}

		$request->set_param( 'context', 'edit' );

		/** This action is documented in wp-includes/rest-api/endpoints/class-wp-rest-attachments-controller.php */
		do_action( 'rest_after_insert_attachment', $attachment, $request, false );

		wp_after_insert_post( $attachment, true, $attachment_before );

		$response = $this->prepare_item_for_response( $attachment, $request );
		$response = rest_ensure_response( $response );

		return $response;
	}

	/**
	 * Performs post-processing on an attachment.
	 *
	 * @since 5.3.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, WP_Error object on failure.
	 */
	public function post_process_item( $request ) {
		switch ( $request['action'] ) {
			case 'create-image-subsizes':
				require_once ABSPATH . 'wp-admin/includes/image.php';
				wp_update_image_subsizes( $request['id'] );
				break;
		}

		$request['context'] = 'edit';

		return $this->prepare_item_for_response( get_post( $request['id'] ), $request );
	}

	/**
	 * Checks if a given request can perform post-processing on an attachment.
	 *
	 * @since 5.3.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has access to update the item, WP_Error object otherwise.
	 */
	public function post_process_item_permissions_check( $request ) {
		return $this->update_item_permissions_check( $request );
	}

	/**
	 * Checks if a given request has access to editing media.
	 *
	 * @since 5.5.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function edit_media_item_permissions_check( $request ) {
		if ( ! current_user_can( 'upload_files' ) ) {
			return new WP_Error(
				'rest_cannot_edit_image',
				__( 'Sorry, you are not allowed to upload media on this site.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return $this->update_item_permissions_check( $request );
	}

	/**
	 * Applies edits to a media item and creates a new attachment record.
	 *
	 * @since 5.5.0
	 * @since 6.9.0 Adds flips capability and editable fields for the newly-created attachment post.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, WP_Error object on failure.
	 */
	public function edit_media_item( $request ) {
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$attachment_id = $request['id'];

		// This also confirms the attachment is an image.
		$image_file = wp_get_original_image_path( $attachment_id );
		$image_meta = wp_get_attachment_metadata( $attachment_id );

		if (
			! $image_meta ||
			! $image_file ||
			! wp_image_file_matches_image_meta( $request['src'], $image_meta, $attachment_id )
		) {
			return new WP_Error(
				'rest_unknown_attachment',
				__( 'Unable to get meta information for file.' ),
				array( 'status' => 404 )
			);
		}

		$supported_types = array( 'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/avif', 'image/heic' );
		$mime_type       = get_post_mime_type( $attachment_id );
		if ( ! in_array( $mime_type, $supported_types, true ) ) {
			return new WP_Error(
				'rest_cannot_edit_file_type',
				__( 'This type of file cannot be edited.' ),
				array( 'status' => 400 )
			);
		}

		// The `modifiers` param takes precedence over the older format.
		if ( isset( $request['modifiers'] ) ) {
			$modifiers = $request['modifiers'];
		} else {
			$modifiers = array();

			if ( isset( $request['flip']['horizontal'] ) || isset( $request['flip']['vertical'] ) ) {
				$flip_args = array(
					'vertical'   => isset( $request['flip']['vertical'] ) ? (bool) $request['flip']['vertical'] : false,
					'horizontal' => isset( $request['flip']['horizontal'] ) ? (bool) $request['flip']['horizontal'] : false,
				);

				$modifiers[] = array(
					'type' => 'flip',
					'args' => array(
						'flip' => $flip_args,
					),
				);
			}

			if ( ! empty( $request['rotation'] ) ) {
				$modifiers[] = array(
					'type' => 'rotate',
					'args' => array(
						'angle' => $request['rotation'],
					),
				);
			}

			if ( isset( $request['x'], $request['y'], $request['width'], $request['height'] ) ) {
				$modifiers[] = array(
					'type' => 'crop',
					'args' => array(
						'left'   => $request['x'],
						'top'    => $request['y'],
						'width'  => $request['width'],
						'height' => $request['height'],
					),
				);
			}

			if ( 0 === count( $modifiers ) ) {
				return new WP_Error(
					'rest_image_not_edited',
					__( 'The image was not edited. Edit the image before applying the changes.' ),
					array( 'status' => 400 )
				);
			}
		}

		/*
		 * If the file doesn't exist, attempt a URL fopen on the src link.
		 * This can occur with certain file replication plugins.
		 * Keep the original file path to get a modified name later.
		 */
		$image_file_to_edit = $image_file;
		if ( ! file_exists( $image_file_to_edit ) ) {
			$image_file_to_edit = _load_image_to_edit_path( $attachment_id );
		}

		$image_editor = wp_get_image_editor( $image_file_to_edit );

		if ( is_wp_error( $image_editor ) ) {
			return new WP_Error(
				'rest_unknown_image_file_type',
				__( 'Unable to edit this image.' ),
				array( 'status' => 500 )
			);
		}

		foreach ( $modifiers as $modifier ) {
			$args = $modifier['args'];
			switch ( $modifier['type'] ) {
				case 'flip':
					/*
					 * Flips the current image.
					 * The vertical flip is the first argument (flip along horizontal axis), the horizontal flip is the second argument (flip along vertical axis).
					 * See: WP_Image_Editor::flip()
					 */
					$result = $image_editor->flip( $args['flip']['vertical'], $args['flip']['horizontal'] );
					if ( is_wp_error( $result ) ) {
						return new WP_Error(
							'rest_image_flip_failed',
							__( 'Unable to flip this image.' ),
							array( 'status' => 500 )
						);
					}
					break;
				case 'rotate':
					// Rotation direction: clockwise vs. counterclockwise.
					$rotate = 0 - $args['angle'];

					if ( 0 !== $rotate ) {
						$result = $image_editor->rotate( $rotate );

						if ( is_wp_error( $result ) ) {
							return new WP_Error(
								'rest_image_rotation_failed',
								__( 'Unable to rotate this image.' ),
								array( 'status' => 500 )
							);
						}
					}

					break;

				case 'crop':
					$size = $image_editor->get_size();

					$crop_x = (int) round( ( $size['width'] * $args['left'] ) / 100.0 );
					$crop_y = (int) round( ( $size['height'] * $args['top'] ) / 100.0 );
					$width  = (int) round( ( $size['width'] * $args['width'] ) / 100.0 );
					$height = (int) round( ( $size['height'] * $args['height'] ) / 100.0 );

					if ( $size['width'] !== $width || $size['height'] !== $height ) {
						$result = $image_editor->crop( $crop_x, $crop_y, $width, $height );

						if ( is_wp_error( $result ) ) {
							return new WP_Error(
								'rest_image_crop_failed',
								__( 'Unable to crop this image.' ),
								array( 'status' => 500 )
							);
						}
					}

					break;

			}
		}

		// Calculate the file name.
		$image_ext  = pathinfo( $image_file, PATHINFO_EXTENSION );
		$image_name = wp_basename( $image_file, ".{$image_ext}" );

		/*
		 * Do not append multiple `-edited` to the file name.
		 * The user may be editing a previously edited image.
		 */
		if ( preg_match( '/-edited(-\d+)?$/', $image_name ) ) {
			// Remove any `-1`, `-2`, etc. `wp_unique_filename()` will add the proper number.
			$image_name = preg_replace( '/-edited(-\d+)?$/', '-edited', $image_name );
		} else {
			// Append `-edited` before the extension.
			$image_name .= '-edited';
		}

		$filename = "{$image_name}.{$image_ext}";

		// Create the uploads subdirectory if needed.
		$uploads = wp_upload_dir();

		// Make the file name unique in the (new) upload directory.
		$filename = wp_unique_filename( $uploads['path'], $filename );

		// Save to disk.
		$saved = $image_editor->save( $uploads['path'] . "/$filename" );

		if ( is_wp_error( $saved ) ) {
			return $saved;
		}

		// Grab original attachment post so we can use it to set defaults.
		$original_attachment_post = get_post( $attachment_id );

		// Check request fields and assign default values.
		$new_attachment_post                 = $this->prepare_item_for_database( $request );
		$new_attachment_post->post_mime_type = $saved['mime-type'];
		$new_attachment_post->guid           = $uploads['url'] . "/$filename";

		// Unset ID so wp_insert_attachment generates a new ID.
		unset( $new_attachment_post->ID );

		// Set new attachment post title with fallbacks.
		$new_attachment_post->post_title = $new_attachment_post->post_title ?? $original_attachment_post->post_title ?? $image_name;

		// Set new attachment post caption (post_excerpt).
		$new_attachment_post->post_excerpt = $new_attachment_post->post_excerpt ?? $original_attachment_post->post_excerpt ?? '';

		// Set new attachment post description (post_content) with fallbacks.
		$new_attachment_post->post_content = $new_attachment_post->post_content ?? $original_attachment_post->post_content ?? '';

		// Set post parent if set in request, else the default of `0` (no parent).
		$new_attachment_post->post_parent = $new_attachment_post->post_parent ?? 0;

		// Insert the new attachment post.
		$new_attachment_id = wp_insert_attachment( wp_slash( (array) $new_attachment_post ), $saved['path'], 0, true );

		if ( is_wp_error( $new_attachment_id ) ) {
			if ( 'db_update_error' === $new_attachment_id->get_error_code() ) {
				$new_attachment_id->add_data( array( 'status' => 500 ) );
			} else {
				$new_attachment_id->add_data( array( 'status' => 400 ) );
			}

			return $new_attachment_id;
		}

		// First, try to use the alt text from the request. If not set, copy the image alt text from the original attachment.
		$image_alt = isset( $request['alt_text'] ) ? sanitize_text_field( $request['alt_text'] ) : get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );

		if ( ! empty( $image_alt ) ) {
			// update_post_meta() expects slashed.
			update_post_meta( $new_attachment_id, '_wp_attachment_image_alt', wp_slash( $image_alt ) );
		}

		if ( wp_is_serving_rest_request() ) {
			/*
			 * Set a custom header with the attachment_id.
			 * Used by the browser/client to resume creating image sub-sizes after a PHP fatal error.
			 */
			header( 'X-WP-Upload-Attachment-ID: ' . $new_attachment_id );
		}

		// Generate image sub-sizes and meta.
		$new_image_meta = wp_generate_attachment_metadata( $new_attachment_id, $saved['path'] );

		// Copy the EXIF metadata from the original attachment if not generated for the edited image.
		if ( isset( $image_meta['image_meta'] ) && isset( $new_image_meta['image_meta'] ) && is_array( $new_image_meta['image_meta'] ) ) {
			// Merge but skip empty values.
			foreach ( (array) $image_meta['image_meta'] as $key => $value ) {
				if ( empty( $new_image_meta['image_meta'][ $key ] ) && ! empty( $value ) ) {
					$new_image_meta['image_meta'][ $key ] = $value;
				}
			}
		}

		// Reset orientation. At this point the image is edited and orientation is correct.
		if ( ! empty( $new_image_meta['image_meta']['orientation'] ) ) {
			$new_image_meta['image_meta']['orientation'] = 1;
		}

		// The attachment_id may change if the site is exported and imported.
		$new_image_meta['parent_image'] = array(
			'attachment_id' => $attachment_id,
			// Path to the originally uploaded image file relative to the uploads directory.
			'file'          => _wp_relative_upload_path( $image_file ),
		);

		/**
		 * Filters the meta data for the new image created by editing an existing image.
		 *
		 * @since 5.5.0
		 *
		 * @param array $new_image_meta    Meta data for the new image.
		 * @param int   $new_attachment_id Attachment post ID for the new image.
		 * @param int   $attachment_id     Attachment post ID for the edited (parent) image.
		 */
		$new_image_meta = apply_filters( 'wp_edited_image_metadata', $new_image_meta, $new_attachment_id, $attachment_id );

		wp_update_attachment_metadata( $new_attachment_id, $new_image_meta );

		$response = $this->prepare_item_for_response( get_post( $new_attachment_id ), $request );
		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '%s/%s/%s', $this->namespace, $this->rest_base, $new_attachment_id ) ) );

		return $response;
	}

	/**
	 * Prepares a single attachment for create or update.
	 *
	 * @since 4.7.0
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return stdClass|WP_Error Post object.
	 */
	protected function prepare_item_for_database( $request ) {
		$prepared_attachment = parent::prepare_item_for_database( $request );

		// Attachment caption (post_excerpt internally).
		if ( isset( $request['caption'] ) ) {
			if ( is_string( $request['caption'] ) ) {
				$prepared_attachment->post_excerpt = $request['caption'];
			} elseif ( isset( $request['caption']['raw'] ) ) {
				$prepared_attachment->post_excerpt = $request['caption']['raw'];
			}
		}

		// Attachment description (post_content internally).
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
	 * @since 5.9.0 Renamed `$post` to `$item` to match parent class for PHP 8 named parameter support.
	 *
	 * @param WP_Post         $item    Attachment object.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response Response object.
	 */
	public function prepare_item_for_response( $item, $request ) {
		// Restores the more descriptive, specific name for use within this method.
		$post = $item;

		$response = parent::prepare_item_for_response( $post, $request );
		$fields   = $this->get_fields_for_response( $request );
		$data     = $response->get_data();

		if ( in_array( 'description', $fields, true ) ) {
			$data['description'] = array(
				'raw'      => $post->post_content,
				/** This filter is documented in wp-includes/post-template.php */
				'rendered' => apply_filters( 'the_content', $post->post_content ),
			);
		}

		if ( in_array( 'caption', $fields, true ) ) {
			/** This filter is documented in wp-includes/post-template.php */
			$caption = apply_filters( 'get_the_excerpt', $post->post_excerpt, $post );

			/** This filter is documented in wp-includes/post-template.php */
			$caption = apply_filters( 'the_excerpt', $caption );

			$data['caption'] = array(
				'raw'      => $post->post_excerpt,
				'rendered' => $caption,
			);
		}

		if ( in_array( 'alt_text', $fields, true ) ) {
			$data['alt_text'] = get_post_meta( $post->ID, '_wp_attachment_image_alt', true );
		}

		if ( in_array( 'media_type', $fields, true ) ) {
			$data['media_type'] = wp_attachment_is_image( $post->ID ) ? 'image' : 'file';
		}

		if ( in_array( 'mime_type', $fields, true ) ) {
			$data['mime_type'] = $post->post_mime_type;
		}

		if ( in_array( 'media_details', $fields, true ) ) {
			$data['media_details'] = wp_get_attachment_metadata( $post->ID );

			// Ensure empty details is an empty object.
			if ( empty( $data['media_details'] ) ) {
				$data['media_details'] = new stdClass();
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
				$data['media_details']['sizes'] = new stdClass();
			}
		}

		if ( in_array( 'post', $fields, true ) ) {
			$data['post'] = ! empty( $post->post_parent ) ? (int) $post->post_parent : null;
		}

		if ( in_array( 'source_url', $fields, true ) ) {
			$data['source_url'] = wp_get_attachment_url( $post->ID );
		}

		if ( in_array( 'missing_image_sizes', $fields, true ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
			$data['missing_image_sizes'] = array_keys( wp_get_missing_image_subsizes( $post->ID ) );
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';

		$data = $this->filter_response_by_context( $data, $context );

		$links = $response->get_links();

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		foreach ( $links as $rel => $rel_links ) {
			foreach ( $rel_links as $link ) {
				$response->add_link( $rel, $link['href'], $link['attributes'] );
			}
		}

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
	 * Prepares attachment links for the request.
	 *
	 * @since 6.9.0
	 *
	 * @param WP_Post $post Post object.
	 * @return array Links for the given attachment.
	 */
	protected function prepare_links( $post ) {
		$links = parent::prepare_links( $post );

		if ( ! empty( $post->post_parent ) ) {
			$post = get_post( $post->post_parent );

			if ( ! empty( $post ) ) {
				$links['https://api.w.org/attached-to'] = array(
					'href'       => rest_url( rest_get_route_for_post( $post ) ),
					'embeddable' => true,
					'post_type'  => $post->post_type,
					'id'         => $post->ID,
				);
			}
		}

		return $links;
	}

	/**
	 * Retrieves the attachment's schema, conforming to JSON Schema.
	 *
	 * @since 4.7.0
	 *
	 * @return array Item schema as an array.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$schema = parent::get_item_schema();

		$schema['properties']['alt_text'] = array(
			'description' => __( 'Alternative text to display when attachment is not displayed.' ),
			'type'        => 'string',
			'context'     => array( 'view', 'edit', 'embed' ),
			'arg_options' => array(
				'sanitize_callback' => 'sanitize_text_field',
			),
		);

		$schema['properties']['caption'] = array(
			'description' => __( 'The attachment caption.' ),
			'type'        => 'object',
			'context'     => array( 'view', 'edit', 'embed' ),
			'arg_options' => array(
				'sanitize_callback' => null, // Note: sanitization implemented in self::prepare_item_for_database().
				'validate_callback' => null, // Note: validation implemented in self::prepare_item_for_database().
			),
			'properties'  => array(
				'raw'      => array(
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
				'sanitize_callback' => null, // Note: sanitization implemented in self::prepare_item_for_database().
				'validate_callback' => null, // Note: validation implemented in self::prepare_item_for_database().
			),
			'properties'  => array(
				'raw'      => array(
					'description' => __( 'Description for the attachment, as it exists in the database.' ),
					'type'        => 'string',
					'context'     => array( 'edit' ),
				),
				'rendered' => array(
					'description' => __( 'HTML description for the attachment, transformed for display.' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
			),
		);

		$schema['properties']['media_type'] = array(
			'description' => __( 'Attachment type.' ),
			'type'        => 'string',
			'enum'        => array( 'image', 'file' ),
			'context'     => array( 'view', 'edit', 'embed' ),
			'readonly'    => true,
		);

		$schema['properties']['mime_type'] = array(
			'description' => __( 'The attachment MIME type.' ),
			'type'        => 'string',
			'context'     => array( 'view', 'edit', 'embed' ),
			'readonly'    => true,
		);

		$schema['properties']['media_details'] = array(
			'description' => __( 'Details about the media file, specific to its type.' ),
			'type'        => 'object',
			'context'     => array( 'view', 'edit', 'embed' ),
			'readonly'    => true,
		);

		$schema['properties']['post'] = array(
			'description' => __( 'The ID for the associated post of the attachment.' ),
			'type'        => 'integer',
			'context'     => array( 'view', 'edit' ),
		);

		$schema['properties']['source_url'] = array(
			'description' => __( 'URL to the original attachment file.' ),
			'type'        => 'string',
			'format'      => 'uri',
			'context'     => array( 'view', 'edit', 'embed' ),
			'readonly'    => true,
		);

		$schema['properties']['missing_image_sizes'] = array(
			'description' => __( 'List of the missing image sizes of the attachment.' ),
			'type'        => 'array',
			'items'       => array( 'type' => 'string' ),
			'context'     => array( 'edit' ),
			'readonly'    => true,
		);

		unset( $schema['properties']['password'] );

		$this->schema = $schema;

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Handles an upload via raw POST data.
	 *
	 * @since 4.7.0
	 * @since 6.6.0 Added the `$time` parameter.
	 *
	 * @param string      $data    Supplied file data.
	 * @param array       $headers HTTP headers from the request.
	 * @param string|null $time    Optional. Time formatted in 'yyyy/mm'. Default null.
	 * @return array|WP_Error Data from wp_handle_sideload().
	 */
	protected function upload_from_data( $data, $headers, $time = null ) {
		if ( empty( $data ) ) {
			return new WP_Error(
				'rest_upload_no_data',
				__( 'No data supplied.' ),
				array( 'status' => 400 )
			);
		}

		if ( empty( $headers['content_type'] ) ) {
			return new WP_Error(
				'rest_upload_no_content_type',
				__( 'No Content-Type supplied.' ),
				array( 'status' => 400 )
			);
		}

		if ( empty( $headers['content_disposition'] ) ) {
			return new WP_Error(
				'rest_upload_no_content_disposition',
				__( 'No Content-Disposition supplied.' ),
				array( 'status' => 400 )
			);
		}

		$filename = self::get_filename_from_disposition( $headers['content_disposition'] );

		if ( empty( $filename ) ) {
			return new WP_Error(
				'rest_upload_invalid_disposition',
				__( 'Invalid Content-Disposition supplied. Content-Disposition needs to be formatted as `attachment; filename="image.png"` or similar.' ),
				array( 'status' => 400 )
			);
		}

		if ( ! empty( $headers['content_md5'] ) ) {
			$content_md5 = array_shift( $headers['content_md5'] );
			$expected    = trim( $content_md5 );
			$actual      = md5( $data );

			if ( $expected !== $actual ) {
				return new WP_Error(
					'rest_upload_hash_mismatch',
					__( 'Content hash did not match expected.' ),
					array( 'status' => 412 )
				);
			}
		}

		// Get the content-type.
		$type = array_shift( $headers['content_type'] );

		// Include filesystem functions to get access to wp_tempnam() and wp_handle_sideload().
		require_once ABSPATH . 'wp-admin/includes/file.php';

		// Save the file.
		$tmpfname = wp_tempnam( $filename );

		$fp = fopen( $tmpfname, 'w+' );

		if ( ! $fp ) {
			return new WP_Error(
				'rest_upload_file_error',
				__( 'Could not open file handle.' ),
				array( 'status' => 500 )
			);
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

		$size_check = self::check_upload_size( $file_data );
		if ( is_wp_error( $size_check ) ) {
			return $size_check;
		}

		$overrides = array(
			'test_form' => false,
		);

		$sideloaded = wp_handle_sideload( $file_data, $overrides, $time );

		if ( isset( $sideloaded['error'] ) ) {
			@unlink( $tmpfname );

			return new WP_Error(
				'rest_upload_sideload_error',
				$sideloaded['error'],
				array( 'status' => 500 )
			);
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
	 *
	 * @link https://tools.ietf.org/html/rfc2388
	 * @link https://tools.ietf.org/html/rfc6266
	 *
	 * @param string[] $disposition_header List of Content-Disposition header values.
	 * @return string|null Filename if available, or null if not found.
	 */
	public static function get_filename_from_disposition( $disposition_header ) {
		// Get the filename.
		$filename = null;

		foreach ( $disposition_header as $value ) {
			$value = trim( $value );

			if ( ! str_contains( $value, ';' ) ) {
				continue;
			}

			list( , $attr_parts ) = explode( ';', $value, 2 );

			$attr_parts = explode( ';', $attr_parts );
			$attributes = array();

			foreach ( $attr_parts as $part ) {
				if ( ! str_contains( $part, '=' ) ) {
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
			if ( str_starts_with( $filename, '"' ) && str_ends_with( $filename, '"' ) ) {
				$filename = substr( $filename, 1, -1 );
			}
		}

		return $filename;
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

	/**
	 * Handles an upload via multipart/form-data ($_FILES).
	 *
	 * @since 4.7.0
	 * @since 6.6.0 Added the `$time` parameter.
	 *
	 * @param array       $files   Data from the `$_FILES` superglobal.
	 * @param array       $headers HTTP headers from the request.
	 * @param string|null $time    Optional. Time formatted in 'yyyy/mm'. Default null.
	 * @return array|WP_Error Data from wp_handle_upload().
	 */
	protected function upload_from_file( $files, $headers, $time = null ) {
		if ( empty( $files ) ) {
			return new WP_Error(
				'rest_upload_no_data',
				__( 'No data supplied.' ),
				array( 'status' => 400 )
			);
		}

		// Verify hash, if given.
		if ( ! empty( $headers['content_md5'] ) ) {
			$content_md5 = array_shift( $headers['content_md5'] );
			$expected    = trim( $content_md5 );
			$actual      = md5_file( $files['file']['tmp_name'] );

			if ( $expected !== $actual ) {
				return new WP_Error(
					'rest_upload_hash_mismatch',
					__( 'Content hash did not match expected.' ),
					array( 'status' => 412 )
				);
			}
		}

		// Pass off to WP to handle the actual upload.
		$overrides = array(
			'test_form' => false,
		);

		// Bypasses is_uploaded_file() when running unit tests.
		if ( defined( 'DIR_TESTDATA' ) && DIR_TESTDATA ) {
			$overrides['action'] = 'wp_handle_mock_upload';
		}

		$size_check = self::check_upload_size( $files['file'] );
		if ( is_wp_error( $size_check ) ) {
			return $size_check;
		}

		// Include filesystem functions to get access to wp_handle_upload().
		require_once ABSPATH . 'wp-admin/includes/file.php';

		$file = wp_handle_upload( $files['file'], $overrides, $time );

		if ( isset( $file['error'] ) ) {
			return new WP_Error(
				'rest_upload_unknown_error',
				$file['error'],
				array( 'status' => 500 )
			);
		}

		return $file;
	}

	/**
	 * Retrieves the supported media types.
	 *
	 * Media types are considered the MIME type category.
	 *
	 * @since 4.7.0
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

	/**
	 * Determine if uploaded file exceeds space quota on multisite.
	 *
	 * Replicates check_upload_size().
	 *
	 * @since 4.9.8
	 *
	 * @param array $file $_FILES array for a given file.
	 * @return true|WP_Error True if can upload, error for errors.
	 */
	protected function check_upload_size( $file ) {
		if ( ! is_multisite() ) {
			return true;
		}

		if ( get_site_option( 'upload_space_check_disabled' ) ) {
			return true;
		}

		$space_left = get_upload_space_available();

		$file_size = filesize( $file['tmp_name'] );

		if ( $space_left < $file_size ) {
			return new WP_Error(
				'rest_upload_limited_space',
				/* translators: %s: Required disk space in kilobytes. */
				sprintf( __( 'Not enough space to upload. %s KB needed.' ), number_format( ( $file_size - $space_left ) / KB_IN_BYTES ) ),
				array( 'status' => 400 )
			);
		}

		if ( $file_size > ( KB_IN_BYTES * get_site_option( 'fileupload_maxk', 1500 ) ) ) {
			return new WP_Error(
				'rest_upload_file_too_big',
				/* translators: %s: Maximum allowed file size in kilobytes. */
				sprintf( __( 'This file is too big. Files must be less than %s KB in size.' ), get_site_option( 'fileupload_maxk', 1500 ) ),
				array( 'status' => 400 )
			);
		}

		// Include multisite admin functions to get access to upload_is_user_over_quota().
		require_once ABSPATH . 'wp-admin/includes/ms.php';

		if ( upload_is_user_over_quota( false ) ) {
			return new WP_Error(
				'rest_upload_user_quota_exceeded',
				__( 'You have used your space quota. Please delete files before uploading.' ),
				array( 'status' => 400 )
			);
		}

		return true;
	}

	/**
	 * Gets the request args for the edit item route.
	 *
	 * @since 5.5.0
	 * @since 6.9.0 Adds flips capability and editable fields for the newly-created attachment post.
	 *
	 * @return array
	 */
	protected function get_edit_media_item_args() {
		$args = array(
			'src'       => array(
				'description' => __( 'URL to the edited image file.' ),
				'type'        => 'string',
				'format'      => 'uri',
				'required'    => true,
			),
			// The `modifiers` param takes precedence over the older format.
			'modifiers' => array(
				'description' => __( 'Array of image edits.' ),
				'type'        => 'array',
				'minItems'    => 1,
				'items'       => array(
					'description' => __( 'Image edit.' ),
					'type'        => 'object',
					'required'    => array(
						'type',
						'args',
					),
					'oneOf'       => array(
						array(
							'title'      => __( 'Flip' ),
							'properties' => array(
								'type' => array(
									'description' => __( 'Flip type.' ),
									'type'        => 'string',
									'enum'        => array( 'flip' ),
								),
								'args' => array(
									'description' => __( 'Flip arguments.' ),
									'type'        => 'object',
									'required'    => array(
										'flip',
									),
									'properties'  => array(
										'flip' => array(
											'description' => __( 'Flip direction.' ),
											'type'        => 'object',
											'required'    => array(
												'horizontal',
												'vertical',
											),
											'properties'  => array(
												'horizontal' => array(
													'description' => __( 'Whether to flip in the horizontal direction.' ),
													'type' => 'boolean',
												),
												'vertical' => array(
													'description' => __( 'Whether to flip in the vertical direction.' ),
													'type' => 'boolean',
												),
											),
										),
									),
								),
							),
						),
						array(
							'title'      => __( 'Rotation' ),
							'properties' => array(
								'type' => array(
									'description' => __( 'Rotation type.' ),
									'type'        => 'string',
									'enum'        => array( 'rotate' ),
								),
								'args' => array(
									'description' => __( 'Rotation arguments.' ),
									'type'        => 'object',
									'required'    => array(
										'angle',
									),
									'properties'  => array(
										'angle' => array(
											'description' => __( 'Angle to rotate clockwise in degrees.' ),
											'type'        => 'number',
										),
									),
								),
							),
						),
						array(
							'title'      => __( 'Crop' ),
							'properties' => array(
								'type' => array(
									'description' => __( 'Crop type.' ),
									'type'        => 'string',
									'enum'        => array( 'crop' ),
								),
								'args' => array(
									'description' => __( 'Crop arguments.' ),
									'type'        => 'object',
									'required'    => array(
										'left',
										'top',
										'width',
										'height',
									),
									'properties'  => array(
										'left'   => array(
											'description' => __( 'Horizontal position from the left to begin the crop as a percentage of the image width.' ),
											'type'        => 'number',
										),
										'top'    => array(
											'description' => __( 'Vertical position from the top to begin the crop as a percentage of the image height.' ),
											'type'        => 'number',
										),
										'width'  => array(
											'description' => __( 'Width of the crop as a percentage of the image width.' ),
											'type'        => 'number',
										),
										'height' => array(
											'description' => __( 'Height of the crop as a percentage of the image height.' ),
											'type'        => 'number',
										),
									),
								),
							),
						),
					),
				),
			),
			'rotation'  => array(
				'description'      => __( 'The amount to rotate the image clockwise in degrees. DEPRECATED: Use `modifiers` instead.' ),
				'type'             => 'integer',
				'minimum'          => 0,
				'exclusiveMinimum' => true,
				'maximum'          => 360,
				'exclusiveMaximum' => true,
			),
			'x'         => array(
				'description' => __( 'As a percentage of the image, the x position to start the crop from. DEPRECATED: Use `modifiers` instead.' ),
				'type'        => 'number',
				'minimum'     => 0,
				'maximum'     => 100,
			),
			'y'         => array(
				'description' => __( 'As a percentage of the image, the y position to start the crop from. DEPRECATED: Use `modifiers` instead.' ),
				'type'        => 'number',
				'minimum'     => 0,
				'maximum'     => 100,
			),
			'width'     => array(
				'description' => __( 'As a percentage of the image, the width to crop the image to. DEPRECATED: Use `modifiers` instead.' ),
				'type'        => 'number',
				'minimum'     => 0,
				'maximum'     => 100,
			),
			'height'    => array(
				'description' => __( 'As a percentage of the image, the height to crop the image to. DEPRECATED: Use `modifiers` instead.' ),
				'type'        => 'number',
				'minimum'     => 0,
				'maximum'     => 100,
			),
		);

		/*
		 * Get the args based on the post schema. This calls `rest_get_endpoint_args_for_schema()`,
		 * which also takes care of sanitization and validation.
		 */
		$update_item_args = $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE );

		if ( isset( $update_item_args['caption'] ) ) {
			$args['caption'] = $update_item_args['caption'];
		}

		if ( isset( $update_item_args['description'] ) ) {
			$args['description'] = $update_item_args['description'];
		}

		if ( isset( $update_item_args['title'] ) ) {
			$args['title'] = $update_item_args['title'];
		}

		if ( isset( $update_item_args['post'] ) ) {
			$args['post'] = $update_item_args['post'];
		}

		if ( isset( $update_item_args['alt_text'] ) ) {
			$args['alt_text'] = $update_item_args['alt_text'];
		}

		return $args;
	}
}
