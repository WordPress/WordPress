<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

switch ($type) {
    case 'tabs':
    case 'tabs_radio':
    default :
        $id = uniqid();
        ?>
<input type="<?php echo esc_attr($type == 'tabs_radio' ? "radio" : "checkbox") ?>" <?php checked($checked) ?> name="woof_section_tabs" id="woof_tab_<?php echo esc_attr($key . "_" . $id); ?>">
        <label class="woof_section_tab_label" for="woof_tab_<?php echo esc_attr($key . "_" . $id) ?>" id="woof_<?php echo esc_attr($key . "_" . $id); ?>_content"><?php esc_html_e($title) ?><span>+</span></label>
        <div class="woof_section_tab" class="woof_<?php echo esc_attr($key) ?>_content"> 
        <?php
    }


