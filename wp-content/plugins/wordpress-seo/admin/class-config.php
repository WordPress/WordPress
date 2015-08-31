<?php
/**
 * @package WPSEO\Admin
 */

/**
 * Class WPSEO_Admin_Pages
 *
 * Class with functionality for the Yoast SEO admin pages.
 */
class WPSEO_Admin_Pages {

	/**
	 * @var string $currentoption The option in use for the current admin page.
	 */
	public $currentoption = 'wpseo';

	/**
	 * Class constructor, which basically only hooks the init function on the init hook
	 */
	function __construct() {
		add_action( 'init', array( $this, 'init' ), 20 );
	}

	/**
	 * Make sure the needed scripts are loaded for admin pages
	 */
	function init() {
		if ( filter_input( INPUT_GET, 'wpseo_reset_defaults' ) && wp_verify_nonce( filter_input( INPUT_GET, 'nonce' ), 'wpseo_reset_defaults' ) && current_user_can( 'manage_options' ) ) {
			WPSEO_Options::reset();
			wp_redirect( admin_url( 'admin.php?page=wpseo_dashboard' ) );
		}

		if ( WPSEO_Utils::grant_access() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'config_page_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'config_page_styles' ) );
		}
	}

	/**
	 * Loads the required styles for the config page.
	 */
	function config_page_styles() {
		wp_enqueue_style( 'dashboard' );
		wp_enqueue_style( 'thickbox' );
		wp_enqueue_style( 'global' );
		wp_enqueue_style( 'wp-admin' );
		wp_enqueue_style( 'yoast-admin-css', plugins_url( 'css/yst_plugin_tools' . WPSEO_CSSJS_SUFFIX . '.css', WPSEO_FILE ), array(), WPSEO_VERSION );

		if ( is_rtl() ) {
			wp_enqueue_style( 'wpseo-rtl', plugins_url( 'css/wpseo-rtl' . WPSEO_CSSJS_SUFFIX . '.css', WPSEO_FILE ), array(), WPSEO_VERSION );
		}
	}

	/**
	 * Loads the required scripts for the config page.
	 */
	function config_page_scripts() {
		wp_enqueue_script( 'wpseo-admin-script', plugins_url( 'js/wp-seo-admin' . WPSEO_CSSJS_SUFFIX . '.js', WPSEO_FILE ), array(
			'jquery',
			'jquery-ui-core',
		), WPSEO_VERSION, true );
		wp_localize_script( 'wpseo-admin-script', 'wpseoAdminL10n', $this->localize_admin_script() );
		wp_enqueue_script( 'dashboard' );
		wp_enqueue_script( 'thickbox' );

		$page = filter_input( INPUT_GET, 'page' );
		$tool = filter_input( INPUT_GET, 'tool' );

		if ( in_array( $page, array( 'wpseo_social', 'wpseo_dashboard' ) ) ) {
			wp_enqueue_media();
			wp_enqueue_script( 'wpseo-admin-media', plugins_url( 'js/wp-seo-admin-media' . WPSEO_CSSJS_SUFFIX . '.js', WPSEO_FILE ), array(
				'jquery',
				'jquery-ui-core',
			), WPSEO_VERSION, true );
			wp_localize_script( 'wpseo-admin-media', 'wpseoMediaL10n', $this->localize_media_script() );
		}

		if ( 'wpseo_tools' === $page && 'bulk-editor' === $tool ) {
			wp_enqueue_script( 'wpseo-bulk-editor', plugins_url( 'js/wp-seo-bulk-editor' . WPSEO_CSSJS_SUFFIX . '.js', WPSEO_FILE ), array( 'jquery' ), WPSEO_VERSION, true );
		}

		if ( 'wpseo_tools' === $page && 'import-export' === $tool ) {
			wp_enqueue_script( 'wpseo-export', plugins_url( 'js/wp-seo-export' . WPSEO_CSSJS_SUFFIX . '.js', WPSEO_FILE ), array( 'jquery' ), WPSEO_VERSION, true );
		}
	}

	/**
	 * Pass some variables to js for upload module.
	 *
	 * @return  array
	 */
	public function localize_media_script() {
		return array(
			'choose_image' => __( 'Use Image', 'wordpress-seo' ),
		);
	}

	/**
	 * Pass some variables to js for the admin JS module.
	 *
	 * %s is replaced with <code>%s</code> and replaced again in the javascript with the actual variable.
	 *
	 * @return  array
	 */
	public function localize_admin_script() {
		return array(
			/* translators: %s: '%%term_title%%' variable used in titles and meta's template that's not compatible with the given template */
			'variable_warning' => sprintf( __( 'Warning: the variable %s cannot be used in this template.', 'wordpress-seo' ), '<code>%s</code>' ) . ' ' . __( 'See the help tab for more info.', 'wordpress-seo' ),
		);
	}

	/********************** DEPRECATED METHODS **********************/

	/**
	 * Exports the current site's Yoast SEO settings.
	 *
	 * @param bool $include_taxonomy Whether to include the taxonomy metadata the plugin creates.
	 *
	 * @return bool|string $return False when failed, the URL to the export file when succeeded.
	 */
	public function export_settings( $include_taxonomy ) {
		_deprecated_function( __METHOD__, 'WPSEO 2.0', 'This method is deprecated, please use the <code>WPSEO_Export</code> class.' );

		$export = new WPSEO_Export( $include_taxonomy );
		if ( $export->success ) {
			return $export->export_zip_url;
		}
		else {
			return false;
		}
	}

	/**
	 * Generates the header for admin pages
	 *
	 * @deprecated 2.0
	 *
	 * @param bool   $form             Whether or not the form start tag should be included.
	 * @param mixed  $option_long_name The long name of the option to use for the current page.
	 * @param string $option           The short name of the option to use for the current page.
	 * @param bool   $contains_files   Whether the form should allow for file uploads.
	 */
	public function admin_header( $form = true, $option_long_name = false, $option = 'wpseo', $contains_files = false ) {
		_deprecated_function( __METHOD__, 'WPSEO 2.0', 'This method is deprecated, please use the <code>Yoast_Form</code> class.' );

		Yoast_Form::get_instance()->admin_header( $form, $option, $contains_files, $option_long_name );
	}

	/**
	 * Generates the footer for admin pages
	 *
	 * @deprecated 2.0
	 *
	 * @param bool $submit       Whether or not a submit button and form end tag should be shown.
	 * @param bool $show_sidebar Whether or not to show the banner sidebar - used by premium plugins to disable it.
	 */
	public function admin_footer( $submit = true, $show_sidebar = true ) {
		_deprecated_function( __METHOD__, 'WPSEO 2.0', 'This method is deprecated, please use the <code>Yoast_Form</code> class.' );

		Yoast_Form::get_instance()->admin_footer( $submit, $show_sidebar );
	}

	/**
	 * Generates the sidebar for admin pages.
	 *
	 * @deprecated 2.0
	 */
	public function admin_sidebar() {
		_deprecated_function( __METHOD__, 'WPSEO 2.0', 'This method is deprecated, please use the <code>Yoast_Form</code> class.' );

		Yoast_Form::get_instance()->admin_sidebar();
	}

	/**
	 * Create a Checkbox input field.
	 *
	 * @deprecated 2.0
	 *
	 * @param string $var        The variable within the option to create the checkbox for.
	 * @param string $label      The label to show for the variable.
	 * @param bool   $label_left Whether the label should be left (true) or right (false).
	 * @param string $option     The option the variable belongs to.
	 */
	public function checkbox( $var, $label, $label_left = false, $option = '' ) {
		_deprecated_function( __METHOD__, 'WPSEO 2.0', 'This method is deprecated, please use the <code>Yoast_Form</code> class.' );

		if ( $option !== '' ) {
			Yoast_Form::get_instance()->set_option( $option );
		}

		Yoast_Form::get_instance()->checkbox( $var, $label, $label_left );
	}

	/**
	 * Create a Text input field.
	 *
	 * @deprecated 2.0
	 *
	 * @param string $var    The variable within the option to create the text input field for.
	 * @param string $label  The label to show for the variable.
	 * @param string $option The option the variable belongs to.
	 */
	function textinput( $var, $label, $option = '' ) {
		_deprecated_function( __METHOD__, 'WPSEO 2.0', 'This method is deprecated, please use the <code>Yoast_Form</code> class.' );

		if ( $option !== '' ) {
			Yoast_Form::get_instance()->set_option( $option );
		}
		Yoast_Form::get_instance()->textinput( $var, $label );
	}

	/**
	 * Create a textarea.
	 *
	 * @deprecated 2.0
	 *
	 * @param string $var    The variable within the option to create the textarea for.
	 * @param string $label  The label to show for the variable.
	 * @param string $option The option the variable belongs to.
	 * @param array  $attr   The CSS class to assign to the textarea.
	 */
	function textarea( $var, $label, $option = '', $attr = array() ) {
		_deprecated_function( __METHOD__, 'WPSEO 2.0', 'This method is deprecated, please use the <code>Yoast_Form</code> class.' );

		if ( $option !== '' ) {
			Yoast_Form::get_instance()->set_option( $option );
		}

		Yoast_Form::get_instance()->textarea( $var, $label, $attr );
	}

	/**
	 * Create a hidden input field.
	 *
	 * @deprecated 2.0
	 *
	 * @param string $var    The variable within the option to create the hidden input for.
	 * @param string $option The option the variable belongs to.
	 */
	function hidden( $var, $option = '' ) {
		_deprecated_function( __METHOD__, 'WPSEO 2.0', 'This method is deprecated, please use the <code>Yoast_Form</code> class.' );

		if ( $option !== '' ) {
			Yoast_Form::get_instance()->set_option( $option );
		}

		Yoast_Form::get_instance()->hidden( $var );
	}

	/**
	 * Create a Select Box.
	 *
	 * @deprecated 2.0
	 *
	 * @param string $var    The variable within the option to create the select for.
	 * @param string $label  The label to show for the variable.
	 * @param array  $values The select options to choose from.
	 * @param string $option The option the variable belongs to.
	 */
	function select( $var, $label, $values, $option = '' ) {
		_deprecated_function( __METHOD__, 'WPSEO 2.0', 'This method is deprecated, please use the <code>Yoast_Form</code> class.' );

		if ( $option !== '' ) {
			Yoast_Form::get_instance()->set_option( $option );
		}

		Yoast_Form::get_instance()->select( $var, $label, $values );
	}

	/**
	 * Create a File upload field.
	 *
	 * @deprecated 2.0
	 *
	 * @param string $var    The variable within the option to create the file upload field for.
	 * @param string $label  The label to show for the variable.
	 * @param string $option The option the variable belongs to.
	 */
	function file_upload( $var, $label, $option = '' ) {
		_deprecated_function( __METHOD__, 'WPSEO 2.0', 'This method is deprecated, please use the <code>Yoast_Form</code> class.' );

		if ( $option !== '' ) {
			Yoast_Form::get_instance()->set_option( $option );
		}

		Yoast_Form::get_instance()->file_upload( $var, $label );
	}

	/**
	 * Media input
	 *
	 * @deprecated 2.0
	 *
	 * @param string $var
	 * @param string $label
	 * @param string $option
	 */
	function media_input( $var, $label, $option = '' ) {
		_deprecated_function( __METHOD__, 'WPSEO 2.0', 'This method is deprecated, please use the <code>Yoast_Form</code> class.' );

		if ( $option !== '' ) {
			Yoast_Form::get_instance()->set_option( $option );
		}

		Yoast_Form::get_instance()->media_input( $var, $label );
	}

	/**
	 * Create a Radio input field.
	 *
	 * @deprecated 2.0
	 *
	 * @param string $var    The variable within the option to create the file upload field for.
	 * @param array  $values The radio options to choose from.
	 * @param string $label  The label to show for the variable.
	 * @param string $option The option the variable belongs to.
	 */
	function radio( $var, $values, $label, $option = '' ) {
		_deprecated_function( __METHOD__, 'WPSEO 2.0', 'This method is deprecated, please use the <code>Yoast_Form</code> class.' );

		if ( $option !== '' ) {
			Yoast_Form::get_instance()->set_option( $option );
		}

		Yoast_Form::get_instance()->radio( $var, $values, $label );
	}

	/**
	 * Create a postbox widget.
	 *
	 * @deprecated 2.0
	 *
	 * @param string $id      ID of the postbox.
	 * @param string $title   Title of the postbox.
	 * @param string $content Content of the postbox.
	 */
	function postbox( $id, $title, $content ) {
		_deprecated_function( __METHOD__, 'WPSEO 2.0', 'This method is deprecated, please re-implement the admin pages.' );

		?>
			<div id="<?php echo esc_attr( $id ); ?>" class="yoastbox">
				<h2><?php echo $title; ?></h2>
				<?php echo $content; ?>
			</div>
		<?php
	}

	/**
	 * Create a form table from an array of rows.
	 *
	 * @deprecated 2.0
	 *
	 * @param array $rows Rows to include in the table.
	 *
	 * @return string
	 */
	function form_table( $rows ) {
		_deprecated_function( __METHOD__, 'WPSEO 2.0', 'This method is deprecated, please re-implement the admin pages.' );

		if ( ! is_array( $rows ) || $rows === array() ) {
			return '';
		}

		$content = '<table class="form-table">';
		foreach ( $rows as $row ) {
			$content .= '<tr><th scope="row">';
			if ( isset( $row['id'] ) && $row['id'] != '' ) {
				$content .= '<label for="' . esc_attr( $row['id'] ) . '">' . esc_html( $row['label'] ) . ':</label>';
			}
			else {
				$content .= esc_html( $row['label'] );
			}
			if ( isset( $row['desc'] ) && $row['desc'] != '' ) {
				$content .= '<br/><small>' . esc_html( $row['desc'] ) . '</small>';
			}
			$content .= '</th><td>';
			$content .= $row['content'];
			$content .= '</td></tr>';
		}
		$content .= '</table>';

		return $content;
	}

	/**
	 * Resets the site to the default Yoast SEO settings and runs a title test to check
	 * whether force rewrite needs to be on.
	 *
	 * @deprecated 1.5.0
	 * @deprecated use WPSEO_Options::reset()
	 * @see        WPSEO_Options::reset()
	 */
	function reset_defaults() {
		_deprecated_function( __METHOD__, 'WPSEO 1.5.0', 'WPSEO_Options::reset()' );
		WPSEO_Options::reset();
	}


} /* End of class */
