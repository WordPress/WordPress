<?php
require_once( './admin.php' );

if ( !is_multisite() )
	wp_die( __( 'Multisite support is not enabled.' ) );

// @todo Create a delete blog cap.
if ( ! current_user_can( 'manage_options' ) )
	wp_die(__( 'You do not have sufficient permissions to delete this site.'));
	
if ( isset( $_GET['h'] ) && $_GET['h'] != '' && get_option( 'delete_blog_hash' ) != false ) {
	if ( get_option( 'delete_blog_hash' ) == $_GET['h'] ) {
		wpmu_delete_blog( $wpdb->blogid );
		wp_die( sprintf( __( 'Thank you for using %s, your site has been deleted. Happy trails to you until we meet again.' ), $current_site->site_name ) );
	} else {
		wp_die( __( "I'm sorry, the link you clicked is stale. Please select another option." ) );
	}
}

$title = __( 'Delete Site' );
$parent_file = 'tools.php';
require_once( './admin-header.php' );

echo '<div class="wrap">';
screen_icon();
echo '<h2>' . esc_html( $title ) . '</h2>';

if ( isset( $_POST['action'] ) && $_POST['action'] == 'deleteblog' && isset( $_POST['confirmdelete'] ) && $_POST['confirmdelete'] == '1' ) {
	$hash = wp_generate_password( 20, false );
	update_option( 'delete_blog_hash', $hash );

	$url_delete = esc_url( admin_url( 'ms-delete-site.php?h=' . $hash ) );

	$content = apply_filters( 'delete_site_email_content', __( "Dear User,
You recently clicked the 'Delete Site' link on your site and filled in a
form on that page.
If you really want to delete your site, click the link below. You will not
be asked to confirm again so only click this link if you are absolutely certain:
###URL_DELETE###

If you delete your site, please consider opening a new site here
some time in the future! (But remember your current site and username
are gone forever.)

Thanks for using the site,
Webmaster
###SITE_NAME###" ) );

	$content = str_replace( '###URL_DELETE###', $url_delete, $content );
	$content = str_replace( '###SITE_NAME###', $current_site->site_name, $content );

	wp_mail( get_option( 'admin_email' ), "[ " . get_option( 'blogname' ) . " ] ".__( 'Delete My Site' ), $content );
	?>

	<p><?php _e( 'Thank you. Please check your email for a link to confirm your action. Your site will not be deleted until this link is clicked. ') ?></p>

<?php } else {
	?>
	<p><?php printf( __( 'If you do not want to use your %s site any more, you can delete it using the form below. When you click <strong>Delete My Site Permanently</strong> you will be sent an email with a link in it. Click on this link to delete your site.'), $current_site->site_name); ?></p>
	<p><?php _e( 'Remember, once deleted your site cannot be restored.' ) ?></p>

	<form method="post" name="deletedirect">
		<input type="hidden" name="action" value="deleteblog" />
		<p><input id="confirmdelete" type="checkbox" name="confirmdelete" value="1" /> <label for="confirmdelete"><strong><?php printf( __( "I'm sure I want to permanently disable my site, and I am aware I can never get it back or use %s again." ), is_subdomain_install() ? $current_blog->domain : $current_site->domain . $current_site->path ); ?></strong></label></p>
		<p class="submit"><input type="submit" value="<?php esc_attr_e( 'Delete My Site Permanently' ) ?>" /></p>
	</form>
	<?php
}
echo '</div>';

include( './admin-footer.php' );
?>
