<?php
// Links
// Copyright (C) 2002, 2003 Mike Little -- mike@zed1.com

require_once('../wp-config.php');

$title = 'Manage Links';
$this_file = 'link-manager.php';

function category_dropdown($fieldname, $selected = 0) {
    global $wpdb, $tablelinkcategories;

    $results = $wpdb->get_results("SELECT cat_id, cat_name, auto_toggle FROM $tablelinkcategories ORDER BY cat_id");
    echo '        <select name="'.$fieldname.'" size="1">'."\n";
    foreach ($results as $row) {
      echo "          <option value=\"".$row->cat_id."\"";
      if ($row->cat_id == $selected)
        echo " selected";
        echo ">".$row->cat_id.": ".$row->cat_name;
        if ($row->auto_toggle == 'Y')
            echo ' (auto toggle)';
        echo "</option>\n";
    }
    echo "        </select>\n";
}

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

$wpvarstoreset = array('action','standalone','cat_id', 'linkurl', 'name', 'image',
                       'description', 'visible', 'target', 'category', 'link_id',
                       'submit', 'order_by', 'links_show_cat_id', 'rating', 'rel',
                       'notes', 'linkcheck[]');
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

$links_show_cat_id = $HTTP_COOKIE_VARS["links_show_cat_id_".$cookiehash];
$links_show_order = $HTTP_COOKIE_VARS["links_show_order_".$cookiehash];

if ($action2 != '')
    $action = $action2;

switch ($action) {
  case 'Assign':
  {
    $standalone = 1;
    include_once('admin-header.php');

    // check the current user's level first.
    if ($user_level < get_settings('links_minadminlevel'))
      die ("Cheatin' uh ?");

    //for each link id (in $linkcheck[]): if the current user level >= the
    //userlevel of the owner of the link then we can proceed.

    if (count($linkcheck) == 0) {
        header('Location: '.$this_file);
        exit;
    }
    $all_links = join(',', $linkcheck);
    $results = $wpdb->get_results("SELECT link_id, link_owner, user_level FROM $tablelinks LEFT JOIN $tableusers ON link_owner = ID WHERE link_id in ($all_links)");
    foreach ($results as $row) {
      if (!get_settings('links_use_adminlevels') || ($user_level >= $row->user_level)) { // ok to proceed
        $ids_to_change[] = $row->link_id;
      }
    }

    // should now have an array of links we can change
    $all_links = join(',', $ids_to_change);
    $q = $wpdb->query("update $tablelinks SET link_owner='$newowner' WHERE link_id IN ($all_links)");

    header('Location: '.$this_file);
    break;
  }
  case 'Visibility':
  {
    $standalone = 1;
    include_once('admin-header.php');

    // check the current user's level first.
    if ($user_level < get_settings('links_minadminlevel'))
      die ("Cheatin' uh ?");

    //for each link id (in $linkcheck[]): toggle the visibility
    if (count($linkcheck) == 0) {
        header('Location: '.$this_file);
        exit;
    }
    $all_links = join(',', $linkcheck);
    $results = $wpdb->get_results("SELECT link_id, link_visible FROM $tablelinks WHERE link_id in ($all_links)");
    foreach ($results as $row) {
        if ($row->link_visible == 'Y') { // ok to proceed
            $ids_to_turnoff[] = $row->link_id;
        } else {
            $ids_to_turnon[] = $row->link_id;
        }
    }

    // should now have two arrays of links to change
    if (count($ids_to_turnoff)) {
        $all_linksoff = join(',', $ids_to_turnoff);
        $q = $wpdb->query("update $tablelinks SET link_visible='N' WHERE link_id IN ($all_linksoff)");
    }

    if (count($ids_to_turnon)) {
        $all_linkson = join(',', $ids_to_turnon);
        $q = $wpdb->query("update $tablelinks SET link_visible='Y' WHERE link_id IN ($all_linkson)");
    }

    header('Location: '.$this_file);
    break;
  }
  case 'Move':
  {
    $standalone = 1;
    include_once('admin-header.php');
    // check the current user's level first.
    if ($user_level < get_settings('links_minadminlevel'))
      die ("Cheatin' uh ?");

    //for each link id (in $linkcheck[]) change category to selected value
    if (count($linkcheck) == 0) {
        header('Location: '.$this_file);
        exit;
    }
    $all_links = join(',', $linkcheck);
    // should now have an array of links we can change
    $q = $wpdb->query("update $tablelinks SET link_category='$category' WHERE link_id IN ($all_links)");

    header('Location: '.$this_file);
    break;
  }

  case 'Add':
  {
    $standalone = 1;
    include_once('admin-header.php');

    $link_url = $HTTP_POST_VARS["linkurl"];
    $link_name = $HTTP_POST_VARS["name"];
    $link_image = $HTTP_POST_VARS["image"];
    $link_target = $HTTP_POST_VARS["target"];
    $link_category = $HTTP_POST_VARS["category"];
    $link_description = $HTTP_POST_VARS["description"];
    $link_visible = $HTTP_POST_VARS["visible"];
    $link_rating = $HTTP_POST_VARS["rating"];
    $link_rel = $HTTP_POST_VARS["rel"];
    $link_notes = $HTTP_POST_VARS["notes"];
    $auto_toggle = get_autotoggle($link_category);

    if ($user_level < get_settings('links_minadminlevel'))
      die ("Cheatin' uh ?");

    // if we are in an auto toggle category and this one is visible then we
    // need to make the others invisible before we add this new one.
    if (($auto_toggle == 'Y') && ($link_visible == 'Y')) {
      $wpdb->query("UPDATE $tablelinks set link_visible = 'N' WHERE link_category = $link_category");
    }
    $wpdb->query("INSERT INTO $tablelinks (link_url, link_name, link_image, link_target, link_category, link_description, link_visible, link_owner, link_rating, link_rel, link_notes) " .
      " VALUES('" . addslashes($link_url) . "','"
           . addslashes($link_name) . "', '"
           . addslashes($link_image) . "', '$link_target', $link_category, '"
           . addslashes($link_description) . "', '$link_visible', $user_ID, $link_rating, '" . addslashes($link_rel) . "', '" . addslashes($link_notes) . "')");

    header('Location: ' . $HTTP_SERVER_VARS['HTTP_REFERER']);
    break;
  } // end Add

  case 'editlink':
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
      include_once('admin-header.php');

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
      $link_notes = $HTTP_POST_VARS["notes"];
      $auto_toggle = get_autotoggle($link_category);

      if ($user_level < get_settings('links_minadminlevel'))
        die ("Cheatin' uh ?");

      // if we are in an auto toggle category and this one is visible then we
      // need to make the others invisible before we update this one.
      if (($auto_toggle == 'Y') && ($link_visible == 'Y')) {
        $wpdb->query("UPDATE $tablelinks set link_visible = 'N' WHERE link_category = $link_category");
      }

      $wpdb->query("UPDATE $tablelinks SET link_url='" . addslashes($link_url) . "',\n " .
             " link_name='" . addslashes($link_name) . "',\n link_image='" . addslashes($link_image) . "',\n " .
             " link_target='$link_target',\n link_category=$link_category,\n " .
             " link_visible='$link_visible',\n link_description='" . addslashes($link_description) . "',\n " .
             " link_rating=$link_rating,\n" .
             " link_rel='" . addslashes($link_rel) . "',\n" .
             " link_notes='" . addslashes($link_notes) . "'\n" .
             " WHERE link_id=$link_id");
    } // end if save
    setcookie('links_show_cat_id_'.$cookiehash, $links_show_cat_id, time()+600);
    header('Location: '.$this_file);
    break;
  } // end Save

  case 'Delete':
  {
    $standalone = 1;
    include_once('admin-header.php');

    $link_id = $HTTP_POST_VARS["link_id"];

    if ($user_level < get_settings('links_minadminlevel'))
      die ("Cheatin' uh ?");

    $wpdb->query("DELETE FROM $tablelinks WHERE link_id = '$link_id'");

    if (isset($links_show_cat_id) && ($links_show_cat_id != ''))
        $cat_id = $links_show_cat_id;

    if (!isset($cat_id) || ($cat_id == '')) {
        if (!isset($links_show_cat_id) || ($links_show_cat_id == ''))
        $cat_id = 'All';
    }
    $links_show_cat_id = $cat_id;
    setcookie("links_show_cat_id_".$cookiehash, $links_show_cat_id, time()+600);
    header('Location: '.$this_file);
    break;
  } // end Delete

  case 'linkedit':
  {
    $standalone=0;
    include_once ('admin-header.php');
    if ($user_level < get_settings('links_minadminlevel')) {
      die("You have no right to edit the links for this blog.<br />Ask for a promotion to your <a href=\"mailto:$admin_email\">blog admin</a>. :)");
    }

    $row = $wpdb->get_row("SELECT link_url, link_name, link_image, link_target, link_description, link_visible, link_category AS cat_id, link_rating, link_rel, link_notes " .
      " FROM $tablelinks " .
      " WHERE link_id = $link_id");

    if ($row) {
      $link_url = stripslashes($row->link_url);
      $link_name = stripslashes($row->link_name);
      $link_image = $row->link_image;
      $link_target = $row->link_target;
      $link_category = $row->cat_id;
      $link_description = stripslashes($row->link_description);
      $link_visible = $row->link_visible;
      $link_rating = $row->link_rating;
      $link_rel = stripslashes($row->link_rel);
      $link_notes = stripslashes($row->link_notes);
    }

?>

<div class="wrap">

  <table width="100%" cellpadding="3" cellspacing="3">
  <form name="editlink" method="post">
    <input type="hidden" name="action" value="editlink" />
    <input type="hidden" name="link_id" value="<?php echo $link_id; ?>" />
    <input type="hidden" name="order_by" value="<?php echo $order_by ?>" />
    <input type="hidden" name="cat_id" value="<?php echo $cat_id ?>" />
    <tr>
      <td colspan="2"><strong>Edit</strong> a link:</td>
    </tr>
    <tr>
      <td align="right">URL:</td>
      <td><input type="text" name="linkurl" size="80" value="<?php echo $link_url; ?>"></td>
    </tr>
    <tr>
      <td align="right">Display Name/Alt text:</td>
      <td><input type="text" name="name" size="80" value="<?php echo $link_name; ?>"></td>
    </tr>
    <tr>
      <td align="right">Image:</td>
      <td><input type="text" name="image" size="80" value="<?php echo $link_image; ?>"></td>
    </tr>
    <tr>
      <td align="right">Description:</td>
      <td><input type="text" name="description" size="80" value="<?php echo $link_description; ?>"></td>
    </tr>
    <tr>
      <td align="right">Rel:</td>
      <td><input type="text" name="rel" size="80" value="<?php echo $link_rel; ?>"></td>
    </tr>
    <tr>
      <td valign="top" align="right">Notes:</td>
      <td><textarea name="notes" cols="80" rows="10"><?php echo $link_notes; ?></textarea></td>
    </tr>
    <tr>
      <td align="right">Rating:</td>
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
    <tr>
      <td align="right">Target:</td>
      <td><label><input type="radio" name="target" value="_blank"   <?php echo(($link_target == '_blank') ? 'checked="checked"' : ''); ?>> _blank</label>
        &nbsp;<label><input type="radio" name="target" value="_top" <?php echo(($link_target == '_top') ? 'checked="checked"' : ''); ?>> _top</label>
        &nbsp;<label><input type="radio" name="target" value=""     <?php echo(($link_target == '') ? 'checked="checked"' : ''); ?>> none</label>
      </td>
    </tr>
    <tr>
      <td align="right">Visible:</td>
      <td><label>
        <input type="radio" name="visible" <?php if ($link_visible == 'Y') echo "checked"; ?> value="Y">
        Yes</label>
        &nbsp;<label>
        <input type="radio" name="visible" <?php if ($link_visible == 'N') echo "checked"; ?> value="N">
        No</label>
      </td>
    </tr>
    <tr>
      <td align="right"><label for="category">Category</label>:</td>
      <td>
<?php category_dropdown('category', $link_category); ?>
      </td>
    </tr>
    <tr>
      <td colspan="2" align="center">
        <input type="submit" name="submit" value="Save" class="search">&nbsp;
        <input type="submit" name="submit" value="Cancel" class="search">
      </td>
    </tr>
  </table>
</div>
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
    if (!isset($order_by) || ($order_by == '')) {
        if (!isset($links_show_order) || ($links_show_order == ''))
        $order_by = 'order_name';
    }
    $links_show_order = $order_by;
    //break; fall through
  } // end Show
  case "popup":
  {
    $link_url = stripslashes($HTTP_GET_VARS["linkurl"]);
    $link_name = stripslashes($HTTP_GET_VARS["name"]);
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
    if (isset($links_show_order) && ($links_show_order != ''))
        $order_by = $links_show_order;

    if (!isset($order_by) || ($order_by == ''))
        $order_by = 'order_name';
    $links_show_order = $order_by;

    setcookie('links_show_cat_id_'.$cookiehash, $links_show_cat_id, time()+600);
    setcookie('links_show_order_'.$cookiehash, $links_show_order, time()+600);
    $standalone=0;
    include_once ("./admin-header.php");
    if ($user_level < get_settings('links_minadminlevel')) {
      die("You have no right to edit the links for this blog.<br>Ask for a promotion to your <a href=\"mailto:$admin_email\">blog admin</a> :)");
    }

    switch ($order_by)
    {
        case 'order_id':     $sqlorderby = 'id';          break;
        case 'order_url':    $sqlorderby = 'url';         break;
        case 'order_desc':   $sqlorderby = 'description'; break;
        case 'order_owner':  $sqlorderby = 'owner';       break;
        case 'order_rating': $sqlorderby = 'rating';      break;
        case 'order_name':
        default:             $sqlorderby = 'name';        break;
    }

  if ($action != "popup") {
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
<ul id="adminmenu2">
	<li><a href="link-manager.php" class="current">Manage Links</a></li>
	<li><a href="link-add.php">Add Link</a></li>
	<li><a href="link-categories.php">Link Categories</a></li>
	<li class="last"><a href="link-import.php">Import Blogroll</a></li>
</ul>
<div class="wrap">
    <form name="cats" method="post">
    <table width="75%" cellpadding="3" cellspacing="3">
      <tr>
        <td>
          <strong>Show</strong> links in category:<?php echo gethelp_link($this_file,'link_categories');?><br />
        </td>
        <td>
          <strong>Order</strong> by:<?php echo gethelp_link($this_file,'order_by');?>
        </td>
		<td>&nbsp;</td>
      </tr>
      <tr>
        <td>
<?php
    $results = $wpdb->get_results("SELECT cat_id, cat_name, auto_toggle FROM $tablelinkcategories ORDER BY cat_id");
    echo "        <select name=\"cat_id\">\n";
    echo "          <option value=\"All\"";
    if ($cat_id == 'All')
      echo " selected";
    echo "> All</option>\n";
    foreach ($results as $row) {
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
          <input type="submit" name="action" value="Show" class="search" /><?php echo gethelp_link($this_file,'show');?>
        </td>
      </tr>
    </table>
    </form>

</div>

<div class="wrap">

    <form name="links" id="links" method="post">
    <input type="hidden" name="link_id" value="" />
    <input type="hidden" name="action" value="" />
    <input type="hidden" name="order_by" value="<?php echo $order_by ?>" />
    <input type="hidden" name="cat_id" value="<?php echo $cat_id ?>" />
  <table width="100%" cellpadding="3" cellspacing="3">
    <tr>
      <th width="15%"><?php echo gethelp_link($this_file,'list_o_links');?> Name</th>
      <th>URI</th>
      <th>Category</th>
      <th>rel</th>
      <th>Image</th>
      <th>Visible</th>
      <th colspan="2">Action</th>
      <th>&nbsp;</th>
  </tr>
<?php
    $sql = "SELECT link_url, link_name, link_image, link_description, link_visible,
            link_category AS cat_id, cat_name AS category, $tableusers.user_login, link_id,
            link_rating, link_rel, $tableusers.user_level
            FROM $tablelinks
            LEFT JOIN $tablelinkcategories ON $tablelinks.link_category = $tablelinkcategories.cat_id
            LEFT JOIN $tableusers ON $tableusers.ID = $tablelinks.link_owner ";

    if (isset($cat_id) && ($cat_id != 'All')) {
      $sql .= " WHERE link_category = $cat_id ";
    }
    $sql .= ' ORDER BY link_' . $sqlorderby;

    // echo "$sql";
    $links = $wpdb->get_results($sql);
    if ($links) {
        foreach ($links as $link) {
            $short_url = str_replace('http://', '', stripslashes($link->link_url));
            $short_url = str_replace('www.', '', $short_url);
            if ('/' == substr($short_url, -1))
                $short_url = substr($short_url, 0, -1);
            if (strlen($short_url) > 35)
                $short_url =  substr($short_url, 0, 32).'...';

            $link->link_name = stripslashes($link->link_name);
            $link->category = stripslashes($link->category);
            $link->link_rel = stripslashes($link->link_rel);
            $link->link_description = stripslashes($link->link_description);
            $image = ($link->link_image != null) ? 'Yes' : 'No';
            $visible = ($link->link_visible == 'Y') ? 'Yes' : 'No';
            ++$i;
            $style = ($i % 2) ? ' class="alternate"' : '';
            echo <<<LINKS
 
 
    <tr valign="middle" $style>
        <td><strong>$link->link_name</strong><br />
        Description: $link->link_description</td>
        <td><a href="$link->link_url" title="Visit $link->link_name">$short_url</a></td>
        <td>$link->category</td>
        <td>$link->link_rel</td>
        <td align='center'>$image</td>
        <td align='center'>$visible</td>
LINKS;
            $show_buttons = 1; // default

            if (get_settings('links_use_adminlevels') && ($link->user_level > $user_level)) {
              $show_buttons = 0;
            }

            if ($show_buttons) {
              echo <<<LINKS
        <td><input type="submit" name="edit" onclick="document.forms['links'].link_id.value='$link->link_id'; document.forms['links'].action.value='linkedit';" value="Edit" class="search" /></td>
        <td><input type="submit" name="delete" onclick="document.forms['links'].link_id.value='$link->link_id'; document.forms['links'].action.value='Delete'; return confirm('You are about to delete this link.\\n  \'Cancel\' to stop, \'OK\' to delete.'); " value="Delete" class="search" /></td>
        <td><input type="checkbox" name="linkcheck[]" value="$link->link_id" /><td>
LINKS;
            } else {
              echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>\n";
            }
		echo "\n\t</tr>";
        }
    }
?>
</table>

</div>

<div class="wrap">
  <table width="100%" cellpadding="3" cellspacing="3">
    <tr><th colspan="4">Manage Multiple Links:</th></tr>
    <tr><td colspan="4">Use the checkboxes on the right to select multiple links and choose an action below:</td></tr>
    <tr>
        <td>
          <input type="submit" name="action2" value="Assign" /> ownership <?php echo gethelp_link($this_file,'assign_ownership');?> to:
<?php
    $results = $wpdb->get_results("SELECT ID, user_login FROM $tableusers WHERE user_level > 0 ORDER BY ID");
    echo "          <select name=\"newowner\" size=\"1\">\n";
    foreach ($results as $row) {
      echo "            <option value=\"".$row->ID."\"";
      echo ">".$row->user_login;
      echo "</option>\n";
    }
    echo "          </select>\n";
?>
        </td>
        <td>
          Toggle <input type="submit" name="action2" value="Visibility" /><?php echo gethelp_link($this_file,'toggle_visibility');?>
        </td>
        <td>
          <input type="submit" name="action2" value="Move" /><?php echo gethelp_link($this_file,'move_to_cat');?> to category
<?php category_dropdown('category'); ?>
        </td>
        <td align="right">
          <a href="#" onclick="checkAll(document.getElementById('links')); return false; ">Toggle Checkboxes</a><?php echo gethelp_link($this_file,'toggle_checkboxes');?>
        </td>
    </tr>
</table>
</form>
<?php
  } // end if !popup
?>
</div>


<?php
    break;
  } // end default
} // end case
?>



<?php include('admin-footer.php'); ?>
