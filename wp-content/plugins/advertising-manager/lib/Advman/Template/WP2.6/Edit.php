<?php
class Advman_Template_Edit
{
	function display($ad, $nw = false)
	{
		$target = $nw ? strtolower(get_class($ad)) : $ad->id;
		$mode = $nw ? 'edit_network' : 'edit_ad';
		$revisions = ($nw) ? $ad->get_network_property('revisions') : $ad->get_property('revisions');
		list($last_user, $last_timestamp, $last_timestamp2) = Advman_Tools::get_last_edit($revisions);
?>

<form action="" method="post" id="advman-form" enctype="multipart/form-data">
<input type="hidden" name="advman-mode" id="advman-mode" value="<?php echo $mode; ?>">
<input type="hidden" name="advman-action" id="advman-action">
<input type="hidden" name="advman-target" id="advman-target" value="<?php echo $target; ?>">
<?php  
	wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );  
	wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );  
?><div class="wrap">
<?php if ($nw): ?>
	<h2><?php printf(__('Edit %s Network Settings', 'advman'), "<span class='" . strtolower(get_class($ad)) . "'>" . $ad->network_name . "</span>"); ?></h2>
<?php else: ?>
	<h2><?php printf(__('Edit Settings for %s Ad:', 'advman'), $ad->network_name); ?> <span class="<?php echo strtolower(get_class($ad)); ?>"><?php echo "[{$ad->id}] " . $ad->name; ?></span></h2>
<?php endif; ?>		
<div id="poststuff">
<div class="submitbox" id="submitpost">
<div id="previewview">
<?php if (!$nw): ?>
	<a id='advman-ad-preview' href="<?php echo $ad->get_preview_url(); ?>" target="wp_preview"><?php _e('Preview this Ad', 'advman'); ?></a>	
<?php endif; ?>		
</div><!-- previewview -->

<div class="inside">
<?php if (!$nw): ?>
	<p><strong><label for='post_status'><?php _e('Ad Status'); ?></label></strong></p>
	<p>
		<select name='advman-active' id='post_status'>
			<option<?php echo ($ad->active ? " selected='selected'" : ""); ?> value='yes'><?php _e('Active', 'advman'); ?></option>
			<option<?php echo ($ad->active ? "" : " selected='selected'"); ?> value='no'><?php _e('Paused', 'advman'); ?></option>
		</select>
	</p>
<?php endif; ?>		
	<p class="curtime"><?php echo __('Last edited', 'advman') . ' <abbr title="' . $last_timestamp2 . '"><b>' . $last_timestamp . ' ' . __('ago', 'advman') . '</b></abbr> ' . __('by', 'advman') . ' ' . $last_user; ?></p>
</div><!-- inside -->

	<div style="white-space:nowrap">
	<p class="submit">
	<input type="button" value="<?php _e('Cancel', 'advman'); ?>" onclick="document.getElementById('advman-action').value='cancel'; this.form.submit();">
	<input type="button" value="<?php _e('Apply', 'advman'); ?>" onclick="document.getElementById('advman-action').value='apply'; this.form.submit();">
	<input type="submit" value="<?php _e('Save &raquo;', 'advman'); ?>" class="button button-highlighted" onclick="document.getElementById('advman-action').value='save';" />
	</p>
	</div>

<div class="side-info">
	<h5><?php _e('Shortcuts', 'advman'); ?></h5>
	<ul>
<?php if ($nw) : ?>
<?php if (!empty($ad->url)) : ?>
		<li><a href="<?php echo $ad->url; ?>"><?php printf(__('%s home page', 'advman'), $ad->network_name); ?></a></li>
<?php endif; ?>
		<li><a href="javascript:submit();" onclick="document.getElementById('advman-action').value='reset'; document.getElementById('advman-target').value='<?php echo strtolower(get_class($ad)); ?>'; document.getElementById('advman-form').submit();"><?php printf(__('Reset %s settings to defaults', 'advman'), $ad->network_name); ?></a></li>
<?php else : ?>
		<li><a href="javascript:submit();" onclick="if(confirm('<?php printf(__('You are about to copy the %s ad:', 'advman'), $ad->networkName); ?>\n\n  <?php echo '[' . $ad->id . '] ' . $ad->name; ?>\n\n<?php _e('Are you sure?', 'advman'); ?>\n<?php _e('(Press Cancel to do nothing, OK to copy)', 'advman'); ?>')){document.getElementById('advman-action').value='copy'; document.getElementById('advman-form').submit(); } else {return false;}"><?php _e('Copy this ad', 'advman'); ?></a></li>
		<li><a href="javascript:submit();" onclick="if(confirm('<?php printf(__('You are about to permanently delete the %s ad:', 'advman'), $ad->network_name); ?>\n\n  <?php echo '[' . $ad->id . '] ' . $ad->name; ?>\n\n<?php _e('Are you sure?', 'advman'); ?>\n<?php _e('(Press Cancel to keep, OK to delete)', 'advman'); ?>')){document.getElementById('advman-action').value='delete'; document.getElementById('advman-form').submit(); } else {return false;}"><?php _e('Delete this ad', 'advman'); ?></a></li>
		<li><a href="javascript:submit();" onclick="document.getElementById('advman-action').value='edit'; document.getElementById('advman-target').value='<?php echo strtolower(get_class($ad)); ?>'; document.getElementById('advman-form').submit();"><?php printf(__('Edit %s Defaults', 'advman'), $ad->network_name); ?></a></li>
<?php endif; ?>
	</ul>

<?php $notes = $nw ? $ad->get_network_property('notes') : $ad->get_property('notes'); ?>
	<h5><?php _e('Notes', 'advman'); ?></h5>
	<label for="ad_code"><?php _e('Display any notes about this ad here:', 'advman'); ?></label><br /><br />
	<textarea rows="8" cols="22" name="advman-notes" id="advman-notes"><?php echo $notes; ?></textarea><br />
</div><!-- side-info -->
</div><!-- submitpost -->

<div id="post-body">
<?php
		
		// Title
		$this->display_title($ad, $nw);
		// Show normal boxes
		do_meta_boxes('advman','main',$ad);
		// Show advanced screen
		$this->display_advanced($ad);
		// Show advanced boxes
		do_meta_boxes('advman','advanced',$ad);
?></div>
</div>
</div>
</form>
</div><!-- wpbody -->
</div><!-- wpcontent -->
</div><!-- wpwrap -->

<?php
	}
	
	function display_title($ad, $nw = false)
	{
?>
<?php if (!$nw) : ?>
<div id="titlediv">
	<h3><label for="title"><?php _e('Name'); ?></label></h3>
<div id="titlewrap">
	<input type="text" name="advman-name" size="30" value="<?php echo $ad->name ?>" id="title" autocomplete="off" />
</div><!-- titlewrap -->
<br />
<span style="font-size:x-small;color:gray;"><?php _e('Enter the name for this ad.', 'advman'); ?> <?php _e('Ads with the same name will rotate according to their relative weights.', 'advman'); ?></span>
</div><!-- titlediv -->
<?php endif; ?>
<?php
	}
	
	function display_advanced($ad)
	{
?><h2><?php _e('Advanced Options', 'advman'); ?></h2>
<?php		
	}
}
