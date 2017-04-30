<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Easy_Instagram_Cache {
	private $default_image_sizes = array( 'thumbnail', 'low_resolution', 'standard_resolution' );
	private $default_video_sizes = array( 'low_resolution', 'standard_resolution' );
	private $cache_directory_name = 'cache/';
	private $default_cache_expire_minutes = 30;
	private $default_cache_path = '';
	private $default_cache_url = '';

	public function __construct() {
		$this->default_cache_path = sprintf( '%s/%s', EASY_INSTAGRAM_PLUGIN_PATH, $this->cache_directory_name );
		$this->default_cache_url = plugins_url( $this->cache_directory_name, dirname( __FILE__ ) );
	}
	
	//================================================================
	// [todo] - check visibility of all methods
	public function get_cache_dir() {
		return get_option( 'easy_instagram_cache_dir_path' , $this->default_cache_path );
	}

	private function get_cache_url() {
		return get_option( 'easy_instagram_cache_dir_url', $this->default_cache_url );
	}

	public function get_cache_directory_name() {
		return $this->cache_directory_name;
	}

	public function get_default_cache_path() {
		return $this->default_cache_path;
	}

	public function get_default_cache_url() {
		return $this->default_cache_url;
	}

	//=========================================================================

	function get_refresh_minutes() {
		return get_option( 'easy_instagram_cache_expire_time', $this->default_cache_expire_minutes );
	}

	//================================================================

	function set_refresh_minutes( $minutes = 0 ) {
		if ( 0 == $minutes ) {
			$minutes = $this->default_cache_expire_minutes;
		}
		update_option( 'easy_instagram_cache_expire_time', (int) $minutes );
	}

	//=================================================================

	// Returns the cached data and a flag telling if the data expired
	function get_cached_data_for_user_or_tag( $id_or_tag, $limit, $type = 'tag' ) {
		$hash = md5( $type . $id_or_tag );
		$path = $this->get_cache_dir() . $hash . '.cache';

		$cached_data = $this->_get_cache_file_content( $path );
		if ( ( null === $cached_data ) || !isset( $cached_data['data'] ) 
			|| !isset( $cached_data['cache_timestamp'] ) || !isset( $cached_data['requested_count'] ) ) {
			return array( null, false ); // No cached data found
		}

		// If limit is greater than the cached data size, force clear cache
		$count = count( $cached_data['data'] );
		if ( $limit > $count && $count == $cached_data['requested_count'] ) {
			return array( $cached_data, true );
		}

		$cache_minutes = $this->get_refresh_minutes();

		$now = time();
		$delta = ( $now - $cached_data['cache_timestamp'] ) / 60;
		if ( $delta > $cache_minutes ) {
			return array( $cached_data, true );
		}
		else {
			return array( $cached_data, false );
		}
	}

	//================================================================

	private function _cache_data( $data, $id_or_tag, $type ) {
		$hash = md5( $type . $id_or_tag );

		$path = $this->get_cache_dir() . $hash . '.cache';

		if ( file_exists( $path ) ) {
			rename( $path, $path . '.old' );
		}

		$handle = fopen( $path, 'w' );
		if ( false === $handle ) {
			return false;
		}

		$serialized = serialize( $data );

		$would_block = true;
		if ( flock( $handle, LOCK_EX, $would_block ) ) {
			fwrite( $handle, $serialized );
			fflush( $handle );
			flock( $handle, LOCK_UN ); // release the lock
		}
		else {
			error_log( 'Couldn\'t get the lock in cache_data.' );
		}

		fclose( $handle );

		$this->_clear_old_cache( $id_or_tag, $type );

		return true;
	}

	//=========================================================================

	private function _clear_old_cache( $id_or_tag, $type ) {
		$hash = md5( $type . $id_or_tag );

		$old_cache_path = $this->get_cached_file_path( $hash . '.cache.old' );
		$new_cache_path = $this->get_cached_file_path( $hash . '.cache' );

		if ( !file_exists( $old_cache_path ) || !file_exists( $new_cache_path ) ) {
			return;
		}

		$old_cached_data = $this->_get_cache_file_content( $old_cache_path );

		if ( !is_array( $old_cached_data ) || !isset( $old_cached_data['data'] ) ) {
			unlink( $old_cache_path );
			return;
		}

		$new_cached_data = $this->_get_cache_file_content( $new_cache_path );
		
		// Get files that are in old cache and not in new cache and delete it
		$new_images = array();
		foreach ( $new_cached_data['data'] as $elem ) {
			foreach ( $this->default_image_sizes as $image_size ) {
				if ( isset( $elem[$image_size] ) && isset( $elem[$image_size]['url'] ) ) {
					$new_images[] = basename( $elem[$image_size]['url'] );
				}
			}
		}

		$to_delete = array();
		foreach ( $old_cached_data['data'] as $elem ) {
			// Delete old images not in new_files
			foreach ( $this->default_image_sizes as $image_size ) {
				if ( isset( $elem[$image_size] ) && isset( $elem[$image_size]['url'] ) ) {
					// Extract the file name from the file URL and look for the file in the cache directory
					$image_basename = basename( $elem[$image_size]['url'] );
					if ( !in_array( $image_basename, $new_images ) ) {
						//if ( !preg_match( '/[0-9]+x[0-9]+\.[^\.]+$/', $image_basename ) )
						$to_delete[] = $image_basename;
					}
				}
			}
		}

		unlink( $old_cache_path );

		if ( empty( $to_delete ) ) {
			return;
		}

		$cache_dir = $this->get_cache_dir();
		$files = scandir( $cache_dir );

		foreach ( $to_delete as $filename ) {
			$file_path = $this->get_cached_file_path( $filename );
			$path_parts = pathinfo( $file_path );

			if ( file_exists( $file_path ) ) {
				unlink( $file_path );
			}
			// Check for custom thumbnails
			if ( false !== stripos( $path_parts['filename'], 'standard_resolution' ) ) {
				foreach ( $files as $file ) {
					if ( preg_match( '/^'.$path_parts['filename'].'-[0-9]+x[0-9]+/', $file ) ) {
						unlink( $this->get_cached_file_path ( $file ) );
					}
				}
			}
		}
	}

	//================================================================

	private function _get_cache_file_content( $path ) {
		if ( !file_exists( $path ) ) {
			return null;
		}

		$handle = fopen( $path, 'r' );
		if ( false === $handle ) {
			return null;
		}

		$locking = true;
		if ( flock( $handle, LOCK_SH, $locking ) ) {
			$data = fread( $handle, filesize( $path ) );
			flock( $handle, LOCK_UN ); // release the lock
		}

		fclose( $handle );

		if ( empty( $data ) ) {
			return null;
		}

		$cached_data = unserialize( $data );
		return $cached_data;
	}

	//================================================================

	function clear_expired_cache_action() {
		$valid_files = array( '.gitignore' );
		$cache_dir = $this->get_cache_dir();

		$files = scandir( $cache_dir );

		if ( ! empty( $files ) ) {
			foreach ( $files as $file ) {
				if ( preg_match( '/\.cache$/', $file ) ) {
					$ret = $this->remove_cache_data_if_expired( $file );

					if ( ! empty( $ret ) ) {
						$valid_files = array_merge( $valid_files, $ret );
					}
				}
			}

			// Remove all the files from the cache folder not in the valid files array (or valid files is empty)
			foreach ( $files as $file ) {
				if ( ( '.' != $file ) && ( '..' != $file ) ) {
					if ( ! in_array( $file, $valid_files ) ) {
						$file_path = $this->get_cached_file_path( $file );
						if ( file_exists( $file_path ) ) {
							unlink( $file_path );
						}
					}
				}
			}
		}
	}

	//================================================================

	function remove_cache_data_if_expired( $filename ) {
		$path = $this->get_cached_file_path( $filename );

		$cached_data = $this->_get_cache_file_content( $path );

		$now = time();
		$delta = ( $now - $cached_data['cache_timestamp'] ) / 60;

		$valid_files = array();

		if ( is_null( $cached_data ) ) {
			return $valid_files;
		}

		if ( $delta > 24 * 60 )	{
			if ( !empty( $cached_data['data'] ) ) {
				$cache_dir = $this->get_cache_dir();
				$files = scandir( $cache_dir );

				foreach ( $cached_data['data'] as $elem ) {
					//Delete images
					foreach ( $this->default_image_sizes as $image_size ) {
						if ( isset( $elem[$image_size] ) && isset( $elem[$image_size]['url'] ) ) {
							// Extract the file name from the file URL and look for the file in the cache directory
							$file_path = $this->get_cached_file_path( basename( $elem[$image_size]['url'] ) );
							if ( file_exists( $file_path ) ) {
								unlink( $file_path );
							}

							//Remove custom thumbnails if any
							if ( 'standard_resolution' == $image_size ) {
								$path_parts = pathinfo( $file_path );
								foreach ( $files as $file ) {
									if ( preg_match( '/^'.$path_parts['filename'].'-[0-9]+x[0-9]+/', $file ) ) {
										unlink( $this->get_cached_file_path( $file ) );
									}
								}
							}
						}
					}
				}
				unlink( $path );
			}
		}
		else {
			if ( ! empty( $cached_data['data'] ) ) {
				$cache_dir = $this->get_cache_dir();
				$files = scandir( $cache_dir );

				foreach ( $cached_data['data'] as $elem ) {
					foreach ( $this->default_image_sizes as $image_size ) {
						if ( isset( $elem[$image_size]['url'] ) ) {
							$image_filename = basename( $elem[$image_size]['url'] );
							$file_path = $this->get_cached_file_path( $image_filename );
							if ( file_exists( $file_path ) ) {
								$valid_files[] = $image_filename;
							}

							//Add custom thumbnails as valid
							if ( 'standard_resolution' == $image_size ) {
								$path_parts = pathinfo( $file_path );
								foreach ( $files as $file ) {
									if ( preg_match( '/^'.$path_parts['filename'].'-[0-9]+x[0-9]+/', $file ) ) {
										$valid_files[] = $file;
									}
								}
							}
						}
					}
				}
			}
			$valid_files[] = $filename; //Keep the cache file as valid
		}

		return $valid_files;
	}

	//================================================================

	function save_remote_image( $remote_image_url, $id ) {
		$filename = '';
		if ( preg_match( '/([^\/\.\?\&]+)\.([^\.\?\/]+)(\?[^\.\/]*)?$/', $remote_image_url, $matches ) ) {
			$filename .= $matches[1] . '_' . $id . '.' . $matches[2];
		}
		else {
			return null;
		}

		$path = $this->get_cached_file_path( $filename );

		$filename_url = $this->get_cached_file_url( $filename );

		// If the file is already in cache, do not download it again.
		if ( file_exists( $path ) ) {
			return $filename_url;
		}

		$image_data = wp_remote_get( $remote_image_url );
		if ( is_wp_error( $image_data ) ) {
			return null;
		}
		$content = ( isset( $image_data['body'] ) ? $image_data['body'] : '' );

		if ( empty( $content ) ) {
			return null;
		}

		if ( false === file_put_contents( $path, $content ) ) {
			throw new Exception( __( 'Unable to write image to cache. Please check Easy Instagram cache directory permissions.', 'Easy_Instagram' ) );
		}

		return $filename_url;
	}

	//=========================================================================

	public function get_cached_file_path( $filename ) {
		return path_join( $this->get_cache_dir(), $filename );
	}

	//=========================================================================

	public function get_cached_file_url( $filename ) {
		return sprintf( '%s%s', trailingslashit( $this->get_cache_url() ), $filename );
	}

	//=========================================================================

	function cache_live_data( $live_data, $endpoint_id, $endpoint_type, $limit ) {
		$cache_data = array( 
			'cache_timestamp' => time(),
			'requested_count' => $limit,
			'data' => array()
		);
		
		$unique_rel = md5( $endpoint_type . $endpoint_id );
		$utils = new Easy_Instagram_Utils();
		
		foreach ( $live_data as $elem ) {
			list( $user_name, $caption_from, $caption_text, $caption_created_time)
				= $utils->get_usename_caption( $elem );

			$cached_elem = array(
				'link'					=> isset( $elem->link ) ? $elem->link : '#',
				'caption_text'			=> $caption_text,
				'caption_from'			=> $caption_from,
				'created_time'			=> $elem->created_time,
				'caption_created_time'	=> $caption_created_time,
				'user_name'				=> $user_name,
				'type'					=> $elem->type,
				'id'					=> $elem->id,
				'unique_rel'				=> $unique_rel
			);

			foreach ( $this->default_image_sizes as $image_size ) {
				if ( isset( $elem->images->$image_size ) ) {
					$image_data = $elem->images;
				}
				else if ( isset( $elem->$image_size) ) {
					$image_data = $elem;
				}
				else {
					break;
				}
				$cached_elem[$image_size] = array(
					'width'  => $image_data->$image_size->width,
					'height' => $image_data->$image_size->height
				);

				$local_url = $this->save_remote_image(
										$image_data->$image_size->url,
										$image_size );

				if ( is_null( $local_url ) ) {
					$cached_elem[$image_size]['url'] = $image_data->$image_size->url;
				}
				else {
					$cached_elem[$image_size]['url'] = $local_url;
				}
			}

			if ( 'video' === $elem->type ) {
				$videos = $elem->videos;
				foreach ( $this->default_video_sizes as $video_size ) {
					if ( isset( $videos->$video_size ) ) {
						$cached_elem['video_'.$video_size] = array(
							'width'  => $videos->$video_size->width,
							'height' => $videos->$video_size->height );
							
						$cached_elem['video_'.$video_size]['url'] = $videos->$video_size->url;
					}
				}
			}

			$cache_data['data'][] = $cached_elem;
		}

		$this->_cache_data( $cache_data, $endpoint_id, $endpoint_type );
		return $cache_data;
	}

	//================================================================

	private function get_cached_custom_thumbnail_basename( $large_image_url, $suffix ) {
		$thumb_basename = null;
		$path_parts = pathinfo( $large_image_url );

		if ( isset( $path_parts['filename'] ) ) {
			$thumb_basename = $path_parts['filename'] . '-' . $suffix . '.' . $path_parts['extension'];
		}
		else {
			//PHP < 5.2.0
			if ( preg_match( '/^([^\.]+)\.[^\.]+$/', $path_parts['basename'], $matches ) ) {
				$thumb_basename = $matches[1] . '-' . $suffix . '.' . $path_parts['extension'];
			}
		}
		return $thumb_basename;
	}

	//================================================================

	function get_cached_custom_thumbnail_path( $large_image_url, $suffix ) {
		$thumb_basename = $this->get_cached_custom_thumbnail_basename( $large_image_url, $suffix );
		if ( empty( $thumb_basename ) ) {
			return null;
		}

		$thumb_path = $this->get_cached_file_path( $thumb_basename );
		if ( file_exists( $thumb_path ) ) {
			return $thumb_path;
		}

		return null;
	}

	//================================================================

	function get_cached_custom_thumbnail_url( $large_image_url, $suffix ) {
		$thumb_basename = $this->get_cached_custom_thumbnail_basename( $large_image_url, $suffix );
		if ( empty( $thumb_basename ) ) {
			return null;
		}

		$thumb_path = $this->get_cached_file_path( $thumb_basename );
		
		if ( file_exists( $thumb_path ) ) {
			$thumb_url = $this->get_cached_file_url( $thumb_basename );
			return $thumb_url;
		}
		return null;
	}

	//================================================================

	function get_custom_thumbnail_url( $elem, $width, $height ) {
		$large_image_url = $elem['standard_resolution']['url'];
		$thumbnail_url = $elem['thumbnail']['url'];

		if ( ! function_exists( 'wp_get_image_editor' ) ) {
			return $thumbnail_url;
		}

		$suffix = sprintf( '%dx%d', $width, $height );
		//If thumbnail in cache, return it
		$custom_thumb_url = $this->get_cached_custom_thumbnail_url( $large_image_url, $suffix );
		if ( !empty( $custom_thumb_url ) ) {
			return $custom_thumb_url;
		}

		//If cache failed and this is not a local file, return the default thumbnail_url
		$cache_url = $this->get_cache_url();
		$pos = strpos( $large_image_url, $cache_url );
		if ( 0 !== $pos ) {
			return $thumbnail_url;
		}

		// If the large image is in cache, create a new thumbnail
		$large_image_filename = basename( $large_image_url );
		$large_image_path = $this->get_cached_file_path( $large_image_filename );

		$image = wp_get_image_editor( $large_image_path );
		if ( is_wp_error( $image ) ) {
			error_log( sprintf( 'Easy Instagram: Unable to resize [%s] !', $large_image_path ) );
			return $thumbnail_url;
		}
		$image->resize( $width, $height, false );
		$new_filename = $image->generate_filename( $suffix );
		$ret = $image->save( $new_filename );
		if ( is_wp_error( $ret ) ) {
			return $thumbnail_url;
		}

		return $this->get_cached_file_url( basename( $new_filename ) );
	}
}
