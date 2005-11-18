<?php

require_once('admin.php');

if (!current_user_can('edit_posts'))
	die(__('You do not have permission to edit posts.'));

$wpvarstoreset = array('action', 'post', 'all', 'last', 'link', 'sort', 'start', 'imgtitle', 'descr', 'attachment', 'flickrtag');

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

wp_delete_attachment($attachment);

header("Location: ".basename(__FILE__)."?post=$post&all=$all&action=view&start=$start");
die;

case 'save':

$overrides = array('action'=>'save');

$file = wp_handle_upload($_FILES['image'], $overrides);

if ( isset($file['error']) )
	die($file['error'] . '<a href="' . basename(__FILE__) . '?action=upload&post="' . $post . '">Back to Image Uploading</a>');

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

header("Location: ".basename(__FILE__)."?post=$post&all=$all&action=view&last=true");
die;

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

if ( $last )
	$start = $wpdb->get_var("SELECT count(ID) FROM $wpdb->posts WHERE post_status = 'attachment' AND left(post_mime_type, 5) = 'image' $and_post") - $num;
else
	$start = (int) $start;

if ( $start < 0 )
	$start = 0;

if ( '' == $sort )
	$sort = "post_date_gmt DESC";

$images = $wpdb->get_results("SELECT ID, post_date, post_title, guid FROM $wpdb->posts WHERE post_status = 'attachment' AND left(post_mime_type, 5) = 'image' $and_post ORDER BY $sort LIMIT $start, $double", ARRAY_A);

if ( count($images) == 0 ) {
	header("Location: ".basename(__FILE__)."?post=$post&action=upload");
	die;
} elseif ( count($images) > $num ) {
	$next = $start + count($images) - $num;
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
$images_html = '';
$images_style = '';
$images_script = '';
if ( count($images) > 0 ) {
	$images = array_slice( $images, 0, $num );
	$__delete = __('Delete');
	$__attachment_on = __('Link to Page');
	$__attachment_off = __('Link to Image');
	$__thumbnail_on = __('Use Thumbnail');
	$__thumbnail_off = __('Use Full Image');
	$__no_thumbnail = __('<del>No Thumbnail</del>');
	$__close = __('Close Options');
	$__confirmdelete = __('Delete this photo from the server?');
	$__nothumb = __('There is no thumbnail associated with this photo.');
	$images_script .= "attachmenton = '$__attachment_on';\nattachmentoff = '$__attachment_off';\n";
	$images_script .= "thumbnailon = '$__thumbnail_on';\nthumbnailoff = '$__thumbnail_off';\n";
	foreach ( $images as $key => $image ) {
		$attachment_ID = $image['ID'];
		$meta = get_post_meta($attachment_ID, '_wp_attachment_metadata', true);
		if (!is_array($meta)) {
			$meta = get_post_meta($attachment_ID, 'imagedata', true); // Try 1.6 Alpha meta key
			if (!is_array($meta)) {
				continue;
			} else {
				add_post_meta($attachment_ID, '_wp_attachment_metadata', $meta);
			}
		}
		$image = array_merge($image, $meta);
		if ( ($image['width'] > 128 || $image['height'] > 96) && !empty($image['thumb']) && file_exists(dirname($image['file']).'/'.$image['thumb']) ) {
			$src = str_replace(basename($image['guid']), '', $image['guid']) . $image['thumb'];
			$images_script .= "src".$attachment_ID."a = '$src';\nsrc".$attachment_ID."b = '".$image['guid']."';\n";
			$thumb = 'true';
			$thumbtext = $__thumbnail_on;
		} else {
			$src = $image['guid'];
			$thumb = 'false';
			$thumbtext = $__no_thumbnail;
		}
		list($image['uwidth'], $image['uheight']) = get_udims($image['width'], $image['height']);
		$height_width = 'height="'.$image['uheight'].'" width="'.$image['uwidth'].'"';
		$uwidth_sum += 128;
		$xpadding = (128 - $image['uwidth']) / 2;
		$ypadding = (96 - $image['uheight']) / 2;
		$images_style .= "#target{$attachment_ID} img { padding: {$ypadding}px {$xpadding}px; }\n";
		$href = get_attachment_link($attachment_ID);
		$images_script .= "href{$attachment_ID}a = '$href';\nhref{$attachment_ID}b = '{$image['guid']}';\n";
		$images_html .= "
<div id='target{$attachment_ID}' class='imagewrap left'>
	<div id='popup{$attachment_ID}' class='popup'>
		<a id=\"L{$attachment_ID}\" onclick=\"toggleLink({$attachment_ID});return false;\" href=\"javascript:void();\">$__attachment_on</a>
		<a id=\"I{$attachment_ID}\" onclick=\"if($thumb)toggleImage({$attachment_ID});else alert('$__nothumb');return false;\" href=\"javascript:void();\">$thumbtext</a>
		<a onclick=\"return confirm('$__confirmdelete')\" href=\"".basename(__FILE__)."?action=delete&amp;attachment={$attachment_ID}&amp;all=$all&amp;start=$start&amp;post=$post\">$__delete</a>
		<a onclick=\"popup.style.display='none';return false;\" href=\"javascript:void()\">$__close</a>
	</div>
	<a id=\"{$attachment_ID}\" rel=\"attachment\" class=\"imagelink\" href=\"$href\" onclick=\"imagePopup({$attachment_ID});return false;\" title=\"{$image['post_title']}\">		
		<img id=\"image{$attachment_ID}\" src=\"$src\" alt=\"{$attachment_ID}\" $height_width />
	</a>
</div>
";
	}
}

$images_width = $uwidth_sum + ( count($images) * 5 ) + 30;

break;

default:
die('This script was not meant to be called directly.');
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_settings('blog_charset'); ?>" />
<meta http-equiv="imagetoolbar" content="no" />
<script type="text/javascript">
/* Define any variables we'll need, such as alternate URLs. */
<?php echo $images_script; ?>

function validateImageName() {
/* This is more for convenience than security. Server-side validation is very thorough.*/
obj = document.getElementById('upload');
r = /.jpg$|.gif$|.png$/i;
if ( obj.value.match(r) )
return true;
alert('Please select a JPG, PNG or GIF file.');
return false;
}
function cancelUpload() {
o = document.getElementById('uploadForm');
o.method = 'GET';
o.action.value = 'view';
o.submit();
}
function imagePopup(i) {
if ( popup )
popup.style.display = 'none';
target = document.getElementById('target'+i);
popup = document.getElementById('popup'+i);
//popup.style.top = (target.offsetTop + 3) + 'px';
popup.style.left = (target.offsetLeft) + 'px';
popup.style.display = 'block';
}
function init() {
popup = false;
}
function toggleLink(n) {
	o=document.getElementById(n);
	oi=document.getElementById('L'+n);
	if ( oi.innerHTML == attachmenton ) {
		o.href = eval('href'+n+'b');
		oi.innerHTML = attachmentoff;
	} else {
		o.href = eval('href'+n+'a');
		oi.innerHTML = attachmenton;
	}
}
function toggleImage(n) {
	o = document.getElementById('image'+n);
	oi = document.getElementById('I'+n);
	if ( oi.innerHTML == thumbnailon ) {
		o.src = eval('src'+n+'b');
		oi.innerHTML = thumbnailoff;
	} else {
		o.src = eval('src'+n+'a');
		oi.innerHTML = thumbnailon;
	}
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
	margin: 6px 2px 0px 6px;
}
#wrap {
	clear: both;
	margin: 0px;
	padding: 0px;
	height: 133px;
	width: 100%;
	overflow: auto;
}
#images {
	clear: both;
	margin: 0px;
	padding: 5px 15px;
	height: 96px;
	white-space: nowrap;
	width: <?php echo $images_width; ?>px;
}
#images img {
	background-color: rgb(209, 226, 239);
}
<?php echo $images_style; ?>
.imagewrap {
	margin-right: 5px;
	height: 96px;
	overflow: hidden;
}
.imagewrap * {
	margin: 0px;
	padding: 0px;
	border: 0px;
}
.imagewrap a, .imagewrap a img, .imagewrap a:hover img, .imagewrap a:visited img, .imagewrap a:active img {
	text-decoration: none;
	float: left;
	text-align: center;
}

#upload-menu {
	background: #fff;
	margin: 0;
	padding: 0;
	list-style: none;
	height: 2em;
	border-bottom: 1px solid #448abd;
}

#upload-menu li {
	float: left;
	margin: 0 0 0 1em;
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
	width: 80%;
	margin-top: 2px;
}
#descr {
	height: 35px;
	v-align: top;
}
#buttons {
	width: 98%;
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
	opacity: .90;
	filter:alpha(opacity=90);
	text-align: center;
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
<body onload="init()">
<ul id="upload-menu">
<li<?php echo $current_1; ?>><a href="<?php echo basename(__FILE__); ?>?action=upload&amp;post=<?php echo $post; ?>&amp;all=<?php echo $all; ?>"><?php _e('Upload Image'); ?></a></li>
<?php if ( $attachments = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_parent = '$post'") ) { ?>
<li<?php echo $current_2; ?>><a href="<?php echo basename(__FILE__); ?>?action=view&amp;post=<?php echo $post; ?>"><?php _e('Attached Images'); ?></a></li>
<?php } ?>
<li<?php echo $current_3; ?>><a href="<?php echo basename(__FILE__); ?>?action=view&amp;post=<?php echo $post; ?>&amp;all=true"><?php _e('All Images'); ?></a></li>
<li> </li>
<?php if ( $action != 'upload' ) { ?>
<?php if ( false !== $back ) : ?>
<li class="spacer"><a href="<?php echo basename(__FILE__); ?>?action=<?php echo $action; ?>&amp;post=<?php echo $post; ?>&amp;all=<?php echo $all; ?>&amp;start=0" title="<?php _e('First'); ?>">|&laquo;</a></li>
<li><a href="<?php echo basename(__FILE__); ?>?action=<?php echo $action; ?>&amp;post=<?php echo $post; ?>&amp;all=<?php echo $all; ?>&amp;start=<?php echo $back; ?>"">&laquo; <?php _e('Back'); ?></a></li>
<?php else : ?>
<li class="inactive spacer">|&lt;</li>
<li class="inactive">&lt;&lt;</li>
<?php endif; ?>
<?php if ( false !== $next ) : ?>
<li><a href="<?php echo basename(__FILE__); ?>?action=<?php echo $action; ?>&amp;post=<?php echo $post; ?>&amp;all=<?php echo $all; ?>&amp;start=<?php echo $next; ?>"><?php _e('Next'); ?> &raquo;</a></li>
<li><a href="<?php echo basename(__FILE__); ?>?action=<?php echo $action; ?>&amp;post=<?php echo $post; ?>&amp;all=<?php echo $all; ?>&amp;last=true" title="<?php _e('Last'); ?>">&raquo;|</a></li>
<?php else : ?>
<li class="inactive">&gt;&gt;</li>
<li class="inactive">&gt;|</li>
<?php endif; ?>
<?php } // endif not upload?>
</ul>
<?php if ( $action == 'view' ) : ?>
<span class="left tip"><?php _e('You can drag and drop these photos into your post. Click on the thumbnail for more options.'); ?></span>
<span class="right tip"></span>
<div id="wrap">
<div id="images">
<?php echo $images_html; ?>
</div>
</div>
<?php elseif ( $action == 'upload' ) : ?>
<div class="tip"></div>
<form enctype="multipart/form-data" id="uploadForm" method="POST" action="<?php echo basename(__FILE__); ?>" onsubmit="return validateImageName()">
<table style="width: 100%">
<tr>
<th scope="row" style="width: 6em; text-align: right;"><label for="upload"><?php _e('Image:'); ?></label></th>
<td><input type="file" id="upload" name="image" onchange="validateImageName()" /></td>
</tr>
<tr>
<th scope="row" style="text-align: right;"><label for="title"><?php _e('Title:'); ?></label></th>
<td><input type="text" id="title" name="imgtitle" /></td>
</tr>
<tr>
<th scope="row" style="text-align: right;"><label for="descr"><?php _e('Description:'); ?></th>
<td><input type="textarea" name="descr" id="descr" value="" /></td>
</tr>
</table>
<p class="submit">
<input type="hidden" name="action" value="save" />
<input type="hidden" name="post" value="<?php echo $post; ?>" />
<input type="hidden" name="all" value="<?php echo $all; ?>" />
<input type="submit" value="<?php _e('Upload'); ?>" />
<input type="button" value="<?php _e('Cancel'); ?>" onclick="cancelUpload()" />
</div>
</form>
<?php endif; ?>
</body>
</html>



