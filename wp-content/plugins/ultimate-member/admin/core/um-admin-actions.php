<?php

	/***
	***	@Add community role to user creatino page
	***/
	add_action('admin_footer_text', 'um_add_custom_user_profile_fields');
	function um_add_custom_user_profile_fields($footer_text) {
		global $ultimatemember, $pagenow;

		if( $pagenow !== 'user-new.php' )
			return $footer_text;

		if( !current_user_can('manage_options') )
			return $footer_text;

	?>
	<table id="table_my_custom_field" style="display:none;">
		<tr>
			<th><label for="um_role"><?php _e( 'Community Role', 'ultimate-member'); ?></label></th>
			<td>
				<select name="um_role" id="um_role">
				<?php foreach( $ultimatemember->query->get_roles() as $key => $value ) { ?>
					<option value="<?php echo $key; ?>" <?php selected( um_get_option('default_role'), $key ); ?> ><?php echo $value; ?></option>
				<?php } ?>
				</select>
			</td>
		</tr>
	</table>
	<script>
	jQuery(function($){
		$('#table_my_custom_field tr').insertAfter($('#role').parentsUntil('tr').parent());
	});
	</script>
	<?php
	}

	/***
	***	@Save the community role in user creation in backend
	***/
	add_action('user_register', 'um_save_custom_user_profile_fields');
	function um_save_custom_user_profile_fields( $user_id ) {
		if( !current_user_can('manage_options') || !is_admin() || !isset( $_POST['um_role'] ) )
			return false;
		update_user_meta($user_id, 'role', sanitize_title_with_dashes( $_POST['um_role'] ) );
	}

	/**
	*
	* Add access settings to category
	*
	**/

	$exclude_taxonomies = array(
			'nav_menu',
			'link_category',
			'post_format',
			'um_user_tag',
			'um_hashtag',
	);

	$taxonomies = get_taxonomies(); 


	foreach ($taxonomies as $key => $taxonomy) {
		if( ! in_array( $key , $exclude_taxonomies ) ){
			add_action( $taxonomy.'_add_form_fields', 'um_category_access_fields_create' );
			add_action( $taxonomy.'_edit_form_fields', 'um_category_access_fields_edit' );
			add_action( 'create_'.$taxonomy, 'um_category_access_fields_save' );
			add_action( 'edited_'.$taxonomy, 'um_category_access_fields_save' );
		}
	}


	function um_category_access_fields_create( $term ){
		global $ultimatemember;

		echo '<div class="form-field term-access-wrap um-conditional-radio-group" data-cond1="2" data-cond1-show="term-roles-wrap" data-cond2="1" data-cond2-show="term-redirect-wrap">';
		echo '<label>' . __('Content Availability','ultimate-member') . '</label>';
		echo '<label><input type="radio" name="_um_accessible" value="0" checked /> '. __('Content accessible to Everyone','ultimate-member') . '</label>
			<label><input type="radio" name="_um_accessible" value="1" /> ' . __('Content accessible to Logged Out Users','ultimate-member') . '</label>
			<label><input type="radio" name="_um_accessible" value="2" /> ' . __('Content accessible to Logged In Users','ultimate-member') . '</label>';
		echo '<p class="description">Who can see content/posts in this category.</p>';
		echo '</div>';
		
		echo '<div class="form-field term-roles-wrap">';
		echo '<label>' . __('Roles who can see the content','ultimate-member') . '</label>';
		foreach($ultimatemember->query->get_roles() as $role_id => $role) {
		echo '<label><input type="checkbox" name="_um_roles[]" value="' . $role_id . '" /> ' . $role . '</label>';
		}
		echo '<p class="description">' . __('This is applicable only if you restrict the content to logged-in users.','ultimate-member') . '</p>';
		echo '<label>' . __('Content Restriction Redirect URL','ultimate-member') . '</label>';
		echo '<input type="text" name="_um_redirect" id="_um_redirect" value="" />';
		echo '<p class="description">' . __('Users who cannot see content will get redirected to that URL.','ultimate-member') . '</p>';
		echo '</div>';
		
		echo '<div class="form-field term-redirect-wrap">';
		echo '<label>' . __('Content Restriction Redirect URL','ultimate-member') . '</label>';
		echo '<input type="text" name="_um_redirect2" id="_um_redirect2" value="" />';
		echo '<p class="description">' . __('Users who cannot see content will get redirected to that URL.','ultimate-member') . '</p>';
		echo '</div>';
	
	}

	function um_category_access_fields_edit( $term ){
		global $ultimatemember;

		$termID = $term->term_id;
		$termMeta = get_option( "category_$termID" );    
		$_um_accessible= (isset( $termMeta['_um_accessible'] ) )? $termMeta['_um_accessible'] : '';
		$_um_redirect=  (isset( $termMeta['_um_redirect'] ) )? $termMeta['_um_redirect'] : '';
		$_um_redirect2=  (isset( $termMeta['_um_redirect2'] ) )? $termMeta['_um_redirect2'] : '';
		$_um_roles=  (isset( $termMeta['_um_roles'] ) )? $termMeta['_um_roles'] : '';

		echo '<tr class="form-field form-required term-access-wrap um-conditional-radio-group" data-cond1="2" data-cond1-show="term-roles-wrap" data-cond2="1" data-cond2-show="term-redirect-wrap" >';
		echo "<th scope='row'><label>" . __('Content Availability','ultimate-member') . "</label></th>";
		echo '<td><label><input type="radio" name="_um_accessible" value="0"  ' . checked( 0, $_um_accessible, 0 ) . ' /> '. __('Content accessible to Everyone','ultimate-member') . '</label><br />
			<label><input type="radio" name="_um_accessible" value="1" ' . checked( 1, $_um_accessible, 0 ) . ' /> ' . __('Content accessible to Logged Out Users','ultimate-member') . '</label><br />
			<label><input type="radio" name="_um_accessible" value="2" ' . checked( 2, $_um_accessible, 0 ) . ' /> ' . __('Content accessible to Logged In Users','ultimate-member') . '</label>';
		echo '<p class="description">Who can see content/posts in this category.</p>';
		echo "</td></tr>";
		
		echo "<tr class='form-field form-required term-roles-wrap'>";
		echo "<th scope='row'><label>" .  __('Roles who can see the content','ultimate-member') . "</label></th>";
		echo '<td>';
		foreach($ultimatemember->query->get_roles() as $role_id => $role) {
			if (  ( isset( $_um_roles ) && is_array( $_um_roles ) && in_array($role_id, $_um_roles ) ) || ( isset( $_um_roles ) && $role_id == $_um_roles ) ) {
				$checked = 'checked';
			} else {
				$checked = '';
			}
		echo '<label><input type="checkbox" name="_um_roles[]" value="' . $role_id . '" ' .  $checked . ' /> ' . $role . '</label>&nbsp;&nbsp;';
		}
		echo '<p class="description">' . __('This is applicable only if you restrict the content to logged-in users.','ultimate-member') . '</p>';
		echo "</td></tr>";
		echo "<tr class='form-field form-required term-roles-wrap'>";
		echo "<th scope='row'><label>" . __('Content Restriction Redirect URL','ultimate-member') . "</label></th>";
		echo '<td>';
		echo '<input type="text" name="_um_redirect" id="_um_redirect" value="' . $_um_redirect . '" />';
		echo '<p class="description">' . __('Users who cannot see content will get redirected to that URL.','ultimate-member') . '</p>';
		echo "</td></tr>";
		
		echo "<tr class='form-field form-required term-redirect-wrap'>";
		echo "<th scope='row'><label>" . __('Content Restriction Redirect URL','ultimate-member') . "</label></th>";
		echo '<td>';
		echo '<input type="text" name="_um_redirect2" id="_um_redirect2" value="' . $_um_redirect2 . '" />';
		echo '<p class="description">' . __('Users who cannot see content will get redirected to that URL.','ultimate-member') . '</p>';
		echo "</td></tr>";
		
	}

	function um_category_access_fields_save( $termID ){

		if ( isset( $_POST['_um_accessible'] ) ) {
			
			// get options from database - if not a array create a new one
			$termMeta = get_option( "category_$termID" );
			if ( !is_array( $termMeta ))
				$termMeta = array();
			
			// get value and save it into the database - maybe you have to sanitize your values (urls, etc...)
			$termMeta['_um_accessible'] = isset( $_POST['_um_accessible'] ) ? $_POST['_um_accessible'] : '';
			$termMeta['_um_redirect'] = isset( $_POST['_um_redirect'] ) ? $_POST['_um_redirect'] : '';
			$termMeta['_um_redirect2'] = isset( $_POST['_um_redirect2'] ) ? $_POST['_um_redirect2'] : '';
			$termMeta['_um_roles'] = isset( $_POST['_um_roles'] ) ? $_POST['_um_roles'] : '';
			
			update_option( "category_$termID", $termMeta );
		}
	}
	
	/***
	***	@Allow mass syncing for roles
	***/
	add_action('um_admin_do_action__mass_role_sync', 'um_admin_do_action__mass_role_sync');
	function um_admin_do_action__mass_role_sync( $action ){
		global $ultimatemember;
		if ( !is_admin() || !current_user_can( 'edit_user' ) ) wp_die('You don\'t have permissions to access this page.');
		
		if ( !isset($_REQUEST['post']) || !is_numeric( $_REQUEST['post'] ) )  wp_die('You\'re not allowed to do this.');

		$post_id = (int) $_REQUEST['post'];
		
		$post = get_post( $post_id );
		$slug = $post->post_name;

		if ( $slug != $_REQUEST['um_role'] )
			wp_die('Invalid community role.');
		
		$um_synced_role = get_post_meta( $post_id, '_um_synced_role', true );

		if ( $slug == 'admin' ) {
			$_REQUEST['wp_role'] = 'administrator';
			update_post_meta( $post_id, '_um_synced_role', 'administrator' );
		}else{
			update_post_meta( $post_id, '_um_synced_role', $_REQUEST['wp_role'] );
		}
		
		$wp_role = ( $_REQUEST['wp_role'] ) ? $_REQUEST['wp_role'] : 'subscriber';
		
		$users = get_users( array( 'fields' => array( 'ID' ), 'meta_key' => 'role', 'meta_value' => $slug ) );
		foreach( $users as $user_id ) {
			$wp_user_object = new WP_User( $user_id );
			$wp_user_object->set_role( $wp_role );
		}
		
		exit( wp_redirect( admin_url( 'post.php?post=' . $post_id ) . '&action=edit&message=1' ) );
	
	}
	
	/***
	***	@add option for WPML
	***/
	add_action('um_admin_before_access_settings', 'um_admin_wpml_post_options', 10, 1 );
	function um_admin_wpml_post_options( $instance ) {
	
		if ( !function_exists('icl_get_current_language') )
			return;
		
		?>
		
		<h4><?php _e('This is a translation of UM profile page?','ultimate-member'); ?></h4>
		
		<p>
			<span><?php $instance->ui_on_off( '_um_wpml_user', 0 ); ?></span>
		</p>
		
		<h4><?php _e('This is a translation of UM account page?','ultimate-member'); ?></h4>
		
		<p>
			<span><?php $instance->ui_on_off( '_um_wpml_account', 0 ); ?></span>
		</p>
		
		<?php
	
	}
	
	/***
	***	@when role is saved
	***/
	function um_admin_delete_role_cache($post_id, $post){
		global $wpdb, $ultimatemember;
		if( get_post_type( $post_id ) == 'um_role') {
			$slug = $post->post_name;
			
			$is_core = get_post_meta( $post_id, '_um_core', true );
			if ( $is_core == 'member' || $is_core == 'admin' ) {
				$slug = $is_core;
				$where = array( 'ID' => $post_id );
				$wpdb->update( $wpdb->posts, array( 'post_name' => $slug ), $where );
			}
			
			delete_option("um_cached_role_{$slug}");

			// need to remove cache of all users
			$users = get_users( array( 'fields' => array( 'ID' ), 'meta_key' => 'role', 'meta_value' => $slug ) );
			foreach( $users as $user ) {
				$ultimatemember->user->remove_cache( $user->ID );
			}
		}
	}
	add_action('save_post', 'um_admin_delete_role_cache', 1111, 2);
	
	/***
	***	@clear user cache
	***/
	add_action('um_admin_do_action__user_cache', 'um_admin_do_action__user_cache');
	function um_admin_do_action__user_cache( $action ){
		global $ultimatemember, $wpdb;
		if ( !is_admin() || !current_user_can('manage_options') ) die();
		

		$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'um_cache_userdata_%'" );

		
		$url = admin_url('admin.php?page=ultimatemember');
		$url = add_query_arg('update','cleared_cache',$url);
		exit( wp_redirect($url) );
	}
	
	/***
	***	@purge temp
	***/
	add_action('um_admin_do_action__purge_temp', 'um_admin_do_action__purge_temp');
	function um_admin_do_action__purge_temp( $action ){
		global $ultimatemember;
		if ( !is_admin() || !current_user_can('manage_options') ) die();
		
		$ultimatemember->files->remove_dir( $ultimatemember->files->upload_temp );
		
		$url = remove_query_arg('um_adm_action', $ultimatemember->permalinks->get_current_url() );
		$url = add_query_arg('update','purged_temp',$url);
		exit( wp_redirect($url) );
	}
	
	/***
	***	@duplicate form
	***/
	add_action('um_admin_do_action__duplicate_form', 'um_admin_do_action__duplicate_form');
	function um_admin_do_action__duplicate_form( $action ){
		global $ultimatemember;
		if ( !is_admin() || !current_user_can('manage_options') ) die();
		if ( !isset($_REQUEST['post_id']) || !is_numeric( $_REQUEST['post_id'] ) ) die();
		
		$post_id = $_REQUEST['post_id'];
		
		$n = array(
			'post_type' 	  	=> 'um_form',
			'post_title'		=> sprintf(__('Duplicate of %s','ultimate-member'), get_the_title($post_id) ),
			'post_status'		=> 'publish',
			'post_author'   	=> um_user('ID'),
		);

		$n_id = wp_insert_post( $n );
		
		$n_fields = get_post_custom( $post_id );
		foreach ( $n_fields as $key => $value ) {
			
			if ( $key == '_um_custom_fields' ) {
				$the_value = unserialize( $value[0] );
			} else {
				$the_value = $value[0];
			}
		
			update_post_meta( $n_id, $key, $the_value );
			
		}
		
		delete_post_meta($n_id, '_um_core');
		
		$url = admin_url('edit.php?post_type=um_form');
		$url = add_query_arg('update','form_duplicated',$url);
		
		exit( wp_redirect( $url ) );
		
	}

	/***
	***	@download a language remotely
	***/
	add_action('um_admin_do_action__um_language_downloader', 'um_admin_do_action__um_language_downloader');
	function um_admin_do_action__um_language_downloader( $action ){
		global $ultimatemember;
		if ( !is_admin() || !current_user_can('manage_options') ) die();
		
		$locale = get_option('WPLANG');
		if ( !$locale ) return;
		if ( !isset( $ultimatemember->available_languages[$locale] ) ) return;
		
		$path = $ultimatemember->files->upload_basedir;
		$path = str_replace('/uploads/ultimatemember','',$path);
		$path = $path . '/languages/plugins/';
		$path = str_replace('//','/',$path);
		
		$remote = 'https://ultimatemember.com/wp-content/languages/plugins/ultimatemember-' . $locale . '.po';
		$remote2 = 'https://ultimatemember.com/wp-content/languages/plugins/ultimatemember-' . $locale . '.mo';

		$remote_tmp = download_url( $remote, $timeout = 300 );
		copy( $remote_tmp, $path . 'ultimatemember-' . $locale . '.po' );
		unlink( $remote_tmp );
		
		$remote2_tmp = download_url( $remote2, $timeout = 300 );
		copy( $remote2_tmp, $path . 'ultimatemember-' . $locale . '.mo' );
		unlink( $remote2_tmp );

		$url = remove_query_arg('um_adm_action', $ultimatemember->permalinks->get_current_url() );
		$url = add_query_arg('update','language_updated',$url);
		exit( wp_redirect($url) );
		
	}
	
	/***
	***	@Action to hide notices in admin
	***/
	add_action('um_admin_do_action__um_hide_locale_notice', 'um_admin_do_action__hide_notice');
	add_action('um_admin_do_action__um_can_register_notice', 'um_admin_do_action__hide_notice');
	add_action('um_admin_do_action__um_hide_exif_notice', 'um_admin_do_action__hide_notice');
	function um_admin_do_action__hide_notice( $action ){
		global $ultimatemember;
		if ( !is_admin() || !current_user_can('manage_options') ) die();
		update_option( $action, 1 );
		exit( wp_redirect( remove_query_arg('um_adm_action') ) );
	}
	
	/***
	***	@Opt-in tracking
	***/
	add_action('um_admin_do_action__opt_into_tracking', 'um_admin_do_action__opt_into_tracking');
	function um_admin_do_action__opt_into_tracking( $action ){
		global $ultimatemember;
		if ( !is_admin() || !current_user_can('manage_options') ) die();
		
		global $reduxConfig;
		$reduxConfig->ReduxFramework->set('allow_tracking', 1);
		
		update_option('um_tracking_notice', 1 );
		
		$tracking = new UM_Tracking();
		$tracking->send_checkin(true);
		
		exit( wp_redirect( remove_query_arg('um_adm_action') ) );
	}
	
	/***
	***	@Opt-out of tracking
	***/
	add_action('um_admin_do_action__opt_out_of_tracking', 'um_admin_do_action__opt_out_of_tracking');
	function um_admin_do_action__opt_out_of_tracking( $action ){
		global $ultimatemember;
		if ( !is_admin() || !current_user_can('manage_options') ) die();
		
		global $reduxConfig;
		$reduxConfig->ReduxFramework->set('allow_tracking', 0);
		
		update_option('um_tracking_notice', 1 );
		
		exit( wp_redirect( remove_query_arg('um_adm_action') ) );
	}
	
	/***
	***	@Un-install UM completely
	***/
	add_action('um_admin_do_action__uninstall_ultimatemember', 'um_admin_do_action__uninstall_ultimatemember');
	function um_admin_do_action__uninstall_ultimatemember( $action ){
		global $ultimatemember;
		if ( !is_admin() || !current_user_can('manage_options') ) die();
		
		$ultimatemember->uninstall->remove_um();
		
	}
	
	/***
	***	@various user actions
	***/
	add_action('um_admin_do_action__user_action', 'um_admin_do_action__user_action');
	function um_admin_do_action__user_action( $action ){
		global $ultimatemember;
		if ( !is_admin() || !current_user_can( 'edit_users' ) ) die();
		if ( !isset( $_REQUEST['sub'] ) ) die();
		if ( !isset($_REQUEST['user_id']) ) die();
		
		um_fetch_user( $_REQUEST['user_id'] );
	
		$subaction = $_REQUEST['sub'];

		do_action("um_admin_user_action_hook", $subaction);
		do_action("um_admin_user_action_{$subaction}_hook");
		
		um_reset_user();
		
		wp_redirect( add_query_arg( 'update', 'user_updated', admin_url('?page=ultimatemember') ) );
		exit;
		
	}
