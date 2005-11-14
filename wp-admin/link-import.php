<?php
// Links
// Copyright (C) 2002 Mike Little -- mike@zed1.com

require_once('admin.php');
$parent_file = 'link-manager.php';
$title = __('Import Blogroll');
$this_file = 'link-import.php';

$step = $_POST['step'];
if (!$step) $step = 0;
?>
<?php
switch ($step) {
    case 0:
    {
        include_once('admin-header.php');
        if ( !current_user_can('manage_links') )
            die (__("Cheatin&#8217; uh?"));

        $opmltype = 'blogrolling'; // default.
?>

<div class="wrap">
<h2><?php _e('Import your blogroll from another system') ?> </h2>
<form enctype="multipart/form-data" action="link-import.php" method="post" name="blogroll">

<p><?php _e('If a program or website you use allows you to export your links or subscriptions as OPML you may import them here.'); ?>
<div style="width: 70%; margin: auto; height: 8em;">
<input type="hidden" name="step" value="1" />
<input type="hidden" name="MAX_FILE_SIZE" value="30000" />
<div style="width: 48%; float: left;">
<h3><?php _e('Specify an OPML URL:'); ?></h3>
<input type="text" name="opml_url" size="50" style="width: 90%;" value="http://" />
</div>

<div style="width: 48%; float: left;">
<h3><?php _e('Or choose from your local disk:'); ?></h3>
<input name="userfile" type="file" size="30" />
</div>


</div>

<p style="clear: both; margin-top: 1em;"><?php _e('Now select a category you want to put these links in.') ?><br />
<?php _e('Category:') ?> <select name="cat_id">
<?php
$categories = $wpdb->get_results("SELECT cat_id, cat_name, auto_toggle FROM $wpdb->linkcategories ORDER BY cat_id");
foreach ($categories as $category) {
?>
<option value="<?php echo $category->cat_id; ?>"><?php echo $category->cat_id.': '.$category->cat_name; ?></option>
<?php
} // end foreach
?>
</select></p>

<p class="submit"><input type="submit" name="submit" value="<?php _e('Import OPML File') ?> &raquo;" /></p>
</form>

</div>
<?php
                break;
            } // end case 0

    case 1: {
                include_once('admin-header.php');
                if ( !current_user_can('manage_links') )
                    die (__("Cheatin' uh ?"));
?>
<div class="wrap">

     <h2><?php _e('Importing...') ?></h2>
<?php
                $cat_id = $_POST['cat_id'];
                if (($cat_id == '') || ($cat_id == 0)) {
                    $cat_id  = 1;
                }

                $opml_url = $_POST['opml_url'];
                if (isset($opml_url) && $opml_url != '') {
					$blogrolling = true;
                }
                else // try to get the upload file.
				{
					$uploaddir = get_settings('fileupload_realpath');
					$uploadfile = $uploaddir.'/'.$_FILES['userfile']['name'];

					if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile))
					{
						//echo "Upload successful.";
						$blogrolling = false;
						$opml_url = $uploadfile;
					} else {
						echo __("Upload error");
					}
				}

                if (isset($opml_url) && $opml_url != '') {
                    $opml = wp_remote_fopen($opml_url);
                    include_once('link-parse-opml.php');

                    $link_count = count($names);
                    for ($i = 0; $i < $link_count; $i++) {
                        if ('Last' == substr($titles[$i], 0, 4))
                            $titles[$i] = '';
                        if ('http' == substr($titles[$i], 0, 4))
                            $titles[$i] = '';
                        $query = "INSERT INTO $wpdb->links (link_url, link_name, link_target, link_category, link_description, link_owner, link_rss)
                                VALUES('{$urls[$i]}', '".$wpdb->escape($names[$i])."', '', $cat_id, '".$wpdb->escape($descriptions[$i])."', $user_ID, '{$feeds[$i]}')\n";
                        $result = $wpdb->query($query);
                        echo sprintf(__("<p>Inserted <strong>%s</strong></p>"), $names[$i]);
                    }
?>
     <p><?php printf(__('Inserted %1$d links into category %2$s. All done! Go <a href="%3$s">manage those links</a>.'), $link_count, $cat_id, 'link-manager.php') ?></p>
<?php
                } // end if got url
                else
                {
                    echo "<p>" . __("You need to supply your OPML url. Press back on your browser and try again") . "</p>\n";
                } // end else

?>
</div>
<?php
                break;
            } // end case 1
} // end switch
?>
</body>
</html>
