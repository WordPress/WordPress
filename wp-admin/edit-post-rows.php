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
<table class="widefat post fixed" cellspacing="0">
	<thead>
	<tr>
<?php print_column_headers('edit'); ?>
	</tr>
	</thead>

	<tfoot>
	<tr>
<?php print_column_headers('edit', false); ?>
	</tr>
	</tfoot>

	<tbody>
<?php post_rows(); ?>
	</tbody>
</table>