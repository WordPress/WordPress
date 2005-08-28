<?php
/*
 * Project:     MagpieRSS: a simple RSS integration tool
 * File:        A compiled file for RSS syndication
 * Author:      Kellan Elliott-McCrea <kellan@protest.net>
 * Version:		0.51
 * License:		GPL
 */

define('RSS', 'RSS');
define('ATOM', 'Atom');
define('MAGPIE_USER_AGENT', 'WordPress/' . $wp_version);

class MagpieRSS { 
	var $parser;
	var $current_item	= array();	// item currently being parsed
	var $items			= array();	// collection of parsed items
	var $channel		= array();	// hash of channel fields
	var $textinput		= array();
	var $image			= array();
	var $feed_type;
	var $feed_version;

	// parser variables
	var $stack				= array(); // parser stack
	var $inchannel			= false;
	var $initem 			= false;
	var $incontent			= false; // if in Atom <content mode="xml"> field 
	var $intextinput		= false;
	var $inimage 			= false;
	var $current_field		= '';
	var $current_namespace	= false;
	
	//var $ERROR = "";
	
	var $_CONTENT_CONSTRUCTS = array('content', 'summary', 'info', 'title', 'tagline', 'copyright');

	function MagpieRSS ($source) {
		
		# if PHP xml isn't compiled in, die
		#
		if ( !function_exists('xml_parser_create') )
			die( "Failed to load PHP's XML Extension. http://www.php.net/manual/en/ref.xml.php" );
		
		$parser = @xml_parser_create();
		
		if ( !is_resource($parser) )
			die( "Failed to create an instance of PHP's XML parser. http://www.php.net/manual/en/ref.xml.php");

		
		$this->parser = $parser;
		
		# pass in parser, and a reference to this object
		# setup handlers
		#
		xml_set_object( $this->parser, $this );
		xml_set_element_handler($this->parser, 
				'feed_start_element', 'feed_end_element' );
						
		xml_set_character_data_handler( $this->parser, 'feed_cdata' ); 
	
		$status = xml_parse( $this->parser, $source );
		
		if (! $status ) {
			$errorcode = xml_get_error_code( $this->parser );
			if ( $errorcode != XML_ERROR_NONE ) {
				$xml_error = xml_error_string( $errorcode );
				$error_line = xml_get_current_line_number($this->parser);
				$error_col = xml_get_current_column_number($this->parser);
				$errormsg = "$xml_error at line $error_line, column $error_col";

				$this->error( $errormsg );
			}
		}
		
		xml_parser_free( $this->parser );

		$this->normalize();
	}
	
	function feed_start_element($p, $element, &$attrs) {
		$el = $element = strtolower($element);
		$attrs = array_change_key_case($attrs, CASE_LOWER);
		
		// check for a namespace, and split if found
		$ns	= false;
		if ( strpos( $element, ':' ) ) {
			list($ns, $el) = split( ':', $element, 2); 
		}
		if ( $ns and $ns != 'rdf' ) {
			$this->current_namespace = $ns;
		}
			
		# if feed type isn't set, then this is first element of feed
		# identify feed from root element
		#
		if (!isset($this->feed_type) ) {
			if ( $el == 'rdf' ) {
				$this->feed_type = RSS;
				$this->feed_version = '1.0';
			}
			elseif ( $el == 'rss' ) {
				$this->feed_type = RSS;
				$this->feed_version = $attrs['version'];
			}
			elseif ( $el == 'feed' ) {
				$this->feed_type = ATOM;
				$this->feed_version = $attrs['version'];
				$this->inchannel = true;
			}
			return;
		}
	
		if ( $el == 'channel' ) 
		{
			$this->inchannel = true;
		}
		elseif ($el == 'item' or $el == 'entry' ) 
		{
			$this->initem = true;
			if ( isset($attrs['rdf:about']) ) {
				$this->current_item['about'] = $attrs['rdf:about'];	
			}
		}
		
		// if we're in the default namespace of an RSS feed,
		//  record textinput or image fields
		elseif ( 
			$this->feed_type == RSS and 
			$this->current_namespace == '' and 
			$el == 'textinput' ) 
		{
			$this->intextinput = true;
		}
		
		elseif (
			$this->feed_type == RSS and 
			$this->current_namespace == '' and 
			$el == 'image' ) 
		{
			$this->inimage = true;
		}
		
		# handle atom content constructs
		elseif ( $this->feed_type == ATOM and in_array($el, $this->_CONTENT_CONSTRUCTS) )
		{
			// avoid clashing w/ RSS mod_content
			if ($el == 'content' ) {
				$el = 'atom_content';
			}
			
			$this->incontent = $el;
			
			
		}
		
		// if inside an Atom content construct (e.g. content or summary) field treat tags as text
		elseif ($this->feed_type == ATOM and $this->incontent ) 
		{
			// if tags are inlined, then flatten
			$attrs_str = join(' ', 
					array_map('map_attrs', 
					array_keys($attrs), 
					array_values($attrs) ) );
			
			$this->append_content( "<$element $attrs_str>"  );
					
			array_unshift( $this->stack, $el );
		}
		
		// Atom support many links per containging element.
		// Magpie treats link elements of type rel='alternate'
		// as being equivalent to RSS's simple link element.
		//
		elseif ($this->feed_type == ATOM and $el == 'link' ) 
		{
			if ( isset($attrs['rel']) and $attrs['rel'] == 'alternate' ) 
			{
				$link_el = 'link';
			}
			else {
				$link_el = 'link_' . $attrs['rel'];
			}
			
			$this->append($link_el, $attrs['href']);
		}
		// set stack[0] to current element
		else {
			array_unshift($this->stack, $el);
		}
	}
	

	
	function feed_cdata ($p, $text) {
		
		if ($this->feed_type == ATOM and $this->incontent) 
		{
			$this->append_content( $text );
		}
		else {
			$current_el = join('_', array_reverse($this->stack));
			$this->append($current_el, $text);
		}
	}
	
	function feed_end_element ($p, $el) {
		$el = strtolower($el);
		
		if ( $el == 'item' or $el == 'entry' ) 
		{
			$this->items[] = $this->current_item;
			$this->current_item = array();
			$this->initem = false;
		}
		elseif ($this->feed_type == RSS and $this->current_namespace == '' and $el == 'textinput' ) 
		{
			$this->intextinput = false;
		}
		elseif ($this->feed_type == RSS and $this->current_namespace == '' and $el == 'image' ) 
		{
			$this->inimage = false;
		}
		elseif ($this->feed_type == ATOM and in_array($el, $this->_CONTENT_CONSTRUCTS) )
		{	
			$this->incontent = false;
		}
		elseif ($el == 'channel' or $el == 'feed' ) 
		{
			$this->inchannel = false;
		}
		elseif ($this->feed_type == ATOM and $this->incontent  ) {
			// balance tags properly
			// note:  i don't think this is actually neccessary
			if ( $this->stack[0] == $el ) 
			{
				$this->append_content("</$el>");
			}
			else {
				$this->append_content("<$el />");
			}

			array_shift( $this->stack );
		}
		else {
			array_shift( $this->stack );
		}
		
		$this->current_namespace = false;
	}
	
	function concat (&$str1, $str2="") {
		if (!isset($str1) ) {
			$str1="";
		}
		$str1 .= $str2;
	}
	
	function append_content($text) {
		if ( $this->initem ) {
			$this->concat( $this->current_item[ $this->incontent ], $text );
		}
		elseif ( $this->inchannel ) {
			$this->concat( $this->channel[ $this->incontent ], $text );
		}
	}
	
	// smart append - field and namespace aware
	function append($el, $text) {
		if (!$el) {
			return;
		}
		if ( $this->current_namespace ) 
		{
			if ( $this->initem ) {
				$this->concat(
					$this->current_item[ $this->current_namespace ][ $el ], $text);
			}
			elseif ($this->inchannel) {
				$this->concat(
					$this->channel[ $this->current_namespace][ $el ], $text );
			}
			elseif ($this->intextinput) {
				$this->concat(
					$this->textinput[ $this->current_namespace][ $el ], $text );
			}
			elseif ($this->inimage) {
				$this->concat(
					$this->image[ $this->current_namespace ][ $el ], $text );
			}
		}
		else {
			if ( $this->initem ) {
				$this->concat(
					$this->current_item[ $el ], $text);
			}
			elseif ($this->intextinput) {
				$this->concat(
					$this->textinput[ $el ], $text );
			}
			elseif ($this->inimage) {
				$this->concat(
					$this->image[ $el ], $text );
			}
			elseif ($this->inchannel) {
				$this->concat(
					$this->channel[ $el ], $text );
			}
			
		}
	}
	
	function normalize () {
		// if atom populate rss fields
		if ( $this->is_atom() ) {
			$this->channel['descripton'] = $this->channel['tagline'];
			for ( $i = 0; $i < count($this->items); $i++) {
				$item = $this->items[$i];
				if ( isset($item['summary']) )
					$item['description'] = $item['summary'];
				if ( isset($item['atom_content']))
					$item['content']['encoded'] = $item['atom_content'];
				
				$this->items[$i] = $item;
			}		
		}
		elseif ( $this->is_rss() ) {
			$this->channel['tagline'] = $this->channel['description'];
			for ( $i = 0; $i < count($this->items); $i++) {
				$item = $this->items[$i];
				if ( isset($item['description']))
					$item['summary'] = $item['description'];
				if ( isset($item['content']['encoded'] ) )
					$item['atom_content'] = $item['content']['encoded'];
			
				$this->items[$i] = $item;
			}
		}
	}
	
	function is_rss () {
		if ( $this->feed_type == RSS ) {
			return $this->feed_version;	
		}
		else {
			return false;
		}
	}
	
	function is_atom() {
		if ( $this->feed_type == ATOM ) {
			return $this->feed_version;
		}
		else {
			return false;
		}
	}

function map_attrs($k, $v) {
	return "$k=\"$v\"";
	}
}
require_once( dirname(__FILE__) . '/class-snoopy.php');

function fetch_rss ($url) {
	// initialize constants
	init();
	
	if ( !isset($url) ) {
		// error("fetch_rss called without a url");
		return false;
	}
	
	// if cache is disabled
	if ( !MAGPIE_CACHE_ON ) {
		// fetch file, and parse it
		$resp = _fetch_remote_file( $url );
		if ( is_success( $resp->status ) ) {
			return _response_to_rss( $resp );
		}
		else {
			// error("Failed to fetch $url and cache is off");
			return false;
		}
	} 
	// else cache is ON
	else {
		// Flow
		// 1. check cache
		// 2. if there is a hit, make sure its fresh
		// 3. if cached obj fails freshness check, fetch remote
		// 4. if remote fails, return stale object, or error
		
		$cache = new RSSCache( MAGPIE_CACHE_DIR, MAGPIE_CACHE_AGE );
		
		if (MAGPIE_DEBUG and $cache->ERROR) {
			debug($cache->ERROR, E_USER_WARNING);
		}
		
		
		$cache_status 	 = 0;		// response of check_cache
		$request_headers = array(); // HTTP headers to send with fetch
		$rss 			 = 0;		// parsed RSS object
		$errormsg		 = 0;		// errors, if any
		
		if (!$cache->ERROR) {
			// return cache HIT, MISS, or STALE
			$cache_status = $cache->check_cache( $url );
		}

		// if object cached, and cache is fresh, return cached obj
		if ( $cache_status == 'HIT' ) {
			$rss = $cache->get( $url );
			if ( isset($rss) and $rss ) {
				$rss->from_cache = 1;
				if ( MAGPIE_DEBUG > 1) {
				debug("MagpieRSS: Cache HIT", E_USER_NOTICE);
			}
				return $rss;
			}
		}
		
		// else attempt a conditional get
		
		// setup headers
		if ( $cache_status == 'STALE' ) {
			$rss = $cache->get( $url );
			if ( $rss->etag and $rss->last_modified ) {
				$request_headers['If-None-Match'] = $rss->etag;
				$request_headers['If-Last-Modified'] = $rss->last_modified;
			}
		}
		
		$resp = _fetch_remote_file( $url, $request_headers );
		
		if (isset($resp) and $resp) {
			if ($resp->status == '304' ) {
				// we have the most current copy
				if ( MAGPIE_DEBUG > 1) {
					debug("Got 304 for $url");
				}
				// reset cache on 304 (at minutillo insistent prodding)
				$cache->set($url, $rss);
				return $rss;
			}
			elseif ( is_success( $resp->status ) ) {
				$rss = _response_to_rss( $resp );
				if ( $rss ) {
					if (MAGPIE_DEBUG > 1) {
						debug("Fetch successful");
					}
					// add object to cache
					$cache->set( $url, $rss );
					return $rss;
				}
			}
			else {
				$errormsg = "Failed to fetch $url. ";
				if ( $resp->error ) {
					# compensate for Snoopy's annoying habbit to tacking
					# on '\n'
					$http_error = substr($resp->error, 0, -2); 
					$errormsg .= "(HTTP Error: $http_error)";
				}
				else {
					$errormsg .=  "(HTTP Response: " . $resp->response_code .')';
				}
			}
		}
		else {
			$errormsg = "Unable to retrieve RSS file for unknown reasons.";
		}
		
		// else fetch failed
		
		// attempt to return cached object
		if ($rss) {
			if ( MAGPIE_DEBUG ) {
				debug("Returning STALE object for $url");
			}
			return $rss;
		}
		
		// else we totally failed
		// error( $errormsg );	
		
		return false;
		
	} // end if ( !MAGPIE_CACHE_ON ) {
} // end fetch_rss()

function _fetch_remote_file ($url, $headers = "" ) {
	// Snoopy is an HTTP client in PHP
	$client = new Snoopy();
	$client->agent = MAGPIE_USER_AGENT;
	$client->read_timeout = MAGPIE_FETCH_TIME_OUT;
	$client->use_gzip = MAGPIE_USE_GZIP;
	if (is_array($headers) ) {
		$client->rawheaders = $headers;
	}
	
	@$client->fetch($url);
	return $client;

}

function _response_to_rss ($resp) {
	$rss = new MagpieRSS( $resp->results );
	
	// if RSS parsed successfully		
	if ( $rss and !$rss->ERROR) {
		
		// find Etag, and Last-Modified
		foreach($resp->headers as $h) {
			// 2003-03-02 - Nicola Asuni (www.tecnick.com) - fixed bug "Undefined offset: 1"
			if (strpos($h, ": ")) {
				list($field, $val) = explode(": ", $h, 2);
			}
			else {
				$field = $h;
				$val = "";
			}
			
			if ( $field == 'ETag' ) {
				$rss->etag = $val;
			}
			
			if ( $field == 'Last-Modified' ) {
				$rss->last_modified = $val;
			}
		}
		
		return $rss;	
	} // else construct error message
	else {
		$errormsg = "Failed to parse RSS file.";
		
		if ($rss) {
			$errormsg .= " (" . $rss->ERROR . ")";
		}
		// error($errormsg);
		
		return false;
	} // end if ($rss and !$rss->error)
}

/*=======================================================================*\
	Function:	init
	Purpose:	setup constants with default values
				check for user overrides
\*=======================================================================*/
function init () {
	if ( defined('MAGPIE_INITALIZED') ) {
		return;
	}
	else {
		define('MAGPIE_INITALIZED', 1);
	}
	
	if ( !defined('MAGPIE_CACHE_ON') ) {
		define('MAGPIE_CACHE_ON', 1);
	}

	if ( !defined('MAGPIE_CACHE_DIR') ) {
		define('MAGPIE_CACHE_DIR', './cache');
	}

	if ( !defined('MAGPIE_CACHE_AGE') ) {
		define('MAGPIE_CACHE_AGE', 60*60); // one hour
	}

	if ( !defined('MAGPIE_CACHE_FRESH_ONLY') ) {
		define('MAGPIE_CACHE_FRESH_ONLY', 0);
	}
	
		if ( !defined('MAGPIE_DEBUG') ) {
		define('MAGPIE_DEBUG', 0);
	}

	if ( !defined('MAGPIE_USER_AGENT') ) {
		$ua = 'WordPress/' . $wp_version;
		
		if ( MAGPIE_CACHE_ON ) {
			$ua = $ua . ')';
		}
		else {
			$ua = $ua . '; No cache)';
		}
		
		define('MAGPIE_USER_AGENT', $ua);
	}
	
	if ( !defined('MAGPIE_FETCH_TIME_OUT') ) {
		define('MAGPIE_FETCH_TIME_OUT', 2);	// 2 second timeout
	}
	
	// use gzip encoding to fetch rss files if supported?
	if ( !defined('MAGPIE_USE_GZIP') ) {
		define('MAGPIE_USE_GZIP', true);	
	}
}

function is_info ($sc) { 
	return $sc >= 100 && $sc < 200; 
}

function is_success ($sc) { 
	return $sc >= 200 && $sc < 300; 
}

function is_redirect ($sc) { 
	return $sc >= 300 && $sc < 400; 
}

function is_error ($sc) { 
	return $sc >= 400 && $sc < 600; 
}

function is_client_error ($sc) { 
	return $sc >= 400 && $sc < 500; 
}

function is_server_error ($sc) { 
	return $sc >= 500 && $sc < 600; 
}

class RSSCache {
	var $BASE_CACHE = 'wp-content/cache';	// where the cache files are stored
	var $MAX_AGE	= 43200;  		// when are files stale, default twelve hours
	var $ERROR 		= '';			// accumulate error messages
	
	function RSSCache ($base='', $age='') {
		if ( $base ) {
			$this->BASE_CACHE = $base;
		}
		if ( $age ) {
			$this->MAX_AGE = $age;
		}
	
	}
	
/*=======================================================================*\
	Function:	set
	Purpose:	add an item to the cache, keyed on url
	Input:		url from wich the rss file was fetched
	Output:		true on sucess	
\*=======================================================================*/
	function set ($url, $rss) {
		global $wpdb;
		$cache_option = 'rss_' . $this->file_name( $url );
		$cache_timestamp = 'rss_' . $this->file_name( $url ) . '_ts';
		
		if ( !$wpdb->get_var("SELECT option_name FROM $wpdb->options WHERE option_name = '$cache_option'") )
			add_option($cache_option, '', '', 'no');
		if ( !$wpdb->get_var("SELECT option_name FROM $wpdb->options WHERE option_name = '$cache_timestamp'") )
			add_option($cache_timestamp, '', '', 'no');
		
		update_option($cache_option, $rss);
		update_option($cache_timestamp, time() );
		
		return $cache_option;
	}
	
/*=======================================================================*\
	Function:	get
	Purpose:	fetch an item from the cache
	Input:		url from wich the rss file was fetched
	Output:		cached object on HIT, false on MISS	
\*=======================================================================*/	
	function get ($url) {
		$this->ERROR = "";
		$cache_option = 'rss_' . $this->file_name( $url );
		
		if ( ! get_option( $cache_option ) ) {
			$this->debug( 
				"Cache doesn't contain: $url (cache option: $cache_option)"
			);
			return 0;
		}
		
		$rss = get_option( $cache_option );
		
		return $rss;
	}

/*=======================================================================*\
	Function:	check_cache
	Purpose:	check a url for membership in the cache
				and whether the object is older then MAX_AGE (ie. STALE)
	Input:		url from wich the rss file was fetched
	Output:		cached object on HIT, false on MISS	
\*=======================================================================*/		
	function check_cache ( $url ) {
		$this->ERROR = "";
		$cache_option = $this->file_name( $url );
		$cache_timestamp = 'rss_' . $this->file_name( $url ) . '_ts';

		if ( $mtime = get_option($cache_timestamp) ) {
			// find how long ago the file was added to the cache
			// and whether that is longer then MAX_AGE
			$age = time() - $mtime;
			if ( $this->MAX_AGE > $age ) {
				// object exists and is current
				return 'HIT';
			}
			else {
				// object exists but is old
				return 'STALE';
			}
		}
		else {
			// object does not exist
			return 'MISS';
		}
	}

/*=======================================================================*\
	Function:	serialize
\*=======================================================================*/		
	function serialize ( $rss ) {
		return serialize( $rss );
	}

/*=======================================================================*\
	Function:	unserialize
\*=======================================================================*/		
	function unserialize ( $data ) {
		return unserialize( $data );
	}
	
/*=======================================================================*\
	Function:	file_name
	Purpose:	map url to location in cache
	Input:		url from wich the rss file was fetched
	Output:		a file name
\*=======================================================================*/		
	function file_name ($url) {
		return md5( $url );
	}
	
/*=======================================================================*\
	Function:	error
	Purpose:	register error
\*=======================================================================*/			
	function error ($errormsg, $lvl=E_USER_WARNING) {
		// append PHP's error message if track_errors enabled
		if ( isset($php_errormsg) ) { 
			$errormsg .= " ($php_errormsg)";
		}
		$this->ERROR = $errormsg;
		if ( MAGPIE_DEBUG ) {
			trigger_error( $errormsg, $lvl);
		}
		else {
			error_log( $errormsg, 0);
		}
	}
			function debug ($debugmsg, $lvl=E_USER_NOTICE) {
		if ( MAGPIE_DEBUG ) {
			$this->error("MagpieRSS [debug] $debugmsg", $lvl);
		}
	}
}

function parse_w3cdtf ( $date_str ) {
	
	# regex to match wc3dtf
	$pat = "/(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})(:(\d{2}))?(?:([-+])(\d{2}):?(\d{2})|(Z))?/";
	
	if ( preg_match( $pat, $date_str, $match ) ) {
		list( $year, $month, $day, $hours, $minutes, $seconds) = 
			array( $match[1], $match[2], $match[3], $match[4], $match[5], $match[6]);
		
		# calc epoch for current date assuming GMT
		$epoch = gmmktime( $hours, $minutes, $seconds, $month, $day, $year);
		
		$offset = 0;
		if ( $match[10] == 'Z' ) {
			# zulu time, aka GMT
		}
		else {
			list( $tz_mod, $tz_hour, $tz_min ) =
				array( $match[8], $match[9], $match[10]);
			
			# zero out the variables
			if ( ! $tz_hour ) { $tz_hour = 0; }
			if ( ! $tz_min ) { $tz_min = 0; }
		
			$offset_secs = (($tz_hour*60)+$tz_min)*60;
			
			# is timezone ahead of GMT?  then subtract offset
			#
			if ( $tz_mod == '+' ) {
				$offset_secs = $offset_secs * -1;
			}
			
			$offset = $offset_secs;	
		}
		$epoch = $epoch + $offset;
		return $epoch;
	}
	else {
		return -1;
	}
	}
function wp_rss ($url, $num) {
	//ini_set("display_errors", false); uncomment to suppress php errors thrown if the feed is not returned.
	$num_items = $num;
	$rss = fetch_rss($url);
		if ( $rss ) {
			echo "<ul>";
			$rss->items = array_slice($rss->items, 0, $num_items);
				foreach ($rss->items as $item ) {
					echo "<li>\n";
					echo "<a href='$item[link]' title='$item[description]'>";
					echo htmlentities($item['title']);
					echo "</a><br />\n";
					echo "</li>\n";
				}		
			echo "</ul>";
	}
		else {
			echo "an error has occured the feed is probably down, try again later.";
	}
}

function get_rss ($uri, $num = 5) { // Like get posts, but for RSS
	$rss = fetch_rss($url);
	if ( $rss ) {
		$rss->items = array_slice($rss->items, 0, $num_items);
		foreach ($rss->items as $item ) {
			echo "<li>\n";
			echo "<a href='$item[link]' title='$item[description]'>";
			echo htmlentities($item['title']);
			echo "</a><br />\n";
			echo "</li>\n";
		}
		return $posts;
	} else {
		return false;
	}
}
?>