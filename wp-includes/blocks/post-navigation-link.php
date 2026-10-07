<?php
/**
 * Server-side rendering of the `core/post-navigation-link` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/post-navigation-link` block on the server.
 *
 * @since 5.9.0
 *
 * @param array  $attributes Block attributes.
 * @param string $content    Block default content.
 *
 * @return string Returns the next or previous post link that is adjacent to the current post.
 */
function render_block_core_post_navigation_link( $attributes, $content ) {
	if ( ! is_singular() ) {
		return '';
	}

	// Get the navigation type to show the proper link. Available options are `next|previous`.
	$navigation_type = $attributes['type'] ?? 'next';
	// Allow only `next` and `previous` in `$navigation_type`.
	if ( ! in_array( $navigation_type, array( 'next', 'previous' ), true ) ) {
		return '';
	}
	$classes = "post-navigation-link-$navigation_type";
	if ( isset( $attributes['textAlign'] ) ) {
		$classes .= " has-text-align-{$attributes['textAlign']}";
	}

	// Set default values.
	$format = '%link';
	$link   = 'next' === $navigation_type ? _x( 'Next', 'label for next post link' ) : _x( 'Previous', 'label for previous post link' );
	$label  = '';

	// Only use hardcoded values here, otherwise we need to add escaping where these values are used.
	$arrow_map = array(
		'none'    => '',
		'arrow'   => array(
			'next'     => '→',
			'previous' => '←',
		),
		'chevron' => array(
			'next'     => '»',
			'previous' => '«',
		),
	);

	// If a custom label is provided, make this a link.
	// `$label` is used to prepend the provided label, if we want to show the page title as well.
	if ( isset( $attributes['label'] ) && ! empty( $attributes['label'] ) ) {
		$label = "{$attributes['label']}";
		$link  = $label;
	}

	// If we want to also show the page title, make the page title a link and prepend the label.
	if ( isset( $attributes['showTitle'] ) && $attributes['showTitle'] ) {
		/*
		 * If the label link option is not enabled but there is a custom label,
		 * display the custom label as text before the linked title.
		 */
		if ( ! $attributes['linkLabel'] ) {
			if ( $label ) {
				$format = '<span class="post-navigation-link__label">' . wp_kses_post( $label ) . '</span> %link';
			}
			$link = '%title';
		} elseif ( isset( $attributes['linkLabel'] ) && $attributes['linkLabel'] ) {
			// If the label link option is enabled and there is a custom label, display it before the title.
			if ( $label ) {
				$link = '<span class="post-navigation-link__label">' . wp_kses_post( $label ) . '</span> <span class="post-navigation-link__title">%title</span>';
			} else {
				/*
				 * If the label link option is enabled and there is no custom label,
				 * add a colon between the label and the post title.
				 */
				$label = 'next' === $navigation_type ? _x( 'Next:', 'label before the title of the next post' ) : _x( 'Previous:', 'label before the title of the previous post' );
				$link  = sprintf(
					'<span class="post-navigation-link__label">%1$s</span> <span class="post-navigation-link__title">%2$s</span>',
					wp_kses_post( $label ),
					'%title'
				);
			}
		}
	}

	// Display arrows.
	if ( isset( $attributes['arrow'] ) && 'none' !== $attributes['arrow'] && isset( $arrow_map[ $attributes['arrow'] ] ) ) {
		$arrow = $arrow_map[ $attributes['arrow'] ][ $navigation_type ];

		if ( 'next' === $navigation_type ) {
			$format = '%link<span class="wp-block-post-navigation-link__arrow-next is-arrow-' . $attributes['arrow'] . '" aria-hidden="true">' . $arrow . '</span>';
		} else {
			$format = '<span class="wp-block-post-navigation-link__arrow-previous is-arrow-' . $attributes['arrow'] . '" aria-hidden="true">' . $arrow . '</span>%link';
		}
	}

	/*
	 * The dynamic portion of the function name, `$navigation_type`,
	 * Refers to the type of adjacency, 'next' or 'previous'.
	 *
	 * @see https://developer.wordpress.org/reference/functions/get_previous_post_link/
	 * @see https://developer.wordpress.org/reference/functions/get_next_post_link/
	 */
	$get_link_function = "get_{$navigation_type}_post_link";

	if ( ! empty( $attributes['taxonomy'] ) ) {
		$content = $get_link_function( $format, $link, true, '', $attributes['taxonomy'] );
	} else {
		$content = $get_link_function( $format, $link );
	}

	// The `{next,previous}_post_link` filters can return null, which renders as no link.
	$content = (string) $content;

	/*
	 * Border, shadow and spacing serialization is skipped for this block so
	 * those styles can be withheld from the empty wrapper rendered when there is
	 * no adjacent post. The wrapper itself is kept for backward compatibility.
	 */
	$support_styles = '' === $content
		? array(
			'class' => '',
			'style' => '',
		)
		: block_core_post_navigation_link_get_support_styles( $attributes );

	if ( '' !== $support_styles['class'] ) {
		$classes .= " {$support_styles['class']}";
	}

	$wrapper_attributes = get_block_wrapper_attributes(
		array(
			'class' => $classes,
			'style' => $support_styles['style'],
		)
	);

	return sprintf(
		'<div %1$s>%2$s</div>',
		$wrapper_attributes,
		$content
	);
}

/**
 * Generates the border, shadow and spacing class names and inline styles for
 * the `core/post-navigation-link` block.
 *
 * Mirrors the default border, shadow and spacing block support serialization,
 * which the block opts out of so the styles are only applied when a link is
 * rendered.
 *
 * @since 7.2.0
 *
 * @param array $attributes The block attributes.
 * @return array {
 *     Border, shadow and spacing class names and inline styles.
 *
 *     @type string $class Class names generated by the style engine, or an empty string.
 *     @type string $style Inline styles generated by the style engine, or an empty string.
 * }
 */
function block_core_post_navigation_link_get_support_styles( $attributes ) {
	$block_styles  = $attributes['style'] ?? array();
	$border        = $block_styles['border'] ?? array();
	$border_styles = array();

	// Border radius and width accept unitless numbers from the original implementation.
	if ( isset( $border['radius'] ) ) {
		$border_styles['radius'] = is_numeric( $border['radius'] ) ? "{$border['radius']}px" : $border['radius'];
	}

	if ( isset( $border['width'] ) ) {
		$border_styles['width'] = is_numeric( $border['width'] ) ? "{$border['width']}px" : $border['width'];
	}

	if ( isset( $border['style'] ) ) {
		$border_styles['style'] = $border['style'];
	}

	// A preset border color is stored in its own attribute rather than under `style`.
	$border_styles['color'] = isset( $attributes['borderColor'] )
		? "var:preset|color|{$attributes['borderColor']}"
		: ( $border['color'] ?? null );

	// Individual border sides e.g. top, left etc.
	foreach ( array( 'top', 'right', 'bottom', 'left' ) as $side ) {
		$border_styles[ $side ] = array(
			'width' => $border[ $side ]['width'] ?? null,
			'color' => $border[ $side ]['color'] ?? null,
			'style' => $border[ $side ]['style'] ?? null,
		);
	}

	$styles = wp_style_engine_get_styles(
		array(
			'border'  => $border_styles,
			'shadow'  => $block_styles['shadow'] ?? null,
			'spacing' => array(
				'margin'  => $block_styles['spacing']['margin'] ?? null,
				'padding' => $block_styles['spacing']['padding'] ?? null,
			),
		)
	);

	return array(
		'class' => $styles['classnames'] ?? '',
		'style' => $styles['css'] ?? '',
	);
}

/**
 * Registers the `core/post-navigation-link` block on the server.
 *
 * @since 5.9.0
 */
function register_block_core_post_navigation_link() {
	register_block_type_from_metadata(
		__DIR__ . '/post-navigation-link',
		array(
			'render_callback' => 'render_block_core_post_navigation_link',
		)
	);
}
add_action( 'init', 'register_block_core_post_navigation_link' );
