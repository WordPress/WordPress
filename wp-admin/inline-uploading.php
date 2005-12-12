<?php

require_once('admin.php');

if (!current_user_can('edit_posts'))
	die(__('You do not have permission to edit posts.'));

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

function get_udims($width, $height) {
	if ( $height <= 96 && $width <= 128 )
		return array($width, $height);
	elseif ( $width / $height > 4 / 3 )
		return array(128, (int) ($height / $width * 128));
	else
		return array((int) ($width / $height * 96), 96);
}

switch($action) {
case 'delete':

if ( !current_user_can('edit_post', (int) $attachment) )	
die(__('You are not allowed to delete this attachment.').' <a href="'.basename(__FILE__)."?post=$post&amp;all=$all&amp;action=upload\">".__('Go back').'</a>');

wp_delete_attachment($attachment);

header("Location: ".basename(__FILE__)."?post=$post&all=$all&action=view&start=$start");
die;

case 'save':

$overrides = array('action'=>'save');

$file = wp_handle_upload($_FILES['image'], $overrides);

if ( isset($file['error']) )
	die($file['error'] . '<a href="' . basename(__FILE__) . '?action=upload&post="' . $post . '">'.__('Back to Image Uploading').'</a>');

$url = $file['url'];
$file = $file['file'];
$filename = basename($file);

// Construct the attachment array
$attachment = array(
	'post_title' => $imgtitle ? $imgtitle : $filename,
	'post_content' => $descr,
	'post_status' => 'attachment',
	'post_parent' => $post,
	'post_mime_type' => $_FILES['image']['type'],
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
	$imagedata['thumb'] = "thumb-$filename";

	add_post_meta($id, '_wp_attachment_metadata', $imagedata);

	if ( $imagedata['width'] * $imagedata['height'] < 3 * 1024 * 1024 ) {
		if ( $imagedata['width'] > 128 && $imagedata['width'] >= $imagedata['height'] * 4 / 3 )
			$error = wp_create_thumbnail($file, 128);
		elseif ( $imagedata['height'] > 96 )
			$error = wp_create_thumbnail($file, 96);
	}
} else {
	add_post_meta($id, '_wp_attachment_metadata', array());
}

header("Location: ".basename(__FILE__)."?post=$post&all=$all&action=view&last=true");
die();

case 'upload':

$current_1 = ' class="current"';
$back = $next = false;
break;

case 'view':

// How many images do we show? How many do we query?
$num = 5;
$double = $num * 2;

if ( $post && empty($all) ) {
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
	header("Location: ".basename(__FILE__)."?post=$post&action=upload");
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
var aa = new Array();
var ab = new Array();
var imga = new Array();
var imgb = new Array();
var srca = new Array();
var srcb = new Array();
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
		$send_delete_cancel = "<a onclick=\"sendToEditor({$ID});return false;\" href=\"javascript:void()\">$__send_to_editor</a>
<a onclick=\"return confirm('$__confirmdelete')\" href=\"".basename(__FILE__)."?action=delete&amp;attachment={$ID}&amp;all=$all&amp;start=$start&amp;post=$post\">$__delete</a>
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
			$script .= "aa[{$ID}] = '<a id=\"{$ID}\" rel=\"attachment\" class=\"imagelink\" href=\"$href\" onclick=\"doPopup({$ID});return false;\" title=\"{$image['post_title']}\">';
ab[{$ID}] = '<a class=\"imagelink\" href=\"{$image['guid']}\" onclick=\"doPopup({$ID});return false;\" title=\"{$image['post_title']}\">';
imga[{$ID}] = '<img id=\"image{$ID}\" src=\"$src\" alt=\"{$image['post_title']}\" $height_width />';
imgb[{$ID}] = '<img id=\"image{$ID}\" src=\"{$image['guid']}\" alt=\"{$image['post_title']}\" $height_width />';
";
			$html .= "<div id='target{$ID}' class='attwrap left'>
	<div id='div{$ID}' class='imagewrap' onclick=\"doPopup({$ID});\">
		<img id=\"image{$ID}\" src=\"$src\" alt=\"{$image['post_title']}\" $height_width />
	</div>
</div>
";
			$popups .= "<div id='popup{$ID}' class='popup'>
	<a id=\"I{$ID}\" onclick=\"if($thumb)toggleImage({$ID});else alert('$__nothumb');return false;\" href=\"javascript:void()\">$thumbtext</a>
	<a id=\"L{$ID}\" onclick=\"toggleLink({$ID});return false;\" href=\"javascript:void()\">$__not_linked</a>
	{$send_delete_cancel}
</div>
";
		} else {
			$script .= "aa[{$ID}] = '<a id=\"{$ID}\" rel=\"attachment\" href=\"$href\" onclick=\"doPopup({$ID});return false;\" title=\"{$attachment['post_title']}\">{$attachment['post_title']}</a>';
ab[{$ID}] = '<a id=\"{$ID}\" href=\"{$attachment['guid']}\" onclick=\"doPopup({$ID});return false;\" title=\"{$attachment['post_title']}\">{$attachment['post_title']}</a>';
";
			$html .= "<div id='target{$ID}' class='attwrap left'>
	<div id='div{$ID}' class='otherwrap' onmousedown=\"selectLink({$ID})\" onclick=\"doPopup({$ID});return false;\">
		<a id=\"{$ID}\" href=\"{$attachment['guid']}\" onmousedown=\"selectLink({$ID});\" onclick=\"return false;\">{$attachment['post_title']}</a>
	</div>
</div>
";
			$popups .= "<div id='popup{$ID}' class='popup'>
	<div class='filetype'>".__('File Type:').' '.str_replace('/',"/\n",$attachment['post_mime_type'])."</div>
	<a id=\"L{$ID}\" onclick=\"toggleOtherLink({$ID});return false;\" href=\"javascript:void()\">$__linked_to_file</a>
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
<meta http-equiv="imagetoolbar" content="no" />
<script type="text/javascript">
/* Define any variables we'll need, such as alternate URLs. */
<?php echo $script; ?>

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
	od=document.getElementById('div'+n);
	ol=document.getElementById('L'+n);
	oi=document.getElementById('I'+n);
	if ( oi.innerHTML == usingthumbnail ) {
		img = imga[n];
	} else {
		img = imgb[n];
	}
	if ( ol.innerHTML == notlinked ) {
		od.innerHTML = ab[n]+img+'</a>';
		ol.innerHTML = linkedtoimage;
	} else if ( ol.innerHTML == linkedtoimage ) {
		od.innerHTML = aa[n]+img+'</a>';
		ol.innerHTML = linkedtopage;
	} else {
		od.innerHTML = img;
		ol.innerHTML = notlinked;
	}
}
function toggleOtherLink(n) {
	od=document.getElementById('div'+n);
	ol=document.getElementById('L'+n);
	if ( ol.innerHTML == linkedtofile ) {
		od.innerHTML = aa[n];
		ol.innerHTML = linkedtopage;
	} else {
		od.innerHTML = ab[n];
		ol.innerHTML = linkedtofile;
	}
}
function toggleImage(n) {
	o = document.getElementById('image'+n);
	oi = document.getElementById('I'+n);
	if ( oi.innerHTML == usingthumbnail ) {
		o.src = srcb[n];
		oi.innerHTML = usingoriginal;
	} else {
		o.src = srca[n];
		oi.innerHTML = usingthumbnail;
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
	h = h.replace(new RegExp(' on(click|mousedown)="[^"]*"', 'g'), ''); // Drop menu events
	h = h.replace(new RegExp('<(/?)A', 'g'), '<$1a'); // Lowercase tagnames
	h = h.replace(new RegExp('<IMG', 'g'), '<img'); // Lowercase again
	h = h.replace(new RegExp('(<img .+?")>', 'g'), '$1 />'); // XHTML
	if ( richedit )
		win.tinyMCE.execCommand('mceInsertContent', false, h);
	else
		win.edInsertContent(win.edCanvas, h);
}
</script>
<style type="text/css">
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
	height: 96px;
/*	white-space: nowrap;*/
	width: <?php echo $images_width; ?>px;
}
#images img {
	background-color: rgb(209, 226, 239);
}
<?php echo $style; ?>
.attwrap, .attwrap * {
	overflow: none;
	margin: 0px;
	padding: 0px;
	border: 0px;
}
.imagewrap {
	margin-right: 5px;
	height: 96px;
	overflow: hidden;
	float: left;
}
.otherwrap {
	margin-right: 5px;
	height: 90px;
	overflow: hidden;
	background-color: #f9fcfe;
	float: left;
	padding: 3px;
}
.otherwrap a {
	display: block;
	width: 122px;
}
.otherwrap a, .otherwrap a:hover, .otherwrap a:active, .otherwrap a:visited {
	color: blue;
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
	width: 100%;
	margin-top: 1px;
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
	padding: 3px;
	position: absolute;
	width: 114px;
	height: 82px;
	display: none;
	background-color: rgb(223, 232, 241);
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
</style>
</head>
<body>
<ul id="upload-menu">
<li<?php echo $current_1; ?>><a href="<?php echo basename(__FILE__); ?>?action=upload&amp;post=<?php echo $post; ?>&amp;all=<?php echo $all; ?>"><?php _e('Upload'); ?></a></li>
<?php if ( $attachments = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_parent = '$post'") ) { ?>
<li<?php echo $current_2; ?>><a href="<?php echo basename(__FILE__); ?>?action=view&amp;post=<?php echo $post; ?>"><?php _e('Browse'); ?></a></li>
<?php } ?>
<?php if ($wpdb->get_var("SELECT count(ID) FROM $wpdb->posts WHERE post_status = 'attachment'")) { ?>
<li<?php echo $current_3; ?>><a href="<?php echo basename(__FILE__); ?>?action=view&amp;post=<?php echo $post; ?>&amp;all=true"><?php _e('Browse All'); ?></a></li>
<?php } ?>
<li> </li>
<?php if ( $action != 'upload' ) { ?>
<?php if ( false !== $back ) : ?>
<li class="spacer"><a href="<?php echo basename(__FILE__); ?>?action=<?php echo $action; ?>&amp;post=<?php echo $post; ?>&amp;all=<?php echo $all; ?>&amp;start=0" title="<?php _e('First'); ?>">|&laquo;</a></li>
<li><a href="<?php echo basename(__FILE__); ?>?action=<?php echo $action; ?>&amp;post=<?php echo $post; ?>&amp;all=<?php echo $all; ?>&amp;start=<?php echo $back; ?>"">&laquo; <?php _e('Back'); ?></a></li>
<?php else : ?>
<li class="inactive spacer">|&laquo;</li>
<li class="inactive">&laquo; <?php _e('Back'); ?></li>
<?php endif; ?>
<?php if ( false !== $next ) : ?>
<li><a href="<?php echo basename(__FILE__); ?>?action=<?php echo $action; ?>&amp;post=<?php echo $post; ?>&amp;all=<?php echo $all; ?>&amp;start=<?php echo $next; ?>"><?php _e('Next'); ?> &raquo;</a></li>
<li><a href="<?php echo basename(__FILE__); ?>?action=<?php echo $action; ?>&amp;post=<?php echo $post; ?>&amp;all=<?php echo $all; ?>&amp;last=true" title="<?php _e('Last'); ?>">&raquo;|</a></li>
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
<form enctype="multipart/form-data" id="uploadForm" method="POST" action="<?php echo basename(__FILE__); ?>">
<table style="width:99%;">
<tr>
<th scope="row" style="width: 4.5em;text-align: right;"><label for="upload"><?php _e('File:'); ?></label></th>
<td><input type="file" id="upload" name="image" /></td>
</tr>
<tr>
<th scope="row" style="text-align: right;"><label for="title"><?php _e('Title:'); ?></label></th>
<td><input type="text" id="title" name="imgtitle" /></td>
</tr>
<tr>
<th scope="row" style="text-align: right;"><label for="descr"><?php _e('Description:'); ?></label></th>
<td><input type="textarea" name="descr" id="descr" value="" /></td>
</tr>
<tr id="buttons">
<th></th>
<td>
<input type="hidden" name="action" value="save" />
<input type="hidden" name="post" value="<?php echo $post; ?>" />
<input type="hidden" name="all" value="<?php echo $all; ?>" />
<input type="submit" value="<?php _e('Upload'); ?>" />
<input type="button" value="<?php _e('Cancel'); ?>" onclick="cancelUpload()" />
</td>
</tr>
</table>
</div>
</form>
<?php endif; ?>
</body>
</html>
