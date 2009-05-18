<?php
/**
 * Import WordPress Administration Panel
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Load WordPress Bootstrap */
require_once ('admin.php');
$title = __('Import');
require_once ('admin-header.php');
$parent_file = 'tools.php';
?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?></h2>
<p><?php _e('If you have posts or comments in another system, WordPress can import those into this blog. To get started, choose a system to import from below:'); ?></p>

<?php

// Load all importers so that they can register.
$import_loc = 'wp-admin/import';
$import_root = ABSPATH.$import_loc;
$imports_dir = @ opendir($import_root);
if ($imports_dir) {
	while (($file = readdir($imports_dir)) !== false) {
		if ($file{0} == '.') {
			continue;
		} elseif (substr($file, -4) == '.php') {
			require_once($import_root . '/' . $file);
		}
	}
}
@closedir($imports_dir);

$importers = get_importers();

if (empty ($importers)) {
	echo '<p>'.__('No importers are available.').'</p>'; // TODO: make more helpful
} else {
?>
<table class="widefat" cellspacing="0">

<?php
	$style = '';
	foreach ($importers as $id => $data) {
		$style = ('class="alternate"' == $style || 'class="alternate active"' == $style) ? '' : 'alternate';
		$action = "<a href='admin.php?import=$id' title='".wptexturize(strip_tags($data[1]))."'>{$data[0]}</a>";

		if ($style != '')
			$style = 'class="'.$style.'"';
		echo "
			<tr $style>
				<td class='import-system row-title'>$action</td>
				<td class='desc'>{$data[1]}</td>
			</tr>";
	}
?>

</table>
<?php
}
?>

</div>

<?php

include ('admin-footer.php');
?>

