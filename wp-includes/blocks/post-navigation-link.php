<?php
/**
 * Server-side rendering of the `core/post-navigation-link` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/post-navigation-link` block on the server.
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
	$navigation_type = isset( $attributes['type'] ) ? $attributes['type'] : 'next';
	// Allow only `next` and `previous` in `$navigation_type`.
	if ( ! in_array( $navigation_type, array( 'next', 'previous' ), true ) ) {
		return '';
	}
	$classes = "post-navigation-link-$navigation_type";
	if ( isset( $attributes['textAlign'] ) ) {
		$classes .= " has-text-align-{$attributes['textAlign']}";
	}
	$wrapper_attributes = get_block_wrapper_attributes(
		array(
			'class' => $classes,
		)
	);
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

	// The dynamic portion of the function name, `$navigation_type`,
	// refers to the type of adjacency, 'next' or 'previous'.
	$get_link_function = "get_{$navigation_type}_post_link";
	$content           = $get_link_function( $format, $link );
	return sprintf(
		'<div %1$s>%2$s</div>',
		$wrapper_attributes,
		$content
	);
}

/**
 * Registers the `core/post-navigation-link` block on the server.
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
