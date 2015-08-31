<?php if (!defined('W3TC')) die(); ?>
<tr>
    <th><label for="minify_htmltidy_options_wrap"><?php w3_e_config_label('minify.htmltidy.options.wrap') ?></label></th>
    <td>
        <input id="minify_htmltidy_options_wrap" class="html_enabled" type="text"
            <?php $this->sealing_disabled('minify') ?> name="minify.htmltidy.options.wrap" value="<?php echo esc_attr($this->_config->get_integer('minify.htmltidy.options.wrap')); ?>" size="8" style="text-align: right;" /> _e('symbols (set to 0 to disable)', 'w3-total-cache'); ?>
    </td>
</tr>
