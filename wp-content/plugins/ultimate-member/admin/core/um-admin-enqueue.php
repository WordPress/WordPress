<?php

class UM_Admin_Enqueue {

	function __construct() {
	
		$this->slug = 'ultimatemember';
		
		add_action('admin_head', array(&$this, 'admin_head'), 9);
		
		add_action('admin_enqueue_scripts',  array(&$this, 'admin_enqueue_scripts'), 9);
		
		add_filter('admin_body_class', array(&$this, 'admin_body_class'), 999 );
		
		add_filter('enter_title_here', array(&$this, 'enter_title_here') );

	}
	
	/***
	***	@enter title placeholder
	***/
	function enter_title_here( $title ){
		$screen = get_current_screen();
		if ( 'um_directory' == $screen->post_type ){
			$title = 'e.g. Member Directory';
		}
		if ( 'um_role' == $screen->post_type ){
			$title = 'e.g. Community Member';
		}
		if ( 'um_form' == $screen->post_type ){
			$title = 'e.g. New Registration Form';
		}
		return $title;
	}
	
	/***
	***	@Runs on admin head
	***/
	function admin_head(){
		
		if ( $this->is_plugin_post_type() ){

			?>
			
			<style type="text/css">
				.um-admin.post-type-<?php echo get_post_type(); ?> div#slugdiv,
				.um-admin.post-type-<?php echo get_post_type(); ?> div#minor-publishing,
				.um-admin.post-type-<?php echo get_post_type(); ?> div#screen-meta-links
				{display:none}
			</style>
			
			<?php
		}
		
	}
	
	/***
	***	@check that we're on a custom post type supported by UM
	***/
	function is_plugin_post_type(){
		if (isset($_REQUEST['post_type'])){
			$post_type = $_REQUEST['post_type'];
			if ( in_array($post_type, array('um_form','um_role','um_directory'))){
				return true;
			}
		} else if ( isset($_REQUEST['action'] ) && $_REQUEST['action'] == 'edit') {
			$post_type = get_post_type();
			if ( in_array($post_type, array('um_form','um_role','um_directory'))){
				return true;
			}
		}
		return false;
	}
	
	/***
	***	@Load Form
	***/
	function load_form(){
	
		wp_register_style('um_admin_form', um_url . 'admin/assets/css/um-admin-form.css' );
		wp_enqueue_style('um_admin_form');
		
		wp_register_script('um_admin_form', um_url . 'admin/assets/js/um-admin-form.js', '', '', true );
		wp_enqueue_script('um_admin_form');
		
	}
	
	/***
	***	@Load dashboard
	***/
	function load_dashboard(){

		wp_register_style('um_admin_dashboard', um_url . 'admin/assets/css/um-admin-dashboard.css' );
		wp_enqueue_style('um_admin_dashboard');
		
		wp_register_script('um_admin_dashboard', um_url . 'admin/assets/js/um-admin-dashboard.js', '', '', true );
		wp_enqueue_script('um_admin_dashboard');
		
	}
	
	/***
	***	@Load modal
	***/
	function load_modal(){

		wp_register_style('um_admin_modal', um_url . 'admin/assets/css/um-admin-modal.css' );
		wp_enqueue_style('um_admin_modal');
		
		wp_register_script('um_admin_modal', um_url . 'admin/assets/js/um-admin-modal.js', '', '', true );
		wp_enqueue_script('um_admin_modal');
		
	}
	
	/***
	***	@Field Processing
	***/
	function load_field(){

		wp_register_script('um_admin_field', um_url . 'admin/assets/js/um-admin-field.js', '', '', true );
		wp_enqueue_script('um_admin_field');
		
	}
	
	/***
	***	@Users table
	***/
	function load_users_js(){

		wp_register_script('um_admin_users', um_url . 'admin/assets/js/um-admin-users.js', '', '', true );
		wp_enqueue_script('um_admin_users');
		
	}
	
	/***
	***	@Load Builder
	***/
	function load_builder(){
	
		wp_register_script('um_admin_builder', um_url . 'admin/assets/js/um-admin-builder.js', '', '', true );
		wp_enqueue_script('um_admin_builder');
		
		wp_register_script('um_admin_dragdrop', um_url . 'admin/assets/js/um-admin-dragdrop.js', '', '', true );
		wp_enqueue_script('um_admin_dragdrop');
		
		wp_register_style('um_admin_builder', um_url . 'admin/assets/css/um-admin-builder.css' );
		wp_enqueue_style('um_admin_builder');
		
	}
	
	/***
	***	@Load core WP styles/scripts
	***/
	function load_core_wp(){
	
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		
		wp_enqueue_script('jquery-ui-draggable');
		wp_enqueue_script('jquery-ui-sortable');
		
	}
	
	/***
	***	@Load Admin Styles
	***/
	function load_css(){
	
		wp_register_style('um_admin_menu', um_url . 'admin/assets/css/um-admin-menu.css' );
		wp_enqueue_style('um_admin_menu');
		
		wp_register_style('um_admin_columns', um_url . 'admin/assets/css/um-admin-columns.css' );
		wp_enqueue_style('um_admin_columns');

		wp_register_style('um_admin_misc', um_url . 'admin/assets/css/um-admin-misc.css' );
		wp_enqueue_style('um_admin_misc');
		
		if ( get_post_type() != 'shop_order' ) {
			wp_register_style('um_admin_select2', um_url . 'admin/assets/css/um-admin-select2.css' );
			wp_enqueue_style('um_admin_select2');
		}

	}
	
	/***
	***	@Load global css
	***/
	function load_global_css(){
	
		wp_register_style('um_admin_global', um_url . 'admin/assets/css/um-admin-global.css' );
		wp_enqueue_style('um_admin_global');
		
	}
	
	/***
	***	@Load jQuery custom code
	***/
	function load_custom_scripts(){
	
		wp_register_script('um_admin_scripts', um_url . 'admin/assets/js/um-admin-scripts.js', '', '', true );
		wp_enqueue_script('um_admin_scripts');
		
	}
	
	/***
	***	@Load AJAX
	***/
	function load_ajax_js(){
	
		wp_register_script('um_admin_ajax', um_url . 'admin/assets/js/um-admin-ajax.js', '', '', true );
		wp_enqueue_script('um_admin_ajax');
		
	}
	
	/***
	***	@Load Redux css
	***/
	function load_redux_css(){
	
		wp_register_style('um_admin_redux', um_url . 'admin/assets/css/um-admin-redux.css' );
		wp_enqueue_style('um_admin_redux');
		
	}
	
	/***
	***	@Boolean check if we're viewing UM backend
	***/
	function is_UM_admin(){
		
		global $current_screen, $post, $tax;

		$screen_id = $current_screen->id;

		if ( !is_admin() ) return false;
		
		if ( strstr( $screen_id, 'ultimatemember'  ) || strstr( $screen_id, 'um_') || strstr($screen_id, 'user') || strstr($screen_id, 'profile') )return true;
		
		if ( $screen_id == 'nav-menus' ) return true;
		
		if ( isset( $post->post_type ) ) return true;

		if ( isset( $tax->name ) ) return true;

		return false;
		
	}
	
	/***
	***	@Adds class to our admin pages
	***/
	function admin_body_class($classes){
		if ( $this->is_UM_admin() ) {
			return "$classes um-admin";
		}
		return $classes;	
	}
	
	/***
	***	@Enqueue scripts and styles
	***/
	function admin_enqueue_scripts(){
		global $ultimatemember, $post;
		
		if ( $this->is_UM_admin() ) {

			if ( get_post_type() != 'shop_order' ) {
				$ultimatemember->styles->wp_enqueue_scripts();
			}
			
			$this->load_global_css();
			$this->load_form();
			$this->load_modal();
			$this->load_dashboard();
			$this->load_field();
			$this->load_users_js();
			$this->load_builder();
			$this->load_redux_css();
			$this->load_css();
			$this->load_core_wp();
			$this->load_ajax_js();
			$this->load_custom_scripts();
			
			if ( is_rtl() ) {
				wp_register_style('um_admin_rtl', um_url . 'admin/assets/css/um-admin-rtl.css' );
				wp_enqueue_style('um_admin_rtl');
			}
		
		} else {
			
			$this->load_global_css();
			
		}
		
	}

}
