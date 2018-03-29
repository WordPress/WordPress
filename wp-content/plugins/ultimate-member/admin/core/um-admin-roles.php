<?php

class UM_Admin_Roles {

	function __construct() {
		
		add_filter('manage_edit-um_role_columns', array(&$this, 'manage_edit_um_role_columns') );
		add_action('manage_um_role_posts_custom_column', array(&$this, 'manage_um_role_posts_custom_column'), 10, 3);
		add_filter( 'post_row_actions',  array(&$this,'remove_row_actions' ), 10, 2 );

	}

	function remove_row_actions( $actions, $post ){
	  global $ultimatemember;
		if( $post->post_type != 'um_role' ) return $actions;
		
		if( $ultimatemember->query->is_core( $post->ID ) ){
			unset( $actions['trash'] );
			unset( $actions['inline hide-if-no-js'] );
		}
		
		return $actions;
	}
	/***
	***	@Custom columns for Role
	***/
	function manage_edit_um_role_columns($columns) {
	
		$admin = new UM_Admin_Metabox();
		
		$new_columns['cb'] = '<input type="checkbox" />';
		$new_columns['title'] = __('Role Title','ultimate-member');
		$new_columns['count'] = __('No. of Members','ultimate-member') . $admin->_tooltip( __('The total number of members who have this role on your site','ultimate-member') );
		$new_columns['core'] = __('Core / Built-in','ultimate-member') . $admin->_tooltip( __('A core role is installed by default and may not be removed','ultimate-member') );
		$new_columns['has_wpadmin_perm'] = __('WP-Admin Access','ultimate-member') . $admin->_tooltip( __('Let you know If users of this role can view the WordPress backend or not','ultimate-member') );
		
		return $new_columns;
		
	}

	/***
	***	@Display custom columns for Role
	***/
	function manage_um_role_posts_custom_column($column_name, $id) {
		global $wpdb, $ultimatemember;
		
		switch ($column_name) {
			
			case 'has_wpadmin_perm':
				if ( $ultimatemember->query->is_core( $id ) ) {
					$role = $ultimatemember->query->is_core( $id );
				} else {
					$post = get_post($id);
					$role = $post->post_name;
				}
				$data = $ultimatemember->query->role_data($role);
				if ( isset( $data['can_access_wpadmin'] ) && $data['can_access_wpadmin'] == 1 ){
					echo '<span class="um-adm-ico um-admin-tipsy-n" title="'.__('This role can access the WordPress backend','ultimate-member').'"><i class="um-faicon-check"></i></span>';
				} else {
					echo __('No','ultimate-member');
				}
				break;
				
			case 'count':
				if ( $ultimatemember->query->is_core( $id ) ) {
					$role = $ultimatemember->query->is_core( $id );
				} else {
					$post = get_post($id);
					$role = $post->post_name;
				}
				echo $ultimatemember->query->count_users_by_role( $role );
				break;
			
			case 'core':
				if ( $ultimatemember->query->is_core( $id ) ) {
					echo '<span class="um-adm-ico um-admin-tipsy-n" title="'.__('Core','ultimate-member').'"><i class="um-faicon-check"></i></span>';
				} else {
					echo '&mdash;';
				}
				break;
				
		}
		
	}
	
}
