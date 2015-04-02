<?php
/**
 * Parse OPML XML files and store in globals.
 *
 * @package WordPress
 * @subpackage Administration
 */

if ( ! defined('ABSPATH') )
	die();

global $opml;

/**
 * XML callback function for the start of a new XML tag.
 *
 * @since 0.71
 * @access private
 *
 * @global array $names
 * @global array $urls
 * @global array $targets
 * @global array $descriptions
 * @global array $feeds
 *
 * @param mixed $parser XML Parser resource.
 * @param string $tagName XML element name.
 * @param array $attrs XML element attributes.
 */
function startElement($parser, $tagName, $attrs) {
	global $names, $urls, $targets, $descriptions, $feeds;

	if ( 'OUTLINE' === $tagName ) {
		$name = '';
		if ( isset( $attrs['TEXT'] ) ) {
			$name = $attrs['TEXT'];
		}
		if ( isset( $attrs['TITLE'] ) ) {
			$name = $attrs['TITLE'];
		}
		$url = '';
		if ( isset( $attrs['URL'] ) ) {
			$url = $attrs['URL'];
		}
		if ( isset( $attrs['HTMLURL'] ) ) {
			$url = $attrs['HTMLURL'];
		}

		// Save the data away.
		$names[] = $name;
		$urls[] = $url;
		$targets[] = isset( $attrs['TARGET'] ) ? $attrs['TARGET'] :  '';
		$feeds[] = isset( $attrs['XMLURL'] ) ? $attrs['XMLURL'] :  '';
		$descriptions[] = isset( $attrs['DESCRIPTION'] ) ? $attrs['DESCRIPTION'] :  '';
	} // End if outline.
}

/**
 * XML callback function that is called at the end of a XML tag.
 *
 * @since 0.71
 * @access private
 *
 * @param mixed $parser XML Parser resource.
 * @param string $tagName XML tag name.
 */
function endElement($parser, $tagName) {
	// Nothing to do.
}

// Create an XML parser
$xml_parser = xml_parser_create();

// Set the functions to handle opening and closing tags
xml_set_element_handler($xml_parser, "startElement", "endElement");

if ( ! xml_parse( $xml_parser, $opml, true ) ) {
	printf(
		/* translators: 1: error message, 2: line number */
		__( 'XML Error: %1$s at line %2$s' ),
		xml_error_string( xml_get_error_code( $xml_parser ) ),
		xml_get_current_line_number( $xml_parser )
	);
}

// Free up memory used by the XML parser
xml_parser_free($xml_parser);
