<?php
// Functions to be called in install and upgrade scripts

// First let's set up the tables:

$wp_queries="CREATE TABLE $wpdb->categories (
  cat_ID int(4) NOT NULL auto_increment,
  cat_name varchar(55) NOT NULL default '',
  category_nicename varchar(200) NOT NULL default '',
  category_description text NOT NULL,
  category_parent int(4) NOT NULL default '0',
  PRIMARY KEY  (cat_ID),
  UNIQUE KEY cat_name (cat_name),
  KEY category_nicename (category_nicename)
);
CREATE TABLE $wpdb->comments (
  comment_ID int(11) unsigned NOT NULL auto_increment,
  comment_post_ID int(11) NOT NULL default '0',
  comment_author tinytext NOT NULL,
  comment_author_email varchar(100) NOT NULL default '',
  comment_author_url varchar(200) NOT NULL default '',
  comment_author_IP varchar(100) NOT NULL default '',
  comment_date datetime NOT NULL default '0000-00-00 00:00:00',
  comment_date_gmt datetime NOT NULL default '0000-00-00 00:00:00',
  comment_content text NOT NULL,
  comment_karma int(11) NOT NULL default '0',
  comment_approved enum('0','1') NOT NULL default '1',
  user_id int(11) NOT NULL default '0',
  PRIMARY KEY  (comment_ID),
  KEY comment_approved (comment_approved),
  KEY comment_post_ID (comment_post_ID)
);
CREATE TABLE $wpdb->linkcategories (
  cat_id int(11) NOT NULL auto_increment,
  cat_name tinytext NOT NULL,
  auto_toggle enum('Y','N') NOT NULL default 'N',
  show_images enum('Y','N') NOT NULL default 'Y',
  show_description enum('Y','N') NOT NULL default 'N',
  show_rating enum('Y','N') NOT NULL default 'Y',
  show_updated enum('Y','N') NOT NULL default 'Y',
  sort_order varchar(64) NOT NULL default 'name',
  sort_desc enum('Y','N') NOT NULL default 'N',
  text_before_link varchar(128) NOT NULL default '<li>',
  text_after_link varchar(128) NOT NULL default '<br />',
  text_after_all varchar(128) NOT NULL default '</li>',
  list_limit int(11) NOT NULL default '-1',
  PRIMARY KEY  (cat_id)
);
CREATE TABLE $wpdb->links (
  link_id int(11) NOT NULL auto_increment,
  link_url varchar(255) NOT NULL default '',
  link_name varchar(255) NOT NULL default '',
  link_image varchar(255) NOT NULL default '',
  link_target varchar(25) NOT NULL default '',
  link_category int(11) NOT NULL default '0',
  link_description varchar(255) NOT NULL default '',
  link_visible enum('Y','N') NOT NULL default 'Y',
  link_owner int(11) NOT NULL default '1',
  link_rating int(11) NOT NULL default '0',
  link_updated datetime NOT NULL default '0000-00-00 00:00:00',
  link_rel varchar(255) NOT NULL default '',
  link_notes mediumtext NOT NULL,
  link_rss varchar(255) NOT NULL default '',
  PRIMARY KEY  (link_id),
  KEY link_category (link_category),
  KEY link_visible (link_visible)
);
CREATE TABLE $wpdb->options (
  option_id int(11) NOT NULL auto_increment,
  blog_id int(11) NOT NULL default '0',
  option_name varchar(64) NOT NULL default '',
  option_can_override enum('Y','N') NOT NULL default 'Y',
  option_type int(11) NOT NULL default '1',
  option_value text NOT NULL,
  option_width int(11) NOT NULL default '20',
  option_height int(11) NOT NULL default '8',
  option_description tinytext NOT NULL,
  option_admin_level int(11) NOT NULL default '1',
  autoload enum('yes','no') NOT NULL default 'yes',
  PRIMARY KEY  (option_id,blog_id,option_name)
);
CREATE TABLE $wpdb->post2cat (
  rel_id int(11) NOT NULL auto_increment,
  post_id int(11) NOT NULL default '0',
  category_id int(11) NOT NULL default '0',
  PRIMARY KEY  (rel_id),
  KEY post_id (post_id,category_id)
);
CREATE TABLE $wpdb->postmeta (
  meta_id int(11) NOT NULL auto_increment,
  post_id int(11) NOT NULL default '0',
  meta_key varchar(255) default NULL,
  meta_value text,
  PRIMARY KEY  (meta_id),
  KEY post_id (post_id),
  KEY meta_key (meta_key)
);
CREATE TABLE $wpdb->posts (
  ID int(10) unsigned NOT NULL auto_increment,
  post_author int(4) NOT NULL default '0',
  post_date datetime NOT NULL default '0000-00-00 00:00:00',
  post_date_gmt datetime NOT NULL default '0000-00-00 00:00:00',
  post_content text NOT NULL,
  post_title text NOT NULL,
  post_category int(4) NOT NULL default '0',
  post_excerpt text NOT NULL,
  post_lat float default NULL,
  post_lon float default NULL,
  post_status enum('publish','draft','private','static') NOT NULL default 'publish',
  comment_status enum('open','closed','registered_only') NOT NULL default 'open',
  ping_status enum('open','closed') NOT NULL default 'open',
  post_password varchar(20) NOT NULL default '',
  post_name varchar(200) NOT NULL default '',
  to_ping text NOT NULL,
  pinged text NOT NULL,
  post_modified datetime NOT NULL default '0000-00-00 00:00:00',
  post_modified_gmt datetime NOT NULL default '0000-00-00 00:00:00',
  post_content_filtered text NOT NULL,
  post_parent int(11) NOT NULL default '0',
  PRIMARY KEY  (ID),
  KEY post_date (post_date),
  KEY post_date_gmt (post_date_gmt),
  KEY post_name (post_name),
  KEY post_status (post_status)
);
CREATE TABLE $wpdb->users (
  ID int(10) unsigned NOT NULL auto_increment,
  user_login varchar(20) NOT NULL default '',
  user_pass varchar(64) NOT NULL default '',
  user_firstname varchar(50) NOT NULL default '',
  user_lastname varchar(50) NOT NULL default '',
  user_nickname varchar(50) NOT NULL default '',
  user_nicename varchar(50) NOT NULL default '',
  user_icq int(10) unsigned NOT NULL default '0',
  user_email varchar(100) NOT NULL default '',
  user_url varchar(100) NOT NULL default '',
  user_ip varchar(15) NOT NULL default '',
  user_domain varchar(200) NOT NULL default '',
  user_browser varchar(200) NOT NULL default '',
  dateYMDhour datetime NOT NULL default '0000-00-00 00:00:00',
  user_level int(2) unsigned NOT NULL default '0',
  user_aim varchar(50) NOT NULL default '',
  user_msn varchar(100) NOT NULL default '',
  user_yim varchar(50) NOT NULL default '',
  user_idmode varchar(20) NOT NULL default '',
  user_activation_key varchar(60) NOT NULL default '',
  user_status int(11) NOT NULL default '0',
  PRIMARY KEY  (ID),
  UNIQUE KEY user_login (user_login)
);";



function upgrade_all() {
	populate_options();
	upgrade_071();
	upgrade_072();
	upgrade_100();
	upgrade_101();
	upgrade_110();
	upgrade_130();
}

function populate_options() {

	$guessurl = preg_replace('|/wp-admin/.*|i', '', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	add_option('siteurl', $guessurl, 'WordPress web address');
	add_option('blogfilename', '', 'Default file for blog');
	add_option('blogname', 'My Weblog', 'Blog title');
	add_option('blogdescription', '', 'Short tagline');
	add_option('new_users_can_blog', 0);
	add_option('users_can_register', 1);
	add_option('admin_email', 'you@example.com');
	add_option('start_of_week', 1);
	add_option('use_balanceTags', 1);
	add_option('use_smilies', 1);
	add_option('require_name_email', 1);
	add_option('comments_notify', 1);
	add_option('posts_per_rss', 10);
	add_option('rss_excerpt_length', 50);
	add_option('rss_use_excerpt', 0);
	add_option('use_fileupload', 0);
	add_option('fileupload_realpath', ABSPATH . 'wp-content');
	add_option('fileupload_url', get_option('siteurl') . '/wp-content');
	add_option('fileupload_allowedtypes', 'jpg jpeg gif png');
	add_option('fileupload_maxk', 300);
	add_option('fileupload_minlevel', 6);
	add_option('mailserver_url', 'mail.example.com');
	add_option('mailserver_login', 'login@example.com');
	add_option('mailserver_pass', 'password');
	add_option('mailserver_port', 110);
	add_option('default_category', 1);
	add_option('default_comment_status', 'open');
	add_option('default_ping_status', 'open');
	add_option('default_pingback_flag', 1);
	add_option('default_post_edit_rows', 9);
	add_option('posts_per_page', 10);
	add_option('what_to_show', 'posts');
	add_option('date_format', 'F j, Y');
	add_option('time_format', 'g:i a');
	add_option('use_geo_positions', 0);
	add_option('use_default_geourl', 0);
	add_option('default_geourl_lat', 0);
	add_option('default_geourl_lon', 0);
	add_option('weblogs_xml_url', 'http://static.wordpress.org/changes.xml');
	add_option('links_updated_date_format', 'F j, Y g:i a');
	add_option('links_recently_updated_prepend', '<em>');
	add_option('links_recently_updated_append', '</em>');
	add_option('links_recently_updated_time', 120);
	add_option('comment_moderation', 0);
	add_option('moderation_notify', 1);
	add_option('permalink_structure');
	add_option('gzipcompression', 0);
	add_option('hack_file', 0);
	add_option('blog_charset', 'utf-8');
	add_option('moderation_keys');
	add_option('active_plugins');
	add_option('home');
	add_option('category_base');
	add_option('ping_sites', 'http://rpc.pingomatic.com/');
	add_option('advanced_edit', 0);
	add_option('comment_max_links', 2);

	// Delete unused options
	$unusedoptions = array ('blodotgsping_url', 'bodyterminator', 'emailtestonly', 'phoneemail_separator', 'smilies_directory', 'subjectprefix', 'use_bbcode', 'use_blodotgsping', 'use_phoneemail', 'use_quicktags', 'use_weblogsping', 'weblogs_cache_file', 'use_preview', 'use_htmltrans', 'smilies_directory', 'rss_language', 'fileupload_allowedusers', 'use_phoneemail', 'default_post_status', 'default_post_category', 'archive_mode', 'time_difference', 'links_minadminlevel', 'links_use_adminlevels', 'links_rating_type', 'links_rating_char', 'links_rating_ignore_zero', 'links_rating_single_image', 'links_rating_image0', 'links_rating_image1', 'links_rating_image2', 'links_rating_image3', 'links_rating_image4', 'links_rating_image5', 'links_rating_image6', 'links_rating_image7', 'links_rating_image8', 'links_rating_image9', 'weblogs_cacheminutes', 'comment_allowed_tags', 'search_engine_friendly_urls');
	foreach ($unusedoptions as $option) :
		delete_option($option);
	endforeach;
}

function upgrade_071() {
	global $wpdb;
	maybe_add_column($wpdb->posts, 'post_status', "ALTER TABLE $wpdb->posts ADD `post_status` ENUM('publish','draft','private') NOT NULL");
	maybe_add_column($wpdb->posts, 'comment_status', "ALTER TABLE $wpdb->posts ADD `comment_status` ENUM('open','closed') NOT NULL");
	maybe_add_column($wpdb->posts, 'ping_status', "ALTER TABLE $wpdb->posts ADD `ping_status` ENUM('open','closed') NOT NULL");
	maybe_add_column($wpdb->posts, 'post_password', "ALTER TABLE $wpdb->posts ADD post_password varchar(20) NOT NULL");
}

function upgrade_072() {
	global $wpdb;
	maybe_add_column($wpdb->links, 'link_notes', "ALTER TABLE $wpdb->links ADD COLUMN link_notes MEDIUMTEXT NOT NULL DEFAULT '' ");
	maybe_add_column($wpdb->linkcategories, 'show_images', "ALTER TABLE $wpdb->linkcategories ADD COLUMN show_images enum('Y','N') NOT NULL default 'Y'");
	maybe_add_column($wpdb->linkcategories, 'show_description', "ALTER TABLE $wpdb->linkcategories ADD COLUMN show_description enum('Y','N') NOT NULL default 'Y'");
	maybe_add_column($wpdb->linkcategories, 'show_rating', "ALTER TABLE $wpdb->linkcategories ADD COLUMN show_rating enum('Y','N') NOT NULL default 'Y'");
	maybe_add_column($wpdb->linkcategories, 'show_updated', "ALTER TABLE $wpdb->linkcategories ADD COLUMN show_updated enum('Y','N') NOT NULL default 'Y'");
	maybe_add_column($wpdb->linkcategories, 'sort_order', "ALTER TABLE $wpdb->linkcategories ADD COLUMN sort_order varchar(64) NOT NULL default 'name'");
	maybe_add_column($wpdb->linkcategories, 'sort_desc', "ALTER TABLE $wpdb->linkcategories ADD COLUMN sort_desc enum('Y','N') NOT NULL default 'N'");
	maybe_add_column($wpdb->linkcategories, 'text_before_link', "ALTER TABLE $wpdb->linkcategories ADD COLUMN text_before_link varchar(128) not null default '<li>'");
	maybe_add_column($wpdb->linkcategories, 'text_after_link', "ALTER TABLE $wpdb->linkcategories ADD COLUMN text_after_link  varchar(128) not null default '<br />'");
	maybe_add_column($wpdb->linkcategories, 'text_after_all', "ALTER TABLE $wpdb->linkcategories ADD COLUMN text_after_all  varchar(128) not null default '</li>'");
	maybe_add_column($wpdb->linkcategories, 'list_limit', "ALTER TABLE $wpdb->linkcategories ADD COLUMN list_limit int not null default -1");
	maybe_add_column($wpdb->posts, 'post_lon', "ALTER TABLE $wpdb->posts ADD COLUMN post_lon float");
	maybe_add_column($wpdb->posts, 'post_lat', "ALTER TABLE $wpdb->posts ADD COLUMN post_lat float ");
	maybe_create_table($wpdb->options, "
	CREATE TABLE $wpdb->options (
	  option_id int(11) NOT NULL auto_increment,
	  blog_id int(11) NOT NULL default 0,
	  option_name varchar(64) NOT NULL default '',
	  option_can_override enum('Y','N') NOT NULL default 'Y',
	  option_type int(11) NOT NULL default 1,
	  option_value varchar(255) NOT NULL default '',
	  option_width int NOT NULL default 20,
	  option_height int NOT NULL default 8,
	  option_description tinytext NOT NULL default '',
	  option_admin_level int NOT NULL DEFAULT '1',
	  PRIMARY KEY (option_id, blog_id, option_name)
	)
	");
}

function upgrade_100() {
	global $wpdb;
	maybe_add_column($wpdb->posts, 'post_name', "ALTER TABLE `$wpdb->posts` ADD `post_name` VARCHAR(200) NOT NULL");
	maybe_add_column($wpdb->posts, 'to_ping', "ALTER TABLE $wpdb->posts ADD `to_ping` TEXT NOT NULL");
	maybe_add_column($wpdb->posts, 'pinged', "ALTER TABLE $wpdb->posts ADD `pinged` TEXT NOT NULL");
	maybe_add_column($wpdb->posts, 'post_modified', "ALTER TABLE $wpdb->posts ADD `post_modified` DATETIME NOT NULL");
	maybe_add_column($wpdb->posts, 'post_content_filtered', "ALTER TABLE $wpdb->posts ADD `post_content_filtered` TEXT NOT NULL");
	maybe_add_column($wpdb->categories, 'category_nicename', "ALTER TABLE `$wpdb->categories` ADD `category_nicename` VARCHAR(200) NOT NULL");	
	maybe_add_column($wpdb->categories, 'category_description', "ALTER TABLE `$wpdb->categories` ADD `category_description` TEXT NOT NULL");
	maybe_add_column($wpdb->categories, 'category_parent', "ALTER TABLE `$wpdb->categories` ADD `category_parent` INT(4) NOT NULL");
	maybe_add_column($wpdb->links, 'link_rss', "ALTER TABLE `$wpdb->links` ADD `link_rss` VARCHAR( 255 ) NOT NULL;");
	maybe_add_column($wpdb->users, 'user_description', "ALTER TABLE `$wpdb->users` ADD `user_description` TEXT NOT NULL");
	maybe_add_column($wpdb->comments, 'comment_approved', "ALTER TABLE $wpdb->comments ADD COLUMN comment_approved ENUM('0', '1') DEFAULT '1' NOT NULL");

	// Create indicies
	add_clean_index($wpdb->posts, 'post_name');
	add_clean_index($wpdb->categories, 'category_nicename');
	add_clean_index($wpdb->comments, 'comment_approved');

	// Get the title and ID of every post, post_name to check if it already has a value
	$posts = $wpdb->get_results("SELECT ID, post_title, post_name FROM $wpdb->posts WHERE post_name = ''");
	if ($posts) {
		foreach($posts as $post) {
			if ('' == $post->post_name) { 
				$newtitle = sanitize_title($post->post_title);
				$wpdb->query("UPDATE $wpdb->posts SET post_name = '$newtitle' WHERE ID = '$post->ID'");
			}
		}
	}
	
	$categories = $wpdb->get_results("SELECT cat_ID, cat_name, category_nicename FROM $wpdb->categories");
	foreach ($categories as $category) {
		if ('' == $category->category_nicename) { 
			$newtitle = sanitize_title($category->cat_name);
			$wpdb->query("UPDATE $wpdb->categories SET category_nicename = '$newtitle' WHERE cat_ID = '$category->cat_ID'");
		}
	}


	$wpdb->query("UPDATE $wpdb->options SET option_value = REPLACE(option_value, 'wp-links/links-images/', 'wp-images/links/')
                                                      WHERE option_name LIKE 'links_rating_image%'
                                                      AND option_value LIKE 'wp-links/links-images/%'");

	// Multiple categories
	maybe_create_table($wpdb->post2cat, "
		CREATE TABLE `$wpdb->post2cat` (
		`rel_id` INT NOT NULL AUTO_INCREMENT ,
		`post_id` INT NOT NULL ,
		`category_id` INT NOT NULL ,
		PRIMARY KEY ( `rel_id` ) ,
		INDEX ( `post_id` , `category_id` )
		)
		");

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
			$cat = $wpdb->get_row("SELECT * FROM $wpdb->post2cat WHERE post_id = $post->ID AND category_id = $post->post_category");
			if (!$cat && 0 != $post->post_category) { // If there's no result
				$wpdb->query("
					INSERT INTO $wpdb->post2cat
					(post_id, category_id)
					VALUES
					('$post->ID', '$post->post_category')
					");
			}
		}
	endif;
}

function upgrade_101() {
	global $wpdb;

	// Less intrusive default
	$wpdb->query("ALTER TABLE `$wpdb->linkcategories` CHANGE `show_description` `show_description` ENUM( 'Y', 'N' ) DEFAULT 'N' NOT NULL"); 
	
	// Clean up indices, add a few
	add_clean_index($wpdb->posts, 'post_name');
	add_clean_index($wpdb->posts, 'post_status');
	add_clean_index($wpdb->categories, 'category_nicename');
	add_clean_index($wpdb->comments, 'comment_approved');
	add_clean_index($wpdb->comments, 'comment_post_ID');
	add_clean_index($wpdb->links , 'link_category');
	add_clean_index($wpdb->links , 'link_visible');
}


function upgrade_110() {
  global $wpdb;
	
	maybe_add_column($wpdb->comments, 'user_id', "ALTER TABLE `$wpdb->comments` ADD `user_id` INT DEFAULT '0' NOT NULL ;");
	maybe_add_column($wpdb->users, 'user_activation_key', "ALTER TABLE `$wpdb->users` ADD `user_activation_key` VARCHAR( 60 ) NOT NULL ;");
	maybe_add_column($wpdb->users, 'user_status', "ALTER TABLE `$wpdb->users` ADD `user_status` INT DEFAULT '0' NOT NULL ;");
	$wpdb->query("ALTER TABLE `$wpdb->posts` CHANGE `comment_status` `comment_status` ENUM( 'open', 'closed', 'registered_only' ) DEFAULT 'open' NOT NULL");

	maybe_add_column($wpdb->users, 'user_nicename', "ALTER TABLE `$wpdb->users` ADD `user_nicename` VARCHAR(50) DEFAULT '' NOT NULL ;");
	maybe_add_column($wpdb->posts, 'post_date_gmt', "ALTER TABLE $wpdb->posts ADD post_date_gmt DATETIME NOT NULL AFTER post_date");
	maybe_add_column($wpdb->posts, 'post_modified_gmt', "ALTER TABLE $wpdb->posts ADD post_modified_gmt DATETIME NOT NULL AFTER post_modified");
	maybe_add_column($wpdb->comments, 'comment_date_gmt', "ALTER TABLE $wpdb->comments ADD comment_date_gmt DATETIME NOT NULL AFTER comment_date");

    // Set user_nicename.
	$users = $wpdb->get_results("SELECT ID, user_nickname, user_nicename FROM $wpdb->users");
	foreach ($users as $user) {
		if ('' == $user->user_nicename) { 
			$newname = sanitize_title($user->user_nickname);
			$wpdb->query("UPDATE $wpdb->users SET user_nicename = '$newname' WHERE ID = '$user->ID'");
		}
	}

	// Convert passwords to MD5 and update table appropiately
	$user_table = $wpdb->get_row("DESCRIBE $wpdb->users user_pass");
	if ($user_table->Type != 'varchar(32)') {
		$wpdb->query("ALTER TABLE $wpdb->users MODIFY user_pass varchar(64) not null");
	}
	
	$query = 'SELECT ID, user_pass from '.$wpdb->users;
	foreach ($wpdb->get_results($query) as $row) {
		if (!preg_match('/^[A-Fa-f0-9]{32}$/', $row->user_pass)) {
			   $wpdb->query('UPDATE '.$wpdb->users.' SET user_pass = MD5(\''.$row->user_pass.'\') WHERE ID = \''.$row->ID.'\'');
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
		$wpdb->query("UPDATE $wpdb->users SET dateYMDhour = DATE_ADD(dateYMDhour, INTERVAL '$add_hours:$add_minutes' HOUR_MINUTE)");
	}

	// post-meta
	maybe_create_table($wpdb->postmeta, "
	CREATE TABLE $wpdb->postmeta (
	  meta_id int(11) NOT NULL auto_increment,
	  post_id int(11) NOT NULL default 0,
	  meta_key varchar(255),
	  meta_value text,
	  PRIMARY KEY (meta_id),
	  INDEX (post_id),
	  INDEX (meta_key)
	)
	");

	// First we need to enlarge option_value so it can hold larger values:
	$wpdb->query("ALTER TABLE `$wpdb->options` CHANGE `option_value` `option_value` TEXT NOT NULL");

	// Fix for CVS versions
	$wpdb->query("UPDATE $wpdb->options SET option_type = '5' WHERE option_name = 'advanced_edit'");

	// Forward-thinking
	$wpdb->query("ALTER TABLE `$wpdb->posts` CHANGE `post_status` `post_status` ENUM( 'publish', 'draft', 'private', 'static' ) DEFAULT 'publish' NOT NULL");
	maybe_add_column($wpdb->posts, 'post_parent', "ALTER TABLE `$wpdb->posts` ADD `post_parent` INT NOT NULL ;");


	$wpdb->query("ALTER TABLE `$wpdb->comments` CHANGE `comment_author_url` `comment_author_url` VARCHAR( 200 ) NOT NULL");
}

function upgrade_130() {
    global $wpdb, $table_prefix;

	add_option('default_email_category', 1, 'Posts by email go to this category');
	add_option('recently_edited');
	add_option('use_linksupdate', 0);

	maybe_add_column($wpdb->options, 'autoload', "ALTER TABLE `$wpdb->options` ADD `autoload` ENUM( 'yes', 'no' ) NOT NULL ;");

	// Set up a few options not to load by default
	$fatoptions = array( 'moderation_keys', 'recently_edited' );
	foreach ($fatoptions as $fatoption) :
		$wpdb->query("UPDATE $wpdb->options SET `autoload` = 'no' WHERE option_name = '$fatoption'");
	endforeach;

    // Remove extraneous backslashes.
	$posts = $wpdb->get_results("SELECT ID, post_title, post_content, post_excerpt FROM $wpdb->posts");
	if ($posts) {
		foreach($posts as $post) {
            $post_content = addslashes(deslash($post->post_content));
            $post_title = addslashes(deslash($post->post_title));
            $post_excerpt = addslashes(deslash($post->post_excerpt));
            $wpdb->query("UPDATE $wpdb->posts SET post_title = '$post_title', post_content = '$post_content', post_excerpt = '$post_excerpt' WHERE ID = '$post->ID'");
		}
	}

    // Remove extraneous backslashes.
	$comments = $wpdb->get_results("SELECT comment_ID, comment_author, comment_content FROM $wpdb->comments");
	if ($comments) {
		foreach($comments as $comment) {
            $comment_content = addslashes(deslash($comment->comment_content));
            $comment_author = addslashes(deslash($comment->comment_author));
            $wpdb->query("UPDATE $wpdb->comments SET comment_content = '$comment_content', comment_author = '$comment_author' WHERE comment_ID = '$comment->comment_ID'");
		}
	}

    // Remove extraneous backslashes.
	$links = $wpdb->get_results("SELECT link_id, link_name, link_description FROM $wpdb->links");
	if ($links) {
		foreach($links as $link) {
            $link_name = addslashes(deslash($link->link_name));
            $link_description = addslashes(deslash($link->link_description));
            $wpdb->query("UPDATE $wpdb->links SET link_name = '$link_name', link_description = '$link_description' WHERE link_id = '$link->link_id'");
		}
	}

    // The "paged" option for what_to_show is no more.
    if ($wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name = 'what_to_show'") == 'paged') {
        $wpdb->query("UPDATE $wpdb->options SET option_value = 'posts' WHERE option_name = 'what_to_show'");
    }

	if ( !is_array( get_settings('active_plugins') ) ) {
		$plugins = explode("\n", trim(get_settings('active_plugins')) );
		update_option('active_plugins', $plugins);
	}

	// Obsolete tables
	$wpdb->query('DROP TABLE IF EXISTS ' . $table_prefix . 'optionvalues');
	$wpdb->query('DROP TABLE IF EXISTS ' . $table_prefix . 'optiontypes');
	$wpdb->query('DROP TABLE IF EXISTS ' . $table_prefix . 'optiongroups');
	$wpdb->query('DROP TABLE IF EXISTS ' . $table_prefix . 'optiongroup_options');
}

// The functions we use to actually do stuff

// General
function maybe_create_table($table_name, $create_ddl) {
    global $wpdb;
    foreach ($wpdb->get_col("SHOW TABLES",0) as $table ) {
        if ($table == $table_name) {
            return true;
        }
    }
    //didn't find it try to create it.
    $q = $wpdb->query($create_ddl);
    // we cannot directly tell that whether this succeeded!
    foreach ($wpdb->get_col("SHOW TABLES",0) as $table ) {
        if ($table == $table_name) {
            return true;
        }
    }
    return false;
}

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


// get_alloptions as it was for 1.2.
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

function dbDelta($queries, $execute = true) {
	global $wpdb;
	
	// Seperate individual queries into an array
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
			$cqueries[strtolower($matches[1])] = $qry;
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
					$fieldname = $fvals[1];
					
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
							//echo "<pre style=\"border:1px solid #ccc;margin-top:5px;\">{$table}:<br/>Found index:".$index_string."</pre>\n";
						}
						//else echo "<pre style=\"border:1px solid #ccc;margin-top:5px;\">{$table}:<br/><b>Did not find index:</b>".$index_string."<br/>".print_r($indices, true)."</pre>\n";
					}
				}

				// For every remaining index specified for the table
				foreach($indices as $index) {
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

function make_db_current() {
	global $wp_queries;

	$alterations = dbDelta($wp_queries);
	echo "<ol>\n";
	foreach($alterations as $alteration) echo "<li>$alteration</li>\n";
	echo "</ol>\n";
}

function rename_field($table, $field, $new) {
//	ALTER TABLE `wp_users` CHANGE `ID` `user_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT 
}

function remove_field($table, $field) {
	global $wpdb;
// ALTER TABLE `wp_users` DROP `user_domain` 
}

?>