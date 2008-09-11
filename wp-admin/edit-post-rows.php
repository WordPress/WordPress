<?php
/**
 * Edit posts rows table for inclusion in administration panels.
 *
 * @package WordPress
 * @subpackage Administration
 */

if ( ! defined('ABSPATH') ) die();
?>
<table class="widefat">
	<thead>
	<tr>

<?php
$posts_columns = wp_manage_posts_columns(); 
$hidden = (array) get_user_option( 'manage-post-columns-hidden' );
foreach ( $posts_columns as $post_column_key => $column_display_name ) {
	if ( 'cb' === $post_column_key )
		$class = ' class="check-column"';
	elseif ( 'comments' === $post_column_key )
		$class = ' class="manage-column column-comments num"';
	elseif ( 'modified' === $post_column_key )
		$class = ' class="manage-column column-date"';
	else
		$class = " class=\"manage-column column-$post_column_key\"";

	$style = '';
	if ( in_array($post_column_key, $hidden) )
		$style = ' style="display:none;"';
?>
	<th scope="col"<?php echo "id=\"$post_column_key\""; echo $class; echo $style?>><?php echo $column_display_name; ?></th>
<?php } ?>

	</tr>
	</thead>
	<tbody>
	  
<?php inline_edit_row( 'post' ) ?>	  

<?php
if ( have_posts() ) {
	post_rows();
} else {
?>
  <tr>
    <td colspan="8"><?php _e('No posts found.') ?></td>
  </tr>
<?php
} // end if ( have_posts() )
?>
	</tbody>
</table>
