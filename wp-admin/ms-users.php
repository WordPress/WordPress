<?php
require_once('admin.php');

if ( !is_multisite() )
	wp_die( __('Multisite support is not enabled.') );

$title = __('Users');
$parent_file = 'ms-admin.php';

wp_enqueue_script( 'admin-forms' );

require_once('admin-header.php');

if ( ! current_user_can( 'manage_network_users' ) )
	wp_die( __('You do not have permission to access this page.') );

if ( isset($_GET['updated']) && $_GET['updated'] == 'true' ) {
	?>
	<div id="message" class="updated fade"><p>
		<?php
		switch ($_GET['action']) {
			case 'delete':
				_e('User deleted !');
			break;
			case 'all_spam':
				_e('Users marked as spam !');
			break;
			case 'all_notspam':
				_e('Users marked as not spam !');
			break;
			case 'all_delete':
				_e('Users deleted !');
			break;
			case 'add':
				_e('User added !');
			break;
		}
		?>
	</p></div>
	<?php
}
?>

<div class="wrap" style="position:relative;">
	<?php
	$apage = isset( $_GET['apage'] ) ? intval( $_GET['apage'] ) : 1;
	$num = isset( $_GET['num'] ) ? intval( $_GET['num'] ) : 15;
	$s = isset($_GET[ 's' ]) ? esc_attr( trim( $_GET[ 's' ] ) ) : '';

	$query = "SELECT * FROM {$wpdb->users}";

	if ( !empty( $s ) ) {
		$search = '%' . trim( $s ) . '%';
		$query .= " WHERE user_login LIKE '$search' OR user_email LIKE '$search'";
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

	$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
	$order = ( 'DESC' == $order ) ? 'DESC' : 'ASC';
	$query .= $order;

	if ( !empty( $s ) )
		$total = $wpdb->get_var( str_replace('SELECT *', 'SELECT COUNT(ID)', $query) );
	else
		$total = $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->users}");

	$query .= " LIMIT " . intval( ( $apage - 1 ) * $num) . ", " . intval( $num );

	$user_list = $wpdb->get_results( $query, ARRAY_A );

	// Pagination
	$user_navigation = paginate_links( array(
		'total' => ceil($total / $num),
		'current' => $apage,
		'base' => add_query_arg( 'apage', '%#%' ),
		'format' => ''
	));

	if ( $user_navigation ) {
		$user_navigation = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
			number_format_i18n( ( $apage - 1 ) * $num + 1 ),
			number_format_i18n( min( $apage * $num, $total ) ),
			number_format_i18n( $total ),
			$user_navigation
		);
	}

	?>
	<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php esc_html_e("Users"); ?></h2>
	<form action="ms-users.php" method="get" class="search-form">
		<p class="search-box">
		<input type="text" name="s" value="<?php if ( isset($_GET['s']) ) esc_attr( stripslashes( $s ) ); ?>" class="search-input" id="user-search-input" />
		<input type="submit" id="post-query-submit" value="<?php esc_attr_e('Search Users') ?>" class="button" />
		</p>
	</form>
	</div>

	<form id="form-user-list" action='ms-edit.php?action=allusers' method='post'>
		<div class="tablenav">
			<?php if ( $user_navigation ) echo "<div class='tablenav-pages'>$user_navigation</div>"; ?>

			<div class="alignleft actions">
				<input type="submit" value="<?php esc_attr_e('Delete') ?>" name="alluser_delete" class="button-secondary delete" />
				<input type="submit" value="<?php esc_attr_e('Mark as Spammers') ?>" name="alluser_spam" class="button-secondary" />
				<input type="submit" value="<?php esc_attr_e('Not Spam') ?>" name="alluser_notspam" class="button-secondary" />
				<?php wp_nonce_field( 'allusers' ); ?>
				<br class="clear" />
			</div>
		</div>

		<?php if ( isset($_GET['s']) && $_GET['s'] != '' ) : ?>
			<p><a href="ms-sites.php?action=blogs&amp;s=<?php echo urlencode( stripslashes( $s ) ); ?>&blog_name=Search+blogs+by+name"><?php _e('Search Blogs for') ?> <strong><?php echo stripslashes( $s ) ?></strong></a></p>
		<?php endif; ?>

		<?php
		// define the columns to display, the syntax is 'internal name' => 'display name'
		$posts_columns = array(
			'checkbox'	 => '',
			'login'      => __('Username'),
			'name'       => __('Name'),
			'email'      => __('E-mail'),
			'registered' => __('Registered'),
			'blogs'      => ''
		);
		$posts_columns = apply_filters('wpmu_users_columns', $posts_columns);
		?>
		<table class="widefat" cellspacing="0">
			<thead>
			<tr>
				<?php foreach( (array) $posts_columns as $column_id => $column_display_name) {
					if ( $column_id == 'blogs' ) {
						echo '<th scope="col">'.__('Blogs').'</th>';
					} elseif ( $column_id == 'checkbox') {
						echo '<th scope="col" class="check-column"><input type="checkbox" /></th>';
					} else { ?>
						<th scope="col"><a href="ms-users.php?sortby=<?php echo $column_id ?>&amp;<?php if ( $order_by == $column_id ) { if ( $order == 'DESC' ) { echo "order=ASC&amp;" ; } else { echo "order=DESC&amp;"; } } ?>apage=<?php echo $apage ?>"><?php echo $column_display_name; ?></a></th>
					<?php } ?>
				<?php } ?>
			</tr>
			</thead>
			<tbody id="users" class="list:user user-list">
			<?php if ($user_list) {
				$class = '';
				foreach ( (array) $user_list as $user) {
					$class = ('alternate' == $class) ? '' : 'alternate';

					$status_list = array( "spam" => "site-spammed", "deleted" => "site-deleted" );

					foreach ( $status_list as $status => $col ) {
						if ( $user[$status] )
							$class = $col;
					}

					?>

					<tr class="<?php echo $class; ?>">
					<?php
					foreach( (array) $posts_columns as $column_name=>$column_display_name) :
						switch($column_name) {
							case 'checkbox': ?>
								<th scope="row" class="check-column"><input type='checkbox' id='user_<?php echo $user['ID'] ?>' name='allusers[]' value='<?php echo esc_attr($user['ID']) ?>' /></th>
							<?php
							break;

							case 'login':
								$avatar	= get_avatar( $user['user_email'], 32 );
								$edit	= esc_url( add_query_arg( 'wp_http_referer', urlencode( esc_url( stripslashes( $_SERVER['REQUEST_URI'] ) ) ), "user-edit.php?user_id=".$user['ID'] ) );
								// @todo Make delete link work like delete button with transfering users (in ms-edit.php)
								//$delete	= esc_url( add_query_arg( 'wp_http_referer', urlencode( esc_url( stripslashes( $_SERVER['REQUEST_URI'] ) ) ), wp_nonce_url( 'ms-edit.php', 'deleteuser' ) . '&amp;action=deleteuser&amp;id=' . $user['ID'] ) );
								?>
								<td class="username column-username">
									<?php echo $avatar; ?><strong><a href="<?php echo $edit; ?>" class="edit"><?php echo stripslashes($user['user_login']); ?></a></strong>
									<br/>
									<div class="row-actions">
										<span class="edit"><a href="<?php echo $edit; ?>">Edit</a></span>
										<?php /*<span class="delete"><a href="<?php echo $delete; ?>" class="delete">Delete</a></span> */ ?>
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

							case 'registered': ?>
								<td><?php echo mysql2date(__('Y-m-d \<\b\r \/\> g:i a'), $user['user_registered']); ?></td>
							<?php
							break;

							case 'blogs':
								$blogs = get_blogs_of_user( $user['ID'], true );
								?>
								<td>
									<?php
									if ( is_array( $blogs ) ) {
										foreach ( (array) $blogs as $key => $val ) {
											$path	= ($val->path == '/') ? '' : $val->path;
											echo '<a href="ms-sites.php?action=editblog&amp;id=' . $val->userblog_id . '">' . str_replace( '.' . $current_site->domain, '', $val->domain . $path ) . '</a>';
											echo ' <small class="row-actions">';

											// Edit
											echo '<a href="ms-sites.php?action=editblog&amp;id=' . $val->userblog_id . '">' . __('Edit') . '</a> | ';

											// View
											echo '<a ';
											if ( get_blog_status( $val->userblog_id, 'spam' ) == 1 )
												echo 'style="background-color: #f66" ';
											echo 'target="_new" href="http://'.$val->domain . $val->path.'">' . __('View') . '</a>';

											echo '</small><br />';
										}
									}
									?>
								</td>
							<?php
							break;

							default: ?>
								<td><?php do_action('manage_users_custom_column', $column_name, $user['ID']); ?></td>
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
				<tr style='background-color: <?php echo $bgcolor; ?>'>
					<td colspan="<?php echo (int) count($posts_columns); ?>"><?php _e('No users found.') ?></td>
				</tr>
				<?php
			} // end if ($users)
			?>
			</tbody>
		</table>

		<div class="tablenav">
			<?php if ( $user_navigation ) echo "<div class='tablenav-pages'>$user_navigation</div>"; ?>

			<div class="alignleft">
				<input type="submit" value="<?php esc_attr_e('Delete') ?>" name="alluser_delete" class="button-secondary delete" />
				<input type="submit" value="<?php esc_attr_e('Mark as Spammers') ?>" name="alluser_spam" class="button-secondary" />
				<input type="submit" value="<?php esc_attr_e('Not Spam') ?>" name="alluser_notspam" class="button-secondary" />
				<?php wp_nonce_field( 'allusers' ); ?>
				<br class="clear" />
			</div>
		</div>
	</form>
</div>

<?php
if ( apply_filters('show_adduser_fields', true) ) :
?>
<div class="wrap">
	<h2><?php _e('Add user') ?></h2>
	<form action="ms-edit.php?action=adduser" method="post">
	<table class="form-table">
		<tr class="form-field form-required">
			<th scope='row'><?php _e('Username') ?></th>
			<td><input type="text" name="user[username]" /></td>
		</tr>
		<tr class="form-field form-required">
			<th scope='row'><?php _e('Email') ?></th>
			<td><input type="text" name="user[email]" /></td>
		</tr>
		<tr class="form-field">
			<td colspan='2'><?php _e('Username and password will be mailed to the above email address.') ?></td>
		</tr>
	</table>
	<p class="submit">
		<?php wp_nonce_field('add-user') ?>
		<input class="button" type="submit" name="Add user" value="<?php esc_attr_e('Add user') ?>" /></p>
	</form>
</div>
<?php endif; ?>

<?php include('admin-footer.php'); ?>
