<?php if (!defined('W3TC')) die(); ?>
<input type="hidden" name="minify.ccjs.options.formatting" value="" />
<label>
    <input class="js_enabled" type="checkbox" name="minify.ccjs.options.formatting" 
        value="pretty_print"
         <?php checked($this->_config->get_string('minify.ccjs.options.formatting'), 'pretty_print'); ?> 
         <?php $this->sealing_disabled('minify') ?> /> <?php w3_e_config_label('minify.ccjs.options.formatting') ?>
</label>
<br />
