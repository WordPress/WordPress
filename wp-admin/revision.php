<?php

require_once('admin.php');

wp_reset_vars(array('revision', 'left', 'right', 'action'));
$revision_id = absint($revision);
$diff        = absint($diff);
$left        = absint($left);
$right       = absint($right);


$parent_file = $redirect = 'edit.php';
$submenu_file = 'edit.php';
$title = __( 'Post Revision' );


switch ( $action ) :
case 'delete' : // stubs
case 'edit' :
	$redirect = remove_query_arg( 'action' );
	break;
case 'restore' :
	if ( !current_user_can( 'edit_post', $revision->post_parent ) )
		break;
	if ( !$revision = wp_get_revision( $revision_id ) )
		break;
	if ( !$post = get_post( $revision->post_parent ) )
		break;

	check_admin_referer( "restore-post_$post->ID|$revision->ID" );

	wp_restore_revision( $revision->ID );
	$redirect = add_query_arg( array( 'message' => 5, 'revision' => $revision->ID ), get_edit_post_link( $post->ID, 'url' ) );
	break;
case 'diff' :
	if ( !$left_revision  = get_post( $left ) )
		break;
	if ( !$right_revision = get_post( $right ) )
		break;

	if ( !current_user_can( 'edit_post', $left_revision->ID ) || !current_user_can( 'edit_post', $right_revision->ID ) )
		break;

	// Don't allow reverse diffs?
	if ( strtotime($right_revision->post_modified_gmt) < strtotime($left_revision->post_modified_gmt) ) {
		$redirect = add_query_arg( array( 'left' => $right, 'right' => $left ) );
		break;
	}

	if ( $left_revision->ID == $right_revision->post_parent ) // right is a revision of left
		$post =& $left_revision;
	elseif ( $left_revision->post_parent == $right_revision->ID ) // left is a revision of right
		$post =& $right_revision;
	elseif ( $left_revision->post_parent == $right_revision->post_parent ) // both are revisions of common parent
		$post = get_post( $left_revision->post_parent );
	else
		break; // Don't diff two unrelated revisions

	if (
		// They're the same
		$left_revision->ID == $right_revision->ID
	||
		// Neither is a revision
		( !wp_get_revision( $left_revision->ID ) && !wp_get_revision( $right_revision->ID ) )
	)
		break;
	
	$post_title = '<a href="' . get_edit_post_link() . '">' . get_the_title() . '</a>';
	$h2 = sprintf( __( 'Compare Revisions of &#8220;%1$s&#8221;' ), $post_title );

	$left  = $left_revision->ID;
	$right = $right_revision->ID;

	$redirect = false;
	break;
case 'view' :
default :
	if ( !$revision = wp_get_revision( $revision_id ) )
		break;
	if ( !$post = get_post( $revision->post_parent ) )
		break;

	if ( !current_user_can( 'edit_post', $revision->ID ) || !current_user_can( 'edit_post', $post->ID ) )
		break;

	$post_title = '<a href="' . get_edit_post_link() . '">' . get_the_title() . '</a>';
	$revision_title = wp_post_revision_title( $revision, false );
	$h2 = sprintf( __( 'Post Revision for &#8220;%1$s&#8221; created on %2$s' ), $post_title, $revision_title );

	// Sets up the diff radio buttons
	$left  = $revision->ID;
	$right = $post->ID;

	$redirect = false;
	break;
endswitch;

if ( $redirect ) {
	wp_redirect( $redirect );
	exit;
}

// Converts post_author ID# into name
add_filter( '_wp_revision_field_post_author', 'get_author_name' );

require_once( 'admin-header.php' );

?>

<div class="wrap">

<h2 class="long-header"><?php echo $h2; ?></h2>

<table class="form-table ie-fixed">
	<col class="th" />
<?php if ( 'diff' == $action ) : ?>
<tr id="revision">
	<th scope="row"></th>
	<th scope="col" class="th-full">
		<span class="alignleft"><?php printf( __('Older: %s'), wp_post_revision_title( $left_revision ) ); ?></span>
		<span class="alignright"><?php printf( __('Newer: %s'), wp_post_revision_title( $right_revision ) ); ?></span>
	</td>
</tr>
<?php endif;

// use get_post_to_edit filters? 
$identical = true;
foreach ( _wp_revision_fields() as $field => $field_title ) :
	if ( 'diff' == $action ) {
		$left_content = apply_filters( "_wp_revision_field_$field", $left_revision->$field, $field );
		$right_content = apply_filters( "_wp_revision_field_$field", $right_revision->$field, $field );
		if ( !$content = wp_text_diff( $left_content, $right_content ) )
			continue; // There is no difference between left and right
		$identical = false;
	} else {
		add_filter( "_wp_revision_field_$field", 'htmlspecialchars' );
		$content = apply_filters( "_wp_revision_field_$field", $revision->$field, $field );
	}
	?>

	<tr id="revision-field-<?php echo $field; ?>">
		<th scope="row"><?php echo wp_specialchars( $field_title ); ?></th>
		<td><pre><?php echo $content; ?></pre></td>
	</tr>

	<?php

endforeach;

if ( 'diff' == $action && $identical ) :

	?>

	<tr><td colspan="2"><div class="updated"><p><?php _e( 'These revisions are identical.' ); ?></p></div></td></tr>

	<?php

endif;

?>

</table>

<br class="clear" />

<h2><?php _e( 'Post Revisions' ); ?></h2>

<?php

wp_list_post_revisions( $post, array( 'format' => 'form-table', 'parent' => true, 'right' => $right, 'left' => $left ) );

require_once( 'admin-footer.php' );
