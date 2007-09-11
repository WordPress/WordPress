<?php
// Here we keep the DB structure and option values

$charset_collate = '';

if ( version_compare(mysql_get_server_info(), '4.1.0', '>=') ) {
	if ( ! empty($wpdb->charset) )
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
	if ( ! empty($wpdb->collate) )
		$charset_collate .= " COLLATE $wpdb->collate";
}

$wp_queries="CREATE TABLE $wpdb->terms (
 term_id bigint(20) NOT NULL auto_increment,
 name varchar(55) NOT NULL default '',
 slug varchar(200) NOT NULL default '',
 term_group bigint(10) NOT NULL default 0,
 PRIMARY KEY  (term_id),
 UNIQUE KEY slug (slug)
) $charset_collate;
CREATE TABLE $wpdb->term_taxonomy (
 term_taxonomy_id bigint(20) NOT NULL auto_increment,
 term_id bigint(20) NOT NULL default 0,
 taxonomy varchar(32) NOT NULL default '',
 description longtext NOT NULL,
 parent bigint(20) NOT NULL default 0,
 count bigint(20) NOT NULL default 0,
 PRIMARY KEY  (term_taxonomy_id),
 UNIQUE KEY term_id_taxonomy (term_id,taxonomy)
) $charset_collate;
CREATE TABLE $wpdb->term_relationships (
 object_id bigint(20) NOT NULL default 0,
 term_taxonomy_id bigint(20) NOT NULL default 0,
 PRIMARY KEY  (object_id,term_taxonomy_id),
 KEY term_taxonomy_id (term_taxonomy_id)
) $charset_collate;
CREATE TABLE $wpdb->comments (
  comment_ID bigint(20) unsigned NOT NULL auto_increment,
  comment_post_ID int(11) NOT NULL default '0',
  comment_author tinytext NOT NULL,
  comment_author_email varchar(100) NOT NULL default '',
  comment_author_url varchar(200) NOT NULL default '',
  comment_author_IP varchar(100) NOT NULL default '',
  comment_date datetime NOT NULL default '0000-00-00 00:00:00',
  comment_date_gmt datetime NOT NULL default '0000-00-00 00:00:00',
  comment_content text NOT NULL,
  comment_karma int(11) NOT NULL default '0',
  comment_approved enum('0','1','spam') NOT NULL default '1',
  comment_agent varchar(255) NOT NULL default '',
  comment_type varchar(20) NOT NULL default '',
  comment_parent bigint(20) NOT NULL default '0',
  user_id bigint(20) NOT NULL default '0',
  PRIMARY KEY  (comment_ID),
  KEY comment_approved (comment_approved),
  KEY comment_post_ID (comment_post_ID)
) $charset_collate;
CREATE TABLE $wpdb->links (
  link_id bigint(20) NOT NULL auto_increment,
  link_url varchar(255) NOT NULL default '',
  link_name varchar(255) NOT NULL default '',
  link_image varchar(255) NOT NULL default '',
  link_target varchar(25) NOT NULL default '',
  link_category bigint(20) NOT NULL default '0',
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
) $charset_collate;
CREATE TABLE $wpdb->options (
  option_id bigint(20) NOT NULL auto_increment,
  blog_id int(11) NOT NULL default '0',
  option_name varchar(64) NOT NULL default '',
  option_value longtext NOT NULL,
  autoload enum('yes','no') NOT NULL default 'yes',
  PRIMARY KEY  (option_id,blog_id,option_name),
  KEY option_name (option_name)
) $charset_collate;
CREATE TABLE $wpdb->postmeta (
  meta_id bigint(20) NOT NULL auto_increment,
  post_id bigint(20) NOT NULL default '0',
  meta_key varchar(255) default NULL,
  meta_value longtext,
  PRIMARY KEY  (meta_id),
  KEY post_id (post_id),
  KEY meta_key (meta_key)
) $charset_collate;
CREATE TABLE $wpdb->posts (
  ID bigint(20) unsigned NOT NULL auto_increment,
  post_author bigint(20) NOT NULL default '0',
  post_date datetime NOT NULL default '0000-00-00 00:00:00',
  post_date_gmt datetime NOT NULL default '0000-00-00 00:00:00',
  post_content longtext NOT NULL,
  post_title text NOT NULL,
  post_category int(4) NOT NULL default '0',
  post_excerpt text NOT NULL,
  post_status enum('publish','draft','private','static','object','attachment','inherit','future', 'pending') NOT NULL default 'publish',
  comment_status enum('open','closed','registered_only') NOT NULL default 'open',
  ping_status enum('open','closed') NOT NULL default 'open',
  post_password varchar(20) NOT NULL default '',
  post_name varchar(200) NOT NULL default '',
  to_ping text NOT NULL,
  pinged text NOT NULL,
  post_modified datetime NOT NULL default '0000-00-00 00:00:00',
  post_modified_gmt datetime NOT NULL default '0000-00-00 00:00:00',
  post_content_filtered text NOT NULL,
  post_parent bigint(20) NOT NULL default '0',
  guid varchar(255) NOT NULL default '',
  menu_order int(11) NOT NULL default '0',
  post_type varchar(20) NOT NULL default 'post',
  post_mime_type varchar(100) NOT NULL default '',
  comment_count bigint(20) NOT NULL default '0',
  PRIMARY KEY  (ID),
  KEY post_name (post_name),
  KEY type_status_date (post_type,post_status,post_date,ID)
) $charset_collate;
CREATE TABLE $wpdb->users (
  ID bigint(20) unsigned NOT NULL auto_increment,
  user_login varchar(60) NOT NULL default '',
  user_pass varchar(64) NOT NULL default '',
  user_nicename varchar(50) NOT NULL default '',
  user_email varchar(100) NOT NULL default '',
  user_url varchar(100) NOT NULL default '',
  user_registered datetime NOT NULL default '0000-00-00 00:00:00',
  user_activation_key varchar(60) NOT NULL default '',
  user_status int(11) NOT NULL default '0',
  display_name varchar(250) NOT NULL default '',
  PRIMARY KEY  (ID),
  KEY user_login_key (user_login),
  KEY user_nicename (user_nicename)
) $charset_collate;
CREATE TABLE $wpdb->usermeta (
  umeta_id bigint(20) NOT NULL auto_increment,
  user_id bigint(20) NOT NULL default '0',
  meta_key varchar(255) default NULL,
  meta_value longtext,
  PRIMARY KEY  (umeta_id),
  KEY user_id (user_id),
  KEY meta_key (meta_key)
) $charset_collate;";

function populate_options() {
	global $wpdb, $wp_db_version;

	$schema = ( isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ) ? 'https://' : 'http://';
	$guessurl = preg_replace('|/wp-admin/.*|i', '', $schema . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	add_option('siteurl', $guessurl);
	add_option('blogname', __('My Blog'));
	add_option('blogdescription', __('Just another WordPress weblog'));
	add_option('users_can_register', 0);
	add_option('admin_email', 'you@example.com');
	add_option('start_of_week', 1);
	add_option('use_balanceTags', 0);
	add_option('use_smilies', 1);
	add_option('require_name_email', 1);
	add_option('comments_notify', 1);
	add_option('posts_per_rss', 10);
	add_option('rss_excerpt_length', 50);
	add_option('rss_use_excerpt', 0);
	add_option('mailserver_url', 'mail.example.com');
	add_option('mailserver_login', 'login@example.com');
	add_option('mailserver_pass', 'password');
	add_option('mailserver_port', 110);
	add_option('default_category', 1);
	add_option('default_comment_status', 'open');
	add_option('default_ping_status', 'open');
	add_option('default_pingback_flag', 1);
	add_option('default_post_edit_rows', 10);
	add_option('posts_per_page', 10);
	add_option('what_to_show', 'posts');
	add_option('date_format', __('F j, Y'));
	add_option('time_format', __('g:i a'));
	add_option('links_updated_date_format', __('F j, Y g:i a'));
	add_option('links_recently_updated_prepend', '<em>');
	add_option('links_recently_updated_append', '</em>');
	add_option('links_recently_updated_time', 120);
	add_option('comment_moderation', 0);
	add_option('moderation_notify', 1);
	add_option('permalink_structure');
	add_option('gzipcompression', 0);
	add_option('hack_file', 0);
	add_option('blog_charset', 'UTF-8');
	add_option('moderation_keys');
	add_option('active_plugins');
	add_option('home', $guessurl);
	// in case it is set, but blank, update "home"
	if ( !__get_option('home') ) update_option('home', $guessurl);
	add_option('category_base');
	add_option('ping_sites', 'http://rpc.pingomatic.com/');
	add_option('advanced_edit', 0);
	add_option('comment_max_links', 2);
	add_option('gmt_offset', date('Z') / 3600);
	// 1.5
	add_option('default_email_category', 1);
	add_option('recently_edited');
	add_option('use_linksupdate', 0);
	add_option('template', 'default');
	add_option('stylesheet', 'default');
	add_option('comment_whitelist', 1);
	add_option('page_uris');
	add_option('blacklist_keys');
	add_option('comment_registration', 0);
	add_option('rss_language', 'en');
	add_option('html_type', 'text/html');
	// 1.5.1
	add_option('use_trackback', 0);
	// 2.0
	add_option('default_role', 'subscriber');
	add_option('db_version', $wp_db_version);
	// 2.0.1
	if ( ini_get('safe_mode') ) {
		// Safe mode screws up mkdir(), so we must use a flat structure.
		add_option('uploads_use_yearmonth_folders', 0);
		add_option('upload_path', 'wp-content');
	} else {
		add_option('uploads_use_yearmonth_folders', 1);
		add_option('upload_path', 'wp-content/uploads');
	}

	// 2.0.3
	add_option('secret', md5(uniqid(microtime())));

	// 2.1
	add_option('blog_public', '1');
	add_option('default_link_category', 2);
	add_option('show_on_front', 'posts');

	// 2.2
	add_option('tag_base');

	// Delete unused options
	$unusedoptions = array ('blodotgsping_url', 'bodyterminator', 'emailtestonly', 'phoneemail_separator', 'smilies_directory', 'subjectprefix', 'use_bbcode', 'use_blodotgsping', 'use_phoneemail', 'use_quicktags', 'use_weblogsping', 'weblogs_cache_file', 'use_preview', 'use_htmltrans', 'smilies_directory', 'fileupload_allowedusers', 'use_phoneemail', 'default_post_status', 'default_post_category', 'archive_mode', 'time_difference', 'links_minadminlevel', 'links_use_adminlevels', 'links_rating_type', 'links_rating_char', 'links_rating_ignore_zero', 'links_rating_single_image', 'links_rating_image0', 'links_rating_image1', 'links_rating_image2', 'links_rating_image3', 'links_rating_image4', 'links_rating_image5', 'links_rating_image6', 'links_rating_image7', 'links_rating_image8', 'links_rating_image9', 'weblogs_cacheminutes', 'comment_allowed_tags', 'search_engine_friendly_urls', 'default_geourl_lat', 'default_geourl_lon', 'use_default_geourl', 'weblogs_xml_url', 'new_users_can_blog', '_wpnonce', '_wp_http_referer', 'Update', 'action', 'rich_editing');
	foreach ($unusedoptions as $option) :
		delete_option($option);
	endforeach;

	// Set up a few options not to load by default
	$fatoptions = array( 'moderation_keys', 'recently_edited', 'blacklist_keys' );
	foreach ($fatoptions as $fatoption) :
		$wpdb->query("UPDATE $wpdb->options SET `autoload` = 'no' WHERE option_name = '$fatoption'");
	endforeach;
}

function populate_roles() {
	populate_roles_160();
	populate_roles_210();
	populate_roles_230();
}

function populate_roles_160() {
	global $wp_roles;

	// Add roles
	add_role('administrator', __('Administrator'));
	add_role('editor', __('Editor'));
	add_role('author', __('Author'));
	add_role('contributor', __('Contributor'));
	add_role('subscriber', __('Subscriber'));

	// Add caps for Administrator role
	$role = get_role('administrator');
	$role->add_cap('switch_themes');
	$role->add_cap('edit_themes');
	$role->add_cap('activate_plugins');
	$role->add_cap('edit_plugins');
	$role->add_cap('edit_users');
	$role->add_cap('edit_files');
	$role->add_cap('manage_options');
	$role->add_cap('moderate_comments');
	$role->add_cap('manage_categories');
	$role->add_cap('manage_links');
	$role->add_cap('upload_files');
	$role->add_cap('import');
	$role->add_cap('unfiltered_html');
	$role->add_cap('edit_posts');
	$role->add_cap('edit_others_posts');
	$role->add_cap('edit_published_posts');
	$role->add_cap('publish_posts');
	$role->add_cap('edit_pages');
	$role->add_cap('read');
	$role->add_cap('level_10');
	$role->add_cap('level_9');
	$role->add_cap('level_8');
	$role->add_cap('level_7');
	$role->add_cap('level_6');
	$role->add_cap('level_5');
	$role->add_cap('level_4');
	$role->add_cap('level_3');
	$role->add_cap('level_2');
	$role->add_cap('level_1');
	$role->add_cap('level_0');

	// Add caps for Editor role
	$role = get_role('editor');
	$role->add_cap('moderate_comments');
	$role->add_cap('manage_categories');
	$role->add_cap('manage_links');
	$role->add_cap('upload_files');
	$role->add_cap('unfiltered_html');
	$role->add_cap('edit_posts');
	$role->add_cap('edit_others_posts');
	$role->add_cap('edit_published_posts');
	$role->add_cap('publish_posts');
	$role->add_cap('edit_pages');
	$role->add_cap('read');
	$role->add_cap('level_7');
	$role->add_cap('level_6');
	$role->add_cap('level_5');
	$role->add_cap('level_4');
	$role->add_cap('level_3');
	$role->add_cap('level_2');
	$role->add_cap('level_1');
	$role->add_cap('level_0');

	// Add caps for Author role
	$role = get_role('author');
	$role->add_cap('upload_files');
	$role->add_cap('edit_posts');
	$role->add_cap('edit_published_posts');
	$role->add_cap('publish_posts');
	$role->add_cap('read');
	$role->add_cap('level_2');
	$role->add_cap('level_1');
	$role->add_cap('level_0');

	// Add caps for Contributor role
	$role = get_role('contributor');
	$role->add_cap('edit_posts');
	$role->add_cap('read');
	$role->add_cap('level_1');
	$role->add_cap('level_0');

	// Add caps for Subscriber role
	$role = get_role('subscriber');
	$role->add_cap('read');
	$role->add_cap('level_0');
}

function populate_roles_210() {
	$roles = array('administrator', 'editor');
	foreach ($roles as $role) {
		$role = get_role($role);
		if ( empty($role) )
			continue;

		$role->add_cap('edit_others_pages');
		$role->add_cap('edit_published_pages');
		$role->add_cap('publish_pages');
		$role->add_cap('delete_pages');
		$role->add_cap('delete_others_pages');
		$role->add_cap('delete_published_pages');
		$role->add_cap('delete_posts');
		$role->add_cap('delete_others_posts');
		$role->add_cap('delete_published_posts');
		$role->add_cap('delete_private_posts');
		$role->add_cap('edit_private_posts');
		$role->add_cap('read_private_posts');
		$role->add_cap('delete_private_pages');
		$role->add_cap('edit_private_pages');
		$role->add_cap('read_private_pages');
	}

	$role = get_role('administrator');
	if ( ! empty($role) ) {
		$role->add_cap('delete_users');
		$role->add_cap('create_users');
	}

	$role = get_role('author');
	if ( ! empty($role) ) {
		$role->add_cap('delete_posts');
		$role->add_cap('delete_published_posts');
	}

	$role = get_role('contributor');
	if ( ! empty($role) ) {
		$role->add_cap('delete_posts');
	}
}

function populate_roles_230() {
	$role = get_role( 'administrator' );

	if ( !empty( $role ) ) {
		$role->add_cap( 'unfiltered_upload' );
	}
}

?>
