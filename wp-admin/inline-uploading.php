<?php

require_once('admin.php');

header('Content-Type: text/html; charset=' . get_option('blog_charset'));

if (!current_user_can('upload_files'))
	die(__('You do not have permission to upload files.'));

$wpvarstoreset = array('action', 'post', 'all', 'last', 'link', 'sort', 'start', 'imgtitle', 'descr', 'attachment');

for ($i=0; $i<count($wpvarstoreset); $i += 1) {
	$wpvar = $wpvarstoreset[$i];
	if (!isset($$wpvar)) {
		if (empty($_POST["$wpvar"])) {
			if (empty($_GET["$wpvar"])) {
				$$wpvar = '';
			} else {
			$$wpvar = $_GET["$wpvar"];
			}
		} else {
			$$wpvar = $_POST["$wpvar"];
		}
	}
}

$post = (int) $post;
$images_width = 1;

switch($action) {
case 'links':
// Do not pass GO.
break;

case 'delete':

check_admin_referer('inlineuploading');

if ( !current_user_can('edit_post', (int) $attachment) )
	die(__('You are not allowed to delete this attachment.').' <a href="'.basename(__FILE__)."?post=$post&amp;all=$all&amp;action=upload\">".__('Go back').'</a>');

wp_delete_attachment($attachment);

wp_redirect(basename(__FILE__) ."?post=$post&all=$all&action=view&start=$start");
die;

case 'save':

check_admin_referer('inlineuploading');

$overrides = array('action'=>'save');

$file = wp_handle_upload($_FILES['image'], $overrides);

if ( isset($file['error']) )
	die($file['error'] . '<br /><a href="' . basename(__FILE__) . '?action=upload&post=' . $post . '">'.__('Back to Image Uploading').'</a>');

$url = $file['url'];
$type = $file['type'];
$file = $file['file'];
$filename = basename($file);

// Construct the attachment array
$attachment = array(
	'post_title' => $imgtitle ? $imgtitle : $filename,
	'post_content' => $descr,
	'post_status' => 'attachment',
	'post_parent' => $post,
	'post_mime_type' => $type,
	'guid' => $url
	);

// Save the data
$id = wp_insert_attachment($attachment, $file, $post);

if ( preg_match('!^image/!', $attachment['post_mime_type']) ) {
	// Generate the attachment's postmeta.
	$imagesize = getimagesize($file);
	$imagedata['width'] = $imagesize['0'];
	$imagedata['height'] = $imagesize['1'];
	list($uwidth, $uheight) = get_udims($imagedata['width'], $imagedata['height']);
	$imagedata['hwstring_small'] = "height='$uheight' width='$uwidth'";
	$imagedata['file'] = $file;

	add_post_meta($id, '_wp_attachment_metadata', $imagedata);

	if ( $imagedata['width'] * $imagedata['height'] < 3 * 1024 * 1024 ) {
		if ( $imagedata['width'] > 128 && $imagedata['width'] >= $imagedata['height'] * 4 / 3 )
			$thumb = wp_create_thumbnail($file, 128);
		elseif ( $imagedata['height'] > 96 )
			$thumb = wp_create_thumbnail($file, 96);

		if ( @file_exists($thumb) ) {
			$newdata = $imagedata;
			$newdata['thumb'] = basename($thumb);
			update_post_meta($id, '_wp_attachment_metadata', $newdata, $imagedata);
		} else {
			$error = $thumb;
		}
	}
} else {
	add_post_meta($id, '_wp_attachment_metadata', array());
}

wp_redirect(basename(__FILE__) . "?post=$post&all=$all&action=view&start=0");
die();

case 'upload':

$current_1 = ' class="current"';
$back = $next = false;
break;

case 'view':

// How many images do we show? How many do we query?
$num = 5;
$double = $num * 2;

if ( $post && (empty($all) || $all == 'false') ) {
	$and_post = "AND post_parent = '$post'";
	$current_2 = ' class="current"';
} else {
	$current_3 = ' class="current"';
}

if (! current_user_can('edit_others_posts') )
	$and_user = "AND post_author = " . $user_ID;

if ( $last )
	$start = $wpdb->get_var("SELECT count(ID) FROM $wpdb->posts WHERE post_status = 'attachment' $and_user $and_post") - $num;
else
	$start = (int) $start;

if ( $start < 0 )
	$start = 0;

if ( '' == $sort )
	$sort = "post_date_gmt DESC";

$attachments = $wpdb->get_results("SELECT ID, post_date, post_title, post_mime_type, guid FROM $wpdb->posts WHERE post_status = 'attachment' $and_type $and_post $and_user ORDER BY $sort LIMIT $start, $double", ARRAY_A);

if ( count($attachments) == 0 ) {
	wp_redirect( basename(__FILE__) ."?post=$post&action=upload" );
	die;
} elseif ( count($attachments) > $num ) {
	$next = $start + count($attachments) - $num;
} else {
	$next = false;
}

if ( $start > 0 ) {
	$back = $start - $num;
	if ( $back < 1 )
		$back = '0';
} else {
	$back = false;
}

$uwidth_sum = 0;
$html = '';
$popups = '';
$style = '';
$script = '';
if ( count($attachments) > 0 ) {
	$attachments = array_slice( $attachments, 0, $num );
	$__delete = __('Delete');
	$__not_linked = __('Not Linked');
	$__linked_to_page = __('Linked to Page');
	$__linked_to_image = __('Linked to Image');
	$__linked_to_file = __('Linked to File');
	$__using_thumbnail = __('Using Thumbnail');
	$__using_original = __('Using Original');
	$__using_title = __('Using Title');
	$__using_filename = __('Using Filename');
	$__using_icon = __('Using Icon');
	$__no_thumbnail = '<del>'.__('No Thumbnail').'</del>';
	$__send_to_editor = __('Send to editor');
	$__close = __('Close Options');
	$__confirmdelete = __('Delete this file from the server?');
	$__nothumb = __('There is no thumbnail associated with this photo.');
	$script .= "notlinked = '$__not_linked';
linkedtoimage = '$__linked_to_image';
linkedtopage = '$__linked_to_page';
linkedtofile = '$__linked_to_file';
usingthumbnail = '$__using_thumbnail';
usingoriginal = '$__using_original';
usingtitle = '$__using_title';
usingfilename = '$__using_filename';
usingicon = '$__using_icon';
var aa = new Array();
var ab = new Array();
var imga = new Array();
var imgb = new Array();
var srca = new Array();
var srcb = new Array();
var title = new Array();
var filename = new Array();
var icon = new Array();
";
	foreach ( $attachments as $key => $attachment ) {
		$ID = $attachment['ID'];
		$href = get_attachment_link($ID);
		$meta = get_post_meta($ID, '_wp_attachment_metadata', true);
		if (!is_array($meta)) {
			$meta = get_post_meta($ID, 'imagedata', true); // Try 1.6 Alpha meta key
			if (!is_array($meta)) {
				$meta = array();
			}
			add_post_meta($ID, '_wp_attachment_metadata', $meta);
		}
		$attachment = array_merge($attachment, $meta);
		$noscript = "<noscript>
		<div class='caption'><a href=\"".basename(__FILE__)."?action=links&amp;attachment={$ID}&amp;post={$post}&amp;all={$all}&amp;start={$start}\">Choose Links</a></div>
		</noscript>
";
		$send_delete_cancel = "<a onclick=\"sendToEditor({$ID});return false;\" href=\"javascript:void()\">$__send_to_editor</a>
<a onclick=\"return confirm('$__confirmdelete')\" href=\"" . wp_nonce_url( basename(__FILE__) . "?action=delete&amp;attachment={$ID}&amp;all=$all&amp;start=$start&amp;post=$post", inlineuploading) . "\">$__delete</a>
		<a onclick=\"popup.style.display='none';return false;\" href=\"javascript:void()\">$__close</a>
";
		$uwidth_sum += 128;
		if ( preg_match('!^image/!', $attachment['post_mime_type'] ) ) {
			$image = & $attachment;
			if ( ($image['width'] > 128 || $image['height'] > 96) && !empty($image['thumb']) && file_exists(dirname($image['file']).'/'.$image['thumb']) ) {
				$src = str_replace(basename($image['guid']), $image['thumb'], $image['guid']);
				$script .= "srca[{$ID}] = '$src';
srcb[{$ID}] = '{$image['guid']}';
";
				$thumb = 'true';
				$thumbtext = $__using_thumbnail;
			} else {
				$src = $image['guid'];
				$thumb = 'false';
				$thumbtext = $__no_thumbnail;
			}
			list($image['uwidth'], $image['uheight']) = get_udims($image['width'], $image['height']);
			$height_width = 'height="'.$image['uheight'].'" width="'.$image['uwidth'].'"';
			$xpadding = (128 - $image['uwidth']) / 2;
			$ypadding = (96 - $image['uheight']) / 2;
			$style .= "#target{$ID} img { padding: {$ypadding}px {$xpadding}px; }\n";
			$title = wp_specialchars($image['post_title'], ENT_QUOTES);
			$script .= "aa[{$ID}] = '<a id=\"p{$ID}\" rel=\"attachment\" class=\"imagelink\" href=\"$href\" onclick=\"doPopup({$ID});return false;\" title=\"{$title}\">';
ab[{$ID}] = '<a class=\"imagelink\" href=\"{$image['guid']}\" onclick=\"doPopup({$ID});return false;\" title=\"{$title}\">';
imga[{$ID}] = '<img id=\"image{$ID}\" src=\"$src\" alt=\"{$title}\" $height_width />';
imgb[{$ID}] = '<img id=\"image{$ID}\" src=\"{$image['guid']}\" alt=\"{$title}\" $height_width />';
";
			$html .= "<div id='target{$ID}' class='attwrap left'>
	<div id='div{$ID}' class='imagewrap' onclick=\"doPopup({$ID});\">
		<img id=\"image{$ID}\" src=\"$src\" alt=\"{$title}\" $height_width />
	</div>
	{$noscript}
</div>
";
			$popups .= "<div id='popup{$ID}' class='popup'>
	<a id=\"I{$ID}\" onclick=\"if($thumb)toggleImage({$ID});else alert('$__nothumb');return false;\" href=\"javascript:void()\">$thumbtext</a>
	<a id=\"L{$ID}\" onclick=\"toggleLink({$ID});return false;\" href=\"javascript:void()\">$__not_linked</a>
	{$send_delete_cancel}
</div>
";
		} else {
			$title = wp_specialchars($attachment['post_title'], ENT_QUOTES);
			$filename = basename($attachment['guid']);
			$icon = get_attachment_icon($ID);
			$toggle_icon = "<a id=\"I{$ID}\" onclick=\"toggleOtherIcon({$ID});return false;\" href=\"javascript:void()\">$__using_title</a>";
			$script .= "aa[{$ID}] = '<a id=\"p{$ID}\" rel=\"attachment\" href=\"$href\" onclick=\"doPopup({$ID});return false;\" title=\"{$title}\">';
ab[{$ID}] = '<a id=\"p{$ID}\" href=\"{$filename}\" onclick=\"doPopup({$ID});return false;\" title=\"{$title}\">';
title[{$ID}] = '{$title}';
filename[{$ID}] = '{$filename}';
icon[{$ID}] = '{$icon}';
";
			$html .= "<div id='target{$ID}' class='attwrap left'>
	<div id='div{$ID}' class='otherwrap usingtext' onmousedown=\"selectLink({$ID})\" onclick=\"doPopup({$ID});return false;\">
		<a id=\"p{$ID}\" href=\"{$attachment['guid']}\" onmousedown=\"selectLink({$ID});\" onclick=\"return false;\">{$title}</a>
	</div>
	{$noscript}
</div>
";
			$popups .= "<div id='popup{$ID}' class='popup'>
	<div class='filetype'>".__('File Type:').' '.str_replace('/',"/\n",$attachment['post_mime_type'])."</div>
	<a id=\"L{$ID}\" onclick=\"toggleOtherLink({$ID});return false;\" href=\"javascript:void()\">$__linked_to_file</a>
	{$toggle_icon}
	{$send_delete_cancel}
</div>
";
		}
	}
}

$images_width = $uwidth_sum + ( count($images) * 6 ) + 35;

break;

default:
die(__('This script was not meant to be called directly.'));
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_settings('blog_charset'); ?>" />
<title></title>
<meta http-equiv="imagetoolbar" content="no" />
<script type="text/javascript">
// <![CDATA[
/* Define any variables we'll need, such as alternate URLs. */
<?php echo $script; ?>
function htmldecode(st) {
	o = document.getElementById('htmldecode');
	if (! o) {
		o = document.createElement("A");
		o.id = "htmldecode"
	}
	o.innerHTML = st;
	r = o.innerHTML;
	return r;
}
function cancelUpload() {
	o = document.getElementById('uploadForm');
	o.method = 'GET';
	o.action.value = 'view';
	o.submit();
}
function doPopup(i) {
	if ( popup )
	popup.style.display = 'none';
	target = document.getElementById('target'+i);
	popup = document.getElementById('popup'+i);
	popup.style.left = (target.offsetLeft) + 'px';
	popup.style.top = (target.offsetTop) + 'px';
	popup.style.display = 'block';
}
popup = false;
function selectLink(n) {
	o=document.getElementById('div'+n);
	if ( typeof document.body.createTextRange == 'undefined' || typeof win.tinyMCE == 'undefined' || win.tinyMCE.configs.length < 1 )
		return;
	r = document.body.createTextRange();
	if ( typeof r != 'undefined' ) {
		r.moveToElementText(o);
		r.select();
	}
}
function toggleLink(n) {
	ol=document.getElementById('L'+n);
	if ( ol.innerHTML == htmldecode(notlinked) ) {
		ol.innerHTML = linkedtoimage;
	} else if ( ol.innerHTML == htmldecode(linkedtoimage) ) {
		ol.innerHTML = linkedtopage;
	} else {
		ol.innerHTML = notlinked;
	}
	updateImage(n);
}
function toggleOtherLink(n) {
	ol=document.getElementById('L'+n);
	if ( ol.innerHTML == htmldecode(linkedtofile) ) {
		ol.innerHTML = linkedtopage;
	} else {
		ol.innerHTML = linkedtofile;
	}
	updateOtherIcon(n);
}
function toggleImage(n) {
	oi = document.getElementById('I'+n);
	if ( oi.innerHTML == htmldecode(usingthumbnail) ) {
		oi.innerHTML = usingoriginal;
	} else {
		oi.innerHTML = usingthumbnail;
	}
	updateImage(n);
}
function toggleOtherIcon(n) {
	od = document.getElementById('div'+n);
	oi = document.getElementById('I'+n);
	if ( oi.innerHTML == htmldecode(usingtitle) ) {
		oi.innerHTML = usingfilename;
		od.className = 'otherwrap usingtext';
	} else if ( oi.innerHTML == htmldecode(usingfilename) && icon[n] != '' ) {
		oi.innerHTML = usingicon;
		od.className = 'otherwrap usingicon';
	} else {
		oi.innerHTML = usingtitle;
		od.className = 'otherwrap usingtext';
	}
	updateOtherIcon(n);
}
function updateImage(n) {
	od=document.getElementById('div'+n);
	ol=document.getElementById('L'+n);
	oi=document.getElementById('I'+n);
	if ( oi.innerHTML == htmldecode(usingthumbnail) ) {
		img = imga[n];
	} else {
		img = imgb[n];
	}
	if ( ol.innerHTML == htmldecode(linkedtoimage) ) {
		od.innerHTML = ab[n]+img+'</a>';
	} else if ( ol.innerHTML == htmldecode(linkedtopage) ) {
		od.innerHTML = aa[n]+img+'</a>';
	} else {
		od.innerHTML = img;
	}
}
function updateOtherIcon(n) {
	od=document.getElementById('div'+n);
	ol=document.getElementById('L'+n);
	oi=document.getElementById('I'+n);
	if ( oi.innerHTML == htmldecode(usingfilename) ) {
		txt = filename[n];
	} else if ( oi.innerHTML == htmldecode(usingicon) ) {
		txt = icon[n];
	} else {
		txt = title[n];
	}
	if ( ol.innerHTML == htmldecode(linkedtofile) ) {
		od.innerHTML = ab[n]+txt+'</a>';
	} else if ( ol.innerHTML == htmldecode(linkedtopage) ) {
		od.innerHTML = aa[n]+txt+'</a>';
	} else {
		od.innerHTML = txt;
	}
}

var win = window.opener ? window.opener : window.dialogArguments;
if (!win) win = top;
tinyMCE = win.tinyMCE;
richedit = ( typeof tinyMCE == 'object' && tinyMCE.configs.length > 0 );
function sendToEditor(n) {
	o = document.getElementById('div'+n);
	h = o.innerHTML.replace(new RegExp('^\\s*(.*?)\\s*$', ''), '$1'); // Trim
	h = h.replace(new RegExp(' (class|title|width|height|id|onclick|onmousedown)=([^\'"][^ ]*)( |/|>)', 'g'), ' $1="$2"$3'); // Enclose attribs in quotes
	h = h.replace(new RegExp(' (width|height)=".*?"', 'g'), ''); // Drop size constraints
	h = h.replace(new RegExp(' on(click|mousedown)="[^"]*"', 'g'), ''); // Drop menu events
	h = h.replace(new RegExp('<(/?)A', 'g'), '<$1a'); // Lowercase tagnames
	h = h.replace(new RegExp('<IMG', 'g'), '<img'); // Lowercase again
	h = h.replace(new RegExp('(<img .+?")>', 'g'), '$1 />'); // XHTML
	if ( richedit )
		win.tinyMCE.execCommand('mceInsertContent', false, h);
	else
		win.edInsertContent(win.edCanvas, h);
}
// ]]>
</script>
<style type="text/css">
<?php if ( $action == 'links' ) : ?>
* html { overflow-x: hidden; }
<?php else : ?>
* html { overflow-y: hidden; }
<?php endif; ?>
body {
	font: 13px "Lucida Grande", "Lucida Sans Unicode", Tahoma, Verdana;
	border: none;
	margin: 0px;
	height: 150px;
	background: #dfe8f1;
}
form {
	margin: 3px 2px 0px 6px;
}
#wrap {
	clear: both;
	padding: 0px;
	width: 100%;
}
#images {
	position: absolute;
	clear: both;
	margin: 0px;
	padding: 15px 15px;
	width: <?php echo $images_width; ?>px;
}
#images img {
	background-color: rgb(209, 226, 239);
}
<?php echo $style; ?>
.attwrap, .attwrap * {
	margin: 0px;
	padding: 0px;
	border: 0px;
}
.imagewrap {
	margin-right: 5px;
	overflow: hidden;
	width: 128px;
}
.otherwrap {
	margin-right: 5px;
	overflow: hidden;
	background-color: #f9fcfe;
}
.otherwrap a {
	display: block;
}
.otherwrap a, .otherwrap a:hover, .otherwrap a:active, .otherwrap a:visited {
	color: blue;
}
.usingicon {
	padding: 0px;
	height: 96px;
	text-align: center;
	width: 128px;
}
.usingtext {
	padding: 3px;
	height: 90px;
	text-align: left;
	width: 122px;
}
.filetype {
	font-size: 80%;
	border-bottom: 3px double #89a
}
.imagewrap, .imagewrap img, .imagewrap a, .imagewrap a img, .imagewrap a:hover img, .imagewrap a:visited img, .imagewrap a:active img {
	text-decoration: none;
}
#upload-menu {
	background: #fff;
	margin: 0px;
	padding: 0;
	list-style: none;
	height: 2em;
	border-bottom: 1px solid #448abd;
	width: 100%;
}
#upload-menu li {
	float: left;
	margin: 0 0 0 .75em;
}
#upload-menu a {
	display: block;
	padding: 5px;
	text-decoration: none;
	color: #000;
	border-top: 3px solid #fff;
}
#upload-menu .current a {
	background: #dfe8f1;
	border-right: 2px solid #448abd;
}
#upload-menu a:hover {
	background: #dfe8f1;
	color: #000;
}
.tip {
	color: rgb(68, 138, 189);
	padding: 2px 1em;
}
.inactive {
	color: #fff;
	padding: 1px 3px;
}
.left {
	float: left;
}
.right {
	float: right;
}
.center {
	text-align: center;
}
#upload-menu li.spacer {
	margin-left: 40px;
}
#title, #descr {
	width: 99%;
	margin-top: 1px;
}
th {
	width: 4.5em;
}
#descr {
	height: 36px;
}
#buttons {
	margin-top: 2px;
	text-align: right;
}
.popup {
	margin: 4px 4px;
	padding: 1px;
	position: absolute;
	width: 114px;
	display: none;
	background-color: rgb(240, 240, 238);
	border-top: 2px solid #fff;
	border-right: 2px solid #ddd;
	border-bottom: 2px solid #ddd;
	border-left: 2px solid #fff;
	text-align: center;
}
.imagewrap .popup {
	opacity: .90;
	filter:alpha(opacity=90);
}
.otherwrap .popup {
	padding-top: 20px;
}
.popup a, .popup a:visited, .popup a:active {
	background-color: transparent;
	display: block;
	width: 100%;
	text-decoration: none;
	color: #246;
}
.popup a:hover {
	background-color: #fff;
	color: #000;
}
.caption {
	text-align: center;
}
#submit {
	margin: 1px;
	width: 99%;
}
#submit input, #submit input:focus {
	background: url( images/fade-butt.png );
	border: 3px double #999;
	border-left-color: #ccc;
	border-top-color: #ccc;
	color: #333;
	padding: 0.25em;
}
#submit input:active {
	background: #f4f4f4;
	border: 3px double #ccc;
	border-left-color: #999;
	border-top-color: #999;
}
.zerosize {
	width: 0px;
	height: 0px;
	overflow: hidden;
	position: absolute;
}
#links {
	margin: 3px 8px;
	line-height: 2em;
}
#links textarea {
	width: 95%;
	height: 4.5em;
}
</style>
</head>
<body>
<ul id="upload-menu">
<li<?php echo $current_1; ?>><a href="<?php echo basename(__FILE__) . "?action=upload&amp;post=$post&amp;all=$all&amp;start=$start"; ?>"><?php _e('Upload'); ?></a></li>
<?php if ( $attachments = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_parent = '$post'") ) { ?>
<li<?php echo $current_2; ?>><a href="<?php echo basename(__FILE__) . "?action=view&amp;post=$post&amp;all=false"; ?>"><?php _e('Browse'); ?></a></li>
<?php } ?>
<?php if ($wpdb->get_var("SELECT count(ID) FROM $wpdb->posts WHERE post_status = 'attachment'")) { ?>
<li<?php echo $current_3; ?>><a href="<?php echo basename(__FILE__) . "?action=view&amp;post=$post&amp;all=true"; ?>"><?php _e('Browse All'); ?></a></li>
<?php } ?>
<li> </li>
<?php if ( $action == 'view' ) { ?>
<?php if ( false !== $back ) : ?>
<li class="spacer"><a href="<?php echo basename(__FILE__) . "?action=$action&amp;post=$post&amp;all=$all&amp;start=0"; ?>" title="<?php _e('First'); ?>">|&laquo;</a></li>
<li><a href="<?php echo basename(__FILE__) . "?action=$action&amp;post=$post&amp;all=$all&amp;start=$back"; ?>">&laquo; <?php _e('Back'); ?></a></li>
<?php else : ?>
<li class="inactive spacer">|&laquo;</li>
<li class="inactive">&laquo; <?php _e('Back'); ?></li>
<?php endif; ?>
<?php if ( false !== $next ) : ?>
<li><a href="<?php echo basename(__FILE__) . "?action=$action&amp;post=$post&amp;all=$all&amp;start=$next"; ?>"><?php _e('Next &raquo;'); ?></a></li>
<li><a href="<?php echo basename(__FILE__) . "?action=$action&amp;post=$post&amp;all=$all&amp;last=true"; ?>" title="<?php _e('Last'); ?>">&raquo;|</a></li>
<?php else : ?>
<li class="inactive"><?php _e('Next'); ?> &raquo;</li>
<li class="inactive">&raquo;|</li>
<?php endif; ?>
<?php } // endif not upload?>
</ul>
<?php if ( $action == 'view' ) : ?>
<div id="wrap">
<!--<div class="tip"><?php _e('You can drag and drop these items into your post. Click on one for more options.'); ?></div>-->
<div id="images">
<?php echo $html; ?>
<?php echo $popups; ?>
</div>
</div>
<?php elseif ( $action == 'upload' ) : ?>
<div class="tip"></div>
<form enctype="multipart/form-data" id="uploadForm" method="post" action="<?php echo basename(__FILE__); ?>">
<table style="width:99%;">
<tr>
<th scope="row" align="right"><label for="upload"><?php _e('File:'); ?></label></th>
<td><input type="file" id="upload" name="image" /></td>
</tr>
<tr>
<th scope="row" align="right"><label for="title"><?php _e('Title:'); ?></label></th>
<td><input type="text" id="title" name="imgtitle" /></td>
</tr>
<tr>
<th scope="row" align="right"><label for="descr"><?php _e('Description:'); ?></label></th>
<td><input type="textarea" name="descr" id="descr" value="" /></td>
</tr>
<tr id="buttons">
<th></th>
<td>
<input type="hidden" name="action" value="save" />
<input type="hidden" name="post" value="<?php echo $post; ?>" />
<input type="hidden" name="all" value="<?php echo $all; ?>" />
<input type="hidden" name="start" value="<?php echo $start; ?>" />
<?php wp_nonce_field( 'inlineuploading' ); ?>
<div id="submit">
<input type="submit" value="<?php _e('Upload'); ?>" />
<?php if ( !empty($all) ) : ?>
<input type="button" value="<?php _e('Cancel'); ?>" onclick="cancelUpload()" />
<?php endif; ?>
</div>
</td>
</tr>
</table>
</form>
<?php elseif ( $action == 'links' ) : ?>
<div id="links">
<?php the_attachment_links($attachment); ?>
</div>
<?php endif; ?>
</body>
</html>
