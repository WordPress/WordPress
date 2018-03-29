<?php

class UM_ADDON_bp_avatar_transfer {

	function __construct() {
		
		add_action('admin_menu', array(&$this, 'admin_menu'), 1001);
		
		add_action('admin_init', array(&$this, 'admin_init'), 1);
		
		add_action('um_admin_addon_hook', array(&$this, 'um_admin_addon_hook') );

	}

	function admin_menu() {
		
		global $ultimatemember;
		$this->addon = $ultimatemember->addons['bp_avatar_transfer'];
		add_submenu_page('ultimatemember', $this->addon[0], $this->addon[0], 'manage_options', 'bp_avatar_transfer', array(&$this, 'content') );
		
	}

	function um_admin_addon_hook( $hook ) {
		global $ultimatemember;
		switch( $hook ) {
			case 'bp_avatar_transfer':
				if ( class_exists('BuddyPress') ) {
					
					$path = bp_core_avatar_upload_path() . '/avatars';

					$files = glob( $path . '/*');
					$i = 0;
					foreach( $files as $key ) {
						$q      =       (count(glob("$key/*")) === 0) ? 0 : 1;
						if ( $q == 1 ) {
							$photo = glob( $key . '/*');
							foreach( $photo as $file ) {
								if ( strstr( $file, 'bpfull' ) ) {
									$get_user_id = explode('/', $file);
									array_pop($get_user_id);
									$user_id = end($get_user_id);
									if ( !file_exists( $ultimatemember->files->upload_basedir . $user_id . '/profile_photo.jpg' ) ) {
										$ultimatemember->files->new_user( $user_id );
										copy( $file, $ultimatemember->files->upload_basedir . $user_id . '/profile_photo.jpg' );
										update_user_meta($user_id, 'profile_photo', 'profile_photo.jpg');
										$i++;
									}
								}
							}
						}
					}
					
					$this->content = '<p><strong>Done. Process completed!</p>';
					$this->content .= $i . ' user(s) changed.</strong></p>';

				}
				break;
		}
	}

	function admin_init() {
		if ( isset( $_REQUEST['um-addon-hook'] ) ) {
			$hook = $_REQUEST['um-addon-hook'];
			do_action("um_admin_addon_hook", $hook);
		}
	}
	
	function content() {
		
		$this->process_link = add_query_arg('um-addon-hook','bp_avatar_transfer');
		
		?>
		
		<div class="wrap">
		
			<h2>Ultimate Member <sup style="font-size:15px"><?php echo ultimatemember_version; ?></sup></h2>
			
			<h3><?php echo $this->addon[0]; ?></h3>
			
			<?php if ( isset( $this->content ) ) { 
				echo $this->content;
			} else { ?>
			
			<p>This tool allows you to move all custom user photos/avatars from BuddyPress to Ultimate Member platform. This can help you If you are switching from BuddyPress.</p>
			<p>Depending on your users database, this could take a few moments. To start the process, click the following button.</p>
			
			<p><a href="<?php echo $this->process_link; ?>" class="button button-primary">Start transferring avatars</a></p>

			<?php } ?>
			
		</div><div class="clear"></div>
		
		<?php
		
	}

}

$UM_ADDON_bp_avatar_transfer = new UM_ADDON_bp_avatar_transfer();