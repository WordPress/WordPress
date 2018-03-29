<?php

class UM_Enqueue {

	function __construct() {

		add_action('wp_head',  array(&$this, 'wp_head'), 999); // high-priority

		$priority = apply_filters( 'um_core_enqueue_priority', 100 );
		add_action('wp_enqueue_scripts',  array(&$this, 'wp_enqueue_scripts'), $priority );

	}

	/***
	***	@Enqueue inline css globally
	***/
	function wp_head() {
		$css = um_get_option('custom_css');
		if ( !$css ) return; ?><!-- ULTIMATE MEMBER INLINE CSS BEGIN --><style type="text/css"><?php print $this->minify( $css ); ?></style><!-- ULTIMATE MEMBER INLINE CSS END --><?php
	}

	/***
	***	@Minify css string
	***/
	function minify( $css ) {
		$css = str_replace(array("\r", "\n"), '', $css);
		$css = str_replace(' {','{', $css );
		$css = str_replace('{ ','{', $css );
		$css = str_replace('; ',';', $css );
		$css = str_replace(';}','}', $css );
		$css = str_replace(': ',':', $css );
		return $css;
	}

	/***
	***	@Enqueue scripts and styles
	***/
	function wp_enqueue_scripts() {
		global $ultimatemember, $post;

		$exclude_home = um_get_option('js_css_exlcude_home');
		if ( $exclude_home && ( is_home() || is_front_page() ) ) {
			return;
		}

		$exclude = um_get_option('js_css_exclude');
		if ( is_array( $exclude ) ) {
			array_filter( $exclude );
		}
		if ( $exclude && !is_admin() && is_array( $exclude ) ) {

			$c_url = $ultimatemember->permalinks->get_current_url( get_option('permalink_structure') );

			foreach( $exclude as $match ) {
				if ( ! empty( $c_url ) && strstr( $c_url, untrailingslashit( $match ) ) ) {
					return;
				}
			}

		}

		$include = um_get_option('js_css_include');
		if ( is_array( $include ) ) {
			array_filter( $include );
		}
		if ( $include && !is_admin() && is_array( $include ) ) {

			$c_url = $ultimatemember->permalinks->get_current_url( get_option('permalink_structure') );

			foreach( $include as $match ) {
				if ( strstr( $c_url, untrailingslashit( $match ) ) ) {
					$force_load = true;
				} else {
					if ( !isset( $force_load ) ) {
						$force_load = false;
					}
				}
			}

		}

		if ( isset($force_load) && $force_load == false ) return;

		// enqueue styles
		if ( um_get_option('disable_minify') ) {

			$this->load_original();

			wp_localize_script( 'um_scripts', 'um_scripts', array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'fileupload' => um_url . 'core/lib/upload/um-file-upload.php',
					'imageupload' => um_url . 'core/lib/upload/um-image-upload.php'
			) );

		} else {

			wp_register_script('um_minified', um_url . 'assets/js/um.min.js', array('jquery', 'jquery-masonry'), ultimatemember_version, true );
			wp_enqueue_script('um_minified');

			wp_localize_script( 'um_minified', 'um_scripts', array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'fileupload' => um_url . 'core/lib/upload/um-file-upload.php',
					'imageupload' => um_url . 'core/lib/upload/um-image-upload.php'
			) );

			wp_register_style('um_minified', um_url . 'assets/css/um.min.css', '', ultimatemember_version, 'all' );
			wp_enqueue_style('um_minified');

		}

		// rtl style
		if ( is_rtl() ) {
			wp_register_style('um_rtl', um_url . 'assets/css/um.rtl.css', '', ultimatemember_version, 'all' );
			wp_enqueue_style('um_rtl');
		}

		// load a localized version for date/time
		$locale = get_locale();
		if ( $locale && file_exists( um_path . 'assets/js/pickadate/translations/' . $locale . '.js' ) ) {
			wp_register_script('um_datetime_locale', um_url . 'assets/js/pickadate/translations/' . $locale . '.js', '', ultimatemember_version, true );
			wp_enqueue_script('um_datetime_locale');
		}

		if(is_object($post) && has_shortcode($post->post_content,'ultimatemember'  )) {
			wp_dequeue_script('jquery-form');
		}
	}

	/***
	***	@This will load original files (not minified)
	***/
	function load_original() {

		$this->load_google_charts();

		$this->load_fonticons();

		$this->load_selectjs();

		$this->load_modal();

		$this->load_css();

		$this->load_fileupload();

		$this->load_datetimepicker();

		$this->load_raty();

		$this->load_scrollto();

		$this->load_scrollbar();

		$this->load_imagecrop();

		$this->load_tipsy();

		$this->load_functions();

		$this->load_responsive();

		$this->load_customjs();

	}

	/***
	***	@Include Google charts
	***/
	function load_google_charts(){

		wp_register_script('um_gchart', 'https://www.google.com/jsapi' );
		wp_enqueue_script('um_gchart');

	}

	/***
	***	@Load plugin css
	***/
	function load_css(){

		wp_register_style('um_styles', um_url . 'assets/css/um-styles.css' );
		wp_enqueue_style('um_styles');

		wp_register_style('um_members', um_url . 'assets/css/um-members.css' );
		wp_enqueue_style('um_members');

		wp_register_style('um_profile', um_url . 'assets/css/um-profile.css' );
		wp_enqueue_style('um_profile');

		wp_register_style('um_account', um_url . 'assets/css/um-account.css' );
		wp_enqueue_style('um_account');

		wp_register_style('um_misc', um_url . 'assets/css/um-misc.css' );
		wp_enqueue_style('um_misc');

	}

	/***
	***	@Load select-dropdowns JS
	***/
	function load_selectjs(){

		if ( class_exists( 'WooCommerce' ) ) {
				wp_dequeue_style( 'select2' );
		        wp_deregister_style( 'select2' );

		        wp_dequeue_script( 'select2');
		        wp_deregister_script('select2');
		}

		wp_register_script('select2', um_url . 'assets/js/select2/select2.full.min.js', array('jquery', 'jquery-masonry') );
		wp_enqueue_script('select2');

		wp_register_style('select2', um_url . 'assets/css/select2/select2.min.css' );
		wp_enqueue_style('select2');

	}

	/***
	***	@Load Fonticons
	***/
	function load_fonticons(){

		wp_register_style('um_fonticons_ii', um_url . 'assets/css/um-fonticons-ii.css' );
		wp_enqueue_style('um_fonticons_ii');

		wp_register_style('um_fonticons_fa', um_url . 'assets/css/um-fonticons-fa.css' );
		wp_enqueue_style('um_fonticons_fa');

	}

	/***
	***	@Load fileupload JS
	***/
	function load_fileupload() {

		wp_register_script('um_jquery_form', um_url . 'assets/js/um-jquery-form.js' );
		wp_enqueue_script('um_jquery_form');

		wp_register_script('um_fileupload', um_url . 'assets/js/um-fileupload.js' );
		wp_enqueue_script('um_fileupload');

		wp_register_style('um_fileupload', um_url . 'assets/css/um-fileupload.css' );
		wp_enqueue_style('um_fileupload');

	}

	/***
	***	@Load JS functions
	***/
	function load_functions(){

		wp_register_script('um_functions', um_url . 'assets/js/um-functions.js' );
		wp_enqueue_script('um_functions');

	}

	/***
	***	@Load custom JS
	***/
	function load_customjs(){

		wp_register_script('um_conditional', um_url . 'assets/js/um-conditional.js' );
		wp_enqueue_script('um_conditional');

		wp_register_script('um_scripts', um_url . 'assets/js/um-scripts.js' );
		wp_enqueue_script('um_scripts');

		wp_register_script('um_members', um_url . 'assets/js/um-members.js' );
		wp_enqueue_script('um_members');

		wp_register_script('um_profile', um_url . 'assets/js/um-profile.js' );
		wp_enqueue_script('um_profile');

		wp_register_script('um_account', um_url . 'assets/js/um-account.js' );
		wp_enqueue_script('um_account');

	}

	/***
	***	@Load date & time picker
	***/
	function load_datetimepicker(){

		wp_register_script('um_datetime', um_url . 'assets/js/pickadate/picker.js' );
		wp_enqueue_script('um_datetime');

		wp_register_script('um_datetime_date', um_url . 'assets/js/pickadate/picker.date.js' );
		wp_enqueue_script('um_datetime_date');

		wp_register_script('um_datetime_time', um_url . 'assets/js/pickadate/picker.time.js' );
		wp_enqueue_script('um_datetime_time');

		wp_register_script('um_datetime_legacy', um_url . 'assets/js/pickadate/legacy.js' );
		wp_enqueue_script('um_datetime_legacy');

		wp_register_style('um_datetime', um_url . 'assets/css/pickadate/default.css' );
		wp_enqueue_style('um_datetime');

		wp_register_style('um_datetime_date', um_url . 'assets/css/pickadate/default.date.css' );
		wp_enqueue_style('um_datetime_date');

		wp_register_style('um_datetime_time', um_url . 'assets/css/pickadate/default.time.css' );
		wp_enqueue_style('um_datetime_time');

	}

	/***
	***	@Load scrollto
	***/
	function load_scrollto(){

		wp_register_script('um_scrollto', um_url . 'assets/js/um-scrollto.js' );
		wp_enqueue_script('um_scrollto');

	}

	/***
	***	@Load scrollbar
	***/
	function load_scrollbar(){

		wp_register_script('um_scrollbar', um_url . 'assets/js/um-scrollbar.js' );
		wp_enqueue_script('um_scrollbar');

		wp_register_style('um_scrollbar', um_url . 'assets/css/um-scrollbar.css' );
		wp_enqueue_style('um_scrollbar');

	}

	/***
	***	@Load rating
	***/
	function load_raty(){

		wp_register_script('um_raty', um_url . 'assets/js/um-raty.js' );
		wp_enqueue_script('um_raty');

		wp_register_style('um_raty', um_url . 'assets/css/um-raty.css' );
		wp_enqueue_style('um_raty');

	}

	/***
	***	@Load crop script
	***/
	function load_imagecrop(){

		wp_register_script('um_crop', um_url . 'assets/js/um-crop.js' );
		wp_enqueue_script('um_crop');

		wp_register_style('um_crop', um_url . 'assets/css/um-crop.css' );
		wp_enqueue_style('um_crop');

	}

	/***
	***	@Load tipsy
	***/
	function load_tipsy(){

		wp_register_script('um_tipsy', um_url . 'assets/js/um-tipsy.js' );
		wp_enqueue_script('um_tipsy');

		wp_register_style('um_tipsy', um_url . 'assets/css/um-tipsy.css' );
		wp_enqueue_style('um_tipsy');

	}

	/***
	***	@Load modal
	***/
	function load_modal(){

		wp_register_style('um_modal', um_url . 'assets/css/um-modal.css' );
		wp_enqueue_style('um_modal');

		wp_register_script('um_modal', um_url . 'assets/js/um-modal.js' );
		wp_enqueue_script('um_modal');

	}

	/***
	***	@Load responsive styles
	***/
	function load_responsive(){

		wp_register_script('um_responsive', um_url . 'assets/js/um-responsive.js' );
		wp_enqueue_script('um_responsive');

		wp_register_style('um_responsive', um_url . 'assets/css/um-responsive.css' );
		wp_enqueue_style('um_responsive');

	}

}
