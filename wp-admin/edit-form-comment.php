<?php
/**
 * Edit comment form for inclusion in another file.
 *
 * @package WordPress
 * @subpackage Administration
 */

// don't load directly
if ( !defined('ABSPATH') )
	die('-1');
?>
<form name="post" action="comment.php" method="post" id="post">
<?php wp_nonce_field('update-comment_' . $comment->comment_ID) ?>
<div class="wrap">
<h2><?php _e('Edit Comment'); ?></h2>

<div id="poststuff">
<input type="hidden" name="user_ID" value="<?php echo (int) $user_ID; ?>" />
<input type="hidden" name="action" value="editedcomment" />
<input type="hidden" name="comment_ID" value="<?php echo esc_attr( $comment->comment_ID ); ?>" />
<input type="hidden" name="comment_post_ID" value="<?php echo esc_attr( $comment->comment_post_ID ); ?>" />

<div id="post-body" class="metabox-holder columns-2">
<div id="post-body-content" class="edit-form-section">
<div id="namediv" class="stuffbox">
<h3><label for="name"><?php _e( 'Author' ) ?></label></h3>
<div class="inside">
<table class="form-table editcomment">
<tbody>
<tr>
	<td class="first"><?php _e( 'Name:' ); ?></td>
	<td><input type="text" name="newcomment_author" size="30" value="<?php echo esc_attr( $comment->comment_author ); ?>" id="name" /></td>
</tr>
<tr>
	<td class="first">
	<?php
		if ( $comment->comment_author_email ) {
			printf( __( 'E-mail (%s):' ), get_comment_author_email_link( __( 'send e-mail' ), '', '' ) );
		} else {
			_e( 'E-mail:' );
		}
?></td>
	<td><input type="text" name="newcomment_author_email" size="30" value="<?php echo $comment->comment_author_email; ?>" id="email" /></td>
</tr>
<tr>
	<td class="first">
	<?php
		if ( ! empty( $comment->comment_author_url ) && 'http://' != $comment->comment_author_url ) {
			$link = '<a href="' . $comment->comment_author_url . '" rel="external nofollow" target="_blank">' . __('visit site') . '</a>';
			/** This filter is documented in wp-includes/comment-template.php */
			printf( __( 'URL (%s):' ), apply_filters( 'get_comment_author_link', $link ) );
		} else {
			_e( 'URL:' );
		} ?></td>
	<td><input type="text" id="newcomment_author_url" name="newcomment_author_url" size="30" class="code" value="<?php echo esc_attr($comment->comment_author_url); ?>" /></td>
</tr>
</tbody>
</table>
<br />
</div>
</div>

<div id="postdiv" class="postarea">
<?php
	$quicktags_settings = array( 'buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,code,close' );
	wp_editor( $comment->comment_content, 'content', array( 'media_buttons' => false, 'tinymce' => false, 'quicktags' => $quicktags_settings ) );
	wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
</div>
</div><!-- /post-body-content -->

<div id="postbox-container-1" class="postbox-container">
<div id="submitdiv" class="stuffbox" >
<h3><span class="hndle"><?php _e('Status') ?></span></h3>
<div class="inside">
<div class="submitbox" id="submitcomment">
<div id="minor-publishing">

<div id="minor-publishing-actions">
<div id="preview-action">
<a class="preview button" href="<?php echo get_comment_link(); ?>" target="_blank"><?php _e('View Comment'); ?></a>
</div>
<div class="clear"></div>
</div>

<div id="misc-publishing-actions">

<div class="misc-pub-section misc-pub-comment-status" id="comment-status-radio">
<label class="approved"><input type="radio"<?php checked( $comment->comment_approved, '1' ); ?> name="comment_status" value="1" /><?php /* translators: comment type radio button */ _ex('Approved', 'adjective') ?></label><br />
<label class="waiting"><input type="radio"<?php checked( $comment->comment_approved, '0' ); ?> name="comment_status" value="0" /><?php /* translators: comment type radio button */ _ex('Pending', 'adjective') ?></label><br />
<label class="spam"><input type="radio"<?php checked( $comment->comment_approved, 'spam' ); ?> name="comment_status" value="spam" /><?php /* translators: comment type radio button */ _ex('Spam', 'adjective'); ?></label>
</div>

<?php if ( $ip = get_comment_author_IP() ) : ?>
<div class="misc-pub-section misc-pub-comment-author-ip">
	<?php _e( 'IP address:' ); ?> <strong><a href="<?php echo esc_url( sprintf( 'http://whois.arin.net/rest/ip/%s', $ip ) ); ?>"><?php echo esc_html( $ip ); ?></a></strong>
</div>
<?php endif; ?>

<div class="misc-pub-section curtime misc-pub-curtime">
<?php
/* translators: Publish box date format, see http://php.net/date */
$datef = __( 'M j, Y @ G:i' );
$stamp = __('Submitted on: <b>%1$s</b>');
$date = date_i18n( $datef, strtotime( $comment->comment_date ) );
?>
<span id="timestamp"><?php printf($stamp, $date); ?></span>&nbsp;<a href="#edit_timestamp" class="edit-timestamp hide-if-no-js"><?php _e('Edit') ?></a>
<div id='timestampdiv' class='hide-if-js'><?php touch_time(('editcomment' == $action), 0); ?></div>
</div>
</div> <!-- misc actions -->
<div class="clear"></div>
</div>

<div id="major-publishing-actions">
<div id="delete-action">
<?php echo "<a class='submitdelete deletion' href='" . wp_nonce_url("comment.php?action=" . ( !EMPTY_TRASH_DAYS ? 'deletecomment' : 'trashcomment' ) . "&amp;c=$comment->comment_ID&amp;_wp_original_http_referer=" . urlencode(wp_get_referer()), 'delete-comment_' . $comment->comment_ID) . "'>" . ( !EMPTY_TRASH_DAYS ? __('Delete Permanently') : __('Move to Trash') ) . "</a>\n"; ?>
</div>
<div id="publishing-action">
<?php submit_button( __( 'Update' ), 'primary', 'save', false ); ?>
</div>
<div class="clear"></div>
</div>
</div>
</div>
</div><!-- /submitdiv -->
</div>

<div id="postbox-container-2" class="postbox-container">
<?php
/** This action is documented in wp-admin/edit-form-advanced.php */
do_action( 'add_meta_boxes', 'comment', $comment );

/**
 * Fires when comment-specific meta boxes are added.
 *
 * @since 3.0.0
 *
 * @param object $comment Comment object.
 */
do_action( 'add_meta_boxes_comment', $comment );

do_meta_boxes(null, 'normal', $comment);

?>
</div>

<input type="hidden" name="c" value="<?php echo esc_attr($comment->comment_ID) ?>" />
<input type="hidden" name="p" value="<?php echo esc_attr($comment->comment_post_ID) ?>" />
<input name="referredby" type="hidden" id="referredby" value="<?php echo esc_url( wp_get_referer() ); ?>" />
<?php wp_original_referer_field(true, 'previous'); ?>
<input type="hidden" name="noredir" value="1" />

</div><!-- /post-body -->
</div>
</div>
</form>

<script type="text/javascript">
try{document.post.name.focus();}catch(e){}
</script>
