<?php
/**
 * HTML/XHTML filter that only allows some elements and attributes
 *
 * Added wp_ prefix to avoid conflicts with existing kses users
 *
 * @version 0.2.2
 * @copyright (C) 2002, 2003, 2005
 * @author Ulf Harnhammar <metaur@users.sourceforge.net>
 *
 * @package External
 * @subpackage KSES
 *
 * @internal
 * *** CONTACT INFORMATION ***
 * E-mail:      metaur at users dot sourceforge dot net
 * Web page:    http://sourceforge.net/projects/kses
 * Paper mail:  Ulf Harnhammar
 *              Ymergatan 17 C
 *              753 25  Uppsala
 *              SWEDEN
 *
 * [kses strips evil scripts!]
 */

/**
 * You can override this in your my-hacks.php file
 * You can also override this in a plugin file. The
 * my-hacks.php is deprecated in its usage.
 *
 * @since 1.2.0
 */
if (!defined('CUSTOM_TAGS'))
	define('CUSTOM_TAGS', false);

if (!CUSTOM_TAGS) {
	/**
	 * Kses global for default allowable HTML tags
	 *
	 * Can be override by using CUSTOM_TAGS constant
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
		'font' => array(
			'color' => array (),
			'face' => array (),
			'size' => array ()),
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
			'class' => array ()),
		'h2' => array(
			'align' => array (),
			'class' => array ()),
		'h3' => array(
			'align' => array (),
			'class' => array ()),
		'h4' => array(
			'align' => array (),
			'class' => array ()),
		'h5' => array(
			'align' => array (),
			'class' => array ()),
		'h6' => array(
			'align' => array (),
			'class' => array ()),
		'hr' => array(
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
			'style' => array (),
			'title' => array ()),
		'strike' => array(),
		'strong' => array(),
		'sub' => array(),
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
	 * Kses allowed HTML elements
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
}

/**
 * wp_kses() - Filters content and keeps only allowable HTML elements.
 *
 * This function makes sure that only the allowed HTML element names,
 * attribute names and attribute values plus only sane HTML entities
 * will occur in $string. You have to remove any slashes from PHP's
 * magic quotes before you call this function.
 *
 * The default allowed protocols are 'http', 'https', 'ftp', 'mailto',
 * 'news', 'irc', 'gopher', 'nntp', 'feed', and finally 'telnet. This
 * covers all common link protocols, except for 'javascript' which
 * should not be allowed for untrusted users.
 *
 * @since 1.0.0
 *
 * @param string $string Content to filter through kses
 * @param array $allowed_html List of allowed HTML elements
 * @param array $allowed_protocols Optional. Allowed protocol in links.
 * @return string Filtered content with only allowed HTML elements
 */
function wp_kses($string, $allowed_html, $allowed_protocols = array ('http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet')) {
	$string = wp_kses_no_null($string);
	$string = wp_kses_js_entities($string);
	$string = wp_kses_normalize_entities($string);
	$allowed_html_fixed = wp_kses_array_lc($allowed_html);
	$string = wp_kses_hook($string, $allowed_html_fixed, $allowed_protocols); // WP changed the order of these funcs and added args to wp_kses_hook
	return wp_kses_split($string, $allowed_html_fixed, $allowed_protocols);
}

/**
 * wp_kses_hook() - You add any kses hooks here.
 *
 * There is currently only one kses WordPress hook and it is
 * called here. All parameters are passed to the hooks and
 * expected to recieve a string.
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
 * wp_kses_version() - This function returns kses' version number.
 *
 * @since 1.0.0
 *
 * @return string Version Number
 */
function wp_kses_version() {
	return '0.2.2';
}

/**
 * wp_kses_split() - Searches for HTML tags, no matter how malformed
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
	return preg_replace('%((<!--.*?(-->|$))|(<[^>]*(>|$)|>))%e',
	"wp_kses_split2('\\1', \$allowed_html, ".'$allowed_protocols)', $string);
}

/**
 * wp_kses_split2() - Callback for wp_kses_split for fixing malformed HTML tags
 *
 * This function does a lot of work. It rejects some very malformed things
 * like <:::>. It returns an empty string, if the element isn't allowed (look
 * ma, no strip_tags()!). Otherwise it splits the tag into an element and an
 * attribute list.
 *
 * After the tag is split into an element and an attribute list, it is run
 * through another filter which will remove illegal attributes and once
 * that is completed, will be returned.
 *
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

	if (preg_match('%^<!--(.*?)(-->)?$%', $string, $matches)) {
		$string = str_replace(array('<!--', '-->'), '', $matches[1]);
		while ( $string != $newstring = wp_kses($string, $allowed_html, $allowed_protocols) )
			$string = $newstring;
		if ( $string == '' )
			return '';
		return "<!--{$string}-->";
	}
	# Allow HTML comments

	if (!preg_match('%^<\s*(/\s*)?([a-zA-Z0-9]+)([^>]*)>?$%', $string, $matches))
		return '';
	# It's seriously malformed

	$slash = trim($matches[1]);
	$elem = $matches[2];
	$attrlist = $matches[3];

	if (!@isset($allowed_html[strtolower($elem)]))
		return '';
	# They are using a not allowed HTML element

	if ($slash != '')
		return "<$slash$elem>";
	# No attributes are allowed for closing elements

	return wp_kses_attr("$slash$elem", $attrlist, $allowed_html, $allowed_protocols);
}

/**
 * wp_kses_attr() - Removes all attributes, if none are allowed for this element
 *
 * If some are allowed it calls wp_kses_hair() to split them further, and then
 * it builds up new HTML code from the data that kses_hair() returns. It also
 * removes "<" and ">" characters, if there are any left. One more thing it
 * does is to check if the tag has a closing XHTML slash, and if it does, it
 * puts one in the returned code as well.
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
	if (preg_match('%\s/\s*$%', $attr))
		$xhtml_slash = ' /';

	# Are any attributes allowed at all for this element?

	if (@ count($allowed_html[strtolower($element)]) == 0)
		return "<$element$xhtml_slash>";

	# Split it

	$attrarr = wp_kses_hair($attr, $allowed_protocols);

	# Go through $attrarr, and save the allowed attributes for this element
	# in $attr2

	$attr2 = '';

	foreach ($attrarr as $arreach) {
		if (!@ isset ($allowed_html[strtolower($element)][strtolower($arreach['name'])]))
			continue; # the attribute is not allowed

		$current = $allowed_html[strtolower($element)][strtolower($arreach['name'])];
		if ($current == '')
			continue; # the attribute is not allowed

		if (!is_array($current))
			$attr2 .= ' '.$arreach['whole'];
		# there are no checks

		else {
			# there are some checks
			$ok = true;
			foreach ($current as $currkey => $currval)
				if (!wp_kses_check_attr_val($arreach['value'], $arreach['vless'], $currkey, $currval)) {
					$ok = false;
					break;
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
 * wp_kses_hair() - Builds an attribute list from string containing attributes.
 *
 * This function does a lot of work. It parses an attribute list into an array
 * with attribute data, and tries to do the right thing even if it gets weird
 * input. It will add quotes around attribute values that don't have any quotes
 * or apostrophes around them, to make it easier to produce HTML code that will
 * conform to W3C's HTML specification. It will also remove bad URL protocols
 * from attribute values.
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
					$attrarr[] = array ('name' => $attrname, 'value' => '', 'whole' => $attrname, 'vless' => 'y');
					$attr = preg_replace('/^\s+/', '', $attr);
				}

				break;

			case 2 : # attribute value, a URL after href= for instance

				if (preg_match('/^"([^"]*)"(\s+|$)/', $attr, $match))
					# "value"
					{
					$thisval = wp_kses_bad_protocol($match[1], $allowed_protocols);

					$attrarr[] = array ('name' => $attrname, 'value' => $thisval, 'whole' => "$attrname=\"$thisval\"", 'vless' => 'n');
					$working = 1;
					$mode = 0;
					$attr = preg_replace('/^"[^"]*"(\s+|$)/', '', $attr);
					break;
				}

				if (preg_match("/^'([^']*)'(\s+|$)/", $attr, $match))
					# 'value'
					{
					$thisval = wp_kses_bad_protocol($match[1], $allowed_protocols);

					$attrarr[] = array ('name' => $attrname, 'value' => $thisval, 'whole' => "$attrname='$thisval'", 'vless' => 'n');
					$working = 1;
					$mode = 0;
					$attr = preg_replace("/^'[^']*'(\s+|$)/", '', $attr);
					break;
				}

				if (preg_match("%^([^\s\"']+)(\s+|$)%", $attr, $match))
					# value
					{
					$thisval = wp_kses_bad_protocol($match[1], $allowed_protocols);

					$attrarr[] = array ('name' => $attrname, 'value' => $thisval, 'whole' => "$attrname=\"$thisval\"", 'vless' => 'n');
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

	if ($mode == 1)
		# special case, for when the attribute list ends with a valueless
		# attribute like "selected"
		$attrarr[] = array ('name' => $attrname, 'value' => '', 'whole' => $attrname, 'vless' => 'y');

	return $attrarr;
}

/**
 * wp_kses_check_attr_val() - Performs different checks for attribute values.
 *
 * The currently implemented checks are "maxlen", "minlen", "maxval", "minval"
 * and "valueless" with even more checks to come soon.
 *
 * @since 1.0.0
 *
 * @param string $value Attribute value
 * @param string $vless Whether the value is valueless or not. Use 'y' or 'n'
 * @param string $checkname What $checkvalue is checking for.
 * @param mixed $checkvalue What constraint the value should pass
 * @return bool Whether check passes (true) or not (false)
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
			# The minval check checks that the attribute value is a positive integer,
			# and that it is not smaller than the given value.

			if (!preg_match('/^\s{0,6}[0-9]{1,6}\s{0,6}$/', $value))
				$ok = false;
			if ($value < $checkvalue)
				$ok = false;
			break;

		case 'valueless' :
			# The valueless check checks if the attribute has a value
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
 * wp_kses_bad_protocol() - Sanitize string from bad protocols
 *
 * This function removes all non-allowed protocols from the beginning
 * of $string. It ignores whitespace and the case of the letters, and
 * it does understand HTML entities. It does its work in a while loop,
 * so it won't be fooled by a string like "javascript:javascript:alert(57)".
 *
 * @since 1.0.0
 *
 * @param string $string Content to filter bad protocols from
 * @param array $allowed_protocols Allowed protocols to keep
 * @return string Filtered content
 */
function wp_kses_bad_protocol($string, $allowed_protocols) {
	$string = wp_kses_no_null($string);
	$string = preg_replace('/\xad+/', '', $string); # deals with Opera "feature"
	$string2 = $string.'a';

	while ($string != $string2) {
		$string2 = $string;
		$string = wp_kses_bad_protocol_once($string, $allowed_protocols);
	} # while

	return $string;
}

/**
 * wp_kses_no_null() - Removes any NULL characters in $string.
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
 * wp_kses_stripslashes() - Strips slashes from in front of quotes
 *
 * This function changes the character sequence  \"  to just  "
 * It leaves all other slashes alone. It's really weird, but the
 * quoting from preg_replace(//e) seems to require this.
 *
 * @since 1.0.0
 *
 * @param string $string String to strip slashes
 * @return string Fixed strings with quoted slashes
 */
function wp_kses_stripslashes($string) {
	return preg_replace('%\\\\"%', '"', $string);
}

/**
 * wp_kses_array_lc() - Goes through an array and changes the keys to all lower case.
 *
 * @since 1.0.0
 *
 * @param array $inarray Unfiltered array
 * @return array Fixed array with all lowercase keys
 */
function wp_kses_array_lc($inarray) {
	$outarray = array ();

	foreach ($inarray as $inkey => $inval) {
		$outkey = strtolower($inkey);
		$outarray[$outkey] = array ();

		foreach ($inval as $inkey2 => $inval2) {
			$outkey2 = strtolower($inkey2);
			$outarray[$outkey][$outkey2] = $inval2;
		} # foreach $inval
	} # foreach $inarray

	return $outarray;
}

/**
 * wp_kses_js_entities() - Removes the HTML JavaScript entities found in early versions of Netscape 4.
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
 * wp_kses_html_error() - Handles parsing errors in wp_kses_hair()
 *
 * The general plan is to remove everything to and including some
 * whitespace, but it deals with quotes and apostrophes as well.
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
 * wp_kses_bad_protocol_once() - Sanitizes content from bad protocols and other characters
 *
 * This function searches for URL protocols at the beginning of $string,
 * while handling whitespace and HTML entities.
 *
 * @since 1.0.0
 *
 * @param string $string Content to check for bad protocols
 * @param string $allowed_protocols Allowed protocols
 * @return string Sanitized content
 */
function wp_kses_bad_protocol_once($string, $allowed_protocols) {
	global $_kses_allowed_protocols;
	$_kses_allowed_protocols = $allowed_protocols;

	$string2 = preg_split('/:|&#58;|&#x3a;/i', $string, 2);
	if ( isset($string2[1]) && !preg_match('%/\?%', $string2[0]) )
		$string = wp_kses_bad_protocol_once2($string2[0], $allowed_protocols) . trim($string2[1]);
	else
		$string = preg_replace_callback('/^((&[^;]*;|[\sA-Za-z0-9])*)'.'(:|&#58;|&#[Xx]3[Aa];)\s*/', create_function('$matches', 'global $_kses_allowed_protocols; return wp_kses_bad_protocol_once2($matches[1], $_kses_allowed_protocols);'), $string);

	return $string;
}

/**
 * wp_kses_bad_protocol_once2() - Callback for wp_kses_bad_protocol_once() regular expression.
 *
 * This function processes URL protocols, checks to see if they're in the
 * white-list or not, and returns different data depending on the answer.
 *
 * @since 1.0.0
 *
 * @param string $string Content to check for bad protocols
 * @param array $allowed_protocols Allowed protocols
 * @return string Sanitized content
 */
function wp_kses_bad_protocol_once2($string, $allowed_protocols) {
	$string2 = wp_kses_decode_entities($string);
	$string2 = preg_replace('/\s/', '', $string2);
	$string2 = wp_kses_no_null($string2);
	$string2 = preg_replace('/\xad+/', '', $string2);
	# deals with Opera "feature"
	$string2 = strtolower($string2);

	$allowed = false;
	foreach ($allowed_protocols as $one_protocol)
		if (strtolower($one_protocol) == $string2) {
			$allowed = true;
			break;
		}

	if ($allowed)
		return "$string2:";
	else
		return '';
}

/**
 * wp_kses_normalize_entities() - Converts and fixes HTML entities
 *
 * This function normalizes HTML entities. It will convert "AT&T" to the
 * correct "AT&amp;T", "&#00058;" to "&#58;", "&#XYZZY;" to "&amp;#XYZZY;"
 * and so on.
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

	$string = preg_replace('/&amp;([A-Za-z][A-Za-z0-9]{0,19});/', '&\\1;', $string);
	$string = preg_replace_callback('/&amp;#0*([0-9]{1,5});/', create_function('$matches', 'return wp_kses_normalize_entities2($matches[1]);'), $string);
	$string = preg_replace('/&amp;#([Xx])0*(([0-9A-Fa-f]{2}){1,2});/', '&#\\1\\2;', $string);

	return $string;
}

/**
 * wp_kses_normalize_entities2() - Callback for wp_kses_normalize_entities() regular expression
 *
 * This function helps wp_kses_normalize_entities() to only accept 16 bit
 * values and nothing more for &#number; entities.
 *
 * @since 1.0.0
 *
 * @param int $i Number encoded entity
 * @return string Correctly encoded entity
 */
function wp_kses_normalize_entities2($i) {
	return (($i > 65535) ? "&amp;#$i;" : "&#$i;");
}

/**
 * wp_kses_decode_entities() - Convert all entities to their character counterparts.
 *
 * This function decodes numeric HTML entities (&#65; and &#x41;). It
 * doesn't do anything with other entities like &auml;, but we don't need
 * them in the URL protocol whitelisting system anyway.
 *
 * @since 1.0.0
 *
 * @param string $string Content to change entities
 * @return string Content after decoded entities
 */
function wp_kses_decode_entities($string) {
	$string = preg_replace('/&#([0-9]+);/e', 'chr("\\1")', $string);
	$string = preg_replace('/&#[Xx]([0-9A-Fa-f]+);/e', 'chr(hexdec("\\1"))', $string);

	return $string;
}

/**
 * wp_filter_kses() - Sanitize content with allowed HTML Kses rules
 *
 * @since 1.0.0
 * @uses $allowedtags
 *
 * @param string $data Content to filter
 * @return string Filtered content
 */
function wp_filter_kses($data) {
	global $allowedtags;
	return addslashes( wp_kses(stripslashes( $data ), $allowedtags) );
}

/**
 * wp_filter_post_kses() - Sanitize content for allowed HTML tags for post content
 *
 * Post content refers to the page contents of the 'post' type and not
 * $_POST data from forms.
 *
 * @since 2.0.0
 * @uses $allowedposttags
 *
 * @param string $data Post content to filter
 * @return string Filtered post content with allowed HTML tags and attributes intact.
 */
function wp_filter_post_kses($data) {
	global $allowedposttags;
	return addslashes ( wp_kses(stripslashes( $data ), $allowedposttags) );
}

/**
 * wp_filter_nohtml_kses() - Strips all of the HTML in the content
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
 * kses_init_filters() - Adds all Kses input form content filters
 *
 * All hooks have default priority. The wp_filter_kses() fucntion
 * is added to the 'pre_comment_content' and 'title_save_pre'
 * hooks. The wp_filter_post_kses() function is added to the
 * 'content_save_pre', 'excerpt_save_pre', and 'content_filtered_save_pre'
 * hooks.
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
 * kses_remove_filters() - Removes all Kses input form content filters
 *
 * A quick procedural method to removing all of the filters
 * that kses uses for content in WordPress Loop.
 *
 * Does not remove the kses_init() function from 'init' hook
 * (priority is default). Also does not remove kses_init()
 * function from 'set_current_user' hook (priority is also
 * default).
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
 * kses_init() - Sets up most of the Kses filters for input form content
 *
 * If you remove the kses_init() function from 'init' hook and
 * 'set_current_user' (priority is default), then none of the
 * Kses filter hooks will be added.
 *
 * First removes all of the Kses filters in case the current user
 * does not need to have Kses filter the content. If the user does
 * not have unfiltered html capability, then Kses filters are added.
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
?>