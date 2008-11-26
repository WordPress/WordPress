<?php
/**
 * Edit posts rows table for inclusion in administration panels.
 *
 * @package WordPress
 * @subpackage Administration
 */

if ( ! defined('ABSPATH') ) die();
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