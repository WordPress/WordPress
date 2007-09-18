<?php
/*
 * wp-app.php - Atom Publishing Protocol support for WordPress
 * Original code by: Elias Torres, http://torrez.us/archives/2006/08/31/491/
 * Modified by: Dougal Campbell, http://dougal.gunters.org/
 *
 * Version: 1.0.5-dc
 */

define('APP_REQUEST', true);

require_once('./wp-config.php');
require_once(ABSPATH . WPINC . '/post-template.php');
require_once(ABSPATH . WPINC . '/atomlib.php');

$_SERVER['PATH_INFO'] = preg_replace( '/.*\/wp-app\.php/', '', $_SERVER['REQUEST_URI'] );

$app_logging = 0;

// TODO: Should be an option somewhere
$always_authenticate = 1;

function log_app($label,$msg) {
	global $app_logging;
	if ($app_logging) {
		$fp = fopen( 'wp-app.log', 'a+');
		$date = gmdate( 'Y-m-d H:i:s' );
		fwrite($fp, "\n\n$date - $label\n$msg\n");
		fclose($fp);
	}
}

if ( !function_exists('wp_set_current_user') ) :
function wp_set_current_user($id, $name = '') {
	global $current_user;

	if ( isset($current_user) && ($id == $current_user->ID) )
		return $current_user;

	$current_user = new WP_User($id, $name);

	return $current_user;
}
endif;

function wa_posts_where_include_drafts_filter($where) {
        $where = str_replace("post_status = 'publish'","post_status = 'publish' OR post_status = 'future' OR post_status = 'draft' OR post_status = 'inherit'", $where);
        return $where;

}
add_filter('posts_where', 'wa_posts_where_include_drafts_filter');

class AtomServer {

	var $ATOM_CONTENT_TYPE = 'application/atom+xml';
	var $CATEGORIES_CONTENT_TYPE = 'application/atomcat+xml';
	var $SERVICE_CONTENT_TYPE = 'application/atomsvc+xml';

	var $ATOM_NS = 'http://www.w3.org/2005/Atom';
	var $ATOMPUB_NS = 'http://www.w3.org/2007/app';

	var $ENTRIES_PATH = "posts";
	var $CATEGORIES_PATH = "categories";
	var $MEDIA_PATH = "attachments";
	var $ENTRY_PATH = "post";
	var $SERVICE_PATH = "service";
	var $MEDIA_SINGLE_PATH = "attachment";

	var $params = array();
	var $script_name = "wp-app.php";
	var $media_content_types = array('image/*','audio/*','video/*');
	var $atom_content_types = array('application/atom+xml');

	var $selectors = array();

	// support for head
	var $do_output = true;

	function AtomServer() {

		$this->script_name = array_pop(explode('/',$_SERVER['SCRIPT_NAME']));

		$this->selectors = array(
			'@/service$@' =>
				array('GET' => 'get_service'),
			'@/categories$@' =>
				array('GET' => 'get_categories_xml'),
			'@/post/(\d+)$@' =>
				array('GET' => 'get_post',
						'PUT' => 'put_post',
						'DELETE' => 'delete_post'),
			'@/posts/?(\d+)?$@' =>
				array('GET' => 'get_posts',
						'POST' => 'create_post'),
			'@/attachments/?(\d+)?$@' =>
				array('GET' => 'get_attachment',
						'POST' => 'create_attachment'),
			'@/attachment/file/(\d+)$@' =>
				array('GET' => 'get_file',
						'PUT' => 'put_file',
						'DELETE' => 'delete_file'),
			'@/attachment/(\d+)$@' =>
				array('GET' => 'get_attachment',
						'PUT' => 'put_attachment',
						'DELETE' => 'delete_attachment'),
		);
	}

	function handle_request() {
		global $always_authenticate;

		$path = $_SERVER['PATH_INFO'];
		$method = $_SERVER['REQUEST_METHOD'];

		log_app('REQUEST',"$method $path\n================");

		$this->process_conditionals();
		//$this->process_conditionals();

		// exception case for HEAD (treat exactly as GET, but don't output)
		if($method == 'HEAD') {
			$this->do_output = false;
			$method = 'GET';
		}

		// redirect to /service in case no path is found.
		if(strlen($path) == 0 || $path == '/') {
			$this->redirect($this->get_service_url());
		}

		// dispatch
		foreach($this->selectors as $regex => $funcs) {
			if(preg_match($regex, $path, $matches)) {
			if(isset($funcs[$method])) {

				// authenticate regardless of the operation and set the current
				// user. each handler will decide if auth is required or not.
				$this->authenticate();
				$u = wp_get_current_user();
				if(!isset($u) || $u->ID == 0) {
					if ($always_authenticate) {
						$this->auth_required('Credentials required.');
					}
				}

				array_shift($matches);
				call_user_func_array(array(&$this,$funcs[$method]), $matches);
				exit();
			} else {
				// only allow what we have handlers for...
				$this->not_allowed(array_keys($funcs));
			}
			}
		}

		// oops, nothing found
		$this->not_found();
	}

	function get_service() {
		log_app('function','get_service()');
		$entries_url = attribute_escape($this->get_entries_url());
		$categories_url = attribute_escape($this->get_categories_url());
		$media_url = attribute_escape($this->get_attachments_url());
                foreach ($this->media_content_types as $med) {
                  $accepted_media_types = $accepted_media_types . "<accept>" . $med . "</accept>";
                }
		$atom_prefix="atom";
		$service_doc = <<<EOD
<service xmlns="$this->ATOMPUB_NS" xmlns:$atom_prefix="$this->ATOM_NS">
  <workspace>
    <$atom_prefix:title>WordPress Workspace</$atom_prefix:title>
    <collection href="$entries_url">
      <$atom_prefix:title>WordPress Posts</$atom_prefix:title>
      <accept>$this->ATOM_CONTENT_TYPE;type=entry</accept>
      <categories href="$categories_url" />
    </collection>
    <collection href="$media_url">
      <$atom_prefix:title>WordPress Media</$atom_prefix:title>
      $accepted_media_types
    </collection>
  </workspace>
</service>

EOD;

		$this->output($service_doc, $this->SERVICE_CONTENT_TYPE);
	}

	function get_categories_xml() {

		log_app('function','get_categories_xml()');
		$home = attribute_escape(get_bloginfo_rss('home'));

		$categories = "";
		$cats = get_categories("hierarchical=0&hide_empty=0");
		foreach ((array) $cats as $cat) {
			$categories .= "    <category term=\"" . attribute_escape($cat->name) .  "\" />\n";
}
		$output = <<<EOD
<app:categories xmlns:app="$this->ATOMPUB_NS"
	xmlns="$this->ATOM_NS"
	fixed="yes" scheme="$home">
	$categories
</app:categories>
EOD;
	$this->output($output, $this->CATEGORIES_CONTENT_TYPE);
}

	/*
	 * Create Post (No arguments)
	 */
	function create_post() {
		global $blog_id, $wpdb;
		$this->get_accepted_content_type($this->atom_content_types);

		$parser = new AtomParser();
		if(!$parser->parse()) {
			$this->client_error();
		}

		$entry = array_pop($parser->feed->entries);

		log_app('Received entry:', print_r($entry,true));

		$catnames = array();
		foreach($entry->categories as $cat)
			array_push($catnames, $cat["term"]);

		$wp_cats = get_categories(array('hide_empty' => false));

		$post_category = array();

		foreach($wp_cats as $cat) {
			if(in_array($cat->name, $catnames))
				array_push($post_category, $cat->term_id);
		}

		$publish = (isset($entry->draft) && trim($entry->draft) == 'yes') ? false : true;

		$cap = ($publish) ? 'publish_posts' : 'edit_posts';

		if(!current_user_can($cap))
			$this->auth_required(__('Sorry, you do not have the right to edit/publish new posts.'));

		$blog_ID = (int ) $blog_id;
		$post_status = ($publish) ? 'publish' : 'draft';
		$post_author = (int) $user->ID;
		$post_title = $entry->title[1];
		$post_content = $entry->content[1];
		$post_excerpt = $entry->summary[1];
		$pubtimes = $this->get_publish_time($entry);
		$post_date = $pubtimes[0];
		$post_date_gmt = $pubtimes[1];

		if ( isset( $_SERVER['HTTP_SLUG'] ) )
			$post_name = $_SERVER['HTTP_SLUG'];

		$post_data = compact('blog_ID', 'post_author', 'post_date', 'post_date_gmt', 'post_content', 'post_title', 'post_category', 'post_status', 'post_excerpt', 'post_name');

		$this->escape($post_data);
		log_app('Inserting Post. Data:', print_r($post_data,true));

		$postID = wp_insert_post($post_data);
		if ( is_wp_error( $postID ) )
			$this->internal_error($postID->get_error_message());

		if (!$postID) {
			$this->internal_error(__('Sorry, your entry could not be posted. Something wrong happened.'));
		}

		// getting warning here about unable to set headers
		// because something in the cache is printing to the buffer
		// could we clean up wp_set_post_categories or cache to not print
		// this could affect our ability to send back the right headers
		@wp_set_post_categories($postID, $post_category);

		$output = $this->get_entry($postID);

		log_app('function',"create_post($postID)");
		$this->created($postID, $output);
	}

	function get_post($postID) {

		global $entry;
		$this->set_current_entry($postID);
		$output = $this->get_entry($postID);
		log_app('function',"get_post($postID)");
		$this->output($output);

	}

	function put_post($postID) {
		global $wpdb;

		// checked for valid content-types (atom+xml)
		// quick check and exit
		$this->get_accepted_content_type($this->atom_content_types);

		$parser = new AtomParser();
		if(!$parser->parse()) {
			$this->bad_request();
		}

		$parsed = array_pop($parser->feed->entries);

		log_app('Received UPDATED entry:', print_r($parsed,true));

		// check for not found
		global $entry;
		$entry = $GLOBALS['entry'];
		$this->set_current_entry($postID);

		if(!current_user_can('edit_post', $entry['ID']))
			$this->auth_required(__('Sorry, you do not have the right to edit this post.'));

		$publish = (isset($parsed->draft) && trim($parsed->draft) == 'yes') ? false : true;

		extract($entry);

		$post_title = $parsed->title[1];
		$post_content = $parsed->content[1];
		$post_excerpt = $parsed->summary[1];
		$pubtimes = $this->get_publish_time($entry);
		$post_date = $pubtimes[0];
		$post_date_gmt = $pubtimes[1];

		// let's not go backwards and make something draft again.
		if(!$publish && $post_status == 'draft') {
			$post_status = ($publish) ? 'publish' : 'draft';
		} elseif($publish) {
			$post_status = 'publish';
		}

		$postdata = compact('ID', 'post_content', 'post_title', 'post_category', 'post_status', 'post_excerpt', 'post_date', 'post_date_gmt');
		$this->escape($postdata);

		$result = wp_update_post($postdata);

		if (!$result) {
			$this->internal_error(__('For some strange yet very annoying reason, this post could not be edited.'));
		}

		log_app('function',"put_post($postID)");
		$this->ok();
	}

	function delete_post($postID) {

		// check for not found
		global $entry;
		$this->set_current_entry($postID);

		if(!current_user_can('edit_post', $postID)) {
			$this->auth_required(__('Sorry, you do not have the right to delete this post.'));
		}

		if ($entry['post_type'] == 'attachment') {
			$this->delete_attachment($postID);
		} else {
			$result = wp_delete_post($postID);

			if (!$result) {
				$this->internal_error(__('For some strange yet very annoying reason, this post could not be deleted.'));
			}

			log_app('function',"delete_post($postID)");
			$this->ok();
		}

	}

	function get_attachment($postID = NULL) {

		global $entry;
		if (!isset($postID)) {
			$this->get_attachments();
		} else {
			$this->set_current_entry($postID);
			$output = $this->get_entry($postID, 'attachment');
			log_app('function',"get_attachment($postID)");
			$this->output($output);
		}
	}

	function create_attachment() {
		global $wp, $wpdb, $wp_query, $blog_id;

		$type = $this->get_accepted_content_type();

		if(!current_user_can('upload_files'))
			$this->auth_required(__('You do not have permission to upload files.'));

		$fp = fopen("php://input", "rb");
		$bits = NULL;
		while(!feof($fp)) {
			$bits .= fread($fp, 4096);
		}
		fclose($fp);

		$slug = '';
		if ( isset( $_SERVER['HTTP_SLUG'] ) )
			$slug = sanitize_file_name( $_SERVER['HTTP_SLUG'] );
		elseif ( isset( $_SERVER['HTTP_TITLE'] ) )
			$slug = sanitize_file_name( $_SERVER['HTTP_TITLE'] );
		elseif ( empty( $slug ) ) // just make a random name
			$slug = substr( md5( uniqid( microtime() ) ), 0, 7);
		$ext = preg_replace( '|.*/([a-z]+)|', '$1', $_SERVER['CONTENT_TYPE'] );
		$slug = "$slug.$ext";
		$file = wp_upload_bits( $slug, NULL, $bits);

		log_app('wp_upload_bits returns:',print_r($file,true));

		$url = $file['url'];
		$file = $file['file'];
		$filename = basename($file);

		$header = apply_filters('wp_create_file_in_uploads', $file); // replicate

		// Construct the attachment array
		$attachment = array(
			'post_title' => $slug,
			'post_content' => $slug,
			'post_status' => 'attachment',
			'post_parent' => 0,
			'post_mime_type' => $type,
			'guid' => $url
			);

		// Save the data
		$postID = wp_insert_attachment($attachment, $file, $post);

		if (!$postID) {
			$this->internal_error(__('Sorry, your entry could not be posted. Something wrong happened.'));
		}

		$output = $this->get_entry($postID, 'attachment');

		$this->created($postID, $output, 'attachment');
		log_app('function',"create_attachment($postID)");
	}

	function put_attachment($postID) {
		global $wpdb;

		// checked for valid content-types (atom+xml)
		// quick check and exit
		$this->get_accepted_content_type($this->atom_content_types);

		$parser = new AtomParser();
		if(!$parser->parse()) {
			$this->bad_request();
		}

		$parsed = array_pop($parser->feed->entries);

		// check for not found
		global $entry;
		$this->set_current_entry($postID);

		if(!current_user_can('edit_post', $entry['ID']))
			$this->auth_required(__('Sorry, you do not have the right to edit this post.'));

		$publish = (isset($parsed->draft) && trim($parsed->draft) == 'yes') ? false : true;

		extract($entry);

		$post_title = $parsed->title[1];
		$post_content = $parsed->content[1];

		$postdata = compact('ID', 'post_content', 'post_title', 'post_category', 'post_status', 'post_excerpt');
		$this->escape($postdata);

		$result = wp_update_post($postdata);

		if (!$result) {
			$this->internal_error(__('For some strange yet very annoying reason, this post could not be edited.'));
		}

		log_app('function',"put_attachment($postID)");
		$this->ok();
	}

	function delete_attachment($postID) {
		log_app('function',"delete_attachment($postID). File '$location' deleted.");

		// check for not found
		global $entry;
		$this->set_current_entry($postID);

		if(!current_user_can('edit_post', $postID)) {
			$this->auth_required(__('Sorry, you do not have the right to delete this post.'));
		}

		$location = get_post_meta($entry['ID'], '_wp_attached_file', true);

		// delete file
		@unlink($location);

		// delete attachment
		$result = wp_delete_post($postID);

		if (!$result) {
			$this->internal_error(__('For some strange yet very annoying reason, this post could not be deleted.'));
		}

		log_app('function',"delete_attachment($postID). File '$location' deleted.");
		$this->ok();
	}

	function get_file($postID) {

		// check for not found
		global $entry;
		$this->set_current_entry($postID);

		// then whether user can edit the specific post
		if(!current_user_can('edit_post', $postID)) {
			$this->auth_required(__('Sorry, you do not have the right to edit this post.'));
		}

		$location = get_post_meta($entry['ID'], '_wp_attached_file', true);
		$filetype = wp_check_filetype($location);

		if(!isset($location) || 'attachment' != $entry['post_type'] || empty($filetype['ext']))
			$this->internal_error(__('Error ocurred while accessing post metadata for file location.'));

		status_header('200');
		header('Content-Type: ' . $entry['post_mime_type']);
		header('Connection: close');

		$fp = fopen($location, "rb");
		while(!feof($fp)) {
			echo fread($fp, 4096);
		}
		fclose($fp);

		log_app('function',"get_file($postID)");
		exit;
	}

	function put_file($postID) {

		$type = $this->get_accepted_content_type();

		// first check if user can upload
		if(!current_user_can('upload_files'))
			$this->auth_required(__('You do not have permission to upload files.'));

		// check for not found
		global $entry;
		$this->set_current_entry($postID);

		// then whether user can edit the specific post
		if(!current_user_can('edit_post', $postID)) {
			$this->auth_required(__('Sorry, you do not have the right to edit this post.'));
		}

		$location = get_post_meta($entry['ID'], '_wp_attached_file', true);
		$filetype = wp_check_filetype($location);

		if(!isset($location) || 'attachment' != $entry['post_type'] || empty($filetype['ext']))
			$this->internal_error(__('Error ocurred while accessing post metadata for file location.'));

		$fp = fopen("php://input", "rb");
		$localfp = fopen($location, "w+");
		while(!feof($fp)) {
			fwrite($localfp,fread($fp, 4096));
		}
		fclose($fp);
		fclose($localfp);

		$ID = $entry['ID'];
		$pubtimes = $this->get_publish_time($entry);
		$post_date = $pubtimes[0];
		$post_date_gmt = $pubtimes[1];

		$post_data = compact('ID', 'post_date', 'post_date_gmt');
		$result = wp_update_post($post_data);

		if (!$result) {
			$this->internal_error(__('Sorry, your entry could not be posted. Something wrong happened.'));
		}

		log_app('function',"put_file($postID)");
		$this->ok();
	}

	function get_entries_url($page = NULL) {
		if($GLOBALS['post_type'] == 'attachment') {
			$path = $this->MEDIA_PATH;
		} else {
			$path = $this->ENTRIES_PATH;
		}
		$url = get_bloginfo('url') . '/' . $this->script_name . '/' . $path;
		if(isset($page) && is_int($page)) {
			$url .= "/$page";
		}
		return $url;
	}

	function the_entries_url($page = NULL) {
		$url = $this->get_entries_url($page);
		echo $url;
	}

	function get_categories_url($page = NULL) {
		return get_bloginfo('url') . '/' . $this->script_name . '/' . $this->CATEGORIES_PATH;
	}

	function the_categories_url() {
		$url = $this->get_categories_url();
		echo $url;
	}

	function get_attachments_url($page = NULL) {
		$url = get_bloginfo('url') . '/' . $this->script_name . '/' . $this->MEDIA_PATH;
		if(isset($page) && is_int($page)) {
			$url .= "/$page";
		}
		return $url;
	}

	function the_attachments_url($page = NULL) {
		$url = $this->get_attachments_url($page);
		echo $url;
	}

	function get_service_url() {
		return get_bloginfo('url') . '/' . $this->script_name . '/' . $this->SERVICE_PATH;
	}

	function get_entry_url($postID = NULL) {
		if(!isset($postID)) {
			global $post;
			$postID = (int) $GLOBALS['post']->ID;
		}

		$url = get_bloginfo('url') . '/' . $this->script_name . '/' . $this->ENTRY_PATH . "/$postID";

		log_app('function',"get_entry_url() = $url");
		return $url;
	}

	function the_entry_url($postID = NULL) {
		$url = $this->get_entry_url($postID);
		echo $url;
	}

	function get_media_url($postID = NULL) {
		if(!isset($postID)) {
			global $post;
			$postID = (int) $GLOBALS['post']->ID;
		}

		$url = get_bloginfo('url') . '/' . $this->script_name . '/' . $this->MEDIA_SINGLE_PATH ."/file/$postID";

		log_app('function',"get_media_url() = $url");
		return $url;
	}

	function the_media_url($postID = NULL) {
		$url = $this->get_media_url($postID);
		echo $url;
	}

	function set_current_entry($postID) {
		global $entry;
		log_app('function',"set_current_entry($postID)");

		if(!isset($postID)) {
			// $this->bad_request();
			$this->not_found();
		}

		$entry = wp_get_single_post($postID,ARRAY_A);

		if(!isset($entry) || !isset($entry['ID']))
			$this->not_found();

		return;
	}

	function get_posts($page = 1, $post_type = 'post') {
			log_app('function',"get_posts($page, '$post_type')");
			$feed = $this->get_feed($page, $post_type);
			$this->output($feed);
	}

	function get_attachments($page = 1, $post_type = 'attachment') {
	    log_app('function',"get_attachments($page, '$post_type')");
	    $GLOBALS['post_type'] = $post_type;
	    $feed = $this->get_feed($page, $post_type);
	    $this->output($feed);
	}

	function get_feed($page = 1, $post_type = 'post') {
		global $post, $wp, $wp_query, $posts, $wpdb, $blog_id, $post_cache;
		log_app('function',"get_feed($page, '$post_type')");
		ob_start();

		if(!isset($page)) {
			$page = 1;
		}
		$page = (int) $page;

		$count = get_option('posts_per_rss');

		wp('what_to_show=posts&posts_per_page=' . $count . '&offset=' . ($count * ($page-1) ));

		$post = $GLOBALS['post'];
		$posts = $GLOBALS['posts'];
		$wp = $GLOBALS['wp'];
		$wp_query = $GLOBALS['wp_query'];
		$wpdb = $GLOBALS['wpdb'];
		$blog_id = (int) $GLOBALS['blog_id'];
		$post_cache = $GLOBALS['post_cache'];
		log_app('function',"query_posts(# " . print_r($wp_query, true) . "#)");

		log_app('function',"total_count(# $wp_query->max_num_pages #)");
		$last_page = $wp_query->max_num_pages;
		$next_page = (($page + 1) > $last_page) ? NULL : $page + 1;
		$prev_page = ($page - 1) < 1 ? NULL : $page - 1;
		$last_page = ((int)$last_page == 1 || (int)$last_page == 0) ? NULL : (int) $last_page;
		$self_page = $page > 1 ? $page : NULL;
?><feed xmlns="<?php echo $this->ATOM_NS ?>" xmlns:app="<?php echo $this->ATOMPUB_NS ?>" xml:lang="<?php echo get_option('rss_language'); ?>">
<id><?php $this->the_entries_url() ?></id>
<updated><?php echo mysql2date('Y-m-d\TH:i:s\Z', get_lastpostmodified('GMT')); ?></updated>
<title type="text"><?php bloginfo_rss('name') ?></title>
<subtitle type="text"><?php bloginfo_rss("description") ?></subtitle>
<link rel="first" type="<?php echo $this->ATOM_CONTENT_TYPE ?>" href="<?php $this->the_entries_url() ?>" />
<?php if(isset($prev_page)): ?>
<link rel="previous" type="<?php echo $this->ATOM_CONTENT_TYPE ?>" href="<?php $this->the_entries_url($prev_page) ?>" />
<?php endif; ?>
<?php if(isset($next_page)): ?>
<link rel="next" type="<?php echo $this->ATOM_CONTENT_TYPE ?>" href="<?php $this->the_entries_url($next_page) ?>" />
<?php endif; ?>
<link rel="last" type="<?php echo $this->ATOM_CONTENT_TYPE ?>" href="<?php $this->the_entries_url($last_page) ?>" />
<link rel="self" type="<?php echo $this->ATOM_CONTENT_TYPE ?>" href="<?php $this->the_entries_url($self_page) ?>" />
<rights type="text">Copyright <?php echo mysql2date('Y', get_lastpostdate('blog')); ?></rights>
<generator uri="http://wordpress.com/" version="1.0.5-dc">WordPress.com Atom API</generator>
<?php if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				$this->echo_entry();
			}
		}
?></feed>
<?php
		$feed = ob_get_contents();
		ob_end_clean();
		return $feed;
	}

	function get_entry($postID, $post_type = 'post') {
		log_app('function',"get_entry($postID, '$post_type')");
		ob_start();
		global $posts, $post, $wp_query, $wp, $wpdb, $blog_id, $post_cache;
		switch($post_type) {
			case 'post':
				$varname = 'p';
				break;
			case 'attachment':
				$varname = 'attachment_id';
				break;
		}
		query_posts($varname . '=' . $postID);
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				$this->echo_entry();
				log_app('$post',print_r($GLOBALS['post'],true));
				$entry = ob_get_contents();
				break;
			}
		}
		ob_end_clean();

		log_app('get_entry returning:',$entry);
		return $entry;
	}

	function echo_entry() { ?>
<entry xmlns="<?php echo $this->ATOM_NS ?>"
       xmlns:app="<?php echo $this->ATOMPUB_NS ?>" xml:lang="<?php echo get_option('rss_language'); ?>">
	<id><?php the_guid($GLOBALS['post']->ID); ?></id>
<?php list($content_type, $content) = $this->prep_content(get_the_title()); ?>
	<title type="<?php echo $content_type ?>"><?php echo $content ?></title>
	<updated><?php echo get_post_modified_time('Y-m-d\TH:i:s\Z', true); ?></updated>
	<published><?php echo get_post_time('Y-m-d\TH:i:s\Z', true); ?></published>
	<app:edited><?php echo get_post_modified_time('Y-m-d\TH:i:s\Z', true); ?></app:edited>
	<app:control>
		<app:draft><?php echo ($GLOBALS['post']->post_status == 'draft' ? 'yes' : 'no') ?></app:draft>
	</app:control>
	<author>
		<name><?php the_author()?></name>
		<email><?php the_author_email()?></email>
<?php if (get_the_author_url() && get_the_author_url() != 'http://') { ?>
		<uri><?php the_author_url()?></uri>
<?php } ?>
	</author>
<?php if($GLOBALS['post']->post_type == 'attachment') { ?>
	<link rel="edit-media" href="<?php $this->the_media_url() ?>" />
	<content type="<?php echo $GLOBALS['post']->post_mime_type ?>" src="<?php the_guid(); ?>"/>
<?php } else { ?>
	<link href="<?php the_permalink_rss() ?>" />
<?php if ( strlen( $GLOBALS['post']->post_content ) ) :
list($content_type, $content) = $this->prep_content(get_the_content()); ?>
	<content type="<?php echo $content_type ?>"><?php echo $content ?></content>
<?php endif; ?>
<?php } ?>
	<link rel="edit" href="<?php $this->the_entry_url() ?>" />
<?php foreach(get_the_category() as $category) { ?>
	<category scheme="<?php bloginfo_rss('home') ?>" term="<?php echo $category->name?>" />
<?php } ?>
<?php list($content_type, $content) = $this->prep_content(get_the_excerpt()); ?>
	<summary type="<?php echo $content_type ?>"><?php echo $content ?></summary>
</entry>
<?php }

	function prep_content($data) {
		if (strpos($data, '<') === false && strpos($data, '&') === false) {
			return array('text', $data);
		}

		$parser = xml_parser_create();
		xml_parse($parser, '<div>' . $data . '</div>', true);
		$code = xml_get_error_code($parser);
		xml_parser_free($parser);

		if (!$code) {
		        if (strpos($data, '<') === false) {
			        return array('text', $data);
                        } else {
			        $data = "<div xmlns='http://www.w3.org/1999/xhtml'>$data</div>";
			        return array('xhtml', $data);
                        }
		}

		if (strpos($data, ']]>') == false) {
			return array('html', "<![CDATA[$data]]>");
		} else {
			return array('html', htmlspecialchars($data));
		}
	}

	function ok() {
		log_app('Status','200: OK');
		header('Content-Type: text/plain');
		status_header('200');
		exit;
	}

	function no_content() {
		log_app('Status','204: No Content');
		header('Content-Type: text/plain');
		status_header('204');
		echo "Deleted.";
		exit;
	}

	function internal_error($msg = 'Internal Server Error') {
		log_app('Status','500: Server Error');
		header('Content-Type: text/plain');
		status_header('500');
		echo $msg;
		exit;
	}

	function bad_request() {
		log_app('Status','400: Bad Request');
		header('Content-Type: text/plain');
		status_header('400');
		exit;
	}

	function length_required() {
		log_app('Status','411: Length Required');
		header("HTTP/1.1 411 Length Required");
		header('Content-Type: text/plain');
		status_header('411');
		exit;
	}

	function invalid_media() {
		log_app('Status','415: Unsupported Media Type');
		header("HTTP/1.1 415 Unsupported Media Type");
		header('Content-Type: text/plain');
		exit;
	}

	function not_found() {
		log_app('Status','404: Not Found');
		header('Content-Type: text/plain');
		status_header('404');
		exit;
	}

	function not_allowed($allow) {
		log_app('Status','405: Not Allowed');
		header('Allow: ' . join(',', $allow));
		status_header('405');
		exit;
	}

	function redirect($url) {

		log_app('Status','302: Redirect');
		$escaped_url = attribute_escape($url);
		$content = <<<EOD
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html>
  <head>
    <title>302 Found</title>
  </head>
<body>
  <h1>Found</h1>
  <p>The document has moved <a href="$escaped_url">here</a>.</p>
  </body>
</html>

EOD;
		header('HTTP/1.1 302 Moved');
		header('Content-Type: text/html');
		header('Location: ' . $url);
		echo $content;
		exit;

	}


	function client_error($msg = 'Client Error') {
		log_app('Status','400: Client Error');
		header('Content-Type: text/plain');
		status_header('400');
		exit;
	}

	function created($post_ID, $content, $post_type = 'post') {
		log_app('created()::$post_ID',"$post_ID, $post_type");
		$edit = $this->get_entry_url($post_ID);
		switch($post_type) {
			case 'post':
				$ctloc = $this->get_entry_url($post_ID);
				break;
			case 'attachment':
				$edit = get_bloginfo('url') . '/' . $this->script_name . "/attachments/$post_ID";
				break;
		}
		header("Content-Type: $this->ATOM_CONTENT_TYPE");
		if(isset($ctloc))
			header('Content-Location: ' . $ctloc);
		header('Location: ' . $edit);
		status_header('201');
		echo $content;
		exit;
	}

	function auth_required($msg) {
		log_app('Status','401: Auth Required');
		nocache_headers();
		header('WWW-Authenticate: Basic realm="WordPress Atom Protocol"');
		header("HTTP/1.1 401 $msg");
		header('Status: ' . $msg);
		header('Content-Type: text/html');
		$content = <<<EOD
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html>
  <head>
    <title>401 Unauthorized</title>
  </head>
<body>
    <h1>401 Unauthorized</h1>
    <p>$msg</p>
  </body>
</html>

EOD;
		echo $content;
		exit;
	}

	function output($xml, $ctype = 'application/atom+xml') {
			status_header('200');
			$xml = '<?xml version="1.0" encoding="' . strtolower(get_option('blog_charset')) . '"?>'."\n".$xml;
			header('Connection: close');
			header('Content-Length: '. strlen($xml));
			header('Content-Type: ' . $ctype);
			header('Content-Disposition: attachment; filename=atom.xml');
			header('Date: '. date('r'));
			if($this->do_output)
				echo $xml;
			log_app('function', "output:\n$xml");
			exit;
	}

	function escape(&$array) {
		global $wpdb;

		foreach ($array as $k => $v) {
				if (is_array($v)) {
						$this->escape($array[$k]);
				} else if (is_object($v)) {
						//skip
				} else {
						$array[$k] = $wpdb->escape($v);
				}
		}
	}

	/*
	 * Access credential through various methods and perform login
	 */
	function authenticate() {
		$login_data = array();
		$already_md5 = false;

		log_app("authenticate()",print_r($_ENV, true));

		// if using mod_rewrite/ENV hack
		// http://www.besthostratings.com/articles/http-auth-php-cgi.html
		if(isset($_SERVER['HTTP_AUTHORIZATION'])) {
			list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) =
				explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
		}

		// If Basic Auth is working...
		if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
			$login_data = array('login' => $_SERVER['PHP_AUTH_USER'],	'password' => $_SERVER['PHP_AUTH_PW']);
			log_app("Basic Auth",$login_data['login']);
		} else {
			// else, do cookie-based authentication
			if (function_exists('wp_get_cookie_login')) {
				$login_data = wp_get_cookie_login();
				$already_md5 = true;
			}
		}

		// call wp_login and set current user
		if (!empty($login_data) && wp_login($login_data['login'], $login_data['password'], $already_md5)) {
			 $current_user = new WP_User(0, $login_data['login']);
			 wp_set_current_user($current_user->ID);
			log_app("authenticate()",$login_data['login']);
		}
	}

	function get_accepted_content_type($types = NULL) {

		if(!isset($types)) {
			$types = $this->media_content_types;
		}

		if(!isset($_SERVER['CONTENT_LENGTH']) || !isset($_SERVER['CONTENT_TYPE'])) {
			$this->length_required();
		}

		$type = $_SERVER['CONTENT_TYPE'];
		list($type,$subtype) = explode('/',$type);
		list($subtype) = explode(";",$subtype); // strip MIME parameters
		log_app("get_accepted_content_type", "type=$type, subtype=$subtype");

		foreach($types as $t) {
			list($acceptedType,$acceptedSubtype) = explode('/',$t);
			if($acceptedType == '*' || $acceptedType == $type) {
				if($acceptedSubtype == '*' || $acceptedSubtype == $subtype)
					return $type . "/" . $subtype;
			}
		}

		$this->invalid_media();
	}

	function process_conditionals() {

		if(empty($this->params)) return;
		if($_SERVER['REQUEST_METHOD'] == 'DELETE') return;

		switch($this->params[0]) {
			case $this->ENTRY_PATH:
				global $post;
				$post = wp_get_single_post($this->params[1]);
				$wp_last_modified = get_post_modified_time('D, d M Y H:i:s', true);
				$post = NULL;
				break;
			case $this->ENTRIES_PATH:
				$wp_last_modified = mysql2date('D, d M Y H:i:s', get_lastpostmodified('GMT'), 0).' GMT';
				break;
			default:
				return;
		}
		$wp_etag = md5($wp_last_modified);
		@header("Last-Modified: $wp_last_modified");
		@header("ETag: $wp_etag");

		// Support for Conditional GET
		if (isset($_SERVER['HTTP_IF_NONE_MATCH']))
			$client_etag = stripslashes($_SERVER['HTTP_IF_NONE_MATCH']);
		else
			$client_etag = false;

		$client_last_modified = trim( $_SERVER['HTTP_IF_MODIFIED_SINCE']);
		// If string is empty, return 0. If not, attempt to parse into a timestamp
		$client_modified_timestamp = $client_last_modified ? strtotime($client_last_modified) : 0;

		// Make a timestamp for our most recent modification...
		$wp_modified_timestamp = strtotime($wp_last_modified);

		if ( ($client_last_modified && $client_etag) ?
		(($client_modified_timestamp >= $wp_modified_timestamp) && ($client_etag == $wp_etag)) :
		(($client_modified_timestamp >= $wp_modified_timestamp) || ($client_etag == $wp_etag)) ) {
			status_header( 304 );
			exit;
		}
	}

	function rfc3339_str2time($str) {

	    $match = false;
	    if(!preg_match("/(\d{4}-\d{2}-\d{2})T(\d{2}\:\d{2}\:\d{2})\.?\d{0,3}(Z|[+-]+\d{2}\:\d{2})/", $str, $match))
			return false;

	    if($match[3] == 'Z')
			$match[3] == '+0000';

	    return strtotime($match[1] . " " . $match[2] . " " . $match[3]);
	}

	function get_publish_time($entry) {

	    $pubtime = $this->rfc3339_str2time($entry->published);

	    if(!$pubtime) {
			return array(current_time('mysql'),current_time('mysql',1));
	    } else {
			return array(date("Y-m-d H:i:s", $pubtime), gmdate("Y-m-d H:i:s", $pubtime));
	    }
	}

}

$server = new AtomServer();
$server->handle_request();

?>
