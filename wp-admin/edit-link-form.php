<?php
/**
 * Edit links form for inclusion in administration panels.
 *
 * @package WordPress
 * @subpackage Administration
 */

if ( ! empty($link_id) ) {
	$heading = sprintf( __( '<a href="%s">Links</a> / Edit Link' ), 'link-manager.php' );
	$submit_text = __('Update Link');
	$form = '<form name="editlink" id="editlink" method="post" action="link.php">';
	$nonce_action = 'update-bookmark_' . $link_id;
} else {
	$heading = sprintf( __( '<a href="%s">Links</a> / Add New Link' ), 'link-manager.php' );
	$submit_text = __('Add Link');
	$form = '<form name="addlink" id="addlink" method="post" action="link.php">';
	$nonce_action = 'add-bookmark';
}

/**
 * Display checked checkboxes attribute for xfn microformat options.
 *
 * @since 1.0.1
 *
 * @param string $class
 * @param string $value
 * @param mixed $deprecated Not used.
 */
function xfn_check($class, $value = '', $deprecated = '') {
	global $link;

	$link_rel = isset( $link->link_rel ) ? $link->link_rel : ''; // In PHP 5.3: $link_rel = $link->link_rel ?: '';
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

/**
 * Display link create form fields.
 *
 * @since 2.7.0
 *
 * @param object $link
 */
function link_submit_meta_box($link) {
?>
<div class="submitbox" id="submitlink">

<div id="minor-publishing">

<?php // Hidden submit button early on so that the browser chooses the right button when form is submitted with Return key ?>
<div style="display:none;">
<input type="submit" name="save" value="<?php echo attribute_escape( __('Save') ); ?>" />
</div>

<div id="minor-publishing-actions">
<div id="preview-action">
<?php if ( !empty($link->link_id) ) { ?>
	<a class="preview button" href="<?php echo $link->link_url; ?>" target="_blank" tabindex="4"><?php _e('Visit Link'); ?></a>
<?php } ?>
</div>
<div class="clear"></div>
</div>

<div id="misc-publishing-actions">
<div class="misc-pub-section misc-pub-section-last">
	<label for="link_private" class="selectit"><input id="link_private" name="link_visible" type="checkbox" value="N" <?php checked($link->link_visible, 'N'); ?> /> <?php _e('Keep this link private') ?></label>
</div>
</div>

</div>

<div id="major-publishing-actions">
<?php do_action('post_submitbox_start'); ?>
<div id="delete-action">
<?php
if ( !empty($_GET['action']) && 'edit' == $_GET['action'] && current_user_can('manage_links') ) { ?>
	<a class="submitdelete deletion" href="<?php echo wp_nonce_url("link.php?action=delete&amp;link_id=$link->link_id", 'delete-bookmark_' . $link->link_id); ?>" onclick="if ( confirm('<?php echo js_escape(sprintf(__("You are about to delete this link '%s'\n  'Cancel' to stop, 'OK' to delete."), $link->link_name )); ?>') ) {return true;}return false;"><?php _e('Delete'); ?></a>
<?php } ?>
</div>

<div id="publishing-action">
<?php if ( !empty($link->link_id) ) { ?>
	<input name="save" type="submit" class="button-primary" id="publish" tabindex="4" accesskey="p" value="<?php _e('Update Link') ?>" />
<?php } else { ?>
	<input name="save" type="submit" class="button-primary" id="publish" tabindex="4" accesskey="p" value="<?php _e('Add Link') ?>" />
<?php } ?>
</div>
<div class="clear"></div>
</div>
<?php do_action('submitlink_box'); ?>
<div class="clear"></div>
</div>
<?php
}
add_meta_box('linksubmitdiv', __('Save'), 'link_submit_meta_box', 'link', 'side', 'core');

/**
 * Display link categories form fields.
 *
 * @since 2.6.0
 *
 * @param object $link
 */
function link_categories_meta_box($link) { ?>
<div id="category-adder" class="wp-hidden-children">
	<h4><a id="category-add-toggle" href="#category-add"><?php _e( '+ Add New Category' ); ?></a></h4>
	<p id="link-category-add" class="wp-hidden-child">
		<label class="hidden" for="newcat"><?php _e( '+ Add New Category' ); ?></label>
		<input type="text" name="newcat" id="newcat" class="form-required form-input-tip" value="<?php _e( 'New category name' ); ?>" aria-required="true" />
		<input type="button" id="category-add-sumbit" class="add:categorychecklist:linkcategorydiv button" value="<?php _e( 'Add' ); ?>" />
		<?php wp_nonce_field( 'add-link-category', '_ajax_nonce', false ); ?>
		<span id="category-ajax-response"></span>
	</p>
</div>

<ul id="category-tabs">
	<li class="ui-tabs-selected"><a href="#categories-all"><?php _e( 'All Categories' ); ?></a></li>
	<li class="hide-if-no-js"><a href="#categories-pop"><?php _e( 'Most Used' ); ?></a></li>
</ul>

<div id="categories-all" class="ui-tabs-panel">
	<ul id="categorychecklist" class="list:category categorychecklist form-no-clear">
		<?php
		if ( isset($link->link_id) )
			wp_link_category_checklist($link->link_id);
		else
			wp_link_category_checklist();
		?>
	</ul>
</div>

<div id="categories-pop" class="ui-tabs-panel" style="display: none;">
	<ul id="categorychecklist-pop" class="categorychecklist form-no-clear">
		<?php wp_popular_terms_checklist('link_category'); ?>
	</ul>
</div>
<?php
}
add_meta_box('linkcategorydiv', __('Categories'), 'link_categories_meta_box', 'link', 'normal', 'core');

/**
 * Display form fields for changing link target.
 *
 * @since 2.6.0
 *
 * @param object $link
 */
function link_target_meta_box($link) { ?>
<fieldset><legend class="hidden"><?php _e('Target') ?></legend>
<label for="link_target_blank" class="selectit">
<input id="link_target_blank" type="radio" name="link_target" value="_blank" <?php echo ( isset( $link->link_target ) && ($link->link_target == '_blank') ? 'checked="checked"' : ''); ?> />
<code>_blank</code></label><br />
<label for="link_target_top" class="selectit">
<input id="link_target_top" type="radio" name="link_target" value="_top" <?php echo ( isset( $link->link_target ) && ($link->link_target == '_top') ? 'checked="checked"' : ''); ?> />
<code>_top</code></label><br />
<label for="link_target_none" class="selectit">
<input id="link_target_none" type="radio" name="link_target" value="" <?php echo ( isset( $link->link_target ) && ($link->link_target == '') ? 'checked="checked"' : ''); ?> />
<?php _e('none') ?></label>
</fieldset>
<p><?php _e('Choose the frame your link targets. Essentially this means if you choose <code>_blank</code> your link will open in a new window.'); ?></p>
<?php
}
add_meta_box('linktargetdiv', __('Target'), 'link_target_meta_box', 'link', 'normal', 'core');

/**
 * Display xfn form fields.
 *
 * @since 2.6.0
 *
 * @param object $link
 */
function link_xfn_meta_box($link) {
?>
<table class="editform" style="width: 100%;" cellspacing="2" cellpadding="5">
	<tr>
		<th style="width: 20%;" scope="row"><label for="link_rel"><?php _e('rel:') ?></label></th>
		<td style="width: 80%;"><input type="text" name="link_rel" id="link_rel" size="50" value="<?php echo ( isset( $link->link_rel ) ? $link->link_rel : ''); ?>" /></td>
	</tr>
	<tr>
		<td colspan="2">
			<table cellpadding="3" cellspacing="5" class="form-table">
				<tr>
					<th scope="row"> <?php _e('identity') ?> </th>
					<td><fieldset><legend class="hidden"> <?php _e('identity') ?> </legend>
						<label for="me">
						<input type="checkbox" name="identity" value="me" id="me" <?php xfn_check('identity', 'me'); ?> />
						<?php _e('another web address of mine') ?></label>
					</fieldset></td>
				</tr>
				<tr>
					<th scope="row"> <?php _e('friendship') ?> </th>
					<td><fieldset><legend class="hidden"> <?php _e('friendship') ?> </legend>
						<label for="contact">
						<input class="valinp" type="radio" name="friendship" value="contact" id="contact" <?php xfn_check('friendship', 'contact', 'radio'); ?> /> <?php _e('contact') ?></label>
						<label for="acquaintance">
						<input class="valinp" type="radio" name="friendship" value="acquaintance" id="acquaintance" <?php xfn_check('friendship', 'acquaintance', 'radio'); ?> />  <?php _e('acquaintance') ?></label>
						<label for="friend">
						<input class="valinp" type="radio" name="friendship" value="friend" id="friend" <?php xfn_check('friendship', 'friend', 'radio'); ?> /> <?php _e('friend') ?></label>
						<label for="friendship">
						<input name="friendship" type="radio" class="valinp" value="" id="friendship" <?php xfn_check('friendship', '', 'radio'); ?> /> <?php _e('none') ?></label>
					</fieldset></td>
				</tr>
				<tr>
					<th scope="row"> <?php _e('physical') ?> </th>
					<td><fieldset><legend class="hidden"> <?php _e('physical') ?> </legend>
						<label for="met">
						<input class="valinp" type="checkbox" name="physical" value="met" id="met" <?php xfn_check('physical', 'met'); ?> />
						<?php _e('met') ?></label>
					</fieldset></td>
				</tr>
				<tr>
					<th scope="row"> <?php _e('professional') ?> </th>
					<td><fieldset><legend class="hidden"> <?php _e('professional') ?> </legend>
						<label for="co-worker">
						<input class="valinp" type="checkbox" name="professional" value="co-worker" id="co-worker" <?php xfn_check('professional', 'co-worker'); ?> />
						<?php _e('co-worker') ?></label>
						<label for="colleague">
						<input class="valinp" type="checkbox" name="professional" value="colleague" id="colleague" <?php xfn_check('professional', 'colleague'); ?> />
						<?php _e('colleague') ?></label>
					</fieldset></td>
				</tr>
				<tr>
					<th scope="row"> <?php _e('geographical') ?> </th>
					<td><fieldset><legend class="hidden"> <?php _e('geographical') ?> </legend>
						<label for="co-resident">
						<input class="valinp" type="radio" name="geographical" value="co-resident" id="co-resident" <?php xfn_check('geographical', 'co-resident', 'radio'); ?> />
						<?php _e('co-resident') ?></label>
						<label for="neighbor">
						<input class="valinp" type="radio" name="geographical" value="neighbor" id="neighbor" <?php xfn_check('geographical', 'neighbor', 'radio'); ?> />
						<?php _e('neighbor') ?></label>
						<label for="geographical">
						<input class="valinp" type="radio" name="geographical" value="" id="geographical" <?php xfn_check('geographical', '', 'radio'); ?> />
						<?php _e('none') ?></label>
					</fieldset></td>
				</tr>
				<tr>
					<th scope="row"> <?php _e('family') ?> </th>
					<td><fieldset><legend class="hidden"> <?php _e('family') ?> </legend>
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
					</fieldset></td>
				</tr>
				<tr>
					<th scope="row"> <?php _e('romantic') ?> </th>
					<td><fieldset><legend class="hidden"> <?php _e('romantic') ?> </legend>
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
					</fieldset></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<p><?php _e('If the link is to a person, you can specify your relationship with them using the above form. If you would like to learn more about the idea check out <a href="http://gmpg.org/xfn/">XFN</a>.'); ?></p>
<?php
}
add_meta_box('linkxfndiv', __('Link Relationship (XFN)'), 'link_xfn_meta_box', 'link', 'normal', 'core');

/**
 * Display advanced link options form fields.
 *
 * @since 2.6.0
 *
 * @param object $link
 */
function link_advanced_meta_box($link) {
?>
<table class="form-table" style="width: 100%;" cellspacing="2" cellpadding="5">
	<tr class="form-field">
		<th valign="top"  scope="row"><label for="link_image"><?php _e('Image Address') ?></label></th>
		<td><input type="text" name="link_image" id="link_image" size="50" value="<?php echo ( isset( $link->link_image ) ? $link->link_image : ''); ?>" style="width: 95%" /></td>
	</tr>
	<tr class="form-field">
		<th valign="top"  scope="row"><label for="rss_uri"><?php _e('RSS Address') ?></label></th>
		<td><input name="link_rss" type="text" id="rss_uri" value="<?php echo  ( isset( $link->link_rss ) ? $link->link_rss : ''); ?>" size="50" style="width: 95%" /></td>
	</tr>
	<tr class="form-field">
		<th valign="top"  scope="row"><label for="link_notes"><?php _e('Notes') ?></label></th>
		<td><textarea name="link_notes" id="link_notes" cols="50" rows="10" style="width: 95%"><?php echo  ( isset( $link->link_notes ) ? $link->link_notes : ''); ?></textarea></td>
	</tr>
	<tr class="form-field">
		<th valign="top"  scope="row"><label for="link_rating"><?php _e('Rating') ?></label></th>
		<td><select name="link_rating" id="link_rating" size="1">
		<?php
			for ($r = 0; $r < 10; $r++) {
				echo('            <option value="'.$r.'" ');
				if ( isset($link->link_rating) && $link->link_rating == $r)
					echo 'selected="selected"';
				echo('>'.$r.'</option>');
			}
		?></select>&nbsp;<?php _e('(Leave at 0 for no rating.)') ?>
		</td>
	</tr>
</table>
<?php
}
add_meta_box('linkadvanceddiv', __('Advanced'), 'link_advanced_meta_box', 'link', 'normal', 'core');

do_action('do_meta_boxes', 'link', 'normal', $link);
do_action('do_meta_boxes', 'link', 'advanced', $link);
do_action('do_meta_boxes', 'link', 'side', $link);

require_once ('admin-header.php');

?>


<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo wp_specialchars( $title ); ?></h2>

<?php if ( isset( $_GET['added'] ) ) : ?>
<div id="message" class="updated fade"><p><?php _e('Link added.'); ?></p></div>
<?php endif; ?>

<?php
if ( !empty($form) )
	echo $form;
if ( !empty($link_added) )
	echo $link_added;

wp_nonce_field( $nonce_action );
wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>

<div id="poststuff" class="metabox-holder">

<div id="side-info-column" class="inner-sidebar">
<?php

do_action('submitlink_box');
$side_meta_boxes = do_meta_boxes( 'link', 'side', $link );

?>
</div>

<div id="post-body" class="<?php echo $side_meta_boxes ? 'has-sidebar' : ''; ?>">
<div id="post-body-content" class="has-sidebar-content">
<div id="namediv" class="stuffbox">
<h3><label for="link_name"><?php _e('Name') ?></label></h3>
<div class="inside">
	<input type="text" name="link_name" size="30" tabindex="1" value="<?php echo $link->link_name; ?>" id="link_name" />
    <p><?php _e('Example: Nifty blogging software'); ?></p>
</div>
</div>

<div id="addressdiv" class="stuffbox">
<h3><label for="link_url"><?php _e('Web Address') ?></label></h3>
<div class="inside">
	<input type="text" name="link_url" size="30" tabindex="1" value="<?php echo $link->link_url; ?>" id="link_url" />
    <p><?php _e('Example: <code>http://wordpress.org/</code> &#8212; don&#8217;t forget the <code>http://</code>'); ?></p>
</div>
</div>

<div id="descriptiondiv" class="stuffbox">
<h3><label for="link_description"><?php _e('Description') ?></label></h3>
<div class="inside">
	<input type="text" name="link_description" size="30" tabindex="1" value="<?php echo isset($link->link_description) ? $link->link_description : ''; ?>" id="link_description" />
    <p><?php _e('This will be shown when someone hovers over the link in the blogroll, or optionally below the link.'); ?></p>
</div>
</div>

<?php

do_meta_boxes('link', 'normal', $link);

do_meta_boxes('link', 'advanced', $link);

if ( $link_id ) : ?>
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
</div>
