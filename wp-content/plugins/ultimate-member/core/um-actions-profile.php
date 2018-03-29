<?php

	/***
	***	@um_profile_content_{main_tab}
	***/
	add_action('um_profile_content_main','um_profile_content_main');
	function um_profile_content_main( $args ) {
		extract( $args );

		if ( !um_get_option('profile_tab_main') && !isset( $_REQUEST['um_action'] ) )
			return;

		$can_view = apply_filters('um_profile_can_view_main', -1, um_profile_id() );

		if ( $can_view == -1 ) {

			do_action("um_before_form", $args);

			do_action("um_before_{$mode}_fields", $args);

			do_action("um_main_{$mode}_fields", $args);

			do_action("um_after_form_fields", $args);

			do_action("um_after_{$mode}_fields", $args);

			do_action("um_after_form", $args);

		} else {

			?>

			<div class="um-profile-note"><span><i class="um-faicon-lock"></i><?php echo $can_view; ?></span></div>

			<?php

		}

	}

	/***
	***	@update user's profile
	***/
	add_action('um_user_edit_profile', 'um_user_edit_profile', 10);
	function um_user_edit_profile( $args ){

		global $ultimatemember;

		$to_update = null;
		$files = null;

		if ( isset( $args['user_id'] ) ) {
			if ( um_current_user_can('edit', $args['user_id'] ) ) {
				$ultimatemember->user->set( $args['user_id'] );
			} else {
				wp_die( __('You are not allowed to edit this user.','ultimate-member') );
			}
		} else if ( isset( $args['_user_id'] ) ) {
			$ultimatemember->user->set( $args['_user_id'] );
		}

		$userinfo = $ultimatemember->user->profile;

		$fields = unserialize( $args['custom_fields'] );

		do_action('um_user_before_updating_profile', $userinfo );

		// loop through fields
		if ( isset( $fields ) && is_array( $fields ) ) {
			foreach( $fields as $key => $array ) {

				if( !um_user_can( 'can_edit_everyone' ) && isset($fields[$key]['editable']) && !$fields[$key]['editable'] ) continue;

				if ( $fields[$key]['type'] == 'multiselect' ||  $fields[$key]['type'] == 'checkbox' && !isset($args['submitted'][$key]) ) {
					delete_user_meta( um_user('ID'), $key );
				}

				if ( isset( $args['submitted'][ $key ] ) ) {

					if ( isset( $fields[$key]['type'] ) && in_array( $fields[$key]['type'], array('image','file') ) && 
						( um_is_temp_upload( $args['submitted'][ $key ] ) || $args['submitted'][ $key ] == 'empty_file' ) ) {

						$files[ $key ] = $args['submitted'][ $key ];

					} else {

						if ( isset( $userinfo[$key]) && $args['submitted'][$key] != $userinfo[$key] ) {
							$to_update[ $key ] = $args['submitted'][ $key ];
						} else if ( $args['submitted'][$key] ) {
							$to_update[ $key ] = $args['submitted'][ $key ];
						}

					}

				}
			}
		}

		if ( isset( $args['submitted']['description'] ) ) {
			$to_update['description'] = $args['submitted']['description'];
		}

		if ( isset( $args['submitted']['role'] ) && !empty( $args['submitted']['role'] ) ) {
			$to_update['role'] = $args['submitted']['role'];
		}

		do_action('um_user_pre_updating_profile', $to_update );

		$to_update = apply_filters('um_user_pre_updating_profile_array', $to_update);

		if ( is_array( $to_update ) ) {
			$ultimatemember->user->update_profile( $to_update );
			do_action('um_after_user_updated', um_user('ID') );
		
		}

		$files = apply_filters('um_user_pre_updating_files_array', $files);
		
		if ( is_array( $files ) ) {
			do_action('um_before_user_upload', um_user('ID'), $files );
			$ultimatemember->user->update_files( $files );
			do_action('um_after_user_upload', um_user('ID'), $files );
		}

		do_action('um_user_after_updating_profile', $to_update );

		do_action('um_update_profile_full_name', $to_update );


		if ( !isset( $args['is_signup'] ) ) {
			$url = $ultimatemember->permalinks->profile_url( true );
			exit( wp_redirect( um_edit_my_profile_cancel_uri( $url ) ) );
		}

	}

	/***
	***	@if editing another user
	***/
	add_action('um_after_form_fields', 'um_editing_user_id_input');
	function um_editing_user_id_input($args){
		global $ultimatemember;
		if ( $ultimatemember->fields->editing == 1 && $ultimatemember->fields->set_mode == 'profile' && $ultimatemember->user->target_id ) { ?>

		<input type="hidden" name="user_id" id="user_id" value="<?php echo $ultimatemember->user->target_id; ?>" />

		<?php

		}
	}

	/***
	***	@meta description
	***/
	add_action('wp_head', 'um_profile_dynamic_meta_desc', 9999999);
	function um_profile_dynamic_meta_desc() {
		global $ultimatemember;

		if ( um_is_core_page('user') && um_get_requested_user() ) {

			um_fetch_user( um_get_requested_user() );

			$content = um_convert_tags( um_get_option('profile_desc') );
			$user_id = um_user('ID');
			$url = um_user_profile_url();

			if ( um_profile('profile_photo') ) {
				$avatar = um_user_uploads_uri() . um_profile('profile_photo');
			} else {
				$avatar = um_get_default_avatar_uri();
			}

			um_reset_user(); ?>

			<meta name="description" content="<?php echo $content; ?>">

			<meta property="og:title" content="<?php echo um_get_display_name( $user_id ); ?>" />
			<meta property="og:type" content="article" />
			<meta property="og:image" content="<?php echo $avatar; ?>" />
			<meta property="og:url" content="<?php echo $url; ?>" />
			<meta property="og:description" content="<?php echo $content; ?>" />

		<?php
		}
	}

	/***
	***	@profile header cover
	***/
	add_action('um_profile_header_cover_area', 'um_profile_header_cover_area', 9 );
	function um_profile_header_cover_area( $args ) {
		global $ultimatemember;

		if ( $args['cover_enabled'] == 1 ) {

			$default_cover = um_get_option('default_cover');

			$overlay = '<span class="um-cover-overlay">
				<span class="um-cover-overlay-s">
					<ins>
						<i class="um-faicon-picture-o"></i>
						<span class="um-cover-overlay-t">'.__('Change your cover photo','ultimate-member').'</span>
					</ins>
				</span>
			</span>';

		?>

			<div class="um-cover <?php if ( um_profile('cover_photo') || ( $default_cover && $default_cover['url'] ) ) echo 'has-cover'; ?>" data-user_id="<?php echo um_profile_id(); ?>" data-ratio="<?php echo $args['cover_ratio']; ?>">

				<?php do_action('um_cover_area_content', um_profile_id() ); ?>

				<?php

					if ( $ultimatemember->fields->editing ) {

						$items = array(
									'<a href="#" class="um-manual-trigger" data-parent=".um-cover" data-child=".um-btn-auto-width">'.__('Change cover photo','ultimate-member').'</a>',
									'<a href="#" class="um-reset-cover-photo" data-user_id="'.um_profile_id().'">'.__('Remove','ultimate-member').'</a>',
									'<a href="#" class="um-dropdown-hide">'.__('Cancel','ultimate-member').'</a>',
						);

						echo $ultimatemember->menu->new_ui( 'bc', 'div.um-cover', 'click', $items );

					}
				?>

				<?php $ultimatemember->fields->add_hidden_field( 'cover_photo' ); ?>

				<?php echo $overlay; ?>

				<div class="um-cover-e">

					<?php if ( um_profile('cover_photo') ) { ?>

					<?php

					if( $ultimatemember->mobile->isMobile() ){
						if ( $ultimatemember->mobile->isTablet() ) {
							echo um_user('cover_photo', 1000);
						} else {
							echo um_user('cover_photo', 300);
						}
					} else {
						echo um_user('cover_photo', 1000);
					}

					?>

					<?php } elseif ( $default_cover && $default_cover['url'] ) {

						$default_cover = $default_cover['url'];

						echo '<img src="'. $default_cover . '" alt="" />';

					} else {

						if ( !isset( $ultimatemember->user->cannot_edit ) ) { ?>

						<a href="#" class="um-cover-add um-manual-trigger" data-parent=".um-cover" data-child=".um-btn-auto-width"><span class="um-cover-add-i"><i class="um-icon-plus um-tip-n" title="<?php _e('Upload a cover photo','ultimate-member'); ?>"></i></span></a>

					<?php }

					} ?>

				</div>

			</div>

			<?php

		}

	}

	/***
	***	@Show social links as icons below profile name
	***/
	add_action('um_after_profile_header_name_args','um_social_links_icons', 50 );
	function um_social_links_icons( $args ) {
		global $ultimatemember;
		if ( isset($args['show_social_links']) && $args['show_social_links'] ) {

			echo '<div class="um-profile-connect um-member-connect">';
			echo $ultimatemember->fields->show_social_urls();
			echo '</div>';

		}
	}

	/***
	***	@profile header
	***/
	add_action('um_profile_header', 'um_profile_header', 9 );
	function um_profile_header( $args ) {
		global $ultimatemember;

		$classes = null;

		if ( !$args['cover_enabled'] ) {
			$classes .= ' no-cover';
		}

		$default_size = str_replace( 'px', '', $args['photosize'] );

		$overlay = '<span class="um-profile-photo-overlay">
			<span class="um-profile-photo-overlay-s">
				<ins>
					<i class="um-faicon-camera"></i>
				</ins>
			</span>
		</span>';

		?>

			<div class="um-header<?php echo $classes; ?>">

				<?php do_action('um_pre_header_editprofile', $args); ?>

				<div class="um-profile-photo" data-user_id="<?php echo um_profile_id(); ?>">

					<a href="<?php echo um_user_profile_url(); ?>" class="um-profile-photo-img" title="<?php echo um_user('display_name'); ?>"><?php echo $overlay . get_avatar( um_user('ID'), $default_size ); ?></a>

					<?php

					if ( !isset( $ultimatemember->user->cannot_edit ) ) {

						$ultimatemember->fields->add_hidden_field( 'profile_photo' );

						if ( !um_profile('profile_photo') ) { // has profile photo

							$items = array(
								'<a href="#" class="um-manual-trigger" data-parent=".um-profile-photo" data-child=".um-btn-auto-width">'.__('Upload photo','ultimate-member').'</a>',
								'<a href="#" class="um-dropdown-hide">'.__('Cancel','ultimate-member').'</a>',
							);

							$items = apply_filters('um_user_photo_menu_view', $items );

							echo $ultimatemember->menu->new_ui( 'bc', 'div.um-profile-photo', 'click', $items );

						} else if ( $ultimatemember->fields->editing == true ) {

							$items = array(
								'<a href="#" class="um-manual-trigger" data-parent=".um-profile-photo" data-child=".um-btn-auto-width">'.__('Change photo','ultimate-member').'</a>',
								'<a href="#" class="um-reset-profile-photo" data-user_id="'.um_profile_id().'" data-default_src="'.um_get_default_avatar_uri().'">'.__('Remove photo','ultimate-member').'</a>',
								'<a href="#" class="um-dropdown-hide">'.__('Cancel','ultimate-member').'</a>',
							);

							$items = apply_filters('um_user_photo_menu_edit', $items );

							echo $ultimatemember->menu->new_ui( 'bc', 'div.um-profile-photo', 'click', $items );

						}

					}

					?>

				</div>

				<div class="um-profile-meta">

					<div class="um-main-meta">

						<?php if ( $args['show_name'] ) { ?>
						<div class="um-name">

							<a href="<?php echo um_user_profile_url(); ?>" title="<?php echo um_user('display_name'); ?>"><?php echo um_user('display_name', 'html'); ?></a>

							<?php do_action('um_after_profile_name_inline', $args ); ?>

						</div>
						<?php } ?>

						<div class="um-clear"></div>

						<?php do_action('um_after_profile_header_name_args', $args ); ?>
						<?php do_action('um_after_profile_header_name'); ?>

					</div>

					<?php if ( isset( $args['metafields'] ) && !empty( $args['metafields'] ) ) { ?>
					<div class="um-meta">

						<?php echo $ultimatemember->profile->show_meta( $args['metafields'] ); ?>

					</div>
					<?php } ?>

					<?php if ( $ultimatemember->fields->viewing == true && um_user('description') && $args['show_bio'] ) { ?>

					<div class="um-meta-text">
						<?php 
						
						$description = get_user_meta( um_user('ID') , 'description', true);
					    if( um_get_option( 'profile_show_html_bio' ) ) : ?>
							<?php echo make_clickable( wpautop( wp_kses_post( $description ) ) ); ?>
						<?php else : ?>
							<?php echo esc_html( $description ); ?>
						<?php endif; ?>
					</div>

					<?php } else if ( $ultimatemember->fields->editing == true  && $args['show_bio'] ) { ?>

					<div class="um-meta-text">
						<textarea id="um-meta-bio" data-character-limit="<?php echo um_get_option('profile_bio_maxchars'); ?>" placeholder="<?php _e('Tell us a bit about yourself...','ultimate-member'); ?>" name="<?php echo 'description-' . $args['form_id']; ?>" id="<?php echo 'description-' . $args['form_id']; ?>"><?php if ( um_user('description') ) { echo um_user('description'); } ?></textarea>
						<span class="um-meta-bio-character um-right"><span class="um-bio-limit"><?php echo um_get_option('profile_bio_maxchars'); ?></span></span>
						<?php 
							if ( $ultimatemember->fields->is_error('description') ) {
								echo $ultimatemember->fields->field_error( $ultimatemember->fields->show_error('description'), true ); 
							}
						?>

					</div>

					<?php } ?>

					<div class="um-profile-status <?php echo um_user('account_status'); ?>">
						<span><?php printf(__('This user account status is %s','ultimate-member'), um_user('account_status_name') ); ?></span>
					</div>

					<?php do_action('um_after_header_meta', um_user('ID'), $args ); ?>

				</div><div class="um-clear"></div>
   
		        <?php
		        if ( $ultimatemember->fields->is_error( 'profile_photo' ) ) {
		            echo $ultimatemember->fields->field_error( $ultimatemember->fields->show_error('profile_photo'), 'force_show' );
		        }
		        ?>

				<?php do_action('um_after_header_info', um_user('ID'), $args); ?>

			</div>

		<?php
	}

	/***
	***	@adds profile permissions to view/edit
	***/
	add_action('um_pre_profile_shortcode', 'um_pre_profile_shortcode');
	function um_pre_profile_shortcode($args){
		global $ultimatemember;
		extract( $args );

		if ( $mode == 'profile' && $ultimatemember->fields->editing == false ) {
			$ultimatemember->fields->viewing = 1;

			if ( um_get_requested_user() ) {
				if ( !um_can_view_profile( um_get_requested_user() ) && ! um_is_myprofile() ) um_redirect_home();
				if ( !um_current_user_can('edit', um_get_requested_user() ) ) $ultimatemember->user->cannot_edit = 1;
				um_fetch_user( um_get_requested_user() );
			} else {
				if ( !is_user_logged_in() ) um_redirect_home();
				if ( !um_user('can_edit_profile') ) $ultimatemember->user->cannot_edit = 1;
			}

		}

		if ( $mode == 'profile' && $ultimatemember->fields->editing == true ) {
			$ultimatemember->fields->editing = 1;

			if ( um_get_requested_user() ) {
				if ( !um_current_user_can('edit', um_get_requested_user() ) ) um_redirect_home();
				um_fetch_user( um_get_requested_user() );
			}

		}

	}

	/***
	***	@display the edit profile icon
	***/
	add_action('um_pre_header_editprofile', 'um_add_edit_icon' );
	function um_add_edit_icon( $args ) {
		global $ultimatemember;
		$output = '';

		if ( !is_user_logged_in() ) return; // not allowed for guests

		if ( isset( $ultimatemember->user->cannot_edit ) && $ultimatemember->user->cannot_edit == 1 ) return; // do not proceed if user cannot edit

		if ( $ultimatemember->fields->editing == true ) {

		?>

		<div class="um-profile-edit um-profile-headericon">

			<a href="#" class="um-profile-edit-a um-profile-save"><i class="um-faicon-check"></i></a>

		</div>

		<?php } else { ?>

		<div class="um-profile-edit um-profile-headericon">

			<a href="#" class="um-profile-edit-a"><i class="um-faicon-cog"></i></a>

			<?php

			$items = array(
				'editprofile' => '<a href="'.um_edit_profile_url().'" class="real_url">'.__('Edit Profile','ultimate-member').'</a>',
				'myaccount' => '<a href="'.um_get_core_page('account').'" class="real_url">'.__('My Account','ultimate-member').'</a>',
				'logout' => '<a href="'.um_get_core_page('logout').'" class="real_url">'.__('Logout','ultimate-member').'</a>',
				'cancel' => '<a href="#" class="um-dropdown-hide">'.__('Cancel','ultimate-member').'</a>',
			);

			$cancel = $items['cancel'];

			if ( !um_is_myprofile() ) {

				$actions = $ultimatemember->user->get_admin_actions();

				unset( $items['myaccount'] );
				unset( $items['logout'] );
				unset( $items['cancel'] );

				if ( is_array( $actions ) ) {
				$items = array_merge( $items, $actions );
				}

				$items = apply_filters('um_profile_edit_menu_items', $items, um_profile_id() );

				$items['cancel'] = $cancel;

			} else {

				$items = apply_filters('um_myprofile_edit_menu_items', $items );

			}

			echo $ultimatemember->menu->new_ui( $args['header_menu'], 'div.um-profile-edit', 'click', $items );

			?>

		</div>

		<?php
		}

	}

	/***
	***	@Show Fields
	***/
	add_action('um_main_profile_fields', 'um_add_profile_fields', 100);
	function um_add_profile_fields($args){
		global $ultimatemember;

		if ( $ultimatemember->fields->editing == true ) {

			echo $ultimatemember->fields->display( 'profile', $args );

		} else {

			$ultimatemember->fields->viewing = true;

			echo $ultimatemember->fields->display_view( 'profile', $args );

		}

	}

	/***
	***	@form processing
	***/
	add_action('um_submit_form_profile', 'um_submit_form_profile', 10);
	function um_submit_form_profile($args){
		global $ultimatemember;

		if ( !isset($ultimatemember->form->errors) ) do_action('um_user_edit_profile', $args);

		do_action('um_user_profile_extra_hook', $args );

	}

	/***
	***	@Show the submit button (highest priority)
	***/
	add_action('um_after_profile_fields', 'um_add_submit_button_to_profile', 1000);
	function um_add_submit_button_to_profile($args){
		global $ultimatemember;

		// DO NOT add when reviewing user's details
		if ( $ultimatemember->user->preview == true && is_admin() ) return;

		// only when editing
		if ( $ultimatemember->fields->editing == false ) return;

		?>

		<div class="um-col-alt">

			<?php if ( isset($args['secondary_btn']) && $args['secondary_btn'] != 0 ) { ?>

			<div class="um-left um-half"><input type="submit" value="<?php echo $args['primary_btn_word']; ?>" class="um-button" /></div>
			<div class="um-right um-half"><a href="<?php echo um_edit_my_profile_cancel_uri(); ?>" class="um-button um-alt"><?php echo $args['secondary_btn_word']; ?></a></div>

			<?php } else { ?>

			<div class="um-center"><input type="submit" value="<?php echo $args['primary_btn_word']; ?>" class="um-button" /></div>

			<?php } ?>

			<div class="um-clear"></div>

		</div>

		<?php
	}

	/***
	***	@display the available profile tabs
	***/
	add_action('um_profile_navbar', 'um_profile_navbar', 9 );
	function um_profile_navbar( $args ) {
		global $ultimatemember;

		if ( !um_get_option('profile_menu') )
			return;

		// get active tabs
		$tabs = $ultimatemember->profile->tabs_active();

		$tabs = apply_filters('um_user_profile_tabs', $tabs );

		$ultimatemember->user->tabs = $tabs;

		// need enough tabs to continue
		if ( count( $tabs ) <= 1 ) return;

		$active_tab = $ultimatemember->profile->active_tab();

		if ( !isset( $tabs[$active_tab] ) ) {
			$active_tab = 'main';
			$ultimatemember->profile->active_tab = $active_tab;
			$ultimatemember->profile->active_subnav = null;
		}

		// Move default tab priority
		$default_tab = um_get_option('profile_menu_default_tab');
		$dtab = ( isset( $tabs[$default_tab] ) )? $tabs[$default_tab] : 'main';
		if ( isset( $tabs[ $default_tab ] ) ) {
			unset( $tabs[$default_tab] );
			$dtabs[$default_tab] = $dtab;
			$tabs = $dtabs + $tabs;
		}

		?>

		<div class="um-profile-nav">

			<?php foreach( $tabs as $id => $tab ) {

				if ( isset( $tab['hidden'] ) ) continue;

				$nav_link = $ultimatemember->permalinks->get_current_url( get_option('permalink_structure') );
				$nav_link = remove_query_arg( 'um_action', $nav_link );
				$nav_link = remove_query_arg( 'subnav', $nav_link );
				$nav_link =  add_query_arg('profiletab', $id, $nav_link );

				$nav_link = apply_filters("um_profile_menu_link_{$id}", $nav_link);

				?>

			<div class="um-profile-nav-item um-profile-nav-<?php echo $id; ?> <?php if ( !um_get_option('profile_menu_icons') ) { echo 'without-icon'; } ?> <?php if ( $id == $active_tab ) { echo 'active'; } ?>">
				<a href="<?php echo $nav_link; ?>" title="<?php echo $tab['name']; ?>">

					<i class="<?php echo $tab['icon']; ?>"></i>

					<?php if ( isset( $tab['notifier'] ) && $tab['notifier'] > 0 ) { ?>
					<span class="um-tab-notifier uimob500-show uimob340-show uimob800-show"><?php echo $tab['notifier']; ?></span>
					<?php } ?>

					<span class="uimob500-hide uimob340-hide uimob800-hide title"><?php echo $tab['name']; ?></span>

				</a>
			</div>

			<?php } ?>

			<div class="um-clear"></div>

		</div>

		<?php foreach( $tabs as $id => $tab ) {

				if ( isset( $tab['subnav'] ) && $active_tab == $id ) {

					$active_subnav = ( $ultimatemember->profile->active_subnav() ) ? $ultimatemember->profile->active_subnav() : $tab['subnav_default'];

					echo '<div class="um-profile-subnav">';
					foreach( $tab['subnav'] as $id => $subtab ) {

					?>

						<a href="<?php echo add_query_arg('subnav', $id ); ?>" class="<?php if ( $active_subnav == $id ) echo 'active'; ?>"><?php echo $subtab; ?></a>

						<?php

					}
					echo '</div>';
				}

			}

	}

	/**
	 * Clean up file for new uploaded files
	 * @param  integer $user_id   
	 * @param  array $arr_files 
	 */
	add_action("um_before_user_upload","um_before_user_upload", 10 ,2 );
	function um_before_user_upload( $user_id, $arr_files ){
		global $ultimatemember;

		um_fetch_user( $user_id );
		
		foreach ($arr_files as $key => $filename ) {
			if( um_user( $key ) ){
				if( basename( $filename ) != basename( um_user( $key ) ) || in_array( $old_filename , array( basename( um_user( $key ) ), basename( $filename ) ) ) || $filename == 'empty_file' ){
					$old_filename = um_user( $key );
					$path = $ultimatemember->files->upload_basedir;
					delete_user_meta( $user_id, $old_filename );
					if ( file_exists( $path . $user_id . '/' . $old_filename ) ) {
						unlink( $path . $user_id . '/' . $old_filename );
					}
				}
			}
		}
	}

