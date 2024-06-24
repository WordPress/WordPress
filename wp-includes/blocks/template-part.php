<?php
/**
 * Server-side rendering of the `core/template-part` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/template-part` block on the server.
 *
 * @param array $attributes The block attributes.
 *
 * @return string The render.
 */
function render_block_core_template_part( $attributes ) {
	static $seen_ids = array();

	$template_part_id = null;
	$content          = null;
	$area             = WP_TEMPLATE_PART_AREA_UNCATEGORIZED;

	if (
		isset( $attributes['slug'] ) &&
		isset( $attributes['theme'] ) &&
		wp_get_theme()->get_stylesheet() === $attributes['theme']
	) {
		$template_part_id    = $attributes['theme'] . '//' . $attributes['slug'];
		$template_part_query = new WP_Query(
			array(
				'post_type'      => 'wp_template_part',
				'post_status'    => 'publish',
				'post_name__in'  => array( $attributes['slug'] ),
				'tax_query'      => array(
					array(
						'taxonomy' => 'wp_theme',
						'field'    => 'slug',
						'terms'    => $attributes['theme'],
					),
				),
				'posts_per_page' => 1,
				'no_found_rows'  => true,
			)
		);
		$template_part_post  = $template_part_query->have_posts() ? $template_part_query->next_post() : null;
		if ( $template_part_post ) {
			// A published post might already exist if this template part was customized elsewhere
			// or if it's part of a customized template.
			$content    = $template_part_post->post_content;
			$area_terms = get_the_terms( $template_part_post, 'wp_template_part_area' );
			if ( ! is_wp_error( $area_terms ) && false !== $area_terms ) {
				$area = $area_terms[0]->name;
			}
			/**
			 * Fires when a block template part is loaded from a template post stored in the database.
			 *
			 * @since 5.9.0
			 *
			 * @param string  $template_part_id   The requested template part namespaced to the theme.
			 * @param array   $attributes         The block attributes.
			 * @param WP_Post $template_part_post The template part post object.
			 * @param string  $content            The template part content.
			 */
			do_action( 'render_block_core_template_part_post', $template_part_id, $attributes, $template_part_post, $content );
		} else {
			// Else, if the template part was provided by the active theme,
			// render the corresponding file content.
			$parent_theme_folders        = get_block_theme_folders( get_template() );
			$child_theme_folders         = get_block_theme_folders( get_stylesheet() );
			$child_theme_part_file_path  = get_theme_file_path( '/' . $child_theme_folders['wp_template_part'] . '/' . $attributes['slug'] . '.html' );
			$parent_theme_part_file_path = get_theme_file_path( '/' . $parent_theme_folders['wp_template_part'] . '/' . $attributes['slug'] . '.html' );
			$template_part_file_path     = 0 === validate_file( $attributes['slug'] ) && file_exists( $child_theme_part_file_path ) ? $child_theme_part_file_path : $parent_theme_part_file_path;
			if ( 0 === validate_file( $attributes['slug'] ) && file_exists( $template_part_file_path ) ) {
				$content = file_get_contents( $template_part_file_path );
				$content = is_string( $content ) && '' !== $content
						? _inject_theme_attribute_in_block_template_content( $content )
						: '';
			}

			if ( '' !== $content && null !== $content ) {
				/**
				 * Fires when a block template part is loaded from a template part in the theme.
				 *
				 * @since 5.9.0
				 *
				 * @param string $template_part_id        The requested template part namespaced to the theme.
				 * @param array  $attributes              The block attributes.
				 * @param string $template_part_file_path Absolute path to the template path.
				 * @param string $content                 The template part content.
				 */
				do_action( 'render_block_core_template_part_file', $template_part_id, $attributes, $template_part_file_path, $content );
			} else {
				/**
				 * Fires when a requested block template part does not exist in the database nor in the theme.
				 *
				 * @since 5.9.0
				 *
				 * @param string $template_part_id        The requested template part namespaced to the theme.
				 * @param array  $attributes              The block attributes.
				 * @param string $template_part_file_path Absolute path to the not found template path.
				 */
				do_action( 'render_block_core_template_part_none', $template_part_id, $attributes, $template_part_file_path );
			}
		}
	}

	// WP_DEBUG_DISPLAY must only be honored when WP_DEBUG. This precedent
	// is set in `wp_debug_mode()`.
	$is_debug = defined( 'WP_DEBUG' ) && WP_DEBUG &&
		defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG_DISPLAY;

	if ( is_null( $content ) && $is_debug ) {
		if ( ! isset( $attributes['slug'] ) ) {
			// If there is no slug this is a placeholder and we dont want to return any message.
			return;
		}
		return sprintf(
			/* translators: %s: Template part slug. */
			__( 'Template part has been deleted or is unavailable: %s' ),
			$attributes['slug']
		);
	}

	if ( isset( $seen_ids[ $template_part_id ] ) ) {
		return $is_debug ?
			// translators: Visible only in the front end, this warning takes the place of a faulty block.
			__( '[block rendering halted]' ) :
			'';
	}

	// Run through the actions that are typically taken on the_content.
	$content                       = shortcode_unautop( $content );
	$content                       = do_shortcode( $content );
	$seen_ids[ $template_part_id ] = true;
	$content                       = do_blocks( $content );
	unset( $seen_ids[ $template_part_id ] );
	$content = wptexturize( $content );
	$content = convert_smilies( $content );
	$content = wp_filter_content_tags( $content );

	// Handle embeds for block template parts.
	global $wp_embed;
	$content = $wp_embed->autoembed( $content );

	if ( empty( $attributes['tagName'] ) || tag_escape( $attributes['tagName'] ) !== $attributes['tagName'] ) {
		$defined_areas = get_allowed_block_template_part_areas();
		$area_tag      = 'div';
		foreach ( $defined_areas as $defined_area ) {
			if ( $defined_area['area'] === $area && isset( $defined_area['area_tag'] ) ) {
				$area_tag = $defined_area['area_tag'];
			}
		}
		$html_tag = $area_tag;
	} else {
		$html_tag = esc_attr( $attributes['tagName'] );
	}
	$wrapper_attributes = get_block_wrapper_attributes();

	return "<$html_tag $wrapper_attributes>" . str_replace( ']]>', ']]&gt;', $content ) . "</$html_tag>";
}

/**
 * Returns an array of variation objects for the template part block.
 *
 * @return array Array containing the block variation objects.
 */
function build_template_part_block_variations() {
	$variations    = array();
	$defined_areas = get_allowed_block_template_part_areas();
	foreach ( $defined_areas as $area ) {
		if ( 'uncategorized' !== $area['area'] ) {
			$variations[] = array(
				'name'        => $area['area'],
				'title'       => $area['label'],
				'description' => $area['description'],
				'attributes'  => array(
					'area' => $area['area'],
				),
				'scope'       => array( 'inserter' ),
				'icon'        => $area['icon'],
			);
		}
	}
	return $variations;
}

/**
 * Registers the `core/template-part` block on the server.
 */
function register_block_core_template_part() {
	register_block_type_from_metadata(
		__DIR__ . '/template-part',
		array(
			'render_callback' => 'render_block_core_template_part',
			'variations'      => build_template_part_block_variations(),
		)
	);
}
add_action( 'init', 'register_block_core_template_part' );
