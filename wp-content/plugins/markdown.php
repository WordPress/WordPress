<?php

#
# Markdown  -  A text-to-HTML conversion tool for web writers
#
# Copyright (c) 2004 John Gruber  
# <http://daringfireball.net/projects/markdown/>
#
# Copyright (c) 2004 Michel Fortin - Translation to PHP  
# <http://www.michelf.com/projects/php-markdown/>
#

# This version has been modified for inclusion in WordPress
# For the original please see Michel's site


global	$MarkdownPHPVersion, $MarkdownSyntaxVersion,
		$md_empty_element_suffix, $md_tab_width,
		$md_nested_brackets_depth, $md_nested_brackets, 
		$md_escape_table, $md_backslash_escape_table;


$MarkdownPHPVersion    = '1.0'; # Sat 21 Aug 2004
$MarkdownSyntaxVersion = '1.0'; # Fri 20 Aug 2004


#
# Global default settings:
#
$md_empty_element_suffix = " />";     # Change to ">" for HTML output
$md_tab_width = 4;


# -- WordPress Plugin Interface -----------------------------------------------
/*
Plugin Name: Markdown
Plugin URI: http://codex.wordpress.org/Plugin:Markdown
Description: <a href="http://daringfireball.net/projects/markdown/syntax">Markdown syntax</a> allows you to write using an easy-to-read, easy-to-write plain text format. Based on the original Perl version by <a href="http://daringfireball.net/">John Gruber</a>. <a href="http://www.michelf.com/projects/php-markdown/">More...</a>
Version: 1.0
Author: Michel Fortin
Author URI: http://www.michelf.com/
*/
if (isset($wp_version)) {
	# Remove default WordPress auto-paragraph filter.
	remove_filter('the_content', 'wpautop');
	remove_filter('the_excerpt', 'wpautop');
	remove_filter('comment_text', 'wpautop');
	# Add Markdown filter with priority 6 (same as Textile).
	add_filter('the_content', 'Markdown', 6);
	add_filter('the_excerpt', 'Markdown', 6);
	add_filter('comment_text', 'Markdown', 6);
}

function smarty_modifier_markdown($text) {
	return Markdown($text);
}

$md_nested_brackets_depth = 6;
$md_nested_brackets = 
	str_repeat('(?>[^\[\]]+|\[', $md_nested_brackets_depth).
	str_repeat('\])*', $md_nested_brackets_depth);

$md_escape_table = array(
	"\\" => md5("\\"),
	"`" => md5("`"),
	"*" => md5("*"),
	"_" => md5("_"),
	"{" => md5("{"),
	"}" => md5("}"),
	"[" => md5("["),
	"]" => md5("]"),
	"(" => md5("("),
	")" => md5(")"),
	"#" => md5("#"),
	"." => md5("."),
	"!" => md5("!")
);
# Create an identical table but for escaped characters.
$md_backslash_escape_table;
foreach ($md_escape_table as $key => $char)
	$md_backslash_escape_table["\\$key"] = $char;


function Markdown($text) {
	global $md_urls, $md_titles, $md_html_blocks;
	$md_urls = array();
	$md_titles = array();
	$md_html_blocks = array();

	$text = str_replace(array("\r\n", "\r"), "\n", $text);

	$text .= "\n\n";

	$text = _Detab($text);

	$text = preg_replace('/^[ \t]+$/m', '', $text);

	$text = _HashHTMLBlocks($text);

	$text = _StripLinkDefinitions($text);

	$text = _EscapeSpecialChars($text);

	$text = _RunBlockGamut($text);

	$text = _UnescapeSpecialChars($text);

	return $text . "\n";
}


function _StripLinkDefinitions($text) {
	$text = preg_replace_callback('{
						^[ \t]*\[(.+)\]:	# id = $1
						  [ \t]*
						  \n?				# maybe *one* newline
						  [ \t]*
						<?(\S+?)>?			# url = $2
						  [ \t]*
						  \n?				# maybe one newline
						  [ \t]*
						(?:
							# Todo: Titles are delimited by "quotes" or (parens).
							["(]
							(.+?)			# title = $3
							[")]
							[ \t]*
						)?	# title is optional
						(?:\n+|\Z)
		}xm',
		'_StripLinkDefinitions_callback',
		$text);
	return $text;
}
function _StripLinkDefinitions_callback($matches) {
	global $md_urls, $md_titles;
	$link_id = strtolower($matches[1]);
	$md_urls[$link_id] = _EncodeAmpsAndAngles($matches[2]);
	if (isset($matches[3]))
		$md_titles[$link_id] = htmlentities($matches[3]);
	return ''; # String that will replace the block
}


function _HashHTMLBlocks($text) {
	$block_tags_a = 'p|div|h[1-6]|blockquote|pre|table|dl|ol|ul|'.
					'script|noscript|form|fieldset|iframe|math|ins|del';
	$block_tags_b = 'p|div|h[1-6]|blockquote|pre|table|dl|ol|ul|'.
					'script|noscript|form|fieldset|iframe|math';

	$text = preg_replace_callback("{
				(						# save in $1
					^					# start of line  (with /m)
					<($block_tags_a)	# start tag = $2
					\\b					# word break
					(.*\\n)*?			# any number of lines, minimally matching
					</\\2>				# the matching end tag
					[ \\t]*				# trailing spaces/tabs
					(?=\\n+|\\Z)	# followed by a newline or end of document
				)
		}xm",
		'_HashHTMLBlocks_callback',
		$text);

	$text = preg_replace_callback("{
				(						# save in $1
					^					# start of line  (with /m)
					<($block_tags_b)	# start tag = $2
					\\b					# word break
					(.*\\n)*?			# any number of lines, minimally matching
					.*</\\2>				# the matching end tag
					[ \\t]*				# trailing spaces/tabs
					(?=\\n+|\\Z)	# followed by a newline or end of document
				)
		}xm",
		'_HashHTMLBlocks_callback',
		$text);

	$text = preg_replace_callback('{
				(?:
					(?<=\n\n)		# Starting after a blank line
					|				# or
					\A\n?			# the beginning of the doc
				)
				(						# save in $1
					[ \t]*
					<(hr)				# start tag = $2
					\b					# word break
					([^<>])*?			# 
					/?>					# the matching end tag
					(?=\n{2,}|\Z)		# followed by a blank line or end of document
				)
		}x',
		'_HashHTMLBlocks_callback',
		$text);

	return $text;
}
function _HashHTMLBlocks_callback($matches) {
	global $md_html_blocks;
	$text = $matches[1];
	$key = md5($text);
	$md_html_blocks[$key] = $text;
	return "\n\n$key\n\n"; # String that will replace the block
}


function _RunBlockGamut($text) {
	global $md_empty_element_suffix;

	$text = _DoHeaders($text);

	# Do Horizontal Rules:
	$text = preg_replace(
		array('/^( ?\* ?){3,}$/m',
			  '/^( ?- ?){3,}$/m',
			  '/^( ?_ ?){3,}$/m'),
		"\n<hr$md_empty_element_suffix\n", 
		$text);

	$text = _DoLists($text);

	$text = _DoCodeBlocks($text);

	$text = _DoBlockQuotes($text);

	# Make links out of things like `<http://example.com/>`
	$text = _DoAutoLinks($text);

	$text = _HashHTMLBlocks($text);

	$text = _FormParagraphs($text);

	return $text;
}


function _RunSpanGamut($text) {
	global $md_empty_element_suffix;
	$text = _DoCodeSpans($text);

	# Fix unencoded ampersands and <'s:
	$text = _EncodeAmpsAndAngles($text);

	# Process anchor and image tags. Images must come first,
	# because ![foo][f] looks like an anchor.
	$text = _DoImages($text);
	$text = _DoAnchors($text);


	$text = _DoItalicsAndBold($text);

	$text = preg_replace('/ {2,}\n/', "<br$md_empty_element_suffix\n", $text);

	return $text;
}


function _EscapeSpecialChars($text) {
	global $md_escape_table;
	$tokens = _TokenizeHTML($text);

	$text = '';   # rebuild $text from the tokens
	foreach ($tokens as $cur_token) {
		if ($cur_token[0] == 'tag') {
			$cur_token[1] = str_replace(array('*', '_'),
				array($md_escape_table['*'], $md_escape_table['_']),
				$cur_token[1]);
			$text .= $cur_token[1];
		} else {
			$t = $cur_token[1];
			$t = _EncodeBackslashEscapes($t);
			$text .= $t;
		}
	}
	return $text;
}


function _DoAnchors($text) {
	global $md_nested_brackets;
	#
	# First, handle reference-style links: [link text] [id]
	#
	$text = preg_replace_callback("{
		(					# wrap whole match in $1
		  \\[
			($md_nested_brackets)	# link text = $2
		  \\]

		  [ ]?				# one optional space
		  (?:\\n[ ]*)?		# one optional newline followed by spaces

		  \\[
			(.*?)		# id = $3
		  \\]
		)
		}xs",
		'_DoAnchors_reference_callback', $text);

	$text = preg_replace_callback("{
		(				# wrap whole match in $1
		  \\[
			($md_nested_brackets)	# link text = $2
		  \\]
		  \\(			# literal paren
			[ \\t]*
			<?(.+?)>?	# href = $3
			[ \\t]*
			(			# $4
			  (['\"])	# quote char = $5
			  (.*?)		# Title = $6
			  \\5		# matching quote
			)?			# title is optional
		  \\)
		)
		}xs",
		'_DoAnchors_inline_callback', $text);

	return $text;
}
function _DoAnchors_reference_callback($matches) {
	global $md_urls, $md_titles, $md_escape_table;
	$whole_match = $matches[1];
	$link_text   = $matches[2];
	$link_id     = strtolower($matches[3]);

	if ($link_id == "") {
		$link_id = strtolower($link_text); # for shortcut links like [this][].
	}

	if (isset($md_urls[$link_id])) {
		$url = $md_urls[$link_id];
		# We've got to encode these to avoid conflicting with italics/bold.
		$url = str_replace(array('*', '_'),
						   array($md_escape_table['*'], $md_escape_table['_']),
						   $url);
		$result = "<a href=\"$url\"";
		if ( isset( $md_titles[$link_id] ) ) {
			$title = $md_titles[$link_id];
			$title = str_replace(array('*',     '_'),
								 array($md_escape_table['*'], 
									   $md_escape_table['_']), $title);
			$result .=  " title=\"$title\"";
		}
		$result .= ">$link_text</a>";
	}
	else {
		$result = $whole_match;
	}
	return $result;
}
function _DoAnchors_inline_callback($matches) {
	global $md_escape_table;
	$whole_match = $matches[1];
	$link_text   = $matches[2];
	$url	  		= $matches[3];
	$title		= $matches[6];

	# We've got to encode these to avoid conflicting with italics/bold.
	$url = str_replace(array('*', '_'),
					   array($md_escape_table['*'], $md_escape_table['_']), 
					   $url);
	$result = "<a href=\"$url\"";
	if (isset($title)) {
		$title = str_replace('"', '&quot', $title);
		$title = str_replace(array('*', '_'),
							 array($md_escape_table['*'], $md_escape_table['_']),
							 $title);
		$result .=  " title=\"$title\"";
	}
	
	$result .= ">$link_text</a>";

	return $result;
}


function _DoImages($text) {
	$text = preg_replace_callback('{
		(				# wrap whole match in $1
		  !\[
			(.*?)		# alt text = $2
		  \]

		  [ ]?				# one optional space
		  (?:\n[ ]*)?		# one optional newline followed by spaces

		  \[
			(.*?)		# id = $3
		  \]

		)
		}xs', 
		'_DoImages_reference_callback', $text);

	#
	# Next, handle inline images:  ![alt text](url "optional title")
	# Don't forget: encode * and _

	$text = preg_replace_callback("{
		(				# wrap whole match in $1
		  !\\[
			(.*?)		# alt text = $2
		  \\]
		  \\(			# literal paren
			[ \\t]*
			<?(\S+?)>?	# src url = $3
			[ \\t]*
			(			# $4
			  (['\"])	# quote char = $5
			  (.*?)		# title = $6
			  \\5		# matching quote
			  [ \\t]*
			)?			# title is optional
		  \\)
		)
		}xs",
		'_DoImages_inline_callback', $text);

	return $text;
}
function _DoImages_reference_callback($matches) {
	global $md_urls, $md_titles, $md_empty_element_suffix, $md_escape_table;
	$whole_match = $matches[1];
	$alt_text    = $matches[2];
	$link_id     = strtolower($matches[3]);

	if ($link_id == "") {
		$link_id = strtolower($alt_text); # for shortcut links like ![this][].
	}

	$alt_text = str_replace('"', '&quot;', $alt_text);
	if (isset($md_urls[$link_id])) {
		$url = $md_urls[$link_id];
		# We've got to encode these to avoid conflicting with italics/bold.
		$url = str_replace(array('*', '_'),
						   array($md_escape_table['*'], $md_escape_table['_']),
						   $url);
		$result = "<img src=\"$url\" alt=\"$alt_text\"";
		if (isset($md_titles[$link_id])) {
			$title = $md_titles[$link_id];
			$title = str_replace(array('*', '_'),
								 array($md_escape_table['*'], 
									   $md_escape_table['_']), $title);
			$result .=  " title=\"$title\"";
		}
		$result .= $md_empty_element_suffix;
	}
	else {
		# If there's no such link ID, leave intact:
		$result = $whole_match;
	}

	return $result;
}
function _DoImages_inline_callback($matches) {
	global $md_empty_element_suffix, $md_escape_table;
	$whole_match = $matches[1];
	$alt_text    = $matches[2];
	$url	  		= $matches[3];
	$title		= '';
	if (isset($matches[6])) {
		$title = $matches[6];
	}

	$alt_text = str_replace('"', '&quot;', $alt_text);
	$title    = str_replace('"', '&quot;', $title);
	# We've got to encode these to avoid conflicting with italics/bold.
	$url = str_replace(array('*', '_'),
					   array($md_escape_table['*'], $md_escape_table['_']),
					   $url);
	$result = "<img src=\"$url\" alt=\"$alt_text\"";
	if (isset($title)) {
		$title = str_replace(array('*', '_'),
							 array($md_escape_table['*'], $md_escape_table['_']),
							 $title);
		$result .=  " title=\"$title\""; # $title already quoted
	}
	$result .= $md_empty_element_suffix;

	return $result;
}


function _DoHeaders($text) {
	$text = preg_replace(
		array("/(.+)[ \t]*\n=+[ \t]*\n+/e",
			  "/(.+)[ \t]*\n-+[ \t]*\n+/e"),
		array("'<h1>'._RunSpanGamut(_UnslashQuotes('\\1')).'</h1>\n\n'",
			  "'<h2>'._RunSpanGamut(_UnslashQuotes('\\1')).'</h2>\n\n'"),
		$text);

	$text = preg_replace("{
			^(\\#{1,6})	# $1 = string of #'s
			[ \\t]*
			(.+?)		# $2 = Header text
			[ \\t]*
			\\#*			# optional closing #'s (not counted)
			\\n+
		}xme",
		"'<h'.strlen('\\1').'>'._RunSpanGamut(_UnslashQuotes('\\2')).'</h'.strlen('\\1').'>\n\n'",
		$text);

	return $text;
}


function _DoLists($text) {
#
# Form HTML ordered (numbered) and unordered (bulleted) lists.
#
	global $md_tab_width;
	$less_than_tab = $md_tab_width - 1;

	# Re-usable patterns to match list item bullets and number markers:
	$marker_ul  = '[*+-]';
	$marker_ol  = '\d+[.]';
	$marker_any = "(?:$marker_ul|$marker_ol)";

	$text = preg_replace_callback("{
			(								# $1
			  (								# $2
				^[ ]{0,$less_than_tab}
			    ($marker_any)				# $3 - first list item marker
				[ \\t]+
			  )
			  (?s:.+?)
			  (								# $4
				  \\z
				|
				  \\n{2,}
				  (?=\\S)
				  (?!						# Negative lookahead for another list item marker
				  	[ \\t]*
				  	{$marker_any}[ \\t]+
				  )
			  )
			)
		}xm",
		'_DoLists_callback', $text);

	return $text;
}
function _DoLists_callback($matches) {
	# Re-usable patterns to match list item bullets and number markers:
	$marker_ul  = '[*+-]';
	$marker_ol  = '\d+[.]';
	$marker_any = "(?:$marker_ul|$marker_ol)";
	
	$list = $matches[1];
	$list_type = preg_match('/[*+-]/', $matches[3]) ? "ul" : "ol";
	# Turn double returns into triple returns, so that we can make a
	# paragraph for the last item in a list, if necessary:
	$list = preg_replace("/\n{2,}/", "\n\n\n", $list);
	$result = _ProcessListItems($list, $marker_any);
	$result = "<$list_type>\n" . $result . "</$list_type>\n\n";
	return $result;
}


function _ProcessListItems($list_str, $marker_any) {
	# trim trailing blank lines:
	$list_str = preg_replace("/\n{2,}\\z/", "\n", $list_str);

	$list_str = preg_replace_callback('{
		(\n)?							# leading line = $1
		(^[ \t]*)						# leading whitespace = $2
		('.$marker_any.') [ \t]+		# list marker = $3
		((?s:.+?)						# list item text   = $4
		(\n{1,2}))
		(?= \n* (\z | \2 ('.$marker_any.') [ \t]+))
		}xm',
		'_ProcessListItems_callback', $list_str);

	return $list_str;
}
function _ProcessListItems_callback($matches) {
	$item = $matches[4];
	$leading_line = $matches[1];
	$leading_space = $matches[2];

	if ($leading_line || preg_match('/\n{2,}/', $item)) {
		$item = _RunBlockGamut(_Outdent($item));
		#$item =~ s/\n+/\n/g;
	}
	else {
		# Recursion for sub-lists:
		$item = _DoLists(_Outdent($item));
		$item = rtrim($item, "\n");
		$item = _RunSpanGamut($item);
	}

	return "<li>" . $item . "</li>\n";
}


function _DoCodeBlocks($text) {
	global $md_tab_width;
	$text = preg_replace_callback("{
			(?:\\n\\n|\\A)
			(	            # $1 = the code block -- one or more lines, starting with a space/tab
			  (?:
				(?:[ ]\{$md_tab_width} | \\t)  # Lines must start with a tab or a tab-width of spaces
				.*\\n+
			  )+
			)
			((?=^[ ]{0,$md_tab_width}\\S)|\\Z)	# Lookahead for non-space at line-start, or end of doc
		}xm",
		'_DoCodeBlocks_callback', $text);

	return $text;
}
function _DoCodeBlocks_callback($matches) {
	$codeblock = $matches[1];

	$codeblock = _EncodeCode(_Outdent($codeblock));
	$codeblock = _Detab($codeblock);
	# trim leading newlines and trailing whitespace
	$codeblock = preg_replace(array('/\A\n+/', '/\s+\z/'), '', $codeblock);

	$result = "\n\n<pre><code>" . $codeblock . "\n</code></pre>\n\n";

	return $result;
}


function _DoCodeSpans($text) {
	$text = preg_replace_callback("@
			(`+)		# $1 = Opening run of `
			(.+?)		# $2 = The code block
			(?<!`)
			\\1
			(?!`)
		@xs",
		'_DoCodeSpans_callback', $text);

	return $text;
}
function _DoCodeSpans_callback($matches) {
	$c = $matches[2];
	$c = preg_replace('/^[ \t]*/', '', $c); # leading whitespace
	$c = preg_replace('/[ \t]*$/', '', $c); # trailing whitespace
	$c = _EncodeCode($c);
	return "<code>$c</code>";
}


function _EncodeCode($_) {
	global $md_escape_table;

	$_ = str_replace('&', '&amp;', $_);

	$_ = str_replace(array('<',    '>'), 
					 array('&lt;', '&gt;'), $_);

	$_ = str_replace(array_keys($md_escape_table), 
					 array_values($md_escape_table), $_);

	return $_;
}


function _DoItalicsAndBold($text) {
	# <strong> must go first:
	$text = preg_replace('{ (\*\*|__) (?=\S) (.+?) (?<=\S) \1 }sx',
		'<strong>\2</strong>', $text);
	# Then <em>:
	$text = preg_replace('{ (\*|_) (?=\S) (.+?) (?<=\S) \1 }sx',
		'<em>\2</em>', $text);

	return $text;
}


function _DoBlockQuotes($text) {
	$text = preg_replace_callback('/
		  (								# Wrap whole match in $1
			(
			  ^[ \t]*>[ \t]?			# ">" at the start of a line
				.+\n					# rest of the first line
			  (.+\n)*					# subsequent consecutive lines
			  \n*						# blanks
			)+
		  )
		/xm',
		'_DoBlockQuotes_callback', $text);

	return $text;
}
function _DoBlockQuotes_callback($matches) {
	$bq = $matches[1];
	# trim one level of quoting - trim whitespace-only lines
	$bq = preg_replace(array('/^[ \t]*>[ \t]?/m', '/^[ \t]+$/m'), '', $bq);
	$bq = _RunBlockGamut($bq);		# recurse

	$bq = preg_replace('/^/m', "  ", $bq);
	# These leading spaces screw with <pre> content, so we need to fix that:
	$bq = preg_replace_callback('{(\s*<pre>.+?</pre>)}sx', 
								'_DoBlockQuotes_callback2', $bq);

	return "<blockquote>\n$bq\n</blockquote>\n\n";
}
function _DoBlockQuotes_callback2($matches) {
	$pre = $matches[1];
	$pre = preg_replace('/^  /m', '', $pre);
	return $pre;
}


function _FormParagraphs($text) {
	global $md_html_blocks;

	# Strip leading and trailing lines:
	$text = preg_replace(array('/\A\n+/', '/\n+\z/'), '', $text);

	$grafs = preg_split('/\n{2,}/', $text, -1, PREG_SPLIT_NO_EMPTY);
	$count = count($grafs);

	foreach ($grafs as $key => $value) {
		if (!isset( $md_html_blocks[$value] )) {
			$value = _RunSpanGamut($value);
			$value = preg_replace('/^([ \t]*)/', '<p>', $value);
			$value .= "</p>";
			$grafs[$key] = $value;
		}
	}

	foreach ($grafs as $key => $value) {
		if (isset( $md_html_blocks[$value] )) {
			$grafs[$key] = $md_html_blocks[$value];
		}
	}

	return implode("\n\n", $grafs);
}


function _EncodeAmpsAndAngles($text) {
	$text = preg_replace('/&(?!#?[xX]?(?:[0-9a-fA-F]+|\w+);)/', 
						 '&amp;', $text);;

	# Encode naked <'s
	$text = preg_replace('{<(?![a-z/?\$!])}i', '&lt;', $text);

	return $text;
}


function _EncodeBackslashEscapes($text) {
	global $md_escape_table, $md_backslash_escape_table;
	# Must process escaped backslashes first.
	return str_replace(array_keys($md_backslash_escape_table),
					   array_values($md_backslash_escape_table), $text);
}


function _DoAutoLinks($text) {
	$text = preg_replace("!<((https?|ftp):[^'\">\\s]+)>!", 
						 '<a href="\1">\1</a>', $text);

	# Email addresses: <address@domain.foo>
	$text = preg_replace('{
		<
		(
			[-.\w]+
			\@
			[-a-z0-9]+(\.[-a-z0-9]+)*\.[a-z]+
		)
		>
		}exi',
		"_EncodeEmailAddress(_UnescapeSpecialChars(_UnslashQuotes('\\1')))",
		$text);

	return $text;
}


function _EncodeEmailAddress($addr) {
	$addr = "mailto:" . $addr;
	$length = strlen($addr);

	# leave ':' alone (to spot mailto: later)
	$addr = preg_replace_callback('/([^\:])/', 
								  '_EncodeEmailAddress_callback', $addr);

	$addr = "<a href=\"$addr\">$addr</a>";
	# strip the mailto: from the visible part
	$addr = preg_replace('/">.+?:/', '">', $addr);

	return $addr;
}
function _EncodeEmailAddress_callback($matches) {
	$char = $matches[1];
	$r = rand(0, 100);
	# roughly 10% raw, 45% hex, 45% dec
	# '@' *must* be encoded. I insist.
	if ($r > 90 && $char != '@') return $char;
	if ($r < 45) return '&#x'.dechex(ord($char)).';';
	return '&#'.ord($char).';';
}


function _UnescapeSpecialChars($text) {
	global $md_escape_table;
	return str_replace(array_values($md_escape_table), 
					   array_keys($md_escape_table), $text);
}


if (!function_exists('_TokenizeHTML')) {
	function _TokenizeHTML($str) {
		$index = 0;
		$tokens = array();

		$depth = 6;
		$nested_tags = str_repeat('(?:<[a-z\/!$](?:[^<>]|',$depth)
					   .str_repeat(')*>)', $depth);
		$match = "(?s:<!(?:--.*?--\s*)+>)|".  # comment
				 "(?s:<\?.*?\?>)|".         # processing instruction
				 "$nested_tags";            # nested tags

		$parts = preg_split("/($match)/", $str, -1, PREG_SPLIT_DELIM_CAPTURE);

		foreach ($parts as $part) {
			if (++$index % 2 && $part != '') 
				array_push($tokens, array('text', $part));
			else
				array_push($tokens, array('tag', $part));
		}

		return $tokens;
	}
}


function _Outdent($text) {
	global $md_tab_width;
	return preg_replace("/^(\\t|[ ]{1,$md_tab_width})/m", "", $text);
}


function _Detab($text) {
	global $md_tab_width;
	$text = preg_replace(
		"/(.*?)\t/e",
		"'\\1'.str_repeat(' ', $md_tab_width - strlen('\\1') % $md_tab_width)",
		$text);
	return $text;
}


function _UnslashQuotes($text) {
	return str_replace('\"', '"', $text);
}

?>