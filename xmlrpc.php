<?php

# fix for mozBlog and other cases where '<?xml' isn't on the very first line
$HTTP_RAW_POST_DATA = trim($HTTP_RAW_POST_DATA);

include('../wp-config.php');
include_once(ABSPATH . WPINC . '/class-IXR.php');

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



class wp_xmlrpc_server extends IXR_Server {

	function wp_xmlrpc_server() {
		$this->IXR_Server(array(
		  'blogger.getUsersBlogs' => 'this:blogger_getUsersBlogs',
		  'blogger.getUserInfo' => 'this:blogger_getUserInfo',
		  'blogger.getPost' => 'this:blogger_getPost',
		  'blogger.getRecentPosts' => 'this:blogger_getRecentPosts',
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
	    'blogid'   => 1,
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
	  $post_data = get_postdata($post_ID);

	  $post_date = mysql2date('Ymd\TH:i:s', $post_data['Date']);

	  $categories = implode(',', wp_get_post_cats(1, $post_ID));

	  $content  = '<title>'.stripslashes($post_data['Title']).'</title>';
	  $content .= '<category>'.$categories.'</category>';
	  $content .= stripslashes($post_data['Content']);
	  
	  $struct = array(
	    'userid'    => $post_data['Author_ID'],
	    'dateCreateed' => mysql2date('Ymd\TH:i:s', $post_data['Date']),
	    'content'     => $content,
	    'postid'  => $post_data['ID']
	  );
	  
	  return $struct;
	}


	/* blogger.getRecentPosts ...gets recent posts */
	function blogger_getRecentPosts($args) {

	  global $tableposts, $wpdb;

	  $blog_ID    = $args[1]; /* though we don't use it yet */
	  $user_login = $args[2];
	  $user_pass  = $args[3];
	  $num_posts  = $args[4];

	  if ($num_posts > 0) {
	    $limit = " LIMIT $num_posts";
	  } else {
	    $limit = '';
	  }

	  if (!$this->login_pass_ok($user_login, $user_pass)) {
	    return $this->error;
	  }

	  $sql = "SELECT * FROM $tableposts ORDER BY post_date DESC".$limit;
	  $result = $wpdb->get_results($sql);

	  if (!$result) {
	    $this->error = new IXR_Error(500, 'Either there are no posts, or something went wrong.');
	    return $this->error;
	  }

	  $i = 0;
	  foreach ($result as $row) {
	    $post_data = array(
	      'ID' => $row->ID,
	      'Author_ID' => $row->post_author,
	      'Date' => $row->post_date,
	      'Content' => $row->post_content,
	      'Title' => $row->post_title,
	      'Category' => $row->post_category
	    );

	    $categories = implode(',', wp_get_post_cats(1, $post_data['ID']));
	    $post_date = mysql2date("Ymd\TH:i:s", $post_data['Date']);

	    $content  = '<title>'.stripslashes($post_data['Title']).'</title>';
	    $content .= '<category>'.$categories.'</category>';
	    $content .= stripslashes($post_data['Content']);

	    $author_data = get_userdata($post_data['Author_ID']);

	    switch($author_data['user_idmode']) {
	    case 'nickname':
	      $author_name = $author_data['user_nickname'];
	    case 'login':
	      $author_name = $author_data['user_login'];
	      break;
	    case 'firstname':
	      $author_name = $author_data['user_firstname'];
	      break;
	    case 'lastname':
	      $author_name = $author_data['user_lastname'];
	      break;
	    case 'namefl':
	      $author_name = $author_data['user_firstname']." ".$author_data['user_lastname'];
	      break;
	    case 'namelf':
	      $author_name = $author_data['user_lastname']." ".$author_data['user_firstname'];
	      break;
	    default:
	      $author_name = $author_data['user_nickname'];
	      break;
	    }

	    $struct[$i] = array(
	      'authorName' => $author_name,
	      'userid' => $post_data['Author_ID'],
	      'dateCreated' => $post_date,
	      'content' => $content,
	      'postid' => $post_data['ID'],
	      'category' => $categories
	    );

	    $i++;
	  }

	  $recent_posts = array();
	  for ($j=0; $j<count($struct); $j++) {
	    array_push($recent_posts, $struct[$j]);
	  }

	  return $recent_posts;
	}
}

$wp_xmlrpc_server = new wp_xmlrpc_server();



/* functions that we ought to relocate
 * and/or roll into a WP class as extension of wpdb
 */

function wp_get_post_cats($blogid = '1', $post_ID = 0) {
	global $wpdb, $tablepost2cat;
	
	$sql = "SELECT category_id 
		FROM $tablepost2cat 
		WHERE post_id = $post_ID 
		ORDER BY category_id";

	$result = $wpdb->get_col($sql);

	return array_unique($result);
}



?>