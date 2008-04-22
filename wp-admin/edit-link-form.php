<?php
if ( ! empty($link_id) ) {
	$heading = __('Edit Link');
	$submit_text = __('Save Changes');
	$form = '<form name="editlink" id="editlink" method="post" action="link.php">';
	$nonce_action = 'update-bookmark_' . $link_id;
} else {
	$heading = __('Add Link');
	$submit_text = __('Add Link');
	$form = '<form name="addlink" id="addlink" method="post" action="link.php">';
	$nonce_action = 'add-bookmark';
}

function xfn_check($class, $value = '', $deprecated = '') {
	global $link;

	$link_rel = $link->link_rel;
	$rels = preg_split('/\s+/', $link_rel);

	if ('' != $value && in_array($value, $rels) ) {
		echo ' checked="checked"';
	}

	if ('' == $value) {
		if ('family' == $class && strpos($link_rel, 'child') === false && strpos($link_rel, 'parent') === false && strpos($link_rel, 'sibling') === false && strpos($link_rel, 'spouse') === false && strpos($link_rel, 'kin') === false) echo ' checked="checked"';
		if ('friendship' == $class && strpos($link_rel, 'friend') === false && strpos($link_rel, 'acquaintance') === false && strpos($link_rel, 'contact') === false) echo ' checked="checked"';
		if ('geographical' == $class && strpos($link_rel, 'co-resident') === false && strpos($link_rel, 'neighbor') === false) echo ' checked="checked"';
		if ('identity' == $class && in_array('me', $rels) ) echo ' checked="checked"';
	}
}
?>

<?php echo $form ?>
<?php wp_nonce_field($nonce_action); ?>
<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>

<div class="wrap">
<h2><?php echo $heading; ?></h2>

<div id="poststuff">

<div class="submitbox" id="submitlink">

<div id="previewview">
<?php if ( !empty($link_id) ) { ?>
<a href="<?php echo $link->link_url; ?>" target="_blank"><?php _e('Visit Link'); ?></a>
<?php } ?>
</div>

<div class="inside">
<p><label for="link_private" class="selectit"><input id="link_private" name="link_visible" type="checkbox" value="N" <?php checked($link->link_visible, 'N'); ?> /> <?php _e('Keep this link private') ?></label></p>
</div>

<p class="submit">
<input type="submit" class="button button-highlighted" name="save" value="<?php _e('Save'); ?>" tabindex="4" />
<?php
if ( ( 'edit' == $action) && current_user_can('manage_links') )
	echo "<a class='submitdelete' href='" . wp_nonce_url("link.php?action=delete&amp;link_id=$link_id", 'delete-bookmark_' . $link_id) . "' onclick=\"if ( confirm('" . js_escape( sprintf( __("You are about to delete this link '%s'\n'Cancel' to stop, 'OK' to delete."), $link->link_name )) . "') ) { return true;}return false;\">" . __('Delete&nbsp;link') . "</a>";
?>
</p>

<div class="side-info">
<h5><?php _e('Related') ?></h5>

<ul>
<li><a href="link-manager.php"><?php _e('Manage All Links') ?></a></li>
<li><a href="edit-link-categories.php"><?php _e('Manage All Link Categories') ?></a></li>
<li><a href="link-import.php"><?php _e('Import Links') ?></a></li>
<?php do_action('link_relatedlinks_list'); ?>
</ul>
</div>
<?php do_action('submitlink_box'); ?>
</div>

<div id="post-body">
<div id="namediv" class="stuffbox">
<h3><?php _e('Name') ?></h3>
<div class="inside">
	<input type="text" name="link_name" size="30" tabindex="1" value="<?php echo $link->link_name; ?>" id="link_name" /><br />
    <?php _e('Example: Nifty blogging software'); ?>
</div>
</div>

<div id="addressdiv" class="stuffbox">
<h3><?php _e('Web Address') ?></h3>
<div class="inside">
	<input type="text" name="link_url" size="30" tabindex="1" value="<?php echo $link->link_url; ?>" id="link_url" /><br />
    <?php _e('Example: <code>http://wordpress.org/</code> &#8212; don&#8217;t forget the <code>http://</code>'); ?>
</div>
</div>

<div id="descriptiondiv" class="stuffbox">
<h3><?php _e('Description') ?></h3>
<div class="inside">
	<input type="text" name="link_description" size="30" tabindex="1" value="<?php echo $link->link_description; ?>" id="link_description" /><br />
    <?php _e('This will be shown when someone hovers over the link in the blogroll, or optionally below the link.'); ?>
</div>
</div>

<div id="linkcategorydiv" class="postbox <?php echo postbox_classes('linkcategorydiv', 'link'); ?>">
<h3><?php _e('Categories') ?></h3>
<div class="inside">

<div id="category-adder" class="wp-hidden-children">
	<h4><a id="category-add-toggle" href="#category-add"><?php _e( '+ Add New Category' ); ?></a></h4>
	<p id="link-category-add" class="wp-hidden-child">
		<input type="text" name="newcat" id="newcat" class="form-required form-input-tip" value="<?php _e( 'New category name' ); ?>" />
		<input type="button" id="category-add-sumbit" class="add:categorychecklist:linkcategorydiv button" value="<?php _e( 'Add' ); ?>" />
		<?php wp_nonce_field( 'add-link-category', '_ajax_nonce', false ); ?>
		<span id="category-ajax-response"></span>
	</p>
</div>

<ul id="category-tabs">
	<li class="ui-tabs-selected"><a href="#categories-all"><?php _e( 'All Categories' ); ?></a></li>
	<li class="wp-no-js-hidden"><a href="#categories-pop"><?php _e( 'Most Used' ); ?></a></li>
</ul>

<div id="categories-all" class="ui-tabs-panel">
	<ul id="categorychecklist" class="list:category categorychecklist form-no-clear">
		<?php wp_link_category_checklist($link_id); ?>
	</ul>
</div>

<div id="categories-pop" class="ui-tabs-panel" style="display: none;">
	<ul id="categorychecklist-pop" class="categorychecklist form-no-clear">
		<?php wp_popular_terms_checklist('link_category'); ?>
	</ul>
</div>

</div>
</div>

<?php do_meta_boxes('link', 'normal', $link); ?>

<h2><?php _e('Advanced Options'); ?></h2>

<div id="linktargetdiv" class="postbox <?php echo postbox_classes('linktargetdiv', 'link'); ?>">
<h3><?php _e('Target') ?></h3>
<div class="inside">
<label for="link_target_blank" class="selectit">
<input id="link_target_blank" type="radio" name="link_target" value="_blank" <?php echo(($link->link_target == '_blank') ? 'checked="checked"' : ''); ?> />
<code>_blank</code></label><br />
<label for="link_target_top" class="selectit">
<input id="link_target_top" type="radio" name="link_target" value="_top" <?php echo(($link->link_target == '_top') ? 'checked="checked"' : ''); ?> />
<code>_top</code></label><br />
<label for="link_target_none" class="selectit">
<input id="link_target_none" type="radio" name="link_target" value="" <?php echo(($link->link_target == '') ? 'checked="checked"' : ''); ?> />
<?php _e('none') ?></label>
<p><?php _e('Choose the frame your link targets. Essentially this means if you choose <code>_blank</code> your link will open in a new window.'); ?></p>
</div>
</div>

<div id="linkxfndiv" class="postbox <?php echo postbox_classes('linkxfndiv', 'link'); ?>">
<h3><?php _e('Link Relationship (XFN)') ?></h3>
<div class="inside">
<table class="editform" style="width: 100%;" cellspacing="2" cellpadding="5">
	<tr>
		<th style="width: 20%;" scope="row"><?php _e('rel:') ?></th>
		<td style="width: 80%;"><input type="text" name="link_rel" id="link_rel" size="50" value="<?php echo $link->link_rel; ?>" /></td>
	</tr>
	<tr>
		<td colspan="2">
			<table cellpadding="3" cellspacing="5" class="form-table">
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
						<label for="friend">
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
<p><?php _e('If the link is to a person, you can specify your relationship with them using the above form. If you would like to learn more about the idea check out <a href="http://gmpg.org/xfn/">XFN</a>.'); ?></p>
</div>
</div>

<div id="linkadvanceddiv" class="postbox <?php echo postbox_classes('linkadvanceddiv', 'link'); ?>">
<h3><?php _e('Advanced') ?></h3>
<div class="inside">
<table class="form-table" style="width: 100%;" cellspacing="2" cellpadding="5">
	<tr class="form-field">
		<th valign="top"  scope="row"><label for="link_image"><?php _e('Image Address') ?></label></th>
		<td><input type="text" name="link_image" id="link_image" size="50" value="<?php echo $link->link_image; ?>" style="width: 95%" /></td>
	</tr>
	<tr class="form-field">
		<th valign="top"  scope="row"><label for="rss_uri"><?php _e('RSS Address') ?></label></th>
		<td><input name="link_rss" type="text" id="rss_uri" value="<?php echo $link->link_rss; ?>" size="50" style="width: 95%" /></td>
	</tr>
	<tr class="form-field">
		<th valign="top"  scope="row"><label for="link_notes"><?php _e('Notes') ?></label></th>
		<td><textarea name="link_notes" id="link_notes" cols="50" rows="10" style="width: 95%"><?php echo $link->link_notes; ?></textarea></td>
	</tr>
	<tr class="form-field">
		<th valign="top"  scope="row"><label for="link_rating"><?php _e('Rating') ?></label></th>
		<td><select name="link_rating" id="link_rating" size="1">
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
</div>
</div>

<?php do_meta_boxes('link', 'advanced', $link); ?>

<?php if ( $link_id ) : ?>
<input type="hidden" name="action" value="save" />
<input type="hidden" name="link_id" value="<?php echo (int) $link_id; ?>" />
<input type="hidden" name="order_by" value="<?php echo attribute_escape($order_by); ?>" />
<input type="hidden" name="cat_id" value="<?php echo (int) $cat_id ?>" />
<?php else: ?>
<input type="hidden" name="action" value="add" />
<?php endif; ?>

</div>
</div>

</div>

</form>
