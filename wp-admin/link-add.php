<?php

$title = 'Add Link';
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

require('admin-header.php');
?>
<ul id="adminmenu2">
	<li><a href="link-manager.php">Manage Links</a></li>
	<li><a href="link-add.php" class="current">Add Link</a></li>
	<li><a href="link-categories.php">Link Categories</a></li>
	<li class="last"><a href="link-import.php">Import Blogroll</a></li>
</ul>
<div class="wrap">

    <table width="100%" cellpadding="3" cellspacing="3">
    <form name="addlink" method="post" action="link-manager.php">
    <input type="hidden" name="action" value="Add" />
    <tr><td colspan="2"><strong>Add</strong> a link:<?php echo gethelp_link($this_file,'add_a_link');?></td></tr>
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
        <td><input type="text" name="image" size="80" value=""></td>
      </tr>
      <tr>
        <td align="right">Description:</td>
        <td><input type="text" name="description" size="80" value=""></td>
      </tr>
      <tr>
        <td align="right">rel:</td>
        <td><input type="text" name="rel" id="rel" size="80" value=""></td>
      </tr>
      <tr>
        <td valign="top" align="right"><a href="http://gmpg.org/xfn/">XFN</a>:</td>
        <td>  <table cellspacing="0">
        <tr>
          <th scope="row">
            friendship
          </th>
          <td>

            <label for="friendship-aquaintance"><input class="valinp" type="radio" name="friendship" value="acquaintance" id="friendship-aquaintance" /> acquaintance</label> <label for="friendship-friend"><input class="valinp" type="radio" name="friendship" value="friend" id="friendship-friend" /> friend</label> <label for="friendship-none"><input class="valinp" type="radio" name="friendship" value="" id="friendship-none" /> none</label>
          </td>
        </tr>
        <tr>
          <th scope="row">

            physical
          </th>
          <td>
            <label for="met"><input class="valinp" type="checkbox" name="physical" value="met" id="met" /> met</label>
          </td>
        </tr>
        <tr>
          <th scope="row">

            professional
          </th>
          <td>
            <label for="co-worker"><input class="valinp" type="checkbox" name="professional" value="co-worker" id="co-worker" /> co-worker</label> <label for="colleague"><input class="valinp" type="checkbox" name="professional" value="colleague" id="colleague" /> colleague</label>
          </td>
        </tr>
        <tr>

          <th scope="row">
            geographical
          </th>
          <td>
            <label for="co-resident"><input class="valinp" type="radio" name="geographical" value="co-resident" id="co-resident" /> co-resident</label> <label for="neighbor"><input class="valinp" type="radio" name="geographical" value="neighbor" id="neighbor" /> neighbor</label> <label for="geographical-none"><input class="valinp" type="radio" name="geographical" value="" id="geographical-none" /> none</label>

          </td>
        </tr>
        <tr>
          <th scope="row">
            family
          </th>
          <td>
            <label for="family-child"><input class="valinp" type="radio" name="family" value="child" id="family-child" /> child</label> <label for="family-parent"><input class="valinp" type="radio" name="family" value="parent" id="family-parent" /> parent</label> <label for="family-sibling"><input class="valinp" type="radio" name="family" value="sibling" id="family-sibling" /> sibling</label> <label for="family-spouse"><input class="valinp" type="radio" name="family" value="spouse" id="family-spouse" /> spouse</label> 
            <label for="family-none"><input class="valinp" type="radio" name="family" value="" id="family-none" /> none</label>

          </td>
        </tr>
        <tr>
          <th scope="row">
            romantic
          </th>
          <td>
            <label for="muse"><input class="valinp" type="checkbox" name="romantic" value="muse" id="muse" /> muse</label> <label for="crush"><input class="valinp" type="checkbox" name="romantic" value="crush" id="crush" /> crush</label> <label for="date"><input class="valinp" type="checkbox" name="romantic" value="date" id="date" /> date</label> <label for="sweetheart"><input class="valinp" type="checkbox" name="romantic" value="sweetheart" id="sweetheart" /> sweetheart</label><label for="spouse"></label>

          </td>
        </tr>
      </table></td>
      </tr>
      <tr>
        <td valign="top" align="right">Notes:</td>
        <td><textarea name="notes" cols="80" rows="10"></textarea></td>
      </tr>
      <tr>
        <td align="right">Rating:</td>
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
      <tr>
        <td align="right">Target:</td>
        <td><label><input type="radio" name="target" value="_blank"> _blank</label>
        &nbsp;<label><input type="radio" name="target" value="_top"> _top</label>
        &nbsp;<label><input type="radio" name="target" value="" checked="checked"> none</label>
        </td>
      </tr>
      <tr>
        <td align="right">Visible:</td>
        <td><label>
          <input type="radio" name="visible" checked="checked" value="Y">
          Yes</label>
          &nbsp;<label>
          <input type="radio" name="visible" value="N">
          No</label>
        </td>
      </tr>
      <tr>
        <td align="right"><label for="category">Category</label>:</td>
        <td>
<?php category_dropdown('category'); ?>
        </td>
      </tr>
      <tr>
        <td colspan="2" align="center">
          <input type="submit" name="submit" value="Add" class="search">
        </td>
      </tr>
    </table>
</div>

<div class="wrap">
<p>You can drag <a href="javascript:void(linkmanpopup=window.open('<?php echo $siteurl; ?>/wp-admin/link-add.php?action=popup&linkurl='+escape(location.href)+'&name='+escape(document.title),'LinkManager','scrollbars=yes,width=750,height=550,left=15,top=15,status=yes,resizable=yes'));linkmanpopup.focus();window.focus();linkmanpopup.focus();" title="Link add bookmarklet">Link This</a> to your toolbar and when you click it a window will pop up that will allow you to add whatever site you're on to your links! Right now this only works on Mozilla or Netscape, but we're working on it.</p>
</div>

<?php
require('admin-footer.php');
?>