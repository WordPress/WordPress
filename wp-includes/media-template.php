<?php
/**
 * WordPress media templates.
 *
 * @package WordPress
 * @subpackage Media
 * @since 3.5.0
 */

/**
 * Prints the templates used in the media manager.
 *
 * @since 3.5.0
 */
function wp_print_media_templates() {
	global $is_IE;
	$class = 'media-modal wp-core-ui';
	if ( $is_IE && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 7') !== false )
		$class .= ' ie7';
	?>
	<script type="text/html" id="tmpl-media-frame">
		<div class="media-frame-menu"></div>
		<div class="media-frame-title"></div>
		<div class="media-frame-router"></div>
		<div class="media-frame-content"></div>
		<div class="media-frame-toolbar"></div>
		<div class="media-frame-uploader"></div>
	</script>

	<script type="text/html" id="tmpl-media-modal">
		<div class="<?php echo $class; ?>">
			<a class="media-modal-close" href="#" title="<?php esc_attr_e('Close'); ?>"><span class="media-modal-icon"></span></a>
			<div class="media-modal-content"></div>
		</div>
		<div class="media-modal-backdrop"></div>
	</script>

	<script type="text/html" id="tmpl-uploader-window">
		<div class="uploader-window-content">
			<h3><?php _e( 'Drop files to upload' ); ?></h3>
		</div>
	</script>

	<script type="text/html" id="tmpl-uploader-inline">
		<# var messageClass = data.message ? 'has-upload-message' : 'no-upload-message'; #>
		<div class="uploader-inline-content {{ messageClass }}">
		<# if ( data.message ) { #>
			<h3 class="upload-message">{{ data.message }}</h3>
		<# } #>
		<?php if ( ! _device_can_upload() ) : ?>
			<h3 class="upload-instructions"><?php printf( __('The web browser on your device cannot be used to upload files. You may be able to use the <a href="%s">native app for your device</a> instead.'), 'http://wordpress.org/mobile/' ); ?></h3>
		<?php elseif ( is_multisite() && ! is_upload_space_available() ) : ?>
			<h3 class="upload-instructions"><?php _e( 'Upload Limit Exceeded' ); ?></h3>
			<?php do_action( 'upload_ui_over_quota' ); ?>

		<?php else : ?>
			<div class="upload-ui">
				<h3 class="upload-instructions drop-instructions"><?php _e( 'Drop files anywhere to upload' ); ?></h3>
				<a href="#" class="browser button button-hero"><?php _e( 'Select Files' ); ?></a>
			</div>

			<div class="upload-inline-status"></div>

			<div class="post-upload-ui">
				<?php
				do_action( 'pre-upload-ui' );
				do_action( 'pre-plupload-upload-ui' );

				if ( 10 === remove_action( 'post-plupload-upload-ui', 'media_upload_flash_bypass' ) ) {
					do_action( 'post-plupload-upload-ui' );
					add_action( 'post-plupload-upload-ui', 'media_upload_flash_bypass' );
				} else {
					do_action( 'post-plupload-upload-ui' );
				}

				$upload_size_unit = $max_upload_size = wp_max_upload_size();
				$byte_sizes = array( 'KB', 'MB', 'GB' );

				for ( $u = -1; $upload_size_unit > 1024 && $u < count( $byte_sizes ) - 1; $u++ ) {
					$upload_size_unit /= 1024;
				}

				if ( $u < 0 ) {
					$upload_size_unit = 0;
					$u = 0;
				} else {
					$upload_size_unit = (int) $upload_size_unit;
				}

				?>

				<p class="max-upload-size"><?php
					printf( __( 'Maximum upload file size: %d%s.' ), esc_html($upload_size_unit), esc_html($byte_sizes[$u]) );
				?></p>

				<?php if ( ( $GLOBALS['is_IE'] || $GLOBALS['is_opera']) && $max_upload_size > 100 * 1024 * 1024 ) :
					$browser_uploader = admin_url( 'media-new.php?browser-uploader&post_id=' ) . '{{ data.postId }}';
					?>
					<p class="big-file-warning"><?php printf( __( 'Your browser has some limitations uploading large files with the multi-file uploader. Please use the <a href="%1$s" target="%2$s">browser uploader</a> for files over 100MB.' ),
						$browser_uploader, '_blank' ); ?></p>
				<?php endif; ?>

				<?php do_action( 'post-upload-ui' ); ?>
			</div>
		<?php endif; ?>
		</div>
	</script>

	<script type="text/html" id="tmpl-uploader-status">
		<h3><?php _e( 'Uploading' ); ?></h3>
		<a class="upload-dismiss-errors" href="#"><?php _e('Dismiss Errors'); ?></a>

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
		<span class="upload-error-label"><?php _e('Error'); ?></span>
		<span class="upload-error-filename">{{{ data.filename }}}</span>
		<span class="upload-error-message">{{ data.message }}</span>
	</script>

	<script type="text/html" id="tmpl-attachment">
		<div class="attachment-preview type-{{ data.type }} subtype-{{ data.subtype }} {{ data.orientation }}">
			<# if ( data.uploading ) { #>
				<div class="media-progress-bar"><div></div></div>
			<# } else if ( 'image' === data.type ) { #>
				<div class="thumbnail">
					<div class="centered">
						<img src="{{ data.size.url }}" draggable="false" />
					</div>
				</div>
			<# } else { #>
				<img src="{{ data.icon }}" class="icon" draggable="false" />
				<div class="filename">
					<div>{{ data.filename }}</div>
				</div>
			<# } #>

			<# if ( data.buttons.close ) { #>
				<a class="close media-modal-icon" href="#" title="<?php _e('Remove'); ?>"></a>
			<# } #>

			<# if ( data.buttons.check ) { #>
				<a class="check" href="#" title="<?php _e('Deselect'); ?>"><div class="media-modal-icon"></div></a>
			<# } #>
		</div>
		<#
		var maybeReadOnly = data.can.save || data.allowLocalEdits ? '' : 'readonly';
		if ( data.describe ) { #>
			<# if ( 'image' === data.type ) { #>
				<input type="text" value="{{ data.caption }}" class="describe" data-setting="caption"
					placeholder="<?php esc_attr_e('Caption this image&hellip;'); ?>" {{ maybeReadOnly }} />
			<# } else { #>
				<input type="text" value="{{ data.title }}" class="describe" data-setting="title"
					<# if ( 'video' === data.type ) { #>
						placeholder="<?php esc_attr_e('Describe this video&hellip;'); ?>"
					<# } else if ( 'audio' === data.type ) { #>
						placeholder="<?php esc_attr_e('Describe this audio file&hellip;'); ?>"
					<# } else { #>
						placeholder="<?php esc_attr_e('Describe this media file&hellip;'); ?>"
					<# } #> {{ maybeReadOnly }} />
			<# } #>
		<# } #>
	</script>

	<script type="text/html" id="tmpl-attachment-details">
		<h3>
			<?php _e('Attachment Details'); ?>

			<span class="settings-save-status">
				<span class="spinner"></span>
				<span class="saved"><?php esc_html_e('Saved.'); ?></span>
			</span>
		</h3>
		<div class="attachment-info">
			<div class="thumbnail">
				<# if ( data.uploading ) { #>
					<div class="media-progress-bar"><div></div></div>
				<# } else if ( 'image' === data.type ) { #>
					<img src="{{ data.size.url }}" draggable="false" />
				<# } else { #>
					<img src="{{ data.icon }}" class="icon" draggable="false" />
				<# } #>
			</div>
			<div class="details">
				<div class="filename">{{ data.filename }}</div>
				<div class="uploaded">{{ data.dateFormatted }}</div>

				<# if ( 'image' === data.type && ! data.uploading ) { #>
					<# if ( data.width && data.height ) { #>
						<div class="dimensions">{{ data.width }} &times; {{ data.height }}</div>
					<# } #>

					<# if ( data.can.save ) { #>
						<a class="edit-attachment" href="{{ data.editLink }}&amp;image-editor" target="_blank"><?php _e( 'Edit Image' ); ?></a>
						<a class="refresh-attachment" href="#"><?php _e( 'Refresh' ); ?></a>
					<# } #>
				<# } #>

				<# if ( data.fileLength ) { #>
					<div class="file-length"><?php _e( 'Length:' ); ?> {{ data.fileLength }}</div>
				<# } #>

				<# if ( ! data.uploading && data.can.remove ) { #>
					<a class="delete-attachment" href="#"><?php _e( 'Delete Permanently' ); ?></a>
				<# } #>

				<div class="compat-meta">
					<# if ( data.compat && data.compat.meta ) { #>
						{{{ data.compat.meta }}}
					<# } #>
				</div>
			</div>
		</div>

		<# var maybeReadOnly = data.can.save || data.allowLocalEdits ? '' : 'readonly'; #>
			<label class="setting" data-setting="title">
				<span><?php _e('Title'); ?></span>
				<input type="text" value="{{ data.title }}" {{ maybeReadOnly }} />
			</label>
			<label class="setting" data-setting="caption">
				<span><?php _e('Caption'); ?></span>
				<textarea {{ maybeReadOnly }}>{{ data.caption }}</textarea>
			</label>
		<# if ( 'image' === data.type ) { #>
			<label class="setting" data-setting="alt">
				<span><?php _e('Alt Text'); ?></span>
				<input type="text" value="{{ data.alt }}" {{ maybeReadOnly }} />
			</label>
		<# } #>
			<label class="setting" data-setting="description">
				<span><?php _e('Description'); ?></span>
				<textarea {{ maybeReadOnly }}>{{ data.description }}</textarea>
			</label>
	</script>

	<script type="text/html" id="tmpl-media-selection">
		<div class="selection-info">
			<span class="count"></span>
			<# if ( data.editable ) { #>
				<a class="edit-selection" href="#"><?php _e('Edit'); ?></a>
			<# } #>
			<# if ( data.clearable ) { #>
				<a class="clear-selection" href="#"><?php _e('Clear'); ?></a>
			<# } #>
		</div>
		<div class="selection-view"></div>
	</script>

	<script type="text/html" id="tmpl-attachment-display-settings">
		<h3><?php _e('Attachment Display Settings'); ?></h3>

		<# if ( 'image' === data.type ) { #>
			<label class="setting">
				<span><?php _e('Alignment'); ?></span>
				<select class="alignment"
					data-setting="align"
					<# if ( data.userSettings ) { #>
						data-user-setting="align"
					<# } #>>

					<option value="left">
						<?php esc_attr_e('Left'); ?>
					</option>
					<option value="center">
						<?php esc_attr_e('Center'); ?>
					</option>
					<option value="right">
						<?php esc_attr_e('Right'); ?>
					</option>
					<option value="none" selected>
						<?php esc_attr_e('None'); ?>
					</option>
				</select>
			</label>
		<# } #>

		<div class="setting">
			<label>
				<# if ( data.model.canEmbed ) { #>
					<span><?php _e('Embed or Link'); ?></span>
				<# } else { #>
					<span><?php _e('Link To'); ?></span>
				<# } #>

				<select class="link-to"
					data-setting="link"
					<# if ( data.userSettings && ! data.model.canEmbed ) { #>
						data-user-setting="urlbutton"
					<# } #>>

				<# if ( data.model.canEmbed ) { #>
					<option value="embed" selected>
						<?php esc_attr_e('Embed Media Player'); ?>
					</option>
					<option value="file">
				<# } else { #>
					<option value="file" selected>
				<# } #>
					<# if ( data.model.canEmbed ) { #>
						<?php esc_attr_e('Link to Media File'); ?>
					<# } else { #>
						<?php esc_attr_e('Media File'); ?>
					<# } #>
					</option>
					<option value="post">
					<# if ( data.model.canEmbed ) { #>
						<?php esc_attr_e('Link to Attachment Page'); ?>
					<# } else { #>
						<?php esc_attr_e('Attachment Page'); ?>
					<# } #>
					</option>
				<# if ( 'image' === data.type ) { #>
					<option value="custom">
						<?php esc_attr_e('Custom URL'); ?>
					</option>
					<option value="none">
						<?php esc_attr_e('None'); ?>
					</option>
				<# } #>
				</select>
			</label>
			<input type="text" class="link-to-custom" data-setting="linkUrl" />
		</div>

		<# if ( 'undefined' !== typeof data.sizes ) { #>
			<label class="setting">
				<span><?php _e('Size'); ?></span>
				<select class="size" name="size"
					data-setting="size"
					<# if ( data.userSettings ) { #>
						data-user-setting="imgsize"
					<# } #>>
					<?php

					$sizes = apply_filters( 'image_size_names_choose', array(
						'thumbnail' => __('Thumbnail'),
						'medium'    => __('Medium'),
						'large'     => __('Large'),
						'full'      => __('Full Size'),
					) );

					foreach ( $sizes as $value => $name ) : ?>
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
		<h3><?php _e('Gallery Settings'); ?></h3>

		<label class="setting">
			<span><?php _e('Link To'); ?></span>
			<select class="link-to"
				data-setting="link"
				<# if ( data.userSettings ) { #>
					data-user-setting="urlbutton"
				<# } #>>

				<option value="post" selected>
					<?php esc_attr_e('Attachment Page'); ?>
				</option>
				<option value="file">
					<?php esc_attr_e('Media File'); ?>
				</option>
				<option value="none">
					<?php esc_attr_e('None'); ?>
				</option>
			</select>
		</label>

		<label class="setting">
			<span><?php _e('Columns'); ?></span>
			<select class="columns" name="columns"
				data-setting="columns">
				<?php for ( $i = 1; $i <= 9; $i++ ) : ?>
					<option value="<?php echo esc_attr( $i ); ?>" <?php selected( $i, 3 ); ?>>
						<?php echo esc_html( $i ); ?>
					</option>
				<?php endfor; ?>
			</select>
		</label>

		<label class="setting">
			<span><?php _e( 'Random Order' ); ?></span>
			<input type="checkbox" data-setting="_orderbyRandom" />
		</label>
	</script>

	<script type="text/html" id="tmpl-embed-link-settings">
		<label class="setting">
			<span><?php _e('Title'); ?></span>
			<input type="text" class="alignment" data-setting="title" />
		</label>
	</script>

	<script type="text/html" id="tmpl-embed-image-settings">
		<div class="thumbnail">
			<img src="{{ data.model.url }}" draggable="false" />
		</div>

		<?php if ( ! apply_filters( 'disable_captions', '' ) ) : ?>
			<label class="setting caption">
				<span><?php _e('Caption'); ?></span>
				<textarea data-setting="caption" />
			</label>
		<?php endif; ?>

		<label class="setting alt-text">
			<span><?php _e('Alt Text'); ?></span>
			<input type="text" data-setting="alt" />
		</label>

		<div class="setting align">
			<span><?php _e('Align'); ?></span>
			<div class="button-group button-large" data-setting="align">
				<button class="button" value="left">
					<?php esc_attr_e('Left'); ?>
				</button>
				<button class="button" value="center">
					<?php esc_attr_e('Center'); ?>
				</button>
				<button class="button" value="right">
					<?php esc_attr_e('Right'); ?>
				</button>
				<button class="button active" value="none">
					<?php esc_attr_e('None'); ?>
				</button>
			</div>
		</div>

		<div class="setting link-to">
			<span><?php _e('Link To'); ?></span>
			<div class="button-group button-large" data-setting="link">
				<button class="button" value="file">
					<?php esc_attr_e('Image URL'); ?>
				</button>
				<button class="button" value="custom">
					<?php esc_attr_e('Custom URL'); ?>
				</button>
				<button class="button active" value="none">
					<?php esc_attr_e('None'); ?>
				</button>
			</div>
			<input type="text" class="link-to-custom" data-setting="linkUrl" />
		</div>
	</script>

	<script type="text/html" id="tmpl-attachments-css">
		<style type="text/css" id="{{ data.id }}-css">
			#{{ data.id }} {
				padding: 0 {{ data.gutter }}px;
			}

			#{{ data.id }} .attachment {
				margin: {{ data.gutter }}px;
				width: {{ data.edge }}px;
			}

			#{{ data.id }} .attachment-preview,
			#{{ data.id }} .attachment-preview .thumbnail {
				width: {{ data.edge }}px;
				height: {{ data.edge }}px;
			}

			#{{ data.id }} .portrait .thumbnail img {
				max-width: {{ data.edge }}px;
				height: auto;
			}

			#{{ data.id }} .landscape .thumbnail img {
				width: auto;
				max-height: {{ data.edge }}px;
			}
		</style>
	</script>
	<?php

	do_action( 'print_media_templates' );
}
