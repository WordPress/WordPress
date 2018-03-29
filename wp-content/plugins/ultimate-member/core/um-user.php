<?php

class UM_User {

	function __construct() {

		$this->id = 0;
		$this->usermeta = null;
		$this->data = null;
		$this->profile = null;

		$this->banned_keys = array(
			'metabox','postbox','meta-box',
			'dismissed_wp_pointers', 'session_tokens',
			'screen_layout', 'wp_user-', 'dismissed',
			'cap_key', 'wp_capabilities',
			'managenav', 'nav_menu','user_activation_key',
			'level_', 'wp_user_level'
		);

		add_action('init',  array(&$this, 'set'), 1);

		$this->preview = false;

		// a list of keys that should never be in wp_usermeta
		$this->update_user_keys = array(
			'user_email',
			'user_pass',
			'user_password',
			'display_name',
		);

		$this->target_id = null;

		// When the cache should be cleared
		add_action('um_delete_user_hook', array(&$this, 'remove_cached_queue') );
		add_action('um_new_user_registration_plain', array(&$this, 'remove_cached_queue') );
		add_action('um_after_user_status_is_changed_hook', array(&$this, 'remove_cached_queue') );

		// When user cache should be cleared
		add_action('um_after_user_updated', array(&$this, 'remove_cache') );
		add_action('um_after_user_account_updated', array(&$this, 'remove_cache') );
		add_action('personal_options_update', array(&$this, 'remove_cache') );
		add_action('edit_user_profile_update', array(&$this, 'remove_cache') );
		add_action('um_when_role_is_set', array(&$this, 'remove_cache') );
		add_action('um_when_status_is_set', array(&$this, 'remove_cache') );

		add_action( 'show_user_profile',        array( $this, 'community_role_edit' ) );
		add_action( 'edit_user_profile',        array( $this, 'community_role_edit' ) );
		add_action( 'personal_options_update',  array( $this, 'community_role_save' ) );
		add_action( 'edit_user_profile_update', array( $this, 'community_role_save' ) );

	}

	/**
	 * Allow changing community role
	 */
	function community_role_edit( $user ) {
		global $ultimatemember;
		if ( current_user_can( 'edit_users' ) && current_user_can( 'edit_user', $user->ID ) ) {
			
			$um_user_role = get_user_meta($user->ID,'role',true);
			?>
			<h2><?php _e('Ultimate Member','ultimate-member') ?></h2>
			<table class="form-table">
				<tbody>
					<tr>
						<th>
							<label for="um_role"><?php _e( 'Community Role', 'ultimate-member'); ?></label>
						</th>
						<td>
							<select name="um_role" id="um_role">
							<?php foreach( $ultimatemember->query->get_roles() as $key => $value ) { ?>
							<option value="<?php echo $key; ?>" <?php selected( $um_user_role, $key ); ?> ><?php echo $value; ?></option>
							<?php } ?>
							</select>
							<span class="description"><?php _e( 'Assign or change the community role for this user', 'ultimate-member'); ?></span>
						</td>
					</tr>
				</tbody>
			</table>
		<?php }

		do_action( 'um_user_profile_section' );
	}

	/**
	 * Save community role
	 */
	public function community_role_save( $user_id ) {
		if ( current_user_can( 'edit_user', $user_id ) && isset( $_POST['um_role'] ) ) {
			update_user_meta( $user_id, 'role', sanitize_title_with_dashes( $_POST['um_role'] ) );
			delete_option( "um_cache_userdata_{$user_id}" );
		}
	}

	/***
	***	@Remove cached queue from Users backend
	***/
	function remove_cached_queue() {
		delete_option('um_cached_users_queue');
	}

	/***
	***	@Converts object to array
	***/
	function toArray($obj)
	{
		if (is_object($obj)) $obj = (array)$obj;
		if (is_array($obj)) {
			$new = array();
			foreach ($obj as $key => $val) {
				$new[$key] = $this->toArray($val);
			}
		} else {
			$new = $obj;
		}

		return $new;
	}

	function get_cached_data( $user_id ) {

		$disallow_cache = um_get_option('um_profile_object_cache_stop');
		if( $disallow_cache ){
			return '';
		}

		if ( is_numeric( $user_id ) && $user_id > 0 ) {
			$find_user = get_option("um_cache_userdata_{$user_id}");
			if ( $find_user ) {
				$find_user = apply_filters('um_user_permissions_filter', $find_user, $user_id);
				return $find_user;
			}
		}
		return '';
	}

	function setup_cache( $user_id, $profile ) {
		
		$disallow_cache = um_get_option('um_profile_object_cache_stop');
		if( $disallow_cache ){
			return '';
		}

		update_option( "um_cache_userdata_{$user_id}", $profile );
	}

	function remove_cache( $user_id ) {
		delete_option( "um_cache_userdata_{$user_id}" );
	}

	/**
	 * @function set()
	 *
	 * @description This method lets you set a user. For example, to retrieve a profile or anything related to that user.
	 *
	 * @usage <?php $ultimatemember->user->set( $user_id, $clean = false ); ?>
	 *
	 * @param $user_id (numeric) (optional) Which user to retrieve. A numeric user ID
	 * @param $clean (boolean) (optional) Should be true or false. Basically, if you did not provide a user ID It will set the current logged in user as a profile
	 *
	 * @returns This API method does not return anything. It sets user profile and permissions and allow you to retrieve any details for that user.
	 *
	 * @example The following example makes you set a user and retrieve their display name after that using the user API.

		<?php

			$ultimatemember->user->set( 12 );
			$display_name = $ultimatemember->user->profile['display_name']; // Should print user display name

		?>

	 *
	 *
	 */
	function set( $user_id = null, $clean = false ) {
		global $ultimatemember;

		if ( isset( $this->profile ) ) {
			unset( $this->profile );
		}

		if ($user_id) {
			$this->id = $user_id;
		} elseif (is_user_logged_in() && $clean == false ){
			$this->id = get_current_user_id();
		} else {
			$this->id = 0;
		}

		if ( $this->get_cached_data( $this->id ) ) {
			$this->profile = $this->get_cached_data( $this->id );
		} else {

		if ($user_id) {

			$this->id = $user_id;
			$this->usermeta = get_user_meta($user_id);
			$this->data = get_userdata($this->id);

		} elseif (is_user_logged_in() && $clean == false ){

			$this->id = get_current_user_id();
			$this->usermeta = get_user_meta($this->id);
			$this->data = get_userdata($this->id);

		} else {

			$this->id = 0;
			$this->usermeta = null;
			$this->data = null;

		}

		// we have a user, populate a profile
		if ( $this->id && $this->toArray($this->data) ) {

			// add user data
			$this->data = $this->toArray($this->data);

			foreach( $this->data as $k=>$v ) {
				if ($k == 'roles') {
					$this->profile['wp_roles'] = implode(',',$v);
				} else if (is_array($v)){
					foreach($v as $k2 => $v2){
						$this->profile[$k2] = $v2;
					}
				} else {
					$this->profile[$k] = $v;
				}
			}

			// add account status
			if ( !isset( $this->usermeta['account_status'][0] ) )  {
				$this->usermeta['account_status'][0] = 'approved';
			}

			if ( $this->usermeta['account_status'][0] == 'approved' ) {
				$this->usermeta['account_status_name'][0] = __('Approved','ultimate-member');
			}

			if ( $this->usermeta['account_status'][0] == 'awaiting_email_confirmation' ) {
				$this->usermeta['account_status_name'][0] = __('Awaiting E-mail Confirmation','ultimate-member');
			}

			if ( $this->usermeta['account_status'][0] == 'awaiting_admin_review' ) {
				$this->usermeta['account_status_name'][0] = __('Pending Review','ultimate-member');
			}

			if ( $this->usermeta['account_status'][0] == 'rejected' ) {
				$this->usermeta['account_status_name'][0] = __('Membership Rejected','ultimate-member');
			}

			if ( $this->usermeta['account_status'][0] == 'inactive' ) {
				$this->usermeta['account_status_name'][0] = __('Membership Inactive','ultimate-member');
			}

			// add user meta
			foreach($this->usermeta as $k=>$v){
				if ( $k == 'display_name') continue;
				$this->profile[$k] = $v[0];
			}

			// add permissions
			$user_role = $this->get_role();
			$this->role_meta = $ultimatemember->query->role_data( $user_role );
			$this->role_meta = apply_filters('um_user_permissions_filter', $this->role_meta, $this->id);

			$this->profile = array_merge( $this->profile, (array)$this->role_meta);

			$this->profile['super_admin'] = ( is_super_admin( $this->id ) ) ? 1 : 0;

			// clean profile
			$this->clean();

			// Setup cache
			$this->setup_cache( $this->id, $this->profile );

		}

		}

	}

	/***
	***	@reset user data
	***/
	function reset( $clean = false ){
		$this->set(0, $clean);
	}

	/***
	***	@Clean user profile
	***/
	function clean(){
		foreach($this->profile as $key => $value){
			foreach($this->banned_keys as $ban){
				if (strstr($key, $ban) || is_numeric($key) )
					unset($this->profile[$key]);
			}
		}
	}

	/**
	 * @function auto_login()
	 *
	 * @description This method lets you auto sign-in a user to your site.
	 *
	 * @usage <?php $ultimatemember->user->auto_login( $user_id, $rememberme = false ); ?>
	 *
	 * @param $user_id (numeric) (required) Which user ID to sign in automatically
	 * @param $rememberme (boolean) (optional) Should be true or false. If you want the user sign in session to use cookies, use true
	 *
	 * @returns Sign in the specified user automatically.
	 *
	 * @example The following example lets you sign in a user automatically by their ID.

		<?php $ultimatemember->user->auto_login( 2 ); ?>

	 *
	 *
	 * @example The following example lets you sign in a user automatically by their ID and makes the plugin remember their session.

		<?php $ultimatemember->user->auto_login( 10, true ); ?>

	 *
	 *
	 */
	function auto_login( $user_id, $rememberme = 0 ) {
		
		wp_set_current_user( $user_id );
		
		wp_set_auth_cookie( $user_id, $rememberme );
		
		$user = get_user_by('ID', $user_id );
		
		do_action( 'wp_login', $user->user_login, $user );

	}

	/***
	***	@Set user's registration details
	***/
	function set_registration_details( $submitted ) {

		if ( isset( $submitted['user_pass'] ) ) {
			unset( $submitted['user_pass'] );
		}

		if ( isset( $submitted['user_password'] ) ) {
			unset( $submitted['user_password'] );
		}

		if ( isset( $submitted['confirm_user_password'] ) ) {
			unset( $submitted['confirm_user_password'] );
		}

		$submitted = apply_filters('um_before_save_filter_submitted', $submitted );

		do_action('um_before_save_registration_details', $this->id, $submitted );

		update_user_meta( $this->id, 'submitted', $submitted );

		do_action('um_after_save_registration_details', $this->id, $submitted );

	}

	/***
	***	@A plain version of password
	***/
	function set_plain_password( $plain ) {
		update_user_meta( $this->id, '_um_cool_but_hard_to_guess_plain_pw', $plain );
	}

	/**
	 * Set last login for new registered users
	 */
	function set_last_login(){
		update_user_meta(  $this->id, '_um_last_login', current_time( 'timestamp' ) );
	}

	function set_role( $role ){

		do_action('um_when_role_is_set', um_user('ID') );

		do_action('um_before_user_role_is_changed');

		$this->profile['role'] = $role;
		
		do_action('um_member_role_upgrade', $role, $this->profile['role'] );

		$this->update_usermeta_info('role');

		do_action('um_after_user_role_is_changed');

		do_action('um_after_user_role_is_updated', um_user('ID'), $role );

	}

	/***
	***	@Set user's account status
	***/
	function set_status( $status ){

		do_action( 'um_when_status_is_set', um_user('ID') );

		$this->profile['account_status'] = $status;

		$this->update_usermeta_info('account_status');

		do_action( 'um_after_user_status_is_changed_hook' );

		do_action( 'um_after_user_status_is_changed', $status);

	}

	/***
	***	@Set user's hash for password reset
	***/
	function password_reset_hash(){
		global $ultimatemember;

		$this->profile['reset_pass_hash'] = $ultimatemember->validation->generate();
		$this->update_usermeta_info('reset_pass_hash');

	}

	/***
	***	@Set user's hash
	***/
	function assign_secretkey(){
		global $ultimatemember;

		do_action('um_before_user_hash_is_changed');

		$this->profile['account_secret_hash'] = $ultimatemember->validation->generate();
		$this->update_usermeta_info('account_secret_hash');

		do_action('um_after_user_hash_is_changed');

	}

	/***
	***	@password reset email
	***/
	function password_reset(){
		global $ultimatemember;
		$this->password_reset_hash();
		$ultimatemember->mail->send( um_user('user_email'), 'resetpw_email' );
	}


	/***
	***	@password changed email
	***/
	function password_changed(){
		global $ultimatemember;
		$ultimatemember->mail->send( um_user('user_email'), 'changedpw_email' );
	}

	/**
	 * @function approve()
	 *
	 * @description This method approves a user membership and sends them an optional welcome/approval e-mail.
	 *
	 * @usage <?php $ultimatemember->user->approve(); ?>
	 *
	 * @returns Approves a user membership.
	 *
	 * @example Approve a pending user and allow him to sign-in to your site.

		<?php

			um_fetch_user( 352 );
			$ultimatemember->user->approve();

		?>

	 *
	 *
	 */
	function approve(){
		global $ultimatemember;

		$user_id = um_user('ID');
		delete_option( "um_cache_userdata_{$user_id}" );

		if ( um_user('account_status') == 'awaiting_admin_review' ) {
			$this->password_reset_hash();
			$ultimatemember->mail->send( um_user('user_email'), 'approved_email' );

		} else {
			$this->password_reset_hash();
			$ultimatemember->mail->send( um_user('user_email'), 'welcome_email');
		}

		$this->set_status('approved');
		$this->delete_meta('account_secret_hash');
		$this->delete_meta('_um_cool_but_hard_to_guess_plain_pw');

		do_action('um_after_user_is_approved', um_user('ID') );

	}

	/***
	***	@pending email
	***/
	function email_pending() {
		global $ultimatemember;
		$this->assign_secretkey();
		$this->set_status('awaiting_email_confirmation');
		$ultimatemember->mail->send( um_user('user_email'), 'checkmail_email' );
	}

	/**
	 * @function pending()
	 *
	 * @description This method puts a user under manual review by administrator and sends them an optional e-mail.
	 *
	 * @usage <?php $ultimatemember->user->pending(); ?>
	 *
	 * @returns Puts a user under review and sends them an email optionally.
	 *
	 * @example An example of putting a user pending manual review

		<?php

			um_fetch_user( 54 );
			$ultimatemember->user->pending();

		?>

	 *
	 *
	 */
	function pending(){
		global $ultimatemember;
		$this->set_status('awaiting_admin_review');
		$ultimatemember->mail->send( um_user('user_email'), 'pending_email' );
	}

	/**
	 * @function reject()
	 *
	 * @description This method rejects a user membership and sends them an optional e-mail.
	 *
	 * @usage <?php $ultimatemember->user->reject(); ?>
	 *
	 * @returns Rejects a user membership.
	 *
	 * @example Reject a user membership example

		<?php

			um_fetch_user( 114 );
			$ultimatemember->user->reject();

		?>

	 *
	 *
	 */
	function reject(){
		global $ultimatemember;
		$this->set_status('rejected');
		$ultimatemember->mail->send( um_user('user_email'), 'rejected_email' );
	}

	/**
	 * @function deactivate()
	 *
	 * @description This method deactivates a user membership and sends them an optional e-mail.
	 *
	 * @usage <?php $ultimatemember->user->deactivate(); ?>
	 *
	 * @returns Deactivates a user membership.
	 *
	 * @example Deactivate a user membership with the following example

		<?php

			um_fetch_user( 32 );
			$ultimatemember->user->deactivate();

		?>

	 *
	 *
	 */
	function deactivate(){
		global $ultimatemember;
		$this->set_status('inactive');

		do_action('um_after_user_is_inactive', um_user('ID') );

		$ultimatemember->mail->send( um_user('user_email'), 'inactive_email' );
	}

	/***
	***	@delete user
	***/
	function delete( $send_mail = true ) {
		global $ultimatemember;

		do_action( 'um_delete_user_hook' );
		do_action( 'um_delete_user', um_user('ID') );

		// send email notifications
		if ( $send_mail ) {
			$ultimatemember->mail->send( um_user('user_email'), 'deletion_email' );
			$ultimatemember->mail->send( um_admin_email(), 'notification_deletion', array('admin' => true ) );
		}

		// remove uploads
		$ultimatemember->files->remove_dir( um_user_uploads_dir() );

		// remove user
		if ( is_multisite() ) {

			if ( !function_exists('wpmu_delete_user') ) {
				require_once( ABSPATH . 'wp-admin/includes/ms.php' );
			}

			wpmu_delete_user( $this->id );

		} else {

			if ( !function_exists('wp_delete_user') ) {
				require_once( ABSPATH . 'wp-admin/includes/user.php' );
			}

			wp_delete_user( $this->id );

		}

	}

	/**
	 * @function get_role()
	 *
	 * @description This method gets a user role in slug format. e.g. member
	 *
	 * @usage <?php $ultimatemember->user->get_role(); ?>
	 *
	 * @returns The user role's slug.
	 *
	 * @example Do something if the user's role is paid-member

		<?php

			um_fetch_user( 12 );

			if ( $ultimatemember->user->get_role() == 'paid-member' ) {
				// Show this to paid customers
			} else {
				// You are a free member
			}

		?>

	 *
	 *
	 */
	function get_role() {
		global $ultimatemember;

		if (isset($this->profile['role']) && !empty( $this->profile['role'] ) ) {
			return $this->profile['role'];
		} else {
			if ( $this->profile['wp_roles'] == 'administrator' ) {
				return 'admin';
			} else {
				return 'member';
			}
		}
	}

	function get_role_name( $slug, $return_role_id = false ) {
		global $wpdb, $ultimatemember;

		if( isset( $ultimatemember->profile->arr_user_roles[ 'is_'.$return_role_id ][ $slug ] ) ){
			return $ultimatemember->profile->arr_user_roles[ 'is_'.$return_role_id ][ $slug ];
		}

		$args = array(
		    	'posts_per_page' => 1,
		    	'post_type' => 'um_role',
		    	'name'	=> $slug,
		    	'post_status' => array('publish'),
		);

		$roles = new WP_Query( $args );
		$role_id = 0;
		$role_title = '';

		if ( $roles->have_posts() ) {
			while ( $roles->have_posts() ) {
				$roles->the_post();
				$role_id = get_the_ID();
				$role_title = get_the_title();
			}
		}

		wp_reset_query();  

		$ultimatemember->profile->arr_user_roles[ 'is_1' ][ $slug ] = $role_id;
		$ultimatemember->profile->arr_user_roles[ 'is_'  ][ $slug ] = $role_title;

		if( $return_role_id ){
			return $role_id;
		}
		
		return $role_title;
	}

	/**
	 * Get role slug by ID
	 * @param  integer $id 
	 * @return string
	 */
	function get_role_slug_by_id( $id ) {
		global $wpdb, $ultimatemember;


		$args = array(
		    	'posts_per_page' => 1,
		    	'post_type' => 'um_role',
		    	'page_id'	=> $id,
		    	'post_status' => array('publish'),
		);

		$roles = new WP_Query( $args );
		$role_slug = '';
		
		if ( $roles->have_posts() ) {
			$role_slug = $roles->post->post_name;
		}

		wp_reset_query();  

		return $role_slug;
	}

	/***
	***	@Update one key in user meta
	***/
	function update_usermeta_info( $key ) {
		// delete the key first just in case
		delete_user_meta( $this->id, $key );
		update_user_meta( $this->id, $key, $this->profile[$key] );
	}

	/**
	 * @function delete_meta()
	 *
	 * @description This method can be used to delete user's meta key.
	 *
	 * @usage <?php $ultimatemember->user->delete_meta( $key ); ?>
	 *
	 * @param $key (string) (required) The meta field key to remove from user
	 *
	 * @returns This method will not return anything. The specified meta key will be deleted from database for the specified user.
	 *
	 * @example Delete user's age field

		<?php

			um_fetch_user( 15 );
			$ultimatemember->user->delete_meta( 'age' );

		?>

	 *
	 *
	 */
	function delete_meta( $key ){
		delete_user_meta( $this->id, $key );
	}

	/***
	***	@Get all bulk actions
	***/
	function get_bulk_admin_actions() {
		$output = '';
		$actions = array();
		$actions = apply_filters('um_admin_bulk_user_actions_hook', $actions );
		foreach($actions as $id => $arr ) {
			if ( isset($arr['disabled'])){
				$arr['disabled'] = 'disabled';
			} else {
				$arr['disabled'] = '';
			}

			$output .= '<option value="' . $id . '" '. $arr['disabled'] . '>' . $arr['label'] . '</option>';
		}
		return $output;
	}

	/***
	***	@Get admin actions for individual user
	***/
	function get_admin_actions() {
		$items = array();
		$actions = array();
		$actions = apply_filters('um_admin_user_actions_hook', $actions );
		if ( !isset( $actions ) || empty( $actions ) ) return false;
		foreach($actions as $id => $arr ) {
			$url = add_query_arg('um_action', $id );
			$url = add_query_arg('uid', um_profile_id(), $url );
			$items[] = '<a href="' . $url .'" class="real_url '.$id.'-item">' . $arr['label'] . '</a>';
		}
		return $items;
	}

	/**
	 * @function is_private_profile()
	 *
	 * @description This method checks if give user profile is private.
	 *
	 * @usage <?php $ultimatemember->user->is_private_profile( $user_id ); ?>
	 *
	 * @param $user_id (numeric) (required) A user ID must be passed to check if the user profile is private
	 *
	 * @returns Returns true if user profile is private and false if user profile is public.
	 *
	 * @example This example display a specific user's name If his profile is public

		<?php

			um_fetch_user( 60 );
			$is_private = $ultimatemember->user->is_private_profile( 60 );
			if ( !$is_private ) {
				echo 'User is public and his name is ' . um_user('display_name');
			}

		?>

	 *
	 *
	 */
	function is_private_profile( $user_id ) {
		$privacy = get_user_meta( $user_id, 'profile_privacy', true );
		if ( $privacy == __('Only me','ultimate-member') ) {
			return true;
		}
		return false;
	}

	/**
	 * @function is_approved()
	 *
	 * @description This method can be used to determine If a certain user is approved or not.
	 *
	 * @usage <?php $ultimatemember->user->is_approved( $user_id ); ?>
	 *
	 * @param $user_id (numeric) (required) The user ID to check approval status for
	 *
	 * @returns True if user is approved and false if user is not approved.
	 *
	 * @example Do something If a user's membership is approved

		<?php

			if ( $ultimatemember->user->is_approved( 55 ) {
				// User account is approved
			} else {
				// User account is not approved
			}

		?>

	 *
	 *
	 */
	function is_approved( $user_id ) {
		$status = get_user_meta( $user_id, 'account_status', true );
		if ( $status == 'approved' || $status == '' ) {
			return true;
		}
		return false;
	}

	/***
	***	@Is private
	***/
	function is_private_case( $user_id, $case ) {
		$privacy = get_user_meta( $user_id, 'profile_privacy', true );

		if ( $privacy == $case ) {
			$bool = apply_filters('um_is_private_filter_hook', false, $privacy, $user_id );
			return $bool;
		}

		return false;
	}

	/***
	***	@update files
	***/
	function update_files( $changes ) {

		global $ultimatemember;

		foreach( $changes as $key => $uri ) {
			$src = um_is_temp_upload( $uri );
			$ultimatemember->files->new_user_upload( $this->id, $src, $key );
		}

	}

	/***
	***	@update profile
	***/
	function update_profile( $changes ) {

		global $ultimatemember;

		$args['ID'] = $this->id;
		$changes = apply_filters('um_before_update_profile', $changes, $this->id);

	   	// save or update profile meta
		foreach( $changes as $key => $value ) {
            if ( !in_array( $key, $this->update_user_keys ) ) {
            	
            	update_user_meta( $this->id, $key, $value );

			} else {

				$args[$key] = esc_attr( $changes[$key] );

			}

		}
        
       
		// update user
		if ( count( $args ) > 1 ) {
			wp_update_user( $args );
		}

	}

	/***
	***	@user exists by meta key and value
	***/
	function user_has_metadata( $key, $value ) {

		global $ultimatemember;
		$value = $ultimatemember->validation->safe_name_in_url( $value );

		$ids = get_users(array( 'fields' => 'ID', 'meta_key' => $key,'meta_value' => $value,'meta_compare' => '=') );
		if ( !isset( $ids ) || empty( $ids ) ) return false;
		foreach( $ids as $k => $id ) {
			if ( $id == um_user('ID') ){
				unset( $ids[$k] );
			} else {
				$duplicates[] = $id;
			}
		}
		if ( isset( $duplicates ) && !empty( $duplicates ) )
			return count( $duplicates );
		return false;
	}


	/***
	***	@user exists by name
	***/
	function user_exists_by_name( $value ) {

		global $ultimatemember;
		
		// Permalink base
		$permalink_base = um_get_option('permalink_base');

		$raw_value = $value;
		$value = $ultimatemember->validation->safe_name_in_url( $value );
		$value = um_clean_user_basename( $value );

		// Search by Profile Slug
		$args = array(
				"fields" => array("ID"),
				'meta_query' => array(
			        'relation' => 'OR',
			        array(
			        	'key'		=>  'um_user_profile_url_slug_'.$permalink_base,
			        	'value'		=> strtolower( $raw_value ),
			        	'compare'	=> '='

			        )
			       
			    )
		);
		
		
		$ids = new WP_User_Query( $args );

		if( $ids->total_users > 0 ){
			$um_user_query = current( $ids->get_results() );
			return $um_user_query->ID;
		}

		// Search by Display Name or ID
		$args = array(
				"fields" => array("ID"),
				"search" => $value,
				'search_columns' => array( 'display_name','ID' )
		);	
		
		$ids = new WP_User_Query( $args );
		
		if( $ids->total_users > 0 ){
			$um_user_query = current( $ids->get_results() );
			return $um_user_query->ID;
		}


		// Search By User Login
		$value = str_replace(".", "_", $value );
		$value = str_replace(" ", "", $value );
		
		$args = array(
				"fields" => array("ID"),
				"search" => $value,
				'search_columns' => array(
			        'user_login',
			    )
		);

		$ids = new WP_User_Query( $args );
		
		if( $ids->total_users > 0 ){
			$um_user_query = current( $ids->get_results() );
			return $um_user_query->ID;
		}

		return false;
	}


	/**
	 * @function user_exists_by_id()
	 *
	 * @description This method checks if a user exists or not in your site based on the user ID.
	 *
	 * @usage <?php $ultimatemember->user->user_exists_by_id( $user_id ); ?>
	 *
	 * @param $user_id (numeric) (required) A user ID must be passed to check if the user exists
	 *
	 * @returns Returns true if user exists and false if user does not exist.
	 *
	 * @example Basic Usage

		<?php

			$boolean = $ultimatemember->user->user_exists_by_id( 15 );
			if ( $boolean ) {
				// That user exists
			}

		?>

	 *
	 *
	 */
	function user_exists_by_id( $user_id ) {
		$aux = get_userdata( intval( $user_id ) );
		if($aux==false){
			return false;
		} else {
			return $user_id;
		}
	}
	/**
	 * @function user_exists_by_email_as_username()
	 *
	 * @description This method checks if a user exists or not in your site based on the user email as username
	 *
	 * @usage <?php $ultimatemember->user->user_exists_by_email_as_username( $slug ); ?>
	 *
	 * @param $slug (string) (required) A user slug must be passed to check if the user exists
	 *
	 * @returns Returns true if user exists and false if user does not exist.
	 *
	 * @example Basic Usage

		<?php

			$boolean = $ultimatemember->user->user_exists_by_email_as_username( 'calumgmail-com' );
			if ( $boolean ) {
				// That user exists
			}

		?>

	 *
	 *
	 */
	function user_exists_by_email_as_username( $slug ){

		$user_id = false;

		$ids = get_users( array( 'fields' => 'ID', 'meta_key' => 'um_email_as_username_'.$slug ) );
		if ( isset( $ids[0] ) && ! empty( $ids[0] ) ){
			$user_id = $ids[0];
		}

		return $user_id;
	}

	/**
	 * Set gravatar hash id
	 */
	function set_gravatar( $user_id ){

		um_fetch_user( $user_id );
		$email_address = um_user('user_email');
		$hash_email_address = '';

		if( $email_address ){
			$hash_email_address = md5( $email_address );
			$this->profile['synced_gravatar_hashed_id'] = $hash_email_address;
			$this->update_usermeta_info('synced_gravatar_hashed_id');
		}

		return $hash_email_address;
	}

}
