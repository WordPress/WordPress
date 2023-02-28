<?php
/**
 * Edit plugin file editor administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( is_multisite() && ! is_network_admin() ) {
	wp_redirect( network_admin_url( 'plugin-editor.php' ) );
	exit;
}

if ( ! current_user_can( 'edit_plugins' ) ) {
	wp_die( __( 'Sorry, you are not allowed to edit plugins for this site.' ) );
}

// Used in the HTML title tag.
$title       = __( 'Edit Plugins' );
$parent_file = 'plugins.php';

$plugins = get_plugins();

if ( empty( $plugins ) ) {
	require_once ABSPATH . 'wp-admin/admin-header.php';
	?>
	<div class="wrap">
		<h1><?php echo esc_html( $title ); ?></h1>
		<div id="message" class="error"><p><?php _e( 'No plugins are currently available.' ); ?></p></div>
	</div>
	<?php
	require_once ABSPATH . 'wp-admin/admin-footer.php';
	exit;
}

$file   = '';
$plugin = '';
if ( isset( $_REQUEST['file'] ) ) {
	$file = wp_unslash( $_REQUEST['file'] );
}

if ( isset( $_REQUEST['plugin'] ) ) {
	$plugin = wp_unslash( $_REQUEST['plugin'] );
}

if ( empty( $plugin ) ) {
	if ( $file ) {

		// Locate the plugin for a given plugin file being edited.
		$file_dirname = dirname( $file );
		foreach ( array_keys( $plugins ) as $plugin_candidate ) {
			if ( $plugin_candidate === $file || ( '.' !== $file_dirname && dirname( $plugin_candidate ) === $file_dirname ) ) {
				$plugin = $plugin_candidate;
				break;
			}
		}

		// Fallback to the file as the plugin.
		if ( empty( $plugin ) ) {
			$plugin = $file;
		}
	} else {
		$plugin = array_keys( $plugins );
		$plugin = $plugin[0];
	}
}

$plugin_files = get_plugin_files( $plugin );

if ( empty( $file ) ) {
	$file = $plugin_files[0];
}

$file      = validate_file_to_edit( $file, $plugin_files );
$real_file = WP_PLUGIN_DIR . '/' . $file;

// Handle fallback editing of file when JavaScript is not available.
$edit_error     = null;
$posted_content = null;

if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
	$r = wp_edit_theme_plugin_file( wp_unslash( $_POST ) );
	if ( is_wp_error( $r ) ) {
		$edit_error = $r;
		if ( check_ajax_referer( 'edit-plugin_' . $file, 'nonce', false ) && isset( $_POST['newcontent'] ) ) {
			$posted_content = wp_unslash( $_POST['newcontent'] );
		}
	} else {
		wp_redirect(
			add_query_arg(
				array(
					'a'      => 1, // This means "success" for some reason.
					'plugin' => $plugin,
					'file'   => $file,
				),
				admin_url( 'plugin-editor.php' )
			)
		);
		exit;
	}
}

// List of allowable extensions.
$editable_extensions = wp_get_plugin_file_editable_extensions( $plugin );

if ( ! is_file( $real_file ) ) {
	wp_die( sprintf( '<p>%s</p>', __( 'File does not exist! Please double check the name and try again.' ) ) );
} else {
	// Get the extension of the file.
	if ( preg_match( '/\.([^.]+)$/', $real_file, $matches ) ) {
		$ext = strtolower( $matches[1] );
		// If extension is not in the acceptable list, skip it.
		if ( ! in_array( $ext, $editable_extensions, true ) ) {
			wp_die( sprintf( '<p>%s</p>', __( 'Files of this type are not editable.' ) ) );
		}
	}
}

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( 'Overview' ),
		'content' =>
				'<p>' . __( 'You can use the plugin file editor to make changes to any of your plugins&#8217; individual PHP files. Be aware that if you make changes, plugins updates will overwrite your customizations.' ) . '</p>' .
				'<p>' . __( 'Choose a plugin to edit from the dropdown menu and click the Select button. Click once on any file name to load it in the editor, and make your changes. Do not forget to save your changes (Update File) when you are finished.' ) . '</p>' .
				'<p>' . __( 'The documentation menu below the editor lists the PHP functions recognized in the plugin file. Clicking Look Up takes you to a web page about that particular function.' ) . '</p>' .
				'<p id="editor-keyboard-trap-help-1">' . __( 'When using a keyboard to navigate:' ) . '</p>' .
				'<ul>' .
				'<li id="editor-keyboard-trap-help-2">' . __( 'In the editing area, the Tab key enters a tab character.' ) . '</li>' .
				'<li id="editor-keyboard-trap-help-3">' . __( 'To move away from this area, press the Esc key followed by the Tab key.' ) . '</li>' .
				'<li id="editor-keyboard-trap-help-4">' . __( 'Screen reader users: when in forms mode, you may need to press the Esc key twice.' ) . '</li>' .
				'</ul>' .
				'<p>' . __( 'If you want to make changes but do not want them to be overwritten when the plugin is updated, you may be ready to think about writing your own plugin. For information on how to edit plugins, write your own from scratch, or just better understand their anatomy, check out the links below.' ) . '</p>' .
				( is_network_admin() ? '<p>' . __( 'Any edits to files from this screen will be reflected on all sites in the network.' ) . '</p>' : '' ),
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://wordpress.org/documentation/article/plugins-editor-screen/">Documentation on Editing Plugins</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://developer.wordpress.org/plugins/">Documentation on Writing Plugins</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/forums/">Support forums</a>' ) . '</p>'
);

$settings = array(
	'codeEditor' => wp_enqueue_code_editor( array( 'file' => $real_file ) ),
);
wp_enqueue_script( 'wp-theme-plugin-editor' );
wp_add_inline_script( 'wp-theme-plugin-editor', sprintf( 'jQuery( function( $ ) { wp.themePluginEditor.init( $( "#template" ), %s ); } )', wp_json_encode( $settings ) ) );
wp_add_inline_script( 'wp-theme-plugin-editor', sprintf( 'wp.themePluginEditor.themeOrPlugin = "plugin";' ) );

require_once ABSPATH . 'wp-admin/admin-header.php';

update_recently_edited( WP_PLUGIN_DIR . '/' . $file );

if ( ! empty( $posted_content ) ) {
	$content = $posted_content;
} else {
	$content = file_get_contents( $real_file );
}

if ( '.php' === substr( $real_file, strrpos( $real_file, '.' ) ) ) {
	$functions = wp_doc_link_parse( $content );

	if ( ! empty( $functions ) ) {
		$docs_select  = '<select name="docs-list" id="docs-list">';
		$docs_select .= '<option value="">' . esc_html__( 'Function Name&hellip;' ) . '</option>';

		foreach ( $functions as $function ) {
			$docs_select .= '<option value="' . esc_attr( $function ) . '">' . esc_html( $function ) . '()</option>';
		}

		$docs_select .= '</select>';
	}
}

$content = esc_textarea( $content );
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
<h2>
	<?php
	if ( is_plugin_active( $plugin ) ) {
		if ( is_writable( $real_file ) ) {
			/* translators: %s: Plugin file name. */
			printf( __( 'Editing %s (active)' ), '<strong>' . esc_html( $file ) . '</strong>' );
		} else {
			/* translators: %s: Plugin file name. */
			printf( __( 'Browsing %s (active)' ), '<strong>' . esc_html( $file ) . '</strong>' );
		}
	} else {
		if ( is_writable( $real_file ) ) {
			/* translators: %s: Plugin file name. */
			printf( __( 'Editing %s (inactive)' ), '<strong>' . esc_html( $file ) . '</strong>' );
		} else {
			/* translators: %s: Plugin file name. */
			printf( __( 'Browsing %s (inactive)' ), '<strong>' . esc_html( $file ) . '</strong>' );
		}
	}
	?>
</h2>
</div>
<div class="alignright">
	<form action="plugin-editor.php" method="get">
		<label for="plugin" id="theme-plugin-editor-selector"><?php _e( 'Select plugin to edit:' ); ?> </label>
		<select name="plugin" id="plugin">
		<?php
		foreach ( $plugins as $plugin_key => $a_plugin ) {
			$plugin_name = $a_plugin['Name'];
			if ( $plugin_key === $plugin ) {
				$selected = " selected='selected'";
			} else {
				$selected = '';
			}
			$plugin_name = esc_attr( $plugin_name );
			$plugin_key  = esc_attr( $plugin_key );
			echo "\n\t<option value=\"$plugin_key\" $selected>$plugin_name</option>";
		}
		?>
		</select>
		<?php submit_button( __( 'Select' ), '', 'Submit', false ); ?>
	</form>
</div>
<br class="clear" />
</div>

<div id="templateside">
	<h2 id="plugin-files-label"><?php _e( 'Plugin Files' ); ?></h2>

	<?php
	$plugin_editable_files = array();
	foreach ( $plugin_files as $plugin_file ) {
		if ( preg_match( '/\.([^.]+)$/', $plugin_file, $matches ) && in_array( $matches[1], $editable_extensions, true ) ) {
			$plugin_editable_files[] = $plugin_file;
		}
	}
	?>
	<ul role="tree" aria-labelledby="plugin-files-label">
	<li role="treeitem" tabindex="-1" aria-expanded="true" aria-level="1" aria-posinset="1" aria-setsize="1">
		<ul role="group">
			<?php wp_print_plugin_file_tree( wp_make_plugin_file_tree( $plugin_editable_files ) ); ?>
		</ul>
	</ul>
</div>

<form name="template" id="template" action="plugin-editor.php" method="post">
	<?php wp_nonce_field( 'edit-plugin_' . $file, 'nonce' ); ?>
	<div>
		<label for="newcontent" id="theme-plugin-editor-label"><?php _e( 'Selected file content:' ); ?></label>
		<textarea cols="70" rows="25" name="newcontent" id="newcontent" aria-describedby="editor-keyboard-trap-help-1 editor-keyboard-trap-help-2 editor-keyboard-trap-help-3 editor-keyboard-trap-help-4"><?php echo $content; ?></textarea>
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="file" value="<?php echo esc_attr( $file ); ?>" />
		<input type="hidden" name="plugin" value="<?php echo esc_attr( $plugin ); ?>" />
	</div>

	<?php if ( ! empty( $docs_select ) ) : ?>
		<div id="documentation" class="hide-if-no-js">
			<label for="docs-list"><?php _e( 'Documentation:' ); ?></label>
			<?php echo $docs_select; ?>
			<input disabled id="docs-lookup" type="button" class="button" value="<?php esc_attr_e( 'Look Up' ); ?>" onclick="if ( '' != jQuery('#docs-list').val() ) { window.open( 'https://api.wordpress.org/core/handbook/1.0/?function=' + escape( jQuery( '#docs-list' ).val() ) + '&amp;locale=<?php echo urlencode( get_user_locale() ); ?>&amp;version=<?php echo urlencode( get_bloginfo( 'version' ) ); ?>&amp;redirect=true'); }" />
		</div>
	<?php endif; ?>

	<?php if ( is_writable( $real_file ) ) : ?>
		<div class="editor-notices">
		<?php if ( in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) ) { ?>
			<div class="notice notice-warning inline active-plugin-edit-warning">
				<p><?php _e( '<strong>Warning:</strong> Making changes to active plugins is not recommended.' ); ?></p>
			</div>
		<?php } ?>
		</div>
		<p class="submit">
			<?php submit_button( __( 'Update File' ), 'primary', 'submit', false ); ?>
			<span class="spinner"></span>
		</p>
	<?php else : ?>
		<p>
			<?php
			printf(
				/* translators: %s: Documentation URL. */
				__( 'You need to make this file writable before you can save your changes. See <a href="%s">Changing File Permissions</a> for more information.' ),
				__( 'https://wordpress.org/documentation/article/changing-file-permissions/' )
			);
			?>
		</p>
	<?php endif; ?>

	<?php wp_print_file_editor_templates(); ?>
</form>
<br class="clear" />
</div>
<?php
$dismissed_pointers = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
if ( ! in_array( 'plugin_editor_notice', $dismissed_pointers, true ) ) :
	// Get a back URL.
	$referer = wp_get_referer();

	$excluded_referer_basenames = array( 'plugin-editor.php', 'wp-login.php' );

	$return_url = admin_url( '/' );
	if ( $referer ) {
		$referer_path = parse_url( $referer, PHP_URL_PATH );
		if ( is_string( $referer_path ) && ! in_array( basename( $referer_path ), $excluded_referer_basenames, true ) ) {
			$return_url = $referer;
		}
	}
	?>
	<div id="file-editor-warning" class="notification-dialog-wrap file-editor-warning hide-if-no-js hidden">
		<div class="notification-dialog-background"></div>
		<div class="notification-dialog">
			<div class="file-editor-warning-content">
				<div class="file-editor-warning-message">
					<h1><?php _e( 'Heads up!' ); ?></h1>
					<p><?php _e( 'You appear to be making direct edits to your plugin in the WordPress dashboard. Editing plugins directly is not recommended as it may introduce incompatibilities that break your site and your changes may be lost in future updates.' ); ?></p>
					<p><?php _e( 'If you absolutely have to make direct edits to this plugin, use a file manager to create a copy with a new name and hang on to the original. That way, you can re-enable a functional version if something goes wrong.' ); ?></p>
				</div>
				<p>
					<a class="button file-editor-warning-go-back" href="<?php echo esc_url( $return_url ); ?>"><?php _e( 'Go back' ); ?></a>
					<button type="button" class="file-editor-warning-dismiss button button-primary"><?php _e( 'I understand' ); ?></button>
				</p>
			</div>
		</div>
	</div>
	<?php
endif; // Editor warning notice.

require_once ABSPATH . 'wp-admin/admin-footer.php';
