<?php
/**
 * Edit posts rows table for inclusion in administration panels.
 *
 * @package WordPress
 * @subpackage Administration
 */

if ( ! defined('ABSPATH') ) die();
?>
<table class="widefat post">
	<thead>
	<tr>
<?php print_column_headers('post'); ?>
	</tr>
	</thead>
	<tbody>

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
	<thead>
	<tr>
<?php print_column_headers('post'); ?>
	</tr>
	</thead>
</table>
