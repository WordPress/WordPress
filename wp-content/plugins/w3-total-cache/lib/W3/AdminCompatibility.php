<?php
/**
 * W3 AdminCompatibility module
 */
if (!defined('W3TC')) {
    die();
}
w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');
/**
 * Class W3_AdminCompatibility
 */
class W3_AdminCompatibility extends W3_Plugin {
    function run() {
        add_filter('pre_update_option_active_plugins',array($this, 'pre_update_option_active_plugins'));
        add_filter('pre_update_site_option_active_sitewide_plugins',array($this, 'pre_update_option_active_plugins'));
        if (false === get_transient('w3tc.verify_plugins')) {
            add_action('admin_notices', array($this, 'verify'));
            add_action('network_admin_notices', array($this, 'verify'));
        }

        $this->_backwards_import();
    }

    /**
     * Active plugins pre update option filter
     *
     * @param string $new_value
     * @return string
     */
    function pre_update_option_active_plugins($new_value) {
        delete_transient('w3tc.verify_plugins');
        return $new_value;
    }

    /**
     * Check that activated plugins are not incompatible with the plugin
     */
    function verify() {
        if (is_network_admin()) {
            $active_plugins = (array) get_site_option( 'active_sitewide_plugins', array() );
            $active_plugins = array_keys($active_plugins);
        } else
            $active_plugins = (array) get_option('active_plugins');
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/ui.php');
        $incomp_plugins = $this->_get_incompatible_plugins();

        $message = '';
        $matches = array_intersect($active_plugins, $incomp_plugins);
        if ($matches) {
            $message = $this->_custom_message($matches);
        }
        if ($message)
            w3_e_error_box($message);
        else
            set_transient('w3tc.verify_plugins', true,7*24*3600);
    }

    /**
     * List of incomatible plugins
     * @return array
     */
    private function _get_incompatible_plugins() {
        return array(
                    'force-gzip/force-gzip.php'
                    ,'wp-http-compression/wp-http-compression.php'
                    ,'gzippy/gzippy.php'
                    ,'wordpress-gzip-compression/ezgz.php'
                    ,'wpcompressor/wpcompressor.php'
                    ,'gzip-pages/filosofo-gzip-compression.php'
                    ,'admin-flush-w3tc-cache/admin_flush_w3tc.php'
                    ,'hyper-cache/plugin.php'
                    ,'aio-cache/aio-cache.php'
                    ,'lite-cache/plugin.php'
                    ,'quick-cache/quick-cache.php'
                    ,'wp-super-cache/wp-cache.php'
                    ,'hyper-cache-extended/plugin.php'
                    ,'batcache/batcache.php'
                    ,'cachify/cachify.php'
                    ,'flexicache/wp-plugin.php'
                    );
    }

    /**
     * Build incompatible plugins message
     * @param $plugins
     * @return string
     */
    private function _custom_message($plugins) {

        $message = __('The following plugins are not compatible with W3 Total Cache and will cause unintended results:', 'w3-total-cache');
        $plugin_names = array();
        foreach ($plugins as $plugin)  {
            $data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);
            $temp = "<li><div>{$data['Name']}</div>";
            if (is_network_admin() && current_user_can('manage_network_plugins'))
                $temp .= ' <a class="button-secondary" href="' . network_admin_url(wp_nonce_url('plugins.php?action=deactivate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s=', 'deactivate-plugin_' . $plugin)) . '" title="' . esc_attr__('Deactivate this plugin', 'w3-total-cache') . '">' . __('Network Deactivate') . '</a>';
            else
                $temp .= ' <a class="button-secondary" href="' . admin_url(wp_nonce_url('plugins.php?action=deactivate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s=', 'deactivate-plugin_' . $plugin)) . '" title="' . esc_attr__('Deactivate this plugin') . '">' . __('Deactivate', 'w3-total-cache') . '</a>';
            $temp .= "</li>";
            $plugin_names[] = $temp;
        }
        return sprintf("<p>$message</p><ul class=\"w3tc-incomp-plugins\">%s</ul>", implode('', $plugin_names));
    }

    /**
     * Handle importing changed configuration from older versions
     */
    private function _backwards_import() {
        if ($this->_config->get_string('cdn.engine') == 'netdna' && $this->_config->get_string('cdn.netdna.authorization_key') == '') {

            $alias = $this->_config->get_string('cdn.netdna.alias');
            $consumerkey = $this->_config->get_string('cdn.netdna.consumerkey');
            $consumersecret = $this->_config->get_string('cdn.netdna.consumersecret');
            if ($alias && $consumerkey && $consumersecret) {
                $this->_config->set('cdn.maxcdn.authorization_key', "$alias+$consumerkey+$consumersecret");
                $this->_config->set('cdn.netdna.authorization_key', "$alias+$consumerkey+$consumersecret");
                $this->_config->set('cdn.engine', 'maxcdn');
                $this->_config->set('cdn.maxcdn.zone_id', $this->_config->get_integer('cdn.netdna.zone_id', 0));
                $this->_config->set('cdn.maxcdn.domain', $this->_config->get_array('cdn.netdna.domain'));
                $this->_config->set('cdn.maxcdn.ssl',  $this->_config->get_string('cdn.netdna.ssl'));

                try{
                    $this->_config->save();
                    $this->_config->refresh_cache();
                } catch(Exception $ex) {}
            }
        }

        if (w3tc_get_extension_config('genesis.theme', 'fragment_reject_logged_roles') != null) {
            $old_extensions = $this->_config->get_array('extensions.settings');
            $genesis = $old_extensions['genesis.theme'];
            $genesis['reject_logged_roles'] = $genesis['fragment_reject_logged_roles'];
            unset($genesis['fragment_reject_logged_roles']);
            $genesis['reject_logged_roles_on_actions'] = $genesis['fragment_reject_logged_roles_on_actions'];
            unset($genesis['fragment_reject_logged_roles_on_actions']);
            $genesis['reject_roles'] = $genesis['fragment_reject_roles'];
            unset($genesis['fragment_reject_roles']);
            $old_extensions['genesis.theme'] = $genesis;
            $this->_config->set("extensions.settings", $old_extensions);
            try{
                $this->_config->save();
                $this->_config->refresh_cache();
            } catch(Exception $ex) {}
        }

        if ($this->_config->get_boolean('cloudflare.enabled') || $this->_config->get_string('cloudflare.email')) {
            $extensions = $this->_config->get_array('extensions.settings');
            $cloudflare =  array();
            $cloudflare['enabled'] = $this->_config->get_string('cloudflare.enabled');
            $cloudflare['email'] = $this->_config->get_string('cloudflare.email');
            $cloudflare['key'] = $this->_config->get_string('cloudflare.key');
            $cloudflare['zone'] = $this->_config->get_string('cloudflare.zone');

            $extensions['cloudflare'] = $cloudflare;
            $this->_config->set("extensions.settings", $extensions);
            try{
                $this->_config->save();
                $this->_config->refresh_cache();
            } catch(Exception $ex) {}
        }
    }
}
