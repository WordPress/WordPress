<?php
/**
 * Adds media-related experimental functionality.
 *
 * @package gutenberg
 */

/**
 * Returns a list of all available image sizes.
 *
 * @return array Existing image sizes.
 */
function gutenberg_get_all_image_sizes(): array {
	$sizes = wp_get_registered_image_subsizes();

	foreach ( $sizes as $name => &$size ) {
		$size['height'] = (int) $size['height'];
		$size['width']  = (int) $size['width'];
		$size['name']   = $name;
	}
	unset( $size );

	return $sizes;
}

/**
 * Returns the default output format mapping for the supported image formats.
 *
 * @return array<string,string> Map of input formats to output formats.
 */
function gutenberg_get_default_image_output_formats() {
	$input_formats = array(
		'image/jpeg',
		'image/png',
		'image/gif',
		'image/webp',
		'image/avif',
		'image/heic',
	);

	$output_formats = array();

	foreach ( $input_formats as $mime_type ) {
		/** This filter is documented in wp-includes/media.php */
		$output_formats = apply_filters(
			'image_editor_output_format', // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			$output_formats,
			'',
			$mime_type
		);
	}

	return $output_formats;
}

/**
 * Filters the REST API root index data to add custom settings.
 *
 * @param WP_REST_Response $response Response data.
 */
function gutenberg_media_processing_filter_rest_index( WP_REST_Response $response ) {
	/** This filter is documented in wp-admin/includes/images.php */
	$image_size_threshold = (int) apply_filters( 'big_image_size_threshold', 2560, array( 0, 0 ), '', 0 ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

	$default_image_output_formats = gutenberg_get_default_image_output_formats();

	/** This filter is documented in wp-includes/class-wp-image-editor-imagick.php */
	$jpeg_interlaced = (bool) apply_filters( 'image_save_progressive', false, 'image/jpeg' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
	/** This filter is documented in wp-includes/class-wp-image-editor-imagick.php */
	$png_interlaced = (bool) apply_filters( 'image_save_progressive', false, 'image/png' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
	/** This filter is documented in wp-includes/class-wp-image-editor-imagick.php */
	$gif_interlaced = (bool) apply_filters( 'image_save_progressive', false, 'image/gif' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

	if ( current_user_can( 'upload_files' ) ) {
		$response->data['image_sizes']          = gutenberg_get_all_image_sizes();
		$response->data['image_size_threshold'] = $image_size_threshold;
		$response->data['image_output_formats'] = (object) $default_image_output_formats;
		$response->data['jpeg_interlaced']      = $jpeg_interlaced;
		$response->data['png_interlaced']       = $png_interlaced;
		$response->data['gif_interlaced']       = $gif_interlaced;
	}

	return $response;
}

add_filter( 'rest_index', 'gutenberg_media_processing_filter_rest_index' );


/**
 * Overrides the REST controller for the attachment post type.
 *
 * @param array  $args      Array of arguments for registering a post type.
 *                          See the register_post_type() function for accepted arguments.
 * @param string $post_type Post type key.
 */
function gutenberg_filter_attachment_post_type_args( array $args, string $post_type ): array {
	if ( 'attachment' === $post_type ) {
		require_once __DIR__ . '/class-gutenberg-rest-attachments-controller.php';

		$args['rest_controller_class'] = Gutenberg_REST_Attachments_Controller::class;
	}

	return $args;
}

add_filter( 'register_post_type_args', 'gutenberg_filter_attachment_post_type_args', 10, 2 );


/**
 * Registers additional REST fields for attachments.
 */
function gutenberg_media_processing_register_rest_fields(): void {
	register_rest_field(
		'attachment',
		'filename',
		array(
			'schema'       => array(
				'description' => __( 'Original attachment file name', 'gutenberg' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
			),
			'get_callback' => 'gutenberg_rest_get_attachment_filename',
		)
	);

	register_rest_field(
		'attachment',
		'filesize',
		array(
			'schema'       => array(
				'description' => __( 'Attachment file size', 'gutenberg' ),
				'type'        => 'number',
				'context'     => array( 'view', 'edit' ),
			),
			'get_callback' => 'gutenberg_rest_get_attachment_filesize',
		)
	);
}

add_action( 'rest_api_init', 'gutenberg_media_processing_register_rest_fields' );

/**
 * Returns the attachment's original file name.
 *
 * @param array $post Post data.
 * @return string|null Attachment file name.
 */
function gutenberg_rest_get_attachment_filename( array $post ): ?string {
	$path = wp_get_original_image_path( $post['id'] );

	if ( $path ) {
		return basename( $path );
	}

	$path = get_attached_file( $post['id'] );

	if ( $path ) {
		return basename( $path );
	}

	return null;
}

/**
 * Returns the attachment's file size in bytes.
 *
 * @param array $post Post data.
 * @return int|null Attachment file size.
 */
function gutenberg_rest_get_attachment_filesize( array $post ): ?int {
	$attachment_id = $post['id'];

	$meta = wp_get_attachment_metadata( $attachment_id );

	if ( isset( $meta['filesize'] ) ) {
		return $meta['filesize'];
	}

	$original_path = wp_get_original_image_path( $attachment_id );
	$attached_file = $original_path ? $original_path : get_attached_file( $attachment_id );

	if ( is_string( $attached_file ) && file_exists( $attached_file ) ) {
		return wp_filesize( $attached_file );
	}

	return null;
}

/**
 * Filters the list of rewrite rules formatted for output to an .htaccess file.
 *
 * Adds support for serving wasm-vips locally.
 *
 * @param string $rules mod_rewrite Rewrite rules formatted for .htaccess.
 * @return string Filtered rewrite rules.
 */
function gutenberg_filter_mod_rewrite_rules( string $rules ): string {
	$rules .= "\n# BEGIN Gutenberg client-side media processing experiment\n" .
				"AddType application/wasm wasm\n" .
				"# END Gutenberg client-side media processing experiment\n";

	return $rules;
}

add_filter( 'mod_rewrite_rules', 'gutenberg_filter_mod_rewrite_rules' );

/**
 * Enables cross-origin isolation in the block editor.
 *
 * Required for enabling SharedArrayBuffer for WebAssembly-based
 * media processing in the editor.
 *
 * @link https://web.dev/coop-coep/
 */
function gutenberg_set_up_cross_origin_isolation() {
	$screen = get_current_screen();

	if ( ! $screen ) {
		return;
	}

	if ( ! $screen->is_block_editor() && 'site-editor' !== $screen->id && ! ( 'widgets' === $screen->id && wp_use_widgets_block_editor() ) ) {
		return;
	}

	$user_id = get_current_user_id();
	if ( ! $user_id ) {
		return;
	}

	// Cross-origin isolation is not needed if users can't upload files anyway.
	if ( ! user_can( $user_id, 'upload_files' ) ) {
		return;
	}

	gutenberg_start_cross_origin_isolation_output_buffer();
}

add_action( 'load-post.php', 'gutenberg_set_up_cross_origin_isolation' );
add_action( 'load-post-new.php', 'gutenberg_set_up_cross_origin_isolation' );
add_action( 'load-site-editor.php', 'gutenberg_set_up_cross_origin_isolation' );
add_action( 'load-widgets.php', 'gutenberg_set_up_cross_origin_isolation' );

/**
 * Sends headers for cross-origin isolation.
 *
 * Uses an output buffer to add crossorigin="anonymous" where needed.
 *
 * @link https://web.dev/coop-coep/
 *
 * @global bool $is_safari
 */
function gutenberg_start_cross_origin_isolation_output_buffer(): void {
	global $is_safari;

	$coep = $is_safari ? 'require-corp' : 'credentialless';

	ob_start(
		function ( string $output ) use ( $coep ): string {
			header( 'Cross-Origin-Opener-Policy: same-origin' );
			header( "Cross-Origin-Embedder-Policy: $coep" );

			return gutenberg_add_crossorigin_attributes( $output );
		}
	);
}

/**
 * Adds crossorigin="anonymous" to relevant tags in the given HTML string.
 *
 * @param string $html HTML input.
 *
 * @return string Modified HTML.
 */
function gutenberg_add_crossorigin_attributes( string $html ): string {
	$site_url = site_url();

	$processor = new WP_HTML_Tag_Processor( $html );

	// See https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/crossorigin.
	$tags = array(
		'AUDIO'  => 'src',
		'IMG'    => 'src',
		'LINK'   => 'href',
		'SCRIPT' => 'src',
		'VIDEO'  => 'src',
		'SOURCE' => 'src',
	);

	$tag_names = array_keys( $tags );

	while ( $processor->next_tag() ) {
		$tag = $processor->get_tag();

		if ( ! in_array( $tag, $tag_names, true ) ) {
			continue;
		}

		if ( 'AUDIO' === $tag || 'VIDEO' === $tag ) {
			$processor->set_bookmark( 'audio-video-parent' );
		}

		$processor->set_bookmark( 'resume' );

		$sought = false;

		$crossorigin = $processor->get_attribute( 'crossorigin' );

		$url = $processor->get_attribute( $tags[ $tag ] );

		if ( is_string( $url ) && ! str_starts_with( $url, $site_url ) && ! str_starts_with( $url, '/' ) && ! is_string( $crossorigin ) ) {
			if ( 'SOURCE' === $tag ) {
				$sought = $processor->seek( 'audio-video-parent' );

				if ( $sought ) {
					$processor->set_attribute( 'crossorigin', 'anonymous' );
				}
			} else {
				$processor->set_attribute( 'crossorigin', 'anonymous' );
			}

			if ( $sought ) {
				$processor->seek( 'resume' );
				$processor->release_bookmark( 'audio-video-parent' );
			}
		}
	}

	return $processor->get_updated_html();
}

/**
 * Overrides templates from wp_print_media_templates with custom ones.
 *
 * Adds `crossorigin` attribute to all tags that
 * could have assets loaded from a different domain.
 */
function gutenberg_override_media_templates(): void {
	remove_action( 'admin_footer', 'wp_print_media_templates' );
	add_action(
		'admin_footer',
		static function (): void {
			ob_start();
			wp_print_media_templates();
			$html = (string) ob_get_clean();

			$tags = array(
				'audio',
				'img',
				'video',
			);

			foreach ( $tags as $tag ) {
				$html = (string) str_replace( "<$tag", "<$tag crossorigin=\"anonymous\"", $html );
			}

			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	);
}

add_action( 'wp_enqueue_media', 'gutenberg_override_media_templates' );
