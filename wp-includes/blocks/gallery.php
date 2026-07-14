<?php
/**
 * Server-side rendering of the `core/gallery` block.
 *
 * @package WordPress
 */

/**
 * Handles backwards compatibility for Gallery Blocks,
 * whose images feature a `data-id` attribute.
 *
 * Now that the Gallery Block contains inner Image Blocks,
 * we add a custom `data-id` attribute before rendering the gallery
 * so that the Image Block can pick it up in its render_callback.
 *
 * @since 5.9.0
 *
 * @param array $parsed_block The block being rendered.
 * @return array The migrated block object.
 */
function block_core_gallery_data_id_backcompatibility( $parsed_block ) {
	if ( 'core/gallery' === $parsed_block['blockName'] ) {
		foreach ( $parsed_block['innerBlocks'] as $key => $inner_block ) {
			if ( 'core/image' === $inner_block['blockName'] ) {
				if ( ! isset( $parsed_block['innerBlocks'][ $key ]['attrs']['data-id'] ) && isset( $inner_block['attrs']['id'] ) ) {
					$parsed_block['innerBlocks'][ $key ]['attrs']['data-id'] = esc_attr( $inner_block['attrs']['id'] );
				}
			}
		}
	}

	return $parsed_block;
}

add_filter( 'render_block_data', 'block_core_gallery_data_id_backcompatibility' );

/**
 * Adds a unique ID to the gallery block context.
 *
 * @since 7.0.0
 *
 * @param array $context      Default context.
 * @param array $parsed_block Block being rendered, filtered by render_block_data.
 * @return array Filtered context.
 */
function block_core_gallery_render_context( $context, $parsed_block ) {
	if ( 'core/gallery' === $parsed_block['blockName'] ) {
		$context['galleryId'] = uniqid();
	}
	return $context;
}

add_filter( 'render_block_context', 'block_core_gallery_render_context', 10, 2 );

/**
 * Returns the column gap value used for Gallery image width calculations.
 *
 * @since 7.1.0
 *
 * @param string|array|null $gap          Gallery block gap value.
 * @param string            $fallback_gap Fallback gap value.
 * @return string Gallery column gap value.
 */
function block_core_gallery_get_column_gap_value( $gap, $fallback_gap ) {
	if ( is_array( $gap ) ) {
		$gap = $gap['left'] ?? $fallback_gap;
	}

	// Make sure $gap is a string to avoid PHP 8.1 deprecation error in preg_match() when the value is null.
	$gap = is_string( $gap ) ? $gap : '';

	// Skip if gap value contains unsupported characters.
	// Regex for CSS value borrowed from `safecss_filter_attr`, and used here
	// because we only want to match against the value, not the CSS attribute.
	$gap = $gap && preg_match( '%[\\\(&=}]|/\*%', $gap ) ? null : $gap;

	// Get spacing CSS variable from preset value if provided.
	if ( is_string( $gap ) && str_contains( $gap, 'var:preset|spacing|' ) ) {
		$index_to_splice = strrpos( $gap, '|' ) + 1;
		$slug            = _wp_to_kebab_case( substr( $gap, $index_to_splice ) );
		$gap             = "var(--wp--preset--spacing--$slug)";
	}

	$gap_column = ( null !== $gap && '' !== $gap ) ? $gap : $fallback_gap;

	// The unstable gallery gap calculation requires a real value (such as `0px`) and not `0`.
	return '0' === $gap_column ? '0px' : $gap_column;
}

/**
 * Resolves a Gallery block's `dynamicContent` to an ordered list of image
 * attachment IDs.
 *
 * The `source` key is the dispatch discriminator and `args` holds the source's
 * parameters. This `{ source, args }` shape mirrors the Block Bindings metadata
 * shape so dynamic mode can migrate to an `innerBlocks` binding with minimal
 * change. `core/attached-media` is a context-relative anchor (the post the gallery is
 * rendered within); future sources translate their REST-named `args` (`author`,
 * `categories`, `after`/`before`, `media_type`, etc.) into `WP_Query` arguments
 * here.
 *
 * @since 7.0.0
 *
 * @param array    $source The gallery's `dynamicContent` attribute.
 * @param WP_Block $block  The gallery block instance being rendered.
 * @return int[] Ordered list of image attachment IDs.
 */
function block_core_gallery_resolve_dynamic_source( $source, $block ) {
	if ( ! is_array( $source ) ) {
		return array();
	}

	$source_name = $source['source'] ?? null;
	$args        = isset( $source['args'] ) && is_array( $source['args'] ) ? $source['args'] : array();

	switch ( $source_name ) {
		case 'core/attached-media':
			// Prefer the post supplied via block context, falling back to the post
			// being rendered. The fallback is what lets a post-bound template (e.g.
			// `single`/`page`) resolve against the actual post at render time even
			// though the editor has no concrete post to preview — the editor gates
			// the dynamic-mode UI on that same context (see `use-dynamic-gallery.js`).
			$post_id = $block->context['postId'] ?? get_the_ID();
			if ( ! $post_id ) {
				return array();
			}

			// Map the camelCase `args` (block-attribute convention) to WP_Query
			// names, defaulting to the same order as the editor preview (see
			// `dynamic-source.js`). Only REST-supported orderby values are
			// allowed; `menu_order` is intentionally unsupported (it isn't a
			// valid media REST `orderby`).
			$orderby = $args['orderBy'] ?? 'date';
			if ( ! in_array( $orderby, array( 'date', 'title' ), true ) ) {
				$orderby = 'date';
			}
			$order = strtoupper( $args['order'] ?? 'desc' ) === 'ASC' ? 'ASC' : 'DESC';

			// Bound the number of resolved images until the gallery supports
			// pagination. Kept in sync with the editor query's `per_page` cap; a
			// case-insensitive grep for `max_images` finds both this and
			// `MAX_IMAGES` in `dynamic-source.js`.
			$max_images = 100;

			$query = new WP_Query(
				array(
					'post_parent'    => $post_id,
					'post_type'      => 'attachment',
					'post_status'    => 'inherit',
					'post_mime_type' => 'image',
					'orderby'        => $orderby,
					'order'          => $order,
					'posts_per_page' => $max_images,
					'fields'         => 'ids',
					'no_found_rows'  => true,
				)
			);

			return array_map( 'intval', $query->posts );
	}

	// Unknown or not-yet-implemented source type.
	return array();
}

/**
 * Builds the link-related image block attributes for a dynamically rendered
 * gallery image, mapping the gallery-wide `linkTo` setting onto a single image.
 *
 * Mirrors the editor's `getHrefAndDestination()` (see `gallery/utils.js`).
 *
 * @since 7.0.0
 *
 * @param int   $attachment_id The image attachment ID.
 * @param array $attributes    The gallery block attributes.
 * @return array Partial image block attributes (`href`, `linkDestination`,
 *               `linkTarget`, `rel`, `lightbox`).
 */
function block_core_gallery_dynamic_image_link_attributes( $attachment_id, $attributes ) {
	$link_to = $attributes['linkTo'] ?? 'none';
	$attrs   = array();

	switch ( $link_to ) {
		// Gutenberg uses 'media'/'attachment'; WP Core uses 'file'/'post'.
		case 'media':
		case 'file':
			$attrs['href']            = wp_get_attachment_url( $attachment_id );
			$attrs['linkDestination'] = 'media';
			break;
		case 'attachment':
		case 'post':
			$attrs['href']            = get_attachment_link( $attachment_id );
			$attrs['linkDestination'] = 'attachment';
			break;
		case 'lightbox':
			$attrs['linkDestination'] = 'none';
			$attrs['lightbox']        = array( 'enabled' => true );
			break;
	}

	if ( ! empty( $attrs['href'] ) && '_blank' === ( $attributes['linkTarget'] ?? '' ) ) {
		$attrs['linkTarget'] = '_blank';
		$attrs['rel']        = 'noopener';
	}

	return $attrs;
}

/**
 * Renders a single `core/image` block for a Gallery block running in dynamic
 * mode, applying the gallery-wide settings that affect how an image renders.
 *
 * The image markup is generated here (via `wp_get_attachment_image()`) and
 * rendered through a real `core/image` block instance so that the image block's
 * own render callback and lightbox behavior run, and so the gallery's existing
 * lightbox/interactivity post-processing can pick it up.
 *
 * @since 7.0.0
 *
 * @param int   $attachment_id The image attachment ID.
 * @param array $attributes    The gallery block attributes.
 * @param array $context       Context to expose to the inner image block.
 * @return string The rendered image block HTML, or an empty string on failure.
 */
function block_core_gallery_render_dynamic_image( $attachment_id, $attributes, $context ) {
	$size_slug    = $attributes['sizeSlug'] ?? 'large';
	$aspect_ratio = $attributes['aspectRatio'] ?? 'auto';

	$img_attr = array( 'class' => 'wp-image-' . $attachment_id );
	if ( $aspect_ratio && 'auto' !== $aspect_ratio ) {
		// Run the aspect ratio through the same sanitization used for every other
		// block inline style, so an unsafe value can't break out of the style
		// attribute or inject additional markup.
		$img_attr['style'] = safecss_filter_attr(
			sprintf( 'aspect-ratio:%s;object-fit:cover;', $aspect_ratio )
		);
	}

	$image_markup = wp_get_attachment_image( $attachment_id, $size_slug, false, $img_attr );
	if ( ! $image_markup ) {
		return '';
	}

	$image_attributes = array_merge(
		array(
			'id'       => $attachment_id,
			'data-id'  => (string) $attachment_id,
			'sizeSlug' => $size_slug,
		),
		block_core_gallery_dynamic_image_link_attributes( $attachment_id, $attributes )
	);

	if ( $aspect_ratio && 'auto' !== $aspect_ratio ) {
		$image_attributes['aspectRatio'] = $aspect_ratio;
		$image_attributes['scale']       = 'cover';
	}

	// Wrap in a link when the gallery links images somewhere.
	if ( ! empty( $image_attributes['href'] ) ) {
		$image_markup = sprintf(
			'<a href="%1$s"%2$s%3$s>%4$s</a>',
			esc_url( $image_attributes['href'] ),
			isset( $image_attributes['linkTarget'] ) ? ' target="' . esc_attr( $image_attributes['linkTarget'] ) . '"' : '',
			isset( $image_attributes['rel'] ) ? ' rel="' . esc_attr( $image_attributes['rel'] ) . '"' : '',
			$image_markup
		);
	}

	// Use the raw caption (`post_excerpt`) so the frontend mirrors the editor
	// preview, which builds the caption from the REST `caption.raw` value. Gap:
	// the REST API exposes no caption run through `wp_get_attachment_caption`, so
	// that filter isn't applied here either.
	$attachment = get_post( $attachment_id );
	$caption    = $attachment ? $attachment->post_excerpt : '';
	if ( '' !== $caption ) {
		$image_markup .= sprintf(
			'<figcaption class="wp-element-caption">%s</figcaption>',
			wp_kses_post( $caption )
		);
	}

	$figure = sprintf(
		'<figure class="wp-block-image size-%1$s">%2$s</figure>',
		esc_attr( $size_slug ),
		$image_markup
	);

	$image_block = array(
		'blockName'    => 'core/image',
		'attrs'        => $image_attributes,
		'innerBlocks'  => array(),
		'innerHTML'    => $figure,
		'innerContent' => array( $figure ),
	);

	return ( new WP_Block( $image_block, $context ) )->render();
}

/**
 * Renders the `core/gallery` block on the server.
 *
 * @since 6.0.0
 *
 * @param array  $attributes Attributes of the block being rendered.
 * @param string $content    Content of the block being rendered.
 * @param array  $block      The block instance being rendered.
 * @return string The content of the block being rendered.
 */
function block_core_gallery_render( $attributes, $content, $block ) {
	static $global_styles = null;

	// In dynamic mode the gallery's images are resolved at render time instead of
	// being authored as inner blocks, so `save.js` persists at most the
	// gallery-level caption — a bare `<figcaption>`, or nothing when there is no
	// caption. Resolve the configured source to a list of attachments, render an
	// image block for each, and build the gallery `<figure>` wrapper from scratch.
	// The gap/randomOrder/lightbox post-processing below then runs over the
	// constructed markup unchanged.
	if ( ! empty( $attributes['dynamicContent'] ) ) {
		$attachment_ids = block_core_gallery_resolve_dynamic_source( $attributes['dynamicContent'], $block );

		// Nothing resolved — no attachments, or an unrecognized source. Render
		// nothing rather than an empty gallery wrapper; a saved caption is
		// meaningless without images, so it is intentionally dropped too.
		if ( empty( $attachment_ids ) ) {
			return '';
		}

		// The source query only fetched IDs (`fields => ids`), which skips
		// WP_Query's cache priming. Each image rendered below reads the
		// attachment post and its meta (via `wp_get_attachment_image()`,
		// `get_post()`, etc.), so warm the post and meta caches in a single pair
		// of queries up front instead of paying ~two queries per attachment.
		// Term cache is left cold: the render path doesn't read attachment terms.
		if ( count( $attachment_ids ) > 1 ) {
			_prime_post_caches( $attachment_ids, false, true );
		}

		// Expose the gallery's provided context (plus galleryId/postId/postType)
		// to each image block, since these images are rendered outside the
		// gallery's real inner-block tree.
		$image_context = array_merge(
			is_array( $block->context ) ? $block->context : array(),
			array(
				'allowResize'          => $attributes['allowResize'] ?? false,
				'imageCrop'            => $attributes['imageCrop'] ?? true,
				'fixedHeight'          => $attributes['fixedHeight'] ?? true,
				'navigationButtonType' => $attributes['navigationButtonType'] ?? 'icon',
			)
		);

		$images_markup = '';
		foreach ( $attachment_ids as $attachment_id ) {
			$images_markup .= block_core_gallery_render_dynamic_image( $attachment_id, $attributes, $image_context );
		}

		// Build the wrapper rather than parsing/splicing saved markup.
		// `get_block_wrapper_attributes()` supplies the block-support
		// classes/styles (align, color, border, spacing, anchor id); the layout
		// render filter adds the flex layout classes downstream — the same way a
		// static gallery's wrapper is composed (`useBlockProps.save()` plus that
		// filter). Only the gallery-specific classes are added explicitly, and
		// they mirror `save.js` (kept in sync deliberately — see that file).
		$gallery_classes  = 'wp-block-gallery has-nested-images';
		$gallery_classes .= isset( $attributes['columns'] )
			? ' columns-' . (int) $attributes['columns']
			: ' columns-default';
		if ( $attributes['imageCrop'] ?? true ) {
			$gallery_classes .= ' is-cropped';
		}
		$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $gallery_classes ) );

		// In dynamic mode `save.js` persists only the gallery-level caption, so
		// `$content` is the saved `<figcaption>` (or empty). Append it after the
		// resolved images — matching the static gallery's `{images}{caption}`
		// order — without parsing it.
		$content = sprintf( '<figure %s>%s%s</figure>', $wrapper_attributes, $images_markup, $content );
	}

	// Adds a style tag for the --wp--style--unstable-gallery-gap var.
	// The Gallery block needs to recalculate Image block width based on
	// the current gap setting in order to maintain the number of flex columns
	// so a css var is added to allow this.

	$style_attr = is_array( $attributes['style'] ?? null )
		? $attributes['style']
		: array();
	if (
		defined( 'IS_GUTENBERG_PLUGIN' ) &&
		IS_GUTENBERG_PLUGIN &&
		function_exists( 'gutenberg_resolve_style_state_aliases' )
	) {
		$style_attr = gutenberg_resolve_style_state_aliases( $style_attr, 'core/gallery' );
	}

	$unique_gallery_classname = wp_unique_id( 'wp-block-gallery-' );
	$processed_content        = new WP_HTML_Tag_Processor( $content );
	$processed_content->next_tag();
	$processed_content->add_class( $unique_gallery_classname );

	// --gallery-block--gutter-size is deprecated. --wp--style--gallery-gap-default should be used by themes that want to set a default
	// gap on the gallery.
	$fallback_gap = 'var( --wp--style--gallery-gap-default, var( --gallery-block--gutter-size, var( --wp--style--block-gap, 0.5em ) ) )';

	if ( null === $global_styles ) {
		$global_styles = function_exists( 'wp_get_global_styles' ) ? wp_get_global_styles() : array();
	}

	$global_gallery_styles = $global_styles['blocks']['core/gallery'] ?? array();
	$global_gallery_gap    = $global_gallery_styles['spacing']['blockGap'] ?? $fallback_gap;
	$has_block_gap         = is_array( $style_attr['spacing'] ?? null ) && array_key_exists( 'blockGap', $style_attr['spacing'] );
	// Prefer the block's own gap value, then Gallery global styles. Missing
	// values fall back to the Gallery blockGap default.
	$block_gap  = $has_block_gap
		? $style_attr['spacing']['blockGap']
		: $global_gallery_gap;
	$gap_column = block_core_gallery_get_column_gap_value( $block_gap, $fallback_gap );

	// Set the CSS variable to the column value for Gallery's flex width calculations.
	$gallery_styles = array(
		array(
			'selector'     => ".wp-block-gallery.{$unique_gallery_classname}",
			'declarations' => array(
				'--wp--style--unstable-gallery-gap' => $gap_column,
			),
		),
	);

	$global_settings          = wp_get_global_settings();
	$viewport_settings        = $global_settings['viewport'] ?? null;
	$responsive_media_queries = array();
	foreach ( array( 'WP_Theme_JSON_Gutenberg', 'WP_Theme_JSON' ) as $theme_json_class_name ) {
		if ( method_exists( $theme_json_class_name, 'get_viewport_media_queries' ) ) {
			$responsive_media_queries = $theme_json_class_name::get_viewport_media_queries( $viewport_settings );
			break;
		}
	}

	foreach ( $responsive_media_queries as $breakpoint => $media_query ) {
		$viewport_style                = $style_attr[ $breakpoint ] ?? null;
		$has_viewport_block_gap        = is_array( $viewport_style ) &&
			is_array( $viewport_style['spacing'] ?? null ) &&
			array_key_exists( 'blockGap', $viewport_style['spacing'] );
		$has_global_viewport_block_gap = is_array( $global_gallery_styles[ $breakpoint ]['spacing'] ?? null ) &&
			array_key_exists( 'blockGap', $global_gallery_styles[ $breakpoint ]['spacing'] );

		// Viewport-specific block values win. Gallery global viewport values
		// only apply when the block has no base gap, so they do not override an instance value.
		if ( $has_viewport_block_gap ) {
			$viewport_gap = $viewport_style['spacing']['blockGap'];
		} elseif ( ! $has_block_gap && $has_global_viewport_block_gap ) {
			$viewport_gap = $global_gallery_styles[ $breakpoint ]['spacing']['blockGap'];
		} else {
			continue;
		}

		if ( null === $viewport_gap ) {
			continue;
		}

		$gallery_styles[] = array(
			'selector'     => ".wp-block-gallery.{$unique_gallery_classname}",
			'declarations' => array(
				'--wp--style--unstable-gallery-gap' => block_core_gallery_get_column_gap_value(
					$viewport_gap,
					$fallback_gap
				),
			),
			'rules_group'  => $media_query,
		);
	}

	wp_style_engine_get_stylesheet_from_css_rules(
		$gallery_styles,
		array( 'context' => 'block-supports' )
	);

	// The WP_HTML_Tag_Processor class calls get_updated_html() internally
	// when the instance is treated as a string, but here we explicitly
	// convert it to a string.
	$updated_content = $processed_content->get_updated_html();

	/*
	 * Randomize the order of image blocks. Ideally we should shuffle
	 * the `$parsed_block['innerBlocks']` via the `render_block_data` hook.
	 * However, this hook doesn't apply inner block updates when blocks are
	 * nested.
	 * @todo In the future, if this hook supports updating innerBlocks in
	 * nested blocks, it should be refactored.
	 *
	 * @see: https://github.com/WordPress/gutenberg/pull/58733
	 */
	if ( ! empty( $attributes['randomOrder'] ) ) {
		// This pattern matches figure elements with the `wp-block-image`
		// class to avoid the gallery's wrapping `figure` element and
		// extract images only.
		$pattern = '/<figure[^>]*\bwp-block-image\b[^>]*>.*?<\/figure>/s';

		preg_match_all( $pattern, $updated_content, $matches );
		if ( $matches ) {
			$image_blocks = $matches[0];
			shuffle( $image_blocks );

			$i               = 0;
			$updated_content = preg_replace_callback(
				$pattern,
				static function () use ( $image_blocks, &$i ) {
					return $image_blocks[ $i++ ];
				},
				$updated_content
			);
		}
	}

	// Gets all image IDs from the state that match this gallery's ID.
	$state      = wp_interactivity_state( 'core/image' );
	$gallery_id = $block->context['galleryId'] ?? null;
	$image_ids  = array();

	// Extracts image IDs from state metadata that match the current gallery ID.
	if ( isset( $gallery_id ) && isset( $state['metadata'] ) ) {
		foreach ( $state['metadata'] as $image_id => $metadata ) {
			if ( isset( $metadata['galleryId'] ) && $metadata['galleryId'] === $gallery_id ) {
				$image_ids[] = $image_id;
			}
		}
	}

	// If there are image IDs associated with this gallery, set interactivity
	// attributes and order metadata for lightbox navigation.
	if ( ! empty( $image_ids ) ) {
		$total          = count( $image_ids );
		$lightbox_index = 0;
		$processor      = new WP_HTML_Tag_Processor( $updated_content );
		$processor->next_tag();
		$processor->set_attribute( 'data-wp-interactive', 'core/gallery' );
		$processor->set_attribute(
			'data-wp-context',
			wp_json_encode(
				array( 'galleryId' => $gallery_id ),
				JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
			)
		);
		while ( $processor->next_tag( 'figure' ) ) {
			$wp_key = $processor->get_attribute( 'data-wp-key' );
			if ( $wp_key && isset( $state['metadata'][ $wp_key ] ) ) {
				$alt = $state['metadata'][ $wp_key ]['alt'];
				wp_interactivity_state(
					'core/image',
					array(
						'metadata' => array(
							$wp_key => array(
								'customAriaLabel'        => empty( $alt )
									/* translators: %1$s: current image index, %2$s: total number of images */
									? sprintf( __( 'Enlarged image %1$s of %2$s' ), $lightbox_index + 1, $total )
									/* translators: %1$s: current image index, %2$s: total number of images, %3$s: Image alt text */
									: sprintf( __( 'Enlarged image %1$s of %2$s: %3$s' ), $lightbox_index + 1, $total, $alt ),
								/* translators: %1$s: current image index, %2$s: total number of images */
								'triggerButtonAriaLabel' => sprintf( __( 'Enlarge %1$s of %2$s' ), $lightbox_index + 1, $total ),
								'order'                  => $lightbox_index,
							),
						),
					)
				);
				++$lightbox_index;
			}
		}
		return $processor->get_updated_html();
	}

	return $updated_content;
}

/**
 * Registers the `core/gallery` block on server.
 *
 * @since 5.9.0
 */
function register_block_core_gallery() {
	register_block_type_from_metadata(
		__DIR__ . '/gallery',
		array(
			'render_callback' => 'block_core_gallery_render',
		)
	);
}

add_action( 'init', 'register_block_core_gallery' );
