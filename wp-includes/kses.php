<?php
// Added wp_ prefix to avoid conflicts with existing kses users
# kses 0.2.1 - HTML/XHTML filter that only allows some elements and attributes
# Copyright (C) 2002, 2003  Ulf Harnhammar
# *** CONTACT INFORMATION ***
#
# E-mail:      metaur at users dot sourceforge dot net
# Web page:    http://sourceforge.net/projects/kses
# Paper mail:  (not at the moment)
#
# [kses strips evil scripts!]
if (!defined('CUSTOM_TAGS'))
	define('CUSTOM_TAGS', false);
	
// You can override this in your my-hacks.php file
if (!CUSTOM_TAGS) {
$allowedtags = array(
				'a' => array(
					'href' => array(),
					'title' => array(),
					'rel' => array()),
				'abbr' => array('title' => array()),
				'acronym' => array('title' => array()),
				'b' => array(),
				'blockquote' => array('cite' => array()),
//				'br' => array(),
				'code' => array(),
//				'del' => array('datetime' => array()),
//				'dd' => array(),
//				'dl' => array(),
//				'dt' => array(),
				'em' => array(),
				'i' => array(),
//				'ins' => array('datetime' => array(), 'cite' => array()),
//				'li' => array(),
//				'ol' => array(),
//				'p' => array(),
//				'q' => array(),
				'strike' => array(),
				'strong' => array(),
//				'sub' => array(),
//				'sup' => array(),
//				'u' => array(),
//				'ul' => array(),
				);
}
function wp_kses($string, $allowed_html, $allowed_protocols =
               array('http', 'https', 'ftp', 'news', 'nntp', 'telnet',
                     'gopher', 'mailto'))
###############################################################################
# This function makes sure that only the allowed HTML element names, attribute
# names and attribute values plus only sane HTML entities will occur in
# $string. You have to remove any slashes from PHP's magic quotes before you
# call this function.
###############################################################################
{
  $string = wp_kses_no_null($string);
  $string = wp_kses_js_entities($string);
  $string = wp_kses_normalize_entities($string);
  $string = wp_kses_hook($string);
  $allowed_html_fixed = wp_kses_array_lc($allowed_html);
  return wp_kses_split($string, $allowed_html_fixed, $allowed_protocols);
} # function wp_kses


function wp_kses_hook($string)
###############################################################################
# You add any kses hooks here.
###############################################################################
{
  return $string;
} # function wp_kses_hook


function wp_kses_version()
###############################################################################
# This function returns kses' version number.
###############################################################################
{
  return '0.2.1';
} # function wp_kses_version


function wp_kses_split($string, $allowed_html, $allowed_protocols)
###############################################################################
# This function searches for HTML tags, no matter how malformed. It also
# matches stray ">" characters.
###############################################################################
{
  return preg_replace('%(<'.   # EITHER: <
                      '[^>]*'. # things that aren't >
                      '(>|$)'. # > or end of string
                      '|>)%e', # OR: just a >
                      "wp_kses_split2('\\1', \$allowed_html, ".
                      '$allowed_protocols)',
                      $string);
} # function wp_kses_split


function wp_kses_split2($string, $allowed_html, $allowed_protocols)
###############################################################################
# This function does a lot of work. It rejects some very malformed things
# like <:::>. It returns an empty string, if the element isn't allowed (look
# ma, no strip_tags()!). Otherwise it splits the tag into an element and an
# attribute list.
###############################################################################
{
  $string = wp_kses_stripslashes($string);

  if (substr($string, 0, 1) != '<')
    return '&gt;';
    # It matched a ">" character

  if (!preg_match('%^<\s*(/\s*)?([a-zA-Z0-9]+)([^>]*)>?$%', $string, $matches))
    return '';
    # It's seriously malformed

  $slash = trim($matches[1]);
  $elem = $matches[2];
  $attrlist = $matches[3];

  if (!is_array($allowed_html[strtolower($elem)]))
    return '';
    # They are using a not allowed HTML element

  return wp_kses_attr("$slash$elem", $attrlist, $allowed_html,
                   $allowed_protocols);
} # function wp_kses_split2


function wp_kses_attr($element, $attr, $allowed_html, $allowed_protocols)
###############################################################################
# This function removes all attributes, if none are allowed for this element.
# If some are allowed it calls wp_kses_hair() to split them further, and then it
# builds up new HTML code from the data that kses_hair() returns. It also
# removes "<" and ">" characters, if there are any left. One more thing it
# does is to check if the tag has a closing XHTML slash, and if it does,
# it puts one in the returned code as well.
###############################################################################
{
# Is there a closing XHTML slash at the end of the attributes?

  $xhtml_slash = '';
  if (preg_match('%\s/\s*$%', $attr))
    $xhtml_slash = ' /';

# Are any attributes allowed at all for this element?

  if (count($allowed_html[strtolower($element)]) == 0)
    return "<$element$xhtml_slash>";

# Split it

  $attrarr = wp_kses_hair($attr, $allowed_protocols);

# Go through $attrarr, and save the allowed attributes for this element
# in $attr2

  $attr2 = '';

  foreach ($attrarr as $arreach)
  {
    $current = $allowed_html[strtolower($element)]
                            [strtolower($arreach['name'])];
    if ($current == '')
      continue; # the attribute is not allowed

    if (!is_array($current))
      $attr2 .= ' '.$arreach['whole'];
    # there are no checks

    else
    {
    # there are some checks
      $ok = true;
      foreach ($current as $currkey => $currval)
        if (!wp_kses_check_attr_val($arreach['value'], $arreach['vless'],
                                 $currkey, $currval))
        { $ok = false; break; }

      if ($ok)
        $attr2 .= ' '.$arreach['whole']; # it passed them
    } # if !is_array($current)
  } # foreach

# Remove any "<" or ">" characters

  $attr2 = preg_replace('/[<>]/', '', $attr2);

  return "<$element$attr2$xhtml_slash>";
} # function wp_kses_attr


function wp_kses_hair($attr, $allowed_protocols)
###############################################################################
# This function does a lot of work. It parses an attribute list into an array
# with attribute data, and tries to do the right thing even if it gets weird
# input. It will add quotes around attribute values that don't have any quotes
# or apostrophes around them, to make it easier to produce HTML code that will
# conform to W3C's HTML specification. It will also remove bad URL protocols
# from attribute values.
###############################################################################
{
  $attrarr = array();
  $mode = 0;
  $attrname = '';

# Loop through the whole attribute list

  while (strlen($attr) != 0)
  {
    $working = 0; # Was the last operation successful?

    switch ($mode)
    {
      case 0: # attribute name, href for instance

        if (preg_match('/^([-a-zA-Z]+)/', $attr, $match))
        {
          $attrname = $match[1];
          $working = $mode = 1;
          $attr = preg_replace('/^[-a-zA-Z]+/', '', $attr);
        }

        break;

      case 1: # equals sign or valueless ("selected")

        if (preg_match('/^\s*=\s*/', $attr)) # equals sign
        {
          $working = 1; $mode = 2;
          $attr = preg_replace('/^\s*=\s*/', '', $attr);
          break;
        }

        if (preg_match('/^\s+/', $attr)) # valueless
        {
          $working = 1; $mode = 0;
          $attrarr[] = array
                        ('name'  => $attrname,
                         'value' => '',
                         'whole' => $attrname,
                         'vless' => 'y');
          $attr = preg_replace('/^\s+/', '', $attr);
        }

        break;

      case 2: # attribute value, a URL after href= for instance

        if (preg_match('/^"([^"]*)"(\s+|$)/', $attr, $match))
         # "value"
        {
          $thisval = wp_kses_bad_protocol($match[1], $allowed_protocols);

          $attrarr[] = array
                        ('name'  => $attrname,
                         'value' => $thisval,
                         'whole' => "$attrname=\"$thisval\"",
                         'vless' => 'n');
          $working = 1; $mode = 0;
          $attr = preg_replace('/^"[^"]*"(\s+|$)/', '', $attr);
          break;
        }

        if (preg_match("/^'([^']*)'(\s+|$)/", $attr, $match))
         # 'value'
        {
          $thisval = wp_kses_bad_protocol($match[1], $allowed_protocols);

          $attrarr[] = array
                        ('name'  => $attrname,
                         'value' => $thisval,
                         'whole' => "$attrname='$thisval'",
                         'vless' => 'n');
          $working = 1; $mode = 0;
          $attr = preg_replace("/^'[^']*'(\s+|$)/", '', $attr);
          break;
        }

        if (preg_match("%^([^\s\"']+)(\s+|$)%", $attr, $match))
         # value
        {
          $thisval = wp_kses_bad_protocol($match[1], $allowed_protocols);

          $attrarr[] = array
                        ('name'  => $attrname,
                         'value' => $thisval,
                         'whole' => "$attrname=\"$thisval\"",
                         'vless' => 'n');
                         # We add quotes to conform to W3C's HTML spec.
          $working = 1; $mode = 0;
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
    $attrarr[] = array
                  ('name'  => $attrname,
                   'value' => '',
                   'whole' => $attrname,
                   'vless' => 'y');

  return $attrarr;
} # function wp_kses_hair


function wp_kses_check_attr_val($value, $vless, $checkname, $checkvalue)
###############################################################################
# This function performs different checks for attribute values. The currently
# implemented checks are "maxlen", "minlen", "maxval", "minval" and "valueless"
# with even more checks to come soon.
###############################################################################
{
  $ok = true;

  switch (strtolower($checkname))
  {
    case 'maxlen':
    # The maxlen check makes sure that the attribute value has a length not
    # greater than the given value. This can be used to avoid Buffer Overflows
    # in WWW clients and various Internet servers.

      if (strlen($value) > $checkvalue)
        $ok = false;
      break;

    case 'minlen':
    # The minlen check makes sure that the attribute value has a length not
    # smaller than the given value.

      if (strlen($value) < $checkvalue)
        $ok = false;
      break;

    case 'maxval':
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

    case 'minval':
    # The minval check checks that the attribute value is a positive integer,
    # and that it is not smaller than the given value.

      if (!preg_match('/^\s{0,6}[0-9]{1,6}\s{0,6}$/', $value))
        $ok = false;
      if ($value < $checkvalue)
        $ok = false;
      break;

    case 'valueless':
    # The valueless check checks if the attribute has a value
    # (like <a href="blah">) or not (<option selected>). If the given value
    # is a "y" or a "Y", the attribute must not have a value.
    # If the given value is an "n" or an "N", the attribute must have one.

      if (strtolower($checkvalue) != $vless)
        $ok = false;
      break;
  } # switch

  return $ok;
} # function wp_kses_check_attr_val


function wp_kses_bad_protocol($string, $allowed_protocols)
###############################################################################
# This function removes all non-allowed protocols from the beginning of
# $string. It ignores whitespace and the case of the letters, and it does
# understand HTML entities. It does its work in a while loop, so it won't be
# fooled by a string like "javascript:javascript:alert(57)".
###############################################################################
{
  $string = wp_kses_no_null($string);
  $string2 = $string.'a';

  while ($string != $string2)
  {
    $string2 = $string;
    $string = wp_kses_bad_protocol_once($string, $allowed_protocols);
  } # while

  return $string;
} # function wp_kses_bad_protocol


function wp_kses_no_null($string)
###############################################################################
# This function removes any NULL or chr(173) characters in $string.
###############################################################################
{
  $string = preg_replace('/\0+/', '', $string);
  $string = preg_replace('/(\\\\0)+/', '', $string);

  return $string;
} # function wp_kses_no_null


function wp_kses_stripslashes($string)
###############################################################################
# This function changes the character sequence  \"  to just  "
# It leaves all other slashes alone. It's really weird, but the quoting from
# preg_replace(//e) seems to require this.
###############################################################################
{
  return preg_replace('%\\\\"%', '"', $string);
} # function wp_kses_stripslashes


function wp_kses_array_lc($inarray)
###############################################################################
# This function goes through an array, and changes the keys to all lower case.
###############################################################################
{
  $outarray = array();

  foreach ($inarray as $inkey => $inval)
  {
    $outkey = strtolower($inkey);
    $outarray[$outkey] = array();

    foreach ($inval as $inkey2 => $inval2)
    {
      $outkey2 = strtolower($inkey2);
      $outarray[$outkey][$outkey2] = $inval2;
    } # foreach $inval
  } # foreach $inarray

  return $outarray;
} # function wp_kses_array_lc


function wp_kses_js_entities($string)
###############################################################################
# This function removes the HTML JavaScript entities found in early versions of
# Netscape 4.
###############################################################################
{
  return preg_replace('%&\s*\{[^}]*(\}\s*;?|$)%', '', $string);
} # function wp_kses_js_entities


function wp_kses_html_error($string)
###############################################################################
# This function deals with parsing errors in wp_kses_hair(). The general plan is
# to remove everything to and including some whitespace, but it deals with
# quotes and apostrophes as well.
###############################################################################
{
  return preg_replace('/^("[^"]*("|$)|\'[^\']*(\'|$)|\S)*\s*/', '', $string);
} # function wp_kses_html_error


function wp_kses_bad_protocol_once($string, $allowed_protocols)
###############################################################################
# This function searches for URL protocols at the beginning of $string, while
# handling whitespace and HTML entities.
###############################################################################
{
  return preg_replace('/^((&[^;]*;|[\sA-Za-z0-9])*)'.
                      '(:|&#58;|&#[Xx]3[Aa];)\s*/e',
                      'wp_kses_bad_protocol_once2("\\1", $allowed_protocols)',
                      $string);
} # function wp_kses_bad_protocol_once


function wp_kses_bad_protocol_once2($string, $allowed_protocols)
###############################################################################
# This function processes URL protocols, checks to see if they're in the white-
# list or not, and returns different data depending on the answer.
###############################################################################
{
  $string2 = wp_kses_decode_entities($string);
  $string2 = preg_replace('/\s/', '', $string2);
  $string2 = wp_kses_no_null($string2);
  $string2 = strtolower($string2);

  $allowed = false;
  foreach ($allowed_protocols as $one_protocol)
    if (strtolower($one_protocol) == $string2)
    {
      $allowed = true;
      break;
    }

  if ($allowed)
    return "$string2:";
  else
    return '';
} # function wp_kses_bad_protocol_once2


function wp_kses_normalize_entities($string)
###############################################################################
# This function normalizes HTML entities. It will convert "AT&T" to the correct
# "AT&amp;T", "&#00058;" to "&#58;", "&#XYZZY;" to "&amp;#XYZZY;" and so on.
###############################################################################
{
# Disarm all entities by converting & to &amp;

  $string = str_replace('&', '&amp;', $string);

# Change back the allowed entities in our entity whitelist

  $string = preg_replace('/&amp;([A-Za-z][A-Za-z0-9]{0,19});/',
                         '&\\1;', $string);
  $string = preg_replace('/&amp;#0*([0-9]{1,5});/e',
                         'wp_kses_normalize_entities2("\\1")', $string);
  $string = preg_replace('/&amp;#([Xx])0*(([0-9A-Fa-f]{2}){1,2});/',
                         '&#\\1\\2;', $string);

  return $string;
} # function wp_kses_normalize_entities


function wp_kses_normalize_entities2($i)
###############################################################################
# This function helps wp_kses_normalize_entities() to only accept 16 bit values
# and nothing more for &#number; entities.
###############################################################################
{
  return (($i > 65535) ? "&amp;#$i;" : "&#$i;");
} # function wp_kses_normalize_entities2


function wp_kses_decode_entities($string)
###############################################################################
# This function decodes numeric HTML entities (&#65; and &#x41;). It doesn't
# do anything with other entities like &auml;, but we don't need them in the
# URL protocol whitelisting system anyway.
###############################################################################
{
  $string = preg_replace('/&#([0-9]+);/e', 'chr("\\1")', $string);
  $string = preg_replace('/&#[Xx]([0-9A-Fa-f]+);/e', 'chr(hexdec("\\1"))',
                         $string);

  return $string;
} # function wp_kses_decode_entities

function wp_filter_kses($data) {
	global $allowedtags;
	return wp_kses($data, $allowedtags);
}

// Filter untrusted content
add_filter('comment_author', 'wp_filter_kses');
add_filter('comment_text', 'wp_filter_kses');

?>