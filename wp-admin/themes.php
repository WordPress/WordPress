<?php

if ( isset($_GET['action']) ) {
	$standalone = 1;
	require_once('admin-header.php');

	check_admin_referer();

	if ('activate' == $_GET['action']) {
	  if (isset($_GET['template'])) {
	    update_option('template', $_GET['template']);
	  }

	  if (isset($_GET['stylesheet'])) {
	    update_option('stylesheet', $_GET['stylesheet']);
	  }

	  header('Location: themes.php?activate=true');
	}
 }

require_once('../wp-includes/wp-l10n.php');
$title = __('Manage Themes');
$parent_file = 'themes.php';
require_once('admin-header.php');

if ($user_level < 9) // Must be at least level 9
	die (__('Sorry, you must be at least a level 8 user to modify themes.'));
?>

<?php
$themes = get_themes();
$theme_names = array_keys($themes);
natcasesort($theme_names);
$current_template = get_settings('template');
$current_stylesheet = get_settings('stylesheet');
$current_theme = 'Default';
$current_parent_theme = '';
$current_template_dir = '/';
$current_stylesheet_dir = '/';

if ($themes) {
	foreach ($theme_names as $theme_name) {
		if ($themes[$theme_name]['Stylesheet'] == $current_stylesheet &&
				$themes[$theme_name]['Template'] == $current_template) {
			$current_theme = $themes[$theme_name]['Name'];
			if ($current_template != 'default')
				$current_template_dir = dirname($themes[$theme_name]['Template Files'][0]);
			if ($current_stylesheet != 'default')
				$current_stylesheet_dir = dirname($themes[$theme_name]['Stylesheet Files'][0]);
		}

		if (($current_template != $current_stylesheet) &&
				($themes[$theme_name]['Stylesheet'] == $themes[$theme_name]['Template']) &&
				($themes[$theme_name]['Template'] == $current_template)) {
			$current_parent_theme = $themes[$theme_name]['Name'];
		}
	}
}
?>

<?php if ($current_parent_theme) { ?>
	<div class="updated"><p><?php printf(__('The active theme is <strong>%s</strong>.  The template files are located in <code>%s</code>.  The stylesheet files are located in <code>%s</code>.  <strong>%s</strong> uses templates from <strong>%s</strong>.  Changes made to the templates will affect both themes.'), $current_theme, $current_template_dir, $current_stylesheet_dir, $current_theme, $current_parent_theme); ?></p></div>
<?php } else { ?>
	<div class="updated"><p><?php printf(__('The active theme is <strong>%s</strong>.  The template files are located in <code>%s</code>.  The stylesheet files are located in <code>%s</code>.'), $current_theme, $current_template_dir, $current_stylesheet_dir); ?></p></div>
<?php } ?>

<div class="wrap">
<h2><?php _e('Theme Management'); ?></h2>
<p><?php _e('Themes are usually downloaded separately from WordPress. To install a theme you generally just need to put the theme file or files into your <code>wp-content/themes</code> directory. Once a theme is installed, you may select it here.'); ?></p>
<?php

if (empty($themes)) {
	_e("<p>Couldn't open themes directory or there are no themes available.</p>"); // TODO: make more helpful
} else {
?>
<table width="100%" cellpadding="3" cellspacing="3">
	<tr>
		<th><?php _e('Theme'); ?></th>
		<th><?php _e('Version'); ?></th>
		<th><?php _e('Author'); ?></th>
		<th><?php _e('Description'); ?></th>
		<th><?php _e('Select'); ?></th>
	</tr>
<?php
	$theme = '';

	$theme_names = array_keys($themes);
	natcasesort($theme_names);

	foreach ($theme_names as $theme_name) {
		$template = $themes[$theme_name]['Template'];
		$stylesheet = $themes[$theme_name]['Stylesheet'];
		$title = $themes[$theme_name]['Title'];
		$version = $themes[$theme_name]['Version'];
		$description = $themes[$theme_name]['Description'];
		$author = $themes[$theme_name]['Author'];

		if ($template == $current_template && $stylesheet == $current_stylesheet) {
			$action = "<a href='themes.php' title='" . __('Active theme') . "' class='edit'>" . __('Active Theme') . '</a>';
		} else {
			$action = "<a href='themes.php?action=activate&amp;template=$template&amp;stylesheet=$stylesheet' title='" . __('Select this theme') . "' class='edit'>" . __('Select') . '</a>';
		}

		$theme = ('class="alternate"' == $theme) ? '' : 'class="alternate"';
		echo "
	  <tr $theme>
	     <td>$title</td>
	     <td>$version</td>
	     <td>$author</td>
	     <td>$description</td>
	     <td>$action</td>
	  </tr>";
	}
?>

</table>
<?php
}
?>
</div>

<?php
include('admin-footer.php');
?>