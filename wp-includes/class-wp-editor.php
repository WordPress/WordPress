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
	private static $drag_drop_upload = false;
	private static $old_dfw_compat = false;
	private static $translation;
	private static $tinymce_scripts_printed = false;
	private static $link_dialog_printed = false;

	private function __construct() {}

	/**
	 * Parse default arguments for the editor instance.
	 *
	 * @static
	 * @param string $editor_id ID for the current editor instance.
	 * @param array  $settings {
	 *     Array of editor arguments.
	 *
	 *     @type bool       $wpautop           Whether to use wpautop(). Default true.
	 *     @type bool       $media_buttons     Whether to show the Add Media/other media buttons.
	 *     @type string     $default_editor    When both TinyMCE and Quicktags are used, set which
	 *                                         editor is shown on page load. Default empty.
	 *     @type bool       $drag_drop_upload  Whether to enable drag & drop on the editor uploading. Default false.
	 *                                         Requires the media modal.
	 *     @type string     $textarea_name     Give the textarea a unique name here. Square brackets
	 *                                         can be used here. Default $editor_id.
	 *     @type int        $textarea_rows     Number rows in the editor textarea. Default 20.
	 *     @type string|int $tabindex          Tabindex value to use. Default empty.
	 *     @type string     $tabfocus_elements The previous and next element ID to move the focus to
	 *                                         when pressing the Tab key in TinyMCE. Default ':prev,:next'.
	 *     @type string     $editor_css        Intended for extra styles for both Visual and Text editors.
	 *                                         Should include `<style>` tags, and can use "scoped". Default empty.
	 *     @type string     $editor_class      Extra classes to add to the editor textarea element. Default empty.
	 *     @type bool       $teeny             Whether to output the minimal editor config. Examples include
	 *                                         Press This and the Comment editor. Default false.
	 *     @type bool       $dfw               Deprecated in 4.1. Since 4.3 used only to enqueue wp-fullscreen-stub.js
	 *                                         for backward compatibility.
	 *     @type bool|array $tinymce           Whether to load TinyMCE. Can be used to pass settings directly to
	 *                                         TinyMCE using an array. Default true.
	 *     @type bool|array $quicktags         Whether to load Quicktags. Can be used to pass settings directly to
	 *                                         Quicktags using an array. Default true.
	 * }
	 * @return array Parsed arguments array.
	 */
	public static function parse_settings( $editor_id, $settings ) {

		/**
		 * Filters the wp_editor() settings.
		 *
		 * @since 4.0.0
		 *
		 * @see _WP_Editors()::parse_settings()
		 *
		 * @param array  $settings  Array of editor arguments.
		 * @param string $editor_id ID for the current editor instance.
		 */
		$settings = apply_filters( 'wp_editor_settings', $settings, $editor_id );

		$set = wp_parse_args( $settings, array(
			'wpautop'             => true,
			'media_buttons'       => true,
			'default_editor'      => '',
			'drag_drop_upload'    => false,
			'textarea_name'       => $editor_id,
			'textarea_rows'       => 20,
			'tabindex'            => '',
			'tabfocus_elements'   => ':prev,:next',
			'editor_css'          => '',
			'editor_class'        => '',
			'teeny'               => false,
			'dfw'                 => false,
			'_content_editor_dfw' => false,
			'tinymce'             => true,
			'quicktags'           => true
		) );

		self::$this_tinymce = ( $set['tinymce'] && user_can_richedit() );

		if ( self::$this_tinymce ) {
			if ( false !== strpos( $editor_id, '[' ) ) {
				self::$this_tinymce = false;
				_deprecated_argument( 'wp_editor()', '3.9.0', 'TinyMCE editor IDs cannot have brackets.' );
			}
		}

		self::$this_quicktags = (bool) $set['quicktags'];

		if ( self::$this_tinymce )
			self::$has_tinymce = true;

		if ( self::$this_quicktags )
			self::$has_quicktags = true;

		if ( $set['dfw'] ) {
			self::$old_dfw_compat = true;
		}

		if ( empty( $set['editor_height'] ) )
			return $set;

		if ( 'content' === $editor_id && empty( $set['tinymce']['wp_autoresize_on'] ) ) {
			// A cookie (set when a user resizes the editor) overrides the height.
			$cookie = (int) get_user_setting( 'ed_size' );

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
	 * @static
	 * @param string $content The initial content of the editor.
	 * @param string $editor_id ID for the textarea and TinyMCE and Quicktags instances (can contain only ASCII letters and numbers).
	 * @param array $settings See _WP_Editors()::parse_settings() for description.
	 */
	public static function editor( $content, $editor_id, $settings = array() ) {
		$set = self::parse_settings( $editor_id, $settings );
		$editor_class = ' class="' . trim( esc_attr( $set['editor_class'] ) . ' wp-editor-area' ) . '"';
		$tabindex = $set['tabindex'] ? ' tabindex="' . (int) $set['tabindex'] . '"' : '';
		$default_editor = 'html';
		$buttons = $autocomplete = '';
		$editor_id_attr = esc_attr( $editor_id );

		if ( $set['drag_drop_upload'] ) {
			self::$drag_drop_upload = true;
		}

		if ( ! empty( $set['editor_height'] ) ) {
			$height = ' style="height: ' . (int) $set['editor_height'] . 'px"';
		} else {
			$height = ' rows="' . (int) $set['textarea_rows'] . '"';
		}

		if ( ! current_user_can( 'upload_files' ) ) {
			$set['media_buttons'] = false;
		}

		if ( self::$this_tinymce ) {
			$autocomplete = ' autocomplete="off"';

			if ( self::$this_quicktags ) {
				$default_editor = $set['default_editor'] ? $set['default_editor'] : wp_default_editor();
				// 'html' is used for the "Text" editor tab.
				if ( 'html' !== $default_editor ) {
					$default_editor = 'tinymce';
				}

				$buttons .= '<button type="button" id="' . $editor_id_attr . '-tmce" class="wp-switch-editor switch-tmce"' .
					' data-wp-editor-id="' . $editor_id_attr . '">' . _x( 'Visual', 'Name for the Visual editor tab' ) . "</button>\n";
				$buttons .= '<button type="button" id="' . $editor_id_attr . '-html" class="wp-switch-editor switch-html"' .
					' data-wp-editor-id="' . $editor_id_attr . '">' . _x( 'Text', 'Name for the Text editor tab (formerly HTML)' ) . "</button>\n";
			} else {
				$default_editor = 'tinymce';
			}
		}

		$switch_class = 'html' === $default_editor ? 'html-active' : 'tmce-active';
		$wrap_class = 'wp-core-ui wp-editor-wrap ' . $switch_class;

		if ( $set['_content_editor_dfw'] ) {
			$wrap_class .= ' has-dfw';
		}

		echo '<div id="wp-' . $editor_id_attr . '-wrap" class="' . $wrap_class . '">';

		if ( self::$editor_buttons_css ) {
			wp_print_styles( 'editor-buttons' );
			self::$editor_buttons_css = false;
		}

		if ( ! empty( $set['editor_css'] ) ) {
			echo $set['editor_css'] . "\n";
		}

		if ( ! empty( $buttons ) || $set['media_buttons'] ) {
			echo '<div id="wp-' . $editor_id_attr . '-editor-tools" class="wp-editor-tools hide-if-no-js">';

			if ( $set['media_buttons'] ) {
				self::$has_medialib = true;

				if ( ! function_exists( 'media_buttons' ) )
					include( ABSPATH . 'wp-admin/includes/media.php' );

				echo '<div id="wp-' . $editor_id_attr . '-media-buttons" class="wp-media-buttons">';

				/**
				 * Fires after the default media button(s) are displayed.
				 *
				 * @since 2.5.0
				 *
				 * @param string $editor_id Unique editor identifier, e.g. 'content'.
				 */
				do_action( 'media_buttons', $editor_id );
				echo "</div>\n";
			}

			echo '<div class="wp-editor-tabs">' . $buttons . "</div>\n";
			echo "</div>\n";
		}

		$quicktags_toolbar = '';

		if ( self::$this_quicktags ) {
			if ( 'content' === $editor_id && ! empty( $GLOBALS['current_screen'] ) && $GLOBALS['current_screen']->base === 'post' ) {
				$toolbar_id = 'ed_toolbar';
			} else {
				$toolbar_id = 'qt_' . $editor_id_attr . '_toolbar';
			}

			$quicktags_toolbar = '<div id="' . $toolbar_id . '" class="quicktags-toolbar"></div>';
		}

		/**
		 * Filters the HTML markup output that displays the editor.
		 *
		 * @since 2.1.0
		 *
		 * @param string $output Editor's HTML markup.
		 */
		$the_editor = apply_filters( 'the_editor', '<div id="wp-' . $editor_id_attr . '-editor-container" class="wp-editor-container">' .
			$quicktags_toolbar .
			'<textarea' . $editor_class . $height . $tabindex . $autocomplete . ' cols="40" name="' . esc_attr( $set['textarea_name'] ) . '" ' .
			'id="' . $editor_id_attr . '">%s</textarea></div>' );

		// Prepare the content for the Visual or Text editor, only when TinyMCE is used (back-compat).
		if ( self::$this_tinymce ) {
			add_filter( 'the_editor_content', 'format_for_editor', 10, 2 );
		}

		/**
		 * Filters the default editor content.
		 *
		 * @since 2.1.0
		 *
		 * @param string $content        Default editor content.
		 * @param string $default_editor The default editor for the current user.
		 *                               Either 'html' or 'tinymce'.
		 */
		$content = apply_filters( 'the_editor_content', $content, $default_editor );

		// Remove the filter as the next editor on the same page may not need it.
		if ( self::$this_tinymce ) {
			remove_filter( 'the_editor_content', 'format_for_editor' );
		}

		// Back-compat for the `htmledit_pre` and `richedit_pre` filters
		if ( 'html' === $default_editor && has_filter( 'htmledit_pre' ) ) {
			/** This filter is documented in wp-includes/deprecated.php */
			$content = apply_filters_deprecated( 'htmledit_pre', array( $content ), '4.3.0', 'format_for_editor' );
		} elseif ( 'tinymce' === $default_editor && has_filter( 'richedit_pre' ) ) {
			/** This filter is documented in wp-includes/deprecated.php */
			$content = apply_filters_deprecated( 'richedit_pre', array( $content ), '4.3.0', 'format_for_editor' );
		}

		if ( false !== stripos( $content, 'textarea' ) ) {
			$content = preg_replace( '%</textarea%i', '&lt;/textarea', $content );
		}

		printf( $the_editor, $content );
		echo "\n</div>\n\n";

		self::editor_settings( $editor_id, $set );
	}

	/**
	 * @static
	 *
	 * @global string $tinymce_version
	 *
	 * @param string $editor_id
	 * @param array  $set
	 */
	public static function editor_settings($editor_id, $set) {
		global $tinymce_version;

		if ( empty(self::$first_init) ) {
			if ( is_admin() ) {
				add_action( 'admin_print_footer_scripts', array( __CLASS__, 'editor_js' ), 50 );
				add_action( 'admin_print_footer_scripts', array( __CLASS__, 'enqueue_scripts' ), 1 );
			} else {
				add_action( 'wp_print_footer_scripts', array( __CLASS__, 'editor_js' ), 50 );
				add_action( 'wp_print_footer_scripts', array( __CLASS__, 'enqueue_scripts' ), 1 );
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

			if ( $set['_content_editor_dfw'] ) {
				$qtInit['buttons'] .= ',dfw';
			}

			/**
			 * Filters the Quicktags settings.
			 *
			 * @since 3.3.0
			 *
			 * @param array  $qtInit    Quicktags settings.
			 * @param string $editor_id The unique editor ID, e.g. 'content'.
			 */
			$qtInit = apply_filters( 'quicktags_settings', $qtInit, $editor_id );

			self::$qt_settings[$editor_id] = $qtInit;

			self::$qt_buttons = array_merge( self::$qt_buttons, explode(',', $qtInit['buttons']) );
		}

		if ( self::$this_tinymce ) {

			if ( empty( self::$first_init ) ) {
				$baseurl = self::get_baseurl();
				$mce_locale = self::get_mce_locale();
				$ext_plugins = '';

				if ( $set['teeny'] ) {

					/**
					 * Filters the list of teenyMCE plugins.
					 *
					 * @since 2.7.0
					 *
					 * @param array  $plugins   An array of teenyMCE plugins.
					 * @param string $editor_id Unique editor identifier, e.g. 'content'.
					 */
					$plugins = apply_filters( 'teeny_mce_plugins', array( 'colorpicker', 'lists', 'fullscreen', 'image', 'wordpress', 'wpeditimage', 'wplink' ), $editor_id );
				} else {

					/**
					 * Filters the list of TinyMCE external plugins.
					 *
					 * The filter takes an associative array of external plugins for
					 * TinyMCE in the form 'plugin_name' => 'url'.
					 *
					 * The url should be absolute, and should include the js filename
					 * to be loaded. For example:
					 * 'myplugin' => 'http://mysite.com/wp-content/plugins/myfolder/mce_plugin.js'.
					 *
					 * If the external plugin adds a button, it should be added with
					 * one of the 'mce_buttons' filters.
					 *
					 * @since 2.5.0
					 *
					 * @param array $external_plugins An array of external TinyMCE plugins.
					 */
					$mce_external_plugins = apply_filters( 'mce_external_plugins', array() );

					$plugins = array(
						'charmap',
						'colorpicker',
						'hr',
						'lists',
						'media',
						'paste',
						'tabfocus',
						'textcolor',
						'fullscreen',
						'wordpress',
						'wpautoresize',
						'wpeditimage',
						'wpemoji',
						'wpgallery',
						'wplink',
						'wpdialogs',
						'wptextpattern',
						'wpview',
					);

					if ( ! self::$has_medialib ) {
						$plugins[] = 'image';
					}

					/**
					 * Filters the list of default TinyMCE plugins.
					 *
					 * The filter specifies which of the default plugins included
					 * in WordPress should be added to the TinyMCE instance.
					 *
					 * @since 3.3.0
					 *
					 * @param array $plugins An array of default TinyMCE plugins.
					 */
					$plugins = array_unique( apply_filters( 'tiny_mce_plugins', $plugins ) );

					if ( ( $key = array_search( 'spellchecker', $plugins ) ) !== false ) {
						// Remove 'spellchecker' from the internal plugins if added with 'tiny_mce_plugins' filter to prevent errors.
						// It can be added with 'mce_external_plugins'.
						unset( $plugins[$key] );
					}

					if ( ! empty( $mce_external_plugins ) ) {

						/**
						 * Filters the translations loaded for external TinyMCE 3.x plugins.
						 *
						 * The filter takes an associative array ('plugin_name' => 'path')
						 * where 'path' is the include path to the file.
						 *
						 * The language file should follow the same format as wp_mce_translation(),
						 * and should define a variable ($strings) that holds all translated strings.
						 *
						 * @since 2.5.0
						 *
						 * @param array $translations Translations for external TinyMCE plugins.
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
							$strings = '';

							// Try to load langs/[locale].js and langs/[locale]_dlg.js
							if ( ! in_array( $name, $loaded_langs, true ) ) {
								$path = str_replace( content_url(), '', $plugurl );
								$path = WP_CONTENT_DIR . $path . '/langs/';

								if ( function_exists('realpath') )
									$path = trailingslashit( realpath($path) );

								if ( @is_file( $path . $mce_locale . '.js' ) )
									$strings .= @file_get_contents( $path . $mce_locale . '.js' ) . "\n";

								if ( @is_file( $path . $mce_locale . '_dlg.js' ) )
									$strings .= @file_get_contents( $path . $mce_locale . '_dlg.js' ) . "\n";

								if ( 'en' != $mce_locale && empty( $strings ) ) {
									if ( @is_file( $path . 'en.js' ) ) {
										$str1 = @file_get_contents( $path . 'en.js' );
										$strings .= preg_replace( '/([\'"])en\./', '$1' . $mce_locale . '.', $str1, 1 ) . "\n";
									}

									if ( @is_file( $path . 'en_dlg.js' ) ) {
										$str2 = @file_get_contents( $path . 'en_dlg.js' );
										$strings .= preg_replace( '/([\'"])en\./', '$1' . $mce_locale . '.', $str2, 1 ) . "\n";
									}
								}

								if ( ! empty( $strings ) )
									$ext_plugins .= "\n" . $strings . "\n";
							}

							$ext_plugins .= 'tinyMCEPreInit.load_ext("' . $plugurl . '", "' . $mce_locale . '");' . "\n";
						}
					}
				}

				self::$plugins = $plugins;
				self::$ext_plugins = $ext_plugins;

				$settings = self::default_settings();
				$settings['plugins'] = implode( ',', $plugins );

				if ( ! empty( $mce_external_plugins ) ) {
					$settings['external_plugins'] = wp_json_encode( $mce_external_plugins );
				}

				/** This filter is documented in wp-admin/includes/media.php */
				if ( apply_filters( 'disable_captions', '' ) ) {
					$settings['wpeditimage_disable_captions'] = true;
				}

				$mce_css = $settings['content_css'];
				$editor_styles = get_editor_stylesheets();

				if ( ! empty( $editor_styles ) ) {
					// Force urlencoding of commas.
					foreach ( $editor_styles as $key => $url ) {
						if ( strpos( $url, ',' ) !== false ) {
							$editor_styles[ $key ] = str_replace( ',', '%2C', $url );
						}
					}

					$mce_css .= ',' . implode( ',', $editor_styles );
				}

				/**
				 * Filters the comma-delimited list of stylesheets to load in TinyMCE.
				 *
				 * @since 2.1.0
				 *
				 * @param string $stylesheets Comma-delimited list of stylesheets.
				 */
				$mce_css = trim( apply_filters( 'mce_css', $mce_css ), ' ,' );

				if ( ! empty( $mce_css ) ) {
					$settings['content_css'] = $mce_css;
				} else {
					unset( $settings['content_css'] );
				}

				self::$first_init = $settings;
			}

			if ( $set['teeny'] ) {

				/**
				 * Filters the list of teenyMCE buttons (Text tab).
				 *
				 * @since 2.7.0
				 *
				 * @param array  $buttons   An array of teenyMCE buttons.
				 * @param string $editor_id Unique editor identifier, e.g. 'content'.
				 */
				$mce_buttons = apply_filters( 'teeny_mce_buttons', array('bold', 'italic', 'underline', 'blockquote', 'strikethrough', 'bullist', 'numlist', 'alignleft', 'aligncenter', 'alignright', 'undo', 'redo', 'link', 'fullscreen'), $editor_id );
				$mce_buttons_2 = $mce_buttons_3 = $mce_buttons_4 = array();
			} else {
				$mce_buttons = array( 'formatselect', 'bold', 'italic', 'bullist', 'numlist', 'blockquote', 'alignleft', 'aligncenter', 'alignright', 'link', 'wp_more', 'spellchecker' );

				if ( ! wp_is_mobile() ) {
					if ( $set['_content_editor_dfw'] ) {
						$mce_buttons[] = 'dfw';
					} else {
						$mce_buttons[] = 'fullscreen';
					}
				}

				$mce_buttons[] = 'wp_adv';

				/**
				 * Filters the first-row list of TinyMCE buttons (Visual tab).
				 *
				 * @since 2.0.0
				 *
				 * @param array  $buttons   First-row list of buttons.
				 * @param string $editor_id Unique editor identifier, e.g. 'content'.
				 */
				$mce_buttons = apply_filters( 'mce_buttons', $mce_buttons, $editor_id );

				$mce_buttons_2 = array( 'strikethrough', 'hr', 'forecolor', 'pastetext', 'removeformat', 'charmap', 'outdent', 'indent', 'undo', 'redo' );

				if ( ! wp_is_mobile() ) {
					$mce_buttons_2[] = 'wp_help';
				}

				/**
				 * Filters the second-row list of TinyMCE buttons (Visual tab).
				 *
				 * @since 2.0.0
				 *
				 * @param array  $buttons   Second-row list of buttons.
				 * @param string $editor_id Unique editor identifier, e.g. 'content'.
				 */
				$mce_buttons_2 = apply_filters( 'mce_buttons_2', $mce_buttons_2, $editor_id );

				/**
				 * Filters the third-row list of TinyMCE buttons (Visual tab).
				 *
				 * @since 2.0.0
				 *
				 * @param array  $buttons   Third-row list of buttons.
				 * @param string $editor_id Unique editor identifier, e.g. 'content'.
				 */
				$mce_buttons_3 = apply_filters( 'mce_buttons_3', array(), $editor_id );

				/**
				 * Filters the fourth-row list of TinyMCE buttons (Visual tab).
				 *
				 * @since 2.5.0
				 *
				 * @param array  $buttons   Fourth-row list of buttons.
				 * @param string $editor_id Unique editor identifier, e.g. 'content'.
				 */
				$mce_buttons_4 = apply_filters( 'mce_buttons_4', array(), $editor_id );
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

				$page_template = get_page_template_slug( $post );

				if ( $page_template !== false ) {
					$page_template = empty( $page_template ) ? 'default' : str_replace( '.', '-', basename( $page_template, '.php' ) );
					$body_class .= ' page-template-' . sanitize_html_class( $page_template );
				}
			}

			$body_class .= ' locale-' . sanitize_html_class( strtolower( str_replace( '_', '-', get_user_locale() ) ) );

			if ( ! empty( $set['tinymce']['body_class'] ) ) {
				$body_class .= ' ' . $set['tinymce']['body_class'];
				unset( $set['tinymce']['body_class'] );
			}

			$mceInit = array (
				'selector' => "#$editor_id",
				'wpautop' => (bool) $set['wpautop'],
				'indent' => ! $set['wpautop'],
				'toolbar1' => implode( ',', $mce_buttons ),
				'toolbar2' => implode( ',', $mce_buttons_2 ),
				'toolbar3' => implode( ',', $mce_buttons_3 ),
				'toolbar4' => implode( ',', $mce_buttons_4 ),
				'tabfocus_elements' => $set['tabfocus_elements'],
				'body_class' => $body_class
			);

			// Merge with the first part of the init array
			$mceInit = array_merge( self::$first_init, $mceInit );

			if ( is_array( $set['tinymce'] ) )
				$mceInit = array_merge( $mceInit, $set['tinymce'] );

			/*
			 * For people who really REALLY know what they're doing with TinyMCE
			 * You can modify $mceInit to add, remove, change elements of the config
			 * before tinyMCE.init. Setting "valid_elements", "invalid_elements"
			 * and "extended_valid_elements" can be done through this filter. Best
			 * is to use the default cleanup by not specifying valid_elements,
			 * as TinyMCE checks against the full set of HTML 5.0 elements and attributes.
			 */
			if ( $set['teeny'] ) {

				/**
				 * Filters the teenyMCE config before init.
				 *
				 * @since 2.7.0
				 *
				 * @param array  $mceInit   An array with teenyMCE config.
				 * @param string $editor_id Unique editor identifier, e.g. 'content'.
				 */
				$mceInit = apply_filters( 'teeny_mce_before_init', $mceInit, $editor_id );
			} else {

				/**
				 * Filters the TinyMCE config before init.
				 *
				 * @since 2.5.0
				 *
				 * @param array  $mceInit   An array with TinyMCE config.
				 * @param string $editor_id Unique editor identifier, e.g. 'content'.
				 */
				$mceInit = apply_filters( 'tiny_mce_before_init', $mceInit, $editor_id );
			}

			if ( empty( $mceInit['toolbar3'] ) && ! empty( $mceInit['toolbar4'] ) ) {
				$mceInit['toolbar3'] = $mceInit['toolbar4'];
				$mceInit['toolbar4'] = '';
			}

			self::$mce_settings[$editor_id] = $mceInit;
		} // end if self::$this_tinymce
	}

	/**
	 *
	 * @static
	 * @param array $init
	 * @return string
	 */
	private static function _parse_init( $init ) {
		$options = '';

		foreach ( $init as $key => $value ) {
			if ( is_bool( $value ) ) {
				$val = $value ? 'true' : 'false';
				$options .= $key . ':' . $val . ',';
				continue;
			} elseif ( ! empty( $value ) && is_string( $value ) && (
				( '{' == $value{0} && '}' == $value{strlen( $value ) - 1} ) ||
				( '[' == $value{0} && ']' == $value{strlen( $value ) - 1} ) ||
				preg_match( '/^\(?function ?\(/', $value ) ) ) {

				$options .= $key . ':' . $value . ',';
				continue;
			}
			$options .= $key . ':"' . $value . '",';
		}

		return '{' . trim( $options, ' ,' ) . '}';
	}

	/**
	 *
	 * @static
	 * 
	 * @param bool $default_scripts Optional. Whether default scripts should be enqueued. Default false.
	 */
	public static function enqueue_scripts( $default_scripts = false ) {
		if ( $default_scripts || self::$has_tinymce ) {
			wp_enqueue_script( 'editor' );
		}

		if ( $default_scripts || self::$has_quicktags ) {
			wp_enqueue_script( 'quicktags' );
			wp_enqueue_style( 'buttons' );
		}

		if ( $default_scripts || in_array( 'wplink', self::$plugins, true ) || in_array( 'link', self::$qt_buttons, true ) ) {
			wp_enqueue_script( 'wplink' );
			wp_enqueue_script( 'jquery-ui-autocomplete' );
		}

		if ( self::$old_dfw_compat ) {
			wp_enqueue_script( 'wp-fullscreen-stub' );
		}

		if ( self::$has_medialib ) {
			add_thickbox();
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_script( 'wp-embed' );
		} elseif ( $default_scripts ) {
			wp_enqueue_script( 'media-upload' );
		}

		/**
		 * Fires when scripts and styles are enqueued for the editor.
		 *
		 * @since 3.9.0
		 *
		 * @param array $to_load An array containing boolean values whether TinyMCE
		 *                       and Quicktags are being loaded.
		 */
		do_action( 'wp_enqueue_editor', array(
			'tinymce'   => ( $default_scripts || self::$has_tinymce ),
			'quicktags' => ( $default_scripts || self::$has_quicktags ),
		) );
	}

	/**
	 * Enqueue all editor scripts.
	 * For use when the editor is going to be initialized after page load.
	 *
	 * @since 4.8.0
	 */
	public static function enqueue_default_editor() {
		// We are past the point where scripts can be enqueued properly.
		if ( did_action( 'wp_enqueue_editor' ) ) {
			return;
		}

		self::enqueue_scripts( true );

		// Also add wp-includes/css/editor.css
		wp_enqueue_style( 'editor-buttons' );

		if ( is_admin() ) {
			add_action( 'admin_print_footer_scripts', array( __CLASS__, 'print_default_editor_scripts' ), 45 );
		} else {
			add_action( 'wp_print_footer_scripts', array( __CLASS__, 'print_default_editor_scripts' ), 45 );
		}
	}

	/**
	 * Print (output) all editor scripts and default settings.
	 * For use when the editor is going to be initialized after page load.
	 *
	 * @since 4.8.0
	 *
	 */
	public static function print_default_editor_scripts() {
		$user_can_richedit = user_can_richedit();

		if ( $user_can_richedit ) {
			$settings = self::default_settings();

			$settings['toolbar1'] = 'bold,italic,bullist,numlist,link';
			$settings['wpautop'] = false;
			$settings['indent'] = true;
			$settings['elementpath'] = false;

			if ( is_rtl() ) {
				$settings['directionality'] = 'rtl';
			}

			// In production all plugins are loaded (they are in wp-editor.js.gz).
			// The 'wpview', 'wpdialogs', and 'media' TinyMCE plugins are not initialized by default.
			// Can be added from js by using the 'wp-before-tinymce-init' event.
			$settings['plugins'] = implode( ',', array(
				'charmap',
				'colorpicker',
				'hr',
				'lists',
				'paste',
				'tabfocus',
				'textcolor',
				'fullscreen',
				'wordpress',
				'wpautoresize',
				'wpeditimage',
				'wpemoji',
				'wpgallery',
				'wplink',
				'wptextpattern',
			) );

			$settings = self::_parse_init( $settings );
		} else {
			$settings = '{}';
		}

		?>
		<script type="text/javascript">
		window.wp = window.wp || {};
		window.wp.editor = window.wp.editor || {};
		window.wp.editor.getDefaultSettings = function() {
			return {
				tinymce: <?php echo $settings; ?>,
				quicktags: {
					buttons: 'strong,em,link,ul,ol,li,code'
				}
			};
		};

		<?php

		if ( $user_can_richedit ) {
			$suffix = SCRIPT_DEBUG ? '' : '.min';
			$baseurl = self::get_baseurl();

			?>
			var tinyMCEPreInit = {
				baseURL: "<?php echo $baseurl; ?>",
				suffix: "<?php echo $suffix; ?>",
				mceInit: {},
				qtInit: {},
				load_ext: function(url,lang){var sl=tinymce.ScriptLoader;sl.markDone(url+'/langs/'+lang+'.js');sl.markDone(url+'/langs/'+lang+'_dlg.js');}
			};
			<?php
		}
		?>
		</script>
		<?php

		if ( $user_can_richedit ) {
			self::print_tinymce_scripts();
		}

		/**
		 * Fires when the editor scripts are loaded for later initialization,
		 * after all scripts and settings are printed.
		 *
		 * @since 4.8.0
		 */
		do_action( 'print_default_editor_scripts' );

		self::wp_link_dialog();
	}

	public static function get_mce_locale() {
		if ( empty( self::$mce_locale ) ) {
			$mce_locale = get_user_locale();
			self::$mce_locale = empty( $mce_locale ) ? 'en' : strtolower( substr( $mce_locale, 0, 2 ) ); // ISO 639-1
		}

		return self::$mce_locale;
	}

	public static function get_baseurl() {
		if ( empty( self::$baseurl ) ) {
			self::$baseurl = includes_url( 'js/tinymce' );
		}

		return self::$baseurl;
	}

	/**
	 * Returns the default TinyMCE settings.
	 * Doesn't include plugins, buttons, editor selector.
	 *
	 * @global string $tinymce_version
	 *
	 * @return array
	 */
	private static function default_settings() {
		global $tinymce_version;

		$shortcut_labels = array();

		foreach ( self::get_translation() as $name => $value ) {
			if ( is_array( $value ) ) {
				$shortcut_labels[$name] = $value[1];
			}
		}

		$settings = array(
			'theme' => 'modern',
			'skin' => 'lightgray',
			'language' => self::get_mce_locale(),
			'formats' => '{' .
				'alignleft: [' .
					'{selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li", styles: {textAlign:"left"}},' .
					'{selector: "img,table,dl.wp-caption", classes: "alignleft"}' .
				'],' .
				'aligncenter: [' .
					'{selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li", styles: {textAlign:"center"}},' .
					'{selector: "img,table,dl.wp-caption", classes: "aligncenter"}' .
				'],' .
				'alignright: [' .
					'{selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li", styles: {textAlign:"right"}},' .
					'{selector: "img,table,dl.wp-caption", classes: "alignright"}' .
				'],' .
				'strikethrough: {inline: "del"}' .
			'}',
			'relative_urls' => false,
			'remove_script_host' => false,
			'convert_urls' => false,
			'browser_spellcheck' => true,
			'fix_list_elements' => true,
			'entities' => '38,amp,60,lt,62,gt',
			'entity_encoding' => 'raw',
			'keep_styles' => false,
			'cache_suffix' => 'wp-mce-' . $tinymce_version,
			'resize' => 'vertical',
			'menubar' => false,
			'branding' => false,

			// Limit the preview styles in the menu/toolbar
			'preview_styles' => 'font-family font-size font-weight font-style text-decoration text-transform',

			'end_container_on_empty_block' => true,
			'wpeditimage_html5_captions' => true,
			'wp_lang_attr' => get_bloginfo( 'language' ),
			'wp_keep_scroll_position' => false,
			'wp_shortcut_labels' => wp_json_encode( $shortcut_labels ),
		);

		$suffix = SCRIPT_DEBUG ? '' : '.min';
		$version = 'ver=' . get_bloginfo( 'version' );

		// Default stylesheets
		$settings['content_css'] = includes_url( "css/dashicons$suffix.css?$version" ) . ',' .
			includes_url( "js/tinymce/skins/wordpress/wp-content.css?$version" );

		return $settings;
	}

	private static function get_translation() {
		if ( empty( self::$translation ) ) {
			self::$translation = array(
			// Default TinyMCE strings
			'New document' => __( 'New document' ),
			'Formats' => _x( 'Formats', 'TinyMCE' ),

			'Headings' => _x( 'Headings', 'TinyMCE' ),
			'Heading 1' => array( __( 'Heading 1' ), 'access1' ),
			'Heading 2' => array( __( 'Heading 2' ), 'access2' ),
			'Heading 3' => array( __( 'Heading 3' ), 'access3' ),
			'Heading 4' => array( __( 'Heading 4' ), 'access4' ),
			'Heading 5' => array( __( 'Heading 5' ), 'access5' ),
			'Heading 6' => array( __( 'Heading 6' ), 'access6' ),

			/* translators: block tags */
			'Blocks' => _x( 'Blocks', 'TinyMCE' ),
			'Paragraph' => array( __( 'Paragraph' ), 'access7' ),
			'Blockquote' => array( __( 'Blockquote' ), 'accessQ' ),
			'Div' => _x( 'Div', 'HTML tag' ),
			'Pre' => _x( 'Pre', 'HTML tag' ),
			'Preformatted' => _x( 'Preformatted', 'HTML tag' ),
			'Address' => _x( 'Address', 'HTML tag' ),

			'Inline' => _x( 'Inline', 'HTML elements' ),
			'Underline' => array( __( 'Underline' ), 'metaU' ),
			'Strikethrough' => array( __( 'Strikethrough' ), 'accessD' ),
			'Subscript' => __( 'Subscript' ),
			'Superscript' => __( 'Superscript' ),
			'Clear formatting' => __( 'Clear formatting' ),
			'Bold' => array( __( 'Bold' ), 'metaB' ),
			'Italic' => array( __( 'Italic' ), 'metaI' ),
			'Code' => array( __( 'Code' ), 'accessX' ),
			'Source code' => __( 'Source code' ),
			'Font Family' => __( 'Font Family' ),
			'Font Sizes' => __( 'Font Sizes' ),

			'Align center' => array( __( 'Align center' ), 'accessC' ),
			'Align right' => array( __( 'Align right' ), 'accessR' ),
			'Align left' => array( __( 'Align left' ), 'accessL' ),
			'Justify' => array( __( 'Justify' ), 'accessJ' ),
			'Increase indent' => __( 'Increase indent' ),
			'Decrease indent' => __( 'Decrease indent' ),

			'Cut' => array( __( 'Cut' ), 'metaX' ),
			'Copy' => array( __( 'Copy' ), 'metaC' ),
			'Paste' => array( __( 'Paste' ), 'metaV' ),
			'Select all' => array( __( 'Select all' ), 'metaA' ),
			'Undo' => array( __( 'Undo' ), 'metaZ' ),
			'Redo' => array( __( 'Redo' ), 'metaY' ),

			'Ok' => __( 'OK' ),
			'Cancel' => __( 'Cancel' ),
			'Close' => __( 'Close' ),
			'Visual aids' => __( 'Visual aids' ),

			'Bullet list' => array( __( 'Bulleted list' ), 'accessU' ),
			'Numbered list' => array( __( 'Numbered list' ), 'accessO' ),
			'Square' => _x( 'Square', 'list style' ),
			'Default' => _x( 'Default', 'list style' ),
			'Circle' => _x( 'Circle', 'list style' ),
			'Disc' => _x('Disc', 'list style' ),
			'Lower Greek' => _x( 'Lower Greek', 'list style' ),
			'Lower Alpha' => _x( 'Lower Alpha', 'list style' ),
			'Upper Alpha' => _x( 'Upper Alpha', 'list style' ),
			'Upper Roman' => _x( 'Upper Roman', 'list style' ),
			'Lower Roman' => _x( 'Lower Roman', 'list style' ),

			// Anchor plugin
			'Name' => _x( 'Name', 'Name of link anchor (TinyMCE)' ),
			'Anchor' => _x( 'Anchor', 'Link anchor (TinyMCE)' ),
			'Anchors' => _x( 'Anchors', 'Link anchors (TinyMCE)' ),
			'Id should start with a letter, followed only by letters, numbers, dashes, dots, colons or underscores.' =>
				__( 'Id should start with a letter, followed only by letters, numbers, dashes, dots, colons or underscores.' ),
			'Id' => _x( 'Id', 'Id for link anchor (TinyMCE)' ),

			// Fullpage plugin
			'Document properties' => __( 'Document properties' ),
			'Robots' => __( 'Robots' ),
			'Title' => __( 'Title' ),
			'Keywords' => __( 'Keywords' ),
			'Encoding' => __( 'Encoding' ),
			'Description' => __( 'Description' ),
			'Author' => __( 'Author' ),

			// Media, image plugins
			'Image' => __( 'Image' ),
			'Insert/edit image' => array( __( 'Insert/edit image' ), 'accessM' ),
			'General' => __( 'General' ),
			'Advanced' => __( 'Advanced' ),
			'Source' => __( 'Source' ),
			'Border' => __( 'Border' ),
			'Constrain proportions' => __( 'Constrain proportions' ),
			'Vertical space' => __( 'Vertical space' ),
			'Image description' => __( 'Image description' ),
			'Style' => __( 'Style' ),
			'Dimensions' => __( 'Dimensions' ),
			'Insert image' => __( 'Insert image' ),
			'Date/time' => __( 'Date/time' ),
			'Insert date/time' => __( 'Insert date/time' ),
			'Table of Contents' => __( 'Table of Contents' ),
			'Insert/Edit code sample' => __( 'Insert/edit code sample' ),
			'Language' => __( 'Language' ),
			'Media' => __( 'Media' ),
			'Insert/edit media' => __( 'Insert/edit media' ),
			'Poster' => __( 'Poster' ),
			'Alternative source' => __( 'Alternative source' ),
			'Paste your embed code below:' => __( 'Paste your embed code below:' ),
			'Insert video' => __( 'Insert video' ),
			'Embed' => __( 'Embed' ),

			// Each of these have a corresponding plugin
			'Special character' => __( 'Special character' ),
			'Right to left' => _x( 'Right to left', 'editor button' ),
			'Left to right' => _x( 'Left to right', 'editor button' ),
			'Emoticons' => __( 'Emoticons' ),
			'Nonbreaking space' => __( 'Nonbreaking space' ),
			'Page break' => __( 'Page break' ),
			'Paste as text' => __( 'Paste as text' ),
			'Preview' => __( 'Preview' ),
			'Print' => __( 'Print' ),
			'Save' => __( 'Save' ),
			'Fullscreen' => __( 'Fullscreen' ),
			'Horizontal line' => __( 'Horizontal line' ),
			'Horizontal space' => __( 'Horizontal space' ),
			'Restore last draft' => __( 'Restore last draft' ),
			'Insert/edit link' => array( __( 'Insert/edit link' ), 'metaK' ),
			'Remove link' => array( __( 'Remove link' ), 'accessS' ),

			// Link plugin
			'Link' => __( 'Link' ),
			'Insert link' => __( 'Insert link' ),
			'Insert/edit link' => __( 'Insert/edit link' ),
			'Target' => __( 'Target' ),
			'New window' => __( 'New window' ),
			'Text to display' => __( 'Text to display' ),
			'Url' => __( 'URL' ),
			'The URL you entered seems to be an email address. Do you want to add the required mailto: prefix?' =>
				__( 'The URL you entered seems to be an email address. Do you want to add the required mailto: prefix?' ),
			'The URL you entered seems to be an external link. Do you want to add the required http:// prefix?' =>
				__( 'The URL you entered seems to be an external link. Do you want to add the required http:// prefix?' ),

			'Color' => __( 'Color' ),
			'Custom color' => __( 'Custom color' ),
			'Custom...' => _x( 'Custom...', 'label for custom color' ), // no ellipsis
			'No color' => __( 'No color' ),
			'R' => _x( 'R', 'Short for red in RGB' ),
			'G' => _x( 'G', 'Short for green in RGB' ),
			'B' => _x( 'B', 'Short for blue in RGB' ),

			// Spelling, search/replace plugins
			'Could not find the specified string.' => __( 'Could not find the specified string.' ),
			'Replace' => _x( 'Replace', 'find/replace' ),
			'Next' => _x( 'Next', 'find/replace' ),
			/* translators: previous */
			'Prev' => _x( 'Prev', 'find/replace' ),
			'Whole words' => _x( 'Whole words', 'find/replace' ),
			'Find and replace' => __( 'Find and replace' ),
			'Replace with' => _x('Replace with', 'find/replace' ),
			'Find' => _x( 'Find', 'find/replace' ),
			'Replace all' => _x( 'Replace all', 'find/replace' ),
			'Match case' => __( 'Match case' ),
			'Spellcheck' => __( 'Check Spelling' ),
			'Finish' => _x( 'Finish', 'spellcheck' ),
			'Ignore all' => _x( 'Ignore all', 'spellcheck' ),
			'Ignore' => _x( 'Ignore', 'spellcheck' ),
			'Add to Dictionary' => __( 'Add to Dictionary' ),

			// TinyMCE tables
			'Insert table' => __( 'Insert table' ),
			'Delete table' => __( 'Delete table' ),
			'Table properties' => __( 'Table properties' ),
			'Row properties' => __( 'Table row properties' ),
			'Cell properties' => __( 'Table cell properties' ),
			'Border color' => __( 'Border color' ),

			'Row' => __( 'Row' ),
			'Rows' => __( 'Rows' ),
			'Column' => _x( 'Column', 'table column' ),
			'Cols' => _x( 'Cols', 'table columns' ),
			'Cell' => _x( 'Cell', 'table cell' ),
			'Header cell' => __( 'Header cell' ),
			'Header' => _x( 'Header', 'table header' ),
			'Body' => _x( 'Body', 'table body' ),
			'Footer' => _x( 'Footer', 'table footer' ),

			'Insert row before' => __( 'Insert row before' ),
			'Insert row after' => __( 'Insert row after' ),
			'Insert column before' => __( 'Insert column before' ),
			'Insert column after' => __( 'Insert column after' ),
			'Paste row before' => __( 'Paste table row before' ),
			'Paste row after' => __( 'Paste table row after' ),
			'Delete row' => __( 'Delete row' ),
			'Delete column' => __( 'Delete column' ),
			'Cut row' => __( 'Cut table row' ),
			'Copy row' => __( 'Copy table row' ),
			'Merge cells' => __( 'Merge table cells' ),
			'Split cell' => __( 'Split table cell' ),

			'Height' => __( 'Height' ),
			'Width' => __( 'Width' ),
			'Caption' => __( 'Caption' ),
			'Alignment' => __( 'Alignment' ),
			'H Align' => _x( 'H Align', 'horizontal table cell alignment' ),
			'Left' => __( 'Left' ),
			'Center' => __( 'Center' ),
			'Right' => __( 'Right' ),
			'None' => _x( 'None', 'table cell alignment attribute' ),
			'V Align' => _x( 'V Align', 'vertical table cell alignment' ),
			'Top' => __( 'Top' ),
			'Middle' => __( 'Middle' ),
			'Bottom' => __( 'Bottom' ),

			'Row group' => __( 'Row group' ),
			'Column group' => __( 'Column group' ),
			'Row type' => __( 'Row type' ),
			'Cell type' => __( 'Cell type' ),
			'Cell padding' => __( 'Cell padding' ),
			'Cell spacing' => __( 'Cell spacing' ),
			'Scope' => _x( 'Scope', 'table cell scope attribute' ),

			'Insert template' => _x( 'Insert template', 'TinyMCE' ),
			'Templates' => _x( 'Templates', 'TinyMCE' ),

			'Background color' => __( 'Background color' ),
			'Text color' => __( 'Text color' ),
			'Show blocks' => _x( 'Show blocks', 'editor button' ),
			'Show invisible characters' => __( 'Show invisible characters' ),

			/* translators: word count */
			'Words: {0}' => sprintf( __( 'Words: %s' ), '{0}' ),
			'Paste is now in plain text mode. Contents will now be pasted as plain text until you toggle this option off.' =>
				__( 'Paste is now in plain text mode. Contents will now be pasted as plain text until you toggle this option off.' ) . "\n\n" .
				__( 'If you&#8217;re looking to paste rich content from Microsoft Word, try turning this option off. The editor will clean up text pasted from Word automatically.' ),
			'Rich Text Area. Press ALT-F9 for menu. Press ALT-F10 for toolbar. Press ALT-0 for help' =>
				__( 'Rich Text Area. Press Alt-Shift-H for help.' ),
			'Rich Text Area. Press Control-Option-H for help.' => __( 'Rich Text Area. Press Control-Option-H for help.' ),
			'You have unsaved changes are you sure you want to navigate away?' =>
				__( 'The changes you made will be lost if you navigate away from this page.' ),
			'Your browser doesn\'t support direct access to the clipboard. Please use the Ctrl+X/C/V keyboard shortcuts instead.' =>
				__( 'Your browser does not support direct access to the clipboard. Please use keyboard shortcuts or your browser&#8217;s edit menu instead.' ),

			// TinyMCE menus
			'Insert' => _x( 'Insert', 'TinyMCE menu' ),
			'File' => _x( 'File', 'TinyMCE menu' ),
			'Edit' => _x( 'Edit', 'TinyMCE menu' ),
			'Tools' => _x( 'Tools', 'TinyMCE menu' ),
			'View' => _x( 'View', 'TinyMCE menu' ),
			'Table' => _x( 'Table', 'TinyMCE menu' ),
			'Format' => _x( 'Format', 'TinyMCE menu' ),

			// WordPress strings
			'Toolbar Toggle' => array( __( 'Toolbar Toggle' ), 'accessZ' ),
			'Insert Read More tag' => array( __( 'Insert Read More tag' ), 'accessT' ),
			'Insert Page Break tag' => array( __( 'Insert Page Break tag' ), 'accessP' ),
			'Read more...' => __( 'Read more...' ), // Title on the placeholder inside the editor (no ellipsis)
			'Distraction-free writing mode' => array( __( 'Distraction-free writing mode' ), 'accessW' ),
			'No alignment' => __( 'No alignment' ), // Tooltip for the 'alignnone' button in the image toolbar
			'Remove' => __( 'Remove' ), // Tooltip for the 'remove' button in the image toolbar
			'Edit ' => __( 'Edit' ), // Tooltip for the 'edit' button in the image toolbar
			'Paste URL or type to search' => __( 'Paste URL or type to search' ), // Placeholder for the inline link dialog
			'Apply'  => __( 'Apply' ), // Tooltip for the 'apply' button in the inline link dialog
			'Link options'  => __( 'Link options' ), // Tooltip for the 'link options' button in the inline link dialog
			'Visual' => _x( 'Visual', 'Name for the Visual editor tab' ), // Editor switch tab label
			'Text' => _x( 'Text', 'Name for the Text editor tab (formerly HTML)' ), // Editor switch tab label

			// Shortcuts help modal
			'Keyboard Shortcuts' => array( __( 'Keyboard Shortcuts' ), 'accessH' ),
			'Default shortcuts,' => __( 'Default shortcuts,' ),
			'Additional shortcuts,' => __( 'Additional shortcuts,' ),
			'Focus shortcuts:' => __( 'Focus shortcuts:' ),
			'Inline toolbar (when an image, link or preview is selected)' => __( 'Inline toolbar (when an image, link or preview is selected)' ),
			'Editor menu (when enabled)' => __( 'Editor menu (when enabled)' ),
			'Editor toolbar' => __( 'Editor toolbar' ),
			'Elements path' => __( 'Elements path' ),
			'Ctrl + Alt + letter:' => __( 'Ctrl + Alt + letter:' ),
			'Shift + Alt + letter:' => __( 'Shift + Alt + letter:' ),
			'Cmd + letter:' => __( 'Cmd + letter:' ),
			'Ctrl + letter:' => __( 'Ctrl + letter:' ),
			'Letter' => __( 'Letter' ),
			'Action' => __( 'Action' ),
			'Warning: the link has been inserted but may have errors. Please test it.' => __( 'Warning: the link has been inserted but may have errors. Please test it.' ),
			'To move focus to other buttons use Tab or the arrow keys. To return focus to the editor press Escape or use one of the buttons.' =>
				__( 'To move focus to other buttons use Tab or the arrow keys. To return focus to the editor press Escape or use one of the buttons.' ),
			'When starting a new paragraph with one of these formatting shortcuts followed by a space, the formatting will be applied automatically. Press Backspace or Escape to undo.' =>
				__( 'When starting a new paragraph with one of these formatting shortcuts followed by a space, the formatting will be applied automatically. Press Backspace or Escape to undo.' ),
			'The following formatting shortcuts are replaced when pressing Enter. Press Escape or the Undo button to undo.' =>
				__( 'The following formatting shortcuts are replaced when pressing Enter. Press Escape or the Undo button to undo.' ),
			'The next group of formatting shortcuts are applied as you type or when you insert them around plain text in the same paragraph. Press Escape or the Undo button to undo.' =>
				__( 'The next group of formatting shortcuts are applied as you type or when you insert them around plain text in the same paragraph. Press Escape or the Undo button to undo.' ),
			);
		}

		/*
		Imagetools plugin (not included):
			'Edit image' => __( 'Edit image' ),
			'Image options' => __( 'Image options' ),
			'Back' => __( 'Back' ),
			'Invert' => __( 'Invert' ),
			'Flip horizontally' => __( 'Flip horizontally' ),
			'Flip vertically' => __( 'Flip vertically' ),
			'Crop' => __( 'Crop' ),
			'Orientation' => __( 'Orientation' ),
			'Resize' => __( 'Resize' ),
			'Rotate clockwise' => __( 'Rotate clockwise' ),
			'Rotate counterclockwise' => __( 'Rotate counterclockwise' ),
			'Sharpen' => __( 'Sharpen' ),
			'Brightness' => __( 'Brightness' ),
			'Color levels' => __( 'Color levels' ),
			'Contrast' => __( 'Contrast' ),
			'Gamma' => __( 'Gamma' ),
			'Zoom in' => __( 'Zoom in' ),
			'Zoom out' => __( 'Zoom out' ),
		*/

		return self::$translation;
	}

	/**
	 * Translates the default TinyMCE strings and returns them as JSON encoded object ready to be loaded with tinymce.addI18n(),
	 * or as JS snippet that should run after tinymce.js is loaded.
	 *
	 * @static
	 * @param string $mce_locale The locale used for the editor.
	 * @param bool $json_only optional Whether to include the JavaScript calls to tinymce.addI18n() and tinymce.ScriptLoader.markDone().
	 * @return string Translation object, JSON encoded.
	 */
	public static function wp_mce_translation( $mce_locale = '', $json_only = false ) {
		if ( ! $mce_locale ) {
			$mce_locale = self::get_mce_locale();
		}

		$mce_translation = self::get_translation();

		foreach ( $mce_translation as $name => $value ) {
			if ( is_array( $value ) ) {
				$mce_translation[$name] = $value[0];
			}
		}

		/**
		 * Filters translated strings prepared for TinyMCE.
		 *
		 * @since 3.9.0
		 *
		 * @param array  $mce_translation Key/value pairs of strings.
		 * @param string $mce_locale      Locale.
		 */
		$mce_translation = apply_filters( 'wp_mce_translation', $mce_translation, $mce_locale );

		foreach ( $mce_translation as $key => $value ) {
			// Remove strings that are not translated.
			if ( $key === $value ) {
				unset( $mce_translation[$key] );
				continue;
			}

			if ( false !== strpos( $value, '&' ) ) {
				$mce_translation[$key] = html_entity_decode( $value, ENT_QUOTES, 'UTF-8' );
			}
		}

		// Set direction
		if ( is_rtl() ) {
			$mce_translation['_dir'] = 'rtl';
		}

		if ( $json_only ) {
			return wp_json_encode( $mce_translation );
		}

		$baseurl = self::get_baseurl();

		return "tinymce.addI18n( '$mce_locale', " . wp_json_encode( $mce_translation ) . ");\n" .
			"tinymce.ScriptLoader.markDone( '$baseurl/langs/$mce_locale.js' );\n";
	}

	/**
	 * Print (output) the main TinyMCE scripts.
	 *
	 * @since 4.8.0
	 *
	 * @static
	 * @global string $tinymce_version
	 * @global bool   $concatenate_scripts
	 * @global bool   $compress_scripts
	 */
	public static function print_tinymce_scripts() {
		global $tinymce_version, $concatenate_scripts, $compress_scripts;

		if ( self::$tinymce_scripts_printed ) {
			return;
		}

		self::$tinymce_scripts_printed = true;

		if ( ! isset( $concatenate_scripts ) ) {
			script_concat_settings();
		}

		$suffix = SCRIPT_DEBUG ? '' : '.min';
		$version = 'ver=' . $tinymce_version;
		$baseurl = self::get_baseurl();

		$has_custom_theme = false;
		foreach ( self::$mce_settings as $init ) {
			if ( ! empty( $init['theme_url'] ) ) {
				$has_custom_theme = true;
				break;
			}
		}

		$compressed = $compress_scripts && $concatenate_scripts && isset( $_SERVER['HTTP_ACCEPT_ENCODING'] )
			&& false !== stripos( $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip' ) && ! $has_custom_theme;

		// Load tinymce.js when running from /src, else load wp-tinymce.js.gz (production) or tinymce.min.js (SCRIPT_DEBUG)
		$mce_suffix = false !== strpos( get_bloginfo( 'version' ), '-src' ) ? '' : '.min';

		if ( $compressed ) {
			echo "<script type='text/javascript' src='{$baseurl}/wp-tinymce.php?c=1&amp;$version'></script>\n";
		} else {
			echo "<script type='text/javascript' src='{$baseurl}/tinymce{$mce_suffix}.js?$version'></script>\n";
			echo "<script type='text/javascript' src='{$baseurl}/plugins/compat3x/plugin{$suffix}.js?$version'></script>\n";
		}

		echo "<script type='text/javascript'>\n" . self::wp_mce_translation() . "</script>\n";
	}

	/**
	 * Print (output) the TinyMCE configuration and initialization scripts.
	 *
	 * @static
	 * @global string $tinymce_version
	 */
	public static function editor_js() {
		global $tinymce_version;

		$tmce_on = ! empty( self::$mce_settings );
		$mceInit = $qtInit = '';

		if ( $tmce_on ) {
			foreach ( self::$mce_settings as $editor_id => $init ) {
				$options = self::_parse_init( $init );
				$mceInit .= "'$editor_id':{$options},";
			}
			$mceInit = '{' . trim( $mceInit, ',' ) . '}';
		} else {
			$mceInit = '{}';
		}

		if ( ! empty( self::$qt_settings ) ) {
			foreach ( self::$qt_settings as $editor_id => $init ) {
				$options = self::_parse_init( $init );
				$qtInit .= "'$editor_id':{$options},";
			}
			$qtInit = '{' . trim( $qtInit, ',' ) . '}';
		} else {
			$qtInit = '{}';
		}

		$ref = array(
			'plugins' => implode( ',', self::$plugins ),
			'theme' => 'modern',
			'language' => self::$mce_locale
		);

		$suffix = SCRIPT_DEBUG ? '' : '.min';
		$baseurl = self::get_baseurl();
		$version = 'ver=' . $tinymce_version;

		/**
		 * Fires immediately before the TinyMCE settings are printed.
		 *
		 * @since 3.2.0
		 *
		 * @param array $mce_settings TinyMCE settings array.
		 */
		do_action( 'before_wp_tiny_mce', self::$mce_settings );
		?>

		<script type="text/javascript">
		tinyMCEPreInit = {
			baseURL: "<?php echo $baseurl; ?>",
			suffix: "<?php echo $suffix; ?>",
			<?php

			if ( self::$drag_drop_upload ) {
				echo 'dragDropUpload: true,';
			}

			?>
			mceInit: <?php echo $mceInit; ?>,
			qtInit: <?php echo $qtInit; ?>,
			ref: <?php echo self::_parse_init( $ref ); ?>,
			load_ext: function(url,lang){var sl=tinymce.ScriptLoader;sl.markDone(url+'/langs/'+lang+'.js');sl.markDone(url+'/langs/'+lang+'_dlg.js');}
		};
		</script>
		<?php

		if ( $tmce_on ) {
			self::print_tinymce_scripts();

			if ( self::$ext_plugins ) {
				// Load the old-format English strings to prevent unsightly labels in old style popups
				echo "<script type='text/javascript' src='{$baseurl}/langs/wp-langs-en.js?$version'></script>\n";
			}
		}

		/**
		 * Fires after tinymce.js is loaded, but before any TinyMCE editor
		 * instances are created.
		 *
		 * @since 3.9.0
		 *
		 * @param array $mce_settings TinyMCE settings array.
		 */
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
			var init, id, $wrap;

			if ( typeof tinymce !== 'undefined' ) {
				if ( tinymce.Env.ie && tinymce.Env.ie < 11 ) {
					tinymce.$( '.wp-editor-wrap ' ).removeClass( 'tmce-active' ).addClass( 'html-active' );
					return;
				}

				for ( id in tinyMCEPreInit.mceInit ) {
					init = tinyMCEPreInit.mceInit[id];
					$wrap = tinymce.$( '#wp-' + id + '-wrap' );

					if ( ( $wrap.hasClass( 'tmce-active' ) || ! tinyMCEPreInit.qtInit.hasOwnProperty( id ) ) && ! init.wp_skip_init ) {
						tinymce.init( init );

						if ( ! window.wpActiveEditor ) {
							window.wpActiveEditor = id;
						}
					}
				}
			}

			if ( typeof quicktags !== 'undefined' ) {
				for ( id in tinyMCEPreInit.qtInit ) {
					quicktags( tinyMCEPreInit.qtInit[id] );

					if ( ! window.wpActiveEditor ) {
						window.wpActiveEditor = id;
					}
				}
			}
		}());
		</script>
		<?php

		if ( in_array( 'wplink', self::$plugins, true ) || in_array( 'link', self::$qt_buttons, true ) ) {
			self::wp_link_dialog();
		}

		/**
		 * Fires after any core TinyMCE editor instances are created.
		 *
		 * @since 3.2.0
		 *
		 * @param array $mce_settings TinyMCE settings array.
		 */
		do_action( 'after_wp_tiny_mce', self::$mce_settings );
	}

	/**
	 * Outputs the HTML for distraction-free writing mode.
	 *
	 * @since 3.2.0
	 * @deprecated 4.3.0
	 *
	 * @static
	 */
	public static function wp_fullscreen_html() {
		_deprecated_function( __FUNCTION__, '4.3.0' );
	}

	/**
	 * Performs post queries for internal linking.
	 *
	 * @since 3.1.0
	 *
	 * @static
	 * @param array $args Optional. Accepts 'pagenum' and 's' (search) arguments.
	 * @return false|array Results.
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
		 * Filters the link query arguments.
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
		 * Filters the link query results.
		 *
		 * Allows modification of the returned link query results.
		 *
		 * @since 3.7.0
		 *
		 * @see 'wp_link_query_args' filter
		 *
		 * @param array $results {
		 *     An associative array of query results.
		 *
		 *     @type array {
		 *         @type int    $ID        Post ID.
		 *         @type string $title     The trimmed, escaped post title.
		 *         @type string $permalink Post permalink.
		 *         @type string $info      A 'Y/m/d'-formatted date for 'post' post type,
		 *                                 the 'singular_name' post type label otherwise.
		 *     }
		 * }
		 * @param array $query  An array of WP_Query arguments.
		 */
		$results = apply_filters( 'wp_link_query', $results, $query );

		return ! empty( $results ) ? $results : false;
	}

	/**
	 * Dialog for internal linking.
	 *
	 * @since 3.1.0
	 *
	 * @static
	 */
	public static function wp_link_dialog() {
		// Run once
		if ( self::$link_dialog_printed ) {
			return;
		}

		self::$link_dialog_printed = true;

		// display: none is required here, see #WP27605
		?>
		<div id="wp-link-backdrop" style="display: none"></div>
		<div id="wp-link-wrap" class="wp-core-ui" style="display: none" role="dialog" aria-labelledby="link-modal-title">
		<form id="wp-link" tabindex="-1">
		<?php wp_nonce_field( 'internal-linking', '_ajax_linking_nonce', false ); ?>
		<h1 id="link-modal-title"><?php _e( 'Insert/edit link' ) ?></h1>
		<button type="button" id="wp-link-close"><span class="screen-reader-text"><?php _e( 'Close' ); ?></span></button>
		<div id="link-selector">
			<div id="link-options">
				<p class="howto" id="wplink-enter-url"><?php _e( 'Enter the destination URL' ); ?></p>
				<div>
					<label><span><?php _e( 'URL' ); ?></span>
					<input id="wp-link-url" type="text" aria-describedby="wplink-enter-url" /></label>
				</div>
				<div class="wp-link-text-field">
					<label><span><?php _e( 'Link Text' ); ?></span>
					<input id="wp-link-text" type="text" /></label>
				</div>
				<div class="link-target">
					<label><span></span>
					<input type="checkbox" id="wp-link-target" /> <?php _e( 'Open link in a new tab' ); ?></label>
				</div>
			</div>
			<p class="howto" id="wplink-link-existing-content"><?php _e( 'Or link to existing content' ); ?></p>
			<div id="search-panel">
				<div class="link-search-wrapper">
					<label>
						<span class="search-label"><?php _e( 'Search' ); ?></span>
						<input type="search" id="wp-link-search" class="link-search-field" autocomplete="off" aria-describedby="wplink-link-existing-content" />
						<span class="spinner"></span>
					</label>
				</div>
				<div id="search-results" class="query-results" tabindex="0">
					<ul></ul>
					<div class="river-waiting">
						<span class="spinner"></span>
					</div>
				</div>
				<div id="most-recent-results" class="query-results" tabindex="0">
					<div class="query-notice" id="query-notice-message">
						<em class="query-notice-default"><?php _e( 'No search term specified. Showing recent items.' ); ?></em>
						<em class="query-notice-hint screen-reader-text"><?php _e( 'Search or use up and down arrow keys to select an item.' ); ?></em>
					</div>
					<ul></ul>
					<div class="river-waiting">
						<span class="spinner"></span>
					</div>
 				</div>
 			</div>
		</div>
		<div class="submitbox">
			<div id="wp-link-cancel">
				<button type="button" class="button"><?php _e( 'Cancel' ); ?></button>
			</div>
			<div id="wp-link-update">
				<input type="submit" value="<?php esc_attr_e( 'Add Link' ); ?>" class="button button-primary" id="wp-link-submit" name="wp-link-submit">
			</div>
		</div>
		</form>
		</div>
		<?php
	}
}
