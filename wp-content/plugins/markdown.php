<?php
/*
Plugin Name: MarkDown
Plugin URI: http://daringfireball.net/projects/markdown/
Description: Markdown is a text-to-HTML conversion tool for web writers. <a href="http://daringfireball.net/projects/markdown/syntax">Markdown syntax</a> allows you to write using an easy-to-read, easy-to-write plain text format, then convert it to structurally valid XHTML. This plugin <strong>enables Markdown for your posts and comments</strong>. Written by <a href="http://daringfireball.net/">John Gruber</a> in Perl, translated to PHP by <a href="http://www.michelf.com/">Michel Fortin</a>, and made a WP plugin by <a href="http://photomatt.net/">Matt</a>. If you use this you should disable Textile 1 and 2 because the syntax conflicts.
Version: 1.0b4
Author: John Gruber
Author URI: http://daringfireball.net/
*/ 


/*
Note to code readers: I've stripped most of the comments from the source, see the original at http://www.michelf.com/php-markdown/?code to get the unaltered version. --Matt
*/

$MarkdownPHPVersion    = '1.0b4.1'; # Sun 4 Apr 2004
$MarkdownSyntaxVersion = '1.0b4'; # Thu 25 Mar 2004
$g_empty_element_suffix = " />";     # Change to ">" for HTML output
$g_tab_width = 4;
$g_nested_brackets_depth = 6;
$g_nested_brackets = 
	str_repeat('(?>[^\[\]]+|\[', $g_nested_brackets_depth).
	str_repeat('\])*', $g_nested_brackets_depth);
$g_escape_table = array(
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
$g_backslash_escape_table;
foreach ($g_escape_table as $key => $char)
	$g_backslash_escape_table["\\$key"] = $char;

$g_urls;
$g_titles;
$g_html_blocks;

function Markdown($text) {
	global $g_urls, $g_titles, $g_html_blocks;
	$g_urls = array();
	$g_titles = array();
	$g_html_blocks = array();
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
						(\S+)				# url = $2
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
	global $g_urls, $g_titles;
	$link_id = strtolower($matches[1]);
	$g_urls[$link_id] = _EncodeAmpsAndAngles($matches[2]);
	if (isset($matches[3]))
		$g_titles[$link_id] = htmlentities($matches[3]);
	return ''; # String that will replace the block
}

function _HashHTMLBlocks($text) {
	$block_tag_re = 'p|div|h[1-6]|blockquote|pre|table|dl|ol|ul|script';
	$text = preg_replace_callback("{
				(						# save in $1
					^					# start of line  (with /m)
					<($block_tag_re)	# start tag = $2
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
					<($block_tag_re)	# start tag = $2
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
	global $g_html_blocks;
	$text = $matches[1];
	$key = md5($text);
	$g_html_blocks[$key] = $text;
	return "\n\n$key\n\n"; # String that will replace the block
}

function _RunBlockGamut($text) {
	global $g_empty_element_suffix;
	
	$text = _DoHeaders($text);

	$text = preg_replace(
		array('/^( ?\* ?){3,}$/m',
			  '/^( ?- ?){3,}$/m'),
		array("\n<hr$g_empty_element_suffix\n", 
			  "\n<hr$g_empty_element_suffix\n"), 
		$text);

	$text = _DoLists($text);

	$text = _DoCodeBlocks($text);

	$text = _DoBlockQuotes($text);

	$text = _DoAutoLinks($text);

	$text = _HashHTMLBlocks($text);

	$text = _FormParagraphs($text);

	return $text;
}


function _RunSpanGamut($text) {
	global $g_empty_element_suffix;
	$text = _DoCodeSpans($text);


	$text = _EncodeAmpsAndAngles($text);

	$text = _DoImages($text);
	$text = _DoAnchors($text);


	$text = _DoItalicsAndBold($text);
	
	# Do hard breaks:
	$text = preg_replace('/ {2,}\n/', "<br$g_empty_element_suffix\n", $text);

	return $text;
}


function _EscapeSpecialChars($text) {
	global $g_escape_table;
	$tokens = _TokenizeHTML($text);

	$text = '';   # rebuild $text from the tokens
	$in_pre = 0;  # Keep track of when we're inside <pre> or <code> tags.
	$tags_to_skip = "!<(/?)(?:pre|code|kbd|script)[\s>]!";

	foreach ($tokens as $cur_token) {
		if ($cur_token[0] == 'tag') {
			$cur_token[1] = str_replace(array('*', '_'),
				array($g_escape_table['*'], $g_escape_table['_']),
				$cur_token[1]);
			$text .= $cur_token[1];
		} else {
			$t = $cur_token[1];
			if (! $in_pre) {
				$t = _EncodeBackslashEscapes($t);
				# $t =~ s{([a-z])/([a-z])}{$1&thinsp;/&thinsp;$2}ig;
			}
			$text .= $t;
		}
	}
	return $text;
}


function _DoAnchors($text) {
	global $g_nested_brackets;

	$text = preg_replace_callback("{
		(					# wrap whole match in $1
		  \\[
		    ($g_nested_brackets)	# link text = $2
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
			($g_nested_brackets)	# link text = $2
		  \\]
		  \\(			# literal paren
			[ \\t]*
			(.+?)		# href = $3
			[ \\t]*
			(			# title = $4
			  (['\"])	# quote char = $5
			  .*?
			  \\5		# matching quote
			)?			# title is optional
		  \\)
		)
		}xs",
		'_DoAnchors_inline_callback', $text);
	
	return $text;
}
function _DoAnchors_reference_callback($matches) {
	global $g_urls, $g_titles;
	$result;
	$whole_match = $matches[1];
	$link_text   = $matches[2];
	$link_id     = strtolower($matches[3]);

	if ($link_id == "") {
		$link_id = strtolower($link_text); # for shortcut links like [this][].
	}

	if (isset($g_urls[$link_id])) {
		$url = $g_urls[$link_id];
		$url = str_replace(array('*',     '_'),
						   array('&#42;', '&#95;'), $url);
		$result = "<a href='$url'";
		if ( isset( $g_title[$link_id] ) ) {
			$title = $g_titles[$link_id];
			$title = str_replace(array('*',     '_'),
								 array('&#42;', '&#95;'), $title);
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
	$result;
	$whole_match = $matches[1];
	$link_text   = $matches[2];
	$url	  		= $matches[3];
	$title		= $matches[4];

	# We've got to encode these to avoid conflicting with italics/bold.
	$url = str_replace(array('*',     '_'),
					   array('&#42;', '&#95;'), $url);
	$result = "<a href=\"$url\"";
	if ($title) {
		$title = str_replace(array('*',     '_'),
							 array('&#42;', '&#95;'), $title);
		$result .=  " title=$title";
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
			(\\S+)		# src url = $3
			[ \\t]*
			(			# title = $4
			  (['\"])	# quote char = $5
			  .*?
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
	global $g_urls, $g_titles, $g_empty_element_suffix;
	$result;
	$whole_match = $matches[1];
	$alt_text    = $matches[2];
	$link_id     = strtolower($matches[3]);

	if ($link_id == "") {
		$link_id = strtolower($alt_text); # for shortcut links like ![this][].
	}
	
	if (isset($g_urls[$link_id])) {
		$url = $g_urls[$link_id];
		$url = str_replace(array('*',     '_'),
						   array('&#42;', '&#95;'), $url);
		$result = "<img src=\"$url\" alt=\"$alt_text\"";
		if (isset($g_titles[$link_id])) {
			$title = $g_titles[$link_id];
			$title = str_replace(array('*',     '_'),
								 array('&#42;', '&#95;'), $title);
			$result .=  " title=\"$title\"";
		}
		$result .= $g_empty_element_suffix;
	}
	else {
		$result = $whole_match;
	}

	return $result;
}
function _DoImages_inline_callback($matches) {
	global $g_empty_element_suffix;
	$result;
	$whole_match = $matches[1];
	$alt_text    = $matches[2];
	$url	  		= $matches[3];
	$title		= $matches[4];

	$url = str_replace(array('*',     '_'),
					   array('&#42;', '&#95;'), $url);
	$result = "<img src=\"$url\" alt=\"$alt_text\"";
	if (isset($title)) {
		$title = str_replace(array('*',     '_'),
							 array('&#42;', '&#95;'), $title);
		$result .=  " title=$title"; # $title already quoted
	}
	$result .= $g_empty_element_suffix;

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
	global $g_tab_width;
	$less_than_tab = $g_tab_width - 1;

	$text = preg_replace_callback("{
			(
			  (
			    ^[ ]{0,$less_than_tab}
			    (\\*|\\d+[.])
			    [ \\t]+
			  )
			  (?s:.+?)
			  (
			      \\z
			    |
				  \\n{2,}
				  (?=\\S)
				  (?![ \\t]* (\\*|\\d+[.]) [ \\t]+)
			  )
			)
		}xm",
		'_DoLists_callback', $text);

	return $text;
}
function _DoLists_callback($matches) {
	$list_type = ($matches[3] == "*") ? "ul" : "ol";
	$list = $matches[1];
	$list = preg_replace("/\n{2,}/", "\n\n\n", $list);
	$result = _ProcessListItems($list);
	$result = "<$list_type>\n" . $result . "</$list_type>\n";
	return $result;
}


function _ProcessListItems($list_str) {
	# trim trailing blank lines:
	$list_str = preg_replace("/\n{2,}\\z/", "\n", $list_str);

	$list_str = preg_replace_callback('{
		(\n)?							# leading line = $1
		(^[ \t]*)						# leading whitespace = $2
		(\*|\d+[.]) [ \t]+				# list marker = $3
		((?s:.+?)						# list item text   = $4
		(\n{1,2}))
		(?= \n* (\z | \2 (\*|\d+[.]) [ \t]+))
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
	global $g_tab_width;
	$text = preg_replace_callback("{
			(.?)			# $1 = preceding character
			(:)				# $2 = colon delimiter
			(\\n+)			# $3 = newlines after colon
			(	            # $4 = the code block -- one or more lines, starting with a space/tab
			  (?:
			    (?:[ ]\{$g_tab_width} | \\t)  # Lines must start with a tab or a tab-width of spaces
			    .*\\n+
			  )+
			)
			((?=^[ ]{0,$g_tab_width}\\S)|\\Z)	# Lookahead for non-space at line-start, or end of doc
		}xm",
		'_DoCodeBlocks_callback', $text);

	return $text;
}
function _DoCodeBlocks_callback($matches) {
	$prevchar  = $matches[1];
	$newlines  = $matches[2];
	$codeblock = $matches[4];

	$result; # return value
	

	$prefix = "";
	if (!(preg_match('/\s/', $prevchar) || ($prevchar == ""))) {
			$prefix = "$prevchar:";
	}
	$codeblock = _EncodeCode(_Outdent($codeblock));
	$codeblock = _Detab($codeblock);
	# trim leading newlines and trailing whitespace
	$codeblock = preg_replace(array('/\A\n+/', '/\s+\z/'), '', $codeblock);
	
	$result = $prefix . "\n\n<pre><code>" . $codeblock . "\n</code></pre>\n\n";

	return $result;
}


function _DoCodeSpans($text) {
	$text = preg_replace_callback("@
			(`+)		# Opening run of `
			(.+?)		# the code block
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

	global $g_escape_table;

	# Encode all ampersands; HTML entities are not
	# entities within a Markdown code span.
	$_ = str_replace('&', '&amp;', $_);

	# Do the angle bracket song and dance:
	$_ = str_replace(array('<',    '>'), 
					 array('&lt;', '&gt;'), $_);

	# Now, escape characters that are magic in Markdown:
	$_ = str_replace(array_keys($g_escape_table), 
					 array_values($g_escape_table), $_);

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
	$bq = preg_replace('/^[ \t]*>[ \t]?/m', '', $bq); 
	$bq = _RunBlockGamut($bq);		# recurse
	$bq = preg_replace('/^/m', "\t", $bq);
	
	return "<blockquote>\n$bq\n</blockquote>\n\n";
}


function _FormParagraphs($text) {
	global $g_html_blocks;

	# Strip leading and trailing lines:
	$text = preg_replace(array('/\A\n+/', '/\n+\z/'), '', $text);

	$grafs = preg_split('/\n{2,}/', $text);
	$count = count($graph);


	foreach ($grafs as $key => $value) {
		if (!isset( $g_html_blocks[$value] )) {
			$value = _RunSpanGamut($value);
			$value = preg_replace('/^([ \t]*)/', '<p>', $value);
			$value .= "</p>";
			$grafs[$key] = $value;
		}
	}


	foreach ($grafs as $key => $value) {
		if (isset( $g_html_blocks[$value] )) {
			$grafs[$key] = $g_html_blocks[$value];
		}
	}

	return implode("\n\n", $grafs);
}


function _EncodeAmpsAndAngles($text) {
	$text = preg_replace('/&(?!#?[xX]?(?:[0-9a-fA-F]+|\w{1,8});)/', 
						 '&amp;', $text);;

	# Encode naked <'s
	$text = preg_replace('{<(?![a-z/?\$!])}i', '&lt;', $text);

	return $text;
}


function _EncodeBackslashEscapes($text) {
	global $g_escape_table, $g_backslash_escape_table;
	# Must process escaped backslashes first.
	return str_replace(array_keys($g_backslash_escape_table),
					   array_values($g_backslash_escape_table), $text);
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

	$addr = preg_replace_callback('/([^\:])/', 
								  '_EncodeEmailAddress_callback', $addr);

	$addr = "<a href=\"$addr\">$addr</a>";
	$addr = preg_replace('/">.+?:/', '">', $addr);

	return $addr;
}
function _EncodeEmailAddress_callback($matches) {
	$char = $matches[1];
	$r = rand(0, 100);
	if ($r > 90 && $char != '@') return $char;
	if ($r < 45) return '&#x'.dechex(ord($char)).';';
	return '&#'.ord($char).';';
}


function _UnescapeSpecialChars($text) {
	global $g_escape_table;
	return str_replace(array_values($g_escape_table), 
					   array_keys($g_escape_table), $text);
}


function _TokenizeHTML($str) {
	$pos = 0;
	$len = strlen($str);
	$tokens = array();

	$depth = 6;
	$nested_tags = str_repeat('(?:<[a-z\/!$](?:[^<>]|',$depth)
				   .str_repeat(')*>)', $depth);
	$match = "(?s:<!(--.*?--\s*)+>)|".  # comment
			 "(?s:<\?.*?\?>)|".         # processing instruction
			 "$nested_tags";            # nested tags
	
	preg_match_all("/($match)/", $str, $matches, 
				   PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

	foreach ($matches as $element) {
		$whole_tag = $element[0][0];
		$tag_start = $element[0][1];
		$sec_start = $tag_start + strlen($whole_tag);
		if ($pos < $tag_start) {
			array_push($tokens, array('text', 
					   substr($str, $pos, $tag_start - $pos)));
		}
		array_push($tokens, array('tag', $whole_tag));
		$pos = $sec_start;
	}
	
	if ($pos < $len)
		array_push($tokens, array('text', 
				   substr($str, $pos, $len - $pos)));
	return $tokens;
}


function _Outdent($text) {
	global $g_tab_width;
	return preg_replace("/^(\\t|[ ]{1,$g_tab_width})/m", "", $text);
}


function _Detab($text) {
	global $g_tab_width;
	$text = preg_replace(
		"/(.*?)\t/e",
		"'\\1'.str_repeat(' ', $g_tab_width - strlen('\\1') % $g_tab_width)",
		$text);
	return $text;
}


function _UnslashQuotes($text) {
	return str_replace('\"', '"', $text);
}

// And now for the filters
remove_filter('the_content', 'wpautop');
remove_filter('the_excerpt', 'wpautop');
remove_filter('comment_text', 'wpautop');

add_filter('the_content', 'Markdown');
add_filter('the_excerpt', 'Markdown');
remove_filter('comment_text', 'Markdown');

?>