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

	function add_role($role, $capabilities, $display_name) {
		$this->roles[$role] = array('name' => $display_name,
																'capabilities' => $capabilities);
		update_option($this->role_key, $this->roles);
		$this->role_objects[$role] = new WP_Role($role, $capabilities);
		$this->role_names[$role] = $display_name;
	}
	
	function remove_role($role) {
		if ( ! isset($this->role_objects[$role]) )
			return;
		
		unset($this->role_objects[$role]);
		unset($this->role_names[$role]);
		unset($this->roles[$role]);
		
		update_option($this->role_key, $this->roles);
	}

	function add_cap($role, $cap, $grant) {
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

	function is_role($caps)
	{
		return empty($this->role_names[$cap]);
	}	
}

class WP_Role {
	var $name;
	var $capabilities;

	function WP_Role($role, $capabilities) {
		$this->name = $role;
		$this->capabilities = $capabilities;
	}

	function add_cap($cap, $grant) {
		global $wp_roles;

		$this->capabilities[$cap] = $grant;
		$wp_roles->add_cap($this->name, $cap);
	}

	function remove_cap($cap) {
		global $wp_roles;

		unset($this->capabilities[$cap]);
		$wp_roles->remove_cap($this->name, $cap);
	}

	function has_cap($cap) {
		if ( !empty($this->capabilities[$cap]) )
			return $this->capabilities[$cap];
		else
			return false;
	}

}

class WP_User {
	var $data;
	var $id;
	var $caps;
	var $cap_key;
	var $roles;
	var $allcaps;

	function WP_User($id) {
		global $wp_roles, $table_prefix;
		$this->id = $id;
		$this->data = get_userdata($id);
		$this->cap_key = $table_prefix . 'capabilities';
		$this->caps = &$this->data->{$this->cap_key};
		$this->get_role_caps();
	}
	
	function get_role_caps() {
		global $wp_roles;
		//Filter out caps that are not role names and assign to $this->roles
		if(is_array($this->caps))
			$this->roles = array_filter($this->caps, array(&$wp_roles, 'is_role'));

		//Build $allcaps from role caps, overlay user's $caps
		$this->allcaps = array();
		foreach($this->roles as $role => $value) {
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
		if(!empty($this->roles[$role]) && (count($this->roles) > 1))
		unset($this->caps[$cap]);
		update_usermeta($this->id, $this->cap_key, $this->caps);
		$this->get_role_caps();
	}
	
	function set_role($role) {
		foreach($this->roles as $oldrole => $value)
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
	    $this->data->user_level = array_reduce(array_keys($this->allcaps), 	array(&$this, 'level_reduction'), 0);
	    update_usermeta($this->id, $table_prefix.'user_level', $this->data->user_level);
	}
	
	function add_cap($cap, $grant = true) {
		$this->caps[$cap] = $grant;
		update_usermeta($this->id, $this->cap_key, $this->caps);
	}

	function remove_cap($cap) {
		if(!empty($this->roles[$role])) return;
		unset($this->caps[$cap]);
		update_usermeta($this->id, $this->cap_key, $this->caps);
	}
	
	//has_cap(capability_or_role_name) or
	//has_cap('edit_post', post_id)
	function has_cap($cap) {
		global $wp_roles;

		if ( is_numeric($cap) )
			$cap = $this->translate_level_to_cap($cap);
		
		$args = array_slice(func_get_args(), 1);
		$args = array_merge(array($cap, $this->id), $args);
		$caps = call_user_func_array('map_meta_cap', $args);
		// Must have ALL requested caps
		foreach ($caps as $cap) {
			//echo "Checking cap $cap<br/>";
			if(empty($this->allcaps[$cap]) || !$this->allcaps[$cap])
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
			else
				// If the post is draft...
				$caps[] = 'edit_posts';
		} else {
			// The user is trying to edit someone else's post.
			$caps[] = 'edit_others_posts';
			// The post is published, extra cap required.
			if ($post->post_status == 'publish')
				$caps[] = 'edit_published_posts';
		}
		break;
	default:
		// If no meta caps match, return the original cap.
		$caps[] = $cap;
	}

	return $caps;
}

// Capability checking wrapper around the global $current_user object.
function current_user_can($capability) {
	global $current_user;

	$args = array_slice(func_get_args(), 1);
	$args = array_merge(array($capability), $args);

	if ( empty($current_user) )
		return false;

	return call_user_func_array(array(&$current_user, 'has_cap'), $args);
}

?>
