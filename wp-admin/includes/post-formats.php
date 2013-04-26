<?php

defined( 'ABSPATH' ) or die;

global $wp_embed;

$format_meta = get_post_format_meta( $post_ID );

wp_nonce_field( 'show-post-format-ui_' . $post_type, 'show_post_format_ui_nonce', false );

?>
<div class="wp-post-format-ui<?php if ( ! $show_post_format_ui ) echo ' no-ui' ?>">
	<div class="post-formats-fields">

		<input type="hidden" name="post_format" id="post_format" value="<?php echo esc_attr( $post_format ); ?>" />

		<div class="field wp-format-quote">
			<label for="wp_format_quote_source"><?php _e( 'Quote source' ); ?></label>
			<input type="text" id="wp_format_quote_source" name="_format_quote_source_name" value="<?php echo esc_attr( $format_meta['quote_source_name'] ); ?>" class="widefat" />
		</div>

		<div class="field wp-format-image">
			<?php if ( ! empty( $format_meta['image'] ) ) :
				$value = $format_meta['image'];
			?>
			<div id="image-preview" class="wp-format-media-preview">
				<?php
					if ( is_numeric( $value ) ) {
						$image = wp_get_attachment_url( $value );
						printf( '<img src="%s" alt="%s" />', esc_url( $image ), get_the_title( $value ) );
					} elseif ( preg_match( '/' . get_shortcode_regex() . '/s', $value ) ) {
						echo do_shortcode( $value );
					} elseif ( ! preg_match( '#<[^>]+>#', $value ) ) {
						printf( '<img src="%s" alt="" />', esc_url( $value ) );
					} else {
						echo $value;
					}
				?>
			</div>
			<?php endif ?>
			<label for="wp_format_image"><?php
				if ( current_user_can( 'unfiltered_html' ) )
					_e( 'Image HTML or URL' );
				else
					_e( 'Image URL' );
			?></label>
			<textarea id="wp_format_image" type="text" name="_format_image" class="widefat"><?php esc_html_e( $format_meta['image'] ); ?></textarea>
			<div data-format="image" class="wp-format-media-holder hide-if-no-js">
				<a href="#" class="wp-format-media-select"
					data-choose="<?php esc_attr_e( 'Choose an Image' ); ?>"
					data-update="<?php esc_attr_e( 'Select Image' ); ?>">
					<?php _e( 'Select / Upload Image' ); ?>
				</a>
			</div>
		</div>

		<div class="field wp-format-link">
			<label for="wp_format_link_url"><?php _e( 'Link URL' ); ?></label>
			<input type="text" id="wp_format_link_url" name="_format_link_url" value="<?php echo esc_url( $format_meta['link_url'] ); ?>" class="widefat" />
		</div>

		<div class="field wp-format-quote">
			<label for="wp_format_quote_source_url"><?php _e( 'Link URL' ); ?></label>
			<input type="text" id="wp_format_quote_source_url" name="_format_quote_source_url" value="<?php echo esc_url( $format_meta['quote_source_url'] ); ?>" class="widefat" />
		</div>

		<div class="field wp-format-image">
			<label for="wp_format_image_url"><?php _e( 'Link URL' ); ?></label>
			<input type="text" id="wp_format_image_url" name="_format_url" value="<?php echo esc_url( $format_meta['url'] ); ?>" class="widefat" />
		</div>

		<div class="field wp-format-video">
			<?php if ( ! empty( $format_meta['video_embed'] ) ):
				$value = $format_meta['video_embed'];
			?>
			<div id="video-preview" class="wp-format-media-preview">
				<?php
					if ( is_numeric( $value ) ) {
						$video = wp_get_attachment_url( $value );
						echo do_shortcode( sprintf( '[video src="%s"]', $video ) );
					} elseif ( preg_match( '/' . get_shortcode_regex() . '/s', $value ) ) {
						echo do_shortcode( $value );
					} elseif ( ! preg_match( '#<[^>]+>#', $value ) ) {
						if ( strstr( $value, home_url() ) )
							echo do_shortcode( sprintf( '[video src="%s"]', $value ) );
						else
							echo $wp_embed->autoembed( $value );
					} else {
						echo $value;
					}
				?>
			</div>
			<?php endif; ?>
			<label for="wp_format_video"><?php
				if ( current_user_can( 'unfiltered_html' ) )
					_e( 'Video embed code or URL' );
				else
					_e( 'Video URL' );
			?></label>
			<textarea id="wp_format_video" type="text" name="_format_video_embed" class="widefat"><?php esc_html_e( $format_meta['video_embed'] ); ?></textarea>
			<div data-format="video" class="wp-format-media-holder hide-if-no-js">
				<a href="#" class="wp-format-media-select"
					data-choose="<?php esc_attr_e( 'Choose a Video' ); ?>"
					data-update="<?php esc_attr_e( 'Select Video' ); ?>">
					<?php _e( 'Select Video From Media Library' ) ?>
				</a>
			</div>
		</div>

		<div class="field wp-format-audio">
			<?php if ( ! empty( $format_meta['audio_embed'] ) ):
				$value = $format_meta['audio_embed'];
			?>
			<div id="audio-preview" class="wp-format-media-preview">
				<?php
					if ( is_numeric( $value ) ) {
						$audio = wp_get_attachment_url( $value );
						echo do_shortcode( sprintf( '[audio src="%s"]', $audio ) );
					} elseif ( preg_match( '/' . get_shortcode_regex() . '/s', $value ) ) {
						echo do_shortcode( $value );
					} elseif ( ! preg_match( '#<[^>]+>#', $value ) ) {
						if ( strstr( $value, home_url() ) )
							echo do_shortcode( sprintf( '[audio src="%s"]', $value ) );
						else
							echo $wp_embed->autoembed( $value );
					} else {
						echo $value;
					}
				?>
			</div>
			<?php endif; ?>
			<label for="wp_format_audio"><?php
				if ( current_user_can( 'unfiltered_html' ) )
					_e( 'Audio embed code or URL' );
				else
					_e( 'Audio URL' );
			?></label>
			<textarea id="wp_format_audio" name="_format_audio_embed" class="widefat"><?php esc_html_e( $format_meta['audio_embed'] ); ?></textarea>
			<div data-format="audio" class="wp-format-media-holder hide-if-no-js">
				<a href="#" class="wp-format-media-select" data-choose="<?php esc_attr_e( 'Choose Audio' ); ?>" data-update="<?php esc_attr_e( 'Select Audio' ); ?>">
					<?php _e( 'Select Audio From Media Library' ) ?>
				</a>
			</div>
		</div>
	</div>
</div>
