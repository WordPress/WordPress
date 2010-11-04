<?php
/**
 * Multisite users administration panel.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.0.0
 */

require_once( './admin.php' );

$wp_list_table = get_list_table('WP_MS_Users_List_Table');
$wp_list_table->check_permissions();
$wp_list_table->prepare_items();

$title = __( 'Users' );
$parent_file = 'users.php';

add_screen_option( 'per_page', array('label' => _x( 'Users', 'users per page (screen options)' )) );

add_contextual_help($current_screen,
	'<p>' . __('This table shows all users across the network and the sites to which they are assigned.') . '</p>' .
	'<p>' . __('Hover over any user on the list to make the edit links appear. The Edit link on the left will take you to his or her Edit User profile page; the Edit link on the right by any site name goes to an Edit Site screen for that site.') . '</p>' .
	'<p>' . __('You can also go to the user&#8217;s profile page by clicking on the individual username.') . '</p>' .
	'<p>' . __('You can sort the table by clicking on any of the bold headings and switch between list and excerpt views by using the icons in the upper right.') . '</p>' .
	'<p>' . __('The bulk action will permanently delete selected users, or mark/unmark those selected as spam. Spam users will have posts removed and will be unable to sign up again with the same email addresses.') . '</p>' .
	'<p>' . __('Add User will add that person to this table and send them an email.') . '</p>' .
	'<p>' . __('Users who are signed up to the network without a site are added as subscribers to the main or primary dashboard site, giving them profile pages to manage their accounts. These users will only see Dashboard and My Sites in the main navigation until a site is created for them.') . '</p>' .
	'<p>' . __('You can make an existing user an additional super admin by going to the Edit User profile page and checking the box to grant that privilege.') . '</p>' .
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Super_Admin_Users_SubPanel" target="_blank">Network Users Documentation</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

require_once( '../admin-header.php' );

if ( isset( $_REQUEST['updated'] ) && $_REQUEST['updated'] == 'true' && ! empty( $_REQUEST['action'] ) ) {
	?>
	<div id="message" class="updated"><p>
		<?php
		switch ( $_REQUEST['action'] ) {
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
	?>
<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php esc_html_e( 'Users' );
	if ( current_user_can( 'create_users') ) : ?>
		<a href="<?php echo network_admin_url('user-new.php'); ?>" class="button add-new-h2"><?php echo esc_html_x( 'Add New', 'users' ); ?></a><?php
	endif;
	
	if ( !empty( $usersearch ) )
	printf( '<span class="subtitle">' . __( 'Search results for &#8220;%s&#8221;' ) . '</span>', esc_html( $usersearch ) );
	?>
	</h2>

	<form action="" method="get" class="search-form">
		<p class="search-box">
		<input type="text" name="s" value="<?php echo esc_attr( $usersearch ); ?>" class="search-input" id="user-search-input" />
		<?php submit_button( __( 'Search Users' ), 'button', 'post-query-submit', false ); ?>
		</p>
	</form>

	<form id="form-user-list" action='edit.php?action=allusers' method='post'>
		<?php $wp_list_table->display(); ?>
	</form>
</div>

<?php require_once( '../admin-footer.php' ); ?>