<?php

class UM_ADDON_install_info {

	function __construct() {
		
		add_action('admin_menu', array(&$this, 'admin_menu'), 1001);
		
		add_action('admin_init', array(&$this, 'admin_init'), 1);
		
		add_action('um_admin_addon_hook', array(&$this, 'um_admin_addon_hook') );

	}


   function admin_menu() {
		
		global $ultimatemember;
		$this->addon = $ultimatemember->addons['install_info'];
		add_submenu_page('ultimatemember', "System Info","System Info", 'manage_options', 'um_install_info', array(&$this, 'content') );
		
	}

	function um_admin_addon_hook( $hook ) {
		global $ultimatemember;

		switch ( $hook ) {
			
			case 'download_install_info':
				
					nocache_headers();

					header( "Content-type: text/plain" );
					header( 'Content-Disposition: attachment; filename="ultimatemember-install-info.txt"' );

					echo wp_strip_all_tags( $_POST['um-install-info'] );
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
		global $wpdb, $ultimatemember;
		
		if( !class_exists( 'Browser' ) )
			require_once um_path . 'core/lib/browser.php';
		
		// Detect browser 
			$browser 	= new Browser();
		
		// Get theme info
			$theme_data = wp_get_theme();
			$theme      = $theme_data->Name . ' ' . $theme_data->Version;
		
		// Identify Hosting Provider
		 	$host 		= um_get_host();
           
           um_fetch_user( get_current_user_id() );
		?>
		
		<div class="wrap">
		
			<h2>Ultimate Member</h2>
			
			<h3><?php echo $this->addon[0]; ?></h3>
			
			<?php if ( isset( $this->content ) ) { 
				echo $this->content;
			} else { ?>

		<form action="<?php echo esc_url( admin_url( 'admin.php?page=um_install_info' ) ); ?>" method="post" dir="ltr">
			<textarea style="width:100%; height:400px;" readonly="readonly" onclick="this.focus();this.select()" id="install-info-textarea" name="um-install-info" title="<?php _e( 'To copy the Install info, click below then press Ctrl + C (PC) or Cmd + C (Mac).', 'edd' ); ?>">
### Begin Install Info ###

## Please include this information when posting support requests ##

<?php do_action( 'um_install_info_before' ); ?>

--- Site Info ---

Site URL:						<?php echo site_url() . "\n"; ?>
Home URL:					<?php echo home_url() . "\n"; ?>
Multisite:					<?php echo is_multisite() ? 'Yes' . "\n" : 'No' . "\n" ?>

--- Hosting Provider ---

<?php if( $host ) : ?>
Host:						<?php echo $host . "\n"; ?>
<?php endif; ?>

--- User Browser ---

<?php echo $browser ; ?>

---- Current User Details --

<?php $user = wp_get_current_user(); ?>
UM Role: <?php echo um_user('role'). "\n"; ?>
WP Role: <?php echo $user->roles ? $user->roles[0] : false; echo  "\n"; ?>

--- WordPress Configurations ---

Version:						<?php echo get_bloginfo( 'version' ) . "\n"; ?>
Language:					<?php echo get_locale()."\n"; ?>
Permalink Structure:			<?php echo get_option( 'permalink_structure' ) . "\n"; ?>
Active Theme:				<?php echo $theme . "\n"; ?>
<?php $show_on_front = get_option( 'show_on_front' ); ?>
<?php if( $show_on_front == "posts" ): ?>
Show On Front:				<?php echo get_option( 'show_on_front' ) . "/static\n" ?>
<?php elseif( $show_on_front == "page" ): ?>
Page On Front:				<?php $id = get_option( 'page_on_front' ); echo get_the_title( $id ) . ' (#' . $id . ')' . "\n" ?>
Page For Posts:				<?php $id = get_option( 'page_for_posts' ); echo get_the_title( $id ) . ' (#' . $id . ')' . "\n" ?>
<?php endif; ?>
ABSPATH:					<?php echo ABSPATH."\n"; ?>
<?php $wp_count_posts = wp_count_posts(); ?>
All Posts/Pages:				<?php echo array_sum((array)$wp_count_posts)."\n";?>
<?php
$request['cmd'] = '_notify-validate';

$params = array(
	'sslverify'		=> false,
	'timeout'		=> 60,
	'user-agent'	=> 'UltimateMember/' . ultimatemember_version,
	'body'			=> $request
);

$response = wp_remote_post( 'https://www.paypal.com/cgi-bin/webscr', $params );

if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
	$WP_REMOTE_POST =  'wp_remote_post() works' . "\n";
} else {
	$WP_REMOTE_POST =  'wp_remote_post() does not work' . "\n";
}
?>
WP Remote Post:           		<?php echo $WP_REMOTE_POST; ?>
WP_DEBUG:                 			<?php echo defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' . "\n" : 'Disabled' . "\n" : 'Not set' . "\n" ?>
WP Table Prefix:          			<?php echo "Length: ". strlen( $wpdb->prefix ); echo ", Status:"; if ( strlen( $wpdb->prefix )>16 ) {echo " ERROR: Too Long";} else {echo " Acceptable";} echo "\n"; ?>
Memory Limit:   				<?php echo ( um_let_to_num( WP_MEMORY_LIMIT )/( 1024 ) )."MB"; ?><?php echo "\n"; ?>

--- UM Configurations ---

Version:						<?php echo ultimatemember_version . "\n"; ?>
Upgraded From:            		<?php echo get_option( 'um_version_upgraded_from', 'None' ) . "\n"; ?>
Current URL Method:			<?php echo um_get_option( 'current_url_method' ). "\n"; ?>
Cache User Profile:			<?php if( um_get_option( 'um_profile_object_cache_stop' ) == 1 ){ echo "No"; }else{ echo "Yes"; } echo "\n"; ?>
Generate Slugs on Directories:	<?php if( um_get_option( 'um_generate_slug_in_directory' ) == 1 ){ echo "No"; }else{ echo "Yes"; } echo "\n"; ?>
Rewrite Rules: 				<?php if( um_get_option( 'um_flush_stop' ) == 1 ){ echo "No"; }else{ echo "Yes"; } echo "\n"; ?>
Force UTF-8 Encoding: 		<?php if( um_get_option( 'um_force_utf8_strings' ) == 1 ){ echo "Yes"; }else{ echo "No"; } echo "\n"; ?>
Time Check Security: 			<?php if( um_get_option( 'enable_timebot' ) == 1 ){ echo "Yes"; }else{ echo "No"; } echo "\n"; ?>
JS/CSS Compression: 			<?php if( um_get_option( 'disable_minify' ) == 0 ){ echo "Yes"; }else{ echo "No"; } echo "\n"; ?>
<?php if( is_multisite() ): ?>
Network Structure:			<?php echo um_get_option( 'network_permalink_structure' ). "\n"; ?>
<?php endif; ?>
Nav Menu Settings: 			<?php if( um_get_option( 'disable_menu' ) == 0 ){ echo "Yes"; }else{ echo "No"; } echo "\n"; ?>
Port Forwarding in URL: 		<?php if( um_get_option( 'um_port_forwarding_url' ) == 1 ){ echo "Yes"; }else{ echo "No"; } echo "\n"; ?>
Exclude CSS/JS on Home: 		<?php if( um_get_option( 'js_css_exlcude_home' ) == 1 ){ echo "Yes"; }else{ echo "No"; } echo "\n"; ?>

--- UM Pages Configuration ---

<?php do_action("um_install_info_before_page_config") ?>
User:						<?php echo get_permalink( um_get_option('core_user') ) . "\n"; ?>
Account:						<?php echo get_permalink( um_get_option('core_account') ) . "\n"; ?>
Members:					<?php echo get_permalink( um_get_option('core_members') ) . "\n"; ?>
Register:						<?php echo get_permalink( um_get_option('core_register') ) . "\n"; ?>
Login:						<?php echo get_permalink( um_get_option('core_login') ) . "\n"; ?>
Logout:						<?php echo get_permalink( um_get_option('core_logout') ) . "\n"; ?>
Password Reset:				<?php echo get_permalink( um_get_option('core_password-reset') ) . "\n"; ?>
<?php do_action("um_install_info_after_page_config") ?>

-- UM Users Configuration ---

Default New User Role: 		<?php  echo um_get_option('default_role') . "\n"; ?>
Profile Permalink Base:		<?php  echo um_get_option('permalink_base') . "\n"; ?>
User Display Name:			<?php  echo um_get_option('display_name') . "\n"; ?>
Force Name to Uppercase:		<?php echo $this->value( um_get_option('force_display_name_capitlized'), 'yesno', true ); ?>
Redirect author to profile: 		<?php echo $this->value( um_get_option('author_redirect'), 'yesno', true ); ?>
Enable Members Directory:	<?php echo $this->value( um_get_option('members_page'), 'yesno', true ); ?>
Use Gravatars: 				<?php echo $this->value( um_get_option('use_gravatars'), 'yesno', true ); ?>
<?php if( um_get_option('use_gravatars') ): ?>Gravatar builtin image:		<?php  echo um_get_option('use_um_gravatar_default_builtin_image') . "\n"; ?>
UM Avatar as blank Gravatar: 	<?php echo $this->value( um_get_option('use_um_gravatar_default_image'), 'yesno', true ); ?><?php endif; ?>
Require a strong password: 	<?php echo $this->value( um_get_option('reset_require_strongpass'), 'onoff', true ); ?>
Editable primary email field in profile view:	<?php echo $this->value( um_get_option('editable_primary_email_in_profile'), 'onoff', true ); ?>  

-- UM Access Configuration ---

Panic Key: 								<?php  echo um_get_option('panic_key') . "\n"; ?>
Global Site Access:						<?php  $arr = array('Site accessible to Everyone','','Site accessible to Logged In Users'); echo $arr[ intval( um_get_option('accessible') ) ] . "\n"; ?>
<?php if( um_get_option('accessible') == 2 ):?>
Custom Redirect URL:						<?php echo um_get_option('access_redirect')."\n";?>
Exclude the following URLs:<?php echo "\t\t\t\t".implode("\t\n\t\t\t\t\t\t\t\t\t\t",um_get_option('access_exclude_uris') )."\n";?><?php endif;?>
Backend Login Screen for Guests:			<?php echo $this->value( um_get_option('wpadmin_login'), 'yesno', true ); ?>
<?php if( ! um_get_option('wpadmin_login') ):?>Redirect to alternative login page:			<?php if( um_get_option('wpadmin_login_redirect') == 'um_login_page' ){ echo um_get_core_page('login')."\n"; }else{ echo um_get_option('wpadmin_login_redirect_url')."\n"; }?><?php endif; ?>
Backend Register Screen for Guests:		<?php echo $this->value( um_get_option('wpadmin_register'), 'yesno', true ); ?>
<?php if( ! um_get_option('wpadmin_register') ):?>Redirect to alternative register page:		<?php if( um_get_option('wpadmin_register_redirect') == 'um_register_page' ){ echo um_get_core_page('register')."\n"; }else{ echo um_get_option('wpadmin_register_redirect_url')."\n"; }?><?php endif; ?>
Access Control widget for Admins only: 		<?php echo $this->value( um_get_option('access_widget_admin_only'), 'yesno', true ); ?>
Enable the Reset Password Limit:			<?php echo $this->value( um_get_option('enable_reset_password_limit'), 'yesno', true ); ?>
<?php if( um_get_option('enable_reset_password_limit') ) { 
	echo "Reset Password Limit:\t\t\t\t\t\t".um_get_option('reset_password_limit_number')."\n"; 
	echo "Disable Reset Password Limit for Admins:\t".$this->value( um_get_option('disable_admin_reset_password_limit'), 'yesno', true ); 
} ?>
<?php  $wpadmin_allow_ips = um_get_option('wpadmin_allow_ips'); if( ! empty( $wpadmin_allow_ips ) ){ ?>
Whitelisted Backend IPs: 					<?php echo count( explode("\n",trim(um_get_option('wpadmin_allow_ips') ) ) )."\n"; ?>
<?php }?>
<?php $blocked_ips = um_get_option('blocked_ips'); if( ! empty( $blocked_ips ) ){ ?>
Blocked IP Addresses: 					<?php echo  count( explode("\n",um_get_option('blocked_ips') ) )."\n"; ?>
<?php }?>
<?php $blocked_emails = um_get_option('blocked_emails'); if( ! empty( $blocked_emails ) ){ ?>
Blocked Email Addresses: 					<?php echo  count( explode("\n",um_get_option('blocked_emails') ) )."\n"; ?>
<?php }?>
<?php $blocked_words =  um_get_option('blocked_words'); if( ! empty( $blocked_words ) ){ ?>
Blacklist Words: 							<?php echo  count( explode("\n",um_get_option('blocked_words') ) )."\n"; ?>
<?php }?>


--- UM Email Configurations --

Mail appears from:			<?php $mail_from = um_get_option('mail_from'); if( ! empty( $mail_from ) ){echo um_get_option('mail_from');}else{echo "-";}; echo "\n";?>
Mail appears from address:	<?php $mail_from_addr = um_get_option('mail_from_addr'); if( ! empty( $mail_from_addr ) ){echo um_get_option('mail_from_addr');}else{echo "-";}; echo "\n";?>
Use HTML for E-mails:			<?php echo $this->value( um_get_option('email_html'), 'yesno', true ); ?>
Account Welcome Email:		<?php echo $this->value( um_get_option('welcome_email_on'), 'yesno', true ); ?>
Account Activation Email:		<?php echo $this->value( um_get_option('checkmail_email_on'), 'yesno', true ); ?>
Pending Review Email:		<?php echo $this->value( um_get_option('pending_email_on'), 'yesno', true ); ?>
Account Approved Email:		<?php echo $this->value( um_get_option('approved_email_on'), 'yesno', true ); ?>
Account Rejected Email:		<?php echo $this->value( um_get_option('rejected_email_on'), 'yesno', true ); ?>
Account Deactivated Email:	<?php echo $this->value( um_get_option('inactive_email_on'), 'yesno', true ); ?>
Account Deleted Email:		<?php echo $this->value( um_get_option('deletion_email_on'), 'yesno', true ); ?>
Password Reset Email:		<?php echo $this->value( um_get_option('resetpw_email_on'), 'yesno', true ); ?>
Password Changed Email:		<?php echo $this->value( um_get_option('changedpw_email_on'), 'yesno', true ); ?>


--- UM Total Users ---

<?php 

$result = count_users();
echo 'All Users('.$result['total_users'].")\n";
foreach($result['avail_roles'] as $role => $count){
    echo $role."(".$count.")\n";
}
?>


--- UM Roles ---

<?php 

	foreach( $ultimatemember->query->get_roles() as $role_id => $role ) {
		echo $role." ({$role_id})\n";
	}

?>


--- UM Custom Templates ---

<?php
// Show templates that have been copied to the theme's edd_templates dir

$dir = get_stylesheet_directory() . '/ultimate-member/templates/*.php';
if ( ! empty( $dir ) ){
	$found =  glob( $dir );
	if( ! empty( $found ) ){
		foreach ( glob( $dir ) as $file ) {
			echo "File: " . $file  . "\n";
		}
	}else {
		echo 'N/A'."\n";
	}
}
echo "\n\n";

$dir = get_stylesheet_directory() . '/ultimate-member/templates/emails/*.html';
echo "-- UM Email HTML Templates -- \n\n";

if ( ! empty( $dir ) ){
	$found =  glob( $dir );
	if( ! empty( $found ) ){
		foreach ( glob( $dir ) as $file ) {
			echo "File: ". $file  . "\n";
		}
	}else {
		echo 'N/A'."\n";
	}
}

?>

--- Web Server Configurations ---

PHP Version:              			<?php echo PHP_VERSION . "\n"; ?>
MySQL Version:            		<?php echo $wpdb->db_version() . "\n"; ?>
Web Server Info:          			<?php echo $_SERVER['SERVER_SOFTWARE'] . "\n"; ?>

--- PHP Configurations --

PHP Memory Limit:         		<?php echo ini_get( 'memory_limit' ) . "\n"; ?>
PHP Upload Max Size:      		<?php echo ini_get( 'upload_max_filesize' ) . "\n"; ?>
PHP Post Max Size:        		<?php echo ini_get( 'post_max_size' ) . "\n"; ?>
PHP Upload Max Filesize:  		<?php echo ini_get( 'upload_max_filesize' ) . "\n"; ?>
PHP Time Limit:           			<?php echo ini_get( 'max_execution_time' ) . "\n"; ?>
PHP Max Input Vars:       		<?php echo ini_get( 'max_input_vars' ) . "\n"; ?>
PHP Arg Separator:        		<?php echo ini_get( 'arg_separator.output' ) . "\n"; ?>
PHP Allow URL File Open:  		<?php echo ini_get( 'allow_url_fopen' ) ? "Yes\n" : "No\n"; ?>


--- Web Server Extensions/Modules ---

DISPLAY ERRORS:           		<?php echo ( ini_get( 'display_errors' ) ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'N/A'; ?><?php echo "\n"; ?>
FSOCKOPEN:                			<?php echo ( function_exists( 'fsockopen' ) ) ? 'Your server supports fsockopen.' : 'Your server does not support fsockopen.'; ?><?php echo "\n"; ?>
cURL:                     				<?php echo ( function_exists( 'curl_init' ) ) ? 'Your server supports cURL.' : 'Your server does not support cURL.'; ?><?php echo "\n"; ?>
SOAP Client:              			<?php echo ( class_exists( 'SoapClient' ) ) ? 'Your server has the SOAP Client enabled.' : 'Your server does not have the SOAP Client enabled.'; ?><?php echo "\n"; ?>
SUHOSIN:                  			<?php echo ( extension_loaded( 'suhosin' ) ) ? 'Your server has SUHOSIN installed.' : 'Your server does not have SUHOSIN installed.'; ?><?php echo "\n"; ?>
GD Library:                  			<?php echo ( extension_loaded( 'gd' ) && function_exists('gd_info') ) ? 'PHP GD library is installed on your web server.' : 'PHP GD library is NOT installed on your web server.'; ?><?php echo "\n"; ?>
Mail:                  			        <?php echo ( function_exists('mail') ) ? 'PHP mail function exist on your web server.' : 'PHP mail function doesn\'t exist on your web server.'; ?><?php echo "\n"; ?>


--- Session Configurations ---

Session:                  			<?php echo isset( $_SESSION ) ? 'Enabled' : 'Disabled'; ?><?php echo "\n"; ?>
Session Name:             			<?php echo esc_html( ini_get( 'session.name' ) ); ?><?php echo "\n"; ?>
Cookie Path:              			<?php echo esc_html( ini_get( 'session.cookie_path' ) ); ?><?php echo "\n"; ?>
Save Path:                			<?php echo esc_html( ini_get( 'session.save_path' ) ); ?><?php echo "\n"; ?>
Use Cookies:              			<?php echo ini_get( 'session.use_cookies' ) ? 'On' : 'Off'; ?><?php echo "\n"; ?>
Use Only Cookies:         		<?php echo ini_get( 'session.use_only_cookies' ) ? 'On' : 'Off'; ?><?php echo "\n"; ?>



--- WordPress Active Plugins ---

<?php
$plugins = get_plugins();
$active_plugins = get_option( 'active_plugins', array() );

foreach ( $plugins as $plugin_path => $plugin ) {
	// If the plugin isn't active, don't show it.
	if ( ! in_array( $plugin_path, $active_plugins ) )
		continue;

	echo $plugin['Name'] . ': ' . $plugin['Version'] ."\n";
}

if ( is_multisite() ) :
?>

--- WordPress Network Active Plugins ---

<?php
$plugins = wp_get_active_network_plugins();
$active_plugins = get_site_option( 'active_sitewide_plugins', array() );

foreach ( $plugins as $plugin_path ) {
	$plugin_base = plugin_basename( $plugin_path );

	// If the plugin isn't active, don't show it.
	if ( ! array_key_exists( $plugin_base, $active_plugins ) )
		continue;

	$plugin = get_plugin_data( $plugin_path );

	echo $plugin['Name'] . ' :' . $plugin['Version'] ."\n";
}

endif;
?>
<?php 
do_action( 'um_install_info_after' );
?>




### End Install Info ###</textarea>
			<p class="submit">
				<input type="hidden" name="um-addon-hook" value="download_install_info" />
				<?php submit_button( 'Download Install Info File', 'primary', 'download_install_info', false ); ?>
			</p>
		</form>		
		
		<?php } ?>

		<?php
		    
	}

	function value( $raw_value = '', $type = 'yesno', $default = '', $default_negate = '' ){

		if( $type == 'yesno' ){
			if( $default == $raw_value ){
				$raw_value = "Yes";
			}else{
				$raw_value = "No";
			}
		}else if( $type == 'onoff' ){
			if( $default == $raw_value ){
				$raw_value = "On";
			}else{
				$raw_value = "Off";
			}
		}

		return $raw_value."\n";
	}

}

$UM_ADDON_install_info = new UM_ADDON_install_info();