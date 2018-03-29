<?php
/**
 * Import and Export data.
 *
 * @package Core\Portability
 */

/**
 * Handles the portability workflow.
 *
 * @private
 *
 * @package ET\Core\Portability
 */
final class ET_Core_Portability {

	/**
	 * Current instance.
	 *
	 * @since 1.0.0
	 *
	 * @type array
	 */
	private $instance = array();

	/**
	 * Constructor.
	 *
	 * @param string $context Protability context previously registered.
	 */
	public function __construct( $context ) {
		// perform this check only in admin area to make sure class loaded properly in Frontend Builder
		if ( ! current_user_can( 'switch_themes' ) && is_admin() ) {
			return false;
		}

		if ( ! $this->instance = et_core_cache_get( $context, 'et_core_portability' ) ) {
			return false;
		}

		if ( $this->instance->view ) {
			if ( ! empty( $_GET['et_fb'] ) ) {
				$this->assets();
			} else {
				add_action( 'admin_footer', array( $this, 'modal' ) );
				add_action( 'customize_controls_print_footer_scripts', array( $this, 'modal' ) );
				add_action( 'admin_enqueue_scripts', array( $this, 'assets' ), 5 );
			}
		}
	}

	/**
	 * Import data.
	 *
	 * @since 1.0.0
	 */
	public function import() {
		// Verify nonce.
		if ( ! ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'et_core_portability_nonce' ) ) ) {
			wp_send_json_error();
		}

		$this->prevent_failure();

		$timestamp = $this->get_timestamp();
		$filesystem = $this->set_filesystem();
		$temp_file_id = sanitize_file_name( $timestamp );
		$temp_file = $this->has_temp_file( $temp_file_id, 'et_core_import' );

		if ( $temp_file ) {
			$import = json_decode( $filesystem->get_contents( $temp_file ), true );
		} else {
			if ( ! isset( $_FILES['file'] ) ) {
				wp_send_json_error();
			}

			// Upload temporary file.
			$upload = wp_handle_upload( $_FILES['file'], array(
				'test_size' => false,
				'test_type' => false,
				'test_form' => false,
			) );

			$temp_file = $this->temp_file( $temp_file_id, 'et_core_import', $upload['file'] );
			$import = json_decode( $filesystem->get_contents( $temp_file ), true );
			$import = $this->validate( $import );
			$import['data'] = $this->apply_query( $import['data'], 'set' );

			if ( ! isset( $import['context'] ) || ( isset( $import['context'] ) && $import['context'] !== $this->instance->context ) ) {
				wp_send_json_error( array( 'message' => 'importContextFail' ) );
			}

			$filesystem->put_contents( $upload['file'], wp_json_encode( (array) $import ) );
		}

		// Upload images and replace current urls.
		if ( isset( $import['images'] ) ) {
			$images = $this->maybe_paginate_images( (array) $import['images'], 'upload_images', $timestamp );
			$import['data'] = $this->replace_images_urls( $images, $import['data'] );
		}

		$data = $import['data'];
		$success = array( 'timestamp' => $timestamp );

		$this->delete_temp_files( 'et_core_import' );

		if ( 'options' === $this->instance->type ) {
			// Reset all data besides excluded data.
			$current_data = $this->apply_query( get_option( $this->instance->target, array() ), 'unset' );

			// Merge remaining current data with new data and update options.
			update_option( $this->instance->target, array_merge( $current_data, $data ) );

			set_theme_mod( 'et_pb_css_synced', 'no' );
		}

		// Pass the post content and let js save the post.
		if ( 'post' === $this->instance->type ) {
			$success['postContent'] = reset( $data );
		}

		if ( 'post_type' === $this->instance->type ) {
			if ( ! $this->import_posts( $data ) ) {
				wp_send_json_error();
			}
		}

		wp_send_json_success( $success );
	}

	/**
	 * Initiate Export.
	 *
	 * @since 1.0.0
	 */
	public function export() {
		if ( ! ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'et_core_portability_nonce' ) ) ) {
			wp_send_json_error();
		}

		$this->prevent_failure();

		$timestamp = $this->get_timestamp();
		$filesystem = $this->set_filesystem();
		$temp_file_id = sanitize_file_name( $timestamp );
		$temp_file = $this->has_temp_file( $temp_file_id, 'et_core_export' );

		if ( $temp_file ) {
			$data = json_decode( $filesystem->get_contents( $temp_file ), true );
		} else {
			$temp_file = $this->temp_file( $temp_file_id, 'et_core_export' );

			if ( 'options' === $this->instance->type ) {
				$data = get_option( $this->instance->target, array() );
			}

			if ( 'post' === $this->instance->type ) {
				if ( ! ( isset( $_POST['post'] ) || isset( $_POST['content'] ) ) ) {
					wp_send_json_error();
				}

				$data = array( intval( $_POST['post'] ) => wp_kses_post( stripcslashes( $_POST['content'] ) ) );
			}

			if ( 'post_type' === $this->instance->type ) {
				$data = $this->export_posts_query();
			}

			$data = $this->apply_query( $data, 'set' );

			// put contents into file, this is temporary,
			// if images get paginated, this content will be brought back out
			// of a temp file in paginated request
			$filesystem->put_contents( $temp_file, wp_json_encode( (array) $data ) );
		}

		$images = $this->get_data_images( $data );
		$data = array(
			'context' => $this->instance->context,
			'data'    => $data,
			'images'  => $this->maybe_paginate_images( $images, 'encode_images', $timestamp ),
		);

		$filesystem->put_contents( $temp_file, wp_json_encode( (array) $data ) );

		wp_send_json_success( array( 'timestamp' => $timestamp ) );
	}


	/**
	 * Download Export Data.
	 *
	 * @since 1.0.0
	 */
	public function download_export() {
		if ( ! isset( $_GET['nonce'] ) && wp_verify_nonce( $_GET['nonce'], 'et_core_portability_nonce' ) ) {
			wp_die( esc_html__( 'The export process failed. Please refresh the page and try again.', ET_CORE_TEXTDOMAIN ) );
		}

		$this->prevent_failure();

		// Retrieve data.
		$timestamp = isset( $_GET['timestamp'] ) ? sanitize_text_field( $_GET['timestamp'] ) : null;
		$name = isset( $_GET['name'] ) ? sanitize_text_field( rawurldecode( $_GET['name'] ) ) : $this->instance->name;
		$filesystem = $this->set_filesystem();
		$temp_file = $this->temp_file( sanitize_file_name( $timestamp ), 'et_core_export' );

		header( 'Content-Description: File Transfer' );
		header( "Content-Disposition: attachment; filename=\"{$name}.json\"" );
		header( 'Content-Type: application/json' );
		header( 'Pragma: no-cache' );

		if ( file_exists( $temp_file ) ) {
			echo $filesystem->get_contents( $temp_file );
		}

		$this->delete_temp_files( 'et_core_export' );

		exit;
	}

	/**
	 * Get selected posts data.
	 *
	 * @since 1.0.0
	 */
	private function export_posts_query() {
		$args = array(
			'post_type'      => $this->instance->target,
			'posts_per_page' => -1,
			'no_found_rows'  => true,
		);

		// Only include selected posts if set and not empty.
		if ( isset( $_POST['selection'] ) ) {
			$include = json_decode( stripslashes( $_POST['selection'] ), true );

			if ( ! empty( $include ) ) {
				$include = array_map( 'intval', array_values( $include ) );
				$args['post__in'] = $include;
			}
		}

		$get_posts = get_posts( apply_filters( "et_core_portability_export_wp_query_{$this->instance->context}", $args ) );
		$taxonomies = get_object_taxonomies( $this->instance->target );
		$posts = array();

		foreach ( $get_posts as $key => $post ) {
			unset(
				$post->post_author,
				$post->guid
			);

			$posts[$post->ID] = $post;

			// Include post meta.
			$post_meta = (array) get_post_meta( $post->ID );

			if ( isset( $post_meta['_edit_lock'] ) ) {
				unset(
					$post_meta['_edit_lock'],
					$post_meta['_edit_last']
				);
			}

			$posts[$post->ID]->post_meta = $post_meta;

			// Include terms.
			$get_terms = (array) wp_get_object_terms( $post->ID, $taxonomies );
			$terms = array();

			// Order terms to make sure children are after the parents.
			while ( $term = array_shift( $get_terms ) ) {
				if ( 0 == $term->parent || isset( $terms[$term->parent] ) ) {
					$terms[$term->term_id] = $term;
				} else {
					// if parent category is also exporting then add the term to the end of the list and process it later
					// otherwise add a term as usual
					if ( $this->is_parent_term_included( $get_terms, $term->parent ) ) {
						$get_terms[] = $term;
					} else {
						$terms[$term->term_id] = $term;
					}
				}
			}

			$posts[$post->ID]->terms = array();

			foreach ( $terms as $term ) {
				$parents_data = array();

				if ( $term->parent ) {
					$parent_slug = isset( $terms[$term->parent] ) ? $terms[$term->parent]->slug : $this->get_parent_slug( $term->parent, $term->taxonomy );
					$parents_data = $this->get_all_parents( $term->parent, $term->taxonomy );
				} else {
					$parent_slug = 0;
				}

				$posts[$post->ID]->terms[$term->term_id] = array(
					'name'        => $term->name,
					'slug'        => $term->slug,
					'taxonomy'    => $term->taxonomy,
					'parent'      => $parent_slug,
					'all_parents' => $parents_data,
					'description' => $term->description
				);
			}
		}

		return $posts;
	}

	/**
	 * Check whether the $parent_id included into the $terms_list.
	 *
	 * @since 1.0.0
	 *
	 * @param array $terms_list Array of term objects.
	 * @param int   $parent_id  .
	 *
	 * @return bool
	 */
	private function is_parent_term_included( $terms_list, $parent_id ) {
		$is_parent_found = false;

		foreach ( $terms_list as $term => $term_details ) {
			if ( $parent_id === $term_details->term_id ) {
				$is_parent_found = true;
			}
		}

		return $is_parent_found;
	}

	/**
	 * Retrieve the term slug.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $parent_id .
	 * @param string $taxonomy  .
	 *
	 * @return int|string
	 */
	private function get_parent_slug( $parent_id, $taxonomy ) {
		$term_data = get_term( $parent_id, $taxonomy );
		$slug = '' === $term_data->slug ? 0 : $term_data->slug;

		return $slug;
	}

	/**
	 * Prepare array of all parents so the correct hierarchy can be restored during the import.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $parent_id .
	 * @param string $taxonomy  .
	 *
	 * @return array
	 */
	private function get_all_parents( $parent_id, $taxonomy ) {
		$parents_data_array = array();
		$parent = $parent_id;

		// retrieve data for all parent categories
		if ( 0 !== $parent  ) {
			while( 0 !== $parent ) {
				$parent_term_data = get_term( $parent, $taxonomy );
				$parents_data_array[$parent_term_data->slug] = array(
					'name' => $parent_term_data->name,
					'description' => $parent_term_data->description,
					'parent' => 0 !== $parent_term_data->parent ? $this->get_parent_slug( $parent_term_data->parent, $taxonomy ) : 0,
				);

				$parent = $parent_term_data->parent;
			}
		}
		//reverse order of items, to simplify the restoring process
		return array_reverse( $parents_data_array );
	}

	/**
	 * Import post.
	 *
	 * @since 1.0.0
	 *
	 * @param array $posts Array of data formated by the portability exporter.
	 *
	 * @return bool
	 */
	private function import_posts( $posts ) {
		global $wpdb;

		if ( empty( $posts ) ) {
			return;
		}

		foreach ( $posts as $post ) {
			if ( isset( $post['post_status'] ) && 'auto-draft' === $post['post_status'] ) {
				continue;
			}

			$fields_validatation = array(
				'ID'         => 'intval',
				'post_title' => 'sanitize_text_field',
				'post_type'  => 'sanitize_text_field',
			);

			if ( ! $post = $this->validate( $post, $fields_validatation ) ) {
				continue;
			}

			$post_exists = post_exists( $post['post_title'] );

			// Make sure the post is published and stop here if the post exists.
			if ( $post_exists && get_post_type( $post_exists ) == $post['post_type'] ) {
				if ( 'publish' !== get_post_status( $post_exists ) ) {
					wp_update_post( array(
						'ID'          => intval( $post_exists ),
						'post_status' => 'publish',
					) );
				}

				continue;
			}

			$post['import_id'] = $post['ID'];
			unset( $post['ID'] );

			$post['post_author'] = (int) get_current_user_id();

			// Insert or update post.
			$post_id = wp_insert_post( $post, true );

			if ( ! $post_id || is_wp_error( $post_id ) ) {
				continue;
			}

			// Insert and set terms.
			if ( isset( $post['terms'] ) && is_array( $post['terms'] ) ) {
				$processed_terms = array();

				foreach ( $post['terms'] as $term ) {
					$fields_validatation = array(
						'name'        => 'sanitize_text_field',
						'slug'        => 'sanitize_title',
						'taxonomy'    => 'sanitize_title',
						'parent'      => 'sanitize_title',
						'description' => 'wp_kses_post',
					);

					if ( ! $term = $this->validate( $term, $fields_validatation ) ) {
						continue;
					}

					if ( empty( $term['parent'] ) ) {
						$parent = 0;
					} else {
						if ( isset( $term['all_parents'] ) && ! empty( $term['all_parents'] ) ) {
							$this->restore_parent_categories( $term['all_parents'], $term['taxonomy'] );
						}

						$parent = term_exists( $term['parent'], $term['taxonomy'] );

						if ( is_array( $parent ) ){
							$parent = $parent['term_id'];
						}
					}

					if ( ! $insert = term_exists( $term['slug'], $term['taxonomy'] ) ) {
						$insert = wp_insert_term( $term['name'], $term['taxonomy'], array(
							'slug'        => $term['slug'],
							'description' => $term['description'],
							'parent'      => intval( $parent ),
						) );
					}

					if ( is_array( $insert ) && ! is_wp_error( $insert ) ) {
						$processed_terms[$term['taxonomy']][] = $term['slug'];
					}
				}

				// Set post terms.
				foreach ( $processed_terms as $taxonomy => $ids ) {
					wp_set_object_terms( $post_id, $ids, $taxonomy );
				}
			}

			// Insert or update post meta.
			if ( isset( $post['post_meta'] ) && is_array( $post['post_meta'] ) ) {
				foreach ( $post['post_meta'] as $meta_key => $meta ) {

					$meta_key = sanitize_text_field( $meta_key );

					if ( count( $meta ) < 2 ) {
						$meta = wp_kses_post( $meta[0] );
					} else {
						$meta = array_map( 'wp_kses_post', $meta );
					}

					update_post_meta( $post_id, $meta_key, $meta );
				}
			}
		}

		return true;
	}

	/**
	 * Restore the categories hierarchy in library.
	 *
	 * @since 1.0.0
	 *
	 * @param array $parents_array    Array of parent categories data.
	 * @param string $taxonomy
	 */
	private function restore_parent_categories( $parents_array, $taxonomy ) {
		foreach( $parents_array as $slug => $category_data ) {
			$current_category = term_exists( $slug, $taxonomy );

			if ( ! is_array( $current_category ) ) {
				$parent_id = 0 !== $category_data['parent'] ? term_exists( $category_data['parent'], $taxonomy ) : 0;
				wp_insert_term( $category_data['name'], $taxonomy, array(
					'slug'        => $slug,
					'description' => $category_data['description'],
					'parent'      => is_array( $parent_id ) ? $parent_id['term_id'] : $parent_id,
				) );
			} else if ( ( ! isset( $current_category['parent'] ) || 0 === $current_category['parent'] ) && 0 !== $category_data['parent'] ) {
				$parent_id = 0 !== $category_data['parent'] ? term_exists( $category_data['parent'], $taxonomy ) : 0;
				wp_update_term( $current_category['term_id'], $taxonomy, array( 'parent' => is_array( $parent_id ) ? $parent_id['term_id'] : $parent_id ) );
			}
		}
	}

	/**
	 * Restrict data according the argument registered.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $data   Array of data the query is applied on.
	 * @param string $method Whether data should be set or reset. Accepts 'set' or 'unset' which is
	 *                       should be used when treating existing data in the db.
	 *
	 * @return array
	 */
	private function apply_query( $data, $method ) {
		$operator = ( $method === 'set' ) ? true : false;

		foreach ( $data as $id => $value ) {
			if ( ! empty( $this->instance->exclude ) && isset( $this->instance->exclude[$id] ) === $operator ) {
				unset( $data[$id] );
			}

			if ( ! empty( $this->instance->include ) && isset( $this->instance->include[$id] ) === ! $operator ) {
				unset( $data[$id] );
			}
		}

		return $data;
	}

	/**
	 * Paginate images processing.
	 *
	 * @since    1.0.0
	 *
	 * @param        $images
	 * @param string $method    Method applied on images.
	 * @param int    $timestamp Timestamp used to store data upon pagination.
	 *
	 * @return array
	 * @internal param array $data Array of images.
	 */
	private function maybe_paginate_images( $images, $method, $timestamp ) {
		if ( count( $images ) > 5 ) {
			$total_pages = ceil( count( $images ) / 5 );
			$page = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
			$slice = 5 * ( $page - 1 );
			$images = array_slice( $images, $slice, 5 );
			$images = $this->$method( $images );
			$filesystem = $this->set_filesystem();
			$temp_file_id = sanitize_file_name( "images_{$timestamp}" );
			$temp_file = $this->temp_file( $temp_file_id, 'et_core_export' );
			$temp_images = json_decode( $filesystem->get_contents( $temp_file ), true );

			if ( is_array( $temp_images ) ){
				$images = array_merge( $temp_images, $images );
			}

			if ( $page < $total_pages ) {
				$filesystem->put_contents( $temp_file, wp_json_encode( (array) $images ) );

				wp_send_json( array(
					'page' => $page,
					'total_pages' => $total_pages,
					'timestamp' => $timestamp
				) );
			}

			$this->delete_temp_files( 'et_core_export', array( $temp_file_id => $temp_file ) );
		} else {
			$images = $this->$method( $images );
		}

		return $images;
	}

	/**
	 * Get all images in the data given.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data  Array of data.
	 * @param bool  $force Set whether the value should be added by force. Usually used for image ids.
	 *
	 * @return array
	 */
	private function get_data_images( $data, $force = false ) {
		$images = array();

		foreach ( $data as $value ) {
			if ( is_array( $value ) || is_object( $value ) ) {
				$images = array_merge( $images, $this->get_data_images( (array) $value ) );
				continue;
			}

			// Extract images from html or shortcodes.
			if ( preg_match_all( '/(src|image_url|image|url)="(?P<src>\w+[^"]*)"/i', $value, $matches ) ) {
				foreach ( array_unique( $matches['src'] ) as $key => $src ) {
					$images = array_merge( $images, $this->get_data_images( array( $key => $src ) ) );
				}
				continue;
			}

			// Extract images from shortcodes gallery.
			if ( preg_match_all( '/gallery_ids="(?P<ids>\w+[^"]*)"/i', $value, $matches ) ) {
				$explode = explode( ',', str_replace( ' ', '', $matches['ids'][0] ) );

				foreach ( $explode as $image_id ) {
					$images = array_merge( $images, $this->get_data_images( array( (int) $image_id ), true ) );
				}
				continue;
			}

			if ( preg_match( '/^.+?\.(jpg|jpeg|jpe|png|gif)/', $value, $match ) || $force ) {
				$basename = basename( $value );

				// Avoid duplicates.
				if ( isset( $images[$value] ) ) {
					continue;
				}

				$images[$value] = $value;
			}
		}

		return $images;
	}

	/**
	 * Encode image in a base64 format.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Array of data for which images need to be encoded if any.
	 *
	 * @return array
	 */
	private function encode_images( $images ) {
		$encoded = array();

		foreach ( $images as $url ) {

			if ( is_int( $url ) ) {
				$id = $url;
				$url = wp_get_attachment_url( $url );
			}

			$request = wp_remote_get( esc_url_raw( $url ), array(
				'timeout' => 2,
				'redirection' => 2,
			) );

			if ( is_array( $request ) && ! is_wp_error( $request ) ) {
				if ( stripos( $request['headers']['content-type'], 'image' ) !== false && ( $image = wp_remote_retrieve_body( $request ) ) ) {
					$encoded[$url] = array(
						'encoded'  => base64_encode( $image ),
						'url'      => $url,
					);

					// Add image id for replacement purposes
					if ( isset( $id ) ) {
						$encoded[$url]['id'] = $id;
					}
				}
			}
		}

		return $encoded;
	}

	/**
	 * Decode base64 formated image and upload it to WP media.
	 *
	 * @since 1.0.0
	 *
	 * @param array $images Array of encoded images which needs to be uploaded.
	 *
	 * @return array
	 */
	private function upload_images( $images ) {
		$filesystem = $this->set_filesystem();

		foreach ( $images as $key => $image ) {
			$basename = sanitize_file_name( wp_basename( $image['url'] ) );
			$attachment = get_posts( array(
				'post_per_page' => 1,
				'post_type'     => 'attachment',
				'pagename'      => pathinfo( $basename, PATHINFO_FILENAME ),
			) );

			// Avoid duplicates.
			if ( ! is_wp_error( $attachment ) && ! empty( $attachment ) ) {
				$attachment_url = wp_get_attachment_url( $attachment[0]->ID );
				$file = get_attached_file( $attachment[0]->ID );
				$filename = sanitize_file_name( wp_basename( $file ) );

				// Allow new image if the basenames don't match.
				if ( $filename === $basename ) {
					// Use existing image only if the basenames and content match.
					if ( $filesystem->get_contents( $file ) === base64_decode( $image['encoded'] ) ) {
						$url = isset( $image['id'] ) ? $attachment[0]->ID : $attachment_url;
					}
				}
			}

			// Create new image.
			if ( ! isset( $url ) ) {
				$temp_file = wp_tempnam();
				$filesystem->put_contents( $temp_file, base64_decode( $image['encoded'] ) );
				$filetype = wp_check_filetype_and_ext( $temp_file, $basename );

				// Avoid further duplicates if the proper_file name match an existing image.
				if ( isset( $filetype['proper_filename'] ) && $filetype['proper_filename'] !== $basename ) {
					if ( isset( $filename ) && $filename === $filetype['proper_filename'] ) {
						// Use existing image only if the basenames and content match.
						if ( $filesystem->get_contents( $file ) === $filesystem->get_contents( $temp_file ) ) {
							$filesystem->delete( $temp_file );
							continue;
						}
					}
				}

				$file = array(
					'name'     => $basename,
					'tmp_name' => $temp_file,
				);
				$upload = media_handle_sideload( $file, 0 );

				if ( ! is_wp_error( $upload ) ) {
					// Set the replacement as an id if the original image was set as an id (for gallery).
					$url = isset( $image['id'] ) ? $upload : wp_get_attachment_url( $upload );
				} else {
					// Make sure the temporary file is removed if media_handle_sideload didn't take care of it.
					$filesystem->delete( $temp_file );
				}
			}

			// Only declare the replace if a url is set.
			if ( isset( $url ) ) {
				$images[$key]['replacement_url'] = $url;
			}

			unset( $url );
		}

		return $images;
	}

	/**
	 * Replace image urls with newly uploaded images.
	 *
	 * @since 1.0.0
	 *
	 * @param array $images Array of new images uploaded.
	 * @param array $data   Array of for which images url needs to be replaced.
	 *
	 * @return array|mixed|object
	 */
	private function replace_images_urls( $images, $data ) {
		$data = wp_json_encode( $data );

		foreach ( $images as $image ) {
			if ( isset( $image['replacement_url'] ) ) {
				if ( isset( $image['id'] ) && is_int( $image['replacement_url'] ) ) {
					$search = $image['id'];
					$replacement = $image['replacement_url'];
					$data = preg_replace( "/(gallery_ids=.*){$search}(.*\")/", "\${1}{$replacement}\${2}", $data );
				} else {
					$url = str_replace( '/', '\/', $image['url'] );
					$replacement = str_replace( '/', '\/', $image['replacement_url'] );
					$data = str_replace( $url, $replacement, $data );
				}
			}
		}

		return json_decode( $data, true );
	}

	/**
	 * Validate data and remove any malicious code.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data              Array of data which needs to be validated.
	 * @param array $fields_validation Array of field and validation callback.
	 *
	 * @return array|bool
	 */
	private function validate( $data, $fields_validation = array() ) {
		if ( ! is_array( $data ) ) {
			return false;
		}

		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) ) {
				$data[$key] = $this->validate( $value, $fields_validation );
			} else {
				if ( isset( $fields_validation[$key] ) ) {
					$data[$key] = call_user_func( $fields_validation[$key], $value );
				} else {
					if ( current_user_can( 'switch_themes' ) ) {
						$data[ $key ] = $value;
					} else {
						$data[ $key ] = wp_kses_post( $value );
					}
				}
			}
		}

		return $data;
	}

	/**
	 * Prevent import and export timout or memory failure.
	 *
	 * @since 1.0.0
	 *
	 * It doesn't need to be reset as in both case the request exit.
	 */
	private function prevent_failure() {
		@set_time_limit( 0 );

		// Increase memory which is safe at this stage of the request.
		if ( (int) @ini_get( 'memory_limit' ) < 256 ) {
			@ini_set( 'memory_limit', '256M' );
		}
	}

	/**
	 * Set WP filesystem to direct. This should only be use to create a temporary file.
	 *
	 * @since 1.0.0
	 *
	 * It is safe to do so since the created file is removed immediately after import. The method does'nt have
	 * to be reset since the ajax query is exited.
	 */
	private function set_filesystem() {
		global $wp_filesystem;

		add_filter( 'filesystem_method', array( $this, 'replace_filesystem_method' ) );
		WP_Filesystem();

		return $wp_filesystem;
	}

	/**
	 * Check if a temporary file is register. Returns temporary file if it exists.
	 *
	 * @param string $id    Unique id used when the temporary file was created.
	 * @param string $group Group name in which files are grouped.
	 *
	 * @return bool
	 */
	private function has_temp_file( $id, $group ) {
		$temp_files = get_option( '_et_core_portability_temp_files', array() );

		if ( isset( $temp_files[$group][$id] ) && file_exists( $temp_files[$group][$id] ) ) {
			return $temp_files[$group][$id];
		}

		return false;
	}

	/**
	 * Create a temp file and register it.
	 *
	 * @since 1.0.0
	 *
	 * @param string      $id        Unique id reference for the temporary file.
	 * @param string      $group     Group name in which files are grouped.
	 * @param string|bool $temp_file Path to the temporary file. False create a new temporary file.
	 *
	 * @return bool|string
	 */
	private function temp_file( $id, $group, $temp_file = false ) {
		$temp_files = get_option( '_et_core_portability_temp_files', array() );

		if ( ! isset( $temp_files[$group] ) ) {
			$temp_files[$group] = array();
		}

		if ( isset( $temp_files[$group][$id] ) && file_exists( $temp_files[$group][$id] ) ) {
			return $temp_files[$group][$id];
		}

		$temp_file = $temp_file ? $temp_file : wp_tempnam();
		$temp_files[$group][$id] = $temp_file;

		update_option( '_et_core_portability_temp_files', $temp_files, false );

		return $temp_file;
	}

	/**
	 * Delete all the temp files.
	 *
	 * @since 1.0.0
	 *
	 * @param bool|string $group         Group name in which files are grouped. Set to true to remove all groups and files.
	 * @param array       $defined_files Array or temoporary files to delete. No argument deletes all temp files.
	 */
	public function delete_temp_files( $group = false, $defined_files = false ) {
		$filesystem = $this->set_filesystem();
		$temp_files = get_option( '_et_core_portability_temp_files', array() );

		// Remove all temp files accross all groups if group is true.
		if ( $group === true ) {
			foreach( $temp_files as $group_id => $_group ) {
				$this->delete_temp_files( $group_id );
			}
		}

		if ( ! isset( $temp_files[$group] ) ) {
			return;
		}

		$delete_files = ( is_array( $defined_files ) && ! empty( $defined_files ) ) ? $defined_files : $temp_files[$group];

		foreach ( $delete_files as $id => $temp_file ) {
			if ( isset( $temp_files[$group][$id] ) && $filesystem->delete( $temp_files[$group][$id] ) ) {
				unset( $temp_files[$group][$id] );
			}
		}

		if ( empty( $temp_files[$group] ) ) {
			unset( $temp_files[$group] );
		}

		if ( empty( $temp_files ) ) {
			delete_option( '_et_core_portability_temp_files' );
		} else {
			update_option( '_et_core_portability_temp_files', $temp_files, false );
		}
	}

	/**
	 * Set WP filesystem method to direct.
	 *
	 * @since 1.0.0
	 */
	public function replace_filesystem_method() {
		return 'direct';
	}

	/**
	 * Get timestamp or create one if it isn't set.
	 *
	 * @since 1.0.0
	 */
	public function get_timestamp() {
		return isset( $_POST['timestamp'] ) && ! empty( $_POST['timestamp'] ) ? sanitize_text_field( $_POST['timestamp'] ) : current_time( 'timestamp' );
	}

	/**
	 * Enqueue assets.
	 *
	 * @since 1.0.0
	 */
	public function assets() {
		$time = '<span>1</span>';

		wp_enqueue_style( 'et-core-portability', ET_CORE_URL . 'admin/css/portability.css', array(
			'et-core-admin',
		), ET_CORE_VERSION );
		wp_enqueue_script( 'et-core-portability', ET_CORE_URL . 'admin/js/portability.js', array(
			'jquery',
			'jquery-ui-tabs',
			'jquery-form',
			'et-core-admin',
		), ET_CORE_VERSION );
		wp_localize_script( 'et-core-portability', 'etCorePortability', array(
			'nonce'         => wp_create_nonce( 'et_core_portability_nonce' ),
			'postMaxSize'   => (int) @ini_get( 'post_max_size' ),
			'uploadMaxSize' => (int) @ini_get( 'upload_max_filesize' ),
			'text'          => array(
				'browserSupport'      => esc_html__( 'The browser version you are currently using is outdated. Please update to the newest version.', ET_CORE_TEXTDOMAIN ),
				'memoryExhausted'     => esc_html__( 'You reached your server memory limit. Please try increasing your PHP memory limit.', ET_CORE_TEXTDOMAIN ),
				'maxSizeExceeded'     => esc_html__( 'This file cannot be imported. It may be caused by file_uploads being disabled in your php.ini. It may also be caused by post_max_size or/and upload_max_filesize being smaller than file selected. Please increase it or transfer more substantial data at the time.', ET_CORE_TEXTDOMAIN ),
				'invalideFile'        => esc_html__( 'Invalid File format. You should be uploading a JSON file.', ET_CORE_TEXTDOMAIN ),
				'importContextFail'   => esc_html__( 'This file should not be imported in this context.', ET_CORE_TEXTDOMAIN ),
				'importing'           => sprintf( esc_html__( 'Import estimated time remaining: %smin', ET_CORE_TEXTDOMAIN ), $time ),
				'exporting'           => sprintf( esc_html__( 'Export estimated time remaining: %smin', ET_CORE_TEXTDOMAIN ), $time ),
				'backuping'           => sprintf( esc_html__( 'Backup estimated time remaining: %smin', ET_CORE_TEXTDOMAIN ), $time ),
			),
		) );
	}

	/**
	 * Modal HTML.
	 *
	 * @since 1.0.0
	 */
	public function modal() {
		$export_url = add_query_arg( array(
			'et_core_portability' => true,
			'context'             => $this->instance->context,
			'name'                => $this->instance->name,
			'nonce'               => wp_create_nonce( 'et_core_portability_nonce' ),
		), admin_url() );

		?>
		<div class="et-core-modal-overlay et-core-form" data-et-core-portability="<?php echo $this->instance->context; ?>">
			<div class="et-core-modal">
				<div class="et-core-modal-header">
					<h3 class="et-core-modal-title"><?php esc_html_e( 'Portability', ET_CORE_TEXTDOMAIN ); ?></h3><a href="#" class="et-core-modal-close" data-et-core-modal="close"></a>
				</div>
				<div data-et-core-tabs class="et-core-modal-tabs-enabled">
					<ul class="et-core-tabs">
						<li><a href="#et-core-portability-export"><?php esc_html_e( 'Export', ET_CORE_TEXTDOMAIN ); ?></a></li>
						<li><a href="#et-core-portability-import"><?php esc_html_e( 'Import', ET_CORE_TEXTDOMAIN ); ?></a></li>
					</ul>
					<div id="et-core-portability-export">
						<div class="et-core-modal-content">
							<?php printf( esc_html__( 'Exporting your %s will create a JSON file that can be imported into a different website.', ET_CORE_TEXTDOMAIN ), $this->instance->name ); ?>
							<h3><?php esc_html_e( 'Export File Name', ET_CORE_TEXTDOMAIN ); ?></h3>
							<form class="et-core-portability-export-form">
								<input type="text" name="" value="<?php echo esc_attr( $this->instance->name ); ?>">
								<?php if ( 'post_type' === $this->instance->type ) : ?>
									<div class="et-core-clearfix"></div>
									<label><input type="checkbox" name="et-core-portability-posts"/><?php esc_html_e( 'Only export selected items', ET_CORE_TEXTDOMAIN ); ?></label>
								<?php endif; ?>
							</form>
						</div>
						<a class="et-core-modal-action" href="#" data-et-core-portability-export="<?php echo esc_url( $export_url ); ?>"><?php printf( esc_html__( 'Export %s', ET_CORE_TEXTDOMAIN ), $this->instance->name ); ?></a>
						<a class="et-core-modal-action et-core-button-danger" href="#" data-et-core-portability-cancel><?php esc_html_e( 'Cancel Export', ET_CORE_TEXTDOMAIN ); ?></a>
					</div>
					<div id="et-core-portability-import">
						<div class="et-core-modal-content">
							<?php if ( 'post' === $this->instance->type ) : ?>
								<?php printf( esc_html__( 'Importing a previously-exported %s file will overwrite all content currently on this page.', ET_CORE_TEXTDOMAIN ), $this->instance->name ); ?>
							<?php elseif ( 'post_type' === $this->instance->type ) : ?>
								<?php printf( esc_html__( 'Select a previously-exported Divi Builder Layouts file to begin importing items. Large collections of image-heavy exports may take several minutes to upload.', ET_CORE_TEXTDOMAIN ), $this->instance->name ); ?>
							<?php else : ?>
								<?php printf( esc_html__( 'Importing a previously-exported %s file will overwrite all current data. Please proceed with caution!', ET_CORE_TEXTDOMAIN ), $this->instance->name ); ?>
							<?php endif; ?>
							<h3><?php esc_html_e( 'Select File To Import', ET_CORE_TEXTDOMAIN ); ?></h3>
							<form class="et-core-portability-import-form">
								<span class="et-core-portability-import-placeholder"><?php esc_html_e( 'No File Selected', ET_CORE_TEXTDOMAIN ); ?></span>
								<button class="et-core-button"><?php esc_html_e( 'Choose File', ET_CORE_TEXTDOMAIN ); ?></button>
								<input type="file">
								<?php if ( 'post_type' !== $this->instance->type ) : ?>
									<div class="et-core-clearfix"></div>
									<label><input type="checkbox" name="et-core-portability-import-backup" /><?php esc_html_e( 'Download backup before importing', ET_CORE_TEXTDOMAIN ); ?></label>
								<?php endif; ?>
							</form>
						</div>
						<a class="et-core-modal-action et-core-portability-import" href="#"><?php printf( esc_html__( 'Import %s', ET_CORE_TEXTDOMAIN ), $this->instance->name ); ?></a>
						<a class="et-core-modal-action et-core-button-danger" href="#" data-et-core-portability-cancel><?php esc_html_e( 'Cancel Import', ET_CORE_TEXTDOMAIN ); ?></a>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}


if ( ! function_exists( 'et_core_portability_register' ) ) :
/**
 * Register portability.
 *
 * This function should be called in an 'admin_init' action callback.
 *
 * @since 1.0.0
 *
 * @param string $context A unique ID used to register the portability arguments.
 *
 * @param array  $args {
 *      Array of arguments used to register the portability.
 *
 * 		@type string $name	  The name used in the various text string.
 * 		@type bool   $view	  Whether the assets and content should load or not.
 * 		      				  Example: `isset( $_GET['page'] ) && $_GET['page'] == 'example'`.
 * 		@type string $db	  The option_name from the wp_option table used to export and import data.
 * 		@type array  $include Optional. Array of all the options scritcly included. Options ids must be set
 *         					  as the array keys.
 *      @type array  $exclude Optional. Array of excluded options. Options ids must be set as the array keys.
 * }
 */
function et_core_portability_register( $context, $args ) {
	$defaults = array(
		'context' => $context,
		'name'    => false,
		'view'    => false,
		'type'    => false,
		'target'  => false,
		'include' => array(),
		'exclude' => array(),
	);

	$data = apply_filters( "et_core_portability_args_{$context}", (object) array_merge( $defaults, (array) $args ) );

	et_core_cache_set( $context, $data, 'et_core_portability' );

	// Stop here if not allowed.
	if ( function_exists( 'et_pb_is_allowed' ) && ! et_pb_is_allowed( array( 'portability', "{$data->context}_portability" ) ) ) {

		// Set view to false if not allowed.
		$data->view = false;
		et_core_cache_set( $context, $data, 'et_core_portability' );

		return;
	}

	if ( $data->view ) {
		et_core_portability_load( $context );
	}
}
endif;

if ( ! function_exists( 'et_core_portability_load' ) ) :
/**
 * Load Portability class.
 *
 * @since 1.0.0
 *
 * @param string $context A unique ID used to register the portability arguments.
 * @return ET_Core_Portability
 */
function et_core_portability_load( $context ) {
	return new ET_Core_Portability( $context );
}
endif;

if ( ! function_exists( 'et_core_portability_link' ) ) :
/**
 * HTML link to trigger the portability modal.
 *
 * @since 1.0.0
 *
 * @param string       $context    The context used to register the portability.
 * @param string|array $attributes Optional. Query string or array of attributes. Default empty.
 *
 * @return string
 */
function et_core_portability_link( $context, $attributes = array() ) {
	$instance = et_core_cache_get( $context, 'et_core_portability' );

	if ( ! current_user_can( 'switch_themes' ) || ! ( isset( $instance->view ) && $instance->view ) ) {
		return;
	}

	$defaults = array(
		'title' => esc_attr__( 'Import & Export', ET_CORE_TEXTDOMAIN ),
	);
	$attributes = array_merge( $defaults, $attributes );

	// Forced attributes.
	$attributes['href'] = '#';
	$attributes['data-et-core-modal'] = "[data-et-core-portability='{$context}']";

	$string = '';

	foreach ( $attributes as $attribute => $value ) {
		if ( null !== $value ){
			$string .= esc_attr( $attribute ) . '="' . esc_attr( $value ) . '" ';
		}
	}

	return sprintf(
		'<a %1$s><span>%2$s</span></a>',
		trim( $string ),
		esc_html( $attributes['title'] )
	);
}
endif;

if ( ! function_exists( 'et_core_portability_ajax_import' ) ) :
/**
 * Ajax portability Import.
 *
 * @since 1.0.0
 *
 * @private
 */
function et_core_portability_ajax_import() {
	if ( ! isset( $_POST['context'] ) ) {
		wp_send_json_error();
	}

	if ( $portability = et_core_portability_load( sanitize_text_field( $_POST['context'] ) ) ) {
		$portability->import();
	}
}
endif;
add_action( 'wp_ajax_et_core_portability_import', 'et_core_portability_ajax_import' );

if ( ! function_exists( 'et_core_portability_ajax_export' ) ) :
/**
 * Ajax portability Export.
 *
 * @since 1.0.0
 *
 * @private
 */
function et_core_portability_ajax_export() {
	if ( ! isset( $_POST['context'] ) ) {
		wp_send_json_error();
	}

	if ( $portability = et_core_portability_load( sanitize_text_field( $_POST['context'] ) ) ) {
		$portability->export();
	}
}
endif;
add_action( 'wp_ajax_et_core_portability_export', 'et_core_portability_ajax_export' );

if ( ! function_exists( 'et_core_portability_ajax_cancel' ) ) :
/**
 * Cancel portability action.
 *
 * @since 1.0.0
 *
 * @private
 */
function et_core_portability_ajax_cancel() {
	if ( ! isset( $_POST['context'] ) || ( ! isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'et_core_portability_nonce' ) ) ) {
		wp_send_json_error();
	}

	if ( $portability = et_core_portability_load( sanitize_text_field( $_POST['context'] ) ) ) {
		$portability->delete_temp_files( true );
	}
}
endif;
add_action( 'wp_ajax_et_core_portability_cancel', 'et_core_portability_ajax_cancel' );

if ( ! function_exists( 'et_core_portability_export' ) ) :
/**
 * Portability export.
 *
 * @since 1.0.0
 *
 * @private
 */
function et_core_portability_export() {
	if ( ! ( isset( $_GET['et_core_portability'] ) && isset( $_GET['timestamp'] ) ) ) {
		return;
	}

	if ( $portability = et_core_portability_load( sanitize_text_field( $_GET['timestamp'] ) ) ) {
		$portability->download_export();
	}
}
endif;
add_action( 'admin_init', 'et_core_portability_export', 20 );
