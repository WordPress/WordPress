<?php
/**
 * Customize API: WP_Customize_Nav_Menu_Control class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 4.4.0
 */

/**
 * Customize Nav Menu Control Class.
 *
 * @since 4.3.0
 */
class WP_Customize_Nav_Menu_Control extends WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @since 4.3.0
	 * @access public
	 * @var string
	 */
	public $type = 'nav_menu';

	/**
	 * The nav menu setting.
	 *
	 * @since 4.3.0
	 * @access public
	 * @var WP_Customize_Nav_Menu_Setting
	 */
	public $setting;

	/**
	 * Don't render the control's content - it uses a JS template instead.
	 *
	 * @since 4.3.0
	 * @access public
	 */
	public function render_content() {}

	/**
	 * JS/Underscore template for the control UI.
	 *
	 * @since 4.3.0
	 * @access public
	 */
	public function content_template() {
		?>
		<button type="button" class="button-secondary add-new-menu-item" aria-label="<?php esc_attr_e( 'Add or remove menu items' ); ?>" aria-expanded="false" aria-controls="available-menu-items">
			<?php _e( 'Add Items' ); ?>
		</button>
		<button type="button" class="button-link reorder-toggle" aria-label="<?php esc_attr_e( 'Reorder menu items' ); ?>" aria-describedby="reorder-items-desc-{{ data.menu_id }}">
			<span class="reorder"><?php _ex( 'Reorder', 'Reorder menu items in Customizer' ); ?></span>
			<span class="reorder-done"><?php _ex( 'Done', 'Cancel reordering menu items in Customizer' ); ?></span>
		</button>
		<p class="screen-reader-text" id="reorder-items-desc-{{ data.menu_id }}"><?php _e( 'When in reorder mode, additional controls to reorder menu items will be available in the items list above.' ); ?></p>
		<span class="menu-delete-item">
			<button type="button" class="button-link menu-delete">
				<?php _e( 'Delete menu' ); ?> <span class="screen-reader-text">{{ data.menu_name }}</span>
			</button>
		</span>
		<?php if ( current_theme_supports( 'menus' ) ) : ?>
		<ul class="menu-settings">
			<li class="customize-control">
				<span class="customize-control-title"><?php _e( 'Menu locations' ); ?></span>
			</li>

			<?php foreach ( get_registered_nav_menus() as $location => $description ) : ?>
			<li class="customize-control customize-control-checkbox assigned-menu-location">
				<label>
					<input type="checkbox" data-menu-id="{{ data.menu_id }}" data-location-id="<?php echo esc_attr( $location ); ?>" class="menu-location" /> <?php echo $description; ?>
					<span class="theme-location-set"><?php printf( /* translators: %s: menu name */ _x( '(Current: %s)', 'Current menu location' ), '<span class="current-menu-location-name-' . esc_attr( $location ) . '"></span>' ); ?></span>
				</label>
			</li>
			<?php endforeach; ?>

		</ul>
		<?php endif;
	}

	/**
	 * Return parameters for this control.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @return array Exported parameters.
	 */
	public function json() {
		$exported            = parent::json();
		$exported['menu_id'] = $this->setting->term_id;

		return $exported;
	}
}
