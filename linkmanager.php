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

include_once('links.config.php');
include_once("./links.php");

$title = "Manage Links";

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

$b2varstoreset = array('action','standalone','cat_id', 'linkurl', 'name', 'image',
                       'description', 'visible', 'target', 'category', 'link_id',
                       'submit', 'order_by', 'links_show_cat_id', 'rating', 'rel');
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

$links_show_cat_id = $HTTP_COOKIE_VARS["links_show_cat_id"];

//error_log("start, links_show_cat_id=$links_show_cat_id");  

switch ($action) {
  case "Add":
  {
    $standalone = 1;
    include_once("./b2header.php");

    $link_url = $HTTP_POST_VARS["linkurl"];
    $link_name = $HTTP_POST_VARS["name"];
    $link_image = $HTTP_POST_VARS["image"];
    $link_target = $HTTP_POST_VARS["target"];
    $link_category = $HTTP_POST_VARS["category"];
    $link_description = $HTTP_POST_VARS["description"];
    $link_visible = $HTTP_POST_VARS["visible"];
    $link_rating = $HTTP_POST_VARS["rating"];
    $link_rel = $HTTP_POST_VARS["rel"];
    $auto_toggle = get_autotoggle($link_category);

    if ($user_level < $minadminlevel)
      die ("Cheatin' uh ?");

    // if we are in an auto toggle category and this one is visible then we
    // need to make the others invisible before we add this new one.
    if (($auto_toggle == 'Y') && ($link_visible == 'Y')) {
      $sql = "UPDATE $tablelinks set link_visible = 'N' WHERE link_category = $link_category";
      $sql_result = mysql_query($sql) or die("Couldn't execute query."."sql=[$sql]". mysql_error());
    }

    $sql = "INSERT INTO $tablelinks (link_url, link_name, link_image, link_target, link_category, link_description, link_visible, link_owner, link_rating, link_rel) " .
      " VALUES('" . addslashes($link_url) . "','"
           . addslashes($link_name) . "', '"
           . addslashes($link_image) . "', '$link_target', $link_category, '"
           . addslashes($link_description) . "', '$link_visible', $user_ID, $link_rating, '" . addslashes($link_rel) ."')";

    $sql_result = mysql_query($sql) or die("Couldn't execute query."."sql=[$sql]". mysql_error());

    header("Location: linkmanager.php");
    break;
  } // end Add

  case "editlink":
  {
    if (isset($submit) && ($submit == "Save")) {

      if (isset($links_show_cat_id) && ($links_show_cat_id != ''))
        $cat_id = $links_show_cat_id;

      if (!isset($cat_id) || ($cat_id == '')) {
        if (!isset($links_show_cat_id) || ($links_show_cat_id == ''))
          $cat_id = 'All';
      }
      $links_show_cat_id = $cat_id;

      $standalone = 1;
      include_once("./b2header.php");

      $link_id = $HTTP_POST_VARS["link_id"];
      $link_url = $HTTP_POST_VARS["linkurl"];
      $link_name = $HTTP_POST_VARS["name"];
      $link_image = $HTTP_POST_VARS["image"];
      $link_target = $HTTP_POST_VARS["target"];
      $link_category = $HTTP_POST_VARS["category"];
      $link_description = $HTTP_POST_VARS["description"];
      $link_visible = $HTTP_POST_VARS["visible"];
      $link_rating = $HTTP_POST_VARS["rating"];
      $link_rel = $HTTP_POST_VARS["rel"];
      $auto_toggle = get_autotoggle($link_category);

      if ($user_level < $minadminlevel)
        die ("Cheatin' uh ?");

      // if we are in an auto toggle category and this one is visible then we
      // need to make the others invisible before we update this one.
      if (($auto_toggle == 'Y') && ($link_visible == 'Y')) {
        $sql = "UPDATE $tablelinks set link_visible = 'N' WHERE link_category = $link_category";
        $sql_result = mysql_query($sql) or die("Couldn't execute query."."sql=[$sql]". mysql_error());
      }

      $sql = "UPDATE $tablelinks SET link_url='" . addslashes($link_url) . "',\n " .
             " link_name='" . addslashes($link_name) . "',\n link_image='" . addslashes($link_image) . "',\n " .
             " link_target='$link_target',\n link_category=$link_category,\n " .
             " link_visible='$link_visible',\n link_description='" . addslashes($link_description) . "',\n " .
             " link_rating=$link_rating,\n" .
             " link_rel='" . addslashes($link_rel) . "'\n" .
             " WHERE link_id=$link_id";
      //error_log($sql);
      $sql_result = mysql_query($sql) or die("Couldn't execute query."."sql=[$sql]". mysql_error());

    } // end if save
    setcookie('links_show_cat_id', $links_show_cat_id, time()+600);
    header("Location: linkmanager.php");
    break;
  } // end Save

  case "Delete":
  {
    $standalone = 1;
    include_once("./b2header.php");

    $link_id = $HTTP_POST_VARS["link_id"];

    if ($user_level < $minadminlevel)
      die ("Cheatin' uh ?");

    $sql = "DELETE FROM $tablelinks WHERE link_id = '$link_id'";
    $sql_result = mysql_query($sql) or die("Couldn't execute query.".mysql_error());

    if (isset($links_show_cat_id) && ($links_show_cat_id != ''))
        $cat_id = $links_show_cat_id;
        
    if (!isset($cat_id) || ($cat_id == '')) {
        if (!isset($links_show_cat_id) || ($links_show_cat_id == ''))
        $cat_id = 'All';
    }
    $links_show_cat_id = $cat_id;
    setcookie("links_show_cat_id", $links_show_cat_id, time()+600);
    header("Location: linkmanager.php");
    break;
  } // end Delete
  case "linkedit":
  {
    $standalone=0;
    include_once ("./b2header.php");
    if ($user_level < $minadminlevel) {
      die("You have no right to edit the links for this blog.<br>Ask for a promotion to your <a href=\"mailto:$admin_email\">blog admin</a> :)");
    }

    $sql = "SELECT link_url, link_name, link_image, link_target, link_description, link_visible, link_category AS cat_id, link_rating, link_rel " .
      " FROM $tablelinks " .
      " WHERE link_id = $link_id";

    $result = mysql_query($sql) or die("Couldn't execute query.".mysql_error());
    if ($row = mysql_fetch_object($result)) {
      $link_url = $row->link_url;
      $link_name = stripslashes($row->link_name);
      $link_image = $row->link_image;
      $link_target = $row->link_target;
      $link_category = $row->cat_id;
      $link_description = stripslashes($row->link_description);
      $link_visible = $row->link_visible;
      $link_rating = $row->link_rating;
      $link_rel = stripslashes($row->link_rel);
    }

?>
    <?php echo $blankline ?>
    <?php echo $tabletop ?>
    <table width="95%" cellpadding="5" cellspacing="0" border="0">
    <form name="editlink" method="post">
    <input type="hidden" name="action" value="editlink" />
    <input type="hidden" name="link_id" value="<?php echo $link_id; ?>" />
    <input type="hidden" name="order_by" value="<?php echo $order_by ?>" />
    <input type="hidden" name="cat_id" value="<?php echo $cat_id ?>" />
    <tr><td colspan="2"><b>Edit</b> a link:</td></tr>
      <tr height="20">
        <td height="20" align="right">URL:</td>
        <td><input type="text" name="linkurl" size="80" value="<?php echo $link_url; ?>"></td>
      </tr>
      <tr height="20">
        <td height="20" align="right">Display Name/Alt text:</td>
        <td><input type="text" name="name" size="80" value="<?php echo $link_name; ?>"></td>
      </tr>
      <tr height="20">
        <td height="20" align="right">Image:</td>
        <td><input type="text" name="image" size="80" value="<?php echo $link_image; ?>"></td>
      </tr>
      <tr height="20">
        <td height="20" align="right">Description:</td>
        <td><input type="text" name="description" size="80" value="<?php echo $link_description; ?>"></td>
      </tr>
      <tr height="20">
        <td height="20" align="right">Rel:</td>
        <td><input type="text" name="rel" size="80" value="<?php echo $link_rel; ?>"></td>
      </tr>
      <tr height="20">
        <td height="20" align="right">Rating:</td>
        <td>
          <select name="rating" size="1">
<?php
    for ($r = 0; $r < 10; $r++) {
      echo('            <option value="'.$r.'" ');
      if ($link_rating == $r)
        echo('selected');
      echo('>'.$r.'</option>');
    }
?>
            </select>&nbsp;(Leave at 0 for no rating.)
        </td>
      </tr>
      <tr height="20">
        <td height="20" align="right">Target:</td>
        <td><input type="radio" name="target" <?php if ($link_target == '_blank') echo "checked"; ?> value="_blank">_blank&nbsp;<input type="radio" name="target" <?php if ($link_target == '_top') echo "checked"; ?> value="_top">_top</td>
      </tr>
      <tr height="20">
        <td height="20" align="right">Visible:</td>
        <td><input type="radio" name="visible" <?php if ($link_visible == 'Y') echo "checked"; ?> value="Y">Y&nbsp;<input type="radio" name="visible" <?php if ($link_visible == 'N') echo "checked"; ?> value="N">N</td>
      </tr>
      <tr height="20">
        <td height="20" align="right">Category:</td>
        <td>
<?php
    $query = "SELECT cat_id, cat_name, auto_toggle FROM $tablelinkcategories ORDER BY cat_id";
    $result = mysql_query($query) or die("Couldn't execute query. ".mysql_error());
    echo "        <select name=\"category\" size=\"1\">\n";
    while($row = mysql_fetch_object($result)) {
      echo "          <option value=\"".$row->cat_id."\"";
      if ($row->cat_id == $link_category)
        echo " selected";
        echo ">".$row->cat_id.": ".$row->cat_name;
        if ($row->auto_toggle == 'Y')
            echo ' (auto toggle)';
        echo "</option>\n";
    }
    echo "        </select>\n";
?>
        </td>
      </tr>
      <tr height="20">
        <td colspan="2" align="center">
          <input type="submit" name="submit" value="Save" class="search">&nbsp;<input type="submit" name="submit" value="Cancel" class="search"></a>
        </td>
      </tr>
    </table>

<?php
    break;
  } // end linkedit
  case "Show":
  {
    if (!isset($cat_id) || ($cat_id == '')) {
        if (!isset($links_show_cat_id) || ($links_show_cat_id == ''))
        $cat_id = 'All';
    }
    $links_show_cat_id = $cat_id;
    //break; fall through
  } // end Show
  case "popup":
  {
    $link_url = $HTTP_GET_VARS["linkurl"];
    $link_name = $HTTP_GET_VARS["name"];
    //break; fall through
  }
  default:
  {
    if (isset($links_show_cat_id) && ($links_show_cat_id != ''))
        $cat_id = $links_show_cat_id;
        
    if (!isset($cat_id) || ($cat_id == '')) {
        if (!isset($links_show_cat_id) || ($links_show_cat_id == ''))
        $cat_id = 'All';
    }
    $links_show_cat_id = $cat_id;
    if (!isset($order_by) || ($order_by == ''))
        $order_by = 'order_id';
    setcookie('links_show_cat_id', $links_show_cat_id, time()+600);
    $standalone=0;
    include_once ("./b2header.php");
    if ($user_level < $minadminlevel) {
      die("You have no right to edit the links for this blog.<br>Ask for a promotion to your <a href=\"mailto:$admin_email\">blog admin</a> :)");
    }

    switch ($order_by)
    {
        case 'order_name':   $sqlorderby = 'name';        break;
        case 'order_url':    $sqlorderby = 'url';         break;
        case 'order_desc':   $sqlorderby = 'description'; break;
        case 'order_owner':  $sqlorderby = 'owner';       break;
        case 'order_rating': $sqlorderby = 'rating';      break;
        case 'order_id':     //fall through
        default:             $sqlorderby = 'id';          break;
    }
    
  if ($action != "popup") {
?>
    <?php echo $blankline ?>
    <?php echo $tabletop ?>
    <form name="cats" method="post">
    <table width="50%" cellpadding="5" cellspacing="0" border="0">
      <tr><td><b>Link Categories:</b></td><td colspan="2"><a href="linkcategories.php">Manage Link Categories</a></td></tr>
      <tr>
        <td>
          <b>Show</b> links in category:<br />
        </td>
        <td>
          <b>Order</b> by:
        </td>
      </tr>
      <tr>
        <td>
<?php
    $query = "SELECT cat_id, cat_name, auto_toggle FROM $tablelinkcategories ORDER BY cat_id";
    $result = mysql_query($query) or die("Couldn't execute query. ".mysql_error());
    echo "        <select name=\"cat_id\">\n";
    echo "          <option value=\"All\"";
    if ($cat_id == 'All')
      echo " selected";
    echo "> All</option>\n";
    while($row = mysql_fetch_object($result)) {
      echo "          <option value=\"".$row->cat_id."\"";
      if ($row->cat_id == $cat_id)
        echo " selected";
        echo ">".$row->cat_id.": ".$row->cat_name;
        if ($row->auto_toggle == 'Y')
            echo ' (auto toggle)';
        echo "</option>\n";
    }
    echo "        </select>\n";
?>
        </td>
        <td>
          <select name="order_by">
            <option value="order_id"     <?php if ($order_by == 'order_id')     echo " selected";?>>Id</option>
            <option value="order_name"   <?php if ($order_by == 'order_name')   echo " selected";?>>Name</option>
            <option value="order_url"    <?php if ($order_by == 'order_url')    echo " selected";?>>URL</option>
            <option value="order_desc"   <?php if ($order_by == 'order_desc')   echo " selected";?>>Description</option>
            <option value="order_owner"  <?php if ($order_by == 'order_owner')  echo " selected";?>>Owner</option>
            <option value="order_rating" <?php if ($order_by == 'order_rating') echo " selected";?>>Rating</option>
          </select>
        </td>
        <td>
          <input type="submit" name="action" value="Show" class="search" />
        </td>
      </tr>
    </table>
    </form>

    <?php echo $tablebottom ?>
    <?php echo $blankline ?>
    <?php echo $tabletop ?>

    <table width="100%" cellpadding="1" cellspacing="0" border="0">
    <form name="links" method="post">
    <input type="hidden" name="link_id" value="" />
    <input type="hidden" name="action" value="" />
    <input type="hidden" name="order_by" value="<?php echo $order_by ?>" />
    <input type="hidden" name="cat_id" value="<?php echo $cat_id ?>" />
    <tr >
      <td style="border-bottom: 1px dotted #9C9A9C;"><b>URL</b></td>
      <td style="border-bottom: 1px dotted #9C9A9C;"><b>Name</b></td>
      <td style="border-bottom: 1px dotted #9C9A9C;"><b>Img?</b></td>
      <td style="border-bottom: 1px dotted #9C9A9C;"><b>Vis?</b></td>
      <td style="border-bottom: 1px dotted #9C9A9C;"><b>Category</b></td>
      <td style="border-bottom: 1px dotted #9C9A9C;">&nbsp;</td>
      <td style="border-bottom: 1px dotted #9C9A9C;">&nbsp;</td>
    </tr>
<?php
    $sql = "SELECT link_url, link_name, link_image, link_description, link_visible, link_category AS cat_id, cat_name AS category, $tableusers.user_login, link_id, link_rating, link_rel "
           . " FROM $tablelinks LEFT JOIN $tablelinkcategories ON $tablelinks.link_category = $tablelinkcategories.cat_id "
           . " LEFT JOIN $tableusers on $tableusers.ID = $tablelinks.link_owner ";
    // have we got a where clause?
    if (($use_adminlevels) || (isset($cat_id) && ($cat_id != 'All')) ) {
        $sql .= " WHERE ";
    }
    if ($use_adminlevels) {
        $sql .= " ($tableusers.user_level <= $user_level"
                . "   OR $tableusers.ID = $user_ID)";
    }
    if (isset($cat_id) && ($cat_id != 'All')) {
      // have we already started the where clause?
      if ($use_adminlevels) {
        $sql .= " AND ";
      }
      $sql .= " link_category = $cat_id ";
    }
    $sql .= " ORDER BY link_".$sqlorderby;

    //echo "$sql";
    $result = mysql_query($sql) or die("Couldn't execute query.".mysql_error());
    while ($row = mysql_fetch_object($result)) {
      $short_url = str_replace('http://', '', $row->link_url);
      if (strlen($short_url) > 35) {
        $short_url =  substr($short_url, 0, 32).'...';
      }
      echo("<tr>\n");
      echo("  <td ><a href=\"".$row->link_url."\">".$short_url."</a></td>\n");
      echo("  <td >".stripslashes($row->link_name)."</td>\n");
      if ($row->link_image != null) {
        echo("  <td align=\"center\">Y</td>\n");
      } else {
        echo("  <td align=\"center\">N</td>\n");
      }
      if ($row->link_visible == 'Y') {
        echo("  <td align=\"center\">Y</td>\n");
      } else {
        echo("  <td align=\"center\">N</td>\n");
      }
      echo("  <td>".stripslashes($row->category)."</td>\n");
      echo("  <td><input type=\"submit\" name=\"edit\" onclick=\"forms['links'].link_id.value='$row->link_id'; forms['links'].action.value='linkedit'; \" value=\"Edit\" class=\"search\" /></td>\n");
      echo("  <td><input type=\"submit\" name=\"delete\" onclick=\"forms['links'].link_id.value='$row->link_id'; forms['links'].action.value='Delete'; return confirm('You are about to delete this link.\\n  \'Cancel\' to stop, \'OK\' to delete.'); \" value=\"Delete\" class=\"search\" /></td>\n");
      echo("</tr>\n");

      echo("<tr>\n");
      echo("  <td style=\"border-bottom: 1px dotted #9C9A9C;\" colspan=\"2\"><b>Desc:</b>&nbsp;".stripslashes($row->link_description)."</td>\n");
      echo("  <td style=\"border-bottom: 1px dotted #9C9A9C;\" ><b>Rel:</b></td>\n");
      $my_rel = stripslashes($row->link_rel);
      if ($my_rel == '') {
          $my_rel = '&nbsp;';
      }
      echo("  <td style=\"border-bottom: 1px dotted #9C9A9C;\" >$my_rel</td>\n");
      echo("  <td style=\"border-bottom: 1px dotted #9C9A9C;\" ><b>Rating:</b>&nbsp;".$row->link_rating."</td>\n");
      echo("  <td style=\"border-bottom: 1px dotted #9C9A9C;\" valign=\"top\"><b>Owner:</b></td>\n");
      echo("  <td style=\"border-bottom: 1px dotted #9C9A9C;\" valign=\"top\">".$row->user_login."</td>\n");
      echo("</tr>\n");
    }
?>
    </form>
    </table>
<?php
  } // end if !popup
?>
    <?php echo $tablebottom ?>
    <?php echo $blankline ?>

    <?php echo $tabletop ?>

    <table width="95%" cellpadding="5" cellspacing="0" border="0">
    <form name="addlink" method="post">
    <input type="hidden" name="action" value="Add" />
    <tr><td colspan="2"><b>Add</b> a link:</td></tr>
      <tr height="20">
        <td height="20" align="right">URL:</td>
        <td><input type="text" name="linkurl" size="80" value="<?php echo $link_url; ?>"></td>
      </tr>
      <tr height="20">
        <td height="20" align="right">Display Name/Alt text:</td>
        <td><input type="text" name="name" size="80" value="<?php echo $name; ?>"></td>
      </tr>
      <tr height="20">
        <td height="20" align="right">Image:</td>
        <td><input type="text" name="image" size="80" value=""></td>
      </tr>
      <tr height="20">
        <td height="20" align="right">Description:</td>
        <td><input type="text" name="description" size="80" value=""></td>
      </tr>
      <tr height="20">
        <td height="20" align="right">Rel:</td>
        <td><input type="text" name="rel" size="80" value=""></td>
      </tr>
      <tr height="20">
        <td height="20" align="right">Rating:</td>
        <td>
          <select name="rating" size="1">
<?php
    for ($r = 0; $r < 10; $r++) {
      echo('            <option value="'.$r.'">'.$r.'</option>');
    }
?>
            </select>&nbsp;(Leave at 0 for no rating.)
        </td>
      </tr>
      <tr height="20">
        <td height="20" align="right">Target:</td>
        <td><input type="radio" name="target" checked="checked" value="_blank">_blank&nbsp;<input type="radio" name="target" value="_top">_top</td>
      </tr>
      <tr height="20">
        <td height="20" align="right">Visible:</td>
        <td><input type="radio" name="visible" checked="checked" value="Y">Y&nbsp;<input type="radio" name="visible" value="N">N</td>
      </tr>
      <tr height="20">
        <td height="20" align="right">Category:</td>
        <td>
<?php
    $query = "SELECT cat_id, cat_name, auto_toggle FROM $tablelinkcategories ORDER BY cat_id";
    $result = mysql_query($query) or die("Couldn't execute query. ".mysql_error());
    echo "        <select name=\"category\" size=\"1\">\n";
    while($row = mysql_fetch_object($result)) {
      echo "          <option value=\"".$row->cat_id."\"";
      if ($row->cat_id == $cat_id)
        echo " selected";
        echo ">".$row->cat_id.": ".$row->cat_name;
        if ($row->auto_toggle == 'Y')
            echo ' (auto toggle)';
        echo "</option>\n";
    }
    echo "        </select>\n";
?>
        </td>
      </tr>
      <tr height="20">
        <td colspan="2" align="center">
          <input type="submit" name="submit" value="Add" class="search">
        </td>
      </tr>
    </table>

<?php
    break;
  } // end default
} // end case
?>

<?php echo $tablebottom ?>

<?php include($b2inc."/b2footer.php") ?>
