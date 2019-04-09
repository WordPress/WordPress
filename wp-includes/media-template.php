<?php
/**
 * WordPress media templates.
 *
 * @package WordPress
 * @subpackage Media
 * @since 3.5.0
 */

/**
 * Output the markup for a audio tag to be used in an Underscore template
 * when data.model is passed.
 *
 * @since 3.9.0
 */
function wp_underscore_audio_template() {
	$audio_types = wp_get_audio_extensions();
	?>
<audio style="visibility: hidden"
	controls
	class="wp-audio-shortcode"
	width="{{ _.isUndefined( data.model.width ) ? 400 : data.model.width }}"
	preload="{{ _.isUndefined( data.model.preload ) ? 'none' : data.model.preload }}"
	<#
	<?php
	foreach ( array( 'autoplay', 'loop' ) as $attr ) :
		?>
	if ( ! _.isUndefined( data.model.<?php echo $attr; ?> ) && data.model.<?php echo $attr; ?> ) {
		#> <?php echo $attr; ?><#
	}
	<?php endforeach ?>#>
>
	<# if ( ! _.isEmpty( data.model.src ) ) { #>
	<source src="{{ data.model.src }}" type="{{ wp.media.view.settings.embedMimes[ data.model.src.split('.').pop() ] }}" />
	<# } #>

	<?php
	foreach ( $audio_types as $type ) :
		?>
	<# if ( ! _.isEmpty( data.model.<?php echo $type; ?> ) ) { #>
	<source src="{{ data.model.<?php echo $type; ?> }}" type="{{ wp.media.view.settings.embedMimes[ '<?php echo $type; ?>' ] }}" />
	<# } #>
		<?php
	endforeach;
	?>
</audio>
	<?php
}

/**
 * Output the markup for a video tag to be used in an Underscore template
 * when data.model is passed.
 *
 * @since 3.9.0
 */
function wp_underscore_video_template() {
	$video_types = wp_get_video_extensions();
	?>
<#  var w_rule = '', classes = [],
		w, h, settings = wp.media.view.settings,
		isYouTube = isVimeo = false;

	if ( ! _.isEmpty( data.model.src ) ) {
		isYouTube = data.model.src.match(/youtube|youtu\.be/);
		isVimeo = -1 !== data.model.src.indexOf('vimeo');
	}

	if ( settings.contentWidth && data.model.width >= settings.contentWidth ) {
		w = settings.contentWidth;
	} else {
		w = data.model.width;
	}

	if ( w !== data.model.width ) {
		h = Math.ceil( ( data.model.height * w ) / data.model.width );
	} else {
		h = data.model.height;
	}

	if ( w ) {
		w_rule = 'width: ' + w + 'px; ';
	}

	if ( isYouTube ) {
		classes.push( 'youtube-video' );
	}

	if ( isVimeo ) {
		classes.push( 'vimeo-video' );
	}

#>
<div style="{{ w_rule }}" class="wp-video">
<video controls
	class="wp-video-shortcode {{ classes.join( ' ' ) }}"
	<# if ( w ) { #>width="{{ w }}"<# } #>
	<# if ( h ) { #>height="{{ h }}"<# } #>
	<?php
	$props = array(
		'poster'  => '',
		'preload' => 'metadata',
	);
	foreach ( $props as $key => $value ) :
		if ( empty( $value ) ) {
			?>
		<#
		if ( ! _.isUndefined( data.model.<?php echo $key; ?> ) && data.model.<?php echo $key; ?> ) {
			#> <?php echo $key; ?>="{{ data.model.<?php echo $key; ?> }}"<#
		} #>
			<?php
		} else {
			echo $key
			?>
			="{{ _.isUndefined( data.model.<?php echo $key; ?> ) ? '<?php echo $value; ?>' : data.model.<?php echo $key; ?> }}"
			<?php
		}
	endforeach;
	?>
	<#
	<?php
	foreach ( array( 'autoplay', 'loop' ) as $attr ) :
		?>
	if ( ! _.isUndefined( data.model.<?php echo $attr; ?> ) && data.model.<?php echo $attr; ?> ) {
		#> <?php echo $attr; ?><#
	}
	<?php endforeach ?>#>
>
	<# if ( ! _.isEmpty( data.model.src ) ) {
		if ( isYouTube ) { #>
		<source src="{{ data.model.src }}" type="video/youtube" />
		<# } else if ( isVimeo ) { #>
		<source src="{{ data.model.src }}" type="video/vimeo" />
		<# } else { #>
		<source src="{{ data.model.src }}" type="{{ settings.embedMimes[ data.model.src.split('.').pop() ] }}" />
		<# }
	} #>

	<?php
	foreach ( $video_types as $type ) :
		?>
	<# if ( data.model.<?php echo $type; ?> ) { #>
	<source src="{{ data.model.<?php echo $type; ?> }}" type="{{ settings.embedMimes[ '<?php echo $type; ?>' ] }}" />
	<# } #>
	<?php endforeach; ?>
	{{{ data.model.content }}}
</video>
</div>
	<?php
}

/**
 * Prints the templates used in the media manager.
 *
 * @since 3.5.0
 *
 * @global bool $is_IE
 */
function wp_print_media_templates() {
	global $is_IE;
	$class = 'media-modal wp-core-ui';
	if ( $is_IE && strpos( $_SERVER['HTTP_USER_AGENT'], 'MSIE 7' ) !== false ) {
		$class .= ' ie7';
	}

	$alt_text_description = sprintf(
		/* translators: 1: link start tag, 2: accessibility text, 3: link end tag */
		__( '%1$sDescribe the purpose of the image%2$s%3$s. Leave empty if the image is purely decorative.' ),
		'<a href="' . esc_url( 'https://www.w3.org/WAI/tutorials/images/decision-tree' ) . '" target="_blank" rel="noopener noreferrer">',
		sprintf(
			'<span class="screen-reader-text"> %s</span>',
			/* translators: accessibility text */
			__( '(opens in a new tab)' )
		),
		'</a>'
	);
	?>
	<!--[if lte IE 8]>
	<style>
		.attachment:focus {
			outline: #1e8cbe solid;
		}
		.selected.attachment {
			outline: #1e8cbe solid;
		}
	</style>
	<![endif]-->
	<script type="text/html" id="tmpl-media-frame">
		<div class="media-frame-menu"></div>
		<div class="media-frame-title"></div>
		<div class="media-frame-router"></div>
		<div class="media-frame-content"></div>
		<div class="media-frame-toolbar"></div>
		<div class="media-frame-uploader"></div>
	</script>

	<script type="text/html" id="tmpl-media-modal">
		<div tabindex="0" class="<?php echo $class; ?>">
			<button type="button" class="media-modal-close"><span class="media-modal-icon"><span class="screen-reader-text"><?php _e( 'Close media panel' ); ?></span></span></button>
			<div class="media-modal-content"></div>
		</div>
		<div class="media-modal-backdrop"></div>
	</script>

	<script type="text/html" id="tmpl-uploader-window">
		<div class="uploader-window-content">
			<h1><?php _e( 'Drop files to upload' ); ?></h1>
		</div>
	</script>

	<script type="text/html" id="tmpl-uploader-editor">
		<div class="uploader-editor-content">
			<div class="uploader-editor-title"><?php _e( 'Drop files to upload' ); ?></div>
		</div>
	</script>

	<script type="text/html" id="tmpl-uploader-inline">
		<# var messageClass = data.message ? 'has-upload-message' : 'no-upload-message'; #>
		<# if ( data.canClose ) { #>
		<button class="close dashicons dashicons-no"><span class="screen-reader-text"><?php _e( 'Close uploader' ); ?></span></button>
		<# } #>
		<div class="uploader-inline-content {{ messageClass }}">
		<# if ( data.message ) { #>
			<h2 class="upload-message">{{ data.message }}</h2>
		<# } #>
		<?php if ( ! _device_can_upload() ) : ?>
			<h2 class="upload-instructions"><?php printf( __( 'The web browser on your device cannot be used to upload files. You may be able to use the <a href="%s">native app for your device</a> instead.' ), 'https://apps.wordpress.org/' ); ?></h2>
		<?php elseif ( is_multisite() && ! is_upload_space_available() ) : ?>
			<h2 class="upload-instructions"><?php _e( 'Upload Limit Exceeded' ); ?></h2>
			<?php
			/** This action is documented in wp-admin/includes/media.php */
			do_action( 'upload_ui_over_quota' );
			?>

		<?php else : ?>
			<div class="upload-ui">
				<h2 class="upload-instructions drop-instructions"><?php _e( 'Drop files anywhere to upload' ); ?></h2>
				<p class="upload-instructions drop-instructions"><?php _ex( 'or', 'Uploader: Drop files here - or - Select Files' ); ?></p>
				<button type="button" class="browser button button-hero"><?php _e( 'Select Files' ); ?></button>
			</div>

			<div class="upload-inline-status"></div>

			<div class="post-upload-ui">
				<?php
				/** This action is documented in wp-admin/includes/media.php */
				do_action( 'pre-upload-ui' );
				/** This action is documented in wp-admin/includes/media.php */
				do_action( 'pre-plupload-upload-ui' );

				if ( 10 === remove_action( 'post-plupload-upload-ui', 'media_upload_flash_bypass' ) ) {
					/** This action is documented in wp-admin/includes/media.php */
					do_action( 'post-plupload-upload-ui' );
					add_action( 'post-plupload-upload-ui', 'media_upload_flash_bypass' );
				} else {
					/** This action is documented in wp-admin/includes/media.php */
					do_action( 'post-plupload-upload-ui' );
				}

				$max_upload_size = wp_max_upload_size();
				if ( ! $max_upload_size ) {
					$max_upload_size = 0;
				}
				?>

				<p class="max-upload-size">
				<?php
					printf( __( 'Maximum upload file size: %s.' ), esc_html( size_format( $max_upload_size ) ) );
				?>
				</p>

				<# if ( data.suggestedWidth && data.suggestedHeight ) { #>
					<p class="suggested-dimensions">
						<?php
							/* translators: 1: suggested width number, 2: suggested height number. */
							printf( __( 'Suggested image dimensions: %1$s by %2$s pixels.' ), '{{data.suggestedWidth}}', '{{data.suggestedHeight}}' );
						?>
					</p>
				<# } #>

				<?php
				/** This action is documented in wp-admin/includes/media.php */
				do_action( 'post-upload-ui' );
				?>
			</div>
		<?php endif; ?>
		</div>
	</script>

	<script type="text/html" id="tmpl-media-library-view-switcher">
		<a href="<?php echo esc_url( add_query_arg( 'mode', 'list', $_SERVER['REQUEST_URI'] ) ); ?>" class="view-list">
			<span class="screen-reader-text"><?php _e( 'List View' ); ?></span>
		</a>
		<a href="<?php echo esc_url( add_query_arg( 'mode', 'grid', $_SERVER['REQUEST_URI'] ) ); ?>" class="view-grid current">
			<span class="screen-reader-text"><?php _e( 'Grid View' ); ?></span>
		</a>
	</script>

	<script type="text/html" id="tmpl-uploader-status">
		<h2><?php _e( 'Uploading' ); ?></h2>
		<button type="button" class="button-link upload-dismiss-errors"><span class="screen-reader-text"><?php _e( 'Dismiss Errors' ); ?></span></button>

		<div class="media-progress-bar"><div></div></div>
		<div class="upload-details">
			<span class="upload-count">
				<span class="upload-index"></span> / <span class="upload-total"></span>
			</span>
			<span class="upload-detail-separator">&ndash;</span>
			<span class="upload-filename"></span>
		</div>
		<div class="upload-errors"></div>
	</script>

	<script type="text/html" id="tmpl-uploader-status-error">
		<span class="upload-error-filename">{{{ data.filename }}}</span>
		<span class="upload-error-message">{{ data.message }}</span>
	</script>

	<script type="text/html" id="tmpl-edit-attachment-frame">
		<div class="edit-media-header">
			<button class="left dashicons <# if ( ! data.hasPrevious ) { #> disabled <# } #>"><span class="screen-reader-text"><?php _e( 'Edit previous media item' ); ?></span></button>
			<button class="right dashicons <# if ( ! data.hasNext ) { #> disabled <# } #>"><span class="screen-reader-text"><?php _e( 'Edit next media item' ); ?></span></button>
		</div>
		<div class="media-frame-title"></div>
		<div class="media-frame-content"></div>
	</script>

	<script type="text/html" id="tmpl-attachment-details-two-column">
		<div class="attachment-media-view {{ data.orientation }}">
			<div class="thumbnail thumbnail-{{ data.type }}">
				<# if ( data.uploading ) { #>
					<div class="media-progress-bar"><div></div></div>
				<# } else if ( data.sizes && data.sizes.large ) { #>
					<img class="details-image" src="{{ data.sizes.large.url }}" draggable="false" alt="" />
				<# } else if ( data.sizes && data.sizes.full ) { #>
					<img class="details-image" src="{{ data.sizes.full.url }}" draggable="false" alt="" />
				<# } else if ( -1 === jQuery.inArray( data.type, [ 'audio', 'video' ] ) ) { #>
					<img class="details-image icon" src="{{ data.icon }}" draggable="false" alt="" />
				<# } #>

				<# if ( 'audio' === data.type ) { #>
				<div class="wp-media-wrapper">
					<audio style="visibility: hidden" controls class="wp-audio-shortcode" width="100%" preload="none">
						<source type="{{ data.mime }}" src="{{ data.url }}"/>
					</audio>
				</div>
				<# } else if ( 'video' === data.type ) {
					var w_rule = '';
					if ( data.width ) {
						w_rule = 'width: ' + data.width + 'px;';
					} else if ( wp.media.view.settings.contentWidth ) {
						w_rule = 'width: ' + wp.media.view.settings.contentWidth + 'px;';
					}
				#>
				<div style="{{ w_rule }}" class="wp-media-wrapper wp-video">
					<video controls="controls" class="wp-video-shortcode" preload="metadata"
						<# if ( data.width ) { #>width="{{ data.width }}"<# } #>
						<# if ( data.height ) { #>height="{{ data.height }}"<# } #>
						<# if ( data.image && data.image.src !== data.icon ) { #>poster="{{ data.image.src }}"<# } #>>
						<source type="{{ data.mime }}" src="{{ data.url }}"/>
					</video>
				</div>
				<# } #>

				<div class="attachment-actions">
					<# if ( 'image' === data.type && ! data.uploading && data.sizes && data.can.save ) { #>
					<button type="button" class="button edit-attachment"><?php _e( 'Edit Image' ); ?></button>
					<# } else if ( 'pdf' === data.subtype && data.sizes ) { #>
					<?php _e( 'Document Preview' ); ?>
					<# } #>
				</div>
			</div>
		</div>
		<div class="attachment-info">
			<span class="settings-save-status">
				<span class="spinner"></span>
				<span class="saved"><?php esc_html_e( 'Saved.' ); ?></span>
			</span>
			<div class="details">
				<div class="filename"><strong><?php _e( 'File name:' ); ?></strong> {{ data.filename }}</div>
				<div class="filename"><strong><?php _e( 'File type:' ); ?></strong> {{ data.mime }}</div>
				<div class="uploaded"><strong><?php _e( 'Uploaded on:' ); ?></strong> {{ data.dateFormatted }}</div>

				<div class="file-size"><strong><?php _e( 'File size:' ); ?></strong> {{ data.filesizeHumanReadable }}</div>
				<# if ( 'image' === data.type && ! data.uploading ) { #>
					<# if ( data.width && data.height ) { #>
						<div class="dimensions"><strong><?php _e( 'Dimensions:' ); ?></strong>
							<?php
							/* translators: 1: a number of pixels wide, 2: a number of pixels tall. */
							printf( __( '%1$s by %2$s pixels' ), '{{ data.width }}', '{{ data.height }}' );
							?>
						</div>
					<# } #>
				<# } #>

				<# if ( data.fileLength && data.fileLengthHumanReadable ) { #>
					<div class="file-length"><strong><?php _e( 'Length:' ); ?></strong>
						<span aria-hidden="true">{{ data.fileLength }}</span>
						<span class="screen-reader-text">{{ data.fileLengthHumanReadable }}</span>
					</div>
				<# } #>

				<# if ( 'audio' === data.type && data.meta.bitrate ) { #>
					<div class="bitrate">
						<strong><?php _e( 'Bitrate:' ); ?></strong> {{ Math.round( data.meta.bitrate / 1000 ) }}kb/s
						<# if ( data.meta.bitrate_mode ) { #>
						{{ ' ' + data.meta.bitrate_mode.toUpperCase() }}
						<# } #>
					</div>
				<# } #>

				<div class="compat-meta">
					<# if ( data.compat && data.compat.meta ) { #>
						{{{ data.compat.meta }}}
					<# } #>
				</div>
			</div>

			<div class="settings">
				<# var maybeReadOnly = data.can.save || data.allowLocalEdits ? '' : 'readonly'; #>
				<# if ( 'image' === data.type ) { #>
					<label class="setting" data-setting="alt">
						<span class="name"><?php _e( 'Alternative Text' ); ?></span>
						<input type="text" value="{{ data.alt }}" aria-describedby="alt-text-description" {{ maybeReadOnly }} />
					</label>
					<p class="description" id="alt-text-description"><?php echo $alt_text_description; ?></p>
				<# } #>
				<?php if ( post_type_supports( 'attachment', 'title' ) ) : ?>
				<label class="setting" data-setting="title">
					<span class="name"><?php _e( 'Title' ); ?></span>
					<input type="text" value="{{ data.title }}" {{ maybeReadOnly }} />
				</label>
				<?php endif; ?>
				<# if ( 'audio' === data.type ) { #>
				<?php
				foreach ( array(
					'artist' => __( 'Artist' ),
					'album'  => __( 'Album' ),
				) as $key => $label ) :
					?>
				<label class="setting" data-setting="<?php echo esc_attr( $key ); ?>">
					<span class="name"><?php echo $label; ?></span>
					<input type="text" value="{{ data.<?php echo $key; ?> || data.meta.<?php echo $key; ?> || '' }}" />
				</label>
				<?php endforeach; ?>
				<# } #>
				<label class="setting" data-setting="caption">
					<span class="name"><?php _e( 'Caption' ); ?></span>
					<textarea {{ maybeReadOnly }}>{{ data.caption }}</textarea>
				</label>
				<label class="setting" data-setting="description">
					<span class="name"><?php _e( 'Description' ); ?></span>
					<textarea {{ maybeReadOnly }}>{{ data.description }}</textarea>
				</label>
				<div class="setting">
					<span class="name"><?php _e( 'Uploaded By' ); ?></span>
					<span class="value">{{ data.authorName }}</span>
				</div>
				<# if ( data.uploadedToTitle ) { #>
					<div class="setting">
						<span class="name"><?php _e( 'Uploaded To' ); ?></span>
						<# if ( data.uploadedToLink ) { #>
							<span class="value"><a href="{{ data.uploadedToLink }}">{{ data.uploadedToTitle }}</a></span>
						<# } else { #>
							<span class="value">{{ data.uploadedToTitle }}</span>
						<# } #>
					</div>
				<# } #>
				<label class="setting" data-setting="url">
					<span class="name"><?php _e( 'Copy Link' ); ?></span>
					<input type="text" value="{{ data.url }}" readonly />
				</label>
				<div class="attachment-compat"></div>
			</div>

			<div class="actions">
				<a class="view-attachment" href="{{ data.link }}"><?php _e( 'View attachment page' ); ?></a>
				<# if ( data.can.save ) { #> |
					<a href="{{ data.editLink }}"><?php _e( 'Edit more details' ); ?></a>
				<# } #>
				<# if ( ! data.uploading && data.can.remove ) { #> |
					<?php if ( MEDIA_TRASH ) : ?>
						<# if ( 'trash' === data.status ) { #>
							<button type="button" class="button-link untrash-attachment"><?php _e( 'Restore from Trash' ); ?></button>
						<# } else { #>
							<button type="button" class="button-link trash-attachment"><?php _e( 'Move to Trash' ); ?></button>
						<# } #>
					<?php else : ?>
						<button type="button" class="button-link delete-attachment"><?php _e( 'Delete Permanently' ); ?></button>
					<?php endif; ?>
				<# } #>
			</div>

		</div>
	</script>

	<script type="text/html" id="tmpl-attachment">
		<div class="attachment-preview js--select-attachment type-{{ data.type }} subtype-{{ data.subtype }} {{ data.orientation }}">
			<div class="thumbnail">
				<# if ( data.uploading ) { #>
					<div class="media-progress-bar"><div style="width: {{ data.percent }}%"></div></div>
				<# } else if ( 'image' === data.type && data.sizes ) { #>
					<div class="centered">
						<img src="{{ data.size.url }}" draggable="false" alt="" />
					</div>
				<# } else { #>
					<div class="centered">
						<# if ( data.image && data.image.src && data.image.src !== data.icon ) { #>
							<img src="{{ data.image.src }}" class="thumbnail" draggable="false" alt="" />
						<# } else if ( data.sizes && data.sizes.medium ) { #>
							<img src="{{ data.sizes.medium.url }}" class="thumbnail" draggable="false" alt="" />
						<# } else { #>
							<img src="{{ data.icon }}" class="icon" draggable="false" alt="" />
						<# } #>
					</div>
					<div class="filename">
						<div>{{ data.filename }}</div>
					</div>
				<# } #>
			</div>
			<# if ( data.buttons.close ) { #>
				<button type="button" class="button-link attachment-close media-modal-icon"><span class="screen-reader-text"><?php _e( 'Remove' ); ?></span></button>
			<# } #>
		</div>
		<# if ( data.buttons.check ) { #>
			<button type="button" class="check" tabindex="-1"><span class="media-modal-icon"></span><span class="screen-reader-text"><?php _e( 'Deselect' ); ?></span></button>
		<# } #>
		<#
		var maybeReadOnly = data.can.save || data.allowLocalEdits ? '' : 'readonly';
		if ( data.describe ) {
			if ( 'image' === data.type ) { #>
				<input type="text" value="{{ data.caption }}" class="describe" data-setting="caption"
					placeholder="<?php esc_attr_e( 'Caption this image&hellip;' ); ?>" {{ maybeReadOnly }} />
			<# } else { #>
				<input type="text" value="{{ data.title }}" class="describe" data-setting="title"
					<# if ( 'video' === data.type ) { #>
						placeholder="<?php esc_attr_e( 'Describe this video&hellip;' ); ?>"
					<# } else if ( 'audio' === data.type ) { #>
						placeholder="<?php esc_attr_e( 'Describe this audio file&hellip;' ); ?>"
					<# } else { #>
						placeholder="<?php esc_attr_e( 'Describe this media file&hellip;' ); ?>"
					<# } #> {{ maybeReadOnly }} />
			<# }
		} #>
	</script>

	<script type="text/html" id="tmpl-attachment-details">
		<h2>
			<?php _e( 'Attachment Details' ); ?>
			<span class="settings-save-status">
				<span class="spinner"></span>
				<span class="saved"><?php esc_html_e( 'Saved.' ); ?></span>
			</span>
		</h2>
		<div class="attachment-info">
			<div class="thumbnail thumbnail-{{ data.type }}">
				<# if ( data.uploading ) { #>
					<div class="media-progress-bar"><div></div></div>
				<# } else if ( 'image' === data.type && data.sizes ) { #>
					<img src="{{ data.size.url }}" draggable="false" alt="" />
				<# } else { #>
					<img src="{{ data.icon }}" class="icon" draggable="false" alt="" />
				<# } #>
			</div>
			<div class="details">
				<div class="filename">{{ data.filename }}</div>
				<div class="uploaded">{{ data.dateFormatted }}</div>

				<div class="file-size">{{ data.filesizeHumanReadable }}</div>
				<# if ( 'image' === data.type && ! data.uploading ) { #>
					<# if ( data.width && data.height ) { #>
						<div class="dimensions">
							<?php
							/* translators: 1: a number of pixels wide, 2: a number of pixels tall. */
							printf( __( '%1$s by %2$s pixels' ), '{{ data.width }}', '{{ data.height }}' );
							?>
						</div>
					<# } #>

					<# if ( data.can.save && data.sizes ) { #>
						<a class="edit-attachment" href="{{ data.editLink }}&amp;image-editor" target="_blank"><?php _e( 'Edit Image' ); ?></a>
					<# } #>
				<# } #>

				<# if ( data.fileLength && data.fileLengthHumanReadable ) { #>
					<div class="file-length"><?php _e( 'Length:' ); ?>
						<span aria-hidden="true">{{ data.fileLength }}</span>
						<span class="screen-reader-text">{{ data.fileLengthHumanReadable }}</span>
					</div>
				<# } #>

				<# if ( ! data.uploading && data.can.remove ) { #>
					<?php if ( MEDIA_TRASH ) : ?>
					<# if ( 'trash' === data.status ) { #>
						<button type="button" class="button-link untrash-attachment"><?php _e( 'Restore from Trash' ); ?></button>
					<# } else { #>
						<button type="button" class="button-link trash-attachment"><?php _e( 'Move to Trash' ); ?></button>
					<# } #>
					<?php else : ?>
						<button type="button" class="button-link delete-attachment"><?php _e( 'Delete Permanently' ); ?></button>
					<?php endif; ?>
				<# } #>

				<div class="compat-meta">
					<# if ( data.compat && data.compat.meta ) { #>
						{{{ data.compat.meta }}}
					<# } #>
				</div>
			</div>
		</div>

		<# var maybeReadOnly = data.can.save || data.allowLocalEdits ? '' : 'readonly'; #>
		<# if ( 'image' === data.type ) { #>
			<label class="setting" data-setting="alt">
				<span class="name"><?php _e( 'Alt Text' ); ?></span>
				<input type="text" value="{{ data.alt }}" aria-describedby="alt-text-description" {{ maybeReadOnly }} />
			</label>
			<p class="description" id="alt-text-description"><?php echo $alt_text_description; ?></p>
		<# } #>
		<?php if ( post_type_supports( 'attachment', 'title' ) ) : ?>
		<label class="setting" data-setting="title">
			<span class="name"><?php _e( 'Title' ); ?></span>
			<input type="text" value="{{ data.title }}" {{ maybeReadOnly }} />
		</label>
		<?php endif; ?>
		<# if ( 'audio' === data.type ) { #>
		<?php
		foreach ( array(
			'artist' => __( 'Artist' ),
			'album'  => __( 'Album' ),
		) as $key => $label ) :
			?>
		<label class="setting" data-setting="<?php echo esc_attr( $key ); ?>">
			<span class="name"><?php echo $label; ?></span>
			<input type="text" value="{{ data.<?php echo $key; ?> || data.meta.<?php echo $key; ?> || '' }}" />
		</label>
		<?php endforeach; ?>
		<# } #>
		<label class="setting" data-setting="caption">
			<span class="name"><?php _e( 'Caption' ); ?></span>
			<textarea {{ maybeReadOnly }}>{{ data.caption }}</textarea>
		</label>
		<label class="setting" data-setting="description">
			<span class="name"><?php _e( 'Description' ); ?></span>
			<textarea {{ maybeReadOnly }}>{{ data.description }}</textarea>
		</label>
		<label class="setting" data-setting="url">
			<span class="name"><?php _e( 'Copy Link' ); ?></span>
			<input type="text" value="{{ data.url }}" readonly />
		</label>
	</script>

	<script type="text/html" id="tmpl-media-selection">
		<div class="selection-info">
			<span class="count"></span>
			<# if ( data.editable ) { #>
				<button type="button" class="button-link edit-selection"><?php _e( 'Edit Selection' ); ?></button>
			<# } #>
			<# if ( data.clearable ) { #>
				<button type="button" class="button-link clear-selection"><?php _e( 'Clear' ); ?></button>
			<# } #>
		</div>
		<div class="selection-view"></div>
	</script>

	<script type="text/html" id="tmpl-attachment-display-settings">
		<h2><?php _e( 'Attachment Display Settings' ); ?></h2>

		<# if ( 'image' === data.type ) { #>
			<label class="setting align">
				<span><?php _e( 'Alignment' ); ?></span>
				<select class="alignment"
					data-setting="align"
					<# if ( data.userSettings ) { #>
						data-user-setting="align"
					<# } #>>

					<option value="left">
						<?php esc_html_e( 'Left' ); ?>
					</option>
					<option value="center">
						<?php esc_html_e( 'Center' ); ?>
					</option>
					<option value="right">
						<?php esc_html_e( 'Right' ); ?>
					</option>
					<option value="none" selected>
						<?php esc_html_e( 'None' ); ?>
					</option>
				</select>
			</label>
		<# } #>

		<div class="setting">
			<label>
				<# if ( data.model.canEmbed ) { #>
					<span><?php _e( 'Embed or Link' ); ?></span>
				<# } else { #>
					<span><?php _e( 'Link To' ); ?></span>
				<# } #>

				<select class="link-to"
					data-setting="link"
					<# if ( data.userSettings && ! data.model.canEmbed ) { #>
						data-user-setting="urlbutton"
					<# } #>>

				<# if ( data.model.canEmbed ) { #>
					<option value="embed" selected>
						<?php esc_html_e( 'Embed Media Player' ); ?>
					</option>
					<option value="file">
				<# } else { #>
					<option value="none" selected>
						<?php esc_html_e( 'None' ); ?>
					</option>
					<option value="file">
				<# } #>
					<# if ( data.model.canEmbed ) { #>
						<?php esc_html_e( 'Link to Media File' ); ?>
					<# } else { #>
						<?php esc_html_e( 'Media File' ); ?>
					<# } #>
					</option>
					<option value="post">
					<# if ( data.model.canEmbed ) { #>
						<?php esc_html_e( 'Link to Attachment Page' ); ?>
					<# } else { #>
						<?php esc_html_e( 'Attachment Page' ); ?>
					<# } #>
					</option>
				<# if ( 'image' === data.type ) { #>
					<option value="custom">
						<?php esc_html_e( 'Custom URL' ); ?>
					</option>
				<# } #>
				</select>
			</label>
			<input type="text" class="link-to-custom" data-setting="linkUrl" />
		</div>

		<# if ( 'undefined' !== typeof data.sizes ) { #>
			<label class="setting">
				<span><?php _e( 'Size' ); ?></span>
				<select class="size" name="size"
					data-setting="size"
					<# if ( data.userSettings ) { #>
						data-user-setting="imgsize"
					<# } #>>
					<?php
					/** This filter is documented in wp-admin/includes/media.php */
					$sizes = apply_filters(
						'image_size_names_choose',
						array(
							'thumbnail' => __( 'Thumbnail' ),
							'medium'    => __( 'Medium' ),
							'large'     => __( 'Large' ),
							'full'      => __( 'Full Size' ),
						)
					);

					foreach ( $sizes as $value => $name ) :
						?>
						<#
						var size = data.sizes['<?php echo esc_js( $value ); ?>'];
						if ( size ) { #>
							<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, 'full' ); ?>>
								<?php echo esc_html( $name ); ?> &ndash; {{ size.width }} &times; {{ size.height }}
							</option>
						<# } #>
					<?php endforeach; ?>
				</select>
			</label>
		<# } #>
	</script>

	<script type="text/html" id="tmpl-gallery-settings">
		<h2><?php _e( 'Gallery Settings' ); ?></h2>

		<label class="setting">
			<span><?php _e( 'Link To' ); ?></span>
			<select class="link-to"
				data-setting="link"
				<# if ( data.userSettings ) { #>
					data-user-setting="urlbutton"
				<# } #>>

				<option value="post" <# if ( ! wp.media.galleryDefaults.link || 'post' == wp.media.galleryDefaults.link ) {
					#>selected="selected"<# }
				#>>
					<?php esc_html_e( 'Attachment Page' ); ?>
				</option>
				<option value="file" <# if ( 'file' == wp.media.galleryDefaults.link ) { #>selected="selected"<# } #>>
					<?php esc_html_e( 'Media File' ); ?>
				</option>
				<option value="none" <# if ( 'none' == wp.media.galleryDefaults.link ) { #>selected="selected"<# } #>>
					<?php esc_html_e( 'None' ); ?>
				</option>
			</select>
		</label>

		<label class="setting">
			<span><?php _e( 'Columns' ); ?></span>
			<select class="columns" name="columns"
				data-setting="columns">
				<?php for ( $i = 1; $i <= 9; $i++ ) : ?>
					<option value="<?php echo esc_attr( $i ); ?>" <#
						if ( <?php echo $i; ?> == wp.media.galleryDefaults.columns ) { #>selected="selected"<# }
					#>>
						<?php echo esc_html( $i ); ?>
					</option>
				<?php endfor; ?>
			</select>
		</label>

		<label class="setting">
			<span><?php _e( 'Random Order' ); ?></span>
			<input type="checkbox" data-setting="_orderbyRandom" />
		</label>

		<label class="setting size">
			<span><?php _e( 'Size' ); ?></span>
			<select class="size" name="size"
				data-setting="size"
				<# if ( data.userSettings ) { #>
					data-user-setting="imgsize"
				<# } #>
				>
				<?php
				/** This filter is documented in wp-admin/includes/media.php */
				$size_names = apply_filters(
					'image_size_names_choose',
					array(
						'thumbnail' => __( 'Thumbnail' ),
						'medium'    => __( 'Medium' ),
						'large'     => __( 'Large' ),
						'full'      => __( 'Full Size' ),
					)
				);

				foreach ( $size_names as $size => $label ) :
					?>
					<option value="<?php echo esc_attr( $size ); ?>">
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</label>
	</script>

	<script type="text/html" id="tmpl-playlist-settings">
		<h2><?php _e( 'Playlist Settings' ); ?></h2>

		<# var emptyModel = _.isEmpty( data.model ),
			isVideo = 'video' === data.controller.get('library').props.get('type'); #>

		<label class="setting">
			<input type="checkbox" data-setting="tracklist" <# if ( emptyModel ) { #>
				checked="checked"
			<# } #> />
			<# if ( isVideo ) { #>
			<span><?php _e( 'Show Video List' ); ?></span>
			<# } else { #>
			<span><?php _e( 'Show Tracklist' ); ?></span>
			<# } #>
		</label>

		<# if ( ! isVideo ) { #>
		<label class="setting">
			<input type="checkbox" data-setting="artists" <# if ( emptyModel ) { #>
				checked="checked"
			<# } #> />
			<span><?php _e( 'Show Artist Name in Tracklist' ); ?></span>
		</label>
		<# } #>

		<label class="setting">
			<input type="checkbox" data-setting="images" <# if ( emptyModel ) { #>
				checked="checked"
			<# } #> />
			<span><?php _e( 'Show Images' ); ?></span>
		</label>
	</script>

	<script type="text/html" id="tmpl-embed-link-settings">
		<label class="setting link-text">
			<span><?php _e( 'Link Text' ); ?></span>
			<input type="text" class="alignment" data-setting="linkText" />
		</label>
		<div class="embed-container" style="display: none;">
			<div class="embed-preview"></div>
		</div>
	</script>

	<script type="text/html" id="tmpl-embed-image-settings">
		<div class="thumbnail">
			<img src="{{ data.model.url }}" draggable="false" alt="" />
		</div>

		<label class="setting alt-text has-description">
			<span><?php _e( 'Alternative Text' ); ?></span>
			<input type="text" data-setting="alt" aria-describedby="alt-text-description" />
		</label>
		<p class="description" id="alt-text-description"><?php echo $alt_text_description; ?></p>

		<?php
		/** This filter is documented in wp-admin/includes/media.php */
		if ( ! apply_filters( 'disable_captions', '' ) ) :
			?>
			<label class="setting caption">
				<span><?php _e( 'Caption' ); ?></span>
				<textarea data-setting="caption" />
			</label>
		<?php endif; ?>

		<div class="setting align">
			<span><?php _e( 'Align' ); ?></span>
			<div class="button-group button-large" data-setting="align">
				<button class="button" value="left">
					<?php esc_html_e( 'Left' ); ?>
				</button>
				<button class="button" value="center">
					<?php esc_html_e( 'Center' ); ?>
				</button>
				<button class="button" value="right">
					<?php esc_html_e( 'Right' ); ?>
				</button>
				<button class="button active" value="none">
					<?php esc_html_e( 'None' ); ?>
				</button>
			</div>
		</div>

		<div class="setting link-to">
			<span><?php _e( 'Link To' ); ?></span>
			<div class="button-group button-large" data-setting="link">
				<button class="button" value="file">
					<?php esc_html_e( 'Image URL' ); ?>
				</button>
				<button class="button" value="custom">
					<?php esc_html_e( 'Custom URL' ); ?>
				</button>
				<button class="button active" value="none">
					<?php esc_html_e( 'None' ); ?>
				</button>
			</div>
			<input type="text" class="link-to-custom" data-setting="linkUrl" />
		</div>
	</script>

	<script type="text/html" id="tmpl-image-details">
		<div class="media-embed">
			<div class="embed-media-settings">
				<div class="column-image">
					<div class="image">
						<img src="{{ data.model.url }}" draggable="false" alt="" />

						<# if ( data.attachment && window.imageEdit ) { #>
							<div class="actions">
								<input type="button" class="edit-attachment button" value="<?php esc_attr_e( 'Edit Original' ); ?>" />
								<input type="button" class="replace-attachment button" value="<?php esc_attr_e( 'Replace' ); ?>" />
							</div>
						<# } #>
					</div>
				</div>
				<div class="column-settings">
					<label class="setting alt-text has-description">
						<span><?php _e( 'Alternative Text' ); ?></span>
						<input type="text" data-setting="alt" value="{{ data.model.alt }}" aria-describedby="alt-text-description" />
					</label>
					<p class="description" id="alt-text-description"><?php echo $alt_text_description; ?></p>

					<?php
					/** This filter is documented in wp-admin/includes/media.php */
					if ( ! apply_filters( 'disable_captions', '' ) ) :
						?>
						<label class="setting caption">
							<span><?php _e( 'Caption' ); ?></span>
							<textarea data-setting="caption">{{ data.model.caption }}</textarea>
						</label>
					<?php endif; ?>

					<h2><?php _e( 'Display Settings' ); ?></h2>
					<div class="setting align">
						<span><?php _e( 'Align' ); ?></span>
						<div class="button-group button-large" data-setting="align">
							<button class="button" value="left">
								<?php esc_html_e( 'Left' ); ?>
							</button>
							<button class="button" value="center">
								<?php esc_html_e( 'Center' ); ?>
							</button>
							<button class="button" value="right">
								<?php esc_html_e( 'Right' ); ?>
							</button>
							<button class="button active" value="none">
								<?php esc_html_e( 'None' ); ?>
							</button>
						</div>
					</div>

					<# if ( data.attachment ) { #>
						<# if ( 'undefined' !== typeof data.attachment.sizes ) { #>
							<label class="setting size">
								<span><?php _e( 'Size' ); ?></span>
								<select class="size" name="size"
									data-setting="size"
									<# if ( data.userSettings ) { #>
										data-user-setting="imgsize"
									<# } #>>
									<?php
									/** This filter is documented in wp-admin/includes/media.php */
									$sizes = apply_filters(
										'image_size_names_choose',
										array(
											'thumbnail' => __( 'Thumbnail' ),
											'medium'    => __( 'Medium' ),
											'large'     => __( 'Large' ),
											'full'      => __( 'Full Size' ),
										)
									);

									foreach ( $sizes as $value => $name ) :
										?>
										<#
										var size = data.sizes['<?php echo esc_js( $value ); ?>'];
										if ( size ) { #>
											<option value="<?php echo esc_attr( $value ); ?>">
												<?php echo esc_html( $name ); ?> &ndash; {{ size.width }} &times; {{ size.height }}
											</option>
										<# } #>
									<?php endforeach; ?>
									<option value="<?php echo esc_attr( 'custom' ); ?>">
										<?php _e( 'Custom Size' ); ?>
									</option>
								</select>
							</label>
						<# } #>
							<div class="custom-size<# if ( data.model.size !== 'custom' ) { #> hidden<# } #>">
								<label><span><?php _e( 'Width' ); ?> <small>(px)</small></span> <input data-setting="customWidth" type="number" step="1" value="{{ data.model.customWidth }}" /></label><span class="sep">&times;</span><label><span><?php _e( 'Height' ); ?> <small>(px)</small></span><input data-setting="customHeight" type="number" step="1" value="{{ data.model.customHeight }}" /></label>
							</div>
					<# } #>

					<div class="setting link-to">
						<span><?php _e( 'Link To' ); ?></span>
						<select data-setting="link">
						<# if ( data.attachment ) { #>
							<option value="file">
								<?php esc_html_e( 'Media File' ); ?>
							</option>
							<option value="post">
								<?php esc_html_e( 'Attachment Page' ); ?>
							</option>
						<# } else { #>
							<option value="file">
								<?php esc_html_e( 'Image URL' ); ?>
							</option>
						<# } #>
							<option value="custom">
								<?php esc_html_e( 'Custom URL' ); ?>
							</option>
							<option value="none">
								<?php esc_html_e( 'None' ); ?>
							</option>
						</select>
						<input type="text" class="link-to-custom" data-setting="linkUrl" />
					</div>
					<div class="advanced-section">
						<h2><button type="button" class="button-link advanced-toggle"><?php _e( 'Advanced Options' ); ?></button></h2>
						<div class="advanced-settings hidden">
							<div class="advanced-image">
								<label class="setting title-text">
									<span><?php _e( 'Image Title Attribute' ); ?></span>
									<input type="text" data-setting="title" value="{{ data.model.title }}" />
								</label>
								<label class="setting extra-classes">
									<span><?php _e( 'Image CSS Class' ); ?></span>
									<input type="text" data-setting="extraClasses" value="{{ data.model.extraClasses }}" />
								</label>
							</div>
							<div class="advanced-link">
								<div class="setting link-target">
									<label><input type="checkbox" data-setting="linkTargetBlank" value="_blank" <# if ( data.model.linkTargetBlank ) { #>checked="checked"<# } #>><?php _e( 'Open link in a new tab' ); ?></label>
								</div>
								<label class="setting link-rel">
									<span><?php _e( 'Link Rel' ); ?></span>
									<input type="text" data-setting="linkRel" value="{{ data.model.linkRel }}" />
								</label>
								<label class="setting link-class-name">
									<span><?php _e( 'Link CSS Class' ); ?></span>
									<input type="text" data-setting="linkClassName" value="{{ data.model.linkClassName }}" />
								</label>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</script>

	<script type="text/html" id="tmpl-image-editor">
		<div id="media-head-{{ data.id }}"></div>
		<div id="image-editor-{{ data.id }}"></div>
	</script>

	<script type="text/html" id="tmpl-audio-details">
		<# var ext, html5types = {
			mp3: wp.media.view.settings.embedMimes.mp3,
			ogg: wp.media.view.settings.embedMimes.ogg
		}; #>

		<?php $audio_types = wp_get_audio_extensions(); ?>
		<div class="media-embed media-embed-details">
			<div class="embed-media-settings embed-audio-settings">
				<?php wp_underscore_audio_template(); ?>

				<# if ( ! _.isEmpty( data.model.src ) ) {
					ext = data.model.src.split('.').pop();
					if ( html5types[ ext ] ) {
						delete html5types[ ext ];
					}
				#>
				<div class="setting">
					<label for="audio-source"><?php _e( 'URL' ); ?></label>
					<input type="text" id="audio-source" readonly data-setting="src" value="{{ data.model.src }}" />
					<button type="button" class="button-link remove-setting"><?php _e( 'Remove audio source' ); ?></button>
				</div>
				<# } #>
				<?php

				foreach ( $audio_types as $type ) :
					?>
				<# if ( ! _.isEmpty( data.model.<?php echo $type; ?> ) ) {
					if ( ! _.isUndefined( html5types.<?php echo $type; ?> ) ) {
						delete html5types.<?php echo $type; ?>;
					}
				#>
				<div class="setting">
					<label for="<?php echo $type . '-source'; ?>"><?php echo strtoupper( $type ); ?></span>
					<input type="text" id="<?php echo $type . '-source'; ?>" readonly data-setting="<?php echo $type; ?>" value="{{ data.model.<?php echo $type; ?> }}" />
					<button type="button" class="button-link remove-setting"><?php _e( 'Remove audio source' ); ?></button>
				</div>
				<# } #>
				<?php endforeach ?>

				<# if ( ! _.isEmpty( html5types ) ) { #>
				<div class="setting">
					<span><?php _e( 'Add alternate sources for maximum HTML5 playback:' ); ?></span>
					<div class="button-large">
					<# _.each( html5types, function (mime, type) { #>
					<button class="button add-media-source" data-mime="{{ mime }}">{{ type }}</button>
					<# } ) #>
					</div>
				</div>
				<# } #>

				<div class="setting preload">
					<span><?php _e( 'Preload' ); ?></span>
					<div class="button-group button-large" data-setting="preload">
						<button class="button" value="auto"><?php _ex( 'Auto', 'auto preload' ); ?></button>
						<button class="button" value="metadata"><?php _e( 'Metadata' ); ?></button>
						<button class="button active" value="none"><?php _e( 'None' ); ?></button>
					</div>
				</div>

				<label class="setting checkbox-setting autoplay">
					<input type="checkbox" data-setting="autoplay" />
					<span><?php _e( 'Autoplay' ); ?></span>
				</label>

				<label class="setting checkbox-setting">
					<input type="checkbox" data-setting="loop" />
					<span><?php _e( 'Loop' ); ?></span>
				</label>
			</div>
		</div>
	</script>

	<script type="text/html" id="tmpl-video-details">
		<# var ext, html5types = {
			mp4: wp.media.view.settings.embedMimes.mp4,
			ogv: wp.media.view.settings.embedMimes.ogv,
			webm: wp.media.view.settings.embedMimes.webm
		}; #>

		<?php $video_types = wp_get_video_extensions(); ?>
		<div class="media-embed media-embed-details">
			<div class="embed-media-settings embed-video-settings">
				<div class="wp-video-holder">
				<#
				var w = ! data.model.width || data.model.width > 640 ? 640 : data.model.width,
					h = ! data.model.height ? 360 : data.model.height;

				if ( data.model.width && w !== data.model.width ) {
					h = Math.ceil( ( h * w ) / data.model.width );
				}
				#>

				<?php wp_underscore_video_template(); ?>

				<# if ( ! _.isEmpty( data.model.src ) ) {
					ext = data.model.src.split('.').pop();
					if ( html5types[ ext ] ) {
						delete html5types[ ext ];
					}
				#>
				<div class="setting">
					<label for="video-source"><?php _e( 'URL' ); ?></label>
					<input type="text" id="video-source" readonly data-setting="src" value="{{ data.model.src }}" />
					<button type="button" class="button-link remove-setting"><?php _e( 'Remove video source' ); ?></button>
				</div>
				<# } #>
				<?php
				foreach ( $video_types as $type ) :
					?>
				<# if ( ! _.isEmpty( data.model.<?php echo $type; ?> ) ) {
					if ( ! _.isUndefined( html5types.<?php echo $type; ?> ) ) {
						delete html5types.<?php echo $type; ?>;
					}
				#>
				<div class="setting">
					<label for="<?php echo $type . '-source'; ?>"><?php echo strtoupper( $type ); ?></label>
					<input type="text" id="<?php echo $type . '-source'; ?>" readonly data-setting="<?php echo $type; ?>" value="{{ data.model.<?php echo $type; ?> }}" />
					<button type="button" class="button-link remove-setting"><?php _e( 'Remove video source' ); ?></button>
				</div>
				<# } #>
				<?php endforeach ?>
				</div>

				<# if ( ! _.isEmpty( html5types ) ) { #>
				<div class="setting">
					<span><?php _e( 'Add alternate sources for maximum HTML5 playback:' ); ?></span>
					<div class="button-large">
					<# _.each( html5types, function (mime, type) { #>
					<button class="button add-media-source" data-mime="{{ mime }}">{{ type }}</button>
					<# } ) #>
					</div>
				</div>
				<# } #>

				<# if ( ! _.isEmpty( data.model.poster ) ) { #>
				<div class="setting">
					<label for="poster-image"><?php _e( 'Poster Image' ); ?></label>
					<input type="text" id="poster-image" readonly data-setting="poster" value="{{ data.model.poster }}" />
					<button type="button" class="button-link remove-setting"><?php _e( 'Remove poster image' ); ?></button>
				</div>
				<# } #>
				<div class="setting preload">
					<span><?php _e( 'Preload' ); ?></span>
					<div class="button-group button-large" data-setting="preload">
						<button class="button" value="auto"><?php _ex( 'Auto', 'auto preload' ); ?></button>
						<button class="button" value="metadata"><?php _e( 'Metadata' ); ?></button>
						<button class="button active" value="none"><?php _e( 'None' ); ?></button>
					</div>
				</div>

				<label class="setting checkbox-setting autoplay">
					<input type="checkbox" data-setting="autoplay" />
					<span><?php _e( 'Autoplay' ); ?></span>
				</label>

				<label class="setting checkbox-setting">
					<input type="checkbox" data-setting="loop" />
					<span><?php _e( 'Loop' ); ?></span>
				</label>

				<div class="setting" data-setting="content">
					<#
					var content = '';
					if ( ! _.isEmpty( data.model.content ) ) {
						var tracks = jQuery( data.model.content ).filter( 'track' );
						_.each( tracks.toArray(), function (track) {
							content += track.outerHTML; #>
						<label for="video-track"><?php _e( 'Tracks (subtitles, captions, descriptions, chapters, or metadata)' ); ?></span>
						<input class="content-track" type="text" id="video-track" readonly value="{{ track.outerHTML }}" />
						<button type="button" class="button-link remove-setting remove-track"><?php _ex( 'Remove video track', 'media' ); ?></button>
						<# } ); #>
					<# } else { #>
					<span><?php _e( 'Tracks (subtitles, captions, descriptions, chapters, or metadata)' ); ?></span>
					<em><?php _e( 'There are no associated subtitles.' ); ?></em>
					<# } #>
					<textarea class="hidden content-setting">{{ content }}</textarea>
				</div>
			</div>
		</div>
	</script>

	<script type="text/html" id="tmpl-editor-gallery">
		<# if ( data.attachments.length ) { #>
			<div class="gallery gallery-columns-{{ data.columns }}">
				<# _.each( data.attachments, function( attachment, index ) { #>
					<dl class="gallery-item">
						<dt class="gallery-icon">
							<# if ( attachment.thumbnail ) { #>
								<img src="{{ attachment.thumbnail.url }}" width="{{ attachment.thumbnail.width }}" height="{{ attachment.thumbnail.height }}" alt="" />
							<# } else { #>
								<img src="{{ attachment.url }}" alt="" />
							<# } #>
						</dt>
						<# if ( attachment.caption ) { #>
							<dd class="wp-caption-text gallery-caption">
								{{{ data.verifyHTML( attachment.caption ) }}}
							</dd>
						<# } #>
					</dl>
					<# if ( index % data.columns === data.columns - 1 ) { #>
						<br style="clear: both;">
					<# } #>
				<# } ); #>
			</div>
		<# } else { #>
			<div class="wpview-error">
				<div class="dashicons dashicons-format-gallery"></div><p><?php _e( 'No items found.' ); ?></p>
			</div>
		<# } #>
	</script>

	<script type="text/html" id="tmpl-crop-content">
		<img class="crop-image" src="{{ data.url }}" alt="<?php esc_attr_e( 'Image crop area preview. Requires mouse interaction.' ); ?>">
		<div class="upload-errors"></div>
	</script>

	<script type="text/html" id="tmpl-site-icon-preview">
		<h2><?php _e( 'Preview' ); ?></h2>
		<strong aria-hidden="true"><?php _e( 'As a browser icon' ); ?></strong>
		<div class="favicon-preview">
			<img src="<?php echo esc_url( admin_url( 'images/' . ( is_rtl() ? 'browser-rtl.png' : 'browser.png' ) ) ); ?>" class="browser-preview" width="182" height="" alt="" />

			<div class="favicon">
				<img id="preview-favicon" src="{{ data.url }}" alt="<?php esc_attr_e( 'Preview as a browser icon' ); ?>"/>
			</div>
			<span class="browser-title" aria-hidden="true"><?php bloginfo( 'name' ); ?></span>
		</div>

		<strong aria-hidden="true"><?php _e( 'As an app icon' ); ?></strong>
		<div class="app-icon-preview">
			<img id="preview-app-icon" src="{{ data.url }}" alt="<?php esc_attr_e( 'Preview as an app icon' ); ?>"/>
		</div>
	</script>

	<?php

	/**
	 * Fires when the custom Backbone media templates are printed.
	 *
	 * @since 3.5.0
	 */
	do_action( 'print_media_templates' );
}
