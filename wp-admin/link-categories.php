<?php
// Links
// Copyright (C) 2002, 2003 Mike Little -- mike@zed1.com

$title = 'Link Categories';
$this_file='link-categories.php';
$parent_file = 'link-manager.php';

$wpvarstoreset = array('action','standalone','cat', 'auto_toggle');
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

switch ($action) {
  case 'addcat':
  {
      $standalone = 1;
      include_once('admin-header.php');

      if ($user_level < get_settings('links_minadminlevel'))
          die ("Cheatin' uh ?");

      $cat_name = addslashes($HTTP_POST_VARS['cat_name']);
      $auto_toggle = $HTTP_POST_VARS['auto_toggle'];
      if ($auto_toggle != 'Y') {
          $auto_toggle = 'N';
      }

      $show_images = $HTTP_POST_VARS['show_images'];
      if ($show_images != 'Y') {
          $show_images = 'N';
      }

      $show_description = $HTTP_POST_VARS['show_description'];
      if ($show_description != 'Y') {
          $show_description = 'N';
      }

      $show_rating = $HTTP_POST_VARS['show_rating'];
      if ($show_rating != 'Y') {
          $show_rating = 'N';
      }

      $show_updated = $HTTP_POST_VARS['show_updated'];
      if ($show_updated != 'Y') {
          $show_updated = 'N';
      }

      $sort_order = $HTTP_POST_VARS['sort_order'];

      $sort_desc = $HTTP_POST_VARS['sort_desc'];
      if ($sort_desc != 'Y') {
          $sort_desc = 'N';
      }
      $text_before_link = addslashes($HTTP_POST_VARS['text_before_link']);
      $text_after_link = addslashes($HTTP_POST_VARS['text_after_link']);
      $text_after_all = addslashes($HTTP_POST_VARS['text_after_all']);

      $list_limit = $HTTP_POST_VARS['list_limit'];
      if ($list_limit == '')
          $list_limit = -1;

      $wpdb->query("INSERT INTO $tablelinkcategories (cat_id, cat_name, auto_toggle, show_images, show_description, \n" .
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

    $cat_id = $HTTP_GET_VARS['cat_id'];
    $cat_name=get_linkcatname($cat_id);
    $cat_name=addslashes($cat_name);

    if ($cat_id=="1")
        die("Can't delete the <strong>$cat_name</strong> link category: this is the default one");

    if ($user_level < get_settings('links_minadminlevel'))
    die ("Cheatin' uh ?");

    $wpdb->query("DELETE FROM $tablelinkcategories WHERE cat_id='$cat_id'");
    $wpdb->query("UPDATE $tablelinks SET link_category=1 WHERE link_category='$cat_id'");

    header('Location: link-categories.php');
    break;
  } // end delete
  case 'Edit':
  {
    include_once ('admin-header.php');
    $cat_id = $HTTP_GET_VARS['cat_id'];
    $row = $wpdb->get_row("SELECT cat_id, cat_name, auto_toggle, show_images, show_description, "
         . " show_rating, show_updated, sort_order, sort_desc, text_before_link, text_after_link, "
         . " text_after_all, list_limit FROM $tablelinkcategories WHERE cat_id=$cat_id");
    if ($row) {
        if ($row->list_limit == -1) {
            $row->list_limit = '';
        }
?>

<ul id="adminmenu2">
	<li><a href="link-manager.php" >Manage Links</a></li>
	<li><a href="link-add.php">Add Link</a></li>
	<li><a href="link-categories.php" class="current">Link Categories</a></li>
	<li class="last"><a href="link-import.php">Import Blogroll</a></li>
</ul>

<div class="wrap">
  <h3>Edit Link Category &#8220;<?php echo $row->cat_name?>&#8221;</h3>
  <p>
  <form name="editcat" method="post">
      <input type="hidden" name="action" value="editedcat" />
      <input type="hidden" name="cat_id" value="<?php echo $row->cat_id ?>" />
    <table border="0">
      <tr>
        <td align="right">Name:</td>
        <td><input type="text" name="cat_name" size="25" value="<?php echo stripslashes($row->cat_name)?>" />&nbsp;&nbsp;&nbsp;
            <label for="auto_toggle">
<input type="checkbox" name="auto_toggle" id="auto_toggle" <?php echo ($row->auto_toggle == 'Y') ? 'checked' : '';?> value="Y" />
auto-toggle?</label></td>
      </tr>
      <tr>
        <td align="right">Show:</td>
        <td>
          <label for="show_images">
          <input type="checkbox" name="show_images" id="show_images"  <?php echo ($row->show_images  == 'Y') ? 'checked' : '';?> value="Y" /> 
          images</label>&nbsp;
          <label for="show_description">
<input type="checkbox" name="show_description" id="show_description" <?php echo ($row->show_description == 'Y') ? 'checked' : '';?> value="Y" />
description</label>          &nbsp;
          <label for="show_rating">
<input type="checkbox" name="show_rating" id="show_rating" <?php echo ($row->show_rating  == 'Y') ? 'checked' : '';?>     value="Y" />
rating</label>          &nbsp;
          <label for="show_updated">
<input type="checkbox" name="show_updated" id="show_updated" <?php echo ($row->show_updated == 'Y') ? 'checked' : '';?>     value="Y" />
updated</label>
        </td>
      </tr>
      <tr>
        <td align="right">Sort by:</td>
        <td>
          <select name="sort_order" size="1">
            <option value="name"    <?php echo ($row->sort_order == 'name') ? 'selected' : ''?>>Name</option>
            <option value="id"      <?php echo ($row->sort_order == 'id') ? 'selected' : ''?>>Id</option>
            <option value="url"     <?php echo ($row->sort_order == 'url') ? 'selected' : ''?>>URL</option>
            <option value="rating"  <?php echo ($row->sort_order == 'rating') ? 'selected' : ''?>>Rating</option>
            <option value="updated" <?php echo ($row->sort_order == 'updated') ? 'selected' : ''?>>Updated</option>
            <option value="rand"  <?php echo ($row->sort_order == 'rand') ? 'selected' : ''?>>Random</option>
            <option value="length"  <?php echo ($row->sort_order == 'length') ? 'selected' : ''?>>Name Length</option>
          </select>&nbsp;&nbsp;
          <input type="checkbox" name="sort_desc" <?php echo ($row->sort_desc  == 'Y') ? 'checked' : '';?> value="Y" /> Descending?<br />
        </td>
      </tr>
      <tr>
        <td align="center">Text/HTML</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td align="right">Before:</td>
        <td><input type="text" name="text_before_link" size="45" value="<?php echo stripslashes($row->text_before_link)?>" /></td>
      </tr>
      <tr>
        <td align="right">Between:</td>
        <td><input type="text" name="text_after_link" size="45" value="<?php echo stripslashes($row->text_after_link)?>" /></td>
      </tr>
      <tr>
        <td align="right">After:</td>
        <td><input type="text" name="text_after_all" size="45" value="<?php echo stripslashes($row->text_after_all)?>" /></td>
      </tr>
      <tr>
        <td align="right">Limit:</td>
        <td><input type="text" name="list_limit" size="5" value="<?php echo $row->list_limit?>"/>          (How many links are shown. Empty for unlimited.)</td>
      </tr>
      <tr>
        <td align="center" colspan="2">
          <input type="submit" name="submit" value="Save" class="search" />&nbsp;
          <input type="submit" name="submit" value="Cancel" class="search">
        </td>
      </tr>
    </table>
  </form>
  </p>
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
      die ("Cheatin' uh ?");

    $submit=$HTTP_POST_VARS["submit"];
    if (isset($submit) && ($submit == "Save")) {

    $cat_id=$HTTP_POST_VARS["cat_id"];

    $cat_name=addslashes($HTTP_POST_VARS["cat_name"]);
    $auto_toggle = $HTTP_POST_VARS["auto_toggle"];
    if ($auto_toggle != 'Y') {
        $auto_toggle = 'N';
    }

    $show_images = $HTTP_POST_VARS["show_images"];
    if ($show_images != 'Y') {
        $show_images = 'N';
    }

    $show_description = $HTTP_POST_VARS["show_description"];
    if ($show_description != 'Y') {
        $show_description = 'N';
    }

    $show_rating = $HTTP_POST_VARS["show_rating"];
    if ($show_rating != 'Y') {
        $show_rating = 'N';
    }

    $show_updated = $HTTP_POST_VARS["show_updated"];
    if ($show_updated != 'Y') {
        $show_updated = 'N';
    }

    $sort_order = $HTTP_POST_VARS["sort_order"];

    $sort_desc = $HTTP_POST_VARS["sort_desc"];
    if ($sort_desc != 'Y') {
        $sort_desc = 'N';
    }
    $text_before_link = addslashes($HTTP_POST_VARS["text_before_link"]);
    $text_after_link = addslashes($HTTP_POST_VARS["text_after_link"]);
    $text_after_all = addslashes($HTTP_POST_VARS["text_after_all"]);

    $list_limit = $HTTP_POST_VARS["list_limit"];
    if ($list_limit == '')
        $list_limit = -1;

    $wpdb->query("UPDATE $tablelinkcategories set
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
      die("You have no right to edit the link categories for this blog.<br>Ask for a promotion to your <a href=\"mailto:$admin_email\">blog admin</a> :)");
    }
?>
<ul id="adminmenu2">
	<li><a href="link-manager.php" >Manage Links</a></li>
	<li><a href="link-add.php">Add Link</a></li>
	<li><a href="link-categories.php" class="current">Link Categories</a></li>
	<li class="last"><a href="link-import.php">Import Blogroll</a></li>
</ul>
<div class="wrap">

          <form name="cats" method="post" action="link-categories.php">
            <strong>Edit</strong> a link category:<?php echo gethelp_link($this_file,'edit_link_category');?><br />
            <table width="100%" cellpadding="5" cellspacing="0" border="0">
              <tr>
			  <th rowspan="2" valign="bottom">Name</th>
                <th rowspan="2" valign="bottom">Id</th>
                <th rowspan="2" valign="bottom">Auto<br />Toggle?</th>
                <th colspan="4" valign="bottom">Show</th>
                <th rowspan="2" valign="bottom">Sort Order</th>
                <th rowspan="2" valign="bottom">Desc?</th>
                <th colspan="3" valign="bottom">Text/HTML</th>
                <th rowspan="2" valign="bottom">Limit</th>
                <th rowspan="2" colspan="2">&nbsp;</th>
              </tr>
              <tr>
                <th valign="top">images?</th>
                <th valign="top">desc?</th>
                <th valign="top">rating?</th>
                <th valign="top">updated?</th>
                <th valign="top">before</th>
                <th valign="top">between</th>
                <th valign="top">after</th>
              </tr>
                <input type="hidden" name="cat_id" value="" />
                <input type="hidden" name="action" value="" />
<?php
$results = $wpdb->get_results("SELECT cat_id, cat_name, auto_toggle, show_images, show_description, "
         . " show_rating, show_updated, sort_order, sort_desc, text_before_link, text_after_link, "
         . " text_after_all, list_limit FROM $tablelinkcategories ORDER BY cat_id");
foreach ($results as $row) {
    if ($row->list_limit == -1) {
        $row->list_limit = 'none';
    }
    $style = ($i % 2) ? ' class="alternate"' : '';
?>
              <tr valign="middle" align="center" <?php echo $style ?> style="border-bottom: 1px dotted #9C9A9C;">
                <td><?php echo stripslashes($row->cat_name)?></td>
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
                <td><a href="link-categories.php?cat_id=<?php echo $row->cat_id?>&amp;action=Edit" class="edit">Edit</a></td>
                <td><a href="link-categories.php?cat_id=<?php echo $row->cat_id?>&amp;action=Delete" onclick="return confirm('You are about to delete this category.\n  \'Cancel\' to stop, \'OK\' to delete.');" class="delete">Delete</a></td>
              </tr>
<?php
        ++$i;
    }
?>
            </table>
        </form>

</div>

<div class="wrap">
    <form name="addcat" method="post">
      <input type="hidden" name="action" value="addcat" />
	  <h3>Add a Link Category:<?php echo gethelp_link($this_file,'add_link_category');?></h3>
    <table width="100%" cellpadding="5" cellspacing="0" border="0">
      <tr>
        <td align="right">Name:</td>
        <td><input type="text" name="cat_name" size="25" />&nbsp;&nbsp;&nbsp;
            <input type="checkbox" name="auto_toggle"  value="Y" /> auto-toggle?</td>
      </tr>
      <tr>
        <td align="right">Show:</td>
        <td>
          <input type="checkbox" name="show_images"  value="Y" /> images&nbsp;&nbsp;
          <input type="checkbox" name="show_description"    value="Y" /> description&nbsp;&nbsp;
          <input type="checkbox" name="show_rating"  value="Y" /> rating&nbsp;&nbsp;
          <input type="checkbox" name="show_updated" value="Y" /> updated</td>
      </tr>
      <tr>
        <td align="right">Sort order:</td>
        <td>
            <select name="sort_order" size="1">
              <option value="name">Name</option>
              <option value="id">Id</option>
              <option value="url">URL</option>
              <option value="rating">Rating</option>
              <option value="updated">Updated</option>
              <option value="rand">Random</option>
            </select>&nbsp;&nbsp;
            <input type="checkbox" name="sort_desc" value="N" /> Descending?<br />
          </td>
      </tr>
      <tr>
        <td align="center">Text/HTML</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td align="right">before:</td>
        <td><input type="text" name="text_before_link" size="45" value="&lt;li&gt;"/></td>
      </tr>
      <tr>
        <td align="right">between:</td>
        <td><input type="text" name="text_after_link" size="45" value="&lt;br /&gt;" /></td>
      </tr>
      <tr>
        <td align="right">after:</td>
        <td><input type="text" name="text_after_all" size="45" value="&lt;/li&gt;"/></td>
      </tr>
      <tr>
        <td align="right">limit:</td>
        <td><input type="text" name="list_limit" size="5" value=""/> (leave empty for no limit)</td>
      </tr>
      <tr>
        <td align="center" colspan="2"><input type="submit" name="submit" value="Add Category!" class="search" /></td>
      </tr>
    </table>
  </form>
</div>
<div class="wrap">
    <h3>Note:</h3>
    <p>Deleting a link category does not delete links from that category.<br />
    It will
    just set them back to the default category <b><?php echo get_linkcatname(1) ?></b>.
    </p>
</div>
<?php
    break;
  } // end default
} // end case
?>
<?php include('admin-footer.php'); ?>