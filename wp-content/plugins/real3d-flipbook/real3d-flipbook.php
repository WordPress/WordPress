<?php

	/*
	Plugin Name: Real 3D Flipbook
	Plugin URI: http://codecanyon.net/user/zlac/portfolio?ref=zlac
	Description: Premium responsive real 3d flipbook  
	Version: 1.2.15
	Author: zlac
	Author URI: http://codecanyon.net/user/zlac?ref=zlac
	*/

	// define( 'WP_DEBUG', true );
	define('REAL3D_FLIPBOOK_DIR', plugin_dir_url( __FILE__ ));
	define('REAL3D_FLIPBOOK_VERSION', '1.2.15');
	
	function trace($var){
		echo("<pre style='background:#fcc;color:#000;font-size:12px;font-weight:bold'>");
		print_r($var);
		echo("</pre>");
	}

	if(!is_admin()) {
		include("includes/plugin-frontend.php");
	}
	else {
		include("includes/plugin-admin.php");
		register_deactivation_hook( __FILE__, "deactivate_real3d_flipbook");
		add_filter("plugin_action_links_" . plugin_basename(__FILE__), "real3d_flipbook_admin_link");
	}
	
	
	function real3d_flipbook_scripts() {
		// wp_enqueue_script("three", plugins_url()."/real3d-flipbook/js/three66.min.js", array('jquery'),REAL3D_FLIPBOOK_VERSION);
		
		// wp_enqueue_script("compatibility", plugins_url()."/real3d-flipbook/js/compatibility.js", array('jquery'),REAL3D_FLIPBOOK_VERSION);
		// wp_enqueue_script("pdfjsworker", plugins_url()."/real3d-flipbook/js/pdf.worker.js", array('compatibility'),REAL3D_FLIPBOOK_VERSION);
		// wp_enqueue_script("pdfjs", plugins_url()."/real3d-flipbook/js/pdf.js", array('pdfjsworker'),REAL3D_FLIPBOOK_VERSION);
		
		wp_enqueue_script("read3d_flipbook", plugins_url()."/real3d-flipbook/js/flipbook.min.js", array('jquery'),REAL3D_FLIPBOOK_VERSION);
		
		wp_enqueue_style( 'flipbook_style', plugins_url()."/real3d-flipbook/css/flipbook.style.css" , array(),REAL3D_FLIPBOOK_VERSION);
		wp_enqueue_style( 'font_awesome', plugins_url()."/real3d-flipbook/css/font-awesome.css" , array(),REAL3D_FLIPBOOK_VERSION);
		//embed script
		wp_enqueue_script("embed", plugins_url()."/real3d-flipbook/js/embed.js", array('read3d_flipbook'),REAL3D_FLIPBOOK_VERSION);
		
	}
	add_action( 'wp_enqueue_scripts', 'real3d_flipbook_scripts' );
	
	function real3d_flipbook_admin_scripts() {
		// wp_enqueue_media();
		// wp_enqueue_script("read3d_flipbook_admin", plugins_url()."/real3d-flipbook/js/plugin_admin.js", array('jquery','jquery-ui-sortable','jquery-ui-resizable','jquery-ui-selectable','jquery-ui-tabs' ),REAL3D_FLIPBOOK_VERSION);
		// wp_enqueue_style( 'read3d_flipbook_admin_css', plugins_url()."/real3d-flipbook/css/flipbook-admin.css",array(), REAL3D_FLIPBOOK_VERSION );
		// wp_enqueue_style( 'jquery-ui-style', plugins_url()."/real3d-flipbook/css/jquery-ui.css",array(), REAL3D_FLIPBOOK_VERSION );
		// pass $flipbooks to javascript
		// wp_localize_script( 'read3d_flipbook_admin', 'options', json_encode($flipbooks[$current_id]) );
	}
	add_action( 'wp_enqueue_scripts', 'real3d_flipbook_admin_scripts' );
	
	function real3d_flipbook_admin_link($links) {
		array_unshift($links, '<a href="' . get_admin_url() . 'options-general.php?page=real3d_flipbook_admin">Admin</a>');
		return $links;
	}
	
	function deactivate_real3d_flipbook() {
		// delete_option("flipbooks");
	}