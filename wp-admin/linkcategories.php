<?php
// $Id$
//
// Links
// Copyright (C) 2002 Mike Little -- mike@zed1.com
//
// This is an add-on to b2 weblog / news publishing tool
// b2 is copyright (c)2001, 2002 by Michel Valdrighi - m@tidakada.com
//
// **********************************************************************
// Copyright (C) 2002 Mike Little
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
// General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
//
// Mike Little (mike@zed1.com)
// *****************************************************************

include_once('../wp-links/links.php');

$title = "Link Categories";

$b2varstoreset = array('action','standalone','cat', 'auto_toggle');
for ($i=0; $i<count($b2varstoreset); $i += 1) {
    $b2var = $b2varstoreset[$i];
    if (!isset($$b2var)) {
        if (empty($HTTP_POST_VARS["$b2var"])) {
            if (empty($HTTP_GET_VARS["$b2var"])) {
                $$b2var = '';
            } else {
                $$b2var = $HTTP_GET_VARS["$b2var"];
            }
        } else {
            $$b2var = $HTTP_POST_VARS["$b2var"];
        }
    }
}

switch ($action) {
  case "addcat":
  {
      $standalone = 1;
      include_once("./b2header.php");

      if ($user_level < get_settings('links_minadminlevel'))
          die ("Cheatin' uh ?");

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

      $wpdb->query("INSERT INTO $tablelinkcategories (cat_id, cat_name, auto_toggle, show_images, show_description, \n" .
             " show_rating, show_updated, sort_order, sort_desc, text_before_link, text_after_link, text_after_all, list_limit) \n" .
             " VALUES ('0', '$cat_name', '$auto_toggle', '$show_images', '$show_description', \n" .
             " '$show_rating', '$show_updated', '$sort_order', '$sort_desc', '$text_before_link', '$text_after_link', \n" .
             " '$text_after_all', $list_limit)");

      header("Location: linkcategories.php");
    break;
  } // end addcat
  case "Delete":
  {
    $standalone = 1;
    include_once("./b2header.php");

    $cat_id = $HTTP_POST_VARS["cat_id"];
    $cat_name=get_linkcatname($cat_id);
    $cat_name=addslashes($cat_name);

    if ($cat_id=="1")
        die("Can't delete the <b>$cat_name</b> link category: this is the default one");

    if ($user_level < get_settings('links_minadminlevel'))
    die ("Cheatin' uh ?");

    $wpdb->query("DELETE FROM $tablelinkcategories WHERE cat_id='$cat_id'");
    $wpdb->query("UPDATE $tablelinks SET link_category=1 WHERE link_category='$cat_id'");

    header("Location: linkcategories.php");
    break;
  } // end delete
  case "Edit":
  {
    include_once ("./b2header.php");
    $cat_id = $HTTP_POST_VARS["cat_id"];
    $row = $wpdb->get_row("SELECT cat_id, cat_name, auto_toggle, show_images, show_description, "
         . " show_rating, show_updated, sort_order, sort_desc, text_before_link, text_after_link, "
         . " text_after_all, list_limit FROM $tablelinkcategories WHERE cat_id=$cat_id");
    if ($row) {
        if ($row->list_limit == -1) {
            $row->list_limit = '';
        }
?>
<div class="wrap">
  <p>Edit Link Category '<b><?php echo $row->cat_name?></b>'</p>
  <p>
    <form name="editcat" method="post">
      <input type="hidden" name="action" value="editedcat" />
      <input type="hidden" name="cat_id" value="<?php echo $row->cat_id ?>" />
    <table border="0">
      <tr>
        <td align="right">Name:</td>
        <td><input type="text" name="cat_name" size="25" value="<?php echo stripslashes($row->cat_name)?>" />&nbsp;&nbsp;&nbsp;
            <input type="checkbox" name="auto_toggle" <?php echo ($row->auto_toggle == 'Y') ? 'checked' : '';?> value="Y" /> auto-toggle?</td>
      </tr>
      <tr>
        <td align="right"><b>Show:</b></td>
        <td>
          <input type="checkbox" name="show_images"      <?php echo ($row->show_images  == 'Y') ? 'checked' : '';?>     value="Y" /> images&nbsp;&nbsp;
          <input type="checkbox" name="show_description" <?php echo ($row->show_description == 'Y') ? 'checked' : '';?> value="Y" /> description&nbsp;&nbsp;
          <input type="checkbox" name="show_rating"      <?php echo ($row->show_rating  == 'Y') ? 'checked' : '';?>     value="Y" /> rating&nbsp;&nbsp;
          <input type="checkbox" name="show_updated"     <?php echo ($row->show_updated == 'Y') ? 'checked' : '';?>     value="Y" /> updated
        </td>
      </tr>
      <tr>
        <td align="right">Sort order:</td>
        <td>
          <select name="sort_order" size="1">                                        
            <option value="name"    <?php echo ($row->sort_order == 'name') ? 'selected' : ''?>>Name</option>
            <option value="id"      <?php echo ($row->sort_order == 'id') ? 'selected' : ''?>>Id</option>                                           
            <option value="url"     <?php echo ($row->sort_order == 'url') ? 'selected' : ''?>>URL</option>
            <option value="rating"  <?php echo ($row->sort_order == 'rating') ? 'selected' : ''?>>Rating</option>
            <option value="updated" <?php echo ($row->sort_order == 'updated') ? 'selected' : ''?>>Updated</option>
            <option value="rand"  <?php echo ($row->sort_order == 'rand') ? 'selected' : ''?>>Random</option>
          </select>&nbsp;&nbsp;
          <input type="checkbox" name="sort_desc" <?php echo ($row->sort_desc  == 'Y') ? 'checked' : '';?> value="Y" /> Descending?<br />
        </td>
      </tr>
      <tr>
        <td align="center"><b>Text/HTML</b></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td align="right">before:</td>
        <td><input type="text" name="text_before_link" size="45" value="<?php echo stripslashes($row->text_before_link)?>" /></td>
      </tr>
      <tr>
        <td align="right">between:</td>
        <td><input type="text" name="text_after_link" size="45" value="<?php echo stripslashes($row->text_after_link)?>" /></td>
      </tr>
      <tr>
        <td align="right">after:</td>
        <td><input type="text" name="text_after_all" size="45" value="<?php echo stripslashes($row->text_after_all)?>" /></td>
      </tr>
      <tr>
        <td align="right">limit:</td>
        <td><input type="text" name="list_limit" size="5" value="<?php echo $row->list_limit?>"/> (leave empty for no limit)</td>
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
    include_once("./b2header.php");

    if ($user_level < get_settings('links_minadminlevel'))
      die ("Cheatin' uh ?");

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
    

    header("Location: linkcategories.php");
    break;
  } // end editcat
  default:
  {
    $standalone=0;
    include_once ("./b2header.php");
    if ($user_level < get_settings('links_minadminlevel')) {
      die("You have no right to edit the link categories for this blog.<br>Ask for a promotion to your <a href=\"mailto:$admin_email\">blog admin</a> :)");
    }
?>
<div class="wrap">
    <table width="" cellpadding="5" cellspacing="0" border="0">
      <tr>
        <td>
          <form name="cats" method="post">
            <b>Edit</b> a link category:<br />
            <table width="" cellpadding="5" cellspacing="0" border="0">
              <tr style="background-color: #ddd;">
                <th rowspan="2" valign="bottom" style="border-bottom: 1px dotted #9C9A9C;" >Id</th>
                <th rowspan="2" valign="bottom" style="border-bottom: 1px dotted #9C9A9C;" >Name</th>
                <th rowspan="2" valign="bottom" style="border-bottom: 1px dotted #9C9A9C;" >Auto<br />Toggle?</th>
                <th colspan="4" valign="bottom" style="border-left: 1px dotted #9C9A9C; border-right: 1px dotted #9C9A9C;">Show</th>
                <th rowspan="2" valign="bottom" style="border-bottom: 1px dotted #9C9A9C;" >Sort Order</th>
                <th rowspan="2" valign="bottom" style="border-bottom: 1px dotted #9C9A9C;" >Desc?</th>
                <th colspan="3" valign="bottom" style="border-left: 1px dotted #9C9A9C; border-right: 1px dotted #9C9A9C;">Text/HTML</th>
                <th rowspan="2" valign="bottom" style="border-bottom: 1px dotted #9C9A9C;" >Limit</th>
                <th rowspan="2" colspan="2" style="border-bottom: 1px dotted #9C9A9C;" >&nbsp;</th>
              </tr>
              <tr style="background-color: #ddd;">
                <th valign="top" style="border-bottom: 1px dotted #9C9A9C; border-left: 1px dotted #9C9A9C;" >images?</th>
                <th valign="top" style="border-bottom: 1px dotted #9C9A9C;" >desc?</th>
                <th valign="top" style="border-bottom: 1px dotted #9C9A9C;" >rating?</th>
                <th valign="top" style="border-bottom: 1px dotted #9C9A9C; border-right: 1px dotted #9C9A9C;" >updated?</th>
                <th valign="top" style="border-bottom: 1px dotted #9C9A9C; border-left: 1px dotted #9C9A9C;" >before</th>
                <th valign="top" style="border-bottom: 1px dotted #9C9A9C;" >between</th>
                <th valign="top" style="border-bottom: 1px dotted #9C9A9C; border-right: 1px dotted #9C9A9C;" >after</th>
              </tr>
                <form name="cats" method="post">
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
              <tr valign="middle" <?php echo $style ?>>
                <td style="border-bottom: 1px dotted #9C9A9C;"><?php echo $row->cat_id?></td>
                <td style="border-bottom: 1px dotted #9C9A9C;"><?php echo stripslashes($row->cat_name)?></td>
                <td style="border-bottom: 1px dotted #9C9A9C;"><?php echo $row->auto_toggle?></td>
                <td style="border-bottom: 1px dotted #9C9A9C;"><?php echo $row->show_images?></td>
                <td style="border-bottom: 1px dotted #9C9A9C;"><?php echo $row->show_description?></td>
                <td style="border-bottom: 1px dotted #9C9A9C;"><?php echo $row->show_rating?></td>
                <td style="border-bottom: 1px dotted #9C9A9C;"><?php echo $row->show_updated?></td>
                <td style="border-bottom: 1px dotted #9C9A9C;"><?php echo $row->sort_order?></td>
                <td style="border-bottom: 1px dotted #9C9A9C;"><?php echo $row->sort_desc?></td>
                <td style="border-bottom: 1px dotted #9C9A9C;" nowrap><?php echo htmlentities($row->text_before_link)?>&nbsp;</td>
                <td style="border-bottom: 1px dotted #9C9A9C;" nowrap><?php echo htmlentities($row->text_after_link)?>&nbsp;</td>
                <td style="border-bottom: 1px dotted #9C9A9C;" nowrap><?php echo htmlentities($row->text_after_all)?></td>
                <td style="border-bottom: 1px dotted #9C9A9C;"><?php echo $row->list_limit?></td>
                <td style="border-bottom: 1px dotted #9C9A9C;"><input type="submit" name="edit" onclick="forms['cats'].cat_id.value='<?php echo $row->cat_id?>'; forms['cats'].action.value='Edit'; " value="Edit" class="search" /></td>
                <td style="border-bottom: 1px dotted #9C9A9C;"><input type="submit" name="delete" onclick="forms['cats'].cat_id.value='<?php echo $row->cat_id?>'; forms['cats'].action.value='Delete'; return confirm('You are about to delete this category.\\n  \'Cancel\' to stop, \'OK\' to delete.'); " value="Delete" class="search" /></td>
              </tr>
<?php
        ++$i;
    }
?>
            </table>

            </table>
          </form>
</div>

<div class="wrap">
    <form name="addcat" method="post">
      <input type="hidden" name="action" value="addcat" />
    <table border="0">
      <tr>
        <th>Add a Link Category:</th>
      </tr>
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
        <td><select name="sort_order" size="1">                                        
              <option value="name">Name</option>
              <option value="id">Id</option>                                           
              <option value="url">URL</option>
              <option value="rating">Rating</option>
              <option value="updated">Updated</option>
              <option value="rand">Random</option>
            </select>&nbsp;&nbsp;
            <input type="checkbox" name="sort_desc" value="N" /> Descending?<br /></td>
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
    </form>
    </table>

</div>

<div class="wrap">
    <b>Note:</b><br />
    Deleting a link category does not delete links from that category.<br />It will
    just set them back to the default category <b><?php echo get_linkcatname(1) ?></b>.
</div>
<?php
    break;
  } // end default
} // end case
?>
</table>

<?php include('b2footer.php'); ?>
