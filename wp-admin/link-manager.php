<?php
// Links
// Copyright (C) 2002, 2003 Mike Little -- mike@zed1.com

require_once('admin.php');

$title = __('Manage Links');
$this_file = $parent_file = 'link-manager.php';

$wpvarstoreset = array('action','cat_id', 'linkurl', 'name', 'image',
                       'description', 'visible', 'target', 'category', 'link_id',
                       'submit', 'order_by', 'links_show_cat_id', 'rating', 'rel',
                       'notes', 'linkcheck[]');

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

$links_show_cat_id = $_COOKIE['links_show_cat_id_' . COOKIEHASH];
$links_show_order = $_COOKIE['links_show_order_' . COOKIEHASH];

if ('' != $_POST['assign']) $action = 'assign';
if ('' != $_POST['visibility']) $action = 'visibility';
if ('' != $_POST['move']) $action = 'move';
if ('' != $_POST['linkcheck']) $linkcheck = $_POST[linkcheck];

switch ($action) {
  case 'assign':
  {
    check_admin_referer();

    // check the current user's level first.
    if ( !current_user_can('manage_links') )
      die (__("Cheatin' uh ?"));

    //for each link id (in $linkcheck[]): if the current user level >= the
    //userlevel of the owner of the link then we can proceed.

    if (count($linkcheck) == 0) {
        header('Location: ' . $this_file);
        exit;
    }
    $all_links = join(',', $linkcheck);
    $results = $wpdb->get_results("SELECT link_id, link_owner FROM $wpdb->links LEFT JOIN $wpdb->users ON link_owner = ID WHERE link_id in ($all_links)");
    foreach ($results as $row) {
       $ids_to_change[] = $row->link_id;
    }

    // should now have an array of links we can change
    $all_links = join(',', $ids_to_change);
    $q = $wpdb->query("update $wpdb->links SET link_owner='$newowner' WHERE link_id IN ($all_links)");

    header('Location: ' . $this_file);
    break;
  }
  case 'visibility':
  {
    check_admin_referer();

    // check the current user's level first.
    if ( !current_user_can('manage_links') )
      die (__("Cheatin' uh ?"));

    //for each link id (in $linkcheck[]): toggle the visibility
    if (count($linkcheck) == 0) {
        header('Location: ' . $this_file);
        exit;
    }
    $all_links = join(',', $linkcheck);
    $results = $wpdb->get_results("SELECT link_id, link_visible FROM $wpdb->links WHERE link_id in ($all_links)");
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
        $q = $wpdb->query("update $wpdb->links SET link_visible='N' WHERE link_id IN ($all_linksoff)");
    }

    if (count($ids_to_turnon)) {
        $all_linkson = join(',', $ids_to_turnon);
        $q = $wpdb->query("update $wpdb->links SET link_visible='Y' WHERE link_id IN ($all_linkson)");
    }

    header('Location: ' . $this_file);
    break;
  }
  case 'move':
  {
    check_admin_referer();

    // check the current user's level first.
    if ( !current_user_can('manage_links') )
      die (__("Cheatin' uh ?"));

    //for each link id (in $linkcheck[]) change category to selected value
    if (count($linkcheck) == 0) {
        header('Location: ' . $this_file);
        exit;
    }
    $all_links = join(',', $linkcheck);
    // should now have an array of links we can change
    $q = $wpdb->query("update $wpdb->links SET link_category='$category' WHERE link_id IN ($all_links)");

    header('Location: ' . $this_file);
    break;
  }

  case 'Add':
  {
    check_admin_referer();

	add_link();
	
    header('Location: ' . $_SERVER['HTTP_REFERER'] . '?added=true');
    break;
  } // end Add

  case 'editlink':
  {
 
	check_admin_referer();
 	
	if (isset($links_show_cat_id) && ($links_show_cat_id != ''))
		$cat_id = $links_show_cat_id;

	if (!isset($cat_id) || ($cat_id == '')) {
		if (!isset($links_show_cat_id) || ($links_show_cat_id == ''))
			$cat_id = 'All';
	}
	$links_show_cat_id = $cat_id;

	$link_id = (int) $_POST['link_id'];
	edit_link($link_id);
	
    setcookie('links_show_cat_id_' . COOKIEHASH, $links_show_cat_id, time()+600);
    wp_redirect($this_file);
    break;
  } // end Save

  case 'Delete':
  {
    check_admin_referer();

    if ( !current_user_can('manage_links') )
      die (__("Cheatin' uh ?"));

    $link_id = (int) $_GET['link_id'];

	wp_delete_link($link_id);
	
    if (isset($links_show_cat_id) && ($links_show_cat_id != ''))
        $cat_id = $links_show_cat_id;

    if (!isset($cat_id) || ($cat_id == '')) {
        if (!isset($links_show_cat_id) || ($links_show_cat_id == ''))
        $cat_id = 'All';
    }
    $links_show_cat_id = $cat_id;
    setcookie('links_show_cat_id_' . COOKIEHASH, $links_show_cat_id, time()+600);
    wp_redirect($this_file);
    break;
  } // end Delete

  case 'linkedit': {
	$xfn = true;
    include_once ('admin-header.php');
    if ( !current_user_can('manage_links') )
      die(__('You do not have sufficient permissions to edit the links for this blog.'));

    $link_id = (int) $_GET['link_id'];
 
	if ( !$link = get_link_to_edit($link_id) )
		die( __('Link not found.') );
		
	include('edit-link-form.php');
    break;
  } // end linkedit
  case __("Show"):
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
    $link_url = stripslashes($_GET["linkurl"]);
    $link_name = stripslashes($_GET["name"]);
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

    setcookie('links_show_cat_id_' . COOKIEHASH, $links_show_cat_id, time()+600);
    setcookie('links_show_order_' . COOKIEHASH, $links_show_order, time()+600);
    include_once ("./admin-header.php");
    if ( !current_user_can('manage_links') )
      die(__("You do not have sufficient permissions to edit the links for this blog."));

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

<div class="wrap">
    <form name="cats" method="post" action="">
    <table width="75%" cellpadding="3" cellspacing="3">
      <tr>
        <td>
        <?php _e('<strong>Show</strong> links in category:'); ?><br />
        </td>
        <td>
          <?php _e('<strong>Order</strong> by:');?>
        </td>
		<td>&nbsp;</td>
      </tr>
      <tr>
        <td>
<?php
    $results = $wpdb->get_results("SELECT cat_id, cat_name, auto_toggle FROM $wpdb->linkcategories ORDER BY cat_id");
    echo "        <select name=\"cat_id\">\n";
    echo "          <option value=\"All\"";
    if ($cat_id == 'All')
      echo " selected='selected'";
    echo "> " . __('All') . "</option>\n";
    foreach ($results as $row) {
      echo "          <option value=\"".$row->cat_id."\"";
      if ($row->cat_id == $cat_id)
        echo " selected='selected'";
        echo ">".$row->cat_id.": ".wp_specialchars($row->cat_name);
        if ($row->auto_toggle == 'Y')
            echo ' (auto toggle)';
        echo "</option>\n";
    }
    echo "        </select>\n";
?>
        </td>
        <td>
          <select name="order_by">
            <option value="order_id"     <?php if ($order_by == 'order_id')     echo " selected='selected'";?>><?php _e('Link ID') ?></option>
            <option value="order_name"   <?php if ($order_by == 'order_name')   echo " selected='selected'";?>><?php _e('Name') ?></option>
            <option value="order_url"    <?php if ($order_by == 'order_url')    echo " selected='selected'";?>><?php _e('URI') ?></option>
            <option value="order_desc"   <?php if ($order_by == 'order_desc')   echo " selected='selected'";?>><?php _e('Description') ?></option>
            <option value="order_owner"  <?php if ($order_by == 'order_owner')  echo " selected='selected'";?>><?php _e('Owner') ?></option>
            <option value="order_rating" <?php if ($order_by == 'order_rating') echo " selected='selected'";?>><?php _e('Rating') ?></option>
          </select>
        </td>
        <td>
          <input type="submit" name="action" value="<?php _e('Show') ?>" />
        </td>
      </tr>
    </table>
    </form>

</div>

<form name="links" id="links" method="post" action="">
<div class="wrap">

    <input type="hidden" name="link_id" value="" />
    <input type="hidden" name="action" value="" />
    <input type="hidden" name="order_by" value="<?php echo wp_specialchars($order_by, 1); ?>" />
    <input type="hidden" name="cat_id" value="<?php echo (int) $cat_id ?>" />
  <table id="the-list-x" width="100%" cellpadding="3" cellspacing="3">
    <tr>
      <th width="15%"><?php _e('Name') ?></th>
      <th><?php _e('URI') ?></th>
      <th><?php _e('Category') ?></th>
      <th><?php _e('rel') ?></th>
      <th><?php _e('Image') ?></th>
      <th><?php _e('Visible') ?></th>
      <th colspan="2"><?php _e('Action') ?></th>
      <th>&nbsp;</th>
  </tr>
<?php
    $sql = "SELECT link_url, link_name, link_image, link_description, link_visible,
            link_category AS cat_id, cat_name AS category, $wpdb->users.user_login, link_id,
            link_rating, link_rel
            FROM $wpdb->links
            LEFT JOIN $wpdb->linkcategories ON $wpdb->links.link_category = $wpdb->linkcategories.cat_id
            LEFT JOIN $wpdb->users ON $wpdb->users.ID = $wpdb->links.link_owner ";

    if (isset($cat_id) && ($cat_id != 'All')) {
      $sql .= " WHERE link_category = $cat_id ";
    }
    $sql .= ' ORDER BY link_' . $sqlorderby;

    // echo "$sql";
    $links = $wpdb->get_results($sql);
    if ($links) {
        foreach ($links as $link) {
      	    $link->link_name = wp_specialchars($link->link_name);
      	    $link->link_category = wp_specialchars($link->link_category);
      	    $link->link_description = wp_specialchars($link->link_description);
            $link->link_url = wp_specialchars($link->link_url);
            $short_url = str_replace('http://', '', $link->link_url);
            $short_url = str_replace('www.', '', $short_url);
            if ('/' == substr($short_url, -1))
                $short_url = substr($short_url, 0, -1);
            if (strlen($short_url) > 35)
                $short_url =  substr($short_url, 0, 32).'...';

            $image = ($link->link_image != null) ? __('Yes') : __('No');
            $visible = ($link->link_visible == 'Y') ? __('Yes') : __('No');
            ++$i;
            $style = ($i % 2) ? '' : ' class="alternate"';
?>
    <tr id="link-<?php echo $link->link_id; ?>" valign="middle" <?php echo $style; ?>>
		<td><strong><?php echo $link->link_name; ?></strong><br />
<?php			
        echo sprintf(__('Description: %s'), $link->link_description) . "</td>";
        echo "<td><a href=\"$link->link_url\" title=\"" . sprintf(__('Visit %s'), $link->link_name) . "\">$short_url</a></td>";
        echo <<<LINKS
        <td>$link->category</td>
        <td>$link->link_rel</td>
        <td align='center'>$image</td>
        <td align='center'>$visible</td>
LINKS;
            $show_buttons = 1; // default

            if ($show_buttons) {
        echo '<td><a href="link-manager.php?link_id=' . $link->link_id . '&amp;action=linkedit" class="edit">' . __('Edit') . '</a></td>';
        echo '<td><a href="link-manager.php?link_id=' . $link->link_id . '&amp;action=Delete"' .  " onclick=\"return deleteSomething( 'link', $link->link_id , '" . sprintf(__("You are about to delete the &quot;%s&quot; link to %s.\\n&quot;Cancel&quot; to stop, &quot;OK&quot; to delete."), wp_specialchars($link->link_name,1), wp_specialchars($link->link_url)) . '\' );" class="delete">' . __('Delete') . '</a></td>';
        echo '<td><input type="checkbox" name="linkcheck[]" value="' . $link->link_id . '" /></td>';
            } else {
              echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>\n";
            }
		echo "\n    </tr>\n";
        }
    }
?>
</table>

<div id="ajax-response"></div>

</div>

<div class="wrap">
  <table width="100%" cellpadding="3" cellspacing="3">
    <tr><th colspan="4"><?php _e('Manage Multiple Links:') ?></th></tr>
    <tr><td colspan="4"><?php _e('Use the checkboxes on the right to select multiple links and choose an action below:') ?></td></tr>
    <tr>
        <td>
          <?php _e('Assign ownership to:'); ?>
<?php
    $results = $wpdb->get_results("SELECT ID, user_login FROM $wpdb->users ORDER BY ID");
    echo "          <select name=\"newowner\" size=\"1\">\n";
    foreach ($results as $row) {
      echo "            <option value=\"".$row->ID."\"";
      echo ">".$row->user_login;
      echo "</option>\n";
    }
    echo "          </select>\n";
?>
        <input name="assign" type="submit" id="assign" value="<?php _e('Go') ?>" />
        </td>
        <td>
          <input name="visibility" type="submit" id="visibility" value="<?php _e('Toggle Visibility') ?>" />
        </td>
        <td>
          <?php _e('Move to category:'); link_category_dropdown('category'); ?> <input name="move" type="submit" id="move" value="<?php _e('Go') ?>" />
        </td>
        <td align="right">
          <a href="#" onclick="checkAll(document.getElementById('links')); return false; "><?php _e('Toggle Checkboxes') ?></a>
        </td>
    </tr>
</table>

<?php
  } // end if !popup
?>
</div>
</form>


<?php
    break;
  } // end default
} // end case
?>

<?php include('admin-footer.php'); ?>
