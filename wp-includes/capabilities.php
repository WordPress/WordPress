<?php

class WP_Roles {
	var $roles;

	var $role_objects = array();
	var $role_names = array();
	var $role_key;

	function WP_Roles() {
		global $table_prefix;
		$this->role_key = $table_prefix . 'user_roles';

		$this->roles = get_option($this->role_key);

		if ( empty($this->roles) )
			return;

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
		
		update_option($this->role_key, $this->roles);
	}

	function add_cap($role, $cap, $grant = true) {
		$this->roles[$role]['capabilities'][$cap] = $grant;
		update_option($this->role_key, $this->roles);
	}

	function remove_cap($role, $cap) {
		unset($this->roles[$role]['capabilities'][$cap]);
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
	var $id = 0;
	var $caps = array();
	var $cap_key;
	var $roles = array();
	var $allcaps = array();

	function WP_User($id, $name = '') {
		global $table_prefix;

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
		$this->cap_key = $table_prefix . 'capabilities';
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
		foreach($this->roles as $role) {
			$role = $wp_roles->get_role($role);
			$this->allcaps = array_merge($this->allcaps, $role->capabilities);
		}
		$this->allcaps = array_merge($this->allcaps, $this->caps);
	}
	
	function add_role($role) {
		$this->caps[$role] = true;
		update_usermeta($this->id, $this->cap_key, $this->caps);
		$this->get_role_caps();
		$this->update_user_level_from_caps();
	}
	
	function remove_role($role) {
		if ( empty($this->roles[$role]) || (count($this->roles) <= 1) )
			return;
		unset($this->caps[$role]);
		update_usermeta($this->id, $this->cap_key, $this->caps);
		$this->get_role_caps();
	}
	
	function set_role($role) {
		foreach($this->roles as $oldrole) 
			unset($this->caps[$oldrole]);
		$this->caps[$role] = true;
		$this->roles = array($role => true);
		update_usermeta($this->id, $this->cap_key, $this->caps);
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
	    global $table_prefix;
	    $this->user_level = array_reduce(array_keys($this->allcaps), 	array(&$this, 'level_reduction'), 0);
	    update_usermeta($this->id, $table_prefix.'user_level', $this->user_level);
	}
	
	function add_cap($cap, $grant = true) {
		$this->caps[$cap] = $grant;
		update_usermeta($this->id, $this->cap_key, $this->caps);
	}

	function remove_cap($cap) {
		if ( empty($this->caps[$cap]) ) return;
		unset($this->caps[$cap]);
		update_usermeta($this->id, $this->cap_key, $this->caps);
	}
	
	//has_cap(capability_or_role_name) or
	//has_cap('edit_post', post_id)
	function has_cap($cap) {
		if ( is_numeric($cap) )
			$cap = $this->translate_level_to_cap($cap);
		
		$args = array_slice(func_get_args(), 1);
		$args = array_merge(array($cap, $this->id), $args);
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
		// edit_post breaks down to edit_posts, edit_published_posts, or
		// edit_others_posts
	case 'edit_post':
		$author_data = get_userdata($user_id);
		//echo "post ID: {$args[0]}<br/>";
		$post = get_post($args[0]);
		$post_author_data = get_userdata($post->post_author);
		//echo "current user id : $user_id, post author id: " . $post_author_data->ID . "<br/>";
		// If the user is the author...
		if ($user_id == $post_author_data->ID) {
			// If the post is published...
			if ($post->post_status == 'publish')
				$caps[] = 'edit_published_posts';
			else if ($post->post_status == 'static')
				$caps[] = 'edit_pages';
			else
				// If the post is draft...
				$caps[] = 'edit_posts';
		} else {
			if ($post->post_status == 'static') {
				$caps[] = 'edit_pages';
				break;
			}

			// The user is trying to edit someone else's post.
			$caps[] = 'edit_others_posts';
			// The post is published, extra cap required.
			if ($post->post_status == 'publish')
				$caps[] = 'edit_published_posts';
		}
		break;
	case 'read_post':
		$post = get_post($args[0]);
		
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

//
// These are deprecated.  Use current_user_can().
//

/* returns true if $user_id can create a new post */
function user_can_create_post($user_id, $blog_id = 1, $category_id = 'None') {
	$author_data = get_userdata($user_id);
	return ($author_data->user_level > 1);
}

/* returns true if $user_id can create a new post */
function user_can_create_draft($user_id, $blog_id = 1, $category_id = 'None') {
	$author_data = get_userdata($user_id);
	return ($author_data->user_level >= 1);
}

/* returns true if $user_id can edit $post_id */
function user_can_edit_post($user_id, $post_id, $blog_id = 1) {
	$author_data = get_userdata($user_id);
	$post = get_post($post_id);
	$post_author_data = get_userdata($post->post_author);

	if ( (($user_id == $post_author_data->ID) && !($post->post_status == 'publish' &&  $author_data->user_level < 2))
	     || ($author_data->user_level > $post_author_data->user_level)
	     || ($author_data->user_level >= 10) ) {
		return true;
	} else {
		return false;
	}
}

/* returns true if $user_id can delete $post_id */
function user_can_delete_post($user_id, $post_id, $blog_id = 1) {
	// right now if one can edit, one can delete
	return user_can_edit_post($user_id, $post_id, $blog_id);
}

/* returns true if $user_id can set new posts' dates on $blog_id */
function user_can_set_post_date($user_id, $blog_id = 1, $category_id = 'None') {
	$author_data = get_userdata($user_id);
	return (($author_data->user_level > 4) && user_can_create_post($user_id, $blog_id, $category_id));
}

/* returns true if $user_id can edit $post_id's date */
function user_can_edit_post_date($user_id, $post_id, $blog_id = 1) {
	$author_data = get_userdata($user_id);
	return (($author_data->user_level > 4) && user_can_edit_post($user_id, $post_id, $blog_id));
}

/* returns true if $user_id can edit $post_id's comments */
function user_can_edit_post_comments($user_id, $post_id, $blog_id = 1) {
	// right now if one can edit a post, one can edit comments made on it
	return user_can_edit_post($user_id, $post_id, $blog_id);
}

/* returns true if $user_id can delete $post_id's comments */
function user_can_delete_post_comments($user_id, $post_id, $blog_id = 1) {
	// right now if one can edit comments, one can delete comments
	return user_can_edit_post_comments($user_id, $post_id, $blog_id);
}

function user_can_edit_user($user_id, $other_user) {
	$user  = get_userdata($user_id);
	$other = get_userdata($other_user);
	if ( $user->user_level > $other->user_level || $user->user_level > 8 || $user->ID == $other->ID )
		return true;
	else
		return false;
}

?>
