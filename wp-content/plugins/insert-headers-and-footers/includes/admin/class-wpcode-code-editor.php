<?php
/**
 * Central place to load the Code Editor used throughout the plugin with
 * similar settings.
 *
 * @package WPCode
 */

/**
 * Class WPCode_Code_Editor.
 */
class WPCode_Code_Editor {

	/**
	 * Array of settings used to instantiate the editor.
	 *
	 * @var array
	 */
	private $settings;

	/**
	 * Array of ids of textareas to init as code editors.
	 *
	 * @var array
	 */
	private $editors;

	/**
	 * WPCode_Code_Editor Constructor.
	 *
	 * @param string $code_type The code type that will be converted to the mime type for the editor.
	 */
	public function __construct( $code_type = 'html' ) {
		$this->load_code_mirror( $code_type );
	}

	/**
	 * If called, this loads CodeMirror on the current admin page with checks.
	 *
	 * @param string $code_type The code type that will be converted to the mime type for the editor.
	 *
	 * @return array|false
	 */
	public function load_code_mirror( $code_type ) {
		if ( ! function_exists( 'wp_enqueue_code_editor' ) ) {
			return false;
		}
		$editor_args = array(
			'type'       => wpcode()->execute->get_mime_for_code_type( $code_type ),
			'showHint'   => true,
			'codemirror' => array(
				'matchBrackets'             => true,
				'gutters'                   => array( 'CodeMirror-lint-markers', 'CodeMirror-foldgutter' ),
				'foldGutter'                => true,
				'autoCloseBrackets'         => true,
				'highlightSelectionMatches' => true,
			),
		);
		if ( ! current_user_can( 'wpcode_edit_snippets' ) ) {
			$editor_args['codemirror']['readOnly'] = true;
		}

		// Allow filtering of the editor args.
		$editor_args = apply_filters( 'wpcode_editor_config', $editor_args );

		// Add filter to override the current user meta for syntax highlighting.
		add_filter( 'get_user_metadata', array( $this, 'override_user_meta' ), 10, 4 );
		// Enqueue code editor and settings for manipulating HTML.
		$this->settings = wp_enqueue_code_editor( $editor_args );
		// Remove the filter.
		remove_filter( 'get_user_metadata', array( $this, 'override_user_meta' ) );

		return $this->settings;
	}

	/**
	 * When using the WPCode editor always load syntax highlighting.
	 *
	 * @param mixed  $value The meta value.
	 * @param int    $object_id The user ID.
	 * @param string $meta_key The meta key being looked up.
	 * @param bool   $single True if only the first value should be returned.
	 *
	 * @return bool|mixed
	 */
	public function override_user_meta( $value, $object_id, $meta_key, $single ) {
		if ( 'syntax_highlighting' !== $meta_key || ! apply_filters( 'wpcode_override_syntax_highlighting', true ) ) {
			return $value;
		}

		return true;
	}

	/**
	 * Load hint scripts if needed.
	 *
	 * @return void
	 */
	public function load_hint_scripts() {
		wp_enqueue_script( 'htmlhint' );
		wp_enqueue_script( 'csslint' );
		wp_enqueue_script( 'jshint' );
	}

	/**
	 * Update a setting for the editor instance.
	 *
	 * @param string $key Key of setting to update.
	 * @param mixed  $value Value to set the setting to, can be string, integer, array.
	 *
	 * @return void
	 */
	public function set_setting( $key, $value ) {
		if ( ! isset( $this->settings ) ) {
			return;
		}
		if ( ! isset( $this->settings['codemirror'] ) ) {
			$this->settings['codemirror'] = array();
		}

		$this->settings['codemirror'][ $key ] = $value;
	}

	/**
	 * Load the inline script needed to initiate the code editor using the current settings.
	 *
	 * @param string $id The textarea ID to init the editor for.
	 *
	 * @return void
	 */
	public function register_editor( $id ) {
		$this->editors[] = $id;
	}

	/**
	 * Load the inline script needed to initiate the code editor using the current settings.
	 *
	 * @return void
	 */
	public function init_editor() {
		wp_add_inline_script(
			'code-editor',
			sprintf(
				'
				jQuery( function() {
							window.wpcode_editor = window.wpcode_editor ? window.wpcode_editor : {};
							var ids = %1$s;
							var settings = %2$s;
							for ( var i in ids ) {
								window.wpcode_editor[ids[i]] = wp.codeEditor.initialize( ids[i], settings );
							}
						} );',
				wp_json_encode( $this->editors ),
				wp_json_encode( $this->get_settings() )
			)
		);
	}

	/**
	 * Get the settings.
	 *
	 * @return array
	 */
	public function get_settings() {
		return $this->settings;
	}
}
