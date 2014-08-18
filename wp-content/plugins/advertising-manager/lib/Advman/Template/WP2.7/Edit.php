<?php
class Advman_Template_Edit
{
	function display($ad, $nw = false)
	{
		$target = $nw ? strtolower(get_class($ad)) : $ad->id;
		$mode = $nw ? 'edit_network' : 'edit_ad';
        $action = isset($_POST['advman-action']) ? OX_Tools::sanitize($_POST['advman-action'], 'key') : '';
        $msg = false;
        switch ($action) {
            case 'apply' : $msg = __("Ad saved.", "advman"); break;
        }

        if ($msg) {
            ?>
            <div id="message" class="updated fade"><p><strong><?php echo $msg; ?></strong></p></div>
        <?php
        }
        ?>
<div class="wrap">
	<div id="icon-edit" class="icon32"><br /></div>
<?php if ($nw): ?>
	<h2><?php printf(__('Edit %s Network Settings', 'advman'), "<span class='" . strtolower(get_class($ad)) . "'>" . $ad->network_name . "</span>"); ?></h2>
<?php else: ?>
	<h2><?php printf(__('Edit Settings for %s Ad:', 'advman'), $ad->network_name); ?> <span class="<?php echo strtolower(get_class($ad)); ?>"><?php echo "[{$ad->id}] " . $ad->name; ?></span></h2>
<?php endif; ?>		
	<form method="post" id="advman-form">
	<input type="hidden" name="advman-mode" id="advman-mode" value="<?php echo $mode; ?>">
	<input type="hidden" name="advman-action" id="advman-action">
	<input type="hidden" name="advman-target" id="advman-target" value="<?php echo $target; ?>">
<?php  
		wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );  
		wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
?>
	<div id="poststuff" class="metabox-holder has-right-sidebar">

	<div id="side-info-column" class="inner-sidebar">
<?php
		$side_meta_boxes = do_meta_boxes('advman', 'side', $ad);
?>	</div><!-- side-info-column -->
	<div id="post-body" class="<?php echo $side_meta_boxes ? 'has-sidebar' : ''; ?>">
	<div id="post-body-content" class="has-sidebar-content">
<?php
		// Title
		$this->display_title($ad, $nw);
		// Show normal boxes
		do_meta_boxes('advman','main',$ad);
		// Show advanced screen
		$this->display_advanced($ad);
		// Show advanced boxes
		do_meta_boxes('advman','advanced',$ad);
?>	</div><!-- post-body-content -->
	</div><!-- post-body -->
	<br class="clear" />
	</div><!-- poststuff -->
	</form>
	</div><!-- wrap -->
<?php
	}
	
	function display_title($ad, $nw = false)
	{
if (!$nw): ?>
<div id="titlediv">
<div id="titlewrap">
	<input type="text" name="advman-name" size="30" value="<?php echo $ad->name; ?>" id="title" autocomplete="off" />
</div><!-- titlewrap -->
<div class="inside">
	<span style="font-size:x-small;color:gray;"><?php _e('Enter the name for this ad.', 'advman'); ?> <?php _e('Ads with the same name will rotate according to their relative weights.', 'advman'); ?></span>
</div><!-- inside -->
</div><!-- titlediv -->
<?php endif;
	}
	
	function display_advanced($ad)
	{
?><h2><?php _e('Advanced Options', 'advman'); ?></h2>
<?php		
	}
}
?>