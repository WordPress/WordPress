<?php if (!defined('W3TC')) die(); ?>
<p>
	<label>
        How many items would you like to display?
    	<input type="text" name="w3tc_widget_latest_items" value="<?php echo esc_attr($this->_config->get_integer('widget.latest.items')); ?>" size="5" class="w3tc-ignore-change"/>
    </label>
</p>
