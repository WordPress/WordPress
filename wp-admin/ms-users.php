<?php
/**
 * Multisite users administration panel.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.0.0
 */

require_once( './admin.php' );

if ( !is_multisite() )
	wp_die( __( 'Multisite support is not enabled.' ) );

if ( ! current_user_can( 'manage_network_users' ) )
	wp_die( __( 'You do not have permission to access this page.' ) );

$title = __( 'Users' );
$parent_file = 'ms-admin.php';

wp_enqueue_script( 'admin-forms' );

require_once( './admin-header.php' );

if ( isset( $_GET['updated'] ) && $_GET['updated'] == 'true' && ! empty( $_GET['action'] ) ) {
	?>
	<div id="message" class="updated"><p>
		<?php
		switch ( $_GET['action'] ) {
			case 'delete':
				_e( 'User deleted.' );
			break;
			case 'all_spam':
				_e( 'Users marked as spam.' );
			break;
			case 'all_notspam':
				_e( 'Users removed from spam.' );
			break;
			case 'all_delete':
				_e( 'Users deleted.' );
			break;
			case 'add':
				_e( 'User added.' );
			break;
		}
		?>
	</p></div>
	<?php
}

	$pagenum = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 0;
	if ( empty( $pagenum ) )
		$pagenum = 1;

	$per_page = (int) get_user_option( 'ms_users_per_page' );
	if ( empty( $per_page ) || $per_page < 1 )
		$per_page = 15;

	$per_page = apply_filters( 'ms_users_per_page', $per_page );

	$s = isset( $_GET['s'] ) ? stripslashes( trim( $_GET[ 's' ] ) ) : '';
	$like_s = esc_sql( like_escape( $s ) );

	$query = "SELECT * FROM {$wpdb->users}";

	if ( !empty( $like_s ) ) {
		$query .= " WHERE user_login LIKE '%$like_s%' OR user_email LIKE '%$like_s%'";
	}

	$order_by = isset( $_GET['sortby'] ) ? $_GET['sortby'] : 'id';
	if ( $order_by == 'email' ) {
		$query .= ' ORDER BY user_email ';
	} elseif ( $order_by == 'login' ) {
		$query .= ' ORDER BY user_login ';
	} elseif ( $order_by == 'name' ) {
		$query .= ' ORDER BY display_name ';
	} elseif ( $order_by == 'registered' ) {
		$query .= ' ORDER BY user_registered ';
	} else {
		$order_by = 'id';
		$query .= ' ORDER BY ID ';
	}

	$order = ( isset( $_GET['order'] ) && 'DESC' == $_GET['order'] ) ? 'DESC' : 'ASC';
	$query .= $order;

	$total = $wpdb->get_var( str_replace( 'SELECT *', 'SELECT COUNT(ID)', $query ) );

	$query .= " LIMIT " . intval( ( $pagenum - 1 ) * $per_page) . ", " . intval( $per_page );

	$user_list = $wpdb->get_results( $query, ARRAY_A );

	$num_pages = ceil( $total / $per_page );
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
	<h2><?php esc_html_e( 'Users' ); ?>
	<a href="#form-add-user" class="button add-new-h2"><?php echo esc_html_x( 'Add New' , 'users'); ?></a>
	<?php
	if ( isset( $_GET['s'] ) && $_GET['s'] )
	printf( '<span class="subtitle">' . __( 'Search results for &#8220;%s&#8221;' ) . '</span>', esc_html( $s ) );
	?>
	</h2>

	<form action="ms-users.php" method="get" class="search-form">
		<p class="search-box">
		<input type="text" name="s" value="<?php echo esc_attr( $s ); ?>" class="search-input" id="user-search-input" />
		<input type="submit" id="post-query-submit" value="<?php esc_attr_e( 'Search Users' ) ?>" class="button" />
		</p>
	</form>

	<form id="form-user-list" action='ms-edit.php?action=allusers' method='post'>
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
				<?php wp_nonce_field( 'bulk-ms-users' ); ?>
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
		$users_columns = array(
			'id'           => __( 'ID' ),
			'login'      => __( 'Username' ),
			'name'       => __( 'Name' ),
			'email'      => __( 'E-mail' ),
			'registered' => __( 'Registered' ),
			'blogs'      => __( 'Sites' )
		);
		$users_columns = apply_filters( 'wpmu_users_columns', $users_columns );
		?>
		<table class="widefat">
			<thead>
			<tr>
				<th class="manage-column column-cb check-column" id="cb" scope="col">
					<input type="checkbox" />
				</th>
				<?php
				$col_url = '';
				foreach($users_columns as $column_id => $column_display_name) {
					$column_link = "<a href='";
					$order2 = '';
					if ( $order_by == $column_id )
						$order2 = ( $order == 'DESC' ) ? 'ASC' : 'DESC';

					$column_link .= esc_url( add_query_arg( array( 'order' => $order2, 'paged' => $pagenum, 'sortby' => $column_id ), remove_query_arg( array( 'action', 'updated' ), $_SERVER['REQUEST_URI'] ) ) );
					$column_link .= "'>{$column_display_name}</a>";
					$col_url .= '<th scope="col">' . ( $column_id == 'blogs' ? $column_display_name : $column_link ) . '</th>';
				}
				echo $col_url; ?>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<th class="manage-column column-cb check-column" id="cb" scope="col">
					<input type="checkbox" />
				</th>
				<?php echo $col_url; ?>
			</tr>
			</tfoot>
			<tbody id="the-user-list" class="list:user">
			<?php if ( $user_list ) {
				$class = '';
				$super_admins = get_site_option( 'site_admins' );
				foreach ( (array) $user_list as $user ) {
					$class = ( 'alternate' == $class ) ? '' : 'alternate';

					$status_list = array( 'spam' => 'site-spammed', 'deleted' => 'site-deleted' );

					foreach ( $status_list as $status => $col ) {
						if ( $user[$status] )
							$class = $col;
					}

					?>
					<tr class="<?php echo $class; ?>">
					<?php
					foreach( (array) $users_columns as $column_name=>$column_display_name ) :
						switch( $column_name ) {
							case 'id': ?>
								<th scope="row" class="check-column">
									<input type="checkbox" id="blog_<?php echo $user['ID'] ?>" name="allusers[]" value="<?php echo esc_attr( $user['ID'] ) ?>" />
								</th>
								<th valign="top" scope="row">
									<?php echo $user['ID'] ?>
								</th>
							<?php
							break;

							case 'login':
								$avatar	= get_avatar( $user['user_email'], 32 );
								$edit_link = ( $current_user->ID == $user['ID'] ) ? 'profile.php' : 'user-edit.php?user_id=' . $user['ID'];
								?>
								<td class="username column-username">
									<?php echo $avatar; ?><strong><a href="<?php echo esc_url( admin_url( $edit_link ) ); ?>" class="edit"><?php echo stripslashes( $user['user_login'] ); ?></a><?php
									if ( in_array( $user['user_login'], $super_admins ) )
										echo ' - ' . __( 'Super admin' );
									?></strong>
									<br/>
									<div class="row-actions">
										<span class="edit"><a href="<?php echo esc_url( admin_url( $edit_link ) ); ?>"><?php _e( 'Edit' ); ?></a></span>
										<?php if ( ! in_array( $user['user_login'], $super_admins ) ) { ?>
										| <span class="delete"><a href="<?php echo $delete	= esc_url( admin_url( add_query_arg( '_wp_http_referer', urlencode( stripslashes( $_SERVER['REQUEST_URI'] ) ), wp_nonce_url( 'ms-edit.php', 'deleteuser' ) . '&amp;action=deleteuser&amp;id=' . $user['ID'] ) ) ); ?>" class="delete"><?php _e( 'Delete' ); ?></a></span>
										<?php } ?>
									</div>
								</td>
							<?php
							break;

							case 'name': ?>
								<td class="name column-name"><?php echo $user['display_name'] ?></td>
							<?php
							break;

							case 'email': ?>
								<td class="email column-email"><a href="mailto:<?php echo $user['user_email'] ?>"><?php echo $user['user_email'] ?></a></td>
							<?php
							break;

							case 'registered': 
								if ( 'list' == $mode )
									$date = 'Y/m/d';
								else
									$date = 'Y/m/d \<\b\r \/\> g:i:s a';
							?>
								<td><?php echo mysql2date( __( $date ), $user['user_registered'] ); ?></td>
							<?php
							break;

							case 'blogs':
								$blogs = get_blogs_of_user( $user['ID'], true );
								?>
								<td>
									<?php
									if ( is_array( $blogs ) ) {
										foreach ( (array) $blogs as $key => $val ) {
											$path	= ( $val->path == '/' ) ? '' : $val->path;
											echo '<a href="'. esc_url( admin_url( 'ms-sites.php?action=editblog&amp;id=' . $val->userblog_id  ) ) .'">' . str_replace( '.' . $current_site->domain, '', $val->domain . $path ) . '</a>';
											echo ' <small class="row-actions">';

											// Edit
											echo '<a href="'. esc_url( admin_url( 'ms-sites.php?action=editblog&amp;id=' . $val->userblog_id  ) ) .'">' . __( 'Edit' ) . '</a> | ';

											// View
											echo '<a ';
											if ( get_blog_status( $val->userblog_id, 'spam' ) == 1 )
												echo 'style="background-color: #faa" ';
											echo 'href="' .  esc_url( get_home_url( $val->userblog_id ) )  . '">' . __( 'View' ) . '</a>';
											
											echo '</small><br />';
										}
									}
									?>
								</td>
							<?php
							break;

							default: ?>
								<td><?php do_action( 'manage_users_custom_column', $column_name, $user['ID'] ); ?></td>
							<?php
							break;
						}
					endforeach
					?>
					</tr>
					<?php
				}
			} else {
			?>
				<tr>
					<td colspan="<?php echo (int) count($users_columns); ?>"><?php _e( 'No users found.' ) ?></td>
				</tr>
				<?php
			} // end if ($users)
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

<?php
if ( apply_filters( 'show_adduser_fields', true ) ) :
?>
<div class="wrap" id="form-add-user">
	<h3><?php _e( 'Add User' ) ?></h3>
	<form action="ms-edit.php?action=adduser" method="post">
	<table class="form-table">
		<tr class="form-field form-required">
			<th scope="row"><?php _e( 'Username' ) ?></th>
			<td><input type="text" class="regular-text" name="user[username]" /></td>
		</tr>
		<tr class="form-field form-required">
			<th scope="row"><?php _e( 'Email' ) ?></th>
			<td><input type="text" class="regular-text" name="user[email]" /></td>
		</tr>
		<tr class="form-field">
			<td colspan="2"><?php _e( 'Username and password will be mailed to the above email address.' ) ?></td>
		</tr>
	</table>
	<p class="submit">
		<?php wp_nonce_field( 'add-user' ) ?>
		<input class="button" type="submit" value="<?php esc_attr_e( 'Add user' ) ?>" /></p>
	</form>
</div>
<?php endif; ?>

<?php include( './admin-footer.php' ); ?>
