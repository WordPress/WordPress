<?php
/**
 * Server-side rendering of the `core/archives` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/archives` block on server.
 *
 * @see WP_Widget_Archives
 *
 * @param array $attributes The block attributes.
 *
 * @return string Returns the post content with archives added.
 */
function render_block_core_archives( $attributes ) {
	$show_post_count = ! empty( $attributes['showPostCounts'] );
<<<<<<< HEAD
	$type            = isset( $attributes['type'] ) ? $attributes['type'] : 'monthly';

	$class = 'wp-block-archives-list';

	if ( ! empty( $attributes['displayAsDropdown'] ) ) {

		$class = 'wp-block-archives-dropdown';

		$dropdown_id = wp_unique_id( 'wp-block-archives-' );
=======

	$class = 'wp-block-archives';

	if ( isset( $attributes['align'] ) ) {
		$class .= " align{$attributes['align']}";
	}

	if ( isset( $attributes['className'] ) ) {
		$class .= " {$attributes['className']}";
	}

	if ( ! empty( $attributes['displayAsDropdown'] ) ) {

		$class .= ' wp-block-archives-dropdown';

		$dropdown_id = esc_attr( uniqid( 'wp-block-archives-' ) );
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
		$title       = __( 'Archives' );

		/** This filter is documented in wp-includes/widgets/class-wp-widget-archives.php */
		$dropdown_args = apply_filters(
			'widget_archives_dropdown_args',
			array(
<<<<<<< HEAD
				'type'            => $type,
=======
				'type'            => 'monthly',
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
				'format'          => 'option',
				'show_post_count' => $show_post_count,
			)
		);

		$dropdown_args['echo'] = 0;

		$archives = wp_get_archives( $dropdown_args );

<<<<<<< HEAD
		$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $class ) );

=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
		switch ( $dropdown_args['type'] ) {
			case 'yearly':
				$label = __( 'Select Year' );
				break;
			case 'monthly':
				$label = __( 'Select Month' );
				break;
			case 'daily':
				$label = __( 'Select Day' );
				break;
			case 'weekly':
				$label = __( 'Select Week' );
				break;
			default:
				$label = __( 'Select Post' );
				break;
		}

<<<<<<< HEAD
		$show_label = empty( $attributes['showLabel'] ) ? ' screen-reader-text' : '';

		$block_content = '<label for="' . $dropdown_id . '" class="wp-block-archives__label' . $show_label . '">' . esc_html( $title ) . '</label>
		<select id="' . $dropdown_id . '" name="archive-dropdown" onchange="document.location.href=this.options[this.selectedIndex].value;">
		<option value="">' . esc_html( $label ) . '</option>' . $archives . '</select>';

		return sprintf(
			'<div %1$s>%2$s</div>',
			$wrapper_attributes,
			$block_content
		);
	}

	/** This filter is documented in wp-includes/widgets/class-wp-widget-archives.php */
	$archives_args = apply_filters(
		'widget_archives_args',
		array(
			'type'            => $type,
			'show_post_count' => $show_post_count,
		)
	);

	$archives_args['echo'] = 0;

	$archives = wp_get_archives( $archives_args );

	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $class ) );

	if ( empty( $archives ) ) {
		return sprintf(
			'<div %1$s>%2$s</div>',
			$wrapper_attributes,
			__( 'No archives to show.' )
		);
	}

	return sprintf(
		'<ul %1$s>%2$s</ul>',
		$wrapper_attributes,
		$archives
	);
=======
		$label = esc_attr( $label );

		$block_content = '<label class="screen-reader-text" for="' . $dropdown_id . '">' . $title . '</label>
	<select id="' . $dropdown_id . '" name="archive-dropdown" onchange="document.location.href=this.options[this.selectedIndex].value;">
	<option value="">' . $label . '</option>' . $archives . '</select>';

		$block_content = sprintf(
			'<div class="%1$s">%2$s</div>',
			esc_attr( $class ),
			$block_content
		);
	} else {

		$class .= ' wp-block-archives-list';

		/** This filter is documented in wp-includes/widgets/class-wp-widget-archives.php */
		$archives_args = apply_filters(
			'widget_archives_args',
			array(
				'type'            => 'monthly',
				'show_post_count' => $show_post_count,
			)
		);

		$archives_args['echo'] = 0;

		$archives = wp_get_archives( $archives_args );

		$classnames = esc_attr( $class );

		if ( empty( $archives ) ) {

			$block_content = sprintf(
				'<div class="%1$s">%2$s</div>',
				$classnames,
				__( 'No archives to show.' )
			);
		} else {

			$block_content = sprintf(
				'<ul class="%1$s">%2$s</ul>',
				$classnames,
				$archives
			);
		}
	}

	return $block_content;
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
}

/**
 * Register archives block.
 */
function register_block_core_archives() {
<<<<<<< HEAD
	register_block_type_from_metadata(
		__DIR__ . '/archives',
		array(
=======
	register_block_type(
		'core/archives',
		array(
			'attributes'      => array(
				'align'             => array(
					'type' => 'string',
				),
				'className'         => array(
					'type' => 'string',
				),
				'displayAsDropdown' => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'showPostCounts'    => array(
					'type'    => 'boolean',
					'default' => false,
				),
			),
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
			'render_callback' => 'render_block_core_archives',
		)
	);
}
<<<<<<< HEAD
=======

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
add_action( 'init', 'register_block_core_archives' );
