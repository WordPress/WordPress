<?php
/**
 * Themes administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

if ( !current_user_can('switch_themes') )
	wp_die( __( 'Cheatin&#8217; uh?' ) );

if ( is_multisite() ) {
	$themes = get_themes();
	$ct = current_theme_info();
	$allowed_themes = apply_filters("allowed_themes", get_site_allowed_themes() ); 
	if( $allowed_themes == false )
		$allowed_themes = array();

	$blog_allowed_themes = wpmu_get_blog_allowedthemes();
	if( is_array( $blog_allowed_themes ) )
		$allowed_themes = array_merge( $allowed_themes, $blog_allowed_themes );
	if( $blog_id != 1 )
		unset( $allowed_themes[ "h3" ] );

	if( isset( $allowed_themes[ wp_specialchars( $ct->stylesheet ) ] ) == false )
		$allowed_themes[ wp_specialchars( $ct->stylesheet ) ] = true;

	reset( $themes );
	foreach( $themes as $key => $theme ) {
		if( isset( $allowed_themes[ wp_specialchars( $theme[ 'Stylesheet' ] ) ] ) == false ) {
			unset( $themes[ $key ] );
		}
	}
	reset( $themes );
}
if ( isset($_GET['action']) ) {
	if ( 'activate' == $_GET['action'] ) {
		check_admin_referer('switch-theme_' . $_GET['template']);
		switch_theme($_GET['template'], $_GET['stylesheet']);
		wp_redirect('themes.php?activated=true');
		exit;
	} else if ( 'delete' == $_GET['action'] ) {
		check_admin_referer('delete-theme_' . $_GET['template']);
		if ( !current_user_can('update_themes') )
			wp_die( __( 'Cheatin&#8217; uh?' ) );
		delete_theme($_GET['template']);
		wp_redirect('themes.php?deleted=true');
		exit;
	}
}

$title = __('Manage Themes');
$parent_file = 'themes.php';

$help = '<p>' . __('Themes give your WordPress style. Once a theme is installed, you may preview it, activate it or deactivate it here.') . '</p>';
if ( current_user_can('install_themes') ) {
	$help .= '<p>' . sprintf(__('You can find additional themes for your site by using the new <a href="%1$s">Theme Browser/Installer</a> functionality or by browsing the <a href="http://wordpress.org/extend/themes/">WordPress Theme Directory</a> directly and installing manually.  To install a theme <em>manually</em>, <a href="%2$s">upload its ZIP archive with the new uploader</a> or copy its folder via FTP into your <code>wp-content/themes</code> directory.'), 'theme-install.php', 'theme-install.php?tab=upload' ) . '</p>';
	$help .= '<p>' . __('Once a theme is uploaded, you should see it on this page.') . '</p>' ;
}

add_contextual_help($current_screen, $help);

add_thickbox();
wp_enqueue_script( 'theme-preview' );

require_once('admin-header.php');
if( is_multisite() && is_super_admin() ) {
	?><div id="message0" class="updated fade"><p><?php _e('Administrator: new themes must be activated in the <a href="wpmu-themes.php">Themes Admin</a> page before they appear here.'); ?></p></div><?php
}
?>

<?php if ( ! validate_current_theme() ) : ?>
<div id="message1" class="updated"><p><?php _e('The active theme is broken.  Reverting to the default theme.'); ?></p></div>
<?php elseif ( isset($_GET['activated']) ) :
		if ( isset($wp_registered_sidebars) && count( (array) $wp_registered_sidebars ) ) { ?>
<div id="message2" class="updated"><p><?php printf(__('New theme activated. This theme supports widgets, please visit the <a href="%s">widgets settings page</a> to configure them.'), admin_url('widgets.php') ); ?></p></div><?php
		} else { ?>
<div id="message2" class="updated"><p><?php printf(__('New theme activated. <a href="%s">Visit site</a>'), get_bloginfo('url') . '/'); ?></p></div><?php
		}
	elseif ( isset($_GET['deleted']) ) : ?>
<div id="message3" class="updated"><p><?php _e('Theme deleted.') ?></p></div>
<?php endif; ?>

<?php
if ( !is_multisite() )
	$themes = get_themes();
$ct = current_theme_info();
unset($themes[$ct->name]);

uksort( $themes, "strnatcasecmp" );
$theme_total = count( $themes );
$per_page = 15;

if ( isset( $_GET['pagenum'] ) )
	$page = absint( $_GET['pagenum'] );

if ( empty($page) )
	$page = 1;

$start = $offset = ( $page - 1 ) * $per_page;

$page_links = paginate_links( array(
	'base' => add_query_arg( 'pagenum', '%#%' ) . '#themenav',
	'format' => '',
	'prev_text' => __('&laquo;'),
	'next_text' => __('&raquo;'),
	'total' => ceil($theme_total / $per_page),
	'current' => $page
));

$themes = array_slice( $themes, $start, $per_page );

/**
 * Check if there is an update for a theme available.
 *
 * Will display link, if there is an update available.
 *
 * @since 2.7.0
 *
 * @param object $theme Theme data object.
 * @return bool False if no valid info was passed.
 */
function theme_update_available( $theme ) {
	static $themes_update;

	if ( is_multisite() && !is_super_admin() )
		return;

	if ( !isset($themes_update) )
		$themes_update = get_site_transient('update_themes');

	if ( is_object($theme) && isset($theme->stylesheet) )
		$stylesheet = $theme->stylesheet;
	elseif ( is_array($theme) && isset($theme['Stylesheet']) )
		$stylesheet = $theme['Stylesheet'];
	else
		return false; //No valid info passed.

	if ( isset($themes_update->response[ $stylesheet ]) ) {
		$update = $themes_update->response[ $stylesheet ];
		$theme_name = is_object($theme) ? $theme->name : (is_array($theme) ? $theme['Name'] : '');
		$details_url = add_query_arg(array('TB_iframe' => 'true', 'width' => 1024, 'height' => 800), $update['url']); //Theme browser inside WP? replace this, Also, theme preview JS will override this on the available list.
		$update_url = wp_nonce_url('update.php?action=upgrade-theme&amp;theme=' . urlencode($stylesheet), 'upgrade-theme_' . $stylesheet);
		$update_onclick = 'onclick="if ( confirm(\'' . esc_js( __("Upgrading this theme will lose any customizations you have made.  'Cancel' to stop, 'OK' to upgrade.") ) . '\') ) {return true;}return false;"';

		if ( ! current_user_can('update_themes') )
			printf( '<p><strong>' . __('There is a new version of %1$s available. <a href="%2$s" class="thickbox" title="%1$s">View version %3$s Details</a>.') . '</strong></p>', $theme_name, $details_url, $update['new_version']);
		else if ( empty($update->package) )
			printf( '<p><strong>' . __('There is a new version of %1$s available. <a href="%2$s" class="thickbox" title="%1$s">View version %3$s Details</a> <em>automatic upgrade unavailable for this theme</em>.') . '</strong></p>', $theme_name, $details_url, $update['new_version']);
		else
			printf( '<p><strong>' . __('There is a new version of %1$s available. <a href="%2$s" class="thickbox" title="%1$s">View version %3$s Details</a> or <a href="%4$s" %5$s >upgrade automatically</a>.') . '</strong></p>', $theme_name, $details_url, $update['new_version'], $update_url, $update_onclick );
	}
}

?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); if ( !is_multisite() || is_super_admin() ) { ?> <a href="theme-install.php" class="button add-new-h2"><?php echo esc_html_x('Add New', 'theme'); ?></a><?php } ?></h2>

<h3><?php _e('Current Theme'); ?></h3>
<div id="current-theme">
<?php if ( $ct->screenshot ) : ?>
<img src="<?php echo $ct->theme_root_uri . '/' . $ct->stylesheet . '/' . $ct->screenshot; ?>" alt="<?php _e('Current theme preview'); ?>" />
<?php endif; ?>
<h4><?php
	/* translators: 1: theme title, 2: theme version, 3: theme author */
	printf(__('%1$s %2$s by %3$s'), $ct->title, $ct->version, $ct->author) ; ?></h4>
<p class="theme-description"><?php echo $ct->description; ?></p>
<?php if ( ( !is_multisite() || is_super_admin() ) && $ct->parent_theme ) { ?>
	<p><?php printf(__('The template files are located in <code>%2$s</code>.  The stylesheet files are located in <code>%3$s</code>.  <strong>%4$s</strong> uses templates from <strong>%5$s</strong>.  Changes made to the templates will affect both themes.'), $ct->title, str_replace( WP_CONTENT_DIR, '', $ct->template_dir ), str_replace( WP_CONTENT_DIR, '', $ct->stylesheet_dir ), $ct->title, $ct->parent_theme); ?></p>
<?php } else { ?>
	<p><?php printf(__('All of this theme&#8217;s files are located in <code>%2$s</code>.'), $ct->title, str_replace( WP_CONTENT_DIR, '', $ct->template_dir ), str_replace( WP_CONTENT_DIR, '', $ct->stylesheet_dir ) ); ?></p>
<?php } ?>
<?php if ( $ct->tags ) : ?>
<p><?php _e('Tags:'); ?> <?php echo join(', ', $ct->tags); ?></p>
<?php endif; ?>
<?php theme_update_available($ct); ?>

</div>

<div class="clear"></div>
<h3><?php _e('Available Themes'); ?></h3>
<div class="clear"></div>

<?php if ( $theme_total ) { ?>

<?php if ( $page_links ) : ?>
<div class="tablenav">
<div class="tablenav-pages"><?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
	number_format_i18n( $start + 1 ),
	number_format_i18n( min( $page * $per_page, $theme_total ) ),
	number_format_i18n( $theme_total ),
	$page_links
); echo $page_links_text; ?></div>
</div>
<?php endif; ?>

<table id="availablethemes" cellspacing="0" cellpadding="0">
<?php
$style = '';

$theme_names = array_keys($themes);
natcasesort($theme_names);

$table = array();
$rows = ceil(count($theme_names) / 3);
for ( $row = 1; $row <= $rows; $row++ )
	for ( $col = 1; $col <= 3; $col++ )
		$table[$row][$col] = array_shift($theme_names);

foreach ( $table as $row => $cols ) {
?>
<tr>
<?php
foreach ( $cols as $col => $theme_name ) {
	$class = array('available-theme');
	if ( $row == 1 ) $class[] = 'top';
	if ( $col == 1 ) $class[] = 'left';
	if ( $row == $rows ) $class[] = 'bottom';
	if ( $col == 3 ) $class[] = 'right';
?>
	<td class="<?php echo join(' ', $class); ?>">
<?php if ( !empty($theme_name) ) :
	$template = $themes[$theme_name]['Template'];
	$stylesheet = $themes[$theme_name]['Stylesheet'];
	$title = $themes[$theme_name]['Title'];
	$version = $themes[$theme_name]['Version'];
	$description = $themes[$theme_name]['Description'];
	$author = $themes[$theme_name]['Author'];
	$screenshot = $themes[$theme_name]['Screenshot'];
	$stylesheet_dir = $themes[$theme_name]['Stylesheet Dir'];
	$template_dir = $themes[$theme_name]['Template Dir'];
	$parent_theme = $themes[$theme_name]['Parent Theme'];
	$theme_root = $themes[$theme_name]['Theme Root'];
	$theme_root_uri = $themes[$theme_name]['Theme Root URI'];
	$preview_link = esc_url(get_option('home') . '/');
	if ( is_ssl() )
		$preview_link = str_replace( 'http://', 'https://', $preview_link );
	$preview_link = htmlspecialchars( add_query_arg( array('preview' => 1, 'template' => $template, 'stylesheet' => $stylesheet, 'TB_iframe' => 'true' ), $preview_link ) );
	$preview_text = esc_attr( sprintf( __('Preview of &#8220;%s&#8221;'), $title ) );
	$tags = $themes[$theme_name]['Tags'];
	$thickbox_class = 'thickbox thickbox-preview';
	$activate_link = wp_nonce_url("themes.php?action=activate&amp;template=".urlencode($template)."&amp;stylesheet=".urlencode($stylesheet), 'switch-theme_' . $template);
	$activate_text = esc_attr( sprintf( __('Activate &#8220;%s&#8221;'), $title ) );
	$actions = array();
	$actions[] = '<a href="' . $activate_link .  '" class="activatelink" title="' . $activate_text . '">' . __('Activate') . '</a>';
	$actions[] = '<a href="' . $preview_link . '" class="thickbox thickbox-preview" title="' . esc_attr(sprintf(__('Preview &#8220;%s&#8221;'), $theme_name)) . '">' . __('Preview') . '</a>';
	if ( current_user_can('update_themes') )
		$actions[] = '<a class="submitdelete deletion" href="' . wp_nonce_url("themes.php?action=delete&amp;template=$stylesheet", 'delete-theme_' . $stylesheet) . '" onclick="' . "if ( confirm('" . esc_js(sprintf( __("You are about to delete this theme '%s'\n  'Cancel' to stop, 'OK' to delete."), $theme_name )) . "') ) {return true;}return false;" . '">' . __('Delete') . '</a>';
	$actions = apply_filters('theme_action_links', $actions, $themes[$theme_name]);

	$actions = implode ( ' | ', $actions );
?>
		<a href="<?php echo $preview_link; ?>" class="<?php echo $thickbox_class; ?> screenshot">
<?php if ( $screenshot ) : ?>
			<img src="<?php echo $theme_root_uri . '/' . $stylesheet . '/' . $screenshot; ?>" alt="" />
<?php endif; ?>
		</a>
<h3><?php
	/* translators: 1: theme title, 2: theme version, 3: theme author */
	printf(__('%1$s %2$s by %3$s'), $title, $version, $author) ; ?></h3>
<p class="description"><?php echo $description; ?></p>
<span class='action-links'><?php echo $actions ?></span>
	<?php if ( ( !is_multisite() || is_super_admin() ) && $parent_theme ) {
	/* translators: 1: theme title, 2:  template dir, 3: stylesheet_dir, 4: theme title, 5: parent_theme */ ?>
	<p><?php printf(__('The template files are located in <code>%2$s</code>.  The stylesheet files are located in <code>%3$s</code>.  <strong>%4$s</strong> uses templates from <strong>%5$s</strong>.  Changes made to the templates will affect both themes.'), $title, str_replace( WP_CONTENT_DIR, '', $template_dir ), str_replace( WP_CONTENT_DIR, '', $stylesheet_dir ), $title, $parent_theme); ?></p>
<?php } else { ?>
	<p><?php printf(__('All of this theme&#8217;s files are located in <code>%2$s</code>.'), $title, str_replace( WP_CONTENT_DIR, '', $template_dir ), str_replace( WP_CONTENT_DIR, '', $stylesheet_dir ) ); ?></p>
<?php } ?>
<?php if ( $tags ) : ?>
<p><?php _e('Tags:'); ?> <?php echo join(', ', $tags); ?></p>
<?php endif; ?>
		<?php theme_update_available( $themes[$theme_name] ); ?>
<?php endif; // end if not empty theme_name ?>
	</td>
<?php } // end foreach $cols ?>
</tr>
<?php } // end foreach $table ?>
</table>
<?php } else { ?>
<p><?php _e('You only have one theme installed at the moment so there is nothing to show you here.  Maybe you should download some more to try out.'); ?></p>
<?php } // end if $theme_total?>
<br class="clear" />

<?php if ( $page_links ) : ?>
<div class="tablenav">
<?php echo "<div class='tablenav-pages'>$page_links_text</div>"; ?>
<br class="clear" />
</div>
<?php endif; ?>

<br class="clear" />

<?php
// List broken themes, if any.
$broken_themes = get_broken_themes();
if ( ( !is_multisite() || is_super_admin() ) && count( $broken_themes ) ) {
?>

<h2><?php _e('Broken Themes'); ?> <?php if ( is_multisite() ) _e( '(Site admin only)' ); ?></h2>
<p><?php _e('The following themes are installed but incomplete.  Themes must have a stylesheet and a template.'); ?></p>

<table id="broken-themes">
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
</div>

<?php require('admin-footer.php'); ?>
