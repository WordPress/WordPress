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
	case 0: {
		include_once('admin-header.php');
		if ( !current_user_can('manage_links') )
			wp_die(__('Cheatin&#8217; uh?'));

		$opmltype = 'blogrolling'; // default.
?>

<div class="wrap">

<h2><?php _e('Import your blogroll from another system') ?> </h2>
<form enctype="multipart/form-data" action="link-import.php" method="post" name="blogroll">
<?php wp_nonce_field('import-bookmarks') ?>

<p><?php _e('If a program or website you use allows you to export your links or subscriptions as OPML you may import them here.'); ?></p>
<div style="width: 70%; margin: auto; height: 8em;">
<input type="hidden" name="step" value="1" />
<input type="hidden" name="MAX_FILE_SIZE" value="30000" />
<div style="width: 48%; float: left;">
<h3><?php _e('Specify an OPML URL:'); ?></h3>
<input type="text" name="opml_url" size="50" style="width: 90%;" value="http://" />
</div>

<div style="width: 48%; float: left;">
<h3><?php _e('Or choose from your local disk:'); ?></h3>
<input id="userfile" name="userfile" type="file" size="30" />
</div>

</div>

<p style="clear: both; margin-top: 1em;"><?php _e('Now select a category you want to put these links in.') ?><br />
<?php _e('Category:') ?> <select name="cat_id">
<?php
$categories = get_categories('hide_empty=0');
foreach ($categories as $category) {
?>
<option value="<?php echo $category->cat_ID; ?>"><?php echo wp_specialchars($category->cat_name); ?></option>
<?php
} // end foreach
?>
</select></p>

<p class="submit"><input type="submit" name="submit" value="<?php _e('Import OPML File &raquo;') ?>" /></p>
</form>

</div>
<?php
		break;
	} // end case 0

	case 1: {
		check_admin_referer('import-bookmarks');

		include_once('admin-header.php');
		if ( !current_user_can('manage_links') )
			wp_die(__('Cheatin&#8217; uh?'));
?>
<div class="wrap">

<h2><?php _e('Importing...') ?></h2>
<?php
		$cat_id = $_POST['cat_id'];
		if ( $cat_id == '' || $cat_id == 0 )
			$cat_id  = 1;

		$opml_url = $_POST['opml_url'];
		if ( isset($opml_url) && $opml_url != '' && $opml_url != 'http://' ) {
			$blogrolling = true;
		} else { // try to get the upload file.
			$overrides = array('test_form' => false, 'test_type' => false);
			$file = wp_handle_upload($_FILES['userfile'], $overrides);

			if ( isset($file['error']) )
				wp_die($file['error']);

			$url = $file['url'];
			$opml_url = $file['file'];
			$blogrolling = false;
		}

		if ( isset($opml_url) && $opml_url != '' ) {
			$opml = wp_remote_fopen($opml_url);
			include_once('link-parse-opml.php');

			$link_count = count($names);
			for ( $i = 0; $i < $link_count; $i++ ) {
				if ('Last' == substr($titles[$i], 0, 4))
					$titles[$i] = '';
				if ( 'http' == substr($titles[$i], 0, 4) )
					$titles[$i] = '';
				$link = array( 'link_url' => $urls[$i], 'link_name' => $wpdb->escape($names[$i]), 'link_category' => array($cat_id), 'link_description' => $wpdb->escape($descriptions[$i]), 'link_owner' => $user_ID, 'link_rss' => $feeds[$i]);
				wp_insert_link($link);
				echo sprintf('<p>'.__('Inserted <strong>%s</strong>').'</p>', $names[$i]);
			}
?>

<p><?php printf(__('Inserted %1$d links into category %2$s. All done! Go <a href="%3$s">manage those links</a>.'), $link_count, $cat_id, 'link-manager.php') ?></p>

<?php
} // end if got url
else
{
	echo "<p>" . __("You need to supply your OPML url. Press back on your browser and try again") . "</p>\n";
} // end else

if ( ! $blogrolling )
	@unlink($opml_url);
?>
</div>
<?php
		break;
	} // end case 1
} // end switch

include('admin-footer.php');

?>