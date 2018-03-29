<?php

class UM_Menu_Item_Custom_Fields_Editor {

	protected static $fields = array();


	/**
	 * Initialize plugin
	 */
	public static function init() {
		add_action( 'wp_nav_menu_item_custom_fields', array( __CLASS__, '_fields' ), 1, 4 );
		add_action( 'wp_update_nav_menu_item', array( __CLASS__, '_save' ), 10, 3 );
		add_filter( 'manage_nav-menus_columns', array( __CLASS__, '_columns' ), 99 );

		self::$fields = array(
		
			'um_nav_public' => __( 'Display Mode'),
			'um_nav_roles' => __('By Role')
			
		);
	}


	public static function _save( $menu_id, $menu_item_db_id, $menu_item_args ) {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		foreach ( self::$fields as $_key => $label ) {
			
			if( $_key == 'um_nav_roles' ){

				$key = sprintf( 'menu-item-%s', $_key );
                
				// Sanitize
				if ( ! empty( $_POST[ $key ][ $menu_item_db_id ] ) ) {
					// Do some checks here...
					$value = $_POST[ $key ][ $menu_item_db_id ];
				}
				else {
					$value = null;
				}

			}else{
				
				$key = sprintf( 'menu-item-%s', $_key );
				
				// Sanitize
				if ( ! empty( $_POST[ $key ][ $menu_item_db_id ] ) ) {
					// Do some checks here...
					$value = $_POST[ $key ][ $menu_item_db_id ];
				}
				else {
					$value = null;
				}
			}

			// Update
			if ( ! is_null( $value ) ) {
				update_post_meta( $menu_item_db_id, $key, $value );
			}
			else {
				delete_post_meta( $menu_item_db_id, $key );
			}
		}
	}

	public static function _fields( $id, $item, $depth, $args ) {
		global $ultimatemember;
		
		?>
		
		<div class="um-nav-edit">
		
			<div class="clear"></div>
			
			<div class="um-nav-edit-h2">UltimateMember Menu Settings</div>
		
		<?php
		foreach ( self::$fields as $_key => $label ) :
			$key   = sprintf( 'menu-item-%s', $_key );
			$id    = sprintf( 'edit-%s-%s', $key, $item->ID );
			$name  = sprintf( '%s[%s]', $key, $item->ID );
			$value = get_post_meta( $item->ID, $key, true );
			$role_name  = sprintf( '%s[%s][]', $key, $item->ID );
			$class = sprintf( 'field-%s', $_key );
			?>
			
				<?php if ( $_key == 'um_nav_public' ) { ?>
				
				<div class="description-wide um-nav-mode">
				
					<span class="description"><?php _e( "Who can see this menu link?"); ?></span><br />
					
					<p class="description">
					
					<label><input type="radio" name="<?php echo $name; ?>" value="0" <?php if (!isset($value) || $value == '') echo 'checked="checked"'; ?> /> Everyone</label>&nbsp;&nbsp;
					
					<label><input type="radio" name="<?php echo $name; ?>" value="1" <?php checked(1, $value); ?> /> Logged Out Users</label>&nbsp;&nbsp;
					
					<label><input type="radio" name="<?php echo $name; ?>" value="2" <?php checked(2, $value); ?> /> Logged In Users</label>&nbsp;&nbsp;
					
					</p>
				
				</div>
				
				<?php } ?>
				
				<?php if ( $_key == 'um_nav_roles' ) { ?>
				 <?php $role_value = get_post_meta( $item->ID, $_key , true ); ?>
				<div class="description-wide um-nav-roles">
				
					<span class="description"><?php _e( "Select the member roles that can see this link"); ?></span><br />
					
					<p class="description">
					
					<?php  foreach($ultimatemember->query->get_roles() as $role_id => $role) { ?>
					<label><input type="checkbox" name="<?php echo $role_name; ?>" value="<?php echo $role_id; ?>" <?php if (  ( is_array($value) && in_array($role_id, $value ) ) || ( isset($value) && $role_id == $value ) ) echo 'checked="checked"'; ?> /> <?php echo $role; ?></label>&nbsp;&nbsp;
					<?php } ?>
					
					</p>
				
				</div>
				
				<?php } ?>
				
			<?php
		endforeach;
		?>
		
		</div>
		
		<?php
	}

	public static function _columns( $columns ) {
		$columns = array_merge( $columns, self::$fields );

		return $columns;
	}
}
UM_Menu_Item_Custom_Fields_Editor::init();