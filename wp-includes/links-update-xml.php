<?php
// Links weblogs.com grabber
// Copyright (C) 2003 Mike Little -- mike@zed1.com

require_once('wp-config.php');

// globals to hold state
$updated_timestamp = 0;
$all_links = array();

/**
 ** preload_links()
 ** Pre-load the visible, non-blank, links into an associative array $all_links
 ** key is url, value is array of link_id and update_time
 ** Note: update time is initialised to 0. That way we only have to update (in
 ** the db) the ones which have been updated (on weblogs.com).
 **/
function preload_links() {
	global $tablelinks, $all_links, $wpdb;
	$links = $wpdb->get_results("SELECT link_id, link_url FROM $tablelinks WHERE link_visible = 'Y' AND link_url <> ''");
	foreach ($links as $link) {
		$link_url = transform_url($link->link_url);
		$all_links[$link_url] = array($link->link_id, 0);
	}
}

/**
 ** update_links()
 ** Update in the db the links which have been updated ($all_links[url][1] != 0)
 **/
function update_links() {
	global $tablelinks, $all_links, $wpdb;
	reset($all_links);
	while (list($id, $val) = each($all_links)) {
		if ($val[1]) {
			$wpdb->query("UPDATE $tablelinks SET link_updated = '$val[1]' WHERE link_id = $val[0]");
		}
	} // end while
}

/**
 ** get_weblogs_updatedfile()
 ** Retrieves and caches a copy of the weblogs.com changed blogs xml file.
 ** If the file exists check it's age, get new copy if old.
 ** If a new or updated file has been written return true (needs processing)
 ** otherwise return false (nothing to do)
 **/
function get_weblogs_updatedfile() {
	global $ignore_weblogs_cache,ABSPATH;
	$update = false;

	if ($ignore_weblogs_cache) {
		$update = true;
	} else {
		if (file_exists(get_settings('weblogs_cache_file'))) {
			// is it old?
			$modtime = filemtime(get_settings('weblogs_cache_file'));
			if ((time() - $modtime) > (get_settings('weblogs_cacheminutes') * 60)) {
				$update = true;
			}
		} else { // doesn't exist
			$update = true;
		}
	}

	if ($update) {
		// get a new copy
		$a = @file(get_settings('weblogs_xml_url'));
		if ($a != false && count($a) && $a[0]) {
			$contents = implode('', $a);

			// Clean up the input, because weblogs.com doesn't output clean XML	
			$contents = preg_replace("/'/",'&#39;',$contents);
			$contents = preg_replace('|[^[:space:][:punct:][:alpha:][:digit:]]|','',$contents);

			$cachefp = fopen(get_settings('weblogs_cache_file'), "w");
			fwrite($cachefp, $contents);
			fclose($cachefp);
		} else {
			return false; //don't try to process
		}
	}
	return $update;
}

/**
 ** startElement()
 ** Callback function. Called at the start of a new xml tag.
 **/
function startElement($parser, $tagName, $attrs) {
	global $updated_timestamp, $all_links;
	if ($tagName == 'WEBLOGUPDATES') {
		//convert 'updated' into php date variable
		$updated_timestamp = strtotime($attrs['UPDATED']);
		//echo('got timestamp of ' . gmdate('F j, Y, H:i:s', $updated_timestamp) . "\n");
	} else if ($tagName == 'WEBLOG') {
		// is this url in our links?
		$link_url = transform_url($attrs['URL']);
		if (isset($all_links[$link_url])) {
			$all_links[$link_url][1] = date('YmdHis', $updated_timestamp - $attrs['WHEN']);
			//echo('set link id ' . $all_links[$link_url][0] . ' to date ' . $all_links[$link_url][1] . "\n");
		}
	}
}

/**
 ** endElement()
 ** Callback function. Called at the end of an xml tag.
 **/
function endElement($parser, $tagName) {
	// nothing to do.
}

/**
 ** transform_url()
 ** Transforms a url to a minimal identifier.
 **
 ** Remove www, remove index.* or default.*, remove
 ** trailing slash
 **/
function transform_url($url) {
	global ABSPATH;
	//echo("transform_url(): $url ");
	$url = str_replace('www.', '', $url);
	$url = str_replace('WWW.', '', $url);
	$url = preg_replace('/(?:index|default)\.[a-z]{2,}/i', '', $url);
	if (substr($url, -1, 1) == '/') {
		$url = substr($url, 0, -1);
	}
	//echo(" now equals $url\n");
	return $url;
} // end transform_url

// get/update the cache file.
// true return means new copy
if (get_weblogs_updatedfile()) {
	//echo('<pre>');
	// pre-load the links
	preload_links();

	// Create an XML parser
	$xml_parser = xml_parser_create();

	// Set the functions to handle opening and closing tags
	xml_set_element_handler($xml_parser, "startElement", "endElement");

	// Open the XML file for reading
	$fp = fopen(ABSPATH.get_settings('weblogs_cache_file'), "r")
		  or die("Error reading XML data.");

	// Read the XML file 16KB at a time
	while ($data = fread($fp, 16384)) {
		// Parse each 4KB chunk with the XML parser created above
		xml_parse($xml_parser, $data, feof($fp))
				or die(sprintf("XML error: %s at line %d",
					xml_error_string(xml_get_error_code($xml_parser)),
					xml_get_current_line_number($xml_parser)));
	}

	// Close the XML file
	fclose($fp);

	// Free up memory used by the XML parser
	xml_parser_free($xml_parser);

	// now update the db with latest times
	update_links();

	//echo('</pre>');
} // end if updated cache file

?>