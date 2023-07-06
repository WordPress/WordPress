<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $WOOBE;
?>

<input type="checkbox" <?php if (!intval($WOOBE->settings->load_switchers)): ?>onclick="woobe_click_checkbox(this, '<?php echo $numcheck ?>')"<?php endif; ?> data-numcheck='<?php echo $numcheck ?>' data-true='<?php echo $labels['true'] ?>' data-false='<?php echo $labels['false'] ?>' data-val-true='<?php echo $vals['true'] ?>' data-val-false='<?php echo $vals['false'] ?>' data-trigger-target='<?php echo $trigger_target ?>' data-class-true='label-inverse-success' data-class-false='label-inverse-default' class="js-switch js-check-change" <?php checked($is) ?> />
<input type="hidden" id="js_check_<?php echo $numcheck ?>" data-hidden-numcheck='<?php echo $numcheck ?>' value="<?php echo $vals[($is ? 'true' : 'false')] ?>" name="<?php echo $name ?>" />
<label data-label-numcheck='<?php echo $numcheck ?>' class="label <?php echo $css_classes ?> <?php echo ($is ? 'label-inverse-success' : 'label-inverse-default') ?> check-change js-check-change-field"><?php echo ($is ? $labels['true'] : $labels['false']) ?></label>
