<?php
/**
 * Revisions administration panel
 *
 * Requires wp-admin/includes/revision.php.
 *
 * @package WordPress
 * @subpackage Administration
 * @since 2.6.0
 *
 * @param int    revision Optional. The revision ID.
 * @param string action   The action to take.
 *                        Accepts 'restore', 'view' or 'edit'.
 * @param int    from     The revision to compare from.
 * @param int    to       Optional, required if revision missing. The revision to compare to.
 */

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

require ABSPATH . 'wp-admin/includes/revision.php';

wp_reset_vars( array( 'revision', 'action', 'from', 'to' ) );

$revision_id = absint( $revision );

$from = is_numeric( $from ) ? absint( $from ) : null;
if ( ! $revision_id )
	$revision_id = absint( $to );
$redirect = 'edit.php';

switch ( $action ) :
case 'restore' :
	if ( ! $revision = wp_get_post_revision( $revision_id ) )
		break;

	if ( ! current_user_can( 'edit_post', $revision->post_parent ) )
		break;

	if ( ! $post = get_post( $revision->post_parent ) )
		break;

	// Revisions disabled (previously checked autosaves && ! wp_is_post_autosave( $revision ))
	if ( ! wp_revisions_enabled( $post ) ) {
		$redirect = 'edit.php?post_type=' . $post->post_type;
		break;
	}

	// Don't allow revision restore when post is locked
	if ( wp_check_post_lock( $post->ID ) )
		break;

	check_admin_referer( "restore-post_{$revision->ID}" );

	wp_restore_post_revision( $revision->ID );
	$redirect = add_query_arg( array( 'message' => 5, 'revision' => $revision->ID ), get_edit_post_link( $post->ID, 'url' ) );
	break;
case 'view' :
case 'edit' :
default :
	if ( ! $revision = wp_get_post_revision( $revision_id ) )
		break;
	if ( ! $post = get_post( $revision->post_parent ) )
		break;

	if ( ! current_user_can( 'read_post', $revision->ID ) || ! current_user_can( 'edit_post', $revision->post_parent ) )
		break;

	// Revisions disabled and we're not looking at an autosave
	if ( ! wp_revisions_enabled( $post ) && ! wp_is_post_autosave( $revision ) ) {
		$redirect = 'edit.php?post_type=' . $post->post_type;
		break;
	}

	$post_title = '<a href="' . get_edit_post_link() . '">' . _draft_or_post_title() . '</a>';
	$h2 = sprintf( __( 'Compare Revisions of &#8220;%1$s&#8221;' ), $post_title );
	$title = __( 'Revisions' );

	$redirect = false;
	break;
endswitch;

// Empty post_type means either malformed object found, or no valid parent was found.
if ( ! $redirect && empty( $post->post_type ) )
	$redirect = 'edit.php';

if ( ! empty( $redirect ) ) {
	wp_redirect( $redirect );
	exit;
}

// This is so that the correct "Edit" menu item is selected.
if ( ! empty( $post->post_type ) && 'post' != $post->post_type )
	$parent_file = $submenu_file = 'edit.php?post_type=' . $post->post_type;
else
	$parent_file = $submenu_file = 'edit.php';

wp_enqueue_script( 'revisions' );
wp_localize_script( 'revisions', '_wpRevisionsSettings', wp_prepare_revisions_for_js( $post, $revision_id, $from ) );

/* Revisions Help Tab */

$revisions_overview  = '<p>' . __( 'This screen is used for managing your content revisions.' ) . '</p>';
$revisions_overview .= '<p>' . __( 'Revisions are saved copies of your post or page, which are periodically created as you update your content. The red text on the left shows the content that was removed. The green text on the right shows the content that was added.' ) . '</p>';
$revisions_overview .= '<p>' . __( 'From this screen you can review, compare, and restore revisions:' ) . '</p>';
$revisions_overview .= '<ul><li>' . __( 'To navigate between revisions, <strong>drag the slider handle left or right</strong> or <strong>use the Previous or Next buttons</strong>.' ) . '</li>';
$revisions_overview .= '<li>' . __( 'Compare two different revisions by <strong>selecting the &#8220;Compare any two revisions&#8221; box</strong> to the side.' ) . '</li>';
$revisions_overview .= '<li>' . __( 'To restore a revision, <strong>click Restore This Revision</strong>.' ) . '</li></ul>';

get_current_screen()->add_help_tab( array(
	'id'      => 'revisions-overview',
	'title'   => __( 'Overview' ),
	'content' => $revisions_overview
) );

$revisions_sidebar  = '<p><strong>' . __( 'For more information:' ) . '</strong></p>';
$revisions_sidebar .= '<p>' . __( '<a href="http://codex.wordpress.org/Revision_Management" target="_blank">Revisions Management</a>' ) . '</p>';
$revisions_sidebar .= '<p>' . __( '<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>' ) . '</p>';

get_current_screen()->set_help_sidebar( $revisions_sidebar );

require_once( ABSPATH . 'wp-admin/admin-header.php' );

?>

<div class="wrap">
	<h2 class="long-header"><?php echo $h2; ?></h2>
</div>

<script id="tmpl-revisions-frame" type="text/html">
	<div class="revisions-control-frame"></div>
	<div class="revisions-diff-frame"></div>
</script>

<script id="tmpl-revisions-buttons" type="text/html">
	<div class="revisions-previous">
		<input class="button" type="button" value="<?php echo esc_attr_x( 'Previous', 'Button label for a previous revision' ); ?>" />
	</div>

	<div class="revisions-next">
		<input class="button" type="button" value="<?php echo esc_attr_x( 'Next', 'Button label for a next revision' ); ?>" />
	</div>
</script>

<script id="tmpl-revisions-checkbox" type="text/html">
	<div class="revision-toggle-compare-mode">
		<label>
			<input type="checkbox" class="compare-two-revisions"
			<#
			if ( 'undefined' !== typeof data && data.model.attributes.compareTwoMode ) {
			 	#> checked="checked"<#
			}
			#>
			/>
			<?php esc_attr_e( 'Compare any two revisions' ); ?>
		</label>
	</div>
</script>

<script id="tmpl-revisions-meta" type="text/html">
	<# if ( ! _.isUndefined( data.attributes ) ) { #>
		<div class="diff-title">
			<# if ( 'from' === data.type ) { #>
				<strong><?php _ex( 'From:', 'Followed by post revision info' ); ?></strong>
			<# } else if ( 'to' === data.type ) { #>
				<strong><?php _ex( 'To:', 'Followed by post revision info' ); ?></strong>
			<# } #>
			<div class="author-card<# if ( data.attributes.autosave ) { #> autosave<# } #>">
				{{{ data.attributes.author.avatar }}}
				<div class="author-info">
				<# if ( data.attributes.autosave ) { #>
					<span class="byline"><?php printf( __( 'Autosave by %s' ),
						'<span class="author-name">{{ data.attributes.author.name }}</span>' ); ?></span>
				<# } else if ( data.attributes.current ) { #>
					<span class="byline"><?php printf( __( 'Current Revision by %s' ),
						'<span class="author-name">{{ data.attributes.author.name }}</span>' ); ?></span>
				<# } else { #>
					<span class="byline"><?php printf( __( 'Revision by %s' ),
						'<span class="author-name">{{ data.attributes.author.name }}</span>' ); ?></span>
				<# } #>
					<span class="time-ago">{{ data.attributes.timeAgo }}</span>
					<span class="date">({{ data.attributes.dateShort }})</span>
				</div>
			<# if ( 'to' === data.type && data.attributes.restoreUrl ) { #>
				<input  <?php if ( wp_check_post_lock( $post->ID ) ) { ?>
					disabled="disabled"
				<?php } else { ?>
					<# if ( data.attributes.current ) { #>
						disabled="disabled"
					<# } #>
				<?php } ?>
				<# if ( data.attributes.autosave ) { #>
					type="button" class="restore-revision button button-primary" value="<?php esc_attr_e( 'Restore This Autosave' ); ?>" />
				<# } else { #>
					type="button" class="restore-revision button button-primary" value="<?php esc_attr_e( 'Restore This Revision' ); ?>" />
				<# } #>
			<# } #>
		</div>
	<# if ( 'tooltip' === data.type ) { #>
		<div class="revisions-tooltip-arrow"><span></span></div>
	<# } #>
<# } #>
</script>

<script id="tmpl-revisions-diff" type="text/html">
	<div class="loading-indicator"><span class="spinner"></span></div>
	<div class="diff-error"><?php _e( 'Sorry, something went wrong. The requested comparison could not be loaded.' ); ?></div>
	<div class="diff">
	<# _.each( data.fields, function( field ) { #>
		<h3>{{ field.name }}</h3>
		{{{ field.diff }}}
	<# }); #>
	</div>
</script>


<?php
require_once( ABSPATH . 'wp-admin/admin-footer.php' );
