<?php


// Links
// Copyright (C) 2002, 2003 Mike Little -- mike@zed1.com

require_once ('admin.php');

$title = __('Manage Bookmarks');
$this_file = $parent_file = 'link-manager.php';
$list_js = true;

$wpvarstoreset = array ('action', 'cat_id', 'linkurl', 'name', 'image', 'description', 'visible', 'target', 'category', 'link_id', 'submit', 'order_by', 'links_show_cat_id', 'rating', 'rel', 'notes', 'linkcheck[]');

for ($i = 0; $i < count($wpvarstoreset); $i += 1) {
	$wpvar = $wpvarstoreset[$i];
	if (!isset ($$wpvar)) {
		if (empty ($_POST["$wpvar"])) {
			if (empty ($_GET["$wpvar"])) {
				$$wpvar = '';
			} else {
				$$wpvar = $_GET["$wpvar"];
			}
		} else {
			$$wpvar = $_POST["$wpvar"];
		}
	}
}

if (empty ($cat_id))
	$cat_id = 'all';

if (empty ($order_by))
	$order_by = 'order_name';

$title = __('Manage Bookmarks');
include_once ("./admin-header.php");

if (!current_user_can('manage_links'))
	die(__("You do not have sufficient permissions to edit the bookmarks for this blog."));

switch ($order_by) {
	case 'order_id' :
		$sqlorderby = 'id';
		break;
	case 'order_url' :
		$sqlorderby = 'url';
		break;
	case 'order_desc' :
		$sqlorderby = 'description';
		break;
	case 'order_owner' :
		$sqlorderby = 'owner';
		break;
	case 'order_rating' :
		$sqlorderby = 'rating';
		break;
	case 'order_name' :
	default :
		$sqlorderby = 'name';
		break;
}
?>
<script type="text/javascript">
<!--
function checkAll(form)
{
	for (i = 0, n = form.elements.length; i < n; i++) {
		if(form.elements[i].type == "checkbox") {
			if(form.elements[i].checked == true)
				form.elements[i].checked = false;
			else
				form.elements[i].checked = true;
		}
	}
}
//-->
</script>

<?php
if ( isset($_GET['deleted']) ) {
	echo '<div style="background-color: rgb(207, 235, 247);" id="message" class="updated fade"><p>';
	$deleted = (int) $_GET['deleted'];
	printf(__('%s bookmarks deleted.'), $deleted);
	echo '</p></div>';
}
?>

<div class="wrap">
<h2><?php _e('Bookmark Management'); ?></h2>
<form name="cats" method="post" action="">
<table width="75%" cellpadding="3" cellspacing="3">
	<tr>
		<td>
			<?php _e('<strong>Show</strong> bookmarks in category:'); ?><br />
		</td>
		<td>
			<?php _e('<strong>Order</strong> by:');?>
		</td>
		<td>&nbsp;</td>
      </tr>
      <tr>
        <td>
		<?php $categories = get_categories("hide_empty=1&type=link"); ?>
		<select name="cat_id">
			<option value="all" <?php echo ($cat_id == 'all') ? " selected='selected'" : ''; ?>><?php _e('All') ?></option>
			<?php foreach ($categories as $cat): ?>
			<option value="<?php echo $cat->cat_ID; ?>"<?php echo ($cat->cat_ID == $cat_id) ? " selected='selected'" : ''; ?>><?php echo wp_specialchars($cat->cat_name); ?>
			</option>
			<?php endforeach; ?>
			</select>
		</td>
		<td>
			<select name="order_by">
				<option value="order_id"     <?php if ($order_by == 'order_id')     echo " selected='selected'";?>><?php _e('Bookmark ID') ?></option>
				<option value="order_name"   <?php if ($order_by == 'order_name')   echo " selected='selected'";?>><?php _e('Name') ?></option>
				<option value="order_url"    <?php if ($order_by == 'order_url')    echo " selected='selected'";?>><?php _e('URI') ?></option>
				<option value="order_desc"   <?php if ($order_by == 'order_desc')   echo " selected='selected'";?>><?php _e('Description') ?></option>
			</select>
		</td>
		<td>
			<input type="submit" name="action" value="<?php _e('Show') ?>" />
		</td>
	</tr>
</table>
</form>

<form name="links" id="links" method="post" action="link.php">
<input type="hidden" name="link_id" value="" />
<input type="hidden" name="action" value="" />
<input type="hidden" name="order_by" value="<?php echo wp_specialchars($order_by, 1); ?>" />
<input type="hidden" name="cat_id" value="<?php echo (int) $cat_id ?>" />
<table id="the-list-x" width="100%" cellpadding="3" cellspacing="3">
	<tr>
		<th width="15%"><?php _e('Name') ?></th>
		<th><?php _e('URI') ?></th>
		<th><?php _e('Categories') ?></th>
		<th><?php _e('rel') ?></th>
		<th><?php _e('Visible') ?></th>
		<th colspan="2"><?php _e('Action') ?></th>
		<th>&nbsp;</th>
	</tr>
<?php
if ( 'all' == $cat_id )
	$cat_id = '';
$links = get_linkz("category=$cat_id&hide_invisible=0&orderby=$sqlorderby&hide_empty=0");
if ($links)
	foreach ($links as $link) {
		$link->link_name = wp_specialchars($link->link_name);
		$link->link_description = wp_specialchars($link->link_description);
		$link->link_url = wp_specialchars($link->link_url);
		$link->link_category = wp_get_link_cats($link->link_id);
		$short_url = str_replace('http://', '', $link->link_url);
		$short_url = str_replace('www.', '', $short_url);
		if ('/' == substr($short_url, -1))
			$short_url = substr($short_url, 0, -1);
		if (strlen($short_url) > 35)
			$short_url = substr($short_url, 0, 32).'...';

		$visible = ($link->link_visible == 'Y') ? __('Yes') : __('No');
		++ $i;
		$style = ($i % 2) ? '' : ' class="alternate"';
?>
	<tr id="link-<?php echo $link->link_id; ?>" valign="middle" <?php echo $style; ?>>
		<td><strong><?php echo $link->link_name; ?></strong><br />
		<?php


		echo $link->link_description . "</td>";
		echo "<td><a href=\"$link->link_url\" title=\"".sprintf(__('Visit %s'), $link->link_name)."\">$short_url</a></td>";
		?>
        <td>
        <?php

		$cat_names = array();
		foreach ($link->link_category as $category) {
			$cat_name = get_the_category_by_ID($category);
			$cat_names[] = wp_specialchars($cat_name);
		}
		echo implode(', ', $cat_names);
		?>
		</td>
        <td><?php echo $link->link_rel; ?></td>
        <td align='center'><?php echo $visible; ?></td>
<?php

		echo '<td><a href="link.php?link_id='.$link->link_id.'&amp;action=edit" class="edit">'.__('Edit').'</a></td>';
		echo '<td><a href="link.php?link_id='.$link->link_id.'&amp;action=delete"'." class='delete' onclick=\"return deleteSomething( 'link', $link->link_id , '".sprintf(__("You are about to delete the &quot;%s&quot; bookmark to %s.\\n&quot;Cancel&quot; to stop, &quot;OK&quot; to delete."), wp_specialchars($link->link_name, 1), wp_specialchars($link->link_url)).'\' );" class="delete">'.__('Delete').'</a></td>';
		echo '<td><input type="checkbox" name="linkcheck[]" value="'.$link->link_id.'" /></td>';
		echo "\n    </tr>\n";
	}
?>
</table>

<div id="ajax-response"></div>

<table width="100%" cellpadding="3" cellspacing="3">
	<tr>
		<td>
			<p class="submit"><input type="submit" class="button" name="deletebookmarks" id="deletebookmarks" value="<?php _e('Delete Checked Bookmarks') ?>" onclick="return confirm('<?php _e("You are about to delete these bookmarks permanently \\n  \'Cancel\' to stop, \'OK\' to delete.") ?>')" /></p>
		</td>
		<td align="right">
			<a href="#" onclick="checkAll(document.getElementById('links')); return false; "><?php _e('Toggle Checkboxes') ?></a>
		</td>
	</tr>
</table>
</div>
</form>

<?php include('admin-footer.php'); ?>