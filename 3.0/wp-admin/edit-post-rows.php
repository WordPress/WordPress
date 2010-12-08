<?php
/**
 * Edit posts rows table for inclusion in administration panels.
 *
 * @package WordPress
 * @subpackage Administration
 */

// don't load directly
if ( !defined('ABSPATH') )
	die('-1');
?>
<table class="widefat <?php echo $post_type_object->hierarchical ? 'page' : 'post'; ?> fixed" cellspacing="0">
	<thead>
	<tr>
<?php print_column_headers( $current_screen ); ?>
	</tr>
	</thead>

	<tfoot>
	<tr>
<?php print_column_headers($current_screen, false); ?>
	</tr>
	</tfoot>

	<tbody>
<?php
if ( $post_type_object->hierarchical )
	page_rows($posts, $pagenum, $per_page);
else
	post_rows();
?>
	</tbody>
</table>