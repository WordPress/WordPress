<?php
/**
 * Server-side rendering of the `core/rss` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/rss` block on server.
 *
 * @param array $attributes The block attributes.
 *
 * @return string Returns the block content with received rss items.
 */
function render_block_core_rss( $attributes ) {
	$rss = fetch_feed( $attributes['feedURL'] );

	if ( is_wp_error( $rss ) ) {
		return '<div class="components-placeholder"><div class="notice notice-error"><strong>' . __( 'RSS Error:' ) . '</strong> ' . $rss->get_error_message() . '</div></div>';
	}

	if ( ! $rss->get_item_quantity() ) {
		return '<div class="components-placeholder"><div class="notice notice-error">' . __( 'An error has occurred, which probably means the feed is down. Try again later.' ) . '</div></div>';
	}

	$rss_items  = $rss->get_items( 0, $attributes['itemsToShow'] );
	$list_items = '';
	foreach ( $rss_items as $item ) {
		$title = esc_html( trim( strip_tags( $item->get_title() ) ) );
		if ( empty( $title ) ) {
			$title = __( '(no title)' );
		}
		$link = $item->get_link();
		$link = esc_url( $link );
		if ( $link ) {
			$title = "<a href='{$link}'>{$title}</a>";
		}
		$title = "<div class='wp-block-rss__item-title'>{$title}</div>";

		$date = '';
		if ( $attributes['displayDate'] ) {
			$date = $item->get_date( 'U' );

			if ( $date ) {
				$date = sprintf(
					'<time datetime="%1$s" class="wp-block-rss__item-publish-date">%2$s</time> ',
					date_i18n( get_option( 'c' ), $date ),
					date_i18n( get_option( 'date_format' ), $date )
				);
			}
		}

		$author = '';
		if ( $attributes['displayAuthor'] ) {
			$author = $item->get_author();
			if ( is_object( $author ) ) {
				$author = $author->get_name();
				$author = '<span class="wp-block-rss__item-author">' . __( 'by' ) . ' ' . esc_html( strip_tags( $author ) ) . '</span>';
			}
		}

		$excerpt = '';
		if ( $attributes['displayExcerpt'] ) {
			$excerpt = html_entity_decode( $item->get_description(), ENT_QUOTES, get_option( 'blog_charset' ) );
			$excerpt = esc_attr( wp_trim_words( $excerpt, $attributes['excerptLength'], ' [&hellip;]' ) );

			// Change existing [...] to [&hellip;].
			if ( '[...]' === substr( $excerpt, -5 ) ) {
				$excerpt = substr( $excerpt, 0, -5 ) . '[&hellip;]';
			}

			$excerpt = '<div class="wp-block-rss__item-excerpt">' . esc_html( $excerpt ) . '</div>';
		}

		$list_items .= "<li class='wp-block-rss__item'>{$title}{$date}{$author}{$excerpt}</li>";
	}

	$class = 'wp-block-rss';
	if ( isset( $attributes['align'] ) ) {
		$class .= ' align' . $attributes['align'];
	}

	if ( isset( $attributes['blockLayout'] ) && 'grid' === $attributes['blockLayout'] ) {
		$class .= ' is-grid';
	}

	if ( isset( $attributes['columns'] ) && 'grid' === $attributes['blockLayout'] ) {
		$class .= ' columns-' . $attributes['columns'];
	}

	if ( isset( $attributes['className'] ) ) {
		$class .= ' ' . $attributes['className'];
	}

	return "<ul class='{$class}'>{$list_items}</ul>";
}

/**
 * Registers the `core/rss` block on server.
 */
function register_block_core_rss() {
	register_block_type(
		'core/rss',
		array(
			'attributes'      => array(
				'align'          => array(
					'type' => 'string',
					'enum' => array( 'left', 'center', 'right', 'wide', 'full' ),
				),
				'className'      => array(
					'type' => 'string',
				),
				'columns'        => array(
					'type'    => 'number',
					'default' => 2,
				),
				'blockLayout'    => array(
					'type'    => 'string',
					'default' => 'list',
				),
				'feedURL'        => array(
					'type'    => 'string',
					'default' => '',
				),
				'itemsToShow'    => array(
					'type'    => 'number',
					'default' => 5,
				),
				'displayExcerpt' => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'displayAuthor'  => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'displayDate'    => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'excerptLength'  => array(
					'type'    => 'number',
					'default' => 55,
				),
			),
			'render_callback' => 'render_block_core_rss',
		)
	);
}
add_action( 'init', 'register_block_core_rss' );
