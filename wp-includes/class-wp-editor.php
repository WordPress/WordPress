<?php
/**
 * Adds the WordPress editor used on the Write and Edit screens.
 *
 * @package WordPress
 * @since 3.3
 *
 * NOTE: Do not instantiate this class directly. Please use the wp_editor() function that will include the file
 * and instantiate the class if needed. If you want to extend this class use the 'init' or earlier action to do it
 * and call wp_editor() as usual when you need to output the HTML.
 *
 * Outputs the HTML and JavaScript for the WordPress editors, TinyMCE and Quicktags.
 * TinyMCE is loaded separately from other Javascript by using wp-tinymce.php. It outputs concatenated
 * pre-compressed version of the core and all default plugins. Additional plugins are loaded directly
 * by TinyMCE using non-blocking method.
 */

class WP_Editor {

	var $mce_settings = array();
	var $qt_settings = array();
	var $plugins = array();
	var $qt_buttons = array();
	var $mce_locale;
	var $ext_plugins;
	var $baseurl;
	var $can_richedit;
	var $default_editor;
	var $first_init;
	var $this_tinymce = false;
	var $this_quicktags = false;
	var $has_tinymce = false;
	var $has_quicktags = false;
	var $has_medialib = false;

	function __construct() {
		$this->can_richedit = user_can_richedit();
		$this->default_editor = $this->wp_default_editor();
	}

	/**
	 * Outputs the HTML and enqueues the JavaScript for a single instance of the editor.
	 *
	 * @param string $content The initial content of the editor.
	 * @param string $editor_id ID for the textarea and TinyMCE and Quicktags instances (can contain only ASCII letters and numbers).
	 * @param array $settings See below for description.
	 */
	function editor( $content, $editor_id, $settings = array() ) {

		$set = wp_parse_args( $settings,  array(
			'wpautop' => true, // use wpautop?
			'media_buttons' => true, // show insert/upload button(s)
			'textarea_name' => $editor_id, // set the textarea name to something different, square brackets [] can be used here
			'textarea_rows' => get_option('default_post_edit_rows', 10), // rows="..."
			'tabindex' => '',
			'editor_css' => '', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
			'editor_class' => '', // add extra class(es) to the editor textarea
			'teeny' => false, // output the minimal editor config used in Press This
			'dfw' => false, // replace the default fullscreen with DFW (needs specific DOM elements and css)
			'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
			'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
		) );

		$this->this_tinymce = !empty($set['tinymce']) && $this->can_richedit;
		$this->this_quicktags = !empty($set['quicktags']);
		$editor_class = ' class="' . trim( $set['editor_class'] . ' wp-editor-area' ) . '"';
		$tabindex = $set['tabindex'] ? ' tabindex="' . (int) $set['tabindex'] . '"' : '';
		$rows = ' rows="' . (int) $set['textarea_rows'] . '"';
		$switch_class = 'html-active';
		$toolbar = $buttons = '';

		if ( !current_user_can( 'upload_files' ) )
			$set['media_buttons'] = false;

		if ( $this->this_quicktags && $this->this_tinymce ) {
			$switch_class = 'html-active';
			$this->has_tinymce = $this->has_quicktags = true;

			if ( 'html' == $this->default_editor ) {
				add_filter('the_editor_content', 'wp_htmledit_pre');
			} else {
				add_filter('the_editor_content', 'wp_richedit_pre');
				$switch_class = 'tmce-active';
			}

			$buttons .= '<a id="' . $editor_id . '-html" class="hide-if-no-js wp-switch-editor switch-html" onclick="switchEditors.go(this);return false;">' . __('HTML') . "</a>\n";
			$buttons .= '<a id="' . $editor_id . '-tmce" class="hide-if-no-js wp-switch-editor switch-tmce" onclick="switchEditors.go(this);return false;">' . __('Visual') . "</a>\n";
		} else {
			if ( $this->this_tinymce )
				$this->has_tinymce = true;

			if ( $this->this_quicktags )
				$this->has_quicktags = true;
		}

		echo '<div id="wp-' . $editor_id . '-wrap" class="wp-editor-wrap ' . $switch_class . '">';

		if ( empty($this->first_init) )
			wp_print_styles('editor-buttons');

		if ( !empty($set['editor_css']) )
			echo $set['editor_css'] . "\n";

		if ( !empty($buttons) || $set['media_buttons'] ) {
			echo '<div id="wp-' . $editor_id . '-editor-tools" class="wp-editor-tools">';
			echo $buttons;

			if ( $set['media_buttons'] ) {
				$this->has_medialib = true;

				if ( !function_exists('media_buttons') )
					include(ABSPATH . 'wp-admin/includes/media.php');

				echo '<div id="wp-' . $editor_id . '-media-buttons" class="hide-if-no-js wp-media-buttons">';
				do_action('media_buttons', $editor_id);
				echo "</div>\n";
			}
			echo "</div>\n";
		}

		$the_editor = apply_filters('the_editor', '<div id="wp-' . $editor_id . '-editor-container" class="wp-editor-container"><textarea' . $editor_class . $rows . $tabindex . ' cols="40" name="' . $set['textarea_name'] . '" id="' . $editor_id . '">%s</textarea></div>');
		$content = apply_filters('the_editor_content', $content);

		printf($the_editor, $content);
		echo "\n</div>\n\n";

		if ( empty($this->first_init) ) {
			add_action( 'admin_print_footer_scripts', array($this, 'editor_js'), 50 );
			add_action( 'wp_print_footer_scripts', array($this, 'editor_js'), 50 );
			add_action( 'admin_footer', array($this, 'enqueue_scripts'), 1 );
			add_action( 'wp_footer', array($this, 'enqueue_scripts'), 1 );
		}

		$this->editor_settings($editor_id, $set);
	}

	function editor_settings($editor_id, $set) {
		global $editor_styles;
		$first_run = false;

		if ( $this->this_quicktags ) {
			$qt_buttons = array();

			$qtInit = array(
				'id' => $editor_id,
				'buttons' => '',
				'disabled_buttons' => ''
			);

			if ( is_array($set['quicktags']) )
				$qtInit = array_merge($qtInit, $set['quicktags']);

			$qtInit = apply_filters( 'quicktags_settings', $qtInit, $editor_id );

			$this->qt_settings[$editor_id] = $qtInit;

			if ( !empty($qtInit['buttons']) || !empty($qtInit['disabled_buttons']) ) {
				if ( strpos( ',' . $qtInit['buttons'] . ',', ',link,' ) !== false )
					$qt_buttons[] = 'link';

				if ( strpos( ',' . $qtInit['disabled_buttons'] . ',', ',link,' ) !== false )
					$qt_buttons = array();
			} else {
				$qt_buttons[] = 'link';
			}

			if ( $set['dfw'] )
				$qt_buttons[] = 'fullscreen';

			$this->qt_buttons = array_merge( $this->qt_buttons, $qt_buttons );
		}

		if ( $this->this_tinymce ) {

			if ( empty($this->first_init) ) {
				$this->baseurl = includes_url('js/tinymce');
				$this->mce_locale = $mce_locale = ( '' == get_locale() ) ? 'en' : strtolower( substr(get_locale(), 0, 2) ); // only ISO 639-1
				$no_captions = (bool) apply_filters( 'disable_captions', '' );
				$plugins = array( 'inlinepopups', 'spellchecker', 'tabfocus', 'paste', 'media', 'fullscreen', 'wordpress', 'wpeditimage', 'wpgallery', 'wplink', 'wpdialogs' );
				$first_run = true;

				if ( $set['teeny'] ) {
					$this->plugins = $plugins = apply_filters( 'teeny_mce_plugins', array('inlinepopups', 'fullscreen', 'wordpress', 'wplink', 'wpdialogs'), $editor_id );
					$ext_plugins = '';
				} else {
					/*
					The following filter takes an associative array of external plugins for TinyMCE in the form 'plugin_name' => 'url'.
					It adds the plugin's name to TinyMCE's plugins init and the call to PluginManager to load the plugin.
					The url should be absolute and should include the js file name to be loaded. Example:
					array( 'myplugin' => 'http://my-site.com/wp-content/plugins/myfolder/mce_plugin.js' )
					If the plugin uses a button, it should be added with one of the "$mce_buttons" filters.
					*/
					$mce_external_plugins = apply_filters('mce_external_plugins', array());

					$ext_plugins = '';
					if ( ! empty($mce_external_plugins) ) {

						/*
						The following filter loads external language files for TinyMCE plugins.
						It takes an associative array 'plugin_name' => 'path', where path is the
						include path to the file. The language file should follow the same format as
						/tinymce/langs/wp-langs.php and should define a variable $strings that
						holds all translated strings.
						When this filter is not used, the function will try to load {mce_locale}.js.
						If that is not found, en.js will be tried next.
						*/
						$mce_external_languages = apply_filters('mce_external_languages', array());

						$loaded_langs = array();
						$strings = '';

						if ( ! empty($mce_external_languages) ) {
							foreach ( $mce_external_languages as $name => $path ) {
								if ( @is_file($path) && @is_readable($path) ) {
									include_once($path);
									$ext_plugins .= $strings . "\n";
									$loaded_langs[] = $name;
								}
							}
						}

						foreach ( $mce_external_plugins as $name => $url ) {

							if ( is_ssl() ) $url = str_replace('http://', 'https://', $url);

							$plugins[] = '-' . $name;

							$plugurl = dirname($url);
							$strings = $str1 = $str2 = '';
							if ( ! in_array($name, $loaded_langs) ) {
								$path = str_replace( content_url(), '', $plugurl );
								$path = WP_CONTENT_DIR . $path . '/langs/';

								if ( function_exists('realpath') )
									$path = trailingslashit( realpath($path) );

								if ( @is_file($path . $mce_locale . '.js') )
									$strings .= @file_get_contents($path . $mce_locale . '.js') . "\n";

								if ( @is_file($path . $mce_locale . '_dlg.js') )
									$strings .= @file_get_contents($path . $mce_locale . '_dlg.js') . "\n";

								if ( 'en' != $mce_locale && empty($strings) ) {
									if ( @is_file($path . 'en.js') ) {
										$str1 = @file_get_contents($path . 'en.js');
										$strings .= preg_replace( '/([\'"])en\./', '$1' . $mce_locale . '.', $str1, 1 ) . "\n";
									}

									if ( @is_file($path . 'en_dlg.js') ) {
										$str2 = @file_get_contents($path . 'en_dlg.js');
										$strings .= preg_replace( '/([\'"])en\./', '$1' . $mce_locale . '.', $str2, 1 ) . "\n";
									}
								}

								if ( ! empty($strings) )
									$ext_plugins .= "\n" . $strings . "\n";
							}

							$ext_plugins .= 'tinyMCEPreInit.load_ext("' . $plugurl . '", "' . $mce_locale . '");' . "\n";
							$ext_plugins .= 'tinymce.PluginManager.load("' . $name . '", "' . $url . '");' . "\n";

							$this->ext_plugins .= $ext_plugins;
						}
					}

					$plugins = array_unique( apply_filters('tiny_mce_plugins', $plugins) );
				}

				if ( $set['dfw'] )
					$plugins[] = 'wpfullscreen';

				$this->plugins = $plugins;

				/*
				The following filter allows localization scripts to change the languages displayed in the spellchecker's drop-down menu.
				By default it uses Google's spellchecker API, but can be configured to use PSpell/ASpell if installed on the server.
				The + sign marks the default language. More information:
				http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/spellchecker
				*/
				$mce_spellchecker_languages = apply_filters('mce_spellchecker_languages', '+English=en,Danish=da,Dutch=nl,Finnish=fi,French=fr,German=de,Italian=it,Polish=pl,Portuguese=pt,Spanish=es,Swedish=sv');

				$this->first_init = array(
					'mode' => 'exact',
					'width' => '100%',
					'theme' => 'advanced',
					'skin' => 'wp_theme',
					'language' => $this->mce_locale,
					'spellchecker_languages' => $mce_spellchecker_languages,
					'theme_advanced_toolbar_location' => 'top',
					'theme_advanced_toolbar_align' => 'left',
					'theme_advanced_statusbar_location' => 'bottom',
					'theme_advanced_resizing' => true,
					'theme_advanced_resize_horizontal' => false,
					'dialog_type' => 'modal',
					'formats' => "{
						alignleft : [
							{selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles : {textAlign : 'left'}},
							{selector : 'img,table', classes : 'alignleft'}
						],
						aligncenter : [
							{selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles : {textAlign : 'center'}},
							{selector : 'img,table', classes : 'aligncenter'}
						],
						alignright : [
							{selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles : {textAlign : 'right'}},
							{selector : 'img,table', classes : 'alignright'}
						],
						strikethrough : {inline : 'del'}
					}",
					'relative_urls' => false,
					'remove_script_host' => false,
					'convert_urls' => false,
					'remove_linebreaks' => true,
					'gecko_spellcheck' => true,
					'keep_styles' => false,
					'entities' => '38,amp,60,lt,62,gt',
					'accessibility_focus' => true,
					'tabfocus_elements' => 'major-publishing-actions',
					'media_strict' => false,
					'paste_remove_styles' => true,
					'paste_remove_spans' => true,
					'paste_strip_class_attributes' => 'all',
					'paste_text_use_dialog' => true,
					'extended_valid_elements' => 'article[*],aside[*],audio[*],canvas[*],command[*],datalist[*],details[*],embed[*],figcaption[*],figure[*],footer[*],header[*],hgroup[*],keygen[*],mark[*],meter[*],nav[*],output[*],progress[*],section[*],source[*],summary,time[*],video[*],wbr',
					'wpeditimage_disable_captions' => $no_captions,
					'wp_fullscreen_content_css' => "$this->baseurl/plugins/wpfullscreen/css/wp-fullscreen.css",
					'plugins' => implode( ',', $plugins )
				);

				// load editor_style.css if the current theme supports it
				if ( ! empty( $editor_styles ) && is_array( $editor_styles ) ) {
					$mce_css = array();
					$editor_styles = array_unique($editor_styles);
					$style_uri = get_stylesheet_directory_uri();
					if ( ! is_child_theme() ) {
						foreach ( $editor_styles as $file )
							$mce_css[] = "$style_uri/$file";
					} else {
						$style_dir    = get_stylesheet_directory();
						$template_uri = get_template_directory_uri();
						$template_dir = get_template_directory();

						foreach ( $editor_styles as $file ) {
							if ( file_exists( "$template_dir/$file" ) )
								$mce_css[] = "$template_uri/$file";
						}

						foreach ( $editor_styles as $file ) {
							if ( file_exists( "$style_dir/$file" ) )
								$mce_css[] = "$style_uri/$file";
						}
					}
					$mce_css = implode( ',', $mce_css );
				} else {
					$mce_css = '';
				}

				$mce_css = trim( apply_filters( 'mce_css', $mce_css ), ' ,' );

				if ( ! empty($mce_css) )
					$this->first_init['content_css'] = $mce_css;
			}

			if ( $set['teeny'] ) {
				$mce_buttons = apply_filters( 'teeny_mce_buttons', array('bold', 'italic', 'underline', 'blockquote', 'separator', 'strikethrough', 'bullist', 'numlist', 'justifyleft', 'justifycenter', 'justifyright', 'undo', 'redo', 'link', 'unlink', 'fullscreen'), $editor_id );
				$mce_buttons_2 = $mce_buttons_3 = $mce_buttons_4 = array();
			} else {
				$mce_buttons = apply_filters('mce_buttons', array('bold', 'italic', 'strikethrough', '|', 'bullist', 'numlist', 'blockquote', '|', 'justifyleft', 'justifycenter', 'justifyright', '|', 'link', 'unlink', 'wp_more', '|', 'spellchecker', 'fullscreen', 'wp_adv' ), $editor_id);
				$mce_buttons_2 = apply_filters('mce_buttons_2', array( 'formatselect', 'underline', 'justifyfull', 'forecolor', '|', 'pastetext', 'pasteword', 'removeformat', '|', 'charmap', '|', 'outdent', 'indent', '|', 'undo', 'redo', 'wp_help' ), $editor_id);
				$mce_buttons_3 = apply_filters('mce_buttons_3', array(), $editor_id);
				$mce_buttons_4 = apply_filters('mce_buttons_4', array(), $editor_id);
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
				'elements' => $editor_id,
				'wpautop' => (bool) $set['wpautop'],
				'remove_linebreaks' => (bool) $set['wpautop'],
				'apply_source_formatting' => (bool) !$set['wpautop'],
				'theme_advanced_buttons1' => implode($mce_buttons, ','),
				'theme_advanced_buttons2' => implode($mce_buttons_2, ','),
				'theme_advanced_buttons3' => implode($mce_buttons_3, ','),
				'theme_advanced_buttons4' => implode($mce_buttons_4, ',')
			);

			if ( $first_run )
				$mceInit = array_merge($this->first_init, $mceInit);

			if ( is_array($set['tinymce']) )
				$mceInit = array_merge($mceInit, $set['tinymce']);

			// For people who really REALLY know what they're doing with TinyMCE
			// You can modify initArray to add, remove, change elements of the config before tinyMCE.init
			// Setting "valid_elements", "invalid_elements" and "extended_valid_elements" can be done through this filter.
			// Best is to use the default cleanup by not specifying valid_elements, as TinyMCE contains full set of XHTML 1.0.
			if ( $set['teeny'] ) {
				$mceInit = apply_filters('teeny_mce_before_init', $mceInit, $editor_id);
			} else {
				$mceInit = apply_filters('tiny_mce_before_init', $mceInit, $editor_id);
			}

			if ( empty($mceInit['theme_advanced_buttons3']) && !empty($mceInit['theme_advanced_buttons4']) ) {
				$mceInit['theme_advanced_buttons3'] = $mceInit['theme_advanced_buttons4'];
				$mceInit['theme_advanced_buttons4'] = '';
			}

			$this->mce_settings[$editor_id] = $mceInit;
			$first_run = false;
		} // end if $this->this_tinymce
	}

	function _parse_init($init) {
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

	/**
	 * Find out which editor should be displayed by default.
	 *
	 * Works out which of the two editors to display as the current editor for a
	 * user.
	 *
	 * @since 2.5.0
	 *
	 * @return string Either 'tinymce', or 'html', or 'test'
	 */
	function wp_default_editor() {
		$r = user_can_richedit() ? 'tinymce' : 'html'; // defaults
		if ( $user = wp_get_current_user() ) { // look for cookie
			$ed = get_user_setting('editor', 'tinymce');
			$r = ( in_array($ed, array('tinymce', 'html', 'test') ) ) ? $ed : $r;
		}
		return apply_filters( 'wp_default_editor', $r ); // filter
	}

	function enqueue_scripts() {
		wp_enqueue_script('word-count');

		if ( $this->has_tinymce )
			wp_enqueue_script('editor');

		if ( $this->has_quicktags )
			wp_enqueue_script('quicktags');

		if ( in_array('wplink', $this->plugins, true) || in_array('link', $this->qt_buttons, true) ) {
			wp_enqueue_script('wplink');
			wp_enqueue_script('wpdialogs-popup');
			wp_enqueue_style('wp-jquery-ui-dialog');
		}

		if ( in_array('wpfullscreen', $this->plugins, true) || in_array('fullscreen', $this->qt_buttons, true) )
			wp_enqueue_script('wp-fullscreen');

		if ( $this->has_medialib ) {
			add_thickbox();
			wp_enqueue_script('media-upload');
		}
	}

	function editor_js() {
		global $tinymce_version, $concatenate_scripts, $compress_scripts;

		/**
		 * Filter "tiny_mce_version" is deprecated
		 *
		 * The tiny_mce_version filter is not needed since external plugins are loaded directly by TinyMCE.
		 * These plugins can be refreshed by appending query string to the URL passed to "mce_external_plugins" filter.
		 * If the plugin has a popup dialog, a query string can be added to the button action that opens it (in the plugin's code).
		 */
		$version = 'ver=' . $tinymce_version;
		$tmce_on = !empty($this->mce_settings);

		if ( ! isset($concatenate_scripts) )
			script_concat_settings();

		$compressed = $compress_scripts && $concatenate_scripts && isset($_SERVER['HTTP_ACCEPT_ENCODING'])
			&& false !== stripos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip');

		if ( $tmce_on && 'en' != $this->mce_locale )
			include_once(ABSPATH . WPINC . '/js/tinymce/langs/wp-langs.php');

		$mceInit = $qtInit = '';
		if ( $tmce_on ) {
			foreach ( $this->mce_settings as $editor_id => $init ) {
				$options = $this->_parse_init( $init );
				$mceInit .= "'$editor_id':{$options},";
			}
			$mceInit = '{' . trim($mceInit, ',') . '}';
		} else {
			$mceInit = '{}';
		}

		if ( !empty($this->qt_settings) ) {
			foreach ( $this->qt_settings as $editor_id => $init ) {
				$options = $this->_parse_init( $init );
				$qtInit .= "'$editor_id':{$options},";
			}
			$qtInit = '{' . trim($qtInit, ',') . '}';
		} else {
			$qtInit = '{}';
		}

		$ref = array(
			'plugins' => implode( ',', $this->plugins ),
			'theme' => 'advanced',
			'language' => $this->mce_locale
		);

		do_action('before_wp_tiny_mce', $this->mce_settings);
?>

	<script type="text/javascript">
		tinyMCEPreInit = {
			base : "<?php echo $this->baseurl; ?>",
			suffix : "",
			query : "<?php echo $version; ?>",
			mceInit : <?php echo $mceInit; ?>,
			qtInit : <?php echo $qtInit; ?>,
			ref : <?php echo $this->_parse_init( $ref ); ?>,
			load_ext : function(url,lang){var sl=tinymce.ScriptLoader;sl.markDone(url+'/langs/'+lang+'.js');sl.markDone(url+'/langs/'+lang+'_dlg.js');}
		};
	</script>
<?php

		if ( $tmce_on ) {
			if ( $compressed )
				echo "<script type='text/javascript' src='$this->baseurl/wp-tinymce.php?c=1&amp;$version'></script>\n";
			else
				echo "<script type='text/javascript' src='$this->baseurl/tiny_mce.js?$version'></script>\n";

			if ( 'en' != $this->mce_locale && isset($lang) )
				echo "<script type='text/javascript'>\n$lang\n</script>\n";
			else
				echo "<script type='text/javascript' src='$this->baseurl/langs/wp-langs-en.js?$version'></script>\n";
		}
?>

	<script type="text/javascript">
		(function(){
			var init, ed, qt, first_init, mce = <?php echo $this->default_editor == 'tinymce' ? 'true' : 'false'; ?>;

			if ( typeof(tinymce) == 'object' ) {
				for ( ed in tinyMCEPreInit.mceInit ) {
					if ( first_init ) {
						init = tinyMCEPreInit.mceInit[ed] = tinymce.extend( {}, first_init, tinyMCEPreInit.mceInit[ed] );
					} else {
						init = first_init = tinyMCEPreInit.mceInit[ed];
					}

					if ( mce )
						try { tinymce.init(init); } catch(e){}
				}
			}

			if ( typeof(QTags) == 'function' ) {
				for ( qt in tinyMCEPreInit.qtInit ) {
					try { quicktags( tinyMCEPreInit.qtInit[qt] ); } catch(e){}
				}
			}
		})();

		var wpActiveEditor;

		jQuery('.wp-editor-wrap').mousedown(function(e){
	    	wpActiveEditor = this.id.slice(3, -5);
		});

<?php

		if ( $this->ext_plugins )
			echo "$this->ext_plugins\n";

		if ( ! $compressed && $tmce_on ) {
?>
		(function(){var t=tinyMCEPreInit,sl=tinymce.ScriptLoader,ln=t.ref.language,th=t.ref.theme,pl=t.ref.plugins;sl.markDone(t.base+'/langs/'+ln+'.js');sl.markDone(t.base+'/themes/'+th+'/langs/'+ln+'.js');sl.markDone(t.base+'/themes/'+th+'/langs/'+ln+'_dlg.js');sl.markDone(t.base+'/themes/advanced/skins/wp_theme/ui.css');tinymce.each(pl.split(','),function(n){if(n&&n.charAt(0)!='-'){sl.markDone(t.base+'/plugins/'+n+'/langs/'+ln+'.js');sl.markDone(t.base+'/plugins/'+n+'/langs/'+ln+'_dlg.js');}});})();
<?php
		}

		if ( !is_admin() )
			echo 'var ajaxurl = "' . admin_url('admin-ajax.php') . '";';
?>
	</script>
<?php

		if ( in_array('wplink', $this->plugins, true) || in_array('link', $this->qt_buttons, true) )
			$this->wp_link_dialog();

		if ( in_array('wpfullscreen', $this->plugins, true) || in_array('fullscreen', $this->qt_buttons, true) )
			$this->wp_fullscreen_html();

		do_action('after_wp_tiny_mce', $this->mce_settings);
	}

	function wp_fullscreen_html() {
		global $content_width, $post;

		$width = isset($content_width) && 800 > $content_width ? $content_width : 800;
		$width = $width + 22; // compensate for the padding and border
		$dfw_width = get_user_setting( 'dfw_width', $width );
		$save = isset($post->post_status) && $post->post_status == 'publish' ? __('Update') : __('Save');
	?>
	<div id="wp-fullscreen-body">
	<div id="fullscreen-topbar">
		<div id="wp-fullscreen-toolbar">
			<div id="wp-fullscreen-close"><a href="#" onclick="fullscreen.off();return false;"><?php _e('Exit fullscreen'); ?></a></div>
			<div id="wp-fullscreen-central-toolbar" style="width:<?php echo $width; ?>px;">

			<div id="wp-fullscreen-mode-bar"><div id="wp-fullscreen-modes">
				<a href="#" onclick="fullscreen.switchmode('tinymce');return false;"><?php _e('Visual'); ?></a>
				<a href="#" onclick="fullscreen.switchmode('html');return false;"><?php _e('HTML'); ?></a>
			</div></div>

			<div id="wp-fullscreen-button-bar"><div id="wp-fullscreen-buttons" class="wp_themeSkin">
	<?php

		$buttons = array(
			// format: title, onclick, show in both editors
			'bold' => array( 'title' => __('Bold (Ctrl + B)'), 'onclick' => 'fullscreen.b();', 'both' => false ),
			'italic' => array( 'title' => __('Italic (Ctrl + I)'), 'onclick' => 'fullscreen.i();', 'both' => false ),
			'0' => 'separator',
			'bullist' => array( 'title' => __('Unordered list (Alt + Shift + U)'), 'onclick' => 'fullscreen.ul();', 'both' => false ),
			'numlist' => array( 'title' => __('Ordered list (Alt + Shift + O)'), 'onclick' => 'fullscreen.ol();', 'both' => false ),
			'1' => 'separator',
			'blockquote' => array( 'title' => __('Blockquote (Alt+Shift+Q)'), 'onclick' => 'fullscreen.blockquote();', 'both' => false ),
			'image' => array( 'title' => __('Insert/edit image (Alt + Shift + M)'), 'onclick' => "fullscreen.medialib();", 'both' => true ),
			'2' => 'separator',
			'link' => array( 'title' => __('Insert/edit link (Alt + Shift + A)'), 'onclick' => 'fullscreen.link();', 'both' => true ),
			'unlink' => array( 'title' => __('Unlink (Alt + Shift + S)'), 'onclick' => 'fullscreen.unlink();', 'both' => false ),
			'3' => 'separator',
			'help' => array( 'title' => __('Help (Alt + Shift + H)'), 'onclick' => 'fullscreen.help();', 'both' => false )
		);

		$buttons = apply_filters( 'wp_fullscreen_buttons', $buttons );

		foreach ( $buttons as $button => $args ) {
			if ( 'separator' == $args ) { ?>
				<div><span aria-orientation="vertical" role="separator" class="mceSeparator"></span></div>
	<?php		continue;
			} ?>

			<div<?php if ( $args['both'] ) { ?> class="wp-fullscreen-both"<?php } ?>>
			<a title="<?php echo $args['title']; ?>" onclick="<?php echo $args['onclick']; ?>return false;" class="mceButton mceButtonEnabled mce_<?php echo $button; ?>" href="#" id="wp_fs_<?php echo $button; ?>" role="button" aria-pressed="false">
			<span class="mceIcon mce_<?php echo $button; ?>"></span>
			</a>
			</div>
	<?php
		} ?>

			</div></div>

			<div id="wp-fullscreen-save">
				<span><?php if ( $post->post_status == 'publish' ) _e('Updated.'); else _e('Saved.'); ?></span>
				<img src="images/wpspin_light.gif" alt="" />
				<input type="button" class="button-primary" value="<?php echo $save; ?>" onclick="fullscreen.save();" />
			</div>

			</div>
		</div>
	</div>

	<div id="wp-fullscreen-wrap" style="width:<?php echo $dfw_width; ?>px;">
		<?php if ( post_type_supports($post->post_type, 'title') ) { ?>
		<label id="wp-fullscreen-title-prompt-text" for="wp-fullscreen-title"><?php echo apply_filters( 'enter_title_here', __( 'Enter title here' ), $post ); ?></label>
		<input type="text" id="wp-fullscreen-title" value="" autocomplete="off" />
		<?php } ?>

		<div id="wp-fullscreen-container">
			<textarea id="wp_mce_fullscreen"></textarea>
		</div>

		<div id="wp-fullscreen-status">
			<div id="wp-fullscreen-count"><?php printf( __( 'Word count: %s' ), '<span class="word-count">0</span>' ); ?></div>
			<div id="wp-fullscreen-tagline"><?php _e('Just write.'); ?></div>
		</div>
	</div>
	</div>

	<div class="fullscreen-overlay" id="fullscreen-overlay"></div>
	<div class="fullscreen-overlay fullscreen-fader fade-600" id="fullscreen-fader"></div>
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
	function wp_link_query( $args = array() ) {
		$pts = get_post_types( array( 'public' => true ), 'objects' );
		$pt_names = array_keys( $pts );

		$query = array(
			'post_type' => $pt_names,
			'suppress_filters' => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'post_status' => 'publish',
			'order' => 'DESC',
			'orderby' => 'post_date',
			'posts_per_page' => 20,
		);

		$args['pagenum'] = isset( $args['pagenum'] ) ? absint( $args['pagenum'] ) : 1;

		if ( isset( $args['s'] ) )
			$query['s'] = $args['s'];

		$query['offset'] = $args['pagenum'] > 1 ? $query['posts_per_page'] * ( $args['pagenum'] - 1 ) : 0;

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

		return $results;
	}

	/**
	 * Dialog for internal linking.
	 *
	 * @since 3.1.0
	 */
	function wp_link_dialog() {
	?>
	<div style="display:none;">
	<form id="wp-link" tabindex="-1">
	<?php wp_nonce_field( 'internal-linking', '_ajax_linking_nonce', false ); ?>
	<div id="link-selector">
		<div id="link-options">
			<p class="howto"><?php _e( 'Enter the destination URL' ); ?></p>
			<div>
				<label><span><?php _e( 'URL' ); ?></span><input id="url-field" type="text" tabindex="10" name="href" /></label>
			</div>
			<div>
				<label><span><?php _e( 'Title' ); ?></span><input id="link-title-field" type="text" tabindex="20" name="linktitle" /></label>
			</div>
			<div class="link-target">
				<label><input type="checkbox" id="link-target-checkbox" tabindex="30" /> <?php _e( 'Open link in a new window/tab' ); ?></label>
			</div>
		</div>
		<?php $show_internal = '1' == get_user_setting( 'wplink', '0' ); ?>
		<p class="howto toggle-arrow <?php if ( $show_internal ) echo 'toggle-arrow-active'; ?>" id="internal-toggle"><?php _e( 'Or link to existing content' ); ?></p>
		<div id="search-panel"<?php if ( ! $show_internal ) echo ' style="display:none"'; ?>>
			<div class="link-search-wrapper">
				<label>
					<span><?php _e( 'Search' ); ?></span>
					<input type="text" id="search-field" class="link-search-field" tabindex="60" autocomplete="off" />
					<img class="waiting" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />
				</label>
			</div>
			<div id="search-results" class="query-results">
				<ul></ul>
				<div class="river-waiting">
					<img class="waiting" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />
				</div>
			</div>
			<div id="most-recent-results" class="query-results">
				<div class="query-notice"><em><?php _e( 'No search term specified. Showing recent items.' ); ?></em></div>
				<ul></ul>
				<div class="river-waiting">
					<img class="waiting" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />
				</div>
			</div>
		</div>
	</div>
	<div class="submitbox">
		<div id="wp-link-cancel">
			<a class="submitdelete deletion" href="#"><?php _e( 'Cancel' ); ?></a>
		</div>
		<div id="wp-link-update">
			<input type="submit" tabindex="100" value="<?php esc_attr_e( 'Add Link' ); ?>" class="button-primary" id="wp-link-submit" name="wp-link-submit">
		</div>
	</div>
	</form>
	</div>
	<?php
	}
}

