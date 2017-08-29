<?php
/*
Plugin Name: adf.ly WordPress Plugin
Plugin URI: http://www.dojo.cc
Description: Monetize your site with <a href="http://adf.ly/?id=6477252">adf.ly</a>, this plugin able to convert your links to adf.ly, no need to do it manually.
Version: 0.2
Author: Internet Marketing Dojo
Author URI: http://dojo.cc
License: GPL2 or Later
*/
/*
adf.ly WordPress Plugin

Options:

- Enable adf.ly
- Convert outgoing links only/all links to adf.ly
- Ad type: Intestitial or banner
- adf.ly id (http://adf.ly/tools.php?easylink)
*/

// Add settings link on plugin page
function dojo_adfly_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=adfly-wordpress-plugin/adfly.php">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
 
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'dojo_adfly_settings_link' );


function dojo_adfly_plugins_get_options() {
    return array(
        'enabled' => get_option('dojo_adfly_plugins_option_enabled'),
        'id' => trim(get_option('dojo_adfly_plugins_option_id')) ?: '-1',
        'popads_enabled' => get_option('dojo_adfly_plugins_option_popads_enabled'),
        'type' => trim(get_option('dojo_adfly_plugins_option_type')) ?: 'int',
        'domain' => trim(get_option('dojo_adfly_plugins_option_domain')) ?: 'adf.ly',
        'nofollow' => get_option('dojo_adfly_plugins_option_nofollow'),
        'website_entry_enabled' => get_option('dojo_adfly_plugins_option_website_entry_enabled'),
        'protocol' => trim(get_option('dojo_adfly_plugins_option_protocol')) ?: 'http',
        'include_exclude_domains_choose' => get_option('dojo_adfly_plugins_option_include_exclude_domains_choose') ?: 'exclude',
        'include_exclude_domains_value' => trim(get_option('dojo_adfly_plugins_option_include_exclude_domains_value')),
        'exclude_roles' => get_option('dojo_adfly_plugins_option_exclude_roles')
    );
}

function dojo_adfly_plugins_gen_script() {
    if (get_option('dojo_adfly_plugins_option_enabled')) {
        $options = dojo_adfly_plugins_get_options();
        global $current_user;
        
        if ($options['exclude_roles']) {
            foreach ($options['exclude_roles'] as $excludeRole) {
                if (in_array($excludeRole, $current_user->roles)) {
                    return false;
                }
            }
        }
        
        echo '
                <script type="text/javascript">
                    var adfly_id = ' . json_encode($options['id']) . ';
                    var adfly_advert = ' . json_encode($options['type']) . ';
                    var adfly_domain = ' . json_encode($options['domain']) . ';
                    ' . ($options['nofollow'] ? 'var adfly_nofollow = true;' : '') . '
                    var adfly_protocol = ' . json_encode($options['protocol']) . ';
                    ' . dojo_adfly_plugins_gen_include_exclude_domains_script($options) . ' 
                    
                    ' . ($options['website_entry_enabled'] ? 'var frequency_cap = 5;' : '') . ' 
                    ' . ($options['website_entry_enabled'] ? 'var frequency_delay = 5;' : '') . ' 
                    ' . ($options['website_entry_enabled'] ? 'var init_delay = 3;' : '') . ' 
                    
                    ' . ($options['popads_enabled'] ? 'var popunder = true;' : '') . ' 
                </script>
                <script src="http://cdn.adf.ly/js/link-converter.js"></script>
                ' . ($options['website_entry_enabled'] ? '<script src="http://cdn.adf.ly/js/entry.js"></script>' : '') . ' 
            ';
    } else {
        return false;
    }
}

function dojo_adfly_plugins_gen_include_exclude_domains_script($options) {
    $script = 'var ';
    if ($options['include_exclude_domains_choose'] == 'include') {
        $script .= 'domains = [';
    } else if ($options['include_exclude_domains_choose'] == 'exclude') {
        $script .= 'exclude_domains = [';
    }
    if (trim($options['include_exclude_domains_value'])) {
        $script .= implode(', ', array_map(function($x) {
            return json_encode(trim($x));
        }, explode(',', trim($options['include_exclude_domains_value']))));
    }
    
    $script .= '];';
    return $script;
}

function dojo_adfly_plugins_create_admin_menu() {
    add_options_page('AdFly WordPress Plugin', 'AdFly WordPress Plugin', 'administrator', __FILE__, 'dojo_adfly_plugins_admin_settings_page', plugins_url('/images/icon.png', __FILE__ ));
    add_action('admin_init', 'dojo_adfly_plugins_register_options');
}

function dojo_adfly_plugins_option_id_validate($value) {
    if (!eregi("^([0-9])+$", str_replace(" ", "", trim($value)))) {
        add_settings_error('dojo_adfly_plugins_option_id', 'dojo_adfly_plugins_option_id', 'User ID is required and must be a number.', 'error');
        return false;
    } else {
        return $value;
    }
}

function dojo_adfly_plugins_option_include_exclude_domains_value_validate($value) {
    $ok = true;
    array_map(function($x) {
        if (!preg_match('/^(?!\-)(?:[a-zA-Z\d\-]{0,62}[a-zA-Z\d]\.){1,126}(?!\d+)[a-zA-Z\d]{1,63}$/', trim($x))) {
            add_settings_error('dojo_adfly_plugins_option_id', 'dojo_adfly_plugins_option_include_exclude_domains_value', $x . ' is not valid domain name.', 'error');
        }
    }, explode(',', trim($value)));
    
    return $ok ? $value : false;
}

function dojo_adfly_plugins_register_options() {
    register_setting('dojo-adfly-settings-group', 'dojo_adfly_plugins_option_enabled');
    register_setting('dojo-adfly-settings-group', 'dojo_adfly_plugins_option_id', 'dojo_adfly_plugins_option_id_validate');
    register_setting('dojo-adfly-settings-group', 'dojo_adfly_plugins_option_popads_enabled');
    register_setting('dojo-adfly-settings-group', 'dojo_adfly_plugins_option_type');
    register_setting('dojo-adfly-settings-group', 'dojo_adfly_plugins_option_domain');
    register_setting('dojo-adfly-settings-group', 'dojo_adfly_plugins_option_nofollow');
    register_setting('dojo-adfly-settings-group', 'dojo_adfly_plugins_option_website_entry_enabled');
    register_setting('dojo-adfly-settings-group', 'dojo_adfly_plugins_option_protocol');
    register_setting('dojo-adfly-settings-group', 'dojo_adfly_plugins_option_include_exclude_domains_choose');
    register_setting('dojo-adfly-settings-group', 'dojo_adfly_plugins_option_include_exclude_domains_value', 'dojo_adfly_plugins_option_include_exclude_domains_value_validate');
    register_setting('dojo-adfly-settings-group', 'dojo_adfly_plugins_option_exclude_roles');
}

function dojo_adfly_plugins_admin_settings_page() {?>
    <div class="wrap">
        <h2>AdFly WordPress Plugin</h2>
        
        <form method="post" action="options.php">
            <?php settings_fields('dojo-adfly-settings-group');?>
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <td scope="row">Integration Enabled</td>
                        <td><input type="checkbox" <?php echo get_option('dojo_adfly_plugins_option_enabled') ? 'checked="checked"' : '' ?> value="1" name="dojo_adfly_plugins_option_enabled" /></td>
                    </tr>
                    <tr valign="top">
                        <td scope="row">AdFly User ID</td>
                        <td>
                            <input type="text" name="dojo_adfly_plugins_option_id" value="<?php echo htmlspecialchars(get_option('dojo_adfly_plugins_option_id'), ENT_QUOTES) ?>" />
                            <p class="description">
                                Simply visit <a href="http://adf.ly/account/referrals" target="_blank">http://adf.ly/account/referrals</a> page.
                                There will be URL http://adf.ly/?id=XXX where XXX is your AdFly User ID.
                            </p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <td scope="row">Ad Type</td>
                        <td>
                            <select name="dojo_adfly_plugins_option_type">
                                <option value="int" <?php echo get_option('dojo_adfly_plugins_option_type') == 'int' ? 'selected="selected"' : '' ?>>Interstitial</option>
                                <option value="banner" <?php echo get_option('dojo_adfly_plugins_option_type') == 'banner' ? 'selected="selected"' : '' ?>>Banner</option>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <td scope="row">AdFly Domain</td>
                        <td>
                            <select name="dojo_adfly_plugins_option_domain">
                                <option value="adf.ly" <?php echo get_option('dojo_adfly_plugins_option_domain') == 'adf.ly' ? 'selected="selected"' : '' ?>>adf.ly</option>
                                <option value="j.gs" <?php echo get_option('dojo_adfly_plugins_option_domain') == 'j.gs' ? 'selected="selected"' : '' ?>>j.gs</option>
                                <option value="q.gs" <?php echo get_option('dojo_adfly_plugins_option_domain') == 'q.gs' ? 'selected="selected"' : '' ?>>q.gs</option>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <td scope="row">Include/Exclude Domains</td>
                        <td>
                            <div>
                                <label>
                                    <input type="radio" name="dojo_adfly_plugins_option_include_exclude_domains_choose" value="include" <?php echo get_option('dojo_adfly_plugins_option_include_exclude_domains_choose') == 'include' ? 'checked="checked"' : '' ?> />
                                    Include
                                </label>
                                <label>
                                    <input type="radio" name="dojo_adfly_plugins_option_include_exclude_domains_choose" value="exclude" <?php echo !get_option('dojo_adfly_plugins_option_include_exclude_domains_choose') || get_option('dojo_adfly_plugins_option_include_exclude_domains_choose') == 'exclude' ? 'checked="checked"' : '' ?> />
                                    Exclude
                                </label>
                            </div>
                            <div>
                                <textarea rows="4" style="width: 64%;" name="dojo_adfly_plugins_option_include_exclude_domains_value"><?php echo htmlspecialchars(trim(get_option('dojo_adfly_plugins_option_include_exclude_domains_value')), ENT_QUOTES) ?></textarea>
                                <p class="description">Comma-separated list of domains.</p>
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <td scope="row">No Follow</td>
                        <td>
                            <input type="checkbox" <?php echo get_option('dojo_adfly_plugins_option_nofollow') ? 'checked="checked"' : '' ?> value="1" name="dojo_adfly_plugins_option_nofollow" />
                            <p class="description">Check this option if you wish links to stop outbound page equity.</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <td scope="row">Protocol</td>
                        <td>
                            <select name="dojo_adfly_plugins_option_protocol">
                                <option value="http" <?php echo get_option('dojo_adfly_plugins_option_protocol') == 'http' ? 'selected="selected"' : '' ?>>http</option>
                                <option value="https" <?php echo get_option('dojo_adfly_plugins_option_protocol') == 'https' ? 'selected="selected"' : '' ?>>https</option>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <td scope="row">Pop Ads Enabled</td>
                        <td>
                            <input type="checkbox" <?php echo get_option('dojo_adfly_plugins_option_popads_enabled') ? 'checked="checked"' : '' ?> value="1" name="dojo_adfly_plugins_option_popads_enabled" />
                            <p class="description"><a href="http://kb.adf.ly/27402711/what-are-pop-ads-on-adfly-how-can-i-use-them" target="_blank">What is this?</a></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <td scope="row">Website Entry Script Enabled</td>
                        <td>
                            <input type="checkbox" <?php echo get_option('dojo_adfly_plugins_option_website_entry_enabled') ? 'checked="checked"' : '' ?> value="1" name="dojo_adfly_plugins_option_website_entry_enabled" />
                            <p class="description">Check this option if you wish to earn money when a visitor simply enters your site.</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <td scope="row">Exclude following user roles from displaying ads</td>
                        <td>
                            <select name="dojo_adfly_plugins_option_exclude_roles[]" multiple="multiple">
                                <option <?php echo get_option('dojo_adfly_plugins_option_exclude_roles') && in_array('subscriber', get_option('dojo_adfly_plugins_option_exclude_roles')) ? ' selected="selected" ' : '' ?> value="subscriber">Subscriber</option>
                                <option <?php echo get_option('dojo_adfly_plugins_option_exclude_roles') && in_array('contributor', get_option('dojo_adfly_plugins_option_exclude_roles')) ? ' selected="selected" ' : '' ?> value="contributor">Contributor</option>
                                <option <?php echo get_option('dojo_adfly_plugins_option_exclude_roles') && in_array('author', get_option('dojo_adfly_plugins_option_exclude_roles')) ? ' selected="selected" ' : '' ?> value="author">Author</option>
                                <option <?php echo get_option('dojo_adfly_plugins_option_exclude_roles') && in_array('editor', get_option('dojo_adfly_plugins_option_exclude_roles')) ? ' selected="selected" ' : '' ?> value="editor">Editor</option>
                                <option <?php echo get_option('dojo_adfly_plugins_option_exclude_roles') && in_array('administrator', get_option('dojo_adfly_plugins_option_exclude_roles')) ? ' selected="selected" ' : '' ?> value="administrator">Administrator</option
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
    
            <p class="submit">
                <input type="submit" class="button-primary" value="<?php _e('Update Settings') ?>" />
            </p>
            
            <p>
            Feedback, bug report, and suggestions are greatly appreciated. Please submit any question to <a target="_blank" href="http://dojo.cc">Internet Marketing Dojo</a>.
            </p>
        </form>
    </div>
<?php }?><?php
    add_action('wp_head', 'dojo_adfly_plugins_gen_script');
    add_action('admin_menu', 'dojo_adfly_plugins_create_admin_menu');
?>
