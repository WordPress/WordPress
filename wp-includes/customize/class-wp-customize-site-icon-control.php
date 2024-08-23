<?php
/**
 * Customize API: WP_Customize_Site_Icon_Control class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 4.4.0
 */

/**
 * Customize Site Icon control class.
 *
 * Used only for custom functionality in JavaScript.
 *
 * @since 4.3.0
 *
 * @see WP_Customize_Cropped_Image_Control
 */
class WP_Customize_Site_Icon_Control extends WP_Customize_Cropped_Image_Control {

	/**
	 * Control type.
	 *
	 * @since 4.3.0
	 * @var string
	 */
	public $type = 'site_icon';

	/**
	 * Constructor.
	 *
	 * @since 4.3.0
	 *
	 * @see WP_Customize_Control::__construct()
	 *
	 * @param WP_Customize_Manager $manager Customizer bootstrap instance.
	 * @param string               $id      Control ID.
	 * @param array                $args    Optional. Arguments to override class property defaults.
	 *                                      See WP_Customize_Control::__construct() for information
	 *                                      on accepted arguments. Default empty array.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
		add_action( 'customize_controls_print_styles', 'wp_site_icon', 99 );
	}

	/**
	 * Renders a JS template for the content of the site icon control.
	 *
	 * @since 4.5.0
	 */
	public function content_template() {
		?>
		<# if ( data.label ) { #>
			<span class="customize-control-title">{{ data.label }}</span>
		<# } #>

		<# if ( data.attachment && data.attachment.id ) { #>
			<div class="attachment-media-view">
				<# if ( data.attachment.sizes ) { #>
					<style>
						:root{
							--site-icon-url: url( '{{ data.attachment.sizes.full ? data.attachment.sizes.full.url : data.attachment.url }}' );
						}
					</style>
					<div class="site-icon-preview customizer">
						<div class="direction-wrap">
							<img src="{{ data.attachment.sizes.full ? data.attachment.sizes.full.url : data.attachment.url }}" class="app-icon-preview" alt="{{
								data.attachment.alt ?
									wp.i18n.sprintf(
										<?php
										/* translators: %s: The selected image alt text. */
										echo wp_json_encode( __( 'App icon preview: Current image: %s' ) )
										?>
										,
										data.attachment.alt
									) :
									wp.i18n.sprintf(
										<?php
										/* translators: %s: The selected image filename. */
										echo wp_json_encode( __( 'App icon preview: The current image has no alternative text. The file name is: %s' ) );
										?>
										,
										data.attachment.filename
									)
							}}" />
							<div class="site-icon-preview-browser">
								<svg role="img" aria-hidden="true" fill="none" xmlns="http://www.w3.org/2000/svg" class="browser-buttons"><path fill-rule="evenodd" clip-rule="evenodd" d="M0 20a6 6 0 1 1 12 0 6 6 0 0 1-12 0Zm18 0a6 6 0 1 1 12 0 6 6 0 0 1-12 0Zm24-6a6 6 0 1 0 0 12 6 6 0 0 0 0-12Z" /></svg>
								<div class="site-icon-preview-tab">
									<img src="{{ data.attachment.sizes.full ? data.attachment.sizes.full.url : data.attachment.url }}" class="browser-icon-preview" alt="{{
										data.attachment.alt ?
											wp.i18n.sprintf(
												<?php
												/* translators: %s: The selected image alt text. */
												echo wp_json_encode( __( 'Browser icon preview: Current image: %s' ) );
												?>
												,
												data.attachment.alt
											) :
											wp.i18n.sprintf(
												<?php
												/* translators: %s: The selected image filename. */
												echo wp_json_encode( __( 'Browser icon preview: The current image has no alternative text. The file name is: %s' ) );
												?>
												,
												data.attachment.filename
											)
									}}" />
									<div class="site-icon-preview-site-title" aria-hidden="true"><# print( '<?php echo esc_js( get_bloginfo( 'name' ) ); ?>' ) #></div>
										<svg role="img" aria-hidden="true" fill="none" xmlns="http://www.w3.org/2000/svg" class="close-button">
											<path d="M12 13.0607L15.7123 16.773L16.773 15.7123L13.0607 12L16.773 8.28772L15.7123 7.22706L12 10.9394L8.28771 7.22705L7.22705 8.28771L10.9394 12L7.22706 15.7123L8.28772 16.773L12 13.0607Z" />
										</svg>
									</div>
								</div>
							</div>
						</div>
					</div>
				<# } #>
				<div class="actions">
					<# if ( data.canUpload ) { #>
						<button type="button" class="button remove-button"><?php echo $this->button_labels['remove']; ?></button>
						<button type="button" class="button upload-button"><?php echo $this->button_labels['change']; ?></button>
					<# } #>
				</div>
			</div>
		<# } else { #>
			<div class="attachment-media-view">
				<# if ( data.canUpload ) { #>
					<button type="button" class="upload-button button-add-media"><?php echo $this->button_labels['site_icon']; ?></button>
				<# } #>
				<div class="actions">
					<# if ( data.defaultAttachment ) { #>
						<button type="button" class="button default-button"><?php echo $this->button_labels['default']; ?></button>
					<# } #>
				</div>
			</div>
		<# } #>
		<# if ( data.description ) { #>
			<span class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>
		<?php
	}
}
