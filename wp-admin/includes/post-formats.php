<?php
global $wp_embed;
$format_meta = get_post_format_meta( $post_ID );

?>
<div class="post-format-description"></div>
<div class="post-formats-fields">

	<input type="hidden" name="post_format" id="post_format" value="<?php echo esc_attr( $post_format ); ?>" />

	<div class="field wp-format-quote">
		<label for="wp_format_quote"><?php _e( 'Quote' ); ?></label>
		<textarea id="wp_format_quote" name="_wp_format_quote" class="widefat"><?php echo esc_textarea( $format_meta['quote'] ); ?></textarea>
	</div>

	<div class="field wp-format-quote">
		<label for="wp_format_quote_source"><?php _e( 'Quote source' ); ?></label>
		<input type="text" id="wp_format_quote_source" name="_wp_format_quote_source" value="<?php echo esc_attr( $format_meta['quote_source'] ); ?>" class="widefat" />
	</div>

	<?php
	$image = false;
	if ( ! empty( $format_meta['image'] ) && is_numeric( $format_meta['image'] ) ) {
		$format_meta['image'] = absint( $format_meta['image'] );
		$image = wp_get_attachment_url( $format_meta['image'] );
	}
	?>
	<div class="field wp-format-image">
		<div data-format="image" class="wp-format-media-holder hide-if-no-js<?php if ( ! $image ) echo ' empty'; ?>">
			<a href="#" class="wp-format-media-select"
				data-choose="<?php esc_attr_e( 'Choose an Image' ); ?>"
				data-update="<?php esc_attr_e( 'Select Image' ); ?>">
				<?php
					if ( $image )
						printf( '<img src="%s" alt="%s" />', esc_url( $image ), get_the_title( $format_meta['image'] ) );
					else
						_e( 'Select / Upload Image' );
				?>
			</a>
		</div>
		<input id="wp_format_image" type="hidden" name="_wp_format_image" value="<?php echo esc_attr( $format_meta['image'] ); ?>" />
	</div>

	<div class="field wp-format-link wp-format-quote wp-format-image">
		<label for="wp_format_url"><?php _e( 'Link URL' ); ?></label>
		<input type="text" id="wp_format_url" name="_wp_format_url" value="<?php echo esc_url( $format_meta['url'] ); ?>" class="widefat" />
	</div>

	<?php
	$show_video_preview = ! empty( $format_meta['video'] );
	?>
	<div class="field wp-format-video<?php if ( $show_video_preview ) echo ' has-media-preview'; ?>">
		<?php if ( $show_video_preview ): ?>
		<div id="video-preview" class="wp-format-media-preview">
			<?php
				if ( is_numeric( $format_meta['video'] ) ) {
					$url = wp_get_attachment_url( $format_meta['video'] );
					echo do_shortcode( sprintf( '[video src="%s"]', $url ) );
				} elseif ( preg_match( '/' . get_shortcode_regex() . '/s', $format_meta['video'] ) ) {
					echo do_shortcode( $format_meta['video'] );
				} elseif ( ! preg_match( '#<[^>]+>#', $format_meta['video'] ) ) {
					if ( strstr( $format_meta['video'], home_url() ) )
						echo do_shortcode( sprintf( '[video src="%s"]', $format_meta['video'] ) );
					else
						echo $wp_embed->autoembed( $format_meta['video'] );
				} else {
					echo $format_meta['video'];
				}
			?>
		</div>
		<?php endif; ?>
		<label for="wp_format_video"><?php _e( 'Video embed code or URL' ); ?></label>
		<textarea id="wp_format_video" type="text" name="_wp_format_video" class="widefat"><?php esc_html_e( $format_meta['video'] ); ?></textarea>
		<div data-format="video" class="wp-format-media-holder hide-if-no-js<?php if ( ! $image ) echo ' empty'; ?>">
			<a href="#" class="wp-format-media-select"
				data-choose="<?php esc_attr_e( 'Choose a Video' ); ?>"
				data-update="<?php esc_attr_e( 'Select Video' ); ?>">
				<?php _e( 'Select Video From Media Library' ) ?>
			</a>
		</div>
	</div>

	<?php
	$show_audio_preview = ! empty( $format_meta['audio'] );
	?>
	<div class="field wp-format-audio<?php if ( $show_audio_preview ) echo ' has-media-preview' ?>">
		<?php if ( $show_audio_preview ): ?>
		<div id="audio-preview" class="wp-format-media-preview">
			<?php
				if ( is_numeric( $format_meta['audio'] ) ) {
					$url = wp_get_attachment_url( $format_meta['audio'] );
					echo do_shortcode( sprintf( '[audio src="%s"]', $url ) );
				} elseif ( preg_match( '/' . get_shortcode_regex() . '/s', $format_meta['audio'] ) ) {
					echo do_shortcode( $format_meta['audio'] );
				} elseif ( ! preg_match( '#<[^>]+>#', $format_meta['audio'] ) ) {
					if ( strstr( $format_meta['audio'], home_url() ) )
						echo do_shortcode( sprintf( '[audio src="%s"]', $format_meta['audio'] ) );
					else
						echo $wp_embed->autoembed( $format_meta['audio'] );
				} else {
					echo $format_meta['audio'];
				}
			?>
		</div>
		<?php endif; ?>
		<label for="wp_format_audio"><?php _e( 'Audio embed code or URL' ); ?></label>
		<textarea id="wp_format_audio" name="_wp_format_audio" class="widefat"><?php esc_html_e( $format_meta['audio'] );
?></textarea>
		<div data-format="audio" class="wp-format-media-holder hide-if-no-js<?php if ( empty( $format_meta['audio'] ) ) echo ' empty'; ?>">
			<a href="#" class="wp-format-media-select" data-choose="<?php esc_attr_e( 'Choose Audio' ); ?>" data-update="<?php esc_attr_e( 'Select Audio' ); ?>">
				<?php _e( 'Select Audio From Media Library' ) ?>
			</a>
		</div>
	</div>
</div>
