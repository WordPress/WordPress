<?php
/* 
 * app.php - Atom Publishing Protocol support for WordPress
 * Original code by: Elias Torres, http://torrez.us/archives/2006/08/31/491/
 * Modified by: Dougal Campbell, http://dougal.gunters.org/
 *
 * Version: 1.0.5-dc
 */

define('APP_REQUEST', true);

require_once('wp-config.php');
require_once('wp-includes/post-template.php');

// Attempt to automatically detect whether to use querystring
// or PATH_INFO, based on our environment:
$use_querystring = $wp_version == 'MU' ? 1 : 0;

// If using querystring, we need to put the path together manually:
if ($use_querystring) {
	$GLOBALS['use_querystring'] = $use_querystring;
	$action = $_GET['action'];
	$eid = (int) $_GET['eid'];

	$_SERVER['PATH_INFO'] = $action;

	if ($eid) {
		$_SERVER['PATH_INFO'] .= "/$eid";
	}
} else {
	$_SERVER['PATH_INFO'] = str_replace( '/app.php', '', $_SERVER['REQUEST_URI'] );
}

$app_logging = 0;

function log_app($label,$msg) {
	global $app_logging;
	if ($app_logging) {
		$fp = fopen( 'app.log', 'a+');
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
	$where = ereg_replace("post_author = ([0-9]+) AND post_status != 'draft'","post_author = \\1 AND post_status = 'draft'", $where);
	return $where;
}
add_filter('posts_where', 'wa_posts_where_include_drafts_filter');

class AtomEntry {
	var $links = array();
	var $categories = array();
}

class AtomParser {

	var $ATOM_CONTENT_ELEMENTS = array('content','summary','title','subtitle','rights');
	var $ATOM_SIMPLE_ELEMENTS = array('id','updated','published','draft');

	var $depth = 0;
	var $indent = 2;
	var $in_content;
	var $ns_contexts = array();
	var $ns_decls = array();
	var $is_xhtml = false;
	var $skipped_div = false;

	var $entry;

	function AtomParser() {

		$this->entry = new AtomEntry();
		$this->map_attrs_func = create_function('$k,$v', 'return "$k=\"$v\"";');
		$this->map_xmlns_func = create_function('$p,$n', '$xd = "xmlns"; if(strlen($n[0])>0) $xd .= ":{$n[0]}"; return "{$xd}=\"{$n[1]}\"";');
	}

	function parse() {

		global $app_logging;
		array_unshift($this->ns_contexts, array());

		$parser = xml_parser_create_ns();
		xml_set_object($parser, $this);
		xml_set_element_handler($parser, "start_element", "end_element");
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,0);
		xml_set_character_data_handler($parser, "cdata");
		xml_set_default_handler($parser, "_default");
		xml_set_start_namespace_decl_handler($parser, "start_ns");
		xml_set_end_namespace_decl_handler($parser, "end_ns");

		$contents = "";

		$fp = fopen("php://input", "r");
		while(!feof($fp)) {
			$line = fgets($fp, 4096);
		 
			if($app_logging) $contents .= $line;

			if(!xml_parse($parser, $line)) {
				log_app("xml_parse_error", "line: $line");
				$this->error = sprintf(__('XML error: %s at line %d')."\n",
					xml_error_string(xml_get_error_code($xml_parser)),
					xml_get_current_line_number($xml_parser));
				log_app("xml_parse_error", $this->error);
				return false;
			}
		}
		fclose($fp);

		xml_parser_free($parser);

		log_app("AtomParser->parse()",trim($contents));

		return true;
	}

	function start_element($parser, $name, $attrs) {

		$tag = array_pop(split(":", $name));

		array_unshift($this->ns_contexts, $this->ns_decls);

		$this->depth++;

		#print str_repeat(" ", $this->depth * $this->indent) . "start_element('$name')" ."\n";
		#print str_repeat(" ", $this->depth+1 * $this->indent) . print_r($this->ns_contexts,true) ."\n";

		if(!empty($this->in_content)) {
			$attrs_prefix = array();

			// resolve prefixes for attributes
			foreach($attrs as $key => $value) {
				$attrs_prefix[$this->ns_to_prefix($key)] = $this->xml_escape($value);
			}
			$attrs_str = join(' ', array_map($this->map_attrs_func, array_keys($attrs_prefix), array_values($attrs_prefix)));
			if(strlen($attrs_str) > 0) {
				$attrs_str = " " . $attrs_str;
			}

			$xmlns_str = join(' ', array_map($this->map_xmlns_func, array_keys($this->ns_contexts[0]), array_values($this->ns_contexts[0])));
			if(strlen($xmlns_str) > 0) {
				$xmlns_str = " " . $xmlns_str;
			}

			// handle self-closing tags (case: a new child found right-away, no text node)
			if(count($this->in_content) == 2) {
				array_push($this->in_content, ">");
			}
		 
			array_push($this->in_content, "<". $this->ns_to_prefix($name) ."{$xmlns_str}{$attrs_str}");
		} else if(in_array($tag, $this->ATOM_CONTENT_ELEMENTS) || in_array($tag, $this->ATOM_SIMPLE_ELEMENTS)) {
			$this->in_content = array();
			$this->is_xhtml = $attrs['type'] == 'xhtml'; 
			array_push($this->in_content, array($tag,$this->depth));
		} else if($tag == 'link') {
			array_push($this->entry->links, $attrs);
		} else if($tag == 'category') {
			array_push($this->entry->categories, $attrs);
		}

		$this->ns_decls = array();
	}

	function end_element($parser, $name) {

		$tag = array_pop(split(":", $name));

		if(!empty($this->in_content)) {
			if($this->in_content[0][0] == $tag && 
			$this->in_content[0][1] == $this->depth) {
				array_shift($this->in_content);
				if($this->is_xhtml) {
					$this->in_content = array_slice($this->in_content, 2, count($this->in_content)-3);
				}
				$this->entry->$tag = join('',$this->in_content);
				$this->in_content = array();
			} else {
				$endtag = $this->ns_to_prefix($name);
				if (strpos($this->in_content[count($this->in_content)-1], '<' . $endtag) !== false) {
					array_push($this->in_content, "/>");
				} else {
					array_push($this->in_content, "</$endtag>");
				}
			}
		}

		array_shift($this->ns_contexts);

		#print str_repeat(" ", $this->depth * $this->indent) . "end_element('$name')" ."\n";

		$this->depth--;
	}

	function start_ns($parser, $prefix, $uri) {
		#print str_repeat(" ", $this->depth * $this->indent) . "starting: " . $prefix . ":" . $uri . "\n";
		array_push($this->ns_decls, array($prefix,$uri));
	}

	function end_ns($parser, $prefix) {
		#print str_repeat(" ", $this->depth * $this->indent) . "ending: #" . $prefix . "#\n";
	}

	function cdata($parser, $data) {
		#print str_repeat(" ", $this->depth * $this->indent) . "data: #" . $data . "#\n";
		if(!empty($this->in_content)) {
			// handle self-closing tags (case: text node found, need to close element started)
			if (strpos($this->in_content[count($this->in_content)-1], '<') !== false) {
				array_push($this->in_content, ">");
			}
			array_push($this->in_content, $this->xml_escape($data));
		}
	}

	function _default($parser, $data) {
		# when does this gets called?
	}


	function ns_to_prefix($qname) {
		$components = split(":", $qname);
		$name = array_pop($components);

		if(!empty($components)) {
			$ns = join(":",$components);
			foreach($this->ns_contexts as $context) {
				foreach($context as $mapping) {
					if($mapping[1] == $ns && strlen($mapping[0]) > 0) {
						return "$mapping[0]:$name";
					}
				}
			}
		} 
		return $name;
	}

	function xml_escape($string)
	{
			 return str_replace(array('&','"',"'",'<','>'), 
				array('&amp;','&quot;','&apos;','&lt;','&gt;'), 
				$string );
	}
}

class AtomServer {

	var $ATOM_CONTENT_TYPE = 'application/atom+xml';
	var $CATEGORIES_CONTENT_TYPE = 'application/atomcat+xml';
	var $INTROSPECTION_CONTENT_TYPE = 'application/atomserv+xml';

	var $ENTRIES_PATH = "posts";
	var $CATEGORIES_PATH = "categories";
	var $MEDIA_PATH = "attachments";
	var $ENTRY_PATH = "post";
	var $MEDIA_SINGLE_PATH = "attachment";

	var $params = array();
	var $script_name = "app.php";
	var $media_content_types = array('image/*','audio/*','video/*');
	var $atom_content_types = array('application/atom+xml');

	var $selectors = array();

	// support for head
	var $do_output = true;

	function AtomServer() {

		$this->script_name = array_pop(explode('/',$_SERVER['SCRIPT_NAME']));

		$this->selectors = array(
			'@/service@' => 
				array('GET' => 'get_service'),
			'@/categories@' =>
				array('GET' => 'get_categories_xml'),
			'@/post/(\d+)@' => 
				array('GET' => 'get_post', 
						'PUT' => 'put_post', 
						'DELETE' => 'delete_post'),
			'@/posts/?([^/]+)?@' => 
				array('GET' => 'get_posts', 
						'POST' => 'create_post'),
			'@/attachments/?(\d+)?@' => 
				array('GET' => 'get_attachment', 
						'POST' => 'create_attachment'),
			'@/attachment/file/(\d+)@' => 
				array('GET' => 'get_file', 
						'PUT' => 'put_file', 
						'DELETE' => 'delete_file'),
			'@/attachment/(\d+)@' => 
				array('GET' => 'get_attachment', 
						'PUT' => 'put_attachment', 
						'DELETE' => 'delete_attachment'),
		);
	}

	function handle_request() {

		$path = $_SERVER['PATH_INFO'];
		$method = $_SERVER['REQUEST_METHOD'];

		log_app('REQUEST',"$method $path\n================");

		//$this->process_conditionals();

		// exception case for HEAD (treat exactly as GET, but don't output)
		if($method == 'HEAD') {
			$this->do_output = false;
			$method = 'GET';
		}

		// lame. 
		if(strlen($path) == 0 || $path == '/') {
			$path = '/service';
		}

		// authenticate regardless of the operation and set the current
		// user. each handler will decide if auth is required or not.
		$this->authenticate();

		// dispatch
		foreach($this->selectors as $regex => $funcs) {
			if(preg_match($regex, $path, $matches)) {
				if(isset($funcs[$method])) {
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
		$entries_url = $this->get_entries_url();
		$categories_url = $this->get_categories_url(); 
		$media_url = $this->get_attachments_url();
		$accepted_content_types = join(',',$this->media_content_types);
		$introspection = <<<EOD
<service xmlns="http://purl.org/atom/app#" xmlns:atom="http://www.w3.org/2005/Atom"> 
	<workspace title="WordPress Workspace"> 
	    <collection href="$entries_url" title="Posts"> 
		<atom:title>WordPress Posts</atom:title> 
		<accept>entry</accept> 
		<categories href="$categories_url" /> 
	    </collection> 
	    <collection href="$media_url" title="Media"> 
		<atom:title>WordPress Media</atom:title> 
		<accept>$accepted_content_types</accept> 
	    </collection> 
	</workspace> 
</service>

EOD;

		$this->output($introspection, $this->INTROSPECTION_CONTENT_TYPE); 
	}

function get_categories_xml() {
	log_app('function','get_categories_xml()');
	$home = get_bloginfo_rss('home');

	$categories = "";
	$cats = get_categories("hierarchical=0&hide_empty=0");
	foreach ((array) $cats as $cat) {
		$categories .= "    <category term=\"" . attribute_escape($cat->cat_name) .  "\" />\n";
	}
        $output = <<<EOD
<app:categories xmlns:app="http://purl.org/atom/app#"
	xmlns="http://www.w3.org/2005/Atom"
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
		global $blog_id;
		$this->get_accepted_content_type($this->atom_content_types);

		$parser = new AtomParser();
		if(!$parser->parse()) {
			$this->client_error();
		}

		$entry = $parser->entry;

		$publish = (isset($entry->draft) && trim($entry->draft) == 'yes') ? false : true;

		$cap = ($publish) ? 'publish_posts' : 'edit_posts';

		if(!current_user_can($cap))
			$this->auth_required('Sorry, you do not have the right to edit/publish new posts.');

		$blog_ID = (int ) $blog_id;
		$post_status = ($publish) ? 'publish' : 'draft';
		$post_author = (int) $user->ID;
		$post_title = $entry->title;
		$post_content = $entry->content;
		$post_excerpt = $entry->summary;
		$post_date = current_time('mysql');
		$post_date_gmt = current_time('mysql', 1);

		$post_data = compact('blog_ID', 'post_author', 'post_date', 'post_date_gmt', 'post_content', 'post_title', 'post_category', 'post_status', 'post_excerpt');

		log_app('Inserting Post. Data:', print_r($post_data,true));

		$postID = wp_insert_post($post_data);

		if (!$postID) {
			$this->internal_error('Sorry, your entry could not be posted. Something wrong happened.');
		}

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

		// checked for valid content-types (atom+xml)
		// quick check and exit
		$this->get_accepted_content_type($this->atom_content_types);

		$parser = new AtomParser();
		if(!$parser->parse()) {
			$this->bad_request();
		}

		$parsed = $parser->entry;

		// check for not found
		global $entry;
		$entry = $GLOBALS['entry'];
		$this->set_current_entry($postID);
		$this->escape($GLOBALS['entry']);

		if(!current_user_can('edit_post', $entry['ID']))
			$this->auth_required('Sorry, you do not have the right to edit this post.');

		$publish = (isset($parsed->draft) && trim($parsed->draft) == 'yes') ? false : true;

		extract($entry);

		$post_title = $parsed->title;
		$post_content = $parsed->content;
		$post_excerpt = $parsed->summary;

		// let's not go backwards and make something draft again.
		if(!$publish && $post_status == 'draft') {
			$post_status = ($publish) ? 'publish' : 'draft';
		}

		$postdata = compact('ID', 'post_content', 'post_title', 'post_category', 'post_status', 'post_excerpt');

		$result = wp_update_post($postdata);

		if (!$result) {
			$this->internal_error('For some strange yet very annoying reason, this post could not be edited.');
		}

		log_app('function',"put_post($postID)");
		$this->ok();
	}

	function delete_post($postID) {

		// check for not found
		global $entry;
		$this->set_current_entry($postID);

		if(!current_user_can('edit_post', $postID)) {
			$this->auth_required('Sorry, you do not have the right to delete this post.');
		}

		if ($entry['post_type'] == 'attachment') {
			$this->delete_attachment($postID);
		} else {
			$result = wp_delete_post($postID);

			if (!$result) {
				$this->internal_error('For some strange yet very annoying reason, this post could not be deleted.');
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
			$this->auth_required('You do not have permission to upload files.');

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

		// checked for valid content-types (atom+xml)
		// quick check and exit
		$this->get_accepted_content_type($this->atom_content_types);

		$parser = new AtomParser();
		if(!$parser->parse()) {
			$this->bad_request();
		}

		$parsed = $parser->entry;

		// check for not found
		global $entry;
		$this->set_current_entry($postID);
		$this->escape($entry);

		if(!current_user_can('edit_post', $entry['ID']))
			$this->auth_required(__('Sorry, you do not have the right to edit this post.'));

		$publish = (isset($parsed->draft) && trim($parsed->draft) == 'yes') ? false : true;

		extract($entry);

		$post_title = $parsed->title;
		$post_content = $parsed->content;

		$postdata = compact('ID', 'post_content', 'post_title', 'post_category', 'post_status', 'post_excerpt');

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

		if(!isset($location))
			$this->internal_error(__('Error ocurred while accessing post metadata for file location.'));

		header('Content-Type: ' . $entry['post_mime_type']);

		$fp = fopen($location, "rb");
		while(!feof($fp)) {
			echo fread($fp, 4096);
		}
		fclose($fp);

		log_app('function',"get_file($postID)");
		$this->ok();
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

		if(!isset($location))
			$this->internal_error(__('Error ocurred while accessing post metadata for file location.'));

		$fp = fopen("php://input", "rb");
		$localfp = fopen($location, "w+");
		while(!feof($fp)) {
			fwrite($localfp,fread($fp, 4096));
		}
		fclose($fp);
		fclose($localfp);

		log_app('function',"put_file($postID)");
		$this->ok();
	}

	function get_entries_url($page = NULL) {
		global $use_querystring;
		$url = get_bloginfo('url') . '/' . $this->script_name;
		if ($use_querystring) {
			$url .= '?action=/' . $this->ENTRIES_PATH;
			if(isset($page) && is_int($page)) {
				$url .= "&amp;eid=$page";
			}
		} else {
			$url .= '/' . $this->ENTRIES_PATH;
			if(isset($page) && is_int($page)) {
				$url .= "/$page";
			}
		}
		return $url;
	}

	function the_entries_url($page = NULL) {
		$url = $this->get_entries_url($page);
		echo $url;
	}

	function get_categories_url($page = NULL) {
		global $use_querystring;
		$url = get_bloginfo('url') . '/' . $this->script_name;
		if ($use_querystring) {
			$url .= '?action=/' . $this->CATEGORIES_PATH;
		} else {
			$url .= '/' . $this->CATEGORIES_PATH;
		}
		return $url;
	}

	function the_categories_url() {
		$url = $this->get_categories_url();
		echo $url;
    }

	function get_attachments_url($page = NULL) {
		global $use_querystring;
		$url = get_bloginfo('url') . '/' . $this->script_name;
		if ($use_querystring) {
			$url .= '?action=/' . $this->MEDIA_PATH;
			if(isset($page) && is_int($page)) {
				$url .= "&amp;eid=$page";
			}
		} else {
			$url .= '/' . $this->MEDIA_PATH;
			if(isset($page) && is_int($page)) {
				$url .= "/$page";
			}
		}
		return $url;
	}

	function the_attachments_url($page = NULL) {
		$url = $this->get_attachments_url($page);
		echo $url;
	}


	function get_entry_url($postID = NULL) {
		global $use_querystring;
		if(!isset($postID)) {
			global $post;
			$postID = (int) $GLOBALS['post']->ID;
		}

		if ($use_querystring) {
			$url = get_bloginfo('url') . '/' . $this->script_name . '?action=/' . $this->ENTRY_PATH . "&amp;eid=$postID";
		} else {
			$url = get_bloginfo('url') . '/' . $this->script_name . '/' . $this->ENTRY_PATH . "/$postID";
		}

		log_app('function',"get_entry_url() = $url");
		return $url;
	}

	function the_entry_url($postID = NULL) {
		$url = $this->get_entry_url($postID);
		echo $url;
	}

	function get_media_url($postID = NULL) {
		global $use_querystring;
		if(!isset($postID)) {
			global $post;
			$postID = (int) $GLOBALS['post']->ID;
		}

		if ($use_querystring) {
			$url = get_bloginfo('url') . '/' . $this->script_name . '?action=/' . $this->MEDIA_SINGLE_PATH ."&amp;eid=$postID";
		} else {
			$url = get_bloginfo('url') . '/' . $this->script_name . '/' . $this->MEDIA_SINGLE_PATH ."/$postID";
		}

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

	function get_posts_count() {
		global $wpdb;
		log_app('function',"get_posts_count()");
		return $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_date_gmt < '" . gmdate("Y-m-d H:i:s",time()) . "'");
	}


	function get_posts($page = 1, $post_type = 'post') {
			log_app('function',"get_posts($page, '$post_type')");
			$feed = $this->get_feed($page, $post_type);
			$this->output($feed);
	}

	function get_attachments($page = 1, $post_type = 'attachment') {
			log_app('function',"get_attachments($page, '$post_type')");
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
		$query = "paged=$page&posts_per_page=$count&order=DESC";
		if($post_type == 'attachment') {
			$query .= "&post_type=$post_type";
		}
		query_posts($query);
		$post = $GLOBALS['post'];
		$posts = $GLOBALS['posts'];
		$wp = $GLOBALS['wp'];
		$wp_query = $GLOBALS['wp_query'];
		$wpdb = $GLOBALS['wpdb'];
		$blog_id = (int) $GLOBALS['blog_id'];
		$post_cache = $GLOBALS['post_cache'];


		$total_count = $this->get_posts_count();
		$last_page = (int) ceil($total_count / $count);
		$next_page = (($page + 1) > $last_page) ? NULL : $page + 1;
		$prev_page = ($page - 1) < 1 ? NULL : $page - 1; 
		$last_page = ((int)$last_page == 1 || (int)$last_page == 0) ? NULL : (int) $last_page;
?><feed xmlns="http://www.w3.org/2005/Atom" xmlns:app="http://purl.org/atom/app#" xml:lang="<?php echo get_option('rss_language'); ?>">
<id><?php $this->the_entries_url() ?></id>
<updated><?php echo mysql2date('Y-m-d\TH:i:s\Z', get_lastpostmodified('GMT')); ?></updated>
<title type="text"><?php bloginfo_rss('name') ?></title>
<subtitle type="text"><?php bloginfo_rss("description") ?></subtitle>
<link rel="first" type="application/atom+xml" href="<?php $this->the_entries_url() ?>" />
<?php if(isset($prev_page)): ?>
<link rel="previous" type="application/atom+xml" href="<?php $this->the_entries_url($prev_page) ?>" />
<?php endif; ?>
<?php if(isset($next_page)): ?>
<link rel="next" type="application/atom+xml" href="<?php $this->the_entries_url($next_page) ?>" />
<?php endif; ?>
<link rel="last" type="application/atom+xml" href="<?php $this->the_entries_url($last_page) ?>" />
<link rel="self" type="application/atom+xml" href="<?php $this->the_entries_url() ?>" />
<rights type="text">Copyright <?php echo mysql2date('Y', get_lastpostdate('blog')); ?></rights>
<generator uri="http://wordpress.com/" version="1.0.5-dc">WordPress.com Atom API</generator>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); 
$post = $GLOBALS['post'];
?>
<entry>
		<id><?php the_guid($post->ID); ?></id>
		<title type="html"><![CDATA[<?php the_title() ?>]]></title>
		<updated><?php echo get_post_modified_time('Y-m-d\TH:i:s\Z', true); ?></updated>
		<published><?php echo get_post_time('Y-m-d\TH:i:s\Z', true); ?></published>
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
	<?php if($GLOBALS['post']->post_status == 'attachment') { ?>
		<link rel="edit" href="<?php $this->the_entry_url() ?>" />
		<link rel="edit-media" href="<?php $this->the_media_url() ?>" />
	<?php } else { ?>
		<link href="<?php permalink_single_rss() ?>" />
		<link rel="edit" href="<?php $this->the_entry_url() ?>" />
	<?php } ?>
	<?php foreach(get_the_category() as $category) { ?>
	 <category scheme="<?php bloginfo_rss('home') ?>" term="<?php echo $category->cat_name?>" />
	<?php } ?>   <summary type="html"><![CDATA[<?php the_excerpt_rss(); ?>]]></summary>
	<?php if ( strlen( $GLOBALS['post']->post_content ) ) : ?>
	<content type="html"><![CDATA[<?php echo get_the_content('', 0, '') ?>]]></content>
<?php endif; ?>
	</entry>
<?php
	endwhile; 
	endif;
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
		if ( have_posts() ) : while ( have_posts() ) : the_post();
		$post = $GLOBALS['post'];
		?>
		<?php log_app('$post',print_r($GLOBALS['post'],true)); ?>
<entry xmlns="http://www.w3.org/2005/Atom" xmlns:app="http://purl.org/atom/app#" xml:lang="<?php echo get_option('rss_language'); ?>">
	<id><?php the_guid($post->ID); ?></id>
	<title type="html"><![CDATA[<?php the_title_rss() ?>]]></title>

	<updated><?php echo get_post_modified_time('Y-m-d\TH:i:s\Z', true); ?></updated>
	<published><?php echo get_post_time('Y-m-d\TH:i:s\Z', true); ?></published>
	<app:control>
		<app:draft><?php echo ($GLOBALS['post']->post_status == 'draft' ? 'yes' : 'no') ?></app:draft>
	</app:control>
	<author>
		<name><?php the_author()?></name>
		<email><?php the_author_email()?></email>
		<uri><?php the_author_url()?></uri>
	</author>
<?php if($GLOBALS['post']->post_type == 'attachment') { ?>
	<link rel="edit" href="<?php $this->the_entry_url() ?>" />
	<link rel="edit-media" href="<?php $this->the_media_url() ?>" />
	<content type="<?php echo $GLOBALS['post']->post_mime_type ?>" src="<?php the_guid(); ?>"/>
<?php } else { ?>
	<link href="<?php permalink_single_rss() ?>" />
	<link rel="edit" href="<?php $this->the_entry_url() ?>" />
<?php } ?>
<?php foreach(get_the_category() as $category) { ?>
	<category scheme="<?php bloginfo_rss('home') ?>" term="<?php echo $category->cat_name?>" />
	<summary type="html"><![CDATA[<?php the_excerpt_rss(); ?>]]></summary>
<?php }
	if ( strlen( $GLOBALS['post']->post_content ) ) : ?>
	<content type="html"><![CDATA[<?php echo get_the_content('', 0, '') ?>]]></content>
<?php endif; ?>
</entry>
<?php
		$entry = ob_get_contents();
		break;
		endwhile;
		else:
			$this->auth_required(__("Access Denied."));
		endif;
		ob_end_clean();

		log_app('get_entry returning:',$entry);
		return $entry; 
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

	function client_error($msg = 'Client Error') {
		log_app('Status','400: Client Errir');
		header('Content-Type: text/plain');
		status_header('400');
		exit;
	}

	function created($post_ID, $content, $post_type = 'post') {
		global $use_querystring;
		log_app('created()::$post_ID',"$post_ID, $post_type");
		$edit = $this->get_entry_url($post_ID);
		switch($post_type) {
			case 'post':
				$ctloc = $this->get_entry_url($post_ID);
				break;
			case 'attachment':
				if ($use_querystring) {
					$edit = get_bloginfo('url') . '/' . $this->script_name . "?action=/attachments&amp;eid=$post_ID";
				} else {
					$edit = get_bloginfo('url') . '/' . $this->script_name . "/attachments/$post_ID";
				}
				break;
		}
		header('Content-Type: application/atom+xml');
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
		header('WWW-Authenticate: Form action="' . get_option('siteurl') . '/wp-login.php"', false); 
		header("HTTP/1.1 401 $msg");
		header('Status: ' . $msg);
		header('Content-Type: plain/text');
		echo $msg;
		exit;
	}

	function output($xml, $ctype = "application/atom+xml") {
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
					return $type;
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


}

$server = new AtomServer();
$server->handle_request();

?>
