<?php
/**
 * Customize API: WP_Customize_Themes_Section class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 4.4.0
 */

/**
 * Customize Themes Section class.
 *
 * A UI container for theme controls, which behaves like a backwards Panel.
 *
 * @since 4.2.0
 *
 * @see WP_Customize_Section
 */
class WP_Customize_Themes_Section extends WP_Customize_Section {

	/**
	 * Customize section type.
	 *
	 * @since 4.2.0
	 * @access public
	 * @var string
	 */
	public $type = 'themes';

	/**
	 * Render the themes section, which behaves like a panel.
	 *
	 * @since 4.2.0
	 * @access protected
	 */
	protected function render() {
		$classes = 'accordion-section control-section control-section-' . $this->type;
		?>
		<li id="accordion-section-<?php echo esc_attr( $this->id ); ?>" class="<?php echo esc_attr( $classes ); ?>">
			<h3 class="accordion-section-title">
				<?php
				if ( $this->manager->is_theme_active() ) {
					echo '<span class="customize-action">' . __( 'Active theme' ) . '</span> ' . $this->title;
				} else {
					echo '<span class="customize-action">' . __( 'Previewing theme' ) . '</span> ' . $this->title;
				}
				?>

				<button type="button" class="button change-theme" tabindex="0"><?php _ex( 'Change', 'theme' ); ?></button>
			</h3>
			<div class="customize-themes-panel control-panel-content themes-php">
				<h3 class="accordion-section-title customize-section-title">
					<span class="customize-action"><?php _e( 'Customizing' ); ?></span>
					<?php _e( 'Themes' ); ?>
					<span class="title-count theme-count"><?php echo count( $this->controls ) + 1 /* Active theme */; ?></span>
				</h3>
				<h3 class="accordion-section-title customize-section-title">
					<?php
					if ( $this->manager->is_theme_active() ) {
						echo '<span class="customize-action">' . __( 'Active theme' ) . '</span> ' . $this->title;
					} else {
						echo '<span class="customize-action">' . __( 'Previewing theme' ) . '</span> ' . $this->title;
					}
					?>
					<button type="button" class="button customize-theme"><?php _e( 'Customize' ); ?></button>
				</h3>

				<div class="theme-overlay" tabindex="0" role="dialog" aria-label="<?php esc_attr_e( 'Theme Details' ); ?>"></div>

				<div id="customize-container"></div>
				<?php if ( count( $this->controls ) > 4 ) : ?>
					<p><label for="themes-filter">
						<span class="screen-reader-text"><?php _e( 'Search installed themes&hellip;' ); ?></span>
						<input type="text" id="themes-filter" placeholder="<?php esc_attr_e( 'Search installed themes&hellip;' ); ?>" />
					</label></p>
				<?php endif; ?>
				<div class="theme-browser rendered">
					<ul class="themes accordion-section-content">
					</ul>
				</div>
			</div>
		</li>
<?php }
}
