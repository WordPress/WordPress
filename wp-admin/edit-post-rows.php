<?php if ( ! defined('ABSPATH') ) die(); ?>
<table class="widefat">
	<thead>
	<tr>

<?php foreach($posts_columns as $column_display_name) { ?>
	<th scope="col"><?php echo $column_display_name; ?></th>
<?php } ?>

	</tr>
	</thead>
	<tbody id="the-list">
<?php
if ( have_posts() ) {
$bgcolor = '';
add_filter('the_title','wp_specialchars');
while (have_posts()) : the_post();
$class = ('alternate' == $class) ? '' : 'alternate';
global $current_user;
$post_owner = ( $current_user->ID == $post->post_author ? 'self' : 'other' );
?>
	<tr id='post-<?php echo $id; ?>' class='<?php echo trim( $class . ' author-' . $post_owner . ' status-' . $post->post_status ); ?>'>

<?php

foreach($posts_columns as $column_name=>$column_display_name) {

	switch($column_name) {

	case 'id':
		?>
		<th scope="row" style="text-align: center"><?php echo $id ?></th>
		<?php
		break;
	case 'modified':
		?>
		<td><?php if ( '0000-00-00 00:00:00' ==$post->post_modified ) _e('Never'); else the_modified_time(__('Y-m-d \<\b\r \/\> g:i:s a')); ?></td>
		<?php
		break;
	case 'date':
		?>
		<td><?php if ( '0000-00-00 00:00:00' ==$post->post_date) _e('Unpublished'); else the_time(__('Y-m-d \<\b\r \/\> g:i:s a')); ?></td>
		<?php
		break;
	case 'title':
		?>
		<td><?php the_title() ?>
		<?php if ('private' == $post->post_status) _e(' - <strong>Private</strong>'); ?></td>
		<?php
		break;

	case 'categories':
		?>
		<td><?php the_category(','); ?></td>
		<?php
		break;

	case 'comments':
		?>
		<td style="text-align: center">
		<?php
		$left = get_pending_comments_num( $post->ID );
		$pending_phrase = sprintf( __('%s pending'), number_format( $left ) );
		if ( $left )
			echo '<strong>';
		comments_number("<a href='edit.php?p=$id&amp;c=1' title='$pending_phrase'>" . __('0') . '</a>', "<a href='edit.php?p=$id&amp;c=1' title='$pending_phrase'>" . __('1') . '</a>', "<a href='edit.php?p=$id&amp;c=1' title='$pending_phrase'>" . __('%') . '</a>');
		if ( $left )
			echo '</strong>';
		?>
		</td>
		<?php
		break;

	case 'author':
		?>
		<td><?php the_author() ?></td>
		<?php
		break;

	case 'control_view':
		?>
		<td><a href="<?php the_permalink(); ?>" rel="permalink" class="view"><?php _e('View'); ?></a></td>
		<?php
		break;

	case 'control_edit':
		?>
		<td><?php if ( current_user_can('edit_post',$post->ID) ) { echo "<a href='post.php?action=edit&amp;post=$id' class='edit'>" . __('Edit') . "</a>"; } ?></td>
		<?php
		break;

	case 'control_delete':
		?>
		<td><?php if ( current_user_can('delete_post',$post->ID) ) { echo "<a href='" . wp_nonce_url("post.php?action=delete&amp;post=$id", 'delete-post_' . $post->ID) . "' class='delete' onclick=\"return deleteSomething( 'post', " . $id . ", '" . js_escape(sprintf(__("You are about to delete this post '%s'.\n'OK' to delete, 'Cancel' to stop."), get_the_title())) . "' );\">" . __('Delete') . "</a>"; } ?></td>
		<?php
		break;

	default:
		?>
		<td><?php do_action('manage_posts_custom_column', $column_name, $id); ?></td>
		<?php
		break;
	}
}
?>
	</tr>
<?php
endwhile;
} else {
?>
  <tr style='background-color: <?php echo $bgcolor; ?>'>
    <td colspan="8"><?php _e('No posts found.') ?></td>
  </tr>
<?php
} // end if ( have_posts() )
?>
	</tbody>
</table>
