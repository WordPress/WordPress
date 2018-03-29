<?php

class UM_ADDON_generate_random_users {

	function __construct() {
		
		add_action('admin_menu', array(&$this, 'admin_menu'), 1001);
		
		add_action('admin_init', array(&$this, 'admin_init'), 1);
		
		add_action('um_admin_addon_hook', array(&$this, 'um_admin_addon_hook') );

	}


   function admin_menu() {
		
		global $ultimatemember;
		$this->addon = $ultimatemember->addons['generate_random_users'];
		add_submenu_page('ultimatemember', $this->addon[0], $this->addon[0], 'manage_options', 'generate_random_users', array(&$this, 'content') );
		
	}

	function um_admin_addon_hook( $hook ) {
		global $ultimatemember;

		switch ( $hook ) {
			case 'generate_random_users':
				$json_url = "https://randomuser.me/api/";

					$arr_post_header = array( );

					if( isset(  $_GET['gender'] ) ){
						$gender =  $_GET['gender'];
						if( $gender != 'both' ){
							$json_url = add_query_arg('gender', $gender, $json_url );
							$arr_post_header['gender'] = $gender;
						}
					}

					if( isset(  $_GET['nationality'] ) ){
						$nationality = implode(",", $_GET['nationality']);
						if( ! empty( $nationality ) ){
							$json_url = add_query_arg('nat', $nationality, $json_url );
						}
						$arr_post_header['nat'] = $nationality;
					}

					if( isset(  $_GET['total_users'] ) ){
						$total_users = intval(  $_GET['total_users'] );
						$json_url = add_query_arg('results', $total_users, $json_url );
						$arr_post_header['results'] = $total_users;
					}


				$response = wp_remote_get(  $json_url, array('timeout' =>  120 ) );
				$json = '';
				
				if( is_array($response) ) {
				  	if( isset( $response['body'] ) && ! empty( $response['body'] ) ){
					  $json = json_decode(  $response['body'] );
					}
				}

				if( is_wp_error( $response ) ){
					wp_die( $response->get_error_message() );
				}

				if( ! empty( $json )  ){
					
					remove_action('um_after_new_user_register', 'um_after_new_user_register', 10, 2);

					$failed_dummies = 0;
					$default_role = um_get_option("default_role");

					foreach( $json->results as $dummy ){
							
							if( isset( $_GET['password'] ) && ! empty( $_GET['password'] ) ){
								$password = $_GET['password'];	
							}else{
								$password = wp_generate_password( 8, false );
							}
							
							$site_url = @$_SERVER['SERVER_NAME'];
							$random_generate = wp_generate_password( 4, false );

							if( username_exists( $dummy->login->username ) ){
								$dummy->login->username = $dummy->login->username.'_'. $random_generate;
							}

							$dummy->login->email = $dummy->login->username."_dummy@{$site_url}";

							if( email_exists( $dummy->login->email ) ){
								$dummy->login->email = $dummy->login->username."_dummy_{$random_generate}@{$site_url}";
							}

							$userdata = array(
								'display_name' 	=> ucfirst( $dummy->name->first )." ".ucfirst( $dummy->name->last ),
								'first_name' 	=> ucfirst( $dummy->name->first ),
							    'last_name' 	=> ucfirst( $dummy->name->last ),
							    'user_email' 	=> $dummy->login->email,
							    'user_login'  	=> $dummy->login->username,
							    'user_pass'   	=> $password,
							);

							$user_id = wp_insert_user( $userdata );
							
							if( is_wp_error( $user_id ) ){
								$failed_dummies++;
							}

							$usermeta = array(
								'synced_profile_photo' 		=> $dummy->picture->large,
								'gender' 					=> ucfirst($dummy->gender),
								'birth_date' 				=> date("Y/m/d", strtotime( $dummy->dob) ),
								'_um_last_login'			=> date("Y/m/d", strtotime( $dummy->registered ) ),
								'mobile_number'				=> $dummy->cell,
								'phone_number'				=> $dummy->phone,
								'synced_gravatar_hashed_id' => md5( strtolower( trim( $dummy->email ) ) ),
								'account_status'			=> 'approved',
								'_um_profile_dummy'			=> true,
								'role'						=> isset( $default_role ) ? $default_role: 'member'
							);

							if( isset( $_GET['add_cover_photo'] ) && $_GET['add_cover_photo'] == 1 ){

								$rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
    							$color = $rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];
    
    							$usermeta['synced_cover_photo'] = 'http://placehold.it/650x350/'.$color.'/'.$color;
								
							}

							foreach ( $usermeta as $key => $value ) {
								update_user_meta( $user_id, $key, $value );
							}

							
					}
					wp_redirect( admin_url("admin.php?page=generate_random_users") ); exit;
				}

			break;

			case 'remove_random_users':
				
				$dummies = new WP_User_Query(
					array(
							'fields' => array('ID'),
							'meta_key' => '_um_profile_dummy',
							'meta_value' => true,
							'meta_compare' => '='
					)
				);

				if( $dummies->total_users > 0 ){
					foreach ( $dummies->get_results() as $dummy ) {
						
							if( isset( $dummy->ID ) ){
								wp_delete_user( $dummy->ID );
							}

					}
				}

				delete_option('um_generated_dumies');
				wp_redirect( admin_url("admin.php?page=generate_random_users") );
				exit;

			break;
			
			default:
				
				break;
		}
		
	}

	function admin_init() {
		if ( isset( $_REQUEST['um-addon-hook'] ) ) {
			$hook = $_REQUEST['um-addon-hook'];
			do_action("um_admin_addon_hook", $hook );
		}
	}
	
	function content() {
		
		?>
		
		<div class="wrap">
		
			<h2>Ultimate Member <sup style="font-size:15px"><?php echo ultimatemember_version; ?></sup></h2>
			
			<h3><?php echo $this->addon[0]; ?></h3>
			
			<?php if ( isset( $this->content ) ) { 
				echo $this->content;
			} else { ?>
			
			<p>This tool allows you to add dummies as Ultimate Member users. </p>
		<?php 
				$dummies = new WP_User_Query(
					array(
							'fields' => array('ID'),
							'meta_key' => '_um_profile_dummy',
							'meta_value' => true,
							'meta_compare' => '='
					)
				);
		?>
		<?php if( $dummies->total_users > 0 ): ?>
		<form method="get">
				<p>
				&nbsp;<input type="submit" class="button button-secondary" value="Remove Generated Dummies (<?php echo $dummies->total_users;?>)"/>
					<input type="hidden" name="um-addon-hook" value="remove_random_users"/>
					<input type="hidden" name="page" value="generate_random_users"/>
		</form>
		<br/>
		<?php endif; ?>				
		<form method="get">
			<label for="total_users">How many dummies? <br/><input type="text" name="total_users" value="30" /> <br/><br/>
				<label for="gender">Gender:</label>	<br/>
				<label><input type='radio' name="gender" value="male"/> Male</label>	<br/>	
				<label><input type='radio' name="gender" value="female"/> Female</label>	<br/>	
				<label><input type='radio' checked="checked" name="gender" value="both"/> Both</label>		
				<br/><br/>
				<label for="nationality">Available Nationalities:</label><br/>
				<select style="width:150px" name="nationality[]" multiple>
				<?php 
				$nationality = array( 'AU', 'BR', 'CA', 'CH', 'DE', 'DK', 'ES', 'FI', 'FR', 'GB', 'IE', 'IR', 'NL', 'NZ', 'TR', 'US' );
				foreach ($nationality as $code ) {
					$value = strtolower($code);
					echo "<option value='".$value."'/> ".$code."</option>";
				}
				?>
				</select>
				<br/><br/>
				<label for="add_cover_photo"><input type="checkbox" name="add_cover_photo" value="1" />
				 Add cover photos?</label>
				 <br/><small>Generates random colored cover photos</small>
				<br/><br/>
				 <label for="password">
				 Account Passwords: 
				 <input type="password" name="password"  />
				<br/><small>if you leave this blank, it will generate random strings password</small>
				</label>
				<br/>
				<input type="submit" class="button button-primary" value="Start Generating Dummies"/>
				<input type="hidden" name="um-addon-hook" value="generate_random_users"/>
				
			<?php } ?>
		</form>

		</div><div class="clear"></div>
		
		<?php
		
	}
}

$UM_ADDON_generate_random_users  = new UM_ADDON_generate_random_users ();