<?php
/**
 * Server-side rendering of the `core/archives` block.
 *
 * @package gutenberg
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

	$class = 'wp-block-archives';

	if ( isset( $attributes['align'] ) ) {
		$class .= " align{$attributes['align']}";
	}

	if ( isset( $attributes['className'] ) ) {
		$class .= " {$attributes['className']}";
	}

	if ( ! empty( $attributes['displayAsDropdown'] ) ) {

		$dropdown_id = esc_attr( uniqid( 'wp-block-archives-' ) );
		$title       = __( 'Archives', 'gutenberg' );

		/** This filter is documented in wp-includes/widgets/class-wp-widget-archives.php */
		$dropdown_args = apply_filters(
			'widget_archives_dropdown_args',
			array(
				'type'            => 'monthly',
				'format'          => 'option',
				'show_post_count' => $show_post_count,
			)
		);

		$dropdown_args['echo'] = 0;

		$archives = wp_get_archives( $dropdown_args );

		switch ( $dropdown_args['type'] ) {
			case 'yearly':
				$label = __( 'Select Year', 'gutenberg' );
				break;
			case 'monthly':
				$label = __( 'Select Month', 'gutenberg' );
				break;
			case 'daily':
				$label = __( 'Select Day', 'gutenberg' );
				break;
			case 'weekly':
				$label = __( 'Select Week', 'gutenberg' );
				break;
			default:
				$label = __( 'Select Post', 'gutenberg' );
				break;
		}

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
				__( 'No archives to show.', 'gutenberg' )
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
}

/**
 * Register archives block.
 */
function register_block_core_archives() {
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
			'render_callback' => 'render_block_core_archives',
		)
	);
}

add_action( 'init', 'register_block_core_archives' );
