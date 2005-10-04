<?php

require_once('admin.php');

if (!current_user_can('edit_posts'))
	die('You do not have permission to edit posts.');

$wpvarstoreset = array('action', 'post', 'all', 'last', 'link', 'sort', 'start', 'imgtitle', 'descr', 'object');

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
	if ( $height < 96 && $width < 128 )
		return array($width, $height);
	elseif ( $width / $height > 4 / 3 )
		return array(128, (int) ($height / $width * 128));
	else
		return array((int) ($width / $height * 96), 96);
}

switch($action) {
case 'delete':

wp_delete_object($object);

header("Location: ".basename(__FILE__)."?post=$post&all=$all&action=view&start=$start");
die;

case 'save':

// Define acceptable image extentions/types here. Tests will apply strtolower().
$exts = array('gif' => IMAGETYPE_GIF, 'jpg' => IMAGETYPE_JPEG, 'png' => IMAGETYPE_PNG);

// Define the error messages for bad uploads.
$upload_err = array(false,
	"The uploaded file exceeds the <code>upload_max_filesize</code> directive in <code>php.ini</code>.",
	"The uploaded file exceeds the <em>MAX_FILE_SIZE</em> directive that was specified in the HTML form.",
	"The uploaded file was only partially uploaded.",
	"No file was uploaded.",
	"Missing a temporary folder.",
	"Failed to write file to disk.");

$iuerror = false;

// Failing any single one of the following tests is fatal.

// A correct form post will pass this test.
if ( !isset($_POST['action']) || $_POST['action'] != 'save' || count($_FILES) != 1 || ! isset($_FILES['image']) || is_array($_FILES['image']['name']) )
	$error = 'Invalid form submission. Only submit approved forms.';

// A successful upload will pass this test.
elseif ( $_FILES['image']['error'] > 0 )
	$error = $upload_err[$_FILES['image']['error']];

// A non-empty file will pass this test.
elseif ( 0 == $_FILES['image']['size'] )
	$error = 'File is empty. Please upload something more substantial.';

// A correct MIME category will pass this test. Full types are not consistent across browsers.
elseif ( ! 'image/' == substr($_FILES['image']['type'], 0, 6) )
	$error = 'Bad MIME type submitted by your browser.';

// An acceptable file extension will pass this test.
elseif ( ! ( ( 0 !== preg_match('#\.?([^\.]*)$#', $_FILES['image']['name'], $matches) ) && ( $ext = strtolower($matches[1]) ) && array_key_exists($ext, $exts) ) )
	$error = 'Bad file extension.';

// A valid uploaded file will pass this test. 
elseif ( ! is_uploaded_file($_FILES['image']['tmp_name']) )
	$error = 'Bad temp file. Try renaming the file and uploading again.';

// A valid image file will pass this test.
elseif ( function_exists('exif_imagetype') && $exts[$ext] != $imagetype = exif_imagetype($_FILES['image']['tmp_name']) )
	$error = 'Bad image file. Try again, or try recreating it.';

// An image with at least one pixel will pass this test.
elseif ( ! ( ( $imagesize = getimagesize($_FILES['image']['tmp_name']) ) && $imagesize[0] > 1 && $imagesize[1] > 1 ) )
	$error = 'The image has no pixels. Isn\'t that odd?';

// A writable uploads dir will pass this test.
elseif ( ! ( ( $uploads = wp_upload_dir() ) && false === $uploads['error'] ) )
	$error = $uploads['error'];

if ( $error )
	// Something wasn't right. Abort and never touch the temp file again.
	die("$error <a href='".basename(__FILE__)."?action=upload&post=$post'>Back to Image Uploading</a>");

// Increment the file number until we have a unique file to save in $dir
$number = '';
$filename = $_FILES['image']['name'];
while ( file_exists($uploads['path'] . "/$filename") )
	$filename = str_replace("$number.$ext", ++$number . ".$ext", $filename);

// Move the file to the uploads dir
$file = $uploads['path'] . "/$filename";
if ( false === move_uploaded_file($_FILES['image']['tmp_name'], $file) )
	die('The uploaded file could not be moved to $file.');
chmod($file, 0775);

// Compute the URL
$url = $uploads['url'] . "/$filename";

// Construct the object array
$object = array(
	'post_title' => $imgtitle ? $imgtitle : $filename,
	'post_content' => $descr,
	'post_status' => 'object',
	'post_parent' => $post,
	'post_type' => $_FILES['image']['type'],
	'guid' => $url
	);

// Save the data
$id = wp_attach_object($object, $post);

// Generate the object's postmeta.
$imagesize = getimagesize($file);
$imagedata['width'] = $imagesize['0'];
$imagedata['height'] = $imagesize['1'];
list($uwidth, $uheight) = get_udims($imagedata['width'], $imagedata['height']);
$imagedata['hwstring_small'] = "height='$uheight' width='$uwidth'";
$imagedata['file'] = $file;

if ( false == add_post_meta($id, 'imagedata', $imagedata) )
	die("failed to add_post_meta");

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
	$start = $wpdb->get_var("SELECT count(ID) FROM $wpdb->posts WHERE post_status = 'object' AND left(post_type, 5) = 'image' $and_post") - $num;
else
	$start = (int) $start;

if ( $start < 0 )
	$start = 0;

if ( '' == $sort )
	$sort = "ID";

$images = $wpdb->get_results("SELECT ID, post_date, post_title, guid FROM $wpdb->posts WHERE post_status = 'object' AND left(post_type, 5) = 'image' $and_post ORDER BY $sort LIMIT $start, $double", ARRAY_A);

if ( count($images) > $num ) {
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

$i = 0;
$uwidth_sum = 0;
$images_html = '';
$images_style = '';
if ( count($images) > 0 ) {
	$images = array_slice( $images, 0, $num );
	foreach ( $images as $key => $image ) {
		$image = array_merge($image, get_post_meta($image['ID'], 'imagedata', true) );
		list($image['uwidth'], $image['uheight']) = get_udims($image['width'], $image['height']);
		$uwidth_sum += 128; //$image['uwidth'];
		$xpadding = (128 - $image['uwidth']) / 2;
		$ypadding = (96 - $image['uheight']) / 2;
		$object = $image['ID'];
		$images_style .= "#target$i img { padding: {$ypadding}px {$xpadding}px; }\n";
		$images_html .= <<<HERE
<div id='target$i' class='imagewrap left'>
	<div id='popup$i' class='popup'>
		<a onclick='return confirm("Delete this photo from the server?")' href='image-uploading.php?action=delete&amp;object=$object&amp;all=$all&amp;start=$start&amp;post=$post'>DELETE</a>
		<a onclick="popup.style.display='none';return false;" href="javascript:void()">CANCEL</a>
	</div>
	<a id='link$i' class='imagelink' href='{$image['guid']}' onclick='imagePopup($i);return false;' title='{$image['post_title']}'>
		<img id='image$i' src='{$image['guid']}' alt='{$image['post_title']}' {$image['hwstring_small']} />
	</a>
</div>
HERE;
		$i++;
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
<meta http-equiv="imagetoolbar" content="no" />
<script type="text/javascript">
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
</script>
<style type="text/css">
body {
font: 13px "Lucida Grande", "Lucida Sans Unicode", Tahoma, Verdana;
border: none;
margin: 0px;
height: 150px;
background: rgb(223, 232, 241);
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
width: <?php echo $images_width; ?>px;
}
#images img {
background-color: rgb(209, 226, 239);
}
<?php echo $images_style; ?>
.imagewrap {
margin-right: 5px;
}
.imagewrap * {
margin: 0px;
padding: 0px;
border: 0px;
}
.imagewrap a, .imagewrap a img, .imagewrap a:hover img, .imagewrap a:visited img, .imagewrap a:active img {
text-decoration: none;
float: left;
display: block;
text-align: center;
}
#menu {
margin: 0px;
list-style: none;
background: rgb(109, 166, 209);
padding: 4px 0px 0px 8px;
text-align: left;
border-bottom: 3px solid rgb(68, 138, 189);
}
#menu li {
display: inline;
margin: 0px;
}
#menu a, #menu a:visited, #menu a:active {
padding: 1px 3px 3px;
text-decoration: none;
color: #234;
background: transparent;
}
#menu a:hover {
background: rgb(203, 214, 228);
color: #000;
}
#menu .current a, #menu .current a:hover, #menu .current a:visited, #menu .current a:active {
background: rgb(223, 232, 241);
padding-bottom: 3px;
color: #000;
border-right: 2px solid rgb(20, 86, 138);
}
.tip {
color: rgb(68, 138, 189);
padding: 1px 3px;
}
.inactive {
color: #579;
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
#menu li.spacer {
margin-left: 40px;
}
label {
float: left;
width: 18%;
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
margin: 23px 9px;
padding: 5px;
position: absolute;
width: 100px;
height: 40px;
display: none;
background-color: rgb(223, 232, 241);
opacity: .90;
filter:alpha(opacity=90);
text-align: center;
}
.popup a, .popup a:visited, .popup a:active {
margin-bottom: 3px;
background-color: transparent;
display: block;
width: 100%;
text-decoration: none;
color: #246;
}
.popup a:hover {
margin-bottom: 3px;
background-color: #fff;
color: #000;
}
</style>
</head>
<body onload="init()">
<ul id="menu">
<li<?php echo $current_1; ?>><a href="image-uploading.php?action=upload&amp;post=<?php echo $post; ?>&amp;all=<?php echo $all; ?>">Upload Photo</a></li>
<li<?php echo $current_2; ?>><a href="image-uploading.php?action=view&amp;post=<?php echo $post; ?>">Browse Attached</a></li>
<li<?php echo $current_3; ?>><a href="image-uploading.php?action=view&amp;post=<?php echo $post; ?>&amp;all=true">Browse All</a></li>
<li> </li>
<?php if ( false !== $back ) : ?>
<li class="spacer"><a href="image-uploading.php?action=view&amp;post=<?php echo $post; ?>&amp;all=<?php echo $all; ?>&amp;start=0" title="First">|&lt;</a></li>
<li><a href="image-uploading.php?action=view&amp;post=<?php echo $post; ?>&amp;all=<?php echo $all; ?>&amp;start=<?php echo $back; ?>" title="Back">&lt;&lt;</a></li>
<?php else : ?>
<li class="inactive spacer">|&lt;</li>
<li class="inactive">&lt;&lt;</li>
<?php endif; ?>
<?php if ( false !== $next ) : ?>
<li><a href="image-uploading.php?action=view&amp;post=<?php echo $post; ?>&amp;all=<?php echo $all; ?>&amp;start=<?php echo $next; ?>" title="Next">&gt;&gt;</a></li>
<li><a href="image-uploading.php?action=view&amp;post=<?php echo $post; ?>&amp;all=<?php echo $all; ?>&amp;last=true" title="Last">&gt;|</a></li>
<?php else : ?>
<li class="inactive">&gt;&gt;</li>
<li class="inactive">&gt;|</li>
<?php endif; ?>
</ul>
<?php if ( $action == 'view' ) : ?>
<span class="left tip">Drag and drop photos to post</span>
<span class="right tip">Click photos for more options</span>
<div id="wrap">
<div id="images">
<?php echo $images_html; ?>
</div>
</div>
<?php elseif ( $action = 'upload' ) : ?>
<div class="center tip">Duplicated filenames will be numbered (photo.jpg, photo1.jpg, etc.)</div>
<form enctype="multipart/form-data" id="uploadForm" method="POST" action="image-uploading.php" onsubmit="return validateImageName()">
<label for="upload">Image:</label><input type="file" id="upload" name="image" onchange="validateImageName()" />
<label for="title">Title:</label><input type="text" id="title" name="imgtitle" />
<label for="descr">Description:</label><input type="textarea" name="descr" id="descr" value="" />
<input type="hidden" name="action" value="save" />
<input type="hidden" name="post" value="<?php echo $post; ?>" />
<input type="hidden" name="all" value="<?php echo $all; ?>" />
<div id="buttons">
<input type="submit" value="Upload" />
<input type="button" value="Cancel" onclick="cancelUpload()" />
</div>
</form>
<?php endif; ?>
</body>
</html>
