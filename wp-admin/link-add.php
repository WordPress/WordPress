<?php
require_once('admin.php');

$title = __('Add Link');
$this_file = 'link-manager.php';
$parent_file = 'link-manager.php';

function category_dropdown($fieldname, $selected = 0) {
	global $wpdb;
	
	$results = $wpdb->get_results("SELECT cat_id, cat_name, auto_toggle FROM $wpdb->linkcategories ORDER BY cat_id");
	echo "\n<select name='$fieldname' size='1'>\n";
	foreach ($results as $row) {
		echo "\n\t<option value='$row->cat_id'";
		if ($row->cat_id == $selected)
			echo " selected='selected'";
		echo ">$row->cat_id : " . wp_specialchars($row->cat_name);
		if ($row->auto_toggle == 'Y')
			echo ' (auto toggle)';
		echo "</option>";
	}
	echo "\n</select>\n";
}

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

$wpvarstoreset = array('action', 'cat_id', 'linkurl', 'name', 'image',
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

<?php if ($_GET['added']) : ?>
<div class="updated"><p><?php _e('Link added.'); ?></p></div>
<?php endif; ?>
<div class="wrap">
<h2><?php _e('<strong>Add</strong> a link:') ?></h2>
     <form name="addlink" method="post" action="link-manager.php">
<fieldset class="options">
	<legend><?php _e('Basics') ?></legend>
        <table class="editform" width="100%" cellspacing="2" cellpadding="5">
         <tr>
           <th width="33%" scope="row"><?php _e('URI:') ?></th>
           <td width="67%"><input type="text" name="linkurl" value="<?php echo wp_specialchars($_GET['linkurl'], 1); ?>" style="width: 95%;" /></td>
         </tr>
         <tr>
           <th scope="row"><?php _e('Link Name:') ?></th>
           <td><input type="text" name="name" value="<?php echo wp_specialchars( urldecode($_GET['name']), 1 ); ?>" style="width: 95%" /></td>
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
              <th scope="row"> <?php _e('family'); ?> </th>
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
