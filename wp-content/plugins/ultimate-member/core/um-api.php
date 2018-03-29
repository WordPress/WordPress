<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class UM_REST_API {

	const VERSION = '1.0';

	private $pretty_print = false;
	public 	$log_requests = true;
	private $is_valid_request = false;
	private $user_id = 0;
	private $stats;
	private $data = array();
	private $override = true;

	public function __construct() {
		
		add_action( 'init',                     array( $this, 'add_endpoint'     ) );
		add_action( 'template_redirect',        array( $this, 'process_query'    ), -1 );
		add_filter( 'query_vars',               array( $this, 'query_vars'       ) );
		
		add_action( 'um_user_profile_section',  array( $this, 'user_key_field'   ), 2 );
		
		add_action( 'personal_options_update',  array( $this, 'update_key'       ) );
		add_action( 'edit_user_profile_update', array( $this, 'update_key'       ) );

		// Determine if JSON_PRETTY_PRINT is available
		$this->pretty_print = defined( 'JSON_PRETTY_PRINT' ) ? JSON_PRETTY_PRINT : null;

		// Allow API request logging to be turned off
		$this->log_requests = apply_filters( 'um_api_log_requests', $this->log_requests );

	}

	/**
	 * Registers a new rewrite endpoint for accessing the API
	 */
	public function add_endpoint( $rewrite_rules ) {
		add_rewrite_endpoint( 'um-api', EP_ALL );
	}

	/**
	 * Registers query vars for API access
	 */
	public function query_vars( $vars ) {

		$vars[] = 'key';
		$vars[] = 'token';
		$vars[] = 'format';
		$vars[] = 'query';
		$vars[] = 'type';
		$vars[] = 'data';
		$vars[] = 'fields';
		$vars[] = 'value';
		$vars[] = 'number';
		$vars[] = 'id';
		$vars[] = 'email';
		$vars[] = 'orderby';
		$vars[] = 'order';
		$vars[] = 'include';
		$vars[] = 'exclude';
		
		$this->vars = $vars;

		return $vars;
	}

	/**
	 * Validate the API request
	 */
	private function validate_request() {
		global $wp_query;

		$this->override = false;

        // Make sure we have both user and api key
		if ( ! empty( $wp_query->query_vars['um-api'] ) ) {

			if ( empty( $wp_query->query_vars['token'] ) || empty( $wp_query->query_vars['key'] ) )
				$this->missing_auth();

			// Retrieve the user by public API key and ensure they exist
			if ( ! ( $user = $this->get_user( $wp_query->query_vars['key'] ) ) ) :
				$this->invalid_key();
			else :
				$token  = urldecode( $wp_query->query_vars['token'] );
				$secret = get_user_meta( $user, 'um_user_secret_key', true );
				$public = urldecode( $wp_query->query_vars['key'] );

				if ( hash_equals( md5( $secret . $public ), $token ) )
					$this->is_valid_request = true;
				else
					$this->invalid_auth();
			endif;
		}
	}

	/**
	 * Retrieve the user ID based on the public key provided
	 */
	public function get_user( $key = '' ) {
		global $wpdb, $wp_query;

		if( empty( $key ) )
			$key = urldecode( $wp_query->query_vars['key'] );

		if ( empty( $key ) ) {
			return false;
		}

		$user = get_transient( md5( 'um_api_user_' . $key ) );

		if ( false === $user ) {
			$user = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'um_user_public_key' AND meta_value = %s LIMIT 1", $key ) );
			set_transient( md5( 'um_api_user_' . $key ) , $user, DAY_IN_SECONDS );
		}

		if ( $user != NULL ) {
			$this->user_id = $user;
			return $user;
		}

		return false;
	}

	/**
	 * Displays a missing authentication error if all the parameters aren't
	 * provided
	 */
	private function missing_auth() {
		$error = array();
		$error['error'] = __( 'You must specify both a token and API key!', 'ultimate-member');

		$this->data = $error;
		$this->output( 401 );
	}

	/**
	 * Displays an authentication failed error if the user failed to provide valid credentials
	 */
	private function invalid_auth() {
		$error = array();
		$error['error'] = __( 'Your request could not be authenticated', 'ultimate-member');

		$this->data = $error;
		$this->output( 401 );
	}

	/**
	 * Displays an invalid API key error if the API key provided couldn't be validated
	 */
	private function invalid_key() {
		$error = array();
		$error['error'] = __( 'Invalid API key', 'ultimate-member');

		$this->data = $error;
		$this->output( 401 );
	}


	/**
	 * Listens for the API and then processes the API requests
	 */
	public function process_query() {
		global $wp_query;

		// Check for um-api var. Get out if not present
		if ( ! isset( $wp_query->query_vars['um-api'] ) )
			return;

		// Check for a valid user and set errors if necessary
		$this->validate_request();

		// Only proceed if no errors have been noted
		if( ! $this->is_valid_request )
			return;

		if( ! defined( 'UM_DOING_API' ) ) {
			define( 'UM_DOING_API', true );
		}

		// Determine the kind of query
		$query_mode = $this->get_query_mode();
		foreach( $this->vars as $k ) {
			$args[ $k ] = isset( $wp_query->query_vars[ $k ] ) ? $wp_query->query_vars[ $k ] : null;
		}

		$data = array();

		switch( $query_mode ) :

			case 'get.stats':
				$data = $this->get_stats( $args );
				break;
				
			case 'get.users':
				$data = $this->get_users( $args );
				break;
				
			case 'get.user':
				$data = $this->get_auser( $args );
				break;
				
			case 'update.user':
				$data = $this->update_user( $args );
				break;
				
			case 'delete.user':
				$data = $this->delete_user( $args );
				break;

			case 'get.following':
				$data = $this->get_following( $args );
				break;
				
			case 'get.followers':
				$data = $this->get_followers( $args );
				break;
				
		endswitch;

		// Allow extensions to setup their own return data
		$this->data = apply_filters( 'um_api_output_data', $data, $query_mode, $this );

		// Log this API request, if enabled. We log it here because we have access to errors.
		$this->log_request( $this->data );

		// Send out data to the output function
		$this->output();
	}
	
	/**
	 * Get some stats
	 */
	public function get_stats( $args ) {
		global $wpdb, $ultimatemember;
		extract( $args );
		
		$response = array();
		$error = array();
		
		$query = "SELECT COUNT(*) FROM {$wpdb->prefix}users";
		$count = absint( $wpdb->get_var($query) );
		$response['stats']['total_users'] = $count;
		
		include_once um_path . 'admin/core/um-admin-dashboard.php';
		$pending = $um_dashboard->get_pending_users_count();
		$response['stats']['pending_users'] = absint( $pending );
		
		if ( class_exists( 'UM_Notifications_API') ) {
			$query = "SELECT COUNT(*) FROM {$wpdb->prefix}um_notifications";
			$total_notifications = absint( $wpdb->get_var( $query ) );
			$response['stats']['total_notifications'] = $total_notifications;
		}
		
		if ( class_exists( 'UM_Messaging_API') ) {
			$query = "SELECT COUNT(*) FROM {$wpdb->prefix}um_conversations";
			$total_conversations = absint( $wpdb->get_var( $query ) );
			$response['stats']['total_conversations'] = $total_conversations;
			
			$query = "SELECT COUNT(*) FROM {$wpdb->prefix}um_messages";
			$total_messages = absint( $wpdb->get_var( $query ) );
			$response['stats']['total_messages'] = $total_messages;
		}
		
		if ( class_exists( 'UM_Online_API') ) {
			global $um_online;
			$total_online = count( $um_online->get_users() );
			$response['stats']['total_online'] = $total_online;
		}
		
		if ( class_exists( 'UM_Reviews_API') ) {
			$query = "SELECT COUNT(*) FROM {$wpdb->prefix}posts WHERE post_status='publish' AND post_type='um_review'";
			$total_reviews = absint( $wpdb->get_var( $query ) );
			$response['stats']['total_reviews'] = $total_reviews;
		}
		
		return $response;
	}
	
	/**
	 * Update user API query
	 */
	public function update_user( $args ) {
		global $ultimatemember;
		extract( $args );
		
		$response = array();
		$error = array();
		
		if ( !$id ) {
			$error['error'] = __('You must provide a user ID','ultimate-member');
			return $error;
		}
		
		if ( !$data ) {
			$error['error'] = __('You need to provide data to update','ultimate-member');
			return $error;
		}
		
		um_fetch_user( $id );
		
		switch ( $data ) {
			case 'status':
				$ultimatemember->user->set_status( $value );
				$response['success'] = __('User status has been changed.','ultimate-member');
				break;
			case 'role':
				$ultimatemember->user->set_role( $value );
				$response['success'] = __('User level has been changed.','ultimate-member');
				break;
			case 'wp_role':
				$wp_user_object = new WP_User( $id );
				$wp_user_object->set_role( $value );
				$response['success'] = __('User WordPress role has been changed.','ultimate-member');
				break;
			default:
				update_user_meta( $id, $data, esc_attr( $value ) );
				$response['success'] = __('User meta has been changed.','ultimate-member');
				break;
		}
		
		return $response;
	}
	
	/**
	 * Process Get followers users API Request
	 */
	public function get_followers( $args ) {
		global $ultimatemember;
		extract( $args );
		
		$response = array();
		$error = array();
		
		if ( !$id ) {
			$error['error'] = __('You must provide a user ID','ultimate-member');
			return $error;
		}
		
		if ( class_exists( 'UM_Followers_API' ) ) {
			global $um_followers;
			$results = $um_followers->api->followers( $id );
			if ( !$results ) {
				$error['error'] = __('No users were found','ultimate-member');
				return $error;
			}
			$response['followers']['count'] = $um_followers->api->count_followers_plain( $id );
			foreach( $results as $k => $v ) {
				$user = get_userdata( $v['user_id2'] );
				$response['followers']['users'][$k]['ID'] = $v['user_id2'];
				$response['followers']['users'][$k]['username'] = $user->user_login;
				$response['followers']['users'][$k]['display_name'] = $user->display_name;
			}
		} else {
			$error['error'] = __('Invalid request','ultimate-member');
			return $error;
		}
		
		return $response;
	}
	
	/**
	 * Process Get following users API Request
	 */
	public function get_following( $args ) {
		global $ultimatemember;
		extract( $args );
		
		$response = array();
		$error = array();
		
		if ( !$id ) {
			$error['error'] = __('You must provide a user ID','ultimate-member');
			return $error;
		}
		
		if ( class_exists( 'UM_Followers_API' ) ) {
			global $um_followers;
			$results = $um_followers->api->following( $id );
			if ( !$results ) {
				$error['error'] = __('No users were found','ultimate-member');
				return $error;
			}
			$response['following']['count'] = $um_followers->api->count_following_plain( $id );
			foreach( $results as $k => $v ) {
				$user = get_userdata( $v['user_id1'] );
				$response['following']['users'][$k]['ID'] = $v['user_id1'];
				$response['following']['users'][$k]['username'] = $user->user_login;
				$response['following']['users'][$k]['display_name'] = $user->display_name;
			}
		} else {
			$error['error'] = __('Invalid request','ultimate-member');
			return $error;
		}
		
		return $response;
	}
	
	/**
	 * Process Get users API Request
	 */
	public function get_users( $args ) {
		global $ultimatemember;
		extract( $args );
		
		$response = array();
		$error = array();
		
		if ( !$number )
			$number = 10;
		
		if ( !$orderby )
			$orderby = 'user_registered';
		
		if ( !$order )
			$order = 'desc';
		
		$loop_a = array('number' => $number, 'orderby' => $orderby, 'order' => $order );
		
		if ( $include ) {
			$include = explode(',', $include );
			$loop_a['include'] = $include;
		}
		
		if ( $exclude ) {
			$exclude = explode(',', $exclude );
			$loop_a['exclude'] = $exclude;
		}
		
		$loop = get_users( $loop_a );
		
		foreach( $loop as $user ) {
			
			unset( $user->data->user_status );
			unset( $user->data->user_activation_key );
			unset( $user->data->user_pass );
			
			um_fetch_user( $user->ID );
			
			foreach( $user as $key => $val ) {
				if ( $key != 'data' ) continue;
				if ( $key == 'data' ) {
					$key = 'profile';
					$val->roles = $user->roles;
					$val->first_name = um_user('first_name');
					$val->last_name = um_user('last_name');
					$val->community_role = um_user('role');
					$val->account_status = um_user('account_status');
					$val->profile_pic_original = $this->getsrc( um_user('profile_photo', 'original') );
					$val->profile_pic_normal = $this->getsrc( um_user('profile_photo', 200) );
					$val->profile_pic_small = $this->getsrc( um_user('profile_photo', 40) );
					$val->cover_photo = $this->getsrc( um_user('cover_photo', 1000) );
					
					if ( class_exists('UM_Followers_API') ) {
						global $um_followers;
						$val->followers_count = $um_followers->api->count_followers_plain( $user->ID );
						$val->following_count = $um_followers->api->count_following_plain( $user->ID );
					}
					
				}
				$response[ $user->ID ] = $val;
			}
			
		}
		
		return $response;
	}
	
	/**
	 * Process delete user via API
	 */
	public function delete_user( $args ) {
		global $ultimatemember;
		extract( $args );
		
		$response = array();
		$error = array();
		
		if ( !isset( $id ) ) {
			$error['error'] = __('You must provide a user ID','ultimate-member');
			return $error;
		}
		
		$user = get_userdata( $id );
		if ( !$user ) {
			$error['error'] = __('Invalid user specified','ultimate-member');
			return $error;
		}
		
		um_fetch_user( $id );
		$ultimatemember->user->delete();
		
		$response['success'] = __('User has been successfully deleted.','ultimate-member');
		
		return $response;
	}
	
	/**
	 * Process Get user API Request
	 */
	public function get_auser( $args ) {
		global $ultimatemember;
		extract( $args );
		
		$response = array();
		$error = array();
		
		if ( !isset( $id ) ) {
			$error['error'] = __('You must provide a user ID','ultimate-member');
			return $error;
		}
		
		$user = get_userdata( $id );
		if ( !$user ) {
			$error['error'] = __('Invalid user specified','ultimate-member');
			return $error;
		}
		
		unset( $user->data->user_status );
		unset( $user->data->user_activation_key );
		unset( $user->data->user_pass );

		um_fetch_user( $user->ID );
		
		if ( isset( $fields ) && $fields ) {
			$fields = explode(',', $fields );
			$response['ID'] = $user->ID;
			$response['username'] = $user->user_login;
			foreach( $fields as $field ) {
			
				switch( $field ) {
					
					default:
						$response[$field] = (  um_profile( $field ) ) ? um_profile( $field ) : '';
						break;
						
					case 'mycred_points':
						$response['mycred_points'] = number_format( (int)get_user_meta( $user->ID, 'mycred_default', true ), 2 );
						break;
					
					case 'cover_photo':
						$response['cover_photo'] = $this->getsrc( um_user('cover_photo', 1000) );
						break;
					
					case 'profile_pic':
						$response['profile_pic_original'] = $this->getsrc( um_user('profile_photo', 'original') );
						$response['profile_pic_normal'] = $this->getsrc( um_user('profile_photo', 200) );
						$response['profile_pic_small'] = $this->getsrc( um_user('profile_photo', 40) );
						break;
						
					case 'status':
						$response['status'] = um_user('account_status');
						break;
						
					case 'role':
						$response['role'] = um_user('role');
						break;
						
					case 'email':
					case 'user_email':
						$response['email'] = um_user('user_email');
						break;
						
					case 'followers':
						if ( class_exists('UM_Followers_API') ) {
							global $um_followers;
							$response['followers_count'] = $um_followers->api->count_followers_plain( $user->ID );
							$response['following_count'] = $um_followers->api->count_following_plain( $user->ID );
						}
						break;
						
				}
				
			}
		} else {

			foreach( $user as $key => $val ) {
				if ( $key != 'data' ) continue;
				if ( $key == 'data' ) {
					$key = 'profile';
					$val->roles = $user->roles;
					$val->first_name = um_user('first_name');
					$val->last_name = um_user('last_name');
					$val->community_role = um_user('role');
					$val->account_status = um_user('account_status');
					$val->profile_pic_original = $this->getsrc( um_user('profile_photo', 'original') );
					$val->profile_pic_normal = $this->getsrc( um_user('profile_photo', 200) );
					$val->profile_pic_small = $this->getsrc( um_user('profile_photo', 40) );
					$val->cover_photo = $this->getsrc( um_user('cover_photo', 1000) );
						
					if ( class_exists('UM_Followers_API') ) {
						global $um_followers;
						$val->followers_count = $um_followers->api->count_followers_plain( $user->ID );
						$val->following_count = $um_followers->api->count_following_plain( $user->ID );
					}
						
				}
				$response = $val;
			}
		
		}
		
		return $response;
	}
	
	/**
	 * Get source
	 */
	public function getsrc( $image ) {
		if (preg_match('/<img.+?src(?: )*=(?: )*[\'"](.*?)[\'"]/si', $image, $arrResult)) {
			return $arrResult[1];
		}
		return '';
	}
	
	/**
	 * Determines the kind of query requested and also ensure it is a valid query
	 */
	public function get_query_mode() {
		global $wp_query;

		// Whitelist our query options
		$accepted = apply_filters( 'um_api_valid_query_modes', array(
			'get.users',
			'get.user',
			'update.user',
			'delete.user',
			'get.following',
			'get.followers',
			'get.stats',
		) );

		$query = isset( $wp_query->query_vars['um-api'] ) ? $wp_query->query_vars['um-api'] : null;
		$error = array();
		// Make sure our query is valid
		if ( ! in_array( $query, $accepted ) ) {
			$error['error'] = __( 'Invalid query!', 'ultimate-member');

			$this->data = $error;
			$this->output();
		}

		return $query;
	}

	/**
	 * Get page number
	 */
	public function get_paged() {
		global $wp_query;

		return isset( $wp_query->query_vars['page'] ) ? $wp_query->query_vars['page'] : 1;
	}

	/**
	 * Retrieve the output format
	 */
	public function get_output_format() {
		global $wp_query;

		$format = isset( $wp_query->query_vars['format'] ) ? $wp_query->query_vars['format'] : 'json';

		return apply_filters( 'um_api_output_format', $format );
	}

	/**
	 * Log each API request, if enabled
	 */
	private function log_request( $data = array() ) {
		if ( ! $this->log_requests )
			return;

	}


	/**
	 * Retrieve the output data
	 */
	public function get_output() {
		return $this->data;
	}

	/**
	 * Output Query in either JSON/XML. The query data is outputted as JSON
	 * by default
	 */
	public function output( $status_code = 200 ) {
		global $wp_query;

		$format = $this->get_output_format();

		status_header( $status_code );

		do_action( 'um_api_output_before', $this->data, $this, $format );

		switch ( $format ) :

			case 'xml' :

				require_once um_path . 'core/lib/array2xml.php';
				$xml = Array2XML::createXML( 'um', $this->data );
				echo $xml->saveXML();

				break;

			case 'json' :
			case '' :

				header( 'Content-Type: application/json' );
				if ( ! empty( $this->pretty_print ) )
					echo json_encode( $this->data, $this->pretty_print );
				else
					echo json_encode( $this->data );

				break;


			default :

				// Allow other formats to be added via extensions
				do_action( 'um_api_output_' . $format, $this->data, $this );

				break;

		endswitch;

		do_action( 'um_api_output_after', $this->data, $this, $format );

		die();
	}

	/**
	 * Modify User Profile
	 */
	function user_key_field( $user ) {
		
		if( ! isset( $user->ID ) ) return;

		if ( current_user_can( 'edit_users' ) && current_user_can( 'edit_user', $user->ID ) ) {
			$user = get_userdata( $user->ID );
			?>
			<table class="form-table">
				<tbody>
					<tr>
						<th>
							<label for="um_set_api_key"><?php _e( 'Ultimate Member REST API', 'ultimate-member'); ?></label>
						</th>
						<td>
							<?php if ( empty( $user->um_user_public_key ) ) { ?>
								<p><input name="um_set_api_key" type="checkbox" id="um_set_api_key" value="0" />
								<span class="description"><?php _e( 'Generate API Key', 'ultimate-member'); ?></span></p>
							<?php } else { ?>
								<p>
								<strong><?php _e( 'Public key:', 'ultimate-member'); ?>&nbsp;</strong><span id="publickey"><?php echo $user->um_user_public_key; ?></span><br/>
								<strong><?php _e( 'Secret key:', 'ultimate-member'); ?>&nbsp;</strong><span id="privatekey"><?php echo $user->um_user_secret_key; ?></span><br/>
								<strong><?php _e( 'Token:', 'ultimate-member'); ?>&nbsp;</strong><span id="token"><?php echo $this->get_token( $user->ID ); ?></span>
								</p>
								<p><input name="um_set_api_key" type="checkbox" id="um_set_api_key" value="0" />
								<span class="description"><?php _e( 'Revoke API Keys', 'ultimate-member'); ?></span></p>
							<?php } ?>
						</td>
					</tr>
				</tbody>
			</table>
		<?php }
	}

	/**
	 * Generate new API keys for a user
	 */
	public function generate_api_key( $user_id = 0, $regenerate = false ) {

		if( empty( $user_id ) ) {
			return false;
		}

		$user = get_userdata( $user_id );

		if( ! $user ) {
			return false;
		}

		if ( empty( $user->um_user_public_key ) ) {
			update_user_meta( $user_id, 'um_user_public_key', $this->generate_public_key( $user->user_email ) );
			update_user_meta( $user_id, 'um_user_secret_key', $this->generate_private_key( $user->ID ) );
		} elseif( $regenerate == true ) {
			$this->revoke_api_key( $user->ID );
			update_user_meta( $user_id, 'um_user_public_key', $this->generate_public_key( $user->user_email ) );
			update_user_meta( $user_id, 'um_user_secret_key', $this->generate_private_key( $user->ID ) );
		} else {
			return false;
		}

		return true;
	}

	/**
	 * Revoke a users API keys
	 */
	public function revoke_api_key( $user_id = 0 ) {

		if( empty( $user_id ) ) {
			return false;
		}

		$user = get_userdata( $user_id );

		if( ! $user ) {
			return false;
		}

		if ( ! empty( $user->um_user_public_key ) ) {
			delete_transient( md5( 'um_api_user_' . $user->um_user_public_key ) );
			delete_user_meta( $user_id, 'um_user_public_key' );
			delete_user_meta( $user_id, 'um_user_secret_key' );
		} else {
			return false;
		}

		return true;
	}


	/**
	 * Generate and Save API key
	 */
	public function update_key( $user_id ) {
		if ( current_user_can( 'edit_user', $user_id ) && isset( $_POST['um_set_api_key'] ) ) {

			$user = get_userdata( $user_id );

			if ( empty( $user->um_user_public_key ) ) {
				update_user_meta( $user_id, 'um_user_public_key', $this->generate_public_key( $user->user_email ) );
				update_user_meta( $user_id, 'um_user_secret_key', $this->generate_private_key( $user->ID ) );
			} else {
				$this->revoke_api_key( $user_id );
			}
		}
	}

	/**
	 * Generate the public key for a user
	 */
	private function generate_public_key( $user_email = '' ) {
		$auth_key = defined( 'AUTH_KEY' ) ? AUTH_KEY : '';
		$public   = hash( 'md5', $user_email . $auth_key . date( 'U' ) );
		return $public;
	}

	/**
	 * Generate the secret key for a user
	 */
	private function generate_private_key( $user_id = 0 ) {
		$auth_key = defined( 'AUTH_KEY' ) ? AUTH_KEY : '';
		$secret   = hash( 'md5', $user_id . $auth_key . date( 'U' ) );
		return $secret;
	}

	/**
	 * Retrieve the user's token
	 */
	private function get_token( $user_id = 0 ) {
		$user = get_userdata( $user_id );
		return hash( 'md5', $user->um_user_secret_key . $user->um_user_public_key );
	}

}
