<?php
require_once('admin.php');

$title = __('Reading Options');
$parent_file = 'options-general.php';

include('admin-header.php');
?>

<div class="wrap">
<h2><?php _e('Reading Options') ?></h2>
<form name="form1" method="post" action="options.php">
<?php wp_nonce_field('update-options') ?>
<p class="submit"><input type="submit" name="Submit" value="<?php _e('Update Options &raquo;') ?>" /></p>
<?php if ( get_pages() ): ?>
<fieldset class="options">
<legend><?php _e('Front Page') ?></legend>
<table class="niceblue">
<tr valign="top">
<th width="33%" scope="row"><?php _e('Front page displays:')?></th>
<td>
	<p><label>
		<input name="show_on_front" type="radio" value="posts" class="tog" <?php checked('posts', get_option('show_on_front')); ?> />
		<?php _e('Your latest posts'); ?>
	</label>
	</p>
	<p><label>
		<input name="show_on_front" type="radio" value="page" class="tog" <?php checked('page', get_option('show_on_front')); ?> />
		<?php printf(__('A <a href="%s">static page</a> (select below)'), 'edit-pages.php'); ?>
	</label>
	</p>
<ul>
	<li><?php printf(__('Front page: %s'), wp_dropdown_pages("name=page_on_front&echo=0&show_option_none=".__('- Select -')."&selected=" . get_option('page_on_front'))); ?></li>
	<li><?php printf(__('Posts page: %s'), wp_dropdown_pages("name=page_for_posts&echo=0&show_option_none=".__('- Select -')."&selected=" . get_option('page_for_posts'))); ?></li>
</ul>
<?php if ( 'page' == get_option('show_on_front') && get_option('page_for_posts') == get_option('page_on_front') ) : ?>
<div id="front-page-warning" class="updated fade-ff0000">
	<p>
		<?php _e('<strong>Warning:</strong> these pages should not be the same!'); ?>
	</p>
</div>
<?php endif; ?>
</td>
</tr>
</table>
</fieldset>
<?php endif; ?>

<fieldset class="options">
<legend><?php _e('Blog Pages') ?></legend>
<table class="niceblue">
<tr valign="top">
<th width="33%" scope="row"><?php _e('Show at most:') ?></th>
<td>
<input name="posts_per_page" type="text" id="posts_per_page" value="<?php form_option('posts_per_page'); ?>" size="3" /> <?php _e('posts') ?>
</td>
</tr>
</table>
</fieldset>

<fieldset class="options">
<legend><?php _e('Syndication Feeds') ?></legend>
<table width="100%" cellspacing="2" cellpadding="5" class="optiontable editform">
<tr valign="top">
<th width="33%" scope="row"><?php _e('Show the most recent:') ?></th>
<td><input name="posts_per_rss" type="text" id="posts_per_rss" value="<?php form_option('posts_per_rss'); ?>" size="3" /> <?php _e('posts') ?></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('For each article, show:') ?> </th>
<td>
<p><label><input name="rss_use_excerpt"  type="radio" value="0" <?php checked(0, get_option('rss_use_excerpt')); ?>	/> <?php _e('Full text') ?></label><br />
<label><input name="rss_use_excerpt" type="radio" value="1" <?php checked(1, get_option('rss_use_excerpt')); ?> /> <?php _e('Summary') ?></label></p>
</td>
</tr>
</table>
</fieldset>

<fieldset class="options">
<legend><?php _e('Avatars') ?></legend>
<table class="niceblue">
<tr valign="top">
<th width="33%" scope="row"><?php _e('Show Avatars?') ?></th>
<td>
<select name="show_avatars" id="show_avatars">
<?php
	$yesorno = array(0 => __("Don't show Avatars"), 1 => __('Show Avatars'));
	foreach ( $yesorno as $key => $value) {
		$selected = (get_option('show_avatars') == $key) ? 'selected="selected"' : '';
		echo "\n\t<option value='$key' $selected>$value</option>";
	}
?>
</select>
</td>
</tr>
<tr valign="top">
<th width="33%" scope="row"><?php _e('Show Avatars with Rating:') ?></th>
<td>
<select name="avatar_rating" id="avatar_rating">
<?php
$ratings = array( 'G' => _c('G|rating'), 'PG' => _c('PG|Rating'), 'R' => _c('R|Rating'), 'X' => _c('X|Rating'));
foreach ($ratings as $key => $rating) :
	$selected = (get_option('avatar_rating') == $key) ? 'selected="selected"' : '';
	echo "\n\t<option value='$key' $selected>$rating</option>";
endforeach;
?>
</select>
</td>
</tr>
</table>
</fieldset>

<table class="niceblue">
<tr valign="top">
<th width="33%" scope="row"><?php _e('Encoding for pages and feeds:') ?></th>
<td><input name="blog_charset" type="text" id="blog_charset" value="<?php form_option('blog_charset'); ?>" size="20" class="code" /><br />
<?php _e('The character encoding you write your blog in (UTF-8 is <a href="http://developer.apple.com/documentation/macos8/TextIntlSvcs/TextEncodingConversionManager/TEC1.5/TEC.b0.html">recommended</a>)') ?></td>
</tr>
</table>
<p>
<label><input type="checkbox" name="gzipcompression" value="1" <?php checked('1', get_option('gzipcompression')); ?> />
<?php _e('WordPress should compress articles (gzip) if browsers ask for them') ?></label>
</p>
<p class="submit">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="posts_per_page,posts_per_rss,rss_use_excerpt,blog_charset,gzipcompression,show_on_front,page_on_front,page_for_posts,show_avatars,avatar_rating" />
<input type="submit" name="Submit" value="<?php _e('Update Options &raquo;') ?>" />
</p>
</form>
</div>
<?php include('./admin-footer.php'); ?>
