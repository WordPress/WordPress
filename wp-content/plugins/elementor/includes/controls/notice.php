<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Notice control.
 *
 * A base control specific for creating Notices in the Editor panels.
 *
 * @since 3.19.0
 */
class Control_Notice extends Base_UI_Control {

	/**
	 * Get notice control type.
	 *
	 * Retrieve the control type, in this case `notice`.
	 *
	 * @since 3.19.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'notice';
	}

	/**
	 * Render notice control output in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `data` JS
	 * object.
	 *
	 * @since 3.19.0
	 * @access public
	 */
	public function content_template() {
		?>
		<#
		if ( ! data.shouldRenderNotice ) {
			return;
		}

		const validNoticeTypes = [ 'info', 'success', 'warning', 'danger' ];
		const showIcon = validNoticeTypes.includes( data.notice_type );
		data.content = elementor.compileTemplate( data.content, { view } );
		#>
		<div class="elementor-control-notice elementor-control-notice-type-{{ data.notice_type }}">
			<# if ( showIcon && data.icon ) { #>
			<div class="elementor-control-notice-icon">
				<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M2.25 9H3M9 2.25V3M15 9H15.75M4.2 4.2L4.725 4.725M13.8 4.2L13.275 4.725M7.27496 12.75H10.725M6.75 12C6.12035 11.5278 5.65525 10.8694 5.42057 10.1181C5.1859 9.36687 5.19355 8.56082 5.44244 7.81415C5.69133 7.06748 6.16884 6.41804 6.80734 5.95784C7.44583 5.49764 8.21294 5.25 9 5.25C9.78706 5.25 10.5542 5.49764 11.1927 5.95784C11.8312 6.41804 12.3087 7.06748 12.5576 7.81415C12.8065 8.56082 12.8141 9.36687 12.5794 10.1181C12.3448 10.8694 11.8796 11.5278 11.25 12C10.9572 12.2899 10.7367 12.6446 10.6064 13.0355C10.4761 13.4264 10.4397 13.8424 10.5 14.25C10.5 14.6478 10.342 15.0294 10.0607 15.3107C9.77936 15.592 9.39782 15.75 9 15.75C8.60218 15.75 8.22064 15.592 7.93934 15.3107C7.65804 15.0294 7.5 14.6478 7.5 14.25C7.56034 13.8424 7.52389 13.4264 7.3936 13.0355C7.2633 12.6446 7.04282 12.2899 6.75 12Z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</div>
			<# } #>
			<div class="elementor-control-notice-main">
				<# if ( data.heading ) { #>
				<div class="elementor-control-notice-main-heading">{{{ data.heading }}}</div>
				<# } #>
				<# if ( data.content ) { #>
				<div class="elementor-control-notice-main-content">{{{ data.content }}}</div>
				<# } #>
				<# if ( data.button_text || data.button_text2 ) { #>
				<div class="elementor-control-notice-main-actions">
					<# if ( data.button_text || data.button_event ) { #>
					<button type="button" class="e-btn e-{{{ data.notice_type }}} e-btn-1" data-event="{{{ data.button_event }}}">
						{{{ data.button_text }}}
					</button>
					<# } #>
					<# if ( data.button_text2 || data.button_event2 ) { #>
					<button type="button" class="e-btn e-{{{ data.notice_type }}} e-btn-2" data-event="{{{ data.button_event2 }}}">
						{{{ data.button_text2 }}}
					</button>
					<# } #>
				</div>
				<# } #>
			</div>
			<# if ( data.dismissible ) { #>
			<button class="elementor-control-notice-dismiss tooltip-target" data-tooltip="<?php echo esc_attr__( 'Don’t show again.', 'elementor' ); ?>" aria-label="<?php echo esc_attr__( 'Don’t show again.', 'elementor' ); ?>">
				<i class="eicon eicon-close" aria-hidden="true"></i>
			</button>
			<# } #>
		</div>
		<?php
	}

	/**
	 * Get notice control default settings.
	 *
	 * Retrieve the default settings of the notice control. Used to return the
	 * default settings while initializing the notice control.
	 *
	 * @since 3.19.0
	 * @access protected
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		return [
			'notice_type' => '', // info, success, warning, danger
			'icon' => true,
			'dismissible' => false,
			'heading' => '',
			'content' => '',
			'button_text' => '',
			'button_event' => '',
			'button_text2' => '',
			'button_event2' => '',
		];
	}
}
