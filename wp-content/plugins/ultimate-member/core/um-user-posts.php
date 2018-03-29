<?php

class UM_User_posts {

	function __construct() {

		add_filter('um_profile_tabs', array(&$this, 'add_tab'), 100);
		
		add_action('um_profile_content_posts', array(&$this, 'add_posts') );
		add_action('um_profile_content_comments', array(&$this, 'add_comments') );
		
		add_action('um_ajax_load_posts__um_load_posts', array(&$this, 'load_posts') );
		add_action('um_ajax_load_posts__um_load_comments', array(&$this, 'load_comments') );
		
	}
	
	/***
	***	@dynamic load of posts
	***/
	function load_posts( $args ) {
		global $ultimatemember;
		
		$array = explode(',', $args );
		$post_type = $array[0];
		$posts_per_page = $array[1];
		$offset = $array[2];
		$author = $array[3];
		
		$offset_n = $posts_per_page + $offset;
		
		$ultimatemember->shortcodes->modified_args = "$post_type,$posts_per_page,$offset_n,$author";
		
		$ultimatemember->shortcodes->loop = $ultimatemember->query->make("post_type=$post_type&posts_per_page=$posts_per_page&offset=$offset&author=$author");
		
		$ultimatemember->shortcodes->load_template('profile/posts-single');
		
	}
	
	/***
	***	@dynamic load of comments
	***/
	function load_comments( $args ) {
		global $ultimatemember;
		
		$array = explode(',', $args );
		$post_type = $array[0];
		$posts_per_page = $array[1];
		$offset = $array[2];
		$author = $array[3];

		$offset_n = $posts_per_page + $offset;
		
		$ultimatemember->shortcodes->modified_args = "$post_type,$posts_per_page,$offset_n,$author";
		
		$ultimatemember->shortcodes->loop = $ultimatemember->query->make("post_type=$post_type&number=$posts_per_page&offset=$offset&user_id=$author");

		$ultimatemember->shortcodes->load_template('profile/comments-single');
		
	}
	
	/***
	***	@adds a tab
	***/
	function add_tab( $tabs ){
		
		$tabs['posts'] = array(
			'name' => __('Posts','ultimate-member'),
			'icon' => 'um-faicon-pencil',
		);
		
		$tabs['comments'] = array(
			'name' => __('Comments','ultimate-member'),
			'icon' => 'um-faicon-comment',
		);
		
		return $tabs;
	}
	
	/***
	***	@add posts
	***/
	function add_posts() {
		global $ultimatemember;
		$ultimatemember->shortcodes->load_template('profile/posts');

	}
	
	/***
	***	@add comments
	***/
	function add_comments() {
		global $ultimatemember;
		$ultimatemember->shortcodes->load_template('profile/comments');
	}
	
	/***
	***	@count posts
	***/
	function count_user_posts_by_type( $user_id= '', $post_type = 'post' ) {
		global $wpdb;
		if ( !$user_id )
			$user_id = um_user('ID');
		
		if ( !$user_id ) return 0;
		
		$where = get_posts_by_author_sql( $post_type, true, $user_id );
		$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts $where" );
		
		return apply_filters('um_pretty_number_formatting', $count);
	}
	
	/***
	***	@count comments
	***/
	function count_user_comments( $user_id = null ) {
		global $wpdb;
		if ( !$user_id )
			$user_id = um_user('ID');
		
		if ( !$user_id ) return 0;

		$count = $wpdb->get_var("SELECT COUNT(comment_ID) FROM " . $wpdb->comments. " WHERE user_id = " . $user_id . " AND comment_approved = '1'");
		
		return apply_filters('um_pretty_number_formatting', $count);
	}

}