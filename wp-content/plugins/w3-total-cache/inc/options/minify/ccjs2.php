<?php

if (!defined('W3TC')) {
    die();
}

$compilation_levels = array(
    'WHITESPACE_ONLY' => __('Whitespace only', 'w3-total-cache'),
    'SIMPLE_OPTIMIZATIONS' => __('Simple optimizations', 'w3-total-cache'),
    'ADVANCED_OPTIMIZATIONS' => __('Advanced optimizations', 'w3-total-cache')
);

$compilation_level = $this->_config->get_string('minify.ccjs.options.compilation_level');
?>
<tr>
    <th><label for="minify_ccjs_path_java"><?php w3_e_config_label('minify.ccjs.path.java') ?></label></th>
    <td>
        <input id="minify_ccjs_path_java" class="js_enabled" type="text" 
           <?php $this->sealing_disabled('minify') ?> name="minify.ccjs.path.java" value="<?php echo esc_attr($this->_config->get_string('minify.ccjs.path.java')); ?>" size="60" />
    </td>
</tr>
<tr>
    <th><label for="minify_ccjs_path_jar"><?php w3_e_config_label('minify.ccjs.path.jar') ?></label></th>
    <td>
        <input id="minify_ccjs_path_jar" class="js_enabled" type="text" 
            <?php $this->sealing_disabled('minify') ?> name="minify.ccjs.path.jar" value="<?php echo esc_attr($this->_config->get_string('minify.ccjs.path.jar')); ?>" size="60" />
    </td>
</tr>
<tr>
    <th>&nbsp;</th>
    <td>
        <input class="minifier_test js_enabled button {type: 'ccjs', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="<?php _e('Test Closure Compiler', 'w3-total-cache'); ?>" />
        <span class="minifier_test_status w3tc-status w3tc-process"></span>
    </td>
</tr>
<tr>
    <th><label for="minify_ccjs_options_compilation_level"><?php w3_e_config_label('minify.ccjs.options.compilation_level') ?></label></th>
    <td>
        <select id="minify_ccjs_options_compilation_level" class="js_enabled" name="minify.ccjs.options.compilation_level" 
            <?php $this->sealing_disabled('minify') ?>>
            <?php foreach ($compilation_levels as $compilation_level_key => $compilation_level_name): ?>
            <option value="<?php echo esc_attr($compilation_level_key); ?>" <?php selected($compilation_level, $compilation_level_key); ?>><?php echo $compilation_level_name; ?></option>
            <?php endforeach; ?>
        </select>
    </td>
</tr>
