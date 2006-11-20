<?php

class WP_Roles {
	var $roles;

	var $role_objects = array();
	var $role_names = array();
	var $role_key;
	var $use_db = true;

	function WP_Roles() {
		$this->_init();
	}

	function _init () {
		global $wpdb;
		global $wp_user_roles;
		$this->role_key = $wpdb->prefix . 'user_roles';
		if ( ! empty($wp_user_roles) ) {
			$this->roles = $wp_user_roles;
			$this->use_db = false;
		} else {
			$this->roles = get_option($this->role_key);
		}

		if ( empty($this->roles) )
			return;

		$this->role_objects = array();
		$this->role_names =  array();
		foreach ($this->roles as $role => $data) {
			$this->role_objects[$role] = new WP_Role($role, $this->roles[$role]['capabilities']);
			$this->role_names[$role] = $this->roles[$role]['name'];
		}
	}

	function add_role($role, $display_name, $capabilities = '') {
		if ( isset($this->roles[$role]) )
			return;

		$this->roles[$role] = array(
			'name' => $display_name,
			'capabilities' => $capabilities);
		if ( $this->use_db )
			update_option($this->role_key, $this->roles);
		$this->role_objects[$role] = new WP_Role($role, $capabilities);
		$this->role_names[$role] = $display_name;
		return $this->role_objects[$role];
	}

	function remove_role($role) {
		if ( ! isset($this->role_objects[$role]) )
			return;

		unset($this->role_objects[$role]);
		unset($this->role_names[$role]);
		unset($this->roles[$role]);
		
		if ( $this->use_db )
			update_option($this->role_key, $this->roles);
	}

	function add_cap($role, $cap, $grant = true) {
		$this->roles[$role]['capabilities'][$cap] = $grant;
		if ( $this->use_db )
			update_option($this->role_key, $this->roles);
	}

	function remove_cap($role, $cap) {
		unset($this->roles[$role]['capabilities'][$cap]);
		if ( $this->use_db )
			update_option($this->role_key, $this->roles);
	}

	function &get_role($role) {
		if ( isset($this->role_objects[$role]) )
			return $this->role_objects[$role];
		else
			return null;
	}

	function get_names() {
		return $this->role_names;
	}

	function is_role($role)
	{
		return isset($this->role_names[$role]);
	}
}

class WP_Role {
	var $name;
	var $capabilities;

	function WP_Role($role, $capabilities) {
		$this->name = $role;
		$this->capabilities = $capabilities;
	}

	function add_cap($cap, $grant = true) {
		global $wp_roles;

		if ( ! isset($wp_roles) )
			$wp_roles = new WP_Roles();

		$this->capabilities[$cap] = $grant;
		$wp_roles->add_cap($this->name, $cap, $grant);
	}

	function remove_cap($cap) {
		global $wp_roles;

		if ( ! isset($wp_roles) )
			$wp_roles = new WP_Roles();

		unset($this->capabilities[$cap]);
		$wp_roles->remove_cap($this->name, $cap);
	}

	function has_cap($cap) {
		$capabilities = apply_filters('role_has_cap', $this->capabilities, $cap, $this->name);
		if ( !empty($capabilities[$cap]) )
			return $capabilities[$cap];
		else
			return false;
	}

}

class WP_User {
	var $data;
	var $ID = 0;
	var $id = 0; // Deprecated, use $ID instead.
	var $caps = array();
	var $cap_key;
	var $roles = array();
	var $allcaps = array();

	function WP_User($id, $name = '') {
		global $wpdb;

		if ( empty($id) && empty($name) )
			return;

		if ( ! is_numeric($id) ) {
			$name = $id;
			$id = 0;
		}

		if ( ! empty($id) )
			$this->data = get_userdata($id);
		else
			$this->data = get_userdatabylogin($name);

		if ( empty($this->data->ID) )
			return;

		foreach (get_object_vars($this->data) as $key => $value) {
			$this->{$key} = $value;
		}

		$this->id = $this->ID;
		$this->_init_caps();
	}

	function _init_caps() {
		global $wpdb;
		$this->cap_key = $wpdb->prefix . 'capabilities';
		$this->caps = &$this->{$this->cap_key};
		if ( ! is_array($this->caps) )
			$this->caps = array();
		$this->get_role_caps();
	}

	function get_role_caps() {
		global $wp_roles;

		if ( ! isset($wp_roles) )
			$wp_roles = new WP_Roles();

		//Filter out caps that are not role names and assign to $this->roles
		if(is_array($this->caps))
			$this->roles = array_filter(array_keys($this->caps), array(&$wp_roles, 'is_role'));

		//Build $allcaps from role caps, overlay user's $caps
		$this->allcaps = array();
		foreach( (array) $this->roles as $role) {
			$role = $wp_roles->get_role($role);
			$this->allcaps = array_merge($this->allcaps, $role->capabilities);
		}
		$this->allcaps = array_merge($this->allcaps, $this->caps);
	}

	function add_role($role) {
		$this->caps[$role] = true;
		update_usermeta($this->ID, $this->cap_key, $this->caps);
		$this->get_role_caps();
		$this->update_user_level_from_caps();
	}

	function remove_role($role) {
		if ( empty($this->roles[$role]) || (count($this->roles) <= 1) )
			return;
		unset($this->caps[$role]);
		update_usermeta($this->ID, $this->cap_key, $this->caps);
		$this->get_role_caps();
	}

	function set_role($role) {
		foreach($this->roles as $oldrole)
			unset($this->caps[$oldrole]);
		if ( !empty($role) ) {
			$this->caps[$role] = true;
			$this->roles = array($role => true);
		} else {
			$this->roles = false;
		}
		update_usermeta($this->ID, $this->cap_key, $this->caps);
		$this->get_role_caps();
		$this->update_user_level_from_caps();
	}

	function level_reduction($max, $item) {
		if(preg_match('/^level_(10|[0-9])$/i', $item, $matches)) {
			$level = intval($matches[1]);
			return max($max, $level);
		} else {
			return $max;
		}
	}

	function update_user_level_from_caps() {
		global $wpdb;
		$this->user_level = array_reduce(array_keys($this->allcaps), 	array(&$this, 'level_reduction'), 0);
		update_usermeta($this->ID, $wpdb->prefix.'user_level', $this->user_level);
	}

	function add_cap($cap, $grant = true) {
		$this->caps[$cap] = $grant;
		update_usermeta($this->ID, $this->cap_key, $this->caps);
	}

	function remove_cap($cap) {
		if ( empty($this->caps[$cap]) ) return;
		unset($this->caps[$cap]);
		update_usermeta($this->ID, $this->cap_key, $this->caps);
	}

	function remove_all_caps() {
		global $wpdb;
		$this->caps = array();
		update_usermeta($this->ID, $this->cap_key, '');
		update_usermeta($this->ID, $wpdb->prefix.'user_level', '');
		$this->get_role_caps();
	}

	//has_cap(capability_or_role_name) or
	//has_cap('edit_post', post_id)
	function has_cap($cap) {
		if ( is_numeric($cap) )
			$cap = $this->translate_level_to_cap($cap);

		$args = array_slice(func_get_args(), 1);
		$args = array_merge(array($cap, $this->ID), $args);
		$caps = call_user_func_array('map_meta_cap', $args);
		// Must have ALL requested caps
		$capabilities = apply_filters('user_has_cap', $this->allcaps, $caps, $args);
		foreach ($caps as $cap) {
			//echo "Checking cap $cap<br/>";
			if(empty($capabilities[$cap]) || !$capabilities[$cap])
				return false;
		}

		return true;
	}

	function translate_level_to_cap($level) {
		return 'level_' . $level;
	}

}

// Map meta capabilities to primitive capabilities.
function map_meta_cap($cap, $user_id) {
	$args = array_slice(func_get_args(), 2);
	$caps = array();

	switch ($cap) {
	case 'delete_user':
		$caps[] = 'delete_users';
		break;
	case 'edit_user':
		$caps[] = 'edit_users';
		break;
	case 'delete_post':
		$author_data = get_userdata($user_id);
		//echo "post ID: {$args[0]}<br/>";
		$post = get_post($args[0]);
		if ( 'page' == $post->post_type ) {
			$args = array_merge(array('delete_page', $user_id), $args);
			return call_user_func_array('map_meta_cap', $args);
		}
		$post_author_data = get_userdata($post->post_author);
		//echo "current user id : $user_id, post author id: " . $post_author_data->ID . "<br/>";
		// If the user is the author...
		if ($user_id == $post_author_data->ID) {
			// If the post is published...
			if ($post->post_status == 'publish')
				$caps[] = 'delete_published_posts';
			else
				// If the post is draft...
				$caps[] = 'delete_posts';
		} else {
			// The user is trying to edit someone else's post.
			$caps[] = 'delete_others_posts';
			// The post is published, extra cap required.
			if ($post->post_status == 'publish')
				$caps[] = 'delete_published_posts';
			else if ($post->post_status == 'private')
				$caps[] = 'delete_private_posts';
		}
		break;
	case 'delete_page':
		$author_data = get_userdata($user_id);
		//echo "post ID: {$args[0]}<br/>";
		$page = get_page($args[0]);
		$page_author_data = get_userdata($page->post_author);
		//echo "current user id : $user_id, page author id: " . $page_author_data->ID . "<br/>";
		// If the user is the author...
		if ($user_id == $page_author_data->ID) {
			// If the page is published...
			if ($page->post_status == 'publish')
				$caps[] = 'delete_published_pages';
			else
				// If the page is draft...
				$caps[] = 'delete_pages';
		} else {
			// The user is trying to edit someone else's page.
			$caps[] = 'delete_others_pages';
			// The page is published, extra cap required.
			if ($page->post_status == 'publish')
				$caps[] = 'delete_published_pages';
			else if ($page->post_status == 'private')
				$caps[] = 'delete_private_pages';
		}
		break;
		// edit_post breaks down to edit_posts, edit_published_posts, or
		// edit_others_posts
	case 'edit_post':
		$author_data = get_userdata($user_id);
		//echo "post ID: {$args[0]}<br/>";
		$post = get_post($args[0]);
		if ( 'page' == $post->post_type ) {
			$args = array_merge(array('edit_page', $user_id), $args);
			return call_user_func_array('map_meta_cap', $args);
		}
		$post_author_data = get_userdata($post->post_author);
		//echo "current user id : $user_id, post author id: " . $post_author_data->ID . "<br/>";
		// If the user is the author...
		if ($user_id == $post_author_data->ID) {
			// If the post is published...
			if ($post->post_status == 'publish')
				$caps[] = 'edit_published_posts';
			else
				// If the post is draft...
				$caps[] = 'edit_posts';
		} else {
			// The user is trying to edit someone else's post.
			$caps[] = 'edit_others_posts';
			// The post is published, extra cap required.
			if ($post->post_status == 'publish')
				$caps[] = 'edit_published_posts';
			else if ($post->post_status == 'private')
				$caps[] = 'edit_private_posts';
		}
		break;
	case 'edit_page':
		$author_data = get_userdata($user_id);
		//echo "post ID: {$args[0]}<br/>";
		$page = get_page($args[0]);
		$page_author_data = get_userdata($page->post_author);
		//echo "current user id : $user_id, page author id: " . $page_author_data->ID . "<br/>";
		// If the user is the author...
		if ($user_id == $page_author_data->ID) {
			// If the page is published...
			if ($page->post_status == 'publish')
				$caps[] = 'edit_published_pages';
			else
				// If the page is draft...
				$caps[] = 'edit_pages';
		} else {
			// The user is trying to edit someone else's page.
			$caps[] = 'edit_others_pages';
			// The page is published, extra cap required.
			if ($page->post_status == 'publish')
				$caps[] = 'edit_published_pages';
			else if ($page->post_status == 'private')
				$caps[] = 'edit_private_pages';
		}
		break;
	case 'read_post':
		$post = get_post($args[0]);
		if ( 'page' == $post->post_type ) {
			$args = array_merge(array('read_page', $user_id), $args);
			return call_user_func_array('map_meta_cap', $args);
		}

		if ( 'private' != $post->post_status ) {
			$caps[] = 'read';
			break;
		}

		$author_data = get_userdata($user_id);
		$post_author_data = get_userdata($post->post_author);
		if ($user_id == $post_author_data->ID)
			$caps[] = 'read';
		else
			$caps[] = 'read_private_posts';
		break;
	case 'read_page':
		$page = get_page($args[0]);

		if ( 'private' != $page->post_status ) {
			$caps[] = 'read';
			break;
		}

		$author_data = get_userdata($user_id);
		$page_author_data = get_userdata($post->post_author);
		if ($user_id == $page_author_data->ID)
			$caps[] = 'read';
		else
			$caps[] = 'read_private_pages';
		break;
	default:
		// If no meta caps match, return the original cap.
		$caps[] = $cap;
	}

	return $caps;
}

// Capability checking wrapper around the global $current_user object.
function current_user_can($capability) {
	$current_user = wp_get_current_user();

	$args = array_slice(func_get_args(), 1);
	$args = array_merge(array($capability), $args);

	if ( empty($current_user) )
		return false;

	return call_user_func_array(array(&$current_user, 'has_cap'), $args);
}

// Convenience wrappers around $wp_roles.
function get_role($role) {
	global $wp_roles;

	if ( ! isset($wp_roles) )
		$wp_roles = new WP_Roles();

	return $wp_roles->get_role($role);
}

function add_role($role, $display_name, $capabilities = '') {
	global $wp_roles;

	if ( ! isset($wp_roles) )
		$wp_roles = new WP_Roles();

	return $wp_roles->add_role($role, $display_name, $capabilities);
}

function remove_role($role) {
	global $wp_roles;

	if ( ! isset($wp_roles) )
		$wp_roles = new WP_Roles();

	return $wp_roles->remove_role($role);
}

?>
