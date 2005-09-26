<?php
require_once('admin.php');

if (!current_user_can('edit_posts'))
	die('You do not have permission to edit posts.');

$wpvarstoreset = array('action', 'post', 'all', 'last', 'link', 'sort', 'start', 'imgtitle', 'descr');

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

switch($action) {
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
move_uploaded_file($_FILES['image']['tmp_name'], $file);
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
if ( $imagedata['height'] < 96 && $imagedata['width'] < 128 ) {
	$uheight = $imagedata['height'];
	$uwidth = $imagedata['width'];
} elseif ( $imagedata['width'] / $imagedata['height'] > 4 / 3 ) {
	$uwidth = 128;
	$uheight = $imagedata['height'] / $imagedata['width'] * $uwidth;
} else {
	$uheight = 96;
	$uwidth = $imagedata['width'] / $imagedata['height'] * $uheight;
}
$imagedata['hwstring_small'] = "height='$uheight' width='$uwidth'";
$imagedata['file'] = $file;

if ( false == add_post_meta($id, 'imagedata', $imagedata) )
	die("failed to add_post_meta");

header("Location: ".basename(__FILE__)."?post=$post&all=$all&action=view&last=true");
die;

case 'upload':
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<script type="text/javascript">
			function validateImageName() {
				/* This is more for convenience than security. Server-side validation is very thorough.*/
				obj = document.getElementById('upload');
				r = /.jpg$|.gif$|.png$/i;
				if ( obj.value.match(r) )
					return true;
				alert('Please select a JPG, PNG or GIF file.');
				obj.parentNode.reset();
				return false;
			}
			function cancelUpload() {
				o = document.getElementById('uploadForm');
				o.method = 'GET';
				o.action.value = 'view';
				o.submit();
			}
		</script>
		<style type="text/css">
			label {
				float: left;
				width: 18%;
			}
			#title, #descr {
				width: 80%;
				margin-top: 2px;
			}
			#descr {
				height: 3em;
				v-align: top;
			}
			#buttons {
				width: 98%;
				text-align: right;
			}
		</style>
	</head>
	<body>
		<form enctype="multipart/form-data" id="uploadForm" method="POST" action="image-uploading.php" onsubmit="return validateImageName()">
			<label for="upload">Image:</label><input type="file" id="upload" name="image" onchange="validateImageName()" /><br />
			<label for="title">Title:</label><input type="text" id="title" name="imgtitle" /><br />
			<label for="descr">Description:</label><input type="textarea" name="descr" id="descr" value="" /><br />
			<input type="hidden" name="action" value="save" />
			<input type="hidden" name="post" value="<?php echo $post; ?>" />
			<input type="hidden" name="all" value="<?php echo $all; ?>" />
			<div id="buttons">
				<input type="submit" value="Upload" />
				<input type="button" value="Cancel" onclick="cancelUpload()" />
			</div>
		</form>
	</body>
</html>
<?php

break;

case 'view':

if ( $post && empty($all) )
	$and_post = "AND post_parent = '$post'";

if ( $last )
	$start = $wpdb->get_var("SELECT count(ID) FROM $wpdb->posts WHERE post_status = 'object' AND left(post_type, 5) = 'image' $and_post") - 5;
else
	$start = (int) $start;

if ( $start < 0 )
	$start = 0;

if ( '' == $sort )
	$sort = "ID";

$images = $wpdb->get_results("SELECT ID, post_date, post_title, guid FROM $wpdb->posts WHERE post_status = 'object' AND left(post_type, 5) = 'image' $and_post ORDER BY $sort LIMIT $start, 10", ARRAY_A);

//if ( count($images) == 0 )
//	header("Location: ".basename(__FILE__)."?post=$post&all=$all&action=upload");

if ( count($images) > 5 ) {
	$next = $start + count($images) - 5;
} else {
	$next = false;
}

if ( $start > 0 ) {
	$back = $start - 5;
	if ( $back < 1 )
		$back = '0';
} else {
	$back = false;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<style type="text/css">
		form {
			display: inline;
		}
		#images, #buttons {
			position: absolute;
			left: 0px;
			width: 98%;
			text-align: center;
		}
		#images {
			top: 0px;
		}
		#buttons {
			top: 112px;
		}
	</style>
</head>
<body>
	<div id="images">
<?php
if ( count($images) > 0 ) {
	$imagerow = '';
	$i = 1;
	foreach ( $images as $image ) {
		if ( $i++ > 5 ) break;
		$image = array_merge($image, get_post_meta($image['ID'], 'imagedata', true) );
?>
	<a href="<?php echo $image['guid']; ?>" disabled="true">
		<img src="<?php echo $image['guid']; ?>" alt="<?php echo $image['post_title']; ?>" <?php echo $image['hwstring_small']; ?> />
	</a>
<?php
	}
}
?>
	<div>
	<div id="buttons">
	<form action="image-uploading.php" method="GET">
		<input type="hidden" name="action" value="view" />
		<input type="hidden" name="all" value="<?php echo $all; ?>" />
		<input type="hidden" name="post" value="<?php echo $post; ?>" />
		<input type="hidden" name="start" value="0" />
		<input type="submit" value="| < <" <?php if ( false === $back ) echo 'disabled="true" ' ?>/>
	</form>
	<form action="image-uploading.php" method="GET">
		<input type="hidden" name="action" value="view" />
		<input type="hidden" name="all" value="<?php echo $all; ?>" />
		<input type="hidden" name="post" value="<?php echo $post; ?>" />
		<input type="hidden" name="start" value="<?php echo $back; ?>" />
		<input type="submit" value="< < < < <" <?php if ( false === $back ) echo 'disabled="true" ' ?>/>
	</form>
	<form action="image-uploading.php" method="GET">
		<input type="hidden" name="action" value="upload" />
		<input type="hidden" name="all" value="<?php echo $all; ?>" />
		<input type="hidden" name="post" value="<?php echo $post; ?>" />
		<input type="submit" value="Upload New" />
	</form>
<?php if ( $all ) : ?>
	<form action="image-uploading.php" method="GET">
		<input type="hidden" name="action" value="view" />
		<input type="hidden" name="all" value="" />
		<input type="hidden" name="post" value="<?php echo $post; ?>" />
		<input type="submit" value="Browse Attached" />
	</form>
<?php else : ?>
	<form action="image-uploading.php" method="GET">
		<input type="hidden" name="action" value="view" />
		<input type="hidden" name="all" value="true" />
		<input type="hidden" name="post" value="<?php echo $post; ?>" />
		<input type="submit" value="Browse All" />
	</form>
<?php endif; ?>
	<form action="image-uploading.php" method="GET">
		<input type="hidden" name="action" value="view" />
		<input type="hidden" name="all" value="<?php echo $all; ?>" />
		<input type="hidden" name="post" value="<?php echo $post; ?>" />
		<input type="hidden" name="start" value="<?php echo $next; ?>" />
		<input type="submit" value="> > > > >" <?php if ( false === $next ) echo 'disabled="true" ' ?>/>
	</form>
	<form action="image-uploading.php" method="GET">
		<input type="hidden" name="action" value="view" />
		<input type="hidden" name="all" value="<?php echo $all; ?>" />
		<input type="hidden" name="post" value="<?php echo $post; ?>" />
		<input type="hidden" name="last" value="true" />
		<input type="submit" value="> > |" <?php if ( false === $next ) echo 'disabled="true" ' ?>/>
	</form>
	</div>
<?php // echo "<pre>".print_r($images,1)."</pre>";
?>
</body>
</html>
<?php
die;

default:
die('This script was not meant to be called directly.');
}
?>
