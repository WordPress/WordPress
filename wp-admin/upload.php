<?php
$title = 'Upload Image or File';

require_once('admin-header.php');

if ($user_level == 0) //Checks to see if user has logged in
	die ("Cheatin' uh ?");

if (!get_settings('use_fileupload')) //Checks if file upload is enabled in the config
	die ("The admin disabled this function");

$allowed_types = explode(' ', trim(get_settings('fileupload_allowedtypes')));

if ($HTTP_POST_VARS['submit']) {
	$action = 'upload';
} else {
	$action = '';
}

if (!is_writable(get_settings('fileupload_realpath')))
	$action = 'not-writable';
?>

<div class="wrap">

<?php
switch ($action) {
case 'not-writable':
?>
<p>It doesn't look like you can use the file upload feature at this time because the directory you have specified (<code><?php echo $get_settings('fileupload_realpath'); ?></code>) doesn't appear to be writable by WordPress. Check the permissions on the directory and for typos.</p>

<?php
break;
case '':
	foreach ($allowed_types as $type) {
		$type_tags[] = "<code>$type</code>";
	}
	$i = implode(', ', $type_tags);
?>
    <p>You can upload files with the extension <?php echo $i ?> as long as they are no larger than <?php echo $fileupload_maxk; ?> <abbr title="Kilobytes">KB</abbr>. If you&#8217;re an admin you can configure these values under <a href="options.php">options</a>.</p>
    <form action="upload.php" method="post" enctype="multipart/form-data">
    <p>
      <label for="img1">File:</label>
      <br />
	<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo get_settings('fileupload_maxk') * 1024 ?>" />
    <input type="file" name="img1" id="img1" size="35" class="uploadform" /></p>
    <p>
      <label for="imgdesc">Description:</label><br />
    <input type="text" name="imgdesc" id="imgdesc" size="30" class="uploadform" />
    </p>
	
    <p>Create a thumbnail?</p>
    <p>
    <label for="thumbsize_no">
    <input type="radio" name="thumbsize" value="none" checked="checked" id="thumbsize_no" />
    No thanks</label>
    <br />
        <label for="thumbsize_small">
<input type="radio" name="thumbsize" value="small" id="thumbsize_small" />
Small (200px largest side)</label>
        <br />
        <label for="thumbsize_large">
<input type="radio" name="thumbsize" value="large" id="thumbsize_large" />
Large (400px largest side)</label>
        <br />
        <label for="thumbsize_custom">
        <input type="radio" name="thumbsize" value="custom" id="thumbsize_custom" />
        Custom size</label>
      : 
      <input type="text" name="imgthumbsizecustom" size="4" />
      px (largest side)    </p>
	<p><input type="submit" name="submit" value="Upload File" /></p>
    </form>
</div><?php 
break;
case 'upload':
?>

<?php //Makes sure they choose a file

//print_r($HTTP_POST_FILES);
//die();


    $imgalt = (isset($HTTP_POST_VARS['imgalt'])) ? $HTTP_POST_VARS['imgalt'] : $imgalt;

    $img1_name = (strlen($imgalt)) ? $HTTP_POST_VARS['imgalt'] : $HTTP_POST_FILES['img1']['name'];
    $img1_type = (strlen($imgalt)) ? $HTTP_POST_VARS['img1_type'] : $HTTP_POST_FILES['img1']['type'];
    $imgdesc = str_replace('"', '&amp;quot;', $HTTP_POST_VARS['imgdesc']);

    $imgtype = explode(".",$img1_name);
    $imgtype = $imgtype[count($imgtype)-1];

    if (in_array($imgtype, $allowed_types) == false) {
        die("File $img1_name of type $imgtype is not allowed.");
    }

    if (strlen($imgalt)) {
        $pathtofile = get_settings('fileupload_realpath')."/".$imgalt;
        $img1 = $HTTP_POST_VARS['img1'];
    } else {
        $pathtofile = get_settings('fileupload_realpath')."/".$img1_name;
        $img1 = $HTTP_POST_FILES['img1']['tmp_name'];
    }

    // makes sure not to upload duplicates, rename duplicates
    $i = 1;
    $pathtofile2 = $pathtofile;
    $tmppathtofile = $pathtofile2;
    $img2_name = $img1_name;

    while (file_exists($pathtofile2)) {
        $pos = strpos($tmppathtofile, '.'.trim($imgtype));
        $pathtofile_start = substr($tmppathtofile, 0, $pos);
        $pathtofile2 = $pathtofile_start.'_'.zeroise($i++, 2).'.'.trim($imgtype);
        $img2_name = explode('/', $pathtofile2);
        $img2_name = $img2_name[count($img2_name)-1];
    }

    if (file_exists($pathtofile) && !strlen($imgalt)) {
        $i = explode(' ', get_settings('fileupload_allowedtypes'));
        $i = implode(', ',array_slice($i, 1, count($i)-2));
        $moved = move_uploaded_file($img1, $pathtofile2);
        // if move_uploaded_file() fails, try copy()
        if (!$moved) {
            $moved = copy($img1, $pathtofile2);
        }
        if (!$moved) {
            die("Couldn't Upload Your File to $pathtofile2.");
        } else {
            @unlink($img1);
        }
    
    // duplicate-renaming function contributed by Gary Lawrence Murphy
    ?>
    <p><strong>Duplicate File?</strong></p>
    <p><b><em>The filename '<?php echo $img1_name; ?>' already exists!</em></b></p>
    <p> filename '<?php echo $img1; ?>' moved to '<?php echo "$pathtofile2 - $img2_name"; ?>'</p>
    <p>Confirm or rename:</p>
    <form action="upload.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo  get_settings('fileupload_maxk') *1024 ?>" />
    <input type="hidden" name="img1_type" value="<?php echo $img1_type;?>" />
    <input type="hidden" name="img1_name" value="<?php echo $img2_name;?>" />
    <input type="hidden" name="img1_size" value="<?php echo $img1_size;?>" />
    <input type="hidden" name="img1" value="<?php echo $pathtofile2;?>" />
    <input type="hidden" name="thumbsize" value="<?php echo $_REQUEST['thumbsize'];?>" />
    <input type="hidden" name="imgthumbsizecustom" value="<?php echo $_REQUEST['imgthumbsizecustom'];?>" />
    Alternate name:<br /><input type="text" name="imgalt" size="30" class="uploadform" value="<?php echo $img2_name;?>" /><br />
    <br />
    Description:<br /><input type="text" name="imgdesc" size="30" class="uploadform" value="<?php echo $imgdesc;?>" />
    <br />
    <input type="submit" name="submit" value="Rename" class="search" />
    </form>
</div>
<?php 
require('admin-footer.php');
die();

    }

    if (!strlen($imgalt)) {
        @$moved = move_uploaded_file($img1, $pathtofile); //Path to your images directory, chmod the dir to 777
        // move_uploaded_file() can fail if open_basedir in PHP.INI doesn't
        // include your tmp directory. Try copy instead?
        if(!moved) {
            $moved = copy($img1, $pathtofile);
        }
        // Still couldn't get it. Give up.
        if (!moved) {
            die("Couldn't Upload Your File to $pathtofile.");
        } else {
            @unlink($img1);
        }
        
    } else {
        rename($img1, $pathtofile)
        or die("Couldn't Upload Your File to $pathtofile.");
    }
    
    if($HTTP_POST_VARS['thumbsize'] != 'none' ) {
        if($HTTP_POST_VARS['thumbsize'] == 'small') {
            $max_side = 200;
        }
        elseif($HTTP_POST_VARS['thumbsize'] == 'large') {
            $max_side = 400;
        }
        elseif($HTTP_POST_VARS['thumbsize'] == 'custom') {
            $max_side = $HTTP_POST_VARS['imgthumbsizecustom'];
        }
        
        $result = wp_create_thumbnail($pathtofile, $max_side, NULL);
        if($result != 1) {
            print $result;
        }
    }



if ( ereg('image/',$img1_type)) {
    $piece_of_code = "&lt;img src=&quot;". get_settings('fileupload_url') ."/$img1_name&quot; alt=&quot;$imgdesc&quot; /&gt;";
} else {
    $piece_of_code = "&lt;a href=&quot;". get_settings('fileupload_url') . "/$img1_name&quot; title=&quot;$imgdesc&quot; /&gt;$imgdesc&lt;/a&gt;";
};

?>

<h3>File uploaded!</h3>
<p>Your file <code><?php echo $img1_name; ?></code> was uploaded successfully !</p>
<p>Here&#8217;s the code to display it:</p>
<p><code><?php echo $piece_of_code; ?></code>
</p>
<p><strong>Image Details</strong>: <br />
Name:
<?php echo $img1_name; ?>
<br />
Size:
<?php echo round($img1_size / 1024, 2); ?> <abbr title="Kilobyte">KB</abbr><br />
Type:
<?php echo $img1_type; ?>
</p>
</div>
<p><a href="upload.php">Start over</a>.</p>
<?php
break;
}
include('admin-footer.php');
?>