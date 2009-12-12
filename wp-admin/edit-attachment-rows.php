<?php
/**
 * Edit attachments table for inclusion in administration panels.
 *
 * @package WordPress
 * @subpackage Administration
 */

// don't load directly
if ( !defined('ABSPATH') )
	die('-1');

if ( have_posts() ) { ?>
<table class="widefat fixed" cellspacing="0">
	<thead>
	<tr>
<?php print_column_headers('upload'); ?>
	</tr>
	</thead>

	<tfoot>
	<tr>
<?php print_column_headers('upload', false); ?>
	</tr>
	</tfoot>

	<tbody id="the-list" class="list:post">
<?php
add_filter('the_title','esc_html');
$alt = '';
$posts_columns = get_column_headers('upload');
$hidden = get_hidden_columns('upload');

while ( have_posts() ) : the_post();

if ( $is_trash && $post->post_status != 'trash' )
	continue;
elseif ( !$is_trash && $post->post_status == 'trash' )
	continue;

$alt = ( 'alternate' == $alt ) ? '' : 'alternate';
global $current_user;
$post_owner = ( $current_user->ID == $post->post_author ? 'self' : 'other' );
$att_title = _draft_or_post_title();
?>
	<tr id='post-<?php echo $id; ?>' class='<?php echo trim( $alt . ' author-' . $post_owner . ' status-' . $post->post_status ); ?>' valign="top">

<?php
foreach ($posts_columns as $column_name => $column_display_name ) {
	$class = "class=\"$column_name column-$column_name\"";

	$style = '';
	if ( in_array($column_name, $hidden) )
		$style = ' style="display:none;"';

	$attributes = "$class$style";

	switch($column_name) {

	case 'cb':
		?>
		<th scope="row" class="check-column"><?php if ( current_user_can('edit_post', $post->ID) ) { ?><input type="checkbox" name="media[]" value="<?php the_ID(); ?>" /><?php } ?></th>
		<?php
		break;

	case 'icon':
		$attributes = 'class="column-icon media-icon"' . $style;
		?>
		<td <?php echo $attributes ?>><?php
			if ( $thumb = wp_get_attachment_image( $post->ID, array(80, 60), true ) ) {
				if ( $is_trash ) echo $thumb;
				else {
?>
				<a href="media.php?action=edit&amp;attachment_id=<?php the_ID(); ?>" title="<?php echo esc_attr(sprintf(__('Edit &#8220;%s&#8221;'), $att_title)); ?>">
					<?php echo $thumb; ?>
				</a>

<?php			}
			}
		?></td>
		<?php
		// TODO
		break;

	case 'media':
		?>
		<td <?php echo $attributes ?>><strong><?php if ( $is_trash ) echo $att_title; else { ?><a href="<?php echo get_edit_post_link( $post->ID ); ?>" title="<?php echo esc_attr(sprintf(__('Edit &#8220;%s&#8221;'), $att_title)); ?>"><?php echo $att_title; ?></a><?php } ?></strong><br />
		<?php echo strtoupper(preg_replace('/^.*?\.(\w+)$/', '$1', get_attached_file($post->ID))); ?>
		<p>
		<?php
		$actions = array();
		if ( current_user_can('edit_post', $post->ID) && !$is_trash )
			$actions['edit'] = '<a href="' . get_edit_post_link($post->ID, true) . '">' . __('Edit') . '</a>';
		if ( current_user_can('delete_post', $post->ID) ) {
			if ( $is_trash )
				$actions['untrash'] = "<a class='submitdelete' href='" . wp_nonce_url("post.php?action=untrash&amp;post=$post->ID", 'untrash-post_' . $post->ID) . "'>" . __('Restore') . "</a>";
			elseif ( EMPTY_TRASH_DAYS && MEDIA_TRASH )
				$actions['trash'] = "<a class='submitdelete' href='" . wp_nonce_url("post.php?action=trash&amp;post=$post->ID", 'trash-post_' . $post->ID) . "'>" . __('Trash') . "</a>";
			if ( $is_trash || !EMPTY_TRASH_DAYS || !MEDIA_TRASH )
				$actions['delete'] = "<a class='submitdelete' href='" . wp_nonce_url("post.php?action=delete&amp;post=$post->ID", 'delete-post_' . $post->ID) . "'>" . __('Delete Permanently') . "</a>";
		}
		if ( !$is_trash )
			$actions['view'] = '<a href="' . get_permalink($post->ID) . '" title="' . esc_attr(sprintf(__('View &#8220;%s&#8221;'), $title)) . '" rel="permalink">' . __('View') . '</a>';
		$actions = apply_filters( 'media_row_actions', $actions, $post );
		$action_count = count($actions);
		$i = 0;
		echo '<div class="row-actions">';
		foreach ( $actions as $action => $link ) {
			++$i;
			( $i == $action_count ) ? $sep = '' : $sep = ' | ';
			echo "<span class='$action'>$link$sep</span>";
		}
		echo '</div>';
		?></p></td>
		<?php
		break;

	case 'author':
		?>
		<td <?php echo $attributes ?>><?php the_author() ?></td>
		<?php
		break;

	case 'tags':
		?>
		<td <?php echo $attributes ?>><?php
		$tags = get_the_tags();
		if ( !empty( $tags ) ) {
			$out = array();
			foreach ( $tags as $c )
				$out[] = "<a href='edit.php?tag=$c->slug'> " . esc_html(sanitize_term_field('name', $c->name, $c->term_id, 'post_tag', 'display')) . "</a>";
			echo join( ', ', $out );
		} else {
			_e('No Tags');
		}
		?></td>
		<?php
		break;

	case 'desc':
		?>
		<td <?php echo $attributes ?>><?php echo has_excerpt() ? $post->post_excerpt : ''; ?></td>
		<?php
		break;

	case 'date':
		if ( '0000-00-00 00:00:00' == $post->post_date && 'date' == $column_name ) {
			$t_time = $h_time = __('Unpublished');
		} else {
			$t_time = get_the_time(__('Y/m/d g:i:s A'));
			$m_time = $post->post_date;
			$time = get_post_time( 'G', true, $post, false );
			if ( ( abs($t_diff = time() - $time) ) < 86400 ) {
				if ( $t_diff < 0 )
					$h_time = sprintf( __('%s from now'), human_time_diff( $time ) );
				else
					$h_time = sprintf( __('%s ago'), human_time_diff( $time ) );
			} else {
				$h_time = mysql2date(__('Y/m/d'), $m_time);
			}
		}
		?>
		<td <?php echo $attributes ?>><?php echo $h_time ?></td>
		<?php
		break;

	case 'parent':
		if ( $post->post_parent > 0 ) {
			if ( get_post($post->post_parent) ) {
				$title =_draft_or_post_title($post->post_parent);
			}
			?>
			<td <?php echo $attributes ?>><strong><a href="<?php echo get_edit_post_link( $post->post_parent ); ?>"><?php echo $title ?></a></strong>, <?php echo get_the_time(__('Y/m/d')); ?></td>
			<?php
		} else {
			?>
			<td <?php echo $attributes ?>><?php _e('(Unattached)'); ?><br />
			<a class="hide-if-no-js" onclick="findPosts.open('media[]','<?php echo $post->ID ?>');return false;" href="#the-list"><?php _e('Attach'); ?></a></td>
			<?php
		}

		break;

	case 'comments':
		$attributes = 'class="comments column-comments num"' . $style;
		?>
		<td <?php echo $attributes ?>><div class="post-com-count-wrapper">
		<?php
		$left = get_pending_comments_num( $post->ID );
		$pending_phrase = sprintf( __('%s pending'), number_format( $left ) );
		if ( $left )
			echo '<strong>';
		comments_number("<a href='edit-comments.php?p=$id' title='$pending_phrase' class='post-com-count'><span class='comment-count'>" . /* translators: comment count link */ _x('0', 'comment count') . '</span></a>', "<a href='edit-comments.php?p=$id' title='$pending_phrase' class='post-com-count'><span class='comment-count'>" . /* translators: comment count link */ _x('1', 'comment count') . '</span></a>', "<a href='edit-comments.php?p=$id' title='$pending_phrase' class='post-com-count'><span class='comment-count'>" . /* translators: comment count link: % will be substituted by comment count */ _x('%', 'comment count') . '</span></a>');
		if ( $left )
			echo '</strong>';
		?>
		</div></td>
		<?php
		break;

	case 'actions':
		?>
		<td <?php echo $attributes ?>>
		<a href="media.php?action=edit&amp;attachment_id=<?php the_ID(); ?>" title="<?php echo esc_attr(sprintf(__('Edit &#8220;%s&#8221;'), $att_title)); ?>"><?php _e('Edit'); ?></a> |
		<a href="<?php the_permalink(); ?>"><?php _e('Get permalink'); ?></a>
		</td>
		<?php
		break;

	default:
		?>
		<td <?php echo $attributes ?>><?php do_action('manage_media_custom_column', $column_name, $id); ?></td>
		<?php
		break;
	}
}
?>
	</tr>
<?php endwhile; ?>
	</tbody>
</table>
<?php } else { ?>

<p><?php _e('No media attachments found.') ?></p>

<?php
} // end if ( have_posts() )
?>

