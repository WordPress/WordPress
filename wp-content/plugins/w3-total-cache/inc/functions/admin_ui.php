<?php
/**
 * Returns button html
 *
 * @param string $text
 * @param string $onclick
 * @param string $class
 * @return string
 */
function w3_button($text, $onclick = '', $class = 'button') {
    return sprintf('<input type="button" class="%s" value="%s" onclick="%s" />', htmlspecialchars($class), htmlspecialchars($text), htmlspecialchars($onclick));
}

/**
 * Returns button link html
 *
 * @param string $text
 * @param string $url
 * @param boolean $new_window
 * @return string
 */
function w3_button_link($text, $url, $new_window = false, $class = 'button') {
    $url = str_replace('&amp;', '&', $url);

    if ($new_window) {
        $onclick = sprintf('window.open(\'%s\');', addslashes($url));
    } else {
        $onclick = sprintf('document.location.href=\'%s\';', addslashes($url));
    }

    return w3_button($text, $onclick, $class);
}

/**
 * Returns hide note button html
 *
 * @param string $text
 * @param string $note
 * @param string $redirect
 * @param boolean $admin if to use config admin
 * @param string $page
 * @param string $custom_method
 * @return string
 */
function w3_button_hide_note($text, $note, $redirect = '', $admin = false, $page = '', $custom_method = 'w3tc_default_hide_note') {
    if ($page == '') {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');
        $page = W3_Request::get_string('page', 'w3tc_dashboard');
    }

    $url = sprintf('admin.php?page=%s&%s&note=%s', $page, $custom_method, $note);

    if ($admin)
        $url .= '&admin=1';

    if ($redirect != '') {
        $url .= '&redirect=' . urlencode($redirect);
    }

    $url = wp_nonce_url($url, 'w3tc');

    return w3_button_link($text, $url);
}

function w3tc_cancel_button($note, $classes = '', $custom_method = 'w3tc_default_hide_note' ) {
    w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');
    $page = W3_Request::get_string('page', 'w3tc_dashboard');

$url = sprintf('admin.php?page=%s&%s&note=%s', $page, $custom_method, $note);

$url = wp_nonce_url($url, 'w3tc');

return w3_button_link(__('Cancel'), $url, false, $classes);
}

function w3tc_action_button($action, $url, $class = '') {
    return w3_button_link($action, $url, false, $class);
}
/**
 * Returns popup button html
 *
 * @param string $text
 * @param string $action
 * @param string $params
 * @param integer $width
 * @param integer $height
 * @return string
 */
function w3_button_popup($text, $action, $params = '', $width = 800, $height = 600) {
    $url = wp_nonce_url(sprintf('admin.php?page=w3tc_dashboard&w3tc_%s%s', $action, ($params != '' ? '&' . $params : '')), 'w3tc');
    $url = str_replace('&amp;', '&', $url);

    $onclick = sprintf('window.open(\'%s\', \'%s\', \'width=%d,height=%d,status=no,toolbar=no,menubar=no,scrollbars=yes\');', $url, $action, $width, $height);

    return w3_button($text, $onclick);
}

/**
 * Returns nonce field HTML
 *
 * @param string $action
 * @param string $name
 * @param bool $referer
 * @internal param bool $echo
 * @return string
 */
function w3_nonce_field($action = -1, $name = '_wpnonce', $referer = true) {
    $name = esc_attr($name);
    $return = '<input type="hidden" name="' . $name . '" value="' . wp_create_nonce($action) . '" />';

    if ($referer) {
        $return .= wp_referer_field(false);
    }

    return $return;
}

/**
 * @param string $body http response body
 */
function w3_in_plugin_update_message($body) {
    $matches = null;
    $regexp = '~==\s*Changelog\s*==\s*=\s*[0-9.]+\s*=(.*)(=\s*' . preg_quote(W3TC_VERSION) . '\s*=|$)~Uis';

    if (preg_match($regexp, $body, $matches)) {
        $changelog = (array) preg_split('~[\r\n]+~', trim($matches[1]));

        echo '<div style="color: #f00;">' . __('Take a minute to update, here\'s why:', 'w3-total-cache') . '</div><div style="font-weight: normal;height:300px;overflow:auto">';
        $ul = false;

        foreach ($changelog as $index => $line) {
            if (preg_match('~^\s*\*\s*~', $line)) {
                if (!$ul) {
                    echo '<ul style="list-style: disc; margin-left: 20px;margin-top:0px;">';
                    $ul = true;
                }
                $line = preg_replace('~^\s*\*\s*~', '', htmlspecialchars($line));
                echo '<li style="width: 50%; margin: 0; float: left; ' . ($index % 2 == 0 ? 'clear: left;' : '') . '">' . $line . '</li>';
            } else {
                if ($ul) {
                    echo '</ul><div style="clear: left;"></div>';
                    $ul = false;
                }
            }
        }

        if ($ul) {
            echo '</ul><div style="clear: left;"></div>';
        }

        echo '</div>';
    }
}

/**
 * Prints the label string for a config key.
 * @param string $config_key
 * @param string $area
 */
function w3_e_config_label($config_key, $area = 'settings') {
    echo w3_config_label($config_key, $area);
}

/**
 * Returns the label string for config key.
 * @param string $config_key
 * @param string $area
 * @return string
 */
function w3_config_label($config_key, $area) {
    /**
     * @var W3_UI_Settings_SettingsHandler $module
     */
    $module = w3_instance("W3_UI_Settings_SettingsHandler");
    return $module->get_label($config_key, $area);
}

/**
 * Returns the meta data for a config key.
 * @param $config_key
 * @return mixed
 */
function w3_config_meta($config_key) {
    /**
     * @var W3_UI_Settings_SettingsHandler $module
     */
    $module = w3_instance("W3_UI_Settings_SettingsHandler");
    return $module->get_meta($config_key);
}

/**
 * @param $config_key
 * @param $meta
 * @return mixed
 */
function w3_config_can_change($config_key, $meta) {
    /**
     * @var W3_UI_Settings_SettingsHandler $module
     */
    $module = w3_instance("W3_UI_Settings_SettingsHandler");
    return $module->can_change($config_key, $meta);
}

function w3tc_show_notification($id) {
    return isset($_GET['w3tc_show_note']) && $_GET['w3tc_show_note'] == $id;
}





/**
 * Returns postbox header
 *
 * @param string $title
 * @param string $class
 * @param string $id
 * @return string
 */
function w3tc_postbox_header($title, $class = '', $id = '') {
    if ( !empty( $id ) ) {
        $id = ' id="' . esc_attr( $id ) . '"';
    }
    return '<div' . $id . ' class="postbox ' . $class . '"><div class="handlediv" title="' . __('Click to toggle', 'w3-total-cache') . '"><br /></div><h3 class="hndle"><span>' . $title . '</span></h3><div class="inside">';
}

/**
 * Returns postbox footer
 *
 * @return string
 */
function w3tc_postbox_footer() {
    return '</div></div>';
}


/**
 * Prints checkbox with config option value
 *
 * @param string $option_id
 * @param bool $disabled
 * @param string $class_prefix
 * @param bool $label
 */
function w3tc_checkbox($option_id, $disabled = false, $class_prefix = '', $label = true) {
    $config = w3_instance('W3_Config');
    $section = substr($option_id, 0, strpos($option_id, '.'));

    $disabled = $disabled || w3tc_is_sealed($section);

    if (!$disabled)
        echo '<input type="hidden" name="' . $option_id . '" value="0" />';

    $name = str_replace('.', '_', $option_id);

    if ($label)
        echo '<label>';
    echo '<input class="'.$class_prefix.'enabled" type="checkbox" id="' . $name .
        '" name="' . $option_id . '" value="1" ';
    checked($config->get_boolean($option_id), true);

    if ($disabled)
        echo 'disabled="disabled" ';

    echo ' />';
}

/**
 * Prints a radio button and if config value matches value
 * @param string $option_id config id
 * @param $value
 * @param bool $disabled
 * @param string $class_prefix
 */
function w3tc_radio($option_id, $value, $disabled = false, $class_prefix = ''){
    $config = w3_instance('W3_Config');
    $section = substr($option_id, 0, strpos($option_id, '.'));

    if(is_bool($value))
        $rValue = $value?'1':'0';
    else
        $rValue = $value;
    $disabled = $disabled || w3tc_is_sealed($section);

    $name = str_replace('.', '_', $option_id);

    echo '<label>';
    echo '<input class="'.$class_prefix.'enabled" type="radio" id="' . $name .
        '" name="' . $option_id . '" value="',$rValue,'" ';
    checked($config->get_boolean($option_id), $value);

    if ($disabled)
        echo 'disabled="disabled" ';

    echo ' />';
}


/**
 * Returns true if config section is sealed
 * @param string $section
 * @return boolean
 */
function w3tc_is_sealed($section) {
    $config = w3_instance('W3_Config');
    $config_master = new W3_Config(true);
    $config_admin = w3_instance('W3_ConfigAdmin');

    if ($config->is_master())
        return false;

    if (w3_is_network() && !$config->is_master() && w3_force_master())
        return true;

    // browsercache settings change rules, so not available in child settings
    if ($section == 'browsercache')
        return true;

    if ($section == 'minify' && !$config_master->get_boolean('minify.enabled'))
        return true;

    return $config_admin->get_boolean($section . '.configuration_sealed');
}

function w3tc_sealing_disabled($section) {
    if (w3tc_is_sealed($section))
        echo 'disabled="disabled" ';
}

/**
 * Prints checkbox with admin config option value
 *
 * @param string $option_id
 * @param boolean $disabled
 */
function w3tc_checkbox_admin($option_id, $disabled = false) {
    $config = w3_instance('W3_Config');
    $config_admin = w3_instance('W3_ConfigAdmin');
    if (!$disabled)
        $disabled = $config->get_boolean('common.force_master');
    $checked = $config_admin->get_boolean($option_id) || $disabled;
    if (!$disabled)
        echo '<input type="hidden" name="' . $option_id . '" value="0" />';

    echo '<label>';
    $id = str_replace('.', '_', $option_id);
    $class = $disabled ? 'disabled' : 'enabled';
    echo '<input id="' . $id . '" class="' . $class . '" type="checkbox" name="' . $option_id .
        '" value="1"';
    checked($checked, true);
    if ($disabled)
        echo ' disabled="disabled"';

    echo ' />';
}


/**
 * Returns nonce field HTML
 *
 * @param string $action
 * @param string $name
 * @param bool $referer
 * @internal param bool $echo
 * @return string
 */
function w3tc_nonce_field($action = -1, $name = '_wpnonce', $referer = true) {
    $name = esc_attr($name);
    $return = '<input type="hidden" name="' . $name . '" value="' . wp_create_nonce($action) . '" />';

    if ($referer) {
        $return .= wp_referer_field(false);
    }

    return $return;
}
