<?php
// Links
// Copyright (C) 2002, 2003 Mike Little -- mike@zed1.com
require_once('../wp-includes/wp-l10n.php');
$title = __('Link Categories');
$this_file='link-categories.php';
$parent_file = 'link-manager.php';

$wpvarstoreset = array('action','standalone','cat', 'auto_toggle');
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

switch ($action) {
  case 'addcat':
  {
      $standalone = 1;
      include_once('admin-header.php');

      if ($user_level < get_settings('links_minadminlevel'))
          die (__("Cheatin' uh ?"));

      $cat_name = addslashes($_POST['cat_name']);
      $auto_toggle = $_POST['auto_toggle'];
      if ($auto_toggle != 'Y') {
          $auto_toggle = 'N';
      }

      $show_images = $_POST['show_images'];
      if ($show_images != 'Y') {
          $show_images = 'N';
      }

      $show_description = $_POST['show_description'];
      if ($show_description != 'Y') {
          $show_description = 'N';
      }

      $show_rating = $_POST['show_rating'];
      if ($show_rating != 'Y') {
          $show_rating = 'N';
      }

      $show_updated = $_POST['show_updated'];
      if ($show_updated != 'Y') {
          $show_updated = 'N';
      }

      $sort_order = $_POST['sort_order'];

      $sort_desc = $_POST['sort_desc'];
      if ($sort_desc != 'Y') {
          $sort_desc = 'N';
      }
      $text_before_link = addslashes($_POST['text_before_link']);
      $text_after_link = addslashes($_POST['text_after_link']);
      $text_after_all = addslashes($_POST['text_after_all']);

      $list_limit = $_POST['list_limit'];
      if ($list_limit == '')
          $list_limit = -1;

      $wpdb->query("INSERT INTO $wpdb->linkcategories (cat_id, cat_name, auto_toggle, show_images, show_description, \n" .
             " show_rating, show_updated, sort_order, sort_desc, text_before_link, text_after_link, text_after_all, list_limit) \n" .
             " VALUES ('0', '$cat_name', '$auto_toggle', '$show_images', '$show_description', \n" .
             " '$show_rating', '$show_updated', '$sort_order', '$sort_desc', '$text_before_link', '$text_after_link', \n" .
             " '$text_after_all', $list_limit)");

      header('Location: link-categories.php');
    break;
  } // end addcat
  case 'Delete':
  {
    $standalone = 1;
    include_once('admin-header.php');

    $cat_id = $_GET['cat_id'];
    $cat_name=get_linkcatname($cat_id);

    if ($cat_id=="1")
        die(sprintf(__("Can't delete the <strong>%s</strong> link category: this is the default one"), $cat_name));

    if ($user_level < get_settings('links_minadminlevel'))
      die (__("Cheatin' uh ?"));

    $wpdb->query("DELETE FROM $wpdb->linkcategories WHERE cat_id='$cat_id'");
    $wpdb->query("UPDATE $wpdb->links SET link_category=1 WHERE link_category='$cat_id'");

    header('Location: link-categories.php');
    break;
  } // end delete
  case 'Edit':
  {
    include_once ('admin-header.php');
    $cat_id = $_GET['cat_id'];
    $row = $wpdb->get_row("SELECT cat_id, cat_name, auto_toggle, show_images, show_description, "
         . " show_rating, show_updated, sort_order, sort_desc, text_before_link, text_after_link, "
         . " text_after_all, list_limit FROM $wpdb->linkcategories WHERE cat_id=$cat_id");
    if ($row) {
        if ($row->list_limit == -1) {
            $row->list_limit = '';
        }
?>

<ul id="adminmenu2">
	<li><a href="link-manager.php" ><?php _e('Manage Links') ?></a></li>
	<li><a href="link-add.php"><?php _e('Add Link') ?></a></li>
	<li><a href="link-categories.php" class="current"><?php _e('Link Categories') ?></a></li>
	<li class="last"><a href="link-import.php"><?php _e('Import Blogroll') ?></a></li>
</ul>

<div class="wrap">
  <h2>Edit &#8220;<?php echo htmlspecialchars($row->cat_name)?>&#8221; Category </h2>

  <form name="editcat" method="post">
      <input type="hidden" name="action" value="editedcat" />
      <input type="hidden" name="cat_id" value="<?php echo $row->cat_id ?>" />
<fieldset class="options">
<legend><?php _e('Category Options') ?></legend>
<table class="editform" width="100%" cellspacing="2" cellpadding="5">
<tr>
	<th width="33%" scope="row"><?php _e('Name:') ?></th>
	<td width="67%"><input name="cat_name" type="text" value="<?php echo htmlspecialchars($row->cat_name)?>" size="30" /></td>
</tr>
<tr>
	<th scope="row"><?php _e('Show:') ?></th>
        <td>
            <label>
            <input type="checkbox" name="show_images"  value="Y" <?php checked('Y', $row->show_images); ?> /> 
            <?php _e('Image') ?></label> <br />
            <label>
            <input type="checkbox" name="show_description" value="Y" <?php checked('Y', $row->show_description); ?> /> 
            <?php _e('Description') ?></label> 
            <?php _e('(shown in <code>title</code> regardless)') ?><br />
            <label>
            <input type="checkbox" name="show_rating"  value="Y" <?php checked('Y', $row->show_rating); ?> /> 
            <?php _e('Rating') ?></label> <br />
            <label>
            <input type="checkbox" name="show_updated" value="Y" <?php checked('Y', $row->show_updated); ?> /> 
            <?php _e('Updated') ?></label>
<?php _e('(shown in <code>title</code> regardless)') ?></td>
</tr>
<tr>
	<th scope="row"><?php _e('Sort order:') ?></th>
	<td>
	<select name="sort_order" size="1">
            <option value="name" <?php echo ($row->sort_order == 'name') ? 'selected="selected"' : ''?>><?php _e('Name') ?></option>
            <option value="id"      <?php echo ($row->sort_order == 'id') ? 'selected' : ''?>><?php _e('Id') ?></option>
            <option value="url"     <?php echo ($row->sort_order == 'url') ? 'selected' : ''?>><?php _e('URL') ?></option>
            <option value="rating"  <?php echo ($row->sort_order == 'rating') ? 'selected' : ''?>><?php _e('Rating') ?></option>
            <option value="updated" <?php echo ($row->sort_order == 'updated') ? 'selected' : ''?>><?php _e('Updated') ?></option>
            <option value="rand"  <?php echo ($row->sort_order == 'rand') ? 'selected' : ''?>><?php _e('Random') ?></option>
            <option value="length"  <?php echo ($row->sort_order == 'length') ? 'selected' : ''?>><?php _e('Name Length') ?></option>
	</select>
	<label>
	<input type="checkbox" name="sort_desc" value="Y" <?php checked('Y', $row->sort_desc); ?> /> 
	<?php _e('Descending') ?></label>
	</td>
</tr>
<tr>
	<th scope="row"><?php _e('Limit:') ?></th>
	<td>
	<input type="text" name="list_limit" size="5" value="<?php echo $row->list_limit ?>" /> 
	<?php _e('(Leave empty for no limit to number of links shown)') ?>
	</td>
</tr>
<tr>
	<th scope="row"><?php _e('Toggle:') ?></th>
	<td><label>
		<input type="checkbox" name="auto_toggle"  value="Y" <?php checked('Y', $row->auto_toggle); ?> /> 
		<?php _e('When new link is added toggle all others to be invisible') ?></label></td>
</tr>

</table>
</fieldset>
<fieldset class="options">
<legend><?php _e('Formatting') ?></legend>
<table class="editform" width="100%" cellspacing="2" cellpadding="5">
<tr>
	<th width="33%" scope="row"><?php _e('Before Link:') ?></th>
	<td width="67%"><input type="text" name="text_before_link" size="45" value="<?php echo htmlspecialchars($row->text_before_link)?>" /></td>
</tr>
<tr>
<th scope="row"><?php _e('Between Link and Description:') ?></th>
<td><input type="text" name="text_after_link" size="45" value="<?php echo htmlspecialchars($row->text_after_link)?>" /></td>
</tr>
<tr>
<th scope="row"><?php _e('After Link:') ?></th>
<td><input type="text" name="text_after_all" size="45" value="<?php echo htmlspecialchars($row->text_after_all)?>"/></td>
</tr>
</table>
</fieldset>
<p class="submit"><input type="submit" name="submit" value="<?php _e('Save Category Settings &raquo;') ?>" /></p>
</form>

</div>
<?php
    } // end if row
    break;
  } // end Edit
  case "editedcat":
  {
    $standalone = 1;
    include_once("./admin-header.php");

    if ($user_level < get_settings('links_minadminlevel'))
      die (__("Cheatin' uh ?"));

    $submit=$_POST["submit"];
    if (isset($submit)) {

    $cat_id=$_POST["cat_id"];

    $cat_name= $_POST["cat_name"];
    $auto_toggle = $_POST["auto_toggle"];
    if ($auto_toggle != 'Y') {
        $auto_toggle = 'N';
    }

    $show_images = $_POST["show_images"];
    if ($show_images != 'Y') {
        $show_images = 'N';
    }

    $show_description = $_POST["show_description"];
    if ($show_description != 'Y') {
        $show_description = 'N';
    }

    $show_rating = $_POST["show_rating"];
    if ($show_rating != 'Y') {
        $show_rating = 'N';
    }

    $show_updated = $_POST["show_updated"];
    if ($show_updated != 'Y') {
        $show_updated = 'N';
    }

    $sort_order = $_POST["sort_order"];

    $sort_desc = $_POST["sort_desc"];
    if ($sort_desc != 'Y') {
        $sort_desc = 'N';
    }
    $text_before_link = addslashes($_POST["text_before_link"]);
    $text_after_link = addslashes($_POST["text_after_link"]);
    $text_after_all = addslashes($_POST["text_after_all"]);

    $list_limit = $_POST["list_limit"];
    if ($list_limit == '')
        $list_limit = -1;

    $wpdb->query("UPDATE $wpdb->linkcategories set
            cat_name='$cat_name',
            auto_toggle='$auto_toggle',
            show_images='$show_images',
            show_description='$show_description',
            show_rating='$show_rating',
            show_updated='$show_updated',
            sort_order='$sort_order',
            sort_desc='$sort_desc',
            text_before_link='$text_before_link',
            text_after_link='$text_after_link',
            text_after_all='$text_after_all',
            list_limit=$list_limit
            WHERE cat_id=$cat_id
            ");
    } // end if save


    header("Location: link-categories.php");
    break;
  } // end editcat
  default:
  {
    $standalone=0;
    include_once ("./admin-header.php");
    if ($user_level < get_settings('links_minadminlevel')) {
      die(__("You have do not have sufficient permissions to edit the link categories for this blog. :)"));
    }
?>
<ul id="adminmenu2">
	<li><a href="link-manager.php" ><?php _e('Manage Links') ?></a></li>
	<li><a href="link-add.php"><?php _e('Add Link') ?></a></li>
	<li><a href="link-categories.php" class="current"><?php _e('Link Categories') ?></a></li>
	<li class="last"><a href="link-import.php"><?php _e('Import Blogroll') ?></a></li>
</ul>
<div class="wrap">
            <h2><?php _e('Link Categories:') ?></h2>
            <table width="100%" cellpadding="5" cellspacing="0" border="0">
              <tr>
 	        <th rowspan="2" valign="bottom"><?php _e('Name') ?></th>
                <th rowspan="2" valign="bottom"><?php _e('ID') ?></th>
                <th rowspan="2" valign="bottom"><?php _e('Toggle?') ?></th>
                <th colspan="4" valign="bottom"><?php _e('Show') ?></th>
                <th rowspan="2" valign="bottom"><?php _e('Sort Order') ?></th>
                <th rowspan="2" valign="bottom"><?php _e('Desc?') ?></th>
                <th colspan="3" valign="bottom"><?php _e('Formatting') ?></th>
                <th rowspan="2" valign="bottom"><?php _e('Limit') ?></th>
                <th rowspan="2" colspan="2">&nbsp;</th>
              </tr>
              <tr>
                <th valign="top"><?php _e('Images') ?></th>
                <th valign="top"><?php _e('Desc.') ?></th>
                <th valign="top"><?php _e('Rating') ?></th>
                <th valign="top"><?php _e('Updated') ?></th>
                <th valign="top"><?php _e('Before') ?></th>
                <th valign="top"><?php _e('Between') ?></th>
                <th valign="top"><?php _e('After') ?></th>
              </tr>
<?php
$results = $wpdb->get_results("SELECT cat_id, cat_name, auto_toggle, show_images, show_description, "
         . " show_rating, show_updated, sort_order, sort_desc, text_before_link, text_after_link, "
         . " text_after_all, list_limit FROM $wpdb->linkcategories ORDER BY cat_id");
$i = 1;
foreach ($results as $row) {
    if ($row->list_limit == -1) {
        $row->list_limit = 'none';
    }
    $style = ($i % 2) ? ' class="alternate"' : '';
?>
              <tr valign="middle" align="center" <?php echo $style ?> style="border-bottom: 1px dotted #9C9A9C;">
                <td><?php echo htmlspecialchars($row->cat_name)?></td>
				<td ><?php echo $row->cat_id?></td>
                <td><?php echo $row->auto_toggle?></td>
                <td><?php echo $row->show_images?></td>
                <td><?php echo $row->show_description?></td>
                <td><?php echo $row->show_rating?></td>
                <td><?php echo $row->show_updated?></td>
                <td><?php echo $row->sort_order?></td>
                <td><?php echo $row->sort_desc?></td>
                <td nowrap="nowrap"><?php echo htmlentities($row->text_before_link)?>&nbsp;</td>
                <td nowrap="nowrap"><?php echo htmlentities($row->text_after_link)?>&nbsp;</td>
                <td nowrap="nowrap"><?php echo htmlentities($row->text_after_all)?></td>
                <td><?php echo $row->list_limit?></td>
                <td><a href="link-categories.php?cat_id=<?php echo $row->cat_id?>&amp;action=Edit" class="edit"><?php _e('Edit') ?></a></td>
                <td><a href="link-categories.php?cat_id=<?php echo $row->cat_id?>&amp;action=Delete" onclick="return confirm('<?php _e("You are about to delete this category.\\n  \'Cancel\' to stop, \'OK\' to delete.") ?>');" class="delete"><?php _e('Delete') ?></a></td>
              </tr>
<?php
        ++$i;
    }
?>
            </table>
<p><?php _e('These are the defaults for when you call a link category with no additional arguments. All of these settings may be overwritten.') ?></p>

</div>

<div class="wrap">
    <form name="addcat" method="post">
      <input type="hidden" name="action" value="addcat" />
	  <h2><?php _e('Add a Link Category:') ?></h2>
<fieldset class="options">
<legend><?php _e('Category Options') ?></legend>
<table class="editform" width="100%" cellspacing="2" cellpadding="5">
<tr>
	<th width="33%" scope="row"><?php _e('Name:') ?></th>
	<td width="67%"><input type="text" name="cat_name" size="30" /></td>
</tr>
<tr>
	<th scope="row"><?php _e('Show:') ?></th>
        <td>
            <label>
            <input type="checkbox" name="show_images"  value="Y" /> 
            <?php _e('Image') ?></label> <br />
            <label>
            <input type="checkbox" name="show_description" value="Y" /> 
            <?php _e('Description') ?></label> 
            <?php _e('(shown in <code>title</code> regardless)') ?><br />
            <label>
            <input type="checkbox" name="show_rating"  value="Y" /> 
            <?php _e('Rating') ?></label> <br />
            <label>
            <input type="checkbox" name="show_updated" value="Y" /> 
            <?php _e('Updated') ?></label>
<?php _e('(shown in <code>title</code> regardless)') ?></td>
</tr>
<tr>
	<th scope="row"><?php _e('Sort order:') ?></th>
	<td>
	<select name="sort_order" size="1">
	<option value="name"><?php _e('Name') ?></option>
	<option value="id"><?php _e('Id') ?></option>
	<option value="url"><?php _e('URL') ?></option>
	<option value="rating"><?php _e('Rating') ?></option>
	<option value="updated"><?php _e('Updated') ?></option>
	<option value="rand"><?php _e('Random') ?></option>
	</select>
	<label>
	<input type="checkbox" name="sort_desc" value="Y" /> 
	<?php _e('Descending') ?></label>
	</td>
</tr>
<tr>
	<th scope="row"><?php _e('Limit:') ?></th>
	<td>
	<input type="text" name="list_limit" size="5" value="" /> <?php _e('(Leave empty for no limit to number of links shown)') ?>
	</td>
</tr>
<tr>
	<th scope="row"><?php _e('Toggle:') ?></th>
	<td><label>
		<input type="checkbox" name="auto_toggle"  value="Y" /> 
		<?php _e('When new link is added toggle all others to be invisible') ?></label></td>
</tr>

</table>
</fieldset>
<fieldset class="options">
<legend><?php _e('Formatting') ?></legend>
<table class="editform" width="100%" cellspacing="2" cellpadding="5">
<tr>
	<th width="33%" scope="row"><?php _e('Before Link:') ?></th>
	<td width="67%"><input type="text" name="text_before_link" size="45" value="&lt;li&gt;" /></td>
</tr>
<tr>
<th scope="row"><?php _e('Between Link and Description:') ?></th>
<td><input type="text" name="text_after_link" size="45" value="&lt;br /&gt;" /></td>
</tr>
<tr>
<th scope="row"><?php _e('After Link:') ?></th>
<td><input type="text" name="text_after_all" size="45" value="&lt;/li&gt;"/></td>
</tr>
</table>
</fieldset>
<p class="submit"><input type="submit" name="submit" value="<?php _e('Add Category &raquo;') ?>" /></p>
  </form>
</div>
<div class="wrap">
    <h3><?php _e('Note:') ?></h3>
    <?php printf(__('<p>Deleting a link category does not delete links from that category.<br />
    It will just set them back to the default category <b>%s</b>.'), get_linkcatname(1)) ?>
    </p>
</div>
<?php
    break;
  } // end default
} // end case
?>
<?php include('admin-footer.php'); ?>
