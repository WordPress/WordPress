<?php
/**
 * Sites List Table class.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 */
class WP_MS_Sites_List_Table extends WP_List_Table {

	function WP_MS_Sites_List_Table() {
		parent::WP_List_Table( array(
			'plural' => 'sites',
		) );
	}

	function check_permissions() {
		if ( ! current_user_can( 'manage_sites' ) )
			wp_die( __( 'You do not have permission to access this page.' ) );
	}

	function prepare_items() {
		global $s, $mode, $wpdb, $current_site;

		$mode = ( empty( $_REQUEST['mode'] ) ) ? 'list' : $_REQUEST['mode'];

		$per_page = $this->get_items_per_page( 'sites_network_per_page' );

		$pagenum = $this->get_pagenum();

		$s = isset( $_REQUEST['s'] ) ? stripslashes( trim( $_REQUEST[ 's' ] ) ) : '';
		$wild = '';
		if ( false !== strpos($s, '*') ) {
			$wild = '%';
			$s = trim($s, '*');
		}

		$like_s = esc_sql( like_escape( $s ) );

		$large_network = false;
		// If the network is large and a search is not being performed, show only the latest blogs with no paging in order
		// to avoid expensive count queries.
		if ( !$s && ( get_blog_count() >= 10000 ) ) {
			if ( !isset($_REQUEST['orderby']) )
				$_GET['orderby'] = $_REQUEST['orderby'] = 'id';
			if ( !isset($_REQUEST['order']) )
				$_GET['order'] = $_REQUEST['order'] = 'DESC';
			$large_network = true;
		}

		$query = "SELECT * FROM {$wpdb->blogs} WHERE site_id = '{$wpdb->siteid}' ";

		if ( empty($s) ) {
			// Nothing to do.
		} elseif ( preg_match('/^[0-9]+\./', $s) ) {
			// IP address
			$reg_blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->registration_log} WHERE {$wpdb->registration_log}.IP LIKE ( '{$like_s}$wild' )" );

			if ( !$reg_blog_ids )
				$reg_blog_ids = array( 0 );

			$query = "SELECT *
				FROM {$wpdb->blogs}
				WHERE site_id = '{$wpdb->siteid}'
				AND {$wpdb->blogs}.blog_id IN (" . implode( ', ', $reg_blog_ids ) . ")";
		} else {
			if ( is_numeric($s) ) {
				$query .= " AND ( {$wpdb->blogs}.blog_id = '{$like_s}' )";
			} elseif ( is_subdomain_install() ) {
				$blog_s = str_replace( '.' . $current_site->domain, '', $like_s );
				$blog_s .= $wild . '.' . $current_site->domain;
				$query .= " AND ( {$wpdb->blogs}.domain LIKE '$blog_s' ) ";
			} else {
				if ( $like_s != trim('/', $current_site->path) )
					$blog_s = $current_site->path .= $like_s . $wild . '/';
				else
					$blog_s = $like_s;
				$query .= " AND  ( {$wpdb->blogs}.path LIKE '$blog_s' )";
			}
		}

		$order_by = isset( $_REQUEST['orderby'] ) ? $_REQUEST['orderby'] : 'id';
		if ( $order_by == 'registered' ) {
			$query .= ' ORDER BY registered ';
		} elseif ( $order_by == 'lastupdated' ) {
			$query .= ' ORDER BY last_updated ';
		} elseif ( $order_by == 'blogname' ) {
			$query .= ' ORDER BY domain ';
		} else {
			$order_by = null;
		}

		if ( isset( $order_by ) ) {
			$order = ( isset( $_REQUEST['order'] ) && 'DESC' == strtoupper( $_REQUEST['order'] ) ) ? "DESC" : "ASC";
			$query .= $order;
		}

		// Don't do an unbounded count on large networks
		if ( ! $large_network )
			$total = $wpdb->get_var( str_replace( 'SELECT *', 'SELECT COUNT( blog_id )', $query ) );

		$query .= " LIMIT " . intval( ( $pagenum - 1 ) * $per_page ) . ", " . intval( $per_page );
		$this->items = $wpdb->get_results( $query, ARRAY_A );

		if ( $large_network )
			$total = count($this->items);

		$this->set_pagination_args( array(
			'total_items' => $total,
			'per_page' => $per_page,
		) );
	}

	function no_items() {
		_e( 'No sites found.' );
	}

	function get_bulk_actions() {
		$actions = array();
		if ( current_user_can( 'delete_sites' ) )
			$actions['delete'] = __( 'Delete' );
		$actions['spam'] = _x( 'Mark as Spam', 'site' );
		$actions['notspam'] = _x( 'Not Spam', 'site' );

		return $actions;
	}

	function pagination( $which ) {
		global $mode;

		parent::pagination( $which );

		if ( 'top' == $which )
			$this->view_switcher( $mode );
	}

	function get_columns() {
		$blogname_columns = ( is_subdomain_install() ) ? __( 'Domain' ) : __( 'Path' );
		$sites_columns = array(
			'cb'          => '<input type="checkbox" />',
			'blogname'    => $blogname_columns,
			'lastupdated' => __( 'Last Updated' ),
			'registered'  => _x( 'Registered', 'site' ),
			'users'       => __( 'Users' )
		);

		if ( has_filter( 'wpmublogsaction' ) )
			$sites_columns['plugins'] = __( 'Actions' );

		$sites_columns = apply_filters( 'wpmu_blogs_columns', $sites_columns );

		return $sites_columns;
	}

	function get_sortable_columns() {
		return array(
			'blogname'    => 'blogname',
			'lastupdated' => 'lastupdated',
			'registered'  => 'id',
		);
	}

	function display_rows() {
		global $current_site, $mode;

		$status_list = array(
			'archived' => array( 'site-archived', __( 'Archived' ) ),
			'spam'     => array( 'site-spammed', _x( 'Spam', 'site' ) ),
			'deleted'  => array( 'site-deleted', __( 'Deleted' ) ),
			'mature'   => array( 'site-mature', __( 'Mature' ) )
		);

		$class = '';
		foreach ( $this->items as $blog ) {
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

			list( $columns, $hidden ) = $this->get_column_info();

			foreach ( $columns as $column_name => $column_display_name ) {
				$style = '';
				if ( in_array( $column_name, $hidden ) )
					$style = ' style="display:none;"';

				switch ( $column_name ) {
					case 'cb': ?>
						<th scope="row" class="check-column">
							<input type="checkbox" id="blog_<?php echo $blog['blog_id'] ?>" name="allblogs[]" value="<?php echo esc_attr( $blog['blog_id'] ) ?>" />
						</th>
					<?php
					break;

					case 'id':?>
						<th valign="top" scope="row">
							<?php echo $blog['blog_id'] ?>
						</th>
					<?php
					break;

					case 'blogname':
						echo "<td class='column-$column_name $column_name'$style>"; ?>
							<a href="<?php echo esc_url( network_admin_url( 'site-info.php?id=' . $blog['blog_id'] ) ); ?>" class="edit"><?php echo $blogname . $blog_state; ?></a>
							<?php
							if ( 'list' != $mode )
								echo '<p>' . sprintf( _x( '%1$s &#8211; <em>%2$s</em>', '%1$s: site name. %2$s: site tagline.' ), get_blog_option( $blog['blog_id'], 'blogname' ), get_blog_option( $blog['blog_id'], 'blogdescription ' ) ) . '</p>';

							// Preordered.
							$actions = array(
								'edit' => '', 'backend' => '',
								'activate' => '', 'deactivate' => '',
								'archive' => '', 'unarchive' => '',
								'spam' => '', 'unspam' => '',
								'delete' => '',
								'visit' => '',
							);

							$actions['edit']	= '<span class="edit"><a href="' . esc_url( network_admin_url( 'site-info.php?id=' . $blog['blog_id'] ) ) . '">' . __( 'Edit' ) . '</a></span>';
							$actions['backend']	= "<span class='backend'><a href='" . esc_url( get_admin_url( $blog['blog_id'] ) ) . "' class='edit'>" . __( 'Dashboard' ) . '</a></span>';
							if ( $current_site->blog_id != $blog['blog_id'] ) {
								if ( get_blog_status( $blog['blog_id'], 'deleted' ) == '1' )
									$actions['activate']	= '<span class="activate"><a href="' . esc_url( network_admin_url( 'edit.php?action=confirm&amp;action2=activateblog&amp;id=' . $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( 'You are about to activate the site %s' ), $blogname ) ) ) ) . '">' . __( 'Activate' ) . '</a></span>';
								else
									$actions['deactivate']	= '<span class="activate"><a href="' . esc_url( network_admin_url( 'edit.php?action=confirm&amp;action2=deactivateblog&amp;id=' . $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( 'You are about to deactivate the site %s' ), $blogname ) ) ) ) . '">' . __( 'Deactivate' ) . '</a></span>';

								if ( get_blog_status( $blog['blog_id'], 'archived' ) == '1' )
									$actions['unarchive']	= '<span class="archive"><a href="' . esc_url( network_admin_url( 'edit.php?action=confirm&amp;action2=unarchiveblog&amp;id=' .  $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( 'You are about to unarchive the site %s.' ), $blogname ) ) ) ) . '">' . __( 'Unarchive' ) . '</a></span>';
								else
									$actions['archive']	= '<span class="archive"><a href="' . esc_url( network_admin_url( 'edit.php?action=confirm&amp;action2=archiveblog&amp;id=' . $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( 'You are about to archive the site %s.' ), $blogname ) ) ) ) . '">' . _x( 'Archive', 'verb; site' ) . '</a></span>';

								if ( get_blog_status( $blog['blog_id'], 'spam' ) == '1' )
									$actions['unspam']	= '<span class="spam"><a href="' . esc_url( network_admin_url( 'edit.php?action=confirm&amp;action2=unspamblog&amp;id=' . $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( 'You are about to unspam the site %s.' ), $blogname ) ) ) ) . '">' . _x( 'Not Spam', 'site' ) . '</a></span>';
								else
									$actions['spam']	= '<span class="spam"><a href="' . esc_url( network_admin_url( 'edit.php?action=confirm&amp;action2=spamblog&amp;id=' . $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( 'You are about to mark the site %s as spam.' ), $blogname ) ) ) ) . '">' . _x( 'Spam', 'site' ) . '</a></span>';

								if ( current_user_can( 'delete_site', $blog['blog_id'] ) )
									$actions['delete']	= '<span class="delete"><a href="' . esc_url( network_admin_url( 'edit.php?action=confirm&amp;action2=deleteblog&amp;id=' . $blog['blog_id'] . '&amp;msg=' . urlencode( sprintf( __( 'You are about to delete the site %s.' ), $blogname ) ) ) ) . '">' . __( 'Delete' ) . '</a></span>';
							}

							$actions['visit']	= "<span class='view'><a href='" . esc_url( get_home_url( $blog['blog_id'] ) ) . "' rel='permalink'>" . __( 'Visit' ) . '</a></span>';
							$actions = array_filter( $actions );
							echo $this->row_actions( $actions );
					?>
						</td>
					<?php
					break;

					case 'lastupdated':
						echo "<td valign='top'class='$column_name column-$column_name'$style>";
							if ( 'list' == $mode )
								$date = 'Y/m/d';
							else
								$date = 'Y/m/d \<\b\r \/\> g:i:s a';
							echo ( $blog['last_updated'] == '0000-00-00 00:00:00' ) ? __( 'Never' ) : mysql2date( $date, $blog['last_updated'] ); ?>
						</td>
					<?php
					break;
				case 'registered':
						echo "<td valign='top'class='$column_name column-$column_name'$style>";
						if ( $blog['registered'] == '0000-00-00 00:00:00' )
							echo '&#x2014;';
						else
							echo mysql2date( $date, $blog['registered'] );
						?>
						</td>
					<?php
					break;
				case 'users':
						echo "<td valign='top'class='$column_name column-$column_name'$style>";
							$blogusers = get_users( array( 'blog_id' => $blog['blog_id'], 'number' => 6) );
							if ( is_array( $blogusers ) ) {
								$blogusers_warning = '';
								if ( count( $blogusers ) > 5 ) {
									$blogusers = array_slice( $blogusers, 0, 5 );
									$blogusers_warning = __( 'Only showing first 5 users.' ) . ' <a href="' . esc_url( get_admin_url( $blog['blog_id'], 'users.php' ) ) . '">' . __( 'More' ) . '</a>';
								}
								foreach ( $blogusers as $user_object ) {
									echo '<a href="' . esc_url( admin_url( 'user-edit.php?user_id=' . $user_object->ID ) ) . '">' . esc_html( $user_object->user_login ) . '</a> ';
									if ( 'list' != $mode )
										echo '( ' . $user_object->user_email . ' )';
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

				default:
					echo "<td class='$column_name column-$column_name'$style>";
					do_action( 'manage_themes_custom_column', $column_name, $theme_key, $theme );
					echo "</td>";
					break;
				}
			}
			?>
			</tr>
			<?php
		}
	}
}

?>
