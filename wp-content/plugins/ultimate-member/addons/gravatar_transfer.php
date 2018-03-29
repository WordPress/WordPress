<?php

class UM_ADDON_gravatar_transfer {

	function __construct() {
		
		add_action('admin_menu', array(&$this, 'admin_menu'), 1001);
		
		add_action('admin_init', array(&$this, 'admin_init'), 1);
		
		add_action('um_admin_addon_hook', array(&$this, 'um_admin_addon_hook') );

	}

   function gravatar_hash(){
   		global $wpdb;
   		$wpdb->query('DELETE FROM '.$wpdb->usermeta.' WHERE meta_key = "synced_gravatar_hashed_id" ');

   		$wpdb->query('INSERT INTO '.$wpdb->usermeta.'(user_id, meta_key, meta_value) 
   		SELECT ID, "synced_gravatar_hashed_id", MD5( LOWER( TRIM(user_email) ) ) FROM '.$wpdb->users.' '); 

   		return true;
   }

   function admin_menu() {
		
		global $ultimatemember;
		$this->addon = $ultimatemember->addons['gravatar_transfer'];
		add_submenu_page('ultimatemember', $this->addon[0], $this->addon[0], 'manage_options', 'gravatar_transfer', array(&$this, 'content') );
		
	}

	function um_admin_addon_hook( $hook ) {
		global $ultimatemember;
		switch( $hook ) {
			case 'gravatar_transfer':
					if( $this->gravatar_hash() ){
					  	$this->content = '<p><strong>Done. Process completed!</p>';
					  	$result = count_users();
						$this->content .= $result['total_users'] . ' user(s) changed.</strong></p>';
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
		
		$this->process_link = add_query_arg('um-addon-hook','gravatar_transfer');
		
		?>
		
		<div class="wrap">
		
			<h2>Ultimate Member <sup style="font-size:15px"><?php echo ultimatemember_version; ?></sup></h2>
			
			<h3><?php echo $this->addon[0]; ?></h3>
			
			<?php if ( isset( $this->content ) ) { 
				echo $this->content;
			} else { ?>
			
			<p>This tool allows you to add gravatars to Ultimate Member users. This can help you to link gravatar photos to user accounts with their email address.</p>
			<p>Depending on your users database, this could take a few moments. To start the process, click the following button.</p>
			
			<p><a href="<?php echo $this->process_link; ?>" class="button button-primary">Start adding gravatars</a></p>

			<?php } ?>
			
		</div><div class="clear"></div>
		
		<?php
		
	}
}

$UM_ADDON_gravatar_transfer = new UM_ADDON_gravatar_transfer();