<?php
if ( ! empty($link_id) ) {
	$heading = __('Edit Bookmark');
	$submit_text = __('Save Changes &raquo;');
	$form = '<form name="editlink" id="editlink" method="post" action="link.php">';
	$nonce_action = 'update-bookmark_' . $link_id;
} else {
	$heading = __('Create Bookmark');
	$submit_text = __('Add Bookmark &raquo;');
	$form = '<form name="addlink" id="addlink" method="post" action="link.php">';
	$nonce_action = 'add-bookmark';
}

function xfn_check($class, $value = '', $type = 'check') {
	global $link;

	$link_rel = $link->link_rel;
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
?>

<div class="wrap"> 
<h2><?php echo $heading ?></h2>
<?php echo $form ?>
<?php wp_nonce_field($nonce_action); ?>

<div id="poststuff">
<div id="moremeta">
<div id="grabit" class="dbx-group">

<fieldset id="categorydiv" class="dbx-box">
<h3 class="dbx-handle"><?php _e('Categories') ?></h3>
<div class="dbx-content">
<p id="jaxcat"></p>
<ul id="categorychecklist"><?php dropdown_categories(get_settings('default_link_category')); ?></ul>
</div>
</fieldset>

<fieldset class="dbx-box">
<h3 class="dbx-handle"><?php _e('Target') ?></h3> 
<div class="dbx-content">
<label for="link_target_blank" class="selectit">
<input id="link_target_blank" type="radio" name="link_target" value="_blank" <?php echo(($link->link_target == '_blank') ? 'checked="checked"' : ''); ?> />
<code>_blank</code></label>
<label for="link_target_top" class="selectit">
<input id="link_target_top" type="radio" name="link_target" value="_top" <?php echo(($link->link_target == '_top') ? 'checked="checked"' : ''); ?> />
<code>_top</code></label>
<label for="link_target_none" class="selectit">
<input id="link_target_none" type="radio" name="link_target" value="" <?php echo(($link->link_target == '') ? 'checked="checked"' : ''); ?> />
<?php _e('none') ?></label>
</div>
</fieldset>

<fieldset class="dbx-box">
<h3 class="dbx-handle"><?php _e('Visible') ?></h3> 
<div class="dbx-content">
<label for="link_visible_yes" class="selectit">
<input id="link_visible_yes" type="radio" name="link_visible" <?php if ($link->link_visible == 'Y') echo "checked='checked'"; ?> value="Y" />
<?php _e('Yes') ?></label>
<label for="link_visible_no" class="selectit">
<input id="link_visible_no" type="radio" name="link_visible" <?php if ($link->link_visible == 'N') echo "checked='checked'"; ?> value="N" />
<?php _e('No') ?></label>
</div>
</fieldset>

</div>
</div>

<table class="editform" width="100%" cellspacing="2" cellpadding="5">
<tr>
<th width="20%" scope="row" valign="top"><label for="link_url"><?php _e('URI:') ?></label></th>
<td width="80%"><input type="text" name="link_url" value="<?php echo $link->link_url; ?>" style="width: 95%" /></td>
</tr>
<tr>
<th scope="row" valign="top"><label for="link_name"><?php _e('Name:') ?></label></th>
<td><input type="text" name="link_name" value="<?php echo $link->link_name; ?>" style="width: 95%" /></td>
</tr>
<tr>
<th scope="row" valign="top"><label for="link_description"><?php _e('Description:') ?></label></th>
<td><input type="text" name="link_description" value="<?php echo $link->link_description; ?>" style="width: 95%" /></td>
</tr>
</table>

<p class="submit">
<input type="submit" name="submit" value="<?php echo $submit_text ?>" />
</p>

<div id="advancedstuff" class="dbx-group" >

<fieldset id="xfn" class="dbx-box">
<h3 class="dbx-handle"><?php _e('Link Relationship (XFN)') ?></h3>
<div class="dbx-content">
<table class="editform" width="100%" cellspacing="2" cellpadding="5">
	<tr>
		<th width="20%" scope="row"><?php _e('rel:') ?></th>
		<td width="80%"><input type="text" name="link_rel" id="link_rel" size="50" value="<?php echo $link->link_rel; ?>" /></td>
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
					<th scope="row"> <?php _e('family') ?> </th>
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
</div>
</fieldset>

<fieldset id="advanced" class="dbx-box">
<h3 class="dbx-handle"><?php _e('Advanced') ?></h3>
<div class="dbx-content">
<table class="editform" width="100%" cellspacing="2" cellpadding="5">
	<tr>
		<th width="20%" scope="row"><?php _e('Image URI:') ?></th>
		<td width="80%"><input type="text" name="link_image" size="50" value="<?php echo $link->link_image; ?>" style="width: 95%" /></td>
	</tr>
	<tr>
		<th scope="row"><?php _e('RSS URI:') ?> </th>
		<td><input name="link_rss" type="text" id="rss_uri" value="<?php echo $link->link_rss; ?>" size="50" style="width: 95%" /></td>
	</tr>
	<tr>
		<th scope="row"><?php _e('Notes:') ?></th>
		<td><textarea name="link_notes" cols="50" rows="10" style="width: 95%"><?php echo $link->link_notes; ?></textarea></td>
	</tr>
	<tr>
		<th scope="row"><?php _e('Rating:') ?></th>
		<td><select name="link_rating" size="1">
		<?php
			for ($r = 0; $r < 10; $r++) {
				echo('            <option value="'.$r.'" ');
				if ($link->link_rating == $r)
					echo 'selected="selected"';
				echo('>'.$r.'</option>');
			}
		?></select>&nbsp;<?php _e('(Leave at 0 for no rating.)') ?>
		</td>
	</tr>
</table>
</fieldset>
</div>

<?php if ( $link_id ) : ?>
<input type="hidden" name="action" value="save" />
<input type="hidden" name="link_id" value="<?php echo (int) $link_id; ?>" />
<input type="hidden" name="order_by" value="<?php echo wp_specialchars($order_by, 1); ?>" />
<input type="hidden" name="cat_id" value="<?php echo (int) $cat_id ?>" />
<?php else: ?>
<input type="hidden" name="action" value="add" />
<?php endif; ?>
</div>
</form>
</div>
