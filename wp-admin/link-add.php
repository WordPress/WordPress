<?php
require_once('../wp-includes/wp-l10n.php');

$title = 'Add Link';
$this_file = 'link-manager.php';
$parent_file = 'link-manager.php';

function category_dropdown($fieldname, $selected = 0) {
    global $wpdb;

    $results = $wpdb->get_results("SELECT cat_id, cat_name, auto_toggle FROM $wpdb->linkcategories ORDER BY cat_id");
    echo '        <select name="'.$fieldname.'" size="1">'."\n";
    foreach ($results as $row) {
      echo "          <option value=\"".$row->cat_id."\"";
      if ($row->cat_id == $selected)
        echo " selected";
        echo ">".$row->cat_id.": ".htmlspecialchars($row->cat_name);
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
    $_GET    = add_magic_quotes($_GET);
    $_POST   = add_magic_quotes($_POST);
    $_COOKIE = add_magic_quotes($_COOKIE);
}

$wpvarstoreset = array('action','standalone','cat_id', 'linkurl', 'name', 'image',
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
$link_url = stripslashes($_GET['linkurl']);
$link_name = htmlentities(stripslashes(urldecode($_GET['name'])));


$xfn = true;
require('admin-header.php');
?>
<ul id="adminmenu2">
	<li><a href="link-manager.php"><?php _e('Manage Links') ?></a></li>
        <li><a href="link-add.php" class="current"><?php _e('Add Link') ?></a></li>
	<li><a href="link-categories.php"><?php _e('Link Categories') ?></a></li>
	<li class="last"><a href="link-import.php"><?php _e('Import Blogroll') ?></a></li>
</ul>
<style type="text/css" media="screen">
th { text-align: right; }
</style>
<?php if ($_GET['added']) : ?>
<div class="updated"><p>Link added.</p></div>
<?php endif; ?>
<div class="wrap">
<h2><?php _e('<strong>Add</strong> a link:') ?></h2>
     <form name="addlink" method="post" action="link-manager.php">
<fieldset class="options">
	<legend><?php _e('Basics') ?></legend>
        <table class="editform" width="100%" cellspacing="2" cellpadding="5">
         <tr>
           <th width="33%" scope="row"><?php _e('URI:') ?></th>
           <td width="67%"><input type="text" name="linkurl" value="<?php echo $_GET['linkurl']; ?>" style="width: 95%;" /></td>
         </tr>
         <tr>
           <th scope="row"><?php _e('Link Name:') ?></th>
           <td><input type="text" name="name" value="<?php echo urldecode($_GET['name']); ?>" style="width: 95%" /></td>
         </tr>
         <tr>
         	<th scope="row"><?php _e('Short description:') ?></th>
         	<td><input type="text" name="description" value="" style="width: 95%" /></td>
         	</tr>
        <tr>
           <th scope="row"><?php _e('Category:') ?></th>
           <td><?php category_dropdown('category'); ?></td>
         </tr>
</table>
</fieldset>
       <p class="submit">
         <input type="submit" name="submit" value="<?php _e('Add Link &raquo;') ?>" /> 
       </p>
	<fieldset class="options">
	<legend><?php _e('Link Relationship (XFN)') ?></legend>
        <table class="editform" width="100%" cellspacing="2" cellpadding="5">
            <tr>
            	<th width="33%" scope="row"><?php _e('rel:') ?></th>
            	<td width="67%"><input type="text" name="rel" id="rel" size="50" value="" /></td>
           	</tr>
            <tr>
            	<th scope="row"><?php _e('<a href="http://gmpg.org/xfn/">XFN</a> Creator:') ?></th>
            	<td><table cellpadding="3" cellspacing="5">
            			<tr>
            				<th scope="row"> <?php _e('friendship') ?> </th>
            				<td>
            				<label for="label1">
							<input class="valinp" type="radio" name="friendship" value="acquaintance" id="label1" />
							<?php _e('acquaintance') ?></label>
							<label for="label2">
							<input class="valinp" type="radio" name="friendship" value="contact" id="label2" />
							<?php _e('contact') ?></label>
							<label for="label3">
							<input class="valinp" type="radio" name="friendship" value="friend" id="label3" />
							<?php _e('friend') ?></label>
							<label for="label4">
                					<input class="valinp" type="radio" name="friendship" value="" id="label4" />
					<?php _e('none') ?></label>
            					</td>
           				</tr>
            			<tr>
            				<th scope="row"> <?php _e('physical') ?> </th>
            				<td><label for="label10">
            					<input class="valinp" type="checkbox" name="physical" value="met" id="label10" />
					<?php _e('met') ?></label>
            					</td>
           				</tr>
            			<tr>
            				<th scope="row"> <?php _e('professional') ?> </th>
            				<td><label for="label20">
            					<input class="valinp" type="checkbox" name="professional" value="co-worker" id="label20" />
					<?php _e('co-worker') ?></label>
                					<label for="label21">
                					<input class="valinp" type="checkbox" name="professional" value="colleague" id="label21" />
					<?php _e('colleague') ?></label>
            					</td>
           				</tr>
            			<tr>
            				<th scope="row"> <?php _e('geographical') ?> </th>
            				<td><label for="label30">
            					<input class="valinp" type="radio" name="geographical" value="co-resident" id="label30" />
					<?php _e('co-resident') ?></label>
                					<label for="label31">
                					<input class="valinp" type="radio" name="geographical" value="neighbor" id="label31" />
					<?php _e('neighbor') ?></label>
                					<label for="label32">
                					<input class="valinp" type="radio" name="geographical" value="" id="label32" />
					<?php _e('none') ?></label>
            					</td>
           				</tr>
            			<tr>
            				<th scope="row"> <?php _e('family') ?> </th>
            				<td>
            						<label for="label40">
            					<input class="valinp" type="radio" name="family" value="child" id="label40" />
					<?php _e('child') ?></label>
            						<label for="label41">
            					<input class="valinp" type="radio" name="family" value="kin" id="label41" />
					<?php _e('kin') ?></label>
                					<label for="label42">
                					<input class="valinp" type="radio" name="family" value="parent" id="label42" />
					<?php _e('parent') ?></label>
                					<label for="label43">
                					<input class="valinp" type="radio" name="family" value="sibling" id="label43" />
					<?php _e('sibling') ?></label>
                					<label for="label44">
                					<input class="valinp" type="radio" name="family" value="spouse" id="label44" />
					<?php _e('spouse') ?></label>
                					<label for="label45">
                					<input class="valinp" type="radio" name="family" value="" id="label45" />
					<?php _e('none') ?></label>
            					</td>
           				</tr>
            			<tr>
            				<th scope="row"> <?php _e('romantic') ?> </th>
            				<td><label for="label50">
            					<input class="valinp" type="checkbox" name="romantic" value="muse" id="label50" />
					<?php _e('muse') ?></label>
                					<label for="label51">
                					<input class="valinp" type="checkbox" name="romantic" value="crush" id="label51" />
					<?php _e('crush') ?></label>
                					<label for="label52">
                					<input class="valinp" type="checkbox" name="romantic" value="date" id="label42" />
					<?php _e('date') ?></label>
                					<label for="label53">
                					<input class="valinp" type="checkbox" name="romantic" value="sweetheart" id="label53" />
					<?php _e('sweetheart') ?></label>
            					</td>
           				</tr>
            			<tr>
            				<th scope="row"> <?php _e('identity') ?> </th>
            				<td><label for="label60">
            					<input class="valinp" type="checkbox" name="identity" value="me" id="label60" />
					<?php _e('me') ?></label>
            					</td>
           				</tr>
            			</table></td>
           	</tr>
</table>
</fieldset>
       <p class="submit">
         <input type="submit" name="submit" value="<?php _e('Add Link &raquo;') ?>" /> 
       </p>
<fieldset class="options">
	<legend><?php _e('Advanced') ?></legend>
        <table class="editform" width="100%" cellspacing="2" cellpadding="5">
         <tr>
           <th width="33%" scope="row"><?php _e('Image URI:') ?></th>
           <td width="67%"><input type="text" name="image" size="50" value="" style="width: 95%" /></td>
         </tr>
<tr>
           <th scope="row"><?php _e('RSS URI:') ?> </th>
           <td><input name="rss_uri" type="text" id="rss_uri" value="" size="50" style="width: 95%" /></td>
         </tr>
         <tr>
           <th scope="row"><?php _e('Notes:') ?></th>
           <td><textarea name="notes" cols="50" rows="10" style="width: 95%"></textarea></td>
         </tr>
         <tr>
           <th scope="row"><?php _e('Rating:') ?></th>
           <td><select name="rating" size="1">
             <?php
    for ($r = 0; $r < 10; $r++) {
      echo('            <option value="'.$r.'">'.$r.'</option>');
    }
?>
           </select>
           &nbsp;<?php _e('(Leave at 0 for no rating.)') ?> </td>
         </tr>
         <tr>
           <th scope="row"><?php _e('Target') ?></th>
           <td><label>
             <input type="radio" name="target" value="_blank" />
             <code>_blank</code></label>
<br />
<label><input type="radio" name="target" value="_top" />
<code>_top</code></label>
<br />
<label><input type="radio" name="target" value="" checked="checked" />
<?php _e('none') ?></label>
<?php _e('(Note that the <code>target</code> attribute is illegal in XHTML 1.1 and 1.0 Strict.)') ?></td>
         </tr>
         <tr>
           <th scope="row"><?php _e('Visible:') ?></th>
           <td><label>
             <input type="radio" name="visible" checked="checked" value="Y" />
<?php _e('Yes') ?></label><br />
<label><input type="radio" name="visible" value="N" /> <input type="hidden" name="action" value="Add" /> 
<?php _e('No') ?></label></td>
         </tr>
</table>
</fieldset>

       <p class="submit">
         <input type="submit" name="submit" value="<?php _e('Add Link &raquo;') ?>" /> 
       </p>
  </form>
</div>

<div class="wrap">
<?php printf(__('<p>You can drag <a href="%s" title="Link add bookmarklet">Link This</a> to your toolbar and when you click it a window will pop up that will allow you to add whatever site you&#8217;re on to your links! Right now this only works on Mozilla or Netscape, but we&#8217;re working on it.</p>'), "javascript:void(linkmanpopup=window.open('" . get_settings('siteurl') . "/wp-admin/link-add.php?action=popup&amp;linkurl='+escape(location.href)+'&amp;name='+escape(document.title),'LinkManager','scrollbars=yes,width=750,height=550,left=15,top=15,status=yes,resizable=yes'));linkmanpopup.focus();window.focus();linkmanpopup.focus();") ?>
</div>

<?php
require('admin-footer.php');
?>
