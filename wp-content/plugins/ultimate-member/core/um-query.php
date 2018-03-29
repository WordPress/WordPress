<?php

class UM_Query {

	public $wp_pages = array();
	public $roles = array();

	function __construct() {

		add_action('wp_loaded', array(&$this, 'get_post_types'), 100 );

	}

	/***
	***	@get wp pages
	***/
	function wp_pages() {
		global $wpdb;

		if( isset( $this->wp_pages ) && ! empty( $this->wp_pages ) ){
			return $this->wp_pages;
		}

		$count_pages = wp_count_posts('page');

		if ( $count_pages->publish > 300 ){
			return 'reached_maximum_limit';
		}


		$pages = $wpdb->get_results('SELECT * FROM '.$wpdb->posts.' WHERE post_type = "page" AND post_status = "publish" ', OBJECT);

		$array = array();
		if( $wpdb->num_rows > 0 ){
			foreach ($pages as $page_data) {
				$array[ $page_data->ID ] = $page_data->post_title;
			}
		}

		$this->wp_pages = $array;

		return $array;
	}

	/***
	***	@get all forms
	***/
	function forms() {

		$args = array(
			'post_type' => 'um_form',
			'posts_per_page' => 200,
			'post_status' => array('publish')
		);

		$query = new WP_Query( $args );
		foreach( $query->posts as $post ) {
			setup_postdata( $post );
			$results[ $post->ID ] = $post->post_title;
		}
		return $results;

	}

	/***
	***	@get all post types
	***/
	function get_post_types() {
		$array = get_post_types();
		foreach( $array as $k => $v ) {
			$this->get_post_types[] = $k;
		}
	}

	/***
	***	@Do custom queries
	***/
	function make( $args ) {

		$defaults = array(
			'post_type' => 'post',
			'post_status' => array('publish')
		);
		$args = wp_parse_args( $args, $defaults );

		if ( isset( $args['post__in'] ) && empty( $args['post__in'] ) )
			return false;

		extract( $args );

		if ( $post_type == 'comment' ) { // comments

			unset( $args['post_type'] );

			$args['type__not_in'] = apply_filters( 'um_excluded_comment_types', array('') );

			$comments = get_comments($args);
			return $comments;

		} else {

			$custom_posts = new WP_Query();
			$custom_posts->query( $args );
			return $custom_posts;

		}

	}

	/***
	***	@Get last users
	***/
	function get_recent_users($number = 5){
		global $wpdb;

		$args = array( 'fields' => 'ID', 'number' => $number, 'orderby' => 'user_registered', 'order' => 'desc' );

		$users = new WP_User_Query( $args );
		return $users->results;
	}

	/***
	***	@Count users by status
	***/
	function count_users_by_status( $status ) {
		$args = array( 'fields' => 'ID', 'number' => 0 );
		if ( $status == 'unassigned' ) {
			$args['meta_query'][] = array(array('key' => 'account_status','compare' => 'NOT EXISTS'));
			$users = new WP_User_Query( $args );
			foreach( $users->results as $user ) {
				update_user_meta( $user, 'account_status', 'approved' );
			}
		} else {
			$args['meta_query'][] = array(array('key' => 'account_status','value' => $status,'compare' => '='));
		}
		$users = new WP_User_Query( $args );
		return count($users->results);
	}

	/***
	***	@Get users by status
	***/
	function get_users_by_status($status, $number = 5){
		global $wpdb;

		$args = array( 'fields' => 'ID', 'number' => $number, 'orderby' => 'user_registered', 'order' => 'desc' );

		$args['meta_query'][] = array(
			array(
				'key'     => 'account_status',
				'value'   => $status,
				'compare' => '='
			)
		);

		$users = new WP_User_Query( $args );
		return $users->results;
	}

	/***
	***	@get user's role
	***/
	function get_role_by_userid( $user_id ) {
		$role = get_user_meta( $user_id, 'role', true );
		if ( !$role ) {
			if ( $user_id == get_current_user_id() && current_user_can('edit_users') ) {
				$role = 'admin';
			} else {
				$role = 'member';
			}
		}
		return $role;
	}

	/***
	***	@Count all users
	***/
	function count_users(){
		$result = count_users();
		return $result['total_users'];
	}

	/***
	***	@Count users of specific role
	***/
	function count_users_by_role($role){
		global $wpdb;

		$args['fields'] = 'ID';
		$args['meta_query'] = array(
			array(
				'key'     => 'role',
				'value'   => $role,
				'compare' => '='
			)
		);

		$users = new WP_User_Query( $args );

		return count($users->results);
	}

	/***
	***	@Using wpdb instead of update_post_meta
	***/
	function update_attr( $key, $post_id, $new_value ){
		update_post_meta( $post_id, '_um_' . $key, $new_value );
	}

	/***
	***	@get data
	***/
	function get_attr( $key, $post_id ){
		$meta = get_post_meta( $post_id, '_um_' . $key, true );
		return $meta;
	}

	/***
	***	@delete data
	***/
	function delete_attr( $key, $post_id ){
		$meta = delete_post_meta( $post_id, '_um_' . $key );
		return $meta;
	}

	/***
	***	@Checks if post has a specific meta key
	***/
	function has_post_meta($key, $value=null, $post_id=null ){
		if (!$post_id){
			global $post;
			$post_id = $post->ID;
		}
		if ($value ){
			if ( get_post_meta($post_id, $key, true) == $value )
				return true;
		} else {
			if ( get_post_meta($post_id, $key, true) )
				return true;
		}
		return false;
	}

	/***
	***	@Get posts with specific meta key/value
	***/
	function find_post_id($post_type, $key, $value){
		$posts = get_posts( array( 'post_type' => $post_type, 'meta_key' => $key, 'meta_value' => $value ) );
		if ( isset($posts[0]) && !empty($posts) )
			return $posts[0]->ID;
		return false;
	}

	/***
	***	@Get role data
	***/
	function role_data( $role_slug ) {
		global $wpdb, $ultimatemember;

		if ( get_option("um_cached_role_{$role_slug}") ) {
			return get_option("um_cached_role_{$role_slug}");
		}

		if ($role_slug == 'admin' || $role_slug == 'member'){
			$try = $this->find_post_id('um_role','_um_core',$role_slug);
			if ( isset( $try ) ){
				$post_id = $try;
				$real_role_slug =  $role_slug;
			}
		} else {
			$post_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'um_role' AND post_name = '$role_slug'");
			$real_role_slug = $role_slug;
		}

		if ( isset($post_id) && $post_id != '' ){
			$meta = get_post_custom( $post_id );
			$array['role'] = $real_role_slug;
			foreach ($meta as $k => $v){
				if ( strstr($k, '_um_') ) {
					$k = str_replace('_um_', '', $k);
					$array[$k] = $v[0];
				}

			}
		} else {

			// no permissions, something wrong
			$array = $ultimatemember->setup->get_initial_permissions( $role_slug );

		}

		if ( !get_option("um_cached_role_{$role_slug}") ) {
			update_option("um_cached_role_{$role_slug}", $array );
		}

		return $array;
	}

	/***
	***	@Get post data
	***/
	function post_data( $post_id ) {
		$array['form_id'] = $post_id;
		$mode = $this->get_attr('mode', $post_id);
		$meta = get_post_custom( $post_id );
		foreach ($meta as $k => $v){
			if ( strstr($k, '_um_'.$mode.'_' ) ) {
				$k = str_replace('_um_'.$mode.'_', '', $k);
				$array[$k] = $v[0];
			} elseif ($k == '_um_mode'){
				$k = str_replace('_um_', '', $k);
				$array[$k] = $v[0];
			} elseif ( strstr($k, '_um_') ) {
				$k = str_replace('_um_', '', $k);
				$array[$k] = $v[0];
			}

		}

		foreach( $array as $k => $v ) {
			if ( strstr( $k, 'login_') || strstr( $k, 'register_' ) || strstr( $k, 'profile_' ) ){
				if ( $mode != 'directory' ) {
					unset($array[$k]);
				}
			}
		}
		return $array;
	}

	/***
	***	@Capture selected value
	***/
	function get_meta_value($key, $array_key=null, $fallback = null){
		global $post;
		$post_id = get_the_ID();
		$try = get_post_meta( $post_id, $key, true);

		if (isset($try) && !empty($try))
			if (is_array($try) && in_array($array_key, $try) ){
			return $array_key;
			} else if ( is_array( $try ) ) {
			return '';
			} else {
			return $try;
			}

		if ($fallback == 'na') {
			$fallback = 0;
			$none = '';
		} else {
			$none = 0;
		}
		return (!empty($fallback)) ? $fallback : $none;
	}

	/***
	***	@Checks if its a core page of UM
	***/
	function is_core( $post_id ){
		$is_core = get_post_meta($post_id, '_um_core', true);
		if ( $is_core != '' ) {
			return $is_core;
		} else {
			return false;
		}
	}

	/***
	***	@Query for UM roles
	***/
	function get_roles( $add_default = false, $exclude = null ){

		$exclude_str = '';

		if( ! is_null( $exclude ) && is_array( $exclude ) ){
			$exclude_str = implode('_', $exclude );
		}

		if( isset( $this->roles['is_add_default_'.$add_default ][ 'is_exclude_'.$exclude_str ] ) ){
			return $this->roles['is_add_default_'.$add_default ][ 'is_exclude_'.$exclude_str ];
		}

		$roles = array();

		if ( $add_default ) {
			$roles[0] = $add_default;
		}

		$args = array(
			'post_type' => 'um_role',
			'posts_per_page' => -1,
			'post_status' => array('publish')
		);

		$posts = get_posts($args);

		if ( $posts ){

			foreach($posts as $post) {

				if ( $this->is_core( $post->ID ) ){
					$roles[ $this->is_core( $post->ID ) ] = $post->post_title;
				} else {
					$roles[ $post->post_name ] = $post->post_title;
				}

			}

		} else {

			$roles['member'] = 'Member';
			$roles['admin'] = 'Admin';

		}

		if ( $exclude ) {
			foreach( $exclude as $role ) {
				unset($roles[$role]);
			}
		}

		$this->roles['is_add_default_'.$add_default ][ 'is_exclude_'.$exclude_str ] = $roles;

		return $roles;

	}

}
