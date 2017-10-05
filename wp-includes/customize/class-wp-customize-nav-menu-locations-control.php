<?php
/**
 * Customize API: WP_Customize_Nav_Menu_Locations_Control class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 4.9.0
 */

/**
 * Customize Nav Menu Locations Control Class.
 *
 * @since 4.9.0
 */
class WP_Customize_Nav_Menu_Locations_Control extends WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @since 4.9.0
	 * @var string
	 */
	public $type = 'nav_menu_locations';

	/**
	 * Don't render the control's content - it uses a JS template instead.
	 *
	 * @since 4.9.0
	 */
	public function render_content() {}

	/**
	 * JS/Underscore template for the control UI.
	 *
	 * @since 4.9.0
	 */
	public function content_template() {
		if ( current_theme_supports( 'menus' ) ):
			?>
			<# var elementId; #>
			<ul class="menu-location-settings">
				<li class="customize-control assigned-menu-locations-title">
					<span class="customize-control-title"><?php _e( 'Menu Locations' ); ?></span>
					<p><?php _e( 'Here\'s where this menu appears. If you\'d like to change that, pick another location.' ); ?></p>
				</li>

				<?php foreach ( get_registered_nav_menus() as $location => $description ) : ?>
					<# elementId = _.uniqueId( 'customize-nav-menu-control-location-' ); #>
					<li class="customize-control customize-control-checkbox assigned-menu-location">
						<span class="customize-inside-control-row">
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
						</span>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php
		endif;
	}
}
