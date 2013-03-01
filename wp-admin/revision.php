<?php
/**
 * Revisions administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('./admin.php');
wp_reset_vars( array( 'revision', 'action' ) );

$revision_id = absint($revision);
$redirect = 'edit.php';

switch ( $action ) :
case 'restore' :
	if ( !$revision = wp_get_post_revision( $revision_id ) )
		break;
	if ( !current_user_can( 'edit_post', $revision->post_parent ) )
		break;
	if ( !$post = get_post( $revision->post_parent ) )
		break;

	// Revisions disabled and we're not looking at an autosave
	if ( ( ! WP_POST_REVISIONS || !post_type_supports($post->post_type, 'revisions') ) && !wp_is_post_autosave( $revision ) ) {
		$redirect = 'edit.php?post_type=' . $post->post_type;
		break;
	}
	check_admin_referer( "restore-post_{$post->ID}|{$revision->ID}" );

	wp_restore_post_revision( $revision->ID );
	$redirect = add_query_arg( array( 'message' => 5, 'revision' => $revision->ID ), get_edit_post_link( $post->ID, 'url' ) );
	break;
case 'view' :
case 'edit' :
default :
	if ( !$revision = wp_get_post_revision( $revision_id ) )
		break;
	if ( !$post = get_post( $revision->post_parent ) )
		break;

	if ( !current_user_can( 'read_post', $revision->ID ) || !current_user_can( 'read_post', $post->ID ) )
		break;

	// Revisions disabled and we're not looking at an autosave
	if ( ( ! WP_POST_REVISIONS || !post_type_supports($post->post_type, 'revisions') ) && !wp_is_post_autosave( $revision ) ) {
		$redirect = 'edit.php?post_type=' . $post->post_type;
		break;
	}

	$post_title = '<a href="' . get_edit_post_link() . '">' . get_the_title() . '</a>';
	$revision_title = wp_post_revision_title( $revision, false );
	$h2 = sprintf( __( 'Compare Revisions of &#8220;%1$s&#8221;' ), $post_title );
	$title = __( 'Revisions' );

	$redirect = false;
	break;
endswitch;

// Empty post_type means either malformed object found, or no valid parent was found.
if ( !$redirect && empty($post->post_type) )
	$redirect = 'edit.php';

if ( !empty($redirect) ) {
	wp_redirect( $redirect );
	exit;
}

// This is so that the correct "Edit" menu item is selected.
if ( !empty($post->post_type) && 'post' != $post->post_type )
	$parent_file = $submenu_file = 'edit.php?post_type=' . $post->post_type;
else
	$parent_file = $submenu_file = 'edit.php';

wp_enqueue_script( 'revisions' );

require_once( './admin-header.php' );

//TODO - Some of the translations below split things into multiple strings that are contextually related and this makes it pretty impossible for RTL translation.
//TODO can we pass the context in a better way
?>
<script type="text/javascript">
var wpRevisionsSettings = <?php echo json_encode( array( 'post_id' => $post->ID, 'nonce' => wp_create_nonce( 'revisions-ajax-nonce' ) ) ); ?>;
</script>

<div id="backbonerevisionsoptions"></div>

<br class="clear"/>
<div class="wrap">
	<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
	<div class="revisiondiffcontainer diffsplit currentversion rightmodelloading">
		<div id="modelsloading" class="updated message"><span class="spinner" ></span> <?php _e( 'Calculating revision diffs' ); ?></div>
		<h2 class="long-header"><?php echo $h2; ?></h2>
		<div id="backbonerevisionsinteract"></div>
		<div id="backbonerevisionsdiff"></div>
<hr />
<?php
	$comparetworevisionslink = get_edit_post_link( $revision->ID );
?>
	</div>
</div>

<script id="tmpl-revision" type="text/html">
	<div id="diffsubheader">
		<span id="diff_from_current_revision"><?php _e( 'Current version' ); ?><?php _e( '- compared to -' ); ?></span>
		<div id="difftitlefrom">{{{ data.revision_from_date_author }}} <?php _e( '- compared to -' ); ?></div>
		<div id="difftitle">{{{ data.revision_date_author }}}</div>
		<div id="diffcancel"><input class="button" onClick="document.location='<?php echo get_edit_post_link( $post->ID ); ?>'" type="submit" id="cancel" value="<?php esc_attr_e( 'Cancel' )?>" /></div>
		<div id="diffrestore"><input class="button button-primary" onClick="document.location='{{{ data.restoreaction }}}'" type="submit" id="restore" value="<?php esc_attr_e( 'Restore' )?>" /></div>
		<div id="comparetworevisions"><input type="checkbox" id="comparetwo" value="comparetwo" {{{ data.comparetwochecked }}} name="comparetwo"/> <?php esc_attr_e( 'Compare two revisions' )?></div>
	</div>
	<div id="removedandadded">
		<div id="removed"><?php _e( 'Removed -' ); ?></div>
		<div id="added"><?php _e( 'Added +' ); ?></div>
	</div
	<div>{{{ data.revisiondiff }}}</div>
</script>

<script id="tmpl-revisionvinteract" type="text/html">
	<div id="diffheader">
<div id="diffprevious"><input class="button" type="submit" id="previous" value="Previous" /></div>
			<div id="diffnext"><input class="button" type="submit" id="next" value="Next" /></div>
			<div id="diffslider">
	<div id="revisioncount">
					<?php _e( 'Comparing' ); ?>
					<span id="diff_left_count"> <?php _e( 'revision' ); ?></span> <span id="diff_left_count_inner"></span>
					<span id="diff_left_current_revision"><?php _e( 'current version' ); ?></span>
					<span id="diff_revision_from">{{{ data.diff_revision_from }}}</span>
					<?php _e( ' to revision' ); ?>
					<span id="diff_count">{{{ data.current_diff }}}</span>
					<?php _e( ' of ' ); ?>
					<span id="diff_max" ></span>
				</div>

			<div id="slider" class="wp-slider"></div>
		</div>
	</div>
</script>
<?php
/*
TODO Convert these into screen options
<script id="tmpl-revisionoptions" type="text/html">
	<div id="revisionoptions">
		<div id="showsplitviewoption">
			<input type='checkbox' id="show_split_view" checked="checked" value="1" /> <?php _e( 'Show split diff view' ); ?>
		</div>
		<div id="toggleshowautosavesoption">
			<input type='checkbox' id="toggleshowautosaves" value="1" /> <?php _e( 'Show autosaves' ); ?>
		</div>
	</div>
</script>
*/
require_once( './admin-footer.php' );