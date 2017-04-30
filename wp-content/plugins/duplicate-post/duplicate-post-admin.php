<?php
// Added by WarmStal
if(!is_admin())
return;

require_once (dirname(__FILE__).'/duplicate-post-options.php');

/**
 * Wrapper for the option 'duplicate_post_version'
 */
function duplicate_post_get_installed_version() {
	return get_option( 'duplicate_post_version' );
}

/**
 * Wrapper for the defined constant DUPLICATE_POST_CURRENT_VERSION
 */
function duplicate_post_get_current_version() {
	return DUPLICATE_POST_CURRENT_VERSION;
}

/**
 * Plugin upgrade
 */
add_action('admin_init','duplicate_post_plugin_upgrade');

function duplicate_post_plugin_upgrade() {
	$installed_version = duplicate_post_get_installed_version();

	if (empty($installed_version)) { // first install

		// Add capability to admin and editors

		// Get default roles
		$default_roles = array(
		3 => 'editor',
		8 => 'administrator',
		);

		// Cycle all roles and assign capability if its level >= duplicate_post_copy_user_level
		foreach ($default_roles as $level => $name){
			$role = get_role($name);
			if(!empty($role)) $role->add_cap( 'copy_posts' );
		}
			
		add_option('duplicate_post_copyexcerpt','1');
		add_option('duplicate_post_copyattachments','0');
		add_option('duplicate_post_copychildren','0');
		add_option('duplicate_post_copystatus','0');
		add_option('duplicate_post_taxonomies_blacklist',array());
		add_option('duplicate_post_show_row','1');
		add_option('duplicate_post_show_adminbar','1');
		add_option('duplicate_post_show_submitbox','1');
	} else if ( $installed_version==duplicate_post_get_current_version() ) { //re-install
		// do nothing
	} else { //upgrade form previous version
		// delete old, obsolete options
		delete_option('duplicate_post_admin_user_level');
		delete_option('duplicate_post_create_user_level');
		delete_option('duplicate_post_view_user_level');
		delete_option('dp_notice');

		/*
		 * Convert old userlevel option to new capability scheme
		 */

		// Get old duplicate_post_copy_user_level option
		$min_user_level = get_option('duplicate_post_copy_user_level');

		if (!empty($min_user_level)){
			// Get default roles
			$default_roles = array(
			1 => 'contributor',
			2 => 'author',
			3 => 'editor',
			8 => 'administrator',
			);

			// Cycle all roles and assign capability if its level >= duplicate_post_copy_user_level
			foreach ($default_roles as $level => $name){
				$role = get_role($name);
				if ($role && $min_user_level <= $level)
				$role->add_cap( 'copy_posts' );
			}

			// delete old option
			delete_option('duplicate_post_copy_user_level');
		}

		add_option('duplicate_post_copyexcerpt','1');
		add_option('duplicate_post_copyattachments','0');
		add_option('duplicate_post_copychildren','0');
		add_option('duplicate_post_copystatus','0');
		add_option('duplicate_post_taxonomies_blacklist',array());
		add_option('duplicate_post_show_row','1');
		add_option('duplicate_post_show_adminbar','1');
		add_option('duplicate_post_show_submitbox','1');
	}
	// Update version number
	update_option( 'duplicate_post_version', duplicate_post_get_current_version() );

}

if (get_option('duplicate_post_show_row') == 1){
	add_filter('post_row_actions', 'duplicate_post_make_duplicate_link_row',10,2);
	add_filter('page_row_actions', 'duplicate_post_make_duplicate_link_row',10,2);
}

/**
 * Add the link to action list for post_row_actions
 */
function duplicate_post_make_duplicate_link_row($actions, $post) {
	if (duplicate_post_is_current_user_allowed_to_copy()) {
		$actions['clone'] = '<a href="'.duplicate_post_get_clone_post_link( $post->ID , 'display', false).'" title="'
		. esc_attr(__("Clone this item", DUPLICATE_POST_I18N_DOMAIN))
		. '">' .  __('Clone', DUPLICATE_POST_I18N_DOMAIN) . '</a>';
		$actions['edit_as_new_draft'] = '<a href="'. duplicate_post_get_clone_post_link( $post->ID ) .'" title="'
		. esc_attr(__('Copy to a new draft', DUPLICATE_POST_I18N_DOMAIN))
		. '">' .  __('New Draft', DUPLICATE_POST_I18N_DOMAIN) . '</a>';
	}
	return $actions;
}

/**
 * Add a button in the post/page edit screen to create a clone
 */
if (get_option('duplicate_post_show_submitbox') == 1){
	add_action( 'post_submitbox_start', 'duplicate_post_add_duplicate_post_button' );
}

function duplicate_post_add_duplicate_post_button() {
	if ( isset( $_GET['post'] ) && duplicate_post_is_current_user_allowed_to_copy()) {
		?>
<div id="duplicate-action">
	<a class="submitduplicate duplication"
		href="<?php echo duplicate_post_get_clone_post_link( $_GET['post'] ) ?>"><?php _e('Copy to a new draft', DUPLICATE_POST_I18N_DOMAIN); ?>
	</a>
</div>
		<?php
	}
}

/**
 * Connect actions to functions
 */
add_action('admin_action_duplicate_post_save_as_new_post', 'duplicate_post_save_as_new_post');
add_action('admin_action_duplicate_post_save_as_new_post_draft', 'duplicate_post_save_as_new_post_draft');

/*
 * This function calls the creation of a new copy of the selected post (as a draft)
 * then redirects to the edit post screen
 */
function duplicate_post_save_as_new_post_draft(){
	duplicate_post_save_as_new_post('draft');
}

/*
 * This function calls the creation of a new copy of the selected post (by default preserving the original publish status)
 * then redirects to the post list
 */
function duplicate_post_save_as_new_post($status = ''){
	if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'duplicate_post_save_as_new_post' == $_REQUEST['action'] ) ) ) {
		wp_die(__('No post to duplicate has been supplied!', DUPLICATE_POST_I18N_DOMAIN));
	}

	// Get the original post
	$id = (isset($_GET['post']) ? $_GET['post'] : $_POST['post']);
	$post = get_post($id);

	// Copy the post and insert it
	if (isset($post) && $post!=null) {
		$new_id = duplicate_post_create_duplicate($post, $status);

		if ($status == ''){
			// Redirect to the post list screen
			wp_redirect( admin_url( 'edit.php?post_type='.$post->post_type) );
		} else {
			// Redirect to the edit screen for the new draft post
			wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_id ) );
		}
		exit;

	} else {
		$post_type_obj = get_post_type_object( $post->post_type );
		wp_die(esc_attr(__('Copy creation failed, could not find original:', DUPLICATE_POST_I18N_DOMAIN)) . ' ' . htmlspecialchars($id));
	}
}

/**
 * Get the currently registered user
 */
function duplicate_post_get_current_user() {
	if (function_exists('wp_get_current_user')) {
		return wp_get_current_user();
	} else if (function_exists('get_currentuserinfo')) {
		global $userdata;
		get_currentuserinfo();
		return $userdata;
	} else {
		$user_login = $_COOKIE[USER_COOKIE];
		$sql = $wpdb->prepare("SELECT * FROM $wpdb->users WHERE user_login=%s", $user_login);
		$current_user = $wpdb->get_results($sql);			
		return $current_user;
	}
}

/**
 * Copy the taxonomies of a post to another post
 */
function duplicate_post_copy_post_taxonomies($new_id, $post) {
	global $wpdb;
	if (isset($wpdb->terms)) {
		// Clear default category (added by wp_insert_post)
		wp_set_object_terms( $new_id, NULL, 'category' );

		$post_taxonomies = get_object_taxonomies($post->post_type);
		$taxonomies_blacklist = get_option('duplicate_post_taxonomies_blacklist');
		if ($taxonomies_blacklist == "") $taxonomies_blacklist = array();
		$taxonomies = array_diff($post_taxonomies, $taxonomies_blacklist);
		foreach ($taxonomies as $taxonomy) {
			$post_terms = wp_get_object_terms($post->ID, $taxonomy, array( 'orderby' => 'term_order' ));
			$terms = array();
			for ($i=0; $i<count($post_terms); $i++) {
				$terms[] = $post_terms[$i]->slug;
			}
			wp_set_object_terms($new_id, $terms, $taxonomy);
		}
	}
}

// Using our action hooks to copy taxonomies
add_action('dp_duplicate_post', 'duplicate_post_copy_post_taxonomies', 10, 2);
add_action('dp_duplicate_page', 'duplicate_post_copy_post_taxonomies', 10, 2);

/**
 * Copy the meta information of a post to another post
 */
function duplicate_post_copy_post_meta_info($new_id, $post) {
	$post_meta_keys = get_post_custom_keys($post->ID);
	if (empty($post_meta_keys)) return;
	$meta_blacklist = explode(",",get_option('duplicate_post_blacklist'));
	if ($meta_blacklist == "") $meta_blacklist = array();
	$meta_keys = array_diff($post_meta_keys, $meta_blacklist);

	foreach ($meta_keys as $meta_key) {
		$meta_values = get_post_custom_values($meta_key, $post->ID);
		foreach ($meta_values as $meta_value) {
			$meta_value = maybe_unserialize($meta_value);
			add_post_meta($new_id, $meta_key, $meta_value);
		}
	}
}

// Using our action hooks to copy meta fields
add_action('dp_duplicate_post', 'duplicate_post_copy_post_meta_info', 10, 2);
add_action('dp_duplicate_page', 'duplicate_post_copy_post_meta_info', 10, 2);

/**
 * Copy the attachments
 * It simply copies the table entries, actual file won't be duplicated
 */
function duplicate_post_copy_children($new_id, $post){
	$copy_attachments = get_option('duplicate_post_copyattachments');
	$copy_children = get_option('duplicate_post_copychildren');

	// get children
	$children = get_posts(array( 'post_type' => 'any', 'numberposts' => -1, 'post_status' => 'any', 'post_parent' => $post->ID ));
	// clone old attachments
	foreach($children as $child){
		if ($copy_attachments == 0 && $child->post_type == 'attachment') continue;
		if ($copy_children == 0 && $child->post_type != 'attachment') continue;
		duplicate_post_create_duplicate($child, '', $new_id);
	}
}
// Using our action hooks to copy attachments
add_action('dp_duplicate_post', 'duplicate_post_copy_children', 10, 2);
add_action('dp_duplicate_page', 'duplicate_post_copy_children', 10, 2);


/**
 * Create a duplicate from a post
 */
function duplicate_post_create_duplicate($post, $status = '', $parent_id = '') {

	// We don't want to clone revisions
	if ($post->post_type == 'revision') return;

	if ($post->post_type != 'attachment'){
		$prefix = get_option('duplicate_post_title_prefix');
		$suffix = get_option('duplicate_post_title_suffix');
		if (!empty($prefix)) $prefix.= " ";
		if (!empty($suffix)) $suffix = " ".$suffix;
		if (get_option('duplicate_post_copystatus') == 0) $status = 'draft';
	}
	$new_post_author = duplicate_post_get_current_user();

	$new_post = array(
	'menu_order' => $post->menu_order,
	'comment_status' => $post->comment_status,
	'ping_status' => $post->ping_status,
	'post_author' => $new_post_author->ID,
	'post_content' => $post->post_content,
	'post_excerpt' => (get_option('duplicate_post_copyexcerpt') == '1') ? $post->post_excerpt : "",
	'post_mime_type' => $post->post_mime_type,
	'post_parent' => $new_post_parent = empty($parent_id)? $post->post_parent : $parent_id,
	'post_password' => $post->post_password,
	'post_status' => $new_post_status = (empty($status))? $post->post_status: $status,
	'post_title' => $prefix.$post->post_title.$suffix,
	'post_type' => $post->post_type,
	);

	if(get_option('duplicate_post_copydate') == 1){
		$new_post['post_date'] = $new_post_date =  $post->post_date ;
		$new_post['post_date_gmt'] = get_gmt_from_date($new_post_date);
	}

	$new_post_id = wp_insert_post($new_post);

	// If the copy is published or scheduled, we have to set a proper slug.
	if ($new_post_status == 'publish' || $new_post_status == 'future'){
		$post_name = wp_unique_post_slug($post->post_name, $new_post_id, $new_post_status, $post->post_type, $new_post_parent);

		$new_post = array();
		$new_post['ID'] = $new_post_id;
		$new_post['post_name'] = $post_name;

		// Update the post into the database
		wp_update_post( $new_post );
	}

	// If you have written a plugin which uses non-WP database tables to save
	// information about a post you can hook this action to dupe that data.
	if ($post->post_type == 'page' || (function_exists('is_post_type_hierarchical') && is_post_type_hierarchical( $post->post_type )))
	do_action( 'dp_duplicate_page', $new_post_id, $post );
	else
	do_action( 'dp_duplicate_post', $new_post_id, $post );

	delete_post_meta($new_post_id, '_dp_original');
	add_post_meta($new_post_id, '_dp_original', $post->ID);

	return $new_post_id;
}

//Add some links on the plugin page
add_filter('plugin_row_meta', 'duplicate_post_add_plugin_links', 10, 2);

function duplicate_post_add_plugin_links($links, $file) {
	if ( $file == plugin_basename(dirname(__FILE__).'/duplicate-post.php') ) {
		$links[] = '<a href="http://lopo.it/duplicate-post-plugin">' . __('Donate', DUPLICATE_POST_I18N_DOMAIN) . '</a>';
		$links[] = '<a href="http://lopo.it/duplicate-post-plugin">' . __('Translate', DUPLICATE_POST_I18N_DOMAIN) . '</a>';
	}
	return $links;
}
?>