<?php
/**
 * Multisite network settings administration panel.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.0.0
 */

require_once( './admin.php' );

if ( ! is_multisite() )
	wp_die( __( 'Multisite support is not enabled.' ) );

if ( ! current_user_can( 'manage_network_options' ) )
    wp_die( __( 'You do not have permission to access this page.' ) );

$title = __( 'Network Options' );
$parent_file = 'ms-admin.php';

include( './admin-header.php' );

if (isset($_GET['updated'])) {
	?>
	<div id="message" class="updated"><p><?php _e( 'Options saved.' ) ?></p></div>
	<?php
}
?>

<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e( 'Network Options' ) ?></h2>
	<form method="post" action="ms-edit.php?action=siteoptions">
		<?php wp_nonce_field( 'siteoptions' ); ?>
		<h3><?php _e( 'Operational Settings' ); ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="site_name"><?php _e( 'Network Name' ) ?></label></th>
				<td>
					<input name="site_name" type="text" id="site_name" class="regular-text" value="<?php echo esc_attr( $current_site->site_name ) ?>" />
					<br />
					<?php _e( 'What you would like to call this website.' ) ?>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="admin_email"><?php _e( 'Network Admin Email' ) ?></label></th>
				<td>
					<input name="admin_email" type="text" id="admin_email" class="regular-text" value="<?php echo esc_attr( get_site_option('admin_email') ) ?>" />
					<br />
					<?php printf( __( 'Registration and support emails will come from this address. An address such as <code>support@%s</code> is recommended.' ), $current_site->domain ); ?>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e( 'Global Terms' ) ?></th>
				<td>
				<label><input type="radio" id="global_terms_enabled" name="global_terms_enabled" value="1"<?php checked( get_site_option( 'global_terms_enabled' ), 1 ) ?>/> <?php _e( 'Maintain a global list of terms from all sites across the network.' ); ?></label><br />
				<label><input type="radio" id="global_terms_enabled" name="global_terms_enabled" value="0"<?php checked( get_site_option( 'global_terms_enabled' ), 0 ) ?>/> <?php _e( 'Disabled' ); ?></label></td>
			</tr>
		</table>
		<h3><?php _e( 'Dashboard Settings' ); ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="dashboard_blog"><?php _e( 'Dashboard Site' ) ?></label></th>
				<td>
					<?php
					if ( $dashboard_blog = get_site_option( 'dashboard_blog' ) ) {
						$details = get_blog_details( $dashboard_blog );
						$blogname = untrailingslashit( sanitize_user( str_replace( '.', '', str_replace( $current_site->domain . $current_site->path, '', $details->domain . $details->path ) ) ) );
					} else {
						$blogname = '';
					}?>
					<input name="dashboard_blog_orig" type="hidden" id="dashboard_blog_orig" value="<?php echo esc_attr( $blogname ); ?>" />
					<input name="dashboard_blog" type="text" id="dashboard_blog" value="<?php echo esc_attr( $blogname ); ?>" class="regular-text" />
					<br />
					<?php _e( "Site path ('dashboard', 'control', 'manager', etc) or blog id.<br />New users are added to this site as the user role defined below if they don't have a site. Leave blank for the main site. Users with the subscriber role on old site will be moved to the new site if changed. The new site will be created if it does not exist." ); ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="default_user_role"><?php _e( 'Dashboard User Default Role' ) ?></label></th>
				<td>
					<select name="default_user_role" id="default_user_role"><?php
					wp_dropdown_roles( get_site_option( 'default_user_role', 'subscriber' ) );
					?>
					</select>
					<br />
					<?php _e( "The default role for new users on the Dashboard site. 'Subscriber' or 'Contributor' roles are recommended." ); ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="admin_notice_feed"><?php _e( 'Admin Notice Feed' ) ?></label></th>
				<td><input name="admin_notice_feed" class="large-text" type="text" id="admin_notice_feed" value="<?php echo esc_attr( get_site_option( 'admin_notice_feed' ) ) ?>" size="80" /><br />
				<?php _e( 'Display the latest post from this RSS or Atom feed on all site dashboards. Leave blank to disable.' ); ?><br />
				
				<?php if ( get_site_option( 'admin_notice_feed' ) != get_home_url( $current_site->id, 'feed/' ) )
					echo __( 'A good one to use would be the feed from your main site: ' ) . esc_url( get_home_url( $current_site->id, 'feed/' ) ) ?></td>
			</tr>
		</table>
		<h3><?php _e( 'Registration Settings' ); ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e( 'Allow new registrations' ) ?></th>
				<?php
				if ( !get_site_option( 'registration' ) )
					update_site_option( 'registration', 'none' );
				$reg = get_site_option( 'registration' );
				?>
				<td>
					<label><input name="registration" type="radio" id="registration1" value="none"<?php checked( $reg, 'none') ?> /> <?php _e( 'Registration is disabled.' ); ?></label><br />
					<label><input name="registration" type="radio" id="registration2" value="user"<?php checked( $reg, 'user') ?> /> <?php _e( 'User accounts may be registered.' ); ?></label><br />
					<label><input name="registration" type="radio" id="registration3" value="blog"<?php checked( $reg, 'blog') ?> /> <?php _e( 'Logged in users may register new sites.' ); ?></label><br />
					<label><input name="registration" type="radio" id="registration4" value="all"<?php checked( $reg, 'all') ?> /> <?php _e( 'Both sites and user accounts can be registered.' ); ?></label><br />
					<p><?php _e( 'Disable or enable registration and who or what can be registered. (Default is disabled.)' ); ?></p>
					<?php if ( is_subdomain_install() ) {
						echo '<p>' . __( 'If registration is disabled, please set <code>NOBLOGREDIRECT</code> in <code>wp-config.php</code> to a url you will redirect visitors to if they visit a non-existent site.' ) . '</p>';
					} ?>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e( 'Registration notification' ) ?></th>
				<?php
				if ( !get_site_option( 'registrationnotification' ) )
					update_site_option( 'registrationnotification', 'yes' );
				?>
				<td>
					<label><input name="registrationnotification" type="checkbox" id="registrationnotification" value="yes"<?php checked( get_site_option( 'registrationnotification' ), 'yes' ) ?> /> <?php _e( 'Send the network admin an email notification every time someone registers a site or user account.' ) ?></label>
				</td>
			</tr>

			<tr valign="top" id="addnewusers">
				<th scope="row"><?php _e( 'Add New Users' ) ?></th>
				<td>
					<label><input name="add_new_users" type="checkbox" id="add_new_users" value="1"<?php checked( get_site_option( 'add_new_users' ) ) ?> /> <?php _e( 'Allow site administrators to add new users to their site via the "Users->Add New" page.' ); ?></label>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="illegal_names"><?php _e( 'Banned Names' ) ?></label></th>
				<td>
					<input name="illegal_names" type="text" id="illegal_names" class="large-text" value="<?php echo esc_attr( implode( " ", get_site_option( 'illegal_names' ) ) ); ?>" size="45" />
					<br />
					<?php _e( 'Users are not allowed to register these sites. Separate names by spaces.' ) ?>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="limited_email_domains"><?php _e( 'Limited Email Registrations' ) ?></label></th>
				<td>
					<?php $limited_email_domains = get_site_option( 'limited_email_domains' );
					$limited_email_domains = str_replace( ' ', "\n", $limited_email_domains ); ?>
					<textarea name="limited_email_domains" id="limited_email_domains" cols="45" rows="5"><?php echo wp_htmledit_pre( $limited_email_domains == '' ? '' : implode( "\n", (array) $limited_email_domains ) ); ?></textarea>
					<br />
					<?php _e( 'If you want to limit site registrations to certain domains. Enter one domain per line.' ) ?>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="banned_email_domains"><?php _e('Banned Email Domains') ?></label></th>
				<td>
					<textarea name="banned_email_domains" id="banned_email_domains" cols="45" rows="5"><?php echo wp_htmledit_pre( get_site_option( 'banned_email_domains' ) == '' ? '' : implode( "\n", (array) get_site_option( 'banned_email_domains' ) ) ); ?></textarea>
					<br />
					<?php _e('If you want to ban domains from site registrations. Enter one domain per line.') ?>
				</td>
			</tr>

		</table>
		<h3><?php _e('New Site Settings'); ?></h3>
		<table class="form-table">

			<tr valign="top">
				<th scope="row"><label for="welcome_email"><?php _e( 'Welcome Email' ) ?></label></th>
				<td>
					<textarea name="welcome_email" id="welcome_email" rows="5" cols="45" class="large-text"><?php echo stripslashes( get_site_option( 'welcome_email' ) ) ?></textarea>
					<br />
					<?php _e( 'The welcome email sent to new site owners.' ) ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="welcome_user_email"><?php _e( 'Welcome User Email' ) ?></label></th>
				<td>
			    	<textarea name="welcome_user_email" id="welcome_user_email" rows="5" cols="45" class="large-text"><?php echo stripslashes( get_site_option( 'welcome_user_email' ) ) ?></textarea>
					<br />
					<?php _e( 'The welcome email sent to new users.' ) ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="first_post"><?php _e( 'First Post' ) ?></label></th>
				<td>
					<textarea name="first_post" id="first_post" rows="5" cols="45" class="large-text"><?php echo stripslashes( get_site_option( 'first_post' ) ) ?></textarea>
					<br />
					<?php _e( 'The first post on a new site.' ) ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="first_page"><?php _e( 'First Page' ) ?></label></th>
				<td>
					<textarea name="first_page" id="first_page" rows="5" cols="45" class="large-text"><?php echo stripslashes( get_site_option('first_page') ) ?></textarea>
					<br />
					<?php _e( 'The first page on a new site.' ) ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="first_comment"><?php _e( 'First Comment' ) ?></label></th>
				<td>
					<textarea name="first_comment" id="first_comment" rows="5" cols="45" class="large-text"><?php echo stripslashes( get_site_option('first_comment') ) ?></textarea>
					<br />
					<?php _e( 'The first comment on a new site.' ) ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="first_comment_author"><?php _e( 'First Comment Author' ) ?></label></th>
				<td>
					<input type="text" size="40" name="first_comment_author" id="first_comment_author" value="<?php echo get_site_option('first_comment_author') ?>" />
					<br />
					<?php _e( 'The author of the first comment on a new site.' ) ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="first_comment_url"><?php _e( 'First Comment URL' ) ?></label></th>
				<td>
					<input type="text" size="40" name="first_comment_url" id="first_comment_url" value="<?php echo esc_attr( get_site_option( 'first_comment_url' ) ) ?>" />
					<br />
					<?php _e( 'The URL for the first comment on a new site.' ) ?>
				</td>
			</tr>
		</table>
		<h3><?php _e( 'Upload Settings' ); ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e( 'Media upload buttons' ) ?></th>
				<?php $mu_media_buttons = get_site_option( 'mu_media_buttons', array() ); ?>
				<td><label><input type="checkbox" id="mu_media_buttons_image" name="mu_media_buttons[image]" value="1"<?php checked( ! empty( $mu_media_buttons['image'] ) ) ?>/> <?php _e( 'Images' ); ?></label><br />
				<label><input type="checkbox" id="mu_media_buttons_video" name="mu_media_buttons[video]" value="1"<?php checked( ! empty( $mu_media_buttons['video'] ) ) ?>/> <?php _e( 'Videos' ); ?></label><br />
				<label><input type="checkbox" id="mu_media_buttons_audio" name="mu_media_buttons[audio]" value="1"<?php checked( ! empty( $mu_media_buttons['audio'] ) ) ?>/> <?php _e( 'Music' ); ?></label><br />
				<?php _e( 'The media upload buttons to display on the "Write Post" page. Make sure you update the allowed upload file types below as well.' ); ?></td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e( 'Site upload space' ) ?></th>
				<td>
				<label><input type="checkbox" id="upload_space_check_disabled" name="upload_space_check_disabled" value="0"<?php checked( get_site_option( 'upload_space_check_disabled' ), 0 ) ?>/> <?php printf( __( 'Limit total size of files uploaded to %s MB' ), '<input name="blog_upload_space" type="text" id="blog_upload_space" value="' . esc_attr( get_site_option('blog_upload_space', 10) ) . '" size="3" />' ); ?></label><br />
			</tr>

			<tr valign="top">
				<th scope="row"><label for="upload_filetypes"><?php _e( 'Upload file types' ) ?></label></th>
				<td><input name="upload_filetypes" type="text" id="upload_filetypes" class="large-text" value="<?php echo esc_attr( get_site_option('upload_filetypes', 'jpg jpeg png gif') ) ?>" size="45" /></td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="fileupload_maxk"><?php _e( 'Max upload file size' ) ?></label></th>
				<td><?php printf( _x( '%s KB', 'File size in kilobytes' ), '<input name="fileupload_maxk" type="text" id="fileupload_maxk" value="' . esc_attr( get_site_option( 'fileupload_maxk', 300 ) ) . '" size="5" />' ); ?></td>
			</tr>
		</table>

<?php
		$languages = get_available_languages();
		if ( ! empty( $languages ) ) {
			$lang = get_site_option( 'WPLANG' );
?>
		<h3><?php _e( 'Network Wide Settings' ); ?></h3>
		<div class="updated inline"><p><strong><?php _e( 'Notice:' ); ?></strong> <?php _e( 'These settings may be overridden by site owners.' ); ?></p></div>
		<table class="form-table">
			<?php
				?>
				<tr valign="top">
					<th><label for="WPLANG"><?php _e( 'Default Language' ) ?></label></th>
					<td>
						<select name="WPLANG" id="WPLANG">
							<?php mu_dropdown_languages( $languages, get_site_option( 'WPLANG' ) ); ?>
						</select>
					</td>
				</tr>
		</table>
<?php
		} // languages
?>

		<h3><?php _e( 'Menu Settings' ); ?></h3>
		<table id="menu" class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e( 'Enable administration menus' ); ?></th>
				<td>
			<?php
			$menu_perms = get_site_option( 'menu_items' );
			$menu_items = apply_filters( 'mu_menu_items', array( 'plugins' => __( 'Plugins' ) ) );
			foreach ( (array) $menu_items as $key => $val ) {
				echo "<label><input type='checkbox' name='menu_items[" . $key . "]' value='1'" .  ( isset( $menu_perms[$key] ) ? checked( $menu_perms[$key], '1', false ) : '' ) . " /> " . esc_html( $val ) . "</label><br/>";
			}
			?>
				</td>
			</tr>
		</table>

		<?php do_action( 'wpmu_options' ); // Add more options here ?>

		<p class="submit"><input type="submit" class="button-primary" name="Submit" value="<?php esc_attr_e( 'Save Changes' ) ?>" /></p>
	</form>
</div>

<?php include( './admin-footer.php' ); ?>
