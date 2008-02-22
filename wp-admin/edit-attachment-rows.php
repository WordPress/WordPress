<?php if ( ! defined('ABSPATH') ) die(); ?>
<table class="widefat">
	<thead>
	<tr>

<?php $posts_columns = wp_manage_media_columns(); ?>
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

	case 'icon':
		?>
		<td class="media-icon"><?php echo the_attachment_link($post->ID, false, array(48,48)); ?></td>
		<?php
		// TODO
		break;

	case 'media':
		?>
		<td><strong><a href="# TODO: upload.php?action=edit&amp;post=<?php the_ID(); ?>"><?php the_title(); ?></a></strong><br />
		<?php echo strtoupper(preg_replace('/^.*?\.(\w+)$/', '$1', $post->guid)); ?>
		<?php do_action('manage_media_media_column', $post->ID); ?>
		</td>
		<?php
		break;

	case 'desc':
		?>
		<td><?php echo has_excerpt() ? $post->post_excerpt : ''; ?></td>
		<?php
		break;

	case 'date':
		if ( '0000-00-00 00:00:00' == $post->post_date && 'date' == $column_name ) {
			$t_time = $h_time = __('Unpublished');
		} else {
			$t_time = get_the_time(__('Y/m/d g:i:s A'));
			$m_time = $post->post_date;
			$time = get_post_time();
			if ( ( abs(time() - $time) ) < 86400 ) {
				if ( 'future' == get_post_status($post->ID) )
					$h_time = sprintf( __('%s from now'), human_time_diff( $time ) );
				else
					$h_time = sprintf( __('%s ago'), human_time_diff( $time ) );
			} else {
				$h_time = mysql2date(__('Y/m/d'), $m_time);
			}
		}
		?>
		<td><?php echo $h_time ?></td>
		<?php
		break;

	case 'parent':
		$title = get_the_title($post->post_parent);
		if ( empty($title) )
			$title = __('(no title)');
		?>
		<td><strong><a href="post.php?action=edit&amp;post=<?php echo $post->post_parent; ?>"><?php echo $title ?></a></strong></td>
		<?php
		break;

	case 'location':
		?>
		<td><a href="<?php the_permalink(); ?>"><?php _e('Permalink'); ?></a></td>
		<?php
		break;

	default:
		?>
		<td><?php do_action('manage_media_custom_column', $column_name, $id); ?></td>
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
