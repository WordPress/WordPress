<?php
/**
 * WordPress Upgrade API
 *
 * Most of the functions are pluggable and can be overwritten
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Include user install customize script. */
if ( file_exists(WP_CONTENT_DIR . '/install.php') )
	require (WP_CONTENT_DIR . '/install.php');

/** WordPress Administration API */
require_once(ABSPATH . 'wp-admin/includes/admin.php');

/** WordPress Schema API */
require_once(ABSPATH . 'wp-admin/includes/schema.php');

if ( !function_exists('wp_install') ) :
/**
 * Installs the blog
 *
 * {@internal Missing Long Description}}
 *
 * @since unknown
 *
 * @param string $blog_title Blog title.
 * @param string $user_name User's username.
 * @param string $user_email User's email.
 * @param bool $public Whether blog is public.
 * @param null $deprecated Optional. Not used.
 * @return array Array keys 'url', 'user_id', 'password', 'password_message'.
 */
function wp_install($blog_title, $user_name, $user_email, $public, $deprecated='') {
	global $wp_rewrite;

	wp_check_mysql_version();
	wp_cache_flush();
	make_db_current_silent();
	populate_options();
	populate_roles();

	update_option('blogname', $blog_title);
	update_option('admin_email', $user_email);
	update_option('blog_public', $public);

	$guessurl = wp_guess_url();

	update_option('siteurl', $guessurl);

	// If not a public blog, don't ping.
	if ( ! $public )
		update_option('default_pingback_flag', 0);

	// Create default user.  If the user already exists, the user tables are
	// being shared among blogs.  Just set the role in that case.
	$user_id = username_exists($user_name);
	if ( !$user_id ) {
		$random_password = wp_generate_password();
		$message = __('<strong><em>Note that password</em></strong> carefully! It is a <em>random</em> password that was generated just for you.');
		$user_id = wp_create_user($user_name, $random_password, $user_email);
		update_usermeta($user_id, 'default_password_nag', true);
	} else {
		$random_password = '';
		$message =  __('User already exists.  Password inherited.');
	}

	$user = new WP_User($user_id);
	$user->set_role('administrator');

	wp_install_defaults($user_id);

	$wp_rewrite->flush_rules();

	wp_new_blog_notification($blog_title, $guessurl, $user_id, $random_password);

	wp_cache_flush();

	return array('url' => $guessurl, 'user_id' => $user_id, 'password' => $random_password, 'password_message' => $message);
}
endif;

if ( !function_exists('wp_install_defaults') ) :
/**
 * {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @since unknown
 *
 * @param int $user_id User ID.
 */
function wp_install_defaults($user_id) {
	global $wpdb;

	// Default category
	$cat_name = __('Uncategorized');
	/* translators: Default category slug */
	$cat_slug = sanitize_title(_x('Uncategorized', 'Default category slug'));

	$wpdb->insert( $wpdb->terms, array('name' => $cat_name, 'slug' => $cat_slug, 'term_group' => 0) );
	$wpdb->insert( $wpdb->term_taxonomy, array('term_id' => '1', 'taxonomy' => 'category', 'description' => '', 'parent' => 0, 'count' => 1));

	// Default link category
	$cat_name = __('Blogroll');
	/* translators: Default link category slug */
	$cat_slug = sanitize_title(_x('Blogroll', 'Default link category slug'));

	$wpdb->insert( $wpdb->terms, array('name' => $cat_name, 'slug' => $cat_slug, 'term_group' => 0) );
	$wpdb->insert( $wpdb->term_taxonomy, array('term_id' => '2', 'taxonomy' => 'link_category', 'description' => '', 'parent' => 0, 'count' => 7));

	// Now drop in some default links
	$default_links = array();
	$default_links[] = array(	'link_url' => 'http://codex.wordpress.org/',
								'link_name' => 'Documentation',
								'link_rss' => '',
								'link_notes' => '');

	$default_links[] = array(	'link_url' => 'http://wordpress.org/development/',
								'link_name' => 'Development Blog',
								'link_rss' => 'http://wordpress.org/development/feed/',
								'link_notes' => '');

	$default_links[] = array(	'link_url' => 'http://wordpress.org/extend/ideas/',
								'link_name' => 'Suggest Ideas',
								'link_rss' => '',
								'link_notes' =>'');

	$default_links[] = array(	'link_url' => 'http://wordpress.org/support/',
								'link_name' => 'Support Forum',
								'link_rss' => '',
								'link_notes' =>'');

	$default_links[] = array(	'link_url' => 'http://wordpress.org/extend/plugins/',
								'link_name' => 'Plugins',
								'link_rss' => '',
								'link_notes' =>'');

	$default_links[] = array(	'link_url' => 'http://wordpress.org/extend/themes/',
								'link_name' => 'Themes',
								'link_rss' => '',
								'link_notes' =>'');

	$default_links[] = array(	'link_url' => 'http://planet.wordpress.org/',
								'link_name' => 'WordPress Planet',
								'link_rss' => '',
								'link_notes' =>'');

	foreach ( $default_links as $link ) {
		$wpdb->insert( $wpdb->links, $link);
		$wpdb->insert( $wpdb->term_relationships, array('term_taxonomy_id' => 2, 'object_id' => $wpdb->insert_id) );
	}

	// First post
	$now = date('Y-m-d H:i:s');
	$now_gmt = gmdate('Y-m-d H:i:s');
	$first_post_guid = get_option('home') . '/?p=1';

	$wpdb->insert( $wpdb->posts, array(
								'post_author' => $user_id,
								'post_date' => $now,
								'post_date_gmt' => $now_gmt,
								'post_content' => sprintf(__('
<p>Welcome to WordPress!  This post contains important information.  After you read it, you can make it private to hide it from visitors but still have the information handy for future reference.</p>
<p>First things first:</p>
<ul>
<li><a href="%1$s" title="Subscribe to the WordPress mailing list for Release Notifications">Subscribe to the WordPress mailing list for release notifications</a></li>
</ul>
<p>As a subscriber, you will receive an email every time an update is available (and only then).  This will make it easier to keep your site up to date, and secure from evildoers.
When a new version is released, <a href="%2$s" title="If you are already logged in, this will take you directly to the Dashboard">log in to the Dashboard</a> and follow the instructions.
Upgrading is a couple of clicks!</p>
<p>Then you can start enjoying the WordPress experience:</p>
<ul>
<li>Edit your personal information at <a href="%3$s" title="Edit settings like your password, your display name and your contact information">Users &#8250; Your Profile</a></li>
<li>Start publishing at <a href="%4$s" title="Create a new post">Posts &#8250; Add New</a> and at <a href="%5$s" title="Create a new page">Pages &#8250; Add New</a></li>
<li>Browse and install plugins at <a href="%6$s" title="Browse and install plugins at the official WordPress repository directly from your Dashboard">Plugins &#8250; Add New</a></li>
<li>Browse and install themes at <a href="%7$s" title="Browse and install themes at the official WordPress repository directly from your Dashboard">Appearance &#8250; Add New Themes</a></li>
<li>Modify and prettify your website&#8217;s links at <a href="%8$s" title="For example, select a link structure like: http://example.com/1999/12/post-name">Settings &#8250; Permalinks</a></li>
<li>Import content from another system or WordPress site at <a href="%9$s" title="WordPress comes with importers for the most common publishing systems">Tools &#8250; Import</a></li>
<li>Find answers to your questions at the <a href="%10$s" title="The official WordPress documentation, maintained by the WordPress community">WordPress Codex</a></li>
</ul>
<p>To keep this post for reference, <a href="%11$s" title="Click to edit the content and settings of this post">click to edit it</a>, go to the Publish box and change its Visibility from Public to Private.</p>
<p>Thank you for selecting WordPress.  We wish you happy publishing!</p>
<p>PS.  Not yet subscribed for update notifications?  <a href="%1$s" title="Subscribe to the WordPress mailing list for Release Notifications">Do it now!</a></p>
'), _c('http://wordpress.org/download/','url for release notification mailing list subscription.'), admin_url(''), admin_url('profile.php'), admin_url('post-new.php'), admin_url('page-new.php'),
admin_url('plugin-install.php'), admin_url('theme-install.php'), admin_url('options-permalink.php'), admin_url('import.php'), _c('http://codex.wordpress.org','url for codex documentation.'), admin_url('post.php?action=edit&post=1')),
								'post_excerpt' => '',
								'post_title' => __('Welcome!'),
								/* translators: Default post slug */
								'post_name' => _x('welcome', 'Default post slug'),
								'post_modified' => $now,
								'post_modified_gmt' => $now_gmt,
								'guid' => $first_post_guid,
								'comment_count' => 1,
								'to_ping' => '',
								'pinged' => '',
								'post_content_filtered' => ''
								));
	$wpdb->insert( $wpdb->term_relationships, array('term_taxonomy_id' => 1, 'object_id' => 1) );

	// Default comment
	$wpdb->insert( $wpdb->comments, array(
								'comment_post_ID' => 1,
								'comment_author' => __('Mr WordPress'),
								'comment_author_email' => '',
								'comment_author_url' => 'http://wordpress.org/',
								'comment_date' => $now,
								'comment_date_gmt' => $now_gmt,
								'comment_content' => __('Hi, this is a comment.<br />To delete a comment, just log in and view the post&#039;s comments. There you will have the option to edit or delete them.')
								));
	// First Page
	$first_post_guid = get_option('home') . '/?page_id=2';
	$wpdb->insert( $wpdb->posts, array(
								'post_author' => $user_id,
								'post_date' => $now,
								'post_date_gmt' => $now_gmt,
								'post_content' => __('This is an example of a WordPress page, you could edit this to put information about yourself or your site so readers know where you are coming from. You can create as many pages like this one or sub-pages as you like and manage all of your content inside of WordPress.'),
								'post_excerpt' => '',
								'post_title' => __('About'),
								/* translators: Default page slug */
								'post_name' => _x('about', 'Default page slug'),
								'post_modified' => $now,
								'post_modified_gmt' => $now_gmt,
								'guid' => $first_post_guid,
								'post_type' => 'page',
								'to_ping' => '',
								'pinged' => '',
								'post_content_filtered' => ''
								));
}
endif;

if ( !function_exists('wp_new_blog_notification') ) :
/**
 * {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @since unknown
 *
 * @param string $blog_title Blog title.
 * @param string $blog_url Blog url.
 * @param int $user_id User ID.
 * @param string $password User's Password.
 */
function wp_new_blog_notification($blog_title, $blog_url, $user_id, $password) {
	$user = new WP_User($user_id);
	$email = $user->user_email;
	$name = $user->user_login;
	$message = sprintf(__("Your new WordPress blog has been successfully set up at:

%1\$s

You can log in to the administrator account with the following information:

Username: %2\$s
Password: %3\$s

We hope you enjoy your new blog. Thanks!

--The WordPress Team
http://wordpress.org/
"), $blog_url, $name, $password);

	@wp_mail($email, __('New WordPress Blog'), $message);
}
endif;

if ( !function_exists('wp_upgrade') ) :
/**
 * Run WordPress Upgrade functions.
 *
 * {@internal Missing Long Description}}
 *
 * @since unknown
 *
 * @return null
 */
function wp_upgrade() {
	global $wp_current_db_version, $wp_db_version;

	$wp_current_db_version = __get_option('db_version');

	// We are up-to-date.  Nothing to do.
	if ( $wp_db_version == $wp_current_db_version )
		return;

	if( ! is_blog_installed() )
		return;

	wp_check_mysql_version();
	wp_cache_flush();
	pre_schema_upgrade();
	make_db_current_silent();
	upgrade_all();
	wp_cache_flush();
}
endif;

/**
 * Functions to be called in install and upgrade scripts.
 *
 * {@internal Missing Long Description}}
 *
 * @since unknown
 */
function upgrade_all() {
	global $wp_current_db_version, $wp_db_version, $wp_rewrite;
	$wp_current_db_version = __get_option('db_version');

	// We are up-to-date.  Nothing to do.
	if ( $wp_db_version == $wp_current_db_version )
		return;

	// If the version is not set in the DB, try to guess the version.
	if ( empty($wp_current_db_version) ) {
		$wp_current_db_version = 0;

		// If the template option exists, we have 1.5.
		$template = __get_option('template');
		if ( !empty($template) )
			$wp_current_db_version = 2541;
	}

	if ( $wp_current_db_version < 6039 )
		upgrade_230_options_table();

	populate_options();

	if ( $wp_current_db_version < 2541 ) {
		upgrade_100();
		upgrade_101();
		upgrade_110();
		upgrade_130();
	}

	if ( $wp_current_db_version < 3308 )
		upgrade_160();

	if ( $wp_current_db_version < 4772 )
		upgrade_210();

	if ( $wp_current_db_version < 4351 )
		upgrade_old_slugs();

	if ( $wp_current_db_version < 5539 )
		upgrade_230();

	if ( $wp_current_db_version < 6124 )
		upgrade_230_old_tables();

	if ( $wp_current_db_version < 7499 )
		upgrade_250();

	if ( $wp_current_db_version < 7796 )
		upgrade_251();

	if ( $wp_current_db_version < 7935 )
		upgrade_252();

	if ( $wp_current_db_version < 8201 )
		upgrade_260();

	if ( $wp_current_db_version < 8989 )
		upgrade_270();

	if ( $wp_current_db_version < 10360 )
		upgrade_280();

	if ( $wp_current_db_version < 11958 )
		upgrade_290();

	maybe_disable_automattic_widgets();

	update_option( 'db_version', $wp_db_version );
	update_option( 'db_upgraded', true );
}

/**
 * Execute changes made in WordPress 1.0.
 *
 * @since 1.0.0
 */
function upgrade_100() {
	global $wpdb;

	// Get the title and ID of every post, post_name to check if it already has a value
	$posts = $wpdb->get_results("SELECT ID, post_title, post_name FROM $wpdb->posts WHERE post_name = ''");
	if ($posts) {
		foreach($posts as $post) {
			if ('' == $post->post_name) {
				$newtitle = sanitize_title($post->post_title);
				$wpdb->query( $wpdb->prepare("UPDATE $wpdb->posts SET post_name = %s WHERE ID = %d", $newtitle, $post->ID) );
			}
		}
	}

	$categories = $wpdb->get_results("SELECT cat_ID, cat_name, category_nicename FROM $wpdb->categories");
	foreach ($categories as $category) {
		if ('' == $category->category_nicename) {
			$newtitle = sanitize_title($category->cat_name);
			$wpdb>update( $wpdb->categories, array('category_nicename' => $newtitle), array('cat_ID' => $category->cat_ID) );
		}
	}

	$wpdb->query("UPDATE $wpdb->options SET option_value = REPLACE(option_value, 'wp-links/links-images/', 'wp-images/links/')
	WHERE option_name LIKE 'links_rating_image%'
	AND option_value LIKE 'wp-links/links-images/%'");

	$done_ids = $wpdb->get_results("SELECT DISTINCT post_id FROM $wpdb->post2cat");
	if ($done_ids) :
		foreach ($done_ids as $done_id) :
			$done_posts[] = $done_id->post_id;
		endforeach;
		$catwhere = ' AND ID NOT IN (' . implode(',', $done_posts) . ')';
	else:
		$catwhere = '';
	endif;

	$allposts = $wpdb->get_results("SELECT ID, post_category FROM $wpdb->posts WHERE post_category != '0' $catwhere");
	if ($allposts) :
		foreach ($allposts as $post) {
			// Check to see if it's already been imported
			$cat = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->post2cat WHERE post_id = %d AND category_id = %d", $post->ID, $post->post_category) );
			if (!$cat && 0 != $post->post_category) { // If there's no result
				$wpdb->insert( $wpdb->post2cat, array('post_id' => $post->ID, 'category_id' => $post->post_category) );
			}
		}
	endif;
}

/**
 * Execute changes made in WordPress 1.0.1.
 *
 * @since 1.0.1
 */
function upgrade_101() {
	global $wpdb;

	// Clean up indices, add a few
	add_clean_index($wpdb->posts, 'post_name');
	add_clean_index($wpdb->posts, 'post_status');
	add_clean_index($wpdb->categories, 'category_nicename');
	add_clean_index($wpdb->comments, 'comment_approved');
	add_clean_index($wpdb->comments, 'comment_post_ID');
	add_clean_index($wpdb->links , 'link_category');
	add_clean_index($wpdb->links , 'link_visible');
}

/**
 * Execute changes made in WordPress 1.2.
 *
 * @since 1.2.0
 */
function upgrade_110() {
	global $wpdb;

	// Set user_nicename.
	$users = $wpdb->get_results("SELECT ID, user_nickname, user_nicename FROM $wpdb->users");
	foreach ($users as $user) {
		if ('' == $user->user_nicename) {
			$newname = sanitize_title($user->user_nickname);
			$wpdb->update( $wpdb->users, array('user_nicename' => $newname), array('ID' => $user->ID) );
		}
	}

	$users = $wpdb->get_results("SELECT ID, user_pass from $wpdb->users");
	foreach ($users as $row) {
		if (!preg_match('/^[A-Fa-f0-9]{32}$/', $row->user_pass)) {
			$wpdb->update( $wpdb->users, array('user_pass' => md5($row->user_pass)), array('ID' => $row->ID) );
		}
	}

	// Get the GMT offset, we'll use that later on
	$all_options = get_alloptions_110();

	$time_difference = $all_options->time_difference;

	$server_time = time()+date('Z');
	$weblogger_time = $server_time + $time_difference*3600;
	$gmt_time = time();

	$diff_gmt_server = ($gmt_time - $server_time) / 3600;
	$diff_weblogger_server = ($weblogger_time - $server_time) / 3600;
	$diff_gmt_weblogger = $diff_gmt_server - $diff_weblogger_server;
	$gmt_offset = -$diff_gmt_weblogger;

	// Add a gmt_offset option, with value $gmt_offset
	add_option('gmt_offset', $gmt_offset);

	// Check if we already set the GMT fields (if we did, then
	// MAX(post_date_gmt) can't be '0000-00-00 00:00:00'
	// <michel_v> I just slapped myself silly for not thinking about it earlier
	$got_gmt_fields = ($wpdb->get_var("SELECT MAX(post_date_gmt) FROM $wpdb->posts") == '0000-00-00 00:00:00') ? false : true;

	if (!$got_gmt_fields) {

		// Add or substract time to all dates, to get GMT dates
		$add_hours = intval($diff_gmt_weblogger);
		$add_minutes = intval(60 * ($diff_gmt_weblogger - $add_hours));
		$wpdb->query("UPDATE $wpdb->posts SET post_date_gmt = DATE_ADD(post_date, INTERVAL '$add_hours:$add_minutes' HOUR_MINUTE)");
		$wpdb->query("UPDATE $wpdb->posts SET post_modified = post_date");
		$wpdb->query("UPDATE $wpdb->posts SET post_modified_gmt = DATE_ADD(post_modified, INTERVAL '$add_hours:$add_minutes' HOUR_MINUTE) WHERE post_modified != '0000-00-00 00:00:00'");
		$wpdb->query("UPDATE $wpdb->comments SET comment_date_gmt = DATE_ADD(comment_date, INTERVAL '$add_hours:$add_minutes' HOUR_MINUTE)");
		$wpdb->query("UPDATE $wpdb->users SET user_registered = DATE_ADD(user_registered, INTERVAL '$add_hours:$add_minutes' HOUR_MINUTE)");
	}

}

/**
 * Execute changes made in WordPress 1.5.
 *
 * @since 1.5.0
 */
function upgrade_130() {
	global $wpdb;

	// Remove extraneous backslashes.
	$posts = $wpdb->get_results("SELECT ID, post_title, post_content, post_excerpt, guid, post_date, post_name, post_status, post_author FROM $wpdb->posts");
	if ($posts) {
		foreach($posts as $post) {
			$post_content = addslashes(deslash($post->post_content));
			$post_title = addslashes(deslash($post->post_title));
			$post_excerpt = addslashes(deslash($post->post_excerpt));
			if ( empty($post->guid) )
				$guid = get_permalink($post->ID);
			else
				$guid = $post->guid;

			$wpdb->update( $wpdb->posts, compact('post_title', 'post_content', 'post_excerpt', 'guid'), array('ID' => $post->ID) );

		}
	}

	// Remove extraneous backslashes.
	$comments = $wpdb->get_results("SELECT comment_ID, comment_author, comment_content FROM $wpdb->comments");
	if ($comments) {
		foreach($comments as $comment) {
			$comment_content = deslash($comment->comment_content);
			$comment_author = deslash($comment->comment_author);

			$wpdb->update($wpdb->comments, compact('comment_content', 'comment_author'), array('comment_ID' => $comment->comment_ID) );
		}
	}

	// Remove extraneous backslashes.
	$links = $wpdb->get_results("SELECT link_id, link_name, link_description FROM $wpdb->links");
	if ($links) {
		foreach($links as $link) {
			$link_name = deslash($link->link_name);
			$link_description = deslash($link->link_description);

			$wpdb->update( $wpdb->links, compact('link_name', 'link_description'), array('link_id' => $link->link_id) );
		}
	}

	$active_plugins = __get_option('active_plugins');

	// If plugins are not stored in an array, they're stored in the old
	// newline separated format.  Convert to new format.
	if ( !is_array( $active_plugins ) ) {
		$active_plugins = explode("\n", trim($active_plugins));
		update_option('active_plugins', $active_plugins);
	}

	// Obsolete tables
	$wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'optionvalues');
	$wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'optiontypes');
	$wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'optiongroups');
	$wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'optiongroup_options');

	// Update comments table to use comment_type
	$wpdb->query("UPDATE $wpdb->comments SET comment_type='trackback', comment_content = REPLACE(comment_content, '<trackback />', '') WHERE comment_content LIKE '<trackback />%'");
	$wpdb->query("UPDATE $wpdb->comments SET comment_type='pingback', comment_content = REPLACE(comment_content, '<pingback />', '') WHERE comment_content LIKE '<pingback />%'");

	// Some versions have multiple duplicate option_name rows with the same values
	$options = $wpdb->get_results("SELECT option_name, COUNT(option_name) AS dupes FROM `$wpdb->options` GROUP BY option_name");
	foreach ( $options as $option ) {
		if ( 1 != $option->dupes ) { // Could this be done in the query?
			$limit = $option->dupes - 1;
			$dupe_ids = $wpdb->get_col( $wpdb->prepare("SELECT option_id FROM $wpdb->options WHERE option_name = %s LIMIT %d", $option->option_name, $limit) );
			if ( $dupe_ids ) {
				$dupe_ids = join($dupe_ids, ',');
				$wpdb->query("DELETE FROM $wpdb->options WHERE option_id IN ($dupe_ids)");
			}
		}
	}

	make_site_theme();
}

/**
 * Execute changes made in WordPress 2.0.
 *
 * @since 2.0.0
 */
function upgrade_160() {
	global $wpdb, $wp_current_db_version;

	populate_roles_160();

	$users = $wpdb->get_results("SELECT * FROM $wpdb->users");
	foreach ( $users as $user ) :
		if ( !empty( $user->user_firstname ) )
			update_usermeta( $user->ID, 'first_name', $wpdb->escape($user->user_firstname) );
		if ( !empty( $user->user_lastname ) )
			update_usermeta( $user->ID, 'last_name', $wpdb->escape($user->user_lastname) );
		if ( !empty( $user->user_nickname ) )
			update_usermeta( $user->ID, 'nickname', $wpdb->escape($user->user_nickname) );
		if ( !empty( $user->user_level ) )
			update_usermeta( $user->ID, $wpdb->prefix . 'user_level', $user->user_level );
		if ( !empty( $user->user_icq ) )
			update_usermeta( $user->ID, 'icq', $wpdb->escape($user->user_icq) );
		if ( !empty( $user->user_aim ) )
			update_usermeta( $user->ID, 'aim', $wpdb->escape($user->user_aim) );
		if ( !empty( $user->user_msn ) )
			update_usermeta( $user->ID, 'msn', $wpdb->escape($user->user_msn) );
		if ( !empty( $user->user_yim ) )
			update_usermeta( $user->ID, 'yim', $wpdb->escape($user->user_icq) );
		if ( !empty( $user->user_description ) )
			update_usermeta( $user->ID, 'description', $wpdb->escape($user->user_description) );

		if ( isset( $user->user_idmode ) ):
			$idmode = $user->user_idmode;
			if ($idmode == 'nickname') $id = $user->user_nickname;
			if ($idmode == 'login') $id = $user->user_login;
			if ($idmode == 'firstname') $id = $user->user_firstname;
			if ($idmode == 'lastname') $id = $user->user_lastname;
			if ($idmode == 'namefl') $id = $user->user_firstname.' '.$user->user_lastname;
			if ($idmode == 'namelf') $id = $user->user_lastname.' '.$user->user_firstname;
			if (!$idmode) $id = $user->user_nickname;
			$wpdb->update( $wpdb->users, array('display_name' => $id), array('ID' => $user->ID) );
		endif;

		// FIXME: RESET_CAPS is temporary code to reset roles and caps if flag is set.
		$caps = get_usermeta( $user->ID, $wpdb->prefix . 'capabilities');
		if ( empty($caps) || defined('RESET_CAPS') ) {
			$level = get_usermeta($user->ID, $wpdb->prefix . 'user_level');
			$role = translate_level_to_role($level);
			update_usermeta( $user->ID, $wpdb->prefix . 'capabilities', array($role => true) );
		}

	endforeach;
	$old_user_fields = array( 'user_firstname', 'user_lastname', 'user_icq', 'user_aim', 'user_msn', 'user_yim', 'user_idmode', 'user_ip', 'user_domain', 'user_browser', 'user_description', 'user_nickname', 'user_level' );
	$wpdb->hide_errors();
	foreach ( $old_user_fields as $old )
		$wpdb->query("ALTER TABLE $wpdb->users DROP $old");
	$wpdb->show_errors();

	// populate comment_count field of posts table
	$comments = $wpdb->get_results( "SELECT comment_post_ID, COUNT(*) as c FROM $wpdb->comments WHERE comment_approved = '1' GROUP BY comment_post_ID" );
	if( is_array( $comments ) )
		foreach ($comments as $comment)
			$wpdb->update( $wpdb->posts, array('comment_count' => $comment->c), array('ID' => $comment->comment_post_ID) );

	// Some alpha versions used a post status of object instead of attachment and put
	// the mime type in post_type instead of post_mime_type.
	if ( $wp_current_db_version > 2541 && $wp_current_db_version <= 3091 ) {
		$objects = $wpdb->get_results("SELECT ID, post_type FROM $wpdb->posts WHERE post_status = 'object'");
		foreach ($objects as $object) {
			$wpdb->update( $wpdb->posts, array(	'post_status' => 'attachment',
												'post_mime_type' => $object->post_type,
												'post_type' => ''),
										 array( 'ID' => $object->ID ) );

			$meta = get_post_meta($object->ID, 'imagedata', true);
			if ( ! empty($meta['file']) )
				update_attached_file( $object->ID, $meta['file'] );
		}
	}
}

/**
 * Execute changes made in WordPress 2.1.
 *
 * @since 2.1.0
 */
function upgrade_210() {
	global $wpdb, $wp_current_db_version;

	if ( $wp_current_db_version < 3506 ) {
		// Update status and type.
		$posts = $wpdb->get_results("SELECT ID, post_status FROM $wpdb->posts");

		if ( ! empty($posts) ) foreach ($posts as $post) {
			$status = $post->post_status;
			$type = 'post';

			if ( 'static' == $status ) {
				$status = 'publish';
				$type = 'page';
			} else if ( 'attachment' == $status ) {
				$status = 'inherit';
				$type = 'attachment';
			}

			$wpdb->query( $wpdb->prepare("UPDATE $wpdb->posts SET post_status = %s, post_type = %s WHERE ID = %d", $status, $type, $post->ID) );
		}
	}

	if ( $wp_current_db_version < 3845 ) {
		populate_roles_210();
	}

	if ( $wp_current_db_version < 3531 ) {
		// Give future posts a post_status of future.
		$now = gmdate('Y-m-d H:i:59');
		$wpdb->query ("UPDATE $wpdb->posts SET post_status = 'future' WHERE post_status = 'publish' AND post_date_gmt > '$now'");

		$posts = $wpdb->get_results("SELECT ID, post_date FROM $wpdb->posts WHERE post_status ='future'");
		if ( !empty($posts) )
			foreach ( $posts as $post )
				wp_schedule_single_event(mysql2date('U', $post->post_date, false), 'publish_future_post', array($post->ID));
	}
}

/**
 * Execute changes made in WordPress 2.3.
 *
 * @since 2.3.0
 */
function upgrade_230() {
	global $wp_current_db_version, $wpdb;

	if ( $wp_current_db_version < 5200 ) {
		populate_roles_230();
	}

	// Convert categories to terms.
	$tt_ids = array();
	$have_tags = false;
	$categories = $wpdb->get_results("SELECT * FROM $wpdb->categories ORDER BY cat_ID");
	foreach ($categories as $category) {
		$term_id = (int) $category->cat_ID;
		$name = $category->cat_name;
		$description = $category->category_description;
		$slug = $category->category_nicename;
		$parent = $category->category_parent;
		$term_group = 0;

		// Associate terms with the same slug in a term group and make slugs unique.
		if ( $exists = $wpdb->get_results( $wpdb->prepare("SELECT term_id, term_group FROM $wpdb->terms WHERE slug = %s", $slug) ) ) {
			$term_group = $exists[0]->term_group;
			$id = $exists[0]->term_id;
			$num = 2;
			do {
				$alt_slug = $slug . "-$num";
				$num++;
				$slug_check = $wpdb->get_var( $wpdb->prepare("SELECT slug FROM $wpdb->terms WHERE slug = %s", $alt_slug) );
			} while ( $slug_check );

			$slug = $alt_slug;

			if ( empty( $term_group ) ) {
				$term_group = $wpdb->get_var("SELECT MAX(term_group) FROM $wpdb->terms GROUP BY term_group") + 1;
				$wpdb->query( $wpdb->prepare("UPDATE $wpdb->terms SET term_group = %d WHERE term_id = %d", $term_group, $id) );
			}
		}

		$wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->terms (term_id, name, slug, term_group) VALUES
		(%d, %s, %s, %d)", $term_id, $name, $slug, $term_group) );

		$count = 0;
		if ( !empty($category->category_count) ) {
			$count = (int) $category->category_count;
			$taxonomy = 'category';
			$wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->term_taxonomy (term_id, taxonomy, description, parent, count) VALUES ( %d, %s, %s, %d, %d)", $term_id, $taxonomy, $description, $parent, $count) );
			$tt_ids[$term_id][$taxonomy] = (int) $wpdb->insert_id;
		}

		if ( !empty($category->link_count) ) {
			$count = (int) $category->link_count;
			$taxonomy = 'link_category';
			$wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->term_taxonomy (term_id, taxonomy, description, parent, count) VALUES ( %d, %s, %s, %d, %d)", $term_id, $taxonomy, $description, $parent, $count) );
			$tt_ids[$term_id][$taxonomy] = (int) $wpdb->insert_id;
		}

		if ( !empty($category->tag_count) ) {
			$have_tags = true;
			$count = (int) $category->tag_count;
			$taxonomy = 'post_tag';
			$wpdb->insert( $wpdb->term_taxonomy, compact('term_id', 'taxonomy', 'description', 'parent', 'count') );
			$tt_ids[$term_id][$taxonomy] = (int) $wpdb->insert_id;
		}

		if ( empty($count) ) {
			$count = 0;
			$taxonomy = 'category';
			$wpdb->insert( $wpdb->term_taxonomy, compact('term_id', 'taxonomy', 'description', 'parent', 'count') );
			$tt_ids[$term_id][$taxonomy] = (int) $wpdb->insert_id;
		}
	}

	$select = 'post_id, category_id';
	if ( $have_tags )
		$select .= ', rel_type';

	$posts = $wpdb->get_results("SELECT $select FROM $wpdb->post2cat GROUP BY post_id, category_id");
	foreach ( $posts as $post ) {
		$post_id = (int) $post->post_id;
		$term_id = (int) $post->category_id;
		$taxonomy = 'category';
		if ( !empty($post->rel_type) && 'tag' == $post->rel_type)
			$taxonomy = 'tag';
		$tt_id = $tt_ids[$term_id][$taxonomy];
		if ( empty($tt_id) )
			continue;

		$wpdb->insert( $wpdb->term_relationships, array('object_id' => $post_id, 'term_taxonomy_id' => $tt_id) );
	}

	// < 3570 we used linkcategories.  >= 3570 we used categories and link2cat.
	if ( $wp_current_db_version < 3570 ) {
		// Create link_category terms for link categories.  Create a map of link cat IDs
		// to link_category terms.
		$link_cat_id_map = array();
		$default_link_cat = 0;
		$tt_ids = array();
		$link_cats = $wpdb->get_results("SELECT cat_id, cat_name FROM " . $wpdb->prefix . 'linkcategories');
		foreach ( $link_cats as $category) {
			$cat_id = (int) $category->cat_id;
			$term_id = 0;
			$name = $wpdb->escape($category->cat_name);
			$slug = sanitize_title($name);
			$term_group = 0;

			// Associate terms with the same slug in a term group and make slugs unique.
			if ( $exists = $wpdb->get_results( $wpdb->prepare("SELECT term_id, term_group FROM $wpdb->terms WHERE slug = %s", $slug) ) ) {
				$term_group = $exists[0]->term_group;
				$term_id = $exists[0]->term_id;
			}

			if ( empty($term_id) ) {
				$wpdb->insert( $wpdb->terms, compact('name', 'slug', 'term_group') );
				$term_id = (int) $wpdb->insert_id;
			}

			$link_cat_id_map[$cat_id] = $term_id;
			$default_link_cat = $term_id;

			$wpdb->insert( $wpdb->term_taxonomy, array('term_id' => $term_id, 'taxonomy' => 'link_category', 'description' => '', 'parent' => 0, 'count' => 0) );
			$tt_ids[$term_id] = (int) $wpdb->insert_id;
		}

		// Associate links to cats.
		$links = $wpdb->get_results("SELECT link_id, link_category FROM $wpdb->links");
		if ( !empty($links) ) foreach ( $links as $link ) {
			if ( 0 == $link->link_category )
				continue;
			if ( ! isset($link_cat_id_map[$link->link_category]) )
				continue;
			$term_id = $link_cat_id_map[$link->link_category];
			$tt_id = $tt_ids[$term_id];
			if ( empty($tt_id) )
				continue;

			$wpdb->insert( $wpdb->term_relationships, array('object_id' => $link->link_id, 'term_taxonomy_id' => $tt_id) );
		}

		// Set default to the last category we grabbed during the upgrade loop.
		update_option('default_link_category', $default_link_cat);
	} else {
		$links = $wpdb->get_results("SELECT link_id, category_id FROM $wpdb->link2cat GROUP BY link_id, category_id");
		foreach ( $links as $link ) {
			$link_id = (int) $link->link_id;
			$term_id = (int) $link->category_id;
			$taxonomy = 'link_category';
			$tt_id = $tt_ids[$term_id][$taxonomy];
			if ( empty($tt_id) )
				continue;
			$wpdb->insert( $wpdb->term_relationships, array('object_id' => $link_id, 'term_taxonomy_id' => $tt_id) );
		}
	}

	if ( $wp_current_db_version < 4772 ) {
		// Obsolete linkcategories table
		$wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'linkcategories');
	}

	// Recalculate all counts
	$terms = $wpdb->get_results("SELECT term_taxonomy_id, taxonomy FROM $wpdb->term_taxonomy");
	foreach ( (array) $terms as $term ) {
		if ( ('post_tag' == $term->taxonomy) || ('category' == $term->taxonomy) )
			$count = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->term_relationships, $wpdb->posts WHERE $wpdb->posts.ID = $wpdb->term_relationships.object_id AND post_status = 'publish' AND post_type = 'post' AND term_taxonomy_id = %d", $term->term_taxonomy_id) );
		else
			$count = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d", $term->term_taxonomy_id) );
		$wpdb->update( $wpdb->term_taxonomy, array('count' => $count), array('term_taxonomy_id' => $term->term_taxonomy_id) );
	}
}

/**
 * Remove old options from the database.
 *
 * @since 2.3.0
 */
function upgrade_230_options_table() {
	global $wpdb;
	$old_options_fields = array( 'option_can_override', 'option_type', 'option_width', 'option_height', 'option_description', 'option_admin_level' );
	$wpdb->hide_errors();
	foreach ( $old_options_fields as $old )
		$wpdb->query("ALTER TABLE $wpdb->options DROP $old");
	$wpdb->show_errors();
}

/**
 * Remove old categories, link2cat, and post2cat database tables.
 *
 * @since 2.3.0
 */
function upgrade_230_old_tables() {
	global $wpdb;
	$wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'categories');
	$wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'link2cat');
	$wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'post2cat');
}

/**
 * Upgrade old slugs made in version 2.2.
 *
 * @since 2.2.0
 */
function upgrade_old_slugs() {
	// upgrade people who were using the Redirect Old Slugs plugin
	global $wpdb;
	$wpdb->query("UPDATE $wpdb->postmeta SET meta_key = '_wp_old_slug' WHERE meta_key = 'old_slug'");
}

/**
 * Execute changes made in WordPress 2.5.0.
 *
 * @since 2.5.0
 */
function upgrade_250() {
	global $wp_current_db_version;

	if ( $wp_current_db_version < 6689 ) {
		populate_roles_250();
	}

}

/**
 * Execute changes made in WordPress 2.5.1.
 *
 * @since 2.5.1
 */
function upgrade_251() {
	global $wp_current_db_version;

	// Make the secret longer
	update_option('secret', wp_generate_password(64));
}

/**
 * Execute changes made in WordPress 2.5.2.
 *
 * @since 2.5.2
 */
function upgrade_252() {
	global $wpdb;

	$wpdb->query("UPDATE $wpdb->users SET user_activation_key = ''");
}

/**
 * Execute changes made in WordPress 2.6.
 *
 * @since 2.6.0
 */
function upgrade_260() {
	global $wp_current_db_version;

	if ( $wp_current_db_version < 8000 )
		populate_roles_260();

	if ( $wp_current_db_version < 8201 ) {
		update_option('enable_app', 1);
		update_option('enable_xmlrpc', 1);
	}
}

/**
 * Execute changes made in WordPress 2.7.
 *
 * @since 2.7.0
 */
function upgrade_270() {
	global $wpdb, $wp_current_db_version;

	if ( $wp_current_db_version < 8980 )
		populate_roles_270();

	// Update post_date for unpublished posts with empty timestamp
	if ( $wp_current_db_version < 8921 )
		$wpdb->query( "UPDATE $wpdb->posts SET post_date = post_modified WHERE post_date = '0000-00-00 00:00:00'" );
}

/**
 * Execute changes made in WordPress 2.8.
 *
 * @since 2.8.0
 */
function upgrade_280() {
	global $wp_current_db_version;

	if ( $wp_current_db_version < 10360 )
		populate_roles_280();
}

/**
 * Execute changes made in WordPress 2.9.
 *
 * @since 2.9.0
 */
function upgrade_290() {
	global $wp_current_db_version;

	if ( $wp_current_db_version < 11958 ) {
		// Previously, setting depth to 1 would redundantly disable threading, but now 2 is the minimum depth to avoid confusion
		if ( get_option( 'thread_comments_depth' ) == '1' ) {
			update_option( 'thread_comments_depth', 2 );
			update_option( 'thread_comments', 0 );
		}
	}
}


// The functions we use to actually do stuff

// General

/**
 * {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @since unknown
 *
 * @param string $table_name Database table name to create.
 * @param string $create_ddl SQL statement to create table.
 * @return bool If table already exists or was created by function.
 */
function maybe_create_table($table_name, $create_ddl) {
	global $wpdb;
	if ( $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name )
		return true;
	//didn't find it try to create it.
	$q = $wpdb->query($create_ddl);
	// we cannot directly tell that whether this succeeded!
	if ( $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name )
		return true;
	return false;
}

/**
 * {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @since unknown
 *
 * @param string $table Database table name.
 * @param string $index Index name to drop.
 * @return bool True, when finished.
 */
function drop_index($table, $index) {
	global $wpdb;
	$wpdb->hide_errors();
	$wpdb->query("ALTER TABLE `$table` DROP INDEX `$index`");
	// Now we need to take out all the extra ones we may have created
	for ($i = 0; $i < 25; $i++) {
		$wpdb->query("ALTER TABLE `$table` DROP INDEX `{$index}_$i`");
	}
	$wpdb->show_errors();
	return true;
}

/**
 * {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @since unknown
 *
 * @param string $table Database table name.
 * @param string $index Database table index column.
 * @return bool True, when done with execution.
 */
function add_clean_index($table, $index) {
	global $wpdb;
	drop_index($table, $index);
	$wpdb->query("ALTER TABLE `$table` ADD INDEX ( `$index` )");
	return true;
}

/**
 ** maybe_add_column()
 ** Add column to db table if it doesn't exist.
 ** Returns:  true if already exists or on successful completion
 **           false on error
 */
function maybe_add_column($table_name, $column_name, $create_ddl) {
	global $wpdb, $debug;
	foreach ($wpdb->get_col("DESC $table_name", 0) as $column ) {
		if ($debug) echo("checking $column == $column_name<br />");
		if ($column == $column_name) {
			return true;
		}
	}
	//didn't find it try to create it.
	$q = $wpdb->query($create_ddl);
	// we cannot directly tell that whether this succeeded!
	foreach ($wpdb->get_col("DESC $table_name", 0) as $column ) {
		if ($column == $column_name) {
			return true;
		}
	}
	return false;
}

/**
 * Retrieve all options as it was for 1.2.
 *
 * @since 1.2.0
 *
 * @return array List of options.
 */
function get_alloptions_110() {
	global $wpdb;
	if ($options = $wpdb->get_results("SELECT option_name, option_value FROM $wpdb->options")) {
		foreach ($options as $option) {
			// "When trying to design a foolproof system,
			//  never underestimate the ingenuity of the fools :)" -- Dougal
			if ('siteurl' == $option->option_name) $option->option_value = preg_replace('|/+$|', '', $option->option_value);
			if ('home' == $option->option_name) $option->option_value = preg_replace('|/+$|', '', $option->option_value);
			if ('category_base' == $option->option_name) $option->option_value = preg_replace('|/+$|', '', $option->option_value);
			$all_options->{$option->option_name} = stripslashes($option->option_value);
		}
	}
	return $all_options;
}

/**
 * Version of get_option that is private to install/upgrade.
 *
 * @since unknown
 * @access private
 *
 * @param string $setting Option name.
 * @return mixed
 */
function __get_option($setting) {
	global $wpdb;

	if ( $setting == 'home' && defined( 'WP_HOME' ) ) {
		return preg_replace( '|/+$|', '', constant( 'WP_HOME' ) );
	}

	if ( $setting == 'siteurl' && defined( 'WP_SITEURL' ) ) {
		return preg_replace( '|/+$|', '', constant( 'WP_SITEURL' ) );
	}

	$option = $wpdb->get_var( $wpdb->prepare("SELECT option_value FROM $wpdb->options WHERE option_name = %s", $setting) );

	if ( 'home' == $setting && '' == $option )
		return __get_option('siteurl');

	if ( 'siteurl' == $setting || 'home' == $setting || 'category_base' == $setting )
		$option = preg_replace('|/+$|', '', $option);

	@ $kellogs = unserialize($option);
	if ($kellogs !== FALSE)
		return $kellogs;
	else
		return $option;
}

/**
 * {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @since unknown
 *
 * @param string $content
 * @return string
 */
function deslash($content) {
	// Note: \\\ inside a regex denotes a single backslash.

	// Replace one or more backslashes followed by a single quote with
	// a single quote.
	$content = preg_replace("/\\\+'/", "'", $content);

	// Replace one or more backslashes followed by a double quote with
	// a double quote.
	$content = preg_replace('/\\\+"/', '"', $content);

	// Replace one or more backslashes with one backslash.
	$content = preg_replace("/\\\+/", "\\", $content);

	return $content;
}

/**
 * {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @since unknown
 *
 * @param unknown_type $queries
 * @param unknown_type $execute
 * @return unknown
 */
function dbDelta($queries, $execute = true) {
	global $wpdb;

	// Separate individual queries into an array
	if( !is_array($queries) ) {
		$queries = explode( ';', $queries );
		if('' == $queries[count($queries) - 1]) array_pop($queries);
	}

	$cqueries = array(); // Creation Queries
	$iqueries = array(); // Insertion Queries
	$for_update = array();

	// Create a tablename index for an array ($cqueries) of queries
	foreach($queries as $qry) {
		if(preg_match("|CREATE TABLE ([^ ]*)|", $qry, $matches)) {
			$cqueries[trim( strtolower($matches[1]), '`' )] = $qry;
			$for_update[$matches[1]] = 'Created table '.$matches[1];
		}
		else if(preg_match("|CREATE DATABASE ([^ ]*)|", $qry, $matches)) {
			array_unshift($cqueries, $qry);
		}
		else if(preg_match("|INSERT INTO ([^ ]*)|", $qry, $matches)) {
			$iqueries[] = $qry;
		}
		else if(preg_match("|UPDATE ([^ ]*)|", $qry, $matches)) {
			$iqueries[] = $qry;
		}
		else {
			// Unrecognized query type
		}
	}

	// Check to see which tables and fields exist
	if($tables = $wpdb->get_col('SHOW TABLES;')) {
		// For every table in the database
		foreach($tables as $table) {
			// If a table query exists for the database table...
			if( array_key_exists(strtolower($table), $cqueries) ) {
				// Clear the field and index arrays
				unset($cfields);
				unset($indices);
				// Get all of the field names in the query from between the parens
				preg_match("|\((.*)\)|ms", $cqueries[strtolower($table)], $match2);
				$qryline = trim($match2[1]);

				// Separate field lines into an array
				$flds = explode("\n", $qryline);

				//echo "<hr/><pre>\n".print_r(strtolower($table), true).":\n".print_r($cqueries, true)."</pre><hr/>";

				// For every field line specified in the query
				foreach($flds as $fld) {
					// Extract the field name
					preg_match("|^([^ ]*)|", trim($fld), $fvals);
					$fieldname = trim( $fvals[1], '`' );

					// Verify the found field name
					$validfield = true;
					switch(strtolower($fieldname))
					{
					case '':
					case 'primary':
					case 'index':
					case 'fulltext':
					case 'unique':
					case 'key':
						$validfield = false;
						$indices[] = trim(trim($fld), ", \n");
						break;
					}
					$fld = trim($fld);

					// If it's a valid field, add it to the field array
					if($validfield) {
						$cfields[strtolower($fieldname)] = trim($fld, ", \n");
					}
				}

				// Fetch the table column structure from the database
				$tablefields = $wpdb->get_results("DESCRIBE {$table};");

				// For every field in the table
				foreach($tablefields as $tablefield) {
					// If the table field exists in the field array...
					if(array_key_exists(strtolower($tablefield->Field), $cfields)) {
						// Get the field type from the query
						preg_match("|".$tablefield->Field." ([^ ]*( unsigned)?)|i", $cfields[strtolower($tablefield->Field)], $matches);
						$fieldtype = $matches[1];

						// Is actual field type different from the field type in query?
						if($tablefield->Type != $fieldtype) {
							// Add a query to change the column type
							$cqueries[] = "ALTER TABLE {$table} CHANGE COLUMN {$tablefield->Field} " . $cfields[strtolower($tablefield->Field)];
							$for_update[$table.'.'.$tablefield->Field] = "Changed type of {$table}.{$tablefield->Field} from {$tablefield->Type} to {$fieldtype}";
						}

						// Get the default value from the array
							//echo "{$cfields[strtolower($tablefield->Field)]}<br>";
						if(preg_match("| DEFAULT '(.*)'|i", $cfields[strtolower($tablefield->Field)], $matches)) {
							$default_value = $matches[1];
							if($tablefield->Default != $default_value)
							{
								// Add a query to change the column's default value
								$cqueries[] = "ALTER TABLE {$table} ALTER COLUMN {$tablefield->Field} SET DEFAULT '{$default_value}'";
								$for_update[$table.'.'.$tablefield->Field] = "Changed default value of {$table}.{$tablefield->Field} from {$tablefield->Default} to {$default_value}";
							}
						}

						// Remove the field from the array (so it's not added)
						unset($cfields[strtolower($tablefield->Field)]);
					}
					else {
						// This field exists in the table, but not in the creation queries?
					}
				}

				// For every remaining field specified for the table
				foreach($cfields as $fieldname => $fielddef) {
					// Push a query line into $cqueries that adds the field to that table
					$cqueries[] = "ALTER TABLE {$table} ADD COLUMN $fielddef";
					$for_update[$table.'.'.$fieldname] = 'Added column '.$table.'.'.$fieldname;
				}

				// Index stuff goes here
				// Fetch the table index structure from the database
				$tableindices = $wpdb->get_results("SHOW INDEX FROM {$table};");

				if($tableindices) {
					// Clear the index array
					unset($index_ary);

					// For every index in the table
					foreach($tableindices as $tableindex) {
						// Add the index to the index data array
						$keyname = $tableindex->Key_name;
						$index_ary[$keyname]['columns'][] = array('fieldname' => $tableindex->Column_name, 'subpart' => $tableindex->Sub_part);
						$index_ary[$keyname]['unique'] = ($tableindex->Non_unique == 0)?true:false;
					}

					// For each actual index in the index array
					foreach($index_ary as $index_name => $index_data) {
						// Build a create string to compare to the query
						$index_string = '';
						if($index_name == 'PRIMARY') {
							$index_string .= 'PRIMARY ';
						}
						else if($index_data['unique']) {
							$index_string .= 'UNIQUE ';
						}
						$index_string .= 'KEY ';
						if($index_name != 'PRIMARY') {
							$index_string .= $index_name;
						}
						$index_columns = '';
						// For each column in the index
						foreach($index_data['columns'] as $column_data) {
							if($index_columns != '') $index_columns .= ',';
							// Add the field to the column list string
							$index_columns .= $column_data['fieldname'];
							if($column_data['subpart'] != '') {
								$index_columns .= '('.$column_data['subpart'].')';
							}
						}
						// Add the column list to the index create string
						$index_string .= ' ('.$index_columns.')';
						if(!(($aindex = array_search($index_string, $indices)) === false)) {
							unset($indices[$aindex]);
							//echo "<pre style=\"border:1px solid #ccc;margin-top:5px;\">{$table}:<br />Found index:".$index_string."</pre>\n";
						}
						//else echo "<pre style=\"border:1px solid #ccc;margin-top:5px;\">{$table}:<br /><b>Did not find index:</b>".$index_string."<br />".print_r($indices, true)."</pre>\n";
					}
				}

				// For every remaining index specified for the table
				foreach ( (array) $indices as $index ) {
					// Push a query line into $cqueries that adds the index to that table
					$cqueries[] = "ALTER TABLE {$table} ADD $index";
					$for_update[$table.'.'.$fieldname] = 'Added index '.$table.' '.$index;
				}

				// Remove the original table creation query from processing
				unset($cqueries[strtolower($table)]);
				unset($for_update[strtolower($table)]);
			} else {
				// This table exists in the database, but not in the creation queries?
			}
		}
	}

	$allqueries = array_merge($cqueries, $iqueries);
	if($execute) {
		foreach($allqueries as $query) {
			//echo "<pre style=\"border:1px solid #ccc;margin-top:5px;\">".print_r($query, true)."</pre>\n";
			$wpdb->query($query);
		}
	}

	return $for_update;
}

/**
 * {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @since unknown
 */
function make_db_current() {
	global $wp_queries;

	$alterations = dbDelta($wp_queries);
	echo "<ol>\n";
	foreach($alterations as $alteration) echo "<li>$alteration</li>\n";
	echo "</ol>\n";
}

/**
 * {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @since unknown
 */
function make_db_current_silent() {
	global $wp_queries;

	$alterations = dbDelta($wp_queries);
}

/**
 * {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @since unknown
 *
 * @param unknown_type $theme_name
 * @param unknown_type $template
 * @return unknown
 */
function make_site_theme_from_oldschool($theme_name, $template) {
	$home_path = get_home_path();
	$site_dir = WP_CONTENT_DIR . "/themes/$template";

	if (! file_exists("$home_path/index.php"))
		return false;

	// Copy files from the old locations to the site theme.
	// TODO: This does not copy arbitarary include dependencies.  Only the
	// standard WP files are copied.
	$files = array('index.php' => 'index.php', 'wp-layout.css' => 'style.css', 'wp-comments.php' => 'comments.php', 'wp-comments-popup.php' => 'comments-popup.php');

	foreach ($files as $oldfile => $newfile) {
		if ($oldfile == 'index.php')
			$oldpath = $home_path;
		else
			$oldpath = ABSPATH;

		if ($oldfile == 'index.php') { // Check to make sure it's not a new index
			$index = implode('', file("$oldpath/$oldfile"));
			if (strpos($index, 'WP_USE_THEMES') !== false) {
				if (! @copy(WP_CONTENT_DIR . '/themes/default/index.php', "$site_dir/$newfile"))
					return false;
				continue; // Don't copy anything
				}
		}

		if (! @copy("$oldpath/$oldfile", "$site_dir/$newfile"))
			return false;

		chmod("$site_dir/$newfile", 0777);

		// Update the blog header include in each file.
		$lines = explode("\n", implode('', file("$site_dir/$newfile")));
		if ($lines) {
			$f = fopen("$site_dir/$newfile", 'w');

			foreach ($lines as $line) {
				if (preg_match('/require.*wp-blog-header/', $line))
					$line = '//' . $line;

				// Update stylesheet references.
				$line = str_replace("<?php echo __get_option('siteurl'); ?>/wp-layout.css", "<?php bloginfo('stylesheet_url'); ?>", $line);

				// Update comments template inclusion.
				$line = str_replace("<?php include(ABSPATH . 'wp-comments.php'); ?>", "<?php comments_template(); ?>", $line);

				fwrite($f, "{$line}\n");
			}
			fclose($f);
		}
	}

	// Add a theme header.
	$header = "/*\nTheme Name: $theme_name\nTheme URI: " . __get_option('siteurl') . "\nDescription: A theme automatically created by the upgrade.\nVersion: 1.0\nAuthor: Moi\n*/\n";

	$stylelines = file_get_contents("$site_dir/style.css");
	if ($stylelines) {
		$f = fopen("$site_dir/style.css", 'w');

		fwrite($f, $header);
		fwrite($f, $stylelines);
		fclose($f);
	}

	return true;
}

/**
 * {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @since unknown
 *
 * @param unknown_type $theme_name
 * @param unknown_type $template
 * @return unknown
 */
function make_site_theme_from_default($theme_name, $template) {
	$site_dir = WP_CONTENT_DIR . "/themes/$template";
	$default_dir = WP_CONTENT_DIR . '/themes/default';

	// Copy files from the default theme to the site theme.
	//$files = array('index.php', 'comments.php', 'comments-popup.php', 'footer.php', 'header.php', 'sidebar.php', 'style.css');

	$theme_dir = @ opendir("$default_dir");
	if ($theme_dir) {
		while(($theme_file = readdir( $theme_dir )) !== false) {
			if (is_dir("$default_dir/$theme_file"))
				continue;
			if (! @copy("$default_dir/$theme_file", "$site_dir/$theme_file"))
				return;
			chmod("$site_dir/$theme_file", 0777);
		}
	}
	@closedir($theme_dir);

	// Rewrite the theme header.
	$stylelines = explode("\n", implode('', file("$site_dir/style.css")));
	if ($stylelines) {
		$f = fopen("$site_dir/style.css", 'w');

		foreach ($stylelines as $line) {
			if (strpos($line, 'Theme Name:') !== false) $line = 'Theme Name: ' . $theme_name;
			elseif (strpos($line, 'Theme URI:') !== false) $line = 'Theme URI: ' . __get_option('url');
			elseif (strpos($line, 'Description:') !== false) $line = 'Description: Your theme.';
			elseif (strpos($line, 'Version:') !== false) $line = 'Version: 1';
			elseif (strpos($line, 'Author:') !== false) $line = 'Author: You';
			fwrite($f, $line . "\n");
		}
		fclose($f);
	}

	// Copy the images.
	umask(0);
	if (! mkdir("$site_dir/images", 0777)) {
		return false;
	}

	$images_dir = @ opendir("$default_dir/images");
	if ($images_dir) {
		while(($image = readdir($images_dir)) !== false) {
			if (is_dir("$default_dir/images/$image"))
				continue;
			if (! @copy("$default_dir/images/$image", "$site_dir/images/$image"))
				return;
			chmod("$site_dir/images/$image", 0777);
		}
	}
	@closedir($images_dir);
}

// Create a site theme from the default theme.
/**
 * {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @since unknown
 *
 * @return unknown
 */
function make_site_theme() {
	// Name the theme after the blog.
	$theme_name = __get_option('blogname');
	$template = sanitize_title($theme_name);
	$site_dir = WP_CONTENT_DIR . "/themes/$template";

	// If the theme already exists, nothing to do.
	if ( is_dir($site_dir)) {
		return false;
	}

	// We must be able to write to the themes dir.
	if (! is_writable(WP_CONTENT_DIR . "/themes")) {
		return false;
	}

	umask(0);
	if (! mkdir($site_dir, 0777)) {
		return false;
	}

	if (file_exists(ABSPATH . 'wp-layout.css')) {
		if (! make_site_theme_from_oldschool($theme_name, $template)) {
			// TODO:  rm -rf the site theme directory.
			return false;
		}
	} else {
		if (! make_site_theme_from_default($theme_name, $template))
			// TODO:  rm -rf the site theme directory.
			return false;
	}

	// Make the new site theme active.
	$current_template = __get_option('template');
	if ($current_template == 'default') {
		update_option('template', $template);
		update_option('stylesheet', $template);
	}
	return $template;
}

/**
 * Translate user level to user role name.
 *
 * @since unknown
 *
 * @param int $level User level.
 * @return string User role name.
 */
function translate_level_to_role($level) {
	switch ($level) {
	case 10:
	case 9:
	case 8:
		return 'administrator';
	case 7:
	case 6:
	case 5:
		return 'editor';
	case 4:
	case 3:
	case 2:
		return 'author';
	case 1:
		return 'contributor';
	case 0:
		return 'subscriber';
	}
}

/**
 * {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @since unknown
 */
function wp_check_mysql_version() {
	global $wpdb;
	$result = $wpdb->check_database_version();
	if ( is_wp_error( $result ) )
		die( $result->get_error_message() );
}

/**
 * {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @since unknown
 */
function maybe_disable_automattic_widgets() {
	$plugins = __get_option( 'active_plugins' );

	foreach ( (array) $plugins as $plugin ) {
		if ( basename( $plugin ) == 'widgets.php' ) {
			array_splice( $plugins, array_search( $plugin, $plugins ), 1 );
			update_option( 'active_plugins', $plugins );
			break;
		}
	}
}

/**
 * Runs before the schema is upgraded.
 */
function pre_schema_upgrade() {
	global $wp_current_db_version, $wp_db_version, $wpdb;

	// Only run if less than 2.9
	if ( $wp_current_db_version >= 11557 )
		return;

	// Delete duplicate options.  Keep the option with the highest option_id.
	$delete_options = $wpdb->get_col("SELECT o1.option_id FROM $wpdb->options AS o1 JOIN $wpdb->options AS o2 ON o2.option_name = o1.option_name AND o2.option_id > o1.option_id");
	if ( !empty($delete_options) ) {
		$delete_options = implode(',', $delete_options);
		$wpdb->query("DELETE FROM $wpdb->options WHERE option_id IN ($delete_options)");
	}

	// Add an index on option_id to satisfy the auto_increment requirement
	$wpdb->query("ALTER TABLE $wpdb->options ADD INDEX option_id (option_id)");

	// Drop the old primary key. The new primary will be created by dbDelta()
	$wpdb->query("ALTER TABLE $wpdb->options DROP PRIMARY KEY");

	// Drop the old option_name index. dbDelta() doesn't do the drop.
	$wpdb->query("ALTER TABLE $wpdb->options DROP INDEX option_name");
}

?>
