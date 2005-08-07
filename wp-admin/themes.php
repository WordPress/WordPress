<?php
require_once('admin.php');

if ( isset($_GET['action']) ) {
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

$title = __('Manage Themes');
$parent_file = 'themes.php';
require_once('admin-header.php');
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
$current_screenshot = $themes[$current_theme]['Screenshot'];
?>

<div class="wrap">
<h2><?php _e('Current Theme'); ?></h2>
<div id="currenttheme">
<?php if ( $current_screenshot ) : ?>
<img src="<?php echo get_option('siteurl') . '/' . $current_stylesheet_dir . '/' . $current_screenshot; ?>" alt="Current theme preview" />
<?php endif; ?>
<h3><?php printf(__('%1$s %2$s by %3$s'), $current_title, $current_version, $themes[$current_theme]['Author']) ; ?></h3>
<p><?php echo $themes[$current_theme]['Description']; ?></p>
<?php if ($current_parent_theme) { ?>
	<p><?php printf(__('The template files are located in <code>%2$s</code>.  The stylesheet files are located in <code>%3$s</code>.  <strong>%4$s</strong> uses templates from <strong>%5$s</strong>.  Changes made to the templates will affect both themes.'), $current_theme, $current_template_dir, $current_stylesheet_dir, $current_theme, $current_parent_theme); ?></p>
<?php } else { ?>
	<p><?php printf(__('All of this theme&#8217;s files are located in <code>%2$s</code>.'), $current_theme, $current_template_dir, $current_stylesheet_dir); ?></p>
<?php } ?>
</div>

<h2><?php _e('Available Themes'); ?></h2>
<?php if ( 1 < count($themes) ) { ?>

<?php
$style = '';

$theme_names = array_keys($themes);
natcasesort($theme_names);

foreach ($theme_names as $theme_name) {
	if ( $theme_name == $current_theme )
		continue;
	$template = $themes[$theme_name]['Template'];
	$stylesheet = $themes[$theme_name]['Stylesheet'];
	$title = $themes[$theme_name]['Title'];
	$version = $themes[$theme_name]['Version'];
	$description = $themes[$theme_name]['Description'];
	$author = $themes[$theme_name]['Author'];
	$screenshot = $themes[$theme_name]['Screenshot'];
	$stylesheet_dir = $themes[$theme_name]['Stylesheet Dir'];
?>
<div class="available-theme">
<h3><a href="<?php echo "themes.php?action=activate&amp;template=$template&amp;stylesheet=$stylesheet"; ?>"><?php echo "$title $version"; ?>
<span>
<?php if ( $screenshot ) : ?>
<img src="<?php echo get_option('siteurl') . '/' . $stylesheet_dir . '/' . $screenshot; ?>" alt="" />
<?php endif; ?>
</span>
</a></h3>
<p><?php echo $description; ?></p>
</div>
<?php
/*
		if ($template == $current_template && $stylesheet == $current_stylesheet) {
			$action = '<strong>' . __('Active Theme') . '</strong>';
			$current = true;
		} else {
			$action = "<a href='' title='" . __('Select this theme') . "' class='edit'>" . __('Select') . '</a>';
			$current = false;
		}

		$style = ('class="alternate"' == $style|| 'class="alternate active"' == $style) ? '' : 'alternate';
		if ($current) $style .= $style == 'alternate' ? ' active' : 'active';
		if ($style != '') $style = 'class="' . $style . '"';

		echo "
	  <tr $style>";
if ( $current )
	echo "<td><strong>$title $version</strong></td>";
else
	echo "<td></td>";
echo "
	     <td class=\"auth\">$author</td>
	     <td class=\"desc\">$description</td>
	     <td class=\"togl\">$action</td>
	  </tr>";
*/
	}

?>

<?php
}
?>

<?php
// List broken themes, if any.
$broken_themes = get_broken_themes();
if (count($broken_themes)) {
?>

<h2><?php _e('Broken Themes'); ?></h2>
<p><?php _e('The following themes are installed but incomplete.  Themes must have a stylesheet and a template.'); ?></p>

<table width="100%" cellpadding="3" cellspacing="3">
	<tr>
		<th><?php _e('Name'); ?></th>
		<th><?php _e('Description'); ?></th>
	</tr>
<?php
	$theme = '';
	
	$theme_names = array_keys($broken_themes);
	natcasesort($theme_names);

	foreach ($theme_names as $theme_name) {
		$title = $broken_themes[$theme_name]['Title'];
		$description = $broken_themes[$theme_name]['Description'];

		$theme = ('class="alternate"' == $theme) ? '' : 'class="alternate"';
		echo "
	  <tr $theme>
	     <td>$title</td>
	     <td>$description</td>
	  </tr>";
	}
?>
</table>
<?php
}
?>

<h2><?php _e('Get More Themes'); ?></h2>
<p><?php _e('You can find additional themes for your site in the <a href="http://wordpress.org/extend/themes/">WordPress theme directory</a>. To install a theme you generally just need to upload the theme folder into your <code>wp-content/themes</code> directory. Once a theme is uploaded, you may activate it on this page.'); ?></p>

</div>

<?php
include('admin-footer.php');
?>