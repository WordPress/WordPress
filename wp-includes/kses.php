<?php
/**
 * kses 0.2.2 - HTML/XHTML filter that only allows some elements and attributes
 * Copyright (C) 2002, 2003, 2005  Ulf Harnhammar
 *
 * This program is free software and open source software; you can redistribute
 * it and/or modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of the License,
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA 02110-1301, USA
 * http://www.gnu.org/licenses/gpl.html
 *
 * [kses strips evil scripts!]
 *
 * Added wp_ prefix to avoid conflicts with existing kses users
 *
 * @version 0.2.2
 * @copyright (C) 2002, 2003, 2005
 * @author Ulf Harnhammar <http://advogato.org/person/metaur/>
 *
 * @package External
 * @subpackage KSES
 *
 */

/**
 * You can override this in a plugin.
 *
 * @since 1.2.0
 */
if ( ! defined( 'CUSTOM_TAGS' ) )
	define( 'CUSTOM_TAGS', false );

if ( ! CUSTOM_TAGS ) {
	/**
	 * Kses global for default allowable HTML tags.
	 *
	 * Can be override by using CUSTOM_TAGS constant.
	 *
	 * @global array $allowedposttags
	 * @since 2.0.0
	 */
	$allowedposttags = array(
		'address' => array(),
		'a' => array(
			'class' => array (),
			'href' => array (),
			'id' => array (),
			'title' => array (),
			'rel' => array (),
			'rev' => array (),
			'name' => array (),
			'target' => array()),
		'abbr' => array(
			'class' => array (),
			'title' => array ()),
		'acronym' => array(
			'title' => array ()),
		'article' => array(
			'align' => array (),
			'class' => array (),
			'dir' => array (),
			'lang' => array(),
			'style' => array (),
			'xml:lang' => array(),
		),
		'aside' => array(
			'align' => array (),
			'class' => array (),
			'dir' => array (),
			'lang' => array(),
			'style' => array (),
			'xml:lang' => array(),
		),
		'b' => array(),
		'big' => array(),
		'blockquote' => array(
			'id' => array (),
			'cite' => array (),
			'class' => array(),
			'lang' => array(),
			'xml:lang' => array()),
		'br' => array (
			'class' => array ()),
		'button' => array(
			'disabled' => array (),
			'name' => array (),
			'type' => array (),
			'value' => array ()),
		'caption' => array(
			'align' => array (),
			'class' => array ()),
		'cite' => array (
			'class' => array(),
			'dir' => array(),
			'lang' => array(),
			'title' => array ()),
		'code' => array (
			'style' => array()),
		'col' => array(
			'align' => array (),
			'char' => array (),
			'charoff' => array (),
			'span' => array (),
			'dir' => array(),
			'style' => array (),
			'valign' => array (),
			'width' => array ()),
		'del' => array(
			'datetime' => array ()),
		'dd' => array(),
		'details' => array(
			'align' => array (),
			'class' => array (),
			'dir' => array (),
			'lang' => array(),
			'open' => array (),
			'style' => array (),
			'xml:lang' => array(),
		),
		'div' => array(
			'align' => array (),
			'class' => array (),
			'dir' => array (),
			'lang' => array(),
			'style' => array (),
			'xml:lang' => array()),
		'dl' => array(),
		'dt' => array(),
		'em' => array(),
		'fieldset' => array(),
		'figure' => array(
			'align' => array (),
			'class' => array (),
			'dir' => array (),
			'lang' => array(),
			'style' => array (),
			'xml:lang' => array(),
		),
		'figcaption' => array(
			'align' => array (),
			'class' => array (),
			'dir' => array (),
			'lang' => array(),
			'style' => array (),
			'xml:lang' => array(),
		),
		'font' => array(
			'color' => array (),
			'face' => array (),
			'size' => array ()),
		'footer' => array(
			'align' => array (),
			'class' => array (),
			'dir' => array (),
			'lang' => array(),
			'style' => array (),
			'xml:lang' => array(),
		),
		'form' => array(
			'action' => array (),
			'accept' => array (),
			'accept-charset' => array (),
			'enctype' => array (),
			'method' => array (),
			'name' => array (),
			'target' => array ()),
		'h1' => array(
			'align' => array (),
			'class' => array (),
			'id'    => array (),
			'style' => array ()),
		'h2' => array (
			'align' => array (),
			'class' => array (),
			'id'    => array (),
			'style' => array ()),
		'h3' => array (
			'align' => array (),
			'class' => array (),
			'id'    => array (),
			'style' => array ()),
		'h4' => array (
			'align' => array (),
			'class' => array (),
			'id'    => array (),
			'style' => array ()),
		'h5' => array (
			'align' => array (),
			'class' => array (),
			'id'    => array (),
			'style' => array ()),
		'h6' => array (
			'align' => array (),
			'class' => array (),
			'id'    => array (),
			'style' => array ()),
		'header' => array(
			'align' => array (),
			'class' => array (),
			'dir' => array (),
			'lang' => array(),
			'style' => array (),
			'xml:lang' => array(),
		),
		'hgroup' => array(
			'align' => array (),
			'class' => array (),
			'dir' => array (),
			'lang' => array(),
			'style' => array (),
			'xml:lang' => array(),
		),
		'hr' => array (
			'align' => array (),
			'class' => array (),
			'noshade' => array (),
			'size' => array (),
			'width' => array ()),
		'i' => array(),
		'img' => array(
			'alt' => array (),
			'align' => array (),
			'border' => array (),
			'class' => array (),
			'height' => array (),
			'hspace' => array (),
			'longdesc' => array (),
			'vspace' => array (),
			'src' => array (),
			'style' => array (),
			'width' => array ()),
		'ins' => array(
			'datetime' => array (),
			'cite' => array ()),
		'kbd' => array(),
		'label' => array(
			'for' => array ()),
		'legend' => array(
			'align' => array ()),
		'li' => array (
			'align' => array (),
			'class' => array ()),
		'menu' => array (
			'class' => array (),
			'style' => array (),
			'type' => array ()),
		'nav' => array(
			'align' => array (),
			'class' => array (),
			'dir' => array (),
			'lang' => array(),
			'style' => array (),
			'xml:lang' => array(),
		),
		'p' => array(
			'class' => array (),
			'align' => array (),
			'dir' => array(),
			'lang' => array(),
			'style' => array (),
			'xml:lang' => array()),
		'pre' => array(
			'style' => array(),
			'width' => array ()),
		'q' => array(
			'cite' => array ()),
		's' => array(),
		'span' => array (
			'class' => array (),
			'dir' => array (),
			'align' => array (),
			'lang' => array (),
			'style' => array (),
			'title' => array (),
			'xml:lang' => array()),
		'section' => array(
			'align' => array (),
			'class' => array (),
			'dir' => array (),
			'lang' => array(),
			'style' => array (),
			'xml:lang' => array(),
		),
		'strike' => array(),
		'strong' => array(),
		'sub' => array(),
		'summary' => array(
			'align' => array (),
			'class' => array (),
			'dir' => array (),
			'lang' => array(),
			'style' => array (),
			'xml:lang' => array(),
		),
		'sup' => array(),
		'table' => array(
			'align' => array (),
			'bgcolor' => array (),
			'border' => array (),
			'cellpadding' => array (),
			'cellspacing' => array (),
			'class' => array (),
			'dir' => array(),
			'id' => array(),
			'rules' => array (),
			'style' => array (),
			'summary' => array (),
			'width' => array ()),
		'tbody' => array(
			'align' => array (),
			'char' => array (),
			'charoff' => array (),
			'valign' => array ()),
		'td' => array(
			'abbr' => array (),
			'align' => array (),
			'axis' => array (),
			'bgcolor' => array (),
			'char' => array (),
			'charoff' => array (),
			'class' => array (),
			'colspan' => array (),
			'dir' => array(),
			'headers' => array (),
			'height' => array (),
			'nowrap' => array (),
			'rowspan' => array (),
			'scope' => array (),
			'style' => array (),
			'valign' => array (),
			'width' => array ()),
		'textarea' => array(
			'cols' => array (),
			'rows' => array (),
			'disabled' => array (),
			'name' => array (),
			'readonly' => array ()),
		'tfoot' => array(
			'align' => array (),
			'char' => array (),
			'class' => array (),
			'charoff' => array (),
			'valign' => array ()),
		'th' => array(
			'abbr' => array (),
			'align' => array (),
			'axis' => array (),
			'bgcolor' => array (),
			'char' => array (),
			'charoff' => array (),
			'class' => array (),
			'colspan' => array (),
			'headers' => array (),
			'height' => array (),
			'nowrap' => array (),
			'rowspan' => array (),
			'scope' => array (),
			'valign' => array (),
			'width' => array ()),
		'thead' => array(
			'align' => array (),
			'char' => array (),
			'charoff' => array (),
			'class' => array (),
			'valign' => array ()),
		'title' => array(),
		'tr' => array(
			'align' => array (),
			'bgcolor' => array (),
			'char' => array (),
			'charoff' => array (),
			'class' => array (),
			'style' => array (),
			'valign' => array ()),
		'tt' => array(),
		'u' => array(),
		'ul' => array (
			'class' => array (),
			'style' => array (),
			'type' => array ()),
		'ol' => array (
			'class' => array (),
			'start' => array (),
			'style' => array (),
			'type' => array ()),
		'var' => array ());

	/**
	 * Kses allowed HTML elements.
	 *
	 * @global array $allowedtags
	 * @since 1.0.0
	 */
	$allowedtags = array(
		'a' => array(
			'href' => array (),
			'title' => array ()),
		'abbr' => array(
			'title' => array ()),
		'acronym' => array(
			'title' => array ()),
		'b' => array(),
		'blockquote' => array(
			'cite' => array ()),
		//	'br' => array(),
		'cite' => array (),
		'code' => array(),
		'del' => array(
			'datetime' => array ()),
		//	'dd' => array(),
		//	'dl' => array(),
		//	'dt' => array(),
		'em' => array (), 'i' => array (),
		//	'ins' => array('datetime' => array(), 'cite' => array()),
		//	'li' => array(),
		//	'ol' => array(),
		//	'p' => array(),
		'q' => array(
			'cite' => array ()),
		'strike' => array(),
		'strong' => array(),
		//	'sub' => array(),
		//	'sup' => array(),
		//	'u' => array(),
		//	'ul' => array(),
	);

	$allowedentitynames = array(
		'nbsp',    'iexcl',  'cent',    'pound',  'curren', 'yen',
		'brvbar',  'sect',   'uml',     'copy',   'ordf',   'laquo',
		'not',     'shy',    'reg',     'macr',   'deg',    'plusmn',
		'acute',   'micro',  'para',    'middot', 'cedil',  'ordm',
		'raquo',   'iquest', 'Agrave',  'Aacute', 'Acirc',  'Atilde',
		'Auml',    'Aring',  'AElig',   'Ccedil', 'Egrave', 'Eacute',
		'Ecirc',   'Euml',   'Igrave',  'Iacute', 'Icirc',  'Iuml',
		'ETH',     'Ntilde', 'Ograve',  'Oacute', 'Ocirc',  'Otilde',
		'Ouml',    'times',  'Oslash',  'Ugrave', 'Uacute', 'Ucirc',
		'Uuml',    'Yacute', 'THORN',   'szlig',  'agrave', 'aacute',
		'acirc',   'atilde', 'auml',    'aring',  'aelig',  'ccedil',
		'egrave',  'eacute', 'ecirc',   'euml',   'igrave', 'iacute',
		'icirc',   'iuml',   'eth',     'ntilde', 'ograve', 'oacute',
		'ocirc',   'otilde', 'ouml',    'divide', 'oslash', 'ugrave',
		'uacute',  'ucirc',  'uuml',    'yacute', 'thorn',  'yuml',
		'quot',    'amp',    'lt',      'gt',     'apos',   'OElig',
		'oelig',   'Scaron', 'scaron',  'Yuml',   'circ',   'tilde',
		'ensp',    'emsp',   'thinsp',  'zwnj',   'zwj',    'lrm',
		'rlm',     'ndash',  'mdash',   'lsquo',  'rsquo',  'sbquo',
		'ldquo',   'rdquo',  'bdquo',   'dagger', 'Dagger', 'permil',
		'lsaquo',  'rsaquo', 'euro',    'fnof',   'Alpha',  'Beta',
		'Gamma',   'Delta',  'Epsilon', 'Zeta',   'Eta',    'Theta',
		'Iota',    'Kappa',  'Lambda',  'Mu',     'Nu',     'Xi',
		'Omicron', 'Pi',     'Rho',     'Sigma',  'Tau',    'Upsilon',
		'Phi',     'Chi',    'Psi',     'Omega',  'alpha',  'beta',
		'gamma',   'delta',  'epsilon', 'zeta',   'eta',    'theta',
		'iota',    'kappa',  'lambda',  'mu',     'nu',     'xi',
		'omicron', 'pi',     'rho',     'sigmaf', 'sigma',  'tau',
		'upsilon', 'phi',    'chi',     'psi',    'omega',  'thetasym',
		'upsih',   'piv',    'bull',    'hellip', 'prime',  'Prime',
		'oline',   'frasl',  'weierp',  'image',  'real',   'trade',
		'alefsym', 'larr',   'uarr',    'rarr',   'darr',   'harr',
		'crarr',   'lArr',   'uArr',    'rArr',   'dArr',   'hArr',
		'forall',  'part',   'exist',   'empty',  'nabla',  'isin',
		'notin',   'ni',     'prod',    'sum',    'minus',  'lowast',
		'radic',   'prop',   'infin',   'ang',    'and',    'or',
		'cap',     'cup',    'int',     'sim',    'cong',   'asymp',
		'ne',      'equiv',  'le',      'ge',     'sub',    'sup',
		'nsub',    'sube',   'supe',    'oplus',  'otimes', 'perp',
		'sdot',    'lceil',  'rceil',   'lfloor', 'rfloor', 'lang',
		'rang',    'loz',    'spades',  'clubs',  'hearts', 'diams',
	);
}

/**
 * Filters content and keeps only allowable HTML elements.
 *
 * This function makes sure that only the allowed HTML element names, attribute
 * names and attribute values plus only sane HTML entities will occur in
 * $string. You have to remove any slashes from PHP's magic quotes before you
 * call this function.
 *
 * The default allowed protocols are 'http', 'https', 'ftp', 'mailto', 'news',
 * 'irc', 'gopher', 'nntp', 'feed', 'telnet, 'mms', 'rtsp' and 'svn'. This
 * covers all common link protocols, except for 'javascript' which should not
 * be allowed for untrusted users.
 *
 * @since 1.0.0
 *
 * @param string $string Content to filter through kses
 * @param array $allowed_html List of allowed HTML elements
 * @param array $allowed_protocols Optional. Allowed protocol in links.
 * @return string Filtered content with only allowed HTML elements
 */
function wp_kses($string, $allowed_html, $allowed_protocols = array ()) {
	$allowed_protocols = wp_parse_args( $allowed_protocols, wp_allowed_protocols() );
	$string = wp_kses_no_null($string);
	$string = wp_kses_js_entities($string);
	$string = wp_kses_normalize_entities($string);
	$allowed_html_fixed = wp_kses_array_lc($allowed_html);
	$string = wp_kses_hook($string, $allowed_html_fixed, $allowed_protocols); // WP changed the order of these funcs and added args to wp_kses_hook
	return wp_kses_split($string, $allowed_html_fixed, $allowed_protocols);
}

/**
 * You add any kses hooks here.
 *
 * There is currently only one kses WordPress hook and it is called here. All
 * parameters are passed to the hooks and expected to receive a string.
 *
 * @since 1.0.0
 *
 * @param string $string Content to filter through kses
 * @param array $allowed_html List of allowed HTML elements
 * @param array $allowed_protocols Allowed protocol in links
 * @return string Filtered content through 'pre_kses' hook
 */
function wp_kses_hook($string, $allowed_html, $allowed_protocols) {
	$string = apply_filters('pre_kses', $string, $allowed_html, $allowed_protocols);
	return $string;
}

/**
 * This function returns kses' version number.
 *
 * @since 1.0.0
 *
 * @return string KSES Version Number
 */
function wp_kses_version() {
	return '0.2.2';
}

/**
 * Searches for HTML tags, no matter how malformed.
 *
 * It also matches stray ">" characters.
 *
 * @since 1.0.0
 *
 * @param string $string Content to filter
 * @param array $allowed_html Allowed HTML elements
 * @param array $allowed_protocols Allowed protocols to keep
 * @return string Content with fixed HTML tags
 */
function wp_kses_split($string, $allowed_html, $allowed_protocols) {
	global $pass_allowed_html, $pass_allowed_protocols;
	$pass_allowed_html = $allowed_html;
	$pass_allowed_protocols = $allowed_protocols;
	return preg_replace_callback( '%(<!--.*?(-->|$))|(<[^>]*(>|$)|>)%', '_wp_kses_split_callback', $string );
}

/**
 * Callback for wp_kses_split.
 *
 * @since 3.1.0
 * @access private
 */
function _wp_kses_split_callback( $match ) {
	global $pass_allowed_html, $pass_allowed_protocols;
	return wp_kses_split2( $match[0], $pass_allowed_html, $pass_allowed_protocols );
}

/**
 * Callback for wp_kses_split for fixing malformed HTML tags.
 *
 * This function does a lot of work. It rejects some very malformed things like
 * <:::>. It returns an empty string, if the element isn't allowed (look ma, no
 * strip_tags()!). Otherwise it splits the tag into an element and an attribute
 * list.
 *
 * After the tag is split into an element and an attribute list, it is run
 * through another filter which will remove illegal attributes and once that is
 * completed, will be returned.
 *
 * @access private
 * @since 1.0.0
 * @uses wp_kses_attr()
 *
 * @param string $string Content to filter
 * @param array $allowed_html Allowed HTML elements
 * @param array $allowed_protocols Allowed protocols to keep
 * @return string Fixed HTML element
 */
function wp_kses_split2($string, $allowed_html, $allowed_protocols) {
	$string = wp_kses_stripslashes($string);

	if (substr($string, 0, 1) != '<')
		return '&gt;';
	# It matched a ">" character

	if ( '<!--' == substr( $string, 0, 4 ) ) {
		$string = str_replace( array('<!--', '-->'), '', $string );
		while ( $string != ($newstring = wp_kses($string, $allowed_html, $allowed_protocols)) )
			$string = $newstring;
		if ( $string == '' )
			return '';
		// prevent multiple dashes in comments
		$string = preg_replace('/--+/', '-', $string);
		// prevent three dashes closing a comment
		$string = preg_replace('/-$/', '', $string);
		return "<!--{$string}-->";
	}
	# Allow HTML comments

	if (!preg_match('%^<\s*(/\s*)?([a-zA-Z0-9]+)([^>]*)>?$%', $string, $matches))
		return '';
	# It's seriously malformed

	$slash = trim($matches[1]);
	$elem = $matches[2];
	$attrlist = $matches[3];

	if ( ! isset($allowed_html[strtolower($elem)]) )
		return '';
	# They are using a not allowed HTML element

	if ($slash != '')
		return "</$elem>";
	# No attributes are allowed for closing elements

	return wp_kses_attr( $elem, $attrlist, $allowed_html, $allowed_protocols );
}

/**
 * Removes all attributes, if none are allowed for this element.
 *
 * If some are allowed it calls wp_kses_hair() to split them further, and then
 * it builds up new HTML code from the data that kses_hair() returns. It also
 * removes "<" and ">" characters, if there are any left. One more thing it does
 * is to check if the tag has a closing XHTML slash, and if it does, it puts one
 * in the returned code as well.
 *
 * @since 1.0.0
 *
 * @param string $element HTML element/tag
 * @param string $attr HTML attributes from HTML element to closing HTML element tag
 * @param array $allowed_html Allowed HTML elements
 * @param array $allowed_protocols Allowed protocols to keep
 * @return string Sanitized HTML element
 */
function wp_kses_attr($element, $attr, $allowed_html, $allowed_protocols) {
	# Is there a closing XHTML slash at the end of the attributes?

	$xhtml_slash = '';
	if (preg_match('%\s*/\s*$%', $attr))
		$xhtml_slash = ' /';

	# Are any attributes allowed at all for this element?
	if ( ! isset($allowed_html[strtolower($element)]) || count($allowed_html[strtolower($element)]) == 0 )
		return "<$element$xhtml_slash>";

	# Split it
	$attrarr = wp_kses_hair($attr, $allowed_protocols);

	# Go through $attrarr, and save the allowed attributes for this element
	# in $attr2
	$attr2 = '';

	$allowed_attr = $allowed_html[strtolower($element)];
	foreach ($attrarr as $arreach) {
		if ( ! isset( $allowed_attr[strtolower($arreach['name'])] ) )
			continue; # the attribute is not allowed

		$current = $allowed_attr[strtolower($arreach['name'])];
		if ( $current == '' )
			continue; # the attribute is not allowed

		if ( ! is_array($current) ) {
			$attr2 .= ' '.$arreach['whole'];
		# there are no checks

		} else {
			# there are some checks
			$ok = true;
			foreach ($current as $currkey => $currval) {
				if ( ! wp_kses_check_attr_val($arreach['value'], $arreach['vless'], $currkey, $currval) ) {
					$ok = false;
					break;
				}
			}

			if ( strtolower($arreach['name']) == 'style' ) {
				$orig_value = $arreach['value'];
				$value = safecss_filter_attr($orig_value);

				if ( empty($value) )
					continue;

				$arreach['value'] = $value;
				$arreach['whole'] = str_replace($orig_value, $value, $arreach['whole']);
			}

			if ($ok)
				$attr2 .= ' '.$arreach['whole']; # it passed them
		} # if !is_array($current)
	} # foreach

	# Remove any "<" or ">" characters
	$attr2 = preg_replace('/[<>]/', '', $attr2);

	return "<$element$attr2$xhtml_slash>";
}

/**
 * Builds an attribute list from string containing attributes.
 *
 * This function does a lot of work. It parses an attribute list into an array
 * with attribute data, and tries to do the right thing even if it gets weird
 * input. It will add quotes around attribute values that don't have any quotes
 * or apostrophes around them, to make it easier to produce HTML code that will
 * conform to W3C's HTML specification. It will also remove bad URL protocols
 * from attribute values.  It also reduces duplicate attributes by using the
 * attribute defined first (foo='bar' foo='baz' will result in foo='bar').
 *
 * @since 1.0.0
 *
 * @param string $attr Attribute list from HTML element to closing HTML element tag
 * @param array $allowed_protocols Allowed protocols to keep
 * @return array List of attributes after parsing
 */
function wp_kses_hair($attr, $allowed_protocols) {
	$attrarr = array ();
	$mode = 0;
	$attrname = '';
	$uris = array('xmlns', 'profile', 'href', 'src', 'cite', 'classid', 'codebase', 'data', 'usemap', 'longdesc', 'action');

	# Loop through the whole attribute list

	while (strlen($attr) != 0) {
		$working = 0; # Was the last operation successful?

		switch ($mode) {
			case 0 : # attribute name, href for instance

				if (preg_match('/^([-a-zA-Z]+)/', $attr, $match)) {
					$attrname = $match[1];
					$working = $mode = 1;
					$attr = preg_replace('/^[-a-zA-Z]+/', '', $attr);
				}

				break;

			case 1 : # equals sign or valueless ("selected")

				if (preg_match('/^\s*=\s*/', $attr)) # equals sign
					{
					$working = 1;
					$mode = 2;
					$attr = preg_replace('/^\s*=\s*/', '', $attr);
					break;
				}

				if (preg_match('/^\s+/', $attr)) # valueless
					{
					$working = 1;
					$mode = 0;
					if(FALSE === array_key_exists($attrname, $attrarr)) {
						$attrarr[$attrname] = array ('name' => $attrname, 'value' => '', 'whole' => $attrname, 'vless' => 'y');
					}
					$attr = preg_replace('/^\s+/', '', $attr);
				}

				break;

			case 2 : # attribute value, a URL after href= for instance

				if (preg_match('%^"([^"]*)"(\s+|/?$)%', $attr, $match))
					# "value"
					{
					$thisval = $match[1];
					if ( in_array(strtolower($attrname), $uris) )
						$thisval = wp_kses_bad_protocol($thisval, $allowed_protocols);

					if(FALSE === array_key_exists($attrname, $attrarr)) {
						$attrarr[$attrname] = array ('name' => $attrname, 'value' => $thisval, 'whole' => "$attrname=\"$thisval\"", 'vless' => 'n');
					}
					$working = 1;
					$mode = 0;
					$attr = preg_replace('/^"[^"]*"(\s+|$)/', '', $attr);
					break;
				}

				if (preg_match("%^'([^']*)'(\s+|/?$)%", $attr, $match))
					# 'value'
					{
					$thisval = $match[1];
					if ( in_array(strtolower($attrname), $uris) )
						$thisval = wp_kses_bad_protocol($thisval, $allowed_protocols);

					if(FALSE === array_key_exists($attrname, $attrarr)) {
						$attrarr[$attrname] = array ('name' => $attrname, 'value' => $thisval, 'whole' => "$attrname='$thisval'", 'vless' => 'n');
					}
					$working = 1;
					$mode = 0;
					$attr = preg_replace("/^'[^']*'(\s+|$)/", '', $attr);
					break;
				}

				if (preg_match("%^([^\s\"']+)(\s+|/?$)%", $attr, $match))
					# value
					{
					$thisval = $match[1];
					if ( in_array(strtolower($attrname), $uris) )
						$thisval = wp_kses_bad_protocol($thisval, $allowed_protocols);

					if(FALSE === array_key_exists($attrname, $attrarr)) {
						$attrarr[$attrname] = array ('name' => $attrname, 'value' => $thisval, 'whole' => "$attrname=\"$thisval\"", 'vless' => 'n');
					}
					# We add quotes to conform to W3C's HTML spec.
					$working = 1;
					$mode = 0;
					$attr = preg_replace("%^[^\s\"']+(\s+|$)%", '', $attr);
				}

				break;
		} # switch

		if ($working == 0) # not well formed, remove and try again
		{
			$attr = wp_kses_html_error($attr);
			$mode = 0;
		}
	} # while

	if ($mode == 1 && FALSE === array_key_exists($attrname, $attrarr))
		# special case, for when the attribute list ends with a valueless
		# attribute like "selected"
		$attrarr[$attrname] = array ('name' => $attrname, 'value' => '', 'whole' => $attrname, 'vless' => 'y');

	return $attrarr;
}

/**
 * Performs different checks for attribute values.
 *
 * The currently implemented checks are "maxlen", "minlen", "maxval", "minval"
 * and "valueless".
 *
 * @since 1.0.0
 *
 * @param string $value Attribute value
 * @param string $vless Whether the value is valueless. Use 'y' or 'n'
 * @param string $checkname What $checkvalue is checking for.
 * @param mixed $checkvalue What constraint the value should pass
 * @return bool Whether check passes
 */
function wp_kses_check_attr_val($value, $vless, $checkname, $checkvalue) {
	$ok = true;

	switch (strtolower($checkname)) {
		case 'maxlen' :
			# The maxlen check makes sure that the attribute value has a length not
			# greater than the given value. This can be used to avoid Buffer Overflows
			# in WWW clients and various Internet servers.

			if (strlen($value) > $checkvalue)
				$ok = false;
			break;

		case 'minlen' :
			# The minlen check makes sure that the attribute value has a length not
			# smaller than the given value.

			if (strlen($value) < $checkvalue)
				$ok = false;
			break;

		case 'maxval' :
			# The maxval check does two things: it checks that the attribute value is
			# an integer from 0 and up, without an excessive amount of zeroes or
			# whitespace (to avoid Buffer Overflows). It also checks that the attribute
			# value is not greater than the given value.
			# This check can be used to avoid Denial of Service attacks.

			if (!preg_match('/^\s{0,6}[0-9]{1,6}\s{0,6}$/', $value))
				$ok = false;
			if ($value > $checkvalue)
				$ok = false;
			break;

		case 'minval' :
			# The minval check makes sure that the attribute value is a positive integer,
			# and that it is not smaller than the given value.

			if (!preg_match('/^\s{0,6}[0-9]{1,6}\s{0,6}$/', $value))
				$ok = false;
			if ($value < $checkvalue)
				$ok = false;
			break;

		case 'valueless' :
			# The valueless check makes sure if the attribute has a value
			# (like <a href="blah">) or not (<option selected>). If the given value
			# is a "y" or a "Y", the attribute must not have a value.
			# If the given value is an "n" or an "N", the attribute must have one.

			if (strtolower($checkvalue) != $vless)
				$ok = false;
			break;
	} # switch

	return $ok;
}

/**
 * Sanitize string from bad protocols.
 *
 * This function removes all non-allowed protocols from the beginning of
 * $string. It ignores whitespace and the case of the letters, and it does
 * understand HTML entities. It does its work in a while loop, so it won't be
 * fooled by a string like "javascript:javascript:alert(57)".
 *
 * @since 1.0.0
 *
 * @param string $string Content to filter bad protocols from
 * @param array $allowed_protocols Allowed protocols to keep
 * @return string Filtered content
 */
function wp_kses_bad_protocol($string, $allowed_protocols) {
	$string = wp_kses_no_null($string);
	$string2 = $string.'a';

	while ($string != $string2) {
		$string2 = $string;
		$string = wp_kses_bad_protocol_once($string, $allowed_protocols);
	} # while

	return $string;
}

/**
 * Removes any NULL characters in $string.
 *
 * @since 1.0.0
 *
 * @param string $string
 * @return string
 */
function wp_kses_no_null($string) {
	$string = preg_replace('/\0+/', '', $string);
	$string = preg_replace('/(\\\\0)+/', '', $string);

	return $string;
}

/**
 * Strips slashes from in front of quotes.
 *
 * This function changes the character sequence \" to just ". It leaves all
 * other slashes alone. It's really weird, but the quoting from
 * preg_replace(//e) seems to require this.
 *
 * @since 1.0.0
 *
 * @param string $string String to strip slashes
 * @return string Fixed string with quoted slashes
 */
function wp_kses_stripslashes($string) {
	return preg_replace('%\\\\"%', '"', $string);
}

/**
 * Goes through an array and changes the keys to all lower case.
 *
 * @since 1.0.0
 *
 * @param array $inarray Unfiltered array
 * @return array Fixed array with all lowercase keys
 */
function wp_kses_array_lc($inarray) {
	$outarray = array ();

	foreach ( (array) $inarray as $inkey => $inval) {
		$outkey = strtolower($inkey);
		$outarray[$outkey] = array ();

		foreach ( (array) $inval as $inkey2 => $inval2) {
			$outkey2 = strtolower($inkey2);
			$outarray[$outkey][$outkey2] = $inval2;
		} # foreach $inval
	} # foreach $inarray

	return $outarray;
}

/**
 * Removes the HTML JavaScript entities found in early versions of Netscape 4.
 *
 * @since 1.0.0
 *
 * @param string $string
 * @return string
 */
function wp_kses_js_entities($string) {
	return preg_replace('%&\s*\{[^}]*(\}\s*;?|$)%', '', $string);
}

/**
 * Handles parsing errors in wp_kses_hair().
 *
 * The general plan is to remove everything to and including some whitespace,
 * but it deals with quotes and apostrophes as well.
 *
 * @since 1.0.0
 *
 * @param string $string
 * @return string
 */
function wp_kses_html_error($string) {
	return preg_replace('/^("[^"]*("|$)|\'[^\']*(\'|$)|\S)*\s*/', '', $string);
}

/**
 * Sanitizes content from bad protocols and other characters.
 *
 * This function searches for URL protocols at the beginning of $string, while
 * handling whitespace and HTML entities.
 *
 * @since 1.0.0
 *
 * @param string $string Content to check for bad protocols
 * @param string $allowed_protocols Allowed protocols
 * @return string Sanitized content
 */
function wp_kses_bad_protocol_once($string, $allowed_protocols) {
	$string2 = preg_split( '/:|&#0*58;|&#x0*3a;/i', $string, 2 );
	if ( isset($string2[1]) && ! preg_match('%/\?%', $string2[0]) )
		$string = wp_kses_bad_protocol_once2( $string2[0], $allowed_protocols ) . trim( $string2[1] );

	return $string;
}

/**
 * Callback for wp_kses_bad_protocol_once() regular expression.
 *
 * This function processes URL protocols, checks to see if they're in the
 * whitelist or not, and returns different data depending on the answer.
 *
 * @access private
 * @since 1.0.0
 *
 * @param string $string URI scheme to check against the whitelist
 * @param string $allowed_protocols Allowed protocols
 * @return string Sanitized content
 */
function wp_kses_bad_protocol_once2( $string, $allowed_protocols ) {
	$string2 = wp_kses_decode_entities($string);
	$string2 = preg_replace('/\s/', '', $string2);
	$string2 = wp_kses_no_null($string2);
	$string2 = strtolower($string2);

	$allowed = false;
	foreach ( (array) $allowed_protocols as $one_protocol )
		if ( strtolower($one_protocol) == $string2 ) {
			$allowed = true;
			break;
		}

	if ($allowed)
		return "$string2:";
	else
		return '';
}

/**
 * Converts and fixes HTML entities.
 *
 * This function normalizes HTML entities. It will convert "AT&T" to the correct
 * "AT&amp;T", "&#00058;" to "&#58;", "&#XYZZY;" to "&amp;#XYZZY;" and so on.
 *
 * @since 1.0.0
 *
 * @param string $string Content to normalize entities
 * @return string Content with normalized entities
 */
function wp_kses_normalize_entities($string) {
	# Disarm all entities by converting & to &amp;

	$string = str_replace('&', '&amp;', $string);

	# Change back the allowed entities in our entity whitelist

	$string = preg_replace_callback('/&amp;([A-Za-z]{2,8});/', 'wp_kses_named_entities', $string);
	$string = preg_replace_callback('/&amp;#(0*[0-9]{1,7});/', 'wp_kses_normalize_entities2', $string);
	$string = preg_replace_callback('/&amp;#[Xx](0*[0-9A-Fa-f]{1,6});/', 'wp_kses_normalize_entities3', $string);

	return $string;
}

/**
 * Callback for wp_kses_normalize_entities() regular expression.
 *
 * This function only accepts valid named entity references, which are finite,
 * case-sensitive, and highly scrutinized by HTML and XML validators.
 *
 * @since 3.0.0
 *
 * @param array $matches preg_replace_callback() matches array
 * @return string Correctly encoded entity
 */
function wp_kses_named_entities($matches) {
	global $allowedentitynames;

	if ( empty($matches[1]) )
		return '';

	$i = $matches[1];
	return ( ( ! in_array($i, $allowedentitynames) ) ? "&amp;$i;" : "&$i;" );
}

/**
 * Callback for wp_kses_normalize_entities() regular expression.
 *
 * This function helps wp_kses_normalize_entities() to only accept 16-bit values
 * and nothing more for &#number; entities.
 *
 * @access private
 * @since 1.0.0
 *
 * @param array $matches preg_replace_callback() matches array
 * @return string Correctly encoded entity
 */
function wp_kses_normalize_entities2($matches) {
	if ( empty($matches[1]) )
		return '';

	$i = $matches[1];
	if (valid_unicode($i)) {
		$i = str_pad(ltrim($i,'0'), 3, '0', STR_PAD_LEFT);
		$i = "&#$i;";
	} else {
		$i = "&amp;#$i;";
	}

	return $i;
}

/**
 * Callback for wp_kses_normalize_entities() for regular expression.
 *
 * This function helps wp_kses_normalize_entities() to only accept valid Unicode
 * numeric entities in hex form.
 *
 * @access private
 *
 * @param array $matches preg_replace_callback() matches array
 * @return string Correctly encoded entity
 */
function wp_kses_normalize_entities3($matches) {
	if ( empty($matches[1]) )
		return '';

	$hexchars = $matches[1];
	return ( ( ! valid_unicode(hexdec($hexchars)) ) ? "&amp;#x$hexchars;" : '&#x'.ltrim($hexchars,'0').';' );
}

/**
 * Helper function to determine if a Unicode value is valid.
 *
 * @param int $i Unicode value
 * @return bool True if the value was a valid Unicode number
 */
function valid_unicode($i) {
	return ( $i == 0x9 || $i == 0xa || $i == 0xd ||
			($i >= 0x20 && $i <= 0xd7ff) ||
			($i >= 0xe000 && $i <= 0xfffd) ||
			($i >= 0x10000 && $i <= 0x10ffff) );
}

/**
 * Convert all entities to their character counterparts.
 *
 * This function decodes numeric HTML entities (&#65; and &#x41;). It doesn't do
 * anything with other entities like &auml;, but we don't need them in the URL
 * protocol whitelisting system anyway.
 *
 * @since 1.0.0
 *
 * @param string $string Content to change entities
 * @return string Content after decoded entities
 */
function wp_kses_decode_entities($string) {
	$string = preg_replace_callback('/&#([0-9]+);/', '_wp_kses_decode_entities_chr', $string);
	$string = preg_replace_callback('/&#[Xx]([0-9A-Fa-f]+);/', '_wp_kses_decode_entities_chr_hexdec', $string);

	return $string;
}

/**
 * Regex callback for wp_kses_decode_entities()
 *
 * @param array $match preg match
 * @return string
 */
function _wp_kses_decode_entities_chr( $match ) {
	return chr( $match[1] );
}

/**
 * Regex callback for wp_kses_decode_entities()
 *
 * @param array $match preg match
 * @return string
 */
function _wp_kses_decode_entities_chr_hexdec( $match ) {
	return chr( hexdec( $match[1] ) );
}

/**
 * Sanitize content with allowed HTML Kses rules.
 *
 * @since 1.0.0
 * @uses $allowedtags
 *
 * @param string $data Content to filter, expected to be escaped with slashes
 * @return string Filtered content
 */
function wp_filter_kses($data) {
	global $allowedtags;
	return addslashes( wp_kses(stripslashes( $data ), $allowedtags) );
}

/**
 * Sanitize content with allowed HTML Kses rules.
 *
 * @since 2.9.0
 * @uses $allowedtags
 *
 * @param string $data Content to filter, expected to not be escaped
 * @return string Filtered content
 */
function wp_kses_data($data) {
	global $allowedtags;
	return wp_kses( $data , $allowedtags );
}

/**
 * Sanitize content for allowed HTML tags for post content.
 *
 * Post content refers to the page contents of the 'post' type and not $_POST
 * data from forms.
 *
 * @since 2.0.0
 * @uses $allowedposttags
 *
 * @param string $data Post content to filter, expected to be escaped with slashes
 * @return string Filtered post content with allowed HTML tags and attributes intact.
 */
function wp_filter_post_kses($data) {
	global $allowedposttags;
	return addslashes ( wp_kses(stripslashes( $data ), $allowedposttags) );
}

/**
 * Sanitize content for allowed HTML tags for post content.
 *
 * Post content refers to the page contents of the 'post' type and not $_POST
 * data from forms.
 *
 * @since 2.9.0
 * @uses $allowedposttags
 *
 * @param string $data Post content to filter
 * @return string Filtered post content with allowed HTML tags and attributes intact.
 */
function wp_kses_post($data) {
	global $allowedposttags;
	return wp_kses( $data , $allowedposttags );
}

/**
 * Strips all of the HTML in the content.
 *
 * @since 2.1.0
 *
 * @param string $data Content to strip all HTML from
 * @return string Filtered content without any HTML
 */
function wp_filter_nohtml_kses($data) {
	return addslashes ( wp_kses(stripslashes( $data ), array()) );
}

/**
 * Adds all Kses input form content filters.
 *
 * All hooks have default priority. The wp_filter_kses() function is added to
 * the 'pre_comment_content' and 'title_save_pre' hooks.
 *
 * The wp_filter_post_kses() function is added to the 'content_save_pre',
 * 'excerpt_save_pre', and 'content_filtered_save_pre' hooks.
 *
 * @since 2.0.0
 * @uses add_filter() See description for what functions are added to what hooks.
 */
function kses_init_filters() {
	// Normal filtering.
	add_filter('pre_comment_content', 'wp_filter_kses');
	add_filter('title_save_pre', 'wp_filter_kses');

	// Post filtering
	add_filter('content_save_pre', 'wp_filter_post_kses');
	add_filter('excerpt_save_pre', 'wp_filter_post_kses');
	add_filter('content_filtered_save_pre', 'wp_filter_post_kses');
}

/**
 * Removes all Kses input form content filters.
 *
 * A quick procedural method to removing all of the filters that kses uses for
 * content in WordPress Loop.
 *
 * Does not remove the kses_init() function from 'init' hook (priority is
 * default). Also does not remove kses_init() function from 'set_current_user'
 * hook (priority is also default).
 *
 * @since 2.0.6
 */
function kses_remove_filters() {
	// Normal filtering.
	remove_filter('pre_comment_content', 'wp_filter_kses');
	remove_filter('title_save_pre', 'wp_filter_kses');

	// Post filtering
	remove_filter('content_save_pre', 'wp_filter_post_kses');
	remove_filter('excerpt_save_pre', 'wp_filter_post_kses');
	remove_filter('content_filtered_save_pre', 'wp_filter_post_kses');
}

/**
 * Sets up most of the Kses filters for input form content.
 *
 * If you remove the kses_init() function from 'init' hook and
 * 'set_current_user' (priority is default), then none of the Kses filter hooks
 * will be added.
 *
 * First removes all of the Kses filters in case the current user does not need
 * to have Kses filter the content. If the user does not have unfiltered_html
 * capability, then Kses filters are added.
 *
 * @uses kses_remove_filters() Removes the Kses filters
 * @uses kses_init_filters() Adds the Kses filters back if the user
 *		does not have unfiltered HTML capability.
 * @since 2.0.0
 */
function kses_init() {
	kses_remove_filters();

	if (current_user_can('unfiltered_html') == false)
		kses_init_filters();
}

add_action('init', 'kses_init');
add_action('set_current_user', 'kses_init');

/**
 * Inline CSS filter
 *
 * @since 2.8.1
 */
function safecss_filter_attr( $css, $deprecated = '' ) {
	if ( !empty( $deprecated ) )
		_deprecated_argument( __FUNCTION__, '2.8.1' ); // Never implemented

	$css = wp_kses_no_null($css);
	$css = str_replace(array("\n","\r","\t"), '', $css);

	if ( preg_match( '%[\\(&=}]|/\*%', $css ) ) // remove any inline css containing \ ( & } = or comments
		return '';

	$css_array = explode( ';', trim( $css ) );
	$allowed_attr = apply_filters( 'safe_style_css', array( 'text-align', 'margin', 'color', 'float',
	'border', 'background', 'background-color', 'border-bottom', 'border-bottom-color',
	'border-bottom-style', 'border-bottom-width', 'border-collapse', 'border-color', 'border-left',
	'border-left-color', 'border-left-style', 'border-left-width', 'border-right', 'border-right-color',
	'border-right-style', 'border-right-width', 'border-spacing', 'border-style', 'border-top',
	'border-top-color', 'border-top-style', 'border-top-width', 'border-width', 'caption-side',
	'clear', 'cursor', 'direction', 'font', 'font-family', 'font-size', 'font-style',
	'font-variant', 'font-weight', 'height', 'letter-spacing', 'line-height', 'margin-bottom',
	'margin-left', 'margin-right', 'margin-top', 'overflow', 'padding', 'padding-bottom',
	'padding-left', 'padding-right', 'padding-top', 'text-decoration', 'text-indent', 'vertical-align',
	'width' ) );

	if ( empty($allowed_attr) )
		return $css;

	$css = '';
	foreach ( $css_array as $css_item ) {
		if ( $css_item == '' )
			continue;
		$css_item = trim( $css_item );
		$found = false;
		if ( strpos( $css_item, ':' ) === false ) {
			$found = true;
		} else {
			$parts = split( ':', $css_item );
			if ( in_array( trim( $parts[0] ), $allowed_attr ) )
				$found = true;
		}
		if ( $found ) {
			if( $css != '' )
				$css .= ';';
			$css .= $css_item;
		}
	}

	return $css;
}
