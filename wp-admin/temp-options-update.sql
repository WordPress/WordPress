--- temporary file to set up options data before update/install script is written.

-- will also need settings in wp-config.php for these tables names
-- //new option tables
-- $tableoptions             = 'options';
-- $tableoptiontypes         = 'optiontypes';
-- $tableoptionvalues        = 'optionvalues';
-- $tableoptiongroups        = 'optiongroups';
-- $tableoptiongroup_options = 'optiongroup_options';


CREATE TABLE optiontypes (
  optiontype_id int(11) NOT NULL auto_increment,
  optiontype_name varchar(64) NOT NULL,
  PRIMARY KEY (optiontype_id)
) TYPE=MyISAM;

INSERT INTO optiontypes (optiontype_id, optiontype_name) VALUES (1, 'integer');
INSERT INTO optiontypes (optiontype_id, optiontype_name) VALUES (2, 'boolean');
INSERT INTO optiontypes (optiontype_id, optiontype_name) VALUES (3, 'string');
INSERT INTO optiontypes (optiontype_id, optiontype_name) VALUES (4, 'date');
INSERT INTO optiontypes (optiontype_id, optiontype_name) VALUES (5, 'select');
INSERT INTO optiontypes (optiontype_id, optiontype_name) VALUES (6, 'range');
INSERT INTO optiontypes (optiontype_id, optiontype_name) VALUES (7, 'sqlselect');


CREATE TABLE options (
  option_id int(11) NOT NULL auto_increment,
  option_name varchar(64) UNIQUE NOT NULL default '',
  option_type int(11) NOT NULL default 1,
  option_value varchar(255) NOT NULL default '',
  option_width int NOT NULL default 20,
  option_height int NOT NULL default 8,
  option_description tinytext NOT NULL default '',
  option_admin_level int NOT NULL DEFAULT '1',
  PRIMARY KEY (option_id)
) TYPE=MyISAM;

--//base options from b2cofig
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(1,'siteurl', 3, 'http://mydomain.com', 'siteurl is your blog\'s URL: for example, \'http://mydomain.com/wordpress\' (no trailing slash !)', 8, 30);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(2,'blogfilename', 3, 'index.php', 'blogfilename is the name of the default file for your blog', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(3,'blogname', 3, 'my weblog', 'blogname is the name of your blog', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(4,'blogdescription', 3, 'babblings!', 'blogdescription is the description of your blog', 8, 40);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(6,'search_engine_friendly_urls', 2, '0', 'Querystring Configuration ** (don\'t change if you don\'t know what you\'re doing)', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(7,'new_users_can_blog', 2, '0', 'whether you want new users to be able to post entries once they have registered', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(8,'users_can_register', 2, '1', 'whether you want to allow users to register on your blog', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(54,'admin_email', 3, 'you@example.com', 'Your email (obvious eh?)', 8, 20);

--// general blog setup
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(9 ,'start_of_week', 5, '1', 'day at the start of the week', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(10,'use_preview', 2, '1', 'Do you want to use the \'preview\' function', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(11,'use_bbcode', 2, '0', 'use BBCode, like [b]bold[/b]', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(12,'use_gmcode', 2, '0', 'use GreyMatter-styles: **bold** \\\\italic\\\\ __underline__', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(13,'use_quicktags', 2, '1', 'buttons for HTML tags (they won\'t work on IE Mac yet)', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(14,'use_htmltrans', 2, '1', 'IMPORTANT! set this to false if you are using Chinese, Japanese, Korean, or other double-bytes languages', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(15,'use_balanceTags', 2, '1', 'this could help balance your HTML code. if it gives bad results, set it to false', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(16,'use_smilies', 2, '1', 'set this to 1 to enable smiley conversion in posts (note: this makes smiley conversion in ALL posts)', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(17,'smilies_directory', 3, 'http://mydomain.com/b2-img/smilies', 'the directory where your smilies are (no trailing slash)', 8, 40);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(18,'require_name_email', 2, '0', 'set this to true to require e-mail and name, or false to allow comments without e-mail/name', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(19,'comment_allowed_tags', 3, '<b><i><strong><em><code><blockquote><p><br><strike><a>', 'here is a list of the tags that are allowed in the comments. You can add tags to the list, just add them in the string, add only the opening tag: for example, only \'&lt;a>\' instead of \'&lt;a href="">&lt;/a>\'', 8, 40);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(20,'comments_notify', 2, '1', 'set this to true to let every author be notified about comments on their posts', 8, 20);

--//rss/rdf feeds
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(21,'posts_per_rss', 1, '10', 'number of last posts to syndicate', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(22,'rss_language', 3, 'en', 'the language of your blog ( see this: http://backend.userland.com/stories/storyReader$16 )', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(23,'rss_encoded_html', 2, '0', 'for b2rss.php: allow encoded HTML in &lt;description> tag?', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(24,'rss_excerpt_length', 1, '50', 'length (in words) of excerpts in the RSS feed? 0=unlimited note: in b2rss.php, this will be set to 0 if you use encoded HTML', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(25,'rss_use_excerpt', 2, '1', 'use the excerpt field for rss feed.', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(26,'use_weblogsping', 2, '0', 'set this to true if you want your site to be listed on http://weblogs.com when you add a new post', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(27,'use_blodotgsping', 2, '0', 'set this to true if you want your site to be listed on http://blo.gs when you add a new post', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(28,'blodotgsping_url', 3, 'http://mydomain.com', 'You shouldn\'t need to change this.', 8, 30);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(29,'use_trackback', 2, '1', 'set this to 0 or 1, whether you want to allow your posts to be trackback\'able or not note: setting it to zero would also disable sending trackbacks', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(30,'use_pingback', 2, '1', 'set this to 0 or 1, whether you want to allow your posts to be pingback\'able or not note: setting it to zero would also disable sending pingbacks', 8, 20);

--//file upload
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(31,'use_fileupload', 2, '0', 'set this to false to disable file upload, or true to enable it', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(32,'fileupload_realpath', 3, '/home/your/site/wordpress/images', 'enter the real path of the directory where you\'ll upload the pictures \nif you\'re unsure about what your real path is, please ask your host\'s support staff \nnote that the  directory must be writable by the webserver (chmod 766) \nnote for windows-servers users: use forwardslashes instead of backslashes', 8, 40);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(33,'fileupload_url', 3, 'http://mydomain.com/images', 'enter the URL of that directory (it\'s used to generate the links to the uploded files)', 8, 40);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(34,'fileupload_allowedtypes', 3, ' jpg gif png ', 'accepted file types, you can add to that list if you want. note: add a space before and after each file type. example: \' jpg gif png \'', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(35,'fileupload_maxk', 1, '96', 'by default, most servers limit the size of uploads to 2048 KB, if you want to set it to a lower value, here it is (you cannot set a higher value than your server limit)', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(36,'fileupload_minlevel', 1, '1', 'you may not want all users to upload pictures/files, so you can set a minimum level for this', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(37,'fileupload_allowedusers', 3, '', '...or you may authorize only some users. enter their logins here, separated by spaces if you leave that variable blank, all users who have the minimum level are authorized to upload note: add a space before and after each login name example: \' barbara anne \'', 8, 30);

-- // email settings
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(38,'mailserver_url', 3, 'mail.example.com', 'mailserver settings', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(39,'mailserver_login', 3, 'login@example.com', 'mailserver settings', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(40,'mailserver_pass', 3, 'password', 'mailserver settings', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(41,'mailserver_port', 1, '110', 'mailserver settings', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(42,'default_category', 1, '1', 'by default posts will have this category', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(43,'subjectprefix', 3, 'blog:', 'subject prefix', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(44,'bodyterminator', 3, '___', 'body terminator string (starting from this string, everything will be ignored, including this string)', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(45,'emailtestonly', 2, '0', 'set this to true to run in test mode', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(46,'use_phoneemail', 2, '0', 'some mobile phone email services will send identical subject & content on the same line if you use such a service, set use_phoneemail to true, and indicate a separator string', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(47,'phoneemail_separator', 3, ':::', 'when you compose your message, you\'ll type your subject then the separator string then you type your login:password, then the separator, then content', 8, 20);
                                                                                                                            
--// original options from options page                                                                                     
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(48,'posts_per_page', 1, '20','How many posts/days to show on the index page.', 4);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(49,'what_to_show', 5, 'posts','Posts, days, or posts paged', 4);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(50,'archive_mode', 5, 'monthly','Which \'unit\' to use for archives.', 4);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(51,'time_difference', 6, '0', 'if you\'re not on the timezone of your server', 4);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(52,'date_format', 3, 'n/j/Y', 'see note for format characters', 4);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(53,'time_format', 3, 'g:i a', 'see note for format characters', 4);


CREATE TABLE optiongroups (
  group_id int(11) NOT NULL auto_increment,
  group_name varchar(64) not null,
  group_desc varchar(255),
  group_longdesc tinytext,
  PRIMARY KEY (group_id)
) TYPE=MyISAM;


CREATE TABLE optiongroup_options (
  group_id int(11) NOT NULL,
  option_id int(11) NOT NULL,
  seq int(11) NOT NULL,
  KEY (group_id, option_id)
) TYPE=MyISAM;

INSERT INTO optiongroups (group_id,  group_name, group_desc) VALUES(1, 'Other Options', 'Posts per page etc. Original options page');
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(1,48,1 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(1,49,2 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(1,50,3 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(1,51,4 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(1,52,5 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(1,53,6 );

INSERT INTO optiongroups (group_id,  group_name, group_desc) VALUES(2, 'General blog settings', 'Things you\'ll probably want to tweak');
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(2,9 ,1 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(2,10,2 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(2,11,3 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(2,12,4 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(2,13,5 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(2,14,6 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(2,15,7 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(2,16,8 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(2,17,9 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(2,18,10);
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(2,19,11);
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(2,20,12);

INSERT INTO optiongroups (group_id,  group_name, group_desc) VALUES(3, 'RSS/RDF Feeds, Track/Ping-backs', 'Settings for RSS/RDF Feeds, Track/ping-backs');
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(3,21,1 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(3,22,2 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(3,23,3 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(3,24,4 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(3,25,5 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(3,26,6 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(3,27,7 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(3,28,8 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(3,29,9 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(3,30,10);

INSERT INTO optiongroups (group_id,  group_name, group_desc) VALUES(4, 'File uploads', 'Settings for file uploads');
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(4,31,1 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(4,32,2 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(4,33,3 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(4,34,4 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(4,35,5 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(4,36,6 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(4,37,7 );

INSERT INTO optiongroups (group_id,  group_name, group_desc) VALUES(5, 'Blog-by-Email settings', 'Settings for blogging via email');
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(5,38,1 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(5,39,2 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(5,40,3 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(5,41,4 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(5,42,5 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(5,43,6 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(5,44,7 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(5,45,8 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(5,46,9 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(5,47,10);

INSERT INTO optiongroups (group_id,  group_name, group_desc) VALUES(6, 'Base settings', 'Basic settings required to get your blog working');
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(6,1,1);
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(6,2,2);
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(6,3,3);
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(6,4,4);
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(6,6,5);
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(6,7,6);
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(6,8,7);
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(6,54,8);


CREATE TABLE optionvalues (
  option_id int(11) NOT NULL,
  optionvalue tinytext(64),
  optionvalue_desc varchar(255),
  optionvalue_max int(11),
  optionvalue_min int(11),
  optionvalue_seq int(11),
  KEY (option_id),
  INDEX (option_id, optionvalue_seq)
) TYPE=MyISAM;


-- select data for what to show
INSERT INTO optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (49, 'days',  'days',        null,null,1);
INSERT INTO optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (49, 'posts', 'posts',       null,null,2);
INSERT INTO optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (49, 'paged', 'posts paged', null,null,3);
-- select data for archive mode                                                      
INSERT INTO optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (50, 'daily',     'daily',       null,null,1);
INSERT INTO optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (50, 'weekly',    'weekly',      null,null,2);
INSERT INTO optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (50, 'monthly',   'monthly',     null,null,3);
INSERT INTO optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (50, 'postbypost','post by post',null,null,4);
-- select data for time diff                                                         
INSERT INTO optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (51, null, null, 13, 0, null);
-- select data for start of week                                                     
INSERT INTO optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (9, '0', 'Sunday',   null,null,1);
INSERT INTO optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (9, '1', 'Monday',   null,null,2);
INSERT INTO optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (9, '6', 'Saturday', null,null,3);


--// Add in a new page for POST DEFAULTS

--// default_post_status  select one of publish draft private
--// default_comment_status select one of open closed
--// default_ping_status select one of open closed
--// default_pingback_flag select one of checked unchecked
--// default_post_category sql_select "SELECT cat_id AS value, cat_name AS label FROM $tablecategories order by cat_name"

INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(55,'default_post_status',    5, 'publish', 'The default state of each new post', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(56,'default_comment_status', 5, 'open', 'The default state of comments for each new post', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(57,'default_ping_status',    5, 'open', 'The default ping state for each new post', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(58,'default_pingback_flag',  5, '1', 'Whether the \'PingBack the URLs in this post\' checkbox should be checked by default', 8, 20);
INSERT INTO options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(59,'default_post_category',  7, '1', 'The default category for each new post', 8, 20);

INSERT INTO optiongroups (group_id,  group_name, group_desc) VALUES(7, 'Default post options', 'Default settings for new posts.');
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(7,55,1 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(7,56,2 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(7,57,3 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(7,58,4 );
INSERT INTO optiongroup_options (group_id, option_id, seq) VALUES(7,59,5 );

-- select data for post_status
INSERT INTO optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (55, 'publish', 'Publish', null,null,1);
INSERT INTO optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (55, 'draft',   'Draft',   null,null,2);
INSERT INTO optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (55, 'private', 'Private', null,null,3);

-- select data for comment_status
INSERT INTO optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (56, 'open', 'Open',   null,null,1);
INSERT INTO optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (56, 'closed', 'Closed', null,null,2);

-- select data for ping_status (aargh duplication!)
INSERT INTO optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (57, 'open', 'Open',   null,null,1);
INSERT INTO optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (57, 'closed', 'Closed', null,null,2);

-- select data for pingback flag
INSERT INTO optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (58, '1', 'Checked',   null,null,1);
INSERT INTO optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (58, '0', 'Unchecked', null,null,2);

-- sql select data for default 
INSERT INTO optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (59, 'SELECT cat_id AS value, cat_name AS label FROM $tablecategories order by cat_name', '', null,null,1);



-----------
-- upgrade to delete tablesettings after granbbing values.