<?php
// Links
// Copyright (C) 2002, 2003 Mike Little -- mike@zed1.com

require_once('admin.php');

$title = __('Manage Links');
$this_file = $parent_file = 'link-manager.php';

function xfn_check($class, $value = '', $type = 'check') {
	global $link_rel;
	$rels = preg_split('/\s+/', $link_rel);

	if ('' != $value && in_array($value, $rels) ) {
		echo ' checked="checked"';
	}

	if ('' == $value) {
		if ('family' == $class && !strstr($link_rel, 'child') && !strstr($link_rel, 'parent') && !strstr($link_rel, 'sibling') && !strstr($link_rel, 'spouse') && !strstr($link_rel, 'kin')) echo ' checked="checked"';
		if ('friendship' == $class && !strstr($link_rel, 'friend') && !strstr($link_rel, 'acquaintance') && !strstr($link_rel, 'contact') ) echo ' checked="checked"';
		if ('geographical' == $class && !strstr($link_rel, 'co-resident') && !strstr($link_rel, 'neighbor') ) echo ' checked="checked"';
		if ('identity' == $class && in_array('me', $rels) ) echo ' checked="checked"';
	}
}

function category_dropdown($fieldname, $selected = 0) {
	global $wpdb;
	
	$results = $wpdb->get_results("SELECT cat_id, cat_name, auto_toggle FROM $wpdb->linkcategories ORDER BY cat_id");
	echo "\n<select name='$fieldname' size='1'>";
	foreach ($results as $row) {
		echo "\n\t<option value='$row->cat_id'";
		if ($row->cat_id == $selected)
			echo " selected='selected'";
		echo ">$row->cat_id: ".wp_specialchars($row->cat_name);
		if ('Y' == $row->auto_toggle)
			echo ' (auto toggle)';
		echo "</option>\n";
	}
	echo "\n</select>\n";
}

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

switch ($action) {
  case 'assign':
  {
    check_admin_referer();

    // check the current user's level first.
    if ($user_level < 5)
      die (__("Cheatin' uh ?"));

    //for each link id (in $linkcheck[]): if the current user level >= the
    //userlevel of the owner of the link then we can proceed.

    if (count($linkcheck) == 0) {
        header('Location: ' . $this_file);
        exit;
    }
    $all_links = join(',', $linkcheck);
    $results = $wpdb->get_results("SELECT link_id, link_owner, user_level FROM $wpdb->links LEFT JOIN $wpdb->users ON link_owner = ID WHERE link_id in ($all_links)");
    foreach ($results as $row) {
      if (($user_level >= $row->user_level)) { // ok to proceed
        $ids_to_change[] = $row->link_id;
      }
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
    if ($user_level < 5)
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
    if ($user_level < 5)
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

    $link_url = wp_specialchars($_POST['linkurl']);
    $link_url = preg_match('/^(https?|ftps?|mailto|news|gopher):/is', $link_url) ? $link_url : 'http://' . $link_url; 
    $link_name = wp_specialchars($_POST['name']);
    $link_image = wp_specialchars($_POST['image']);
    $link_target = $_POST['target'];
    $link_category = $_POST['category'];
    $link_description = $_POST['description'];
    $link_visible = $_POST['visible'];
    $link_rating = $_POST['rating'];
    $link_rel = $_POST['rel'];
    $link_notes = $_POST['notes'];
	$link_rss_uri =  wp_specialchars($_POST['rss_uri']);
    $auto_toggle = get_autotoggle($link_category);

    if ($user_level < 5)
      die (__("Cheatin' uh ?"));

    // if we are in an auto toggle category and this one is visible then we
    // need to make the others invisible before we add this new one.
    if (($auto_toggle == 'Y') && ($link_visible == 'Y')) {
      $wpdb->query("UPDATE $wpdb->links set link_visible = 'N' WHERE link_category = $link_category");
    }
    $wpdb->query("INSERT INTO $wpdb->links (link_url, link_name, link_image, link_target, link_category, link_description, link_visible, link_owner, link_rating, link_rel, link_notes, link_rss) " .
      " VALUES('" . $link_url . "','"
           . $link_name . "', '"
           . $link_image . "', '$link_target', $link_category, '"
           . $link_description . "', '$link_visible', $user_ID, $link_rating, '" . $link_rel . "', '" . $link_notes . "', '$link_rss_uri')");

    header('Location: ' . $_SERVER['HTTP_REFERER'] . '?added=true');
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

      check_admin_referer();

      $link_id = (int) $_POST['link_id'];
      $link_url = wp_specialchars($_POST['linkurl']);
      $link_url = preg_match('/^(https?|ftps?|mailto|news|gopher):/is', $link_url) ? $link_url : 'http://' . $link_url; 
      $link_name = wp_specialchars($_POST['name']);
      $link_image = wp_specialchars($_POST['image']);
      $link_target = wp_specialchars($_POST['target']);
      $link_category = $_POST['category'];
      $link_description = $_POST['description'];
      $link_visible = $_POST['visible'];
      $link_rating = $_POST['rating'];
      $link_rel = $_POST['rel'];
      $link_notes = $_POST['notes'];
	  $link_rss_uri =  $_POST['rss_uri'];
      $auto_toggle = get_autotoggle($link_category);

      if ($user_level < 5)
        die (__("Cheatin' uh ?"));

      // if we are in an auto toggle category and this one is visible then we
      // need to make the others invisible before we update this one.
      if (($auto_toggle == 'Y') && ($link_visible == 'Y')) {
        $wpdb->query("UPDATE $wpdb->links set link_visible = 'N' WHERE link_category = $link_category");
      }

      $wpdb->query("UPDATE $wpdb->links SET link_url='" . $link_url . "',
	  link_name='" . $link_name . "',\n link_image='" . $link_image . "',
	  link_target='$link_target',\n link_category=$link_category,
	  link_visible='$link_visible',\n link_description='" . $link_description . "',
	  link_rating=$link_rating,
	  link_rel='" . $link_rel . "',
	  link_notes='" . $link_notes . "',
	  link_rss = '$link_rss_uri'
	  WHERE link_id=$link_id");
    } // end if save
    setcookie('links_show_cat_id_' . COOKIEHASH, $links_show_cat_id, time()+600);
    header('Location: ' . $this_file);
    break;
  } // end Save

  case 'Delete':
  {
    check_admin_referer();

    $link_id = (int) $_GET['link_id'];

    if ($user_level < 5)
      die (__("Cheatin' uh ?"));

    $wpdb->query("DELETE FROM $wpdb->links WHERE link_id = $link_id");

    if (isset($links_show_cat_id) && ($links_show_cat_id != ''))
        $cat_id = $links_show_cat_id;

    if (!isset($cat_id) || ($cat_id == '')) {
        if (!isset($links_show_cat_id) || ($links_show_cat_id == ''))
        $cat_id = 'All';
    }
    $links_show_cat_id = $cat_id;
    setcookie('links_show_cat_id_' . COOKIEHASH, $links_show_cat_id, time()+600);
    header('Location: '.$this_file);
    break;
  } // end Delete

  case 'linkedit': {
	$xfn = true;
    include_once ('admin-header.php');
    if ($user_level < 5)
      die(__('You do not have sufficient permissions to edit the links for this blog.'));

    $link_id = (int) $_GET['link_id'];
    $row = $wpdb->get_row("SELECT * FROM $wpdb->links WHERE link_id = $link_id");

    if ($row) {
      $link_url = wp_specialchars($row->link_url, 1);
      $link_name = wp_specialchars($row->link_name, 1);
      $link_image = $row->link_image;
      $link_target = $row->link_target;
      $link_category = $row->link_category;
      $link_description = wp_specialchars($row->link_description);
      $link_visible = $row->link_visible;
      $link_rating = $row->link_rating;
      $link_rel = $row->link_rel;
      $link_notes = wp_specialchars($row->link_notes);
	  $link_rss_uri = wp_specialchars($row->link_rss);
    } else {
		die( __('Link not found.') ); 
	}

?>

<div class="wrap"> 
  <form action="" method="post" name="editlink" id="editlink"> 
  <h2><?php _e('Edit a link:') ?></h2>
<fieldset class="options">
    <legend><?php _e('Basics') ?></legend>
        <table class="editform" width="100%" cellspacing="2" cellpadding="5">
         <tr>
           <th width="33%" scope="row"><?php _e('URI:') ?></th>
           <td width="67%"><input type="text" name="linkurl" value="<?php echo $link_url; ?>" style="width: 95%;" /></td>
         </tr>
         <tr>
           <th scope="row"><?php _e('Link Name:') ?></th>
           <td><input type="text" name="name" value="<?php echo $link_name; ?>" style="width: 95%" /></td>
         </tr>
         <tr>
            <th scope="row"><?php _e('Short description:') ?></th>
         	<td><input type="text" name="description" value="<?php echo $link_description; ?>" style="width: 95%" /></td>
         	</tr>
        <tr>
           <th scope="row"><?php _e('Category:') ?></th>
           <td><?php category_dropdown('category', $link_category); ?></td>
         </tr>
</table>
</fieldset>
       <p class="submit">
       <input type="submit" name="submit" value="<?php _e('Save Changes &raquo;') ?>" />
       </p>
	<fieldset class="options">
        <legend><?php _e('Link Relationship (XFN)') ?></legend>
        <table class="editform" width="100%" cellspacing="2" cellpadding="5">
            <tr>
                <th width="33%" scope="row"><?php _e('rel:') ?></th>
            	<td width="67%"><input type="text" name="rel" id="rel" size="50" value="<?php echo $link_rel; ?>" /></td>
           	</tr>
            <tr>
                <th scope="row"><?php _e('<a href="http://gmpg.org/xfn/">XFN</a> Creator:') ?></th>
            	<td>
					<table cellpadding="3" cellspacing="5">
	          <tr>
              <th scope="row"> <?php _e('identity') ?> </th>
              <td>
                <label for="me">
                <input type="checkbox" name="identity" value="me" id="me" <?php xfn_check('identity', 'me'); ?> />
          <?php _e('another web address of mine') ?></label>
              </td>
            </tr>
            <tr>
              <th scope="row"> <?php _e('friendship') ?> </th>
              <td>
			    <label for="contact">
                <input class="valinp" type="radio" name="friendship" value="contact" id="contact" <?php xfn_check('friendship', 'contact', 'radio'); ?> /> <?php _e('contact') ?></label>
                <label for="acquaintance">
                <input class="valinp" type="radio" name="friendship" value="acquaintance" id="acquaintance" <?php xfn_check('friendship', 'acquaintance', 'radio'); ?> />  <?php _e('acquaintance') ?></label>
                <label id="friend">
                <input class="valinp" type="radio" name="friendship" value="friend" id="friend" <?php xfn_check('friendship', 'friend', 'radio'); ?> /> <?php _e('friend') ?></label>
                <label for="friendship">
                <input name="friendship" type="radio" class="valinp" value="" id="friendship" <?php xfn_check('friendship', '', 'radio'); ?> /> <?php _e('none') ?></label>
              </td>
            </tr>
            <tr>
              <th scope="row"> <?php _e('physical') ?> </th>
              <td>
                <label for="met">
                <input class="valinp" type="checkbox" name="physical" value="met" id="met" <?php xfn_check('physical', 'met'); ?> />
          <?php _e('met') ?></label>
              </td>
            </tr>
            <tr>
              <th scope="row"> <?php _e('professional') ?> </th>
              <td>
                <label for="co-worker">
                <input class="valinp" type="checkbox" name="professional" value="co-worker" id="co-worker" <?php xfn_check('professional', 'co-worker'); ?> />
          <?php _e('co-worker') ?></label>
                <label for="colleague">
                <input class="valinp" type="checkbox" name="professional" value="colleague" id="colleague" <?php xfn_check('professional', 'colleague'); ?> />
          <?php _e('colleague') ?></label>
              </td>
            </tr>
            <tr>
              <th scope="row"> <?php _e('geographical') ?> </th>
              <td>
                <label for="co-resident">
                <input class="valinp" type="radio" name="geographical" value="co-resident" id="co-resident" <?php xfn_check('geographical', 'co-resident', 'radio'); ?> />
          <?php _e('co-resident') ?></label>
                <label for="neighbor">
                <input class="valinp" type="radio" name="geographical" value="neighbor" id="neighbor" <?php xfn_check('geographical', 'neighbor', 'radio'); ?> />
          <?php _e('neighbor') ?></label>
                <label for="geographical">
                <input class="valinp" type="radio" name="geographical" value="" id="geographical" <?php xfn_check('geographical', '', 'radio'); ?> />
          <?php _e('none') ?></label>
              </td>
            </tr>
            <tr>
              <th scope="row"> family </th>
              <td>
                <label for="child">
                <input class="valinp" type="radio" name="family" value="child" id="child" <?php xfn_check('family', 'child', 'radio'); ?>  />
          <?php _e('child') ?></label>
                <label for="kin">
                <input class="valinp" type="radio" name="family" value="kin" id="kin" <?php xfn_check('family', 'kin', 'radio'); ?>  />
          <?php _e('kin') ?></label>
                <label for="parent">
                <input class="valinp" type="radio" name="family" value="parent" id="parent" <?php xfn_check('family', 'parent', 'radio'); ?> />
          <?php _e('parent') ?></label>
                <label for="sibling">
                <input class="valinp" type="radio" name="family" value="sibling" id="sibling" <?php xfn_check('family', 'sibling', 'radio'); ?> />
          <?php _e('sibling') ?></label>
                <label for="spouse">
                <input class="valinp" type="radio" name="family" value="spouse" id="spouse" <?php xfn_check('family', 'spouse', 'radio'); ?> />
          <?php _e('spouse') ?></label>
                <label for="family">
                <input class="valinp" type="radio" name="family" value="" id="family" <?php xfn_check('family', '', 'radio'); ?> />
          <?php _e('none') ?></label>
              </td>
            </tr>
            <tr>
              <th scope="row"> <?php _e('romantic') ?> </th>
              <td>
                <label for="muse">
                <input class="valinp" type="checkbox" name="romantic" value="muse" id="muse" <?php xfn_check('romantic', 'muse'); ?> />
         <?php _e('muse') ?></label>
                <label for="crush">
                <input class="valinp" type="checkbox" name="romantic" value="crush" id="crush" <?php xfn_check('romantic', 'crush'); ?> />
         <?php _e('crush') ?></label>
                <label for="date">
                <input class="valinp" type="checkbox" name="romantic" value="date" id="date" <?php xfn_check('romantic', 'date'); ?> />
         <?php _e('date') ?></label>
                <label for="romantic">
                <input class="valinp" type="checkbox" name="romantic" value="sweetheart" id="romantic" <?php xfn_check('romantic', 'sweetheart'); ?> />
         <?php _e('sweetheart') ?></label>
              </td>
            </tr>
        </table>
		  </td>
           	</tr>
</table>
</fieldset>
       <p class="submit">
       <input type="submit" name="submit" value="<?php _e('Save Changes &raquo;') ?>" />
       </p>
<fieldset class="options">
        <legend><?php _e('Advanced') ?></legend>
        <table class="editform" width="100%" cellspacing="2" cellpadding="5">
         <tr>
           <th width="33%" scope="row"><?php _e('Image URI:') ?></th>
           <td width="67%"><input type="text" name="image" size="50" value="<?php echo $link_image; ?>" style="width: 95%" /></td>
         </tr>
<tr>
           <th scope="row"><?php _e('RSS URI:') ?> </th>
           <td><input name="rss_uri" type="text" id="rss_uri" value="<?php echo $link_rss_uri; ?>" size="50" style="width: 95%" /></td>
         </tr>
         <tr>
           <th scope="row"><?php _e('Notes:') ?></th>
           <td><textarea name="notes" cols="50" rows="10" style="width: 95%"><?php echo $link_notes; ?></textarea></td>
         </tr>
         <tr>
           <th scope="row"><?php _e('Rating:') ?></th>
           <td><select name="rating" size="1">
<?php
    for ($r = 0; $r < 10; $r++) {
      echo('            <option value="'.$r.'" ');
      if ($link_rating == $r)
        echo 'selected="selected"';
      echo('>'.$r.'</option>');
    }
?>
           </select>
         &nbsp;<?php _e('(Leave at 0 for no rating.)') ?> </td>
         </tr>
         <tr>
           <th scope="row"><?php _e('Target') ?></th>
           <td><label>
          <input type="radio" name="target" value="_blank"   <?php echo(($link_target == '_blank') ? 'checked="checked"' : ''); ?> />
          <code>_blank</code></label><br />
<label>
<input type="radio" name="target" value="_top" <?php echo(($link_target == '_top') ? 'checked="checked"' : ''); ?> />
<code>_top</code></label><br />
<label>
<input type="radio" name="target" value=""     <?php echo(($link_target == '') ? 'checked="checked"' : ''); ?> />
<?php _e('none') ?></label><br />
<?php _e('(Note that the <code>target</code> attribute is illegal in XHTML 1.1 and 1.0 Strict.)') ?></td>
         </tr>
         <tr>
           <th scope="row"><?php _e('Visible:') ?></th>
           <td><label>
             <input type="radio" name="visible" <?php if ($link_visible == 'Y') echo "checked='checked'"; ?> value="Y" />
<?php _e('Yes') ?></label><br /><label>
<input type="radio" name="visible" <?php if ($link_visible == 'N') echo "checked='checked'"; ?> value="N" />
<?php _e('No') ?></label></td>
         </tr>
</table>
</fieldset>
<p class="submit"><input type="submit" name="submit" value="<?php _e('Save Changes &raquo;') ?>" />
          <input type="hidden" name="action" value="editlink" />
          <input type="hidden" name="link_id" value="<?php echo (int) $link_id; ?>" />
          <input type="hidden" name="order_by" value="<?php echo wp_specialchars($order_by, 1); ?>" />
          <input type="hidden" name="cat_id" value="<?php echo (int) $cat_id ?>" /></p>
  </form> 
</div>
<?php
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
    if ($user_level < 5) {
      die(__("You do not have sufficient permissions to edit the links for this blog."));
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
  <table width="100%" cellpadding="3" cellspacing="3">
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
            link_rating, link_rel, $wpdb->users.user_level
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
            $style = ($i % 2) ? ' class="alternate"' : '';
            echo <<<LINKS

    <tr valign="middle" $style>
        <td><strong>$link->link_name</strong><br />
LINKS;
        echo sprintf(__('Description: %s'), $link->link_description) . "</td>";
        echo "<td><a href=\"$link->link_url\" title=\"" . sprintf(__('Visit %s'), $link->link_name) . "\">$short_url</a></td>";
        echo <<<LINKS
        <td>$link->category</td>
        <td>$link->link_rel</td>
        <td align='center'>$image</td>
        <td align='center'>$visible</td>
LINKS;
            $show_buttons = 1; // default

            if ($link->user_level > $user_level) {
              $show_buttons = 0;
            }

            if ($show_buttons) {
        echo '<td><a href="link-manager.php?link_id=' . $link->link_id . '&amp;action=linkedit" class="edit">' . __('Edit') . '</a></td>';
        echo '<td><a href="link-manager.php?link_id=' . $link->link_id . '&amp;action=Delete"' .  " onclick=\"return confirm('" . __("You are about to delete this link.\\n  \'Cancel\' to stop, \'OK\' to delete.") .  "');" . '" class="delete">' . __('Delete') . '</a></td>';
        echo '<td><input type="checkbox" name="linkcheck[]" value="' . $link->link_id . '" /></td>';
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
    <tr><th colspan="4"><?php _e('Manage Multiple Links:') ?></th></tr>
    <tr><td colspan="4"><?php _e('Use the checkboxes on the right to select multiple links and choose an action below:') ?></td></tr>
    <tr>
        <td>
          <?php _e('Assign ownership to:'); ?>
<?php
    $results = $wpdb->get_results("SELECT ID, user_login FROM $wpdb->users WHERE user_level > 0 ORDER BY ID");
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
          <?php _e('Move to category:'); category_dropdown('category'); ?> <input name="move" type="submit" id="move" value="<?php _e('Go') ?>" />
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