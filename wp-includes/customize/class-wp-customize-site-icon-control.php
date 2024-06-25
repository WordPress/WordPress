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
		<# if ( data.description ) { #>
			<span class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>

		<# if ( data.attachment && data.attachment.id ) { #>
			<div class="attachment-media-view">
				<# if ( data.attachment.sizes ) { #>
					<div class="site-icon-preview wp-clearfix">
						<div class="favicon-preview">
							<img src="<?php echo esc_url( admin_url( 'images/' . ( is_rtl() ? 'browser-rtl.png' : 'browser.png' ) ) ); ?>" class="browser-preview" width="182" alt="" />

							<div class="favicon">
								<img src="{{ data.attachment.sizes.full ? data.attachment.sizes.full.url : data.attachment.url }}" alt="{{
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
							</div>
							<span class="browser-title" aria-hidden="true"><# print( '<?php echo esc_js( get_bloginfo( 'name' ) ); ?>' ) #></span>
						</div>
						<img class="app-icon-preview" src="{{ data.attachment.sizes.full ? data.attachment.sizes.full.url : data.attachment.url }}" alt="{{
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
						}}"/>
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
		<?php
	}
}
