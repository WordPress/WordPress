<?php
// Links
// Copyright (C) 2002 Mike Little -- mike@zed1.com

require_once('../wp-config.php');

$parent_file = 'link-manager.php';
$title = 'Import Blogroll';
$this_file = 'link-import.php';

$step = $HTTP_POST_VARS['step'];
if (!$step) $step = 0;
?>
<?php
switch ($step) {
    case 0:
    {
        $standalone = 0;
        include_once('admin-header.php');
        if ($user_level < get_settings('links_minadminlevel'))
            die ("Cheatin&#8217; uh?");

        $opmltype = 'blogrolling'; // default.
?>

<ul id="adminmenu2">
	<li><a href="link-manager.php" >Manage Links</a></li>
	<li><a href="link-add.php">Add Link</a></li>
	<li><a href="link-categories.php">Link Categories</a></li>
	<li class="last"><a href="link-import.php"  class="current">Import Blogroll</a></li>
</ul>

<div class="wrap">

    <h3>Import your blogroll from another system </h3>
	<!-- <form name="blogroll" action="link-import.php" method="get"> -->
	<form enctype="multipart/form-data" action="link-import.php" method="post" name="blogroll">

	<ol>
    <li>Go to <a href="http://www.blogrolling.com">Blogrolling.com</a>
    and sign in. Once you've done that, click on <strong>Get Code</strong>, and then
    look for the <strong><abbr title="Outline Processor Markup Language">OPML</abbr>
    code</strong><?php echo gethelp_link($this_file,'opml_code');?>.</li>
    <li>Or go to <a href="http://blo.gs">Blo.gs</a> and sign in. Once you've done
    that in the 'Welcome Back' box on the right, click on <strong>share</strong>, and then
    look for the <strong><abbr title="Outline Processor Markup Language">OPML</abbr>
    link</strong> (favorites.opml)<?php echo gethelp_link($this_file,'opml_code');?>.</li>
    <li>Select that text and copy it or copy the link/shortcut into the box below.<br />
       <input type="hidden" name="step" value="1" />
       Your OPML URL:<?php echo gethelp_link($this_file,'opml_code');?> <input type="text" name="opml_url" size="65" />
	</li>
    <li>
	   <strong>or</strong> you can upload an OPML file from your desktop aggregator:<br />
       <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
       <label>Upload this file: <input name="userfile" type="file" /></label>
    </li>

    <li>Now select a category you want to put these links in.<br />
	Category: <?php echo gethelp_link($this_file,'link_category');?><select name="cat_id">
<?php
	$categories = $wpdb->get_results("SELECT cat_id, cat_name, auto_toggle FROM $tablelinkcategories ORDER BY cat_id");
	foreach ($categories as $category) {
?>
    <option value="<?php echo $category->cat_id; ?>"><?php echo $category->cat_id.': '.$category->cat_name; ?></option>
<?php
        } // end foreach
?>
    </select>

	</li>

    <li><input type="submit" name="submit" value="Import!" /><?php echo gethelp_link($this_file,'import');?></li>
	</ol>
    </form>

</div>
<?php
                break;
            } // end case 0

    case 1: {
                $standalone = 0;
                include_once('admin-header.php');
                if ($user_level < get_settings('links_minadminlevel'))
                    die ("Cheatin' uh ?");
?>
<div class="wrap">

     <h3>Importing...</h3>
<?php
                $cat_id = $HTTP_POST_VARS['cat_id'];
                if (($cat_id == '') || ($cat_id == 0)) {
                    $cat_id  = 1;
                }

                $opml_url = $HTTP_POST_VARS['opml_url'];
                if (isset($opml_url) && $opml_url != '') {
					$blogrolling = true;
                }
                else // try to get the upload file.
				{
					$uploaddir = $fileupload_realpath;
					$uploadfile = $uploaddir.'/'.$_FILES['userfile']['name'];

					if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile))
					{
						//echo "Upload successful.<p />";
						$blogrolling = false;
						$opml_url = $uploadfile;
					} else {
						echo "Upload error<p />";
					}
				}

                if (isset($opml_url) && $opml_url != '') {
                    $opml = implode('', file($opml_url));
                    include_once('link-parse-opml.php');

                    $link_count = count($names);
                    for ($i = 0; $i < $link_count; $i++) {
                        if ('Last' == substr($titles[$i], 0, 4))
                            $titles[$i] = '';
                        if ('http' == substr($titles[$i], 0, 4))
                            $titles[$i] = '';
                        $query = "INSERT INTO $tablelinks (link_url, link_name, link_target, link_category, link_description, link_owner)
                                  VALUES('{$urls[$i]}', '".addslashes($names[$i])."', '', $cat_id, '".addslashes($descriptions[$i])."', $user_ID)\n";
                        $result = $wpdb->query($query);
                        echo "<p>Inserted <strong>{$names[$i]}</strong></p>";
                    }
?>
     <p>Inserted <?php echo $link_count ?> links into category <?php echo $cat_id; ?>. All done! Go <a href="link-manager.php">manage those links</a>.</p>
<?php
                } // end if got url
                else
                {
                    echo "<p>You need to supply your OPML url. Press back on your browser and try again</p>\n";
                } // end else

?>
<?php
                break;
            } // end case 1
} // end switch
?>
</div>
</body>
</html>