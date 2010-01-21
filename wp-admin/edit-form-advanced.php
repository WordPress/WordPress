<?php
/**
 * Post advanced form for inclusion in the administration panels.
 *
 * @package WordPress
 * @subpackage Administration
 */

// don't load directly
if ( !defined('ABSPATH') )
	die('-1');

/**
 * Post ID global
 * @name $post_ID
 * @var int
 */
$post_ID = isset($post_ID) ? (int) $post_ID : 0;
$temp_ID = isset($temp_ID) ? (int) $temp_ID : 0;
$user_ID = isset($user_ID) ? (int) $user_ID : 0;
$action = isset($action) ? $action : '';

$messages = array();
$messages['post'] = array(
	'',
	sprintf( __('Post updated. <a href="%s">View post</a>'), get_permalink($post_ID) ),
	__('Custom field updated.'),
	__('Custom field deleted.'),
	__('Post updated.'),
	/* translators: %s: date and time of the revision */
	isset($_GET['revision']) ? sprintf( __('Post restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
	sprintf( __('Post published. <a href="%s">View post</a>'), get_permalink($post_ID) ),
	__('Post saved.'),
	sprintf( __('Post submitted. <a target="_blank" href="%s">Preview post</a>'), add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ),
	sprintf( __('Post scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview post</a>'),
		// translators: Publish box date format, see http://php.net/date
		date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), get_permalink($post_ID) ),
	sprintf( __('Post draft updated. <a target="_blank" href="%s">Preview post</a>'), add_query_arg( 'preview', 'true', get_permalink($post_ID) ) )
);
$messages['page'] = array(
	'',
	sprintf( __('Page updated. <a href="%s">View page</a>'), get_permalink($post_ID) ),
	__('Custom field updated.'),
	__('Custom field deleted.'),
	__('Page updated.'),
	isset($_GET['revision']) ? sprintf( __('Page restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
	sprintf( __('Page published. <a href="%s">View page</a>'), get_permalink($post_ID) ),
	sprintf( __('Page submitted. <a target="_blank" href="%s">Preview page</a>'), add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ),
	sprintf( __('Page scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview page</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), get_permalink($post_ID) ),
	sprintf( __('Page draft updated. <a target="_blank" href="%s">Preview page</a>'), add_query_arg( 'preview', 'true', get_permalink($post_ID) ) )
);

$message = false;
if ( isset($_GET['message']) ) {
	$_GET['message'] = absint( $_GET['message'] );
	if ( isset($messages[$post_type][$_GET['message']]) )
		$message = $messages[$post_type][$_GET['message']];
	elseif ( !isset($messages[$post_type]) && isset($messages['post'][$_GET['message']]) )
		$message = $messages['post'][$_GET['message']];
}

$notice = false;
if ( 0 == $post_ID ) {
	$form_action = 'post';
	$nonce_action = 'add-' . $post_type;
	$temp_ID = -1 * time(); // don't change this formula without looking at wp_write_post()
	$form_extra = "<input type='hidden' id='post_ID' name='temp_ID' value='" . esc_attr($temp_ID) . "' />";
	$autosave = false;
} else {
	$form_action = 'editpost';
	$nonce_action = 'update-' . $post_type . '_' . $post_ID;
	$form_extra = "<input type='hidden' id='post_ID' name='post_ID' value='" . esc_attr($post_ID) . "' />";
	$autosave = wp_get_post_autosave( $post_ID );

	// Detect if there exists an autosave newer than the post and if that autosave is different than the post
	if ( $autosave && mysql2date( 'U', $autosave->post_modified_gmt, false ) > mysql2date( 'U', $post->post_modified_gmt, false ) ) {
		foreach ( _wp_post_revision_fields() as $autosave_field => $_autosave_field ) {
			if ( normalize_whitespace( $autosave->$autosave_field ) != normalize_whitespace( $post->$autosave_field ) ) {
				$notice = sprintf( __( 'There is an autosave of this post that is more recent than the version below.  <a href="%s">View the autosave</a>' ), get_edit_post_link( $autosave->ID ) );
				break;
			}
		}
		unset($autosave_field, $_autosave_field);
	}
}

$post_type_object = get_post_type_object($post_type);
$post_type_cap = $post_type_object->capability_type;

// All meta boxes should be defined and added before the first do_meta_boxes() call (or potentially during the do_meta_boxes action).
require_once('includes/meta-boxes.php');

add_meta_box('submitdiv', __('Publish'), 'post_submit_meta_box', $post_type, 'side', 'core');

// all tag-style taxonomies
foreach ( get_object_taxonomies($post_type) as $tax_name ) {
	if ( !is_taxonomy_hierarchical($tax_name) ) {
		$taxonomy = get_taxonomy($tax_name);
		$label = isset($taxonomy->label) ? esc_attr($taxonomy->label) : $tax_name;

		add_meta_box('tagsdiv-' . $tax_name, $label, 'post_tags_meta_box', $post_type, 'side', 'core');
	}
}

if ( is_object_in_taxonomy($post_type, 'category') )
	add_meta_box('categorydiv', __('Categories'), 'post_categories_meta_box', $post_type, 'side', 'core');

if ( post_type_supports($post_type, 'page-attributes') )
	add_meta_box('pageparentdiv', __('Attributes'), 'page_attributes_meta_box', $post_type, 'side', 'core');

if ( current_theme_supports( 'post-thumbnails', $post_type ) && post_type_supports($post_type, 'post-thumbnails') )
	add_meta_box('postimagediv', __('Post Thumbnail'), 'post_thumbnail_meta_box', $post_type, 'side', 'low');

if ( post_type_supports($post_type, 'excerpts') )
	add_meta_box('postexcerpt', __('Excerpt'), 'post_excerpt_meta_box', $post_type, 'normal', 'core');

if ( post_type_supports($post_type, 'trackbacks') )
	add_meta_box('trackbacksdiv', __('Send Trackbacks'), 'post_trackback_meta_box', $post_type, 'normal', 'core');

if ( post_type_supports($post_type, 'custom-fields') )
	add_meta_box('postcustom', __('Custom Fields'), 'post_custom_meta_box', $post_type, 'normal', 'core');

do_action('dbx_post_advanced');
if ( post_type_supports($post_type, 'comments') )
	add_meta_box('commentstatusdiv', __('Discussion'), 'post_comment_status_meta_box', $post_type, 'normal', 'core');

if ( ('publish' == $post->post_status || 'private' == $post->post_status) && post_type_supports($post_type, 'comments') )
	add_meta_box('commentsdiv', __('Comments'), 'post_comment_meta_box', $post_type, 'normal', 'core');

if ( !( 'pending' == $post->post_status && !current_user_can( 'publish_posts' ) ) )
	add_meta_box('slugdiv', __('Slug'), 'post_slug_meta_box', $post_type, 'normal', 'core');

$authors = get_editable_user_ids( $current_user->id ); // TODO: ROLE SYSTEM
if ( $post->post_author && !in_array($post->post_author, $authors) )
	$authors[] = $post->post_author;
if ( $authors && count( $authors ) > 1 )
	add_meta_box('authordiv', __('Author'), 'post_author_meta_box', $post_type, 'normal', 'core');

if ( post_type_supports($post_type, 'revisions') && 0 < $post_ID && wp_get_post_revisions( $post_ID ) )
	add_meta_box('revisionsdiv', __('Revisions'), 'post_revisions_meta_box', $post_type, 'normal', 'core');

do_action('do_meta_boxes', $post_type, 'normal', $post);
do_action('do_meta_boxes', $post_type, 'advanced', $post);
do_action('do_meta_boxes', $post_type, 'side', $post);

add_contextual_help($current_screen, drag_drop_help());

require_once('admin-header.php');
?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?></h2>
<?php if ( $notice ) : ?>
<div id="notice" class="error"><p><?php echo $notice ?></p></div>
<?php endif; ?>
<?php if ( $message ) : ?>
<div id="message" class="updated"><p><?php echo $message; ?></p></div>
<?php endif; ?>
<form name="post" action="post.php" method="post" id="post">
<?php wp_nonce_field($nonce_action); ?>
<input type="hidden" id="user-id" name="user_ID" value="<?php echo (int) $user_ID ?>" />
<input type="hidden" id="hiddenaction" name="action" value="<?php echo esc_attr($form_action) ?>" />
<input type="hidden" id="originalaction" name="originalaction" value="<?php echo esc_attr($form_action) ?>" />
<input type="hidden" id="post_author" name="post_author" value="<?php echo esc_attr( $post->post_author ); ?>" />
<input type="hidden" id="post_type" name="post_type" value="<?php echo esc_attr($post_type) ?>" />
<input type="hidden" id="original_post_status" name="original_post_status" value="<?php echo esc_attr($post->post_status) ?>" />
<input name="referredby" type="hidden" id="referredby" value="<?php echo esc_url(stripslashes(wp_get_referer())); ?>" />
<?php
if ( 'draft' != $post->post_status )
	wp_original_referer_field(true, 'previous');

echo $form_extra ?>

<div id="poststuff" class="metabox-holder<?php echo 2 == $screen_layout_columns ? ' has-right-sidebar' : ''; ?>">
<div id="side-info-column" class="inner-sidebar">

<?php
('page' == $post_type) ? do_action('submitpage_box') : do_action('submitpost_box');
$side_meta_boxes = do_meta_boxes($post_type, 'side', $post);
?>
</div>

<div id="post-body">
<div id="post-body-content">
<div id="titlediv">
<div id="titlewrap">
	<label class="screen-reader-text" for="title"><?php _e('Title') ?></label>
	<input type="text" name="post_title" size="30" tabindex="1" value="<?php echo esc_attr( htmlspecialchars( $post->post_title ) ); ?>" id="title" autocomplete="off" />
</div>
<div class="inside">
<?php
$sample_permalink_html = get_sample_permalink_html($post->ID);
if ( !( 'pending' == $post->post_status && !current_user_can( 'publish_' . $post_type_cap . 's' ) ) ) { ?>
	<div id="edit-slug-box">
<?php
	if ( ! empty($post->ID) && ! empty($sample_permalink_html) ) :
		echo $sample_permalink_html;
endif; ?>
	</div>
<?php
} ?>
</div>
</div>

<div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" class="postarea">

<?php the_editor($post->post_content); ?>

<table id="post-status-info" cellspacing="0"><tbody><tr>
	<td id="wp-word-count"></td>
	<td class="autosave-info">
	<span id="autosave">&nbsp;</span>
<?php
	if ( $post_ID ) {
		echo '<span id="last-edit">';
		if ( $last_id = get_post_meta($post_ID, '_edit_last', true) ) {
			$last_user = get_userdata($last_id);
			printf(__('Last edited by %1$s on %2$s at %3$s'), esc_html( $last_user->display_name ), mysql2date(get_option('date_format'), $post->post_modified), mysql2date(get_option('time_format'), $post->post_modified));
		} else {
			printf(__('Last edited on %1$s at %2$s'), mysql2date(get_option('date_format'), $post->post_modified), mysql2date(get_option('time_format'), $post->post_modified));
		}
		echo '</span>';
	} ?>
	</td>
</tr></tbody></table>

<?php
wp_nonce_field( 'autosave', 'autosavenonce', false );
wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
wp_nonce_field( 'getpermalink', 'getpermalinknonce', false );
wp_nonce_field( 'samplepermalink', 'samplepermalinknonce', false );
wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
</div>

<?php

do_meta_boxes($post_type, 'normal', $post);

( 'page' == $post_type ) ? do_action('edit_page_form') : do_action('edit_form_advanced');

do_meta_boxes($post_type, 'advanced', $post);

do_action('dbx_post_sidebar'); ?>

</div>
</div>
<br class="clear" />
</div><!-- /poststuff -->
</form>
</div>

<?php wp_comment_reply(); ?>

<?php if ((isset($post->post_title) && '' == $post->post_title) || (isset($_GET['message']) && 2 > $_GET['message'])) : ?>
<script type="text/javascript">
try{document.post.title.focus();}catch(e){}
</script>
<?php endif; ?>
