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

		do_action('switch_theme', get_current_theme());

	  header('Location: themes.php?activated=true');
	}
 }

require_once('../wp-includes/wp-l10n.php');
$title = __('Manage Themes');
$parent_file = 'themes.php';
require_once('admin-header.php');

if ($user_level < 9)
	die (__('Sorry, you must be at least a level 9 user to modify themes.'));
?>

<?php if ( ! validate_current_theme() ) : ?>
<div class="updated"><p><?php _e('The active theme is broken.  Reverting to the default theme.'); ?></p></div>
<?php elseif ( isset($activated) ) : ?>
<div class="updated"><p><?php _e('New theme activated'); ?></p></div>
<?php endif; ?>

<?php
$themes = get_themes();
$current_theme = get_current_theme();
$current_title = $themes[$current_theme]['Title'];
$current_version = $themes[$current_theme]['Version'];
$current_parent_theme = $themes[$current_theme]['Parent Theme'];
$current_template_dir = $themes[$current_theme]['Template Dir'];
$current_stylesheet_dir = $themes[$current_theme]['Stylesheet Dir'];
$current_template = $themes[$current_theme]['Template'];
$current_stylesheet = $themes[$current_theme]['Stylesheet'];
?>

<div class="wrap">
<h2><?php _e('Current Theme'); ?></h2>
<div id="currenttheme">
<h3><?php printf(__('%s %s by %s'), $current_title, $current_version, $themes[$current_theme]['Author']) ; ?></h3>
<p><?php echo $themes[$current_theme]['Description']; ?></p>
<?php if ($current_parent_theme) { ?>
	<p><?php printf(__('The active theme is <strong>%s</strong>.  The template files are located in <code>%s</code>.  The stylesheet files are located in <code>%s</code>.  <strong>%s</strong> uses templates from <strong>%s</strong>.  Changes made to the templates will affect both themes.'), $current_theme, $current_template_dir, $current_stylesheet_dir, $current_theme, $current_parent_theme); ?></p>
<?php } else { ?>
	<p><?php printf(__('The active theme is <strong>%s</strong>.  The template files are located in <code>%s</code>.  The stylesheet files are located in <code>%s</code>.'), $current_theme, $current_template_dir, $current_stylesheet_dir); ?></p>
<?php } ?>
</div>

<h2><?php _e('Other Themes'); ?></h2>
<p><?php _e('Themes are usually downloaded separately from WordPress. To install a theme you generally just need to put the theme file or files into your <code>wp-content/themes</code> directory. Once a theme is installed, you may select it here.'); ?></p>

<?php if ( 1 < count($themes) ) { ?>
<table width="100%" cellpadding="3" cellspacing="3">
	<tr>
		<th><?php _e('Name'); ?></th>
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
			$action = __('Active Theme');
		} else {
			$action = "<a href='themes.php?action=activate&amp;template=$template&amp;stylesheet=$stylesheet' title='" . __('Select this theme') . "' class='edit'>" . __('Select') . '</a>';
		}

		$theme = ('class="alternate"' == $theme) ? '' : 'class="alternate"';
		echo "
	  <tr $theme>
	     <td>$title $version</td>
	     <td align='center'>$author</td>
	     <td>$description</td>
	     <td align='center'>$action</td>
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