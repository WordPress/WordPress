<?php

class UM_Admin_Users {

	function __construct() {

		$this->custom_role = 'um_role';

		add_filter('manage_users_columns', array(&$this, 'manage_users_columns') );

		add_action('manage_users_custom_column', array(&$this, 'manage_users_custom_column'), 10, 3);

		add_action('restrict_manage_users', array(&$this, 'restrict_manage_users') );

		add_action('admin_init',  array(&$this, 'um_bulk_users_edit'), 9);

		add_filter('views_users', array(&$this, 'views_users') );

		add_filter('pre_user_query', array(&$this, 'sort_by_newest') );

		add_filter('pre_user_query', array(&$this, 'custom_users_filter') );

		add_filter('user_row_actions', array(&$this, 'user_row_actions'), 10, 2);

	}

	/***
	***	@Custom row actions for users page
	***/
	function user_row_actions($actions, $user_object) {

		$user_id = $user_object->ID;
		um_fetch_user( $user_id );

		$actions['frontend_profile'] = "<a class='' href='" . um_user_profile_url() . "'>" . __( 'View profile','ultimate-member') . "</a>";

		if ( um_user('submitted') ) {
			$actions['view_info'] = '<a href="#" data-modal="UM_preview_registration" data-modal-size="smaller" data-dynamic-content="um_admin_review_registration" data-arg1="'.$user_id.'" data-arg2="edit_registration">' . __('Info','ultimate-member') . '</a>';
		}

		$actions = apply_filters('um_admin_user_row_actions', $actions, $user_id );

		return $actions;
	}

	/***
	***	@sort users by newest first
	***/
	function sort_by_newest( $query ){
		global $wpdb, $pagenow;

		if ( is_admin() && $pagenow == 'users.php' ) {
			if (!isset($_REQUEST['orderby'])) {
				$query->query_vars["order"] = 'desc';
				$query->query_orderby = " ORDER BY user_registered ".($query->query_vars["order"] == "desc" ? "desc " : "asc ");//set sort order
			}
		}

		return $query;

	}

	/***
	***	@custom users filter
	***/
	function custom_users_filter( $query ){
		global $wpdb, $pagenow;

		if ( is_admin() && $pagenow=='users.php' && isset($_GET[ $this->custom_role ]) && $_GET[ $this->custom_role ] != '') {

			$role = $_GET[ $this->custom_role ];
			$query->query_where =
			str_replace('WHERE 1=1',
					"WHERE 1=1 AND {$wpdb->users}.ID IN (
						 SELECT {$wpdb->usermeta}.user_id FROM $wpdb->usermeta
							WHERE {$wpdb->usermeta}.meta_key = 'role'
							AND {$wpdb->usermeta}.meta_value = '{$role}')",
					$query->query_where
			);

		}

		if ( is_admin() && $pagenow=='users.php' && isset($_GET[ 'status' ]) && $_GET[ 'status' ] != '') {

			$status = urldecode($_GET[ 'status' ]);

			if ( $status == 'needs-verification') {
			$query->query_where = str_replace('WHERE 1=1',
						"WHERE 1=1 AND {$wpdb->users}.ID IN (
							 SELECT {$wpdb->usermeta}.user_id FROM $wpdb->usermeta
								WHERE {$wpdb->usermeta}.meta_key = '_um_verified'
								AND {$wpdb->usermeta}.meta_value = 'pending')",
						$query->query_where
			);
			} else {
			$query->query_where = str_replace('WHERE 1=1',
						"WHERE 1=1 AND {$wpdb->users}.ID IN (
							 SELECT {$wpdb->usermeta}.user_id FROM $wpdb->usermeta
								WHERE {$wpdb->usermeta}.meta_key = 'account_status'
								AND {$wpdb->usermeta}.meta_value = '{$status}')",
						$query->query_where
			);
			}

		}

		return $query;

	}

	/***
	***	@Change the roles with UM roles
	***/
	function views_users( $views ) {
		global $ultimatemember, $query;

		remove_filter('pre_user_query', array(&$this, 'custom_users_filter') );

		$old_views = $views;
		$views     = array();

		if ( !isset($_REQUEST[ $this->custom_role ]) && !isset($_REQUEST['status']) ) {
			$views['all'] = '<a href="'.admin_url('users.php').'" class="current">All <span class="count">('.$ultimatemember->query->count_users().')</span></a>';
		} else {
			$views['all'] = '<a href="'.admin_url('users.php').'">All <span class="count">('.$ultimatemember->query->count_users().')</span></a>';
		}

		$status = array(
			'approved' => __('Approved','ultimate-member'),
			'awaiting_admin_review' => __('Pending review','ultimate-member'),
			'awaiting_email_confirmation' => __('Waiting e-mail confirmation','ultimate-member'),
			'inactive' => __('Inactive','ultimate-member'),
			'rejected' => __('Rejected','ultimate-member')
		);

		$ultimatemember->query->count_users_by_status( 'unassigned' );

		foreach( $status as $k => $v ) {
			if ( isset($_REQUEST['status']) && $_REQUEST['status'] == $k ) {
				$current = 'class="current"';
			} else {
				$current = '';
			}

			$views[ $k ] = '<a href="'.admin_url('users.php').'?status='.$k.'" ' . $current . '>'. $v . ' <span class="count">('.$ultimatemember->query->count_users_by_status( $k ).')</span></a>';
		}

		$views = apply_filters('um_admin_views_users', $views );

		// remove all filters
		unset($old_views['all']);

		// add separator
		$views['subsep'] = '<span></span>';

		// merge views
		foreach( $old_views as $key => $view ) {
			$views[ $key ] = $view;
		}

		return $views;
	}

	/***
	***	@Bulk user editing actions
	***/
	function um_bulk_users_edit(){
		global $ultimatemember;

		$admin_err = 0;

		if (isset($_REQUEST) && !empty ($_REQUEST) ){

			// bulk change role
			if (isset($_REQUEST['users']) && is_array($_REQUEST['users']) && isset($_REQUEST['um_changeit']) && $_REQUEST['um_changeit'] != '' && isset($_REQUEST['um_change_role']) && !empty($_REQUEST['um_change_role']) ){

					if ( ! current_user_can( 'edit_users' ) )
						wp_die( __( 'You do not have enough permissions to do that.','ultimate-member') );

					check_admin_referer('bulk-users');

					$users = $_REQUEST['users'];
					$new_role = current( array_filter( $_REQUEST['um_change_role'] ) );

					foreach($users as $user_id){
						$ultimatemember->user->set( $user_id );
						// change role for non-wp admins or non-community admins only
						if ( current_user_can('manage_options') || ( !um_user('super_admin') || um_user('role') != 'admin' ) ) {
							$ultimatemember->user->set_role( $new_role );
						} else {
							$admin_err = 1;
						}
					}

					if ( $admin_err == 0 ){
						
						$uri = admin_url('users.php');
						
						$uri = $this->set_redirect_uri( $uri );
				
						$uri = add_query_arg( 'update', 'users_role_updated', $uri );
						
						wp_redirect( $uri );

						exit;

					} else {

						$uri = admin_url('users.php');
						
						$uri = $this->set_redirect_uri( $uri );
				
						$uri = add_query_arg( 'update', 'err_admin_role', $uri );

						wp_redirect( $uri );

						exit;
					}

			} else if ( isset($_REQUEST['um_changeit']) && $_REQUEST['um_changeit'] != '' ) {

				$uri = admin_url('users.php');
				
				$uri = $this->set_redirect_uri( $uri );
				
				wp_redirect( $uri );
				
				exit;

			}

			// bulk edit users
			if ( isset($_REQUEST['users']) && is_array($_REQUEST['users']) && isset($_REQUEST['um_bulkedit']) && $_REQUEST['um_bulkedit'] != '' && isset($_REQUEST['um_bulk_action']) && !empty($_REQUEST['um_bulk_action']) ){

					if ( ! current_user_can( 'edit_users' ) )
						wp_die( __( 'You do not have enough permissions to do that.','ultimate-member') );

					check_admin_referer('bulk-users');

					$users = $_REQUEST['users'];
					$bulk_action = current( array_filter( $_REQUEST['um_bulk_action']) );

					foreach($users as $user_id){
						$ultimatemember->user->set( $user_id );
						if ( !um_user('super_admin') ) {

							do_action("um_admin_user_action_hook", $bulk_action);

							do_action("um_admin_user_action_{$bulk_action}_hook");

						} else {
							$admin_err = 1;
						}
					}

					// Finished. redirect now
					if ( $admin_err == 0 ){

						$uri = admin_url('users.php');
						
						$uri = $this->set_redirect_uri( $uri );

						$uri = add_query_arg( 'update', 'users_updated', $uri );

						wp_redirect( $uri );

						exit;
					} else {
						wp_redirect( admin_url('users.php?update=err_users_updated') );
						exit;
					}

			} else if ( isset($_REQUEST['um_bulkedit']) && $_REQUEST['um_bulkedit'] != '' ) {

				$uri = admin_url('users.php');

				$uri = $this->set_redirect_uri( $uri );

				wp_redirect( $uri );

				exit;

			}

			// filter by user role
			if ( isset($_REQUEST['um_filter_role']) && ! isset( $_REQUEST['um_filter_processed'] ) && ( ! isset( $_REQUEST['new_role'] )  ||  empty( $_REQUEST['new_role'] ) ) && $_REQUEST['um_filter_role'] ) {
				$filter_role = current( array_filter(  $_REQUEST['um_filter_role'] ) );
				$uri = add_query_arg('um_role',$filter_role);
				$uri = add_query_arg('s',$_REQUEST['s'], $uri);
				$uri = add_query_arg('um_filter_processed',true, $uri);

				exit( wp_redirect( $uri ) );
			}

		}
	}

	/***
	***	@Add UM roles to users admin
	***/
	function restrict_manage_users() {
		global $ultimatemember;
		?>

			<div style="float:right;margin:0 4px">

				<label class="screen-reader-text" for="um_filter_role"><?php _e('Filter by','ultimate-member'); ?></label>
				<select name="um_filter_role[]" id="um_filter_role" class="" style="width: 120px">
					<option value="0"><?php _e('Filter by','ultimate-member'); ?></option>
					<?php
						$roles = $ultimatemember->query->get_roles();
						$um_filter_role =  '';
						if( isset( $_REQUEST['um_filter_role'] ) && is_array( $_REQUEST['um_filter_role'] ) ){
							$um_filter_role = current( array_filter( $_REQUEST['um_filter_role'] ) );
						}

						foreach( $roles as $role => $role_name ) { ?>
						<option value="<?php echo urlencode( $role ); ?>" <?php selected( $role, $um_filter_role ); ?>><?php echo $role_name; ?></option>
						<?php } ?>
				</select>

				<input name="um_role" id="um_role" class="button" value="<?php _e('Filter'); ?>" type="submit" />

			</div>

			<div style="float:right;margin:0 4px">

				<label class="screen-reader-text" for="um_bulk_action"><?php _e('UM Action','ultimate-member'); ?></label>
				<select name="um_bulk_action[]" id="um_bulk_action" class="" style="width: 200px">
					<option value="0"><?php _e('UM Action','ultimate-member'); ?></option>
					<?php echo $ultimatemember->user->get_bulk_admin_actions(); ?>
				</select>

				<input name="um_bulkedit" id="um_bulkedit" class="button" value="<?php _e('Apply'); ?>" type="submit" />

			</div>

			<div style="float:right;margin:0 4px">

				<label class="screen-reader-text" for="um_change_role"><?php _e('Community role&hellip;','ultimate-member'); ?></label>
				<select name="um_change_role[]" id="um_change_role" class="" style="width: 160px">
					<?php foreach($ultimatemember->query->get_roles( $add_default = 'Community role&hellip;' ) as $key => $value) { ?>
					<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
					<?php } ?>
				</select>

				<input name="um_changeit" id="um_changeit" class="button" value="<?php _e('Change','ultimate-member'); ?>" type="submit" />

			</div>

			<?php if( isset( $_REQUEST['status'] ) && ! empty( $_REQUEST['status'] ) ){ ?>
				<input type="hidden" name="status" id="um_status" value="<?php echo esc_attr( $_REQUEST['status'] );?>"/>
			<?php } ?>

		<?php

	}

	/***
	***	@add user columns
	***/
	function manage_users_columns($columns) {

		$admin = new UM_Admin_Metabox();

		$columns['um_role'] = __('Community Role','ultimate-member') . $admin->_tooltip( __('This is the membership role set by Ultimate Member plugin','ultimate-member') );

		return $columns;
	}

	/***
	***	@show user columns
	***/
	function manage_users_custom_column($value, $column_name, $user_id) {
		global $ultimatemember;

		if ( $this->custom_role == $column_name ) {

			if ( get_option( "um_cache_userdata_{$user_id}" ) ) {
				delete_option( "um_cache_userdata_{$user_id}" );
			}
			um_fetch_user( $user_id );
			return $ultimatemember->user->get_role_name( um_user('role') );

		}

		return $value;
	}

	/**
	 * Sets redirect URI 
	 * @param string $uri 
	 */
	function set_redirect_uri( $uri ){

		if( isset( $_REQUEST['s'] ) && ! empty( $_REQUEST['s'] ) ){
			$uri = add_query_arg( 's', $_REQUEST['s'], $uri );
		}

		if( isset( $_REQUEST['status'] ) && ! empty( $_REQUEST['status'] ) ){
			$uri = add_query_arg( 'status', $_REQUEST['status'], $uri );
		}

		if( isset( $_REQUEST['um_filter_role'] ) && ! empty( $_REQUEST['um_filter_role'] ) ){
			foreach ( $_REQUEST['um_filter_role'] as $key => $value) {
				$uri = add_query_arg( 'um_filter_role', $value, $uri );
			}
		}

		return $uri;

	}

}
