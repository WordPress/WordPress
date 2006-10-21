<?php

define('XMLRPC_REQUEST', true);

// Some browser-embedded clients send cookies. We don't want them.
$_COOKIE = array();

# fix for mozBlog and other cases where '<?xml' isn't on the very first line
if ( isset($HTTP_RAW_POST_DATA) )
	$HTTP_RAW_POST_DATA = trim($HTTP_RAW_POST_DATA);

include('./wp-config.php');

if ( isset( $_GET['rsd'] ) ) { // http://archipelago.phrasewise.com/rsd 
header('Content-type: text/xml; charset=' . get_settings('blog_charset'), true);

?>
<?php echo '<?xml version="1.0" encoding="'.get_settings('blog_charset').'"?'.'>'; ?>
<rsd version="1.0" xmlns="http://archipelago.phrasewise.com/rsd">
  <service>
    <engineName>WordPress</engineName>
    <engineLink>http://wordpress.org/</engineLink>
    <homePageLink><?php bloginfo_rss('url') ?></homePageLink>
    <apis>
      <api name="Movable Type" blogID="1" preferred="true" apiLink="<?php bloginfo_rss('url') ?>/xmlrpc.php" />
      <api name="MetaWeblog" blogID="1" preferred="false" apiLink="<?php bloginfo_rss('url') ?>/xmlrpc.php" />
      <api name="Blogger" blogID="1" preferred="false" apiLink="<?php bloginfo_rss('url') ?>/xmlrpc.php" />
    </apis>
  </service>
</rsd>
<?php
exit;
}

include_once(ABSPATH . WPINC . '/class-IXR.php');

// Turn off all warnings and errors.
// error_reporting(0);

$post_default_title = ""; // posts submitted via the xmlrpc interface get that title

$xmlrpc_logging = 0;

function logIO($io,$msg) {
	global $xmlrpc_logging;
	if ($xmlrpc_logging) {
		$fp = fopen("../xmlrpc.log","a+");
		$date = gmdate("Y-m-d H:i:s ");
		$iot = ($io == "I") ? " Input: " : " Output: ";
		fwrite($fp, "\n\n".$date.$iot.$msg);
		fclose($fp);
	}
	return true;
	}

function starify($string) {
	$i = strlen($string);
	return str_repeat('*', $i);
}

if ( isset($HTTP_RAW_POST_DATA) )
  logIO("I", $HTTP_RAW_POST_DATA);


class wp_xmlrpc_server extends IXR_Server {

	function wp_xmlrpc_server() {
		$this->methods = array(
		  // Blogger API
		  'blogger.getUsersBlogs' => 'this:blogger_getUsersBlogs',
		  'blogger.getUserInfo' => 'this:blogger_getUserInfo',
		  'blogger.getPost' => 'this:blogger_getPost',
		  'blogger.getRecentPosts' => 'this:blogger_getRecentPosts',
		  'blogger.getTemplate' => 'this:blogger_getTemplate',
		  'blogger.setTemplate' => 'this:blogger_setTemplate',
		  'blogger.newPost' => 'this:blogger_newPost',
		  'blogger.editPost' => 'this:blogger_editPost',
		  'blogger.deletePost' => 'this:blogger_deletePost',

		  // MetaWeblog API (with MT extensions to structs)
		  'metaWeblog.newPost' => 'this:mw_newPost',
		  'metaWeblog.editPost' => 'this:mw_editPost',
		  'metaWeblog.getPost' => 'this:mw_getPost',
		  'metaWeblog.getRecentPosts' => 'this:mw_getRecentPosts',
		  'metaWeblog.getCategories' => 'this:mw_getCategories',
		  'metaWeblog.newMediaObject' => 'this:mw_newMediaObject',

		  // MetaWeblog API aliases for Blogger API
		  // see http://www.xmlrpc.com/stories/storyReader$2460
		  'metaWeblog.deletePost' => 'this:blogger_deletePost',
		  'metaWeblog.getTemplate' => 'this:blogger_getTemplate',
		  'metaWeblog.setTemplate' => 'this:blogger_setTemplate',
		  'metaWeblog.getUsersBlogs' => 'this:blogger_getUsersBlogs',

		  // MovableType API
		  'mt.getCategoryList' => 'this:mt_getCategoryList',
		  'mt.getRecentPostTitles' => 'this:mt_getRecentPostTitles',
		  'mt.getPostCategories' => 'this:mt_getPostCategories',
		  'mt.setPostCategories' => 'this:mt_setPostCategories',
		  'mt.supportedMethods' => 'this:mt_supportedMethods',
		  'mt.supportedTextFilters' => 'this:mt_supportedTextFilters',
		  'mt.getTrackbackPings' => 'this:mt_getTrackbackPings',
		  'mt.publishPost' => 'this:mt_publishPost',

		  // PingBack
		  'pingback.ping' => 'this:pingback_ping',
		  'pingback.extensions.getPingbacks' => 'this:pingback_extensions_getPingbacks',

		  'demo.sayHello' => 'this:sayHello',
		  'demo.addTwoNumbers' => 'this:addTwoNumbers'
		);
		$this->methods = apply_filters('xmlrpc_methods', $this->methods);
		$this->IXR_Server($this->methods);
	}

	function sayHello($args) {
		return 'Hello!';
	}

	function addTwoNumbers($args) {
		$number1 = $args[0];
		$number2 = $args[1];
		return $number1 + $number2;
	}

	function login_pass_ok($user_login, $user_pass) {
	  if (!user_pass_ok($user_login, $user_pass)) {
	    $this->error = new IXR_Error(403, 'Bad login/pass combination.');
	    return false;
	  }
	  return true;
	}

	function escape(&$array) {
		global $wpdb;

		foreach ( (array) $array as $k => $v ) {
			if (is_array($v)) {
				$this->escape($array[$k]);
			} else if (is_object($v)) {
				//skip
			} else {
				$array[$k] = $wpdb->escape($v);
			}
		}
	}

	/* Blogger API functions
	 * specs on http://plant.blogger.com/api and http://groups.yahoo.com/group/bloggerDev/
	 */


	/* blogger.getUsersBlogs will make more sense once we support multiple blogs */
	function blogger_getUsersBlogs($args) {

		$this->escape($args);

	  $user_login = $args[1];
	  $user_pass  = $args[2];

	  if (!$this->login_pass_ok($user_login, $user_pass)) {
	    return $this->error;
	  }

	  set_current_user(0, $user_login);
	  $is_admin = current_user_can('level_8');

	  $struct = array(
	    'isAdmin'  => $is_admin,
	    'url'      => get_settings('home') . '/',
	    'blogid'   => '1',
	    'blogName' => get_settings('blogname')
	  );

	  return array($struct);
	}


	/* blogger.getUsersInfo gives your client some info about you, so you don't have to */
	function blogger_getUserInfo($args) {

		$this->escape($args);

	  $user_login = $args[1];
	  $user_pass  = $args[2];

	  if (!$this->login_pass_ok($user_login, $user_pass)) {
	    return $this->error;
	  }

	  $user_data = get_userdatabylogin($user_login);

	  $struct = array(
	    'nickname'  => $user_data->nickname,
	    'userid'    => $user_data->ID,
	    'url'       => $user_data->user_url,
	    'email'     => $user_data->user_email,
	    'lastname'  => $user_data->last_name,
	    'firstname' => $user_data->first_name
	  );

	  return $struct;
	}


	/* blogger.getPost ...gets a post */
	function blogger_getPost($args) {

		$this->escape($args);

	  $post_ID    = $args[1];
	  $user_login = $args[2];
	  $user_pass  = $args[3];

	  if (!$this->login_pass_ok($user_login, $user_pass)) {
	    return $this->error;
	  }

	  $user_data = get_userdatabylogin($user_login);
	  $post_data = wp_get_single_post($post_ID, ARRAY_A);

	  $categories = implode(',', wp_get_post_cats(1, $post_ID));

	  $content  = '<title>'.stripslashes($post_data['post_title']).'</title>';
	  $content .= '<category>'.$categories.'</category>';
	  $content .= stripslashes($post_data['post_content']);

	  $struct = array(
	    'userid'    => $post_data['post_author'],
	    'dateCreated' => new IXR_Date(mysql2date('Ymd\TH:i:s', $post_data['post_date'])),
	    'content'     => $content,
	    'postid'  => $post_data['ID']
	  );

	  return $struct;
	}


	/* blogger.getRecentPosts ...gets recent posts */
	function blogger_getRecentPosts($args) {

	  global $wpdb;

		$this->escape($args);

	  $blog_ID    = $args[1]; /* though we don't use it yet */
	  $user_login = $args[2];
	  $user_pass  = $args[3];
	  $num_posts  = $args[4];

	  if (!$this->login_pass_ok($user_login, $user_pass)) {
	    return $this->error;
	  }

	  $posts_list = wp_get_recent_posts($num_posts);

	  if (!$posts_list) {
	    $this->error = new IXR_Error(500, 'Either there are no posts, or something went wrong.');
	    return $this->error;
	  }

	  foreach ($posts_list as $entry) {
	  
	    $post_date = mysql2date('Ymd\TH:i:s', $entry['post_date']);
	    $categories = implode(',', wp_get_post_cats(1, $entry['ID']));

	    $content  = '<title>'.stripslashes($entry['post_title']).'</title>';
	    $content .= '<category>'.$categories.'</category>';
	    $content .= stripslashes($entry['post_content']);

	    $struct[] = array(
	      'userid' => $entry['post_author'],
	      'dateCreated' => new IXR_Date($post_date),
	      'content' => $content,
	      'postid' => $entry['ID'],
	    );

	  }

	  $recent_posts = array();
	  for ($j=0; $j<count($struct); $j++) {
	    array_push($recent_posts, $struct[$j]);
	  }

	  return $recent_posts;
	}


	/* blogger.getTemplate returns your blog_filename */
	function blogger_getTemplate($args) {

		$this->escape($args);

	  $blog_ID    = $args[1];
	  $user_login = $args[2];
	  $user_pass  = $args[3];
	  $template   = $args[4]; /* could be 'main' or 'archiveIndex', but we don't use it */

	  if (!$this->login_pass_ok($user_login, $user_pass)) {
	    return $this->error;
	  }

	  set_current_user(0, $user_login);
	  if ( !current_user_can('edit_themes') ) {
	    return new IXR_Error(401, 'Sorry, this user can not edit the template.');
	  }

	  /* warning: here we make the assumption that the weblog's URI is on the same server */
	  $filename = get_settings('home') . '/';
	  $filename = preg_replace('#https?://.+?/#', $_SERVER['DOCUMENT_ROOT'].'/', $filename);

	  $f = fopen($filename, 'r');
	  $content = fread($f, filesize($filename));
	  fclose($f);

	  /* so it is actually editable with a windows/mac client */
	  // FIXME: (or delete me) do we really want to cater to bad clients at the expense of good ones by BEEPing up their line breaks? commented.     $content = str_replace("\n", "\r\n", $content); 

	  return $content;
	}


	/* blogger.setTemplate updates the content of blog_filename */
	function blogger_setTemplate($args) {

		$this->escape($args);

	  $blog_ID    = $args[1];
	  $user_login = $args[2];
	  $user_pass  = $args[3];
	  $content    = $args[4];
	  $template   = $args[5]; /* could be 'main' or 'archiveIndex', but we don't use it */

	  if (!$this->login_pass_ok($user_login, $user_pass)) {
	    return $this->error;
	  }

	  set_current_user(0, $user_login);
	  if ( !current_user_can('edit_themes') ) {
	    return new IXR_Error(401, 'Sorry, this user can not edit the template.');
	  }

	  /* warning: here we make the assumption that the weblog's URI is on the same server */
	  $filename = get_settings('home') . '/';
	  $filename = preg_replace('#https?://.+?/#', $_SERVER['DOCUMENT_ROOT'].'/', $filename);

	  if ($f = fopen($filename, 'w+')) {
	    fwrite($f, $content);
	    fclose($f);
	  } else {
	    return new IXR_Error(500, 'Either the file is not writable, or something wrong happened. The file has not been updated.');
	  }

	  return true;
	}


	/* blogger.newPost ...creates a new post */
	function blogger_newPost($args) {

	  global $wpdb;

		$this->escape($args);

	  $blog_ID    = $args[1]; /* though we don't use it yet */
	  $user_login = $args[2];
	  $user_pass  = $args[3];
	  $content    = $args[4];
	  $publish    = $args[5];

	  if (!$this->login_pass_ok($user_login, $user_pass)) {
	    return $this->error;
	  }
	  
	  $cap = ($publish) ? 'publish_posts' : 'edit_posts';
	  $user = set_current_user(0, $user_login);
	  if ( !current_user_can($cap) )
	    return new IXR_Error(401, 'Sorry, you can not post on this weblog or category.');

	  $post_status = ($publish) ? 'publish' : 'draft';

	  $post_author = $user->ID;

	  $post_title = xmlrpc_getposttitle($content);
	  $post_category = xmlrpc_getpostcategory($content);
	  $post_content = xmlrpc_removepostdata($content);

	  $post_date = current_time('mysql');
	  $post_date_gmt = current_time('mysql', 1);

	  $post_data = compact('blog_ID', 'post_author', 'post_date', 'post_date_gmt', 'post_content', 'post_title', 'post_category', 'post_status');

	  $post_ID = wp_insert_post($post_data);

	  if (!$post_ID) {
	    return new IXR_Error(500, 'Sorry, your entry could not be posted. Something wrong happened.');
	  }

	  logIO('O', "Posted ! ID: $post_ID");

	  return $post_ID;
	}


	/* blogger.editPost ...edits a post */
	function blogger_editPost($args) {

	  global $wpdb;

		$this->escape($args);

	  $post_ID     = $args[1];
	  $user_login  = $args[2];
	  $user_pass   = $args[3];
	  $content     = $args[4];
	  $publish     = $args[5];

	  if (!$this->login_pass_ok($user_login, $user_pass)) {
	    return $this->error;
	  }

	  $actual_post = wp_get_single_post($post_ID,ARRAY_A);

	  if (!$actual_post) {
	  	return new IXR_Error(404, 'Sorry, no such post.');
	  }

		$this->escape($actual_post);

	  set_current_user(0, $user_login);
	  if ( !current_user_can('edit_post', $post_ID) )
	    return new IXR_Error(401, 'Sorry, you do not have the right to edit this post.');

	  extract($actual_post);

	  $post_title = xmlrpc_getposttitle($content);
	  $post_category = xmlrpc_getpostcategory($content);
	  $post_content = xmlrpc_removepostdata($content);

	  $postdata = compact('ID', 'post_content', 'post_title', 'post_category', 'post_status', 'post_excerpt');

	  $result = wp_update_post($postdata);

	  if (!$result) {
	  	return new IXR_Error(500, 'For some strange yet very annoying reason, this post could not be edited.');
	  }

	  return true;
	}


	/* blogger.deletePost ...deletes a post */
	function blogger_deletePost($args) {

	  global $wpdb;

		$this->escape($args);

	  $post_ID     = $args[1];
	  $user_login  = $args[2];
	  $user_pass   = $args[3];
	  $publish     = $args[4];

	  if (!$this->login_pass_ok($user_login, $user_pass)) {
	    return $this->error;
	  }

	  $actual_post = wp_get_single_post($post_ID,ARRAY_A);

	  if (!$actual_post) {
	  	return new IXR_Error(404, 'Sorry, no such post.');
	  }

	  set_current_user(0, $user_login);
	  if ( !current_user_can('edit_post', $post_ID) )
	    return new IXR_Error(401, 'Sorry, you do not have the right to delete this post.');

	  $result = wp_delete_post($post_ID);

	  if (!$result) {
	  	return new IXR_Error(500, 'For some strange yet very annoying reason, this post could not be deleted.');
	  }

	  return true;
	}



	/* MetaWeblog API functions
	 * specs on wherever Dave Winer wants them to be
	 */

	/* metaweblog.newPost creates a post */
	function mw_newPost($args) {

	  global $wpdb, $post_default_category;

		$this->escape($args);

	  $blog_ID     = $args[0]; // we will support this in the near future
	  $user_login  = $args[1];
	  $user_pass   = $args[2];
	  $content_struct = $args[3];
	  $publish     = $args[4];

	  if (!$this->login_pass_ok($user_login, $user_pass)) {
	    return $this->error;
	  }

	  $user = set_current_user(0, $user_login);
	  if ( !current_user_can('publish_posts') )
	    return new IXR_Error(401, 'Sorry, you can not post on this weblog or category.');

	  $post_author = $user->ID;

	  $post_title = $content_struct['title'];
	  $post_content = apply_filters( 'content_save_pre', $content_struct['description'] );
	  $post_status = $publish ? 'publish' : 'draft';

	  $post_excerpt = $content_struct['mt_excerpt'];
	  $post_more = $content_struct['mt_text_more'];

	  $comment_status = (empty($content_struct['mt_allow_comments'])) ?
	    get_settings('default_comment_status')
	    : $content_struct['mt_allow_comments'];

	  $ping_status = (empty($content_struct['mt_allow_pings'])) ?
	    get_settings('default_ping_status')
	    : $content_struct['mt_allow_pings'];

	  if ($post_more) {
	    $post_content = $post_content . "\n<!--more-->\n" . $post_more;
	  }

		$to_ping = $content_struct['mt_tb_ping_urls'];

	  // Do some timestamp voodoo
	  $dateCreatedd = $content_struct['dateCreated'];
	  if (!empty($dateCreatedd)) {
	    $dateCreated = $dateCreatedd->getIso();
	    $post_date     = get_date_from_gmt(iso8601_to_datetime($dateCreated));
	    $post_date_gmt = iso8601_to_datetime($dateCreated, GMT);
	  } else {
	    $post_date     = current_time('mysql');
	    $post_date_gmt = current_time('mysql', 1);
	  }

	  $catnames = $content_struct['categories'];
	  logIO('O', 'Post cats: ' . printr($catnames,true));
	  $post_category = array();

	  if (is_array($catnames)) {
	    foreach ($catnames as $cat) {
	      $post_category[] = get_cat_ID($cat);
	    }
	  }
		
	  // We've got all the data -- post it:
	  $postdata = compact('post_author', 'post_date', 'post_date_gmt', 'post_content', 'post_title', 'post_category', 'post_status', 'post_excerpt', 'comment_status', 'ping_status', 'to_ping');

	  $post_ID = wp_insert_post($postdata);

	  if (!$post_ID) {
	    return new IXR_Error(500, 'Sorry, your entry could not be posted. Something wrong happened.');
	  }

	  logIO('O', "Posted ! ID: $post_ID");

	  return strval($post_ID);
	}


	/* metaweblog.editPost ...edits a post */
	function mw_editPost($args) {

	  global $wpdb, $post_default_category;

		$this->escape($args);

	  $post_ID     = $args[0];
	  $user_login  = $args[1];
	  $user_pass   = $args[2];
	  $content_struct = $args[3];
	  $publish     = $args[4];

	  if (!$this->login_pass_ok($user_login, $user_pass)) {
	    return $this->error;
	  }

	  set_current_user(0, $user_login);
	  if ( !current_user_can('edit_post', $post_ID) )
	    return new IXR_Error(401, 'Sorry, you can not edit this post.');

	  $postdata = wp_get_single_post($post_ID, ARRAY_A);
	  extract($postdata);
		$this->escape($postdata);

	  $post_title = $content_struct['title'];
	  $post_content = apply_filters( 'content_save_pre', $content_struct['description'] );
	  $catnames = $content_struct['categories'];

	  $post_category = array();
		
	  if (is_array($catnames)) {
	    foreach ($catnames as $cat) {
	      $post_category[] = get_cat_ID($cat);
	    }
	  }

	  $post_excerpt = $content_struct['mt_excerpt'];
	  $post_more = $content_struct['mt_text_more'];
	  $post_status = $publish ? 'publish' : 'draft';

	  if ($post_more) {
	    $post_content = $post_content . "\n<!--more-->\n" . $post_more;
	  }

		$to_ping = $content_struct['mt_tb_ping_urls'];

	  $comment_status = (empty($content_struct['mt_allow_comments'])) ?
	    get_settings('default_comment_status')
	    : $content_struct['mt_allow_comments'];

	  $ping_status = (empty($content_struct['mt_allow_pings'])) ?
	    get_settings('default_ping_status')
	    : $content_struct['mt_allow_pings'];

	  // Do some timestamp voodoo
	  $dateCreatedd = $content_struct['dateCreated'];
	  if (!empty($dateCreatedd)) {
	    $dateCreated = $dateCreatedd->getIso();
	    $post_date     = get_date_from_gmt(iso8601_to_datetime($dateCreated));
	    $post_date_gmt = iso8601_to_datetime($dateCreated, GMT);
	  } else {
	    $post_date     = $postdata['post_date'];
	    $post_date_gmt = $postdata['post_date_gmt'];
	  }

	  // We've got all the data -- post it:
	  $newpost = compact('ID', 'post_content', 'post_title', 'post_category', 'post_status', 'post_excerpt', 'comment_status', 'ping_status', 'post_date', 'post_date_gmt', 'to_ping');

	  $result = wp_update_post($newpost);
	  if (!$result) {
	    return new IXR_Error(500, 'Sorry, your entry could not be edited. Something wrong happened.');
	  }

	  logIO('O',"(MW) Edited ! ID: $post_ID");

	  return true;
	}


	/* metaweblog.getPost ...returns a post */
	function mw_getPost($args) {

	  global $wpdb;

		$this->escape($args);

	  $post_ID     = $args[0];
	  $user_login  = $args[1];
	  $user_pass   = $args[2];

	  if (!$this->login_pass_ok($user_login, $user_pass)) {
	    return $this->error;
	  }

	  $postdata = wp_get_single_post($post_ID, ARRAY_A);

	  if ($postdata['post_date'] != '') {

	    $post_date = mysql2date('Ymd\TH:i:s', $postdata['post_date']);

	    $categories = array();
	    $catids = wp_get_post_cats('', $post_ID);
	    foreach($catids as $catid) {
	      $categories[] = get_cat_name($catid);
	    }

	    $post = get_extended($postdata['post_content']);
	    $link = post_permalink($postdata['ID']);

	    $allow_comments = ('open' == $postdata['comment_status']) ? 1 : 0;
	    $allow_pings = ('open' == $postdata['ping_status']) ? 1 : 0;

	    $resp = array(
	      'dateCreated' => new IXR_Date($post_date),
	      'userid' => $postdata['post_author'],
	      'postid' => $postdata['ID'],
	      'description' => $post['main'],
	      'title' => $postdata['post_title'],
	      'link' => $link,
	      'permaLink' => $link,
// commented out because no other tool seems to use this
//	      'content' => $entry['post_content'],
	      'categories' => $categories,
	      'mt_excerpt' => $postdata['post_excerpt'],
	      'mt_text_more' => $post['extended'],
	      'mt_allow_comments' => $allow_comments,
	      'mt_allow_pings' => $allow_pings
	    );

	    return $resp;
	  } else {
	  	return new IXR_Error(404, 'Sorry, no such post.');
	  }
	}


	/* metaweblog.getRecentPosts ...returns recent posts */
	function mw_getRecentPosts($args) {

		$this->escape($args);

	  $blog_ID     = $args[0];
	  $user_login  = $args[1];
	  $user_pass   = $args[2];
	  $num_posts   = $args[3];

	  if (!$this->login_pass_ok($user_login, $user_pass)) {
	    return $this->error;
	  }

	  $posts_list = wp_get_recent_posts($num_posts);

	  if (!$posts_list) {
	    $this->error = new IXR_Error(500, 'Either there are no posts, or something went wrong.');
	    return $this->error;
	  }

	  foreach ($posts_list as $entry) {
	  
	    $post_date = mysql2date('Ymd\TH:i:s', $entry['post_date']);
	    $categories = array();
	    $catids = wp_get_post_cats('', $entry['ID']);
	    foreach($catids as $catid) {
	      $categories[] = get_cat_name($catid);
	    }

	    $post = get_extended($entry['post_content']);
	    $link = post_permalink($entry['ID']);

	    $allow_comments = ('open' == $entry['comment_status']) ? 1 : 0;
	    $allow_pings = ('open' == $entry['ping_status']) ? 1 : 0;

	    $struct[] = array(
	      'dateCreated' => new IXR_Date($post_date),
	      'userid' => $entry['post_author'],
	      'postid' => $entry['ID'],
	      'description' => $post['main'],
	      'title' => $entry['post_title'],
	      'link' => $link,
	      'permaLink' => $link,
// commented out because no other tool seems to use this
//	      'content' => $entry['post_content'],
	      'categories' => $categories,
	      'mt_excerpt' => $entry['post_excerpt'],
	      'mt_text_more' => $post['extended'],
	      'mt_allow_comments' => $allow_comments,
	      'mt_allow_pings' => $allow_pings
	    );

	  }

	  $recent_posts = array();
	  for ($j=0; $j<count($struct); $j++) {
	    array_push($recent_posts, $struct[$j]);
	  }
	  
	  return $recent_posts;
	}


	/* metaweblog.getCategories ...returns the list of categories on a given weblog */
	function mw_getCategories($args) {

	  global $wpdb;

		$this->escape($args);

	  $blog_ID     = $args[0];
	  $user_login  = $args[1];
	  $user_pass   = $args[2];

	  if (!$this->login_pass_ok($user_login, $user_pass)) {
	    return $this->error;
	  }

	  $categories_struct = array();

	  // FIXME: can we avoid using direct SQL there?
	  if ($cats = $wpdb->get_results("SELECT cat_ID,cat_name FROM $wpdb->categories", ARRAY_A)) {
	    foreach ($cats as $cat) {
	      $struct['categoryId'] = $cat['cat_ID'];
	      $struct['description'] = $cat['cat_name'];
	      $struct['categoryName'] = $cat['cat_name'];
	      $struct['htmlUrl'] = wp_specialchars(get_category_link($cat['cat_ID']));
	      $struct['rssUrl'] = wp_specialchars(get_category_rss_link(false, $cat['cat_ID'], $cat['cat_name']));

	      $categories_struct[] = $struct;
	    }
	  }

	  return $categories_struct;
	}


	/* metaweblog.newMediaObject uploads a file, following your settings */
	function mw_newMediaObject($args) {
		// adapted from a patch by Johann Richard
		// http://mycvs.org/archives/2004/06/30/file-upload-to-wordpress-in-ecto/

		global $wpdb;

		$blog_ID     = $wpdb->escape($args[0]);
		$user_login  = $wpdb->escape($args[1]);
		$user_pass   = $wpdb->escape($args[2]);
		$data        = $args[3];

		$name = $data['name'];
		$type = $data['type'];
		$bits = $data['bits'];

		logIO('O', '(MW) Received '.strlen($bits).' bytes');

		if ( !$this->login_pass_ok($user_login, $user_pass) )
			return $this->error;

		set_current_user(0, $user_login);
		if ( !current_user_can('upload_files') ) {
			logIO('O', '(MW) User does not have upload_files capability');
			$this->error = new IXR_Error(401, 'You are not allowed to upload files to this site.');
			return $this->error;
		}

		$upload = wp_upload_bits($name, $type, $bits);
		if ( ! empty($upload['error']) ) {
			logIO('O', '(MW) Could not write file '.$name);
			return new IXR_Error(500, 'Could not write file '.$name);
		}
		
		return array('url' => $upload['url']);
	}


	/* MovableType API functions
	 * specs on http://www.movabletype.org/docs/mtmanual_programmatic.html
	 */

	/* mt.getRecentPostTitles ...returns recent posts' titles */
	function mt_getRecentPostTitles($args) {

		$this->escape($args);

	  $blog_ID     = $args[0];
	  $user_login  = $args[1];
	  $user_pass   = $args[2];
	  $num_posts   = $args[3];

	  if (!$this->login_pass_ok($user_login, $user_pass)) {
	    return $this->error;
	  }

	  $posts_list = wp_get_recent_posts($num_posts);

	  if (!$posts_list) {
	    $this->error = new IXR_Error(500, 'Either there are no posts, or something went wrong.');
	    return $this->error;
	  }

	  foreach ($posts_list as $entry) {
	  
	    $post_date = mysql2date('Ymd\TH:i:s', $entry['post_date']);

	    $struct[] = array(
	      'dateCreated' => new IXR_Date($post_date),
	      'userid' => $entry['post_author'],
	      'postid' => $entry['ID'],
	      'title' => $entry['post_title'],
	    );

	  }

	  $recent_posts = array();
	  for ($j=0; $j<count($struct); $j++) {
	    array_push($recent_posts, $struct[$j]);
	  }
	  
	  return $recent_posts;
	}


	/* mt.getCategoryList ...returns the list of categories on a given weblog */
	function mt_getCategoryList($args) {

	  global $wpdb;

		$this->escape($args);

	  $blog_ID     = $args[0];
	  $user_login  = $args[1];
	  $user_pass   = $args[2];

	  if (!$this->login_pass_ok($user_login, $user_pass)) {
	    return $this->error;
	  }

	  $categories_struct = array();

	  // FIXME: can we avoid using direct SQL there?
	  if ($cats = $wpdb->get_results("SELECT cat_ID, cat_name FROM $wpdb->categories", ARRAY_A)) {
	    foreach ($cats as $cat) {
	      $struct['categoryId'] = $cat['cat_ID'];
	      $struct['categoryName'] = $cat['cat_name'];

	      $categories_struct[] = $struct;
	    }
	  }

	  return $categories_struct;
	}


	/* mt.getPostCategories ...returns a post's categories */
	function mt_getPostCategories($args) {

		$this->escape($args);

	  $post_ID     = $args[0];
	  $user_login  = $args[1];
	  $user_pass   = $args[2];

	  if (!$this->login_pass_ok($user_login, $user_pass)) {
	    return $this->error;
	  }

	  $categories = array();
	  $catids = wp_get_post_cats('', intval($post_ID));
	  // first listed category will be the primary category
	  $isPrimary = true;
	  foreach($catids as $catid) {
	    $categories[] = array(
	      'categoryName' => get_cat_name($catid),
	      'categoryId' => $catid,
	      'isPrimary' => $isPrimary
	    );
	    $isPrimary = false;
	  }
 
	  return $categories;
	}


	/* mt.setPostCategories ...sets a post's categories */
	function mt_setPostCategories($args) {

		$this->escape($args);

	  $post_ID     = $args[0];
	  $user_login  = $args[1];
	  $user_pass   = $args[2];
	  $categories  = $args[3];

	  if (!$this->login_pass_ok($user_login, $user_pass)) {
	    return $this->error;
	  }

	  set_current_user(0, $user_login);
	  if ( !current_user_can('edit_post', $post_ID) )
	    return new IXR_Error(401, 'Sorry, you can not edit this post.');

	  foreach($categories as $cat) {
	    $catids[] = $cat['categoryId'];
	  }
	
	  wp_set_post_cats('', $post_ID, $catids);

	  return true;
	}


	/* mt.supportedMethods ...returns an array of methods supported by this server */
	function mt_supportedMethods($args) {

	  $supported_methods = array();
	  foreach($this->methods as $key=>$value) {
	    $supported_methods[] = $key;
	  }

	  return $supported_methods;
	}


	/* mt.supportedTextFilters ...returns an empty array because we don't
	   support per-post text filters yet */
	function mt_supportedTextFilters($args) {
	  return array();
	}


	/* mt.getTrackbackPings ...returns trackbacks sent to a given post */
	function mt_getTrackbackPings($args) {

	  global $wpdb;

	  $post_ID = intval($args);

	  $actual_post = wp_get_single_post($post_ID, ARRAY_A);

	  if (!$actual_post) {
	  	return new IXR_Error(404, 'Sorry, no such post.');
	  }

	  $comments = $wpdb->get_results("SELECT comment_author_url, comment_content, comment_author_IP, comment_type FROM $wpdb->comments WHERE comment_post_ID = $post_ID");

	  if (!$comments) {
	  	return array();
	  }

	  $trackback_pings = array();
	  foreach($comments as $comment) {
	    if ( 'trackback' == $comment->comment_type ) {
	      $content = $comment->comment_content;
	      $title = substr($content, 8, (strpos($content, '</strong>') - 8));
	      $trackback_pings[] = array(
	        'pingTitle' => $title,
	        'pingURL'   => $comment->comment_author_url,
	        'pingIP'    => $comment->comment_author_IP
	      );
		}
	  }

	  return $trackback_pings;
	}


	/* mt.publishPost ...sets a post's publish status to 'publish' */
	function mt_publishPost($args) {

		$this->escape($args);

	  $post_ID     = $args[0];
	  $user_login  = $args[1];
	  $user_pass   = $args[2];

	  if (!$this->login_pass_ok($user_login, $user_pass)) {
	    return $this->error;
	  }

	  set_current_user(0, $user_login);
	  if ( !current_user_can('edit_post', $post_ID) )
	    return new IXR_Error(401, 'Sorry, you can not edit this post.');

	  $postdata = wp_get_single_post($post_ID,ARRAY_A);

	  $postdata['post_status'] = 'publish';

	  // retain old cats
	  $cats = wp_get_post_cats('',$post_ID);
	  $postdata['post_category'] = $cats;
		$this->escape($postdata);

	  $result = wp_update_post($postdata);

	  return $result;
	}



	/* PingBack functions
	 * specs on www.hixie.ch/specs/pingback/pingback
	 */

	/* pingback.ping gets a pingback and registers it */
	function pingback_ping($args) {
		global $wpdb, $wp_version; 

		$this->escape($args);

		$pagelinkedfrom = $args[0];
		$pagelinkedto   = $args[1];

		$title = '';

		$pagelinkedfrom = str_replace('&amp;', '&', $pagelinkedfrom);
		$pagelinkedto   = preg_replace('#&([^amp\;])#is', '&amp;$1', $pagelinkedto);

		$error_code = -1;

		// Check if the page linked to is in our site
		$pos1 = strpos($pagelinkedto, str_replace(array('http://www.','http://','https://www.','https://'), '', get_settings('home')));
		if( !$pos1 )
	  		return new IXR_Error(0, 'Is there no link to us?');

		// let's find which post is linked to
		// FIXME: does url_to_postid() cover all these cases already?
		//        if so, then let's use it and drop the old code.
		$urltest = parse_url($pagelinkedto);
		if ($post_ID = url_to_postid($pagelinkedto)) {
			$way = 'url_to_postid()';
		} elseif (preg_match('#p/[0-9]{1,}#', $urltest['path'], $match)) {
			// the path defines the post_ID (archives/p/XXXX)
			$blah = explode('/', $match[0]);
			$post_ID = $blah[1];
			$way = 'from the path';
		} elseif (preg_match('#p=[0-9]{1,}#', $urltest['query'], $match)) {
			// the querystring defines the post_ID (?p=XXXX)
			$blah = explode('=', $match[0]);
			$post_ID = $blah[1];
			$way = 'from the querystring';
		} elseif (isset($urltest['fragment'])) {
			// an #anchor is there, it's either...
			if (intval($urltest['fragment'])) {
				// ...an integer #XXXX (simpliest case)
				$post_ID = $urltest['fragment'];
				$way = 'from the fragment (numeric)';
			} elseif (preg_match('/post-[0-9]+/',$urltest['fragment'])) {
				// ...a post id in the form 'post-###'
				$post_ID = preg_replace('/[^0-9]+/', '', $urltest['fragment']);
				$way = 'from the fragment (post-###)';
			} elseif (is_string($urltest['fragment'])) {
				// ...or a string #title, a little more complicated
				$title = preg_replace('/[^a-z0-9]/i', '.', $urltest['fragment']);
				$sql = "SELECT ID FROM $wpdb->posts WHERE post_title RLIKE '$title'";
				if (! ($post_ID = $wpdb->get_var($sql)) ) {
					// returning unknown error '0' is better than die()ing
			  		return new IXR_Error(0, '');
				}
				$way = 'from the fragment (title)';
			}
		} else {
			// TODO: Attempt to extract a post ID from the given URL
	  		return new IXR_Error(33, 'The specified target URI cannot be used as a target. It either doesn\'t exist, or it is not a pingback-enabled resource.');
		}
		$post_ID = (int) $post_ID;


		logIO("O","(PB) URI='$pagelinkedto' ID='$post_ID' Found='$way'");

		$post = get_post($post_ID);

		if ( !$post ) // Post_ID not found
	  		return new IXR_Error(33, 'The specified target URI cannot be used as a target. It either doesn\'t exist, or it is not a pingback-enabled resource.');

		if ( $post_ID == url_to_postid($pagelinkedfrom) )
			return new IXR_Error(0, 'The source URI and the target URI cannot both point to the same resource.');

		// Check if pings are on
		if ( 'closed' == $post->ping_status )
	  		return new IXR_Error(33, 'The specified target URI cannot be used as a target. It either doesn\'t exist, or it is not a pingback-enabled resource.');

		// Let's check that the remote site didn't already pingback this entry
		$result = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = '$post_ID' AND comment_author_url = '$pagelinkedfrom'");

		if ( $wpdb->num_rows ) // We already have a Pingback from this URL
	  		return new IXR_Error(48, 'The pingback has already been registered.');

		// very stupid, but gives time to the 'from' server to publish !
		sleep(1);

		// Let's check the remote site
		$linea = wp_remote_fopen( $pagelinkedfrom );
		if ( !$linea )
	  		return new IXR_Error(16, 'The source URI does not exist.');

		// Work around bug in strip_tags():
		$linea = str_replace('<!DOC', '<DOC', $linea);
		$linea = preg_replace( '/[\s\r\n\t]+/', ' ', $linea ); // normalize spaces
		$linea = preg_replace( "/ <(h1|h2|h3|h4|h5|h6|p|th|td|li|dt|dd|pre|caption|input|textarea|button|body)[^>]*>/", "\n\n", $linea );

		preg_match('|<title>([^<]*?)</title>|is', $linea, $matchtitle);
		$title = $matchtitle[1];
		if ( empty( $title ) )
			return new IXR_Error(32, 'We cannot find a title on that page.');

		$linea = strip_tags( $linea, '<a>' ); // just keep the tag we need

		$p = explode( "\n\n", $linea );
		
		$sem_regexp_pb = "/(\\/|\\\|\*|\?|\+|\.|\^|\\$|\(|\)|\[|\]|\||\{|\})/";
		$sem_regexp_fix = "\\\\$1";
		$link = preg_replace( $sem_regexp_pb, $sem_regexp_fix, $pagelinkedfrom );
		
		$finished = false;
		foreach ( $p as $para ) {
			if ( $finished )
				continue;
			if ( strstr( $para, $pagelinkedto ) ) {
				$context = preg_replace( "/.*<a[^>]+".$link."[^>]*>([^>]+)<\/a>.*/", "$1", $para );
				$excerpt = strip_tags( $para );
				$excerpt = trim( $excerpt );
				$use     = preg_quote( $context );
				$excerpt = preg_replace("|.*?\s(.{0,100}$use.{0,100})\s|s", "$1", $excerpt);
				$finished = true;
			}
		}

		if ( empty($context) ) // URL pattern not found
			return new IXR_Error(17, 'The source URI does not contain a link to the target URI, and so cannot be used as a source.');

		$pagelinkedfrom = preg_replace('#&([^amp\;])#is', '&amp;$1', $pagelinkedfrom);

		$context = '[...] ' . wp_specialchars( $excerpt ) . ' [...]';
		$original_pagelinkedfrom = $pagelinkedfrom;
		$pagelinkedfrom = $wpdb->escape( $pagelinkedfrom );
		$original_title = $title;

		$comment_post_ID = (int) $post_ID;
		$comment_author = $title;
		$this->escape($comment_author);
		$comment_author_url = $pagelinkedfrom;
		$comment_content = $context;
		$this->escape($comment_content);
		$comment_type = 'pingback';

		$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_url', 'comment_content', 'comment_type');

		wp_new_comment($commentdata);
		do_action('pingback_post', $wpdb->insert_id);
		
		return "Pingback from $pagelinkedfrom to $pagelinkedto registered. Keep the web talking! :-)";
	}


	/* pingback.extensions.getPingbacks returns an array of URLs
	   that pingbacked the given URL
	   specs on http://www.aquarionics.com/misc/archives/blogite/0198.html */
	function pingback_extensions_getPingbacks($args) {

		global $wpdb;

		$this->escape($args);

		$url = $args;

		$post_ID = url_to_postid($url);
		if (!$post_ID) {
			// We aren't sure that the resource is available and/or pingback enabled
	  		return new IXR_Error(33, 'The specified target URI cannot be used as a target. It either doesn\'t exist, or it is not a pingback-enabled resource.');
		}

		$actual_post = wp_get_single_post($post_ID, ARRAY_A);

		if (!$actual_post) {
			// No such post = resource not found
	  		return new IXR_Error(32, 'The specified target URI does not exist.');
		}

		$comments = $wpdb->get_results("SELECT comment_author_url, comment_content, comment_author_IP, comment_type FROM $wpdb->comments WHERE comment_post_ID = $post_ID");

		if (!$comments) {
			return array();
		}

		$pingbacks = array();
		foreach($comments as $comment) {
			if ( 'pingback' == $comment->comment_type )
				$pingbacks[] = $comment->comment_author_url;
		}

		return $pingbacks;
	}
}


$wp_xmlrpc_server = new wp_xmlrpc_server();

?>
