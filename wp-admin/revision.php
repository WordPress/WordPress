<?php

require_once('admin.php');

$parent_file = 'edit.php';
$submenu_file = 'edit.php';

wp_reset_vars(array('revision', 'diff', 'restore'));

$revision_id = absint($revision);
$diff        = absint($diff);

if ( $diff ) {
	$restore = false;
	$revision = get_post( $revision_id );
	$post = 'revision' == $revision->post_type ? get_post( $revision->post_parent ) : get_post( $revision_id );
	$left_revision = get_post( $diff );

	// Don't allow reverse diffs?
	if ( strtotime($revision->post_modified_gmt) < strtotime($left_revision->post_modified_gmt) ) {
		wp_redirect( add_query_arg( array( 'diff' => $revision->ID, 'revision' => $diff ) ) );
		exit;
	}

	$h2 = __( 'Compare Revisions of &#8220;%1$s&#8221;' );
	$right = $revision->ID;
	$left  = $left_revision->ID;

	if (
		// They're the same
		$left_revision->ID == $revision->ID
	||
		// They don't have a comment parent (and we're not comparing a revision to it's post)
		( $left_revision->ID != $revision->post_parent && $left_revision->post_parent != $revision->ID && $left_revision->post_parent != $revision->post_parent )
	||
		// Neither is a revision
		( !wp_get_revision( $left_revision->ID ) && !wp_get_revision( $revision->ID ) )
	) {
		wp_redirect( get_edit_post_link( $revision->ID, 'url' ) );
		exit();
	}
} else {
	$revision = wp_get_revision( $revision_id );
	$post = get_post( $revision->post_parent );
	$h2 = __( 'Post Revision for &#8220;%1$s&#8221; created on %2$s' );
	$right = $post->ID;
	$left  = $revision->ID;
}

if ( !$revision || !$post ) {
	wp_redirect("edit.php");
	exit();
}

if ( $restore && current_user_can( 'edit_post', $revision->post_parent ) ) {
	check_admin_referer( "restore-post_$post->ID|$revision->ID" );
	wp_restore_revision( $revision->ID );
	wp_redirect( add_query_arg( array( 'message' => 5, 'revision' => $revision->ID ), get_edit_post_link( $post->ID, 'url' ) ) );
	exit;
}

add_filter( '_wp_revision_field_post_author', 'get_author_name' );

$title = __( 'Post Revision' );

require_once( 'admin-header.php' );

$post_title = '<a href="' . get_edit_post_link() . '">' . get_the_title() . '</a>';
$revision_time = wp_post_revision_time( $revision );
?>

<div class="wrap">

<h2 style="padding-right: 0"><?php printf( $h2, $post_title, $revision_time ); ?></h2>

<table class="form-table ie-fixed">
	<col class="th" />
<?php if ( $diff ) : ?>

<tr id="revision">
	<th scope="row"></th>
	<th scope="col" class="th-full"><?php printf( __('Older: %s'), wp_post_revision_time( $left_revision ) ); ?></td>
	<th scope="col" class="th-full"><?php printf( __('Newer: %s'), wp_post_revision_time( $revision ) ); ?></td>
</tr>

<?php endif;

// use get_post_to_edit ? 
$identical = true;
foreach ( _wp_revision_fields() as $field => $field_title ) :
	if ( !$diff )
		add_filter( "_wp_revision_field_$field", 'htmlspecialchars' );
	$content = apply_filters( "_wp_revision_field_$field", $revision->$field, $field );
	if ( $diff ) {
		$left_content = apply_filters( "_wp_revision_field_$field", $left_revision->$field, $field );
		if ( !$content = wp_text_diff( $left_content, $content ) )
			continue;
	}
	$identical = false;
	?>

	<tr id="revision-field-<?php echo $field; ?>"?>
		<th scope="row"><?php echo wp_specialchars( $field_title ); ?></th>
		<td colspan="2"><pre><?php echo $content; ?></pre></td>
	</tr>

	<?php

endforeach;

if ( $diff && $identical ) :

	?>

	<tr><td colspan="3"><div class="updated"><p><?php _e( 'These revisions are identical' ); ?></p></div></td></tr>

	<?php

endif;

?>

</table>

<br class="clear" />

<h2><?php _e( 'Post Revisions' ); ?></h2>

<?php
	wp_list_post_revisions( $post, array( 'format' => 'form-table', 'exclude' => $revision->ID, 'parent' => true, 'right' => $right, 'left' => $left ) );

	require_once( 'admin-footer.php' );
