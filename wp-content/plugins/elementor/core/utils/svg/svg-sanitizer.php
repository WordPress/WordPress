<?php
namespace Elementor\Core\Utils\Svg;

use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor SVG Sanitizer.
 *
 * A class that is responsible for sanitizing SVG files.
 *
 * @since 3.16.0
 */
class Svg_Sanitizer {

	/**
	 * @var \DOMDocument
	 */
	private $svg_dom = null;

	/**
	 * Sanitize File
	 *
	 * @since 3.16.0
	 * @access public
	 *
	 * @param $filename
	 * @return bool
	 */
	public function sanitize_file( $filename ) {
		$original_content = Utils::file_get_contents( $filename );
		$is_encoded = $this->is_encoded( $original_content );

		if ( $is_encoded ) {
			$decoded = $this->decode_svg( $original_content );
			if ( false === $decoded ) {
				return false;
			}
			$original_content = $decoded;
		}

		$valid_svg = $this->sanitize( $original_content );

		if ( false === $valid_svg ) {
			return false;
		}

		// If we were gzipped, we need to re-zip
		if ( $is_encoded ) {
			$valid_svg = $this->encode_svg( $valid_svg );
		}
		file_put_contents( $filename, $valid_svg );

		return true;
	}

	/**
	 * Sanitize
	 *
	 * @since 3.16.0
	 * @access public
	 *
	 * @param $content
	 * @return bool|string
	 */
	public function sanitize( $content ) {
		// Strip php tags
		$content = $this->strip_comments( $content );
		$content = $this->strip_php_tags( $content );
		$content = $this->strip_line_breaks( $content );

		// Find the start and end tags so we can cut out miscellaneous garbage.
		$start = strpos( $content, '<svg' );
		$end = strrpos( $content, '</svg>' );
		if ( false === $start || false === $end ) {
			return false;
		}

		$content = substr( $content, $start, ( $end - $start + 6 ) );

		// If the server's PHP version is 8 or up, make sure to Disable the ability to load external entities
		$php_version_under_eight = version_compare( PHP_VERSION, '8.0.0', '<' );
		if ( $php_version_under_eight ) {
			$libxml_disable_entity_loader = libxml_disable_entity_loader( true ); // phpcs:ignore Generic.PHP.DeprecatedFunctions.Deprecated
		}
		// Suppress the errors
		$libxml_use_internal_errors = libxml_use_internal_errors( true );

		// Create DomDocument instance
		$this->svg_dom = new \DOMDocument();
		$this->svg_dom->formatOutput = false;
		$this->svg_dom->preserveWhiteSpace = false;
		$this->svg_dom->strictErrorChecking = false;

		$open_svg = $this->svg_dom->loadXML( $content );
		if ( ! $open_svg ) {
			return false;
		}

		$this->strip_doctype();
		$this->sanitize_elements();

		// Export sanitized svg to string
		// Using documentElement to strip out <?xml version="1.0" encoding="UTF-8"...
		$sanitized = $this->svg_dom->saveXML( $this->svg_dom->documentElement, LIBXML_NOEMPTYTAG );

		// Restore defaults
		if ( $php_version_under_eight ) {
			libxml_disable_entity_loader( $libxml_disable_entity_loader ); // phpcs:ignore Generic.PHP.DeprecatedFunctions.Deprecated
		}
		libxml_use_internal_errors( $libxml_use_internal_errors );

		return $sanitized;
	}

	/**
	 * Is Encoded
	 *
	 * Check if the contents of the SVG file are gzipped
	 *
	 * @see http://www.gzip.org/zlib/rfc-gzip.html#member-format
	 *
	 * @since 3.16.0
	 * @access private
	 *
	 * @param $contents
	 *
	 * @return bool
	 */
	private function is_encoded( $contents ) {
		$needle = "\x1f\x8b\x08";
		if ( function_exists( 'mb_strpos' ) ) {
			return 0 === mb_strpos( $contents, $needle );
		} else {
			return 0 === strpos( $contents, $needle );
		}
	}

	/**
	 * Encode SVG
	 *
	 * @since 3.16.0
	 * @access private
	 *
	 * @param $content
	 * @return string
	 */
	private function encode_svg( $content ) {
		return gzencode( $content );
	}

	/**
	 * Decode SVG
	 *
	 * @since 3.16.0
	 * @access private
	 *
	 * @param $content
	 *
	 * @return string
	 */
	private function decode_svg( $content ) {
		return gzdecode( $content );
	}

	/**
	 * Is Allowed Tag
	 *
	 * @since 3.16.0
	 * @access private
	 *
	 * @param $element
	 * @return bool
	 */
	private function is_allowed_tag( $element ) {
		static $allowed_tags = false;
		if ( false === $allowed_tags ) {
			$allowed_tags = $this->get_allowed_elements();
		}

		$tag_name = $element->tagName; // phpcs:ignore -- php DomDocument

		if ( ! in_array( strtolower( $tag_name ), $allowed_tags ) ) {
			$this->remove_element( $element );
			return false;
		}

		return true;
	}

	/**
	 * Remove Element
	 *
	 * Removes the passed element from its DomDocument tree
	 *
	 * @since 3.16.0
	 * @access private
	 *
	 * @param $element
	 */
	private function remove_element( $element ) {
		$element->parentNode->removeChild( $element ); // phpcs:ignore -- php DomDocument
	}

	/**
	 * Is It An Attribute
	 *
	 * @since 3.16.0
	 * @access private
	 *
	 * @param $name
	 * @param $check
	 * @return bool
	 */
	private function is_a_attribute( $name, $check ) {
		return 0 === strpos( $name, $check . '-' );
	}

	/**
	 * Is Remote Value
	 *
	 * @since 3.16.0
	 * @access private
	 *
	 * @param $value
	 * @return string
	 */
	private function is_remote_value( $value ) {
		$value = trim( preg_replace( '/[^ -~]/xu', '', $value ) );
		$wrapped_in_url = preg_match( '~^url\(\s*[\'"]\s*(.*)\s*[\'"]\s*\)$~xi', $value, $match );
		if ( ! $wrapped_in_url ) {
			return false;
		}

		$value = trim( $match[1], '\'"' );
		return preg_match( '~^((https?|ftp|file):)?//~xi', $value );
	}

	/**
	 * Has JS Value
	 *
	 * @since 3.16.0
	 * @access private
	 *
	 * @param $value
	 * @return false|int
	 */
	private function has_js_value( $value ) {
		return preg_match( '/base64|data|(?:java)?script|alert\(|window\.|document/i', $value );
	}

	/**
	 * Get Allowed Attributes
	 *
	 * Returns an array of allowed tag attributes in SVG files.
	 *
	 * @since 3.16.0
	 * @access private
	 *
	 * @return array
	 */
	private function get_allowed_attributes() {
		$allowed_attributes = [
			'accent-height',
			'accumulate',
			'additivive',
			'alignment-baseline',
			'aria-hidden',
			'aria-controls',
			'aria-describedby',
			'aria-description',
			'aria-expanded',
			'aria-haspopup',
			'aria-label',
			'aria-labelledby',
			'aria-roledescription',
			'ascent',
			'attributename',
			'attributetype',
			'azimuth',
			'basefrequency',
			'baseline-shift',
			'begin',
			'bias',
			'by',
			'class',
			'clip',
			'clip-path',
			'clip-rule',
			'clippathunits',
			'color',
			'color-interpolation',
			'color-interpolation-filters',
			'color-profile',
			'color-rendering',
			'cx',
			'cy',
			'd',
			'dx',
			'dy',
			'diffuseconstant',
			'direction',
			'display',
			'divisor',
			'dominant-baseline',
			'dur',
			'edgemode',
			'elevation',
			'end',
			'fill',
			'fill-opacity',
			'fill-rule',
			'filter',
			'filterres',
			'filterunits',
			'flood-color',
			'flood-opacity',
			'font-family',
			'font-size',
			'font-size-adjust',
			'font-stretch',
			'font-style',
			'font-variant',
			'font-weight',
			'fx',
			'fy',
			'g1',
			'g2',
			'glyph-name',
			'glyphref',
			'gradienttransform',
			'gradientunits',
			'height',
			'href',
			'id',
			'image-rendering',
			'in',
			'in2',
			'k',
			'k1',
			'k2',
			'k3',
			'k4',
			'kerning',
			'keypoints',
			'keysplines',
			'keytimes',
			'lang',
			'lengthadjust',
			'letter-spacing',
			'kernelmatrix',
			'kernelunitlength',
			'lighting-color',
			'local',
			'marker-end',
			'marker-mid',
			'marker-start',
			'markerheight',
			'markerunits',
			'markerwidth',
			'mask',
			'maskcontentunits',
			'maskunits',
			'max',
			'media',
			'method',
			'mode',
			'min',
			'name',
			'numoctaves',
			'offset',
			'opacity',
			'operator',
			'order',
			'orient',
			'orientation',
			'origin',
			'overflow',
			'paint-order',
			'path',
			'pathlength',
			'patterncontentunits',
			'patterntransform',
			'patternunits',
			'points',
			'preservealpha',
			'preserveaspectratio',
			'primitiveunits',
			'r',
			'rx',
			'ry',
			'radius',
			'refx',
			'refy',
			'repeatcount',
			'repeatdur',
			'requiredfeatures',
			'restart',
			'result',
			'role',
			'rotate',
			'scale',
			'seed',
			'shape-rendering',
			'spacing',
			'specularconstant',
			'specularexponent',
			'spreadmethod',
			'startoffset',
			'stddeviation',
			'stitchtiles',
			'stop-color',
			'stop-opacity',
			'stroke',
			'stroke-dasharray',
			'stroke-dashoffset',
			'stroke-linecap',
			'stroke-linejoin',
			'stroke-miterlimit',
			'stroke-opacity',
			'stroke-width',
			'style',
			'surfacescale',
			'systemlanguage',
			'tabindex',
			'targetx',
			'targety',
			'transform',
			'transform-origin',
			'text-anchor',
			'text-decoration',
			'text-rendering',
			'textlength',
			'type',
			'u1',
			'u2',
			'underline-position',
			'underline-thickness',
			'unicode',
			'unicode-bidi',
			'values',
			'vector-effect',
			'vert-adv-y',
			'vert-origin-x',
			'vert-origin-y',
			'viewbox',
			'visibility',
			'width',
			'word-spacing',
			'wrap',
			'writing-mode',
			'x',
			'x1',
			'x2',
			'xchannelselector',
			'xlink:href',
			'xlink:title',
			'xmlns',
			'xmlns:se',
			'xmlns:xlink',
			'xml:lang',
			'xml:space',
			'y',
			'y1',
			'y2',
			'ychannelselector',
			'z',
			'zoomandpan',
		];

		/**
		 * Allowed attributes in SVG file.
		 *
		 * Filters the list of allowed attributes in SVG files.
		 *
		 * Since SVG files can run JS code that may inject malicious code, all attributes
		 * are removed except the allowed attributes.
		 *
		 * This hook can be used to manage allowed SVG attributes. To either add new
		 * attributes or delete existing attributes. To strengthen or weaken site security.
		 *
		 * @param array $allowed_attributes A list of allowed attributes.
		 */
		$allowed_attributes = apply_filters( 'elementor/files/svg/allowed_attributes', $allowed_attributes );

		return $allowed_attributes;
	}

	/**
	 * Get Allowed Elements
	 *
	 * Returns an array of allowed element tags to be in SVG files.
	 *
	 * @since 3.16.0
	 * @access private
	 *
	 * @return array
	 */
	private function get_allowed_elements() {
		$allowed_elements = [
			'a',
			'animate',
			'animateMotion',
			'animateTransform',
			'circle',
			'clippath',
			'defs',
			'desc',
			'ellipse',
			'feBlend',
			'feColorMatrix',
			'feComponentTransfer',
			'feComposite',
			'feConvolveMatrix',
			'feDiffuseLighting',
			'feDisplacementMap',
			'feDistantLight',
			'feDropShadow',
			'feFlood',
			'feFuncA',
			'feFuncB',
			'feFuncG',
			'feFuncR',
			'feGaussianBlur',
			'feImage',
			'feMerge',
			'feMergeNode',
			'feMorphology',
			'feOffset',
			'fePointLight',
			'feSpecularLighting',
			'feSpotLight',
			'feTile',
			'feTurbulence',
			'filter',
			'foreignobject',
			'g',
			'image',
			'line',
			'lineargradient',
			'marker',
			'mask',
			'metadata',
			'mpath',
			'path',
			'pattern',
			'polygon',
			'polyline',
			'radialgradient',
			'rect',
			'set',
			'stop',
			'style',
			'svg',
			'switch',
			'symbol',
			'text',
			'textpath',
			'title',
			'tspan',
			'use',
			'view',
		];

		/**
		 * Allowed elements in SVG file.
		 *
		 * Filters the list of allowed elements in SVG files.
		 *
		 * Since SVG files can run JS code that may inject malicious code, all elements
		 * are removed except the allowed elements.
		 *
		 * This hook can be used to manage SVG elements. To either add new elements or
		 * delete existing elements. To strengthen or weaken site security.
		 *
		 * @param array $allowed_elements A list of allowed elements.
		 */
		$allowed_elements = apply_filters( 'elementor/files/svg/allowed_elements', $allowed_elements );

		return $allowed_elements;
	}

	/**
	 * Validate Allowed Attributes
	 *
	 * @since 3.16.0
	 * @access private
	 *
	 * @param \DOMElement $element
	 */
	private function validate_allowed_attributes( $element ) {
		static $allowed_attributes = false;
		if ( false === $allowed_attributes ) {
			$allowed_attributes = $this->get_allowed_attributes();
		}

		for ( $index = $element->attributes->length - 1; $index >= 0; $index-- ) {
			// get attribute name
			$attr_name = $element->attributes->item( $index )->name;
			$attr_name_lowercase = strtolower( $attr_name );
			// Remove attribute if not in whitelist
			if ( ! in_array( $attr_name_lowercase, $allowed_attributes ) && ! $this->is_a_attribute( $attr_name_lowercase, 'aria' ) && ! $this->is_a_attribute( $attr_name_lowercase, 'data' ) ) {
				$element->removeAttribute( $attr_name );
				continue;
			}

			$attr_value = $element->attributes->item( $index )->value;

			// Remove attribute if it has a remote reference or js or data-URI/base64
			if ( ! empty( $attr_value ) && ( $this->is_remote_value( $attr_value ) || $this->has_js_value( $attr_value ) ) ) {
				$element->removeAttribute( $attr_name );
				continue;
			}
		}
	}

	/**
	 * Strip xlinks
	 *
	 * @since 3.16.0
	 * @access private
	 *
	 * @param \DOMElement $element
	 */
	private function strip_xlinks( $element ) {
		$xlinks = $element->getAttributeNS( 'http://www.w3.org/1999/xlink', 'href' );

		if ( ! $xlinks ) {
			return;
		}

		if ( ! $this->is_safe_href( $xlinks ) ) {
			$element->removeAttributeNS( 'http://www.w3.org/1999/xlink', 'href' );
		}
	}

	/**
	 * @see https://github.com/darylldoyle/svg-sanitizer/blob/2321a914e/src/Sanitizer.php#L454
	 */
	private function is_safe_href( $value ) {
		// Allow empty values.
		if ( empty( $value ) ) {
			return true;
		}

		// Allow fragment identifiers.
		if ( '#' === substr( $value, 0, 1 ) ) {
			return true;
		}

		// Allow relative URIs.
		if ( '/' === substr( $value, 0, 1 ) ) {
			return true;
		}

		// Allow HTTPS domains.
		if ( 'https://' === substr( $value, 0, 8 ) ) {
			return true;
		}

		// Allow HTTP domains.
		if ( 'http://' === substr( $value, 0, 7 ) ) {
			return true;
		}

		// Allow known data URIs.
		if ( in_array( substr( $value, 0, 14 ), [
			'data:image/png', // PNG
			'data:image/gif', // GIF
			'data:image/jpg', // JPG
			'data:image/jpe', // JPEG
			'data:image/pjp', // PJPEG
		], true ) ) {
			return true;
		}

		// Allow known short data URIs.
		if ( in_array( substr( $value, 0, 12 ), [
			'data:img/png', // PNG
			'data:img/gif', // GIF
			'data:img/jpg', // JPG
			'data:img/jpe', // JPEG
			'data:img/pjp', // PJPEG
		], true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Validate Use Tag
	 *
	 * @since 3.16.0
	 * @access private
	 *
	 * @param $element
	 */
	private function validate_use_tag( $element ) {
		$xlinks = $element->getAttributeNS( 'http://www.w3.org/1999/xlink', 'href' );
		if ( $xlinks && '#' !== substr( $xlinks, 0, 1 ) ) {
			$element->parentNode->removeChild( $element ); // phpcs:ignore -- php DomNode
		}
	}

	/**
	 * Strip Doctype
	 *
	 * @since 3.16.0
	 * @access private
	 */
	private function strip_doctype() {
		foreach ( $this->svg_dom->childNodes as $child ) {
			if ( XML_DOCUMENT_TYPE_NODE === $child->nodeType ) { // phpcs:ignore -- php DomDocument
				$child->parentNode->removeChild( $child ); // phpcs:ignore -- php DomDocument
			}
		}
	}

	/**
	 * Sanitize Elements
	 *
	 * @since 3.16.0
	 * @access private
	 */
	private function sanitize_elements() {
		$elements = $this->svg_dom->getElementsByTagName( '*' );
		// loop through all elements
		// we do this backwards so we don't skip anything if we delete a node
		// see comments at: http://php.net/manual/en/class.domnamednodemap.php
		for ( $index = $elements->length - 1; $index >= 0; $index-- ) {
			/**
			 * @var \DOMElement $current_element
			 */
			$current_element = $elements->item( $index );
			// If the tag isn't in the whitelist, remove it and continue with next iteration
			if ( ! $this->is_allowed_tag( $current_element ) ) {
				continue;
			}

			// validate element attributes
			$this->validate_allowed_attributes( $current_element );

			$this->strip_xlinks( $current_element );

			if ( 'use' === strtolower( $current_element->tagName ) ) { // phpcs:ignore -- php DomDocument
				$this->validate_use_tag( $current_element );
			}
		}
	}

	/**
	 * Strip PHP Tags
	 *
	 * @since 3.16.0
	 * @access private
	 *
	 * @param string $content
	 * @return string
	 */
	private function strip_php_tags( $content ) {
		$content = preg_replace( '/<\?(=|php)(.+?)\?>/i', '', $content );
		// Remove XML, ASP, etc.
		$content = preg_replace( '/<\?(.*)\?>/Us', '', $content );
		$content = preg_replace( '/<\%(.*)\%>/Us', '', $content );

		if ( ( false !== strpos( $content, '<?' ) ) || ( false !== strpos( $content, '<%' ) ) ) {
			return '';
		}
		return $content;
	}

	/**
	 * Strip Comments
	 *
	 * @since 3.16.0
	 * @access private
	 *
	 * @param string $content
	 * @return string
	 */
	private function strip_comments( $content ) {
		// Remove comments.
		$content = preg_replace( '/<!--(.*)-->/Us', '', $content );
		$content = preg_replace( '/\/\*(.*)\*\//Us', '', $content );
		if ( ( false !== strpos( $content, '<!--' ) ) || ( false !== strpos( $content, '/*' ) ) ) {
			return '';
		}
		return $content;
	}

	/**
	 * Strip Line Breaks
	 *
	 * @since 3.16.0
	 * @access private
	 *
	 * @param string $content
	 * @return string
	 */
	private function strip_line_breaks( $content ) {
		// Remove line breaks.
		return preg_replace( '/\r|\n/', '', $content );
	}
}
