<?php
require_once ('admin.php');
$title = __('Import');
$parent_file = 'import.php';
require_once ('admin-header.php');
?>

<div class="wrap">
<h2><?php _e('Import'); ?></h2>
<p><?php _e('If you have posts or comments in another system WordPress can import them into your current blog. To get started, choose a system to import from below:'); ?></p>

<?php

// Load all importers so that they can register.
$import_loc = 'wp-admin/import';
$import_root = ABSPATH.$import_loc;
$imports_dir = @ dir($import_root);
if ($imports_dir) {
	while (($file = $imports_dir->read()) !== false) {
		if (preg_match('|^\.+$|', $file))
			continue;
		if (preg_match('|\.php$|', $file))
			require_once("$import_root/$file");
	}
}

$importers = get_importers();

if (empty ($importers)) {
	_e("<p>No importers are available.</p>"); // TODO: make more helpful
} else {
?>
<table width="100%" cellpadding="3" cellspacing="3">

<?php
	$style = '';
	foreach ($importers as $id => $data) {
		$style = ('class="alternate"' == $style || 'class="alternate active"' == $style) ? '' : 'alternate';
		$action = "<a href='admin.php?import=$id' title='{$data[1]}'>{$data[0]}</a>";

		if ($style != '')
			$style = 'class="'.$style.'"';
		echo "
			<tr $style>
				<td class=\"togl\">$action</td>
				<td class=\"desc\">{$data[1]}</td>
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

