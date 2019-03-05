<?php
/**
 * Customize API: WP_Customize_Media_Control class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 4.4.0
 */

/**
 * Customize Media Control class.
 *
 * @since 4.2.0
 *
 * @see WP_Customize_Control
 */
class WP_Customize_Media_Control extends WP_Customize_Control {
	/**
	 * Control type.
	 *
	 * @since 4.2.0
	 * @var string
	 */
	public $type = 'media';

	/**
	 * Media control mime type.
	 *
	 * @since 4.2.0
	 * @var string
	 */
	public $mime_type = '';

	/**
	 * Button labels.
	 *
	 * @since 4.2.0
	 * @var array
	 */
	public $button_labels = array();

	/**
	 * Constructor.
	 *
	 * @since 4.1.0
	 * @since 4.2.0 Moved from WP_Customize_Upload_Control.
	 *
	 * @param WP_Customize_Manager $manager Customizer bootstrap instance.
	 * @param string               $id      Control ID.
	 * @param array                $args    Optional. Arguments to override class property defaults.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );

		$this->button_labels = wp_parse_args( $this->button_labels, $this->get_default_button_labels() );
	}

	/**
	 * Enqueue control related scripts/styles.
	 *
	 * @since 3.4.0
	 * @since 4.2.0 Moved from WP_Customize_Upload_Control.
	 */
	public function enqueue() {
		wp_enqueue_media();
	}

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @since 3.4.0
	 * @since 4.2.0 Moved from WP_Customize_Upload_Control.
	 *
	 * @see WP_Customize_Control::to_json()
	 */
	public function to_json() {
		parent::to_json();
		$this->json['label']         = html_entity_decode( $this->label, ENT_QUOTES, get_bloginfo( 'charset' ) );
		$this->json['mime_type']     = $this->mime_type;
		$this->json['button_labels'] = $this->button_labels;
		$this->json['canUpload']     = current_user_can( 'upload_files' );

		$value = $this->value();

		if ( is_object( $this->setting ) ) {
			if ( $this->setting->default ) {
				// Fake an attachment model - needs all fields used by template.
				// Note that the default value must be a URL, NOT an attachment ID.
				$type               = in_array( substr( $this->setting->default, -3 ), array( 'jpg', 'png', 'gif', 'bmp' ) ) ? 'image' : 'document';
				$default_attachment = array(
					'id'    => 1,
					'url'   => $this->setting->default,
					'type'  => $type,
					'icon'  => wp_mime_type_icon( $type ),
					'title' => wp_basename( $this->setting->default ),
				);

				if ( 'image' === $type ) {
					$default_attachment['sizes'] = array(
						'full' => array( 'url' => $this->setting->default ),
					);
				}

				$this->json['defaultAttachment'] = $default_attachment;
			}

			if ( $value && $this->setting->default && $value === $this->setting->default ) {
				// Set the default as the attachment.
				$this->json['attachment'] = $this->json['defaultAttachment'];
			} elseif ( $value ) {
				$this->json['attachment'] = wp_prepare_attachment_for_js( $value );
			}
		}
	}

	/**
	 * Don't render any content for this control from PHP.
	 *
	 * @since 3.4.0
	 * @since 4.2.0 Moved from WP_Customize_Upload_Control.
	 *
	 * @see WP_Customize_Media_Control::content_template()
	 */
	public function render_content() {}

	/**
	 * Render a JS template for the content of the media control.
	 *
	 * @since 4.1.0
	 * @since 4.2.0 Moved from WP_Customize_Upload_Control.
	 */
	public function content_template() {
		?>
		<#
		var descriptionId = _.uniqueId( 'customize-media-control-description-' );
		var describedByAttr = data.description ? ' aria-describedby="' + descriptionId + '" ' : '';
		#>
		<# if ( data.label ) { #>
			<span class="customize-control-title">{{ data.label }}</span>
		<# } #>
		<div class="customize-control-notifications-container"></div>
		<# if ( data.description ) { #>
			<span id="{{ descriptionId }}" class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>

		<# if ( data.attachment && data.attachment.id ) { #>
			<div class="attachment-media-view attachment-media-view-{{ data.attachment.type }} {{ data.attachment.orientation }}">
				<div class="thumbnail thumbnail-{{ data.attachment.type }}">
					<# if ( 'image' === data.attachment.type && data.attachment.sizes && data.attachment.sizes.medium ) { #>
						<img class="attachment-thumb" src="{{ data.attachment.sizes.medium.url }}" draggable="false" alt="" />
					<# } else if ( 'image' === data.attachment.type && data.attachment.sizes && data.attachment.sizes.full ) { #>
						<img class="attachment-thumb" src="{{ data.attachment.sizes.full.url }}" draggable="false" alt="" />
					<# } else if ( 'audio' === data.attachment.type ) { #>
						<# if ( data.attachment.image && data.attachment.image.src && data.attachment.image.src !== data.attachment.icon ) { #>
							<img src="{{ data.attachment.image.src }}" class="thumbnail" draggable="false" alt="" />
						<# } else { #>
							<img src="{{ data.attachment.icon }}" class="attachment-thumb type-icon" draggable="false" alt="" />
						<# } #>
						<p class="attachment-meta attachment-meta-title">&#8220;{{ data.attachment.title }}&#8221;</p>
						<# if ( data.attachment.album || data.attachment.meta.album ) { #>
						<p class="attachment-meta"><em>{{ data.attachment.album || data.attachment.meta.album }}</em></p>
						<# } #>
						<# if ( data.attachment.artist || data.attachment.meta.artist ) { #>
						<p class="attachment-meta">{{ data.attachment.artist || data.attachment.meta.artist }}</p>
						<# } #>
						<audio style="visibility: hidden" controls class="wp-audio-shortcode" width="100%" preload="none">
							<source type="{{ data.attachment.mime }}" src="{{ data.attachment.url }}"/>
						</audio>
					<# } else if ( 'video' === data.attachment.type ) { #>
						<div class="wp-media-wrapper wp-video">
							<video controls="controls" class="wp-video-shortcode" preload="metadata"
								<# if ( data.attachment.image && data.attachment.image.src !== data.attachment.icon ) { #>poster="{{ data.attachment.image.src }}"<# } #>>
								<source type="{{ data.attachment.mime }}" src="{{ data.attachment.url }}"/>
							</video>
						</div>
					<# } else { #>
						<img class="attachment-thumb type-icon icon" src="{{ data.attachment.icon }}" draggable="false" alt="" />
						<p class="attachment-title">{{ data.attachment.title }}</p>
					<# } #>
				</div>
				<div class="actions">
					<# if ( data.canUpload ) { #>
					<button type="button" class="button remove-button">{{ data.button_labels.remove }}</button>
					<button type="button" class="button upload-button control-focus" {{{ describedByAttr }}}>{{ data.button_labels.change }}</button>
					<# } #>
				</div>
			</div>
		<# } else { #>
			<div class="attachment-media-view">
				<# if ( data.canUpload ) { #>
					<button type="button" class="upload-button button-add-media" {{{ describedByAttr }}}>{{ data.button_labels.select }}</button>
				<# } #>
				<div class="actions">
					<# if ( data.defaultAttachment ) { #>
						<button type="button" class="button default-button">{{ data.button_labels['default'] }}</button>
					<# } #>
				</div>
			</div>
		<# } #>
		<?php
	}

	/**
	 * Get default button labels.
	 *
	 * Provides an array of the default button labels based on the mime type of the current control.
	 *
	 * @since 4.9.0
	 *
	 * @return array An associative array of default button labels.
	 */
	public function get_default_button_labels() {
		// Get just the mime type and strip the mime subtype if present.
		$mime_type = ! empty( $this->mime_type ) ? strtok( ltrim( $this->mime_type, '/' ), '/' ) : 'default';

		switch ( $mime_type ) {
			case 'video':
				return array(
					'select'       => __( 'Select video' ),
					'change'       => __( 'Change video' ),
					'default'      => __( 'Default' ),
					'remove'       => __( 'Remove' ),
					'placeholder'  => __( 'No video selected' ),
					'frame_title'  => __( 'Select video' ),
					'frame_button' => __( 'Choose video' ),
				);
			case 'audio':
				return array(
					'select'       => __( 'Select audio' ),
					'change'       => __( 'Change audio' ),
					'default'      => __( 'Default' ),
					'remove'       => __( 'Remove' ),
					'placeholder'  => __( 'No audio selected' ),
					'frame_title'  => __( 'Select audio' ),
					'frame_button' => __( 'Choose audio' ),
				);
			case 'image':
				return array(
					'select'       => __( 'Select image' ),
					'site_icon'    => __( 'Select site icon' ),
					'change'       => __( 'Change image' ),
					'default'      => __( 'Default' ),
					'remove'       => __( 'Remove' ),
					'placeholder'  => __( 'No image selected' ),
					'frame_title'  => __( 'Select image' ),
					'frame_button' => __( 'Choose image' ),
				);
			default:
				return array(
					'select'       => __( 'Select file' ),
					'change'       => __( 'Change file' ),
					'default'      => __( 'Default' ),
					'remove'       => __( 'Remove' ),
					'placeholder'  => __( 'No file selected' ),
					'frame_title'  => __( 'Select file' ),
					'frame_button' => __( 'Choose file' ),
				);
		} // End switch().
	}
}
