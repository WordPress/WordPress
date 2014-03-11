<?php
/**
 * Facilitates adding of the WordPress editor as used on the Write and Edit screens.
 *
 * @package WordPress
 * @since 3.3.0
 *
 * Private, not included by default. See wp_editor() in wp-includes/general-template.php.
 */

final class _WP_Editors {
	public static $mce_locale;

	private static $mce_settings = array();
	private static $qt_settings = array();
	private static $plugins = array();
	private static $qt_buttons = array();
	private static $ext_plugins;
	private static $baseurl;
	private static $first_init;
	private static $this_tinymce = false;
	private static $this_quicktags = false;
	private static $has_tinymce = false;
	private static $has_quicktags = false;
	private static $has_medialib = false;
	private static $editor_buttons_css = true;

	private function __construct() {}

	public static function parse_settings($editor_id, $settings) {
		$set = wp_parse_args( $settings,  array(
			'wpautop' => true, // use wpautop?
			'media_buttons' => true, // show insert/upload button(s)
			'default_editor' => '', // When both TinyMCE and Quicktags are used, set which editor is shown on loading the page
			'textarea_name' => $editor_id, // set the textarea name to something different, square brackets [] can be used here
			'textarea_rows' => 20,
			'tabindex' => '',
			'tabfocus_elements' => ':prev,:next', // the previous and next element ID to move the focus to when pressing the Tab key in TinyMCE
			'editor_css' => '', // intended for extra styles for both visual and Text editors buttons, needs to include the <style> tags, can use "scoped".
			'editor_class' => '', // add extra class(es) to the editor textarea
			'teeny' => false, // output the minimal editor config used in Press This
			'dfw' => false, // replace the default fullscreen with DFW (needs specific DOM elements and css)
			'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
			'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
		) );

		self::$this_tinymce = ( $set['tinymce'] && user_can_richedit() );
		self::$this_quicktags = (bool) $set['quicktags'];

		if ( self::$this_tinymce )
			self::$has_tinymce = true;

		if ( self::$this_quicktags )
			self::$has_quicktags = true;

		if ( empty( $set['editor_height'] ) )
			return $set;

		if ( 'content' === $editor_id ) {
			// A cookie (set when a user resizes the editor) overrides the height.
			$cookie = (int) get_user_setting( 'ed_size' );

			// Upgrade an old TinyMCE cookie if it is still around, and the new one isn't.
			if ( ! $cookie && isset( $_COOKIE['TinyMCE_content_size'] ) ) {
				parse_str( $_COOKIE['TinyMCE_content_size'], $cookie );
 				$cookie = $cookie['ch'];
			}

			if ( $cookie )
				$set['editor_height'] = $cookie;
		}

		if ( $set['editor_height'] < 50 )
			$set['editor_height'] = 50;
		elseif ( $set['editor_height'] > 5000 )
			$set['editor_height'] = 5000;

		return $set;
	}

	/**
	 * Outputs the HTML for a single instance of the editor.
	 *
	 * @param string $content The initial content of the editor.
	 * @param string $editor_id ID for the textarea and TinyMCE and Quicktags instances (can contain only ASCII letters and numbers).
	 * @param array $settings See the _parse_settings() method for description.
	 */
	public static function editor( $content, $editor_id, $settings = array() ) {

		$set = self::parse_settings( $editor_id, $settings );
		$editor_class = ' class="' . trim( $set['editor_class'] . ' wp-editor-area' ) . '"';
		$tabindex = $set['tabindex'] ? ' tabindex="' . (int) $set['tabindex'] . '"' : '';
		$switch_class = 'html-active';
		$toolbar = $buttons = $autocomplete = '';

		if ( ! empty( $set['editor_height'] ) )
			$height = ' style="height: ' . $set['editor_height'] . 'px"';
		else
			$height = ' rows="' . $set['textarea_rows'] . '"';

		if ( !current_user_can( 'upload_files' ) )
			$set['media_buttons'] = false;

		if ( ! self::$this_quicktags && self::$this_tinymce ) {
			$switch_class = 'tmce-active';
			$autocomplete = ' autocomplete="off"';
		} elseif ( self::$this_quicktags && self::$this_tinymce ) {
			$default_editor = $set['default_editor'] ? $set['default_editor'] : wp_default_editor();
			$autocomplete = ' autocomplete="off"';

			// 'html' is used for the "Text" editor tab.
			if ( 'html' === $default_editor ) {
				add_filter('the_editor_content', 'wp_htmledit_pre');
				$switch_class = 'html-active';
			} else {
				add_filter('the_editor_content', 'wp_richedit_pre');
				$switch_class = 'tmce-active';
			}

			$buttons .= '<a id="' . $editor_id . '-html" class="wp-switch-editor switch-html" onclick="switchEditors.switchto(this);">' . _x( 'Text', 'Name for the Text editor tab (formerly HTML)' ) . "</a>\n";
			$buttons .= '<a id="' . $editor_id . '-tmce" class="wp-switch-editor switch-tmce" onclick="switchEditors.switchto(this);">' . __('Visual') . "</a>\n";
		}

		echo '<div id="wp-' . $editor_id . '-wrap" class="wp-core-ui wp-editor-wrap ' . $switch_class . '">';

		if ( self::$editor_buttons_css ) {
			wp_print_styles('editor-buttons');
			self::$editor_buttons_css = false;
		}

		if ( !empty($set['editor_css']) )
			echo $set['editor_css'] . "\n";

		if ( !empty($buttons) || $set['media_buttons'] ) {
			echo '<div id="wp-' . $editor_id . '-editor-tools" class="wp-editor-tools hide-if-no-js">';

			if ( $set['media_buttons'] ) {
				self::$has_medialib = true;

				if ( !function_exists('media_buttons') )
					include(ABSPATH . 'wp-admin/includes/media.php');

				echo '<div id="wp-' . $editor_id . '-media-buttons" class="wp-media-buttons">';
				do_action('media_buttons', $editor_id);
				echo "</div>\n";
			}

			echo '<div class="wp-editor-tabs">' . $buttons . "</div>\n";
			echo "</div>\n";
		}

		$the_editor = apply_filters( 'the_editor', '<div id="wp-' . $editor_id . '-editor-container" class="wp-editor-container">' .
			'<textarea' . $editor_class . $height . $tabindex . $autocomplete . ' cols="40" name="' . $set['textarea_name'] . '" ' .
			'id="' . $editor_id . '">%s</textarea></div>' );
		$content = apply_filters( 'the_editor_content', $content );

		printf( $the_editor, $content );
		echo "\n</div>\n\n";

		self::editor_settings($editor_id, $set);
	}

	public static function editor_settings($editor_id, $set) {
		$first_run = false;

		if ( empty(self::$first_init) ) {
			if ( is_admin() ) {
				add_action( 'admin_print_footer_scripts', array( __CLASS__, 'editor_js'), 50 );
				add_action( 'admin_footer', array( __CLASS__, 'enqueue_scripts'), 1 );
			} else {
				add_action( 'wp_print_footer_scripts', array( __CLASS__, 'editor_js'), 50 );
				add_action( 'wp_footer', array( __CLASS__, 'enqueue_scripts'), 1 );
			}
		}

		if ( self::$this_quicktags ) {

			$qtInit = array(
				'id' => $editor_id,
				'buttons' => ''
			);

			if ( is_array($set['quicktags']) )
				$qtInit = array_merge($qtInit, $set['quicktags']);

			if ( empty($qtInit['buttons']) )
				$qtInit['buttons'] = 'strong,em,link,block,del,ins,img,ul,ol,li,code,more,close';

			if ( $set['dfw'] )
				$qtInit['buttons'] .= ',fullscreen';

			$qtInit = apply_filters('quicktags_settings', $qtInit, $editor_id);
			self::$qt_settings[$editor_id] = $qtInit;

			self::$qt_buttons = array_merge( self::$qt_buttons, explode(',', $qtInit['buttons']) );
		}

		if ( self::$this_tinymce ) {

			if ( empty( self::$first_init ) ) {
				self::$baseurl = includes_url( 'js/tinymce' );
				$mce_locale = get_locale();

				if ( empty( $mce_locale ) || 'en' == substr( $mce_locale, 0, 2 ) ) {
					$mce_locale = 'en';
				}

				self::$mce_locale = $mce_locale;
				$no_captions = (bool) apply_filters( 'disable_captions', '' );
				$first_run = true;
				$ext_plugins = '';

				if ( $set['teeny'] ) {
					self::$plugins = $plugins = apply_filters( 'teeny_mce_plugins', array( 'fullscreen', 'image', 'wordpress', 'wpeditimage', 'wplink' ), $editor_id );
				} else {
					/**
					 * TinyMCE external plugins filter
					 *
					 * Takes an associative array of external plugins for TinyMCE in the form 'plugin_name' => 'url'.
					 * The url should be absolute and should include the js file name to be loaded.
					 * Example: 'myplugin' => 'http://my-site.com/wp-content/plugins/myfolder/mce_plugin.js'.
					 * If the plugin adds a button, it should be added with one of the "$mce_buttons" filters.
					 */
					$mce_external_plugins = apply_filters( 'mce_external_plugins', array() );

					/**
					 * TinyMCE default plugins filter
					 *
					 * Specifies which of the default plugins that are included in WordPress should be added to
					 * the TinyMCE instance.
					 */
					$plugins = array_unique( apply_filters( 'tiny_mce_plugins', array(
						'charmap',
						'hr',
						'media',
						'paste',
						'tabfocus',
						'textcolor',
						'fullscreen',
						'wordpress',
						'wpeditimage',
						'wpgallery',
						'wplink',
						'wpdialogs',
						'wpview',
					) ) );

					if ( ( $key = array_search( 'spellchecker', $plugins ) ) !== false ) {
						// Remove 'spellchecker' from the internal plugins if added with 'tiny_mce_plugins' filter to prevent errors.
						// It can be added with 'mce_external_plugins'.
						unset( $plugins[$key] );
					}

					if ( ! empty( $mce_external_plugins ) ) {
						/**
						 * This filter loads translations for external TinyMCE 3.x plugins.
						 *
						 * Takes an associative array 'plugin_name' => 'path', where path is the
						 * include path to the file. The language file should follow the same format as
						 * wp_mce_translation() and should define a variable $strings that
						 * holds all translated strings.
						 */
						$mce_external_languages = apply_filters( 'mce_external_languages', array() );

						$loaded_langs = array();
						$strings = '';

						if ( ! empty( $mce_external_languages ) ) {
							foreach ( $mce_external_languages as $name => $path ) {
								if ( @is_file( $path ) && @is_readable( $path ) ) {
									include_once( $path );
									$ext_plugins .= $strings . "\n";
									$loaded_langs[] = $name;
								}
							}
						}

						foreach ( $mce_external_plugins as $name => $url ) {
							if ( in_array( $name, $plugins, true ) ) {
								unset( $mce_external_plugins[ $name ] );
								continue;
							}

							$url = set_url_scheme( $url );
							$mce_external_plugins[ $name ] = $url;
							$plugurl = dirname( $url );

							if ( in_array( $name, $loaded_langs ) ) {
								$ext_plugins .= 'tinyMCEPreInit.load_ext("' . $plugurl . '", "' . $mce_locale . '");' . "\n";
							}
						}
					}
				}

				if ( $set['dfw'] )
					$plugins[] = 'wpfullscreen';

				self::$plugins = $plugins;
				self::$ext_plugins = $ext_plugins;

				self::$first_init = array(
					'theme' => 'modern',
					'skin' => 'lightgray',
					'language' => self::$mce_locale,
					'resize' => 'vertical',
					'formats' => "{
						alignleft: [
							{selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles: {textAlign:'left'}},
							{selector: 'img,table,dl.wp-caption', classes: 'alignleft'}
						],
						aligncenter: [
							{selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles: {textAlign:'center'}},
							{selector: 'img,table,dl.wp-caption', classes: 'aligncenter'}
						],
						alignright: [
							{selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles: {textAlign:'right'}},
							{selector: 'img,table,dl.wp-caption', classes: 'alignright'}
						],
						strikethrough: {inline: 'del'}
					}",
					'relative_urls' => false,
					'remove_script_host' => false,
					'convert_urls' => false,
					'browser_spellcheck' => true,
					'fix_list_elements' => true,
					'entities' => '38,amp,60,lt,62,gt',
					'entity_encoding' => 'raw',
					'menubar' => false,
					'keep_styles' => false,
					'paste_remove_styles' => true,

					// Limit the preview styles in the menu/toolbar
					'preview_styles' => 'font-family font-size font-weight font-style text-decoration text-transform',

					'wpeditimage_disable_captions' => $no_captions,
					'plugins' => implode( ',', $plugins ),
				);

				if ( ! empty( $mce_external_plugins ) ) {
					self::$first_init['external_plugins'] = json_encode( $mce_external_plugins );
				}

				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				$version = 'ver=' . $GLOBALS['wp_version'];
				$dashicons = includes_url( "css/dashicons$suffix.css?$version" );

				// WordPress default stylesheet and dashicons
				$mce_css = array( $dashicons, self::$baseurl . '/skins/wordpress/wp-content.css' );

				// load editor_style.css if the current theme supports it
				if ( ! empty( $GLOBALS['editor_styles'] ) && is_array( $GLOBALS['editor_styles'] ) ) {
					$editor_styles = $GLOBALS['editor_styles'];

					$editor_styles = array_unique( array_filter( $editor_styles ) );
					$style_uri = get_stylesheet_directory_uri();
					$style_dir = get_stylesheet_directory();

					// Support externally referenced styles (like, say, fonts).
					foreach ( $editor_styles as $key => $file ) {
						if ( preg_match( '~^(https?:)?//~', $file ) ) {
							$mce_css[] = esc_url_raw( $file );
							unset( $editor_styles[ $key ] );
						}
					}

					// Look in a parent theme first, that way child theme CSS overrides.
					if ( is_child_theme() ) {
						$template_uri = get_template_directory_uri();
						$template_dir = get_template_directory();

						foreach ( $editor_styles as $key => $file ) {
							if ( $file && file_exists( "$template_dir/$file" ) )
								$mce_css[] = "$template_uri/$file";
						}
					}

					foreach ( $editor_styles as $file ) {
						if ( $file && file_exists( "$style_dir/$file" ) )
							$mce_css[] = "$style_uri/$file";
					}
				}

				$mce_css = trim( apply_filters( 'mce_css', implode( ',', $mce_css ) ), ' ,' );

				if ( ! empty($mce_css) )
					self::$first_init['content_css'] = $mce_css;
			}

			if ( $set['teeny'] ) {
				$mce_buttons = apply_filters( 'teeny_mce_buttons', array('bold', 'italic', 'underline', 'blockquote', 'strikethrough', 'bullist', 'numlist', 'alignleft', 'aligncenter', 'alignright', 'undo', 'redo', 'link', 'unlink', 'fullscreen'), $editor_id );
				$mce_buttons_2 = $mce_buttons_3 = $mce_buttons_4 = array();
			} else {
				$mce_buttons = apply_filters('mce_buttons', array('bold', 'italic', 'strikethrough', 'bullist', 'numlist', 'blockquote', 'hr', 'alignleft', 'aligncenter', 'alignright', 'link', 'unlink', 'wp_more', 'spellchecker', 'fullscreen', 'wp_adv' ), $editor_id);
				$mce_buttons_2 = apply_filters('mce_buttons_2', array( 'formatselect', 'underline', 'alignjustify', 'forecolor', 'pastetext', 'removeformat', 'charmap', 'outdent', 'indent', 'undo', 'redo', 'wp_help' ), $editor_id);
				$mce_buttons_3 = apply_filters('mce_buttons_3', array(), $editor_id);
				$mce_buttons_4 = apply_filters('mce_buttons_4', array(), $editor_id);
			}

			$body_class = $editor_id;

			if ( $post = get_post() ) {
				$body_class .= ' post-type-' . sanitize_html_class( $post->post_type ) . ' post-status-' . sanitize_html_class( $post->post_status );
				if ( post_type_supports( $post->post_type, 'post-formats' ) ) {
					$post_format = get_post_format( $post );
					if ( $post_format && ! is_wp_error( $post_format ) )
						$body_class .= ' post-format-' . sanitize_html_class( $post_format );
					else
						$body_class .= ' post-format-standard';
				}
			}

			if ( !empty($set['tinymce']['body_class']) ) {
				$body_class .= ' ' . $set['tinymce']['body_class'];
				unset($set['tinymce']['body_class']);
			}

			if ( $set['dfw'] ) {
				// replace the first 'fullscreen' with 'wp_fullscreen'
				if ( ($key = array_search('fullscreen', $mce_buttons)) !== false )
					$mce_buttons[$key] = 'wp_fullscreen';
				elseif ( ($key = array_search('fullscreen', $mce_buttons_2)) !== false )
					$mce_buttons_2[$key] = 'wp_fullscreen';
				elseif ( ($key = array_search('fullscreen', $mce_buttons_3)) !== false )
					$mce_buttons_3[$key] = 'wp_fullscreen';
				elseif ( ($key = array_search('fullscreen', $mce_buttons_4)) !== false )
					$mce_buttons_4[$key] = 'wp_fullscreen';
			}

			$mceInit = array (
				'selector' => "#$editor_id",
				'wpautop' => (bool) $set['wpautop'],
				'indent' => ! $set['wpautop'],
				'toolbar1' => implode($mce_buttons, ','),
				'toolbar2' => implode($mce_buttons_2, ','),
				'toolbar3' => implode($mce_buttons_3, ','),
				'toolbar4' => implode($mce_buttons_4, ','),
				'tabfocus_elements' => $set['tabfocus_elements'],
				'body_class' => $body_class
			);

			if ( $first_run )
				$mceInit = array_merge( self::$first_init, $mceInit );

			if ( is_array( $set['tinymce'] ) )
				$mceInit = array_merge( $mceInit, $set['tinymce'] );

			// For people who really REALLY know what they're doing with TinyMCE
			// You can modify $mceInit to add, remove, change elements of the config before tinyMCE.init
			// Setting "valid_elements", "invalid_elements" and "extended_valid_elements" can be done through this filter.
			// Best is to use the default cleanup by not specifying valid_elements, as TinyMCE contains full set of XHTML 1.0.
			if ( $set['teeny'] ) {
				$mceInit = apply_filters( 'teeny_mce_before_init', $mceInit, $editor_id );
			} else {
				$mceInit = apply_filters( 'tiny_mce_before_init', $mceInit, $editor_id );
			}

			if ( empty( $mceInit['toolbar3'] ) && ! empty( $mceInit['toolbar4'] ) ) {
				$mceInit['toolbar3'] = $mceInit['toolbar4'];
				$mceInit['toolbar4'] = '';
			}

			self::$mce_settings[$editor_id] = $mceInit;
		} // end if self::$this_tinymce
	}

	private static function _parse_init($init) {
		$options = '';

		foreach ( $init as $k => $v ) {
			if ( is_bool($v) ) {
				$val = $v ? 'true' : 'false';
				$options .= $k . ':' . $val . ',';
				continue;
			} elseif ( !empty($v) && is_string($v) && ( ('{' == $v{0} && '}' == $v{strlen($v) - 1}) || ('[' == $v{0} && ']' == $v{strlen($v) - 1}) || preg_match('/^\(?function ?\(/', $v) ) ) {
				$options .= $k . ':' . $v . ',';
				continue;
			}
			$options .= $k . ':"' . $v . '",';
		}

		return '{' . trim( $options, ' ,' ) . '}';
	}

	public static function enqueue_scripts() {
		wp_enqueue_script('word-count');

		if ( self::$has_tinymce )
			wp_enqueue_script('editor');

		if ( self::$has_quicktags )
			wp_enqueue_script('quicktags');

		if ( in_array('wplink', self::$plugins, true) || in_array('link', self::$qt_buttons, true) ) {
			wp_enqueue_script('wplink');
		}

		if ( in_array('wpfullscreen', self::$plugins, true) || in_array('fullscreen', self::$qt_buttons, true) )
			wp_enqueue_script('wp-fullscreen');

		if ( self::$has_medialib ) {
			add_thickbox();
			wp_enqueue_script('media-upload');

			if ( self::$has_tinymce )
				wp_enqueue_script('mce-view');
		}
	}

	public static function wp_mce_translation() {

		$mce_translation = array(
			// Default TinyMCE strings
			'Cut' => __('Cut'),
			'Header 2' => __('Header 2'),
			'Your browser doesn\'t support direct access to the clipboard. Please use the Ctrl+X/C/V keyboard shortcuts instead.' => __('Your browser does not support direct access to the clipboard. Please use the Ctrl+X/C/V keyboard shortcuts instead.'),
			'Div' => __('Div'),
			'Paste' => __('Paste'),
			'Close' => __('Close'),
			'Pre' => __('Pre'),
			'Align right' => __('Align right'),
			'New document' => __('New document'),
			'Blockquote' => __('Blockquote'),
			'Numbered list' => __('Numbered list'),
			'Increase indent' => __('Increase indent'),
			'Formats' => __('Formats'),
			'Headers' => __('Headers'),
			'Select all' => __('Select all'),
			'Header 3' => __('Header 3'),
			'Blocks' => __('Blocks'),
			'Undo' => __('Undo'),
			'Strikethrough' => __('Strikethrough'),
			'Bullet list' => __('Bullet list'),
			'Header 1' => __('Header 1'),
			'Superscript' => __('Superscript'),
			'Clear formatting' => __('Clear formatting'),
			'Subscript' => __('Subscript'),
			'Header 6' => __('Header 6'),
			'Redo' => __('Redo'),
			'Paragraph' => __('Paragraph'),
			'Ok' => __('Ok'),
			'Bold' => __('Bold'),
			'Code' => __('Code'),
			'Italic' => __('Italic'),
			'Align center' => __('Align center'),
			'Header 5' => __('Header 5'),
			'Decrease indent' => __('Decrease indent'),
			'Header 4' => __('Header 4'),
			'Paste is now in plain text mode. Contents will now be pasted as plain text until you toggle this option off.' => __('Paste is now in plain text mode. Contents will now be pasted as plain text until you toggle this option off.'),
			'Underline' => __('Underline'),
			'Cancel' => __('Cancel'),
			'Justify' => __('Justify'),
			'Inline' => __('Inline'),
			'Copy' => __('Copy'),
			'Align left' => __('Align left'),
			'Visual aids' => __('Visual aids'),
			'Lower Greek' => __('Lower Greek'),
			'Square' => __('Square'),
			'Default' => __('Default'),
			'Lower Alpha' => __('Lower Alpha'),
			'Circle' => __('Circle'),
			'Disc' => __('Disc'),
			'Upper Alpha' => __('Upper Alpha'),
			'Upper Roman' => __('Upper Roman'),
			'Lower Roman' => __('Lower Roman'),
			'Name' => __('Name'),
			'Anchor' => __('Anchor'),
			'You have unsaved changes are you sure you want to navigate away?' => __('You have unsaved changes are you sure you want to navigate away?'),
			'Restore last draft' => __('Restore last draft'),
			'Special character' => __('Special character'),
			'Source code' => __('Source code'),
			'Right to left' => __('Right to left'),
			'Left to right' => __('Left to right'),
			'Emoticons' => __('Emoticons'),
			'Robots' => __('Robots'),
			'Document properties' => __('Document properties'),
			'Title' => __('Title'),
			'Keywords' => __('Keywords'),
			'Encoding' => __('Encoding'),
			'Description' => __('Description'),
			'Author' => __('Author'),
			'Fullscreen' => __('Fullscreen'),
			'Horizontal line' => __('Horizontal line'),
			'Horizontal space' => __('Horizontal space'),
			'Insert/edit image' => __('Insert/edit image'),
			'General' => __('General'),
			'Advanced' => __('Advanced'),
			'Source' => __('Source'),
			'Border' => __('Border'),
			'Constrain proportions' => __('Constrain proportions'),
			'Vertical space' => __('Vertical space'),
			'Image description' => __('Image description'),
			'Style' => __('Style'),
			'Dimensions' => __('Dimensions'),
			'Insert image' => __('Insert image'),
			'Insert date/time' => __('Insert date/time'),
			'Remove link' => __('Remove link'),
			'Url' => __('Url'),
			'Text to display' => __('Text to display'),
			'Anchors' => __('Anchors'),
			'Insert link' => __('Insert link'),
			'New window' => __('New window'),
			'None' => __('None'),
			'Target' => __('Target'),
			'Insert/edit link' => __('Insert/edit link'),
			'Insert/edit video' => __('Insert/edit video'),
			'Poster' => __('Poster'),
			'Alternative source' => __('Alternative source'),
			'Paste your embed code below:' => __('Paste your embed code below:'),
			'Insert video' => __('Insert video'),
			'Embed' => __('Embed'),
			'Nonbreaking space' => __('Nonbreaking space'),
			'Page break' => __('Page break'),
			'Paste as text' => __('Paste as text'),
			'Preview' => __('Preview'),
			'Print' => __('Print'),
			'Save' => __('Save'),
			'Could not find the specified string.' => __('Could not find the specified string.'),
			'Replace' => __('Replace'),
			'Next' => __('Next'),
			'Whole words' => __('Whole words'),
			'Find and replace' => __('Find and replace'),
			'Replace with' => __('Replace with'),
			'Find' => __('Find'),
			'Replace all' => __('Replace all'),
			'Match case' => __('Match case'),
			'Prev' => __('Prev'),
			'Spellcheck' => __('Spellcheck'),
			'Finish' => __('Finish'),
			'Ignore all' => __('Ignore all'),
			'Ignore' => __('Ignore'),
			'Insert row before' => __('Insert row before'),
			'Rows' => __('Rows'),
			'Height' => __('Height'),
			'Paste row after' => __('Paste row after'),
			'Alignment' => __('Alignment'),
			'Column group' => __('Column group'),
			'Row' => __('Row'),
			'Insert column before' => __('Insert column before'),
			'Split cell' => __('Split cell'),
			'Cell padding' => __('Cell padding'),
			'Cell spacing' => __('Cell spacing'),
			'Row type' => __('Row type'),
			'Insert table' => __('Insert table'),
			'Body' => __('Body'),
			'Caption' => __('Caption'),
			'Footer' => __('Footer'),
			'Delete row' => __('Delete row'),
			'Paste row before' => __('Paste row before'),
			'Scope' => __('Scope'),
			'Delete table' => __('Delete table'),
			'Header cell' => __('Header cell'),
			'Column' => __('Column'),
			'Cell' => __('Cell'),
			'Header' => __('Header'),
			'Cell type' => __('Cell type'),
			'Copy row' => __('Copy row'),
			'Row properties' => __('Row properties'),
			'Table properties' => __('Table properties'),
			'Row group' => __('Row group'),
			'Right' => __('Right'),
			'Insert column after' => __('Insert column after'),
			'Cols' => __('Cols'),
			'Insert row after' => __('Insert row after'),
			'Width' => __('Width'),
			'Cell properties' => __('Cell properties'),
			'Left' => __('Left'),
			'Cut row' => __('Cut row'),
			'Delete column' => __('Delete column'),
			'Center' => __('Center'),
			'Merge cells' => __('Merge cells'),
			'Insert template' => __('Insert template'),
			'Templates' => __('Templates'),
			'Background color' => __('Background color'),
			'Text color' => __('Text color'),
			'Show blocks' => __('Show blocks'),
			'Show invisible characters' => __('Show invisible characters'),
			'Words: {0}' => __('Words: {0}'),
			'Insert' => __('Insert'),
			'File' => __('File'),
			'Edit' => __('Edit'),
			'Rich Text Area. Press ALT-F9 for menu. Press ALT-F10 for toolbar. Press ALT-0 for help' => __('Rich Text Area. Press ALT-F9 for menu. Press ALT-F10 for toolbar. Press ALT-0 for help'),
			'Tools' => __('Tools'),
			'View' => __('View'),
			'Table' => __('Table'),
			'Format' => __('Format'),

			// WordPress strings
			'Help' => __('Help'),
			'Toolbar Toggle' => __('Toolbar Toggle'),
			'Insert Read More tag' => __('Insert Read More tag'),
			'Distraction Free Writing' => __('Distraction Free Writing'),
		);

		$baseurl = self::$baseurl;
		$mce_locale = self::$mce_locale;

		$mce_translation = apply_filters( 'wp_mce_translation', $mce_translation, $mce_locale );

		foreach ( $mce_translation as $key => $value ) {
			if ( strpos( $value, '&' ) !== false )
				$mce_translation[$key] = html_entity_decode( $value, ENT_QUOTES, 'UTF-8' );
		}

		return "tinymce.addI18n( '$mce_locale', " . json_encode( $mce_translation ) . ");\n" .
			"tinymce.ScriptLoader.markDone( '$baseurl/langs/$mce_locale.js' );\n";
	}

	public static function editor_js() {
		global $tinymce_version, $concatenate_scripts, $compress_scripts;

		/**
		 * Filter "tiny_mce_version" is deprecated
		 *
		 * The tiny_mce_version filter is not needed since external plugins are loaded directly by TinyMCE.
		 * These plugins can be refreshed by appending query string to the URL passed to "mce_external_plugins" filter.
		 * If the plugin has a popup dialog, a query string can be added to the button action that opens it (in the plugin's code).
		 */
		$version = 'ver=' . $tinymce_version;
		$tmce_on = !empty(self::$mce_settings);

		if ( ! isset($concatenate_scripts) )
			script_concat_settings();

		$compressed = $compress_scripts && $concatenate_scripts && isset($_SERVER['HTTP_ACCEPT_ENCODING'])
			&& false !== stripos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip');

		$mceInit = $qtInit = '';
		if ( $tmce_on ) {
			foreach ( self::$mce_settings as $editor_id => $init ) {
				$options = self::_parse_init( $init );
				$mceInit .= "'$editor_id':{$options},";
			}
			$mceInit = '{' . trim($mceInit, ',') . '}';
		} else {
			$mceInit = '{}';
		}

		if ( !empty(self::$qt_settings) ) {
			foreach ( self::$qt_settings as $editor_id => $init ) {
				$options = self::_parse_init( $init );
				$qtInit .= "'$editor_id':{$options},";
			}
			$qtInit = '{' . trim($qtInit, ',') . '}';
		} else {
			$qtInit = '{}';
		}

		$ref = array(
			'plugins' => implode( ',', self::$plugins ),
			'theme' => 'modern',
			'language' => self::$mce_locale
		);

		$suffix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';

		do_action( 'before_wp_tiny_mce', self::$mce_settings );
		?>

		<script type="text/javascript">
		tinyMCEPreInit = {
			baseURL: "<?php echo self::$baseurl; ?>",
			suffix: "<?php echo $suffix; ?>",
			mceInit: <?php echo $mceInit; ?>,
			qtInit: <?php echo $qtInit; ?>,
			ref: <?php echo self::_parse_init( $ref ); ?>,
			load_ext: function(url,lang){var sl=tinymce.ScriptLoader;sl.markDone(url+'/langs/'+lang+'.js');sl.markDone(url+'/langs/'+lang+'_dlg.js');}
		};
		</script>
		<?php

		$baseurl = self::$baseurl;

		if ( $tmce_on ) {
			if ( $compressed ) {
				echo "<script type='text/javascript' src='{$baseurl}/wp-tinymce.php?c=1&amp;$version'></script>\n";
			} else {
				echo "<script type='text/javascript' src='{$baseurl}/tinymce.js?$version'></script>\n";
				echo "<script type='text/javascript' src='{$baseurl}/plugins/compat3x/plugin{$suffix}.js?$version'></script>\n";
			}

			if ( 'en' != self::$mce_locale )
				echo "<script type='text/javascript'>\n" . self::wp_mce_translation() . "</script>\n";

			if ( self::$ext_plugins ) {
				// Load the old-format English strings to prevent unsightly labels in old style popups
				echo "<script type='text/javascript' src='{$baseurl}/langs/wp-langs-en.js?$version'></script>\n";
			}
		}

		// Allow scripts to be added after tinymce.js has been loaded but before any editor instances are created.
		do_action( 'wp_tiny_mce_init', self::$mce_settings );

		?>
		<script type="text/javascript">
		<?php

		if ( self::$ext_plugins )
			echo self::$ext_plugins . "\n";

		if ( ! is_admin() )
			echo 'var ajaxurl = "' . admin_url( 'admin-ajax.php', 'relative' ) . '";';

		?>

		( function() {
			var init, edId, qtId, firstInit, wrapper;

			if ( typeof tinymce !== 'undefined' ) {
				for ( edId in tinyMCEPreInit.mceInit ) {
					if ( firstInit ) {
						init = tinyMCEPreInit.mceInit[edId] = tinymce.extend( {}, firstInit, tinyMCEPreInit.mceInit[edId] );
					} else {
						init = firstInit = tinyMCEPreInit.mceInit[edId];
					}

					wrapper = tinymce.DOM.select( '#wp-' + edId + '-wrap' )[0];

					if ( ( tinymce.DOM.hasClass( wrapper, 'tmce-active' ) || ! tinyMCEPreInit.qtInit.hasOwnProperty( edId ) ) &&
						! init.wp_skip_init ) {

						try {
							tinymce.init( init );

							if ( ! window.wpActiveEditor ) {
								window.wpActiveEditor = edId;
							}
						} catch(e){}
					}
				}
			}

			if ( typeof quicktags !== 'undefined' ) {
				for ( qtId in tinyMCEPreInit.qtInit ) {
					try {
						quicktags( tinyMCEPreInit.qtInit[qtId] );

						if ( ! window.wpActiveEditor ) {
							window.wpActiveEditor = qtId;
						}
					} catch(e){};
				}
			}

			if ( typeof jQuery !== 'undefined' ) {
				jQuery('.wp-editor-wrap').on( 'click.wp-editor', function() {
					if ( this.id ) {
						window.wpActiveEditor = this.id.slice( 3, -5 );
					}
				});
			} else {
				for ( qtId in tinyMCEPreInit.qtInit ) {
					document.getElementById( 'wp-' + qtId + '-wrap' ).onclick = function() {
						window.wpActiveEditor = this.id.slice( 3, -5 );
					}
				}
			}
		}());
		</script>
		<?php

		if ( in_array( 'wplink', self::$plugins, true ) || in_array( 'link', self::$qt_buttons, true ) )
			self::wp_link_dialog();

		if ( in_array( 'wpfullscreen', self::$plugins, true ) || in_array( 'fullscreen', self::$qt_buttons, true ) )
			self::wp_fullscreen_html();

		do_action( 'after_wp_tiny_mce', self::$mce_settings );
	}

	public static function wp_fullscreen_html() {
		global $content_width;
		$post = get_post();

		$width = isset( $content_width ) && 800 > $content_width ? $content_width : 800;
		$width = $width + 22; // compensate for the padding and border
		$dfw_width = get_user_setting( 'dfw_width', $width );
		$save = isset( $post->post_status ) && $post->post_status == 'publish' ? __('Update') : __('Save');

		?>
		<div id="wp-fullscreen-body"<?php if ( is_rtl() ) echo ' class="rtl"'; ?> data-theme-width="<?php echo (int) $width; ?>" data-dfw-width="<?php echo (int) $dfw_width; ?>">
		<div id="fullscreen-topbar">
			<div id="wp-fullscreen-toolbar">
			<div id="wp-fullscreen-close"><a href="#" onclick="wp.editor.fullscreen.off();return false;"><?php _e('Exit fullscreen'); ?></a></div>
			<div id="wp-fullscreen-central-toolbar" style="width:<?php echo $width; ?>px;">

			<div id="wp-fullscreen-mode-bar"><div id="wp-fullscreen-modes">
				<a href="#" onclick="wp.editor.fullscreen.switchmode('tinymce');return false;"><?php _e( 'Visual' ); ?></a>
				<a href="#" onclick="wp.editor.fullscreen.switchmode('html');return false;"><?php _ex( 'Text', 'Name for the Text editor tab (formerly HTML)' ); ?></a>
			</div></div>

			<div id="wp-fullscreen-button-bar"><div id="wp-fullscreen-buttons" class="mce-toolbar">
		<?php

		$buttons = array(
			// format: title, onclick, show in both editors
			'bold' => array( 'title' => __('Bold (Ctrl + B)'), 'both' => false ),
			'italic' => array( 'title' => __('Italic (Ctrl + I)'), 'both' => false ),
			'bullist' => array( 'title' => __('Unordered list (Alt + Shift + U)'), 'both' => false ),
			'numlist' => array( 'title' => __('Ordered list (Alt + Shift + O)'), 'both' => false ),
			'blockquote' => array( 'title' => __('Blockquote (Alt + Shift + Q)'), 'both' => false ),
			'wp-media-library' => array( 'title' => __('Media library (Alt + Shift + M)'), 'both' => true ),
			'link' => array( 'title' => __('Insert/edit link (Alt + Shift + A)'), 'both' => true ),
			'unlink' => array( 'title' => __('Unlink (Alt + Shift + S)'), 'both' => false ),
			'help' => array( 'title' => __('Help (Alt + Shift + H)'), 'both' => false ),
		);

		$buttons = apply_filters( 'wp_fullscreen_buttons', $buttons );

		foreach ( $buttons as $button => $args ) {
			if ( 'separator' == $args ) {
				continue;
			}

			$onclick = ! empty( $args['onclick'] ) ? ' onclick="' . $args['onclick'] . '"' : '';
			?>

			<div class="mce-widget mce-btn<?php if ( $args['both'] ) { ?> wp-fullscreen-both<?php } ?>">
			<button type="button" role="presentation" title="<?php echo $args['title']; ?>"<?php echo $onclick; ?> id="wp_fs_<?php echo $button; ?>">
				<i class="mce-ico mce-i-<?php echo $button; ?>"></i>
			</button>
			</div>
			<?php
		}

		?>

		</div></div>

		<div id="wp-fullscreen-save">
			<input type="button" class="button-primary right" value="<?php echo $save; ?>" onclick="wp.editor.fullscreen.save();" />
			<span class="spinner"></span>
			<span class="wp-fullscreen-saved-message"><?php if ( $post->post_status == 'publish' ) _e('Updated.'); else _e('Saved.'); ?></span>
			<span class="wp-fullscreen-error-message"><?php _e('Save failed.'); ?></span>
		</div>

		</div>
		</div>
	</div>
	<div id="wp-fullscreen-status">
		<div id="wp-fullscreen-count"><?php printf( __( 'Word count: %s' ), '<span class="word-count">0</span>' ); ?></div>
		<div id="wp-fullscreen-tagline"><?php _e('Just write.'); ?></div>
	</div>
	</div>

	<div class="fullscreen-overlay" id="fullscreen-overlay"></div>
	<div class="fullscreen-overlay fullscreen-fader fade-300" id="fullscreen-fader"></div>
	<?php
	}

	/**
	 * Performs post queries for internal linking.
	 *
	 * @since 3.1.0
	 *
	 * @param array $args Optional. Accepts 'pagenum' and 's' (search) arguments.
	 * @return array Results.
	 */
	public static function wp_link_query( $args = array() ) {
		$pts = get_post_types( array( 'public' => true ), 'objects' );
		$pt_names = array_keys( $pts );

		$query = array(
			'post_type' => $pt_names,
			'suppress_filters' => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'post_status' => 'publish',
			'posts_per_page' => 20,
		);

		$args['pagenum'] = isset( $args['pagenum'] ) ? absint( $args['pagenum'] ) : 1;

		if ( isset( $args['s'] ) )
			$query['s'] = $args['s'];

		$query['offset'] = $args['pagenum'] > 1 ? $query['posts_per_page'] * ( $args['pagenum'] - 1 ) : 0;

		/**
		 * Filter the link query arguments.
		 *
		 * Allows modification of the link query arguments before querying.
		 *
		 * @see WP_Query for a full list of arguments
		 *
		 * @since 3.7.0
		 *
		 * @param array $query An array of WP_Query arguments.
		 */
		$query = apply_filters( 'wp_link_query_args', $query );

		// Do main query.
		$get_posts = new WP_Query;
		$posts = $get_posts->query( $query );
		// Check if any posts were found.
		if ( ! $get_posts->post_count )
			return false;

		// Build results.
		$results = array();
		foreach ( $posts as $post ) {
			if ( 'post' == $post->post_type )
				$info = mysql2date( __( 'Y/m/d' ), $post->post_date );
			else
				$info = $pts[ $post->post_type ]->labels->singular_name;

			$results[] = array(
				'ID' => $post->ID,
				'title' => trim( esc_html( strip_tags( get_the_title( $post ) ) ) ),
				'permalink' => get_permalink( $post->ID ),
				'info' => $info,
			);
		}

		/**
		 * Filter the link query results.
		 *
		 * Allows modification of the returned link query results.
		 *
		 * @since 3.7.0
		 *
		 * @param array $results {
		 *     An associative array of query results.
		 *
		 *     @type array {
		 *         @type int    'ID'        The post ID.
		 *         @type string 'title'     The trimmed, escaped post title.
		 *         @type string 'permalink' The post permalink.
		 *         @type string 'info'      A 'Y/m/d'-formatted date for 'post' post type, the 'singular_name' post type label otherwise.
		 *     }
		 * }
		 * @param array $query   An array of WP_Query arguments. @see 'wp_link_query_args' filter
		 */
		return apply_filters( 'wp_link_query', $results, $query );
	}

	/**
	 * Dialog for internal linking.
	 *
	 * @since 3.1.0
	 */
	public static function wp_link_dialog() {
		$search_panel_visible = '1' == get_user_setting( 'wplink', '0' ) ? ' class="search-panel-visible"' : '';

		?>
		<div id="wp-link-backdrop"></div>
		<div id="wp-link-wrap"<?php echo $search_panel_visible; ?>>
		<form id="wp-link" tabindex="-1">
		<?php wp_nonce_field( 'internal-linking', '_ajax_linking_nonce', false ); ?>
		<div id="link-modal-title">
			<?php _e( 'Insert/edit link' ) ?>
			<div id="wp-link-close" tabindex="0"></div>
	 	</div>
		<div id="link-selector">
			<div id="link-options">
				<p class="howto"><?php _e( 'Enter the destination URL' ); ?></p>
				<div>
					<label><span><?php _e( 'URL' ); ?></span><input id="url-field" type="text" name="href" /></label>
				</div>
				<div>
					<label><span><?php _e( 'Title' ); ?></span><input id="link-title-field" type="text" name="linktitle" /></label>
				</div>
				<div class="link-target">
					<label><input type="checkbox" id="link-target-checkbox" /> <?php _e( 'Open link in a new window/tab' ); ?></label>
				</div>
			</div>
			<p class="howto" id="wp-link-search-toggle"><?php _e( 'Or link to existing content' ); ?></p>
			<div id="search-panel">
				<div class="link-search-wrapper">
					<label>
						<span class="search-label"><?php _e( 'Search' ); ?></span>
						<input type="search" id="search-field" class="link-search-field" autocomplete="off" />
						<span class="spinner"></span>
					</label>
				</div>
				<div id="search-results" class="query-results">
					<ul></ul>
					<div class="river-waiting">
						<span class="spinner"></span>
					</div>
				</div>
				<div id="most-recent-results" class="query-results">
					<div class="query-notice"><em><?php _e( 'No search term specified. Showing recent items.' ); ?></em></div>
					<ul></ul>
					<div class="river-waiting">
						<span class="spinner"></span>
					</div>
				</div>
			</div>
		</div>
		<div class="submitbox">
			<div id="wp-link-update">
				<input type="submit" value="<?php esc_attr_e( 'Add Link' ); ?>" class="button-primary" id="wp-link-submit" name="wp-link-submit">
			</div>
		</div>
		</form>
		</div>
		<?php
	}
}
