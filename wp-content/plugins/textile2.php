<?php
/*
Plugin Name: Textile 2
Version: 2.0 Beta
Plugin URI: http://www.huddledmasses.org/
Description: This is a simple wrapper for <a href="http://textism.com/?wp">Dean Allen's</a> Humane Web Text Generator, also known as <a href="http://www.textism.com/tools/textile/">Textile</a>. Version 2 adds a lot of flexibility that makes it almost a HTML meta-language. As a cost, it's slower. If you use this plugin you should disable Textile 1 and Markdown, as they don't play well together.
Author: Dean Allen
Author URI: http://www.textism.com/?wp
*/

/*

This is Textile
A Humane Web Text Generator

Version 2.0 beta
8 July, 2003

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

$hlgn = "(?:\<(?!>)|(?<!<)\>|\<\>|\=|[()]+)";
$vlgn = "[\-^~]";
$clas = "(?:\([^)]+\))";
$lnge = "(?:\[[^]]+\])";
$styl = "(?:\{[^}]+\})";
$cspn = "(?:\\\\\d+)";
$rspn = "(?:\/\d+)";
$textile_a = "(?:$hlgn?$vlgn?|$vlgn?$hlgn?)";
$textile_s = "(?:$cspn?$rspn?|$rspn?$cspn?)";
$textile_c = "(?:$clas?$styl?$lnge?|$styl?$lnge?$clas?|$lnge?$styl?$clas?)";
$pnct = '[\!"#\$%&\'()\*\+,\-\./:;<=>\?@\[\\\]\^_`{\|}\~]';

function textile($text,$lite='') {

	if (get_magic_quotes_gpc()==1)
		$text = stripslashes($text);

	$text = incomingEntities($text);
	$text = encodeEntities($text);
	$text = fixEntities($text);
	$text = cleanWhiteSpace($text);

	$text = getRefs($text);

	$text = noTextile($text);
	$text = image($text);
	$text = links($text);
	$text = span($text);
	$text = superscript($text);
	$text = footnoteRef($text);
	$text = code($text);
	$text = glyphs($text);
	$text = retrieve($text);

	if ($lite=='') {
		$text = lists($text);
		$text = table($text);
		$text = block($text);
	}

		/* clean up <notextile> */
	$text = preg_replace('/<\/?notextile>/', "",$text);

		/* turn the temp char back to an ampersand entity */
	$text = str_replace("x%x%","&amp;",$text);

	$text = str_replace("<br />","<br />\n",$text);

	return trim($text);
}

function pba($in,$element="") // "parse block attributes"
{
	global $hlgn,$vlgn,$clas,$styl,$cspn,$rspn,$textile_a,$textile_s,$textile_c;

	$style=''; $class=''; $language=''; $colspan=''; $rowspan=''; $id=''; $atts='';

	if (!empty($in)) {
		$matched = $in;
		if($element=='td'){
			if(preg_match("/\\\\(\d+)/",$matched,$csp)) $colspan=$csp[1];
			if(preg_match("/\/(\d+)/",$matched,$rsp)) $rowspan=$rsp[1];

			if (preg_match("/($vlgn)/",$matched,$vert))
				$style[] = "vertical-align:".vAlign($vert[1]).";";
		}

		if(preg_match("/\{([^}]*)\}/",$matched,$sty)) {
			$style[]=$sty[1].';';
			$matched = str_replace($sty[0],'',$matched);
		}

		if(preg_match("/\[([^)]+)\]/U",$matched,$lng)) {
			$language=$lng[1];
			$matched = str_replace($lng[0],'',$matched);
		}

		if(preg_match("/\(([^()]+)\)/U",$matched,$cls)) {
			$class=$cls[1];
			$matched = str_replace($cls[0],'',$matched);
		}

		if(preg_match("/([(]+)/",$matched,$pl)) {
			$style[] = "padding-left:".strlen($pl[1])."em;";
			$matched = str_replace($pl[0],'',$matched);
		}
		if(preg_match("/([)]+)/",$matched,$pr)) {
			dump($pr);
			$style[] = "padding-right:".strlen($pr[1])."em;";
			$matched = str_replace($pr[0],'',$matched);
		}

		if (preg_match("/($hlgn)/",$matched,$horiz))
			$style[] = "text-align:".hAlign($horiz[1]).";";

		if (preg_match("/^(.*)#(.*)$/",$class,$ids)) {
			$id = $ids[2];
			$class = $ids[1];
		}

		if($style) $atts.=' style="'.join("",$style).'"';
		if($class) $atts.=' class="'.$class.'"';
		if($language) $atts.=' lang="'.$language.'"';
		if($id) $atts.=' id="'.$id.'"';
		if($colspan) $atts.=' colspan="'.$colspan.'"';
		if($rowspan) $atts.=' rowspan="'.$rowspan.'"';

		return $atts;
	} else {
		return '';
	}
}

// -------------------------------------------------------------
function table($text)
{
	global $textile_a,$textile_c,$textile_s;
	$text = $text."\n\n";
	return preg_replace_callback("/^(?:table(_?$textile_s$textile_a$textile_c)\. ?\n)?^($textile_a$textile_c\.? ?\|.*\|)\n\n/smU",
		"fTable",$text);
}

// -------------------------------------------------------------
function fTable($matches)
{
	global $textile_s,$textile_a,$textile_c;
	$tatts = pba($matches[1],'table');

	   foreach(preg_split("/\|$/m",$matches[2],-1,PREG_SPLIT_NO_EMPTY) as $row){
		if (preg_match("/^($textile_a$textile_c\. )(.*)/m",$row,$rmtch)) {
			$ratts = pba($rmtch[1],'tr');
			$row = $rmtch[2];
		} else $ratts = '';

		foreach(explode("|",$row) as $cell){
			$ctyp = "d";
			if (preg_match("/^_/",$cell)) $ctyp = "h";
			if (preg_match("/^(_?$textile_s$textile_a$textile_c\. )(.*)/",$cell,$cmtch)) {
				$catts = pba($cmtch[1],'td');
				$cell = $cmtch[2];
			} else $catts = '';

			if(trim($cell)!='')
				$cells[] = "\t\t\t<t$ctyp$catts>$cell</t$ctyp>";
		}
		$rows[] = "\t\t<tr$ratts>\n".join("\n",$cells)."\n\t\t</tr>";
		unset($cells,$catts);
	}
	return "\t<table$tatts>\n".join("\n",$rows)."\n\t</table>\n\n";
}


// -------------------------------------------------------------
function lists($text)
{
	global $textile_a,$textile_c;
	return preg_replace_callback("/^([#*]+$textile_c .*)$(?![^#*])/smU","fList",$text);
}

// -------------------------------------------------------------
function fList($m)
{
	global $textile_a,$textile_c;
	$text = explode("\n",$m[0]);
	foreach($text as $line){
		$nextline = next($text);
		if(preg_match("/^([#*]+)($textile_a$textile_c) (.*)$/s",$line,$m)) {
			list(,$tl,$atts,$content) = $m;
			$nl = preg_replace("/^([#*]+)\s.*/","$1",$nextline);
			if(!isset($lists[$tl])){
				$lists[$tl] = true;
				$atts = pba($atts);
				$line = "\t<".lT($tl)."l$atts>\n\t<li>".$content;
			} else {
				$line = "\t\t<li>".$content;
			}

			if ($nl===$tl){
				$line .= "</li>";
			} elseif($nl=="*" or $nl=="#") {
				$line .= "</li>\n\t</".lT($tl)."l>\n\t</li>";
				unset($lists[$tl]);
			}
			if (!$nl) {
				foreach($lists as $k=>$v){
					$line .= "</li>\n\t</".lT($k)."l>";
					unset($lists[$k]);
				}
			}
		}
		$out[] = $line;
	}
	return join("\n",$out);
}

// -------------------------------------------------------------
function lT($in)
{
	return preg_match("/^#+/",$in) ? 'o' : 'u';
}

// -------------------------------------------------------------
function block($text)
{
	global $textile_a,$textile_c;

	$pre = false;
	$find = array('bq','h[1-6]','fn\d+','p');

	$text = preg_replace("/(.+)\n(?![#*\s|])/",
		"$1<br />", $text);

	$text = explode("\n",$text);
	array_push($text," ");

	foreach($text as $line) {
		if (preg_match('/<pre>/i',$line)) { $pre = true; }
		foreach($find as $tag){
			$line = ($pre==false)
			?    preg_replace_callback("/^($tag)($textile_a$textile_c)\.(?::(\S+))? (.*)$/",
					"fBlock",$line)
			:    $line;
		}

		$line = preg_replace('/^(?!\t|<\/?pre|<\/?code|$| )(.*)/',"\t<p>$1</p>",$line);

		$line=($pre==true) ? str_replace("<br />","\n",$line):$line;
		if (preg_match('/<\/pre>/i',$line)) { $pre = false; }

		$out[] = $line;
	}
	return join("\n",$out);
}

// -------------------------------------------------------------
function fBlock($m)
{
#        dump($m);
	list(,$tag,$atts,$cite,$content) = $m;

	$atts = pba($atts);

	if(preg_match("/fn(\d+)/",$tag,$fns)){
		$tag = 'p';
		$atts.= ' id="fn'.$fns[1].'"';
		$content = '<sup>'.$fns[1].'</sup> '.$content;
	}

	$start = "\t<$tag";
	$end = "</$tag>";

	if ($tag=="bq") {
		$cite = checkRefs($cite);
		$cite = ($cite!='') ? ' cite="'.$cite.'"' : '';
		$start = "\t<blockquote$cite>\n\t\t<p";
		$end = "</p>\n\t</blockquote>";
	}

	return "$start$atts>$content$end";
}


// -------------------------------------------------------------
function span($text)
{
	global $textile_c,$pnct;
	$qtags = array('\*\*','\*','\?\?','-','__','_','%','\+','~');

	foreach($qtags as $f) {
		$text = preg_replace_callback(
			"/(?<=^|\s|\>|[[:punct:]]|[{(\[])
			($f)
			($textile_c)
			(?::(\S+))?
			(\w.+\w)
			([[:punct:]]*)
			$f
			(?=[])}]|[[:punct:]]+|\s|$)
		/xmU","fSpan",$text);
	}
	return $text;
}

// -------------------------------------------------------------
function fSpan($m)
{
#        dump($m);
	global $textile_c;
	$qtags = array(
		'*'   => 'b',
		'**'  => 'strong',
		'??'  => 'cite',
		'_'   => 'em',
		'__'  => 'i',
		'-'   => 'del',
		'%'   => 'span',
		'+'   => 'ins',
		'~'   => 'sub');

		list(,$tag,$atts,$cite,$content,$end) = $m;
		$tag = $qtags[$tag];
		$atts = pba($atts);
		$atts.= ($cite!='') ? 'cite="'.$cite.'"' : '';

	return "<$tag$atts>$content$end</$tag>";
}

// -------------------------------------------------------------
function links($text)
{
	global $textile_c;
	return preg_replace_callback('/
		([\s[{(]|[[:punct:]])?     # $pre
		"                          # start
		('.$textile_c.')                   # $atts
		([^"]+)                  # $text
		\s?
		(?:\(([^)]+)\)(?="))?    # $title
		":
		(\S+\b)                    # $url
		(\/)?                      # $slash
		([^\w\/;]*)                # $post
		(?=\s|$)
	/Ux',"fLink",$text);
}

// -------------------------------------------------------------
function fLink($m)
{
	list(,$pre,$atts,$text,$title,$url,$slash,$post) = $m;

	$url = checkRefs($url);

	$atts = pba($atts);
	$atts.= ($title!='') ? ' title="'.$title.'"' : '';

	$atts = ($atts!='') ? shelve($atts) : '';

	return $pre.'<a href="'.$url.$slash.'"'.$atts.'>'.$text.'</a>'.$post;

}

// -------------------------------------------------------------
function getRefs($text)
{
	return preg_replace_callback("/(?<=^|\s)\[(.+)\]((?:http:\/\/|\/)\S+)(?=\s|$)/U",
		"refs",$text);
}

// -------------------------------------------------------------
function refs($m)
{
	list(,$flag,$url) = $m;
	$GLOBALS['urlrefs'][$flag] = $url;
	return '';
}

// -------------------------------------------------------------
function checkRefs($text)
{
	global $urlrefs;
	return (isset($urlrefs[$text])) ? $urlrefs[$text] : $text;
}

// -------------------------------------------------------------
function image($text)
{
	global $textile_c;
	return preg_replace_callback("/
		\!                   # opening
		(\<|\=|\>)?          # optional alignment atts
		($textile_c)                 # optional style,class atts
		(?:\. )?             # optional dot-space
		([^\s(!]+)           # presume this is the src
		\s?                  # optional space
		(?:\(([^\)]+)\))?    # optional title
		\!                   # closing
		(?::(\S+))?          # optional href
		(?=\s|$)             # lookahead: space or end of string
	/Ux","fImage",$text);
}

// -------------------------------------------------------------
function fImage($m)
{
	list(,$algn,$atts,$url) = $m;
	$atts = pba($atts);
	$atts.= ($algn!='') ? ' align="'.iAlign($algn).'"' : '';
	$atts.= (isset($m[4])) ? ' title="'.$m[4].'"' : '';
	$size = @getimagesize($url);
	if($size) $atts.= " $size[3]";

	$href = (isset($m[5])) ? checkRefs($m[5]) : '';
	$url = checkRefs($url);

	$out = '';
	$out.= ($href!='') ? '<a href="'.$href.'">' : '';
	$out.= '<img src="'.$url.'"'.$atts.' />';
	$out.= ($href!='') ? '</a>' : '';

	return $out;
}

// -------------------------------------------------------------
function code($text)
{
	global $pnct;
	return preg_replace_callback("/
		(?:^|(?<=[\s\(])|([[{]))         # 1 open bracket?
		@                                # opening
		(?:\|(\w+)\|)?                   # 2 language
		(.+)                             # 3 code
		@                                # closing
		(?:$|([\]}])|
		(?=[[:punct:]]{1,2}|
		\s))                             # 4 closing bracket?
	/Ux","fCode",$text);
}

// -------------------------------------------------------------
function fCode($m)
{
	list(,$before,$lang,$code,$after) = $m;
	$lang = ($lang!='') ? ' language="'.$lang.'"' : '';
	return $before.'<code'.$lang.'>'.detextile($code).'</code>'.$after;
}

// -------------------------------------------------------------
function shelve($val)
{
	$GLOBALS['shelf'][] = $val;
	return ' <'.count($GLOBALS['shelf']).'>';
}

// -------------------------------------------------------------
function retrieve($text)
{
	global $shelf;
	  $i = 0;
	if(is_array($shelf)) {
	foreach($shelf as $r){
		$i++;
		$text = str_replace("<$i>",$r,$text);
	}
	}
		return $text;
}

// -------------------------------------------------------------
function incomingEntities($text)
{
	/*  turn any incoming ampersands into a dummy character for now.
		This uses a negative lookahead for alphanumerics followed by a semicolon,
		implying an incoming html entity, to be skipped */

	return preg_replace("/&(?![a-z]+;|#[0-9]+;)/i","x%x%",$text);
}

// -------------------------------------------------------------
function encodeEntities($text)
{
	/*  Convert high and low ascii to entities. If multibyte string functions are
		available (on by default in php 4.3+), we convert using unicode mapping as
		defined in the function encode_high(). If not, we use php's nasty
		built-in htmlentities() */

	return (function_exists('mb_encode_numericentity'))
	?    encode_high($text)
	:    htmlentities($text,ENT_NOQUOTES,"utf-8");
}

// -------------------------------------------------------------
function fixEntities($text)
{
	/*  de-entify any remaining angle brackets or ampersands */
	return str_replace(array("&gt;", "&lt;", "&amp;"),
		array(">", "<", "&"), $text);
}

// -------------------------------------------------------------
function cleanWhiteSpace($text)
{
	$out = str_replace(array("\r\n","\t"), array("\n",''), $text);
	$out = preg_replace("/\n{3,}/","\n\n",$out);
	$out = preg_replace("/\n *\n/","\n\n",$out);
	$out = preg_replace('/"$/',"\" ", $out);
	return $out;
}

// -------------------------------------------------------------
function noTextile($text)
{
	return preg_replace('/(^|\s)==(.*)==(\s|$)?/msU',
		'$1<notextile>$2</notextile>$3',$text);
}

// -------------------------------------------------------------
function superscript($text)
{
	return preg_replace('/\^(.*)\^/mU','<sup>$1</sup>',$text);
}

// -------------------------------------------------------------
function footnoteRef($text)
{
	return preg_replace('/\b\[([0-9]+)\](\s)?/U',
		'<sup><a href="#fn$1">$1</a></sup>$2',$text);
}

// -------------------------------------------------------------
function glyphs($text)
{
		// fix: hackish
	$text = preg_replace('/"\z/',"\" ", $text);

	$glyph_search = array(
	'/([^\s[{(>])?\'(?(1)|(?=\s|s\b|[[:punct:]]))/',        //  single closing
	'/\'/',                                                 //  single opening
	'/([^\s[{(>])?"(?(1)|(?=\s|[[:punct:]]))/',             //  double closing
	'/"/',                                                  //  double opening
	'/\b( )?\.{3}/',                                        //  ellipsis
	'/\b([A-Z][A-Z0-9]{2,})\b(?:[(]([^)]*)[)])/',           //  3+ uppercase acronym
	'/(^|[^"][>\s])([A-Z][A-Z0-9 ]{2,})([^<a-z0-9]|$)/',    //  3+ uppercase caps
	'/\s?--\s?/',                                           //  em dash
	'/\s-\s/',                                              //  en dash
	'/(\d+) ?x ?(\d+)/',                                    //  dimension sign
	'/\b ?[([]TM[])]/i',                                    //  trademark
	'/\b ?[([]R[])]/i',                                     //  registered
	'/\b ?[([]C[])]/i');                                    //  copyright

$glyph_replace = array(
	'$1&#8217;$2',                          //  single closing
	'&#8216;',                              //  single opening
	'$1&#8221;',                            //  double closing
	'&#8220;',                              //  double opening
	'$1&#8230;',                            //  ellipsis
	'<acronym title="$2">$1</acronym>',     //  3+ uppercase acronym
	'$1<span class="caps">$2</span>$3',     //  3+ uppercase caps
	'&#8212;',                              //  em dash
	' &#8211; ',                            //  en dash
	'$1&#215;$2',                           //  dimension sign
	'&#8482;',                              //  trademark
	'&#174;',                               //  registered
	'&#169;');                              //  copyright


$codepre = false;
	/*  if no html, do a simple search and replace... */
if (!preg_match("/<.*>/",$text)) {
	$text = preg_replace($glyph_search,$glyph_replace,$text);
	return $text;
} else {
	$text = preg_split("/(<.*>)/U",$text,-1,PREG_SPLIT_DELIM_CAPTURE);
	foreach($text as $line) {
			$offtags = ('code|pre|kbd|notextile');

				/*  matches are off if we're between <code>, <pre> etc. */
			if (preg_match('/<('.$offtags.')>/i',$line)) $codepre = true;
			if (preg_match('/<\/('.$offtags.')>/i',$line)) $codepre = false;

			if (!preg_match("/<.*>/",$line) && $codepre == false) {
				$line = preg_replace($glyph_search,$glyph_replace,$line);
			}

				/* do htmlspecial if between <code> */
			if ($codepre == true) {
				$line = htmlspecialchars($line,ENT_NOQUOTES,"UTF-8");
				$line = preg_replace('/&lt;(\/?'.$offtags.')&gt;/',"<$1>",$line);
			}

		$glyph_out[] = $line;
	}
	return join('',$glyph_out);
}
}

// -------------------------------------------------------------
function iAlign($in)
{
	$vals = array(
		'<'=>'left',
		'='=>'center',
		'>'=>'right');
	return (isset($vals[$in])) ? $vals[$in] : '';
}

// -------------------------------------------------------------
function hAlign($in)
{
	$vals = array(
		'<'=>'left',
		'='=>'center',
		'>'=>'right',
		'<>'=>'justify');
	return (isset($vals[$in])) ? $vals[$in] : '';
}

// -------------------------------------------------------------
function vAlign($in)
{
	$vals = array(
		'^'=>'top',
		'-'=>'middle',
		'~'=>'bottom');
	return (isset($vals[$in])) ? $vals[$in] : '';
}

// -------------------------------------------------------------
function encode_high($text,$charset="UTF-8")
{
	$cmap = cmap();
	return mb_encode_numericentity($text, $cmap, $charset);
}

// -------------------------------------------------------------
function decode_high($text,$charset="UTF-8")
{
	$cmap = cmap();
	return mb_decode_numericentity($text, $cmap, $charset);
}

// -------------------------------------------------------------
function cmap()
{
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
	 8364, 8364, 0, $f);
	return $cmap;
}


// -------------------------------------------------------------
    function textile_popup_help($name,$helpvar,$windowW,$windowH) {
        return ' <a target="_blank" href="http://www.textpattern.com/help/?item='.$helpvar.'" onclick="window.open(this.href, \'popupwindow\', \'width='.$windowW.',height='.$windowH.',scrollbars,resizable\'); return false;">'.$name.'</a><br />';

        return $out;
    }

    function txtgps($thing)
    {
        if (isset($_POST[$thing])){
            if (get_magic_quotes_gpc()==1){
                return stripslashes($_POST[$thing]);
            } else {
                return $_POST[$thing];
            }
        } else {
            return '';
        }
    }

// WordPress users.  If you want to change what is textiled, do so here!
// Default filters we don't want because of Textile 2
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