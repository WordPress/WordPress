<?php
// Links
// Copyright (C) 2002, 2003 Mike Little -- mike@zed1.com

require_once('../wp-config.php');

$title = 'Manage Links';
$this_file = 'link-manager.php';

function xfn_check($class, $value = '', $type = 'check') {
	global $link_rel;
	if ('' != $value && strstr($link_rel, $value)) {
		echo ' checked="checked"';
	}
	if ('' == $value) {
		if ('family' == $class && !strstr($link_rel, 'child') && !strstr($link_rel, 'parent') && !strstr($link_rel, 'sibling') && !strstr($link_rel, 'spouse') ) echo ' checked="checked"';
		if ('friendship' == $class && !strstr($link_rel, 'friend') && !strstr($link_rel, 'acquaintance') ) echo ' checked="checked"';
		if ('geographical' == $class && !strstr($link_rel, 'co-resident') && !strstr($link_rel, 'neighbor') ) echo ' checked="checked"';
	}
}

function category_dropdown($fieldname, $selected = 0) {
	global $wpdb, $tablelinkcategories;
	
	$results = $wpdb->get_results("SELECT cat_id, cat_name, auto_toggle FROM $tablelinkcategories ORDER BY cat_id");
	echo "\n<select name='$fieldname' size='1'>";
	foreach ($results as $row) {
		echo "\n\t<option value='$row->cat_id'";
		if ($row->cat_id == $selected)
			echo " selected='selected'";
		echo ">$row->cat_id: $row->cat_name";
		if ('Y' == $row->auto_toggle)
			echo ' (auto toggle)';
		echo "</option>\n";
	}
	echo "\n</select>\n";
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

$links_show_cat_id = $HTTP_COOKIE_VARS['links_show_cat_id_' . $cookiehash];
$links_show_order = $HTTP_COOKIE_VARS['links_show_order_' . $cookiehash];

if (!empty($action2)) {
    $action = $action2;
}

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
        header('Location: ' . $this_file);
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

    header('Location: ' . $this_file);
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
        header('Location: ' . $this_file);
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

    header('Location: ' . $this_file);
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
        header('Location: ' . $this_file);
        exit;
    }
    $all_links = join(',', $linkcheck);
    // should now have an array of links we can change
    $q = $wpdb->query("update $tablelinks SET link_category='$category' WHERE link_id IN ($all_links)");

    header('Location: ' . $this_file);
    break;
  }

  case 'Add':
  {
    $standalone = 1;
    include_once('admin-header.php');

    $link_url = $HTTP_POST_VARS['linkurl'];
    $link_name = $HTTP_POST_VARS['name'];
    $link_image = $HTTP_POST_VARS['image'];
    $link_target = $HTTP_POST_VARS['target'];
    $link_category = $HTTP_POST_VARS['category'];
    $link_description = $HTTP_POST_VARS['description'];
    $link_visible = $HTTP_POST_VARS['visible'];
    $link_rating = $HTTP_POST_VARS['rating'];
    $link_rel = $HTTP_POST_VARS['rel'];
    $link_notes = $HTTP_POST_VARS['notes'];
	$link_rss_uri =  $HTTP_POST_VARS['rss_uri'];
    $auto_toggle = get_autotoggle($link_category);

    if ($user_level < get_settings('links_minadminlevel'))
      die ("Cheatin' uh ?");

    // if we are in an auto toggle category and this one is visible then we
    // need to make the others invisible before we add this new one.
    if (($auto_toggle == 'Y') && ($link_visible == 'Y')) {
      $wpdb->query("UPDATE $tablelinks set link_visible = 'N' WHERE link_category = $link_category");
    }
    $wpdb->query("INSERT INTO $tablelinks (link_url, link_name, link_image, link_target, link_category, link_description, link_visible, link_owner, link_rating, link_rel, link_notes, link_rss) " .
      " VALUES('" . addslashes($link_url) . "','"
           . addslashes($link_name) . "', '"
           . addslashes($link_image) . "', '$link_target', $link_category, '"
           . addslashes($link_description) . "', '$link_visible', $user_ID, $link_rating, '" . addslashes($link_rel) . "', '" . addslashes($link_notes) . "', '$link_rss_uri')");

    header('Location: ' . $HTTP_SERVER_VARS['HTTP_REFERER']);
    break;
  } // end Add

  case 'editlink':
  {
    if (isset($submit)) {

      if (isset($links_show_cat_id) && ($links_show_cat_id != ''))
        $cat_id = $links_show_cat_id;

      if (!isset($cat_id) || ($cat_id == '')) {
        if (!isset($links_show_cat_id) || ($links_show_cat_id == ''))
          $cat_id = 'All';
      }
      $links_show_cat_id = $cat_id;

      $standalone = 1;
      include_once('admin-header.php');

      $link_id = $HTTP_POST_VARS['link_id'];
      $link_url = $HTTP_POST_VARS['linkurl'];
      $link_name = $HTTP_POST_VARS['name'];
      $link_image = $HTTP_POST_VARS['image'];
      $link_target = $HTTP_POST_VARS['target'];
      $link_category = $HTTP_POST_VARS['category'];
      $link_description = $HTTP_POST_VARS['description'];
      $link_visible = $HTTP_POST_VARS['visible'];
      $link_rating = $HTTP_POST_VARS['rating'];
      $link_rel = $HTTP_POST_VARS['rel'];
      $link_notes = $HTTP_POST_VARS['notes'];
	  $link_rss_uri =  $HTTP_POST_VARS['rss_uri'];
      $auto_toggle = get_autotoggle($link_category);

      if ($user_level < get_settings('links_minadminlevel'))
        die ("Cheatin' uh ?");

      // if we are in an auto toggle category and this one is visible then we
      // need to make the others invisible before we update this one.
      if (($auto_toggle == 'Y') && ($link_visible == 'Y')) {
        $wpdb->query("UPDATE $tablelinks set link_visible = 'N' WHERE link_category = $link_category");
      }

      $wpdb->query("UPDATE $tablelinks SET link_url='" . addslashes($link_url) . "',
	  link_name='" . addslashes($link_name) . "',\n link_image='" . addslashes($link_image) . "',
	  link_target='$link_target',\n link_category=$link_category,
	  link_visible='$link_visible',\n link_description='" . addslashes($link_description) . "',
	  link_rating=$link_rating,
	  link_rel='" . addslashes($link_rel) . "',
	  link_notes='" . addslashes($link_notes) . "',
	  link_rss = '$link_rss_uri'
	  WHERE link_id=$link_id");
    } // end if save
    setcookie('links_show_cat_id_' . $cookiehash, $links_show_cat_id, time()+600);
    header('Location: ' . $this_file);
    break;
  } // end Save

  case 'Delete':
  {
    $standalone = 1;
    include_once('admin-header.php');

    $link_id = $HTTP_GET_VARS["link_id"];

    if ($user_level < get_settings('links_minadminlevel'))
      die ("Cheatin' uh ?");

    $wpdb->query("DELETE FROM $tablelinks WHERE link_id = $link_id");

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
      die("You have no right to edit the links for this blog.<br />Ask for a promotion to your <a href='mailto:$admin_email'>blog admin</a>. :)");
    }

    $row = $wpdb->get_row("SELECT * 
	FROM $tablelinks 
	WHERE link_id = $link_id");

    if ($row) {
      $link_url = stripslashes($row->link_url);
      $link_name = stripslashes($row->link_name);
      $link_image = $row->link_image;
      $link_target = $row->link_target;
      $link_category = $row->link_category;
      $link_description = stripslashes($row->link_description);
      $link_visible = $row->link_visible;
      $link_rating = $row->link_rating;
      $link_rel = stripslashes($row->link_rel);
      $link_notes = stripslashes($row->link_notes);
	  $link_rss_uri = $row->link_rss;
    }

?>
<ul id="adminmenu2"> 
  <li><a href="link-manager.php" class="current">Manage Links</a></li> 
  <li><a href="link-add.php">Add Link</a></li> 
  <li><a href="link-categories.php">Link Categories</a></li> 
  <li class="last"><a href="link-import.php">Import Blogroll</a></li> 
</ul> 
<style media="screen" type="text/css">
th { text-align: right; }
</style>
<div class="wrap"> 
  <form action="" method="post" name="editlink" id="editlink"> 
  <h3>Edit a link:</h3>
    <table width="100%"  border="0" cellspacing="5" cellpadding="3">
      <tr>
        <th scope="row">URI:</th>
        <td><input type="text" name="linkurl" size="80" value="<?php echo $link_url; ?>" /></td>
      </tr>
      <tr>
        <th scope="row">Link Name: </th>
        <td><input type="text" name="name" size="80" value="<?php echo $link_name; ?>" /></td>
      </tr>
      <tr>
        <th scope="row">RSS URI: </th>
        <td><input name="rss_uri" type="text" id="rss_uri" value="<?php echo $link_rss_uri; ?>" size="80"></td>
      </tr>
      <tr>
        <th scope="row">Image:</th>
        <td><input type="text" name="image" size="80" value="<?php echo $link_image; ?>" /></td>
      </tr>
      <tr>
        <th scope="row">Description:</th>
        <td><input type="text" name="description" size="80" value="<?php echo $link_description; ?>" /></td>
      </tr>
      <tr>
        <th scope="row">rel:</th>
        <td><input type="text" name="rel" id="rel" size="80" value="<?php echo $link_rel; ?>" /></td>
      </tr>
      <tr>
        <th scope="row"><a href="http://gmpg.org/xfn/">XFN</a>:</th>
        <td><table cellpadding="3" cellspacing="5">
            <tr>
              <th scope="row"> friendship </th>
              <td>
                <label for="label">
                <input class="valinp" type="radio" name="friendship" value="acquaintance" id="label" <?php xfn_check('friendship', 'acquaintance', 'radio'); ?> />  acquaintance</label>
                <label for="label2">
                <input class="valinp" type="radio" name="friendship" value="friend" id="label2" <?php xfn_check('friendship', 'friend', 'radio'); ?> /> friend</label>
                <label for="label3">
                <input name="friendship" type="radio" class="valinp" id="label3" value="" <?php xfn_check('friendship', '', 'radio'); ?> />
          none</label>
              </td>
            </tr>
            <tr>
              <th scope="row"> physical </th>
              <td>
                <label for="label4">
                <input class="valinp" type="checkbox" name="physical" value="met" id="label4" <?php xfn_check('physical', 'met'); ?> />
          met</label>
              </td>
            </tr>
            <tr>
              <th scope="row"> professional </th>
              <td>
                <label for="label5">
                <input class="valinp" type="checkbox" name="professional" value="co-worker" id="label5" <?php xfn_check('professional', 'co-worker'); ?> />
          co-worker</label>
                <label for="label6">
                <input class="valinp" type="checkbox" name="professional" value="colleague" id="label6" <?php xfn_check('professional', 'colleague'); ?> />
          colleague</label>
              </td>
            </tr>
            <tr>
              <th scope="row"> geographical </th>
              <td>
                <label for="label7">
                <input class="valinp" type="radio" name="geographical" value="co-resident" id="label7" <?php xfn_check('geographical', 'co-resident', 'radio'); ?> />
          co-resident</label>
                <label for="label8">
                <input class="valinp" type="radio" name="geographical" value="neighbor" id="label8" <?php xfn_check('geographical', 'neighbor', 'radio'); ?> />
          neighbor</label>
                <label for="label9">
                <input class="valinp" type="radio" name="geographical" value="" id="label9" <?php xfn_check('geographical', '', 'radio'); ?> />
          none</label>
              </td>
            </tr>
            <tr>
              <th scope="row"> family </th>
              <td>
                <label for="label10">
                <input class="valinp" type="radio" name="family" value="child" id="label10" <?php xfn_check('family', 'child', 'radio'); ?>  />
          child</label>
                <label for="label11">
                <input class="valinp" type="radio" name="family" value="parent" id="label11" <?php xfn_check('family', 'parent', 'radio'); ?> />
          parent</label>
                <label for="label12">
                <input class="valinp" type="radio" name="family" value="sibling" id="label12" <?php xfn_check('family', 'sibling', 'radio'); ?> />
          sibling</label>
                <label for="label13">
                <input class="valinp" type="radio" name="family" value="spouse" id="label13" <?php xfn_check('family', 'spouse', 'radio'); ?> />
          spouse</label>
                <label for="label14">
                <input class="valinp" type="radio" name="family" value="" id="label14" <?php xfn_check('family', '', 'radio'); ?> />
          none</label>
              </td>
            </tr>
            <tr>
              <th scope="row"> romantic </th>
              <td>
                <label for="label15">
                <input class="valinp" type="checkbox" name="romantic" value="muse" id="label15" <?php xfn_check('romantic', 'muse'); ?> />
          muse</label>
                <label for="label16">
                <input class="valinp" type="checkbox" name="romantic" value="crush" id="label16" <?php xfn_check('romantic', 'crush'); ?> />
          crush</label>
                <label for="label17">
                <input class="valinp" type="checkbox" name="romantic" value="date" id="label17" <?php xfn_check('romantic', 'date'); ?> />
          date</label>
                <label for="label18">
                <input class="valinp" type="checkbox" name="romantic" value="sweetheart" id="label18" <?php xfn_check('romantic', 'sweetheart'); ?> />
          sweetheart</label>
              </td>
            </tr>
        </table></td>
      </tr>
      <tr>
        <th scope="row">Notes:</th>
        <td><textarea name="notes" cols="80" rows="10"><?php echo $link_notes; ?></textarea></td>
      </tr>
      <tr>
        <th scope="row">Rating:</th>
        <td><select name="rating" size="1">
          <?php
    for ($r = 0; $r < 10; $r++) {
      echo('            <option value="'.$r.'" ');
      if ($link_rating == $r)
        echo 'selected="selected"';
      echo('>'.$r.'</option>');
    }
?>
        </select> (Leave at 0 for no rating.) </td>
      </tr>
      <tr>
        <th scope="row">Target:</th>
        <td><label>
          <input type="radio" name="target" value="_blank"   <?php echo(($link_target == '_blank') ? 'checked="checked"' : ''); ?> />
          <code>_blank</code></label>
&nbsp;<label>
<input type="radio" name="target" value="_top" <?php echo(($link_target == '_top') ? 'checked="checked"' : ''); ?> />
<code>_top</code></label>
&nbsp;
<label>
<input type="radio" name="target" value=""     <?php echo(($link_target == '') ? 'checked="checked"' : ''); ?> />
none (Note that the <code>target</code> attribute is illegal in XHTML 1.1 and 1.0 Strict.)</label></td>
      </tr>
      <tr>
        <th scope="row">Visible:</th>
        <td><label>
          <input type="radio" name="visible" <?php if ($link_visible == 'Y') echo "checked"; ?> value="Y" />
Yes</label>
&nbsp;
<label>
<input type="radio" name="visible" <?php if ($link_visible == 'N') echo "checked"; ?> value="N" />
No</label></td>
      </tr>
      <tr>
        <th scope="row">Category:</th>
        <td><?php category_dropdown('category', $link_category); ?></td>
      </tr>
      <tr>
        <td colspan="2" align="center"><input type="submit" name="submit" value="Save Changes" class="search" />
          &nbsp;
          <input type="submit" name="submit" value="Cancel" class="search" />
          <input type="hidden" name="action" value="editlink" />
          <input type="hidden" name="link_id" value="<?php echo $link_id; ?>" />
          <input type="hidden" name="order_by" value="<?php echo $order_by ?>" />
          <input type="hidden" name="cat_id" value="<?php echo $cat_id ?>" /></td>
      </tr>
    </table>
  </form> 
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
    <form name="cats" method="post" action="">
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
      echo " selected='selected'";
    echo "> All</option>\n";
    foreach ($results as $row) {
      echo "          <option value=\"".$row->cat_id."\"";
      if ($row->cat_id == $cat_id)
        echo " selected='selected'";
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
            <option value="order_id"     <?php if ($order_by == 'order_id')     echo " selected='selected'";?>>Link ID</option>
            <option value="order_name"   <?php if ($order_by == 'order_name')   echo " selected='selected'";?>>Name</option>
            <option value="order_url"    <?php if ($order_by == 'order_url')    echo " selected='selected'";?>>URI</option>
            <option value="order_desc"   <?php if ($order_by == 'order_desc')   echo " selected='selected'";?>>Description</option>
            <option value="order_owner"  <?php if ($order_by == 'order_owner')  echo " selected='selected'";?>>Owner</option>
            <option value="order_rating" <?php if ($order_by == 'order_rating') echo " selected='selected'";?>>Rating</option>
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

    <form name="links" id="links" method="post" action="">
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
        <td><a href="link-manager.php?link_id=$link->link_id&amp;action=linkedit" class="edit">Edit</a></td>
        <td><a href="link-manager.php?link_id=$link->link_id&amp;action=Delete" onclick="return confirm('You are about to delete this link.\\n  \'Cancel\' to stop, \'OK\' to delete.');" class="delete">Delete</a></td>
        <td><input type="checkbox" name="linkcheck[]" value="$link->link_id" /></td>
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

<?php
  } // end if !popup
?>
</form>
</div>


<?php
    break;
  } // end default
} // end case
?>



<?php include('admin-footer.php'); ?>
