<?php if (!defined('W3TC')) die(); ?>
<tr>
    <th><label for="minify_yuijs_path_java"><?php _e('Path to JAVA executable:', 'w3-total-cache'); ?></label></th>
    <td>
        <input id="minify_yuijs_path_java" class="js_enabled" type="text"
           <?php $this->sealing_disabled('minify') ?> name="minify.yuijs.path.java" value="<?php echo esc_attr($this->_config->get_string('minify.yuijs.path.java')); ?>" size="100" />
    </td>
</tr>
<tr>
    <th><label for="minify_yuijs_path_jar"><?php _e('Path to JAR file:', 'w3-total-cache'); ?></label></th>
    <td>
        <input id="minify_yuijs_path_jar" class="js_enabled" type="text"
           <?php $this->sealing_disabled('minify') ?> name="minify.yuijs.path.jar" value="<?php echo esc_attr($this->_config->get_string('minify.yuijs.path.jar')); ?>" size="100" />
    </td>
</tr>
<tr>
    <th>&nbsp;</th>
    <td>
        <input class="minifier_test button js_enabled {type: 'yuijs', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="<?php _e('Test YUI Compressor', 'w3-total-cache'); ?>" />
        <span class="minifier_test_status w3tc-status w3tc-process"></span>
    </td>
</tr>
<tr>
    <th><label for="minify_yuijs_options_line-break"><?php w3_e_config_label('minify.yuijs.options.line-break') ?></label></th>
    <td>
        <input id="minify_yuijs_options_line-break" class="js_enabled"
           type="text" <?php $this->sealing_disabled('minify') ?>
           name="minify.yuijs.options.line-break" value="<?php echo esc_attr($this->_config->get_integer('minify.yuijs.options.line-break')); ?>" size="8" style="text-align: right;" /> <?php _e('symbols (set to 0 to disable)', 'w3-total-cache'); ?>
    </td>
</tr>
