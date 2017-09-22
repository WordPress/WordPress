<?php
/**
 * Press This class and display functionality
 *
 * @package WordPress
 * @subpackage Press_This
 * @since 4.2.0
 */

/**
 * Press This class.
 *
 * @since 4.2.0
 */
class WP_Press_This {
	// Used to trigger the bookmarklet update notice.
	const VERSION = 8;
	public $version = 8;

	private $images = array();

	private $embeds = array();

	private $domain = '';

	/**
	 * Constructor.
	 *
	 * @since 4.2.0
	 */
	public function __construct() {}

	/**
	 * App and site settings data, including i18n strings for the client-side.
	 *
	 * @since 4.2.0
	 *
	 * @return array Site settings.
	 */
	public function site_settings() {
		return array(
			/**
			 * Filters whether or not Press This should redirect the user in the parent window upon save.
			 *
			 * @since 4.2.0
			 *
			 * @param bool $redirect Whether to redirect in parent window or not. Default false.
			 */
			'redirInParent' => apply_filters( 'press_this_redirect_in_parent', false ),
		);
	}

	/**
	 * Get the source's images and save them locally, for posterity, unless we can't.
	 *
	 * @since 4.2.0
	 *
	 * @param int    $post_id Post ID.
	 * @param string $content Optional. Current expected markup for Press This. Expects slashed. Default empty.
	 * @return string New markup with old image URLs replaced with the local attachment ones if swapped.
	 */
	public function side_load_images( $post_id, $content = '' ) {
		$content = wp_unslash( $content );

		if ( preg_match_all( '/<img [^>]+>/', $content, $matches ) && current_user_can( 'upload_files' ) ) {
			foreach ( (array) $matches[0] as $image ) {
				// This is inserted from our JS so HTML attributes should always be in double quotes.
				if ( ! preg_match( '/src="([^"]+)"/', $image, $url_matches ) ) {
					continue;
				}

				$image_src = $url_matches[1];

				// Don't try to sideload a file without a file extension, leads to WP upload error.
				if ( ! preg_match( '/[^\?]+\.(?:jpe?g|jpe|gif|png)(?:\?|$)/i', $image_src ) ) {
					continue;
				}

				// Sideload image, which gives us a new image src.
				$new_src = media_sideload_image( $image_src, $post_id, null, 'src' );

				if ( ! is_wp_error( $new_src ) ) {
					// Replace the POSTED content <img> with correct uploaded ones.
					// Need to do it in two steps so we don't replace links to the original image if any.
					$new_image = str_replace( $image_src, $new_src, $image );
					$content = str_replace( $image, $new_image, $content );
				}
			}
		}

		// Expected slashed
		return wp_slash( $content );
	}

	/**
	 * Ajax handler for saving the post as draft or published.
	 *
	 * @since 4.2.0
	 */
	public function save_post() {
		if ( empty( $_POST['post_ID'] ) || ! $post_id = (int) $_POST['post_ID'] ) {
			wp_send_json_error( array( 'errorMessage' => __( 'Missing post ID.' ) ) );
		}

		if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'update-post_' . $post_id ) ||
			! current_user_can( 'edit_post', $post_id ) ) {

			wp_send_json_error( array( 'errorMessage' => __( 'Invalid post.' ) ) );
		}

		$post_data = array(
			'ID'            => $post_id,
			'post_title'    => ( ! empty( $_POST['post_title'] ) ) ? sanitize_text_field( trim( $_POST['post_title'] ) ) : '',
			'post_content'  => ( ! empty( $_POST['post_content'] ) ) ? trim( $_POST['post_content'] ) : '',
			'post_type'     => 'post',
			'post_status'   => 'draft',
			'post_format'   => ( ! empty( $_POST['post_format'] ) ) ? sanitize_text_field( $_POST['post_format'] ) : '',
		);

		// Only accept categories if the user actually can assign
		$category_tax = get_taxonomy( 'category' );
		if ( current_user_can( $category_tax->cap->assign_terms ) ) {
			$post_data['post_category'] = ( ! empty( $_POST['post_category'] ) ) ? $_POST['post_category'] : array();
		}

		// Only accept taxonomies if the user can actually assign
		if ( ! empty( $_POST['tax_input'] ) ) {
			$tax_input = $_POST['tax_input'];
			foreach ( $tax_input as $tax => $_ti ) {
				$tax_object = get_taxonomy( $tax );
				if ( ! $tax_object || ! current_user_can( $tax_object->cap->assign_terms ) ) {
					unset( $tax_input[ $tax ] );
				}
			}

			$post_data['tax_input'] = $tax_input;
		}

		// Toggle status to pending if user cannot actually publish
		if ( ! empty( $_POST['post_status'] ) && 'publish' === $_POST['post_status'] ) {
			if ( current_user_can( 'publish_posts' ) ) {
				$post_data['post_status'] = 'publish';
			} else {
				$post_data['post_status'] = 'pending';
			}
		}

		$post_data['post_content'] = $this->side_load_images( $post_id, $post_data['post_content'] );

		/**
		 * Filters the post data of a Press This post before saving/updating.
		 *
		 * The {@see 'side_load_images'} action has already run at this point.
		 *
		 * @since 4.5.0
		 *
		 * @param array $post_data The post data.
		 */
		$post_data = apply_filters( 'press_this_save_post', $post_data );

		$updated = wp_update_post( $post_data, true );

		if ( is_wp_error( $updated ) ) {
			wp_send_json_error( array( 'errorMessage' => $updated->get_error_message() ) );
		} else {
			if ( isset( $post_data['post_format'] ) ) {
				if ( current_theme_supports( 'post-formats', $post_data['post_format'] ) ) {
					set_post_format( $post_id, $post_data['post_format'] );
				} elseif ( $post_data['post_format'] ) {
					set_post_format( $post_id, false );
				}
			}

			$forceRedirect = false;

			if ( 'publish' === get_post_status( $post_id ) ) {
				$redirect = get_post_permalink( $post_id );
			} elseif ( isset( $_POST['pt-force-redirect'] ) && $_POST['pt-force-redirect'] === 'true' ) {
				$forceRedirect = true;
				$redirect = get_edit_post_link( $post_id, 'js' );
			} else {
				$redirect = false;
			}

			/**
			 * Filters the URL to redirect to when Press This saves.
			 *
			 * @since 4.2.0
			 *
			 * @param string $url     Redirect URL. If `$status` is 'publish', this will be the post permalink.
			 *                        Otherwise, the default is false resulting in no redirect.
			 * @param int    $post_id Post ID.
			 * @param string $status  Post status.
			 */
			$redirect = apply_filters( 'press_this_save_redirect', $redirect, $post_id, $post_data['post_status'] );

			if ( $redirect ) {
				wp_send_json_success( array( 'redirect' => $redirect, 'force' => $forceRedirect ) );
			} else {
				wp_send_json_success( array( 'postSaved' => true ) );
			}
		}
	}

	/**
	 * Ajax handler for adding a new category.
	 *
	 * @since 4.2.0
	 */
	public function add_category() {
		if ( false === wp_verify_nonce( $_POST['new_cat_nonce'], 'add-category' ) ) {
			wp_send_json_error();
		}

		$taxonomy = get_taxonomy( 'category' );

		if ( ! current_user_can( $taxonomy->cap->edit_terms ) || empty( $_POST['name'] ) ) {
			wp_send_json_error();
		}

		$parent = isset( $_POST['parent'] ) && (int) $_POST['parent'] > 0 ? (int) $_POST['parent'] : 0;
		$names = explode( ',', $_POST['name'] );
		$added = $data = array();

		foreach ( $names as $cat_name ) {
			$cat_name = trim( $cat_name );
			$cat_nicename = sanitize_title( $cat_name );

			if ( empty( $cat_nicename ) ) {
				continue;
			}

			// @todo Find a more performant way to check existence, maybe get_term() with a separate parent check.
			if ( term_exists( $cat_name, $taxonomy->name, $parent ) ) {
				if ( count( $names ) === 1 ) {
					wp_send_json_error( array( 'errorMessage' => __( 'This category already exists.' ) ) );
				} else {
					continue;
				}
			}

			$cat_id = wp_insert_term( $cat_name, $taxonomy->name, array( 'parent' => $parent ) );

			if ( is_wp_error( $cat_id ) ) {
				continue;
			} elseif ( is_array( $cat_id ) ) {
				$cat_id = $cat_id['term_id'];
			}

			$added[] = $cat_id;
		}

		if ( empty( $added ) ) {
			wp_send_json_error( array( 'errorMessage' => __( 'This category cannot be added. Please change the name and try again.' ) ) );
		}

		foreach ( $added as $new_cat_id ) {
			$new_cat = get_category( $new_cat_id );

			if ( is_wp_error( $new_cat ) ) {
				wp_send_json_error( array( 'errorMessage' => __( 'Error while adding the category. Please try again later.' ) ) );
			}

			$data[] = array(
				'term_id' => $new_cat->term_id,
				'name' => $new_cat->name,
				'parent' => $new_cat->parent,
			);
		}
		wp_send_json_success( $data );
	}

	/**
	 * Downloads the source's HTML via server-side call for the given URL.
	 *
	 * @since 4.2.0
	 *
	 * @param string $url URL to scan.
	 * @return string Source's HTML sanitized markup
	 */
	public function fetch_source_html( $url ) {
		if ( empty( $url ) ) {
			return new WP_Error( 'invalid-url', __( 'A valid URL was not provided.' ) );
		}

		$remote_url = wp_safe_remote_get( $url, array(
			'timeout' => 30,
			// Use an explicit user-agent for Press This
			'user-agent' => 'Press This (WordPress/' . get_bloginfo( 'version' ) . '); ' . get_bloginfo( 'url' )
		) );

		if ( is_wp_error( $remote_url ) ) {
			return $remote_url;
		}

		$allowed_elements = array(
			'img' => array(
				'src'      => true,
				'width'    => true,
				'height'   => true,
			),
			'iframe' => array(
				'src'      => true,
			),
			'link' => array(
				'rel'      => true,
				'itemprop' => true,
				'href'     => true,
			),
			'meta' => array(
				'property' => true,
				'name'     => true,
				'content'  => true,
			)
		);

		$source_content = wp_remote_retrieve_body( $remote_url );
		$source_content = wp_kses( $source_content, $allowed_elements );

		return $source_content;
	}

	/**
	 * Utility method to limit an array to 50 values.
	 *
	 * @ignore
	 * @since 4.2.0
	 *
	 * @param array $value Array to limit.
	 * @return array Original array if fewer than 50 values, limited array, empty array otherwise.
	 */
	private function _limit_array( $value ) {
		if ( is_array( $value ) ) {
			if ( count( $value ) > 50 ) {
				return array_slice( $value, 0, 50 );
			}

			return $value;
		}

		return array();
	}

	/**
	 * Utility method to limit the length of a given string to 5,000 characters.
	 *
	 * @ignore
	 * @since 4.2.0
	 *
	 * @param string $value String to limit.
	 * @return bool|int|string If boolean or integer, that value. If a string, the original value
	 *                         if fewer than 5,000 characters, a truncated version, otherwise an
	 *                         empty string.
	 */
	private function _limit_string( $value ) {
		$return = '';

		if ( is_numeric( $value ) || is_bool( $value ) ) {
			$return = $value;
		} else if ( is_string( $value ) ) {
			if ( mb_strlen( $value ) > 5000 ) {
				$return = mb_substr( $value, 0, 5000 );
			} else {
				$return = $value;
			}

			$return = html_entity_decode( $return, ENT_QUOTES, 'UTF-8' );
			$return = sanitize_text_field( trim( $return ) );
		}

		return $return;
	}

	/**
	 * Utility method to limit a given URL to 2,048 characters.
	 *
	 * @ignore
	 * @since 4.2.0
	 *
	 * @param string $url URL to check for length and validity.
	 * @return string Escaped URL if of valid length (< 2048) and makeup. Empty string otherwise.
	 */
	private function _limit_url( $url ) {
		if ( ! is_string( $url ) ) {
			return '';
		}

		// HTTP 1.1 allows 8000 chars but the "de-facto" standard supported in all current browsers is 2048.
		if ( strlen( $url ) > 2048 ) {
			return ''; // Return empty rather than a truncated/invalid URL
		}

		// Does not look like a URL.
		if ( ! preg_match( '/^([!#$&-;=?-\[\]_a-z~]|%[0-9a-fA-F]{2})+$/', $url ) ) {
			return '';
		}

		// If the URL is root-relative, prepend the protocol and domain name
		if ( $url && $this->domain && preg_match( '%^/[^/]+%', $url ) ) {
			$url = $this->domain . $url;
		}

		// Not absolute or protocol-relative URL.
		if ( ! preg_match( '%^(?:https?:)?//[^/]+%', $url ) ) {
			return '';
		}

		return esc_url_raw( $url, array( 'http', 'https' ) );
	}

	/**
	 * Utility method to limit image source URLs.
	 *
	 * Excluded URLs include share-this type buttons, loaders, spinners, spacers, WordPress interface images,
	 * tiny buttons or thumbs, mathtag.com or quantserve.com images, or the WordPress.com stats gif.
	 *
	 * @ignore
	 * @since 4.2.0
	 *
	 * @param string $src Image source URL.
	 * @return string If not matched an excluded URL type, the original URL, empty string otherwise.
	 */
	private function _limit_img( $src ) {
		$src = $this->_limit_url( $src );

		if ( preg_match( '!/ad[sx]?/!i', $src ) ) {
			// Ads
			return '';
		} else if ( preg_match( '!(/share-?this[^.]+?\.[a-z0-9]{3,4})(\?.*)?$!i', $src ) ) {
			// Share-this type button
			return '';
		} else if ( preg_match( '!/(spinner|loading|spacer|blank|rss)\.(gif|jpg|png)!i', $src ) ) {
			// Loaders, spinners, spacers
			return '';
		} else if ( preg_match( '!/([^./]+[-_])?(spinner|loading|spacer|blank)s?([-_][^./]+)?\.[a-z0-9]{3,4}!i', $src ) ) {
			// Fancy loaders, spinners, spacers
			return '';
		} else if ( preg_match( '!([^./]+[-_])?thumb[^.]*\.(gif|jpg|png)$!i', $src ) ) {
			// Thumbnails, too small, usually irrelevant to context
			return '';
		} else if ( false !== stripos( $src, '/wp-includes/' ) ) {
			// Classic WordPress interface images
			return '';
		} else if ( preg_match( '![^\d]\d{1,2}x\d+\.(gif|jpg|png)$!i', $src ) ) {
			// Most often tiny buttons/thumbs (< 100px wide)
			return '';
		} else if ( preg_match( '!/pixel\.(mathtag|quantserve)\.com!i', $src ) ) {
			// See mathtag.com and https://www.quantcast.com/how-we-do-it/iab-standard-measurement/how-we-collect-data/
			return '';
		} else if ( preg_match( '!/[gb]\.gif(\?.+)?$!i', $src ) ) {
			// WordPress.com stats gif
			return '';
		}

		return $src;
	}

	/**
	 * Limit embed source URLs to specific providers.
	 *
	 * Not all core oEmbed providers are supported. Supported providers include YouTube, Vimeo,
	 * Daily Motion, SoundCloud, and Twitter.
	 *
	 * @ignore
	 * @since 4.2.0
	 *
	 * @param string $src Embed source URL.
	 * @return string If not from a supported provider, an empty string. Otherwise, a reformatted embed URL.
	 */
	private function _limit_embed( $src ) {
		$src = $this->_limit_url( $src );

		if ( empty( $src ) )
			return '';

		if ( preg_match( '!//(m|www)\.youtube\.com/(embed|v)/([^?]+)\?.+$!i', $src, $src_matches ) ) {
			// Embedded Youtube videos (www or mobile)
			$src = 'https://www.youtube.com/watch?v=' . $src_matches[3];
		} else if ( preg_match( '!//player\.vimeo\.com/video/([\d]+)([?/].*)?$!i', $src, $src_matches ) ) {
			// Embedded Vimeo iframe videos
			$src = 'https://vimeo.com/' . (int) $src_matches[1];
		} else if ( preg_match( '!//vimeo\.com/moogaloop\.swf\?clip_id=([\d]+)$!i', $src, $src_matches ) ) {
			// Embedded Vimeo Flash videos
			$src = 'https://vimeo.com/' . (int) $src_matches[1];
		} else if ( preg_match( '!//(www\.)?dailymotion\.com/embed/video/([^/?]+)([/?].+)?!i', $src, $src_matches ) ) {
			// Embedded Daily Motion videos
			$src = 'https://www.dailymotion.com/video/' . $src_matches[2];
		} else {
			$oembed = _wp_oembed_get_object();

			if ( ! $oembed->get_provider( $src, array( 'discover' => false ) ) ) {
				$src = '';
			}
		}

		return $src;
	}

	/**
	 * Process a meta data entry from the source.
	 *
	 * @ignore
	 * @since 4.2.0
	 *
	 * @param string $meta_name  Meta key name.
	 * @param mixed  $meta_value Meta value.
	 * @param array  $data       Associative array of source data.
	 * @return array Processed data array.
	 */
	private function _process_meta_entry( $meta_name, $meta_value, $data ) {
		if ( preg_match( '/:?(title|description|keywords|site_name)$/', $meta_name ) ) {
			$data['_meta'][ $meta_name ] = $meta_value;
		} else {
			switch ( $meta_name ) {
				case 'og:url':
				case 'og:video':
				case 'og:video:secure_url':
					$meta_value = $this->_limit_embed( $meta_value );

					if ( ! isset( $data['_embeds'] ) ) {
						$data['_embeds'] = array();
					}

					if ( ! empty( $meta_value ) && ! in_array( $meta_value, $data['_embeds'] ) ) {
						$data['_embeds'][] = $meta_value;
					}

					break;
				case 'og:image':
				case 'og:image:secure_url':
				case 'twitter:image0:src':
				case 'twitter:image0':
				case 'twitter:image:src':
				case 'twitter:image':
					$meta_value = $this->_limit_img( $meta_value );

					if ( ! isset( $data['_images'] ) ) {
						$data['_images'] = array();
					}

					if ( ! empty( $meta_value ) && ! in_array( $meta_value, $data['_images'] ) ) {
						$data['_images'][] = $meta_value;
					}

					break;
			}
		}

		return $data;
	}

	/**
	 * Fetches and parses _meta, _images, and _links data from the source.
	 *
	 * @since 4.2.0
	 *
	 * @param string $url  URL to scan.
	 * @param array  $data Optional. Existing data array if you have one. Default empty array.
	 * @return array New data array.
	 */
	public function source_data_fetch_fallback( $url, $data = array() ) {
		if ( empty( $url ) ) {
			return array();
		}

		// Download source page to tmp file.
		$source_content = $this->fetch_source_html( $url );
		if ( is_wp_error( $source_content ) ) {
			return array( 'errors' => $source_content->get_error_messages() );
		}

		// Fetch and gather <meta> data first, so discovered media is offered 1st to user.
		if ( empty( $data['_meta'] ) ) {
			$data['_meta'] = array();
		}

		if ( preg_match_all( '/<meta [^>]+>/', $source_content, $matches ) ) {
			$items = $this->_limit_array( $matches[0] );

			foreach ( $items as $value ) {
				if ( preg_match( '/(property|name)="([^"]+)"[^>]+content="([^"]+)"/', $value, $new_matches ) ) {
					$meta_name  = $this->_limit_string( $new_matches[2] );
					$meta_value = $this->_limit_string( $new_matches[3] );

					// Sanity check. $key is usually things like 'title', 'description', 'keywords', etc.
					if ( strlen( $meta_name ) > 100 ) {
						continue;
					}

					$data = $this->_process_meta_entry( $meta_name, $meta_value, $data );
				}
			}
		}

		// Fetch and gather <img> data.
		if ( empty( $data['_images'] ) ) {
			$data['_images'] = array();
		}

		if ( preg_match_all( '/<img [^>]+>/', $source_content, $matches ) ) {
			$items = $this->_limit_array( $matches[0] );

			foreach ( $items as $value ) {
				if ( ( preg_match( '/width=(\'|")(\d+)\\1/i', $value, $new_matches ) && $new_matches[2] < 256 ) ||
					( preg_match( '/height=(\'|")(\d+)\\1/i', $value, $new_matches ) && $new_matches[2] < 128 ) ) {

					continue;
				}

				if ( preg_match( '/src=(\'|")([^\'"]+)\\1/i', $value, $new_matches ) ) {
					$src = $this->_limit_img( $new_matches[2] );
					if ( ! empty( $src ) && ! in_array( $src, $data['_images'] ) ) {
						$data['_images'][] = $src;
					}
				}
			}
		}

		// Fetch and gather <iframe> data.
		if ( empty( $data['_embeds'] ) ) {
			$data['_embeds'] = array();
		}

		if ( preg_match_all( '/<iframe [^>]+>/', $source_content, $matches ) ) {
			$items = $this->_limit_array( $matches[0] );

			foreach ( $items as $value ) {
				if ( preg_match( '/src=(\'|")([^\'"]+)\\1/', $value, $new_matches ) ) {
					$src = $this->_limit_embed( $new_matches[2] );

					if ( ! empty( $src ) && ! in_array( $src, $data['_embeds'] ) ) {
						$data['_embeds'][] = $src;
					}
				}
			}
		}

		// Fetch and gather <link> data.
		if ( empty( $data['_links'] ) ) {
			$data['_links'] = array();
		}

		if ( preg_match_all( '/<link [^>]+>/', $source_content, $matches ) ) {
			$items = $this->_limit_array( $matches[0] );

			foreach ( $items as $value ) {
				if ( preg_match( '/rel=["\'](canonical|shortlink|icon)["\']/i', $value, $matches_rel ) && preg_match( '/href=[\'"]([^\'" ]+)[\'"]/i', $value, $matches_url ) ) {
					$rel = $matches_rel[1];
					$url = $this->_limit_url( $matches_url[1] );

					if ( ! empty( $url ) && empty( $data['_links'][ $rel ] ) ) {
						$data['_links'][ $rel ] = $url;
					}
				}
			}
		}

		return $data;
	}

	/**
	 * Handles backward-compat with the legacy version of Press This by supporting its query string params.
	 *
	 * @since 4.2.0
	 *
	 * @return array
	 */
	public function merge_or_fetch_data() {
		// Get data from $_POST and $_GET, as appropriate ($_POST > $_GET), to remain backward compatible.
		$data = array();

		// Only instantiate the keys we want. Sanity check and sanitize each one.
		foreach ( array( 'u', 's', 't', 'v' ) as $key ) {
			if ( ! empty( $_POST[ $key ] ) ) {
				$value = wp_unslash( $_POST[ $key ] );
			} else if ( ! empty( $_GET[ $key ] ) ) {
				$value = wp_unslash( $_GET[ $key ] );
			} else {
				continue;
			}

			if ( 'u' === $key ) {
				$value = $this->_limit_url( $value );

				if ( preg_match( '%^(?:https?:)?//[^/]+%i', $value, $domain_match ) ) {
					$this->domain = $domain_match[0];
				}
			} else {
				$value = $this->_limit_string( $value );
			}

			if ( ! empty( $value ) ) {
				$data[ $key ] = $value;
			}
		}

		/**
		 * Filters whether to enable in-source media discovery in Press This.
		 *
		 * @since 4.2.0
		 *
		 * @param bool $enable Whether to enable media discovery.
		 */
		if ( apply_filters( 'enable_press_this_media_discovery', true ) ) {
			/*
			 * If no title, _images, _embed, and _meta was passed via $_POST, fetch data from source as fallback,
			 * making PT fully backward compatible with the older bookmarklet.
			 */
			if ( empty( $_POST ) && ! empty( $data['u'] ) ) {
				if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'scan-site' ) ) {
					$data = $this->source_data_fetch_fallback( $data['u'], $data );
				} else {
					$data['errors'] = 'missing nonce';
				}
			} else {
				foreach ( array( '_images', '_embeds' ) as $type ) {
					if ( empty( $_POST[ $type ] ) ) {
						continue;
					}

					$data[ $type ] = array();
					$items = $this->_limit_array( $_POST[ $type ] );

					foreach ( $items as $key => $value ) {
						if ( $type === '_images' ) {
							$value = $this->_limit_img( wp_unslash( $value ) );
						} else {
							$value = $this->_limit_embed( wp_unslash( $value ) );
						}

						if ( ! empty( $value ) ) {
							$data[ $type ][] = $value;
						}
					}
				}

				foreach ( array( '_meta', '_links' ) as $type ) {
					if ( empty( $_POST[ $type ] ) ) {
						continue;
					}

					$data[ $type ] = array();
					$items = $this->_limit_array( $_POST[ $type ] );

					foreach ( $items as $key => $value ) {
						// Sanity check. These are associative arrays, $key is usually things like 'title', 'description', 'keywords', etc.
						if ( empty( $key ) || strlen( $key ) > 100 ) {
							continue;
						}

						if ( $type === '_meta' ) {
							$value = $this->_limit_string( wp_unslash( $value ) );

							if ( ! empty( $value ) ) {
								$data = $this->_process_meta_entry( $key, $value, $data );
							}
						} else {
							if ( in_array( $key, array( 'canonical', 'shortlink', 'icon' ), true ) ) {
								$data[ $type ][ $key ] = $this->_limit_url( wp_unslash( $value ) );
							}
						}
					}
				}
			}

			// Support passing a single image src as `i`
			if ( ! empty( $_REQUEST['i'] ) && ( $img_src = $this->_limit_img( wp_unslash( $_REQUEST['i'] ) ) ) ) {
				if ( empty( $data['_images'] ) ) {
					$data['_images'] = array( $img_src );
				} elseif ( ! in_array( $img_src, $data['_images'], true ) ) {
					array_unshift( $data['_images'], $img_src );
				}
			}
		}

		/**
		 * Filters the Press This data array.
		 *
		 * @since 4.2.0
		 *
		 * @param array $data Press This Data array.
		 */
		return apply_filters( 'press_this_data', $data );
	}

	/**
	 * Adds another stylesheet inside TinyMCE.
	 *
	 * @since 4.2.0
	 *
	 * @param string $styles URL to editor stylesheet.
	 * @return string Possibly modified stylesheets list.
	 */
	public function add_editor_style( $styles ) {
		if ( ! empty( $styles ) ) {
			$styles .= ',';
		}

		$press_this = admin_url( 'css/press-this-editor.css' );
		if ( is_rtl() ) {
			$press_this = str_replace( '.css', '-rtl.css', $press_this );
		}

		return $styles . $press_this;
	}

	/**
	 * Outputs the post format selection HTML.
	 *
	 * @since 4.2.0
	 *
	 * @param WP_Post $post Post object.
	 */
	public function post_formats_html( $post ) {
		if ( current_theme_supports( 'post-formats' ) && post_type_supports( $post->post_type, 'post-formats' ) ) {
			$post_formats = get_theme_support( 'post-formats' );

			if ( is_array( $post_formats[0] ) ) {
				$post_format = get_post_format( $post->ID );

				if ( ! $post_format ) {
					$post_format = '0';
				}

				// Add in the current one if it isn't there yet, in case the current theme doesn't support it.
				if ( $post_format && ! in_array( $post_format, $post_formats[0] ) ) {
					$post_formats[0][] = $post_format;
				}

				?>
				<div id="post-formats-select">
				<fieldset><legend class="screen-reader-text"><?php _e( 'Post Formats' ); ?></legend>
					<input type="radio" name="post_format" class="post-format" id="post-format-0" value="0" <?php checked( $post_format, '0' ); ?> />
					<label for="post-format-0" class="post-format-icon post-format-standard"><?php echo get_post_format_string( 'standard' ); ?></label>
					<?php

					foreach ( $post_formats[0] as $format ) {
						$attr_format = esc_attr( $format );
						?>
						<br />
						<input type="radio" name="post_format" class="post-format" id="post-format-<?php echo $attr_format; ?>" value="<?php echo $attr_format; ?>" <?php checked( $post_format, $format ); ?> />
						<label for="post-format-<?php echo $attr_format ?>" class="post-format-icon post-format-<?php echo $attr_format; ?>"><?php echo esc_html( get_post_format_string( $format ) ); ?></label>
						<?php
					 }

					 ?>
				</fieldset>
				</div>
				<?php
			}
		}
	}

	/**
	 * Outputs the categories HTML.
	 *
	 * @since 4.2.0
	 *
	 * @param WP_Post $post Post object.
	 */
	public function categories_html( $post ) {
		$taxonomy = get_taxonomy( 'category' );

		// Bail if user cannot assign terms
		if ( ! current_user_can( $taxonomy->cap->assign_terms ) ) {
			return;
		}

		// Only show "add" if user can edit terms
		if ( current_user_can( $taxonomy->cap->edit_terms ) ) {
			?>
			<button type="button" class="add-cat-toggle button-link" aria-expanded="false">
				<span class="dashicons dashicons-plus"></span><span class="screen-reader-text"><?php _e( 'Toggle add category' ); ?></span>
			</button>
			<div class="add-category is-hidden">
				<label class="screen-reader-text" for="new-category"><?php echo $taxonomy->labels->add_new_item; ?></label>
				<input type="text" id="new-category" class="add-category-name" placeholder="<?php echo esc_attr( $taxonomy->labels->new_item_name ); ?>" value="" aria-required="true">
				<label class="screen-reader-text" for="new-category-parent"><?php echo $taxonomy->labels->parent_item_colon; ?></label>
				<div class="postform-wrapper">
					<?php
					wp_dropdown_categories( array(
						'taxonomy'         => 'category',
						'hide_empty'       => 0,
						'name'             => 'new-category-parent',
						'orderby'          => 'name',
						'hierarchical'     => 1,
						'show_option_none' => '&mdash; ' . $taxonomy->labels->parent_item . ' &mdash;'
					) );
					?>
				</div>
				<button type="button" class="add-cat-submit"><?php _e( 'Add' ); ?></button>
			</div>
			<?php

		}
		?>
		<div class="categories-search-wrapper">
			<input id="categories-search" type="search" class="categories-search" placeholder="<?php esc_attr_e( 'Search categories by name' ) ?>">
			<label for="categories-search">
				<span class="dashicons dashicons-search"></span><span class="screen-reader-text"><?php _e( 'Search categories' ); ?></span>
			</label>
		</div>
		<div aria-label="<?php esc_attr_e( 'Categories' ); ?>">
			<ul class="categories-select">
				<?php wp_terms_checklist( $post->ID, array( 'taxonomy' => 'category', 'list_only' => true ) ); ?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Outputs the tags HTML.
	 *
	 * @since 4.2.0
	 *
	 * @param WP_Post $post Post object.
	 */
	public function tags_html( $post ) {
		$taxonomy              = get_taxonomy( 'post_tag' );
		$user_can_assign_terms = current_user_can( $taxonomy->cap->assign_terms );
		$esc_tags              = get_terms_to_edit( $post->ID, 'post_tag' );

		if ( ! $esc_tags || is_wp_error( $esc_tags ) ) {
			$esc_tags = '';
		}

		?>
		<div class="tagsdiv" id="post_tag">
			<div class="jaxtag">
			<input type="hidden" name="tax_input[post_tag]" class="the-tags" value="<?php echo $esc_tags; // escaped in get_terms_to_edit() ?>">
		 	<?php

			if ( $user_can_assign_terms ) {
				?>
				<div class="ajaxtag hide-if-no-js">
					<label class="screen-reader-text" for="new-tag-post_tag"><?php _e( 'Tags' ); ?></label>
					<p>
						<input type="text" id="new-tag-post_tag" name="newtag[post_tag]" class="newtag form-input-tip" size="16" autocomplete="off" value="" aria-describedby="new-tag-desc" />
						<button type="button" class="tagadd"><?php _e( 'Add' ); ?></button>
					</p>
				</div>
				<p class="howto" id="new-tag-desc">
					<?php echo $taxonomy->labels->separate_items_with_commas; ?>
				</p>
				<?php
			}

			?>
			</div>
			<ul class="tagchecklist" role="list"></ul>
		</div>
		<?php

		if ( $user_can_assign_terms ) {
			?>
			<button type="button" class="button-link tagcloud-link" id="link-post_tag" aria-expanded="false"><?php echo $taxonomy->labels->choose_from_most_used; ?></button>
			<?php
		}
	}

	/**
	 * Get a list of embeds with no duplicates.
	 *
	 * @since 4.2.0
	 *
	 * @param array $data The site's data.
	 * @return array Embeds selected to be available.
	 */
	public function get_embeds( $data ) {
		$selected_embeds = array();

		// Make sure to add the Pressed page if it's a valid oembed itself
		if ( ! empty ( $data['u'] ) && $this->_limit_embed( $data['u'] ) ) {
			$data['_embeds'][] = $data['u'];
		}

		if ( ! empty( $data['_embeds'] ) ) {
			foreach ( $data['_embeds'] as $src ) {
				$prot_relative_src = preg_replace( '/^https?:/', '', $src );

				if ( in_array( $prot_relative_src, $this->embeds ) ) {
					continue;
				}

				$selected_embeds[] = $src;
				$this->embeds[] = $prot_relative_src;
			}
		}

		return $selected_embeds;
	}

	/**
	 * Get a list of images with no duplicates.
	 *
	 * @since 4.2.0
	 *
	 * @param array $data The site's data.
	 * @return array
	 */
	public function get_images( $data ) {
		$selected_images = array();

		if ( ! empty( $data['_images'] ) ) {
			foreach ( $data['_images'] as $src ) {
				if ( false !== strpos( $src, 'gravatar.com' ) ) {
					$src = preg_replace( '%http://[\d]+\.gravatar\.com/%', 'https://secure.gravatar.com/', $src );
				}

				$prot_relative_src = preg_replace( '/^https?:/', '', $src );

				if ( in_array( $prot_relative_src, $this->images ) ||
					( false !== strpos( $src, 'avatar' ) && count( $this->images ) > 15 ) ) {
					// Skip: already selected or some type of avatar and we've already gathered more than 15 images.
					continue;
				}

				$selected_images[] = $src;
				$this->images[] = $prot_relative_src;
			}
		}

		return $selected_images;
	}

	/**
	 * Gets the source page's canonical link, based on passed location and meta data.
	 *
	 * @since 4.2.0
	 *
 	 * @param array $data The site's data.
	 * @return string Discovered canonical URL, or empty
	 */
	public function get_canonical_link( $data ) {
		$link = '';

		if ( ! empty( $data['_links']['canonical'] ) ) {
			$link = $data['_links']['canonical'];
		} elseif ( ! empty( $data['u'] ) ) {
			$link = $data['u'];
		} elseif ( ! empty( $data['_meta'] ) ) {
			if ( ! empty( $data['_meta']['twitter:url'] ) ) {
				$link = $data['_meta']['twitter:url'];
			} else if ( ! empty( $data['_meta']['og:url'] ) ) {
				$link = $data['_meta']['og:url'];
			}
		}

		if ( empty( $link ) && ! empty( $data['_links']['shortlink'] ) ) {
			$link = $data['_links']['shortlink'];
		}

		return $link;
	}

	/**
	 * Gets the source page's site name, based on passed meta data.
	 *
	 * @since 4.2.0
	 *
	 * @param array $data The site's data.
	 * @return string Discovered site name, or empty
	 */
	public function get_source_site_name( $data ) {
		$name = '';

		if ( ! empty( $data['_meta'] ) ) {
			if ( ! empty( $data['_meta']['og:site_name'] ) ) {
				$name = $data['_meta']['og:site_name'];
			} else if ( ! empty( $data['_meta']['application-name'] ) ) {
				$name = $data['_meta']['application-name'];
			}
		}

		return $name;
	}

	/**
	 * Gets the source page's title, based on passed title and meta data.
	 *
	 * @since 4.2.0
	 *
	 * @param array $data The site's data.
	 * @return string Discovered page title, or empty
	 */
	public function get_suggested_title( $data ) {
		$title = '';

		if ( ! empty( $data['t'] ) ) {
			$title = $data['t'];
		} elseif ( ! empty( $data['_meta'] ) ) {
			if ( ! empty( $data['_meta']['twitter:title'] ) ) {
				$title = $data['_meta']['twitter:title'];
			} else if ( ! empty( $data['_meta']['og:title'] ) ) {
				$title = $data['_meta']['og:title'];
			} else if ( ! empty( $data['_meta']['title'] ) ) {
				$title = $data['_meta']['title'];
			}
		}

		return $title;
	}

	/**
	 * Gets the source page's suggested content, based on passed data (description, selection, etc).
	 *
	 * Features a blockquoted excerpt, as well as content attribution, if any.
	 *
	 * @since 4.2.0
	 *
	 * @param array $data The site's data.
	 * @return string Discovered content, or empty
	 */
	public function get_suggested_content( $data ) {
		$content = $text = '';

		if ( ! empty( $data['s'] ) ) {
			$text = $data['s'];
		} else if ( ! empty( $data['_meta'] ) ) {
			if ( ! empty( $data['_meta']['twitter:description'] ) ) {
				$text = $data['_meta']['twitter:description'];
			} else if ( ! empty( $data['_meta']['og:description'] ) ) {
				$text = $data['_meta']['og:description'];
			} else if ( ! empty( $data['_meta']['description'] ) ) {
				$text = $data['_meta']['description'];
			}

			// If there is an ellipsis at the end, the description is very likely auto-generated. Better to ignore it.
			if ( $text && substr( $text, -3 ) === '...' ) {
				$text = '';
			}
		}

		$default_html = array( 'quote' => '', 'link' => '', 'embed' => '' );

		if ( ! empty( $data['u'] ) && $this->_limit_embed( $data['u'] ) ) {
			$default_html['embed'] = '<p>[embed]' . $data['u'] . '[/embed]</p>';

			if ( ! empty( $data['s'] ) ) {
				// If the user has selected some text, do quote it.
				$default_html['quote'] = '<blockquote>%1$s</blockquote>';
			}
		} else {
			$default_html['quote'] = '<blockquote>%1$s</blockquote>';
			$default_html['link'] = '<p>' . _x( 'Source:', 'Used in Press This to indicate where the content comes from.' ) .
				' <em><a href="%1$s">%2$s</a></em></p>';
		}

		/**
		 * Filters the default HTML tags used in the suggested content for the editor.
		 *
		 * The HTML strings use printf format. After filtering the content is added at the specified places with `sprintf()`.
		 *
		 * @since 4.2.0
		 *
		 * @param array $default_html Associative array with three possible keys:
		 *                                - 'quote' where %1$s is replaced with the site description or the selected content.
		 *                                - 'link' where %1$s is link href, %2$s is link text, usually the source page title.
		 *                                - 'embed' which contains an [embed] shortcode when the source page offers embeddable content.
		 * @param array $data         Associative array containing the data from the source page.
		 */
		$default_html = apply_filters( 'press_this_suggested_html', $default_html, $data );

		if ( ! empty( $default_html['embed'] ) ) {
			$content .= $default_html['embed'];
		}

		// Wrap suggested content in the specified HTML.
		if ( ! empty( $default_html['quote'] ) && $text ) {
			$content .= sprintf( $default_html['quote'], $text );
		}

		// Add source attribution if there is one available.
		if ( ! empty( $default_html['link'] ) ) {
			$title = $this->get_suggested_title( $data );
			$url = $this->get_canonical_link( $data );

			if ( ! $title ) {
				$title = $this->get_source_site_name( $data );
			}

			if ( $url && $title ) {
				$content .= sprintf( $default_html['link'], $url, $title );
			}
		}

		return $content;
	}

	/**
	 * Serves the app's base HTML, which in turns calls the load script.
	 *
	 * @since 4.2.0
	 *
	 * @global WP_Locale $wp_locale
	 * @global bool      $is_IE
	 */
	public function html() {
		global $wp_locale;

		$wp_version = get_bloginfo( 'version' );

		// Get data, new (POST) and old (GET).
		$data = $this->merge_or_fetch_data();

		$post_title = $this->get_suggested_title( $data );

		$post_content = $this->get_suggested_content( $data );

		// Get site settings array/data.
		$site_settings = $this->site_settings();

		// Pass the images and embeds
		$images = $this->get_images( $data );
		$embeds = $this->get_embeds( $data );

		$site_data = array(
			'v' => ! empty( $data['v'] ) ? $data['v'] : '',
			'u' => ! empty( $data['u'] ) ? $data['u'] : '',
			'hasData' => ! empty( $data ) && ! isset( $data['errors'] ),
		);

		if ( ! empty( $images ) ) {
			$site_data['_images'] = $images;
		}

		if ( ! empty( $embeds ) ) {
			$site_data['_embeds'] = $embeds;
		}

		// Add press-this-editor.css and remove theme's editor-style.css, if any.
		remove_editor_styles();

		add_filter( 'mce_css', array( $this, 'add_editor_style' ) );

		if ( ! empty( $GLOBALS['is_IE'] ) ) {
			@header( 'X-UA-Compatible: IE=edge' );
		}

		@header( 'Content-Type: ' . get_option( 'html_type' ) . '; charset=' . get_option( 'blog_charset' ) );

?>
<!DOCTYPE html>
<!--[if IE 7]>         <html class="lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>         <html class="lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?>> <!--<![endif]-->
<head>
	<meta http-equiv="Content-Type" content="<?php echo esc_attr( get_bloginfo( 'html_type' ) ); ?>; charset=<?php echo esc_attr( get_option( 'blog_charset' ) ); ?>" />
	<meta name="viewport" content="width=device-width">
	<title><?php esc_html_e( 'Press This!' ) ?></title>

	<script>
		window.wpPressThisData   = <?php echo wp_json_encode( $site_data ); ?>;
		window.wpPressThisConfig = <?php echo wp_json_encode( $site_settings ); ?>;
	</script>

	<script type="text/javascript">
		var ajaxurl = '<?php echo esc_js( admin_url( 'admin-ajax.php', 'relative' ) ); ?>',
			pagenow = 'press-this',
			typenow = 'post',
			adminpage = 'press-this-php',
			thousandsSeparator = '<?php echo addslashes( $wp_locale->number_format['thousands_sep'] ); ?>',
			decimalPoint = '<?php echo addslashes( $wp_locale->number_format['decimal_point'] ); ?>',
			isRtl = <?php echo (int) is_rtl(); ?>;
	</script>

	<?php
		/*
		 * $post->ID is needed for the embed shortcode so we can show oEmbed previews in the editor.
		 * Maybe find a way without it.
		 */
		$post = get_default_post_to_edit( 'post', true );
		$post_ID = (int) $post->ID;

		wp_enqueue_media( array( 'post' => $post_ID ) );
		wp_enqueue_style( 'press-this' );
		wp_enqueue_script( 'press-this' );
		wp_enqueue_script( 'json2' );
		wp_enqueue_script( 'editor' );

		$categories_tax   = get_taxonomy( 'category' );
		$show_categories  = current_user_can( $categories_tax->cap->assign_terms ) || current_user_can( $categories_tax->cap->edit_terms );

		$tag_tax          = get_taxonomy( 'post_tag' );
		$show_tags        = current_user_can( $tag_tax->cap->assign_terms );

		$supports_formats = false;
		$post_format      = 0;

		if ( current_theme_supports( 'post-formats' ) && post_type_supports( $post->post_type, 'post-formats' ) ) {
			$supports_formats = true;

			if ( ! ( $post_format = get_post_format( $post_ID ) ) ) {
				$post_format = 0;
			}
		}

		/** This action is documented in wp-admin/admin-header.php */
		do_action( 'admin_enqueue_scripts', 'press-this.php' );

		/** This action is documented in wp-admin/admin-header.php */
		do_action( 'admin_print_styles-press-this.php' );

		/** This action is documented in wp-admin/admin-header.php */
		do_action( 'admin_print_styles' );

		/** This action is documented in wp-admin/admin-header.php */
		do_action( 'admin_print_scripts-press-this.php' );

		/** This action is documented in wp-admin/admin-header.php */
		do_action( 'admin_print_scripts' );

		/** This action is documented in wp-admin/admin-header.php */
		do_action( 'admin_head-press-this.php' );

		/** This action is documented in wp-admin/admin-header.php */
		do_action( 'admin_head' );
	?>
</head>
<?php

	$admin_body_class  = 'press-this';
	$admin_body_class .= ( is_rtl() ) ? ' rtl' : '';
	$admin_body_class .= ' branch-' . str_replace( array( '.', ',' ), '-', floatval( $wp_version ) );
	$admin_body_class .= ' version-' . str_replace( '.', '-', preg_replace( '/^([.0-9]+).*/', '$1', $wp_version ) );
	$admin_body_class .= ' admin-color-' . sanitize_html_class( get_user_option( 'admin_color' ), 'fresh' );
	$admin_body_class .= ' locale-' . sanitize_html_class( strtolower( str_replace( '_', '-', get_user_locale() ) ) );

	/** This filter is documented in wp-admin/admin-header.php */
	$admin_body_classes = apply_filters( 'admin_body_class', '' );

?>
<body class="wp-admin wp-core-ui <?php echo $admin_body_classes . ' ' . $admin_body_class; ?>">
	<div id="adminbar" class="adminbar">
		<h1 id="current-site" class="current-site">
			<a class="current-site-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="home">
				<span class="dashicons dashicons-wordpress"></span>
				<span class="current-site-name"><?php bloginfo( 'name' ); ?></span>
			</a>
		</h1>
		<button type="button" class="options button-link closed">
			<span class="dashicons dashicons-tag on-closed"></span>
			<span class="screen-reader-text on-closed"><?php _e( 'Show post options' ); ?></span>
			<span aria-hidden="true" class="on-open"><?php _e( 'Done' ); ?></span>
			<span class="screen-reader-text on-open"><?php _e( 'Hide post options' ); ?></span>
		</button>
	</div>

	<div id="scanbar" class="scan">
		<form method="GET">
			<label for="url-scan" class="screen-reader-text"><?php _e( 'Scan site for content' ); ?></label>
			<input type="url" name="u" id="url-scan" class="scan-url" value="<?php echo esc_attr( $site_data['u'] ) ?>" placeholder="<?php esc_attr_e( 'Enter a URL to scan' ) ?>" />
			<input type="submit" name="url-scan-submit" id="url-scan-submit" class="scan-submit" value="<?php esc_attr_e( 'Scan' ) ?>" />
			<?php wp_nonce_field( 'scan-site' ); ?>
		</form>
	</div>

	<form id="pressthis-form" method="post" action="post.php" autocomplete="off">
		<input type="hidden" name="post_ID" id="post_ID" value="<?php echo $post_ID; ?>" />
		<input type="hidden" name="action" value="press-this-save-post" />
		<input type="hidden" name="post_status" id="post_status" value="draft" />
		<input type="hidden" name="wp-preview" id="wp-preview" value="" />
		<input type="hidden" name="post_title" id="post_title" value="" />
		<input type="hidden" name="pt-force-redirect" id="pt-force-redirect" value="" />
		<?php

		wp_nonce_field( 'update-post_' . $post_ID, '_wpnonce', false );
		wp_nonce_field( 'add-category', '_ajax_nonce-add-category', false );

		?>

	<div class="wrapper">
		<div class="editor-wrapper">
			<div class="alerts" role="alert" aria-live="assertive" aria-relevant="all" aria-atomic="true">
				<?php

				if ( isset( $data['v'] ) && $this->version > $data['v'] ) {
					?>
					<p class="alert is-notice">
						<?php printf( __( 'You should upgrade <a href="%s" target="_blank">your bookmarklet</a> to the latest version!' ), admin_url( 'tools.php' ) ); ?>
					</p>
					<?php
				}

				?>
			</div>

			<div id="app-container" class="editor">
				<span id="title-container-label" class="post-title-placeholder" aria-hidden="true"><?php _e( 'Post title' ); ?></span>
				<h2 id="title-container" class="post-title" contenteditable="true" spellcheck="true" aria-label="<?php esc_attr_e( 'Post title' ); ?>" tabindex="0"><?php echo esc_html( $post_title ); ?></h2>

				<div class="media-list-container">
					<div class="media-list-inner-container">
						<h2 class="screen-reader-text"><?php _e( 'Suggested media' ); ?></h2>
						<ul class="media-list"></ul>
					</div>
				</div>

				<?php
				wp_editor( $post_content, 'pressthis', array(
					'drag_drop_upload' => true,
					'editor_height'    => 600,
					'media_buttons'    => false,
					'textarea_name'    => 'post_content',
					'teeny'            => true,
					'tinymce'          => array(
						'resize'                => false,
						'wordpress_adv_hidden'  => false,
						'add_unload_trigger'    => false,
						'statusbar'             => false,
						'autoresize_min_height' => 600,
						'wp_autoresize_on'      => true,
						'plugins'               => 'lists,media,paste,tabfocus,fullscreen,wordpress,wpautoresize,wpeditimage,wpgallery,wplink,wptextpattern,wpview',
						'toolbar1'              => 'bold,italic,bullist,numlist,blockquote,link,unlink',
						'toolbar2'              => 'undo,redo',
					),
					'quicktags' => array(
						'buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,code,more',
					),
				) );

				?>
			</div>
		</div>

		<div class="options-panel-back is-hidden" tabindex="-1"></div>
		<div class="options-panel is-off-screen is-hidden" tabindex="-1">
			<div class="post-options">

				<?php if ( $supports_formats ) : ?>
					<button type="button" class="post-option">
						<span class="dashicons dashicons-admin-post"></span>
						<span class="post-option-title"><?php _ex( 'Format', 'post format' ); ?></span>
						<span class="post-option-contents" id="post-option-post-format"><?php echo esc_html( get_post_format_string( $post_format ) ); ?></span>
						<span class="dashicons post-option-forward"></span>
					</button>
				<?php endif; ?>

				<?php if ( $show_categories ) : ?>
					<button type="button" class="post-option">
						<span class="dashicons dashicons-category"></span>
						<span class="post-option-title"><?php _e( 'Categories' ); ?></span>
						<span class="dashicons post-option-forward"></span>
					</button>
				<?php endif; ?>

				<?php if ( $show_tags ) : ?>
					<button type="button" class="post-option">
						<span class="dashicons dashicons-tag"></span>
						<span class="post-option-title"><?php _e( 'Tags' ); ?></span>
						<span class="dashicons post-option-forward"></span>
					</button>
				<?php endif; ?>
			</div>

			<?php if ( $supports_formats ) : ?>
				<div class="setting-modal is-off-screen is-hidden">
					<button type="button" class="modal-close">
						<span class="dashicons post-option-back"></span>
						<span class="setting-title" aria-hidden="true"><?php _ex( 'Format', 'post format' ); ?></span>
						<span class="screen-reader-text"><?php _e( 'Back to post options' ) ?></span>
					</button>
					<?php $this->post_formats_html( $post ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $show_categories ) : ?>
				<div class="setting-modal is-off-screen is-hidden">
					<button type="button" class="modal-close">
						<span class="dashicons post-option-back"></span>
						<span class="setting-title" aria-hidden="true"><?php _e( 'Categories' ); ?></span>
						<span class="screen-reader-text"><?php _e( 'Back to post options' ) ?></span>
					</button>
					<?php $this->categories_html( $post ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $show_tags ) : ?>
				<div class="setting-modal tags is-off-screen is-hidden">
					<button type="button" class="modal-close">
						<span class="dashicons post-option-back"></span>
						<span class="setting-title" aria-hidden="true"><?php _e( 'Tags' ); ?></span>
						<span class="screen-reader-text"><?php _e( 'Back to post options' ) ?></span>
					</button>
					<?php $this->tags_html( $post ); ?>
				</div>
			<?php endif; ?>
		</div><!-- .options-panel -->
	</div><!-- .wrapper -->

	<div class="press-this-actions">
		<div class="pressthis-media-buttons">
			<button type="button" class="insert-media" data-editor="pressthis">
				<span class="dashicons dashicons-admin-media"></span>
				<span class="screen-reader-text"><?php _e( 'Add Media' ); ?></span>
			</button>
		</div>
		<div class="post-actions">
			<span class="spinner">&nbsp;</span>
			<div class="split-button">
				<div class="split-button-head">
					<button type="button" class="publish-button split-button-primary" aria-live="polite">
						<span class="publish"><?php echo ( current_user_can( 'publish_posts' ) ) ? __( 'Publish' ) : __( 'Submit for Review' ); ?></span>
						<span class="saving-draft"><?php _e( 'Saving&hellip;' ); ?></span>
					</button><button type="button" class="split-button-toggle" aria-haspopup="true" aria-expanded="false">
						<i class="dashicons dashicons-arrow-down-alt2"></i>
						<span class="screen-reader-text"><?php _e('More actions'); ?></span>
					</button>
				</div>
				<ul class="split-button-body">
					<li><button type="button" class="button-link draft-button split-button-option"><?php _e( 'Save Draft' ); ?></button></li>
					<li><button type="button" class="button-link standard-editor-button split-button-option"><?php _e( 'Standard Editor' ); ?></button></li>
					<li><button type="button" class="button-link preview-button split-button-option"><?php _e( 'Preview' ); ?></button></li>
				</ul>
			</div>
		</div>
	</div>
	</form>

	<?php
	/** This action is documented in wp-admin/admin-footer.php */
	do_action( 'admin_footer', '' );

	/** This action is documented in wp-admin/admin-footer.php */
	do_action( 'admin_print_footer_scripts-press-this.php' );

	/** This action is documented in wp-admin/admin-footer.php */
	do_action( 'admin_print_footer_scripts' );

	/** This action is documented in wp-admin/admin-footer.php */
	do_action( 'admin_footer-press-this.php' );
	?>
</body>
</html>
<?php
		die();
	}
}
