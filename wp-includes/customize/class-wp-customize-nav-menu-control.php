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
	 * @var string
	 */
	public $type = 'nav_menu';

	/**
	 * The nav menu setting.
	 *
	 * @since 4.3.0
	 * @var WP_Customize_Nav_Menu_Setting
	 */
	public $setting;

	/**
	 * Don't render the control's content - it uses a JS template instead.
	 *
	 * @since 4.3.0
	 */
	public function render_content() {}

	/**
	 * JS/Underscore template for the control UI.
	 *
	 * @since 4.3.0
	 */
	public function content_template() {
		?>
		<# var elementId; #>
		<button type="button" class="button add-new-menu-item" aria-label="<?php esc_attr_e( 'Add or remove menu items' ); ?>" aria-expanded="false" aria-controls="available-menu-items">
			<?php _e( 'Add Items' ); ?>
		</button>
		<button type="button" class="button-link reorder-toggle" aria-label="<?php esc_attr_e( 'Reorder menu items' ); ?>" aria-describedby="reorder-items-desc-{{ data.menu_id }}">
			<span class="reorder"><?php _e( 'Reorder' ); ?></span>
			<span class="reorder-done"><?php _e( 'Done' ); ?></span>
		</button>
		<p class="screen-reader-text" id="reorder-items-desc-{{ data.menu_id }}"><?php _e( 'When in reorder mode, additional controls to reorder menu items will be available in the items list above.' ); ?></p>
		<span class="menu-delete-item">
			<button type="button" class="button-link button-link-delete">
				<?php _e( 'Delete Menu' ); ?>
			</button>
		</span>
		<?php if ( current_theme_supports( 'menus' ) ) : ?>
		<ul class="menu-settings">
			<li class="customize-control">
				<span class="customize-control-title"><?php _e( 'Display Location' ); ?></span>
			</li>

			<?php foreach ( get_registered_nav_menus() as $location => $description ) : ?>
				<# elementId = _.uniqueId( 'customize-nav-menu-control-location-' ); #>
				<li class="customize-control customize-control-checkbox assigned-menu-location customize-inside-control-row">
					<input id="{{ elementId }}" type="checkbox" data-menu-id="{{ data.menu_id }}" data-location-id="<?php echo esc_attr( $location ); ?>" class="menu-location" />
					<label for="{{ elementId }}">
						<?php echo $description; ?>
						<span class="theme-location-set">
							<?php
							/* translators: %s: menu name */
							printf( _x( '(Current: %s)', 'menu location' ),
								'<span class="current-menu-location-name-' . esc_attr( $location ) . '"></span>'
							);
							?>
						</span>
					</label>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>
		<?php
	}

	/**
	 * Return parameters for this control.
	 *
	 * @since 4.3.0
	 *
	 * @return array Exported parameters.
	 */
	public function json() {
		$exported            = parent::json();
		$exported['menu_id'] = $this->setting->term_id;

		return $exported;
	}
}
