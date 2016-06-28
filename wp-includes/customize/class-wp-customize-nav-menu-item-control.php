<?php
/**
 * Customize API: WP_Customize_Nav_Menu_Item_Control class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 4.4.0
 */

/**
 * Customize control to represent the name field for a given menu.
 *
 * @since 4.3.0
 */
class WP_Customize_Nav_Menu_Item_Control extends WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @since 4.3.0
	 * @access public
	 * @var string
	 */
	public $type = 'nav_menu_item';

	/**
	 * The nav menu item setting.
	 *
	 * @since 4.3.0
	 * @access public
	 * @var WP_Customize_Nav_Menu_Item_Setting
	 */
	public $setting;

	/**
	 * Constructor.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @see WP_Customize_Control::__construct()
	 *
	 * @param WP_Customize_Manager $manager Customizer bootstrap instance.
	 * @param string               $id      The control ID.
	 * @param array                $args    Optional. Overrides class property defaults.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
	}

	/**
	 * Don't render the control's content - it's rendered with a JS template.
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
		<div class="menu-item-bar">
			<div class="menu-item-handle">
				<span class="item-type" aria-hidden="true">{{ data.item_type_label }}</span>
				<span class="item-title" aria-hidden="true">
					<span class="spinner"></span>
					<span class="menu-item-title<# if ( ! data.title ) { #> no-title<# } #>">{{ data.title || wp.customize.Menus.data.l10n.untitled }}</span>
				</span>
				<span class="item-controls">
					<button type="button" class="button-link item-edit" aria-expanded="false"><span class="screen-reader-text"><?php
						/* translators: 1: Title of a menu item, 2: Type of a menu item */
						printf( __( 'Edit menu item: %1$s (%2$s)' ), '{{ data.title || wp.customize.Menus.data.l10n.untitled }}', '{{ data.item_type_label }}' );
					?></span><span class="toggle-indicator" aria-hidden="true"></span></button>
					<button type="button" class="button-link item-delete submitdelete deletion"><span class="screen-reader-text"><?php
						/* translators: 1: Title of a menu item, 2: Type of a menu item */
						printf( __( 'Remove Menu Item: %1$s (%2$s)' ), '{{ data.title || wp.customize.Menus.data.l10n.untitled }}', '{{ data.item_type_label }}' );
					?></span></button>
				</span>
			</div>
		</div>

		<div class="menu-item-settings" id="menu-item-settings-{{ data.menu_item_id }}">
			<# if ( 'custom' === data.item_type ) { #>
			<p class="field-url description description-thin">
				<label for="edit-menu-item-url-{{ data.menu_item_id }}">
					<?php _e( 'URL' ); ?><br />
					<input class="widefat code edit-menu-item-url" type="text" id="edit-menu-item-url-{{ data.menu_item_id }}" name="menu-item-url" />
				</label>
			</p>
		<# } #>
			<p class="description description-thin">
				<label for="edit-menu-item-title-{{ data.menu_item_id }}">
					<?php _e( 'Navigation Label' ); ?><br />
					<input type="text" id="edit-menu-item-title-{{ data.menu_item_id }}" class="widefat edit-menu-item-title" name="menu-item-title" />
				</label>
			</p>
			<p class="field-link-target description description-thin">
				<label for="edit-menu-item-target-{{ data.menu_item_id }}">
					<input type="checkbox" id="edit-menu-item-target-{{ data.menu_item_id }}" class="edit-menu-item-target" value="_blank" name="menu-item-target" />
					<?php _e( 'Open link in a new tab' ); ?>
				</label>
			</p>
			<p class="field-title-attribute field-attr-title description description-thin">
				<label for="edit-menu-item-attr-title-{{ data.menu_item_id }}">
					<?php _e( 'Title Attribute' ); ?><br />
					<input type="text" id="edit-menu-item-attr-title-{{ data.menu_item_id }}" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title" />
				</label>
			</p>
			<p class="field-css-classes description description-thin">
				<label for="edit-menu-item-classes-{{ data.menu_item_id }}">
					<?php _e( 'CSS Classes' ); ?><br />
					<input type="text" id="edit-menu-item-classes-{{ data.menu_item_id }}" class="widefat code edit-menu-item-classes" name="menu-item-classes" />
				</label>
			</p>
			<p class="field-xfn description description-thin">
				<label for="edit-menu-item-xfn-{{ data.menu_item_id }}">
					<?php _e( 'Link Relationship (XFN)' ); ?><br />
					<input type="text" id="edit-menu-item-xfn-{{ data.menu_item_id }}" class="widefat code edit-menu-item-xfn" name="menu-item-xfn" />
				</label>
			</p>
			<p class="field-description description description-thin">
				<label for="edit-menu-item-description-{{ data.menu_item_id }}">
					<?php _e( 'Description' ); ?><br />
					<textarea id="edit-menu-item-description-{{ data.menu_item_id }}" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description">{{ data.description }}</textarea>
					<span class="description"><?php _e( 'The description will be displayed in the menu if the current theme supports it.' ); ?></span>
				</label>
			</p>

			<div class="menu-item-actions description-thin submitbox">
				<# if ( ( 'post_type' === data.item_type || 'taxonomy' === data.item_type ) && '' !== data.original_title ) { #>
				<p class="link-to-original">
					<?php printf( __( 'Original: %s' ), '<a class="original-link" href="{{ data.url }}">{{ data.original_title }}</a>' ); ?>
				</p>
				<# } #>

				<button type="button" class="button-link item-delete submitdelete deletion"><?php _e( 'Remove' ); ?></button>
				<span class="spinner"></span>
			</div>
			<input type="hidden" name="menu-item-db-id[{{ data.menu_item_id }}]" class="menu-item-data-db-id" value="{{ data.menu_item_id }}" />
			<input type="hidden" name="menu-item-parent-id[{{ data.menu_item_id }}]" class="menu-item-data-parent-id" value="{{ data.parent }}" />
		</div><!-- .menu-item-settings-->
		<ul class="menu-item-transport"></ul>
		<?php
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
		$exported                 = parent::json();
		$exported['menu_item_id'] = $this->setting->post_id;

		return $exported;
	}
}
