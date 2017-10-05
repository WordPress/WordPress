<?php
/**
 * Theme editor administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

if ( is_multisite() && ! is_network_admin() ) {
	wp_redirect( network_admin_url( 'theme-editor.php' ) );
	exit();
}

if ( !current_user_can('edit_themes') )
	wp_die('<p>'.__('Sorry, you are not allowed to edit templates for this site.').'</p>');

$title = __("Edit Themes");
$parent_file = 'themes.php';

get_current_screen()->add_help_tab( array(
'id'		=> 'overview',
'title'		=> __('Overview'),
'content'	=>
	'<p>' . __( 'You can use the Theme Editor to edit the individual CSS and PHP files which make up your theme.' ) . '</p>' .
	'<p>' . __( 'Begin by choosing a theme to edit from the dropdown menu and clicking the Select button. A list then appears of the theme&#8217;s template files. Clicking once on any file name causes the file to appear in the large Editor box.' ) . '</p>' .
	'<p>' . __( 'For PHP files, you can use the Documentation dropdown to select from functions recognized in that file. Look Up takes you to a web page with reference material about that particular function.' ) . '</p>' .
	'<p id="editor-keyboard-trap-help-1">' . __( 'When using a keyboard to navigate:' ) . '</p>' .
	'<ul>' .
	'<li id="editor-keyboard-trap-help-2">' . __( 'In the editing area, the Tab key enters a tab character.' ) . '</li>' .
	'<li id="editor-keyboard-trap-help-3">' . __( 'To move away from this area, press the Esc key followed by the Tab key.' ) . '</li>' .
	'<li id="editor-keyboard-trap-help-4">' . __( 'Screen reader users: when in forms mode, you may need to press the Esc key twice.' ) . '</li>' .
	'</ul>' .
	'<p>' . __( 'After typing in your edits, click Update File.' ) . '</p>' .
	'<p>' . __( '<strong>Advice:</strong> think very carefully about your site crashing if you are live-editing the theme currently in use.' ) . '</p>' .
	/* translators: %s: link to codex article about child themes */
	'<p>' . sprintf( __( 'Upgrading to a newer version of the same theme will override changes made here. To avoid this, consider creating a <a href="%s">child theme</a> instead.' ), __( 'https://codex.wordpress.org/Child_Themes' ) ) . '</p>' .
	( is_network_admin() ? '<p>' . __( 'Any edits to files from this screen will be reflected on all sites in the network.' ) . '</p>' : '' ),
) );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="https://codex.wordpress.org/Theme_Development">Documentation on Theme Development</a>') . '</p>' .
	'<p>' . __('<a href="https://codex.wordpress.org/Using_Themes">Documentation on Using Themes</a>') . '</p>' .
	'<p>' . __('<a href="https://codex.wordpress.org/Editing_Files">Documentation on Editing Files</a>') . '</p>' .
	'<p>' . __('<a href="https://codex.wordpress.org/Template_Tags">Documentation on Template Tags</a>') . '</p>' .
	'<p>' . __('<a href="https://wordpress.org/support/">Support Forums</a>') . '</p>'
);

wp_reset_vars( array( 'action', 'error', 'file', 'theme' ) );

if ( $theme ) {
	$stylesheet = $theme;
} else {
	$stylesheet = get_stylesheet();
}

$theme = wp_get_theme( $stylesheet );

if ( ! $theme->exists() ) {
	wp_die( __( 'The requested theme does not exist.' ) );
}

if ( $theme->errors() && 'theme_no_stylesheet' == $theme->errors()->get_error_code() ) {
	wp_die( __( 'The requested theme does not exist.' ) . ' ' . $theme->errors()->get_error_message() );
}

$allowed_files = $style_files = array();
$has_templates = false;

$file_types = wp_get_theme_file_editable_extensions( $theme );

foreach ( $file_types as $type ) {
	switch ( $type ) {
		case 'php':
			$allowed_files += $theme->get_files( 'php', 1 );
			$has_templates = ! empty( $allowed_files );
			break;
		case 'css':
			$style_files = $theme->get_files( 'css' );
			$allowed_files['style.css'] = $style_files['style.css'];
			$allowed_files += $style_files;
			break;
		default:
			$allowed_files += $theme->get_files( $type );
			break;
	}
}

if ( empty( $file ) ) {
	$relative_file = 'style.css';
	$file = $allowed_files['style.css'];
} else {
	$relative_file = wp_unslash( $file );
	$file = $theme->get_stylesheet_directory() . '/' . $relative_file;
}

validate_file_to_edit( $file, $allowed_files );

// Handle fallback editing of file when JavaScript is not available.
$edit_error = null;
$posted_content = null;
if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
	$r = wp_edit_theme_plugin_file( wp_unslash( $_POST ) );
	if ( is_wp_error( $r ) ) {
		$edit_error = $r;
		if ( check_ajax_referer( 'edit-theme_' . $file . $stylesheet, 'nonce', false ) && isset( $_POST['newcontent'] ) ) {
			$posted_content = wp_unslash( $_POST['newcontent'] );
		}
	} else {
		wp_redirect( add_query_arg(
			array(
				'a' => 1, // This means "success" for some reason.
				'theme' => $stylesheet,
				'file' => $relative_file,
			),
			admin_url( 'theme-editor.php' )
		) );
		exit;
	}
}

	$settings = array(
		'codeEditor' => wp_enqueue_code_editor( compact( 'file' ) ),
	);
	wp_enqueue_script( 'wp-theme-plugin-editor' );
	wp_add_inline_script( 'wp-theme-plugin-editor', sprintf( 'jQuery( function( $ ) { wp.themePluginEditor.init( $( "#template" ), %s ); } )', wp_json_encode( $settings ) ) );
	wp_add_inline_script( 'wp-theme-plugin-editor', 'wp.themePluginEditor.themeOrPlugin = "theme";' );

	require_once( ABSPATH . 'wp-admin/admin-header.php' );

	update_recently_edited( $file );

	if ( ! is_file( $file ) )
		$error = true;

	$content = '';
	if ( ! empty( $posted_content ) ) {
		$content = $posted_content;
	} elseif ( ! $error && filesize( $file ) > 0 ) {
		$f = fopen($file, 'r');
		$content = fread($f, filesize($file));

		if ( '.php' == substr( $file, strrpos( $file, '.' ) ) ) {
			$functions = wp_doc_link_parse( $content );

			$docs_select = '<select name="docs-list" id="docs-list">';
			$docs_select .= '<option value="">' . esc_attr__( 'Function Name&hellip;' ) . '</option>';
			foreach ( $functions as $function ) {
				$docs_select .= '<option value="' . esc_attr( urlencode( $function ) ) . '">' . htmlspecialchars( $function ) . '()</option>';
			}
			$docs_select .= '</select>';
		}

		$content = esc_textarea( $content );
	}

$file_description = get_file_description( $relative_file );
$file_show = array_search( $file, array_filter( $allowed_files ) );
$description = esc_html( $file_description );
if ( $file_description != $file_show ) {
	$description .= ' <span>(' . esc_html( $file_show ) . ')</span>';
}
?>
<div class="wrap">
<h1><?php echo esc_html( $title ); ?></h1>

<?php if ( isset( $_GET['a'] ) ) : ?>
	<div id="message" class="updated notice is-dismissible">
		<p><?php _e( 'File edited successfully.' ); ?></p>
	</div>
<?php elseif ( is_wp_error( $edit_error ) ) : ?>
	<div id="message" class="notice notice-error">
		<p><?php _e( 'There was an error while trying to update the file. You may need to fix something and try updating again.' ); ?></p>
		<pre><?php echo esc_html( $edit_error->get_error_message() ? $edit_error->get_error_message() : $edit_error->get_error_code() ); ?></pre>
	</div>
<?php endif; ?>

<div class="fileedit-sub">
<div class="alignleft">
<h2><?php echo $theme->display( 'Name' ); if ( $description ) echo ': ' . $description; ?></h2>
</div>
<div class="alignright">
	<form action="theme-editor.php" method="get">
		<strong><label for="theme"><?php _e('Select theme to edit:'); ?> </label></strong>
		<select name="theme" id="theme">
<?php
foreach ( wp_get_themes( array( 'errors' => null ) ) as $a_stylesheet => $a_theme ) {
	if ( $a_theme->errors() && 'theme_no_stylesheet' == $a_theme->errors()->get_error_code() )
		continue;

	$selected = $a_stylesheet == $stylesheet ? ' selected="selected"' : '';
	echo "\n\t" . '<option value="' . esc_attr( $a_stylesheet ) . '"' . $selected . '>' . $a_theme->display('Name') . '</option>';
}
?>
		</select>
		<?php submit_button( __( 'Select' ), '', 'Submit', false ); ?>
	</form>
</div>
<br class="clear" />
</div>
<?php
if ( $theme->errors() )
	echo '<div class="error"><p><strong>' . __( 'This theme is broken.' ) . '</strong> ' . $theme->errors()->get_error_message() . '</p></div>';
?>
	<div id="templateside">
<?php
if ( $allowed_files ) :
	$previous_file_type = '';

	foreach ( $allowed_files as $filename => $absolute_filename ) :
		$file_type = substr( $filename, strrpos( $filename, '.' ) );

		if ( $file_type !== $previous_file_type ) {
			if ( '' !== $previous_file_type ) {
				echo "\t</ul>\n";
			}

			switch ( $file_type ) {
				case '.php':
					if ( $has_templates || $theme->parent() ) :
						echo "\t<h2>" . __( 'Templates' ) . "</h2>\n";
						if ( $theme->parent() ) {
							echo '<p class="howto">' . sprintf( __( 'This child theme inherits templates from a parent theme, %s.' ),
								sprintf( '<a href="%s">%s</a>',
									self_admin_url( 'theme-editor.php?theme=' . urlencode( $theme->get_template() ) ),
									$theme->parent()->display( 'Name' )
								)
							) . "</p>\n";
						}
					endif;
					break;
				case '.css':
					echo "\t<h2>" . _x( 'Styles', 'Theme stylesheets in theme editor' ) . "</h2>\n";
					break;
				default:
					/* translators: %s: file extension */
					echo "\t<h2>" . sprintf( __( '%s files' ), $file_type ) . "</h2>\n";
					break;
			}

			echo "\t<ul>\n";
		}

		$file_description = esc_html( get_file_description( $filename ) );
		if ( $filename !== basename( $absolute_filename ) || $file_description !== $filename ) {
			$file_description .= '<br /><span class="nonessential">(' . esc_html( $filename ) . ')</span>';
		}

		if ( $absolute_filename === $file ) {
			$file_description = '<span class="notice notice-info">' . $file_description . '</span>';
		}

		$previous_file_type = $file_type;
?>
		<li><a href="theme-editor.php?file=<?php echo urlencode( $filename ) ?>&amp;theme=<?php echo urlencode( $stylesheet ) ?>"><?php echo $file_description; ?></a></li>
<?php
	endforeach;
?>
</ul>
<?php endif; ?>
</div>
<?php if ( $error ) :
	echo '<div class="error"><p>' . __('Oops, no such file exists! Double check the name and try again, merci.') . '</p></div>';
else : ?>
	<form name="template" id="template" action="theme-editor.php" method="post">
		<?php wp_nonce_field( 'edit-theme_' . $file . $stylesheet, 'nonce' ); ?>
		<div>
			<label for="newcontent" id="theme-plugin-editor-label"><?php _e( 'Selected file content:' ); ?></label>
			<textarea cols="70" rows="30" name="newcontent" id="newcontent" aria-describedby="editor-keyboard-trap-help-1 editor-keyboard-trap-help-2 editor-keyboard-trap-help-3 editor-keyboard-trap-help-4"><?php echo $content; ?></textarea>
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="file" value="<?php echo esc_attr( $relative_file ); ?>" />
			<input type="hidden" name="theme" value="<?php echo esc_attr( $theme->get_stylesheet() ); ?>" />
		</div>
	<?php if ( ! empty( $functions ) ) : ?>
		<div id="documentation" class="hide-if-no-js">
		<label for="docs-list"><?php _e('Documentation:') ?></label>
		<?php echo $docs_select; ?>
		<input type="button" class="button" value="<?php esc_attr_e( 'Look Up' ); ?>" onclick="if ( '' != jQuery('#docs-list').val() ) { window.open( 'https://api.wordpress.org/core/handbook/1.0/?function=' + escape( jQuery( '#docs-list' ).val() ) + '&amp;locale=<?php echo urlencode( get_user_locale() ) ?>&amp;version=<?php echo urlencode( get_bloginfo( 'version' ) ) ?>&amp;redirect=true'); }" />
		</div>
	<?php endif; ?>

	<div>
		<div class="editor-notices">
			<?php if ( is_child_theme() && $theme->get_stylesheet() == get_template() ) : ?>
				<div class="notice notice-warning inline">
					<p>
						<?php if ( is_writeable( $file ) ) { ?><strong><?php _e( 'Caution:' ); ?></strong><?php } ?>
						<?php _e( 'This is a file in your current parent theme.' ); ?>
					</p>
				</div>
			<?php endif; ?>
		</div>
	<?php if ( is_writeable( $file ) ) : ?>
		<p class="submit">
			<?php submit_button( __( 'Update File' ), 'primary', 'submit', false ); ?>
			<span class="spinner"></span>
		</p>
	<?php else : ?>
		<p><em><?php _e('You need to make this file writable before you can save your changes. See <a href="https://codex.wordpress.org/Changing_File_Permissions">the Codex</a> for more information.'); ?></em></p>
	<?php endif; ?>
	</div>
	<?php wp_print_file_editor_templates(); ?>
	</form>
<?php
endif; // $error
?>
<br class="clear" />
</div>
<?php
$dismissed_pointers = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
if ( ! in_array( 'theme_editor_notice', $dismissed_pointers, true ) ) :
?>
<div id="file-editor-warning" class="notification-dialog-wrap file-editor-warning hide-if-no-js">
	<div class="notification-dialog-background"></div>
	<div class="notification-dialog" role="dialog" aria-labelledby="file-editor-warning-title" tabindex="0">
		<div class="file-editor-warning-content">
			<h1 id="file-editor-warning-title"><?php _e( 'Heads up!' ); ?></h1>
			<p>
				<?php
				echo sprintf(
					/* translators: %s is a link to Custom CSS section in the Customizer. */
					__( 'You appear to be making direct edits to your theme in the WordPress Dashboard. We recommend that you don&#8217;t! Editing this code directly is dangerous, and can leave you unable to log back in to WordPress and undo changes. There&#8217;s no need to change your CSS here &mdash; you can edit and live preview CSS changes in WordPress&#8217;s <a href="%s">built in CSS editor</a>.' ),
					esc_url( add_query_arg( 'autofocus[section]', 'custom_css', admin_url( 'customize.php' ) ) )
				);
				?>
			</p>
			<p><?php _e( 'If you decide to go ahead with direct edits anyway, make sure to back up all your site&#8217;s files before making changes so you can restore a functional version if something goes wrong.' ); ?></p>
			<p><button type="button" class="file-editor-warning-dismiss button-primary"><?php _e( 'I understand' ); ?></button></p>
		</div>
	</div>
</div>
<?php
endif; // editor warning notice

include(ABSPATH . 'wp-admin/admin-footer.php' );
