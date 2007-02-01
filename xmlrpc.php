<?php

define('XMLRPC_REQUEST', true);

// Some browser-embedded clients send cookies. We don't want them.
$_COOKIE = array();

# fix for mozBlog and other cases where '<?xml' isn't on the very first line
if ( isset($HTTP_RAW_POST_DATA) )
	$HTTP_RAW_POST_DATA = trim($HTTP_RAW_POST_DATA);

include('./wp-config.php');

if ( isset( $_GET['rsd'] ) ) { // http://archipelago.phrasewise.com/rsd 
header('Content-type: text/xml; charset=' . get_option('blog_charset'), true);

?>
<?php echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>
<rsd version="1.0" xmlns="http://archipelago.phrasewise.com/rsd">
  <service>
    <engineName>WordPress</engineName>
    <engineLink>http://wordpress.org/</engineLink>
    <homePageLink><?php bloginfo_rss('url') ?></homePageLink>
    <apis>
      <api name="WordPress" blogID="1" preferred="false" apiLink="<?php bloginfo_rss('url') ?>/xmlrpc.php" />
      <api name="Movable Type" blogID="1" preferred="true" apiLink="<?php bloginfo_rss('url') ?>/xmlrpc.php" />
      <api name="MetaWeblog" blogID="1" preferred="false" apiLink="<?php bloginfo_rss('url') ?>/xmlrpc.php" />
      <api name="Blogger" blogID="1" preferred="false" apiLink="<?php bloginfo_rss('url') ?>/xmlrpc.php" />
    </apis>
  </service>
</rsd>
<?php
exit;
}

include_once(ABSPATH . 'wp-admin/admin-functions.php');
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
			// WordPress API
			'wp.getPage'			=> 'this:wp_getPage',
			'wp.getPages'			=> 'this:wp_getPages',
			'wp.newPage'			=> 'this:wp_newPage',
			'wp.deletePage'			=> 'this:wp_deletePage',
			'wp.editPage'			=> 'this:wp_editPage',
			'wp.getPageList'		=> 'this:wp_getPageList',
			'wp.getAuthors'			=> 'this:wp_getAuthors',
			'wp.getCategories'		=> 'this:mw_getCategories',		// Alias
			'wp.newCategory'		=> 'this:wp_newCategory',
			'wp.suggestCategories'	=> 'this:wp_suggestCategories',

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

	/**
	 * WordPress XML-RPC API
	 * wp_getPage
	 */
	function wp_getPage($args) {
		$this->escape($args);

		$blog_id	= $args[0];
		$page_id	= $args[1];
		$username	= $args[2];
		$password	= $args[3];

		if(!$this->login_pass_ok($username, $password)) {
			return($this->error);
		}

		// Lookup page info.
		$page = get_page($page_id);

		// If we found the page then format the data.
		if($page->ID && ($page->post_type == "page")) {
			// Get all of the page content and link.
			$full_page = get_extended($page->post_content);
			$link = post_permalink($page->ID);

			// Get info the page parent if there is one.
			$parent_title = "";
			if(!empty($page->post_parent)) {
				$parent = get_page($page->post_parent);
				$parent_title = $parent->post_title;
			}

			// Determine comment and ping settings.
			$allow_comments = ("open" == $page->comment_status) ? 1 : 0;
			$allow_pings = ("open" == $page->ping_status) ? 1 : 0;

			// Format page date.
			$page_date = mysql2date("Ymd\TH:i:s", $page->post_date);

			// Pull the categories info together.
			$categories = array();
			foreach(wp_get_post_categories($page->ID) as $cat_id) {
				$categories[] = get_cat_name($cat_id);
			}

			// Get the author info.
			$author = get_userdata($page->post_author);

			$page_struct = array(
				"dateCreated"			=> new IXR_Date($page_date),
				"userid"				=> $page->post_author,
				"page_id"				=> $page->ID,
				"page_status"			=> $page->post_status,
				"description"			=> $full_page["main"],
				"title"					=> $page->post_title,
				"link"					=> $link,
				"permaLink"				=> $link,
				"categories"			=> $categories,
				"excerpt"				=> $page->post_excerpt,
				"text_more"				=> $full_page["extended"],
				"mt_allow_comments"		=> $allow_comments,
				"mt_allow_pings"		=> $allow_pings,
				"wp_slug"				=> $page->post_name,
				"wp_password"			=> $page->post_password,
				"wp_author"				=> $author->display_name,
				"wp_page_parent_id"		=> $page->post_parent,
				"wp_page_parent_title"	=> $parent_title,
				"wp_page_order"			=> $page->menu_order,
				"wp_author_username"	=> $author->user_login
			);

			return($page_struct);
		}
		// If the page doesn't exist indicate that.
		else {
			return(new IXR_Error(404, "Sorry, no such page."));
		}
	}

	/**
	 * WordPress XML-RPC API
 	 * wp_getPages
	 */
	function wp_getPages($args) {
		$this->escape($args);

		$blog_id	= $args[0];
		$username	= $args[1];
		$password	= $args[2];

		if(!$this->login_pass_ok($username, $password)) {
			return($this->error);
		}

		// Lookup info on pages.
		$pages = array();
		$pages = get_pages();
		$num_pages = count($pages);

		// If we have pages, put together their info.
		if($num_pages >= 1) {
			$pages_struct = array();

			for($i = 0; $i < $num_pages; $i++) {
				$page = wp_xmlrpc_server::wp_getPage(array(
					$blog_id, $pages[$i]->ID, $username, $password
				));
				$pages_struct[] = $page;
			}

			return($pages_struct);
		}
		// If no pages were found return an error.
		else {
			return(new IXR_Error(404, "Sorry, no pages were found."));
		}
	}

	/**
	 * WordPress XML-RPC API
 	 * wp_newPage
	 */
	function wp_newPage($args) {
		$this->escape($args);

		$blog_id	= $args[0];
		$username	= $args[1];
		$password	= $args[2];
		$page		= $args[3];
		$publish	= $args[4];

		if(!$this->login_pass_ok($username, $password)) {
			return($this->error);
		}

		// Set the user context and check if they are allowed
		// to add new pages.
		$user = set_current_user(0, $username);
		if(!current_user_can("publish_pages")) {
			return(new IXR_Error(401, "Sorry, you can not add new pages."));
		}

		// Mark this as content for a page.
		$args[3]["post_type"] = "page";

		// Let mw_newPost do all of the heavy lifting.
		return($this->mw_newPost($args));
	}

	/**
	 * WordPress XML-RPC API
	 * wp_deletePage
	 */
	function wp_deletePage($args) {
		$this->escape($args);

		$blog_id	= $args[0];
		$username	= $args[1];
		$password	= $args[2];
		$page_id	= $args[3];

		if(!$this->login_pass_ok($username, $password)) {
			return($this->error);
		}

		// Get the current page based on the page_id and
		// make sure it is a page and not a post.
		$actual_page = wp_get_single_post($page_id, ARRAY_A);
		if(
			!$actual_page
			|| ($actual_page["post_type"] != "page")
		) {
			return(new IXR_Error(404, "Sorry, no such page."));
		}

		// Set the user context and make sure they can delete pages.
		set_current_user(0, $username);
		if(!current_user_can("delete_page", $page_id)) {
			return(new IXR_Error(401, "Sorry, you do not have the right to delete this page."));
		}

		// Attempt to delete the page.
		$result = wp_delete_post($page_id);
		if(!$result) {
			return(new IXR_Error(500, "Failed to delete the page."));
		}

		return(true);
	}

	/**
	 * WordPress XML-RPC API
	 * wp_editPage
	 */
	function wp_editPage($args) {
		$this->escape($args);

		$blog_id	= $args[0];
		$page_id	= $args[1];
		$username	= $args[2];
		$password	= $args[3];
		$content	= $args[4];
		$publish	= $args[5];

		if(!$this->login_pass_ok($username, $password)) {
			return($this->error);
		}

		// Get the page data and make sure it is a page.
		$actual_page = wp_get_single_post($page_id, ARRAY_A);
		if(
			!$actual_page
			|| ($actual_page["post_type"] != "page")
		) {
			return(new IXR_Error(404, "Sorry, no such page."));
		}

		// Set the user context and make sure they are allowed to edit pages.
		set_current_user(0, $username);
		if(!current_user_can("edit_page", $page_id)) {
			return(new IXR_Error(401, "Sorry, you do not have the right to edit this page."));
		}

		// Mark this as content for a page.
		$content["post_type"] = "page";

		// Arrange args in the way mw_editPost understands.
		$args = array(
			$page_id,
			$username,
			$password,
			$content,
			$publish
		);

		// Let mw_editPost do all of the heavy lifting.
		return($this->mw_editPost($args));
	}

	/**
	 * WordPress XML-RPC API
	 * wp_getPageList
	 */
	function wp_getPageList($args) {
		global $wpdb;

		$this->escape($args);

		$blog_id				= $args[0];
		$username				= $args[1];
		$password				= $args[2];

		if(!$this->login_pass_ok($username, $password)) {
			return($this->error);
		}

		// Get list of pages ids and titles
		$page_list = $wpdb->get_results("
			SELECT ID page_id,
				post_title page_title,
				post_parent page_parent_id
			FROM {$wpdb->posts}
			WHERE post_type = 'page'
			ORDER BY ID
		");

		return($page_list);
	}

	/**
	 * WordPress XML-RPC API
	 * wp_getAuthors
	 */
	function wp_getAuthors($args) {
		global $wpdb;

		$this->escape($args);

		$blog_id	= $args[0];
		$username	= $args[1];
		$password	= $args[2];

		if(!$this->login_pass_ok($username, $password)) {
			return($this->error);
		}

		return(get_users_of_blog());
	}

	/**
	 * WordPress XML-RPC API
	 * wp_newCategory
	 */
	function wp_newCategory($args) {
		$this->escape($args);

		$blog_id				= $args[0];
		$username				= $args[1];
		$password				= $args[2];
		$category				= $args[3];

		if(!$this->login_pass_ok($username, $password)) {
			return($this->error);
		}

		// Set the user context and make sure they are
		// allowed to add a category.
		set_current_user(0, $username);
		if(!current_user_can("manage_categories", $page_id)) {
			return(new IXR_Error(401, "Sorry, you do not have the right to add a category."));
		}

		// We need this to make use of the wp_insert_category()
		// funciton.
		require_once(ABSPATH . "wp-admin/admin-db.php");

		// If no slug was provided make it empty so that
		// WordPress will generate one.
		if(empty($category["slug"])) {
			$category["slug"] = "";
		}

		// If no parent_id was provided make it empty
		// so that it will be a top level page (no parent).
		if(empty($category["parent_id"])) {
			$category["parent_id"] = "";
		}

		// If no description was provided make it empty.
		if(empty($category["description"])) {
			$category["description"] = "";
		}
		
		$new_category = array(
			"cat_name"				=> $category["name"],
			"category_nicename"		=> $category["slug"],
			"category_parent"		=> $category["parent_id"],
			"category_description"	=> $category["description"]
		);

		$cat_id = wp_insert_category($new_category);
		if(!$cat_id) {
			return(new IXR_Error(500, "Sorry, the new category failed."));
		}

		return($cat_id);
	}

	/**
	 * WordPress XML-RPC API
	 * wp_suggestCategories
	 */
	function wp_suggestCategories($args) {
		global $wpdb;

		$this->escape($args);

		$blog_id				= $args[0];
		$username				= $args[1];
		$password				= $args[2];
		$category				= $args[3];
		$max_results			= $args[4];

		if(!$this->login_pass_ok($username, $password)) {
			return($this->error);
		}

		// Only set a limit if one was provided.
		$limit = "";
		if(!empty($max_results)) {
			$limit = "LIMIT {$max_results}";
		}

		$category_suggestions = $wpdb->get_results("
			SELECT cat_ID category_id,
				cat_name category_name
			FROM {$wpdb->categories}
			WHERE cat_name LIKE '{$category}%'
			{$limit}
		");

		return($category_suggestions);
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
	    'url'      => get_option('home') . '/',
	    'blogid'   => '1',
	    'blogName' => get_option('blogname')
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

		$categories = implode(',', wp_get_post_categories($post_ID));

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
			$categories = implode(',', wp_get_post_categories($entry['ID']));

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

	  /* warning: here we make the assumption that the weblog's URL is on the same server */
	  $filename = get_option('home') . '/';
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

	  /* warning: here we make the assumption that the weblog's URL is on the same server */
	  $filename = get_option('home') . '/';
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
	  $this->attach_uploads( $post_ID, $post_content );

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
	  $this->attach_uploads( $ID, $post_content );

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

		// The post_type defaults to post, but could also be page.
		$post_type = "post";
		if(
			!empty($content_struct["post_type"])
			&& ($content_struct["post_type"] == "page")
		) {
			$post_type = "page";
		}

		// Let WordPress generate the post_name (slug) unless
		// one has been provided.
		$post_name = "";
		if(!empty($content_struct["wp_slug"])) {
			$post_name = $content_struct["wp_slug"];
		}

		// Only use a password if one was given.
		if(!empty($content_struct["wp_password"])) {
			$post_password = $content_struct["wp_password"];
		}

		// Only set a post parent if one was provided.
		if(!empty($content_struct["wp_page_parent_id"])) {
			$post_parent = $content_struct["wp_page_parent_id"];
		}

		// Only set the menu_order if it was provided.
		if(!empty($content_struct["wp_page_order"])) {
			$menu_order = $content_struct["wp_page_order"];
		}

	  $post_author = $user->ID;

		// If an author id was provided then use it instead.
		if(!empty($content_struct["wp_author_id"])) {
			$post_author = $content_struct["wp_author_id"];
		}

	  $post_title = $content_struct['title'];
	  $post_content = apply_filters( 'content_save_pre', $content_struct['description'] );
	  $post_status = $publish ? 'publish' : 'draft';

	  $post_excerpt = $content_struct['mt_excerpt'];
	  $post_more = $content_struct['mt_text_more'];

	  $comment_status = (empty($content_struct['mt_allow_comments'])) ?
	    get_option('default_comment_status')
	    : $content_struct['mt_allow_comments'];

	  $ping_status = (empty($content_struct['mt_allow_pings'])) ?
	    get_option('default_ping_status')
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
	  $postdata = compact('post_author', 'post_date', 'post_date_gmt', 'post_content', 'post_title', 'post_category', 'post_status', 'post_excerpt', 'comment_status', 'ping_status', 'to_ping', 'post_type', 'post_name', 'post_password', 'post_parent', 'menu_order');

	  $post_ID = wp_insert_post($postdata);

	  if (!$post_ID) {
	    return new IXR_Error(500, 'Sorry, your entry could not be posted. Something wrong happened.');
	  }

	  $this->attach_uploads( $post_ID, $post_content );

	  logIO('O', "Posted ! ID: $post_ID");

	  return strval($post_ID);
	}

	function attach_uploads( $post_ID, $post_content ) {
		global $wpdb;

		// find any unattached files
		$attachments = $wpdb->get_results( "SELECT ID, guid FROM {$wpdb->posts} WHERE post_parent = '-1' AND post_type = 'attachment'" );
		if( is_array( $attachments ) ) {
			foreach( $attachments as $file ) {
				if( strpos( $post_content, $file->guid ) !== false ) {
					$wpdb->query( "UPDATE {$wpdb->posts} SET post_parent = '$post_ID' WHERE ID = '{$file->ID}'" );
				}
			}
		}
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

		// The post_type defaults to post, but could also be page.
		$post_type = "post";
		if(
			!empty($content_struct["post_type"])
			&& ($content_struct["post_type"] == "page")
		) {
			$post_type = "page";
		}

		// Let WordPress manage slug if none was provided.
		$post_name = "";
		if(!empty($content_struct["wp_slug"])) {
			$post_name = $content_struct["wp_slug"];
		}

		// Only use a password if one was given.
		if(!empty($content_struct["wp_password"])) {
			$post_password = $content_struct["wp_password"];
		}

		// Only set a post parent if one was given.
		if(!empty($content_struct["wp_page_parent_id"])) {
			$post_parent = $content_struct["wp_page_parent_id"];
		}

		// Only set the menu_order if it was given.
		if(!empty($content_struct["wp_page_order"])) {
			$menu_order = $content_struct["wp_page_order"];
		}

		// Only set the post_author if one is set.
		if(!empty($content_struct["wp_author_id"])) {
			$post_author = $content_struct["wp_author_id"];
		}

		// Only set ping_status if it was provided.
		if(isset($content_struct["mt_allow_pings"])) {
			switch($content_struct["mt_allow_pings"]) {
				case "0":
					$ping_status = "closed";
					break;
				case "1":
					$ping_status = "open";
					break;
			}
		}

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
	    get_option('default_comment_status')
	    : $content_struct['mt_allow_comments'];

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
	  $newpost = compact('ID', 'post_content', 'post_title', 'post_category', 'post_status', 'post_excerpt', 'comment_status', 'ping_status', 'post_date', 'post_date_gmt', 'to_ping', 'post_name', 'post_password', 'post_parent', 'menu_order', 'post_author');

	  $result = wp_update_post($newpost);
	  if (!$result) {
	    return new IXR_Error(500, 'Sorry, your entry could not be edited. Something wrong happened.');
	  }
	  $this->attach_uploads( $ID, $post_content );

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
	    $catids = wp_get_post_categories($post_ID);
	    foreach($catids as $catid) {
	      $categories[] = get_cat_name($catid);
	    }

	    $post = get_extended($postdata['post_content']);
	    $link = post_permalink($postdata['ID']);

		// Get the author info.
		$author = get_userdata($postdata['post_author']);

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
	      'mt_allow_pings' => $allow_pings,
          'wp_slug' => $postdata['post_name'],
          'wp_password' => $postdata['post_password'],
          'wp_author' => $author->display_name,
          'wp_author_username'	=> $author->user_login
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
			$catids = wp_get_post_categories($entry['ID']);
			foreach($catids as $catid) {
				$categories[] = get_cat_name($catid);
			}

			$post = get_extended($entry['post_content']);
			$link = post_permalink($entry['ID']);

			// Get the post author info.
			$author = get_userdata($entry['post_author']);

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
				'mt_allow_pings' => $allow_pings,
				'wp_slug' => $entry['post_name'],
				'wp_password' => $entry['post_password'],
				'wp_author' => $author->display_name,
				'wp_author_username' => $author->user_login
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
		if ($cats = $wpdb->get_results("SELECT cat_ID,cat_name,category_parent FROM $wpdb->categories", ARRAY_A)) {
			foreach ($cats as $cat) {
				$struct['categoryId'] = $cat['cat_ID'];
				$struct['parentId'] = $cat['category_parent'];
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

		$name = sanitize_file_name( $data['name'] );
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

		if ( $upload_err = apply_filters( "pre_upload_error", false ) )
			return new IXR_Error(500, $upload_err);

		$upload = wp_upload_bits($name, $type, $bits);
		if ( ! empty($upload['error']) ) {
			logIO('O', '(MW) Could not write file '.$name);
			return new IXR_Error(500, 'Could not write file '.$name);
		}
		// Construct the attachment array
		// attach to post_id -1
		$post_id = -1;
		$attachment = array(
			'post_title' => $name,
			'post_content' => '',
			'post_type' => 'attachment',
			'post_parent' => $post_id,
			'post_mime_type' => $type,
			'guid' => $upload[ 'url' ]
		);
		// Save the data
		$id = wp_insert_attachment( $attachment, $upload[ 'file' ], $post_id );
		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );

		return apply_filters( 'wp_handle_upload', array( 'file' => $upload[ 'file' ], 'url' => $upload[ 'url' ], 'type' => $type ) );
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
		$catids = wp_get_post_categories(intval($post_ID));
		// first listed category will be the primary category
		$isPrimary = true;
		foreach($catids as $catid) {
			$categories[] = array(
				'categoryName' => get_cat_name($catid),
				'categoryId' => (string) $catid,
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

		wp_set_post_categories($post_ID, $catids);

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
		$cats = wp_get_post_categories($post_ID);
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
		$pos1 = strpos($pagelinkedto, str_replace(array('http://www.','http://','https://www.','https://'), '', get_option('home')));
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
	  		return new IXR_Error(33, 'The specified target URL cannot be used as a target. It either doesn\'t exist, or it is not a pingback-enabled resource.');
		}
		$post_ID = (int) $post_ID;


		logIO("O","(PB) URL='$pagelinkedto' ID='$post_ID' Found='$way'");

		$post = get_post($post_ID);

		if ( !$post ) // Post_ID not found
	  		return new IXR_Error(33, 'The specified target URL cannot be used as a target. It either doesn\'t exist, or it is not a pingback-enabled resource.');

		if ( $post_ID == url_to_postid($pagelinkedfrom) )
			return new IXR_Error(0, 'The source URL and the target URL cannot both point to the same resource.');

		// Check if pings are on
		if ( 'closed' == $post->ping_status )
	  		return new IXR_Error(33, 'The specified target URL cannot be used as a target. It either doesn\'t exist, or it is not a pingback-enabled resource.');

		// Let's check that the remote site didn't already pingback this entry
		$result = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = '$post_ID' AND comment_author_url = '$pagelinkedfrom'");

		if ( $wpdb->num_rows ) // We already have a Pingback from this URL
	  		return new IXR_Error(48, 'The pingback has already been registered.');

		// very stupid, but gives time to the 'from' server to publish !
		sleep(1);

		// Let's check the remote site
		$linea = wp_remote_fopen( $pagelinkedfrom );
		if ( !$linea )
	  		return new IXR_Error(16, 'The source URL does not exist.');

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

		$preg_target = preg_quote($pagelinkedto);

		foreach ( $p as $para ) {
			if ( strpos($para, $pagelinkedto) !== false ) { // it exists, but is it a link?
				preg_match("|<a[^>]+?".$preg_target."[^>]*>([^>]+?)</a>|", $para, $context);

				// If the URL isn't in a link context, keep looking
				if ( empty($context) )
					continue;

				// We're going to use this fake tag to mark the context in a bit
				// the marker is needed in case the link text appears more than once in the paragraph
				$excerpt = preg_replace('|\</?wpcontext\>|', '', $para);

				// prevent really long link text
				if ( strlen($context[1]) > 100 )
					$context[1] = substr($context[1], 0, 100) . '...';

				$marker = '<wpcontext>'.$context[1].'</wpcontext>';    // set up our marker
				$excerpt= str_replace($context[0], $marker, $excerpt); // swap out the link for our marker
				$excerpt = strip_tags($excerpt, '<wpcontext>');        // strip all tags but our context marker
				$excerpt = trim($excerpt);
				$preg_marker = preg_quote($marker);
				$excerpt = preg_replace("|.*?\s(.{0,100}$preg_marker.{0,100})\s.*|s", '$1', $excerpt);
				$excerpt = strip_tags($excerpt); // YES, again, to remove the marker wrapper
				break;
			}
		}

		if ( empty($context) ) // Link to target not found
			return new IXR_Error(17, 'The source URL does not contain a link to the target URL, and so cannot be used as a source.');

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

		$comment_ID = wp_new_comment($commentdata);
		do_action('pingback_post', $comment_ID);

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
	  		return new IXR_Error(33, 'The specified target URL cannot be used as a target. It either doesn\'t exist, or it is not a pingback-enabled resource.');
		}

		$actual_post = wp_get_single_post($post_ID, ARRAY_A);

		if (!$actual_post) {
			// No such post = resource not found
	  		return new IXR_Error(32, 'The specified target URL does not exist.');
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
