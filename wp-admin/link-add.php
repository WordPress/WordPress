<?php

$title = 'Add Link';
$this_file = 'link-manager.php';
$parent_file = 'link-manager.php';

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
$link_url = stripslashes($HTTP_GET_VARS['linkurl']);
$link_name = htmlentities(stripslashes(urldecode($HTTP_GET_VARS['name'])));

require('admin-header.php');
?>
<ul id="adminmenu2">
	<li><a href="link-manager.php">Manage Links</a></li>
	<li><a href="link-add.php" class="current">Add Link</a></li>
	<li><a href="link-categories.php">Link Categories</a></li>
	<li class="last"><a href="link-import.php">Import Blogroll</a></li>
</ul>
<style type="text/css" media="screen">
th { text-align: right; }
</style>
<div class="wrap">
<h2><strong>Add</strong> a link: <?php echo gethelp_link($this_file,'add_a_link');?></h2>
     <form name="addlink" method="post" action="link-manager.php">
<fieldset class="options">
	<legend>Basics</legend>
        <table class="editform" width="100%" cellspacing="2" cellpadding="5">
         <tr>
           <th width="33%" scope="row">URI:</th>
           <td width="67%"><input type="text" name="linkurl" value="<?php echo $_GET['linkurl']; ?>" style="width: 95%; /"></td>
         </tr>
         <tr>
           <th scope="row">Link Name:</th>
           <td><input type="text" name="name" value="<?php echo urldecode($_GET['name']); ?>" style="width: 95%" /></td>
         </tr>
         <tr>
         	<th scope="row">Short description:</th>
         	<td><input type="text" name="description" value="" style="width: 95%" /></td>
         	</tr>
        <tr>
           <th scope="row">Category:</th>
           <td><?php category_dropdown('category'); ?></td>
         </tr>
</table>
</fieldset>
       <p class="submit">
         <input type="submit" name="submit" value="Add Link &raquo;" /> 
       </p>
	<fieldset class="options">
	<legend>Link Relationship (XFN)</legend>
        <table class="editform" width="100%" cellspacing="2" cellpadding="5">
            <tr>
            	<th width="33%" scope="row">rel:</th>
            	<td width="67%"><input type="text" name="rel" id="rel" size="50" value=""></td>
           	</tr>
            <tr>
            	<th scope="row"><a href="http://gmpg.org/xfn/">XFN</a> Creator:</th>
            	<td><table cellpadding="3" cellspacing="5">
            			<tr>
            				<th scope="row"> friendship </th>
            				<td><label for="label">
            					<input class="valinp" type="radio" name="friendship" value="acquaintance" id="label"  />
					acquaintance</label>
                					<label for="label2">
                					<input class="valinp" type="radio" name="friendship" value="friend" id="label2" />
					friend</label>
                					<label for="label3">
                					<input class="valinp" type="radio" name="friendship" value="" id="label3" />
					none</label>
            					</td>
           				</tr>
            			<tr>
            				<th scope="row"> physical </th>
            				<td><label for="label4">
            					<input class="valinp" type="checkbox" name="physical" value="met" id="label4" />
					met</label>
            					</td>
           				</tr>
            			<tr>
            				<th scope="row"> professional </th>
            				<td><label for="label5">
            					<input class="valinp" type="checkbox" name="professional" value="co-worker" id="label5" />
					co-worker</label>
                					<label for="label6">
                					<input class="valinp" type="checkbox" name="professional" value="colleague" id="label6" />
					colleague</label>
            					</td>
           				</tr>
            			<tr>
            				<th scope="row"> geographical </th>
            				<td><label for="label7">
            					<input class="valinp" type="radio" name="geographical" value="co-resident" id="label7" />
					co-resident</label>
                					<label for="label8">
                					<input class="valinp" type="radio" name="geographical" value="neighbor" id="label8" />
					neighbor</label>
                					<label for="label9">
                					<input class="valinp" type="radio" name="geographical" value="" id="label9" />
					none</label>
            					</td>
           				</tr>
            			<tr>
            				<th scope="row"> family </th>
            				<td><label for="label10">
            					<input class="valinp" type="radio" name="family" value="child" id="label10" />
					child</label>
                					<label for="label11">
                					<input class="valinp" type="radio" name="family" value="parent" id="label11" />
					parent</label>
                					<label for="label12">
                					<input class="valinp" type="radio" name="family" value="sibling" id="label12" />
					sibling</label>
                					<label for="label13">
                					<input class="valinp" type="radio" name="family" value="spouse" id="label13" />
					spouse</label>
                					<label for="label14">
                					<input class="valinp" type="radio" name="family" value="" id="label14" />
					none</label>
            					</td>
           				</tr>
            			<tr>
            				<th scope="row"> romantic </th>
            				<td><label for="label15">
            					<input class="valinp" type="checkbox" name="romantic" value="muse" id="label15" />
					muse</label>
                					<label for="label16">
                					<input class="valinp" type="checkbox" name="romantic" value="crush" id="label16" />
					crush</label>
                					<label for="label17">
                					<input class="valinp" type="checkbox" name="romantic" value="date" id="label17" />
					date</label>
                					<label for="label18">
                					<input class="valinp" type="checkbox" name="romantic" value="sweetheart" id="label18" />
					sweetheart</label>
            					</td>
           				</tr>
            			</table></td>
           	</tr>
</table>
</fieldset>
       <p class="submit">
         <input type="submit" name="submit" value="Add Link &raquo;" /> 
       </p>
<fieldset class="options">
	<legend>Advanced</legend>
        <table class="editform" width="100%" cellspacing="2" cellpadding="5">
         <tr>
           <th width="33%" scope="row">Image URI:</th>
           <td width="67%"><input type="text" name="image" size="50" value="" style="width: 95%" /></td>
         </tr>
<tr>
           <th scope="row">RSS URI: </th>
           <td><input name="rss_uri" type="text" id="rss_uri" value="" size="50" style="width: 95%" /></td>
         </tr>
         <tr>
           <th scope="row">Notes:</th>
           <td><textarea name="notes" cols="50" rows="10" style="width: 95%"></textarea></td>
         </tr>
         <tr>
           <th scope="row">Rating:</th>
           <td><select name="rating" size="1">
             <?php
    for ($r = 0; $r < 10; $r++) {
      echo('            <option value="'.$r.'">'.$r.'</option>');
    }
?>
           </select>
           &nbsp;(Leave at 0 for no rating.) </td>
         </tr>
         <tr>
           <th scope="row">Target</th>
           <td><label>
             <input type="radio" name="target" value="_blank" />
             <code>_blank</code></label>
<br />
<label><input type="radio" name="target" value="_top" />
<code>_top</code></label>
<br />
<label><input type="radio" name="target" value="" checked="checked" />
none</label>
(Note that the <code>target</code> attribute is illegal in XHTML 1.1 and 1.0 Strict.)</td>
         </tr>
         <tr>
           <th scope="row">Visible:</th>
           <td><label>
             <input type="radio" name="visible" checked="checked" value="Y" />
Yes</label><br />
<label><input type="radio" name="visible" value="N"> <input type="hidden" name="action" value="Add" /> 
No</label></td>
         </tr>
</table>
</fieldset>

       <p class="submit">
         <input type="submit" name="submit" value="Add Link &raquo;" /> 
       </p>
  </form>
</div>

<div class="wrap">
<p>You can drag <a href="javascript:void(linkmanpopup=window.open('<?php echo get_settings('siteurl'); ?>/wp-admin/link-add.php?action=popup&linkurl='+escape(location.href)+'&name='+escape(document.title),'LinkManager','scrollbars=yes,width=750,height=550,left=15,top=15,status=yes,resizable=yes'));linkmanpopup.focus();window.focus();linkmanpopup.focus();" title="Link add bookmarklet">Link This</a> to your toolbar and when you click it a window will pop up that will allow you to add whatever site you're on to your links! Right now this only works on Mozilla or Netscape, but we're working on it.</p>
</div>

<?php
require('admin-footer.php');
?>