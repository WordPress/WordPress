<?php

	/***
	***	@sync with WP role
	***/
	add_action('um_after_user_role_is_updated','um_setup_synced_wp_role', 50, 2);
	function um_setup_synced_wp_role( $user_id, $role ) {
		global $ultimatemember;
		
		$meta = $ultimatemember->query->role_data( $role );
		$meta = apply_filters('um_user_permissions_filter', $meta, $user_id );
		$wp_user_object = new WP_User( $user_id );
		
		if ( isset( $meta['synced_role'] ) && $meta['synced_role'] ) {
			$wp_user_object->add_role( $meta['synced_role'] );
		}elseif( ! $wp_user_object->roles ) { // Fallback user default role if nothing set
			$wp_user_object->add_role( 'subscriber' );
		}
	}

	/***
	*** @remove previously synced WP role
	***/
	add_action('um_when_role_is_set', 'um_remove_prev_synced_wp_role');
	function um_remove_prev_synced_wp_role( $user_id ) {
		global $ultimatemember;

		um_fetch_user( $user_id );
		$role = um_user('role');
		$meta = $ultimatemember->query->role_data( $role );
		if ( isset( $meta['synced_role'] ) && $meta['synced_role'] ) {
			$wp_user_object = new WP_User( $user_id );
			$wp_user_object->remove_role( $meta['synced_role'] );
		}
	}


	/***
	***	@after user uploads, clean up uploads dir
	***/
	add_action('um_after_user_upload','um_remove_unused_uploads', 10);
	function um_remove_unused_uploads( $user_id ) {
		global $ultimatemember;

		um_fetch_user( $user_id );

		$array = $ultimatemember->user->profile;

		$files = glob( um_user_uploads_dir() . '*', GLOB_BRACE);

		if ( file_exists( um_user_uploads_dir() ) && $files && isset( $array ) && is_array( $array ) ) {

			foreach($files as $file) {
				$str = basename($file);
				if ( !strstr( $str, 'profile_photo') && !strstr( $str, 'cover_photo') && !strstr( $str, 'stream_photo') && !preg_grep('/' . $str . '/', $array ) )
					unlink( $file );
			}

		}

	}

	/***
	***	@listen to a new user creation in backend
	***/
	add_action( 'user_register', 'um_new_user_via_wpadmin', 10, 1 );
	function um_new_user_via_wpadmin( $user_id ) {

		if ( is_admin() ) {

			global $ultimatemember;

			if( isset( $_POST['um_role'] ) ) {
				$args['role'] = $_POST['um_role'];
			} else {
				$args['role'] = um_get_option('default_role');
			}

			do_action('um_after_new_user_register', $user_id, $args);

			do_action('um_update_profile_full_name', $_POST);

			// generate profile slug 
			$ultimatemember->permalinks->profile_url( true );

		}

	}

	/***
	***	@adds main links to a logout widget
	***/
	add_action('um_logout_user_links', 'um_logout_user_links', 100 );
	function um_logout_user_links( $args ) {
		global $ultimatemember;

	?>

		<li><a href="<?php echo um_get_core_page('account'); ?>"><?php _e('Your account','ultimate-member'); ?></a></li>
		<li><a href="<?php echo esc_url( add_query_arg('redirect_to', $ultimatemember->permalinks->get_current_url(true), um_get_core_page('logout') ) ); ?>"><?php _e('Logout','ultimate-member'); ?></a></li>

	<?php

	}
