<?php
namespace Elementor\Core\Utils\ImportExport\Parsers;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WordPress eXtended RSS file parser implementations,
 * Originally made by WordPress part of WordPress/Importer.
 * https://plugins.trac.wordpress.org/browser/wordpress-importer/trunk/parsers/class-wxr-parser.php
 *
 * What was done:
 * Reformat of the code.
 * Changed text domain.
 */

/**
 * WordPress Importer class for managing parsing of WXR files.
 */
class WXR_Parser {

	public function parse( $file ) {
		// Attempt to use proper XML parsers first.
		if ( extension_loaded( 'simplexml' ) ) {
			$parser = new WXR_Parser_SimpleXML();
			$result = $parser->parse( $file );

			// If SimpleXML succeeds or this is an invalid WXR file then return the results.
			if ( ! is_wp_error( $result ) || 'SimpleXML_parse_error' != $result->get_error_code() ) {
				return $result;
			}
		} elseif ( extension_loaded( 'xml' ) ) {
			$parser = new WXR_Parser_XML();
			$result = $parser->parse( $file );

			// If XMLParser succeeds or this is an invalid WXR file then return the results.
			if ( ! is_wp_error( $result ) || 'XML_parse_error' != $result->get_error_code() ) {
				return $result;
			}
		}

		// Use regular expressions if nothing else available or this is bad XML.
		$parser = new WXR_Parser_Regex();

		return $parser->parse( $file );
	}
}
