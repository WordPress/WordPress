<?php
/**
 * Customize API: WP_Customize_Themes_Panel class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 4.9.0
 */

/**
 * Customize Themes Panel Class
 *
 * @since 4.9.0
 *
 * @see WP_Customize_Panel
 */
class WP_Customize_Themes_Panel extends WP_Customize_Panel {

	/**
	 * Panel type.
	 *
	 * @since 4.9.0
	 * @var string
	 */
	public $type = 'themes';

	/**
	 * An Underscore (JS) template for rendering this panel's container.
	 *
	 * The themes panel renders a custom panel heading with the current theme and a switch themes button.
	 *
	 * @see WP_Customize_Panel::print_template()
	 *
	 * @since 4.9.0
	 */
	protected function render_template() {
		?>
		<li id="accordion-section-{{ data.id }}" class="accordion-section control-panel-themes">
			<h3 class="accordion-section-title">
				<?php
				if ( $this->manager->is_theme_active() ) {
					echo '<span class="customize-action">' . __( 'Active theme' ) . '</span> {{ data.title }}';
				} else {
					echo '<span class="customize-action">' . __( 'Previewing theme' ) . '</span> {{ data.title }}';
				}
				?>

				<?php if ( current_user_can( 'switch_themes' ) ) : ?>
					<button type="button" class="button change-theme" aria-label="<?php _e( 'Change theme' ); ?>"><?php _ex( 'Change', 'theme' ); ?></button>
				<?php endif; ?>
			</h3>
			<ul class="accordion-sub-container control-panel-content"></ul>
		</li>
		<?php
	}

	/**
	 * An Underscore (JS) template for this panel's content (but not its container).
	 *
	 * Class variables for this panel class are available in the `data` JS object;
	 * export custom variables by overriding WP_Customize_Panel::json().
	 *
	 * @since 4.9.0
	 *
	 * @see WP_Customize_Panel::print_template()
	 */
	protected function content_template() {
		?>
		<li class="panel-meta customize-info accordion-section <# if ( ! data.description ) { #> cannot-expand<# } #>">
			<button class="customize-panel-back" tabindex="-1" type="button"><span class="screen-reader-text"><?php _e( 'Back' ); ?></span></button>
			<div class="accordion-section-title">
				<span class="preview-notice">
					<?php
					/* translators: %s: themes panel title in the Customizer */
					echo sprintf( __( 'You are browsing %s' ), '<strong class="panel-title">' . __( 'Themes' ) . '</strong>' ); // Separate strings for consistency with other panels.
					?>
				</span>
				<?php if ( current_user_can( 'install_themes' ) && ! is_multisite() ) : ?>
					<# if ( data.description ) { #>
						<button class="customize-help-toggle dashicons dashicons-editor-help" type="button" aria-expanded="false"><span class="screen-reader-text"><?php _e( 'Help' ); ?></span></button>
					<# } #>
				<?php endif; ?>
			</div>
			<?php if ( current_user_can( 'install_themes' ) && ! is_multisite() ) : ?>
				<# if ( data.description ) { #>
					<div class="description customize-panel-description">
						{{{ data.description }}}
					</div>
				<# } #>
			<?php endif; ?>
		</li>
		<li id="customize-themes-loading-container">
			<span class="customize-loading-text-installing-theme"><?php _e( 'Downloading your new theme&hellip;' ); ?></span>
			<span class="customize-loading-text"><?php _e( 'Setting up your live preview. This may take a bit.' ); ?></span>
		</li><?php // Used as a full-screen overlay transition after clicking to preview a theme. ?>
		<li class="customize-themes-full-container-container">
			<ul class="customize-themes-full-container">
				<li class="customize-themes-notifications"></li>
			</ul>
		</li>
		<?php
	}
}
