<?php

# fix for mozBlog and other cases where '<?xml' isn't on the very first line
$HTTP_RAW_POST_DATA = trim($HTTP_RAW_POST_DATA);

include('../wp-config.php');
include_once(ABSPATH . WPINC . '/class-IXR.php');
include_once(ABSPATH . WPINC . '/functions-post.php');

// Turn off all warnings and errors.
// error_reporting(0);

$post_default_title = ""; // posts submitted via the xmlrpc interface get that title
$post_default_category = 1; // posts submitted via the xmlrpc interface go into that category

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

logIO("I", $HTTP_RAW_POST_DATA);


function printr($var, $do_not_echo = false) {
	// from php.net/print_r user contributed notes 
	ob_start();
	print_r($var);
	$code =  htmlentities(ob_get_contents());
	ob_clean();
	if (!$do_not_echo) {
	  echo "<pre>$code</pre>";
	}
	return $code;
}

function mkdir_p($target) {
	// from php.net/mkdir user contributed notes 
	if (file_exists($target)) {
	  if (!is_dir($target)) {
	    return false;
	  } else {
	    return true;
	  }
	}

	// Attempting to create the directory may clutter up our display.
	if (@mkdir($target)) {
	  return true;
	}

	// If the above failed, attempt to create the parent node, then try again.
	if (mkdir_p(dirname($target))) {
	  return mkdir_p($target);
	}

	return false;
}


class wp_xmlrpc_server extends IXR_Server {

	function wp_xmlrpc_server() {
		$this->IXR_Server(array(
		  'blogger.getUsersBlogs' => 'this:blogger_getUsersBlogs',
		  'blogger.getUserInfo' => 'this:blogger_getUserInfo',
		  'blogger.getPost' => 'this:blogger_getPost',
		  'blogger.getRecentPosts' => 'this:blogger_getRecentPosts',
		  'blogger.getTemplate' => 'this:blogger_getTemplate',
		  'blogger.setTemplate' => 'this:blogger_setTemplate',
		  'blogger.newPost' => 'this:blogger_newPost',
		  'blogger.editPost' => 'this:blogger_editPost',
		  'blogger.deletePost' => 'this:blogger_deletePost',

		  'metaWeblog.newPost' => 'this:mw_newPost',
		  'metaWeblog.editPost' => 'this:mw_editPost',
		  'metaWeblog.getPost' => 'this:mw_getPost',
		  'metaWeblog.getRecentPosts' => 'this:mw_getRecentPosts',
		  'metaWeblog.getCategories' => 'this:mw_getCategories',
		  'metaWeblog.newMediaObject' => 'this:mw_newMediaObject',

		  'demo.sayHello' => 'this:sayHello',
		  'demo.addTwoNumbers' => 'this:addTwoNumbers'
		));
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




	/* Blogger API functions
	 * specs on http://plant.blogger.com/api and http://groups.yahoo.com/group/bloggerDev/
	 */


	/* blogger.getUsersBlogs will make more sense once we support multiple blogs */
	function blogger_getUsersBlogs($args) {

	  $user_login = $args[1];
	  $user_pass  = $args[2];

	  if (!$this->login_pass_ok($user_login, $user_pass)) {
	    return $this->error;
	  }

	  $user_data = get_userdatabylogin($user_login);
	  $is_admin = $user_data->user_level > 3;

	  $struct = array(
	    'isAdmin'  => $is_admin,
	    'url'      => get_settings('home') .'/'.get_settings('blogfilename'),
	    'blogid'   => '1',
	    'blogName' => get_settings('blogname')
	  );

	  return array($struct);
	}


	/* blogger.getUsersInfo gives your client some info about you, so you don't have to */
	function blogger_getUserInfo($args) {

	  $user_login = $args[1];
	  $user_pass  = $args[2];

	  if (!$this->login_pass_ok($user_login, $user_pass)) {
	    return $this->error;
	  }

	  $user_data = get_userdatabylogin($user_login);

	  $struct = array(
	    'nickname'  => $user_data->user_nickname,
	    'userid'    => $user_data->ID,
	    'url'       => $user_data->user_url,
	    'email'     => $user_data->user_email,
	    'lastname'  => $user_data->user_lastname,
	    'firstname' => $user_data->user_firstname
	  );

	  return $struct;
	}


	/* blogger.getPost ...gets a post */
	function blogger_getPost($args) {

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

	    $content  = '<title>'.stripslashes($entry['post_itle']).'</title>';
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

	  $blog_ID    = $args[1];
	  $user_login = $args[2];
	  $user_pass  = $args[3];
	  $template   = $args[4]; /* could be 'main' or 'archiveIndex', but we don't use it */

	  if (!$this->login_pass_ok($user_login, $user_pass)) {
	    return $this->error;
	  }

	  $user_data = get_userdatabylogin($user_login);

	  if ($user_data->user_level < 3) {
	    return new IXR_Error(401, 'Sorry, users whose level is less than 3, can not edit the template.');
	  }

	  /* warning: here we make the assumption that the weblog's URI is on the same server */
	  $filename = get_settings('home').'/'.get_settings('blogfilename');
	  $filename = preg_replace('#http://.+?/#', $_SERVER['DOCUMENT_ROOT'].'/', $filename);

	  $f = fopen($filename, 'r');
	  $content = fread($f, filesize($filename));
	  fclose($f);

	  /* so it is actually editable with a windows/mac client */
	  // FIXME: (or delete me) do we really want to cater to bad clients at the expense of good ones by BEEPing up their line breaks? commented.     $content = str_replace("\n", "\r\n", $content); 

	  return $content;
	}


	/* blogger.setTemplate updates the content of blog_filename */
	function blogger_setTemplate($args) {

	  $blog_ID    = $args[1];
	  $user_login = $args[2];
	  $user_pass  = $args[3];
	  $content    = $args[4];
	  $template   = $args[5]; /* could be 'main' or 'archiveIndex', but we don't use it */

	  if (!$this->login_pass_ok($user_login, $user_pass)) {
	    return $this->error;
	  }

	  $user_data = get_userdatabylogin($user_login);

	  if ($user_data->user_level < 3) {
	    return new IXR_Error(401, 'Sorry, users whose level is less than 3, can not edit the template.');
	  }

	  /* warning: here we make the assumption that the weblog's URI is on the same server */
	  $filename = get_settings('home').'/'.get_settings('blogfilename');
	  $filename = preg_replace('#http://.+?/#', $_SERVER['DOCUMENT_ROOT'].'/', $filename);

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

	  $blog_ID    = $args[1]; /* though we don't use it yet */
	  $user_login = $args[2];
	  $user_pass  = $args[3];
	  $content    = $args[4];
	  $publish    = $args[5];

	  if (!$this->login_pass_ok($user_login, $user_pass)) {
	    return $this->error;
	  }

	  $user_data = get_userdatabylogin($user_login);
	  if (!user_can_create_post($user_data->ID, $blog_ID)) {
	    return new IXR_Error(401, 'Sorry, you can not post on this weblog or category.');
	  }

	  $post_status = ($publish) ? 'publish' : 'draft';

	  $post_author = $user_data->ID;

	  $post_title = xmlrpc_getposttitle($content);
	  $post_category = xmlrpc_getpostcategory($content);

	  $content = xmlrpc_removepostdata($content);
	  $post_content = format_to_post($content);

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

	  $post_ID     = $args[1];
	  $user_login  = $args[2];
	  $user_pass   = $args[3];
	  $new_content = $args[4];
	  $publish     = $args[5];

	  if (!$this->login_pass_ok($user_login, $user_pass)) {
	    return $this->error;
	  }

	  $actual_post = wp_get_single_post($post_ID,ARRAY_A);

	  if (!$actual_post) {
	  	return new IXR_Error(404, 'Sorry, no such post.');
	  }

	  $post_author_data = get_userdata($actual_post['post_author']);
	  $user_data = get_userdatabylogin($user_login);

	  if (!user_can_edit_post($user_data->ID, $post_ID)) {
	    return new IXR_Error(401, 'Sorry, you do not have the right to edit this post.');
	  }

	  extract($actual_post);
	  $content = $newcontent;

	  $post_title = xmlrpc_getposttitle($content);
	  $post_category = xmlrpc_getpostcategory($content);

	  $content = xmlrpc_removepostdata($content);
	  $post_content = format_to_post($content);

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

	  $user_data = get_userdatabylogin($user_login);

	  if (!user_can_delete_post($user_data->ID, $post_ID)) {
	    return new IXR_Error(401, 'Sorry, you do not have the right to delete this post.');
	  }

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

	  global $wpdb;

	  $blog_ID     = $args[0]; // we will support this in the near future
	  $user_login  = $args[1];
	  $user_pass   = $args[2];
	  $content_struct = $args[3];
	  $publish     = $args[4];

	  if (!$this->login_pass_ok($user_login, $user_pass)) {
	    return $this->error;
	  }

	  $user_data = get_userdatabylogin($user_login);
	  if (!user_can_create_post($user_data->ID, $blog_ID)) {
	    return new IXR_Error(401, 'Sorry, you can not post on this weblog or category.');
	  }

	  $post_author = $user_data->ID;

	  $post_title = $content_struct['title'];
	  $post_content = format_to_post($content_struct['description']);
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
		
	  // Do some timestamp voodoo
	  $dateCreatedd = $content_struct['dateCreated'];
	  $dateCreated = $dateCreatedd->getIso();
	  if (!empty($dateCreated)) {
	    $post_date     = get_date_from_gmt(iso8601_to_datetime($dateCreated));
	    $post_date_gmt = iso8601_to_datetime($dateCreated, GMT);
	  } else {
	    $post_date     = current_time('mysql');
	    $post_date_gmt = current_time('mysql', 1);
	  }

	  $catnames = $content_struct['categories'];
	  logio('O', 'Post cats: ' . printr($catnames,true));
	  $post_category = array();

	  if ($catnames) {
	    foreach ($catnames as $cat) {
	      $post_category[] = get_cat_ID($cat);
	    }
	  } else {
	    $post_category[] = 1;
	  }
		
	  // We've got all the data -- post it:
	  $postdata = compact('post_author', 'post_date', 'post_date_gmt', 'post_content', 'post_title', 'post_category', 'post_status', 'post_excerpt', 'comment_status', 'ping_status');

	  $post_ID = wp_insert_post($postdata);

	  if (!$post_ID) {
	    return new IXR_Error(500, 'Sorry, your entry could not be posted. Something wrong happened.');
	  }

	  logIO('O', "Posted ! ID: $post_ID");

	  // FIXME: do we pingback always? pingback($content, $post_ID);
	  trackback_url_list($content_struct['mt_tb_ping_urls'],$post_ID);

	  return $post_ID;
	}


	/* metaweblog.editPost ...edits a post */
	function mw_editPost($args) {

	  global $wpdb;

	  $post_ID     = $args[0];
	  $user_login  = $args[1];
	  $user_pass   = $args[2];
	  $content_struct = $args[3];
	  $publish     = $args[4];

	  if (!$this->login_pass_ok($user_login, $user_pass)) {
	    return $this->error;
	  }

	  $user_data = get_userdatabylogin($user_login);
	  if (!user_can_edit_post($user_data->ID, $post_ID)) {
	    return new IXR_Error(401, 'Sorry, you can not edit this post.');
	  }

	  extract($postdata);

	  $post_title = $content_struct['title'];
	  $post_content = format_to_post($content_struct['description']);
	  $catnames = $content_struct['categories'];
		
	  if ($catnames) {
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

	  $comment_status = (empty($content_struct['mt_allow_comments'])) ?
	    get_settings('default_comment_status')
	    : $content_struct['mt_allow_comments'];

	  $ping_status = (empty($content_struct['mt_allow_pings'])) ?
	    get_settings('default_ping_status')
	    : $content_struct['mt_allow_pings'];

	  // Do some timestamp voodoo
	  $dateCreated = $content_struct['dateCreated'];
	  $dateCreated = $dateCreated->getIso();
	  if (!empty($dateCreated)) {
	    $post_date     = get_date_from_gmt(iso8601_to_datetime($dateCreated));
	    $post_date_gmt = iso8601_to_datetime($dateCreated, GMT);
	  } else {
	    $post_date     = $postdata['post_date'];
	    $post_date_gmt = $postdata['post_date_gmt'];
	  }

	  // We've got all the data -- post it:
	  $newpost = compact('ID', 'post_content', 'post_title', 'post_category', 'post_status', 'post_excerpt', 'comment_status', 'ping_status', 'post_date', 'post_date_gmt');

	  $post_ID = wp_update_post($newpost);
	  if (!$post_ID) {
	    return new IXR_Error(500, 'Sorry, your entry could not be edited. Something wrong happened.');
	  }

	  logIO('O',"(MW) Edited ! ID: $post_ID");

	  // FIXME: do we pingback always? pingback($content, $post_ID);
	  trackback_url_list($content_struct['mt_tb_ping_urls'], $post_ID);

	  return $post_ID;
	}


	/* metaweblog.getPost ...returns a post */
	function mw_getPost($args) {

	  global $wpdb;

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
	    $link = post_permalink($entry['ID']);

	    $allow_comments = ('open' == $postdata['comment_status']) ? 1 : 0;
	    $allow_pings = ('open' == $postdata['ping_status']) ? 1 : 0;

	    $resp = array(
	      'dateCreated' => new IXR_Date($post_date),
	      'userid' => $entry['post_author'],
	      'postid' => $entry['ID'],
	      'description' => $post['main'],
	      'title' => $entry['post_title'],
	      'link' => $link,
	      'permaLink' => $link,
// commented out because no other tool seems to use them
//	      'content' => $entry['post_content'],
//	      'categories' => $categories
	      'mt_excerpt' => $entry['post_excerpt'],
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
// commented out because no other tool seems to use them
//	      'content' => $entry['post_content'],
//	      'categories' => $categories
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
	      $struct['htmlUrl'] = htmlspecialchars(get_category_link(false, $cat['cat_ID'], $cat['cat_name']));
	      $struct['rssUrl'] = htmlspecialchars(get_category_rss_link(false, $cat['cat_ID'], $cat['cat_name']));

	      $categories_struct[] = $struct;
	    }
	  }

	  return $categories_struct;
	}


	/* metaweblog.newMediaObject uploads a file, following your settings */
	function mw_newMediaObject($args) {
	  // adapted from a patch by Johann Richard
	  // http://mycvs.org/archives/2004/06/30/file-upload-to-wordpress-in-ecto/

	  $blog_ID     = $args[0];
	  $user_login  = $args[1];
	  $user_pass   = $args[2];
	  $data        = $args[3];

	  $name = $data['name'];
	  $type = $data['type'];
	  $bits = $data['bits'];

	  $file_realpath = get_settings('fileupload_realpath'); 
	  $file_url = get_settings('fileupload_url');

	  logIO('O', '(MW) Received '.strlen($bits).' bytes');

	  if (!$this->login_pass_ok($user_login, $user_pass)) {
	    return $this->error;
	  }

	  $user_data = get_userdatabylogin($user_login);

	  if(!get_settings('use_fileupload')) {
	    // Uploads not allowed
	    logIO('O', '(MW) Uploads not allowed');
	    $this->error = new IXR_Error(405, 'No uploads allowed for this site.');
	    return $this->error;
	  } 

	  if(get_settings('fileupload_minlevel') > $user_data->user_level) {
	    // User has not enough privileges
	    logIO('O', '(MW) Not enough privilege: user level too low');
	    $this->error = new IXR_Error(401, 'You are not allowed to upload files to this site.');
	    return $this->error;
	  }

	  if(trim($file_realpath) == '' || trim($file_url) == '' ) {
	    // WordPress is not correctly configured
	    logIO('O', '(MW) Bad configuration. Real/URL path not defined');
	    $this->error = new IXR_Error(500, 'Please configure WordPress with valid paths for file upload.');
	    return $this->error;
	  }

	  $prefix = '/';

	  if(!empty($name)) {
	    // Create the path
	    $localpath = $file_realpath.$prefix.$name;
	    $url = $file_url.$prefix.$name;

	    if (mkdir_p(dirname($localpath))) {

	      /* encode & write data (binary) */
	      $ifp = fopen($localpath, 'wb');
	      $success = fwrite($ifp, $bits);
	      fclose($ifp);
	      @chmod($localpath, 0666);

	      if($success) {
	        $resp = array($url);
	        return $resp;
	      } else {
	        logIO('O', '(MW) Could not write file '.$name.' to '.$localpath);
	        return new IXR_Error(500, 'Could not write file '.$name);
	      }

	    } else {
	      return new IXR_Error(500, 'Could not create directories for '.$name);
	    }
	  }
	}



	/* MovableType API functions
	 * specs on http://www.movabletype.org/docs/mtmanual_programmatic.html
	 */

	/* mt.getRecentPostTitles ...returns recent posts' titles */
	function mt_getRecentPostTitles($args) {

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

}


$wp_xmlrpc_server = new wp_xmlrpc_server();

?>