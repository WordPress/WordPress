<?php
/*
This is a compressed copy of NBBC. Do not edit!

Copyright (c) 2008-9, the Phantom Inker.  All rights reserved.
Portions Copyright (c) 2004-2008 AddedBytes.com

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:

* Redistributions of source code must retain the above copyright
  notice, this list of conditions and the following disclaimer.

* Redistributions in binary form must reproduce the above copyright
  notice, this list of conditions and the following disclaimer in
  the documentation and/or other materials provided with the
  distribution.

THIS SOFTWARE IS PROVIDED BY THE PHANTOM INKER "AS IS" AND ANY EXPRESS
OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR
BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN
IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

define("BBCODE_VERSION", "1.4.5");
define("BBCODE_RELEASE", "2010-09-17");
define("BBCODE_VERBATIM", 2);
define("BBCODE_REQUIRED", 1);
define("BBCODE_OPTIONAL", 0);
define("BBCODE_PROHIBIT", -1);
define("BBCODE_CHECK", 1);
define("BBCODE_OUTPUT", 2);
define("BBCODE_ENDTAG", 5);
define("BBCODE_TAG", 4);
define("BBCODE_TEXT", 3);
define("BBCODE_NL", 2);
define("BBCODE_WS", 1);
define("BBCODE_EOI", 0);
define("BBCODE_LEXSTATE_TEXT", 0);
define("BBCODE_LEXSTATE_TAG", 1);
define("BBCODE_MODE_SIMPLE", 0);
define("BBCODE_MODE_CALLBACK", 1);
define("BBCODE_MODE_INTERNAL", 2);
define("BBCODE_MODE_LIBRARY", 3);
define("BBCODE_MODE_ENHANCED", 4);
define("BBCODE_STACK_TOKEN", 0);
define("BBCODE_STACK_TEXT", 1);
define("BBCODE_STACK_TAG", 2);
define("BBCODE_STACK_CLASS", 3);
if (!function_exists('str_split')) {
function str_split($string, $split_length = 1) {
$array = explode("\r\n", chunk_split($string, $split_length));
array_pop($array);
return $array;
}
}
$BBCode_SourceDir = dirname(__FILE__);

class BBCodeLexer {
var $token;
var $text;
var $tag;
var $state;
var $input;
var $ptr;
var $unget;
var $verbatim;
var $debug;
var $tagmarker;
var $end_tagmarker;
var $pat_main;
var $pat_comment;
var $pat_comment2;
var $pat_wiki;
function BBCodeLexer($string, $tagmarker = '[') {
$regex_beginmarkers = Array( '[' => '\[', '<' => '<', '{' => '\{', '(' => '\(' );
$regex_endmarkers = Array( '[' => '\]', '<' => '>', '{' => '\}', '(' => '\)' );
$endmarkers = Array( '[' => ']', '<' => '>', '{' => '}', '(' => ')' );
if (!isset($regex_endmarkers[$tagmarker])) $tagmarker = '[';
$e = $regex_endmarkers[$tagmarker];
$b = $regex_beginmarkers[$tagmarker];
$this->tagmarker = $tagmarker;
$this->end_tagmarker = $endmarkers[$tagmarker];
$this->pat_main = "/( "
. "{$b}"
. "(?! -- | ' | !-- | {$b}{$b} )"
. "(?: [^\\n\\r{$b}{$e}] | \\\" [^\\\"\\n\\r]* \\\" | \\' [^\\'\\n\\r]* \\' )*"
. "{$e}"
. "| {$b}{$b} (?: [^{$e}\\r\\n] | {$e}[^{$e}\\r\\n] )* {$e}{$e}"
. "| {$b} (?: -- | ' ) (?: [^{$e}\\n\\r]* ) {$e}"
. "| {$b}!-- (?: [^-] | -[^-] | --[^{$e}] )* --{$e}"
. "| -----+"
. "| \\x0D\\x0A | \\x0A\\x0D | \\x0D | \\x0A"
. "| [\\x00-\\x09\\x0B-\\x0C\\x0E-\\x20]+(?=[\\x0D\\x0A{$b}]|-----|$)"
. "| (?<=[\\x0D\\x0A{$e}]|-----|^)[\\x00-\\x09\\x0B-\\x0C\\x0E-\\x20]+"
. " )/Dx";
$this->input = preg_split($this->pat_main, $string, -1, PREG_SPLIT_DELIM_CAPTURE);
$this->pat_comment = "/^ {$b} (?: -- | ' ) /Dx";
$this->pat_comment2 = "/^ {$b}!-- (?: [^-] | -[^-] | --[^{$e}] )* --{$e} $/Dx";
$this->pat_wiki = "/^ {$b}{$b} ([^\\|]*) (?:\\|(.*))? {$e}{$e} $/Dx";
$this->ptr = 0;
$this->unget = false;
$this->state = BBCODE_LEXSTATE_TEXT;
$this->verbatim = false;
$this->token = BBCODE_EOI;
$this->tag = false;
$this->text = "";
}
function GuessTextLength() {
$length = 0;
$ptr = 0;
$state = BBCODE_LEXSTATE_TEXT;
while ($ptr < count($this->input)) {
$text = $this->input[$ptr++];
if ($state == BBCODE_LEXSTATE_TEXT) {
$state = BBCODE_LEXSTATE_TAG;
$length += strlen($text);
}
else {
switch (ord(substr($this->text, 0, 1))) {
case 10:
case 13:
$state = BBCODE_LEXSTATE_TEXT;
$length++;
break;
default:
$state = BBCODE_LEXSTATE_TEXT;
$length += strlen($text);
break;
case 40:
case 60:
case 91:
case 123:
$state = BBCODE_LEXSTATE_TEXT;
break;
}
}
}
return $length;
}
function NextToken() {
if ($this->unget) {
$this->unget = false;
return $this->token;
}
while (true) {
if ($this->ptr >= count($this->input)) {
$this->text = "";
$this->tag = false;
return $this->token = BBCODE_EOI;
}
$this->text = preg_replace("/[\\x00-\\x08\\x0B-\\x0C\\x0E-\\x1F]/", "",
$this->input[$this->ptr++]);
if ($this->verbatim) {
$this->tag = false;
if ($this->state == BBCODE_LEXSTATE_TEXT) {
$this->state = BBCODE_LEXSTATE_TAG;
$token_type = BBCODE_TEXT;
}
else {
$this->state = BBCODE_LEXSTATE_TEXT;
switch (ord(substr($this->text, 0, 1))) {
case 10:
case 13:
$token_type = BBCODE_NL;
break;
default:
$token_type = BBCODE_WS;
break;
case 45:
case 40:
case 60:
case 91:
case 123:
$token_type = BBCODE_TEXT;
break;
}
}
if (strlen($this->text) > 0)
return $this->token = $token_type;
}
else if ($this->state == BBCODE_LEXSTATE_TEXT) {
$this->state = BBCODE_LEXSTATE_TAG;
$this->tag = false;
if (strlen($this->text) > 0)
return $this->token = BBCODE_TEXT;
}
else {
switch (ord(substr($this->text, 0, 1))) {
case 10:
case 13:
$this->tag = false;
$this->state = BBCODE_LEXSTATE_TEXT;
return $this->token = BBCODE_NL;
case 45:
if (preg_match("/^-----/", $this->text)) {
$this->tag = Array('_name' => 'rule', '_endtag' => false, '_default' => '');
$this->state = BBCODE_LEXSTATE_TEXT;
return $this->token = BBCODE_TAG;
}
else {
$this->tag = false;
$this->state = BBCODE_LEXSTATE_TEXT;
if (strlen($this->text) > 0)
return $this->token = BBCODE_TEXT;
continue;
}
default:
$this->tag = false;
$this->state = BBCODE_LEXSTATE_TEXT;
return $this->token = BBCODE_WS;
case 40:
case 60:
case 91:
case 123:
if (preg_match($this->pat_comment, $this->text)) {
$this->state = BBCODE_LEXSTATE_TEXT;
continue;
}
if (preg_match($this->pat_comment2, $this->text)) {
$this->state = BBCODE_LEXSTATE_TEXT;
continue;
}
if (preg_match($this->pat_wiki, $this->text, $matches)) {
$this->tag = Array('_name' => 'wiki', '_endtag' => false,
'_default' => @$matches[1], 'title' => @$matches[2]);
$this->state = BBCODE_LEXSTATE_TEXT;
return $this->token = BBCODE_TAG;
}
$this->tag = $this->Internal_DecodeTag($this->text);
$this->state = BBCODE_LEXSTATE_TEXT;
return $this->token = ($this->tag['_end'] ? BBCODE_ENDTAG : BBCODE_TAG);
}
}
}
}
function UngetToken() {
if ($this->token !== BBCODE_EOI)
$this->unget = true;
}
function PeekToken() {
$result = $this->NextToken();
if ($this->token !== BBCODE_EOI)
$this->unget = true;
return $result;
}
function SaveState() {
return Array(
'token' => $this->token,
'text' => $this->text,
'tag' => $this->tag,
'state' => $this->state,
'input' => $this->input,
'ptr' => $this->ptr,
'unget' => $this->unget,
'verbatim' => $this->verbatim
);
}
function RestoreState($state) {
if (!is_array($state)) return;
$this->token = @$state['token'];
$this->text = @$state['text'];
$this->tag = @$state['tag'];
$this->state = @$state['state'];
$this->input = @$state['input'];
$this->ptr = @$state['ptr'];
$this->unget = @$state['unget'];
$this->verbatim = @$state['verbatim'];
}
function Internal_StripQuotes($string) {
if (preg_match("/^\\\"(.*)\\\"$/", $string, $matches))
return $matches[1];
else if (preg_match("/^\\'(.*)\\'$/", $string, $matches))
return $matches[1];
else return $string;
}
function Internal_ClassifyPiece($ptr, $pieces) {
if ($ptr >= count($pieces)) return -1;
$piece = $pieces[$ptr];
if ($piece == '=') return '=';
else if (preg_match("/^[\\'\\\"]/", $piece)) return '"';
else if (preg_match("/^[\\x00-\\x20]+$/", $piece)) return ' ';
else return 'A';
}
function Internal_DecodeTag($tag) {
$result = Array('_tag' => $tag, '_endtag' => '', '_name' => '',
'_hasend' => false, '_end' => false, '_default' => false);
$tag = substr($tag, 1, strlen($tag)-2);
$ch = ord(substr($tag, 0, 1));
if ($ch >= 0 && $ch <= 32) return $result;
$pieces = preg_split("/(\\\"[^\\\"]+\\\"|\\'[^\\']+\\'|=|[\\x00-\\x20]+)/",
$tag, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
$ptr = 0;
if (count($pieces) < 1) return $result;
if (@substr($pieces[$ptr], 0, 1) == '/') {
$result['_name'] = strtolower(substr($pieces[$ptr++], 1));
$result['_end'] = true;
}
else {
$result['_name'] = strtolower($pieces[$ptr++]);
$result['_end'] = false;
}
while (($type = $this->Internal_ClassifyPiece($ptr, $pieces)) == ' ')
$ptr++;
$params = Array();
if ($type != '=') {
$result['_default'] = false;
$params[] = Array('key' => '', 'value' => '');
}
else {
$ptr++;
while (($type = $this->Internal_ClassifyPiece($ptr, $pieces)) == ' ')
$ptr++;
if ($type == "\"")
$value = $this->Internal_StripQuotes($pieces[$ptr++]);
else {
$after_space = false;
$start = $ptr;
while (($type = $this->Internal_ClassifyPiece($ptr, $pieces)) != -1) {
if ($type == ' ') $after_space = true;
if ($type == '=' && $after_space) break;
$ptr++;
}
if ($type == -1) $ptr--;
if ($type == '=') {
$ptr--;
while ($ptr > $start && $this->Internal_ClassifyPiece($ptr, $pieces) == ' ')
$ptr--;
while ($ptr > $start && $this->Internal_ClassifyPiece($ptr, $pieces) != ' ')
$ptr--;
}
$value = "";
for (; $start <= $ptr; $start++) {
if ($this->Internal_ClassifyPiece($start, $pieces) == ' ')
$value .= " ";
else $value .= $this->Internal_StripQuotes($pieces[$start]);
}
$value = trim($value);
$ptr++;
}
$result['_default'] = $value;
$params[] = Array('key' => '', 'value' => $value);
}
while (($type = $this->Internal_ClassifyPiece($ptr, $pieces)) != -1) {
while ($type == ' ') {
$ptr++;
$type = $this->Internal_ClassifyPiece($ptr, $pieces);
}
if ($type == 'A' || $type == '"')
$key = strtolower($this->Internal_StripQuotes(@$pieces[$ptr++]));
else if ($type == '=') {
$ptr++;
continue;
}
else if ($type == -1) break;
while (($type = $this->Internal_ClassifyPiece($ptr, $pieces)) == ' ')
$ptr++;
if ($type != '=')
$value = $this->Internal_StripQuotes($key);
else {
$ptr++;
while (($type = $this->Internal_ClassifyPiece($ptr, $pieces)) == ' ')
$ptr++;
if ($type == '"') {
$value = $this->Internal_StripQuotes($pieces[$ptr++]);
}
else if ($type != -1) {
$value = $pieces[$ptr++];
while (($type = $this->Internal_ClassifyPiece($ptr, $pieces)) != -1
&& $type != ' ')
$value .= $pieces[$ptr++];
}
else $value = "";
}
if (substr($key, 0, 1) != '_')
$result[$key] = $value;
$params[] = Array('key' => $key, 'value' => $value);
}
$result['_params'] = $params;
return $result;
}
}

class BBCodeLibrary {
var $default_smileys = Array(
':)' => 'smile.gif', ':-)' => 'smile.gif',
'=)' => 'smile.gif', '=-)' => 'smile.gif',
':(' => 'frown.gif', ':-(' => 'frown.gif',
'=(' => 'frown.gif', '=-(' => 'frown.gif',
':D' => 'bigsmile.gif', ':-D' => 'bigsmile.gif',
'=D' => 'bigsmile.gif', '=-D' => 'bigsmile.gif',
'>:('=> 'angry.gif', '>:-('=> 'angry.gif',
'>=('=> 'angry.gif', '>=-('=> 'angry.gif',
'D:' => 'angry.gif', 'D-:' => 'angry.gif',
'D=' => 'angry.gif', 'D-=' => 'angry.gif',
'>:)'=> 'evil.gif', '>:-)'=> 'evil.gif',
'>=)'=> 'evil.gif', '>=-)'=> 'evil.gif',
'>:D'=> 'evil.gif', '>:-D'=> 'evil.gif',
'>=D'=> 'evil.gif', '>=-D'=> 'evil.gif',
'>;)'=> 'sneaky.gif', '>;-)'=> 'sneaky.gif',
'>;D'=> 'sneaky.gif', '>;-D'=> 'sneaky.gif',
'O:)' => 'saint.gif', 'O:-)' => 'saint.gif',
'O=)' => 'saint.gif', 'O=-)' => 'saint.gif',
':O' => 'surprise.gif', ':-O' => 'surprise.gif',
'=O' => 'surprise.gif', '=-O' => 'surprise.gif',
':?' => 'confuse.gif', ':-?' => 'confuse.gif',
'=?' => 'confuse.gif', '=-?' => 'confuse.gif',
':s' => 'worry.gif', ':-S' => 'worry.gif',
'=s' => 'worry.gif', '=-S' => 'worry.gif',
':|' => 'neutral.gif', ':-|' => 'neutral.gif',
'=|' => 'neutral.gif', '=-|' => 'neutral.gif',
':I' => 'neutral.gif', ':-I' => 'neutral.gif',
'=I' => 'neutral.gif', '=-I' => 'neutral.gif',
':/' => 'irritated.gif', ':-/' => 'irritated.gif',
'=/' => 'irritated.gif', '=-/' => 'irritated.gif',
':\\' => 'irritated.gif', ':-\\' => 'irritated.gif',
'=\\' => 'irritated.gif', '=-\\' => 'irritated.gif',
':P' => 'tongue.gif', ':-P' => 'tongue.gif',
'=P' => 'tongue.gif', '=-P' => 'tongue.gif',
'X-P' => 'tongue.gif',
'8)' => 'bigeyes.gif', '8-)' => 'bigeyes.gif',
'B)' => 'cool.gif', 'B-)' => 'cool.gif',
';)' => 'wink.gif', ';-)' => 'wink.gif',
';D' => 'bigwink.gif', ';-D' => 'bigwink.gif',
'^_^'=> 'anime.gif', '^^;' => 'sweatdrop.gif',
'>_>'=> 'lookright.gif', '>.>' => 'lookright.gif',
'<_<'=> 'lookleft.gif', '<.<' => 'lookleft.gif',
'XD' => 'laugh.gif', 'X-D' => 'laugh.gif',
';D' => 'bigwink.gif', ';-D' => 'bigwink.gif',
':3' => 'smile3.gif', ':-3' => 'smile3.gif',
'=3' => 'smile3.gif', '=-3' => 'smile3.gif',
';3' => 'wink3.gif', ';-3' => 'wink3.gif',
'<g>' => 'teeth.gif', '<G>' => 'teeth.gif',
'o.O' => 'boggle.gif', 'O.o' => 'boggle.gif',
':blue:' => 'blue.gif',
':zzz:' => 'sleepy.gif',
'<3' => 'heart.gif',
':star:' => 'star.gif',
);
var $default_tag_rules = Array(
'b' => Array(
'simple_start' => "<b>",
'simple_end' => "</b>",
'class' => 'inline',
'allow_in' => Array('listitem', 'block', 'columns', 'inline', 'link'),
'plain_start' => "<b>",
'plain_end' => "</b>",
),
'i' => Array(
'simple_start' => "<i>",
'simple_end' => "</i>",
'class' => 'inline',
'allow_in' => Array('listitem', 'block', 'columns', 'inline', 'link'),
'plain_start' => "<i>",
'plain_end' => "</i>",
),
'u' => Array(
'simple_start' => "<u>",
'simple_end' => "</u>",
'class' => 'inline',
'allow_in' => Array('listitem', 'block', 'columns', 'inline', 'link'),
'plain_start' => "<u>",
'plain_end' => "</u>",
),
's' => Array(
'simple_start' => "<strike>",
'simple_end' => "</strike>",
'class' => 'inline',
'allow_in' => Array('listitem', 'block', 'columns', 'inline', 'link'),
'plain_start' => "<i>",
'plain_end' => "</i>",
),
'font' => Array(
'mode' => BBCODE_MODE_LIBRARY,
'allow' => Array('_default' => '/^[a-zA-Z0-9._ -]+$/'),
'method' => 'DoFont',
'class' => 'inline',
'allow_in' => Array('listitem', 'block', 'columns', 'inline', 'link'),
),
'color' => Array(
'mode' => BBCODE_MODE_ENHANCED,
'allow' => Array('_default' => '/^#?[a-zA-Z0-9._ -]+$/'),
'template' => '<span style="color:{$_default/tw}">{$_content/v}</span>',
'class' => 'inline',
'allow_in' => Array('listitem', 'block', 'columns', 'inline', 'link'),
),
'size' => Array(
'mode' => BBCODE_MODE_LIBRARY,
'allow' => Array('_default' => '/^[0-9.]+$/D'),
'method' => 'DoSize',
'class' => 'inline',
'allow_in' => Array('listitem', 'block', 'columns', 'inline', 'link'),
),
'sup' => Array(
'simple_start' => "<sup>",
'simple_end' => "</sup>",
'class' => 'inline',
'allow_in' => Array('listitem', 'block', 'columns', 'inline', 'link'),
),
'sub' => Array(
'simple_start' => "<sub>",
'simple_end' => "</sub>",
'class' => 'inline',
'allow_in' => Array('listitem', 'block', 'columns', 'inline', 'link'),
),
'spoiler' => Array(
'simple_start' => "<span class=\"bbcode_spoiler\">",
'simple_end' => "</span>",
'class' => 'inline',
'allow_in' => Array('listitem', 'block', 'columns', 'inline', 'link'),
),
'acronym' => Array(
'mode' => BBCODE_MODE_ENHANCED,
'template' => '<span class="bbcode_acronym" title="{$_default/e}">{$_content/v}</span>',
'class' => 'inline',
'allow_in' => Array('listitem', 'block', 'columns', 'inline', 'link'),
),
'url' => Array(
'mode' => BBCODE_MODE_LIBRARY,
'method' => 'DoURL',
'class' => 'link',
'allow_in' => Array('listitem', 'block', 'columns', 'inline'),
'content' => BBCODE_REQUIRED,
'plain_start' => "<a href=\"{\$link}\">",
'plain_end' => "</a>",
'plain_content' => Array('_content', '_default'),
'plain_link' => Array('_default', '_content'),
),
'email' => Array(
'mode' => BBCODE_MODE_LIBRARY,
'method' => 'DoEmail',
'class' => 'link',
'allow_in' => Array('listitem', 'block', 'columns', 'inline'),
'content' => BBCODE_REQUIRED,
'plain_start' => "<a href=\"mailto:{\$link}\">",
'plain_end' => "</a>",
'plain_content' => Array('_content', '_default'),
'plain_link' => Array('_default', '_content'),
),
'wiki' => Array(
'mode' => BBCODE_MODE_LIBRARY,
'method' => "DoWiki",
'class' => 'link',
'allow_in' => Array('listitem', 'block', 'columns', 'inline'),
'end_tag' => BBCODE_PROHIBIT,
'content' => BBCODE_PROHIBIT,
'plain_start' => "<b>[",
'plain_end' => "]</b>",
'plain_content' => Array('title', '_default'),
'plain_link' => Array('_default', '_content'),
),
'img' => Array(
'mode' => BBCODE_MODE_LIBRARY,
'method' => "DoImage",
'class' => 'image',
'allow_in' => Array('listitem', 'block', 'columns', 'inline', 'link'),
'end_tag' => BBCODE_REQUIRED,
'content' => BBCODE_REQUIRED,
'plain_start' => "[image]",
'plain_content' => Array(),
),
'rule' => Array(
'mode' => BBCODE_MODE_LIBRARY,
'method' => "DoRule",
'class' => 'block',
'allow_in' => Array('listitem', 'block', 'columns'),
'end_tag' => BBCODE_PROHIBIT,
'content' => BBCODE_PROHIBIT,
'before_tag' => "sns",
'after_tag' => "sns",
'plain_start' => "\n-----\n",
'plain_end' => "",
'plain_content' => Array(),
),
'br' => Array(
'mode' => BBCODE_MODE_SIMPLE,
'simple_start' => "<br />\n",
'simple_end' => "",
'class' => 'inline',
'allow_in' => Array('listitem', 'block', 'columns', 'inline', 'link'),
'end_tag' => BBCODE_PROHIBIT,
'content' => BBCODE_PROHIBIT,
'before_tag' => "s",
'after_tag' => "s",
'plain_start' => "\n",
'plain_end' => "",
'plain_content' => Array(),
),
'left' => Array(
'simple_start' => "\n<div class=\"bbcode_left\" style=\"text-align:left\">\n",
'simple_end' => "\n</div>\n",
'allow_in' => Array('listitem', 'block', 'columns'),
'before_tag' => "sns",
'after_tag' => "sns",
'before_endtag' => "sns",
'after_endtag' => "sns",
'plain_start' => "\n",
'plain_end' => "\n",
),
'right' => Array(
'simple_start' => "\n<div class=\"bbcode_right\" style=\"text-align:right\">\n",
'simple_end' => "\n</div>\n",
'allow_in' => Array('listitem', 'block', 'columns'),
'before_tag' => "sns",
'after_tag' => "sns",
'before_endtag' => "sns",
'after_endtag' => "sns",
'plain_start' => "\n",
'plain_end' => "\n",
),
'center' => Array(
'simple_start' => "\n<div class=\"bbcode_center\" style=\"text-align:center\">\n",
'simple_end' => "\n</div>\n",
'allow_in' => Array('listitem', 'block', 'columns'),
'before_tag' => "sns",
'after_tag' => "sns",
'before_endtag' => "sns",
'after_endtag' => "sns",
'plain_start' => "\n",
'plain_end' => "\n",
),
'indent' => Array(
'simple_start' => "\n<div class=\"bbcode_indent\" style=\"margin-left:4em\">\n",
'simple_end' => "\n</div>\n",
'allow_in' => Array('listitem', 'block', 'columns'),
'before_tag' => "sns",
'after_tag' => "sns",
'before_endtag' => "sns",
'after_endtag' => "sns",
'plain_start' => "\n",
'plain_end' => "\n",
),
'columns' => Array(
'simple_start' => "\n<table class=\"bbcode_columns\"><tbody><tr><td class=\"bbcode_column bbcode_firstcolumn\">\n",
'simple_end' => "\n</td></tr></tbody></table>\n",
'class' => 'columns',
'allow_in' => Array('listitem', 'block', 'columns'),
'end_tag' => BBCODE_REQUIRED,
'content' => BBCODE_REQUIRED,
'before_tag' => "sns",
'after_tag' => "sns",
'before_endtag' => "sns",
'after_endtag' => "sns",
'plain_start' => "\n",
'plain_end' => "\n",
),
'nextcol' => Array(
'simple_start' => "\n</td><td class=\"bbcode_column\">\n",
'class' => 'nextcol',
'allow_in' => Array('columns'),
'end_tag' => BBCODE_PROHIBIT,
'content' => BBCODE_PROHIBIT,
'before_tag' => "sns",
'after_tag' => "sns",
'before_endtag' => "sns",
'after_endtag' => "sns",
'plain_start' => "\n",
'plain_end' => "",
),
'code' => Array(
'mode' => BBCODE_MODE_ENHANCED,
'template' => "\n<div class=\"bbcode_code\">\n<div class=\"bbcode_code_head\">Code:</div>\n<div class=\"bbcode_code_body\" style=\"white-space:pre\">{\$_content/v}</div>\n</div>\n",
'class' => 'code',
'allow_in' => Array('listitem', 'block', 'columns'),
'content' => BBCODE_VERBATIM,
'before_tag' => "sns",
'after_tag' => "sn",
'before_endtag' => "sn",
'after_endtag' => "sns",
'plain_start' => "\n<b>Code:</b>\n",
'plain_end' => "\n",
),
'quote' => Array(
'mode' => BBCODE_MODE_LIBRARY,
'method' => "DoQuote",
'allow_in' => Array('listitem', 'block', 'columns'),
'before_tag' => "sns",
'after_tag' => "sns",
'before_endtag' => "sns",
'after_endtag' => "sns",
'plain_start' => "\n<b>Quote:</b>\n",
'plain_end' => "\n",
),
'list' => Array(
'mode' => BBCODE_MODE_LIBRARY,
'method' => 'DoList',
'class' => 'list',
'allow_in' => Array('listitem', 'block', 'columns'),
'before_tag' => "sns",
'after_tag' => "sns",
'before_endtag' => "sns",
'after_endtag' => "sns",
'plain_start' => "\n",
'plain_end' => "\n",
),
'*' => Array(
'simple_start' => "<li>",
'simple_end' => "</li>\n",
'class' => 'listitem',
'allow_in' => Array('list'),
'end_tag' => BBCODE_OPTIONAL,
'before_tag' => "s",
'after_tag' => "s",
'before_endtag' => "sns",
'after_endtag' => "sns",
'plain_start' => "\n * ",
'plain_end' => "\n",
),
);
function DoURL($bbcode, $action, $name, $default, $params, $content) {
if ($action == BBCODE_CHECK) return true;
$url = is_string($default) ? $default : $bbcode->UnHTMLEncode(strip_tags($content));
if ($bbcode->IsValidURL($url)) {
if ($bbcode->debug)
print "ISVALIDURL<br />";
if ($bbcode->url_targetable !== false && isset($params['target']))
$target = " target=\"" . htmlspecialchars($params['target']) . "\"";
else $target = "";
if ($bbcode->url_target !== false)
if (!($bbcode->url_targetable == 'override' && isset($params['target'])))
$target = " target=\"" . htmlspecialchars($bbcode->url_target) . "\"";
return '<a href="' . htmlspecialchars($url) . '" class="bbcode_url"' . $target . '>' . $content . '</a>';
}
else return htmlspecialchars($params['_tag']) . $content . htmlspecialchars($params['_endtag']);
}
function DoEmail($bbcode, $action, $name, $default, $params, $content) {
if ($action == BBCODE_CHECK) return true;
$email = is_string($default) ? $default : $bbcode->UnHTMLEncode(strip_tags($content));
if ($bbcode->IsValidEmail($email))
return '<a href="mailto:' . htmlspecialchars($email) . '" class="bbcode_email">' . $content . '</a>';
else return htmlspecialchars($params['_tag']) . $content . htmlspecialchars($params['_endtag']);
}
function DoSize($bbcode, $action, $name, $default, $params, $content) {
switch ($default) {
case '0': $size = '.5em'; break;
case '1': $size = '.67em'; break;
case '2': $size = '.83em'; break;
default:
case '3': $size = '1.0em'; break;
case '4': $size = '1.17em'; break;
case '5': $size = '1.5em'; break;
case '6': $size = '2.0em'; break;
case '7': $size = '2.5em'; break;
}
return "<span style=\"font-size:$size\">$content</span>";
}
function DoFont($bbcode, $action, $name, $default, $params, $content) {
$fonts = explode(",", $default);
$result = "";
$special_fonts = Array(
'serif' => 'serif',
'sans-serif' => 'sans-serif',
'sans serif' => 'sans-serif',
'sansserif' => 'sans-serif',
'sans' => 'sans-serif',
'cursive' => 'cursive',
'fantasy' => 'fantasy',
'monospace' => 'monospace',
'mono' => 'monospace',
);
foreach ($fonts as $font) {
$font = trim($font);
if (isset($special_fonts[$font])) {
if (strlen($result) > 0) $result .= ",";
$result .= $special_fonts[$font];
}
else if (strlen($font) > 0) {
if (strlen($result) > 0) $result .= ",";
$result .= "'$font'";
}
}
return "<span style=\"font-family:$result\">$content</span>";
}
function DoWiki($bbcode, $action, $name, $default, $params, $content) {
$name = $bbcode->Wikify($default);
if ($action == BBCODE_CHECK)
return strlen($name) > 0;
$title = trim(@$params['title']);
if (strlen($title) <= 0) $title = trim($default);
return "<a href=\"{$bbcode->wiki_url}$name\" class=\"bbcode_wiki\">"
. htmlspecialchars($title) . "</a>";
}
function DoImage($bbcode, $action, $name, $default, $params, $content) {
if ($action == BBCODE_CHECK) return true;
$content = trim($bbcode->UnHTMLEncode(strip_tags($content)));
if (preg_match("/\\.(?:gif|jpeg|jpg|jpe|png)$/", $content)) {
if (preg_match("/^[a-zA-Z0-9_][^:]+$/", $content)) {
if (!preg_match("/(?:\\/\\.\\.\\/)|(?:^\\.\\.\\/)|(?:^\\/)/", $content)) {
$info = @getimagesize("{$bbcode->local_img_dir}/{$content}");
if ($info[2] == IMAGETYPE_GIF || $info[2] == IMAGETYPE_JPEG || $info[2] == IMAGETYPE_PNG) {
return "<img src=\""
. htmlspecialchars("{$bbcode->local_img_url}/{$content}") . "\" alt=\""
. htmlspecialchars(basename($content)) . "\" width=\""
. htmlspecialchars($info[0]) . "\" height=\""
. htmlspecialchars($info[1]) . "\" class=\"bbcode_img\" />";
}
}
}
else if ($bbcode->IsValidURL($content, false)) {
return "<img src=\"" . htmlspecialchars($content) . "\" alt=\""
. htmlspecialchars(basename($content)) . "\" class=\"bbcode_img\" />";
}
}
return htmlspecialchars($params['_tag']) . htmlspecialchars($content) . htmlspecialchars($params['_endtag']);
}
function DoRule($bbcode, $action, $name, $default, $params, $content) {
if ($action == BBCODE_CHECK) return true;
else return $bbcode->rule_html;
}
function DoQuote($bbcode, $action, $name, $default, $params, $content) {
if ($action == BBCODE_CHECK) return true;
if (isset($params['name'])) {
$title = htmlspecialchars(trim($params['name'])) . " wrote";
if (isset($params['date']))
$title .= " on " . htmlspecialchars(trim($params['date']));
$title .= ":";
if (isset($params['url'])) {
$url = trim($params['url']);
if ($bbcode->IsValidURL($url))
$title = "<a href=\"" . htmlspecialchars($params['url']) . "\">" . $title . "</a>";
}
}
else if (!is_string($default))
$title = "Quote:";
else $title = htmlspecialchars(trim($default)) . " wrote:";
return "\n<div class=\"bbcode_quote\">\n<div class=\"bbcode_quote_head\">"
. $title . "</div>\n<div class=\"bbcode_quote_body\">"
. $content . "</div>\n</div>\n";
}
function DoList($bbcode, $action, $name, $default, $params, $content) {
$list_styles = Array(
'1' => 'decimal',
'01' => 'decimal-leading-zero',
'i' => 'lower-roman',
'I' => 'upper-roman',
'a' => 'lower-alpha',
'A' => 'upper-alpha',
);
$ci_list_styles = Array(
'circle' => 'circle',
'disc' => 'disc',
'square' => 'square',
'greek' => 'lower-greek',
'armenian' => 'armenian',
'georgian' => 'georgian',
);
$ul_types = Array(
'circle' => 'circle',
'disc' => 'disc',
'square' => 'square',
);
$default = trim($default);
if ($action == BBCODE_CHECK) {
if (!is_string($default) || strlen($default) == "") return true;
else if (isset($list_styles[$default])) return true;
else if (isset($ci_list_styles[strtolower($default)])) return true;
else return false;
}
if (!is_string($default) || strlen($default) == "") {
$elem = 'ul';
$type = '';
}
else if ($default == '1') {
$elem = 'ol';
$type = '';
}
else if (isset($list_styles[$default])) {
$elem = 'ol';
$type = $list_styles[$default];
}
else {
$default = strtolower($default);
if (isset($ul_types[$default])) {
$elem = 'ul';
$type = $ul_types[$default];
}
else if (isset($ci_list_styles[$default])) {
$elem = 'ol';
$type = $ci_list_styles[$default];
}
}
if (strlen($type))
return "\n<$elem class=\"bbcode_list\" style=\"list-style-type:$type\">\n$content</$elem>\n";
else return "\n<$elem class=\"bbcode_list\">\n$content</$elem>\n";
}
}

class BBCodeEmailAddressValidator {
function check_email_address($strEmailAddress) {
if (preg_match('/[\x00-\x1F\x7F-\xFF]/', $strEmailAddress)) {
return false;
}
$intAtSymbol = strrpos($strEmailAddress, '@');
if ($intAtSymbol === false) {
return false;
}
$arrEmailAddress[0] = substr($strEmailAddress, 0, $intAtSymbol);
$arrEmailAddress[1] = substr($strEmailAddress, $intAtSymbol + 1);
$arrTempAddress[0] = preg_replace('/"[^"]+"/'
,''
,$arrEmailAddress[0]);
$arrTempAddress[1] = $arrEmailAddress[1];
$strTempAddress = $arrTempAddress[0] . $arrTempAddress[1];
if (strrpos($strTempAddress, '@') !== false) {
return false;
}
if (!$this->check_local_portion($arrEmailAddress[0])) {
return false;
}
if (!$this->check_domain_portion($arrEmailAddress[1])) {
return false;
}
return true;
}
function check_local_portion($strLocalPortion) {
if (!$this->check_text_length($strLocalPortion, 1, 64)) {
return false;
}
$arrLocalPortion = explode('.', $strLocalPortion);
for ($i = 0, $max = sizeof($arrLocalPortion); $i < $max; $i++) {
if (!preg_match('.^('
. '([A-Za-z0-9!#$%&\'*+/=?^_`{|}~-]'
. '[A-Za-z0-9!#$%&\'*+/=?^_`{|}~-]{0,63})'
.'|'
. '("[^\\\"]{0,62}")'
.')$.'
,$arrLocalPortion[$i])) {
return false;
}
}
return true;
}
function check_domain_portion($strDomainPortion) {
if (!$this->check_text_length($strDomainPortion, 1, 255)) {
return false;
}
if (preg_match('/^(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])'
.'(\.(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])){3}$/'
,$strDomainPortion) ||
preg_match('/^\[(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])'
.'(\.(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])){3}\]$/'
,$strDomainPortion)) {
return true;
} else {
$arrDomainPortion = explode('.', $strDomainPortion);
if (sizeof($arrDomainPortion) < 2) {
return false;
}
for ($i = 0, $max = sizeof($arrDomainPortion); $i < $max; $i++) {
if (!$this->check_text_length($arrDomainPortion[$i], 1, 63)) {
return false;
}
if (!preg_match('/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|'
.'([A-Za-z0-9]+))$/', $arrDomainPortion[$i])) {
return false;
}
}
}
return true;
}
function check_text_length($strText, $intMinimum, $intMaximum) {
$intTextLength = strlen($strText);
if (($intTextLength < $intMinimum) || ($intTextLength > $intMaximum)) {
return false;
} else {
return true;
}
}
}

class BBCode {
var $tag_rules;
var $defaults;
var $current_class;
var $root_class;
var $lost_start_tags;
var $start_tags;
var $allow_ampersand;
var $tag_marker;
var $ignore_newlines;
var $plain_mode;
var $detect_urls;
var $url_pattern;
var $output_limit;
var $text_length;
var $was_limited;
var $limit_tail;
var $limit_precision;
var $smiley_dir;
var $smiley_url;
var $smileys;
var $smiley_regex;
var $enable_smileys;
var $wiki_url;
var $local_img_dir;
var $local_img_url;
var $url_targetable;
var $url_target;
var $rule_html;
var $pre_trim;
var $post_trim;
var $debug;


/* ADDED */
// singleton instance
private static $instance;

// private constructor function
// to prevent external instantiation
private function __construct()
{
	$this->defaults = new BBCodeLibrary;
	$this->tag_rules = $this->defaults->default_tag_rules;
	$this->smileys = $this->defaults->default_smileys;
	$this->enable_smileys = true;
	$this->smiley_regex = false;
	$this->smiley_dir = $this->GetDefaultSmileyDir();
	$this->smiley_url = $this->GetDefaultSmileyURL();
	$this->wiki_url = $this->GetDefaultWikiURL();
	$this->local_img_dir = $this->GetDefaultLocalImgDir();
	$this->local_img_url = $this->GetDefaultLocalImgURL();
	$this->rule_html = $this->GetDefaultRuleHTML();
	$this->pre_trim = "";
	$this->post_trim = "";
	$this->root_class = 'block';
	$this->lost_start_tags = Array();
	$this->start_tags = Array();
	$this->tag_marker = '[';
	$this->allow_ampsersand = false;
	$this->current_class = $this->root_class;
	$this->debug = false;
	$this->ignore_newlines = false;
	$this->output_limit = 0;
	$this->plain_mode = false;
	$this->was_limited = false;
	$this->limit_tail = "...";
	$this->limit_precision = 0.15;
	$this->detect_urls = false;
	$this->url_pattern = '<a href="{$url/h}">{$text/h}</a>';
	$this->url_targetable = false;
	$this->url_target = false;
}

// getInstance method
public static function getInstance()
{
    if(!self::$instance)
    {
      self::$instance = new self();
    }

    return self::$instance;
}
/* ADDED */


function SetPreTrim($trim = "a") { $this->pre_trim = $trim; }
function GetPreTrim() { return $this->pre_trim; }
function SetPostTrim($trim = "a") { $this->post_trim = $trim; }
function GetPostTrim() { return $this->post_trim; }
function SetRoot($class = 'block') { $this->root_class = $class; }
function SetRootInline() { $this->root_class = 'inline'; }
function SetRootBlock() { $this->root_class = 'block'; }
function GetRoot() { return $this->root_class; }
function SetDebug($enable = true) { $this->debug = $enable; }
function GetDebug() { return $this->debug; }
function SetAllowAmpersand($enable = true) { $this->allow_ampersand = $enable; }
function GetAllowAmpersand() { return $this->allow_ampersand; }
function SetTagMarker($marker = '[') { $this->tag_marker = $marker; }
function GetTagMarker() { return $this->tag_marker; }
function SetIgnoreNewlines($ignore = true) { $this->ignore_newlines = $ignore; }
function GetIgnoreNewlines() { return $this->ignore_newlines; }
function SetLimit($limit = 0) { $this->output_limit = $limit; }
function GetLimit() { return $this->output_limit; }
function SetLimitTail($tail = "...") { $this->limit_tail = $tail; }
function GetLimitTail() { return $this->limit_tail; }
function SetLimitPrecision($prec = 0.15) { $this->limit_precision = $prec; }
function GetLimitPrecision() { return $this->limit_precision; }
function WasLimited() { return $this->was_limited; }
function SetPlainMode($enable = true) { $this->plain_mode = $enable; }
function GetPlainMode() { return $this->plain_mode; }
function SetDetectURLs($enable = true) { $this->detect_urls = $enable; }
function GetDetectURLs() { return $this->detect_urls; }
function SetURLPattern($pattern) { $this->url_pattern = $pattern; }
function GetURLPattern() { return $this->url_pattern; }
function SetURLTargetable($enable) { $this->url_targetable = $enable; }
function GetURLTargetable() { return $this->url_targetable; }
function SetURLTarget($target) { $this->url_target = $target; }
function GetURLTarget() { return $this->url_target; }
function AddRule($name, $rule) { $this->tag_rules[$name] = $rule; }
function RemoveRule($name) { unset($this->tag_rules[$name]); }
function GetRule($name) { return isset($this->tag_rules[$name])
? $this->tag_rules[$name] : false; }
function ClearRules() { $this->tag_rules = Array(); }
function GetDefaultRule($name) { return isset($this->defaults->default_tag_rules[$name])
? $this->defaults->default_tag_rules[$name] : false; }
function SetDefaultRule($name) { if (isset($this->defaults->default_tag_rules[$name]))
$this->AddRule($name, $this->defaults->default_tag_rules[$name]);
else $this->RemoveRule($name); }
function GetDefaultRules() { return $this->defaults->default_tag_rules; }
function SetDefaultRules() { $this->tag_rules = $this->defaults->default_tag_rules; }
function SetWikiURL($url) { $this->wiki_url = $url; }
function GetWikiURL($url) { return $this->wiki_url; }
function GetDefaultWikiURL() { return '/?page='; }
function SetLocalImgDir($path) { $this->local_img_dir = $path; }
function GetLocalImgDir() { return $this->local_img_dir; }
function GetDefaultLocalImgDir() { return "img"; }
function SetLocalImgURL($path) { $this->local_img_url = $path; }
function GetLocalImgURL() { return $this->local_img_url; }
function GetDefaultLocalImgURL() { return "img"; }
function SetRuleHTML($html) { $this->rule_html = $html; }
function GetRuleHTML() { return $this->rule_html; }
function GetDefaultRuleHTML() { return "\n<hr class=\"bbcode_rule\" />\n"; }
function AddSmiley($code, $image) { $this->smileys[$code] = $image; $this->smiley_regex = false; }
function RemoveSmiley($code) { unset($this->smileys[$code]); $this->smiley_regex = false; }
function GetSmiley($code) { return isset($this->smileys[$code])
? $this->smileys[$code] : false; }
function ClearSmileys() { $this->smileys = Array(); $this->smiley_regex = false; }
function GetDefaultSmiley($code) { return isset($this->defaults->default_smileys[$code])
? $this->defaults->default_smileys[$code] : false; }
function SetDefaultSmiley($code) { $this->smileys[$code] = @$this->defaults->default_smileys[$code];
$this->smiley_regex = false; }
function GetDefaultSmileys() { return $this->defaults->default_smileys; }
function SetDefaultSmileys() { $this->smileys = $this->defaults->default_smileys;
$this->smiley_regex = false; }
function SetSmileyDir($path) { $this->smiley_dir = $path; }
function GetSmileyDir() { return $this->smiley_dir; }
function GetDefaultSmileyDir() { return "smileys"; }
function SetSmileyURL($path) { $this->smiley_url = $path; }
function GetSmileyURL() { return $this->smiley_url; }
function GetDefaultSmileyURL() { return "smileys"; }
function SetEnableSmileys($enable = true) { $this->enable_smileys = $enable; }
function GetEnableSmileys() { return $this->enable_smileys; }
function nl2br($string) {
return preg_replace("/\\x0A|\\x0D|\\x0A\\x0D|\\x0D\\x0A/", "<br />\n", $string);
}
function UnHTMLEncode($string) {
if (function_exists("html_entity_decode"))
return html_entity_decode($string);
$string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
$string = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $string);
$trans_tbl = get_html_translation_table(HTML_ENTITIES);
$trans_tbl = array_flip($trans_tbl);
return strtr($string, $trans_tbl);
}
function Wikify($string) {
return rawurlencode(str_replace(" ", "_",
trim(preg_replace("/[!?;@#\$%\\^&*<>=+`~\\x00-\\x20_-]+/", " ", $string))));
}
function IsValidURL($string, $email_too = true) {
if (preg_match("/^
(?:https?|ftp):\\/\\/
(?:
(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\\.)+
[a-zA-Z0-9]
(?:[a-zA-Z0-9-]*[a-zA-Z0-9])?
|
\\[
(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.){3}
(?:
25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-zA-Z0-9-]*[a-zA-Z0-9]:
(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21-\\x5A\\x53-\\x7F]
|\\\\[\\x01-\\x09\\x0B\\x0C\\x0E-\\x7F])+
)
\\]
)
(?::[0-9]{1,5})?
(?:[\\/\\?\\#][^\\n\\r]*)?
$/Dx", $string)) return true;
if (preg_match("/^[^:]+([\\/\\\\?#][^\\r\\n]*)?$/D", $string))
return true;
if ($email_too)
if (substr($string, 0, 7) == "mailto:")
return $this->IsValidEmail(substr($string, 7));
return false;
}
function IsValidEmail($string) {
$validator = new BBCodeEmailAddressValidator;
return $validator->check_email_address($string);
/*
return preg_match("/^
(?:
[a-z0-9\\!\\#\\\$\\%\\&\\'\\*\\+\\/=\\?\\^_`\\{\\|\\}~-]+
(?:\.[a-z0-9\\!\\#\\\$\\%\\&\\'\\*\\+\\/=\\?\\^_`\\{\\|\\}~-]+)*
|
\"(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]
|\\\\[\\x01-\\x09\\x0B\\x0C\\x0E-\\x7F])*\"
)
@
(?:
(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+
[a-z0-9]
(?:[a-z0-9-]*[a-z0-9])?
|
\\[
(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.){3}
(?:
25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:
(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21-\\x5A\\x53-\\x7F]
|\\\\[\\x01-\\x09\\x0B\\x0C\\x0E-\\x7F])+
)
\\]
)
$/Dx", $string);
*/
}
function HTMLEncode($string) {
if (!$this->allow_ampersand)
return htmlspecialchars($string);
else return str_replace(Array('<', '>', '"'),
Array('&lt;', '&gt;', '&quot;'), $string);
}
function FixupOutput($string) {
if (!$this->detect_urls) {
$output = $this->Internal_ProcessSmileys($string);
}
else {
$chunks = $this->Internal_AutoDetectURLs($string);
$output = Array();
if (count($chunks)) {
$is_a_url = false;
foreach ($chunks as $index => $chunk) {
if (!$is_a_url) {
$chunk = $this->Internal_ProcessSmileys($chunk);
}
$output[] = $chunk;
$is_a_url = !$is_a_url;
}
}
$output = implode("", $output);
}
return $output;
}
function Internal_ProcessSmileys($string) {
if (!$this->enable_smileys || $this->plain_mode) {
$output = $this->HTMLEncode($string);
}
else {
if ($this->smiley_regex === false) {
$this->Internal_RebuildSmileys();
}
$tokens = preg_split($this->smiley_regex, $string, -1, PREG_SPLIT_DELIM_CAPTURE);
if (count($tokens) <= 1) {
$output = $this->HTMLEncode($string);
}
else {
$output = "";
$is_a_smiley = false;
foreach ($tokens as $token) {
if (!$is_a_smiley) {
$output .= $this->HTMLEncode($token);
}
else {
if (isset($this->smiley_info[$token])) {
$info = $this->smiley_info[$token];
}
else {
$info = @getimagesize($this->smiley_dir . '/' . $this->smileys[$token]);
$this->smiley_info[$token] = $info;
}
$alt = htmlspecialchars($token);
$output .= "<img src=\"" . htmlspecialchars($this->smiley_url . '/' . $this->smileys[$token])
. "\" width=\"{$info[0]}\" height=\"{$info[1]}\""
. " alt=\"$alt\" title=\"$alt\" class=\"bbcode_smiley\" />";
}
$is_a_smiley = !$is_a_smiley;
}
}
}
return $output;
}
function Internal_RebuildSmileys() {
$regex = Array("/(?<![\\w])(");
$first = true;
foreach ($this->smileys as $code => $filename) {
if (!$first) $regex[] = "|";
$regex[] = preg_quote("$code", '/');
$first = false;
}
$regex[] = ")(?![\\w])/";
$this->smiley_regex = implode("", $regex);
}
function Internal_AutoDetectURLs($string) {
$output = preg_split("/( (?:
(?:https?|ftp) : \\/*
(?:
(?: (?: [a-zA-Z0-9-]{2,} \\. )+
(?: arpa | com | org | net | edu | gov | mil | int | [a-z]{2}
| aero | biz | coop | info | museum | name | pro
| example | invalid | localhost | test | local | onion | swift ) )
| (?: [0-9]{1,3} \\. [0-9]{1,3} \\. [0-9]{1,3} \\. [0-9]{1,3} )
| (?: [0-9A-Fa-f:]+ : [0-9A-Fa-f]{1,4} )
)
(?: : [0-9]+ )?
(?! [a-zA-Z0-9.:-] )
(?:
\\/
[^&?#\\(\\)\\[\\]\\{\\}<>\\'\\\"\\x00-\\x20\\x7F-\\xFF]*
)?
(?:
[?#]
[^\\(\\)\\[\\]\\{\\}<>\\'\\\"\\x00-\\x20\\x7F-\\xFF]+
)?
) | (?:
(?:
(?: (?: [a-zA-Z0-9-]{2,} \\. )+
(?: arpa | com | org | net | edu | gov | mil | int | [a-z]{2}
| aero | biz | coop | info | museum | name | pro
| example | invalid | localhost | test | local | onion | swift ) )
| (?: [0-9]{1,3} \\. [0-9]{1,3} \\. [0-9]{1,3} \\. [0-9]{1,3} )
)
(?: : [0-9]+ )?
(?! [a-zA-Z0-9.:-] )
(?:
\\/
[^&?#\\(\\)\\[\\]\\{\\}<>\\'\\\"\\x00-\\x20\\x7F-\\xFF]*
)?
(?:
[?#]
[^\\(\\)\\[\\]\\{\\}<>\\'\\\"\\x00-\\x20\\x7F-\\xFF]+
)?
) | (?:
[a-zA-Z0-9._-]{2,} @
(?:
(?: (?: [a-zA-Z0-9-]{2,} \\. )+
(?: arpa | com | org | net | edu | gov | mil | int | [a-z]{2}
| aero | biz | coop | info | museum | name | pro
| example | invalid | localhost | test | local | onion | swift ) )
| (?: [0-9]{1,3} \\. [0-9]{1,3} \\. [0-9]{1,3} \\. [0-9]{1,3} )
)
) )/Dx", $string, -1, PREG_SPLIT_DELIM_CAPTURE);
if (count($output) > 1) {
$is_a_url = false;
foreach ($output as $index => $token) {
if ($is_a_url) {
if (preg_match("/^[a-zA-Z0-9._-]{2,}@/", $token)) {
$url = "mailto:" . $token;
}
else if (preg_match("/^(https?:|ftp:)\\/*([^\\/&?#]+)\\/*(.*)\$/", $token, $matches)) {
$url = $matches[1] . '/' . '/' . $matches[2] . "/" . $matches[3];
}
else {
preg_match("/^([^\\/&?#]+)\\/*(.*)\$/", $token, $matches);
$url = "http:/" . "/" . $matches[1] . "/" . $matches[2];
}
$params = @parse_url($url);
if (!is_array($params)) $params = Array();
$params['url'] = $url;
$params['link'] = $url;
$params['text'] = $token;
$output[$index] = $this->FillTemplate($this->url_pattern, $params);
}
$is_a_url = !$is_a_url;
}
}
return $output;
}
function FillTemplate($template, $insert_array, $default_array = Array()) {
$pieces = preg_split('/(\{\$[a-zA-Z0-9_.:\/-]+\})/', $template,
-1, PREG_SPLIT_DELIM_CAPTURE);
if (count($pieces) <= 1)
return $template;
$result = Array();
$is_an_insert = false;
foreach ($pieces as $piece) {
if (!$is_an_insert) {
$result[] = $piece;
}
else if (!preg_match('/\{\$([a-zA-Z0-9_:-]+)((?:\\.[a-zA-Z0-9_:-]+)*)(?:\/([a-zA-Z0-9_:-]+))?\}/', $piece, $matches)) {
$result[] = $piece;
}
else {
if (isset($insert_array[$matches[1]]))
$value = @$insert_array[$matches[1]];
else $value = @$default_array[$matches[1]];
if (strlen(@$matches[2])) {
foreach (split(".", substr($matches[2], 1)) as $index) {
if (is_array($value))
$value = @$value[$index];
else if (is_object($value)) {
$value = (array)$value;
$value = @$value[$index];
}
else $value = "";
}
}
switch (gettype($value)) {
case 'boolean': $value = $value ? "true" : "false"; break;
case 'integer': $value = (string)$value; break;
case 'double': $value = (string)$value; break;
case 'string': break;
default: $value = ""; break;
}
if (strlen(@$matches[3]))
$flags = array_flip(str_split($matches[3]));
else $flags = Array();
if (!isset($flags['v'])) {
if (isset($flags['w']))
$value = preg_replace("/[\\x00-\\x09\\x0B-\x0C\x0E-\\x20]+/", " ", $value);
if (isset($flags['t'])) $value = trim($value);
if (isset($flags['b'])) $value = basename($value);
if (isset($flags['e'])) $value = $this->HTMLEncode($value);
else if (isset($flags['k'])) $value = $this->Wikify($value);
else if (isset($flags['h'])) $value = htmlspecialchars($value);
else if (isset($flags['u'])) $value = urlencode($value);
if (isset($flags['n'])) $value = $this->nl2br($value);
}
$result[] = $value;
}
$is_an_insert = !$is_an_insert;
}
return implode("", $result);
}
function Internal_CollectText($array, $start = 0) {
ob_start();
for ($start = intval($start), $end = count($array); $start < $end; $start++)
print $array[$start][BBCODE_STACK_TEXT];
$output = ob_get_contents();
ob_end_clean();
return $output;
}
function Internal_CollectTextReverse($array, $start = 0, $end = 0) {
ob_start();
for ($start = intval($start); $start >= $end; $start--)
print $array[$start][BBCODE_STACK_TEXT];
$output = ob_get_contents();
ob_end_clean();
return $output;
}
function Internal_GenerateOutput($pos) {
$output = Array();
while (count($this->stack) > $pos) {
$token = array_pop($this->stack);
if ($token[BBCODE_STACK_TOKEN] != BBCODE_TAG) {
$output[] = $token;
}
else {
$name = @$token[BBCODE_STACK_TAG]['_name'];
$rule = @$this->tag_rules[$name];
$end_tag = @$rule['end_tag'];
if (!isset($rule['end_tag'])) $end_tag = BBCODE_REQUIRED;
else $end_tag = $rule['end_tag'];
array_pop($this->start_tags[$name]);
if ($end_tag == BBCODE_PROHIBIT) {
$output[] = Array(
BBCODE_STACK_TOKEN => BBCODE_TEXT,
BBCODE_STACK_TAG => false,
BBCODE_STACK_TEXT => $token[BBCODE_STACK_TEXT],
BBCODE_STACK_CLASS => $this->current_class,
);
}
else {
if ($end_tag == BBCODE_REQUIRED)
@$this->lost_start_tags[$name] += 1;
$end = $this->Internal_CleanupWSByIteratingPointer(@$rule['before_endtag'], 0, $output);
$this->Internal_CleanupWSByPoppingStack(@$rule['after_tag'], $output);
$tag_body = $this->Internal_CollectTextReverse($output, count($output)-1, $end);
$this->Internal_CleanupWSByPoppingStack(@$rule['before_tag'], $this->stack);
$this->Internal_UpdateParamsForMissingEndTag(@$token[BBCODE_STACK_TAG]);
$tag_output = $this->DoTag(BBCODE_OUTPUT, $name,
@$token[BBCODE_STACK_TAG]['_default'], @$token[BBCODE_STACK_TAG], $tag_body);
$output = Array(Array(
BBCODE_STACK_TOKEN => BBCODE_TEXT,
BBCODE_STACK_TAG => false,
BBCODE_STACK_TEXT => $tag_output,
BBCODE_STACK_CLASS => $this->current_class
));
}
}
}
$this->Internal_ComputeCurrentClass();
return $output;
}
function Internal_RewindToClass($class_list) {
$pos = count($this->stack) - 1;
while ($pos >= 0 && !in_array($this->stack[$pos][BBCODE_STACK_CLASS], $class_list))
$pos--;
if ($pos < 0) {
if (!in_array($this->root_class, $class_list))
return false;
}
$output = $this->Internal_GenerateOutput($pos+1);
while (count($output)) {
$token = array_pop($output);
$token[BBCODE_STACK_CLASS] = $this->current_class;
$this->stack[] = $token;
}
return true;
}
function Internal_FinishTag($tag_name) {
if (strlen($tag_name) <= 0)
return false;
if (isset($this->start_tags[$tag_name])
&& count($this->start_tags[$tag_name]))
$pos = array_pop($this->start_tags[$tag_name]);
else $pos = -1;
if ($pos < 0) return false;
$newpos = $this->Internal_CleanupWSByIteratingPointer(@$this->tag_rules[$tag_name]['after_tag'],
$pos+1, $this->stack);
$delta = $newpos - ($pos+1);
$output = $this->Internal_GenerateOutput($newpos);
$newend = $this->Internal_CleanupWSByIteratingPointer(@$this->tag_rules[$tag_name]['before_endtag'],
0, $output);
$output = $this->Internal_CollectTextReverse($output, count($output) - 1, $newend);
while ($delta-- > 0)
array_pop($this->stack);
$this->Internal_ComputeCurrentClass();
return $output;
}
function Internal_ComputeCurrentClass() {
if (count($this->stack) > 0)
$this->current_class = $this->stack[count($this->stack)-1][BBCODE_STACK_CLASS];
else $this->current_class = $this->root_class;
}
function Internal_DumpStack($array = false, $raw = false) {
if (!$raw) $string = "<span style='color: #00C;'>";
else $string = "";
if ($array === false)
$array = $this->stack;
foreach ($array as $item) {
switch (@$item[BBCODE_STACK_TOKEN]) {
case BBCODE_TEXT:
$string .= "\"" . htmlspecialchars(@$item[BBCODE_STACK_TEXT]) . "\" ";
break;
case BBCODE_WS:
$string .= "WS ";
break;
case BBCODE_NL:
$string .= "NL ";
break;
case BBCODE_TAG:
$string .= "[" . htmlspecialchars(@$item[BBCODE_STACK_TAG]['_name']) . "] ";
break;
default:
$string .= "unknown ";
break;
}
}
if (!$raw) $string .= "</span>";
return $string;
}
function Internal_CleanupWSByPoppingStack($pattern, &$array) {
if (strlen($pattern) <= 0) return;
$oldlen = count($array);
foreach (str_split($pattern) as $char) {
switch ($char) {
case 's':
while (count($array) > 0 && $array[count($array)-1][BBCODE_STACK_TOKEN] == BBCODE_WS)
array_pop($array);
break;
case 'n':
if (count($array) > 0 && $array[count($array)-1][BBCODE_STACK_TOKEN] == BBCODE_NL)
array_pop($array);
break;
case 'a':
while (count($array) > 0
&& (($token = $array[count($array)-1][BBCODE_STACK_TOKEN]) == BBCODE_WS
|| $token == BBCODE_NL))
array_pop($array);
break;
}
}
if (count($array) != $oldlen) {
$this->Internal_ComputeCurrentClass();
}
}
function Internal_CleanupWSByEatingInput($pattern) {
if (strlen($pattern) <= 0) return;
foreach (str_split($pattern) as $char) {
switch ($char) {
case 's':
$token_type = $this->lexer->NextToken();
while ($token_type == BBCODE_WS) {
$token_type = $this->lexer->NextToken();
}
$this->lexer->UngetToken();
break;
case 'n':
$token_type = $this->lexer->NextToken();
if ($token_type != BBCODE_NL)
$this->lexer->UngetToken();
break;
case 'a':
$token_type = $this->lexer->NextToken();
while ($token_type == BBCODE_WS || $token_type == BBCODE_NL) {
$token_type = $this->lexer->NextToken();
}
$this->lexer->UngetToken();
break;
}
}
}
function Internal_CleanupWSByIteratingPointer($pattern, $pos, $array) {
if (strlen($pattern) <= 0) return $pos;
foreach (str_split($pattern) as $char) {
switch ($char) {
case 's':
while ($pos < count($array) && $array[$pos][BBCODE_STACK_TOKEN] == BBCODE_WS)
$pos++;
break;
case 'n':
if ($pos < count($array) && $array[$pos][BBCODE_STACK_TOKEN] == BBCODE_NL)
$pos++;
break;
case 'a':
while ($pos < count($array)
&& (($token = $array[$pos][BBCODE_STACK_TOKEN]) == BBCODE_WS || $token == BBCODE_NL))
$pos++;
break;
}
}
return $pos;
}
function Internal_LimitText($string, $limit) {
$chunks = preg_split("/([\\x00-\\x20]+)/", $string, -1, PREG_SPLIT_DELIM_CAPTURE);
$output = "";
foreach ($chunks as $chunk) {
if (strlen($output) + strlen($chunk) > $limit)
break;
$output .= $chunk;
}
$output = rtrim($output);
return $output;
}
function Internal_DoLimit() {
$this->Internal_CleanupWSByPoppingStack("a", $this->stack);
if (strlen($this->limit_tail) > 0) {
$this->stack[] = Array(
BBCODE_STACK_TOKEN => BBCODE_TEXT,
BBCODE_STACK_TEXT => $this->limit_tail,
BBCODE_STACK_TAG => false,
BBCODE_STACK_CLASS => $this->current_class,
);
}
$this->was_limited = true;
}
function DoTag($action, $tag_name, $default_value, $params, $contents) {
$tag_rule = @$this->tag_rules[$tag_name];
switch ($action) {
case BBCODE_CHECK:
if (isset($tag_rule['allow'])) {
foreach ($tag_rule['allow'] as $param => $pattern) {
if ($param == '_content') $value = $contents;
else if ($param == '_defaultcontent') {
if (strlen($default_value))
$value = $default_value;
else $value = $contents;
}
else {
if (isset($params[$param]))
$value = $params[$param];
else $value = @$tag_rule['default'][$param];
}
if (!preg_match($pattern, $value)) {
return false;
}
}
return true;
}
switch (@$tag_rule['mode']) {
default:
case BBCODE_MODE_SIMPLE:
$result = true;
break;
case BBCODE_MODE_ENHANCED:
$result = true;
break;
case BBCODE_MODE_INTERNAL:
$result = @call_user_func(Array($this, @$tag_rule['method']), BBCODE_CHECK,
$tag_name, $default_value, $params, $contents);
break;
case BBCODE_MODE_LIBRARY:
$result = @call_user_func(Array($this->defaults, @$tag_rule['method']), $this, BBCODE_CHECK,
$tag_name, $default_value, $params, $contents);
break;
case BBCODE_MODE_CALLBACK:
$result = @call_user_func(@$tag_rule['method'], $this, BBCODE_CHECK,
$tag_name, $default_value, $params, $contents);
break;
}
return $result;
case BBCODE_OUTPUT:
if ($this->plain_mode) {
if (!isset($tag_rule['plain_content']))
$plain_content = Array('_content');
else $plain_content = $tag_rule['plain_content'];
$result = $possible_content = "";
foreach ($plain_content as $possible_content) {
if ($possible_content == '_content'
&& strlen($contents) > 0) {
$result = $contents;
break;
}
if (isset($params[$possible_content])
&& strlen($params[$possible_content]) > 0) {
$result = htmlspecialchars($params[$possible_content]);
break;
}
}
$start = @$tag_rule['plain_start'];
$end = @$tag_rule['plain_end'];
if (isset($tag_rule['plain_link'])) {
$link = $possible_content = "";
foreach ($tag_rule['plain_link'] as $possible_content) {
if ($possible_content == '_content'
&& strlen($contents) > 0) {
$link = $this->UnHTMLEncode(strip_tags($contents));
break;
}
if (isset($params[$possible_content])
&& strlen($params[$possible_content]) > 0) {
$link = $params[$possible_content];
break;
}
}
$params = @parse_url($link);
if (!is_array($params)) $params = Array();
$params['link'] = $link;
$params['url'] = $link;
$start = $this->FillTemplate($start, $params);
$end = $this->FillTemplate($end, $params);
}
return $start . $result . $end;
}
switch (@$tag_rule['mode']) {
default:
case BBCODE_MODE_SIMPLE:
$result = @$tag_rule['simple_start'] . $contents . @$tag_rule['simple_end'];
break;
case BBCODE_MODE_ENHANCED:
$result = $this->Internal_DoEnhancedTag($tag_rule, $params, $contents);
break;
case BBCODE_MODE_INTERNAL:
$result = @call_user_func(Array($this, @$tag_rule['method']), BBCODE_OUTPUT,
$tag_name, $default_value, $params, $contents);
break;
case BBCODE_MODE_LIBRARY:
$result = @call_user_func(Array($this->defaults, @$tag_rule['method']), $this, BBCODE_OUTPUT,
$tag_name, $default_value, $params, $contents);
break;
case BBCODE_MODE_CALLBACK:
$result = @call_user_func(@$tag_rule['method'], $this, BBCODE_OUTPUT,
$tag_name, $default_value, $params, $contents);
break;
}
return $result;
default:
return false;
}
}
function Internal_DoEnhancedTag($tag_rule, $params, $contents) {
$params['_content'] = $contents;
$params['_defaultcontent'] = strlen(@$params['_default']) ? $params['_default'] : $contents;
return $this->FillTemplate(@$tag_rule['template'], $params, @$tag_rule['default']);
}
function Internal_UpdateParamsForMissingEndTag(&$params) {
switch ($this->tag_marker) {
case '[': $tail_marker = ']'; break;
case '<': $tail_marker = '>'; break;
case '{': $tail_marker = '}'; break;
case '(': $tail_marker = ')'; break;
default: $tail_marker = $this->tag_marker; break;
}
$params['_endtag'] = $this->tag_marker . '/' . $params['_name'] . $tail_marker;
}
function Internal_ProcessIsolatedTag($tag_name, $tag_params, $tag_rule) {
if (!$this->DoTag(BBCODE_CHECK, $tag_name, @$tag_params['_default'], $tag_params, "")) {
$this->stack[] = Array(
BBCODE_STACK_TOKEN => BBCODE_TEXT,
BBCODE_STACK_TEXT => $this->FixupOutput($this->lexer->text),
BBCODE_STACK_TAG => false,
BBCODE_STACK_CLASS => $this->current_class,
);
return;
}
$this->Internal_CleanupWSByPoppingStack(@$tag_rule['before_tag'], $this->stack);
$output = $this->DoTag(BBCODE_OUTPUT, $tag_name, @$tag_params['_default'], $tag_params, "");
$this->Internal_CleanupWSByEatingInput(@$tag_rule['after_tag']);
$this->stack[] = Array(
BBCODE_STACK_TOKEN => BBCODE_TEXT,
BBCODE_STACK_TEXT => $output,
BBCODE_STACK_TAG => false,
BBCODE_STACK_CLASS => $this->current_class,
);
}
function Internal_ProcessVerbatimTag($tag_name, $tag_params, $tag_rule) {
$state = $this->lexer->SaveState();
$end_tag = $this->lexer->tagmarker . "/" . $tag_name . $this->lexer->end_tagmarker;
$start = count($this->stack);
$this->lexer->verbatim = true;
while (($token_type = $this->lexer->NextToken()) != BBCODE_EOI) {
if ($this->lexer->text == $end_tag) {
$end_tag_params = $this->lexer->tag;
break;
}
if ($this->output_limit > 0
&& $this->text_length + strlen($this->lexer->text) >= $this->output_limit) {
$text = $this->Internal_LimitText($this->lexer->text,
$this->output_limit - $this->text_length);
if (strlen($text) > 0) {
$this->text_length += strlen($text);
$this->stack[] = Array(
BBCODE_STACK_TOKEN => BBCODE_TEXT,
BBCODE_STACK_TEXT => $this->FixupOutput($text),
BBCODE_STACK_TAG => false,
BBCODE_STACK_CLASS => $this->current_class,
);
}
$this->Internal_DoLimit();
break;
}
$this->text_length += strlen($this->lexer->text);
$this->stack[] = Array(
BBCODE_STACK_TOKEN => $token_type,
BBCODE_STACK_TEXT => htmlspecialchars($this->lexer->text),
BBCODE_STACK_TAG => $this->lexer->tag,
BBCODE_STACK_CLASS => $this->current_class,
);
}
$this->lexer->verbatim = false;
if ($token_type == BBCODE_EOI) {
$this->lexer->RestoreState($state);
$this->stack[] = Array(
BBCODE_STACK_TOKEN => BBCODE_TEXT,
BBCODE_STACK_TEXT => $this->FixupOutput($this->lexer->text),
BBCODE_STACK_TAG => false,
BBCODE_STACK_CLASS => $this->current_class,
);
return;
}
$newstart = $this->Internal_CleanupWSByIteratingPointer(@$tag_rule['after_tag'], $start, $this->stack);
$this->Internal_CleanupWSByPoppingStack(@$tag_rule['before_endtag'], $this->stack);
$this->Internal_CleanupWSByEatingInput(@$tag_rule['after_endtag']);
$content = $this->Internal_CollectText($this->stack, $newstart);
array_splice($this->stack, $start);
$this->Internal_ComputeCurrentClass();
$this->Internal_CleanupWSByPoppingStack(@$tag_rule['before_tag'], $this->stack);
$tag_params['_endtag'] = $end_tag_params['_tag'];
$tag_params['_hasend'] = true;
$output = $this->DoTag(BBCODE_OUTPUT, $tag_name,
@$tag_params['_default'], $tag_params, $content);
$this->stack[] = Array(
BBCODE_STACK_TOKEN => BBCODE_TEXT,
BBCODE_STACK_TEXT => $output,
BBCODE_STACK_TAG => false,
BBCODE_STACK_CLASS => $this->current_class,
);
}
function Internal_ParseStartTagToken() {
$tag_params = $this->lexer->tag;
$tag_name = @$tag_params['_name'];
if (!isset($this->tag_rules[$tag_name])) {
$this->stack[] = Array(
BBCODE_STACK_TOKEN => BBCODE_TEXT,
BBCODE_STACK_TEXT => $this->FixupOutput($this->lexer->text),
BBCODE_STACK_TAG => false,
BBCODE_STACK_CLASS => $this->current_class,
);
return;
}
$tag_rule = $this->tag_rules[$tag_name];
$allow_in = is_array($tag_rule['allow_in'])
? $tag_rule['allow_in'] : Array($this->root_class);
if (!in_array($this->current_class, $allow_in)) {
if (!$this->Internal_RewindToClass($allow_in)) {
$this->stack[] = Array(
BBCODE_STACK_TOKEN => BBCODE_TEXT,
BBCODE_STACK_TEXT => $this->FixupOutput($this->lexer->text),
BBCODE_STACK_TAG => false,
BBCODE_STACK_CLASS => $this->current_class,
);
return;
}
}
$end_tag = isset($tag_rule['end_tag']) ? $tag_rule['end_tag'] : BBCODE_REQUIRED;
if ($end_tag == BBCODE_PROHIBIT) {
$this->Internal_ProcessIsolatedTag($tag_name, $tag_params, $tag_rule);
return;
}
if (!$this->DoTag(BBCODE_CHECK, $tag_name, @$tag_params['_default'], $tag_params, "")) {
$this->stack[] = Array(
BBCODE_STACK_TOKEN => BBCODE_TEXT,
BBCODE_STACK_TEXT => $this->FixupOutput($this->lexer->text),
BBCODE_STACK_TAG => false,
BBCODE_STACK_CLASS => $this->current_class,
);
return;
}
if (@$tag_rule['content'] == BBCODE_VERBATIM) {
$this->Internal_ProcessVerbatimTag($tag_name, $tag_params, $tag_rule);
return;
}
if (isset($tag_rule['class']))
$newclass = $tag_rule['class'];
else $newclass = $this->root_class;
$this->stack[] = Array(
BBCODE_STACK_TOKEN => $this->lexer->token,
BBCODE_STACK_TEXT => $this->FixupOutput($this->lexer->text),
BBCODE_STACK_TAG => $this->lexer->tag,
BBCODE_STACK_CLASS => ($this->current_class = $newclass),
);
if (!isset($this->start_tags[$tag_name]))
$this->start_tags[$tag_name] = Array(count($this->stack)-1);
else $this->start_tags[$tag_name][] = count($this->stack)-1;
}
function Internal_ParseEndTagToken() {
$tag_params = $this->lexer->tag;
$tag_name = @$tag_params['_name'];
$contents = $this->Internal_FinishTag($tag_name);
if ($contents === false) {
if (@$this->lost_start_tags[$tag_name] > 0) {
$this->lost_start_tags[$tag_name]--;
}
else {
$this->stack[] = Array(
BBCODE_STACK_TOKEN => BBCODE_TEXT,
BBCODE_STACK_TEXT => $this->FixupOutput($this->lexer->text),
BBCODE_STACK_TAG => false,
BBCODE_STACK_CLASS => $this->current_class,
);
}
return;
}
$start_tag_node = array_pop($this->stack);
$start_tag_params = $start_tag_node[BBCODE_STACK_TAG];
$this->Internal_ComputeCurrentClass();
$this->Internal_CleanupWSByPoppingStack(@$this->tag_rules[$tag_name]['before_tag'], $this->stack);
$start_tag_params['_endtag'] = $tag_params['_tag'];
$start_tag_params['_hasend'] = true;
$output = $this->DoTag(BBCODE_OUTPUT, $tag_name, @$start_tag_params['_default'],
$start_tag_params, $contents);
$this->Internal_CleanupWSByEatingInput(@$this->tag_rules[$tag_name]['after_endtag']);
$this->stack[] = Array(
BBCODE_STACK_TOKEN => BBCODE_TEXT,
BBCODE_STACK_TEXT => $output,
BBCODE_STACK_TAG => false,
BBCODE_STACK_CLASS => $this->current_class,
);
}
function Parse($string) {
$this->lexer = new BBCodeLexer($string, $this->tag_marker);
$this->lexer->debug = $this->debug;
$old_output_limit = $this->output_limit;
if ($this->output_limit > 0) {
if (strlen($string) < $this->output_limit) {
$this->output_limit = 0;
}
else if ($this->limit_precision > 0) {
$guess_length = $this->lexer->GuessTextLength();
if ($guess_length < $this->output_limit * ($this->limit_precision + 1.0)) {
$this->output_limit = 0;
}
else {
}
}
}
$this->stack = Array();
$this->start_tags = Array();
$this->lost_start_tags = Array();
$this->text_length = 0;
$this->was_limited = false;
if (strlen($this->pre_trim) > 0)
$this->Internal_CleanupWSByEatingInput($this->pre_trim);
$newline = $this->plain_mode ? "\n" : "<br />\n";
while (true) {
if (($token_type = $this->lexer->NextToken()) == BBCODE_EOI) {
break;
}
switch ($token_type) {
case BBCODE_TEXT:
if ($this->output_limit > 0
&& $this->text_length + strlen($this->lexer->text) >= $this->output_limit) {
$text = $this->Internal_LimitText($this->lexer->text,
$this->output_limit - $this->text_length);
if (strlen($text) > 0) {
$this->text_length += strlen($text);
$this->stack[] = Array(
BBCODE_STACK_TOKEN => BBCODE_TEXT,
BBCODE_STACK_TEXT => $this->FixupOutput($text),
BBCODE_STACK_TAG => false,
BBCODE_STACK_CLASS => $this->current_class,
);
}
$this->Internal_DoLimit();
break 2;
}
$this->text_length += strlen($this->lexer->text);
$this->stack[] = Array(
BBCODE_STACK_TOKEN => BBCODE_TEXT,
BBCODE_STACK_TEXT => $this->FixupOutput($this->lexer->text),
BBCODE_STACK_TAG => false,
BBCODE_STACK_CLASS => $this->current_class,
);
break;
case BBCODE_WS:
if ($this->output_limit > 0
&& $this->text_length + strlen($this->lexer->text) >= $this->output_limit) {
$this->Internal_DoLimit();
break 2;
}
$this->text_length += strlen($this->lexer->text);
$this->stack[] = Array(
BBCODE_STACK_TOKEN => BBCODE_WS,
BBCODE_STACK_TEXT => $this->lexer->text,
BBCODE_STACK_TAG => false,
BBCODE_STACK_CLASS => $this->current_class,
);
break;
case BBCODE_NL:
if ($this->ignore_newlines) {
if ($this->output_limit > 0
&& $this->text_length + 1 >= $this->output_limit) {
$this->Internal_DoLimit();
break 2;
}
$this->text_length += 1;
$this->stack[] = Array(
BBCODE_STACK_TOKEN => BBCODE_WS,
BBCODE_STACK_TEXT => "\n",
BBCODE_STACK_TAG => false,
BBCODE_STACK_CLASS => $this->current_class,
);
}
else {
$this->Internal_CleanupWSByPoppingStack("s", $this->stack);
if ($this->output_limit > 0
&& $this->text_length + 1 >= $this->output_limit) {
$this->Internal_DoLimit();
break 2;
}
$this->text_length += 1;
$this->stack[] = Array(
BBCODE_STACK_TOKEN => BBCODE_NL,
BBCODE_STACK_TEXT => $newline,
BBCODE_STACK_TAG => false,
BBCODE_STACK_CLASS => $this->current_class,
);
$this->Internal_CleanupWSByEatingInput("s");
}
break;
case BBCODE_TAG:
$this->Internal_ParseStartTagToken();
break;
case BBCODE_ENDTAG:
$this->Internal_ParseEndTagToken();
break;
default:
break;
}
}
if (strlen($this->post_trim) > 0)
$this->Internal_CleanupWSByPoppingStack($this->post_trim, $this->stack);
$result = $this->Internal_GenerateOutput(0);
$result = $this->Internal_CollectTextReverse($result, count($result) - 1);
$this->output_limit = $old_output_limit;
if ($this->plain_mode) {
$result = preg_replace("/[\\x00-\\x09\\x0B-\\x20]+/", " ", $result);
$result = preg_replace("/(?:[\\x20]*\\n){2,}[\\x20]*/", "\n\n", $result);
$result = trim($result);
}
return $result;
}
}

