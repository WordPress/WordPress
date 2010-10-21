<?php
/**
 * Multisite sites administration panel.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.0.0
 */

require_once( './admin.php' );

if ( ! is_multisite() )
	wp_die( __( 'Multisite support is not enabled.' ) );

$wp_list_table = get_list_table('sites');
$wp_list_table->check_permissions();

$title = __( 'Sites' );
$parent_file = 'sites.php';

add_screen_option( 'per_page', array('label' => _x( 'Sites', 'sites per page (screen options)' )) );

if ( isset( $_REQUEST['action'] ) && 'editblog' == $_REQUEST['action'] ) {
	add_contextual_help($current_screen,
		'<p>' . __('This extensive list of options has five modules: Site Info, Site Options, allowing Site Themes for this given site, changing user roles and passwords for that site, adding a new user, and Miscellaneous Site Actions (upload size limits).') . '</p>' .
		'<p>' . __('Note that some fields in Site Options are grayed out and say Serialized Data. These are stored values in the database which you cannot change from here.') . '</p>' .
		'<p><strong>' . __('For more information:') . '</strong></p>' .
		'<p>' . __('<a href="http://codex.wordpress.org/Super_Admin_Sites_Edit_Site" target="_blank">Documentation on Editing Sites</a>') . '</p>' .
		'<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
	);
} else {
	add_contextual_help($current_screen,
		'<p>' . __('Add New takes you to the Add New Site screen. You can search for a site by Name, ID number, or IP address. Screen Options allows you to choose how many sites to display on one page.') . '</p>' .
		'<p>' . __('This is the main table of all sites on this network. Switch between list and excerpt views by using the icons above the right side of the table.') . '</p>' .
		'<p>' . __('Hovering over each site reveals seven options (three for the primary site):') . '</p>' .
		'<ul><li>' . __('An Edit link to a separate Edit Site screen.') . '</li>' .
		'<li>' . __('Dashboard to the Dashboard for that site.') . '</li>' .
		'<li>' . __('Deactivate, Archive, and Spam which lead to confirmation screens. These actions can be reversed later.') . '</li>' .
		'<li>' . __('Delete which is a permanent action after the confirmations screen.') . '</li>' .
		'<li>' . __('Visit to go to the frontend site live.') . '</li></ul>' .
		'<p>' . __('The site ID is used internally, and is not shown on the front end of the site or to users/viewers.') . '</p>' .
		'<p>' . __('Clicking on bold settings can re-sort this table. The upper right icons switch between list and excerpt views.') . '</p>' .
		'<p>' . __('If the admin email for the new site does not exist in the database, a new user will also be created.') . '</p>' .
		'<p><strong>' . __('For more information:') . '</strong></p>' .
		'<p>' . __('<a href="http://codex.wordpress.org/Super_Admin_Sites_SubPanel" target="_blank">Documentation on Sites</a>') . '</p>' .
		'<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
	);
}

$id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;

$msg = '';
if ( isset( $_REQUEST['updated'] ) && $_REQUEST['updated'] == 'true' && ! empty( $_REQUEST['action'] ) ) {
	switch ( $_REQUEST['action'] ) {
		case 'all_notspam':
			$msg = __( 'Sites removed from spam.' );
		break;
		case 'all_spam':
			$msg = __( 'Sites marked as spam.' );
		break;
		case 'all_delete':
			$msg = __( 'Sites deleted.' );
		break;
		case 'delete':
			$msg = __( 'Site deleted.' );
		break;
		case 'archive':
			$msg = __( 'Site archived.' );
		break;
		case 'unarchive':
			$msg = __( 'Site unarchived.' );
		break;
		case 'activate':
			$msg = __( 'Site activated.' );
		break;
		case 'deactivate':
			$msg = __( 'Site deactivated.' );
		break;
		case 'unspam':
			$msg = __( 'Site removed from spam.' );
		break;
		case 'spam':
			$msg = __( 'Site marked as spam.' );
		break;
		default:
			$msg = __( 'Settings saved.' );
		break;
	}
	if ( $msg )
		$msg = '<div class="updated" id="message"><p>' . $msg . '</p></div>';
}

$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'list';

switch ( $action ) {
	// Edit site
	case 'editblog':
		$blog_prefix = $wpdb->get_blog_prefix( $id );
		$options = $wpdb->get_results( "SELECT * FROM {$blog_prefix}options WHERE option_name NOT LIKE '\_%' AND option_name NOT LIKE '%user_roles'" );
		$details = get_blog_details( $id );
		if ( $details->site_id != $wpdb->siteid )
			wp_die( __( 'You do not have permission to access this page.' ) );

		$editblog_roles = get_blog_option( $id, "{$blog_prefix}user_roles" );
		$is_main_site = is_main_site( $id );

		require_once( '../admin-header.php' );
		?>
		<div class="wrap">
		<?php screen_icon('index'); ?>
		<h2><?php _e( 'Edit Site' ); ?> - <a href="<?php echo esc_url( get_home_url( $id ) ); ?>"><?php echo esc_url( get_home_url( $id ) ); ?></a></h2>
		<?php echo $msg; ?>
		<form method="post" action="edit.php?action=updateblog">
			<?php wp_nonce_field( 'editblog' ); ?>
			<input type="hidden" name="id" value="<?php echo esc_attr( $id ) ?>" />
			<div class="metabox-holder" style="width:49%;float:left;">
				<div id="blogedit_bloginfo" class="postbox">
				<h3 class="hndle"><span><?php _e( 'Site info (wp_blogs)' ); ?></span></h3>
				<div class="inside">
					<table class="form-table">
						<tr class="form-field form-required">
							<th scope="row"><?php _e( 'Domain' ) ?></th>
							<?php
							$protocol = is_ssl() ? 'https://' : 'http://';
							if ( $is_main_site ) { ?>
							<td><code><?php echo $protocol; echo esc_attr( $details->domain ) ?></code></td>
							<?php } else { ?>
							<td><?php echo $protocol; ?><input name="blog[domain]" type="text" id="domain" value="<?php echo esc_attr( $details->domain ) ?>" size="33" /></td>
							<?php } ?>
						</tr>
						<tr class="form-field form-required">
							<th scope="row"><?php _e( 'Path' ) ?></th>
							<?php if ( $is_main_site ) { ?>
							<td><code><?php echo esc_attr( $details->path ) ?></code></td>
							<?php } else { ?>
							<td><input name="blog[path]" type="text" id="path" value="<?php echo esc_attr( $details->path ) ?>" size="40" style='margin-bottom:5px;' />
							<br /><input type="checkbox" style="width:20px;" name="update_home_url" value="update" <?php if ( get_blog_option( $id, 'siteurl' ) == untrailingslashit( get_blogaddress_by_id ($id ) ) || get_blog_option( $id, 'home' ) == untrailingslashit( get_blogaddress_by_id( $id ) ) ) echo 'checked="checked"'; ?> /> <?php _e( 'Update <code>siteurl</code> and <code>home</code> as well.' ); ?></td>
							<?php } ?>
						</tr>
						<tr class="form-field">
							<th scope="row"><?php _ex( 'Registered', 'site' ) ?></th>
							<td><input name="blog[registered]" type="text" id="blog_registered" value="<?php echo esc_attr( $details->registered ) ?>" size="40" /></td>
						</tr>
						<tr class="form-field">
							<th scope="row"><?php _e('Last Updated') ?></th>
							<td><input name="blog[last_updated]" type="text" id="blog_last_updated" value="<?php echo esc_attr( $details->last_updated ) ?>" size="40" /></td>
						</tr>
						<?php
						$radio_fields = array( 'public' => __( 'Public' ) );
						if ( ! $is_main_site ) {
							$radio_fields['archived'] = __( 'Archived' );
							$radio_fields['spam']     = _x( 'Spam', 'site' );
							$radio_fields['deleted']  = __( 'Deleted' );
						}
						$radio_fields['mature'] = __( 'Mature' );
						foreach ( $radio_fields as $field_key => $field_label ) {
						?>
						<tr>
							<th scope="row"><?php echo $field_label; ?></th>
							<td>
								<input type="radio" name="blog[<?php echo $field_key; ?>]" id="blog_<?php echo $field_key; ?>_1" value="1"<?php checked( $details->$field_key, 1 ); ?> />
								<label for="blog_<?php echo $field_key; ?>_1"><?php _e('Yes'); ?></label>
								<input type="radio" name="blog[<?php echo $field_key; ?>]" id="blog_<?php echo $field_key; ?>_0" value="0"<?php checked( $details->$field_key, 0 ); ?> />
								<label for="blog_<?php echo $field_key; ?>_0"><?php _e('No'); ?></label>
							</td>
						</tr>
						<?php } ?>
					</table>
					<p class="submit" style="text-align:center;"><input type="submit" name="Submit" value="<?php esc_attr_e( 'Update Options' ) ?>" /></p>
				</div>
				</div>

				<div id="blogedit_blogoptions" class="postbox" >
				<h3 class="hndle"><span><?php printf( __( 'Site options (%soptions)' ), $blog_prefix ); ?></span></h3>
				<div class="inside">
					<table class="form-table">
						<?php
						$editblog_default_role = 'subscriber';
						foreach ( $options as $option ) {
							if ( $option->option_name == 'default_role' )
								$editblog_default_role = $option->option_value;
							$disabled = false;
							$class = 'all-options';
							if ( is_serialized( $option->option_value ) ) {
								if ( is_serialized_string( $option->option_value ) ) {
									$option->option_value = esc_html( maybe_unserialize( $option->option_value ), 'single' );
								} else {
									$option->option_value = 'SERIALIZED DATA';
									$disabled = true;
									$class = 'all-options disabled';
								}
							}
							if ( strpos( $option->option_value, "\n" ) !== false ) {
							?>
								<tr class="form-field">
									<th scope="row"><?php echo ucwords( str_replace( "_", " ", $option->option_name ) ) ?></th>
									<td><textarea class="<?php echo $class; ?>" rows="5" cols="40" name="option[<?php echo esc_attr( $option->option_name ) ?>]" id="<?php echo esc_attr( $option->option_name ) ?>"<?php disabled( $disabled ) ?>><?php echo wp_htmledit_pre( $option->option_value ) ?></textarea></td>
								</tr>
							<?php
							} else {
							?>
								<tr class="form-field">
									<th scope="row"><?php echo esc_html( ucwords( str_replace( "_", " ", $option->option_name ) ) ); ?></th>
									<?php if ( $is_main_site && in_array( $option->option_name, array( 'siteurl', 'home' ) ) ) { ?>
									<td><code><?php echo esc_html( $option->option_value ) ?></code></td>
									<?php } else { ?>
									<td><input class="<?php echo $class; ?>" name="option[<?php echo esc_attr( $option->option_name ) ?>]" type="text" id="<?php echo esc_attr( $option->option_name ) ?>" value="<?php echo esc_attr( $option->option_value ) ?>" size="40" <?php disabled( $disabled ) ?> /></td>
									<?php } ?>
								</tr>
							<?php
							}
						} // End foreach
						?>
					</table>
					<p class="submit" style="text-align:center;"><input type="submit" name="Submit" value="<?php esc_attr_e( 'Update Options' ) ?>" /></p>
				</div>
				</div>
			</div>

			<div class="metabox-holder" style="width:49%;float:right;">
				<?php
				// Site Themes
				$themes = get_themes();
				$blog_allowed_themes = wpmu_get_blog_allowedthemes( $id );
				$allowed_themes = get_site_option( 'allowedthemes' );

				if ( ! $allowed_themes )
					$allowed_themes = array_keys( $themes );

				$out = '';
				foreach ( $themes as $key => $theme ) {
					$theme_key = esc_html( $theme['Stylesheet'] );
					if ( ! isset( $allowed_themes[$theme_key] ) ) {
						$checked = isset( $blog_allowed_themes[ $theme_key ] ) ? 'checked="checked"' : '';
						$out .= '<tr class="form-field form-required">
								<th title="' . esc_attr( $theme["Description"] ).'" scope="row">' . esc_html( $key ) . '</th>
								<td><label><input name="theme[' . esc_attr( $theme_key ) . ']" type="checkbox" style="width:20px;" value="on" '.$checked.'/> ' . __( 'Active' ) . '</label></td>
							</tr>';
					}
				}

				if ( $out != '' ) {
				?>
				<div id="blogedit_blogthemes" class="postbox">
				<h3 class="hndle"><span><?php esc_html_e( 'Site Themes' ); ?></span></h3>
				<div class="inside">
					<p class="description"><?php _e( 'Activate the themename of an existing theme and hit "Update Options" to allow the theme for this site.' ) ?></p>
					<table class="form-table">
						<?php echo $out; ?>
					</table>
					<p class="submit" style="text-align:center;"><input type="submit" name="Submit" value="<?php esc_attr_e( 'Update Options' ) ?>" /></p>
				</div></div>
				<?php }

				// Site users
				$blogusers = get_users( array( 'blog_id' => $id, 'number' => 20 ) );
				if ( is_array( $blogusers ) ) {
					echo '<div id="blogedit_blogusers" class="postbox"><h3 class="hndle"><span>' . __( 'Site Users' ) . '</span></h3><div class="inside">';
					echo '<table class="form-table">';
					echo "<tr><th>" . __( 'User' ) . "</th><th>" . __( 'Role' ) . "</th><th>" . __( 'Password' ) . "</th><th>" . __( 'Remove' ) . "</th></tr>";
					$user_count = 0;
					foreach ( $blogusers as $user_id => $user_object ) {
						$user_count++;
						$existing_role = reset( $user_object->roles );

						echo '<tr><td><a href="user-edit.php?user_id=' . $user_id . '">' . $user_object->user_login . '</a></td>';
						if ( $user_id != $current_user->data->ID ) {
							?>
							<td>
								<select name="role[<?php echo $user_id ?>]" id="new_role_1"><?php
									foreach ( $editblog_roles as $role => $role_assoc ){
										$name = translate_user_role( $role_assoc['name'] );
										echo '<option ' . selected( $role, $existing_role, false ) . ' value="' . esc_attr( $role ) . '">' . esc_html( $name ) . '</option>';
									}
									?>
								</select>
							</td>
							<td>
								<input type="text" name="user_password[<?php echo esc_attr( $user_id ) ?>]" />
							</td>
							<?php
							echo '<td><input title="' . __( 'Click to remove user' ) . '" type="checkbox" name="blogusers[' . esc_attr( $user_id ) . ']" /></td>';
						} else {
							echo "<td><strong>" . __ ( 'N/A' ) . "</strong></td><td><strong>" . __ ( 'N/A' ) . "</strong></td><td><strong>" . __( 'N/A' ) . "</strong></td>";
						}
						echo '</tr>';
					}
					echo "</table>";
					echo '<p class="submit" style="text-align:center;"><input type="submit" name="Submit" value="' . esc_attr__( 'Update Options' ) . '" /></p>';
					if ( 20 == $user_count )
						echo '<p>' . sprintf( __('First 20 users shown. <a href="%s">Manage all users</a>.'), get_admin_url($id, 'users.php') ) . '</p>';
					echo "</div></div>";
				}
				?>

				<div id="blogedit_blogadduser" class="postbox">
				<h3 class="hndle"><span><?php _e( 'Add a new user' ); ?></span></h3>
				<div class="inside">
					<p class="description"><?php _e( 'Enter the username of an existing user and hit &#8220;Update Options&#8221; to add the user.' ) ?></p>
					<table class="form-table">
							<tr>
								<th scope="row"><?php _e( 'User&nbsp;Login:' ) ?></th>
								<td><input type="text" name="newuser" id="newuser" /></td>
							</tr>
							<tr>
								<th scope="row"><?php _e( 'Role:' ) ?></th>
								<td>
									<select name="new_role" id="new_role_0">
									<?php
									reset( $editblog_roles );
									foreach ( $editblog_roles as $role => $role_assoc ){
										$name = translate_user_role( $role_assoc['name'] );
										$selected = ( $role == $editblog_default_role ) ? 'selected="selected"' : '';
										echo '<option ' . $selected . ' value="' . esc_attr( $role ) . '">' . esc_html( $name ) . '</option>';
									}
									?>
									</select>
								</td>
							</tr>
						</table>
					<p class="submit" style="text-align:center;"><input type="submit" name="Submit" value="<?php esc_attr_e( 'Update Options' ) ?>" /></p>
				</div>
				</div>

				<div id="blogedit_miscoptions" class="postbox">
				<h3 class="hndle"><span><?php _e( 'Misc Site Actions' ) ?></span></h3>
				<div class="inside">
					<table class="form-table">
							<?php do_action( 'wpmueditblogaction', $id ); ?>
					</table>
					<p class="submit" style="text-align:center;"><input type="submit" name="Submit" value="<?php esc_attr_e( 'Update Options' ) ?>" /></p>
				</div>
				</div>
			</div>

			<div style="clear:both;"></div>
		</form>
		</div>
		<?php
	break;

	// List sites
	case 'list':
	default:
		$wp_list_table->prepare_items();

		require_once( '../admin-header.php' );
		?>

		<div class="wrap">
		<?php screen_icon('index'); ?>
		<h2><?php _e('Sites') ?>
		<?php echo $msg; ?>
		<a href="<?php echo network_admin_url('site-new.php'); ?>" class="button add-new-h2"><?php echo esc_html_x( 'Add New', 'sites' ); ?></a>
		<?php if ( isset( $_REQUEST['s'] ) && $_REQUEST['s'] ) {
			printf( '<span class="subtitle">' . __( 'Search results for &#8220;%s&#8221;' ) . '</span>', esc_html( $s ) );
		} ?>
		</h2>

		<form action="" method="get" id="ms-search">
		<p class="search-box">
		<input type="hidden" name="action" value="blogs" />
		<input type="text" name="s" value="<?php echo esc_attr( $s ); ?>" />
		<input type="submit" class="button" value="<?php esc_attr_e( 'Search Site by' ) ?>" />
		<select name="searchaction">
			<option value="name" selected="selected"><?php _e( 'Name' ); ?></option>
			<option value="id"><?php _e( 'ID' ); ?></option>
			<option value="ip"><?php _e( 'IP address' ); ?></option>
		</select>
		</p>
		</form>

		<form id="form-site-list" action="edit.php?action=allblogs" method="post">
			<?php $wp_list_table->display(); ?>
		</form>
		</div>
		<?php
	break;
} // end switch( $action )

require_once( '../admin-footer.php' ); ?>
