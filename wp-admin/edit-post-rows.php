<?php if ( ! defined('ABSPATH') ) die(); ?>
<table class="widefat">
	<thead>
	<tr>

<?php $posts_columns = wp_manage_posts_columns(); ?>
<?php foreach($posts_columns as $column_display_name) { ?>
	<th scope="col"><?php echo $column_display_name; ?></th>
<?php } ?>

	</tr>
	</thead>
	<tbody id="the-list" class="list:post">
<?php
$i_post = 0;
if ( have_posts() ) {
$bgcolor = '';
add_filter('the_title','wp_specialchars');
while (have_posts()) : the_post(); $i_post++;
if ( 16 == $i_post )
	echo "\t</tbody>\n\t<tbody id='the-extra-list' class='list:post' style='display: none'>\n"; // Hack!
$class = ( $i_post > 15 || 'alternate' == $class) ? '' : 'alternate';
global $current_user;
$post_owner = ( $current_user->ID == $post->post_author ? 'self' : 'other' );
?>
	<tr id='post-<?php echo $id; ?>' class='<?php echo trim( $class . ' author-' . $post_owner . ' status-' . $post->post_status ); ?>' valign="top">

<?php

foreach($posts_columns as $column_name=>$column_display_name) {

	switch($column_name) {

	case 'cb':
		?>
		<th scope="row" style="text-align: center"><input type="checkbox" name="delete[]" value="<?php the_ID(); ?>" /></th>
		<?php
		break;
	case 'modified':
		?>
		<td><?php if ( '0000-00-00 00:00:00' ==$post->post_modified ) _e('Never'); else the_modified_time(__('Y/m/d \<\b\r \/\> g:i:s a')); ?></td>
		<?php
		break;
	case 'date':
		?>
		<td><a href="<?php the_permalink(); ?>" rel="permalink">
		<?php 
		if ( '0000-00-00 00:00:00' ==$post->post_date ) {
			_e('Unpublished');
		} else {
			if ( ( abs(time() - get_post_time()) ) < 86400 ) {
				if ( ( 'future' == $post->post_status) )
					echo sprintf( __('%s from now'), human_time_diff( get_post_time() ) );
				else
					echo sprintf( __('%s ago'), human_time_diff( get_post_time() ) );
			} else {
				the_time(__('Y/m/d'));
			}
		}
		?></a></td>
		<?php
		break;
	case 'title':
		?>
		<td><strong><a href="post.php?action=edit&post=<?php the_ID(); ?>"><?php the_title() ?></a></strong>
		<?php if ('private' == $post->post_status) _e(' &#8212; <strong>Private</strong>'); ?></td>
		<?php
		break;

	case 'categories':
		?>
		<td><?php
		$categories = get_the_category();
		if ( !empty( $categories ) ) {
			$out = array();
			foreach ( $categories as $c )
				$out[] = "<a href='edit.php?category_name=$c->slug'> " . wp_specialchars(sanitize_term_field('name', $c->name, $c->term_id, 'category', 'display')) . "</a>";
			echo join( ', ', $out );
		} else {
			_e('Uncategorized');
		}
		?></td>
		<?php
		break;

	case 'tags':
		?>
		<td><?php
		$tags = get_the_tags();
		if ( !empty( $tags ) ) {
			$out = array();
			foreach ( $tags as $c )
				$out[] = "<a href='edit.php?tag=$c->slug'> " . wp_specialchars(sanitize_term_field('name', $c->name, $c->term_id, 'post_tag', 'display')) . "</a>";
			echo join( ', ', $out );
		} else {
			_e('No Tags');
		}
		?></td>
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
		comments_number("<a href='edit.php?p=$id&amp;c=1' title='$pending_phrase' class='post-com-count comment-count'><span>" . __('0') . '</span></a>', "<a href='edit.php?p=$id&amp;c=1' title='$pending_phrase' class='post-com-count comment-count'><span>" . __('1') . '</span></a>', "<a href='edit.php?p=$id&amp;c=1' title='$pending_phrase' class='post-com-count comment-count'><span>" . __('%') . '</span></a>');
		if ( $left )
			echo '</strong>';
		?>
		</td>
		<?php
		break;

	case 'author':
		?>
		<td><a href="edit.php?author=<?php the_author_ID(); ?>"><?php the_author() ?></a></td>
		<?php
		break;

	case 'status':
		?>
		<td>
		<?php
		switch ( $post->post_status ) {
			case 'publish' :
			case 'private' :
				_e('Published');
				break;
			case 'future' :
				_e('Scheduled');
				break;
			case 'pending' :
				_e('Pending Review');
				break;
			case 'draft' :
				_e('Unpublished');
				break;
		}
		?>
		</td>
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
		<td><?php if ( current_user_can('delete_post',$post->ID) ) { echo "<a href='" . wp_nonce_url("post.php?action=delete&amp;post=$id", 'delete-post_' . $post->ID) . "' class='delete:the-list:post-$post->ID delete'>" . __('Delete') . "</a>"; } ?></td>
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
