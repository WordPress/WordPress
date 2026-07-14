<?php
/**
 * Server-side rendering of the `core/playlist-track` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/playlist-track` block on server.
 *
 * @since 6.9.0
 *
 * @param array         $attributes The block attributes.
 * @param string        $content    The block content.
 * @param WP_Block|null $block      The block instance.
 *
 * @return string Returns the Playlist Track.
 */
function render_block_core_playlist_track( $attributes, $content = '', $block = null ) {
	if ( empty( $attributes['id'] ) ) {
		return '';
	}

	$wrapper_attributes = get_block_wrapper_attributes();
	$show_images        = true;
	if ( $block instanceof WP_Block && isset( $block->context['showImages'] ) ) {
		$show_images = $block->context['showImages'];
	}

	$artist = $attributes['artist'] ?? '';
	$image  = $attributes['image'] ?? '';
	$alt    = $attributes['imageAlt'] ?? '';
	$length = $attributes['length'] ?? '';
	$title  = isset( $attributes['title'] ) && ! empty( $attributes['title'] ) ? $attributes['title'] : __( 'Unknown title' );

	$html  = '<li ' . $wrapper_attributes . '>';
	$html .= '<button data-wp-on--click="actions.changeTrack" data-wp-bind--aria-current="state.isCurrentTrack" class="wp-block-playlist-track__button">';

	if ( $show_images && $image ) {
		$html .= '<img class="wp-block-playlist-track__image" src="' . esc_url( $image ) . '" alt="' . esc_attr( $alt ) . '" />';
	}

	$html .= '<span class="wp-block-playlist-track__content">';
	if ( $title ) {
		$html .= '<span class="wp-block-playlist-track__title">' . esc_html( $title ) . '</span>';
	}
	if ( $artist ) {
		$html .= '<span class="wp-block-playlist-track__artist">' . esc_html( $artist ) . '</span>';
	}
	$html .= '</span>';

	if ( $length ) {
		$html .= '<span class="wp-block-playlist-track__length">';
		$html .= '<span class="screen-reader-text">' . esc_html__( 'Duration:' ) . ' </span>';
		$html .= esc_html( $length );
		$html .= '</span>';
	}

	$html .= '<span class="screen-reader-text" data-wp-text="state.trackButtonActionLabel">';
	$html .= esc_html__( 'Play' );
	$html .= '</span>';
	$html .= '</button>';
	$html .= '</li>';

	return $html;
}

/**
 * Registers the `core/playlist-track` block on server.
 *
 * @since 6.9.0
 */
function register_block_core_playlist_track() {
	register_block_type_from_metadata(
		__DIR__ . '/playlist-track',
		array(
			'render_callback' => 'render_block_core_playlist_track',
		)
	);
}
add_action( 'init', 'register_block_core_playlist_track' );
