<?php
$_wp_installing = 1;
require('../wp-config.php');
require('wp-install-helper.php');

$thisfile = 'upgrade-071-to-072.php';
$step = $HTTP_GET_VARS['step'];
if (!$step) $step = 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<title>WordPress > .71 to .72 Upgrade</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<style media="screen" type="text/css">
	body {
		font-family: Georgia, "Times New Roman", Times, serif;
		margin-left: 15%;
		margin-right: 15%;
	}
	#logo {
		margin: 0;
		padding: 0;
		background-image: url(http://wordpress.org/images/wordpress.gif);
		background-repeat: no-repeat;
		height: 72px;
		border-bottom: 4px solid #333;
	}
	#logo a {
		display: block;
		height: 72px;
	}
	#logo a span {
		display: none;
	}
	p {
		line-height: 140%;
	}
	</style>
</head>
<body>
<h1 id="logo"><a href="http://wordpress.org"><span>WordPress</span></a></h1>
<?php
switch($step) {

	case 0:
?>
<p>Welcome to WordPress. You're already part of the family so this should be familiar 
  to you now. We think you'll find to like in this latest version, here are some 
  things to watch out for:</p>
<ul>

  <li>One of the biggest changes is that b2config.php has been removed! <strong>But</strong> we
  will use it <em>one last time</em> as part of this upgrade process. Some other files
  have been eliminated, so it's generally safest to delete all your old files
  (apart from b2config.php) before re-uploading the new ones.</li>

  <li>The new configuration file is called wp-config.php. We provide a
  <em>sample</em> version of this file. The only things you will have to
  configure in this file is your database connection info, and your table names.
  All other configuration info is now held in the database.</li>

  <li>If you have any troubles try out the <a
  href="http://wordpress.org/support/">support forums</a>.</li>

  <li><strong>Back up</strong> your database before you do anything. Yes, you.
  Right now.</li>

</ul>
<p><code></code>Have you looked at the <a href="../readme.html">readme</a>? If 
  you&#8217;re all ready, <a href="<?php echo $thisfile;?>?step=1">let's go</a>! </p>
<?php
	break;

    case 1:
?>
<h1>Step 1</h1>
<p>There are some changes we need to make to the links tables with this version, so lets get those out of 
  the way.</p>
<?php
$error_count = 0;
$tablename = $tablelinks;
$ddl = "ALTER TABLE $tablelinks ADD COLUMN link_notes MEDIUMTEXT NOT NULL DEFAULT '' ";
maybe_add_column($tablename, 'link_notes', $ddl);
if (check_column($tablelinks, 'link_notes', 'mediumtext')) {
    $res .= $tablename . ' - ok <br />';
} else {
    $res .= 'There was a problem with ' . $tablename . '<br />';
    ++$error_count;
}
?>
<p>OK, that wasn't too bad was it? Let's move on to <a href="<?php echo $thisfile;?>?step=2">step 2</a>!</p>

<?php
	case 2:
?>
<h1>Step 2</h1>
<p>There are a few new database tables with this version, so lets get those out of 
  the way.</p>
<?php
$error_count = 0;
$tablename = $tableoptions;
$ddl = "
CREATE TABLE $tableoptions (
  option_id int(11) NOT NULL auto_increment,
  blog_id int(11) NOT NULL default 0,
  option_name varchar(64) NOT NULL default '',
  option_can_override enum ('Y','N') NOT NULL default 'Y',
  option_type int(11) NOT NULL default 1,
  option_value varchar(255) NOT NULL default '',
  option_width int NOT NULL default 20,
  option_height int NOT NULL default 8,
  option_description tinytext NOT NULL default '',
  option_admin_level int NOT NULL DEFAULT '1',
  PRIMARY KEY (option_id, blog_id, option_name)
)
";

if (maybe_create_table($tablename, $ddl) == true) {
    $res .= $tablename . ' - ok <br />';
} else {
    $res .= 'There was a problem with ' . $tablename . '<br />';
    ++$error_count;
}

$tablename = $tableoptiontypes;
$ddl = "
CREATE TABLE $tableoptiontypes (
  optiontype_id int(11) NOT NULL auto_increment,
  optiontype_name varchar(64) NOT NULL,
  PRIMARY KEY (optiontype_id)
)
";

if (maybe_create_table($tablename, $ddl) == true) {
    $res .= $tablename . ' - ok <br />';
} else {
    $res .= 'There was a problem with ' . $tablename . '<br />';
    ++$error_count;
}

$tablename = $tableoptiongroups;
$ddl = "
CREATE TABLE $tableoptiongroups (
  group_id int(11) NOT NULL auto_increment,
  group_name varchar(64) not null,
  group_desc varchar(255),
  group_longdesc tinytext,
  PRIMARY KEY (group_id)
)
";

if (maybe_create_table($tablename, $ddl) == true) {
    $res .= $tablename . ' - ok <br />';
} else {
    $res .= 'There was a problem with ' . $tablename . '<br />';
    ++$error_count;
}

$tablename = $tableoptiongroup_options;
$ddl = "
CREATE TABLE $tableoptiongroup_options (
  group_id int(11) NOT NULL,
  option_id int(11) NOT NULL,
  seq int(11) NOT NULL,
  PRIMARY KEY (group_id, option_id)
)
";

if (maybe_create_table($tablename, $ddl) == true) {
    $res .= $tablename . ' - ok <br />';
} else {
    $res .= 'There was a problem with ' . $tablename . '<br />';
    ++$error_count;
}

$tablename = $tableoptionvalues;
$ddl = "
CREATE TABLE $tableoptionvalues (
  option_id int(11) NOT NULL,
  optionvalue tinytext,
  optionvalue_desc varchar(255),
  optionvalue_max int(11),
  optionvalue_min int(11),
  optionvalue_seq int(11),
  UNIQUE (option_id, optionvalue(255)),
  INDEX (option_id, optionvalue_seq)
)
";

if (maybe_create_table($tablename, $ddl) == true) {
    $res .= $tablename . ' - ok <br />';
} else {
    $res .= 'There was a problem with ' . $tablename . '<br />';
    ++$error_count;
}

?>

<p><?php echo $res ?></p>

<?php
if ($error_count > 0) {
?>
<p>Hmmm... there was some kind of error. If you cannot figure out
   see from the output above how to correct the problems please
   visit our <a href="http://wordpress.org/support/">support
   forums</a> and report your problem.</p>
<?php
} else {
?>
<p>OK, the tables got created, now to populate them with data.</p>
<?php
}

$option_data = array(
"REPLACE INTO $tableoptiontypes (optiontype_id, optiontype_name) VALUES (1, 'integer')",
"REPLACE INTO $tableoptiontypes (optiontype_id, optiontype_name) VALUES (2, 'boolean')",
"REPLACE INTO $tableoptiontypes (optiontype_id, optiontype_name) VALUES (3, 'string')",
"REPLACE INTO $tableoptiontypes (optiontype_id, optiontype_name) VALUES (4, 'date')",
"REPLACE INTO $tableoptiontypes (optiontype_id, optiontype_name) VALUES (5, 'select')",
"REPLACE INTO $tableoptiontypes (optiontype_id, optiontype_name) VALUES (6, 'range')",
"REPLACE INTO $tableoptiontypes (optiontype_id, optiontype_name) VALUES (7, 'sqlselect')",
//base options from b2cofig
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(1,'siteurl', 3, 'http://example.com', 'siteurl is your blog\'s URL: for example, \'http://example.com/wordpress\' (no trailing slash !)', 8, 30)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(2,'blogfilename', 3, 'index.php', 'blogfilename is the name of the default file for your blog', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(3,'blogname', 3, 'my weblog', 'blogname is the name of your blog', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(4,'blogdescription', 3, 'babblings!', 'blogdescription is the description of your blog', 8, 40)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(6,'search_engine_friendly_urls', 2, '0', 'Querystring Configuration ** (don\'t change if you don\'t know what you\'re doing)', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(7,'new_users_can_blog', 2, '0', 'whether you want new users to be able to post entries once they have registered', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(8,'users_can_register', 2, '1', 'whether you want to allow users to register on your blog', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(54,'admin_email', 3, 'you@example.com', 'Your email (obvious eh?)', 8, 20)",
// general blog setup
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(9 ,'start_of_week', 5, '1', 'day at the start of the week', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(10,'use_preview', 2, '1', 'Do you want to use the \'preview\' function', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(11,'use_bbcode', 2, '0', 'use BBCode, like [b]bold[/b]', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(12,'use_gmcode', 2, '0', 'use GreyMatter-styles: **bold** \\\\italic\\\\ __underline__', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(13,'use_quicktags', 2, '1', 'buttons for HTML tags (they won\'t work on IE Mac yet)', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(14,'use_htmltrans', 2, '1', 'IMPORTANT! set this to false if you are using Chinese, Japanese, Korean, or other double-bytes languages', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(15,'use_balanceTags', 2, '1', 'this could help balance your HTML code. if it gives bad results, set it to false', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(16,'use_smilies', 2, '1', 'set this to true to enable smiley conversion in posts (note: this makes smiley conversion in ALL posts)', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(17,'smilies_directory', 3, 'http://example.com/b2-img/smilies', 'the directory where your smilies are (no trailing slash)', 8, 40)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(18,'require_name_email', 2, '0', 'set this to true to require e-mail and name, or false to allow comments without e-mail/name', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(19,'comment_allowed_tags', 3, '<b><i><strong><em><code><blockquote><p><br><strike><a>', 'here is a list of the tags that are allowed in the comments. You can add tags to the list, just add them in the string, add only the opening tag: for example, only \'&lt;a>\' instead of \'&lt;a href=\"\">&lt;/a>\'', 8, 40)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(20,'comments_notify', 2, '1', 'set this to true to let every author be notified about comments on their posts', 8, 20)",
//rss/rdf feeds
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(21,'posts_per_rss', 1, '10', 'number of last posts to syndicate', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(22,'rss_language', 3, 'en', 'the language of your blog ( see this: http://backend.userland.com/stories/storyReader$16 )', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(23,'rss_encoded_html', 2, '0', 'for b2rss.php: allow encoded HTML in &lt;description> tag?', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(24,'rss_excerpt_length', 1, '50', 'length (in words) of excerpts in the RSS feed? 0=unlimited note: in b2rss.php, this will be set to 0 if you use encoded HTML', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(25,'rss_use_excerpt', 2, '1', 'use the excerpt field for rss feed.', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(26,'use_weblogsping', 2, '0', 'set this to true if you want your site to be listed on http://weblogs.com when you add a new post', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(27,'use_blodotgsping', 2, '0', 'set this to true if you want your site to be listed on http://blo.gs when you add a new post', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(28,'blodotgsping_url', 3, 'http://example.com', 'You shouldn\'t need to change this.', 8, 30)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(29,'use_trackback', 2, '1', 'set this to false or true, whether you want to allow your posts to be trackback\'able or not note: setting it to false would also disable sending trackbacks', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(30,'use_pingback', 2, '1', 'set this to false or true, whether you want to allow your posts to be pingback\'able or not note: setting it to false would also disable sending pingbacks', 8, 20)",
//file upload
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(31,'use_fileupload', 2, '0', 'set this to false to disable file upload, or true to enable it', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(32,'fileupload_realpath', 3, '/home/your/site/wordpress/images', 'enter the real path of the directory where you\'ll upload the pictures \nif you\'re unsure about what your real path is, please ask your host\'s support staff \nnote that the  directory must be writable by the webserver (chmod 766) \nnote for windows-servers users: use forwardslashes instead of backslashes', 8, 40)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(33,'fileupload_url', 3, 'http://example.com/images', 'enter the URL of that directory (it\'s used to generate the links to the uploded files)', 8, 40)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(34,'fileupload_allowedtypes', 3, ' jpg gif png ', 'accepted file types, you can add to that list if you want. note: add a space before and after each file type. example: \' jpg gif png \'', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(35,'fileupload_maxk', 1, '96', 'by default, most servers limit the size of uploads to 2048 KB, if you want to set it to a lower value, here it is (you cannot set a higher value than your server limit)', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(36,'fileupload_minlevel', 1, '1', 'you may not want all users to upload pictures/files, so you can set a minimum level for this', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(37,'fileupload_allowedusers', 3, '', '...or you may authorize only some users. enter their logins here, separated by spaces if you leave that variable blank, all users who have the minimum level are authorized to upload note: add a space before and after each login name example: \' barbara anne \'', 8, 30)",
// email settings
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(38,'mailserver_url', 3, 'mail.example.com', 'mailserver settings', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(39,'mailserver_login', 3, 'login@example.com', 'mailserver settings', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(40,'mailserver_pass', 3, 'password', 'mailserver settings', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(41,'mailserver_port', 1, '110', 'mailserver settings', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(42,'default_category', 1, '1', 'by default posts will have this category', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(43,'subjectprefix', 3, 'blog:', 'subject prefix', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(44,'bodyterminator', 3, '___', 'body terminator string (starting from this string, everything will be ignored, including this string)', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(45,'emailtestonly', 2, '0', 'set this to true to run in test mode', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(46,'use_phoneemail', 2, '0', 'some mobile phone email services will send identical subject & content on the same line if you use such a service, set use_phoneemail to true, and indicate a separator string', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(47,'phoneemail_separator', 3, ':::', 'when you compose your message, you\'ll type your subject then the separator string then you type your login:password, then the separator, then content', 8, 20)",
// original options from options page
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(48,'posts_per_page', 1, '20','How many posts/days to show on the index page.', 4, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(49,'what_to_show', 5, 'posts','Posts, days, or posts paged', 4, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(50,'archive_mode', 5, 'monthly','Which \'unit\' to use for archives.', 4, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(51,'time_difference', 6, '0', 'if you\'re not on the timezone of your server', 4, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(52,'date_format', 3, 'n/j/Y', 'see note for format characters', 4, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(53,'time_format', 3, 'g:i a', 'see note for format characters', 4, 20)",

//'pages' of options
"REPLACE INTO $tableoptiongroups (group_id,  group_name, group_desc) VALUES(1, 'Other Options', 'Posts per page etc. Original options page')",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(1,48,1 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(1,49,2 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(1,50,3 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(1,51,4 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(1,52,5 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(1,53,6 )",

"REPLACE INTO $tableoptiongroups (group_id,  group_name, group_desc) VALUES(2, 'General blog settings', 'Things you\'ll probably want to tweak')",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(2,9 ,1 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(2,10,2 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(2,11,3 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(2,12,4 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(2,13,5 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(2,14,6 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(2,15,7 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(2,16,8 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(2,17,9 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(2,18,10)",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(2,19,11)",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(2,20,12)",

"REPLACE INTO $tableoptiongroups (group_id,  group_name, group_desc) VALUES(3, 'RSS/RDF Feeds, Track/Ping-backs', 'Settings for RSS/RDF Feeds, Track/ping-backs')",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(3,21,1 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(3,22,2 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(3,23,3 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(3,24,4 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(3,25,5 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(3,26,6 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(3,27,7 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(3,28,8 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(3,29,9 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(3,30,10)",

"REPLACE INTO $tableoptiongroups (group_id,  group_name, group_desc) VALUES(4, 'File uploads', 'Settings for file uploads')",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(4,31,1 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(4,32,2 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(4,33,3 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(4,34,4 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(4,35,5 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(4,36,6 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(4,37,7 )",

"REPLACE INTO $tableoptiongroups (group_id,  group_name, group_desc) VALUES(5, 'Blog-by-Email settings', 'Settings for blogging via email')",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(5,38,1 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(5,39,2 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(5,40,3 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(5,41,4 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(5,42,5 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(5,43,6 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(5,44,7 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(5,45,8 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(5,46,9 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(5,47,10)",

"REPLACE INTO $tableoptiongroups (group_id,  group_name, group_desc) VALUES(6, 'Base settings', 'Basic settings required to get your blog working')",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(6,1,1)",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(6,2,2)",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(6,3,3)",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(6,4,4)",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(6,6,5)",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(6,7,6)",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(6,8,7)",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(6,54,8)",

// select data for what to show
"REPLACE INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (49, 'days',  'days',        null,null,1)",
"REPLACE INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (49, 'posts', 'posts',       null,null,2)",
"REPLACE INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (49, 'paged', 'posts paged', null,null,3)",
// select data for archive mode
"REPLACE INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (50, 'daily',     'daily',       null,null,1)",
"REPLACE INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (50, 'weekly',    'weekly',      null,null,2)",
"REPLACE INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (50, 'monthly',   'monthly',     null,null,3)",
"REPLACE INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (50, 'postbypost','post by post',null,null,4)",
// select data for time diff
"REPLACE INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (51, null, null, 13, -13, null)",
// select data for start of week
"REPLACE INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (9, '0', 'Sunday',   null,null,1)",
"REPLACE INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (9, '1', 'Monday',   null,null,2)",
"REPLACE INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (9, '6', 'Saturday', null,null,3)",


// Add in a new page for POST DEFAULTS

// default_post_status  select one of publish draft private
// default_comment_status select one of open closed
// default_ping_status select one of open closed
// default_pingback_flag select one of checked unchecked
// default_post_category sql_select "SELECT cat_id AS value, cat_name AS label FROM $tablecategories order by cat_name"

"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(55,'default_post_status',    5, 'publish', 'The default state of each new post', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(56,'default_comment_status', 5, 'open', 'The default state of comments for each new post', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(57,'default_ping_status',    5, 'open', 'The default ping state for each new post', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(58,'default_pingback_flag',  5, '1', 'Whether the \'PingBack the URLs in this post\' checkbox should be checked by default', 8, 20)",
"REPLACE INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(59,'default_post_category',  7, '1', 'The default category for each new post', 8, 20)",

"REPLACE INTO $tableoptiongroups (group_id,  group_name, group_desc) VALUES(7, 'Default post options', 'Default settings for new posts.')",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(7,55,1 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(7,56,2 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(7,57,3 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(7,58,4 )",
"REPLACE INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES(7,59,5 )",

// select data for post_status
"REPLACE INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (55, 'publish', 'Publish', null,null,1)",
"REPLACE INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (55, 'draft',   'Draft',   null,null,2)",
"REPLACE INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (55, 'private', 'Private', null,null,3)",

// select data for comment_status
"REPLACE INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (56, 'open', 'Open',   null,null,1)",
"REPLACE INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (56, 'closed', 'Closed', null,null,2)",

// select data for ping_status (aargh duplication!)
"REPLACE INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (57, 'open', 'Open',   null,null,1)",
"REPLACE INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (57, 'closed', 'Closed', null,null,2)",

// select data for pingback flag
"REPLACE INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (58, '1', 'Checked',   null,null,1)",
"REPLACE INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (58, '0', 'Unchecked', null,null,2)",

// sql select data for default
"REPLACE INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (59, 'SELECT cat_id AS value, cat_name AS label FROM $tablecategories order by cat_name', '', null,null,1)",
);

foreach ($option_data as $query) {
    $q = $wpdb->query($query);
}

?>

<p>Good, the option data was inserted okay.</p>
<p>Now to grab your settings from b2config</p>

<?php
    // pull in the old settings to define them globally
    if (file_exists('../b2config.php')) {
        include('../b2config.php');
    
        // now update the database with those settings
        $query = "UPDATE $tableoptions SET option_value='".addslashes($siteurl                 )."' WHERE option_id=1"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($blogfilename            )."' WHERE option_id=2"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($blogname                )."' WHERE option_id=3"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($blogdescription         )."' WHERE option_id=4"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($admin_email             )."' WHERE option_id=54"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($new_users_can_blog      )."' WHERE option_id=7"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($users_can_register      )."' WHERE option_id=8"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($start_of_week           )."' WHERE option_id=9"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($use_preview             )."' WHERE option_id=10"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($use_bbcode              )."' WHERE option_id=11"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($use_gmcode              )."' WHERE option_id=12"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($use_quicktags           )."' WHERE option_id=13"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($use_htmltrans           )."' WHERE option_id=14"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($use_balanceTags         )."' WHERE option_id=15"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($use_fileupload          )."' WHERE option_id=31"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($fileupload_realpath     )."' WHERE option_id=32"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($fileupload_url          )."' WHERE option_id=33"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($fileupload_allowedtypes )."' WHERE option_id=34"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($fileupload_maxk         )."' WHERE option_id=35"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($fileupload_minlevel     )."' WHERE option_id=36"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($fileupload_allowedusers )."' WHERE option_id=37"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($posts_per_rss           )."' WHERE option_id=21"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($rss_language            )."' WHERE option_id=22"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($rss_encoded_html        )."' WHERE option_id=23"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($rss_excerpt_length      )."' WHERE option_id=24"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($rss_use_excerpt         )."' WHERE option_id=25"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($use_weblogsping         )."' WHERE option_id=26"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($use_blodotgsping        )."' WHERE option_id=27"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($blodotgsping_url        )."' WHERE option_id=28"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($use_trackback           )."' WHERE option_id=29"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($use_pingback            )."' WHERE option_id=30"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($require_name_email      )."' WHERE option_id=18"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($comment_allowed_tags    )."' WHERE option_id=19"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($comments_notify         )."' WHERE option_id=20"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($use_smilies             )."' WHERE option_id=16"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($smilies_directory       )."' WHERE option_id=17"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($mailserver_url          )."' WHERE option_id=38"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($mailserver_login        )."' WHERE option_id=39"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($mailserver_pass         )."' WHERE option_id=40"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($mailserver_port         )."' WHERE option_id=41"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($default_category        )."' WHERE option_id=42"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($subjectprefix           )."' WHERE option_id=43"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($bodyterminator          )."' WHERE option_id=44"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($emailtestonly           )."' WHERE option_id=45"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($use_phoneemail          )."' WHERE option_id=46"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($phoneemail_separator    )."' WHERE option_id=47"; $q = $wpdb->query($query);

        // now pickup the old settings table data
        $v = $wpdb->get_var("SELECT posts_per_page from $tablesettings");
        if ($v != null) {
            $query = "UPDATE $tableoptions SET option_value='".addslashes($v)."' WHERE option_id=48";
            $q = $wpdb->query($query);
        }

        $v = $wpdb->get_var("SELECT what_to_show from $tablesettings");
        if ($v != null) {
            $query = "UPDATE $tableoptions SET option_value='".addslashes($v)."' WHERE option_id=49";
            $q = $wpdb->query($query);
        }

        $v = $wpdb->get_var("SELECT archive_mode from $tablesettings");
        if ($v != null) {
            $query = "UPDATE $tableoptions SET option_value='".addslashes($v)."' WHERE option_id=50";
            $q = $wpdb->query($query);
        }

        $v = $wpdb->get_var("SELECT time_difference from $tablesettings");
        if ($v != null) {
            $query = "UPDATE $tableoptions SET option_value='".addslashes($v)."' WHERE option_id=51";
            $q = $wpdb->query($query);
        }

        $v = $wpdb->get_var("SELECT date_format from $tablesettings");
        if ($v != null) {
            $query = "UPDATE $tableoptions SET option_value='".addslashes($v)."' WHERE option_id=52";
            $q = $wpdb->query($query);
        }

        $v = $wpdb->get_var("SELECT time_format from $tablesettings");
        if ($v != null) {
            $query = "UPDATE $tableoptions SET option_value='".addslashes($v)."' WHERE option_id=53";
            $q = $wpdb->query($query);
        }

        // ok it can go now
        $query = "DROP TABLE $tablesettings";
        $q = $wpdb->query($query);
?>
    <p>OK. All settings transferred.</p>
    <p>Congratulations! You have updated to the latest version of WordPress</p>
    <p>You can now delete your b2config.php file, and go play with your
        <a href="<?php echo $siteurl; ?>">updated blog</a> </p>
<?php
    // end if b2config exists
    } else {
?>
<p>Hmm... I couldn't find b2config.php so I couldn't transfer all your settings.
    You need to check them on the <a href="wp-options.php?option_group_id=6">admin options page</a>.</p>
<p>You can now go play with your <a href="<?php echo $siteurl ? $siteurl : '../index.php'; ?>">updated blog</a> </p>
<?php
    } // end else no b2config
	break;
}
?>
</body>
</html>