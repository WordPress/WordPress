<?php
/**
 * Tools Administration Screen.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

$is_privacy_guide = ( isset( $_GET['wp-privacy-policy-guide'] ) && current_user_can( 'manage_privacy_options' ) );

if ( $is_privacy_guide ) {
	$title = __( 'Privacy Policy Guide' );

	// "Borrow" xfn.js for now so we don't have to create new files.
	wp_enqueue_script( 'xfn' );

} else {

	$title = __( 'Tools' );

	get_current_screen()->add_help_tab(
		array(
			'id'      => 'converter',
			'title'   => __( 'Categories and Tags Converter' ),
			'content' => '<p>' . __( 'Categories have hierarchy, meaning that you can nest sub-categories. Tags do not have hierarchy and cannot be nested. Sometimes people start out using one on their posts, then later realize that the other would work better for their content.' ) . '</p>' .
			'<p>' . __( 'The Categories and Tags Converter link on this screen will take you to the Import screen, where that Converter is one of the plugins you can install. Once that plugin is installed, the Activate Plugin &amp; Run Importer link will take you to a screen where you can choose to convert tags into categories or vice versa.' ) . '</p>',
		)
	);

	get_current_screen()->set_help_sidebar(
		'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
		'<p>' . __( '<a href="https://codex.wordpress.org/Tools_Screen">Documentation on Tools</a>' ) . '</p>' .
		'<p>' . __( '<a href="https://wordpress.org/support/">Support Forums</a>' ) . '</p>'
	);
}

require_once( ABSPATH . 'wp-admin/admin-header.php' );

?>
<div class="wrap">
<h1><?php echo esc_html( $title ); ?></h1>
<?php

if ( $is_privacy_guide ) {
	?>
	<div class="wp-privacy-policy-guide">
		<?php WP_Privacy_Policy_Content::privacy_policy_guide(); ?>
	</div>
	<?php

} else {

	if ( current_user_can( 'import' ) ) :
		$cats = get_taxonomy( 'category' );
		$tags = get_taxonomy( 'post_tag' );
		if ( current_user_can( $cats->cap->manage_terms ) || current_user_can( $tags->cap->manage_terms ) ) :
			?>
	<div class="card">
		<h2 class="title"><?php _e( 'Categories and Tags Converter' ); ?></h2>
		<p><?php printf( __( 'If you want to convert your categories to tags (or vice versa), use the <a href="%s">Categories and Tags Converter</a> available from the Import screen.' ), 'import.php' ); ?></p>
	</div>
			<?php
	endif;
	endif;

	/**
	 * Fires at the end of the Tools Administration screen.
	 *
	 * @since 2.8.0
	 */
	do_action( 'tool_box' );
}
?>
</div>
<?php
include( ABSPATH . 'wp-admin/admin-footer.php' );
