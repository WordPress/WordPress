<?php
$title = 'Discussion Options';

function add_magic_quotes($array) {
	foreach ($array as $k => $v) {
		if (is_array($v)) {
			$array[$k] = add_magic_quotes($v);
		} else {
			$array[$k] = addslashes($v);
		}
	}
	return $array;
}

if (!get_magic_quotes_gpc()) {
	$HTTP_GET_VARS    = add_magic_quotes($HTTP_GET_VARS);
	$HTTP_POST_VARS   = add_magic_quotes($HTTP_POST_VARS);
	$HTTP_COOKIE_VARS = add_magic_quotes($HTTP_COOKIE_VARS);
}

$wpvarstoreset = array('action','standalone', 'option_group_id');
for ($i=0; $i<count($wpvarstoreset); $i += 1) {
	$wpvar = $wpvarstoreset[$i];
	if (!isset($$wpvar)) {
		if (empty($HTTP_POST_VARS["$wpvar"])) {
			if (empty($HTTP_GET_VARS["$wpvar"])) {
				$$wpvar = '';
			} else {
				$$wpvar = $HTTP_GET_VARS["$wpvar"];
			}
		} else {
			$$wpvar = $HTTP_POST_VARS["$wpvar"];
		}
	}
}

require_once('optionhandler.php');


$standalone = 0;
include_once('admin-header.php');
if ($user_level <= 3) {
	die("You have do not have sufficient permissions to edit the options for this blog.");
}
?>
 <ul id="adminmenu2"> 
	<li><a href="options-general.php">General</a></li> 
	<li><a href="options-writing.php">Writing</a></li> 
	<li><a class="current">Reading</a></li> 
	<li><a href="options-discussion.php">Discussion</a></li> 
	<?php
    //we need to iterate through the available option groups.
    $option_groups = $wpdb->get_results("SELECT group_id, group_name, group_desc, group_longdesc FROM $tableoptiongroups ORDER BY group_id");
    foreach ($option_groups as $option_group) {
        if ($option_group->group_id == $option_group_id) {
            $current_desc=$option_group->group_desc;
            $current_long_desc = $option_group->group_longdesc;
            echo("  <li><a id=\"current2\" href=\"options.php?option_group_id={$option_group->group_id}\" title=\"{$option_group->group_desc}\">{$option_group->group_name}</a></li>\n");
        } else {
            echo("  <li><a href=\"options.php?option_group_id={$option_group->group_id}\" title=\"{$option_group->group_desc}\">{$option_group->group_name}</a></li>\n");
        }
    } // end for each group
?> 
	<li class="last"><a href="options-permalink.php">Permalinks</a></li> 
</ul> 
<br clear="all" /> 
<div class="wrap"> 
	<h2>Reading Options</h2> 
	<form name="form1" method="post" action="options.php"> 
		<input type="hidden" name="action" value="update" /> 
		<input type="hidden" name="page_options" value="'posts_per_page','what_to_show','rss_use_excerpt','blog_charset','gzipcompression' " /> 
		<fieldset> 
		<legend>Front Page</legend> 
		<table width="100%" cellspacing="2" cellpadding="5" class="editform"> 
			<tr valign="top"> 
				<th width="33%" scope="row">Show the most recent:</th> 
				<td><input name="posts_per_page" type="text" id="posts_per_page" value="<?php echo get_settings('posts_per_page'); ?>" size="3" /> 
					<select name="what_to_show" id="what_to_show" > 
						<option value="days" <?php selected('days', get_settings('what_to_show')); ?>>days</option> 
						<option value="posts" <?php selected('posts', get_settings('what_to_show')); ?>>posts</option> 
						<option value="paged" <?php selected('paged', get_settings('what_to_show')); ?>>posts paged</option> 
					</select> </td> 
			</tr> 
		</table> 
		</fieldset> 

		<fieldset> 
		<legend>Syndication Feeds</legend> 
		<table width="100%" cellspacing="2" cellpadding="5" class="editform"> 
			<tr valign="top"> 
				<th width="33%" scope="row">Show the most recent:</th> 
				<td><input name="posts_per_rss" type="text" id="posts_per_rss" value="<?php echo get_settings('posts_per_rss'); ?>" size="3" /> 
					posts </td> 
			</tr>
			<tr valign="top">
				<th scope="row"> For each article, show: </th>
				<td><label>
					<input name="rss_use_excerpt"  type="radio" value="0" <?php checked(0, get_settings('rss_use_excerpt')); ?>  />
					full text</label>					<br>
					<label>
					<input name="rss_use_excerpt" type="radio" value="1" <?php checked(1, get_settings('rss_use_excerpt')); ?> />
					summary</label> </td>
			</tr> 
		</table> 
		</fieldset> 
				<table width="100%" cellspacing="2" cellpadding="5" class="editform"> 
			<tr valign="top"> 
				<th width="33%" scope="row"> Encoding for pages and feeds:</th> 
				<td><input name="blog_charset" type="text" id="blog_charset" value="<?php echo get_settings('blog_charset'); ?>" size="20" class="code" />
                    <br />
The character encoding you write your blog in (UTF-8 recommended<a href="http://developer.apple.com/documentation/macos8/TextIntlSvcs/TextEncodingConversionManager/TEC1.5/TEC.b0.html"></a>)</td> 
			</tr>
		</table> 
		<p>
			<label>
			<input type="checkbox" name="gzipcompression" value="1" <?php checked('1', get_settings('gzipcompression')); ?> /> 
			WordPress should compress articles (gzip) if browsers ask for them</label>
		</p>
		<p style="text-align: right;"> 
			<input type="submit" name="Submit" value="Update Options" /> 
		</p> 
	</form> 
</div> 
<?php include("admin-footer.php") ?>
