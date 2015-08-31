<?php
/**
 * Fork of the WordPress settings handling.
 */

/**
 * Prints out all settings sections added to a particular settings page
 *
 * Part of the Settings API. Use this in a settings page callback function
 * to output all the sections and fields that were added to that $page with
 * add_settings_section() and add_settings_field()
 *
 * @global array $wp_settings_sections Storage array of all settings sections added to admin pages
 * @global array $wp_settings_fields Storage array of settings fields and info about their pages/sections
 *
 * @param string $page The slug name of the page whos settings sections you want to output
 *                       for extensions its the extension id
 */
function w3tc_do_settings_sections( $page ) {
    global $w3tc_settings_sections, $w3tc_settings_fields;

    if ( ! isset( $w3tc_settings_sections ) || !isset( $w3tc_settings_sections[$page] ) )
        return;

    foreach ( (array) $w3tc_settings_sections[$page] as $section ) {
        if ( $section['title'] )
            echo "<h4>{$section['title']}</h4>\n";
        do_action("w3tc_do_settings_sections_after_title-{$page}");
        if ( $section['callback'] )
            call_user_func( $section['callback'], $section );

        if ( ! isset( $w3tc_settings_fields ) || !isset( $w3tc_settings_fields[$page] ) || !isset( $w3tc_settings_fields[$page][$section['id']] ) )
            continue;
        do_action("w3tc_do_settings_sections_before_table-{$page}");
        echo '<table class="form-table">';
        w3tc_do_settings_fields( $page, $section['id'] );
        echo '</table>';
        do_action("w3tc_do_settings_sections_after_table-{$page}");
    }
}

/**
 * Print out the settings fields for a particular settings section
 *
 * Part of the Settings API. Use this in a settings page to output
 * a specific section. Should normally be called by do_settings_sections()
 * rather than directly.
 *
 * @global array $wp_settings_fields Storage array of settings fields and their pages/sections
 *
 * @param string $page Slug title of the admin page who's settings fields you want to show.
 * @param string $section Slug title of the settings section who's fields you want to show.
 */
function w3tc_do_settings_fields($page, $section) {
    global $w3tc_settings_fields;

    if ( !isset($w3tc_settings_fields) || !isset($w3tc_settings_fields[$page]) || !isset($w3tc_settings_fields[$page][$section]) )
        return;

    foreach ( (array) $w3tc_settings_fields[$page][$section] as $id => $field ) {
        echo '<tr valign="top">';
        if ( !empty($field['args']['label_for']) )
            echo '<th scope="row"><label for="' . esc_attr( $field['args']['label_for'] ) . '">' . $field['title'] . '</label></th>';
        else
            echo '<th scope="row">' . $field['title'] . '</th>';
        echo '<td>';
        call_user_func($field['callback'], $id, $field['args']);

        if ( !empty($field['args']['description'])) {
                echo '<br />';
            echo '<span class="description">', esc_html($field['args']['description']), '</span>';
        }
        echo '</td>';
        echo '</tr>';
    }
}

/**
 * Register a setting and its sanitization callback
 *
 * @param string $option_group A settings group name.
 * @param string $option_name The name of an option to sanitize and save.
 * @param string $sanitize_callback A callback function that sanitizes the option's value.
 * @return void
 */
function w3tc_register_setting( $option_group, $option_name, $sanitize_callback = '' ) {
    global $w3tc_new_whitelist_options;

    $w3tc_new_whitelist_options[ $option_group ][] = $option_name;
    if ( $sanitize_callback != '' )
        add_filter( "w3tc_sanitize_option_{$option_name}", $sanitize_callback );
}

/**
 * Add a new section to a settings page.
 *
 * Part of the Settings API. Use this to define new settings sections for an admin page.
 * Show settings sections in your admin page callback function with do_settings_sections().
 * Add settings fields to your section with add_settings_field()
 *
 * The $callback argument should be the name of a function that echoes out any
 * content you want to show at the top of the settings section before the actual
 * fields. It can output nothing if you want.
 *
 *
 * @global array $wp_settings_sections Storage array of all settings sections added to admin pages
 *
 * @param string $id Slug-name to identify the section. Used in the 'id' attribute of tags.
 * @param string $title Formatted title of the section. Shown as the heading for the section.
 * @param string $callback Function that echos out any content at the top of the section (between heading and fields).
 * @param string $page The slug-name of the settings page on which to show the section. Built-in pages include 'general', 'reading', 'writing', 'discussion', 'media', etc. Create your own using add_options_page();
 */
function w3tc_add_settings_section($id, $title, $callback, $page) {
    global $w3tc_settings_sections;

    if ( !isset($w3tc_settings_sections) )
        $w3tc_settings_sections = array();
    if ( !isset($w3tc_settings_sections[$page]) )
        $w3tc_settings_sections[$page] = array();
    if ( !isset($w3tc_settings_sections[$page][$id]) )
        $w3tc_settings_sections[$page][$id] = array();

    $w3tc_settings_sections[$page][$id] = array('id' => $id, 'title' => $title, 'callback' => $callback);
}

/**
 * Add a new field to a section of a settings page
 *
 * Part of the Settings API. Use this to define a settings field that will show
 * as part of a settings section inside a settings page. The fields are shown using
 * do_settings_fields() in do_settings-sections()
 *
 * The $callback argument should be the name of a function that echoes out the
 * html input tags for this setting field. Use get_option() to retrieve existing
 * values to show.
 * *
 * @global array $w3tc_settings_fields Storage array of settings fields and info about their pages/sections
 *
 * @param string $id Slug-name to identify the field. Used in the 'id' attribute of tags.
 * @param string $title Formatted title of the field. Shown as the label for the field during output.
 * @param string $callback Function that fills the field with the desired form inputs. The function should echo its output.
 * @param string $page The slug-name of the settings page on which to show the section (general, reading, writing, ...).
 * @param string $section The slug-name of the section of the settings page in which to show the box (default, ...).
 * @param array $args Additional arguments
 */
function w3tc_add_settings_field($id, $title, $callback, $page, $section = 'default', $args = array()) {
    global $w3tc_settings_fields;

    if ( !isset($w3tc_settings_fields) )
        $w3tc_settings_fields = array();
    if ( !isset($w3tc_settings_fields[$page]) )
        $w3tc_settings_fields[$page] = array();
    if ( !isset($w3tc_settings_fields[$page][$section]) )
        $w3tc_settings_fields[$page][$section] = array();

    $w3tc_settings_fields[$page][$section][$id] = array('id' => $id, 'title' => $title, 'callback' => $callback, 'args' => $args);
}

/**
 * Takes the extension id and the setting and returns corresponding name and id to be used in HTML
 *
 * @param $extension_id
 * @param $setting
 * @return array(name, id)
 */
function w3tc_get_name_and_id($extension_id, $setting) {
    $name = "extensions.settings.$extension_id.$setting";
    $id = str_replace('.','_', $name);
    return array($name, $id);
}

/**
 * Loads the admin related part of core extensions
 */
function w3_extensions_admin_init() {
    static $loaded = false;
    if ($loaded)
        return;

    $loaded = true;

    $folder_path = W3TC_DIR . '/extensions/';
    foreach (glob($folder_path . "*Admin.php") as $filename) {
        include $filename;
    }
    do_action('w3tc_extensions_admin_init');
}

/**
 * Check if an extension is loaded
 * @param $extension
 * @return bool
 */
function w3_is_extension_active($extension) {
    $config = w3_instance('W3_Config');
    $extensions = $config->get_array('extensions.active');
    return array_key_exists($extension, $extensions);
}

/**
 * Get registered extensions
 * @param $config
 * @return array
 */
function w3_get_extensions($config) {
    return apply_filters("w3tc_extensions", __return_empty_array(), $config);
}

/**
 * Returns the inactive extensions
 * @param $config
 * @return array
 */
function w3_get_inactive_extensions($config) {
    $extensions = w3_get_extensions($config);
    $config = w3_instance('W3_Config');
    $active_extensions = $config->get_array('extensions.active');
    return array_diff_key($extensions, $active_extensions);
}

/**
 * Returns the active extensions
 * @param $config
 * @return array
 */
function w3_get_active_extensions($config) {
    $extensions = w3_get_extensions($config);
    $extensions_keys = array_keys($extensions);
    $config = w3_instance('W3_Config');
    $active_extensions = $config->get_array('extensions.active');
    return array_intersect_key($extensions, $active_extensions);
}

/**
 * Checks if an extension is sealed by Network Admin
 * @param $extension
 * @return bool
 */
function w3_extension_is_sealed($extension) {
    if (is_network_admin() || !w3_is_multisite())
        return false;
    $config_admin = w3_instance('W3_ConfigAdmin');
    $all_checked = $config_admin->get_array('extensions.configuration_sealed');
    return isset($all_checked[$extension]) && $all_checked[$extension];
}

/**
 * @param $extension_name
 * @param $extension_id
 */
function w3_e_extension_activation_notification($extension_name, $extension_id) {
    w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/ui.php');
    $page = w3tc_get_current_page();
    printf(
        w3_get_notification_box('<p>' . 
            __('It appears that activating the <a href="%s">%s</a> extension for W3 Total Cache will be helpful for your site. <a class="button" href="%s">Click here</a> to try it. %s', 'w3-total-cache') . '</p>', $extension_id)
        , sprintf(w3_admin_url('admin.php?page=w3tc_extensions#%s'), $extension_id)
        , $extension_name
        , sprintf(w3_admin_url('admin.php?page='. $page .'&w3tc_extensions_activate=%s'), $extension_id)
            , w3_button_hide_note(__('Hide this message', 'w3-total-cache'), 'hide-extension-notification', '', true,'','w3tc_default_hide_note_custom='.$extension_id)
    );
}

/**
 * Checks if an extension notification is hidden
 *
 * @param $extension_id
 * @return bool
 */
function w3tc_extension_hidden($extension_id) {
    $w3_config = w3_instance('W3_ConfigAdmin');
    $extensions = $w3_config->get_array('notes.hide_extensions');
    return in_array($extension_id, $extensions);
}


/**
 * @param $extension
 * @param W3_Config $w3_config
 * @return bool
 */
function w3tc_activate_extension($extension, $w3_config) {
    $all_extensions = w3_get_extensions($w3_config);
    $extensions = $w3_config->get_array('extensions.active');
    if (!w3_is_extension_active($extension)) {
        $meta = $all_extensions[$extension];
        $extensions[$extension] = $meta['path'];

        ksort($extensions, SORT_STRING);
        $w3_config->set('extensions.active', $extensions);
        try {
            $w3_config->save();
            do_action("w3tc_activate_extension-{$extension}");
            return true;
        } catch (Exception $ex) {
        }
    }
    return false;
}


/**
 * @param $extension
 * @param W3_Config $config
 * @param bool $dont_save_config
 * @return bool
 */
function w3tc_deactivate_extension($extension, $config, $dont_save_config = false) {
    $extensions = $config->get_array('extensions.active');
    if (array_key_exists($extension, $extensions)) {
        unset($extensions[$extension]);
        ksort($extensions, SORT_STRING);
        $config->set('extensions.active', $extensions);
        try {
            if (!$dont_save_config)
                $config->save();
            do_action("w3tc_deactivate_extension-{$extension}");
            return true;
        } catch (Exception $ex) {}
    }
    return false;
}

/**
 * @param string $extension_id
 * @param bool $valid if site can handle extension
 * @return bool
 */
function w3tc_show_extension_notification($extension_id, $valid) {
    return !w3_is_extension_active($extension_id) && $valid && !w3tc_extension_hidden($extension_id);
}
