<?php
/**
 * Customize API: WP_Customize_Header_Image_Setting class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 4.4.0
 */

/**
 * A setting that is used to filter a value, but will not save the results.
 *
 * Results should be properly handled using another setting or callback.
 *
 * @since 3.4.0
 *
 * @see WP_Customize_Setting
 *
 * @phpstan-type Header_Image_Data array{
 *     attachment_id?: int,
 *     url?: string,
 *     thumbnail_url?: string,
 *     timestamp?: int,
 *     width?: int,
 *     height?: int,
 *     alt_text?: string,
 *     attachment_parent?: int,
 * }
 */
final class WP_Customize_Header_Image_Setting extends WP_Customize_Setting {
	public $id = 'header_image_data';

	/**
	 * @since 3.4.0
	 *
	 * @global Custom_Image_Header $custom_image_header
	 *
	 * @param $value
	 */
	public function update( $value ) {
		global $custom_image_header;

		// If _custom_header_background_just_in_time() fails to initialize $custom_image_header when not is_admin().
		if ( empty( $custom_image_header ) ) {
			require_once( ABSPATH . 'wp-admin/custom-header.php' );
			$args                   = get_theme_support( 'custom-header' );
			$admin_head_callback    = isset( $args[0]['admin-head-callback'] ) ? $args[0]['admin-head-callback'] : null;
			$admin_preview_callback = isset( $args[0]['admin-preview-callback'] ) ? $args[0]['admin-preview-callback'] : null;
			$custom_image_header    = new Custom_Image_Header( $admin_head_callback, $admin_preview_callback );
		}

		// If the value doesn't exist (removed or random),
		// use the header_image value.
		if ( ! $value ) {
			$value = $this->manager->get_setting( 'header_image' )->post_value();
		}

		if ( is_array( $value ) && isset( $value['choice'] ) ) {
			$custom_image_header->set_header_image( $value['choice'] );
		} else {
			$custom_image_header->set_header_image( $value );
		}
	}

	/**
	 * Sanitizes a header value.
	 *
	 * The value is expected to be one of the following:
	 *
	 * - An array of header image data, with the keys `attachment_id`, `url`, `thumbnail_url`, `timestamp`, `width`,
	 *   `height`, `alt_text`, and `attachment_parent`, as supplied by {@see get_uploaded_header_images()}. Any other
	 *   key is discarded.
	 * - An array with a `choice` key, being the legacy format in which any of the other accepted values is nested.
	 * - The string `remove-header`, `random-default-image`, or `random-uploaded-image`.
	 * - A string corresponding to one of the keys for the array returned by {@see get_uploaded_header_images()}, or
	 *   one of the keys for the array passed into {@see register_default_headers()}.
	 *
	 * @since 7.1.1
	 *
	 * @see WP_Customize_Header_Image_Setting::update()
	 * @see Custom_Image_Header::set_header_image()
	 *
	 * @param mixed $value Value to sanitize.
	 * @return array|string|WP_Error|null Sanitized value, or `null`/`WP_Error` if invalid. The array holds
	 *                                    the header image data, or that data nested under a `choice` key,
	 *                                    before the `customize_sanitize_header_image_data` filter, which
	 *                                    may return anything, is applied to it.
	 *
	 * @phpstan-return array<mixed, mixed>|string|WP_Error|null
	 */
	public function sanitize( $value ) {
		/*
		 * The update() method unwraps the legacy `choice` format before handing the value off to
		 * Custom_Image_Header::set_header_image(), so the nested value is what must be sanitized.
		 */
		if ( is_array( $value ) && isset( $value['choice'] ) ) {
			$choice = $this->sanitize_choice( $value['choice'] );
			if ( is_null( $choice ) || is_wp_error( $choice ) ) {
				return $choice;
			}
			$value = array( 'choice' => $choice );
		} else {
			$value = $this->sanitize_choice( $value );
			if ( is_null( $value ) || is_wp_error( $value ) ) {
				return $value;
			}
		}

		return parent::sanitize( $value );
	}

	/**
	 * Sanitizes a header image choice.
	 *
	 * This is the value which is ultimately passed to {@see Custom_Image_Header::set_header_image()}, whether
	 * supplied at the top level of the setting value or nested under its legacy `choice` key.
	 *
	 * @since 7.1.1
	 *
	 * @param mixed $value Value to sanitize.
	 * @return array|string|WP_Error|null Sanitized value, or `null`/`WP_Error` if invalid.
	 *
	 * @phpstan-return Header_Image_Data|string|WP_Error|null
	 */
	private function sanitize_choice( $value ) {
		// Custom_Image_Header::set_header_image() accepts an object in place of an array.
		if ( is_object( $value ) ) {
			$value = (array) $value;
		}

		if ( is_string( $value ) ) {
			return sanitize_text_field( $value );
		}

		if ( ! is_array( $value ) ) {
			return null;
		}

		/*
		 * The sanitized value is assembled member by member rather than filtered down from the
		 * supplied one, so that nothing but the members below can end up in it.
		 */
		$sanitized = array();

		if ( isset( $value['attachment_id'] ) ) {
			if ( ! is_scalar( $value['attachment_id'] ) ) {
				return null;
			}
			$attachment_id = absint( $value['attachment_id'] );

			/*
			 * A supplied attachment must be an existing image, since its ID is written to postmeta and its
			 * data displayed. Note that an ID of zero must be skipped rather than looked up, as
			 * get_post_mime_type() falls back to the global post when passed an empty value.
			 */
			if ( $attachment_id > 0 ) {
				$mime_type = get_post_mime_type( $attachment_id );
				if ( ! is_string( $mime_type ) || ! str_starts_with( $mime_type, 'image/' ) ) {
					return null;
				}
			}

			$sanitized['attachment_id'] = $attachment_id;
		}

		if ( isset( $value['url'] ) ) {
			if ( ! is_string( $value['url'] ) ) {
				return null;
			}
			$sanitized['url'] = esc_url_raw( $value['url'] );
			if ( '' === $sanitized['url'] ) {
				return new WP_Error( 'invalid_url', __( 'Invalid URL.' ) );
			}
		}

		if ( isset( $value['thumbnail_url'] ) ) {
			if ( ! is_string( $value['thumbnail_url'] ) ) {
				return null;
			}
			$sanitized['thumbnail_url'] = esc_url_raw( $value['thumbnail_url'] );
			if ( '' === $sanitized['thumbnail_url'] ) {
				return new WP_Error( 'invalid_url', __( 'Invalid URL.' ) );
			}
		}

		if ( isset( $value['timestamp'] ) ) {
			if ( ! is_scalar( $value['timestamp'] ) ) {
				return null;
			}
			$sanitized['timestamp'] = absint( $value['timestamp'] );
		}

		if ( isset( $value['width'] ) ) {
			if ( ! is_scalar( $value['width'] ) ) {
				return null;
			}
			$sanitized['width'] = absint( $value['width'] );
		}

		if ( isset( $value['height'] ) ) {
			if ( ! is_scalar( $value['height'] ) ) {
				return null;
			}
			$sanitized['height'] = absint( $value['height'] );
		}

		if ( isset( $value['alt_text'] ) ) {
			if ( ! is_string( $value['alt_text'] ) ) {
				return null;
			}
			$sanitized['alt_text'] = sanitize_text_field( $value['alt_text'] );
		}

		if ( isset( $value['attachment_parent'] ) ) {
			if ( ! is_scalar( $value['attachment_parent'] ) ) {
				return null;
			}
			$sanitized['attachment_parent'] = absint( $value['attachment_parent'] );
		}

		return $sanitized;
	}
}
