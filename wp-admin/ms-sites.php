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

if ( ! current_user_can( 'manage_sites' ) )
	wp_die( __( 'You do not have permission to access this page.' ) );

$title = __( 'Sites' );
$parent_file = 'ms-admin.php';

wp_enqueue_script( 'admin-forms' );

require_once( './admin-header.php' );

$id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;

if ( isset( $_GET['updated'] ) && $_GET['updated'] == 'true' && ! empty( $_GET['action'] ) ) {
	?>
	<div id="message" class="updated"><p>
		<?php
		switch ( $_GET['action'] ) {
			case 'all_notspam':
				_e( 'Sites removed from spam.' );
			break;
			case 'all_spam':
				_e( 'Sites marked as spam.' );
			break;
			case 'all_delete':
				_e( 'Sites deleted.' );
			break;
			case 'delete':
				_e( 'Site deleted.' );
			break;
			case 'add-blog':
				_e( 'Site added.' );
			break;
			case 'archive':
				_e( 'Site archived.' );
			break;
			case 'unarchive':
				_e( 'Site unarchived.' );
			break;
			case 'activate':
				_e( 'Site activated.' );
			break;
			case 'deactivate':
				_e( 'Site deactivated.' );
			break;
			case 'unspam':
				_e( 'Site removed from spam.' );
			break;
			case 'spam':
				_e( 'Site marked as spam.' );
			break;
			default:
				_e( 'Settings saved.' );
			break;
		}
		?>
	</p></div>
	<?php
}

$action = isset( $_GET['action'] ) ? $_GET['action'] : 'list';

switch ( $action ) {
	// Edit site
	case 'editblog':
		$blog_prefix = $wpdb->get_blog_prefix( $id );
		$options = $wpdb->get_results( "SELECT * FROM {$blog_prefix}options WHERE option_name NOT LIKE '\_%' AND option_name NOT LIKE '%user_roles'" );
		$details = get_blog_details( $id );
		$editblog_roles = get_blog_option( $id, "{$blog_prefix}user_roles" );
		$is_main_site = is_main_site( $id );
		?>
		<div class="wrap">
		<?php screen_icon(); ?>
		<h2><?php _e( 'Edit Site' ); ?> - <a href="<?php echo esc_url( get_home_url( $id ) ); ?>"><?php echo esc_url( get_home_url( $id ) ); ?></a></h2>
		<form method="post" action="ms-edit.php?action=updateblog">
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
							<th scope="row"><?php _e( 'Registered') ?></th>
							<td><input name="blog[registered]" type="text" id="blog_registered" value="<?php echo esc_attr( $details->registered ) ?>" size="40" /></td>
						</tr>
						<tr class="form-field">
							<th scope="row"><?php _e('Last Updated') ?></th>
							<td><input name="blog[last_updated]" type="text" id="blog_last_updated" value="<?php echo esc_attr( $details->last_updated ) ?>" size="40" /></td>
						</tr>
						<tr>
							<th scope="row"><?php _e( 'Site Settings' ); ?></th>
							<td>
						<?php
						$checked_fields = array( 'public' => __( 'Public' ) );
						if ( ! $is_main_site ) {
							$checked_fields['archived'] = __( 'Archived' );
							$checked_fields['spam']     = __( 'Spam' );
							$checked_fields['deleted']  = __( 'Deleted' );
						}
						$checked_fields['mature'] = __( 'Mature' );
						foreach ( $checked_fields as $field_key => $field_label ) {
						?>
								<input type="checkbox" name="blog[<?php echo $field_key; ?>]" id="blog_<?php echo $field_key; ?>" value="1"<?php checked( $details->$field_key ); ?> />
								<label for="blog_<?php echo $field_key; ?>"><?php echo $field_label; ?></label><br/>
						<?php } ?>
							</td>
						</tr>
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
									<td><textarea class="<?php echo $class; ?>" rows="5" cols="40" name="option[<?php echo esc_attr( $option->option_name ) ?>]" id="<?php echo esc_attr( $option->option_name ) ?>"<?php disabled( $disabled ) ?>><?php wp_htmledit_pre( $option->option_value ) ?></textarea></td>
								</tr>
							<?php
							} else {
							?>
								<tr class="form-field">
									<th scope="row"><?php esc_html_e( ucwords( str_replace( "_", " ", $option->option_name ) ) ); ?></th>
									<?php if ( $is_main_site && in_array( $option->option_name, array( 'siteurl', 'home' ) ) ) { ?>
									<td><code><?php esc_html_e( $option->option_value ) ?></code></td>
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
				$blogusers = get_users_of_blog( $id );
				if ( is_array( $blogusers ) ) {
					echo '<div id="blogedit_blogusers" class="postbox"><h3 class="hndle"><span>' . __( 'Site Users' ) . '</span></h3><div class="inside">';
					echo '<table class="form-table">';
					echo "<tr><th>" . __( 'User' ) . "</th><th>" . __( 'Role' ) . "</th><th>" . __( 'Password' ) . "</th><th>" . __( 'Remove' ) . "</th></tr>";
					reset( $blogusers );
					foreach ( (array) $blogusers as $key => $val ) {
						if ( isset( $val->meta_value ) && ! $val->meta_value )
							continue;
						$t = @unserialize( $val->meta_value );
						if ( is_array( $t ) ) {
							reset( $t );
							$existing_role = key( $t );
						}
						echo '<tr><td><a href="user-edit.php?user_id=' . $val->user_id . '">' . $val->user_login . '</a></td>';
						if ( $val->user_id != $current_user->data->ID ) {
							?>
							<td>
								<select name="role[<?php echo $val->user_id ?>]" id="new_role"><?php
									foreach ( $editblog_roles as $role => $role_assoc ){
										$name = translate_user_role( $role_assoc['name'] );
										echo '<option ' . selected( $role, $existing_role ) . ' value="' . esc_attr( $role ) . '">' . esc_html( $name ) . '</option>';
									}
									?>
								</select>
							</td>
							<td>
								<input type="text" name="user_password[<?php echo esc_attr( $val->user_id ) ?>]" />
							</td>
							<?php
							echo '<td><input title="' . __( 'Click to remove user' ) . '" type="checkbox" name="blogusers[' . esc_attr( $val->user_id ) . ']" /></td>';
						} else {
							echo "<td><strong>" . __ ( 'N/A' ) . "</strong></td><td><strong>" . __ ( 'N/A' ) . "</strong></td><td><strong>" . __( 'N/A' ) . "</strong></td>";
						}
						echo '</tr>';
					}
					echo "</table>";
					echo '<p class="submit" style="text-align:center;"><input type="submit" name="Submit" value="' . esc_attr__( 'Update Options' ) . '" /></p>';
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
									<select name="new_role" id="new_role">
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
		$pagenum = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 0;
		if ( empty($pagenum) )
			$pagenum = 1;

		$per_page = (int) get_user_option( 'ms_sites_per_page' );
		if ( empty( $per_page ) || $per_page < 1 )
			$per_page = 15;

		$per_page = apply_filters( 'ms_sites_per_page', $per_page );

		$s = isset( $_GET['s'] ) ? stripslashes( trim( $_GET[ 's' ] ) ) : '';
		$like_s = esc_sql( like_escape( $s ) );

		$query = "SELECT * FROM {$wpdb->blogs} WHERE site_id = '{$wpdb->siteid}' ";

		if ( isset( $_GET['searchaction'] ) ) {
			if ( 'name' == $_GET['searchaction'] ) {
				$query .= " AND ( {$wpdb->blogs}.domain LIKE '%{$like_s}%' OR {$wpdb->blogs}.path LIKE '%{$like_s}%' ) ";
			} elseif ( 'id' == $_GET['searchaction'] ) {
				$query .= " AND {$wpdb->blogs}.blog_id = '{$like_s}' ";
			} elseif ( 'ip' == $_GET['searchaction'] ) {
				$query = "SELECT *
					FROM {$wpdb->blogs}, {$wpdb->registration_log}
					WHERE site_id = '{$wpdb->siteid}'
					AND {$wpdb->blogs}.blog_id = {$wpdb->registration_log}.blog_id
					AND {$wpdb->registration_log}.IP LIKE ('%{$like_s}%')";
			}
		}

		$order_by = isset( $_GET['sortby'] ) ? $_GET['sortby'] : 'id';
		if ( $order_by == 'registered' ) {
			$query .= ' ORDER BY registered ';
		} elseif ( $order_by == 'lastupdated' ) {
			$query .= ' ORDER BY last_updated ';
		} elseif ( $order_by == 'blogname' ) {
			$query .= ' ORDER BY domain ';
		} else {
			$order_by = 'id';
			$query .= " ORDER BY {$wpdb->blogs}.blog_id ";
		}

		$order = ( isset( $_GET['order'] ) && 'DESC' == $_GET['order'] ) ? "DESC" : "ASC";
		$query .= $order;

		$total = $wpdb->get_var( str_replace( 'SELECT *', 'SELECT COUNT(blog_id)', $query ) );

		$query .= " LIMIT " . intval( ( $pagenum - 1 ) * $per_page ) . ", " . intval( $per_page );
		$blog_list = $wpdb->get_results( $query, ARRAY_A );

		$num_pages = ceil($total / $per_page);
		$page_links = paginate_links( array(
			'base' => add_query_arg( 'paged', '%#%' ),
			'format' => '',
			'prev_text' => __( '&laquo;' ),
			'next_text' => __( '&raquo;' ),
			'total' => $num_pages,
			'current' => $pagenum
		));

		if ( empty( $_GET['mode'] ) )
			$mode = 'list';
		else
			$mode = esc_attr( $_GET['mode'] );
		?>

		<div class="wrap">
		<?php screen_icon(); ?>
		<h2><?php _e('Sites') ?>
		<a href="#form-add-site" class="button add-new-h2"><?php echo esc_html_x( 'Add New', 'sites' ); ?></a>
		<?php
		if ( isset( $_GET['s'] ) && $_GET['s'] )
		printf( '<span class="subtitle">' . __( 'Search results for &#8220;%s&#8221;' ) . '</span>', esc_html( $s ) );
		?>
		</h2>

		<form action="ms-sites.php" method="get" id="ms-search">
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

		<form id="form-site-list" action="ms-edit.php?action=allblogs" method="post">
		<input type="hidden" name="mode" value="<?php echo esc_attr( $mode ); ?>" />
		<div class="tablenav">
		<div class="alignleft actions">
			<select name="action">
				<option value="-1" selected="selected"><?php _e( 'Bulk Actions' ); ?></option>
				<option value="delete"><?php _e( 'Delete' ); ?></option>
				<option value="spam"><?php _e( 'Mark as Spam' ); ?></option>
				<option value="notspam"><?php _e( 'Not Spam' ); ?></option>
			</select>
			<input type="submit" value="<?php esc_attr_e( 'Apply' ); ?>" name="doaction" id="doaction" class="button-secondary action" />
			<?php wp_nonce_field( 'bulk-ms-sites' ); ?>
		</div>

		<?php if ( $page_links ) { ?>
		<div class="tablenav-pages">
		<?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
		number_format_i18n( ( $pagenum - 1 ) * $per_page + 1 ),
		number_format_i18n( min( $pagenum * $per_page, $num_pages ) ),
		number_format_i18n( $num_pages ),
		$page_links
		); echo $page_links_text; ?>
		</div>
		<?php } ?>

		<div class="view-switch">
			<a href="<?php echo esc_url( add_query_arg( 'mode', 'list', $_SERVER['REQUEST_URI'] ) ) ?>"><img <?php if ( 'list' == $mode ) echo 'class="current"'; ?> id="view-switch-list" src="<?php echo esc_url( includes_url( 'images/blank.gif' ) ); ?>" width="20" height="20" title="<?php _e( 'List View' ) ?>" alt="<?php _e( 'List View' ) ?>" /></a>
			<a href="<?php echo esc_url( add_query_arg( 'mode', 'excerpt', $_SERVER['REQUEST_URI'] ) ) ?>"><img <?php if ( 'excerpt' == $mode ) echo 'class="current"'; ?> id="view-switch-excerpt" src="<?php echo esc_url( includes_url( 'images/blank.gif' ) ); ?>" width="20" height="20" title="<?php _e( 'Excerpt View' ) ?>" alt="<?php _e( 'Excerpt View' ) ?>" /></a>
		</div>

		</div>

		<div class="clear"></div>

		<?php
		// define the columns to display, the syntax is 'internal name' => 'display name'
		$blogname_columns = ( is_subdomain_install() ) ? __( 'Domain' ) : __( 'Path' );
		$sites_columns = array(
			'id'           => __( 'ID' ),
			'blogname'     => $blogname_columns,
			'lastupdated'  => __( 'Last Updated'),
			'registered'   => __( 'Registered' ),
			'users'        => __( 'Users' )
		);

		if ( has_filter( 'wpmublogsaction' ) )
			$sites_columns['plugins'] = __( 'Actions' );

		$sites_columns = apply_filters( 'wpmu_blogs_columns', $sites_columns );
		?>

		<table class="widefat">
			<thead>
				<tr>
				<th class="manage-column column-cb check-column" id="cb" scope="col">
					<input type="checkbox" />
				</th>
				<?php
				$col_url = '';
				foreach($sites_columns as $column_id => $column_display_name) {
					$column_link = "<a href='";
					$order2 = '';
					if ( $order_by == $column_id )
						$order2 = ( $order == 'DESC' ) ? 'ASC' : 'DESC';

					$column_link .= esc_url( add_query_arg( array( 'order' => $order2, 'paged' => $pagenum, 'sortby' => $column_id ), remove_query_arg( array('action', 'updated'), $_SERVER['REQUEST_URI'] ) ) );
					$column_link .= "'>{$column_display_name}</a>";
					$col_url .= '<th scope="col">' . ( ( $column_id == 'users' || $column_id == 'plugins' ) ? $column_display_name : $column_link ) . '</th>';
				}
				echo $col_url ?>
				</tr>
			</thead>
			<tfoot>
				<tr>
				<th class="manage-column column-cb check-column" id="cb1" scope="col">
					<input type="checkbox" />
				</th>
					<?php echo $col_url ?>
				</tr>
			</tfoot>
			<tbody id="the-site-list" class="list:site">
			<?php
			$status_list = array( 'archived' => array( 'site-archived', __( 'Archived' ) ), 'spam' => array( 'site-spammed', __( 'Spam' ) ), 'deleted' => array( 'site-deleted', __( 'Deleted' ) ), 'mature' => array( 'site-mature', __( 'Mature' ) ) );
			if ( $blog_list ) {
				$class = '';
				foreach ( $blog_list as $blog ) {
					$class = ( 'alternate' == $class ) ? '' : 'alternate';
					reset( $status_list );

					$blog_states = array();
					foreach ( $status_list as $status => $col ) {
						if ( get_blog_status( $blog['blog_id'], $status ) == 1 ) {
							$class = $col[0];
							$blog_states[] = $col[1];
						}
					}
					$blog_state = '';
					if ( ! empty( $blog_states ) ) {
						$state_count = count( $blog_states );
						$i = 0;
						$blog_state .= ' - ';
						foreach ( $blog_states as $state ) {
							++$i;
							( $i == $state_count ) ? $sep = '' : $sep = ', ';
							$blog_state .= "<span class='post-state'>$state$sep</span>";
						}
					}
					echo "<tr class='$class'>";

					$blogname = ( is_subdomain_install() ) ? str_replace( '.'.$current_site->domain, '', $blog['domain'] ) : $blog['path'];
					foreach ( $sites_columns as $column_name=>$column_display_name ) {
						switch ( $column_name ) {
							case 'id': ?>
								<th scope="row" class="check-column">
									<input type="checkbox" id="blog_<?php echo $blog['blog_id'] ?>" name="allblogs[]" value="<?php echo esc_attr( $blog['blog_id'] ) ?>" />
								</th>
								<th valign="top" scope="row">
									<?php echo $blog['blog_id'] ?>
								</th>
							<?php
							break;

							case 'blogname': ?>
								<td class="column-title">
									<a href="<?php echo esc_url( admin_url( 'ms-sites.php?action=editblog&amp;id=' . $blog['blog_id'] ) ); ?>" class="edit"><?php echo $blogname . $blog_state; ?></a>
									<?php
									if ( 'list' != $mode )
										echo '<p>' . sprintf( _x( '%1$s &#8211; <em>%2$s', '%1$s: site name. %2$s: site tagline.' ), get_blog_option( $blog['blog_id'], 'blogname' ), get_blog_option( $blog['blog_id'], 'blogdescription ' ) ) . '</em></p>';

									// Preordered.
									$actions = array(
										'edit' => '', 'backend' => '',
										'activate' => '', 'deactivate' => '',
										'archive' => '', 'unarchive' => '',
										'spam' => '', 'unspam' => '',
										'delete' => '',
										'visit' => '',
									);

									$actions['edit']	= '<span class="edit"><a href="' . esc_url( admin_url( 'ms-sites.php?action=editblog&amp;id=' . $blog['blog_id'] ) ) . '">' . __( 'Edit' ) . '</a><span>';
									$actions['backend']	= "<span class='backend'><a href='" . esc_url( get_admin_url($blog['blog_id']) ) . "' class='edit'>" . __( 'Backend' ) . '</a></span>';
									if ( $current_site->blog_id != $blog['blog_id'] ) {
										if ( get_blog_status( $blog['blog_id'], 'deleted' ) == '1' )
											$actions['activate']	= '<span class="activate"><a href="' . esc_url( admin_url( 'ms-edit.php?action=confirm&amp;action2=activateblog&amp;id=' . $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( 'You are about to activate the site %s' ), $blogname ) ) ) ) . '">' . __( 'Activate' ) . '</a></span>';
										else
											$actions['deactivate']	= '<span class="activate"><a href="' . esc_url( admin_url( 'ms-edit.php?action=confirm&amp;action2=deactivateblog&amp;id=' . $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( 'You are about to deactivate the site %s' ), $blogname ) ) ) ) . '">' . __( 'Deactivate' ) . '</a></span>';

										if ( get_blog_status( $blog['blog_id'], 'archived' ) == '1' )
											$actions['unarchive']	= '<span class="archive"><a href="' . esc_url( admin_url( 'ms-edit.php?action=confirm&amp;action2=unarchiveblog&amp;id=' .  $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( 'You are about to unarchive the site %s.' ), $blogname ) ) ) ) . '">' . __( 'Unarchive' ) . '</a></span>';
										else
											$actions['archive']	= '<span class="archive"><a href="' . esc_url( admin_url( 'ms-edit.php?action=confirm&amp;action2=archiveblog&amp;id=' . $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( 'You are about to archive the site %s.' ), $blogname ) ) ) ) . '">' . __( 'Archive' ) . '</a></span>';

										if ( get_blog_status( $blog['blog_id'], 'spam' ) == '1' )
											$actions['unspam']	= '<span class="spam"><a href="' . esc_url( admin_url( 'ms-edit.php?action=confirm&amp;action2=unspamblog&amp;id=' . $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( 'You are about to unspam the site %s.' ), $blogname ) ) ) ) . '">' . __( 'Not Spam' ) . '</a></span>';
										else
											$actions['spam']	= '<span class="spam"><a href="' . esc_url( admin_url( 'ms-edit.php?action=confirm&amp;action2=spamblog&amp;id=' . $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( 'You are about to mark the site %s as spam.' ), $blogname ) ) ) ) . '">' . __( 'Spam' ) . '</a></span>';

										$actions['delete']	= '<span class="delete"><a href="' . esc_url( admin_url( 'ms-edit.php?action=confirm&amp;action2=deleteblog&amp;id=' . $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( 'You are about to delete the site %s.' ), $blogname ) ) ) ) . '">' . __( 'Delete' ) . '</a></span>';
									}

									$actions['visit']	= "<span class='view'><a href='" . esc_url( get_home_url( $blog['blog_id'] ) ) . "' rel='permalink'>" . __( 'Visit' ) . '</a>';
									$actions = array_filter( $actions );
									if ( count( $actions ) ) : ?>
									<div class="row-actions">
										<?php echo implode( ' | ', $actions ); ?>
									</div>
									<?php endif; ?>
								</td>
							<?php
							break;

							case 'lastupdated': ?>
								<td valign="top">
									<?php
									if ( 'list' == $mode )
										$date = 'Y/m/d';
									else
										$date = 'Y/m/d \<\b\r \/\> g:i:s a';
									echo ( $blog['last_updated'] == '0000-00-00 00:00:00' ) ? __( 'Never' ) : mysql2date( __( $date ), $blog['last_updated'] ); ?>
								</td>
							<?php
							break;
						case 'registered': ?>
								<td valign="top">
								<?php
								if ( $blog['registered'] == '0000-00-00 00:00:00' )
									echo '&#x2014;';
								else
									echo mysql2date( __( $date ), $blog['registered'] );
								?>
								</td>
						<?php
						break;
							case 'users': ?>
								<td valign="top">
									<?php
									$blogusers = get_users_of_blog( $blog['blog_id'] );
									if ( is_array( $blogusers ) ) {
										$blogusers_warning = '';
										if ( count( $blogusers ) > 5 ) {
											$blogusers = array_slice( $blogusers, 0, 5 );
											$blogusers_warning = __( 'Only showing first 5 users.' ) . ' <a href="' . esc_url( get_admin_url( $blog['blog_id'], 'users.php' ) ) . '">' . __( 'More' ) . '</a>';
										}
										foreach ( $blogusers as $key => $val ) {
											echo '<a href="' . esc_url( admin_url( 'user-edit.php?user_id=' . $val->user_id ) ) . '">' . esc_html( $val->user_login ) . '</a> ';
											if ( 'list' != $mode )
												echo '(' . $val->user_email . ')';
											echo '<br />';
										}
										if ( $blogusers_warning != '' )
											echo '<strong>' . $blogusers_warning . '</strong><br />';
									}
									?>
								</td>
							<?php
							break;

							case 'plugins': ?>
								<?php if ( has_filter( 'wpmublogsaction' ) ) { ?>
								<td valign="top">
									<?php do_action( 'wpmublogsaction', $blog['blog_id'] ); ?>
								</td>
								<?php } ?>
							<?php break;

							default: ?>
								<?php if ( has_filter( 'manage_blogs_custom_column' ) ) { ?>
								<td valign="top">
									<?php do_action( 'manage_blogs_custom_column', $column_name, $blog['blog_id'] ); ?>
								</td>
								<?php } ?>
							<?php break;
						}
					}
					?>
					</tr>
					<?php
				}
			} else { ?>
				<tr>
					<td colspan="<?php echo (int) count( $sites_columns ); ?>"><?php _e( 'No blogs found.' ) ?></td>
				</tr>
			<?php
			} // end if ($blogs)
			?>

			</tbody>
		</table>
		<div class="tablenav">
			<?php
			if ( $page_links )
				echo "<div class='tablenav-pages'>$page_links_text</div>";
			?>

			<div class="alignleft actions">
			<select name="action2">
				<option value="-1" selected="selected"><?php _e( 'Bulk Actions' ); ?></option>
				<option value="delete"><?php _e( 'Delete' ); ?></option>
				<option value="spam"><?php _e( 'Mark as Spam' ); ?></option>
				<option value="notspam"><?php _e( 'Not Spam' ); ?></option>
			</select>
			<input type="submit" value="<?php esc_attr_e( 'Apply' ); ?>" name="doaction2" id="doaction2" class="button-secondary action" />
			</div>
			<br class="clear" />
		</div>

		</form>
		</div>

		<div id="form-add-site" class="wrap">
			<h3><?php _e( 'Add Site' ) ?></h3>
			<form method="post" action="ms-edit.php?action=addblog">
				<?php wp_nonce_field( 'add-blog' ) ?>
				<table class="form-table">
					<tr class="form-field form-required">
						<th scope="row"><?php _e( 'Site Address' ) ?></th>
						<td>
						<?php if ( is_subdomain_install() ) { ?>
							<input name="blog[domain]" type="text" class="regular-text" title="<?php _e( 'Domain' ) ?>"/>.<?php echo $current_site->domain;?>
						<?php } else {
							echo $current_site->domain . $current_site->path ?><input name="blog[domain]" class="regular-text" type="text" title="<?php _e( 'Domain' ) ?>"/>
						<?php }
						echo '<p>' . __( 'Only the characters a-z and 0-9 recommended.' ) . '</p>';
						?>
						</td>
					</tr>
					<tr class="form-field form-required">
						<th scope="row"><?php _e( 'Site Title' ) ?></th>
						<td><input name="blog[title]" type="text" class="regular-text" title="<?php _e( 'Title' ) ?>"/></td>
					</tr>
					<tr class="form-field form-required">
						<th scope="row"><?php _e( 'Admin Email' ) ?></th>
						<td><input name="blog[email]" type="text" class="regular-text" title="<?php _e( 'Email' ) ?>"/></td>
					</tr>
					<tr class="form-field">
						<td colspan="2"><?php _e( 'A new user will be created if the above email address is not in the database.' ) ?><br /><?php _e( 'The username and password will be mailed to this email address.' ) ?></td>
					</tr>
				</table>
				<p class="submit">
					<input class="button" type="submit" name="go" value="<?php esc_attr_e( 'Add Site' ) ?>" /></p>
			</form>
		</div>
		<?php
	break;
} // end switch( $action )

include( './admin-footer.php' ); ?>
