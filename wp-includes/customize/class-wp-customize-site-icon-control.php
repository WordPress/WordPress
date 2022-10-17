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
	 * @access public
	 * @var string
	 */
	public $type = 'site_icon';

	/**
	 * Constructor.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @param WP_Customize_Manager $manager Customizer bootstrap instance.
	 * @param string               $id      Control ID.
	 * @param array                $args    Optional. Arguments to override class property defaults.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
		add_action( 'customize_controls_print_styles', 'wp_site_icon', 99 );
	}

	/**
	 * Renders a JS template for the content of the site icon control.
	 *
	 * @since 4.5.0
	 * @access public
	 */
	public function content_template() {
		?>
		<label for="{{ data.settings['default'] }}-button">
			<# if ( data.label ) { #>
				<span class="customize-control-title">{{ data.label }}</span>
			<# } #>
			<# if ( data.description ) { #>
				<span class="description customize-control-description">{{{ data.description }}}</span>
			<# } #>
		</label>

		<# if ( data.attachment && data.attachment.id ) { #>
			<div class="attachment-media-view">
				<# if ( data.attachment.sizes ) { #>
					<div class="site-icon-preview">
						<div class="favicon-preview">
							<img src="<?php echo esc_url( admin_url( 'images/' . ( is_rtl() ? 'browser-rtl.png' : 'browser.png' ) ) ); ?>" class="browser-preview" width="182" alt="" />

							<div class="favicon">
								<img src="{{ data.attachment.sizes.full.url }}" alt="<?php esc_attr_e( 'Preview as a browser icon' ); ?>"/>
							</div>
							<span class="browser-title" aria-hidden="true"><?php echo esc_js( get_bloginfo( 'name' ) ); ?></span>
						</div>
						<img class="app-icon-preview" src="{{ data.attachment.sizes.full.url }}" alt="<?php esc_attr_e( 'Preview as an app icon' ); ?>"/>
					</div>
				<# } #>
				<div class="actions">
					<# if ( data.canUpload ) { #>
						<button type="button" class="button remove-button"><?php echo $this->button_labels['remove']; ?></button>
						<button type="button" class="button upload-button" id="{{ data.settings['default'] }}-button"><?php echo $this->button_labels['change']; ?></button>
						<div style="clear:both"></div>
					<# } #>
				</div>
			</div>
		<# } else { #>
			<div class="attachment-media-view">
				<div class="placeholder">
					<?php echo $this->button_labels['placeholder']; ?>
				</div>
				<div class="actions">
					<# if ( data.defaultAttachment ) { #>
						<button type="button" class="button default-button"><?php echo $this->button_labels['default']; ?></button>
					<# } #>
					<# if ( data.canUpload ) { #>
						<button type="button" class="button upload-button" id="{{ data.settings['default'] }}-button"><?php echo $this->button_labels['select']; ?></button>
					<# } #>
					<div style="clear:both"></div>
				</div>
			</div>
		<# } #>
		<?php
	}
}
