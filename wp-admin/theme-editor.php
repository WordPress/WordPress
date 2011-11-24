<?php
/**
 * Theme editor administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('./admin.php');

if ( is_multisite() && ! is_network_admin() ) {
	wp_redirect( network_admin_url( 'theme-editor.php' ) );
	exit();
}

if ( !current_user_can('edit_themes') )
	wp_die('<p>'.__('You do not have sufficient permissions to edit templates for this site.').'</p>');

$title = __("Edit Themes");
$parent_file = 'themes.php';

get_current_screen()->add_help_tab( array(
'id'		=> 'overview',
'title'		=> __('Overview'),
'content'	=> 
	'<p>' . __('You can use the Theme Editor to edit the individual CSS and PHP files which make up your theme.') . '</p>
	<p>' . __('Begin by choosing a theme to edit from the dropdown menu and clicking Select. A list then appears of all the template files. Clicking once on any file name causes the file to appear in the large Editor box.') . '</p>
	<p>' . __('For PHP files, you can use the Documentation dropdown to select from functions recognized in that file. Lookup takes you to a web page with reference material about that particular function.') . '</p>
	<p>' . __('After typing in your edits, click Update File.') . '</p>
	<p>' . __('<strong>Advice:</strong> think very carefully about your site crashing if you are live-editing the theme currently in use.') . '</p>
	<p>' . __('Upgrading to a newer version of the same theme will override changes made here. To avoid this, consider creating a <a href="http://codex.wordpress.org/Child_Themes" target="_blank">child theme</a> instead.') . '</p>' .
	( is_network_admin() ? '<p>' . __('Any edits to files from this screen will be reflected on all sites in the network.') . '</p>' : '' )
) );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Theme_Development" target="_blank">Documentation on Theme Development</a>') . '</p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Using_Themes" target="_blank">Documentation on Using Themes</a>') . '</p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Editing_Files" target="_blank">Documentation on Editing Files</a>') . '</p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Template_Tags" target="_blank">Documentation on Template Tags</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

wp_reset_vars(array('action', 'redirect', 'profile', 'error', 'warning', 'a', 'file', 'theme', 'dir'));

$themes = get_themes();

if (empty($theme)) {
	$theme = get_current_theme();
} else {
	$theme = stripslashes($theme);
}

if ( ! isset($themes[$theme]) )
	wp_die(__('The requested theme does not exist.'));

$allowed_files = array_merge( $themes[$theme]['Stylesheet Files'], $themes[$theme]['Template Files'] );

if ( empty( $file ) ) {
	if ( false !== array_search( $themes[$theme]['Stylesheet Dir'] . '/style.css', $allowed_files ) )
		$file = $themes[$theme]['Stylesheet Dir'] . '/style.css';
	else
		$file = $allowed_files[0];
} else {
	$file = stripslashes($file);
	if ( 'theme' == $dir ) {
		$file = dirname(dirname($themes[$theme]['Template Dir'])) . $file ;
	} else if ( 'style' == $dir) {
		$file = dirname(dirname($themes[$theme]['Stylesheet Dir'])) . $file ;
	}
}

validate_file_to_edit($file, $allowed_files);
$scrollto = isset($_REQUEST['scrollto']) ? (int) $_REQUEST['scrollto'] : 0;
$file_show = basename( $file );

switch($action) {

case 'update':

	check_admin_referer('edit-theme_' . $file . $theme);

	$newcontent = stripslashes($_POST['newcontent']);
	$theme = urlencode($theme);
	if (is_writeable($file)) {
		//is_writable() not always reliable, check return value. see comments @ http://uk.php.net/is_writable
		$f = fopen($file, 'w+');
		if ($f !== FALSE) {
			fwrite($f, $newcontent);
			fclose($f);
			$location = "theme-editor.php?file=$file&theme=$theme&a=te&scrollto=$scrollto";
		} else {
			$location = "theme-editor.php?file=$file&theme=$theme&scrollto=$scrollto";
		}
	} else {
		$location = "theme-editor.php?file=$file&theme=$theme&scrollto=$scrollto";
	}

	$location = wp_kses_no_null($location);
	$strip = array('%0d', '%0a', '%0D', '%0A');
	$location = _deep_replace($strip, $location);
	header("Location: $location");
	exit();

break;

default:

	require_once(ABSPATH . 'wp-admin/admin-header.php');

	update_recently_edited($file);

	if ( !is_file($file) )
		$error = 1;

	$content = '';
	if ( !$error && filesize($file) > 0 ) {
		$f = fopen($file, 'r');
		$content = fread($f, filesize($file));

		if ( '.php' == substr( $file, strrpos( $file, '.' ) ) ) {
			$functions = wp_doc_link_parse( $content );

			$docs_select = '<select name="docs-list" id="docs-list">';
			$docs_select .= '<option value="">' . esc_attr__( 'Function Name...' ) . '</option>';
			foreach ( $functions as $function ) {
				$docs_select .= '<option value="' . esc_attr( urlencode( $function ) ) . '">' . htmlspecialchars( $function ) . '()</option>';
			}
			$docs_select .= '</select>';
		}

		$content = esc_textarea( $content );
	}

	?>
<?php if (isset($_GET['a'])) : ?>
 <div id="message" class="updated"><p><?php _e('File edited successfully.') ?></p></div>
<?php endif;

$description = get_file_description($file);
$desc_header = ( $description != $file_show ) ? "$description <span>($file_show)</span>" : $file_show;

$is_child_theme = $themes[$theme]['Template'] != $themes[$theme]['Stylesheet'];
?>
<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?></h2>

<div class="fileedit-sub">
<div class="alignleft">
<h3><?php echo $themes[$theme]['Name'] . ': ' . $desc_header; ?></h3>
</div>
<div class="alignright">
	<form action="theme-editor.php" method="post">
		<strong><label for="theme"><?php _e('Select theme to edit:'); ?> </label></strong>
		<select name="theme" id="theme">
<?php
	foreach ($themes as $a_theme) {
	$theme_name = $a_theme['Name'];
	if ($theme_name == $theme) $selected = " selected='selected'";
	else $selected = '';
	$theme_name = esc_attr($theme_name);
	echo "\n\t<option value=\"$theme_name\" $selected>$theme_name</option>";
}
?>
		</select>
		<?php submit_button( __( 'Select' ), 'button', 'Submit', false ); ?>
	</form>
</div>
<br class="clear" />
</div>
	<div id="templateside">
<?php
if ($allowed_files) :
?>
	<h3><?php _e('Templates'); ?></h3>
	<?php if ( $is_child_theme ) : ?>
	<p class="howto"><?php printf( __( 'This child theme inherits templates from a parent theme, %s.' ), $themes[$theme]['Parent Theme'] ); ?></p>
	<?php endif; ?>
	<ul>
<?php
	$template_mapping = array();
	$template_dir = $themes[$theme]['Template Dir'];
	foreach ( $themes[$theme]['Template Files'] as $template_file ) {
		// Don't show parent templates.
		if ( $is_child_theme && strpos( $template_file, trailingslashit( $template_dir ) ) === 0 )
			continue;

		$description = trim( get_file_description($template_file) );
		$template_show = basename($template_file);
		$filedesc = ( $description != $template_file ) ? "$description<br /><span class='nonessential'>($template_show)</span>" : "$description";
		$filedesc = ( $template_file == $file ) ? "<span class='highlight'>$description<br /><span class='nonessential'>($template_show)</span></span>" : $filedesc;
		$template_mapping[ $description ] = array( _get_template_edit_filename($template_file, $template_dir), $filedesc );
	}
	ksort( $template_mapping );
	while ( list( $template_sorted_key, list( $template_file, $filedesc ) ) = each( $template_mapping ) ) :
	?>
		<li><a href="theme-editor.php?file=<?php echo urlencode( $template_file ) ?>&amp;theme=<?php echo urlencode( $theme ) ?>&amp;dir=theme"><?php echo $filedesc ?></a></li>
<?php endwhile; ?>
	</ul>
	<h3><?php /* translators: Theme stylesheets in theme editor */ _ex('Styles', 'Theme stylesheets in theme editor'); ?></h3>
	<ul>
<?php
	$template_mapping = array();
	$stylesheet_dir = $themes[$theme]['Stylesheet Dir'];
	foreach ( $themes[$theme]['Stylesheet Files'] as $style_file ) {
		// Don't show parent styles.
		if ( $is_child_theme && strpos( $style_file, trailingslashit( $template_dir ) ) === 0 )
			continue;

		$description = trim( get_file_description($style_file) );
		$style_show = basename($style_file);
		$filedesc = ( $description != $style_file ) ? "$description<br /><span class='nonessential'>($style_show)</span>" : "$description";
		$filedesc = ( $style_file == $file ) ? "<span class='highlight'>$description<br /><span class='nonessential'>($style_show)</span></span>" : $filedesc;
		$template_mapping[ $description ] = array( _get_template_edit_filename($style_file, $stylesheet_dir), $filedesc );
	}
	ksort( $template_mapping );
	while ( list( $template_sorted_key, list( $style_file, $filedesc ) ) = each( $template_mapping ) ) :
		?>
		<li><a href="theme-editor.php?file=<?php echo urlencode( $style_file ) ?>&amp;theme=<?php echo urlencode($theme) ?>&amp;dir=style"><?php echo $filedesc ?></a></li>
<?php endwhile; ?>
	</ul>
<?php endif; ?>
</div>
<?php if (!$error) { ?>
	<form name="template" id="template" action="theme-editor.php" method="post">
	<?php wp_nonce_field('edit-theme_' . $file . $theme) ?>
		 <div><textarea cols="70" rows="25" name="newcontent" id="newcontent" tabindex="1"><?php echo $content ?></textarea>
		 <input type="hidden" name="action" value="update" />
		 <input type="hidden" name="file" value="<?php echo esc_attr($file) ?>" />
		 <input type="hidden" name="theme" value="<?php echo esc_attr($theme) ?>" />
		 <input type="hidden" name="scrollto" id="scrollto" value="<?php echo $scrollto; ?>" />
		 </div>
	<?php if ( isset($functions ) && count($functions) ) { ?>
		<div id="documentation" class="hide-if-no-js">
		<label for="docs-list"><?php _e('Documentation:') ?></label>
		<?php echo $docs_select; ?>
		<input type="button" class="button" value=" <?php esc_attr_e( 'Lookup' ); ?> " onclick="if ( '' != jQuery('#docs-list').val() ) { window.open( 'http://api.wordpress.org/core/handbook/1.0/?function=' + escape( jQuery( '#docs-list' ).val() ) + '&amp;locale=<?php echo urlencode( get_locale() ) ?>&amp;version=<?php echo urlencode( $wp_version ) ?>&amp;redirect=true'); }" />
		</div>
	<?php } ?>

		<div>
		<?php if ( is_child_theme() && ! $is_child_theme && $themes[$theme]['Template'] == get_option('template') ) : ?>
			<p><?php if ( is_writeable( $file ) ) { ?><strong><?php _e( 'Caution:' ); ?></strong><?php } ?>
			<?php _e( 'This is a file in your current parent theme.' ); ?></p>
		<?php endif; ?>
<?php
	if ( is_writeable( $file ) ) :
		submit_button( __( 'Update File' ), 'primary', 'submit', true, array( 'tabindex' => '2' ) );
	else : ?>
<p><em><?php _e('You need to make this file writable before you can save your changes. See <a href="http://codex.wordpress.org/Changing_File_Permissions">the Codex</a> for more information.'); ?></em></p>
<?php endif; ?>
		</div>
	</form>
<?php
	} else {
		echo '<div class="error"><p>' . __('Oops, no such file exists! Double check the name and try again, merci.') . '</p></div>';
	}
?>
<br class="clear" />
</div>
<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
	$('#template').submit(function(){ $('#scrollto').val( $('#newcontent').scrollTop() ); });
	$('#newcontent').scrollTop( $('#scrollto').val() );
});
/* ]]> */
</script>
<?php
break;
}

include(ABSPATH . "wp-admin/admin-footer.php");
