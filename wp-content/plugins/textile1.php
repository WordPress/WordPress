<?php
/*
Plugin Name: Textile 1
Version: 1.0
Plugin URI: http://www.huddledmasses.org/
Description: This is a simple wrapper for <a href="http://textism.com/?wp">Dean Allen's</a> Humane Web Text Generator, also known as <a href="http://www.textism.com/tools/textile/">Textile</a>. If you use this plugin you should disable Textile 2 and Markdown, as they don't play well together.
Author: Dean Allen
Author URI: http://www.textism.com/?wp
*/

/*

This is Textile
A Humane Web Text Generator

Version 1.0
21 Feb, 2003

Copyright (c) 2003, Dean Allen, www.textism.com
All rights reserved.

_______
LICENSE

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:

* Redistributions of source code must retain the above copyright notice,
  this list of conditions and the following disclaimer.

* Redistributions in binary form must reproduce the above copyright notice,
  this list of conditions and the following disclaimer in the documentation
  and/or other materials provided with the distribution.

* Neither the name Textile nor the names of its contributors may be used to
  endorse or promote products derived from this software without specific
  prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
POSSIBILITY OF SUCH DAMAGE.

*/


	function textile($text) {
	$text = stripslashes($text);

	$text = preg_replace("/&(?![#a-zA-Z0-9]+;)/","x%x%",$text);

	if (function_exists('mb_encode_numericentity')) {
		$text = encode_high($text);
	} else {
		$text = htmlentities($text,ENT_NOQUOTES,"utf-8");
	}

	$text = str_replace(array("&gt;", "&lt;", "&amp;"), array(">", "<", "&"), $text);

	$text = str_replace("\r\n", "\n", $text);

	$text = str_replace("\t", "", $text);

	$text = preg_split("/\n/",$text);
	foreach($text as $line){
		$line = trim($line);
		$lineout[] = $line;
	}

	$text = implode("\n",$lineout);

	$text = preg_replace('/(^|\s)==(.*)==(\s|$)?/msU','$1<notextile>$2</notextile>$3',$text);

	$text = preg_replace('/!([^\s\(=]+)\s?(?:\(([^\)]+)\))?!(\s)?/mU','<img src="$1" alt="$2" border="0" />$3',$text);

	$text = preg_replace('/(<img.+ \/>):(\S+)(\s)/U','<a href="$2">$1</a>$3',$text);

	$text = preg_replace(
		'/
		([\s[{(]|[[:punct:]])?		# 1 optional space or brackets before
		"							#   starting "
		([^"\(]+)					# 2 text of link
		\s?							#   opt space
		(?:\(([^\(]*)\))?			# 3 opt title attribute in parenths
		":							#   dividing ":
		(\S+\b)						# 4 suppose this is the url
		(\/)?						# 5 opt trailing slash
		([^[:alnum:]\/;]*)			# 6 opt punctuation after the url
		(\s|$)						# 7 either white space or end of string
		/x',
		'$1<a href="$4$5" title="$3">$2</a>$6$7',$text);

	$qtags = array(
		'\*\*'=>'b',
		'\*'=>'strong',
		'\?\?'=>'cite',
		'-'=>'del',
		'\+'=>'ins',
		'~'=>'sub',
		'@'=>'code');

	foreach($qtags as $f=>$r){
		$text = preg_replace(
			'/(^|\s|>)'.$f.'\b(.+)\b([[:punct:]]*)'.$f.'([[:punct:]]{0,2})(\s|$)?/mU',
			'$1<'.$r.'>$2$3</'.$r.'>$4$5',
			$text);
	}

	$text = preg_replace('/(^|\s)__(.*)__([[:punct:]]{0,2})(\s|$)?/mU','$1<i>$2</i>$3$4',$text);
	$text = preg_replace('/(^|\s)_(.*)_([[:punct:]]{0,2})(\s|$)?/mU','$1<em>$2</em>$3$4',$text);

	$text = preg_replace('/\^(.*)\^/mU','<sup>$1</sup>',$text);

	$text = preg_replace('/"$/',"\" ", $text);
	$glyph_search = array(
		'/([^\s[{(>])?\'(?(1)|(?=\s|s\b))/',					# single closing
		'/\'/',													# single opening
		'/([^\s[{(])?"(?(1)|(?=\s))/',							# double closing
		'/"/',													# double opening
		'/\b( )?\.{3}/',										# ellipsis
		'/\b([A-Z][A-Z0-9]{2,})\b(?:[(]([^)]*)[)])/',			# 3+ uppercase acronym
		'/(^|[^"][>\s])([A-Z][A-Z0-9 ]{2,})([^<a-z0-9]|$)/',	# 3+ uppercase caps
		'/\s?--\s?/',											# em dash
		'/\s-\s/',												# en dash
		'/(\d+) ?x ?(\d+)/',									# dimension sign
		'/\b ?[([]TM[])]/i',									# trademark
		'/\b ?[([]R[])]/i',										# registered
		'/\b ?[([]C[])]/i');									# copyright

	$glyph_replace = array(
		'$1&#8217;$2',							# single closing
		'&#8216;',								# single opening
		'$1&#8221;',							# double closing
		'&#8220;',								# double opening
		'$1&#8230;',							# ellipsis
		'<acronym title="$2">$1</acronym>',		# 3+ uppercase acronym
		'$1<span class="caps">$2</span>$3',		# 3+ uppercase caps
		'&#8212;',								# em dash
		' &#8211; ',							# en dash
		'$1&#215;$2',							# dimension sign
		'&#8482;',								# trademark
		'&#174;',								# registered
		'&#169;');								# copyright

	$codepre = false;

	if(!preg_match("/<.*>/",$text)){
		$text = preg_replace($glyph_search,$glyph_replace,$text);
	} else {

		$text = preg_split("/(<.*>)/U",$text,-1,PREG_SPLIT_DELIM_CAPTURE);
			foreach($text as $line){

					# matches are off if we're between <code>, <pre> etc.
				if(preg_match('/<(code|pre|kbd|notextile)>/i',$line)){$codepre = true; }
				if(preg_match('/<\/(code|pre|kbd|notextile)>/i',$line)){$codepre = false; }

				if(!preg_match("/<.*>/",$line) && $codepre == false){
					$line = preg_replace($glyph_search,$glyph_replace,$line);
				}

				# convert htmlspecial if between <code>
				if ($codepre == true){
					$line = htmlspecialchars($line,ENT_NOQUOTES,"UTF-8");
					$line = str_replace("&lt;pre&gt;","<pre>",$line);
					$line = str_replace("&lt;code&gt;","<code>",$line);
					$line = str_replace("&lt;notextile&gt;","<notextile>",$line);
					$line = str_replace("&lt;kbd&gt;","<kbd>",$line);
				}

				# each line gets pushed to a new array
			$glyph_out[] = $line;
		}
			# $text is now the new array, cast to a string
		$text = implode('',$glyph_out);
	}

	$text = preg_replace("/(\S)(_*)([[:punct:]]*) *\n([^#*\s])/", "$1$2$3<br />$4", $text);

	$text = str_replace("l><br />", "l>\n", $text);

	$text = preg_split("/\n/",$text);

	array_push($text," ");

			$list = '';
			$pre = false;

			$block_find = array(
				'/^\s?\*\s(.*)/',						# bulleted list *
				'/^\s?#\s(.*)/',						# numeric list #
				'/^bq\. (.*)/',							# blockquote bq.
				'/^h(\d)\(([[:alnum:]]+)\)\.\s(.*)/',	# header hn(class).  w/ css class
				'/^h(\d)\. (.*)/',						# plain header hn.
				'/^p\(([[:alnum:]]+)\)\.\s(.*)/',		# para p(class).  w/ css class
				'/^p\. (.*)/i',	 						# plain paragraph
				'/^([^\t ]+.*)/i'						# remaining plain paragraph
				);

			$block_replace = array(
				"\t\t<li>$1</li>",
				"\t\t\t<li>$1</li>",
				"\t<blockquote>$1</blockquote>",
				"\t<h$1 class=\"$2\">$3</h$1>$4",
				"\t<h$1>$2</h$1>$3",
				"\t<p class=\"$1\">$2</p>$3",
				"\t<p>$1</p>",
				"\t<p>$1</p>$2"
				);

	foreach($text as $line){

			if(preg_match('/<pre>/i',$line)){$pre = true; }

			if ($pre == false){
				$line = preg_replace($block_find,$block_replace,$line);
			}

			if ($pre == true){
				$line = str_replace("<br />","\n",$line);
			}
				if(preg_match('/<\/pre>/i',$line)){$pre = false; }

			if ($list == '' && preg_match('/^\t\t<li>/',$line)){
					$list = "ul";
				$line = preg_replace('/^(\t\t<li>.*)/',"\t<ul>\n$1",$line);

			} else if ($list == '' && preg_match('/^\t\t\t<li>/',$line)){
					$list = "ol";
				$line = preg_replace('/^\t(\t\t<li>.*)/',"\t<ol>\n$1",$line);

			# at the end of a ul
			} else if ($list == 'ul' && !preg_match('/^\t\t<li>/',$line)){
					$list = '';
				$line = preg_replace('/^(.*)$/',"\t</ul>\n$1",$line);

			# at the end of a ol
			} else if ($list == 'ol' && !preg_match('/^\t\t\t<li>/',$line)){
					$list = '';
				$line = preg_replace('/^(.*)$/',"\t</ol>\n$1",$line);
			}

			$block_out[] = $line;
	}
	$text = implode("\n",$block_out);
	$text = preg_replace('/<\/?notextile>/', "",$text);
	$text = str_replace("x%x%","&#38;",$text);
	$text = str_replace("<br />","<br />\n",$text);
	return $text;
}

function callback_url($text,$title='',$url) {
		$out = 'a href="'.$url.'"';
		$out.=($title!='')?' title="'.$title.'"':'';
		$out.='>$text</a>';
		return $out;
	}



function textile_popup_help($name,$helpvar,$windowW,$windowH) {
	$out = $name;
	$out .= ' <a target="_blank" href="http://www.textpattern.com/help/?item='.$helpvar.'"';
	$out .= ' onclick="window.open(this.href, \'popupwindow\', \'width='.$windowW.',height='.$windowH.',scrollbars,resizable\'); return false;" style="color:blue;background-color:#ddd">?</a><br />';
	print $out;
}


function encode_high($text) {
	$cmap = cmap();
	return mb_encode_numericentity($text, $cmap, "UTF-8");
}


function decode_high($text) {
	$cmap = cmap();
	return mb_decode_numericentity($text, $cmap, "UTF-8");
}


function cmap() {

	$f = 0xffff;

	$cmap = array(
	 160,  255,  0, $f,
	 402,  402,  0, $f,
	 913,  929,  0, $f,
	 931,  937,  0, $f,
	 945,  969,  0, $f,
	 977,  978,  0, $f,
	 982,  982,  0, $f,
	 8226, 8226, 0, $f,
	 8230, 8230, 0, $f,
	 8242, 8243, 0, $f,
	 8254, 8254, 0, $f,
	 8260, 8260, 0, $f,
	 8465, 8465, 0, $f,
	 8472, 8472, 0, $f,
	 8476, 8476, 0, $f,
	 8482, 8482, 0, $f,
	 8501, 8501, 0, $f,
	 8592, 8596, 0, $f,
	 8629, 8629, 0, $f,
	 8656, 8660, 0, $f,
	 8704, 8704, 0, $f,
	 8706, 8707, 0, $f,
	 8709, 8709, 0, $f,
	 8711, 8713, 0, $f,
	 8715, 8715, 0, $f,
	 8719, 8719, 0, $f,
	 8721, 8722, 0, $f,
	 8727, 8727, 0, $f,
	 8730, 8730, 0, $f,
	 8733, 8734, 0, $f,
	 8736, 8736, 0, $f,
	 8743, 8747, 0, $f,
	 8756, 8756, 0, $f,
	 8764, 8764, 0, $f,
	 8773, 8773, 0, $f,
	 8776, 8776, 0, $f,
	 8800, 8801, 0, $f,
	 8804, 8805, 0, $f,
	 8834, 8836, 0, $f,
	 8838, 8839, 0, $f,
	 8853, 8853, 0, $f,
	 8855, 8855, 0, $f,
	 8869, 8869, 0, $f,
	 8901, 8901, 0, $f,
	 8968, 8971, 0, $f,
	 9001, 9002, 0, $f,
	 9674, 9674, 0, $f,
	 9824, 9824, 0, $f,
	 9827, 9827, 0, $f,
	 9829, 9830, 0, $f,
	 338,  339,  0, $f,
	 352,  353,  0, $f,
	 376,  376,  0, $f,
	 710,  710,  0, $f,
	 732,  732,  0, $f,
	 8194, 8195, 0, $f,
	 8201, 8201, 0, $f,
	 8204, 8207, 0, $f,
	 8211, 8212, 0, $f,
	 8216, 8218, 0, $f,
	 8218, 8218, 0, $f,
	 8220, 8222, 0, $f,
	 8224, 8225, 0, $f,
	 8240, 8240, 0, $f,
	 8249, 8250, 0, $f,
	 8364, 8364, 0, $f
	 );

	return $cmap;
}


function linkit($text,$title,$url){
    $url = preg_replace("/&(?!amp;)/","&amp;",$url);
    $out = '<a href="'.$url;
    if ($title!='') {
		$title = trim($title);
        $out .= ' title="'.$title;
    }
    $out .= '>'.$text.'</a>';

    return $out;
}

// And now for the filters
remove_filter('the_content', 'wpautop');
remove_filter('the_excerpt', 'wpautop');
remove_filter('comment_text', 'wpautop');

remove_filter('the_content', 'wptexturize');
remove_filter('the_excerpt', 'wptexturize');
remove_filter('comment_text', 'wptexturize');

add_filter('the_content', 'textile', 6);
add_filter('the_excerpt', 'textile', 6);
add_filter('comment_text', 'textile', 6);

?>