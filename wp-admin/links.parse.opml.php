<?php
require_once('../wp-config.php');
require_once($abspath.$b2inc.'/b2functions.php');

// columns we wish to find are:  link_url, link_name, link_target, link_description
// we need to map XML attribute names to our columns
// if we are doing OPML use this map
$opml_map = array(
                  'link_url' => 'URL',
                  'link_name' => 'TEXT',
                  'link_target' => 'TARGET',
                  'link_description' => 'DESCRIPTION'
                 );

$map = $opml_map;

/**
 ** startElement()
 ** Callback function. Called at the start of a new xml tag.
 **/
function startElement($parser, $tagName, $attrs) {
	global $updated_timestamp, $all_links, $map;
    global $names, $urls, $targets, $descriptions;

	if ($tagName == 'OUTLINE') {
        if ($map['link_url'] != '')
            $link_url  = $attrs[$map['link_url']];
        if ($map['link_name'] != '')
            $link_name  = $attrs[$map['link_name']];
        if ($map['link_target'] != '')
            $link_target  = $attrs[$map['link_target']];
        if ($map['link_description'] != '')
            $link_description  = $attrs[$map['link_description']];
        //echo("got data: link_url = [$link_url], link_name = [$link_name], link_target = [$link_target], link_description = [$link_description]<br />\n");
        // save the data away.
        $names[] = $link_name;
        $urls[] = $link_url;
        $targets[] = $link_target;
        $descriptions[] = $link_description;
    }
}

/**
 ** endElement()
 ** Callback function. Called at the end of an xml tag.
 **/
function endElement($parser, $tagName) {
	// nothing to do.
}

// Create an XML parser
$xml_parser = xml_parser_create();

// Set the functions to handle opening and closing tags
xml_set_element_handler($xml_parser, "startElement", "endElement");

xml_parse($xml_parser, $opml, true)
    or echo(sprintf("XML error: %s at line %d",
                   xml_error_string(xml_get_error_code($xml_parser)),
                   xml_get_current_line_number($xml_parser)));

// Free up memory used by the XML parser
xml_parser_free($xml_parser);
?>