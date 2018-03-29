<?php

	/***
	***	@Main tabs
	***/
	add_filter('um_profile_tabs', 'um_profile_tabs', 1 );
	function um_profile_tabs( $tabs ) {

		$tabs['main'] = array(
			'name' => __('About','ultimate-member'),
			'icon' => 'um-faicon-user'
		);

		return $tabs;
	}

	/***
	***	@dynamic profile page title
	***/
	add_filter('wp_title', 'um_dynamic_user_profile_pagetitle', 100000, 2 );
	add_filter('pre_get_document_title', 'um_dynamic_user_profile_pagetitle', 100000, 2 );
	function um_dynamic_user_profile_pagetitle( $title, $sep = '' ) {
		global $paged, $page, $ultimatemember;

		$profile_title = um_get_option('profile_title');

		if ( um_is_core_page('user') && um_get_requested_user() ) {

			um_fetch_user( um_get_requested_user() );

			$profile_title = um_convert_tags( $profile_title );

			$title = $profile_title;

			um_reset_user();

		}

		return $title;
	}

	/***
	***	@try and modify the page title in page
	***/
	add_filter('the_title', 'um_dynamic_user_profile_title', 100000, 2 );
	function um_dynamic_user_profile_title( $title, $id = '' ) {
		global $ultimatemember;


		if( is_admin() ){
			return $title;
		}

		if (  $id == $ultimatemember->permalinks->core['user'] && in_the_loop() ) {
			if ( um_is_core_page('user') && um_get_requested_user() ) {
				$title = um_get_display_name( um_get_requested_user() );
			} else if ( um_is_core_page('user') && is_user_logged_in() ) {
				$title = um_get_display_name( get_current_user_id() );
			}
		}


		if( ! function_exists('utf8_decode') ){
			return $title;
		}

		return (strlen($title)!==strlen(utf8_decode($title))) ? $title : utf8_encode($title);
	}



	/***
	*** @Add cover photo label of file size limit
	***/
	add_filter('um_predefined_fields_hook','um_change_profile_cover_photo_label',10,1);
	function um_change_profile_cover_photo_label( $args ){
		global $ultimatemember;
		$max_size =  $ultimatemember->files->format_bytes( $args['cover_photo']['max_size'] );
		list( $file_size, $unit ) = explode(' ', $max_size );

		if( $file_size >= 999999999  ){

		}else{
			$args['cover_photo']['upload_text'] .= '<small class=\'um-max-filesize\'>( '.__('max','ultimate-member').': <span>'.$file_size.$unit.'</span> )</small>';
		}
		return $args;
	}


	/***
	*** @Add profile photo label of file size limit
	***/
	add_filter('um_predefined_fields_hook','um_change_profile_photo_label',10,1);
	function um_change_profile_photo_label( $args ){
		global $ultimatemember;
		$max_size =  $ultimatemember->files->format_bytes( $args['profile_photo']['max_size'] );
		list( $file_size, $unit ) = explode(' ', $max_size );

		if( $file_size >= 999999999  ){

		}else{
			$args['profile_photo']['upload_text'] .= '<small class=\'um-max-filesize\'>( '.__('max','ultimate-member').': <span>'.$file_size.$unit.'</span> )</small>';
		}
		return $args;
	}
