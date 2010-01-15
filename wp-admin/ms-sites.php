<?php
require_once('admin.php');

if ( !is_multisite() )
	wp_die( __('Multisite support is not enabled.') );

$title = __('Blogs');
$parent_file = 'ms-admin.php';

wp_enqueue_script( 'admin-forms' );

require_once('admin-header.php');

if ( !is_super_admin() )
	wp_die( __('You do not have permission to access this page.') );

$id = intval( $_GET['id'] );
$protocol = is_ssl() ? 'https://' : 'http://';

if ( $_GET['updated'] == 'true' ) {
	?>
	<div id="message" class="updated fade"><p>
		<?php
		switch ($_GET['action']) {
			case 'all_notspam':
				_e('Blogs mark as not spam !');
			break;
			case 'all_spam':
				_e('Blogs mark as spam !');
			break;
			case 'all_delete':
				_e('Blogs deleted !');
			break;
			case 'delete':
				_e('Blog deleted !');
			break;
			case 'add-blog':
				_e('Blog added !');
			break;
			case 'archive':
				_e('Blog archived !');
			break;
			case 'unarchive':
				_e('Blog unarchived !');
			break;
			case 'activate':
				_e('Blog activated !');
			break;
			case 'deactivate':
				_e('Blog deactivated !');
			break;
			case 'unspam':
				_e('Blog mark as not spam !');
			break;
			case 'spam':
				_e('Blog mark as spam !');
			break;
			case 'umature':
				_e('Blog mark as not mature !');
			break;
			case 'mature':
				_e('Blog mark as mature !');
			break;
			default:
				_e('Options saved !');
			break;
		}
		?>
	</p></div>
	<?php
}

switch( $_GET['action'] ) {
	// Edit blog
	case "editblog":
		$blog_prefix = $wpdb->get_blog_prefix( $id );
		$options = $wpdb->get_results( "SELECT * FROM {$blog_prefix}options WHERE option_name NOT LIKE '_transient_rss%' AND option_name NOT LIKE '%user_roles'", ARRAY_A );
		$details = $wpdb->get_row( "SELECT * FROM {$wpdb->blogs} WHERE blog_id = '{$id}'", ARRAY_A );
		$editblog_roles = get_blog_option( $id, "{$blog_prefix}user_roles" );
		?>
		<div class="wrap">
		<h2><?php _e('Edit Blog'); ?> - <a href='http://<?php echo $details['domain'].$details['path']; ?>'>http://<?php echo $details['domain'].$details['path']; ?></a></h2>
		<form method="post" action="ms-edit.php?action=updateblog">
			<?php wp_nonce_field('editblog'); ?>
			<input type="hidden" name="id" value="<?php echo esc_attr($id) ?>" />
			<div class='metabox-holder' style='width:49%;float:left;'>
			<div id="blogedit_bloginfo" class="postbox " >
			<h3 class='hndle'><span><?php _e('Blog info (wp_blogs)'); ?></span></h3>
			<div class="inside">
				<table class="form-table">
							<tr class="form-field form-required">
								<th scope="row"><?php _e('Domain') ?></th>
								<td>http://<input name="blog[domain]" type="text" id="domain" value="<?php echo $details['domain'] ?>" size="33" /></td>
							</tr>
							<tr class="form-field form-required">
								<th scope="row"><?php _e('Path') ?></th>
								<td><input name="blog[path]" type="text" id="path" value="<?php echo esc_attr($details['path']) ?>" size="40" style='margin-bottom:5px;' />
								<br /><input type='checkbox' style='width:20px;' name='update_home_url' value='update' <?php if( get_blog_option( $id, 'siteurl' ) == preg_replace('|/+$|', '', 'http://' . $details['domain'] . $details['path']) || get_blog_option( $id, 'home' ) == preg_replace('|/+$|', '', 'http://' . $details['domain'] . $details['path']) ) echo 'checked="checked"'; ?> /> <?php _e( "Update 'siteurl' and 'home' as well." ); ?></td>
							</tr>
							<tr class="form-field">
								<th scope="row"><?php _e('Registered') ?></th>
								<td><input name="blog[registered]" type="text" id="blog_registered" value="<?php echo esc_attr($details['registered']) ?>" size="40" /></td>
							</tr>
							<tr class="form-field">
								<th scope="row"><?php _e('Last Updated') ?></th>
								<td><input name="blog[last_updated]" type="text" id="blog_last_updated" value="<?php echo esc_attr($details['last_updated']) ?>" size="40" /></td>
							</tr>
							<tr class="form-field">
								<th scope="row"><?php _e('Public') ?></th>
								<td>
									<input type='radio' style='width:20px;' name='blog[public]' value='1' <?php if( $details['public'] == '1' ) echo 'checked="checked"'; ?> /> <?php _e('Yes') ?>
									<input type='radio' style='width:20px;' name='blog[public]' value='0' <?php if( $details['public'] == '0' ) echo 'checked="checked"'; ?> /> <?php _e('No') ?>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row"><?php _e( 'Archived' ); ?></th>
								<td>
									<input type='radio' style='width:20px;' name='blog[archived]' value='1' <?php if( $details['archived'] == '1' ) echo 'checked="checked"'; ?> /> <?php _e('Yes') ?>
									<input type='radio' style='width:20px;' name='blog[archived]' value='0' <?php if( $details['archived'] == '0' ) echo 'checked="checked"'; ?> /> <?php _e('No') ?>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row"><?php _e( 'Mature' ); ?></th>
								<td>
									<input type='radio' style='width:20px;' name='blog[mature]' value='1' <?php if( $details['mature'] == '1' ) echo 'checked="checked"'; ?> /> <?php _e('Yes') ?>
									<input type='radio' style='width:20px;' name='blog[mature]' value='0' <?php if( $details['mature'] == '0' ) echo 'checked="checked"'; ?> /> <?php _e('No') ?>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row"><?php _e( 'Spam' ); ?></th>
								<td>
									<input type='radio' style='width:20px;' name='blog[spam]' value='1' <?php if( $details['spam'] == '1' ) echo 'checked="checked"'; ?> /> <?php _e('Yes') ?>
									<input type='radio' style='width:20px;' name='blog[spam]' value='0' <?php if( $details['spam'] == '0' ) echo 'checked="checked"'; ?> /> <?php _e('No') ?>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row"><?php _e( 'Deleted' ); ?></th>
								<td>
									<input type='radio' style='width:20px;' name='blog[deleted]' value='1' <?php if( $details['deleted'] == '1' ) echo 'checked="checked"'; ?> /> <?php _e('Yes') ?>
									<input type='radio' style='width:20px;' name='blog[deleted]' value='0' <?php if( $details['deleted'] == '0' ) echo 'checked="checked"'; ?> /> <?php _e('No') ?>
								</td>
							</tr>
						</table>
						<p class="submit" style="margin:-15px 0 -5px 230px;"><input type="submit" name="Submit" value="<?php esc_attr_e('Update Options') ?>" /></p>
			</div></div>

			<div id="blogedit_blogoptions" class="postbox " >
			<h3 class='hndle'><span><?php printf( __('Blog options (%soptions)'), $blog_prefix ); ?></span></h3>
			<div class="inside">
				<table class="form-table">
							<?php
							$editblog_default_role = 'subscriber';
							foreach ( $options as $key => $val ) {
								if( $val['option_name'] == 'default_role' ) {
									$editblog_default_role = $val['option_value'];
								}
								$disabled = '';
								if ( is_serialized($val['option_value']) ) {
									if ( is_serialized_string($val['option_value']) ) {
										$val['option_value'] = wp_specialchars(maybe_unserialize($val['option_value']), 'single');
									} else {
										$val['option_value'] = "SERIALIZED DATA";
										$disabled = ' disabled="disabled"';
									}
								}
								if ( stristr($val['option_value'], "\r") || stristr($val['option_value'], "\n") || stristr($val['option_value'], "\r\n") ) {
								?>
									<tr class="form-field">
										<th scope="row"><?php echo ucwords( str_replace( "_", " ", $val['option_name'] ) ) ?></th>
										<td><textarea rows="5" cols="40" name="option[<?php echo $val['option_name'] ?>]" type="text" id="<?php echo $val['option_name'] ?>"<?php echo $disabled ?>><?php echo wp_specialchars( stripslashes( $val['option_value'] ), 1 ) ?></textarea></td>
									</tr>
								<?php
								} else {
								?>
									<tr class="form-field">
										<th scope="row"><?php echo ucwords( str_replace( "_", " ", $val['option_name'] ) ) ?></th>
										<td><input name="option[<?php echo $val['option_name'] ?>]" type="text" id="<?php echo $val['option_name'] ?>" value="<?php echo esc_attr( stripslashes( $val['option_value'] ), 1 ) ?>" size="40" <?php echo $disabled ?> /></td>
									</tr>
								<?php
								}
							} // End foreach
							?>
						</table>
						<p class="submit" style="margin:-15px 0 -5px 230px;"><input type="submit" name="Submit" value="<?php esc_attr_e('Update Options') ?>" /></p>
			</div></div>
			</div>

			<div class='metabox-holder' style='width:49%;float:right;'>
			<?php
					// Blog Themes
					$themes = get_themes();
					$blog_allowed_themes = wpmu_get_blog_allowedthemes( $id );
					$allowed_themes = get_site_option( "allowedthemes" );
					if( $allowed_themes == false ) {
						$allowed_themes = array_keys( $themes );
					}
					$out = '';
					foreach( $themes as $key => $theme ) {
						$theme_key = wp_specialchars( $theme['Stylesheet'] );
						if( isset($allowed_themes[$theme_key] ) == false ) {
							$checked = ( isset($blog_allowed_themes[ $theme_key ]) ) ? 'checked="checked"' : '';
							$out .= '<tr class="form-field form-required">
									<th title="'.htmlspecialchars( $theme["Description"] ).'" scope="row">'.$key.'</th>
									<td><input name="theme['.$theme_key.']" type="checkbox" style="width:20px;" value="on" '.$checked.'/>' . __( 'Active' ) . '</td>
								</tr>';
						}
					}

					if ( $out != '' ) {
			?>
			<div id="blogedit_blogthemes" class="postbox">
			<h3 class='hndle'><span><?php _e('Blog Themes'); ?></span></h3>
			<div class="inside">
				<table class="form-table">
					<tr><th style="font-weight:bold;"><?php _e('Theme'); ?></th></tr>
					<?php echo $out; ?>
				</table>
				<p class="submit" style="margin:-15px 0 -5px 230px;"><input type="submit" name="Submit" value="<?php esc_attr_e('Update Options') ?>" /></p>
			</div></div>
			<?php } ?>

			<?php
					// Blog users
					$blogusers = get_users_of_blog( $id );
					if( is_array( $blogusers ) ) {
						echo '<div id="blogedit_blogusers" class="postbox"><h3 class="hndle"><span>' . __('Blog Users') . '</span></h3><div class="inside">';
						echo '<table class="form-table">';
						echo "<tr><th>" . __('User') . "</th><th>" . __('Role') . "</th><th>" . __('Password') . "</th><th>" . __('Remove') . "</th></tr>";
						reset($blogusers);
						foreach ( (array) $blogusers as $key => $val ) {
							$t = @unserialize( $val->meta_value );
							if( is_array( $t ) ) {
								reset( $t );
								$existing_role = key( $t );
							}
							echo '<tr><td><a href="user-edit.php?user_id=' . $val->user_id . '">' . $val->user_login . '</a></td>';
							if( $val->user_id != $current_user->data->ID ) {
								?>
								<td>
									<select name="role[<?php echo $val->user_id ?>]" id="new_role"><?php
										foreach( $editblog_roles as $role => $role_assoc ){
											$name = translate_with_context($role_assoc['name']);
											$selected = ( $role == $existing_role ) ? 'selected="selected"' : '';
											echo "<option {$selected} value=\"" . esc_attr($role) . "\">{$name}</option>";
										}
										?>
									</select>
								</td>
								<td>
										<input type='text' name='user_password[<?php echo $val->user_id ?>]' />
								</td>
								<?php
								echo '<td><input title="' . __('Click to remove user') . '" type="checkbox" name="blogusers[' . $val->user_id . ']" /></td>';
							} else {
								echo "<td><strong>" . __ ('N/A') . "</strong></td><td><strong>" . __ ('N/A') . "</strong></td><td><strong>" . __('N/A') . "</strong></td>";
							}
							echo '</tr>';
						}
						echo "</table>";
						echo '<p class="submit" style="margin:-15px 0 -5px 230px;"><input type="submit" name="Submit" value="' . esc_attr__('Update Options') . '" /></p>';
						echo "</div></div>";
					}
			?>

			<div id="blogedit_blogadduser" class="postbox">
			<h3 class='hndle'><span><?php _e('Add a new user'); ?></span></h3>
			<div class="inside">
				<p style="margin:10px 0 0px;padding:0px 10px 10px;border-bottom:1px solid #DFDFDF;"><?php _e('Enter the username of an existing user and hit <em>Update Options</em> to add the user.') ?></p>
				<table class="form-table">
						<tr>
							<th scope="row"><?php _e('User&nbsp;Login:') ?></th>
							<td><input type="text" name="newuser" id="newuser" /></td>
						</tr>
						<tr>
							<th scope="row"><?php _e('Role:') ?></th>
							<td>
								<select name="new_role" id="new_role">
								<?php
								reset( $editblog_roles );
								foreach( $editblog_roles as $role => $role_assoc ){
									$name = translate_with_context($role_assoc['name']);
									$selected = ( $role == $editblog_default_role ) ? 'selected="selected"' : '';
									echo "<option {$selected} value=\"" . esc_attr($role) . "\">{$name}</option>";
								}
								?>
								</select>
							</td>
						</tr>
					</table>
				<p class="submit" style="margin:-15px 0 -5px 230px;"><input type="submit" name="Submit" value="<?php esc_attr_e('Update Options') ?>" /></p>
			</div></div>

			<div id="blogedit_miscoptions" class="postbox">
			<h3 class='hndle'><span><?php _e('Misc Blog Actions') ?></span></h3>
			<div class="inside">
				<table class="form-table">
						<?php do_action( 'wpmueditblogaction', $id ); ?>
					</table>
				<p class="submit" style="margin:-15px 0 -5px 230px;"><input type="submit" name="Submit" value="<?php esc_attr_e('Update Options') ?>" /></p>
			</div></div>

			</div>

			<div style="clear:both;"></div>
		</form>
		</div>
		<?php
	break;

	// List blogs
	default:
		$apage = ( isset($_GET['apage'] ) && intval( $_GET['apage'] ) ) ? absint( $_GET['apage'] ) : 1;
		$num = ( isset($_GET['num'] ) && intval( $_GET['num'] ) ) ? absint( $_GET['num'] ) : 15;
		$s = wp_specialchars( trim( $_GET[ 's' ] ) );
		$like_s = like_escape($s);

		$query = "SELECT * FROM {$wpdb->blogs} WHERE site_id = '{$wpdb->siteid}' ";

		if( isset($_GET['blog_name']) ) {
			$query .= " AND ( {$wpdb->blogs}.domain LIKE '%{$like_s}%' OR {$wpdb->blogs}.path LIKE '%{$like_s}%' ) ";
		} elseif( isset($_GET['blog_id']) ) {
			$query .= " AND   blog_id = '". absint( $_GET['blog_id'] )."' ";
		} elseif( isset($_GET['blog_ip']) ) {
			$query = "SELECT *
				FROM {$wpdb->blogs}, {$wpdb->registration_log}
				WHERE site_id = '{$wpdb->siteid}'
				AND {$wpdb->blogs}.blog_id = {$wpdb->registration_log}.blog_id
				AND {$wpdb->registration_log}.IP LIKE ('%{$like_s}%')";
		}

		if( isset( $_GET['sortby'] ) == false ) {
			$_GET['sortby'] = 'id';
		}

		if( $_GET['sortby'] == 'registered' ) {
			$query .= ' ORDER BY registered ';
		} elseif( $_GET['sortby'] == 'id' ) {
			$query .= ' ORDER BY ' . $wpdb->blogs . '.blog_id ';
		} elseif( $_GET['sortby'] == 'lastupdated' ) {
			$query .= ' ORDER BY last_updated ';
		} elseif( $_GET['sortby'] == 'blogname' ) {
			$query .= ' ORDER BY domain ';
		}

		$query .= ( $_GET['order'] == 'DESC' ) ? 'DESC' : 'ASC';

		if( !empty($s) ) {
			$total = $wpdb->get_var( str_replace('SELECT *', 'SELECT COUNT(blog_id)', $query) );
		} else {
			$total = $wpdb->get_var( "SELECT COUNT(blog_id) FROM {$wpdb->blogs} WHERE site_id = '{$wpdb->siteid}' ");
		}

		$query .= " LIMIT " . intval( ( $apage - 1 ) * $num) . ", " . intval( $num );
		$blog_list = $wpdb->get_results( $query, ARRAY_A );

		// Pagination
		$url2 = "&amp;order=" . $_GET['order'] . "&amp;sortby=" . $_GET['sortby'] . "&amp;s=";
		if( $_GET[ 'blog_ip' ] ) {
			$url2 .= "&amp;ip_address=" . urlencode( $s );
		} else {
			$url2 .= $s . "&amp;ip_address=" . urlencode( $s );
		}
		$blog_navigation = paginate_links( array(
			'base' => add_query_arg( 'apage', '%#%' ).$url2,
			'format' => '',
			'total' => ceil($total / $num),
			'current' => $apage
		));
		?>

		<div class="wrap" style="position:relative;">
		<h2><?php _e('Blogs') ?></h2>

		<form action="ms-sites.php" method="get" id="ms-search">
			<input type="hidden" name="action" value="blogs" />
			<input type="text" name="s" value="<?php if (isset($_GET['s'])) echo stripslashes( esc_attr( $s, 1 ) ); ?>" size="17" />
			<input type="submit" class="button" name="blog_name" value="<?php esc_attr_e('Search blogs by name') ?>" />
			<input type="submit" class="button" name="blog_id" value="<?php esc_attr_e('by blog ID') ?>" />
			<input type="submit" class="button" name="blog_ip" value="<?php esc_attr_e('by IP address') ?>" />
		</form>

		<form id="form-blog-list" action="ms-edit.php?action=allblogs" method="post">

		<div class="tablenav">
			<?php if ( $blog_navigation ) echo "<div class='tablenav-pages'>$blog_navigation</div>"; ?>

			<div class="alignleft">
				<input type="submit" value="<?php esc_attr_e('Delete') ?>" name="allblog_delete" class="button-secondary delete" />
				<input type="submit" value="<?php esc_attr_e('Mark as Spam') ?>" name="allblog_spam" class="button-secondary" />
				<input type="submit" value="<?php esc_attr_e('Not Spam') ?>" name="allblog_notspam" class="button-secondary" />
				<?php wp_nonce_field( 'allblogs' ); ?>
				<br class="clear" />
			</div>
		</div>

		<br class="clear" />

		<?php if( isset($_GET['s']) && !empty($_GET['s']) ) : ?>
			<p><a href="ms-users.php?action=users&s=<?php echo urlencode( stripslashes( $s ) ) ?>"><?php _e('Search Users:') ?> <strong><?php echo stripslashes( $s ); ?></strong></a></p>
		<?php endif; ?>

		<?php
		// define the columns to display, the syntax is 'internal name' => 'display name'
		$blogname_columns = ( is_subdomain_install() ) ? __('Domain') : __('Path');
		$posts_columns = array(
			'id'           => __('ID'),
			'blogname'     => $blogname_columns,
			'lastupdated'  => __('Last Updated'),
			'registered'   => __('Registered'),
			'users'        => __('Users')
		);

		if( has_filter( 'wpmublogsaction' ) )
			$posts_columns['plugins'] = __('Actions');

		$posts_columns = apply_filters('wpmu_blogs_columns', $posts_columns);

		$sortby_url = "s=";
		if( $_GET[ 'blog_ip' ] ) {
			$sortby_url .= "&ip_address=" . urlencode( $s );
		} else {
			$sortby_url .= urlencode( $s ) . "&ip_address=" . urlencode( $s );
		}
		?>

		<table width="100%" cellpadding="3" cellspacing="3" class="widefat">
			<thead>
				<tr>
				<th scope="col" class="check-column"></th>
				<?php foreach($posts_columns as $column_id => $column_display_name) {
					$column_link = "<a href='ms-sites.php?{$sortby_url}&amp;sortby={$column_id}&amp;";
					if( $_GET['sortby'] == $column_id ) {
						$column_link .= $_GET[ 'order' ] == 'DESC' ? 'order=ASC&amp;' : 'order=DESC&amp;';
					}
					$column_link .= "apage={$apage}'>{$column_display_name}</a>";

					$col_url = ($column_id == 'users' || $column_id == 'plugins') ? $column_display_name : $column_link;
					?>
					<th scope="col"><?php echo $col_url ?></th>
				<?php } ?>
				</tr>
			</thead>
			<tbody id="the-list">
			<?php
			if ($blog_list) {
				$bgcolor = $class = '';
				$status_list = array( "archived" => "#fee", "spam" => "#faa", "deleted" => "#f55" );
				foreach ($blog_list as $blog) {
					$class = ('alternate' == $class) ? '' : 'alternate';
					reset( $status_list );

					$bgcolour = "";
					foreach ( $status_list as $status => $col ) {
						if( get_blog_status( $blog['blog_id'], $status ) == 1 ) {
							$bgcolour = "style='background: $col'";
						}
					}
					echo "<tr $bgcolour class='$class'>";

					$blogname = ( is_subdomain_install() ) ? str_replace('.'.$current_site->domain, '', $blog['domain']) : $blog['path'];
					foreach( $posts_columns as $column_name=>$column_display_name ) {
						switch($column_name) {
							case 'id': ?>
								<th scope="row" class="check-column">
									<input type='checkbox' id='blog_<?php echo $blog['blog_id'] ?>' name='allblogs[]' value='<?php echo esc_attr($blog['blog_id']) ?>' />
								</th>
								<th scope="row">
									<?php echo $blog['blog_id'] ?>
								</th>
							<?php
							break;

							case 'blogname': ?>
								<td valign="top">
									<a href="ms-sites.php?action=editblog&amp;id=<?php echo $blog['blog_id'] ?>" class="edit"><?php echo $blogname; ?></a>
									<br/>
									<?php
									$controlActions	= array();
									$controlActions[]	= '<a href="ms-sites.php?action=editblog&amp;id=' . $blog['blog_id'] . '" class="edit">' . __('Edit') . '</a>';
									$controlActions[]	= "<a href='{$protocol}{$blog['domain']}{$blog['path']}wp-admin/' class='edit'>" . __('Backend') . '</a>';

									if( get_blog_status( $blog['blog_id'], "deleted" ) == '1' )
										$controlActions[]	= '<a class="delete" href="ms-edit.php?action=confirm&amp;action2=activateblog&amp;ref=' . urlencode( $_SERVER['REQUEST_URI'] ) . '&amp;id=' . $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( "You are about to activate the blog %s" ), $blogname ) ) . '">' . __('Activate') . '</a>';
									else
										$controlActions[]	= '<a class="delete" href="ms-edit.php?action=confirm&amp;action2=deactivateblog&amp;ref=' . urlencode( $_SERVER['REQUEST_URI'] ) . '&amp;id=' . $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( "You are about to deactivate the blog %s" ), $blogname ) ) . '">' . __('Deactivate') . '</a>';

									if( get_blog_status( $blog['blog_id'], "archived" ) == '1' )
										$controlActions[]	= '<a class="delete" href="ms-edit.php?action=confirm&amp;action2=unarchiveblog&amp;id=' .  $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( "You are about to unarchive the blog %s" ), $blogname ) ) . '">' . __('Unarchive') . '</a>';
									else
										$controlActions[]	= '<a class="delete" href="ms-edit.php?action=confirm&amp;action2=archiveblog&amp;id=' . $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( "You are about to archive the blog %s" ), $blogname ) ) . '">' . __('Archive') . '</a>';

									if( get_blog_status( $blog['blog_id'], "spam" ) == '1' )
										$controlActions[]	= '<a class="delete" href="ms-edit.php?action=confirm&amp;action2=unspamblog&amp;id=' . $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( "You are about to unspam the blog %s" ), $blogname ) ) . '">' . __('Not Spam') . '</a>';
									else
										$controlActions[]	= '<a class="delete" href="ms-edit.php?action=confirm&amp;action2=spamblog&amp;id=' . $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( "You are about to mark the blog %s as spam" ), $blogname ) ) . '">' . __("Spam") . '</a>';

									$controlActions[]	= '<a class="delete" href="ms-edit.php?action=confirm&amp;action2=deleteblog&amp;id=' . $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( "You are about to delete the blog %s" ), $blogname ) ) . '">' . __("Delete") . '</a>';

									$controlActions[]	= "<a href='http://{$blog['domain']}{$blog['path']}' rel='permalink'>" . __('Visit') . '</a>';
									?>

									<?php if (count($controlActions)) : ?>
									<div class="row-actions">
										<?php echo implode(' | ', $controlActions); ?>
									</div>
									<?php endif; ?>
								</td>
							<?php
							break;

							case 'lastupdated': ?>
								<td valign="top">
									<?php echo ( $blog['last_updated'] == '0000-00-00 00:00:00' ) ? __("Never") : mysql2date(__('Y-m-d \<\b\r \/\> g:i:s a'), $blog['last_updated']); ?>
								</td>
							<?php
							break;
							case 'registered': ?>
								<td valign="top">
									<?php echo mysql2date(__('Y-m-d \<\b\r \/\> g:i:s a'), $blog['registered']); ?>
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
											$blogusers_warning = __( 'Only showing first 5 users.' ) . ' <a href="' . $protocol . $blog[ 'domain' ] . $blog[ 'path' ] . 'wp-admin/users.php">' . __( 'More' ) . '</a>';
										}
										foreach ( $blogusers as $key => $val ) {
											echo '<a href="user-edit.php?user_id=' . $val->user_id . '">' . $val->user_login . '</a> ('.$val->user_email.')<br />';
										}
										if( $blogusers_warning != '' ) {
											echo '<strong>' . $blogusers_warning . '</strong><br />';
										}
									}
									?>
								</td>
							<?php
							break;

							case 'plugins': ?>
								<?php if( has_filter( 'wpmublogsaction' ) ) { ?>
								<td valign="top">
									<?php do_action( "wpmublogsaction", $blog['blog_id'] ); ?>
								</td>
								<?php } ?>
							<?php break;

							default: ?>
								<?php if( has_filter( 'manage_blogs_custom_column' ) ) { ?>
								<td valign="top">
									<?php do_action('manage_blogs_custom_column', $column_name, $blog['blog_id']); ?>
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
				<tr style='background-color: <?php echo $bgcolor; ?>'>
					<td colspan="8"><?php _e('No blogs found.') ?></td>
				</tr>
			<?php
			} // end if ($blogs)
			?>

			</tbody>
		</table>
		</form>
		</div>

		<div class="wrap">
			<a name="form-add-blog"></a>
			<h2><?php _e('Add Blog') ?></h2>
			<form method="post" action="ms-edit.php?action=addblog">
				<?php wp_nonce_field('add-blog') ?>
				<table class="form-table">
					<tr class="form-field form-required">
						<th style="text-align:center;" scope='row'><?php _e('Blog Address') ?></th>
						<td>
						<?php if ( is_subdomain_install() ) { ?>
							<input name="blog[domain]" type="text" title="<?php _e('Domain') ?>"/>.<?php echo $current_site->domain;?>
						<?php } else {
							echo $current_site->domain . $current_site->path ?><input name="blog[domain]" type="text" title="<?php _e('Domain') ?>"/>
						<?php }
						echo "<p>" . __( 'Only the characters a-z and 0-9 recommended.' ) . "</p>";
						?>
						</td>
					</tr>
					<tr class="form-field form-required">
						<th style="text-align:center;" scope='row'><?php _e('Blog Title') ?></th>
						<td><input name="blog[title]" type="text" size="20" title="<?php _e('Title') ?>"/></td>
					</tr>
					<tr class="form-field form-required">
						<th style="text-align:center;" scope='row'><?php _e('Admin Email') ?></th>
						<td><input name="blog[email]" type="text" size="20" title="<?php _e('Email') ?>"/></td>
					</tr>
					<tr class="form-field">
						<td colspan='2'><?php _e('A new user will be created if the above email address is not in the database.') ?><br /><?php _e('The username and password will be mailed to this email address.') ?></td>
					</tr>
				</table>
				<p class="submit">
					<input class="button" type="submit" name="go" value="<?php esc_attr_e('Add Blog') ?>" /></p>
			</form>
		</div>
		<?php
	break;
} // end switch( $action )

include('admin-footer.php'); ?>
