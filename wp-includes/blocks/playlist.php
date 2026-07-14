<?php
/**
 * Server-side rendering of the `core/playlist` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/playlist` block on server.
 *
 * @since 6.9.0
 *
 * @param array    $attributes The block attributes.
 * @param string   $content    The block content.
 * @param WP_Block $block      The block instance.
 *
 * @return string Returns the Playlist.
 */
function render_block_core_playlist( $attributes, $content, $block ) {
	$playlist_id              = wp_unique_id( 'playlist-' );
	$playlist_tracks          = array();
	$tracks_data              = array();
	$show_play_button_artwork = ! empty( $attributes['showPlayButtonArtwork'] );

	// Parse inner blocks to extract track data.
	// This approach avoids duplicating track data in the HTML output.
	if ( ! empty( $block->inner_blocks ) ) {
		foreach ( $block->inner_blocks as $inner_block ) {
			if ( 'core/playlist-track' === $inner_block->name ) {
				$track_attributes = $inner_block->attributes;

				if ( empty( $track_attributes['id'] ) ) {
					continue;
				}

				$track_id          = 'track-' . count( $playlist_tracks );
				$playlist_tracks[] = $track_id;

				// Extract track metadata from block attributes.
				$title      = isset( $track_attributes['title'] ) && ! empty( $track_attributes['title'] ) ? $track_attributes['title'] : __( 'Unknown title' );
				$artist     = $track_attributes['artist'] ?? '';
				$album      = $track_attributes['album'] ?? '';
				$image      = $track_attributes['image'] ?? '';
				$image_alt  = $track_attributes['imageAlt'] ?? '';
				$url        = $track_attributes['src'] ?? '';
				$aria_label = $title;

				if ( $title && $artist && $album ) {
					$aria_label = sprintf(
						/* translators: %1$s: track title, %2$s: artist name, %3$s: album name. */
						_x( '%1$s by %2$s from the album %3$s', 'track title, artist name, album name' ),
						$title,
						$artist,
						$album
					);
				}

				// Data is passed to wp_interactivity_state() which JSON-encodes it,
				// so we use wp_strip_all_tags() instead of esc_html() to prevent
				// HTML injection without double-encoding. URLs still use esc_url().
				$tracks_data[ $track_id ] = array(
					'url'       => esc_url( $url ),
					'title'     => wp_strip_all_tags( $title ),
					'artist'    => wp_strip_all_tags( $artist ),
					'album'     => wp_strip_all_tags( $album ),
					'image'     => esc_url( $image ),
					'imageAlt'  => wp_strip_all_tags( $image_alt ),
					'ariaLabel' => wp_strip_all_tags( $aria_label ),
				);
			}
		}
	}

	if ( empty( $playlist_tracks ) ) {
		return '';
	}

	wp_enqueue_script_module( '@wordpress/block-library/playlist/view' );

	// Add the playlist tracks to the global state,
	// but keep them isolated from other playlists with the help of playlistId.
	wp_interactivity_state(
		'core/playlist',
		array(
			'playlists' => array(
				$playlist_id => array(
					'tracks' => $tracks_data,
				),
			),
		)
	);

	// Add waveform player container with translated button labels.
	$label_play  = esc_attr__( 'Play' );
	$label_pause = esc_attr__( 'Pause' );
	$label_seek  = esc_attr__( 'Seek' );
	/* translators: %1$s: current audio time, %2$s: total audio duration. */
	$label_seek_value                       = esc_attr_x(
		'%1$s of %2$s',
		'audio current time of total duration'
	);
	$waveform_color_attribute               = '';
	$waveform_gradient_attribute            = '';
	$waveform_background_color_attribute    = '';
	$waveform_background_gradient_attribute = '';
	if ( ! empty( $attributes['waveformColor'] ) ) {
		$waveform_color_attribute = sprintf(
			' data-waveform-player-color="%s"',
			esc_attr( $attributes['waveformColor'] )
		);
	}
	if ( ! empty( $attributes['waveformGradient'] ) ) {
		$waveform_gradient_attribute = sprintf(
			' data-waveform-player-gradient="%s"',
			esc_attr( $attributes['waveformGradient'] )
		);
	}
	if ( ! empty( $attributes['waveformBackgroundColor'] ) ) {
		$waveform_background_color_attribute = sprintf(
			' data-waveform-player-background-color="%s"',
			esc_attr( $attributes['waveformBackgroundColor'] )
		);
	}
	if ( ! empty( $attributes['waveformBackgroundGradient'] ) ) {
		$waveform_background_gradient_attribute = sprintf(
			' data-waveform-player-background-gradient="%s"',
			esc_attr( $attributes['waveformBackgroundGradient'] )
		);
	}
	$html = '<div class="wp-block-playlist__waveform-player"' .
		$waveform_color_attribute .
		$waveform_gradient_attribute .
		$waveform_background_color_attribute .
		$waveform_background_gradient_attribute . '
		data-wp-watch="callbacks.initWaveformPlayer"
		data-label-play="' . $label_play . '"
		data-label-pause="' . $label_pause . '"
		data-label-seek="' . $label_seek . '"
		data-label-seek-value="' . $label_seek_value . '"
	></div>';

	// Add the waveform player container inside the figure.
	$figure = null;
	preg_match( '/<figure[^>]*>/', $content, $figure );
	if ( ! empty( $figure[0] ) ) {
		$content = preg_replace( '/(<figure[^>]*>)/', '$1' . $html, $content, 1 );
	}

	$processor = new WP_HTML_Tag_Processor( $content );
	$processor->next_tag( 'figure' );
	$processor->set_attribute( 'data-wp-interactive', 'core/playlist' );

	$waveform_style = $attributes['waveformStyle'] ?? 'bars';

	$processor->set_attribute(
		'data-wp-context',
		wp_json_encode(
			array(
				'playlistId'            => $playlist_id,
				'currentId'             => $playlist_tracks[0],
				'isPlaying'             => false,
				'tracks'                => $playlist_tracks,
				'waveformStyle'         => $waveform_style,
				'showPlayButtonArtwork' => $show_play_button_artwork,
				'labelPauseTrack'       => __( 'Pause' ),
				'labelSelectTrack'      => __( 'Play' ),
			)
		)
	);

	// Track IDs are render-time only. Add them after inner blocks have rendered
	// so track buttons can update the Interactivity API state without storing
	// persistent unique IDs in post content.
	$track_index = 0;
	while ( $processor->next_tag( array( 'class_name' => 'wp-block-playlist-track__button' ) ) ) {
		$track_id = $playlist_tracks[ $track_index ] ?? null;

		if ( null === $track_id ) {
			break;
		}

		$processor->set_attribute(
			'data-wp-context',
			wp_json_encode(
				array(
					'trackId' => $track_id,
				)
			)
		);

		++$track_index;
	}

	return $processor->get_updated_html();
}

/**
 * Registers the `core/playlist` block on server.
 *
 * @since 6.9.0
 */
function register_block_core_playlist() {
	register_block_type_from_metadata(
		__DIR__ . '/playlist',
		array(
			'render_callback' => 'render_block_core_playlist',
		)
	);
}
add_action( 'init', 'register_block_core_playlist' );
